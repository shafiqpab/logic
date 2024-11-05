<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Accessories Followup Report.
Functionality	:	
JS Functions	:
Created by		:	Fuad 
Creation date 	: 	10-02-2013
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
echo load_html_head_contents("Accessories Followup Report", "../../", 1, 1,$unicode,'1','');
?>	

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated()
	{
		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name')==false)//*txt_date_from*txt_date_to*From Date*To Date
		{
			return;
		}
		else
		{	
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_brand_id*cbo_team_name*cbo_team_member*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*cbo_season_id*txt_internal_ref*txt_order_no*cbo_year_selection*cbo_category_by',"../../");
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/order_history_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			var search_by=reponse[3];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+tot_rows+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			/*if(tot_rows*1>1)
			{
			if(search_by==1)
			    {
			
				 var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["total_order_qnty","total_order_qnty_in_pcs","value_req_qnty","value_pre_costing","value_wo_qty","value_in_qty","value_rec_qty","value_issue_qty","value_leftover_qty"],
					   col: [5,7,14,15,16,17,18,19,20],
					   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}	
				 }
				}
			if(search_by==2)
			    {
			
				 var tableFilters = 
				 {
					 
					col_operation: {
					   id: ["total_order_qnty","value_rec_qty","value_issue_qty","value_leftover_qty"],
					   col: [5,8,9,10],
					   operation: ["sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
					}	
				 }
				}
				setFilterGrid("table_body",-1);
			}*/
			 var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["ord_qty","value_yarn_amount"],
					   col: [10,11],
					   operation: ["sum","sum"],
					   write_method: ["innerHTML","innerHTML"]
					}	
				 }
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}
	function fn_report_generated_show_two(type)
	{
		var job_no=document.getElementById('txt_job_no').value;	
		var order_no=document.getElementById('txt_order_no').value;
		var season=document.getElementById('cbo_season_id').value;
		var internal_ref=document.getElementById('txt_internal_ref').value;
		var date_form=document.getElementById('txt_date_from').value;
		var date_to=document.getElementById('txt_date_to').value;
		var style_ref=document.getElementById('txt_style_ref').value;

		if (type=1)
		{
			if(job_no!="" || order_no!="" || internal_ref!="" || style_ref!="")
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
		}
		else
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
			{
				return;
			}
		}	

			var data="action=report_generate_2"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*cbo_season_id*txt_internal_ref*txt_order_no*cbo_year_selection*cbo_category_by',"../../");
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/order_history_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_show_two_response;
		
	}
		
	
	function fn_report_generated_show_two_response()
	{
		if(http.readyState == 4) 
		{
			
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			var search_by=reponse[3];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+tot_rows+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		
			 var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["ord_qty","value_yarn_amount"],
					   col: [10,11],
					   operation: ["sum","sum"],
					   write_method: ["innerHTML","innerHTML"]
					}	
				 }
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}
	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}
	

	function new_window(html_filter_print)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		//if(html_filter_print*1>1) $("#table_body tr:first").hide();
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="400px";
		
		//if(html_filter_print*1>1) $("#table_body tr:first").show();
		$("#table_body tr:first").show();
	}	
	
	
	/*function generate_report(company,job_no,type)
	{
		var data="action="+type+"&txt_job_no='"+job_no+"'&cbo_company_name="+company;
		http.open("POST","../woven_order/requires/pre_cost_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
	}
	
	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><body>'+http.responseText+'</body</html>');
			d.close();
		}
	}*/
	
	/*function openmypage(po_id,item_name,job_no,book_num,trim_dtla_id,action)
	{ //alert(book_num);
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_history_report_controller.php?po_id='+po_id+'&item_name='+item_name+'&job_no='+job_no+'&book_num='+book_num+'&trim_dtla_id='+trim_dtla_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	function openmypage_inhouse(po_id,item_name,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_history_report_controller.php?po_id='+po_id+'&item_name='+item_name+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage_issue(po_id,item_name,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_history_report_controller.php?po_id='+po_id+'&item_name='+item_name+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	function order_qty_popup(company,job_no,po_id,buyer,action)
	{
		//alert(po_id);
		var popup_width='800px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_history_report_controller.php?company='+company+'&job_no='+job_no+'&po_id='+po_id+'&buyer='+buyer+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}*/
	
	function remarks_popup(po_id,color_id,send_to_factory_date,recv_from_factory_date,submitted_to_buyer,approval_status_date,pp_submitted_to_buyer,pp_approval_status_date)
	{
		//alert(country_id);
		var popup_width='800px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_history_report_controller.php?action=remarks_veiw&po_id='+po_id+'&color_id='+color_id+'&send_to_factory_date='+send_to_factory_date+'&recv_from_factory_date='+recv_from_factory_date+'&submitted_to_buyer='+submitted_to_buyer+'&approval_status_date='+approval_status_date+'&pp_submitted_to_buyer='+pp_submitted_to_buyer+'&pp_approval_status_date='+pp_approval_status_date, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	
	function labdip_popup(po_id,color_id,send_to_factory_date,recv_from_factory_date,submitted_to_buyer,approval_status_date,pp_submitted_to_buyer,pp_approval_status_date)
	{
		//alert(country_id);
		var popup_width='320px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_history_report_controller.php?action=labdip_veiw&po_id='+po_id+'&color_id='+color_id+'&send_to_factory_date='+send_to_factory_date+'&recv_from_factory_date='+recv_from_factory_date+'&submitted_to_buyer='+submitted_to_buyer+'&approval_status_date='+approval_status_date+'&pp_submitted_to_buyer='+pp_submitted_to_buyer+'&pp_approval_status_date='+pp_approval_status_date, 'Details Veiw', 'width='+popup_width+', height=200px,center=1,resize=0,scrolling=0','../');
	}
	
	function ppsample_popup(po_id,color_id,send_to_factory_date,recv_from_factory_date,submitted_to_buyer,approval_status_date,pp_submitted_to_buyer,pp_approval_status_date)
	{
		//alert(country_id);
		var popup_width='320px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_history_report_controller.php?action=ppsample_veiw&po_id='+po_id+'&color_id='+color_id+'&send_to_factory_date='+send_to_factory_date+'&recv_from_factory_date='+recv_from_factory_date+'&submitted_to_buyer='+submitted_to_buyer+'&approval_status_date='+approval_status_date+'&pp_submitted_to_buyer='+pp_submitted_to_buyer+'&pp_approval_status_date='+pp_approval_status_date, 'Details Veiw', 'width='+popup_width+', height=200px,center=1,resize=0,scrolling=0','../');
	}
	
	function booking_date_popup(po_id,color_id)
	{
		//alert(country_id);
		var popup_width='320px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_history_report_controller.php?action=booking_date_view&po_id='+po_id+'&color_id='+color_id, 'Details Veiw', 'width='+popup_width+', height=200px,center=1,resize=0,scrolling=0','../');
	}
	
	
	function fin_receive_date_popup(po_id,color_id,booking_id)
	{
		//alert(country_id);
		var popup_width='320px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_history_report_controller.php?action=fin_receive_date_view&po_id='+po_id+'&color_id='+color_id+'&booking_id='+booking_id, 'Details Veiw', 'width='+popup_width+', height=200px,center=1,resize=0,scrolling=0','../');
	}
	
	/*function search_populate(str)
		{
			
			if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Shipment Date";
				//document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"	value=""  />';
			}
			else if(str==2)
			{
				document.getElementById('search_by_th_up').innerHTML="Precost Date";
				///document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"	value=""  />';
			}
			
		}*/
	
</script>

</head>

<body onLoad="set_hotkey();">
<form id="accessoriesFollowup_report">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:1500px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:1500px;">
                <table class="rpt_table" width="1500" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                   		<tr>                    
                            <th>Company Name</th>
                            <th>Buyer Name</th>
							<th>Brand Name</th>
                            <th>Team</th>
                            <th>Team Member</th>
                            <th>Job No</th>
                            <th>Style Ref.</th>
							<th>Season</th>
                            <th>Internal Ref.</th>
                            <th>Order No</th>
                            <th>Date Category</th>
                            <th align="center">Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                            <?
								echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_history_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
						<td id="brand_td"><?=create_drop_down( "cbo_brand_id", 130, $blank_array,"", 1, "-- All Brand --", $selected, "",0,"" ); ?></td>
                         <td>
                             <?
                                    echo create_drop_down( "cbo_team_name", 120, "select id,team_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_name","id,team_name", 1, "-- Select Team --", $selected, " load_drop_down( 'requires/order_history_report_controller', this.value, 'load_drop_down_team_member', 'team_td' )" );
                              ?>
                         </td>
                         <td id="team_td">
							 <? 
                                echo create_drop_down( "cbo_team_member", 150, $blank_array,"", 1, "-Select Team Member- ", $selected, "" );
                             ?>	
                         </td>
                        <td align="center">
                    	<input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Job No" >
                        </td>
                        <td align="center">
                    	<input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:80px" placeholder="Styel Ref" >
                        </td>
						<td id="season_td">
                            <? 
                                echo create_drop_down( "cbo_season_id", 70, $blank_array,"", 1, "-- All --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:80px" placeholder="Internal Ref"></td>
                        
                         <td align="center">
                    	<input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:70px" placeholder="Order No" >
                        </td>
                    
                       <td>
                        <select name="cbo_category_by" id="cbo_category_by"  style="width:100px" class="combo_boxes">
                            <option value="1">Ship Date Wise </option>
                            <option value="2">PO Rec. Date Wise </option>
                            <option value="3">Update Date</option>
                        </select>
                    </td>
                  
                    			
                  <td>
                  <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >&nbsp; To
                   <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" ></td>
                    </td> 
                       
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated()" />
							<input type="button" id="show_button2" class="formbutton" style="width:50px" value="Show 2" onClick="fn_report_generated_show_two(1)" />
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
    
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
 </form>    
</body>
<script>
	//set_multiselect('cbo_item_group','0','0','0','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
