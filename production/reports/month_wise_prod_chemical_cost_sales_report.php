<?
/* -------------------------------------------- Comments

  Purpose           :   This form will Create Month Wise Chemical Cost and Sales Production Reprot
  Functionality :
  JS Functions  :
  Created by        :   Aziz
  Creation date     :   25-04-2022
  Updated by        :
  Update date       :
  QC Performed BY   :
  QC Date           :
  Comments      :

 */
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Month Wise Prod Chemical Cost and Sales Production Report", "../../", 1, 1, '', '', '');
?>
<script>
    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';
  

    var tableFilters =
            {
                //col_30: "none",
                col_operation: {
                    id: ["tot_dyeing_prod_qty"],
                    col: [1],
                    operation: ["sum"],
                    write_method: ["innerHTML"]
                }
            }
    function print_button_setting(company)
	{    
	     // console.log('hello');
		//$('#button_data_panel').html('');
		get_php_form_data(company,'print_button_variable_setting','requires/month_wise_prod_chemical_cost_sales_report_controller' );
	}
    function fn_dyeing_report_generated(operation)
    {
      
        var b_number = document.getElementById('txt_batch_id').value;
        var batch_no = document.getElementById('txt_batch_no').value;
      
        var company = document.getElementById('cbo_company_name').value;
        var workingCompany = document.getElementById('cbo_working_company_name').value;
        var txt_date_from = document.getElementById('txt_date_from').value;
        var txt_date_to = document.getElementById('txt_date_to').value;
		var cbo_batch_type = document.getElementById('cbo_batch_type').value;
		var txt_ref_no = document.getElementById('txt_ref_no').value;
      
        if (batch_no != "" || txt_ref_no != "" )
        {
            if (form_validation('cbo_company_name', 'Company') == false)
            {
                return;
            }
        }
        else if(company==0 && workingCompany==0)
        {
            if (form_validation('cbo_company_name*txt_date_from*txt_date_to', 'LC Company*From date Fill*To date Fill') == false)
            {
                return;
            }
        }
        else if(txt_date_from=="" || txt_date_to=="")
        {
             if (form_validation('txt_date_from*txt_date_to', 'From date Fill*To date Fill') == false)
            {
                return;
            }

        }
           freeze_window(5);
		   var report_title=$( "div.form_caption" ).html();
		   
            if (operation == 1)
            {
                var data = "action=report_generate&operation=" + operation + get_submitted_data_string('cbo_company_name*cbo_working_company_name*txt_batch_id*txt_batch_no*txt_date_from*txt_date_to*search_type*cbo_batch_type*txt_ref_no', "../../")+'&report_title='+report_title;;
                //alert(data);
            }
            
        http.open("POST", "requires/month_wise_prod_chemical_cost_sales_report_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_show_batch_report;
    }

    function fnc_show_batch_report()
    {
        if (http.readyState == 4)
        {
           //var response = trim(http.responseText).split("****");
		   var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]); 
           
			 document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(3);" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
       
         //   append_report_checkbox('table_header_1', 1);
            
                    setFilterGrid("table_body", -1, tableFilters);
           
            show_msg('3');
            release_freezing();
        }
    }
    function new_window(type)
    {
        if(document.getElementById('table_body'))
			{
				document.getElementById('scroll_body').style.overflow="auto";
				document.getElementById('scroll_body').style.maxHeight="none";
				$('#scroll_body tr:first').hide();
			}
		 

        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close();

       if(document.getElementById('table_body'))
			{
				document.getElementById('scroll_body').style.overflow="auto";
				document.getElementById('scroll_body').style.maxHeight="397px";
		        $('#scroll_body tr:first').show();
		    }
			 
    }
    function batchnumber()
    {
        if (form_validation('cbo_company_name', 'Company') == false)
        {
            return;
        }
        var company_name = document.getElementById('cbo_company_name').value;
        var batch_number = document.getElementById('batch_number_show').value;
        var batch_type = document.getElementById('cbo_batch_type').value;
        var page_link = "requires/month_wise_prod_chemical_cost_sales_report_controller.php?action=batchnumbershow&company_name=" + company_name + "&batch_type=" + batch_type;
        var title = "Batch Number";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=0,scrolling=0', '../')

        emailwindow.onclose = function ()
        {
            var theemail = this.contentDoc.getElementById("selected_id").value;
            var batch = theemail.split("_");
            document.getElementById('batch_number').value = batch[0];
            document.getElementById('batch_number_show').value = batch[1];
            release_freezing();
        }
    }

    function jobnumber(id)
    {
        if (form_validation('cbo_company_name', 'Company') == false)
        {
            return;
        }
        var company_name = document.getElementById('cbo_company_name').value;
        var cbo_buyer_name = document.getElementById('cbo_buyer_name').value;
        var cbo_buyer_name = document.getElementById('cbo_buyer_name').value;
        var batch_type = document.getElementById('cbo_batch_type').value;
        var year = document.getElementById('cbo_year').value;
        var page_link = "requires/month_wise_prod_chemical_cost_sales_report_controller.php?action=jobnumbershow&company_id=" + company_name + "&cbo_buyer_name=" + cbo_buyer_name + "&year=" + year + "&batch_type=" + batch_type;
        var title = "Job Number";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=615px,height=420px,center=1,resize=0,scrolling=0', '../')

        emailwindow.onclose = function ()
        {
            var theemail = this.contentDoc.getElementById("selected_id").value;
            //var job=theemail.split("_");
            document.getElementById('job_number').value = theemail;
            document.getElementById('job_number_show').value = theemail;
            release_freezing();
        }
    }

    function openmypage_order(id)
    {
        if (form_validation('cbo_company_name', 'Company') == false)
        {
            return;
        }
        var company_name = document.getElementById('cbo_company_name').value;
        var buyer_name = document.getElementById('cbo_buyer_name').value;
        var year = document.getElementById('cbo_year').value;
        var job_number = document.getElementById('job_number_show').value;
        var batch_number = document.getElementById('batch_number_show').value;
        var batch_type = document.getElementById('cbo_batch_type').value;
        var year = document.getElementById('cbo_year').value;
        var page_link = "requires/month_wise_prod_chemical_cost_sales_report_controller.php?action=order_number_popup&company_name=" + company_name + "&buyer_name=" + buyer_name + "&year=" + year + "&batch_type=" + batch_type;
        var title = "Order Number";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=515px,height=420px,center=1,resize=0,scrolling=0', '../')
        emailwindow.onclose = function ()
        {
            var theemail = this.contentDoc.getElementById("selected_id").value;
            //var job=theemail.split("_");
            document.getElementById('hidden_order_no').value = theemail;
            document.getElementById('order_no').value = theemail;
            release_freezing();
        }
    }

    function toggle(x, origColor) {
        var newColor = 'green';
        if (x.style) {
            x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
        }
    }

    function js_set_value(str) {
        toggle(document.getElementById('tr_' + str), '#FFF');
    }

    function openmypage_color(id)
    {
        if (form_validation('cbo_company_name', 'Company') == false)
        {
            return;
        }
        var company_name = document.getElementById('cbo_company_name').value;
        var buyer_name = document.getElementById('cbo_buyer_name').value;
        var txtcolor = document.getElementById('txt_color').value;
        var job_number = document.getElementById('job_number_show').value;
        var batch_number = document.getElementById('batch_number_show').value;
        //var ext_number=document.getElementById('txt_ext_no').value;
        var year = document.getElementById('cbo_year').value;
        var page_link = "requires/month_wise_prod_chemical_cost_sales_report_controller.php?action=check_color_id&txtcolor=" + txtcolor;
        var title = "Color Name";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=350px,center=1,resize=0,scrolling=0', '../')
        emailwindow.onclose = function ()
        {
            var theemail = this.contentDoc.getElementById("selected_id").value;
            var split_value = theemail.split('_');
            //alert(theemail);
            document.getElementById('hidden_color_id').value = split_value[0];
            document.getElementById('txt_color').value = split_value[1];
            release_freezing();
        }
    }

    function change_color(v_id, e_color)
    {
        if (document.getElementById(v_id).bgColor == "#33CC00")
        {
            document.getElementById(v_id).bgColor = e_color;
        } else
        {
            document.getElementById(v_id).bgColor = "#33CC00";
        }
    }

   
     

    function fabric_dtls_poup(batch_id, dia_type, order_id, action)
    {
        var company_name = document.getElementById('cbo_company_name').value;
		//alert(batch_id);
        var popup_width = '500px';
        //alert(batch_id);
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/month_wise_prod_chemical_cost_sales_report_controller.php?company_name=' + company_name + '&batch_id=' + batch_id + '&dia_type=' + dia_type + '&order_id=' + order_id + '&action=' + action, 'Details Veiw', 'width=' + popup_width + ', height=350px,center=1,resize=0,scrolling=0', '../');
    }
    function search_populate(str)
    {
        if (str == 1)
        {
            document.getElementById('search_by_th_up').innerHTML = "Dyeing Date";
            $('#search_by_th_up').css('color', 'blue');
        } else if (str == 2)
        {
            document.getElementById('search_by_th_up').innerHTML = "Insert Date";
            $('#search_by_th_up').css('color', 'blue');
        }

    }
function fnc_dyeing_popup(date_key,batch_id,company_id,action,type)
{ //alert(des_prod)
	var w_companyID = $("#cbo_working_company_name").val();
	var company_id = $("#cbo_company_name").val();
	 
	var popup_width='1020px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/month_wise_prod_chemical_cost_sales_report_controller.php?w_companyID='+w_companyID+'&date_key='+date_key+'&batch_id='+batch_id+'&type='+type+'&company_id='+company_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
}
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs("../../", ''); ?>
        <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
            <h3 style="width:870px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id, 'content_search_panel', '')">-Search Panel</h3>         <div id="content_search_panel" >
                <fieldset style="width:870px;">
                    <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                       
                        <th width="100" class="must_entry_caption">Company</th>
                        <th width="100">Working Company</th>
                       
                        <th width="100">Batch Type</th>
                         <th width="70">Batch No</th>
                         <th width="70">Int. Ref No</th>
                        <th width="60">Search By</th>
                        <th width="200" id="search_by_th_up" class="must_entry_caption">Dyeing Date</th>
                        <th  width="70">&nbsp;</th>
                        </thead>
                        <tbody>
                            <tr>

                                <td>
                                    <?
                                    echo create_drop_down("cbo_company_name", 150, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name", "id,company_name", 1, "-- Select Company --", 0, "");
                                    ?>
                                </td>
                                 <td>
                                    <?
                                    echo create_drop_down("cbo_working_company_name", 150, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name", "id,company_name", 1, "-- Select Working Company --", 0, "");
                                    ?>
                                </td>
                                  <td align="center">	
								<?
                                    echo create_drop_down( "cbo_batch_type", 100, $order_source,"",1, "--All--", 0,0,0 );
                                ?>
                            	</td>
                                <td>
                                    <input type="text"  name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:70px;" placeholder="Wr/Br" onDblClick="batchnumber();">                                    <input type="hidden" name="txt_batch_id" id="txt_batch_id">
                                </td>
                                 <td>
                                    <input type="text"  name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:70px;" placeholder="Wr.">                                     
                                </td>
                              
                            
                                <td>
                                    <?
                                    $search_type_arr = array(1 => "Dyeing Date", 2 => "Insert Date");
                                    $fnc_name = "search_populate(this.value)";
                                    echo create_drop_down("search_type", 100, $search_type_arr, "", 0, "-Select-", 0, $fnc_name, 0, "", "", "", "", "");
                                    ?>
                                </td>
                                <td align="center">
                                    <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:45px" placeholder="From Date"/>To
                                    <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:45px" placeholder="To Date"/>
                                </td>
                                <td><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1', 'report_container*report_container2', '', '', '')" class="formbutton" style="width:50px" /></td>
                            </tr>
                            <tr>
                                <td colspan="19" align="center">
                                    <? echo load_month_buttons(1); ?>
                                    &nbsp; 
                                    <span id="button_data_panel"></span>

                                  
                                    <input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_dyeing_report_generated(1)" />&nbsp;
                                  
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </fieldset>
            </div>
            <div id="report_container"></div>
            <div id="report_container2"></div>
            <div id="report_container3" style="visibility: hidden;"></div>
        </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>