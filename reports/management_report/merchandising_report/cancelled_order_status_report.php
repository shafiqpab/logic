<?php
/********************************* Comments *************************
*	Purpose			: 	This Form Will Create Cancelled Order status Report.
*	Functionality	:	
*	JS Functions	:
*	Created by		:	Md. Saidul Islam 
*	Creation date 	: 	04-10-2015
*	Updated by 		: 		
*	Update date		: 		   
*	QC Performed BY	:		
*	QC Date			:	
*	Comments		:
*********************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Cancelled Order Status Report","../../../", 1, 1, $unicode,1,1);
?>	

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	function fn_report_generated()
	{
		if($("#txt_job_no").val()=="" && $("#txt_order_no").val()=="")
		{
			var validationFill='cbo_company_name*txt_date_from*txt_date_to';
			var validationCap='Company Name*From Date*To Date';
		}
		else
		{
			var validationFill='cbo_company_name';
			var validationCap='Company Name';
		}
		
		if(form_validation(validationFill,validationCap)==false)
		{
			return;
		}
		else
		{	
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_agent*txt_style*cbo_team_leader*cbo_team_member*cbo_factory_marchant*txt_job_no*txt_order_no*txt_inter_ref*cbo_date_category*txt_date_from*txt_date_to*cbo_order_status',"../../../");
			freeze_window(3);
			http.open("POST","requires/cancelled_order_status_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton"/></a> <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>'; 
			show_msg('3');
			release_freezing();

			var tableFilters = 
			{
				col_31: "none",
				col_operation:
				{ 
					id: ["total_po_qty","total_lcsc_val","total_fb_qty","total_tb_qty","total_sb_qty","total_yi_qty","total_knite_qty","total_dyeing_qty","total_ffr_val","total_tr_val","total_ex_factory_qty"],
					col: [11,12,13,14,15,16,17,18,19,20,26],
					operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
				}
			}
			setFilterGrid("table_body",-1,tableFilters);
		}
	}
	
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
		$('#table_body tr:first').show();
	}
	
    function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('main_body').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
		$('#table_body tr:first').show();
	}
	
	function openmypage_bill(po_id,type,tittle)
	{
		//alert(po_id); return;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/cancelled_order_status_report_controller.php?po_id='+po_id+'&action='+type, tittle, 'width=560px, height=350px, center=1, resize=0, scrolling=0', '../../');
	}
	
	function openmypage_popup(po_break_down_id,popup_caption,action)
	{
		//var title="";
		emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/cancelled_order_status_report_controller.php?po_break_down_id='+po_break_down_id+'&action='+action, popup_caption, 'width=570px,height=350px,center=1,resize=0,scrolling=0','../../');
		
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
    <form id="cost_breakdown_rpt">
        <div style="width:100%;" align="center">
            <?php echo load_freeze_divs ("../../../"); ?>
             <h3 align="left" id="accordion_h1" style="width:1320px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
                <div id="content_search_panel"> 
                <fieldset style="width:1320px;">
                    <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <tr>                   
                                <th class="must_entry_caption">Company Name</th>
                                <th>Buyer Name</th>
                                <th>Agent</th>
                                <th>Style</th>
                                <th>Team Leader</th>
                                <th>Team Mamber</th>
                                <th>Factory Merchant</th>
                                <th>Job No</th>
                                <th>Order No</th>
								<th>Internal Ref.</th>
                                <th>Active Status</th>
                                <th>Date Category</th>
                                <th>Date</th>
                                <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                            </tr>
                         </thead>
                        <tbody>
                        <tr class="general">
                            <td> 
                                <?php
                                    echo create_drop_down( "cbo_company_name", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cancelled_order_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/cancelled_order_status_report_controller', this.value, 'load_drop_down_agent', 'agent_td' );" );
                                ?>
                            </td>
                            <td id="buyer_td">
                                <?php 
                                    echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                                ?>
                            </td>
                            <td id="agent_td">
                                <?php
                                
									echo create_drop_down( "cbo_agent", 80, $blank_array,"", 1, "-- All --", $selected, "" );  

								
								
								?>
                            </td>
                            <td>
                                <input type="text" name="txt_style" id="txt_style" class="text_boxes" style="width:80px" placeholder="Write" />
                            </td>
                            <td>
                                <?php
									echo create_drop_down( "cbo_team_leader", 65, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- All --", $selected, "load_drop_down( 'requires/cancelled_order_status_report_controller', this.value, 'dealing_merchant_dropdown', 'div_marchant' );load_drop_down( 'requires/cancelled_order_status_report_controller', this.value, 'factory_merchant_dropdown', 'div_marchant_factory' ) " );
								?>
                            </td>
                            <td id="div_marchant">
                                <?php
                                    echo create_drop_down( "cbo_team_member", 65,$blank_array,"", 1,"-- All --", date("Y",time()), "",0,"" );
                                ?>
                            </td>
                            <td id="div_marchant_factory">
                                <?php
                                    echo create_drop_down( "cbo_factory_marchant", 80,$blank_array,"",1,"-- All --", date("Y",time()), "",0,"" );
                                ?>
                            </td>
                            <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Write" /></td>
                            <td>
                                <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:70px" placeholder="Write" />
                            </td>
							<td><input type="text" name="txt_inter_ref" id="txt_inter_ref" class="text_boxes" style="width:70px" placeholder="Write" /></td>
                            <td id="div_marchant_factory">
                                <?php
                                echo create_drop_down( "cbo_order_status", 85, $row_status,"",0,"",3,"" ); 
                                ?>
                            </td>
							
                            <td>
                                <?php
                                    $date_cat_arr=array(1=>'Country Ship Date',2=>'Pub. Ship Date',3=>'Cancelled Date',4=>'InActive Date',5=>'PO Insert Date');
									echo create_drop_down( "cbo_date_category", 80, $date_cat_arr,"", 0, "-- Select --", 3, "",0,'','','','','' );
                                ?>
                            </td>
                            <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" >&nbsp; To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" ></td>
                            <td>
                                <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated()" />
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <table>
                        <tr>
                            <td>
                                <?php echo load_month_buttons(1); ?>
                            </td>
                        </tr>
                    </table> 
                </fieldset>
            </div>
        </div>
        <div id="report_container" style="text-align:center;"></div>
        <div id="report_container2"></div>
     </form>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>