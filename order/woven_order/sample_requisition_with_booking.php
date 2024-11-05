<?
/*-------------------------------------------- Comments
Purpose			         :  This Form Will Create Sample Requisition with booking Entry.
Functionality	         :
JS Functions	         :
Created by		         : Rehan Uddin
Creation date 	         : 24/4/2018
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
// echo "<pre>";
// print_r($season_mandatory_sql);die;
foreach($season_mandatory_sql as $key=>$value)
{
	$season_mandatory_arr[$value[csf("company_name")]]=$value[csf("season_mandatory")];
}

// print_r($_SESSION['logic_erp']['mandatory_field'][203]);

// print_r($season_mandatory_arr);

$season_mandatory_arr=json_encode($season_mandatory_arr);
//echo implode('*',$_SESSION['logic_erp']['mandatory_message'][203]);die;
$fabric_dia=0;
// if (array_search('txt_fabric_Dia', $_SESSION['logic_erp']['mandatory_field'][203][8]) !== false) {
//     $fabric_dia=1;
// }

if($_SESSION['logic_erp']['mandatory_field'][203][8] == 'txt_fabric_Dia'){
	 
	$fabric_dia=1;
	 
}



// echo "<br>";
// print_r($fabric_dia);
 

function arrayExclude($array,Array $excludeKeys){
    foreach($array as $key => $value){
        if(!in_array($key, $excludeKeys)){
            $return[$key] = $value;
        }
    }
    return $return;
}

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Requisition","../../", 1, 1, $unicode,1,'');
?>
<script>
<?
	//echo "var mandatory_field = '". implode('*',$_SESSION['logic_erp']['mandatory_field'][203]) . "';\n";
	//echo "var mandatory_message = '". implode('*',$_SESSION['logic_erp']['mandatory_message'][203]) . "';\n";
?>
	var season_mandatory_arr='<? echo $season_mandatory_arr;?>';
 	var season_mandatory_arr=JSON.parse(season_mandatory_arr);

  	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
  	var permission='<? echo $permission; ?>';

  	function sample_wise_item(sample_mst_id,sample,inc,type)
  	{
  		//alert(sample_mst_id+sample+inc+type);
  		var data=sample_mst_id+'**'+sample;
  		var qty =return_global_ajax_value( data, 'sample_wise_item_data', '', 'requires/sample_requisition_with_booking_controller');
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
		var updateidRequiredDtl=$("#updateidRequiredDtl_1").val();
		if((!updateidRequiredDtl) && (!req_id))
		{
			alert("Save Fabric first!!");
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
		freeze_window(operation);
  		var req_id=$("#update_id").val();	
		 var  ready_to_approved=$("#cbo_ready_to_approved").val();	
  		var sample_stage=$("#cbo_sample_stage").val();
  		if(!req_id)
  		{
  			alert("Save requisition first!!");
			release_freezing();
  			return;
  		}	
		if(ready_to_approved==2 || ready_to_approved==0)
  		{
  			alert("Ready to approve Yes first!!");
			release_freezing();
  			return;
  		}
		
		//alert(req_id+'='+sample_stage);
  		if(sample_stage==2 || sample_stage==3)
  		{
			var rr=confirm("Please Check Yarn Count at Yarn Details Button.");
			if(rr==true)
			{
			}
			else
			{
				release_freezing();	
				return;
			}
  			if (form_validation('cbo_company_name*cbo_buyer_name*txt_booking_date*cbo_pay_mode','Company*Buyer*Booking Date*Pay Mode')==false)
  			{
				release_freezing();
  				return;
  			}
  			else
  			{
  				var row_nums=$('#tbl_required_fabric tr').length-1;
  				var data_all=""; var z=1;
  				for (var i=1; i<=row_nums; i++)
  				{
  					if (form_validation('cboRfSampleName_'+i+'*cboRfGarmentItem_'+i+'*cboRfBodyPart_'+i+'*cboRfFabricNature_'+i+'*txtRfFabricDescription_'+i+'*txtRfGsm_'+i+'*txtRfColorAllData_'+i+'*cboRfColorType_'+i+'*cboRfUom_'+i+'*txtRfReqQty_'+i,'Sample Name*Garment Item*Body Part*Fabric Nature*Fabric Desc*Gsm*Browse Color*Color Type*Uom*ReqDzn*ReqQty')==false)
  					{
						release_freezing();
  						return;
  					}
  					//data_all=data_all+get_submitted_data_string('cboRfSampleName_'+i+'*cboRfGarmentItem_'+i+'*cboRfBodyPart_'+i+'*cboRfFabricNature_'+i+'*txtRfFabricDescription_'+i+'*txtRfGsm_'+i+'*txtRfDia_'+i+'*txtRfColor_'+i+'*cboRfColorType_'+i+'*cboRfWidthDia_'+i+'*cboRfUom_'+i+'*txtRfReqDzn_'+i+'*txtRfReqQty_'+i+'*txtProcessLoss_'+i+'*txtGrayFabric_'+i+'*updateidRequiredDtl_'+i+'*txtRfColorAllData_'+i+'*fabricDelvDate_'+i+'*cboRfFabricSource_'+i+'*txtRfRemarks_'+i+'*libyarncountdeterminationid_'+i,"../../");
					
					data_all+="&cboRfSampleName_" + z + "='" + $('#cboRfSampleName_'+i).val()+"'"+"&cboRfGarmentItem_" + z + "='" + $('#cboRfGarmentItem_'+i).val()+"'"+"&cboRfBodyPart_" + z + "='" + $('#cboRfBodyPart_'+i).val()+"'"+"&cboRfFabricNature_" + z + "='" + $('#cboRfFabricNature_'+i).val()+"'"+"&txtRfFabricDescription_" + z + "='" + $('#txtRfFabricDescription_'+i).val()+"'"+"&txtRfGsm_" + z + "='" + $('#txtRfGsm_'+i).val()+"'"+"&txtRfDia_" + z + "='" + $('#txtRfDia_'+i).val()+"'"+"&txtRfColor_" + z + "='" + $('#txtRfColor_'+i).val()+"'"+"&cboRfColorType_" + z + "='" + $('#cboRfColorType_'+i).val()+"'"+"&cboRfWidthDia_" + z + "='" + $('#cboRfWidthDia_'+i).val()+"'"+"&cboRfUom_" + z + "='" + $('#cboRfUom_'+i).val()+"'"+"&txtRfReqDzn_" + z + "='" + $('#txtRfReqDzn_'+i).val()+"'"+"&txtRfReqQty_" + z + "='" + $('#txtRfReqQty_'+i).val()+"'"+"&txtProcessLoss_" + z + "='" + $('#txtProcessLoss_'+i).val()+"'"+"&txtGrayFabric_" + z + "='" + $('#txtGrayFabric_'+i).val()+"'"+"&updateidRequiredDtl_" + z + "='" + $('#updateidRequiredDtl_'+i).val()+"'"+"&txtRfColorAllData_" + z + "='" + $('#txtRfColorAllData_'+i).val()+"'"+"&fabricDelvDate_" + z + "='" + $('#fabricDelvDate_'+i).val()+"'"+"&cboRfFabricSource_" + z + "='" + $('#cboRfFabricSource_'+i).val()+"'"+"&txtRfRemarks_" + z + "='" + $('#txtRfRemarks_'+i).val()+"'"+"&libyarncountdeterminationid_" + z + "='" + $('#libyarncountdeterminationid_'+i).val()+"'";
					
					z++;
  				}
  				var operation=0;
  				var cbo_team_leader_book=$('#cbo_team_leader').val();
  				var cbo_dealing_merchant_book=$('#cbo_dealing_merchant').val();
  				var txt_style_desc=$('#txt_style_desc').val();
				
  				var data="action=save_update_delete_booking&operation="+operation+'&total_row='+row_nums+'&cbo_team_leader_book='+cbo_team_leader_book+'&cbo_dealing_merchant_book='+cbo_dealing_merchant_book+'&txt_style_desc='+txt_style_desc+'&sample_stage='+sample_stage+data_all+get_submitted_data_string('update_id*cbo_company_name*cbo_buyer_name*txt_booking_no*cbo_fabric_source*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_supplier_name*txt_attention*cbo_ready_to_approved_book*txt_buyer_req_no*cbo_sources*txt_revise_no*txt_booking_remarks*txt_processloss_breck_down',"../../");

  				http.open("POST","requires/sample_requisition_with_booking_controller.php",true);
  				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  				http.send(data);
  				http.onreadystatechange = fnc_fabric_booking_reponse;
  			}
  		}
  		else{
			if (form_validation('cbo_company_name*txt_booking_date*cbo_pay_mode','Company*Booking Date*Pay Mode')==false)
  			{
				release_freezing();
  				return;
  			}
			else{
				var title="PO Data";
				var page_link='requires/sample_requisition_with_booking_controller.php?action=generate_booking_popup&job_id='+document.getElementById('txt_quotation_id').value;
				emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=200px,center=1,resize=0,scrolling=0', '')
				emailwindow.onclose = function()
				{
					var theform = this.contentDoc.forms[0];
					var select_template_data = this.contentDoc.getElementById('select_template_data').value;

					if(select_template_data!=''){
						var row_nums=$('#tbl_required_fabric tr').length-1;
						var data_all=""; var z=1;
						for (var i=1; i<=row_nums; i++)
						{
							if (form_validation('cboRfSampleName_'+i+'*cboRfGarmentItem_'+i+'*cboRfBodyPart_'+i+'*cboRfFabricNature_'+i+'*txtRfFabricDescription_'+i+'*txtRfGsm_'+i+'*txtRfColorAllData_'+i+'*cboRfColorType_'+i+'*cboRfUom_'+i+'*txtRfReqQty_'+i,'Sample Name*Garment Item*Body Part*Fabric Nature*Fabric Desc*Gsm*Browse Color*Color Type*Uom*ReqDzn*ReqQty')==false)
							{
								release_freezing();
								return;
							}
							
							
							data_all+="&cboRfSampleName_" + z + "='" + $('#cboRfSampleName_'+i).val()+"'"+"&cboRfGarmentItem_" + z + "='" + $('#cboRfGarmentItem_'+i).val()+"'"+"&cboRfBodyPart_" + z + "='" + $('#cboRfBodyPart_'+i).val()+"'"+"&cboRfFabricNature_" + z + "='" + $('#cboRfFabricNature_'+i).val()+"'"+"&txtRfFabricDescription_" + z + "='" + $('#txtRfFabricDescription_'+i).val()+"'"+"&txtRfGsm_" + z + "='" + $('#txtRfGsm_'+i).val()+"'"+"&txtRfDia_" + z + "='" + $('#txtRfDia_'+i).val()+"'"+"&txtRfColor_" + z + "='" + $('#txtRfColor_'+i).val()+"'"+"&cboRfColorType_" + z + "='" + $('#cboRfColorType_'+i).val()+"'"+"&cboRfWidthDia_" + z + "='" + $('#cboRfWidthDia_'+i).val()+"'"+"&cboRfUom_" + z + "='" + $('#cboRfUom_'+i).val()+"'"+"&txtRfReqDzn_" + z + "='" + $('#txtRfReqDzn_'+i).val()+"'"+"&txtRfReqQty_" + z + "='" + $('#txtRfReqQty_'+i).val()+"'"+"&txtProcessLoss_" + z + "='" + $('#txtProcessLoss_'+i).val()+"'"+"&txtGrayFabric_" + z + "='" + $('#txtGrayFabric_'+i).val()+"'"+"&updateidRequiredDtl_" + z + "='" + $('#updateidRequiredDtl_'+i).val()+"'"+"&txtRfColorAllData_" + z + "='" + $('#txtRfColorAllData_'+i).val()+"'"+"&fabricDelvDate_" + z + "='" + $('#fabricDelvDate_'+i).val()+"'"+"&cboRfFabricSource_" + z + "='" + $('#cboRfFabricSource_'+i).val()+"'"+"&txtRfRemarks_" + z + "='" + $('#txtRfRemarks_'+i).val()+"'"+"&libyarncountdeterminationid_" + z + "='" + $('#libyarncountdeterminationid_'+i).val()+"'";
							z++;
						}
						var operation=0;
						var cbo_team_leader_book=$('#cbo_team_leader').val();
						var cbo_dealing_merchant_book=$('#cbo_dealing_merchant').val();
						var txt_style_desc=$('#txt_style_desc').val();
						
						var data="action=save_update_delete_booking&operation="+operation+'&total_row='+row_nums+'&cbo_team_leader_book='+cbo_team_leader_book+'&cbo_dealing_merchant_book='+cbo_dealing_merchant_book+'&txt_style_desc='+txt_style_desc+'&sample_stage='+sample_stage+'&po_data='+select_template_data+data_all+get_submitted_data_string('update_id*cbo_company_name*cbo_buyer_name*txt_booking_no*cbo_fabric_source*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_supplier_name*txt_attention*cbo_ready_to_approved_book*txt_buyer_req_no*cbo_sources*txt_revise_no*txt_booking_remarks*txt_processloss_breck_down*txt_quotation_id',"../../");
						
						http.open("POST","requires/sample_requisition_with_booking_controller.php",true);
						http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
						http.send(data);
						http.onreadystatechange = fnc_fabric_booking_reponse;
					}
					else{
						alert("Please Select PO From Popup");
						release_freezing();
                		return;
					}
				}
			}
  		}
  	}

  	function fnc_fabric_booking_reponse()
  	{
  		if(http.readyState == 4)
  		{
  			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==14)
            {
                alert("This Booking is Approved. Not allowed This Button!!");
                release_freezing();
                return;
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

  	function open_stripe_color_popup()
	{
		var req_id=$("#update_id").val();
		if(!req_id)
		{
			alert("Save requisition first!!");
			return;	
		}
		var row_num=$('#required_fabric_container tr').length-1;
		for (var i=1; i<=row_num; i++)
		{
			if($('#updateidRequiredDtl_'+i).val()=='')
			{
			  alert("Save or Update Fabric Cost")
			  return;
			}
		}

		var txt_job_no=document.getElementById('txt_requisition_id').value;
		var hidd_job_id=document.getElementById('update_id').value;
		var index_page=$('#index_page', window.parent.document).val();
		var page_link="sample_stripe_color_measurement.php?permission="+permission+'&txt_job_no='+txt_job_no+'&hidd_job_id='+hidd_job_id+'&index_page='+index_page;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Set Details", 'width=1200px,height=500px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}

  	function copy_requisition(operation)
	{
		alert("After copy company name, buyer name,sample stage changing is not allowed");
		var comp=$('#cbo_company_name').val();
		if(Array.isArray(season_mandatory_arr) && season_mandatory_arr.length)
		{
		   if(season_mandatory_arr[comp]==1)
		   {
		   	    var data1="cbo_sample_stage*txt_requisition_date*txt_style_name*cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_season_name*cbo_product_department*cbo_dealing_merchant";
		   	    var data2="Sample Stage*Requisition Date*Style Name*Company Name*Location*Buyer*Season Name*Product Department*Dealing Merchandiser";
		   }
		   if(season_mandatory_arr[comp]!=1)
		   {
		    	var data1="cbo_sample_stage*txt_requisition_date*txt_style_name*cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_product_department*cbo_dealing_merchant";
		   	    var data2="Sample Stage*Requisition Date*Style Name*Company Name*Location*Buyer*Product Department*Dealing Merchandiser";

		   }
		}
		else{
			var data1="cbo_sample_stage*txt_requisition_date*txt_style_name*cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_product_department*cbo_dealing_merchant";
		   	var data2="Sample Stage*Requisition Date*Style Name*Company Name*Location*Buyer*Product Department*Dealing Merchandiser";
		}

	   if (form_validation(data1,data2)==false)
		{
			return;
		}
		else
		{
			var data="action=copy_requisition&operation="+operation+get_submitted_data_string('txt_requisition_id*cbo_sample_stage*cbo_company_name*txt_requisition_date*txt_quotation_id*txt_style_name*cbo_location_name*cbo_buyer_name*cbo_season_name*cbo_product_department*txt_item_catgory*cbo_team_leader*cbo_dealing_merchant*cbo_agent*txt_buyer_ref*txt_est_ship_date*txt_bhmerchant*txt_remarks*cbo_ready_to_approved*txt_material_dlvry_date*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/sample_requisition_with_booking_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
		 	http.onreadystatechange = fnc_sample_requisition_mst_info_reponse;
		 	$("#txt_booking_no").val('');
			 $('#content_sample_details').hide();
			$('#content_required_fabric').hide();
			$('#content_required_accessories').hide();
			$('#content_required_embellishment').hide();


		}

		$("#approvedMsg").html('');$("#booking_approvedMsg").html('');
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
		var comp = $('#cbo_company_name').val();
		var txt_booking_no=$('#txt_booking_no').val();
		freeze_window(operation);

 		if(operation==4 || operation==5 || operation==6 || operation==7 || operation==8 || operation==9 || operation==10 || operation==11  || operation==12  || operation==13 || operation==14 || operation==15 || operation==16)
		{
			if (form_validation('txt_requisition_id','Requisition Id')==false)
  			{
				release_freezing();
  				return;
  			}
			if(operation==4) var action="sample_requisition_print";
			else if(operation==5) var action="sample_requisition_print1";
			else if(operation==6) var action="sample_requisition_print6"; 
			else if(operation==7) var action="sample_requisition_print7";
			else if(operation==8) var action="sample_requisition_print8"; 
			else if(operation==9) var action="sample_requisition_print9";
			else if(operation==10) var action="sample_requisition_print10";
			else if(operation==11) var action="sample_requisition_print11";
			else if(operation==12) var action="sample_requisition_print12";
			else if(operation==13) var action="sample_requisition_print13";
			else if(operation==14) var action="sample_requisition_print14";
			else if(operation==15) var action="sample_requisition_print15";
			else if(operation==16) var action="sample_requisition_print16";
			var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id*cbo_template_id',"../../");

			if(operation==6)
			{
				 freeze_window(5);
				var user_id = "<? echo $user_id; ?>";
				$.ajax({
					url: 'requires/sample_requisition_with_booking_controller.php',
					type: 'POST',
					data: data,
					success: function(data){
						
						window.open('../../auto_mail/tmp/sample_req_with_booking_'+user_id+'.pdf');
						release_freezing();
						
					}
				});
				//release_freezing();
				return;
			}
			else{
				print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_booking_no').val()+'*'+$('#cbo_template_id').val(), action, "requires/sample_requisition_with_booking_controller" );
				release_freezing();
				return;
			}
		}
		 
		// if(Array.isArray(season_mandatory_arr) && season_mandatory_arr.length)
		// {
		// 	if(season_mandatory_arr[comp]==1)
		// 	{
		// 		var data1="cbo_sample_stage*txt_requisition_date*cbo_company_name*cbo_location_name*cbo_buyer_name*txt_style_name*cbo_product_department*cbo_season_year*cbo_season_name*txt_item_catgory*cbo_team_leader*cbo_dealing_merchant"

		// 		var data2="Sample Stage*Requisition Date*Company Name*Location*Buyer*Style Name*Product Department*Season Year*Season Name*Product Category*Team Leader*Dealing Merchandiser";
		// 	}
		// 	if(season_mandatory_arr[comp]!=1)
		// 	{
		// 		var data1="cbo_sample_stage*txt_requisition_date*cbo_company_name*cbo_location_name*cbo_buyer_name*txt_style_name*cbo_product_department*txt_item_catgory*cbo_team_leader*cbo_dealing_merchant"

		// 		var data2="Sample Stage*Requisition Date*Company Name*Location*Buyer*Style Name*Product Department*Product Category*Team Leader*Dealing Merchandiser";
		// 	}
		// }
		// else
		// {
		// 	var data1="cbo_sample_stage*txt_requisition_date*cbo_company_name*cbo_location_name*cbo_buyer_name*txt_style_name*cbo_product_department*txt_item_catgory*cbo_team_leader*cbo_dealing_merchant"
		// 	var data2="Sample Stage*Requisition Date*Company Name*Location*Buyer*Style Name*Product Department*Product Category*Team Leader*Dealing Merchandiser";
		// }

		// var isFileMandatory = "";
		// < ?
		// 	if(!empty($_SESSION['logic_erp']['mandatory_field'][203][7])) echo " isFileMandatory = ". $_SESSION['logic_erp']['mandatory_field'][203][7] . ";\n";
		// ?>

		// if($("#multiple_file_field")[0].files.length==0 && isFileMandatory!="" && $('#update_id').val()==''){
		// 	document.getElementById("multiple_file_field").focus();
		// 	var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
		// 	document.getElementById("multiple_file_field").style.backgroundImage=bgcolor;
		// 	alert("Please Add File in Master Part");
		// 	return;
		// }

		// if ('< ?php echo implode('*', arrayExclude($_SESSION['logic_erp']['mandatory_field'][203],array(7,8))); ?>') {
		// 	if (form_validation('< ?php echo implode('*',  arrayExclude($_SESSION['logic_erp']['mandatory_field'][203],array(7,8))); ?>', '< ?php echo implode('*',  arrayExclude($_SESSION['logic_erp']['field_message'][203],array(7,8))); ?>') == false) {
		// 		return;
		// 	}
		// }

		// if (form_validation(data1,data2)==false)
		// {
		// 	release_freezing();
		// 	return;
		// }
 
		if( form_validation('cbo_sample_stage*txt_requisition_date*cbo_company_name*cbo_location_name*cbo_buyer_name*txt_style_name*cbo_product_department*txt_item_catgory*cbo_team_leader*cbo_dealing_merchant','Sample Stage*Requisition Date*Company Name*Location*Buyer*Style Name*Product Department*Product Category*Team Leader*Dealing Merchandiser')==false )
		{
			release_freezing();
			return;
		}

		if('<? echo $_SESSION['logic_erp']['mandatory_field'][203][1];?>'){
			if (form_validation('<? echo $_SESSION['logic_erp']['mandatory_field'][203][1];?>','<? echo $_SESSION['logic_erp']['field_message'][203][1];?>')==false)
			{ 
				release_freezing();
				return;
			}
		}
		if('<? echo $_SESSION['logic_erp']['mandatory_field'][203][2];?>'){
			if (form_validation('<? echo $_SESSION['logic_erp']['mandatory_field'][203][2];?>','<? echo $_SESSION['logic_erp']['field_message'][203][2];?>')==false)
			{ 
				release_freezing();
				return;
			}
		}
		if('<? echo $_SESSION['logic_erp']['mandatory_field'][203][3];?>'){
			if (form_validation('<? echo $_SESSION['logic_erp']['mandatory_field'][203][3];?>','<? echo $_SESSION['logic_erp']['field_message'][203][3];?>')==false)
			{ 
				release_freezing();
				return;
			}
		}
		if('<? echo $_SESSION['logic_erp']['mandatory_field'][203][4];?>'){
			if (form_validation('<? echo $_SESSION['logic_erp']['mandatory_field'][203][4];?>','<? echo $_SESSION['logic_erp']['field_message'][203][4];?>')==false)
			{ 
				release_freezing();
				return;
			}
		}
		if('<? echo $_SESSION['logic_erp']['mandatory_field'][203][5];?>'){
			if (form_validation('<? echo $_SESSION['logic_erp']['mandatory_field'][203][5];?>','<? echo $_SESSION['logic_erp']['field_message'][203][5];?>')==false)
			{ 
				release_freezing();
				return;
			}
		}
		if('<? echo $_SESSION['logic_erp']['mandatory_field'][203][6];?>'){
			if (form_validation('<? echo $_SESSION['logic_erp']['mandatory_field'][203][6];?>','<? echo $_SESSION['logic_erp']['field_message'][203][6];?>')==false)
			{ 
				release_freezing();
				return;
			}
		}
		if('<? echo $_SESSION['logic_erp']['mandatory_field'][203][9];?>'){
			if (form_validation('<? echo $_SESSION['logic_erp']['mandatory_field'][203][9];?>','<? echo $_SESSION['logic_erp']['field_message'][203][9];?>')==false)
			{ 
				release_freezing();
				return;
			}
		}
		if('<? echo $_SESSION['logic_erp']['mandatory_field'][203][10];?>'){
			if (form_validation('<? echo $_SESSION['logic_erp']['mandatory_field'][203][10];?>','<? echo $_SESSION['logic_erp']['field_message'][203][10];?>')==false)
			{ 
				release_freezing();
				return;
			}
		}
		if('<? echo $_SESSION['logic_erp']['mandatory_field'][203][11];?>'){
			if (form_validation('<? echo $_SESSION['logic_erp']['mandatory_field'][203][11];?>','<? echo $_SESSION['logic_erp']['field_message'][203][11];?>')==false)
			{ 
				release_freezing();
				return;
			}
		} 
		

		// if('< ? echo implode('*', $_SESSION['logic_erp']['mandatory_field'][203]);?>' ){
		// 	if (form_validation('< ? echo implode('*', $_SESSION['logic_erp']['mandatory_field'][203]);?>','< ? echo implode('*', $_SESSION['logic_erp']['field_message'][203]);?>')==false)
		// 	{ 
		// 		release_freezing();
		// 		return;
		// 	}
		// } 
		 
		var data = "action=save_update_delete_mst&operation="+operation+get_submitted_data_string('txt_requisition_id*cbo_sample_stage*txt_requisition_date*cbo_company_name*cbo_location_name*cbo_buyer_name*txt_style_name*txt_quotation_id*txt_style_desc*cbo_product_department*cbo_sub_dept*cbo_brand_id*cbo_season_year*cbo_season_name*txt_item_catgory*txt_buyer_ref*cbo_agent*cbo_client*cbo_team_leader*cbo_dealing_merchant*cbo_factory_merchant*txt_bhmerchant*cbo_design_source_id*cbo_qltyLabel*cbo_quality_level*sustainability_standard*cbo_fab_material*txt_copy_form*txt_material_dlvry_date*txt_qrr_date*txt_est_ship_date*cbo_ready_to_approved*txt_remarks*update_id*sample_fabric_booking_file*txt_internal_ref*cbo_fit_id*txt_control_no*hidden_revised_number',"../../");//alert(data); return;
		
		http.open("POST","requires/sample_requisition_with_booking_controller.php", true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_sample_requisition_mst_info_reponse;
	}

	function fnc_sample_requisition_mst_info_reponse()
	{
		if(http.readyState == 4)
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==0 )
			{
			   show_msg(reponse[0]);
			   $("#txt_requisition_id").val(reponse[1]);
			   $("#cbo_sample_stage").attr('disabled','disabled');
			   $("#cbo_buyer_name").attr('disabled','disabled');
			   $("#update_id").val(reponse[2]);
			   $("#cbo_company_name").attr('disabled','disabled');
			   set_button_status(1, permission, 'fnc_sample_requisition_mst_info',1);
			   uploadFile( $("#update_id").val() );
			   //fileUpload('sample_fabric_booking_file',$("#update_id").val(),'sample_fabric_booking','../../',1);
			   get_php_form_data(reponse[2], "populate_data_from_requisition_search_popup", "requires/sample_requisition_with_booking_controller" );
			}			
			// alert(reponse[0]);
			if(reponse[1]==11 )
			{
				alert("Image Mandatory");
			}
			if(reponse[0]==1 )
			{
				uploadFile( $("#update_id").val() );
				show_msg(reponse[0]);
			}

			if(reponse[0]==10 )
			{
				show_msg(reponse[0]);
			}

			if(reponse[0]==14)
			{
				alert(reponse[1]);release_freezing(); return;
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
		freeze_window(operation);
		var update_id=$('#update_id').val();
		var txtDeltedIdSd=$('#txtDeltedIdSd').val();
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_location_name=$('#cbo_location_name').val();
		if(update_id=="")
		{
			alert("Save Master Part!!");
			release_freezing();
			return;
		}
		else
		{
			var row_num=$('#tbl_sample_details tr').length-1;
			//alert(row_num);hiddenColorid txtisupdated_11
			var data_all=""; var z=1;
			for (var i=1; i<=row_num; i++)
			{
				if (form_validation('cboSampleName_'+i+'*cboGarmentItem_'+i+'*txtColor_'+i+'*txtSampleProdQty_'+i+'*txtSubmissionQty_'+i+'*txtDelvStartDate_'+i+'*txtDelvEndDate_'+i,'Sample Name*Garment Item*Color*Sample Production Qty*Sample Submission Qty*Start Date*End Date')==false)
				{
					release_freezing();
					return;
				}
				//data_all=data_all+get_submitted_data_string('cboSampleName_'+i+'*cboGarmentItem_'+i+'*txtSmv_'+i+'*txtArticle_'+i+'*txtColor_'+i+'*txtSampleProdQty_'+i+'*txtSubmissionQty_'+i+'*txtDelvStartDate_'+i+'*txtDelvEndDate_'+i+'*txtBuyerSubDate_'+i+'*txtRemarks_'+i+'*txtChargeUnit_'+i+'*cboCurrency_'+i+'*updateidsampledtl_'+i+'*hiddenColorid_'+i+'*txtAllData_'+i+'*hiddenadditionalvaluedata_'+i+'*txtisupdated_'+i,"../../");
				
				data_all+="&cboSampleName_" + z + "='" + $('#cboSampleName_'+i).val()+"'"+"&cboGarmentItem_" + z + "='" + $('#cboGarmentItem_'+i).val()+"'"+"&txtSmv_" + z + "='" + $('#txtSmv_'+i).val()+"'"+"&txtArticle_" + z + "='" + $('#txtArticle_'+i).val()+"'"+"&txtColor_" + z + "='" + $('#txtColor_'+i).val()+"'"+"&txtSampleProdQty_" + z + "='" + $('#txtSampleProdQty_'+i).val()+"'"+"&txtSubmissionQty_" + z + "='" + $('#txtSubmissionQty_'+i).val()+"'"+"&txtDelvStartDate_" + z + "='" + $('#txtDelvStartDate_'+i).val()+"'"+"&txtDelvEndDate_" + z + "='" + $('#txtDelvEndDate_'+i).val()+"'"+"&txtBuyerSubDate_" + z + "='" + $('#txtBuyerSubDate_'+i).val()+"'"+"&txtRemarks_" + z + "='" + $('#txtRemarks_'+i).val()+"'"+"&txtChargeUnit_" + z + "='" + $('#txtChargeUnit_'+i).val()+"'"+"&cboCurrency_" + z + "='" + $('#cboCurrency_'+i).val()+"'"+"&updateidsampledtl_" + z + "='" + $('#updateidsampledtl_'+i).val()+"'"+"&hiddenColorid_" + z + "='" + $('#hiddenColorid_'+i).val()+"'"+"&txtAllData_" + z + "='" + $('#txtAllData_'+i).val()+"'"+"&hiddenadditionalvaluedata_" + z + "='" + $('#hiddenadditionalvaluedata_'+i).val()+"'"+"&txtisupdated_" + z + "='" + $('#txtisupdated_'+i).val()+"'";
				z++;
			}

			var data="action=save_update_delete_sample_details&operation="+operation+'&total_row='+row_num+'&update_id='+update_id+'&cbo_company_name='+cbo_company_name+'&cbo_location_name='+cbo_location_name+'&txtDeltedIdSd='+txtDeltedIdSd+data_all;
			//alert(data); release_freezing(); return;
			   
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
			if(reponse[0]==14)
			{
				alert(reponse[2]);
				release_freezing();
				return;
			}

			if(reponse[0]==0)
			{
				show_msg(reponse[0]);
				fnc_load_tr(reponse[1],1);
		 		var upId=document.getElementById("update_id").value;
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_1', 'load_drop_down_required_fabric_sample_name', 'rfSampleId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_1', 'load_drop_down_required_fabric_gmts_item', 'rfItemId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_2', 'load_drop_down_required_fabric_sample_name','raSampleId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_2', 'load_drop_down_required_fabric_gmts_item', 'raItemId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_3', 'load_drop_down_required_fabric_sample_name','reSampleId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_3', 'load_drop_down_required_fabric_gmts_item', 'reItemIid_1');
				$("#cbo_buyer_name").attr('disabled','disabled');
			 }

			if(reponse[0]==1 )
			{
				//alert(reponse[0]);
				show_msg(reponse[0]);
				fnc_load_tr(reponse[1],1);
				var upId=document.getElementById("update_id").value;
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_1', 'load_drop_down_required_fabric_sample_name', 'rfSampleId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_1', 'load_drop_down_required_fabric_gmts_item', 'rfItemId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_2', 'load_drop_down_required_fabric_sample_name','raSampleId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_2', 'load_drop_down_required_fabric_gmts_item', 'raItemId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_3', 'load_drop_down_required_fabric_sample_name','reSampleId_1');
				load_drop_down( 'requires/sample_requisition_with_booking_controller', upId+'_3', 'load_drop_down_required_fabric_gmts_item', 'reItemIid_1');
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
		//alert(222);
		freeze_window(operation);
		var update_id=$('#update_id').val()*1;
		if(update_id=="" || update_id==0)
		{
			alert("Save Master Part!!");
			release_freezing();
			return;
		}
		else
		{
			var row_nums=$('#tbl_required_fabric tr').length-1;
			var data_all=""; var z=1;
			for (var i=1; i<=row_nums; i++)
			{ 
				if (<?php echo $fabric_dia ?>==1) {
					if (form_validation('cboRfSampleName_'+i+'*cboRfGarmentItem_'+i+'*cboRfBodyPart_'+i+'*cboRfFabricNature_'+i+'*txtRfFabricDescription_'+i+'*txtRfGsm_'+i+'*txtRfDia_'+i+'*cboRfColorType_'+i+'*cboRfUom_'+i+'*txtRfReqQty_'+i+'*cboRfWidthDia_'+i+'*txtRfColorAllData_'+i,'Sample Name*Garment Item*Body Part*Fabric Nature*Fabric Desc*Gsm*Dia*Color Type*Uom*ReqQty*Width /Dia*Color')==false)
					{
						release_freezing();
						return;
					}
				}
				else{
					if (form_validation('cboRfSampleName_'+i+'*cboRfGarmentItem_'+i+'*cboRfBodyPart_'+i+'*cboRfFabricNature_'+i+'*txtRfFabricDescription_'+i+'*txtRfGsm_'+i+'*cboRfColorType_'+i+'*cboRfUom_'+i+'*txtRfReqQty_'+i+'*cboRfWidthDia_'+i+'*txtRfColorAllData_'+i,'Sample Name*Garment Item*Body Part*Fabric Nature*Fabric Desc*Gsm*Color Type*Uom*ReqQty*Width /Dia*Color')==false)
					{
						release_freezing();
						return;
					}
				}
				//data_all=data_all+get_submitted_data_string('cboRfSampleName_'+i+'*cboRfGarmentItem_'+i+'*txtProcessLoss_'+i+'*txtGrayFabric_'+i+'*fabricDelvDate_'+i+'*cboRfFabricSource_'+i+'*txtRfRemarks_'+i+'*cboRfBodyPart_'+i+'*cboRfFabricNature_'+i+'*txtRfFabricDescription_'+i+'*txtRfGsm_'+i+'*txtRfDia_'+i+'*txtRfColor_'+i+'*cboRfColorType_'+i+'*cboRfWidthDia_'+i+'*cboRfUom_'+i+'*txtRfReqDzn_'+i+'*txtRfReqQty_'+i+'*updateidRequiredDtl_'+i+'*txtRfColorAllData_'+i+'*libyarncountdeterminationid_'+i+'*hiddencollarCuffdata_'+i+'*txtRfyarndtls_'+i,"../../");
				
				data_all+="&cboRfSampleName_" + z + "='" + $('#cboRfSampleName_'+i).val()+"'"+"&cboRfGarmentItem_" + z + "='" + $('#cboRfGarmentItem_'+i).val()+"'"+"&txtProcessLoss_" + z + "='" + $('#txtProcessLoss_'+i).val()+"'"+"&txtGrayFabric_" + z + "='" + $('#txtGrayFabric_'+i).val()+"'"+"&fabricDelvDate_" + z + "='" + $('#fabricDelvDate_'+i).val()+"'"+"&cboRfFabricSource_" + z + "='" + $('#cboRfFabricSource_'+i).val()+"'"+"&txtRfRemarks_" + z + "='" + $('#txtRfRemarks_'+i).val()+"'"+"&cboRfBodyPart_" + z + "='" + $('#cboRfBodyPart_'+i).val()+"'"+"&cboRfFabricNature_" + z + "='" + $('#cboRfFabricNature_'+i).val()+"'"+"&txtRfFabricDescription_" + z + "='" + $('#txtRfFabricDescription_'+i).val()+"'"+"&txtRfGsm_" + z + "='" + $('#txtRfGsm_'+i).val()+"'"+"&txtRfDia_" + z + "='" + $('#txtRfDia_'+i).val()+"'"+"&txtRfColor_" + z + "='" + $('#txtRfColor_'+i).val()+"'"+"&cboRfColorType_" + z + "='" + $('#cboRfColorType_'+i).val()+"'"+"&cboRfWidthDia_" + z + "='" + $('#cboRfWidthDia_'+i).val()+"'"+"&cboRfUom_" + z + "='" + $('#cboRfUom_'+i).val()+"'"+"&txtRfReqDzn_" + z + "='" + $('#txtRfReqDzn_'+i).val()+"'"+"&txtRfReqQty_" + z + "='" + $('#txtRfReqQty_'+i).val()+"'"+"&updateidRequiredDtl_" + z + "='" + $('#updateidRequiredDtl_'+i).val()+"'"+"&txtRfColorAllData_" + z + "='" + $('#txtRfColorAllData_'+i).val()+"'"+"&libyarncountdeterminationid_" + z + "='" + $('#libyarncountdeterminationid_'+i).val()+"'"+"&hiddencollarCuffdata_" + z + "='" + $('#hiddencollarCuffdata_'+i).val()+"'"+"&txtRfyarndtls_" + z + "='" + $('#txtRfyarndtls_'+i).val()+"'";
				z++;
			}
		
			//var data="action=save_update_delete_required_fabric&operation="+operation+'&total_row='+row_nums+'&update_id='+update_id+'&txt_booking_no='+txt_booking_no+'&cbo_buyer_name='+cbo_buyer_name+'&cbo_location_name='+cbo_location_name+'&cbo_team_leader='+cbo_team_leader+'&cbo_company_name='+cbo_company_name+data_all;
			
			var data="action=save_update_delete_required_fabric&operation="+operation+'&total_row='+row_nums+get_submitted_data_string('update_id*txt_booking_no*cbo_buyer_name*cbo_team_leader*cbo_location_name*cbo_company_name',"../../")+data_all;
			//alert(data);
			
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
			if(reponse[0]==13)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			if(reponse[0]==14)
			{
				alert("This Booking is Approved. So Update or Delete not allowed!!");
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
		freeze_window(operation);
		var update_id=$('#update_id').val();
		var txtDeltedIdRa=$('#txtDeltedIdRa').val();
		if(update_id=="")
		{
			alert("Save Master Part!!");
			release_freezing();
			return;
		}
		else
		{
			var row_num=$('#tbl_required_accessories tr').length-1;

			var data_all=""; var z=1;
			for (var i=1; i<=row_num; i++)
			{
				if (form_validation('cboRaSampleName_'+i+'*cboRaGarmentItem_'+i+'*cboRaTrimsGroup_'+i+'*txtRaDescription_'+i+'*cboRaUom_'+i+'*txtRaReqQty_'+i,'Sample Name*Garment Item*Trims Group*Description*Uom*ReqDzn*ReqQty')==false)
				{
					release_freezing();
					return;
				}
				//data_all=data_all+get_submitted_data_string('cboRaSampleName_'+i+'*cboRaGarmentItem_'+i+'*cboRaFabricSource_'+i+'*accDate_'+i+'*cboRaSupplierName_'+i+'*cboRaTrimsGroup_'+i+'*txtRaDescription_'+i+'*txtRaBrandSupp_'+i+'*cboRaUom_'+i+'*txtRaReqDzn_'+i+'*txtRaReqQty_'+i+'*txtRaRemarks_'+i+'*updateidAccessoriesDtl_'+i,"../../");
				
				data_all+="&cboRaSampleName_" + z + "='" + $('#cboRaSampleName_'+i).val()+"'"+"&cboRaGarmentItem_" + z + "='" + $('#cboRaGarmentItem_'+i).val()+"'"+"&cboRaFabricSource_" + z + "='" + $('#cboRaFabricSource_'+i).val()+"'"+"&accDate_" + z + "='" + $('#accDate_'+i).val()+"'"+"&cboRaSupplierName_" + z + "='" + $('#cboRaSupplierName_'+i).val()+"'"+"&cboRaTrimsGroup_" + z + "='" + $('#cboRaTrimsGroup_'+i).val()+"'"+"&txtRaDescription_" + z + "='" + $('#txtRaDescription_'+i).val()+"'"+"&txtRaBrandSupp_" + z + "='" + $('#txtRaBrandSupp_'+i).val()+"'"+"&cboRaUom_" + z + "='" + $('#cboRaUom_'+i).val()+"'"+"&txtRaReqDzn_" + z + "='" + $('#txtRaReqDzn_'+i).val()+"'"+"&txtRaReqQty_" + z + "='" + $('#txtRaReqQty_'+i).val()+"'"+"&txtRaRemarks_" + z + "='" + $('#txtRaRemarks_'+i).val()+"'"+"&updateidAccessoriesDtl_" + z + "='" + $('#updateidAccessoriesDtl_'+i).val()+"'";
				z++;
			}
			//var data="action=save_update_delete_required_accessories&operation="+operation+'&total_row='+row_num+'&update_id='+update_id+'&txtDeltedIdRa='+txtDeltedIdRa+data_all;
			
			var data="action=save_update_delete_required_accessories&operation="+operation+'&total_row='+row_num+get_submitted_data_string('update_id*txtDeltedIdRa*cbo_buyer_name*cbo_team_leader*cbo_location_name*cbo_company_name',"../../")+data_all;
			
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

	function fnc_required_embellishment_info( operation )
	{
		freeze_window(operation);
		var update_id=$('#update_id').val();
		
		if(update_id=="")
		{
			alert("Save Master Part!!");
			release_freezing();
			return;
		}
		else
		{
			var row_num=$('#tbl_required_embellishment tr').length-1;

			var data_all=""; var z=1;
			for (var i=1; i<=row_num; i++)
			{
				if (form_validation('cboReSampleName_'+i+'*cboReGarmentItem_'+i+'*cboReName_'+i+'*cboReType_'+i,'Sample Name*Garment Item*Name*Type')==false)
				{
					release_freezing();
					return;
				}
				//data_all=data_all+get_submitted_data_string('cboReSampleName_'+i+'*cboReGarmentItem_'+i+'*cboReName_'+i+'*cboReType_'+i+'*txtReRemarks_'+i+'*cboReSupplierName_'+i+'*cboReBodyPart_'+i+'*deliveryDate_'+i+'*txtReQty_'+i+'*txtReRate_'+i+'*txtReAmount_'+i+'*txtcolorBreakdown_'+i+'*updateidRequiredEmbellishdtl_'+i,"../../");
				
				data_all+="&cboReSampleName_" + z + "='" + $('#cboReSampleName_'+i).val()+"'"+"&cboReGarmentItem_" + z + "='" + $('#cboReGarmentItem_'+i).val()+"'"+"&cboReName_" + z + "='" + $('#cboReName_'+i).val()+"'"+"&cboReType_" + z + "='" + $('#cboReType_'+i).val()+"'"+"&txtReRemarks_" + z + "='" + $('#txtReRemarks_'+i).val()+"'"+"&cboReSupplierName_" + z + "='" + $('#cboReSupplierName_'+i).val()+"'"+"&cboReBodyPart_" + z + "='" + $('#cboReBodyPart_'+i).val()+"'"+"&deliveryDate_" + z + "='" + $('#deliveryDate_'+i).val()+"'"+"&txtReQty_" + z + "='" + $('#txtReQty_'+i).val()+"'"+"&txtReRate_" + z + "='" + $('#txtReRate_'+i).val()+"'"+"&txtReAmount_" + z + "='" + $('#txtReAmount_'+i).val()+"'"+"&txtcolorBreakdown_" + z + "='" + $('#txtcolorBreakdown_'+i).val()+"'"+"&updateidRequiredEmbellishdtl_" + z + "='" + $('#updateidRequiredEmbellishdtl_'+i).val()+"'";
				z++;
			}
			//txtReQty_ txtReRate_ txtReAmount_ 
			//var data="action=save_update_delete_required_embellishment&operation="+operation+'&total_row='+row_num+'&update_id='+update_id+'&txtDeltedIdRe='+txtDeltedIdRe+data_all;
			var data="action=save_update_delete_required_embellishment&operation="+operation+'&total_row='+row_num+get_submitted_data_string('update_id*txtDeltedIdRe*cbo_buyer_name*cbo_team_leader*cbo_location_name*cbo_company_name',"../../")+data_all;
			
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
	function get_smv_value(id,value){
		var jobId=$('#txt_quotation_id').val();
		var data=jobId+'**'+value;
		var smv_value = return_global_ajax_value( data, 'get_smv_value', '', 'requires/sample_requisition_with_booking_controller');
		$("#txtSmv_"+id).val(smv_value);
	}

	function fnc_load_tr(up_id,type) //after save of details part or browse requisition this function load all saved data from db in specific tr
	{
		var sampleStage=$('#cbo_sample_stage').val();
		var jobId=$('#txt_quotation_id').val();
		var companyId=$('#cbo_company_name').val();
		//var update_id=$('#update_id').val();
		
		if(type==1)//Sample Details
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
			var data=up_id+'**'+type+'**'+sampleStage+'**'+jobId+'**'+companyId;
			//alert(up_id);
			if(up_id!="")
			{
				var is_updated_found = return_global_ajax_value( data, 'get_update_found', '', 'requires/sample_requisition_with_booking_controller');
				if(is_updated_found==1)
				{
					fnc_show_msg_details_info(type);
				}
			}
		
			var list_view_tr = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sample_requisition_with_booking_controller');
			// alert(list_view_tr);
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
		else  if(type==2)//Fabric
		{
			var data=up_id+'**'+type+'**'+sampleStage+'**'+jobId;
			if(up_id!="")
			{
				var is_updated_found = return_global_ajax_value( data, 'get_update_found', '', 'requires/sample_requisition_with_booking_controller');
				$('#fab_dtls_td').html('');
			
				if(is_updated_found==1)
				{
					fnc_show_msg_details_info(type);
				}
			}
			
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
		else if(type==3)//Accessories
		{
			var data=up_id+'**'+type+'**'+sampleStage+'**'+jobId;
			 //$("#tbl_required_accessories tbody > tr").remove();
			if(up_id!="")
			{
				var is_updated_found = return_global_ajax_value( data, 'get_update_found', '', 'requires/sample_requisition_with_booking_controller');
				$('#acc_dtls_td').html('');
			
				if(is_updated_found==1)
				{
					fnc_show_msg_details_info(type);
				}
			}

			var list_view_tr = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sample_requisition_with_booking_controller');
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
				var save_update = return_global_ajax_value( data, 'check_save_update', '', 'requires/sample_requisition_with_booking_controller');
				//alert(save_update);
				set_button_status(save_update, permission, 'fnc_required_accessories_info',4,0);
				return;
			}
		}
		else if(type==4)//Embellishment
		{
			var data=up_id+'**'+type+'**'+sampleStage+'**'+jobId;
			if(up_id!="")
			{
				var is_updated_found = return_global_ajax_value( data, 'get_update_found', '', 'requires/sample_requisition_with_booking_controller');
				$('#emb_dtls_td').html('');
				if(is_updated_found==1)
				{
					fnc_show_msg_details_info(type);
				}
			}
			
			var list_view_tr = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sample_requisition_with_booking_controller');
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
				var save_update = return_global_ajax_value( data, 'check_save_update', '', 'requires/sample_requisition_with_booking_controller');
				set_button_status(save_update, permission, 'fnc_required_embellishment_info',5,0);
				return;
			}
		}
	}

	function openmypage_sizeinfo(page_link,title,inc) // this function for sample details- sample qty browse
	{
		var txt_style_id = $('#update_id').val();
		var update_id_dtl = $('#updateidsampledtl_'+inc).val();
		var hiddenColorid = $('#hiddenColorid_'+inc).val();
		if (txt_style_id=='')
		{
			alert("Save Master Part First!!");
			return;
		}
		else
		{
			get_php_form_data(update_id_dtl, "load_data_to_sizeinfo","requires/sample_requisition_with_booking_controller" );
			var data=$('#txtAllData_'+inc).val();
			var title = 'Size Info';
			var page_link = page_link +'&data='+data+'&update_id_dtl='+update_id_dtl+'&hiddenColorid='+hiddenColorid;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=930px,height=400px,center=1,resize=1,scrolling=0','../');
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
		var page_link='requires/sample_requisition_with_booking_controller.php?action=fabric_description_popup&fabric_nature='+cbofabricnature+'&libyarncountdeterminationid='+libyarncountdeterminationid;
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
			$('#cboGarmentItem_' + i).removeAttr("onchange").attr("onchange", "get_smv_value(" + i + ",this.value);");
			$('#txtColor_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_color_size('requires/sample_requisition_with_booking_controller.php?action=color_popup','Color Search','1','"+i+"');");
			$('#txtSampleProdQty_'+i).removeAttr("onfocus");
			$('#txtSampleProdQty_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_sizeinfo('requires/sample_requisition_with_booking_controller.php?action=sizeinfo_popup','Size Search','"+i+"')");
			$('#txtFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../',document.getElementById('updateidsampledtl_"+i+"').value,'', 'sample_details_1', 0 ,1);");
			$('#txtRemarks_'+i).removeAttr("onClick").attr("onClick","openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','1','"+i+"');");
	
			 $('#cboSampleName_'+i).removeAttr("disabled",true);//issue id==21845
			 $('#cboGarmentItem_'+i).removeAttr("disabled",true);//issue id==21845
			//$('#txtSampleProdQty_'+i).removeAttr("disabled","");
			//$('#txtAllData_'+i).removeAttr("disabled","");
			//$('#txtArticle_'+i).removeAttr("disabled","");
			//$('#txtSmv_'+i).removeAttr("disabled","");
			//$('#txtColor_'+i).removeAttr("disabled","");
			//$('#txtSubmissionQty_'+i).removeAttr("disabled","");
			//$('#txtDelvStartDate_'+i).removeAttr("class").attr("class","datepicker");
			//$('#txtDelvEndDate_'+i).removeAttr("class").attr("class","datepicker");
			//$('#txtBuyerSubDate_'+i).removeAttr("class").attr("class","datepicker");
			$('#increase_'+i).removeAttr("value").attr("value","+");
			$('#decrease_'+i).removeAttr("value").attr("value","-");
			$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
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
					/* $('#cboSampleName_'+i).removeAttr("disabled","");
					$('#cboGarmentItem_'+i).removeAttr("disabled","");
					$('#txtSampleProdQty_'+i).removeAttr("disabled","");
					$('#txtAllData_'+i).removeAttr("disabled","");
					$('#txtArticle_'+i).removeAttr("disabled","");
					$('#txtSmv_'+i).removeAttr("disabled","");
					$('#txtColor_'+i).removeAttr("disabled","");
					$('#txtSubmissionQty_'+i).removeAttr("disabled",""); */
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
		if(rowNo==1 && numRow==1)
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
				$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+",this);");
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
			if (!$('#chkBoxrequiredfabric').is(":checked"))
			{
				$('#txtRfColorAllData_'+i).val('');
				$('#txtRfReqQty_'+i).val('');
				$('#txtGrayFabric_'+i).val('');
				$('#cboRfSampleName_'+i).val('');
				$('#cboRfGarmentItem_'+i).val('');
				$('#cboRfBodyPart_'+i).val('');
				$('#txtRfFabricDescription_'+i).val('');
				$('#txtRfGsm_'+i).val('');
				$('#txtRfDia_'+i).val('');
				$('#txtRfColor_'+i).val('');
				$('#cboRfColorType_'+i).val('');
				$('#cboRfWidthDia_'+i).val('');
				$('#cboRfFabricSource_'+i).val('');
				$('#txtProcessLoss_'+i).val('');
				$('#txtGrayFabric_'+i).val('');
				$('#cboRfFabricNature_'+i).val('');
			}
				$('#updateidRequiredDtl_'+i).val('');
				$('#cboRfUom_'+i).val($('#cboRfUom_'+k).val());
				$('#txtRfReqDzn_'+i).val('');
				$('#txtMemoryDataRf_'+i).val('');
			

			$('#txtRfReqDzn_'+i).removeAttr("onblur").attr("onblur","calculate_required_qty('1','"+i+"')");
			$('#cboRfBodyPart_'+i).removeAttr("onchange").attr("onchange","load_data_to_rfcolor('"+i+"')");
			$('#txtProcessLoss_'+i).removeAttr("onchange").attr("onchange","calculate_requirement('"+i+"')");
			$('#cboRfSampleName_'+i).removeAttr("onchange").attr("onchange","sample_wise_item('"+mst_id+"',this.value,'"+i+"',1)");
 			$('#txtRfColor_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_rf_color('requires/sample_requisition_with_booking_controller.php?action=color_popup_rf','Color Search','"+i+"');");
 			$('#txtRfRemarks_'+i).removeAttr("onClick").attr("onClick","openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','2','"+i+"');");
			$('#txtRfyarndtls_'+i).removeAttr("onClick").attr("onClick","openmypage_remarks2('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Yarn','2','"+i+"');");

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
		if( rowNo==1 && numRow==1)
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
			$('#cboRaUom_'+i).val($('#cboRaUom_'+k).val());
			$('#txtRaReqDzn_'+i).val('');
			$('#txtRaReqQty_'+i).val('');
			$('#txtRaRemarks_'+i).val('');
			$('#txtMemoryDataRa_'+i).val('');

			//$('#txtRaReqDzn_'+i).removeAttr("onblur").attr("onblur","calculate_required_qty('2','"+i+"')");
			$('#cboRaTrimsGroup_'+i).removeAttr("onchange").attr("onchange","load_uom_for_trims('"+i+"',this.value);");
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
			$('#cboReName_'+i).val('');
			$('#cboReType_'+i).val('');
			$('#txtReRemarks_'+i).val('');
			
			$('#txtReQty_'+i).val('');
			$('#txtReRate_'+i).val('');
			$('#txtReAmount_'+i).val('');
			$('#txtcolorBreakdown_'+i).val('');
			
			$('#txtReRemarks_'+i).removeAttr("onClick").attr("onClick","openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','4','"+i+"');");
			
			$('#txtReQty_'+i).removeAttr("onClick").attr("onClick","open_consumption_popup('requires/sample_requisition_with_booking_controller.php?action=consumption_popup','Consumtion Entry Form','"+i+"');");
			
			$('#reTxtFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../', document.getElementById('updateidRequiredEmbellishdtl_"+i+"').value,'', 'required_embellishment_1', 0 ,1);");
			$('#cboReSampleName_'+i).removeAttr("onchange").attr("onchange","sample_wise_item('"+mst_id+"',this.value,'"+i+"',3)");
			$('#deliveryDate_'+i).removeAttr("class").attr("class","datepicker");
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
		load_drop_down( 'requires/sample_requisition_with_booking_controller', cboembname+'_'+i, 'load_drop_down_emb_type', 'reType_'+i );
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
			var cbo_company_name=$('#cbo_company_name').val();
			var title = 'Style ID Search';
			var page_link = 'requires/sample_requisition_with_booking_controller.php?&action=style_id_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&cbo_company_name='+cbo_company_name, title, 'width=1000px,height=400px,center=1,resize=1,scrolling=0','../');
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
		if(sample_stage==2)
		{
			var company = $("#cbo_company_name").val();
			var page_link='requires/sample_requisition_with_booking_controller.php?action=inquiry_popup&company='+company;
			var title="Search  Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=980px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var inquiryId=this.contentDoc.getElementById("txt_inquiry_id").value; // mrr number
				get_php_form_data(inquiryId, "populate_data_from_inquiry_search", "requires/sample_requisition_with_booking_controller");
				$("#cbo_company_name").attr('disabled','disabled');
				$("#cbo_buyer_name").attr('disabled','disabled');
				$("#cbo_company_name").attr('disabled','disabled');
				$("#cbo_dealing_merchant").attr('disabled','disabled');
	 			//$("#cbo_agent").attr('disabled','disabled');
	 			$("#txt_est_ship_date").attr('disabled','disabled');
	 		}
	 	}
	}
	
	function openmypage_remarks2(page_link,title,type,inc)
	{
		if(type==2) //for required fabric
		{
			var remarks=$("#txtRfyarndtls_"+inc).val();
			var page_link = page_link + "&remarks="+remarks;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=370px,height=250px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var txt_remarks=this.contentDoc.getElementById("txt_remarks").value;
//Remarks
				if (txt_remarks!="" &&  title=='Yarn')
				{
					$("#txtRfyarndtls_"+inc).val( txt_remarks );
				}
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
//Remarks
				if (txt_remarks!=""  ||  1==1)
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

		if(type==4) // for emb
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
		if(sampleStage==1 || sampleStage==2 || sampleStage==3)
		{
			var style_db_id=document.getElementById('txt_quotation_id').value;
			if(type==1) // for sample details color
			{
				var page_link = page_link + "&style_db_id="+style_db_id+ "&cbo_buyer_name="+cbo_buyer_name+ "&sampleStage="+sampleStage;
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=290px,height=400px,center=1,resize=1,scrolling=0','../');
				emailwindow.onclose=function()
				{
					freeze_window(5);
					var colordata=this.contentDoc.getElementById("color_data").value;
					if(colordata!="")
					{
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
					}
					release_freezing();
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

	function openmypage_rf_color(page_link,title,inc)
	{
		var sampleName=$('#cboRfSampleName_'+inc).val();
		var company=$('#cbo_company_name').val();
		var garmentItem=$('#cboRfGarmentItem_'+inc).val();
		var mainId=$('#update_id').val();
		var dtlId=$('#updateidRequiredDtl_'+inc).val();
		var data=$('#txtRfColorAllData_'+inc).val();
		var page_link = page_link + "&sampleName="+sampleName+ "&garmentItem="+garmentItem+'&data='+data+'&mainId='+mainId+'&dtlId='+dtlId+'&company='+company;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=250px,center=1,resize=1,scrolling=0','../');
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
			  //document.write ("User does not want to continue!");
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&cbo_company_name='+cbo_company_name, title, 'width=1070px,height=400px,center=1,resize=1,scrolling=0','../');
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
				//tbl_required_embellishment
				reset_form('content_required_embellishment*content_required_accessories*content_required_fabric*content_sample_details','','','','');
				release_freezing();
				$('#content_sample_details').hide();
				$('#content_required_fabric').hide();
				$('#content_required_accessories').hide();
				$('#content_required_embellishment').hide();
				$("#cbo_sample_stage").attr('disabled','disabled');
				if(booking_no!='')
				{
					 get_php_form_data( booking_no, "populate_booking_data_from_search_popup", "requires/sample_requisition_with_booking_controller" );
				}
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
		$('#cboRaUom_'+inc).attr('disabled','disabled');
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
	}

	function fnc_show_acknowledge() {
     	show_list_view('', 'show_acknowledge', 'list_acknowledge', 'requires/sample_requisition_with_booking_controller', 'setFilterGrid(\'tbl_list_search\',-1);');
    }

	 function btn_load_acknowledge()
	 {
		 var buyer=$("#cbo_buyer_name").val();
		 if(buyer!=0)
		 {
			 load_drop_down( 'requires/sample_requisition_with_booking_controller', buyer, 'load_drop_down_season_buyer', 'season_td');
			 load_drop_down( 'requires/sample_requisition_with_booking_controller', buyer, 'load_drop_down_season_buyer', 'season_td');
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
		if(cbo_currercy!=0 && booking_date!="")
		{
			var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/sample_requisition_with_booking_controller');
			var response=response.split("_");
			$('#txt_exchange_rate').val(response[1]);
		}
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
				//alert(cons_req.value);
				//var json_data=this.contentDoc.getElementById("json_data");
				document.getElementById('txtcolorBreakdown_'+i).value=cons_break_down.value;
				document.getElementById('txtReQty_'+i).value=cons_req.value;
				document.getElementById('txtReRate_'+i).value=rate.value;
				document.getElementById('txtReAmount_'+i).value=amount.value;
				//document.getElementById('jsondata_'+i).value=json_data.value;
				//calculate_amount(i)
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
		$('#cbo_dealing_merchant_book').attr('disabled','disabled');
		//$('#cbo_team_leader_book').attr('disabled','disabled');
	}
	
	function sub_dept_load(cbo_buyer_name,cbo_product_department)
	{
		if(cbo_buyer_name ==0 || cbo_product_department==0 )
		{
			return;
		}
		else
		{
			load_drop_down( 'requires/sample_requisition_with_booking_controller',cbo_buyer_name+'_'+cbo_product_department, 'load_drop_down_sub_dep', 'sub_td' );
		}
	}
	
	function openpage_collarCuff(i) {
    	var bodypartid = $('#cboRfBodyPart_'+i).val();
    	var bodyparttype =return_global_ajax_value( bodypartid, 'body_part_type', '', 'requires/sample_requisition_with_booking_controller');
    	if(bodyparttype==40 || bodyparttype==50)
    	{
    		var sampleid = $('#cboRfSampleName_'+i).val();
    		var update_dtls_id = $('#updateidRequiredDtl_'+i).val();
    		var collarCuff_data = $('#hiddencollarCuffdata_'+i).val();
    		var mst_id = $('#update_id').val();
	    	if (sampleid == "") {
	    		alert("Please Select Sample Name");
	    		return;
	    	}
	    	var page_link = 'requires/sample_requisition_with_booking_controller.php?action=collarCuff_info_popup&bodypartid=' + bodypartid + '&bodyparttype='+bodyparttype+'&update_dtls_id=' + update_dtls_id +'&sampleid='+sampleid+'&updateId='+mst_id+'&collarCuff_data='+collarCuff_data;
	    	var title = 'Collar and Cuff Measurement Info';

	    	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=570px,height=300px,center=1,resize=1,scrolling=0', '../');
	    	emailwindow.onclose = function () {
	    		var theform = this.contentDoc.forms[0];
	    		var hidden_collarCuff_data = this.contentDoc.getElementById("hidden_collarCuff_data").value;

	    		$('#hiddencollarCuffdata_'+i).val(hidden_collarCuff_data);
	    	}
    	}
    	else{
    		return;
    	}
    }
	
    function openpage_additionalvalue(i) {
    	var sampleid = $('#cboSampleName_'+i).val();
		var update_dtls_id = $('#updateidsampledtl_'+i).val();
		var additionalvalue_data = $('#hiddenadditionalvaluedata_'+i).val();
		var mst_id = $('#update_id').val();
    	if (sampleid == "") {
    		alert("Please Select Sample Name");
    		return;
    	}
    	var page_link = 'requires/sample_requisition_with_booking_controller.php?action=additional_value_popup&update_dtls_id=' + update_dtls_id +'&sampleid='+sampleid+'&updateId='+mst_id+'&additionalvalue_data='+additionalvalue_data;
    	var title = 'Additional Value Info';

    	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=930px,height=130px,center=1,resize=1,scrolling=0', '../');
    	emailwindow.onclose = function () {
    		var theform = this.contentDoc.forms[0];
    		var hidden_additional_value_data = this.contentDoc.getElementById("hidden_additional_value_data").value;

    		$('#hiddenadditionalvaluedata_'+i).val(hidden_additional_value_data);
    	}    	
    }

	function dtm_popup(page_link,title)
	{
		var req_no=$('#txt_requisition_id').val();
		var update_id=$('#update_id').val();
		//alert(req_id);
		var booking_no=$('#txt_booking_no').val();
	 //	var selected_no=0;
	
		if(booking_no=='')
		{
			alert('Booking  Not Found.');
			$('#txt_booking_no').focus();
			return;
		}
	
		page_link=page_link+'&req_no='+req_no+'&booking_no='+booking_no+'&update_id='+update_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=1,scrolling=0','../')
	}
	
	function open_rmg_process_loss_popup(page_link,title)
	{
		var txt_processloss_breck_down=document.getElementById('txt_processloss_breck_down').value
		page_link=page_link+'&txt_processloss_breck_down='+txt_processloss_breck_down;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=230px,height=230px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("txt_processloss_breck_down");
			if (theemail.value!="")
			{
				document.getElementById('txt_processloss_breck_down').value=theemail.value;
			}
		}
	}

	function uploadFile(mst_id)
	{
		$(document).ready(function() { 
			 
			var suc=0;
			var fail=0;
			for( var i = 0 ; i < $("#multiple_file_field")[0].files.length ; i++)
			{
				var fd = new FormData();
				console.log($("#multiple_file_field")[0].files[i]);
				var files = $("#multiple_file_field")[0].files[i]; 
				fd.append('file', files); 
				$.ajax({ 
					url: 'requires/sample_requisition_with_booking_controller.php?action=file_upload&mst_id='+ mst_id, 
					type: 'post', 
					data:fd, 
					contentType: false, 
					processData: false, 
					success: function(response){ 
						var res=response.split('**');
						if(res[0] == 0){ 
							suc++;
						}
						else if(fail==0)
						{
							alert('Image not uploaded');
							fail++;
						}  
					}, 
				}); 
			}

			if(suc > 0 )
			{
				 document.getElementById('multiple_file_field').value='';
			}
		}); 
	}
	
	function fnc_show_msg_details_info(type)
	{
		$('#sample_dtls_td').html('');
		$('#fab_dtls_td').html('');
		$('#acc_dtls_td').html('');
		$('#emb_dtls_td').html('');
		
		if(type==1)
		{
			$('#sample_dtls_td').html('Sample part changed found, Please synchronize all next part.');
		}
		if(type==2)
		{
			$('#fab_dtls_td').html('Sample part changed found, Please synchronize.');
		}
		if(type==3)
		{
			$('#acc_dtls_td').html('Sample part changed found, Please synchronize.');
		}
		if(type==4)
		{
			$('#emb_dtls_td').html('Sample part changed found, Please synchronize.');
		}
	}
	
	function fnc_get_company_config(company_id)
	{
		get_php_form_data(company_id,'get_company_config','requires/sample_requisition_with_booking_controller' );
		location_select();
		get_php_form_data(company_id, "company_wise_report_button_setting", "requires/sample_requisition_with_booking_controller");

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
		
		sub_dept_load(buyer_id,document.getElementById('cbo_product_department').value);
	}

	function call_print_button_for_mail(mail,mail_body,type){
		 var booking_id=$('#txt_selected_id').val();
		 var txt_alter_user_id=$('#txt_alter_user_id').val();
		 var cbo_company_id=$('#cbo_company_id').val();
		 var sysIdArr=booking_id.split(',');
		 var mail=mail.split(',');
		 var ret_data=return_global_ajax_value(sysIdArr.join(',')+'__'+mail.join(',')+'__'+txt_alter_user_id+'__'+cbo_company_id+'__'+type, 'app_mail_notification', '', 'requires/dyeing_work_order_approval_controller');
		 //alert(ret_data);
	}


	//function sendMail()
	function call_print_button_for_mail(mail, mail_body, type)
	{ 
		var mail=mail.split(',');
		// var mail = $("#txt_mail_address").val();
		// alert(mail);
		// alert(222);
		var comp = $('#cbo_company_name').val();
		var txt_booking_no=$('#txt_booking_no').val();
		//if(confirm("Do you want to send mail?")==false){return;}
		
		if (form_validation('txt_requisition_id', 'System Id')==false)
		{
			return;
		} 
		//var action="sample_requisition_print";
		var is_mail_send = 1;
		var action="sample_requisition_print"; 
		var is_mail_send = 1;
		var data="action="+action+"&is_mail_send="+is_mail_send+"&mail="+mail+get_submitted_data_string('txt_booking_no*cbo_company_name*update_id*cbo_template_id',"");
		http.open("POST","requires/sample_requisition_with_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_mai_send_response;
	}
	function fnc_mai_send_response(){
		if(http.readyState == 4) 
		{
			var date = $("#date_value").val();
			alert("****Mail Sent.---"+date);
		}
	}

	function fnc_get_buyer_config(buyer_id)
	{
		//sub_dept_load(buyer_id,document.getElementById('cbo_product_department').value);
		//check_tna_templete(buyer_id);
		get_php_form_data(buyer_id+'*'+1,'get_buyer_config','requires/sample_requisition_with_booking_controller' );
		//set_field_level_access(document.getElementById("cbo_company_name").value);
	}
</script>
</head>
<body onLoad="set_hotkey(); btn_load_acknowledge();check_exchange_rate();">
	<div style="width:100%;">
		<?=load_freeze_divs ("../../",$permission); ?>
		<form name="sample_requisition_1" id="sample_requisition_1">
			<fieldset style="width:1340px;">
				<legend>Sample Requisition</legend>
				<div style="width:1050px; float:left;" align="center">
                    <table cellpadding="1" cellspacing="2" width="1050">
                        <tr>
                            <td colspan="4" align="right">Requisition ID</td>
                            <td colspan="4"><input type="text" name="txt_requisition_id" id="txt_requisition_id" class="text_boxes" style="width: 140px;margin-right: 38px;" placeholder="Requisition Id" readonly onDblClick="openmypage_requisition();" > </td>
							<input type="hidden" id="hidden_revised_number" name="hidden_revised_number" value=""/>
							<Input type="hidden" name="date_value" class="text_boxes" id="date_value" value="<?= date("Y-m-d h:i:s a");?>" style="width:30px" />
                        </tr>
                        <tr>
                            <td width="110" class="must_entry_caption">Sample Stage</td>
                            <td width="150"><?=create_drop_down( "cbo_sample_stage", 130, $sample_stage, "", 1, "-- Select Stage --", $selected, "fnc_browse_style(this.value);", "", "" ); ?> </td>
                            <td width="110" class="must_entry_caption">Requisition Date</td>
                            <td width="150"><input name="txt_requisition_date" id="txt_requisition_date" class="datepicker" type="text" value="<? echo date('d-m-Y')?>" style="width:120px;"  /></td>
                            <td width="110" class="must_entry_caption">Company Name</td>
                            <td width="150"><?=create_drop_down( "cbo_company_name", 130, "select comp.id,comp.company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_get_company_config(this.value);  " ); 
							/*load_drop_down( 'requires/sample_requisition_with_booking_controller', this.value, 'load_drop_down_location', 'location_td' );
							load_drop_down( 'requires/sample_requisition_with_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );
							load_drop_down( 'requires/sample_requisition_with_booking_controller', this.value, 'load_drop_down_agent', 'agent_td' );
							load_drop_down( 'requires/sample_requisition_with_booking_controller', this.value, 'load_drop_down_party_type', 'party_type_td' );*/
							?></td>
                            <td width="110" class="must_entry_caption">Location</td>
                            <td id="location_td"><? echo create_drop_down( "cbo_location_name", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 $location_credential_cond order by location_name",'id,location_name', 1, '-Select Location-', 0, "" ); ?></td>
                        </tr>
                        <tr>							
                            <td class="must_entry_caption">Buyer Name</td>
                            <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "fnc_get_buyer_config(this.value);" ); ?></td>							
                            <td class="must_entry_caption">Style Ref</td>
                            <td>
                                <input name="txt_style_name" id="txt_style_name" class="text_boxes" type="text" value="" style="width:120px;" placeholder="Browse" onDblClick="check_sample_stage();" onChange="document.getElementById('txt_quotation_id').value='';document.getElementById('txt_quotation_job_no').value='';" readonly /> 
                                <input type="hidden" id="txt_quotation_id" name="txt_quotation_id" class="text_boxes" style="width:30px;">
                                <input type="hidden" id="txt_quotation_job_no" name="txt_quotation_job_no" class="text_boxes" style="width:30px;">
                                <input type="hidden" name="update_id" id="update_id" value="">
                                <input type="hidden" name="hidd_variable_data" id="hidd_variable_data" value="">
                            </td>
                            <td>Style Desc./Req. No</td>
                            <td><input type="text" name="txt_style_desc" id="txt_style_desc" class="text_boxes" style="width:120px;"></td>
                            <td class="must_entry_caption">Product. Dept</td>
                            <td><?=create_drop_down( "cbo_product_department", 130,$product_dept ,'', 1, '--- Select Department ---', 0, "sub_dept_load(document.getElementById('cbo_buyer_name').value,this.value);"); ?></td>
                        </tr>
                        <tr>
                            <td>Sub. Dept </td>
                            <td id="sub_td"><? echo create_drop_down( "cbo_sub_dept", 130, $blank_array,"", 1, "-- Select Sub Dep --", $selected, "" ); ?></td>							
                            <td>Brand</td>
                            <td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 130, $blank_array,'', 1, "--Brand--",$selected, "" ); ?>
                            <td class="must_entry_caption">Season<? echo create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                            <td id="season_td"><? echo create_drop_down( "cbo_season_name", 130, $blank_array,"", 1, "-- Select Season --", $selected, "" ); ?></td>
                            <td class="must_entry_caption">Product Category</td>
                            <td><? echo create_drop_down( "txt_item_catgory", 130, $product_category,"", 1, "-- Select Product Category --", 1, "","","" ); ?></td>							
                        </tr>
                        <tr>
                            <td>Buyer Ref</td>
                            <td><input type="text" name="txt_buyer_ref" id="txt_buyer_ref" class="text_boxes" style="width:120px;"></td>
                            <td>Agent Name</td>
                            <td id="agent_td"><? echo create_drop_down( "cbo_agent", 130, $blank_array,"", 1, "-- Select Agent --", $selected, "" ); ?></td>
                            <td>Client</td>
                            <td id="party_type_td"><? echo create_drop_down( "cbo_client", 130, $blank_array,"", 1, "-- Select Client --", $selected, "" ); ?></td>
                            <td>Design Source</td>
                            <td><? echo create_drop_down( "cbo_design_source_id", 130, $design_source_arr,"", 1, "-- Select --", "", "" ); ?></td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Team Leader</td>
                            <td id="div_teamleader"><? echo create_drop_down( "cbo_team_leader", 130, "select id,team_leader_name from lib_marketing_team where project_type=1 and team_type in (0,1,2) and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'requires/sample_requisition_with_booking_controller', this.value, 'cbo_dealing_merchant', 'div_marchant' );load_drop_down( 'requires/sample_requisition_with_booking_controller', this.value, 'cbo_factory_merchant', 'div_marchant_factory' );" ); ?></td>
                            <td class="must_entry_caption">Dealing Merchant </td>
                            <td id="div_marchant" ><? echo create_drop_down( "cbo_dealing_merchant", 130, $blank_array,"", 1, "-- Select Team Member --", $selected, "fnc_fab_marchd()" ); ?></td>
							<td class="must_entry_caption">Factory Merchant</td>   
                        <td id="div_marchant_factory"><? echo create_drop_down( "cbo_factory_merchant", 130, $blank_array,"", 1, "-- Select Team Member --", $selected, "fnc_fab_marchd()" ); ?></td>
                            <td> BH Merchandiser</td>
                            <td><input class="text_boxes" type="text" style="width:120px;"  name="txt_bhmerchant" id="txt_bhmerchant"/></td>
                        </tr>
                        <tr>
                            <td>Quality Label</td>
                            <td><? echo create_drop_down( "cbo_qltyLabel", 130, $quality_label,"", 1, "--Quality Label--", $selected, "" ); ?></td>
                            <td>Order Nature</td>
                            <td><? echo create_drop_down( "cbo_quality_level", 130, $fbooking_order_nature,"", 1, "-- Select--", 0, "","","" ); ?></td>
                            <td>Sustainability Standard</td>
                            <td>
								<? 
                                $sustainability_standard=array(1=>"GOTS",2=>"OCS",3=>"BCI",4=>"GRS",5=>"C2C",6=>"SUPIMA",7=>"Others",8=>"Conventional");
                                echo create_drop_down( "sustainability_standard", 130, $sustainability_standard,"", 1, "-- Select--", 0, "","","" ); 
                                ?>
                            </td>
                            <td>Fab. Material</td>
                            <td>
								<? 
                                $fab_material=array(1=>"Organic",2=>"BCI");
                                echo create_drop_down( "cbo_fab_material", 130, $fab_material,"", 1, "-- Select--", 0, "","","" ); 
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Copy From</td>
                            <td><input class="text_boxes" type="text" style="width:120px;" name="txt_copy_form" id="txt_copy_form" disabled/></td>
                            <td>Material Deli.Date</td>
                            <td><input name="txt_material_dlvry_date" id="txt_material_dlvry_date" class="datepicker" type="text" value="" style="width:120px;" /></td>
                            <td>Est. Ship Date</td>
                            <td><input name="txt_est_ship_date" id="txt_est_ship_date" class="datepicker" type="text" value="" style="width:120px; text-align:left" /></td>
                            <td>QRR Date</td>
                            <td><input name="txt_qrr_date" id="txt_qrr_date" class="datepicker" type="text" value="<? //echo date('d-m-Y')?>" style="width:120px;"  /></td>
                        </tr>
                        <tr>
                        	<td>Int. Ref/Control No</td>
                            <td><input name="txt_internal_ref" class="text_boxes" ID="txt_internal_ref" style="width:120px;"></td>
                            <td>Fit</td> 
                            <td><?=create_drop_down( "cbo_fit_id",130, $fit_list_arr,"", 1, "--Fit List--", $selected, "" ); ?></td>
                            <td>Ready To App.</td>
                            <td><? echo create_drop_down( "cbo_ready_to_approved", 130, $yes_no,"", 1, "-- Select--", 2, "","",""); ?></td>
                            <td>Remarks/Desc./M.List</td>
                            <td><input name="txt_remarks" class="text_boxes" ID="txt_remarks" style="width:120px" maxlength="500" title="Maximum 500 Character"></td>
                        </tr>
                        <tr>
							<!-- <td>Control No</td> -->
                            <td><input type="hidden" name="txt_control_no" class="text_boxes" ID="txt_control_no" style="width:120px;"></td>
							<td><input type="file" class="image_uploader" id="multiple_file_field" name="multiple_file_field" multiple style="width:130px" accept=".jpg,.png,.jpeg,.bmp"></td>
                            <td align="left"> <input type="button" id="image_button" class="image_uploader" style="width:110px" value="ADD FILE" maxlength="300" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'sample_requisition_1', 2 ,1)" /></td>
                            <td align="left"> <input type="button" id="image_button" class="image_uploader" style="width:130px" value="ADD IMAGE" maxlength="300" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'sample_requisition_2', 1 ,1)" /></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="8" align="center" height="15">
                            	<span id="approvedMsg" style="color:red;font-size:22px;font-weight: bold;"></span>
                            </td>
                        </tr>
                    </table>
				</div>
                <div style="width:5px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
                <div id="list_acknowledge" style="max-height:300px; width:290px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
			</fieldset>
            <br/>
			<fieldset style="width:1340px;">
				<legend>Sample Fabric Booking</legend>
				<div style="width:1050px; float:left;" align="center">
                    <table cellpadding="1" cellspacing="2" width="1050">
                        <tr>
                            <td width="110"> Booking No <input type="hidden" name="cbo_fabric_natu" id="cbo_fabric_natu" value="2"></td>
                            <td width="150"><input class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_booking('requires/sample_requisition_booking_with_order_controller.php?action=fabric_booking_popup','fabric Booking Search')" readonly placeholder="Display" name="txt_booking_no" id="txt_booking_no" disabled="" />
                            </td>
                            <td width="110" class="must_entry_caption">Booking Date</td>
                            <td width="150"><input class="datepicker" type="text" style="width:120px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" disabled /></td>
                            <td width="110">Style Desc</td>
                            <td width="150"><input class="text_boxes" type="text" style="width:120px" name="txt_style_desc_book" id="txt_style_desc_book"   readonly="" /></td>
                            <td width="110">Currency</td>
                            <td><? echo create_drop_down( "cbo_currency", 130, $currency,"", 1, "-- Select --", 2, "",0 ); ?></td>
                        </tr>
                        <tr>
                            <td>Exchange Rate</td>
                            <td><input style="width:120px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly /></td>
                            <td>Source</td>
                            <td><? echo create_drop_down( "cbo_sources", 130, $source,"", 1, "-- Select --", "","", "", ""); ?></td>
                            <td class="must_entry_caption">Pay Mode</td>
                            <td><? echo create_drop_down( "cbo_pay_mode", 130, $pay_mode,"", 1, "-- Select Pay Mode --", "", "load_drop_down( 'requires/sample_requisition_with_booking_controller', this.value, 'load_drop_down_suplier', 'sup_td' )","","1,2,3,5" ); ?></td>
                            <td>Supplier Name </td>
                            <td id="sup_td"><? echo create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.status_active =1 and a.is_deleted=0  and b.party_type in (select party_type from lib_supplier_party_type where a.id=b.supplier_id and b.party_type=21) order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 ); ?>
                            </td>
                        </tr>
                        <tr>
                        	<td>Fabric Source</td>
                            <td><? echo create_drop_down( "cbo_fabric_source", 130, $fabric_source,"", 1, "-- Select --", "","enable_disable(this.value);fabic_srce_con_fnc()", "", "1,2,3"); ?></td>
                            <td>Team Leader</td>
                            <td><? echo create_drop_down( "cbo_team_leader_book", 130, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "",1 ); ?></td>
                            <td>Dealing Merch.</td>
                            <td><? echo create_drop_down( "cbo_dealing_merchant_book", 130, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1,"-- Select Team Member --", $selected, ""); ?></td>
                            <td>Buyer Req. No</td>
                            <td><input class="text_boxes" type="text" style="width:120px;"  name="txt_buyer_req_no" id="txt_buyer_req_no"/></td>
                        </tr>
                        <tr>
                        	<td>Attention</td>
                            <td colspan="3"><input class="text_boxes" type="text" style="width:380px;"  name="txt_attention" id="txt_attention"/></td>
                            <td>Remarks</td>
                            <td colspan="3"><input class="text_boxes" type="text" style="width:380px;"  name="txt_booking_remarks" id="txt_booking_remarks"/></td>
                        </tr>
                        <tr>
                        	<td>Revise</td>
                            <td><input class="text_boxes" type="text" style="width:120px;"  name="txt_revise_no" id="txt_revise_no"/></td>
                            <td>Ready To App.</td>
                            <td>
							<?=create_drop_down( "cbo_ready_to_approved_book", 130, $yes_no,"", 1, "-- Select--", 2, "get_php_form_data( this.value+'_'+document.getElementById('txt_booking_no').value, 'check_dtls_part', 'requires/sample_requisition_with_booking_controller');","","" ); ?>
                            <input type="hidden" name="is_found_dtls_part" id="is_found_dtls_part" value="1"/>
                            </td>
                            <td><input type="button" id="set_button" class="image_uploader" style="width:110px;" value="Trims Dye To Match" onClick="dtm_popup('requires/sample_requisition_with_booking_controller.php?action=dtm_popup','DTM')"  /></td>
                            <td> 
                                <input type="button" id="set_button" class="image_uploader" style="width:130px;" value="Process Loss %" onClick="open_rmg_process_loss_popup('requires/sample_requisition_with_booking_controller.php?action=rmg_process_loss_popup','Process Loss %');" />
                                <input style="width:60px;" type="hidden" class="text_boxes"  name="txt_processloss_breck_down" id="txt_processloss_breck_down" />
                        	</td>
                            <td><input type="button" class="image_uploader" style="width:110px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('txt_booking_no').value,'', 'sample_booking_non', 0 ,1)"></td>
                            <td><input type="file" multiple id="sample_fabric_booking_file" class="image_uploader"  style="width:120px" accept="image/*" onChange="document.getElementById('txt_is_file_uploaded').value=1"></td>
                        </tr>
                        <tr>
                        	<td colspan="4" align="center"><input type="button" id="set_button" class="image_uploader" style="width:150px;" value="Terms & Condition/Notes" onClick="open_terms_condition_popup('requires/sample_requisition_with_booking_controller.php?action=terms_condition_popup','Terms Condition')" /></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="8" align="center" height="15">
                            	<span id="booking_approvedMsg" style="color:red;font-size:22px;font-weight: bold;margin-left:10%"></span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="8" valign="bottom" align="center" class="button_container">
                            <? echo load_submit_buttons( $permission, "fnc_sample_requisition_mst_info", 0,0,"button_status(1);",1); ?>
                            &nbsp;<input type="button" id="report" class="formbutton" value="Print" onClick="fnc_sample_requisition_mst_info(4);" style="width:60px;display:none" />&nbsp;
                            &nbsp;<input type="button" id="report1" class="formbutton" value="Print2" onClick="fnc_sample_requisition_mst_info(5);" style="width:60px;display:none" />&nbsp;
                            <input type="button" id="report2" class="formbutton" value="Print3" onClick="fnc_sample_requisition_mst_info(6);" style="width:60px;display:none" />&nbsp;
                            <input type="button" id="report3" class="formbutton" value="Print4" onClick="fnc_sample_requisition_mst_info(7);" style="width:60px;display:none" />&nbsp;
							<input type="button" id="report4" class="formbutton" value="Print5" onClick="fnc_sample_requisition_mst_info(8);" style="width:60px;display:none" />&nbsp;
							<input type="button" id="report6" class="formbutton" value="Print6" onClick="fnc_sample_requisition_mst_info(9);" style="width:60px;display:none" />&nbsp;
							<input type="button" id="report7" class="formbutton" value="Print7" onClick="fnc_sample_requisition_mst_info(10);" style="width:60px;display:none" />&nbsp;
							<input type="button" id="report8" class="formbutton" value="Print8" onClick="fnc_sample_requisition_mst_info(11);" style="width:60px;display:none" />&nbsp;
							<input type="button" id="report9" class="formbutton" value="Print9" onClick="fnc_sample_requisition_mst_info(12);" style="width:60px;display:none" />&nbsp;
							<input type="button" id="report10" class="formbutton" value="Print10" onClick="fnc_sample_requisition_mst_info(13);" style="width:60px;display:none" />&nbsp;
							<input type="button" id="report11" class="formbutton" value="Print11" onClick="fnc_sample_requisition_mst_info(14);" style="width:60px;display:none" />&nbsp;
							<input type="button" id="report12" class="formbutton" value="Print12" onClick="fnc_sample_requisition_mst_info(15);" style="width:60px;display:none" />&nbsp;
							<input type="button" id="report13" class="formbutton" value="Print13" onClick="fnc_sample_requisition_mst_info(16);" style="width:60px;display:none" />&nbsp;
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
        <br/>
		<h3 align="left" class="accordion_h" onClick="show_hide_content('sample_details', ''); fnc_load_tr(document.getElementById('update_id').value,1); hide_others_section('sd');" style="width:1450px;"> +Sample Details <b id="sample_dtls_td" style=" color:#F00"> </b> </h3>
		<div id="content_sample_details" style="display:none;">
			<form name="sample_details_1" id="sample_details_1">
				<fieldset style="width:1450px;" id="sample_dtls">
                    <legend>Sample Details&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="chkBoxSample" id="chkBoxSample"></legend>
                    <table cellpadding="0" cellspacing="0" width="1450" class="rpt_table" border="1" rules="all" id="tbl_sample_details">
                        <thead>
                            <th width="100" class="must_entry_caption">Sample Name </th>
                            <th width="100" class="must_entry_caption">Garment Item</th>
                            <th width="40">SMV</th>
                            <th width="60">Article/Style No</th>
                            <th width="80" class="must_entry_caption">Color</th>
                            <th width="100" class="must_entry_caption">Sample Req Qty</th>
                            <th width="100" class="must_entry_caption">Submn. Qty</th>
                            <th width="85" class="must_entry_caption">Delv. Start Date</th>
                            <th width="85" class="must_entry_caption">Delv. End Date</th>
                            <th width="85" class="">Buyer Sub. Date</th>
                            <th width="70">Charge/ Unit</th>
                            <th width="70">Currency</th>
                            <th>Image</th>
                            <th>Comments</th>
                            <th>Additional Value</th>
                            <th>&nbsp;</th>
                        </thead>
                        <tbody id="sample_details_container">
                            <tr id="tr_1" style="height:10px;" class="general">
                                <td id="sample_td">
                                    <?
                                    //$sql="select id,sample_name from  lib_sample where  status_active=1 and is_deleted=0";
                                    echo create_drop_down( "cboSampleName_1", 100, $blank_array,"", 1, "Select Sample", $selected, "");
                                    ?>
                                </td>
                                <td align="center" id="item_id_1"><?=create_drop_down( "cboGarmentItem_1", 100, get_garments_item_array(2),"", 1, "Select Item", 0, ""); ?></td>
                                <td align="center" id="smv_1">
                                    <input style="width:40px;" type="text" class="text_boxes_numeric"  name="txtSmv_1" id="txtSmv_1"/>
                                    <input type="hidden" id="updateidsampledtl_1" name="updateidsampledtl_1"  class="text_boxes" style="width:20px" value="" />
                                    <input type="hidden" id="txtDeltedIdSd" name="txtDeltedIdSd"  class="text_boxes" style="width:20px" value="" />
                                    <input type="hidden" id="txtisupdated_1" name="txtisupdated_1"  class="text_boxes" style="width:20px" value="" />
                                </td>
                                <td align="center" id="article_1"><input style="width:60px;" type="text" class="text_boxes"  name="txtArticle_1" id="txtArticle_1" placeholder="Write" /></td>
                                <td align="center" id="color_1">
                                    <input style="width:80px;" type="text" class="text_boxes"  name="txtColor_1" id="txtColor_1" placeholder="Write/Browse" onDblClick="openmypage_color_size('requires/sample_requisition_with_booking_controller.php?action=color_popup','Color Search','1','1');"/>
                                     <input type="hidden" id="hiddenColorid_1" name="hiddenColorid_1"  class="text_boxes" style="width:20px"  />
                                </td>
                                <td align="center" id="sample_prod_qty_1">
                                    <input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSampleProdQty_1" id="txtSampleProdQty_1"  readonly placeholder="Browse" onDblClick="openmypage_sizeinfo('requires/sample_requisition_with_booking_controller.php?action=sizeinfo_popup','Size Search','1')" /><input type="hidden" class="text_boxes"  name="txtAllData_1" id="txtAllData_1"/>
                                </td>
                                <td align="center" id="submission_qty_1"><input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSubmissionQty_1" id="txtSubmissionQty_1" placeholder="Display" readonly /></td>
                                <td align="center" id="delv_start_date_1"><input style="width:85px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="txtDelvStartDate_1" id="txtDelvStartDate_1" /></td>
                                <td align="center" id="delv_end_date_1"><input style="width:85px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="txtDelvEndDate_1" id="txtDelvEndDate_1" /></td>
                                <td align="center" id="buyer_sub_date_1"><input style="width:85px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="txtBuyerSubDate_1" id="txtBuyerSubDate_1" /></td>
                                <td align="center" id="charge_unit_1"><input style="width:70px;" type="text" class="text_boxes_numeric"  name="txtChargeUnit_1" id="txtChargeUnit_1" placeholder="Write"/></td>
                                <td align="center" id="currency_1"><?=create_drop_down( "cboCurrency_1", 70, $currency, "","","",2, "", "", "" ); ?></td>
                                <td id="image_1"><input type="button" class="image_uploader" name="txtFile_1" id="txtFile_1" onClick="file_uploader ( '../../', document.getElementById('updateidsampledtl_1').value,'', 'sample_details_1', 0 ,1)" style="" value="ADD IMAGE"></td>
                                <td align="center" id="remarks_1">
                                    <input style="width:70px;" type="text" class="text_boxes"  name="txtRemarks_1" id="txtRemarks_1" placeholder="click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','1','1');"/>
                                </td>
                                <td>
                                    <input type="button" name="additionalvalue_1" id="additionalvalue_1" class="formbuttonplasminus" value="Additional Value" onClick="openpage_additionalvalue(1);" style="width:100px"/>
                                    <input type="hidden" name="hiddenadditionalvaluedata_1" id="hiddenadditionalvaluedata_1" value="">
                                </td>
                                <td width="70">
                                    <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1);" />
                                    <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="margin-top: 5px;" width="1450">
                        <tr>
                            <td colspan="16" valign="bottom" align="center">
                                <?=load_submit_buttons( $permission, "fnc_sample_details_info", 0,0 ,"button_status(2)",2); ?>

                                <input type="hidden" name="hidden_size_id" id="hidden_size_id" value="">
                                <input type="hidden" name="hidden_bhqty" id="hidden_bhqty" value="">
                                <input type="hidden" name="hidden_plnqnty" id="hidden_plnqnty" value="">
                                <input type="hidden" name="hidden_dyqnty" id="hidden_dyqnty" value="">
                                <input type="hidden" name="hidden_testqnty" id="hidden_testqnty" value="">
                                <input type="hidden" name="hidden_testfitqnty" id="hidden_testfitqnty" value="">
                                <input type="hidden" name="hidden_selfqnty" id="hidden_selfqnty" value="">
                                <input type="hidden" name="hidden_samp_deptqty" id="hidden_samp_deptqty" value="">
                                <input type="hidden" name="hidden_othersqty" id="hidden_othersqty" value="">
                                <input type="hidden" name="hidden_totalqnty" id="hidden_totalqnty" value="">
                                <input type="hidden" name="hidden_tbl_size_id" id="hidden_tbl_size_id" value="">
                                <input type="button" onClick="fnc_sample_name_change();" class="formbutton" value="Sample Name Change" name="">
                            </td>
                        </tr>
                    </table>
				</fieldset>
			</form>
		</div>
        
		<h3 align="left" class="accordion_h" onClick="show_hide_content('required_fabric', ''); fnc_load_tr(document.getElementById('update_id').value,2); hide_others_section('rf');" style="width:1500px;"> +Required Fabric &nbsp; &nbsp;&nbsp;<b id="fab_dtls_td" style=" color:#F00">  </b> </h3>
		<div id="content_required_fabric" style="display:none;">
			<form name="required_fabric_1" id="required_fabric_1">
				<fieldset style="width:1500px;" id="required_fab_dtls">
                    <legend>Required Fabric &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="chkBoxrequiredfabric" id="chkBoxrequiredfabric"> </legend>
                    <table cellpadding="0" cellspacing="0" width="1500" class="rpt_table" border="1" rules="all" id="tbl_required_fabric">
                        <thead>
                            <th width="90" class="must_entry_caption">Sample Name </th>
                            <th width="100" class="must_entry_caption">Garment Item</th>
                            <th width="90" class="must_entry_caption">Body Part</th>
                            <th width="90" class="must_entry_caption">Fabric Nature</th>
                            <th width="75" class="must_entry_caption">Fabric Description</th>
                            <th width="50" class="must_entry_caption">GSM</th>
                            <th width="50">Dia</th>
                            <th width="70" class="must_entry_caption">Color</th>
                            <th width="80" class="must_entry_caption">Color Type</th>
                            <th width="80" class="must_entry_caption">Width /Dia</th>
                            <th width="50" class="must_entry_caption">UOM</th>
                            <th width="60" class="must_entry_caption">Finish Req. Qty.</th>
                            <th width="60">Process Loss %</th>
                            <th width="60">Grey Req.Qty</th>
                            <th width="60">Fabric Del. Date</th>
                            <th width="70">Fabric Source</th>
                            <th width="60">IMG</th>
                            <th width="60">Yarn Details</th>
                            <th width="60">Remarks</th>
                            <th width="80">Collar & Cuff</th>
                            <th>&nbsp;</th>
                        </thead>
                        <tbody id="required_fabric_container">
                            <tr id="tr_1" class="general">
                                <td id="rfSampleId_1"><?=create_drop_down( "cboRfSampleName_1", 90, $blank_array,"", 1, "select Sample", $selected,""); ?></td>
                                <td id="rfItemId_1"><?=create_drop_down( "cboRfGarmentItem_1", 100, $blank_array,"", 1, "Select Item", 0, ""); ?></td>
                                <td id="rf_body_part_1"><?=create_drop_down( "cboRfBodyPart_1", 90, $body_part,"", 1, "Select Body Part", 0, "load_data_to_rfcolor('1');"); ?></td>
                                <td id="rf_fabric_nature_1"><?=create_drop_down( "cboRfFabricNature_1", 90, $item_category,"", 0, "Select Fabric Nature", 0, "","","2,3"); ?></td>
                                <td id="rf_fabric_description_1">
                                    <input style="width:62px;" type="text" class="text_boxes"  name="txtRfFabricDescription_1" id="txtRfFabricDescription_1" placeholder="Write/Browse" onDblClick="open_fabric_description_popup(1);" readonly/>
                                    <input type="hidden" name="libyarncountdeterminationid_1" id="libyarncountdeterminationid_1" class="text_boxes" style="width:10px" >
                                </td>
                                <td id="rf_gsm_1">
                                    <input style="width:38px;" type="text" class="text_boxes_numeric"  name="txtRfGsm_1" id="txtRfGsm_1" placeholder="Display/Write"/>
                                    <input type="hidden" id="updateidRequiredDtl_1" name="updateidRequiredDtl_1"  value=""  class="text_boxes" />
                                    <input type="hidden" id="txtDeltedIdRf" name="txtDeltedIdRf"  class="text_boxes" style="width:20px" value="" />
                                </td>
                                <td id="rf_dia_1"><input style="width:38px;" type="text" placeholder="Write" class="text_boxes"  name="txtRfDia_1" id="txtRfDia_1"/></td>
                                <td id="rf_color_1">
                                    <input style="width:58px;" type="text" class="text_boxes"  name="txtRfColor_1" id="txtRfColor_1" placeholder="Write/Browse" onDblClick="openmypage_rf_color('requires/sample_requisition_with_booking_controller.php?action=color_popup_rf','Color Search','1');" readonly />
                                    <input type="hidden" name="txtRfColorAllData_1" id="txtRfColorAllData_1" value=""  class="text_boxes">
                                </td>
                                <td id="rf_color_type_1"><?=create_drop_down( "cboRfColorType_1", 80, $color_type,"", 1, "Select Color Type", 0, ""); ?></td>
                                <td id="rf_width_dia_1"><?=create_drop_down( "cboRfWidthDia_1", 80, $fabric_typee,"", 1, "Select Width/Dia", 0, ""); ?></td>
                                <td id="rf_uom_1"><?=create_drop_down( "cboRfUom_1", 50, $unit_of_measurement,'', '', "",12,"","","12,27,1,23" ); ?></td>
                                <td id="rf_req_dzn_1" style="display: none;"><input style="width:48px;" type="text" class="text_boxes_numeric"  name="txtRfReqDzn_1" id="txtRfReqDzn_1" placeholder="Write" onBlur="calculate_required_qty('1','1');" /></td>
                                <td id="rf_req_qty_1">
                                    <input style="width:48px;" type="text" class="text_boxes_numeric"  name="txtRfReqQty_1" id="txtRfReqQty_1" readonly />
                                    <input type="hidden" class="text_boxes"  name="txtMemoryDataRf_1" id="txtMemoryDataRf_1" />
                                </td>
                                <td id="rf_reqs_qty_1"><input style="width:48px;" type="text" class="text_boxes_numeric"  name="txtProcessLoss_1" id="txtProcessLoss_1" placeholder=""  onChange="calculate_requirement('1');" /></td>
                                <td id="rf_grey_qnty_1"><input style="width:48px;" type="text" class="text_boxes_numeric"  name="txtGrayFabric_1" id="txtGrayFabric_1" readonly /></td>
                                <td id="deliveryrfDateid_1"><input style="width:48px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="fabricDelvDate_1" id="fabricDelvDate_1" /></td>
                                <td id="rf_fab_1"><?=create_drop_down( "cboRfFabricSource_1", 70, $fabric_source,'', '', "",'',"","","1,2,4" ); ?></td>
                                <td id="rf_image_1"><input type="button" class="image_uploader" name="txtRfFile_1" id="txtRfFile_1" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredDtl_1').value,'', 'required_fabric_1', 0 ,1)" value="ADD IMAGE"></td>
                                <td id="rf_yarn_dtls_1"><input style="width:48px;" type="text" class="text_boxes"  name="txtRfyarndtls_1" id="txtRfyarndtls_1" placeholder="Browse/Write" onClick="openmypage_remarks2('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Yarn','2','1');" /></td>
                                <td id="rf_remarks_1"><input style="width:48px;" type="text" class="text_boxes"  name="txtRfRemarks_1" id="txtRfRemarks_1" placeholder="Click" readonly onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','2','1');" /></td>
                                <td>
                                    <input type="button" name="collarCuff_1" id="collarCuff_1" class="formbuttonplasminus" value="Collar & Cuff" onClick="openpage_collarCuff(1);" style="width:80px"/>
                                    <input type="hidden" name="hiddencollarCuffdata_1" id="hiddencollarCuffdata_1" value="">
                                </td>
                                <td>
                                    <input type="button" id="increaserf_1" name="increaserf_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_rf_tr(1);" />
                                    <input type="button" id="decreaserf_1" name="decreaserf_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_rf_deleteRow(1);" />
                                </td>
                             </tr>
                         </tbody>
                    </table>
                    <table width="1500">
                        <tr>
                            <td colspan="21" valign="bottom" align="center" >
                                <?=load_submit_buttons( $permission, "fnc_required_fabric_details_info", 0,0 ,"button_status(3)",3); ?>
                                <input type="button" value="Generate Booking" class="formbutton" name="generate_booking" id="generate_booking" onClick="fnc_generate_booking();">
                                <input type="button" value="YarnDetails" class="formbutton" name="generate_booking" id="generate_booking" onClick="fnc_yarn_dtls();">
                                <input type="button" class="formbutton" style="width:100px" name="stripe_color" id="stripe_color" value="Stripe Color"  onClick="open_stripe_color_popup();"/>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </form>
        </div>
    <h3 align="left" class="accordion_h" onClick="show_hide_content('required_accessories', ''); fnc_load_tr(document.getElementById('update_id').value,3); hide_others_section('acc');" style="width: 1450px;"> +Required Accessories &nbsp; &nbsp;&nbsp;<b id="acc_dtls_td" style=" color:#F00">  </b> </h3>
    <div id="content_required_accessories" style="display:none;">
        <form name="required_accessories_1" id="required_accessories_1">
            <fieldset style="width: 1450px;" id="required_accessories_dtls">
                <legend>Required Accessories </legend>
                <table cellpadding="0" cellspacing="0" width="1450" class="rpt_table" border="1" rules="all" id="tbl_required_accessories">
                    <thead>
                        <th width="100" class="must_entry_caption">Sample Name </th>
                        <th width="100" class="must_entry_caption">Garment Item</th>
                        <th width="100" class="must_entry_caption">Trims Group</th>
                        <th width="130" class="must_entry_caption">Description</th>
                        <th width="100" class="">Supplier</th>
                        <th width="130">Brand/ Supp. Ref</th>
                        <th width="60" class="must_entry_caption">UOM</th>
                        <th width="100" class="">Req/Dzn</th>
                        <th width="100" class="must_entry_caption">Req. Qty.</th>
                        <th width="70" class="">Acc. Del. Date</th>
                        <th width="80" class="">Acc. Source</th>
                        <th width="100">Remarks</th>
                        <th width="80" >Image</th>
                        <th>&nbsp;</th>
                    </thead>

                    <tbody id="required_accessories_container">
                        <tr id="tr_1" class="general">
                            <td id="raSampleId_1"><?=create_drop_down( "cboRaSampleName_1", 100, $blank_array,"", 1, "Select Sample", $selected, ""); ?></td>
                            <td id="raItemId_1"><?=create_drop_down( "cboRaGarmentItem_1", 100, $blank_array,"", 1, "Select Item", 0, ""); ?></td>
                            <td id="ra_trims_group_1"><?=create_drop_down( "cboRaTrimsGroup_1", 100, "select item_name,id from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name","id,item_name", 1, "Select Item", 0, "load_uom_for_trims('1',this.value);"); ?></td>
                            <td id="ra_description_1">
                                <input style="width:120px;" type="text" class="text_boxes"  name="txtRaDescription_1" id="txtRaDescription_1" placeholder="Write"/>
                                <input type="hidden" id="updateidAccessoriesDtl_1" name="updateidAccessoriesDtl_1"  class="text_boxes" style="width:20px" value="" />
                                <input type="hidden" id="txtDeltedIdRa" name="txtDeltedIdRa"  class="text_boxes" style="width:20px" value="" />
                            </td>
                            <td><?=create_drop_down( "cboRaSupplierName_1", 100, "select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.status_active =1 and a.is_deleted=0  and b.party_type in (select party_type from lib_supplier_party_type where a.id=b.supplier_id and b.party_type in(4,5)) order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 ); ?></td>
                            <td id="ra_brand_supp_1"><input style="width:120px;" type="text" class="text_boxes"  name="txtRaBrandSupp_1" id="txtRaBrandSupp_1" placeholder="Write" /></td>
                            <td id="ra_uom_1"><?=create_drop_down( "cboRaUom_1", 60, $unit_of_measurement,'', '', "",12,"","","" ); ?></td>
                            <td id="ra_req_dzn_1"><input style="width:90px;" type="text" class="text_boxes_numeric"  name="txtRaReqDzn_1" id="txtRaReqDzn_1" placeholder="Write" /></td>
                            <td id="ra_req_qty_1">
                                <input style="width:90px;" type="text" class="text_boxes_numeric"  name="txtRaReqQty_1" id="txtRaReqQty_1" placeholder="Write" />
                                <input type="hidden" class="text_boxes"  name="txtMemoryDataRa_1" id="txtMemoryDataRa_1" />
                            </td>
                            <td id="deliveryraDateid_1"><input style="width:70px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="accDate_1" id="accDate_1" /></td>
                            <td id="ra_fab_1"><?=create_drop_down( "cboRaFabricSource_1", 80, $fabric_source,'', '', "",'',"","","2,4" ); ?></td>
                            <td id="ra_remarks_1"><input style="width:90px;" type="text" class="text_boxes"  name="txtRaRemarks_1" id="txtRaRemarks_1" placeholder="Click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','3','1');" /></td>
                            <td id="ra_image_1"><input type="button" class="image_uploader" name="txtRaFile_1" id="txtRaFile_1" onClick="file_uploader ( '../../', document.getElementById('updateidAccessoriesDtl_1').value,'', 'required_accessories_1', 0 ,1)" style="width:80px;" value="ADD IMAGE"></td>
                            <td width="70">
                                <input type="button" id="increasera_1" name="increasera_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_ra_tr(1)" />
                                <input type="button" id="decreasera_1" name="decreasera_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_ra_deleteRow(1);" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table  width="1450">
                    <tr>
                        <td colspan="13" valign="bottom" align="center">
                            <?=load_submit_buttons( $permission, "fnc_required_accessories_info", 0,0 ,"button_status(4)",4); ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
		</div>
		<h3 align="left" class="accordion_h" onClick="show_hide_content('required_embellishment',''); fnc_load_tr(document.getElementById('update_id').value,4); hide_others_section('emb');" style="width: 1360px;"> +Required Embellishment &nbsp; &nbsp;&nbsp;<b id="emb_dtls_td" style=" color:#F00">  </b> </h3>
		<div id="content_required_embellishment" style="display:none;">
			<form name="required_embellishment_1" id="required_embellishment_1">
				<fieldset style="width: 1360px;" id="required_embellishment_dtls">
                    <legend style="width:1380px;">Required Embellishment </legend>
                    <table cellpadding="0" cellspacing="0" width="1360px" class="rpt_table" border="1" rules="all" id="tbl_required_embellishment">
                        <thead>
                            <th width="140" class="must_entry_caption" align="">Sample Name </th>
                            <th width="140" class="must_entry_caption">Garment Item</th>
                            <th width="140" class="must_entry_caption">Name</th>
                            <th width="140" class="must_entry_caption">Type</th>
                            <th width="95" class="">Body Part</th>
                            <th width="100" class="">Supplier</th>
                            
                            <th width="60" class="">Qty Pcs</th>
                            <th width="50" class="">Rate</th>
                            <th width="60" class="">Amount</th>
                            
                            <th width="100" class="">Remarks</th>
                            <th width="70" class="">Delivery Date</th>
                            <th width="120">Image</th>
                            <th>&nbsp;</th>
                        </thead>

                        <tbody id="required_embellishment_container">
                            <tr id="tr_1" class="general">
                                <td id="reSampleId_1"><?=create_drop_down( "cboReSampleName_1", 140, $blank_array,"", 1, "Select Sample", $selected, ""); ?></td>
                                <td id="reItemIid_1"><?=create_drop_down( "cboReGarmentItem_1", 140, $blank_array,"", 1, "Select Item", 0, ""); ?></td>
                                <td id="re_name_1"><?=create_drop_down( "cboReName_1", 140, $emblishment_name_array,"", 1, "Select Name", 0, "cbotype_loder(1);"); ?></td>
                                <td id="reType_1"><?=create_drop_down( "cboReType_1", 140, $blank_array,"", 1, "Select Item", 0, ""); ?></td>
                                <td id="re_body_part_1"><?=create_drop_down( "cboReBodyPart_1", 95, $body_part,"", 1, "Select Body Part", 0, "");?></td>
                                <td><?=create_drop_down( "cboReSupplierName_1", 100, "select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.status_active =1 and a.is_deleted=0  and b.party_type in (select party_type from lib_supplier_party_type where a.id=b.supplier_id and b.party_type=23) order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 ); ?></td>
                                <td id="re_qty_1">
                                    <input style="width:50px;" type="text" class="text_boxes"  name="txtReQty_1" id="txtReQty_1" placeholder="Click"  onClick="open_consumption_popup('requires/sample_requisition_with_booking_controller.php?action=consumption_popup','Consumtion Entry Form','1');"/>
                                    <input style="width:40px;" type="hidden" class="text_boxes"  name="txtcolorBreakdown_1" id="txtcolorBreakdown_1"  readonly="" />
                                </td>
                                <td id="re_rate_1"><input style="width:40px;" type="text" class="text_boxes" name="txtReRate_1" id="txtReRate_1" placeholder="Rate" readonly /></td>
                                <td id="re_amount_1"><input style="width:50px" type="text" class="text_boxes" name="txtReAmount_1" id="txtReAmount_1" placeholder="Amount" readonly /></td>
                                <td id="re_remarks_1"><input style="width:90px;" type="text" class="text_boxes"  name="txtReRemarks_1" id="txtReRemarks_1" placeholder="Click"  readonly="" onClick="openmypage_remarks('requires/sample_requisition_with_booking_controller.php?action=all_remarks_popup','Remarks','4','1');"/></td>
                                <td id="deliveryDateid_1"><input style="width:70px;" type="text" class="datepicker" placeholder="Select Date" autocomplete="off" name="deliveryDate_1" id="deliveryDate_1" /></td>
                                <td id="re_image_1"><input type="button" class="image_uploader" name="reTxtFile_1" id="reTxtFile_1" onClick="file_uploader ( '../../',document.getElementById('updateidRequiredEmbellishdtl_1').value,'', 'required_embellishment_1', 0 ,1);" style="width:120px;" value="ADD/VIEW IMAGE">

                                <input type="hidden" id="updateidRequiredEmbellishdtl_1" name="updateidRequiredEmbellishdtl_1"  class="text_boxes" style="width:20px" value="" />
                                <input type="hidden" id="txtDeltedIdRe" name="txtDeltedIdRe"  class="text_boxes" style="width:20px" value="" />
                                </td>
                                <td>
                                    <input type="button" id="increasere_1" name="increasere_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_re_tr(1)" />
                                    <input type="button" id="decreasere_1" name="decreasere_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_re_deleteRow(1);" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table width="1360px">
                        <tr>
                            <td colspan="10" valign="bottom" align="center">
                                <?=load_submit_buttons( $permission, "fnc_required_embellishment_info", 0,0 ,"button_status(5)",5); ?>
                            </td>
                        </tr>
                    </table>
				</fieldset>
			</form>
		</div>
	</div>
</body>
<script type="text/javascript">
	var buyer=$("#cbo_buyer_name").val();
	if(buyer!=0)
	{
		load_drop_down( 'requires/sample_requisition_with_booking_controller', buyer, 'load_drop_down_season_buyer', 'season_td');
	}
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
