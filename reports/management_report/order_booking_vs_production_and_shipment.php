<?php
/*--- -----------------------------------------
Purpose			:	This page will display Order Booking VS Production And Shipment report
Functionality	:
JS Functions	:
Created by		:	Rakib Hasan Mondal
Creation date 	:	04-02-2023
Updated by 		:
Update date		:
Oracle Convert 	:
Convert date	:
QC Performed BY	:
QC Date			:
Comments		:
*/
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_level=$_SESSION['logic_erp']["user_level"];

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Booking VS Production And Shipment","../../", 1, 1, $unicode,1,1);
?>

<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../../logout.php';
    var permission='<?php echo $permission; ?>';

    function openmypage(page_link,title)
    {
         
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=370px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        { 
            freeze_window(5);
            // release_freezing();
            get_php_form_data(1,'booking_popup','requires/order_booking_vs_production_and_shipment_controller.php');
            // $("#cbo_company_name").attr("disabled","disabled");
        }
    }
    function open_popup(action,month='',title='',buyer='')
	{
        let form_date = '';
        let to_date = '';
        let lc_company = $('#cbo_lc_company_name').val();
        let wo_company = $('#cbo_work_company_name').val();
        if (buyer =='') {
            buyer = $('#cbo_buyer_name').val();
        }
       
        form_date =  $('#txt_date_from').val();
        to_date =  $('#txt_date_to').val();
       
        let link= 'requires/order_booking_vs_production_and_shipment_controller.php'; 
	    let page_link=link+'?action='+action+'&lc_company='+lc_company+'&wo_company='+wo_company+'&buyer='+buyer+'&month='+month+'&form_date='+form_date+'&to_date='+to_date;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=390px,center=1,resize=0,scrolling=0','../')
		// emailwindow.onclose=function()
		// {
		
		// }
	}
     
    function getCompanyId() 
	{	 
	    var company_id = document.getElementById('cbo_work_company_name').value;

	    if(company_id !='') {
	      var data="action=load_drop_down_location&choosenCompany="+company_id;
	      http.open("POST","requires/order_booking_vs_production_and_shipment_controller.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data); 
	      http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#location_td').html(response);
	              set_multiselect('cbo_location_name','0','0','','0');
				  setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_name,'0');getLocationId();") ,3000)];
	          }			 
	      };
	    }         
	}
    function fn_report_generated(id) {  
        if(form_validation('cbo_lc_company_name*txt_date_from*txt_date_to', 'Company*Date*Date')==false) {
            return;
        }
        freeze_window(operation);
        var txt_date_from = document.getElementById('txt_date_from').value;
        var txt_date_to = document.getElementById('txt_date_to').value; 
        var data="action=generate_report&type="+id+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+get_submitted_data_string('cbo_lc_company_name*cbo_work_company_name*cbo_buyer_name', "../../");
        http.open("POST", "requires/order_booking_vs_production_and_shipment_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_generate_report_main_reponse; 
    } 
    function fnc_generate_report_main_reponse() {
        if(http.readyState == 4) 
        {
            show_msg('3'); 
            var reponse=trim(http.responseText).split("####"); 
            if(reponse[2]=="show_chart")
            {
                // alert(reponse[3]+reponse[4]);
                // showChart(reponse[3],reponse[4]);
            }
            $('#report_container2').html(reponse[0]);
            // alert(reponse[2]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
            // setFilterGrid("table_body",-1,tableFilters);     
            release_freezing();
        }
        
    }
    function new_window() {
        document.getElementById('scroll_body').style.overflow='auto';
        document.getElementById('scroll_body').style.maxHeight='none'; 
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>'); 
        d.close();
        document.getElementById('scroll_body').style.overflowY='scroll';
        document.getElementById('scroll_body').style.maxHeight='300px';
    }
   

</script>

<style>
    tbody#report-container tr td {
        text-align: center;
        padding: 3px 1px;
    } 
</style>

</head>

<body>
    <?php echo load_freeze_divs('../', $permission); ?>
	<form id="order_vs_prod_spmt_rpt"> 
        <div class="search-panel" style="display:flex; justify-content:center;"  align="center">
            <fieldset style="width:745px;">
                <legend>Search Pannel</legend>
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>
                        <th class="must_entry_caption">LC Company</th>
                        <th>Working Company</th>
                        <th>Buyer Name</th>
                        <th colspan="2">Shipment Date</th>
                        <th>  
                            <input type="button"  style="padding:0 12px" id="booking_vs_production" class="formbutton" value="Reset"  name="booking_vs_production"  onClick="reset_form('order_vs_prod_spmt_rpt','','')" /> 
                            
                            <input type="button" style="padding:0 12px"  id="booking_vs_shipment" class="formbutton" value="Booking Vs Production"  name="booking_vs_shipment"  onClick="fn_report_generated(1)" />
                        </th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td id="lc_company"> 
                                <?php
                                    echo create_drop_down( 'cbo_lc_company_name', 120, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $selected,"load_drop_down( 'requires/order_booking_vs_production_and_shipment_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );;get_php_form_data( this.value, 'eval_multi_select', 'requires/order_booking_vs_production_and_shipment_controller' );");
                                ?>
                            </td>
                            <td> 
                                <?php  
                                    echo create_drop_down( "cbo_work_company_name", 120, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "Select Company", $selected, "" ); 
                                ?>
                            </td>
                            <td id="buyer_td">
                                <?php 
                                    echo create_drop_down('cbo_buyer_name', 120, $blank_array, '', 1, '-- Select Buyer --', $selected, '');
                                ?>
                            </td> 
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly>
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" readonly>
                            </td>
                            <td align="center">
                                <input type="button" style="padding:0 12px"  id="buyer_w_booking_vs_spmnt" class="formbutton" value="Booking Vs Shipment"  name="buyer_w_booking_vs_spmnt"  onClick="fn_report_generated(2)" />
                            </td>                    
                        </tr>
                        <tr>
                            <td colspan="5" align="center">
                                <?php echo load_month_buttons(1); ?>
                            </td>
                            <td align="center"> 
                                <input type="button" style="padding:0 12px"  id="buyer_w_prod" class="formbutton" value="Buyer Wise Booking Vs Production"  name="buyer_w_prod"  onClick="fn_report_generated(3)" /> 
                                
                                <input type="button" style="padding:0 12px"  id="buyer_w_shipment" class="formbutton" value="Buyer Wise Booking Vs Shipment"  name="buyer_w_shipment"  onClick="fn_report_generated(4)" />

                            </td>
                        </tr> 
                    </tbody>        
                </table>				
            </fieldset>
        </div>  
	</form>
    <div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2"></div>
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
    set_multiselect('cbo_work_company_name','0','0','','0');
    set_multiselect('cbo_buyer_name','0','0','','0');  
    setTimeout[($("#lc_company a").attr("onclick","disappear_list(cbo_location_name,'0');getCompanyId();") ,3000)];
    document.getElementById('cbo_year_selection').onchange = resetDates;
</script>
</html>