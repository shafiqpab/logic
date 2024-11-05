<?
/*-------------------------------------------- Comments 
Version          : V1
Purpose			 : This form will create Multi Job Service Booking
Functionality	 :	
JS Functions	 :
Created by		 : Aziz 
Creation date 	 : 03-09-2018
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
	var mandatory_field='';
	var field_message = '';
	<?

	if(isset($_SESSION['logic_erp']['mandatory_field'][228]))
	{
		echo " mandatory_field = '". implode('*',$_SESSION['logic_erp']['mandatory_field'][228]) . "';\n";
		echo " field_message = '". implode('*',$_SESSION['logic_erp']['field_message'][228]) . "';\n";
	}
	?>
    
function openmypage_order(page_link,title)
{
	var cbo_short_type=document.getElementById('cbo_short_type').value;
	//alert(cbo_short_type);
	
	if (form_validation('txt_booking_no*cbo_company_name','Booking No*Company')==false)
	{
		return;
	}	
	else
	{
		page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_booking_month*cbo_booking_year*cbo_fabric_description*cbo_process*cbo_colorsizesensitive*cbo_short_type','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=470px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];;
			var id=this.contentDoc.getElementById("po_number_id");
			var po=this.contentDoc.getElementById("po_number");
			var conv_fab_id=this.contentDoc.getElementById("conv_fab_mst_id");
			//alert(conv_fab_id.value);
			if (id.value!="")
			{
				//reset_form('','booking_list_view','txt_order_no_id*txt_order_no*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_source*txt_booking_no','txt_booking_date,<? echo date("d-m-Y"); ?>');
				freeze_window(5);
				document.getElementById('txt_order_no_id').value=id.value;
				document.getElementById('txt_order_no').value=po.value;
				document.getElementById('txt_conv_id').value=conv_fab_id.value;
				get_php_form_data( id.value+'_'+cbo_short_type, "populate_order_data_from_search_popup", "requires/service_booking_multi_job_wise_knitting_controller" );
				set_button_status(1, permission, 'fnc_trims_booking',1);
				release_freezing();
	
			}
		}
	}
}


function set_process(fabric_desription_id,type)
{	
	//alert(fabric_desription_id);
	var txt_program_no=document.getElementById('txt_program_no').value;
	var company_name=document.getElementById('cbo_company_name').value;
	var cbo_colorsizesensitive=document.getElementById('cbo_colorsizesensitive').value;
	$("#txt_conv_id").val(fabric_desription_id);	
	if (form_validation('cbo_colorsizesensitive','Sensitivity')==false)
	{
		return;
	}	
	$("#booking_list_view").text('');
	fabric_desription_id=$("#cbo_fabric_description").val();
	if(type=='set_process')
	{     
		show_list_view(document.getElementById('txt_order_no_id').value+'**'+1+'**'+fabric_desription_id+'**'+document.getElementById('cbo_process').value+'**'+document.getElementById('cbo_colorsizesensitive').value+'**'+document.getElementById('txt_order_no_id').value+'**'+document.getElementById('txt_booking_no').value+'****'+document.getElementById('service_rate_from').value+'**'+document.getElementById('txt_program_no').value+'**'+document.getElementById('cbo_pay_mode').value+'**'+document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_short_type').value, 'show_detail_booking_list_view','booking_list_view','requires/service_booking_multi_job_wise_knitting_controller','$(\'#hide_fabric_description\').val(\'\')');
	}
	if(type=="colorsizesensitive")
	{        
		show_list_view(document.getElementById('txt_order_no_id').value+'**'+1+'**'+fabric_desription_id+'**'+document.getElementById('cbo_process').value+'**'+document.getElementById('cbo_colorsizesensitive').value+'**'+document.getElementById('txt_order_no_id').value+'**'+document.getElementById('txt_booking_no').value+'****'+document.getElementById('service_rate_from').value+'**'+document.getElementById('txt_program_no').value+'**'+document.getElementById('cbo_pay_mode').value+'**'+document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_short_type').value, 'show_detail_booking_list_view','booking_list_view','requires/service_booking_multi_job_wise_knitting_controller','$(\'#hide_fabric_description\').val(\'\')');
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

function calculate_amount(param1,param2)
{
	var txt_woqnty=(document.getElementById('txt_woqnty_'+param1+'_'+param2).value)*1;
	
	var txt_reqqty=(document.getElementById('txt_reqqty_'+param1+'_'+param2).value)*1;
	var txt_pre_amount=(document.getElementById('txt_pre_amount_'+param1+'_'+param2).value)*1;
	var priv_amount=(document.getElementById('txt_priv_amount_'+param1+'_'+param2).value)*1;
	var txt_hidden_bal_woqnty=(document.getElementById('txt_hidden_bal_woqnty_'+param1+'_'+param2).value)*1;
	var hidd_bal_amount=(document.getElementById('hidd_bal_amount_'+param1+'_'+param2).value)*1;
	var updateid= (document.getElementById('updateid_'+param1+'_'+param2).value)*1;
	var txt_rate=(document.getElementById('txt_rate_'+param1+'_'+param2).value)*1;
	var service_rate_from = $('#service_rate_from').val()*1; 
	var pre_cost_rate=$('#txt_rate_'+param1+'_'+param2).attr('pre-cost-rate')*1;
	console.log(service_rate_from+'--'+pre_cost_rate+'--'+txt_rate);
	//var txt_amount=(txt_woqnty*txt_rate)+priv_amount;
	var prev_woqnty=(document.getElementById('txt_prev_woqnty_'+param1+'_'+param2).value)*1;
	// var hidd_amount=(document.getElementById('hidd_amount_'+param1+'_'+param2).value)*1;
	 
		if(txt_pre_amount !=""){

			if(txt_amount >txt_pre_amount){
				alert("Amount can't greater than budget");
					document.getElementById('txt_rate_'+param1+'_'+param2).value=number_format((txt_pre_amount/prev_woqnty),1,'.','');;
					document.getElementById('txt_amount_'+param1+'_'+param2).value=txt_pre_amount;
					document.getElementById('txt_woqnty_'+param1+'_'+param2).value=prev_woqnty;
				return;
			}else{
				document.getElementById('txt_amount_'+param1+'_'+param2).value=number_format((txt_woqnty*txt_rate),2,'.','');;;
			}
		}

	if(service_rate_from==2) //No
	{
		/*if(txt_rate>pre_cost_rate)
		{
			alert("Rate can't greater then budget");
			$('#txt_rate_'+param1+'_'+param2).val(pre_cost_rate)
			return;
		}	*/
		
		if(txt_woqnty<1)
		{
		var txt_amount=txt_woqnty*txt_rate;
		}
		else
		{
		var txt_amount=number_format(txt_woqnty*txt_rate,4,'.','');
		}
		if(txt_woqnty<1) //For Fraction value check
		{
			var tot_wo_amt=((txt_woqnty*txt_rate)+priv_amount);//-txt_prev_woamt;
		}
		else
		{
			var tot_wo_amt=number_format(((txt_woqnty*txt_rate)+priv_amount),4,'.','');//-txt_prev_woamt;
		}
		
		var total_req_amount=txt_reqqty*pre_cost_rate;

		//    alert(tot_wo_amt+'>'+txt_pre_amount +'&&'+total_req_amount);
	    if(updateid>0){

					var bal_amount=(txt_reqqty*pre_cost_rate)-priv_amount;
					var current_amount=(txt_woqnty*txt_rate);
					if(current_amount>priv_amount){
						var extra_amt=current_amount-priv_amount;
					}
				 
					if(extra_amt>bal_amount)
					{
						//var txt_amount=txt_hidden_bal_woqnty*txt_rate;
						//var txt_woqnty=txt_hidden_bal_woqnty;
						alert("amount can't greater then budget");
						document.getElementById('txt_woqnty_'+param1+'_'+param2).value=prev_woqnty;
						document.getElementById('txt_amount_'+param1+'_'+param2).value=priv_amount;
						document.getElementById('txt_rate_'+param1+'_'+param2).value=priv_amount/prev_woqnty;
						return;
					}
		}else{


			var is_editable=(document.getElementById('txt_amount_vali_id').value)*1;
			if(is_editable==1){

				console.log(txt_amount+"==>"+tot_wo_amt+"==>"+hidd_bal_amount);
				if(txt_amount>hidd_bal_amount && hidd_bal_amount>0)
				{
					//var txt_amount=txt_hidden_bal_woqnty*pre_cost_rate;
					//var txt_woqnty=txt_hidden_bal_woqnty;
					alert("amount can't greater then budget");
					$('#txt_woqnty_'+param1+'_'+param2).val(txt_hidden_bal_woqnty);
				
					$('#txt_amount_'+param1+'_'+param2).val(txt_hidden_bal_woqnty*pre_cost_rate);
					$('#txt_rate_'+param1+'_'+param2).val(pre_cost_rate);
			
					return;
				}
			}
		}

		
 

		
	}
	//var txt_amount=txt_woqnty*txt_rate;
	//document.getElementById('txt_amount_'+param1+'_'+param2).value=txt_amount;	

}

function copy_value(param1,param2,type)
{
	 var copy_val=document.getElementById('copy_val').checked;
	 var rowCount=$('#table_'+param1+' tbody tr').length;
	 if(copy_val==true)
	  {
		  for(var j=param2; j<=rowCount; j++)
		  {
			  if(type=='txt_rate')
			  {
				var txt_woqnty=(document.getElementById('txt_woqnty_'+param1+'_'+j).value)*1;
				var txt_rate=(document.getElementById('txt_rate_'+param1+'_'+param2).value)*1;
				
				var pre_cost_rate=$('#txt_rate_'+param1+'_'+j).attr('pre-cost-rate')*1;
				var txt_pre_amount=(document.getElementById('txt_pre_amount_'+param1+'_'+j).value)*1;
				var priv_amount=(document.getElementById('txt_priv_amount_'+param1+'_'+j).value)*1;
				//  var txt_amount=(txt_woqnty*txt_rate)+priv_amount;	
				//  var amount_cal=txt_woqnty*txt_rate;	
				
				//  var txt_amount_cal=number_format_common(txt_woqnty*txt_rate,4,0);	
				//  var txt_pre_amount_cal=number_format_common(txt_pre_amount,4,0);	
				
				var txt_amount=txt_woqnty*txt_rate;	
				document.getElementById('txt_rate_'+param1+'_'+j).value=txt_rate;
				document.getElementById('txt_amount_'+param1+'_'+j).value=txt_amount;	
			  }
			  
			  if(type=='txt_woqnty')
			  {
				var txt_woqnty=(document.getElementById('txt_woqnty_'+param1+'_'+param2).value)*1;
				var txt_rate=(document.getElementById('txt_rate_'+param1+'_'+j).value)*1;
				var txt_pre_amount=(document.getElementById('txt_pre_amount_'+param1+'_'+j).value)*1;
				var txt_reqqty=(document.getElementById('txt_reqqty_'+param1+'_'+j).value)*1;
				var priv_amount=(document.getElementById('txt_priv_amount_'+param1+'_'+j).value)*1;
				var prev_woqnty=(document.getElementById('txt_prev_woqnty_'+param1+'_'+j).value)*1;
				var txt_hidden_bal_woqnty=(document.getElementById('txt_hidden_bal_woqnty_'+param1+'_'+j).value)*1;
				
				//var txt_amount_cal=number_format_common(txt_woqnty*txt_rate,4,0);	
				//var txt_pre_amount_cal=number_format_common(txt_pre_amount,4,0);
				//  var txt_woqnty=txt_woqnty+prev_woqnty;		
				//  var txt_amount_cal=txt_woqnty*txt_rate;	
				//   var amount_cal=txt_woqnty*txt_rate;	
				var txt_amount=txt_woqnty*txt_rate;
				document.getElementById('txt_woqnty_'+param1+'_'+j).value=txt_woqnty;
				document.getElementById('txt_amount_'+param1+'_'+j).value=txt_amount;
			  }
			 /*var artworkno= document.getElementById('artworkno_'+param1+'_'+j).value;
			 var mcdia= document.getElementById('txt_mcdia_'+param1+'_'+j).value;
			 var slength= document.getElementById('txt_slength_'+param1+'_'+j).value;
			 var uom= document.getElementById('uom_'+param1+'_'+j).value;
			 var startdate= document.getElementById('startdate_'+param1+'_'+j).value;
			 var enddate= document.getElementById('enddate_'+param1+'_'+j).value;*/
			 /* if(type=='startdate')
			  {
				  var start_date=(document.getElementById('startdate_'+param1+'_'+param2).value);
				  document.getElementById('startdate_'+param1+'_'+j).value=start_date;
			  }
			  if(type=='enddate')
			  {
				  var end_date=(document.getElementById('enddate_'+param1+'_'+param2).value);
				  document.getElementById('enddate_'+param1+'_'+j).value=end_date;
			  }*/
			
			  if(type=='artworkno' || type=='mcdia' || type=='slength' || type=='uom' || type=='startdate' || type=='enddate' || type=='ycount' || type=='lotno' || type=='brand') 
			  {
				  var artworkno=(document.getElementById('artworkno_'+param1+'_'+param2).value);
				  document.getElementById('artworkno_'+param1+'_'+j).value=artworkno;
				  
				  var txt_ycount=(document.getElementById('txt_ycount_'+param1+'_'+param2).value);
				  document.getElementById('txt_ycount_'+param1+'_'+j).value=txt_ycount;
				  //  alert(copy_val+'='+type+'='+txt_ycount);
				   var lottt=(document.getElementById('txt_lot_'+param1+'_'+param2).value);
				  document.getElementById('txt_lot_'+param1+'_'+j).value=lottt;
				  
				    var txt_brand=(document.getElementById('txt_brand_'+param1+'_'+param2).value);
				  document.getElementById('txt_brand_'+param1+'_'+j).value=txt_brand;
				  
				  var txt_mcdia=(document.getElementById('txt_mcdia_'+param1+'_'+param2).value);
				  document.getElementById('txt_mcdia_'+param1+'_'+j).value=txt_mcdia;
				  
				 // var art_workno=(document.getElementById('txt_gg_'+param1+'_'+param2).value);
				  //document.getElementById('txt_gg_'+param1+'_'+j).value=mgg;
				  
				  var txt_slength=(document.getElementById('txt_slength_'+param1+'_'+param2).value);
				  document.getElementById('txt_slength_'+param1+'_'+j).value=txt_slength;
				  
				   var uom=(document.getElementById('uom_'+param1+'_'+param2).value);
				  document.getElementById('uom_'+param1+'_'+j).value=uom;
				  
				    var end_date=(document.getElementById('enddate_'+param1+'_'+param2).value);
				  document.getElementById('enddate_'+param1+'_'+j).value=end_date;
				  
				  var start_date=(document.getElementById('startdate_'+param1+'_'+param2).value);
				  document.getElementById('startdate_'+param1+'_'+j).value=start_date;
				  
			  }
			  
			  
			 
			  if(type=='composition')
			  {
				  var composition=(document.getElementById('subcon_supplier_compo_'+param1+'_'+param2).value);
				  var supplier_rate_id=(document.getElementById('subcon_supplier_rateid_'+param1+'_'+param2).value);
				  document.getElementById('subcon_supplier_compo_'+param1+'_'+j).value=composition;
				  document.getElementById('subcon_supplier_rateid_'+param1+'_'+j).value=supplier_rate_id;
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
		http.open("POST","requires/service_booking_multi_job_wise_knitting_controller.php",true);
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
		http.open("POST","requires/service_booking_multi_job_wise_knitting_controller.php",true);
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
	var company=$("#cbo_company_name").val()*1;
	var cbo_short_type=$("#cbo_short_type").val()*1;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&company='+company+'&cbo_short_type='+cbo_short_type, title, 'width=1000px,height=450px,center=1,resize=1,scrolling=0','../')
		
	//emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1080px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];       
		var theemail=this.contentDoc.getElementById("selected_booking");		
		if (theemail.value!="")
		{
			reset_form('servicebooking_1','booking_list_view','','txt_booking_date,<? echo date("d-m-Y"); ?>');
			$('#hide_fabric_description').val('');
			$('#cbo_fabric_description').val(0);
			$('#txt_order_no').val('');
			$('#txt_order_no_id').val('');
		 	get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/service_booking_multi_job_wise_knitting_controller" );
	   		set_button_status(1, permission, 'fnc_trims_booking',1);
		    show_list_view(document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_booking_no').value, 'fabric_detls_list_view','data_panel','requires/service_booking_multi_job_wise_knitting_controller','');
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
	var data_all="";
	if (form_validation('cbo_company_name*cbo_pay_mode','Company Name*Pay Mode')==false)
	{
		return;
	}
	else
	{
		
		
	data_all=data_all+get_submitted_data_string('txt_booking_no*cbo_booking_month*cbo_booking_year*cbo_company_name*cbo_supplier_name*cbo_currency*txt_exchange_rate*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_source*txt_attention*txt_tenor*cbo_buyer_name*txt_order_no_id*cbo_process*cbo_colorsizesensitive*cbo_ready_to_approved*txt_remark*txt_delivery_to*cbo_short_type',"../../");
	}

	if(mandatory_field){
			if (form_validation(mandatory_field,field_message)==false)
			{
				return;
			}
		}
	if(operation==2)
		{
			var r=confirm("Press OK to Delete All Or Press Cancel");
			if(r==false){
				release_freezing();
				return;
			}
		}
	//reset_form('','servicebooking_1','txt_booking_no','');

	var hide_fabric_description=$('#hide_fabric_description').val();
	var data="action=save_update_delete&operation="+operation+data_all+'&hide_fabric_description='+hide_fabric_description;
	freeze_window(operation);
	http.open("POST","requires/service_booking_multi_job_wise_knitting_controller.php",true);
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
				document.getElementById('update_id').value=reponse[2];
			}
		 	set_button_status(1, permission, 'fnc_trims_booking',1);
			$('#cbo_buyer_name').attr('disabled','disabled');
			$('#cbo_company_name').attr('disabled','disabled');
		 }
		
		if(trim(reponse[0])=='approved'){
			alert("This booking is approved")
		    release_freezing();
		    return;
		}
		
		 if(reponse[0]==2)
		 {
			set_button_status(0, permission, 'fnc_trims_booking',1);
			release_freezing();
			reset_form('','data_panel','txt_booking_no*txt_order_no*cbo_company_name*txt_order_no_id*txt_job_no*cbo_buyer_name*cbo_currency*txt_exchange_rate*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_source*cbo_supplier_name*txt_attention*txt_tenor','txt_booking_date,<? echo date("d-m-Y"); ?>'); 
			$('#data_panel').text('');
			 
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
	if (form_validation('txt_order_no*cbo_fabric_description','PO No*Fabric Desc.')==false)
	{
		return;
	}
	if(mandatory_field){
			if (form_validation(mandatory_field,field_message)==false)
			{
				return;
			}
		}
		
		if(operation==2)
		{
			var r=confirm("Press OK to Delete Detail Part Or Press Cancel");
			if(r==false){
				release_freezing();
				return;
			}
		}
	var data_all="";
	var hide_fabric_description=$('#hide_fabric_description').val();
	var row_num=$('#table_'+hide_fabric_description+' tbody tr').length;
	//alert(row_num);
	var program_no =$('#txt_program_no').val();    
	//var txt_prev_wo_qnty=($('#txt_prev_wo_qnty_'+hide_fabric_description).val())*1;
    var tot_req_qnty=0; var tot_wo_qnty=0;
	for (var i=1; i<=row_num; i++)
	{
		
		var txt_woqnty= (document.getElementById('txt_woqnty_'+hide_fabric_description+'_'+i).value)*1;
		var txt_reqqty= (document.getElementById('txt_reqqty_'+hide_fabric_description+'_'+i).value)*1;
		var txt_prev_woqnty= (document.getElementById('txt_prev_woqnty_'+hide_fabric_description+'_'+i).value)*1;
		var txt_hidden_bal_woqnty= (document.getElementById('txt_hidden_bal_woqnty_'+hide_fabric_description+'_'+i).value)*1;
		var updateid= (document.getElementById('updateid_'+hide_fabric_description+'_'+i).value)*1;
		var txt_amount= (document.getElementById('txt_amount_'+hide_fabric_description+'_'+i).value)*1;
		var is_editable=(document.getElementById('txt_amount_vali_id').value)*1;
	
		if(operation!=2)
		{
		
			if(updateid!='')
			{
				
				if(operation==0)
				{
					var total_curr_wo_woqnty=txt_woqnty;
				}
				else
				{
					var total_curr_wo_woqnty=txt_woqnty;
				}
			}
			else
			{
				if(operation==0)
				{
					var total_curr_wo_woqnty=number_format(txt_prev_woqnty+txt_woqnty,2,'.','');
				}
				else
				{
					var total_curr_wo_woqnty=number_format(txt_prev_woqnty+txt_woqnty,2,'.','');
				}
			
				if(is_editable==1){
				
					var hidd_bal_amount= (document.getElementById('hidd_bal_amount_'+hide_fabric_description+'_'+i).value)*1;
					
					 if(txt_amount>hidd_bal_amount){
				 		var booking_msg2="Exceed Amount not allowed.\n Bal. Amount : "+hidd_bal_amount+"\n Current. Amount : "+txt_amount;
				 		alert(booking_msg2);
				 		return;
					 }
				}
			}
			
			if(is_editable !=1){
				if(txt_reqqty>0)
				{
					//alert(total_curr_wo_woqnty+'=='+txt_prev_woqnty+'=='+txt_woqnty+'=='+txt_reqqty);
					if(total_curr_wo_woqnty>txt_reqqty)
					{
							var booking_msg="Exceed qty not allowed.\n Req. Qty : "+txt_reqqty+"\n Current. Qty : "+total_curr_wo_woqnty;
							alert(booking_msg);
							return;
					}
				}
			}



			if(txt_woqnty>0)
			{
				//alert(total_curr_wo_woqnty+'=='+txt_prev_woqnty+'=='+txt_woqnty+'=='+txt_reqqty);
				if(total_curr_wo_woqnty>txt_reqqty)
				{
						var booking_msg="Exceed qty not allowed.\n Req. Qty : "+txt_reqqty;
						alert(booking_msg);
						return;
				}
			}
		}
	
		
		data_all+=get_submitted_data_string('po_id_'+hide_fabric_description+'_'+i+'*fabric_description_id_'+hide_fabric_description+'_'+i+'*artworkno_'+hide_fabric_description+'_'+i+'*color_size_table_id_'+hide_fabric_description+'_'+i+'*gmts_color_id_'+hide_fabric_description+'_'+i+'*item_color_id_'+hide_fabric_description+'_'+i+'*uom_'+hide_fabric_description+'_'+i+'*txt_woqnty_'+hide_fabric_description+'_'+i+'*txt_rate_'+hide_fabric_description+'_'+i+'*txt_amount_'+hide_fabric_description+'_'+i+'*txt_paln_cut_'+hide_fabric_description+'_'+i+'*updateid_'+hide_fabric_description+'_'+i+'*startdate_'+hide_fabric_description+'_'+i+'*enddate_'+hide_fabric_description+'_'+i+'*subcon_supplier_compo_'+hide_fabric_description+'_'+i+'*subcon_supplier_rateid_'+hide_fabric_description+'_'+i+'*txt_job_no_'+hide_fabric_description+'_'+i+'*txt_program_nos_'+hide_fabric_description+'_'+i+'*txt_mcdia_'+hide_fabric_description+'_'+i+'*item_size_'+hide_fabric_description+'_'+i+'*txt_slength_'+hide_fabric_description+'_'+i+'*txt_ycount_'+hide_fabric_description+'_'+i+'*txt_lot_'+hide_fabric_description+'_'+i+'*txt_brand_'+hide_fabric_description+'_'+i+'*txt_findia_'+hide_fabric_description+'_'+i+'*txt_fingsm_'+hide_fabric_description+'_'+i+'*txt_remark_dtls_'+hide_fabric_description+'_'+i,"../../",i);	
		//alert(data_all);
	}
	
		
	data_all=data_all+get_submitted_data_string('cbo_process*cbo_colorsizesensitive*txt_booking_no*cbo_dia*txt_all_update_id*update_id',"../../");
	
	var data="action=save_update_delete_dtls&operation="+operation+data_all+'&hide_fabric_description='+hide_fabric_description+'&row_num='+row_num+'&program_no='+program_no;
	//alert(data_all);
	freeze_window(operation);
	http.open("POST","requires/service_booking_multi_job_wise_knitting_controller.php",true);
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
			$("#txt_program_no").val('');
			//$("#txt_order_no_id").val('');
			$("#cbo_colorsizesensitive").removeAttr("disabled","disabled");
			$("#cbo_fabric_description").removeAttr("disabled","disabled");
		 	set_button_status(0, permission, 'fnc_service_booking_dtls',2);
			show_list_view(reponse[2]+'**'+reponse[2], 'fabric_detls_list_view','data_panel','requires/service_booking_multi_job_wise_knitting_controller','$(\'#hide_fabric_description\').val(\'\')');
		 }
		 if(trim(reponse[0])=='approved'){
			alert("This booking is approved")
		    release_freezing();
		    return;
		}
		
		 release_freezing();
		 
		// get_php_form_data(reponse[1], "populate_data_from_search_popup", "requires/service_booking_multi_job_wise_knitting_controller" );
	     
		
	}
}
 

function update_booking_data(data)
{
	var data=data.split("_");
	$("#booking_list_view").text('');
	
	$("#hide_fabric_description").val(data[2]);
	$("#cbo_colorsizesensitive").val(data[4]);
	$("#txt_order_no_id").val(data[5]);
	$("#txt_order_no").val(data[9]);
	$("#cbo_colorsizesensitive").attr("disabled",true);
	$("#cbo_fabric_description").attr("disabled",true);
	$("#txt_all_update_id").val(data[0]);
	$("#cbo_dia").val(data[7]);
    $("#txt_program_no").val(data[8]);
	var company= $("#cbo_company_name").val();
   // alert(company);
	load_drop_down( 'requires/service_booking_multi_job_wise_knitting_controller', data[1], 'load_drop_down_fabric_description', 'fabric_description_td' );
	$("#cbo_fabric_description").val(data[2]);
	$("#txt_conv_id").val(data[2]);
	load_drop_down( 'requires/service_booking_multi_job_wise_knitting_controller', data[2], 'load_drop_down_dia', 'dia_td');
	show_list_view(data[1]+'**'+0+'**'+data[2]+'**'+data[3]+'**'+data[4]+'**'+data[5]+'**'+data[6]+'**'+data[0]+'**'+document.getElementById('service_rate_from').value+'**'+data[8]+'**'+company+'**'+document.getElementById('cbo_short_type').value, 'update_detail_booking_list_view','booking_list_view','requires/service_booking_multi_job_wise_knitting_controller','');
    set_button_status(1, permission, 'fnc_service_booking_dtls',2);//cbo_fabric_description
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
		http.open("POST","requires/service_booking_multi_job_wise_knitting_controller.php",true);
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
	//
	//var cbo_process=$("#cbo_process").val();
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		var show_comments='';
		var show_buyer='';
		if(action=='show_trim_booking_report')
		{
			var r=confirm("Press  \"Ok\"  to Hide  Comments\nPress  \"Cancel\"  to Show Comments");
			//alert(r)
			if (r==true)
			{
				show_comments="1";
			}
			else
			{
				show_comments="0";
			} 
		}
		if(action=='show_service_booking_report2' || action=='show_service_booking_report3' || action=='show_service_booking_report4' || action=='show_service_booking_report5' || action=='show_service_booking_report6')
		{
			var r=confirm("Press  \"Ok\"  to Show  Rate And Amount\nPress  \"Cancel\"  to Hide Rate And Amount");
			//alert(r)
			if (r==true)
			{
				show_comments="1";
			}
			else
			{
				show_comments="0";
			} 
		}
		
		
		if(action == "show_trim_booking_report3")
		{
			show_comments="1";
		}
		
		var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name',"../../")+'&show_comments='+show_comments+'&show_buyer='+1;
		http.open("POST","requires/service_booking_multi_job_wise_knitting_controller.php",true);
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
	
	//$("#Refresh2").attr('disabled','disabled');
	var response=return_global_ajax_value( cbo_currercy+"**"+booking_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/service_booking_multi_job_wise_knitting_controller');
	var response=response.split("_");
	$('#txt_exchange_rate').val(response[1]);
}

function service_supplier_popup(id)
{
	var cbo_company_name = $('#cbo_company_name').val();
	var cbo_supplier_name = $('#cbo_supplier_name').val();
	if (form_validation('cbo_company_name*cbo_supplier_name*txt_exchange_rate','Company Name*cbo_supplier_name*Conversion Rate')==false)
	{
		return;
	}
	hidden_supplier_rate_id=$('#subcon_supplier_rateid_'+id).val();
	var title="Supplier Work Order Rate Info";
	var page_link = 'requires/service_booking_multi_job_wise_knitting_controller.php?cbo_company_name='+cbo_company_name+'&cbo_supplier_name='+$("#cbo_supplier_name").val()+'&txt_exchange_rate='+$("#txt_exchange_rate").val()+'&hidden_supplier_rate_id='+hidden_supplier_rate_id+'&action=Supplier_workorder_popup';
	  
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
    
   
	if (form_validation('txt_order_no*cbo_colorsizesensitive','Order No*Sensitivity')==false)
	{
		return;
	}
    else
    {
        var orderNo = document.getElementById('txt_order_no').value;         
       // var jobNo = document.getElementById('txt_job_no').value; 
        var supplier_id = document.getElementById('cbo_supplier_name').value; 
        var orderId = document.getElementById('txt_order_no_id').value;
		var convId = document.getElementById('txt_conv_id').value; 
		var prog_no = document.getElementById('txt_program_nos_'+convId+'_1').value; 

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link+"&orderNo="+orderNo+"&supplier_id="+supplier_id+"&order_id="+orderId+"&pay_mode="+pay_mode+"&convId="+convId+"&prog_no="+prog_no, title, 'width=800px,height=450px,center=1,resize=1,scrolling=0','../')
        emailwindow.onclose=function()
        {           
            var theform = this.contentDoc.forms[0];
         	 var prog_ids=this.contentDoc.getElementById("txt_selected_id").value; 
			 var conv_fab_ids=this.contentDoc.getElementById("txt_selected_name").value;
           // var data = theemail.split("_");
			//alert(conv_fab_ids);
           if (prog_ids!="")
            {
                get_php_form_data( prog_ids, "populate_data_from_program_popup", "requires/service_booking_multi_job_wise_knitting_controller" );
                set_button_status(1, permission, 'fnc_trims_booking',1);

				//document.getElementById('cbo_fabric_description').value=conv_fab_ids;
				document.getElementById('txt_program_no').value=prog_ids;
				document.getElementById('txt_program_nos_'+convId+'_1').value=prog_ids;

				load_drop_down( 'requires/service_booking_multi_job_wise_knitting_controller',conv_fab_ids, 'load_drop_down_dia', 'dia_td');
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
		$("#print_booking1").hide();	 
		$("#print_booking2").hide();	
		$("#print_booking3").hide();	
		$("#print_booking4").hide();
		$("#print_booking5").hide(); 
		$("#print_booking6").hide(); 
	}
	else
	{
		$("#print_booking").show();	 
		$("#print_booking1").show();	 
		$("#print_booking2").show();
		$("#print_booking3").show();
		$("#print_booking4").hide();	 
		$("#print_booking5").hide();
		$("#print_booking5").hide();
	}
	
	var report_id=report_ids.split(",");
	for (var k=0; k<report_id.length; k++)
	{
		if(report_id[k]==13)
		{
			$("#print_booking").show();	 
		}
		if(report_id[k]==15)
		{
			$("#print_booking2").show();	 
		}
		if(report_id[k]==16)
		{
			$("#print_booking3").show();	 
		}
		if(report_id[k]==177)
		{
			$("#print_booking4").show();	 
		}
		if(report_id[k]==175)
		{
			$("#print_booking5").show();	 
		}
		if(report_id[k]==176)
		{
			$("#print_booking6").show();	 
		}
	}
}
function fnResetForm()
{
	//reset_form('servicebookingknitting_1','','','','','');
	//alert(33);
	reset_form('servicebookingknitting_2','','','','','cbo_process*cbo_colorsizesensitive');
}

</script>

</head>

<body onLoad="set_hotkey(); check_exchange_rate();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
	<table  width="900" cellspacing="2" cellpadding="0" border="0" style="">
	<tr>
	<td>
    <form name="servicebooking_1"  autocomplete="off" id="servicebooking_1">
        <fieldset style="width:1000px;">
        <legend>Service Booking</legend>
            <table  width="1000" cellspacing="2" cellpadding="0" border="0" style="">
                <tr>
                    <td colspan="4"align="right" class="must_entry_caption">Booking No </td>              <!-- 11-00030  -->
                    <td colspan="4">
                    	<input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/service_booking_multi_job_wise_knitting_controller.php?action=service_booking_popup','Service Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
                    </td>
                </tr>
                <tr>
                    <td width="110" class="must_entry_caption">Booking Month</td>   
                    <td width="140"> 
						<? 
                        echo create_drop_down( "cbo_booking_month", 80, $months,"", 1, "-- Select --", "", "",0 );		
                        echo create_drop_down( "cbo_booking_year", 50, $year,"", 1, "-- Select --", date('Y'), "",0 );		
                        ?>
                    </td>
                    <td width="110" class="must_entry_caption">Company Name</td>
                    <td width="140"><?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/service_booking_multi_job_wise_knitting_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data( this.value, 'print_report_button', 'requires/service_booking_multi_job_wise_knitting_controller');check_exchange_rate();","","" ); ?>	  
                    </td>
					<td width="110">Buyer Name</td>   
                    <td width="140" id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td>
                    <td width="110">Booking Date</td>
                    <td><input class="datepicker" type="text" style="width:120px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" disabled /></td>
                </tr>
                <tr>
                    <td>Currency</td>
                    <td><?=create_drop_down( "cbo_currency", 130, $currency,"", 1, "-- Select --", 2, "set_conversion_rate(this.value, $('#txt_booking_date').val(), '../../', 'txt_exchange_rate')",0 ); ?></td>
                    <td>Conversion Rate</td>
                    <td><input style="width:120px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate"  readonly /> </td>
                    <td>Delivery Date</td>
                    <td><input class="datepicker" type="text" style="width:120px" name="txt_delivery_date" id="txt_delivery_date"/></td>
                    <td class="must_entry_caption">Pay Mode</td>
                    <td><?=create_drop_down( "cbo_pay_mode", 130, $pay_mode,"", 1, "-- Select Pay Mode --", "", "load_drop_down( 'requires/service_booking_multi_job_wise_knitting_controller', this.value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_supplier', 'supplier_td' )","" ); ?></td>
                </tr>
                <tr>
                    <td>Source</td>              <!-- 11-00030  -->
                    <td> <? echo create_drop_down( "cbo_source", 130, $source,"", 1, "-- Select Source --", "", "","" ); ?></td>
                    <td>Supplier/Party Name</td>
                    <td id="supplier_td"><?=create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/service_booking_multi_job_wise_knitting_controller');",0 ); ?></td> 
                    <td>Tenor</td>
                    <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td> 
                    <td>Delivery  To</td>                   
                    <td ><input style="width:120px;" type="text" class="text_boxes" name="txt_delivery_to" id="txt_delivery_to" /></td>
                </tr>
                <tr>
                    <td>Attention</td>   
                    <td colspan="3">
                    	<input class="text_boxes" type="text" style="width:370px;"  name="txt_attention" id="txt_attention"/>
                    	<input type="hidden" class="image_uploader" style="width:162px" value="Lab DIP No" onClick="openmypage( 'requires/service_booking_multi_job_wise_knitting_controller.php?action=lapdip_no_popup', 'Lapdip No', 'lapdip');">
                    </td>
                    <td>Remark</td>
                    <td colspan="3"><input class="text_boxes" type="text" style="width:370px;"  name="txt_remark" id="txt_remark"/></td>
                </tr>
				<tr>
                    <td>Ready To Approved</td>  
                    <td><?=create_drop_down( "cbo_ready_to_approved", 130, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>
					<td>Booking Type</td>  
					<?
					$bookingTypeArr=array(10=>"Short");
					?>
                    <td><?=create_drop_down( "cbo_short_type", 130, $bookingTypeArr,"", 1, "-- Select --", 1, "","" ); ?></td>
                    <td>&nbsp;</td>
                    <td valign="middle">
                    	<input type="button" class="image_uploader" style="width:120px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('txt_booking_no').value,'', 'service_booking', 0 ,1)">
                    </td>
                    <td>&nbsp;</td>
                    <td><? 
                        include("../../terms_condition/terms_condition.php");
                        terms_condition(228,'txt_booking_no','../../');
                        ?>                    
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" colspan="8" valign="top" id="booking_list_view1"></td>
                </tr>
                 <tr>
                    <td align="center" colspan="8" valign="top" id="app_status" style="font-size:18px; color:#F00"></td>
                </tr>
                <tr>
                    <td align="center" colspan="8" valign="middle" class="button_container">
						<input class="text_boxes" type="hidden" style="width:272px;"  name="update_id" id="update_id"/>
                    	<? echo load_submit_buttons( $permission, "fnc_trims_booking", 0,0 ,"reset_form('servicebooking_1','','','','','cbo_process')",1) ; ?>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="8">
                        <div id="pdf_file_name"></div>
                        <input type="button" value="Print Booking" onClick="generate_trim_report('show_service_booking_report')"  style="width:100px;display:none;" name="print_booking" id="print_booking" class="formbutton"/> &nbsp;
						<input type="button" value="Print Booking 2" onClick="generate_trim_report('show_service_booking_report2')"  style="width:105px;display:none;" name="print_booking2" id="print_booking2" class="formbutton"/>
						<input type="button" value="Print Booking 3" onClick="generate_trim_report('show_service_booking_report3')"   name="print_booking3" id="print_booking3" style="width:105px;display:none;" class="formbutton"/>
						<input type="button" value="Print Booking 4" onClick="generate_trim_report('show_service_booking_report4')"   name="print_booking4" id="print_booking4" style="width:105px;display:none;" class="formbutton"/>
						<input type="button" value="Print Booking 5" onClick="generate_trim_report('show_service_booking_report5')"   name="print_booking5" id="print_booking5" style="width:105px;display:none;" class="formbutton"/>
						<input type="button" value="Print Booking 6" onClick="generate_trim_report('show_service_booking_report6')"   name="print_booking6" id="print_booking6" style="width:105px;display:none;" class="formbutton"/>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
	   </td>
         </tr>
	  </table>
    <br/>
    <form name="servicebookingknitting_2"  autocomplete="off" id="servicebookingknitting_2">   
        <fieldset style="width:1200px;">
        <legend align="center">Service Booking Detail&nbsp;<b style=" margin-left:270px;"> Selected Order No &nbsp;&nbsp;<input class="text_boxes" type="text" style="width:200px;" placeholder="Double click for Order"  onDblClick="openmypage_order('requires/service_booking_multi_job_wise_knitting_controller.php?action=order_search_popup','Order Search')"   name="txt_order_no" id="txt_order_no"/>
                    	<input class="text_boxes" type="hidden" style="width:272px;"  name="txt_order_no_id" id="txt_order_no_id"/>
                        <input class="text_boxes" type="hidden" style="width:272px;"  name="txt_conv_id" id="txt_conv_id"/>
                        <input class="text_boxes" type="hidden"   name="service_rate_from" id="service_rate_from" value=""/></b></legend>
            <table  width="900" cellspacing="2" cellpadding="0" border="0">
			   
                    <td colspan="6" align="center">
                    	
             </td>   
                <tr>
                    <td width="100">Process</td>
                    <td id="process_td">
                    	<? echo create_drop_down( "cbo_process", 172, $conversion_cost_head_array,"", 1, "-- Select --", 1, "",1 ); ?>
                    </td>
                    <td width="100">Sensitivity</td>
                    <td>
                    	<? 
						//set_process(document.getElementById('cbo_fabric_description').value, 'colorsizesensitive')
						echo create_drop_down( "cbo_colorsizesensitive", 172, $size_color_sensitive,"", 1, "--Select--", "1", "",$disabled,"1,3" ); ?>
                    </td>
                    <td align="left">Program No</td>
                    <td>
                        <input class="text_boxes" type="text" style="width:80%;" placeholder="Double click for Program No"  onDblClick="openmypage_programs('requires/service_booking_multi_job_wise_knitting_controller.php?action=programs_search_popup','Programs Search')"   name="txt_program_no" id="txt_program_no" readonly />                        
                    </td>   
                </tr>
                <tr>
                	<td align="center" colspan="6" valign="top" id="booking_list_view1"></td>
                </tr>
                <tr>
                    <td>Fabric Description</td>
                    <td id="fabric_description_td" colspan="3">
                    	<? echo create_drop_down( "cbo_fabric_description",448, $blank_array,"", 1, "-- Select --", $selected, "",0 ); ?> 
                    </td> 
                    <td align="left" style="display:none">Dia</td>
                    <td id="dia_td" style="display:none">
                    	<? echo create_drop_down( "cbo_dia",80, $blank_array,"", 1, "-Select-", $selected, "",0 ); ?> 
                    </td>
                </tr>
                <tr>
                    <td width="130" align="left"><b>Copy</b> :<input type="checkbox" id="copy_val" name="copy_val"/> 
                        <input type="hidden" name="hide_fabric_description"   id="hide_fabric_description" value="">
                        <input type="hidden" name="txt_all_update_id"   id="txt_all_update_id" value="">
						<input type="hidden" name="txt_amount_vali_id"   id="txt_amount_vali_id" value="">
                    </td>              <!-- 11-00030  -->
                    <td width="170"></td>                   
                </tr>
                <tr>
                    <td align="center" colspan="6" valign="middle" class="button_container">
                    	<? echo load_submit_buttons( $permission, "fnc_service_booking_dtls", 0,0 ,"fnResetForm();",2) ; ?>
                    </td>
                </tr>
            </table>
            <div id="booking_list_view"></div>
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