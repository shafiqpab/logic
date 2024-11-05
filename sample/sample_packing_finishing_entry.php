<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Packing And Finishing
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	23-09-2023
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
echo load_html_head_contents("Packing N Finishing","../", 1, 1, $unicode,'','');

?>	

<script>
	var permission='<? echo $permission; ?>';
	var field_message="";
	var mandatory_field=""; 
	<?
	if(isset($_SESSION['logic_erp']['mandatory_field'][629]))
	{
		echo " mandatory_field = '". implode('*',$_SESSION['logic_erp']['mandatory_field'][629]) . "';\n";
		echo " field_message = '". implode('*',$_SESSION['logic_erp']['field_message'][629]) . "';\n";
	}
		?>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
 

	function openmypage(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var smp_id=this.contentDoc.getElementById("selected_id").value;//requisition id
					
			if (smp_id!="")
			{
 				$('#breakdown_td_id').html('');
				$("#txt_sample_requisition_id").val(smp_id);
				get_php_form_data(smp_id, "populate_data_from_search_popup", "requires/sample_packing_finishing_entry_controller" );
				
				show_list_view(smp_id,'show_sample_item_listview','list_view_country','requires/sample_packing_finishing_entry_controller','');		
				
				show_list_view(smp_id,'show_dtls_listview','list_view_container','requires/sample_packing_finishing_entry_controller','');
  				
				set_button_status(0, permission, 'fnc_sample_finishing_entry',1,0);
				$("#cbo_company_name").attr("disabled",true);
				
				release_freezing();
			}
		}
	} 
	
	function put_sample_item_data(sample_dtls_part_tbl_id,smp_id,gmts)
	{
		var req_id=$("#hidden_requisition_id").val();
		//alert(mst_id+' '+smp_id+' '+gmts+' '+req_id);return;
		//freeze_window(5);
		get_php_form_data(sample_dtls_part_tbl_id+'**'+smp_id+'**'+req_id+'**'+gmts, "color_and_size_level", "requires/sample_packing_finishing_entry_controller" ); 
		set_button_status(0, permission, 'fnc_sample_finishing_entry',1,0);
		//release_freezing();
	}
 
	function fn_total(tableName,index) // for color and size level
	{
		var colSizeRej= $("#colSizeRej_"+tableName+index).val()*1;
		var txt_cumul_previ_qty=$("#txt_cumul_previ_qty").val()*1;
		// var previ_rej_qty = $("#txt_cumul_rej_qty").val()*1;
		//alert(txt_cumul_previ_qty+'='+txt_cumul_previ_qty);
		//var colorSizeinput = $("#colorSizeinput_"+tableName+index).val()*1;
		var colorSizeinput = $("#txt_total_cutting_qty").val()*1;
		
		var previ_rej_qty = $("#txt_cumul_rej_qty").val()*1;
		var cumul_sewing_qty=$("#txt_cumul_finishing_qty").val()*1;
		var filed_value = $("#colSizeQty_"+tableName+index).val()*1+colSizeRej+txt_cumul_previ_qty+previ_rej_qty;
		var placeholder_value = $("#colSizeQty_"+tableName+index).attr('placeholder');
		
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
		$("#txt_finishing_qty").val(totalVal);
		var totalValRej=$("#txt_reject_qnty").val();
		if(filed_value*1 > colorSizeinput*1)
		{
			if( confirm("Qnty Excceded by "+(colorSizeinput-filed_value)) )	
			{
				$("#txt_finishing_qty").val('');
				$("#colSizeQty_"+tableName+index).val('');
				$("#colSizeRej_"+tableName+index).val('');
				$("#txt_reject_qnty").val(totalValRej-colSizeRej);
			}
			else
			{
				$("#colSizeQty_"+tableName+index).val('');
				$("#colSizeRej_"+tableName+index).val('');
				$("#txt_reject_qnty").val('');
				$("#txt_finishing_qty").val('');
			}
		}
	}
	
	function fn_total_rej(tableName,index) // for color and size level
	{
		var filed_value = $("#colSizeRej_"+tableName+index).val()*1;
		var cumul_sewing_qty=$("#txt_cumul_finishing_qty").val()*1;
		//var dtls_update_id=$("#dtls_update_id").val();
		var cumul_previ_qty=$("#txt_cumul_previ_qty").val()*1;
		var previ_rej_qty = $("#txt_cumul_rej_qty").val()*1;
		//alert(txt_cumul_previ_qty+'='+txt_cumul_previ_qty);
		var colSizeQty = $("#colSizeQty_"+tableName+index).val()*1;
		//var colorSizeinput = $("#colorSizeinput_"+tableName+index).val()*1;
		 var colorSizeinput = $("#txt_total_cutting_qty").val()*1;
		var placeholder_value = $("#colSizeQty_"+tableName+index).attr('placeholder');
		var totalRow = $("#table_"+tableName+" tr").length;
		//if(dtls_update_id!="") { previ_rej_qty=0;}
		
		var total_qty=colSizeQty+filed_value+previ_rej_qty+cumul_previ_qty;
		math_operation( "total_"+tableName, "colSizeRej_"+tableName, "+", totalRow);
		var totalValRej = 0;
		$("input[name=colorSizeRej]").each(function(index, element) {
			//alert(total_qty+'='+colorSizeinput);
			if(total_qty>colorSizeinput)
			{
				//$("#colSizeRej_"+tableName+index).val('');
				 $(this).val('');
			}
			totalValRej += ( $(this).val() )*1;
		});
		$("#txt_reject_qnty").val(totalValRej);
	}

	function fnc_sample_finishing_entry(operation)
	{
		freeze_window(operation);
		if(operation==0 && $('#txt_total_cutting_qty').val()==0)
		{
			alert("Total cutting value zero");
			release_freezing();
			return;
		}
		if(operation==4)
		{
			var check_print_ids="";
			var i=1;
			//var check_id=$("#sample_detail_tbl tr").length();
			$("#sample_detail_tbl tr").each(function(){
				//alert($("#check_for_print_"+i).is(":checked"));
				if($("#check_for_print_"+i).is(":checked")){
					if(check_print_ids=="") check_print_ids  = $("#check_for_print_"+i).val() ;
					else
					check_print_ids += ','+$("#check_for_print_"+i).val();				
				}
				i++;
			});
			if(check_print_ids==""){
				alert("Select at least one checkbox"); 
				$("#check_for_print_"+i).focus();
				release_freezing();
				return;
			}else{
				print_report( $('#cbo_company_name').val()+'*'+$('#mst_update_id').val()+'*'+$("#cbo_sample_name").val()+'*'+$("#cbo_item_name").val()+'*'+check_print_ids+'*'+$("#hidden_requisition_id").val()+'*'+$("#hidden_sample_dtls_tbl_id").val(), "packing_finishing_print", "requires/sample_packing_finishing_entry_controller" ) 
				release_freezing();
			 	return;
			}
		}
		else if(operation==0 || operation==1 || operation==2)
		{	
			if(operation==2){
				
				var r=confirm("Press OK to Delete Or Press Cancel");
				if(r==false){
					release_freezing();
					return;
				}
			}
			
			if (form_validation('cbo_company_name*txt_sample_requisition_id*cbo_finishing_company*txt_finishing_date*cbo_sample_name*cbo_sample_team*txt_finishing_qty','Company Name*Sample Requisition ID*Finishing Company*Finishing Date*Sample Name*Sample Team*Finishing Qty')==false)
			{
				release_freezing();
				return;
			}		
			else
			{
				var colorList = ($('#hidden_colorSizeID').val()).split(",");
				//alert(colorList);return;
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
	
				if(mandatory_field){
					if(form_validation(mandatory_field,field_message)==false)
				   	{
					   	release_freezing();
						return;
				  	}
			  	}
				
				var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+"&colorIDvalueRej="+colorIDvalueRej+get_submitted_data_string('mst_update_id*dtls_update_id*cbo_company_name*txt_sample_requisition_id*cbo_buyer_name*txt_style_no*cbo_item_name*txt_sample_qty*cbo_source*cbo_finishing_company*cbo_location*cbo_floor*cbo_sample_name*txt_finishing_date*txt_reporting_hour*txt_finishing_qty*txt_reject_qnty*txt_remark*hidden_sample_dtls_tbl_id*hidden_requisition_id*cbo_sample_team*txt_total_cutting_qty',"../");
				// alert(data);return;
				
				http.open("POST","requires/sample_packing_finishing_entry_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_sample_finishing_entry_Reply_info; 
			}
		}
	}
  
	function fnc_sample_finishing_entry_Reply_info()
	{
		if(http.readyState == 4) 
		{
			var response=http.responseText.split('**');		 
			
			if(response[0]==0 || response[0]==1)
			{
				show_msg(trim(response[0]));
				show_list_view(response[2],'show_dtls_listview','list_view_container','requires/sample_packing_finishing_entry_controller','');
				var val =return_global_ajax_value( response[5]+"__"+response[1]+"__"+$('#cbo_sample_name').val()+"__"+$('#cbo_item_name').val(), 'populate_data_yet_to_cut', '', 'requires/sample_packing_finishing_entry_controller');
				var prod_qty=$("#txt_sample_qty").val();
				var total_cut=$("#txt_total_cutting_qty").val();
				$("#txt_cumul_finishing_qty").val(val);
				$("#txt_yet_to_finishing").val(total_cut*1 - val*1);
				childFormReset();
				set_button_status(0, permission, 'fnc_sample_finishing_entry',1,0);
				$("#txt_finishing_date").datepicker().datepicker("setDate", new Date());
	
			}
			else if(response[0]==2)//delete reponse;
			{
				show_msg(trim(response[0]));
				show_list_view(response[2],'show_dtls_listview','list_view_container','requires/sample_packing_finishing_entry_controller','');
				if(response[6]==1)
				{
					childFormReset(1);
				}
				set_button_status(0, permission, 'fnc_sample_finishing_entry',1,0);
			}
			release_freezing();
		}
	} 

	function childFormReset(type)
	{	if(type==1)
		{
			reset_form('samplepackingandfinishing_1','','','','');
		}
		else {
			reset_form('','','txt_finishing_date*txt_reporting_hour*txt_finishing_qty*txt_reject_qnty*txt_remark','','');
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
			
			if(hour>23) hour=23;
			
			if(str_length>=2)
			{
				minutes= minutes.substr(0, 2);
				if(minutes*1>59) minutes=59;
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
<body onLoad="set_hotkey();">
<div style="width:100%;">
	<? echo load_freeze_divs("../",$permission); ?>
	<div style="width:930px; float:left;">
        <fieldset style="width:930px;">
        <legend>Packing And Finishing</legend>  
			<form name="samplepackingandfinishing_1" id="samplepackingandfinishing_1" autocomplete="off" >
                <fieldset>
                    <table width="100%" border="0">
                        <tr>
                            <td width="100" class="must_entry_caption">Company</td>
                            <td width="170"><?=create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-Select Company-", $selected, "load_drop_down( 'requires/sample_packing_finishing_entry_controller', this.value, 'load_drop_down_location', 'location_td' );" ); ?></td>
                            <td width="100">Source</td>
                            <td width="170"><?=create_drop_down( "cbo_source", 150, $knitting_source,"", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/sample_packing_finishing_entry_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_finishing', 'sew_company_td' );", 0, '1,3' ); ?></td>
                            <td width="100" class="must_entry_caption">Finishing Company</td>
                            <td id="sew_company_td"><?=create_drop_down( "cbo_finishing_company", 150, $blank_array,"", 1, "-Fin. Company-", $selected, "" ); ?></td>
                        </tr>
                        <tr>  
                            <td class="must_entry_caption">Sample Req. No</td>
                            <td><input name="txt_sample_requisition_id" placeholder="Double Click to Search" id="txt_sample_requisition_id" onDblClick="openmypage('requires/sample_packing_finishing_entry_controller.php?action=sample_requisition_popup&company='+document.getElementById('cbo_company_name').value,'Sample Requisition ID');"  class="text_boxes" style="width:140px " readonly>
                                <input type="hidden" id="mst_update_id" />	 
                                <input type="hidden" id="hidden_requisition_id" />	 
                            </td>
                            <td>Buyer</td>
                            <td><?=create_drop_down( "cbo_buyer_name", 150, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 ); ?></td>  
                            <td>Style</td>
                            <td><input name="txt_style_no" id="txt_style_no" class="text_boxes"  style="width:140px " disabled readonly></td>
                        </tr>
                        <tr> 
                            <td>Prod Qty</td>
                            <td><input name="txt_sample_qty" id="txt_sample_qty" class="text_boxes"  style="width:140px " disabled readonly></td>
                            <td>Location</td>
                            <td id="location_td"><?=create_drop_down( "cbo_location", 150,$blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
                            <td>Floor</td>
                            <td id="floor_td">
								<? 
                                $floor_library=return_library_array( "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0  and production_process in (5) order by floor_name", "id", "floor_name");
                                echo create_drop_down( "cbo_floor", 150, $floor_library,"", 1, "-- Select Floor --", $selected, "" );
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
                            <table cellpadding="0" cellspacing="1" width="100%">
                                <tr>
                                    <td class="must_entry_caption">Sample Name</td> 
                                    <td><?=create_drop_down( "cbo_sample_name", 150,"select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name","id,sample_name", 1, "Select Sample", $selected, "",1,0 );	?></td> 
                                    <input type="hidden" name="hidden_sample_dtls_tbl_id" id="hidden_sample_dtls_tbl_id" value="">
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Item Name</td>
                                    <td>
                                    <?
                                    $item_arrs="select id,item_name from lib_garment_item where status_active=1 and is_deleted=0";
                                    echo create_drop_down( "cbo_item_name", 150, $item_arrs,"id,item_name", 1, "-- Select Item --", $selected, "",1,0 );	
                                    ?>
                                    </td>  
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Finishing Date</td>
                                    <td><input name="txt_finishing_date" id="txt_finishing_date" class="datepicker" type="text" value="<? echo date("d-m-Y")?>" style="width:138px;" onChange="load_drop_down( 'requires/sample_packing_finishing_entry_controller',document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+this.value, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );" /></td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Sample Team</td> 
                                    <td id="sample_team_td"> <?=create_drop_down( "cbo_sample_team", 150,"select id,team_name from lib_sample_production_team where is_deleted=0 and status_active=1 order by team_name","id,team_name", 1, "--Select Team--", $selected ); ?>	
                                    </td> 
                                    <input type="hidden" name="hidden_sample_dtls_tbl_id" id="hidden_sample_dtls_tbl_id" value="">
                                </tr>
                                <tr>
                                    <td>Reporting Hour</td> 
                                    <td><input name="txt_reporting_hour" id="txt_reporting_hour" class="text_boxes" style="width:138px" placeholder="24 Hour Format" maxlength="8" onBlur="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyUp="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyPress="return numOnly(this,event,this.id);" /></td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Finishing Qty</td> 
                                    <td><input name="txt_finishing_qty" id="txt_finishing_qty" class="text_boxes_numeric"  style="width:138px" readonly ><input type="hidden" id="hidden_colorSizeID"  value=""/></td>
                                </tr>
                                <tr>
                                    <td>Reject Qty</td>
                                    <td><input type="text" name="txt_reject_qnty" id="txt_reject_qnty" class="text_boxes_numeric" style="width:138px;" readonly /></td>
                                </tr>
                                <tr>
                                    <td>Remarks</td>
                                    <td><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:138px;" /></td>
                                </tr>
                            </table>
                        </fieldset>
                        </td>
                        <td width="1%" valign="top">&nbsp;</td>
                        <td width="22%" valign="top">
                            <fieldset>
                            <legend>Display</legend>
                                <table cellpadding="0" cellspacing="2" width="100%" >
                                    <tr>
                                        <td width="110" id="dynamic_cut_qty">Tot Finishing Qty</td>
                                        <td><input type="text" name="txt_total_cutting_qty" id="txt_total_cutting_qty" class="text_boxes_numeric" style="width:80px" readonly disabled /></td>
                                    </tr>
                                    <tr>
                                        <td>Cumul. Fin. Qty</td>
                                        <td>
                                            <input type="text" name="txt_cumul_finishing_qty" id="txt_cumul_finishing_qty" class="text_boxes_numeric" style="width:80px" readonly disabled /><input type="hidden" name="txt_cumul_rej_qty" id="txt_cumul_rej_qty" class="text_boxes_numeric" style="width:80px" readonly disabled />
                                            <input type="hidden" name="txt_cumul_previ_qty" id="txt_cumul_previ_qty" class="text_boxes_numeric" style="width:80px" readonly disabled />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Yet to Finishing</td>
                                        <td><input type="text" name="txt_yet_to_finishing" id="txt_yet_to_finishing" class="text_boxes_numeric" style="width:80px" / readonly disabled ></td>
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
                            echo load_submit_buttons( $permission, "fnc_sample_finishing_entry", 0,0,"reset_form('samplepackingandfinishing_1', 'list_view_country', '', 'txt_finishing_date, ".$date."*txt_challan,0','childFormReset()')",1); 
                            ?>
                            <input type="button" class="formbutton" name="btn_ids" id="btn_ids" value="Print" onClick="fnc_sample_finishing_entry(4)"  style="width:80px"/>
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