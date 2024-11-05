<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create print imbro receive

Functionality	:
JS Functions	:
Created by		:	Bilas
Creation date 	: 	25-02-2013
Updated by 		: 	Kausar (Creating Print Report )
Update date		: 	08-01-2014
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
// echo "<pre>";
// print_r($_SESSION['logic_erp']['mandatory_field'][602][2]);die;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Print Embro Receive Info","../", 1, 1, $unicode);
function arrayExclude($array,Array $excludeKeys){
    foreach($array as $key => $value){
        if(!in_array($key, $excludeKeys)){
            $return[$key] = $value;
        }
    }
    return $return;
} 

if ($db_type == 0) {
    $sending_location="select concat(b.id,'*',a.id) id,concat(b.location_name,':',a.company_name) location_name from lib_company a, lib_location b where a.id=b.company_id and b.status_active =1 and b.is_deleted=0 and a.status_active =1 and a.is_deleted=0 order by a.company_name";
} else if ($db_type == 2 || $db_type == 1) {
    $sending_location="select b.id||'*'||a.id as id, b.location_name||' : '||a.company_name as location_name  from lib_company a, lib_location b where a.id=b.company_id and b.status_active =1 and b.is_deleted=0 and a.status_active =1 and a.is_deleted=0 order by a.company_name";
}
?>

<script>
 
var field_level_menual = 1;
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

<? $data_arr = json_encode($_SESSION['logic_erp']['data_arr'][727]);
if ($data_arr){
	echo "var field_level_data= " . $data_arr . ";\n";
}
?>

// order popup function here
function dynamic_must_entry_caption(data)
{
 	if(data==1)
	{
		$('#locations').css('color','blue');
		$('#floors').css('color','blue');

	}
	else
	{
		$('#locations').css('color','black');
		$('#floors').css('color','black');

	}

}


	function openmypage(page_link,title)
	{
		//	if ( form_validation('cbo_company_name','Company Name')==false )
		//	{
		//		return;
		//	}
		//	else
		//	{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=370px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var po_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
				var item_id=this.contentDoc.getElementById("hidden_grmtItem_id").value;
				var po_qnty=this.contentDoc.getElementById("hidden_po_qnty").value;
				var country_id=this.contentDoc.getElementById("hidden_country_id").value;
				var company_id=this.contentDoc.getElementById("hidden_company_id").value;
				var country_ship_date=this.contentDoc.getElementById("hidden_country_ship_date").value;
                
				get_php_form_data(company_id,'load_variable_settings','requires/print_embro_receive_controller');
				get_php_form_data(company_id,'load_variable_settings_reject','requires/print_embro_receive_controller');
				print_button_setting(company_id);

				if (po_id!="")
				{
					freeze_window(5);
					//alert(country_ship_date);
					$("#txt_order_qty").val(po_qnty);
					$('#cbo_item_name').val(item_id);
					$("#cbo_country_name").val(country_id);
					$("#cbo_company_name").val(company_id);
					childFormReset();//child form initialize
					$('#cbo_embel_name').val(0);
					$('#cbo_embel_type').val(0);
					$("#country_ship_date").val(country_ship_date);
					get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_embro_receive_controller" );

					var variableSettings=$('#sewing_production_variable').val();
					var variableSettingsReject=$('#embro_production_variable').val();
					var styleOrOrderWisw=$('#styleOrOrderWisw').val();
					/*if(variableSettings!=1){
						get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+variableSettingsReject, "color_and_size_level", "requires/print_embro_receive_controller" );
					}
					else
					{
						$("#txt_receive_qty").removeAttr("readonly");
					}*/

					if(variableSettings==1)
					{
						$("#txt_receive_qty").removeAttr("readonly");
					}
					else
					{
						$('#txt_receive_qty').attr('readonly','readonly');
					}

					if(variableSettingsReject!=1)
					{
						$("#txt_reject_qty").attr("readonly");
					}
					else
					{
						$("#txt_reject_qty").removeAttr("readonly");
					}
					//$('#cbo_embel_name').val(1);
					//$('#cbo_embel_name').trigger('change');

					show_list_view(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id+'**1','show_dtls_listview','printing_production_list_view','requires/print_embro_receive_controller','');
					setFilterGrid("tbl_search",-1);
					show_list_view(po_id,'show_country_listview','list_view_country','requires/print_embro_receive_controller','');
					set_button_status(0, permission, 'fnc_receive_print_embroidery_entry',1,0);
					load_drop_down( 'requires/print_embro_receive_controller', po_id, 'load_drop_down_color_type', 'color_type_td');
					setFieldLevelAccess(company_id);
					// alert(company_id);
					release_freezing();
				}
				$("#cbo_company_name").attr("disabled","disabled");
			}
//		}//end else
	}//end function

	function uploadFile(mst_id)
	{
		// alert(mst_id)
		$(document).ready(function() {
			var suc=0;
			var fail=0;
			for( var i = 0 ; i < $("#cbo_file")[0].files.length ; i++)
			{
				var fd = new FormData();
				console.log($("#cbo_file")[0].files[i]);
				var files = $("#cbo_file")[0].files[i];
				fd.append('file', files);
				$.ajax({
					url: 'requires/print_embro_receive_controller.php?action=file_upload&mst_id='+ mst_id,
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
							alert('file not uploaded');
							fail++;
						}
					},
				});
			}

			if(suc > 0 )
			{
				document.getElementById('cbo_file').value='';
			}
		});
	}

	// embrodery receive save here
	function fnc_receive_print_embroidery_entry(operation)
	{
		//alert("YES");
		var source=$("#cbo_source").val();
		if(operation==4)
		{ //embro_production_variable
			var master_ids = ""; var total_tr=$('#tbl_search tr').length;
			for(i=1; i<=total_tr; i++)
			{
				try
				{
					if ($('#tbl_'+i).is(":checked"))
					{
						master_id = $('#mstidall_'+i).val();
						if(master_ids=="") master_ids= master_id; else master_ids +='_'+master_id;
					}
				}
				catch(e)
				{
					//got error no operation
				}
			}
			//alert(master_ids);
			if(master_ids=="")
			{
				alert("Please Select At Least One Item");
				return;
			}
			// alert($('#txt_mst_id_all').val());
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title+'*'+$("#sewing_production_variable").val(), "emblishment_receive_print", "requires/print_embro_receive_controller" )
			return;
		}
		if(operation==6)
		{ //embro_production_variable
			var master_ids = ""; var total_tr=$('#tbl_search tr').length;
			for(i=1; i<=total_tr; i++)
			{
				try
				{
					if ($('#tbl_'+i).is(":checked"))
					{
						master_id = $('#mstidall_'+i).val();

						if(master_ids=="") master_ids= master_id; else master_ids +='_'+master_id;
					}
				}
				catch(e)
				{
					//got error no operation
				}
			}
			//alert(master_ids);
			if(master_ids=="")
			{
				alert("Please Select At Least One Item");
				return;
			}
			// alert($('#txt_mst_id_all').val());
			var report_title=$("div.form_caption").html();
			print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title+'*'+$("#sewing_production_variable").val(), "emblishment_receive_print5", "requires/print_embro_receive_controller" )
			return;
		}
		if(operation==5)
		{ //embro_production_variable
			var master_ids = ""; var total_tr=$('#tbl_search tr').length;
			for(i=1; i<=total_tr; i++)
			{
				try
				{
					if ($('#tbl_'+i).is(":checked"))
					{
						master_id = $('#mstidall_'+i).val();

						if(master_ids=="") master_ids= master_id; else master_ids +='_'+master_id;
					}
				}
				catch(e)
				{
					//got error no operation
				}
			}
			//alert(master_ids);
			if(master_ids=="")
			{
				alert("Please Select At Least One Item");
				return;
			}
			// alert($('#txt_mst_id_all').val());
			var report_title=$("div.form_caption").html();
			print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title+'*'+$("#sewing_production_variable").val(), "emblishment_receive_print_2", "requires/print_embro_receive_controller" )
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if('<? echo $_SESSION['logic_erp']['mandatory_field'][602][1]; ?>')
			{
				if (form_validation('<? echo $_SESSION['logic_erp']['mandatory_field'][602][1]; ?>','<? echo $_SESSION['logic_erp']['mandatory_message'][602][1]; ?>')==false) {return;}
			}

			if('<? echo $_SESSION['logic_erp']['mandatory_field'][602][3]; ?>') 
			{
				if (form_validation('<? echo $_SESSION['logic_erp']['mandatory_field'][602][3]; ?>','<? echo $_SESSION['logic_erp']['mandatory_message'][602][3]; ?>')==false) {return;}
			}

			if($("#txt_file").val()==''){

				if('<? echo  $_SESSION['logic_erp']['mandatory_field'][602][2]; ?>') 
				{
					if (form_validation('<? echo $_SESSION['logic_erp']['mandatory_field'][602][2]; ?>', '<? echo $_SESSION['logic_erp']['mandatory_message'][602][2]; ?>')==false) {return;}
				}
			}
			
			 
			if(form_validation('cbo_company_name*txt_order_no*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*txt_receive_date*txt_receive_qty*txt_challan','Company Name*Order No*Embel. Name* Embel. Type*Source*Embel.Company*Receive Date*Receive Quantity*Challan No')==false)
			{
				return;
			}
			else
			{
				if(source==1)
				{
					if(form_validation('cbo_location*cbo_floor','Location*Floor')==false)
					{
						return;
					}
				}
				var current_date='<? echo date("d-m-Y"); ?>';
				if(date_compare($('#txt_receive_date').val(), current_date)==false)
				{
					alert("Embel Receive Date Can not Be Greater Than Current Date");
					return;
				}

				freeze_window(operation);
				var sewing_production_variable = $("#sewing_production_variable").val();
				var variableSettingsReject=$('#embro_production_variable').val();
				var colorList = ($('#hidden_colorSizeID').val()).split(",");

				var i=0;  var k=0; var colorIDvalue=''; var colorIDvalueRej='';
				var colorBundleVal=""; var j=0;
				if(sewing_production_variable==2)//color level
				{
					$("input[name=txt_color]").each(function(index, element) {
						if( $(this).val()!='' )
						{
							if(i==0)
							{
								colorIDvalue = colorList[i]+"*"+$(this).val();
							}
							else
							{
								colorIDvalue += "**"+colorList[i]+"*"+$(this).val();
							}
						}
						i++;
					});
					$("input[name=colorSizeBundleQnty]").each(function(index, element) {
						if( $(this).val()!='' )
						{
							if(j==0)
							{
								colorBundleVal = colorList[j]+"*"+$(this).val();
							}
							else
							{
								colorBundleVal += "**"+colorList[j]+"*"+$(this).val();
							}
						}
						j++;

					});
				}
				else if(sewing_production_variable==3)//color and size level
				{
					$("input[name=colorSize]").each(function(index, element) {
						if( $(this).val()!='' )
						{
							if(i==0)
							{
								colorIDvalue = colorList[i]+"*"+$(this).val();
							}
							else
							{
								colorIDvalue += "***"+colorList[i]+"*"+$(this).val();
							}
						}
						i++;

					});

					$("input[name=colorSizeBundleQnty]").each(function(index, element) {
						if( $(this).val()!='' )
						{
							if(j==0)
							{
								colorBundleVal = colorList[j]+"*"+$(this).val();
							}
							else
							{
								colorBundleVal += "***"+colorList[j]+"*"+$(this).val();
							}
						}
						j++;

					});
				}
				if(variableSettingsReject==2)//color level
				{
					$("input[name=txtColSizeRej]").each(function(index, element) {
						if( $(this).val()!='' )
						{
							if(k==0)
							{
								colorIDvalueRej = colorList[k]+"*"+$(this).val();
							}
							else
							{
								colorIDvalueRej += "**"+colorList[k]+"*"+$(this).val();
							}
						}
						k++;

					});
					//alert (colorIDvalueRej);return;
				}
				else if(variableSettingsReject==3)//color and size level
				{
					$("input[name=colorSizeRej]").each(function(index, element) {
						if( $(this).val()!='' )
						{
							if(k==0)
							{
								colorIDvalueRej = colorList[k]+"*"+$(this).val();
							}
							else
							{
								colorIDvalueRej += "***"+colorList[k]+"*"+$(this).val();
							}
						}
						k++;

					});
				}
				// alert(colorBundleVal); cbo_work_order
				// return
				var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+"&colorIDvalueRej="+colorIDvalueRej+"&colorBundleVal="+colorBundleVal+get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*sewing_production_variable*embro_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*cbo_location*cbo_floor*txt_receive_date*txt_receive_qty*txt_challan*txt_remark*txt_issue_qty*txt_reject_qty*txt_cumul_receive_qty*txt_yet_to_receive*hidden_break_down_html*txt_mst_id*hidden_currency_id*hidden_exchange_rate*hidden_piece_rate*cbo_work_order*cbo_sending_location*cbo_color_type*hid_body_part*save_dataReject*allReject_defect_id*defectReject_type_id*is_posted_account*get_entry_no*get_entry_date*country_ship_date',"../");
				// alert (data);return;
				http.open("POST","requires/print_embro_receive_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_receive_print_embroidery_Reply_info;
			}
		}
	}

	function fnc_receive_print_embroidery_Reply_info()
	{
		if(http.readyState == 4)
		{
			// alert(777);
			// alert(http.responseText);
			var variableSettings = $('#sewing_production_variable').val();
			var variableSettingsReject = $('#embro_production_variable').val();
			var styleOrOrderWisw = $('#styleOrOrderWisw').val();
			var item_id = $('#cbo_item_name').val();
			var country_id = $("#cbo_country_name").val();
			var company = $("#cbo_company_name").val();
			var emb_type = $("#cbo_embel_type").val();

			var reponse = http.responseText.split('**');

			uploadFile($("#hidden_po_break_down_id").val());

			if(reponse[0]==15)
			{
				setTimeout('fnc_fabric_cost_dtls('+ reponse[1]+')',8000);
			}
			if(reponse[0]==0)//insert
			{
				//alert(reponse[0]+" "+reponse[1]+" "+reponse[2]);
				var po_id = reponse[1];
				show_msg(trim(reponse[0]));
				show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id,'show_dtls_listview','printing_production_list_view','requires/print_embro_receive_controller','');
				setFilterGrid("tbl_search",-1);
				//reset_form('','','txt_receive_qty*txt_challan*txt_reject_qty*hidden_break_down_html*txt_remark*txt_mst_id','txt_receive_date,<? echo date("d-m-Y"); ?>','');
				reset_form('','','txt_receive_qty*txt_challan*txt_reject_qty*hidden_break_down_html*txt_remark*txt_mst_id*save_dataReject*allReject_defect_id*cbo_emb_company*cbo_work_order_booking_no*cbo_work_order','','','txt_receive_date');
				
				get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_embro_receive_controller" );

				if(variableSettings!=1) {
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id+'**'+company+'**'+variableSettingsReject+'**'+emb_type+'**'+$("#country_ship_date").val(), "color_and_size_level", "requires/print_embro_receive_controller" );
				}
				else
				{
					$("#txt_receive_qty").removeAttr("readonly");
				}

				if(variableSettingsReject!=1)
				{
					$("#txt_reject_qty").attr("readonly");
				}
				else
				{
					$("#txt_reject_qty").removeAttr("readonly");
				}
				release_freezing();
			}
			
			if(reponse[0]==1)//update
			{
				var po_id = reponse[1];
				show_msg(trim(reponse[0]));
				show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id,'show_dtls_listview','printing_production_list_view','requires/print_embro_receive_controller','');
				setFilterGrid("tbl_search",-1);
				reset_form('','','txt_receive_qty*txt_challan*txt_reject_qty*hidden_break_down_html*txt_remark*txt_mst_id*txt_sys_chln*save_dataReject*allReject_defect_id*cbo_emb_company*cbo_work_order_booking_no*cbo_work_order','','','txt_receive_date');
				get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_embro_receive_controller" );

				if(variableSettings!=1) {
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id+'**'+company+'**'+variableSettingsReject+'**'+emb_type+'**'+$("#country_ship_date").val(), "color_and_size_level", "requires/print_embro_receive_controller" );
				}
				else
				{
					$("#txt_receive_qty").removeAttr("readonly");
				}

				if(variableSettingsReject!=1)
				{
					$("#txt_reject_qty").attr("readonly");
				}
				else
				{
					$("#txt_reject_qty").removeAttr("readonly");
				}
				set_button_status(0, permission, 'fnc_receive_print_embroidery_entry',1,0);
				release_freezing();
			}
			if(reponse[0]==2)//delete
			{
				var po_id = reponse[1];
				show_msg(trim(reponse[0]));
				show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id,'show_dtls_listview','printing_production_list_view','requires/print_embro_receive_controller','');
				setFilterGrid("tbl_search",-1);
				reset_form('','','txt_receive_qty*txt_challan*txt_reject_qty*hidden_break_down_html*txt_remark*txt_mst_id*txt_sys_chln','','','txt_receive_date');
				get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_embro_receive_controller" );

				if(variableSettings!=1) {
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id+'**'+company+'**'+variableSettingsReject+'**'+emb_type, "color_and_size_level", "requires/print_embro_receive_controller" );
				}
				else
				{
					$("#txt_receive_qty").removeAttr("readonly");
				}
				if(variableSettingsReject!=1)
				{
					$("#txt_reject_qty").attr("readonly");
				}
				else
				{
					$("#txt_reject_qty").removeAttr("readonly");
				}
				set_button_status(0, permission, 'fnc_receive_print_embroidery_entry',1,0);
				release_freezing();
			}
			else if(reponse[0]==35)
			{
				$("#txt_receive_qty").val("");
				show_msg('25');
				alert(reponse[1]);
				release_freezing();
				return;
			}
			else if(reponse[0]==786)
			{
				alert("Projected PO is not allowed to production. Please check variable settings.");
			}
			else if(reponse[0]==420)
			{
				alert("Color Size Breakdown ID Not Found.");
				release_freezing();
				return false;
			}
			else if(reponse[0]==421)
			{
				alert(reponse[1]);
				release_freezing();
				return false;
			}
			release_freezing();

		}
	}

	function childFormReset()
	{
		reset_form('','','txt_receive_qty*txt_reject_qty*txt_challan*hidden_break_down_html*txt_remark*txt_receive_qty*txt_cumul_receive_qty*txt_yet_to_receive*txt_mst_id','','');
		$('#txt_receive_qty').attr('placeholder','')//placeholder value initilize
		$('#txt_cumul_receive_qty').attr('placeholder','')//placeholder value initilize
		$('#txt_yet_to_receive').attr('placeholder','')//placeholder value initilize
		$('#printing_production_list_view').html('')//listview container
		$("#breakdown_td_id").html('');
	}

	function fn_total(tableName,index) // for color and size level
	{
		var filed_value = $("#colSize_"+tableName+index).val();
		var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder');
		var txt_user_lebel=$('#txt_user_lebel').val();
		var variable_is_controll=$('#variable_is_controll').val();
		if(filed_value*1 > placeholder_value*1)
		{
			if(variable_is_controll==1 && txt_user_lebel!=2)
			{
				alert("Qnty Excceded by"+(placeholder_value-filed_value));
				$("#colSize_"+tableName+index).val('');
			}
			else
			{
				if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )
					void(0);
				else
				{
					$("#colSize_"+tableName+index).val('');
				}
			}
		}
		var totalRow = $("#table_"+tableName+" tr").length;
		//alert(tableName);
		math_operation( "total_"+tableName, "colSize_"+tableName, "+", totalRow);
		if($("#total_"+tableName).val()*1!=0)
		{
			$("#total_"+tableName).html($("#total_"+tableName).val());
		}
		var totalVal = 0;
		$("input[name=colorSize]").each(function(index, element) {
			totalVal += ( $(this).val() )*1;
		});
		$("#txt_receive_qty").val(totalVal);
	}

	function fn_total_rej(tableName,index) // for color and size level
	{
		var filed_value = $("#colSizeRej_"+tableName+index).val();
		var colsizes= $("#colSize_"+tableName+index).val();
	    if(colsizes=="" && filed_value !="")
	    {
	    	// this if condition add for when size null but reject qnty given scenery
	    	$("#colSize_"+tableName+index).val(0);
	    }
		var totalRow = $("#table_"+tableName+" tr").length;
		//alert(tableName);
		math_operation( "total_"+tableName, "colSizeRej_"+tableName, "+", totalRow);

		var totalValRej = 0;
		$("input[name=colorSizeRej]").each(function(index, element) {
			totalValRej += ( $(this).val() )*1;
		});
		$("#txt_reject_qty").val(totalValRej);
	}

	function fn_colorlevel_total(index) //for color level
	{
		var filed_value = $("#colSize_"+index).val();
		var placeholder_value = $("#colSize_"+index).attr('placeholder');
		var txt_user_lebel=$('#txt_user_lebel').val();
		var variable_is_controll=$('#variable_is_controll').val();

		if(filed_value*1 > placeholder_value*1)
		{
			if(variable_is_controll==1 && txt_user_lebel!=2)
			{
				alert("Qnty Excceded by"+(placeholder_value-filed_value));
				$("#colSize_"+index).val('');
			}
			else
			{
				if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )
					void(0);
				else
				{
					$("#colSize_"+index).val('');
				}
			}
		}
		var totalRow = $("#table_color tbody tr").length;
		//alert(totalRow);
		math_operation( "total_color", "colSize_", "+", totalRow);
		$("#txt_receive_qty").val( $("#total_color").val() );
	}

	function fn_colorRej_total(index) //for color level
	{
		var filed_value = $("#colSizeRej_"+index).val();
		var totalRow = $("#table_color tbody tr").length;
		//alert(totalRow);
		math_operation( "total_color_rej", "colSizeRej_", "+", totalRow);
		$("#txt_reject_qty").val( $("#total_color_rej").val() );
	}

	function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty,country_ship_date)
	{
		freeze_window(5);

		childFormReset();//child from reset
		$("#cbo_item_name").val(item_id);
		$("#txt_order_qty").val(po_qnty);
		$("#cbo_country_name").val(country_id);

		$('#cbo_embel_name').val(0);
		$('#cbo_embel_type').val(0);
       // alert(country_ship_date);
		get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_embro_receive_controller" );

		var variableSettings=$('#sewing_production_variable').val();
		var variableSettingsReject=$('#embro_production_variable').val();
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();

		if(variableSettings==1)
		{
			$("#txt_receive_qty").removeAttr("readonly");
		}
		else
		{
			$('#txt_receive_qty').attr('readonly','readonly');
		}

		if(variableSettingsReject!=1)
		{
			$("#txt_reject_qty").attr("readonly");
		}
		else
		{
			$("#txt_reject_qty").removeAttr("readonly");
		}

		show_list_view(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id+'**1','show_dtls_listview','printing_production_list_view','requires/print_embro_receive_controller','');
		set_button_status(0, permission, 'fnc_receive_print_embroidery_entry',1,0);
		release_freezing();
	}

	/*var selected_id = new Array; var selected_name = new Array;//txt_mst_id_all
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
			}
		}
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) {
			//alert(str);
			if (str!="") str=str.split("_");
			//toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				//selected_name.push( str[2] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				//selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				//name += selected_name[i] + '*';
			}

			id = id.substr( 0, id.length - 1 );
			//name = name.substr( 0, name.length - 1 );

			$('#txt_mst_id_all').val( id );
			//$('#hide_order_no').val( name );
		}*/

		// function will loop through all input tags and create
// url string from checked checkboxes



	function fnc_checkbox_check(rowNo)
	{
		var isChecked=$('#tbl_'+rowNo).is(":checked");
		var emblname=$('#emblname_'+rowNo).val();
		var mst_source= $('#productionsource_'+rowNo).val();

		if(isChecked==true)
		{
			$('#Print1').removeClass('formbutton_disabled').addClass('formbutton');
			$('#print02').removeClass('formbutton_disabled').addClass('formbutton');
			$('#print03').removeClass('formbutton_disabled').addClass('formbutton');
			$('#print05').removeClass('formbutton_disabled').addClass('formbutton');
			//$('#print02').style.display='inline';

			$('#Print1').removeAttr("onClick").attr("onClick", "fnc_receive_print_embroidery_entry(" + 4 + ");");

			$('#Print05').removeAttr("onClick").attr("onClick", "fnc_receive_print_embroidery_entry(" + 6 + ");");

			var tot_row=$('#tbl_search tr').length-1;
			for(var i=1; i<=tot_row; i++)
			{
				if(i!=rowNo)
				{
					try
					{
						if ($('#tbl_'+i).is(":checked"))
						{
							var emblnameCurrent=$('#emblname_'+i).val();
							var productionsourceCurrent=$('#productionsource_'+i).val();
							if((emblname!=emblnameCurrent) || (mst_source!=productionsourceCurrent) )
							{
								alert("Please Select Same Emblname Or Source ");
								$('#tbl_'+rowNo).attr('checked',false);
								return;
							}
						}
					}
					catch(e)
					{
						//got error no operation
					}
				}
			}
		}
	}

	function fnc_checkbox_check3(k)
	{
		var row_num=$('#tbl_search  tr').length-1;
		var mst_embel_name= $('#cbo_embel_name').val()*1;
		var mst_source= $('#cbo_source').val()*1;
		var all_id="";

		var isChecked=$('#checkedId_'+k).is(":checked");
		//if(isChecked==true)
		//{
		for (var i=1; i<=row_num; i++)
		{
			var embel_name= $('#emblname_'+i).val()*1;
			var source= $('#productionsource_'+i).val()*1;

			if(mst_source!=source)
			{
				alert('Same Embel Name and Source are Alowed');
				//$('checkedId_'+i).prop('checked', false);
				$('#checkedId_'+i).attr('checked',false);
				return;
			}
			if (document.getElementById('checkedId_'+i).checked==true)
			{
				document.getElementById('checkedId_'+i).value=1;
				var mst_all_id= $('#mstidall_'+i).val()*1;
				if(all_id=="")
				{
					var all_id=mst_all_id;
				}
				else
				{
					 var all_id =all_id+"_"+mst_all_id;
				}
				//alert(all_id );
				$('#txt_mst_id_all').val(all_id)
			}
			else
			{
				document.getElementById('checkedId_'+i).value=0;
				//document.getElementById('checkedId_'+i).value=1;
				var mst_all_id= $('#mstidall_'+i).val()*1;
				if(all_id=="")
				{
					var all_id=mst_all_id;
				}
				else
				{
					 var all_id =all_id+"_"+mst_all_id;
				}//alert(document.getElementById('checkedId_'+i).value);
			}//$('#mst_id_all_'+i).val(all_id)//$('#id_all').val(all_id)
		} //alert(all_id);
	}

	function load_location()
	{
		var cbo_company_name = $('#cbo_company_name').val();
		var cbo_source = $('#cbo_source').val();
		var cbo_emb_company = $('#cbo_emb_company').val();
		//working company change to work order refresh
		$('#cbo_work_order_booking_no').val('');
		$('#cbo_work_order').val('');
		if(cbo_source==1)
		{
			load_drop_down( 'requires/print_embro_receive_controller',cbo_emb_company, 'load_drop_down_location', 'location_td');
		}
		else
		{
			load_drop_down( 'requires/print_embro_receive_controller',cbo_company_name, 'load_drop_down_location', 'location_td');
		}
	}

	

	function fnc_workorder_search(supplier_id)
	{

		if( form_validation('cbo_company_name*txt_order_no*cbo_emb_company*cbo_embel_name','Company Name*Order No*Embel.Company*Embel.Name')==false )
		{
			return;
		}

		if($("#cbo_source").val()!=3)
		{
			return;
		}
		//alert(supplier_id)
		var company = $("#cbo_company_name").val();
		var po_break_down_id = $("#hidden_po_break_down_id").val();
		var cbo_embel_name = $("#cbo_embel_name").val();
		//var cbo_embel_type = $("#cbo_embel_type").val();
		var gmt_item = $("#cbo_item_name").val();
		load_drop_down( 'requires/print_embro_receive_controller', company+"_"+supplier_id+"_"+po_break_down_id+"_"+cbo_embel_name+"_"+gmt_item, 'load_drop_down_workorder', 'workorder_td' );
		//alert($('#cbo_cutting_company option').size())
	}

	function fnc_workorder_rate(data,id)
	{
		get_php_form_data(data+"_"+id, "populate_workorder_rate", "requires/print_embro_receive_controller" );
	}

	function fn_with_source_report2() // Print 3
	{
		var master_ids = ""; var total_tr=$('#tbl_search tr').length;
		for(i=1; i<=total_tr; i++)
		{
			try
			{
				if ($('#tbl_'+i).is(":checked"))
				{
					master_id = $('#mstidall_'+i).val();
					if(master_ids=="") master_ids= master_id; else master_ids +='_'+master_id;
				}
			}
			catch(e)
			{
				//got error no operation
			}
		}
		//alert(master_ids);
		if(master_ids=="")
		{
			alert("Please Select At Least One Item");
			return;
		}
	 	var report_title=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title, "emblishment_issue_print2", "requires/print_embro_receive_controller" )
		 return;
	}


	//for print button
	function print_button_setting(data)
	{
		$('#data_panel').html('');
		get_php_form_data(data,'print_button_variable_setting','requires/print_embro_receive_controller');
	}

	function print_report_button_setting(report_ids)
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
		 	if(report_id[k]==66)
			{
				$('#print02').show();
			}
			else if(report_id[k]==111)
			{
				$('#print03').show();
			}
		}
	}

	function openmypage_body_part(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=600px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			$('#hid_body_part').val('');
			var break_data=this.contentDoc.getElementById("hidden_break_tot_row");
			var break_delete_id=this.contentDoc.getElementById("txtDeletedId");
			$('#hid_body_part').val(break_data.value);
			$('#txt_body_part').val(break_data.value);
		}
	}

	function openmypage_woNo()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var cbo_service_source = $('#cbo_source').val();
		var po_order_id = $('#hidden_po_break_down_id').val();
		var po_order_no = $('#txt_order_no').val();
		if (form_validation('cbo_company_name*cbo_source*txt_order_no','Company*Source*Order No')==false)
		{
			return;
		}
		else
	  	{
			if (form_validation('cbo_company_name','cbo_source','Company','Source')==false)
			{
				return;
			}

			var page_link='requires/print_embro_receive_controller.php?company_id='+cbo_company_id+'&cbo_service_source='+cbo_service_source+'&po_order_id='+po_order_id+'&po_order_no='+po_order_no+'&action=service_booking_popup';
			var title='WO Number Popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1320px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_booking");
				if (theemail.value!="")
  				{
					var wo_data=(theemail.value).split("_");
	  				var wo_no=wo_data[1];
	  				var wo_id=wo_data[0];
					$('#txt_wo_id').val(wo_id);
					$('#txt_wo_no').val(wo_no);
					$('#txt_wo_no').attr('disabled',true);

  				}

			}
		}
	}

	function openmypage_work_order()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var cbo_service_source = $('#cbo_source').val();
		var po_order_id = $('#hidden_po_break_down_id').val();
		var po_order_no = $('#txt_order_no').val();
		var supplier_id = $('#cbo_emb_company').val();
		var cbo_embel_name = $('#cbo_embel_name').val();
		if (form_validation('cbo_company_name*cbo_source*txt_order_no*cbo_emb_company','Company*Source*Order No*Supplier')==false)
		{
			return;
		}
		else
	  	{
			if (form_validation('cbo_company_name','cbo_source','Company','Source')==false)
			{
				return;
			}

			var page_link='requires/print_embro_receive_controller.php?company_id='+cbo_company_id+'&cbo_service_source='+cbo_service_source+'&po_order_id='+po_order_id+'&po_order_no='+po_order_no+'&supplier_id='+supplier_id+'&cbo_embel_name='+cbo_embel_name+'&action=work_order_popup';
			var title='WO Number Popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1320px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("idNbookingNo");
				//var datat=this.contentDoc.getElementById("idNbookingNo");
				if (theemail.value!="")
  				{
					
					var wo_data=(theemail.value).split("_");
	  				var wo_no=wo_data[1];
	  				var wo_id=wo_data[0];
					$('#cbo_work_order').val(wo_id);
					$('#cbo_work_order_booking_no').val(wo_no);
					//$('#txt_wo_no').attr('disabled',true);

  				}

			}
		}
	}


   function openmypage_defectQty(type)
  {
	var cbo_company_id = $('#cbo_company_name').val();
		var cbo_service_source = $('#cbo_source').val();
		var po_order_id = $('#hidden_po_break_down_id').val();
		var po_order_no = $('#txt_order_no').val();
		if (form_validation('cbo_company_name*cbo_source*txt_order_no','Company*Source*Order No')==false)
		{
			return;
		}
		else
	  	{
			if (form_validation('cbo_company_name','cbo_source','Company','Source')==false)
			{
				return;
			}
			if(txt_order_no=='')
	        {
				alert('Please Order No Browse First.');
				return;
        	}
			else if(type==1)
	       	{
				var save_data=$('#save_dataReject').val();
				var all_defect_id=$('#allReject_defect_id').val();
				// var defect_qty=$('#txt_spot_qnty').val();
	     	}
			var page_link='requires/print_embro_receive_controller.php?company_id='+cbo_company_id+'&cbo_service_source='+cbo_service_source+'&po_order_id='+po_order_id+'&po_order_no='+po_order_no+'&action=defect_data&save_data='+save_data;
			var title='Reject Qty Info';
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=400px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var save_string=this.contentDoc.getElementById("save_string").value;
			    var tot_defectQnty=this.contentDoc.getElementById("tot_defectQnty").value;
			    var all_defect_id=this.contentDoc.getElementById("all_defect_id").value;
			    var defect_type_id=this.contentDoc.getElementById("defect_type_id").value;
				if(type==1) //Reject
			   	{
				   $('#save_dataReject').val(save_string);
					//$('#txt_spot_qnty').val(tot_defectQnty);
				   $('#allReject_defect_id').val(all_defect_id);
			       $('#defectReject_type_id').val(type);
		    	}

			}
		}

   }

	function load_emb_company(source_id)
	{
		if($('#cbo_emb_company option').length==2)
		{
			if($('#cbo_emb_company option:first').val()==0)
			{
				var cbo_emb_company_id=$('#cbo_emb_company option:last').val();
				$('#cbo_emb_company').val(cbo_emb_company_id);
				load_location();
				if(source_id==3)
				{
					fnc_workorder_search(cbo_emb_company_id);
				}
			}
		}
	}

	function active_placeholder_qty(color_id) {
		$("#table_" + color_id).find("input[name=colorSize]").each(function(index, element) {
			if ($('#set_all_' + color_id).prop('checked') == true) {
				$(this).val($(this).attr('placeholder'));

			} else {
				$(this).val('');
			}
		});

		var totalVal = 0;
		$("input[name=colorSize]").each(function(index, element) {
			totalVal += ($(this).val()) * 1;
		});
		$("#txt_receive_qty").val(totalVal);
	}


	function show_cost_details()
	{
		var system_id=$("#hidden_po_break_down_id").val();
		if(system_id=="")
		{
			alert('Order No Required!');
			return;
		}

		var page_link='requires/print_embro_receive_controller.php?action=show_cost_details&sys_id='+system_id;
		var title='Cost Details';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=730px,height=330px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{

		}
	}

	function fn_generate_color_size_break_down(emb_type)
	{		
		let variableSettings=$('#sewing_production_variable').val();
		let variableSettingsReject=$('#embro_production_variable').val();
		let styleOrOrderWisw=$('#styleOrOrderWisw').val();
		let item_id=$('#cbo_item_name').val();
		let country_id = $("#cbo_country_name").val();
		let company = $("#cbo_company_name").val();
		let po_id=$("#hidden_po_break_down_id").val();
		let country_ship_date = $("#country_ship_date").val();

		if(variableSettings!=1) 
		{
			get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id+'**'+company+'**'+variableSettingsReject+'**'+emb_type+'**'+country_ship_date, "color_and_size_level", "requires/print_embro_receive_controller" );
		}
		else
		{
			$("#txt_issue_qty").removeAttr("readonly");
		}
	}
</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;">
    	<? echo load_freeze_divs ("../",$permission);  ?>
        <div style="width:930px; float:left" align="center">
            <fieldset style="width:930px;">
                <legend>Production Module</legend>
                <form name="printembroreceive_1" id="printembroreceive_1" method="" autocomplete="off" >
                    <fieldset>
                        <table width="100%">
                            <tr>
								<td width="130" class="must_entry_caption">Order No</td>
								<td width="170">
									<input name="txt_order_no" placeholder="Double Click to Search" onDblClick="openmypage('requires/print_embro_receive_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value+'&order_id='+document.getElementById('hidden_po_break_down_id').value,'Order Search')" id="txt_order_no" class="text_boxes" style="width:188px " readonly />
									<input type="hidden" id="hidden_po_break_down_id" value="" />
									<input type="hidden" id="country_ship_date">
								</td>
                                <td width="100" class="must_entry_caption">Company</td>
                                <td>
                                    <?
                                    echo create_drop_down("cbo_company_name", 200, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "get_php_form_data(this.value,'load_field_level_access','requires/print_embro_receive_controller');", 1);
                                    ?>
                                    <input type="hidden" id="sewing_production_variable" />
                                    <input type="hidden" id="styleOrOrderWisw" />
                                    <input type="hidden" id="embro_production_variable" />
                                    <input type="hidden" id="variable_is_controll" />
                            		<input type="hidden" id="txt_user_lebel" value="<? echo $_SESSION['logic_erp']['user_level']; ?>" />
                                    <input type="hidden" id="hidden_currency_id" />
                                    <input type="hidden" id="hidden_exchange_rate" />
                            		<input type="hidden" id="wip_valuation_for_accounts" />
                                    <input type="hidden" id="hidden_piece_rate" />
                            		<input type="hidden" id="report_ids" name="report_ids"/>
                            		<input type="hidden" id="is_posted_account" name="is_posted_account"/>
                                </td>
                                <td width="130" >Country</td>
                                <td width="170">
                                    <?
                                    echo create_drop_down( "cbo_country_name", 200, "select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 );
                                    ?>
                                </td>
                           </tr>
                           <tr>
                                <td width="">Buyer</td>
                                <td width="170">
                                <?
                                echo create_drop_down( "cbo_buyer_name", 200, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 );
                                ?>
                                </td>
                                <td width="130">Style</td>
                                <td width="150">
                                    <input name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:188px " disabled  readonly>
                                </td>
                                <td width="130">Item</td>
                                <td width="170">
                                <?
                                echo create_drop_down( "cbo_item_name", 200, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );
                                ?>
                                </td>
                           </tr>
                           <tr>
                                <td width="">Order Qnty</td>
                                <td width="170">
                                    <input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric"  style="width:188px" disabled readonly>
                                </td>
                                <td width="130" class="must_entry_caption">Embel.Name</td>
                                <td width="170" id="embel_name_td">
                                    <?
                                    echo create_drop_down( "cbo_embel_name", 200, $emblishment_name_array,"", 1, "-- Select Embel.Name --", $selected, "load_drop_down( 'requires/print_embro_receive_controller', this.value+'**'+$('#hidden_po_break_down_id').val(), 'load_drop_down_emb_receive_type', 'emb_type_td' );  get_php_form_data($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#sewing_production_variable').val()+'**'+$('#styleOrOrderWisw').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val()+'**'+$('#cbo_company_name').val()+'**'+$('#embro_production_variable').val()+'**'+$('#cbo_embel_type').val(), 'color_and_size_level', 'requires/print_embro_receive_controller' ); show_list_view($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val(),'show_dtls_listview','printing_production_list_view','requires/print_embro_receive_controller',''); get_php_form_data($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val(), 'populate_data_from_search_popup', 'requires/print_embro_receive_controller' );" );
                                     ?>
                                </td>
                                <td width="130" class="must_entry_caption">Embel.Type</td>
                                <td id="emb_type_td" width="170">
                                    <?
                                    echo create_drop_down( "cbo_embel_type", 200, $blank_array,"", 1, "-- Select --", $selected, "");
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td width="" class="must_entry_caption">Source</td>
                                <td width="170">
                                    <?
                                    echo create_drop_down( "cbo_source", 200, $knitting_source,"", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/print_embro_receive_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_emb_receive', 'emb_company_td' );dynamic_must_entry_caption(this.value);load_emb_company(this.value);", 0, '1,3' );
                                    ?>
                                </td>
                                <td width="130" class="must_entry_caption">Working Company</td>
                                <td id="emb_company_td">
                                    <?
                                    echo create_drop_down( "cbo_emb_company", 200, $blank_array,"", 1, "-- Select Embel.Company --", $selected, "" );
                                    ?>
                                </td>
                                <td width="130" id="locations">Location</td>
                                <td width="170" id="location_td">
                                    <?
                                    echo create_drop_down( "cbo_location", 200, $blank_array,"", 1, "-- Select Location --", $selected, "" );
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td width="" id="floors">Floor/Unit</td>
                                <td width="170" id="floor_td">
                                    <?
                                    echo create_drop_down( "cbo_floor", 200, $blank_array,"", 1, "-- Select Floor --", $selected, "" );
                                    ?>
                                </td>
                               	<td width="130" class="">Work Order</td>
                                <td width="" id="workorder_td">
                                    <?
                                    // echo create_drop_down( "cbo_work_order", 200, $blank_array,"", 1, "-- Select Work Order--", $selected, "",0 );
                                    ?>
									<input type="text" name="cbo_work_order_booking_no" id="cbo_work_order_booking_no" class="text_boxes" style="width:187px;" placeholder="Browse" onDblClick="openmypage_work_order();" />
									<input type="hidden" id="cbo_work_order" name="cbo_work_order" value="0" />
                                </td>
								<td>WO NO</td>
								<td>
									<input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:187px;" placeholder="Browse/Write/scan" onDblClick="openmypage_woNo();" />
									<input type="hidden" id="txt_wo_id" value="0" />
								</td>
                            </tr>
							<tr>
								<td width="130" class="cbo_sending_location">Receiving Location</td>
								<td width="170">
								<?
								echo create_drop_down( "cbo_sending_location", 200, $sending_location,"id,location_name", 1, "-- Select Receiving Location --", $selected, "" );
								?>
								</td>
								<td width="130">Gate Entry No. </td>
								<td>
								<input width="170" type="text" name="get_entry_no" id="get_entry_no" class="text_boxes" style="width:187px;"/>  
								</td>
								<td width="130">Gate Entry Date</td>
								<td>
								<input name="get_entry_date" id="get_entry_date" class="datepicker"  type="text" value="<? echo date("d-m-Y")?>" style="width:187px;"  />
								</td>
								<td></td>
								<td>
									<input type="button" id="wip_valuation_for_accounts_button" name="" style="width:90px;display:none;" class="formbutton" value="Cost Details" onClick="show_cost_details();">
								</td> 
						    </tr>
                        </table>
                </fieldset>
                <table cellpadding="0" cellspacing="1" width="100%">
                <tr>
                    <td width="35%" valign="top">
                        <fieldset>
                            <legend>New Entry</legend>
                            <table cellpadding="0" cellspacing="2" width="350px">
                                <tr>
									<td width="100" class="must_entry_caption">Receive Date</td>
									<td width="250">
										<input name="txt_receive_date" id="txt_receive_date" class="datepicker"  type="text" value="<? echo date("d-m-Y")?>" style="width:100px;"/>
										Body Part<input type="text" name="txt_body_part" id="txt_body_part" class="text_boxes" style="width:50px" placeholder="Browse" onDblClick="openmypage_body_part('requires/print_embro_receive_controller.php?action=body_part_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value+'&po_break_down_id='+document.getElementById('hidden_po_break_down_id').value+'&hid_body_part='+document.getElementById('hid_body_part').value,'Body Part Search')"  readonly />
										<input type="hidden" name="hid_body_part" id="hid_body_part" class="text_boxes" style="width:50px"  readonly />
									</td>
                                </tr>
                                <tr>
                                  	<td class="">Color Type</td>
                                  	<td id="color_type_td" colspan="2">
                                  		<?
                                  		echo create_drop_down( "cbo_color_type", 110, $blank_array,"", 1, "Select Type", $selected, "",1,0 );
                                  		?>
                                  	</td>
                                </tr>
                                <tr>
                                    <td width="" class="must_entry_caption">Receive Qnty</td>
                                    <td width="">
                                    <input type="text" name="txt_receive_qty" id="txt_receive_qty" class="text_boxes_numeric"  style="width:100px" readonly >
                                    <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
                                    <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Reject Qnty</td>
                                    <td><input type="text" name="txt_reject_qty" id="txt_reject_qty"  class="text_boxes_numeric"  style="width:100px" readonly > </td>
                                    <td><input type="button" name="btn" id="btn" class="formbuttonplasminus" value="Reject Chk" style="width:70px" onClick="openmypage_defectQty(1);"/></td>
                                </tr>
                                <tr>
                                    <td width="" class="must_entry_caption">Challan No</td>
                                    <td>
                                    <input type="text" name="txt_challan" id="txt_challan" class="text_boxes" style="width:100px" />
                                        Sys. Chln.<input type="text" name="txt_sys_chln" id="txt_sys_chln" class="text_boxes" style="width:45px" placeholder="Display" disabled />
                                    </td>
                                </tr>
                                <tr>
                                    <td width="">Remarks</td>
                                    <td width="">
                                       <input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:220px" />
                                    </td>
                  					<td>&nbsp;</td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                    <td width="1%" valign="top"></td>
                    <td width="25%" valign="top">
                        <fieldset>
                            <legend>Display</legend>
							<table  cellpadding="0" cellspacing="2" width="250px" >
								<tr>
									<td width="100">Issue Qnty</td>
									<td width="90">
									<input type="text" name="txt_issue_qty" id="txt_issue_qty" class="text_boxes_numeric" style="width:80px" disabled readonly />
									</td>
								</tr>
								<tr>
									<td width="">Cumul.Receive Qnty</td>
									<td width="">
									<input type="text" name="txt_cumul_receive_qty" id="txt_cumul_receive_qty" class="text_boxes_numeric" style="width:80px" disabled readonly/>
									</td>
								</tr>
								<tr>
									<td width="">Yet to Receive</td>
									<td width="">
									<input type="text" name="txt_yet_to_receive" id="txt_yet_to_receive" class="text_boxes_numeric" style="width:80px" disabled readonly/>
									</td></tr><br/>
								</tr>
								<tr>	
									<td></td> 
									<td>
										<input type="button" id="image_button" class="image_uploader" style="width:50px;" value="IMAGE" onClick="file_uploader('../', document.getElementById('hidden_po_break_down_id').value,'', 'embellishment_receive_entry',1,1)"/>
										<input type="button" id="image_button" class="image_uploader" style="width:50px;" value="FILE" onClick="file_uploader('../', document.getElementById('hidden_po_break_down_id').value,'', 'embellishment_receive_entry',2,1)"/>
									</td>
								</tr>
								<td  align="left">File</td>
								<td align="left">
									<input type="file" multiple id="cbo_file" class="image_uploader" style="width:150px" onChange="document.getElementById('txt_file').value=1">
									<input type="hidden" multiple id="txt_file">
								</td>
							</table>
                        </fieldset>
                    </td>
                    <td width="33%" valign="top" >
                        <div style="max-height:350px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                        <br/>
                    </td>
                </tr>
				<tr>
					<td colspan="10">
						<div style="width:100%;text-align: center;font-size: 16px;color: red;font-weight: bold;" id="posted_account_msg"></div>
					</td>
				</tr>
                <tr>
                    <td align="center" colspan="9" valign="middle" class="button_container">
                        <?
						$date=date('d-m-Y');
                        echo load_submit_buttons( $permission, "fnc_receive_print_embroidery_entry", 0, 1,"reset_form('printembroreceive_1','list_view_country','','txt_receive_date,".$date."','childFormReset()')",1);
                        ?>
                        <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
                        <input id="print02" class="formbutton formbutton_disabled" style="width:90px;display: none" value="Print 2" name="print02" onClick="fn_with_source_report2()" type="button">

                        <input id="print03" class="formbutton formbutton_disabled" style="width:90px;display: none" value="Print 3" name="print03" onClick="fnc_receive_print_embroidery_entry(5)" type="button">

						<input id="print05" class="formbutton formbutton_disabled" style="width:90px;" value="Print 5" name="print05" onClick="fnc_receive_print_embroidery_entry(6)" type="button">

						    <input type="hidden" name="save_dataReject" id="save_dataReject" readonly />
                            <input type="hidden" name="allReject_defect_id" id="allReject_defect_id" readonly />
                            <input type="hidden" name="defectReject_type_id" id="defectReject_type_id" readonly />

                    </td>
                	<td>&nbsp;</td>
                </tr>
            </table>
            <div style="width:900px; margin-top:5px;"  id="printing_production_list_view" align="center"></div>
            </form>
            </fieldset>
        </div>
		<div id="list_view_country" style="width:380px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:13px"></div>
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$(function(){
		//alert("body loaded");
		// for (var property in mandatory_field_arr) {
		// 	$("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
		// }
	});

	<?
	if(implode('*', $_SESSION['logic_erp']['mandatory_field'][727])) 
	{
		$json_mandatory_field = json_encode($_SESSION['logic_erp']['mandatory_field'][727]);
		echo "var mandatory_field_arr= " . $json_mandatory_field . ";\n";
	}
	?>
	if('<? echo implode('*', $_SESSION['logic_erp']['mandatory_field'][727]);?>')
	{
		$.each(mandatory_field_arr, function(key, value){
			$(("."+value)).css("color", "blue");
		})
	}

</script>
</html>
