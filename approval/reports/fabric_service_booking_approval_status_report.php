<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Fabric Service Booking Approval Status Report.
Functionality	:	
JS Functions	:
Created by		:	Md Rakib
Creation date 	: 	10-08-2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Fabric Service Booking Approval Status", "../../", 1, 1, '', 1, 1);
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';
	 
	function fn_report_generated()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*txt_job_no*txt_fabric_service_booking_no*cbo_booking_type*txt_booking_date_from*txt_booking_date_to*txt_approval_date*cbo_year_selection*cbo_buyer_name*cbo_approve_type',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/fabric_service_booking_approval_status_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			var tableFilters = { col_0: "none" }
			setFilterGrid("tbl_list_search",-1);
			show_msg('3');
			release_freezing();
	 	}		
	}	

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#tbl_list_search tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'+
	   '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
		
		$("#tbl_list_search tr:first").show();
	}

	function fabric_booking_req_report(print_btn,txt_booking_no,cbo_company_name,hidden_supplier_id,booking_mst_id){

		var show_rate="0";
		var show_buyer="0";

		if(print_btn==11){

		$action="show_trim_booking_bpkw_report";

		}
		else if(print_btn==59){
			var r=confirm("Press  \"Cancel\"  to Show  Rate and Amount\nPress  \"OK\"  to Hide Rate and Amount");
			if (r==true) show_rate="1"; else show_rate="0";

			var action="show_trim_booking_bpkw_report";

		}
		else if(print_btn==12){
			var action="show_trim_booking_report1";
			
		}
		else if(print_btn==136){
			var r=confirm("Press  \"Cancel\"  to Hide Buyer and Style \nPress  \"OK\"  to Show Buyer and Style ");
			if (r==true) show_buyer="1"; else show_buyer="0";

			var action="show_trim_booking_report3"
		}
		else{
			var action="show_trim_booking_report2";	

		}

		window.open("../../../order/woven_order/requires/service_booking_controller.php?txt_booking_no='" +txt_booking_no+"'&cbo_company_name="+cbo_company_name+"&hidden_supplier_id="+hidden_supplier_id+"&booking_mst_id="+booking_mst_id+"&show_rate="+show_rate+"&show_buyer="+show_buyer+"&action="+action, true );
		}
</script>
</head>
<body onLoad="set_hotkey();">
<form id="priceQuotationApprovalReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
         <h3 style="width:1150px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel">
         <fieldset style="width:1150px;">
             <table class="rpt_table" width="1150" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
					<th>Job No</th>
					<th>Fabric Service Booking No</th>
					<th>Booking Type</th>
					<th>Booking Date Range</th> 
					<th>Approval Date</th>
					<th>Buyer Name</th>
					<th>Approval Type</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('priceQuotationApprovalReport_1','report_container*report_container2','','','')" class="formbutton" style="width:80px"/></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/fabric_service_booking_approval_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
						<td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px" ></td>
						<td><input name="txt_fabric_service_booking_no" id="txt_fabric_service_booking_no" class="text_boxes" style="width:100px" ></td>

						<td>
							<? echo create_drop_down( "cbo_booking_type", 120, $booking_type_arr,"", 1, "-Select-", $selected, "",0); ?>
						</td>

						<td>
							<input type="text" name="txt_booking_date_from" id="txt_booking_date_from" class="datepicker" style="width:70px;" placeholder="From Date" />                    							
                            To
                            <input type="text" name="txt_booking_date_to" id="txt_booking_date_to" placeholder="To Date" class="datepicker" style="width:70px;" />
						</td>

						<td>
							<input type="text" name="txt_approval_date" id="txt_approval_date" class="datepicker" style="width:80px;"  />
						</td>

						<td id="buyer_td">
							<?
								echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
							?>
                        </td>

						<td>
                        	<?
								$search_by_arr=array(1=>"Pending",3=>"Approved");
								echo create_drop_down("cbo_approve_type", 100, $search_by_arr, "", 0, "", "", "", 0);
							?>
                        </td> 
						                      
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated()"/>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="9" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
    	</div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="center"></div>
    </div>
 </form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
