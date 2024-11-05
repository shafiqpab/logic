<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Style Wise Finich Fabric Stock Report

Functionality	:
JS Functions	:
Created by		:	Aziz
Creation date 	: 	18-02-2017
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
		//"value_total_today_rtn_qnty","value_total_today_trans_in_qnty",,"value_total_rec_ret_qty","value_total_rec_trns_qty","value_total_today_issue_rcv_rtn_qty","value_recv_total_today_issue_trns_out_qnty","value_total_issue_ret_qty","value_total_issue_trns_qty"
		//col_15: "none",
		col_operation: {
		id: ["value_total_req_qty","value_total_today_rec_qty","value_total_today_rtn_qnty","value_total_today_trans_in_qnty","value_total_rec_qty","value_total_rec_ret_qty","value_total_rec_trns_qty","value_total_rec_bal","value_recv_today_issue_qty","value_total_today_issue_rcv_rtn_qty","value_recv_total_today_issue_trns_out_qnty","value_total_issue_qty","value_total_issue_ret_qty","value_total_issue_trns_qty","value_total_stock"],
		//col: [9,10,11,12,13,14,15,16,17,18,19,20,21,22,23],
		col: [10,11,12,13,14,15,16,17,18,19,20,21,22,23,24],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	var tableFilters2 =
	{
		//"value_total_today_rtn_qnty","value_total_today_trans_in_qnty","value_total_rec_ret_qty","value_total_rec_trns_qty","value_total_today_issue_rcv_rtn_qty","value_recv_total_today_issue_trns_out_qnty,"value_total_issue_ret_qty","value_total_issue_trns_qty""
		//col_15: "none",
		col_operation: {
		id: ["value_total_req_qty","value_total_today_rec_qty","value_total_rec_qty","value_total_rec_bal","value_recv_today_issue_qty","value_total_issue_qty","value_total_stock"],
		//col: [8,9,10,11,12,13,14],
		col: [9,10,11,12,13,14,15],
		operation: ["sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	var tableFilters3 =
	{
		//"value_total_today_rtn_qnty","value_total_today_trans_in_qnty","value_total_rec_ret_qty","value_total_rec_trns_qty","value_total_today_issue_rcv_rtn_qty","value_recv_total_today_issue_trns_out_qnty,"value_total_issue_ret_qty","value_total_issue_trns_qty""
		//col_15: "none",
		col_operation: {
		id: ["value_total_req_qty","value_total_today_rec_qty","value_total_rec_qty","value_total_rec_bal","value_total_rec_amount","value_recv_today_issue_qty","value_total_issue_qty","value_total_iss_amount","value_total_stock","value_total_StockValue"],
		//col: [8,9,10,11,12,13,14],
		col: [9,10,11,12,14,15,16,18,19,21],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	var btn_type=1;
	function generate_report(type)
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		if(type == "2")
		{
			var data="action=report_generate_uom"+get_submitted_data_string('cbo_company_id*txt_date_from*cbo_buyer_id*cbo_year*cbo_uom*cbo_report_type*cbo_search_by*cbo_value_range_by*txt_search_comm',"../../../")+'&report_title='+report_title;
			btn_type=2;
		}
		else if(type == "3")
		{
			var data="action=report_generate2"+get_submitted_data_string('cbo_company_id*txt_date_from*cbo_buyer_id*cbo_year*cbo_report_type*cbo_search_by*cbo_value_range_by*txt_search_comm',"../../../")+'&report_title='+report_title;
			btn_type=3;
		}
		else
		{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*txt_date_from*cbo_buyer_id*cbo_year*cbo_report_type*cbo_search_by*cbo_value_range_by*txt_search_comm',"../../../")+'&report_title='+report_title;
			btn_type=1;
		}

		//alert (data);
		freeze_window(3);
		http.open("POST","requires/style_wise_finish_fabric_stock_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var ReportType = $("#cbo_report_type").val();
			var reponse=trim(http.responseText).split("**");

			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			var cbo_presentation=$('#cbo_presentation').val();
			if(btn_type==1)
			{
				if(ReportType==1)
				{
					setFilterGrid("table_body",-1,tableFilters);
				}
				else
				{
					setFilterGrid("table_body",-1,tableFilters2);
				}
			}
			if(btn_type==3)
			{
				if(ReportType==1)
				{
					setFilterGrid("table_body",-1,tableFilters);
				}
				else
				{
					setFilterGrid("table_body",-1,tableFilters3);
				}
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
		var page_link='requires/style_wise_finish_fabric_stock_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/style_wise_finish_fabric_stock_controller.php?action=order_no_popup&data='+data,'Order No Popup', 'width=700px,height=420px,center=1,resize=0','../../')

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

	function openmypage(po_id,prod_id,color,from_date,type,action,style_ref_no,rate)
	{
		var companyID = $("#cbo_company_id").val();
		if(type==0)
		{
			popup_width='1270px';
		}
		else if(type==1)
		{
			popup_width='1395px';
		}
		else if(type==2)
		{
			popup_width='1150px';
		}
		else if(type==3)
		{
			popup_width='1000px';
		}
		else if(type==4)
		{
			popup_width='885px';
		}
		else if(type==5)
		{
			popup_width='1430px';
		}
		else if(type==6)
		{
			popup_width='885px';
		}
		else if(type==8)
		{
			popup_width='1170px';
		}
		else
		{
			var popup_width='600px';
		}

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_finish_fabric_stock_controller.php?companyID='+companyID+'&po_id='+po_id+'&prod_id='+prod_id+'&from_date='+from_date+'&color='+color+'&type='+type+'&action='+action+'&style_ref_no='+style_ref_no+'&rate='+rate, 'Details Veiw', 'width='+popup_width+', height=300px,center=1,resize=0,scrolling=0','../../');
	}
	
	function openmypage_req_qnty(po_id,prod_id,color,from_date,job_key,poID,desc_key,type,action,style_ref_no)
	{
		var companyID = $("#cbo_company_id").val();
			//alert(poID);
		var popup_width='1050px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_finish_fabric_stock_controller.php?companyID='+companyID+'&po_id='+po_id+'&prod_id='+prod_id+'&from_date='+from_date+'&job_key='+job_key+'&poID='+poID+'&desc_key='+desc_key+'&color='+color+'&type='+type+'&action='+action+'&style_ref_no='+style_ref_no, 'Details Veiw', 'width='+popup_width+', height=300px,center=1,resize=0,scrolling=0','../../');
	}

	function openmypage_ex_factory(po_id,rpt_type)
	{
		var companyID = $("#cbo_company_id").val();
		var action="";
		if(rpt_type==1)
		{
			action="open_exfactory";
			popup_width='500px';
		}
		else
		{
			action="open_order_exfactory";
			popup_width='500px';
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_finish_fabric_stock_controller.php?companyID='+companyID+'&po_id='+po_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=400px,center=1,resize=0,scrolling=0','../../');
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
    <h3 style="width:1100px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1040px;">
                <table class="rpt_table" width="940" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="140" class="must_entry_caption">Company</th>
                            <th width="140">Buyer</th>
                            <th width="110">Category By</th>
                            <th width="90">Year</th>
                            <th width="90">UOM</th>
                            <th width="90">Search By</th>
                            <th width="100" id="td_search">Enter Job</th>
                            <th width="100">Value Range</th>
                            <th width="75">Date</th>
                            <th width="70" colspan="3"><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('ordewisefinishfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center" class="general">
                        <td>
                            <?
                               echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/style_wise_finish_fabric_stock_controller',this.value+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <?
                                echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <?
                                $report_arr=array(1=>'Knit Finish',2=>'Woven Finish');
                                echo create_drop_down( "cbo_report_type", 115, $report_arr, "", 0, "--  --", 0, "", "", "1,2");
                            ?>
                        </td>
                        <td>
                            <?
								$selected_year=date("Y");
                                echo create_drop_down( "cbo_year", 90, $year,"", 1, "--All Year--", "", "",0 );
                            ?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_uom", 90, $unit_of_measurement ,"", 0, "", "", "", "","1,12,23,27" );
                            ?>
                        </td>
                        <td>
                            <?
								$search_by=array(1=>'Job',2=>'Style',3=>'Order',4=>'File',5=>'Ref.');
                                echo create_drop_down( "cbo_search_by", 90, $search_by,"", 0, "--Select--", 1, "change_caption(this.value);",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_search_comm" name="txt_search_comm" class="text_boxes" style="width:100px"  placeholder="Write" />
                        </td>
                        <td>
                            <?
								$value_range_by=array(1=>'Value with 0',2=>'Value without 0');
                                echo create_drop_down( "cbo_value_range_by", 90, $value_range_by,"", 1, "--Select--", 1, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time());?>" class="datepicker" style="width:75px;" readonly/>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show 2" onClick="generate_report(3)" style="width:70px" class="formbutton" />
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="UOM Wise" onClick="generate_report(2)" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>

    </form>
</div>
	<div id="report_container" align="center"></div>
    <div id="report_container2"></div>
</body>
<script>
	//set_multiselect('cbo_yarn_type*cbo_yarn_count','0*0','0*0','','0*0');
	set_multiselect('cbo_uom','0','0','','0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
