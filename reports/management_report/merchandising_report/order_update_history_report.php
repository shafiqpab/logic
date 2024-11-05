<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Gmts Shipment Schedule Report
				
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
echo load_html_head_contents("Order Info","../../../", 1, 1, $unicode);
?>	

<script>
var permission='<? echo $permission; ?>';

var tableFilters = 
	{
		col_operation: 
		{
			id: ["value_smv_tot","total_order_qnty","total_order_qnty_pcs","value_yarn_req_tot","value_total_order_value","value_total_commission","value_total_net_order_value","total_ex_factory_qnty","total_short_access_qnty","total_over_access_qnty","value_total_short_access_value","value_total_over_access_value"],
			col: [13,14,16,17,19,20,21,22,23,24,25,26],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		} , 
		col_27: "select",
		col_31: "select",
	}	
function generate_report_main(e)
	{
			if (e!=13) {var unicode=e.keyCode? e.keyCode : e.charCode } else {unicode=13;}
			var inn=document.getElementById('fillter_check').value;
			if(inn=='')
			{
				generate_report('report_container2',1)
			}
			if(inn==1)
			{
				show_inner_filter(unicode);
			}
	}
		
function generate_report(div,stype)
	{
		if (form_validation('cbo_company_name*txt_week_from*txt_week_to*txt_date_from*txt_date_to','Comapny Name*Week From*Week To*Month Range From*Month Range To')==false)
		{
			return;
		}
		
		
		document.getElementById(div).innerHTML="";
		
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		
		var txt_week_from=document.getElementById('txt_week_from').value;
		var txt_week_to=document.getElementById('txt_week_to').value;
		
		var txt_date_from=document.getElementById('txt_date_from').value;
		var txt_date_to=document.getElementById('txt_date_to').value;
		
		var cbo_year_selection=document.getElementById('cbo_year_selection').value;
		
		var data=cbo_company_name+"_"+txt_week_from+"_"+txt_week_to+"_"+txt_date_from+"_"+txt_date_to+"_"+cbo_year_selection;
		
		//alert(data);return;
		
			
			if (window.XMLHttpRequest)
			{// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
			}
			else
			{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			
			/*if (txt_date_from==0){
			$("#messagebox").removeClass().addClass('messagebox').text('Please Select From Date....').fadeIn(1000);
			return false; }
			else if (txt_date_to==0){
			$("#messagebox").removeClass().addClass('messagebox').text('Please Select To Date....').fadeIn(1000);
			return false; }*/
		    freeze_window(3);
			xmlhttp.onreadystatechange=function()
			{
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			var response=(xmlhttp.responseText).split('####');	
			document.getElementById(div).innerHTML=response[0];
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 

			append_report_checkbox('table_header_1',1);

			
				 setFilterGrid("table-body",-1,tableFilters);
				 //document.getElementById('content_summary3_panel').innerHTML=document.getElementById('shipment_performance').innerHTML

			// var myColValues=TF_GetColValues("table-body",0);
			release_freezing();
			//percent_set()
			}
			}
			xmlhttp.open("GET","requires/order_update_history_report_controller.php?data="+data+"&type=report_generate",true);
			xmlhttp.send();
	}
	
	




	
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../");  ?>
   <h3 align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3>
       <div id="content_search_panel"> 
       
            <form>
                <fieldset style="width:90%;">
                    <div  style="width:100%" align="center">
                            <table class="rpt_table" width="990" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th colspan="2">Week Range</th>
                                        <th colspan="2">Month Range</th>
                                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" /></th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td>
                                           <?
                                           		echo create_drop_down( "cbo_company_name", 300, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " " );
                                            ?> 
                                    </td>
                                    <td><input name="txt_week_from" id="txt_week_from"  class="text_boxes_numeric" style="width:120px; text-align:center"></td>
                                    <td><input name="txt_week_to" id="txt_week_to"  class="text_boxes_numeric" style="width:120px; text-align:center"></td>
                                    <td><input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:140px; text-align:center"></td>
                                    <td><input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:140px; text-align:center"></td>
                                    <td>
                                    	<input type="button" name="search" id="search" value="Show" onClick="generate_report_main(13)" style="width:100px" class="formbutton" />
                                    	<input name="fillter_check" id="fillter_check" type="hidden" >
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" align="center"><font size="+1"><b>Year :&nbsp;&nbsp;</b></font><? echo load_month_buttons(1); ?></td>
                                </tr>
                            </table>
                    </div>
                </fieldset>
            </form>
        </div>
       <div id="report_container" align="center"></div>
       <div id="report_container2"> 
       
        </div>
    </div>
    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>