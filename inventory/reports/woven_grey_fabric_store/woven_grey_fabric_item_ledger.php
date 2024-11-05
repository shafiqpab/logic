<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Woven Grey Fabric Item Ledger Report
				
Functionality	:	
JS Functions	:
Created by		:	Md. Helal Uddin
Creation date 	: 	29-08-2022
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
echo load_html_head_contents("Woven Grey Fabric Item Ledger Report","../../../", 1, 1, $unicode,1,1); 
?>	
	<script>
		var permission='<? echo $permission; ?>';
		if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

		function openmypage_item()
		{
			if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
			var company = $("#cbo_company_name").val();	
			var page_link='requires/woven_grey_fabric_item_ledger_controller.php?action=item_description_search&company='+company; 
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

		function generate_report(rpt_type)
		{
			
			var cbo_company_name = $("#cbo_company_name").val();
			var txt_product_id = $("#txt_product_id").val();
			var cbo_method = $("#cbo_method").val();
			var from_date = $("#txt_date_from").val();
			var to_date = $("#txt_date_to").val();	
			var txt_po_no = $("#txt_po_no").val();	
			var report_title=$( "div.form_caption" ).html();	

			if(txt_po_no!='' && rpt_type==2){
				var fielText="cbo_company_name";	
				var msgText="Company Name";	
			}
			else
			{
				var fielText="cbo_company_name*txt_product";	
				var msgText="Company Name*Item Description";	
			}
			if( form_validation(fielText,msgText)==false )
			{
				return;
			} 
			
			var dataString = "&cbo_company_name="+cbo_company_name+"&txt_product_id="+txt_product_id+"&cbo_method="+cbo_method+"&from_date="+from_date+"&to_date="+to_date+"&rpt_type="+rpt_type+"&txt_po_no="+txt_po_no+"&report_title="+report_title;
			var data="action=generate_report"+dataString;
			freeze_window(3);
			http.open("POST","requires/woven_grey_fabric_item_ledger_controller.php",true);
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
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
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
		
		function openmypage(trans_id,prod_id,trans_type,action)
		{
			//alert (production_date);
			var width_pop=950;
			var page_title="";
			page_title='Transaction Details';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_grey_fabric_item_ledger_controller.php?trans_id='+trans_id+'&prod_id='+prod_id+'&trans_type='+trans_type+'&action='+action, page_title, 'width='+width_pop+'px,height=400px,center=1,resize=0,scrolling=0','../../');
		}
	</script>
	</head>
	<body onLoad="set_hotkey()">
		<div style="width:100%;" align="center">
			<? echo load_freeze_divs ("../../../",$permission);  ?>   		 
		    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" > 
		    <h3 style="width:1000px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
		    <div id="content_search_panel" style="width:100%;" align="center">
		        <fieldset style="width:1000px;">
					<table class="rpt_table" cellpadding="0" cellspacing="0">
		                <thead>
		                    <tr>
		                        <th width="150" class="must_entry_caption">Company</th>
		                        <th width="150" class="must_entry_caption">Item Description</th>
		                        <th width="110">Po Number</th>
		                        <th width="130">Method</th>
		                        <th width="200">Date</th>
		                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_field()" /></th>
		                    </tr>
		                </thead>
		                <tr class="general" align="center">
		                    <td>
		                            <? 
		                               echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
		                            ?>                            
		                    </td>
		                    <td align="center">
		                        <input style="width:140px;"  name="txt_product" id="txt_product"  ondblclick="openmypage_item()"  class="text_boxes" placeholder="Dubble Click For Item"  readonly />   
		                        <input type="hidden" name="txt_product_id" id="txt_product_id"/>   <input type="hidden" name="txt_product_no" id="txt_product_no"/>             
		                    </td>
		                    <td align="center">
		                        <input style="width:100px;"  name="txt_po_no" id="txt_po_no" class="text_boxes" placeholder="PO Number"  />   
		                    </td>
		                    <td align="center">
								<?   
		                            echo create_drop_down( "cbo_method", 130, $store_method,"", 1, "Weighted Average", $selected, "", "","");
		                        ?>
		                    </td>
		                    <td align="center">
		                         <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:70px"/>                    							
		                         To
		                         <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:70px"/>                                                        
		                    </td>
		                    <td>
		                        <input type="button" name="search" id="search" value="Item Wise" onClick="generate_report(1)" style="width:100px" class="formbutton" />
		                        <input type="button" name="search" id="search" value="Order Wise" onClick="generate_report(2)" style="width:100px" class="formbutton" />
		                    </td>
		                </tr>
		                <tr>
		                    <td colspan="6" align="center"><? echo load_month_buttons(1);  ?></td>
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
