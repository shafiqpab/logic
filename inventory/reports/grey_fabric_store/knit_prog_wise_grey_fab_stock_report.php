<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Knitting Program Wise Grey Fabric Stock Report [Sales Order].
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	24-04-2017
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
echo load_html_head_contents("Knitting Program Wise Grey Fabric Stock Report [Sales Order]", "../../../", 1, 1,'',1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";  
	var permission = '<? echo $permission; ?>';
	var tableFilters = 
		{
			col_27: "none",
			col_operation: {
				id: ["value_program_qnty","value_recv_qnty","value_trans_qty_in","value_totalRecv","value_knit_issue_qty","value_trans_qty_out","value_totalIssue","value_row_stock","value_roll"],
				col: [19,20,21,22,23,24,25,26,27],
				operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}	
		}
	
	function openmypage_booking_no()
	{
		if( form_validation('cbo_pocompany_id','Po Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_pocompany_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = ""//$("#cbo_year_selection").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/knit_prog_wise_grey_fab_stock_report_controller.php?action=fabricBooking_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Booking No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=420px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_data=this.contentDoc.getElementById("hidden_booking_data").value;
			if (booking_data!="")
			{
				var exdata=booking_data.split("__");
				$('#txt_booking_no').val(exdata[1]);
				$('#txt_booking_id').val(exdata[0]);
			}
		}
	}
	
	function openmypage_order()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year = $("#cbo_year").val();
		var page_link='requires/knit_prog_wise_grey_fab_stock_report_controller.php?action=order_no_search_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year='+cbo_year;
		var title='Sales Order No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=420px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_data=this.contentDoc.getElementById("hidden_booking_data").value;
			if (order_data!="")
			{
				var exdata=order_data.split("**");
				$('#txt_order_no').val(exdata[1]);
				$('#hide_order_id').val(exdata[0]);	 
			}
		}
	}
		
	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_id','Comapny Name')==false)
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var src_action="";
		if(type ==1)
		{
			src_action = "report_generate";
		}else{
			src_action = "report_generate_weight_lvl";
		}

		var data="action="+src_action+get_submitted_data_string('cbo_company_id*cbo_within_group*cbo_pocompany_id*cbo_buyer_id*cbo_year*txt_booking_no*txt_booking_id*txt_order_no*hide_order_id*txt_program_no*txt_date_from',"../../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/knit_prog_wise_grey_fab_stock_report_controller.php",true);
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
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			setFilterGrid("tbl_list_search",-1,tableFilters);
			//append_report_checkbox('table_header_1',1);
			// $("input:checkbox").hide();
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}
	
	function openmypage_receive(po_id,prog_no,booking_no,action)
	{ //alert(des_prod)
		var companyID = $("#cbo_company_id").val();
		var popup_width='670px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/knit_prog_wise_grey_fab_stock_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&prog_no='+prog_no+'&booking_no='+booking_no+'&prog_no='+prog_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}
	
	function openmypage_issue(po_id,prog_no,booking_no,action)
	{ //alert(des_prod)
		var companyID = $("#cbo_company_id").val();
		var popup_width='670px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/knit_prog_wise_grey_fab_stock_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&prog_no='+prog_no+'&booking_no='+booking_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}
	
	function openmypage_trans(po_id,prog_no,booking_no,action)
	{ //alert(des_prod)
		var companyID = $("#cbo_company_id").val();
		var popup_width='670px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/knit_prog_wise_grey_fab_stock_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&prog_no='+prog_no+'&booking_no='+booking_no+'&prog_no='+prog_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}

	function fnc_bookingpopup(val)
	{
		if(val==1)
		{
			$('#txt_booking_no').attr('placeholder','Browse/Write');
			$('#txt_booking_no').removeAttr("onDblClick").attr("onDblClick","openmypage_booking_no();");
		}
		else
		{
			$('#txt_booking_no').attr('placeholder','Write');
			$('#txt_booking_no').removeAttr('onDblClick','onDblClick');	
		}
	}
	
	/*function js_set_value( str ) {
		toggle( document.getElementById( 'tr_' + str), '#FFF' );
	}*/
</script>
</head>
<body onLoad="set_hotkey();">
<form id="knittingStatusReport_1">
    <div style="width:100%;" align="center">    
    <? echo load_freeze_divs ("../../../",'');  ?>
    <h3 style="width:1000px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
    <div id="content_search_panel" >      
    	<fieldset style="width:1000px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Within Group</th>
                    <th>Po Company</th>
                    <th>Po Buyer Name</th>
                    <th>Booking Year</th>
                    <th>Fabric Booking No</th>
                    <th>Sales Order No</th>
                    <th>Program No</th>
                    <th>Program Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knittingStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                </thead>
                <tbody>
                    <tr align="center" class="general">
                        <td><? echo create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", $selected, "" ); ?>
                        </td>
                        <td><? echo create_drop_down( "cbo_within_group", 70, $yes_no,"", 1, "--Select--", 0, "fnc_bookingpopup(this.value);" ); ?></td>
                        
                        <td><? echo create_drop_down( "cbo_pocompany_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Po Company-", $selected, "load_drop_down( 'requires/knit_prog_wise_grey_fab_stock_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?>
                        </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "-All Buyer-", $selected, "",0,"" ); ?></td>
                        <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                        <td>
                            <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:80px" onDblClick="openmypage_booking_no();" placeholder="Write/Browse" />
                            <input type="hidden" id="txt_booking_id" name="txt_booking_id"/>
                        </td>
                        <td>
                            <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:80px" placeholder="Browse Or Write" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');">
                            <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                        </td>
                        <td><input name="txt_program_no" id="txt_program_no" class="text_boxes_numeric" style="width:60px"></td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="Date"/></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" /><input type="button" id="show_button" class="formbutton" style="width:70px" value="Weight Lvl." onClick="fn_report_generated(2)" /></td>
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
