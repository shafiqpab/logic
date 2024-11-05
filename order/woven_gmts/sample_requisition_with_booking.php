<?
/*-------------------------------------------- Comments
Purpose			         :  This Form Will Create Sample Requisition With Booking.
Functionality	         :
JS Functions	         :
Created by		         : Kausar
Creation date 	         : 07/10/2020
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

$user_id=$_SESSION['logic_erp']['user_id'];
$data_level_secured=$_SESSION['logic_erp']["data_level_secured"];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$location_id = $userCredential[0][csf('location_id')];
$company_credential_cond = "";
if ($company_id >0) {
    $company_credential_cond = " and comp.id in($company_id)";
}
if ($location_id !='') {
    $location_credential_cond = " and id in($location_id)";
}
//echo $location_credential_cond.'ddddssdss';
$season_mandatory_sql=sql_select( "select company_name, season_mandatory from variable_order_tracking where variable_list=44 and status_active=1");
foreach($season_mandatory_sql as $key=>$value)
{
	$season_mandatory_arr[$value[csf("company_name")]]=$value[csf("season_mandatory")];
}
//print_r($season_mandatory_arr);
$season_mandatory_arr=json_encode($season_mandatory_arr);

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Requisition With Booking","../../", 1, 1, $unicode,1,'');
?>
<script>

	var season_mandatory_arr='<? echo $season_mandatory_arr;?>';
 	var season_mandatory_arr=JSON.parse(season_mandatory_arr);

  	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
  	var permission='<? echo $permission; ?>';


  	function sample_wise_item(sample_mst_id,sample,inc,type)
  	{
  		//alert(sample_mst_id+sample+inc+type);
  		var data=sample_mst_id+'**'+sample;
  		var qty =return_global_ajax_value( data, 'sample_wise_item_data', '', 'requires/sample_requisition_with_booking_controller') ;
  		qty=trim(qty);
		
  		if(type==1) $("#cboRfGarmentItem_"+inc).val(qty);
  		else if(type==2) $("#cboRaGarmentItem_"+inc).val(qty);
  		else if(type==3) $("#cboReGarmentItem_"+inc).val(qty);
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

	function fnc_yarn_dtls()
	{
		var req_id=$("#update_id").val();
		var sample_stage=$("#cbo_sample_stage").val();
		var sample_stage=$("#cbo_sample_stage").val();
		var cbo_company_name=$('#cbo_company_name').val();
		if(!req_id)
		{
			alert("Save requisition first!!");
			return;
	
		}
		var title = 'Yarn Details';
		var page_link = 'requires/sample_requisition_with_booking_controller.php?&action=yarn_dtls_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&req_id='+req_id, title, 'width=1000px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_tbl_id=this.contentDoc.getElementById("selected_job").value;//mst id
	
			if (mst_tbl_id!="")
			{
			}
		}
	}
	
  	function fnc_generate_booking()
  	{
  		var req_id=$("#update_id").val();
		var operation=0;
		freeze_window(operation);
  		var sample_stage=$("#cbo_sample_stage").val();
  		if(!req_id)
  		{
  			alert("Save requisition first!!");
			release_freezing();
  			return;
  		}
		
		if (form_validation('txt_booking_date*cbo_pay_mode*cbo_supplier_name*cbo_fabric_source','Booking Date*Pay Mode*Supplier Name*Fabric Source')==false)
		{
			release_freezing();
			return;
		}
		else
		{

			var row_nums=$('#tbl_required_fabric tr').length-1;
			var data_all="";
			for (var i=1; i<=row_nums; i++)
			{
				if (form_validation('cboRfSampleName_'+i+'*cboRfGarmentItem_'+i+'*cboRfBodyPart_'+i+'*cboRfFabricNature_'+i+'*txtRfFabricDescription_'+i+'*txtRfGsm_'+i+'*txtRfColorAllData_'+i+'*cboRfColorType_'+i+'*cboRfUom_'+i+'*txtRfReqQty_'+i,'Sample Name*Garment Item*Body Part*Fabric Nature*Fabric Desc*Gsm*Browse Color*Color Type*Uom*ReqQty')==false)
				{
					release_freezing();
					return;
				}
				data_all=data_all+get_submitted_data_string('cboRfSampleName_'+i+'*cboRfGarmentItem_'+i+'*cboRfBodyPart_'+i+'*cboRfFabricNature_'+i+'*txtRfFabricDescription_'+i+'*txtRfGsm_'+i+'*txtRfDia_'+i+'*txtRfColor_'+i+'*cboRfColorType_'+i+'*cboRfWidthDia_'+i+'*cboRfUom_'+i+'*txtRfReqQty_'+i+'*txtProcessLoss_'+i+'*txtGrayFabric_'+i+'*updateidRequiredDtl_'+i+'*txtRfColorAllData_'+i+'*fabricDelvDate_'+i+'*cboRfFabricSource_'+i+'*txtRfRemarks_'+i+'*libyarncountdeterminationid_'+i+'*txtRate_'+i,"../../");
			}
			
			var data="action=save_update_delete_booking&operation="+operation+'&total_row='+row_nums+data_all+get_submitted_data_string('update_id*cbo_company_name*cbo_buyer_name*txt_booking_no*cbo_fabric_source*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_supplier_name*txt_attention*cbo_ready_to_approved_book*cbo_team_leader_book*cbo_dealing_merchant_book*txt_buyer_req_no*cbo_sources*txt_revise_no*txt_style_desc*txt_bodywashcolor*txt_quotation_id*cbo_sample_stage',"../../");

			http.open("POST","requires/sample_requisition_with_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_fabric_booking_reponse;
		}
  	}

  	function fnc_fabric_booking_reponse()
  	{
  		if(http.readyState == 4)
  		{
  			var reponse=trim(http.responseText).split('**');
			var sample_stage=$("#cbo_sample_stage").val();
			if(sample_stage==1)//With Order [After Order Place]
			{
				if(trim(reponse[0])=='approved'){
					alert("This booking is approved");
					release_freezing();
					return;
				}
				if(trim(reponse[0])=='sal1'){
					alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
					release_freezing();
					return;
				}
				if(trim(reponse[0])=='pi1'){
					alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
					release_freezing();
					return;
				}
			}
			else if(sample_stage!=1)//Non Order [Before Order Place]
			{
				
			}
			
  			if(trim(reponse[0])==0 || trim(reponse[0])==1 || trim(reponse[0])==2)
  			{
  				document.getElementById('txt_booking_no').value=reponse[1];
  				//set_button_status(1, permission, 'fnc_fabric_booking',1);
  				show_msg(trim(reponse[0]));
  			}
  			release_freezing();
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
		   	    var data1="cbo_sample_stage*txt_requisition_date*txt_style_name*cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_season_name*cbo_product_department*cbo_dealing_merchant";
		   	    var data2="Sample Stage*Requisition Date*Style Name*Company Name*Location*Buyer*Season Name*Product Department*Dealing Merchant";
		   }
		   if(season_mandatory_arr[comp]!=1)
		   {
		    	var data1="cbo_sample_stage*txt_requisition_date*txt_style_name*cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_product_department*cbo_dealing_merchant";
		   	    var data2="Sample Stage*Requisition Date*Style Name*Company Name*Location*Buyer*Product Department*Dealing Merchant";

		   }

	   if (form_validation(data1,data2)==false)
		{
			return;
		}
		else
		{
			var data="action=copy_requisition&operation="+operation+get_submitted_data_string('txt_requisition_id*cbo_sample_stage*cbo_company_name*txt_requisition_date*txt_quotation_id*txt_style_name*cbo_location_name*cbo_buyer_name*cbo_season_name*cbo_product_department*cbo_dealing_merchant*cbo_agent*txt_buyer_ref*txt_est_ship_date*txt_bhmerchant*txt_remarks*cbo_ready_to_approved*txt_material_dlvry_date*update_id*cbo_season_year*cbo_brand_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/sample_requisition_with_booking_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
		 	http.onreadystatechange = fnc_sample_requisition_mst_info_reponse;
		 	$("#txt_booking_no").val('');


		}

		$("#approvedMsg").html('');
		$('#required_fab_dtls').removeProp('disabled','');
		$('#sample_dtls').removeProp('disabled','');
		$('#required_accessories_dtls').removeProp('disabled','');
		$('#required_embellishment_dtls').removeProp('disabled','');
		$('#txt_requisition_date').removeAttr('disabled','');
		$('#txt_booking_date').val('<? echo date('d-m-Y');?>');
		$('#cbo_location_name').attr('disabled','true');
		if($('#cbo_sample_stage').val()!=1)
		{
			$('#txt_style_name').removeAttr('disabled','');
			$("#txt_style_name").removeAttr('readonly','');
		}
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
		var txt_booking_no=$('#txt_booking_no').val();
 		if(operation==4 || operation==5 || operation==6)
		{
			if (form_validation('txt_requisition_id','Requisition Id')==false)
  			{
  				return;
  			}
			if(operation==4) var action="sample_requisition_print"; else if(operation==5) var action="sample_requisition_print1";else if(operation==6) var action="sample_requisition_print3";
			
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_booking_no').val()+'*'+$('#cbo_template_id').val(), action, "requires/sample_requisition_with_booking_controller" );
			return;
		}
	   if(season_mandatory_arr[comp]==1)
	   {
	   	    var data1="cbo_sample_stage*txt_requisition_date*txt_style_name*cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_season_name*cbo_product_department*cbo_dealing_merchant";
	   	    var data2="Sample Stage*Requisition Date*Style Name*Company Name*Location*Buyer*Season Name*Product Department*Dealing Merchant";
	   }
	   if(season_mandatory_arr[comp]!=1)
	   {
	    	var data1="cbo_sample_stage*txt_requisition_date*txt_style_name*cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_product_department*cbo_dealing_merchant";
	   	    var data2="Sample Stage*Requisition Date*Style Name*Company Name*Location*Buyer*Product Department*Dealing Merchant";
	   }

		if (form_validation(data1,data2)==false)
		{
			return;
		}
		else
		{
			freeze_window(operation);
			if(operation==1 || operation==2)
			{
				var acknowledge = trim(return_global_ajax_value(document.getElementById('txt_requisition_id').value, 'check_requisition_acknowledg', '', 'requires/sample_requisition_with_booking_controller'));
				console.log(acknowledge);

				if(acknowledge*1>0)
				{
					release_freezing();
					alert('Requisition Acknowledged . Any change not allowed');
					return;
				}
			}
			var data="action=save_update_delete_mst&operation="+operation+get_submitted_data_string('txt_requisition_id*cbo_sample_stage*cbo_company_name*txt_requisition_date*txt_quotation_id*txt_style_name*cbo_location_name*cbo_buyer_name*cbo_season_name*cbo_product_department*cbo_dealing_merchant*cbo_agent*txt_buyer_ref*txt_est_ship_date*txt_bhmerchant*txt_remarks*update_id*cbo_ready_to_approved*txt_material_dlvry_date*cbo_season_year*cbo_brand_id',"../../");
			
			http.open("POST","requires/sample_requisition_with_booking_controller.php", true);
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
			var row_num=$('#sample_details_container tr').length;
			//alert(row_num);hiddenColorid
			var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				if (form_validation('cboSampleName_'+i+'*cboGarmentItem_'+i+'*txtColor_'+i+'*txtSampleProdQty_'+i+'*txtSubmissionQty_'+i,'Sample Name*Garment Item*Color*Sample Production Qty*Sample Submission Qty*')==false)
				{
					return;
				}
				data_all=data_all+get_submitted_data_string('cboSampleName_'+i+'*cboGarmentItem_'+i+'*txtSmv_'+i+'*txtArticle_'+i+'*txtColor_'+i+'*txtSampleProdQty_'+i+'*txtSubmissionQty_'+i+'*txtDelvStartDate_'+i+'*txtDelvEndDate_'+i+'*txtBuyerSubDate_'+i+'*txtRemarks_'+i+'*txtChargeUnit_'+i+'*txtMchart_'+i+'*cboCurrency_'+i+'*updateidsampledtl_'+i+'*hiddenColorid_'+i+'*txtAllData_'+i,"../../");
			}

			var data="action=save_update_delete_sample_details&operation="+operation+'&total_row='+row_num+'&update_id='+update_id+'&txtDeltedIdSd='+txtDeltedIdSd+data_all;
		   //alert(data); return;
		   freeze_window(operation);
		    if(operation==0 ||  operation==1 || operation==2)
		    {
		       var acknowledge = trim(return_global_ajax_value(document.getElementById('txt_requisition_id').value, 'check_requisition_acknowledg', '', 'requires/sample_requisition_with_booking_controller'));
		       console.log(acknowledge);
		       
		       if(acknowledge*1>0)
		       {
		         release_freezing();
		         alert('Requisition Acknowledged . Any change not allowed');
		         return;
		       }
		    }
		   http.open("POST","requires/sample_requisition_with_booking_controller.php", true);
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
				/*load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_1', 'load_drop_down_required_fabric_sample_name', 'rfSampleId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_1', 'load_drop_down_required_fabric_gmts_item', 'rfItemId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_2', 'load_drop_down_required_fabric_sample_name','raSampleId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_2', 'load_drop_down_required_fabric_gmts_item', 'raItemId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_3', 'load_drop_down_required_fabric_sample_name','waSampleId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_3', 'load_drop_down_required_fabric_gmts_item', 'waItemIid_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_4', 'load_drop_down_required_fabric_sample_name','prSampleId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_4', 'load_drop_down_required_fabric_gmts_item', 'prItemIid_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_5', 'load_drop_down_required_fabric_sample_name','reSampleId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_5', 'load_drop_down_required_fabric_gmts_item', 'reItemIid_1');*/
				$("#cbo_buyer_name").attr('disabled','disabled');
			 }

			if(reponse[0]==1 )
			{
				show_msg(reponse[0]);
				fnc_load_tr(reponse[1],1);
				var upId=document.getElementById("update_id").value;
				/*load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_1', 'load_drop_down_required_fabric_sample_name', 'rfSampleId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_1', 'load_drop_down_required_fabric_gmts_item', 'rfItemId_1');
				
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_2', 'load_drop_down_required_fabric_sample_name','raSampleId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_2', 'load_drop_down_required_fabric_gmts_item', 'raItemId_1');
				
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_3', 'load_drop_down_required_fabric_sample_name','waSampleId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_3', 'load_drop_down_required_fabric_gmts_item', 'waItemIid_1');
				
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_4', 'load_drop_down_required_fabric_sample_name','prSampleId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_4', 'load_drop_down_required_fabric_gmts_item', 'prItemIid_1');
				
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_5', 'load_drop_down_required_fabric_sample_name','reSampleId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_5', 'load_drop_down_required_fabric_gmts_item', 'reItemIid_1');*/
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
		var txt_booking_no=$('#txt_booking_no').val();
		if(update_id=="")
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
				if (form_validation('cboRfSampleName_'+i+'*cboRfGarmentItem_'+i+'*cboRfBodyPart_'+i+'*cboRfFabricNature_'+i+'*txtRfFabricDescription_'+i+'*txtRfGsm_'+i+'*cboRfColorType_'+i+'*cboRfUom_'+i+'*txtRfReqQty_'+i,'Sample Name*Garment Item*Body Part*Fabric Nature*Fabric Desc*Gsm*Color Type*Uom*ReqQty')==false)
				{
					return;
				}

				var source=$('#cboRfFabricSource_'+i).val();

				if(source==2){

					if (form_validation('txtRate_'+i,'Rate')==false)
						{
						return;
						}
				}

//cboRfBodyPartType

				data_all=data_all+get_submitted_data_string('cboRfSampleName_'+i+'*cboRfGarmentItem_'+i+'*txtProcessLoss_'+i+'*txtGrayFabric_'+i+'*txtRate_'+i+'*txtAmount_'+i+'*fabricDelvDate_'+i+'*cboRfFabricSource_'+i+'*txtRfRemarks_'+i+'*cboRfBodyPart_'+i+'*cboRfFabricNature_'+i+'*txtRfFabricDescription_'+i+'*txtRfGsm_'+i+'*txtRfDia_'+i+'*txtRfColor_'+i+'*cboRfColorType_'+i+'*cboRfWidthDia_'+i+'*cboRfUom_'+i+'*txtRfReqQty_'+i+'*updateidRequiredDtl_'+i+'*txtRfColorAllData_'+i+'*libyarncountdeterminationid_'+i+'*cboweighttype_'+i+'*txtcuttablewidth_'+i+'*cboRfBodyPartType_'+i,"../../");
			}

			var data="action=save_update_delete_required_fabric&operation="+operation+'&total_row='+row_nums+'&update_id='+update_id+'&txt_booking_no='+txt_booking_no+'&txtDeltedIdRf='+txtDeltedIdRf+data_all;
			//alert(data);
			freeze_window(operation);
			if(operation==0 || operation==1 || operation==2)
			{
			  var acknowledge = trim(return_global_ajax_value(document.getElementById('txt_requisition_id').value, 'check_requisition_acknowledg', '', 'requires/sample_requisition_with_booking_controller'));
			  console.log(acknowledge);
			  
			  if(acknowledge*1>0)
			  {
			    release_freezing();
			    alert('Requisition Acknowledged . Any change not allowed');
			    return;
			  }
			}
			http.open("POST","requires/sample_requisition_with_booking_controller.php", true);
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
				if (form_validation('cboRaSampleName_'+i+'*cboRaGarmentItem_'+i+'*cboRaTrimsGroup_'+i+'*txtRaDescription_'+i+'*cboRaUom_'+i+'*txtRaReqQty_'+i,'Sample Name*Garment Item*Trims Group*Description*Uom*ReqDzn*ReqQty')==false)
				{
					return;
				}
				data_all=data_all+get_submitted_data_string('cboRaSampleName_'+i+'*cboRaGarmentItem_'+i+'*cboRaFabricSource_'+i+'*accDate_'+i+'*hidnominasupplierid_'+i+'*cboRaTrimsGroup_'+i+'*txtRaDescription_'+i+'*txtRaBrandSupp_'+i+'*cboRaUom_'+i+'*txtRaReqDzn_'+i+'*txtRaReqQty_'+i+'*txtRaRemarks_'+i+'*updateidAccessoriesDtl_'+i,"../../");
			}
			var data="action=save_update_delete_required_accessories&operation="+operation+'&total_row='+row_num+'&update_id='+update_id+'&txtDeltedIdRa='+txtDeltedIdRa+data_all;
			freeze_window(operation);
			if(operation==0 || operation==1 || operation==2)
			{
			  var acknowledge = trim(return_global_ajax_value(document.getElementById('txt_requisition_id').value, 'check_requisition_acknowledg', '', 'requires/sample_requisition_with_booking_controller'));
			  console.log(acknowledge);
			  
			  if(acknowledge*1>0)
			  {
			    release_freezing();
			    alert('Requisition Acknowledged . Any change not allowed');
			    return;
			  }
			}
			http.open("POST","requires/sample_requisition_with_booking_controller.php", true);
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
	
	function fnc_required_wash_info( operation )
	{
		var update_id=$('#update_id').val();
		var txtDeltedIdWa=$('#txtDeltedIdWa').val();
		if(update_id=="")
		{
			alert("Save Master Part!!");
			return;
		}
		else
		{
			var row_num=$('#tbl_required_wash tr').length-1;
			var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				if (form_validation('cboWaSampleName_'+i+'*cboWaGarmentItem_'+i+'*cboWaName_'+i+'*cboReType_'+i+'*txtWaQty_'+i,'Sample Name*Garment Item*Name*Type*Qty Pcs')==false)
				{
					return;
				}
				data_all=data_all+get_submitted_data_string('cboWaSampleName_'+i+'*cboWaGarmentItem_'+i+'*cboWaName_'+i+'*cboReType_'+i+'*txtWaRemarks_'+i+'*cboWaSupplierName_'+i+'*cboWaBodyPart_'+i+'*deliveryWaDate_'+i+'*txtWaQty_'+i+'*txtWaRate_'+i+'*txtWaAmount_'+i+'*txtWacolorBreakdown_'+i+'*updateidRequiredWaDtls_'+i,"../../");
			}
			//txtReQty_ txtReRate_ txtReAmount_ 
			var data="action=save_update_delete_required_wash&operation="+operation+'&total_row='+row_num+'&update_id='+update_id+'&txtDeltedIdWa='+txtDeltedIdWa+data_all;
			//alert(data); return;
			freeze_window(operation);
			if(operation==0 || operation==1 || operation==2)
			{
			  var acknowledge = trim(return_global_ajax_value(document.getElementById('txt_requisition_id').value, 'check_requisition_acknowledg', '', 'requires/sample_requisition_with_booking_controller'));
			  console.log(acknowledge);
			  
			  if(acknowledge*1>0)
			  {
			    release_freezing();
			    alert('Requisition Acknowledged . Any change not allowed');
			    return;
			  }
			}
			http.open("POST","requires/sample_requisition_with_booking_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_required_wash_info_reponse;
		}
	}
	
	function fnc_required_wash_info_reponse()
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
	
	function fnc_required_print_info( operation )
	{
		var update_id=$('#update_id').val();
		var txtDeltedIdPr=$('#txtDeltedIdPr').val();
		if(update_id=="")
		{
			alert("save master part!!");
			return;
		}
		else
		{
			var row_num=$('#tbl_required_print tr').length-1;

			var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				if (form_validation('cboPrSampleName_'+i+'*cboPrGarmentItem_'+i+'*cboPrName_'+i+'*cboPrType_'+i+'*txtPrQty_'+i,'Sample Name*Garment Item*Name*Type*Qty Pcs')==false)
				{
					return;
				}
				data_all=data_all+get_submitted_data_string('cboPrSampleName_'+i+'*cboPrGarmentItem_'+i+'*cboPrName_'+i+'*cboPrType_'+i+'*txtPrRemarks_'+i+'*cboPrSupplierName_'+i+'*cboPrBodyPart_'+i+'*deliveryPrDate_'+i+'*txtPrQty_'+i+'*txtPrRate_'+i+'*txtPrAmount_'+i+'*txtPrcolorBreakdown_'+i+'*updateidRequiredPrDtls_'+i,"../../");
			}
			//txtReQty_ txtReRate_ txtReAmount_ 
			var data="action=save_update_delete_required_print&operation="+operation+'&total_row='+row_num+'&update_id='+update_id+'&txtDeltedIdPr='+txtDeltedIdPr+data_all;
			freeze_window(operation);
			if(operation==0 || operation==1 || operation==2)
			{
			  var acknowledge = trim(return_global_ajax_value(document.getElementById('txt_requisition_id').value, 'check_requisition_acknowledg', '', 'requires/sample_requisition_with_booking_controller'));
			  console.log(acknowledge);
			  
			  if(acknowledge*1>0)
			  {
			    release_freezing();
			    alert('Requisition Acknowledged . Any change not allowed');
			    return;
			  }
			}
			http.open("POST","requires/sample_requisition_with_booking_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_required_print_info_reponse;
		}
	}
	
	function fnc_required_print_info_reponse()
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
			    fnc_load_tr(reponse[1],5);
 			}

			if(reponse[0]==1 )
			{
				show_msg(reponse[0]);
 				fnc_load_tr(reponse[1],5);
 				//fnc_load_tr(reponse[1],1);
 			}
 			if(reponse[0]==2)
			{
			   	//alert(22);
			   	show_msg(reponse[0]);
				fnc_load_tr(reponse[1],5);
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
				if (form_validation('cboReSampleName_'+i+'*cboReGarmentItem_'+i+'*cboReName_'+i+'*cboReTypeId_'+i+'*txtReQty_'+i,'Sample Name*Garment Item*Name*Type*Qty Pcs')==false)
				{
					return;
				}
				 
				data_all=data_all+get_submitted_data_string('cboReSampleName_'+i+'*cboReGarmentItem_'+i+'*cboReName_'+i+'*cboReTypeId_'+i+'*txtReRemarks_'+i+'*cboReSupplierName_'+i+'*cboReBodyPart_'+i+'*deliveryDate_'+i+'*txtReQty_'+i+'*txtReRate_'+i+'*txtReAmount_'+i+'*txtcolorBreakdown_'+i+'*updateidRequiredEmbellishdtl_'+i,"../../");
			}
			//txtReQty_ txtReRate_ txtReAmount_ 
			var data="action=save_update_delete_required_embellishment&operation="+operation+'&total_row='+row_num+'&update_id='+update_id+'&txtDeltedIdRe='+txtDeltedIdRe+data_all;
			freeze_window(operation);
			if(operation==0 || operation==1 || operation==2)
			{
			  var acknowledge = trim(return_global_ajax_value(document.getElementById('txt_requisition_id').value, 'check_requisition_acknowledg', '', 'requires/sample_requisition_with_booking_controller'));
			  console.log(acknowledge);
			  
			  if(acknowledge*1>0)
			  {
			    release_freezing();
			    alert('Requisition Acknowledged . Any change not allowed');
			    return;
			  }
			}
			http.open("POST","requires/sample_requisition_with_booking_controller.php", true);
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
			    fnc_load_tr(reponse[1],6);
 			}

			if(reponse[0]==1 )
			{
				show_msg(reponse[0]);
 				fnc_load_tr(reponse[1],6);
 				//fnc_load_tr(reponse[1],1);
 			}
 			if(reponse[0]==2)
			{
			   	//alert(22);
			   	show_msg(reponse[0]);
			    button_status(7);
 			}
			else if(reponse[0]==10 )
			{
				show_msg(reponse[0]);
 			}
			release_freezing();
		}
	}
	function get_smv_value(id,value){
		var jobId=$('#txt_quotation_id').val();
		var data=jobId+'**'+value;
		var smv_value = return_global_ajax_value( data, 'get_smv_value', '', 'requires/sample_requisition_with_booking_controller');
		$("#txtSmv_"+id).val(smv_value);
	}

	function fnc_load_tr(up_id,type) //after save of details part or browse requisition this function load all saved data from db in specific tr
	{
		if(up_id=="") up_id=$("#update_id").val();
		if(type==1)
		{
			var all_variable_settings=$('#hidd_variable_data').val();
			///var ex_variable=all_variable_settings.split("_");
			if(all_variable_settings==1)
			{
				$('#txtColor_1').attr('readonly',true);
				$('#txtColor_1').attr('placeholder','Browse');
				//$('#txtColor_1').removeAttr("onDblClick").attr("onDblClick","color_select_popup("+1+")");
				
				$('#txtColor_1').removeAttr("onDblClick").attr("onDblClick","openmypage_color_size('requires/sample_requisition_with_booking_controller.php?action=color_popup','Color Search','1','1');");
			}
			else 
			{
				$('#txtColor_1').attr('readonly',false);
				$('#txtColor_1').attr('placeholder','Write/Browse');
				//$('#txtColor_1').removeAttr('onDblClick','onDblClick');	
				$('#txtColor_1').removeAttr("onDblClick").attr("onDblClick","openmypage_color_size('requires/sample_requisition_with_booking_controller.php?action=color_popup','Color Search','1','1');");
			}
			
			var data=up_id+'**'+type;
			var list_view_tr = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sample_requisition_with_booking_controller');
			if(list_view_tr=="" || list_view_tr==0)
			{
				set_button_status(0, permission, 'fnc_sample_details_info',2);
				return;
			}
			else(list_view_tr!='')
			{
				$("#sample_details_container tr").remove();
				$("#sample_details_container").append(list_view_tr);
				set_all_onclick();
				var save_update = return_global_ajax_value( data, 'check_save_update', '', 'requires/sample_requisition_with_booking_controller');
				set_button_status(save_update, permission, 'fnc_sample_details_info',2);
				return;
			}
			return;
		}
		else if(type==2)
		{
			load_drop_down( 'requires/sample_requisition_with_booking_controller', up_id+'_1', 'load_drop_down_required_fabric_sample_name', 'rfSampleId_1');
			load_drop_down( 'requires/sample_requisition_with_booking_controller', up_id+'_1', 'load_drop_down_required_fabric_gmts_item', 'rfItemId_1');
			
			var data=up_id+'**'+type;
			var list_view_tr = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sample_requisition_with_booking_controller');
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
				var save_update = return_global_ajax_value( data, 'check_save_update', '', 'requires/sample_requisition_with_booking_controller');
				set_button_status(save_update, permission, 'fnc_required_fabric_details_info',3,0);

				return;
			}
		}
		else if(type==3)
		{
			load_drop_down( 'requires/sample_requisition_with_booking_controller', up_id+'_2', 'load_drop_down_required_fabric_sample_name','raSampleId_1');
			load_drop_down( 'requires/sample_requisition_with_booking_controller', up_id+'_2', 'load_drop_down_required_fabric_gmts_item', 'raItemId_1');
			
			var data=up_id+'**'+type;

			var list_view_tr = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sample_requisition_with_booking_controller');
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
			load_drop_down( 'requires/sample_requisition_with_booking_controller', up_id+'_3', 'load_drop_down_required_fabric_sample_name','waSampleId_1');
			load_drop_down( 'requires/sample_requisition_with_booking_controller', up_id+'_3', 'load_drop_down_required_fabric_gmts_item', 'waItemIid_1');
			
			var data=up_id+'**'+type;
			var list_view_tr = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sample_requisition_with_booking_controller');
			if(list_view_tr=="" || list_view_tr==0)
			{
				set_button_status(0, permission, 'fnc_required_wash_info',5,0);
				return;
			}
			else(list_view_tr!='')
			{
				$("#required_wash_container tr").remove();
				$("#required_wash_container").append(list_view_tr);
				set_all_onclick();
				var save_update = return_global_ajax_value( data, 'check_save_update', '', 'requires/sample_requisition_with_booking_controller');
				set_button_status(save_update, permission, 'fnc_required_wash_info',5,0);
				return;
			}
		}
		else if(type==5)
		{
			load_drop_down( 'requires/sample_requisition_with_booking_controller', up_id+'_4', 'load_drop_down_required_fabric_sample_name','prSampleId_1');
			load_drop_down( 'requires/sample_requisition_with_booking_controller', up_id+'_4', 'load_drop_down_required_fabric_gmts_item', 'prItemIid_1');
			var data=up_id+'**'+type;
			var list_view_tr = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sample_requisition_with_booking_controller');
			if(list_view_tr=="" || list_view_tr==0)
			{
				set_button_status(0, permission, 'fnc_required_print_info',6,0);
				return;
			}
			else(list_view_tr!='')
			{
				$("#required_print_container tr").remove();
				$("#required_print_container").append(list_view_tr);
				set_all_onclick();
				var save_update = return_global_ajax_value( data, 'check_save_update', '', 'requires/sample_requisition_with_booking_controller');
				set_button_status(save_update, permission, 'fnc_required_print_info',6,0);
				return;
			}
		}
		else if(type==6)
		{
			load_drop_down( 'requires/sample_requisition_with_booking_controller', up_id+'_5', 'load_drop_down_required_fabric_sample_name','reSampleId_1');
			load_drop_down( 'requires/sample_requisition_with_booking_controller', up_id+'_5', 'load_drop_down_required_fabric_gmts_item', 'reItemIid_1');
			var data=up_id+'**'+type;
			var list_view_tr = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sample_requisition_with_booking_controller');
			if(list_view_tr=="" || list_view_tr==0)
			{
				set_button_status(0, permission, 'fnc_required_embellishment_info',7,0);
				return;
			}
			else(list_view_tr!='')
			{
				$("#required_embellishment_container tr").remove();
				$("#required_embellishment_container").append(list_view_tr);
				set_all_onclick();
				var save_update = return_global_ajax_value( data, 'check_save_update', '', 'requires/sample_requisition_with_booking_controller');
				set_button_status(save_update, permission, 'fnc_required_embellishment_info',7,0);
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
			var style_id="";
			if($('#cbo_sample_stage').val()==1) style_id=$('#txt_quotation_id').val();
			get_php_form_data(update_id_dtl+'__'+style_id, "load_data_to_sizeinfo","requires/sample_requisition_with_booking_controller" );
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
		if(form_validation('cbo_sample_stage','Sample Stage')==false)
		{
			return;   
		}
		else
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
			reset_form('required_wash_1','','');
			set_button_status(0, permission, 'fnc_required_wash_info',5);
		}
		else if(type==6)
		{
			reset_form('required_print_1','','');
			set_button_status(0, permission, 'fnc_required_print_info',6);
		}
		else if(type==7)
		{
			reset_form('required_embellishment_1','','');
			set_button_status(0, permission, 'fnc_required_embellishment_info',7);
		}
	}

	function auto_sampleDtls_color_generate(inc)
	{
		var sampleName=$('#cboRfSampleName_'+inc).val();
		var garmentItem=$('#cboRfGarmentItem_'+inc).val();
		var update_id=$('#update_id').val();
		var data=sampleName+"***"+garmentItem+"***"+update_id;
		var result = return_global_ajax_value( data, 'auto_sd_color_generation', '', 'requires/sample_requisition_with_booking_controller');
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
			$("#txt_style_name").attr("placeholder", "Browse");
			$("#cbo_season_name").removeAttr('disabled','disabled');
			$("#cbo_season_year").removeAttr('disabled','disabled');
			$("#cbo_brand_id").removeAttr('disabled','disabled');
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
			$("#txt_bhmerchant").removeAttr('disabled','');
			$("#cbo_agent").removeAttr('disabled','');
			$("#txt_est_ship_date").removeAttr('disabled','');
			$("#txt_remarks").removeAttr('disabled','');
			$('#txt_style_name').attr("ondblclick","check_sample_stage()");
			$("#txt_style_name").attr("placeholder", "Browse/Write");
			$("#cbo_season_name").attr('disabled','disabled');
			$("#cbo_season_year").removeAttr('disabled','disabled');
			$("#cbo_brand_id").attr('disabled','disabled');
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
			$("#txt_style_name").attr("placeholder", "Write");
			$("#cbo_season_name").removeAttr('disabled','disabled');
			$("#cbo_season_year").removeAttr('disabled','disabled');
			$("#cbo_brand_id").removeAttr('disabled','disabled');
		}
	}

	function open_fabric_description_popup(i)
	{
		var RfFabricNature=$('#cboRfFabricNature_'+i).val();
		 
		
		var page_link='requires/sample_requisition_with_booking_controller.php?action=fabric_description_popup&incid='+i+'&quotation_id='+$("#txt_quotation_id").val()+'&sampleStage='+$("#cbo_sample_stage").val()+'&RfFabricNature='+RfFabricNature;
		var title="Fabric Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=350px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var fabdata=this.contentDoc.getElementById("hid_libDes").value; // mrr number
			//alert(fabdata);
			var exfabdata = fabdata.split("_");
			var fabric_description=trim(exfabdata[3])+' '+trim(exfabdata[4])+' '+trim(exfabdata[5])+' '+trim(exfabdata[9]);
			//1090_d_66CW_2X Twill S/D_14X12_86X50_245___
			
			$("#libyarncountdeterminationid_"+i).val(exfabdata[0]);
			$("#txtRfFabricDescription_"+i).val(fabric_description);
			$("#txtRfGsm_"+i).val(exfabdata[6]);
			$("#cboweighttype_"+i).val(exfabdata[7]);
			$("#txtRfDia_"+i).val(exfabdata[10]);
			$("#txtcuttablewidth_"+i).val(exfabdata[11]);
			//$("#cboRfColorType_"+i).val(exfabdata[8]);
		}
	}

	function add_break_down_tr_bk(i)
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
			if (!$('#chkBoxSample').is(":checked"))
			{
				$('#txtAllData_'+i).val('');
				$('#txtSampleProdQty_'+i).val('');
				$('#txtSubmissionQty_'+i).val('');
			}

			$('#txtColor_'+i).val('');
			$('#txtSize_'+i).val('');


			$('#txtChargeUnit_'+i).val('');
			$('#txtMchart_'+i).val('');
			$('#txtColor_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_color_size('requires/sample_requisition_with_booking_controller.php?action=color_popup','Color Search','1','"+i+"');");
			$('#txtSampleProdQty_'+i).removeAttr("onfocus");


			$('#txtSampleProdQty_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_sizeinfo('requires/sample_requisition_with_booking_controller.php?action=sizeinfo_popup','Size Search','"+i+"')");

			$('#txtFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../',document.getElementById('updateidsampledtl_"+i+"').value,'', 'sample_details_1', 0 ,1);");

			$('#txtRemarks_'+i).removeAttr("onClick").attr("onClick","openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','1','"+i+"');");

			$('#cboSampleName_'+i).removeAttr("disabled","");
			$('#cboGarmentItem_'+i).removeAttr("disabled","");
			$('#txtSampleProdQty_'+i).removeAttr("disabled","");
			$('#txtDelvStartDate_'+i).removeAttr("class").attr("class","datepicker");
			$('#txtDelvEndDate_'+i).removeAttr("class").attr("class","datepicker");
			$('#txtBuyerSubDate_'+i).removeAttr("class").attr("class","datepicker");
			$('#increase_'+i).removeAttr("value").attr("value","+");
			$('#decrease_'+i).removeAttr("value").attr("value","-");
			$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
			set_all_onclick();
		}
	}
	function add_break_down_tr(i,tr)
	{
		var row_num=$('#tbl_sample_details tbody tr').length;
		var j=i;
		var index = $(tr).closest("tr").index();
		console.log("index "+index+" i="+i);
		var i=row_num;
		i++;
		var k=i-1;
		var tr=$("#tbl_sample_details tbody tr:eq("+index+")");
		var cl=$("#tbl_sample_details tbody tr:eq("+index+")").clone().find("input,select").each(function() {
		$(this).attr({
		'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
		'value': function(_, value) { return value }              
		});
		}).end();
		tr.after(cl);
		$('#cboSampleName_'+i).val($('#cboSampleName_'+k).val());
		$('#cboGarmentItem_'+i).val($('#cboGarmentItem_'+k).val());
		$('#updateidsampledtl_'+i).val('');
		if (!$('#chkBoxSample').is(":checked"))
		{
			$('#txtAllData_'+i).val('');
			$('#txtSampleProdQty_'+i).val('');
			$('#txtSubmissionQty_'+i).val('');
		}

		$('#txtColor_'+i).val('');
		$('#txtSize_'+i).val('');


		$('#txtChargeUnit_'+i).val('');
		$('#txtMchart_'+i).val('');
		$('#cboGarmentItem_' + i).removeAttr("onchange").attr("onchange", "get_smv_value(" + i + ",this.value);");
		$('#txtColor_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_color_size('requires/sample_requisition_with_booking_controller.php?action=color_popup','Color Search','1','"+i+"');");
		$('#txtSampleProdQty_'+i).removeAttr("onfocus");


		$('#txtSampleProdQty_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_sizeinfo('requires/sample_requisition_with_booking_controller.php?action=sizeinfo_popup','Size Search','"+i+"')");

		$('#txtFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../',document.getElementById('updateidsampledtl_"+i+"').value,'', 'sample_details_1', 0 ,1);");

		$('#txtRemarks_'+i).removeAttr("onClick").attr("onClick","openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','1','"+i+"');");

		$('#cboSampleName_'+i).removeAttr("disabled","");
		$('#cboGarmentItem_'+i).removeAttr("disabled","");
		$('#txtSampleProdQty_'+i).removeAttr("disabled","");
		$('#txtDelvStartDate_'+i).removeAttr("class").attr("class","datepicker");
		$('#txtDelvEndDate_'+i).removeAttr("class").attr("class","datepicker");
		$('#txtBuyerSubDate_'+i).removeAttr("class").attr("class","datepicker");
		$('#increase_'+i).removeAttr("value").attr("value","+");
		$('#decrease_'+i).removeAttr("value").attr("value","-");
		$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+",this);");
		$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");

		var row_num=$('#tbl_sample_details tbody tr').length;	
		for(i = 1;i <= row_num;i++)
		{
		    $("#tbl_sample_details tr:eq("+i+")").find("input,select").each(function() {
		       $(this).attr({
    				'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
    				'value': function(_, value) { return value }
			    });
			 
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+",this);");
				$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
				$('#cboGarmentItem_' + i).removeAttr("onchange").attr("onchange", "get_smv_value(" + i + ",this.value);");
				$('#txtColor_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_color_size('requires/sample_requisition_with_booking_controller.php?action=color_popup','Color Search','1','"+i+"');");
				$('#txtSampleProdQty_'+i).removeAttr("onfocus");
				$('#txtSampleProdQty_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_sizeinfo('requires/sample_requisition_with_booking_controller.php?action=sizeinfo_popup','Size Search','"+i+"')");
				$('#txtFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../',document.getElementById('updateidsampledtl_"+i+"').value,'', 'sample_details_1', 0 ,1);");
				$('#txtRemarks_'+i).removeAttr("onClick").attr("onClick","openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','1','"+i+"');");
				$('#cboSampleName_'+i).removeAttr("disabled","");
				$('#cboGarmentItem_'+i).removeAttr("disabled","");
				$('#txtSampleProdQty_'+i).removeAttr("disabled","");
				$('#txtDelvStartDate_'+i).removeAttr("class").attr("class","datepicker");
				$('#txtDelvEndDate_'+i).removeAttr("class").attr("class","datepicker");
				$('#txtBuyerSubDate_'+i).removeAttr("class").attr("class","datepicker");
				$('#increase_'+i).removeAttr("value").attr("value","+");
				$('#decrease_'+i).removeAttr("value").attr("value","-");
				$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+",this);");
				$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
			})
		}
		set_all_onclick();

	}
	function add_break_down_tr_from_color(i,tr)
	{
		var row_num=$('#tbl_sample_details tbody tr').length;
		var j=i;
		//var index = $(tr).closest("tr").index();
		var index = tr;
		console.log("index "+index+" i="+i);
		var i=row_num;
		i++;
		var k=i-1;
		var tr=$("#tbl_sample_details tbody tr:eq("+index+")");
		var cl=$("#tbl_sample_details tbody tr:eq("+index+")").clone().find("input,select").each(function() {
		$(this).attr({
		'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
		'value': function(_, value) { return value }              
		});
		}).end();
		tr.after(cl);
		$('#cboSampleName_'+i).val($('#cboSampleName_'+k).val());
		$('#cboGarmentItem_'+i).val($('#cboGarmentItem_'+k).val());
		$('#updateidsampledtl_'+i).val('');
		if (!$('#chkBoxSample').is(":checked"))
		{
			$('#txtAllData_'+i).val('');
			$('#txtSampleProdQty_'+i).val('');
			$('#txtSubmissionQty_'+i).val('');
		}

		$('#txtColor_'+i).val('');
		$('#txtSize_'+i).val('');


		$('#txtChargeUnit_'+i).val('');
		$('#txtMchart_'+i).val('');
		$('#txtColor_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_color_size('requires/sample_requisition_with_booking_controller.php?action=color_popup','Color Search','1','"+i+"');");
		$('#txtSampleProdQty_'+i).removeAttr("onfocus");


		$('#txtSampleProdQty_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_sizeinfo('requires/sample_requisition_with_booking_controller.php?action=sizeinfo_popup','Size Search','"+i+"')");

		$('#txtFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../',document.getElementById('updateidsampledtl_"+i+"').value,'', 'sample_details_1', 0 ,1);");

		$('#txtRemarks_'+i).removeAttr("onClick").attr("onClick","openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','1','"+i+"');");
		$('#cboGarmentItem_'+i).removeAttr("onchange").attr("onchange", "get_smv_value("+i+",this.value);");

		$('#cboSampleName_'+i).removeAttr("disabled","");
		$('#cboGarmentItem_'+i).removeAttr("disabled","");
		$('#txtSampleProdQty_'+i).removeAttr("disabled","");
		$('#txtDelvStartDate_'+i).removeAttr("class").attr("class","datepicker");
		$('#txtDelvEndDate_'+i).removeAttr("class").attr("class","datepicker");
		$('#txtBuyerSubDate_'+i).removeAttr("class").attr("class","datepicker");
		$('#increase_'+i).removeAttr("value").attr("value","+");
		$('#decrease_'+i).removeAttr("value").attr("value","-");
		$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+",this);");
		$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");

		var row_num=$('#tbl_sample_details tbody tr').length;	
		for(i = 1;i <= row_num;i++)
		{
		    $("#tbl_sample_details tr:eq("+i+")").find("input,select").each(function() {
		       $(this).attr({
    				'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
    				'value': function(_, value) { return value }
			    });
			 
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+",this);");
				$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
				$('#cboGarmentItem_'+i).removeAttr("onchange").attr("onchange", "get_smv_value("+i+",this.value);");

				$('#txtColor_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_color_size('requires/sample_requisition_with_booking_controller.php?action=color_popup','Color Search','1','"+i+"');");
				$('#txtSampleProdQty_'+i).removeAttr("onfocus");


				$('#txtSampleProdQty_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_sizeinfo('requires/sample_requisition_with_booking_controller.php?action=sizeinfo_popup','Size Search','"+i+"')");

				$('#txtFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../',document.getElementById('updateidsampledtl_"+i+"').value,'', 'sample_details_1', 0 ,1);");

				$('#txtRemarks_'+i).removeAttr("onClick").attr("onClick","openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','1','"+i+"');");
				$('#cboSampleName_'+i).removeAttr("disabled","");
				$('#cboGarmentItem_'+i).removeAttr("disabled","");
				$('#txtSampleProdQty_'+i).removeAttr("disabled","");
				$('#txtDelvStartDate_'+i).removeAttr("class").attr("class","datepicker");
				$('#txtDelvEndDate_'+i).removeAttr("class").attr("class","datepicker");
				$('#txtBuyerSubDate_'+i).removeAttr("class").attr("class","datepicker");
				$('#increase_'+i).removeAttr("value").attr("value","+");
				$('#decrease_'+i).removeAttr("value").attr("value","-");
				$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+",this);");
				$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
			})
		}
		set_all_onclick();

	}

	function fn_deleteRow(rowNo)
	{
		var index=rowNo-1
		var numRow = $('table#tbl_sample_details tbody tr').length;
		//alert(numRow+'='+rowNo);
		if(rowNo==1)
		{
			return false;
		}
		$("table#tbl_sample_details tbody tr:eq("+index+")").remove();
		for(i = rowNo;i <= numRow;i++)
		{
			$("#tbl_sample_details tr:eq("+i+")").find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'value': function(_, value) { return value }
				});
				$('#txtColor_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_color_size('requires/sample_requisition_with_booking_controller.php?action=color_popup','Color Search','1','"+i+"');");
			$('#txtSampleProdQty_'+i).removeAttr("onfocus");


			$('#txtSampleProdQty_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_sizeinfo('requires/sample_requisition_with_booking_controller.php?action=sizeinfo_popup','Size Search','"+i+"')");

			$('#txtFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../',document.getElementById('updateidsampledtl_"+i+"').value,'', 'sample_details_1', 0 ,1);");

			$('#txtRemarks_'+i).removeAttr("onClick").attr("onClick","openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','1','"+i+"');");

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
		var row_num=$('#tbl_required_fabric tbody tr').length;
		var mst_id=$("#update_id").val();
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
			$('#cboRfColorType_'+i).val($('#cboRfColorType_'+k).val());
			$('#cboRfWidthDia_'+i).val($('#cboRfWidthDia_'+k).val());
			$('#updateidRequiredDtl_'+i).val('');
			$('#cboRfBodyPart_'+i).val('');
			$('#cboRfFabricNature_'+i).val(3);
			$('#txtRfFabricDescription_'+i).val('');
			$('#txtRfGsm_'+i).val('');
			$('#cboweighttype_'+i).val('');
			$('#txtRfDia_'+i).val('');
			$('#txtcuttablewidth_'+i).val('');
			$('#txtRfColor_'+i).val('');
 			$('#cboRfUom_'+i).val($('#cboRfUom_'+k).val());
			$('#txtRfReqDzn_'+i).val('');
			$('#txtRfReqQty_'+i).val('');
			$('#txtRfColorAllData_'+i).val('');
			$('#txtMemoryDataRf_'+i).val('');
			$('#txtProcessLoss_'+i).val('');
			$('#txtGrayFabric_'+i).val('');

			$('#txtRfReqDzn_'+i).removeAttr("onblur").attr("onblur","calculate_required_qty('1','"+i+"')");
			$('#cboRfBodyPart_'+i).removeAttr("onchange").attr("onchange","load_data_to_rfcolor('"+i+"')");
			$('#txtProcessLoss_'+i).removeAttr("onchange").attr("onchange","calculate_requirement('"+i+"')");
			$('#txtRate_'+i).removeAttr("onchange").attr("onchange","calculate_amount('"+i+"')");
			$('#cboRfSampleName_'+i).removeAttr("onchange").attr("onchange","sample_wise_item('"+mst_id+"',this.value,'"+i+"',1)");
 			$('#txtRfColor_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_rf_color('requires/sample_requisition_with_booking_controller.php?action=color_popup_rf','Color Search','"+i+"');");
 			$('#txtRfRemarks_'+i).removeAttr("onClick").attr("onClick","openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','2','"+i+"');");

			$('#txtRfFabricDescription_'+i).removeAttr("onDblClick").attr("onDblClick","open_fabric_description_popup("+i+")");

			$('#txtRfFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../',document.getElementById('updateidRequiredDtl_"+i+"').value,'', 'required_fabric_1', 0 ,1);");
			$('#fabricDelvDate_'+i).removeAttr("class").attr("class","datepicker");

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
		var numRow = $('table#tbl_required_fabric tbody tr').length;
		//alert(numRow+'='+rowNo);
		if( rowNo==1)
		{
			return false;
		}
		$("table#tbl_required_fabric tbody tr:eq("+index+")").remove();
		for(i = rowNo;i <= numRow;i++)
		{
			$("#tbl_required_fabric tr:eq("+i+")").find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'value': function(_, value) { return value }
				});
				//$("#tbl_required_fabric tr:last").removeAttr('id').attr('id','fabriccosttbltr_'+i);
				$('#txtRfReqDzn_'+i).removeAttr("onblur").attr("onblur","calculate_required_qty('1','"+i+"')");
				$('#cboRfBodyPart_'+i).removeAttr("onchange").attr("onchange","load_data_to_rfcolor('"+i+"')");

				$('#txtRfColor_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_rf_color('requires/sample_requisition_with_booking_controller.php?action=color_popup_rf','Color Search','"+i+"');");

				$('#txtRfFabricDescription_'+i).removeAttr("onDblClick").attr("onDblClick","open_fabric_description_popup("+i+")");

				$('#txtRfFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../',document.getElementById('updateidRequiredDtl_"+i+"').value,'', 'required_fabric_1', 0 ,1);");

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
		var mst_id=$("#update_id").val();
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
			$('#hidnominasupplierid_'+i).val('');
			$('#txtnominasupplier_'+i).val('');
			$('#cboRaUom_'+i).val($('#cboRaUom_'+k).val());
			$('#txtRaReqDzn_'+i).val('');
			$('#txtRaReqQty_'+i).val('');
			$('#txtRaRemarks_'+i).val('');
			$('#txtMemoryDataRa_'+i).val('');

			//$('#txtRaReqDzn_'+i).removeAttr("onblur").attr("onblur","calculate_required_qty('2','"+i+"')");
			$('#txtnominasupplier_'+i).removeAttr("onDblClick").attr("onDblClick","fncopenpopup_trimsupplier("+i+")");
			$('#cbogrouptext_'+i).removeAttr("onDblClick").attr("onDblClick","openpopup_itemgroup("+i+");");
			$('#txtRaRemarks_'+i).removeAttr("onClick").attr("onClick","openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','3','"+i+"');");

			$('#txtRaFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../',document.getElementById('updateidAccessoriesDtl_"+i+"').value,'', 'required_accessories_1', 0 ,1);");
			$('#cboRaSampleName_'+i).removeAttr("onchange").attr("onchange","sample_wise_item('"+mst_id+"',this.value,'"+i+"',2)");
			$('#accDate_'+i).removeAttr("class").attr("class","datepicker");
			$('#increasera_'+i).removeAttr("value").attr("value","+");
			$('#decreasera_'+i).removeAttr("value").attr("value","-");
			$('#increasera_'+i).removeAttr("onclick").attr("onclick","add_ra_tr("+i+");");
			$('#decreasera_'+i).removeAttr("onclick").attr("onclick","fn_ra_deleteRow("+i+");");
			set_all_onclick();
		}
	}
	
	function fncopenpopup_trimsupplier(inc)
	{
		var nominasupplier=$('#hidnominasupplierid_'+inc).val();

		var page_link="requires/sample_requisition_with_booking_controller.php?nominasupplier="+trim(nominasupplier)+"&action=openpopup_trimsupplier";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Nominated Supplier PopUp', 'width=450px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var suppdata=this.contentDoc.getElementById("suppdata").value;
			//alert(itemdata);
			var suppdataarr=suppdata.split(",");
			var a=0;  var suppid=""; var suppname="";
			for(var b=1; b<=suppdataarr.length; b++)
			{
				var exdata="";
				var exdata=suppdataarr[a].split("***");

				if(suppid=="") suppid=exdata[0]; else suppid+=','+exdata[0];
				if(suppname=="") suppname=exdata[1]; else suppname+=','+exdata[1];
				a++;
			}

			$('#hidnominasupplierid_'+inc).val(suppid);
			$('#txtnominasupplier_'+inc).val(suppname);
		}
	}

	function fn_ra_deleteRow(rowNo)
	{


			var numRow1 = $('table#tbl_required_accessories tbody tr').length;
			if(numRow1!=1)
			{	
				var index=rowNo-1
				$("table#tbl_required_accessories tbody tr:eq("+index+")").remove()
				var numRow1 = $('table#tbl_required_accessories tbody tr').length;
				for(i = rowNo;i <= numRow1;i++)
				{
					$("#tbl_required_accessories tr:eq("+i+")").find("input,select").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
							//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
							'value': function(_, value) { return value }
						});
						$('#tbl_required_accessories tr:eq('+i+') td:eq(4)').attr('id','tdsupplier_'+i);
						if($('#seq_'+i).val()=="" || $('#seq_'+i).val()!="")
						{
							$('#seq_'+i).val( i );
						}
						$('#txtnominasupplier_'+i).removeAttr("onDblClick").attr("onDblClick","fncopenpopup_trimsupplier("+i+")");
						$('#decreasera_'+i).removeAttr("onClick").attr("onClick","fn_ra_deleteRow("+i+");");
		
					})
				}
			}
			
		



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
				// $('#tbl_required_accessories tbody tr:last').remove();
			}
			else
			{
				return false;
			}
		}
	}
	
	function add_wash_tr(i)
	{
		var row_num=$('#tbl_required_wash tbody tr').length;
		var mst_id=$("#update_id").val();
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			var k=i-1;
			$("#tbl_required_wash tbody tr:last").clone().find("input,select").each(function(){
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
					'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_required_wash");
			$("#tbl_required_wash tbody tr:last td:nth-child(4)").removeAttr('id').attr('id','reType_'+i);
			$('#updateidRequiredWaDtls_'+i).val('');
			$('#cboWaSampleName_'+i).val($('#cboWaSampleName_'+k).val());
			$('#cboWaGarmentItem_'+i).val($('#cboWaGarmentItem_'+k).val());
			$('#cboWaName_'+i).val(3);
			$('#cboReType_'+i).val('');
			$('#txtWaRemarks_'+i).val('');
			
			$('#txtWaQty_'+i).val('');
			$('#txtWaRate_'+i).val('');
			$('#txtWaAmount_'+i).val('');
			$('#txtWacolorBreakdown_'+i).val('');
			
			$('#txtWaRemarks_'+i).removeAttr("onClick").attr("onClick","openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','4','"+i+"');");
			
			$('#txtWaQty_'+i).removeAttr("onClick").attr("onClick","open_consumption_popupWash('requires/sample_requisition_with_booking_controller.php?action=consumption_popup','Consumtion Entry Form','"+i+"');");
			
			$('#waTxtFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../', document.getElementById('updateidRequiredWaDtls_"+i+"').value,'', 'requiredwash_1', 0 ,1);");
			//$('#cboWaSampleName_'+i).removeAttr("onchange").attr("onchange","sample_wise_item('"+mst_id+"',this.value,'"+i+"',3)");
			$('#deliveryWaDate_'+i).removeAttr("class").attr("class","datepicker");
			$('#increasere_'+i).removeAttr("value").attr("value","+");
			$('#decreasere_'+i).removeAttr("value").attr("value","-");
			$('#increasere_'+i).removeAttr("onclick").attr("onclick","add_wash_tr("+i+");");
			$('#decreasere_'+i).removeAttr("onclick").attr("onclick","fn_wash_deleteRow("+i+");");
			//$('#cboWaName_'+i).removeAttr("onChange").attr("onChange","cbotype_loder( "+i+" )");
			set_all_onclick();
		}
	}
	
	function fn_wash_deleteRow(rowNo)
	{
		if($('#tbl_required_wash').val()!=2)
		{
			var numRow = $('#tbl_required_wash tbody tr').length;
			var k=rowNo-1;
			if(numRow==rowNo && rowNo!=1)
			{
				var updateIdDtls=$('#updateidRequiredWaDtls_'+rowNo).val();
				var txt_deleted_id=document.getElementById("txtDeltedIdWa").value;
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
				document.getElementById("txtDeltedIdWa").value=selected_id;
				$('#tbl_required_wash tbody tr:last').remove();
			}
			else
			{
				return false;
			}
		}
	}
	
	function add_print_tr(i)
	{
		var row_num=$('#tbl_required_print tbody tr').length;
		var mst_id=$("#update_id").val();
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			var k=i-1;
			$("#tbl_required_print tbody tr:last").clone().find("input,select").each(function(){
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
					'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_required_print");
			$("#tbl_required_print tbody tr:last td:nth-child(4)").removeAttr('id').attr('id','reType_'+i);
			$('#updateidRequiredPrDtls_'+i).val('');
			$('#cboPrSampleName_'+i).val($('#cboPrSampleName_'+k).val());
			$('#cboPrGarmentItem_'+i).val($('#cboPrGarmentItem_'+k).val());
			$('#cboPrName_'+i).val(1);
			$('#cboPrType_'+i).val(0);
			$('#txtPrRemarks_'+i).val('');
			
			$('#txtPrQty_'+i).val('');
			$('#txtPrRate_'+i).val('');
			$('#txtPrAmount_'+i).val('');
			$('#txtPrcolorBreakdown_'+i).val('');
			
			$('#txtPrRemarks_'+i).removeAttr("onClick").attr("onClick","openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','5','"+i+"');");
			
			$('#txtPrQty_'+i).removeAttr("onClick").attr("onClick","open_consumption_popupPrint('requires/sample_requisition_with_booking_controller.php?action=consumption_popup','Consumtion Entry Form','"+i+"');");
			
			$('#prTxtFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../', document.getElementById('updateidRequiredPrDtls_"+i+"').value,'', 'requiredPrint_1', 0 ,1);");
			$('#cboPrSampleName_'+i).removeAttr("onchange").attr("onchange","sample_wise_item('"+mst_id+"',this.value,'"+i+"',3)");
			$('#deliveryPrDate_'+i).removeAttr("class").attr("class","datepicker");
			$('#increasere_'+i).removeAttr("value").attr("value","+");
			$('#decreasere_'+i).removeAttr("value").attr("value","-");
			$('#increasere_'+i).removeAttr("onclick").attr("onclick","add_print_tr("+i+");");
			$('#decreasere_'+i).removeAttr("onclick").attr("onclick","fn_print_deleteRow("+i+");");
			//$('#cboPrName_'+i).removeAttr("onChange").attr("onChange","cbotype_loder( "+i+" )");
			set_all_onclick();
		}
	}
	
	function fn_print_deleteRow(rowNo)
	{
		if($('#tbl_required_print').val()!=2)
		{
			var numRow = $('#tbl_required_print tbody tr').length;
			var k=rowNo-1;
			if(numRow==rowNo && rowNo!=1)
			{
				var updateIdDtls=$('#updateidRequiredPrDtls_'+rowNo).val();
				var txt_deleted_id=document.getElementById("txtDeltedIdPr").value;
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
				document.getElementById("txtDeltedIdPr").value=selected_id;
				$('#tbl_required_print tbody tr:last').remove();
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
		var mst_id=$("#update_id").val();
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
			$('#cboReName_'+i).val(3);
			$('#cboReType_'+i).val(0);
			$('#txtReRemarks_'+i).val('');
			
			$('#txtReQty_'+i).val('');
			$('#txtReRate_'+i).val('');
			$('#txtReAmount_'+i).val('');
			$('#txtcolorBreakdown_'+i).val('');
			
			$('#txtReRemarks_'+i).removeAttr("onClick").attr("onClick","openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','6','"+i+"');");
			
			$('#txtReQty_'+i).removeAttr("onClick").attr("onClick","open_consumption_popup('requires/sample_requisition_with_booking_controller.php?action=consumption_popup','Consumtion Entry Form','"+i+"');");
			
			$('#reTxtFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../', document.getElementById('updateidRequiredEmbellishdtl_"+i+"').value,'', 'required_embellishment_1', 0 ,1);");
			$('#cboReSampleName_'+i).removeAttr("onchange").attr("onchange","sample_wise_item('"+mst_id+"',this.value,'"+i+"',3)");
			$('#deliveryDate_'+i).removeAttr("class").attr("class","datepicker");
			$('#increasere_'+i).removeAttr("value").attr("value","+");
			$('#decreasere_'+i).removeAttr("value").attr("value","-");
			$('#increasere_'+i).removeAttr("onclick").attr("onclick","add_re_tr("+i+");");
			$('#decreasere_'+i).removeAttr("onclick").attr("onclick","fn_re_deleteRow("+i+");");
			//$('#cboReName_'+i).removeAttr("onChange").attr("onChange","cbotype_loder( "+i+" )");
			set_all_onclick();
		}
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

	function cbotype_loder( i )
	{
		var cboembname=document.getElementById('cboReName_'+i).value;
		load_drop_down( 'requires/sample_requisition_with_booking_controller', cboembname+'_'+i, 'load_drop_down_emb_type', 'reType_'+i );
	}

	function openmypage(sample_stage)
	{
		if(sample_stage==1)
		{
			var cbo_company_name=$('#cbo_company_name').val();
			var title = 'Style ID Search';
			var page_link = 'requires/sample_requisition_with_booking_controller.php?&action=style_id_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&cbo_company_name='+cbo_company_name, title, 'width=1200px,height=400px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var mst_tbl_id=this.contentDoc.getElementById("selected_job").value;//mst id

				if (mst_tbl_id!="")
				{
					freeze_window(5);
					get_php_form_data(mst_tbl_id, "populate_data_from_search_popup", "requires/sample_requisition_with_booking_controller" );
					$("#cbo_company_name").attr('disabled','disabled');
					$("#cbo_location_name").attr('disabled','disabled');
					$("#cbo_buyer_name").attr('disabled','disabled');
					$("#cbo_dealing_merchant").attr('disabled','disabled');
					$("#cbo_product_department").attr('disabled','disabled');
					var browse_job_no=$('#txt_quotation_job_no').val();
					load_drop_down( 'requires/sample_requisition_with_booking_controller', '', 'load_drop_down_trims_group_from_budget_for_after_order', 'ra_trims_group_1');
					release_freezing();
				}
			}
		}
		else if(sample_stage==2)
		{
			var company = $("#cbo_company_name").val();
			var page_link='requires/sample_requisition_with_booking_controller.php?action=inquiry_popup&company='+company;
			var title="Search  Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1190px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var inquiryId=this.contentDoc.getElementById("txt_inquiry_id").value; // mrr number				
				get_php_form_data(inquiryId, "populate_data_from_inquiry_search", "requires/sample_requisition_with_booking_controller");
				$("#cbo_company_name").attr('disabled','disabled');
				$("#cbo_buyer_name").attr('disabled','disabled');
				$("#cbo_company_name").attr('disabled','disabled');
				$("#cbo_dealing_merchant").attr('disabled','disabled');
	 			$("#cbo_season_year").attr('disabled','disabled');
				$("#cbo_brand_id").attr('disabled','disabled');
	 			$("#txt_est_ship_date").attr('disabled','disabled');
	 		}
	 	}
	}
	
	function openmypage_remarks(page_link,title,type,inc)
	{
		if(type==1) // for sample details
		{
			var remarks=$("#txtRemarks_"+inc).val();
			var page_link = page_link + "&remarks="+remarks;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=370px,height=250px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var txt_remarks=this.contentDoc.getElementById("txt_remarks").value;

				if (txt_remarks!="" || 1==1)
				{
					$("#txtRemarks_"+inc).val( txt_remarks );
				}
			}
		}

		if(type==2) // for required fabric
		{
			var remarks=$("#txtRfRemarks_"+inc).val();
			var page_link = page_link + "&remarks="+remarks;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=370px,height=250px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var txt_remarks=this.contentDoc.getElementById("txt_remarks").value;

				if (txt_remarks!="" || 1==1)
				{
					$("#txtRfRemarks_"+inc).val( txt_remarks );
				}
			}
		}

		if(type==3) // for required acc
		{
			var remarks=$("#txtRaRemarks_"+inc).val();
			var page_link = page_link + "&remarks="+remarks;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=370px,height=250px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var txt_remarks=this.contentDoc.getElementById("txt_remarks").value;

				if (txt_remarks!="" || 1==1)
				{
					$("#txtRaRemarks_"+inc).val( txt_remarks );
				}
			}
		}

		if(type==4) // for Wash
		{
			var remarks=$("#txtWaRemarks_"+inc).val();
			var page_link = page_link + "&remarks="+remarks;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=370px,height=250px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var txt_remarks=this.contentDoc.getElementById("txt_remarks").value;

				if (txt_remarks!="" || 1==1)
				{
					$("#txtWaRemarks_"+inc).val( txt_remarks );
				}
			}
		}
		if(type==5) // for Print
		{
			var remarks=$("#txtPrRemarks_"+inc).val();
			var page_link = page_link + "&remarks="+remarks;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=370px,height=250px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var txt_remarks=this.contentDoc.getElementById("txt_remarks").value;

				if (txt_remarks!="" || 1==1)
				{
					$("#txtPrRemarks_"+inc).val( txt_remarks );
				}
			}
		}
		if(type==6) // for emb
		{
			var remarks=$("#txtReRemarks_"+inc).val();
			var page_link = page_link + "&remarks="+remarks;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=370px,height=250px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var txt_remarks=this.contentDoc.getElementById("txt_remarks").value;

				if (txt_remarks!="" || 1==1)
				{
					$("#txtReRemarks_"+inc).val( txt_remarks );
				}
			}
		}
	}

	function openmypage_color_size(page_link,title,type,inc)
	{
		var sampleStage=$("#cbo_sample_stage").val();
		var cbo_buyer_name=$("#cbo_buyer_name").val();
		if(sampleStage==1)
		{
			/* var style_db_id=document.getElementById('txt_quotation_id').value;
			if(type==1) // for sample details color
			{
				var page_link = page_link + "&style_db_id="+style_db_id;
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=400px,center=1,resize=1,scrolling=0','../');
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0];
					var col_name=this.contentDoc.getElementById("txt_color_name").value;//mst id
					//var color_id=this.contentDoc.getElementById("txt_color_id").value;//mst id

					if (col_name!="")
					{
						$("#txtColor_"+inc).val('');
						$("#txtColor_"+inc).val( col_name );
						//$("#hiddenColor_id"+inc).val( color_id );
						freeze_window(5);
						release_freezing();
					}
				}
			} */

			var style_db_id=document.getElementById('txt_quotation_id').value;
			
			if(type==1) // for sample details color
			{
				var page_link = page_link + "&style_db_id="+style_db_id+ "&cbo_buyer_name="+cbo_buyer_name+ "&sampleStage="+sampleStage;
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=290px,height=400px,center=1,resize=1,scrolling=0','../');
				emailwindow.onclose=function()
				{
					freeze_window(5);
					var colordata=this.contentDoc.getElementById("color_data").value;
					var row_count=inc;
					var colordata=colordata.split("__");
					var a=0; var n=0;
					for(var b=1; b<=colordata.length; b++)
					{
						var exdata="";
						var exdata=colordata[a].split("***");
						if(a==0)
						{
							document.getElementById('hiddenColorid_'+inc).value=exdata[0];
							document.getElementById('txtColor_'+inc).value=exdata[1];
						}
						else
						{
							add_break_down_tr_from_color(inc,inc-1);							
							row_count++;
							document.getElementById('hiddenColorid_'+row_count).value=exdata[0];
							document.getElementById('txtColor_'+row_count).value=exdata[1];
							document.getElementById('cboGarmentItem_'+row_count).value=$('#cboGarmentItem_'+inc).val();
							document.getElementById('cboSampleName_'+row_count).value=$('#cboSampleName_'+inc).val();
							inc++;
						}
						a++;
					}
					release_freezing();
				}
			}

			if(type==2) // for required fabric color
			{
				var page_link = page_link + "&style_db_id="+style_db_id+ "&cbo_buyer_name="+cbo_buyer_name+ "&sampleStage="+sampleStage;
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=400px,center=1,resize=1,scrolling=0','../');
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0];
					var col_name=this.contentDoc.getElementById("color_data").value;//mst id

					/* if (col_name!="")
					{
						$("#txtRfColor_"+inc).val( col_name );
						freeze_window(5);
						release_freezing();
					} */
					freeze_window(5);
					var colordata=this.contentDoc.getElementById("color_data").value;
					var row_count=inc;
					var colordata=colordata.split("__");
					var a=0; var n=0;
					for(var b=1; b<=colordata.length; b++)
					{
						var exdata="";
						var exdata=colordata[a].split("***");
						if(a==0)
						{
							document.getElementById('hiddenColorid_'+inc).value=exdata[0];
							document.getElementById('txtColor_'+inc).value=exdata[1];
						}
						else
						{
							add_break_down_tr_from_color(inc,inc-1);							
							row_count++;
							document.getElementById('hiddenColorid_'+row_count).value=exdata[0];
							document.getElementById('txtColor_'+row_count).value=exdata[1];
							document.getElementById('cboGarmentItem_'+row_count).value=$('#cboGarmentItem_'+inc).val();
							document.getElementById('cboSampleName_'+row_count).value=$('#cboSampleName_'+inc).val();
							inc++;
						}
						a++;
					}
					release_freezing();
				}
			}
		}
		else
		{
			//var style_db_id=document.getElementById('txt_quotation_id').value;
			var style_db_id="";
			var page_link = page_link + "&style_db_id="+style_db_id+ "&cbo_buyer_name="+cbo_buyer_name+ "&sampleStage="+sampleStage;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=400px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var col_name=this.contentDoc.getElementById("color_data").value;//mst id

				freeze_window(5);
				var colordata=this.contentDoc.getElementById("color_data").value;
				var row_count=inc;
				var colordata=colordata.split("__");
				var a=0; var n=0;
				for(var b=1; b<=colordata.length; b++)
				{
					var exdata="";
					var exdata=colordata[a].split("***");
					if(a==0)
					{
						document.getElementById('hiddenColorid_'+inc).value=exdata[0];
						document.getElementById('txtColor_'+inc).value=exdata[1];
					}
					else
					{
						add_break_down_tr_from_color(inc,inc-1);							
						row_count++;
						document.getElementById('hiddenColorid_'+row_count).value=exdata[0];
						document.getElementById('txtColor_'+row_count).value=exdata[1];
						document.getElementById('cboGarmentItem_'+row_count).value=$('#cboGarmentItem_'+inc).val();
						document.getElementById('cboSampleName_'+row_count).value=$('#cboSampleName_'+inc).val();
						inc++;
					}
					a++;
				}
				release_freezing();
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=250px,center=1,resize=1,scrolling=0','../');
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
				$('#txtRfColorAllData_'+inc).val(receive_all_data);
				$('#txtRfReqQty_'+inc).val(total_qnty);
				$('#txtProcessLoss_'+inc).val(total_loss);
				$('#txtGrayFabric_'+inc).val(total_grey);
				$("#txtRfColor_"+inc).val(color);
			}
		}
	}

	function fnc_sample_name_change()
	{
		var sample_mst_id=$('#update_id').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		if(!sample_mst_id)
		{
			alert("please save master part!");
			return;
		}
		var page_link="requires/sample_requisition_with_booking_controller.php?action=sample_name_change_popup"

		page_link = page_link + "&sample_mst_id="+sample_mst_id+ "&cbo_buyer_name="+cbo_buyer_name;
		var title="sample name change";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=250px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var retVal = confirm("Sucessfully Updated.You have to load page for update info.Do you want to Refresh?");
		   if( retVal == true )
		   {
			  window.location.reload();
			  return true;
		   }
		   else
		   {
			   return true;
		   }
		}
	}

	function openmypage_requisition()
	{
		hide_left_menu("Button1");
		var cbo_company_name=$('#cbo_company_name').val();
	
		var title = 'Requisition ID Search';
		var page_link = 'requires/sample_requisition_with_booking_controller.php?&action=requisition_id_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&cbo_company_name='+cbo_company_name, title, 'width=1330px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_tbl_id=this.contentDoc.getElementById("selected_job").value;//mst id

			if (mst_tbl_id!="")
			{
				freeze_window(5);
				$("#txt_booking_no").val('');
				get_php_form_data(mst_tbl_id, "populate_data_from_requisition_search_popup", "requires/sample_requisition_with_booking_controller" );
				var booking_no=$("#txt_booking_no").val();

				$("#cbo_company_name").attr('disabled','disabled');
				$("#cbo_buyer_name").attr('disabled','disabled');
				//set_button_status(1, permission, 'fnc_sample_requisition_mst_info',1,0);
				release_freezing();
				$('#content_sample_details').hide();
				$('#content_required_fabric').hide();
				$('#content_required_accessories').hide();
				$('#content_required_wash').hide();
				$('#content_required_print').hide();
				$('#content_required_embellishment').hide();
				$("#cbo_sample_stage").attr('disabled','disabled');
				get_php_form_data( booking_no+'__'+$("#cbo_sample_stage").val(), "populate_booking_data_from_search_popup", "requires/sample_requisition_with_booking_controller" );
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
			$('#content_required_wash').hide();
			$('#content_required_print').hide();
 		}
 		else if(type=='rf')
 		{
 			$('#content_sample_details').hide();
 			$('#content_required_accessories').hide();
 			$('#content_required_embellishment').hide();
			$('#content_required_wash').hide();
			$('#content_required_print').hide();
 		}
 		else if(type=='acc')
 		{
 			$('#content_sample_details').hide();
 			$('#content_required_fabric').hide();
 			$('#content_required_embellishment').hide();
			$('#content_required_wash').hide();
			$('#content_required_print').hide();
 		}
 		else if(type=='wash')
 		{
 			$('#content_sample_details').hide();
 			$('#content_required_fabric').hide();
 			$('#content_required_accessories').hide();
			$('#content_required_embellishment').hide();
			$('#content_required_print').hide();
 		}
		else if(type=='print')
 		{
 			$('#content_sample_details').hide();
 			$('#content_required_fabric').hide();
 			$('#content_required_accessories').hide();
			$('#content_required_embellishment').hide();
			$('#content_required_wash').hide();
 		}
		else if(type=='emb')
 		{
 			$('#content_sample_details').hide();
 			$('#content_required_fabric').hide();
 			$('#content_required_accessories').hide();
			$('#content_required_wash').hide();
			$('#content_required_print').hide();
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
 			var qty =return_global_ajax_value( data, 'populate_data_to_req_qty', '', 'requires/sample_requisition_with_booking_controller') ;
 			var qty=qty.trim();
 			var reqQty=(dznQty/12)*qty;
 			//alert(qty+' '+reqQty);
 			$('#txtRfReqQty_'+inc).val(reqQty.toFixed(4));
 		}

 		else if(type==2)
 		{
 			var sampleName=$('#cboRaSampleName_'+inc).val()*1;
 			var garmentItem=$('#cboRaGarmentItem_'+inc).val()*1;
 			var dznQty=$('#txtRaReqDzn_'+inc).val()*1;
 			var updateId=$('#update_id').val();
 			var data=sampleName+'___'+garmentItem+'___'+updateId+'___'+'0'+'___'+type;
 			var qty =return_global_ajax_value( data, 'populate_data_to_req_qty', '', 'requires/sample_requisition_with_booking_controller') ;
 			var qty=qty.trim();
 			var reqQty=(dznQty/12)*qty;
 			$('#txtRaReqQty_'+inc).val(reqQty.toFixed(4));
 		}
 	}
	
 	function load_uom_for_trims(inc,data)
 	{
 		var result = return_global_ajax_value( data, 'load_data_to_uom', '', 'requires/sample_requisition_with_booking_controller');
 		var res=trim(result);
 		$('#cboRaUom_'+inc).val(res);
 	}

 	function load_data_to_rfcolor(inc)
 	{
 		var sampleName=$('#cboRfSampleName_'+inc).val()*1;
 		var garmentItem=$('#cboRfGarmentItem_'+inc).val()*1;
 		var mainId=$('#update_id').val();
 		var data=sampleName+"_"+garmentItem+"_"+mainId;
 		var result = return_global_ajax_value( data, 'load_data_to_colorRF', '', 'requires/sample_requisition_with_booking_controller');
	 	//var res=trim(result).split("_");
	 	var reponse=trim(result).split('_');
	 	$('#txtRfColor_'+inc).val(reponse[1]);
		if(reponse[1])
		{
			$('#txtRfColorAllData_'+inc).val('');
		}
	 	//$('#txtRfColorAllData_'+inc).val(trim(result));
	}

	function fnc_show_acknowledge() {
     	show_list_view('', 'show_acknowledge', 'list_acknowledge', 'requires/sample_requisition_with_booking_controller', 'setFilterGrid(\'tbl_list_search\',-1);');
     }

	 function btn_load_acknowledge()
	 {
		 if($("#cbo_buyer_name").val()!=0)
		 {
			 load_drop_down( 'requires/sample_requisition_with_booking_controller', document.getElementById('cbo_buyer_name').value, 'load_drop_down_season_buyer', 'season_td');
	
			 load_drop_down( 'requires/sample_requisition_with_booking_controller', document.getElementById('cbo_buyer_name').value, 'load_drop_down_season_buyer', 'season_td');
		 }
		 
        var count =1;//trim(return_global_ajax_value("", 'btn_load_acknowledge', '', 'requires/sample_requisition_with_booking_controller'));
        if(count > 0){
            $("#list_acknowledge").html("<span id='btn_span' style='cursor: pointer; width: 60px !important; float: left;'><input id='show' onClick='fnc_show_acknowledge()' type='button' class='formbutton' value='&nbsp;&nbsp;show&nbsp;&nbsp;' style='background-color:#d9534f !important; background-image:none !important;border-color: #d43f3a;' title='Refusing Cause List'></span>");
        }else
        {
            $("#list_acknowledge").html("<span id='btn_span_disabled' style='cursor: pointer; width: 60px !important; float: left;'><input id='show' type='button' class='formbutton_disabled' value='&nbsp;&nbsp;show&nbsp;&nbsp;' style='background-color:#ccc !important; background-image:none !important;border-color: #ccc;' title='Refusing Cause List'></span>");
        }
        (function blink() {
        $('#btn_span').fadeOut(900).fadeIn(900, blink);
        })();
     }

     function check_exchange_rate()
     {
     	var cbo_currercy=$('#cbo_currency').val();
     	var booking_date = $('#txt_booking_date').val();
     	var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/sample_requisition_with_booking_controller');
     	var response=response.split("_");
     	$('#txt_exchange_rate').val(response[1]);

     }

     function openmypage_booking(page_link,title)
     {
     	var booking_no=$("#txt_booking_no").val();
     	var sample_stage=$("#cbo_sample_stage").val();
     	if(!booking_no)
     	{
     		alert("Save First");
     		return ;
     	}
     	if(sample_stage!=1)
     	{
     		page_link="requires/sample_requisition_booking_non_order_controller.php?action=fabric_booking_popup";
     	}
     	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=450px,center=1,resize=1,scrolling=0','../')
     	emailwindow.onclose=function()
     	{
     		var theform=this.contentDoc.forms[0];
     		var theemail=this.contentDoc.getElementById("selected_booking");
     		if (theemail.value!="")
     		{
     			freeze_window(5);
				$("#cbo_company_name").attr('disabled','disabled');
				$("#cbo_buyer_name").attr('disabled','disabled');
     			get_php_form_data( theemail.value, "populate_booking_data_from_search_popup", "requires/sample_requisition_with_booking_controller" );
     			//set_button_status(1, permission, 'fnc_fabric_booking',1);
     			release_freezing();
     		}
     	}
     }

     function calculate_requirement(i)
     {
      	var cbo_company_name= document.getElementById('cbo_company_name').value;
     	var cbo_fabric_natu= 2;
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
     	//document.getElementById('txtAmount_'+i).value=number_format_common((document.getElementById('txtRate_'+i).value)*1*WastageQty,5,0);
     }
	 
	function open_consumption_popupWash(page_link,title,i)
	{
		var update_id=document.getElementById('update_id').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var txtcolorBreakdown=document.getElementById('txtWacolorBreakdown_'+i).value;
		var txtReQty=document.getElementById('txtWaQty_'+i).value;
		var cboReSampleName=document.getElementById('cboWaSampleName_'+i).value;
		var cboReGarmentItem=document.getElementById('cboWaGarmentItem_'+i).value;
		var updateidRequiredEmbellishdtl=document.getElementById('updateidRequiredWaDtls_'+i).value;
		if(cboReSampleName==0 ){
			alert("Select Sample name")
		}
		else{
			var page_link=page_link+'&update_id='+update_id+'&cbo_company_name='+cbo_company_name+'&txtcolorBreakdown='+txtcolorBreakdown+'&txtReQty='+txtReQty+'&cboReSampleName='+cboReSampleName+'&cboReGarmentItem='+cboReGarmentItem+'&updateidRequiredEmbellishdtl='+updateidRequiredEmbellishdtl;//
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=450px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function(){
				var cons_break_down=this.contentDoc.getElementById("cons_break_down");
				var cons_req=this.contentDoc.getElementById("qty_sum");
				var rate=this.contentDoc.getElementById("rate_sum");
				var amount=this.contentDoc.getElementById("amount_sum");
				
				document.getElementById('txtWacolorBreakdown_'+i).value=cons_break_down.value;
				document.getElementById('txtWaQty_'+i).value=cons_req.value;
				document.getElementById('txtWaRate_'+i).value=rate.value;
				document.getElementById('txtWaAmount_'+i).value=amount.value;
			}
		}
	}
	
	function open_consumption_popupPrint(page_link,title,i)
	{
		var update_id=document.getElementById('update_id').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var txtcolorBreakdown=document.getElementById('txtPrcolorBreakdown_'+i).value;
		var txtReQty=document.getElementById('txtPrQty_'+i).value;
		var cboReSampleName=document.getElementById('cboPrSampleName_'+i).value;
		var cboReGarmentItem=document.getElementById('cboPrGarmentItem_'+i).value;
		var updateidRequiredEmbellishdtl=document.getElementById('updateidRequiredPrDtls_'+i).value;
		if(cboReSampleName==0 ){
			alert("Select Sample name")
		}
		else{
			var page_link=page_link+'&update_id='+update_id+'&cbo_company_name='+cbo_company_name+'&txtcolorBreakdown='+txtcolorBreakdown+'&txtReQty='+txtReQty+'&cboReSampleName='+cboReSampleName+'&cboReGarmentItem='+cboReGarmentItem+'&updateidRequiredEmbellishdtl='+updateidRequiredEmbellishdtl;//
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=450px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function(){
				var cons_break_down=this.contentDoc.getElementById("cons_break_down");
				var cons_req=this.contentDoc.getElementById("qty_sum");
				var rate=this.contentDoc.getElementById("rate_sum");
				var amount=this.contentDoc.getElementById("amount_sum");
				
				document.getElementById('txtPrcolorBreakdown_'+i).value=cons_break_down.value;
				document.getElementById('txtPrQty_'+i).value=cons_req.value;
				document.getElementById('txtPrRate_'+i).value=rate.value;
				document.getElementById('txtPrAmount_'+i).value=amount.value;
			}
		}
	}
	 
	function open_consumption_popup(page_link,title,i)
	{
		var update_id=document.getElementById('update_id').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var txtcolorBreakdown=document.getElementById('txtcolorBreakdown_'+i).value;
		var txtReQty=document.getElementById('txtReQty_'+i).value;
		var cboReSampleName=document.getElementById('cboReSampleName_'+i).value;
		var cboReGarmentItem=document.getElementById('cboReGarmentItem_'+i).value;
		var updateidRequiredEmbellishdtl=document.getElementById('updateidRequiredEmbellishdtl_'+i).value;
		if(cboReSampleName==0 ){
			alert("Select Sample name")
		}
		else{
			var page_link=page_link+'&update_id='+update_id+'&cbo_company_name='+cbo_company_name+'&txtcolorBreakdown='+txtcolorBreakdown+'&txtReQty='+txtReQty+'&cboReSampleName='+cboReSampleName+'&cboReGarmentItem='+cboReGarmentItem+'&updateidRequiredEmbellishdtl='+updateidRequiredEmbellishdtl;//
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=450px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function(){
				var cons_break_down=this.contentDoc.getElementById("cons_break_down");
				var cons_req=this.contentDoc.getElementById("qty_sum");
				var rate=this.contentDoc.getElementById("rate_sum");
				var amount=this.contentDoc.getElementById("amount_sum");
				
				document.getElementById('txtcolorBreakdown_'+i).value=cons_break_down.value;
				document.getElementById('txtReQty_'+i).value=cons_req.value;
				document.getElementById('txtReRate_'+i).value=rate.value;
				document.getElementById('txtReAmount_'+i).value=amount.value;
			}
		}
	}
	
	function fnc_fab_marchd()
	{
		var sample_marchand=$('#cbo_dealing_merchant').val();
		var cbo_team_leader_book=$('#cbo_team_leader_book').val();
		//alert(sample_marchand);
		
		$('#cbo_dealing_merchant_book').val(sample_marchand);
		$('#cbo_dealing_merchant_book').attr('disabled','disabled');
		//$('#cbo_team_leader_book').attr('disabled','disabled');
	}
	
	function fnc_marchd_chk(team_id)
	{
		var cbo_team_leader_book=$('#cbo_team_leader_book').val();
		var sample_marchand=$('#cbo_dealing_merchant').val();
		//alert(team_id+'='+cbo_team_leader_book);
		load_drop_down( 'requires/sample_requisition_with_booking_controller', cbo_team_leader_book, 'cbo_dealing_merchant_book', 'div_marchant' );
		$('#cbo_dealing_merchant_book').val(sample_marchand);
		//$('#cbo_dealing_merchant_book').attr('disabled','disabled');
		//$('#cbo_team_leader_book').attr('disabled','disabled');
	}
	
	
	//function sendMail()
	function call_print_button_for_mail()
	{
		//if(confirm("Do you want to send mail?")==false){return;}
		
		if (form_validation('txt_requisition_id','System Id')==false)
		{
			return;
		}
		  
		var sys_id=$('#txt_requisition_id').val();
		
		var data="sys_id="+sys_id;
 		freeze_window(operation);
		http.open("POST","../../auto_mail/woven/sample_requisition_with_booking_auto_mail.php",true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function fnc_btb_mst_reponse()
		{
			if(http.readyState == 4)
			{
				var reponse=trim(http.responseText);
				alert(reponse);
				release_freezing();
			}
		}
	}


	function calculate_amount(id){
	var final_req_qnty=document.getElementById('txtGrayFabric_'+id).value*1;
	var txt_rate=document.getElementById('txtRate_'+id).value;	
	var amount=number_format_common((final_req_qnty*txt_rate),5,0);
	document.getElementById('txtAmount_'+id).value=amount
 }
 
 
 	function openpopup_itemgroup(i)
	{
		var page_link="requires/sample_requisition_with_booking_controller.php?action=openpopup_itemgroup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Item Group Select', 'width=480px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			//var id=this.contentDoc.getElementById("gid");
			var itemdata=this.contentDoc.getElementById("itemdata").value;
			//alert(itemdata);
			var row_count=$('#tbl_required_accessories tbody tr').length;
			var itemdata=itemdata.split("__");
			var a=0; var n=0;
			for(var b=1; b<=itemdata.length; b++)
			{
				//alert(itemdata[a]);
				var exdata="";
				var exdata=itemdata[a].split("***");
				if(a==0)
				{
					document.getElementById('cboRaTrimsGroup_'+i).value=exdata[0];
					document.getElementById('cbogrouptext_'+i).value=exdata[1];
					document.getElementById('cboRaUom_'+i).value=exdata[2];
					$('#cbogrouptext_'+i).removeAttr("title").attr( 'title',exdata[1] );
					//set_trim_cons_uom(exdata[0],i);
				}
				else
				{
					add_ra_tr(row_count);
					n++;
					row_count++;
					document.getElementById('cboRaTrimsGroup_'+row_count).value=exdata[0];
					document.getElementById('cbogrouptext_'+row_count).value=exdata[1];
					document.getElementById('cboRaUom_'+row_count).value=exdata[2];
					$('#cbogrouptext_'+row_count).removeAttr("title").attr( 'title',exdata[1] );
					//set_trim_cons_uom(exdata[0],row_count);
				}
				a++;
			}
		}
	}
	
	function openmypage_template_name(title)
	{
		var update_id=$("#update_id").val();
		var buyer=$("#cbo_buyer_name").val();
		
		var row_count=$('#tbl_required_accessories tbody tr').length;
		if(row_count == 0){
			//$('#txt_trim_pre_cost').click();
			show_hide_content('required_accessories', ''); 
			fnc_load_tr(document.getElementById('update_id').value,3); 
			hide_others_section('acc');
		}
		var page_link='requires/sample_requisition_with_booking_controller.php?action=trims_cost_template_name_popup&company=' + document.getElementById('cbo_company_name').value + '&buyer_name=' + document.getElementById('cbo_buyer_name').value+ '&update_id=' + document.getElementById('update_id').value;
		if ( form_validation('cbo_company_name*cbo_buyer_name','Company Name*Buyer')==false )
		{
			return;
		}
		else
		{
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=400px,center=1,resize=0,scrolling=0', '')
			emailwindow.onclose = function()
			{
				var theform = this.contentDoc.forms[0];
				var select_template_data = this.contentDoc.getElementById('select_template_data').value;
				//alert(select_template_data);
				if(select_template_data != '')
				{
					load_template_data(select_template_data);
				}
			}
		}
	}
	
	
	function load_template_data(data)
	{
		var row_count=$('#tbl_required_accessories tr').length-1;
		
		var itemdata=data.split(",");
		//alert(row_count+'--'+itemdata.length)
		var a=0; var n=0;
		for(var b=1; b<=itemdata.length; b++)
		{
			var exdata="";
			var exdata=itemdata[a].split("***");
	
			if(row_count == 1 && document.getElementById('cboRaUom_1').value == 0)
			{
				document.getElementById('cboRaTrimsGroup_1').value=exdata[2];
				document.getElementById('cbogrouptext_1').value=exdata[0];
				document.getElementById('txtRaDescription_1').value=exdata[10];
				//document.getElementById('excessper_1').value=exdata[11];
				document.getElementById('txtRaBrandSupp_1').value=exdata[9];
				document.getElementById('hidnominasupplierid_1').value=exdata[8];
				document.getElementById('txtnominasupplier_1').value=exdata[13];
				document.getElementById('cboRaUom_1').value=exdata[3];
				document.getElementById('txtRaReqQty_1').value=exdata[12];
				//document.getElementById('totalcons_1').value=exdata[4];
				//document.getElementById('txttrimrate_1').value=exdata[5];
				//document.getElementById('txttrimamount_1').value=exdata[6];
				//alert("a")
				//calculate_trim_cost(1);
	
			}
			else if(row_count == 1 && document.getElementById('cboRaTrimsGroup_1').value == 42)
			{
				document.getElementById('cboRaTrimsGroup_1').value=exdata[2];
				document.getElementById('cbogrouptext_1').value=exdata[0];
				document.getElementById('txtRaDescription_1').value=exdata[10];
				//document.getElementById('excessper_1').value=exdata[11];
				document.getElementById('txtRaBrandSupp_1').value=exdata[9];
				document.getElementById('hidnominasupplierid_1').value=exdata[8];
				document.getElementById('txtnominasupplier_1').value=exdata[13];
				document.getElementById('cboRaUom_1').value=exdata[3];
				document.getElementById('txtRaReqQty_1').value='';
				//alert("b")
				//document.getElementById('txttrimrate_1').value='';
				//document.getElementById('txttrimamount_1').value='';
				//document.getElementById('cboapbrequired_1').value=exdata[7];
				//calculate_trim_cost(1);
	
			}
			else
			{
				
				if(b<=row_count)
				{
					row_count=b;
				}
				else
				{
					add_ra_tr(row_count);
					n++;
					row_count++;
				}
				
				document.getElementById('cboRaTrimsGroup_'+row_count).value=exdata[2];
				document.getElementById('cbogrouptext_'+row_count).value=exdata[0];
				document.getElementById('txtRaDescription_'+row_count).value=exdata[10];
				//document.getElementById('excessper_'+row_count).value=exdata[11];
				document.getElementById('txtRaBrandSupp_'+row_count).value=exdata[9];
				document.getElementById('hidnominasupplierid_'+row_count).value=exdata[8];
				document.getElementById('txtnominasupplier_'+row_count).value=exdata[13];
				
				document.getElementById('cboRaUom_'+row_count).value=exdata[3];
				document.getElementById('txtRaReqQty_'+row_count).value=exdata[12];
				//add_ra_tr(row_count);
				//row_count++;
				//alert("c")
				//document.getElementById('txttrimrate_'+row_count).value=exdata[5];
				//document.getElementById('txttrimamount_'+row_count).value=exdata[6];
				//document.getElementById('cboapbrequired_'+row_count).value=exdata[7];
				//calculate_trim_cost(row_count);
			}
			a++;
		}
	}
	
	function fnc_get_company_config(company_id)
	{
		get_php_form_data(company_id,'get_company_config','requires/sample_requisition_with_booking_controller' );
		location_select();
		
		/*var celid=mst_mandatory_field.split("*")
		//alert( celid.length+"="+mandatory_field+"="+celid)
		var a=0;
		for (var i = 1; i <= celid.length; i++)
		{
			var td=$('#'+celid[a]).val();
			//alert(td+'='+celid[a])
			$('#'+celid[a]).closest('td').prev().css('color', 'blue');
			a++;
		}*/
	}
	
	function location_select()
	{
		if($('#cbo_location_name option').length==2)
		{
			if($('#cbo_location_name option:first').val()==0)
			{
				$('#cbo_location_name').val($('#cbo_location_name option:last').val());
				//eval($('#cbo_location_name').attr('onchange')); 
			}
		}
		else if($('#cbo_location_name option').length==1)
		{
			$('#cbo_location_name').val($('#cbo_location_name option:last').val());
			//eval($('#cbo_location_name').attr('onchange'));
		}	
	}
	
	function fnc_get_buyer_config(buyer_id)
	{
		get_php_form_data($('#cbo_company_name').val()+'*'+buyer_id+'*'+1,'get_buyer_config','requires/sample_requisition_with_booking_controller' );
		
		//sub_dept_load(buyer_id,document.getElementById('cbo_product_department').value);
	}
</script>
</head>
<body onLoad="set_hotkey(); btn_load_acknowledge(); check_exchange_rate();">
	<div style="width:100%;">
		<?=load_freeze_divs ("../../",$permission);  ?>
		<form name="sample_requisition_1" id="sample_requisition_1">
			<fieldset style="width:1100px;">
				<legend>Sample Requisition</legend>
				<div style="width:780px; float:left;" align="center">
					<table cellpadding="1" cellspacing="2" width="780">
						<tr>
							<td colspan="3" align="right">Requisition Id</td>
							<td colspan="3" align="left"> <input type="text" name="txt_requisition_id" id="txt_requisition_id" class="text_boxes" style="width: 140px;margin-right: 38px;" placeholder="Requisition Id" readonly onDblClick="openmypage_requisition();" > </td>
						</tr>
						<tr>
							<td width="100" class="must_entry_caption">Sample Stage</td>
							<td width="160"><?=create_drop_down( "cbo_sample_stage", 150, $sample_stage, "", 1, "-- Select Stage --", $selected, "fnc_browse_style(this.value);", "", "" ); ?></td>
							<td width="100" class="must_entry_caption">Requisition Date</td>
							<td width="160"><input name="txt_requisition_date" id="txt_requisition_date" class="datepicker" type="text" value="<? echo date('d-m-Y')?>" style="width:140px;" /></td>
							<td width="100" class="must_entry_caption">Style Ref</td>
							<td>
                                <input name="txt_style_name" id="txt_style_name" class="text_boxes" type="text" value="" style="width:140px;" placeholder="Wr/Br" onDblClick="check_sample_stage();" readonly /> 
                                <input type="hidden" id="txt_quotation_id" name="txt_quotation_id" class="text_boxes" style="width:30px;">
                                <input type="hidden" id="txt_quotation_job_no" name="txt_quotation_job_no" class="text_boxes" style="width:30px;">
                                <input type="hidden" name="update_id" id="update_id" value="">
                                <input type="hidden" name="hidd_variable_data" id="hidd_variable_data" value="">
                            </td>
						</tr>
						<tr>
							<td class="must_entry_caption">Company Name</td>
							<td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id,comp.company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_get_company_config(this.value);" );/*load_drop_down( 'requires/sample_requisition_with_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/sample_requisition_with_booking_controller', this.value, 'load_drop_down_agent', 'agent_td' );load_drop_down( 'requires/sample_requisition_with_booking_controller', this.value, 'load_drop_down_location', 'location_td' );*/ ?></td>
							<td class="must_entry_caption">Location</td>
							<td id="location_td"><? echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 $location_credential_cond order by location_name",'id,location_name', 1, '--- Select Location ---', 0, "" ); ?></td>
							<td class="must_entry_caption">Buyer Name</td>
							<td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "" ); ?></td>
						</tr>
						<tr>
							<td class="must_entry_caption">Season</td>
							<td id="season_td"><? echo create_drop_down( "cbo_season_name", 150, $blank_array,"", 1, "-- Select Season --", $selected, "" ); ?></td>
                            <td>Season Year</td>
							<td><? echo create_drop_down( "cbo_season_year", 150, create_year_array(),"", 1, "-Season Year-", $selected, "" ); ?></td>
                            <td>Brand</td>
							<td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 150, $blank_array,"", 1, "-Brand-", $selected, "" ); ?></td>
						</tr>
						<tr>
                        	<td class="must_entry_caption">Product. Dept</td>
							<td><? echo create_drop_down( "cbo_product_department", 150,$product_dept ,'', 1, '--- Select Department ---', 0, ""  ); ?></td>
							<td class="must_entry_caption">Dealing Merchant </td>
							<td><? 
							 
							if($data_level_secured==1)//Limit Access user // ===Issue Id=21156 (2022 yr)======another-27809
							{
								$sqlTeam=sql_select("select b.id from lib_mkt_team_member_info a, lib_marketing_team b where a.team_id=b.id and a.data_level_security=1 and a.user_tag_id='$user_id' and a.status_active =1 and a.is_deleted=0");
								//$mktTeamId="";
								foreach($sqlTeam as $row){
									$mktTeamIdArr[$row[csf('id')]]=$row[csf('id')];
								}
								$mktTeamId=implode(",",$mktTeamIdArr);
								$mktTeamAccess="";
								if(count($mktTeamIdArr)>0) $mktTeamAccess=" and b.team_id in($mktTeamId)";//Dont hide Issue id ISD-20-31821
							}
							else //All Acces user 
							{
								$mktTeamAccess="";	
							}
							
							echo create_drop_down( "cbo_dealing_merchant", 150, "select b.id,b.team_member_name from lib_marketing_team a,lib_mkt_team_member_info b where a.id=b.team_id and  a.PROJECT_TYPE=2 and b.status_active =1 and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0 $mktTeamAccess order by b.team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "fnc_fab_marchd()" ); ?></td>
							<td>Agent Name</td>
							<td id="agent_td"><? echo create_drop_down( "cbo_agent", 150, $blank_array,"", 1, "-- Select Agent --", $selected, "" ); ?></td>
							
						</tr>
						<tr>
                        	<td>Buyer Ref</td>
							<td><input type="text" name="txt_buyer_ref" id="txt_buyer_ref" class="text_boxes" style="width:140px;"></td>
							<td> BH Merchant</td>
							<td><input class="text_boxes" type="text" style="width:140px;"  name="txt_bhmerchant" id="txt_bhmerchant"/></td>
							<td>Est. Ship Date</td>
							<td><input name="txt_est_ship_date" id="txt_est_ship_date" class="datepicker" type="text" style="width:140px;" /></td>
						</tr>
						<tr>
                        	<td>Material Deli.Date</td>
							<td><input name="txt_material_dlvry_date" id="txt_material_dlvry_date" class="datepicker" type="text" value="" style="width:140px;" /></td>
                            <td>Remarks/Desc.</td>
							<td colspan="4"><input name="txt_remarks" class="text_boxes" ID="txt_remarks" style="width:395px" maxlength="500" title="Maximum 500 Character"></td>
						</tr>
                        <tr>
                        	<td>Ready To App.</td>
							<td><? echo create_drop_down( "cbo_ready_to_approved", 150, $yes_no,"", 1, "-- Select--", 2, "","",""); ?></td>
							<td align="center"> <input type="button" id="image_button" class="image_uploader" style="width:90px" value="ADD FILE" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'sample_requisition_1', 2 ,1)" /></td>
							<td align="center"> <input type="button" id="image_button" class="image_uploader" style="width:120px" value="ADD IMAGE FRONT" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'samplereqfrontimage_1', 0 ,1)" /></td>
                            <td align="center"> <input type="button" id="image_button" class="image_uploader" style="width:100px" value="ADD IMAGE BACK" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'samplereqbackimage_1', 0 ,1)" /></td>
							<td>&nbsp;</td>
                        </tr>
						<tr>
							<td colspan="6" align="center" height="15">
								<span id="approvedMsg" style="color:red;font-size:22px;font-weight: bold;"></span>
							</td>
						</tr>
					</table>
				</div>
                <div style="width:5px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
                <div id="list_acknowledge" style="max-height:300px; width:290px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
			</fieldset>

			<fieldset style="width:1100px;">
				<legend>Sample Fabric Work Order</legend>
				<div style="width:770px; float:left;" align="center">
					<table cellpadding="1" cellspacing="2" width="760">
						<tr>
							<td width="100">Work Order No<input type="hidden" name="cbo_fabric_natu" id="cbo_fabric_natu" value="2"></td>
							<td width="160">
								<input class="text_boxes" type="text" style="width:140px" onDblClick="openmypage_booking('requires/sample_requisition_booking_with_order_controller.php?action=fabric_booking_popup','fabric Booking Search')" readonly placeholder="Display" name="txt_booking_no" id="txt_booking_no" disabled="" /></td>
							<td width="100" class="must_entry_caption">Booking Date</td>
							<td width="160"><input class="datepicker" type="text" style="width:140px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" disabled /></td>
							<td width="100">Style Desc.</td>
							<td><input class="text_boxes" type="text" style="width:140px" name="txt_style_desc" id="txt_style_desc" /></td>
						</tr>
						<tr>
							<td>Currency</td>
							<td><? echo create_drop_down( "cbo_currency", 150, $currency,"", 1, "-- Select --", 2, "",0 ); ?></td>
							<td>Exchange Rate</td>
							<td><input style="width:140px;" type="text" class="text_boxes_numeric"  name="txt_exchange_rate" id="txt_exchange_rate" readonly /></td>
							<td>Attention</td>
							<td colspan="3"><input class="text_boxes" type="text" style="width:140px;"  name="txt_attention" id="txt_attention"/></td>
						</tr>
						<tr>
							<td class="must_entry_caption">Pay Mode</td>
							<td><? echo create_drop_down( "cbo_pay_mode", 150, $pay_mode,"", 1, "-- Select Pay Mode --", 3, "","","1,2,3,5" ); ?></td>
							<td>Supplier Name </td>
							<td id="sup_td"><? echo create_drop_down( "cbo_supplier_name", 150, "select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.status_active =1 and a.is_deleted=0  and b.party_type in (select party_type from lib_supplier_party_type where a.id=b.supplier_id and b.party_type in(9,21) ) group by a.id,a.supplier_name order by a.supplier_name ","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 ); ?>
                            </td>
                            <td>Ready To App.</td>
                            <td><? echo create_drop_down( "cbo_ready_to_approved_book", 150, $yes_no,"", 1, "-- Select--", 2, "get_php_form_data( this.value+'_'+document.getElementById('txt_booking_no').value, 'check_dtls_part', 'requires/sample_requisition_with_booking_controller');","","" ); ?>
                                <input type="hidden" name="is_found_dtls_part" id="is_found_dtls_part" value="1"/>
                            </td>
						</tr>
						<tr>
							<td>Team Leader</td>
							<td><? echo create_drop_down( "cbo_team_leader_book", 150, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 and project_type=2 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "fnc_marchd_chk(this.value)" ); ?></td>
							<td>Dealing Merch.</td>
							<td id="div_marchant"><? echo create_drop_down( "cbo_dealing_merchant_book", 150, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1,"-- Select Team Member --", $selected, ""); ?></td>
							<td>Fabric Source</td>
							<td><? echo create_drop_down( "cbo_fabric_source", 150, $fabric_source,"", 1, "-- Select --", "","enable_disable(this.value);fabic_srce_con_fnc()", "", "1,2,3,4,5"); ?></td>
						</tr>
						<tr>
							<td>Source</td>
							<td><? echo create_drop_down( "cbo_sources", 150, $source,"", 1, "-- Select --", "","", "", ""); ?></td>
							<td>Body/Wash Color</td>
							<td><input style="width:140px;" type="text" class="text_boxes" name="txt_bodywashcolor" id="txt_bodywashcolor" readonly placeholder="Display" /></td>
							<td>Revise</td>
							<td><input class="text_boxes" type="text" style="width:140px;"  name="txt_revise_no" id="txt_revise_no"/></td>
						</tr>
						<tr>
							<td>Buyer Req. No</td>
							<td><input class="text_boxes" type="text" style="width:140px;"  name="txt_buyer_req_no" id="txt_buyer_req_no"/></td>
							<td colspan="2"><input type="button" class="image_uploader" style="width:192px" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('txt_booking_no').value,'', 'sample_booking_non', 0 ,1)"></td>
							<td colspan="2"><input type="button" id="set_button" class="image_uploader" style="width:150px;" value="Terms & Condition/Notes" onClick="open_terms_condition_popup('requires/sample_requisition_with_booking_controller.php?action=terms_condition_popup','Terms Condition')" /></td>
						</tr>
						<tr>
							<td colspan="6" height="5">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="6" valign="bottom" align="center" class="button_container">
								<? echo load_submit_buttons( $permission, "fnc_sample_requisition_mst_info", 0,0,"button_status(1);",1); ?>
                                &nbsp;<input type="button" id="report" class="formbutton" value="Print" onClick="fnc_sample_requisition_mst_info(4);" style="width:60px;  display:none" />&nbsp;
                                &nbsp;<input type="button" id="report1" class="formbutton" value="Print1" onClick="fnc_sample_requisition_mst_info(5);" style="width:60px" />&nbsp;
							
                                <? echo create_drop_down( "cbo_template_id", 90, $report_template_list,'', 0, '', 0, ""); ?>
                                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                <input type="button" id="copy_btn" class="formbutton" value="Copy" onClick="copy_requisition(5);" style="width:50px;" />
                                
                                <input class="formbutton" type="button" onClick="fnSendMail('../../', 'txt_requisition_id', 1, 1, '')" value="Mail Send" style="width:80px;">
                                <br>
							</td>
						</tr>
					</table>
				</div>
                <div style="width:5px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
                <div id="list_acknowledge" style="max-height:300px; width:290px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
			</fieldset>
		</form>
        <h3 align="left" class="accordion_h" onClick="show_hide_content('sample_details', ''); fnc_load_tr(document.getElementById('update_id').value,1); hide_others_section('sd');" style="width:1100px;"> +Sample Details </h3>
        <div id="content_sample_details" style="display:none;">
			<form name="sample_details_1" id="sample_details_1">
				<fieldset style="width:1100px;" id="sample_dtls">
					<table width="100%" cellpadding="0" cellspacing="2" align="center" style="">
						<tr>
							<td align="center" valign="top" id="po_list_views">
								<legend>Sample details&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="chkBoxSample" id="chkBoxSample"></legend>
								<table cellpadding="0" cellspacing="0" width="1170" class="rpt_table" border="1" rules="all" id="tbl_sample_details">
									<thead>
										<th width="100" class="must_entry_caption">Sample Name </th>
										<th width="100" class="must_entry_caption">Garment Item</th>
										<th width="50">SMV</th>
										<th width="60">Article No</th>
										<th width="80" class="must_entry_caption">Color</th>
										<th width="70" class="must_entry_caption">Sample Req Qty</th>
										<th width="70" class="must_entry_caption">Submn. Qty</th>
										<th width="65">Delv. Start Date</th>
										<th width="65">Delv. End Date</th>
										<th width="65">Buyer Sub. Date</th>
										<th width="70">Charge/ Unit</th>
										<th width="60">Currency</th>
										<th width="70">Image</th>
										<th width="70">M-Chart No</th>
										<th width="70">Comments</th>
										<th>&nbsp;</th>
									</thead>
									<tbody id="sample_details_container">
										<tr id="tr_1" style="height:10px;" class="general">
											<td id="sample_td"><?=create_drop_down( "cboSampleName_1", 100, $blank_array,"", 1, "-Sample-", $selected, ""); ?></td>
											<td id="item_id_1"><?=create_drop_down( "cboGarmentItem_1", 100, get_garments_item_array(3),"", 1, "-Item-", 0, ""); ?></td>
											<td id="smv_1">
												<input style="width:40px;" type="text" class="text_boxes_numeric"  name="txtSmv_1" id="txtSmv_1"/>
												<input type="hidden" id="updateidsampledtl_1" name="updateidsampledtl_1" class="text_boxes" style="width:20px" />
                                                <input type="hidden" id="txtDeltedIdSd" name="txtDeltedIdSd"  class="text_boxes" style="width:20px" value="" />
											</td>
											<td id="article_1"><input style="width:50px;" type="text" class="text_boxes"  name="txtArticle_1" id="txtArticle_1" placeholder="Write" /></td>
											<td id="color_1">
												<input style="width:70px;" type="text" class="text_boxes"  name="txtColor_1" id="txtColor_1" placeholder="Write/Browse" onDblClick="openmypage_color_size('requires/sample_requisition_with_booking_controller.php?action=color_popup','Color Search','1','1');"/>
                                                 <input type="hidden" id="hiddenColorid_1" name="hiddenColorid_1"  class="text_boxes" style="width:20px"  />
											</td>
											<td id="sample_prod_qty_1">
												<input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtSampleProdQty_1" id="txtSampleProdQty_1"  readonly placeholder="Browse" onDblClick="openmypage_sizeinfo('requires/sample_requisition_with_booking_controller.php?action=sizeinfo_popup','Size Search','1')" /><input type="hidden" class="text_boxes"  name="txtAllData_1" id="txtAllData_1"/>
											</td>
											<td id="submission_qty_1"><input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtSubmissionQty_1" id="txtSubmissionQty_1" placeholder="Display" readonly /></td>
											<td id="delv_start_date_1"><input style="width:55px;" type="text" class="datepicker" placeholder="Date" autocomplete="off" name="txtDelvStartDate_1" id="txtDelvStartDate_1" /></td>
											<td id="delv_end_date_1"><input style="width:55px;" type="text" class="datepicker" placeholder="Date" autocomplete="off" name="txtDelvEndDate_1" id="txtDelvEndDate_1" /></td>

											<td id="buyer_sub_date_1"><input style="width:55px;" type="text" class="datepicker" placeholder="Date" autocomplete="off" name="txtBuyerSubDate_1" id="txtBuyerSubDate_1" /></td>
											<td id="charge_unit_1"><input style="width:55px;" type="text" class="text_boxes_numeric"  name="txtChargeUnit_1" id="txtChargeUnit_1" placeholder="Write"/></td>

											<td id="currency_1"><?=create_drop_down( "cboCurrency_1", 60, $currency, "","","",2, "", "", "" ); ?></td>
											<td id="image_1"><input type="button" class="image_uploader" name="txtFile_1" id="txtFile_1" onClick="file_uploader ( '../../', document.getElementById('updateidsampledtl_1').value,'', 'sample_details_1', 0 ,1);" style="" value="ADD IMAGE"></td>

											<td id="mchart_1"><input style="width:60px;" type="text" class="text_boxes"  name="txtMchart_1" id="txtMchart_1" placeholder="Write"/></td>

											<td id="remarks_1"><input style="width:60px;" type="text" class="text_boxes"  name="txtRemarks_1" id="txtRemarks_1" placeholder="Click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','1','1');"/></td>

											<td width="70">
												<input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1);" />
												<input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
											</td>
										</tr>
									</tbody>
								</table>
								<table style="margin-top: 5px;">
									<tr>
										<td colspan="16" height="40" valign="bottom" align="center" class="">
											<?=load_submit_buttons( $permission, "fnc_sample_details_info", 0,0 ,"button_status(2)",2); ?>
											<input type="hidden" name="hidden_size_id" id="hidden_size_id" value="">
											<input type="hidden" name="hidden_bhqty" id="hidden_bhqty" value="">
											<input type="hidden" name="hidden_plnqnty" id="hidden_plnqnty" value="">
											<input type="hidden" name="hidden_dyqnty" id="hidden_dyqnty" value="">
											<input type="hidden" name="hidden_testqnty" id="hidden_testqnty" value="">
											<input type="hidden" name="hidden_selfqnty" id="hidden_selfqnty" value="">
											<input type="hidden" name="hidden_totalqnty" id="hidden_totalqnty" value="">
											<input type="hidden" name="hidden_tbl_size_id" id="hidden_tbl_size_id" value="">
											<input type="button" onClick="fnc_sample_name_change();" class="formbutton" value="Sample Name Change" name="">
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>

		<h3 align="left" class="accordion_h" onClick="show_hide_content('required_fabric', ''); fnc_load_tr(document.getElementById('update_id').value,2); hide_others_section('rf');" style="width:1625px;"> +Required Fabric </h3>
		<div id="content_required_fabric" style="display:none;">
			<form name="required_fabric_1" id="required_fabric_1">
				<fieldset style="width:1725px;" id="required_fab_dtls">
					<table width="100%" cellpadding="0" cellspacing="2" align="center" >
						<tr>
							<td align="center" valign="top" id="po_list_view">
								<legend>Required Fabric </legend>
								<table cellpadding="0" cellspacing="0" width="1725" class="rpt_table" border="1" rules="all" id="tbl_required_fabric">
									<thead>
										<th width="95" class="must_entry_caption">Sample Name </th>
										<th width="95" class="must_entry_caption">Garment Item</th>
										<th width="95" class="must_entry_caption">Body Part</th>
										<th width="95" class="must_entry_caption">Body Part Type</th>
										<th width="95" class="must_entry_caption">Fabric Nature</th>
										<th width="70" class="must_entry_caption">Fabric Desc.</th>
										<th width="50" class="must_entry_caption">Fabric Weight</th>
                                        <th width="70">F.Weight Type</th>
										<th width="50">Full Width</th>
                                        <th width="50">Cuttable Width</th>
										<th width="70" class="must_entry_caption">Color</th>
										<th width="80" class="must_entry_caption">Color Type</th>
										<th width="80">Width Type</th>
										<th width="50" class="must_entry_caption">UOM</th>
										<th width="60" class="must_entry_caption">Finish Req. Qty.</th>
										<th width="50">Process Loss %</th>
										<th width="60">Final Req. Qty</th>
										<th width="60">Rate</th>
										<th width="60">Amount</th>
										<th width="60">Fabric Del. Date</th>
										<th width="70">Fabric Source</th>
										<th width="50">Image</th>
										<th width="60">Remarks</th>
										<th>&nbsp;</th>
									</thead>

									<tbody id="required_fabric_container">
										<tr id="tr_1" style="height:10px;" class="general">
											<td id="rfSampleId_1"><?=create_drop_down( "cboRfSampleName_1", 95, $blank_array,"", 1,"-Sample-", $selected,""); ?></td>
											<td id="rfItemId_1"><?=create_drop_down( "cboRfGarmentItem_1", 95, $blank_array,"", 1, "-Gmts. Item-", 0, ""); ?></td>
											<td id="rf_body_part_1"><?=create_drop_down( "cboRfBodyPart_1", 95, $body_part,"", 1, "Select Body Part", 0, "load_data_to_rfcolor('1');"); ?></td>
											<td id="rf_body_part_type_1"><?=create_drop_down( "cboRfBodyPartType_1", 95, $body_part_type,"", 1, "Select Body Part Type", 0, ""); ?></td>
											<td id="rf_fabric_nature_1"><?=create_drop_down( "cboRfFabricNature_1", 95, $item_category,"", 0, "-Fabric Nature-", 3, "","","2,3"); ?></td>
											<td id="rf_fabric_description_1">
												<input style="width:58px;" type="text" class="text_boxes" name="txtRfFabricDescription_1" id="txtRfFabricDescription_1" placeholder="Browse" onDblClick="open_fabric_description_popup(1);" readonly/>
												<input type="hidden" name="libyarncountdeterminationid_1" id="libyarncountdeterminationid_1" class="text_boxes" style="width:10px" >
											</td>
											<td id="rf_gsm_1">
                                                <input style="width:38px;" type="text" class="text_boxes_numeric"  name="txtRfGsm_1" id="txtRfGsm_1" placeholder="Display/Write"/>
                                                <input type="hidden" id="updateidRequiredDtl_1" name="updateidRequiredDtl_1"  value=""  class="text_boxes" />
                                                <input type="hidden" id="txtDeltedIdRf" name="txtDeltedIdRf"  class="text_boxes" style="width:20px" value="" />
                                            </td>
                                            <td id="weighttype_1"><?=create_drop_down( "cboweighttype_1", 70, $fabric_weight_type,"", 1, "-Select-", '', "",$disabled,"" ); ?></td>
											<td id="rf_dia_1"><input style="width:38px;" type="text" placeholder="Write" class="text_boxes"  name="txtRfDia_1" id="txtRfDia_1"/></td>
                                            <td id="cuttablewidth_1"><input style="width:38px;" type="text" placeholder="Write" class="text_boxes"  name="txtcuttablewidth_1" id="txtcuttablewidth_1"/></td>
                                            <td id="rf_color_1">
                                            	<input style="width:58px;" type="text" class="text_boxes"  name="txtRfColor_1" id="txtRfColor_1" placeholder="Write/Browse" onDblClick="openmypage_rf_color('requires/sample_requisition_with_booking_controller.php?action=color_popup_rf','Color Search','1');" readonly />
                                            	<input type="hidden" name="txtRfColorAllData_1" id="txtRfColorAllData_1" value=""  class="text_boxes">
                                            </td>
                                            <td id="rf_color_type_1"><?=create_drop_down( "cboRfColorType_1", 80, $color_type,"", 1, "-Color Type-", 1, "","","1,3,4,5,7,20,25,26,28,39"); ?></td>
                                            <td id="rf_width_dia_1"><?=create_drop_down( "cboRfWidthDia_1", 80, $fabric_typee,"", 1, "-Width Type-", 1, ""); ?></td>
                                            <td id="rf_uom_1"><?=create_drop_down( "cboRfUom_1", 50, $unit_of_measurement,'', '', "",27,"","","12,27,1,23" ); ?></td>
                                            
                                            <td id="rf_req_qty_1">
                                            	<input style="width:48px;" type="text" class="text_boxes_numeric"  name="txtRfReqQty_1" id="txtRfReqQty_1" placeholder="" readonly />
                                                <input type="hidden" class="text_boxes"  name="txtMemoryDataRf_1" id="txtMemoryDataRf_1" />
                                            </td>
                                            <td id="rf_reqs_qty_1"><input style="width:38px;" type="text" class="text_boxes_numeric"  name="txtProcessLoss_1" id="txtProcessLoss_1" placeholder=""  onChange="calculate_requirement('1');" /></td>
                                            <td id="rf_grey_qnty_1"><input style="width:48px;" type="text" class="text_boxes_numeric"  name="txtGrayFabric_1" id="txtGrayFabric_1" placeholder="" readonly /></td>
											<td id="rf_rate_1"><input style="width:48px;" type="text" class="text_boxes_numeric"  name="txtRate_1" id="txtRate_1" placeholder="" onChange="calculate_amount(1)"/></td>
											<td id="rf_amount_1"><input style="width:48px;" type="text" class="text_boxes_numeric"  name="txtAmount_1" id="txtAmount_1" placeholder=""  /></td>
                                            
                                            <td id="deliveryrfDateid_1"><input style="width:48px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="fabricDelvDate_1" id="fabricDelvDate_1" /></td>
                                            <td id="rf_fab_1"><?=create_drop_down( "cboRfFabricSource_1", 70, $fabric_source,'', 1, "-Select-",0,"","","2,3,4,5" ); ?></td>
                                            <td id="rf_image_1"><input type="button" class="image_uploader" name="txtRfFile_1" id="txtRfFile_1" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredDtl_1').value,'', 'required_fabric_1', 0 ,1)" value="IMAGE"></td>
                                            <td id="rf_remarks_1"><input style="width:48px;" type="text" class="text_boxes"  name="txtRfRemarks_1" id="txtRfRemarks_1" placeholder="click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','2','1');"  /></td>
                                            
                                            <td>
                                                <input type="button" id="increaserf_1" name="increaserf_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_rf_tr(1);" />
                                                <input type="button" id="decreaserf_1" name="decreaserf_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_rf_deleteRow(1);" />
                                            </td>
                         				</tr>
                     				</tbody>
                 				</table>
                                <table>
                                    <tr>
                                        <td colspan="20" valign="bottom" align="center" class="">
											<?=load_submit_buttons( $permission, "fnc_required_fabric_details_info", 0,0 ,"button_status(3)",3); ?>
                                            <input type="button" value="Generate Booking" class="formbutton" name="generate_booking" id="generate_booking" onClick="fnc_generate_booking();">
											&nbsp;<input type="button" id="report3" class="formbutton" value="Fabric Booking" onClick="fnc_sample_requisition_mst_info(6);" style="width:80px" />&nbsp;
                                            <input type="button" value="YarnDetails" class="formbutton" name="generate_booking" style="display:none" id="generate_booking" onClick="fnc_yarn_dtls(); ">
                                        </td>
                                    </tr>
                                </table>
             				</td>
         				</tr>
    				</table>
 				</fieldset>
			</form>
		</div>
        
        <h3 align="left" class="accordion_h" onClick="show_hide_content('required_accessories', ''); fnc_load_tr(document.getElementById('update_id').value,3); hide_others_section('acc');" style="width: 1200px;"> +Required Accessories </h3>
        <div id="content_required_accessories" style="display:none;">
            <form name="required_accessories_1" id="required_accessories_1">
                <fieldset style="width: 1200px;" id="required_accessories_dtls">
                    <table width="100%" cellpadding="0" cellspacing="2" align="center" style="">
                        <tr>
                            <td align="center" valign="top" id="po_list_view">
                                <legend>Required Accessories</legend>
                                <table cellpadding="0" cellspacing="0" width="1200" class="rpt_table" border="1" rules="all" id="tbl_required_accessories">
                                    <thead>
                                        <th width="100" class="must_entry_caption">Sample Name </th>
                                        <th width="100" class="must_entry_caption">Garment Item</th>
                                        <th width="100" class="must_entry_caption">Trims Group&nbsp <span id="load_temp" style="float:right; width:10px; font-weight: bold;background-color: white;color:black; border: 1px white solid; cursor: pointer;" onClick="openmypage_template_name('Template Search')">...</span></th>
                                        <th width="130" class="must_entry_caption">Description</th>
                                        <th width="100">N.Supplier</th>
                                        <th width="90">Brand/ Supp. Ref</th>
                                        <th width="60" class="must_entry_caption">UOM</th>
                                        <th width="60">Req/Dzn</th>
                                        <th width="70" class="must_entry_caption">Req. Qty.</th>
                                        <th width="65">Acc. Del. Date</th>
                                        <th width="80">Acc. Source</th>
                                        <th width="70">Remarks</th>
                                        <th width="70">Image</th>
                                        <th>&nbsp;</th>
                                    </thead>
                                    <tbody id="required_accessories_container">
                                        <tr id="tr_1" style="height:10px;"  class="general">
                                            <td id="raSampleId_1"><?=create_drop_down( "cboRaSampleName_1", 100, $blank_array,"", 1, "-Sample-", $selected, ""); ?></td>
                                            <td id="raItemId_1"><?=create_drop_down( "cboRaGarmentItem_1", 100, $blank_array,"", 1, "-Item-", 0, ""); ?> </td>
                                            <td id="ra_trims_group_1">
											<input placeholder="Browse" title="" readonly type="text" id="cbogrouptext_1" name="cbogrouptext_1" class="text_boxes" style="width:88px" value="" onDblClick="openpopup_itemgroup(1);"/>
	                    					<input type="hidden" id="cboRaTrimsGroup_1" name="cboRaTrimsGroup_1" class="text_boxes" style="width:50px" value=""/>
											<? //=create_drop_down( "cboRaTrimsGroup_1", 100, "select item_name,id from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name","id,item_name", 1, "-Trims Group-", 0, "load_uom_for_trims('1',this.value);"); ?></td>
                                            <td id="ra_description_1">
                                                <input style="width:120px;" type="text" class="text_boxes" name="txtRaDescription_1" id="txtRaDescription_1" placeholder="Write"/>
                                                <input type="hidden" id="updateidAccessoriesDtl_1" name="updateidAccessoriesDtl_1" class="text_boxes" style="width:20px" value="" />
                                            </td>
                                            <td>
												<input readonly type="text" id="txtnominasupplier_1" name="txtnominasupplier_1" class="text_boxes" placeholder="Browse" style="width:90px" onDblClick="fncopenpopup_trimsupplier(1);"/>
                            					<input type="hidden" id="hidnominasupplierid_1" name="hidnominasupplierid_1" class="text_boxes" style="width:50px"  />
                                            </td>
                                            
                                            
                                            <td id="ra_brand_supp_1">
                                                <input type="hidden" id="txtDeltedIdRa" name="txtDeltedIdRa"  class="text_boxes" style="width:20px" value="" />
                                                <input style="width:80px;" type="text" class="text_boxes"  name="txtRaBrandSupp_1" id="txtRaBrandSupp_1" placeholder="Write"  />
                                            </td>
                                            <td id="ra_uom_1"><?=create_drop_down( "cboRaUom_1", 60, $unit_of_measurement,'', '', "","","","","" ); ?></td>
                                            <td id="ra_req_dzn_1"><input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtRaReqDzn_1" id="txtRaReqDzn_1" placeholder="Write" /></td>
                                            <td id="ra_req_qty_1"><input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtRaReqQty_1" id="txtRaReqQty_1" placeholder="Write" /></td>
                                            <td id="deliveryraDateid_1">
                                                <input type="hidden" class="text_boxes"  name="txtMemoryDataRa_1" id="txtMemoryDataRa_1" />
                                                <input style="width:55px;" type="text" class="datepicker" placeholder="Date" autocomplete="off" name="accDate_1" id="accDate_1" />
                                            </td>
                                            <td id="ra_fab_1"><?=create_drop_down( "cboRaFabricSource_1", 80, $fabric_source,'', '', "",'',"","","2,3,4,5" ); ?> </td>
                                            <td id="ra_remarks_1"><input style="width:60px;" type="text" class="text_boxes"  name="txtRaRemarks_1" id="txtRaRemarks_1" placeholder="click"  readonly onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','3','1');" />
                                            </td>
                                            <td id="ra_image_1"><input type="button" class="image_uploader" name="txtRaFile_1" id="txtRaFile_1" onClick="file_uploader ( '../../', document.getElementById('updateidAccessoriesDtl_1').value,'', 'required_accessories_1', 0 ,1)" style="width:70px;" value="ADD IMAGE"></td>
                                            <td>
                                            <input type="button" id="increasera_1" name="increasera_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_ra_tr(1);" />
                                            <input type="button" id="decreasera_1" name="decreasera_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_ra_deleteRow(1);" />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table>
                                    <tr>
                                        <td colspan="13" valign="bottom" align="center"><?=load_submit_buttons( $permission, "fnc_required_accessories_info", 0,0 ,"button_status(4)",4); ?></td>
                                    </tr>
                            	</table>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </form>
        </div>
        <h3 align="left" class="accordion_h" onClick="show_hide_content('required_wash', ''); fnc_load_tr(document.getElementById('update_id').value,4); hide_others_section('wash');" style="width: 1200px;"> +Required Wash</h3>
		<div id="content_required_wash" style="display:none;">
			<form name="required_wash_1" id="required_wash_1">
				<fieldset style="width: 1200px;" id="required_wash_dtls">
					<table width="100%" cellpadding="0" cellspacing="2" align="left" style="">
						<tr>
							<td align="center" valign="top" id="po_list_view">
								<legend style="width:1200px;">Required Wash </legend>
								<table cellpadding="0" cellspacing="0" width="1200px" class="rpt_table" border="1" rules="all" id="tbl_required_wash">
									<thead>
										<th width="100" class="must_entry_caption">Sample Name </th>
										<th width="100" class="must_entry_caption">Garment Item</th>
										<th width="120" class="must_entry_caption">Wash Name</th>
										<th width="120" class="must_entry_caption">Wash Type</th>
										<th width="95">Body Part</th>
										<th width="100">Supplier</th>
                                        <th width="70">Qty Pcs</th>
                                        <th width="60">Rate</th>
                                        <th width="70">Amount</th>
										<th width="100">Remarks</th>
										<th width="65">Delivery Date</th>
										<th width="70">Image</th>
										<th>&nbsp;</th>
									</thead>

									<tbody id="required_wash_container">
										<tr id="tr_1" style="height:10px;" class="general">
											<td id="waSampleId_1"><?=create_drop_down( "cboWaSampleName_1", 100, $blank_array,"", 1, "-Sample-", $selected, ""); ?></td>
											<td id="waItemIid_1"><?=create_drop_down( "cboWaGarmentItem_1", 100, $blank_array,"", 1, "Select Item", 0, ""); ?></td>
											<td id="re_name_1"><?=create_drop_down( "cboWaName_1", 120, $emblishment_name_array,"", 1, "Select Name", 3, "","","3" );//cbotype_loder(3); ?></td>
                                            
											<td id="reType_1"><?=create_drop_down( "cboReType_1", 120, $emblishment_wash_type,"", 1, "Select Item", 0, ""); ?></td>
											<td id="re_body_part_1"><?=create_drop_down( "cboWaBodyPart_1", 95, $body_part,"", 1, "Select Body Part", 0, ""); ?></td>
											<td><?=create_drop_down( "cboWaSupplierName_1", 100, "select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.status_active =1 and a.is_deleted=0  and b.party_type in (select party_type from lib_supplier_party_type where a.id=b.supplier_id and b.party_type=23) order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 ); ?></td>
                                            <td id="re_qty_1">
												<input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtWaQty_1" id="txtWaQty_1" placeholder="click"  onClick="open_consumption_popupWash('requires/sample_requisition_with_booking_controller.php?action=consumption_popup','Consumtion Entry Form','1');" readonly/>
                                                <input style="width:60px;" type="hidden" class="text_boxes"  name="txtWacolorBreakdown_1" id="txtWacolorBreakdown_1"  readonly="" />
											</td>
                                            <td id="re_rate_1"><input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtWaRate_1" id="txtWaRate_1" placeholder="Rate"  readonly="" /></td>
                                            <td id="re_amount_1"><input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtWaAmount_1" id="txtWaAmount_1" placeholder="Amount"  readonly="" /></td>
                                            <td id="re_remarks_1"><input style="width:90px;" type="text" class="text_boxes"  name="txtWaRemarks_1" id="txtWaRemarks_1" placeholder="click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','4','1');"/> </td>
											<td id="deliveryDateid_1"><input style="width:55px;" type="text" class="datepicker" placeholder="Date" autocomplete="off" name="deliveryWaDate_1" id="deliveryWaDate_1" /></td>
											<td id="re_image_1">
                                            	<input type="button" class="image_uploader" name="waTxtFile_1" id="waTxtFile_1" onClick="file_uploader ( '../../',document.getElementById('updateidRequiredWaDtls_1').value,'', 'requiredwash_1', 0 ,1);" style="width:70px;" value="CLICK TO ADD/VIEW IMAGE">
												<input type="hidden" id="updateidRequiredWaDtls_1" name="updateidRequiredWaDtls_1"  class="text_boxes" style="width:20px" value="" />
												<input type="hidden" id="txtDeltedIdWa" name="txtDeltedIdWa"  class="text_boxes" style="width:20px" value="" />
                                            </td>
											<td>
												<input type="button" id="increasere_1" name="increasere_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_wash_tr(1);" />
												<input type="button" id="decreasere_1" name="decreasere_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_wash_deleteRow(1);" />
											</td>
										</tr>
									</tbody>
								</table>
								<table>
									<tr>
										<td colspan="10" valign="bottom" align="center">
											<?=load_submit_buttons( $permission, "fnc_required_wash_info", 0,0 ,"button_status(5)",5); ?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
        <h3 align="left" class="accordion_h" onClick="show_hide_content('required_print', ''); fnc_load_tr(document.getElementById('update_id').value,5); hide_others_section('print');" style="width: 1200px;"> +Required Print</h3>
		<div id="content_required_print" style="display:none;">
			<form name="required_print_1" id="required_printt_1">
				<fieldset style="width: 1200px;" id="required_print_dtls">
					<table width="100%" cellpadding="0" cellspacing="2" align="left" style="">
						<tr>
							<td align="center" valign="top" id="po_list_view">
								<legend style="width:1200px;">Required Print</legend>
								<table cellpadding="0" cellspacing="0" width="1200px" class="rpt_table" border="1" rules="all" id="tbl_required_print">
									<thead>
										<th width="100" class="must_entry_caption">Sample Name </th>
										<th width="100" class="must_entry_caption">Garment Item</th>
										<th width="120" class="must_entry_caption">Print Name</th>
										<th width="120" class="must_entry_caption">Print Type</th>
										<th width="95">Body Part</th>
										<th width="100">Supplier</th>
                                        <th width="70">Qty Pcs</th>
                                        <th width="60">Rate</th>
                                        <th width="70">Amount</th>
										<th width="100">Remarks</th>
										<th width="65">Delivery Date</th>
										<th width="70">Image</th>
										<th>&nbsp;</th>
									</thead>

									<tbody id="required_print_container">
										<tr id="tr_1" style="height:10px;" class="general">
											<td id="prSampleId_1"><?=create_drop_down( "cboPrSampleName_1", 100, $blank_array,"", 1, "-Sample-", $selected, ""); ?></td>
											<td id="prItemIid_1"><?=create_drop_down( "cboPrGarmentItem_1", 100, $blank_array,"", 1, "Select Item", 0, ""); ?></td>
											<td id="re_name_1"><?=create_drop_down( "cboPrName_1", 120, $emblishment_name_array,"", 1, "Select Name", 1, "","","1" );//cbotype_loder(1); ?></td>
											<td id="reType_1"><?=create_drop_down( "cboPrType_1", 120, $emblishment_print_type,"", 1, "Select Item", 0, ""); ?></td>
											<td id="re_body_part_1"><?=create_drop_down( "cboPrBodyPart_1", 95, $body_part,"", 1, "Select Body Part", 0, ""); ?></td>
											<td><?=create_drop_down( "cboPrSupplierName_1", 100, "select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.status_active =1 and a.is_deleted=0  and b.party_type in (select party_type from lib_supplier_party_type where a.id=b.supplier_id and b.party_type=23) order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 ); ?></td>
                                            <td id="re_qty_1">
												<input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtPrQty_1" id="txtPrQty_1" placeholder="click"  onClick="open_consumption_popupPrint('requires/sample_requisition_with_booking_controller.php?action=consumption_popup','Consumtion Entry Form','1');" readonly />
                                                <input style="width:60px;" type="hidden" class="text_boxes"  name="txtPrcolorBreakdown_1" id="txtPrcolorBreakdown_1"  readonly="" />
											</td>
                                            <td id="re_rate_1"><input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtPrRate_1" id="txtPrRate_1" placeholder="Rate"  readonly="" /></td>
                                            <td id="re_amount_1"><input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtPrAmount_1" id="txtPrAmount_1" placeholder="Amount"  readonly="" /></td>
                                            <td id="re_remarks_1"><input style="width:90px;" type="text" class="text_boxes"  name="txtPrRemarks_1" id="txtPrRemarks_1" placeholder="click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','5','1');"/> </td>
											<td id="deliveryDateid_1"><input style="width:55px;" type="text" class="datepicker" placeholder="Date" autocomplete="off" name="deliveryPrDate_1" id="deliveryPrDate_1" /></td>
											<td id="re_image_1">
                                            	<input type="button" class="image_uploader" name="prTxtFile_1" id="prTxtFile_1" onClick="file_uploader ( '../../',document.getElementById('updateidRequiredPrDtls_1').value,'', 'requiredPrint_1', 0 ,1);" style="width:70px;" value="CLICK TO ADD/VIEW IMAGE">
												<input type="hidden" id="updateidRequiredPrDtls_1" name="updateidRequiredPrDtls_1"  class="text_boxes" style="width:20px" value="" />
												<input type="hidden" id="txtDeltedIdPr" name="txtDeltedIdPr"  class="text_boxes" style="width:20px" value="" />
                                            </td>
											<td>
												<input type="button" id="increasere_1" name="increasere_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_print_tr(1);" />
												<input type="button" id="decreasere_1" name="decreasere_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_print_deleteRow(1);" />
											</td>
										</tr>
									</tbody>
								</table>
								<table>
									<tr>
										<td colspan="10" valign="bottom" align="center">
											<?=load_submit_buttons( $permission, "fnc_required_print_info", 0,0 ,"button_status(6)",6); ?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
		<h3 align="left" class="accordion_h" onClick="show_hide_content('required_embellishment', ''); fnc_load_tr(document.getElementById('update_id').value,6); hide_others_section('emb');" style="width: 1200px;"> +Required Embroidery</h3>
		<div id="content_required_embellishment" style="display:none;">
			<form name="required_embellishment_1" id="required_embellishment_1">
				<fieldset style="width: 1200px;" id="required_embellishment_dtls">
					<table width="100%" cellpadding="0" cellspacing="2" align="left" style="">
						<tr>
							<td align="center" valign="top" id="po_list_view">
								<legend style="width:1200px;">Required Embroidery</legend>
								<table cellpadding="0" cellspacing="0" width="1200px" class="rpt_table" border="1" rules="all" id="tbl_required_embellishment">
									<thead>
										<th width="100" class="must_entry_caption">Sample Name </th>
										<th width="100" class="must_entry_caption">Garment Item</th>
										<th width="120" class="must_entry_caption">Embroidery Name</th>
										<th width="120" class="must_entry_caption">Embroidery Type</th>
										<th width="95">Body Part</th>
										<th width="100">Supplier</th>
                                        <th width="70">Qty Pcs</th>
                                        <th width="60">Rate</th>
                                        <th width="70">Amount</th>
										<th width="100">Remarks</th>
										<th width="65">Delivery Date</th>
										<th width="70">Image</th>
										<th>&nbsp;</th>
									</thead>

									<tbody id="required_embellishment_container">
										<tr id="tr_1" style="height:10px;" class="general">
											<td id="reSampleId_1"><?=create_drop_down( "cboReSampleName_1", 100, $blank_array,"", 1, "-Sample-", $selected, ""); ?></td>
											<td id="reItemIid_1"><?=create_drop_down( "cboReGarmentItem_1", 100, $blank_array,"", 1, "Select Item", 0, ""); ?></td>
											<td id="re_name_1"><?=create_drop_down( "cboReName_1", 120, $emblishment_name_array,"", 1, "Select Name", 2, "","","2" );//cbotype_loder(1); ?></td>
											<td id="reType_1"><?=create_drop_down( "cboReTypeId_1", 120, $emblishment_embroy_type,"", 1, "Select Item", 0, ""); ?></td>
											<td id="re_body_part_1"><?=create_drop_down( "cboReBodyPart_1", 95, $body_part,"", 1, "Select Body Part", 0, ""); ?></td>
											<td><?=create_drop_down( "cboReSupplierName_1", 100, "select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.status_active =1 and a.is_deleted=0  and b.party_type in (select party_type from lib_supplier_party_type where a.id=b.supplier_id and b.party_type=23) order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 ); ?></td>
                                            <td id="re_qty_1">
												<input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtReQty_1" id="txtReQty_1" placeholder="click"  onClick="open_consumption_popup('requires/sample_requisition_with_booking_controller.php?action=consumption_popup','Consumtion Entry Form','1');" readonly />
                                                <input style="width:60px;" type="hidden" class="text_boxes"  name="txtcolorBreakdown_1" id="txtcolorBreakdown_1"  readonly="" />
											</td>
                                            <td id="re_rate_1"><input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtReRate_1" id="txtReRate_1" placeholder="Rate"  readonly="" /></td>
                                            <td id="re_amount_1"><input style="width:60px;" type="text" class="text_boxes_numeric"  name="txtReAmount_1" id="txtReAmount_1" placeholder="Amount"  readonly="" /></td>
                                            <td id="re_remarks_1"><input style="width:90px;" type="text" class="text_boxes"  name="txtReRemarks_1" id="txtReRemarks_1" placeholder="click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','6','1');"/> </td>
											<td id="deliveryDateid_1"><input style="width:55px;" type="text" class="datepicker" placeholder="Date" autocomplete="off" name="deliveryDate_1" id="deliveryDate_1" /></td>
											<td id="re_image_1">
                                            	<input type="button" class="image_uploader" name="reTxtFile_1" id="reTxtFile_1" onClick="file_uploader ( '../../',document.getElementById('updateidRequiredEmbellishdtl_1').value,'', 'required_embellishment_1', 0 ,1);" style="width:70px;" value="CLICK TO ADD/VIEW IMAGE">
												<input type="hidden" id="updateidRequiredEmbellishdtl_1" name="updateidRequiredEmbellishdtl_1"  class="text_boxes" style="width:20px" value="" />
												<input type="hidden" id="txtDeltedIdRe" name="txtDeltedIdRe"  class="text_boxes" style="width:20px" value="" />
                                            </td>
											<td>
												<input type="button" id="increasere_1" name="increasere_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_re_tr(1);" />
												<input type="button" id="decreasere_1" name="decreasere_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_re_deleteRow(1);" />
											</td>
										</tr>
									</tbody>
								</table>
								<table>
									<tr>
										<td colspan="10" valign="bottom" align="center">
											<?=load_submit_buttons( $permission, "fnc_required_embellishment_info", 0,0 ,"button_status(7)",7); ?>
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
	var buyer=$("#cbo_buyer_name").val();
	if(buyer!=0)
	{
		load_drop_down( 'requires/sample_requisition_with_booking_controller', buyer, 'load_drop_down_season_buyer', 'season_td');
	}
</script>
</html>