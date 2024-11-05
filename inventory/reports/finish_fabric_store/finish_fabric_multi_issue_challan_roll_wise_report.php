<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Finish Fabric Multi Issue Challan Report [Roll Wise].
Functionality	:
JS Functions	:
Created by		:	Abu Sayed
Creation date 	: 	07-03-2023
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
echo load_html_head_contents("Finish Fabric Multi Issue Challan Report [Roll Wise]", "../../../", 1, 1,$unicode,'','');

?>

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
var permission = '<? echo $permission; ?>';

	function fn_report_generated()
	{
		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*From Date*To Date')==false)
		{
			return;
		}

		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_cust_buyer_name*txt_fso_no*txt_booking_no*txt_batch_no*txt_date_from*txt_date_to*cbo_within_group',"../../../");
		freeze_window(3);
		http.open("POST","requires/finish_fabric_multi_issue_challan_roll_wise_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        //alert(data);
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText);
			$('#report_container').html(reponse);
			show_msg('3');
			release_freezing();
		}
	}

	function fnc_checkbox_check(rowNo)
	{
		var isChecked=$('#tbl_'+rowNo).is(":checked");

		//var issue_to=$('#cust_buyer_id_'+rowNo).val();
		var buyer_id=$('#cust_buyer_id_'+rowNo).val();

		if(isChecked==true)
		{
			var tot_row=$('#tbl_list_search tr').length-1;
			for(var i=1; i<=tot_row; i++)
			{
				if(i!=rowNo)
				{
					try
					{
						if ($('#tbl_'+i).is(":checked"))
						{
							var buyer_idCurrent=$('#cust_buyer_id_'+i).val();
							if( (buyer_id!=buyer_idCurrent) )
							{
								alert("Please Select Same Customer Buyer...");
								$('#tbl_'+rowNo).attr('checked',false);
								return;
							}
						}
					}
					catch(e){}
				}
			}
		}
	}

	function fn_with_source_report(operation)
	{
	 	if(operation==1) // Print
		{
			var master_ids = ""; var total_tr=$('#tbl_list_search tr').length;
			for(i=1; i<total_tr; i++)
			{
				try
				{
					if ($('#tbl_'+i).is(":checked"))
					{
						master_id = $('#mstidall_'+i).val();
						if(master_ids=="") master_ids= master_id; else master_ids +='_'+master_id;
					}
				}
				catch(e){}
			}
			if(master_ids=="")
			{
				alert("Please Select At Least One Item");
				return;
			}
			freeze_window(3);
			//alert(master_ids);
			var report_title="Delivery Challan";
			print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title+'*'+$('#txt_delivery_date').val(), "delivery_challan_print", "requires/finish_fabric_multi_issue_challan_roll_wise_report_controller" );
			release_freezing();
			return;
		}
	}

	function openmypage_sales_no()
	{
        if (form_validation('cbo_company_name', 'Company Name') == false) {
            return;
        }

        var companyID = $("#cbo_company_name").val();
        var page_link = 'requires/finish_fabric_multi_issue_challan_roll_wise_report_controller.php?action=job_no_search_popup&companyID=' + companyID;
        var title = 'Sales No Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=890px,height=390px,center=1,resize=1,scrolling=0', '../../');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var sales_no = this.contentDoc.getElementById("hide_job_no").value.split("_");
            //var job_no=this.contentDoc.getElementById("hide_job_no").value;
            //var job_id=this.contentDoc.getElementById("hide_job_id").value;

            $('#txt_fso_no').val(sales_no[1]);
            //$('#hide_job_id').val(order_id);
        }
    }

	function openmypage_booking()
	{
        if (form_validation('cbo_company_name', 'Company Name') == false)
		{
            return;
        }

        var companyID = $("#cbo_company_name").val();
        var page_link = 'requires/finish_fabric_multi_issue_challan_roll_wise_report_controller.php?action=booking_no_search_popup&companyID=' + companyID;
        var title = 'Booking No Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0', '../../');
        emailwindow.onclose = function ()
		{
            var theform = this.contentDoc.forms[0];
            //var machine_no=this.contentDoc.getElementById("hide_machine").value.split("_");
            var booking_no = this.contentDoc.getElementById("hide_booking_no").value.split("_");
            //var order_id=this.contentDoc.getElementById("hide_order_id").value;

            $('#txt_booking_no').val(booking_no[1]);
            //$('#hide_order_id').val(order_id);
        }
    }

</script>
</head>

<body onLoad="set_hotkey();">
<form id="">
    <div style="width:100%;" align="center">

        <? echo load_freeze_divs ("../../../",'');  ?>

         <fieldset style="width:1110px;">
        	<legend>Search Panel</legend>
			<div align="left">
        		<b>Delivery Date : </b> <input type="text"  name="txt_delivery_date" id="txt_delivery_date" class="datepicker" style="width:70px;" value="<? echo date("d-m-Y"); ?>" readonly>
        	</div>
            <table class="rpt_table" width="1110px" cellpadding="0" cellspacing="0" align="center">
               <thead>
                    <tr>
                        <th class="must_entry_caption">Company Name</th>
						<th>Within Group</th>
                        <th>Buyer Name</th>
                        <th>Cust. Buyer Name</th>
                        <th>FSO No</th>
                        <th>Fabric Booking No.</th>
                        <th>Batch No</th>
                        <th id="search_text_td" class="must_entry_caption">Finish Fabric Issue Date	</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                    </tr>
                 </thead>
                <tbody>
                <tr class="general">
                    <td width="150">
                        <?
                            echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/finish_fabric_multi_issue_challan_roll_wise_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/finish_fabric_multi_issue_challan_roll_wise_report_controller',this.value, 'load_drop_down_cust_buyer', 'cust_buyer_td' );" );
                        ?>
                    </td>
					<td>
						<?php echo create_drop_down("cbo_within_group", 100, $yes_no, "", 0, "", 2, "", 0); ?>
					</td>
                    <td width="100" id="buyer_td">
                        <?
                            echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td width="100" id="cust_buyer_td">
                        <?
                            echo create_drop_down( "cbo_cust_buyer_name", 100, $blank_array,"", 1, "-- Select Cust Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td width="100">
                    	<input type="text"  name="txt_fso_no" id="txt_fso_no" class="text_boxes" style="width:100px;" onDblClick="openmypage_sales_no();"  placeholder="Write/Browse" autocomplete="off">
                    </td>
                    <td width="100">
                    	<input type="text"  name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px;" placeholder="Write/Browse" onDblClick="openmypage_booking();" autocomplete="off">
                    </td>
                    <td width="100">
                  	 <input type="text"  name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:100px;"   placeholder="Write">
                    </td>
                    <td width="">
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly >&nbsp; To
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" readonly >
                    </td>
                    <td width="110">
                        <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" />
                    </td>
                </tr>
                </tbody>
            </table>
            <table>
            	<tr>
                	<td>
 						<? echo load_month_buttons(1); ?>
                   	</td>
                </tr>
                <tr align="center">
                	<td>
                		<input id="print" class="formbutton" style="width:90px;" value="Print" name="print" onclick="fn_with_source_report(1)" type="button">
                	</td>
                </tr>
            </table>
            <br />
        </fieldset>
    </div>
    <br>
    <div id="report_container" align="center" style="width:1190px; margin: 0 auto;"></div>
 </form>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
