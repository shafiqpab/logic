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
		id: ["total_batch_qty","total_qc_pass_qty","total_hole_defect","total_dye_defect_count","total_insect_defect_count","total_yellowSpot_defect_count","total_poly_defect_count","total_dust_defect_count","total_oilspot_defect_count","total_flyconta_defect_count","total_slub_defect_count","total_patta_defect_count","total_cut_defect_count","total_sinker_defect_count","total_print_mis_defect_count","total_yarn_conta_defect_count","total_slub_hole_defect_count","total_softener_Spot_defect_count","total_dirty_stain_defect_count","total_neps_defect_count","total_needle_drop_defect_count","total_chem_defect_count","total_cotton_seeds_defect_count","total_Loop_hole_defect_count","total_dead_cotton_defect_count","total_thick_thin_defect_count","total_rust_spot_defect_count","total_needle_broken_mark_defect_count","total_dirty_spot_defect_count","total_side_center_shade_defect_count","total_bowing_defect_count","total_uneven_defect_count","total_yellow_writing_defect_count","total_fabric_missing_defect_count","total_dia_mark_defect_count","total_miss_print_defect_count","total_hairy_defect_count","total_gsm_hole_defect_count","total_compacting_mark_defect_count","total_rib_body_shade_defect_count","total_running_shade_defect_count","total_plastic_conta_defect_count","total_crease_mark_defect_count","total_patches_defect_count","total_mc_toppage_defect_count","total_needle_line_defect_count","total_crample_mark_defect_count","total_shite_specks_defect_count","total_mellange_effect_defect_count","total_line_mark_defect_count","total_loop_out_defect_count","total_needle_broken_defect_count","total_loop_defect_count","total_oil_spot_line_defect_count","total_lycra_out_drop_defect_count","total_miss_yarn_defect_count","total_color_contra_defect_count","total_friction_mark_defect_count","total_pin_out_defect_count","total_rust_stain_defect_count","total_stop_mark_defect_count","total_compacting_broken_defect_count","total_grease_spot_defect_count","total_cut_hole_defect_count","total_snagging_pull_out_defect_count","total_press_off_defect_count","total_wheel_free_defect_count","total_count_mix_defect_count","total_black_spot_defect_count","total_set_up_defect_count","total_pin_ole_defect_count","total_totalDefect_point","total_reject_qty"],
	   //col: [24,26,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,51],
	   //col: [27,29,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,54],
	   col: [29,30,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]

	
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
		//var txt_date_from_qc=$('#txt_date_from_qc').val();
		//var txt_date_to_qc=$('#txt_date_to_qc').val();
		var txt_job_no=$('#txt_job_no').val();
		var txt_order_no=$('#txt_order_no').val(); 
		var txt_booking_no=$('#txt_booking_no').val();
		var txt_barcode_no=$('#txt_barcode_no').val();
		var txt_batch_no=$('#txt_batch_no').val();
		var cbo_date_range_type=$('#cbo_date_range_type').val();
		
		if(txt_job_no!="" || txt_order_no!="" || txt_booking_no!="" || txt_barcode_no!="" || txt_batch_no!="")
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
	
		var data="action=report_generate&&report_format="+type+get_submitted_data_string('cbo_company_name*cbo_knitting_company*cbo_location_name*cbo_buyer_name*cbo_year*txt_job_no*txt_booking_no*hide_order_id*cbo_knitting_source*cbo_del_floor*txt_date_from*txt_date_to*txt_barcode_no*txt_batch_no*cbo_roll_status*cbo_with_defect*cbo_date_range_type',"../../../");
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/daily_roll_wise_finishing_qc_report_controller.php",true);
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
			
			/*if(reponse[2]==1)
			{
				setFilterGrid("table_body",-1,tableFilters);
				setFilterGrid("table_body2",-1,tableFilters2);
			}*/
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1,tableFilters);
			setFilterGrid("table_body_show2",-1,tableFilters3);
			setFilterGrid("table_body_show4",-1,tableFilters4);
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
	

	
	function openmypage_order()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var page_link='requires/daily_roll_wise_finishing_qc_report_controller.php?action=order_no_search_popup&companyID='+companyID;
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
		
		page_link='requires/daily_roll_wise_finishing_qc_report_controller.php?action=job_popup'+'&company_id='+company_id+'&buyer_name='+buyer_id+'&cbo_year='+cbo_year;
		
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
		
		page_link='requires/daily_roll_wise_finishing_qc_report_controller.php?action=booking_popup'+'&company_id='+company_id+'&buyer_name='+buyer_id+'&cbo_year='+cbo_year;
		
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
			load_drop_down( 'requires/daily_roll_wise_finishing_qc_report_controller',cbo_knitting_company, 'load_drop_down_location', 'location' );
		}
		else
		{
			load_drop_down( 'requires/daily_roll_wise_finishing_qc_report_controller',cbo_company_id, 'load_drop_down_location', 'location' );
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
			load_drop_down( 'requires/daily_roll_wise_finishing_qc_report_controller',cbo_knitting_company+'_'+cbo_location_name, 'load_drop_down_floor', 'cbo_del_floor' );
		}
		else
		{
			load_drop_down( 'requires/daily_roll_wise_finishing_qc_report_controller',cbo_company_id+'_'+cbo_location_name, 'load_drop_down_floor', 'cbo_del_floor' );
		}
	}
	function fnc_date_range(selected_type)
	{
		if(selected_type==2){$("#dateID").text("QC Date");}
		else{$("#dateID").text("Production Date");}
	}	
</script>

</head>
 
<body onLoad="set_hotkey();">
 <div style="width:100%;" align="center">
<form id="monthly_ex_factory" name="monthly_ex_factory">
    <div style="width:1940px;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1840px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1970px;">
                <table class="rpt_table" width="1940" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                    	<tr>                   
                            <th width="120">Company Name</th>
                            <th width="100">Source</th>
                            <th width="120">Company</th>
                            <th width="120">Location</th>
                            <th width="120">Buyer</th>
                            <th width="60">Year</th>
                            <th width="80">Job No</th>
                            <th width="100">Order No</th>
                            <th width="100">Sample Booking No</th>
                            <th width="100">Barcode No</th>
                            <th width="80">Batch No</th>
                            <th width="100">Floor</th>
                            <th width="80">QC Status</th>
                            <th width="50">With Defect</th>
                            <th width="100">Date Range Type</th>
                            <th width="170" class="must_entry_caption" id="dateID">Production Date</th>
                            <th width="160" colspan="2">
                            <input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:70px" onClick="reset_form('','report_container*report_container2','','','')" />
                            </th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td align="center"> 
							<?
                            echo create_drop_down("cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "Select Company", $selected, "load_drop_down( 'requires/daily_roll_wise_finishing_qc_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );");
                            ?>
                         </td>
                          <td>
                            <? 
                                echo create_drop_down( "cbo_knitting_source", 120, $knitting_source,"", 1, "-- Source --", $selected, "load_drop_down( 'requires/daily_roll_wise_finishing_qc_report_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_knitting_com','knitting_com');",0,"1,3" );
                            ?>
                            
                            
                            	<!--echo create_drop_down("cbo_knitting_source",150,$knitting_source,"", 1, "-- Select --", 0,"store_load(1);load_drop_down( 'requires/grey_production_entry_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','knitting_com');service_booking_check();",0,'1,3');-->
                        </td>
                        <td id="knitting_com">
                            <?
                               // echo create_drop_down( "cbo_knitting_company", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "Select Delivery Company", $selected,"load_drop_down( 'requires/daily_roll_wise_finishing_qc_report_controller', this.value, 'load_drop_down_location', 'location' );chng_val(1001);" );
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
                                <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:70px" placeholder="Write" o  autocomplete="off">
                        </td>
                       
	                    <td id="del_floor_td">
	                        <? echo create_drop_down( "cbo_del_floor", 120, $blank_array,"", 1, "-- Select Delivery Floor --", $selected, "" );?>
	                    </td>
	                    <td align="center">
	                    	<? $roll_status = array(0 => 'All', 1 => 'QC Pass', 2 => 'Held Up', 3 => 'Reject');
							echo create_drop_down("cbo_roll_status", 80, $roll_status, "", 0, "-- Select --", 0, "", ''); ?>
						</td>
						<td align="center">
	                    	<? $with_defect = array(1 => 'Yes', 2 => 'No');
							echo create_drop_down("cbo_with_defect", 50, $with_defect, "", 0, "-- Select --", 1, "", ''); ?>
						</td>
						<td align="center">
	                    	<? $date_range_arr = array(1 => 'Production Date', 2 => 'QC Date');
							echo create_drop_down("cbo_date_range_type", 100, $date_range_arr, "", 0, "-- Select --", 1, "fnc_date_range(this.value);", ''); ?>
						</td>
                      	<td>
                      		<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" >&nbsp; To
                      		<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" >
                      	</td>

                      <!-- <td><input type="text" name="txt_date_from_qc" id="txt_date_from_qc" class="datepicker" style="width:60px" placeholder="From Date" >&nbsp; To
                      <input type="text" name="txt_date_to_qc" id="txt_date_to_qc" class="datepicker" style="width:60px"  placeholder="To Date" ></td>-->

                      <td>
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1);" />
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
