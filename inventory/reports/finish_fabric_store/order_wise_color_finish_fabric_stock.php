<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Order Wise Finich Fabric Stock Report

Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	24-04-2014
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
		//col_15: "none",
		col_operation: {
		id: ["total_order_qnty","total_req_qty","total_rec_qty","total_grey_used_qty","total_rec_bal","total_issue_qty","total_stock","total_possible_cut_pcs","total_actual_cut_qty"],
		col: [16,18,19,20,21,22,23,25,26],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	var tableFiltersWithoutBodypar =
	{
		col_operation: {
		id: ["value_total_order_qnty","value_total_req_qty","value_total_rec_qty","value_total_rec_bal","value_total_issue_qty","value_total_stock","value_total_possible_cut_pcs","value_total_actual_cut_qty"],
		col: [15,17,18,19,20,21,23,24],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	var tableFiltersWithoutRackShelf =
	{
		col_operation: {
		id: ["value_total_order_qnty","value_total_req_qty","value_total_rec_qty","value_total_rec_bal","value_total_issue_qty","value_total_stock","value_total_possible_cut_pcs","value_total_actual_cut_qty"],
		col: [13,15,16,17,18,19,21,22],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function generate_report(type)
	{
		/*var company = document.getElementById('cbo_company_id').value;
		var buyer = document.getElementById('cbo_buyer_id').value;
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}*/
		var report_title=$( "div.form_caption" ).html();
		if(type == 1)
		{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*txt_date_from*cbo_buyer_id*txt_book_no*txt_book_id*cbo_year*cbo_report_type*txt_job_no*txt_job_id*txt_order_no*txt_order_id*txt_file_no*txt_ref_no*cbo_presantation_type*txt_style',"../../../")+'&report_title='+report_title+'&type='+type;
		}
		else if(type == 3)
		{
			var data="action=report_generate2"+get_submitted_data_string('cbo_company_id*txt_date_from*cbo_buyer_id*txt_book_no*txt_book_id*cbo_year*cbo_report_type*txt_job_no*txt_job_id*txt_order_no*txt_order_id*txt_file_no*txt_ref_no*cbo_presantation_type*txt_style*cbo_shipment_status',"../../../")+'&report_title='+report_title+'&type='+type;
		}
		else if(type == 4)
		{
			var data="action=report_generate3"+get_submitted_data_string('cbo_company_id*txt_date_from*cbo_buyer_id*txt_book_no*txt_book_id*cbo_year*cbo_report_type*txt_job_no*txt_job_id*txt_order_no*txt_order_id*txt_file_no*txt_ref_no*cbo_presantation_type*txt_style',"../../../")+'&report_title='+report_title+'&type='+type;
		}
		else
		{
			var data="action=report_generate_uom"+get_submitted_data_string('cbo_company_id*txt_date_from*cbo_buyer_id*txt_book_no*txt_book_id*cbo_year*cbo_report_type*txt_job_no*txt_job_id*txt_order_no*txt_order_id*txt_file_no*txt_ref_no*cbo_presantation_type*cbo_uom',"../../../")+'&report_title='+report_title+'&type='+type;
		}

		//alert (data);
		freeze_window(3);
		http.open("POST","requires/order_wise_color_finish_fabric_stock_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			//alert(reponse[1]);
			$("#report_container2").html(reponse[0]);
			if(typeof(reponse[1]) != 'undefined')
			{
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[2]+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				//alert(reponse[2] + '=' + reponse[3]);
				if(reponse[2]==4)
				{
					if( typeof(reponse[3]) != 'undefined')
					{
						setFilterGrid("table_body",-1,tableFiltersWithoutRackShelf);
					}
					else
					{
						setFilterGrid("table_body",-1,tableFiltersWithoutBodypar);
					}
				}
				else if(reponse[2]!=3)
				{
				setFilterGrid("table_body",-1);
				setFilterGrid("table_body2",-1);
				}
				else
				{
				 setFilterGrid("table_body",-1,tableFilters);
				}
				show_msg('3');
			}

			release_freezing();
		}
	}

	function new_window(type)
	{

		//alert(type);
		if(type!=3)
		{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide();
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		$('#table_body2 tr:first').hide();

		}
		else
		{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide();
		}

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		if(type!=3)
		{
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#table_body tr:first').show();
		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="380px";
		$('#table_body2 tr:first').show();
		}
		else
		{
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#table_body tr:first').show();
		}
	}

	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		//alert(companyID);
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/order_wise_color_finish_fabric_stock_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
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
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('cbo_year').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/order_wise_color_finish_fabric_stock_controller.php?action=order_no_popup&data='+data,'Order No Popup', 'width=700px,height=420px,center=1,resize=0','../../')

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

	/*function openmypage(po_id,prod_id,color,gsm_weight,action)
	{
		alert(gsm_weight);
		var companyID = $("#cbo_company_id").val();
		var popup_width='600px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_color_finish_fabric_stock_controller.php?companyID='+companyID+'&po_id='+po_id+'&gsm_weight='+gsm_weight+'&prod_id='+prod_id+'&color='+color+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}*/
	function openmypage_booking()
	{
			//var job_no = $("#txt_job_no").val();
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('cbo_year').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/order_wise_color_finish_fabric_stock_controller.php?action=booking_no_popup&data='+data,'Booking No Popup', 'width=1050px,height=420px,center=1,resize=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");
			//var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				//document.getElementById("txt_booking_id").value=theemail.value;
			    document.getElementById("txt_book_no").value=theemail.value;
				release_freezing();
			}
		}
	}

	function openmypage(po_id,body_part_id,color,gsm_weight,action)
	{
		var companyID = $("#cbo_company_id").val();
		if(action=='grey_used_receive_popup')
		{
			var popup_width='680px';
			var popup_height='220px';
		}
		else
		{
			var popup_width='600px';
			var popup_height='420px';
		}

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_color_finish_fabric_stock_controller.php?companyID='+companyID+'&po_id='+po_id+'&body_part_id='+body_part_id+'&color='+color+'&gsm_weight='+gsm_weight+'&action='+action, 'Details Veiw', 'width='+popup_width+', height='+popup_height+',center=1,resize=0,scrolling=0','../../');
	}

	function generate_req_qty_report(po_id,job_no,buyer_name,body_part_id,color_id,booking_no,booking_type,action)
	{
		//alert(action);
		var companyID = $("#cbo_company_id").val();
		var popup_width='700px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_color_finish_fabric_stock_controller.php?companyID='+companyID+'&po_id='+po_id+'&job_no='+job_no+'&buyer_name='+buyer_name+'&body_part_id='+body_part_id+'&color_id='+color_id+'&booking_no='+booking_no+'&booking_type='+booking_type+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>
    <form name="ordewisefinishfabricstock_1" id="ordewisefinishfabricstock_1" autocomplete="off" >
    <h3 style="width:1400px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1400px;">
                <table class="rpt_table" width="1400" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="140" class="must_entry_caption">Company</th>
                            <th width="130" class="must_entry_caption">Buyer</th>
                            <th width="110">Search By</th>
                            <th width="65">From Ship Date</th>
                            <th width="60">Year</th>
                            <th width="75">F.Booking No.</th>
                            <th width="75">Job</th>
                            <th width="75">Order</th>
                            <th width="70">File No</th>
                            <th width="70">Ref. No</th>
                            <th width="70">Style</th>
                            <th width="70">UOM</th>
                            <th width="100">Shipment Status</th>
                             <th width="75">Type</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:50px" class="formbutton" onClick="reset_form('ordewisefinishfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center">
                        <td>
                            <?
                               echo create_drop_down( "cbo_company_id", 140, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_wise_color_finish_fabric_stock_controller',this.value+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data( this.value, 'company_wise_report_button_setting','requires/order_wise_color_finish_fabric_stock_controller' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <?
                                /*echo create_drop_down( "cbo_buyer_id", 130, $blank_array,"", 1, "--Select Buyer--", 0, "",0 ); */
                                echo create_drop_down("cbo_buyer_id", 130, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "--Select Buyer--", $selected, "");
                           ?>
                        </td>
                        <td>
                            <?

                                $report_arr=array(1=>'Knit Finish',3=>'Woven Finish');
                                //echo create_drop_down( "cbo_report_type", 115, $report_arr, "", 0, "--  --",$_SESSION['fabric_nature'] , "", "", "");
                                echo create_drop_down( "cbo_report_type", 115, $report_arr, "", 0, "--  --",1 , "", "", "");
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from"   class="datepicker" style="width:60px;" value=" <? echo date("d-m-Y");?> "/>
                        </td>
                        <td>
                            <?
								$selected_year=date("Y");
                                echo create_drop_down( "cbo_year", 60, $year,"", 1, "--Select Year--", $selected_year, "",0 );
                            ?>
                        </td>


                        <td>
                            <input type="text" id="txt_book_no" name="txt_book_no" class="text_boxes" style="width:70px" onDblClick="openmypage_booking();" placeholder="Write/Browse" />
                            <input type="hidden" id="txt_book_id" name="txt_book_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:70px" onDblClick="openmypage_job();" placeholder="Write/Browse" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
                            <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:70px" onDblClick="openmypage_order();" placeholder="Write/Browse"  />
                            <input type="hidden" id="txt_order_id" name="txt_order_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
                            <input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes" style="width:70px"  placeholder="Write"  />

                        </td>
                        <td>
                            <input type="text" id="txt_ref_no" name="txt_ref_no" class="text_boxes" style="width:70px"  placeholder="Write"  />

                        </td>
                        <td>
                            <input type="text" id="txt_style" name="txt_style" class="text_boxes" style="width:70px"  placeholder="Write"  />

                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_uom", 70, $unit_of_measurement, "", 0, "--  --", 0, "", "", "1,12,23,27");
                            ?>
                        </td>
                         <td>
                            <?
                                $shipment_status=array(1=>'Running',2=>'Full Shipment');
                                echo create_drop_down( "cbo_shipment_status", 100, $shipment_status, "", 1, "-- Select --", 0, "", "", "");
                            ?>
                        </td>
                        <td>
                            <?
                                $presant_arr=array(1=>'Order Wise',2=>'Style /Job Wise');
                                echo create_drop_down( "cbo_presantation_type", 75, $presant_arr, "", 0, "--  --", 0, "", "", "");
                            ?>
                        </td>

                        <td>
                            <input type="button" name="search1" id="search1" value="Show" onClick="generate_report(1)" style="display: none;width:50px" class="formbutton" />
                            <input type="button" name="search2" id="search2" value="Show2" onClick="generate_report(3)" style="display: none;width:50px" class="formbutton" />
                            <input type="button" name="search3" id="search3" value="Report" onClick="generate_report(2)" style="display: none;width:50px" class="formbutton" />
                            <input type="button" name="search4" id="search4" value="Show3" onClick="generate_report(4)" style="display: none;width:50px" class="formbutton" />

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
	set_multiselect('cbo_uom','0','0','','0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
