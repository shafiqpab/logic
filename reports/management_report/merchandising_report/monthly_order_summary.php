<?
/*-------------------------------------------- Comments
Version                  :   V1
Purpose			         : 	This form will create  Monthly Capacity and order qty Report
Functionality	         :
JS Functions	         :
Created by		         :	Md. Saidul Islam Reza
Creation date 	         :  13 June,2021
Requirment Client        :
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         : 
						   
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Order Info","../../../", 1, 1, $unicode,1,1);
//--------------------------------------------------------------------------------------------------------------------
?>
<script>
var permission='<? echo $permission; ?>';

function fn_report_generate(type)
{

		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false)
		{
			return;
		}
		else
		{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_order_status*cbo_buyer_name*cbo_product_category*cbo_date_type*txt_date_from*txt_date_to',"../../../")+'&report_title='+$( "div.form_caption" ).html()+'&type='+type;
			freeze_window(3);
			http.open("POST","requires/monthly_order_summary_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generate_reponse;
		}
	}

	function fn_report_generate_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	}

 



	function new_window()
	{
		//document.getElementById('scroll_body').style.overflow='auto';
		//document.getElementById('scroll_body').style.maxHeight='none';
		//$("#table_body tr:first").hide();
		//$("#table_body1 tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
	
		//document.getElementById('scroll_body').style.overflowY='scroll';
		//document.getElementById('scroll_body').style.maxHeight='300px';
		//$("#table_body tr:first").show();
	}



</script>




</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../");  ?>
        <h3 align="left" id="accordion_h1" class="accordion_h" style="width:900px;" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <form id="monthly_capacity_order_qnty" name="monthly_capacity_order_qnty">
                <div style="width:900px">
                    <fieldset>
                    <legend>Select Search Data for Generate Report</legend>
                        <table cellpadding="0" cellspacing="2" width="100%" class="rpt_table" border="1" rules="all">
                          <thead>
                            <tr>
                                <th class="must_entry_caption">Company</th>
                                <th>Order Status</th>
                                <th>Buyer</th>
                                <th>Product Category</th>
                                <th>Date Type</th>
                                <th colspan="2" id="date_type_filed_td">Date Range</th>
                                <th>
                                 <input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('monthly_capacity_order_qnty','report_container*report_container2','','','');" /></th>
                            </tr>
                            <tr class="general">
                                <td>
                                <?
                                	echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select --", $selected,"load_drop_down( 'requires/monthly_order_summary_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );set_multiselect('cbo_buyer_name','0','0','','0','');" );
                                ?>
                                </td>
                                <td>
                                <?
                                 	echo create_drop_down( "cbo_order_status", 80, $order_status,"", 1, "All", 1, ""); ?>

                                </td>
                                <td id="buyer_td">
                                    <? 
                                        echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select --", $selected, "" );
                                    ?>	
                                </td>
                                
								<td> 
									<? 
										echo create_drop_down( "cbo_product_category", 120, $product_category,"", 1, "-- Select --", $selected, ""  );
									?>	
								</td>
                                
								<td> 
									<? 
										echo create_drop_down( "cbo_date_type", 100, array(1=>"Country Ship Date"),"", 0, "-- Select --", $selected, "$('#date_type_filed_td').text(this.options[this.selectedIndex].text);$('#date_type_filed_td').css('color', 'rgb(64,108,240)');"  );
									?>	
								</td>
                                
                                
                                <td><input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:80px" placeholder="From Date"></td>
                        		<td><input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:80px" placeholder="To Date"></td>
                                <td>
                                	<input type="button" name="search" value="Show" onClick="fn_report_generate(1)" style="width:80px" class="formbutton" />
                                    </td>

                            </tr>
							<tr>
                    		<td colspan="9" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                  		  </tr>
                           </thead>
                        </table>
                    </fieldset>
                </div>
            </form>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div>
    </div>
</body>


<script type="text/javascript">
	set_multiselect('cbo_buyer_name*cbo_product_category','0','0','','0','');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>


</html>