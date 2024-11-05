<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Order Wise Finich Fabric Stock Report

Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	24-04-2014
Updated by 		: 	Aziz
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
echo load_html_head_contents("Order Wise Finich Fabric Stock Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters =
	{
		//col_15: "none",

		// col_operation: {
		// id: ["value_total_req_qty","value_total_tranin_qty","value_total_rec_qty","value_total_issue_ret_qty","value_total_receive_qty","value_total_rec_bal","value_total_tranout_qty","value_total_issue_qty","value_total_issue_balance_qty","value_recv_ret_qty","value_total_issue_quantity","value_total_stock","total_possible_cut_pcs","total_actual_cut_qty"],
		// col: [13,14,15,16,17,18,19,20,21,22,23,24,26,27],
		// operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		// write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		// }

		/*col_operation: {
		id: ["value_total_req_qty","value_total_tranin_qty","value_total_rec_qty","value_total_issue_ret_qty","value_total_receive_qty","value_total_rec_bal","value_total_tranout_qty","value_total_issue_qty","value_total_issue_balance_qty","value_recv_ret_qty","value_total_issue_quantity","value_total_stock","total_possible_cut_pcs","total_actual_cut_qty"],
		col: [14,15,16,17,18,19,20,21,22,23,24,25,28,29],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}*/

		col_operation: {
		id: ["value_total_req_qty","value_total_tranin_qty","value_total_rec_qty","value_total_issue_ret_qty","value_total_receive_qty","value_total_rec_bal","value_total_tranout_qty","value_total_issue_qty","value_total_issue_cut_qty","value_total_issue_balance_qty","value_recv_ret_qty","value_total_issue_quantity","value_total_stock","total_possible_cut_pcs","total_actual_cut_qty"],
		col: [14,15,16,17,18,19,20,21,22,23,24,25,26,29,30],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	var tableFilters2 =
	{
		//col_15: "none",
		col_operation: {
		id: ["value_total_req_qty","value_total_tranin_qty","value_total_rec_qty","value_total_issue_ret_qty","value_total_receive_qty","value_total_rec_bal","value_total_tranout_qty","value_total_issue_qty","value_total_issue_balance_qty","value_recv_ret_qty","value_total_issue_quantity","value_total_stock","total_possible_cut_pcs","total_actual_cut_qty"],
		col: [9,10,11,12,13,14,15,16,17,18,19,20,22,23],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	//------------------
	var tableFilters55 =
	{
		col_operation: {
		id: ["value_total_req_qty","value_total_tranin_qty","value_total_rec_qty","value_total_issue_ret_qty","value_total_receive_qty","value_total_rec_bal","value_total_tranout_qty","value_total_issue_qty","value_total_issue_qty_reprocess","value_total_issue_balance_qty","value_recv_ret_qty","value_total_issue_quantity","value_total_stock","total_possible_cut_pcs","total_actual_cut_qty"],
		col: [14,15,16,17,18,19,20,21,22,23,24,25,26,29,30],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	var tableFilters5 =
	{
		//col_15: "none",
		col_operation: {
		id: ["value_total_req_qty","value_total_tranin_qty","value_total_rec_qty","value_total_issue_ret_qty","value_total_receive_qty","value_total_rec_bal","value_total_tranout_qty","value_total_issue_qty","value_total_issue_qty_reprocess","value_total_issue_balance_qty","value_recv_ret_qty","value_total_issue_quantity","value_total_stock","total_possible_cut_pcs","total_actual_cut_qty"],
		col: [9,10,11,12,13,14,15,16,17,18,19,20,21,23,24],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	//--------------------

	var tableFilters3 =
	{
		//col_15: "none",
		col_operation: {
		id: ["value_total_req_qty","value_total_tranin_qty","value_total_rec_qty","value_total_issue_ret_qty","value_total_receive_qty","value_total_rec_bal","value_total_tranout_qty","value_total_issue_qty","value_recv_ret_qty","value_total_issue_quantity","value_total_stock","total_possible_cut_pcs","total_actual_cut_qty"],
		// col: [11,12,13,14,15,16,17,18,19,20,21,23,24],
		col: [16,17,18,19,20,21,22,23,24,25,26,27,28],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	var tableFilters4 =
	{
		//col_15: "none",
		col_operation: {
		id: ["value_total_req_qty","value_total_tranin_qty","value_total_rec_qty","value_total_issue_ret_qty","value_total_receive_qty","value_total_rec_bal","value_total_tranout_qty","value_total_issue_qty","value_recv_ret_qty","value_total_issue_quantity","value_total_stock","total_possible_cut_pcs","total_actual_cut_qty"],
		//col: [10,11,12,13,14,15,16,17,18,19,20   ,22,23],11
		col: [13,14,15,16,17,18,19,20,21,22,23,25,26],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function print_button_setting()
	{
	//  console.log('hello');
		//$('#button_data_panel').html('');
		get_php_form_data($('#cbo_company_id').val(),'print_button_variable_setting','requires/order_wise_finish_fabric_stock_controller' );
	}

	function generate_report(type)
	{
		/*if( form_validation('cbo_company_id*cbo_store_id','Company Name*Store')==false )
		{
			return;
		}*/
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_id*txt_date_from_st*cbo_buyer_id*cbo_year*cbo_report_type*cbo_search_by*txt_search_comm*cbo_presentation*cbo_value_for_search_by*cbo_store_id*txt_search_booking*cbo_shipment_status*cbo_sock_for*cbo_date_cat*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/order_wise_finish_fabric_stock_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("**");
			//alert(reponse[0]);
			$("#report_container2").html(reponse[0]);
			var type = reponse[2];
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			var cbo_presentation=$('#cbo_presentation').val();
			var cbo_report_type=$('#cbo_report_type').val();
			if(type==1) // show button
			{

				if(cbo_report_type==1)
				{
					if(cbo_presentation==1) // order wise
					{
						setFilterGrid("table_body",-1,tableFilters);
					}
					else // style wise
					{
						setFilterGrid("table_body",-1,tableFilters2);
					}
				}
			}
			else if(type==4) // show button
			{

				if(cbo_report_type==1)
				{
					if(cbo_presentation==1) // order wise
					{
						setFilterGrid("table_body",-1,tableFilters55);
					}
					else // style wise
					{
						setFilterGrid("table_body",-1,tableFilters5);
					}
				}
			}
			else if(type==3) // show button
			{
				if(cbo_presentation==1) // order wise
				{
					setFilterGrid("table_body",-1,tableFilters4);
				}
			}
			else // show 2 button
			{
				setFilterGrid("table_body",-1,tableFilters3);
			}
			show_msg('3');
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

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
	}

	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/order_wise_finish_fabric_stock_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;

			$('#txt_job_no').val(job_no);
			$('#hide_job_id').val(job_id);
		}
	}

	function openmypage_order()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_job_no').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/order_wise_finish_fabric_stock_controller.php?action=order_no_popup&data='+data,'Order No Popup', 'width=700px,height=420px,center=1,resize=0','../../')

		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("order_no_id");
			var theemailv=this.contentDoc.getElementById("order_no_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_order_id").value=theemail.value;
			    document.getElementById("txt_order_no").value=theemailv.value;
				release_freezing();
			}
		}
	}

	function openmypage_booking()
	{
		//var job_no = $("#txt_job_no").val();
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('cbo_year').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/order_wise_finish_fabric_stock_controller.php?action=booking_no_popup&data='+data,'Booking No Popup', 'width=1050px,height=420px,center=1,resize=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");
			//var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				//document.getElementById("txt_booking_id").value=theemail.value;
			    document.getElementById("txt_search_booking").value=theemail.value;
				release_freezing();
			}
		}
	}

	function openmypage(po_id,prod_id,color,type,action,store_id,from_date="")
	{
		var companyID = $("#cbo_company_id").val();
		if(type==1)
		{
			var popup_width='1245px';
		}
		else if(type==2)
		{
			var popup_width='1100px';
		}
		else if(type==3)
		{
			var popup_width='1090px';
		}
		else if(type==4)
		{
			var popup_width='990px';
		}
		else if(type==5)
		{
			var popup_width='1090px';
		}
		else if(type==55)
		{
			popup_width='550px';
		}
		else if(type==0)
		{
			popup_width='1050px';
		}
		else
		{
			var popup_width='600px';
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_finish_fabric_stock_controller.php?companyID='+companyID+'&po_id='+po_id+'&prod_id='+prod_id+'&color='+color+'&store_id='+store_id+'&from_date='+from_date+'&type='+type+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}


	function openmypage_ex_factory(po_id,rpt_type)
	{
		var companyID = $("#cbo_company_id").val();
		var action="";
		if(rpt_type==1)
		{
			action="open_exfactory";
			popup_width='350px';
		}
		else
		{
			action="open_order_exfactory";
			popup_width='500px';
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_finish_fabric_stock_controller.php?companyID='+companyID+'&po_id='+po_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=400px,center=1,resize=0,scrolling=0','../../');
	}

	function change_caption(type)
	{
		if(type==1)
		{
			$('#td_search').html('Enter Job');
		}
		else if(type==2)
		{
			$('#td_search').html('Enter Style');
		}
		else if(type==3)
		{
			$('#td_search').html('Enter Order');
		}
		else if(type==4)
		{
			$('#td_search').html('Enter File');
		}
		else if(type==5)
		{
			$('#td_search').html('Enter Ref.');
		}
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>
    <form name="ordewisefinishfabricstock_1" id="ordewisefinishfabricstock_1" autocomplete="off" >
    <h3 style="width:1580px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1580px;">
                <table class="rpt_table" width="1580" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="120" class="must_entry_caption">Company</th>
                            <th width="120">Buyer</th>
                            <th width="80">Category By</th>
                            <th width="90">Year</th>
                            <th width="70">Search By</th>
                            <th width="80" id="td_search">Enter Job</th>
                            <th width="80">Enter Booking</th>
                            <th width="100" class="must_entry_caption">Store</th>
                            <th width="80">Shipment Status</th>
                            <th width="70">Presentation</th>
                            <th width="80">Value for Search By</th>
                            <th width="60">Date</th>
                            <th width="80">Stock For</th>
                            <th width="80">Date Category</th>
                            <th width="150">Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('ordewisefinishfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center" class="general">
                        <td>
                            <?
                               echo create_drop_down( "cbo_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "print_button_setting();load_drop_down( 'requires/order_wise_finish_fabric_stock_controller',this.value+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/order_wise_finish_fabric_stock_controller',this.value, 'load_drop_down_store', 'store_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <?
                                echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <?
                                $report_arr=array(1=>'Knit Finish',2=>'Woven Finish');
                                echo create_drop_down( "cbo_report_type", 80, $report_arr, "", 0, "--  --", 0, "", "", "");
                            ?>
                        </td>
                        <td>
                            <?
                                //echo create_drop_down( "cbo_year", 90, $year,"", 1, "--All Year--", 0, "",0 );
								echo create_drop_down( "cbo_year", 90, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                            ?>
                        </td>
                         <td>
                            <?
								$search_by=array(1=>'Job',2=>'Style',3=>'Order',4=>'File',5=>'Ref.');
                                echo create_drop_down( "cbo_search_by", 70, $search_by,"", 0, "--Select--", 1, "change_caption(this.value);",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_search_comm" name="txt_search_comm" class="text_boxes" style="width:80px"  placeholder="Write" />
                        </td>
                        <td>
                            <input type="text" id="txt_search_booking" name="txt_search_booking" class="text_boxes" style="width:80px"  placeholder="Write/Browse" onDblClick="openmypage_booking()" />
                        </td>
                        <td id="store_td">
                            <?
                            	echo create_drop_down( "cbo_store_id", 100, $blank_array,"", 1, "--Select Store--", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <?
                                //echo create_drop_down( "cbo_shipment_status", 80, $shipment_status,"", 0, "", 0, "",0 );
								 $ship_status_arr = array(1=>"Full Pending",2=>"Partial Delivery",3=>"Full Delivery/Closed");
								 echo create_drop_down( "cbo_shipment_status", 100, $ship_status_arr,"", 1,"-All-","", "",0,"" );
                            ?>
                        </td>
                        <td>
                            <?
								$presentation=array(1=>"Order Wise",2=>"Style Wise");
                                echo create_drop_down( "cbo_presentation", 70, $presentation,"", 0, "", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <?
								$cbo_value_for_search_by=array(1=>"Value with 0",2=>"Value without 0");
                                echo create_drop_down( "cbo_value_for_search_by", 80, $cbo_value_for_search_by,"", 0, "", 1, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from_st" id="txt_date_from_st" value="<? echo date("d-m-Y", time());?>" class="datepicker" style="width:55px;" readonly/>
                        </td>
                        <td>
                            <?
								$stock_for_arr=array(1=>"Running Order",2=>"Cancelled Order",3=>"Left Over");
                                echo create_drop_down( "cbo_sock_for", 80, $stock_for_arr,"", 1, "Select", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <?
								$date_cate_arr=array(1=>"Ship Date",2=>"Cancel Date");
                                echo create_drop_down( "cbo_date_cat", 80, $date_cate_arr,"", 1, "Select", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px;" readonly/>&nbsp;To&nbsp;
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px;" readonly/>
                        </td>
                        <td>
						    <span id="button_data_panel"></span>
                            <!-- <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:60px" class="formbutton" />
                            <input type="button" name="search" id="search" value="Show2" onClick="generate_report(2)" style="width:60px;" class="formbutton" />
                            <input type="button" name="search" id="search" value="Show3" onClick="generate_report(3)" style="width:60px;" class="formbutton" /> -->
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="15" align="center"><? echo load_month_buttons(1);  ?></td>
                        <td></td>
                    </tr>
                </table>
            </fieldset>
        </div>

    </form>
</div>
	<div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2"></div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_multiselect('cbo_year','0','0','0');
	set_multiselect('cbo_year','0','1','<?= date("Y",time());?>','0');
</script>
</html>
