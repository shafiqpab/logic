<?php
/*-------------------------------------------- Comments -----------------------
Purpose			: This Form Will Create Knitting Program Report.
Functionality	:	
JS Functions	:
Created by		: Md. Nuruzzaman 
Creation date 	: 20-07-2020
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
echo load_html_head_contents("Knitting Program Report", "../../", 1, 1,'',1,1);
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';

/*
|--------------------------------------------------------------------------
| order no browse
|--------------------------------------------------------------------------
*/
function openmypage_order()
{
	if(form_validation('cbo_company_name','LC. Company Name')==false)
	{
		return;
	}
	
	var companyID = $("#cbo_company_name").val();
	var jobYear = $("#cbo_year").val();
	var data=companyID+'_'+jobYear;
	var page_link='requires/knitting_program_report_controller.php?action=order_no_search_popup&data='+data;
	var title='Order No Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var order_no=this.contentDoc.getElementById("hide_order_no").value;
		var order_id=this.contentDoc.getElementById("hide_order_id").value;
		
		$('#txt_order_no').val(order_no);
		$('#hide_order_id').val(order_id);	 
	}
}

/*
|--------------------------------------------------------------------------
| booking no browse
|--------------------------------------------------------------------------
*/
function openmypage_booking()
{
	if( form_validation('cbo_company_name','LC. Company Name')==false )
	{
		return;
	}

	var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value+'_'+$('#cbo_booking_type').val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/knitting_program_report_controller.php?action=booking_no_search_popup&data='+data,'Booking No Popup', 'width=1050px,height=420px,center=1,resize=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			freeze_window(5);
			document.getElementById("txt_booking_no").value=theemail.value;
			release_freezing();
		}
	}
}

/*
|--------------------------------------------------------------------------
| show report
|--------------------------------------------------------------------------
*/
function fn_report_generated(presentationType)
{
	/*if (form_validation('cbo_company_name','Comapny Name') == false)
	{
		return;
	}*/
	
	if($('#cbo_type').val() == 1 || $('#cbo_type').val() == 2)
	{
		/*if (form_validation('cbo_party_type','W. Comapny/Party Name') == false)
		{
			return;
		}*/
	}
	
	if($('#txt_job_no').val() != '' || $('#txt_internal').val() != '')
	{
		if (form_validation('cbo_company_name','LC. Comapny Name') == false)
		{
			return;
		}
	}
	
	if(presentationType==2)
	{
		var txt_job_no = $("#txt_job_no").val();
		var txt_order_no = $("#txt_order_no").val();
		if(txt_job_no=="" && txt_order_no=="")
		{
			alert("Please Insert Job or Order No.");
			$('#txt_job_no').focus();	
			return;
		}
	}
	
	//var data="action=report_generate"+get_submitted_data_string('cbo_type*cbo_company_name*cbo_buyer_name*txt_job_no*txt_machine_dia*cbo_party_type*txt_booking_no*txt_order_no*txt_file_no*txt_internal*hide_order_id*txt_program_no*txt_machine_no*txt_date_from*txt_date_to*cbo_knitting_status*cbo_based_on*cbo_year',"../../")+'&presentationType='+presentationType;
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_type*cbo_party_type*cbo_buyer_name*cbo_location_id*cbo_floor_id*txt_machine_dia*cbo_year*txt_job_no*txt_file_no*txt_internal*txt_order_no*hide_order_id*cbo_booking_type*txt_booking_no*txt_program_no*cbo_knitting_status*cbo_based_on*txt_date_from*txt_date_to',"../../")+'&presentationType='+presentationType;
	freeze_window(3);
	http.open("POST","requires/knitting_program_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse;
}
	
/*
|--------------------------------------------------------------------------
| reponse of show report
|--------------------------------------------------------------------------
*/
function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		var response=trim(http.responseText).split("####");
		$('#report_container2').html(response[0]);
		/*
		document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:110px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:100px"/>&nbsp;&nbsp;<input type="button" onclick="generate_requisition_report()" value="Requisition Print" name="Print" id="Print" class="formbutton" style="width:120px;display:none;"/>&nbsp;&nbsp;<input type="button" onclick="generate_requisition_report_two()" value="Requisition Print2" name="Print" id="Print2" class="formbutton" style="width:120px;display:none;"/>&nbsp;&nbsp;<input type="button" onclick="generate_requisition_report_three()" value="Requisition Print3" name="Print" id="Print3" class="formbutton" style="width:120px;display:none;"/>&nbsp;&nbsp;<input type="button" onclick="generate_requisition_report_four()" value="Requisition Print4" name="Print" id="Print4" class="formbutton" style="width:120px;display:none;"/>&nbsp;&nbsp;<input type="button" onclick="generate_knitting_card(1)" value="Knitting Card" name="card" id="Print5" class="formbutton" style="width:100px;display:none;"/>&nbsp;&nbsp;<input type="button" onclick="generate_knitting_card(2)" value="Knitting Card 2" name="card" id="Print6" class="formbutton" style="width:100px;display:none;"/>&nbsp;&nbsp;<input type="button" onclick="generate_knitting_card(3)" value="Knitting Card 3" name="card" id="Print7" class="formbutton" style="width:100px;display:none;"/>&nbsp;&nbsp;<input type="button" onclick="generate_knitting_card(4)" value="Knitting Card 4" name="card" id="Print8" class="formbutton" style="width:100px;"/>&nbsp;&nbsp;<input type="button" onclick="generate_knitting_card(5)" value="Knitting Card 5" name="card" id="Print9" class="formbutton" style="width:100px;"/>&nbsp;&nbsp;<input type="button" onclick="generate_knitting_card(6)" value="Knitting Card 6" name="card" id="Print10" class="formbutton" style="width:100px;"/>';
		*/ 
		document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:110px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:100px"/>'; 
		var company_id = $("#cbo_company_name").val();
		get_php_form_data( company_id, 'company_wise_report_button_setting','requires/knitting_program_report_controller' );

		show_msg('3');
		release_freezing();
 	}
}

/*
|--------------------------------------------------------------------------
| func_onchange_company
|--------------------------------------------------------------------------
*/
function func_onchange_company()
{
	if($('#cbo_company_name').val() != 0)
	{
		$('#cbo_party_type').val(0).attr('disabled', 'disabled');
	}
	else
	{
		$('#cbo_party_type').val(0).removeAttr('disabled');
	}
}

/*
|--------------------------------------------------------------------------
| func_onchange_party
|--------------------------------------------------------------------------
*/
function func_onchange_party()
{
	if($('#cbo_party_type').val() != 0)
	{
		$('#cbo_company_name').val(0).attr('disabled', 'disabled');
	}
	else
	{
		$('#cbo_company_name').val(0).removeAttr('disabled');
	}
}





function openmypage_job()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	
	var companyID = $("#cbo_company_name").val();
	var page_link='requires/knitting_program_report_controller.php?action=job_no_search_popup&companyID='+companyID;
	var title='Job No Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var job_no=this.contentDoc.getElementById("hide_job_no").value;
		var job_id=this.contentDoc.getElementById("hide_job_id").value;
		
		$('#txt_job_no').val(order_no);
		$('#hide_job_id').val(order_id);	 
	}
}

function generate_report(company_id,program_id)
{
	 print_report( company_id+'*'+program_id, "print", "requires/knitting_program_report_controller" ) ;
}

function selected_row(rowNo)
{
	var isChecked=$('#tbl_'+rowNo).is(":checked");
	var job_no=$('#job_no_'+rowNo).val();
	var source_no=$('#source_id_'+rowNo).val();
	var party_no=$('#party_id_'+rowNo).val();
	
	if(isChecked==true)
	{
		var tot_row=$('#tbl_list_search tbody tr').length;
		for(var i=1; i<=tot_row; i++)
		{ 
			if(i!=rowNo)
			{
				try 
				{
					if ($('#tbl_'+i).is(":checked"))
					{
						var job_noCurrent=$('#job_no_'+i).val();
						if((job_no!=job_noCurrent))
						{
							alert("Please Select Same Job No.");
							$('#tbl_'+rowNo).attr('checked',false);
							return;
						}
						// for checking same source
						var source_noCurrent=$('#source_id_'+i).val();
						if((source_no!=source_noCurrent))
						{
							alert("Please Select Same Source.");
							$('#tbl_'+rowNo).attr('checked',false);
							return;
						}
						// for party same
						var party_noCurrent=$('#party_id_'+i).val();
						if((party_no!=party_noCurrent))
						{
							alert("Please Select Same Party.");
							$('#tbl_'+rowNo).attr('checked',false);
							return;
						}
					}
				}
				catch(e) 
				{
					//got error no operation
				}
			}
		}
	}
}
/*function selected_forAll(forAll){
	//alert(forAll);
	if(forAll==1)
}
*/
function generate_requisition_report()
{ 
	var program_ids = ""; var total_tr=$('#tbl_list_search tbody tr').length;
	for(i=1; i<total_tr; i++)
	{
		try 
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				program_id = $('#promram_id_'+i).val();
				if(program_ids=="") program_ids= program_id; else program_ids +=','+program_id;
			}
		}
		catch(e) 
		{
			//got error no operation
		}
	}
	if(program_ids=="")
	{
		alert("Please Select At Least One Program");
		return;
	}
	print_report(program_ids, "requisition_print", "requires/knitting_program_report_controller" ) ;
}

function generate_requisition_report_two()
{ 
	var program_ids = ""; var total_tr=$('#tbl_list_search tbody tr').length;
    var typeForAttention = $("#typeForAttention").val();
	for(i=1; i<total_tr; i++)
	{
		try 
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				program_id = $('#promram_id_'+i).val();
				if(program_ids=="") program_ids= program_id; else program_ids +=','+program_id;
			}
		}
		catch(e) 
		{
			//got error no operation
		}
	}
	
	if(program_ids=="")
	{
		alert("Please Select At Least One Program");
		return;
	}
	print_report(program_ids + "**" + typeForAttention, "requisition_print_two", "requires/knitting_program_report_controller" ) ;
}

function generate_requisition_report_three()
{ 
	var program_ids = ""; var total_tr=$('#tbl_list_search tbody tr').length;
    var typeForAttention = $("#typeForAttention").val();
	for(i=1; i<total_tr; i++)
	{
		try 
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				program_id = $('#promram_id_'+i).val();
				if(program_ids=="") program_ids= program_id; else program_ids +=','+program_id;
			}
		}
		catch(e) 
		{
			//got error no operation
		}
	}
	
	if(program_ids=="")
	{
		alert("Please Select At Least One Program"); 
		return;
	}
	print_report(program_ids + "**" + typeForAttention, "requisition_print_three", "requires/knitting_program_report_controller" ) ;
}

function generate_requisition_report_four()
{ 
	var program_ids = ""; var total_tr=$('#tbl_list_search tbody tr').length;
    var typeForAttention = $("#typeForAttention").val();
	for(i=1; i<total_tr; i++)
	{
		try 
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				program_id = $('#promram_id_'+i).val();
				if(program_ids=="") program_ids= program_id; else program_ids +=','+program_id;
			}
		}
		catch(e) 
		{
			//got error no operation
		}
	}
	
	if(program_ids=="")
	{
		alert("Please Select At Least One Program"); 
		return;
	}
	print_report(program_ids + "**" + typeForAttention, "requisition_print_four", "requires/knitting_program_report_controller" ) ;
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	
	//$("#tbl_list_search").find('input([name="check"])').hide();	
	$('input[type="checkbox"]').hide();
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" type="text/css" href="../../css/style_common.css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 
	$('input[type="checkbox"]').show();
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="330px";
}

function fn_open_machine()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	
	var companyID = $("#cbo_company_name").val();
	var page_link='requires/knitting_program_report_controller.php?action=machine_no_search_popup&companyID='+companyID;
	var title='Machine No Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=260px,height=300px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var machine_no=this.contentDoc.getElementById("hide_machine").value.split("_");
		$('#txt_machine_no').val(machine_no[1]);
	}
}

function generate_report(company_id,program_id)
{ 
	var path='../../';
	var page = 'planning_info_entry';
	print_report( company_id+'*'+program_id+'*'+path+'*'+page+'*'+2, "print", "../requires/yarn_requisition_entry_controller" ) 
}

function generate_report2(company_id,program_id)
{
	var path='../../';  
	 print_report( company_id+'*'+program_id+'*'+path, "print", "../requires/yarn_requisition_entry_controller" ) 
}


function generate_knitting_card(type)
{ 
	var program_ids = ""; var total_tr=$('#tbl_list_search tbody tr').length;
    //var typeForAttention = $("#typeForAttention").val();
	for(i=1; i<total_tr; i++)
	{
		try 
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				program_id = $('#promram_id_'+i).val();
				if(program_ids=="") program_ids= program_id; else program_ids +=','+program_id;
			}
		}
		catch(e) 
		{
			//got error no operation
		}
	}

	if(program_ids=="")
	{
		alert("Please Select At Least One Program");
		return;
	}

	if(type==1)
	{
		print_report(program_ids, "knitting_card_print", "requires/knitting_program_report_controller" ) ;
	}
	else if(type==2)
	{
		print_report(program_ids, "knitting_card_print_2", "requires/knitting_program_report_controller" ) ;
	}else if(type==3)
	{
		print_report(program_ids, "knitting_card_print_3", "requires/knitting_program_report_controller" ) ;
	}
	else if(type==4)
	{
		print_report(program_ids, "knitting_card_print_4", "requires/knitting_program_report_controller" ) ;
	}
	else if(type==5)
	{
		print_report(program_ids, "knitting_card_print_5", "requires/knitting_program_report_controller" ) ;	
	}else if(type==6)
	{
		print_report(program_ids, "knitting_card_print_6", "requires/knitting_program_report_controller" ) ;
	}
	
	
}

function openmypage_popup(program_id,action)
{
	var companyID = $("#cbo_company_name").val();
	var page_link='requires/knitting_program_report_controller.php?action='+action+'&companyID='+companyID+'&program_id='+program_id;
	var title='';
	if(action == 'knitting_popup' || action == 'grey_receive_popup')
	{
		title='Knitting Popup';
		popup_width = '1050px';
	}
	else if(action == 'grey_purchase_delivery')
	{
		title='Delivery Popup';
		popup_width = '760px';
	}
	else if(action == 'po_details_action')
	{
		title='PO Popup';
		popup_width = '350px';
	}
	else if(action == 'program_qnty_popup_action')
	{
		title='Program Qnty Popup';
		popup_width = '350px';
	}
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=390px,center=1,resize=1,scrolling=0','../');

}
</script>
</head>
<body onLoad="set_hotkey();">
<form id="knittingStatusReport_1">
    <div style="width:100%;" align="center">    
		<? echo load_freeze_divs ("../../",'');  ?>
		<h3 style="width:2100px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
		<div id="content_search_panel">      
		<fieldset style="width:2100px;">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
				<thead>
                    <th class="must_entry_caption">LC Company</th>
                    <th>Type</th>
                    <th>W. Company/ Party</th>
                    <th>Buyer Name</th>
                    <th>W. Location</th>
                    <th>Floor</th>
                    <th>Machine Dia</th>
                    <th>Job Year</th>
                    <th>Job No</th>
                    <th>File No</th>
                    <th>Internal Ref.</th>
                    <th>Order No</th>
                    <th>Booking Type</th>
                    <th>Booking No</th>
                    <th>Program No</th>
                    <th>Status</th>
                    <th>Based On</th>
                    <th>Date</th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
							<?php
                                echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select--", $selected, "load_drop_down( 'requires/knitting_program_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/knitting_program_report_controller', this.value, 'load_drop_down_location', 'location_td' );func_onchange_company();" );
                            ?>
                        </td>
                        <td>
							<?php
                                $search_by_arr=array(0=>"All",1=>"Inside",3=>"Outside");
                                echo create_drop_down( "cbo_type", 120, $search_by_arr,"",0, "", "1","load_drop_down( 'requires/knitting_program_report_controller',this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_party_type', 'party_type_td' );",0 );
                            ?>
                        </td> 
                        <td id="party_type_td">
                        	<?php
								echo create_drop_down( "cbo_party_type", 120, $blank_array,"",1, "--Select--", '',"load_drop_down( 'requires/knitting_program_report_controller', this.value, 'load_drop_down_location', 'location_td' ); func_onchange_party();" );
							?>
                        </td>
                        <td id="buyer_td">
                            <?php 
                                echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "- All Buyer -", $selected, "",0,"" );
                            ?>
                        </td>
                        <td id="location_td">
                        	<?php
								echo create_drop_down( "cbo_location_id", 120, $blank_array,"",1, "--Select--", "",'','' );
							?>
                        </td> 
                        <td id="floor_td">
                        	<?php
								echo create_drop_down( "cbo_floor_id", 120, $blank_array,"",1, "--Select--", "",'','' );
							?>
                        </td>
                        <td>
                            <input name="txt_machine_dia" id="txt_machine_dia" class="text_boxes" style="width:100px">
                        </td>
                        <td>
                        	<?php
								echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>
                        </td>
                        <td>
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px" placeholder=" Write" autocomplete="off">
                        </td>
                        <td>
                            <input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:100px" placeholder=" Write" autocomplete="off">
                        </td>
                        <td>
                            <input type="text" name="txt_internal" id="txt_internal" class="text_boxes" style="width:100px" placeholder=" Write" autocomplete="off">
                        </td>
                        <td>
                            <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:100px" placeholder="Browse Or Write" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off">
                            <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                        </td>
                        <td>
                            <?php
								$bookingTypeArr = array('0_0'=>'All', '1_2'=>'Main', '1_1'=>'Short', '4_2'=>'Sample with Order', 5=>'Sample without Order'); 
                                echo create_drop_down( "cbo_booking_type", 120, $bookingTypeArr,"", 0, "", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                        	<input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes_numeric" style="width:100px" onDblClick="openmypage_booking();" placeholder="Browse Or Write"  />
                        	<input type="hidden" id="txt_booking_id" name="txt_booking_id"/>
                        </td>
                        <td>
                            <input name="txt_program_no" id="txt_program_no" class="text_boxes_numeric" style="width:100px">
                        </td>
                         <td align="center">
                            <?php
                                echo create_drop_down( "cbo_knitting_status", 120, $knitting_program_status,"", 0, "- Select -", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                        	<?php
								$based_on_arr=array(1=>"Plan Date",2=>"Program Date",3=>"Shipment Date");
								echo create_drop_down( "cbo_based_on", 120, $based_on_arr, "", 0, "", 2,"",0 );
							?>
                        </td> 
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px" placeholder="From Date"/>
                            &nbsp;
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px" placeholder="To Date"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="18" align="center"><?php echo load_month_buttons(1); ?><input type="button" id="show_button2" class="formbutton" style="width:60px;" value="Show" onClick="fn_report_generated(1)" />
                             <input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knittingStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:60px" /></td>
                        <!--<td>
                        <input type="button" id="show_button1" class="formbutton" style="width:100px;" value="Job/Order Status" onClick="fn_report_generated(2)" />
                        <input type="button" id="show_button3" class="formbutton" style="width:70px;" value="Summary" onClick="fn_report_generated(3)" />
                         <input type="button" id="show_button4" class="formbutton" style="width:60px;" value="Short" onClick="fn_report_generated(4)" />
                         <input type="button" id="show_button111" class="formbutton" style="width:65px;" value="Color Wise" onClick="fn_report_generated(111)" />
                        </td>
                        <td align="center">
                        </td>-->
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div style="width: 1500px;" id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>   
</body>
<script>
	set_multiselect('cbo_knitting_status','0','0','','');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>