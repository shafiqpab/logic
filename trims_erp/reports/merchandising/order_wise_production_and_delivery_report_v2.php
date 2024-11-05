<?php
/*-------------------------------------------- Comments
Purpose         :   This form will create Total Report for Trims
                
Functionality   :   
JS Functions    :
Created by      :   Sapayth Hossain
Creation date   :   07-06-2020
Updated by      :       
Update date     :          
QC Performed BY :       
QC Date         :   
Comments        :
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents('Order Wise Production and Delivery Report V2', '../../../', 1, 1, $unicode, 1, 1);
?>
<style>
    table tr td, table tr th {
        padding: 5px;
        vertical-align: middle;
    }
</style>
<script>
var permission='<?php echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
    
    
    function load_internal(id) {
        if(id !=1 ) {
            $('#txt_internal_no').attr('disabled', true);
            $('#txt_internal_no').val('');
        } else {
            $('#txt_internal_no').attr('disabled', false);
        }
    }

    function generate_report(report_type) {
        if(document.getElementById('cbo_company_id').value==0 || (document.getElementById('txt_order_no').value=='' &&  document.getElementById('txt_internal_no').value=='' && document.getElementById('txt_style_ref').value=='')){
            if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From date*To date')==false )
            {
                return;
            }
        }
        
        var data="action=generate_report&report_type="+report_type+"&report_title="+$( "div.form_caption" ).html()+get_submitted_data_string('cbo_company_id*cbo_location_name*cbo_order_source*txt_order_no*cbo_customer_name*txt_internal_no*cbo_section_id*cbo_delivery_status*txt_date_from*txt_date_to*txt_style_ref',"../../../");
        freeze_window(3);
        http.open("POST","requires/order_wise_production_and_delivery_report_controller_v2.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_report_reponse;
    }

    function generate_report_reponse() {   
        if(http.readyState == 4) 
        {    
            var response=trim(http.responseText).split("**");
            $("#report_container").html(response[0]);  
            document.getElementById('report_button_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="printPreview()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            // setFilterGrid("table_body_id",-1,'');
            show_msg('3');
            release_freezing();
        }
    }

    function printPreview() {
        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="none";
        $('#table_body_id tr:first').hide();
        var w = window.open("Print Preview", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
        d.close(); 
        $('#table_body_id tr:first').show();
        document.getElementById('scroll_body').style.overflow="auto"; 
        document.getElementById('scroll_body').style.maxHeight="250px";
    }

    function clearDate() {
        document.getElementById('txt_date_from').value = '';
        document.getElementById('txt_date_to').value = '';
    }

    function fnc_amount_details(id, orderRate, exchangeRate, internalRef, section, action, popupTitle) {
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_production_and_delivery_report_controller_v2.php?jobDtlsId='+id+'&internalRef='+internalRef+'&section='+section+'&orderRate='+orderRate+'&exchangeRate='+exchangeRate+'&action='+action, popupTitle, 'width=1000px,height=320px,center=1,resize=0', '../../');
        emailwindow.onclose=function() {}
    }

    function downloiadFile(id,company_name)
    {
        var title = 'Trims Order Receive File Download';    
        var page_link = 'requires/order_wise_production_and_delivery_report_controller_v2.php?action=get_user_pi_file&id='+id+'&company_name='+company_name;
          
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
        
        emailwindow.onclose=function()
        {
            
        }

    }

</script>
</head>
<body>
    <div align="center">
        <?php echo load_freeze_divs ("../../../", $permission);  ?>
        <h3 style="width:80%;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> - Search Panel</h3>
        <div id="content_search_panel" style="width:80%"> 
            <form name="monthly_capacity_booked_1" id="monthly_capacity_booked_1" autocomplete="off" >    
                <fieldset>  
                    <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <thead>
                            <th width="120" class="must_entry_caption">Company</th>
                            <th width="120">Location</th>
                            <th width="100">Order Source</th>
                            <th width="100">Customer Name</th>
                            <th width="100">Work Order No</th>
                            <th width="100">Style</th>
                            <th width="100">Internal Ref</th>
                            <th width="100">Section</th>
                            <th colspan="2" id="th_date_caption" class="must_entry_caption">Date Range</th>                        
                            <th width="100">Delivery Status</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('monthly_capacity_booked_1','','','','')" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td>
                                    <?php
                                        echo create_drop_down('cbo_company_id', 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '--Select Company--', $selected, "load_drop_down( 'requires/order_wise_production_and_delivery_report_controller_v2', this.value, 'load_drop_down_buyer', 'buyer_td');load_drop_down( 'requires/order_wise_production_and_delivery_report_controller_v2', this.value, 'load_drop_down_location', 'location_td');", '', '', '', '', '', 2);
                                    ?>
                                </td>
                                <td id="location_td">
                                    <?php 
                                        echo create_drop_down( 'cbo_location_name', 100, $blank_array, '', 1, '-- Select Location --', $selected, '', '', '', '', '', '', 2);
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        $order_source = array(1 => 'Internal', 2 => 'External');
                                        echo create_drop_down('cbo_order_source', 100, $order_source, '', 1, '--All--', 0, "load_drop_down( 'requires/order_wise_production_and_delivery_report_controller_v2', (document.getElementById('cbo_company_id').value+'_'+this.value), 'load_drop_down_buyer', 'buyer_td' );load_internal(this.value)", '', '', '', '', '',2);
                                    ?>
                                </td>
                                <td id="buyer_td">
                                    <?php
                                        echo create_drop_down('cbo_customer_name', 100, $blank_array, '', 1, '--All--', $selected, '', '', '', '', '', '', 2);
                                    ?>
                                </td>
                                 <td >
                                    <input type="text" name="txt_order_no" id="txt_order_no" onfocus="clearDate();" class="text_boxes" style="width:100px;" />
                                </td>
                                 <td >
                                    <input type="text" name="txt_style_ref" id="txt_style_ref" onfocus="clearDate();" class="text_boxes" style="width:100px;" />
                                </td>
                                <td >
                                    <input type="text" name="txt_internal_no" id="txt_internal_no" value="" class="text_boxes" style="width:100px;" disabled />                                             
                                </td>
                                <td>
                                    <?php 
                                        echo create_drop_down('cbo_section_id', 100, $trims_section, '', 1, '--All--', '', '');
                                    ?>
                                </td>
                                <td width="90">
                                    <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px;"/>                                                
                                </td>
                                 <td width="90">
                                    <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px;"/>                        
                                </td>

                               <td> 
                                   <?php   
                                        echo create_drop_down( "cbo_delivery_status", 100, $delivery_status, "", 1, "--All--","", " $('#th_date_caption').html($('#cbo_date_category option:selected').text());", "", "");
                                    ?>
                                </td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />
                            </td>
                            </tr>
                            <tr>
                                <td colspan="13" align="center"><?php echo load_month_buttons(1);  ?></td>
                            </tr>
                        </tbody>
                    </table> 
                </fieldset>
            </form> 
        </div>
    </div>

    <div id="report_button_container" align="center"></div>

    <div id="report_container" align="center">
        <!-- <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="3600">
            <thead>
                <tr>
                    <th width="35">SL</th>
                    <th width="100">Customer Name</th>
                    <th width="100">Cust. Buyer</th>
                    <th width="100">Cust. Work Order</th>
                    <th width="100">Trims Job No</th>
                    <th width="100">Style</th>
                    <th width="100">Internal Ref</th>
                    <th width="100">Section</th>
                    <th width="100">Item Group</th>
                    <th width="100">Order UOM</th>
                    <th width="130">Item Description</th>
                    <th width="100">Item Color</th>
                    <th>Item Size</th>
                    <th>Req. Qty/Ord Rcv</th>
                    <th>Rate</th>
                    <th>Ord Rcv Currency</th>
                    <th>Req. Value [Tk]</th>
                    <th>Prod. Qty</th>
                    <th>Prod. Value [Tk]</th>
                    <th>Prod. Bal. Qty</th>
                    <th>Prod. Bal. Value [Tk]</th>
                    <th>QC. Qty QC. Bal. Qty</th>
                    <th>Deli. Qty.</th>
                    <th>Deli. Value [Tk]</th>
                    <th>Deli. Balance Qty</th>
                    <th>Deli. Balance Value [Tk]</th>
                    <th>Bill Qty</th>
                    <th>Bill Amount [Tk]</th>
                    <th>Bill Banalce Qty</th>
                    <th>Bill Balance Amount [Tk]</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="20">1</td>
                    <td rowspan="20">A One Polar Ltd.</td>
                    <td rowspan="20">CnA</td>
                    <td rowspan="20">AOPL-TB-20-01280</td>
                    <td rowspan="20">MTL-TOR-20-01789</td>
                    <td rowspan="20">606-20Q2-1J14 JOG SHORT</td>
                    <td rowspan="20">AC20-67</td>
                    <td rowspan="2">Printed Label</td>
                    <td rowspan="2">Care Label</td>
                    <td rowspan="2">Pcs</td>
                    <td>S333 Two Part</td>
                    <td>UNIVERSAL BLACK</td>
                    <td></td>
                    <td>22,660.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>S333 Two Part</td>
                    <td>UNIVERSAL BLACK</td>
                    <td></td>
                    <td>22,660.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td rowspan="3">Poly</td>
                    <td rowspan="3">Poly [Pcs]</td>
                    <td rowspan="3">Pcs</td>
                    <td rowspan="3">10 MM PE POLY</td>
                    <td rowspan="3">0</td>
                    <td>74X55X74</td>
                    <td>5,355.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>74X55X74</td>
                    <td>5,355.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>74X55X74</td>
                    <td>5,355.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td rowspan="6">Price Ticket</td>
                    <td rowspan="6">Price Ticket</td>
                    <td rowspan="6">Pcs</td>
                    <td rowspan="6">TYPE 105</td>
                    <td rowspan="6">0</td>
                    <td>M</td>
                    <td>5,355.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>S</td>
                    <td>5,355.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>L</td>
                    <td>5,355.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>XL</td>
                    <td>5,355.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>XXL</td>
                    <td>5,355.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>3XL</td>
                    <td>5,355.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Elastic</td>
                    <td>Elastic [Yds]</td>
                    <td>Yds</td>
                    <td>450 DNR,5.3 C.M Elastic</td>
                    <td>BLACK</td>
                    <td>M</td>
                    <td>5,355.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td rowspan="2">Price Ticket</td>
                    <td rowspan="2">Price Ticket</td>
                    <td rowspan="2">Pcs</td>
                    <td rowspan="2">TYPE 105</td>
                    <td rowspan="2">0</td>
                    <td>M</td>
                    <td>5,355.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>M</td>
                    <td>5,355.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td rowspan="6">Price Ticket</td>
                    <td rowspan="6">Price Ticket</td>
                    <td rowspan="6">Pcs</td>
                    <td rowspan="6">TYPE 105</td>
                    <td rowspan="6">0</td>
                    <td>M</td>
                    <td>5,355.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>M</td>
                    <td>5,355.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>M</td>
                    <td>5,355.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>M</td>
                    <td>5,355.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>M</td>
                    <td>5,355.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>M</td>
                    <td>5,355.00</td>
                    <td>0.25</td>
                    <td>USD</td>
                    <td>90.64</td>
                    <td>41010</td>
                    <td>90.64</td>
                    <td>-</td>
                    <td></td>
                    <td>41010</td>
                    <td>-</td>
                    <td>41,010.00</td>
                    <td>77.70</td>
                    <td>3,234.00</td>
                    <td>41,010.00</td>
                    <td>10,252.50</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="12" align="right"><b>Total:</b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>10,081.00</td>
                </tr>
            </tfoot>
        </table> -->
    </div>
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
