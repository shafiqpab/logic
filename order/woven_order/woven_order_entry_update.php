<?
/*--------------------------------------------Comments----------------

Converted by             :  Ashraful
Converted Date           :  24-05-2014
Purpose			         : 	This form will create Knit Garments Order Entry
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 	13-10-2012
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
Comments		         :From this version oracle conversion is start
----------------------------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Info","../../", 1, 1, $unicode,1,'');
//load_html_head_contents($title, $path, $filter, $popup, $unicode, $multi_select, $am_chart)
?>	
<script>
<?
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][122]) ;
echo "var field_level_data= ". $data_arr . ";\n";
?>

var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
// Master Form-----------------------------------------------------------------------------
function openmypage(page_link,title)
{
	var garments_nature=document.getElementById('garments_nature').value;
	page_link=page_link+'&garments_nature='+garments_nature;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_job");
		if (theemail.value!="")
		{
			freeze_window(5);
		    reset_form('','','txt_po_no*txt_po_received_date*txt_pub_shipment_date*txt_org_shipment_date*txt_po_quantity*txt_avg_price*txt_amount*txt_excess_cut*txt_plan_cut*cbo_status*txt_details_remark*cbo_delay_for*show_textcbo_delay_for*cbo_packing_po_level*update_id_details*color_size_break_down','','');
			get_php_form_data(theemail.value, "populate_data_from_search_popup", "requires/woven_order_entry_controller_update" );
			show_list_view(theemail.value,'show_po_active_listview','po_list_view','requires/woven_order_entry_controller_update','');
		 	//show_list_view(theemail.value,'show_deleted_po_active_listview','deleted_po_list_view','../woven_order/requires/woven_order_entry_controller_update','');
			
			set_button_status(1, permission, 'fnc_order_entry',1);
			//load_drop_down( 'requires/woven_order_entry_controller_update', theemail.value, 'load_drop_down_projected_po', 'projected_po_td' );
			//load_drop_down( 'requires/woven_order_entry_controller_update_update', theemail.value, 'load_drop_down_projected_po', 'projected_po_td' );
			$("#cbo_company_name").attr("disabled",true);
			$("#cbo_location_name").attr("disabled",true);
			$("#cbo_buyer_name").attr("disabled",true);
			$("#cbo_currercy").attr("disabled",true);
			$("#cbo_product_department").attr("disabled",true);
			$("#cbo_sub_dept").attr("disabled",true);
			$("#cbo_region").attr("disabled",true);
			$("#txt_item_catgory").attr("disabled",true);
			$("#cbo_team_leader").attr("disabled",true);
			$("#cbo_dealing_merchant").attr("disabled",true);
			$("#cbo_ship_mode").attr("disabled",true);
			$("#cbo_order_uom").attr("disabled",true);
			$("#cbo_client").attr("disabled",true);
			release_freezing();
			set_field_level_access( $("#cbo_company_name").val());
			$("#txt_org_shipment_date").attr("disabled",false);
		}
	}
}


function open_qoutation_popup(page_link,title)
{
	var txt_quotation_id= document.getElementById('txt_quotation_id').value;
	if(txt_quotation_id!=''){
		var r=confirm('Quotation Id :'+txt_quotation_id+" Already Attached With This Job.\n If You want to Replace It Press OK \n After replace your SMV Will Remove.  ");
		if(r==false)
		{
			return;	
		}
		else
		{
			
		}
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_id");
		if (theemail.value!="")
		{
			freeze_window(5);
			document.getElementById('tot_smv_qnty').value='';
			get_php_form_data( theemail.value, "populate_data_from_search_popup_quotation", "requires/woven_order_entry_controller_update" );
			load_drop_down('requires/woven_order_entry_controller_update',document.getElementById("cbo_company_name").value, 'load_drop_down_location', 'location' );
			location_select();
			release_freezing();
		}
	}
}

 
function fnc_order_entry( operation )
{
	if(operation==2)
	{
		alert("Delete Restricted")
		return;
	}
	
	var tot_smv_qnty=document.getElementById('tot_smv_qnty').value;
	if(tot_smv_qnty*1<=0){
		alert("Insert SMV")
	}
	//check season validation
	var testoptionlength = $("#cbo_season_name option").length-1;
	//alert(testoptionlength);
	if(testoptionlength>0) {
		if(form_validation('cbo_season_name','Select Season')==false)
		{
			return;
		}
	}
	
	if (form_validation('cbo_company_name*cbo_location_name*cbo_buyer_name*txt_style_ref*cbo_product_department*txt_item_catgory*cbo_dealing_merchant*item_id*tot_smv_qnty','Company Name*Location Name*Buyer Name*Style Ref*Product Department*Item Catagory*Dealing Merchant*Item Details*SMV')==false)
	{
		return;
	}	
	else
	{
		var data="action=save_update_delete_mst&operation="+operation+get_submitted_data_string('txt_job_no*hidd_job_id*garments_nature*cbo_company_name*cbo_location_name*cbo_buyer_name*txt_style_ref*txt_style_description*cbo_product_department*txt_product_code*cbo_sub_dept*cbo_currercy*cbo_agent*cbo_client*txt_repeat_no*cbo_region*txt_item_catgory*cbo_team_leader*cbo_dealing_merchant*cbo_factory_merchant*cbo_packing*txt_remarks*cbo_ship_mode*cbo_order_uom*item_id*set_breck_down*tot_set_qnty*tot_smv_qnty*txt_quotation_id*update_id*cbo_season_name*txt_bhmerchant*set_smv_id',"../../");
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/woven_order_entry_controller_update.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_on_submit_reponse_mst;
	}
}

function fnc_on_submit_reponse_mst()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]=="SMV"){
			alert("Insert SMV in Item Pop-Up");
			release_freezing();
			return;
		}
		if(reponse[0]==50){
			alert("BOM Approved. Update or Delete Restricted.");
			release_freezing();
			return;
		}
		if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1)
		{
			document.getElementById('txt_job_no').value=reponse[1];
			document.getElementById('update_id').value=reponse[1];
			document.getElementById('hidd_job_id').value=reponse[3];
			document.getElementById('set_pcs').value=document.getElementById('cbo_order_uom').value
			document.getElementById('set_unit').value=document.getElementById('cbo_currercy').value
			set_button_status(1, permission, 'fnc_order_entry',1);
		}
		show_msg(trim(reponse[0]));
		release_freezing();
	}
}

function location_select()
{
	if($('#cbo_location_name option').length==2)
	{
		if($('#cbo_location_name option:first').val()==0)
		{
			$('#cbo_location_name').val($('#cbo_location_name option:last').val());
			eval($('#cbo_location_name').attr('onchange')); 
		}
	}
	else if($('#cbo_location_name option').length==1)
	{
		$('#cbo_location_name').val($('#cbo_location_name option:last').val());
		eval($('#cbo_location_name').attr('onchange'));
	}	
}

	function open_set_popup(unit_id,texboxid)
	{ 
		var	pcs_or_set="";
		var txt_job_no=document.getElementById('txt_job_no').value;	
		var set_smv_id=document.getElementById('set_smv_id').value;
		var txt_style_ref=document.getElementById('txt_style_ref').value;
		var set_breck_down=document.getElementById('set_breck_down').value;
		var tot_set_qnty=document.getElementById('tot_set_qnty').value;
		var tot_smv_qnty=document.getElementById('tot_smv_qnty').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		if(form_validation('cbo_buyer_name*txt_style_ref','Buyer*Style')==false)
		{
			return;
		}
		
		var precost = return_ajax_request_value(txt_job_no, 'check_precost', 'requires/woven_order_entry_controller');
		var data=precost.split("_");
		if(data[2]>0){
			alert("Pre Cost Approved, Any Change not allowed.");
			document.getElementById('cbo_order_uom').value=data[1];
			//return;
		}
		else if(data[0]>0 && texboxid=='cbo_order_uom'){
			alert("Pre Cost Found, UOM Change not allowed");
			document.getElementById('cbo_order_uom').value=data[1];
			return;
		}
		else if (data[0]>0 && texboxid=='set_button'){
			alert("Pre Cost Found, only Sew. and Cut. SMV Change allowed");
			//document.getElementById('cbo_order_uom').value=data[1];
		}
	
		if(unit_id==58) pcs_or_set="Item Details For Set";
		if(unit_id==57) pcs_or_set="Item Details For Pack";
		else pcs_or_set="Item Details For Pcs";
		
		var page_link="requires/woven_order_entry_controller_update.php?txt_job_no="+trim(txt_job_no)+"&action=open_set_list_view&set_breck_down="+set_breck_down+"&tot_set_qnty="+tot_set_qnty+'&unit_id='+unit_id+'&tot_smv_qnty='+tot_smv_qnty+'&precostfound='+data[0]+'&precostapproved='+data[2]+'&set_smv_id='+set_smv_id+'&txt_style_ref='+txt_style_ref+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, pcs_or_set, 'width=1150px,height=300px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var set_breck_down=this.contentDoc.getElementById("set_breck_down");
			var item_id=this.contentDoc.getElementById("item_id");
			var tot_set_qnty=this.contentDoc.getElementById("tot_set_qnty");
			var tot_smv_qnty=this.contentDoc.getElementById("tot_smv_qnty");
			document.getElementById('set_breck_down').value=set_breck_down.value;
			document.getElementById('item_id').value=item_id.value;
			document.getElementById('tot_set_qnty').value=tot_set_qnty.value;
			document.getElementById('tot_smv_qnty').value=tot_smv_qnty.value;
		}		
	}

// Master Form End -----------------------------------------------------------------------------

//Dtls Form-------------------------------------------------------------------------------------
function open_color_size_popup(page_link,title)
{
	if(document.getElementById('txt_po_quantity').value=="" || document.getElementById('txt_po_quantity').value==0)
	{
		alert('Please enter valid order quantity');
		$('#txt_po_quantity').focus();
		return false;
	}
	if(document.getElementById('update_id_details').value=="" || document.getElementById('update_id_details').value==0 )
	{
	   alert('Please Save The Po first.');
		return false;	
	}
	else
	{
		var update_id_details=document.getElementById('update_id_details').value;
		var txt_po_no=document.getElementById('txt_po_no').value;
		
		var txt_po_quantity=document.getElementById('txt_po_quantity').value;
		var set_breck_down=document.getElementById('set_breck_down').value;
		var item_id=document.getElementById('item_id').value;
		var tot_set_qnty=document.getElementById('tot_set_qnty').value;
		var cbo_order_uom=document.getElementById('cbo_order_uom').value;
		var color_size_break_down=document.getElementById('color_size_break_down').value;
		var txt_avg_price =document.getElementById('txt_avg_price').value;
		var txt_excess_cut=document.getElementById('txt_excess_cut').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var txt_org_shipment_date=document.getElementById('txt_org_shipment_date').value;

		
		var page_link=page_link+'&data='+update_id_details+'&txt_po_quantity='+txt_po_quantity+'&set_breck_down='+set_breck_down+'&item_id='+item_id+'&tot_set_qnty='+tot_set_qnty+'&cbo_order_uom='+cbo_order_uom+'&color_size_break_down='+color_size_break_down+'&txt_po_no='+txt_po_no+'&txt_avg_price='+txt_avg_price+'&txt_excess_cut='+txt_excess_cut+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&txt_org_shipment_date='+txt_org_shipment_date;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=550px,center=1,resize=1,scrolling=0','../../')
		
		emailwindow.onclose=function()
		{
			var tot_set_qnty=(document.getElementById('tot_set_qnty').value)*1;
		    var cbo_order_uom=document.getElementById('cbo_order_uom').value;
			var txt_avg_rate=this.contentDoc.getElementById("txt_tot_avg_rate");
			var txt_total_amt=this.contentDoc.getElementById("txt_tot_amount");
			var txt_avg_excess_cut=this.contentDoc.getElementById("txt_tot_excess_cut");
			var txt_total_plan_cut=this.contentDoc.getElementById("txt_tot_plancut");
			var txt_avg_price="";
			var txt_plan_cut=""
			if(cbo_order_uom==58)
			{
				txt_avg_price=txt_avg_rate.value*tot_set_qnty;
				alert(txt_avg_rate.value+'='+tot_set_qnty);
				document.getElementById('txt_avg_price').value=txt_avg_rate.value*tot_set_qnty;
				txt_plan_cut=number_format_common((txt_total_plan_cut.value/tot_set_qnty),6,0,0);
				document.getElementById('txt_plan_cut').value=number_format_common((txt_total_plan_cut.value/tot_set_qnty),6,0,0);
			}
			else
			{
				txt_avg_price=txt_avg_rate.value;
				document.getElementById('txt_avg_price').value=txt_avg_rate.value;
				txt_plan_cut=number_format_common(txt_total_plan_cut.value,6,0,0);;
			    document.getElementById('txt_plan_cut').value=txt_total_plan_cut.value;
			}
		    var txt_amount=txt_total_amt.value;
		    document.getElementById('txt_amount').value=txt_total_amt.value;
			var txt_excess_cut=txt_avg_excess_cut.value;
		    document.getElementById('txt_excess_cut').value=txt_avg_excess_cut.value;
			fnc_order_entry_details( 1 );
		}
	}
}


function set_excess_cut( val, excs )
{
	if (excs=="")
	{
		if ( val!="" || val!=0 )
		{
			var excs_cut=return_ajax_request_value(val+"_"+document.getElementById('cbo_company_name').value, "get_excess_cut_percent", "requires/woven_order_entry_controller_update") ;
			document.getElementById('txt_excess_cut').value=excs_cut;
			var txt_plan_cut=(val*1)+((excs_cut*val)/100);
			document.getElementById('txt_plan_cut').value=number_format_common(txt_plan_cut, 6, 0);
		}
	}
	else
	{
		var txt_plan_cut=(val*1)+((excs*val)/100);
		document.getElementById('txt_plan_cut').value=number_format_common(txt_plan_cut, 6, 0);
	}
	var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
	math_operation( 'txt_amount', 'txt_avg_price*txt_po_quantity', '*','', ddd );
}

function publish_shipment_date(company_id)
{
	var publish_shipment_date=return_global_ajax_value(company_id, 'publish_shipment_date', '', 'requires/woven_order_entry_controller_update');
	if(publish_shipment_date==1)
	{
		$('#txt_pub_shipment_date').attr('disabled',false);
	}
	else
	{
		$('#txt_pub_shipment_date').attr('disabled',true);
	}
}

function set_pub_ship_date()
{
	var company_id=$('#cbo_company_name').val()
	var publish_shipment_date=return_global_ajax_value(company_id, 'publish_shipment_date', '', 'requires/woven_order_entry_controller_update');
	if(publish_shipment_date==1)
	{
		$('#txt_pub_shipment_date').attr('disabled',false);
	}
	else
	{
		var txt_org_shipment_date=$('#txt_org_shipment_date').val()
		$('#txt_pub_shipment_date').val(txt_org_shipment_date);
	}
	
}

function fnc_order_entry_details( operation )
{
	if(operation==2)
	{
		alert("Delete Restricted")
		return;
	}
	if (form_validation('update_id*txt_po_no*txt_po_received_date*txt_po_quantity*txt_avg_price*txt_org_shipment_date','Master Info*PO Number*PO Received Date*PO Quantity*Avg. Price*Org. Shipment Date')==false)
	{
		return;   
	}
	var txt_avg_price=document.getElementById('txt_avg_price').value;
	if(operation==1) //Check Only Update event
	{
		var chk_extended_ship_date=document.getElementById('chk_extended_ship_date').value;
		var org_shipment_date=document.getElementById('txt_org_shipment_date').value;
		var factory_rec_date=document.getElementById('txt_factory_rec_date').value;
		var pub_shipment_date=document.getElementById('txt_pub_shipment_date').value;
		var po_received_date=document.getElementById('txt_po_received_date').value;
		
		if(chk_extended_ship_date!="" && (org_shipment_date>chk_extended_ship_date  || pub_shipment_date>chk_extended_ship_date ) )
		{
			alert('Ship date not allowed greater than extended ship date');
			return; 
			
		}
	}
	
	if(txt_avg_price==0)
	{
		alert("Avg. Price 0 not accepted")
	}
	else
	{
		
		var data="action=save_update_delete_dtls&operation="+operation+get_submitted_data_string('cbo_company_name*hidd_job_id*cbo_order_status*txt_po_no*txt_po_received_date*txt_pub_shipment_date*txt_org_shipment_date*txt_factory_rec_date*txt_po_quantity*txt_avg_price*txt_amount*txt_excess_cut*txt_plan_cut*cbo_status*txt_details_remark*update_id_details*update_id*cbo_packing*cbo_delay_for*cbo_packing_po_level*color_size_break_down*txt_grouping*cbo_projected_po*cbo_tna_task*txt_sc_lc*txt_file_no*cbo_buyer_name',"../../");
		freeze_window(operation);
		http.open("POST","requires/woven_order_entry_controller_update.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_on_submit_reponse;
	}
}
	 
function fnc_on_submit_reponse()
{
	if(http.readyState == 4) 
	{
		 //alert(http.responseText);
		 var reponse=http.responseText.split('**');
		 if(trim(reponse[0]) ==12)
		 {
			alert("Org. Shipment Date Not Allowed");
			release_freezing();	
			return; 
		 }
		 if(reponse[0]==50){
			alert("BOM Approved. Update or Delete Restricted.");
			release_freezing();
			return;
		}
	 	 if(trim(reponse[0])=='LeadTime')
		 {
			 alert("Bellow "+ trim(reponse[2]) +" Days Lead Time not allow. If required, please take approval of Marketing Director.");
			 release_freezing();
			 return;
		 }
		 
		if(reponse[0]==16)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
			 
		 show_msg(trim(reponse[0]));
		 show_list_view(document.getElementById('txt_job_no').value,'show_po_active_listview','po_list_view','../woven_order/requires/woven_order_entry_controller_update','');
		 show_list_view(document.getElementById('txt_job_no').value,'show_deleted_po_active_listview','deleted_po_list_view','../woven_order/requires/woven_order_entry_controller_update','');
		 if(trim(reponse[0]) !=11)
		 {
		reset_form('','','txt_po_no*txt_po_received_date*txt_pub_shipment_date*txt_org_shipment_date*txt_factory_rec_date*txt_po_quantity*txt_avg_price*txt_amount*txt_excess_cut*txt_plan_cut*cbo_status*txt_details_remark*cbo_delay_for*show_textcbo_delay_for*cbo_packing_po_level*update_id_details*color_size_break_down*txt_grouping*cbo_projected_po*cbo_tna_task','','');
		 $('#txt_avg_price').removeAttr('disabled');
		 $('#txt_avg_price').removeAttr('title');
		 document.getElementById('txt_total_job_quantity').value=trim(reponse[2])
		 document.getElementById('txt_avg_unit_price').value=trim(reponse[3])
		 document.getElementById('txt_job_total_price').value=trim(reponse[4])
		 document.getElementById('set_pcs').value=trim(reponse[5])
		 document.getElementById('set_unit').value=trim(reponse[6])
		 }
		 set_button_status(0, permission, 'fnc_order_entry_details',2);
		 load_drop_down( 'requires/woven_order_entry_controller_update', document.getElementById('txt_job_no').value, 'load_drop_down_projected_po', 'projected_po_td' )
		 release_freezing();
	}
}
function get_details_form_data(id,type,path)
{
	reset_form('','','cbo_order_status*txt_po_no*txt_po_received_date*txt_pub_shipment_date*txt_org_shipment_date*txt_po_quantity*txt_avg_price*txt_amount*txt_excess_cut*txt_plan_cut*cbo_status*txt_details_remark*cbo_delay_for*show_textcbo_delay_for*cbo_packing_po_level*update_id_details*color_size_break_down*txt_grouping*cbo_projected_po*cbo_tna_task','','');
	get_php_form_data( id, type, path );
}

function set_tna_task()
{
	var txt_po_received_date=document.getElementById('txt_po_received_date').value
	var txt_pub_shipment_date=document.getElementById('txt_pub_shipment_date').value
	var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	
	var txt_org_shipment_date=document.getElementById('txt_org_shipment_date').value;
	var txt_factory_rec_date=document.getElementById('txt_factory_rec_date').value;
	
	var datediff = date_compare(txt_po_received_date,txt_pub_shipment_date);//date_diff('d', txt_po_received_date, txt_pub_shipment_date);
	//alert(datediff);
	if(datediff==false)
	{
		alert("PO Received Date Is Greater Than Pub. Shipment Date.");
		$('#txt_pub_shipment_date').val("");
		return;
	}
	
	var recdatediff = date_compare(txt_po_received_date,txt_factory_rec_date);//date_diff('d', txt_po_received_date, txt_pub_shipment_date);
	//alert(datediff);
	if(recdatediff==false)
	{
		alert("PO Received Date Is Greater Than Fac. Receive Date.");
		$('#txt_factory_rec_date').val("");
		return;
	}
	
	var shipdatediff = date_compare(txt_pub_shipment_date,txt_org_shipment_date);
	if(shipdatediff==false)
	{
		alert("Pub. Shipment Date. Is Greater Than Org. Shipment Date.");
		$('#txt_org_shipment_date').val("");
		return;
	}
	load_drop_down( 'requires/woven_order_entry_controller_update', txt_po_received_date+"_"+txt_pub_shipment_date+"_"+cbo_buyer_name, 'load_drop_down_tna_task', 'tna_task_td' )
}

function sub_dept_load(cbo_buyer_name,cbo_product_department)
{
	if(cbo_buyer_name ==0 || cbo_product_department==0 )
	{
		return
	}
	else
	{
		load_drop_down( 'requires/woven_order_entry_controller_update',cbo_buyer_name+'_'+cbo_product_department, 'load_drop_down_sub_dep', 'sub_td' )
	}
}

function pop_entry_actual_po()
{
	var po_id = $('#update_id_details').val();
	var txt_job_no = $('#txt_job_no').val();
	if(po_id=="")
	{
		alert("Save The PO First");
		return;
	}
	var page_link='requires/woven_order_entry_controller_update.php?action=actual_po_info_popup&po_id='+po_id+'&txt_job_no='+txt_job_no;
	var title='Actual Po Entry Info';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=300px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		//var theform=this.contentDoc.forms[0]
		//var actual_infos=this.contentDoc.getElementById("actual_po_infos").value;  
		//$('#actual_po_infos_'+row_id).val(actual_infos);            
	}
}
//Dtls Form End -------------------------------------------------------------------------------------
//--------------------------------------------------------For PP meeting popup-------------------------------------------------------------

function pp_meeting_po(type)
{
	var update_id = $('#update_id').val();
	
	if(update_id=="")
	{
		alert("Select Job No First");
		return;
	}
	var page_link='requires/woven_order_entry_controller_update.php?action=all_po_ppMeeting&update_id='+update_id+"&type="+type;
	var popup_width='';
	if(type==1) var title='PP Meeting Info'; else if(type==2)  var title='Extended Ship Date Info';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=450px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		//var theform=this.contentDoc.forms[0]
		//var actual_infos=this.contentDoc.getElementById("actual_po_infos").value;  
		//$('#actual_po_infos_'+row_id).val(actual_infos);            
	}
}
function file_handover_po(type)
{
	var update_id = $('#update_id').val();
	
	if(update_id=="")
	{
		alert("Select Job No First");
		return;
	}
	var page_link='requires/woven_order_entry_controller_update.php?action=all_po_file_handover&update_id='+update_id+"&type="+type;
	var popup_width='';
	 var title='File Handover Info'; 
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=450px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		//var theform=this.contentDoc.forms[0]
		//var actual_infos=this.contentDoc.getElementById("actual_po_infos").value;  
		//$('#actual_po_infos_'+row_id).val(actual_infos);            
	}
}
function booking_meeting_date()
{
	var update_id = $('#update_id').val();
	
	if(update_id=="")
	{
		alert("Select Job No First");
		return;
	}
	var page_link='requires/woven_order_entry_controller_update.php?action=bookingMeetingDate&update_id='+update_id;
	var title='Booking Meeting Date';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=450px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		//var theform=this.contentDoc.forms[0]
		//var actual_infos=this.contentDoc.getElementById("actual_po_infos").value;  
		//$('#actual_po_infos_'+row_id).val(actual_infos);            
	}
}

function fnc_rec_date(val)
{
	$("#txt_po_received_date").val( '<? echo date("d-m-Y"); ?>' );
}



</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
<!-- Important Field outside Form -->  
     <? echo load_freeze_divs ("../../",$permission);  ?>
     <table width="90%" cellpadding="0" cellspacing="2" align="center" >
     	<tr>
        	<td width="70%" align="center" valign="top">  <!--   Form Left Container -->
            	<fieldset style="width:950px;">
                <legend>Garments Order Entry </legend>
                <form name="orderentry_1" id="orderentry_1" autocomplete="off">
                    <table width="900" cellspacing="2" cellpadding="0" border="0">
                        <tr>
                            <td width="100">Job No</td>              <!-- 11-00030  -->
                            <td width="150"><input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/woven_order_entry_controller_update.php?action=order_popup','Job/Order Selection Form')" class="text_boxes" placeholder="New Job No" name="txt_job_no" id="txt_job_no" readonly />
                            <input type="hidden" name="hidd_job_id" id="hidd_job_id" style="width:30px;" class="text_boxes" />
                            </td>
                            <td width="100" class="must_entry_caption">Company Name</td>
                            <td width="150">
								<? echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/woven_order_entry_controller_update', this.value, 'load_drop_down_location', 'location' ); load_drop_down( 'requires/woven_order_entry_controller_update', this.value, 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/woven_order_entry_controller_update', this.value, 'load_drop_down_agent', 'agent_td' );publish_shipment_date( this.value ); load_drop_down( 'requires/woven_order_entry_controller_update', this.value, 'load_drop_down_party_type', 'party_type_td' );publish_shipment_date( this.value ) " );
                                ?>
                                <input type="hidden" name="set_smv_id" id="set_smv_id" style="width:30px;" /> 
                            </td>
                            <td width="100" class="must_entry_caption">Location Name</td>
                            <td id="location"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Buyer Name</td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "" ); ?></td>
                            <td class="must_entry_caption">Style Ref.</td><td>
                            	<input class="text_boxes" type="text" style="width:140px" placeholder="Double Click for Quotation" name="txt_style_ref" id="txt_style_ref" onDblClick="open_qoutation_popup('requires/woven_order_entry_controller_update.php?action=quotation_id_popup','Quotation ID Selection Form')" readonly/>	
                            </td>
                            <td>Style Description</td>
                            <td><input class="text_boxes" type="text" style="width:140px;" name="txt_style_description" id="txt_style_description"/></td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Pord. Dept.</td>   
                            <td><? echo create_drop_down( "cbo_product_department", 82, $product_dept, "", 1, "-Select-", $selected, "sub_dept_load(document.getElementById('cbo_buyer_name').value,this.value)", "", "" ); ?>
                            	<input class="text_boxes" type="text" style="width:40px;" name="txt_product_code" id="txt_product_code" maxlength="10" title="Maximum 10 Character" readonly />
                            </td>
                            <td>Sub. Dept.</td>
                            <td id="sub_td"><? echo create_drop_down( "cbo_sub_dept", 150, $blank_array,"", 1, "-- Select Sub Dep --", $selected, "" ); ?></td>
                            <td>Currency</td>
                            <td><? echo create_drop_down( "cbo_currercy", 150, $currency,'', 0, "",2, "" ); ?></td>
                        </tr>
                        <tr>
                            <td>Repeat No</td>
                            <td><input style="width:140px;" class="text_boxes" name="txt_repeat_no" id="txt_repeat_no" readonly /></td>
                            <td>Region</td>
                            <td><? echo create_drop_down( "cbo_region", 150, $region, 1, "-- Select Region --", $selected, "" ); ?></td>
                            <td class="must_entry_caption">Product Category</td>
                            <td><? echo create_drop_down( "txt_item_catgory", 150, $product_category,"", 1, "-- Select Product Category --", 1, "","","" ); ?></td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Team Leader</td>   
                            <td><? echo create_drop_down( "cbo_team_leader", 150, "select id,team_leader_name from lib_marketing_team where project_type=1 and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'requires/woven_order_entry_controller_update', this.value, 'cbo_dealing_merchant', 'div_marchant' );load_drop_down( 'requires/woven_order_entry_controller_update', this.value, 'cbo_factory_merchant', 'div_marchant_factory' ) " ); ?>		
                            </td>
                            <td class="must_entry_caption">Dealing Merchant</td>   
                            <td id="div_marchant"><? echo create_drop_down( "cbo_dealing_merchant", 150, $blank_array,"", 1, "-- Select Team Member --", $selected, "" ); ?></td>
                            <td class="must_entry_caption">Factory Merchant</td>   
                            <td id="div_marchant_factory"><? echo create_drop_down( "cbo_factory_merchant", 150, $blank_array,"", 1, "-- Select Team Member --", $selected, "" ); ?></td>
                        </tr>
                        <tr>
                            <td>BH Merchant</td>
                            <td><input class="text_boxes" type="text" style="width:140px;"  name="txt_bhmerchant" id="txt_bhmerchant"/></td>
                            <td>Remarks</td>
                            <td><input class="text_boxes" type="text" style="width:140px;"  name="txt_remarks" id="txt_remarks"/></td>
                            <td>Ship Mode</td>
                            <td><? echo create_drop_down( "cbo_ship_mode", 150,$shipment_mode, 1, "", $selected, "" ); ?></td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Order Uom</td>
                            <td><? echo create_drop_down( "cbo_order_uom",50, $unit_of_measurement, "",0, "", 1, "","","1,58" );?>
                            	<input type="button" id="set_button" class="image_uploader" style="width:85px;" value="Item Details" onClick="open_set_popup(document.getElementById('cbo_order_uom').value,this.id)" /> 
                            </td>
                            <td class="must_entry_caption">SMV</td>
                            <td><input class="text_boxes_numeric" type="text" style="width:140px;" name="tot_smv_qnty" id="tot_smv_qnty" readonly/></td>
                            <td>Packing </td>
                            <td><? echo create_drop_down( "cbo_packing", 150, $packing,"", 1, "--Select--", $selected, "","","" ); ?></td>
                        </tr>
                        <tr>
                            <td>Agent </td>
                            <td id="agent_td"><? echo create_drop_down( "cbo_agent", 150, $blank_array,"", 1, "-- Select Agent --", $selected, "" ); ?></td>
                            <td>Client</td>
                            <td id="party_type_td"><? echo create_drop_down( "cbo_client", 150, $blank_array,"", 1, "-- Select Client --", $selected, "" ); ?></td>
                            <td>Season</td>
                            <td id="season_td"><? echo create_drop_down( "cbo_season_name", 150, $blank_array,"", 1, "-- Select Season --", $selected, "" ); ?></td>
                        </tr>
                        <tr>
                            <td>Images</td>
                            <td><input type="button" class="image_uploader" style="width:150px" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'knit_order_entry', 0 ,1)">
                            </td>
                            <td>File</td>
                            <td><input type="button" class="image_uploader" style="width:150px" value="CLICK TO ADD FILE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'knit_order_entry', 2 ,1)"></td>
                            <td>&nbsp;</td>
                            <td><input type="button" value="Booking Meeting Date" class="image_uploader" style="width:150px" onClick="booking_meeting_date()"/></td>
                        </tr>
                        <tr>
                            <td align="center" colspan="6" height="15" valign="middle">
                                <input type="hidden" id="update_id"> <input type="hidden" id="txt_quotation_id"><input type="hidden" id="set_breck_down" />     
                                <input type="hidden" id="item_id" /><input type="hidden" id="tot_set_qnty" />  
                            </td>
                        </tr>
                        <tr>
                            <td align="center" colspan="6" valign="middle" class="button_container">
                            <? echo load_submit_buttons( $permission, "fnc_order_entry", 0,0,"reset_form('orderentry_1*orderdetailsentry_2','deleted_po_list_view*po_list_view','')",1);?>
                            </td>
                        </tr>
                    </table>
                 </form>
              </fieldset>
           </td>
         </tr>
         <tr>
         	<td align="center">
            <fieldset style="width:1050px;">
                <legend>PO Details Entry</legend>
            	<form id="orderdetailsentry_2" autocomplete="off">
                    <table style="border:none" cellpadding="0" cellspacing="2" border="0" rules="all">
                    <thead class="form_table_header">
                        <tr align="center" >
                             <th  width="100" height="27">Order Status </th>
                             <th  width="100" height="27">PO No</th>
                             <th  width="90" >PO Received Date  </th>
                             <th  width="90">Pub. Shipment Date</th>
                             <th  width="90">Org. Shipment Date</th>
                              <th  width="90">Fac. Receive Date</th>
                             <th  width="70">PO Quantity  </th>
                             <th  width="60">Avg. Price</th>
                             <th  width="85">Amount</th>
                             <th  width="60">Excess Cut %</th>
                              <th  width="70">Plan Cut</th>
                              <!-- <th  width="85">Country</th>-->
                             <th  width="85">Status</th>
                           </tr> 
                           </thead>
                        <tr>
                           
                                <td height="22" >
                                    <? echo create_drop_down( "cbo_order_status", 100, $order_status, 0, "", $selected,"", "fnc_rec_date( this.value );" ); ?> 
                                </td>
                              <td height="22" ><input class="text_boxes" name="txt_po_no" id="txt_po_no" type="text" value=""  style="width:100px"/></td>
                              <td ><input name="txt_po_received_date" id="txt_po_received_date" class="datepicker" type="text" onChange="set_tna_task()" value="" style="width:80px;"readonly/>
                              </td>
                              <td ><input name="txt_pub_shipment_date" id="txt_pub_shipment_date" class="datepicker" type="text" onChange="set_tna_task()" value="" style="width:100px;" readonly/>
                              </td>
                              <td  ><input  name="txt_org_shipment_date" id="txt_org_shipment_date" class="datepicker" type="text" value=""  style="width:80px;" onChange="set_pub_ship_date()" readonly/>
                              </td>
                                <td><input  name="txt_factory_rec_date" id="txt_factory_rec_date" class="datepicker" type="text" value=""  style="width:80px;"  readonly/>
                              </td>
                              
                              <td >
                              	<input name="txt_po_quantity" id="txt_po_quantity" onDblClick="open_color_size_popup('requires/size_color_breakdown_controller_update.php?action=populate_size_color_breakdown_pop_up','Color Size Entry Form')" class="text_boxes_numeric" type="text"  style="width:70px" onBlur="set_excess_cut(this.value,document.getElementById('txt_excess_cut').value)" placeholder="Dbl. Click" readonly /></td>
                              <td >
                              	<input name="txt_avg_price" id="txt_avg_price" onBlur="math_operation( 'txt_amount', 'txt_avg_price*txt_po_quantity', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} )"  class="text_boxes_numeric" type="text" value=""  style="width:55px "  /></td>
                              <td>
                              	<input name="txt_amount" id="txt_amount" class="text_boxes_numeric" type="text" value=""  style="width:85px " readonly disabled/>
                              </td>
                              <td >
                              	<input name="txt_excess_cut" id="txt_excess_cut" onBlur="set_excess_cut( document.getElementById('txt_po_quantity').value, this.value )" class="text_boxes_numeric" type="text" style="width:55px " disabled/>
                               </td>
                               <td><input name="txt_plan_cut" id="txt_plan_cut"  class="text_boxes_numeric" type="text" value=""  style="width:65px " disabled/></td>
                               <!--<td> 
                                    
                                        <?php
                                             // echo create_drop_down( "cbo_po_country", 90,"select id,country_name from lib_country where status_active=1 and is_deleted=0 order by country_name", "id,country_name", 1, "--- Select ", "" ); ?>
                                          
                                         
                                   </td>-->
                                   
                             
                              
                              <td >
							  <?php
                              echo create_drop_down( "cbo_status", 85, $row_status, 0, "", 1, "","",1 ); 
							  ?>
                              </td>
                                   
                            	<td ></td>
                            </tr>
                            <tr>
                            	<td align="right"><strong>Projected Po</strong></td>
                                <td height="20" id="projected_po_td">
                                <?php
                                   echo create_drop_down( "cbo_projected_po", 110,$blank_array, "", 1, "--Select--", "","",1 ); 
								?>
                                </td>
                                <td align="right"><strong>TNA From /Upto</strong></td>
                                <td height="20" id="tna_task_td">
                                <?php
                                   echo create_drop_down( "cbo_tna_task", 110,$blank_array, "", 1, "--Select--", "","",1 ); 
								?>
                                </td>
                                <td align="right"><strong>Grouping</strong></td>
                                <td  height="20">
                                <input type="text" id="txt_grouping" class="text_boxes" style="width:70px" readonly>
                                </td>
                                <td align="right"><strong>Delay For</strong></td>
                                <td colspan="4" height="20">
                                <?php
                                echo create_drop_down( "cbo_delay_for", 315, $delay_for, 0, "", 1, "","",1,"" ); 
							    ?>
                                </td>
                                	
                        	</tr>
                             <tr>
                             <td align="right"><strong>Packing</strong> </td>
                                <td  >
                                <?	
									echo create_drop_down( "cbo_packing_po_level", 100, $packing,"", 1, "--Select--", "", "",1,"" ); ?>
                                </td>
                            	<td align="right"><strong>Remarks</strong></td>
                                <td colspan="3" height="20">
                                <input type="text" id="txt_details_remark" class="text_boxes" style="width:283px">
                                </td>
                                <td align="right"><input type="button" value="Actual Po No" class="image_uploader" style="width:80px" onClick="pop_entry_actual_po()"/></td>
                                <td><input type="button" value="PP Meeting" class="image_uploader" style="width:65px" onClick="pp_meeting_po(1)"/></td>
                                <td><input type="button" value="Extended Ship Date" id="txt_extended_ship_date" class="image_uploader" style="width:100px" onClick="pp_meeting_po(2)"/> 
                                <input type="hidden" id="chk_extended_ship_date"  /> </td> 	
								<td><input type="button" value="File Handover" class="image_uploader" style="width:75px" onClick="file_handover_po(1)"/></td> 	
                        	</tr>
                                <tr>
                                    <td align="right"><strong>File No.</strong></td>
                                    <td><input type="text" value="" class="text_boxes" id="txt_file_no" name="txt_file_no" style="width:100px"></td>
                                    <td align="right"><strong>LC/SC</strong></td>
                                    <td colspan="4"><input type="text" value="" class="text_boxes" id="txt_sc_lc" name="txt_sc_lc" style="width:283px"></td>
                                </tr>
                            <tr>
                                <td colspan="12" height="20">
                                <input type="hidden" id="update_id_details">
                                <input type="hidden" id="color_size_break_down" value="" />  
                                </td>	
                        	</tr>
                            <tr>
                                <td colspan="12" height="50" valign="middle" align="center" class="button_container">
									<?
									$dd="disable_enable_fields( 'txt_avg_price', 0 )";
									echo load_submit_buttons( $permission, "fnc_order_entry_details", 0,0 ,"reset_form('orderdetailsentry_2','','','',$dd)",2) ; 
									?>
                                </td>
                            </tr>
                             
                            <tr align="center">
                                <td colspan="12" id="po_list_view">
                                </td>	
                        	</tr>
                            <tr align="center">
                                <td colspan="12" id="deleted_po_list_view">
                                </td>	
                        	</tr>
                            <tr align="center">
                                <td colspan="12" id="">
                                    <table>
                                      <tr bgcolor="">
                                    <td  width="128" colspan="2">&nbsp;Projected Job Quantity</td>
                                    <td width="180"><input  value="" name="txt_projected_job_quantity" id="txt_projected_job_quantity" style="width:103px " class="text_boxes_numeric" readonly>
                                   <!-- <input type="text" class="text_boxes" style="width:40px;" readonly name="set_pcs" id="set_pcs" />-->
                                    <? 
                                        echo create_drop_down( "pojected_set_pcs",60, $unit_of_measurement, "",1, "--", "", "",1,"1,58" );
										?>
                                    </td> 
                                    
                                    <td>&nbsp;&nbsp;Projected Avg Unit Price</td>
                                    <td  colspan="2">
                                    <div><input name="txt_projected_price" type="text" class="text_boxes_numeric" id="txt_projected_price" style="width:85px; text-align:right " value="<? //echo $txt_unit_price; ?>" readonly>&nbsp;&nbsp;
                                   <!-- <input type="text" class="text_boxes" style="width:40px;" readonly name="set_unit" id="set_unit" value="USD"/>-->
                                    <?
                                     echo create_drop_down( "projected_set_unit", 60, $currency,"", 1, "--", "", "" ,1,"");
									 ?>
                                    </div>
                                    </td>
                                    <td>&nbsp;Projected Total Price </td>
                                    <td><input  type="text" style="width:173px "  class='text_boxes_numeric' name="txt_project_total_price" id="txt_project_total_price" value="<? echo $txt_total_price; ?>" readonly> 	</td>
                                    
                                    </tr>
                                    <tr bgcolor="">
                                    <td  width="128" colspan="2">&nbsp;Job Quantity</td>
                                    <td width="180"><input  value="" name="txt_total_job_quantity" id="txt_total_job_quantity" style="width:103px " class="text_boxes_numeric" readonly>
                                   <!-- <input type="text" class="text_boxes" style="width:40px;" readonly name="set_pcs" id="set_pcs" />-->
                                    <? 
                                        echo create_drop_down( "set_pcs",60, $unit_of_measurement, "",1, "--", "", "",1,"1,58" );
										?>
                                    </td> 
                                    
                                    <td>&nbsp;&nbsp;Avg Unit Price</td>
                                    <td  colspan="2">
                                    <div><input name="txt_avg_unit_price" type="text" class="text_boxes_numeric" id="txt_avg_unit_price" style="width:85px; text-align:right " value="<? echo $txt_unit_price; ?>" readonly>&nbsp;&nbsp;
                                   <!-- <input type="text" class="text_boxes" style="width:40px;" readonly name="set_unit" id="set_unit" value="USD"/>-->
                                    <?
                                     echo create_drop_down( "set_unit", 60, $currency,"", 1, "--", "", "" ,1,"");
									 ?>
                                    </div>
                                    </td>
                                    <td>&nbsp;Total Price </td>
                                    <td><input  type="text" style="width:173px "  class='text_boxes_numeric' name="txt_job_total_price" id="txt_job_total_price" value="<? echo $txt_total_price; ?>" readonly> 	</td>
                                    
                                    </tr>
                                    </table>
                                </td>	
                        	</tr>
                            
                       </table>
            	</form>
                </fieldset>
            </td>
         
         </tr>
	</table>
	</div>
</body>
   <script>
	set_multiselect('cbo_delay_for','0','0','','');
</script>        
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>