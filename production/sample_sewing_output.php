<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create sample sewing output
				
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza 
Creation date 	: 	08-04-2015
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
 

function openmypage(page_link,title)
{
	
	if ( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	else
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=940px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var smp_id=this.contentDoc.getElementById("selected_id").value;//po id
					
			if (smp_id!="")
			{
				//freeze_window(5);
				$("#txt_sample_devlopment_id").val(smp_id);
				get_php_form_data(smp_id, "populate_data_from_search_popup", "requires/sample_sewing_output_controller" );
				
				show_list_view(smp_id,'show_sample_item_listview','list_view_country','requires/sample_sewing_output_controller','');		
				
				show_list_view(smp_id,'show_dtls_listview','list_view_container','requires/sample_sewing_output_controller','');
				
				//childFormReset();//child from reset
				
				set_button_status(0, permission, 'fnc_sample_sewing_output_entry',1,0);
				release_freezing();
			}
		}
	}
}//end function




function put_sample_item_data(mst_id,smp_id)
{
	freeze_window(5);
	
	
/*	
	
	//childFormReset();//child from reset
	get_php_form_data(smp_id, "populate_data_from_search_popup", "requires/sample_sewing_output_controller" );
	
	var variableSettings=$('#sewing_production_variable').val();
	var variableSettingsReject=$('#sewing_production_variable_rej').val();
	var styleOrOrderWisw=$('#styleOrOrderWisw').val();
	var prod_reso_allo=$('#prod_reso_allo').val();
	
	if(variableSettings!=1)
	{ 
		get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject, "color_and_size_level", "requires/sample_sewing_output_controller" ); 
		$("#txt_qc_pass_qty").attr("readonly","readonly");
	}
	else
	{
		$("#txt_qc_pass_qty").removeAttr("readonly");
	}
	
	if(variableSettingsReject!=1)
	{
		$("#txt_reject_qnty").attr("readonly");
	}
	else
	{
		$("#txt_reject_qnty").removeAttr("readonly");
	}
	
	
*/	
	
	get_php_form_data(mst_id+'**'+smp_id, "color_and_size_level", "requires/sample_sewing_output_controller" ); 

	
	//show_list_view(smp_id,'show_dtls_listview','list_view_container','requires/sample_sewing_output_controller','');
	
	set_button_status(0, permission, 'fnc_sample_sewing_output_entry',1,0);
	release_freezing();
}



function fn_total(tableName,index) // for color and size level
{
    var filed_value = $("#colSizeQty_"+tableName+index).val();
	var placeholder_value = $("#colSizeQty_"+tableName+index).attr('placeholder');
	
	if(filed_value*1 > placeholder_value*1)
	{
		if( confirm("Qnty Excceded by "+(placeholder_value-filed_value)) )	
			void(0);
		else
		{
			$("#colSizeQty_"+tableName+index).val('');
 		}
	}
	
	var totalRow = $("#table_"+tableName+" tr").length;
	math_operation( "total_"+tableName, "colSizeQty_"+tableName, "+", totalRow);
	if($("#total_"+tableName).val()*1!=0)
	{
		$("#total_"+tableName).html($("#total_"+tableName).val());
	}
	
	var totalVal = 0;
	$("input[name=colSizeQty]").each(function(index, element) {
        totalVal += ( $(this).val() )*1;
    });
	$("#txt_qc_pass_qty").val(totalVal);
}



function fn_total_rej(tableName,index) // for color and size level
{
    var filed_value = $("#colSizeRej_"+tableName+index).val();
	var totalRow = $("#table_"+tableName+" tr").length;
	math_operation( "total_"+tableName, "colSizeRej_"+tableName, "+", totalRow);
	var totalValRej = 0;
	$("input[name=colorSizeRej]").each(function(index, element) {
        totalValRej += ( $(this).val() )*1;
    });
	$("#txt_reject_qnty").val(totalValRej);
}



function fnc_sample_sewing_output_entry(operation)
{
	if(operation==4)
	{
		 print_report( $('#cbo_company_name').val()+'*'+$('#mst_update_id').val()+'*'+$("#cbo_sample_name").val(), "sewing_output_print", "requires/sample_sewing_output_controller" ) 
		 return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{	
 		if (form_validation('cbo_company_name*txt_sample_devlopment_id*cbo_sewing_company*txt_sewing_date*cbo_sewing_line*txt_reporting_hour*txt_qc_pass_qty*txt_challan','Company Name*Sample Devlopment ID*Sewing Company*Sewing Date*Sewing Line*Reporting Hour*QC Pass Qty*Challan')==false)
		{
			return;
			
		}		
		else
		{
			//freeze_window(operation);			
			
			var colorList = ($('#hidden_colorSizeID').val()).split(",");
			var i=0;  var k=0; var colorIDvalue=''; var colorIDvalueRej='';
			
 				$("input[name=colSizeQty]").each(function(index, element) {
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
			
			
			
			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+"&colorIDvalueRej="+colorIDvalueRej+get_submitted_data_string('mst_update_id*dtls_update_id*cbo_company_name*txt_sample_devlopment_id*cbo_buyer_name*txt_style_no*cbo_item_name*txt_sample_qty*cbo_source*cbo_sewing_company*cbo_location*cbo_floor*cbo_sample_name*txt_sewing_date*cbo_sewing_line*txt_reporting_hour*txt_supervisor*txt_qc_pass_qty*txt_alter_qnty*txt_spot_qnty*txt_reject_qnty*txt_challan*txt_remark',"../");

 			http.open("POST","requires/sample_sewing_output_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_sample_sewing_output_entry_Reply_info;
		
		}
	}
}
  
function fnc_sample_sewing_output_entry_Reply_info()
{
 	if(http.readyState == 4) 
	{
		
		var reponse=http.responseText.split('**');		 
		
		if(reponse[0]==0)//insert response;
		{
			show_msg(trim(reponse[0]));
			show_list_view(reponse[2],'show_dtls_listview','list_view_container','requires/sample_sewing_output_controller','');
			$('#mst_update_id').val(reponse[1]);
			$('#txt_sys_chln').val(reponse[4]);
			$('#breakdown_td_id').html('');
			childFormReset();
		}
		else if(reponse[0]==1)//update response;
		{
			show_msg(trim(reponse[0]));
			
			show_list_view(reponse[2],'show_dtls_listview','list_view_container','requires/sample_sewing_output_controller','');
			childFormReset();
			set_button_status(0, permission, 'fnc_sample_sewing_output_entry',1,0);
		}
		else if(reponse[0]==2)//delete response;
		{
			show_msg(trim(reponse[0]));
			set_button_status(0, permission, 'fnc_sample_sewing_output_entry',1,0);
		}
		
		release_freezing();
 	}
} 


function childFormReset()
{
	reset_form('','','cbo_sample_name*txt_sewing_date*cbo_sewing_line*txt_reporting_hour*txt_supervisor*txt_qc_pass_qty*txt_alter_qnty*txt_spot_qnty*txt_reject_qnty*txt_challan*txt_sys_chln*txt_remark','','');
}//fnc;



function fnc_valid_time(val,field_id)
{
	var val_length=val.length;
	if(val_length==2)
	{
		document.getElementById(field_id).value=val+":";
	}
	
	var colon_contains=val.contains(":");
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




//------------------------------------------------------------ end=======================


/*

function childFormReset()
{
	reset_form('','','cbo_produced_by*cbo_sewing_line*txt_reporting_hour*txt_supervisor*txt_qc_pass_qty*txt_reject_qnty*txt_alter_qnty*txt_remark*txt_input_quantity*txt_cumul_sewing_qty*txt_yet_to_sewing*hidden_break_down_html*txt_mst_id*txt_spot_qnty','','');
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
	$("#txt_qc_pass_qty").val( $("#total_color").val() );
} 


function fn_colorRej_total(index) //for color level
{
	var filed_value = $("#colSizeRej_"+index).val();
    var totalRow = $("#table_color tbody tr").length;
	//alert(totalRow);
	math_operation( "total_color_rej", "colSizeRej_", "+", totalRow);
	$("#txt_reject_qnty").val( $("#total_color_rej").val() );
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

function check_produced_by(val)
{
	//alert (val)
	var order_no=$('#txt_sample_devlopment_id').val();
	var po_id=$('#hidden_po_break_down_id').val();
	var item_id=$('#cbo_item_name').val();
	var order_qty=$('#txt_order_qty').val();
	if(order_no=="")
	{
		alert ("Order Number is Empty! Plase Browse 1st.");
		$('#cbo_produced_by').val('');
		return;
	}
	else
	{
		var response=return_global_ajax_value( po_id+'**'+item_id, 'piece_rate_order_cheack', '', 'requires/sample_sewing_output_controller');
		var response=response.split("_");
		if(response[0]==1)
		{
			if (response[2]>=order_qty)
			{
				if(val==1)
				{
					alert ("This Order Fully Produced By Piece Rate Worker But Selected Salary Based. Plese Check Piece Rate WO No :-"+response[1]);
					$('#cbo_produced_by').val(2);
				}
			}
			else
			{
				if(val!=0)
				{
					if(val==1) 
					{
						var worker_type='Salary Based Worker.';
					}
					else
					{
						var worker_type='Piece Rate Worker.';
					}
					
					var bal_qty=order_qty-response[2];
					
					var r=confirm("Press \"OK\" You Select "+ worker_type + " \nPress \"Cancel\" Select New Produced by.");
					if (r==true)
					{
						alert ("Total Salary Based Cutting Qty Balance :- "+ bal_qty);
					}
					else
					{
						alert ("Total Salary Based Cutting Qty Balance :- "+ bal_qty);
						if(val==1)
						{
							$('#cbo_produced_by').val(2);
						}
						else
						{
							$('#cbo_produced_by').val(1);
						}
					}
				}
			}
		}
		else if (response[0]==0)
		{
			if(val==2)
			{
				alert ("This order fully produced by salary based worker, but selected piece rate worker");	
				$('#cbo_produced_by').val(1);
			}
		}
	}
}
 */   
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../",$permission);  ?>
	<div style="width:930px; float:left;">
        <fieldset style="width:930px;">
        <legend>Sample Production</legend>  
			<form name="samplesewingoutput_1" id="samplesewingoutput_1" autocomplete="off" >
                <fieldset>
                    <table width="100%" border="0">
                        <tr>
                            <td align="right" width="102" class="must_entry_caption">Company</td>
                            <td width="170">                                
								<? 
                                 echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/sample_sewing_output_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                                ?>
							</td>
                            <td align="right" width="102" class="must_entry_caption">Sample Dev. ID</td>
                            <td width="170">
								<input name="txt_sample_devlopment_id" placeholder="Double Click to Search" id="txt_sample_devlopment_id" onDblClick="openmypage('requires/sample_sewing_output_controller.php?action=sample_popup&company='+document.getElementById('cbo_company_name').value,'Sample Development ID')"  class="text_boxes" style="width:155px " readonly>
								<input type="hidden" id="mst_update_id" readonly />	 

                            </td>
                            <td align="right">Buyer</td>
                            <td>
								<?
                                echo create_drop_down( "cbo_buyer_name", 170, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 );
                                ?>
							</td>
                        </tr>
                        <tr>    
                            <td align="right">Style</td>
                            <td>
                                <input name="txt_style_no" id="txt_style_no" class="text_boxes"  style="width:158px " disabled readonly>
							</td>
                        	 <td align="right"> Item Name</td>
                             <td>
								 <?
                                 echo create_drop_down( "cbo_item_name", 170, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );	
                                 ?>
							 </td>  
                            <td align="right">Sample Qty</td>
                            <td>
                                <input name="txt_sample_qty" id="txt_sample_qty" class="text_boxes"  style="width:158px " disabled readonly>
							</td>
                        </tr>
                        <tr>  
                             <td align="right">Source</td>
                             <td>
								 <?
                                 echo create_drop_down( "cbo_source", 170, $knitting_source,"", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/sample_sewing_output_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_sewing_output', 'sew_company_td' );", 0, '1,3' );
                                 ?>
                             </td>
                         	 <td align="right" class="must_entry_caption">Sewing Company</td>
                             <td id="sew_company_td" >
								 <?
                                 echo create_drop_down( "cbo_sewing_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "" );
                                 ?>
						     </td>
                             <td align="right">Location</td>
                             <td id="location_td">
								 <?
                                 echo create_drop_down( "cbo_location", 168,$blank_array,"", 1, "-- Select Location --", $selected, "" );
                                 ?>
							 </td>
                        </tr>
                        <tr>
                             <td align="right">Floor</td>
                             <td id="floor_td" colspan="5">
								 <? 
								 $floor_library=return_library_array( "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0  and production_process in (5) order by floor_name", "id", "floor_name"  );
								 
								 echo create_drop_down( "cbo_floor", 170, $floor_library,"", 1, "-- Select Floor --", $selected, "" );
                                 ?>
                             </td>
                        </tr>
                    </table>
                </fieldset>
                
                <br>
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                    	<td width="35%" valign="top">
                            <fieldset>
                            <legend>New Entry</legend>
                                <table  cellpadding="0" cellspacing="1" width="100%">
                                     <tr>
                                        <td align="right" class="must_entry_caption">Sample Name</td> 
                                        <td>            
                              			<?
                              			echo create_drop_down( "cbo_sample_name", 112,"select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name","id,sample_name", 1, "Select Sample", $selected, "",1,0 );		
							  			?>	
                              			</td> 
                                   </tr>
                                    <tr>
                                    	<td align="right" class="must_entry_caption">Sewing Date</td>
                                         <td width=""> 
                                         <input name="txt_sewing_date" id="txt_sewing_date" class="datepicker" type="text" value="<? echo date("d-m-Y")?>" style="width:100px;"  onChange="load_drop_down( 'requires/sample_sewing_output_controller',document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+this.value, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );" />
                                        </td>
                                     </tr>
                                     <tr>
                                        <td align="right" class="must_entry_caption">Sewing Line No</td> 
                                        <td id="sewing_line_td">            
                              			<?
										$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
										echo create_drop_down( "cbo_sewing_line", 112, $line_library,"", 1, "Select Line", $selected, "",0,0 );		
							  			?>	
                              			</td> 
                                   </tr>
                                     <tr>
                                       <td align="right" class="must_entry_caption">Reporting Hour</td> 
                                       <td ><input name="txt_reporting_hour" id="txt_reporting_hour" class="text_boxes" style="width:100px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyUp="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyPress="return numOnly(this,event,this.id);" maxlength="8" />
                                       </td>
                                     </tr>
                                   <tr>
                                         <td align="right">Supervisor</td> 
                                         <td width=""> 
                                         <input type="text" name="txt_supervisor" id="txt_supervisor" class="text_boxes"  style="width:100px">
                                         </td>
                                   </tr>
                                   <tr>
                                        <td align="right" class="must_entry_caption">QC Pass Qty</td> 
                                        <td width="" valign="top"><input name="txt_qc_pass_qty" id="txt_qc_pass_qty" class="text_boxes_numeric"  style="width:100px" readonly >
                                            <input type="hidden" id="hidden_colorSizeID"  value=""/>
                                        </td>
                                   </tr>
                                   <tr>
                                     <td align="right">Alter Qty </td>
                                     <td><input type="text" name="txt_alter_qnty" id="txt_alter_qnty" class="text_boxes_numeric" style="width:100px; text-align:right" /></td>
                                   </tr>
                                   <tr>
                                     <td align="right" >Spot Qty </td>
                                     <td><input type="text" name="txt_spot_qnty" id="txt_spot_qnty" class="text_boxes_numeric" style="width:100px; text-align:right" /></td>
                                   </tr>
                                   <tr>
                                     <td align="right">Reject Qty</td>
                                     <td><input type="text" name="txt_reject_qnty" id="txt_reject_qnty" class="text_boxes_numeric" style="width:100px;" readonly /></td>
                                   </tr>
                                    <tr>
                                         <td align="right" class="must_entry_caption">Challan No</td> 
                                         <td>
                                           <input type="text" name="txt_challan" id="txt_challan" class="text_boxes" value="0" style="width:55px" />
                                           Sys. Chln.<input type="text" name="txt_sys_chln" id="txt_sys_chln" class="text_boxes" style="width:45px" placeholder="Display" disabled />
                                         </td>
                                    </tr>
                                   <tr>
                                     <td align="right">Remarks</td>
                                     <td><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:165px;" /></td>
                                   </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td width="1%" valign="top">
                        </td>
                         <td width="22%" valign="top">
                            <fieldset>
                            <legend>Display</legend>
                                <table  cellpadding="0" cellspacing="2" width="100%" >
                                    <tr>
                                        <td align="right" width="110">Cumul. Sew. Qty</td>
                                        <td>
                                            <input type="text" name="txt_cumul_sewing_qty" id="txt_cumul_sewing_qty" class="text_boxes_numeric" style="width:80px" readonly disabled />
                                        </td>
                                    </tr>
                                     <tr>
                                        <td align="right" width="110">Yet to Sewing</td>
                                        <td>
                                            <input type="text" name="txt_yet_to_sewing" id="txt_yet_to_sewing" class="text_boxes_numeric" style="width:80px" / readonly disabled >
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>	
                        </td>
                        <td width="40%" valign="top" >
                            <div style="max-height:350px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                        </td>                         
                     </tr>
                     <tr>
		   				<td align="center" colspan="9" valign="middle" class="button_container">
							<?
							$date=date('d-m-Y');
                            echo load_submit_buttons( $permission, "fnc_sample_sewing_output_entry", 0,1,"reset_form('samplesewingoutput_1','list_view_country','','txt_sewing_date,".$date."*txt_challan,0','childFormReset()')",1); 
                            ?>
                            <input type="hidden" name="dtls_update_id" id="dtls_update_id" readonly />
           				</td>
           				<td>&nbsp;</td>					
		  			</tr>
                </table>
            </form>
        	</fieldset>
            <div style="float:left;"id="list_view_container"></div>

        </div>

		<div id="list_view_country" style="width:370px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>