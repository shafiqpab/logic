<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Batch Reprot
Functionality	:
JS Functions	:
Created by		:	Aziz
Creation date 	: 	21-01-2014
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
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Daily Batch Creation Report", "../../", 1, 1,'','','');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

	var show2TableFilters =
	{
		col_operation: {
		id: ["value_batch_qnty","value_total_trims_weight","value_batch_weight","value_roll_no"],
		col: [33,34,35,36],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	var tableFilters =
	{
		col_operation: {
		id: ["value_batch_qnty","value_total_trims_weight","value_batch_weight","value_roll_no"],
		col: [33,34,35,36],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	var tableFilters_2 =
	{
		col_operation: {
		id: ["value_sbatch_qnty","value_stotal_trims_weight","value_sbatch_weight","value_s_roll_total"],
		col: [16,17,18,19],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

function fn_report_generated(operation)
{
	var b_number=document.getElementById('batch_number').value;
	var batch_no=document.getElementById('batch_number_show').value;
	var hidden_ext=document.getElementById('hidden_ext_no').value;
	var ext_no=document.getElementById('txt_ext_no').value;
	var j_number=document.getElementById('job_number').value;
	var job_number=document.getElementById('job_number_show').value;
	var fso_number=document.getElementById('fso_number_show').value;
	var booking_no=document.getElementById('txt_booking_no_show').value;
	var cbo_batch_against=document.getElementById('cbo_batch_against').value;
	var cbo_load_unload=document.getElementById('cbo_load_unload').value;
	var txt_style_no=document.getElementById('txt_style_no').value;
	var txt_int_ref_no=document.getElementById('txt_int_ref_no').value;


	if(j_number!="" || job_number!="" || ext_no!="" || hidden_ext!="" || batch_no!="" || booking_no !="" || fso_number != "" || txt_style_no !="" || txt_int_ref_no !="")
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
	}
	else
	{
		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
		{
			return;
		}
	}
	freeze_window(5);
 	var data="action=batch_report&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_name*job_number_show*job_number*batch_number*batch_number_show*txt_ext_no*hidden_ext_no*cbo_year*cbo_batch_type*txt_style_no*fso_number_show*fso_number*txt_booking_no_show*txt_booking_no*txt_date_from*txt_date_to*cbo_batch_against*cbo_load_unload*txt_int_ref_no',    "../../");

	http.open("POST","requires/batch_report_for_sales_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_show_batch_report;
}
function fnc_show_batch_report()
{
	if(http.readyState == 4)
	{
		var reponse=trim(http.responseText).split("**");
		$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			// alert(reponse[2]);
			if(reponse[2]==2)
			{
				setFilterGrid("table_body",-1,show2TableFilters);
			}
			else
			{
				setFilterGrid("table_body",-1,tableFilters);
			}
			if(document.getElementById('table_body2'))
			{
				setFilterGrid("table_body2",-1,tableFilters_2);
			}


		show_msg('3');
		release_freezing();

	}
}
function new_window()
	{
		if(document.getElementById('table_body'))
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$('#scroll_body tr:first').hide();
		}

		if(document.getElementById('table_body2'))
		{
			document.getElementById('scroll_body_subcon').style.overflow="auto";
			document.getElementById('scroll_body_subcon').style.maxHeight="none";
			$('#scroll_body_subcon tr:first').hide();
		}

		var w = window.open("Surprise", "#");

		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		if(document.getElementById('table_body'))
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="397px";
	        $('#scroll_body tr:first').show();
	    }

        if(document.getElementById('table_body2'))
		{
			document.getElementById('scroll_body_subcon').style.overflow="auto";
			document.getElementById('scroll_body_subcon').style.maxHeight="397px";
	        $('#scroll_body_subcon tr:first').show();
		}
	}

function batchnumber()
{
	if(form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_name').value;
	var batch_number=document.getElementById('batch_number_show').value;
	var year=document.getElementById('cbo_year_selection').value;
	var page_link="requires/batch_report_for_sales_controller.php?action=batchnumbershow&company_name="+company_name+"&year="+year;
	var title="Batch Number";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("selected_id").value;
		var batch=theemail.split("_");
		document.getElementById('batch_number').value=batch[0];
		document.getElementById('batch_number_show').value=batch[1];
		release_freezing();
	}
}

function jobnumber()
{
	if(form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_name').value;
	var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	var year=document.getElementById('cbo_year').value;
	var batch_type=document.getElementById('cbo_batch_type').value;

	var page_link="requires/batch_report_for_sales_controller.php?action=jobnumbershow&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&year="+year+"&batch_type="+batch_type;
	var title="Job Number";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("selected_id").value;

		document.getElementById('job_number').value=theemail;
		document.getElementById('job_number_show').value=theemail;
		release_freezing();
	}
}
function openmypage_order(id)
{
	if(form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_name').value;
	var buyer_name=document.getElementById('cbo_buyer_name').value;
	var year=document.getElementById('cbo_year').value;
	var job_number=document.getElementById('job_number_show').value;
	var batch_number=document.getElementById('batch_number_show').value;
	var ext_number=document.getElementById('txt_ext_no').value;
	var year=document.getElementById('cbo_year').value;
	var batch_type=document.getElementById('cbo_batch_type').value;
	var page_link="requires/batch_report_for_sales_controller.php?action=order_number_popup&company_name="+company_name+"&buyer_name="+buyer_name+"&year="+year+"&batch_type="+batch_type;
	var title="Order Number";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=420px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("selected_id").value;
		//var job=theemail.split("_");
		document.getElementById('hidden_order_no').value=theemail;
		document.getElementById('order_no').value=theemail;
		release_freezing();
	}
}

function batch_extension()
{
	var company_name=document.getElementById('cbo_company_name').value;
	var buyer_name=document.getElementById('cbo_buyer_name').value;
	var year=document.getElementById('cbo_year').value;
	var job_number=document.getElementById('job_number_show').value;
	var batch_number=document.getElementById('batch_number_show').value;
	var batch_number_hidden=document.getElementById('batch_number').value;
	if(form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_name').value;
	var page_link="requires/batch_report_for_sales_controller.php?action=batchextensionpopup&company_name="+company_name+"&buyer_name="+buyer_name+"&year="+year+"&job_number_show="+job_number_show+"&batch_number_show="+batch_number_show+"&batch_number_hidden="+batch_number_hidden;
	//var page_link="requires/batch_report_for_sales_controller.php?action=batchextensionpopup&company_name="+company_name;
	var title="Extention Number";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=400px,center=1,resize=0,scrolling=0','../')

	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("selected_id").value;
		var batch=theemail.split("_");
		document.getElementById('txt_ext_no').value=batch[1];
		release_freezing();
	}
}

	function toggle( x, origColor ) {
		var newColor = 'green';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function js_set_value( str ) {
		toggle( document.getElementById( 'tr_' + str), '#FFF' );
	}

	function fsoNumber()
	{
 		var cbo_company_id = $('#cbo_company_name').val();
	 	if (form_validation('cbo_company_name', 'Company') == false) { return; }
	 	else
	 	{
 			var title = 'FSO Selection Form';
 			var page_link = 'requires/batch_report_for_sales_controller.php?cbo_company_id=' + cbo_company_id + '&action=FSO_No_popup';

 			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0', '../');

 			emailwindow.onclose = function ()
 			{
	 			var theform=this.contentDoc.forms[0];
				var fso_no=this.contentDoc.getElementById("hide_fso_no").value;
				var fso_id=this.contentDoc.getElementById("hide_fso_id").value;
				//alert (job_no);
				$('#fso_number_show').val(fso_no);
				$('#fso_number').val(fso_id);
 			}
 		}
 	}

 	function openmypage_booking()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var cbo_year = $("#cbo_year").val();

		var page_link='requires/batch_report_for_sales_controller.php?action=booking_no_popup&companyID='+companyID+ '&cbo_year='+cbo_year;
		var title='Booking No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=710px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_no=this.contentDoc.getElementById("hide_job_no").value;
			var booking_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_booking_no_show').val(booking_no);
			$('#txt_booking_no').val(booking_no);
		}
	}
	function clear_hidden(src)
	{
		if(src == 'book')
		{
			$('#txt_booking_no').val("");
		}
	}

	function generate_batch_print_report(print_btn,company_id,sys_id,batch_no,working_company,ext_no,batch_sl_no,booking_no_id,roll_maintained)
	{
		var report_title="Batch Creation";
		//   alert(print_btn);
		if(print_btn==86)
		{
			// $("#Print_1").show();
			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+working_company+'&action=batch_card_print', true );
		}
		else if(print_btn==185)//Print Button 2;
		{
			// $("#Print2").show();
			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+booking_no_id+'*'+working_company+'*'+roll_maintained+'&action=batch_card_print_2', true );
		}
		else if(print_btn==186)//Print Button 3;
		{
			// $("#Print3").show();
			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+booking_no_id+'*'+working_company+'&action=batch_card_print_3', true );
		}
		if(print_btn==187)//Print Button 4;
		{
			// $("#Print4").show();
			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+booking_no_id+'*'+working_company+'*'+roll_maintained+'&action=batch_card_print_4', true );
		}
		if(print_btn==224)//Print Button 5;
		{
			// $("#Print5").show();
			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+booking_no_id+'*'+working_company+'*'+roll_maintained+'&action=batch_card_print_5', true );
		}
		if(print_btn==225)//Print Button 6;
		{
			// $("#Print6").show();
			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+booking_no_id+'*'+working_company+'*'+roll_maintained+'&action=batch_card_print_6', true );
		}
		if(print_btn==226)//Print Button 7;
		{
			// $("#Print7").show();
			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+booking_no_id+'*'+working_company+'*'+roll_maintained+'&action=batch_card_print_7', true );
		}
		if(print_btn==220)//Print Button 8;
		{
			// $("#Print8").show();
			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+booking_no_id+'*'+working_company+'*'+roll_maintained+'&action=batch_card_print_8', true );
		}
		if(print_btn==235)//Print Button 9;
		{
			// $("#Print9").show();
			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+booking_no_id+'*'+working_company+'*'+roll_maintained+'&action=batch_card_print_9', true );
		}
		if(print_btn==274)//Print Button 10;
		{
			// $("#Print10").show();
			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+booking_no_id+'*'+working_company+'*'+roll_maintained+'&action=batch_card_print_10', true );
		}
		if(print_btn==241)//Print Button 11;
		{
			// $("#Print11").show();
			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+booking_no_id+'*'+working_company+'*'+roll_maintained+'&action=batch_card_print_11', true );
		}
		if(print_btn==269)//Print Button 12;
		{
			// $("#Print12").show();
			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+booking_no_id+'*'+working_company+'*'+roll_maintained+'&action=batch_card_print_12', true );
		}
		if(print_btn==324)//Prog.Wise;
		{
			// $("#Print13").show();
			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+booking_no_id+'*'+working_company+'*'+roll_maintained+'&action=batch_card_prog_wise', true );
		}
		if(print_btn==280)//Print Button 14;
		{
			// $("#Print14").show();
			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+booking_no_id+'*'+working_company+'*'+roll_maintained+'&action=batch_card_print_14', true );
		}
		if(print_btn==304)//Print Button 15;
		{
			// $("#Print15").show();
			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+booking_no_id+'*'+working_company+'*'+roll_maintained+'&action=batch_card_print_15', true );
		}
		if(print_btn==719)//Print Button 16;
		{
			// $("#Print16").show();
			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+booking_no_id+'*'+working_company+'*'+roll_maintained+'&action=batch_card_print_19', true );
		}
		if(print_btn==723)//Print Button 17;
		{
			// $("#Print17").show();
			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+booking_no_id+'*'+working_company+'*'+roll_maintained+'&action=batch_card_print_17', true );
		}
		if(print_btn==339)//Print Button 18;
		{
			// $("#Print18").show();
			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+working_company+'&action=batch_card_print_18', true );
		}
		if(print_btn==370)// batch card 19
		{

			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+working_company+'&action=batch_card_print_20', true );
		}
		if(print_btn==404)// batch card 21
		{

			window.open("../requires/batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+batch_no+'*'+ext_no+'*'+report_title+'*'+booking_no_id+'*'+working_company+'*'+roll_maintained+'&action=batch_card_print_22', true );
		}
	}
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
         <h3 style="width:1610px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel" >
             <fieldset style="width:1610px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Batch Type</th>
                            <th>Buyer</th>
                            <th>Year</th>
                            <th>Style No.</th>
                            <th>Job No</th>
                            <th>FSO No</th>
                            <th>Fabric Booking No</th>
							<th>Int Ref No.</th>
                            <th>Batch Against</th>
                            <th>Batch Status</th>
                            <th>Batch Number</th>
                            <th>Ext. No</th>
                            <th class="must_entry_caption">Batch Date</th>
                            <th colspan="3" ><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td>
                                    <?
                                        echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down('requires/batch_report_for_sales_controller',this.value+'_'+document.getElementById('cbo_batch_type').value, 'load_drop_down_buyer','cbo_buyer_name_td' );" );
                                    ?>
                                </td>
								 <td>
                                    <?
                                        $batch_type_arr=array(1=>"Self Batch",2=>"SubCon Batch");//,3=>"Sample Batch"
                                        echo create_drop_down( "cbo_batch_type",70, $batch_type_arr,"",1, "--All--", 0,"load_drop_down('requires/batch_report_for_sales_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer', 'cbo_buyer_name_td' );",0 );
                                    ?>
                                </td>
                                <td id="cbo_buyer_name_td">
                                	<?
										 echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
										  // echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
									?>
                                </td>
                                 <td id="extention_td">
                                	<?
                                       echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", "", "",0,"" );
									?>
                                </td>
                                <td>
                                     <input type="text"  name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write">
                                </td>
                                <td>
                                    <input type="text"  name="job_number_show" id="job_number_show" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write/Browse" onDblClick="jobnumber();">
                                     <input type="hidden" name="job_number" id="job_number">
                                </td>

                                <td>
                                     <input type="text"  name="fso_number_show" id="fso_number_show" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Browse" onDblClick="fsoNumber();" readonly>
                                     <input type="hidden" name="fso_number" id="fso_number">
                                </td>
                                <td>
                                     <input type="text"  name="txt_booking_no_show" id="txt_booking_no_show" class="text_boxes" style="width:100px;" tabindex="1" placeholder="Write/Browse" onDblClick="openmypage_booking();" onKeyUp="clear_hidden('book');" readonly>
                                     <input type="hidden" name="txt_booking_no" id="txt_booking_no">
                                </td>
								<td>
                                     <input type="text"  name="txt_int_ref_no" id="txt_int_ref_no" class="text_boxes" style="width:100px;" tabindex="1" placeholder="Write">
                                </td>
                                <td>
                                    <?
                                       echo create_drop_down( "cbo_batch_against", 120, $batch_against,"", 1, '--- Select ---', "", "",'','1,2,3,5,7','','','',1 );
									?>
                                </td>
                                <td>
                                    <?
                                    $load_unload_arr = array(1=>"Loading",2=>"Un-loading",3=>"Waiting for loading");
                                    echo create_drop_down( "cbo_load_unload", 110, $load_unload_arr,'', '1', '---- Select ----', '',"",'','','','','',1);
									?>
                                </td>
                                <td>
                                     <input type="text"  name="batch_number_show" id="batch_number_show" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write/Browse" onDblClick="batchnumber();">
                                     <input type="hidden" name="batch_number" id="batch_number">
                                </td>
                                 <td>
                                     <input type="text"  name="txt_ext_no" id="txt_ext_no" class="text_boxes" style="width:60px;" tabindex="1" placeholder="Write/Browse Search" onDblClick="batch_extension();">
                                     <input type="hidden" name="hidden_ext_no" id="hidden_ext_no">
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/>
                                     &nbsp;To&nbsp;
                                     <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
                                </td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" /></td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:50px" value="Show 2" onClick="fn_report_generated(2)" /></td>
								<td><input type="button" id="show_button" class="formbutton" style="width:50px" value="Show 3" onClick="fn_report_generated(3)" /></td>
                            </tr>
                            <tr>
                                <td align="center" colspan="12">
                                    <? echo load_month_buttons(1); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <div id="report_container"></div>
    		<div id="report_container2"></div>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>