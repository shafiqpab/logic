<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	Allocation data [Sales].
Functionality	:	
JS Functions	:
Created by		:	Md. Didarul Alam
Creation date 	: 	27-05-2023
Updated by 		: 	Md. Didarul Alam	
Update date		: 	12-12-2023 [custom quantity allocation free]	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Allocation dasta free [sales] ", "../", 1, 1, '', 1, 1);
?>
<script>
    if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";
    var permission = '<? echo $permission; ?>';

    function fn_report_generated()
	{
        if (form_validation('cbo_company_name', 'Comapny Name') == false)
		{
            return;
        }

        var txt_sales_no = $("#txt_sales_no").val();
        var txt_booking_no = $("#txt_booking_no").val();
        var txt_product = $("#txt_product").val();
        var txt_lot_no = $("#txt_lot_no").val();
        var cbo_cust_buyer_id = $("#cbo_cust_buyer_id").val()*1;
        var cbo_buyer_id = $("#cbo_buyer_id").val()*1;

        var txt_date_from = $("#txt_date_from").val();
        var txt_date_to = $("#txt_date_to").val();

        if(txt_sales_no =="" && txt_booking_no =="" && txt_product ==""  && txt_lot_no ==""  && cbo_cust_buyer_id ==0 && cbo_buyer_id ==0)
        {
            if(txt_date_from =="" && txt_date_to =="")
            {
                alert("Please select either date range or any one ref like sales order, booking no ETC");
                return;
            }
        }
      
        var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_buyer_id*cbo_cust_buyer_id*txt_product*txt_lot_no*hidden_prod_no*cbo_within_group*txt_sales_no*hidden_fso_id*txt_booking_no*hidden_booking_id*txt_date_from*txt_date_to*cbo_year_selection', "../");
        //alert(data);
        freeze_window(3);
        http.open("POST", "requires/yarn_allocation_data_free_sales_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }

    function fn_report_generated_reponse()
	{
        if (http.readyState == 4)
		{
            var response = trim(http.responseText).split("####");
            $('#report_container2').html(response[0]);
            document.getElementById('report_container').innerHTML = '<a href="' + response[1] + '" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:120px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:100px"/>';
            var company_id = $("#cbo_company_name").val();
            show_msg('3');
            release_freezing();
        }
    }

    function openmypage_booking()
	{
        if (form_validation('cbo_company_name', 'Company Name') == false)
		{
            return;
        }

        var companyID = $("#cbo_company_name").val();
        var page_link = 'requires/yarn_allocation_data_free_sales_controller.php?action=booking_no_search_popup&companyID=' + companyID;
        var title = 'Booking No Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function ()
		{
            var theform = this.contentDoc.forms[0];
            var booking_no = this.contentDoc.getElementById("hide_booking_no").value.split("_");

            $('#hidden_booking_id').val(booking_no[0]);
            $('#txt_booking_no').val(booking_no[1]);
        }
    }

    function openmypage_sales_no()
	{
        if (form_validation('cbo_company_name', 'Company Name') == false)
		{
            return;
        }

        var companyID = $("#cbo_company_name").val();
        var within_group = $("#cbo_within_group").val();
        var page_link = 'requires/yarn_allocation_data_free_sales_controller.php?action=job_no_search_popup&companyID=' + companyID+'&within_group='+within_group;
        var title = 'Sales No Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=890px,height=390px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var fso_no=this.contentDoc.getElementById("hide_job_no").value;
            var fso_id=this.contentDoc.getElementById("hide_job_id").value;
            $('#txt_sales_no').val(fso_no);
            $('#hidden_fso_id').val(fso_id);
        }
    }

    function new_window()
	{
        document.getElementById('scroll_body').style.overflow = "auto";
        document.getElementById('scroll_body').style.maxHeight = "none";

        //$("#tbl_list_search").find('input([name="check"])').hide();
        $('input[type="checkbox"]').hide();

        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
            '<html><head><title></title><link rel="stylesheet" type="text/css" href="css/style_common.css" media="print" /></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
        d.close();
        $('input[type="checkbox"]').show();
        document.getElementById('scroll_body').style.overflowY = "scroll";
        document.getElementById('scroll_body').style.maxHeight = "330px";
    }
	
    function openmypage_item()
    {
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        var company = $("#cbo_company_name").val(); 
        var page_link='requires/yarn_allocation_data_free_sales_controller.php?action=item_description_search&company='+company; 
        var title="Search Item Popup";
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0]; 
            var prodID=this.contentDoc.getElementById("txt_selected_id").value; // product ID
            $("#txt_product").val(prodID);
        }
    }

    function fnc_lot_no() 
    {
        if (form_validation('cbo_company_name', 'Company Name') == false)
        {
            return;
        }
        var cbo_company_name = $("#cbo_company_name").val();

        var page_link = 'requires/yarn_allocation_data_free_sales_controller.php?action=lot_no_search&cbo_company_name=' + cbo_company_name;
        var title = "Search Lot No Popup";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0', '')
        emailwindow.onclose = function ()
        {
            var theform = this.contentDoc.forms[0];
            var prod_id = this.contentDoc.getElementById("hidden_product").value;
            var lot = this.contentDoc.getElementById("hidden_lot").value;
            $("#hidden_prod_no").val(prod_id);
            $("#txt_lot_no").val(lot);
        }
    }

    function func_allocation_free(sl,free_ref,reducible_wo_qty,reducible_requisition_qty)
    {    
        var ydsw_free_qty = $("#txt_wo_free_qty__"+sl).val()*1;
        var requisition_free_qty = $("#txt_requisition_free_qty__"+sl).val()*1;    

        if( !$("#full_allocation_free_yes").is(':checked') && ydsw_free_qty==0 && requisition_free_qty==0 )
        {
            alert("Please fill up YDSW. Free/Requisition Free");
            return;
        }

        if(ydsw_free_qty>reducible_wo_qty || requisition_free_qty>reducible_requisition_qty) 
        {
            alert("Quantity is not available");
            return;
        }
       
        var data = "action=yarn_allocation_free&free_ref="+free_ref+"&ydsw_free_qty="+ydsw_free_qty+"&requisition_free_qty="+requisition_free_qty+get_submitted_data_string('cbo_company_name*cbo_buyer_id*cbo_cust_buyer_id*txt_product*txt_lot_no*hidden_prod_no*cbo_within_group*txt_sales_no*hidden_fso_id*txt_booking_no*hidden_booking_id*txt_date_from*txt_date_to*cbo_year_selection*full_allocation_free_yes', "../");

        freeze_window(3);
        http.open("POST", "requires/yarn_allocation_data_free_sales_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = func_allocation_free_reponse;
        
    }	

    function func_allocation_free_reponse()
	{
        if (http.readyState == 4)
		{
            var response = trim(http.responseText).split("####");
            if(response[0]==0)
            {
                show_msg('3');
                release_freezing();
                fn_report_generated();
            }
            else{
                release_freezing();
            }
            
            
        }
    }

</script>
</head>
<body onLoad="set_hotkey();">


    <div style="width:100%;" align="center">
		<? echo load_freeze_divs("../", ''); ?>
        <form name="yarnAllocationDataFreeSales_1" id="yarnAllocationDataFreeSales_1_1" autocomplete="off" > 
        <h3 style="width:1080px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
        <div id="content_search_panel">
            <fieldset style="width:1080px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>
                        <th class="must_entry_caption">Company Name</th>
                        <th>Buyer</th>
                        <th>Cust. Buyer</th>
                        <th>Prod. ID</th>
                        <th>Lot</th>
                        <th>Within Group</th>
                        <th>Sales Order</th>
                        <th>Booking No</th>
                        <th>Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('yarnAllocationDataFreeSales_1','report_container*report_container2','','','','');" /></th>
                    </thead>
                    <tbody>
                    <tr class="general">
                        <td>
							<?
							echo create_drop_down("cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "- Select Company -", $selected, "load_drop_down( 'requires/yarn_allocation_data_free_sales_controller', this.value, 'load_drop_down_cust_buyer', 'cust_buyer_td');load_drop_down('requires/yarn_allocation_data_free_sales_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );"); 
							?>
                        </td>

                        <td id="buyer_td">
                            <?php echo create_drop_down("cbo_buyer_id", 100, $blank_array, "", 1, "-- Select Buyer --", 0, "",0, ''); ?>
                        </td>

                        <td id="cust_buyer_td">
                            <?php echo create_drop_down("cbo_cust_buyer_id", 100, $blank_array, "", 1, "-- Select Cust Buyer --", 0, "",0, ''); ?>
                        </td>

                        <td align="center">
                            <input style="width:80px;"  name="txt_product" id="txt_product"  onDblClick="openmypage_item()"  class="text_boxes_numeric" placeholder="Write/Brows" />            
                        </td>

                        <td>
                            <input name="txt_lot_no" id="txt_lot_no" class="text_boxes" style="width:90px" placeholder="Write">
                            <input type="hidden" id="hidden_prod_no" name="hidden_prod_no" value="">
                        </td>

                        <td>
                            <?php echo create_drop_down("cbo_within_group", 80, $yes_no, "", 1, "-- Select --", 0, ""); ?>
                        </td>

                        <td>
                            <input type="text" name="txt_sales_no" id="txt_sales_no" class="text_boxes" onDblClick="openmypage_sales_no();" style="width:100px" placeholder=" Write/Browse" autocomplete="off">
                            <input type="hidden" id="hidden_fso_id" name="hidden_fso_id" value="">
                        </td>

                        <td>
                            <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px" placeholder="Write/Browse" onDblClick="openmypage_booking();" autocomplete="off">
                            <input type="hidden" id="hidden_booking_id" name="hidden_booking_id" value="">
                        </td>
                        
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/>&nbsp;
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show"
                                   onClick="fn_report_generated()"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
</form>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>