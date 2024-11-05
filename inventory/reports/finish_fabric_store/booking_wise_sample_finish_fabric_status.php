<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Order Wise Finich Fabric Stock Report

Functionality	:
JS Functions	:
Created by		:
Creation date 	:
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
echo load_html_head_contents("Order Wise Color Finich Fabric Stock Report","../../../", 1, 1, $unicode,1,1);

?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters =
	{
		col_operation: {
		id: ["value_total_booking_quantity","value_total_today_recv","value_total_today_issue_ret","value_total_today_transfer_in","value_total_recv","value_total_issue_ret","value_total_transfer_in","value_total_receive_balance","value_total_today_issue","value_total_today_recv_return","value_total_today_trans_out","value_total_issue","value_total_recv_return","value_total_trans_out","value_total_stock_qty"],
		col: [12,13,14,15,16,17,18,19,20,21,22,23,24,25,26],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function generate_report(type)
	{
		var company_name=document.getElementById('cbo_company_id').value;
		var buyer_id=document.getElementById('cbo_buyer_id').value;
		var job_no=document.getElementById('cbo_search_by').value;
		var book_no=document.getElementById('txt_search_comm').value;
		var txt_ref_no=document.getElementById('cbo_year').value;    		
		var store_id=document.getElementById('cbo_store_id').value;    		

		if(form_validation('cbo_company_id*txt_date_from','Company*Date')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_search_by*txt_search_comm*cbo_year*cbo_value_with*cbo_store_id*txt_date_from',"../../../")+'&report_title='+report_title+'&cbo_report_type='+type;

		freeze_window(3);
		http.open("POST","requires/booking_wise_sample_finish_fabric_status_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			if(typeof(reponse[1]) != 'undefined') 
			{
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				setFilterGrid("table_body",-1,tableFilters);
				show_msg('3');
			}

			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('#table_body tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
	}

	function openmypage_booking()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('cbo_year').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/booking_wise_sample_finish_fabric_status_controller.php?action=booking_no_popup&data='+data,'Booking No Popup', 'width=800px,height=420px,center=1,resize=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hdn_booking_id=this.contentDoc.getElementById("hdn_booking_id").value;
            var hdn_booking_no=this.contentDoc.getElementById("hdn_booking_no").value;
			document.getElementById("txt_book_no").value=hdn_booking_no;
			document.getElementById("txt_book_id").value=hdn_booking_id;
		}
	}

	function openmypage(po_id,body_part_id,color,action)
	{
		var companyID = $("#cbo_company_id").val();
		var popup_width='600px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/booking_wise_sample_finish_fabric_status_controller.php?companyID='+companyID+'&po_id='+po_id+'&body_part_id='+body_part_id+'&color='+color+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}


	function openmypage_qnty(booking_no,prod_ref,action,from_date,transType,is_today)
	{
		var companyID = $("#cbo_company_id").val();
		var popup_width='800px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/booking_wise_sample_finish_fabric_status_controller.php?companyID='+companyID+'&booking_no='+booking_no+'&prod_ref='+prod_ref+'&action='+action+'&from_date='+from_date+'&transType='+transType+'&is_today='+is_today, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}


	function getCompanyId() 
	{
	    var company_id = $("#cbo_company_id").val();
	    load_drop_down( 'requires/booking_wise_sample_finish_fabric_status_controller',company_id, 'load_drop_down_store', 'store_td' );
	    set_multiselect('cbo_store_id','0','0','','0');
	    //load_drop_down( 'requires/booking_wise_sample_finish_fabric_status_controller',company_id, 'load_drop_down_buyer', 'buyer_td' );       
	}

	function change_caption(type)
	{
		if(type==1)
		{
			$('#td_search').html('Fabric Booking');
		}
		else if(type==2)
		{
			$('#td_search').html('Internal Referece');
		}
		else if(type==3)
		{
			$('#td_search').html('Sample Requisition');
		}
	}
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>
    <form name="ordewisefinishfabricstock_1" id="ordewisefinishfabricstock_1" autocomplete="off" >
    <h3 style="width:790px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:790px;">
                <table class="rpt_table" width="790" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="130" class="must_entry_caption">Company</th>
                            <th width="100">Buyer</th>
                            <th width="150">Search By</th>
                            <th width="120" id="td_search">Fabric Booking</th>
                            <th width="80">Year</th>
                            <th width="10">value</th>
							<th width="100">Store</th>
                            <th width="100" class="must_entry_caption">Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('ordewisefinishfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr align="center">
                        <td id="company_td">
                            <?
                               echo create_drop_down( "cbo_company_id", 130, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/booking_wise_sample_finish_fabric_status_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );getCompanyId()" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <?
                                echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                           ?>
                        </td>
                        <td>
                            <?
								$search_by=array( 1=>'Fabric Booking', 2=>'Internal Referece',3=>'Sample Requisition');
                                echo create_drop_down( "cbo_search_by", 130, $search_by,"", 0, "--Select--", 1, "change_caption(this.value);",0 );
                           
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_search_comm" name="txt_search_comm" class="text_boxes" style="width:120px"  placeholder="Write" />
                        </td>
                        <td>
                            <?
								$selected_year=date("Y");
                                echo create_drop_down( "cbo_year", 80, $year,"", 1, "--Select Year--", $selected_year, "",0 );
                            ?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_value_with", 100, array(1=>'Value With 0',2=>'Value Without 0'),"", 0, "", 1, "",0 );
                           ?>
                        </td>
						<td id="store_td">
                            <?
                                echo create_drop_down( "cbo_store_id", 120, $blank_array,"", 1, "--Select Store--", 0, "",0 );
                           ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date('d-m-Y', time());?>" class="datepicker" style="width:75px;" readonly/>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                	</tbody>
                </table>
            </fieldset>
        </div>
    </form>
</div>
	<div id="report_container" align="center"></div>
    <div id="report_container2"></div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
