<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish Fabric Issue Entry
				
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar 
Creation date 	: 	06-07-2013
Updated by 		: 	Kausar (Creating Report)	
Update date		: 	14-12-2013	   
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
echo load_html_head_contents("Finish Fabric Issue Info","../../", 1, 1, '','',''); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

function active_inactive(str,roll_field_reset)
{
	$('#cbo_sample_type').val(0);
	$('#cbo_sewing_source').val(0);
	$('#cbo_sewing_company').val(0);
	$('#cbo_buyer_name').val(0);
	$('#txt_issue_qnty').val('');
	$('#txt_issue_req_qnty').val('');
	$('#hidden_prod_id').val('');
	$('#txt_batch_no').val('');
	$('#hidden_batch_id').val('');
	$('#all_po_id').val('');
	$('#save_data').val('');
	$('#save_string').val('');
	$('#txt_order_numbers').val('');
	$('#txt_fabric_received').val('');
	$('#txt_cumulative_issued').val('');
	$('#txt_yet_to_issue').val('');
	$('#previous_prod_id').val('');
	$('#txt_fabric_desc').val('');
	$('#list_fabric_desc_container').html('');
	$('#txt_global_stock').val('');
	
	if(roll_field_reset==1)
	{
		$('#txt_no_of_roll').val('');
		$('#txt_no_of_roll').attr('disabled','disabled');
		$('#txt_no_of_roll').attr('placeholder','Display');
	}
	
	if(str==3 || str==10 || str==26 || str==29 || str==30 || str==31)
	{
		$('#cbo_sample_type').attr('disabled','disabled');
		$('#cbo_sewing_source').attr('disabled','disabled');	
		$('#cbo_sewing_company').attr('disabled','disabled');	
		$('#cbo_buyer_name').removeAttr('disabled','disabled');
		
		$('#txt_issue_qnty').removeAttr('readonly');
		$('#txt_issue_qnty').removeAttr('onDblClick');	
		$('#txt_issue_qnty').removeAttr('placeholder');
	}
	else if(str==4 || str==8)
	{
		$('#cbo_sample_type').removeAttr('disabled','disabled');
		$('#cbo_sewing_source').removeAttr('disabled','disabled');	
		$('#cbo_sewing_company').removeAttr('disabled','disabled');	
		
		if(str==4)
		{
			$('#cbo_buyer_name').attr('disabled','disabled');
			$('#txt_issue_qnty').attr('readonly','readonly');
			$('#txt_issue_qnty').attr('onDblClick','openmypage_po();');	
			$('#txt_issue_qnty').attr('placeholder','Double Click To Search');
		}
		else
		{
			$('#cbo_buyer_name').removeAttr('disabled','disabled');
			$('#txt_issue_qnty').removeAttr('readonly');
			$('#txt_issue_qnty').removeAttr('onDblClick');	
			$('#txt_issue_qnty').removeAttr('placeholder');
		}
	}
	else
	{
		$('#cbo_sample_type').attr('disabled','disabled');
		$('#cbo_sewing_source').removeAttr('disabled','disabled');
		$('#cbo_sewing_company').removeAttr('disabled','disabled');
		$('#cbo_buyer_name').attr('disabled','disabled');
		$('#txt_issue_qnty').attr('readonly','readonly');
		$('#txt_issue_qnty').attr('onDblClick','openmypage_po();');	
		$('#txt_issue_qnty').attr('placeholder','Double Click To Search');
	}
}

function po_fld_reset(str)
{
	if(str==1)
	{
		$('#cbo_buyer_name').removeAttr('disabled','disabled');
		$('#txt_issue_qnty').removeAttr('readonly');
		$('#txt_issue_qnty').removeAttr('onDblClick');	
		$('#txt_issue_qnty').removeAttr('placeholder');
	}
	else
	{
		$('#cbo_buyer_name').attr('disabled','disabled');
		$('#txt_issue_qnty').attr('readonly','readonly');
		$('#txt_issue_qnty').attr('onDblClick','openmypage_po();');	
		$('#txt_issue_qnty').attr('placeholder','Double Click To Search');
	}
}

function openmypage_batchnum()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var issue_purpose = $('#cbo_issue_purpose').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	else
	{
		var page_link='requires/finish_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&issue_purpose='+issue_purpose+'&action=batch_number_popup';
		var title='Batch Number Popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=990px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;
			var batch_no=this.contentDoc.getElementById("hidden_batch_no").value; //Access form field with id="emailfield"
			var without_order=this.contentDoc.getElementById("hidden_without_order").value;
			
			$('#txt_batch_no').val(batch_no);
			$('#hidden_batch_id').val(batch_id);
			$('#all_po_id').val('');
			$('#save_data').val('');
			$('#save_string').val('');
			$('#txt_order_numbers').val('');
			$('#txt_fabric_received').val('');
			$('#txt_cumulative_issued').val('');
			$('#txt_yet_to_issue').val('');
			$('#previous_prod_id').val('');
			$('#txt_fabric_desc').val('');
			$('#txt_global_stock').val('');
			
			if(issue_purpose==3 || issue_purpose==10 || issue_purpose==26 || issue_purpose==29 || issue_purpose==30 || issue_purpose==31)
			{
				po_fld_reset(without_order);
			}
			
			var roll_maintained=$('#roll_maintained').val();
			if(roll_maintained!=1)
			{
				//load_drop_down( 'requires/finish_fabric_issue_controller', batch_id+"**0", 'load_drop_down_fabric_desc', 'fabricDesc_td' );
				show_list_view(batch_id,'show_fabric_desc_listview','list_fabric_desc_container','requires/finish_fabric_issue_controller','');
			}
		}
	}
}

function openmypage_fabricDescription()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var save_string = $('#save_string').val();
	var hidden_prod_id = $('#hidden_prod_id').val();
	var txt_fabric_desc = $('#txt_fabric_desc').val();
	var hidden_batch_id = $('#hidden_batch_id').val();

	if (form_validation('cbo_company_id*txt_batch_no','Company*Batch')==false)
	{
		return;
	}
	
	var title = 'Fabric Description Info';	
	var page_link = 'requires/finish_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&save_string='+save_string+'&hidden_prod_id='+hidden_prod_id+'&txt_fabric_desc='+txt_fabric_desc+'&hidden_batch_id='+hidden_batch_id+'&action=fabricDescription_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var product_id=this.contentDoc.getElementById("product_id").value; //Access form field with id="emailfield"
		var product_details=this.contentDoc.getElementById("product_details").value; //Access form field with id="emailfield"
		var number_of_roll=this.contentDoc.getElementById("number_of_roll").value; //Access form field with id="emailfield"
		var hidden_roll_issue_qnty=this.contentDoc.getElementById("hidden_roll_issue_qnty").value; //Access form field with id="emailfield"
		var save_string=this.contentDoc.getElementById("save_string").value; //Access form field with id="emailfield"
		
		$('#save_string').val( save_string );
		$('#txt_issue_req_qnty').val(hidden_roll_issue_qnty);
		$('#txt_issue_qnty').val('');
		$('#txt_no_of_roll').val( number_of_roll );
		$('#hidden_prod_id').val(product_id);	
		$('#txt_fabric_desc').val(product_details);	
	}
}

function openmypage_po()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var roll_maintained = $('#roll_maintained').val();
	var save_data = $('#save_data').val();
	var all_po_id = $('#all_po_id').val();
	var txt_issue_req_qnty = $('#txt_issue_req_qnty').val(); 
	var distribution_method = $('#distribution_method_id').val();
	var cbo_buyer_name = $('#cbo_buyer_name').val();
	var hidden_batch_id = $('#hidden_batch_id').val();
	var dtls_tbl_id = $('#update_dtls_id').val();
	var prod_id = $("#hidden_prod_id").val();
	
	if (form_validation('cbo_company_id*txt_batch_no*txt_fabric_desc','Company*Batch No*Fabric Description')==false)
	{
		return;
	}
	
	var title = 'PO Info';	
	var page_link = 'requires/finish_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&all_po_id='+all_po_id+'&roll_maintained='+roll_maintained+'&save_data='+save_data+'&txt_issue_req_qnty='+txt_issue_req_qnty+'&prev_distribution_method='+distribution_method+'&cbo_buyer_name='+cbo_buyer_name+'&dtls_tbl_id='+dtls_tbl_id+'&prod_id='+prod_id+'&action=po_popup'+'&batch_id='+hidden_batch_id;
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=870px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var save_data=this.contentDoc.getElementById("save_data").value;	 //Access form field with id="emailfield"
		var hide_issue_qnty=this.contentDoc.getElementById("tot_issue_qnty").value*1; //Access form field with id="emailfield"
		var all_po_id=this.contentDoc.getElementById("all_po_id").value; //Access form field with id="emailfield"
		var all_po_no=this.contentDoc.getElementById("all_po_no").value; //Access form field with id="emailfield"
		var distribution_method=this.contentDoc.getElementById("distribution_method").value;
		var buyer_id=this.contentDoc.getElementById("buyer_id").value; //Access form field with id="emailfield"
		var tot_issue_qnty=hide_issue_qnty.toFixed(2);

		$('#save_data').val(save_data);
		$('#txt_issue_qnty').val(tot_issue_qnty);
		$('#txt_issue_req_qnty').val(tot_issue_qnty );
		$('#cbo_buyer_name').val(buyer_id);
		$('#all_po_id').val(all_po_id);
		$('#txt_order_numbers').val(all_po_no);
		$('#distribution_method_id').val(distribution_method);
		
		if(all_po_id!="")
		{
			var prod_id=$('#hidden_prod_id').val();
			var bodypart_id=$('#cbo_body_part').val();
			get_php_form_data(all_po_id+"**"+prod_id+"**"+hidden_batch_id+"**"+bodypart_id, "populate_data_about_order", "requires/finish_fabric_issue_controller" );
			load_drop_down( 'requires/finish_fabric_issue_controller',all_po_id, 'load_drop_down_gmt_item', 'gmt_item_td' );
		}
	}
}

function openmypage_systemId()
{
	var cbo_company_id = $('#cbo_company_id').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	
	var title = 'Finish Fabric Issue Info';	
	var page_link = 'requires/finish_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&action=finishFabricIssue_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=390px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var finish_fabric_issue_id=this.contentDoc.getElementById("finish_fabric_issue_id").value; //Access form field with id="emailfield"
		
		reset_form('finishFabricEntry_1','div_details_list_view','','cbo_issue_purpose,9','','roll_maintained');
		get_php_form_data(finish_fabric_issue_id, "populate_data_from_issue_master", "requires/finish_fabric_issue_controller" );
		show_list_view(finish_fabric_issue_id,'show_finish_fabric_issue_listview','div_details_list_view','requires/finish_fabric_issue_controller','');
		set_button_status(0, permission, 'fnc_fabric_issue_entry',1,1);	
	}
}

function generate_report_file(data,action,page)
{
	window.open("requires/finish_fabric_issue_controller.php?data=" + data+'&action='+action, true );
}
		

function fnc_fabric_issue_entry(operation)
{
	if(operation==4)
	{
		var print_with_vat=0;
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_system_id').val()+'*'+print_with_vat,'finish_fabric_issue_print','requires/finish_fabric_issue_controller');
		/*print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#cbo_cutting_floor').val()+'*'+report_title, "finish_fabric_issue_print", "requires/finish_fabric_issue_controller" ) */
		return;
	}
	else if(operation==5)
	{
		if ($("#txt_system_id").val()=="")
		{
			alert ("Please Save First.");
			return;
		}
		var print_with_vat=1;
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_system_id').val()+'*'+print_with_vat,'finish_fabric_issue_print','requires/finish_fabric_issue_controller');
		/*print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#cbo_cutting_floor').val()+'*'+report_title, "finish_fabric_issue_print", "requires/finish_fabric_issue_controller" ) */
		return;
	}
	else if(operation==6)
	{
		if ($("#txt_system_id").val()=="")
		{
			alert ("Please Save First.");
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_system_id').val(),'finish_fabric_issue_print2','requires/finish_fabric_issue_controller');
		/*print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#cbo_cutting_floor').val()+'*'+report_title, "finish_fabric_issue_print", "requires/finish_fabric_issue_controller" ) */
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		var cbo_issue_purpose=$('#cbo_issue_purpose').val();
		
		if( form_validation('cbo_company_id*txt_issue_date*cbo_buyer_name','Company*Issue Date*Buyer Name')==false )
		{
			return;
		}	
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_issue_date').val(), current_date)==false)
		{
			alert("Issue Date Can not Be Greater Than Current Date");
			return;
		}
		
		if(cbo_issue_purpose==4 || cbo_issue_purpose==8)
		{
			if( form_validation('cbo_sample_type','Sample Type')==false )
			{
				return;
			}	
		}
		
		if(cbo_issue_purpose==4 || cbo_issue_purpose==8 || cbo_issue_purpose==9)
		{
			if(form_validation('cbo_sewing_source*cbo_sewing_company','Sewing Source*Sewing Company')==false )
			{
				return;
			}	
		}
		
		if(form_validation('cbo_store_name*txt_batch_no*txt_fabric_desc*txt_issue_qnty','Store Name*Batch No*Fabric Description*Issue Qnty')==false )
		{
			return;
		}
		
		if(($("#txt_issue_qnty").val()*1 > $("#txt_yet_to_issue").val()*1+$("#hidden_issue_qnty").val()*1)) 
		{
			alert("Issue Quantity Exceeds Yet To Issue Quantity.");
			return;
		}
		
		var dataString = "txt_system_id*cbo_company_id*cbo_issue_purpose*cbo_sample_type*txt_issue_date*txt_challan_no*cbo_sewing_source*cbo_sewing_company*cbo_buyer_name*txt_cut_req*cbo_store_name*txt_batch_no*hidden_batch_id*txt_fabric_desc*txt_issue_qnty*txt_no_of_roll*txt_remarks*txt_rack*txt_shelf*cbo_cutting_floor*hidden_prod_id*previous_prod_id*update_id*save_data*save_string*update_dtls_id*update_trans_id*hidden_issue_qnty*txt_issue_req_qnty*all_po_id*roll_maintained*cbo_body_part*cbo_item_name*hidden_fabric_rate*cbouom";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/finish_fabric_issue_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_issue_entry_reponse;
	}
}

function fnc_fabric_issue_entry_reponse()
{	
	if(http.readyState == 4) 
	{	  		
		var reponse=trim(http.responseText).split('**');		
		
		show_msg(reponse[0]); 	
			
		if(reponse[0]==0 || reponse[0]==1)
		{
			$("#update_id").val(reponse[2]);
			$("#txt_system_id").val(reponse[3]);
			$('#cbo_company_id').attr('disabled','disabled');
			$('#cbo_issue_purpose').attr('disabled','disabled');
			
			reset_form('finishFabricEntry_1','','','','','update_id*txt_system_id*cbo_company_id*cbo_issue_purpose*cbo_sample_type*txt_issue_date*txt_challan_no*cbo_sewing_source*cbo_sewing_company*txt_cut_req*cbo_store_name*txt_batch_no*hidden_batch_id*roll_maintained');

			show_list_view(reponse[2],'show_finish_fabric_issue_listview','div_details_list_view','requires/finish_fabric_issue_controller','');
			$('#txt_fabric_desc').focus();
			
			set_button_status(0, permission, 'fnc_fabric_issue_entry',1,1);	
		}	
		else if(reponse[0]==17)
		{
			alert(reponse[1]);
		}
		release_freezing();
	}
}

function set_form_data(data)
{
	var cbo_issue_purpose=$('#cbo_issue_purpose').val();
	var data=data.split("**");
	$('#hidden_prod_id').val(data[0]);
	$('#txt_fabric_desc').val(data[1]);
	$('#txt_rack').val(data[2]);
	$('#txt_shelf').val(data[3]);
	$('#txt_global_stock').val(data[4]);
	$('#txt_color').val(data[5]);
	$('#cbouom').val(data[6]);
	
	var hidden_batch_id=$('#hidden_batch_id').val();
	
	reset_form('','','save_data*txt_issue_qnty*txt_issue_req_qnty*all_po_id*txt_order_numbers*distribution_method_id*txt_fabric_received*txt_cumulative_issued*txt_yet_to_issue','','','');
	load_drop_down( 'requires/finish_fabric_issue_controller', data[0]+"**"+hidden_batch_id, 'load_drop_down_body_part', 'body_part_td' );
	var body_part_length=$("#cbo_body_part option").length;
	if(body_part_length==2)
	{
		$('#cbo_body_part').val($('#cbo_body_part option:last').val());
	}
	
	if($("#txt_issue_qnty").attr('readonly') == undefined)
	{
		get_php_form_data(hidden_batch_id+"**"+data[0], "populate_data_about_sample", "requires/finish_fabric_issue_controller" );
	}
	else
	{
		openmypage_po();
		/*if(cbo_issue_purpose==4 || cbo_issue_purpose==9)
		{
			openmypage_po();
		}*/
	}
}

function check_batch(data)
{
	var cbo_company_id=$('#cbo_company_id').val();
	var roll_maintained=$('#roll_maintained').val();
	var cbo_issue_purpose=$('#cbo_issue_purpose').val();
	
	if(form_validation('cbo_company_id','Company')==false)
	{
		$('#txt_batch_no').val('');
		$('#hidden_batch_id').val('');
		return;
	}
	
	var batch_data=return_global_ajax_value( data+"**"+cbo_company_id+"**"+cbo_issue_purpose, 'check_batch_no', '', 'requires/finish_fabric_issue_controller');
	batch_data=batch_data.split("**");
	var batch_id=trim(batch_data[0]);
	var without_order=batch_data[1];
	if(batch_id==0)
	{
		alert("Batch No Found");
		reset_form('','list_fabric_desc_container','txt_batch_no*hidden_batch_id*save_data*txt_issue_qnty*txt_issue_req_qnty*cbo_buyer_name*txt_cut_req*all_po_id*txt_order_numbers*distribution_method_id*txt_fabric_received*txt_cumulative_issued*txt_yet_to_issue*txt_fabric_desc*txt_rack*txt_shelf*hidden_prod_id*txt_global_stock*cbo_body_part','','','');
		return;
	}
	else
	{
		freeze_window(5);
		reset_form('','list_fabric_desc_container','txt_order_numbers*txt_fabric_received*txt_cumulative_issued*txt_yet_to_issue*txt_global_stock','','','');
		$('#hidden_batch_id').val(batch_id);
		
		if(cbo_issue_purpose==3 || cbo_issue_purpose==10 || cbo_issue_purpose==26 || cbo_issue_purpose==29 || cbo_issue_purpose==30 || cbo_issue_purpose==31)
		{
			po_fld_reset(without_order);
		}
		
		if(roll_maintained!=1)
		{
			show_list_view(batch_id,'show_fabric_desc_listview','list_fabric_desc_container','requires/finish_fabric_issue_controller','');
		}
		release_freezing();
	}
	
}

function js_set_value(id)
{
	var roll_maintained=$('#roll_maintained').val();
	get_php_form_data(id+"**"+roll_maintained,'populate_issue_details_form_data', 'requires/finish_fabric_issue_controller');
}
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission); ?><br />    		 
    <form name="finishFabricEntry_1" id="finishFabricEntry_1" autocomplete="off" >
    <div style="width:740px; float:left;" align="center"> 
        <fieldset style="width:730px;">
        <legend>Finish Fabric Issue Entry</legend>
        	<fieldset style="width:730px;">
                <table width="730" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>Issue No</strong></td>
                        <td colspan="3" align="left">
                            <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemId();" readonly />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">&nbsp;</td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Company</td>
                        <td>
                            <? 
								echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down( 'requires/finish_fabric_issue_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );load_drop_down( 'requires/finish_fabric_issue_controller', this.value, 'load_drop_down_store', 'store_td' );" );//get_php_form_data(this.value,'roll_maintained','requires/finish_fabric_issue_controller' );
							?>
                        </td>
                        <td class="must_entry_caption">Issue Purpose</td>
                        <td>
                            <?
                                echo create_drop_down("cbo_issue_purpose", 150,$yarn_issue_purpose,"", 0,"",'9',"active_inactive(this.value,0);",'','3,4,8,9,10,26,29,30,31');
                            ?>
                        </td>
                        <td>Sample Type</td>
                        <td>
                            <? 
								echo create_drop_down( "cbo_sample_type", 150, "select id, sample_name from lib_sample where status_active=1 and is_deleted=0 order by sample_name","id,sample_name", 1, "--Select Sample Type--", 0, "",1 );
							?>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Issue Date</td>
                        <td>
                            <input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:138px;" readonly placeholder="Select Date" />
                        </td>
                        <td>Challan No.</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:138px;" maxlength="20" title="Maximum 20 Character" />
                        </td>
                        <td class="must_entry_caption">Sewing Source</td>
                        <td>
                            <?
                                echo create_drop_down("cbo_sewing_source", 150, $knitting_source,"", 1,"-- Select Source --", 0,"load_drop_down( 'requires/finish_fabric_issue_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_sewing_com','sewingcom_td');","","","","","2");
                            ?>
                        </td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Sewing Company</td>
                        <td id="sewingcom_td">
                            <?
                                echo create_drop_down("cbo_sewing_company", 150, $blank_array,"", 1,"-- Select Sewing Company --", 0,"");
                            ?>
                        </td>
                        <td class="must_entry_caption">Buyer Name</td>
                        <td id="buyer_td_id">
                            <?
							   echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1 );  
 							?>
                        </td>
                        <td>Cutt. Req. No</td>
                        <td>
                            <input type="text" name="txt_cut_req" id="txt_cut_req" class="text_boxes_numeric" style="width:138px;" />
                        </td>
                    </tr>
                </table>
            </fieldset>
            <table width="730" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
                <tr>
                    <td width="68%" valign="top">
                        <fieldset>
                        <legend>New Entry</legend>
                            <table id="tbl_item_info"  cellpadding="0" cellspacing="1" width="100%">										
                                <tr>
                                	<td class="must_entry_caption" width="30%">Store Name</td>
                                    <td id="store_td">
                                        <?
                                            echo create_drop_down( "cbo_store_name", 170, "select id, store_name from lib_store_location where find_in_set(2,item_category_id) and status_active=1 and is_deleted=0 order by store_name","id,store_name", 1, "-- Select store --", 0, "" );
                                        ?>	
                                    </td>
                                </tr>
                                <tr>	
                                	<td class="must_entry_caption">Batch No.</td>
                                    <td>
                                        <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:158px;" placeholder="Write / Browse" onDblClick="openmypage_batchnum();" onChange="check_batch(this.value);"/>
                                        <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" readonly />
                                    </td>
                                </tr>						
                                <tr>
                                    <td class="must_entry_caption">Fabric Description</td>
                                    <td id="fabricDesc_td">
                                    	<input type="text" name="txt_fabric_desc" id="txt_fabric_desc" class="text_boxes" style="width:200px;" placeholder="Display" disabled />
                                        <input type="hidden" name="hidden_prod_id" id="hidden_prod_id" readonly>
                                        <span class="must_entry_caption">UOM</span>
										<?
                                        echo create_drop_down( "cbouom", 70, $unit_of_measurement,'', 1, '-Uom-', 12, "",1,"1,12,23,27" );
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Body Part</td>
                                    <td id="body_part_td">
                                         <?
                                            echo create_drop_down( "cbo_body_part", 170, $body_part,"", 1, "-- Select Body Part --", 0, "",0 );
                                         ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Garments Item</td>
                                    <td id="gmt_item_td">
                                         <?
										 echo create_drop_down( "cbo_item_name", 170, $garments_item,"", 1, "-- Select Gmt. Item --", "", "",0,0 );	
										 ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Color</td>						
                                    <td>
                                    	<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:158px" placeholder="Display" disabled />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Issue Qnty</td>
                                    <td>
                                    	<input type="text" name="txt_issue_qnty" id="txt_issue_qnty" class="text_boxes_numeric" style="width:158px;" readonly placeholder="Double Click To Search" onDblClick="openmypage_po();" /></td>
                                </tr>
                                <tr>
                                    <td>No. of Roll</td>						
                                    <td>
                                    	<input type="text" name="txt_no_of_roll" id="txt_no_of_roll" class="text_boxes_numeric" style="width:158px" /> <!--disabled="disabled" placeholder="Display"-->
                                    </td>
                                </tr>
                                <tr>
                                    <td>Rack</td>						
                                    <td>
                                    	<input type="text" name="txt_rack" id="txt_rack" class="text_boxes" style="width:158px" placeholder="Display" disabled />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Shelf</td>						
                                    <td>
                                    	<input type="text" name="txt_shelf" id="txt_shelf" class="text_boxes" style="width:158px" placeholder="Display" disabled/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Cutting Unit No</td>						
                                    <td>
                                    	<?
                                        echo create_drop_down( "cbo_cutting_floor", 170, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=1 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
										?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Remarks</td>						
                                    <td>
                                    	<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:300px" />
                                    </td>
                                </tr>
							</table>
						</fieldset>
					</td>
					<td width="2%" valign="top"></td>
					<td width="30%" valign="top">
						<fieldset>
                        <legend>Display</legend>					
                            <table id="tbl_display_info"  cellpadding="0" cellspacing="1" width="100%" >				
                                <tr>
                                    <td>Order Numbers</td>						
                                	<td>
                                    	<input type="text" name="txt_order_numbers" id="txt_order_numbers" class="text_boxes" style="width:100px" disabled />
                                    </td>
								</tr>
                                <tr>
                                    <td>Fabric Received</td>						
                                    <td><input type="text" name="txt_fabric_received" id="txt_fabric_received" class="text_boxes_numeric" style="width:100px" disabled /></td>
                                </tr>
                                <tr>
                                    <td>Cumulative Issued</td>
                                    <td><input type="text" name="txt_cumulative_issued" id="txt_cumulative_issued" class="text_boxes_numeric" style="width:100px" disabled /></td>
                                </tr>					
                                <tr>
                                    <td>Yet to Issue</td>
                                    <td><input type="text" name="txt_yet_to_issue" id="txt_yet_to_issue" class="text_boxes_numeric" style="width:100px" disabled /></td>
                                </tr>
                                <tr>
                                    <td>Global Stock</td>
                                    <td><input type="text" name="txt_global_stock" id="txt_global_stock" placeholder="Display" class="text_boxes" style="width:100px" readonly disabled /></td>
                                </tr>											
                            </table>                  
                       </fieldset>	
              		</td>
				</tr>	 	
                <tr>
                    <td align="center" colspan="3" class="button_container" width="100%">
                        
                        <input type="hidden" id="update_id" name="update_id" value="" >
                        <input type="hidden" name="save_data" id="save_data" readonly>
                        <input type="hidden" name="save_string" id="save_string" readonly>
                        <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                        <input type="hidden" name="update_trans_id" id="update_trans_id" readonly>
                        <input type="hidden" name="previous_prod_id" id="previous_prod_id" readonly>
                        <input type="hidden" name="hidden_issue_qnty" id="hidden_issue_qnty" readonly>
                        <input type="hidden" name="txt_issue_req_qnty" id="txt_issue_req_qnty" readonly>
                        <input type="hidden" name="all_po_id" id="all_po_id" readonly>
                        <input type="hidden" name="roll_maintained" id="roll_maintained" value="0" readonly>
                        <input type="hidden" name="distribution_method_id" id="distribution_method_id" readonly />
                        <input type="hidden" name="hidden_fabric_rate" id="hidden_fabric_rate" readonly />
                        
                        <?
                            echo load_submit_buttons($permission, "fnc_fabric_issue_entry", 0,1,"reset_form('finishFabricEntry_1','div_details_list_view*list_fabric_desc_container','','cbo_issue_purpose,9','disable_enable_fields(\'cbo_company_id*cbo_issue_purpose\');active_inactive(9,1);')",1);
                        ?>
                         <input type="button" name="print_vat" id="print_vat" value="Print With VAT" onClick="fnc_fabric_issue_entry(5)" style="width:100px" class="formbutton" />
                           <input type="button" name="print_3" id="print_3" value="Print 3" onClick="fnc_fabric_issue_entry(6)" style="width:70px" class="formbutton" />
                    </td>
                </tr>
            </table>
            <div style="width:730px;" id="div_details_list_view"></div>
		</fieldset>
	</div>
    <div id="list_fabric_desc_container" style="width:490px; margin-left:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
	</form>
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
