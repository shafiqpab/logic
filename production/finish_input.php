<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create iron input
				
Functionality	:	
JS Functions	:
Created by		:	Bilas 
Creation date 	: 	25-03-2013
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
echo load_html_head_contents("Finish Input Info","../", 1, 1, $unicode,'','');

?>	

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';

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
			if (po_id!="")
			{
				freeze_window(5);
				$("#txt_order_qty").val(po_qnty);
				$("#cbo_item_name").val(item_id);
				childFormReset();//child from reset
				get_php_form_data(po_id+'**'+item_id, "populate_data_from_search_popup", "requires/finish_input_controller" );
 				
				var variableSettings=$('#sewing_production_variable').val();
				var styleOrOrderWisw=$('#styleOrOrderWisw').val();
				if(variableSettings!=1){ 
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw, "color_and_size_level", "requires/finish_input_controller" ); 
				}
				else
				{
					$("#txt_iron_qty").removeAttr("readonly");
				}
				show_list_view(po_id+'**'+item_id,'show_dtls_listview','list_view_container','requires/finish_input_controller','');
				set_button_status(0, permission, 'fnc_iron_input',1);
				release_freezing();
			}
		}
	}//end else
}//end function



function fnc_iron_input(operation)
{
	 
 	if ( form_validation('cbo_company_name*txt_order_no*cbo_iron_company*txt_iron_date*txt_iron_qty','Company Name*Order No*Iron Company*Input Date*Input Quantity')==false )
		{
			return;
		}		
	else
		{
 
			freeze_window(operation);			
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
			 
			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('garments_nature*cbo_company_name*sewing_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*cbo_source*cbo_iron_company*cbo_location*cbo_floor*txt_iron_date*txt_reporting_hour*cbo_time*txt_iron_qty*txt_remark*txt_sewing_quantity*txt_cumul_iron_qty*txt_yet_to_iron*hidden_break_down_html*txt_mst_id',"../../");
 			
 			http.open("POST","requires/finish_input_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_iron_input_Reply_info;
		}
}
  
function fnc_iron_input_Reply_info()
{
 	if(http.readyState == 4) 
	{
		// alert(http.responseText);
		var variableSettings=$('#sewing_production_variable').val();
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();
		var item_id=$('#cbo_item_name').val();
		var reponse=http.responseText.split('**');		 
		if(reponse[0]==15) 
		{ 
			 setTimeout('fnc_fabric_cost_dtls('+ reponse[1]+')',8000); 
		}
		if(reponse[0]==0)
		{ 
			var po_id = reponse[1];
			show_msg(reponse[0]);
 			show_list_view(po_id+'**'+$("#cbo_item_name").val(),'show_dtls_listview','list_view_container','requires/finish_input_controller','');
			reset_form('','','txt_iron_date*txt_reporting_hour*cbo_time*txt_iron_qty*txt_remark*txt_sewing_quantity*txt_cumul_iron_qty*txt_yet_to_iron*hidden_break_down_html*txt_mst_id','','');
			get_php_form_data(po_id+'**'+$('#cbo_item_name').val(), "populate_data_from_search_popup", "requires/finish_input_controller" );
 			if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw, "color_and_size_level", "requires/finish_input_controller" ); 
			}
			else
			{
				$("#txt_iron_qty").removeAttr("readonly");
			}
			release_freezing();
		}
		if(reponse[0]==1)
		{
			var po_id = reponse[1];
			show_msg(reponse[0]);
			show_list_view(po_id+'**'+$("#cbo_item_name").val(),'show_dtls_listview','list_view_container','requires/finish_input_controller','');
			reset_form('','','txt_iron_date*txt_reporting_hour*cbo_time*txt_iron_qty*txt_remark*txt_sewing_quantity*txt_cumul_iron_qty*txt_yet_to_iron*hidden_break_down_html*txt_mst_id','','');
			get_php_form_data(po_id+'**'+$('#cbo_item_name').val(), "populate_data_from_search_popup", "requires/finish_input_controller" );
			if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw, "color_and_size_level", "requires/finish_input_controller" ); 
			}
			else
			{
				$("#txt_iron_qty").removeAttr("readonly");
			}
			set_button_status(0, permission, 'fnc_iron_input',1);
			release_freezing();
		}
		if(reponse[0]==2)
		{
			var po_id = reponse[1];
			show_msg(reponse[0]);
			show_list_view(po_id+'**'+$("#cbo_item_name").val(),'show_dtls_listview','list_view_container','requires/finish_input_controller','');
			reset_form('','','txt_iron_date*txt_reporting_hour*cbo_time*txt_iron_qty*txt_remark*txt_sewing_quantity*txt_cumul_iron_qty*txt_yet_to_iron*hidden_break_down_html*txt_mst_id','','');
			get_php_form_data(po_id+'**'+$('#cbo_item_name').val(), "populate_data_from_search_popup", "requires/finish_input_controller" );
			if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw, "color_and_size_level", "requires/finish_input_controller" ); 
			}
			else
			{
				$("#txt_iron_qty").removeAttr("readonly");
			}
			set_button_status(0, permission, 'fnc_iron_input',1);
			release_freezing();
		}
 	}
} 

function childFormReset()
{
	//txt_iron_date  txt_reporting_hour cbo_time txt_iron_qty txt_remark txt_sewing_quantity txt_cumul_iron_qty txt_yet_to_iron
	reset_form('','','txt_iron_date*txt_reporting_hour*cbo_time*txt_iron_qty*txt_remark*txt_sewing_quantity*txt_cumul_iron_qty*txt_yet_to_iron*hidden_break_down_html*txt_mst_id','','');
 	$('#txt_sewing_quantity').attr('placeholder','');//placeholder value initilize
	$('#txt_cumul_iron_qty').attr('placeholder','');//placeholder value initilize
	$('#txt_yet_to_iron').attr('placeholder','');//placeholder value initilize
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
	$("#txt_iron_qty").val(totalVal);
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
	$("#txt_iron_qty").val( $("#total_color").val() );
} 



</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;">
    <? echo load_freeze_divs ("../",$permission);  ?>
	<fieldset style="width:900px;">
        <legend>Production Module</legend> 
        <form name="ironinput_1" id="ironinput_1" method="post" autocomplete="off" >         
             <fieldset>
                <table width="100%" border="0">
                    <tr>
                        <td width="130" class="must_entry_caption">Company</td>
                        <td width="480" colspan="3">                                
							<? 
                            echo create_drop_down( "cbo_company_name", 470, "select id,company_name from lib_company where status_active =1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/finish_input_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value,'load_variable_settings','requires/finish_input_controller');" );
                            ?>
                             <input type="hidden" id="sewing_production_variable" />	 
                             <input type="hidden" id="styleOrOrderWisw" />	 
                        </td>
                        <td width="100" class="must_entry_caption">Order No</td>
                        <td width="200">
                        <input name="txt_order_no" placeholder="Double Click to Search" id="txt_order_no" onDblClick="openmypage('requires/finish_input_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Order Search')"  class="text_boxes" style="width:155px " readonly />
                         <input type="hidden" id="hidden_po_break_down_id" value="" />
                        </td>
                    </tr>
                    <tr>    
                        <td width="130">Buyer</td>
                        <td width="180">
							<? 
                            echo create_drop_down( "cbo_buyer_name", 170, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 );
                            ?>
                        </td>
                        <td width="100">Job No</td>
                        <td width="200">
                        	<input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:155px " disabled readonly />	
                        </td>
                        <td width="130">Style</td>
                        <td width="">
                        	<input name="txt_style_no" id="txt_style_no" class="text_boxes"  style="width:155px " disabled readonly />
                        </td>
                       
                    </tr>
                    <tr>  
                         <td width="130"> Item </td>
                         <td width="">
							 <?
                             echo create_drop_down( "cbo_item_name", 170, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );	
                             ?>
                         </td>  
                         <td width="130">Order Qnty</td>
                         <td width="">
                         <input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric" style="width:155px " disabled readonly  />
                         </td>
                         <td width="130">Source</td>
                         <td width="">
							 <?
                             echo create_drop_down( "cbo_source", 170, $knitting_source,"", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/finish_input_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_source', 'iron_company_td' );", 0, '1,3' );
                             ?>
                         </td>
                        
                    </tr>
                    <tr>
                         <td width="130" class="must_entry_caption">Iron Company</td>
                         <td width="" id="iron_company_td" >
							 <?
                             echo create_drop_down( "cbo_iron_company", 170, $blank_array,"", 1, "-- Select --", $selected, "" );
                             ?>
                         </td>
                         <td width="130">Location</td>
                         <td width="180" id="location_td">
							 <?
                             echo create_drop_down( "cbo_location", 170, $blank_array,"", 1, "-- Select --", $selected, "" );
                             ?>
                         </td>
                         <td width="130">Floor</td>
                          <td width="" id="floor_td">
							  <? 
                              echo create_drop_down( "cbo_floor", 170, $blank_array,"", 1, "-- Select --", $selected, "" );
                              ?> 
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
                                <td width="120" class="must_entry_caption">Finish. Input Date</td>
                                 <td width="190"> 
                                  	<input type="text" name="txt_iron_date" id="txt_iron_date" class="datepicker" value=""  style="width:100px;"  />
                                </td>
                            </tr>
                             <tr>
                                <td width="">Reporting Hour</td>  
                                 <td width=""> 
                                     <input name="txt_reporting_hour" id="txt_reporting_hour" class="text_boxes_numeric"  style="width:40px" onKeyUp="fn_hour_check()" />
                                     <?
                                     echo create_drop_down( "cbo_time", 50, $time_source, 0, "", 1, "" );
                                     ?> 
                                 </td> 
                             </tr> 
                             <tr>                                
                                <td width="" class="must_entry_caption">Finish. Input Qnty</td> 
                                <td width=""> 
                                    <input type="text" name="txt_iron_qty" id="txt_iron_qty" class="text_boxes_numeric"  style="width:100px" readonly />
                                    <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
                                    <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                </td>
                            </tr>
                            <tr>
                                <td width="">Remarks</td> 
                                <td width="" > 
                                <input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:170px" />
                                </td>
                           </tr>
                        </table>
                    	</fieldset>
                	</td>
					<td width="1%" valign="top"></td>
                	<td width="25%" valign="top">
                        <fieldset>
                        <legend>Display</legend>
                            <table  cellpadding="0" cellspacing="1" width="100%" >
                                <tr>
                                    <td width="130">Sewing Quantity</td>
                                    <td>
                                     <input type="text" name="txt_sewing_quantity" id="txt_sewing_quantity" class="text_boxes_numeric" style="width:80px"  readonly disabled />
                                    </td>
                                </tr>
                                <tr>
                                    <td width=""> Cuml. Fin. Input Qty</td>
                                    <td>
                                     <input type="text" name="txt_cumul_iron_qty" id="txt_cumul_iron_qty" class="text_boxes_numeric" style="width:80px"  readonly disabled />
                                    </td>
                                </tr>
                                 <tr>
                                    <td width="">Yet to Finish. Input</td>
                                    <td>
                                     <input type="text" name="txt_yet_to_iron" id="txt_yet_to_iron" class="text_boxes_numeric" style="width:80px" readonly disabled />
                                    </td>
                                </tr>
                            </table>
                        </fieldset>	
                    </td>
                    <td width="39%" valign="top" >
                        <div style="max-height:350px; overflow-y:scroll; position:fixed" id="breakdown_td_id" align="center"></div>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="9" valign="middle" class="button_container">
                        <?
                        echo load_submit_buttons( $permission, "fnc_iron_input", 0,0 ,"reset_form('ironinput_1','','','','childFormReset()')",1); 
                        ?>
                        <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly />
                    </td>
                    <td>&nbsp;</td>					
                </tr>
			</table>
      		<div style="width:900px; margin-top:5px;"  id="list_view_container" align="center"></div>
		</form>   
	</fieldset>
</div>  
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>