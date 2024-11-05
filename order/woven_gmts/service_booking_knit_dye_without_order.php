<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Service Booking For Knitting and Dyeing [Without Order]
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	06-11-2016	
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
echo load_html_head_contents("Service Booking For Knitting and Dyeing [Without Order]", "../../", 1, 1,$unicode,'','');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	function openmypage_order(page_link,title)
	{
		if (form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}	
		else
		{
			page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=470px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];;
				var id=this.contentDoc.getElementById("booking_id").value;
				
				if (id!="")
				{
					booking_number=id.split('_');
					freeze_window(5);
					document.getElementById('txt_fabric_booking_id').value=booking_number[0];
					document.getElementById('txt_fabric_booking').value=booking_number[1];
					get_php_form_data(booking_number[0], "populate_order_data_from_search_popup", "requires/service_booking_knit_dye_without_order_controller" );
					check_exchange_rate();
					release_freezing();
				}
			}
		}
	}
	
	function load_fabric_dropdown(fabric_source)
	{
		load_drop_down( 'requires/service_booking_knit_dye_without_order_controller', document.getElementById('txt_fabric_booking_id').value+"_"+fabric_source, 'load_drop_down_fabric_description', 'fabric_description_td' )
	}
	
	function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currency').val();
		var booking_date = $('#txt_booking_date').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/service_booking_knit_dye_without_order_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
	}
	
	function get_related_data(fabric_id)
	{
		var cbo_fabric_source=document.getElementById('cbo_fabric_source').value;	
		get_php_form_data(fabric_id+"_"+cbo_fabric_source, "get_related_data", "requires/service_booking_knit_dye_without_order_controller" );
	}
	
	function calculate_amount()
	{
		var txt_woqnty=(document.getElementById('txt_wo_qty').value)*1;
		var txt_rate=(document.getElementById('txt_rate').value)*1;
		var txt_amount=txt_woqnty*txt_rate;
		document.getElementById('txt_amount').value=txt_amount;	
	}
	
	function openmypage_booking(page_link,title)
	{
		page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");
			
			if (theemail.value!="")
			{
				reset_form('servicebooking_1','knit_dye_list_view_panel','','txt_booking_date,<? echo date("d-m-Y"); ?>','','cbo_currency');
				get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/service_booking_knit_dye_without_order_controller" );
				get_php_form_data(document.getElementById('txt_fabric_booking_id').value, "populate_order_data_from_search_popup", "requires/service_booking_knit_dye_without_order_controller" );
				show_list_view(theemail.value+"_"+document.getElementById('txt_fabric_booking_id').value+"_"+document.getElementById('cbo_fabric_source').value, 'knit_dye_detls_list_view','knit_dye_list_view_panel','requires/service_booking_knit_dye_without_order_controller','');
				make_enable_disable(1);
				set_button_status(0, permission, 'fnc_knit_dye_non_ord_booking',1,1);
			}
		}
	}
	
	function fnc_knit_dye_non_ord_booking( operation )
	{
		if(operation==4)
	  {
		  if(confirm('Press Ok To Show Amount and Rate.'))
		  {
			var rate_amount=1;  
		  }
		var report_title=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_name').val()+'*'+$('#txt_booking_no').val()+'*'+$('#txt_fabric_booking_id').val()+'*'+$('#txt_mst_id').val()+'*'+report_title+'*'+$('#txt_fabric_booking').val()+'*'+rate_amount, "service_booking_print", "requires/service_booking_knit_dye_without_order_controller" ) 
		 return;
			}
		if (form_validation('cbo_company_name*txt_fabric_booking*txt_booking_date*txt_dev_start_date*txt_dev_end_date*cbo_pay_mode*cbo_knitdye_type','Company Name*Fab.Booking No*Booking Date*Delivery Start Date*Delivery End Date*Pay Mode*Booking Type')==false)
		{
			return;
		}
		else
		{
			var data_all="";
			data_all=data_all+get_submitted_data_string('txt_booking_no*txt_mst_id*cbo_company_name*txt_fabric_booking*txt_fabric_booking_id*cbo_buyer_name*txt_booking_date*cbo_currency*txt_exchange_rate*cbo_pay_mode*cbo_source*cbo_knitdye_source*cbo_supplier_name*txt_attention*cbo_gmtcolor*cbo_fabric_description*txt_gsm*txt_fin_dia*cbo_uom*txt_art_work*txt_wo_qty*txt_rate*txt_amount*txt_dev_start_date*txt_dev_end_date*txt_remarks*dtls_id*cbo_knitdye_type*cbo_fabric_source',"../../");
		
			//alert(data_all);
			var data="action=save_update_delete&operation="+operation+data_all;
			freeze_window(operation);
			http.open("POST","requires/service_booking_knit_dye_without_order_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_knit_dye_non_ord_booking_reponse;
		}
	}
	
	function fnc_knit_dye_non_ord_booking_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			show_msg(trim(reponse[0]));
			if(reponse[0]==0 || reponse[0]==1)
			{
				document.getElementById('txt_booking_no').value=reponse[1];
				document.getElementById('txt_mst_id').value=reponse[2];
				set_button_status(0, permission, 'fnc_knit_dye_non_ord_booking',1,1);
				show_list_view(reponse[2]+"_"+document.getElementById('txt_fabric_booking_id').value+"_"+document.getElementById('cbo_fabric_source').value, 'knit_dye_detls_list_view','knit_dye_list_view_panel','requires/service_booking_knit_dye_without_order_controller','');
				make_enable_disable(1);
				reset_form('','','cbo_fabric_source*cbo_fabric_description*txt_gsm*txt_fin_dia*cbo_uom*txt_art_work*txt_wo_qty*txt_rate*txt_amount*txt_dev_start_date*txt_dev_end_date*txt_remarks*dtls_id*cbo_knitdye_type','txt_booking_date,<? echo date("d-m-Y"); ?>');
			}
			if(reponse[0]==2)
			{
				set_button_status(0, permission, 'fnc_knit_dye_non_ord_booking',1,1);
			}
			release_freezing();
		}
	}
	
	function get_dtls_data(id)
	{
		get_php_form_data( id+'__'+document.getElementById('txt_fabric_booking_id').value, "populate_data_dtls_from_search_popup", "requires/service_booking_knit_dye_without_order_controller" );
	}
	
	function generate_trim_report(action)
	{
		if (form_validation('txt_booking_no','Booking No')==false)
		{
			return;
		}
		else
		{
			var report_title=$( "div.form_caption" ).html();
			var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_fabric_booking_id*txt_mst_id',"../../")+'&report_title='+report_title;
			http.open("POST","requires/service_booking_knit_dye_without_order_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_trim_report_reponse;
		}	
	}
	
	function generate_trim_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var file_data=http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel2').html(file_data[0] );
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel2').innerHTML+'</body</html>');
			d.close();
		}
	}

	function make_enable_disable(type)
	{
		if(type==1)
		{
			$('#txt_fabric_booking').attr('disabled', 'disabled');
			$('#cbo_buyer_name').attr('disabled', 'disabled');
			$('#cbo_currency').attr('disabled', 'disabled');
			$('#txt_exchange_rate').attr('disabled', 'disabled');    
			$('#cbo_pay_mode').attr('disabled', 'disabled');
			$('#cbo_source').attr('disabled', 'disabled');
			$('#cbo_knitdye_source').attr('disabled', 'disabled');
			$('#cbo_supplier_name').attr('disabled', 'disabled');
			$('#txt_attention').attr('disabled', 'disabled');
		}
		else
		{
			$('#txt_fabric_booking').removeAttr('disabled', 'disabled');
			$('#cbo_buyer_name').removeAttr('disabled', 'disabled');
			$('#cbo_currency').removeAttr('disabled', 'disabled');
			$('#txt_exchange_rate').removeAttr('disabled', 'disabled');    
			$('#cbo_pay_mode').removeAttr('disabled', 'disabled');
			$('#cbo_source').removeAttr('disabled', 'disabled');
			$('#cbo_knitdye_source').removeAttr('disabled', 'disabled');
			$('#cbo_supplier_name').removeAttr('disabled', 'disabled');
			$('#txt_attention').removeAttr('disabled', 'disabled');
		}
	}	

</script>
</head>
<body onLoad="set_hotkey(); check_exchange_rate();">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="servicebooking_1"  autocomplete="off" id="servicebooking_1">
        <fieldset style="width:900px;">
        <legend>Service Booking</legend>
            <table width="880" cellspacing="2" cellpadding="0" border="1">
                <tr>
                    <td colspan="3" align="right" class="must_entry_caption">WO No </td>
                    <td colspan="3">
                        <input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/service_booking_knit_dye_without_order_controller.php?action=knit_dye_without_order_booking_search','Knitting and Dyeing Without Order Booking Search')" readonly placeholder="Browse" name="txt_booking_no" id="txt_booking_no"/>
                        <input class="text_boxes" type="hidden" style="width:160px"  readonly  name="txt_mst_id" id="txt_mst_id" />
                    </td>
                </tr>
                <tr>
                    <td width="120" class="must_entry_caption">Company Name</td>
                    <td width="160">
						<? $date=date('d-m-Y');
                        echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "reset_form('servicebooking_1','knit_dye_list_view_panel','','txt_booking_date,".$date."','','cbo_company_name*cbo_currency');load_drop_down( 'requires/service_booking_knit_dye_without_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' ); make_enable_disable(0);","","" ); ?>
                    </td>
                    <td width="120" class="must_entry_caption">Fab.Booking No</td>   
                    <td width="160">
                        <input class="text_boxes" type="text" style="width:140px;" placeholder="Double click for Booking"  onDblClick="openmypage_order('requires/service_booking_knit_dye_without_order_controller.php?action=order_search_popup','Order Search')"   name="txt_fabric_booking" id="txt_fabric_booking"/>
                        <input class="text_boxes" type="hidden" style="width:772px;"  name="txt_fabric_booking_id" id="txt_fabric_booking_id"/>
                    </td>   
                    <td width="120">Buyer Name</td>   
                    <td id="buyer_td"> <? echo create_drop_down( "cbo_buyer_name", 150, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,"" ); ?>
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Booking Date</td>
                    <td><input class="datepicker" type="text" style="width:140px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" disabled />	
                    </td>   
                    <td>Currency</td>
                    <td><? echo create_drop_down( "cbo_currency", 150, $currency,"", 1, "-- Select --", 2, "check_exchange_rate()",0 );	?></td>	
                    <td>Exchange Rate</td>
                    <td><input style="width:140px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly /></td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Pay Mode</td>
                    <td><? echo create_drop_down( "cbo_pay_mode", 150, $pay_mode,"", 1, "-- Select Pay Mode --", "", "","" ); ?></td>
                    <td>Source</td>
                    <td><? echo create_drop_down( "cbo_source", 150, $source,"", 1, "-- Select Source --", "", "","" ); ?></td>
                    <td>Knit. Dye. Source</td>
                    <td><? echo create_drop_down( "cbo_knitdye_source", 150, $knitting_source,"", 1, "-- Select Source --", "", "load_drop_down( 'requires/service_booking_knit_dye_without_order_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_supplier', 'suplier_td' )","","1,3" ); ?></td>
                </tr>
                <tr>
                <td>Supplier Name</td>
                <td id="suplier_td"><? echo create_drop_down( "cbo_supplier_name", 150, $blank_array,"", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/service_booking_knit_dye_without_order_controller');",0 ); ?> 
                </td> 
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                	<td>Attention</td>   
                    <td colspan="3">
                        <input class="text_boxes" type="text" style="width:400px"  name="txt_attention" id="txt_attention"/>
                        <input type="hidden" class="image_uploader" style="width:162px" value="Lab DIP No" onClick="openmypage( 'requires/service_booking_knit_dye_without_order_controller.php?action=lapdip_no_popup', 'Lapdip No','lapdip')">
                    </td>
                	<td align="center" height="10" colspan="2" >
                		<? 
							include("../../terms_condition/terms_condition.php");
							terms_condition(194,'txt_fabric_booking','../../');
						?>
                	</td>
                </tr>
                <tr>
                	<td align="center" colspan="6" valign="top" id="booking_list_view1"></td>
                </tr>
            </table>
        </fieldset>
        <br/>
        <fieldset style="width:900px;">
        <legend>Service Booking Details</legend>
            <table border="0" width="880" cellpadding="0" cellspacing="2">
                <tr>
                    <td width="120px" class="must_entry_caption">Fabric Source</td>
                    <td width="160px"><? echo create_drop_down( "cbo_fabric_source", 150, $aop_nonor_fabric_source, "",0, "", 1, "load_fabric_dropdown(this.value)","","1" ); ?></td>
                    <td width="120px" class="must_entry_caption">Fabric Description</td>
                    <td width="160px" id="fabric_description_td"><? echo create_drop_down( "cbo_fabric_description", 150, $blank_array, "",1, "-- Select Fabric --", $selected, "","","" ); ?></td>
                    <td width="120px">GSM</td>   
                    <td><input class="text_boxes" type="text" style="width:140px;" name="txt_gsm" id="txt_gsm"/></td>
                </tr>
                <tr>
                    <td>Dia</td>   
                    <td><input class="text_boxes" type="text" style="width:140px;" name="txt_fin_dia" id="txt_fin_dia"/></td>
                    <td>UOM</td>   
                    <td><? echo create_drop_down( "cbo_uom", 150, $unit_of_measurement,"", 1, "-- Select --", $selected, "","","" ); ?></td>
                    <td>Artwork No</td>   
                    <td><input class="text_boxes" type="text" style="width:140px;" name="txt_art_work" id="txt_art_work"/></td>   
                </tr>
                <tr>
                    <td>Gmts. Color</td>   
                    <td id="gmtcolor_td"><? echo create_drop_down( "cbo_gmtcolor", 150, $blank_array,"", 1, "-- Select --", $selected, "",1,"" ); ?></td>
                    <td>WO. Qty.</td>   
                    <td><input class="text_boxes_numeric" type="text" style="width:140px;" onChange="calculate_amount()" name="txt_wo_qty" id="txt_wo_qty"/></td>
                    <td class="must_entry_caption">Rate</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:140px;" onChange="calculate_amount()" name="txt_rate" id="txt_rate"/></td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Amount</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:140px;" name="txt_amount" id="txt_amount" readonly/></td>
                    <td class="must_entry_caption">Delivery Start Date</td>
                    <td><input class="datepicker" type="text" style="width:140px;" name="txt_dev_start_date" id="txt_dev_start_date"/></td>
                    <td class="must_entry_caption">Delivery End Date</td>
                    <td><input class="datepicker" type="text" style="width:140px;" name="txt_dev_end_date" id="txt_dev_end_date"/></td>   
                </tr>
                <tr>
                    <td>Remarks</td>   
                    <td><input spellcheck="true"  class="text_boxes" type="text" style="width:140px;" name="txt_remarks" id="txt_remarks"/></td>
                    <td class="must_entry_caption">Process</td>
					<td>
					<?
						$filtered_ids="1,25,26,31,33,35,36,37,60,62,63,64,65,66,67,68,69,70,71,73,82,83,84,85,89,90,91,93,94,129,135,136,145,156";
						echo create_drop_down( "cbo_knitdye_type", 150, $conversion_cost_head_array,"", 1, "--Select Type--", "", "","",$filtered_ids );
					?>
					</td>
                    <td>&nbsp;</td> 
                    <td valign="middle">
                    	<input type="button" class="image_uploader" style="width:145px" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('txt_mst_id').value,document.getElementById('dtls_id').value, 'knit_dye_non_order_booking', 0 ,1)">
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="6" valign="middle" class="button_container">
                        <? echo load_submit_buttons( $permission, "fnc_knit_dye_non_ord_booking", 0,1,"",1);?>
                        <input class="text_boxes" type="hidden" style="width:120px;" name="dtls_id" id="dtls_id"/>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="6" height="10" style="display:none">
                        <div id="pdf_file_name"></div>
                        <input type="button" value="Print Booking" onClick="generate_trim_report('show_trim_booking_report')" style="width:100px" name="print_booking" id="print_booking" class="formbutton" />
                    </td>
                </tr>
            </table>
            <div id="knit_dye_list_view_panel"></div>
            <br/>
            <div style="" id="data_panel"></div>
            <br/>
            <div style="display:none" id="data_panel2"></div> 
        </fieldset>
    </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>