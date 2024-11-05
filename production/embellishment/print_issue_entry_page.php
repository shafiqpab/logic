<?
/*-------------------------------------------- Comments
Purpose			: 	This form will created print issue entry
				
Functionality	:	
JS Functions	:
Created by		:	REZA 
Creation date 	: 	25-05-2015
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Print Issue Entry","../../", 1, 1, $unicode,'','');
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1040px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var po_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
			var item_id=this.contentDoc.getElementById("hidden_grmtItem_id").value;
			var po_qnty=this.contentDoc.getElementById("hidden_po_qnty").value;	
			var country_id=this.contentDoc.getElementById("hidden_country_id").value;
				
			if (po_id!="")
			{ 
				freeze_window(5);
				$("#txt_order_qty").val(po_qnty);
				$('#cbo_item_name').val(item_id);
				$("#cbo_country_name").val(country_id);
				
				childFormReset();//child form initialize
				$('#cbo_embel_name').val(0);
				$('#cbo_embel_type').val(0);
				get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_issue_entry_page_controller" );
 				
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
				
				show_list_view(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id+'**1','show_dtls_listview','printing_production_list_view','requires/print_issue_entry_page_controller','setFilterGrid(\'tbl_list_search\',-1)');	
				
							
				show_list_view(po_id,'show_country_listview','list_view_country','requires/print_issue_entry_page_controller','');	
				set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0);
				release_freezing();
			}
			$("#cbo_company_name").attr("disabled","disabled"); 
		}
	}//end else
}//end function



function fnc_issue_print_embroidery_entry(operation)
{
	if(operation==4)
	{
		 var report_title=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_name').val()+'*'+$('#txt_mst_id').val()+'*'+report_title, "emblishment_issue_print", "requires/print_issue_entry_page_controller" ) 
		 return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if ( form_validation('cbo_company_name*txt_order_no*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*txt_issue_date*txt_issue_qty','Company Name*Order No*Embel. Name* Embel. Type*Source*Embel.Company*Issue Date*Issue Quantity')==false )
		{
			return;
		}		
		else
		{
			
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_issue_date').val(), current_date)==false)
			{
				alert("Embel Issue Date Can not Be Greater Than Current Date");
				return;
			}	
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
			
			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*sewing_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*cbo_location*cbo_floor*txt_issue_date*txt_issue_qty*txt_challan*txt_remark*txt_cutting_qty*txt_cumul_issue_qty*txt_yet_to_issue*hidden_break_down_html*txt_mst_id',"../../");
			//alert (data);return;
			freeze_window(operation);
			http.open("POST","requires/print_issue_entry_page_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_issue_print_embroidery_Reply_info;
		}
	}
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
		
		var reponse=http.responseText.split('**');		 
		if(reponse[0]==15) 
		{ 
			 setTimeout('fnc_issue_print_embroidery_entry('+ reponse[1]+')',8000); 
		}
		if(reponse[0]==0)//insert
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
 			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id,'show_dtls_listview','printing_production_list_view','requires/print_issue_entry_page_controller','setFilterGrid(\'tbl_list_search\',-1)');
			reset_form('','','txt_issue_qty*txt_challan*txt_iss_id*txt_remark*hidden_break_down_html*hidden_colorSizeID*txt_mst_id','txt_issue_date,<? echo date("d-m-Y"); ?>','');
			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_issue_entry_page_controller" );
			
			if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id, "color_and_size_level", "requires/print_issue_entry_page_controller" ); 
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
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id,'show_dtls_listview','printing_production_list_view','requires/print_issue_entry_page_controller','setFilterGrid(\'tbl_list_search\',-1)');
			reset_form('','','txt_issue_qty*txt_challan*txt_iss_id*txt_remark*hidden_break_down_html*hidden_colorSizeID*txt_mst_id','txt_issue_date,<? echo date("d-m-Y"); ?>','');
 			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_issue_entry_page_controller" );
			
 			if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id, "color_and_size_level", "requires/print_issue_entry_page_controller" ); 
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
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id,'show_dtls_listview','printing_production_list_view','requires/print_issue_entry_page_controller','setFilterGrid(\'tbl_list_search\',-1)');
			reset_form('','','txt_issue_qty*txt_challan*txt_iss_id*txt_remark*hidden_break_down_html*hidden_colorSizeID*txt_mst_id','txt_issue_date,<? echo date("d-m-Y"); ?>','');
 			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_issue_entry_page_controller" );
			
 			if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id, "color_and_size_level", "requires/print_issue_entry_page_controller" ); 
			}
			else
			{
				$("#txt_issue_qty").removeAttr("readonly");
			}
			set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0);
			release_freezing();
		}
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
	$("#txt_issue_qty").val(totalVal);
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
	$("#txt_issue_qty").val( $("#total_color").val() );
} 

function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty)
{
	freeze_window(5);
	
	childFormReset();//child from reset
	$("#cbo_item_name").val(item_id);
	$("#txt_order_qty").val(po_qnty);
	$("#cbo_country_name").val(country_id);
	
	$('#cbo_embel_name').val(0);
	$('#cbo_embel_type').val(0);
 				
	get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/print_issue_entry_page_controller" );
	
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
	
	show_list_view(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id+'**1','show_dtls_listview','printing_production_list_view','requires/print_issue_entry_page_controller','');	
	set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0);
	release_freezing();
}

</script>
</head>
<body onLoad="set_hotkey()">
 <div style="width:100%;">
  	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div style="width:930px; float:left" align="center"> 
 		<fieldset style="width:930px;">
        <legend>Production Module</legend>
        <form name="printembro_1" id="printembro_1" method="" autocomplete="off" >
            <fieldset>
                <table width="100%">
                    <tr>
                        <td width="130" class="must_entry_caption">Company</td>
                        <td>
                            <? 
                            echo create_drop_down( "cbo_company_name", 200, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/print_issue_entry_page_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value,'load_variable_settings','requires/print_issue_entry_page_controller');" );
                            ?>
                            <input type="hidden" id="sewing_production_variable" />	 
                            <input type="hidden" id="styleOrOrderWisw" />
                        </td>
                        <td width="130" class="must_entry_caption">Order No</td>
                        <td width="170">
                            <input name="txt_order_no" placeholder="Double Click to Search" onDblClick="openmypage('requires/print_issue_entry_page_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Order Search')" id="txt_order_no" class="text_boxes" style="width:190px " readonly />
                            <input type="hidden" id="hidden_po_break_down_id" value="" />
                        </td>
                        <td width="130" >Country</td>
                        <td width="170">
                            <?
                                echo create_drop_down("cbo_country_name",200,"select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 );
                            ?> 
                        </td>
                    </tr>
                    <tr>    
                        <td width="130">Buyer</td>
                        <td width="170">
                            <? 
                            echo create_drop_down( "cbo_buyer_name", 200, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 );
                            ?>	
                        </td>
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
                    </tr>
                    <tr>    
                        <td width="130">Order Qnty</td>
                        <td width="170">
                            <input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric"  style="width:188px" disabled readonly>
                        </td>
                         <td width="130" class="must_entry_caption">Embel. Name</td>
                         <td width="170" id="embel_name_td">
                             <? 
							 echo create_drop_down( "cbo_embel_name", 200, $blank_array,"", 1, "-- Select Embel.Name --", $selected, "" );
                             //echo create_drop_down( "cbo_embel_name", 200, $emblishment_name_array,"", 1, "-- Select Embel.Name --", $selected, "load_drop_down( 'requires/print_issue_entry_page_controller', this.value, 'load_drop_down_embro_issue_type', 'embro_type_td'); get_php_form_data($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#sewing_production_variable').val()+'**'+$('#styleOrOrderWisw').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val(), 'color_and_size_level', 'requires/print_issue_entry_page_controller' ); show_list_view($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val(),'show_dtls_listview','printing_production_list_view','requires/print_issue_entry_page_controller',''); get_php_form_data($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val(), 'populate_data_from_search_popup', 'requires/print_issue_entry_page_controller' );" );
                             ?>
                         </td>
                         <td width="130" class="must_entry_caption">Embel. Type</td>
                         <td id="embro_type_td" width="170">
                             <? 
                             echo create_drop_down( "cbo_embel_type", 200, $blank_array,"", 1, "-- Select --", $selected, "" );
                             //echo create_drop_down( "cbo_embel_type", 200, $emblishment_print_type,"", 1, "--- Select Printing ---", $selected, "" );
                             ?>
                         </td>
                    </tr> 
                    <tr>
                          <td width="130" class="must_entry_caption">Source</td>
                          <td width="170">
                              <? 
                              echo create_drop_down( "cbo_source", 200, $knitting_source,"", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/print_issue_entry_page_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_embro_issue_source', 'emb_company_td' );", 0, '1,3' );
                               ?>
                          </td>
                          <td width="130" class="must_entry_caption">Embel.Company</td>
                          <td id="emb_company_td">
                              <? 
                              echo create_drop_down( "cbo_emb_company", 200, $blank_array,"", 1, "-- Select --", $selected, "" );
                              ?>
                          </td>
                          <td width="130">Location</td>
                          <td width="170" id="location_td">
                              <? 
                              echo create_drop_down( "cbo_location", 200, $blank_array,"", 1, "-- Select Location --", $selected, "" );
                              ?>
                          </td>
                    </tr>
                    <tr>
                         <td width="130">Floor</td>
                         <td width="170" id="floor_td">
                             <? 
                             echo create_drop_down( "cbo_floor", 200, $blank_array,"", 1, "-- Select Floor --", $selected, "" );
                             ?>
                         </td>
                    </tr>
                </table>
                </fieldset> <br />
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                          <td width="35%" valign="top">
                               <fieldset>
                                  <legend>New Entry</legend>
                                       <table  cellpadding="0" cellspacing="2" width="100%">
                                          <tr>
                                               <td width="80" class="must_entry_caption">Issue Date</td>
                                               <td colspan="3" width="110"> 
                                                    <input type="text" name="txt_issue_date" id="txt_issue_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:100px;"  />
                                               </td>
                                          </tr> 
                                          <tr>
                                               <td class="must_entry_caption">Issue Qty</td> 
                                               <td colspan="3"> 
                                                   <input type="text" name="txt_issue_qty" id="txt_issue_qty"  class="text_boxes_numeric"  style="width:100px" readonly >
                                                   <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
                                                   <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
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
                                               <td>Remarks</td> 
                                               <td colspan="3"> 
                                               <input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:212px" title="450 Characters Only." />
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
                                            <td width="100">Cutt. Qty</td>
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
                                echo load_submit_buttons( $permission, "fnc_issue_print_embroidery_entry", 0,1 ,"reset_form('printembro_1','list_view_country','','txt_issue_date,".$date."','childFormReset()')",1); 
                            ?>
                            <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
                        </td>
                        <td>&nbsp;</td>				
                    </tr>
               </table>
               <div style="width:900px; margin-top:5px;"  id="printing_production_list_view" align="center"></div>
        </form>
        </fieldset>
    </div>
	<div id="list_view_country" style="width:370px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
//$('#cbo_embel_name').val(1);
//$('#cbo_embel_name').trigger('change');
</script>
</html>