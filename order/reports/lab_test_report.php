<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Lab test Report.
Functionality	:	
JS Functions	:
Created by		:	Safa
Creation date 	: 	12-04-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
CRM ID			:   7878
Update CRM ID	:
*/

session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Lab Test Report","../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	var tableFilters = {
						col_operation: {
						id: ["total_wo_fin_qty","total_wo_grey_qty"],
						col: [9,10],
						operation: ["sum","sum"],
						write_method: ["innerHTML","innerHTML"]
						}
					}
		
    function generate_report(operation)
    {
        if(form_validation('cbo_company_name','Company Name')==false)
        {
            release_freezing();
            return;
        }
        else
        {	
            var report_title=$( "div.form_caption" ).html();
            var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_style_no*txt_wo_no*cbo_supplier*txt_date_from*txt_date_to*cbo_year_selection',"../../")+'&report_title='+report_title;
            //alert(data);return;
            freeze_window(3);
            http.open("POST","requires/lab_test_report_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fn_report_generated_reponse;
        }
    }

    function fn_report_generated_reponse()
    {
        if(http.readyState == 4)
        {
            var response=trim(http.responseText).split("****");
            $('#report_container2').html(response[0]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            setFilterGrid('table_body',-1);
            show_msg('3');
            release_freezing();
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
        '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close();

        $('#table_body tr:first').show();
        document.getElementById('scroll_body').style.overflowY="scroll";
        document.getElementById('scroll_body').style.maxHeight="400px";
    }

	function fn_order_disable(type_id)
	{
		if(type_id==2)
		{
			$('#txt_wo_no').attr("disabled",true);
		}
		else
		{
			$('#txt_wo_no').attr("disabled",false);
		}
	}
	
	
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
        <form id="orderStatusReport" name="orderStatusReport">
			<? echo load_freeze_divs ("../../"); ?>
            <h3 align="left" id="accordion_h1" style="width:1050px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1050px;">
                <table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" align="center" rules="all">
                    <thead>
						<th class="" width="150">Company Name</th>
                        <th width="130">Buyer Name</th>
                        <th width="120">Job NO</th>
                        <th width="120">Style NO</th>
                        <th width="120">WO NO</th>
						<th width="120">Test Company</th>
			
                        <th width="170" class="must_entry_caption">WO Date Range</th>
                       
                        <th width="70"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:70px" onClick="reset_form('orderStatusReport','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tr class="general">
                        <td> 
							<?
                            echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/lab_test_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
			 
                        <td id="buyer_td">
                            <? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?>
                        </td>
                        
                        <td>
                            <input style="width:120px;" name="txt_job_no" id="txt_job_no"  class="text_boxes" placeholder="Write" />   
                        </td>

                        <td>
                            <input style="width:120px;" name="txt_style_no" id="txt_style_no"  class="text_boxes" placeholder="Write" />   
                        </td>

                        <td>
                            <input style="width:120px;" name="txt_wo_no" id="txt_wo_no"  class="text_boxes" placeholder="Write" />   
                        </td>

                        <td>
                            <?=create_drop_down( "cbo_supplier", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=26 order by a.supplier_name","id,supplier_name", 1, "-- Select Test Company--", 0, "","" ); ?>
                        </td>           
					
                        <td>
                            <input type="text" id="txt_date_from" name="txt_date_from" class="datepicker" style="width:60px" readonly>To
                            <input type="text" id="txt_date_to" name="txt_date_to" class="datepicker" style="width:60px" readonly>
                        </td>
                       
                        <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="9" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                    </tr>
                </table>
            </fieldset>
            </div>
        </form>
    </div> 
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>  
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
