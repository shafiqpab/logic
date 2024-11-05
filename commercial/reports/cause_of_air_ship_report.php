<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Cause Of Air Shipment
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	06/04/2020
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
echo load_html_head_contents("Cause Of Air Shipment","../../", 1, 1, $unicode,'',''); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function openmypage_style()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		var txt_style_ref_no = $("#txt_style_ref_no").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var page_link='requires/cause_of_air_ship_report_controller.php?action=style_refarence_surch&company='+company+'&buyer='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_style_ref").val(style_des);
			$("#txt_style_ref_id").val(style_id); 
			$("#txt_style_ref_no").val(style_no); 
		}
	}
	
	
	function openmypage_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();	
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_order_id = $("#txt_order_id").val();
		var txt_order = $("#txt_order").val();
		var page_link='requires/cause_of_air_ship_report_controller.php?action=order_surch&company='+company+'&buyer='+buyer+'&txt_order_id_no='+txt_order_id_no+'&txt_order_id='+txt_order_id+'&txt_order='+txt_order;  
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_order").val(style_des);
			$("#txt_order_id").val(style_id); 
			$("#txt_order_id_no").val(style_des_no);
		}
	}

	function generate_report(operation)
	{
		var txt_date_from_ship = $("#txt_date_from_ship").val();
		var txt_date_to_ship = $("#txt_date_to_ship").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var txt_order = $("#txt_order").val();
		var txt_invoice_no = $("#txt_invoice_no").val();
		
		if(txt_date_from_ship=="" && txt_date_to_ship=="" && txt_style_ref=="" && txt_order=="" && txt_invoice_no=="")
		{
			if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_style_ref*txt_style_ref_id*txt_order*txt_order_id*txt_date_from_ship*txt_date_to_ship*txt_invoice_no*txt_date_from*txt_date_to*cbo_date_type',"../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/cause_of_air_ship_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse; 
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert(http.responseText);	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			release_freezing();
			show_msg('3');
			//document.getElementById('report_container').innerHTML=report_convert_button('../../');
		}
	} 

	function new_window()
	{
		 
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			//$('#table_body tr:first').hide(); 
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close(); 
			document.getElementById('scroll_body').style.overflow="auto"; 
			document.getElementById('scroll_body').style.maxHeight="250px";
			//$('#table_body tr:first').show();
	}
	
	
function reset_field()
{
	reset_form('stock_ledger_1','report_container2','','','','');
}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" >
    <h3 align="left" id="accordion_h1" style="width:1150px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div style="width:1150px;" align="center" id="content_search_panel">
        <fieldset style="width:1150px;">
        <legend>Search Panel</legend> 
                <table class="rpt_table" width="1150" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="140" class="must_entry_caption">Company</th>                                
                        <th width="140">Buyer</th>
                        <th width="130">Style</th>
                        <th width="130">Order</th>
                        <th width="160">Ship Date Range</th>
                        <th width="120">Invoice</th>
                        <th width="90">Date type </th>
                        <th width="160" class="must_entry_caption">Invoice Date Range</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
						<?
                        echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cause_of_air_ship_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                        ?>                          
                    </td>
                    <td id="buyer_td">
					<? 
						echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
					?>
                    </td>
                    <td align="center">
                        <input style="width:120px;"  name="txt_style_ref" id="txt_style_ref"  ondblclick="openmypage_style()" class="text_boxes" placeholder="Browse or Write"/>   
                        <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>    <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>            
                    </td>
                    <td align="center">
                        <input style="width:120px;"  name="txt_order" id="txt_order"  ondblclick="openmypage_order()"  class="text_boxes" placeholder="Browse or Write"   />   
                        <input type="hidden" name="txt_order_id" id="txt_order_id"/> <input type="hidden" name="txt_order_id_no" id="txt_order_id_no"/>               
                    </td>
                    <td>
                    <input type="text" name="txt_date_from_ship" id="txt_date_from_ship" class="datepicker" style="width:55px;"/>                    							
                    To
                    <input type="text" name="txt_date_to_ship" id="txt_date_to_ship" class="datepicker" style="width:55px;"/>
                    </td>
                    <td>
                    <input type="text" id="txt_invoice_no" name="txt_invoice_no" style="width:110px;" class="text_boxes" />
                    </td>
                    <td>
					<?
						$date_type_array=array(1=>"Invoice Date",2=>"Ex-factory Date"); 
						echo create_drop_down( "cbo_date_type", 85, $date_type_array,"", 0, "", $selected, "",0,"" );
					?>
                    </td>
                    <td>
                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px;"/>                    							
                    To
                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px;"/>
                    </td>
                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:70px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                    <td colspan="14" align="center"><? echo load_month_buttons(1);  ?></td>
                </tr>
            </table> 
        </fieldset> 
           
    </div>
    <br /> 
    
        <!-- Result Contain Start-------------------------------------------------------------------->
        	<div id="report_container" align="center"></div>
            <div id="report_container2"></div> 
        <!-- Result Contain END-------------------------------------------------------------------->
    
    </form>    
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
