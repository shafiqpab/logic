<?
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This form will create Woven Garments Price Quotation Entry Entry
Functionality	         :	
JS Functions	         :
Created by		         :	Rashed 
Creation date 	         : 	18-10-2012
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 		
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         :
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//-------------------------------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Info","../../", 1, 1, $unicode,'','');

?>	
<script>

/*document.onclick = function (e) {
	alert(e)
    e = e || window.event;      
    var target = e.target || e.srcElement;
    if (target !== btn && (!target.contains(modal) || target !== modal)) {
        modal.style.display = 'none';
    }  
}*/
/*$(document).bind('DOMSubtreeModified', function () {
   if ($('.validation_errors').length) {
       alert("test");
   }
});*/

/*$("input").each(function() {
    $(this).attr("originalValue", $(this).val()); 
});
*/

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission = '<? echo $permission; ?>';
var str_construction = [<? echo substr(return_library_autocomplete( "select construction from wo_pri_quo_fabric_cost_dtls group by construction ", "construction"), 0, -1); ?> ];
var str_composition = [<? echo substr(return_library_autocomplete( "select composition from wo_pri_quo_fabric_cost_dtls group by composition", "composition"), 0, -1); ?>];
var str_incoterm_place = [<? echo substr(return_library_autocomplete( "select incoterm_place from  wo_price_quotation group by incoterm_place", "incoterm_place"), 0, -1); ?>];
var str_factory = [<? echo substr(return_library_autocomplete( "select factory from  wo_price_quotation group by factory", "factory"), 0, -1); ?>];
// Common For All----------------------------------------------------

function set_auto_complete(type)
{
	if(type=='price_quation_mst')
	{
			$("#txt_incoterm_place").autocomplete({
			source: str_incoterm_place
			});
			$("#txt_factory").autocomplete({
			source:  str_factory 
			}); 
	}
	if(type=='tbl_fabric_cost')
	{
		var row_num=$('#tbl_fabric_cost tr').length-1;
		for (var i=1; i<=row_num; i++)
		{
			$("#txtconstruction_"+i).autocomplete({
			source: str_construction
			});
			$("#txtcomposition_"+i).autocomplete({
			source:  str_composition 
			}); 
		}
	}
}
// Start Master Form-----------------------------------------
function openmypage(page_link,title)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var theemail=this.contentDoc.getElementById("selected_id") //Access form field with id="emailfield"
		if (theemail.value!="")
		{
			freeze_window(5);
			get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/quotation_entry_simple_final_controller" );
			show_table()
			set_button_status(1, permission, 'fnc_quotation_entry',1);
			release_freezing();
		}
		 
	}
}


function openmypage_inquery()// Quotation Inquery Id
	{
		//reset_form('','list_container_recipe_items*recipe_items_list_view','','','','');
	
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var page_link='requires/quotation_entry_simple_final_controller.php?action=inquery_mrr_popup&company='+company; 
		var title="Search  Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			
			var mrrNumber=this.contentDoc.getElementById("hidden_issue_number").value; // mrr number
			mrrNumber = mrrNumber.split("_"); 
			//var mrrId=this.contentDoc.getElementById("issue_id").value; // mrr number
	
			$("#txt_quotation_inquery_sys_id").val(mrrNumber[0]);
			$("#inquery_id").val(mrrNumber[1]);
			
			get_php_form_data(mrrNumber[0], "populate_data_from_data_inquery", "requires/quotation_entry_simple_final_controller");
			
			//set_button_status(1, permission, 'fnc_quotation_inquery',1,1);	
			
		}
	}


function open_set_popup(unit_id)
{
			var txt_quotation_id=document.getElementById('update_id').value;
			var set_breck_down=document.getElementById('set_breck_down').value;
			var tot_set_qnty=document.getElementById('tot_set_qnty').value;
			var page_link="requires/quotation_entry_simple_final_controller.php?txt_quotation_id="+trim(txt_quotation_id)+"&action=open_set_list_view&set_breck_down="+set_breck_down+"&tot_set_qnty="+tot_set_qnty+'&unit_id='+unit_id;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Item Details", 'width=860px,height=450px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var set_breck_down=this.contentDoc.getElementById("set_breck_down") //Access form field with id="emailfield"
				var item_id=this.contentDoc.getElementById("item_id") //Access form field with id="emailfield"
				var tot_set_qnty=this.contentDoc.getElementById("tot_set_qnty") //Access form field with id="emailfield"
				document.getElementById('set_breck_down').value=set_breck_down.value;
				document.getElementById('item_id').value=item_id.value;
				document.getElementById('tot_set_qnty').value=tot_set_qnty.value;

			}		
		
}
function set_exchange_rate(currency)
{
	if(currency==1)
	{
		document.getElementById('txt_exchange_rate').value=1;
	}
	else
	{
		document.getElementById('txt_exchange_rate').value=80;	
	}
	
}

function calculate_lead_time()
{
  var txt_est_ship_date= document.getElementById('txt_est_ship_date').value;
  var txt_op_date= document.getElementById('txt_op_date').value;
  var lead_time = return_ajax_request_value(txt_est_ship_date+"_"+txt_op_date, 'lead_time_calculate', 'requires/quotation_entry_simple_final_controller')	
  document.getElementById('txt_lead_time').value=trim(lead_time)
}

function fnc_quotation_entry( operation )
{
	if(operation==2)
	{
		alert("Delete Restricted")
		return;
	}
	if (form_validation('txt_quotation_inquery_sys_id*cbo_company_name*cbo_buyer_name*txt_style_ref*cbo_currercy*txt_exchange_rate*cbo_costing_per*cbo_order_uom*item_id*txt_quotation_date','Quotation_Inquery_Sys*Company Name*Buyer Name*Style Ref*Currency*Exchange Rate*Costing Per*UOM*Item*Quot Date')==false)
	{
		return;
	}
	else
	{

var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_quotation_id*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_revised_no*cbo_pord_dept*txt_product_code*txt_style_desc*cbo_currercy*cbo_agent*txt_offer_qnty*cbo_region*cbo_color_range*cbo_inco_term*txt_incoterm_place*txt_machine_line*txt_prod_line_hr*cbo_costing_per*txt_quotation_date*txt_est_ship_date*txt_factory*txt_remarks*garments_nature*cbo_order_uom*item_id*set_breck_down*tot_set_qnty*update_id*cbo_approved_status*cm_cost_predefined_method_id*txt_exchange_rate*txt_sew_smv*txt_cut_smv*txt_sew_efficiency_per*txt_cut_efficiency_per*txt_efficiency_wastage*cbo_season_name*txt_op_date*txt_quotation_inquery_sys_id*inquery_id*txt_m_list*txt_bh_merchant*cbo_quotation_status',"../../");
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/quotation_entry_simple_final_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_quotation_entry_reponse;
	}
}

function fnc_quotation_entry_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		document.getElementById('update_id').value  = reponse[1];
		document.getElementById('txt_quotation_id').value  = reponse[1];
		if(reponse[2]==1)
		{
			document.getElementById('cbo_approved_status').value = '2';
			document.getElementById('approve1').value = 'Approved';
			document.getElementById('cbo_company_name').disabled=false;
			document.getElementById('cbo_buyer_name').disabled=false;
			document.getElementById('txt_style_ref').disabled=false; 
			document.getElementById('txt_revised_no').disabled=false; 
			document.getElementById('cbo_pord_dept').disabled=false;
			document.getElementById('txt_style_desc').disabled=false;
			document.getElementById('cbo_currercy').disabled=false;
			document.getElementById('cbo_agent').disabled=false;
			document.getElementById('txt_offer_qnty').disabled=false;
			document.getElementById('cbo_region').disabled=false;
			document.getElementById('cbo_color_range').disabled=false;
			document.getElementById('cbo_inco_term').disabled=false;
			document.getElementById('txt_incoterm_place').disabled=false;
			document.getElementById('txt_machine_line').disabled=false;
			document.getElementById('txt_prod_line_hr').disabled=false;
			document.getElementById('cbo_costing_per').disabled=false;
			document.getElementById('txt_quotation_date').disabled=false;
			document.getElementById('txt_est_ship_date').disabled=false;
			document.getElementById('txt_factory').disabled=false;
			document.getElementById('txt_remarks').disabled=false;
			document.getElementById('cbo_order_uom').disabled=false;
			document.getElementById('image_button').disabled=false;
			document.getElementById('set_button').disabled=false;
			document.getElementById('save1').disabled=false;
			document.getElementById('update1').disabled=false;
			document.getElementById('Delete1').disabled=false;
			//===================
			document.getElementById('txt_lab_test_pre_cost').disabled=false;
			document.getElementById('txt_inspection_pre_cost').disabled=false;
			document.getElementById('txt_cm_pre_cost').disabled=false;
			document.getElementById('txt_freight_pre_cost').disabled=false;
			document.getElementById('txt_common_oh_pre_cost').disabled=false;
			document.getElementById('txt_1st_quoted_price_pre_cost').disabled=false;
			document.getElementById('txt_first_quoted_price_date').disabled=false;
			document.getElementById('txt_revised_price_pre_cost').disabled=false;
			document.getElementById('txt_revised_price_date').disabled=false;
			document.getElementById('txt_confirm_price_pre_cost').disabled=false;
			document.getElementById('txt_confirm_date_pre_cost').disabled=false;
			document.getElementById('save2').disabled=false;
			document.getElementById('update2').disabled=false;
			document.getElementById('Delete2').disabled=false;

		}
		if(reponse[2]==2)
		{
			document.getElementById('cbo_approved_status').value = '1';
			document.getElementById('approve1').value = 'Un-Approved';
			document.getElementById('cbo_company_name').disabled=true;
			document.getElementById('cbo_buyer_name').disabled=true;
			document.getElementById('txt_style_ref').disabled=true;
			document.getElementById('txt_revised_no').disabled=true; 
			document.getElementById('cbo_pord_dept').disabled=true;
			document.getElementById('txt_style_desc').disabled=true;
			document.getElementById('cbo_currercy').disabled=true;
			document.getElementById('cbo_agent').disabled=true;
			document.getElementById('txt_offer_qnty').disabled=true;
			document.getElementById('cbo_region').disabled=true;
			document.getElementById('cbo_color_range').disabled=true;
			document.getElementById('cbo_inco_term').disabled=true;
			document.getElementById('txt_incoterm_place').disabled=true;
			document.getElementById('txt_machine_line').disabled=true;
			document.getElementById('txt_prod_line_hr').disabled=true;
			document.getElementById('cbo_costing_per').disabled=true;
			document.getElementById('txt_quotation_date').disabled=true;
			document.getElementById('txt_est_ship_date').disabled=true;
			document.getElementById('txt_factory').disabled=true;
			document.getElementById('txt_remarks').disabled=true;
			document.getElementById('cbo_order_uom').disabled=true;
			document.getElementById('image_button').disabled=true;
			document.getElementById('set_button').disabled=true;
			document.getElementById('save1').disabled=true;
			document.getElementById('update1').disabled=true;
			document.getElementById('Delete1').disabled=true;
         }
		set_button_status(1, permission, 'fnc_quotation_entry',1);
		if(reponse[0]==0 || reponse[0]==1)
		{
				show_table()
				release_freezing();
		}
		if(reponse[0]==2)
		{
			    reset_form('quotationmst_1','cost_container','','cbo_currercy,2*cbo_costing_per,1*cbo_approved_status,2','')
			    set_button_status(0, permission, 'fnc_quotation_entry',1);
				release_freezing();
		}
	}
}

function copy_quatation(operation)
{
	if (form_validation('cbo_company_name*cbo_buyer_name*txt_style_ref*cbo_currercy*txt_exchange_rate*cbo_costing_per*cbo_order_uom*item_id*txt_quotation_date','Company Name*Buyer Name*Style Ref*Currency*Exchange Rate*Costing Per*UOM*Item*Quot Date')==false)
	{
		return;
	}
	else
	{
		var data="action=copy_quatation&operation="+operation+get_submitted_data_string('txt_quotation_id*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_revised_no*cbo_pord_dept*txt_product_code*txt_style_desc*cbo_currercy*cbo_agent*txt_offer_qnty*cbo_region*cbo_color_range*cbo_inco_term*txt_incoterm_place*txt_machine_line*txt_prod_line_hr*cbo_costing_per*txt_quotation_date*txt_est_ship_date*txt_factory*txt_remarks*garments_nature*cbo_order_uom*item_id*set_breck_down*tot_set_qnty*update_id*cbo_approved_status*cm_cost_predefined_method_id*txt_exchange_rate*txt_sew_smv*txt_cut_smv*txt_sew_efficiency_per*txt_cut_efficiency_per*txt_efficiency_wastage*cbo_season_name*txt_quotation_inquery_sys_id*inquery_id*m_list*txt_bh_merchant*cbo_quotation_status',"../../");
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/quotation_entry_simple_final_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = copy_quatation_reponse;
	}
}

function copy_quatation_reponse()
{
	if(http.readyState == 4) 
	{
		alert(http.responseText);
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(trim(reponse[0]));
		//document.getElementById('update_id').value  = reponse[2];
		//alert(reponse[2]);
		
		document.getElementById('txt_quotation_id').value  = reponse[2];
		//document.getElementById('update_id_dtls').value  = reponse[3];
		
		if(reponse[3]==1)
		{
			document.getElementById('cbo_approved_status').value = '2';
			document.getElementById('approve1').value = 'Approved';
			document.getElementById('cbo_company_name').disabled=false;
			document.getElementById('cbo_buyer_name').disabled=false;
			document.getElementById('txt_style_ref').disabled=false; 
			document.getElementById('txt_revised_no').disabled=false; 
			document.getElementById('cbo_pord_dept').disabled=false;
			document.getElementById('txt_style_desc').disabled=false;
			document.getElementById('cbo_currercy').disabled=false;
			document.getElementById('cbo_agent').disabled=false;
			document.getElementById('txt_offer_qnty').disabled=false;
			document.getElementById('cbo_region').disabled=false;
			document.getElementById('cbo_color_range').disabled=false;
			document.getElementById('cbo_inco_term').disabled=false;
			document.getElementById('txt_incoterm_place').disabled=false;
			document.getElementById('txt_machine_line').disabled=false;
			document.getElementById('txt_prod_line_hr').disabled=false;
			document.getElementById('cbo_costing_per').disabled=false;
			document.getElementById('txt_quotation_date').disabled=false;
			document.getElementById('txt_est_ship_date').disabled=false;
			document.getElementById('txt_factory').disabled=false;
			document.getElementById('txt_remarks').disabled=false;
			document.getElementById('cbo_order_uom').disabled=false;
			document.getElementById('image_button').disabled=false;
			document.getElementById('set_button').disabled=false;
			document.getElementById('save1').disabled=false;
			document.getElementById('update1').disabled=false;
			document.getElementById('Delete1').disabled=false;
			//===================
			document.getElementById('txt_lab_test_pre_cost').disabled=false;
			document.getElementById('txt_inspection_pre_cost').disabled=false;
			document.getElementById('txt_cm_pre_cost').disabled=false;
			document.getElementById('txt_freight_pre_cost').disabled=false;
			document.getElementById('txt_common_oh_pre_cost').disabled=false;
			document.getElementById('txt_1st_quoted_price_pre_cost').disabled=false;
			document.getElementById('txt_first_quoted_price_date').disabled=false;
			document.getElementById('txt_revised_price_pre_cost').disabled=false;
			document.getElementById('txt_revised_price_date').disabled=false;
			document.getElementById('txt_confirm_price_pre_cost').disabled=false;
			document.getElementById('txt_confirm_date_pre_cost').disabled=false;
			document.getElementById('save2').disabled=false;
			document.getElementById('update2').disabled=false;
			document.getElementById('Delete2').disabled=false;

		}
		if(reponse[3]==2)
		{
			document.getElementById('cbo_approved_status').value = '1';
			document.getElementById('approve1').value = 'Un-Approved';
			document.getElementById('cbo_company_name').disabled=true;
			document.getElementById('cbo_buyer_name').disabled=true;
			document.getElementById('txt_style_ref').disabled=true;
			document.getElementById('txt_revised_no').disabled=true; 
			document.getElementById('cbo_pord_dept').disabled=true;
			document.getElementById('txt_style_desc').disabled=true;
			document.getElementById('cbo_currercy').disabled=true;
			document.getElementById('cbo_agent').disabled=true;
			document.getElementById('txt_offer_qnty').disabled=true;
			document.getElementById('cbo_region').disabled=true;
			document.getElementById('cbo_color_range').disabled=true;
			document.getElementById('cbo_inco_term').disabled=true;
			document.getElementById('txt_incoterm_place').disabled=true;
			document.getElementById('txt_machine_line').disabled=true;
			document.getElementById('txt_prod_line_hr').disabled=true;
			document.getElementById('cbo_costing_per').disabled=true;
			document.getElementById('txt_quotation_date').disabled=true;
			document.getElementById('txt_est_ship_date').disabled=true;
			document.getElementById('txt_factory').disabled=true;
			document.getElementById('txt_remarks').disabled=true;
			document.getElementById('cbo_order_uom').disabled=true;
			document.getElementById('image_button').disabled=true;
			document.getElementById('set_button').disabled=true;
			document.getElementById('save1').disabled=true;
			document.getElementById('update1').disabled=true;
			document.getElementById('Delete1').disabled=true;
			//===================
			document.getElementById('txt_lab_test_pre_cost').disabled=true;
			document.getElementById('txt_inspection_pre_cost').disabled=true;
			document.getElementById('txt_cm_pre_cost').disabled=true;
			document.getElementById('txt_freight_pre_cost').disabled=true;
			document.getElementById('txt_common_oh_pre_cost').disabled=true;
			document.getElementById('txt_1st_quoted_price_pre_cost').disabled=true;
			document.getElementById('txt_first_quoted_price_date').disabled=true;
			document.getElementById('txt_revised_price_pre_cost').disabled=true;
			document.getElementById('txt_revised_price_date').disabled=true;
			document.getElementById('txt_confirm_price_pre_cost').disabled=true;
			document.getElementById('txt_confirm_date_pre_cost').disabled=true;
			document.getElementById('save2').disabled=true;
			document.getElementById('update2').disabled=true;
			document.getElementById('Delete2').disabled=true;
         }
		set_button_status(1, permission, 'fnc_quotation_entry',1);
		//show_list_view(reponse[2],'show_fabric_cost_listview','cost_container','../woven_order/requires/quotation_entry_simple_final_controller','');
		release_freezing();
		
	}
}

function cm_cost_predefined_method(company_id)
{
	var cm_cost_method=return_global_ajax_value(company_id, 'cm_cost_predefined_method', '', 'requires/quotation_entry_simple_final_controller');
	if(cm_cost_method ==0)
	{
		$("#txt_cm_pre_cost").attr("disabled",false);
		$("#txt_sew_smv").attr("disabled",true);
		$("#txt_sew_efficiency_per").attr("disabled",true);
		$("#txt_cut_smv").attr("disabled",true);
		$("#txt_cut_efficiency_per").attr("disabled",true);
	}
	if(cm_cost_method ==1)
	{
		$("#txt_sew_smv").attr("disabled",false);
		$("#txt_sew_efficiency_per").attr("disabled",false);
		$("#txt_cut_smv").attr("disabled",true);
		$("#txt_cut_efficiency_per").attr("disabled",true);
		$("#txt_cm_pre_cost").attr("disabled",true);
	}
	if(cm_cost_method ==2)
	{
		$("#txt_sew_smv").attr("disabled",false);
		$("#txt_sew_efficiency_per").attr("disabled",false);
		$("#txt_cut_smv").attr("disabled",false);
		$("#txt_cut_efficiency_per").attr("disabled",false);
		$("#txt_cm_pre_cost").attr("disabled",true);
	}
	if(cm_cost_method ==3)
	{
		$("#txt_sew_smv").attr("disabled",true);
		$("#txt_sew_efficiency_per").attr("disabled",true);
		$("#txt_cut_smv").attr("disabled",true);
		$("#txt_cut_efficiency_per").attr("disabled",true);
		$("#txt_cm_pre_cost").attr("disabled",true);
	}
	document.getElementById('cm_cost_predefined_method_id').value=cm_cost_method;
	
}

function calculate_cm_cost_with_method()
{
	//1. CM Cost = SMV*CPM+ (SMV*CPM)* Efficiency Wastage%
//2. CM Cost= CU(SMV*CPM/ Efficiency %)+ SF(SMV*CPM/ Efficiency %)
//3. CM Cost = {(MCE/26)/NFM)*MPL)}/[{(PHL)*WH}]*Costing Per/Exchange Rate


	var cm_cost=0;
	var cbo_company_name=(document.getElementById('cbo_company_name').value)*1;
	var cm_cost_predefined_method_id=document.getElementById('cm_cost_predefined_method_id').value;
	var txt_sew_smv=parseFloat(document.getElementById('txt_sew_smv').value);
	var txt_cut_smv=parseFloat(document.getElementById('txt_cut_smv').value);
	var txt_sew_efficiency_per=parseFloat(document.getElementById('txt_sew_efficiency_per').value);
	var txt_cut_efficiency_per=parseFloat(document.getElementById('txt_cut_efficiency_per').value);
	//var txt_efficiency_wastage= parseFloat(document.getElementById('txt_efficiency_wastage').value);
	
	var cbo_currercy= document.getElementById('cbo_currercy').value;
	var txt_exchange_rate= document.getElementById('txt_exchange_rate').value;
	var txt_machine_line= document.getElementById('txt_machine_line').value;
	var txt_prod_line_hr= document.getElementById('txt_prod_line_hr').value;
	var cbo_costing_per= document.getElementById('cbo_costing_per').value;
	var cbo_costing_per_value=0
	if(cbo_costing_per==1)
	{
		cbo_costing_per_value=12;
	}
	if(cbo_costing_per==2)
	{
		cbo_costing_per_value=1;
	}
	if(cbo_costing_per==3)
	{
		cbo_costing_per_value=24;
	}
	if(cbo_costing_per==4)
	{
		cbo_costing_per_value=36;
	}
	if(cbo_costing_per==5)
	{
		cbo_costing_per_value=48;
	}
	 


	var cpm=return_global_ajax_value(cbo_company_name, 'cost_per_minute', '', 'requires/quotation_entry_controller');
	//alert(cpm);
	var data=cpm.split("_");
	//alert(cm_cost_predefined_method_id)
	if(cm_cost_predefined_method_id==1)
	{
		
		if(data[3]==0 || data[3]=="" )
		{
		   alert("Insert Cost Per Minute in Library>Merchandising Detailes>Financial Parameter Setup");
		   return;
		}
		var txt_efficiency_wastage=100-txt_sew_efficiency_per;
		document.getElementById('txt_efficiency_wastage').value=txt_efficiency_wastage;
		
		var cm_cost=(txt_sew_smv*data[3]*cbo_costing_per_value)+((txt_sew_smv*data[3]*cbo_costing_per_value)*(txt_efficiency_wastage/100));
		cm_cost=cm_cost/txt_exchange_rate;
	}
	if(cm_cost_predefined_method_id==2)
	{
		
		if(data[3]==0 ||data[3]=="" )
		{
		   alert("Insert Cost Per Minute in Library>Merchandising Detailes>Financial Parameter Setup");
		   return;
		}
		var cut_per=txt_cut_efficiency_per/100;
		var sew_per=txt_sew_efficiency_per/100;
		var cu=(txt_cut_smv*data[3]*cbo_costing_per_value)/cut_per
		var su=(txt_sew_smv*data[3]*cbo_costing_per_value)/sew_per
		var cm_cost=(cu+su)/txt_exchange_rate;
	}
	
	
	if(cm_cost_predefined_method_id==3)
	{
		//3. CM Cost = {(MCE/26)/NFM)*MPL)}/[{(PHL)*WH}]*Costing Per/Exchange Rate

		//var cpm=return_global_ajax_value(cbo_company_name, 'cost_per_minute', '', 'requires/quotation_entry_controller');
		//alert("EX"+txt_exchange_rate);
		if(cbo_currercy==0 || cbo_currercy=="")
		{
		   alert("Insert Currency");
		   return;
		}
		if(txt_exchange_rate==0 || txt_exchange_rate=="")
		{
		   alert("Insert Exchange Rate");
		   return;
		}
		if(txt_machine_line==0 || txt_machine_line=="")
		{
		   alert("Insert Machine/Line");
		   return;
		}
		if(txt_prod_line_hr==0 || txt_prod_line_hr=="")
		{
		   alert("Insert Prod/Line/Hr");
		   return;
		}
		if(cbo_costing_per==0 || cbo_costing_per=="")
		{
		   alert("Insert Costing Per");
		   return;
		}
		//alert(cm_cost_predefined_method_id)
		if(data[0]==0)
		{
		   alert("Insert Monthly CM Expense in Library>Merchandising Detailes>Financial Parameter Setup");
		   return;
		}
		if(data[1]==0)
		{
		   alert("Insert No. of Factory Machine  in Library>Merchandising Detailes>Financial Parameter Setup");
		   return;
		}
		if(data[2]==0)
		{
		   alert("Insert Working Hour in Library>Merchandising Detailes>Financial Parameter Setup");
		   return;
		}
		var per_day_cost=data[0]/26;
		var per_machine_cost=per_day_cost/data[1];
		var per_line_cost=per_machine_cost*txt_machine_line;
		var total_production_per_line=txt_prod_line_hr*data[2];
		var per_product_cost=per_line_cost/total_production_per_line;
		if(cbo_costing_per==1)
		{
			//final_cost_psc=txt_final_cost_dzn_pre_cost/12;
			var cm_cost=(per_product_cost*12)/txt_exchange_rate;
		}
		if(cbo_costing_per==2)
		{
			//final_cost_psc=txt_final_cost_dzn_pre_cost/12;
			var cm_cost=(per_product_cost*1)/txt_exchange_rate;
		}
		if(cbo_costing_per==3)
		{
			//final_cost_psc=txt_final_cost_dzn_pre_cost/12;
			var cm_cost=(per_product_cost*24)/txt_exchange_rate;
		}
		if(cbo_costing_per==4)
		{
			//final_cost_psc=txt_final_cost_dzn_pre_cost/12;
			var cm_cost=(per_product_cost*36)/txt_exchange_rate;
		}
		if(cbo_costing_per==5)
		{
			//final_cost_psc=txt_final_cost_dzn_pre_cost/12;
			var cm_cost=(per_product_cost*48)/txt_exchange_rate;
		}
		//alert(cm_cost);
		
		//var su=(txt_sew_smv*cpm)/txt_sew_efficiency_per
		//var cm_cost=per_product_cost*;
		//number_format_common(final_cost_psc, 1, 0, currency);
	}
	if(cm_cost!=Infinity)
	{
	   /*document.getElementById('txt_cm_pre_cost').value=number_format_common(cm_cost,1,0,cbo_currercy)	;
	   calculate_main_total()*/
		$("#table_2  tfoot tr:eq(2)").find("input,select").each(function() {
		if($(this).attr("id")=='CM_AY' || $(this).attr("id")=='CM_BY' || $(this).attr("id")=='CM_CY' )
		{
		 return;
		}
		var id=$(this).attr("id").split("_");
		document.getElementById('CM_'+id[1]).value=number_format_common(cm_cost,1,0);
		total_garments_cost_dzn();
	 })
	}
}


//// End Master Form-----------------------------------------








//==================================

function generate_table()
{
	mst_id=document.getElementById('update_id').value ;
	if(mst_id=="" || mst_id==0 )
	{
	alert("Select a quatation");
	return;
	}
	var num_row=document.getElementById('num_row').value;
	var num_col=document.getElementById('num_col').value;
	if (form_validation('num_row*num_col','Num Row*Num Col')==false)
	{
		return;
	}
	show_list_view(num_row+'_'+num_col,'generate_table','data_container','../woven_order/requires/quotation_entry_simple_final_controller','');
}

function copy_val(fieldid,table)
{
	
var value=document.getElementById(fieldid).value
field=fieldid.split("_");
var index=field[1]*1-1;
//alert(index)
$("#"+table+ " tbody tr:eq("+index+")").find("input,select").each(function() {
																		   
				var id=$(this).attr("id").split("_");
				 if(id[0]=="A")
				 {
					 return;
				 }
				 $(this).val(value);
				 sum_value($(this).attr("id"),table)
				total_fabric_cost_dzn();
				
			})	
}

function sum_value(fieldid,table)
{
	//copy_val(fieldid,table);
	var total=0;
	fieldid=fieldid.split("_");	
	if(table=="table_1")
	{
	
	var row_num=$('#table_1 tbody tr').length;
	//alert(row_num)
	for(i=2;i <= row_num;i++)
	{
		//alert(fieldid[0]+'_'+i)
		var value=document.getElementById(fieldid[0]+'_'+i).value*1
		//alert(value)
		total=total+value;
	}
	//alert(total)
	document.getElementById('sum1_'+fieldid[0]).value=number_format_common(total,1,0);
	claculate_percent(1)
	claculate_percent(2)
	}
	
	
	if(table=="table_2")
	{
	var row_num=$('#table_2 tbody tr').length;
	for(i =1;i <= row_num;i++)
	{
		var value=document.getElementById(fieldid[0]+'_'+i).value*1
		total=total+value;
	}
	document.getElementById('sum4_'+fieldid[0]).value=number_format_common(total,1,0);
	total_fabric_cost_dzn()
	}
	
	if(table=="table_3")
	{
	var row_num=$('#table_3 tbody tr').length;
	for(i =1;i <= row_num;i++)
	{
		var value=document.getElementById(fieldid[0]+'_'+i).value*1
		total=total+value;
	}
	document.getElementById('sum6_'+fieldid[0]).value=number_format_common(total,1,0);
	total_garments_cost_dzn()
	}
	if(table=="table_4")
	{
	var row_num=$('#table_4 tbody tr').length;
	for(i =1;i <= row_num;i++)
	{
		var value=document.getElementById(fieldid[0]+'_'+i).value*1
		total=total+value;
	}
	document.getElementById('Esum1_'+fieldid[0]).value=number_format_common(total,1,0);
	total_garments_cost_dzn()
	}
	
	if(table=="table_5")
	{
	var row_num=$('#table_5 tbody tr').length;
	for(i =1;i <= row_num;i++)
	{
		var value=document.getElementById(fieldid[0]+'_'+i).value*1
		total=total+value;
	}
	document.getElementById('TRsum1_'+fieldid[0]).value=number_format_common(total,1,0);
	total_garments_cost_dzn()
	}
	
	if(table=="table_6")
	{
	var row_num=$('#table_6 tbody tr').length;
	for(i =1;i <= row_num;i++)
	{
		var value=document.getElementById(fieldid[0]+'_'+i).value*1
		total=total+value;
	}
	document.getElementById('OCsum1_'+fieldid[0]).value=number_format_common(total,1,0);
	total_garments_cost_dzn()
	}
	
}


function claculate_percent(str)
{
	if(str==1)
	{
			/*var Fper_1=document.getElementById('Fper_1').value;
			if(Fper_1=="")
			{
				Fper_1=0;
			}*/
			$("#table_1  tfoot tr:eq(0)").find("input,select").each(function() {
				var id=$(this).attr("id").split("_");
				var value=$(this).attr("value");
				var Fper=document.getElementById('Fper_'+id[1]).value;
				//alert(Fper)
				document.getElementById('Fper_'+id[1]).value=Fper
				if(Fper=="")
				{
					Fper=0;
				}
				var percent=((parseInt(Fper)*value)/100)+(value*1);
				if('sum2_'+id[1]=='sum2_A')
				{
					return;
				}
				document.getElementById('sum2_'+id[1]).value=number_format_common(percent,1,0);
				total_fabric_cost_dzn()
			})
	}
	
	if(str==2)
	{
		    
			$("#table_1  tfoot tr:eq(0)").find("input,select").each(function() {
				var id=$(this).attr("id").split("_");
				
				var value=document.getElementById('sum2_'+id[1]).value;
				var Yper=document.getElementById('Yper_'+id[1]).value;
				if(Yper=="")
				{
					Yper=0;
				}
				if('sum3_'+id[1]=='sum3_A')
				{
					return;
				}
				var percent=((parseInt(Yper)*value)/100)+(value*1)
				document.getElementById('sum3_'+id[1]).value=number_format_common(percent,1,0);
				total_fabric_cost_dzn()
			})
	}
	
	if(str==3)
	{
		    var Commercialsum_C=document.getElementById('Commercialsum_C').value;
			if(Commercialsum_C=="")
			{
				Commercialsum_C=0;
			}
			$("#table_7  tfoot tr:eq(2)").find("input,select").each(function() {
				if($(this).attr("id")=='Commercialsum_A' || $(this).attr("id")=='Commercialsum_B' || $(this).attr("id")=='Commercialsum_C' )
				{
				 return;
				}
				var id=$(this).attr("id").split("_");
				
				var total_fabric_cost_dzn= document.getElementById('sum5_'+id[1]+"Y").value;
				var TrimCostTotal= document.getElementById('TRsum1_'+id[1]+"TR").value;
				var value=(total_fabric_cost_dzn*1)+(TrimCostTotal*1);
				var percent=((parseInt(Commercialsum_C)*value)/100)
				document.getElementById('Commercialsum_'+id[1]).value=number_format_common(percent/12,1,0);
				var TotalGarmentsCostDzn=document.getElementById('sum7_'+id[1]+'T').value;
				var PriceAfterCommercialCostPcs=number_format_common((((TotalGarmentsCostDzn*1)+percent)/12),1,0);
				document.getElementById('Commercialsum1_'+id[1]).value=PriceAfterCommercialCostPcs;
				//document.getElementById('Commercialsum_'+id[1]).value=number_format_common(percent/12,1,0);
				//total_fabric_cost_dzn()
			})
	}
	
	if(str==4)
	{
		    var profit_C=document.getElementById('profit_C').value;
			if(profit_C=="")
			{
				profit_C=0;
			}
			$("#table_7  tfoot tr:eq(4)").find("input,select").each(function() {
				if($(this).attr("id")=='profit_A' || $(this).attr("id")=='profit_B' || $(this).attr("id")=='profit_C' )
				{
				 return;
				}
				var id=$(this).attr("id").split("_");
				
				var value=document.getElementById('Commercialsum1_'+id[1]).value;
				
				var percent=((parseInt(profit_C)*value)/100)+(value*1);
				document.getElementById('profit_'+id[1]).value=number_format_common(percent,1,0);
				//var PriceAfterCommercialCostPcs=number_format_common((((value*1)+percent)/12),1,0);
				//document.getElementById('Commercialsum1_'+id[1]).value=PriceAfterCommercialCostPcs;
				//document.getElementById('Commercialsum_'+id[1]).value=number_format_common(percent/12,1,0);
				//total_fabric_cost_dzn()
			})
	}
	
	if(str==5)
	{
		    var buyingcommision_C=document.getElementById('buyingcommision_C').value;
			if(buyingcommision_C=="")
			{
				buyingcommision_C=0;
			}
			$("#table_7  tfoot tr:eq(5)").find("input,select").each(function() {
				if($(this).attr("id")=='buyingcommision_A' || $(this).attr("id")=='buyingcommision_B' || $(this).attr("id")=='buyingcommision_C' )
				{
				 return;
				}
				var id=$(this).attr("id").split("_");
				var value=document.getElementById('profit_'+id[1]).value;
				var percent=((parseInt(buyingcommision_C)*value)/100);
				document.getElementById('buyingcommision_'+id[1]).value=number_format_common(percent,1,0);
				var QuotedPrice=(value*1)+percent;
				document.getElementById('quotedPrice_'+id[1]).value=number_format_common(QuotedPrice,1,0);
				//total_fabric_cost_dzn()
			})
	}
	
}

function calculate_trim_cost(fieldid,table)
{
            fieldid=fieldid.split("_");
			//alert(fieldid[1])
			var rate=document.getElementById('BTR_'+fieldid[1]).value;
			var amount=document.getElementById('CTR_'+fieldid[1]).value;
			//alert(rate);
			if(rate=="")
			{
				rate=0;
			}
			if(amount=="")
			{
				amount=0;
			}
			var trindex=(fieldid[1]*1)-1;
			$("#table_5  tbody tr:eq("+trindex+")").find("input,select").each(function() {
				var id=$(this).attr("id").split("_");																	   
				if(id[0]=='ATR' || id[0]=='BTR' || id[0]=='CTR' )
				{
				 return;
				}
				
				var value=rate*amount;
				$(this).val(value);
				sum_value($(this).attr("id"),'table_5')
			})	
}

function make_combine()
{
 var combine=document.getElementById('combine').checked;
 var average=document.getElementById('average').checked;
 if(combine==true)
 {
	 var combine_column=0;
	   var combine_price=0;
	   $("#table_7  tfoot tr:eq(6)").find("input,select").each(function() {
		if($(this).attr("id")=='quotedPrice_A' || $(this).attr("id")=='quotedPrice_B' || $(this).attr("id")=='quotedPrice_C' )
		{
		 return;
		}
		var id=$(this).attr("id").split("_");
		var QuotedPrice=(document.getElementById('quotedPrice_'+id[1]).value)*1;
		if(QuotedPrice>0)
		{
			combine_column=combine_column+1;
		}
		combine_price+=QuotedPrice;
		
	}) 
	   document.getElementById('makecombine_D').value=number_format_common(combine_price,1,0);
 }
 else
 {
	document.getElementById('makecombine_D').value=""; 
 }
}


function make_average()
{
 
 var average=document.getElementById('average').checked;
 if(average==true)
 {
	   var combine_column=0;
	   var combine_price=0;
	   $("#table_7  tfoot tr:eq(6)").find("input,select").each(function() {
		if($(this).attr("id")=='quotedPrice_A' || $(this).attr("id")=='quotedPrice_B' || $(this).attr("id")=='quotedPrice_C' )
		{
		 return;
		}
		var id=$(this).attr("id").split("_");
		var QuotedPrice=(document.getElementById('quotedPrice_'+id[1]).value)*1;
		if(QuotedPrice>0)
		{
			combine_column=combine_column+1;
		}
		combine_price+=QuotedPrice;
		
	}) 
	   document.getElementById('makeaverage_D').value=number_format_common(combine_price/combine_column,1,0);
 }
 else
 {
	document.getElementById('makeaverage_D').value=""; 
 }
}


function total_fabric_cost_dzn()
{
		$("#table_1  tfoot tr:eq(0)").find("input,select").each(function() {
			var id=$(this).attr("id").split("_");
			//var value=document.getElementById('sum2_'+id[1]).value;
			//var percent=((parseInt(Yper_1)*value)/100)+(value*1)
			//alert(percent)
			if('sum5_'+id[1]+"Y"=='sum5_AY')
			{
				return;
			}
			var yarn_cons =document.getElementById('sum3_'+id[1]).value;
			var fabric_price_kg=document.getElementById('sum4_'+id[1]+"Y").value;
			if(fabric_price_kg =="")
			{
			var total_fabric_cost_dzn=yarn_cons*1;
			document.getElementById('sum5_'+id[1]+"Y").value=number_format_common(total_fabric_cost_dzn,1,0);
			total_garments_cost_dzn()
			}
			else if(fabric_price_kg ==0)
			{
			var total_fabric_cost_dzn=yarn_cons*1;
			document.getElementById('sum5_'+id[1]+"Y").value=number_format_common(total_fabric_cost_dzn,1,0);
			total_garments_cost_dzn()	
			}
			else
			{
			var total_fabric_cost_dzn=yarn_cons*fabric_price_kg;
			document.getElementById('sum5_'+id[1]+"Y").value=number_format_common(total_fabric_cost_dzn,1,0);
			total_garments_cost_dzn()	
			}
		})
	
}

function total_garments_cost_dzn()
{
		$("#table_1  tfoot tr:eq(0)").find("input,select").each(function() {
			var id=$(this).attr("id").split("_");
			var total_fabric_cost_dzn= document.getElementById('sum5_'+id[1]+"Y").value;
			var cm_cost_dzn= document.getElementById('CM_'+id[1]+"Y").value;
			var OtherFabricCostTotal= document.getElementById('sum6_'+id[1]+"T").value;
			var EbellishmentCostTotal= document.getElementById('Esum1_'+id[1]+"E").value;
			var TrimCostTotal= document.getElementById('TRsum1_'+id[1]+"TR").value;
			var OtherCostTotal= document.getElementById('OCsum1_'+id[1]+"OC").value;
			var total_garments_cost_dzn=(total_fabric_cost_dzn*1)+(cm_cost_dzn*1)+(OtherFabricCostTotal*1)+(EbellishmentCostTotal*1)+(TrimCostTotal*1)+(OtherCostTotal*1);
			document.getElementById('sum7_'+id[1]+"T").value=number_format_common(total_garments_cost_dzn,1,0);
			document.getElementById('sum8_'+id[1]+"T").value=number_format_common(total_garments_cost_dzn/12,1,0);
			claculate_percent(3)
		})
	
}

function add_break_down_tr(table)
{
	if(table=='table_1')
	{
	  var row_num=$('#table_1 tbody tr').length;
	 $("#table_1 tbody tr:last").clone().find("input,select").each(function() {
			var row=$(this).attr("id").split("_");	
			var i=row[1]*1;
			
			if(row_num != i)
			{
				return;
			}
			i++;
			//alert(i)
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});
			}).end().appendTo("#table_1");
	$("#table_1  tbody tr:eq(0)").find("input,select").each(function() {
		 var fieldid=$(this).attr("id");
		 if(fieldid.split("_")[0]=="A")
		 {
			 return;
		 }
		 sum_value(fieldid,'table_1')
	});
	
	}
	if(table=='table_2')
	{
	  var row_num=$('#table_2 tbody tr').length;
	 $("#table_2 tbody tr:last").clone().find("input,select").each(function() {
			var row=$(this).attr("id").split("_");	
			var i=row[1]*1;
			
			if(row_num != i)
			{
				return;
			}
			i++;
			//alert(i)
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});
			}).end().appendTo("#table_2");
	 $("#table_2  tbody tr:eq(0)").find("input,select").each(function() {
		 var fieldid=$(this).attr("id");
		 if(fieldid.split("_")[0]=="AY")
		 {
			 return;
		 }
		 sum_value(fieldid,'table_2')
	});
	}
	if(table=='table_3')
	{
	  var row_num=$('#table_3 tbody tr').length;
	 $("#table_3 tbody tr:last").clone().find("input,select").each(function() {
			var row=$(this).attr("id").split("_");	
			var i=row[1]*1;
			
			if(row_num != i)
			{
				return;
			}
			i++;
			//alert(i)
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});
			}).end().appendTo("#table_3");
	 $("#table_3  tbody tr:eq(0)").find("input,select").each(function() {
		 var fieldid=$(this).attr("id");
		 if(fieldid.split("_")[0]=="AT")
		 {
			 return;
		 }
		 sum_value(fieldid,'table_3')
	});
	}
	
	if(table=='table_4')
	{
	  var row_num=$('#table_4 tbody tr').length;
	 $("#table_4 tbody tr:last").clone().find("input,select").each(function() {
			var row=$(this).attr("id").split("_");	
			var i=row[1]*1;
			
			if(row_num != i)
			{
				return;
			}
			i++;
			//alert(i)
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});
			}).end().appendTo("#table_4");
	 $("#table_4  tbody tr:eq(0)").find("input,select").each(function() {
		 var fieldid=$(this).attr("id");
		 if(fieldid.split("_")[0]=="AE")
		 {
			 return;
		 }
		 sum_value(fieldid,'table_4')
	});
	}
	
	if(table=='table_5')
	{
	  var row_num=$('#table_5 tbody tr').length;
	 $("#table_5 tbody tr:last").clone().find("input,select").each(function() {
			var row=$(this).attr("id").split("_");	
			var i=row[1]*1;
			
			if(row_num != i)
			{
				return;
			}
			i++;
			//alert(i)
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});
			}).end().appendTo("#table_5");
	 $("#table_5  tbody tr:eq(0)").find("input,select").each(function() {
		 var fieldid=$(this).attr("id");
		 if(fieldid.split("_")[0]=="ATR")
		 {
			 return;
		 }
		 sum_value(fieldid,'table_5')
	});
	}
	
	if(table=='table_6')
	{
	  var row_num=$('#table_6 tbody tr').length;
	 $("#table_6 tbody tr:last").clone().find("input,select").each(function() {
			var row=$(this).attr("id").split("_");	
			var i=row[1]*1;
			
			if(row_num != i)
			{
				return;
			}
			i++;
			//alert(i)
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});
			}).end().appendTo("#table_6");
	 $("#table_6  tbody tr:eq(0)").find("input,select").each(function() {
		 var fieldid=$(this).attr("id");
		 if(fieldid.split("_")[0]=="AOC")
		 {
			 return;
		 }
		 sum_value(fieldid,'table_6')
	});
	}
	 //context()
}

function id_detection(field_id)
{
	document.getElementById('num_cel').value=field_id
}


function delete_break_down_tr(table)
{
	var num_cel=document.getElementById('num_cel').value;
	if(num_cel=="")
	{
	alert("Select Row First")	
	return;
	}
	if(table=='table_1')
	{
			var rowNo=num_cel.split("_")[1]
			if(rowNo!=1)
			{
				/*var permission_array=permission.split("_");
				var updateid=$('#comarcialupdateid_'+rowNo).val();
				if(updateid !="" && permission_array[2]==1)
				{
				var booking=return_global_ajax_value(updateid, 'delete_row_comarcial_cost', '', 'requires/pre_cost_entry_controller');
				}*/
				var index=rowNo-1;
				//alert(index)
				$("#table_1 tbody tr:eq("+index+")").remove()
				var numRow = $('#table_1 tbody tr').length; 
				for(i = rowNo;i <= numRow;i++)
				{
					var index2=i-1
					$("#table_1 tbody tr:eq("+index2+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								'value': function(_, value) { return value }             
							}); 
					})
				}
				$("#table_1  tbody tr:eq(0)").find("input,select").each(function() {
					 var fieldid=$(this).attr("id");
					 if(fieldid.split("_")[0]=="A")
					 {
						 return;
					 }
					 sum_value(fieldid,'table_1')
				 });
			}
	}
	if(table=='table_2')
	{
			var rowNo=num_cel.split("_")[1]
			if(rowNo!=1)
			{
				/*var permission_array=permission.split("_");
				var updateid=$('#comarcialupdateid_'+rowNo).val();
				if(updateid !="" && permission_array[2]==1)
				{
				var booking=return_global_ajax_value(updateid, 'delete_row_comarcial_cost', '', 'requires/pre_cost_entry_controller');
				}*/
				var index=rowNo-1;
				//alert(index)
				$("#table_2 tbody tr:eq("+index+")").remove()
				var numRow = $('#table_2 tbody tr').length; 
				for(i = rowNo;i <= numRow;i++)
				{
					var index2=i-1
					$("#table_2 tbody tr:eq("+index2+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								'value': function(_, value) { return value }             
							}); 
					})
				}
				$("#table_2  tbody tr:eq(0)").find("input,select").each(function() {
					 var fieldid=$(this).attr("id");
					 if(fieldid.split("_")[0]=="AY")
					 {
						 return;
					 }
					 sum_value(fieldid,'table_2')
				});
			}
	}
	if(table=='table_3')
	{
			var rowNo=num_cel.split("_")[1]
			if(rowNo!=1)
			{
				/*var permission_array=permission.split("_");
				var updateid=$('#comarcialupdateid_'+rowNo).val();
				if(updateid !="" && permission_array[2]==1)
				{
				var booking=return_global_ajax_value(updateid, 'delete_row_comarcial_cost', '', 'requires/pre_cost_entry_controller');
				}*/
				var index=rowNo-1;
				//alert(index)
				$("#table_3 tbody tr:eq("+index+")").remove()
				var numRow = $('#table_3 tbody tr').length; 
				for(i = rowNo;i <= numRow;i++)
				{
					var index2=i-1
					$("#table_3 tbody tr:eq("+index2+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								'value': function(_, value) { return value }             
							}); 
					})
				}
				 $("#table_3  tbody tr:eq(0)").find("input,select").each(function() {
					 var fieldid=$(this).attr("id");
					 if(fieldid.split("_")[0]=="AT")
					 {
						 return;
					 }
					 sum_value(fieldid,'table_3')
				});
			}
	}
	if(table=='table_4')
	{
			var rowNo=num_cel.split("_")[1]
			if(rowNo!=1)
			{
				/*var permission_array=permission.split("_");
				var updateid=$('#comarcialupdateid_'+rowNo).val();
				if(updateid !="" && permission_array[2]==1)
				{
				var booking=return_global_ajax_value(updateid, 'delete_row_comarcial_cost', '', 'requires/pre_cost_entry_controller');
				}*/
				var index=rowNo-1;
				//alert(index)
				$("#table_4 tbody tr:eq("+index+")").remove()
				var numRow = $('#table_4 tbody tr').length; 
				for(i = rowNo;i <= numRow;i++)
				{
					var index2=i-1
					$("#table_4 tbody tr:eq("+index2+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								'value': function(_, value) { return value }             
							}); 
					})
				}
				 $("#table_4  tbody tr:eq(0)").find("input,select").each(function() {
					 var fieldid=$(this).attr("id");
					 if(fieldid.split("_")[0]=="AE")
					 {
						 return;
					 }
					 sum_value(fieldid,'table_4')
				});
			}
	}
	
	if(table=='table_5')
	{
			var rowNo=num_cel.split("_")[1]
			if(rowNo!=1)
			{
				/*var permission_array=permission.split("_");
				var updateid=$('#comarcialupdateid_'+rowNo).val();
				if(updateid !="" && permission_array[2]==1)
				{
				var booking=return_global_ajax_value(updateid, 'delete_row_comarcial_cost', '', 'requires/pre_cost_entry_controller');
				}*/
				var index=rowNo-1;
				//alert(index)
				$("#table_5 tbody tr:eq("+index+")").remove()
				var numRow = $('#table_5 tbody tr').length; 
				for(i = rowNo;i <= numRow;i++)
				{
					var index2=i-1
					$("#table_5 tbody tr:eq("+index2+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								'value': function(_, value) { return value }             
							}); 
					})
				}
				 $("#table_5  tbody tr:eq(0)").find("input,select").each(function() {
					 var fieldid=$(this).attr("id");
					 if(fieldid.split("_")[0]=="ATR")
					 {
						 return;
					 }
					 sum_value(fieldid,'table_5')
				});
			}
	}
	
	if(table=='table_6')
	{
			var rowNo=num_cel.split("_")[1]
			if(rowNo!=1)
			{
				/*var permission_array=permission.split("_");
				var updateid=$('#comarcialupdateid_'+rowNo).val();
				if(updateid !="" && permission_array[2]==1)
				{
				var booking=return_global_ajax_value(updateid, 'delete_row_comarcial_cost', '', 'requires/pre_cost_entry_controller');
				}*/
				var index=rowNo-1;
				//alert(index)
				$("#table_6 tbody tr:eq("+index+")").remove()
				var numRow = $('#table_6 tbody tr').length; 
				for(i = rowNo;i <= numRow;i++)
				{
					var index2=i-1
					$("#table_6 tbody tr:eq("+index2+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								'value': function(_, value) { return value }             
							}); 
					})
				}
				 $("#table_6  tbody tr:eq(0)").find("input,select").each(function() {
					 var fieldid=$(this).attr("id");
					 if(fieldid.split("_")[0]=="AOC")
					 {
						 return;
					 }
					 sum_value(fieldid,'table_6')
				});
			}
	}


}

function add_break_down_tr1(btn_id,table)
{
	if(table=='fabrication_table')
	{
	  var row_num=$('#fabrication_table tbody tr').length;
	 $("#fabrication_table tbody tr:last").clone().find("input,select").each(function() {
			var row=$(this).attr("id").split("_");	
			var i=row[1]*1;
			
			if(row_num != i)
			{
				return;
			}
			i++;
			//alert(i)
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});
			}).end().appendTo("#fabrication_table");
	}
	if(table=='measurment_table')
	{
	  var row_num=$('#measurment_table tbody tr').length;
	 $("#measurment_table tbody tr:last").clone().find("input,select").each(function() {
			var row=$(this).attr("id").split("_");	
			var i=row[1]*1;
			
			if(row_num != i)
			{
				return;
			}
			i++;
			//alert(i)
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});
			}).end().appendTo("#measurment_table");
	}
	
	if(table=='marker_table')
	{
	  var row_num=$('#marker_table tbody tr').length;
	 $("#marker_table tbody tr:last").clone().find("input,select").each(function() {
			var row=$(this).attr("id").split("_");	
			var i=row[1]*1;
			
			if(row_num != i)
			{
				return;
			}
			i++;
			//alert(i)
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});
			}).end().appendTo("#marker_table");
	}
}

function delete_break_down_tr1(btn_id,table)
{
    if(table=='fabrication_table')
	{
			var rowNo=btn_id.split("_")[1]
			if(rowNo!=1)
			{
				/*var permission_array=permission.split("_");
				var updateid=$('#comarcialupdateid_'+rowNo).val();
				if(updateid !="" && permission_array[2]==1)
				{
				var booking=return_global_ajax_value(updateid, 'delete_row_comarcial_cost', '', 'requires/pre_cost_entry_controller');
				}*/
				var index=rowNo-1;
				//alert(index)
				$("#fabrication_table tbody tr:eq("+index+")").remove()
				var numRow = $('#fabrication_table tbody tr').length; 
				for(i = rowNo;i <= numRow;i++)
				{
					var index2=i-1
					$("#fabrication_table tbody tr:eq("+index2+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								'value': function(_, value) { return value }             
							}); 
					})
				}
			}
	}	
	if(table=='measurment_table')
	{
			var rowNo=btn_id.split("_")[1]
			if(rowNo!=1)
			{
				/*var permission_array=permission.split("_");
				var updateid=$('#comarcialupdateid_'+rowNo).val();
				if(updateid !="" && permission_array[2]==1)
				{
				var booking=return_global_ajax_value(updateid, 'delete_row_comarcial_cost', '', 'requires/pre_cost_entry_controller');
				}*/
				var index=rowNo-1;
				//alert(index)
				$("#measurment_table tbody tr:eq("+index+")").remove()
				var numRow = $('#measurment_table tbody tr').length; 
				for(i = rowNo;i <= numRow;i++)
				{
					var index2=i-1
					$("#measurment_table tbody tr:eq("+index2+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								'value': function(_, value) { return value }             
							}); 
					})
				}
			}
	}
	if(table=='marker_table')
	{
			var rowNo=btn_id.split("_")[1]
			if(rowNo!=1)
			{
				/*var permission_array=permission.split("_");
				var updateid=$('#comarcialupdateid_'+rowNo).val();
				if(updateid !="" && permission_array[2]==1)
				{
				var booking=return_global_ajax_value(updateid, 'delete_row_comarcial_cost', '', 'requires/pre_cost_entry_controller');
				}*/
				var index=rowNo-1;
				//alert(index)
				$("#marker_table tbody tr:eq("+index+")").remove()
				var numRow = $('#marker_table tbody tr').length; 
				for(i = rowNo;i <= numRow;i++)
				{
					var index2=i-1
					$("#marker_table tbody tr:eq("+index2+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								'value': function(_, value) { return value }             
							}); 
					})
				}
			}
	}	
}

function fnc_simple_pri_dtls( operation )
{
		mst_id=document.getElementById('update_id').value ;
		if(mst_id=="" || mst_id==0 )
		{
		alert("Select a quatation");
		return;
		}
		
		var colnum=0;
		$("#table_1  thead tr").find("th").each(function() {
		colnum++;
		});
		
		var table_1_tbody="";
		var row_num=$('#table_1 tbody tr').length;
		for(i =1;i <= row_num;i++)
		{
			var index=i-1;
			var value="";
			$("#table_1  tbody tr:eq("+index+")").find("input,select").each(function() {
			    /*var id=$(this).attr("id");
				if (form_validation(id,'A')==false)
				{
					return;
				}*/
				var val=$(this).attr("value");
				if(val=="")
				{
					val=0
				}
				
				if(value =="")
				{
					value=value+val;
				}
				else
				{
					value=value+"_"+val;
				}
			});
			
			if(table_1_tbody=="")
			{
				table_1_tbody=table_1_tbody+value
			}
			else
			{
				table_1_tbody=table_1_tbody+"__"+value
			}
		}
		
		var table_1_tfoot="";
		var row_num=$('#table_1 tfoot tr').length;
		for(i =1;i <= row_num;i++)
		{
			var index=i-1;
			var value="";
			$("#table_1  tfoot tr:eq("+index+")").find("input,select").each(function() {
			    /*var id=$(this).attr("id");
				if (form_validation(id,'A')==false)
				{
					return;
				}*/
				var val=$(this).attr("value");
				if(val=="")
				{
					val=0
				}
				
				if(value =="")
				{
					value=value+val;
				}
				else
				{
					value=value+"_"+val;
				}
			});
			
			if(table_1_tfoot=="")
			{
				table_1_tfoot=table_1_tfoot+value
			}
			else
			{
				table_1_tfoot=table_1_tfoot+"__"+value
			}
		}
	
	
	//alert(table_1_tbody+"tttt"+table_1_tfoot);
	
	//return;
	
		var table_2_tbody="";
		var row_num2=$('#table_2 tbody tr').length;
		for(i =1;i <= row_num2;i++)
		{
			var index2=i-1;
			var value2="";
			$("#table_2  tbody tr:eq("+index2+")").find("input,select").each(function() {
				/*var id=$(this).attr("id");
				if (form_validation(id,'A')==false)
				{
					return;
				}*/
				var val2=$(this).attr("value");
				if(val2=="")
				{
					val2=0
				}
				if(value2 =="")
				{
				value2=value2+val2;
				}
				else
				{
				value2=value2+"_"+val2;
				}
			});
			if(table_2_tbody=="")
			{
			table_2_tbody=table_2_tbody+value2
			}
			else
			{
				table_2_tbody=table_2_tbody+"__"+value2
			}
		}
		
		var table_2_tfoot="";
		var row_num2=$('#table_2 tfoot tr').length;
		for(i =1;i <= row_num2;i++)
		{
			var index2=i-1;
			var value2="";
			$("#table_2  tfoot tr:eq("+index2+")").find("input,select").each(function() {
				/*var id=$(this).attr("id");
				if (form_validation(id,'A')==false)
				{
					return;
				}*/
				var val2=$(this).attr("value");
				if(val2=="")
				{
					val2=0
				}
				if(value2 =="")
				{
				value2=value2+val2;
				}
				else
				{
				value2=value2+"_"+val2;
				}
			});
			if(table_2_tfoot=="")
			{
			table_2_tfoot=table_2_tfoot+value2
			}
			else
			{
				table_2_tfoot=table_2_tfoot+"__"+value2
			}
		}
		
		//alert(table_2_tbody+"  ====   "+table_2_tfoot);
		//return;
		var table_3_tbody="";
		var row_num3=$('#table_3 tbody tr').length;
		for(i =1;i <= row_num3;i++)
		{
			var index3=i-1;
			var value3="";
			$("#table_3  tbody tr:eq("+index3+")").find("input,select").each(function() {
			    /*var id=$(this).attr("id");
				if (form_validation(id,'A')==false)
				{
					return;
				}*/
				var val3=$(this).attr("value");
				if(val3=="")
				{
					val3=0
				}
				if(value3 =="")
				{
				value3=value3+val3;
				}
				else
				{
				value3=value3+"_"+val3;
				}
			});
			if(table_3_tbody=="")
			{
			table_3_tbody=table_3_tbody+value3
			}
			else
			{
				table_3_tbody=table_3_tbody+"__"+value3
			}
		}
		
		var table_3_tfoot="";
		var row_num3=$('#table_3 tfoot tr').length;
		for(i =1;i <= row_num3;i++)
		{
			var index3=i-1;
			var value3="";
			$("#table_3  tfoot tr:eq("+index3+")").find("input,select").each(function() {
			    /*var id=$(this).attr("id");
				if (form_validation(id,'A')==false)
				{
					return;
				}*/
				var val3=$(this).attr("value");
				if(val3=="")
				{
					val3=0
				}
				if(value3 =="")
				{
				value3=value3+val3;
				}
				else
				{
				value3=value3+"_"+val3;
				}
			});
			if(table_3_tfoot=="")
			{
			table_3_tfoot=table_3_tfoot+value3
			}
			else
			{
				table_3_tfoot=table_3_tfoot+"__"+value3
			}
		}
		//alert(table_3_tbody+"  ====   "+table_3_tfoot);
		//return;
		
		var table_4_tbody="";
		var row_num4=$('#table_4 tbody tr').length;
		for(i =1;i <= row_num4;i++)
		{
			var index4=i-1;
			var value4="";
			$("#table_4  tbody tr:eq("+index4+")").find("input,select").each(function() {
			    /*var id=$(this).attr("id");
				if (form_validation(id,'A')==false)
				{
					return;
				}*/
				var val4=$(this).attr("value");
				if(val4=="")
				{
					val4=0
				}
				if(value4 =="")
				{
				value4=value4+val4;
				}
				else
				{
				value4=value4+"_"+val4;
				}
			});
			if(table_4_tbody=="")
			{
			table_4_tbody=table_4_tbody+value4
			}
			else
			{
				table_4_tbody=table_4_tbody+"__"+value4
			}
		}
		
		var table_4_tfoot="";
		var row_num4=$('#table_4 tfoot tr').length;
		for(i =1;i <= row_num4;i++)
		{
			var index4=i-1;
			var value4="";
			$("#table_4  tfoot tr:eq("+index4+")").find("input,select").each(function() {
			    /*var id=$(this).attr("id");
				if (form_validation(id,'A')==false)
				{
					return;
				}*/
				var val4=$(this).attr("value");
				if(val4=="")
				{
					val4=0
				}
				if(value4 =="")
				{
				value4=value4+val4;
				}
				else
				{
				value4=value4+"_"+val4;
				}
			});
			if(table_4_tfoot=="")
			{
			table_4_tfoot=table_4_tfoot+value4
			}
			else
			{
				table_4_tfoot=table_4_tfoot+"__"+value4
			}
		}
		//alert(table_4_tbody+"  ====   "+table_4_tfoot);
		//return;
		
		
		var table_5_tbody="";
		var row_num5=$('#table_5 tbody tr').length;
		for(i =1;i <= row_num5;i++)
		{
			var index5=i-1;
			var value5="";
			$("#table_5  tbody tr:eq("+index5+")").find("input,select").each(function() {
			    /*var id=$(this).attr("id");
				if (form_validation(id,'A')==false)
				{
					return;
				}*/
				var val5=$(this).attr("value");
				if(val5=="")
				{
					val5=0
				}
				if(value5 =="")
				{
				value5=value5+val5;
				}
				else
				{
				value5=value5+"_"+val5;
				}
			});
			if(table_5_tbody=="")
			{
			table_5_tbody=table_5_tbody+value5
			}
			else
			{
				table_5_tbody=table_5_tbody+"__"+value5
			}
		}
		
		var table_5_tfoot="";
		var row_num5=$('#table_5 tfoot tr').length;
		for(i =1;i <= row_num5;i++)
		{
			var index5=i-1;
			var value5="";
			$("#table_5  tfoot tr:eq("+index5+")").find("input,select").each(function() {
			    /*var id=$(this).attr("id");
				if (form_validation(id,'A')==false)
				{
					return;
				}*/
				var val5=$(this).attr("value");
				if(val5=="")
				{
					val5=0
				}
				if(value5 =="")
				{
				value5=value5+val5;
				}
				else
				{
				value5=value5+"_"+val5;
				}
			});
			if(table_5_tfoot=="")
			{
			table_5_tfoot=table_5_tfoot+value5
			}
			else
			{
				table_5_tfoot=table_5_tfoot+"__"+value5
			}
		}
		//alert(table_5_tbody+"  ====   "+table_5_tfoot);
		//return;
		
		
		var table_6_tbody="";
		var row_num6=$('#table_6 tbody tr').length;
		for(i =1;i <= row_num6;i++)
		{
			var index6=i-1;
			var value6="";
			$("#table_6  tbody tr:eq("+index6+")").find("input,select").each(function() {
			    /*var id=$(this).attr("id");
				if (form_validation(id,'A')==false)
				{
					return;
				}*/
				var val6=$(this).attr("value");
				if(val6=="")
				{
					val6=0
				}
				if(value6 =="")
				{
				value6=value6+val6;
				}
				else
				{
				value6=value6+"_"+val6;
				}
			});
			if(table_6_tbody=="")
			{
			table_6_tbody=table_6_tbody+value6
			}
			else
			{
				table_6_tbody=table_6_tbody+"__"+value6
			}
		}
		
		var table_6_tfoot="";
		var row_num6=$('#table_6 tfoot tr').length;
		for(i =1;i <= row_num6;i++)
		{
			var index6=i-1;
			var value6="";
			$("#table_6  tfoot tr:eq("+index6+")").find("input,select").each(function() {
			    /*var id=$(this).attr("id");
				if (form_validation(id,'A')==false)
				{
					return;
				}*/
				var val6=$(this).attr("value");
				if(val6=="")
				{
					val6=0
				}
				if(value6 =="")
				{
				value6=value6+val6;
				}
				else
				{
				value6=value6+"_"+val6;
				}
			});
			if(table_6_tfoot=="")
			{
			table_6_tfoot=table_6_tfoot+value6
			}
			else
			{
				table_6_tfoot=table_6_tfoot+"__"+value6
			}
		}
		//alert(table_6_tbody+"  ====   "+table_6_tfoot);
		//return;
		
		
	
		var table_7_tfoot="";
		var row_num7=$('#table_7 tfoot tr').length;
		for(i =1;i <= row_num7;i++)
		{
			var index7=i-1;
			var value7="";
			$("#table_7  tfoot tr:eq("+index7+")").find("input,select").each(function() {
			    /*var id=$(this).attr("id");
				if (form_validation(id,'A')==false)
				{
					return;
				}*/
				var val7=$(this).attr("value");
				if(val7=="")
				{
					val7=0
				}
				if(value7 =="")
				{
				value7=value7+val7;
				}
				else
				{
				value7=value7+"_"+val7;
				}
			});
			if(table_7_tfoot=="")
			{
			table_7_tfoot=table_7_tfoot+value7
			}
			else
			{
				table_7_tfoot=table_7_tfoot+"__"+value7
			}
		}
		//alert(table_7_tfoot);
		//return;
		
		
		
		
		
		
		
		
		var tot_row_fab="";
		var row_num_fab=$('#fabrication_table tbody tr').length;
		for(i =1;i <= row_num_fab;i++)
		{
			var indexfab=i-1;
			var valuefab="";
			$("#fabrication_table  tbody tr:eq("+indexfab+")").find("input,select").each(function() {
			    /*var id=$(this).attr("id");
				if (form_validation(id,'A')==false)
				{
					return;
				}*/
				var valfab=$(this).attr("value");
				if(valfab=="")
				{
					valfab=0
				}
				if(valuefab =="")
				{
				valuefab=valuefab+valfab;
				}
				else
				{
				valuefab=valuefab+"_"+valfab;
				}
			});
			if(tot_row_fab=="")
			{
			tot_row_fab=tot_row_fab+valuefab.split("_+")[0];
			}
			else
			{
				tot_row_fab=tot_row_fab+"__"+valuefab.split("_+")[0];
			}
		}
		
		var tot_row_mes="";
		var row_num_mes=$('#measurment_table tbody tr').length;
		for(i =1;i <= row_num_mes;i++)
		{
			var indexmes=i-1;
			var valuemes="";
			$("#measurment_table  tbody tr:eq("+indexmes+")").find("input,select").each(function() {
			    /*var id=$(this).attr("id");
				if (form_validation(id,'A')==false)
				{
					return;
				}*/
				var valmes=$(this).attr("value");
				if(valmes=="")
				{
					valmes=0
				}
				if(valuemes =="")
				{
				valuemes=valuemes+valmes;
				}
				else
				{
				valuemes=valuemes+"_"+valmes;
				}
			});
			if(tot_row_mes=="")
			{
			tot_row_mes=tot_row_mes+valuemes.split("_+")[0];
			}
			else
			{
				tot_row_mes=tot_row_mes+"__"+valuemes.split("_+")[0];
			}
		}
		
		var tot_row_mar="";
		var row_num_mar=$('#marker_table tbody tr').length;
		for(i =1;i <= row_num_mar;i++)
		{
			var indexmar=i-1;
			var valuemar="";
			$("#marker_table  tbody tr:eq("+indexmar+")").find("input,select").each(function() {
			    /*var id=$(this).attr("id");
				if (form_validation(id,'A')==false)
				{
					return;
				}*/
				var valmar=$(this).attr("value");
				if(valmar=="")
				{
					valmar=0
				}
				if(valuemar =="")
				{
				valuemar=valuemar+valmar;
				}
				else
				{
				valuemar=valuemar+"_"+valmar;
				}
			});
			if(tot_row_mar=="")
			{
			tot_row_mar=tot_row_mar+valuemar.split("_+")[0];
			}
			else
			{
				tot_row_mar=tot_row_mar+"__"+valuemar.split("_+")[0];
			}
		}
		
	   
		var data="action=save_update_delet_simple_pri&operation="+operation+'&table_1_tbody='+table_1_tbody+'&table_1_tfoot='+table_1_tfoot+'&table_2_tbody='+table_2_tbody+'&table_2_tfoot='+table_2_tfoot+'&table_3_tbody='+table_3_tbody+'&table_3_tfoot='+table_3_tfoot+'&table_4_tbody='+table_4_tbody+'&table_4_tfoot='+table_4_tfoot+'&table_5_tbody='+table_5_tbody+'&table_5_tfoot='+table_5_tfoot+'&table_6_tbody='+table_6_tbody+'&table_6_tfoot='+table_6_tfoot+'&table_7_tfoot='+table_7_tfoot+'&tot_row_fab='+tot_row_fab+'&tot_row_mes='+tot_row_mes+'&tot_row_mar='+tot_row_mar+'&mst_id='+mst_id;
		
		//alert(data);
		//return;
		freeze_window(operation);
		http.open("POST","requires/quotation_entry_simple_final_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_simple_pri_dtls_reponse;
	
}

function fnc_simple_pri_dtls_reponse()
{ 	
    var reponse=trim(http.responseText).split('**');
	/*if(reponse[0]==15) 
	{ 
		 setTimeout('fnc_fabric_cost_dtls('+ reponse[1]+')',8000); 
	}*/
	if(http.readyState == 4) 
	{
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		show_table()
		release_freezing();
	}
}

function show_table()
{
	var mst_id=document.getElementById('update_id').value ;
	if(mst_id=="" || mst_id==0 )
	{
	alert("Select a quatation");
	return;
	}
	var coll_add=document.getElementById('coll_add').value;
	if(coll_add=="")
	{
		coll_add=0;
	}
	show_list_view(mst_id+"_"+coll_add,'show_table_data','container','../woven_order/requires/quotation_entry_simple_final_controller','');
	document.getElementById('coll_add').value=0;
}

function generate_report(type)
{
	//alert(type)
	if (form_validation('txt_quotation_id','Please Select The Job Number.')==false)
	{
		return;
	}
	else
	{
		eval(get_submitted_variables('txt_quotation_id*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_quotation_date'));
		var data="action=generate_report&type="+type+"&"+get_submitted_data_string('txt_quotation_id*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_quotation_date',"../../");
		
		http.open("POST","requires/quotation_entry_simple_final_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
	}
}


function fnc_generate_report_reponse()
{
	if(http.readyState == 4) 
	{
		$('#data_panel').html( http.responseText );
		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}

function add_column()
{
    var coll_add=document.getElementById('coll_add').value;
	var r=confirm("Click Ok to save Existing Data and Add Column\n Click  Cencel not to Add Column");
	if(r==false)
	{
		return;
	}
	else
	{
	fnc_simple_pri_dtls( 0 )	
	}
}

function delete_column(colindex)
{
	var tr = $(colindex).parent();
	for (var i = 0; i < tr.children().length; i++) 
	{
		if (tr.children().get(i) == colindex) {
		var column = i;
		break;
		}
	}
	if(i==tr.children().length-1)
	{
		var r=confirm("Are You Sure To Remove Last Column");
		if(r==false)
		{
			return;
		}
		else
		{
		var table_1_width=$('#table_1').width();
		$('#table_1 tr').find('td:eq('+i+'),th:eq('+i+')').remove();
		
		$('#table_1 tr').find('td:eq('+i+'),th:eq('+i+')').find("input,select").each(function() {
		//alert($(this).attr("id"));
		})
		$('#table_1').width(table_1_width-100);
		
		var table_2_width=$('#table_2').width();
		$('#table_2 tr').find('td:eq('+i+'),th:eq('+i+')').remove();
		
		$('#table_2 tr').find('td:eq('+i+'),th:eq('+i+')').find("input,select").each(function() {
		//alert($(this).attr("id"));
		})
		$('#table_2').width(table_2_width-100);
		
		var table_3_width=$('#table_3').width();
		$('#table_3 tr').find('td:eq('+i+'),th:eq('+i+')').remove();
		
		$('#table_3 tr').find('td:eq('+i+'),th:eq('+i+')').find("input,select").each(function() {
		//alert($(this).attr("id"));
		})
		$('#table_3').width(table_3_width-100);
		}
	}
}

function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currercy').val();
		var quotation_date = $('#txt_quotation_date').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+quotation_date, 'check_conversion_rate', '', 'requires/quotation_entry_simple_final_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
		
	}

</script>
<script>
function context()
{
      $(function() {
        $('.mythingy').contextPopup({
          title: 'My Popup Menu',
          items: [
            {label:'Add Row',     icon:'icons/shopping-basket.png',               action:function() { add_break_down_tr() } },
            {label:'Another Thing', icon:'icons/receipt-text.png',                action:function() { alert('clicked 2') } },
            {label:'Blah Blah',     icon:'icons/book-open-list.png',              action:function() { alert('clicked 3') } },
            null, // divider
            {label:'Sheep',         icon:'icons/application-monitor.png',         action:function() { alert('clicked 4') } },
            {label:'Cheese',        icon:'icons/bin-metal.png',                   action:function() { alert('clicked 5') } },
            {label:'Bacon',         icon:'icons/magnifier-zoom-actual-equal.png', action:function() { alert('clicked 6') } },
            null, // divider
            {label:'Onwards',       icon:'icons/application-table.png',           action:function() { alert('clicked 7') } },
            {label:'Flutters',      icon:'icons/cassette.png',                    action:function() { alert('clicked 8') } }
          ]
        });
      });
}
    </script>
 
</head>
 
<body onLoad="set_hotkey();set_auto_complete('price_quation_mst')" >
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission);  ?>
       <fieldset style="width:1070px;">
                        <legend>Price Quotation</legend>
                        <form name="quotationmst_1" id="quotationmst_1" autocomplete="off"> 
                            <div style="width:1070px;">  
                                <table  width="100%" cellspacing="2" cellpadding=""  border="0">
                                <tr>
                                <td colspan="8"  align="center" class="must_entry_caption">Quotation ID &nbsp; <input type="text" id="txt_quotation_id" name="txt_quotation_id" class="text_boxes" style="width:150px;" readonly placeholder="Browse Quotation" onDblClick="openmypage('requires/quotation_entry_simple_final_controller.php?action=quotation_id_popup','Quotation ID Selection Form')"/></td>
                                </tr>
                                    <tr>
                                        <td align="right" width="150" class="must_entry_caption">Inquery ID</td>
                                        <td  width="150">
                                        <input type="text" id="txt_quotation_inquery_sys_id" name="txt_quotation_inquery_sys_id" class="text_boxes" style="width:150px;" readonly placeholder="Browse Quotation Inquery" onDblClick="openmypage_inquery()"/>
                                        <input type="hidden" class="text_boxes"  style="width:50px;"  id="inquery_id" name="inquery_id"/> 
                                        </td>
                                        <td  align="right"  width="150" class="must_entry_caption">Company</td>
                                        <td  width="150">
                                        <?
                                        echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/quotation_entry_simple_final_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );  load_drop_down( 'requires/quotation_entry_simple_final_controller', this.value, 'load_drop_down_agent', 'agent_td');cm_cost_predefined_method(this.value)" );
                                        ?>
                                        </td>
                                        <td align="right"  width="180" class="must_entry_caption">Buyer</td>
                                        <td id="buyer_td"  width="150">
                                        <? 
                                        echo create_drop_down( "cbo_buyer_name", 160, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                                        ?>
                                        </td>
                                        <td align="right"  width="130" class="must_entry_caption">Style Ref</td>
                                        <td>
                                        <input class="text_boxes" type="text" style="width:150px;" name="txt_style_ref" id="txt_style_ref" maxlength="75" title="Maximum 75 Character" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">Revised No</td>
                                        <td><input class="text_boxes_numeric" type="text" style="width:150px;" name="txt_revised_no" id="txt_revised_no"/></td>
                                        <td align="right">Pord. Dept.</td>
                                        <td>
                                        <? 
                                        echo create_drop_down( "cbo_pord_dept", 100, $product_dept,"", 1, "-- Select --",0, "" );
                                        ?>
                                        <input class="text_boxes" type="text" style="width:40px;" name="txt_product_code" id="txt_product_code" maxlength="10" title="Maximum 10 Character" />
                                        </td>
                                        <td align="right">Style Desc.</td>
                                        <td colspan="3"><input class="text_boxes" type="text" style="width:440px;" name="txt_style_desc" id="txt_style_desc" maxlength="100" title="Maximum 100 Character"/></td>
                                    </tr> 
                                    <tr>
                                        <td align="right" class="must_entry_caption">Currency</td>
                                        <td>
                                        <? //set_exchange_rate(this.value)
                                        echo create_drop_down( "cbo_currercy",60, $currency,"", 0, "", 2, "" ,"","");
                                        ?>	
                                        ER. <input class="text_boxes_numeric" type="text" style="width:60px;" name="txt_exchange_rate" id="txt_exchange_rate" onChange="calculate_cm_cost_with_method()"/>  
                                        </td>
                                        <td align="right">Agent</td>
                                        <td id="agent_td">
                                        <?	  
                                        echo create_drop_down( "cbo_agent", 160, $blank_array,"", 1, "-- Select Agent --", $selected, "" );
                                        ?>
                                        </td>
                                        <td align="right">Offer Qnty.</td>
                                        <td><input class="text_boxes_numeric" type="text" style="width:150px;" name="txt_offer_qnty" id="txt_offer_qnty"/></td>
                                        <td align="right">Region</td>
                                        <td>
                                        <? 
                                        echo create_drop_down( "cbo_region", 160, $region, 1, "-- Select Region --", 0, "" );
                                        ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td  align="right">Color Range</td>
                                        <td>
                                        <? 
                                        echo create_drop_down( "cbo_color_range", 160, $color_range,"", 1, "-- Select--", 0, "" );
                                        ?>
                                        </td>
                                        <td  align="right">Incoterm</td>
                                        <td>
                                        <? 
                                        echo create_drop_down( "cbo_inco_term", 160, $incoterm,"", 0, "",1,"" );
                                        ?>
                                        </td>
                                        <td align="right">Incoterm Place</td>
                                        <td><input class="text_boxes" type="text" style="width:150px;" name="txt_incoterm_place" id="txt_incoterm_place" maxlength="100" title="Maximum 100 Character"/></td>
                                        <td align="right">Machine/Line</td>
                                        <td><input class="text_boxes_numeric" type="text" style="width:150px;" name="txt_machine_line" id="txt_machine_line" /></td> 
                                    </tr> 
                                    <tr>
                                        <td align="right">Prod/Line/Hr</td>
                                        <td><input class="text_boxes_numeric" type="text" style="width:150px;" name="txt_prod_line_hr" id="txt_prod_line_hr" /></td>
                                        
                                        
                                        <td align="right" class="must_entry_caption">Costing Per</td>
                                        <td>
                                        <? 
                                        echo create_drop_down( "cbo_costing_per", 160, $costing_per, "",1, "-- Select--", 1, "change_caption_cost_dtls( this.value, 'change_caption_dzn' )","","" );
                                        
                                        ?>
                                        </td>
                                        <td align="right" class="must_entry_caption">Quot. Date</td>
                                        <td><input class="datepicker" type="text" style="width:150px;" name="txt_quotation_date" id="txt_quotation_date" onChange="check_exchange_rate();"/></td>
                                        

                                        <td align="right">OP Date</td>
                                        <td><input class="datepicker" type="text" style="width:150px;" name="txt_op_date" id="txt_op_date" onChange="calculate_lead_time()"/></td>
                                    
                                    </tr>
                                    <tr>
                                        <td align="right">Factory</td>
                                        <td><input class="text_boxes" type="text" style="width:150px;" name="txt_factory" id="txt_factory" maxlength="100" title="Maximum 100 Character"/></td>
                                        
                                        <td align="right" class="must_entry_caption">Order UOM </td> 
                                        <td>
                                        <? 
                                        echo create_drop_down( "cbo_order_uom",60, $unit_of_measurement, "",0, "", 1, "change_caption_cost_dtls( this.value, 'change_caption_pcs' )","","1,58" );
                                        ?>
                                        <input type="button" id="set_button" class="image_uploader" style="width:95px;" value="Item Details" onClick="open_set_popup(document.getElementById('cbo_order_uom').value)" />
                                        <input type="hidden" id="set_breck_down" />     
                                        <input type="hidden" id="item_id" /> 
                                        <input type="hidden" id="tot_set_qnty" />    
                                        </td>
                                        <td align="right">Images</td>
                                        <td><input type="button" id="image_button" class="image_uploader" style="width:160px" value="CLICK TO ADD IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'quotation_entry', 0 ,1)" />                                          
                                        
                                        </td> 
                                        <td align="right">Est. Ship Date</td>
                                        <td>
                                        <input class="datepicker" type="text" style="width:70px;" name="txt_est_ship_date" id="txt_est_ship_date" onChange="calculate_lead_time()"/>
                                        <input class="text_boxes" type="text" style="width:60px;"  name="txt_lead_time" id="txt_lead_time"  readonly/>
                                        </td>
                                    </tr>
                                    <tr> 
                                        <td align="right">Sew. SMV</td>
                                        <td >
                                        <input class="text_boxes_numeric" type="text" style="width:150px;" name="txt_sew_smv" id="txt_sew_smv" onChange="calculate_cm_cost_with_method()" />  
                                        </td>
                                        <td align="right">Sew Effi. %</td>
                                        <td>
                                        <input class="text_boxes_numeric" type="text" style="width:150px;" name="txt_sew_efficiency_per" id="txt_sew_efficiency_per" onChange="calculate_cm_cost_with_method()" />  
                                        </td>
                                        <td align="right">Cut. SMV</td>
                                        <td >
                                        <input class="text_boxes_numeric" type="text" style="width:150px;" name="txt_cut_smv" id="txt_cut_smv" onChange="calculate_cm_cost_with_method()" />  
                                        </td>
                                        
                                        <td align="right">Cut Efficiency %</td>
                                        <td>
                                        <input class="text_boxes_numeric" type="text" style="width:150px;" name="txt_cut_efficiency_per" id="txt_cut_efficiency_per" onChange="calculate_cm_cost_with_method()"  />  
                                        </td>
                                    </tr>
                                    <tr> 
                                        <td align="right">Season</td>
                                        <td id="season_td">
                         <? echo create_drop_down( "cbo_season_name", 165, $blank_array,"", 1, "-- Select Season --", $selected, "" ); ?>
                                        <!--<input class="text_boxes" type="text" style="width:150px;" name="txt_season" id="txt_season" maxlength="50" title="Maximum 500 Character"/>-->  
                                        </td>
                                        <td align="right">M. List</td>
                                        
                                        <td>
                                        <input class="text_boxes" type="text" style="width:150px;" name="txt_m_list" id="txt_m_list" maxlength="500" title="Maximum 500 Character"/>  
                                        </td>
                                         <td align="right">BH Merchant</td>
                                        
                                        <td>
                                        <input class="text_boxes" type="text" style="width:150px;" name="txt_bh_merchant" id="txt_bh_merchant" maxlength="500" title="Maximum 500 Character"/>  
                                        </td>
                                        <td align="right">Approved</td>
                                        <td width=""> 
                                        <? 
                                        echo create_drop_down( "cbo_approved_status", 160, $yes_no,"", 0, "", 2, "",1,"" );
                                        ?>
                                        </td>
                                    </tr>
                                     <tr>
                                     <td align="right">Remarks</td>
                                        
                                        <td colspan="5">
                                        <input class="text_boxes" type="text" style="width:700px;" name="txt_remarks" id="txt_remarks" maxlength="500" title="Maximum 500 Character"/>  
                                        </td> 
                                       
                                        
                                        <td align="right">Status</td>
                                        <td width=""> 
                                        <?
										
										echo create_drop_down( "cbo_quotation_status", 160, $buyer_quotation_status,"", 1, "-- Select--", 0, "" );
                                        ?>
                                        </td>
                                    </tr>
                                    <tr> 
                                        <td align="right" style="display:none">Efficiency Wastage%</td>
                                        <td >
                                        <input class="text_boxes_numeric" type="hidden" style="width:150px;" name="txt_efficiency_wastage" id="txt_efficiency_wastage" onChange="calculate_cm_cost_with_method()" readonly />  
                                        </td>
                                        <td align="center" height="10" valign="middle"  colspan="6">  </td>
                                    </tr>
                                    <tr> 
                                        <td align="right" valign="top" class="button_container" colspan="5"> 
                                        <input type="hidden" id="cm_cost_predefined_method_id" value="" />
                                        <input type="hidden" id="update_id" value="" />
                                        <? 
                                        $dd="disable_enable_fields( 'cbo_company_name*cbo_buyer_name*txt_style_ref*txt_revised_no*cbo_pord_dept*txt_style_desc*cbo_currercy*cbo_agent*txt_offer_qnty*cbo_region*cbo_color_range*cbo_inco_term*txt_incoterm_place*txt_machine_line*txt_prod_line_hr*cbo_costing_per*txt_quotation_date*txt_est_ship_date*txt_factory*txt_remarks*garments_nature*cbo_order_uom*set_button*image_button*save1*update1*Delete1*txt_lab_test_pre_cost*txt_inspection_pre_cost*txt_cm_pre_cost*txt_freight_pre_cost*txt_common_oh_pre_cost*txt_1st_quoted_price_pre_cost*txt_first_quoted_price_date*txt_revised_price_pre_cost*txt_revised_price_date*txt_confirm_price_pre_cost*txt_confirm_date_pre_cost*save2*update2*Delete2', 0 )";
                                        echo load_submit_buttons( $permission, "fnc_quotation_entry", 0,0 ,"reset_form('quotationmst_1*quotationdtls_2','cost_container','','cbo_currercy,2*cbo_costing_per,1*cbo_approved_status,2',$dd)",1,1) ; ?>  
                                        
                                        </td>
                                        <td align="left" valign="top" class="button_container" colspan="5">
                                        <input type="button" id="copy_btn" class="formbutton" value="Copy" onClick="copy_quatation(5)" />
                                        <input type="button" id="report_btn" class="formbutton" value="Quotation Rpt" onClick="generate_report('preCostRpt')" />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </form>
                    </fieldset>
                    <br/>
                    <fieldset style="width:1320px;">
                   <div class="container" style="width:1315px; overflow: hidden;" id="container">
                      <div class="left" style="float: left;width:433px;background: white;">
                      <div style="">
                      Add <input class="text_boxes_numeric"  style="width:50px;" name="coll_add" id="coll_add"/>  after last Column  
                    <input type="button" id="coll_addb" style="width:50px" class="formbutton" value="ADD" onClick="add_column()"/>
                    <input type="hidden" class="text_boxes"  style="width:70px;" name="num_cel" id="num_cel"/> 
                    <table width="433" cellspacing="0" cellpadding=""  border="0" class="rpt_table" rules="all" id="fabrication_table">
					<thead>
					<tr>
					<th width="170" style="text-align:left;">Fabrication</th>
					<th width="170">
					</th>
					<th width="80">
					</th>
					</tr>
					</thead>
					<tbody>
					<tr>
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationA_1" id="txtfabricationA_1" maxlength="100" title="Maximum 100 Character" value="Color"/></td>
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationB_1" id="txtfabricationB_1" maxlength="100" title="Maximum 100 Character"/></td>
					<td width="80">
					<input type="button" id="increasefabrication_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'fabrication_table')" />
					<input type="button" id="decreasefabrication_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'fabrication_table')"/>
					</td>
					</tr>
					<tr>
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationA_2" id="txtfabricationA_2" maxlength="100" title="Maximum 100 Character" value="Fabrication"/></td>
					
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationB_2" id="txtfabricationB_2" maxlength="100" title="Maximum 100 Character"/></td>
					<td width="80">
					<input type="button" id="increasefabrication_2" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'fabrication_table')" />
					<input type="button" id="decreasefabrication_2" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'fabrication_table')"/>
					</td>
					</tr>
					 <tr>
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationA_3" id="txtfabricationA_3" maxlength="100" title="Maximum 100 Character" value="Composition"/></td>
					
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationB_3" id="txtfabricationB_3" maxlength="100" title="Maximum 100 Character"/></td>
					<td width="80">
					<input type="button" id="increasefabrication_3" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'fabrication_table')" />
					<input type="button" id="decreasefabrication_3" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'fabrication_table')"/>
					</td>
					</tr>
					<tr>
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationA_4" id="txtfabricationA_4" maxlength="100" title="Maximum 100 Character" value="Fabric Weight"/></td>
					
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationB_4" id="txtfabricationB_4" maxlength="100" title="Maximum 100 Character"/></td>
					<td width="80">
					<input type="button" id="increasefabrication_4" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'fabrication_table')" />
					<input type="button" id="decreasefabrication_4" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'fabrication_table')"/>
					</td>
					</tr>
					</tbody>
					</table>
                    <br/>
                    <table width="433" cellspacing="0" cellpadding=""  border="0" class="rpt_table" rules="all" id="measurment_table">
					<thead>
					<tr>
					<th width="170" style="text-align:left">Measurment</th>
					<th width="170">
					</th>
					<th width="80">
					</th>
					</tr>
					</thead>
					<tbody>
					<tr>
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentA_1" id="measurmentA_1" maxlength="100" title="Maximum 100 Character" value="HSP LENGHT"/></td>
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentB_1" id="measurmentB_1" maxlength="100" title="Maximum 100 Character"/></td>
					<td width="80">
					<input type="button" id="increasemeasurment_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'measurment_table')" />
					<input type="button" id="decreasemeasurment_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'measurment_table')"/>
					</td>
					</tr>
					<tr>
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentA_2" id="measurmentA_2" maxlength="100" title="Maximum 100 Character" value="1/2 CHEST"/></td>
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentB_2" id="measurmentB_2" maxlength="100" title="Maximum 100 Character"/></td>
					<td width="80">
					<input type="button" id="increasemeasurment_2" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'measurment_table')" />
					<input type="button" id="decreasemeasurment_2" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'measurment_table')"/>
					</td>
					</tr>
					<tr>
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentA_3" id="measurmentA_3" maxlength="100" title="Maximum 100 Character" value="1/2 Bottom"/></td>
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentB_3" id="measurmentB_3" maxlength="100" title="Maximum 100 Character"/></td>
					<td width="80">
					<input type="button" id="increasemeasurment_3" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'measurment_table')" />
					<input type="button" id="decreasemeasurment_3" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'measurment_table')"/>
					</td>
					</tr>
					<tr>
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentA_4" id="measurmentA_4" maxlength="100" title="Maximum 100 Character" value="1/2 HIP"/></td>
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentB_4" id="measurmentB_4" maxlength="100" title="Maximum 100 Character"/></td>
					<td width="80">
					<input type="button" id="increasemeasurment_4" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'measurment_table')" />
					<input type="button" id="decreasemeasurment_4" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'measurment_table')"/>
					</td>
					</tr>
					
					</tbody>
					</table>
                    <br/>
                    <table width="433" cellspacing="0" cellpadding=""  border="0" class="rpt_table" rules="all" id="marker_table">
				   <thead>
					<tr>
					<th width="170" style="text-align:left">Marker</th>
					<th width="170">
					</th>
					<th width="80">
					</th>
					</tr>
					</thead>
					<tbody>
					<tr>
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerA_1" id="markerA_1" maxlength="100" title="Maximum 500 Character" value="Width"/></td>
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerB_1" id="markerB_1" maxlength="100" title="Maximum 500 Character"/></td>
					<td width="80">
					<input type="button" id="increasemarker_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'marker_table')" />
					<input type="button" id="decreasemarker_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'marker_table')"/>
					</td>
					</tr>
					<tr>
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerA_2" id="markerA_2" maxlength="100" title="Maximum 500 Character" value="Length"/></td>
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerB_2" id="markerB_2" maxlength="100" title="Maximum 500 Character"/></td>
					<td width="80">
					<input type="button" id="increasemarker_2" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'marker_table')" />
					<input type="button" id="decreasemarker_2" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'marker_table')"/>
					</td>
					</tr>
					<tr>
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerA_3" id="markerA_3" maxlength="100" title="Maximum 500 Character" value="Pcs"/></td>
					<td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerB_3" id="markerB_3" maxlength="100" title="Maximum 500 Character"/></td>
					<td width="80">
					<input type="button" id="increasemarker_3" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'marker_table')" />
					<input type="button" id="decreasemarker_3" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'marker_table')"/>
					</td>
					</tr>
					</tbody>
					</table>
                    </div>
                    </div>
                    
                    
                    <div class="right" style="float: left;width:882px;background: white;" align="left">
                    <div style="margin-left:10px" id="data_container">
                    <?
					$header_array=array(1=>"B",2=>"C",3=>"D",4=>"E",5=>"F",6=>"G",7=>"H",8=>"I",9=>"J",10=>"K",11=>"L",12=>"M",13=>"N",14=>"O",15=>"P",16=>"Q",17=>"R",18=>"S",19=>"T",20=>"U",21=>"V",22=>"W",23=>"X",24=>"Y",25=>"Z");
					$data=explode("_",$data);
					$num_row=3;
					$num_col=4;
					//$td_with=floor((1100-($num_col*15))/$num_col);
					$td_with=100;
					$table_width=($num_col*$td_with)+200;
					?>
					<strong>
					FABRIC & YARN DETAILS
					</strong>
					<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr('table_1')" />
					<input type="button" id="increaseconversion_2" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr('table_1')"/>
                    Click Last Column Header To Remove Last Column
					<table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center" rules="all" id="table_1">
					<thead>	
					<tr>
					<th style="width:200px;">
					 A
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
					?>
					<th style="width:<? echo $td_with; ?>px;" onClick="delete_column(this)">
					<? echo $header_array[$col];?>
					</th>
					<?
					}
					?>
					</tr>
					</thead>
                    
					<tbody>
					<?
					for($row=1;$row <= $num_row; $row++)
					{
						if($row==1)
						{
							$value="Particulars";
							$class="text_boxes";
						}
						else
						{
							$value="";
							$class="text_boxes_numeric";
						}
					?>
					<tr class="mythingy">
					<td style="width:200px;">
					<input class="text_boxes"  style="width:200px;"  id="A_<? echo $row?>" onClick="id_detection(this.id)" value="<? echo $value;  ?>"/> 
					</td>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}
					?>
					<td style="width:<? echo $td_with; ?>px;">
					<input class="<? echo $class; ?>"  style="width:<? echo $td_with; ?>px;"  id="<? echo $header_array[$col]."_".$row; ?>" onChange="sum_value(this.id,'table_1')" onClick="id_detection(this.id)" <? echo $readonly; ?> /> 
					</td>
					<?
					}
					?>
					</tr>
					<?
					}
					?>
					</tbody>
					<tfoot>	
					<tr>
					<th style="width:200px;">
                    Total Finish Fabric Cons
                   <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "sum1_A"?>" value="Total Finish Fabric Cons" /> 
					 
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
					if($col<=2)
					{
						$readonly="disabled";
					}
					else
					{
					  $readonly="";	
					}
						
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum1_".$header_array[$col];?>" <? echo $readonly; ?> readonly /> 
					</th>
					<?
					}
					?>
					</tr>
                    <tr>
					<th style="width:200px;">
					 Garments Process Loss%
                     <input type="hidden" class="text_boxes_numeric"  style="width:200px;"  id="<? echo "Fper_A"?>" value="Garments Process Loss"/> 
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "Fper_".$header_array[$col];?>" onChange="claculate_percent(1)" <? echo $readonly;  ?> /> 
					</th>
					<?
					}
					?>
					</tr>
					<tr>
					<th style="width:200px;">
					 Finish Fabric Cons
                     <input type="hidden" class="text_boxes"  style="width:200px;" id="<? echo "sum2_A"?>" value="Finish Fabric Cons" />  
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;" id="<? echo "sum2_".$header_array[$col];?>" <? echo $readonly;  ?>  readonly /> 
					</th>
					<?
					}
					?>
					</tr>
                    <tr>
					<th style="width:200px;">
					 Dyeing and Knitting ProcessLoss %
                     <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "Yper_A";?>" value="Dyeing and Knitting ProcessLoss" />
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "Yper_".$header_array[$col];?>" onChange="claculate_percent(2)" <? echo $readonly;  ?>  /> 
					</th>
					<?
					}
					?>
					</tr>
					<tr>
					<th style="width:200px;">
					Grey Fabric and Yarn Cons 
                    <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "sum3_A";?>" value="Grey Fabric and Yarn Cons "/> 
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
					    if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}	
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum3_".$header_array[$col];?>" <? echo $readonly;  ?>  readonly/> 
					</th>
					<?
					}
					?>
					</tr>
                    <tr>
					<th style="width:200px;">
					 Yarn Count 
                     <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "Ycount_A";?>" value="Yarn Count"/> 
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes"  style="width:<? echo $td_with; ?>px;"  id="<? echo "Ycount_".$header_array[$col];?>" <? echo $readonly;  ?> /> 
					</th>
					<?
					}
					?>
					</tr>
					</tfoot>
					</table>
					
					
					<strong>Body Fabric Cost </strong>
					<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr('table_2')" />
					<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr('table_2')"/>
					<table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="table_2">
					<tbody>
					<?
					for($row=1;$row <= $num_row; $row++)
					{
						if($row==1)
						{
							$class="text_boxes_numeric";
						}
						else
						{
							$class="text_boxes_numeric";
						}
					?>
					<tr class="mythingy">
					<td style="width:200px;">
					<input class="text_boxes"  style="width:200px;"  id="AY_<? echo $row?>" onClick="id_detection(this.id)"//> 
					</td>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}	
					?>
					<td style="width:<? echo $td_with; ?>px;">
					<input class="<? echo $class; ?>"  style="width:<? echo $td_with; ?>px;"  id="<? echo $header_array[$col]."Y_".$row; ?>" onChange="sum_value(this.id,'table_2')" onClick="id_detection(this.id)" <? echo $readonly;  ?>/> 
					</td>
					<?
					}
					?>
					</tr>
					<?
					}
					?>
					</tbody>
					<tfoot>	
					<tr>
					<th style="width:200px;">
					 Fabric Price/KG
                     <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "sum4_AY";?>" value="Fabric Price/KG" />
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}	
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum4_".$header_array[$col]."Y";?>" <? echo $readonly;  ?> /> 
					</th>
					<?
					}
					?>
					</tr>
					<tr>
					<th style="width:200px;">
					 Total Fabric Cost/Dzn
                      <input type="hidden" class="text_boxes"  style="width:200px;" id="<? echo "sum5_AY";?>" value="Total Fabric Cost Dzn" /> 
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}	
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;" id="<? echo "sum5_".$header_array[$col]."Y";?>" <? echo $readonly;  ?> /> 
					</th>
					<?
					}
					?>
					</tr>
					<tr>
					<th style="width:200px;">
					 CM Cost/Dzn
                     <input type="hidden" class="text_boxes"  style="width:200px;" id="<? echo "CM_AY";?>"  value="CM Cost Dzn"/>
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}	
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;" id="<? echo "CM_".$header_array[$col]."Y";?>" onChange="total_garments_cost_dzn()" <? echo $readonly;  ?> /> 
					</th>
					<?
					}
					?>
					</tr>
					</tfoot>
					</table>
					<strong>Other Fabric Cost</strong>
					<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr('table_3')" />
					<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr('table_3')"/>
					<table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="table_3">
					<tbody>
					<?
					for($row=1;$row <= $num_row; $row++)
					{
					?>
					<tr class="mythingy">
					<td style="width:200px;">
					<input class="text_boxes"  style="width:200px;"  id="AT_<? echo $row?>" onClick="id_detection(this.id)"//> 
					</td>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}
					?>
					<td style="width:<? echo $td_with; ?>px;">
					<input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo $header_array[$col]."T_".$row; ?>" onChange="sum_value(this.id,'table_3')" onClick="id_detection(this.id)" <? echo $readonly;  ?>/> 
					</td>
					<?
					}
					?>
					</tr>
					<?
					}
					?>
					</tbody>
					<tfoot>	
					<tr>
					<th style="width:200px;">
					 Total 
                     <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "sum6_AT";?>" value="Total" /> 
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum6_".$header_array[$col]."T";?>" <? echo $readonly;  ?> /> 
					</th>
					<?
					}
					?>
					</tr>
					
					
					</tfoot>
					</table>
                    
                    <strong>Ebellishment Cost</strong>
					<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr('table_4')" />
					<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr('table_4')"/>
					<table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="table_4">
					<tbody>
					<?
					for($row=1;$row <= $num_row; $row++)
					{
					?>
					<tr class="mythingy">
					<td style="width:200px;">
					<input class="text_boxes"  style="width:200px;"  id="AE_<? echo $row?>" onClick="id_detection(this.id)"//> 
					</td>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}
					?>
					<td style="width:<? echo $td_with; ?>px;">
					<input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo $header_array[$col]."E_".$row; ?>" onChange="sum_value(this.id,'table_4')" onClick="id_detection(this.id)" <? echo $readonly;  ?>/> 
					</td>
					<?
					}
					?>
					</tr>
					<?
					}
					?>
					</tbody>
					<tfoot>	
					<tr>
					<th style="width:200px;">
					 Total 
                     <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "Esum1_AE";?>"  value="Total"/> 
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "Esum1_".$header_array[$col]."E";?>" <? echo $readonly;  ?> /> 
					</th>
					<?
					}
					?>
					</tr>
					
					
					</tfoot>
					</table>
                    
                    
                    <strong>Trim Cost</strong>
					<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr('table_5')" />
					<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr('table_5')"/>
					<table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="table_5">
					<tbody>
					<?
					for($row=1;$row <= $num_row; $row++)
					{
					?>
					<tr class="mythingy">
					<td style="width:200px;">
					<input class="text_boxes"  style="width:200px;"  id="ATR_<? echo $row?>" onClick="id_detection(this.id)"/> 
					</td>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
					?>
					<td style="width:<? echo $td_with; ?>px;">
					<input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo $header_array[$col]."TR_".$row; ?>" onChange="sum_value(this.id,'table_5')" onClick="id_detection(this.id)"/> 
					</td>
					<?
					}
					?>
					</tr>
					<?
					}
					?>
					</tbody>
					<tfoot>	
					<tr>
					<th style="width:200px;">
					 Total 
                      <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "TRsum1_ATR";?>" value="Total" /> 
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "TRsum1_".$header_array[$col]."TR";?>" /> 
					</th>
					<?
					}
					?>
					</tr>
					</tfoot>
					</table>
                    
                    <strong>Other Cost</strong>
					<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr('table_6')" />
					<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr('table_6')"/>
					<table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="table_6">
					<tbody>
					<?
					for($row=1;$row <= $num_row; $row++)
					{
					?>
					<tr class="mythingy">
					<td style="width:200px;">
					<input class="text_boxes"  style="width:200px;"  id="AOC_<? echo $row?>" onClick="id_detection(this.id)"/> 
					</td>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}
					?>
					<td style="width:<? echo $td_with; ?>px;">
					<input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo $header_array[$col]."OC_".$row; ?>" onChange="sum_value(this.id,'table_6')" onClick="id_detection(this.id)" <? echo $readonly;  ?>/> 
					</td>
					<?
					}
					?>
					</tr>
					<?
					}
					?>
					</tbody>
					<tfoot>	
					<tr>
					<th style="width:200px;">
					   Total 
                       <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "OCsum1_AOC";?>" value="Total" /> 
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "OCsum1_".$header_array[$col]."OC";?>" <? echo $readonly;  ?> /> 
					</th>
					<?
					}
					?>
					</tr>
					</tfoot>
					</table>
                    
                    
                    
                    
                    
                    
                    <strong>Price Summary</strong>
					<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr('table_7')" />
					<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr('table_7')"/>
					<table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:<? echo $table_width+65; ?>px;text-align:center;" rules="all" id="table_7">
					
					<tfoot>	
					<tr>
					<th width="300">
					 Total Garments Cost/Dzn
                     <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "sum7_AT";?>" value="Total Garments Cost Dzn" /> 
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
					    if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}	
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum7_".$header_array[$col]."T";?>" <? echo $readonly;  ?>  /> 
					</th>
					<?
					}
					?>
					</tr>
                    
					<tr>
					<th width="300">
					 Total Garments Cost/Pcs
                     <input type="hidden" class="text_boxes"  style="width:200px;" id="<? echo "sum8_AT";?>"  value="Total Garments Cost Pcs"/> 
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;" id="<? echo "sum8_".$header_array[$col]."T";?>"  <? echo $readonly;  ?>  /> 
					</th>
					<?
					}
					?>
					</tr>
                    <tr>
					<th width="300">
					 Commercial Cost/Dzn
                      <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "Commercialsum_A";?>" value="Commercial Cost Dzn" /> 
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col==1)
						{
							$readonly="disabled";
						}
						else if($col==2)
						{
							$readonly="";
						}
						else
						{
						  $readonly="readonly";	
						}
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "Commercialsum_".$header_array[$col];?>" onChange="claculate_percent(3)" <? echo $readonly;  ?> /> 
					</th>
					<?
					}
					?>
					</tr>
                    <tr>
					<th width="300">
					 Price After Commercial Cost/Pcs
                     <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "Commercialsum1_A";?>" value="Price After Commercial Cost Pcs" /> 
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}	
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "Commercialsum1_".$header_array[$col];?>" <? echo $readonly;  ?>  /> 
					</th>
					<?
					}
					?>
					</tr>
                    
                    <tr>
					<th width="300">
					Price After Profit 
                    <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "profit_A";?>" value="Price After Profit" /> 
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col==1)
						{
							$readonly="disabled";
						}
						else if($col==2)
						{
							$readonly="";
						}
						else
						{
						  $readonly="readonly";	
			            }
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "profit_".$header_array[$col];?>" onChange="claculate_percent(4)" <? echo $readonly;  ?>  /> 
					</th>
					<?
					}
					?>
					</tr>
					<tr>
					<th width="300">
					Buying Commision
                     <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "buyingcommision_A";?>" value="Buying Commision"/> 
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col==1)
						{
							$readonly="disabled";
						}
						else if($col==2)
						{
							$readonly="";
						}
						else
						{
						  $readonly="readonly";	
			            }
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "buyingcommision_".$header_array[$col];?>" onChange="claculate_percent(5)" <? echo $readonly;  ?> /> 
					</th>
					<?
					}
					?>
					</tr>
                    <tr>
					<th width="300">
					Quoted Price 
                    <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "quotedPrice_A";?>" value="Quoted Price"/>
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}	
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "quotedPrice_".$header_array[$col];?>" <? echo $readonly;  ?>/> 
					</th>
					<?
					}
					?>
					</tr>
                    
                    <tr>
					<th width="300">
					Make Combine 
                    <input  type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "makecombine_A";?>" value="Make Combine"/> 
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
					?>
					<th style="width:<? echo $td_with; ?>px;">
                    <?
                    if($col==1)
                    {
						?>
                        <input type="checkbox" id="combine" onClick="make_combine()"/>
                        <?
                    
                    }
					else
					{
					?>
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "makecombine_".$header_array[$col];?>" readonly/> 
                     <?
					}
					 ?>
					</th>
					<?
					}
					?>
					</tr>
                    <tr>
					<th width="300">
					Make Average
                    <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "makeaverage_A";?>" value="Make Average"/>   
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
					?>
					<th style="width:<? echo $td_with; ?>px;">
                    <?
                    if($col==1)
                    {
						?>
                        <input type="checkbox" id="average" onChange="make_average()"/>
                        <?
                    
                    }
					else
					{
					?>
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "makeaverage_".$header_array[$col];?>" readonly/> 
                     <?
					}
					 ?>
					</th>
					<?
					}
					?>
					</tr>
					<tr>
					<th width="300">
					Terget Price
                    <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "sum10_AT";?>" value="Terget Price"/> 
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
						if($col<=2)
						{
							$readonly="disabled";
						}
						else
						{
						  $readonly="";	
						}	
					?>
					<th style="width:<? echo $td_with; ?>px;">
					 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum10_".$header_array[$col]."T";?>" <? echo $readonly;  ?>/> 
					</th>
					<?
					}
					?>
					</tr>
					</tfoot>
					</table>
                    
                    </div>
                    </div>
                    </div>
                    <br/>
    
					</fieldset>
                    
                    
                    
                    
                    
         
<div style="display:none" id="data_panel"></div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
