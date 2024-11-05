<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Garments Order Entry

Functionality	:
JS Functions	:
Created by		:	Bilas
Creation date 	: 	06-03-2013
Updated by 		: 	Kausar (Creating Print Report )
Update date		: 	09-01-2014
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
echo load_html_head_contents("Ex Factory Info","../", 1, 1, $unicode,'','');

?>
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function openmypage_lcsc(page_link,title)
{
	var company = $("#cbo_company_name").val();
	if( form_validation('txt_order_no','Order Number')==false )
	{
		return;
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1040px,height=380px,center=1,resize=0,scrolling=0','')
	emailwindow.onclose=function()
	{
		//hidden_invoice_no hidden_lcsc_no
		var theform=this.contentDoc.forms[0];
		var invoice_no=( this.contentDoc.getElementById("hidden_invoice_no").value).split("**");
		var lcsc_no=( this.contentDoc.getElementById("hidden_lcsc_no").value).split("**");
		var invoice_id=invoice_no[0];
		var lcsc_id=lcsc_no[0];
		
		$("#txt_invoice_no").val(invoice_no[1]);
		$("#txt_lc_sc_no").val(lcsc_no[1]);
		$("#txt_invoice_no").attr('placeholder',invoice_no[0]);
		$("#txt_lc_sc_no").attr('placeholder',lcsc_no[0]);
	}
}

function openmypage(page_link,title)
{
	var company = $("#cbo_company_name").val();
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=370px,center=1,resize=0,scrolling=0','')
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
			$("#cbo_item_name").val(item_id);
			$("#cbo_country_name").val(country_id);

 			childFormReset();//child from reset
			get_php_form_data(po_id+'**'+item_id+'**'+country_id, "populate_data_from_search_popup", "requires/ex_factory_controller" );
			com_variable_chk();
			var variableSettings=$('#sewing_production_variable').val();
			var styleOrOrderWisw=$('#styleOrOrderWisw').val();
			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id, "color_and_size_level", "requires/ex_factory_controller" );
			}
			else
			{
				$("#txt_ex_quantity").removeAttr("readonly");
			}
			show_list_view(po_id+'**'+item_id+'**'+country_id,'show_dtls_listview','ex_factory_list_view','requires/ex_factory_controller','setFilterGrid(\'tbl_list_search\',-1)');
			show_list_view(po_id,'show_country_listview','list_view_country','requires/ex_factory_controller','');
  			set_button_status(0, permission, 'fnc_exFactory_entry',1,0);
 			release_freezing();
		}
	}
}

function fnc_exFactory_entry(operation)
{
	if(operation==4)
	{
		 var report_title=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_name').val()+'*'+$('#txt_mst_id').val()+'*'+report_title, "ex_factory_print", "requires/ex_factory_controller" )
		 return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if ( form_validation('cbo_company_name*txt_order_no*txt_ex_quantity*txt_ex_factory_date','Company Name*Order No*ex-factory Quantity*Date')==false )
		{
			return;
		}
		else
		{
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_ex_factory_date').val(), current_date)==false)
			{
				alert("Ex Factory Date Can not Be Greater Than Current Date");
				return;
			}

			if($("#txt_invoice_no").val()!='')
				var invoice_id = $("#txt_invoice_no").attr('placeholder');
			else
				var invoice_id = '';

			if($("#txt_lc_sc_no").val()!='')
				var lcsc_id = $("#txt_lc_sc_no").attr('placeholder');
			else
				var lcsc_id = '';


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

			var data="action=save_update_delete&operation="+operation+'&invoice_id='+invoice_id+'&lcsc_id='+lcsc_id+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*sewing_production_variable*cbo_location_name*cbo_item_name*hidden_po_break_down_id*hidden_colorSizeID*txt_ex_factory_date*txt_ex_quantity*txt_total_carton_qnty*txt_challan_no*txt_invoice_no*txt_lc_sc_no*txt_ctn_qnty*txt_transport_com*txt_remark*shipping_status*txt_finish_quantity*txt_cumul_quantity*txt_yet_quantity*txt_mst_id*cbo_ins_qty_validation_type',"../");
 			freeze_window(operation);
 			http.open("POST","requires/ex_factory_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_exFactory_entry_Reply_info;
		}
	}
}

function fnc_exFactory_entry_Reply_info()
{
 	if(http.readyState == 4)
	{
		//alert(http.responseText);return;
		var variableSettings=$('#sewing_production_variable').val();
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();
		var item_id=$('#cbo_item_name').val();
		var country_id = $("#cbo_country_name").val();

		var reponse=http.responseText.split('**');
		if(reponse[0]==15)
		{
			 setTimeout('fnc_exFactory_entry('+ reponse[1]+')',8000);
		}
		else if(reponse[0]==0)
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id,'show_dtls_listview','ex_factory_list_view','requires/ex_factory_controller','setFilterGrid(\'tbl_list_search\',-1)');
			reset_form('','','txt_ex_quantity*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*txt_challan_no*txt_invoice_no*txt_lc_sc_no*txt_ctn_qnty*txt_transport_com*shipping_status*txt_remark','','','txt_ex_factory_date');

			$('#txt_invoice_no').attr('placeholder','Double Click To Search');//placeholder value initilize
			$('#txt_lc_sc_no').attr('placeholder','');//placeholder value initilize

			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id, "populate_data_from_search_popup", "requires/ex_factory_controller" );

			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id, "color_and_size_level", "requires/ex_factory_controller" );
			}
			else
			{
				$("#txt_ex_quantity").removeAttr("readonly");
			}
		}
		else if(reponse[0]==1)
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id,'show_dtls_listview','ex_factory_list_view','requires/ex_factory_controller','setFilterGrid(\'tbl_list_search\',-1)');
			reset_form('','','txt_ex_quantity*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*txt_challan_no*txt_invoice_no*txt_lc_sc_no*txt_ctn_qnty*txt_transport_com*shipping_status*txt_remark*cbo_company_name','','','txt_ex_factory_date');
			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id, "populate_data_from_search_popup", "requires/ex_factory_controller" );
			$('#txt_ex_quantity').attr('placeholder','');//placeholder value initilize
			$('#txt_invoice_no').attr('placeholder','Double Click To Search');//placeholder value initilize
			$('#txt_lc_sc_no').attr('placeholder','');//placeholder value initilize

			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id, "color_and_size_level", "requires/ex_factory_controller" );
			}
			else
			{
				$("#txt_ex_quantity").removeAttr("readonly");
			}
			set_button_status(0, permission, 'fnc_exFactory_entry',1,0);
		}
		else if(reponse[0]==2)
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id,'show_dtls_listview','ex_factory_list_view','requires/ex_factory_controller','');
			reset_form('','','txt_ex_quantity*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*txt_challan_no*txt_invoice_no*txt_lc_sc_no*txt_ctn_qnty*txt_transport_com*shipping_status*txt_remark','','','txt_ex_factory_date');
			$('#txt_ex_quantity').attr('placeholder','');//placeholder value initilize
			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id, "populate_data_from_search_popup", "requires/ex_factory_controller" );

			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id, "color_and_size_level", "requires/ex_factory_controller" );
			}
			else
			{
				$("#txt_ex_quantity").removeAttr("readonly");
			}
			set_button_status(0, permission, 'fnc_exFactory_entry',1,0);
		}
		else if(reponse[0]==25)
		{
			$("#txt_ex_quantity").val("");
			show_msg('30');
		}
		else if(reponse[0]==35)
		{
			$("#txt_ex_quantity").val("");
			show_msg('30');
			alert(reponse[1]);
			release_freezing();
			return;
		}
		else if(reponse[0]==786)
		{
			alert("Projected PO is not allowed to production. Please check variable settings.");
		}
		release_freezing();
 	}
}

function childFormReset()
{
	reset_form('','ex_factory_list_view','txt_ex_quantity*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*txt_challan_no*txt_invoice_no*txt_lc_sc_no*txt_ctn_qnty*txt_transport_com*txt_remark*shipping_status*txt_finish_quantity*txt_cumul_quantity*txt_yet_quantity','','');
	$('#txt_ex_quantity').attr('placeholder','');//placeholder value initilize
	$('#txt_finish_quantity').attr('placeholder','');//placeholder value initilize
	$('#txt_cumul_quantity').attr('placeholder','');//placeholder value initilize
	$('#txt_yet_quantity').attr('placeholder','');//placeholder value initilize
 	$("#ex_factory_list_view").html('');
	$("#breakdown_td_id").html('');
}

function fn_qnty_per_ctn()
{
	 var exQnty = $('#txt_ex_quantity').val();
	 var ctnQnty = $('#txt_total_carton_qnty').val();

	 if(exQnty!="" && ctnQnty!="")
	 {
		 var ctn_per_qnty = parseInt( Number( exQnty/ctnQnty ) );
		 $('#txt_ctn_qnty').val(ctn_per_qnty);
	 }
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
	$("#txt_ex_quantity").val(totalVal);
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
	$("#txt_ex_quantity").val( $("#total_color").val() );
}

function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty)
{
	freeze_window(5);

	$("#cbo_item_name").val(item_id);
	$("#txt_order_qty").val(po_qnty);
	$("#cbo_country_name").val(country_id);

	childFormReset();//child from reset
	get_php_form_data(po_id+'**'+item_id+'**'+country_id, "populate_data_from_search_popup", "requires/ex_factory_controller" );

	var variableSettings=$('#sewing_production_variable').val();
	var styleOrOrderWisw=$('#styleOrOrderWisw').val();
	if(variableSettings!=1)
	{
		get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id, "color_and_size_level", "requires/ex_factory_controller" );
	}
	else
	{
		$("#txt_ex_quantity").removeAttr("readonly");
	}

	show_list_view(po_id+'**'+item_id+'**'+country_id,'show_dtls_listview','ex_factory_list_view','requires/ex_factory_controller','');
	set_button_status(0, permission, 'fnc_exFactory_entry',1,0);
	release_freezing();
}
function com_variable_chk()
{
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	alert(cbo_company_name);
	get_php_form_data(cbo_company_name,'load_variable_settings','requires/ex_factory_controller');

}

</script>
</head>
<body onLoad="set_hotkey();com_variable_chk();">
<div style="width:100%;">
	<?  echo load_freeze_divs ("../",$permission);  ?>
    <div style="width:930px; float:left" align="center">
        <form name="exFactory_1" id="exFactory_1" autocomplete="off" >
        <fieldset style="width:930px;">
            <legend>Production Module</legend>
                <fieldset>
                <table width="100%" border="0">
                    <tr>
                        <td width="130" align="right" class="must_entry_caption">Company Name </td>
                        <td width="170">
                            <?
							//get_php_form_data(this.value,'load_variable_settings','requires/ex_factory_controller');
                            echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", '', "load_drop_down( 'requires/ex_factory_controller', this.value, 'load_drop_down_location', 'location_td' ); ",0 ); ?>
                            <input type="hidden" name="sewing_production_variable" id="sewing_production_variable" value="" />
                            <input type="hidden" id="styleOrOrderWisw" />
                            <input type="hidden" id="variable_is_controll" />
                            <input type="hidden" id="txt_user_lebel" value="<? echo $_SESSION['logic_erp']['user_level']; ?>" />
                        </td>
                        <td width="130" align="right">Location</td>
                        <td width="170" id="location_td">
                           <? echo create_drop_down( "cbo_location_name", 172, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                        </td>
                        <td width="130" align="right" class="must_entry_caption">Order No</td>
                        <td><input name="txt_order_no" id="txt_order_no"  placeholder="Double Click to Search" onDblClick="openmypage('requires/ex_factory_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Order Search')" class="text_boxes" style="width:160px " readonly />
                        <input type="hidden" id="hidden_po_break_down_id" value="" /></td>
                    </tr>
                    <tr>
                        <td align="right">Buyer Name</td>
                        <td  id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1 );?></td>
                        <td align="right"> Job No.</td>
                        <td>
                             <input style="width:160px;" type="text"   class="text_boxes" name="txt_job_no" id="txt_job_no" disabled  />
                        </td>

                        <td align="right">
                           Style
                        </td>
                        <td>
                            <input class="text_boxes" name="txt_style_no" id="txt_style_no" type="text" style="width:160px;" disabled />
                        </td>
                     </tr>
                    <tr>
                         <td align="right">Shipment Date</td>
                        <td>
                            <input class="text_boxes" name="txt_shipment_date" id="txt_shipment_date"   style="width:160px" disabled />
                        </td>
                        <td height="" align="right">Item</td>
                            <td >
                               <?
                                 echo create_drop_down( "cbo_item_name", 172, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );
                              ?>
                            </td>

                          <td align="right">Order Qty.</td>
                          <td>
                                 <input class="text_boxes"  name="txt_order_qty" id="txt_order_qty" type="text" style="width:160px;" disabled/>
                          </td>
                     </tr>
                     <tr>
                        <td align="right">Country</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_country_name", 172, "select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 );
                            ?>
                        </td>
                        <td align="right">Inspection Qty Validation</td>
                        <td>
                             <?
							    echo create_drop_down( "cbo_ins_qty_validation_type", 172, $validation_type, 1, "-- Select --", $selected,1,1 );
							?>
                        </td>
                     </tr>
                </table>
                </fieldset>
                <br />
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                        <td width="30%" valign="top">
                          <fieldset>
                          <legend>New Entry</legend>
                            <table  cellpadding="0" cellspacing="2" width="100%">
                                <tr>
                                   <td width="110" align="right" class="must_entry_caption">Ex- Factory Date </td>
                                   <td width="190">
                                        <input name="txt_ex_factory_date" id="txt_ex_factory_date" class="datepicker" value="<? echo date("d-m-Y")?>" style="width:100px;" >
                                   </td>
                                </tr>
                                <tr>
                                    <td align="right" class="must_entry_caption"> Ex- Factory Qnty</td>
                                    <td>
                                        <input name="txt_ex_quantity" id="txt_ex_quantity" class="text_boxes_numeric" type="text"  style="width:100px;" readonly />
                                        <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
                                        <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">Total Carton Qnty</td>
                                    <td>
                                       <input name="txt_total_carton_qnty" id="txt_total_carton_qnty" type="text" class="text_boxes_numeric"  style="width:100px" onKeyUp="fn_qnty_per_ctn()" />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right"> Challan No</td>
                                    <td>
                                      <input name="txt_challan_no" id="txt_challan_no" class="text_boxes" type="text"  style="width:100px" maxlength="50" />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right"> Invoice No</td>
                                    <td>
                                      <input name="txt_invoice_no" id="txt_invoice_no" type="text" style="width:100px;" onDblClick="openmypage_lcsc('requires/ex_factory_controller.php?action=lcsc_popup&company='+document.getElementById('cbo_company_name').value,'Order Search')" class="text_boxes" placeholder="Double Click To Search" maxlength="50" readonly  />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right"> LC/SC No</td>
                                    <td>
                                      <input name="txt_lc_sc_no" id="txt_lc_sc_no"  class="text_boxes" type="text" style="width:100px" maxlength="50" readonly />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right"> Qnty/Ctn(Pcs/Set)</td>
                                    <td>
                                         <input name="txt_ctn_qnty" id="txt_ctn_qnty" class="text_boxes_numeric"  style="width:100px" readonly />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">Trans. Company</td>
                                    <td>
                                         <input name="txt_transport_com" id="txt_transport_com"  class="text_boxes" type="text" style="width:100px" maxlength="50" />
                                    </td>
                                </tr>
                                <tr>
                                     <td width="102" align="right">Remarks</td>
                                     <td width="165">
                                         <input name="txt_remark" id="txt_remark" type="text"  class="text_boxes" style="width:150px;" maxlength="450"  />
                                     </td>
                                </tr>
                                <tr>
                                      <td width="102" align="right">Shipping Status<span id="completion_perc"></span></td>
                                      <td width="165">
                                          <?
                                             echo create_drop_down( "shipping_status", 110, $shipment_status,"", 0, "-- Select --", 2, "",0,'2,3','','','','' );
                                         ?>
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
                                      <td width="160" align="right"> Sewing Finish Qnty</td>
                                      <td>
                                          <input name="txt_finish_quantity" id="txt_finish_quantity"  class="text_boxes_numeric" type="text" style="width:80px" disabled readonly  />
                                      </td>
                                  </tr>
                                  <tr>
                                      <td align="right">Cuml. Ex-Factory Qnty</td>
                                      <td>
                                          <input type="text" name="txt_cumul_quantity" id="txt_cumul_quantity" class="text_boxes_numeric"  style="width:80px" disabled readonly  />
                                      </td>
                                  </tr>
                                   <tr>
                                      <td align="right">Yet to Ex-Factory Qnty</td>
                                      <td>
                                          <input type="text" name="txt_yet_quantity" id="txt_yet_quantity" class="text_boxes_numeric"  style="width:80px" disabled readonly />
                                      </td>
                                  </tr>
                               </table>
                          </fieldset>
                      </td>
                    <td width="41%" valign="top">
                        <div style="max-height:330px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                    </td>
                </tr>
                </table>
                <br />
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                        <td align="center" colspan="6" valign="middle" class="button_container">
                             <?
								$date=date('d-m-Y');
                                echo load_submit_buttons( $permission, "fnc_exFactory_entry", 0,1,"reset_form('exFactory_1','ex_factory_list_view*list_view_country','','txt_ex_factory_date,".$date."','childFormReset()')",1);
                            ?>
                             <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
                        </td>
                    </tr>
                </table>
                <div style="width:930px; margin-top:5px;"  id="ex_factory_list_view" align="center"></div>
           </fieldset>
        </form>
    </div>
	<div id="list_view_country" style="width:370px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
