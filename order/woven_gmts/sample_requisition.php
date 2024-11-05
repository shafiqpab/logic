<?
/*-------------------------------------------- Comments
Purpose			         :  This Form Will Create Sample Requisition Entry.
Functionality	         :
JS Functions	         :
Created by		         : Rehan Uddin
Creation date 	         : 10/12/2016
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
$season_mandatory_sql=sql_select( "select company_name, season_mandatory from variable_order_tracking where variable_list=44 and status_active=1");
foreach($season_mandatory_sql as $key=>$value)
{
	$season_mandatory_arr[$value[csf("company_name")]]=$value[csf("season_mandatory")];
}
//print_r($season_mandatory_arr);
$season_mandatory_arr=json_encode($season_mandatory_arr);




//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Requisition", "../../", 1, 1,$unicode,'','');
?>
<script>

	var season_mandatory_arr='<? echo $season_mandatory_arr;?>';
 	var season_mandatory_arr=JSON.parse(season_mandatory_arr);

  	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
  	var permission='<? echo $permission; ?>';

  	function remove_style_ref_id()
  	{
  		if(form_validation('cbo_buyer_name','Buyer Name')==false)
			{
				$('#txt_style_name').val('');
				return;
			}
  		var data=$('#cbo_buyer_name').val()+'**'+$('#txt_style_name').val();
 	 	var style_ref_id =return_global_ajax_value( data, 'load_php_style_ref', '', 'requires/sample_requisition_controller');
 	 	if(style_ref_id == 0){
 	 		$('#txt_style_ref_id').val('');
 	 	}
 	 	else{
 	 		$('#txt_style_ref_id').val(style_ref_id);
 	 	}
  	}



  	function copy_requisition(operation)
	{
		alert("After copy company name, buyer name,sample stage changing is not allowed");
		/*if (form_validation('cbo_sample_stage*txt_requisition_date*txt_style_name*cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_season_name*cbo_product_department*cbo_dealing_merchant','Sample Stage*Requisition Date*Style Name*Company Name*Location*Buyer*Season Name*Product Department*Dealing Merchant')==false)
		{
			return;
		}*/
		var comp=$('#cbo_company_name').val();
	   if(season_mandatory_arr[comp]==1)
	   {
	   	    var data1="cbo_sample_stage*txt_requisition_date*txt_style_name*cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_season_name*cbo_product_department*cbo_team_leader*cbo_dealing_merchant";
	   	    var data2="Sample Stage*Requisition Date*Style Name*Company Name*Location*Buyer*Season Name*Product Department*Team Leader*Dealing Merchant";
	   }
	   if(season_mandatory_arr[comp]!=1)
	   {
	    	var data1="cbo_sample_stage*txt_requisition_date*txt_style_name*cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_product_department*cbo_team_leader*cbo_dealing_merchant";
	   	    var data2="Sample Stage*Requisition Date*Style Name*Company Name*Location*Buyer*Product Department*Team Leader*Dealing Merchant";

	   }

	   if (form_validation(data1,data2)==false)
		{
			return;
		}


		else
		{
			var data="action=copy_requisition&operation="+operation+get_submitted_data_string('txt_requisition_id*cbo_sample_stage*cbo_company_name*txt_requisition_date*txt_quotation_id*txt_style_name*cbo_location_name*cbo_buyer_name*cbo_season_name*cbo_product_department*cbo_dealing_merchant*cbo_agent*txt_buyer_ref*txt_est_ship_date*txt_bhmerchant*txt_remarks*cbo_ready_to_approved*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/sample_requisition_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
		 	http.onreadystatechange = fnc_sample_requisition_mst_info_reponse;


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
		$('#txt_bhmerchant').removeAttr('disabled','');
		$('#txt_est_ship_date').removeAttr('disabled','');
		$('#txt_remarks').removeAttr('disabled','');
		$('#cbo_ready_to_approved').removeAttr('disabled','');
		set_button_status(1, permission, 'fnc_sample_requisition_mst_info',1);

	}

  	function fnc_sample_requisition_mst_info( operation )
	{
		var comp=$('#cbo_company_name').val();
 		if(operation==4)
		{
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val(), "sample_requisition_print", "requires/sample_requisition_controller" );
			 return;
		}
		if(operation==1 || operation==2)
		{
			var check_acknowledge = return_global_ajax_value( document.getElementById('txt_requisition_id').value+"**"+document.getElementById('update_id').value, 'check_acknowledge', '', 'requires/sample_requisition_controller');
			if(trim(check_acknowledge)==1)
			{
				alert('Requisition already acknowledged . Update or delete not allowed');
				return;
			}
		}
	   if(season_mandatory_arr[comp]==1)
	   {
	   	    var data1="cbo_sample_stage*txt_requisition_date*txt_style_name*cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_season_name*cbo_product_department*cbo_team_leader*cbo_dealing_merchant";
	   	    var data2="Sample Stage*Requisition Date*Style Name*Company Name*Location*Buyer*Season Name*Product Department*Team Leader*Dealing Merchant";
	   }
	   if(season_mandatory_arr[comp]!=1)
	   {
	    	var data1="cbo_sample_stage*txt_requisition_date*txt_style_name*cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_product_department*cbo_team_leader*cbo_dealing_merchant";
	   	    var data2="Sample Stage*Requisition Date*Style Name*Company Name*Location*Buyer*Product Department*Team Leader*Dealing Merchant";

	   }

	   if (form_validation(data1,data2)==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete_mst&operation="+operation+get_submitted_data_string('txt_requisition_id*cbo_sample_stage*cbo_company_name*txt_requisition_date*txt_quotation_id*txt_style_name*txt_style_ref_id*cbo_location_name*cbo_buyer_name*cbo_season_name*cbo_product_department*cbo_dealing_merchant*cbo_agent*txt_buyer_ref*txt_est_ship_date*txt_bhmerchant*txt_remarks*update_id*cbo_ready_to_approved*txt_material_dlvry_date*cbo_season_year*cbo_team_leader',"../../");
			freeze_window(operation);
			http.open("POST","requires/sample_requisition_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_sample_requisition_mst_info_reponse;
		}
	}

	function fnc_sample_requisition_mst_info_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==0 )
			{
			   show_msg(reponse[0]);
			   $("#txt_requisition_id").val(reponse[1]);
			   $("#update_id").val(reponse[2]);
			   $("#cbo_company_name").attr('disabled','disabled');
				$("#cbo_buyer_name").attr('disabled','disabled');
				$("#cbo_dealing_merchant").attr('disabled','disabled');
				$("#cbo_sample_stage").attr('disabled','disabled');
			   set_button_status(1, permission, 'fnc_sample_requisition_mst_info',1);
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
				reset_form('sample_requisition_1','','');
				reset_form('sample_details_1','','');
				reset_form('required_fabric_1','','');
				reset_form('required_accessories_1','','');
				reset_form('required_embellishment_1','','');
 				$("#cbo_company_name").removeAttr('disabled','');
				$("#cbo_buyer_name").removeAttr('disabled','');
				$("#cbo_dealing_merchant").removeAttr('disabled','');
				$("#cbo_sample_stage").removeAttr('disabled','');
				set_button_status(0, permission, 'fnc_sample_requisition_mst_info',1);

			}


			release_freezing();
		}
	}



	function fnc_sample_details_info( operation )
	{

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
				if (form_validation('cboSampleName_'+i+'*cboGarmentItem_'+i+'*txtColor_'+i+'*txtSampleProdQty_'+i+'*txtSubmissionQty_'+i+'*txtDelvEndDate_'+i,'Sample Name*Garment Item*Color*Sample Production Qty*Sample Submission Qty*Start Date*End Date')==false)
				{
					return;
				}
				data_all=data_all+get_submitted_data_string('cboSampleName_'+i+'*cboGarmentItem_'+i+'*txtSmv_'+i+'*txtArticle_'+i+'*txtColor_'+i+'*txtSampleProdQty_'+i+'*txtSubmissionQty_'+i+'*txtDelvStartDate_'+i+'*txtDelvEndDate_'+i+'*txtChargeUnit_'+i+'*cboCurrency_'+i+'*updateidsampledtl_'+i+'*txtAllData_'+i,"../../");
			}

			var data="action=save_update_delete_sample_details&operation="+operation+'&total_row='+row_num+'&update_id='+update_id+'&txtDeltedIdSd='+txtDeltedIdSd+data_all;
			   //alert(data); return;
			   freeze_window(operation);
			   http.open("POST","requires/sample_requisition_controller.php", true);
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
				load_drop_down( 'requires/sample_requisition_controller', upId+'_1', 'load_drop_down_required_fabric_sample_name', 'rfSampleId_1');
				load_drop_down( 'requires/sample_requisition_controller', upId+'_1', 'load_drop_down_required_fabric_gmts_item', 'rfItemId_1');
				load_drop_down( 'requires/sample_requisition_controller', upId+'_2', 'load_drop_down_required_fabric_sample_name','raSampleId_1');
				load_drop_down( 'requires/sample_requisition_controller', upId+'_2', 'load_drop_down_required_fabric_gmts_item', 'raItemId_1');
				load_drop_down( 'requires/sample_requisition_controller', upId+'_3', 'load_drop_down_required_fabric_sample_name','reSampleId_1');
				load_drop_down( 'requires/sample_requisition_controller', upId+'_3', 'load_drop_down_required_fabric_gmts_item', 'reItemIid_1');
				$("#cbo_buyer_name").attr('disabled','disabled');
			 }

			if(reponse[0]==1 )
			{
				show_msg(reponse[0]);
				fnc_load_tr(reponse[1],1);
				var upId=document.getElementById("update_id").value;
				load_drop_down( 'requires/sample_requisition_controller', upId+'_1', 'load_drop_down_required_fabric_sample_name', 'rfSampleId_1');
				load_drop_down( 'requires/sample_requisition_controller', upId+'_1', 'load_drop_down_required_fabric_gmts_item', 'rfItemId_1');
				load_drop_down( 'requires/sample_requisition_controller', upId+'_2', 'load_drop_down_required_fabric_sample_name','raSampleId_1');
				load_drop_down( 'requires/sample_requisition_controller', upId+'_2', 'load_drop_down_required_fabric_gmts_item', 'raItemId_1');
				load_drop_down( 'requires/sample_requisition_controller', upId+'_3', 'load_drop_down_required_fabric_sample_name','reSampleId_1');
				load_drop_down( 'requires/sample_requisition_controller', upId+'_3', 'load_drop_down_required_fabric_gmts_item', 'reItemIid_1');
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

		}
	}
	function fnc_required_fabric_details_info( operation )
	{
	 		var update_id=$('#update_id').val()*1;
	 		var txtDeltedIdRf=$('#txtDeltedIdRf').val();
			if(update_id=="")
			{
				alert("save master part!!");
				return;
			}
			/*if(operation==2)
			{
				alert("Under Process!!");
				return;
			}
	   		*/
			else
			{
	 			var row_nums=$('#tbl_required_fabric tr').length-1;
	 			var data_all="";
				for (var i=1; i<=row_nums; i++)
				{
					if (form_validation('cboRfSampleName_'+i+'*cboRfGarmentItem_'+i+'*cboRfBodyPart_'+i+'*cboRfFabricNature_'+i+'*txtRfFabricDescription_'+i+'*txtRfGsm_'+i+'*txtRfDia_'+i+'*txtRfColorAllData_'+i+'*cboRfColorType_'+i+'*cboRfWidthDia_'+i+'*cboRfUom_'+i+'*txtRfReqQty_'+i,'Sample Name*Garment Item*Body Part*Fabric Nature*Fabric Desc*Gsm*Dia*Browse Color*Color Type*Width Dia*Uom*ReqQty')==false)
						{
						return;
						}
	 			data_all=data_all+get_submitted_data_string('cboRfSampleName_'+i+'*cboRfGarmentItem_'+i+'*cboRfBodyPart_'+i+'*cboRfFabricNature_'+i+'*txtRfFabricDescription_'+i+'*txtRfGsm_'+i+'*txtRfDia_'+i+'*txtRfColor_'+i+'*cboRfColorType_'+i+'*cboRfWidthDia_'+i+'*cboRfUom_'+i+'*txtRfReqDzn_'+i+'*txtRfReqQty_'+i+'*updateidRequiredDtl_'+i+'*txtRfColorAllData_'+i+'*libyarncountdeterminationid_'+i+'*txtProcessLoss_'+i+'*txtGrayFabric_'+i,"../../");
	 			}

				var data="action=save_update_delete_required_fabric&operation="+operation+'&total_row='+row_nums+'&update_id='+update_id+'&txtDeltedIdRf='+txtDeltedIdRf+data_all;
				freeze_window(operation);
				http.open("POST","requires/sample_requisition_controller.php", true);
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
				data_all=data_all+get_submitted_data_string('cboRaSampleName_'+i+'*cboRaGarmentItem_'+i+'*cboRaTrimsGroup_'+i+'*txtRaDescription_'+i+'*txtRaBrandSupp_'+i+'*cboRaUom_'+i+'*txtRaReqDzn_'+i+'*txtRaReqQty_'+i+'*txtRaRemarks_'+i+'*updateidAccessoriesDtl_'+i,"../../");
			}
			var data="action=save_update_delete_required_accessories&operation="+operation+'&total_row='+row_num+'&update_id='+update_id+'&txtDeltedIdRa='+txtDeltedIdRa+data_all;
			freeze_window(operation);
			http.open("POST","requires/sample_requisition_controller.php", true);
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
				data_all=data_all+get_submitted_data_string('cboReSampleName_'+i+'*cboReGarmentItem_'+i+'*cboReName_'+i+'*cboReType_'+i+'*txtReRemarks_'+i+'*updateidRequiredEmbellishdtl_'+i,"../../");
			}
			var data="action=save_update_delete_required_embellishment&operation="+operation+'&total_row='+row_num+'&update_id='+update_id+'&txtDeltedIdRe='+txtDeltedIdRe+data_all;
			freeze_window(operation);
			http.open("POST","requires/sample_requisition_controller.php", true);
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
		}
	}

	function fnc_load_tr(up_id,type) //after save of details part or browse requisition this function load all saved data from db in specific tr
	{
		if(type==1)
		{
				var data=up_id+'**'+type;
 	 			var list_view_tr = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sample_requisition_controller');
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
			var list_view_tr = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sample_requisition_controller');
			if(list_view_tr=="" || list_view_tr==0)
			{

				set_button_status(0, permission, 'fnc_required_fabric_details_info',3,0);
				return;
			}
			else (list_view_tr!='')
			{
				$("#required_fabric_container tr").remove();
				$("#required_fabric_container").append(list_view_tr);
				set_all_onclick();
				set_button_status(1, permission, 'fnc_required_fabric_details_info',3,0);

				return;
			}

		}

		else if(type==3)
		{
			var data=up_id+'**'+type;
			 //$("#tbl_required_accessories tbody > tr").remove();

			var list_view_tr = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sample_requisition_controller');
			if(list_view_tr=="" || list_view_tr==0)
			{
				/*var row_nums=$('#tbl_required_accessories tr').length-1;
				if(row_nums>1)
				{
				  $("#tbl_required_accessories tbody tr:first").clone().appendTo("#tbl_required_accessories");
				  $('#tbl_required_accessories tbody tr:not(:first)').remove();
				  //$("#tbl_required_accessories tbody tr:last").remove();
				}*/
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
			var list_view_tr = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sample_requisition_controller');
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
			get_php_form_data(update_id_dtl, "load_data_to_sizeinfo","requires/sample_requisition_controller" );
			var data=$('#txtAllData_'+inc).val();
			var title = 'Size Info';
			var page_link = page_link +'&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=400px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var receive_all_data=this.contentDoc.getElementById("hidden_size_data").value;
				var receive_self_and_total_data=this.contentDoc.getElementById("hidden_total_self_and_all_data").value;
				if(receive_all_data!="")
				{

					$('#txtAllData_'+inc).val( receive_all_data );
					var res=receive_self_and_total_data.split('___');
					var submission_qty=res[1]-res[0];
					$('#txtSampleProdQty_'+inc).val(res[1]);
					$('#txtSubmissionQty_'+inc).val(submission_qty);
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
		else if(sampleStage==3)
		{
			openmypage(3);
		}
	}


	function button_status(type)
	{
		if(type==1)
		{
			reset_form('sample_requisition_1','','');
			set_button_status(0, permission, 'fnc_sample_requisition_mst_info',1);
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
			set_button_status(0, permission, 'fnc_required_fabric_details_info',3);
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
		var result = return_global_ajax_value( data, 'auto_sd_color_generation', '', 'requires/sample_requisition_controller');
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
			$('#txt_style_name').attr("ondblclick","check_sample_stage()");
			$('#txt_style_name').removeAttr("onchange",' ');
	 	}
		else if(data==2 || data==3)
		{
	 		$("#cbo_company_name").removeAttr('disabled','');
			$("#cbo_location_name").removeAttr('disabled','');
			$("#txt_style_name").removeAttr('readonly','');
			$("#cbo_buyer_name").removeAttr('disabled','');
			$("#cbo_season_name").removeAttr('disabled','');
			$("#cbo_dealing_merchant").removeAttr('disabled','');
			$("#cbo_product_department").removeAttr('disabled','');
			$("#txt_bhmerchant").removeAttr('disabled','');
			$("#cbo_agent").removeAttr('disabled','');
			$("#txt_est_ship_date").removeAttr('disabled','');
			$("#txt_remarks").removeAttr('disabled','');
			$('#txt_style_name').attr("ondblclick","check_sample_stage()");
			$('#txt_style_name').attr("onchange","remove_style_ref_id()");

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
			$("#txt_bhmerchant").removeAttr('disabled','');
			$("#cbo_agent").removeAttr('disabled','');
			$("#txt_est_ship_date").removeAttr('disabled','');
			$("#txt_remarks").removeAttr('disabled','');
			$('#txt_style_name').removeAttr("ondblclick");

		}
	}

	function open_fabric_description_popup(i)
	{
		var cbofabricnature=document.getElementById('cboRfFabricNature_'+i).value;
		var libyarncountdeterminationid =document.getElementById('libyarncountdeterminationid_'+i).value
		var page_link='requires/sample_requisition_controller.php?action=fabric_description_popup&fabric_nature='+cbofabricnature+'&libyarncountdeterminationid='+libyarncountdeterminationid;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
			{
				var fab_des_id=this.contentDoc.getElementById("fab_des_id");
				//var fab_nature_id=this.contentDoc.getElementById("fab_nature_id");
				var fab_desctiption=this.contentDoc.getElementById("fab_desctiption");
				var fab_gsm=this.contentDoc.getElementById("fab_gsm");
				document.getElementById('libyarncountdeterminationid_'+i).value=fab_des_id.value;
				document.getElementById('txtRfFabricDescription_'+i).value=fab_desctiption.value;
				document.getElementById('txtRfGsm_'+i).value=fab_gsm.value;

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
			$('#txtAllData_'+i).val('');
			$('#txtColor_'+i).val('');
			$('#txtSize_'+i).val('');
			$('#txtSampleProdQty_'+i).val('');
			$('#txtSubmissionQty_'+i).val('');

			$('#txtChargeUnit_'+i).val('');
			
			
			if($('#hidd_color_from_lib').val()==1) 
			{
				$('#txtColor_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_color_size('requires/sample_requisition_controller.php?action=color_popup','Color Search','1','"+i+"');");
				$('#txtColor_'+i).attr('readonly',true);
				$('#txtColor_'+i).attr('placeholder','Browse');
			}
			else
			{
				$('#txtColor_'+i).attr('readonly',false);
				$('#txtColor_'+i).attr('placeholder','Write');
				$('#txtColor_'+i).removeAttr('onDblClick','onDblClick');	
			}
			
			//$('#txtColor_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_color_size('requires/sample_requisition_controller.php?action=color_popup','Color Search','1','"+i+"');");
			$('#txtSampleProdQty_'+i).removeAttr("onfocus");


			$('#txtSampleProdQty_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_sizeinfo('requires/sample_requisition_controller.php?action=sizeinfo_popup','Size Search','"+i+"')");

			$('#txtFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../',document.getElementById('updateidsampledtl_"+i+"').value,'', 'sample_details_1', 0 ,1);");

			$('#cboSampleName_'+i).removeAttr("disabled","");
			$('#cboGarmentItem_'+i).removeAttr("disabled","");
			$('#txtSampleProdQty_'+i).removeAttr("disabled","");
			$('#txtDelvStartDate_'+i).removeAttr("class").attr("class","datepicker");
			$('#txtDelvEndDate_'+i).removeAttr("class").attr("class","datepicker");
			$('#increase_'+i).removeAttr("value").attr("value","+");
			$('#decrease_'+i).removeAttr("value").attr("value","-");
			$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
			set_all_onclick();
		}
	}

	function fn_deleteRow(rowNo)
	{
		if($('#tbl_sample_details').val()!=2)
		{
 			var numRow = $('#tbl_sample_details tbody tr').length;
			var k=rowNo-1;
			if(numRow==rowNo && rowNo!=1)
			{
						var updateIdDtls=$('#updateidsampledtl_'+rowNo).val();
						var txt_deleted_id=document.getElementById("txtDeltedIdSd").value;
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
 							document.getElementById("txtDeltedIdSd").value=selected_id;
 							$('#tbl_sample_details tbody tr:last').remove();

			}
			else
			{
				return false;
			}
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


			//$('#txtRfReqDzn_'+i).removeAttr("onblur").attr("onblur","calculate_required_qty('1','"+i+"')");
			$('#cboRfBodyPart_'+i).removeAttr("onchange").attr("onchange","load_data_to_rfcolor('"+i+"')");

			$('#txtRfColor_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_rf_color('requires/sample_requisition_controller.php?action=color_popup_rf','Color Search','"+i+"');");

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
		if($('#tbl_required_fabric').val()!=2)
		{
			var numRow = $('#tbl_required_fabric tbody tr').length;
			var k=rowNo-1;
			if(numRow==rowNo && rowNo!=1)
			{
				var updateIdDtls=$('#updateidRequiredDtl_'+rowNo).val();
				var txt_deleted_id=document.getElementById("txtDeltedIdRf").value;
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
				document.getElementById("txtDeltedIdRf").value=selected_id;
				$('#tbl_required_fabric tbody tr:last').remove();

			}
			else
			{
				return false;
			}
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
			$('#txtRaRemarks_'+i).val('');
			$('#txtMemoryDataRa_'+i).val('');

			$('#txtRaReqDzn_'+i).removeAttr("onblur").attr("onblur","calculate_required_qty('2','"+i+"')");
			$('#cboRaTrimsGroup_'+i).removeAttr("onchange").attr("onchange","load_uom_for_trims('"+i+"',this.value);");
			$('#txtRaFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../',document.getElementById('updateidAccessoriesDtl_"+i+"').value,'', 'required_accessories_1', 0 ,1);");
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
			$('#reTxtFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../', document.getElementById('updateidRequiredEmbellishdtl_"+i+"').value,'', 'required_embellishment_1', 0 ,1);");
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
		load_drop_down( 'requires/sample_requisition_controller', cboembname+'_'+i, 'load_drop_down_emb_type', 'reType_'+i );
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
			var page_link = 'requires/sample_requisition_controller.php?&action=style_id_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var mst_tbl_id=this.contentDoc.getElementById("selected_job").value;//mst id

				if (mst_tbl_id!="")
				{
					freeze_window(5);
					get_php_form_data(mst_tbl_id, "populate_data_from_search_popup", "requires/sample_requisition_controller" );
					$("#cbo_company_name").attr('disabled','disabled');
					$("#cbo_location_name").attr('disabled','disabled');
					$("#cbo_buyer_name").attr('disabled','disabled');
					$("#cbo_dealing_merchant").attr('disabled','disabled');
					$("#cbo_product_department").attr('disabled','disabled');
					var browse_job_no=$('#txt_quotation_job_no').val();
					load_drop_down( 'requires/sample_requisition_controller', '', 'load_drop_down_trims_group_from_budget_for_after_order', 'ra_trims_group_1');
					release_freezing();
				}
			}
		}

		if(sample_stage==2 || sample_stage==3)
		{
			if(form_validation('cbo_buyer_name','Buyer Name')==false)
			{
				return;
			}
			var company = $("#cbo_company_name").val();
			var page_link='requires/sample_requisition_controller.php?action=inquiry_popup&company='+company;
			var title="Search  Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=980px,height=400px,center=1,resize=0,scrolling=0','../')
			//var txt_style_ref=document.getElementById('txt_style_ref').value;//Hide by ISD-23-28630
			//var buyer_name=document.getElementById('cbo_buyer_name').value;
			//var txt_style_ref_id=document.getElementById('txt_style_ref_id').value;
		 	//var page_link='requires/sample_requisition_controller.php?action=style_ref_popup&buyer_name='+buyer_name;
			//emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Style Description', 'width=460px,height=250px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				/*var theform=this.contentDoc.forms[0];
				var inquiryId=this.contentDoc.getElementById("txt_inquiry_id").value; // mrr number
				get_php_form_data(inquiryId, "populate_data_from_inquiry_search", "requires/sample_requisition_controller");
				$("#cbo_company_name").attr('disabled','disabled');
				$("#cbo_buyer_name").attr('disabled','disabled');
				$("#cbo_company_name").attr('disabled','disabled');
				$("#cbo_dealing_merchant").attr('disabled','disabled');
	 			//$("#cbo_agent").attr('disabled','disabled');
	 			$("#txt_est_ship_date").attr('disabled','disabled');*/
	 			var txt_style=this.contentDoc.getElementById("txt_style_name").value;
				var id_style=this.contentDoc.getElementById("txt_style_ref_id").value;
				$('#txt_style_name').val(txt_style);
				$('#txt_style_ref_id').val(id_style);

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
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=400px,center=1,resize=1,scrolling=0','../');
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0];
					var col_name=this.contentDoc.getElementById("hidden_color_name").value;//mst id

					if (col_name!="")
					{
						var row_count=$('#tbl_sample_details tr').length-1;
						var current_row = inc;
						var a=0; var n=0;
						var colData=col_name.split(",");
						for(var b=1; b<=colData.length; b++){
							if(a==0){
								$("#txtColor_"+inc).val( colData[a] );
							}
							else{
								add_break_down_tr(row_count);
								row_count++;
								$("#txtColor_"+row_count).val( colData[a] );
							}
							a++;
						}
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
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=400px,center=1,resize=1,scrolling=0','../');
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0];
					var col_name=this.contentDoc.getElementById("hidden_color_name").value;//mst id
					if (col_name!="")
					{
						var row_count=$('#tbl_sample_details tr').length-1;
						var current_row = inc;
						var a=0; var n=0;
						var colData=col_name.split(",");
						for(var b=1; b<=colData.length; b++){
							if(a==0){
								$("#txtColor_"+inc).val( colData[a] );
							}
							else{
								add_break_down_tr(row_count);
								row_count++;
								$("#txtColor_"+row_count).val( colData[a] );
							}
							a++;
						}
					}
				}
		}
	}


	function openmypage_rf_color(page_link,title,inc)
	{

		var sampleName=$('#cboRfSampleName_'+inc).val();
		var company=$('#cbo_company_name').val();
		var garmentItem=$('#cboRfGarmentItem_'+inc).val();
		var mainId=$('#update_id').val();
		var dtlId=$('#updateidRequiredDtl_'+inc).val();
		var data=$('#txtRfColorAllData_'+inc).val();
		var page_link = page_link + "&sampleName="+sampleName+ "&garmentItem="+garmentItem+'&data='+data+'&mainId='+mainId+'&dtlId='+dtlId+'&company='+company;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=670px,height=250px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{

			var receive_all_data=this.contentDoc.getElementById("txtRfColorAllData").value;
			var color=this.contentDoc.getElementById("displayAllcol").value;
			
			var total_qnty=this.contentDoc.getElementById("total_qnty_kg").value*1;
			total_qnty=total_qnty.toFixed(2);
			var total_loss=this.contentDoc.getElementById("total_loss").value*1;
			total_loss=total_loss.toFixed(2);
			var total_grey=this.contentDoc.getElementById("total_grey").value*1;
			total_grey=total_grey.toFixed(2);
			
			if(receive_all_data!="")
			{
				$('#txtRfColorAllData_'+inc).val( receive_all_data );
				$("#txtRfColor_"+inc).val(color);
				
				$('#txtRfReqQty_'+inc).val(total_qnty);
				$('#txtProcessLoss_'+inc).val(total_loss);
				$('#txtGrayFabric_'+inc).val(total_grey);
			}
		}
	}
	
	function openmypage_requisition()
	{
		var title = 'Requisition ID Search';
		var page_link = 'requires/sample_requisition_controller.php?&action=requisition_id_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_tbl_id=this.contentDoc.getElementById("selected_job").value;//mst id

			if (mst_tbl_id!="")
			{
				freeze_window(5);
				get_php_form_data(mst_tbl_id, "populate_data_from_requisition_search_popup", "requires/sample_requisition_controller" );
				fnc_variable_settings_check($("#cbo_company_name").val());

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
	
	function fnc_variable_settings_check()
	{
		var company_id=$('#cbo_company_name').val();
		var all_variable_settings=return_ajax_request_value(company_id, 'load_variable_settings', 'requires/sample_requisition_controller');
		var color_from_lib=all_variable_settings;
		
		if(color_from_lib==1)
		{
			$('#hidd_color_from_lib').val(color_from_lib);
			$('#txtColor_1').attr('readonly',true);
			$('#txtColor_1').attr('placeholder','Browse');
			$('#txtColor_1').removeAttr("onDblClick").attr("onDblClick","openmypage_color_size('requires/sample_requisition_controller.php?action=color_popup','Color Search','1','"+1+"');");
		}
		else 
		{
			$('#hidd_color_from_lib').val(2);
			$('#txtColor_1').attr('readonly',false);
			$('#txtColor_1').attr('placeholder','Write');
			$('#txtColor_1').removeAttr('onDblClick','onDblClick');	
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
 			var dznQty=$('#txtRfReqDzn_'+inc).val()*1;
 			var updateId=$('#update_id').val();
 			var data=sampleName+'___'+garmentItem+'___'+updateId+'___'+rf_colors+'___'+type;
 			var qty =return_global_ajax_value( data, 'populate_data_to_req_qty', '', 'requires/sample_requisition_controller') ;
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
 			var qty =return_global_ajax_value( data, 'populate_data_to_req_qty', '', 'requires/sample_requisition_controller') ;
 			var qty=qty.trim();
 			var reqQty=(dznQty/12)*qty;
 			$('#txtRaReqQty_'+inc).val(reqQty.toFixed(2));
 		}
 	}
 	function load_uom_for_trims(inc,data)
 	{
 		var result = return_global_ajax_value( data, 'load_data_to_uom', '', 'requires/sample_requisition_controller');
 		var res=trim(result);
 		$('#cboRaUom_'+inc).val(res);
 	}

 	function load_data_to_rfcolor(inc)
 	{
 		var sampleName=$('#cboRfSampleName_'+inc).val()*1;
 		var garmentItem=$('#cboRfGarmentItem_'+inc).val()*1;
 		var mainId=$('#update_id').val();
 		var data=sampleName+"_"+garmentItem+"_"+mainId;
 		var result = return_global_ajax_value( data, 'load_data_to_colorRF', '', 'requires/sample_requisition_controller');
	 	//var res=trim(result).split("_");
	 	var reponse=trim(result).split('_');
	 	$('#txtRfColor_'+inc).val(reponse[1]);
	 	$('#txtRfColorAllData_'+inc).val(trim(result));
	}
	function style_from_library(data){
		/*if(data == 1){//ISD-23-28630
			$('#txt_style_name').removeAttr("onDblClick").attr("onDblClick","check_sample_stage()");
			$('#txt_style_name').prop('readonly', true);
			$('#txt_style_name').val("");
			$('#txt_style_ref_id').val("");
			$('#txt_style_name').attr('placeholder','Browse');
		}
		if(data == 2){
			$('#txt_style_name').removeAttr("onDblClick");
			$('#txt_style_name').prop('readonly', false);
			$('#txt_style_name').val("");
			$('#txt_style_ref_id').val("");
			$('#txt_style_name').attr('placeholder','Write');
		}*/
	}
	function get_company_config(company_id){
    	get_php_form_data(company_id,'get_company_config','requires/sample_requisition_controller' );
	}

	function season_semple_load(buyer_id){
		load_drop_down( 'requires/sample_requisition_controller', buyer_id, 'load_drop_down_season_buyer', 'season_td');
		load_drop_down( 'requires/sample_requisition_controller', buyer_id, 'load_drop_down_sample_for_buyer', 'sample_td');
	}


</script>
</head>
<body onLoad="set_hotkey();" style="min-height: 1200px;">
	<div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission);  ?>
		<form name="sample_requisition_1" id="sample_requisition_1">
			<fieldset style="width:1050px;margin-bottom: 10px;">
				<legend>Sample Requisition</legend>
				<table cellpadding="1" cellspacing="1" width="1050">
					<tr>
						<td colspan="4" align="right">Requisition ID</td>
						<td colspan="4"> <input type="text" name="txt_requisition_id" id="txt_requisition_id" class="text_boxes" style="width: 145px;margin-right: 38px;" placeholder="Requisition ID" readonly onDblClick="openmypage_requisition();" > </td>
					</tr>

					<tr>
						<td width="100" class="must_entry_caption">Sample Stage</td>
						<td width="150">
							<?=create_drop_down( "cbo_sample_stage", 130, $sample_stage, "", 1, "-- Select Stage --", $selected, "fnc_browse_style(this.value);", "", "" ); ?>
						</td>
						<td width="100" class="must_entry_caption">Requisition Date</td>
						<td width="150">
							<input name="txt_requisition_date" id="txt_requisition_date" class="datepicker" type="text" value="<? echo date('d-m-Y')?>" style="width:120px;"  />
                            <input type="hidden" id="txt_quotation_id" name="txt_quotation_id" class="text_boxes" style="width:30px;">
							<input type="hidden" id="txt_quotation_job_no" name="txt_quotation_job_no" class="text_boxes" style="width:30px;">
                            <input type="hidden" name="hidd_color_from_lib" id="hidd_color_from_lib" style="width:30px;" class="text_boxes" />
						</td>
						<td width="100" class="must_entry_caption">Company Name</td>
						<td width="150">
							<?
							echo create_drop_down( "cbo_company_name", 130, "select comp.id,comp.company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "get_company_config(this.value); fnc_variable_settings_check(this.value);" );
							?>
						</td>
                        <td width="100" class="must_entry_caption">Location</td>
						<td id="location_td"><?=create_drop_down( "cbo_location_name", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name",'id,location_name', 1, '--- Select Location ---', 0, ""  ); ?></td>
					</tr>
					<tr>
						<td class="must_entry_caption">Buyer Name</td>
						<td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "" ); ?></td>
						<td class="must_entry_caption">Style Ref</td>
						<td>
							<input name="txt_style_name" id="txt_style_name" class="text_boxes" type="text" value="" style="width:120px;" placeholder="Write/Browse" onDblClick="check_sample_stage()" />
							<input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id">
						</td>
                        <td class="must_entry_caption">Season<?=create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-S.Year-", 1, "",0,"" ); ?></td>
						<td id="season_td"><?=create_drop_down( "cbo_season_name", 130, $blank_array,"", 1, "-- Select Season --", $selected, "" ); ?></td>
                        <td class="must_entry_caption">Product. Dept</td>
						<td><?=create_drop_down( "cbo_product_department", 130,$product_dept ,'', 1, '-Department-', 0, ""  ); ?></td>
					</tr>
					<tr>
						<td class="must_entry_caption">Team Leader</td>   
                        <td id="leader_td"><?=create_drop_down( "cbo_team_leader", 130, "select id,team_leader_name from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-Select Team-", $teamId, "load_drop_down( 'requires/sample_requisition_controller', this.value, 'load_drop_down_dealing_merchant', 'div_marchant' );"); ?></td>
						<td class="must_entry_caption">Dealing Merchant </td>
						<td id="div_marchant"><?=create_drop_down( "cbo_dealing_merchant", 130, $blank_array,"", 1, "-Select Team Member-", $selected, "" ); ?></td>
                        <td>Agent Name</td>
						<td id="agent_td"><?= create_drop_down( "cbo_agent", 130, $blank_array,"", 1, "-- Select Agent --", $selected, "" ); ?></td>
                        <td>Est. Ship Date</td>
						<td><input name="txt_est_ship_date" id="txt_est_ship_date" class="datepicker" type="text" value="" style="width:120px; text-align:left" /></td>
					</tr>
					<tr>
						<td>BH Merchant</td>
						<td><input class="text_boxes" type="text" style="width:120px;"  name="txt_bhmerchant" id="txt_bhmerchant"/></td>
                    	<td>Buyer Ref</td>
						<td><input type="text" name="txt_buyer_ref" id="txt_buyer_ref" class="text_boxes" style="width:120px;"></td>
                        <td>Mat. Delv. Date</td>
						<td><input name="txt_material_dlvry_date" id="txt_material_dlvry_date" class="datepicker" type="text" value="<? echo date('d-m-Y')?>" style="width:120px;"  /></td>
						<td>Ready To Approved</td>
						<td><?=create_drop_down( "cbo_ready_to_approved", 130, $yes_no,"", 1, "-- Select--", 2, "","",""); ?></td>
					</tr>
					<tr>
                    	<td>Remarks/Desc.</td>
						<td colspan="3">
							<input name="txt_remarks" class="text_boxes" ID="txt_remarks" style="width:370px" maxlength="500" title="Maximum 500 Character">
						</td>
                        <td>&nbsp;</td>
                        <td  align="left"> <input type="button" id="image_button" class="image_uploader" style="width:120px" value="ADD FILE" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'sample_requisition_1', 2 ,1)" /></td>
                        
						
					</tr>

					<tr>
						<td colspan="8" align="center" height="15">
							<span id="approvedMsg" style="color:red;font-size:22px;font-weight: bold;"></span>
						</td>
					</tr>

					<tr>
						<td colspan="6" align="center" height="15">
							<input type="hidden" name="update_id" id="update_id" value="">
						</td>
					</tr>

					<tr>
						<td colspan="5" height="40" valign="bottom" align="center" class="button_container">
							<?
							echo load_submit_buttons( $permission, "fnc_sample_requisition_mst_info", 0,1,"button_status(1)",1);
							?>
							&nbsp; &nbsp; <input type="button" id="copy_btn" class="formbutton" value="Copy" onClick="copy_requisition(5);" />
						</td>

					</tr>



				</table>
			</fieldset>
		</form>
		<h3 align="left" class="accordion_h" onClick="show_hide_content('sample_details', '');fnc_load_tr(document.getElementById('update_id').value,1);hide_others_section('sd');" style="width:1150px;"> +Sample Details </h3>
		<div id="content_sample_details" style="display:none;">
			<form name="sample_details_1" id="sample_details_1">
				<fieldset style="width:1150px;" id="sample_dtls">
					<table width="100%" cellpadding="0" cellspacing="2" align="center" style="">
						<tr>
							<td align="center" valign="top" id="po_list_views">

								<legend>Sample details</legend>
								<table cellpadding="0" cellspacing="0" width="1150" class="rpt_table" border="1" rules="all" id="tbl_sample_details">
									<thead>
										<th width="100" class="must_entry_caption">Sample Name </th>
										<th width="100" class="must_entry_caption">Garment Item</th>
										<th width="40">SMV</th>
										<th width="60">Article No</th>
										<th width="80" class="must_entry_caption">Color</th>
										<th width="100" class="must_entry_caption">Sample Req Qty</th>
										<th width="100" class="must_entry_caption">Submn. Qty</th>
										<th width="85">Delv. Start Date</th>
										<th width="85" class="must_entry_caption">Delv. End Date</th>
										<th width="70">Charge/ Unit</th>
										<th width="70">Currency</th>
										<th>Image</th>
										<th></th>
									</thead>
									<tbody id="sample_details_container">
										<tr id="tr_1" style="height:10px;" class="general" colorData="1">
											<td id="sample_td">
												<?
												$sql="select id,sample_name from  lib_sample where  status_active=1 and is_deleted=0";
												echo create_drop_down( "cboSampleName_1", 100, $blank_array,"", 1, "--Select Sample--", $selected, "");
												?>
											</td>
											<td align="center" id="item_id_1">
												<?
							     //   $sql="select id,item_name from  lib_garment_item where status_active=1 and is_deleted=0";
												echo create_drop_down( "cboGarmentItem_1", 100, get_garments_item_array(3),"", 1, "Select Item", 0, "");

												?>
											</td>
											<td align="center" id="smv_1">
												<input style="width:40px;" type="text" class="text_boxes_numeric"  name="txtSmv_1" id="txtSmv_1"/>
												<input type="hidden" id="updateidsampledtl_1" name="updateidsampledtl_1"  class="text_boxes" style="width:20px" value="" />
											</td>
											<input type="hidden" id="txtDeltedIdSd" name="txtDeltedIdSd"  class="text_boxes" style="width:20px" value="" />
											<td align="center" id="article_1">
												<input style="width:60px;" type="text" class="text_boxes"  name="txtArticle_1" id="txtArticle_1" placeholder="Write" />
											</td>
											<td align="center" id="color_1">
												<input style="width:80px;" type="text" class="text_boxes"  name="txtColor_1" id="txtColor_1" placeholder="Write/Browse" onDblClick="openmypage_color_size('requires/sample_requisition_controller.php?action=color_popup','Color Search','1','1');"/>
											</td>

											<td align="center" id="sample_prod_qty_1">
												<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSampleProdQty_1" id="txtSampleProdQty_1"  readonly placeholder="Browse" onDblClick="openmypage_sizeinfo('requires/sample_requisition_controller.php?action=sizeinfo_popup','Size Search','1')" /><input type="hidden" class="text_boxes"  name="txtAllData_1" id="txtAllData_1"/>

											</td>

											<td align="center" id="submission_qty_1">
												<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSubmissionQty_1" id="txtSubmissionQty_1" placeholder="Display" disabled />
											</td>

											<td align="center" id="delv_start_date_1">
												<input style="width:85px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="txtDelvStartDate_1" id="txtDelvStartDate_1" />
											</td>


											<td align="center" id="delv_end_date_1">
												<input style="width:85px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="txtDelvEndDate_1" id="txtDelvEndDate_1" />
											</td>

											<td align="center" id="charge_unit_1">
												<input style="width:70px;" type="text" class="text_boxes_numeric"  name="txtChargeUnit_1" id="txtChargeUnit_1" placeholder="Write"/>
											</td>

											<td align="center" id="currency_1">

												<?
												echo create_drop_down( "cboCurrency_1", 70, $currency, "","","",2, "", "", "" );
												?>
											</td>

											<td id="image_1"><input type="button" class="image_uploader" name="txtFile_1" id="txtFile_1" onClick="file_uploader ( '../../', document.getElementById('updateidsampledtl_1').value,'', 'sample_details_1', 0 ,1)" style="" value="ADD IMAGE"></td>
											<td width="70">
												<input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)" />
												<input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
											</td>
										</tr>

									</tbody>

								</table>
								<table style="margin-top: 5px;">
									<tr>
										<td colspan="14" height="40" valign="bottom" align="center" class="">

											<?
                                               // echo load_submit_buttons( $permission, "fnc_sample_details_info", 0,0,"button_status_SD()",1);
											echo load_submit_buttons( $permission, "fnc_sample_details_info", 0,0 ,"button_status(2)",2);
											?>

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

		<h3 align="left" class="accordion_h" onClick="show_hide_content('required_fabric', '');fnc_load_tr(document.getElementById('update_id').value,2);hide_others_section('rf');" style="width:1150px;"> +Required Fabric </h3>
		<div id="content_required_fabric" style="display:none;">
			<form name="required_fabric_1" id="required_fabric_1">
				<fieldset style="width:1150px;" id="required_fab_dtls">
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
										<th class="must_entry_caption">Fabric Weight</th>
										<th class="must_entry_caption">Width</th>
										<th class="must_entry_caption">Color</th>
										<th class="must_entry_caption">Color Type</th>
										<th class="must_entry_caption">Width/ Dia</th>
										<th class="must_entry_caption">UOM</th>
										<th >Req/Dzn</th>
										<th class="must_entry_caption">Finish Req. Qty.</th>
										<!--<th class="">Process Loss %</th>
										<th class="">Grey Req.Qty</th>-->
                                        
										<th class="">Image</th>
										<th class="must_entry_caption"></th>
									</thead>

									<tbody id="required_fabric_container">
										<tr class="" id="tr_1" style="height:10px;" class="general">
											<td align="center" id="rfSampleId_1">
												<?
												echo create_drop_down( "cboRfSampleName_1", 95, $blank_array,"", 1, "select Sample", $selected,"");
												?>

											</td>
											<td align="center" id="rfItemId_1">
												<?
												echo create_drop_down( "cboRfGarmentItem_1", 95, $blank_array,"", 1, "Select Item", 0, "");

												?>
											</td>
											<td align="center" id="rf_body_part_1">
												<?
												echo create_drop_down( "cboRfBodyPart_1", 95, $body_part,"", 1, "Select Body Part", 0, "load_data_to_rfcolor('1');");

												?>
											</td>
											<td align="center" id="rf_fabric_nature_1">
												<?
												echo create_drop_down( "cboRfFabricNature_1", 95, $item_category,"", 0, "Select Fabric Nature", 3,"","","2,3");

												?>

											</td>
											<td align="center" id="rf_fabric_description_1">
												<input style="width:60px;" type="text" class="text_boxes"  name="txtRfFabricDescription_1" id="txtRfFabricDescription_1" placeholder="Browse" onDblClick="open_fabric_description_popup(1)" readonly/>
												<input type="hidden" name="libyarncountdeterminationid_1" id="libyarncountdeterminationid_1" class="text_boxes" style="width:10px" >
											</td>

											<td align="center" id="rf_gsm_1">
												<input style="width:40px;" type="text" class="text_boxes_numeric"  name="txtRfGsm_1" id="txtRfGsm_1" placeholder="Display/Write"/>
											</td>
											<input type="hidden" id="updateidRequiredDtl_1" name="updateidRequiredDtl_1"  value=""  class="text_boxes" />
											<input type="hidden" id="txtDeltedIdRf" name="txtDeltedIdRf"  class="text_boxes" style="width:20px" value="" />


											<td align="center" id="rf_dia_1">
												<input style="width:40px;" type="text" placeholder="Write" class="text_boxes"  name="txtRfDia_1" id="txtRfDia_1"/>
											</td>

                          

                             <td align="center" id="rf_color_1">
                             	<input style="width:60px;" type="text" class="text_boxes"  name="txtRfColor_1" id="txtRfColor_1" placeholder="Browse" onDblClick="openmypage_rf_color('requires/sample_requisition_controller.php?action=color_popup_rf','Color Search','1');" readonly />
                             </td>
                             <input type="hidden" name="txtRfColorAllData_1" id="txtRfColorAllData_1" value=""  class="text_boxes">

                             <td align="center" id="rf_color_type_1">
                             	<?
                             	echo create_drop_down( "cboRfColorType_1", 95, $color_type,"", 1, "Select Color Type", 0, "");

                             	?>
                             </td>
                             <td align="center" id="rf_width_dia_1">
                             	<?
                             	echo create_drop_down( "cboRfWidthDia_1", 80, $fabric_typee,"", 1, "Select Width/Dia", 0, "");

                             	?>
                             </td>



                             <td align="center" id="rf_uom_1">
                             	<?
                             	echo create_drop_down( "cboRfUom_1", 56, $unit_of_measurement,'', '', "",12,"","","12,27,1,23" );
                             	?>
                             </td>

                             <td align="center" id="rf_req_dzn_1">
                             	<input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtRfReqDzn_1" id="txtRfReqDzn_1" placeholder="Write" onBlur="calculate_required_qty('1','1');" />
                                <input type="hidden" class="text_boxes"  name="txtMemoryDataRf_1" id="txtMemoryDataRf_1" />
                             </td>
                             

                            
                             
                             
                              <td align="center" id="rf_req_qty_1">
                             	<input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtRfReqQty_1" id="txtRfReqQty_1" placeholder="" readonly />
                             </td>
                            
                             <td align="center" id="rf_reqs_qty_1" style="display:none">
                             	<input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtProcessLoss_1" id="txtProcessLoss_1" placeholder=""  onChange="calculate_requirement('1');" />
                             </td>

                             <td align="center" id="rf_grey_qnty_1" style="display:none">
                             	<input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtGrayFabric_1" id="txtGrayFabric_1" placeholder="" readonly />
                             </td>


                             <td id="rf_image_1"><input type="button" class="image_uploader" name="txtRfFile_1" id="txtRfFile_1" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredDtl_1').value,'', 'required_fabric_1', 0 ,1)" value="ADD IMAGE"></td>
                             <td width="70">
                             	<input type="button" id="increaserf_1" name="increaserf_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_rf_tr(1)" />
                             	<input type="button" id="decreaserf_1" name="decreaserf_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_rf_deleteRow(1);" />
                             </td>
                         </tr>

                     </tbody>

                 </table>
                 <table>
                 	<tr>
                 		<td colspan="15" height="40" valign="bottom" align="center" class="">
                 			<?
                 			echo load_submit_buttons( $permission, "fnc_required_fabric_details_info", 0,0 ,"button_status(3)",3);
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
<h3 align="left" class="accordion_h" onClick="show_hide_content('required_accessories', '');fnc_load_tr(document.getElementById('update_id').value,3);hide_others_section('acc');" style="width: 1150px;"> +Required Accessories </h3>
<div id="content_required_accessories" style="display:none;">
	<form name="required_accessories_1" id="required_accessories_1">
		<fieldset style="width: 1150px;" id="required_accessories_dtls">
			<table width="100%" cellpadding="0" cellspacing="2" align="center" style="">
				<tr>
					<td align="center" valign="top" id="po_list_view">

						<legend>Required Accessories </legend>
						<table cellpadding="0" cellspacing="0" width="1150" class="rpt_table" border="1" rules="all" id="tbl_required_accessories">
							<thead>
								<th class="must_entry_caption">Sample Name </th>
								<th class="must_entry_caption">Garment Item</th>
								<th class="must_entry_caption">Trims Group</th>
								<th class="must_entry_caption">Description</th>
								<th>Brand/ Supp. Ref</th>
								<th class="must_entry_caption">UOM</th>
								<th class="must_entry_caption">Req/Dzn</th>
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
										/* $sql="select id,item_name from  lib_garment_item where status_active=1 and is_deleted=0";*/
										echo create_drop_down( "cboRaGarmentItem_1", 100, $blank_array,"", 1, "Select Item", 0, "");

										?>


									</td>
									<td align="center" id="ra_trims_group_1" width="100">
										<?
							       // $sql="select id,item_name from  lib_garment_item where status_active=1 and is_deleted=0";
										$sql="select item_name,id from lib_item_group where item_category=4 and is_deleted=0 and trim_type=1 and
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

									<td align="center" id="ra_req_qty_1" width="100">
										<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtRaReqQty_1" id="txtRaReqQty_1" placeholder="write" readonly/>
									</td>
									<input type="hidden" class="text_boxes"  name="txtMemoryDataRa_1" id="txtMemoryDataRa_1" />

									<td align="center" id="ra_remarks_1" width="70">
										<input style="width:70px;" type="text" class="text_boxes"  name="txtRaRemarks_1" id="txtRaRemarks_1" placeholder="write" />
									</td>

									<td id="ra_image_1"><input type="button" class="image_uploader" name="txtRaFile_1" id="txtRaFile_1" onClick="file_uploader ( '../../', document.getElementById('updateidAccessoriesDtl_1').value,'', 'required_accessories_1', 0 ,1)" style="width:80px;" value="ADD IMAGE"></td>
									<td width="70">
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



											<td id="re_image_1"><input type="button" class="image_uploader" name="reTxtFile_1" id="reTxtFile_1" onClick="file_uploader ( '../../',document.getElementById('updateidRequiredEmbellishdtl_1').value,'', 'required_embellishment_1', 0 ,1);"style="width:170px;" value="CLICK TO ADD/VIEW IMAGE"></td>

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


<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	var buyer_id=$("#cbo_buyer_name").val()*1;
 	load_drop_down( 'requires/sample_requisition_controller', buyer_id, 'load_drop_down_season_buyer', 'season_td');
	load_drop_down( 'requires/sample_requisition_controller', buyer_id, 'load_drop_down_sample_for_buyer', 'sample_td');
</script>
</html>
