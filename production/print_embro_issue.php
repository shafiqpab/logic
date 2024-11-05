<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create print imbro issue

Functionality	:
JS Functions	:
Created by		:	Bilas
Creation date 	: 	24-02-2013
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

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Embellishment Issue Entry","../", 1, 1, $unicode,'','');
// $mandatory_field_arr = json_encode($_SESSION['logic_erp']['mandatory_field'][601]);
function arrayExclude($array,Array $excludeKeys){
    foreach($array as $key => $value){
        if(!in_array($key, $excludeKeys)){
            $return[$key] = $value;
        }
    }
    return $return;
}
// print_r($_SESSION['logic_erp']['mandatory_field'][601]);

if ($db_type == 0) {
    $sending_location="select concat(b.id,'*',a.id) id,concat(b.location_name,':',a.company_name) location_name from lib_company a, lib_location b where a.id=b.company_id and b.status_active =1 and b.is_deleted=0 and a.status_active =1 and a.is_deleted=0 order by a.company_name";
} else if ($db_type == 2 || $db_type == 1) {
    $sending_location="select b.id||'*'||a.id as id, b.location_name||' : '||a.company_name as location_name  from lib_company a, lib_location b where a.id=b.company_id and b.status_active =1 and b.is_deleted=0 and a.status_active =1 and a.is_deleted=0 order by a.company_name";
}
?>

<script>
<? $data_arr = json_encode($_SESSION['logic_erp']['data_arr'][601]);
if ($data_arr)
	echo "var field_level_data= " . $data_arr . ";\n";
//   echo "alert(JSON.stringify(field_level_data));";
?>
var field_level_menual = 1;
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
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
			var country_ship_date=this.contentDoc.getElementById("hid_country_ship_date").value;

			get_php_form_data(company_id,'load_variable_settings','requires/print_embro_issue_controller');
 
			print_button_setting(company_id);
			if (po_id!="")
			{
				freeze_window(5);
			
				$("#txt_order_qty").val(po_qnty);
				$('#cbo_item_name').val(item_id);
				$("#cbo_country_name").val(country_id);
				$("#cbo_company_name").val(company_id);
				$("#country_ship_date").val(country_ship_date);
				//alert(country_ship_date);
				childFormReset();//child form initialize
				$('#cbo_embel_name').val(0);
				$('#cbo_embel_type').val(0);
				get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_embro_issue_controller" );

				var variableSettings=$('#sewing_production_variable').val();
				var styleOrOrderWisw=$('#styleOrOrderWisw').val();

				if(variableSettings==1)
				{
					$("#txt_issue_qty").removeAttr("readonly");
				}
				else
				{
					$('#txt_issue_qty').attr('readonly','readonly');
				}
				//$('#cbo_embel_name').val(1);
				//$('#cbo_embel_name').trigger('change');
				show_list_view(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id+'**1','show_dtls_listview','printing_production_list_view','requires/print_embro_issue_controller','');
				setFilterGrid("tbl_search",-1);
				show_list_view(po_id,'show_country_listview','list_view_country','requires/print_embro_issue_controller','');
				set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0);
				load_drop_down( 'requires/print_embro_issue_controller', po_id, 'load_drop_down_color_type', 'color_type_td');
				load_drop_down( 'requires/print_embro_issue_controller', company_id, 'load_drop_down_lc_location', 'location_lc_td');
				setFieldLevelAccess(company_id);
				release_freezing();
			}
			$("#cbo_company_name").attr("disabled","disabled");
		}
	//	}//end else
}//end function

function openmypage_body_part(page_link,title)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=370px,center=1,resize=0,scrolling=0','')
	emailwindow.onclose=function()
	{
		$('#hid_body_part').val('');
		var break_data=this.contentDoc.getElementById("hidden_break_tot_row"); 
		var break_delete_id=this.contentDoc.getElementById("txtDeletedId"); 
		$('#hid_body_part').val(break_data.value);
		$('#txt_body_part').val(break_data.value);
	}		
}
function fnc_issue_print_embroidery_entry(operation)
{
	var source=$("#cbo_source").val();
	 
	if(operation==4)
	{


		// var report_title=$("div.form_caption").html();
		 //print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title, "emblishment_issue_print", "requires/print_embro_issue_controller" )
		// return;
	}
	 

	else if(operation==0 || operation==1 || operation==2)
	{
		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][601]); ?>') 
		{
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][601]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][601]); ?>')==false) {return;}
		}
		
		// alert("abc");
		if ( form_validation('cbo_company_name*txt_order_no*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*txt_issue_date*txt_issue_qty','Company Name*Order No*Embel. Name* Embel. Type*Source*Embel.Company*Issue Date*Issue Quantity')==false )
		{
			return;
		}
		else
		{
			if(source==1)
			{
				if ( form_validation('cbo_location*cbo_floor','Location*Floor')==false )
				{
					return;
				}

			}

			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_issue_date').val(), current_date)==false)
			{
				alert("Embel Issue Date Can not Be Greater Than Current Date");
				return;
			}
			var sewing_production_variable = $("#sewing_production_variable").val();
			var colorList = ($('#hidden_colorSizeID').val()).split(",");

			var i=0;var colorIDvalue=''; var k=0; var colorBundleVal="";
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
						if(k==0)
						{
							colorBundleVal = colorList[k]+"*"+$(this).val();
						}
						else
						{
							colorBundleVal += "**"+colorList[k]+"*"+$(this).val();
						}
					}
					k++;
					
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
						if(k==0)
						{
							colorBundleVal = colorList[k]+"*"+$(this).val();
						}
						else
						{
							colorBundleVal += "***"+colorList[k]+"*"+$(this).val();
						}
					}
					k++;
					
				});
			}

			// alert(colorBundleVal);
			// return;

			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+"&colorBundleVal="+colorBundleVal+get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*sewing_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*cbo_location*cbo_floor*txt_issue_date*txt_issue_qty*txt_challan*txt_remark*txt_cutting_qty*txt_cumul_issue_qty*txt_yet_to_issue*hidden_break_down_html*txt_mst_id*cbo_sending_location*txt_manual_cut_no*cbo_color_type*txt_remark_dtls*hid_body_part*cbo_location_lc*cbo_floor_lc*cbo_work_order*country_ship_date',"../");
			//my data
			//alert (data);return;
			freeze_window(operation);
			http.open("POST","requires/print_embro_issue_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_issue_print_embroidery_Reply_info;
		}
	}
}

function fnc_workorder_rate(data,id)
{
	get_php_form_data(data+"_"+id, "populate_workorder_rate", "requires/print_embro_issue_controller" );
}
function fnc_workorder_search(supplier_id)
{

	if( form_validation('cbo_company_name*txt_order_no*cbo_emb_company*cbo_embel_name','Company Name*Order No*Embel.Company*Embel.Name')==false )
	{
		return;
	}

	$("#cbo_work_order").val('0');
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
	load_drop_down( 'requires/print_embro_issue_controller', company+"_"+supplier_id+"_"+po_break_down_id+"_"+cbo_embel_name+"_"+gmt_item, 'load_drop_down_workorder', 'workorder_td' );
	//alert($('#cbo_cutting_company option').size())
}
function fnc_issue_print_embroidery_Reply_info()
{
 	if(http.readyState == 4)
	{
		// alert(http.responseText);
		var variableSettings=$('#sewing_production_variable').val();
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();
		var item_id=$('#cbo_item_name').val();
		var country_id = $("#cbo_country_name").val();
		var company = $("#cbo_company_name").val();
		var emb_type = $("#cbo_embel_type").val();

		var reponse=http.responseText.split('**');
		if(reponse[0]==15)
		{
			 setTimeout('fnc_issue_print_embroidery_entry('+ reponse[1]+')',8000);
		}
		if(reponse[0]==0)//insert
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
 			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id,'show_dtls_listview','printing_production_list_view','requires/print_embro_issue_controller','');
			setFilterGrid("tbl_search",-1);
			reset_form('','','txt_issue_qty*txt_challan*txt_iss_id*txt_remark_dtls*hidden_break_down_html*hidden_colorSizeID*txt_mst_id','','','txt_cutting_date');
			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_embro_issue_controller" );

			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id+'**'+company+'**'+emb_type+'**'+$("#country_ship_date").val(), "color_and_size_level", "requires/print_embro_issue_controller" );
			}
			else
			{
				$("#txt_issue_qty").removeAttr("readonly");
			}
 			release_freezing();
		}
		else if(reponse[0]==1)//update
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id,'show_dtls_listview','printing_production_list_view','requires/print_embro_issue_controller','');
			setFilterGrid("tbl_search",-1);
			reset_form('','','txt_issue_qty*txt_challan*txt_iss_id*txt_remark*hidden_break_down_html*hidden_colorSizeID*txt_mst_id','','','txt_cutting_date');
 			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_embro_issue_controller" );

 			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id+'**'+company+'**'+emb_type+'**'+$("#country_ship_date").val(), "color_and_size_level", "requires/print_embro_issue_controller" );
			}
			else
			{
				$("#txt_issue_qty").removeAttr("readonly");
			}
			set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0);
			release_freezing();
		}
		else if(reponse[0]==2)//delete
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id,'show_dtls_listview','printing_production_list_view','requires/print_embro_issue_controller','');
			reset_form('','','txt_issue_qty*txt_challan*txt_iss_id*txt_remark*hidden_break_down_html*hidden_colorSizeID*txt_mst_id','','','txt_cutting_date');
 			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_embro_issue_controller" );

 			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id+'**'+company+'**'+emb_type+'**'+$("#country_ship_date").val(), "color_and_size_level", "requires/print_embro_issue_controller" );
			}
			else
			{
				$("#txt_issue_qty").removeAttr("readonly");
			}
			set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0);
			release_freezing();
		}
		else if(reponse[0]==35)
		{
			$("#txt_issue_qty").val("");
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
		release_freezing();
 	}
}
function childFormReset()
{
	reset_form('','','txt_issue_qty*txt_challan*txt_iss_id*hidden_break_down_html*hidden_colorSizeID*txt_remark*txt_cutting_qty*txt_cumul_issue_qty*txt_yet_to_issue*txt_mst_id','','');
	$('#txt_issue_qty').attr('placeholder','');//placeholder value initilize
	$('#txt_cutting_qty').attr('placeholder','');//placeholder value initilize
	$('#txt_cumul_issue_qty').attr('placeholder','');//placeholder value initilize
	$('#txt_yet_to_issue').attr('placeholder','');//placeholder value initilize
	$('#printing_production_list_view').html('');//listview container
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
	$("#txt_issue_qty").val(totalVal);
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
	$("#txt_issue_qty").val( $("#total_color").val() );
}
var x = "";

function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty,country_ship_date)
{
	freeze_window(5);
     x = country_ship_date ;
	childFormReset();//child from reset
	$("#cbo_item_name").val(item_id);
	$("#txt_order_qty").val(po_qnty);
	$("#cbo_country_name").val(country_id);
    $("#country_ship_date").val(country_ship_date);
	$('#cbo_embel_name').val(0);
	$('#cbo_embel_type').val(0);
    var country_ship_date1= $("#country_ship_date").val(country_ship_date);
   // alert(country_ship_date1);
	//alert(country_ship_date);
	get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_embro_issue_controller" );

	var variableSettings=$('#sewing_production_variable').val();
	var styleOrOrderWisw=$('#styleOrOrderWisw').val();

	if(variableSettings==1)
	{
		$("#txt_issue_qty").removeAttr("readonly");
	}
	else
	{
		$('#txt_issue_qty').attr('readonly','readonly');
	}

	show_list_view(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id+'**1'+'**'+country_ship_date,'show_dtls_listview','printing_production_list_view','requires/print_embro_issue_controller','');
	setFilterGrid("tbl_search",-1);
	set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0);
	release_freezing();
}
function fn_with_source_report()
{
	  var mst_id=$('#txt_mst_id').val();
		 if(mst_id=="")
		 {
			alert('Please Select from list View first '); return;
		 }
	 var report_title=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_name').val()+'*'+mst_id+'*'+report_title, "emblishment_issue_print", "requires/print_embro_issue_controller" )
		 return;
}
function fn_without_source_report()
{
	 var report_title=$( "div.form_caption" ).html();
	 var mst_id=$('#txt_mst_id').val();
	 if(mst_id=="")
	 {
		alert('Please Select from list View first '); return;
	 }

		 print_report( $('#cbo_company_name').val()+'*'+mst_id+'*'+report_title, "emblishment_without_print", "requires/print_embro_issue_controller" )
		 return;
}
function fn_with_source_report2() // Print 2
{
	var master_ids = ""; var total_tr=$('#tbl_search tr').length;
	for(i=1; i<total_tr; i++)
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
	 print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title, "emblishment_issue_print2", "requires/print_embro_issue_controller" )
	 return;
}
function fn_with_source_report3() // Print 3
{
	var master_ids = ""; var total_tr=$('#tbl_search tr').length;
	for(i=1; i<total_tr; i++)
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
	 print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title, "emblishment_issue_print3", "requires/print_embro_issue_controller" )
	 return;
}

//for print button
function print_button_setting(data)
{
	$('#data_panel').html('');
	get_php_form_data(data,'print_button_variable_setting','requires/print_embro_issue_controller');
}
function print_report_button_setting(report_ids)
{
	var report_id=report_ids.split(",");
	for (var k=0; k<report_id.length; k++)
	{
		if(report_id[k]==47)
		{
			$('#data_panel').append( '<input type="button"  id="print" class="formbutton" style="width:90px;" value="Print"  name="print"  onClick="fn_with_source_report()" />&nbsp;&nbsp;&nbsp;' );
		}
		else if(report_id[k]==48)
		{
			$('#data_panel').append( '<input type="button"  id="print2" class="formbutton" style="width:90px;" value="Without Source"  name="print2"  onClick="fn_without_source_report()" />&nbsp;&nbsp;&nbsp;' );
		}
		else if(report_id[k]==66)
		{
			$('#data_panel').append( '<input type="button"  id="print02" class="formbutton" style="width:90px;" value="Print 2"  name="print02"  onClick="fn_with_source_report2()" />&nbsp;&nbsp;&nbsp;' );
		}
	}
}

function fnc_checkbox_check(rowNo)
{
	var isChecked=$('#tbl_'+rowNo).is(":checked");
	var emblname=$('#emblname_'+rowNo).val();
	var embltype=$('#embltype_'+rowNo).val();
	var mst_source= $('#productionsource_'+rowNo).val();

	var serving_company= $('#serving_company_'+rowNo).val();
	var location= $('#location_'+rowNo).val();

	if(isChecked==true)
	{
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
						var embltypeCurrent=$('#embltype_'+i).val();
						var productionsourceCurrent=$('#productionsource_'+i).val();

						var serving_companyCurrent= $('#serving_company_'+i).val();
						var locationCurrent= $('#location_'+i).val();

						//alert(emblname+"_"+emblnameCurrent+"**"+mst_source+"_"+productionsourceCurrent+"**"+serving_company+"_"+serving_companyCurrent+"**"+location+"_"+locationCurrent+"**"+embltype+"_"+embltypeCurrent);

						if((emblname!=emblnameCurrent) || (mst_source!=productionsourceCurrent) || (serving_company!=serving_companyCurrent) || (location!=locationCurrent) || (embltype!=embltypeCurrent))
						{
							alert("Please Select Same Emblname, Source, Embel. Company, Location");
							$('#tbl_'+rowNo).attr('checked',false);
							return;
						}

						// if((emblname!=emblnameCurrent) && (mst_source!=productionsourceCurrent) && (serving_company!=serving_companyCurrent) && (location!=locationCurrent) )
						// {
						// 	alert("Please Select Same Emblname, Source, Embel. Company, Location");
						// 	$('#tbl_'+rowNo).attr('checked',false);
						// 	return;
						// }
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

function fnc_load_from_dtls(data)
{
	//alert(data); return;
	get_php_form_data(data,'populate_issue_form_data','requires/print_embro_issue_controller');
}

function fn_chk_next_process_qty(tableName,index,sizeId) // for color and size level
{
	// alert('ok');return;
	var data="action=chk_next_process_qty&colorId="+tableName+"&sizeId="+sizeId+get_submitted_data_string('cbo_country_name*sewing_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_item_name*cbo_embel_name',"../");
	//alert(data); return;
	var cur_value = $("#colSize_"+tableName+index).val()*1;
	var update_value = $("#colSizeUpQty_"+tableName+index).val()*1;
	$.ajax({
		url: 'requires/print_embro_issue_controller.php',
		type: 'POST',
		data: data,
		success: function(response)
		{
			var resData = trim(response).split("****");
			var rcvQty = resData[0]*1;
			var prevIssueQty = resData[1]*1;
			// alert(update_value+'=='+cur_value+'=='+prevIssueQty+'=='+rcvQty);
			if((prevIssueQty+cur_value-update_value)*1 < rcvQty*1)
			{	
				alert('Sorry! Issue qnty will not less than receive qnty. Pre Issue Qty='+prevIssueQty+'; Rec Qty='+rcvQty);			
				$("#colSize_"+tableName+index).val(update_value);		 		
			}
		}
	});
}

function openmypage_woNo()
{
		var cbo_company_id = $('#cbo_company_name').val();
		var cbo_service_source = $('#cbo_source').val();
		
		var po_order_id = $('#hidden_po_break_down_id').val();		
			
		var po_order_no = $('#txt_order_no').val();
		if (form_validation('cbo_company_name*cbo_source*txt_order_no','Company*Source*Service Company*Order No')==false)
		{
			return;
		}
		else
	  	{			
			if (form_validation('cbo_company_name','cbo_source','Service Company','Source')==false)
			{
				return;
			}
			
			var page_link='requires/print_embro_issue_controller.php?company_id='+cbo_company_id+'&cbo_service_source='+cbo_service_source+'&po_order_id='+po_order_id+'&po_order_no='+po_order_no+'&action=service_booking_popup';
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
	

	function load_location()
	{
		var cbo_company_name = $('#cbo_company_name').val();
		var cbo_source = $('#cbo_source').val();
		var cbo_emb_company = $('#cbo_emb_company').val();
		if(cbo_source==1)
		{
			load_drop_down( 'requires/print_embro_issue_controller',cbo_emb_company, 'load_drop_down_location', 'location_td');
		}
		else
		{
			load_drop_down( 'requires/print_embro_issue_controller',cbo_company_name, 'load_drop_down_location', 'location_td');
				
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
		$("#txt_issue_qty").val(totalVal);
	}
		

	function show_cost_details()
	{
		var system_id=$("#hidden_po_break_down_id").val();
		if(system_id=="")
		{
			alert('Order No Required!');
			return;
		}

		var page_link='requires/print_embro_issue_controller.php?action=show_cost_details&sys_id='+system_id;
		var title='Cost Details';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=730px,height=330px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{

		}
	}

	function fn_generate_color_size_break_down(emb_type)
	{		
		let variableSettings=$('#sewing_production_variable').val();
		let styleOrOrderWisw=$('#styleOrOrderWisw').val();
		let item_id=$('#cbo_item_name').val();
		let country_id = $("#cbo_country_name").val();
		let company = $("#cbo_company_name").val();
		let po_id=$("#hidden_po_break_down_id").val();
        let country_ship_date=$("#country_ship_date").val();
		if(variableSettings!=1) 
		{
			get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id+'**'+company+'**'+emb_type+'**'+country_ship_date, "color_and_size_level", "requires/print_embro_issue_controller" );
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
    <div style="width:1030px; float:left" align="center">
 		<fieldset style="width:1000px;">
        <legend>Production Module</legend>
        <form name="printembro_1" id="printembro_1" method="" autocomplete="off" >
            <fieldset>
                <table width="100%">
                    <tr>
						<td width="130" class="must_entry_caption">Order No</td>
						<td width="170">
							<input name="txt_order_no" placeholder="Double Click to Search" onDblClick="openmypage('requires/print_embro_issue_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Order Search')" id="txt_order_no" class="text_boxes" style="width:190px " readonly />
							<input type="hidden" id="hidden_po_break_down_id" value="" />
							<input type="hidden" id="country_ship_date" />
						</td>
                        <td width="130" class="must_entry_caption">Company</td>
                        <td>
                            <?
                            echo create_drop_down( "cbo_company_name", 200, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "",1 );
                            ?>
                            <input type="hidden" id="sewing_production_variable" />
                            <input type="hidden" id="styleOrOrderWisw" />
                            <input type="hidden" id="report_ids" name="report_ids"/>
                            <input type="hidden" id="variable_is_controll" />
                            <input type="hidden" id="wip_valuation_for_accounts" />
                            <input type="hidden" id="txt_user_lebel" value="<? echo $_SESSION['logic_erp']['user_level']; ?>" />
                        </td>
                        <td width="130" >Location</td>
                        <td width="170" id="location_lc_td">
                            <?
							echo create_drop_down("cbo_location_lc", 200, $blank_array, "", 1, "-- Select Location --", $selected, "");
                            ?>
                        </td>
                    </tr>
                    <tr>
                    	<td width="130">Floor/Unit</td>
                        <td width="170" id="floor_lc_td">
                            <?
                            echo create_drop_down( "cbo_floor_lc", 200, $blank_array,"", 1, "-- Select Floor --", $selected, "" );
                            ?>
                        </td>
                    	<td width="130">Country</td>
                        <td width="170">
                            <?
                            echo create_drop_down("cbo_country_name",200,"select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 );
                            ?>
                        </td>
                        <td width="130">Buyer</td>
                        <td width="170">
                            <?
                            echo create_drop_down( "cbo_buyer_name", 200, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 );
                            ?>
                        </td>
                    </tr>
                    <tr>
                    	 <td width="130">Style</td>
                        <td width="150">
                            <input name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:188px" disabled  readonly>
                        </td>

                        <td width="130">Item</td>
                        <td width="170">
                            <?
                            echo create_drop_down( "cbo_item_name", 200, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );
                            ?>
                        </td>
                        <td width="130">Order Qnty</td>
                        <td width="170">
                            <input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric"  style="width:188px" disabled readonly>
                        </td>
                    </tr>
                    <tr>
                    	<td width="130" class="must_entry_caption">Embel. Name</td>
                         <td width="170" id="embel_name_td">
                            <?
							echo create_drop_down( "cbo_embel_name", 200, $blank_array,"", 1, "-- Select Embel.Name --", $selected, "" );
                            ?>
                        </td>
                    	<td width="130" class="must_entry_caption">Embel. Type</td>
                        <td id="embro_type_td" width="170">
                            <?
                            echo create_drop_down( "cbo_embel_type", 200, $blank_array,"", 1, "-- Select --", $selected, "" );
                            ?>
                        </td>
                        <td width="130" class="must_entry_caption">Source</td>
                        <td width="170">
                            <?
                            echo create_drop_down( "cbo_source", 200, $knitting_source,"", 1, "-- Select Source --", 1, "load_drop_down( 'requires/print_embro_issue_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_embro_issue_source', 'emb_company_td' );dynamic_must_entry_caption(this.value);load_emb_company(this.value);", 0, '1,3' );
                            ?>
                        </td>
                    </tr>
                    <tr>
                    	<td width="130" class="must_entry_caption">Embel.Company</td>
                        <td id="emb_company_td">
                            <?
                            echo create_drop_down( "cbo_emb_company", 200, $blank_array,"", 1, "-- Select --", $selected, "" );
                            ?>
                        </td>
                    	<td width="130" id="locations" class="must_entry_caption">Location</td>
                        <td width="170" id="location_td">
                            <?
                            echo create_drop_down( "cbo_location", 200, $blank_array,"", 1, "-- Select Location --", $selected, "" );
                            ?>
                        </td>
                        <td width="130" id="floors" class="must_entry_caption">Floor/Unit</td>
                        <td width="170" id="floor_td">
                            <?
                            echo create_drop_down( "cbo_floor", 200, $blank_array,"", 1, "-- Select Floor --", $selected, "" );
                            ?>
                        </td>
                    </tr>
                    <tr> 
					    <td width="130" class="cbo_work_order">Work Order</td>
						<td width="" id="workorder_td">
							<?
							echo create_drop_down( "cbo_work_order", 200, $blank_array,"", 1, "-- Select Work Order--", $selected, "",0 );
							?>
						</td>
                    	<td width="130" class="cbo_sending_location">Sending Location</td>
                        <td width="170">
                            <?
                            echo create_drop_down( "cbo_sending_location", 200, $sending_location,"id,location_name", 1, "-- Select Sending Location --", $selected, "" );
                            ?>
                        </td>
							<td width="130">WO NO</td>
                        	<td width="130">
                            <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:187px;" placeholder="Browse/Write/scan" onDblClick="openmypage_woNo();" />
                            <input type="hidden" id="txt_wo_id" value="0" />
                        </td>
                    </tr>
					<tr>
						<td width="130">Remarks</td>
							<td width="170">
							<input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:94%" title="450 Characters Only." />
						</td>
						<td></td>						  
						<td>
							<input type="button" id="wip_valuation_for_accounts_button" name="" style="width:90px;display:none;" class="formbutton" value="Cost Details" onClick="show_cost_details();">
						</td>
					</tr>
                </table>
                </fieldset> <br />
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
						<td width="35%" valign="top">
							<fieldset>
								<legend style="height:70px">New Entry
									<div style="float: center; color: red;">
										<span id="td_title">Cutting Company:<span id="cutcompany" style="word-break:break-all"></span></span><br>
										<span>Cutting Location:<span id="cutloaction" style="word-break:break-all"></span></span>
									</div>
								</legend>
									<table cellpadding="0" cellspacing="2" width="100%">
										<tr>
											<td width="80" class="must_entry_caption">Issue Date</td>
											<td width="110">
												<input type="text" name="txt_issue_date" id="txt_issue_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:100px;"  />
											</td>
											<td>Body Part</td>
											<td>
											<input type="text" name="txt_body_part" id="txt_body_part" class="text_boxes" style="width:50px" placeholder="Browse" onDblClick="openmypage_body_part('requires/print_embro_issue_controller.php?action=body_part_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value+'&po_break_down_id='+document.getElementById('hidden_po_break_down_id').value+'&hid_body_part='+document.getElementById('hid_body_part').value,'Body Part Search')"  readonly />
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
											<td class="must_entry_caption">Issue Qty</td>
											<td colspan="3">
												<input type="text" name="txt_issue_qty" id="txt_issue_qty" class="text_boxes_numeric"  style="width:100px" readonly >
												<input type="hidden" id="hidden_break_down_html" value="" readonly disabled />
												<input type="hidden" id="hidden_colorSizeID" value="" readonly disabled />
											</td>
										</tr>
										<tr>
											<td>Challan No</td>
											<td>
											<input type="text" name="txt_challan" id="txt_challan" class="text_boxes" style="width:100px" />
											</td>
											<td>Iss. ID</td>
											<td>
											<input type="text" name="txt_iss_id" id="txt_iss_id" class="text_boxes" style="width:50px" readonly />
											</td>
										</tr>
										<tr>
											<td>Manual Cut No</td>
											<td>
											<input type="text" name="txt_manual_cut_no" id="txt_manual_cut_no" class="text_boxes text_boxes_numeric" style="width:100px" />
											</td>
										</tr>
										<tr>
											<td>Remarks</td>
											<td colspan="3">
											<input type="text" name="txt_remark_dtls" id="txt_remark_dtls" class="text_boxes" style="width:212px" title="450 Characters Only." />
											</td>
										</tr>
								</table>
							</fieldset>
						</td>
						<td width="1%" valign="top"></td>
						<td width="22%" valign="top">
							<fieldset>
							<legend>Display</legend>
								<table  cellpadding="0" cellspacing="2" width="100%" >
									<tr>
										<td width="100" id="tdcut_sew">Cutt. Qty</td>
										<td width="90">
										<input type="text" name="txt_cutting_qty" id="txt_cutting_qty" class="text_boxes_numeric" style="width:80px" disabled readonly/>
										</td>
									</tr>
									<tr>
										<td>Cuml. Issue Qty</td>
										<td >
										<input type="text" name="txt_cumul_issue_qty" id="txt_cumul_issue_qty" class="text_boxes_numeric" style="width:80px" disabled readonly/>
										</td>
									</tr>
									<tr>
										<td>Yet to Issue</td>
										<td>
										<input type="text" name="txt_yet_to_issue" id="txt_yet_to_issue" class="text_boxes_numeric" style="width:80px" disabled readonly/>
										</td>
									</tr>
									
								</table>
							</fieldset>
						</td>
						<td width="40%" valign="top">
							<div style="max-height:350px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
						</td>
                    </tr>
                    <tr>
                        <td align="center" colspan="9" valign="middle" class="button_container">
                            <?
							$date=date('d-m-Y');
                            echo load_submit_buttons( $permission, "fnc_issue_print_embroidery_entry", 0,0 ,"reset_form('printembro_1','list_view_country','','txt_issue_date,".$date."','childFormReset()')",1);
                            ?>
                            <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
                            <!--<input type="button" name="button" id="button" class="formbutton" style="width:90px" onClick="fn_with_source_report()"  value="Print" >
                             <input type="button" name="button" id="button" class="formbutton" style="width:90px" onClick="fn_without_source_report()"  value="Without Source" >-->
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                     <tr>
                    	<td colspan="10" align="center" id="data_panel"></td>
                    </tr>
                    <tr>
                    	<td colspan="10" align="center">
                    		<input id="print03" class="formbutton" style="width:90px;" value="Print 3" name="print03" onClick="fn_with_source_report3()" type="button">
                    	</td>
                    </tr>
				</table>
               <div style="width:980px; margin-top:5px;"  id="printing_production_list_view" align="center"></div>
            </form>
        </fieldset>
    </div>
	<div id="list_view_country" style="width:390px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
load_drop_down( 'requires/print_embro_issue_controller', $('#cbo_source').val()+'**'+$('#cbo_company_name').val(), 'load_drop_down_embro_issue_source', 'emb_company_td' );
load_emb_company($('#cbo_source').val());
//$('#cbo_embel_name').val(1);
//$('#cbo_embel_name').trigger('change');
<?
if(implode('*', $_SESSION['logic_erp']['mandatory_field'][601])) 
{
	$json_mandatory_field = json_encode($_SESSION['logic_erp']['mandatory_field'][601]);
	echo "var mandatory_field_arr= " . $json_mandatory_field . ";\n";
}
?>
if('<? echo implode('*', $_SESSION['logic_erp']['mandatory_field'][601]);?>')
{
	$.each(mandatory_field_arr, function(key, value){
		 
		$(("."+value)).css("color", "blue");
	})
}
</script>
</html>