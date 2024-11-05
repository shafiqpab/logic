<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Gmts Shipment Schedule Report
				
Functionality	:	
JS Functions	:
Created by		:	Ashraful 
Creation date 	: 	31-12-2014
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
echo load_html_head_contents("Order Info","../../../", 1, 1, $unicode);
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	function generate_report()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job_no*txt_style_ref*txt_po_no*txt_po_id*txt_internal_ref*txt_file_no',"../../../")+'&report_title='+report_title;
		//alert (data); return;
		freeze_window(3);
		http.open("POST","requires/country_wise_shipment_pending_report_controller.php",true);
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
			//setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		//$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		//$('#scroll_body tr:first').show();
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../");  ?>
    <form name="countryshipmentpending_1" id="countryshipmentpending_1" autocomplete="off" > 
        <h3 style="width:900px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
        <fieldset style="width:900px" >
            <table class="rpt_table" width="900" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Year</th>
                    <th>Job No</th>
                    <th>Internal Ref.</th>
                    <th>File No</th>
                    <th>Style Name</th>
                    <th>Po Number</th>
                    <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" /></th>
                </thead>
                <tr class="general">
                    <td>
                        <?
                            echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/country_wise_shipment_pending_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td');");
                        ?>                                     
                    </td>
                    <td id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                        ?>	
                    </td>
                    <td>
                        <?
                            echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                        ?>
                    </td>
                    <td>
                        <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" />
                    </td>
                     <td>
                        <input type="text" name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px" />
                    </td>
                     <td>
                        <input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px" />
                    </td>
                    <td>
                        <input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:80px" />
                    </td>
                    <td>
                        <input type="text" name="txt_po_no" id="txt_po_no" class="text_boxes" style="width:80px" />
                        <input type="hidden" name="txt_po_id" id="txt_po_id" class="text_boxes" style="width:50px" />
                    </td>
                    <td>
                        <input type="button" name="show" id="show" onClick="generate_report();" class="formbutton" style="width:70px" value="Show" />
                    </td>
                </tr>
            </table>
        </fieldset>
        </div>
    </form>
	<div id="report_container" align="center"></div>
    <div id="report_container2"></div>      
    </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>