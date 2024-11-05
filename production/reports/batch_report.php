<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Batch Reprot
Functionality	:
JS Functions	:
Created by		:	Aziz
Creation date 	: 	21-01-2014
Updated by 		: 	Aziz
Update date		: 	28-04-2014
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

var tableFilters =
{
	col_30: "none",
	col_operation: {
	id: ["value_total_btq"],
	col: [19],
	operation: ["sum"],
	write_method: ["innerHTML"]
	}
}
var tableFilters2 =
{
	col_30: "none",
	col_operation: {
	id: ["value_total_btq_subcon"],
	col: [16],
	operation: ["sum"],
	write_method: ["innerHTML"]
	}
}
var tableFilters3 =
{
	col_30: "none",
	col_operation: {
	id: ["value_total_sam_btq"],
	col: [18],
	operation: ["sum"],
	write_method: ["innerHTML"]
	}
}
function fn_report_generated(operation)
{
	var b_number=document.getElementById('batch_number').value;
	var batch_no=document.getElementById('batch_number_show').value;
	var booking_number=document.getElementById('txt_hide_booking_id').value;
	var booking_no=document.getElementById('txt_booking_no').value;
	var working_company_id=document.getElementById('cbo_working_company_id').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
		//alert(batch_number);job_number_show
	var order_no=document.getElementById('order_no').value;
	var hidden_order=document.getElementById('hidden_order_no').value;
	var hidden_ext=document.getElementById('hidden_ext_no').value;
	var ext_no=document.getElementById('txt_ext_no').value;
	var j_number=document.getElementById('job_number').value;
	var job_number=document.getElementById('job_number_show').value;
	var repot_type=document.getElementById('cbo_type').value;
	var file_no=document.getElementById('file_no').value;
	var floor_no=document.getElementById('cbo_floor').value;
	var ref_no=document.getElementById('ref_no').value;
	if(j_number!="" || job_number!="" || order_no!="" || file_no!="" || floor_no!="" || ref_no!="" || hidden_order!="" || ext_no!="" || hidden_ext!="" || batch_no!="" || booking_no!="" || repot_type==2 || repot_type==3 || repot_type==4)
	{

		if(cbo_company_name == 0 && working_company_id ==0)
		{
			alert("Please Select either a company or a working company");
			return;
		}

	}
	else
	{
		/*if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
		{
			return;
		}*/
		if(cbo_company_name == 0 && working_company_id ==0)
		{
			alert("Please Select either a company or a working company");
			return;
		}
		else if( form_validation('txt_date_from*txt_date_to','Form Date*To Date')==false )
		{
			return;
		}

	}
	//alert(operation);
	if(operation==1)
	{
		var actions='batch_report';
	}else{
		var actions='batch_report_show2';
	}
		freeze_window(5);
	    var data="action="+actions+get_submitted_data_string('cbo_company_name*cbo_working_company_id*cbo_buyer_name*job_number_show*job_number*batch_number*batch_number_show*txt_hide_booking_id*txt_booking_no*txt_ext_no*hidden_ext_no*order_no*hidden_order_no*cbo_type*cbo_year*cbo_batch_type*txt_date_from*txt_date_to*file_no*ref_no*cbo_floor',"../../");
		//alert(data);
			http.open("POST","requires/batch_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_batch_report;
}
function fnc_show_batch_report()
{
	/*if(http.readyState == 4)
	{
		// alert(http.responseText);
		document.getElementById('report_container2').innerHTML=http.responseText;
		document.getElementById('report_container').innerHTML=report_convert_button('../../');
		setFilterGrid("table_body",-1,tableFilters);
		setFilterGrid("table_body2",-1,tableFilters2);
		setFilterGrid("table_body3",-1,tableFilters3);
		show_msg('3');
		release_freezing();
	}*/
	if(http.readyState == 4)
	{
		release_freezing();
		// alert(http.responseText);
		var response=trim(http.responseText).split("####");
		//alert(response[3]);
		$("#report_container2").html(response[0]);
		// document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		// document.getElementById('report_container').innerHTML = report_convert_button('../../');
        // append_report_checkbox('table_header_1', 1);


		if(response[2]==0 || response[2]==1 || response[2]==2 || response[2]==3)
		{
			//document.getElementById('report_container').innerHTML=report_convert_button('../../');

			document.getElementById('report_container').innerHTML = '<a href="requires/' + response[1] + '" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			append_report_checkbox('table_header_1',1);

			document.getElementById("check_uncheck_tr").style.display="table";
			if($("#check_uncheck").is(":checked")==false)
				$("#check_uncheck").attr("checked","checked");


			setFilterGrid("table_body",-1,tableFilters);
			setFilterGrid("table_body2",-1,tableFilters2);
			setFilterGrid("table_body3",-1,tableFilters3);
			var level= new Array();
			var leveld= new Array();
			var obj=JSON.parse(response[2]);
			var objd=JSON.parse(response[3]);
			for(i in obj){
				level.push(obj[i])
				leveld.push(objd[i])
			}
			show_msg('3');

		}

		else{
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		}


	}
}

function fn_check_uncheck(){
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

function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	$('#table_body tr:first').hide();
	$('#table_body2 tr:first').hide();
	$('#table_body3 tr:first').hide();

	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close();
	$('#table_body tr:first').show();
	$('#table_body2 tr:first').show();
	$('#table_body3 tr:first').show();
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="330px";
}
<!--BatchNumber -->
function batchnumber()
{
	if(form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_name').value;
	var batch_number=document.getElementById('batch_number_show').value;
	var page_link="requires/batch_report_controller.php?action=batchnumbershow&company_name="+company_name;
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
<!--BookingNumber -->
function openmypage_booking()
{
    if( form_validation('cbo_company_name','Company Name')==false )
    {
        return;
    }
    var companyID = $("#cbo_company_name").val();
    var buyer_name = $("#cbo_buyer_name").val();
    var cbo_year_id = $("#cbo_year").val();
    //var cbo_month_id = $("#cbo_month").val();
    var page_link='requires/batch_report_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
    var title='Booking No Search';
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1010px,height=370px,center=1,resize=1,scrolling=0','../');
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var booking_no=this.contentDoc.getElementById("hide_booking_no").value;
        var booking_id=this.contentDoc.getElementById("hide_booking_id").value;
        $('#txt_booking_no').val(booking_no);
        $('#txt_hide_booking_id').val(booking_id);
    }
}
<!--JobNumber -->
function jobnumber(id)
{
	if(form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_name').value;
	var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	var year=document.getElementById('cbo_year').value;
	var batch_type=document.getElementById('cbo_batch_type').value;
	//alert(batch_type);
	var page_link="requires/batch_report_controller.php?action=jobnumbershow&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&year="+year+"&batch_type="+batch_type;
	var title="Job Number";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("selected_id").value;
		//var job=theemail.split("_");
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
	var page_link="requires/batch_report_controller.php?action=order_number_popup&company_name="+company_name+"&buyer_name="+buyer_name+"&year="+year+"&batch_type="+batch_type;
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
	var page_link="requires/batch_report_controller.php?action=batchextensionpopup&company_name="+company_name+"&buyer_name="+buyer_name+"&year="+year+"&job_number_show="+job_number_show+"&batch_number_show="+batch_number_show+"&batch_number_hidden="+batch_number_hidden;
	//var page_link="requires/batch_report_controller.php?action=batchextensionpopup&company_name="+company_name;
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

function toggle( x, origColor )
{
	var newColor = 'green';
	if ( x.style ) {
		x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
	}
}

function js_set_value( str ) {
	toggle( document.getElementById( 'tr_' + str), '#FFF' );
}

	function generate_batch_print_report(print_btn,company_id,sys_id,batch_no,working_company,ext_no,batch_sl_no,booking_no_id,roll_maintained,entry_form)
	{

		var report_title="Batch Creation";
		//   alert(print_btn);
		if(entry_form==36)
		{
			var report_title="SubCon Batch Creation";
			window.open("../../subcontract_bill/requires/subcon_batch_creation_controller.php?data=" + company_id+'*'+sys_id+'*'+batch_sl_no+'*'+report_title+'*'+1+'&action=batch_card_print', true );
		}
		else
		{
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
		}

	}
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
         <h3 style="width:1470px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel" >
             <fieldset style="width:1470px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th>Batch Type</th>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Working Company</th>
                            <th>Floor No</th>
                            <th>Buyer</th>
                            <th>Year</th>
                            <th>Job No</th>
                            <th>Batch No</th>
                            <th>Booking No</th>
                            <th>Ext. No</th>
                            <th>Order No</th>
                            <th>File No</th>
                            <th>Ref. No</th>
                            <th>Report Type</th>
                            <th class="must_entry_caption">Batch Date</th>
                            <th colspan="2"><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:100px" /></th>
                        </thead>
                        <tbody>
                            <tr>
                                 <td>
                                    <?
                                        $batch_type_arr=array(1=>"Self Batch",2=>"SubCon Batch",3=>"Sample Batch");
                                        echo create_drop_down( "cbo_batch_type",70, $batch_type_arr,"",1, "--All--", 0,"load_drop_down('requires/batch_report_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer', 'cbo_buyer_name_td' );",0 );
                                    ?>
                                </td>
                                <td>
                                    <?
                                        echo create_drop_down( "cbo_company_name", 100, "select id,company_name from lib_company where status_active=1 and is_deleted=0 and core_business not in(3)  order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down('requires/batch_report_controller',this.value+'_'+document.getElementById('cbo_batch_type').value, 'load_drop_down_buyer','cbo_buyer_name_td' );" );
                                    ?>
                                </td>


                                <td>
                                    <?
                                        echo create_drop_down( "cbo_working_company_id", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '--Select Working--', 0,"load_drop_down('requires/batch_report_controller',this.value, 'load_drop_down_floor', 'td_floor' );",'','','','','',3);
                                    ?>
                                </td>

                                <td id="td_floor">
									<?
									echo create_drop_down("cbo_floor", 100, $blank_array,"", 1, "-- Select Floor--", 0, "",0,"","","","");
									?></td>

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
                                     <input type="text"  name="job_number_show" id="job_number_show" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write/Browse" onDblClick="jobnumber();">
                                     <input type="hidden" name="job_number" id="job_number">
                                 </td>
                                <td>
                                     <input type="text"  name="batch_number_show" id="batch_number_show" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write/Browse" onDblClick="batchnumber();">
                                     <input type="hidden" name="batch_number" id="batch_number">
                                </td>
                                 <td>
                                     <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:80px" placeholder="Browse Or Write" onDblClick="openmypage_booking();" >
                                <input type="hidden" name="txt_hide_booking_id" id="txt_hide_booking_id" readonly>
                                </td>
                                 <td>
                                     <input type="text"  name="txt_ext_no" id="txt_ext_no" class="text_boxes" style="width:60px;" tabindex="1" placeholder="Write/Browse Search" onDblClick="batch_extension();">
                                     <input type="hidden" name="hidden_ext_no" id="hidden_ext_no">
                                </td>
                                  <td>
                                     <input type="text"  name="order_no" id="order_no" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write/Browse" onDblClick="openmypage_order()">
                                     <input type="hidden" name="hidden_order_no" id="hidden_order_no">
                                </td>
                                <td>
                                     <input type="text"  name="file_no" id="file_no" class="text_boxes" style="width:60px;" tabindex="1" placeholder="Write" >

                                </td>
                                 <td>
                                     <input type="text"  name="ref_no" id="ref_no" class="text_boxes" style="width:60px;" tabindex="1" placeholder="Write" >

                                </td>

                                <td>
                                <?
								$search_by_arr=array(1=>"Date Wise Report",2=>"Wait For Heat Setting",5=>"Wait For Singeing",3=>"Wait For Dyeing",4=>"Wait For Re-Dyeing");
								echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "", "",'',0 );
								?>
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/>
                                     &nbsp;To&nbsp;
                                     <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
                                </td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" /></td>
								<td><input type="button" id="show_button" class="formbutton" style="width:50px" value="Show 2" onClick="fn_report_generated(2)" /></td>
                            </tr>

                        </tbody>
                    </table>
                    <table>
            	<tr>
                	<td colspan="10">
 						<? echo load_month_buttons(1); ?>
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
            <div id="report_container"></div>
    		<div id="report_container2"></div>
		</form>
	</div>

</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>