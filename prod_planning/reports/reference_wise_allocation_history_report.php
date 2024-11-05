<?
/*-------------------------------------------- Comments -----------------------
Purpose         :   This Form Will Create Reference Wise Allocation History Report.
Functionality   :   
JS Functions    :
Created by      :   Rezoanul Antu
Creation date   :   22-01-2024
Updated by      :       
Update date     :          
QC Performed BY :       
QC Date         :   
Comments        :
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Dia Wise Knitting Program Summary", "../../", 1, 1,'',1,1);

?>  

<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
    var permission = '<? echo $permission; ?>';
    
    function fn_report_generated(type)
    {
        if (form_validation('cbo_company_name','Comapny Name')==false)
        {
            return;
        }
        var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_name*cbo_within_group*cbo_buyer_name*cbo_year*cbo_booking_type*txt_booking_id*cbo_date_type*txt_lot*txt_date_from*txt_date_to*txt_product_id',"../../");
        freeze_window(3);
        http.open("POST","requires/reference_wise_allocation_history_report_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }
        
    function fn_report_generated_reponse()
    {
        if(http.readyState == 4) 
        {
            var response=trim(http.responseText).split("####");
            $('#report_container2').html(response[0]);
            
            document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:100px"/>'; 

            show_msg('3');
            release_freezing();
        }
        
    }
        
    function new_window()
    {
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'+
        '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close();
    }
    function openmypage_order()
    {
        if(form_validation('cbo_company_name','Company Name')==false)
        {
            return;
        }
        
        var companyID = $("#cbo_company_name").val();
        var page_link='requires/reference_wise_allocation_history_report_controller.php?action=order_no_search_popup&companyID='+companyID;
        var title='Order No Search';
        
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=870px,height=390px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var order_no=this.contentDoc.getElementById("hide_order_no").value;
            var order_id=this.contentDoc.getElementById("hide_order_id").value;
            
            $('#txt_order_no').val(order_no);
            $('#hide_order_id').val(order_id);   
        }
    }

    function openmypage_booking()
    {
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        var companyID = $("#cbo_company_name").val();
        var buyerID = $("#cbo_buyer_name").val();


        var page_link='requires/reference_wise_allocation_history_report_controller.php?action=booking_no_search_popup&companyID='+companyID+'&buyerID='+buyerID;
        var title='FSO/Booking No Popup';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=420px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var theemail=this.contentDoc.getElementById("selected_booking_no");
            var theemail_id=this.contentDoc.getElementById("selected_booking_id");
            //var response=theemail.value.split('_');
            if (theemail.value!="")
            {
                freeze_window(5);
                //document.getElementById("txt_booking_id").value=theemail.value;
                document.getElementById("txt_booking_no").value=theemail.value;
                document.getElementById("txt_booking_no_hdn").value=theemail.value;
                document.getElementById("txt_booking_id").value=theemail_id.value;
                release_freezing();
            }
        }
    }

    function openmypage_mc_group()
    {
        if(form_validation('cbo_company_name','Company Name')==false)
        {
            return;
        }
        
        var companyID = $("#cbo_company_name").val();
        var page_link='requires/dia_wise_program_report_controller.php?action=mc_group_search_popup&companyID='+companyID;
        var title='Machine Group Search';
        
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=870px,height=390px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var theemail=this.contentDoc.getElementById("selected_booking");
            var response=theemail.value.split('_');
            if (theemail.value!="")
            {
                freeze_window(5);
                document.getElementById("txt_mc_group").value=response[0];
                document.getElementById("hide_mc_group_id").value=response[1];
                release_freezing();
            }

            /*var theform=this.contentDoc.forms[0];
            var order_no=this.contentDoc.getElementById("hide_order_no").value;
            var order_id=this.contentDoc.getElementById("hide_order_id").value;

            $('#txt_mc_group').val(order_no);
            $('#hide_mc_group_id').val(order_id); */  
        }
    }

    function change_date_caption(date_type)
    {
        if(date_type==1)
        {            
            var caption = "Allocation Date";
        }
        else if(date_type==2)
        {
            var caption = "FSO Date";
        }
        else if(date_type==3)
        {
            var caption = "Booking Date";
        }
        else if(date_type==4)
        {
            var caption = "Shipment Date";
        }
        $("#date_caption").html(caption);
    }

    function openmypage_item()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		/*var txt_produc_no = $("#txt_product_no").val();
		var txt_produc_id = $("#txt_product_id").val();
		var txt_product = $("#txt_product").val();
		var page_link='requires/item_ledger_report_controller.php?action=item_description_search&company='+company+'&txt_produc_no='+txt_produc_no+'&txt_produc_id='+txt_produc_id+'&txt_product='+txt_product; */
		var page_link='requires/reference_wise_allocation_history_report_controller.php?action=item_description_search&company='+company; 
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=0,scrolling=0','../../')
        emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			var prodNo=this.contentDoc.getElementById("txt_selected_no").value; // product Serial No
			$("#txt_lot").val(prodDescription);
			$("#txt_product_id").val(prodID);
			$("#txt_product_no").val(prodNo); 
		}
	}
</script>

</head>
 
<body onLoad="set_hotkey();">
    <form id="knittingStatusReport_1">
        <div style="width:100%;" align="center">    
            <? echo load_freeze_divs ("../../",'');  ?>
            <h3 style="width:1300px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
            <div id="content_search_panel" >      
            <fieldset style="width:1300px;">
                <table class="rpt_table" width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>
                        <th class="must_entry_caption">Company Name</th>
                        <th>Within Group</th>
                        <th>Buyer Name</th>
                        <th>Year</th>
                        <th>Booking Type</th>
                        <th>Booking/FSO No</th>
                        <th>Date Criteria</th>                   
                        <th>Yarn Lot</th>
                        <th id="date_caption">FSO Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knittingStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:100px" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> 
                                <?
                                    echo create_drop_down( "cbo_company_name", 150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/reference_wise_allocation_history_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                ?>
                            </td>
                            <td>
                                <?php echo create_drop_down("cbo_within_group", 100, $yes_no, "", 0, "", 1, "", 0); ?>
                            </td>  
                            <td id="buyer_td">
                                <? 
                                    echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                                ?>
                            </td>
                            <td> 
                                <?
                                    echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                                ?>
                            </td>
                            <td>
                                <? $booking_type_arr = array(1 => 'Main Fabric Booking', 2 => 'Partial Fabric Booking', 3 => 'Short Fabric Booking', 4 => 'Sample Fabric Booking With Order', 5 => 'Sample Fabric Booking Without Order'); ?>
                                <?php echo create_drop_down("cbo_booking_type", 100, $booking_type_arr, "", 1, "All", 0, "", 0); ?>
                            </td>
                            <td>
                                <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:80px" onDblClick="openmypage_booking();" placeholder="FSO Write/Browse" />
                                <input type="hidden" name="txt_booking_id" id="txt_booking_id"/>   
                                <input type="hidden" name="txt_booking_no_hdn" id="txt_booking_no_hdn"/> 
                            </td>
                            <td>
                                <? $date_type_arr = array(1 => 'Allcoation Date', 2 => 'FSO Date', 3 => 'Booking Date', 4 => 'Shipment Date'); ?>
                                <?php echo create_drop_down("cbo_date_type", 100, $date_type_arr, "", 0, "", 2, "change_date_caption(this.value);", 0); ?>
                            </td>
                            <td>
                                <input type="text" id="txt_lot" name="txt_lot" class="text_boxes" style="width:80px" onDblClick="openmypage_item();" placeholder="Browse" />
                                <input type="hidden" name="txt_product_id" id="txt_product_id"/>   
                                <input type="hidden" name="txt_product_no" id="txt_product_no"/> 
                            </td>  
                            <td>
                                <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px;" placeholder="From Date" readonly/>&nbsp;To&nbsp;
                                <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px;" placeholder="To Date" readonly/>                
                            </td>
                            <td>
                                <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated(1)" />
                            </td>
                        </tr>
                        <tr>
                            <td align="center" colspan="14">
                                <? echo load_month_buttons(1); ?>
                            </td>
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
