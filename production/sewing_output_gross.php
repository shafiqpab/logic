<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create sewing output
				
Functionality	:	
JS Functions	:
Created by		:	Ashraful 
Creation date 	: 	09-03-2013
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

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sewing Out Info","../", 1, 1, $unicode,'','');

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
 $(document).ready(function() {
   $('#display_input_qty').hide();
});

function openmypage(page_link,title)
{
	if ( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	else
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var po_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
			var item_id=this.contentDoc.getElementById("hidden_grmtItem_id").value;
			var po_qnty=this.contentDoc.getElementById("hidden_po_qnty").value;
			var country_id=this.contentDoc.getElementById("hidden_country_id").value; 
					
			
				//freeze_window(5);
				$("#txt_order_qty").val(po_qnty);
				$("#cbo_item_name").val(item_id);
				$("#cbo_country_name").val(country_id);
				
				childFormReset();//child from reset
				get_php_form_data(po_id+'**'+item_id+'**'+country_id, "populate_data_from_search_popup", "requires/sewing_output_gross_controller" );
 				
				var variableSettings=$('#sewing_production_variable').val();
				var styleOrOrderWisw=$('#styleOrOrderWisw').val();
			
				
				var prod_reso_allo=$('#prod_reso_allo').val();
				
				show_list_view(po_id+'**'+item_id+'**'+country_id+'**'+prod_reso_allo,'show_dtls_listview','list_view_container','requires/sewing_output_gross_controller','');
				set_button_status(0, permission, 'fnc_sewing_output_entry',1,0);
				release_freezing();
			
		}
	}//end else
}//end function



function fnc_sewing_output_entry(operation)
{
	if(operation==4)
	{
		 var report_title=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_name').val()+'*'+$('#txt_mst_id').val()+'*'+report_title, "sewing_output_print", "requires/sewing_output_gross_controller" ) 
		 return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
 		if ( form_validation('cbo_company_name*txt_order_no*cbo_sewing_company*txt_sewing_date*txt_sewing_qty*txt_reporting_hour','Company Name*Order No*Sewing Company*Sewing Date*Sewing Quantity*Reporting Hour')==false )
		{
			return;
		}		
	else
		{
			
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_sewing_date').val(), current_date)==false)
			{
				alert("Sewing Date Can not Be Greater Than Current Date");
				return;
			}	
			/*if($("#cbo_source").val()==1 && ($("#cbo_sewing_line").val()==0 || $("#cbo_sewing_line").val()=="") )
			{
				alert("Please Select Sewing Line");return;
			}*/
			//freeze_window(operation);			
			var sewing_production_variable = $("#sewing_production_variable").val();
			var colorList = ($('#hidden_colorSizeID').val()).split(",");
			
			var i=0;var colorIDvalue='';
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
			
			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('cbo_company_name*cbo_country_name*sewing_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*cbo_source*cbo_sewing_company*cbo_location*cbo_floor*txt_sewing_date*cbo_sewing_line*txt_reporting_hour*txt_super_visor*txt_sewing_qty*txt_reject_qnty*txt_alter_qnty*txt_remark*hidden_break_down_html*txt_mst_id*prod_reso_allo*txt_spot_qnty*save_data*defect_type_id*all_defect_id*save_dataBack*allBack_defect_id*defectBack_type_id*save_dataWest*allWest_defect_id*defectWest_type_id*save_dataMeasure*allMeasure_defect_id*defectMeasure_type_id',"../");
			
 			 
 			http.open("POST","requires/sewing_output_gross_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_sewing_output_entry_Reply_info;
		}
	}
}
  
function fnc_sewing_output_entry_Reply_info()
{
 	if(http.readyState == 4) 
	{
		
		var variableSettings=$('#sewing_production_variable').val();
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();
		var item_id=$('#cbo_item_name').val();
		var country_id = $("#cbo_country_name").val();
		var prod_reso_allo=$('#prod_reso_allo').val();
		
		var reponse=trim(http.responseText).split('**');		 
		if(reponse[0]==15) 
		{ 
			 setTimeout('fnc_sewing_output_entry('+ reponse[1]+')',8000); 
		}
		if(reponse[0]==0)
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
 			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+prod_reso_allo,'show_dtls_listview','list_view_container','requires/sewing_output_gross_controller','');
			reset_form('','','txt_sewing_date*cbo_sewing_line*txt_reporting_hour*txt_super_visor*txt_sewing_qty*txt_reject_qnty*txt_alter_qnty*txt_challan*txt_remark*txt_input_quantity*txt_cumul_sewing_qty*txt_yet_to_sewing*hidden_break_down_html*txt_mst_id*txt_spot_qnty*save_data*defect_type_id*all_defect_id*save_dataBack*allBack_defect_id*defectBack_type_id*save_dataWest*allWest_defect_id*defectWest_type_id*save_dataMeasure*allMeasure_defect_id*defectMeasure_type_id','','');
			get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/sewing_output_gross_controller" );
 		
			release_freezing();
		}
		if(reponse[0]==1)
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+prod_reso_allo,'show_dtls_listview','list_view_container','requires/sewing_output_gross_controller','');
			reset_form('','','txt_sewing_date*cbo_sewing_line*txt_reporting_hour*txt_super_visor*txt_sewing_qty*txt_reject_qnty*txt_alter_qnty*txt_challan*txt_remark*txt_input_quantity*txt_cumul_sewing_qty*txt_yet_to_sewing*hidden_break_down_html*txt_mst_id*txt_spot_qnty*save_data*defect_type_id*all_defect_id*save_dataBack*allBack_defect_id*defectBack_type_id*save_dataWest*allWest_defect_id*defectWest_type_id*save_dataMeasure*allMeasure_defect_id*defectMeasure_type_id','','');
			get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/sewing_output_gross_controller" );
		
			set_button_status(0, permission, 'fnc_sewing_output_entry',1,0);
			release_freezing();
		}
		if(reponse[0]==2)
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+prod_reso_allo,'show_dtls_listview','list_view_container','requires/sewing_output_gross_controller','');
			reset_form('','','txt_sewing_date*cbo_sewing_line*txt_reporting_hour*txt_super_visor*txt_sewing_qty*txt_reject_qnty*txt_alter_qnty*txt_challan*txt_remark*txt_input_quantity*txt_cumul_sewing_qty*txt_yet_to_sewing*hidden_break_down_html*txt_mst_id*txt_spot_qnty','','');
			get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/sewing_output_gross_controller" );
			
			set_button_status(0, permission, 'fnc_sewing_output_entry',1,0);
			release_freezing();
		}
 	}
} 

function childFormReset()
{
	reset_form('','','txt_sewing_date*cbo_sewing_line*txt_reporting_hour*txt_super_visor*txt_sewing_qty*txt_reject_qnty*txt_alter_qnty*txt_challan*txt_remark*txt_input_quantity*txt_cumul_sewing_qty*txt_yet_to_sewing*hidden_break_down_html*txt_mst_id*txt_spot_qnty','','');
 	$('#txt_input_quantity').attr('placeholder','');//placeholder value initilize
	$('#txt_cumul_sewing_qty').attr('placeholder','');//placeholder value initilize
	$('#txt_yet_to_sewing').attr('placeholder','');//placeholder value initilize
	$('#list_view_container').html('');//listview container
	$("#breakdown_td_id").html('');

}  

function fn_hour_check(val)
{
	if(val*1>12)
	{
		alert("You Cross 12!!This is 12 Hours.");
		$("#txt_reporting_hour").val('');
	}
}

function fn_total(tableName,index) // for color and size level
{
    var filed_value = $("#colSize_"+tableName+index).val();
	var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder');
	if(filed_value*1 > placeholder_value*1)
	{
		if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )	
			void(0);
		else
		{
			$("#colSize_"+tableName+index).val('');
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
	$("#txt_sewing_qty").val(totalVal);
}

function fn_colorlevel_total(index) //for color level
{
	
	var filed_value = $("#colSize_"+index).val();
	var placeholder_value = $("#colSize_"+index).attr('placeholder');
	if(filed_value*1 > placeholder_value*1)
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
	$("#txt_sewing_qty").val( $("#total_color").val() );
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
function openmypage_defectQty(type)
{
	var txt_mst_id=$("#txt_mst_id").val();
	var company_name=$("#cbo_company_name").val();
	var txt_job_no=$('#txt_job_no').val();
	var txt_order_no=$('#txt_order_no').val();
	var hidden_po_break_down_id=$('#hidden_po_break_down_id').val();
	if(txt_order_no=='')
	{
		alert('Please Order No Browse First.');
		return;
	}
	else
	{
		if(type==1) //Front Back
		{
			var save_data=$('#save_data').val();
			var all_defect_id=$('#all_defect_id').val();
			//var defect_qty=$('#txt_alter_qnty').val();
		}
		else if(type==2) //back*/
		{
			var save_data=$('#save_dataBack').val();
			var all_defect_id=$('#allBack_defect_id').val();
			//var defect_qty=$('#txt_spot_qnty').val();
		}
		else if(type==3) //West
		{
			var save_data=$('#save_dataWest').val();
			var all_defect_id=$('#allWest_defect_id').val();
			//var defect_qty=$('#txt_spot_qnty').val();
		}
		else if(type==4) //Measure
		{
			var save_data=$('#save_dataMeasure').val();
			var all_defect_id=$('#allMeasure_defect_id').val();
			//var defect_qty=$('#txt_spot_qnty').val();
		}

		var defect_qty=0; var title = '';
		if (type==1) //Front Back
		{
			defect_qty=$('#txt_alter_qnty').val();
			title = 'Front Info';
		}
		else if (type==2) //back
		{
			defect_qty=$('#txt_spot_qnty').val();
			title = 'Back Qty Info';
		}

		var page_link = 'requires/sewing_output_gross_controller.php?hidden_po_break_down_id='+hidden_po_break_down_id+'&txt_mst_id='+txt_mst_id+'&save_data='+save_data+'&defect_qty='+defect_qty+'&all_defect_id='+all_defect_id+'&type='+type+'&action=defect_data';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=400px,center=1,resize=1,scrolling=0','');

		emailwindow.onclose=function()
		{
			var save_string=this.contentDoc.getElementById("save_string").value;
			var tot_defectQnty=this.contentDoc.getElementById("tot_defectQnty").value;
			var all_defect_id=this.contentDoc.getElementById("all_defect_id").value;
			var defect_type_id=this.contentDoc.getElementById("defect_type_id").value;
			//alert(save_string);
			if(type==1) //Front Back
			{
				$('#save_data').val(save_string);
				//$('#txt_alter_qnty').val(tot_defectQnty);
				$('#all_defect_id').val(all_defect_id);
				$('#defect_type_id').val(defect_type_id);
			}
			else if(type==2)  // Back
			{
				$('#save_dataBack').val(save_string);
				//$('#txt_spot_qnty').val(tot_defectQnty);
				$('#allBack_defect_id').val(all_defect_id);
				$('#defectBack_type_id').val(defect_type_id);
			}
			else if(type==3)  // West
			{
				$('#save_dataWest').val(save_string);
				//$('#txt_spot_qnty').val(tot_defectQnty);
				$('#allWest_defect_id').val(all_defect_id);
				$('#defectWest_type_id').val(defect_type_id);
			}
			else if(type==4)  // Measure
			{
				$('#save_dataMeasure').val(save_string);
				//$('#txt_spot_qnty').val(tot_defectQnty);
				$('#allMeasure_defect_id').val(all_defect_id);
				$('#defectMeasure_type_id').val(defect_type_id);
			}
			release_freezing();
		}
	}
}
    
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">

<? echo load_freeze_divs ("../",$permission);  ?>

<fieldset style="width:950px;">
<legend>Production Module</legend>  
			<form name="sewingoutput_1" id="sewingoutput_1" autocomplete="off" >
                <fieldset>
                    <table width="100%" border="0">
                        <tr>
                            <td width="102" class="must_entry_caption">Company</td>
                            <td>                                
								<? 
                                 echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/sewing_output_gross_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value,'load_variable_settings','requires/sewing_output_gross_controller');" );
                                ?>
                                <input type="hidden" id="sewing_production_variable" />	 
                                <input type="hidden" id="styleOrOrderWisw" />
                                <input type="hidden" id="prod_reso_allo" />
							</td>
                            <td width="102" class="must_entry_caption">Order No</td>
                            <td width="170">
								<input name="txt_order_no" placeholder="Double Click to Search" id="txt_order_no" onDblClick="openmypage('requires/sewing_output_gross_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Order Search')"  class="text_boxes" style="width:155px " readonly>
                                <input type="hidden" id="hidden_po_break_down_id" value="" />
							</td>
                            <td width="130" >Country</td>
                            <td width="170">
                                <?
                                    echo create_drop_down( "cbo_country_name", 170, "select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 );
                                ?> 
                            </td>
                        </tr>
                        <tr>    
                            <td width="102">Buyer</td>
                            <td width="170">
                            <?
                            echo create_drop_down( "cbo_buyer_name", 170, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 );
							?>
							</td>
                             <td width="102">Job No</td>
                            <td width="170">
                            <input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:155px " disabled readonly>	
							</td>
                            <td width="102">Style</td>
                            <td width="170">
							<input name="txt_style_no" id="txt_style_no" class="text_boxes"  style="width:158px " disabled readonly>
							</td>
                           
                        </tr>
                        <tr>  
                        	 <td width="102"> Item </td>
                             <td width="170">
							 <?
                             echo create_drop_down( "cbo_item_name", 170, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );	
                             ?>
							 </td>  
                             <td width="102">Order Qnty</td>
                             <td width="170">
							 <input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric" style="width:155px " disabled readonly>
							 </td>
                             <td width="102">Source</td>
                             <td width="170">
                             <?
                             echo create_drop_down( "cbo_source", 170, $knitting_source,"", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/sewing_output_gross_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_sewing_output', 'sew_company_td' );get_php_form_data(this.value,'line_disable_enable','requires/sewing_output_gross_controller');get_php_form_data($('#cbo_company_name').val()+'**'+this.value+'**'+$('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_country_name').val(), 'display_bl_qnty', 'requires/sewing_output_gross_controller');", 0, '1,3' );
							 ?>
                             </td>
                            
                        </tr>
                        <tr>
                         	 <td width="102" class="must_entry_caption">Sewing Company</td>
                             <td width="170" id="sew_company_td" >
                             <?
                             echo create_drop_down( "cbo_sewing_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "" );
							 ?>
						     </td>
                             <td width="102">Location</td>
                             <td width="170" id="location_td">
                             <?
                             echo create_drop_down( "cbo_location", 167,$blank_array,"", 1, "-- Select Location --", $selected, "" );
							 ?>
							 </td>
                             <td width="102">Floor</td>
                              <td width="170" id="floor_td">
                              <? 
                              echo create_drop_down( "cbo_floor", 170, $blank_array,"", 1, "-- Select Floor --", $selected, "" );
							  ?>
                              </td>
                        </tr>
                    </table>
                </fieldset>
                
                <table><tr><td colspan="6" height="5"></td></tr></table>
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                    	<td width="30%" valign="top">
                            <fieldset>
                            <legend>New Entry</legend>
                                <table  cellpadding="0" cellspacing="1" width="100%">
                                    <tr>
                                    	<td width="120" class="must_entry_caption">Sewing Date</td>
                                         <td width="" colspan="2"> 
                                         <input name="txt_sewing_date" id="txt_sewing_date" class="datepicker" type="text" value="<? echo date("d-m-Y")?>" style="width:100px;"  onChange="load_drop_down( 'requires/sewing_output_gross_controller', document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+this.value, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );" />
                                        </td>
                                     </tr>
                                     <tr>
                                        <td width="120">Sewing Line No</td> 
                                        <td id="sewing_line_td" colspan="2">            
                              			<?
                              			echo create_drop_down( "cbo_sewing_line", 110, $blank_array,"", 1, "Select Line", $selected, "",1,0 );		
							  			?>	
                              			</td> 
                                   </tr>
                                     <tr>
                                       <td class="must_entry_caption">Reporting Hour</td> 
                                       <td colspan="2">
                                          
                                         <input name="txt_reporting_hour" id="txt_reporting_hour" class="text_boxes" style="width:100px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyUp="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyPress="return numOnly(this,event,this.id);" maxlength="8" />
                                       </td>
                                     </tr>
                                   <tr>
                                         <td width="120">Supervisor</td> 
                                         <td width="" colspan="2"> 
                                         <input type="text" name="txt_super_visor" id="txt_super_visor" class="text_boxes"  style="width:100px">
                                         </td>
                                   </tr>
                                   <tr>
                                        <td width="120" valign="top" class="must_entry_caption">QC Pass Qty</td> 
                                        <td width="" valign="top" colspan="2"><input name="txt_sewing_qty" id="txt_sewing_qty" class="text_boxes_numeric"  style="width:100px"  >
                                            <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
                                            <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                        </td>
                                   </tr>
                                   <tr>
                                     <td>Alter Qty </td>
                                     <td><input type="text" name="txt_alter_qnty" id="txt_alter_qnty" class="text_boxes_numeric" style="width:100px; text-align:right" /> </td>
                                     <td><input type="hidden"  name="btn" id="btn" class="formbuttonplasminus" value="Front Check" style="width:70px" onClick="openmypage_defectQty(1);"/></td>
                                   </tr>
                                   <tr>
                                     <td >Spot Qty </td>
                                     <td><input type="text" name="txt_spot_qnty" id="txt_spot_qnty" class="text_boxes_numeric" style="width:100px; text-align:right" /></td>
                                      <td><input type="hidden" name="btn" id="btn" class="formbuttonplasminus" value="Back Check" style="width:70px" onClick="openmypage_defectQty(2);"/></td>
                                   </tr>
                                   <tr>
                                     <td valign="top">Reject Qty</td>
                                     <td valign="top"><input type="text" name="txt_reject_qnty" id="txt_reject_qnty" class="text_boxes_numeric" style="width:100px; " /></td>
                                     <td><input type="hidden" name="btn" id="btn" class="formbuttonplasminus" value="Westband Check" style="width:70px" onClick="openmypage_defectQty(3);"/></td>
                                   </tr>
                                    <tr>
                                         <td width="">Challan No</td> 
                                         <td>
                                           <input type="text" name="txt_challan" id="txt_challan" class="text_boxes" style="width:100px" />
                                         </td>
                                         <td><input type="hidden" name="btn" id="btn" class="formbuttonplasminus" value="MEASUREMENT" style="width:70px" onClick="openmypage_defectQty(4);"/></td>
                                    </tr>
                                   <tr>
                                     <td valign="top">Remarks</td>
                                     <td valign="top" colspan="2"><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:150px;" /></td>
                                   </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td width="1%" valign="top">
                        </td>
                         <td width="25%" valign="top">
                         <div id="display_input_qty">
                            <fieldset>
                            <legend>Display</legend>
                                <table  cellpadding="0" cellspacing="2" width="100%" >
                                    <tr>
                                        <td width="120">Input Quantity</td>
                                        <td>
                                            <input type="text" name="txt_input_quantity" id="txt_input_quantity" class="text_boxes_numeric" style="width:80px" readonly disabled  />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="120">Cumul. Sew. Qty</td>
                                        <td>
                                            <input type="text" name="txt_cumul_sewing_qty" id="txt_cumul_sewing_qty" class="text_boxes_numeric" style="width:80px" readonly disabled />
                                        </td>
                                    </tr>
                                     <tr>
                                        <td width="120">Yet to Sewing</td>
                                        <td>
                                            <input type="text" name="txt_yet_to_sewing" id="txt_yet_to_sewing" class="text_boxes_numeric" style="width:80px" / readonly disabled >
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>	
                            </div>
                     <tr>
		   				<td align="center" colspan="9" valign="middle" class="button_container">
							<?
                            echo load_submit_buttons( $permission, "fnc_sewing_output_entry", 0,0,"reset_form('sewingoutput_1','','','','childFormReset()')",1); 
                            ?>
                            <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly />
                            
                            <input type="hidden" name="save_data" id="save_data" readonly />
                            <input type="hidden" name="all_defect_id" id="all_defect_id" readonly />
                            <input type="hidden" name="defect_type_id" id="defect_type_id" readonly />
                            
                            <input type="hidden" name="save_dataBack" id="save_dataBack" readonly />
                            <input type="hidden" name="allBack_defect_id" id="allBack_defect_id" readonly />
                            <input type="hidden" name="defectBack_type_id" id="defectBack_type_id" readonly />
                            
                            <input type="hidden" name="save_dataWest" id="save_dataWest" readonly />
                            <input type="hidden" name="allWest_defect_id" id="allWest_defect_id" readonly />
                            <input type="hidden" name="defectWest_type_id" id="defectWest_type_id" readonly />
                            
                            <input type="hidden" name="save_dataMeasure" id="save_dataMeasure" readonly />
                            <input type="hidden" name="allMeasure_defect_id" id="allMeasure_defect_id" readonly />
                            <input type="hidden" name="defectMeasure_type_id" id="defectMeasure_type_id" readonly />
           				</td>
           				<td>&nbsp;</td>					
		  			</tr>
                </table>
                <div style="width:940px; margin-top:5px;"  id="list_view_container" align="center"></div>
            </form>
        </fieldset>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>