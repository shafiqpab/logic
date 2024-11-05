<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Garments Finishing Delivery Entry
				
Functionality	:	
JS Functions	:
Created by		:	Shafiq 
Creation date 	: 	10-01-2021
Updated by 		: 	
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$u_id=$_SESSION['logic_erp']['user_id'];
$level=return_field_value("user_level","user_passwd","id='$u_id' and valid=1 ","user_level");


//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$location_id = $userCredential[0][csf('location_id')];

$company_credential_cond = "";

if ($company_id >0) {
    $company_credential_cond = " and id in($company_id)";
}

if ($store_location_id !='') {
    $store_location_credential_cond = " and a.id in($store_location_id)"; 
}

if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;  
}
//========== user credential end ==========

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Garments Finishing Delivery Entry","../", 1, 1, $unicode,'','');

?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
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
	if ( form_validation('cbo_company_name*cbo_source*cbo_finish_company','Company Name*Production Source*Production Company')==false )
	{
		return;
	}

	else
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			freeze_window();
			var theform=this.contentDoc.forms[0];
			var po_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
			var item_id=this.contentDoc.getElementById("hidden_grmtItem_id").value;
			var po_qnty=this.contentDoc.getElementById("hidden_po_qnty").value;	
			var country_id=this.contentDoc.getElementById("hidden_country_id").value;
			var po_number=this.contentDoc.getElementById("hidden_po_no").value; 
			var style_no=this.contentDoc.getElementById("hidden_style_no").value; 
			var job_no=this.contentDoc.getElementById("hidden_job_no").value; 
			var buyer=this.contentDoc.getElementById("hidden_buyer").value; 
			var pack_type='';
			// alert(po_number);
				
			if (po_id!="")
			{
				childFormReset();//child from reset
				//freeze_window(5);
				// $("#txt_country_qty").val(po_qnty);
				$("#cbo_item_name").val(item_id);
				$("#cbo_country_name").val(country_id);
				$("#txt_job_no").val(job_no);
				$("#txt_style_no").val(style_no);
				$("#cbo_buyer_name").val(buyer);
				$("#txt_order_no").val(po_number);
				$("#hidden_po_break_down_id").val(po_id);

				
				// get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/garments_finishing_delivery_entry_controller" );
 				
				var variableSettings=$('#sewing_production_variable').val();
				var styleOrOrderWisw=$('#styleOrOrderWisw').val();
				var variableSettingsReject=$('#finish_production_variable_rej').val();
				var txt_job_no=$('#txt_job_no').val();
				// alert(variableSettings);
				var system_id = $('#txt_system_id').val();
				if(variableSettings!=1)
				{ 
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+txt_job_no+'**'+$('#hidden_preceding_process').val(), "color_and_size_level", "requires/garments_finishing_delivery_entry_controller");
				}
				else
				{
					get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/garments_finishing_delivery_entry_controller" );
					$("#txt_finishing_qty").removeAttr("readonly");
					get_php_form_data(po_id+'**'+item_id+'**'+txt_job_no, "gross_level_entry", "requires/garments_finishing_delivery_entry_controller");
				}
				
								
				var data = po_id+'**'+item_id+'**'+country_id+'**'+txt_job_no+'**'+system_id;
				$.get("requires/garments_finishing_delivery_entry_controller.php?action=show_all_listview&data="+data, function(data, status){
			      // alert("Data: " + data + "\nStatus: " + status);
			      dataEx = data.split('******');
			      // document.getElementById('list_view_container').innerHTML=dataEx[0];
			      document.getElementById('list_view_country').innerHTML=dataEx[1];
			    });


				// show_list_view(po_id+'**'+item_id+'**'+country_id+'**'+txt_job_no+'**'+pack_type,'show_dtls_listview','list_view_container','requires/garments_finishing_delivery_entry_controller','setFilterGrid(\'tbl_list_search\',-1)');
				// show_list_view(po_id,'show_country_listview','list_view_country','requires/garments_finishing_delivery_entry_controller','');
				
				set_button_status(0, permission, 'fnc_finishing_entry',1,0);
				release_freezing();
			}
			$("#cbo_company_name").attr("disabled","disabled"); 
			release_freezing();
		}
	}//end else
}//end function

function fnc_finishing_entry(operation)
{
	freeze_window(operation);
	var source=$("#cbo_source").val();
	if(operation==4)
	{
		 var report_title=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title, "delivery_entry_print", "requires/garments_finishing_delivery_entry_controller" );
		 release_freezing(); 
		 return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if ( form_validation('cbo_company_name*txt_order_no*cbo_finish_company*txt_delivery_date','Company Name*Order No*Finishing Company*Finishing Date')==false )
		{
			release_freezing(); 
			return;
		}		
		else
		 {
		 	if(source==1)
			{
				if ( form_validation('cbo_location*cbo_floor','Location*Floor')==false )
				{
					release_freezing(); 
					return;
				}

			}
			
			if($('#txt_finishing_qty').val()<1)
			{
				alert("Delivery quantity should be filled up.");
				release_freezing(); 
				return;
			}
			
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_delivery_date').val(), current_date)==false)
			{
				alert("Delivery Date Can not Be Greater Than Current Date");
				release_freezing(); 
				return;
			}	
						
			var sewing_production_variable = $("#sewing_production_variable").val();
			var colorList = ($('#hidden_colorSizeID').val()).split(",");
			var variableSettingsReject=$('#finish_production_variable_rej').val();

			if(sewing_production_variable=="" || sewing_production_variable==0)
			{
 				sewing_production_variable=3;
 				variableSettingsReject=3;
			}
			
			var i=0; var k=0; var colorIDvalue=''; var colorIDvalueRej='';
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
			}		
			
			 
			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+"&colorIDvalueRej="+colorIDvalueRej+get_submitted_data_string('garments_nature*cbo_company_name*sewing_production_variable*finish_production_variable_rej*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*cbo_country_name*txt_order_qty*cbo_source*cbo_finish_company*cbo_location*cbo_floor*txt_delivery_date*txt_finishing_qty*txt_carton_qty*txt_system_no*txt_system_id*txt_remark*txt_finish_input_qty*txt_cumul_delivery_qty*txt_yet_to_delivery*hidden_break_down_html*txt_mst_id',"../");
 			
 			http.open("POST","requires/garments_finishing_delivery_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_finishing_entry_Reply_info;
		}
	}
}
 

function fnc_finishing_entry_Reply_info()
{
 	if(http.readyState == 4) 
	{
		// alert(http.responseText);
		var variableSettings=$('#sewing_production_variable').val();
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();
		var variableSettingsReject=$('#finish_production_variable_rej').val();
		var item_id=$('#cbo_item_name').val();
		var country_id = $("#cbo_country_name").val();
		var pack_type='';
		
		var reponse=trim(http.responseText).split('**');
		

		if(reponse[0]==786)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		
		if(reponse[0]==25)
		{
			$("#txt_finishing_qty").val("");
			show_msg('31');
			release_freezing();
		}
		if(reponse[0]==35)
		{
			$("#txt_finishing_qty").val("");
			show_msg('25');
			alert(reponse[1]);
			release_freezing();
			return;
		}
		else if(reponse[0]==36)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		if(reponse[0]==505)
		{
			alert("Full Shipment Order Can't be saved,updated!!");
			release_freezing();
			return;
		}
        if (reponse[0].length>2) reponse[0]=10;
		
		if(reponse[0]==0)
		{ 
			document.getElementById('txt_system_no').value = reponse[2];
			document.getElementById('txt_system_id').value = reponse[3];
			var po_id = reponse[1];
			show_msg(reponse[0]);
			var txt_job_no=$('#txt_job_no').val();
 			show_list_view(reponse[3],'show_dtls_listview','list_view_container','requires/garments_finishing_delivery_entry_controller','setFilterGrid(\'tbl_list_search\',-1)');
			reset_form('','breakdown_td_id','txt_finishing_qty*txt_carton_qty*txt_finish_input_qty*txt_cumul_delivery_qty*txt_yet_to_delivery*hidden_break_down_html*txt_mst_id*txt_order_no*hidden_po_break_down_id*txt_job_no*txt_style_no*txt_order_qty*cbo_item_name*cbo_country_name*txt_country_qty*cbo_buyer_name','','','txt_delivery_date');
			// get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/garments_finishing_delivery_entry_controller" );
			
				
			release_freezing();
 			/*if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+txt_job_no+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "color_and_size_level", "requires/garments_finishing_delivery_entry_controller" ); 
			}
			else
			{
				get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/garments_finishing_delivery_entry_controller" );
				$("#txt_finishing_qty").removeAttr("readonly");
			}*/	
			set_button_status(0, '____1', 'fnc_finishing_entry',1,1);		
		}
		if(reponse[0]==1)
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			var txt_job_no=$('#txt_job_no').val();
			show_list_view(reponse[3],'show_dtls_listview','list_view_container','requires/garments_finishing_delivery_entry_controller','setFilterGrid(\'tbl_list_search\',-1)');
			reset_form('','breakdown_td_id','txt_finishing_qty*txt_carton_qty*txt_remark*txt_finish_input_qty*txt_cumul_delivery_qty*txt_yet_to_delivery*hidden_break_down_html*txt_mst_id*txt_order_no*hidden_po_break_down_id*txt_job_no*txt_style_no*txt_order_qty*cbo_item_name*cbo_country_name*txt_country_qty*cbo_buyer_name','','','txt_delivery_date');
			// get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/garments_finishing_delivery_entry_controller" );
			
			release_freezing();
			/*if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+txt_job_no+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "color_and_size_level", "requires/garments_finishing_delivery_entry_controller" ); 
			}
			else
			{
				get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/garments_finishing_delivery_entry_controller" );
				$("#txt_finishing_qty").removeAttr("readonly");
			}*/

			set_button_status(0, permission, 'fnc_finishing_entry',1,0);
		}
		if(reponse[0]==2)
		{
			if(reponse[4]==2)
			{
				var po_id = reponse[1];
				show_msg(trim(reponse[0]));
				var txt_job_no=$('#txt_job_no').val();
				show_list_view(reponse[3],'show_dtls_listview','list_view_container','requires/garments_finishing_delivery_entry_controller','setFilterGrid(\'tbl_list_search\',-1)');
				reset_form('','breakdown_td_id','txt_finishing_qty*txt_carton_qty*txt_remark*txt_finish_input_qty*txt_cumul_delivery_qty*txt_yet_to_delivery*hidden_break_down_html*txt_mst_id*txt_order_no*hidden_po_break_down_id*txt_job_no*txt_style_no*txt_order_qty*cbo_item_name*cbo_country_name*txt_country_qty*cbo_buyer_name','','','txt_delivery_date');
				// get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/garments_finishing_delivery_entry_controller" );
				
				release_freezing();
				/*if(variableSettings!=1) { 
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+txt_job_no+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "color_and_size_level", "requires/garments_finishing_delivery_entry_controller" ); 
				}
				else
				{
					get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/garments_finishing_delivery_entry_controller" );
					$("#txt_finishing_qty").removeAttr("readonly");
				}*/
			}
			if(reponse[4]==1)
			{
				release_freezing();
				location.reload();
			}	
			set_button_status(0, permission, 'fnc_finishing_entry',1,0);
		}

		if(reponse[0]==10)
		{
			show_msg(trim(reponse[0]));
			release_freezing();
			return;
		}
		
 	}
} 


function childFormReset()
{
	reset_form('','','txt_finishing_qty*txt_carton_qty*txt_finish_input_qty*txt_cumul_delivery_qty*txt_yet_to_delivery*hidden_break_down_html*txt_mst_id*txt_order_no*hidden_po_break_down_id','','');
	disable_enable_fields('cbo_company_name',0);
 	$('#txt_cumul_delivery_qty').attr('placeholder','');//placeholder value initilize
	$('#txt_yet_to_delivery').attr('placeholder','');//placeholder value initilize
	// $('#list_view_container').html('');//listview container
	$("#breakdown_td_id").html('');
}  

function system_number_popup()
{
	if ( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	else
	{		
		var page_link = 'requires/garments_finishing_delivery_entry_controller.php?action=system_number_popup&company='+$('#cbo_company_name').val()+'&location_name='+$('#cbo_location_name').val()+'&fnisn_company='+$('#cbo_finish_company').val()+'&source='+$('#cbo_source').val();
		
		var title = 'Garments Finishing Delivery Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=1','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			
			var responseDataArr=this.contentDoc.getElementById("hidden_search_data").value.split('_');

			document.getElementById('txt_system_id').value=responseDataArr[0];
			document.getElementById('txt_system_no').value=responseDataArr[1];
			
			get_php_form_data(responseDataArr[0], "populate_mst_form_data", "requires/garments_finishing_delivery_entry_controller" );			
			
			show_list_view(responseDataArr[0],'show_dtls_listview','list_view_container','requires/garments_finishing_delivery_entry_controller','setFilterGrid(\'tbl_list_search\',-1)');
			
			reset_form('','','txt_finishing_qty*txt_carton_qty*txt_finish_input_qty*txt_cumul_delivery_qty*txt_yet_to_delivery*hidden_break_down_html*txt_order_no*hidden_po_break_down_id*txt_mst_id','','');
			set_button_status(0, '____1', 'fnc_finishing_entry',1,1);
			release_freezing();
			
		}
		
	}
} 

function fn_total(tableName,index) // for color and size level
{
    var filed_value = $("#colSize_"+tableName+index).val();
	var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder');
	var txt_user_lebel=$('#txt_user_lebel').val();
	var hidden_variable_cntl=$('#hidden_variable_cntl').val()*1;
	
	if(filed_value*1 > placeholder_value*1)
	{
		if(hidden_variable_cntl==1 && txt_user_lebel!=2)
		{
			alert("Qnty Excceded by"+(placeholder_value-filed_value));
			$("#colSize_"+tableName+index).val('');
			$("#txt_finishing_qty").val('');
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
	var totalfinishAmount = 0;
	$("input[name=colorSize]").each(function(index, element) {
		var color_id=$(this).attr('id').split("_");
		var finish_amount=$("#colorSizefabricRate_"+color_id[1]).val()*( $(this).val() )*1;
		totalfinishAmount+=finish_amount;
        totalVal += ( $(this).val() )*1;
		
    });
	$("#txt_finishing_qty").val(totalVal);
	$("#fabric_data").val(totalfinishAmount);
}

function fn_total_rej(tableName,index) // for color and size level
{
    var filed_value = $("#colSizeRej_"+tableName+index).val();
	var colsizes= $("#colSize_"+tableName+index).val();
    if(colsizes=="" && filed_value !="")
    {
    	$("#colSize_"+tableName+index).val(0);
    }
	
	var totalRow = $("#table_"+tableName+" tr").length;
	//alert(tableName);
	math_operation( "total_"+tableName, "colSizeRej_"+tableName, "+", totalRow);
	
	var totalValRej = 0;
	$("input[name=colorSizeRej]").each(function(index, element) {
        totalValRej += ( $(this).val() )*1;
    });
	$("#txt_reject_qnty").val(totalValRej);
}

function fn_colorlevel_total(index) //for color level
{
 	var filed_value = $("#colSize_"+index).val();
	var placeholder_value = $("#colSize_"+index).attr('placeholder');
	var txt_user_lebel=$('#txt_user_lebel').val();
	var hidden_variable_cntl=$('#hidden_variable_cntl').val()*1;
	if(filed_value*1 > placeholder_value*1)
	{
		if(hidden_variable_cntl==1 && txt_user_lebel!=2)
		{
			alert("Qnty Excceded by"+(placeholder_value-filed_value));
			$("#colSize_"+index).val('');
			$("#txt_finishing_qty").val('');
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
	$("#txt_finishing_qty").val( $("#total_color").val() );
	var total_fabric_amount=0;
	for(var j=1;j<=totalRow;j++)
	{
		total_fabric_amount+=($("#colSize_"+j).val()*1)*($("#colorSizefabricRate_"+j).val()*1)
	}
	//alert(total_fabric_amount)
	$("#fabric_data").val(total_fabric_amount);
} 

function fn_colorRej_total(index) //for color level
{
	var filed_value = $("#colSizeRej_"+index).val();
    var totalRow = $("#table_color tbody tr").length;
	//alert(totalRow);
	math_operation( "total_color_rej", "colSizeRej_", "+", totalRow);
	$("#txt_reject_qnty").val( $("#total_color_rej").val() );
}

function fnc_company_check(val)  
{
	if(val==1)
	{
		if($("#cbo_company_name").val()==0)
		{
			alert("Please Select Company.");
			$("#cbo_source").val(0);
			$("#cbo_finish_company").val(0);
			return;
		}
		else
		{
			get_php_form_data(document.getElementById('cbo_finish_company').value,'production_process_control','requires/garments_finishing_delivery_entry_controller' );
		}
	}
	else
	{
		get_php_form_data(document.getElementById('cbo_company_name').value,'production_process_control','requires/garments_finishing_delivery_entry_controller' );
	}
}



function put_country_data(po_id, item_id, country_id, country_qnty, plan_qnty)
{
	freeze_window(5);
	
	$("#cbo_item_name").val(item_id);
	$("#txt_country_qty").val(country_qnty);
	$("#cbo_country_name").val(country_id);
 				
	// childFormReset();//child from reset
	// get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/garments_finishing_delivery_entry_controller" );
	
	var variableSettings=$('#sewing_production_variable').val();
	var styleOrOrderWisw=$('#styleOrOrderWisw').val();
	var variableSettingsReject=$('#finish_production_variable_rej').val();
	var txt_job_no=$('#txt_job_no').val();
	if(variableSettings!=1)
	{ 
		get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+txt_job_no+'**'+$('#hidden_preceding_process').val(), "color_and_size_level", "requires/garments_finishing_delivery_entry_controller");
	}
	else
	{
		get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/garments_finishing_delivery_entry_controller" );
		$("#txt_finishing_qty").removeAttr("readonly");
	}
	
	
			
	// show_list_view(po_id+'**'+item_id+'**'+country_id+'**'+txt_job_no,'show_dtls_listview','list_view_container','requires/garments_finishing_delivery_entry_controller','');
	set_button_status(0, permission, 'fnc_finishing_entry',1,0);
	release_freezing();
}

function fnc_valid_time(val,field_id)
{
	var val_length=val.length;
	if(val_length==2)
	{
		document.getElementById(field_id).value=val+":";
	}
	
	var colon_contains=val.includes(":");
	if(colon_contains==false)
	{
		if(val>23)
		{
			document.getElementById(field_id).value='23:';
		}
	}
	else
	{
		var data=val.split(":");
		var minutes=data[1];
		var str_length=minutes.length;
		var hour=data[0]*1;
		
		if(hour>23)
		{
			hour=23;
		}
		
		if(str_length>=2)
		{
			minutes= minutes.substr(0, 2);
			if(minutes*1>59)
			{
				minutes=59;
			}
		}
		
		var valid_time=hour+":"+minutes;
		document.getElementById(field_id).value=valid_time;
	}
}

function numOnly(myfield, e, field_id)
{
	var key;
	var keychar;
	if (window.event)
		key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;
	keychar = String.fromCharCode(key);

	// control keys
	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
	return true;
	// numbers
	else if ((("0123456789:").indexOf(keychar) > -1))
	{
		var dotposl=document.getElementById(field_id).value.lastIndexOf(":");
		if(keychar==":" && dotposl!=-1)
		{
			return false;
		}
		return true;
	}
	else
		return false;
}
function active_placeholder_qty(color_id) //color Size level
{
	$("#table_" + color_id).find("input[name=colorSize]").each(function(index, element) {
		if ($('#set_all_' + color_id).prop('checked') == true) {
			if ($(this).attr('placeholder') != '' && $(this).attr('placeholder') > 0) {
				$(this).val($(this).attr('placeholder')); 
			}
		} else {
			$(this).val('');
		}
	});

	var totalVal = 0;
	$("input[name=colorSize]").each(function(index, element) {
		totalVal += ($(this).val()) * 1;
	});
	$("#txt_finishing_qty").val(totalVal);	
}
function active_placeholder_qty_color(color_id) //color level
{
	$("#table_color").find("input[name=txt_color]").each(function(index, element) {
		if ($('#set_all').prop('checked') == true) 
		{
			if ($(this).attr('placeholder') != '' && $(this).attr('placeholder') > 0) {
				$(this).val($(this).attr('placeholder'));
			}
		} 
		else 
		{
			$(this).val('');
		}
	});

	var totalVal = 0;
	$("input[name=txt_color]").each(function(index, element) {
		totalVal += ($(this).val()) * 1;
	});
	$("#total_color").val(totalVal);	
	$("#txt_finishing_qty").val(totalVal); 
}   
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../",$permission);  ?>
 	<div style="width:900px; float:left" align="center">
        <fieldset style="width:930px;">
        <legend>Garments Finishing Delivery Entry</legend>  
            <form name="finishingentry_1" id="finishingentry_1" autocomplete="off" >
 				<fieldset>
                <table width="100%" border="0">
                	<tr>
                        <td align="right" colspan="3"><strong> Challan No</strong></td>
                        <td colspan="3"> 
                          <input name="txt_system_no" id="txt_system_no" class="text_boxes" type="text"  style="width:160px" onDblClick="system_number_popup()" placeholder="Browse or Search" />
                          <input name="txt_system_id" id="txt_system_id" class="text_boxes" type="hidden"  style="width:160px"/>
                        </td>
                    </tr>
                    <tr>
                        <td width="100" class="must_entry_caption">Company</td>
                        <td><? echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company where status_active =1 and is_deleted=0 $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "get_php_form_data(this.value,'load_variable_settings','requires/garments_finishing_delivery_entry_controller');" ); ?>	 
                            <input type="hidden" id="sewing_production_variable" />	 
                            <input type="hidden" id="styleOrOrderWisw" /> 
                            <input type="hidden" id="finish_production_variable_rej" />
                            <input type="hidden" id="txt_qty_source" />
                            <input type="hidden" id="variable_is_controll" />
                            <input type="hidden" id="txt_user_lebel" value="<? echo $level; ?>" />
                        </td>

                        <td class="must_entry_caption">Source</td>
                        <td id="source_td"><? echo create_drop_down( "cbo_source", 170, $knitting_source,"", 1, "-- Select Source --", $selected, "fnc_company_check(this.value);load_drop_down( 'requires/garments_finishing_delivery_entry_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_source', 'finishing_td' );dynamic_must_entry_caption(this.value);", 0, '1,3' ); ?></td>
                        <input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl" value="0">
                        <input type="hidden" name="hidden_preceding_process" id="hidden_preceding_process" value="0">

                        <td class="must_entry_caption">Finish. Company</td>
                        <td id="finishing_td"><? echo create_drop_down( "cbo_finish_company", 170, $blank_array,"", 1, "-Select finishing Company-", $selected, "" );?></td>


                         
                    </tr>

                <tr>
					<td id="locations">Location</td>
					<td id="location_td"><? echo create_drop_down( "cbo_location", 167, $blank_array, "", 1, "-- Select Location --", $selected, "" ); ?></td>
					<td id="floors">Floor</td>
                        <td id="floor_td"><? echo create_drop_down( "cbo_floor", 170, $blank_array, "", 1, "-- Select Floor --", $selected, "" ); ?></td>
                    <td class="must_entry_caption">Delivery Date</td>
                    <td> 
                        <input type="text" name="txt_delivery_date" id="txt_delivery_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:167px;"  />
                    </td>                	
                </tr>

                <tr>
                    <td id="floors">Remarks</td>
                    <td colspan="4">
                    	<input type="text" name="txt_remark" id="txt_remark" value="" class="text_boxes" style="width:100%;"  />
                    </td>
                    
                </tr>

                </table>
                </fieldset>
                <br />                 
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                    	<td width="35%" valign="top">
                           <fieldset>
                            <legend>New Entry</legend>
                            <table  cellpadding="0" cellspacing="2" width="100%">
                            	<tr>
                                    <td width="100" class="must_entry_caption">Order No</td>
									<td width="175">
									<input name="txt_order_no" placeholder="Browse" id="txt_order_no" onDblClick="openmypage('requires/garments_finishing_delivery_entry_controller.php?action=order_popup&company='+$('#cbo_company_name').val()+'&garments_nature='+$('#garments_nature').val()+'&production_company='+$('#cbo_finish_company').val()+'&hidden_variable_cntl='+$('#hidden_variable_cntl').val()+'&hidden_preceding_process='+$('#hidden_preceding_process').val(),'Order Search')"  class="text_boxes" style="width:120px " readonly />
									<input type="hidden" id="hidden_po_break_down_id" value="" />
									</td>
                                </tr>
                                <tr>
                                    <td >Delivery Qty</td>
                                    <td colspan="2"><input name="txt_finishing_qty" id="txt_finishing_qty" class="text_boxes_numeric"  style="width:120px; text-align:right" readonly />
                                        <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
                                        <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total Carton Qty</td>
                                    <td colspan="2"><input type="text" name="txt_carton_qty" id="txt_carton_qty" class="text_boxes_numeric"   style="width:120px; text-align:right" /></td>
                                </tr>
                            </table>
                            </fieldset>
                        </td>
                        <td width="1%" valign="top">
                        </td>
                        <td width="25%" valign="top">
                            <fieldset>
                            <legend>Display</legend>
                            <table  cellpadding="0" cellspacing="2" width="100%" >
                                <tr>
                                    <td width="120">Total Finish Qty</td> 
                                    <td><input type="text" name="txt_finish_input_qty" id="txt_finish_input_qty" class="text_boxes_numeric" style="width:100px" disabled /></td>
                                </tr>
                                <tr>
                                    <td width="">Total Delivery Qty</td>
                                    <td><input type="text" name="txt_cumul_delivery_qty" id="txt_cumul_delivery_qty" class="text_boxes_numeric" style="width:100px" disabled /></td>
                                </tr>
                                <tr>
                                    <td width="">Balance</td>
                                    <td><input type="text" name="txt_yet_to_delivery" id="txt_yet_to_delivery" class="text_boxes_numeric" style="width:100px" disabled /></td>
                                </tr>
                                <tr>
                                    <td width="">Job No</td>
                                    <td><input style="width:100px;" type="text"   class="text_boxes" name="txt_job_no" id="txt_job_no" disabled  /></td>
                                </tr>
                                <tr>
                                    <td width="">Style</td>
                                    <td><input class="text_boxes" name="txt_style_no" id="txt_style_no" type="text" style="width:100px;" disabled /></td>
                                </tr>
                                <tr>
                                    <td width="">Order Qty.</td>
                                    <td><input class="text_boxes"  name="txt_order_qty" id="txt_order_qty" type="text" style="width:100px;" disabled/></td>
                                </tr>
                                <tr>
                                    <td width="">Item</td>
                                    <td><?
                                    echo create_drop_down( "cbo_item_name", 112, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );	
                                    ?></td>
                                </tr>
                                <tr>
                                    <td width="">Country</td>
                                    <td><?
                                    echo create_drop_down( "cbo_country_name", 112, "select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 );
                                    ?></td>
                                </tr>
                                <tr>
                                    <td width="">Country Qnty.</td>
                                    <td><input type="text" name="txt_country_qty" id="txt_country_qty" class="text_boxes_numeric" style="width:100px" disabled /></td>
                                </tr>
                                <tr>
                                    <td width="">Buyer Name</td>
                                    <td  id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 112, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1 );?></td>
                                  </tr>
                                </tr>
                                
                            </table>
                            </fieldset>
                        </td>
                        <td width="43%" valign="top" >
                            <div style="max-height:300px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                        </td>
                    </tr>
                     <tr>
		   				<td align="center" colspan="9" valign="middle" class="button_container">
           				<?
							$date=date('d-m-Y');
							echo load_submit_buttons( $permission, "fnc_finishing_entry", 0,1 ,"reset_form('finishingentry_1','list_view_container*list_view_country','','txt_delivery_date,".$date."','childFormReset();')",1); 
		   				?>
                        <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly />
           				</td>
           				<td>&nbsp;</td>					
		  			</tr>
                </table>
                <div style="width:930px; margin-top:5px;"  id="list_view_container" align="center"></div>
            </form>
        </fieldset>
	</div>
	<!-- <div id="list_view_country" style="width:400px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:40px"></div>         -->
	<div id="list_view_country" style="width:400px; overflow:auto; padding-top:5px; position:absolute; left:950px"></div>        
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>