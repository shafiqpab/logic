<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Knitting Production Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	12-03-2016
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
echo load_html_head_contents("Batch Plan And Dyeing Status Report", "../../", 1, 1,'',1,1);

?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';

function fn_report_generated()
{
	
	var order_no=$('#txt_order_no').val();
	var job_no=$('#txt_job_no').val();
	if(order_no!="" || job_no!="")
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
	}
	else
	{
		if (form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*From Date*To Date')==false)
		{
			return;
		}
	}
	
	var report_title=$( "div.form_caption").html();
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*txt_order_no*txt_job_no*cbo_year',"../../")+'&report_title='+report_title;
	//alert(data);
	freeze_window(3);
	http.open("POST","requires/batch_plan_dyeing_status_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse;
}

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		var response=trim(http.responseText).split("####");
		document.getElementById('report_container2').innerHTML=http.responseText;
		document.getElementById('report_container').innerHTML=report_convert_button('../../');
			
		/*$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>'; */
		
		/*var tableFilters = {
								col_operation: {
									id: ["value_tot_bom","value_tot_booking","value_tot_aShift","value_tot_bShift","value_tot_cShift","value_tot_dayTot_qty","value_tot_cumTot_qty","value_tot_balance","value_tot_delivery"],
								   col: [10,13,14,15,16,17,18,19,20],
								   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
								   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
								}
							}*/
		setFilterGrid("table_body",-1);
		
		show_msg('3');
		release_freezing();
 	}
	
}

/*function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	
	$('#table_body tr:first').hide();
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 

	$('#table_body tr:first').show();
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="380px";
}*/

</script>

</head>
 
<body onLoad="set_hotkey();">
<? echo load_freeze_divs ("../../",'');  ?>
<form id="batchPlanReport_1">
    <div style="width:100%;" align="center">    
         <h3 style="width:900px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:900px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer</th>
                     <th>Year</th> 
                     <th>Job No</th>
                    <th>Order No</th>
                    <th class="must_entry_caption">Shipment Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('batchPlanReport_1','report_container*report_container2','','','')" class="formbutton" style="width:80px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "- Select Company -", $selected, "load_drop_down( 'requires/batch_plan_dyeing_status_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "- All Buyer -", $selected, "",0,"" );
                            ?>
                        </td>
                       <td id="">
                            <?
                              echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>
                        </td>
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px" /></td>
                        <td><input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:100px" /></td>
                         <td>
                         <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" > &nbsp;To  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date" >
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated()" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <table>
            	<tr>
                	<td>
 						<? echo load_month_buttons(1); ?>
                   	</td>
                </tr>
            </table> 
            <br />
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
 </form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>
