<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create PO Wise Invoice Report
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	24/03/2014
Updated by 		: 	Rakib	
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
//----------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Item Ledger","../../", 1, 1, $unicode,1,1); 
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
		var page_link='requires/po_wise_invoice_report_controller.php?action=style_refarence_surch&company='+company+'&buyer='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref;
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
		var page_link='requires/po_wise_invoice_report_controller.php?action=order_surch&company='+company+'&buyer='+buyer+'&txt_order_id_no='+txt_order_id_no+'&txt_order_id='+txt_order_id+'&txt_order='+txt_order;  
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=0,scrolling=0','../')
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
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		} 

		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_buyer_name = $("#cbo_buyer_name").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();	
		var txt_order = $("#txt_order").val();
		var txt_order_id = $("#txt_order_id").val();	
		
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name+"&txt_style_ref="+txt_style_ref+"&txt_style_ref_id="+txt_style_ref_id+"&txt_order="+txt_order+"&txt_order_id="+txt_order_id+"&rpt_type="+operation;
		var data="action=generate_report"+dataString;
		freeze_window(operation);
		http.open("POST","requires/po_wise_invoice_report_controller.php",true);
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
			$('#table_body tr:first').hide(); 
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close(); 
			document.getElementById('scroll_body').style.overflow="auto"; 
			document.getElementById('scroll_body').style.maxHeight="250px";
			$('#table_body tr:first').show();
	}
		
	function reset_field()
	{
		reset_form('stock_ledger_1','report_container2','','','','');
	}

	function openmypage(is_lc,lc_sc_id,lc_sc_no)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/po_wise_invoice_report_controller.php?action=lcsc_popup&is_lc='+is_lc+'&lc_sc_id='+lc_sc_id+'&lc_sc_no='+lc_sc_no, 'LC/SC Details', 'width=400,height=400px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_invoice(order_id,invoice_id)
	{
		page_link='requires/po_wise_invoice_report_controller.php?action=po_id_details&order_id='+order_id+'&invoice_id='+invoice_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'PO Info', 'width=980px,height=350px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			//alert("Jahid");
		}
	}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" >
    <h3 align="left" id="accordion_h1" style="width:900px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div style="width:900px;" align="center" id="content_search_panel">
        <fieldset style="width:900px;">
        <legend>Search Panel</legend> 
                <table class="rpt_table" width="800" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="150" class="must_entry_caption">Company</th>                                
                        <th width="150" >Buyer Name</th>
                        <th width="150">Style Ref NO.</th>
                        <th width="150" >Order No.</th>
                        <th ><input type="reset" name="res" id="res" value="Reset" style="width:90px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                        <?
                        	echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/po_wise_invoice_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                        ?>                          
                    </td>
                    <td id="buyer_td">
                    	<? 
                        	echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                        ?></td>
                    <td align="center">
                        <input style="width:130px;"  name="txt_style_ref" id="txt_style_ref"  ondblclick="openmypage_style()" class="text_boxes" placeholder="Browse or Write"/>   
                        <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>    <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>            
                    </td>
                    
                     <td align="center">
                        <input style="width:130px;"  name="txt_order" id="txt_order"  ondblclick="openmypage_order()"  class="text_boxes" placeholder="Browse or Write"   />   
                        <input type="hidden" name="txt_order_id" id="txt_order_id"/> <input type="hidden" name="txt_order_id_no" id="txt_order_id_no"/>               
                    </td>
                    
                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:90px" class="formbutton" />
                        <input type="button" name="search" id="search" value="Show 2" onClick="generate_report(4)" style="width:90px" class="formbutton" />
                    </td>
                </tr>
                
            </table> 
        </fieldset> 
           
    </div>
    <br /> 
    
        <!-- Result Contain Start----------------------------------->
        <fieldset style="width:1200px;">
        	<div id="report_container" align="center"></div>
            <div id="report_container2"></div> 
        </fieldset>
        <!-- Result Contain END------------------------------------>    
    
    </form>    
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
