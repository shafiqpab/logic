<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Daily Yarn Issue Report
				
Functionality	:	
JS Functions	:
Created by		:	Ashraful
Creation date 	: 	18-02-2014
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
echo load_html_head_contents("Daily Yarn Issue Report","../../../", 1, 1, $unicode,1,1); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function generate_report()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	
	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_style*txt_style_id*txt_order_no*txt_order_id*txt_item_no*txt_item_id*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
	freeze_window(3);
	http.open("POST","requires/order_wise_trims_issue_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse;  
}

function generate_report_reponse()
{	
	if(http.readyState == 4) 
	{	 
 		var reponse=trim(http.responseText).split("####");
		$("#report_container2").html(reponse[0]);  
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		
		setFilterGrid("table_body",-1);
		
		show_msg('3');
		release_freezing();
	}
} 

function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	$("#table_body tr:first").hide();
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 

	$("#table_body tr:first").show();
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="380px";
}

function openmypage_order()
{
	
	
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var style_id=$('#txt_style_id').val();
	var company = $("#cbo_company_name").val();	
	var buyer=$("#cbo_buyer_name").val();
	var page_link='requires/order_wise_trims_issue_controller.php?action=order_wise_search&company='+company+'&buyer='+buyer+'&style_id='+style_id; 
	var title="Search Order Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=560px,height=370px,center=1,resize=0,scrolling=0','../../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var prodID=this.contentDoc.getElementById("txt_selected_id").value;
		//alert(prodID); // product ID
		var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
		$("#txt_order_no").val(prodDescription);
		$("#txt_order_id").val(prodID); 
	}
}


function openmypage_style()
{
	
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_name").val();	
	var buyer=$("#cbo_buyer_name").val();
	var page_link='requires/order_wise_trims_issue_controller.php?action=style_wise_search&company='+company+'&buyer='+buyer; 
	var title="Search Style Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=370px,center=1,resize=0,scrolling=0','../../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var prodID=this.contentDoc.getElementById("txt_selected_id").value;
		//alert(prodID); // product ID
		var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
		$("#txt_style").val(prodDescription);
		
		$("#txt_style_id").val(prodID); 
		//alert(prodID);
	}
}


function openmypage_item()
{
    var order_entry=$("#txt_order_no").val();
	var orderno = $("#txt_order_id").val();	
	var company_name=$("#cbo_company_name").val();	
	var page_link='requires/order_wise_trims_issue_controller.php?action=item_wise_search&&order_entry='+order_entry+'&orderno='+orderno+'&company_name='+company_name; 
	var title="Search Item Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=0,scrolling=0','../../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var prodID=this.contentDoc.getElementById("txt_selected_id").value;
		var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
		 //alert(prodDescription);
		$("#txt_item_id").val(prodID);
		
		$("#txt_item_no").val(prodDescription); 
	}
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?><br />    		 
    <form name="dailyYarnIssueReport_1" id="dailyYarnIssueReport_1" autocomplete="off"   > 
             <h3 style="width:1110px;  margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div style="width:1120px;" align="center" id="content_search_panel">
            <fieldset >
            
                <table class="rpt_table" width="1100" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="170" class="must_entry_caption">Company</th>                                
                            <th width="120">Buyer</th>
                            <th width="100">Style</th>
                            <th width="140">Order</th>
                            <th width="140">Item</th>
                                <th width="240" >Shipment date range</th>
                            <th width="120"><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('dailyYarnIssueReport_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center">
                        <td>
                            <? 
							$sql="SELECT id, company_name FROM lib_company WHERE status_active =1 AND is_deleted =0 and core_business not in(3) ORDER BY company_name";
                               echo create_drop_down( "cbo_company_name", 150, $sql,"id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_wise_trims_issue_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>                            
                        </td>
                        <td id="buyer_td"> 
                            <?
								echo create_drop_down( "cbo_buyer_name", 140,$blank_array,"", 1, "-- Select Buyer--", $selected, "","","","","","");
                                              ?>
                        </td>
                        <td>
                           <input style="width:120px;" name="txt_style" id="txt_style" class="text_boxes" onDblClick="openmypage_style()" placeholder="Browse" readonly />
                           <input type="hidden" name="txt_style_id" id="txt_style_id"/>
                        </td>
                        <td>
                            <input style="width:140px;" name="txt_order_no" id="txt_order_no" class="text_boxes" onDblClick="openmypage_order()" placeholder="Browse/Write"  />
                            <input type="hidden" name="txt_order_id" id="txt_order_id"/>
                        </td>
                        <td>
                              <input style="width:140px;" name="txt_item_no" id="txt_item_no" class="text_boxes" onDblClick="openmypage_item()" placeholder="Browse" readonly /> 
                              <input type="hidden" name="txt_item_id" id="txt_item_id"/>                   
                        </td>
                        
                        
                         <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px" placeholder="To Date"/>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:100px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table> 
            </fieldset> 
                 
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div>   
       
    </form>    
</div>    
</body>  

<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
