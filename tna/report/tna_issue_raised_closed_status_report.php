<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
 
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//------------------------------------------------------------------------------------

echo load_html_head_contents("TNA Issue Raised & Closed Status Report","../../", 1, 1, $unicode,1,'');

?>
<script>
if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
var permission = '<? echo $permission; ?>';

let fnc_generate_report = (btn) => {
        
		

        if($("#txt_job_no").val() == '' && $("#txt_order_no").val() == ''  && $("#tna_task_id").val() == ''   && $("#cbo_buyer_id").val() == ''){
            if (form_validation('cbo_company_id*txt_date_from*txt_date_to', 'Company Name*Date From*Date To') ==
			false) {return;}
        }
     


        
    
        var data = "action=generate_tna_report" + get_submitted_data_string(
            'cbo_company_id*tna_task_id*cbo_buyer_id*txt_date_from*txt_date_to*txt_job_no*txt_order_no*cbo_date_type*cbo_raised_closed_id', "../../");
        freeze_window(operation);
        http.open("POST", "requires/tna_issue_raised_closed_status_report_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = () =>{
            if (http.readyState == 4) {
                var reponse = http.responseText.split('###');
                //alert(reponse[0]);
                $("#report_container").html(reponse[0]);
				document.getElementById('print_button').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

                setFilterGrid("table_body", -1);
                release_freezing();
            }
        }

	}


function openmypage_task()
{
	// if( form_validation('cbo_company_id','Company Name')==false )
	// {
	// 	return;
	// }
	var company_id = $("#cbo_company_id").val();	
	var tna_task_id = $("#tna_task_id").val();

	var page_link='requires/tna_issue_raised_closed_status_report_controller.php?action=tna_task_list_popup&company_id='+company_id+'&tna_task_id='+tna_task_id;  
	var title="Search Task Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var task_name=this.contentDoc.getElementById("txt_selected_task_name").value; // product ID
		var task_id=this.contentDoc.getElementById("txt_selected_task_id").value; // product Description
		$("#txt_taks_name").val(task_name);
		$("#tna_task_id").val(task_id); 
		
	}
}


    // alert(Dataset);
let new_window = () =>{
		document.getElementById('scroll_body').style.overflow = "auto";
		document.getElementById('scroll_body').style.maxHeight = "none";
		$('#table_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd"><html><head><title></title><link rel="stylesheet" href="../../css/style_print.css" type="text/css"  /></head><body>' +
			document.getElementById('report_container').innerHTML + '</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflowY = "scroll";
		document.getElementById('scroll_body').style.maxHeight = "400px";
		$('#table_body tr:first').show();
	}


	function fn_on_change()
	{
		var cbo_company_id = $("#cbo_company_id").val();
		//load_drop_down( 'requires/shipment_schedule_controller', cbo_company_id, 'load_drop_down_buyer', 'buyer_td' );

		load_drop_down( 'requires/tna_issue_raised_closed_status_report_controller', cbo_company_id, 'load_drop_down_buyer', 'buyer_td' );

	}


</script>

<body onLoad="set_hotkey()">
    <div align="center">
        <? echo load_freeze_divs ("../../");  ?>
        <fieldset style="width:980px;">
            <table width="100%" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th class="must_entry_caption"> Company Name</th>
                        <th class="" id="buyer_caption">Buyer Name</th>
                        <th>Task</th>
                        <th>Job No</th>
                        <th>Order No</th>
                        <th>Issue Status</th>
                        <th>Date Category</th>
                        <th colspan="2" width="70" id="from_date_html">Ship Date</th>
                        <th><input type="button" class="formbutton" style="width:70px;" value="Reset" onClick="" /></th>
                    </tr>
                </thead>
                <tr class="general">
                    <td align="center">
                        <?
					   		echo create_drop_down( "cbo_company_id", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 0, "-- Select  --", $selected, " " );
						?>
                    </td>

                    <td id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                         ?>
                    </td>
                    <td align="center">
                        <input style="width:100px;" name="txt_taks_name" id="txt_taks_name"
                            ondblclick="openmypage_task()" class="text_boxes" placeholder="Browse" readonly />
                        <input type="hidden" name="tna_task_id" id="tna_task_id" />
                    </td>
                    <td align="center">
                        <input type="text" name="txt_job_no" id="txt_job_no" autocomplete="off" class="text_boxes"
                            style="width:80px" />
                    </td>
                    <td align="center">
                        <input type="text" name="txt_order_no" id="txt_order_no" autocomplete="off" class="text_boxes"
                            style="width:80px">
                    </td>
                    <td align="center">
                        <?
                    	$issue_raised_closed_arr = array(0=>"ALL",1=>"Raised",2=>"Closed");
                        echo create_drop_down( "cbo_raised_closed_id", 80, $issue_raised_closed_arr,"", 0, "-- Select --", $selected, "",0,"" );
					 ?>
                    </td>
                    <td align="center">
                        <? 
                    	$date_type_arr = array(1 => "Raised date",2 => "Closed date",3 => "Ship date");
                        echo create_drop_down( "cbo_date_type", 80, $date_type_arr,"", 0, "-- Select --", $selected, "$('#from_date_html').text($('#cbo_date_type option:selected').text());",0,"" );
					 ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px"
                            value="" />
                    </td>
                    <td align="center">
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"
                            value="" />
                    </td>
                    <td>
                        <input type="button" class="formbutton" style="width:70px;" value="Show"
                            onClick="fnc_generate_report(1)" id="show_btn_1" />
                    </td>
                </tr>
                <tr>
                    <td colspan="10" align="center" valign="middle">
                        <? echo load_month_buttons(1); ?>
                    </td>
                </tr>

            </table>

        </fieldset>

		<div id="print_button"></div>
        <div style="margin-top:5px" id="report_container"></div>

    </div>
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_multiselect('cbo_company_id','0','0','','0','fn_on_change()');
</script>

</html>