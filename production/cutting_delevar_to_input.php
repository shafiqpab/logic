<?
/*-------------------------------------------- Comments
Purpose			: 	This form will Cutting Delivery To Input Entry
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	25-11-2014
Purpose			:
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
echo load_html_head_contents("Cutting Delivery Info","../", 1, 1, $unicode,'','');
function arrayExclude($array,Array $excludeKeys){
    foreach($array as $key => $value){
        if(!in_array($key, $excludeKeys)){
            $return[$key] = $value;
        }
    }
    return $return;
}

$u_id=$_SESSION['logic_erp']['user_id'];
$level=return_field_value("user_level","user_passwd","id='$u_id' and valid=1 ","user_level");
//  print_r($_SESSION['logic_erp']['mandatory_field'][589]);
?>	
<script>
	<? $data_arr = json_encode($_SESSION['logic_erp']['data_arr'][589]);
	if ($data_arr)
		echo "var field_level_data= " . $data_arr . ";\n";
	//    echo "alert(JSON.stringify(field_level_data));";
	?>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";





function openmypage(page_link,title)
{
	var company = $("#cbo_company_name").val();
	var delivery_basis = $("#cbo_delivery_basis").val();
	var page_link='requires/cutting_delevar_to_input_controller.php?action=order_popup&company='+company+'&garments_nature='+document.getElementById('garments_nature').value+'&delivery_basis='+delivery_basis;
	var title='Order Search';
	
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1040px,height=370px,center=1,resize=0,scrolling=0','')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		if(delivery_basis==1)
		{
			var po_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
			var item_id=this.contentDoc.getElementById("hidden_grmtItem_id").value; 
			var po_qnty=this.contentDoc.getElementById("hidden_po_qnty").value;
			var country_id=this.contentDoc.getElementById("hidden_country_id").value; 

			if (po_id!="")
			{
				freeze_window(5);
				release_freezing();
				$("#txt_order_qty").val(po_qnty);
				$("#cbo_item_name").val(item_id);
				$("#cbo_country_name").val(country_id);
				childFormReset();//child from reset
				get_php_form_data(po_id+'**'+item_id+'**'+country_id, "populate_data_from_search_popup", "requires/cutting_delevar_to_input_controller" );
				
				var variableSettings=$('#sewing_production_variable').val();
				var styleOrOrderWisw=$('#styleOrOrderWisw').val();
				if(variableSettings!=1) 
				{ 
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+company, "color_and_size_level", "requires/cutting_delevar_to_input_controller" ); 
				}
				else
				{
					$("#txt_ex_quantity").removeAttr("readonly");
					$("#txt_total_carton_qnty").removeAttr("readonly");
				}
				show_list_view(po_id,'show_country_listview','list_view_country','requires/cutting_delevar_to_input_controller','');
			}
  			set_button_status(0, permission, 'fnc_cutDelivery',1,0);
		}
		else if( delivery_basis==2 )
		{
			var all_data=this.contentDoc.getElementById("update_mst_id").value.split('_');//po id
			//alert(all_data)
			$("#txt_order_no").val(all_data[1]);
			show_list_view(all_data[1],'cutqc_level','list_view_country','requires/cutting_delevar_to_input_controller','');
			get_php_form_data(all_data[1], "populate_data_for_cutno_popup", "requires/cutting_delevar_to_input_controller" );
			$("#txt_ex_quantity").val($("#txt_bundel_total").val());
			$("#txt_total_carton_qnty").val($("#txt_bundel_number").val());
		}
		$("#cbo_company_name").attr("disabled","disabled"); 
	}
}

function fnc_cutDelivery(operation)
{
	
	if(operation==5)
	{
		if ( form_validation('txt_system_id','System Number')==false )
		{
			alert("Please save the delivery first"); return;
		}		
		else
		{
			 var cbo_delivery_basis= $("#cbo_delivery_basis").val();
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_ex_factory_date').val()+'*'+report_title+'*'+cbo_delivery_basis, "cut_delivery_print2", "requires/cutting_delevar_to_input_controller" ) 
			 return;
		}
	}
	else if(operation==4)
	{
		if ( form_validation('txt_system_id','System Number')==false )
		{
			alert("Please save the delivery first"); return;
		}		
		else
		{   var show_buyer_name='';
			var r=confirm("Press  \"Cancel\"  to hide  Buyer Name\nPress  \"OK\"  to Show Buyer Name");
			if (r==true) show_buyer_name="1"; else show_buyer_name="0";
			
			 var cbo_delivery_basis= $("#cbo_delivery_basis").val();
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_ex_factory_date').val()+'*'+report_title+'*'+cbo_delivery_basis+'*'+show_buyer_name, "cut_delivery_print", "requires/cutting_delevar_to_input_controller" ) 
			 return;
		}
	}
	else if(operation==0 || operation==1 || operation==2)
	{		
		if ('<?php echo implode('*', arrayExclude($_SESSION['logic_erp']['mandatory_field'][589],array(1))); ?>') 
		{
			if (form_validation('<?php echo implode('*',  arrayExclude($_SESSION['logic_erp']['mandatory_field'][589],array(1))); ?>', '<?php echo implode('*',  arrayExclude($_SESSION['logic_erp']['field_message'][589],array(1))); ?>') == false) {

				return;
			}
		}
		if ('<?php echo implode('*', arrayExclude($_SESSION['logic_erp']['mandatory_field'][589],array(2))); ?>') {
			if (form_validation('<?php echo implode('*',  arrayExclude($_SESSION['logic_erp']['mandatory_field'][589],array(2))); ?>', '<?php echo implode('*',  arrayExclude($_SESSION['logic_erp']['field_message'][589],array(2))); ?>') == false) {

				return;
			}
		}
		if ('<?php echo implode('*', arrayExclude($_SESSION['logic_erp']['mandatory_field'][589],array(3))); ?>') {
			if (form_validation('<?php echo implode('*',  arrayExclude($_SESSION['logic_erp']['mandatory_field'][589],array(3))); ?>', '<?php echo implode('*',  arrayExclude($_SESSION['logic_erp']['field_message'][589],array(3))); ?>') == false) {

				return;
			}
		}
		if ('<?php echo implode('*', arrayExclude($_SESSION['logic_erp']['mandatory_field'][589],array(4))); ?>') {
			if (form_validation('<?php echo implode('*',  arrayExclude($_SESSION['logic_erp']['mandatory_field'][589],array(4))); ?>', '<?php echo implode('*',  arrayExclude($_SESSION['logic_erp']['field_message'][589],array(4))); ?>') == false) {

				return;
			}
		}
		if ( form_validation('cbo_company_name*txt_order_no*txt_ex_quantity*txt_ex_factory_date','Company Name*Order No*ex-factory Quantity*Date')==false )
		{
			
			return;
		}
		else
		{
			var cbo_delivery_basis= $("#cbo_delivery_basis").val();
			var j=0; var colorIDvalue='';
			if(cbo_delivery_basis==1)
			{
			
				var current_date='<? echo date("d-m-Y"); ?>';
				if(date_compare($('#txt_ex_factory_date').val(), current_date)==false)
				{
					alert("Cutting Delivery Date Can not Be Greater Than Current Date");
					return;
				}	
				var sewing_production_variable = $("#sewing_production_variable").val();
				var colorList = ($('#hidden_colorSizeID').val()).split(",");
				
				var i=0;var colorIDvalue='';var k=1;var color_id="";var m=1;
				var tem_arr=new Array();
				var check_res=0;
				if(sewing_production_variable==0){sewing_production_variable=3;}
				if(sewing_production_variable==2)//color level
				{
					$("input[name=txt_color]").each(function(index, element) {
						if( $(this).val()!='' )
						{
							if(($('#txtbundle_'+m).val()*1)<=0)
							{
								check_res=1;
								return false;
								
							}
							if(i==0)
							{
								colorIDvalue = colorList[i]+"*"+$(this).val()+"*"+$('#txtbundle_'+m).val();
							}
							else
							{
								colorIDvalue += "**"+colorList[i]+"*"+$(this).val()+"*"+$('#txtbundle_'+m).val();
							}
							k++;
						}
						i++;
						m++;
					});
					if(check_res)
					{
						alert("Bundle Quantity Shold Be Greater Then Zero");
						$('#txtbundle_'+m).focus();
						check_res=0;
						return false;
					}
				}
				else if(sewing_production_variable==3)//color and size level
				{
					
					$("input[name=colorSize]").each(function(index, element) {
						
						color_id=colorList[i].split("*");
						
						if( jQuery.inArray( color_id[1], tem_arr ) == -1 ) 
						{
							tem_arr.push( color_id[1] );
							m=1;
						}
							
						if( $(this).val()!='')
						{
							if(($('#txtbundle_'+color_id[1]+m).val()*1)<=0)
							{
								check_res=1;
								return false;
								
							}
							else if(k==1)
							{
								colorIDvalue = colorList[i]+"*"+$(this).val()+"*"+$('#txtbundle_'+color_id[1]+m).val();
							}
							else
							{
								colorIDvalue += "***"+colorList[i]+"*"+$(this).val()+"*"+$('#txtbundle_'+color_id[1]+m).val();
							}
							k++;
						}
						i++;
						m++;
					});
					if(check_res)
					{
						alert("Bundle Quantity Shold Be Greater Then Zero");
						$('#txtbundle_'+color_id[1]+m).focus();
						check_res=0;
						return false;
					}
				}
			
			}
			else if(cbo_delivery_basis==2)
			{
				
				$("#bundle_table_body").find('tr').each(function()
				{
					if($(this).find('input[name="bundle_check[]"]').is(':checked'))
					{
						
					 var qcpassQty=$(this).find('input[name="qcpassQty[]"]').val();
					 var pobreakDownId=$(this).find('input[name="pobreakDownId[]"]').val();
					 var colorSizeId=$(this).find('input[name="colorSizeId[]"]').val();
					 var bundleNo=$(this).find('input[name="bundleNo[]"]').val();
					j++;
					colorIDvalue+='&qcpass_' + j + '=' + qcpassQty+ '&pobreakDownId_' + j + '=' +pobreakDownId + '&colorSizeId_' + j + '='+colorSizeId+ '&bundleNo_' + j + '='+bundleNo;
					}
				});
				
				if(j<1)
				{
					alert('No data found');
					return;
				}
				
			}
			
			
			
			var data="action=save_update_delete&operation="+operation+'&tot_row='+j+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*sewing_production_variable*cbo_location_name*cbo_item_name*hidden_po_break_down_id*hidden_colorSizeID*txt_ex_factory_date*txt_ex_quantity*txt_total_carton_qnty*txt_challan_no*txt_remark*txt_finish_quantity*txt_cumul_quantity*txt_yet_quantity*txt_mst_id*txt_system_no*txt_system_id*cbo_buyer_name*cbo_delivery_basis*cbo_knitting_source*cbo_cutting_company*cbo_cut_com_location*cbo_delivery_basis*txt_order_no*txt_cutting_update*cbo_sewing_source*cbo_sewing_company*cbo_sew_com_location',"../");
			//alert(data);return;
 			freeze_window(operation);
 			http.open("POST","requires/cutting_delevar_to_input_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_cutDelivery_Reply_info;
		}
	}
}
  
function fnc_cutDelivery_Reply_info()
{
 	if(http.readyState == 4)
	{
		var reponse=http.responseText.split('**');
		if(reponse[5]==1)
		{
			var variableSettings=$('#sewing_production_variable').val();
			var styleOrOrderWisw=$('#styleOrOrderWisw').val();
			var item_id=$('#cbo_item_name').val();
			var country_id = $("#cbo_country_name").val();
			
			if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_cutDelivery('+ reponse[1]+')',8000); 
			}
			else if(reponse[0]==0)
			{
				var po_id = reponse[1];
				show_msg(trim(reponse[0]));
				
				$("#txt_system_id").val(trim(reponse[2]));
				$("#txt_system_no").val(trim(reponse[3]));
				$("#txt_challan_no").val(trim(reponse[4]));
				
				show_list_view(reponse[2],'show_dtls_listview_mst','ex_factory_list_view','requires/cutting_delevar_to_input_controller','');
				setFilterGrid("details_table",-1);
				reset_form('','breakdown_td_id','txt_order_no*txt_ex_quantity*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*txt_remark*txt_finish_quantity*txt_cumul_quantity*txt_yet_quantity*txt_job_no*txt_style_no*txt_shipment_date*txt_order_qty*cbo_item_name*cbo_country_name*cbo_buyer_name','','');
				fn_remove_placeholder();
				
				set_button_status(0, permission, 'fnc_cutDelivery',1,1);
				release_freezing();
			} 
			else if(reponse[0]==1)
			{
				var po_id = reponse[1];
				show_msg(trim(reponse[0]));
				
				show_list_view(reponse[2],'show_dtls_listview_mst','ex_factory_list_view','requires/cutting_delevar_to_input_controller','');
				setFilterGrid("details_table",-1);
				reset_form('','breakdown_td_id','txt_ex_quantity*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*txt_remark*txt_finish_quantity*txt_cumul_quantity*txt_yet_quantity*txt_job_no*txt_style_no*txt_shipment_date*txt_order_qty*cbo_item_name*cbo_country_name*cbo_buyer_name','','');
				fn_remove_placeholder();
				set_button_status(0, permission, 'fnc_cutDelivery',1,1);
				release_freezing();
			}
			else if(reponse[0]==2)
			{
				var po_id = reponse[1];
				show_msg(trim(reponse[0]));
				show_list_view(reponse[2],'show_dtls_listview_mst','ex_factory_list_view','requires/cutting_delevar_to_input_controller','');
				setFilterGrid("details_table",-1);
				//show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id,'show_dtls_listview','ex_factory_list_view','requires/cutting_delevar_to_input_controller','');		
				reset_form('','breakdown_td_id','txt_ex_factory_date*txt_ex_quantity*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*txt_challan_no*txt_remark','','');
				fn_remove_placeholder();
				set_button_status(0, permission, 'fnc_cutDelivery',1,1);
				release_freezing();
			}
			else
			{
				fn_remove_placeholder();
				release_freezing();
			}
		}
		else if(reponse[5]==2)
		{
			if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_cutDelivery('+ reponse[1]+')',8000); 
			}
			else if(reponse[0]==0 || reponse[0]==1)
			{
				show_msg(trim(reponse[0]));
				
				$("#txt_mst_id").val(trim(reponse[1]));
				$("#txt_system_id").val(trim(reponse[2]));
				$("#txt_system_no").val(trim(reponse[3]));
				$("#txt_challan_no").val(trim(reponse[4]));
				set_button_status(1, permission, 'fnc_cutDelivery',1,1);
				release_freezing();	
			}
			else if(reponse[0]==10)
			{
			set_button_status(0, permission, 'fnc_cutDelivery',1,1);
			release_freezing();		
			}
		}
		release_freezing();	
 	}
} 

function childFormReset()
{
	reset_form('','','txt_ex_quantity*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*txt_remark*txt_finish_quantity*txt_cumul_quantity*txt_yet_quantity','','');
	$('#txt_ex_quantity').attr('placeholder','');//placeholder value initilize
	$('#txt_finish_quantity').attr('placeholder','');//placeholder value initilize
	$('#txt_cumul_quantity').attr('placeholder','');//placeholder value initilize
	$('#txt_yet_quantity').attr('placeholder','');//placeholder value initilize ex_factory_list_view
 	//$("#ex_factory_list_view").html('');
	$("#breakdown_td_id").html('');
}

  
function fn_total(tableName,index,ref) // for color and size level
{
	//alert(tableName);
    var filed_value = $("#colSize_"+tableName+index).val();
	var bundle_qnty = $("#txtbundle_"+tableName+index).val();
	var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder');
	var txt_user_lebel=$('#txt_user_lebel').val();
	var variable_is_controll=$('#variable_is_controll').val();
	if(ref==1)
	{
		if(filed_value*1 > placeholder_value*1)
		{
			if(variable_is_controll==1 && txt_user_lebel!=2)
			{
				alert("Qnty Excceded by"+(placeholder_value-filed_value));
				$("#colSize_"+tableName+index).val('');
				$("#txtbundle_"+tableName+index).val('');
				var totalVal=0;
				$("input[name=colorSize]").each(function(index, element) {
					totalVal += ( $(this).val() )*1;
				});
				$("#txt_ex_quantity").val(totalVal);
				return;
			}
			else
			{
				page_link='requires/cutting_delevar_to_input_controller.php?action=confirm_popup'+'&placeholder_value='+placeholder_value+'&filed_value='+filed_value;;
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Confirm Msg', 'width=200px,height=80px,center=1,resize=0,scrolling=0','');
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0];
					var decision_ref=this.contentDoc.getElementById("hidden_ref").value;
					if(decision_ref==2)
					{
						$("#colSize_"+tableName+index).val('');
						$("#txtbundle_"+tableName+index).val('');
						var totalVal=0;
						$("input[name=colorSize]").each(function(index, element) {
							totalVal += ( $(this).val() )*1;
						});
						$("#txt_ex_quantity").val(totalVal);
						return;
						
					}
					else
					{
						//void(2);
						var totalRow = $("#table_"+tableName+" tr").length;
						//alert(tableName);
						math_operation( "total_"+tableName, "colSize_"+tableName, "+", totalRow);
						if($("#total_"+tableName).val()*1!=0)
						{
							$("#total_"+tableName).html($("#total_"+tableName).val());
						}
						var totalVal=0;
						$("input[name=colorSize]").each(function(index, element) {
							totalVal += ( $(this).val() )*1;
						});
						$("#txt_ex_quantity").val(totalVal);
					}
				}
			}
		}
		else
		{
			/*var totalRow = $("#table_"+tableName+" tr").length;
			//alert(tableName);
			math_operation( "total_"+tableName, "colSize_"+tableName, "+", totalRow);
			if($("#total_"+tableName).val()*1!=0)
			{
				$("#total_"+tableName).html($("#total_"+tableName).val());
			}
			var totalVal=totalbundle= 0;
			$("input[name=colorSize]").each(function(index, element) {
				totalVal += ( $(this).val() )*1;
			});
			$("#txt_ex_quantity").val(totalVal);*/
			
			
		}
	}
	else
	{
		var totalbundle=0;
		
		$("input[name=txt_bundle]").each(function(index, element) {
			totalbundle += ( $(this).val() )*1;
		});
		$("#txt_total_carton_qnty").val(totalbundle);	
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
	$("#txt_ex_quantity").val(totalVal);
	
	var totalbundle=0;
	$("input[name=txt_bundle]").each(function(index, element) {
		totalbundle += ( $(this).val() )*1;
	});
	$("#txt_total_carton_qnty").val(totalbundle);
	
}

function fn_colorlevel_total(index,ref) //for color level
{
	var filed_value = $("#colSize_"+index).val();
	var placeholder_value = $("#colSize_"+index).attr('placeholder');
	//alert(placeholder_value);
	
	/*if(filed_value*1 > placeholder_value*1)
	{
		if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )	
			void(0);
		else
		{
			$("#colSize_"+index).val('');
 		}
	}
	
    var totalRow = $("#table_color tbody tr").length;
	//alert(totalRow);
	math_operation( "total_color", "colSize_", "+", totalRow);
	$("#txt_ex_quantity").val( $("#total_color").val() );*/
	
	
	var totalRow = $("#table_color tbody tr").length;
	if(ref==1)
	{
		if(filed_value*1 > placeholder_value*1)
		{
			page_link='requires/cutting_delevar_to_input_controller.php?action=confirm_popup'+'&placeholder_value='+placeholder_value+'&filed_value='+filed_value;;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Confirm Msg', 'width=200px,height=80px,center=1,resize=0,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var decision_ref=this.contentDoc.getElementById("hidden_ref").value;
				if(decision_ref==2)
				{
					$("#colSize_"+index).val('');
					$("#txtbundle_"+index).val('');
					math_operation( "total_color", "colSize_", "+", totalRow);
					$("#txt_ex_quantity").val( $("#total_color").val() );
					return;
					
				}
				else
				{
					math_operation( "total_color", "colSize_", "+", totalRow);
					$("#txt_ex_quantity").val( $("#total_color").val() );
				}
			}
		}
		else
		{
			math_operation( "total_color", "colSize_", "+", totalRow);
			$("#txt_ex_quantity").val( $("#total_color").val() );
		}
	}
	else
	{
		math_operation( "total_bundle", "txtbundle_", "+", totalRow);
		$("#txt_total_carton_qnty").val( $("#total_bundle").val() );
	}
	
	math_operation( "total_color", "colSize_", "+", totalRow);
	$("#txt_ex_quantity").val( $("#total_color").val() );
	math_operation( "total_bundle", "txtbundle_", "+", totalRow);
	$("#txt_total_carton_qnty").val( $("#total_bundle").val() );
	
} 

function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty)
{
	freeze_window(5);
	
	$("#cbo_item_name").val(item_id);
	$("#txt_order_qty").val(po_qnty);
	$("#cbo_country_name").val(country_id);

	childFormReset();//child from reset
	get_php_form_data(po_id+'**'+item_id+'**'+country_id, "populate_data_from_search_popup", "requires/cutting_delevar_to_input_controller" );
	
	var variableSettings=$('#sewing_production_variable').val();
	var styleOrOrderWisw=$('#styleOrOrderWisw').val();
	if(variableSettings!=1)
	{ 
		get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id, "color_and_size_level", "requires/cutting_delevar_to_input_controller" ); 
	}
	else
	{
		$("#txt_ex_quantity").removeAttr("readonly");
		$("#txt_total_carton_qnty").removeAttr("readonly");
		
	}
	set_button_status(0, permission, 'fnc_cutDelivery',1,0);
	release_freezing();
}

function delivery_sys_popup()
{
	
	var company = $("#cbo_company_name").val();
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var delivery_basis_id=$("#cbo_delivery_basis").val();
	var page_link='requires/cutting_delevar_to_input_controller.php?action=sys_surch_popup&company='+document.getElementById('cbo_company_name').value+'&delivery_basis_id='+delivery_basis_id;
	var title="Delivery System Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=930px,height=370px,center=1,resize=0,scrolling=0','')
	emailwindow.onclose=function()
	{
		var delivery_id=this.contentDoc.getElementById("hidden_delivery_id").value;
		//alert(delivery_id);return;
		if(delivery_id !="")
		{
			reset_form('exFactory_1','breakdown_td_id','','','','sewing_production_variable*styleOrOrderWisw*txt_user_lebel*variable_is_controll');
			fn_remove_placeholder();
			
			if(delivery_basis_id==1)
			{
				
			get_php_form_data(delivery_id, "populate_muster_from_date", "requires/cutting_delevar_to_input_controller" );
			show_list_view(delivery_id,'show_dtls_listview_mst','ex_factory_list_view','requires/cutting_delevar_to_input_controller','');
			setFilterGrid("details_table",-1);
			set_button_status(0, permission, 'fnc_cutDelivery',1,1);
			}
			else if(delivery_basis_id==2)
			{
			get_php_form_data(delivery_id, "populate_muster_from_date", "requires/cutting_delevar_to_input_controller" );
			get_php_form_data(delivery_id,'populate_cutDelivery_details','requires/cutting_delevar_to_input_controller');
			get_php_form_data(delivery_id,'populate_data_from_cutting_update','requires/cutting_delevar_to_input_controller');
			
			show_list_view(delivery_id+"_"+$("#txt_order_no").val(),'cutqc_level_update','list_view_country','requires/cutting_delevar_to_input_controller','');
			set_button_status(1, permission, 'fnc_cutDelivery',1,1);
			}
		}
		
	}

}

function fn_remove_placeholder()
{
	$('#txt_ex_quantity').removeAttr('placeholder');
	$('#txt_cumul_quantity').removeAttr('placeholder');
	$('#txt_yet_quantity').removeAttr('placeholder');
}

function change_capsion_name()
{
	var delivery_basis=$('#cbo_delivery_basis').val();
	//alert(delivery_basis)
	if(delivery_basis==1)
	{
		document.getElementById('order_bundle_caption').innerHTML="Order No";
		$('#order_bundle_caption').css('color','blue');
		$("#txt_order_no").attr("placeholder", "Double Click to Search");
	}
	else if(delivery_basis==2)
	{
		document.getElementById('order_bundle_caption').innerHTML="Cut Number";
		$('#order_bundle_caption').css('color','blue');
		$("#txt_order_no").attr("placeholder", "Browse/Scan");
	}
	else
	{
		document.getElementById('order_bundle_caption').innerHTML="Bundle No";
		$('#order_bundle_caption').css('color','blue');
		$("#txt_order_no").attr("placeholder", "Browse/Scan");
	}
	
}

$('#txt_order_no').live('keydown', function(e)
	 {
	 if (e.keyCode === 13)
	 {
	 e.preventDefault();
	 scan_cut_no(this.value); 
	 }
	});	
	
	function scan_cut_no(cutting_qcno)
	{
		var delivery_basis=$("#cbo_delivery_basis").val();
		if(delivery_basis==2)
		{
			
			show_list_view(cutting_qcno,'cutqc_level','list_view_country','requires/cutting_delevar_to_input_controller','');
			get_php_form_data(cutting_qcno, "populate_data_for_cutno_popup", "requires/cutting_delevar_to_input_controller" );
			$("#txt_ex_quantity").val($("#txt_bundel_total").val());
			$("#txt_total_carton_qnty").val($("#txt_bundel_number").val());	
		}
		
	}
	function calculate_bundle_qty()
	{
		var j=0; var qcpass_qty=0;
		$("#bundle_table_body").find('tr').each(function()
		{
			if($(this).find('input[name="bundle_check[]"]').is(':checked'))
			{
				j++;
				qcpass_qty+=parseInt($(this).find('input[name="qcpassQty[]"]').val());
			}
			else
			{
				$("#all_check").attr('checked', false);	
			}
		});	
		//alert(qcpass_qty)
		$("#txt_ex_quantity").val(qcpass_qty);
		$("#txt_total_carton_qnty").val(j);
	}
	
	
	function check_all(tot_check_box_id)
	{
		
		if ($("#all_check").is(":checked"))
		{ 
			$("#bundle_table_body").find('tr').each(function()
			{
				$(this).find('input[name="bundle_check[]"]').attr('checked', true);
			});
		}
		else
		{
			$("#bundle_table_body").find('tr').each(function()
			{
				$(this).find('input[name="bundle_check[]"]').attr('checked', false);
			});	
		}
		calculate_bundle_qty();
	}
	
function generate_report(type) // Print 2
	{
			var company = $("#cbo_company_name").val();
			if( form_validation('txt_system_no*cbo_company_name','System No*Company Name')==false )
			{
				return;
			}
		
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+$('#txt_ex_factory_date').val()+'*'+type, "cutting_delivery_to_input_print", "requires/cutting_delevar_to_input_controller" ) ;
			 
			
			//return;
			show_msg("3");
		
	}
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:1400px;">
	<?  echo load_freeze_divs ("../",$permission);  ?>
    <div style="width:930px; float:left" align="center">
        <form name="exFactory_1" id="exFactory_1" autocomplete="off" >     
        <fieldset style="width:930px;">
            <legend>Production Module</legend>
                <fieldset>                                       
                <table width="100%" border="0">
                	<tr>
                        <td align="right" colspan="3">Sys Challan No</td>
                        <td colspan="3"> 
                          <input name="txt_system_no" id="txt_system_no" class="text_boxes" type="text"  style="width:160px" onDblClick="delivery_sys_popup()" placeholder="Browse or Search" />
                          <input name="txt_system_id" id="txt_system_id" class="text_boxes" type="hidden"  style="width:100px"/>
                        </td>
                    </tr>
                    <tr>
                        <td width="130" align="right" class="must_entry_caption">Company Name </td>
                        <td width="170">
                            <?
                            echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", '', "load_drop_down( 'requires/cutting_delevar_to_input_controller', this.value, 'load_drop_down_location', 'location_td' ); get_php_form_data(this.value,'load_variable_settings','requires/cutting_delevar_to_input_controller');change_capsion_name()",0 ); ?>
                            <input type="hidden" name="sewing_production_variable" id="sewing_production_variable" value="" />
                            <input type="hidden" id="styleOrOrderWisw" />  
                            <input type="hidden" id="variable_is_controll" />
                            <input type="hidden" id="txt_user_lebel" value="<? echo $level; ?>" />
                        </td>
                        <td width="130" align="right">Location</td>
                        <td width="170" id="location_td">
                           <? echo create_drop_down( "cbo_location_name", 172, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                        </td>   
                        <td width="130" align="right" class="must_entry_caption">Delivery Date </td>
                        <td > 
                        <input name="txt_ex_factory_date" id="txt_ex_factory_date" class="datepicker" type="text"  style="width:160px;" >
                        </td>
                    </tr>
                    <tr>
                        <td align="right" >Delivery Basis</td>
                        <td>
                        <?
							$delivery_basis_arr=array(1=>"Order No",2=>"Cut Number",3=>"Bundle Number"); 
                        	echo create_drop_down( "cbo_delivery_basis", 172, $delivery_basis_arr,"", 1, "-- Select--", 1,"","1" );
                        ?>
                        </td>
                        <td align="right"> Challan No</td>
                        <td > 
                          <input name="txt_challan_no" id="txt_challan_no" class="text_boxes" type="text"  style="width:160px" maxlength="50" readonly disabled />
                        </td>
                        <td align="right" >Cutting Source</td>
                        <td >
                            <?
                                echo create_drop_down( "cbo_knitting_source", 170, $knitting_source,"", 1, "-- Select --", $selected, "load_drop_down( 'requires/cutting_delevar_to_input_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_knit_com', 'knitting_company_td' );","","1,3" );
                            ?>
                        </td>
                        
                    </tr>
                    <tr>
                    	<td  align="right" class="must_entry_caption">Cutting Company</td>
                        <td  id="knitting_company_td">
                            <?
                                echo create_drop_down( "cbo_cutting_company", 170, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                            ?>	
                        </td>
                        <td  align="right" class="must_entry_caption" >Cut. Company Location</td>
                        <td  id="cutt_com_location_td">
                            <?
                                echo create_drop_down( "cbo_cut_com_location", 170, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                            ?>	
                        </td>
                        <td align="right" >Sewing Source</td>
                        <td >
                            <?
                                echo create_drop_down( "cbo_sewing_source", 170, $knitting_source,"", 1, "-- Select --", $selected, "load_drop_down( 'requires/cutting_delevar_to_input_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_sewing_com', 'sewing_company_td' );","","1,3" );
                            ?>
                        </td>
                    </tr>
                    <tr>
                    	<td  align="right" class="must_entry_caption" >Sewing Company</td>
                        <td  id="sewing_company_td">
                            <?
                                echo create_drop_down( "cbo_sewing_company", 170, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                            ?>	
                        </td>
                        <td  align="right" class="must_entry_caption">Sewing Company Location</td>
                        <td  id="sew_com_location_td">
                            <?
                                echo create_drop_down( "cbo_sew_com_location", 170, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                            ?>	
                        </td>
                        
                    </tr>
                </table>
                </fieldset>
                <br /> 
                <table cellpadding="0" cellspacing="1" width="100%" id="child_table">
                    <tr>
                        <td width="30%" valign="top">
                          <fieldset>
                          <legend>New Entry</legend>
                            <table  cellpadding="0" cellspacing="2" width="100%">
                                <tr>
                                    <td width="130" align="right" class="must_entry_caption" id="order_bundle_caption">Order/Cut No</td>
                                    <td><input name="txt_order_no" id="txt_order_no"  placeholder="Double Click to Search" onDblClick="openmypage()" class="text_boxes" style="width:150px"  />
                                    <input type="hidden" id="hidden_po_break_down_id" value="" /></td>
                                </tr>
                                <tr>
                                    <td align="right" class="must_entry_caption" > Delivery Qnty</td>
                                    <td>
                                        <input name="txt_ex_quantity" id="txt_ex_quantity" class="text_boxes_numeric" type="text"  style="width:150px;" readonly />
                                        <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
                                        <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">Total Bundle Qnty</td>
                                    <td>
                                       <input name="txt_total_carton_qnty" id="txt_total_carton_qnty" type="text" class="text_boxes_numeric"  style="width:150px" readonly/>
                                    </td>
                                </tr>
                                <tr>
                                     <td width="102" align="right">Remarks</td> 
                                     <td width="165"> 
                                         <input name="txt_remark" id="txt_remark" type="text"  class="text_boxes" style="width:150px;" maxlength="450"  />
                                     </td>
                                </tr>
                           </table>
                        </fieldset>
                    </td>
                    <td width="1%" valign="top"></td>
                    <td width="28%" valign="top">
                          <fieldset>
                          <legend>Display</legend>
                              <table cellpadding="0" cellspacing="2" width="100%" >
                                 <tr>
                                      <td width="160" align="right"> Total Cut Qnty</td>
                                      <td>
                                          <input name="txt_finish_quantity" id="txt_finish_quantity"  class="text_boxes_numeric" type="text" style="width:100px" disabled readonly  />
                                      </td>
                                  </tr> 
                                  <tr>
                                      <td align="right">Total Delivery Qnty</td>
                                      <td>
                                          <input type="text" name="txt_cumul_quantity" id="txt_cumul_quantity" class="text_boxes_numeric"  style="width:100px" disabled readonly  />
                                      </td>
                                  </tr>
                                   <tr>
                                      <td align="right">Yet to Delivery Qnty</td>
                                      <td>
                                          <input type="text" name="txt_yet_quantity" id="txt_yet_quantity" class="text_boxes_numeric"  style="width:100px" disabled readonly />
                                      </td>
                                  </tr>
                                  <tr>
                                    <td align="right"> Job No.</td>
                                    <td>	
                                    <input style="width:100px;" type="text"   class="text_boxes" name="txt_job_no" id="txt_job_no" disabled  />
                                    </td>
                                  </tr>
                                  <tr>
                                    <td align="right"> Style </td>
                                    <td>	
                                    <input class="text_boxes" name="txt_style_no" id="txt_style_no" type="text" style="width:100px;" disabled />
                                    </td>
                                  </tr>
                                  <tr>
                                    <td align="right">Shipment Date</td>
                                    <td>
                                    <input class="text_boxes" name="txt_shipment_date" id="txt_shipment_date"   style="width:100px" disabled />
                                    </td>
                                  </tr>
                                  <tr>
                                    <td align="right">Order Qty.</td>
                                    <td>
                                    <input class="text_boxes"  name="txt_order_qty" id="txt_order_qty" type="text" style="width:100px;" disabled/>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td align="right">Item</td>   
                                    <td >
                                    <?
                                    echo create_drop_down( "cbo_item_name", 112, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );	
                                    ?>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td align="right">Country</td>
                                    <td>
                                    <?
                                    echo create_drop_down( "cbo_country_name", 112, "select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 );
                                    ?> 
                                    </td>
                                  </tr>
                                  <tr>
                                    <td align="right">Buyer Name</td>
                                    <td  id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 112, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1 );?></td>
                                  </tr>
                               </table>
                          </fieldset>
                      </td>
                    <td width="41%" valign="top">
                        <div style="max-height:530px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                    </td>    
                </tr>
                </table>
                <br />
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                        <td align="center" colspan="6" valign="middle" class="button_container">
                             <? 
                                echo load_submit_buttons( $permission, "fnc_cutDelivery", 0,1,"reset_form('exFactory_1','ex_factory_list_view*list_view_country','','','childFormReset()')",1);
                            ?>
                             <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
                             <input type="hidden" name="txt_cutting_update" id="txt_cutting_update" readonly >
                             <input type="button" value="Print2" onClick="fnc_cutDelivery(5)" style="width:80px" class="formbuttonplasminus" />
                             <input type="button" name="search" id="search" value="Cutting Wise" onClick="generate_report(1)" style="width:100px" class="formbuttonplasminus" />
                        </td>
                        
                    </tr> 
                </table>
                <div style="width:930px; margin-top:5px;"  id="ex_factory_list_view" align="center">
</div>
           </fieldset>
        </form>
    </div>
	<div id="list_view_country" style="width:388px;float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>   
</div>
</body> 
<script src="../includes/functions_bottom.js" type="text/javascript"></script>  
</html>