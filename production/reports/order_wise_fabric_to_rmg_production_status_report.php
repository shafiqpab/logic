<?php
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Report Will Create Order Wise Fabric to RMG Production Status Report.
Functionality	:
JS Functions	:
Created by		:	Abu Sayed
Creation date 	: 	01-10-2023
Updated by 		:
Update date		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Wise Fabric to RMG Production Status Report", "../../", 1, 1,$unicode,1,1);

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';
 	//order wise
	/* var tableFilters1 =
	{
		// col_54:'none',
		col_operation: {
			id: ["td_smv_qty","td_po_qty","td_fob_value","td_plan_cutting","td_cut_lay","td_cutting","td_cutting_reject","td_reject_qty","td_short_access_cut","td_print_issue","td_printIss_subcon","td_print_receive","td_printRec_subcon","total_emb_issue","td_embIss_subcon","total_emb_receive","td_embRec_subcon","total_sp_issue","total_sp_receive","total_sewing_input","total_sewing_out","total_subcon_sewing_input","total_subcon_sewing_out","total_sewing_input_all_qnty","total_sewing_output_all_qnty","finishing_rcv_qnty","td_wash_iss","td_wash_rec","total_iron_qnty","total_re_iron_qnty","total_poly_in_qnty","total_poly_out_qnty","total_poly_qnty","td_finish_in","td_finish_out","td_transfer_in","td_transfer_out","td_finish","td_reject","td_sewing_reject","td_ex_factory","td_short","td_excess","td_balance"],
			// col: [11,13,17,18,19,20,21,22,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,48,49,50,51,52,54,55,56,57],
			col: [11,12,14,19,20,21,22,23,24,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,51,52,53,54,55,57,58,59,60,61,62],

			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
 	} */



 	/* function fnc_load_report_format(data)
 	{
 		var report_ids = return_global_ajax_value( data, 'load_report_format', '', 'requires/order_wise_fabric_to_rmg_production_status_report_controller');
  		print_report_button_setting(report_ids);
 	} */

 	function ClearTextBoxValues()
 	{
 		// $("#cbo_buyer_name").val('');
	   // $("#cbo_job_year").val('');
		$("#txt_style_ref_no").val('');
		$("#txt_style_ref_id").val('');
		$("#txt_style_ref").val('');
		$("#txt_order_id_no").val('');
		$("#txt_order_id").val('');
		$("#txt_order").val('');
		$("#txt_style_ref_number").val('');

 	}

 	/* function print_report_button_setting(report_ids)
 	{
 		if(trim(report_ids)=="")
		{
 			$("#show_button").show();
			$("#show_button2").show();
			$("#show_button1").show();
			$("#show_button3").show();
			$("#show_button4").show();
			$("#show_button5").show();
			$("#show_button6").show();
		}
		else
		{
			var report_id=report_ids.split(",");
			$("#show_button").hide();
			$("#show_button2").hide();
			$("#show_button1").hide();
			$("#show_button3").hide();
			$("#show_button4").hide();
			$("#show_button5").hide();
			$("#show_button6").hide();
			for (var k=0; k<report_id.length; k++)
			{
				if(report_id[k]==124)
				{
					$("#show_button").show();
				}
				else if(report_id[k]==125)
				{
					$("#show_button2").show();
				}
				else if(report_id[k]==126)
				{
					$("#show_button1").show();
				}
				else if(report_id[k]==127)
				{
					$("#show_button3").show();
				}
				else if(report_id[k]==128)
				{
					$("#show_button4").show();
				}
				else if(report_id[k]==292)
				{
					$("#show_button6").show();
				}
				else if(report_id[k]==293)
				{
					$("#show_button5").show();
				}
			}
		}
	} */

	function fn_report_generated(type)
	{

		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var cbo_location=$('#cbo_location').val();
		var txt_style_ref=$('#txt_style_ref').val();
		var txt_order=$('#txt_order').val();
		var txt_style_ref_number=$('#txt_style_ref_number').val();

		if(cbo_buyer_name>0 || txt_style_ref!="" || txt_style_ref_number!="" || txt_order!="" )
		{
			if (form_validation('cbo_company_name','Comapny Name')==false)
			{
				release_freezing();
				return;
			}
		}
		else
		{
			if (form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*From Date*To Date')==false)
			{
				release_freezing();
				return;
			}
		}


		var data="action=report_generate"+"&type="+type+get_submitted_data_string('cbo_garments_nature*cbo_company_name*cbo_buyer_name*cbo_location*cbo_floor*txt_date_from*txt_date_to*cbo_job_year*txt_style_ref*txt_style_ref_id*txt_order*txt_order_id*shipping_status*active_status*orderStatus*txt_style_ref_number*cbo_date_type',"../../");
		freeze_window(3);
		http.open("POST","requires/order_wise_fabric_to_rmg_production_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			// alert(reponse[2]);
			var reponse=trim(http.responseText).split("####");
			// alert(reponse[2]);
			show_msg('3');
			release_freezing();
			$('#report_container2').html(reponse[0]);
			// document.getElementById('report_container').innerHTML=report_convert_button('../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			append_report_checkbox('table_header_1',1);

			/*document.getElementById("check_uncheck_tr").style.display="table";
            if($("#check_uncheck").is(":checked")==false){
                $("#check_uncheck").attr("checked","checked");

            }else{
                $("#check_uncheck").rmoveAttr("checked");
            }*/

			if(reponse[2]==1)
			{
				setFilterGrid("table_body",-1,tableFilters1);
			}
			else if(reponse[2]==2)
			{
				setFilterGrid("table_body",-1,tableFilters2);
			}
			else if(reponse[2]==3)
			{
				setFilterGrid("table_body",-1,tableFilters3);
			}
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="400px";
	}

	function fn_check_uncheck()
	{
	    var lengths = $("[type=checkbox]").length;
	    if($("#check_uncheck").is(":checked") != true){
	        for(var i=0; i<=lengths; i++){

	            $("[type=checkbox]").prop('checked', false);
	            $("[type=checkbox]").removeClass('rpt_check');
	            $("[type=checkbox]").removeAttr('checked');
	        }
	    }else{
	        $("[type=checkbox]").prop('checked', true);
	        for(var i=0; i<=lengths; i++){

	            $("[type=checkbox]").not("#check_uncheck").addClass('rpt_check');
	            $("[type=checkbox]").attr('checked',"checked");
	        }
	    }
	}

	function openmypage_remark(po_break_down_id,item_id,country_id,action)
	{
		var garments_nature = $("#cbo_garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_fabric_to_rmg_production_status_report_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&country_id='+country_id+'&action='+action, 'Remarks Veiw', 'width=550px,height=450px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_color_countyr_remark(po_break_down_id,item_id,country_id,color_id,action)
	{
		var garments_nature = $("#cbo_garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_fabric_to_rmg_production_status_report_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&country_id='+country_id+'&color_id='+color_id+'&action='+action, 'Remarks Veiw', 'width=550px,height=450px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_job_color_remark(job_num,job_no,po_no,color_id,action)
	{
		var garments_nature = $("#cbo_garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_fabric_to_rmg_production_status_report_controller.php?job_no='+job_no+'&po_no='+po_no+'&color_id='+color_id+'&action='+action, 'Remarks Veiw', 'width=550px,height=450px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_order(po_break_down_id,company_name,item_id,country_id,action)
	{
		//var garments_nature = $("#cbo_garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_fabric_to_rmg_production_status_report_controller.php?po_break_down_id='+po_break_down_id+'&company_name='+company_name+'&item_id='+item_id+'&country_id='+country_id+'&action='+action, 'Order Quantity', 'width=850px,height=350px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_finrcv_popup(po_break_down_id,company_name,item_id,country_id,color_id,action)
	{
		//var garments_nature = $("#cbo_garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_fabric_to_rmg_production_status_report_controller.php?po_break_down_id='+po_break_down_id+'&company_name='+company_name+'&item_id='+item_id+'&country_id='+country_id+'&color_id='+color_id+'&action='+action, 'Fabric Receive Details', 'width=850px,height=350px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_finissue_popup(po_break_down_id,company_name,item_id,country_id,color_id,action)
	{
		//var garments_nature = $("#cbo_garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_fabric_to_rmg_production_status_report_controller.php?po_break_down_id='+po_break_down_id+'&company_name='+company_name+'&item_id='+item_id+'&country_id='+country_id+'&color_id='+color_id+'&action='+action, 'Fabrics Issue To Cutting Details', 'width=660px,height=350px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_trims_popup(po_break_down_id,company_name,item_id,country_id,action)
	{
		//var garments_nature = $("#cbo_garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_fabric_to_rmg_production_status_report_controller.php?po_break_down_id='+po_break_down_id+'&company_name='+company_name+'&item_id='+item_id+'&country_id='+country_id+'&action='+action, 'Trims Status Details', 'width=1120px,height=350px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_job_color_order(company_name,job_no,po_no,color_id,action)
	{
		//var garments_nature = $("#cbo_garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_fabric_to_rmg_production_status_report_controller.php?job_no='+job_no+'&po_no='+po_no+'&company_name='+company_name+'&color_id='+color_id+'&action='+action, 'Order Quantity', 'width=750px,height=350px,center=1,resize=0,scrolling=0','../');
	}

	/*function openmypage_order_colorSize(po_break_down_id,company_name,item_id,country_id,action)
	{
		//var garments_nature = $("#cbo_garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_fabric_to_rmg_production_status_report_controller.php?po_break_down_id='+po_break_down_id+'&company_name='+company_name+'&item_id='+item_id+'&country_id='+country_id+'&action='+action, 'Order Quantity', 'width=750px,height=350px,center=1,resize=0,scrolling=0','../');
	}
	*/

	function openmypage_order_country(po_break_down_id,company_name,item_id,country_id,color_id,action)
	{
		//var garments_nature = $("#cbo_garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_fabric_to_rmg_production_status_report_controller.php?po_break_down_id='+po_break_down_id+'&company_name='+company_name+'&item_id='+item_id+'&country_id='+country_id+'&color_id='+color_id+'&action='+action, 'Order Quantity', 'width=750px,height=350px,center=1,resize=0,scrolling=0','../');
	}



	function openmypage_sewing_replace(po_id,item_id,action,country_id,color_id,reportType,rpt_leb)
	{
		//alert(country_id);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_fabric_to_rmg_production_status_report_controller.php?po_id='+po_id+'&item_id='+item_id+'&action='+action+'&country_id='+country_id+'&color_id='+color_id+'&reportType='+reportType+'&country_id='+country_id, 'Sewing Replace Quantity', 'width=350px,height=350px,center=1,resize=0,scrolling=0','../');
	}


	function openmypage_cutting_replace(po_id,item_id,action,country_id,color_id,reportType,rpt_leb)
	{
		//alert(country_id);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_fabric_to_rmg_production_status_report_controller.php?po_id='+po_id+'&item_id='+item_id+'&action='+action+'&country_id='+country_id+'&color_id='+color_id+'&reportType='+reportType+'&country_id='+country_id, 'Cutting Replace Quantity', 'width=350px,height=350px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage(po_break_down_id,item_id,action,location_id,floor_id,dateOrLocWise,country_id)
	{
		if(action==2 || action==3)
			var popupWidth = "width=420px,height=350px,";
		else if (action==10)
			var popupWidth = "width=550px,height=420px,";
		else
			var popupWidth = "width=750px,height=420px,";

		if (action==2)
		{
			var popup_caption="Embl. Issue Details";
		}
		else if (action==3)
		{
			var popup_caption="Embl. Rec. Details";
		}
		else
		{
			var popup_caption="Production Quantity";
		}

		emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/order_wise_fabric_to_rmg_production_status_report_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&location_id='+location_id+'&floor_id='+floor_id+'&dateOrLocWise='+dateOrLocWise+'&country_id='+country_id, popup_caption, popupWidth+'center=1,resize=0,scrolling=0','../');
	}

	function openmypage_popup(po_break_down_id,item_id,country_id,color_id,prod_popup_type,prod_popup_lelel,action)
	{
		if (prod_popup_type==1)
		{
			var popup_caption="Cutting Qnty Details";
		}
		else if (prod_popup_type==2)
		{
			var popup_caption="Embl. Issue. Details";
		}
		else if (prod_popup_type==3)
		{
			var popup_caption="Embl. Rec. Details";
		}
		else if (prod_popup_type==4)
		{
			var popup_caption="Sewing Input Details";
		}
		else if (prod_popup_type==5)
		{
			var popup_caption="Sewing Output Details";
		}
		else if (prod_popup_type==7)
		{
			var popup_caption="Iron Details";
		}
		else if (prod_popup_type==8)
		{
			var popup_caption="Finish Details";
		}
		else if (prod_popup_type==11)
		{
			var popup_caption="Poly Details";
		}
		else if (prod_popup_type==17)
		{
			var popup_caption="Cutting Lay Qnty Details";
		}
		else if (prod_popup_type==100)
		{
			var popup_caption="Transfer Details";
		}
		else
		{
			var popup_caption="Ex-fact Details";
		}

		emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/order_wise_fabric_to_rmg_production_status_report_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&country_id='+country_id+'&color_id='+color_id+'&prod_popup_type='+prod_popup_type+'&prod_popup_lelel='+prod_popup_lelel+'&action='+action, popup_caption, 'width=800px,height=350px,center=1,resize=0,scrolling=0','../');
	}
	// sent = 1; receive = 2; inhouse=1; subcon = 2
    function openmypage_finishpopup(po_id,item_id,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_fabric_to_rmg_production_status_report_controller.php?po_id='+po_id+'&item_id='+item_id+'&action='+action, 'Finish Recieve Popup', 'width=800px,height=500px,center=1,resize=0,scrolling=0','../');

	}

	function openmypage_job_color_prod(company_name,job_no,po_no,color_id,prod_popup_type,prod_popup_lelel,action)
	{
		if (prod_popup_type==1)
		{
			var popup_caption="Cutting Qnty Details";
		}
		else if (prod_popup_type==2)
		{
			var popup_caption="Printing Issue Details";
		}
		else if (prod_popup_type==3)
		{
			var popup_caption="Printing Receive Details";
		}
		else if (prod_popup_type==4)
		{
			var popup_caption="Sewing Input Details";
		}
		else if (prod_popup_type==5)
		{
			var popup_caption="Sewing Output Details";
		}
		else if (prod_popup_type==6)
		{
			var popup_caption="Reject Details";
		}
		else if (prod_popup_type==7)
		{
			var popup_caption="Iron Details";
		}
		else if (prod_popup_type==8)
		{
			var popup_caption="Finish Details";
		}
		else if (prod_popup_type==11)
		{
			var popup_caption="Poly Details";
		}
		else if (prod_popup_type==17)
		{
			var popup_caption="Cutting Lay Qnty Details";
		}
		else
		{
			var popup_caption="Ex-fact Details";
		}

		emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/order_wise_fabric_to_rmg_production_status_report_controller.php?company_name='+company_name+'&job_no='+job_no+'&po_no='+po_no+'&color_id='+color_id+'&prod_popup_type='+prod_popup_type+'&prod_popup_lelel='+prod_popup_lelel+'&action='+action, popup_caption, 'width=800px,height=350px,center=1,resize=0,scrolling=0','../');
	}


	/*function disable_order( val )
	{
		$('#txt_order_no').val('');
		if(val==5)
		{
			$('#Order_td').html('Style Ref.');
		}
		else
		{
			$('#Order_td').html('Order No');
		}
	}*/



	function openmypage_style()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var buyer = $("#cbo_buyer_name").val();
		var job_year = $("#cbo_job_year").val();
		var txt_style_ref_no = $("#txt_style_ref_no").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var txt_style_ref_number = $("#txt_style_ref_number").val();
		var page_link='requires/order_wise_fabric_to_rmg_production_status_report_controller.php?action=style_refarence_surch&company='+company+'&buyer='+buyer+'&job_year='+job_year+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref+'&txt_style_ref_number='+txt_style_ref_number;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			var style_number_no=this.contentDoc.getElementById("txt_selected_style_number_no").value; // product Description
			//alert(style_no);
			$("#txt_style_ref").val(style_des);
			$("#txt_style_ref_id").val(style_id);
			$("#txt_style_ref_no").val(style_no);
			$("#txt_style_ref_number").val(style_number_no);
		}
	}

	function openall_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var buyer = $("#cbo_buyer_name").val();
		var job_year = $("#cbo_job_year").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_order_id = $("#txt_order_id").val();
		var txt_order = $("#txt_order").val();
		var page_link='requires/order_wise_fabric_to_rmg_production_status_report_controller.php?action=order_surch&company='+company+'&buyer='+buyer+'&job_year='+job_year+'&txt_style_ref_id='+txt_style_ref_id+'&txt_order_id_no='+txt_order_id_no+'&txt_order_id='+txt_order_id+'&txt_order='+txt_order;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_order").val(style_des);
			$("#txt_order_id").val(style_id);
			$("#txt_order_id_no").val(style_des_no);
		}
	}
	function fnc_chng_orderNo(orderNos)
	{
		$("#txt_order_id").val("");
		$("#txt_order_id_no").val("");
	}
	function fnc_chng_jobNo(orderNos)
	{
		$("#txt_style_ref_id").val("");
		$("#txt_style_ref_no").val("");
	}

	function generate_order_report(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,action,page,entryForm)
	{

		var show_yarn_rate='';
		var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");

		if (r==true) show_yarn_rate="1"; else show_yarn_rate="0";
		if(action=="show_fabric_booking_report_gr" || action=="show_fabric_booking_report")
		{
			var report_title="Budget Wise Fabric Booking";
		}

		else
		{
			var report_title="";
		}
		if(action=="show_fabric_booking_report_jk" )
		{
			var report_title="Main Fabric Booking New";
			var is_mail_send="";
			var mail_id="";
			var path="../";

			var data="action="+action+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&txt_order_no_id='+"'"+order_id+"'"+
					'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
					'&cbo_fabric_source='+"'"+fabric_source+"'"+
					'&id_approved_id='+"'"+approved+"'"+
					'&txt_job_no='+"'"+job_no+"'"+
					'&report_title='+report_title+
					'&is_mail_send='+is_mail_send+
					'&mail_id='+mail_id+
					'&show_yarn_rate='+show_yarn_rate+
					'&path='+path;
		}
		else
		{
			var data="action="+action+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&txt_order_no_id='+"'"+order_id+"'"+
					'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
					'&cbo_fabric_source='+"'"+fabric_source+"'"+
					'&id_approved_id='+"'"+approved+"'"+
					'&txt_job_no='+"'"+job_no+"'"+
					'&show_yarn_rate='+show_yarn_rate+"'"+
					'&report_title='+report_title;
		}



					//var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no',"../../../")+'&report_title='+$report_title+'&show_yarn_rate='+show_yarn_rate+'&path=../../../';
		if(entryForm==86)
		{
			http.open("POST","../../order/woven_order/requires/fabric_booking_controller.php",true);
		}
		else if(type==1)
		{
			http.open("POST","../../order/woven_order/requires/short_fabric_booking_controller.php",true);
		}
		else if(type==2)
		{
			http.open("POST","../../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
			// if(page==154)
			// {
			// 	http.open("POST","../../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
			// }
			// else
			// {
			// 	//alert(action);
			// 	http.open("POST","../../order/woven_order/requires/fabric_booking_controller.php",true);

			// }
		}
		else
		{
			http.open("POST","../../order/woven_order/requires/sample_booking_controller.php",true);
		}

		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report_reponse;
	}

	function generate_fabric_report_reponse()
	{
		if(http.readyState == 4)
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../css/style_common.css" type="text/css" />
			d.close();
		}
	}

	function date_fill_change(str)
	{
		if (str==0 || str==1)
		{
			document.getElementById('search_date_td').innerHTML='Pub. Shipment Date';
		}
		else if(str==2)
		{
			document.getElementById('search_date_td').innerHTML='Org. Shipment Date';
		}
		else if(str==3)
		{
			document.getElementById('search_date_td').innerHTML='PO Receive Date';
		}
		else if(str==4)
		{
			document.getElementById('search_date_td').innerHTML='PO Insert Date';
		}

	}

</script>

</head>

<body onLoad="set_hotkey();">

<form id="dateWiseProductionReport_1">
    <div style="width:100%;" align="center">

        <? echo load_freeze_divs ("../../",'');  ?>

         <h3 style="width:1700px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <div id="content_search_panel" >
         <fieldset style="width:1700px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
               <thead>
                        <th class="must_entry_caption">Company Name</th>
                        <th>Buyer Name</th>
                        <th>Location</th>
                        <th style="display:none;">Floor</th>
                        <th>Job Year</th>
                        <th>Style</th>
                        <th>Job No</th>
                        <th id="Order_td">Order No</th>
                        <th style="display:none;">Gmts. Nature</th>
                        <th>Date Type</th>
                        <th id="search_date_td">Pub. Shipment Date</th>
                        <th>Shiping Status</th>
                        <th>Active Status</th>
                        <th>Order Status</th>
                        <th align="center">
                        <input type="reset" id="reset_btn" class="formbutton" style="width:70px;" value="Reset" onClick="reset_form('dateWiseProductionReport_1','report_container*report_container2','','','')" /></th>
                 </thead>
                <tbody>
                <tr class="general">
                    <td>
                        <?
							echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name",0, "-- Select Company --", $selected, "");

                            /* echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "ClearTextBoxValues();load_drop_down( 'requires/order_wise_fabric_to_rmg_production_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/order_wise_fabric_to_rmg_production_status_report_controller', this.value, 'load_drop_down_location', 'location_td' );fnc_load_report_format(this.value);" ); */
                        ?>
                    </td>
                    <input type="hidden" name="report_ids" id="report_ids" />
                    <td id="buyer_td">
                        <?
                            echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td id="location_td">
                    	<?
                            echo create_drop_down( "cbo_location", 120, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td id="floor_td" style="display:none;">
                    	<?
                            echo create_drop_down( "cbo_floor", 110, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td align="center">
					<?
						//$year_current=date("Y");

                        echo create_drop_down( "cbo_job_year", 50, $year,"", 1, "All",'','','');
                    ?>
                    </td>

                    <td align="center">
                        <input name="txt_style_ref_number" id="txt_style_ref_number"ondblclick="openmypage_style()" onKeyUp="fnc_chng_jobNo(this.value)" class="text_boxes" style="width:70px" placeholder="Browse Only"  readonly />
                    </td>

                    <td>
                        <input style="width:80px;"  name="txt_style_ref" id="txt_style_ref"  ondblclick="openmypage_style()" onKeyUp="fnc_chng_jobNo(this.value)" class="text_boxes" placeholder="Browse Only"  readonly />
                        <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>    <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>
                    </td>
                    <td>
                        <input style="width:100px;"  name="txt_order" id="txt_order"  ondblclick="openall_order()" onKeyUp="fnc_chng_orderNo(this.value)"  class="text_boxes" placeholder="Browse or Write"   />
                        <input type="hidden" name="txt_order_id" id="txt_order_id"/> <input type="hidden" name="txt_order_id_no" id="txt_order_id_no"/>
                    </td>

                    <td style="display:none;">
                    	<?
                            $arr = array(1=>"ALL",2=>"Woven",3=>"Knit");
							echo create_drop_down( "cbo_garments_nature", 70, $arr,"", 0, "-- Select --", $selected, "",0,"" );
                        ?>
                    </td>
					<td>
						<?
						$date_type_arr=array(1=>'Pub. Shipment Date',2=>'Org. Shipment Date',3=>'PO Receive Date',4=>'PO Insert Date');
						echo create_drop_down( "cbo_date_type", 130, $date_type_arr, "", 1, "----Select----",1, "date_fill_change(this.value);",0,"" );
						?>
					</td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" >&nbsp; To
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date"  ></td>
                    <td>
					<?
						$shipment_status_fashion=array(0=>"ALL",1=>"Full Pending",2=>"Partial Shipment",3=>"Full Shipment",4=>"Full Shipment & Partial Shipment");
                   		echo create_drop_down( "shipping_status", 90, $shipment_status_fashion,"", 0, "", 0, "",0,'','','','','' );
                    ?>
                    </td>
                    <td>
					<?
						$active_st=array(1=>"ALL",0=>"Active");
                   		echo create_drop_down( "active_status", 90, $active_st,"", 0, "", 0, "",0,'','','','','' );
                    ?>
                    </td>
                    <td>
					<?
						$order_st=array(0=>"ALL",1=>"Confirmed",2=>"Projected");
                   		echo create_drop_down( "orderStatus", 90, $order_st,"", 0, "", 0, "",0,'','','','','' );
                    ?>
                    </td>

                    <td width="290px;" align="center">
                        <input type="button" id="show_button" class="formbutton" style="width:80px; float:left; " value="Order Wise" onClick="fn_report_generated(1)"/>
                        <input type="button" id="show_button2" class="formbutton" style="width:80px;float:left; display:block" value="Color Wise" onClick="fn_report_generated(2)" />
                        <input type="button" id="show_button1" class="formbutton" style="width:115px; float:left; display:block " value="Job And Color Wise" onClick="fn_report_generated(3)" />
                    </td>
                </tr>
                </tbody>
            </table>
            <table>
            	<tr>
                	<td>
 						<? echo load_month_buttons(1); ?>
                    </td>
					<td>

					</td>
                </tr>
            </table>
            <table align="left">
                <tr id="check_uncheck_tr" style="display:none;">
                    <td><input type="checkbox" id="check_uncheck" name="check_uncheck" onClick="fn_check_uncheck()"/> <strong style="color:#176aaa; font-size:14px; font-weight:bold;">Check/Uncheck All</strong>
                    </td>
                </tr>
            </table>
            <br />
        </fieldset>
    </div>
    </div>

    <div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2" align="left"></div>
 </form>
</body>

<script>
//set_multiselect('cbo_company_name','0','0','','0');
$('#cbo_location').val(0);
$('#active_status').val(0);
set_multiselect('cbo_company_name','0','0','0','0');
$("#multiselect_dropdown_table_headercbo_company_name").click(function(){
	var data=$("#cbo_company_name").val();
	//alert(data);return;
	load_drop_down( 'requires/order_wise_fabric_to_rmg_production_status_report_controller',data, 'load_drop_down_buyer', 'buyer_td' );
	load_drop_down( 'requires/order_wise_fabric_to_rmg_production_status_report_controller', data, 'load_drop_down_location', 'location_td' );

	$(function(){
		ClearTextBoxValues();
		//fnc_load_report_format(data);
	});

});

</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<!--<script class="include" type="text/javascript" src="../../js/chart/logic_chart.js"></script>-->
</html>
