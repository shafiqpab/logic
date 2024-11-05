<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Date wise fabric booking report controller.
Functionality	:
JS Functions	:
Created by		:	Zakaria joy
Creation date 	: 	17-07-2019
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
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

$fabric_booking_type = array( 0 =>'-- Select --',1 => "Main Fabric Booking",2=>'Partial Fabric Booking', 3 => "Short Fabric Booking", 4 => "Sample Fabric Booking - With Order", 5 => 'Sample Fabric Booking - Without Order');
$booking_status_arr = array(1 => "Approved",2 => "Un-Approved");
echo load_html_head_contents("Date Wise Fabric Booking", "../../", 1, 1,$unicode,1,1);
?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	function fn_report_generated(type)
	{
		console.log(type);
		freeze_window(3);
		 if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*date from*date to')==false )
		 {
		 	release_freezing();
			return;
		 }
		else
		{
			console.log("here");
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_fab_booking_type*cbo_fabric_source*cbo_party_id*cbo_pay_mode*txt_date_from*txt_date_to*txt_booking_no_id*txt_booking_no*cbofabricnature',"../../")+'&report_title='+report_title+'&type='+type;
			
			http.open("POST","requires/date_wise_fabric_booking_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			//alert(reponse[3])
			if(trim(reponse[3])==0)
			{
				if(tot_rows*1>1){
					var tableFilters = {
						col_operation: {
						   id: ["total_booking_qty_kg","total_booking_qty_yds","total_booking_qty_mtr","total_booking_amount"],
						   col: [17,18,19,20],
						   operation: ["sum","sum","sum","sum"],
						   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					 }
					 setFilterGrid("table_body",-1,tableFilters);
				}
			}
			else if(trim(reponse[3])==2)
			{
				if(tot_rows*1>1){
					var tableFilters = {
						col_operation: {
						   id: ["value_qtykg","value_qtyyds","value_qtymtr","value_amount"],
						   col: [21,22,23,24],
						   operation: ["sum","sum","sum","sum"],
						   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					 }
					 setFilterGrid("table_body",-1,tableFilters);
				}
			}
			else if(trim(reponse[3])==1)
			{
				if(tot_rows*1>1){
					
					 setFilterGrid("table_body",-1);
				}
			}

			//document.getElementById('report_container').innerHTML=report_convert_button('../../');
			//$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;';
	 		show_msg('3');
			release_freezing();
		}
	}
	
	function openmypage_fabricBooking()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_fab_booking_type = $('#cbo_fab_booking_type').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Booking Selection Form';
			var page_link = 'requires/date_wise_fabric_booking_report_controller.php?cbo_company_id='+cbo_company_id+'&cbo_fab_booking_type='+cbo_fab_booking_type+'&action=fabricBooking_popup';
			var popup_width="1070px";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=400px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var theemail=this.contentDoc.getElementById("hidden_booking_id").value;	 //Knit Id for Kintting Plan
				var theename=this.contentDoc.getElementById("hidden_booking_no").value; //all data for Kintting Plan
				if(theemail!="")
				{
					freeze_window(5);
					
					$('#txt_booking_no').val(theename);
					$('#txt_booking_no_id').val(theemail);
					release_freezing();
				} 
			}
		}
	}
	
	function fnRemoveHidden(str){
		document.getElementById(str).value='';
	}
</script>

</head>
<body onLoad="set_hotkey();">
<form id="bookingreport_1">
<div style="width:100%; margin:1px auto;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <h3 style="width:1100px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:1100px" >
            <fieldset style="width:1100px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <th width="150" class="must_entry_caption">Company Name</th>
                    <th width="150">Buyer</th>
                    <th width="100">Fab Nature</th>
                    <th width="120">Booking Type</th>
                    <th width="100">Source</th>
                    <th width="100">Pay Mode</th>
                    <th width="150">Party Name</th>
                    <th width="100">Booking No</th>
                   <!--  <th width="100">Approval Status</th> -->
                    <th width="140" class="must_entry_caption" colspan="2">Booking Date Range</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('bookingreport_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/date_wise_fabric_booking_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 150, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, ""); ?></td>
                        <td><? echo create_drop_down( "cbofabricnature", 100, $item_category, "", 1,"All Type", $selected, "","","2,3" ); ?></td>
                        <td><? echo create_drop_down( "cbo_fab_booking_type", 120, $fabric_booking_type, "--Select Type--", $selected, ""); ?></td>
                        <td><? echo create_drop_down( "cbo_fabric_source", 100, $fabric_source,"",1,"--Select Source--", $selected, ""); ?></td>
                        <td><? echo create_drop_down( "cbo_pay_mode", 100, $pay_mode,"", 1, "-- Select Pay Mode --", $selected, "load_drop_down( 'requires/date_wise_fabric_booking_report_controller', this.value, 'load_drop_down_suplier', 'sup_td' )","","1,2,3,5" ); ?></td>
                        <td id="sup_td"><? echo create_drop_down( "cbo_party_id", 150, $blank_array, "", 1,"-- Select Party --", $selected, "" ); ?></td>
                       <!--  <td><? //echo create_drop_down( "cbo_approval_status", 100, $booking_status_arr, "", 1,"All Type", $selected, "" ); ?></td> -->
                       
						<td><input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:90px" placeholder="Browse/Write" onChange="fnRemoveHidden('txt_booking_no_id');" onDblClick="openmypage_fabricBooking();" > <input type="hidden" name="txt_booking_no_id" id="txt_booking_no_id" value=""></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"/><input type="hidden" name="cbo_approval_status" id="cbo_approval_status" value="0"></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" /></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(0);" /></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="8"><?=load_month_buttons(1); ?></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Details" onClick="fn_report_generated(1);" /></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Fabric Wise" onClick="fn_report_generated(2);" /></td>
                    </tr>
                </tbody>
            </table>
        	</fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="left"></div>
    </div>
</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>