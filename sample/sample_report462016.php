<?
/*-------------------------------------------- Comments
Purpose			: 	
				
Functionality	:	
JS Functions	:
Created by		:	
Creation date 	: 	02-04-2016
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
echo load_html_head_contents("Sample Report","../", 1, 1, $unicode,1,1); 
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
		var page_link='requires/sample_report_controller.php?action=item_description_search&company='+company; 
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=640px,height=370px,center=1,resize=0,scrolling=0','../')
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
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		} 
		var cbo_company_name = $("#cbo_company_name").val();
		var txt_product_id = $("#txt_product_id").val();
		var cbo_supplier = $("#cbo_supplier").val();
		var txt_lot_no = $("#txt_lot_no").val();	
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		var title=$("div.form_caption" ).html();
		var dataString = "&cbo_company_name="+cbo_company_name+"&txt_product_id="+txt_product_id+"&cbo_supplier="+cbo_supplier+"&from_date="+from_date+"&to_date="+to_date+"&txt_lot_no="+txt_lot_no+"&title="+title;
		var data="action=generate_report"+dataString;
		freeze_window(operation);
		http.open("POST","requires/sample_report_controller.php",true);
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
		'<html><head><link rel="stylesheet" href="../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close(); 
			document.getElementById('scroll_body').style.overflow="auto"; 
			document.getElementById('scroll_body').style.maxHeight="250px";
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",$permission);  ?>   		 
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" > 
    <h3 style="width:800px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel" style="width:100%;" align="center">
        <fieldset style="width:800px;">
        <legend>Search Panel</legend> 
			<table class="rpt_table" width="800" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="150" class="must_entry_caption">Category</th>                                
                        <th width="140" class="must_entry_caption">Item Description</th>
                        <th width="130">Supplier</th>
                        <th>Date</th>
                        <th width="70"><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr class="general">
                    <td align="center">
                            <? 
							$sample_category=array(1=>"Basic",2=>"Casual Wear",3=>"Dress Up",4=>"Holiday",5=>"Occasion Wear",6=>
                                "Sport Wear",7=>"Work Wear");
                               echo create_drop_down( "cbo_category_name", 140,$sample_category,"", 1, "-- Select Category --", $selected, "load_drop_down( 'requires/sample_report_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );" );
                            ?>                            
                    </td>
                    <td align="center">
                        <input style="width:130px;"  name="txt_product" id="txt_product"  ondblclick="openmypage_item()"  class="text_boxes" placeholder="Dubble Click For Item"  readonly />   
                        <input type="hidden" name="txt_product_id" id="txt_product_id"/>   <input type="hidden" name="txt_product_no" id="txt_product_no"/>             
                    </td>
                    <td align="center" id="supplier_td">
						<?
							$supplier="select id,company_name from lib_company";
                                    echo create_drop_down( "cbo_supplier_source", 100,$supplier,"id,company_name", 1, "-- Select --", "","", "", "");
						?>
                    </td>
                    <td align="center">
                         <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:70px"/>                    							
                         To
                         <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:70px"/>                                                        
                    </td>
                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:70px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                    <td colspan="6"><? echo load_month_buttons(1);  ?></td>
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
<script src="../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
