<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Roll wise Grey Sales Order To Sales Order Transfer Status-FSO
				
Functionality	:	
JS Functions	:
Created by		:	Md. Abu Sayed
Creation date 	: 	09-02-2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Roll wise Grey Sales Order To Sales Order Transfer Status-FSO","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

    var tableFilters = 
    {
        col_operation: {
            id: ["tot_from_stockQty","from_order_qnty","tot_f_balance_qty","tot_to_stockQty","to_order_qnty","tot_to_balance_qty"],
            col: [8,9,10,19,20,21],
            operation: ["sum","sum","sum","sum","sum","sum"],
            write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
        }
    }

	function generate_report(rpt_type)
	{
		var report_title=$( "div.form_caption" ).html(); 
		var cbo_company_id = $("#cbo_company_id").val();
		var txt_sales_order_no = $("#txt_sales_order_no").val();
		var txt_booking_no = $("#txt_booking_no").val();
		var from_date = $("#txt_date_from").val();
        var to_date = $("#txt_date_to").val();
		var hide_job_id = $("#hide_job_id").val();
        if(txt_booking_no!="" || txt_sales_order_no!="") 
        {
            if( form_validation('cbo_company_id','Company Name')==false )
            {
                return;
            }   
        }
        else 
        {
            if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Report Type*Date Form*Date To')==false )
            {
                return;
            } 
        }
	    var dataString = "&cbo_company_id="+cbo_company_id+"&txt_sales_order_no="+txt_sales_order_no+"&txt_booking_no="+txt_booking_no+"&from_date="+from_date+"&to_date="+to_date+"&report_title="+report_title+"&rpt_type="+rpt_type+"&hide_job_id="+hide_job_id;
        if (rpt_type==1) 
        {
            var action = "action=generate_report"; // Show
        }
        else
        {
            var action = "action=generate_report2"; // Show 2
        }
	    // alert(dataString);return;
		var data=action+dataString;
		// alert (data);return;
		freeze_window(1);
		http.open("POST","requires/roll_wise_grey_sales_order_to_sales_order_transfer_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			//alert (reponse[2]);return;
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
            setFilterGrid("tbl_sales_order_transfer",-1,tableFilters);
            //setFilterGrid("tbl_booking_status",-1);
			//setFilterGrid("tbl_transfer_status",-1);
	 		show_msg('3');
			release_freezing();
		}
	}

    function open_mypage_fso(company_id,mst_ids)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/roll_wise_grey_sales_order_to_sales_order_transfer_controller.php?mst_ids='+mst_ids+"&company_id="+company_id+'&action=fso_dtls_popup', 'Details Veiw', 'width=910, height=350px,center=1,resize=1,scrolling=0','../../');
	}

    function openmypage_job() // Sales Order NO function
    {
        if (form_validation('cbo_company_id', 'Company Name') == false) {
            return;
        }

        var companyID = $("#cbo_company_id").val();
        var page_link = 'requires/roll_wise_grey_sales_order_to_sales_order_transfer_controller.php?action=style_ref_search_popup&companyID=' + companyID;
        ;
        var title = 'Style Ref./ Job No. Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=840px,height=400px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var job_no = this.contentDoc.getElementById("hide_job_no").value;
            var job_id = this.contentDoc.getElementById("hide_job_id").value;

            $('#txt_sales_order_no').val(job_no);
            $('#hide_job_id').val(job_id);
        }
    }

    function openmypage_booking() 
    {
        if (form_validation('cbo_company_id', 'Company Name') == false) {
            return;
        }
        var companyID = $("#cbo_company_id").val();
        var page_link = 'requires/roll_wise_grey_sales_order_to_sales_order_transfer_controller.php?action=booking_no_search_popup&companyID=' + companyID;
        var title = 'Booking Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=370px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var booking_no = this.contentDoc.getElementById("hidden_booking_no").value;
            var booking_num = this.contentDoc.getElementById("hidden_booking_num").value;

            $('#txt_booking_no').val(booking_no);
            $('#hide_booking_id').val(booking_num);
        }
    }
	

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>  		 
    <form name="orderwisegreyfabricstock_1" id="orderwisegreyfabricstock_1" autocomplete="off" > 
    <h3 style="width:900px; margin-top:10px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:900px;">
                <table class="rpt_table" width="900" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="120" class="must_entry_caption">Company</th>
                            <th>Booking No.</th>
                          	<th>Sales Order NO</th>
                            <th class="must_entry_caption">Transfer Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('orderwisegreyfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr class="general">
                    	<td>
                            <? 
                                echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                //load_drop_down( 'requires/roll_wise_grey_sales_order_to_sales_order_transfer_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );
                            ?>                            
                        </td>
                        <td> 
                            <!-- <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:120px" placeholder="Write"/> -->

                            <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:115px" placeholder="Browse Or Write" onDblClick="openmypage_booking();"/>
                            <input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
                        </td>
                        <td> 
                            <!-- <input type="text" id="txt_sales_order_no" name="txt_sales_order_no" class="text_boxes" style="width:120px" placeholder="Write"/> -->

                            <input type="text" name="txt_sales_order_no" id="txt_sales_order_no" class="text_boxes" style="width:115px" placeholder="Browse Or Write" onDblClick="openmypage_job();" />
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:70px;" />
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;"/>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />
                            <input type="button" name="search" id="search" value="Show 2" onClick="generate_report(2)" style="width:80px" class="formbutton" />
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    	<tr>
                            <td colspan="8" align="center"><? echo load_month_buttons(1);  ?></td>
                        </tr>
                    </tfoot>
                </table> 
            </fieldset> 
        </div>
        <br />
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div>   
    </form>    
</div>    
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
