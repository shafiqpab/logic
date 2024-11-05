<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Dyeing Requirement Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	19-04-2015
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
echo load_html_head_contents("Dyeing Requirment Report", "../../", 1, 1,'',1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';

	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_name*cbo_buyer_name','Comapny Name*Buyer Name')==false)
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_season*txt_gsm*txt_gsm_to*txt_date_from*txt_date_to*txt_order*txt_color',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/dyeing_requirment_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			//setFilterGrid("tbl_list_search",-1);
			//append_report_checkbox('table_header_1',1);
			// $("input:checkbox").hide();
			show_msg('3');
			release_freezing();
		}
	}
	
	function openmypage_order()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var page_link='requires/dyeing_requirment_report_controller.php?action=order_no_search_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		var title='Order No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;
			$('#txt_order_no').val(order_no);
			$('#txt_order').val(order_id);	 
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}
	
	function js_set_value( str ) {
	toggle( document.getElementById( 'tr_' + str), '#FFF' );
	}
</script>
</head>
<body onLoad="set_hotkey();">
<form id="dyeingReqReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:950px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:950px;">
            <table class="rpt_table" width="950" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th class="must_entry_caption">Buyer Name</th>
                    <th>Season</th>
                    <th>Order No</th>
                    <th>Color</th>
                    <th>GSM</th>
                    <th>Booking Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dyeingReqReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
							<?
                            	echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "- Select Company -", $selected, "load_drop_down( 'requires/dyeing_requirment_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
							<? 
                            	echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "- All Buyer -", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                        	<input type="text" id="txt_season" name="txt_season" class="text_boxes" style="width:80px"  placeholder="Write Season" />
                        </td>
                        <td>
                       		<input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:80px" onDblClick="openmypage_order()"  placeholder="Browse Order" />
                        	<input type="hidden" id="txt_order" name="txt_order" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
                        	<input type="text" id="txt_color" name="txt_color" class="text_boxes" style="width:80px"  placeholder="Write Color" />
                        </td>     
                        <td>
                            <input type="text" name="txt_gsm" id="txt_gsm" class="text_boxes" style="width:60px" placeholder="Wrt. Gsm" >
                            To
                            <input type="text" name="txt_gsm_to" id="txt_gsm_to" class="text_boxes" style="width:60px" placeholder="Wrt. Gsm" >
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px" placeholder="From Date"/>
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px" placeholder="To Date"/>
                        </td>
                        <td>
                        	<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="8" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
