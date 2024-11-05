<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Service Acknowledgement				
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	04-09-2022
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
echo load_html_head_contents("Service Booking For Dyeing V2", "../../", 1, 1,$unicode,'','');
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 

var permission='<? echo $permission; ?>';
function openmypage_work_order(page_link,title)
	{
		if (form_validation('cbo_company_name*cbo_wo_type','Company Name*WO Type')==false)
		{
			return;
		}	
		else
		{
			var wo_type=$("#cbo_wo_type").val();var process="";
			if(wo_type==1){
				page_link='requires/service_acknowledgement_controller.php?action=work_order_embellishment_popup';
			}else if(wo_type==2){
				page_link='requires/service_acknowledgement_controller.php?action=embellishment_wo_without_order_popup';
			}else if(wo_type==3){
				page_link='requires/service_acknowledgement_controller.php?action=work_order_lab_test_popup';
			}else if(wo_type==4){
				page_link='requires/service_acknowledgement_controller.php?action=work_order_knitting_popup';
				process=1;
			}else if(wo_type==5){
				page_link='requires/service_acknowledgement_controller.php?action=work_order_knitting_popup';
				process=31;
			}else if(wo_type==6){
				page_link='requires/service_acknowledgement_controller.php?action=service_wo_popup';
			}
			
			page_link=page_link+"&process_id="+process+get_submitted_data_string('cbo_company_name*cbo_wo_type','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=450px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var job_no="";
				var theform=this.contentDoc.forms[0];
				var booking=this.contentDoc.getElementById("selected_booking");
				var booking_id=this.contentDoc.getElementById("selected_booking_id");
				var supplier_id=this.contentDoc.getElementById("hidd_supplier_id");
				var exchange_rate=this.contentDoc.getElementById("hidd_exchange_rate");
					if(wo_type==4 || wo_type==5){
						var job_no=this.contentDoc.getElementById("selected_job_no");
					}
				if (booking.value!="")
				{
					freeze_window(5);
					
					
					$("#txt_workorder_no").val(booking.value);
					$('#txt_workorder_no_id').val(booking_id.value);
					$("#cbo_service_company").val(supplier_id.value);
					$('#txt_exchange_rate').val(exchange_rate.value);
					
					show_list_view(booking_id.value+'_'+booking.value+'_'+wo_type+'_'+job_no.value+'_'+process,'print_booking_list_view','booking_list_view2','requires/service_acknowledgement_controller','setFilterGrid(\'list_view\',-1)');
				
					release_freezing();
				}
			}
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
		http.open("POST","requires/service_acknowledgement_controller.php",true);
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
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&cbo_company_name='+cbo_company_name, title, 'width=1000px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		
		if (theemail.value!="")
		{
		 	get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/service_acknowledgement_controller" );
	   		set_button_status(1, permission, 'fnc_trims_booking',1);
			disable_enable_fields('cbo_company_name*cbo_wo_type*txt_workorder_no',1);
		}
	}
}





	function fnc_trims_booking( operation )
	{
		

		
		var data_all="";
		if (form_validation('cbo_company_name*txt_workorder_no','Company Name*WO Booking')==false)
		{
			return;
		}
		else
		{
			
			var row_num=$('#table_list_view tbody tr').length;

       var check_field=0;

	   var cnt_fill_ackn_qty=0;
	   for (var i=1; i<=row_num; i++)
	   {
			var ackn_qnty=$('#txt_ackn_qnty_'+i).val()*1;
			if(ackn_qnty=='')
			{
				cnt_fill_ackn_qty=cnt_fill_ackn_qty+1;
				
			}
	   }
	   if(cnt_fill_ackn_qty==row_num){
				alert("fill up Ackn Qnty.");
				check_field=1 ; return;
	   }

		for (var i=1; i<=row_num; i++)
		{
			var ackn_qnty=$('#txt_ackn_qnty_'+i).val()*1;
			var wo_qnty=$('#txt_wo_qnty_'+i).val()*1;
			var cum_qnty=$('#txt_cum_qnty_'+i).val()*1;
			var total_qty=ackn_qnty+cum_qnty;
			
			 

			if(operation==0)
			{
				if(wo_qnty < total_qty){
					alert("Ackn Qty grather than WO qty"); txt_ackn_qnty_1
					return;
				};
			}

			data_all+=get_submitted_data_string('txt_job_no_'+i+'*order_id_'+i+'*gmts_item_id_'+i+'*emb_name_id_'+i+'*emb_type_id_'+i+'*body_part_id_'+i+'*uom_id_'+i+'*txt_wo_qnty_'+i+'*txt_ackn_qnty_'+i+'*txt_rate_'+i+'*txt_amount_'+i+'*txt_remarks_'+i+'*txt_test_item_'+i+'*test_item_id_'+i+'*test_for_id_'+i+'*test_category_id_'+i+'*color_id_'+i+'*txt_vat_amount_'+i+'*txt_tot_amount_'+i+'*txt_fab_description_'+i+'*txt_gsm_'+i+'*txt_dia_'+i+'*service_for_id_'+i+'*txt_service_details_'+i+'*txt_item_description_'+i+'*item_category_id_'+i+'*item_group_id_'+i+'*txt_service_number_'+i+'*fab_color_id_'+i+'*update_dtls_id_'+i+'*wo_dtls_id_'+i+'*service_lib_id_'+i,"../../",i);
			
		}
		

						
		data_all=data_all+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_workorder_no*txt_workorder_no_id*cbo_wo_type*txt_exchange_rate*txt_booking_date*txt_manual_challan*cbo_service_company*txt_remarks*booking_mst_id',"../../");
		
	
		}
		


		
		var data="action=save_update_delete&operation="+operation+data_all+'&row_num='+row_num;
		
		freeze_window(operation);
		http.open("POST","requires/service_acknowledgement_controller.php",true);
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
				document.getElementById('update_id').value=reponse[2];
				document.getElementById('booking_mst_id').value=reponse[2];
				disable_enable_fields('cbo_company_name*cbo_wo_type*txt_workorder_no',1);
				set_button_status(1, permission, 'fnc_trims_booking',1);
				 var wo_type=$('#cbo_wo_type').val();
   				 show_list_view(reponse[2]+'_'+wo_type,'service_ackn_booking_list_view','booking_list_view2','requires/service_acknowledgement_controller','setFilterGrid(\'list_view\',-1)');
				
			}
			if(reponse[0]==2)
			{
				disable_enable_fields('cbo_company_name*cbo_wo_type*txt_workorder_no',0);
  				reset_form('servicebooking_1','booking_list_view2','','txt_booking_date,<? echo date("d-m-Y"); ?>');
 			    set_button_status(0, permission, 'fnc_trims_booking',1);
  			}
 			 release_freezing();
		}
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
			http.open("POST","requires/service_acknowledgement_controller.php",true);
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


	function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currency').val();
		var booking_date = $('#txt_booking_date').val();
		var cbo_company_name = $('#cbo_company_name').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/service_acknowledgement_controller');
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
		var page_link = 'requires/service_acknowledgement_controller.php?cbo_company_name='+cbo_company_name+'&cbo_supplier_name='+$("#cbo_supplier_name").val()+'&txt_exchange_rate='+$("#txt_exchange_rate").val()+'&hidden_supplier_rate_id='+hidden_supplier_rate_id+'&action=Supplier_workorder_popup';
		
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
	function fnc_fab_booking(page_link,title)
	{
		if (form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var company=$("#cbo_company_name").val()*1;
	
		var order_no_id=$("#txt_order_no_id").val()*1;
		
		//alert(company);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&company='+company+'&order_no_id='+order_no_id, title, 'width=1190px,height=450px,center=1,resize=1,scrolling=0','../')
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
			$("#basis_on").html("Selected Embellishment");
			
		}
		else if(basis_id==2){
			$("#basis_on").html("Selected Embellishment Without Order");
			
		}else if(basis_id==3){
			$("#basis_on").html("Selected Lab Test");
			
		}else if(basis_id==4){
			$("#basis_on").html("Selected Knitting");
			
		}else if(basis_id==5){
			$("#basis_on").html("Selected Dyeing");
			
		}else if(basis_id==6){
			$("#basis_on").html("Selected Service WO");
			
		}
		$("#txt_workorder_no").val("");
		$("#txt_workorder_no_id").val("");
		reset_form('servicebooking_1','booking_list_view2','','','','cbo_company_name*cbo_wo_type*txt_booking_date');
		 

	}
	function fnc_amount(rowid){
	
		var rate=$("#txt_rate_"+rowid).val()*1;
		var qnty=$("#txt_ackn_qnty_"+rowid).val()*1;
		var wo_qnty=$("#txt_wo_qnty_"+rowid).val()*1;
		if(qnty>wo_qnty){
			alert("Not allow Ackn Qnty more than WO Qnty.");
			$("#txt_ackn_qnty_"+rowid).val("");
			return;
		}
		var amount=rate*qnty;
		$("#txt_amount_"+rowid).val(amount.toFixed(6));
	}

	function generate_report(type){
		if ( form_validation('txt_booking_no','Booking No')==false ){
			return;
		}
		else{
				
			$report_title=$( "div.form_caption" ).html();
			var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*cbo_wo_type*txt_workorder_no*txt_manual_challan*txt_booking_date*cbo_service_company*txt_exchange_rate*txt_remarks*booking_mst_id',"../../")+'&report_title='+$report_title+'&path=../../';
			freeze_window(5);
			http.open("POST","requires/service_acknowledgement_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_report_reponse;
		}
	}

	function generate_report_reponse(){
		if(http.readyState == 4){
			var file_data=http.responseText.split('****');
			if(file_data[2]==100)
		{
		
			//$('#print_report4')[0].click();
		document.getElementById('printbooking').click();
		}
		else
		{
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0]);
		}
			// $('#pdf_file_name').html(file_data[1]);
			// $('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
			d.close();
			var content=document.getElementById('data_panel').innerHTML;
			release_freezing();
		}
	}

</script>
 
</head>
 
<body onLoad="set_hotkey();check_exchange_rate();">
<div style="width:100%;" align="center">
     <? echo load_freeze_divs ("../../",$permission);  ?>
            	<form name="servicebooking_1"  autocomplete="off" id="servicebooking_1">
            	<fieldset style="width:1200px;">
                <legend>Service Acknowledgement</legend>
            		<table  width="1200" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                    
                    <td  width="120" height="" align="right" class="must_entry_caption" colspan="4"> Service Acknowledge No: </td>              <!-- 11-00030  -->
                    <td  width="120" colspan="6">
                        <input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/service_acknowledgement_controller.php?action=service_booking_popup','Service Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
						<input type="hidden" id="booking_mst_id" value="">
                    </td>
                  
                    </tr>
					<tr>
                            
						
						  <td  width="120" class="must_entry_caption" align="right">Company Name</td>
						  <td>
						  <? 

							  //echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/woven_order_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' ); check_exchange_rate();","","" );
							  echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "check_exchange_rate();","","" );
							?>	  
						  </td>
					  <td class="must_entry_caption" width="120" align="right">Basis On</td>   
                      <td> 
                         <? 
						$basis_on=array(1=>"Embellishment",2=>"Embellishment Without Order",3=>"Lab Test",4=>"Knitting",5=>"Dyeing",6=>"Service WO");
                    	echo create_drop_down( "cbo_wo_type", 120, $basis_on,"",1, "-Select-", 1, "fnc_basis_value(this.value)",0 );		
                   	
                         ?>
                        </td>
							<td class="must_entry_caption"  width="120" id="basis_on" align="right">Selected Booking No</td>   
                            <td >
                                 <input class="text_boxes" type="text" style="width:160px;" placeholder="Double click for Order"  onDblClick="openmypage_work_order('requires/service_acknowledgement_controller.php?action=work_order_search_popup','Order Search')"   name="txt_workorder_no" id="txt_workorder_no"/>
                                 <input class="text_boxes" type="hidden" style="width:772px;"  name="txt_workorder_no_id" id="txt_workorder_no_id"/>
                                 <input class="text_boxes" type="hidden"   name="service_rate_from" id="service_rate_from" value=""/>
								 <input class="text_boxes" type="hidden"   name="update_id" id="update_id" value=""/>
                            </td>   
						   
					
							<td align="right" width="120">Manual Challan:</td>  
                        	<td height="10" >
                            	<input class="text_boxes" type="text" style="width: 120px;"  name="txt_manual_challan" id="txt_manual_challan"/>
                            	<input type="hidden" class="image_uploader" style="width:162px" value="Lab DIP No" onClick="openmypage('requires/service_acknowledgement_controller.php?action=lapdip_no_popup','Lapdip No','lapdip')">
                            </td> 
							<td  width="120" align="right">Ackn Date</td>
                         <td >
                                    <input class="datepicker" type="text" style="width:120px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" disabled />	
                         </td>
					</tr>
                  
                        
                       
                        <tr>
						   <td  width="120" align="right">Service Company:</td>
                             <td id="supplier_td">
                               <?
							   		echo create_drop_down( "cbo_service_company", 120, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
							   ?> 
                            </td> 
                     
                        	<td align="right" width="120">Exchange Rate</td>
                              <td >
                             <input style="width:120px;" type="text" class="text_boxes_numeric"  name="txt_exchange_rate" id="txt_exchange_rate"  readonly />  
                              </td>
							
							  <td  width="120" align="right">Remarks</td>
                             <td id="supplier_td" colspan="4">
								<input class="text_boxes" type="text" style="width: 360px;"  name="txt_remarks" id="txt_remarks"/>
                            </td> 
                       
							<td valign="middle" align="right" colspan="2">
								<input type="button" class="image_uploader" style="width:120px" value="ADD FILE" onClick="file_uploader ( '../../', document.getElementById('txt_booking_no').value,'', 'service_acknowledgement', 2 ,1)">
                            </td>
                           
                     </tr>

						
                        <tr>
                        	<input type="hidden" name="update_id" id="update_id" value=""><input type="hidden" id="report_ids" >
                        	<td id="button_data_panel" align="center" colspan="10" height="10"></td>
                        </tr>
                    </table>
                 
              </fieldset>
          <!-- </form>-->
              <br/>
           <!--<form name="servicebooking_2"  autocomplete="off" id="servicebooking_2">   -->
              <fieldset style="width:1200px;">
                <legend>Service Booking</legend> 
              <div id="booking_list_view">
			 			 
              </div>
			  <table  width="900" cellspacing="2" cellpadding="0" border="0"> 
                    <tr align="center">
                        <td colspan="6" id="booking_list_view2"></td>	
                    </tr>
						<tr>
                        	<td align="center" colspan="10" valign="middle" class="button_container">
                              <? echo load_submit_buttons( $permission, "fnc_trims_booking", 0,0 ,"reset_form('servicebooking_1','','','','','');",1) ; ?>
							  <input type="button" id="report" class="formbutton" value="Print" onClick="generate_report('show_service_ackn_report');" style="width:60px;" />
                            </td>
                        </tr>
						<tr>                     
                       		  <td  width="130" height="" align="right" colspan="6"> 
								
								 <input type="hidden" name="cbo_process"   id="cbo_process" value="">
                        		 <input type="hidden" name="cbo_colorsizesensitive"   id="cbo_colorsizesensitive" value="">
								 <input type="hidden" name="hide_fabric_description"   id="hide_fabric_description" value="">
                         		 <input type="hidden" name="txt_all_update_id"   id="txt_all_update_id" value="">
                         	 </td>              <!-- 11-00030  -->
                        	<td  width="170" > </td>
                        </tr>
                    </table>
           	  <br/>  <br/> 
             
             
               <div style="display:none" id="data_panel">
              </div> 
              </fieldset>
           </form>
              

</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>