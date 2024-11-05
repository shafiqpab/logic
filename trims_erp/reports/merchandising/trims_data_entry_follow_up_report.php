<?php
/*-------------------------------------------- Comments
Purpose         :   This form will create Trims Data Entry Follow Up Report
                
Functionality   :   
JS Functions    :
Created by      :   Md. Abu Sayed
Creation date   :   29-09-2021
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
echo load_html_head_contents('Trims Data Entry Follow Up Report', '../../../', 1, 1, $unicode, 1, 1);
?>
<style>
    table tr td, table tr th {
        padding: 5px;
        vertical-align: middle;
    }
</style>
<script>
var permission='<?php echo $permission; ?>';

var tableFilters1 = 
		 {
			//col_0: "none",
			col_operation: {
				id: ["value_req_qty","value_booked_qty","value_Req_value","value_prod_qty","value_prod_val","value_prod_bal_qty","value_prod_bal_val","value_prod_rej_qty","value_prod_rej_val","value_qc_qty","value_qc_bal_qty","value_deli_qty","value_deli_val","value_deli_bal_qty","value_deli_bal_val","value_bill_qty","value_bill_amount_qty","value_bill_balance_qty","value_bill_balance_amount"],
				col: [15,17,20,23,24,25,26,27,28,29,30,31,32,33,34,36,37,38,39],
				operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}	
		}


var tableFilters2 = 
{
    //col_0: "none",
    col_operation: {
        id: ["value_req_qty","value_booked_qty","value_Req_value","value_prod_qty","value_prod_val","value_prod_bal_qty","value_prod_bal_val","value_prod_rej_qty","value_prod_rej_val","value_qc_qty","value_qc_bal_qty","value_deli_qty","value_deli_val","value_deli_bal_qty","value_deli_bal_val","value_bill_qty","value_bill_amount_qty","value_bill_balance_qty","value_bill_balance_amount"],
        col: [16,18,21,24,25,26,27,28,29,30,31,32,33,34,35,37,38,39,40],
        operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
        write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
    }   
}

var tableFilters3 = 
		 {
			//col_0: "none",
			col_operation: {
				id: ["value_Req_value","value_Req_value_taka","value_deli_val_usd","value_deli_val_taka","value_deli_bal_usd","value_deli_bal_taka"],
				col: [17,18,23,24,27,28],
				operation: ["sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}	
		}

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
    
    
    function generate_report(report_type) {
        if(document.getElementById('cbo_company_id').value==0 || (document.getElementById('txt_order_no').value==''  && document.getElementById('txt_style_ref').value=='')){
            if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From date*To date')==false )
            {
                return;
            }
        }
        
        var data="action=generate_report&report_type="+report_type+"&report_title="+$( "div.form_caption" ).html()+get_submitted_data_string('cbo_company_id*cbo_location_name*cbo_order_source*txt_order_no*cbo_customer_name*cbo_section_id*cbo_delivery_status*txt_date_from*txt_date_to*txt_style_ref',"../../../");
        freeze_window(3);
        http.open("POST","requires/trims_data_entry_follow_up_report_controller.php",true);
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
           setFilterGrid("table_body_id",-1,tableFilters1);
           setFilterGrid("table_body_id1",-1,tableFilters2);
           setFilterGrid("table_body_id2",-1,tableFilters3);
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
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/trims_data_entry_follow_up_report_controller.php?jobDtlsId='+id+'&internalRef='+internalRef+'&section='+section+'&orderRate='+orderRate+'&exchangeRate='+exchangeRate+'&action='+action, popupTitle, 'width=1000px,height=320px,center=1,resize=0', '../../');
        emailwindow.onclose=function() {}
    }

    function fnc_transfer_in_out_details(from_received_id, to_received_id, type,trns_system_no,size_id, action,popupTitle) {
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/trims_data_entry_follow_up_report_controller.php?from_received_id='+from_received_id+'&to_received_id='+to_received_id+'&type='+type+'&trns_system_no='+trns_system_no+'&size_id='+size_id+'&action='+action,popupTitle, 'width=800px,height=320px,center=1,resize=0', '../../');
        emailwindow.onclose=function() {}
    }

    function downloiadFile(id,company_name)
    {
        var title = 'Trims Order Receive File Download';    
        var page_link = 'requires/trims_data_entry_follow_up_report_controller.php?action=get_user_pi_file&id='+id+'&company_name='+company_name;
          
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
        
        emailwindow.onclose=function()
        {
            
        }

    }

</script>
</head>
<body>
    <div align="left" style="padding-left: 20px;">
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
                            <th width="100">Section</th>
                            <th colspan="2" id="th_date_caption" class="must_entry_caption">Order Receive Date Range</th>                        
                            <th width="100">Delivery Status</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:120px" class="formbutton" onClick="reset_form('monthly_capacity_booked_1','','','','')" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td>
                                    <?php
                                        echo create_drop_down('cbo_company_id', 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '--Select Company--', $selected, "load_drop_down( 'requires/trims_data_entry_follow_up_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td');load_drop_down( 'requires/trims_data_entry_follow_up_report_controller', this.value, 'load_drop_down_location', 'location_td');", '', '', '', '', '', 2);
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
                                        echo create_drop_down('cbo_order_source', 100, $order_source, '', 1, '--All--', 0, "load_drop_down( 'requires/trims_data_entry_follow_up_report_controller', (document.getElementById('cbo_company_id').value+'_'+this.value), 'load_drop_down_buyer', 'buyer_td' );", '', '', '', '', '',2);
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
                                <input type="button" name="search" id="search" value="Show 1" onClick="generate_report(1)" style="width:50px" class="formbutton" />
                                <input type="button" name="search" id="search" value="Show 2" onClick="generate_report(2)" style="width:50px" class="formbutton" /><input type="button" name="search" id="search" value="Show 3" onClick="generate_report(3)" style="width:50px" class="formbutton" />
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
       
    </div>
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
