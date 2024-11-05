<?
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This form will create Woven Short Fabric Booking
Functionality	         :
JS Functions	         :
Created by		         :	Zakaria
Creation date 	         : 	17-02-19
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
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
//--------------------------------------------------------------------------------------------------------------------
$field_level_data = array();
if(count($_SESSION['logic_erp']['data_arr'][88])>0)
{
	$field_level_data = $_SESSION['logic_erp']['data_arr'][88];
}
echo load_html_head_contents("Woven Fabric Booking", "../../", 1, 1,$unicode,1,'');
?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';
<?
	$data_arr= json_encode($_SESSION['logic_erp']['data_arr'][88] );
	echo "var field_level_data= ". $data_arr . ";\n";
	if($_SESSION['logic_erp']['mandatory_field'][275][31]){
		$short_booking_mendatory = 'must_entry_caption';
	}
	else{
		$short_booking_mendatory = '';
	}
	//dynamic disabled korte bola hoychhe kintu kore nai
?>
	$(document).ready(function(){
		$( "#cbo_brand_id" ).prop( "disabled", true );
		$( "#cbo_season_id" ).prop( "disabled", true );
		$( "#cbo_season_year" ).prop( "disabled", true );
		$( "#txt_bodywashColor" ).prop( "disabled", true );
		$( "#txt_rd_no" ).prop( "disabled", true );
		$( "#txt_fabric_ref" ).prop( "disabled", true );
	
	});

	function openmypage_order(page_link,title)
	{
		if(document.getElementById('id_approved_id').value==1 || document.getElementById('id_approved_id').value==3)
		{
			alert("This booking is approved")
			return;
		}
		var month_check=$('#month_id').val();
		//alert(month_check);
		if(month_check==1)
		{
			if (form_validation('cbo_booking_month*cbo_booking_year*cbo_fabric_natu*cbo_fabric_source','Booking Month*Booking Year*Fabric Nature*Fabric Source')==false)
			{
				return;
			}
		}
		else
		{
			if (form_validation('cbo_booking_year*cbo_fabric_natu*cbo_fabric_source','Booking Year*Fabric Nature*Fabric Source')==false)
			{
				return;
			}
		}
		var txt_booking_no=document.getElementById('txt_booking_no').value;
		var check_is_booking_used_id=return_global_ajax_value(txt_booking_no, 'check_is_booking_used', '', 'requires/short_fabric_booking_controller');
		if(trim(check_is_booking_used_id) !="")
		{
			alert("This booking used in PI Table. So Adding or removing order is not allowed")
			return;
		}
		else
		{
			if(txt_booking_no=="")
			{
			page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_booking_month*cbo_booking_year*txt_booking_date*cbo_brand_id*cbo_season_id*cbo_season_year','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=450px,center=1,resize=1,scrolling=0','../')
			}
			else
			{
				var r=confirm("Existing Item against these Order  Will be Deleted")
				if(r==true)
				{
				var delete_booking_item=return_global_ajax_value(txt_booking_no, 'delete_booking_item', '', 'requires/short_fabric_booking_controller');
				show_list_view(txt_booking_no,'show_fabric_booking','booking_list_view','requires/short_fabric_booking_controller','setFilterGrid(\'list_view\',-1)');
				page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_booking_month*cbo_booking_year*txt_booking_date*cbo_brand_id*cbo_season_id*cbo_season_year','../../');
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=450px,center=1,resize=1,scrolling=0','../')
				}
				else
				{
					return;
				}
			}
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];;
				var id=this.contentDoc.getElementById("po_number_id");
				var po=this.contentDoc.getElementById("po_number");
				if (id.value!="")
				{
					freeze_window(5);
					resetDtlsForm();
					document.getElementById('txt_order_no_id').value=id.value;
					document.getElementById('txt_order_no').value=po.value;
					var cbo_fabric_natu =document.getElementById('cbo_fabric_natu').value;
					var cbo_fabric_source=document.getElementById('cbo_fabric_source').value;
					var supplier_name =document.getElementById('cbo_supplier_name').value;
				
					var cbouom=document.getElementById('cbouom').value;
					get_php_form_data( id.value, "populate_order_data_from_search_popup", "requires/short_fabric_booking_controller" );
					check_month_setting();
				
					var reportId=document.getElementById('report_ids').value;
					print_report_button_setting(reportId);
					
					fnc_get_po_config(id.value+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom+'_'+supplier_name+'_1');
					release_freezing();
					fnc_generate_booking()
				}
			}
		}
	}
	
	function fnc_get_po_config(data)
	{
		//alert(data); return
		var exdata=data.split('_');
		var po_id=exdata[0];
		var fabricnature=exdata[1];
		var fabricsource=exdata[2];
		var fabricuom=exdata[3];
		var supplier=exdata[4];
		var rowid=exdata[5];
		get_php_form_data(po_id+'_'+fabricnature+'_'+fabricsource+'_'+fabricuom+'_'+supplier+'_'+rowid,'get_po_config','requires/short_fabric_booking_controller' );
	}
	
	function resetDtlsForm()
	{
		$('#booking_list_view').html('');
		$('#details_list tr:not(:first)').remove();
		$('#txtsl_1').val(1);
		/*$('#cboorderid_1').val(0);
		$('#cbofabricdescriptionid_1').val(0);
		$('#cbogarmentscolorid_1').val(0);
		$('#cbofabriccolorid_1').val(0);*/
		$('#cbogarmentssizeid_1').val(0);
		$('#cboitemsizeid_1').val(0);
		$('#txtdiawidth_1').val('');
		$('#txtfinishqnty_1').val('');
		$('#txtprocessloss_1').val('');
		$('#txtgreyqnty_1').val('');
		$('#txtrate_1').val('');
		$('#txtamount_1').val('');
		$('#txtrmgqty_1').val('');
		$('#cbodivisionid_1').val(0);
		$('#cboresponsibledept_1').val(0);
		$('#txtresponsibleperson_1').val('');
		$('#txtreason_1').val('');
		$('#txtremarks_1').val('');
		$('#updateiddetails_1').val('');
		$('#hiddencollarCuffdata_1').val('');
		$('#hiddencolorType_1').val('');
	}
	
	function set_process_loss(i)
	{
		var fabricid=$('#cbofabricdescriptionid_'+i).val();
		var prosess_loss=return_global_ajax_value(fabricid, 'prosess_loss_set', '', 'requires/short_fabric_booking_controller');
		get_php_form_data(fabricid+'_'+i, 'prosess_loss_set_2', 'requires/short_fabric_booking_controller');
		document.getElementById('txtprocessloss_'+i).value=trim(prosess_loss);
		calculate_requirement(i);
	}
	
	function booking_rate_sourcing(fab_dtl_id,vari_type,i)
	{
		//alert(fab_dtl_id);
		 get_php_form_data( fab_dtl_id+'_'+vari_type+'_'+i, "sourcing_rate_from_budget", "requires/short_fabric_booking_controller" );
		 calculate_requirement(i);
	}
	
	function cs_booking_rate_supplier(fab_dtl_id,vari_type,i)
	{
		// alert(fab_dtl_id);
		var company=$("#cbo_company_name").val()*1;
		var job_no=$("#txt_job_no").val();
		var cbo_supplier_name=$("#cbo_supplier_name").val();
		get_php_form_data( fab_dtl_id+'_'+company+'_'+job_no+'_'+cbo_supplier_name+'_'+i, "supplier_rate_from_cs", "requires/short_fabric_booking_controller" );
		calculate_requirement(i);
	}

	function openmypage_booking(page_link,title)
	{
		// emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1250px,height=450px,center=1,resize=1,scrolling=0','../')
		// emailwindow.onclose=function()
		var company=$("#cbo_company_name").val()*1;
		var buyer=$("#cbo_buyer_name").val()*1;
		var brand_id=$("#cbo_brand_id").val()*1;
		var season_id=$("#cbo_season_id").val()*1;
		//alert(company);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&company='+company+'&buyer_id='+buyer+'&cbo_brand_id='+brand_id+'&cbo_season_id='+season_id, title, 'width=1230px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");
			if (theemail.value!="")
			{
				freeze_window(5);
				reset_form('fabricbooking_1','booking_list_view','', 'txt_booking_date,<? echo date("d-m-Y"); ?>');
				get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/short_fabric_booking_controller" );
				check_month_setting();
				var reportId=document.getElementById('report_ids').value;
	
				print_report_button_setting(reportId);
				reset_form('orderdetailsentry_2','booking_list_view','','','')
				resetDtlsForm();
				var txt_order_no_id=document.getElementById('txt_order_no_id').value
				var cbo_fabric_natu =document.getElementById('cbo_fabric_natu').value
				var supplier_name =document.getElementById('cbo_supplier_name').value
				var cbo_fabric_source=document.getElementById('cbo_fabric_source').value
				var cbouom=document.getElementById('cbouom').value
				
				fnc_get_po_config(txt_order_no_id+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom+'_'+supplier_name+'_1');
				
				show_list_view(theemail.value,'show_fabric_booking','booking_list_view','requires/short_fabric_booking_controller','setFilterGrid(\'list_view\',-1)');
				set_button_status(1, permission, 'fnc_fabric_booking',1);
				release_freezing();
			}
		}
	}

	function calculate_requirement(i)
	{
		var cbo_company_name= document.getElementById('cbo_company_name').value;
		var cbo_fabric_natu= document.getElementById('cbo_fabric_natu').value
		var process_loss_method_id=return_global_ajax_value(cbo_company_name+'_'+cbo_fabric_natu, 'process_loss_method_id', '', 'requires/short_fabric_booking_controller');
		var txt_finish_qnty=(document.getElementById('txtfinishqnty_'+i).value)*1;
		var processloss=(document.getElementById('txtprocessloss_'+i).value)*1;
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
		document.getElementById('txtgreyqnty_'+i).value= WastageQty;
		document.getElementById('txtamount_'+i).value=number_format_common((document.getElementById('txtrate_'+i).value)*1*WastageQty,5,0)
	}

	function fnc_fabric_booking( operation )
	{
		freeze_window(operation);
		if(operation==2)
		{
			alert("Delete Restricted");
			release_freezing();
			return;
		}
		if(document.getElementById('id_approved_id').value==1)
		{
			alert("This booking is approved");
			release_freezing();
			return;
		}
		var month_set_id=$('#month_id').val();
		if(month_set_id==1)
		{
			if (form_validation('cbo_booking_month','Booking Month')==false)
			{
				release_freezing();
				return;
			}
		}
	
		var delivery_date=$('#txt_delivery_date').val();
		if(date_compare($('#txt_booking_date').val(), delivery_date)==false)
		{
			alert("Delivery Date Not Allowed Less than Booking Date");
			release_freezing();
			return;
		}
		
		if (form_validation('txt_order_no_id*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_supplier_name','Order No*Booking Date*Delivery Date*Pay Mode*Supplier Name')==false)
		{
			release_freezing();
			return;
		}
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_order_no_id*update_id*cbo_company_name*cbo_buyer_name*txt_job_no*txt_booking_no*cbo_fabric_natu*cbo_fabric_source*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_booking_month*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_source*cbo_booking_year*cbo_ready_to_approved*cbo_short_booking_type*cbouom*txt_remark*delivery_address*cbo_season_year*cbo_season_id*cbo_brand_id*txt_reqsn_no*txt_reqsn_id',"../../");
			
			http.open("POST","requires/short_fabric_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_fabric_booking_reponse;
		}
	}

	function fnc_fabric_booking_reponse(){
	
		if(http.readyState == 4){
			 var reponse=trim(http.responseText).split('**');
			 if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1 || parseInt(trim(reponse[0]))==2){
				 show_msg(trim(reponse[0]));
				 document.getElementById('txt_booking_no').value=reponse[1];
				 document.getElementById('update_id').value=reponse[2];
				 set_button_status(1, permission, 'fnc_fabric_booking',1);
				 release_freezing();
			 }
			if(trim(reponse[0])=='approved'){
				alert("This booking is approved");
				release_freezing();
				return;
			}
			/*if(trim(reponse[0])=='sal1'){
				alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}*/
			if(trim(reponse[0])=='pi1'){
				alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			release_freezing();
		}
	}

	function fnc_fabric_booking_dtls( operation )
	{
		freeze_window(operation);
		/*if(operation==2)
		{
			alert("Delete Restricted");
			release_freezing();
			return;
		}*/
		if(document.getElementById('id_approved_id').value==1)
		{
			alert("This booking is approved");
			release_freezing();
			return;
		}
		
		/*if('<?// echo implode('*',$_SESSION['logic_erp']['mandatory_field'][275]);?>'){
			if (form_validation('<?// echo implode('*',$_SESSION['logic_erp']['mandatory_field'][275]);?>','<?// echo implode('*',$_SESSION['logic_erp']['mandatory_message'][275]);?>')==false)
			{
				release_freezing();
				return;
			}
		}*/
		
		
		if (form_validation('txt_booking_no*txt_order_no_id*txt_booking_date','Booking No*Order No*Booking Date')==false)
		{
			release_freezing();
			return;
		}
		
		var row_num=$('#details_list tr').length;
		//alert(row_num); release_freezing();  return;
		var data_all=""; var z=1;
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('cboorderid_'+i+'*cbofabricdescriptionid_'+i+'*cbogarmentscolorid_'+i+'*cbofabriccolorid_'+i+'*cbogarmentssizeid_'+i+'*txtdiawidth_'+i+'*txtfinishqnty_'+i+'*cboresponsibledept_'+i+'*txtresponsibleperson_'+i+'*txtreason_'+i,'Po No*Fabric Description*Garments Color*Fabric Color*Garments size*Width*Fabric Qty*Responsible Dept*Responsible Person*Reason')==false)
			{
				release_freezing();
				return;
			}
			
			data_all+="&cboorderid_" + z + "='" + $('#cboorderid_'+i).val()+"'"+"&cbofabricdescriptionid_" + z + "='" + $('#cbofabricdescriptionid_'+i).val()+"'"+"&cbogarmentscolorid_" + z + "='" + $('#cbogarmentscolorid_'+i).val()+"'"+"&cbofabriccolorid_" + z + "='" + $('#cbofabriccolorid_'+i).val()+"'"+"&cbogarmentssizeid_" + z + "='" + $('#cbogarmentssizeid_'+i).val()+"'"+"&cboitemsizeid_" + z + "='" + $('#cboitemsizeid_'+i).val()+"'"+"&txtdiawidth_" + z + "='" + $('#txtdiawidth_'+i).val()+"'"+"&txtfinishqnty_" + z + "='" + $('#txtfinishqnty_'+i).val()+"'"+"&txtprocessloss_" + z + "='" + $('#txtprocessloss_'+i).val()+"'"+"&txtgreyqnty_" + z + "='" + $('#txtgreyqnty_'+i).val()+"'"+"&txtrate_" + z + "='" + $('#txtrate_'+i).val()+"'"+"&txtamount_" + z + "='" + $('#txtamount_'+i).val()+"'"+"&txtrmgqty_" + z + "='" + $('#txtrmgqty_'+i).val()+"'"+"&cbodivisionid_" + z + "='" + $('#cbodivisionid_'+i).val()+"'"+"&cboresponsibledept_" + z + "='" + $('#cboresponsibledept_'+i).val()+"'"+"&txtresponsibleperson_" + z + "='" + $('#txtresponsibleperson_'+i).val()+"'"+"&txtreason_" + z + "='" + $('#txtreason_'+i).val()+"'"+"&txtremarks_" + z + "='" + $('#txtremarks_'+i).val()+"'"+"&updateiddetails_" + z + "='" + $('#updateiddetails_'+i).val()+"'";
			z++;		
		}
		
		var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('txt_booking_no*update_id*txt_job_no*txt_reqsn_no*txt_reqsn_id*cbo_pay_mode',"../../")+data_all;
		
		http.open("POST","requires/short_fabric_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
	}

	function fnc_fabric_booking_dtls_reponse(){
		if(http.readyState == 4){
			 var reponse=http.responseText.split('**');
			 if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1 || parseInt(trim(reponse[0]))==2){
				 show_msg(trim(reponse[0]));
				 resetDtlsForm();
				 set_button_status(0, permission, 'fnc_fabric_booking_dtls',2);
				 show_list_view(reponse[1],'show_fabric_booking','booking_list_view','requires/short_fabric_booking_controller','setFilterGrid(\'list_view\',-1)');
			 }
			 if(trim(reponse[0])=='approved'){
				alert("This booking is approved");
				release_freezing();
				return;
			}
			/*if(trim(reponse[0])=='sal1'){
				alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}*/
			if(trim(reponse[0])=='pi1'){
				alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='recv1'){
				alert("Receive Number Found :"+trim(reponse[2])+"\n So Delete Not Possible")
			    release_freezing();
			    return;
		    }
			release_freezing();
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

	function generate_fabric_report(type,report_type)
	{
		if (form_validation('txt_booking_no','Booking No')==false)
		{
			return;
		}
		else
		{
			var show_yarn_rate='';
			var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
			if (r==true)
			{
				show_yarn_rate="1";
			}
			else
			{
				show_yarn_rate="0";
			}
			$report_title=$( "div.form_caption" ).html();
	
			var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no',"../../")+'&report_title='+$report_title+'&show_yarn_rate='+show_yarn_rate+'&report_type='+report_type+'&path=../../';
			freeze_window(5);
			http.open("POST","requires/short_fabric_booking_controller.php",true);
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
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
			release_freezing();
		}
	}

	function print_report_button_setting(report_ids)
	{
		$("#print").hide();	
		$("#print_booking3").hide();	
		$("#print_booking4").hide();	
		$("#print_booking_urmi").hide();	
		$("#print_booking_3").hide();	
		$("#fabric_booking_report").hide();
		$("#fabric_booking_report_2").hide();
		
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			//alert(report_id[k]);
			if(report_id[k]==8) $("#print").show();
			if(report_id[k]==9) $("#print_booking3").show();
			if(report_id[k]==10) $("#print_booking4").show();
			if(report_id[k]==46) $("#print_booking_urmi").show();
			if(report_id[k]==136) $("#print_booking_3").show();
			if(report_id[k]==155) $("#fabric_booking_report").show();
			if(report_id[k]==749) $("#fabric_booking_report_2").show();
		}
	}
	
	function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currency').val();
		var booking_date = $('#txt_booking_date').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/short_fabric_booking_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
	}
	
	function check_month_setting()
	{
		var cbo_company_name=$('#cbo_company_name').val();
		var response=return_global_ajax_value( cbo_company_name, 'check_month_maintain', '', 'requires/short_fabric_booking_controller');

		var response=response.split("_");
		if(response[0]==1)
		{
			$('#month_id').val(1);
			$('#booking_td').css('color','blue');
		}
		else
		{
			$('#month_id').val(2);
			$('#booking_td').css('color','black');
			$('#cbo_booking_month').val('');
		}
	}
	
	function validate_suplier(){
		var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
		var company=document.getElementById('cbo_company_name').value;
		var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
	}
	
	function buyer_select()
	{
		if($('#cbo_buyer_name option').length==2)
		{
			if($('#cbo_buyer_name option:first').val()==0)
			{
				$('#cbo_buyer_name').val($('#cbo_buyer_name option:last').val());
				//eval($('#cbo_buyer_name').attr('onchange')); 
			}
		}
		else if($('#cbo_buyer_name option').length==1)
		{
			$('#cbo_buyer_name').val($('#cbo_buyer_name option:last').val());
			//eval($('#cbo_buyer_name').attr('onchange'));
		}	
	}
	
	function fnc_brandload()
	{
		var buyer=$('#cbo_buyer_name').val();
		if(buyer!=0)
		{
			load_drop_down( 'requires/short_fabric_booking_controller', buyer, 'load_drop_down_brand', 'brand_td');
		}
	}
	
	function fnc_openmypage_requisition(page_link,title)
	{
		if (form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var page_link=page_link+'&company_id='+cbo_company_name+'&buyer_id='+cbo_buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=980px,height=455px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("txt_booking").value;
			//alert(theemail)
			if(theemail!=""){
				var exdata=theemail.split("_");
				$("#txt_reqsn_no").val( exdata[0] );
				$("#txt_reqsn_id").val( exdata[1] );
				
				//reset_form('trimsbooking_1','booking_list_view','id_approved_id','txt_booking_date,<? echo date("d-m-Y"); ?>','','cbo_company_name*cbo_buyer_name*cbo_basis_id');
				
				get_php_form_data( exdata[0], "populate_data_from_search_popup_requisition", "requires/short_fabric_booking_controller");
				
				$('#cbo_company_name').attr('disabled',true);
				$('#cbo_buyer_name').attr('disabled',true);
				
				var reportId=document.getElementById('report_ids').value;
				
				print_report_button_setting(reportId);
				
				var txt_order_no_id=document.getElementById('txt_order_no_id').value;
				var cbo_fabric_natu =document.getElementById('cbo_fabric_natu').value;
				var cbo_fabric_source=document.getElementById('cbo_fabric_source').value;
				var cbouom=document.getElementById('cbouom').value;
				var supplier_name =document.getElementById('cbo_supplier_name').value;
				
				//fnc_get_po_config(txt_order_no_id+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom+'_1');
				var data=exdata[1]+'_'+txt_order_no_id+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom+'_'+supplier_name+'_1';
				
				var list_view_orders = return_global_ajax_value( data, 'load_php_reqdtls_form', '', 'requires/short_fabric_booking_controller');
				if(list_view_orders!='')
				{
					$("#details_list tr").remove();
					$("#details_list").append(list_view_orders);
				}
			}
		}
	}
	
	function fnc_get_buyer_config(buyer_id)
	{
		get_php_form_data(buyer_id+'*'+1,'get_buyer_config','requires/short_fabric_booking_controller' );
	}
	
</script>

</head>

<body onLoad="set_hotkey(); check_exchange_rate(); check_month_setting(); buyer_select(); fnc_brandload();">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>
        <form name="fabricbooking_1"  autocomplete="off" id="fabricbooking_1">
            <fieldset style="width:1280px;">
            <legend>Short Fabric Booking[WVN]</legend>
            <table width="1280" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td colspan="5" align="right" class="must_entry_caption"><b>BOOKING NO</b></td>
                    <td colspan="5">
                    	<input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/short_fabric_booking_controller.php?action=fabric_booking_popup','Fabric Booking Search');" readonly placeholder="Browse" name="txt_booking_no" id="txt_booking_no"/>
                        <input type="hidden" id="id_approved_id">
                        <input type="hidden" id="update_id">
                        <input type="hidden" id="month_id" class="text_boxes"  style="width:20px" >
                        <input type="hidden" id="txt_reqsn_id"/>
                    </td>
                </tr>
                <tr>
                    <td width="100" class="must_entry_caption">Company Name</td>
                    <td width="150"><?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/short_fabric_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );check_month_setting();validate_suplier()",0,"" ); ?>
                        <input type="hidden" id="report_ids">
                    </td>
                    <td width="100" class="must_entry_caption">Buyer Name</td>
                    <td width="150" id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td>
                    <td width="100">Brand</td>
                    <td width="150" id="brand_td"><?= create_drop_down( "cbo_brand_id", 130, $blank_array,'', 1, "--Brand--",$selected );?></td>
                    <td width="100">Season <? echo create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                    <td width="150" id="season_td"><? echo create_drop_down( "cbo_season_id", 130, $blank_array,'', 1, "--Season--",$selected, "" ); ?></td>
                    <td width="100" id="booking_td">Booking Month</td>
                    <td><?=create_drop_down( "cbo_booking_month", 75, $months,"", 1, "-Select-", "", "",0 ); ?>
                    	<?=create_drop_down( "cbo_booking_year", 50, $year,"", 1, "-Select-", date('Y'), "",0 ); ?>
                    </td>
                </tr>
                <tr>
                	<td class="must_entry_caption">Booking Date</td>
                    <td><input class="datepicker" type="text" style="width:120px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<?=date("d-m-Y"); ?>" disabled /></td>
                    <td class="must_entry_caption">Delivery Date</td>
                    <td><input class="datepicker" type="text" style="width:120px" name="txt_delivery_date" id="txt_delivery_date"/></td>
                    <td>Currency</td>
                    <td><?=create_drop_down( "cbo_currency", 130, $currency,"", 1, "-- Select --", 2, "",0 ); ?></td>
                    <td>Exchange Rate</td>
                    <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_exchange_rate" id="txt_exchange_rate" readonly /></td>
                    <td>Source</td>
                    <td><?=create_drop_down( "cbo_source", 130, $source,"", 1, "-- Select Source --", "", "","" ); ?></td>
                </tr>
                <tr>
                	<td class="must_entry_caption">Fabric Nature</td>
                    <td>
						<?
                        echo create_drop_down( "cbo_fabric_natu", 78, $item_category,"", 1, "-- Select --", 3,$onchange_func, $is_disabled, "2,3");
                        echo create_drop_down( "cbouom", 50, $unit_of_measurement,'', 1, '-Uom-', $row[csf('uom')], "",$disabled,"1,12,23,27" );
                        ?>
                    </td>
                    <td class="must_entry_caption">Fabric Source</td>
                    <td><?=create_drop_down( "cbo_fabric_source", 130, $fabric_source,"", 1, "-- Select --", 2,"enable_disable(this.value);", "", ""); ?></td>
                    <td class="must_entry_caption">Requisition No</td>
                    <td><input name="txt_reqsn_no" id="txt_reqsn_no" class="text_boxes" type="text" style="width:120px" onDblClick="fnc_openmypage_requisition( 'requires/short_fabric_booking_controller.php?action=requisition_popup','Short Fabric Requisition Search');" readonly placeholder="Browse" /></td>
                    <td class="must_entry_caption">Order No</td>
                    <td colspan="3">
                        <input class="text_boxes" type="text" style="width:370px;" placeholder="Double click for Order"  onDblClick="openmypage_order('requires/short_fabric_booking_controller.php?action=order_search_popup','Order Search');" name="txt_order_no" id="txt_order_no"/>
                        <input class="text_boxes" type="hidden" style="width:100px;"  name="txt_order_no_id" id="txt_order_no_id"/>
                    </td>
                </tr>
                <tr>
                	<td>Job No.</td>
                    <td><input style="width:120px;" type="text" class="text_boxes"  name="txt_job_no" id="txt_job_no" disabled  /></td>
                	<td>M.Style/Int. Ref No</td>
                    <td><Input name="txt_intarnal_ref" class="text_boxes" readonly placeholder="Display" ID="txt_intarnal_ref" style="width:120px" ></td>
                    <td>File no</td>
                    <td ><Input name="txt_file_no" class="text_boxes" readonly placeholder="Display" ID="txt_file_no" style="width:120px" ></td>
                    <td class="must_entry_caption">Pay Mode</td>
                    <td><?=create_drop_down( "cbo_pay_mode", 130, $pay_mode,"", 1, "-- Select Pay Mode --", 2, "load_drop_down( 'requires/short_fabric_booking_controller', this.value, 'load_drop_down_suplier', 'sup_td' )","","1,2,3,5" ); ?></td>
                    <td class="must_entry_caption">Supplier Name</td>
                    <td id="sup_td"><?=create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a,lib_supplier_tag_company b,lib_company  c  where a.id=b.supplier_id and c.id=b.tag_company and a.status_active =1 and a.is_deleted=0 and c.core_business not in(3) group by a.id,a.supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/short_fabric_booking_controller');",0 ); ?></td>
                </tr>
                <tr>
                	<td>Body/Wash Color</td>
                    <td><input class="text_boxes" type="text" style="width:120px;" name="txt_bodywashColor" id="txt_bodywashColor"/></td>
                	<td class="<?=$short_booking_mendatory; ?>">Short Booking Type</td>
                    <td><?=create_drop_down( "cbo_short_booking_type", 130, $short_booking_type,"", 1, "-- Select--", "", "","","" ); ?></td>
                    <td>Attention</td>
                    <td colspan="3">
                        <input class="text_boxes" type="text" style="width:370px;"  name="txt_attention" id="txt_attention" />
                        <input type="hidden" class="image_uploader" style="width:120px" value="Lab DIP No" onClick="openmypage('requires/short_fabric_booking_controller.php?action=lapdip_no_popup','Lapdip No','lapdip')">
                    </td>
                    <td>Ready To Approved</td>
                    <td><?=create_drop_down( "cbo_ready_to_approved", 130, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>
                </tr>
                <tr>
                    <td>Remarks</td>
                    <td colspan="3"><textarea class="text_area" type="text" maxlength="300" style="width:370px; height:30px;" name="txt_remark" id="txt_remark" placeholder="Remarks"/></textarea></td>
                    <td>Delivery Address</td>
                    <td colspan="3"><textarea id="delivery_address" class="text_area" style="width:370px; height:30px;" placeholder="Delivery Address" title="Allowed Characters: abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-,%@!/<>?+[]{};: "></textarea></td>
                    <td align="center" colspan="2">
						<?
                        include("../../terms_condition/terms_condition.php");
                        terms_condition(88,'txt_booking_no','../../');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="10" valign="top" id="app_sms2" style="font-size:18px; color:#F00"></td>
                </tr>
                <tr>
                    <td align="center" colspan="10" valign="middle" class="button_container">
                    <? $date=date('d-m-Y'); echo load_submit_buttons( $permission, "fnc_fabric_booking", 0,0 ,"reset_form('fabricbooking_1','','booking_list_view','cbo_pay_mode,3*cbo_booking_year,2024*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_date,".$date."')",1) ; ?>
                    </td>
                </tr>
           </table>
            </fieldset>
        </form>
        <br>
        <form name="orderdetailsentry_2" autocomplete="off" id="orderdetailsentry_2">
            <fieldset style="width:1550px;">
            <legend>Details</legend>
                <table width="1550" cellspacing="2" cellpadding="0" border="1" class="rpt_table" rules="all">
                    <thead>
                        <th width="20">SL</th>
                        <th width="80" class="must_entry_caption">PO No</th>
                        <th width="200" class="must_entry_caption">Fabric Description</th>
                        <th width="60">RD No</th>
                        <th width="60">Fabric Ref</th>
                        <th width="80" class="must_entry_caption">GMTS Color</th>
                        <th width="80">Fab.Color</th>
                        <th width="60" class="must_entry_caption">GMTS Size</th>
                        <th width="60">Item Size</th>
                        <th width="60" class="must_entry_caption">Width</th>
                        <th width="60" class="must_entry_caption">Fabric Qty</th>
                        <th width="50">Wast. %</th>
                        <th width="60">Total Fabric Qty</th>
                        <th width="50">Rate</th>
                        <th width="60">Amount</th>
                        <th width="60">RMG Qty</th>
                        <th width="70">Division</th>
                        <th width="70" class="must_entry_caption">Respon. Dept.</th>
                        <th width="70" class="must_entry_caption">Respon. Person</th>
                        <th width="70" class="must_entry_caption">Reason</th>
                        <th width="70">Remarks</th>
                        <th>&nbsp;</th>
                    </thead>
                    <tbody id="details_list">
                        <tr id="trid_1">
                            <td>
                                <input name="txtsl_1" id="txtsl_1" class="text_boxes_numeric" type="text" style="width:10px" value="1" readonly disabled/>
                                <input type="hidden" id="updateiddetails_1">
                            </td>
                            <td id="orderdropdowntd_1"><?=create_drop_down("cboorderid_1", 80, $blank_array,"", 1, "-Select-", $selected, "" ); ?></td>
                            <td id="fabricdescriptionidtd_1"><?=create_drop_down("cbofabricdescriptionid_1", 200, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                            <td id="rdnotd_1"><input name="txtrdno_1" id="txtrdno_1" class="text_boxes" type="text" placeholder="Display" style="width:50px" disabled/></td>
                            <td id="fabricreftd_1"><input name="txtfabricref_1" id="txtfabricref_1" class="text_boxes" type="text" placeholder="Display" style="width:50px" disabled/></td>
                            <td id="garmentscoloridtd_1"><?=create_drop_down("cbogarmentscolorid_1", 80, $blank_array,"", 1, "-Select-", $selected, "" ); ?></td>
                            <td id="fabriccoloridtd_1"><?=create_drop_down("cbofabriccolorid_1", 80, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                            <td id="garmentssizeidtd_1"><?=create_drop_down( "cbogarmentssizeid_1", 60, $blank_array,"", 1, "-Select-", $selected, "" ); ?></td>
                            <td id="itemsizeidtd_1"><?=create_drop_down( "cboitemsizeid_1", 60, $blank_array,"", 1, "-Select-", $selected, "" ); ?></td>
                            <td><input name="txtdiawidth_1" id="txtdiawidth_1" class="text_boxes" type="text" placeholder="Write" style="width:50px "/></td>
                            <td><input name="txtfinishqnty_1" id="txtfinishqnty_1" class="text_boxes_numeric" type="text" onChange="calculate_requirement(1);" style="width:50px"/></td>
                            <td><input name="txtprocessloss_1" id="txtprocessloss_1" class="text_boxes_numeric" type="text" onChange="calculate_requirement(1);" style="width:40px"/></td>
                            <td><input name="txtgreyqnty_1" id="txtgreyqnty_1" class="text_boxes_numeric" type="text" style="width:50px" readonly/></td>
                            <td><input name="txtrate_1" id="txtrate_1" class="text_boxes_numeric" type="text" onChange="calculate_requirement(1);" style="width:40px" /></td>
                            <td><input name="txtamount_1" id="txtamount_1" class="text_boxes_numeric" type="text" style="width:50px" readonly/></td>
                            <td><input name="txtrmgqty_1" id="txtrmgqty_1" class="text_boxes_numeric" type="text" style="width:50px" /></td>
                            <td><?=create_drop_down("cbodivisionid_1", 70, $short_division_array,"", 1, "--Select--", $selected, "" ); ?></td>
                            <td><?=create_drop_down( "cboresponsibledept_1", 70,"select id, department_name from lib_department where status_active=1 and is_deleted=0 order by  department_name", "id,department_name", 0, "", '', '', '',''); ?></td>
                            <td><input name="txtresponsibleperson_1" id="txtresponsibleperson_1" class="text_boxes" type="text" style="width:60px" /></td>
                            <td><input name="txtreason_1" id="txtreason_1" class="text_boxes" type="text" style="width:60px"/></td>
                            <td><input name="txtremarks_1" id="txtremarks_1" class="text_boxes" type="text" style="width:60px"/></td>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td align="center" colspan="22" valign="middle" class="button_container">
                                <?=load_submit_buttons( $permission, "fnc_fabric_booking_dtls", 0,0 ,"reset_form('orderdetailsentry_2','','','','')",2) ; ?>
                                <input type="button" value="Print Booking" onClick="generate_fabric_report('show_fabric_booking_report',1)"  style="width:100px; display:none;" name="print" id="print" class="formbutton" />
                                <input type="button"  value="Print Booking2" onClick="generate_fabric_report('show_fabric_booking_report3',1)"  style="width:100px; display:none;" name="print_booking3" id="print_booking3" class="formbutton" />
                                <input type="button"  value="Fabric Booking" onClick="generate_fabric_report('show_fabric_booking_report4',1)"  style="width:100px; display:none;" name="print_booking4" id="print_booking4" class="formbutton" />
                                <input type="button"  value="Print Booking Urmi" onClick="generate_fabric_report('show_fabric_booking_report_urmi',1)"  style="width:110px; display:none;" name="print_booking_urmi" id="print_booking_urmi" class="formbutton" />
                                <input type="button" value="Print 3 " onClick="generate_fabric_report('print_booking_3',1)"  style="width:130px;display:none;" name="print_booking_3" id="print_booking_3" class="formbutton" />
                                <input type="button"  value="Fabric Booking Report" onClick="generate_fabric_report('fabric_booking_report',1)"  style="width:140px; display:none;" name="fabric_booking_report" id="fabric_booking_report" class="formbutton" />
                                <input type="button"  value="Fabric Booking Report 2" onClick="generate_fabric_report('fabric_booking_report_2',1)"  style="width:140px; display:none;" name="fabric_booking_report_2" id="fabric_booking_report_2" class="formbutton" />
                                <div id="pdf_file_name" style="display: none;">
                                
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                 </table>
            </fieldset>
        </form>
        <br/>
        <fieldset style="width:1200px;">
            <legend>List View</legend>
            <table style="border:none" cellpadding="0" cellspacing="2" border="0">
                <tr align="center">
                	<td id="booking_list_view"></td>
                </tr>
            </table>
        </fieldset>
	</div>
   <div style="display:none" id="data_panel"></div>
</body>

<script>
	//set_multiselect('cbo_responsible_dept','0','0','','0');
	$( document ).ready(function() {
	load_drop_down( 'requires/short_fabric_booking_controller', document.getElementById('cbo_pay_mode').value, 'load_drop_down_suplier', 'sup_td');
	});
	jQuery("#delivery_address").keyup(function(e)
	{
		var c = String.fromCharCode(e.which);
		var evt = (e) ? e : window.event;
		var key = (evt.keyCode) ? evt.keyCode : evt.which;
		if (key == 13)
		{
			var text = $("#delivery_address").val();
			var lines = text.split(/\r|\r\n|\n/);
			var count = (lines.length*1)+1;
			document.getElementById("delivery_address").value =document.getElementById("delivery_address").value+ "\n";
			return false;
		}
		else {
			return true;
		}
	});

</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>