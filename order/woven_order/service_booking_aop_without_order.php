<?
/*-------------------------------------------- Comments 
Version          : V1
Purpose			 : This form will create Service Booking aop without order
Functionality	 :	
JS Functions	 :
Created by		 : Ashraful 
Creation date 	 : 17-08-2015
Requirment Client: 
Requirment By    : 
Requirment type  : 
Requirment       : 
Affected page    : 
Affected Code    :              
DB Script        : 
Updated by 		 : 
Update date		 : 
QC Performed BY	 :		
QC Date			 :	
Comments		 : From this version oracle conversion is start
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Service Booking For AOP Without Order", "../../", 1, 1,$unicode,'','');
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
				//reset_form('','booking_list_view','txt_fabric_booking*txt_fabric_booking_id*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_source*txt_booking_no');
				freeze_window(5);
				document.getElementById('txt_fabric_booking_id').value=booking_number[0];
				document.getElementById('txt_fabric_booking').value=booking_number[1];
				get_php_form_data(booking_number[0], "populate_order_data_from_search_popup", "requires/service_booking_aop_without_order_controller" );
				release_freezing();
			}
		}
	}
}

function load_fabric_dropdown(fabric_source)
	{
		load_drop_down( 'requires/service_booking_aop_without_order_controller', document.getElementById('txt_fabric_booking_id').value+"_"+fabric_source, 'load_drop_down_fabric_description', 'fabric_description_td' )
	}
	
function check_exchange_rate()
{
	var cbo_currercy=$('#cbo_currency').val();
	var booking_date = $('#txt_booking_date').val();
	var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/service_booking_aop_without_order_controller');
	var response=response.split("_");
	$('#txt_exchange_rate').val(response[1]);
	
}

function get_related_data(fabric_id)
{
	var cbo_fabric_source=document.getElementById('cbo_fabric_source').value;
	var company_name=document.getElementById('cbo_company_name').value;
	get_php_form_data(fabric_id+"_"+cbo_fabric_source+"_"+company_name, "get_related_data", "requires/service_booking_aop_without_order_controller" );

}
	



function calculate_amount()
{
	var txt_reqwoqnty=(document.getElementById('txt_req_wo_qty').value)*1;    
	var txt_woqnty=(document.getElementById('txt_wo_qty').value)*1;
    if(txt_woqnty>txt_reqwoqnty){
        alert("WO Qnty Can Not Greater Then Required Qty("+txt_reqwoqnty+")");
        $('#txt_wo_qty').val('');
        $('#txt_amount').val('');
        return;
    }
	var txt_rate=(document.getElementById('txt_rate').value)*1;
	var txt_amount=txt_woqnty*txt_rate;
	document.getElementById('txt_amount').value=txt_amount;	

}

function openmypage_booking(page_link,title)
{
	page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name','../../');
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		
		if (theemail.value!="")
		{
			reset_form('servicebooking_1','aop_list_view_panel','','txt_booking_date,<? echo date("d-m-Y"); ?>','','cbo_currency');
		 	get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/service_booking_aop_without_order_controller" );
			get_php_form_data(document.getElementById('txt_fabric_booking_id').value, "populate_order_data_from_search_popup", "requires/service_booking_aop_without_order_controller" );
		    show_list_view(theemail.value+"_"+document.getElementById('txt_fabric_booking_id').value+"_"+document.getElementById('cbo_fabric_source').value, 'aop_detls_list_view','aop_list_view_panel','requires/service_booking_aop_without_order_controller','');
	   		set_button_status(0, permission, 'fnc_aop_non_ord_booking',1);
		}
	}
}





function open_terms_condition_popup(page_link,title)
{
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	if (txt_booking_no=="")
	{
		alert("Save The Booking First")
		return;
	}	
	else
	{
	    page_link=page_link+get_submitted_data_string('txt_booking_no','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=470px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
}


function fnc_aop_non_ord_booking( operation )
{
	var data_all="";
	if (form_validation('cbo_company_name*txt_fabric_booking*txt_booking_date*cbo_fabric_description*txt_dev_start_date*txt_dev_end_date*cbo_printing_color*cbo_pay_mode','Company Name*Fab.Booking No*Booking Date*Fabric Description*Delivery Start Date*Delivery End Date*Print Color*Pay Mode')==false)
	{
		return;
	}
	else
	{
		data_all=data_all+get_submitted_data_string('txt_booking_no*mst_id*cbo_company_name*txt_fabric_booking*txt_fabric_booking_id*cbo_buyer_name*txt_booking_date*cbo_currency*txt_exchange_rate*cbo_pay_mode*cbo_source*cbo_aop_source*cbo_supplier_name*txt_attention*txt_delivery_to*txt_tenor*cbo_fabric_source*cbo_fabric_description*txt_gsm*txt_fin_dia*cbo_uom*txt_art_work*txt_wo_qty*txt_rate*txt_amount*txt_dev_start_date*txt_dev_end_date*aop_type*aop_mc_type*txt_remarks*cbo_printing_color*dtls_id',"../../");
	}
	
	var data="action=save_update_delete&operation="+operation+data_all;
	freeze_window(operation);
	http.open("POST","requires/service_booking_aop_without_order_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_aop_non_ord_booking_reponse;
}
	 
function fnc_aop_non_ord_booking_reponse()
{
	if(http.readyState == 4) 
	{
		 var reponse=trim(http.responseText).split('**');
		 if(trim(reponse[0])=='approved'){
            alert("This booking is approved");
            release_freezing();
            return;
        }
        
        if(trim(reponse[0])=='pi1'){
            alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
            release_freezing();
            return;
        }
        
        if(trim(reponse[0])=='rec1'){
            alert("Receive  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
            release_freezing();
            return;
        }
        
        if(trim(reponse[0])=='iss1'){
            alert("Issue found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
            release_freezing();
            return;
        }
		if(trim(reponse[0])=='issueFinProcess'){
			alert("Fabric Issue to Fin. Process Found :"+trim(reponse[2])+"\n")
		    release_freezing();
		    return;
		}
		
		 show_msg(trim(reponse[0]));
		 if(reponse[0]==0 || reponse[0]==1)
		 {
			document.getElementById('txt_booking_no').value=reponse[1];
			document.getElementById('mst_id').value=reponse[2];
		 	set_button_status(0, permission, 'fnc_aop_non_ord_booking',1);
			show_list_view(reponse[2]+"_"+document.getElementById('txt_fabric_booking_id').value+"_"+document.getElementById('cbo_fabric_source').value, 'aop_detls_list_view','aop_list_view_panel','requires/service_booking_aop_without_order_controller','');
			reset_form('','','cbo_fabric_source*cbo_fabric_description*txt_gsm*txt_fin_dia*aop_type*aop_mc_type*cbo_uom*txt_art_work*txt_wo_qty*txt_rate*txt_amount*txt_dev_start_date*txt_dev_end_date*txt_remarks*dtls_id','txt_booking_date,<? echo date("d-m-Y"); ?>');
		 }
		 if(reponse[0]==2)
		 {
			location.reload();
		 }
		 release_freezing();
	}
}
 

function get_dtls_data(id)
{
	get_php_form_data( id, "populate_data_dtls_from_search_popup", "requires/service_booking_aop_without_order_controller" );
	set_button_status(1, permission, 'fnc_aop_non_ord_booking',1);
}
	
function generate_trim_report(action)
{
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		$report_title=$( "div.form_caption" ).html();
		var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_fabric_booking_id*mst_id*cbo_supplier_name*cbo_pay_mode',"../../")+'&report_title='+$report_title;
		http.open("POST","requires/service_booking_aop_without_order_controller.php",true);
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

</script>
 
</head>
 
<body onLoad="set_hotkey();check_exchange_rate();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="servicebooking_1"  autocomplete="off" id="servicebooking_1">
        <fieldset style="width:1000px;">
            <legend>Service Booking</legend>
            <table width="1000" cellspacing="2" cellpadding="0" border="1">
                <tr>
                <td colspan="4" align="right" class="must_entry_caption"> WO No </td>
                <td colspan="4">
                    <input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/service_booking_aop_without_order_controller.php?action=aop_without_order_booking_search','AOP Without Order Booking Search');" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
                    <input class="text_boxes" type="hidden" style="width:160px"  readonly  name="mst_id" id="mst_id" />
                </td>
                </tr>
                <tr>
                    <td width="110" class="must_entry_caption">Company Name</td>
                    <td width="140">
                    <?  $date=date('d-m-Y');
                    echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "reset_form('servicebooking_1','aop_list_view_panel','','txt_booking_date,".$date."','','cbo_company_name*cbo_currency');load_drop_down( 'requires/service_booking_aop_without_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' )","","" );
                    ?>	  
                    </td>
                    <td width="110" class="must_entry_caption">Fab.Booking No</td>   
                    <td width="140">
                        <input class="text_boxes" type="text" style="width:120px;" placeholder="Double click for Booking" onDblClick="openmypage_order('requires/service_booking_aop_without_order_controller.php?action=order_search_popup','Order Search');" name="txt_fabric_booking" id="txt_fabric_booking"/>
                        <input class="text_boxes" type="hidden" style="width:72px;"  name="txt_fabric_booking_id" id="txt_fabric_booking_id"/>
                    </td>   
                    <td width="110">Buyer Name</td>   
                    <td width="140" id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td>
                    <td width="110" class="must_entry_caption">Booking Date</td>
                    <td><input class="datepicker" type="text" style="width:120px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<?=date("d-m-Y"); ?>" disabled /></td> 
                </tr>
                <tr>
                    <td>Currency</td>
                    <td><?=create_drop_down( "cbo_currency", 130, $currency,"", 1, "-- Select --", 2, "check_exchange_rate()",0 ); ?></td>	
                    <td>Exchange Rate</td>
                    <td><input style="width:120px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly  /></td>
                    <td class="must_entry_caption">Pay Mode</td>
                    <td><?=create_drop_down( "cbo_pay_mode", 130, $pay_mode,"", 1, "-- Select Pay Mode --", "", "load_drop_down( 'requires/service_booking_aop_without_order_controller',this.value, 'load_drop_down_supplier', 'supplier_td' )","" ); ?></td>
                    <td>Source</td>              <!-- 11-00030  -->
                    <td><?=create_drop_down( "cbo_source", 130, $source,"", 1, "-- Select Source --", "", "","" ); ?></td>
                </tr>
                <tr>   
                    <td>Aop Source</td>              <!-- 11-00030  -->
                    <td><?=create_drop_down( "cbo_aop_source", 130, $knitting_source,"", 1, "-- Select Source --", "", "","","1,3" ); ?></td>
                    <td>Tenor</td>
                    <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
                    <td>Supplier Name</td>
                    <td id="supplier_td"><?=create_drop_down( "cbo_supplier_name", 130, $blank_array,"", 1, "-- Select Supplier --", $selected, "",0 ); ?></td>
                    <td>&nbsp;</td>
                    <td>
                    <?
                    include("../../terms_condition/terms_condition.php");
                    terms_condition(177,'txt_booking_no','../../');
                    ?>
                    </td>
                </tr>
                <tr> 
                    <td>Attention</td>   
                    <td colspan="3">
                        <input class="text_boxes" type="text" style="width:370px;"  name="txt_attention" id="txt_attention"/>
                        <input type="hidden" class="image_uploader" style="width:162px" value="Lab DIP No" onClick="openmypage('requires/service_booking_aop_without_order_controller.php?action=lapdip_no_popup','Lapdip No','lapdip')">
                    </td> 
                    <td>Delivery To</td>
                        <td ><input style="width:120px;" type="text" class="text_boxes" name="txt_delivery_to" id="txt_delivery_to" />
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>  
                </tr>
                <tr>
                	<td align="center" colspan="8" valign="top" id="booking_list_view1"></td>
                </tr>
            </table>
        </fieldset>
        <br/>
        <fieldset style="width:1000px;">
            <legend>Service Booking</legend>
            <table   border="0" width="1000" cellpadding="0" cellspacing="2">
                <tr>
                    <td width="110" class="must_entry_caption">Fabric Source</td>
                    <td width="140"><?=create_drop_down( "cbo_fabric_source", 130, $aop_nonor_fabric_source, "",0, "", 1, "load_fabric_dropdown(this.value)","","" ); ?></td>
                    <td width="110" class="must_entry_caption">Fabric Description</td>
                    <td width="140" id="fabric_description_td"><?=create_drop_down( "cbo_fabric_description", 130, $blank_array, "",1, "-- Select Fabric --", $selected, "","","" ); ?></td>
                    <td width="110">AOP.GSM</td>   
                    <td width="140"><input class="text_boxes" type="text" style="width:120px;"   name="txt_gsm" id="txt_gsm"/></td>
                    <td width="110">AOP.Dia</td>   
                    <td><input class="text_boxes" type="text" style="width:120px;"   name="txt_fin_dia" id="txt_fin_dia"/></td>
                </tr>
                <tr>
                    <td>UOM</td>   
                    <td><?=create_drop_down( "cbo_uom", 130, $unit_of_measurement,"", 1, "-- Select --", $selected, "","","" );?></td>
                    <td>Artwork No</td>   
                    <td><input class="text_boxes" type="text" style="width:120px;"   name="txt_art_work" id="txt_art_work"/></td>   
                    <td>Gmts. Color</td>   
                    <td id="gmtcolor_td"><?=create_drop_down( "cbo_gmtcolor", 130, $blank_array,"", 1, "-- Select --", $selected, "",1,"" ); ?></td>
                    <td>WO. Qnty</td>   
                    <td> 
                        <input class="text_boxes_numeric" type="text" style="width:120px;" onChange="calculate_amount()"   name="txt_wo_qty" id="txt_wo_qty"/>
                        <input class="text_boxes_numeric" type="hidden"  id="txt_req_wo_qty"/>
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Aop Rate</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:120px;"  onChange="calculate_amount();" name="txt_rate" id="txt_rate"/></td>
                    <td class="must_entry_caption">Amount</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:120px;"   name="txt_amount" id="txt_amount" readonly/></td>
                    <td class="must_entry_caption">Delivery Start Date</td>
                    <td><input class="datepicker" type="text" style="width:120px;" name="txt_dev_start_date" id="txt_dev_start_date"/></td>
                    <td class="must_entry_caption">Delivery End Date</td>
                    <td><input class="datepicker" type="text" style="width:120px;" name="txt_dev_end_date" id="txt_dev_end_date"/></td>   
                </tr>
                <tr>
                	<td class="must_entry_caption">Printing Color</td>   
                    <td id="printcolor_td"><?= create_drop_down( "cbo_printing_color", 130, $blank_array,"", 1, "-- Select --", $selected, "","","" ); ?></td>
                    <td>Remarks</td>   
                    <td><input spellcheck="true"  class="text_boxes" type="text" style="width:120px;"   name="txt_remarks" id="txt_remarks"/></td>
                    <td>&nbsp;</td> 
                    <td><input type="button" class="image_uploader" style="width:130px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('mst_id').value,document.getElementById('dtls_id').value, 'aop_non_order_booking', 0 ,1)"></td>
                </tr>
                <tr>
                	<td>Aop Type</td>
                    <td><? echo create_drop_down("aop_type", 130, $print_type,"", 1, "--Select--","","",0); ?></td>
                    <td>Aop M/C Type</td>
                    <td><? echo create_drop_down("aop_mc_type", 130, $aop_mc_typeArr,"", 1, "--Select--","","",0); ?></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" colspan="8" valign="middle" class="button_container">
						<?=load_submit_buttons( $permission, "fnc_aop_non_ord_booking", 0,0 ,"",1) ; ?>
                        <input class="text_boxes" type="hidden" style="width:160px;"   name="dtls_id" id="dtls_id"/>
                    </td>
                </tr>
                <tr>
                    <td align="right" colspan="5">
                        <div id="pdf_file_name"></div>
                        <input type="button" value="Print Booking" onClick="generate_trim_report('show_trim_booking_report')"  style="width:100px" name="print_booking" id="print_booking" class="formbutton" />
                        <input type="button" value="Print Booking 2" onClick="generate_trim_report('show_trim_booking_report_2')"  style="width:100px" name="print_booking_2" id="print_booking_2" class="formbutton" />
                    </td>
                </tr>
            </table>
            <div id="aop_list_view_panel"></div>
            <br/><br/> 
            <div style="" id="data_panel"></div>
            <br/><br/>
            <div style="display:none" id="data_panel2"></div> 
        </fieldset>
    </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>