<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	Knitting Production QC Report.
Functionality	:	
JS Functions	:
Created by		:	Kaiyum
Creation date 	: 	04-11-2017
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
echo load_html_head_contents("Knitting Production QC Report","../../../", 1, 1, $unicode,0,0);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	
	var tableFilters = 
	 {
		//col_30: "none",
		col_operation: {
		id: ["td_total_qc_pass_qty","td_total_taka","td_total_hole_defect","td_total_loop_defect","td_total_press_defect_count","td_total_lycraout_defect_count","td_total_lycradrop_defect_count","td_total_dust_defect_count","td_total_oilspot_defect_count","td_total_flyconta_defect_count","td_total_slub_defect_count","td_total_patta_defect_count","td_total_neddle_defect_count","td_total_sinker_defect_count","td_total_wheel_defect_count","td_total_count_defect_count","td_total_yarn_defect_count","td_total_neps_defect_count","td_total_black_defect_count","td_total_oilink_defect_count","td_total_setup_defect_count","td_total_pin_hole_defect","td_total_slub_hole_defect","td_total_needle_mark_defect","td_total_cont_mark_defect","td_total_thin_mark_defect","td_total_miss_yarn_qty","td_total_totalDefect_point","td_total_reject_qty"],
	   //col: [24,26,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,51],
	   //col: [27,29,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,54],
	   //col: [32,34,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,66],
	   col: [32,35,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,67],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } 
	 var tableFilters3 = 
	 {
		//col_30: "none",
		col_operation: {
		id: ["td_total_qc_pass_qty","td_total_hole_defect","td_total_loop_defect","td_total_missyarn_defect","td_total_lycraout_defect_count","td_total_lycradrop_defect_count","td_total_oilspot_defect_count","td_total_flyconta_defect_count","td_total_slub_defect_count","td_total_neddle_defect_count","td_total_wheel_defect_count","td_total_count_defect_count","td_total_yarn_defect_count","td_total_setup_defect_count","td_total_pin_hole_defect_count","td_total_totalDefect_point","td_total_reject_qty"],
	   //col: [24,26,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,51],
	   //col: [27,29,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,54],
	   col: [20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,40],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } 
	var tableFilters4 = 
	{
		//col_30: "none",
		col_operation: {
		id: ["td_total_qc_pass_qty","td_total_taka","td_total_hole_defect","td_total_loop_defect","td_total_press_defect_count","td_total_lycraout_defect_count","td_total_lycradrop_defect_count","td_total_dust_defect_count","td_total_oilspot_defect_count","td_total_flyconta_defect_count","td_total_slub_defect_count","td_total_patta_defect_count","td_total_neddle_defect_count","td_total_sinker_defect_count","td_total_wheel_defect_count","td_total_count_defect_count","td_total_yarn_defect_count","td_total_neps_defect_count","td_total_black_defect_count","td_total_oilink_defect_count","td_total_setup_defect_count","td_total_pin_hole_defect","td_total_slub_hole_defect","td_total_needle_mark_defect","td_total_totalDefect_point","td_total_reject_qty"],
	   	col: [33,35,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,64],
	   	operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   	write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	 
	function chng_val(vall)
	{
		if(vall=1001)
		{
			if(form_validation('txt_date_to','Date From')==false)
				{
					if(form_validation('txt_date_from','Date From')==false)
					{
						return;
					}
				}
				
		}
		if(vall=1002)
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false)
				{
					return;
				}
		}
	}
	function fn_report_generated(type)
	{
		var company=$('#cbo_company_name').val();
		var del_company=$('#cbo_knitting_company').val();
		var txt_date_from=$('#txt_date_from').val();
		var txt_date_to=$('#txt_date_to').val();
		var txt_date_from_qc=$('#txt_date_from_qc').val();
		var txt_date_to_qc=$('#txt_date_to_qc').val();
		var txt_job_no=$('#txt_job_no').val();
		var txt_order_no=$('#txt_order_no').val(); 
		var txt_booking_no=$('#txt_booking_no').val();
		var txt_barcode_no=$('#txt_barcode_no').val();
		var txt_program_no=$('#txt_program_no').val();
		
		if(txt_job_no!="" || txt_order_no!="" || txt_booking_no!="" || txt_barcode_no!="" || txt_program_no!="")
		{
			if (form_validation('cbo_company_name','Comapny Name')==false)
			{
				release_freezing();
				return;
			}
		}
		else
		{
			if(type==2)
			{
				if(form_validation('txt_date_from*txt_date_to','Date From*Date To')==false)
				{
					release_freezing();
					return;
				}
			}
			else if(type==4) // Show 3
			{
				if(txt_date_from_qc!="" || txt_date_to_qc!="")
				{
					if (form_validation('cbo_company_name','Comapny Name')==false)
					{
						release_freezing();
						return;
					}
				}
				else
				{
					if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*Date From*Date To')==false)
					{
						release_freezing();
						return;
					} 
				}					
			}
			else
			{
				var dateRangeType=$('#cbo_date_range_type').val();
				if(dateRangeType==1)
				{
					if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*Date From*Date To')==false)
					{
						release_freezing();
						return;
					}
				}
				else
				{
					if(form_validation('cbo_company_name*txt_date_from_qc*txt_date_to_qc','Comapny Name*QC Date From*QC Date To')==false)
					{
						release_freezing();
						return;
					}
				}
				
			}
		}
	
		var data="action=report_generate&&report_format="+type+get_submitted_data_string('cbo_company_name*cbo_knitting_company*cbo_location_name*cbo_buyer_name*cbo_year*txt_job_no*txt_booking_no*hide_order_id*cbo_knitting_source*cbo_del_floor*txt_date_from*txt_date_to*txt_date_from_qc*txt_date_to_qc*txt_barcode_no*txt_program_no*cbo_roll_status*cbo_date_range_type',"../../../");
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/daily_roll_wise_knitting_qc_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
		
	}
		
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			release_freezing();
			var reponse=trim(http.responseText).split("####");
			//alert(reponse);
			$('#report_container2').html(reponse[0]);
			setFilterGrid("table_body",-1,tableFilters);
			setFilterGrid("table_body_show2",-1,tableFilters3);
			setFilterGrid("table_body_show4",-1,tableFilters4);
			/*if(reponse[2]==1)
			{
				setFilterGrid("table_body",-1,tableFilters);
				setFilterGrid("table_body2",-1,tableFilters2);
			}*/
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
		}
	}

	
	function new_window()
	{
		/*document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";*/
		
		$('#table_body tr:first').hide();
		$('#table_body_show4 tr:first').hide();
		//$('#table_body2 tr:first').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		$('#table_body tr:first').show();
		$('#table_body_show4 tr:first').show();
		//$('#table_body2 tr:first').show();
		
		
		/*document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";*/
	}
	
	function generate_report_excel_only(type)
	{
	    var company=$('#cbo_company_name').val();
		var del_company=$('#cbo_knitting_company').val();
		var txt_date_from=$('#txt_date_from').val();
		var txt_date_to=$('#txt_date_to').val();
		var txt_date_from_qc=$('#txt_date_from_qc').val();
		var txt_date_to_qc=$('#txt_date_to_qc').val();
		var txt_job_no=$('#txt_job_no').val();
		var txt_order_no=$('#txt_order_no').val(); 
		var txt_booking_no=$('#txt_booking_no').val();
		var txt_barcode_no=$('#txt_barcode_no').val();
		var txt_program_no=$('#txt_program_no').val();
		//alert(txt_job_no+'='+txt_order_no+'='+txt_booking_no+'='+txt_barcode_no+'='+txt_program_no);
		if(txt_job_no!="" || txt_order_no!="" || txt_booking_no!="" || txt_barcode_no!="" || txt_program_no!="")
		{
			if (form_validation('cbo_company_name','Comapny Name')==false)
			{
				release_freezing();
				return;
			}
		}
		else
		{
			if(txt_date_from_qc!="" || txt_date_to_qc!="")
			{
				if (form_validation('cbo_company_name','Comapny Name')==false)
				{
					release_freezing();
					return;
				}
			}
			else
			{
				if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*Date From*Date To')==false)
				{
					release_freezing();
					return;
				} 
			}
		}
	
		var data="action=report_generate_excel_only&&report_format="+type+get_submitted_data_string('cbo_company_name*cbo_knitting_company*cbo_location_name*cbo_buyer_name*cbo_year*txt_job_no*txt_booking_no*hide_order_id*cbo_knitting_source*cbo_del_floor*txt_date_from*txt_date_to*txt_date_from_qc*txt_date_to_qc*txt_barcode_no*txt_program_no*cbo_roll_status*cbo_date_range_type',"../../../");
		//alert(data);return;
	 	freeze_window(2);
	    http.open("POST","requires/daily_roll_wise_knitting_qc_report_controller.php",true);
	    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	    http.send(data);
	    http.onreadystatechange = generate_report_reponse_exel_only;
	}

	function generate_report_reponse_exel_only()
	{
	    if(http.readyState == 4) 
	    {  
	      var reponse=trim(http.responseText).split("####");

	      if(reponse!='')
	      {
	        $('#aa1').removeAttr('href').attr('href','requires/'+reponse[0]);
	        document.getElementById('aa1').click();
	      }
	      show_msg('3');
	      release_freezing();
	    }
	}
	
	function openmypage_order()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var page_link='requires/daily_roll_wise_knitting_qc_report_controller.php?action=order_no_search_popup&companyID='+companyID;
		var title='Order No Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;
			
			$('#txt_order_no').val(order_no);
			$('#hide_order_id').val(order_id);	 
		}
	}
	
	function openmypage_job_info()
	{
		var company_id=document.getElementById('cbo_company_name').value;
		var buyer_id=document.getElementById('cbo_buyer_name').value;
		var cbo_year=document.getElementById('cbo_year').value;
		
		page_link='requires/daily_roll_wise_knitting_qc_report_controller.php?action=job_popup'+'&company_id='+company_id+'&buyer_name='+buyer_id+'&cbo_year='+cbo_year;
		
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Job Info", 'width=590px,height=370px,center=1,resize=0,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var job_no=this.contentDoc.getElementById("hide_job_no").value;//alert(item_description_all); 
				job_no=job_no.split("_");
				document.getElementById('cbo_year').value=job_no[0];
				document.getElementById('txt_job_no').value=job_no[1];
			}
		}
	}
	
	function openmypage_booking_info()
	{
		var company_id=document.getElementById('cbo_company_name').value;
		var buyer_id=document.getElementById('cbo_buyer_name').value;
		var cbo_year=document.getElementById('cbo_year').value;
		
		page_link='requires/daily_roll_wise_knitting_qc_report_controller.php?action=booking_popup'+'&company_id='+company_id+'&buyer_name='+buyer_id+'&cbo_year='+cbo_year;
		
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Job Info", 'width=1000px,height=370px,center=1,resize=0,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var booking_no=this.contentDoc.getElementById("hide_booking_no").value;//alert(item_description_all); 
				//document.getElementById('cbo_year').value=job_no[0];
				document.getElementById('txt_booking_no').value=booking_no;
			}
		}
	}	
	function load_location()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var cbo_knitting_source = $('#cbo_knitting_source').val();
		var cbo_knitting_company = $('#cbo_knitting_company').val();
		if(cbo_knitting_source==1)
		{
			load_drop_down( 'requires/daily_roll_wise_knitting_qc_report_controller',cbo_knitting_company, 'load_drop_down_location', 'location' );
		}
		else
		{
			load_drop_down( 'requires/daily_roll_wise_knitting_qc_report_controller',cbo_company_id, 'load_drop_down_location', 'location' );
		}
	}
	function load_floor()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var cbo_knitting_source = $('#cbo_knitting_source').val();
		var cbo_knitting_company = $('#cbo_knitting_company').val();
		var cbo_location_name = $('#cbo_location_name').val();
		if(cbo_knitting_source==1)
		{
			load_drop_down( 'requires/daily_roll_wise_knitting_qc_report_controller',cbo_knitting_company+'_'+cbo_location_name, 'load_drop_down_floor', 'cbo_del_floor' );
		}
		else
		{
			load_drop_down( 'requires/daily_roll_wise_knitting_qc_report_controller',cbo_company_id+'_'+cbo_location_name, 'load_drop_down_floor', 'cbo_del_floor' );
		}
	}	
</script>

</head>
 
<body onLoad="set_hotkey();">
 <div style="width:100%;" align="center">
<form id="monthly_ex_factory" name="monthly_ex_factory">
    <div style="width:2240px;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:2270px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:2270px;">
                <table class="rpt_table" width="2240" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                    	<tr>                   
                            <th width="120">Company Name</th>
                            <th width="100">Knitting Source</th>
                            <th width="120">Working Company</th>
                            <th width="120">Location</th>
                            <th width="120">Buyer</th>
                            <th width="60">Year</th>
                            <th width="80">Job No</th>
                            <th width="100">Order No</th>
                            <th width="100">Sample Booking No</th>
                            <th width="100">Barcode No</th>
                            <th width="80">Program No</th>
                            <th width="100">Floor</th>
                            <th width="80">QC Status</th>
                            <th width="100">Date Range Type</th>
                            <th width="170" class="must_entry_caption">Production Date</th>
                            <th width="170" >QC Date</th>
                            <th width="160" colspan="2">
                            <input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:70px" onClick="reset_form('','report_container*report_container2','','','')" />
                            </th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td align="center"> 
							<?
                            echo create_drop_down("cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "Select Company", $selected, "load_drop_down( 'requires/daily_roll_wise_knitting_qc_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );");
                            ?>
                         </td>
                          <td>
                            <? 
                                echo create_drop_down( "cbo_knitting_source", 120, $knitting_source,"", 1, "Knitting Source", $selected, "load_drop_down( 'requires/daily_roll_wise_knitting_qc_report_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_knitting_com','knitting_com');",0,"1,3" );
                            ?>
                            
                            
                            	<!--echo create_drop_down("cbo_knitting_source",150,$knitting_source,"", 1, "-- Select --", 0,"store_load(1);load_drop_down( 'requires/grey_production_entry_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','knitting_com');service_booking_check();",0,'1,3');-->
                        </td>
                        <td id="knitting_com">
                            <?
                               // echo create_drop_down( "cbo_knitting_company", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "Select Delivery Company", $selected,"load_drop_down( 'requires/daily_roll_wise_knitting_qc_report_controller', this.value, 'load_drop_down_location', 'location' );chng_val(1001);" );
							   echo create_drop_down( "cbo_knitting_company", 120, $blank_array,"", 1, "Select Delivery Company", $selected,"" );
                            ?>
                        </td>
                        <td id="location">
							 <? 
	                            echo create_drop_down( "cbo_location_name", 120, $blank_array,"", 1, "-- Select --", $selected, "" );
	                         ?>	  
	                    </td>
                       <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                        	<?
								echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>
                        </td>
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Write/Browse" onDblClick="openmypage_job_info()"/>
                        </td>
                       
                        <td>
                                <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:120px" placeholder="Write/Browse" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off">
                                <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                        </td>
                         <td>
                        	<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:90px" placeholder="Write/Browse" onDblClick="openmypage_booking_info()"/>
                        </td>
                        <td>
                                <input type="text" name="txt_barcode_no" id="txt_barcode_no" class="text_boxes" style="width:90px" placeholder="Write" o  autocomplete="off">
                        </td>
                        <td>
                                <input type="text" name="txt_program_no" id="txt_program_no" class="text_boxes" style="width:70px" placeholder="Write" o  autocomplete="off">
                        </td>
                       
                        <td id="del_floor_td">
                        <? echo create_drop_down( "cbo_del_floor", 120, $blank_array,"", 1, "-- Select Delivery Floor --", $selected, "" );?>
                      </td>
                  	<td align="center">
                    	<? $roll_status = array(0 => 'All', 1 => 'QC Pass', 2 => 'Held Up', 3 => 'Reject');
						echo create_drop_down("cbo_roll_status", 80, $roll_status, "", 0, "-- Select --", 0, "", ''); ?>
					</td>
					<td align="center">
                    	<? $date_range_selection = array(0 => '-- Select --', 1 => 'Production Date', 2 => 'QC Date');
						echo create_drop_down("cbo_date_range_type", 100, $date_range_selection, "", 0, "-- Select --", 1, "", ''); ?>
					</td>
                      <td>
                      <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" >&nbsp; To
                      <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" ></td>

                      <td><input type="text" name="txt_date_from_qc" id="txt_date_from_qc" class="datepicker" style="width:60px" placeholder="From Date" >&nbsp; To
                      <input type="text" name="txt_date_to_qc" id="txt_date_to_qc" class="datepicker" style="width:60px"  placeholder="To Date" ></td>

                      <td>
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1);" />
                       	<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show-2" onClick="fn_report_generated(3);" />
                       	<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show-3" onClick="fn_report_generated(4);" />
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="Format-2" onClick="fn_report_generated(2);" />
                        <input type="button" id="search" class="formbutton" style="width:70px" value="Excel Only" onClick="generate_report_excel_only(3)" /><a href="" id="aa1"></a>
                      </td>
                    </tr>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td>
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </table> 
            </fieldset>
        </div>
    </div>
     </form>

    <!--<div id="report_container" align="center"></div>-->
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    <div style="display:none" id="data_panel"></div>  
 </div>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>