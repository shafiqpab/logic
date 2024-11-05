<?
/*-------------------------------------------- Comments
Purpose			:	This form will create Buyer Inquiry Woven
Functionality	:
JS Functions	:
Created by		:	Ashraful
Creation date 	: 	6-8-2012
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
echo load_html_head_contents("Quotation Inquiry Entry", "../../", 1, 1,$unicode,'','');
/*echo '<pre>';
print_r($_SESSION['logic_erp']['mandatory_field'][434]); die;*/
?>


<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

	var department = [<? echo substr(return_library_autocomplete( "select department_name from wo_quotation_inquery where  status_active=1 and is_deleted=0 group by department_name", "department_name" ), 0, -1); ?>];

	$(document).ready(function(e)
	 {
			$("#txt_department").autocomplete({
			 source: department
		  });
	 });

	function fnc_quotation_inquery( operation )
	{
		freeze_window(operation);
		if(operation==4)
		{
			if(form_validation('cbo_company_name*txt_system_id','Select Company*System ID')==false)
			{
				release_freezing();
				return;
			}
	
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'**'+$('#txt_system_id').val()+'**'+$('#update_id').val()+'**'+report_title, "inquery_entry_print", "requires/quotation_inquery_controller" )
			release_freezing();
			return;
		}
		 
		//check season validation
		/*var testoptionlength = $("#cbo_season_name option").length-1;
		if(testoptionlength>0) {
			if(form_validation('cbo_season_name','Select Season')==false)
			{
				release_freezing();
				return;
			}
		}*/
	
		if (form_validation('cbo_company_name*cbo_buyer_name*txt_style_ref*txt_inquery_date*cbo_team_leader*txt_fabrication*txt_con_rec_target_date*cbo_gmt_item*txt_style_description*cbo_dealing_merchant','Company*Buyer*Style Ref*Inquiry Date*Team_ Leader*Fabrication*Con Rec Target Date*Gmts Item*Style Description*Dealing Merchant')==false)
		{
			release_freezing();
			return;
		}
		else // Save Here
		{
			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][434]);?>'){
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][434]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][434]);?>')==false)
				{
					release_freezing();
					return;
				}
			}
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_style_ref*txt_inquery_date*cbo_season_name*cbo_status*txt_request_no*txt_remarks*cbo_team_leader*cbo_dealing_merchant*txt_system_id*cbo_gmt_item*set_breck_down*tot_set_qnty*txt_sew_smv*cbo_order_uom*txt_est_ship_date*txt_fabrication*txt_offer_qty*txt_color*txt_color_id*txt_req_quot_date*txt_target_samp_date*txt_actual_req_quot_date*txt_actual_sam_send_date*txt_department*txt_buyer_submit_price*txt_buyer_target_price*cbo_season_year*cbo_brand*update_id*cbo_priority*txt_con_rec_target_date*cbo_concern_marchant*txt_cutable_width*txt_style_description',"../../");
			 /*alert(data);
			 release_freezing();
			return;*/	 
			//
			http.open("POST","requires/quotation_inquery_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_quotation_inquery_reponse;
		}
	}

	function fnc_quotation_inquery_reponse()
	{
		if(http.readyState == 4)
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]=='pricequotation')
			{
				var quotation_msg="Delete Restricted, Quotation Found, Quotation ID is: "+reponse[1];
				alert(quotation_msg);
				release_freezing();
				return;
			}
			
			if(reponse[0]=='jobno')
			{
				var quotation_msg="Delete Restricted, Job Found, Job No is: "+reponse[1];
				alert(quotation_msg);
				release_freezing();
				return;
			}
			if(reponse[0]=='costsheet')
			{
				var quotation_msg="Delete Restricted, Spot Costing Found, Cost Sheet No is: "+reponse[1];
				alert(quotation_msg);
				release_freezing();
				return;
			}
	
			if(reponse[0]==0 )
			{
			   show_msg(reponse[0]);
			   $("#txt_system_id").val(reponse[1]);
			   $("#update_id").val(reponse[2]);
			   $("#txt_color_id").val(reponse[3]);
			   set_button_status(1, permission, 'fnc_quotation_inquery',1,1);

			   fileUpload('quotation_file*quotation_inquery_front_image*quotation_inquery_back_image',$("#update_id").val(),'quotation_inquery*quotation_inquery_front_image*quotation_inquery_back_image','../../',1);

			}
			if(reponse[0]==1 )
			{
				show_msg(reponse[0]);
				$("#txt_color_id").val(reponse[3]);
				fileUpload('quotation_file*quotation_inquery_front_image*quotation_inquery_back_image',$("#update_id").val(),'quotation_inquery*quotation_inquery_front_image*quotation_inquery_back_image','../../',1);

			}
			if(reponse[0]==10 )
			{
				show_msg(reponse[0]);
			}
			if(reponse[0]==2)
			{
				show_msg(reponse[0]);
				reset_form('quotationinquery_1','','');
			}
			release_freezing();
		}
	}

	function open_mrrpopup()
	{
		//reset_form('','list_container_recipe_items*recipe_items_list_view','','','','');

		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var page_link='requires/quotation_inquery_controller.php?action=mrr_popup&company='+company;
		var title="Search  Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1180px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];

			var mrrNumber=this.contentDoc.getElementById("hidden_issue_number").value; // mrr number
			mrrNumber = mrrNumber.split("_");
			//var mrrId=this.contentDoc.getElementById("issue_id").value; // mrr number

			$("#txt_system_id").val(mrrNumber[0]);
			$("#update_id").val(mrrNumber[1]);

			get_php_form_data(mrrNumber[0], "populate_data_from_data", "requires/quotation_inquery_controller");

			set_button_status(1, permission, 'fnc_quotation_inquery',1,1);
		}
	}

	function openmypage_fabric_popup()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var save_data = $('#txt_fabrication').val();

		if (form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}

		var page_link='requires/quotation_inquery_controller.php?save_data='+save_data+'&action=buyer_inquery_fab_popup';
		var title='Fabric Details';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1240px,height=390px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var save_data=this.contentDoc.getElementById("save_data").value;
			var save_text_data=this.contentDoc.getElementById("save_text_data").value;
			//var tot_trims_wgt=this.contentDoc.getElementById("tot_trims_qnty").value;
			//alert(save_data);
			$('#txt_fabrication').val(save_data);
			$('#save_text_data').val(save_text_data);
			//$('#txt_tot_trims_weight').val( tot_trims_wgt );
		}
	}

	function buyer_season_load()
	{
		var company = $("#cbo_company_name").val();
		var cbo_buyer_name = $("#cbo_buyer_name").val();
		load_drop_down( 'requires/quotation_inquery_controller', cbo_buyer_name+"_"+company, 'load_drop_down_season_buyer', 'season_td' );
	}
function check_quatation(){
	var txt_style_ref=$('#txt_style_ref').val();
	var txt_inquery_id=$('#txt_system_id').val();
	var response=return_global_ajax_value( txt_style_ref+"**"+txt_inquery_id, 'check_style_ref', '', 'requires/quotation_inquery_controller');
	response=trim(response).split('**');
	if(response[0]==1){
		var r=confirm("Following quotation id found against ' "+ txt_style_ref +" ' style ref.\n"+response[1]+". \n If you want to continue press Ok, otherwise press Cancel");
		if(r==false)
		{
			$('#txt_style_ref').val('')
			return;
		}
		else
		{
			//continue;
		}
	}
}
function color_id_reset()
{
	$('#txt_color_id').val('');
}
function fnc_variable_settings_check(company_id)
{
	$('#txt_color').val('');
	$('#txt_color_id').val('');
	var color_from_lib=return_ajax_request_value(company_id, 'load_variable_settings', 'requires/quotation_inquery_controller');
	if(color_from_lib==1)
	{
		$('#txt_color_id').val('');
		$('#txt_color').attr('readonly',true);
		$('#txt_color').attr('placeholder','Browse');
		$('#txt_color').removeAttr("onDblClick").attr("onDblClick","color_select_popup()");
	}
	else
	{
		$('#txt_color_id').val('');
		$('#txt_color').attr('readonly',false);
		$('#txt_color').attr('placeholder','Write');
		$('#txt_color').removeAttr('onDblClick','onDblClick');
	}
}
function color_select_popup()
{
	var buyer_name=$('#cbo_buyer_name').val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/quotation_inquery_controller.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var color_name=this.contentDoc.getElementById("color_name");
		var color_id=this.contentDoc.getElementById("color_id");
		if (color_name.value!="")
		{
			$('#txt_color').val(color_name.value);
			$('#txt_color_id').val(color_id.value);

		}
	}
}

	// function sendMail()
	// {
	// 	var alert_msg='';
	// 	if($('#text_mail_send_date').val()){alert_msg='Last Mail Send Date:'+$('#text_mail_send_date').val();}
		
	// 	if(confirm(alert_msg+'\n Do you want to send mail?')==0){
	// 		return false
	// 	}
		
	// 	if (form_validation('txt_system_id','System Id')==false)
	// 	{
	// 		return;
	// 	}
		
	// 	var sys_id=$('#txt_system_id').val();
		
		
	// 	var data="sys_id="+sys_id+"&update_id="+$('#update_id').val();
 	// 	freeze_window(operation);
	// 	http.open("POST","../../auto_mail/woven/buyer_inquiry_woven_auto_mail.php",true);
	// 	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	// 	http.send(data);
	// 	http.onreadystatechange = function fnc_btb_mst_reponse()
	// 	{
	// 		if(http.readyState == 4)
	// 		{
	// 			var reponse=trim(http.responseText);
	// 			alert(reponse);
	// 			release_freezing();
	// 		}
	// 	}
	// }


    function call_print_button_for_mail(mail,mail_body,type)
	{
        var update_id=$('#update_id').val();
		var sys_id=$('#txt_system_id').val();
		 
		var ret_data=return_global_ajax_value(sys_id+'__'+update_id+'__'+mail+'__'+mail_body, 'fabric_app', '', '../../auto_mail/woven/buyer_inquiry_woven_auto_mail');
		alert(ret_data);
	}

	function fnc_quotation_inquery_copy( operation )
	{
		if (form_validation('cbo_company_name*cbo_buyer_name*txt_style_ref*txt_inquery_date*txt_fabrication*txt_con_rec_target_date*cbo_gmt_item*txt_style_description*cbo_dealing_merchant','Company*Buyer*Style Ref*Inquiry Date*Fabrication*Con Rec Target Date*Gmts. Item*Style Description*Dealing Merchant')==false)
		{
			return;
		}
		else // Save Here
		{
			if(!confirm('Buyer Inquiry Copy! Sure?')){return false;}
			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][434]);?>'){
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][434]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][434]);?>')==false)
				{
					return;
				}
			}
			
 			
			var pagLink='requires/quotation_inquery_controller.php?season_name='+$('#cbo_season_name').val()+'&season_year='+$('#cbo_season_year').val()+'&buyer_name='+$('#cbo_buyer_name').val()+'&cbo_brand='+$('#cbo_brand').val()+'&action=copy_data_change_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', pagLink, 'Copy Data Change', 'width=470px,height=120px,center=1,resize=1,scrolling=0','../');
	
			emailwindow.onclose=function()
			{		
				var theform=this.contentDoc.forms[0];
				var style_ref=this.contentDoc.getElementById("txt_change_style_ref").value;
				var wash_color=this.contentDoc.getElementById("txt_change_wash_color").value;
				var season_name=this.contentDoc.getElementById("cbo_change_season_name").value;
				var season_year=this.contentDoc.getElementById("cbo_change_season_year").value;
				var change_brand_id=this.contentDoc.getElementById("cbo_change_brand_id").value;
				
				$('#txt_style_ref').val(style_ref);
				$('#txt_color').val(wash_color);
				$('#cbo_season_name').val(season_name);
				$('#cbo_season_year').val(season_year);
				$('#cbo_brand').val(change_brand_id);
				
				
				if (form_validation('txt_style_ref*txt_color','Style Ref*Body/Wash Color')==false)
				{
					return;
				}
				else{
					var data="action=save_update_delete_copy&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_style_ref*txt_inquery_date*cbo_season_name*cbo_status*txt_request_no*txt_remarks*cbo_team_leader*cbo_dealing_merchant*txt_system_id*cbo_gmt_item*set_breck_down*tot_set_qnty*txt_sew_smv*cbo_order_uom*txt_est_ship_date*txt_fabrication*txt_offer_qty*txt_color*txt_color_id*txt_req_quot_date*txt_target_samp_date*txt_actual_req_quot_date*txt_actual_sam_send_date*txt_department*txt_buyer_submit_price*txt_buyer_target_price*cbo_season_year*cbo_brand*update_id*cbo_priority*txt_con_rec_target_date*cbo_concern_marchant*txt_cutable_width*txt_style_description',"../../");
					 //alert(data);	 
					//freeze_window(operation);
					http.open("POST","requires/quotation_inquery_controller.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = fnc_quotation_inquery_copy_reponse;
				}
				
			}
						
			
		}
	}

	function fnc_quotation_inquery_copy_reponse()
	{
		if(http.readyState == 4)
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]=='pricequotation')
			{
				var quotation_msg="Delete Restricted, Quotation Found, Quotation ID is: "+reponse[1];
				alert(quotation_msg);
				return;
			}
	
			if(reponse[0]==36 )
			{
			   show_msg(reponse[0]);
			   $("#txt_system_id").val(reponse[1]);
			   $("#update_id").val(reponse[2]);
			   $("#txt_color_id").val(reponse[3]);
			   $("#txt_copy_sys_id").val(reponse[4]);
			   $("#txt_is_file_uploaded").val('');
			   set_button_status(1, permission, 'fnc_quotation_inquery',1,1);
			}
			if(reponse[0]==10 )
			{
				show_msg(reponse[0]);
			}
		}
	}
	
	function reset_fnc()
	{
		reset_form('','','txt_style_ref*cbo_status*txt_request_no*txt_remarks*txt_system_id*cbo_gmt_item*txt_est_ship_date*txt_fabrication*save_text_data*txt_offer_qty*txt_color*txt_color_id*txt_req_quot_date*txt_target_samp_date*txt_actual_req_quot_date*txt_actual_sam_send_date*txt_department*txt_buyer_submit_price*txt_buyer_target_price*update_id*cbo_priority*txt_con_rec_target_date*txt_cutable_width*txt_style_description*txt_is_file_uploaded','','');
		set_button_status(0, permission, 'fnc_quotation_inquery',1,1);
	}
	
	function open_set_popup(unit_id)
	{
		var txt_quotation_id=document.getElementById('update_id').value;
		var set_breck_down=document.getElementById('set_breck_down').value;
		var tot_set_qnty=document.getElementById('tot_set_qnty').value;
		var txt_inquery_id=0;
		var set_smv_id=0;
		var txt_style_ref=document.getElementById('txt_style_ref').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var cbo_company_name=0;
		var item_id=document.getElementById('cbo_gmt_item').value;

		var page_link="requires/quotation_inquery_controller.php?txt_quotation_id="+trim(txt_quotation_id)+"&action=open_set_list_view&set_breck_down="+set_breck_down+"&tot_set_qnty="+tot_set_qnty+'&unit_id='+unit_id+'&txt_inquery_id='+txt_inquery_id+'&set_smv_id='+set_smv_id+'&txt_style_ref='+txt_style_ref+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&item_id='+item_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Item Details", 'width=860px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var set_breck_down=this.contentDoc.getElementById("set_breck_down")
			var item_id=this.contentDoc.getElementById("item_id")
			var tot_set_qnty=this.contentDoc.getElementById("tot_set_qnty")
			var tot_smv_qnty=this.contentDoc.getElementById('tot_smv_qnty');
			document.getElementById('set_breck_down').value=set_breck_down.value;
			document.getElementById('cbo_gmt_item').value=item_id.value;
			document.getElementById('tot_set_qnty').value=tot_set_qnty.value;
			document.getElementById('txt_sew_smv').value=tot_smv_qnty.value;
		}
	}
	function fnc_appSubmission_withoutanyChange()
	{
		freeze_window(1);
		var update_id=$("#update_id").val();
		var txt_est_ship_date=$("#txt_est_ship_date").val();
		var txt_offer_qty=$("#txt_offer_qty").val();
		if(txt_est_ship_date=='' && txt_offer_qty==''){
			if (form_validation('txt_est_ship_date*txt_offer_qty','Bulk Est. Ship Date*Bulk Offer Qty')==false)
			{
				release_freezing();
				return;
			}
		}
		if(txt_est_ship_date !=''){
			txt_est_ship_date = change_date_format(txt_est_ship_date);
		}
		//var check_is_master_part_saved=return_global_ajax_value(update_id, 'check_is_master_part_saved', '', 'requires/pre_cost_entry_controller_v2');

		if(trim(update_id)=="")
		{
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function(){
				$(this).html('Please Save Master Part').removeClass('messagebox').addClass('messagebox_error').fadeOut(7000);
			});
			release_freezing();
			return;
		}
		else
		{			
			var submission_withoutanyChange=return_global_ajax_value(update_id+'**'+txt_est_ship_date+'**'+$("#txt_offer_qty").val(), 'appSubmission_withoutanyChange', '', 'requires/quotation_inquery_controller');
			var response=submission_withoutanyChange.split('**');
			if(trim(response[0])==1)
			{
				alert("Data is Updated Successfully.");
				release_freezing();
				return;
			}
			else
			{
				alert("Data is not Updated Successfully.");
				release_freezing();
				return;
			}
		}
	}

</script>
</head>
<body onLoad="set_hotkey(); buyer_season_load();">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission);  ?>
        <fieldset style="width:1050px;">
            <legend>Quotation Inquiry</legend>
            <form name="quotationinquery_1" id="quotationinquery_1" autocomplete="off">
                <table  width="1050" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td colspan="4" align="right">System ID </td>
                        <td colspan="4">
                            <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="open_mrrpopup();" class="text_boxes" placeholder="Browse" name="txt_system_id" id="txt_system_id" readonly />
                            <input type="hidden" name="update_id" id="update_id" />
                        </td>
                    </tr>
                    <tr>
                        <td width="105" class="must_entry_caption">Company</td>
                        <td width="150"><? echo create_drop_down( "cbo_company_name", 130, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/quotation_inquery_controller', this.value, 'load_drop_down_buyer', 'buyer_td');load_drop_down( 'requires/quotation_inquery_controller', this.value, 'load_drop_down_season_com', 'season_td'); fnc_variable_settings_check(this.value);",0); ?></td>
                        <td width="105" class="must_entry_caption">M.Style Ref/Name</td>
                        <td width="150"><input class="text_boxes" type="text" style="width:120px" placeholder="Write"  name="txt_style_ref" id="txt_style_ref" onBlur="check_quatation();"/></td>
                        <td width="105" class="must_entry_caption">Inq.Rcvd Date</td>
                        <td width="150"><input name="txt_inquery_date" style="width:120px"  id="txt_inquery_date" placeholder="Select Date" class="datepicker" type="text" value="" /></td>
                        <td width="105">Buyer Inquiry No</td>
                        <td><input class="text_boxes" type="text" style="width:120px" placeholder="Write" name="txt_request_no" id="txt_request_no"/></td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Style Description </td>
                        <td><input name="txt_style_description" id="txt_style_description" placeholder="Write" class="text_boxes" type="text" style="width:120px" /></td>
                    	<td class="must_entry_caption">Buyer </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ,0); ?></td>
                        <td>Season&nbsp;&nbsp;<? echo create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                        <td id="season_td"><? echo create_drop_down( "cbo_season_name", 130, $blank_array,"", 1, "-Season-", $selected, "" ); ?></td>
                        <td>Brand</td>
                        <td id="brand_td"><? echo create_drop_down( "cbo_brand", 130, $blank_array,"",1, "-Brand-", $selected,""); ?></td>
                    </tr>
                    <tr>
                    	<td>Order UOM</td>
						<td>
							<?=create_drop_down( "cbo_order_uom",55, $unit_of_measurement, "",0, "", 1, "","","1,58" ); ?>
                            <input type="button" id="set_button" class="image_uploader" style="width:70px;" value="Item Details" onClick="open_set_popup(document.getElementById('cbo_order_uom').value);" />
                            <input type="hidden" id="set_breck_down" />
                            <input type="hidden" id="cbo_gmt_item"  />
                            <input type="hidden" id="tot_set_qnty" />
                            <input type="hidden" id="txt_sew_smv" />
                            <input type="hidden" name="is_season_must" id="is_season_must" style="width:30px;" class="text_boxes" />
	                    </td>
						<td class="must_entry_caption">Team leader</td>
                       <td><? 
					   if($_SESSION['logic_erp']['user_level']!=2){$whereCon=" and USER_TAG_ID={$_SESSION['logic_erp']['user_id']}";}
					   echo create_drop_down( "cbo_team_leader", 130, "select id, team_leader_name from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0  and id in(select TEAM_ID from LIB_MKT_TEAM_MEMBER_INFO where 1=1 and status_active=1 $whereCon )  order by team_leader_name","id,team_leader_name", 1, "-Select Team-", $selected, "load_drop_down( 'requires/quotation_inquery_controller', this.value, 'load_drop_down_dealing_merchant', 'div_marchant'); load_drop_down( 'requires/quotation_inquery_controller', this.value, 'load_drop_down_sample_marchant', 'div_sample_marchant'); " ); ?>
                       <input type="hidden" id="txt_cutable_width" name="txt_cutable_width" value="">
                       </td>
                       
                       <td>Sample Merchant</td>
                        <td id="div_sample_marchant"><? echo create_drop_down( "cbo_concern_marchant", 130, "select b.id,b.team_member_name from lib_marketing_team a,lib_mkt_team_member_info b where a.id=b.team_id and a.PROJECT_TYPE=2 and b.status_active =1 and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0 order by b.team_member_name","id,team_member_name", 1, "-- Select Merchant --", $selected, "" ); //a.lib_mkt_team_member_info_id=b.id and ?></td>
                       
					   <td class="must_entry_caption">Dealing Merchant</td>
                       <td id="div_marchant"><? echo create_drop_down( "cbo_dealing_merchant",130, "select b.id,b.team_member_name from lib_marketing_team a,lib_mkt_team_member_info b where a.id=b.team_id and  a.PROJECT_TYPE=2 and b.status_active =1 and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0 order by b.team_member_name","id,team_member_name", 1, "-- Select Merchant --", $selected, "" ); //a.lib_mkt_team_member_info_id=b.id and ?></td>
                    </tr>
                     <tr>
                     	<td>Body/Wash Color</td>
                        <td>
                             <input class="text_boxes" type="text" placeholder="Write/Browse" style="width:120px"  name="txt_color" id="txt_color" onChange="color_id_reset();" />
                             <input type="hidden" name="txt_color_id" id="txt_color_id"/>
                        </td>
                        
                     	<td class="must_entry_caption">Fabrication</td>
                        <td colspan="3">
                        	<input class="hidden" type="hidden" placeholder="Browse" style="width:140px"  name="txt_fabrication" id="txt_fabrication" />
                        	<input class="text_boxes" type="text" placeholder="Browse" style="width:377px"  name="save_text_data" id="save_text_data" onDblClick="openmypage_fabric_popup();" readonly/>
                        </td>
                        <td>Department Name</td>
                        <td><input  name="txt_department" id="txt_department" placeholder="Write" class="text_boxes" type="text" style="width:120px" /></td>
                    </tr>
                    <tr>
                    	<td>Bulk Est. Ship Date</td>
                        <td><input type="text" style="width:120px" class="datepicker" placeholder="Select Date"  name="txt_est_ship_date" id="txt_est_ship_date"/></td>
                        <td>Bulk Offer Qty</td>
                        <td><input class="text_boxes_numeric" type="text" placeholder="Write" style="width:120px"  name="txt_offer_qty" id="txt_offer_qty"/></td>
                        <td>Tgt. Req. Quot. Date</td>
                        <td><input type="text" style="width:120px" class="datepicker" placeholder="Select Date"  name="txt_req_quot_date" id="txt_req_quot_date"/></td>
                        <td>Tgt. Samp Sub:Date</td>
                        <td><input type="text" style="width:120px" class="datepicker" placeholder="Select Date"  name="txt_target_samp_date" id="txt_target_samp_date"/></td>
                        
                    </tr>
                    <tr>
                    	<td>Act. Samp.Send Date</td>
                        <td><input type="text" style="width:120px" class="datepicker" placeholder="Select Date"  name="txt_actual_sam_send_date" id="txt_actual_sam_send_date"/></td>
                        <td>Act. Quot. Date</td>
                        <td><input type="text" style="width:120px" class="datepicker" placeholder="Select Date"  name="txt_actual_req_quot_date" id="txt_actual_req_quot_date"/></td>
                    	<td class="must_entry_caption">Cons. Rec.Tgt.Date</td>
                        <td><input type="text" style="width:120px" class="datepicker" placeholder="Select Date"  name="txt_con_rec_target_date" id="txt_con_rec_target_date"/></td>
                        <td>Buyer Tgt. Price</td>
                        <td><input type="text" style="width:120px" class="text_boxes_numeric" placeholder="Write"  name="txt_buyer_target_price" id="txt_buyer_target_price"/></td>
                        
                    </tr>
                    <tr>
                    	<td> Buyer Submit Price</td>
                       <td><input  name="txt_buyer_submit_price" id="txt_buyer_submit_price" placeholder="Write" class="text_boxes" type="text" style="width:120px" /></td>
                       <td>Priority</td>
                        <td><?=create_drop_down( "cbo_priority", 130, $priority_arr,"", 1, "Select", $selected, "" ); ?></td>
						<td>Status</td>
                        <td><? echo create_drop_down( "cbo_status", 130, $row_status,"", "", "", 1, "" ); ?></td>
                        <td>Copy ID No</td>
                        <td><input class="text_boxes" type="text" style="width:120px" name="txt_copy_sys_id" id="txt_copy_sys_id" readonly/></td>
                    </tr>
                    <tr>
                        <td>Remarks </td>
                        <td colspan="7"><input class="text_boxes" type="text" style="width:895px" placeholder="Write"  name="txt_remarks" id="txt_remarks"/></td>
                    </tr>
                    <tr>
                        <td>ADD File</td>
                        <td>
                        	<input type="file" multiple id="quotation_file" class="image_uploader" style="width:130px" accept=".text,.pdf,.xls,.xlsx" onChange="document.getElementById('txt_is_file_uploaded').value=1" >
                            <input type="button" class="image_uploader" style="width:130px" value=" ADD File" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'quotation_inquery', 2 ,1)">
                        </td>
                        <td>Front Image</td>
                       <td>
                       		<input type="file" multiple id="quotation_inquery_front_image" class="image_uploader" style="width:130px" accept="image/*" onChange="document.getElementById('txt_is_file_uploaded').value=1">
                            <input type="button" id="image_button" class="image_uploader" style="width:130px" value="ADD IMAGE FRONT" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'quotation_inquery_front_image', 0 ,1)" />
                       </td>
                       <td>Back Image</td>
                       <td>
 							<input type="file" multiple id="quotation_inquery_back_image" class="image_uploader" style="width:130px" accept="image/*" onChange="document.getElementById('txt_is_file_uploaded').value=1">
                            <input type="button" id="image_button" class="image_uploader" style="width:130px" value="ADD IMAGE BACK" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'quotation_inquery_back_image', 0 ,1)" />
                            <input type="hidden" id="txt_is_file_uploaded" value="">
                       </td>
                       <td colspan="2"><input type="button" name="btn_appSubmission_withoutanyChange" class="formbuttonplasminus" style="width:130px;" onClick="fnc_appSubmission_withoutanyChange();" value="Update Shipment Date"></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="8" valign="middle" style="max-height:380px; min-height:15px;" id="size_color_breakdown11">
                        <?=load_submit_buttons( $permission, "fnc_quotation_inquery", 0,1 ,"reset_fnc();",1); ?>
                        
                        <input class="formbutton" type="button" onClick="fnSendMail('../../','',1,0,0,1,0)" value="Mail Send" style="width:80px;">
                        <input class="text_boxes" type="hidden" name="text_mail_send_date" id="text_mail_send_date"/>
                        <input type="button" name="button" class="formbutton" value="Copy Buyer Inquiry" onClick="fnc_quotation_inquery_copy(5);" style="width:130px;" /><br>
						 
                        <div style="color:#FF0000" id="text_mail_send_status"></div>
                        </td>
                   </tr>
                </table>
            </form>
        </fieldset>
        <div id="size_color_breakdown">
        </div>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	var company_id=document.getElementById('cbo_company_name').value;
	if(company_id!=0){
		setTimeout(function(){
			load_drop_down( 'requires/quotation_inquery_controller', company_id, 'load_drop_down_buyer', 'buyer_td');
			load_drop_down( 'requires/quotation_inquery_controller', company_id, 'load_drop_down_season_com', 'season_td');
			fnc_variable_settings_check(company_id);
			
			
			var buyer_id=document.getElementById('cbo_buyer_name').value; 
			if(buyer_id!=0){
				setTimeout(function(){
					load_drop_down( 'requires/quotation_inquery_controller',document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_season_buyer', 'season_td'); 
					load_drop_down( 'requires/quotation_inquery_controller', document.getElementById('cbo_buyer_name').value, 'load_drop_down_brand', 'brand_td');
					
				}, 500);
			}
			
			
		}, 1000);
	}
	
	
	
	
	
	
	
</script>



</html>