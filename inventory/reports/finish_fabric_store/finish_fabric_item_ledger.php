﻿<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Dyes Chemical Ledger
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	15-10-2014
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			: 
Comments		: 
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission; 
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Finish Fabric Item Ledger","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function openmypage_item()
	{
		if( form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var cbo_item_category_id = $("#cbo_item_category_id").val();	
		var page_link='requires/finish_fabric_item_ledger_controller.php?action=item_description_search&company='+company+"&cbo_item_category_id="+cbo_item_category_id; 
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			var prodNo=this.contentDoc.getElementById("txt_selected_no").value; // product Serial No
			$("#txt_product").val(prodDescription);
			$("#txt_product_id").val(prodID);
			$("#txt_product_no").val(prodNo); 
		}
	}

	function generate_report(operation)
	{
		if( form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false )
		{
			return;
		} 
		var cbo_company_name = $("#cbo_company_name").val();
		var txt_product_id = $("#txt_product_id").val();
		var cbo_method = $("#cbo_method").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		var user_product_id = $("#user_product_id").val();	
		var cbo_item_category_id = $("#cbo_item_category_id").val();	
		var report_title=$( "div.form_caption" ).html();	
		
		var dataString = "&cbo_company_name="+cbo_company_name+"&txt_product_id="+txt_product_id+"&user_product_id="+user_product_id+"&cbo_method="+cbo_method+"&from_date="+from_date+"&to_date="+to_date+"&report_title="+report_title+"&cbo_item_category_id="+cbo_item_category_id;
		
		var data="action=generate_report"+dataString;
		freeze_window(operation);
		http.open("POST","requires/finish_fabric_item_ledger_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		 
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none"; 
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close(); 
			document.getElementById('scroll_body').style.overflow="auto"; 
			document.getElementById('scroll_body').style.maxHeight="250px";
	}

	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission);  ?>   		 
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" > 
    <h3 style="width:850px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel" style="width:100%;" align="center">
        <fieldset style="width:850px;">
			<table class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="150" class="must_entry_caption">Company</th>
                        <th width="100" class="must_entry_caption">Item Category</th>
                        <th width="130">Item Description</th>
                        <th width="60">Product Id</th>
                        <th width="110">Method</th>
                        <th width="170">Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('stock_ledger_1','report_container*report_container2','','','')" /></th>
                    </tr>
                </thead>
                <tr class="general" align="center">
                    <td>
                            <? 
                               echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>                            
                    </td>
                    <td>
						<? 
                            echo create_drop_down( "cbo_item_category_id", 100,$item_category,"", 1, "-Category-", 1, "","","2,3","","","");
                        ?> 
                  	</td>
                    <td align="center">
                        <input type="text" style="width:125px;"  name="txt_product" id="txt_product"  ondblclick="openmypage_item()"  class="text_boxes" placeholder="Dubble Click For Item"  readonly />   
                        <input type="hidden" name="txt_product_id" id="txt_product_id"/>   <input type="hidden" name="txt_product_no" id="txt_product_no"/>  
                    </td>
                    <td align="center">
                        <input type="text" name="user_product_id" id="user_product_id" class="text_boxes" style="width:55px;"/>                
                    </td>
                    <td align="center">
						<?   
                            echo create_drop_down( "cbo_method", 110, $store_method,"", 1, "Weighted Average", $selected, "", "","");
                        ?>
                    </td>
                    <td align="center">
                         <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px"/>                    							
                         To
                         <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px"/>                                                        
                    </td>
                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:70px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7"><? echo load_month_buttons(1);  ?></td>
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>