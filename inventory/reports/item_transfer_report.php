<?
/*-------------------------------------------- Comments
Purpose			: 	This form created for Item Transfer Report
				
Functionality	:	
JS Functions	:
Created by		:	Saidul REZA 
Creation date 	: 	06/09/2015
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
echo load_html_head_contents("Date Wise Item Receive Issue","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	
function reset_field()
{
	reset_form('item_receive_issue_1','report_container2','','','','');
}


function  generate_report(rptType)
{
	var cbo_transfer_type = $("#cbo_transfer_type").val();
	var cbo_company_from = $("#cbo_company_from").val();
	var cbo_company_to = $("#cbo_company_to").val();
	var cbo_item_cat = $("#cbo_item_cat").val();
	var txt_booking_no = $("#txt_booking_no").val();
	var txt_date_from = $("#txt_date_from").val();
	var txt_date_to = $("#txt_date_to").val();

	if(cbo_transfer_type == 2 || cbo_transfer_type == 4)
        {    if( form_validation('cbo_transfer_type*cbo_company_from*cbo_item_cat','Transfer Criteria*From Company*Item Cetagory')==false )
            {
                    return;
            }
        }else{
             if( form_validation('cbo_transfer_type*cbo_company_from*cbo_company_to*cbo_item_cat','Transfer Criteria*From Company*To Company*Item Cetagory')==false )
            {
                    return;
            }
        }
	var dataString = "&cbo_transfer_type="+cbo_transfer_type+"&cbo_company_from="+cbo_company_from+"&cbo_company_to="+cbo_company_to+"&cbo_item_cat="+cbo_item_cat+"&txt_booking_no="+txt_booking_no+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to;
	var data="action=generate_report"+dataString;
	freeze_window(5);
	http.open("POST","requires/item_transfer_report_controller.php",true);
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
		//alert(reponse[2]);
		$("#report_container2").html(reponse[0]);
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		
		//document.getElementById('report_container').innerHTML=report_convert_button('../../');
		//append_report_checkbox('table_header',1);
		
			var tableFilters3 = 
			 {
				col_30: "none",
				col_operation: {
				id: ["value_total_receive_qty","value_total_order_amt","value_total_issue_qty","value_total_amount"],
			   col: [19,20,21,23],
			   operation: ["sum","sum","sum","sum"],
			   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
				}
			 }
			 
			setFilterGrid("table_body",-1,tableFilters3);
		
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
		$('.hide_td').hide();  
		 $('#tbl_headers tr:first').show();
		 $('.hide_td_header').show();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="360px";
 		$('#table_body tr:first').show();
 		$('.hide_td').show();
		 $('#tbl_headers tr:first').hide();
		 $('.hide_td_header').hide(); 
		
	}

	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
   <h3 align="left" id="accordion_h1" style="width:1000px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    
    <div style="width:1000px;" align="center" id="content_search_panel">
        <fieldset style="width:100%;">
                <table class="rpt_table" cellpadding="0" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="must_entry_caption">Transfer Criteria</th>
                        <th class="must_entry_caption">From Company</th>   
                        <th class="must_entry_caption">To Company</th>   
                        <th class="must_entry_caption">Item Category</th>
                        <th>Fab. Booking No</th>
                        <th>Transfer Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr class="general">
                     <td align="center">
						<?   
                        $transferType=array(1=>'From Company To Company',2=>'From Store To Store',4=>'From Order To Order');
                        echo create_drop_down( "cbo_transfer_type", 130, $transferType,"", 0, "--Select--", $selected, "", "","");
                        ?>              
                     </td>
                    <td>
                         <?
                        	echo create_drop_down( "cbo_company_from", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                        ?>                          
                    </td>
                    <td>
                         <?
                        	echo create_drop_down( "cbo_company_to", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                        ?>                          
                    </td>
                    <td>
						<?
                        	//echo create_drop_down( "cbo_item_cat", 120, $item_category,"", 1, "-- Select Item --", $selected, "job_order_per()",0,"1,2,4,13,8,9,10,11,15,16,17,18,19,20,21,22" );
                            echo create_drop_down( "cbo_item_cat", 120, $item_category,"", 1, "-- Select Item --", $selected, "",0,"1,2,4,5,6,7,13,8,9,10,11,15,16,17,18,19,20,21,22,23,32,34,35,36,37,38,39,23,33,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,69,70,89,90,91,92,93,94" ); //job_order_per()
                        ?>
                    </td>
					<td>
						<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px;">
					</td>
                    <td>
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px;" placeholder="From Date" readonly/>&nbsp; TO &nbsp;
                    	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:100px;" readonly/>
                    </td>
                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                	<td colspan="13" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                </tr>
                
            </table> 
        </fieldset> 
           
    </div>
        <!-- Result Contain Start-------------------------------------------------------------------->
        	<div id="report_container" align="center"></div>
            <div id="report_container2"></div> 
        <!-- Result Contain END-------------------------------------------------------------------->
    
    
    </form>    
</div>    
</body>
<!--<script>
	set_multiselect('cbo_source','0','0','','0');
</script>  
-->
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
