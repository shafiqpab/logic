<?
/*-------------------------------------------- Comments
Purpose			:
Functionality	:
JS Functions	:
Created by		:	Ashraful
Creation date 	: 	6-8-2012
Updated by 		: 	Kaiyum
Update date		: 	26-09-2016
QC Performed BY	:
QC Date			:
Comments		: 	[ Kaiyum: update for 'buyer wise season auto select' ]
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Quotation Inquery Entry", "../../", 1, 1,$unicode,'','');
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
		if(operation==4)
		{
			 if(form_validation('cbo_company_name*txt_system_id','Select Company*System ID')==false)
			{
				return;
			}
	
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'**'+$('#txt_system_id').val()+'**'+$('#update_id').val()+'**'+report_title, "inquery_entry_print", "requires/quotation_inquery_controller" )
			 return;
		}
		 
		//check season validation
		var testoptionlength = $("#cbo_season_name option").length-1;
		//alert(testoptionlength);
		if(testoptionlength>0) {
			if(form_validation('cbo_season_name','Select Season')==false)
			{
				return;
			}
		}
	
		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][433]);?>'){
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][433]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][433]);?>')==false)
			{
				
				return;
			}
		}
				
		if (form_validation('cbo_company_name*cbo_buyer_name*txt_style_ref*txt_inquery_date*cbo_team_leader*txt_request_no*txt_est_ship_date*txt_fabrication','Company*Buyer*Style Ref*Inquery Date*Team Leader*Inquery No*Bulk Est Ship Date*Fabrication')==false)
		{
			return;
		}
		else // Save Here
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_style_ref*txt_inquery_date*cbo_team_leader*cbo_season_name*cbo_status*txt_request_no*txt_remarks*cbo_dealing_merchant*txt_system_id*cbo_gmt_item*txt_est_ship_date*txt_fabrication*save_text_data*txt_offer_qty*txt_color*txt_color_id*txt_req_quot_date*txt_target_samp_date*txt_actual_req_quot_date*txt_actual_sam_send_date*txt_department*txt_buyer_submit_price*txt_buyer_target_price*update_id*txt_bh_merchant*cbo_color_type*txt_possible_order_con_date*txt_lead_time*price_info_break_down*sample_info_break_down*cbo_product_department*cbo_sub_dept*cbo_factory_merchant*txt_style_id*set_breck_down*tot_set_qnty*txt_sew_smv*cbo_order_uom*txt_style_description*cbo_currercy*cbo_season_year*cbo_design_source_id*cbo_qltyLabel*cbo_brand_id*cbo_location_name*cbo_customer_year*cbo_week',"../../");
			
		//	alert(data);	return;
			//freeze_window(operation);
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
		    return;
		}

		if(reponse[0]==0 )
		{
		   show_msg(reponse[0]);
		   $("#txt_system_id").val(reponse[1]);
		   $("#update_id").val(reponse[2]);
		   $("#txt_color_id").val(reponse[3]);
		   set_button_status(1, permission, 'fnc_quotation_inquery',1,1);
		}
		if(reponse[0]==1 )
		{
			show_msg(reponse[0]);
			$("#txt_color_id").val(reponse[3]);
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
		//release_freezing();
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
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1350px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];

		var mrrNumber=this.contentDoc.getElementById("hidden_issue_number").value; // mrr number
		mrrNumber = mrrNumber.split("_");
		//var mrrId=this.contentDoc.getElementById("issue_id").value; // mrr number

		$("#txt_system_id").val(mrrNumber[0]);
		$("#update_id").val(mrrNumber[1]);

		get_php_form_data(mrrNumber[1], "populate_data_from_data", "requires/quotation_inquery_controller");

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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=450px,center=1,resize=1,scrolling=0','../');

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

function openmypage_fabric_popup1()
{
	var cbo_company_id = $('#cbo_company_name').val();
	var save_data = $('#txt_fabrication').val();
	var txt_style_from_lib = $('#txt_style_from_lib').val();
	var parameter='width=610px,height=410px,center=1,resize=1,scrolling=0';
	if(txt_style_from_lib==1)
	{
		parameter='width=940px,height=510px,center=1,resize=1,scrolling=0';
	}

	if (form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}

	var page_link='requires/quotation_inquery_controller.php?save_data='+save_data+'&action=buyer_inquery_fab_popup'+'&txt_style_from_lib='+txt_style_from_lib;
	var title='Fabric Detail ';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, parameter,'../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var save_data=this.contentDoc.getElementById("save_data").value;
		//var tot_trims_wgt=this.contentDoc.getElementById("tot_trims_qnty").value;
		//alert(save_data);
		$('#txt_fabrication').val(save_data);
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
function style_id_reset()
{
	$("#txt_style_id").val('');
	$("#txt_style_from_lib").val('');
}
function fnc_variable_settings_check(company_id)
{
	$('#txt_color').val('');
	$('#txt_color_id').val('');
	$("#txt_style_from_lib").val('');
	var fab=document.querySelector("#txt_fabrication");
	if(fab)
	{
		$("#txt_fabrication").val('');
	}
	
	var lib_data=return_ajax_request_value(company_id, 'load_variable_settings', 'requires/quotation_inquery_controller');
	var data=lib_data.split("**");
	var color_from_lib=data[0];
	var style_from_lib=data[1];
	$("#txt_style_from_lib").val(style_from_lib);
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

	if(style_from_lib==1)
	{
		$('#txt_style_id').val('');
		$('#txt_style_ref').attr('readonly',true);
		$('#txt_style_ref').attr('placeholder','Browse');
		$('#txt_style_ref').removeAttr("onDblClick").attr("onDblClick","style_select_popup()");
	}
	else
	{
		$('#txt_style_id').val('');
		$('#txt_style_ref').attr('readonly',false);
		$('#txt_style_ref').attr('placeholder','Write');
		$('#txt_style_ref').removeAttr('onDblClick','onDblClick');

		$('#cbo_gmt_item').removeAttr('disabled','disabled');
		$('#txt_department').removeAttr('disabled','disabled');
		$('#cbo_product_department').removeAttr('disabled','disabled');
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
function style_select_popup()
{
	var cbo_buyer_name=$("#cbo_buyer_name").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/quotation_inquery_controller.php?action=style_popup&cbo_buyer_name='+cbo_buyer_name, 'Style Pop Up', 'width=370px,height=370px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var style_name=this.contentDoc.getElementById("style_name");
		var style_id=this.contentDoc.getElementById("style_id");
		var all_data=this.contentDoc.getElementById("all_data");
		console.log(all_data.value);
		if (style_name.value!="")
		{
			$('#txt_style_ref').val(style_name.value);
			$('#txt_style_id').val(style_id.value);
			get_php_form_data(style_id.value+"_"+style_name.value, "populate_data_from_style_popup", "requires/quotation_inquery_controller" );
			var data=all_data.value.split("_");

			if(data.length>2)
			{
				$("#cbo_gmt_item").val(data[2]);
				$("#cbo_gmt_item").attr('disabled', 'disabled');

				if(data[3].length>0)
				{
					<?php

						$department_arr=return_library_array( "select id,department_name from lib_department_name where status_active=1 and is_deleted=0", "id", "department_name"  );						
						$js_array = json_encode($department_arr);
						echo "var javascript_department_arr = ". $js_array . ";\n";
					?>
					$("#txt_department").val(javascript_department_arr[data[3]]);
					$("#txt_department").attr('disabled', 'disabled');
					console.log(javascript_department_arr);

				}
				
				$("#cbo_product_department").val(data[4]);
				if(cbo_buyer_name==0 ||cbo_buyer_name=="")
				{
					$("#cbo_buyer_name").val(data[5]);
					$("#cbo_buyer_name").attr('disabled', 'disabled');
				}
				$("#cbo_product_department").attr('disabled', 'disabled');
			}
		}
	}
}
function price_info_popup()
{
	var break_down = $('#price_info_break_down').val();
	var update_id = $('#update_id').val();

	var page_link='requires/quotation_inquery_controller.php?break_down='+break_down+'&update_id='+update_id+'&action=price_info_popup';
	var title='Price Info';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var save_data=this.contentDoc.getElementById("save_data").value;
		console.log(save_data);
		$('#price_info_break_down').val(save_data);
		get_php_form_data(save_data, "populate_data_target_submit_price", "requires/quotation_inquery_controller" );
	}
}

function sample_info_popup()
{
	var break_down = $('#sample_info_break_down').val();
	var update_id = $('#update_id').val();

	var page_link='requires/quotation_inquery_controller.php?break_down='+break_down+'&update_id='+update_id+'&action=sample_info_popup';
	var title='Sample Info';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=600px,height=390px,center=1,resize=1,scrolling=0','../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var save_data=this.contentDoc.getElementById("save_data").value;
		$('#sample_info_break_down').val(save_data);
	}
}

function sub_dept_load(cbo_buyer_name,cbo_product_department)
	{
		if(cbo_buyer_name ==0 || cbo_product_department==0 )
		{
			return
		}
		else
		{
			load_drop_down( 'requires/quotation_inquery_controller',cbo_buyer_name+'_'+cbo_product_department, 'load_drop_down_sub_dep', 'sub_td' )
		}
	}
function copyQuotion()
{
	// var copyText = document.getElementById("txt_system_id");
	// copyText.select();
	// copyText.setSelectionRange(0, 99999)
	// document.execCommand("copy");
	var operation=0;
	var data="action=copy_quotation&operation=0"+get_submitted_data_string('txt_system_id*cbo_company_name*update_id',"../../");
		//alert(data);	return;
	freeze_window(operation);
	http.open("POST","requires/quotation_inquery_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_quotation_copy_reponse;
}

function fnc_quotation_copy_reponse()
{
	if(http.readyState == 4)
	{
		//alert(http.responseText);
		console.log(http.responseText);
		var reponse=trim(http.responseText).split('**');
		

		if(reponse[0]==0 )
		{
		   show_msg(reponse[0]);
		   $("#txt_system_id").val(reponse[1]);
		   $("#update_id").val(reponse[2]);
		   set_button_status(1, permission, 'fnc_quotation_inquery',1,1);
		}
		
		if(reponse[0]==10 )
		{
			show_msg(reponse[0]);
		}
		
		release_freezing();
	}
}

function set_week_date(){
		var week=document.getElementById('cbo_week').value*1;
		var year=document.getElementById('cbo_year_selection').value;
	
		if(week){
			
			$('.month_button').attr('disabled','true');
			$('.month_button_selected').attr('disabled','true');
			$('#txt_date_from').attr('disabled','true');
			$('#txt_date_to').attr('disabled','true');
			var week_date=return_global_ajax_value(week+"_"+year, 'week_date', '', 'requires/weekly_capacity_and_order_booking_status_controller');
			var week_date_arr=week_date.split('_');
			document.getElementById('txt_date_from').value=week_date_arr[0];
			document.getElementById('txt_date_to').value=week_date_arr[1];
		}else{
			$('.month_button').removeAttr('disabled');
			$('.month_button_selected').removeAttr('disabled');
			$('#txt_date_from').removeAttr('disabled');
			$('#txt_date_to').removeAttr('disabled');
		}
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
			//calculate_cm_cost_with_method();
			//fnc_calculate_dep_oper_interest_income();
		}
	}
	
    function call_print_button_for_mail(mail,mail_body,type)
	{
        var update_id=$('#update_id').val();
		var sys_id=$('#txt_system_id').val();
		 
		var ret_data=return_global_ajax_value(sys_id+'__'+update_id+'__'+mail+'__'+mail_body, 'app_notification', '', 'requires/quotation_inquery_controller');
		alert(ret_data);
	}	

</script>
</head>
<body onLoad="set_hotkey();buyer_season_load();">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission);  ?>
        <fieldset style="width:1050px;">
            <legend>Quotation Inquery</legend>
            <form name="quotationinquery_1" id="quotationinquery_1" autocomplete="off">
                <table  width="1050" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="right" colspan="4">System ID </td>
                        <td colspan="2">
                            <input style="width:160px;" type="text" title="Double Click to Search" onDblClick="open_mrrpopup();" class="text_boxes" placeholder="Browse" name="txt_system_id" id="txt_system_id" readonly />
                           <input type="hidden" name="update_id" id="update_id" />
                        </td>
                        <td colspan="2" align="right"><span onclick="copyQuotion();" class="formbutton">Copy Quotation ID</span></td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Company</td>
                        <td width="150"><?=create_drop_down( "cbo_company_name", 130, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/quotation_inquery_controller', this.value, 'load_drop_down_buyer', 'buyer_td'); load_drop_down( 'requires/quotation_inquery_controller', this.value, 'load_drop_down_season_com', 'season_td'); fnc_variable_settings_check(this.value); load_drop_down( 'requires/quotation_inquery_controller', this.value, 'load_drop_down_sew_location', 'location' ); ",0); ?>
                        </td>
						<td width="110">Location Name</td>
						<td width="150" id="location"><?=create_drop_down("cbo_location_name", 130, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                        <td width="110" class="must_entry_caption">Buyer</td>
                        <td width="150" id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "sub_dept_load(this.value,document.getElementById('cbo_product_department').value);load_drop_down( 'requires/quotation_inquery_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" ,0); ?></td>
                        <td width="120" class="must_entry_caption">Buyer Inquiry No</td>
                        <td><input class="text_boxes" type="text" style="width:120px" placeholder="Write"  name="txt_request_no" id="txt_request_no"/></td>
                    </tr>
                    <tr>
						<td class="must_entry_caption">Style Ref/Name</td>
                        <td id="location">
                            <input class="text_boxes" type="text" style="width:120px" placeholder="Write"  name="txt_style_ref" id="txt_style_ref" onChange="style_id_reset();" onBlur="check_quatation();"/>
                            <input type="hidden" name="txt_style_id" id="txt_style_id">
                            <input type="hidden" name="txt_style_from_lib" id="txt_style_from_lib" value="0">
                        </td>
						<td>Style Description</td>
                   		<td><input class="text_boxes" type="text" style="width:120px;" name="txt_style_description" id="txt_style_description"/></td>
                        <td>Brand</td>
                   		<td id="brand_td"><?=create_drop_down( "cbo_brand_id", 130, $blank_array,'', 1, "--Brand--",$selected, "" ); ?>
                        <td>Season <?=create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-S.Year-", 1, "",0,"" ); ?></td>
                        <td id="season_td"><?=create_drop_down( "cbo_season_name", 130, $blank_array,"", 1, "-- Select Season --", $selected, "" ); ?></td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Inq.Rcvd Date</td>
                        <td><input name="txt_inquery_date" style="width:120px"  id="txt_inquery_date" placeholder="Select Date" class="datepicker" type="text" value="" /></td>
                        <td class="must_entry_caption">Team Leader</td>   
                        <td id="leader_td"><?=create_drop_down( "cbo_team_leader", 130, "select id,team_leader_name from lib_marketing_team where project_type=1 and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $teamId, "load_drop_down( 'requires/quotation_inquery_controller', this.value, 'cbo_dealing_merchant', 'div_marchant' ); load_drop_down( 'requires/quotation_inquery_controller', this.value, 'cbo_factory_merchant', 'div_marchant_factory' ); " );
                        ?></td>
					 	<td>Dealing Merchandiser</td>
                        <td id="div_marchant"><?=create_drop_down( "cbo_dealing_merchant", 130, $blank_array,"", 1, "-Select Team Member-", $selected, "" ); ?></td>
                        <td>Factory Merchandiser</td>
                        <td id="div_marchant_factory"><?=create_drop_down( "cbo_factory_merchant", 130, $blank_array,"", 1, "-Select Team Member-", $selected, "" ); ?></td>  
                    </tr>
                    <tr>
                    	<td>BH Merchandiser</td>
                        <td><input class="text_boxes" type="text" style="width:120px" placeholder="Write"  name="txt_bh_merchant" id="txt_bh_merchant"/></td>
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
						<td class="must_entry_caption">Fabrication</td>
                        <td>
                        	<input class="hidden" type="hidden" style="width:120px"  name="txt_fabrication" id="txt_fabrication" readonly/>
                        	<input class="text_boxes" type="text" placeholder="Browse" style="width:120px"  name="save_text_data" id="save_text_data" onDblClick="openmypage_fabric_popup();" readonly/>
                        </td>
                        <td>Color Type</td>
                        <td><?=create_drop_down( "cbo_color_type", 130, $color_type,"", 1, "-- Select Color Type --", $selected, "" ); ?></td> 
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Prod. Dept.</td>
	                    <td><?=create_drop_down( "cbo_product_department", 130, $product_dept, "", 1, "-Select-", $selected, "sub_dept_load(document.getElementById('cbo_buyer_name').value,this.value)", "", "" ); ?></td> 
	                    <td>Sub. Dept </td>
	                    <td id="sub_td"><? echo create_drop_down( "cbo_sub_dept", 130, $blank_array,"", 1, "-- Select Sub Dep --", $selected, "" ); ?></td>
                     	<td>Department Name</td>
                        <td><input name="txt_department" id="txt_department" placeholder="Write" class="text_boxes" type="text" style="width:120px" /></td>
						<td class="must_entry_caption">Bulk Est. Ship Date</td>
                        <td><input type="text" style="width:120px" class="datepicker" placeholder="Select Date"  name="txt_est_ship_date" id="txt_est_ship_date"/></td>
                    </tr>
                    <tr>  
						<td>Lead Time</td>
						<td><input class="text_boxes_numeric" type="text" placeholder="Write" style="width:120px"  name="txt_lead_time" id="txt_lead_time"/></td>  
						<td>Bulk Offer Qty</td>
                        <td><input class="text_boxes_numeric" type="text" placeholder="Write" style="width:120px"  name="txt_offer_qty" id="txt_offer_qty"/></td>
                        <td>Body Color</td>
                        <td>
                            <input class="text_boxes" type="text" placeholder="Write/Browse" style="width:120px"  name="txt_color" id="txt_color" onChange="color_id_reset();" />
                            <input type="hidden" name="txt_color_id" id="txt_color_id"/>
                        </td>
                        <td>Possible Order Con. Date</td>
                        <td><input type="text" style="width:120px" class="datepicker" placeholder="Date" name="txt_possible_order_con_date" id="txt_possible_order_con_date"/></td>
                    </tr>
                    <tr>
                        <td>Target Samp Sub:Date</td>
                        <td><input type="text" style="width:120px" class="datepicker" placeholder="Select Date" name="txt_target_samp_date" id="txt_target_samp_date"/></td>
                        <td>Actual Samp.Send Date</td>
                        <td><input type="text" style="width:120px" class="datepicker" placeholder="Select Date" name="txt_actual_sam_send_date" id="txt_actual_sam_send_date"/></td>						
                    	<td>Target Req. Quot. Date</td>
                        <td><input type="text" style="width:120px" class="datepicker" placeholder="Select Date" name="txt_req_quot_date" id="txt_req_quot_date"/></td>
                        <td>Actual Quot. Date</td>
                        <td><input type="text" style="width:120px" class="datepicker" placeholder="Select Date" name="txt_actual_req_quot_date" id="txt_actual_req_quot_date"/></td>
                    </tr>
					<tr>
                    	<td>Currency</td>
                 		<td><?=create_drop_down( "cbo_currercy", 130, $currency,'', 0, "",2, "" ); ?></td>  
						<td>Buyer Target Price</td>
                        <td><input type="text" style="width:120px" class="text_boxes_numeric"  name="txt_buyer_target_price" id="txt_buyer_target_price" readonly/></td>
                        <td>Buyer Submit Price</td>
                        <td><input name="txt_buyer_submit_price" id="txt_buyer_submit_price"  class="text_boxes" type="text" style="width:120px" readonly/></td>
                    	<td>Design Source</td>
						<td><?=create_drop_down( "cbo_design_source_id", 130, $design_source_arr,"", 1, "-- Select --", "", "" ); ?></td>
					</tr>
					<tr>
                    	<td>Quality Label</td>
                    	<td><?=create_drop_down( "cbo_qltyLabel", 130, $quality_label,"", 1, "--Quality Label--", $selected, "" ); ?></td>
                    	<td>Customer Sales Week</td>
						<td> 
							<?
                            $weekArr=array();
                            $current_date=date("Y");
                            $sql=sql_select("select id,week from week_of_year  where year=$current_date order by week");
                            //echo "select id,week from week_of_year  where year=$current_date order by week";
                            foreach($sql as $row){
                                $weekArr[$row[csf('week')]]="Week-".$row[csf('week')];
                            }
                              echo create_drop_down( "cbo_week", 130, $weekArr,"", 1, "-- Select --", $selected, "set_week_date()"  );
                            ?>
                        </td>
                        <td>Customer Sales Year</td>
						<td><?=create_drop_down( "cbo_customer_year", 130, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
						<td>Status</td>
						<td><?=create_drop_down( "cbo_status", 130, $inquery_status_arr,"", 1, "--Select--", "", "" ); ?></td>
					</tr>
					<tr>
                    	<td>Remarks </td>
                        <td colspan="3"><input class="text_boxes" type="text" style="width:380px" placeholder="Write"  name="txt_remarks" id="txt_remarks"/></td>
                    	<td>
                          <input type="button" class="image_uploader" id="price_info" style="width:100px" value="Price Info." onClick="price_info_popup();">
                          <input type="hidden" id="price_info_break_down" >
                        </td>
                        <td>
                          <input type="button" class="image_uploader" id="sample_info" style="width:130px" value="Sample Info." onClick="sample_info_popup();">
                          <input type="hidden" value="" id="sample_info_break_down" >
                        </td>
                        <td><input type="button" class="image_uploader" style="width:110px" value="ADD File" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'quotation_inquery', 2 ,1);"></td>
                        <td><input type="button" class="image_uploader" style="width:130px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'quotation_inquery', 0 ,1);"></td>
					</tr>
                    <tr>
                        <td align="center" colspan="8" valign="middle" style="max-height:380px; min-height:15px;" id="size_color_breakdown11">
                        	<?=load_submit_buttons( $permission, "fnc_quotation_inquery", 0,1 ,"reset_form('quotationinquery_1','','')",1); ?>
							<input class="formbutton" type="button" onClick="fnSendMail('../../','',1,0,0,1,0)" value="Mail Send" style="width:80px;">
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
</html>