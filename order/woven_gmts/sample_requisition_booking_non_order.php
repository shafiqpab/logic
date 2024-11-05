<?
/*-------------------------------------------- Comments
Version                  :   
Purpose			         : 	This form will create Sample Requisition Fabric Booking (Without Order)
Functionality	         :	
JS Functions	         :
Created by		         :	Zakaria Joy
Creation date 	         : 	19-20-2020
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
Comments		         : Copy From Knit Gmts 
Entry form 				 : 439
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Booking Non Order", "../../", 1, 1,$unicode,'','');
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
?>	
<script>
<?
	$data_array = sql_select("SELECT  booking_no from wo_booking_mst");
	$operation_booking_no = array();
	foreach($data_array as $row)
	{
		$operation_booking_no[$row[csf("booking_no")]]= $row[csf("booking_no")];
	}
	$operation_booking_no = json_encode($operation_booking_no);
	echo "var operation_booking_no = ".$operation_booking_no.";\n";
?>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';
<?
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][90] );
echo "var field_level_data= ". $data_arr . ";\n";
?>

	function calculate_requirement(i)
	{
		var cbo_company_name= document.getElementById('cbo_company_name').value;  
		var cbo_fabric_natu= document.getElementById('cbo_fabric_natu').value
		var process_loss_method_id=return_global_ajax_value(cbo_company_name+'_'+cbo_fabric_natu, 'process_loss_method_id', '', 'requires/sample_requisition_booking_with_order_controller');
		var txt_finish_qnty=(document.getElementById('txtRfReqQty_'+i).value)*1;
		var processloss=(document.getElementById('txtProcessLoss_'+i).value)*1;
		var WastageQty='';
		if(process_loss_method_id==1)
		{
			WastageQty=txt_finish_qnty+txt_finish_qnty*(processloss/100);
		}
		else if(process_loss_method_id==2)
		{
			var devided_val = 1-(processloss/100);
			var WastageQty=parseFloat(txt_finish_qnty/devided_val);
		}
		else
		{
			WastageQty=0;
		}
		WastageQty= number_format_common( WastageQty, 5, 0) ;	
		document.getElementById('txtGrayFabric_'+i).value= WastageQty;
		document.getElementById('txtAmount_'+i).value=number_format_common((document.getElementById('txtRate_'+i).value)*1*WastageQty,5,0);
	}

	function calculate_amount(i)
	{
		var grey_qnty=$("#txtGrayFabric_"+i).val()*1;
		var fin_qnty=$("#txtRfReqQty_"+i).val()*1;
		var rate=$("#txtRate_"+i).val()*1;
		if(grey_qnty)
		{
			$("#txtAmount_"+i).val(number_format_common(rate*grey_qnty,5,0));
		}
		else
		{
			$("#txtAmount_"+i).val(number_format_common(rate*fin_qnty,5,0));
		}
	}

	function add_rf_tr(i)
	{ 
		var row_num=$('#tbl_required_fabric tbody tr').length;
		if (row_num!=i)
		{
			return false;
		}

		else
		{ 
			i++;
		   var k=i-1;
			$("#tbl_required_fabric tbody tr:last").clone().find("input,select").each(function(){
			$(this).attr({ 
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
			  'value': function(_, value) { return value }              
			});
			}).end().appendTo("#tbl_required_fabric");
			$('#cboRfSampleName_'+i).val($('#cboRfSampleName_'+k).val());
			$('#cboRfGarmentItem_'+i).val($('#cboRfGarmentItem_'+k).val());
			$('#updateidRequiredDtl_'+i).val('');
			$('#cboRfBodyPart_'+i).val('');
			$('#cboRfFabricNature_'+i).val('');
			$('#txtRfFabricDescription_'+i).val('');
			$('#txtRfGsm_'+i).val('');
			$('#txtRfDia_'+i).val('');
			$('#txtRfColor_'+i).val('');
			$('#cboRfColorType_'+i).val('');
			$('#cboRfWidthDia_'+i).val('');
			$('#cboRfUom_'+i).val($('#cboRfUom_'+k).val());
			$('#txtRfReqDzn_'+i).val('');
			$('#txtRfReqQty_'+i).val('');
			$('#txtRfColorAllData_'+i).val('');	
			$('#txtMemoryDataRf_'+i).val('');			
			
			 
			$('#txtRfReqDzn_'+i).removeAttr("onblur").attr("onblur","calculate_required_qty('1','"+i+"')");
			 $('#cboRfBodyPart_'+i).removeAttr("onchange").attr("onchange","load_data_to_rfcolor('"+i+"')");

			$('#txtRfColor_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_rf_color('requires/sample_requisition_booking_non_order_controller.php?action=color_popup_rf','Color Search','"+i+"');");

			$('#txtRfFabricDescription_'+i).removeAttr("onDblClick").attr("onDblClick","open_fabric_description_popup("+i+")");

			$('#txtRfFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../',document.getElementById('updateidRequiredDtl_"+i+"').value,'', 'required_fabric_1', 0 ,1);");
			 
			$('#increaserf_'+i).removeAttr("value").attr("value","+");
			$('#decreaserf_'+i).removeAttr("value").attr("value","-");
			$('#increaserf_'+i).removeAttr("onclick").attr("onclick","add_rf_tr("+i+");");
			$('#decreaserf_'+i).removeAttr("onclick").attr("onclick","fn_rf_deleteRow("+i+");");
			set_all_onclick();
		}
	}
   
	function fn_rf_deleteRow(rowNo) 
	{ 
		alert("delete is not allowed");
	}
	
	function openmypage_requisition()
	{
		var title = 'Requisition ID Search';
		var company=$("#cbo_company_name").val();
		var buyer=$("#cbo_buyer_name").val();
		if(company==0 || buyer==0)
		{
			alert("Select company and buyer");
			return;
		} 
		var page_link = 'requires/sample_requisition_booking_non_order_controller.php?&action=requisition_id_popup&company='+company+'&buyer='+buyer;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_tbl_id=this.contentDoc.getElementById("selected_job").value;//mst id
			
			if (mst_tbl_id!="")
			{
				freeze_window(5);
				get_php_form_data(mst_tbl_id, "populate_data_from_requisition_search_popup", "requires/sample_requisition_booking_non_order_controller" );

 				$("#cbo_company_name").attr('disabled','disabled');
 				$("#cbo_buyer_name").attr('disabled','disabled');
 				//set_button_status(1, permission, 'fnc_sample_requisition_mst_info',1,0);
  				release_freezing();
				$('#content_sample_details').hide();
				$('#content_required_fabric').hide();
				$('#content_required_accessories').hide();
				$('#content_required_embellishment').hide();
				$("#cbo_sample_stage").attr('disabled','disabled');
			}
		}
	}

	function fnc_load_tr(data,type)  
	{
		//type =1 for requisition browse and type=2 for booking save or browse
		var list_view_tr = return_global_ajax_value( data+'___'+type, 'load_php_dtls_form', '', 'requires/sample_requisition_booking_non_order_controller');
		if(list_view_tr!='')
		{
			$("#required_fabric_container tr").remove();
			$("#required_fabric_container").append(list_view_tr);
			set_all_onclick();
		}
 	}

	function openmypage_booking(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");
			if (theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/sample_requisition_booking_non_order_controller" );
				set_button_status(1, permission, 'fnc_fabric_booking',1);
				print_button_setting();
				check_kniting_charge();
				release_freezing();
			}
		}
	}

	function color_from_library(company_id)
	{
		var color_from_library=return_global_ajax_value(company_id, 'color_from_library', '', 'requires/sample_requisition_booking_non_order_controller');
		if(color_from_library==1)
		{
			$('#txt_gmt_color').attr('readonly',true);
			$('#txt_gmt_color').attr('placeholder','Click');
			$('#txt_gmt_color').attr('onClick',"color_select_popup(document.getElementById('cbo_buyer_name').value,this.id);");
			
			$('#txt_color').attr('readonly',true);
			$('#txt_color').attr('placeholder','Click');
			$('#txt_color').attr('onClick',"color_select_popup(document.getElementById('cbo_buyer_name').value,this.id);");
		}
		else
		{
			$('#txt_gmt_color').attr('readonly',false);
			$('#txt_gmt_color').removeAttr('placeholder','Click');
			$('#txt_gmt_color').removeAttr('onClick',"color_select_popup(document.getElementById('cbo_buyer_name').value,this.id);");
			
			$('#txt_color').attr('readonly',false);
			$('#txt_color').removeAttr('placeholder','Click');
			$('#txt_color').removeAttr('onClick',"color_select_popup(document.getElementById('cbo_buyer_name').value,this.id);");
		}
	}

	function color_select_popup(buyer_name,text_box)
	{
		//var page_link='requires/sample_requisition_booking_non_order_controller.php?action=color_popup'
		//alert(page_link)
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sample_requisition_booking_non_order_controller.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var color_name=this.contentDoc.getElementById("color_name");
			if (color_name.value!="")
			{
				$('#'+text_box).val(color_name.value);
			}
		}
	}

	function calculate_requirement(i)
	{
		var cbo_company_name= document.getElementById('cbo_company_name').value;  
		var cbo_fabric_natu= document.getElementById('cbo_fabric_natu').value
		var process_loss_method_id=return_global_ajax_value(cbo_company_name+'_'+cbo_fabric_natu, 'process_loss_method_id', '', 'requires/sample_requisition_booking_with_order_controller');
		var txt_finish_qnty=(document.getElementById('txtRfReqQty_'+i).value)*1;
		var processloss=(document.getElementById('txtProcessLoss_'+i).value)*1;
		var WastageQty='';
		if(process_loss_method_id==1)
		{
			WastageQty=txt_finish_qnty+txt_finish_qnty*(processloss/100);
		}
		else if(process_loss_method_id==2)
		{
			var devided_val = 1-(processloss/100);
			var WastageQty=parseFloat(txt_finish_qnty/devided_val);
		}
		else
		{
			WastageQty=0;
		}
		WastageQty= number_format_common( WastageQty, 5, 0) ;	
		document.getElementById('txtGrayFabric_'+i).value= WastageQty;
		document.getElementById('txtAmount_'+i).value=number_format_common((document.getElementById('txtRate_'+i).value)*1*WastageQty,5,0);
	}

	function fnc_fabric_booking( operation )
	{
		if(operation==1)
		{
		  var txt_booking_no=$('#txt_booking_no').val();
		  var issue_number=return_global_ajax_value(txt_booking_no, 'booking_no_check', '', 'requires/sample_requisition_booking_non_order_controller');
		  
		 //alert(issue_number);
		  if(trim(issue_number)!='')
		  {
			alert('Source Changing not Allowed .Yarn has already been issued.'+issue_number); 
			
			get_php_form_data( txt_booking_no, "populate_data_from_search_popup", "requires/sample_requisition_booking_non_order_controller" );
			
			return;
		  }
		}
		 
		if(document.getElementById('id_approved_id').value==1)
		{
			alert("This booking is approved")
			return;
		}
		
		if(document.getElementById('is_found_dtls_part').value==0)
		{
			alert("Without detail part save ,ready to approved request not allowed.")
			return;
		}
		
		
		var delivery_date=$('#txt_delivery_date').val();
		if(date_compare($('#txt_booking_date').val(), delivery_date)==false)
		{
			alert("Delivery Date Not Allowed Less than Booking Date");
			return;
		}
	
		if (form_validation('cbo_company_name*cbo_buyer_name*cbo_fabric_natu*txt_booking_date*txt_delivery_date*cbo_pay_mode*txt_requisition','Company Name*Buyer Name*Fabric Nature*Booking Date*Delivery Date*Pay Mode*Requisition')==false)
		{
			return;
		}	
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_booking_no*cbo_fabric_natu*cbo_fabric_source*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_source*cbo_ready_to_approved*cbo_team_leader*cbo_dealing_merchant*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/sample_requisition_booking_non_order_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_fabric_booking_reponse;
		}
	}
	
	function reload_full_page()
	{
		window.location.reload();	
	}
	
	function fnc_fabric_booking_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])==0 || trim(reponse[0])==1 || trim(reponse[0])==2)
			{
				document.getElementById('txt_booking_no').value=reponse[1];
				document.getElementById('update_id').value=reponse[2];
				set_button_status(1, permission, 'fnc_fabric_booking',1);
				show_msg(trim(reponse[0]));
				 
				if(trim(reponse[0])==2)
				{
					 setTimeout('reload_full_page()',2000);				 
				}
			}
			if(trim(reponse[0])=='knit')
			{
				alert("Knitting Production found with this Challan  :"+trim(reponse[1])+"\n So Update/Delete Not Allowed");
				release_freezing();
				return;
			}
	
			if(trim(reponse[0])=='yarn')
			{
				alert("Yarn Issue found with this Challan  :"+trim(reponse[1])+"\n So Update/Delete Not Allowed");
				release_freezing();
				return;
			}
	
	
			if(trim(reponse[0])=='approved')
			{
				alert("This booking is approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='sal1')
			{
				alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='pi1')
			{
				alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			release_freezing();
		}
	}

	function fnc_required_fabric_details_info( operation )
	{
		var booking_no=$('#txt_booking_no').val(); 
		var requisition_id=$('#hidden_requisition_id').val();
		var update_id=$('#update_id').val();
			if(booking_no=="")
		{
			alert("save master part!!");
			return;
		}
		
		else
		{
			var row_nums=$('#tbl_required_fabric tr').length-1;
			var data_all="";
			for (var i=1; i<=row_nums; i++)
			{
				if (form_validation('cboRfSampleName_'+i+'*cboRfGarmentItem_'+i+'*cboRfBodyPart_'+i+'*cboRfFabricNature_'+i+'*txtRfFabricDescription_'+i+'*txtRfGsm_'+i+'*txtRfDia_'+i+'*txtRfColorAllData_'+i+'*cboRfColorType_'+i+'*cboRfWidthDia_'+i+'*cboRfUom_'+i+'*txtRfReqQty_'+i,'Sample Name*Garment Item*Body Part*Fabric Nature*Fabric Desc*Gsm*Dia*Browse Color*Color Type*Width Dia*Uom*ReqDzn*ReqQty')==false)
					{
					return;
					}
				data_all=data_all+get_submitted_data_string('cboRfSampleName_'+i+'*cboRfGarmentItem_'+i+'*cboRfBodyPart_'+i+'*cboRfFabricNature_'+i+'*txtRfFabricDescription_'+i+'*txtRfGsm_'+i+'*txtRfDia_'+i+'*txtRfColor_'+i+'*cboRfColorType_'+i+'*cboRfWidthDia_'+i+'*cboRfUom_'+i+'*txtRfReqQty_'+i+'*txtProcessLoss_'+i+'*txtGrayFabric_'+i+'*txtRate_'+i+'*txtAmount_'+i+'*updateidRequiredDtl_'+i+'*txtRfColorAllData_'+i+'*libyarncountdeterminationid_'+i,"../../"); 
			}

			var data="action=save_update_delete_required_fabric&operation="+operation+'&total_row='+row_nums+'&booking_no='+booking_no+'&update_id='+update_id+'&requisition_id='+requisition_id+data_all;
			freeze_window(operation);
			http.open("POST","requires/sample_requisition_booking_non_order_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_required_fabric_details_info_response;
		}
	}
	
	function fnc_required_fabric_details_info_response()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==0)
			{
				show_msg(reponse[0]);
				fnc_load_tr(reponse[1],2);  
				set_button_status(1, permission, 'fnc_required_fabric_details_info',3);
  			 }
			 
			if(reponse[0]==1 )
			{
				show_msg(reponse[0]);
				fnc_load_tr(reponse[1],2);
				set_button_status(1, permission, 'fnc_required_fabric_details_info',3);
  			}
			else if(reponse[0]==10 )
			{
				show_msg(reponse[0]);
			}
			release_freezing();
		}
	}
 
	function open_fabric_decription_popup()
	{
		var cbofabricnature=document.getElementById('cbo_fabric_natu').value;
		var page_link='requires/sample_requisition_booking_non_order_controller.php?action=fabric_description_popup&fabric_nature='+cbofabricnature
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var fab_des_id=this.contentDoc.getElementById("fab_des_id");
			var fab_nature_id=this.contentDoc.getElementById("fab_nature_id");
			var fab_desctiption=this.contentDoc.getElementById("fab_desctiption");
			var fab_gsm=this.contentDoc.getElementById("fab_gsm");
			var yarn_desctiption=this.contentDoc.getElementById("yarn_desctiption");
			var construction=this.contentDoc.getElementById("construction");
			var composition=this.contentDoc.getElementById("composition");
			document.getElementById('libyarncountdeterminationid').value=fab_des_id.value;
			document.getElementById('txt_fabricdescription').value=fab_desctiption.value;
			document.getElementById('txt_gsm').value=fab_gsm.value;
			document.getElementById('yarnbreackdown').value=yarn_desctiption.value;
			document.getElementById('construction').value=construction.value;
			document.getElementById('composition').value=composition.value;
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

	function enable_disable(value)
	{
		/*if(value==2){
			document.getElementById('txt_rate').disabled=false;
		}
		else
		{
			document.getElementById('txt_rate').disabled=true;
		}*/
	}

	function generate_fabric_report(type)
	{
		if (form_validation('txt_booking_no','Booking No')==false)
		{
			return;
		}
		else
		{
			$report_title=$( "div.form_caption" ).html();
			if(type==2)
			{
				var data="action=show_fabric_booking_report2"+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id*cbo_fabric_natu',"../../")+'&report_title='+$report_title;	
			}
			else if(type==1)
			{
			var data="action=show_fabric_booking_report"+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id*cbo_fabric_natu',"../../")+'&report_title='+$report_title;
			}
			else if(type==3)
			{
				var data="action=show_fabric_booking_report3"+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id*cbo_fabric_natu',"../../")+'&report_title='+$report_title;	
			}
			else if(type==4)
			{
				var data="action=show_fabric_booking_report4"+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id*cbo_fabric_natu',"../../")+'&report_title='+$report_title;	
			}
			else if(type==5)
			{
				var data="action=show_fabric_booking_report5"+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id*cbo_fabric_natu',"../../")+'&report_title='+$report_title;	
			}
			else if(type==6)
			{
				var data="action=show_fabric_booking_report6"+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id*cbo_fabric_natu',"../../")+'&report_title='+$report_title;	
			}
			//freeze_window(5);
			http.open("POST","requires/sample_requisition_booking_non_order_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;
		}	
	}

	function generate_fabric_report_reponse()
	{
		if(http.readyState == 4) 
		{	
			var file_data=http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}

	function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currency').val();
		var booking_date = $('#txt_booking_date').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/sample_requisition_booking_non_order_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
	}
	
	function check_kniting_charge()
	{
		var cbo_company_name=$('#cbo_company_name').val();
		var response=return_global_ajax_value( cbo_company_name, 'check_kniting_charge', '', 'requires/sample_requisition_booking_non_order_controller');
		
		var response=response.split("_");
		if(response[0]==1)
		{
			$('#txt_knitting_charge').removeAttr('disabled','disabled');	
		}
		else
		{
			$('#txt_knitting_charge').attr('disabled','disabled');
			$('#txt_knitting_charge').val('');			
			
		}
	}
	/*function validate_suplier(){
		var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
		var company=document.getElementById('cbo_company_name').value;
		var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
		if(company==cbo_supplier_name && cbo_pay_mode==5){
			alert("Same Company Not Allowed");
			document.getElementById('cbo_supplier_name').value=0;
			return;
		}
	}*/
	
	function print_button_setting()
	{
		$('#button_panel').html('');
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/sample_requisition_booking_non_order_controller' ); 
	}
	
	function print_report_button_setting(report_ids) 
	{
		var report_id=report_ids.split(",");
		
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==34)
			{
				$('#button_panel').append( '<input type="button" value="Print Booking" onClick="generate_fabric_report(1)"  style="width:100px" name="print" id="print" class="formbutton" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==35)
			{
				$('#button_panel').append( '<input type="button" value="Print Booking2" onClick="generate_fabric_report(2)"  style="width:100px" name="print" id="print" class="formbutton" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==36)
			{
				$('#button_panel').append( '<input type="button" value="Print Amana" onClick="generate_fabric_report(3)"  style="width:100px" name="print3" id="print3" class="formbutton" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==37)
			{
				$('#button_panel').append( '<input type="button" value="Print AKH" onClick="generate_fabric_report(4)"  style="width:100px" name="print4" id="print4" class="formbutton" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==64)
			{
				$('#button_panel').append( '<input type="button" value="Print Metro" onClick="generate_fabric_report(5)"  style="width:100px" name="print5" id="print5" class="formbutton" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==72)
			{
				$('#button_panel').append( '<input type="button" value="Print FFL" onClick="generate_fabric_report(6)"  style="width:100px" name="print6" id="print6" class="formbutton" />&nbsp;&nbsp;&nbsp;' );
			}
		}
	}
	
	function open_trims_acc_popup(title)
	{
		var txt_booking_no=document.getElementById('txt_booking_no').value;
		var update_id=document.getElementById('update_id').value;
		if (txt_booking_no=="")
		{
			alert("Save The Booking First")
			return;
		}	
		else
		{
			page_link='requires/sample_requisition_booking_non_order_controller.php?action=acc_popup'+get_submitted_data_string('txt_booking_no*update_id','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title,'width=720px,height=470px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				//var theform=this.contentDoc.forms[0];
				//var theemail=this.contentDoc.getElementById("selected_data").value;
				//document.getElementById('trims_acc_hidden_data').value=theemail;
			}
		}
	}

	function fabic_srce_con_fnc()
	{
	   if($('#cbo_fabric_source').val()!=0)
	   {
			 $('#cbo_fabric_source_dtls').attr('disabled',true);
			 $('#cbo_fabric_source_dtls').val(0);
			 $('#cbo_fabric_source_dtls_id').css('color','black');
	   }
	   else
	   {
			$('#cbo_fabric_source_dtls').attr('disabled',false); 
			$('#cbo_fabric_source_dtls_id').css('color','blue');
			
			if (form_validation('cbo_fabric_source_dtls','Fabric Source Dtls')==false)
			{
				return;
			}
	   }
	}
	
	function fabric_up_con_fnc()
	{
		if($('#update_id_details').val()!=0)
		{	
			alert('Please, update Master and Details part.');
			//fabic_srce_con_fnc();
		}
	}

	function openmypage_supplier()
	{
		var company_name = $('#cbo_company_name').val();
		var pay_modeid = $('#cbo_pay_mode').val();
		var supplier_id = $('#cbo_supplier_name').val();
		var title = 'Supplier Selection Form';	
		
		var page_link = 'requires/sample_requisition_booking_non_order_controller.php?&action=supplier_popup&company_name='+company_name+'&pay_modeid='+pay_modeid+'&supplier_id='+supplier_id;
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var supplier_id=this.contentDoc.getElementById("hidden_supplier_id").value;	 //Access form field with id="emailfield"
			var supplier_name=this.contentDoc.getElementById("hidden_supplier_name").value;
			$('#cbo_supplier_name').val(supplier_id);
			$('#txt_supplier_name').val(supplier_name);
		}
	}
</script>
</head>
 
<body onLoad="set_hotkey(); check_exchange_rate();check_kniting_charge();print_button_setting();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="fabricbooking_1"  autocomplete="off" id="fabricbooking_1">
        <fieldset style="width:950px;">
            <legend>Sample Booking [Without Order] </legend>
            <table  width="900" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td width="130" align="right"> Booking No </td>              <!-- 11-00030  -->
                    <td width="170"><input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/sample_requisition_booking_non_order_controller.php?action=fabric_booking_popup','fabric Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
                    </td>
                    <td align="right" class="must_entry_caption">Company Name</td>
                    <td><?=create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/sample_requisition_booking_non_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );color_from_library( this.value );check_kniting_charge();print_button_setting();","","" ); // new condtion ?>	
                        <input type="hidden" id="report_ids" name="report_ids"/> 
                        <input type="hidden" id="update_id" name="update_id"/>  
                    </td>
                    <td align="right" class="must_entry_caption">Buyer Name</td>   
                    <td id="buyer_td"> 
						<?  
                        $sql="select id,buyer_name from lib_buyer  where status_active=1 and is_deleted=0   order by buyer_name";
                        echo create_drop_down( "cbo_buyer_name", 172, $sql,"id,buyer_name", 1, "-- Select Buyer --", $selected, "","","" );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="right" class="must_entry_caption">Fabric Nature</td>
                    <td><?=create_drop_down( "cbo_fabric_natu", 172, $item_category,"", 1, "-- Select --", 1,$onchange_func, $is_disabled, "2,3"); ?></td>
                    <td align="right" class="must_entry_caption">Fabric Source</td>
                    <td><?=create_drop_down( "cbo_fabric_source", 172, $fabric_source,"", 1,"-- Select--", "","enable_disable(this.value);fabic_srce_con_fnc()", "", "1,2,3"); ?></td>
                    <td align="right" class="must_entry_caption">Booking Date</td>
                    <td><input class="datepicker" type="text" style="width:160px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" disabled /></td>                              
                </tr>
                <tr>
                    <td align="right">Currency</td>
                    <td><?=create_drop_down( "cbo_currency", 172, $currency,"", 1, "-- Select --", 2, "",0 ); ?></td>
                    <td align="right">Exchange Rate</td>
                    <td><input style="width:160px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly  /></td>
                    <td align="right"> Source</td>
                    <td><?=create_drop_down( "cbo_source", 172, $source,"", 1, "-- Select Source --", "", "","" ); ?></td> 
                </tr>
                <tr>
                    <td class="must_entry_caption" align="right">Delivery Date</td>
                    <td><input class="datepicker" type="text" style="width:160px" name="txt_delivery_date" id="txt_delivery_date"/></td>
                    <td align="right" class="must_entry_caption">Pay Mode</td>
                    <td><?=create_drop_down( "cbo_pay_mode", 172, $pay_mode,"", 1, "-- Select Pay Mode --", 3, "load_drop_down( 'requires/sample_requisition_booking_non_order_controller', this.value, 'load_drop_down_suplier', 'sup_td' )","","1,2,3,5" ); ?></td>
                    <td align="right"> Supplier Name</td>              <!-- 11-00030  -->
                    <td id="sup_td">
						<?
                        echo create_drop_down( "cbo_supplier_name", 172, $blank_array,"", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/sample_requisition_booking_non_order_controller');",0 );
                        ?>
                        <!--<input type="text" name="txt_supplier_name" id="txt_supplier_name" class="text_boxes" style="width:150px;" placeholder="Click To Search"  onClick="openmypage_supplier();" readonly />
                        <input type="hidden" name="cbo_supplier_name" id="cbo_supplier_name" value="" />-->
                    </td>
                </tr>
                <tr>
                    <td align="right">Attention</td>   
                    <td align="left" height="10" colspan="3">
                    <input class="text_boxes" type="text" style="width:97%;"  name="txt_attention" id="txt_attention"/>
                    <input type="hidden" class="image_uploader" style="width:162px" value="Lab DIP No" onClick="openmypage('requires/sample_requisition_booking_non_order_controller.php?action=lapdip_no_popup','Lapdip No','lapdip')">
                    <input type="hidden" id="id_approved_id"></td>
                    <td align="right">Ready To Approved</td>  
                    <td align="center"><?=create_drop_down( "cbo_ready_to_approved", 172, $yes_no,"", 1, "-- Select--", 2, "get_php_form_data( this.value+'_'+document.getElementById('txt_booking_no').value, 'check_dtls_part', 'requires/sample_requisition_booking_non_order_controller');","","" ); ?>
                    <input type="hidden" name="is_found_dtls_part" id="is_found_dtls_part" value="1"/>
                    </td>
                </tr>
                <tr>
                    <td align="right">Team Leader</td>   
                    <td><?=create_drop_down( "cbo_team_leader", 172, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'requires/sample_requisition_booking_non_order_controller', this.value, 'cbo_dealing_merchant', 'div_marchant' ) " ); ?></td>
                    <td align="right">Dealing Merchant</td>   
                    <td id="div_marchant"><?=create_drop_down( "cbo_dealing_merchant", 172, $blank_array,"", 1, "-- Select Team Member --", $selected, "" ); ?></td>
                    <td align="right"></td>
                    <td><input type="button" id="set_button" class="image_uploader" style="width:160px;" value="Terms & Condition/Notes" onClick="open_terms_condition_popup('requires/sample_requisition_booking_non_order_controller.php?action=terms_condition_popup','Terms Condition')" /></td>
                </tr>
                <tr>
                    <td align="right"></td> 
                    <td><input type="button" class="image_uploader" style="width:192px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('txt_booking_no').value,'', 'sample_booking_non', 0 ,1)"></td> 
                    <td>&nbsp;</td>
                    <td>&nbsp;<input type="button" id="set_button" class="image_uploader" style="width:165px;" value="Accessories" onClick="open_trims_acc_popup('Accessories Dtls')" /></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                	<td align="center" colspan="6" valign="top" id="app_sms2" style="font-size:18px; color:#F00"></td>
                </tr>
                <tr>
                    <td align="center" colspan="6" valign="middle" class="button_container">
                    <? $date=date('d-m-Y'); echo load_submit_buttons( $permission, "fnc_fabric_booking", 0,0 ,"reset_form('fabricbooking_1','','','cbo_pay_mode,3*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_date,".$date."')",1) ; ?>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="6" height="10"></td>
                </tr>
            </table>
        </fieldset>
    </form>
              
    <form name="required_fabric_1" id="required_fabric_1">
        <fieldset style="width:1350px;" id="required_fab_dtls">
            <table width="100%" cellpadding="0" cellspacing="2" align="center" >
                <tr>
                    <td align="right" ><strong> Sample Requisition &nbsp;</strong></td>
                    <td colspan="3"> <input type="text" name="txt_requisition" id="txt_requisition" class="text_boxes" style="width: 145px;margin-right: 38px;" placeholder="Browse Requisition" readonly onDblClick="openmypage_requisition();" >
                    	<input type="hidden" name="hidden_requisition_id" id="hidden_requisition_id">
                    </td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
            <table width="100%" cellpadding="0" cellspacing="2" align="center" >
                <tr>
                    <td align="center" valign="top" id="po_list_view">
                    <legend>Required Fabric </legend>
                    <table cellpadding="0" cellspacing="0" width="1350" class="rpt_table" border="1" rules="all" id="tbl_required_fabric">
                        <thead>
                            <th class="must_entry_caption">Sample Name </th>
                            <th class="must_entry_caption">Garment Item</th>
                            <th class="must_entry_caption">Body Part</th>
                            <th class="must_entry_caption">Fabric Nature</th>
                            <th class="must_entry_caption">Fabric Description</th>
                            <th class="must_entry_caption">GSM/Ounce</th>
                            <th class="must_entry_caption">Width</th>
                            <th class="must_entry_caption">Color</th>
                            <th class="must_entry_caption">Color Type</th>
                            <th class="must_entry_caption">Type</th>
                            <th class="must_entry_caption">UOM</th>
                            <th class="must_entry_caption">Req. Qty.</th>
                            <th class="">Process Loss</th>
                            <th class="">Req. Grey.</th>
                            <th class="">Rate</th>
                            <th class="">Amount</th>
                            <th class="must_entry_caption"></th>
                        </thead>
                
                        <tbody id="required_fabric_container">
                            <tr id="tr_1" style="height:10px;" class="general">
                                <td align="center" id="rfSampleId_1">
                                    <?
                                    $sql="select id,sample_name from lib_sample where status_active=1 and is_deleted=0";
                                    echo create_drop_down( "cboRfSampleName_1", 95, $sql,"id,sample_name", 1, "select Sample", $selected,"",'1');	
                                    ?>		 
                                </td>
                                <td align="center" id="rfItemId_1"><?=create_drop_down( "cboRfGarmentItem_1", 95, $blank_array,"", 1, "Select Item", 0, "",'1'); ?></td>
                                <td align="center" id="rf_body_part_1"><?=create_drop_down( "cboRfBodyPart_1", 95, $body_part,"", 1, "Select Body Part", 0, "load_data_to_rfcolor('1');",'1'); ?></td>                              
                                <td align="center" id="rf_fabric_nature_1"><?=create_drop_down( "cboRfFabricNature_1", 95, $item_category,"", 0, "Select Fabric Nature", 3, "","1","2,3");  ?></td>
                                <td align="center" id="rf_fabric_description_1">
                                    <input style="width:80px;" type="text" class="text_boxes"  name="txtRfFabricDescription_1" id="txtRfFabricDescription_1" placeholder="write/browse" onDblClick="open_fabric_description_popup(1)" readonly disabled=""/>
                                    <input type="hidden" name="libyarncountdeterminationid_1" id="libyarncountdeterminationid_1" class="text_boxes" style="width:10px" >
                                </td>
                                <td align="center" id="rf_gsm_1"><input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtRfGsm_1" id="txtRfGsm_1" placeholder="" readonly disabled=""/></td>
                                <td align="center" id="rf_dia_1">
                                    <input type="hidden" id="updateidRequiredDtl_1" name="updateidRequiredDtl_1"  value=""  class="text_boxes" /> 
                                    <input type="hidden" id="txtDeltedIdRf" name="txtDeltedIdRf"  class="text_boxes" style="width:20px" value="" />
                                    <input style="width:40px;" type="text" class="text_boxes"  name="txtRfDia_1" id="txtRfDia_1" readonly disabled=""/>
                                </td>
                                <td align="center" id="rf_color_1">
                                    <input style="width:60px;" type="text" class="text_boxes"  name="txtRfColor_1" id="txtRfColor_1" placeholder="Write/Browse" onDblClick="openmypage_rf_color('requires/sample_requisition_booking_non_order_controller.php?action=color_popup_rf','Color Search','1');" readonly disabled="" />
                                    <input type="hidden" name="txtRfColorAllData_1" id="txtRfColorAllData_1" value=""  class="text_boxes">
                                </td>
                                <td align="center" id="rf_color_type_1"><?=create_drop_down( "cboRfColorType_1", 95, $color_type,"", 1, "Select Color Type", 0, "",'1'); ?></td>
                                <td align="center" id="rf_width_dia_1"><?=create_drop_down( "cboRfWidthDia_1", 80, $fabric_typee,"", 1, "Select Type", 0, "",'1'); ?></td>
                                <td align="center" id="rf_uom_1"><?=create_drop_down( "cboRfUom_1", 56, $unit_of_measurement,'', '', "",12,"","1","12,27,1,23" ); ?></td>
                                <td align="center" id="rf_req_qty_1"><input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtRfReqQty_1" id="txtRfReqQty_1" placeholder=""  onChange="calculate_requirement('1')"/></td>
                                <td align="center" id="rf_reqs_qty_1"><input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtProcessLoss_1" id="txtProcessLoss_1" placeholder=""  onChange="calculate_requirement('1')" /></td>
                                <td align="center" id="rf_req_qnty_1"><input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtGrayFabric_1" id="txtGrayFabric_1" placeholder="" readonly /></td>
                                <td align="center" id="rf_req_qnty_1"><input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtRate_1" id="txtRate_1" placeholder="" onChange="calculate_amount('1')" /></td>
                                <td align="center" id="rf_req_qnty_1"><input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtAmount_1" id="txtAmount_1" placeholder=""  /></td>
                                <td width="70">
                                    <!--   <td id="rf_image_1"><input type="button" class="image_uploader" name="txtRfFile_1" id="txtRfFile_1" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredDtl_1').value,'', 'required_fabric_1', 0 ,1)" value="ADD IMAGE" disabled=""></td> -->
                                    <input type="hidden" class="text_boxes"  name="txtMemoryDataRf_1" id="txtMemoryDataRf_1" />
                                    <input type="button" id="increaserf_1" name="increaserf_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_rf_tr(1)"  disabled=""/>
                                    <input type="button" id="decreaserf_1" name="decreaserf_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_rf_deleteRow(1);" disabled="" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table>
                        <tr>
                            <td colspan="14" valign="bottom" align="center">
                                <?=load_submit_buttons( $permission, "fnc_required_fabric_details_info", 0,0 ,"reset_form('required_fabric_1','','','')",3); ?>
                                <input type="button" value="Fabric booking" onClick="generate_fabric_report('1')"  style="width:130px;" name="print_booking_urmi" id="print_booking_urmi" class="formbutton" />
                                <input type="button" value="Print Booking" onClick="generate_fabric_report('2')"  style="width:130px;" name="print_booking_barnali" id="print_booking_barnali" class="formbutton" />
                            </td>	
                        </tr>
                    </table>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
<!-- 
 <fieldset style="width:1670px;">
                <legend>Booking Entry</legend>
            	
                    <table style="border:none" cellpadding="0" cellspacing="2" border="0">
                             
                            <tr align="center">
                                <td colspan="12" id="booking_list_view">
                                </td>	
                        	</tr>
                       </table>
            	
                </fieldset>
                 -->
	</div>
   <div style="display:none" id="data_panel"></div>
   

</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$( document ).ready(function() {
//load_drop_down( 'requires/sample_requisition_booking_non_order_controller', document.getElementById('cbo_pay_mode').value, 'load_drop_down_suplier', 'sup_td' )
});
//set_multiselect( 'cbo_booking_gr', '1', '0', '0', '0' ); 

</script>
</html>