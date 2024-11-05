<?
/*************************************************Comments***********************************
* Purpose			: 	This form will create Dyes And Chemical Issue Requisition Entry     *
* Functionality	:                                                                           *
* JS Functions	:                                                                           *
* Created by		:	Fuad                                                               	*
* Creation date 	: 	11-11-2014                                                          *
* Updated by 		:                                           							*
* Update date		:                                                          				*
* QC Performed BY	:                                                                       *
* QC Date			:                                                                       *
* Comments		:                                                                           *
********************************************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_id=$_SESSION['logic_erp']['user_id'];
echo load_html_head_contents("Dyes And Chemical Issue Requisition","../../", 1, 1, $unicode,1,1);
//--------------------------------------------------------------------------------------------------------------------

?>

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var mandatory_field_arr="";
	<?php
		if($_SESSION['logic_erp']['mandatory_field'][156]!="")
		{
			$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][156] );
			echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
		}
	?>

	function rcv_basis_reset()
	{
		//document.getElementById('cbo_receive_basis').value=0;
		reset_form('chemicaldyesissuerequisition_1','list_container_recipe_items*recipe_items_list_view*last_update_message','','','','cbo_company_name');
	}

	// receipe no poup
	function openmypage_labdipNo()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var txt_recipe_id = $('#txt_recipe_id').val();

		if (form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		else
		{
			var title = 'Recipe No Selection Form';
			var page_link = 'requires/chemical_dyes_issue_requisition_controller.php?cbo_company_id='+cbo_company_id+'&recipe_id='+txt_recipe_id+'&action=labdip_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=390px,center=1,resize=1,scrolling=0','../');

			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var recipe_id=this.contentDoc.getElementById("hidden_recipe_id").value;
				var subprocess_id=this.contentDoc.getElementById("hidden_subprocess_id").value;
				var hidden_store_id=this.contentDoc.getElementById("hidden_store_id").value;
				$('#cbo_store_name').val(hidden_store_id);
				if(recipe_id!="")
				{
					freeze_window(5);
					get_php_form_data(recipe_id, "populate_data_from_recipe_popup", "requires/chemical_dyes_issue_requisition_controller" );
					var cbo_company_id= $('#cbo_company_name').val();
					show_list_view(cbo_company_id+'**'+subprocess_id+"**"+recipe_id, 'item_details', 'list_container_recipe_items', 'requires/chemical_dyes_issue_requisition_controller', '');
					document.getElementById('last_update_message').innerHTML = '';
					$('#is_apply_last_update').val(1);
					release_freezing();
				}
			}
		}
	}

    function multi_requisition_print(){
        if( form_validation('cbo_company_name','Working Company')==false ){
            return;
        }else {
            var cbo_company_id = $('#cbo_company_name').val();
            var title = 'Multiple Requisition Select Popup';
            var page_link = 'requires/chemical_dyes_issue_requisition_controller.php?cbo_company_id=' + cbo_company_id + '&action=multiple_requisition_popup';
            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=760px,height=380px,center=1,resize=1,scrolling=0', '../');
            emailwindow.onclose = function () {
                var theform = this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
                var requ_id = this.contentDoc.getElementById("hidden_requ_id").value;
                if (requ_id != "") {
                    freeze_window(5);
                    var report_title=$( "div.form_caption" ).html();
                    print_report( cbo_company_id+'*'+requ_id+'*'+report_title, "chemical_dyes_issue_requisition_for_multi", "requires/chemical_dyes_issue_requisition_controller" );
                    release_freezing();
                }
            }
        }
    }

	function apply_last_update()
	{
		if( form_validation('txt_mrr_no','Requisition Number')==false )
		{
			return;
		}
		$( "#last_update" ).click(function() {
  		  $( "#last_update" ).css('background', '#7e97bd');
 		 });
		var recipe_id= $('#txt_recipe_id').val();
		var cbo_company_id= $('#cbo_company_name').val();
		if(recipe_id!="")
		{
			freeze_window(5);
			get_php_form_data(recipe_id, "populate_data_from_recipe_popup", "requires/chemical_dyes_issue_requisition_controller" );
			var subprocess_id=return_global_ajax_value(recipe_id, 'get_subprocess_id', '', 'requires/chemical_dyes_issue_requisition_controller');
			show_list_view(cbo_company_id+'**'+subprocess_id+"**"+recipe_id,'item_details','list_container_recipe_items','requires/chemical_dyes_issue_requisition_controller','');
			$('#is_apply_last_update').val(1);
			release_freezing();
		}
	}

	function fnc_chemical_dyes_issue_requisition(operation)
	{
		//alert(operation);
		var issue_val="";
		if (operation==4 || operation==5 || operation==6)
		{
			if(operation==6)
			{
				var report_title=$( "div.form_caption" ).html();
				print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+operation+'*'+issue_val, "chemical_dyes_issue_requisition_print3", "requires/chemical_dyes_issue_requisition_controller" );
			}
			else
			{
				if(operation==5)
				{
					if( form_validation('cbo_company_name*txt_mrr_no','Company Name*Requisition No')==false )
					{
						return;
					}

					var r=confirm("Press  \"OK\"  to open without issue value\nPress  \"Cancel\"  to open with issue value");
					if (r==true) issue_val="1"; else issue_val="0";
				}
				var report_title=$( "div.form_caption" ).html();
				print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+operation+'*'+issue_val+'*'+$('#txt_batch_no').val(), "chemical_dyes_issue_requisition_print", "requires/chemical_dyes_issue_requisition_controller" );
				return;
			}

		}
		else if (operation==7)
		{
			if( form_validation('cbo_company_name*txt_mrr_no','Company Name*Requisition No')==false )
			{
				return;
			}
			var r=confirm("Press  \"OK\"  to open without issue value\nPress  \"Cancel\"  to open with issue value");
			if (r==true) issue_val="1"; else issue_val="0";
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+operation+'*'+issue_val, "chemical_dyes_issue_requisition_print_scandex", "requires/chemical_dyes_issue_requisition_controller" );
			return;
		}
		else if (operation==8)
		{
			if($('#update_id').val()=="")
			{
				alert("Select Save Data First....");
				return;
			}
			if( form_validation('cbo_company_name*txt_mrr_no','Company Name*Requisition No')==false )
			{
				return;
			}
			var r=confirm("Press  \"OK\"  to open without issue value\nPress  \"Cancel\"  to open with issue value");
			if (r==true) issue_val="1"; else issue_val="0";
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+operation+'*'+issue_val, "chemical_dyes_issue_requisition_print6", "requires/chemical_dyes_issue_requisition_controller" );
			return;
		}
		else if (operation==9)
		{
			if( form_validation('cbo_company_name*txt_mrr_no','Company Name*Requisition No')==false )
			{
				return;
			}
			var r=confirm("Press  \"OK\"  to open without issue value\nPress  \"Cancel\"  to open with issue value");
			if (r==true) issue_val="1"; else issue_val="0";
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+operation+'*'+issue_val, "chemical_dyes_issue_requisition_print7", "requires/chemical_dyes_issue_requisition_controller" );
			return;
		}

		/*else if (operation==5)
		{
			//alert(operation);
			if( form_validation('cbo_company_name*txt_mrr_no','Company Name*Requisition No')==false )
			{
				return; //'*'+type+'*'
			}
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+operation+'*'+report_title, "chemical_dyes_issue_requisition_print", "requires/chemical_dyes_issue_requisition_controller" );
			return;
		}*/
		else if(operation==0 || operation==1 || operation==2)
		{

			if(operation==2)
			{
				var yes=confirm('Are you sure to delete this?');
				if(yes==false)
				{
					return;
				}
			}

			/*if(operation==2)
			{
				show_msg('13');
				return;
			}*/

			if( form_validation('cbo_company_name*txt_requisition_date*txt_recipe_no*cbo_store_name','Company Name*Requisition Date*Recipe No*Store')==false )
			{
				return;
			}

			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][156]);?>')
			{
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][156]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][156]);?>')==false)
				{
					return;
				}
			}

			var mst_data=get_submitted_data_string('txt_mrr_no*update_id*cbo_company_name*cbo_location_name*txt_requisition_date*cbo_receive_basis*txt_batch_id*txt_recipe_id*cbo_method*machine_id*is_apply_last_update*cbo_store_name',"../../");

			var row_num=$('#tbl_list_search tbody tr').length;
			for (var i=1; i<=row_num; i++)
			{
				mst_data=mst_data+get_submitted_data_string('txt_prod_id_'+i+'*txt_item_cat_'+i+'*cbo_dose_base_'+i+'*txt_ratio_'+i+'*txt_recipe_qnty_'+i+'*txt_adj_per_'+i+'*cbo_adj_type_'+i+'*txt_reqn_qnty_'+i+'*reqn_qnty_edit_'+i+'*updateIdDtls_'+i+'*txt_subprocess_id_'+i+'*txt_seq_no_'+i+'*txt_lot_'+i+'*txt_sub_seq_no_'+i+'*txt_comment_'+i,"../../",i)
			}

			var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+mst_data;
			freeze_window(operation);
			http.open("POST","requires/chemical_dyes_issue_requisition_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_chemical_dyes_issue_requisition_reponse;
		}
	}

	function fnc_chemical_dyes_issue_requisition_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			show_msg(reponse[0]);

			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('txt_mrr_no').value = reponse[1];
				document.getElementById('update_id').value = reponse[2];

				$('#cbo_company_name').attr('disabled','disabled');
				$('#txt_recipe_no').attr('disabled','disabled');
				$('#cbo_store_name').attr('disabled','disabled');
				document.getElementById('last_update_message').innerHTML = '';
				//reset_form( '', 'list_container_recipe_items', '', '', '', '' );
				show_list_view(reponse[2], 'item_details_for_update', 'list_container_recipe_items', 'requires/chemical_dyes_issue_requisition_controller', '');
				set_button_status(1, permission, 'fnc_chemical_dyes_issue_requisition',1,1);
			}
			else if(reponse[0]==13)
			{
				alert("Issue Found="+reponse[2]);
				release_freezing();
				return;
			}
			else if(reponse[0]==2)
			{
				fnResetForm();
			}
			release_freezing();
		}
	}

	function open_mrrpopup()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var page_link='requires/chemical_dyes_issue_requisition_controller.php?action=mrr_popup&company='+company;
		var title="Requisition Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var reqnId=this.contentDoc.getElementById("hidden_sys_id").value;

			reset_form('','list_container_recipe_items*recipe_items_list_view*last_update_message','','','','');
			$('#txt_recipe_no').attr('disabled','disabled');
			get_php_form_data(reqnId, "populate_data_from_data", "requires/chemical_dyes_issue_requisition_controller");
			show_list_view(reqnId, 'item_details_for_update', 'list_container_recipe_items', 'requires/chemical_dyes_issue_requisition_controller', '');
			$('#cbo_store_name').attr('disabled','disabled');
		}
	}

	function calculate_requs_qty(i)
	{
		var txt_adj_per = $("#txt_adj_per_"+i).val()*1;
		var cbo_adj_type = $("#cbo_adj_type_"+i).val();
	    var recipe_qnty=$("#txt_recipe_qnty_"+i).val()*1;

		var requisition_qty=0;

		var adj_qty=(txt_adj_per*recipe_qnty)/100;

		if(cbo_adj_type==1)
		{
			requisition_qty=recipe_qnty+adj_qty;
		}
		else if(cbo_adj_type==2)
		{
			requisition_qty=recipe_qnty-adj_qty;
		}
		else
		{
			var requisition_qty=recipe_qnty;
		}

		$("#reqn_qnty_edit_"+i).val(requisition_qty.toFixed(6));
		$("#txt_reqn_qnty_"+i).val(requisition_qty.toFixed(6));
		//$("#reqn_qnty_edit_"+i).val(number_format_common(requisition_qty,5,0));
		//$("#txt_reqn_qnty_"+i).val(number_format_common(requisition_qty,5,0));
	}

	function fn_machine_seach()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var txt_recipe_id = $('#txt_recipe_id').val();

		if (form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		else
		{
			var title = 'Machine No Selection Form';
			var page_link = 'requires/chemical_dyes_issue_requisition_controller.php?cbo_company_id='+cbo_company_id+'&txt_recipe_id='+txt_recipe_id+'&action=machineNo_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=755px,height=350px,center=1,resize=1,scrolling=0','../');

			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var machine_id=this.contentDoc.getElementById("hidden_machine_id").value;
				var machine_name=this.contentDoc.getElementById("hidden_machine_name").value;

				$('#machine_id').val(machine_id);
				$('#txt_machine_no').val(machine_name);
			}
		}

	}

	function without_rate_fnc(type)
	{
		if( form_validation('txt_mrr_no*cbo_company_name*txt_requisition_date*txt_recipe_no','Requisition Number*Company Name*Requisition Date*Recipe No')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
			if(type==1)
			{
				print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, "chemical_dyes_issue_requisition_without_rate_print", "requires/chemical_dyes_issue_requisition_controller" );
			}
			else if(type==3)
			{
				print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, "chemical_dyes_issue_requisition_without_rate_print_urmi", "requires/chemical_dyes_issue_requisition_controller" );
			}
			else if(type==4)
			{
				print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, "chemical_dyes_issue_requisition_without_rate_print_3", "requires/chemical_dyes_issue_requisition_controller" );
			}
			else if(type==5)
			{
				print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, "chemical_dyes_issue_requisition_without_rate_print_4", "requires/chemical_dyes_issue_requisition_controller" );
			}
			else if(type==7)
			{
				print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, "chemical_dyes_issue_requisition_without_rate_print_5", "requires/chemical_dyes_issue_requisition_controller" );
			}
			else if(type==8)
            {
                print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, "chemical_dyes_issue_requisition_without_rate_print_6", "requires/chemical_dyes_issue_requisition_controller" );
            }
			else if(type==9)
            {
                print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, "chemical_dyes_issue_requisition_without_rate_print_7", "requires/chemical_dyes_issue_requisition_controller" );
            }
            else if(type==10)
			{
				var dataStr=$('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title;
				// print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, "chemical_dyes_issue_requisition_without_rate_print_10", "requires/chemical_dyes_issue_requisition_controller" );
				 // var url=return_ajax_request_value(dataStr, "chemical_dyes_issue_requisition_without_rate_print_10", "requires/chemical_dyes_issue_requisition_controller");
				// alert(data);
				var action_type="chemical_dyes_issue_requisition_without_rate_print_10";
				var data="action="+action_type+get_submitted_data_string('cbo_company_name*update_id',"../../")+'&data='+dataStr+'&report_title='+report_title;
				//alert(data);return;
				var user_id = "<? echo $user_id; ?>";
				$.ajax({
					url: 'requires/chemical_dyes_issue_requisition_controller.php',
					type: 'POST',
					data: data,
					success: function(data){
						window.open('requires/chemiDyesReq_'+user_id+'.pdf');	
						//release_freezing();	
					}
				});

				// window.open(url,"##");
			}
			else if(type==11)
			{
				print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, "chemical_dyes_issue_requisition_without_rate_print_11", "requires/chemical_dyes_issue_requisition_controller" );
			}
			else
			{
				print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, "chemical_dyes_issue_requisition_without_rate_print_2", "requires/chemical_dyes_issue_requisition_controller" );
			}

		return;
	}

	function print_adding_topping(type)
	{
		if( form_validation('txt_mrr_no','Requisition Number')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		if(type==1)
		{
	  		print_report($('#update_id').val()+'*'+report_title, "print_adding_topping_without_rate_print", "requires/chemical_dyes_issue_requisition_controller" );
	  		return;
		}
		else
		{
			print_report($('#update_id').val()+'*'+report_title, "print_adding_topping", "requires/chemical_dyes_issue_requisition_controller" );
			return;
		}
	}
	function fnResetForm()
	{
		//alert(33);
		$("#cbo_company_name").attr("disabled",false);
		//disable_enable_fields(\'cbo_company_id*cbo_company_id\',0)
		//reset_form('slittingsqueezing_1','','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');
		reset_form('chemicaldyesissuerequisition_1','list_container_recipe_items*recipe_items_list_view*last_update_message','','','','');
	}

	function dataExchange(operation,type)
	{

			//var recipe_id=$('#txt_recipe_no').val();
			if( form_validation('txt_mrr_no','Requisition Number')==false )
			{
				return;
			}
			var mst_data=get_submitted_data_string('txt_mrr_no*update_id*txt_recipe_no',"../../");
			if(type==1)
			{
				var data="action=data_exchange&operation="+operation+mst_data;
			}
			else
			{
				var data="action=data_exchange_libas&operation="+operation+mst_data;
			}

			http.open("POST","requires/chemical_dyes_issue_requisition_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = data_exchange_reponse;
	}

	function data_exchange_reponse()
	{
		if(http.readyState == 4)
		{

			var reponse=trim(http.responseText).split('**');
			//alert(reponse);
			if((reponse[0]==0 || reponse[0]==1))
			{
				alert("Exchange Successfull")  ;
			}
			//show_msg(reponse[0]);

			/*if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('txt_mrr_no').value = reponse[1];
				document.getElementById('update_id').value = reponse[2];

				$('#cbo_company_name').attr('disabled','disabled');
				document.getElementById('last_update_message').innerHTML = '';
				//reset_form( '', 'list_container_recipe_items', '', '', '', '' );
				show_list_view(reponse[2], 'item_details_for_update', 'list_container_recipe_items', 'requires/chemical_dyes_issue_requisition_controller', '');
				set_button_status(1, permission, 'fnc_chemical_dyes_issue_requisition',1,1);
			}*/
			release_freezing();
		}
	}
	function fnc_send_printer_text(type)
	{
			if( form_validation('txt_mrr_no','Requisition Number')==false )
			{
				return;
			}
			//var mst_data=get_submitted_data_string('txt_mrr_no*update_id*txt_recipe_no',"../../");

		var recipe_no=$('#txt_recipe_no').val();
		var mst_id=$('#update_id').val();
		var batch_no=$('#txt_batch_no').val();
		var batch_id=$('#txt_batch_id').val();
		if(mst_id=="")
		{
			alert("Save First");
			return;
		}
		var data="";


		data=mst_id+"***"+recipe_no+"***"+batch_no+"***"+batch_id;
		if (type==1)
		{
			var url=return_ajax_request_value(data, "report_sendtoprint_text_file", "requires/chemical_dyes_issue_requisition_controller");
		}
		else if(type==2)
		{
			var url=return_ajax_request_value(data, "report_artexport_text_file", "requires/chemical_dyes_issue_requisition_controller");
		}
		else if(type==3)
		{
			var url=return_ajax_request_value(data, "report_artexport2_text_file", "requires/chemical_dyes_issue_requisition_controller");
		}

		// alert(url);
		//alert(data);return;

		//var txt_file=$('#txt_file').val(url);
		//document.getElementById('txt_file').value = url;
		 $('#txt_file').removeAttr('href').attr('href','requires/'+trim(url));
            //$('#print_report4')[0].click();
        document.getElementById('txt_file').click();
		//window.open("requires/"+trim(url)+".txt","##");
	}

function print_report_button_setting(report_ids){
	$("#print1").hide();
	$("#adding_topping").hide();
	$("#without_rate").hide();
	$("#without_rate_2").hide();
	$("#without_rate_urmi").hide();
	$("#without_rate_4").hide();
	$("#adding_topping_without_rate").hide();
	$("#print2").hide();
	$("#btb_print3").hide();
	$("#without_rate_5").hide();
	$("#without_rate_6").hide();
	$("#without_rate_7").hide();
	$("#print4").hide();
	$("#print_5").hide();
	$("#print6").hide();
	$("#print7").hide();
	var report_id=report_ids.split(",");

	for (var k=0; k<report_id.length; k++){
		if(report_id[k]==78){
			$("#print1").show();
		}
		else if(report_id[k]==188){
			$("#adding_topping").show();
		}
		else if(report_id[k]==80){
			$("#without_rate").show();
		}
		else if(report_id[k]==189){
			$("#without_rate_2").show();
		}
		else if(report_id[k]==190){
			$("#adding_topping_without_rate").show();
		}
		else if(report_id[k]==210){
			$("#without_rate_urmi").show();
			//alert(210);
		}
		else if(report_id[k]==130){
			$("#without_rate_4").show();
		}
		else if(report_id[k]==121){
			$("#print2").show();
		}
		else if(report_id[k]==85){
			$("#btb_print3").show();
		}
		else if(report_id[k]==440){
            $("#without_rate_5").show();
        }
		else if(report_id[k]==441){
            $("#without_rate_6").show();
        }
		else if(report_id[k]==132){
            $("#without_rate_7").show();
        }
		else if(report_id[k]==807){
            $("#print_5").show();
        }
		else if(report_id[k]==137){
			$("#print4").show();
		}
		else if(report_id[k]==72){
			$("#print6").show();
		}
		else if(report_id[k]==191){
			$("#print7").show();
		}
	}
}



</script>
<style>
    .formbuttonplasminus{
        margin-top: 8px;
    }
	.formbuttonplasminus:hover, .formbuttonplasminus:focus {
	cursor:pointer;
	border:outset 1px #CC00FF;
	background:#C2DCFF;
	color:#171717;
	font-weight:bold;
	padding: 1px 2px;
	border-radius:8px;
}
#last_update:hover {background-color:#6394bf}
#last_update:active {
  background-color:#d17394;
  box-shadow: 0 3px #666;
  transform: translateY(2px);
}
</style>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission);  ?>
        <form name="chemicaldyesissuerequisition_1" id="chemicaldyesissuerequisition_1" autocomplete="off" >
    		<div style="width:1000px;">
            	<fieldset style="width:1000px;">
                <legend>Dyes And Chemical Issue Requisition</legend>
                	<table width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                        <tr>
                            <td colspan="6" align="center">&nbsp;<b>Requisition Number</b>
                                <input type="text" name="txt_mrr_no" id="txt_mrr_no" class="text_boxes" style="width:155px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly /> <input type="hidden" name="update_id" id="update_id" />
                            </td>
                       </tr>
                       <tr>
                           <td width="130" align="right" class="must_entry_caption">Working Company </td>
                           <td width="170">
                                <?
                                	echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "rcv_basis_reset();load_drop_down( 'requires/chemical_dyes_issue_requisition_controller', this.value, 'load_drop_down_location', 'location_td');get_php_form_data( this.value, 'print_report_button_setting', 'requires/chemical_dyes_issue_requisition_controller' );","");
                                ?>
                           </td>
                           <td width="130" align="right"> Location </td>
                           <td width="170" id="location_td">
                               <? echo create_drop_down( "cbo_location_name", 170, $blank_array,"", 1, "-- Select Location --", 0, "" ); ?>
                           </td>
                           <td  width="130" align="right" class="must_entry_caption" >Requisition Date </td>
                           <td width="170">
                               <input type="text" name="txt_requisition_date" id="txt_requisition_date" class="datepicker" style="width:160px;" placeholder="Select Date" />
                           </td>
                        </tr>
                        <tr>
                           <td align="right" class="must_entry_caption" > Issue Basis </td>
                           <td>
                                <? echo create_drop_down("cbo_receive_basis",170,$receive_basis_arr,"",0,"- Select Basis -",8,"","1","8"); ?>
                           </td>
                           <td align="right" class="must_entry_caption">Recipe No. </td>
                           <td>
                               <input class="text_boxes"  type="text" name="txt_recipe_no" id="txt_recipe_no" onDblClick="openmypage_labdipNo();" placeholder="Double Click" style="width:160px;"    />
                               <input class="text_boxes"  type="hidden" name="txt_recipe_id" id="txt_recipe_id"style="width:160px;"    />
							   
                           </td>
                           <td align="right">Batch No </td>
                           <td>
                                <input class="text_boxes" type="text" name="txt_batch_no" id="txt_batch_no" disabled placeholder="Display" style="width:160px;" />
                                <input class="text_boxes" type="hidden" name="txt_batch_id" id="txt_batch_id" />
                           </td>
                        </tr>
                        <tr>
                           <td align="right">Method</td>
                           <td><? echo create_drop_down( "cbo_method", 170, $dyeing_method,"", 1, "--Select Method--", $selected, "",0 ); ?></td>
                           <td align="right" style="display:none"> Total Liquor (ltr)</td>
                           <td style="display:none">
                                <input type="text" name="txt_tot_liquor" id="txt_tot_liquor" class="text_boxes_numeric" style="width:160px;display:none" placeholder="Display" disabled />
                           </td>
                           <td align="right"> Batch Weight </td>
                           <td>
                                <input type="text" name="txt_batch_weight" id="txt_batch_weight" class="text_boxes_numeric" style="width:160px;" placeholder="Display" disabled />
                           </td>
                            <td align="right">Machine No</td>
                           <td>
                                <input type="text" name="txt_machine_no" id="txt_machine_no" class="text_boxes" style="width:158px;" onDblClick="fn_machine_seach();" placeholder="Browse" readonly/>
                                <input type="hidden" name="machine_id" id="machine_id" class="text_boxes"/>
                           </td>
                       </tr>
                       <tr>
                           <td align="right" class="must_entry_caption">Store Name</td>
                           <td><? echo create_drop_down( "cbo_store_name", 160, "select lib_store_location.id,lib_store_location.store_name from lib_store_location,lib_store_location_category where lib_store_location.id=lib_store_location_category.store_location_id and lib_store_location.status_active=1 and lib_store_location.is_deleted=0  and lib_store_location_category.category_type in(5,6,7,23) group by lib_store_location.id,lib_store_location.store_name order by lib_store_location.store_name","id,store_name", 1, "-- Select Store --", $storeName, "",1 ); ?></td>
                           <td align="right"></td>
                           <td></td>
                           <td align="right"></td>
                           <td></td>
                       </tr>

					</table>
				</fieldset>
			</div>
            <div style="width:1160px;">
 
                <fieldset>
                    <table cellpadding="0" cellspacing="1" width="100%">
                        <tr>
                           <td colspan="6" align="center"><div style="color:#F00; font-size:18px" id="last_update_message"></div></td>
                        </tr>
                        <tr>
                           <td colspan="6" align="center"><div id="list_container_recipe_items" style="margin-top:10px"><span class="button_container">
                          </span></div></td>
                        </tr>
                        <tr>
                            <td align="center" colspan="5" width="65%" valign="middle" class="button_container">
                            	<input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>

								<?
								//	reset_form('chemicaldyesissuerequisition_1','list_container_recipe_items*recipe_items_list_view*last_update_message','','','','');

								echo load_submit_buttons($permission, "fnc_chemical_dyes_issue_requisition", 0,0,"fnResetForm();",1); ?>

								<span style="position: relative; " >
                                <input type="button" name="adding_topping" class="formbuttonplasminus" value="Adding/Topping" id="adding_topping" onClick="print_adding_topping();" style="display:none;"/>
                                <input type="button" name="without_rate" class="formbuttonplasminus" value="Without Rate" id="without_rate" onClick="without_rate_fnc(1);" style="display:none;"/>
                                 <input type="button" name="without_rate_2" class="formbuttonplasminus" value="Without Rate 2" id="without_rate_2" onClick="without_rate_fnc(2);" style="display:none;"/>
								 <input type="button" name="without_rate_urmi" class="formbuttonplasminus" value="Without Rate Urmi" id="without_rate_urmi" onClick="without_rate_fnc(3);" style="display:none;"/>
								  <input type="button" name="without_rate_4" class="formbuttonplasminus" value="Requisition Print" id="without_rate_4" onClick="without_rate_fnc(4);" style="display:none;"/>

								  <input type="button" name="without_rate_5" class="formbuttonplasminus" value="Requisition Print1" id="without_rate_5" onClick="without_rate_fnc(5);" style=""/>
                                  <input type="button" name="without_rate_6" class="formbuttonplasminus" value="Requisition Print2" id="without_rate_6" onClick="without_rate_fnc(7);" style=""/>
                                  <input type="button" name="without_rate_7" class="formbuttonplasminus" value="Requisition Print3" id="without_rate_7" onClick="without_rate_fnc(8);" style="display:none;"/>
                                  <input type="button" name="print_5" class="formbuttonplasminus" value="Requisition Print4" id="print_5" onClick="without_rate_fnc(11);" style="display:none;"/>
                                <input type="button" name="adding_topping_without_rate" class="formbuttonplasminus" value="Adding/Topping Without Rate" id="adding_topping_without_rate" style="margin-left:0px; display:none;" onClick="print_adding_topping(1);" />
                                </span>

                                <input type="button" name="last_update" class="formbuttonplasminus" value="Apply Last Update" id="last_update" onClick="apply_last_update();" />

                                <input type="hidden" name="is_apply_last_update" id="is_apply_last_update" value="0">
                                <input type="button" name="data_exchange" class="formbuttonplasminus" value=" Logic Art " id="data_exchange" onClick="dataExchange(0,1);"/>

                                 <input type="button" name="data_exchange_libas" class="formbuttonplasminus" value="Data Exchange - Enmous" id="data_exchange_libas" onClick="dataExchange(0,2);"/>
                                 <input type="button" name="data_exchange_ods" class="formbuttonplasminus" value="Send To Print" id="data_exchange_ods" onClick="fnc_send_printer_text(1);"/> &nbsp;<a id="txt_file" href="" style="text-decoration:none" download hidden>BB</a>

                                 <input type="button" name="logic_art_export" class="formbuttonplasminus" value="Logic Art Export" id="logic_art_export" onClick="fnc_send_printer_text(2);"/> &nbsp;<a id="txt_file" href="" style="text-decoration:none" download hidden>BB</a>
								 <input type="button" name="logic_art_export" class="formbuttonplasminus" value="Logic Art Export2" id="logic_art_export" onClick="fnc_send_printer_text(3);"/> &nbsp;<a id="txt_file" href="" style="text-decoration:none" download hidden>BB</a>

								 <input type="button" name="print1" class="formbuttonplasminus" value="Print" id="print1" onClick="fnc_chemical_dyes_issue_requisition(4);" style="width:80px; display:none;"/>
                                <input type="button" name="print2" class="formbuttonplasminus" value="Print Report 2" id="print2" onClick="fnc_chemical_dyes_issue_requisition(5);" style="display:none;"/>
                                <input type="button" name="btb_print3" class="formbuttonplasminus" value="Print3" id="btb_print3" onClick="fnc_chemical_dyes_issue_requisition(6);" style="width:80px; display:none;"/>
                                <input type="button" name="print4" class="formbuttonplasminus" value="Print4" id="print4" onClick="without_rate_fnc(9);" style="width:80px; display:none;"/>
                                <input type="button" name="print_multi" class="formbuttonplasminus" value="Print Multiple Requisition" id="print_multi" onClick="multi_requisition_print();" style="width:146px;"/>
                                <input type="button" name="print_scan" class="formbuttonplasminus" value="Print 5" id="print_scan" onClick="fnc_chemical_dyes_issue_requisition(7);"/>
								<input type="button" name="print6" class="formbuttonplasminus" value="Print 6" id="print6" onClick="fnc_chemical_dyes_issue_requisition(8);"/>
                                <input type="button" name="without_rate_10" class="formbuttonplasminus" value="Requisition-PAL" id="without_rate_10" onClick="without_rate_fnc(10);" style=""/>
								<input type="button" name="print7" class="formbuttonplasminus" value="Print 7" id="print7" onClick="fnc_chemical_dyes_issue_requisition(9);"/>


                            </td>
                          <tr>
                          	<td></td>
                          </tr>
                       	</tr>
                       	<tr>
                           	<td colspan="6" align="center"><div id="recipe_items_list_view" style="margin-top:10px"></div></td>
                    	</tr>
                    </table>
              	</fieldset>
    		</div>
		</form>
	</div>
</body>
<script>
	$(document).ready(function() {
		for (var property in mandatory_field_arr) {
			$("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
		}
	});
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
		$('#txt_requisition_date').datepicker({
			dateFormat: 'dd-M-yy',
		    minDate: 0
		});
	});
</script>
</html>
