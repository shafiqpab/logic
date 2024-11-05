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
	//col: [18,20,21,22,24,25,26,27,28,29,30,31],
	var tableFilters = 
	{
		col_operation: 
		{
			id: ["td_order_qty_pcs","td_order_val","td_commission","td_net_order_value","td_smv_tot","td_smv_tot_iron","td_yarn_req_tot","td_ex_factory_qnty","td_short_access_qnty","td_over_access_qnty","td_total_short_access_value","td_total_over_access_value"],
			//col: [19,21,22,23,26,27,28,29,31,32,33,34],
			col: [18,20,21,22,25,26,27,28,30,31,32,33],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		} , 
		/*col_28: "select",
		col_29: "select",
		col_33: "select",*/
	}
	
	function generate_report(div,type)
	{
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
		var cbo_agent_name=document.getElementById('cbo_agent_name').value;
		if(cbo_company_name ==0)
		{
			alert("Select Company");
			return;
		}
		if(txt_date_from =="" || txt_date_to =="")
		{
			alert("Select Date Range");
			return;
		}
		var r=confirm("Press  OK to open  without zero value\nPress  Cancel  to open with zero value");

		if(r==true)
		{
			zero_value=0
		}
		else
		{
			zero_value=1
		}
		var data=cbo_company_name+"_"+cbo_buyer_name+"_"+txt_date_from+"_"+txt_date_to+"_"+cbo_team_name+"_"+cbo_team_member+"_"+cbo_category_by+"_"+zero_value+"_"+cbo_capacity_source+"_"+cbo_year+"_"+txt_style_ref+"_"+cbo_factory_merchant+"_"+cbo_agent_name;
		freeze_window(3);
		http.open("GET","requires/group_capacity_and_order_booking_status_controller.php?data="+data+"&type="+type,true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=(http.responseText).split('####');	
			document.getElementById('report_container2').innerHTML=response[0];
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			append_report_checkbox('table_header_1',1);
			setFilterGrid("table_body",-1,tableFilters);
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/group_capacity_and_order_booking_status_controller.php?action=work_progress_report_details&job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type, 'Work Progress Report Details', 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../../')
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
	function fn_report_generated_summary()
	{
		if(form_validation('cbo_company_name','Company Name')==false)//*txt_date_from*txt_date_to*From Date*To Date
		{
			return;
		}
		else
		{	
		//alert($('#cbo_year_selection').val())
			var data="action=generate_report_summary"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*cbo_category_by*cbo_capacity_source*txt_date_from*txt_date_to',"../../../");
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/group_capacity_and_order_booking_status_controller.php",true);
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
		var page_link='requires/group_capacity_and_order_booking_status_controller.php?action='+action+'&job_no='+job_no+'&type='+type
		var title="File View";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=300px,center=1,resize=0,scrolling=0','../../')
	}	
	
	function generate_ex_factory_popup(action,country_id,id,width)
	{
		//alert(job_no); 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/group_capacity_and_order_booking_status_controller.php?action='+action+'&country_id='+country_id+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
	
</script>
</head>
<body>
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../");  ?>
   <h3 align="left"  id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div id="content_search_panel" > 
            <form>
                <fieldset style="width:90%;">
                    <div  style="width:100%" align="center">
                            <table class="rpt_table" width="990" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                    <tr>
                                        <th colspan="13"><font size="3"></font></th>
                                    </tr>
                                    <tr>
                                        <th>Company</th>
                                        <th>Buyer</th>
                                        <th>Agent</th>
                                        <th>Style</th>
                                        <th>Year</th>
                                        <th>Team</th>
                                        <th>Team Member</th>
                                        <th>Factory Merchant</th>
                                        <th colspan="2">Date</th>
                                        <th>Date Category</th>
                                        <th>Capacity Source</th>
                                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:90px" class="formbutton" /></th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td>
                                           <?
                                           echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " load_drop_down( 'requires/group_capacity_and_order_booking_status_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/group_capacity_and_order_booking_status_controller', this.value, 'load_drop_down_agent', 'agent_td' )" );
                                            ?> 
                                    </td>
                                    <td id="buyer_td">
                                     <? 
                                        echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                                     ?>	
                                    </td>
                                    <td id="agent_td">
                                     <? 
                                        echo create_drop_down( "cbo_agent_name", 172, $blank_array,"", 1, "-- Select agent --", $selected, "" );
                                     ?>	
                                    </td>
                                    <td align="center">
                                    <input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px" />
                                    </td>
                                    <td>
                                     <? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?>	
                                    </td>
                                    <td >                
                                    
                                    <?
                                           echo create_drop_down( "cbo_team_name", 130, "select id,team_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_name","id,team_name", 1, "-- Select Team --", $selected, " load_drop_down( '../merchandising_report/requires/group_capacity_and_order_booking_status_controller', this.value, 'load_drop_down_team_member', 'team_td' );load_drop_down( '../merchandising_report/requires/group_capacity_and_order_booking_status_controller', this.value, 'cbo_factory_merchant', 'div_marchant_factory' ) " );
                                            ?>
                                    </td>
                                    <td id="team_td">
                                    <div id="div_team">
                                    <? 
                                        echo create_drop_down( "cbo_team_member", 172, $blank_array,"", 1, "- Select Team Member- ", $selected, "" );
                                     ?>	
                                    </div>
                                    </td>
                                    <td id="div_marchant_factory" > 
									<? 
                                        echo create_drop_down( "cbo_factory_merchant", 172, $blank_array,"", 1, "-- Select --", $selected, "" );
                                    ?>	
                                    </td>
                                    <td><input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:75px">
                                    </td>
                                    <td><input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:75px">
                                    </td>
                                    <td>
                                    <? echo create_drop_down( "cbo_category_by", 100, $report_date_catagory,"", 0, "", $selected, "" );?>
                                    <!--<select name="cbo_category_by" id="cbo_category_by"  style="width:130px" class="combo_boxes">
                                        <option value="1">Country Ship Date Wise </option>
                                        <option value="2" selected>Pub Ship Date Wise </option>
                                        <option value="3">Org. Ship Date Wise </option>
                                        <option value="4">Insert Date Wise </option>
                                    </select>-->
                                    
                                    </td>
                                    <td>
                                    <?
                                     echo create_drop_down( "cbo_capacity_source",160,$knitting_source,"", 1, "--All--", $selected, "","","1,3","","","","");
                                    ?>
                                    </td>
                                    <td><input type="button" name="search" id="search" value="Show" onClick="generate_report('report_container2','report_generate_gr')" style="width:90px" class="formbutton" />

                                      
                                        <input name="fillter_check" id="fillter_check" type="hidden" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="12" align="center">
                                        <? echo load_month_buttons(1); ?>
                                    </td>
                                    <td>
                                   <!-- <input type="button" name="summary" id="summary" value="Summary" onClick="fn_report_generated_summary()" style="width:90px" class="formbutton" />-->
                                    </td>
                                </tr>
                            </table>
                    </div>
                </fieldset>
            </form>
        </div>
       <div id="report_container" align="center"></div>
       <div id="report_container2"></div>
    </div>
    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>