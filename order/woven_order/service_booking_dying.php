<?
/*-------------------------------------------- Comments 
Version          : V1
Purpose			 : This form will create Service Booking
Functionality	 :	
JS Functions	 :
Created by		 : Ashraful 
Creation date 	 : 27-02-2015
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
$selected_dyeing_process_id="25,26,31,32,33,34,38,39,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,88,89,90,91,92,93,94,100,128,29,132,133,135,136,137,138,139,140,141,142,143, 144,145,146,147,148,149,150,151,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,180,181,182,183,184,185,186,187,189,190,191,192,193, 194,195,196,197,198,199,200,201,203,205,209,210,211,212,263,219,221,224,225,227,231,232,233,234,238,239,242,245,257,258,259,260,261,265,266,267,276,281,287,298,299,300,303,304,305,306,309, 310,311,312,313,314,315,316,317,318,319,320,321,322,323,324,325,326,327,328,329,330,331,332,333,335,336,337,338,339,340,341,342,343,344,345,346,347,348,349,350,351,352,353,354,355, 356,357,358,359,360,361,362,363,364,365,366,367,368,369,370,371,372,373,374,375,376,377,378,379,380,381,382,383,385,386,387,388,390,391,394,395,396,397,398,399,400,401,402,403, 404,405,412,413,414,415,416,417,418,419,420,421,422,423,424,425,427,428,429,430,431,432,433,434,435,436,437,438,439,440,441,442,443,476,481,289";
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Woven Service Booking", "../../", 1, 1,$unicode,'','');
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 

var permission='<? echo $permission; ?>';
function openmypage_order(page_link,title)
{
	if (form_validation('cbo_booking_month*cbo_booking_year*cbo_pay_mode','Booking Month*Booking Year*Fabric Nature*Fabric Source*Pay Mode')==false)
	{
		return;
	}	
	else
	{
		var cbo_short_type=document.getElementById('cbo_short_type').value;
		page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_booking_month*cbo_booking_year*txt_booking_date*txt_fab_booking*cbo_short_type','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1300px,height=470px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];;
			var id=this.contentDoc.getElementById("po_number_id");
			var po=this.contentDoc.getElementById("po_number");
			var job_no=this.contentDoc.getElementById("job_no");
			if (id.value!="")
			{
				reset_form('','booking_list_view','txt_order_no_id*txt_order_no*cbo_currency*txt_exchange_rate*txt_booking_date*cbo_supplier_name*txt_attention*txt_tenor*txt_delivery_date*cbo_source*txt_booking_no','txt_booking_date,<? echo date("d-m-Y"); ?>');
				freeze_window(5);
				document.getElementById('txt_order_no_id').value=id.value;
				document.getElementById('txt_order_no').value=po.value;
				document.getElementById('txt_job_no').value=job_no.value;
				get_php_form_data( id.value+'_'+cbo_short_type, "populate_order_data_from_search_popup", "requires/service_booking_dyeing_controller" );
				release_freezing();
	
			}
		}
	}
}


function set_process(fabric_desription_id,type)
{
	
	$("#booking_list_view").text('');
	fabric_desription_id=$("#cbo_fabric_description").val();
	if(type=='set_process')
	{
		
		
	show_list_view(document.getElementById('txt_job_no').value+'**'+1+'**'+fabric_desription_id+'**'+document.getElementById('cbo_process').value+'**'+document.getElementById('cbo_colorsizesensitive').value+'**'+document.getElementById('txt_order_no_id').value+'**'+document.getElementById('txt_booking_no').value+'****'+document.getElementById('service_rate_from').value+'**'+document.getElementById('cbo_short_type').value, 'lapdip_approval_list_view_edit','booking_list_view','requires/service_booking_dyeing_controller','$(\'#hide_fabric_description\').val(\'\');setFilterGrid(\'table_search\',-1)');//+document.getElementById('cbo_short_type').value
	}
	if(type=="colorsizesensitive")
	{
	show_list_view(document.getElementById('txt_job_no').value+'**'+1+'**'+fabric_desription_id+'**'+document.getElementById('cbo_process').value+'**'+document.getElementById('cbo_colorsizesensitive').value+'**'+document.getElementById('txt_order_no_id').value+'**'+document.getElementById('txt_booking_no').value+'****'+document.getElementById('service_rate_from').value+'**'+document.getElementById('cbo_short_type').value, 'lapdip_approval_list_view_edit','booking_list_view','requires/service_booking_dyeing_controller','$(\'#hide_fabric_description\').val(\'\');setFilterGrid(\'table_search\',-1)');
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
	var txt_rate=(document.getElementById('txt_rate_'+param1+'_'+param2).value)*1;
	var service_rate_from = $('#service_rate_from').val()*1;
	var pre_cost_rate=$('#txt_rate_'+param1+'_'+param2).attr('pre-cost-rate')*1;
	//console.log(service_rate_from+'--'+pre_cost_rate+'--'+txt_rate);
	if(service_rate_from==2) //No
	{
		if(txt_rate>pre_cost_rate)
		{
			alert("Rate can't greater then budget");
			$('#txt_rate_'+param1+'_'+param2).val('')
			return;
		}	
	}
	
	document.getElementById('txt_amount_'+param1+'_'+param2).value=txt_amount;	

}
function calculate_fin_req(param1,param2)
{
	var txt_woqnty=(document.getElementById('txt_woqnty_'+param1+'_'+param2).value)*1;
	var prodess_loss=(document.getElementById('txt_prodess_loss_'+param1+'_'+param2).value)*1;
	var req_fin_qty=txt_woqnty-((txt_woqnty*prodess_loss)/100);
				 
  document.getElementById('txt_prodess_loss_'+param1+'_'+param2).value=prodess_loss;	
  document.getElementById('txt_req_fin_woqnty_'+param1+'_'+param2).value=number_format_common(req_fin_qty,5);
	if(prodess_loss==0 || prodess_loss=='')
	{
	 document.getElementById('txt_req_fin_woqnty_'+param1+'_'+param2).value=0;
	}

}

function copy_value(param1,param2,type)
{
	 var copy_val=document.getElementById('copy_val').checked;
	 var rowCount=$('#table_'+param1+' tbody tr').length;
	 if(copy_val==true)
	  {
		 var tot_wo_qnty=0;var tot_amount=0;var tot_finFabQnty=0;
		  for(var j=param2; j<=rowCount; j++)
		  {
			  if(type=='txt_rate')
			  {
				  var txt_woqnty=(document.getElementById('txt_woqnty_'+param1+'_'+j).value)*1;
	              var txt_rate=(document.getElementById('txt_rate_'+param1+'_'+param2).value)*1;
                  var txt_amount=txt_woqnty*txt_rate;	
				  document.getElementById('txt_rate_'+param1+'_'+j).value=txt_rate;
				  document.getElementById('txt_amount_'+param1+'_'+j).value=txt_amount;	
			  }
			  
			  else if(type=='txt_woqnty')
			  {
				  var txt_woqnty=(document.getElementById('txt_woqnty_'+param1+'_'+param2).value)*1;
	              var txt_rate=(document.getElementById('txt_rate_'+param1+'_'+j).value)*1;
                  var txt_amount=txt_woqnty*txt_rate;
				  console.log(`${param1} ==>  ${j}`);	
				  document.getElementById('txt_woqnty_'+param1+'_'+j).value=txt_woqnty;
				  document.getElementById('txt_amount_'+param1+'_'+j).value=txt_amount;	
			  }
			  else if(type=='txt_prodess_loss_')
			  {
				  var prodess_loss=(document.getElementById('txt_prodess_loss_'+param1+'_'+param2).value)*1;
				  var txt_woqnty=(document.getElementById('txt_woqnty_'+param1+'_'+j).value)*1;
				   
                  var req_fin_qty=txt_woqnty-((txt_woqnty*prodess_loss)/100);
				 
				  document.getElementById('txt_prodess_loss_'+param1+'_'+j).value=prodess_loss;	
				  document.getElementById('txt_req_fin_woqnty_'+param1+'_'+j).value=number_format_common(req_fin_qty,5);
				    if(prodess_loss==0 || prodess_loss=='')
					{
				 	 document.getElementById('txt_req_fin_woqnty_'+param1+'_'+j).value=0;
					}
			  
				
			  }
			  else if(type=='uom')
			  {
				  var uom=(document.getElementById('uom_'+param1+'_'+param2).value)*1;
				  document.getElementById('uom_'+param1+'_'+j).value=uom;
			  }
			  else if(type=='composition')
			  {
				  var composition=(document.getElementById('subcon_supplier_compo_'+param1+'_'+param2).value);
				  var supplier_rate_id=(document.getElementById('subcon_supplier_rateid_'+param1+'_'+param2).value);
				  document.getElementById('subcon_supplier_compo_'+param1+'_'+j).value=composition;
				  document.getElementById('subcon_supplier_rateid_'+param1+'_'+j).value=supplier_rate_id;
			  }
			  else
			  {
				  var doc_value=document.getElementById(type+param1+'_'+param2).value;
				  document.getElementById(type+param1+'_'+j).value=doc_value;
			  }

			  $('#txt_woqnty_'+param1+'_'+j).each(function() { tot_wo_qnty += parseInt($(this).val());});
			  $('#txt_amount_'+param1+'_'+j).each(function() { tot_amount += parseInt($(this).val());});
			  $('#txt_req_fin_woqnty_'+param1+'_'+j).each(function() { tot_finFabQnty += parseInt($(this).val());});
		    
		  }
		  		$("#tot_wo_qnty").val(tot_wo_qnty);
				$("#tot_amount").val(tot_amount);
				$("#tot_finFabQnty").val(tot_finFabQnty);
		 
	  }else{


		  var tot_wo_qnty=0;var tot_amount=0;var tot_finFabQnty=0;
		  for(var j=1; j<=rowCount; j++)
		  {
		     $('#txt_woqnty_'+param1+'_'+j).each(function() { tot_wo_qnty += parseInt($(this).val());});
			  $('#txt_amount_'+param1+'_'+j).each(function() { tot_amount += parseInt($(this).val());});
			  $('#txt_req_fin_woqnty_'+param1+'_'+j).each(function() { tot_finFabQnty += parseInt($(this).val());});

		  }
		        $("#tot_wo_qnty").val(tot_wo_qnty);
				$("#tot_amount").val(tot_amount);
				$("#tot_finFabQnty").val(tot_finFabQnty);
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
		http.open("POST","requires/service_booking_dyeing_controller.php",true);
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
		http.open("POST","requires/service_booking_dyeing_controller.php",true);
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
		 	get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/service_booking_dyeing_controller" );
	   		set_button_status(1, permission, 'fnc_trims_booking',1);
		    show_list_view(document.getElementById('txt_job_no').value+'**'+document.getElementById('txt_booking_no').value, 'fabric_detls_list_view','data_panel','requires/service_booking_dyeing_controller','');
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
{ //id="txt_order_no"
	var data_all="";
	if (form_validation('cbo_booking_month*cbo_company_name*txt_order_no*cbo_supplier_name*cbo_pay_mode','Booking Month*Company Name*Order No*Pay Mode')==false)
	{
		return;
	}
	else
	{
	data_all=data_all+get_submitted_data_string('txt_booking_no*cbo_booking_month*cbo_booking_year*cbo_company_name*cbo_supplier_name*cbo_currency*txt_exchange_rate*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_source*txt_attention*txt_tenor*cbo_buyer_name*txt_job_no*txt_order_no_id*cbo_process*cbo_colorsizesensitive*cbo_material*txt_fab_booking*cbo_ready_to_approved*cbo_short_type',"../../");
	}
	var is_approved=$('#id_approved_id').val();//Chech The Approval item.. Change not allowed

	if(is_approved==1 || is_approved==3)
	{
		alert("This Order is Approved. So Change Not Allowed");
		release_freezing();
		return;
	}
	var hide_fabric_description=$('#hide_fabric_description').val();
	var data="action=save_update_delete&operation="+operation+data_all+'&hide_fabric_description='+hide_fabric_description;
	freeze_window(operation);
	http.open("POST","requires/service_booking_dyeing_controller.php",true);
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
			$("#txt_fab_booking").removeAttr("disabled","disabled");
		 	set_button_status(1, permission, 'fnc_trims_booking',1);
		 }
		 if(reponse[0]==2)
		 {
			set_button_status(0, permission, 'fnc_trims_booking',1);
			reset_form('','','txt_booking_no*txt_order_no*cbo_company_name*txt_order_no_id*txt_job_no*cbo_buyer_name*cbo_currency*txt_exchange_rate*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_source*cbo_supplier_name*txt_attention*txt_tenor','txt_booking_date,<? echo date("d-m-Y"); ?>'); 
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
	var txt_job_no = $('#txt_job_no').val();
	var txt_booking_no = $('#txt_booking_no').val();
	var cbo_process = $('#cbo_process').val();
	var txt_order_no_id = $('#txt_order_no_id').val();
	
	var data_all="";
	
	var hide_fabric_description=$('#hide_fabric_description').val();
	var row_num=$('#table_'+hide_fabric_description+' tbody tr').length-1;
	
	//var txt_prev_wo_qnty=($('#txt_prev_wo_qnty_'+hide_fabric_description).val())*1;
	
	/*if(operation!=2)
	{
	var response=return_global_ajax_value( hide_fabric_description+"**"+txt_job_no+"**"+txt_booking_no+"**"+cbo_process+"**"+txt_order_no_id, 'check_fabric_process_data', '', 'requires/service_booking_dyeing_controller');
	var conv_arr=rtrim(response);
	var conv_data=conv_arr.split("_");
	var req_qty= conv_data[0]*1;
	}*/
		
		
	var tot_req_qnty=0;var tot_wo_woqnty=0;
	for (var i=1; i<=row_num; i++)
	{
	
	var txt_woqnty= (document.getElementById('txt_woqnty_'+hide_fabric_description+'_'+i).value)*1;
	var txt_reqqty= (document.getElementById('txt_reqwoqnty_'+hide_fabric_description+'_'+i).value)*1;
	var txt_prev_woqnty= (document.getElementById('txt_prev_woqnty_'+hide_fabric_description+'_'+i).value)*1;
	
	if(operation!=2) //24989 issue for FP
	{
		if(operation==0)
		{
			var total_curr_wo_woqnty=txt_prev_woqnty+txt_woqnty;
		}
		else
		{
			var total_curr_wo_woqnty=txt_prev_woqnty+txt_woqnty;
		}
		if(txt_woqnty>0)
		{
			//alert(total_curr_wo_woqnty+'=='+txt_prev_woqnty+'=='+txt_reqqty+'=='+txt_woqnty);
			if(total_curr_wo_woqnty>txt_reqqty)
			{
					var booking_msg="Exceed qty not allowed.\n Req. Qty : "+txt_reqqty;
					alert(booking_msg);
					return;
			}
		}
	}
	
	// tot_prev_woqnty +=wo_prev_woqnty;
	  //	tot_wo_woqnty +=txt_woqnty;
	 //   tot_req_qnty +=txt_reqqty;
	  
	  /*if (form_validation('txt_woqnty_'+hide_fabric_description+'_'+i,'Wo Qty')==false)
		{
			return;
		}*/
							
	
		data_all+=get_submitted_data_string('po_id_'+hide_fabric_description+'_'+i+'*fabric_description_id_'+hide_fabric_description+'_'+i+'*artworkno_'+hide_fabric_description+'_'+i+'*color_size_table_id_'+hide_fabric_description+'_'+i+'*gmts_color_id_'+hide_fabric_description+'_'+i+'*item_color_id_'+hide_fabric_description+'_'+i+'*gmts_size_id_'+hide_fabric_description+'_'+i+'*item_size_'+hide_fabric_description+'_'+i+'*uom_'+hide_fabric_description+'_'+i+'*txt_woqnty_'+hide_fabric_description+'_'+i+'*txt_rate_'+hide_fabric_description+'_'+i+'*txt_amount_'+hide_fabric_description+'_'+i+'*txt_paln_cut_'+hide_fabric_description+'_'+i+'*updateid_'+hide_fabric_description+'_'+i+'*startdate_'+hide_fabric_description+'_'+i+'*enddate_'+hide_fabric_description+'_'+i+'*item_color_'+hide_fabric_description+'_'+i+'*findia_'+hide_fabric_description+'_'+i+'*fingsm_'+hide_fabric_description+'_'+i+'*labdipno_'+hide_fabric_description+'_'+i+'*subcon_supplier_compo_'+hide_fabric_description+'_'+i+'*subcon_supplier_rateid_'+hide_fabric_description+'_'+i+'*ycount_'+hide_fabric_description+'_'+i+'*ylot_'+hide_fabric_description+'_'+i+'*brand_'+hide_fabric_description+'_'+i+'*mcdia_'+hide_fabric_description+'_'+i+'*txt_prodess_loss_'+hide_fabric_description+'_'+i+'*txt_req_fin_woqnty_'+hide_fabric_description+'_'+i+'*cbo_color_range_'+hide_fabric_description+'_'+i+'*item_shade_per_'+hide_fabric_description+'_'+i+'*cbo_dia_type_'+hide_fabric_description+'_'+i+'*txt_remark_'+hide_fabric_description+'_'+i,"../../",i);	
	}
	//alert(req_qty+'='+txt_prev_wo_qnty);
	
	/*if(operation!=2)
	{
		if(operation==0)
		{
			var total_curr_wo_woqnty=txt_prev_wo_qnty+tot_wo_woqnty;
		}
		else
		{
			var total_curr_wo_woqnty=(txt_prev_wo_qnty+tot_wo_woqnty);
		}
		
		//var check_balance_qnty=total_curr_wo_woqnty-tot_req_qnty;
		alert(total_curr_wo_woqnty+'=='+txt_prev_wo_qnty+'=='+tot_req_qnty+'=='+tot_wo_woqnty);
		if(total_curr_wo_woqnty>txt_reqqty)
		{
			
				var booking_msg="Exceed qty not allowed.\n Req. Qty : "+txt_reqqty;
				alert(booking_msg);
				return;
		}
	}*/
	
	data_all=data_all+get_submitted_data_string('cbo_process*cbo_colorsizesensitive*txt_job_no*txt_booking_no*txt_all_update_id*update_id',"../../");

	var is_approved=$('#id_approved_id').val();//Chech The Approval item.. Change not allowed

	if(is_approved==1 || is_approved==3)
	{
		alert("This Order is Approved. So Change Not Allowed");
		release_freezing();
		return;
	}

	var data="action=save_update_delete_dtls&operation="+operation+data_all+'&hide_fabric_description='+hide_fabric_description+'&row_num='+row_num;
	//alert(data)
	freeze_window(operation);
	http.open("POST","requires/service_booking_dyeing_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_service_booking_dtls_reponse;
}
	 
function fnc_service_booking_dtls_reponse()
{
	if(http.readyState == 4) 
	{
		// alert(http.responseText);
		console.log(http.responseText);
		 var reponse=trim(http.responseText).split('**');
		 show_msg(trim(reponse[0]));
		 if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		 {
		
		 	$('#booking_list_view').text('');
			$("#cbo_colorsizesensitive").val(0);
			$("#cbo_fabric_description").val(0);
			$("#cbo_colorsizesensitive").removeAttr("disabled","disabled");
			$("#cbo_fabric_description").removeAttr("disabled","disabled");
		 	set_button_status(0, permission, 'fnc_service_booking_dtls',2);
		 }
		
		 release_freezing();
		 
		// get_php_form_data(reponse[1], "populate_data_from_search_popup", "requires/service_booking_dyeing_controller" );
		 if(reponse[0]!=10)
		 {
	     show_list_view(reponse[1]+'**'+reponse[2], 'fabric_detls_list_view','data_panel','requires/service_booking_dyeing_controller','$(\'#hide_fabric_description\').val(\'\')');
		 }
		
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
	$("#cbo_process").val(data[3]);
	//alert(data[3]);
	
	
	show_list_view(data[1]+'**'+0+'**'+data[2]+'**'+data[3]+'**'+data[4]+'**'+data[5]+'**'+data[6]+'**'+data[0]+'**'+document.getElementById('service_rate_from').value, 'lapdip_approval_list_view_edit','booking_list_view','requires/service_booking_dyeing_controller','setFilterGrid(\'table_search\',-1)');//setFilterGrid(\'table_search\',-1)
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
		http.open("POST","requires/service_booking_dyeing_controller.php",true);
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
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		
		var show_comments='';
		if(action=='show_trim_booking_report')
		{
			var r=confirm("Press  \"Ok\"  to Show  Rate, Amount,Comment\nPress  \"Cancel\"  to Hide Rate,Amount,Comment");
			//alert(r)
			if (r==true)
			{
				show_comments="0";
			}
			else
			{
				show_comments="1";
			} 
		}
		else if(action=='show_trim_booking_report5')
		{
			var r=confirm("Press  \"Ok\"  to Show  Rate & Amount\nPress  \"Cancel\"  to Hide Rate & Amount");
			//alert(r)
			if (r==true)
			{
				show_comments="0";
			}
			else
			{
				show_comments="1";
			} 
		}
		 
		//cbo_template_id
		var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_job_no*cbo_template_id',"../../")+'&show_comments='+show_comments;
		http.open("POST","requires/service_booking_dyeing_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_trim_report_reponse;
	}	
}

function generate_trim_report_reponse()
{
	if(http.readyState == 4) 
	{
		$('#data_panel2').html( http.responseText );
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
	var response=return_global_ajax_value( cbo_currercy+"**"+booking_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/service_booking_dyeing_controller');
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
	var page_link = 'requires/service_booking_dyeing_controller.php?cbo_company_name='+cbo_company_name+'&cbo_supplier_name='+$("#cbo_supplier_name").val()+'&txt_exchange_rate='+$("#txt_exchange_rate").val()+'&hidden_supplier_rate_id='+hidden_supplier_rate_id+'&action=Supplier_workorder_popup';
	  
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
				$("#txt_fab_booking").val(theemail.value);
				
			}
		}
	}

	function print_button_setting(company)
	{
		// $('#button_data_panel').html('');
		// //alert(company);
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/service_booking_dyeing_controller' );
	}

	function print_report_button_setting(report_ids)
	{
		var report_id=report_ids.split(",");
		$("#print_booking").hide();
		$("#print_booking1").hide();
		$("#print_booking3").hide();
		$("#print_booking4").hide();
		$("#print_booking5").hide();
		$("#print_booking6").hide();
		$("#print_inhouse").hide();
		$("#print_outside").hide();
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==8){
			$("#print_booking").show();
		}else if(report_id[k]==12){
			$("#print_booking1").show();
		}else if(report_id[k]==16){
			$("#print_booking3").show();
		}
		else if(report_id[k]==177){
			$("#print_booking4").show();
		}
		else if(report_id[k]==175){
			$("#print_booking5").show();
		}
		else if(report_id[k]==176){
			$("#print_booking6").show();
		}
		else if(report_id[k]==508){
			$("#print_inhouse").show();
		}
		else if(report_id[k]==172){
			$("#print_outside").show();
		} 
		
	 }
	}
	
	function open_colur_cuff_popup(page_link,title){
		var txt_booking_no=trim(document.getElementById('txt_booking_no').value);
		if(txt_booking_no=="")
		{
			alert('Booking Save First');	
			return;
		}
		var txt_order_no_id=document.getElementById('txt_order_no_id').value
		page_link=page_link+'&txt_booking_no='+txt_booking_no+'&po_id='+txt_order_no_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=600px,height=400px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			/*var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("processloss_breck_down");
			if (theemail.value!=""){
				document.getElementById('processloss_breck_down').value=theemail.value;
			}*/
		}
	}
	function fnc_budget_popup(){
		var txt_booking_no=trim(document.getElementById('txt_booking_no').value);
		if(txt_booking_no=="")
		{
			alert('Booking Save First');	
			return;
		}
	//	var page_link="budget_conversion_info_popup";
		title='Budget Information';
		var txt_order_no_id=document.getElementById('txt_order_no_id').value;
		var txt_job_no=document.getElementById('txt_job_no').value
	//	page_link=page_link+'&txt_booking_no='+txt_booking_no+'&po_id='+txt_order_no_id+'&txt_job_no='+txt_job_no;
	var page_link = 'requires/service_booking_dyeing_controller.php?cbo_company_name='+cbo_company_name+'&txt_order_no_id='+txt_order_no_id+'&txt_job_no='+txt_job_no+'&action=budget_conversion_info_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=710px,height=400px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			/*var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("processloss_breck_down");
			if (theemail.value!=""){
				document.getElementById('processloss_breck_down').value=theemail.value;
			}*/
		}
	}
	 
</script>
 
</head>
 
<body onLoad="set_hotkey();check_exchange_rate();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="servicebooking_1"  autocomplete="off" id="servicebooking_1">
        <fieldset style="width:1000px;">
            <legend>Service Booking</legend>
            <table  width="1000" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td colspan="4" align="right" class="must_entry_caption">Booking No</td>              <!-- 11-00030  -->
                    <td colspan="4">
                    <input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/service_booking_dyeing_controller.php?action=service_booking_popup','Service Booking Search');" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
					<input type="hidden" id="id_approved_id">
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
                    <td width="140"><?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/service_booking_dyeing_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );check_exchange_rate();print_button_setting(this.value);","","" ); ?></td>
                    
                    <td width="110">Buyer Name</td>   
                    <td width="140" id="buyer_td"> 
                    <?=create_drop_down( "cbo_buyer_name", 130, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,"" ); ?> </td>
                    <td width="110">Booking Date</td>
                    <td><input class="datepicker" type="text" style="width:120px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<?=date("d-m-Y")?>" disabled /> 
					
				</td>
                </tr>
                <tr>
                    <td class="must_entry_caption">
						
					Selected Order No
					</td>   
                    <td colspan="3">
					Type: <select style="width:55px;" class="text_boxes" id="cbo_short_type">
						<option value="0">Select-</option>
						<option value="10">Short</option>
					</select>

                        <input class="text_boxes" type="text" style="width:290px;" placeholder="Double click for Order"  onDblClick="openmypage_order('requires/service_booking_dyeing_controller.php?action=order_search_popup','Order Search')"   name="txt_order_no" id="txt_order_no"/>
                        <input class="text_boxes" type="hidden" style="width:72px;"  name="txt_order_no_id" id="txt_order_no_id"/>
                        <input class="text_boxes" type="hidden"   name="service_rate_from" id="service_rate_from" value=""/>
                        <input class="text_boxes" type="hidden"   name="update_id" id="update_id" value=""/>
                    </td> 
                    <td>Job No.</td>
                    <td><input style="width:120px;" type="text" class="text_boxes"  name="txt_job_no" id="txt_job_no" disabled  /></td>
                    <td>Delivery Date</td>
                    <td><input class="datepicker" type="text" style="width:120px" name="txt_delivery_date" id="txt_delivery_date"/>	</td>
                </tr>
                <tr>
                    <td>Currency</td>
                    <td><?=create_drop_down( "cbo_currency", 130, $currency,"", 1, "-- Select --", 2, "check_exchange_rate();",0 ); ?></td>
                    <td>Exchange Rate</td>
                    <td><input style="width:120px;" type="text" class="text_boxes_numeric"  name="txt_exchange_rate" id="txt_exchange_rate"  readonly /></td>
                    <td  class="must_entry_caption">Pay Mode</td>
                    <td><?=create_drop_down( "cbo_pay_mode", 130, $pay_mode,"", 1, "-- Select Pay Mode --", "", "load_drop_down( 'requires/service_booking_dyeing_controller', this.value, 'load_drop_down_supplier', 'supplier_td' )","" ); ?></td>
                    <td> Source </td>              <!-- 11-00030  -->
                    <td><?=create_drop_down( "cbo_source", 130, $source,"", 1, "-- Select Source --", "", "","" ); ?></td>
                </tr>
                <tr>
                    <td>Supplier Name</td>
                    <td id="supplier_td"> <?=create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(21) and a.status_active =1 and a.is_deleted=0 order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/service_booking_dyeing_controller');",0 ); ?></td> 
                    <td>With Material</td>
                    <td>
                        <?
                        $with_material_array=array(1=>'With Material',2=>'Without Material');
                        echo create_drop_down( "cbo_material", 130, $with_material_array,"", 0, "-- Select Material --", 2, "","" );
                        ?> 
                    </td> 
                    <td>Tenor</td>
                	<td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
                    <td>Fabric Booking</td>
                    <td><input class="text_boxes" type="text" style="width:120px;"  name="txt_fab_booking" id="txt_fab_booking" onDblClick="fnc_fab_booking('requires/service_booking_dyeing_controller.php?action=fabric_booking_popup','fabric Booking Search')" placeholder="Browser" readonly/> </td>
                </tr>
                <tr>
                	<td>Attention</td>   
                    <td colspan="3">
                        <input class="text_boxes" type="text" style="width:370px;"  name="txt_attention" id="txt_attention"/>
                        <input type="hidden" class="image_uploader" style="width:62px" value="Lab DIP No" onClick="openmypage('requires/service_booking_dyeing_controller.php?action=lapdip_no_popup','Lapdip No','lapdip')">
                    </td>
                	<td><input type="button" class="image_uploader" style="width:110px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('txt_booking_no').value,'', 'service_booking', 0 ,1)"></td>
                    <td>
						<? 
                        include("../../terms_condition/terms_condition.php");
                        terms_condition(232,'txt_booking_no','../../');
                        ?>                            
                    </td>
                    <td><input type="button" id="set_button3" class="image_uploader" style="width:110px;" value="Collar & Cuff" onClick="open_colur_cuff_popup('requires/service_booking_dyeing_controller.php?action=colur_cuff_popup&permissions=<?=$permission?>','Collar & Cuff')" /></td>

					<td> &nbsp;&nbsp;<a href="#" onClick="fnc_budget_popup()">Budget Info </a> </td>
                </tr>
				<tr>
                    <td>Ready To Approved</td>
                    <td><? echo create_drop_down( "cbo_ready_to_approved", 130, $yes_no,"", 1, "-- Select--", "", "","","" ); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
                </tr>
                <tr>
                	<td align="center" colspan="8" valign="top" id="booking_list_view1"></td>
                </tr>
				<tr>
                    <td align="center" colspan="8" valign="top" id="app_status" style="font-size:18px; color:#F00">
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="8" valign="middle" class="button_container">
					<? echo create_drop_down( "cbo_template_id", 85, $report_template_list,'', 0, '', 0, ""); ?>
						<?=load_submit_buttons( $permission, "fnc_trims_booking", 0,0 ,"",1) ; ?>
                        <input type="hidden" name="update_id" id="update_id" value=""><input type="hidden" id="report_ids" >
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="8">
                        <div id="pdf_file_name"></div>
                        <input type="button" value="Print Booking" onClick="generate_trim_report('show_trim_booking_report')"  style="width:120px; display: none;" name="print_booking" id="print_booking" class="formbutton" />
                        <input type="button" value="Print Booking 1" onClick="generate_trim_report('show_trim_booking_report1')"  style="width:120px; display: none;" name="print_booking1" id="print_booking1" class="formbutton" />                        
                        <input type="button" value="Print Booking 3" onClick="generate_trim_report('show_trim_booking_report3')"  style="width:120px; display: none;" name="print_booking3" id="print_booking3" class="formbutton" />
						<input type="button" value="Print Booking 4" onClick="generate_trim_report('show_trim_booking_report4')"  style="width:120px;display: none; " name="print_booking4" id="print_booking4" class="formbutton" />
						<input type="button" value="Print Booking 5" onClick="generate_trim_report('show_trim_booking_report5')"  style="width:120px;display: none; " name="print_booking5" id="print_booking5" class="formbutton" />
                        <input type="button" value="Print Booking 6" onClick="generate_trim_report('show_trim_booking_report6')"  style="width:120px;display: none; " name="print_booking6" id="print_booking6" class="formbutton" />
						<input type="button" value="Inhouse" onClick="generate_trim_report('show_trim_booking_inhouse')"  style="width:120px;display: none; " name="print_inhouse" id="print_inhouse" class="formbutton" />
						<input type="button" value="Outside" onClick="generate_trim_report('show_trim_booking_outside')"  style="width:120px;display: none; " name="print_outside" id="print_outside" class="formbutton" />
                        </td>
                    </tr>
                    </table>
                    
              </fieldset>
           </form>
                       
              <br/>
           <form name="servicebookingknitting_1"  autocomplete="off" id="servicebookingknitting_1">   
              <fieldset style="width:1200px;">
                <legend>Service Booking</legend>
            		<table  width="900" cellspacing="2" cellpadding="0" border="0">
                    
                        
                        <tr>
                        <td align="" >Process</td>
                        <td align="" id="process_td">
							<?
							
                            echo create_drop_down( "cbo_process", 172, $conversion_cost_head_array,"", 1, "-- Select --", 31, "","",$selected_dyeing_process_id );
                            ?>
                        </td>
                        <td align="">Sensitivity</td>
                        <td align="">
					    <? 
						echo create_drop_down( "cbo_colorsizesensitive", 172, $size_color_sensitive,"", 1, "--Select--", "1", "set_process(document.getElementById('cbo_fabric_description').value,'colorsizesensitive')",$disabled,"" ); 
						?>
                        </td>
                        <td  width="130" height="" align="right"><b>Copy</b> :<input type="checkbox" id="copy_val" name="copy_val"/> 
                         <input type="hidden" name="hide_fabric_description"   id="hide_fabric_description" value="">
                          <input type="hidden" name="txt_all_update_id"   id="txt_all_update_id" value="">
                          </td>              <!-- 11-00030  -->
                        <td  width="170" >
                            
                        </td>
                        </tr>
                        <tr>
                        	<td align="center" colspan="6" valign="top" id="booking_list_view1">
                            	
                            </td>
                        </tr>
                            <tr>
                            <td align="">Fabric Description</td>
                            <td align="" id="fabric_description_td" colspan="5">
                                   <?
                                        echo create_drop_down( "cbo_fabric_description",650, $blank_array,"", 1, "-- Select --", $selected, "",0 );
                                   ?> 
                             </td>
                        </tr>
						
                        <tr>
                        	<td align="center" colspan="6" valign="middle" class="button_container">
                              <? echo load_submit_buttons( $permission, "fnc_service_booking_dtls", 0,0 ,"",2) ; ?>
                            </td>
                        </tr>
                    </table>
                 
              <div id="booking_list_view">
              </div>
           	  <br/>  <br/> 
              <div style="" id="data_panel">
              </div>
              <br/>  <br/>
               <div style="display:none" id="data_panel2">
              </div> 
              </fieldset>
           </form>
              

</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>