<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sample Sewing Input
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	18-08-2019
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
echo load_html_head_contents("Sample Sewing Input","../", 1, 1, $unicode,'','');
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

function openmypage(page_link,title)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=940px,height=370px,center=1,resize=0,scrolling=0','')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var smp_id=this.contentDoc.getElementById("selected_id").value;//requisition id
				
		if (smp_id!="")
		{
			$("#txt_sample_requisition_id").val(smp_id);
			get_php_form_data(smp_id, "populate_data_from_search_popup", "requires/sample_sewing_input_controller" );
			
			show_list_view(smp_id,'show_sample_item_listview','list_view_country','requires/sample_sewing_input_controller','');		
			
			show_list_view(smp_id,'show_dtls_listview','list_view_container','requires/sample_sewing_input_controller','');
			
			set_button_status(0, permission, 'fnc_sample_sewing_entry',1,0);
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
 	get_php_form_data(sample_dtls_part_tbl_id+'**'+smp_id+'**'+req_id+'**'+gmts, "color_and_size_level", "requires/sample_sewing_input_controller" ); 
    set_button_status(0, permission, 'fnc_sample_sewing_entry',1,0);
	//release_freezing();
}
 
function fn_total(tableName,index) // for color and size level
{
    var filed_value = $("#colSizeQty_"+tableName+index).val();
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
	$("#txt_sewing_qty").val(totalVal);
	if(filed_value*1 > placeholder_value*1)
	{
		if( confirm("Qnty Excceded by "+(placeholder_value-filed_value)) )	
		{
			$("#txt_sewing_qty").val('');
			$("#colSizeQty_"+tableName+index).val('');
		}
		else
		{
			$("#colSizeQty_"+tableName+index).val('');
			$("#txt_sewing_qty").val('');
 		}
	}
}

function fnc_sample_sewing_entry(operation)
{
	if(operation==0 && $('#txt_total_cutting_qty').val()==0)
	{
		alert("Total cutting value zero");
	}
	if(operation==0 || operation==1 || operation==2)
	{	
 		if (form_validation('cbo_company_name*txt_sample_requisition_id*cbo_sewing_company*txt_sewing_date*cbo_sample_name*cbo_sample_team*txt_sewing_qty','Company Name*Sample Requisition ID*Sewing Company*Sewing Date*Sample Name*Sample Team*Sewing Quantity')==false)
		{
			return;
		}		
		else
		{
 			var colorList = ($('#hidden_colorSizeID').val()).split(",");
 			//alert(colorList);return;
 			var i=0;  var k=0; var colorIDvalue='';
			
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
			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('mst_update_id*dtls_update_id*cbo_company_name*txt_sample_requisition_id*cbo_buyer_name*txt_style_no*cbo_item_name*txt_sample_qty*cbo_source*cbo_sewing_company*cbo_location*cbo_floor*cbo_sample_name*txt_sewing_date*txt_sewing_qty*txt_remark*hidden_sample_dtls_tbl_id*hidden_requisition_id*cbo_sample_team*txt_total_cutting_qty',"../");
			// alert(data);return;
			freeze_window(operation);
  			http.open("POST","requires/sample_sewing_input_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_sample_sewing_entry_Reply_info; 
		}
	}
}
  
function fnc_sample_sewing_entry_Reply_info()
{
 	if(http.readyState == 4) 
	{
		var response=http.responseText.split('**');		 
		
		if(response[0]==0 || response[0]==1)//insert update response;
		{
			show_msg(trim(response[0])); 
			show_list_view(response[2],'show_dtls_listview','list_view_container','requires/sample_sewing_input_controller','');
			$('#mst_update_id').val(response[1]);
  			$('#breakdown_td_id').html('');
  			var val =return_global_ajax_value( response[5]+"__"+response[1]+"__"+$('#cbo_sample_name').val()+"__"+$('#cbo_item_name').val(), 'populate_data_yet_to_cut', '', 'requires/sample_sewing_input_controller');
  			var prod_qty=$("#txt_sample_qty").val();
  			var total_cut=$("#txt_total_cutting_qty").val();
  			$("#txt_cumul_sewing_qty").val(val);
  			$("#txt_yet_to_sewing").val(total_cut*1 - val*1);
   			childFormReset();
			set_button_status(0, permission, 'fnc_sample_sewing_entry',1,0);
   			$("#txt_sewing_date").datepicker().datepicker("setDate", new Date());
		}
		else if(response[0]==2)//delete reponse;
		{
			show_msg(trim(response[0]));
			set_button_status(0, permission, 'fnc_sample_sewing_entry',1,0);
		}
		release_freezing();
 	}
} 

function childFormReset()
{
	reset_form('','','txt_sewing_date*txt_sewing_qty*txt_remark','','');
}
 
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../",$permission);  ?>
	<div style="width:930px; float:left;">
        <fieldset style="width:930px;">
        <legend>Sample Sewing Input</legend>  
			<form name="samplesewinginput_1" id="samplesewinginput_1" autocomplete="off" >
                <fieldset>
                    <table width="100%" border="0">
                        <tr>
                            <td width="110" class="must_entry_caption">Company</td>
                            <td width="160"><? echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/sample_sewing_input_controller', this.value, 'load_drop_down_location', 'location_td' );" ); ?></td>
							 <td width="110">Source</td>
                             <td width="160"><? echo create_drop_down( "cbo_source", 150, $knitting_source,"", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/sample_sewing_input_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_sewing_output', 'sew_company_td' );", 0, '1,3' ); ?></td>
                         	 <td width="110" class="must_entry_caption">Sewing Company</td>
                             <td id="sew_company_td"><?  echo create_drop_down( "cbo_sewing_company", 150, $blank_array,"", 1, "--- Select ---", $selected, "" ); ?></td>
                        </tr>
                        <tr>  
                        	<td class="must_entry_caption">Sample Req. No</td>
                            <td>
								<input name="txt_sample_requisition_id" placeholder="Double Click to Search" id="txt_sample_requisition_id" onDblClick="openmypage('requires/sample_sewing_input_controller.php?action=sample_requisition_popup&company='+document.getElementById('cbo_company_name').value,'Sample Requisition ID')"  class="text_boxes" style="width:140px " readonly>
								<input type="hidden" id="mst_update_id" />	 
								<input type="hidden" id="hidden_requisition_id" />	 
                            </td>
                            <td>Buyer</td>
                            <td><? echo create_drop_down( "cbo_buyer_name", 150, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 ); ?></td>  
                            <td>Style</td>
                            <td><input name="txt_style_no" id="txt_style_no" class="text_boxes"  style="width:140px " disabled readonly></td>
                        </tr>
                        <tr> 
                            <td>Prod Qty</td>
                            <td><input name="txt_sample_qty" id="txt_sample_qty" class="text_boxes"  style="width:140px" disabled readonly></td>
                            <td>Location</td>
                            <td id="location_td"><? echo create_drop_down( "cbo_location", 150,$blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
							<td>Floor</td>
                            <td id="floor_td"><? $floor_library=return_library_array( "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0  and production_process in (5) order by floor_name", "id", "floor_name"  );
								 echo create_drop_down( "cbo_floor", 150, $floor_library,"", 1, "-- Select Floor --", $selected, "" ); ?>
                            </td> 
                        </tr>
                    </table>
                </fieldset>
                <br>
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                    	<td width="31%" valign="top">
                            <fieldset>
                            	<legend>New Entry</legend>
                                <table cellpadding="0" cellspacing="1" width="100%">
                                    <tr>
                                        <td width="130" class="must_entry_caption">Sample Name
                                        	<input type="hidden" name="hidden_sample_dtls_tbl_id" id="hidden_sample_dtls_tbl_id" value="">
                                            <input type="hidden" id="hidden_colorSizeID"  value=""/>
                                        </td> 
                                        <td><? echo create_drop_down( "cbo_sample_name", 140,"select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name","id,sample_name", 1, "Select Sample", $selected, "",1,0 );	?></td> 
                                    </tr>
                                    <tr>
                                        <td class="must_entry_caption">Item Name</td>
                                        <td><? echo create_drop_down( "cbo_item_name", 140, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 ); ?></td>  
                                    </tr>
                                    <tr>
                                        <td class="must_entry_caption">Input Date</td>
                                        <td> 
                                        	<input name="txt_sewing_date" id="txt_sewing_date" class="datepicker" type="text" value="<? echo date("d-m-Y")?>" style="width:130px;"  onChange="load_drop_down( 'requires/sample_sewing_input_controller', document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+this.value, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="must_entry_caption">Sample Team</td> 
                                        <td><? echo create_drop_down( "cbo_sample_team", 140,"select id,team_name from lib_sample_production_team where is_deleted=0 and status_active=1 order by team_name","id,team_name", 1, "Select Team", $selected ); ?></td> 
                                    </tr>
                                    <tr>
                                        <td class="must_entry_caption">Input Quantity</td> 
                                        <td><input name="txt_sewing_qty" id="txt_sewing_qty" class="text_boxes_numeric"  style="width:130px" readonly ></td>
                                    </tr>
                                    <tr>
                                        <td>Remarks</td>
                                        <td><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:130px;" /></td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td width="1%" valign="top">&nbsp;</td>
                        <td width="23%" valign="top">
                            <fieldset>
                            <legend>Display</legend>
                                <table  cellpadding="0" cellspacing="2" width="100%" >
                                    <tr>
                                        <td width="110" id="dynamic_cut_qty">Total Cutting Qty</td>
                                        <td><input type="text" name="txt_total_cutting_qty" id="txt_total_cutting_qty" class="text_boxes_numeric" style="width:80px" readonly disabled /></td>
                                    </tr>
                                    <tr>
                                        <td width="110">Cumul. Input Qty</td>
                                        <td><input type="text" name="txt_cumul_sewing_qty" id="txt_cumul_sewing_qty" class="text_boxes_numeric" style="width:80px" readonly disabled /></td>
                                    </tr>
                                    <tr>
                                        <td width="110">Yet to Input</td>
                                        <td><input type="text" name="txt_yet_to_sewing" id="txt_yet_to_sewing" class="text_boxes_numeric" style="width:80px" / readonly disabled ></td>
                                    </tr>
                                </table>
                            </fieldset>	
                        </td>
                        <td width="43%" valign="top" >
                            <div style="max-height:380px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                        </td>                         
                     </tr>
                     <tr>
		   				<td align="center" colspan="4" valign="middle" class="button_container">
							<?
							$date=date('d-m-Y');
                            echo load_submit_buttons( $permission, "fnc_sample_sewing_entry", 0,0,"reset_form('samplesewinginput_1','list_view_country','','txt_sewing_date,".$date."','childFormReset()')",1); 
                            ?>
                            <input type="hidden" name="dtls_update_id" id="dtls_update_id" readonly />
           				</td>
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