<?
/*-------------------------------------------- Comments
Purpose			         :  This Form Will Create Sewater Sample Requisition Entry.
Functionality	         :
JS Functions	         :
Created by		         : Md. Saidul Islam
Creation date 	         : 16/9/2019
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
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sewater Sample Requisition", "../../../", 1, 1,$unicode,'','');
$qcCons_from=return_field_value("editable","variable_order_tracking","variable_list=74 and is_deleted=0 and status_active=1","editable");

?>
<script>

  	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
  	var permission='<? echo $permission; ?>';
	var cmValidation='<?=$qcCons_from; ?>';
	//alert(cmValidation);
	var season_mandatory_arr=new Array();

	<?

	$lib_season_mandatory_arr=array();
	$season_mandatory_sql=sql_select( "select company_name, season_mandatory from variable_order_tracking where variable_list=44 and status_active=1");
	foreach($season_mandatory_sql as $key=>$value)
	{
		$lib_season_mandatory_arr[$value[csf("company_name")]]=$value[csf("season_mandatory")];
	}
	$lib_season_mandatory_arr=json_encode($lib_season_mandatory_arr); 
	echo "season_mandatory_arr = ". $lib_season_mandatory_arr . ";\n";
	?>

  	function copy_requisition(operation)
	{
		alert("After copy company name, buyer name,sample stage changing is not allowed");
		
		/*if(season_mandatory_arr[comp]==1)
		{
			var data1="cbo_sample_stage*txt_requisition_date*txt_style_name*cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_season_name*cbo_product_department*cbo_dealing_merchant";
			var data2="Sample Stage*Requisition Date*Style Name*Company Name*Location*Buyer*Season Name*Product Department*Dealing Merchant";
		}
		if(season_mandatory_arr[comp]!=1)
		{
			var data1="cbo_sample_stage*txt_requisition_date*txt_style_name*cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_product_department*cbo_dealing_merchant";
			var data2="Sample Stage*Requisition Date*Style Name*Company Name*Location*Buyer*Product Department*Dealing Merchant";
		}*/

	   
		var data1="cbo_sample_stage*txt_requisition_date*txt_style_name*cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_season_name*cbo_product_department*cbo_dealing_merchant";
		var data2="Sample Stage*Requisition Date*Style Name*Company Name*Location*Buyer*Season Name*Product Department*Dealing Merchant";
	   
	   
	   if (form_validation(data1,data2)==false)
		{
			return;
		}
		else
		{
			var data="action=copy_requisition&operation="+operation+get_submitted_data_string('txt_requisition_id*cbo_sample_stage*cbo_company_name*txt_requisition_date*txt_quotation_id*txt_style_name*cbo_location_name*cbo_buyer_name*cbo_season_name*cbo_product_department*cbo_dealing_merchant*cbo_agent*txt_buyer_ref*txt_est_ship_date*txt_remarks*cbo_ready_to_approved*update_id*cbo_sample_team*cbo_team_leader*txt_material_dlvry_date',"../../../");
			freeze_window(operation);
			http.open("POST","requires/sweater_sample_requisition_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
		 	http.onreadystatechange = copy_requisition_reponse;


		}

		$("#approvedMsg").html('');
		$('#required_fab_dtls').removeProp('disabled','');
		$('#sample_dtls').removeProp('disabled','');
		$('#required_accessories_dtls').removeProp('disabled','');
		$('#required_embellishment_dtls').removeProp('disabled','');
		$('#txt_requisition_date').removeAttr('disabled','');
		$('#cbo_location_name').attr('disabled','true');
		$('#txt_style_name').removeAttr('disabled','');
 		$('#cbo_season_name').removeAttr('disabled','');
		$('#cbo_product_department').removeAttr('disabled','');
		$('#cbo_dealing_merchant').removeAttr('disabled','');
		$('#cbo_agent').removeAttr('disabled','');
		$('#txt_buyer_ref').removeAttr('disabled','');
		
		$('#txt_est_ship_date').removeAttr('disabled','');
		$('#txt_remarks').removeAttr('disabled','');
		$('#cbo_ready_to_approved').removeAttr('disabled','');
		set_button_status(1, permission, 'fnc_sweater_sample_requisition_mst_info_reponse',1);

	}
	
	function copy_requisition_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==0 )
			{
			   show_msg(reponse[0]);
			   $("#txt_requisition_id").val(reponse[1]);
			   $("#update_id").val(reponse[2]);
			   set_button_status(1, permission, 'fnc_sweater_sample_requisition_mst_info',1);
			   
			}

			if(reponse[0]==1 )
			{
				show_msg(reponse[0]);
			}
			if(reponse[0]==10 )
			{
				show_msg(reponse[0]);
			}

			if(reponse[0]==2 )
			{
				show_msg(reponse[0]);
				reset_form('sweater_sample_requisition_1','','');
				reset_form('sample_details_1','','');
				reset_form('required_fabric_1','','');
				reset_form('required_accessories_1','','');
				reset_form('required_embellishment_1','','');
 				$("#cbo_company_name").removeAttr('disabled','');
				$("#cbo_buyer_name").removeAttr('disabled','');
				$("#cbo_dealing_merchant").removeAttr('disabled','');
				$("#cbo_sample_stage").removeAttr('disabled','');
				set_button_status(0, permission, 'fnc_sweater_sample_requisition_mst_info',1);

			}


			release_freezing();
			
			if($("#cbo_ready_to_approved").val()==1){
				//var returnValue=return_global_ajax_value(reponse[2], 'sweater_sample_requisition_mail_notification', '', '../../../auto_mail/sweater_sample_requisition_mail_notification');
				//alert(returnValue);
			}
			
			uploadFile( $("#update_id").val());
		
		
		}
	}

	

  	function fnc_sweater_sample_requisition_mst_info( operation )
	{
		
		var comp=$('#cbo_company_name').val();
 		if(operation==4)
		{
			if (form_validation('txt_requisition_id','Save Data First')==false)
			{
				alert("Save Data First");
			}
			else
			{
				print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val(), "sweater_sample_requisition_print", "requires/sweater_sample_requisition_controller" );
			}
			return;
		}
		if(operation==5)
		{
			if (form_validation('txt_requisition_id','Save Data First')==false)
			{
				alert("Save Data First");
			}
			else
			{
				print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val(), "sweater_sample_requisition_print2", "requires/sweater_sample_requisition_controller" );
			}
			 return;
		}

	   if(season_mandatory_arr[comp]==1)
	   {
	   	    var data1="cbo_sample_stage*txt_requisition_date*txt_style_name*cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_season_name*cbo_team_leader*cbo_dealing_merchant*cbo_sample_team*cbo_product_department";
	   	    var data2="Sample Stage*Requisition Date*Style Name*Company Name*Location*Buyer*Season Name*Team Leader*Dealing Merchant*Sample Team*Product Department";
	   }
	   if(season_mandatory_arr[comp]!=1)
	   {
	    	var data1="cbo_sample_stage*txt_requisition_date*txt_style_name*cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_team_leader*cbo_dealing_merchant*cbo_sample_team*cbo_product_department";
	   	    var data2="Sample Stage*Requisition Date*Style Name*Company Name*Location*Buyer*Product Department*Team Leader*Dealing Merchant*Sample Team*Product Department";
	   }
	   
	   if (form_validation(data1,data2)==false)
		{
			return;
		}
		else
		{
			if($("#sample_mst_file")[0].files.length==0 && $('#update_id').val()==''){
				alert("Please Add File in Master Part");return;	
			}
			
			var data="action=save_update_delete_mst&operation="+operation+get_submitted_data_string('txt_requisition_id*cbo_sample_stage*cbo_company_name*txt_requisition_date*txt_quotation_id*txt_style_name*cbo_location_name*cbo_buyer_name*cbo_season_name*cbo_product_department*cbo_dealing_merchant*cbo_agent*txt_buyer_ref*txt_est_ship_date*txt_remarks*update_id*cbo_ready_to_approved*txt_material_dlvry_date*cbo_sample_team*cbo_season_year*cbo_brand_id*cbo_team_leader',"../../../");
			freeze_window(operation);
			http.open("POST","requires/sweater_sample_requisition_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_sweater_sample_requisition_mst_info_reponse;
		}
	}

	function fnc_sweater_sample_requisition_mst_info_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==0 )
			{
			   show_msg(reponse[0]);
			   $("#txt_requisition_id").val(reponse[1]);
			   $("#update_id").val(reponse[2]);
			   set_button_status(1, permission, 'fnc_sweater_sample_requisition_mst_info',1);
			   
			}

			if(reponse[0]==1 )
			{
				show_msg(reponse[0]);
			}
			if(reponse[0]==10 )
			{
				show_msg(reponse[0]);
			}

			if(reponse[0]==2 )
			{
				show_msg(reponse[0]);
				reset_form('sweater_sample_requisition_1','','');
				reset_form('sample_details_1','','');
				reset_form('required_fabric_1','','');
				reset_form('required_accessories_1','','');
				reset_form('required_embellishment_1','','');
 				$("#cbo_company_name").removeAttr('disabled','');
				$("#cbo_buyer_name").removeAttr('disabled','');
				$("#cbo_dealing_merchant").removeAttr('disabled','');
				$("#cbo_sample_stage").removeAttr('disabled','');
				set_button_status(0, permission, 'fnc_sweater_sample_requisition_mst_info',1);

			}


			release_freezing();
			
			
			uploadFile( $("#update_id").val());
		
		
		}
	}


function mail_send(){
	
   if (form_validation('update_id*cbo_ready_to_approved','System Id*Ready App Yes')==false)
	{
		return;
	}
	else
	{
		var returnValue=return_global_ajax_value($("#update_id").val(), 'sweater_sample_requisition_mail_notification', '', '../../../auto_mail/sweater_sample_requisition_mail_notification');
		alert(returnValue);
	}
}
	

	function call_print_button_for_mail(mail,mail_body,type){

	 
		if (form_validation('update_id*cbo_ready_to_approved','System Id*Ready App Yes')==false)
		{
			return;
		}
		else
		{
			var returnValue=return_global_ajax_value($("#update_id").val()+'__'+mail, 'sweater_sample_requisition_mail_notification', '', '../../../auto_mail/sweater_sample_requisition_mail_notification');
			alert(returnValue);
		}	
		
	
		

	 }
	
	 


	function fnc_sample_details_info( operation )
	{

		var is_att_file=return_global_ajax_value($('#update_id').val(), 'check_attached_file', '', 'requires/sweater_sample_requisition_controller');
		if(is_att_file==0){
			alert("Please Add File in Master Part");return;	
		}
		
		var cbo_company_name=$('#cbo_company_name').val();
		var update_id=$('#update_id').val();
		var txtDeltedIdSd=$('#txtDeltedIdSd').val();
		if(update_id=="")
		{
			alert("save master part!!");
			return;
		}

		else
		{
			var row_num=$('#tbl_sample_details tr').length-1;
			var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				if (form_validation('cboSampleName_'+i+'*cboGarmentItem_'+i+'*txtColor_'+i+'*txtSampleProdQty_'+i+'*txtSubmissionQty_'+i+'*txtDelvStartDate_'+i+'*txtDelvEndDate_'+i,'Sample Name*Garment Item*Color*Sample Production Qty*Sample Submission Qty*Start Date*End Date')==false)
				{
					return;
				} 
				data_all=data_all+get_submitted_data_string('cboSampleName_'+i+'*cboGarmentItem_'+i+'*txtSmv_'+i+'*txtArticle_'+i+'*txtColor_'+i+'*txtSampleProdQty_'+i+'*txtSubmissionQty_'+i+'*txtDelvStartDate_'+i+'*txtDelvEndDate_'+i+'*txtChargeUnit_'+i+'*cboCurrency_'+i+'*updateidsampledtl_'+i+'*txtAllData_'+i,"../../../");
			}

			var data="action=save_update_delete_sample_details&operation="+operation+'&total_row='+row_num+'&update_id='+update_id+'&cbo_company_name='+cbo_company_name+'&txtDeltedIdSd='+txtDeltedIdSd+data_all;
			   //alert(data); return;
			   freeze_window(operation);
			   http.open("POST","requires/sweater_sample_requisition_controller.php", true);
			   http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			   http.send(data);
			   http.onreadystatechange = fnc_sample_details_info_reponse;
		}
	}

	function fnc_sample_details_info_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==321)
			{
				alert("Data found in fabric/Accessories/Embellishment !!");
				release_freezing();
				return;
			}
			if(reponse[0]==323)
			{
				alert("Approved Requisition Can not be Deleted!!");
				release_freezing();
				return;
			}

			if(reponse[0]==0)
			{
				show_msg(reponse[0]);
				fnc_load_tr(reponse[1],1);
		 		var upId=document.getElementById("update_id").value;
				load_drop_down( 'requires/sweater_sample_requisition_controller', upId+'_1', 'load_drop_down_required_fabric_sample_name', 'rfSampleId_1');
				load_drop_down( 'requires/sweater_sample_requisition_controller', upId+'_1', 'load_drop_down_required_fabric_gmts_item', 'rfItemId_1');
				load_drop_down( 'requires/sweater_sample_requisition_controller', upId+'_2', 'load_drop_down_required_fabric_sample_name','raSampleId_1');
				load_drop_down( 'requires/sweater_sample_requisition_controller', upId+'_2', 'load_drop_down_required_fabric_gmts_item', 'raItemId_1');
				load_drop_down( 'requires/sweater_sample_requisition_controller', upId+'_3', 'load_drop_down_required_fabric_sample_name','reSampleId_1');
				load_drop_down( 'requires/sweater_sample_requisition_controller', upId+'_3', 'load_drop_down_required_fabric_gmts_item', 'reItemIid_1');
				$("#cbo_buyer_name").attr('disabled','disabled');
			 }

			if(reponse[0]==1 )
			{
				show_msg(reponse[0]);
				fnc_load_tr(reponse[1],1);
				var upId=document.getElementById("update_id").value;
				load_drop_down( 'requires/sweater_sample_requisition_controller', upId+'_1', 'load_drop_down_required_fabric_sample_name', 'rfSampleId_1');
				load_drop_down( 'requires/sweater_sample_requisition_controller', upId+'_1', 'load_drop_down_required_fabric_gmts_item', 'rfItemId_1');
				load_drop_down( 'requires/sweater_sample_requisition_controller', upId+'_2', 'load_drop_down_required_fabric_sample_name','raSampleId_1');
				load_drop_down( 'requires/sweater_sample_requisition_controller', upId+'_2', 'load_drop_down_required_fabric_gmts_item', 'raItemId_1');
				load_drop_down( 'requires/sweater_sample_requisition_controller', upId+'_3', 'load_drop_down_required_fabric_sample_name','reSampleId_1');
				load_drop_down( 'requires/sweater_sample_requisition_controller', upId+'_3', 'load_drop_down_required_fabric_gmts_item', 'reItemIid_1');
				$("#cbo_buyer_name").attr('disabled','disabled');


			}
			
			if(reponse[0]==2)
			{

				show_msg(reponse[0]);
				button_status(2);
 				//window.location.reload();
			}
			else if(reponse[0]==10 )
			{
				show_msg(reponse[0]);
			}
			release_freezing();
			
			var addDays = return_global_ajax_value( $('#update_id').val()+'__1'+'____'+$('#cbo_company_name').val(), 'update_delivery_date', '', 'requires/sweater_sample_requisition_controller');
			if(cmValidation==1)
	   		{
				auto_acknowledge($('#update_id').val());
	   		}

			
			

		}
	}
	function fnc_required_yarn_details_info( operation )
	{
		var is_att_file=return_global_ajax_value($('#update_id').val(), 'check_attached_file', '', 'requires/sweater_sample_requisition_controller');
		if(is_att_file==0){
			alert("Please Add File in Master Part");return;	
		}
			
			
		var update_id=$('#update_id').val()*1;
		var txtDeltedIdRf=$('#txtDeltedIdRf').val();

		if(update_id=="")
		{
			alert("save master part!!");
			return;
		}
		else
		{
			var row_nums=$('#tbl_required_yarn tr').length-1;
			var data_all="";
			for (var i=1; i<=row_nums; i++)
			{
				if (form_validation('cboRfSampleName_'+i+'*cboRfGarmentItem_'+i+'*cboRfBodyPart_'+i+'*cboRfFabricNature_'+i+'*txtRfFabricDescription_'+i+'*txtRfGauge_'+i+'*txtRfColorAllData_'+i+'*cboRfColorType_'+i+'*cboRfUom_'+i+'*txtRfReqQty_'+i,'Sample Name*Garment Item*Body Part*Fabric Nature*Fabric Desc*Gauge*Browse Color*Color Type*Uom*ReqQty')==false)
				{
					return;
				}
			
			data_all=data_all+get_submitted_data_string('cboRfSampleName_'+i+'*txtRfDevelopmentNo_'+i+'*cboRfGarmentItem_'+i+'*cboRfBodyPart_'+i+'*cboRfFabricNature_'+i+'*txtRfFabricDescription_'+i+'*txtRfGauge_'+i+'*txtRfColor_'+i+'*cboRfColorType_'+i+'*cboRfUom_'+i+'*txtRfRemarks_'+i+'*txtRfReqQty_'+i+'*updateidRequiredDtl_'+i+'*txtRfColorAllData_'+i+'*libyarncountdeterminationid_'+i+'*cboRfGmtsColorId_'+i+'*txtBuyerProv_'+i+'*txtNoOfEnds_'+i,"../../../");
			}
			//alert(data_all);
			
			var data="action=save_update_delete_required_fabric&operation="+operation+'&total_row='+row_nums+'&update_id='+update_id+'&txtDeltedIdRf='+txtDeltedIdRf+data_all;
			freeze_window(operation);
			http.open("POST","requires/sweater_sample_requisition_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_required_yarn_details_info_response;
		}
	}

	function fnc_required_yarn_details_info_response()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==321)
			{
				alert("Data found in booking .Delete Restricted!!");
				release_freezing();
				return;
			}
			if(reponse[0]==323)
			{
				alert("Approved Requisition Can not be Deleted!!");
				release_freezing();
				return;
			}
			if(reponse[0]==0)
			{
				show_msg(reponse[0]);
				fnc_load_tr(reponse[1],2);

 			 }

			if(reponse[0]==1 )
			{
				show_msg(reponse[0]);
				fnc_load_tr(reponse[1],2);
  				var upId=document.getElementById("update_id").value;
 			}
 			if(reponse[0]==2)
			{
				show_msg(reponse[0]);
				button_status(3);
 			}
			else if(reponse[0]==10 )
			{
				show_msg(reponse[0]);
			}
			release_freezing();
			
		}
	}

	function fnc_required_accessories_info( operation )
	{
		
		var is_att_file=return_global_ajax_value($('#update_id').val(), 'check_attached_file', '', 'requires/sweater_sample_requisition_controller');
		if(is_att_file==0){
			alert("Please Add File in Master Part");return;	
		}

		var update_id=$('#update_id').val();
		var txtDeltedIdRa=$('#txtDeltedIdRa').val();
		if(update_id=="")
		{
			alert("save master part!!");
			return;
		}

		else
		{
			var row_num=$('#tbl_required_accessories tr').length-1;

			var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				if (form_validation('cboRaSampleName_'+i+'*cboRaGarmentItem_'+i+'*cboRaTrimsGroup_'+i+'*txtRaDescription_'+i+'*cboRaUom_'+i+'*txtRaReqDzn_'+i+'*txtRaReqQty_'+i,'Sample Name*Garment Item*Trims Group*Description*Uom*ReqDzn*ReqQty')==false)
				{
					return;
				}
				data_all=data_all+get_submitted_data_string('cboRaSampleName_'+i+'*cboRaGarmentItem_'+i+'*cboRaTrimsGroup_'+i+'*txtRaDescription_'+i+'*txtRaBrandSupp_'+i+'*cboRaUom_'+i+'*txtRaReqDzn_'+i+'*txtRaReqQty_'+i+'*txtRaRemarks_'+i+'*updateidAccessoriesDtl_'+i+'*txtRaexQty_'+i,"../../../");
			}
			var data="action=save_update_delete_required_accessories&operation="+operation+'&total_row='+row_num+'&update_id='+update_id+'&txtDeltedIdRa='+txtDeltedIdRa+data_all;
			freeze_window(operation);
			http.open("POST","requires/sweater_sample_requisition_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_required_accessories_info_reponse;
		}
	}

	function fnc_required_accessories_info_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==323)
			{
				alert("Approved Requisition Can not be Deleted!!");
				release_freezing();
				return;
			}

			if(reponse[0]==0)
			{
				show_msg(reponse[0]);
				fnc_load_tr(reponse[1],3);
				//fnc_load_tr(reponse[1],1);
 			}

			if(reponse[0]==1 )
			{
				show_msg(reponse[0]);
				fnc_load_tr(reponse[1],3);
				fnc_load_tr(reponse[1],1);
			}
			if(reponse[0]==2)
			{
				show_msg(reponse[0]);
				button_status(4);
			}
			else if(reponse[0]==10 )
			{
				show_msg(reponse[0]);
			}
			release_freezing();
		}
	}

	function fnc_required_embellishment_info( operation )
	{
		var is_att_file=return_global_ajax_value($('#update_id').val(), 'check_attached_file', '', 'requires/sweater_sample_requisition_controller');
		if(is_att_file==0){
			alert("Please Add File in Master Part");return;	
		}
		var cbo_company_name=$('#cbo_company_name').val();
		var update_id=$('#update_id').val();
		var txtDeltedIdRe=$('#txtDeltedIdRe').val();
		if(update_id=="")
		{
			alert("save master part!!");
			return;
		}


		else
		{
			var row_num=$('#tbl_required_embellishment tr').length-1;

			var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				if (form_validation('cboReSampleName_'+i+'*cboReGarmentItem_'+i+'*cboReName_'+i+'*cboReType_'+i,'Sample Name*Garment Item*Name*Type')==false)
				{
					return;
				}
				data_all=data_all+get_submitted_data_string('cboReSampleName_'+i+'*cboReGarmentItem_'+i+'*cboReName_'+i+'*cboReType_'+i+'*txtReRemarks_'+i+'*updateidRequiredEmbellishdtl_'+i,"../../../");
			}
			var data="action=save_update_delete_required_embellishment&operation="+operation+'&total_row='+row_num+'&update_id='+update_id+'&cbo_company_name='+cbo_company_name+'&txtDeltedIdRe='+txtDeltedIdRe+data_all;
			freeze_window(operation);
			http.open("POST","requires/sweater_sample_requisition_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_required_embellishment_info_reponse;
		}
	}

	function fnc_required_embellishment_info_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');

			if(reponse[0]==323)
			{
				alert("Approved Requisition Can not be Deleted!!");
				release_freezing();
				return;
			}
			if(reponse[0]==0)
			{
			   	show_msg(reponse[0]);
			    fnc_load_tr(reponse[1],4);
 			}

			if(reponse[0]==1 )
			{
				show_msg(reponse[0]);
 				fnc_load_tr(reponse[1],4);
 				//fnc_load_tr(reponse[1],1);
 			}
 			if(reponse[0]==2)
			{
			   	//alert(22);
			   	show_msg(reponse[0]);
			    button_status(5);
 			}
			else if(reponse[0]==10 )
			{
				show_msg(reponse[0]);
 			}
			release_freezing();
			var addDays = return_global_ajax_value( $('#update_id').val()+'__1'+'____'+$('#cbo_company_name').val(), 'update_delivery_date', '', 'requires/sweater_sample_requisition_controller');
			if(cmValidation==1)
	   		{
				auto_acknowledge($('#update_id').val());
	   		}
			

		}
	}

	function fnc_load_tr(up_id,type) //after save of details part or browse requisition this function load all saved data from db in specific tr
	{
		if(type==1)
		{
				var data=up_id+'**'+type;
 	 			var list_view_tr = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sweater_sample_requisition_controller');
				if(list_view_tr=="" || list_view_tr==0)
				{
 				//	$("#tbl_sample_details tbody tr").clone().appendTo("#sample_details_container");
				 //  $('#tbl_sample_details tbody tr:first').remove();
  					set_button_status(0, permission, 'fnc_sample_details_info',2);
					return;
				}
				else(list_view_tr!='')
				{
					$("#sample_details_container tr").remove();
					$("#sample_details_container").append(list_view_tr);
					set_all_onclick();
					set_button_status(1, permission, 'fnc_sample_details_info',2);
				//	list_view_tr='';
 					return;
				}

			return;
		}
		else  if(type==2)
		{
			var data=up_id+'**'+type;
			var list_view_tr = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sweater_sample_requisition_controller');
			if(list_view_tr=="" || list_view_tr==0)
			{

				set_button_status(0, permission, 'fnc_required_yarn_details_info',3,0);
				return;
			}
			else (list_view_tr!='')
			{
				$("#required_fabric_container tr").remove();
				$("#required_fabric_container").append(list_view_tr);
				set_all_onclick();
				//set_button_status(1, permission, 'fnc_required_yarn_details_info',3,0);
				
				//alert(($('#submit_button_status').val()*1));
				if($('#cboRfBodyPart_1').val()>0){
					set_button_status(1, permission, 'fnc_required_yarn_details_info',3,0);
				}
				else
				{
					set_button_status(0, permission, 'fnc_required_yarn_details_info',3,0);
				}
				

				return;
			}

		}

		else if(type==3)
		{
			var data=up_id+'**'+type;
			var list_view_tr = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sweater_sample_requisition_controller');
			if(list_view_tr=="" || list_view_tr==0)
			{
				set_button_status(0, permission, 'fnc_required_accessories_info',4,0);
				return;
			}
			else(list_view_tr!='')
			{
				$("#required_accessories_container tr").remove();
				$("#required_accessories_container").append(list_view_tr);
				set_all_onclick();
				set_button_status(1, permission, 'fnc_required_accessories_info',4,0);
				return;
			}

		}

		else if(type==4)
		{
			var data=up_id+'**'+type;
			var list_view_tr = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sweater_sample_requisition_controller');
			if(list_view_tr=="" || list_view_tr==0)
			{

				set_button_status(0, permission, 'fnc_required_embellishment_info',5,0);
				return;
			}
			else(list_view_tr!='')
			{
				$("#required_embellishment_container tr").remove();
				$("#required_embellishment_container").append(list_view_tr);
				set_all_onclick();
				set_button_status(1, permission, 'fnc_required_embellishment_info',5,0);
				return;
			}

		}

	}

	function openmypage_sizeinfo(page_link,title,inc) // this function for sample details- sample qty browse
	{
		var txt_style_id = $('#update_id').val();
		var update_id_dtl = $('#updateidsampledtl_'+inc).val();
		if (txt_style_id=='')
		{
			alert("Save Master Part First!!");
			return;
		}
		else
		{
			get_php_form_data(update_id_dtl, "load_data_to_sizeinfo","requires/sweater_sample_requisition_controller" );
			var data=$('#txtAllData_'+inc).val();
			var title = 'Size Info';
			var page_link = page_link +'&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=400px,center=1,resize=1,scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var receive_all_data=this.contentDoc.getElementById("hidden_size_data").value;
				var total_bh_qty=this.contentDoc.getElementById("txt_total_bh_qty").value;
				var receive_self_and_total_data=this.contentDoc.getElementById("hidden_total_self_and_all_data").value;
				if(receive_all_data!="")
				{

					$('#txtAllData_'+inc).val( receive_all_data );
					var res=receive_self_and_total_data.split('___');
					var submission_qty=res[1]-res[0];
					$('#txtSampleProdQty_'+inc).val(res[1]);
					//$('#txtSubmissionQty_'+inc).val(submission_qty);
					$('#txtSubmissionQty_'+inc).val(total_bh_qty);
				}

			}
		}

	}
	function check_sample_stage() // this function will check whether the sample is after order place or not
	{
		var sampleStage=document.getElementById("cbo_sample_stage").value;
		if(sampleStage==1)
		{
			openmypage(1);
		}
		else if(sampleStage==2)
		{
			openmypage(2);
		}
	}


	function button_status(type)
	{
		if(type==1)
		{
			reset_form('sweater_sample_requisition_1','','');
			set_button_status(0, permission, 'fnc_sweater_sample_requisition_mst_info',1);
			window.location.reload();
		}
		else if(type==2)
		{
			reset_form('sample_details_1','','');
			set_button_status(0, permission, 'fnc_sample_details_info',2);
		}
		else if(type==3)
		{
			reset_form('required_fabric_1','','');
			set_button_status(0, permission, 'fnc_required_yarn_details_info',3);
		}

		else if(type==4)
		{
			reset_form('required_accessories_1','','');
			set_button_status(0, permission, 'fnc_required_accessories_info',4);
		}
		else if(type==5)
		{
			reset_form('required_embellishment_1','','');
			set_button_status(0, permission, 'fnc_required_embellishment_info',5);
		}
	}

	function auto_sampleDtls_color_generate(inc)
	{
		var sampleName=$('#cboRfSampleName_'+inc).val();
		var garmentItem=$('#cboRfGarmentItem_'+inc).val();
		var update_id=$('#update_id').val();
		var data=sampleName+"***"+garmentItem+"***"+update_id;
		var result = return_global_ajax_value( data, 'auto_sd_color_generation', '', 'requires/sweater_sample_requisition_controller');
		$('#txtRfColorAllData_'+inc).val(result);
	}

	function fnc_browse_style(data)
	{
		if(data==1)
		{
			$("#cbo_company_name").attr('disabled','disabled');
			$("#cbo_location_name").attr('disabled','disabled');
			$("#cbo_buyer_name").attr('disabled','disabled');
			$("#txt_style_name").attr('readonly','readonly');
			$("#cbo_dealing_merchant").attr('disabled','disabled');
			$("#cbo_product_department").attr('disabled','disabled');
			$("#cbo_agent").attr('disabled','disabled');
			//$('#txt_style_name').removeAttr('placeholder','placeholder');
			$('#txt_style_name').attr('placeholder','Browse');
			$('#txt_style_name').attr("ondblclick","check_sample_stage()");
	 	}
		else if(data==2)
		{
	 		$("#cbo_company_name").removeAttr('disabled','');
			$("#cbo_location_name").removeAttr('disabled','');
			$("#txt_style_name").removeAttr('readonly','');
			$("#cbo_buyer_name").removeAttr('disabled','');
			$("#cbo_season_name").removeAttr('disabled','');
			$("#cbo_dealing_merchant").removeAttr('disabled','');
			$("#cbo_product_department").removeAttr('disabled','');
			$("#cbo_agent").removeAttr('disabled','');
			$("#txt_est_ship_date").removeAttr('disabled','');
			$("#txt_remarks").removeAttr('disabled','');
			$('#txt_style_name').attr('placeholder','Write/Browse');
			$('#txt_style_name').attr("ondblclick","check_sample_stage()");
			
	 	}
		else
		{

			$("#cbo_company_name").removeAttr('disabled','');
			$("#cbo_location_name").removeAttr('disabled','');
			$("#txt_style_name").removeAttr('readonly','');
			$("#cbo_buyer_name").removeAttr('disabled','');
			$("#cbo_season_name").removeAttr('disabled','');
			$("#cbo_dealing_merchant").removeAttr('disabled','');
			$("#cbo_product_department").removeAttr('disabled','');
			$("#cbo_agent").removeAttr('disabled','');
			$("#txt_est_ship_date").removeAttr('disabled','');
			$("#txt_remarks").removeAttr('disabled','');
			$('#txt_style_name').attr('placeholder','Write/Browse');
			$('#txt_style_name').removeAttr("ondblclick");

		}
	}

	function open_fabric_description_popup(i)
	{
		var cbofabricnature=document.getElementById('cboRfFabricNature_'+i).value;
		var libyarncountdeterminationid =document.getElementById('libyarncountdeterminationid_'+i).value
		var page_link='requires/sweater_sample_requisition_controller.php?action=fabric_description_popup&fabric_nature='+cbofabricnature+'&libyarncountdeterminationid='+libyarncountdeterminationid;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Yarn Description', 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var fab_des_id=this.contentDoc.getElementById("fab_des_id");
			//var fab_nature_id=this.contentDoc.getElementById("fab_nature_id");
			var fab_desctiption=this.contentDoc.getElementById("fab_desctiption");
			var fab_gsm=this.contentDoc.getElementById("fab_gsm");
			document.getElementById('libyarncountdeterminationid_'+i).value=fab_des_id.value;
			document.getElementById('txtRfFabricDescription_'+i).value=fab_desctiption.value;
			document.getElementById('txtRfGauge_1'+i).value=fab_gsm.value;
		}
	}

	function required_fab_remarks(i)
	{
		var existing_rem=$("#txtRfRemarks_"+i).val();
		//alert(existing_rem);
		var page_link='requires/sweater_sample_requisition_controller.php?action=fabric_remarks_popup&existing_rem='+existing_rem;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Remarks', 'width=550px,height=250px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
			{
				var remarks=this.contentDoc.getElementById("remarks_hidden");
 				document.getElementById('txtRfRemarks_'+i).value=remarks.value;

			}
	}



	function add_break_down_tr(i)
	{
		var row_num=$('#tbl_sample_details tbody tr').length;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			var k=i-1;
			$("#tbl_sample_details tbody tr:last").clone().find("input,select").each(function(){
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
					'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_sample_details");

			$('#cboSampleName_'+i).val($('#cboSampleName_'+k).val());
			$('#cboGarmentItem_'+i).val($('#cboGarmentItem_'+k).val());
			$('#updateidsampledtl_'+i).val('');
			/*$('#txtAllData_'+i).val('');
			$('#txtColor_'+i).val('');
			$('#txtSize_'+i).val('');
			$('#txtSampleProdQty_'+i).val('');
			$('#txtSubmissionQty_'+i).val('');
			$('#txtChargeUnit_'+i).val('');*/
			
			$('#txtColor_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_color_size('requires/sweater_sample_requisition_controller.php?action=color_popup','Color Search','1','"+i+"');");
			$('#txtSampleProdQty_'+i).removeAttr("onfocus");


			$('#txtSampleProdQty_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_sizeinfo('requires/sweater_sample_requisition_controller.php?action=sizeinfo_popup','Size Search','"+i+"')");

			$('#txtFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../../',document.getElementById('updateidsampledtl_"+i+"').value,'', 'sample_details_1', 0 ,1);");

			//$('#cboSampleName_'+i).removeAttr("disabled","");
			//$('#cboGarmentItem_'+i).removeAttr("disabled","");
			$('#txtSampleProdQty_'+i).removeAttr("disabled","");
			$('#txtDelvStartDate_'+i).removeAttr("class").attr("class","datepicker");
			$('#txtDelvStartDate_'+i).attr("disabled","disabled");
			
			$('#txtDelvEndDate_'+i).removeAttr("class").attr("class","datepicker");
			$('#increase_'+i).removeAttr("value").attr("value","+");
			$('#decrease_'+i).removeAttr("value").attr("value","-");
			$('#decrease_'+i).removeAttr("disabled");
			$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
			set_all_onclick();
		}
	}

	function fn_deleteRow(rowNo)
	{
		var index=rowNo-1;
		$("table#tbl_sample_details tbody tr:eq("+index+")").remove();
		var numRow = $('table#tbl_sample_details tbody tr').length;
		for(i = rowNo;i <= numRow;i++)
		{
			$("#tbl_sample_details tr:eq("+i+")").find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'value': function(_, value) { return value }
				});

				$('#txtColor_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_color_size('requires/sweater_sample_requisition_controller.php?action=color_popup','Color Search','1','"+i+"');");
				$('#txtSampleProdQty_'+i).removeAttr("onfocus");


				$('#txtSampleProdQty_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_sizeinfo('requires/sweater_sample_requisition_controller.php?action=sizeinfo_popup','Size Search','"+i+"')");

				$('#txtFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../../',document.getElementById('updateidsampledtl_"+i+"').value,'', 'sample_details_1', 0 ,1);");


				$('#txtDelvStartDate_'+i).removeAttr("class").attr("class","datepicker");
				$('#txtDelvEndDate_'+i).removeAttr("class").attr("class","datepicker");
				$('#increase_'+i).removeAttr("value").attr("value","+");
				$('#decrease_'+i).removeAttr("value").attr("value","-");
				$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
				set_all_onclick();

			});
        }


	}

	function add_rf_tr(i)
	{
		var row_num=$('#tbl_required_yarn tbody tr').length;
		if (row_num!=i)
		{
			return false;
		}

		else
		{
			i++;
			var k=i-1;
			$("#tbl_required_yarn tbody tr:last").clone().find("input,select").each(function(){
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
					'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_required_yarn");
			$('#cboRfSampleName_'+i).val($('#cboRfSampleName_'+k).val());
			$('#cboRfGarmentItem_'+i).val($('#cboRfGarmentItem_'+k).val());
			$('#cboRfColorType_'+i).val($('#cboRfColorType_'+k).val());
			$('#updateidRequiredDtl_'+i).val('');
			$('#cboRfBodyPart_'+i).val('');
			$('#cboRfFabricNature_'+i).val('');
			$('#txtRfFabricDescription_'+i).val('');
			$('#txtRfGauge_1'+i).val('');
			$('#txtRfColor_'+i).val('');
			//$('#cboRfColorType_'+i).val('');
			//$('#cboRfWidthDia_'+i).val('');
			$('#cboRfUom_'+i).val($('#cboRfUom_'+k).val());
			$('#txtRfReqQty_'+i).val('');
			$('#txtRfColorAllData_'+i).val('');
			$('#txtRfRemarks_'+i).val('');
			$('#txtMemoryDataRf_'+i).val('');
			$('#cboRfBodyPart_'+i).val('');
			$('#cboRfBodyPartname_'+i).val('');


			$('#txtRfRemarks_'+i).removeAttr("onclick").attr("onclick","required_fab_remarks("+i+")");
			$('#cboRfBodyPartname_'+i).removeAttr("onDblClick").attr("onDblClick","open_body_part_popup("+i+")");

			$('#cboRfBodyPart_'+i).removeAttr("onchange").attr("onchange","load_data_to_rfcolor('"+i+"')");

			$('#txtRfColor_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_rf_color('requires/sweater_sample_requisition_controller.php?action=color_popup_rf','Color Search','"+i+"');");

			$('#txtRfFabricDescription_'+i).removeAttr("onDblClick").attr("onDblClick","open_fabric_description_popup("+i+")");

			$('#txtRfFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../../',document.getElementById('updateidRequiredDtl_"+i+"').value,'', 'required_fabric_1', 0 ,1);");

			$('#increaserf_'+i).removeAttr("value").attr("value","+");
			$('#decreaserf_'+i).removeAttr("value").attr("value","-");
			$('#increaserf_'+i).removeAttr("onclick").attr("onclick","add_rf_tr("+i+");");
			$('#decreaserf_'+i).removeAttr("onclick").attr("onclick","fn_rf_deleteRow("+i+");");
			set_all_onclick();

		}
	}

	function fn_rf_deleteRow(rowNo)
	{

		var index=rowNo-1
		$("table#tbl_required_yarn tbody tr:eq("+index+")").remove();
		var numRow = $('table#tbl_required_yarn tbody tr').length;
		for(i = rowNo;i <= numRow;i++)
		{
			$("#tbl_required_yarn tr:eq("+i+")").find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'value': function(_, value) { return value }
				});
				//$("#tbl_required_yarn tr:last").removeAttr('id').attr('id','fabriccosttbltr_'+i);
				$('#cboRfBodyPart_'+i).removeAttr("onchange").attr("onchange","load_data_to_rfcolor('"+i+"')");

				$('#txtRfColor_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_rf_color('requires/sweater_sample_requisition_controller.php?action=color_popup_rf','Color Search','"+i+"');");

				$('#txtRfFabricDescription_'+i).removeAttr("onDblClick").attr("onDblClick","open_fabric_description_popup("+i+")");

				$('#txtRfFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../../',document.getElementById('updateidRequiredDtl_"+i+"').value,'', 'required_fabric_1', 0 ,1);");

				$('#increaserf_'+i).removeAttr("value").attr("value","+");
				$('#decreaserf_'+i).removeAttr("value").attr("value","-");
				$('#increaserf_'+i).removeAttr("onclick").attr("onclick","add_rf_tr("+i+");");
				$('#decreaserf_'+i).removeAttr("onclick").attr("onclick","fn_rf_deleteRow("+i+");");
				set_all_onclick();

			});
        }




	}

	function add_ra_tr(i)
	{
		var row_num=$('#tbl_required_accessories tbody tr').length;

		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			var k=i-1;
			$("#tbl_required_accessories tbody tr:last").clone().find("input,select").each(function(){
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
					'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_required_accessories");
			$('#cboRaSampleName_'+i).val($('#cboRaSampleName_'+k).val());
			$('#cboRaGarmentItem_'+i).val($('#cboRaGarmentItem_'+k).val());
			$('#updateidAccessoriesDtl_'+i).val('');
			$('#cboRaTrimsGroup_'+i).val('');
			$('#txtRaDescription_'+i).val('');
			$('#txtRaBrandSupp_'+i).val('');
			$('#cboRaUom_'+i).val($('#cboRaUom_'+k).val());
			$('#txtRaReqDzn_'+i).val('');
			$('#txtRaReqQty_'+i).val('');
			$('#txtRaexQty_'+i).val('');
			$('#txtRaRemarks_'+i).val('');
			$('#txtMemoryDataRa_'+i).val('');


			$('#txtRaReqDzn_'+i).removeAttr("onblur").attr("onblur","calculate_required_qty('2','"+i+"')");
			$('#txtRaexQty_'+i).removeAttr("onblur").attr("onblur","calculate_required_qty('3','"+i+"')");
			$('#cboRaTrimsGroup_'+i).removeAttr("onchange").attr("onchange","load_uom_for_trims('"+i+"',this.value);");
			$('#txtRaFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../../',document.getElementById('updateidAccessoriesDtl_"+i+"').value,'', 'required_accessories_1', 0 ,1);");

			$('#increasera_'+i).removeAttr("value").attr("value","+");
			$('#decreasera_'+i).removeAttr("value").attr("value","-");
			$('#increasera_'+i).removeAttr("onclick").attr("onclick","add_ra_tr("+i+");");
			$('#decreasera_'+i).removeAttr("onclick").attr("onclick","fn_ra_deleteRow("+i+");");
			set_all_onclick();
		}
	}

	function fn_ra_deleteRow(rowNo)
	{
		if($('#tbl_required_accessories').val()!=2)
		{
			var numRow = $('#tbl_required_accessories tbody tr').length;
			var k=rowNo-1;
			if(numRow==rowNo && rowNo!=1)
			{
				var updateIdDtls=$('#updateidAccessoriesDtl_'+rowNo).val();
				var txt_deleted_id=document.getElementById("txtDeltedIdRa").value;
				var selected_id='';
				if(updateIdDtls!='')
				{
					if(txt_deleted_id=='')
					{
						selected_id=updateIdDtls;
					}

					else
					{
						selected_id=txt_deleted_id+','+updateIdDtls;
					}

				}
				document.getElementById("txtDeltedIdRa").value=selected_id;
				$('#tbl_required_accessories tbody tr:last').remove();

			}
			else
			{
				return false;
			}
		}
	}

	function add_re_tr(i)
	{
		var row_num=$('#tbl_required_embellishment tbody tr').length;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			var k=i-1;
			$("#tbl_required_embellishment tbody tr:last").clone().find("input,select").each(function(){
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
					'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_required_embellishment");
			$("#tbl_required_embellishment tbody tr:last td:nth-child(4)").removeAttr('id').attr('id','reType_'+i);
			$('#updateidRequiredEmbellishdtl_'+i).val('');
			$('#cboReSampleName_'+i).val($('#cboReSampleName_'+k).val());
			$('#cboReGarmentItem_'+i).val($('#cboReGarmentItem_'+k).val());
			$('#cboReName_'+i).val('');
			$('#cboReType_'+i).val('');
			$('#txtReRemarks_'+i).val('');
			$('#reTxtFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../../', document.getElementById('updateidRequiredEmbellishdtl_"+i+"').value,'', 'required_embellishment_1', 0 ,1);");
			$('#increasere_'+i).removeAttr("value").attr("value","+");
			$('#decreasere_'+i).removeAttr("value").attr("value","-");
			$('#increasere_'+i).removeAttr("onclick").attr("onclick","add_re_tr("+i+");");
			$('#decreasere_'+i).removeAttr("onclick").attr("onclick","fn_re_deleteRow("+i+");");
			$('#cboReName_'+i).removeAttr("onChange").attr("onChange","cbotype_loder( "+i+" )");
			set_all_onclick();
		}
	}

	function cbotype_loder( i )
	{
		var cboembname=document.getElementById('cboReName_'+i).value;
		load_drop_down( 'requires/sweater_sample_requisition_controller', cboembname+'_'+i, 'load_drop_down_emb_type', 'reType_'+i );
	}

	function fn_re_deleteRow(rowNo)
	{
		if($('#tbl_required_embellishment').val()!=2)
		{
			var numRow = $('#tbl_required_embellishment tbody tr').length;
			var k=rowNo-1;
			if(numRow==rowNo && rowNo!=1)
			{
				var updateIdDtls=$('#updateidRequiredEmbellishdtl_'+rowNo).val();
				var txt_deleted_id=document.getElementById("txtDeltedIdRe").value;
				var selected_id='';
				if(updateIdDtls!='')
				{
					if(txt_deleted_id=='')
					{
						selected_id=updateIdDtls;
					}

					else
					{
						selected_id=txt_deleted_id+','+updateIdDtls;
					}

				}
				document.getElementById("txtDeltedIdRe").value=selected_id;
				$('#tbl_required_embellishment tbody tr:last').remove();


			}
			else
			{
				return false;
			}
		}
	}

	function openmypage(sample_stage)
	{
		if(sample_stage==1)
		{
			var title = 'Style ID Search';
			var page_link = 'requires/sweater_sample_requisition_controller.php?&action=style_id_popup&company='+$("#cbo_company_name").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=1,scrolling=0','../../../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var mst_tbl_id=this.contentDoc.getElementById("selected_job").value;//mst id

				if (mst_tbl_id!="")
				{
					freeze_window(5);
					get_php_form_data(mst_tbl_id, "populate_data_from_search_popup", "requires/sweater_sample_requisition_controller" );
					$("#cbo_company_name").attr('disabled','disabled');
					$("#cbo_location_name").attr('disabled','disabled');
					$("#cbo_buyer_name").attr('disabled','disabled');
					$("#cbo_dealing_merchant").attr('disabled','disabled');
					$("#cbo_product_department").attr('disabled','disabled');
					var browse_job_no=$('#txt_quotation_job_no').val();
					load_drop_down( 'requires/sweater_sample_requisition_controller', '', 'load_drop_down_trims_group_from_budget_for_after_order', 'ra_trims_group_1');
					release_freezing();
				}
			}
		}

		if(sample_stage==2)
		{

			var company = $("#cbo_company_name").val();
			var page_link='requires/sweater_sample_requisition_controller.php?action=inquiry_popup&company='+company;
			var title="Search  Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=980px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var inquiryId=this.contentDoc.getElementById("txt_inquiry_id").value; // mrr number
				get_php_form_data(inquiryId, "populate_data_from_inquiry_search", "requires/sweater_sample_requisition_controller");
				$("#cbo_company_name").attr('disabled','disabled');
				$("#cbo_buyer_name").attr('disabled','disabled');
				$("#cbo_company_name").attr('disabled','disabled');
				$("#cbo_dealing_merchant").attr('disabled','disabled');
	 			//$("#cbo_agent").attr('disabled','disabled');
	 			$("#txt_est_ship_date").attr('disabled','disabled');

	 		}
	 	}
	}
	function openmypage_color_size(page_link,title,type,inc)
	{
		var sampleStage=$("#cbo_sample_stage").val();
		if(sampleStage==1)
		{
			var style_db_id=document.getElementById('txt_quotation_id').value;
			if(type==1) // for sample details color
			{
				var page_link = page_link + "&style_db_id="+style_db_id;
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=400px,center=1,resize=1,scrolling=0','../');
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0];
					var col_name=this.contentDoc.getElementById("txt_color_name").value;//mst id

					if (col_name!="")
					{
						$("#txtColor_"+inc).val( col_name );
						freeze_window(5);
						release_freezing();
					}
				}
			}

			if(type==2) // for required fabric color
			{
				var page_link = page_link + "&style_db_id="+style_db_id;
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=400px,center=1,resize=1,scrolling=0','../');
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0];
					var col_name=this.contentDoc.getElementById("txt_color_name").value;//mst id

					if (col_name!="")
					{
						$("#txtRfColor_"+inc).val( col_name );
						freeze_window(5);
						release_freezing();
					}
				}
			}

		}
		else
		{
				//var style_db_id=document.getElementById('txt_quotation_id').value;
				var style_db_id="";
				var page_link = page_link + "&style_db_id="+style_db_id;
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=400px,center=1,resize=1,scrolling=0','../');
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0];
					var col_name=this.contentDoc.getElementById("txt_color_name").value;//mst id

					if (col_name!="")
					{
						$("#txtColor_"+inc).val( col_name );
						freeze_window(5);
						release_freezing();
					}
				}
		}
	}


	function openmypage_rf_color_backup(page_link,title,inc)
	{

		var sampleName=$('#cboRfSampleName_'+inc).val();
		var garmentItem=$('#cboRfGarmentItem_'+inc).val();
		var mainId=$('#update_id').val();
		var dtlId=$('#updateidRequiredDtl_'+inc).val();
		var data=$('#txtRfColorAllData_'+inc).val();
		//	alert(data);
		var page_link = page_link + "&sampleName="+sampleName+ "&garmentItem="+garmentItem+'&data='+data+'&mainId='+mainId+'&dtlId='+dtlId;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=250px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{

			var receive_all_data=this.contentDoc.getElementById("txtRfColorAllData").value;
			var color=this.contentDoc.getElementById("displayAllcol").value;
			if(receive_all_data!="")
			{
				$('#txtRfColorAllData_'+inc).val( receive_all_data );
				$("#txtRfColor_"+inc).val(color);
			}

		}


	}
	
	
	function openmypage_rf_color(page_link,title,inc)
	{

		var sampleUom=$('#cboRfUom_'+inc).val();
		var sampleName=$('#cboRfSampleName_'+inc).val();
		var company=$('#cbo_company_name').val();
		var garmentItem=$('#cboRfGarmentItem_'+inc).val();
		var gmtsColorId=$('#cboRfGmtsColorId_'+inc).val();
		
		
		var mainId=$('#update_id').val();
		var dtlId=$('#updateidRequiredDtl_'+inc).val();
		var data=$('#txtRfColorAllData_'+inc).val();
		var FabricDescription=$('#txtRfFabricDescription_'+inc).val();
		var page_link = page_link + "&sampleName="+sampleName+ "&garmentItem="+garmentItem+'&data='+data+'&mainId='+mainId+'&dtlId='+dtlId+'&company='+company+'&sampleUom='+sampleUom+'&FabricDescription='+FabricDescription+'&gmtsColorId='+gmtsColorId;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=250px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{

			var receive_all_data=this.contentDoc.getElementById("txtRfColorAllData").value;
			var color=this.contentDoc.getElementById("displayAllcol").value;
			var total_grey=this.contentDoc.getElementById("total_grey").value*1;
			total_grey=total_grey.toFixed(2);

			if(receive_all_data!="")
			{
				var dataArr=receive_all_data.split("-----");
				dataArr=dataArr[0].split("__");
				
				$('#cboRfUom_'+inc).val( dataArr[9]);
				$('#txtRfColorAllData_'+inc).val( receive_all_data );
				$('#txtRfReqQty_'+inc).val(total_grey);
				$("#txtRfColor_"+inc).val(color);
			}
		}
	}

	function openmypage_requisition()
	{
		hide_left_menu("Button1");
		var title = 'Requisition ID Search';
		var page_link = 'requires/sweater_sample_requisition_controller.php?&action=requisition_id_popup&company_id='+$('#cbo_company_name').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1300px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_tbl_id=this.contentDoc.getElementById("selected_job").value;//mst id

			if (mst_tbl_id!="")
			{
				freeze_window(5);
				get_php_form_data(mst_tbl_id, "populate_data_from_requisition_search_popup", "requires/sweater_sample_requisition_controller" );
				get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/sweater_sample_requisition_controller' );
				$("#cbo_company_name").attr('disabled','disabled');
				$("#cbo_buyer_name").attr('disabled','disabled');
 				//set_button_status(1, permission, 'fnc_sweater_sample_requisition_mst_info',1,0);
 				release_freezing();
 				$('#content_sample_details').hide();
 				$('#content_required_fabric').hide();
 				$('#content_required_accessories').hide();
 				$('#content_required_embellishment').hide();
 				$("#cbo_sample_stage").attr('disabled','disabled');
 			}
 		}
 	}

 	function show_hide_content(row, id)
 	{
 		$('#content_'+row).toggle('fast', function() {

 		});
 	}
 	function hide_others_section(type)
 	{
 		if(type=='sd')
 		{
 			$('#content_required_fabric').hide();
 			$('#content_required_accessories').hide();
 			$('#content_required_embellishment').hide();
 		}
 		else if(type=='rf')
 		{
 			$('#content_sample_details').hide();
 			$('#content_required_accessories').hide();
 			$('#content_required_embellishment').hide();
 		}
 		else if(type=='acc')
 		{
 			$('#content_sample_details').hide();
 			$('#content_required_fabric').hide();
 			$('#content_required_embellishment').hide();
 		}
 		else if(type=='emb')
 		{
 			$('#content_sample_details').hide();
 			$('#content_required_fabric').hide();
 			$('#content_required_accessories').hide();
 		}
 	}

 	function calculate_required_qty(type,inc)
 	{
 		if(type==1)
 		{
 			var sampleName=$('#cboRfSampleName_'+inc).val()*1;
 			var garmentItem=$('#cboRfGarmentItem_'+inc).val()*1;
 			var rf_colors=$('#txtRfColor_'+inc).val();
 			var rf_color=rf_colors.split("***");
 			var col_length=rf_color.length;
 			var updateId=$('#update_id').val();
 			var data=sampleName+'___'+garmentItem+'___'+updateId+'___'+rf_colors+'___'+type;
 			var qty =return_global_ajax_value( data, 'populate_data_to_req_qty', '', 'requires/sweater_sample_requisition_controller') ;
 			var qty=qty.trim();
 			var reqQty=(dznQty/12)*qty;
 			//alert(qty+' '+reqQty);
 			$('#txtRfReqQty_'+inc).val(reqQty.toFixed(2));
 		}

 		else if(type==2)
 		{
 			var sampleName=$('#cboRaSampleName_'+inc).val()*1;
 			var garmentItem=$('#cboRaGarmentItem_'+inc).val()*1;
 			var dznQty=$('#txtRaReqDzn_'+inc).val()*1;
 			var updateId=$('#update_id').val();
 			var data=sampleName+'___'+garmentItem+'___'+updateId+'___'+'0'+'___'+type;
 			var qty =return_global_ajax_value( data, 'populate_data_to_req_qty', '', 'requires/sweater_sample_requisition_controller') ;
 			var qty=qty.trim();
 			var reqQty=(dznQty/12)*qty;
 			$('#txtRaReqQty_'+inc).val(reqQty.toFixed(2));
 		}
		else if(type==3) //txtRaexQty_
 		{
 			
 			//var txtRaReqQty=$('#txtRaReqQty_'+inc).val()*1;
 			var txtRaexQty=$('#txtRaexQty_'+inc).val()*1;
			
			var sampleName=$('#cboRaSampleName_'+inc).val()*1;
 			var garmentItem=$('#cboRaGarmentItem_'+inc).val()*1;
 			var dznQty=$('#txtRaReqDzn_'+inc).val()*1;
 			var updateId=$('#update_id').val();
 			var data=sampleName+'___'+garmentItem+'___'+updateId+'___'+'0'+'___'+type;
 			var qty =return_global_ajax_value( data, 'populate_data_to_req_qty', '', 'requires/sweater_sample_requisition_controller') ;
			//alert(qty+'='+txtRaexQty);
 			var qty=qty.trim();
 			var reqQty=((dznQty/12)*qty)+txtRaexQty;
 			$('#txtRaReqQty_'+inc).val(reqQty.toFixed(2));
 			
 		}
 	}
 	function load_uom_for_trims(inc,data)
 	{
 		var result = return_global_ajax_value( data, 'load_data_to_uom', '', 'requires/sweater_sample_requisition_controller');
 		var res=trim(result);
 		$('#cboRaUom_'+inc).val(res);
 	}

 	function load_data_to_rfcolor(inc)
 	{
 		var sampleName=$('#cboRfSampleName_'+inc).val()*1;
 		var garmentItem=$('#cboRfGarmentItem_'+inc).val()*1;
 		var mainId=$('#update_id').val();
 		var data=sampleName+"__"+garmentItem+"__"+mainId;
 		var result = return_global_ajax_value( data, 'load_data_to_colorRF', '', 'requires/sweater_sample_requisition_controller');
	 	var reponse=trim(result).split('__');
	 	$('#txtRfColor_'+inc).val(reponse[1]);
	 	$('#txtRfColorAllData_'+inc).val(trim(result));
	}

	function fnc_show_acknowledge() {
     	show_list_view('', 'show_acknowledge', 'list_acknowledge', 'requires/sweater_sample_requisition_controller', 'setFilterGrid(\'tbl_list_search\',-1);');
     }

	 function btn_load_acknowledge(){
        var count =1;
        if(count > 0){
            $("#list_acknowledge").html("<span id='btn_span' style='cursor: pointer; width: 60px !important; float: left;'><input id='show' onClick='fnc_show_acknowledge()' type='button' class='formbutton' value='&nbsp;&nbsp;show&nbsp;&nbsp;' style='background-color:#d9534f !important; background-image:none !important;border-color: #d43f3a;' title='Refusing Cause List'></span>");
        }
		else
        {
            $("#list_acknowledge").html("<span id='btn_span_disabled' style='cursor: pointer; width: 60px !important; float: left;'><input id='show' type='button' class='formbutton_disabled' value='&nbsp;&nbsp;show&nbsp;&nbsp;' style='background-color:#ccc !important; background-image:none !important;border-color: #ccc;' title='Refusing Cause List'></span>");
        }
        (
		function blink(){$('#btn_span').fadeOut(900).fadeIn(900, blink);})();
     }



function open_body_part_popup(i){

		var page_link='requires/sweater_sample_requisition_controller.php?action=body_part_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var id=this.contentDoc.getElementById("gid");
			var name=this.contentDoc.getElementById("gname");
			var type=this.contentDoc.getElementById("gtype");
			document.getElementById('cboRfBodyPartname_'+i).value=name.value;
			document.getElementById('cboRfBodyPart_'+i).value=id.value;//
			load_data_to_rfcolor(i);
		}
	}


	function fn_show_calender() {
		var company_name=$('#cbo_company_name').val();
		var requisition_date=$('#txt_requisition_date').val();
		var requisition_date=$('#txt_requisition_date').val();
		var sample_team=$('#cbo_sample_team').val();
		if(requisition_date!='' && company_name>0 && sample_team>0){
			show_list_view(company_name+'__'+requisition_date+'__'+sample_team, 'show_calender', 'list_acknowledge', 'requires/sweater_sample_requisition_controller', '');
		}
     }

	function fn_calculate_delivery_date(i){
		
		var txtDelvStartDate=$('#txtDelvStartDate_'+i).val();
		var currentDate='<? echo date('d-m-Y',time());?>';
		
		if(date_compare( currentDate, txtDelvStartDate)==true){
			var addDays = return_global_ajax_value( $('#update_id').val()+'__0__'+txtDelvStartDate+'__'+$('#cbo_company_name').val(), 'update_delivery_date', '', 'requires/sweater_sample_requisition_controller');
		//	alert(addDays+'='+currentDate);
			var DelvEndDate=add_days( txtDelvStartDate, addDays, '', '' );
			
			var txtDelvEndDate=$('#txtDelvEndDate_'+i).val(DelvEndDate);
		}
		else
		{
			$('#txtDelvStartDate_'+i).val(currentDate);
			alert('Sorry! Back date not allowed for start date.');
			
		}
		dateCopy();
	}

	function dateCopy(){
		var numRow = $('#sample_details_container tr').length;
		var txtDelvStartDate=$('#txtDelvStartDate_1').val();
		var txtDelvEndDate=$('#txtDelvEndDate_1').val();
		for(var i=2;i<=numRow; i++){
			$('#txtDelvStartDate_'+i).val(txtDelvStartDate);
			$('#txtDelvEndDate_'+i).val(txtDelvEndDate);
		}
	}



	function auto_acknowledge(requisitionId){
			var dataString = return_global_ajax_value(requisitionId, 'get_sample_requisition', '', 'requires/sweater_sample_requisition_controller');
			
			var operation=0;
			
			if(dataString){
				var dataArr=dataString.split('***');
				var dataStr='&update_id_1='+dataArr[0]+'&sample_req_id_1='+dataArr[1]+'&sample_req_no_1='+dataArr[2]+'&req_date_1='+dataArr[3]+'&company_id_1='+dataArr[4]+'&buyer_id_1='+dataArr[5]+'&season_1='+dataArr[6]+'&style_ref_1='+dataArr[7]+'&sample_qty_1='+dataArr[8]+'&required_qty_1='+dataArr[9]+'&embellishment_status_id_1='+dataArr[10]+'&delv_start_date_1='+dataArr[11]+'&delv_end_date_1='+dataArr[12]+'&team_leader_1='+dataArr[13]+'&txt_confirm_del_end_date_1='+dataArr[14]+'&txt_refusing_cause_1='+dataArr[15]+'&dealing_marchant_1='+dataArr[16];
				
				var data="action=approve&operation="+operation+'&total_row=2&cbo_acknowledge_type=2&tbl_1=1'+dataStr;
				
				//alert(data);
				
				http.open("POST","requires/sweater_sample_acknowledge_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=function(){
					if(http.readyState == 4)
					{
						var data=http.responseText.split('**');
						if(data[0]==1){alert('Acknowledge Done');}
						//else{alert('Acknowledge Done');}
					}

				}
			}
			
			
		
		}
		
	
	

//auto_acknowledge(2250);

	function print_button_setting()
	{
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/sweater_sample_requisition_controller' );
	}

</script>


<script type="text/javascript"> 
function uploadFile(mst_id){
	$(document).ready(function() { 
		var fd = new FormData(); 
		var files = $('#sample_mst_file')[0].files[0]; 
		fd.append('file', files); 
		$.ajax({ 
			url: 'requires/sweater_sample_requisition_controller.php?action=file_upload&mst_id='+ mst_id, 
			type: 'post', 
			data: fd, 
			contentType: false, 
			processData: false, 
			success: function(response){ 
				if(response != 0){ 
					document.getElementById('sample_mst_file').value='';
				} 
				else{ 
					alert('file not uploaded'); 
				} 
			}, 
		}); 
	}); 
}
</script>

  
</head>
<body onLoad="set_hotkey(); btn_load_acknowledge();">
	<div style="width:100%;">
		<? echo load_freeze_divs ("../../../",$permission);  ?>
		<form name="sweater_sample_requisition_1" id="sweater_sample_requisition_1" enctype="multipart/form-data" method="post">
			<fieldset style="width:1100px;">
				<legend>Sample Requisition</legend>
                <div style="width:770px; float:left;" align="center">
				<table cellpadding="2" cellspacing="2">
					<tr>
						<td colspan="3" align="right">Requisition Id</td>
						<td colspan="3"> <input type="text" name="txt_requisition_id" id="txt_requisition_id" class="text_boxes" style="width: 140px;margin-right: 38px;" placeholder="Requisition Id" readonly onDblClick="openmypage_requisition();" > </td>
					</tr>
					<tr>
						<td class="must_entry_caption" align="right" width="100">Sample Stage</td>
						<td width="150"><?=create_drop_down( "cbo_sample_stage", 150, $sample_stage, "", 1, "-- Select Stage --", 2, "fnc_browse_style(this.value);", "", "" ); ?>
						</td>
						<td class="must_entry_caption" align="right" width="100">Requisition Date</td>
						<td width="150">
							<input name="txt_requisition_date" id="txt_requisition_date" class="datepicker" type="text" value="<? echo date('d-m-Y')?>" style="width:140px;" disabled />
						</td>
						<td class="must_entry_caption" align="right" width="120">Master/Style Ref</td>
						<td> <input name="txt_style_name" id="txt_style_name" class="text_boxes" type="text" value="" style="width:140px;" placeholder="Write/Browse" onDblClick="check_sample_stage();" /> </td>
						<input type="hidden" id="txt_quotation_id" name="txt_quotation_id" class="text_boxes" style="width:30px;">
						<input type="hidden" id="txt_quotation_job_no" name="txt_quotation_job_no" class="text_boxes" style="width:30px;">
                        <input type="hidden" name="update_id" id="update_id" value="">
					</tr>
					<tr>
						<td class="must_entry_caption" align="right">Company Name</td>
						<td><?=create_drop_down( "cbo_company_name", 150, "select comp.id,comp.company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sweater_sample_requisition_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/sweater_sample_requisition_controller', this.value, 'load_drop_down_agent', 'agent_td' );load_drop_down( 'requires/sweater_sample_requisition_controller', this.value, 'load_drop_down_location', 'location_td' );fn_show_calender();print_button_setting();" ); ?></td>
						<td class="must_entry_caption" align="right">Location</td>
						<td id="location_td"><?=create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name",'id,location_name', 1, '--- Select Location ---', 0, ""  ); ?></td>
						<td class="must_entry_caption" align="right">Buyer Name</td>
						<td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", 0, "" ); ?></td>
					</tr>
					<tr>
						<td class="must_entry_caption" align="right">Season</td>
						<td id="season_td"><?=create_drop_down( "cbo_season_name", 150, $blank_array,"", 1, "-- Select Season --", $selected, "" ); ?></td>
                        <td align="right">Season Year</td>
						<td><? echo create_drop_down( "cbo_season_year", 150, create_year_array(),"", 1, "-Season Year-", $selected, "" ); ?></td>
						<td align="right">Brand</td>
						<td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 150, $blank_array,"", 1, "-Brand-", $selected, "" ); ?></td>
					</tr>
					<tr>
                    	<td align="right" class="must_entry_caption">Team Leader</td>
						<td><?=create_drop_down( "cbo_team_leader", 150, "select id,team_leader_name from lib_marketing_team where project_type=6 and team_type in (0,1,2) and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/sweater_sample_requisition_controller', this.value, 'sample_dealing_merchant', 'div_marchant' );" ); ?>
                        </td>
                    	<td class="must_entry_caption"  align="right">Dealing Merchant </td>
						<td id="div_marchant"><?=create_drop_down( "cbo_dealing_merchant", 150, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" ); ?></td>
						<td align="right">Agent Name</td>
						<td id="agent_td"><?=create_drop_down( "cbo_agent", 150, $blank_array,"", 1, "-- Select Agent --", $selected, "" ); ?></td>
					</tr>
					<tr>
                    	<td align="right">Buyer Ref</td>
						<td><input type="text" name="txt_buyer_ref" id="txt_buyer_ref" class="text_boxes" style="width:140px;"></td>
						<td align="right">Est. Ship Date</td>
						<td><input name="txt_est_ship_date" id="txt_est_ship_date" class="datepicker" type="text" value="" style="width:140px; text-align:left"  /></td>
                        <td align="right" class="must_entry_caption">Sample Team</td>
						<td><?=create_drop_down( "cbo_sample_team", 150, "select id,team_name from  lib_sample_production_team where product_category=6 and is_deleted=0","id,team_name", 1, "-- Select --", $selected, "fn_show_calender()" ); ?></td>
					</tr>
					<tr>
                    	<td align="right">Mat. Delivery Date</td>
						<td><input name="txt_material_dlvry_date" id="txt_material_dlvry_date" class="datepicker" type="text" value="<? echo date('d-m-Y')?>" style="width:140px;"  /></td>
                    	<td align="right">Remarks/Desc.</td>
						<td><input name="txt_remarks" class="text_boxes" id="txt_remarks" style="width:140px" maxlength="500" title="Maximum 500 Character"></td>
						<td align="right">Ready To Approved</td>
						<td><?=create_drop_down( "cbo_ready_to_approved", 150, $yes_no,"", 1, "-- Select--", 2, "","",""); ?></td>
					</tr>
                    <tr>
                    <td class="must_entry_caption" align="right">Product. Dept</td>
						<td><?=create_drop_down( "cbo_product_department", 150,$product_dept ,'', 1, '--- Select Department ---', 0, ""  ); ?></td>
                        
                    	
					</tr>
                    
                    <tr>
                    	<td align="right">File</td>
						<td align="left"><input type="file" id="sample_mst_file" class="image_uploader" style="width:150px" accept=".pdf,.zip,.doc,.text,.xls,.xlsx"></td>
                    	<td></td>
                        <td colspan="2"> <input type="button" id="image_button" class="image_uploader" style="width:150px" value="CLICK TO ADD FILE" onClick="file_uploader( '../../../', document.getElementById('update_id').value,'', 'sweater_sample_requisition_1', 2 ,1)" title="0" />
                        </td>
                   </tr>
					<tr>
						<td colspan="6" align="center" height="15">
							<span id="approvedMsg" style="color:red;font-size:22px;font-weight: bold;"></span>
						</td>
					</tr>
					<tr>
						<td colspan="6" height="40" valign="bottom" align="center" class="button_container">
							<?
							echo load_submit_buttons( $permission, "fnc_sweater_sample_requisition_mst_info", 0,0,"button_status(1)",1);
							?>
							<input style="width:80px;" type="button" id="copy_btn" class="formbutton" value="Copy" onClick="copy_requisition(5);" />
                            
                            <input style="width:80px;" type="button" id="copy_btn" class="formbutton" value="Mail Send" onClick="fnSendMail('../../../','',1,0,0,1,0,$('#cbo_company_name').val()+'_63_1')" />
						</td>
						
						
					</tr>
					<tr>
						<td colspan="6" align="center"> <span id="button_data_panel"></span> </td>
					</tr>
                        
				</table>
                </div>
                <div style="width:5px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
                <div id="list_acknowledge" style="max-height:300px; width:290px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
			</fieldset>
		</form>
		<h3 align="left" class="accordion_h" onClick="show_hide_content('sample_details', '');fnc_load_tr(document.getElementById('update_id').value,1);hide_others_section('sd');" style="width:1220px;"> +Sample Details </h3>
		<div id="content_sample_details" style="display:none;">
			<form name="sample_details_1" id="sample_details_1">
				<fieldset style="width:1150px;" id="sample_dtls">
					<table width="100%" cellpadding="0" cellspacing="2" align="center" style="">
						<tr>
							<td align="center" valign="top" id="po_list_views">

								<legend>Sample details</legend>
								<table cellpadding="0" cellspacing="0" width="1220" class="rpt_table" border="1" rules="all" id="tbl_sample_details">
									<thead>
										<th width="100" class="must_entry_caption">Sample Name </th>
										<th width="100" class="must_entry_caption">Garment Item</th>
                                       <!-- <th width="70" class="must_entry_caption">Weight</th>-->
										<th width="40">SMV</th>
										<th width="60">Article No</th>
										<th width="80" class="must_entry_caption">GMTS Color</th>
										<th width="100" class="must_entry_caption">Sample Req Qty</th>
										<th width="100" class="must_entry_caption">Submn. Qty</th>
										<th width="85" class="must_entry_caption"> Start Date</th>
										<th width="85" class="must_entry_caption">Delivery Date</th>
										<th width="70">Charge/ Unit</th>
										<th width="70">Currency</th>
										<th>Image</th>
										<th></th>
									</thead>
									<tbody id="sample_details_container">
										<tr id="tr_1" style="height:10px;" class="general">
											<td id="sample_td">
												<?
												$sql="select id,sample_name from  lib_sample where  status_active=1 and is_deleted=0";
												echo create_drop_down( "cboSampleName_1", 100, $blank_array,"", 1, "Select Sample", $selected, "");
												?>
											</td>
											<td align="center" id="item_id_1">
												<?
												echo create_drop_down( "cboGarmentItem_1", 100, get_garments_item_array(100),"", 1, "Select Item", 0, "");
												?>
											</td>
                                            <!--<td align="center" id="weight_1">
												<input style="width:72px;" type="text" class="text_boxes_numeric"  name="txtweight_1" id="txtweight_1"/>
												 
											</td>-->
                                            <td align="center" id="smv_1">
												<input style="width:40px;" type="text" class="text_boxes_numeric"  name="txtSmv_1" id="txtSmv_1"/>
												<input type="hidden" id="updateidsampledtl_1" name="updateidsampledtl_1"  class="text_boxes" style="width:20px" value="" />
											</td>
											
											<input type="hidden" id="txtDeltedIdSd" name="txtDeltedIdSd"  class="text_boxes" style="width:20px" value="" />
											<td align="center" id="article_1">
												<input style="width:60px;" type="text" class="text_boxes"  name="txtArticle_1" id="txtArticle_1" placeholder="Write" />
											</td>
											<td align="center" id="color_1">
												<input style="width:80px;" type="text" class="text_boxes"  name="txtColor_1" id="txtColor_1" placeholder="Write/Browse" onDblClick="openmypage_color_size('requires/sweater_sample_requisition_controller.php?action=color_popup','Color Search','1','1');"/>
											</td>

											<td align="center" id="sample_prod_qty_1">
												<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSampleProdQty_1" id="txtSampleProdQty_1"  readonly placeholder="Browse" onDblClick="openmypage_sizeinfo('requires/sweater_sample_requisition_controller.php?action=sizeinfo_popup','Size Search','1')" /><input type="hidden" class="text_boxes"  name="txtAllData_1" id="txtAllData_1"/>

											</td>

											<td align="center" id="submission_qty_1">
												<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSubmissionQty_1" id="txtSubmissionQty_1" placeholder="Display" readonly />
											</td>

											<td align="center" id="delv_start_date_1">
												<input style="width:85px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="txtDelvStartDate_1" id="txtDelvStartDate_1" onChange="fn_calculate_delivery_date(1)" />
											</td>


											<td align="center" id="delv_end_date_1">
												<input style="width:85px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="txtDelvEndDate_1" id="txtDelvEndDate_1" readonly disabled />
											</td>

											<td align="center" id="charge_unit_1">
												<input style="width:70px;" type="text" class="text_boxes_numeric"  name="txtChargeUnit_1" id="txtChargeUnit_1" placeholder="Write"/>
											</td>

											<td align="center" id="currency_1">

												<?
												echo create_drop_down( "cboCurrency_1", 70, $currency, "","","",2, "", "", "" );
												?>
											</td>

											<td id="image_1"><input type="button" class="image_uploader" name="txtFile_1" id="txtFile_1" onClick="file_uploader ( '../../../', document.getElementById('updateidsampledtl_1').value,'', 'sample_details_1', 0 ,1)" style="" value="ADD IMAGE"></td>
											<td width="70">
												<input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)" />
												<input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
											</td>
										</tr>

									</tbody>

								</table>
								<table style="margin-top: 5px;">
									<tr>
										<td colspan="15" height="40" valign="bottom" align="center" class="">
											<? echo load_submit_buttons( $permission, "fnc_sample_details_info", 0,0 ,"button_status(2)",2); ?>

											<input type="hidden" name="hidden_size_id" id="hidden_size_id" value="">
											<input type="hidden" name="hidden_bhqty" id="hidden_bhqty" value="">
											<input type="hidden" name="hidden_plnqnty" id="hidden_plnqnty" value="">
											<input type="hidden" name="hidden_dyqnty" id="hidden_dyqnty" value="">
											<input type="hidden" name="hidden_testqnty" id="hidden_testqnty" value="">
											<input type="hidden" name="hidden_selfqnty" id="hidden_selfqnty" value="">
											<input type="hidden" name="hidden_totalqnty" id="hidden_totalqnty" value="">
											<input type="hidden" name="hidden_tbl_size_id" id="hidden_tbl_size_id" value="">
										</td>
									</tr>
								</table>


							</td>
						</tr>
					</table>

				</fieldset>
			</form>
		</div>

		<h3 align="left" class="accordion_h" onClick="show_hide_content('required_fabric', '');fnc_load_tr(document.getElementById('update_id').value,2);hide_others_section('rf');" style="width:1150px;"> +Required Yarn </h3>
		<div id="content_required_fabric" style="display:none;">
			<form name="required_fabric_1" id="required_fabric_1">
				<fieldset style="width:1290px;" id="required_fab_dtls">
					<table width="100%" cellpadding="0" cellspacing="2" align="center" >
						<tr>
							<td align="center" valign="top" id="po_list_view">

								<legend>Required Yarn </legend>
								<table cellpadding="0" cellspacing="0" width="1290" class="rpt_table" border="1" rules="all" id="tbl_required_yarn">
									<thead>
										<th class="must_entry_caption">Sample Name </th>
                                        <th>Development No.</th>
										<th class="must_entry_caption">Garment Item</th>
                                        
                                        <th>Buyer prov</th>
                                        
										<th class="must_entry_caption">Gmts Color</th>
										<th class="must_entry_caption">Body Part</th>
										<th class="must_entry_caption">Yarn Nature</th>
										<th class="must_entry_caption">Yarn Description</th>
										<th class="must_entry_caption">Gauge</th>
                                        
                                        <th>No of Ends</th>
                                        
										<th class="must_entry_caption">Color</th>
										<th class="must_entry_caption">Color Type</th>
										<th class="must_entry_caption">UOM</th>
										<th class="must_entry_caption">Total Yarn Req.</th>
										<th class="" style="display:none;">Remarks</th>
										<th class="">Image</th>
										<th></th>
									</thead>

									<tbody id="required_fabric_container">
										
                                        
                                        <tr  id="tr_1" style="height:10px;" class="general">
											<td align="center" id="rfSampleId_1">
												<?
												echo create_drop_down( "cboRfSampleName_1", 95, $blank_array,"", 1, "select Sample", $selected,"");
												?>
											</td>
                                            <td align="center" id="rfDevelopmentNo_1">
												<input style="width:70px;" type="text" placeholder="Write" class="text_boxes"  name="txtRfDevelopmentNo_1" id="txtRfDevelopmentNo_1"/>
											</td>
											<td align="center" id="rfItemId_1">
												<?
												echo create_drop_down( "cboRfGarmentItem_1", 95, $blank_array,"", 1, "Select Item", 0, "");
												?>
											</td>
                                            
                                            
                                            <td align="center">
												<input style="width:70px;" type="text" placeholder="Write" class="text_boxes"  name="txtBuyerProv_1" id="txtBuyerProv_1"/>
											</td>
                                            
                                            
											<td align="center" id="rfGmtsColorId_1">
												<?
												echo create_drop_down( "cboRfGmtsColorId_1", 70, $blank_array,"", 1, "Select Item", 0, "");
												?>
											</td>
											<td align="center" id="rf_body_part_1">
												<input type="hidden" id="cboRfBodyPart_1" name="cboRfBodyPart_1" class="text_boxes" style="width:70px"  value=""  readonly/>
												<input type="text" id="cboRfBodyPartname_1" name="cboRfBodyPartname_1" class="text_boxes" style="width:70px" onDblClick="open_body_part_popup(1)" value=""   placeholder="DblClick" readonly/>
											</td>
											<td align="center" id="rf_fabric_nature_1">
												<?
												echo create_drop_down( "cboRfFabricNature_1", 95, $item_category,"", 0, "Select Fabric Nature", 0, "","","100");//2,3,

												?>

											</td>
											<td align="center" id="rf_fabric_description_1">
												<input style="width:60px;" type="text" class="text_boxes"  name="txtRfFabricDescription_1" id="txtRfFabricDescription_1" placeholder="Write/Browse" onDblClick="open_fabric_description_popup(1)" />
												<input type="hidden" name="libyarncountdeterminationid_1" id="libyarncountdeterminationid_1" class="text_boxes" style="width:10px" value="0" >
											</td>
											<td align="center" id="rf_gauge_1">
												<?php echo create_drop_down( "txtRfGauge_1", 50, $gauge_arr,"", '', "", "",""); ?>
											</td>
                                            
                                            <td align="center">
												<input style="width:70px;" type="text" placeholder="Write" class="text_boxes_numeric"  name="txtNoOfEnds_1" id="txtNoOfEnds_1"/>
											</td>

                          
                                             <td align="center" id="rf_color_1">
                                                <input style="width:60px;" type="text" class="text_boxes"  name="txtRfColor_1" id="txtRfColor_1" placeholder="Write/Browse" onDblClick="openmypage_rf_color('requires/sweater_sample_requisition_controller.php?action=color_popup_rf','Color Search','1');" readonly />
                                             </td>
                
                                             <td align="center" id="rf_color_type_1">
                                                <?
                                                echo create_drop_down( "cboRfColorType_1", 95, $color_type,"", 1, "Select Color Type", 2, "");
                                                ?>
                                                <input type="hidden" name="txtRfColorAllData_1" id="txtRfColorAllData_1" value=""  class="text_boxes">
                                                <input type="hidden" id="updateidRequiredDtl_1" name="updateidRequiredDtl_1"  class="text_boxes" style="width:20px" value=""  />
                                                <input type="hidden" id="txtDeltedIdRf" name="txtDeltedIdRf"  class="text_boxes" style="width:20px" value="" />
                                                
                                                
                                             </td>
                                           
                                             <td align="center" id="rf_uom_1">
                                                <?
                                                echo create_drop_down( "cboRfUom_1", 56, $unit_of_measurement,'', '', "",15,"",1,"12,15,27,1,23" );
                                                ?>
                                             </td>
                
                                             <td align="center" id="rf_req_qty_1">
                                                <input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtRfReqQty_1" id="txtRfReqQty_1" placeholder="" readonly />
                                             </td>
                
                                               <td align="center" style="display:none;">
                                                <input style="width:50px;" type="text" class="text_boxes"  name="txtRfRemarks_1" id="txtRfRemarks_1"   onclick="required_fab_remarks(1);" placeholder="Click" readonly />
                                             </td>
                                             <td id="rf_image_1"><input type="button" class="image_uploader" name="txtRfFile_1" id="txtRfFile_1" onClick="file_uploader ( '../../../', document.getElementById('updateidRequiredDtl_1').value,'', 'required_fabric_1', 0 ,1)" value="ADD IMAGE"></td>
                                             <td width="70">
                                                <input type="button" id="increaserf_1" name="increaserf_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_rf_tr(1)" />
                                                <input type="button" id="decreaserf_1" name="decreaserf_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_rf_deleteRow(1);" />
                                             </td>
                                         </tr>

                                     
                                     </tbody>
                
                                 </table>
                                 <table>
                                    <tr>
                                        <td colspan="18" height="40" valign="bottom" align="center" class="">
                                            <?
                                            echo load_submit_buttons( $permission, "fnc_required_yarn_details_info", 0,0 ,"button_status(3)",3);
                                            ?>
                                             <!--<input type="button" class="formbutton" style="width:100px" name="stripe_color" id="stripe_color" value="Stripe Color"  onClick="open_stripe_color_popup()"/>-->
                                        </td>
                
                                    </tr>
                                 </table>
                             </td>
                         </tr>
                     </table>
                
                 </fieldset>
                </form>

</div>
<h3 align="left" class="accordion_h" onClick="show_hide_content('required_accessories', '');fnc_load_tr(document.getElementById('update_id').value,3);hide_others_section('acc');" style="width: 1250px;"> +Required Accessories </h3>
<div id="content_required_accessories" style="display:none;">
	<form name="required_accessories_1" id="required_accessories_1">
		<fieldset style="width: 1250px;" id="required_accessories_dtls">
			<table width="100%" cellpadding="0" cellspacing="2" align="center" style="">
				<tr>
					<td align="center" valign="top" id="po_list_view">

						<legend>Required Accessories </legend>
						<table cellpadding="0" cellspacing="0" width="1250" class="rpt_table" border="1" rules="all" id="tbl_required_accessories">
							<thead>
								<th class="must_entry_caption">Sample Name </th>
								<th class="must_entry_caption">Garment Item</th>
								<th class="must_entry_caption">Trims Group</th>
								<th class="must_entry_caption">Description</th>
								<th>Brand/ Supp. Ref</th>
								<th class="must_entry_caption">UOM</th>
								<th class="must_entry_caption">Req/Dzn</th>
                                <th class="must_entry_caption">Ex.Qty</th>
								<th class="must_entry_caption">Req. Qty.</th>
								<th>Remarks</th>
								<th class="">Image</th>
								<th class="must_entry_caption"></th>
							</thead>

							<tbody id="required_accessories_container">
								<tr class="" id="tr_1" style="height:10px;"  class="general">
									<td align="center" id="raSampleId_1" width="100">
										<?
										echo create_drop_down( "cboRaSampleName_1", 100, $blank_array,"", 1, "select Sample", $selected, "");
										?>

									</td>

									<td align="center" id="raItemId_1" width="100">
										<?
										echo create_drop_down( "cboRaGarmentItem_1", 100, $blank_array,"", 1, "Select Item", 0, "");
										?>
									</td>
									<td align="center" id="ra_trims_group_1" width="100">
										<?
										$sql="select item_name,id from lib_item_group where   is_deleted=0   and
										status_active=1 order by item_name";
										echo create_drop_down( "cboRaTrimsGroup_1", 100, $sql,"id,item_name", 1, "Select Item", 0, "load_uom_for_trims('1',this.value);");
										?>
									</td>
									<td align="center" id="ra_description_1" width="130">
										<input style="width:130px;" type="text" class="text_boxes"  name="txtRaDescription_1" id="txtRaDescription_1" placeholder="write"/>

										<input type="hidden" id="updateidAccessoriesDtl_1" name="updateidAccessoriesDtl_1"  class="text_boxes" style="width:20px" value="" />
									</td>
									<input type="hidden" id="txtDeltedIdRa" name="txtDeltedIdRa"  class="text_boxes" style="width:20px" value="" />
									<td align="center" id="ra_brand_supp_1" width="130">
										<input style="width:130px;" type="text" class="text_boxes"  name="txtRaBrandSupp_1" id="txtRaBrandSupp_1" placeholder="write"  />
									</td>
									<td align="center" id="ra_uom_1" width="100">
										<?
										echo create_drop_down( "cboRaUom_1", 100, $unit_of_measurement,'', '', "",12,"","","" );
										?>
									</td>

									<td align="center" id="ra_req_dzn_1" width="100">
										<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtRaReqDzn_1" id="txtRaReqDzn_1" placeholder="write" onBlur="calculate_required_qty('2','1');"/>
									</td>
                                    <td align="center" id="ra_req_ex_1" width="80">
										<input style="width:80px;" type="text" class="text_boxes_numeric"  name="txtRaexQty_1" id="txtRaexQty_1" placeholder="write" onBlur="calculate_required_qty('3','1');"/>
									</td>

									<td align="center" id="ra_req_qty_1" width="100">
										<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtRaReqQty_1" id="txtRaReqQty_1" placeholder="write" readonly/>
									</td>
									<input type="hidden" class="text_boxes"  name="txtMemoryDataRa_1" id="txtMemoryDataRa_1" />

									<td align="center" id="ra_remarks_1" width="70">
										<input style="width:70px;" type="text" class="text_boxes"  name="txtRaRemarks_1" id="txtRaRemarks_1" placeholder="write" />
									</td>

									<td id="ra_image_1"><input type="button" class="image_uploader" name="txtRaFile_1" id="txtRaFile_1" onClick="file_uploader ( '../../../', document.getElementById('updateidAccessoriesDtl_1').value,'', 'required_accessories_1', 0 ,1)" style="width:80px;" value="ADD IMAGE"></td>
									<td width="100">
										<input type="button" id="increasera_1" name="increasera_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_ra_tr(1)" />
										<input type="button" id="decreasera_1" name="decreasera_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_ra_deleteRow(1);" />
									</td>
								</tr>

							</tbody>

						</table>
						<table>
							<tr>
								<td colspan="10" height="40" valign="bottom" align="center">
									<?
									echo load_submit_buttons( $permission, "fnc_required_accessories_info", 0,0 ,"button_status(4)",4);
									?>
								</td>
							</tr>
						</table>
					</table>

				</fieldset>
			</form>

		</div>
		<h3 align="left" class="accordion_h" onClick="show_hide_content('required_embellishment', '');fnc_load_tr(document.getElementById('update_id').value,4);hide_others_section('emb');" style="width: 1150px;"> +Required Embellishment </h3>
		<div id="content_required_embellishment" style="display:none;">
			<form name="required_embellishment_1" id="required_embellishment_1">
				<fieldset style="width: 950px;" id="required_embellishment_dtls">



					<table width="100%" cellpadding="0" cellspacing="2" align="left" style="">
						<tr>
							<td align="center" valign="top" id="po_list_view">

								<legend style="width:950px;">Required Embellishment </legend>
								<table cellpadding="0" cellspacing="0" width="950px" class="rpt_table" border="1" rules="all" id="tbl_required_embellishment">
									<thead>
										<th class="must_entry_caption" align="">Sample Name </th>
										<th class="must_entry_caption">Garment Item</th>
										<th class="must_entry_caption">Name</th>
										<th class="must_entry_caption">Type</th>
										<th class="">Remarks</th>
										<th class="">Image</th>
										<th class="must_entry_caption"></th>
									</thead>

									<tbody id="required_embellishment_container">
										<tr id="tr_1" style="height:10px;" class="general">
											<td align="center" id="reSampleId_1">
												<?
												$sql="select id,sample_name from  lib_sample where  status_active=1 and is_deleted=0";
												echo create_drop_down( "cboReSampleName_1", 140, $blank_array,"", 1, "select Sample", $selected, "");
												?>

											</td>

											<td align="center" id="reItemIid_1">
												<?
												$sql="select id,item_name from  lib_garment_item where status_active=1 and is_deleted=0";
												echo create_drop_down( "cboReGarmentItem_1", 140, $blank_array,"", 1, "Select Item", 0, "");

												?>


											</td>
											<td align="center" id="re_name_1">
												<?
							        //$sql="select id,item_name from  lib_garment_item where status_active=1 and is_deleted=0";
												echo create_drop_down( "cboReName_1", 140, $emblishment_name_array,"", 1, "Select Name", 0, "cbotype_loder(1);");


												?>
											</td>
											<td align="center" id="reType_1">
												<?
							       // $sql="select id, emb_type from  wo_pre_cost_embe_cost_dtls where status_active=1 and is_deleted=0";
												echo create_drop_down( "cboReType_1", 140, $blank_array,"", 1, "Select Item", 0, "");

												?>
											</td>
											<td align="center" id="re_remarks_1">
												<input style="width:90px;" type="text" class="text_boxes"  name="txtReRemarks_1" id="txtReRemarks_1" placeholder="write"/>


											</td>



											<td id="re_image_1"><input type="button" class="image_uploader" name="reTxtFile_1" id="reTxtFile_1" onClick="file_uploader ( '../../../',document.getElementById('updateidRequiredEmbellishdtl_1').value,'', 'required_embellishment_1', 0 ,1);"style="width:170px;" value="CLICK TO ADD/VIEW IMAGE"></td>

											<input type="hidden" id="updateidRequiredEmbellishdtl_1" name="updateidRequiredEmbellishdtl_1"  class="text_boxes" style="width:20px" value="" />
											<input type="hidden" id="txtDeltedIdRe" name="txtDeltedIdRe"  class="text_boxes" style="width:20px" value="" />
											<td width="70">
												<input type="button" id="increasere_1" name="increasere_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_re_tr(1)" />
												<input type="button" id="decreasere_1" name="decreasere_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_re_deleteRow(1);" />
											</td>
										</tr>
									</tbody>
								</table>
								<table style="">
									<tr>
										<td height="5"></td>
									</tr>
									<tr >
										<td colspan="7" height="40" valign="bottom" align="center" class="">
											<?
											echo load_submit_buttons( $permission, "fnc_required_embellishment_info", 0,0 ,"button_status(5)",5);
											?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">$("#cbo_buyer_name").val(0);</script>
</html>
