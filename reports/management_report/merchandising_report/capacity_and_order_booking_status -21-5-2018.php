<?
/*-------------------------------------------- Comments----------------
Purpose			: 	This form will create Gmts Shipment Schedule Report
Functionality	:	
JS Functions	:
Created by		:	Monzu 
Creation date 	: 	11/01/2013
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		: Need show delay reason and remarks in Order entry page to Capacity and order booking status report [ Repeat ]. Issue id=5617 update by jahid date 08-08-15
-----------------------------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------
echo load_html_head_contents("Order Info","../../../", 1, 1, $unicode,'',1);
?>	
<script>
	var permission='<? echo $permission; ?>';
	//[14,17,18,20,21,22,23,24,25,26,27,28]
	var tableFilters = 
	{
		col_operation: 
		{
			id: ["value_smv_tot","total_order_qnty_pcs","value_yarn_req_tot","value_total_order_value","value_total_commission","value_total_net_order_value","total_ex_factory_qnty","total_ex_factory_val","total_short_access_qnty","total_over_access_qnty","value_total_short_access_value","value_total_over_access_value"],
		  //col: [17,20,21,23,24,25,26,27,28,29,30,31],
			col: [20,23,24,26,27,28,29,30,31,32,33,34],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		} , 
		col_31: "select",
		col_32: "select",
		col_33: "select",
		col_36: "select",
		col_37: "select",
		display_all_text:'Show All'
	}
	var tableFilters2 = 
	{
		col_operation: 
		{
			id: ["value_smv_tot","total_order_qnty_pcs","value_yarn_req_tot","value_total_order_value","value_total_commission","value_total_net_order_value","total_ex_factory_qnty","total_ex_factory_val","total_short_access_qnty","total_over_access_qnty","value_total_short_access_value","value_total_over_access_value"],
		  //col: [17,20,21,23,24,25,26,27,28,29,30,31],
			col: [21,24,25,27,28,29,30,31,32,33,34,35],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		} , 
		col_32: "select",
		col_33: "select",
		col_34: "select",
		col_37: "select",
		col_38: "select",
		display_all_text:'Show All'
	}
	
	function generate_report(div,type,type_summary)
	{
		if( form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
		{
			return;
		}
		var txt_date_from=document.getElementById('txt_date_from').value;
		var txt_date_to=document.getElementById('txt_date_to').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var cbo_team_name=document.getElementById('cbo_team_name').value;
		var cbo_team_member=document.getElementById('cbo_team_member').value;
		var cbo_category_by=document.getElementById('cbo_category_by').value;
		var zero_value=0;
		var cbo_capacity_source=document.getElementById('cbo_capacity_source').value;
		var cbo_year=document.getElementById('cbo_year').value;
		var txt_style_ref=document.getElementById('txt_style_ref').value;
		var cbo_factory_merchant=document.getElementById('cbo_factory_merchant').value;
		var cbo_agent=document.getElementById('cbo_agent').value;
		var cbo_product_category=document.getElementById('cbo_product_category').value;
		var cbo_style_owner=document.getElementById('cbo_style_owner').value;
		var cbo_location_id=document.getElementById('cbo_location_id').value;
		
		var r=confirm("Press  OK to open  without zero value Of Order Qnty\n Press  Cancel to open  without zero value Of Order Qnty Or Allowcate Qnty");

		if(r==true)
		{
			zero_value=0
		}
		else
		{
			zero_value=1
		}
		var data=cbo_company_name+"_"+cbo_buyer_name+"_"+txt_date_from+"_"+txt_date_to+"_"+cbo_team_name+"_"+cbo_team_member+"_"+cbo_category_by+"_"+zero_value+"_"+cbo_capacity_source+"_"+cbo_year+"_"+txt_style_ref+"_"+cbo_factory_merchant+"_"+cbo_agent+"_"+cbo_product_category+"_"+cbo_style_owner+"_"+cbo_location_id;
		freeze_window(3);
		http.open("GET","requires/capacity_and_order_booking_status_controller.php?data="+data+"&type_summary="+type_summary+"&type="+type,true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=(http.responseText).split('****');	
			//alert(response[0]);
			//document.getElementById('report_container2').innerHTML=response[0];
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			append_report_checkbox('table_header_1',1);
			if(response[2]!=2)
			{
				setFilterGrid("table-body",-1,tableFilters);
			}
			else
			{
				//alert(response[2]);
				setFilterGrid("table-body",-1,tableFilters2);
			}
			release_freezing();
		}
	}
	
	function openmypage_image(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
		}
	}
	
	function order_dtls_popup(job_no,po_id,template_id,tna_process_type)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/capacity_and_order_booking_status_controller.php?action=work_progress_report_details&job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type, 'Work Progress Report Details PO Wise', 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
		}
	}
	
	function country_order_dtls_popup(job_no,po_id,template_id,tna_process_type,country_id)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/capacity_and_order_booking_status_controller.php?action=country_work_progress_report_details&job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&country_id='+country_id, 'Work Progress Report Details Country PO Wise', 'width=1150px,height=480px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
		}
	}
	
	function print_report_part_by_part(id,button_id)
	{
		$(button_id).removeAttr("onClick").attr("onClick","javascript:window.print()");
		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+document.getElementById(id).innerHTML+'</body</html>');
		d.close();
		$(button_id).removeAttr("onClick").attr("onClick","print_report_part_by_part('"+id+"','"+button_id+"')");
	}
	
	
//for report summary
	function fn_report_generated_summary(type)
	{
		
		if( form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
		{
			return;
		}
		else
		{	
		//alert($('#cbo_year_selection').val())
		if(type==1)
		{
			var data="action=generate_report_summary"+get_submitted_data_string('cbo_company_name*cbo_style_owner*cbo_buyer_name*cbo_year*cbo_team_name*cbo_team_member*cbo_category_by*cbo_capacity_source*txt_date_from*txt_date_to*cbo_product_category*cbo_location_id*txt_style_ref*cbo_agent',"../../../");
		}
		else if(type==3)
		{
			var data="action=generate_report_summary3"+get_submitted_data_string('cbo_company_name*cbo_style_owner*cbo_buyer_name*cbo_year*cbo_team_name*cbo_team_member*cbo_category_by*cbo_capacity_source*txt_date_from*txt_date_to*cbo_product_category*cbo_location_id*txt_style_ref*cbo_agent',"../../../");
		}
		else
		{
			var data="action=generate_report_summary2"+get_submitted_data_string('cbo_company_name*cbo_style_owner*cbo_buyer_name*cbo_year*cbo_team_name*cbo_team_member*cbo_category_by*cbo_capacity_source*txt_date_from*txt_date_to*cbo_product_category*cbo_location_id*txt_style_ref*cbo_agent*cbo_factory_merchant',"../../../");
		}
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/capacity_and_order_booking_status_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_summary_reponse;
		}
	}
	
	function fn_report_generated_summary_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		//document.getElementById('approval_div').style.overflow="auto";
		//document.getElementById('approval_div').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		
		//document.getElementById('approval_div').style.overflowY="scroll";
		//document.getElementById('approval_div').style.maxHeight="380px";
	}	
	
	/*function progress_comment_popup(job_no,po_id,template_id,tna_process_type)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/shipment_date_wise_wp_report_controller.php?job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&action=update_tna_progress_comment'+'&permission='+permission, "TNA Progress Comment", 'width=1030px,height=390px,center=1,resize=1,scrolling=0','../../');
	}*/
	
	function progress_comment_popup(job_no,po_id,template_id,tna_process_type)
	{
		var data="action=update_tna_progress_comment"+
								'&job_no='+"'"+job_no+"'"+
								'&po_id='+"'"+po_id+"'"+
								'&template_id='+"'"+template_id+"'"+
								'&tna_process_type='+"'"+tna_process_type+"'"+
								'&permission='+"'"+permission+"'";	
								
		http.open("POST","requires/shipment_date_wise_wp_report_controller.php",true);
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_progress_comment_reponse;	
	}
	
	function generate_progress_comment_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
			d.close();
		}
	}
	
	function openmypage_file(action,job_no,type)
	{
		var page_link='requires/capacity_and_order_booking_status_controller.php?action='+action+'&job_no='+job_no+'&type='+type
		var title="File View";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=300px,center=1,resize=0,scrolling=0','../../')
	}
	
	function set_defult_date(companyId){
		var defult_date=return_global_ajax_value(companyId, 'get_defult_date', '', 'requires/capacity_and_order_booking_status_controller');
		document.getElementById('cbo_category_by').value=trim(defult_date);
		//alert(defult_date);
		
	}
	function generate_ex_factory_popup(action,job_no,id,width)
	{
		//alert(job_no); 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/capacity_and_order_booking_status_controller.php?action='+action+'&job_no='+job_no+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
</script>
</head>
<body onLoad="set_defult_date(document.getElementById('cbo_company_name').value)">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../");  ?>
        <form name="capacityOrderBooking_1" id="capacityOrderBooking_1" autocomplete="off" > 
        <h3 style="width:1535px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div id="content_search_panel" style="width:1530px" >      
			<fieldset>
                <table align="center" class="rpt_table" width="1530" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th class="">Company</th>
                            <th class="">Location</th>
                            <th class="">Style Owner </th>
                            <th>Buyer</th>
                            <th>Agent</th>
                            <th>Style</th>
                            <th>Job Year</th>
                            <th>Team</th>
                            <th>Dealing Merchant</th>
                            <th>Factory Merchant</th>
                            <th>Product Category</th>
                            <th class="must_entry_caption" colspan="2">Date</th>
                            <th>Date Category</th>
                            <th>Capacity Source</th>
                            <th><input type="reset" name="reset" id="reset" value="Reset" style="width:50px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " load_drop_down( 'requires/capacity_and_order_booking_status_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/capacity_and_order_booking_status_controller', this.value, 'load_drop_down_agent', 'agent_td');load_drop_down( 'requires/capacity_and_order_booking_status_controller', this.value, 'load_drop_down_location', 'location_td' );set_defult_date(this.value);get_php_form_data( this.value, 'company_wise_report_button_setting','requires/capacity_and_order_booking_status_controller' );" );
                            ?> 
                        </td>
                         <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_id", 100, $blank_array,"", 1, "-- Select Location --", $selected, "" );
                            ?>	
                        </td>
                         <td>
                            <?
                                echo create_drop_down( "cbo_style_owner", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/capacity_and_order_booking_status_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/capacity_and_order_booking_status_controller', this.value, 'load_drop_down_agent', 'agent_td'); set_defult_date(this.value)" );
                            ?> 
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                            ?>	
                        </td>
                        <td id="agent_td">
                            <? 
                                echo create_drop_down( "cbo_agent", 130, $blank_array,"", 1, "-- Select Agent --", $selected, "" );
                            ?>	
                        </td>
                        <td align="center">
                            <input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px" />
                        </td>
                        <td>
                            <? echo create_drop_down( "cbo_year", 50, create_year_array(),"", 1,"-- All --",0 , "",0,"" );//date("Y",time()) ?>	
                        </td>
                        <td >                
                            <?
                                echo create_drop_down( "cbo_team_name", 80, "select id,team_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_name","id,team_name", 1, "-- Select Team --", $selected, " load_drop_down( '../merchandising_report/requires/capacity_and_order_booking_status_controller', this.value, 'load_drop_down_team_member', 'team_td' );load_drop_down( '../merchandising_report/requires/capacity_and_order_booking_status_controller', this.value, 'cbo_factory_merchant', 'div_marchant_factory' ) " );
                            ?>
                        </td>
                        <td id="team_td">
                        <div id="div_team">
                            <? 
                                echo create_drop_down( "cbo_team_member", 100, $blank_array,"", 1, "- Select Dealing Merchant- ", $selected, "" );
                            ?>	
                        </div>
                        </td>
                        
                        
                        <td id="div_marchant_factory" > 
                            <? 
                                echo create_drop_down( "cbo_factory_merchant", 100, $blank_array,"", 1, "-- Select --", $selected, "" );
                            ?>	
                        </td>
                        
                        <td> 
                            <? 
								echo create_drop_down( "cbo_product_category", 100, $product_category,"", 1, "-- Select --", $selected, ""  );
                            ?>	
                        </td>
                        
                        <td><input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:50px" placeholder="From Date"></td>
                        <td><input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:50px" placeholder="To Date"></td>
                        <td>
                        <? 
                                echo create_drop_down( "cbo_category_by", 80, $report_date_catagory,"", 0, "", $selected, "",'',"1,2,4" );
                            ?>	
                            <!--<select name="cbo_category_by" id="cbo_category_by"  style="width:100px" class="combo_boxes">
                                <option value="1">Country Ship Date Wise </option>
                                <option value="2" selected>Pub Ship Date Wise </option>
                            </select>-->
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_capacity_source",90,$knitting_source,"", 1, "--All--", $selected, "","","1,3","","","","");
                            ?>
                        </td>
                        <td>
                        <input type="button" name="search" id="search1" value="Show" onClick="generate_report('report_container2','report_generate',3)" style="width:50px; display: none;" class="formbutton" /> 
                        <input name="fillter_check" id="fillter_check" type="hidden" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="16" align="center">
                            <? echo load_month_buttons(1); ?> &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="search" id="search2" value="Location Wise" onClick="generate_report('report_container2','report_generate_location',4)" style="width:90px;" class="formbutton" />
                            &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="search" id="search2" value="Location Wise Summary" onClick="generate_report('report_container2','report_generate_location',5)" style="width:150px;" class="formbutton" />
                            <input type="button" name="summary" id="summary1" value="Summary" onClick="fn_report_generated_summary(1)" style="width:70px; display: none;" class="formbutton" />
                            <input type="button" name="summary" id="summary2" value="Summary2" onClick="fn_report_generated_summary(2)" style="width:70px; display: none;" class="formbutton" />
                            
                            
                            <input type="button" name="summary" id="summary3" value="Summary3[Mkt]" onClick="fn_report_generated_summary(3)" class="formbutton" />
                            
                            
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div>
        </form>
    </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?php 
/*
|	QC Comments 	:
|	Company and Team dropdown onchange takes more time to load dropdown because all return_library_array is loaded before that dropdown loaded action
|	Need to reset the form when onchange the company
|	Don't use exit() or die in every action in controller page

*/

?>