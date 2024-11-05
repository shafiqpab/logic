<?
/*-------------------------------------------- Comments 
Version          : V1
Purpose			 : This form will create Service Booking
Functionality	 :	
JS Functions	 :
Created by		 : Ashraful Islam 
Creation date 	 : 27-12-2015
Requirment Client: 
Requirment By    : 
Requirment type  : 
Requirment       : 
Affected page    : 
Affected Code    :              
DB Script        : 
Updated by 		 : 
Update date		 : 
QC Performed BY	 :		
QC Date			 :	
Comments		 : From this version oracle conversion is start
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Woven Service Booking", "../../", 1, 1,$unicode,'','');
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
    
function openmypage_order(page_link,title)
{
	if (form_validation('cbo_basis_on','Booking Basis')==false)
	{
		return;
	}	
	else
	{
		page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_basis_on*txt_fab_booking','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=470px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];;
			var id=this.contentDoc.getElementById("po_number_id");
			var po=this.contentDoc.getElementById("po_number");
			var booking_no=this.contentDoc.getElementById("booking_no");
			if (id.value!="")
			{
				reset_form('','booking_list_view','txt_order_no_id*txt_order_no*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_supplier_name*txt_attention*txt_delivery_date*txt_booking_no','txt_booking_date,<? echo date("d-m-Y"); ?>');
				freeze_window(5);
				document.getElementById('txt_order_no_id').value=id.value;
				document.getElementById('txt_order_no').value=po.value;
				document.getElementById('txt_fab_booking').value=booking_no.value;
				get_php_form_data( id.value, "populate_order_data_from_search_popup", "requires/service_booking_knitting_controller_v2" );
				release_freezing();
	
			}
		}
	}
}


function set_process(fabric_desription_id,type)
{	
	$("#booking_list_view").text('');
	// fabric_desription_id=$("#cbo_fabric_description").val();
	if(type=='set_process')
	{     
		show_list_view(document.getElementById('txt_job_no').value+'**'+1+'**'+fabric_desription_id+'**'+1+'**'+1+'**'+document.getElementById('txt_order_no_id').value+'**'+document.getElementById('txt_booking_no').value+'****'+document.getElementById('service_rate_from').value+'**'+document.getElementById('txt_program_no').value+'**'+document.getElementById('cbo_pay_mode').value, 'lapdip_approval_list_view_edit','booking_list_view','requires/service_booking_knitting_controller_v2','$(\'#hide_fabric_description\').val(\'\')');
	}
	if(type=="colorsizesensitive")
	{        
		show_list_view(document.getElementById('txt_job_no').value+'**'+1+'**'+fabric_desription_id+'**'+1+'**'+1+'**'+document.getElementById('txt_order_no_id').value+'**'+document.getElementById('txt_booking_no').value+'****'+document.getElementById('service_rate_from').value+'**'+document.getElementById('txt_program_no').value+'**'+document.getElementById('cbo_pay_mode').value, 'lapdip_approval_list_view_edit','booking_list_view','requires/service_booking_knitting_controller_v2','$(\'#hide_fabric_description\').val(\'\')');
	}
	$("#hide_fabric_description").val(fabric_desription_id);
}

function fnc_fabric_description_id(color_id, button_status, type)
{
	var hide_color_id='';
	if(type==1)
	{
		hide_color_id=document.getElementById('hide_fabric_description').value;
		//document.getElementById('copy_val').checked=true;
	}
	else
	{
		hide_color_id=parseInt(document.getElementById('hide_fabric_description').value);
		//document.getElementById('copy_val').checked=false;
	}

	if(color_id==hide_color_id)
	{
		document.getElementById('hide_fabric_description').value='';
		set_button_status(0, permission, 'fnc_trims_booking',1);
	}
	else
	{
		document.getElementById('hide_fabric_description').value=color_id;
		set_button_status(button_status, permission, 'fnc_trims_booking',1);	
	}
}
function setmaster_value(process, sensitivity)
{
	document.getElementById('cbo_process').value=process;
	document.getElementById('cbo_colorsizesensitive').value=sensitivity;
}

function calculate_amount(rowId)
{
	var txt_balqnty=(document.getElementById('txt_balqnty_'+rowId).value)*1;
	var txt_woqnty=(document.getElementById('txt_woqnty_'+rowId).value)*1;
	var txt_rate=(document.getElementById('txt_rate_'+rowId).value)*1;
	var service_rate_from = $('#service_rate_from').val()*1;
	var pre_cost_rate=$('#txt_rate_'+rowId).attr('pre-cost-rate')*1;
	console.log(service_rate_from+'--'+pre_cost_rate+'--'+txt_rate);

	if(txt_woqnty>txt_balqnty)
		{
			alert("Exceed qty not allowed.\n Bal. Qty :");
			$('#txt_woqnty_'+rowId).val('')
			return;
		}	

	if(service_rate_from==2) //No
	{
		if(txt_rate>pre_cost_rate)
		{
			alert("Rate can't greater then budget");
			$('#txt_rate_'+rowId).val('')
			return;
		}	
	}
	var txt_amount=txt_woqnty*txt_rate;
	document.getElementById('txt_amount_'+rowId).value=txt_amount;	

}

function copy_value(type,row_id,color_id)
{
	 var copy_val=document.getElementById('copy_qnty').checked;
	 var copy_rate=document.getElementById('copy_rate').checked;
	 var rowCount=$('#table_list_view tbody tr').length;
		if(type=='txt_woqnty'){
			for(var j=1; j<=rowCount; j++)
			{
				
					document.getElementById('txt_woqnty_'+j).value="";
					document.getElementById('txt_amount_'+j).value="";	
					
			}
		}

		if(type=='txt_rate') {

			for(var j=1; j<=rowCount; j++)
			{
				
					document.getElementById('txt_rate_'+j).value="";
					
			}
		}
				
		if(type=='gmts_color') {

				for(var j=1; j<=rowCount; j++){
				var color=document.getElementById('gmts_color_id_'+j).value;

					if(color_id==color){
						
						var txt_balqnty=(document.getElementById('txt_balqnty_'+j).value)*1;
						var hidden_rate=(document.getElementById('hidden_rate_'+j).value)*1;
						document.getElementById('txt_woqnty_'+j).value=txt_balqnty;
						document.getElementById('txt_rate_'+j).value=hidden_rate;
						var txt_amount=txt_balqnty*hidden_rate;
						document.getElementById('txt_amount_'+j).value=txt_amount;	
					}

				}
			}



			if(type=='sdate') {

				var sdate=document.getElementById('startdate_'+row_id).value;
				for(var j=1; j<=rowCount; j++){
				var color=document.getElementById('gmts_color_id_'+j).value;
				

					if(color_id==color){
						
						document.getElementById('startdate_'+j).value=sdate;
						
					}

				}
			}

			if(type=='edate') {

				var edate=document.getElementById('enddate_'+row_id).value;
				for(var j=1; j<=rowCount; j++){
				var color=document.getElementById('gmts_color_id_'+j).value;

					if(color_id==color){

					document.getElementById('enddate_'+j).value=edate;

					}

				}
			}



	
}


function fnc_generate_booking()
{
	
	if (form_validation('txt_order_no_id','Order No*Fabric Nature*Fabric Source')==false)
	{
		return;
	}
	else
	{
		var data="action=generate_fabric_booking"+get_submitted_data_string('txt_order_no_id',"../../");
		http.open("POST","requires/service_booking_knitting_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_booking_reponse;
	}
}

function fnc_generate_booking_reponse()
{
	if(http.readyState == 4) 
	{
		document.getElementById('booking_list_view').innerHTML=http.responseText;
	}
}

/*function fnc_generate_booking(param,cbo_company_name)
{
	
	    var txt_delivery_date= document.getElementById('txt_delivery_date').value
	    var data="'"+param+"'"
		var data="action=generate_fabric_booking&data="+data+'&cbo_company_name='+cbo_company_name+'&txt_delivery_date='+txt_delivery_date;
		http.open("POST","requires/service_booking_knitting_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_booking_reponse;
	
}

function fnc_generate_booking_reponse()
{
	if(http.readyState == 4) 
	{
		document.getElementById('booking_list_view').innerHTML=http.responseText;
		set_all_onclick();
		
	}
}*/


function open_consumption_popup(page_link,title,po_id,i)
{
	var cbo_company_id=document.getElementById('cbo_company_name').value;
	var po_id =document.getElementById(po_id).value;
	var txtwoq=document.getElementById('txtwoq_'+i).value;
	var cons_breck_downn=document.getElementById('consbreckdown_'+i).value;
	var cbocolorsizesensitive=document.getElementById('cbocolorsizesensitive_'+i).value;
	if(po_id==0 )
	{
		alert("Select Po Id")
	}
	
	else
	{
		var page_link=page_link+'&po_id='+po_id+'&cbo_company_id='+cbo_company_id+'&txtwoq='+txtwoq+'&cons_breck_downn='+cons_breck_downn+'&cbocolorsizesensitive='+cbocolorsizesensitive;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
			var woq=this.contentDoc.getElementById("cons_sum");
			document.getElementById('consbreckdown_'+i).value=cons_breck_down.value;
			document.getElementById('txtwoq_'+i).value=woq.value;
			document.getElementById('txtamount_'+i).value=(woq.value)*1*(document.getElementById('txtrate_'+i).value);
		}	
	}
}
 
 
function openmypage_booking(page_link,title)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];       
		var theemail=this.contentDoc.getElementById("selected_booking");		
		if (theemail.value!="")
		{
			reset_form('servicebooking_1','booking_list_view','','txt_booking_date,<? echo date("d-m-Y"); ?>');
			$('#hide_fabric_description').val('');
		 	get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/service_booking_knitting_controller_v2" );
	   		set_button_status(1, permission, 'fnc_trims_booking',1);
		    show_list_view(document.getElementById('txt_job_no').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('booking_mst_id').value, 'fabric_detls_list_view','data_panel','requires/service_booking_knitting_controller_v2','');
		}
	}
}


function open_terms_condition_popup(page_link,title)
{
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	if (txt_booking_no=="")
	{
		alert("Save The Booking First")
		return;
	}	
	else
	{
	    page_link=page_link+get_submitted_data_string('txt_booking_no','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=470px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
}


function fnc_trims_booking( operation )
{
	freeze_window(operation);
	var data_all="";
	if (form_validation('txt_order_no*cbo_company_name*cbo_pay_mode*cbo_supplier_name*cbo_currency','Order No*Company Name*Pay Mode*Supplier Name*Currency')==false)
	{
		release_freezing();
		return;
	}
	else
	{
		data_all=data_all+get_submitted_data_string('txt_booking_no*cbo_company_name*cbo_supplier_name*cbo_currency*txt_exchange_rate*txt_booking_date*txt_delivery_date*cbo_pay_mode*txt_attention*cbo_buyer_name*txt_job_no*txt_order_no_id*cbo_process*cbo_colorsizesensitive*cbo_ready_to_approved*txt_fab_booking',"../../");
	}
	//reset_form('','servicebooking_1','txt_booking_no','');
	var is_approved=$('#id_approved_id').val();//Chech The Approval item.. Change not allowed

	if(is_approved==1 || is_approved==3)
	{
		alert("This Order is Approved. So Change Not Allowed");
		release_freezing();
		return;
	}
	var hide_fabric_description=$('#hide_fabric_description').val();
	var data="action=save_update_delete&operation="+operation+data_all+'&hide_fabric_description='+hide_fabric_description;
	
	http.open("POST","requires/service_booking_knitting_controller_v2.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_trims_booking_reponse;
}
	 
function fnc_trims_booking_reponse()
{
	if(http.readyState == 4) 
	{
		 var reponse=trim(http.responseText).split('**');
		 show_msg(trim(reponse[0]));
		 if(reponse[0]==0 || reponse[0]==1)
		 {
			document.getElementById('txt_booking_no').value=reponse[1];
			if(reponse[0]==0)
			{
				document.getElementById('booking_mst_id').value=reponse[2];
			}
			$("#txt_fab_booking").removeAttr("disabled","disabled");
		 	set_button_status(1, permission, 'fnc_trims_booking',1);
		 }
		
		/*if(trim(reponse[0])=='approved'){
			alert("This booking is approved")
		    release_freezing();
		    return;
		}*/
		
		 if(reponse[0]==2)
		 {
			set_button_status(0, permission, 'fnc_trims_booking',1);
			reset_form('','','txt_booking_no*txt_order_no*cbo_company_name*txt_order_no_id*txt_job_no*cbo_buyer_name*cbo_currency*txt_exchange_rate*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_supplier_name*txt_attention','txt_booking_date,<? echo date("d-m-Y"); ?>'); 
		 }
		 release_freezing();
	}
}
 



function fnc_service_booking_dtls( operation )
{
	
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		alert('Please  Save Master Part First');return;
	}
	var data_all="";
	var hide_fabric_description=$('#hide_fabric_description').val();
	var row_num=$('#table_list_view tbody tr').length;
	var program_no =$('#txt_program_no').val();    
	var txt_prev_wo_qnty=($('#txt_prev_wo_qnty_'+hide_fabric_description).val())*1;
    var tot_req_qnty=0; var tot_wo_qnty=0;
	// alert(row_num);
	for (var i=1; i<=row_num; i++)
	{
		var txt_reqqty= (document.getElementById('txt_reqqty_'+i).value)*1;
		var txt_woqnty= (document.getElementById('txt_woqnty_'+i).value)*1;
		var rate= (document.getElementById('txt_rate_'+i).value)*1;
	
		
		  tot_req_qnty +=txt_reqqty;
		  tot_wo_qnty +=txt_woqnty;
		 

		  
		
		data_all+=get_submitted_data_string('po_id_'+i+'*fabric_description_id_'+i+'*artworkno_'+i+'*color_size_table_id_'+i+'*gmts_color_id_'+i+'*item_color_id_'+i+'*gmts_size_id_'+i+'*item_size_'+i+'*uom_'+i+'*txt_woqnty_'+i+'*txt_rate_'+i+'*txt_amount_'+i+'*txt_paln_cut_'+i+'*updateid_'+i+'*startdate_'+i+'*enddate_'+i+'*sizesensitive_id_'+i,"../../",i);	


		
	}
	if(operation==0)
	{
		var total_curr_wo_woqnty=txt_prev_wo_qnty+tot_wo_qnty;
	}
	else
	{
		var total_curr_wo_woqnty=txt_prev_wo_qnty+tot_wo_qnty;
	}
	
	var check_balance_qnty=total_curr_wo_woqnty-tot_req_qnty;
	//alert(total_curr_wo_woqnty+'='+txt_prev_wo_qnty+'='+tot_wo_qnty);
	if(operation!=2)
	{
		if(number_format(total_curr_wo_woqnty,3)>tot_req_qnty)
		{
				var booking_msg="Exceed qty not allowed.\n Total Req. Qty : "+tot_req_qnty;
				alert(booking_msg);
				return;
		}
	}
		
	data_all=data_all+get_submitted_data_string('cbo_process*cbo_colorsizesensitive*txt_job_no*txt_booking_no*cbo_dia*txt_all_update_id*booking_mst_id',"../../");
	var data="action=save_update_delete_dtls&operation="+operation+data_all+'&hide_fabric_description='+hide_fabric_description+'&row_num='+row_num+'&program_no='+program_no;
	freeze_window(operation);
	http.open("POST","requires/service_booking_knitting_controller_v2.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_service_booking_dtls_reponse;
}
	 
function fnc_service_booking_dtls_reponse()
{
	if(http.readyState == 4) 
	{
		// alert(http.responseText);
		 var reponse=trim(http.responseText).split('**');
		 show_msg(trim(reponse[0]));
		 if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		 {
		
		 	$('#booking_list_view').text('');
			$("#cbo_colorsizesensitive").val(1);
			$("#cbo_fabric_description").val(0);
			$("#cbo_dia").val(0);
			$("#cbo_colorsizesensitive").removeAttr("disabled","disabled");
			$("#cbo_fabric_description").removeAttr("disabled","disabled");
		
		 	set_button_status(0, permission, 'fnc_service_booking_dtls',2);
			show_list_view(reponse[1]+'**'+reponse[2]+'**'+reponse[3], 'fabric_detls_list_view','data_panel','requires/service_booking_knitting_controller_v2','$(\'#hide_fabric_description\').val(\'\')');
		 }
		 /*if(trim(reponse[0])=='approved'){
			alert("This booking is approved")
		    release_freezing();
		    return;
		}*/
		
		 release_freezing();
		 
		// get_php_form_data(reponse[1], "populate_data_from_search_popup", "requires/service_booking_knitting_controller_v2" );
	     
		
	}
}
 

function update_booking_data(data)
{
	var data=data.split("_");
	$("#booking_list_view").text('');
	$("#cbo_fabric_description").val(data[2]);
	$("#hide_fabric_description").val(data[2]);
	$("#cbo_colorsizesensitive").val(data[4]);
	$("#cbo_colorsizesensitive").attr("disabled",true);
	$("#cbo_fabric_description").attr("disabled",true);
	$("#txt_all_update_id").val(data[0]);
	$("#cbo_dia").val(data[7]);
    $("#txt_program_no").val(data[8]);
    //alert(data[8]);
	load_drop_down( 'requires/service_booking_knitting_controller_v2', data[2]+'**'+data[7]+'**'+data[2], 'load_drop_down_dia', 'dia_td');
	show_list_view(data[1]+'**'+0+'**'+data[2]+'**'+data[3]+'**'+data[4]+'**'+data[5]+'**'+data[6]+'**'+data[0]+'**'+document.getElementById('service_rate_from').value, 'lapdip_approval_list_view_edit','booking_list_view','requires/service_booking_knitting_controller_v2','');
    set_button_status(1, permission, 'fnc_service_booking_dtls',2);
}




function fnc_show_booking()
{
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		var data="action=show_trim_booking"+get_submitted_data_string('txt_booking_no',"../../");
		http.open("POST","requires/service_booking_knitting_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_booking_reponse;
	}
}

function fnc_show_booking_reponse()
{
	if(http.readyState == 4) 
	{
		document.getElementById('booking_list_view').innerHTML=http.responseText;
		set_button_status(1, permission, 'fnc_trims_booking',2);
		set_all_onclick();
	}
}

function generate_trim_report(action)
{

	/*var test=$("#app_status").val();
	alert(test);
	return;*/
	/*if(document.getElementById('app_status').value==1)
	{
		alert("test")
		return;
	}*/
	//
	//var cbo_process=$("#cbo_process").val();
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		var show_comments='';
		
		
		
		var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name*cbo_pay_mode*id_approved_id*txt_job_no*booking_mst_id*txt_booking_date',"../../")+'&show_comments='+show_comments;
		// alert(data);return;

		http.open("POST","requires/service_booking_knitting_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_trim_report_reponse;
	}	
}

function generate_trim_report_reponse()
{
	if(http.readyState == 4) 
	{
		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel2').html(file_data[0] );
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel2').innerHTML+'</body</html>');
		d.close();
	}
}
function check_exchange_rate()
{
	var cbo_currercy=$('#cbo_currency').val();
	var booking_date = $('#txt_booking_date').val();
	var cbo_company_name = $('#cbo_company_name').val();
	var response=return_global_ajax_value( cbo_currercy+"**"+booking_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/service_booking_knitting_controller_v2');
	var response=response.split("_");
	$('#txt_exchange_rate').val(response[1]);
}

function service_supplier_popup(id)
{
	var cbo_company_name = $('#cbo_company_name').val();
	var cbo_supplier_name = $('#cbo_supplier_name').val();
	if (form_validation('cbo_company_name*cbo_supplier_name*txt_exchange_rate','Company Name*cbo_supplier_name*Exchange Rate')==false)
	{
		return;
	}
	hidden_supplier_rate_id=$('#subcon_supplier_rateid_'+id).val();
	var title="Supplier Work Order Rate Info";
	var page_link = 'requires/service_booking_knitting_controller_v2.php?cbo_company_name='+cbo_company_name+'&cbo_supplier_name='+$("#cbo_supplier_name").val()+'&txt_exchange_rate='+$("#txt_exchange_rate").val()+'&hidden_supplier_rate_id='+hidden_supplier_rate_id+'&action=Supplier_workorder_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=400px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var hide_charge_id=this.contentDoc.getElementById("hide_charge_id").value;	
		var hide_supplier_rate=this.contentDoc.getElementById("hide_supplier_rate").value;
		var construction_compo=this.contentDoc.getElementById("hide_construction_compo").value;		
		//alert('#subcon_supplier_compo_'+id)
		$('#subcon_supplier_compo_'+id).val(construction_compo);
		$('#subcon_supplier_rateid_'+id).val(hide_charge_id);
		$('#txt_rate_'+id).val(hide_supplier_rate);
		var fabric_id=id.split("_");
		copy_value(fabric_id[0],fabric_id[1],'txt_rate');
		copy_value(fabric_id[0],fabric_id[1],'composition');
	}
}



// Program No pop-up 
function openmypage_programs(page_link,title)
{
    var pay_mode = document.getElementById('cbo_pay_mode').value; 
    
    if((pay_mode ==3) || (pay_mode ==5))
    {      
       $('#cbo_supplier_name').prop('disabled', true);
    }
    else 
    {
       $('#cbo_supplier_name').prop('disabled', false);
    }
    
   
	if (form_validation('txt_order_no','Order No')==false)
	{
		return;
	}
    else
    {
        var orderNo = document.getElementById('txt_order_no').value;         
        var jobNo = document.getElementById('txt_job_no').value; 
        var supplier_id = document.getElementById('cbo_supplier_name').value; 
        var orderId = document.getElementById('txt_order_no_id').value; 

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link+"&orderNo="+orderNo+"&jobNo="+jobNo+"&supplier_id="+supplier_id+"&order_id="+orderId+"&pay_mode="+pay_mode, title, 'width=900px,height=450px,center=1,resize=1,scrolling=0','../')
        emailwindow.onclose=function()
        {           
            var theform = this.contentDoc.forms[0];
            var theemail = this.contentDoc.getElementById("selected_program_no_primary_id").value; 
            var data = theemail.split("_");
            if (data[0]!="")
            {
                get_php_form_data( data[0], "populate_data_from_program_popup", "requires/service_booking_knitting_controller_v2" );
                set_button_status(1, permission, 'fnc_trims_booking',1);

				document.getElementById('cbo_fabric_description').value=data[1];

				load_drop_down( 'requires/service_booking_knitting_controller_v2',data[1], 'load_drop_down_dia', 'dia_td');
				set_process(data[1],'set_process');	
                //set_button_status(1, permission, 'fnc_trims_booking',1);
            }
        }
    }
}


function print_report_button_setting(report_ids)
{
	if(report_ids){
		$("#print_booking").hide();	 
	
	}
	else
	{
		$("#print_booking").show();	 

	}
	
	var report_id=report_ids.split(",");
	for (var k=0; k<report_id.length; k++)
		{
			
			if(report_id[k]==175)
			{
				$("#print_booking").show();	 
			}
		}
}

	function fnc_fab_booking(page_link,title)
	{
		if (form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var company=$("#cbo_company_name").val()*1;
		var buyer=$("#cbo_buyer_name").val()*1;
		var order_no_id=$("#txt_order_no_id").val()*1;
		var txt_job_no=$("#txt_job_no").val();
		//alert(company);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&company='+company+'&buyer='+buyer+'&order_no_id='+order_no_id+'&job_no='+txt_job_no, title, 'width=1190px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");
			if (theemail.value!="")
			{
				//reset_form('fabricbooking_1','booking_list_view','','txt_booking_date,<? //echo date("d-m-Y"); ?>');
			//	get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/service_booking_aop_urmi_controller" );
				//check_month_setting();
				//var is_approved_id=$('#id_approved_id').val();
				//alert(is_approved_id);
			
				//$('#cbo_company_name').attr('disabled','true');
				//set_button_status(1, permission, 'fnc_fabric_booking',1);
				$("#txt_fab_booking").val(theemail.value);
			
				
			}
		}
	}
	function fnc_basis_value(basis_id){
		if(basis_id==1){
			$("#basis_on").html("Selected Order No");
		
		}else{
			$("#basis_on").html("Selected Booking No");
			
		}

	}

</script>

</head>

<body onLoad="set_hotkey(); check_exchange_rate();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="servicebooking_1"  autocomplete="off" id="servicebooking_1">
        <fieldset style="width:950px;">
        <legend>Service Booking</legend>
            <table  width="1100" cellspacing="2" cellpadding="0" border="0" style="">
                <tr>
                    <td align="right" class="must_entry_caption" colspan="5">Booking No </td>              <!-- 11-00030  -->
                    <td colspan="5">
                    	<input class="text_boxes" type="text" style="width:150px" onDblClick="openmypage_booking('requires/service_booking_knitting_controller_v2.php?action=service_booking_popup','Service Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
                        <input type="hidden" id="id_approved_id">
                        <input type="hidden" id="booking_mst_id">
                    </td>                       
                </tr>
                <tr>
				  <td  width="100" class="must_entry_caption">Company Name</td>
                    <td  >
						<? 
                            echo create_drop_down( "cbo_company_name", 100, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/service_booking_knitting_controller_v2', this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data( this.value, 'print_report_button', 'requires/service_booking_knitting_controller_v2');check_exchange_rate();","","" );
                        ?>	  
                    </td>
					<td class="must_entry_caption" width="100" >Basis On</td>   
                    <td> 
                    <? 
						$basis_on=array(1=>"Order",2=>"Booking");
                    	echo create_drop_down( "cbo_basis_on", 112, $basis_on,"", 1, "-Select-", 2, "fnc_basis_value(this.value)",0 );		
                   	
                    ?>
                    </td>
                    <td class="must_entry_caption"  width="100" id="basis_on">Selected Booking No</td>   
                    <td colspan="3">
                    	<input class="text_boxes" type="text" style="width:330px" placeholder="Browse Order"  onDblClick="openmypage_order('requires/service_booking_knitting_controller_v2.php?action=order_search_popup','Order Search')"   name="txt_order_no" id="txt_order_no"/>
                    	<input class="text_boxes" type="hidden" style="width:42px;"  name="txt_order_no_id" id="txt_order_no_id"/>
                        <input class="text_boxes" type="hidden"   name="service_rate_from" id="service_rate_from" value=""/>
                    </td>
					<td width="100">Job No.</td>
                    <td width="100">
                    	<input style="width:100px;" type="text" class="text_boxes"  name="txt_job_no" id="txt_job_no" disabled  /> 
                    </td>
                </tr>
                <tr>
			    	
                    <td width="100">Buyer Name</td>   
                    <td id="buyer_td"> 
						<?  
                        	echo create_drop_down( "cbo_buyer_name", 100, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
					<td width="100">Booking Date</td>
                    <td >
                    	<input class="datepicker" type="text" style="width:100px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" disabled />	
                    </td>
					<td  width="100">Delivery Date</td>
                    <td >
                    	<input class="datepicker" type="text" style="width:100px" name="txt_delivery_date" id="txt_delivery_date"/>	
                    </td>
					<td class="must_entry_caption" width="100">Pay Mode</td>
                    <td>
                    	<? echo create_drop_down( "cbo_pay_mode", 112, $pay_mode,"", 1, "-- Select Pay Mode --", "", "load_drop_down( 'requires/service_booking_knitting_controller_v2', this.value, 'load_drop_down_supplier', 'supplier_td' )","" ); ?> 
                    </td>
					<td   width="100">Fabric Booking</td>
					<td><input class="text_boxes" type="text" style="width:100px;"  name="txt_fab_booking" id="txt_fab_booking" onDblClick="fnc_fab_booking('requires/service_booking_knitting_controller_v2.php?action=fabric_booking_popup','fabric Booking Search')" placeholder="Browser" readonly/> </td>
                </tr>
                <tr>
			    	
					<td class="must_entry_caption" width="100">Supplier/Party Name</td>
                    <td id="supplier_td"  width="100">
						<?
                            echo create_drop_down( "cbo_supplier_name", 100, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/service_booking_knitting_controller_v2');",0 );
                        ?> 
                    </td> 
                    <td width="100" class="must_entry_caption">Currency</td>
                    <td>
						<? 
                        	echo create_drop_down( "cbo_currency", 112, $currency,"", 1, "-- Select --", 0, "check_exchange_rate();",0 );//	set_conversion_rate(this.value, $('#txt_booking_date').val(), '../../', 'txt_exchange_rate')	
                        ?>	
                    </td>
                    <td  width="100">Exchange Rate</td>
                    <td>
                    	<input style="width:100px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate"  readonly />  
                    </td>
                    <td  width="100">Attention</td>   
                    <td  >
                    	<input class="text_boxes" type="text" style="width:100px;"  name="txt_attention" id="txt_attention"/>
                    	<input type="hidden" class="image_uploader" style="width:100px" value="Lab DIP No" onClick="openmypage( 'requires/service_booking_knitting_controller_v2.php?action=lapdip_no_popup', 'Lapdip No', 'lapdip')">
                    </td>
					<td  width="100">Ready To Approved</td>  
                    <td >
                    <?
                    echo create_drop_down( "cbo_ready_to_approved", 112, $yes_no,"", 1, "-- Select--", 2, "","","" );
                    ?>
                    </td>
                </tr>
              
              
                <tr>
                   
                    <td valign="middle" colspan="5" align="right">
						<input type="button" class="image_uploader" style="width:120px" value="ADD FILE" onClick="file_uploader ( '../../', document.getElementById('txt_booking_no').value,'', 'service_booking_for_knitting_v2', 2 ,1)">
                    	<input type="button" class="image_uploader" style="width:160px" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('txt_booking_no').value,'', 'service_booking_for_knitting_v2', 0 ,1)">
                    </td>
                  
                    <td colspan="5" align="left">
                    
						<? 
                        include("../../terms_condition/terms_condition.php");
                        terms_condition(182,'txt_booking_no','../../');
                        ?>                    
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="10" valign="top" id="booking_list_view1">
                    </td>
                </tr>
                 <tr>
                    <td align="center" colspan="10" valign="top" id="app_status" style="font-size:18px; color:#F00">
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="10" valign="middle" class="button_container">
                    	<? echo load_submit_buttons( $permission, "fnc_trims_booking", 0,0 ,"reset_form('servicebooking_1','','','','','')",1) ; ?>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="10" height="10">
                        <div id="pdf_file_name"></div>
                       
						 <input type="button" value="Print Booking" onClick="generate_trim_report('show_trim_booking_report')"  style="width:100px;display:none;" name="print_booking" id="print_booking" class="formbutton" />  
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
    <br/>
    <form name="servicebookingknitting_1"  autocomplete="off" id="servicebookingknitting_1">   
        <fieldset style="width:1200px;">
        <legend>Service Booking</legend>
          
			<div>
					<p align="right">
                        <input type="hidden" name="hide_fabric_description"   id="hide_fabric_description" value="">
                        <input type="hidden" name="txt_all_update_id"   id="txt_all_update_id" value="">
					</p>              <!-- 11-00030  -->
                                
              
				</div>
            <div id="booking_list_view"></div>
			<table  width="900" cellspacing="2" cellpadding="0" border="0">
             
               
		
			 <tr>                     
					 <td  width="130" height="" align="right" colspan="6"> 
						 <input type="hidden" name="txt_program_no"   id="txt_program_no" value="">
						 <input type="hidden" name="cbo_process"   id="cbo_process" value="">
						 <input type="hidden" name="cbo_fabric_description"   id="cbo_fabric_description" value="">
						 <input type="hidden" name="cbo_dia"   id="cbo_dia" value="">
						 <input type="hidden" name="cbo_colorsizesensitive"   id="cbo_colorsizesensitive" value="">
						 <input type="hidden" name="hide_fabric_description"   id="hide_fabric_description" value="">
						 <input type="hidden" name="txt_all_update_id"   id="txt_all_update_id" value="">
					 </td>              <!-- 11-00030  -->
				 <td  width="170" > </td>
			 </tr>
			 <tr>
				 <td align="center" colspan="6" valign="middle" class="button_container">
					 
					 <? echo load_submit_buttons( $permission, "fnc_service_booking_dtls", 0,0 ,"reset_form('servicebookingknitting_1','','','','','')",2) ; ?>
				 </td>
			 </tr>
			 
		 </table>
            <br/><br/> 
            <div style="" id="data_panel"></div>
            <br/><br/>
            <div style="display:none" id="data_panel2"></div> 
        </fieldset>
    </form>
</div>
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>