<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create sewing output
				
Functionality	:	
JS Functions	:
Created by		:   Hakim	
Creation date 	:   27-05-2013	
Updated by 		: 		
Update date		: 
Oracle Convert 	:	Kausar		
Convert date	: 	26-05-2014	   
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
		var company_name=$("#cbo_company_name").val();
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=420px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hidden_mst_id");//po id
			var theemailItem=this.contentDoc.getElementById("hidden_grmtItem_id");
			//var ret_id=theemail.value.split("_");
			var po_id=theemail.value;

			var item_id=theemailItem.value;
			//var order_qty=ret_id[2];
			//var order_no=ret_id[3];
			//alert (po_id)
			if (po_id!="")
			{
				$("#hidden_po_break_down_id").val(po_id);
				//freeze_window(5);
				//childFormReset();
				//$("#txt_order_no").val( order_no );
				$("#cbo_item_name").val(item_id);
				//$("#txt_order_qty").val( order_qty );
				//$("#txt_job_no").val( ret_id[4] );
				//$("#process_id").val( ret_id[5] );
				//$("#cbo_buyer_name").val( ret_id[6] );
				//$("#txt_style_no").val( ret_id[7] );
				
				get_php_form_data(po_id+'**'+item_id+'**'+$('#txt_qty_source').val(), "populate_data_from_search_popup", "requires/poly_entry_controller" );
				var variableSettings=$('#sewing_production_variable').val();
				//alert (variableSettings);
				if(variableSettings!=1)
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+$('#txt_qty_source').val(), "color_and_size_level", "requires/poly_entry_controller" ); 
				else
					$("#txt_sewing_qty").removeAttr("readonly");
				show_list_view(po_id+'**'+item_id+'**'+variableSettings+'**'+$('#txt_qty_source').val(),'show_dtls_listview','sewing_output_dtls_list_view','requires/poly_entry_controller','');			
				set_button_status(0, permission, 'fnc_subcon_sewing_output_entry',1);
				release_freezing();
			}
		}
	}
	
	function fnc_subcon_sewing_output_entry(operation)
	{
		if( form_validation('cbo_company_name*txt_order_no*txt_poly_date*txt_sewing_qty','Company Name*Order Number*Sewing Date*Sewing Quntity')==false )
		{
			return;
		}
		var sewing_production_variable = $("#sewing_production_variable").val();
		var item_id = $("#cbo_item_name").val();
		var po_id = $("#hidden_po_break_down_id").val();
		
		var total_row = $("#txt_total_row_count").val();
		var tot_span = $("#txt_span_count").val();
		var tot_row =$("#table_"+j+" tr").length;
		
		if(sewing_production_variable==2)//color level
		{
			var total_row = $("#txt_total_row_count").val();
			var data1="";
			var i=1;
			for(i=1; i<=total_row; i++)
			{
				data1+=get_submitted_data_string('txt_colo_size_mst_id_'+i+'*colSize_'+i,"../");
			}
		}
		else if(sewing_production_variable==3)//color and size level
		{
			var tot_span = $("#txt_span_count").val();
			var data2="";var k=1;var z=1;var j=1;var tot_row_count="";
			for(k=1; k<=tot_span; k++)
			{
				var tot_row =$("#table_"+j+" tr").length;
				for(i=1; i<=tot_row; i++)
				{
					data2+=get_submitted_data_string('txt_colo_size_mst_id'+k+'___'+i+'*colSize'+k+'___'+i,"../");
				}
				if(tot_row_count=="")tot_row_count=tot_row; else tot_row_count=tot_row_count+","+tot_row;
				j++;
			}
		}
		freeze_window(operation);	
	
		var data="action=save_update_delete&operation="+operation+"&tot_row="+tot_row_count+"&tot_span="+tot_span+"&total_row="+total_row+get_submitted_data_string('cbo_company_name*txt_order_no*cbo_buyer_name*txt_job_no*txt_style_no*cbo_item_name*txt_order_qty*cbo_location*cbo_floor*txt_poly_date*cbo_poly_line*txt_hours*txt_sewing_qty*txt_reject_qnty*prod_reso_allo*txt_alter_qnty*txt_super_visor*txt_remark*txt_cumul_poly_qty*txt_yet_to_poly*sewing_production_variable*hidden_po_break_down_id*process_id*hidden_colorSizeID*txt_mst_id*txt_spot_qnty*txt_challan',"../");
	 
		var sewing_production_variable = $("#sewing_production_variable").val();
		if(sewing_production_variable==2)//color level
		{
			data=data+data1;
		}
		else if(sewing_production_variable==3)//color and size level
		{
			data=data+data2;
		}
		//alert (data);
		http.open("POST","requires/poly_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_subcon_sewing_output_entry_respone;
	}
	
	function fnc_subcon_sewing_output_entry_respone()
	{
		if(http.readyState == 4)
		{
			//alert (http.responseText); //return;
			var reponse=trim(http.responseText).split('**');
			show_msg(reponse[0]);
			var po_id = $("#hidden_po_break_down_id").val();
			var variableSettings=$('#sewing_production_variable').val();
			var item_id = $("#cbo_item_name").val();
			show_list_view(po_id+'**'+item_id+'**'+variableSettings+'**'+$('#txt_qty_source').val(),'show_dtls_listview','sewing_output_dtls_list_view','requires/poly_entry_controller','');		
			reset_form('','','cbo_poly_line*txt_hours*txt_sewing_qty*txt_reject_qnty*txt_spot_qnty*txt_alter_qnty*txt_cumul_poly_qty*txt_yet_to_poly*txt_super_visor*txt_challan*txt_remark*hidden_break_down_html','txt_poly_date,<? echo date("d-m-Y"); ?>',''); 	
			get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+$('#txt_qty_source').val(), "populate_data_from_search_popup", "requires/poly_entry_controller" );
			if(variableSettings!=1)
			{ 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+$('#txt_qty_source').val(), "color_and_size_level", "requires/poly_entry_controller" ); 
			}
			else
			{
				$("#txt_sewing_qty").removeAttr("readonly");
			}
			set_button_status(0, permission, 'fnc_subcon_sewing_output_entry',1);
			release_freezing();
		}
	}
	
	function fn_colorlevel_total(index) //for color level
	{
		var tot_row = $("#txt_total_row_count").val();
		var total_qnty="";
		for(var i=1; i<=tot_row; i++)
		{
			total_qnty=total_qnty*1+$("#colSize_"+i).val()*1;
		}
		document.getElementById('txt_sewing_qty').value=total_qnty;
		document.getElementById('total_color').value=total_qnty;
	}
	
	function sum_qnty(id,vid)
	{
		var filed_value = $("#colSize"+id+"___"+vid).val();
		var placeholder_value = $("#colSize"+id+"___"+vid).attr('placeholder');
		//alert (placeholder_value)
		if(filed_value*1 > placeholder_value*1)
		{
			if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )	
				void(0);
			else
			{
				$("#colSize"+id+"___"+vid).val('');
			}
		}
		
		var totalRow = $("#table_"+id+" tr").length;
		var total=0;
		for(var i=1; i<=totalRow; i++)
		{
			total=(total*1)+($("#colSize"+id+"___"+i).val()*1);
		}
		$("#total_"+id).html(total);
		var totalVal = 0;
		$("input[name=colSize]").each(function(index, element) {
			totalVal += ( $(this).val() )*1;
		});
		$("#txt_sewing_qty").val(totalVal);
	}

	function childFormReset()
	{
		reset_form('','','cbo_poly_line*txt_hours*txt_sewing_qty*txt_reject_qnty*txt_alter_qnty*txt_cumul_poly_qty*txt_yet_to_poly*txt_super_visor*txt_challan*txt_remark','txt_poly_date,<? echo date("d-m-Y"); ?>',''); 	
		$('#txt_cumul_poly_qty').attr('placeholder','');//placeholder value initilize
		$('#txt_yet_to_poly').attr('placeholder','');//placeholder value initilize
		$('#sewing_output_dtls_list_view').html('');//listview container
		$("#breakdown_td_id").html('');
	}  

	function fnc_move_cursor(val,id,field_id,lnth,max_val)
	{
		var str_length=val.length;
		if(str_length==lnth)
		{
			$('#'+field_id).select();
			$('#'+field_id).focus();
		}
		if(val>max_val)
		{
			document.getElementById(id).value=max_val;
		}
	}
	
	function fn_hour_check(val)
	{
		if(val*1>12)
		{
			alert("You Cross 12!!This is 12 Hours.");
			$("#txt_hours").val('');
		}
	}
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
  
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",$permission);  ?>
        <fieldset style="width:750px;">
        <legend>Production Module</legend>  
			<form name="sewingoutput_1" id="sewingoutput_1" autocomplete="off" >
                <fieldset>
                    <table width="100%" border="0">
                        <tr>
                            <td width="100" class="must_entry_caption">Company</td>
                            <td width="140" >                                
								<? 
									 echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/poly_entry_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value,'load_variable_settings','requires/poly_entry_controller');" );
                                ?>
                                <input type="hidden" id="sewing_production_variable" />
								<input type="hidden" id="styleOrOrderWisw" /> 
                                <input type="hidden" id="prod_reso_allo" /> 
                                <input type="hidden" id="variable_is_controll" />
                                <input type="hidden" id="txt_qty_source" />
							</td>
                            <td width="100" class="must_entry_caption">Order No</td>
                            <td width="140">
								<input name="txt_order_no" placeholder="Double Click to Search" id="txt_order_no" onDblClick="openmypage('requires/poly_entry_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Order Search')"  class="text_boxes" style="width:130px " readonly>
                                <input type="hidden" id="hidden_po_break_down_id" value="" style="width:100px " />	
                                <input type="hidden" id="hidden_order_qnty" value="" />
                                <input type="hidden" id="process_id" value="" />
							</td>
                            <td width="100">Buyer</td>
                            <td width="140">
								<?
                                    echo create_drop_down( "cbo_buyer_name", 140, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 );
                                ?>
							</td>
                        </tr>
                        <tr>    
                            <td>Job No</td>
                            <td>
                            	<input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:130px " disabled readonly>	
							</td>
                            <td>Style</td>
                            <td>
								<input name="txt_style_no" id="txt_style_no" class="text_boxes"  style="width:130px " disabled readonly>
							</td>
                            <td> Item </td>
                             <td>
								 <?
                                 	echo create_drop_down( "cbo_item_name", 140, $garments_item,"", 1, "-- Select Item --", $selected, "",0,0 );	
                                 ?>
							 </td> 
                        </tr>
                        <tr>  
                             <td>Order Qty</td>
                             <td>
								 <input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric" style="width:130px " disabled readonly>
							 </td>
                             <td>Location</td>
                             <td id="location_td">
								 <?
                                 	echo create_drop_down( "cbo_location", 140,$blank_array,"", 1, "-- Select Location --", $selected, "" );
                                 ?>
							 </td>  
                             <td>Floor</td>
                             <td id="floor_td">
								  <? 
                                 	 echo create_drop_down( "cbo_floor", 140, $blank_array,"", 1, "-- Select Floor --", $selected, "" );
                                  ?>
                             </td>                          
                        </tr>                        
                    </table>
                </fieldset>
                <table cellpadding="0" cellspacing="1" width="100%">
                	<tr><td colspan="6" height="5">&nbsp;</td></tr>
                    <tr>
                        <td width="35%" valign="top">
                                <fieldset>
                                <legend>New Entry</legend>
                                    <table cellpadding="0" cellspacing="1" width="100%">
                                        <tr>
                                            <td width="110" class="must_entry_caption">Poly Date</td>
                                            <td width="110"> 
                                                <input name="txt_poly_date" id="txt_poly_date" class="datepicker"  type="text" value="<? echo date("d-m-Y")?>" style="width:100px;" onChange="load_drop_down( 'requires/poly_entry_controller',document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+this.value, 'load_drop_down_sewing_line_floor', 'poly_line_td' );" />

                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Poly Line No</td>
                                            <td id="poly_line_td">            
												<?
													echo create_drop_down( "cbo_poly_line", 110, $blank_array,"", 1, "Select Line", $selected, "",0,0 );		
                                                ?>	
                                            </td> 
                                        </tr>
                                        <tr>
                                            <td align="left">Reporting Hour</td> 
                                            <td >
                                               
                                                  <input name="txt_hours" id="txt_hours" class="text_boxes" style="width:100px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,'txt_hours');" onKeyUp="fnc_valid_time(this.value,'txt_hours');" onKeyPress="return numOnly(this,event,this.id);" maxlength="8" />
												
                                          </td>
                                        </tr>
                                        <tr>
                                            <td class="must_entry_caption">QC Pass Qty</td> 
                                            <td>
                                                <input name="txt_sewing_qty" id="txt_sewing_qty" class="text_boxes_numeric"  style="width:100px" readonly >
                                                <input type="hidden" id="hidden_break_down_html"  value="" />
                                                <input type="hidden" id="hidden_colorSizeID"  value="" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Alter Qty </td>
                                            <td>
                                                <input type="text" name="txt_alter_qnty" id="txt_alter_qnty" class="text_boxes_numeric" style="width:100px; text-align:right" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td >Spot Qty </td>
                                            <td><input type="text" name="txt_spot_qnty" id="txt_spot_qnty" class="text_boxes_numeric" style="width:100px; text-align:right" /></td>
                                        </tr>
                                        <tr>
                                            <td>Reject Qty</td>
                                            <td>
                                                <input type="text" name="txt_reject_qnty" id="txt_reject_qnty" class="text_boxes_numeric" style="width:100px; text-align:right" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Supervisor</td> 
                                            <td> 
                                                <input type="text" name="txt_super_visor" id="txt_super_visor" class="text_boxes"  style="width:100px">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Challan No</td> 
                                            <td>
                                                <input type="text" name="txt_challan" id="txt_challan" class="text_boxes" style="width:100px" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Remarks</td>
                                            <td>
                                                <input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:150px;" />
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>
                        </td>
                        <td width="1%" valign="top"></td>
                        <td width="25%" valign="top">
                            <fieldset>
                            <legend>Display</legend>
                                <table  cellpadding="0" cellspacing="2" width="100%" >
                                    <tr>
                                        <td width="120">Cumul. Poly Qty</td>
                                        <td>
                                            <input type="text" name="txt_cumul_poly_qty" id="txt_cumul_poly_qty" class="text_boxes_numeric" style="width:70px" disabled />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="120">Yet to Poly</td>
                                        <td>
                                            <input type="text" name="txt_yet_to_poly" id="txt_yet_to_poly" class="text_boxes_numeric" style="width:70px" readonly disabled />
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>	
                        </td>
                        <td width="38%" valign="top" >
                            <div style="max-height:350px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                        </td>                         
                     </tr>
                     <tr>
		   				<td align="center" colspan="9" valign="middle" class="button_container">
							<?
								$date=date('d-m-Y');
								echo load_submit_buttons( $permission, "fnc_subcon_sewing_output_entry", 0,0 ,"reset_form('sewingoutput_1','','','txt_poly_date,".$date."','childFormReset()');$('#txt_sewing_qty').attr('placeholder','');",1); 
                            ?>
                            <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly />
           				</td>
           				<td>&nbsp;</td>					
		  			</tr>
                </table>
            </form>
        </fieldset>
     <div style="width:800px; margin-top:5px;" id="sewing_output_dtls_list_view" align="center"></div>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>