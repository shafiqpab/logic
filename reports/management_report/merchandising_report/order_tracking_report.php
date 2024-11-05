<?
/* -------------------------------------------- Comments -----------------------
  Purpose			: 	This Form Will Create Order Tracking Report.
  Functionality	:
  JS Functions	:
  Created by		:	Shafiq
  Creation date 	: 	13-09-2021
  Updated by 		:
  Update date		:
  QC Performed BY	:
  QC Date			:
  Comments		    :   Passion for writing beautiful and clean code
 */

session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Tracking Report", "../../../", 1, 1, $unicode, 1, 1);
?>	
<script type="text/javascript" src="../../../js/jquery.image-zoom.js"></script>
<script>

    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../../logout.php";
    var permission = '<? echo $permission; ?>';
    var cbo_search_by = $("#cbo_search_by").val();

    var tableFilters =
    {
        /*col_operation: {
            id: ["value_total_order_value", "total_order_qnty_pcs", "td_ship_per_ex_qty", "total_ship_qnty", "value_total_ship_value", "total_balance_ship_qnty"],
            col: [14, 15, 40, 42, 43, 44],
            operation: ["sum", "sum", "sum", "sum", "sum", "sum", "sum", "sum", "sum"],
            write_method: ["innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML"]
        }*/
    }

    
	function fnExportToExcel()
	{
		// $(".fltrow").hide();
		var tableData = document.getElementById("report_container2").innerHTML;
		// alert(tableData);
	    var data_type = 'data:application/vnd.ms-excel;base64,',
		template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="2px">{table}</table></body></html>',
		base64 = function (s) {
			return window.btoa(unescape(encodeURIComponent(s)))
		},
		format = function (s, c) {
			return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; })
		}
		
		var ctx = {
			worksheet: 'Worksheet',
			table: tableData
		}
		
	    document.getElementById("dlink").href = data_type + base64(format(template, ctx));
	    document.getElementById("dlink").traget = "_blank";
	    document.getElementById("dlink").click();
		// $(".fltrow").show();
		// alert('ok');
	}
    
    function fn_excel_history()
    {
       
        freeze_window(3);
        var data = "action=report_generate_excel" + get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*txt_order_no*cbo_year*cbo_order_status*cbo_status*cbo_shipment_status*cbo_report_type*cbo_date_category', "../../../");
        //alert(data);           
        
        http.open("POST", "requires/order_tracking_report_controller.php", true);
        
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_excel_generated_reponse;
        
    }
    function fn_report_excel_generated_reponse()
    {
        if (http.readyState == 4)
        { 
            var response = trim(http.responseText).split("####");
            $('#report_container3').html(response[0]);
            release_freezing();
            window.open("requires/"+response[1]);
        }

    }

    function fn_report_generated(type)
    {
        var job_no = $("#txt_job_no").val();
        var style_ref = $("#txt_style_ref").val();
        var order_no = $("#txt_order_no").val();
        var date_from = $("#txt_date_from").val();
        var date_category = $("#cbo_date_category").val();
        var shipment_status = $("#cbo_shipment_status").val();

        if(job_no=="" && style_ref=="" && order_no=="" && shipment_status==0 && date_from=="")
        {
            alert("Please Enter Search Value of Job No, Style Ref,Order No,Shipment Status or Date Range Field.");
            return;
        }

        if(date_from!="" && date_category==0)
        {
            alert("Please select date category.");
            return;
        }

	    if (form_validation('cbo_company_name', 'Comapny Name') == false)//*txt_date_from*txt_date_to----*From Date*To Date
        {
            return;
        } 
        else
        {
            freeze_window(3);
            var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*txt_order_no*cbo_year*cbo_order_status*cbo_status*cbo_shipment_status*cbo_report_type*cbo_date_category', "../../../")+'&type='+type;
            //alert(data);           
			
            http.open("POST", "requires/order_tracking_report_controller.php", true);
			
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fn_report_generated_reponse;
        }
    }

    function fn_report_generated_reponse()
    {
        if (http.readyState == 4)
        {   
            release_freezing();
            var reponse = trim(http.responseText).split("**");
            $('#report_container2').html(reponse[0]);
            document.getElementById('report_container').innerHTML = report_convert_button('../../../');
            //document.getElementById('report_container').innerHTML ='<input onclick="print_priview_html_exel( \'report_container2\', \'scroll_body\',\'table_header_1\',\'report_table_footer\', 1, \'0\',\'../../../\' )" type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"> <input type="button" onclick="print_priview_html( \'report_container2\', \'scroll_body\',\'table_header_1\',\'report_table_footer\', 3, \'0\',\'../../../\' )" value="HTML Preview" name="Print" class="formbutton" style="width:100px">';
            append_report_checkbox('table_header_1', 1);
            //append_report_checkbox('table_header_1', 1);
            
            document.getElementById("check_uncheck_tr").style.display="table";
            if($("#check_uncheck").is(":checked")==false)
                $("#check_uncheck").attr("checked","checked"); 
			
            setFilterGrid("table_body", -1, tableFilters);
            show_msg('3');
            release_freezing();
        }

    }
    

    function fn_check_uncheck(){
        var lengths = $("[type=checkbox]").length;
        if($("#check_uncheck").is(":checked") != true){     
            for(var i=0; i<=lengths; i++){
                
                $("[type=checkbox]").prop('checked', false);
                $("[type=checkbox]").removeClass('rpt_check');
                $("[type=checkbox]").removeAttr('checked');
            }
        }else{
            $("[type=checkbox]").prop('checked', true);
            for(var i=0; i<=lengths; i++){
                
                $("[type=checkbox]").not("#check_uncheck").addClass('rpt_check');
                $("[type=checkbox]").attr('checked',"checked");
            }
        }    
    }

    function show_popup(action, job_number, width,type)
    {
        var title = '';
        if(type==1)
        {
            title = "Sample Approval Details";
        }
        else if(type==2)
        {
            title = "Order Status Details";
        }
        else if(type==3 || type==4)
        {
            title = "Main Fabric Booking Approval Status";
        }
        else if(type==5)
        {
            title = "Work Order Details";
        }
        else if(type==6)
        {
            title = "Trims/Acc Status Details";
        }
        else
        {
            title = "Closing Status Details";
        }
        
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_tracking_report_controller.php?action=' + action + '&job_number=' + job_number, title, 'width=' + width + ',height=400px,center=1,resize=0,scrolling=0', '../../');
    }

    function show_progress_report_daysInHand(action, job_number, width, country)
    {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_tracking_report_controller.php?action=' + action + '&job_number=' + job_number + '&country=' + country, 'Work Progress Report Details', 'width=' + width + ',height=370px,center=1,resize=0,scrolling=0', '../../');
    }

    function show_trims_rec(action, po_number, po_id, width)
    {
        var budget_version=document.getElementById('cbo_budget_version').value;
		if(budget_version==1)
		{
	   	 	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_tracking_report_controller.php?action=' + action + '&po_number=' + po_number + '&po_id=' + po_id, 'Trims Receive Details', 'width=' + width + ',height=400px,center=1,resize=0,scrolling=0', '../../');
		}
		else
		{
			 emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_tracking_report_controller2.php?action=' + action + '&po_number=' + po_number + '&po_id=' + po_id, 'Trims Receive Details', 'width=' + width + ',height=400px,center=1,resize=0,scrolling=0', '../../');
		}
    }

    function progress_comment_popup(job_no, po_id, template_id, tna_process_type)
    {
         var budget_version=document.getElementById('cbo_budget_version').value;
		var data = "action=update_tna_progress_comment" +
                '&job_no=' + "'" + job_no + "'" +
                '&po_id=' + "'" + po_id + "'" +
                '&template_id=' + "'" + template_id + "'" +
                '&tna_process_type=' + "'" + tna_process_type + "'" +
                '&permission=' + "'" + permission + "'";

       if(budget_version==1)
	   {
	   	 http.open("POST", "requires/order_tracking_report_controller.php", true);
	   }
	   else
	   {
	   	 http.open("POST", "requires/order_tracking_report_controller2.php", true);
	   }

        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_progress_comment_reponse;
    }

    function generate_progress_comment_reponse()
    {
        if (http.readyState == 4)
        {
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><title></title></head><body>' + http.responseText + '</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
            d.close();
        }
    }

    function disable_order(val)
    {
        $('#cbo_ls_sc').val(0);

        if (val == 1)
        {
            $('#cbo_ls_sc').removeAttr('disabled', 'disabled');
        } else
        {
            $('#cbo_ls_sc').attr('disabled', 'disabled');
        }
        if (val == 3)
        {
            document.getElementById('search_by_th_up').innerHTML = "Country Ship Date";
        } else
        {
            document.getElementById('search_by_th_up').innerHTML = "Pub. Shipment Date";
        }
    }

    function openmypage_image(page_link, title)
    {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0', '../../')
        emailwindow.onclose = function ()
        {
        }
    }




    function openmypage_order(po_break_down_id, company_name, item_id, country_id, action)
    {
        //var garments_nature = $("#cbo_garments_nature").val();
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_tracking_report_controller.php?po_break_down_id=' + po_break_down_id + '&company_name=' + company_name + '&item_id=' + item_id + '&country_id=' + country_id + '&action=' + action, 'Order Quantity', 'width=750px,height=350px,center=1,resize=0,scrolling=0', '../../');
    }

    function print_report_button_setting(report_ids) 
    {
       
        /*$('#show_button').hide();
        $('#show_button1').hide();
        var report_id=report_ids.split(",");
        report_id.forEach(function(items)
        {
            if(items==108){$('#show_button').show();}
            else if(items==248){$('#show_button1').show();}
        });*/
    }
    
    function print_priview_html_exel(report_div, scroll_div, header_table, footer_table, report_type, link_pos, rel_path, extra_func, top_table)
    {
        var filter=0;
        
       
        if ( report_type==1  || report_type==2)
        {
            

            var original_html = document.getElementById(report_div).innerHTML;
            var mxheght= document.getElementById(scroll_div).style.maxHeight;
            var total_width=0;
            var top_wd=$("#"+header_table).width();
            var botom_wd=$("#"+scroll_div+ " table").width();
            var top_wd_new=0;
            var botom_wd_new=0;
            var k=0;
            var idd;
            var theadCheckedColumn='';

                $("#"+header_table+" thead th").each(function() {
                    var wd=($(this).width());

                    if (!$('#'+header_table+"_"+k).hasClass('rpt_check'))
                    {
                         //alert(k+"-"+$('#'+header_table+"_"+k).hasClass('rpt_check'))
                        total_width=(total_width*1)+(wd*1);
                        //alert (total_width);
                        $("#"+header_table).find("tr").each(function(){
                            $(this).find("th:eq("+k+")").addClass('out_of_report') ;
                        });
                        $("#"+scroll_div+" table").find("tr").each(function(){
                            $(this).find("td:eq("+k+")").addClass('out_of_report') ;
                        });
                        $("#"+footer_table).find("tfoot tr").each(function(){
                            $(this).find("th:eq("+k+")").addClass('out_of_report');
                        });
                    }
                    else
                    {
                        $("#"+header_table).find("tr").each(function(){
                            var colHeader=$(this).find("th:eq("+k+")").text();
                            console.log(colHeader);
                            theadCheckedColumn=theadCheckedColumn+'***'+colHeader;
                        });
                        
                    }
                    k++;
                });
                var search_panel=document.getElementById('cbo_company_name').value+'*__*'+document.getElementById('cbo_buyer_name').value+'*__*'+document.getElementById('cbo_year').value+'*__*'+document.getElementById('txt_job_no').value+'*__*'+document.getElementById('txt_style_ref').value+'*__*'+document.getElementById('txt_order_no').value+'*__*'+document.getElementById('cbo_shipment_status').value+'*__*'+document.getElementById('cbo_status').value+'*__*'+document.getElementById('cbo_order_status').value+'*__*'+document.getElementById('cbo_report_type').value+'*__*'+document.getElementById('cbo_date_category').value+'*__*'+document.getElementById('txt_date_from').value+'*__*'+document.getElementById('txt_date_to').value+'*__*'+document.getElementById('cbo_year_selection').value;
                details_ids = trim(return_global_ajax_value(theadCheckedColumn+'*_*'+search_panel, 'save_excel_column', '', 'requires/order_tracking_report_controller'));
                console.log(details_ids+'=>'+theadCheckedColumn);
                $(".out_of_report").remove();
                $(".rpt_check").remove();

                top_wd_new=top_wd-total_width;
                botom_wd_new=botom_wd-total_width;
                

                 if ($("#"+scroll_div +" table tr:first").attr('class')=='fltrow')
                 {
                    filter=1;
                    $("#"+scroll_div +" table tr:first").remove();
                 }

                 $("#"+header_table).width(top_wd_new);
                 $("#"+scroll_div+ " table").width(botom_wd_new);

                 document.getElementById(scroll_div).style.overflow="auto";
                 document.getElementById(scroll_div).style.maxHeight="none";

                var html = document.getElementById(report_div).innerHTML;
                 var tto=($(html).find('a').replaceWith(function() {
                    return this.innerHTML;
                }).end().html());

                 $.post(rel_path+"includes/common_functions_for_js.php",
                  { path: rel_path, action: "generate_report_file", htm_doc: tto },
                  function(data){
                    window.open(rel_path+trim(data), "#");
                  }
                );
                 document.getElementById(report_div).innerHTML="" ;
                 document.getElementById(report_div).innerHTML=original_html ;
                  if ($("#"+scroll_div +" table tr:first").attr('class')=='fltrow')
                 {
                    $("#"+scroll_div +" table tr:first").remove();
                 }
                 if (!tableFilters) var tableFilters="";
                 setFilterGrid('table_body',-1,tableFilters);

        }
    }

    function new_window()
    {
        const el = document.querySelector('#scroll_body');
          if (el) {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none"; 

        }
        
        $(".flt").hide();
        
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close(); 
         if (el) {
            document.getElementById('scroll_body').style.overflowY="auto"; 
            document.getElementById('scroll_body').style.maxHeight="400px";

        }
        
        $(".flt").show();
    }


    function generate_fabric_report(booking_no)//print_booking_10
    {        
        var show_yarn_rate='';        
       
        var r=confirm("Do You Want to Hide Buyer and Style Name?");
        if (r==true) show_yarn_rate="1"; else show_yarn_rate="0";
            
        var report_title=$( "div.form_caption" ).html();
        var data="action=print_booking_10&txt_booking_no="+booking_no+get_submitted_data_string('cbo_company_name',"../../")+'&report_title='+report_title+'&show_yarn_rate='+show_yarn_rate+'&path=../../../';
        freeze_window(5);
        http.open("POST","requires/order_tracking_report_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_fabric_report_reponse;       
    }


    function generate_fabric_report_reponse()
    {
        if(http.readyState == 4){
            var file_data=http.responseText.split('****');
            if(file_data[2]==100)
            {
                $('#data_panel').html(file_data[1]);
                $('#aal_report4').removeAttr('href').attr('href','requires/'+trim(file_data[0]));
                    //$('#print_report4')[0].click();
                document.getElementById('aal_report4').click();
            }
            else
            {
                $('#pdf_file_name').html(file_data[1]);
                $('#data_panel').html(file_data[0] );
            }
            var w = window.open("Surprise", "_blank");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
            d.close();
            var content=document.getElementById('data_panel').innerHTML;

            $('#data_panel').html('');
            $('#pdf_file_name').html('');
            $('#aal_report4').html('');
            release_freezing();
        }
    }

</script>
</head>
<body onLoad="set_hotkey();">
    <div id="data_panel"></div>
    <div id="aal_report4"></div>
    <div id="pdf_file_name"></div>

    <form id="dateWiseProductionReport_1">
        <div style="width:100%;" align="center">    
            <? echo load_freeze_divs("../../../", ''); ?>
            <!-- <div style="text-align: center;color: red;font-weight: bold;font-size: 16px;">This report is under development.</div> -->
            <h3 style="width:1370px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id, 'content_search_panel', '')"> -Search Panel</h3> 
            <div id="content_search_panel" >      
                <fieldset style="width:1370px;">
                    <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>                    
                            <th class="must_entry_caption">Company Name</th>
                            <th>Buyer Name</th>
                            <th>Job Year</th>              
                            <th>Job No</th>     
                            <th>Style Ref.</th>
                            <th>Order No</th>  
                            <th>Shipment Status</th>
                            <th>Order Status</th>
                            <th>Status</th>
                            <th>Report Type</th>
                            <th>Date Category</th>
                            <th id="search_by_th_up">Date Range </th>
                            <th width="160">
                                <input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" />
                            </th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td align="center"> 
                                    <?
                                    echo create_drop_down("cbo_company_name", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_tracking_report_controller',this.value,'load_drop_down_buyer', 'buyer_td' );");
                                    ?>
                                </td>
                                <td id="buyer_td"  align="center">
                                    <?
                                    echo create_drop_down("cbo_buyer_name", 110, $blank_array, "", 1, "-- Select Buyer --", $selected, "", 1, "");
                                    ?>
                                </td>
                                <td align="center">
                                    <?
                                    echo create_drop_down("cbo_year", 65, create_year_array(), "", 1, "-- All --", date('Y'), "", 0, "");
                                    ?>
                                </td>
                                <td align="center">
                                    <input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Job No" >
                                </td>
                                <td align="center">
                                    <input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px" placeholder="Styel Ref" >
                                </td>
                                <td align="center">
                                    <input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:70px" placeholder="Order No" >
                                </td>
                                <td>
                                    <? 
                                    array_push($shipment_status, "Running");
                                    // print_r($shipment_status);
                                    echo create_drop_down( "cbo_shipment_status", 100, $shipment_status, "", 1, "----All----","4", "",0,"" ); 
                                    ?>
                                </td>
                                <td>
                                    <? echo create_drop_down( "cbo_status", 100, $order_status, "", 1, "----All----","1", "",0,"" ); ?>
                                </td>
                                <td>
                                    <? echo create_drop_down( "cbo_order_status", 100, $row_status, "", 1, "----All----","1", "",0,"" ); ?>
                                </td>
                                <td>
                                    <?
                                        $report_type = array(1=>"Job Wise", 2=>"Order Wise");
                                        echo create_drop_down( "cbo_report_type", 100, $report_type, "", 1, "----All----","1", "",0,"" ); 
                                    ?>
                                </td>
                                <td>
                                    <? 
                                        $date_category = array(1=>"Original Ship Date", 2=>"PO Ship Date", 3=>"Fac. Receive Date");
                                        echo create_drop_down( "cbo_date_category", 100, $date_category, "", 1, "----All----","1", "",0,"" ); 
                                    ?>
                                </td>


                                
						
                                <td align="center">
                                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date">&nbsp;
                                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date">
                                </td>
                                <td>
                                    <input type="button" id="show_button" class="formbutton" style="width:60px;" value="Show" onClick="fn_report_generated(1)" />&nbsp;<input type="button" id="show_button" class="formbutton" style="width:60px;" value="Summary" onClick="fn_report_generated(2)" />
                                    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table>
                        <tr>
                            <td>
                                <? echo load_month_buttons(1); ?>
                            </td>
                            <td><a onclick="fn_excel_history(1)" id="dlink" style="text-decoration:none"><input type="button" value="Export To Excel"  name="excel" id="exportBtn" class="formbutton" style="width:100px"/></a></td>
                        </tr>                        
                    </table>
                    <table align="left">
                        <tr id="check_uncheck_tr" style="display:none;">
                            <td><input type="checkbox" id="check_uncheck" name="check_uncheck" onClick="fn_check_uncheck()"/> <strong style="color:#176aaa; font-size:14px; font-weight:bold;">Check/Uncheck All</strong>
                            </td>
                        </tr>
                    </table>                    
                    <br />
                </fieldset>
            </div>
        </div>

        <div id="report_container" align="center" style="padding: 5px;"></div>
        <div id="report_container2" align="left"></div>
        <div id="report_container3" style="display:none;"></div>
    </form>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_multiselect('cbo_shipment_status', '0', '0', '0', '0');
</script>
</html>
