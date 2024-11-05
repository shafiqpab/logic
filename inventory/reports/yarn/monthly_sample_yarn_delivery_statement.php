<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Job/Order Wise Dyed Yarn Report
				
Functionality	:	
JS Functions	:
Created by		:	 
Creation date 	:
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
echo load_html_head_contents("Job/Order Wise Dyed Yarn Report","../../../", 1, 1, $unicode,1,1); 
?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function generate_report(type)
{
	if( form_validation('txt_date_from*txt_date_to','To Date*From Date')==false )
	{
		return;
	}
	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*cbo_order_type',"../../../")+'&report_title='+report_title+'&type='+type;
	//alert (data);
	freeze_window(3);
	http.open("POST","requires/monthly_sample_yarn_delivery_statement_controller.php",true);
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
    '<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 

	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="380px";
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>		 
    <form name="DyedYarnReport_1" id="DyedYarnReport_1" autocomplete="off" > 
    <h3 align="left" id="accordion_h1" style="width:750px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel">
            <fieldset style="width:610px;">
                <table class="rpt_table" width="740" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 
                        	<th>Company Name</th>	
                            <th >Order Type</th>
                            <th>Buyer Name</th>
                            <th colspan="2" class="must_entry_caption">Delivery Date</th>
                            <th colspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('DyedYarnReport_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center">
                        <td>
                            <? 
                                echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/monthly_sample_yarn_delivery_statement_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/monthly_sample_yarn_delivery_statement_controller' );" );
                            ?>                            
                        </td>
                        <td>
                            <?
                            $order_type=array(1=>"With Order",2=>"Without Order");
                            echo create_drop_down( "cbo_order_type", 80, $order_type,"", 1, "ALL", 0, "",0 );
                            ?>
                        </td>
                        <td id="buyer_td">
							<? 
                                echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                             <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" readonly disabled>
                        </td>
                        <td>
                             <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" readonly disabled>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                    <tfoot>
                        <tr align="center">
                            <td colspan="10" align="center"><? echo load_month_buttons(1);  ?></td>
                        </tr>
                    </tfoot>
                </table> 
            </fieldset> 
        </div>
    </form>    
</div> 
<div id="report_container" align="center" style="padding: 10px;"></div>
<div id="report_container2" align="center"></div>
<div style="display:none" id="data_panel"></div>   
</body>
<script>
	set_multiselect('cbo_buyer_name','0','0','','0');
</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
