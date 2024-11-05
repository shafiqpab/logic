<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create  Title Order Booking Status Report.
Functionality	:	
JS Functions	:
Created by		:	Kaiyum
Creation date 	: 	28-01-2017
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
echo load_html_head_contents(" Title Order Booking Status Report", "../../../", 1, 1,'',1,1);

?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
var permission = '<? echo $permission; ?>';
 
function fn_report_generated(type)
{

	/*var cbo_company_name=$('#cbo_company_name').val();
	var txt_fab_bk_no 	=$('#txt_fab_bk_no').val();
	var txt_job_no 		=$('#txt_job_no').val();
	var txt_style_ref 	=$('#txt_style_ref').val();
	var txt_ord_no 		=$('#txt_ord_no').val();
	var txt_req_no 		=$('#txt_req_no').val();*/
/*	if((txt_fab_bk_no || txt_job_no || txt_style_ref || txt_ord_no || txt_req_no)=="")
	{
		if (form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*Date From*Date To')==false)
		{
			return;
		}
	}*/
	
	//alert(report_title);
	if (form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*Date From*Date To')==false)
	{
		return;
	}
	else
	{
		var report_title=$( "div.form_caption" ).html(); // dynamic report title
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_agent_name*cbo_year*txt_job_no*txt_style_ref*txt_ord_no*cbo_team_name*cbo_team_member*cbo_factory_marchant*cbo_date_category*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;;
		//alert(data);
		
		freeze_window(3);
		http.open("POST","requires/title_order_booking_status_report_controller.php",true);
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
		//alert(response[3]);
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>'; 
		
		var tableFilters = 
		{
			//col_33: "none",
			col_operation: {
			id: ["total_order_qnty","value_tot_mkt_required","value_tot_required_cost","value_tot_booking_qty","value_yarn_iss_qty","value_yarn_iss_cost","value_req_bal_qty","value_cost_bal_cost"],
			col: [10,16,17,18,19,20,21,22],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}
	 	setFilterGrid("table_body",-1,tableFilters);
		show_msg('3');
		release_freezing();
 	}
	
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	
	$("#table_body tr:first").hide();
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 
	$("#table_body tr:first").show();
	
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="400px";
}


function generate_worder_report(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,action)
{
	var data="action="+action+
				'&txt_booking_no='+"'"+booking_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&txt_order_no_id='+"'"+order_id+"'"+
				'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
				'&cbo_fabric_source='+"'"+fabric_source+"'"+
				'&id_approved_id='+"'"+approved+"'"+
				'&txt_job_no='+"'"+job_no+"'";
				//alert(action)
	if(type==1)	
	{			
		http.open("POST","../../../order/woven_order/requires/short_fabric_booking_controller.php",true);
	}
	else if(type==2)
	{
		http.open("POST","../../../order/woven_order/requires/fabric_booking_controller.php",true);
	}
	else
	{
		http.open("POST","../../../order/woven_order/requires/sample_booking_controller.php",true);
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
'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
		d.close();
	}
}



</script>

<style>
	hr
	{
		color: #676767;
		background-color: #676767;
		height: 1px;
	}
</style> 
</head>
 
<body onLoad="set_hotkey();">

<form id="yarnPurchaseReqReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../../",'');  ?>
         
         <h3 style="width:1400px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1340px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Agent</th>
                    <th>Job Year</th>
                    <th>Job No</th>
                    <th>Style Ref</th>
                    <th>Order No</th>
                    <th>Team</th>
                    <th>Team Member</th>
                    <th>Factory Merchant</th>
                    <th>Date Category</th>
                    <th colspan="2">Date</th>
                             
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('yarnPurchaseReqReport_1','report_container*report_container2','','','')" class="formbutton" style="width:80px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/title_order_booking_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/title_order_booking_status_report_controller', this.value, 'load_drop_down_agent', 'agent_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td id="agent_td">
                            <? 
                                echo create_drop_down( "cbo_agent_name", 120, $blank_array,"", 1, "-- All Agent --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", "", "",0,"" ); ?></td>
                        <td>
                        <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Write" />
                        </td>
                        <td>
                        <input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px" placeholder="Write" />
                        </td>
                        <td>
                        <input type="text" name="txt_ord_no" id="txt_ord_no" class="text_boxes" style="width:70px" placeholder="Write" />
                        </td>
                        <td >                
                            <?
                                echo create_drop_down( "cbo_team_name", 100, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, " load_drop_down( 'requires/title_order_booking_status_report_controller', this.value, 'load_drop_down_team_member', 'team_td' );load_drop_down( 'requires/title_order_booking_status_report_controller', this.value, 'factory_merchant_dropdown', 'div_marchant_factory' );" );
                            ?>
                        </td>
                        <td id="team_td">             
                             <? 
								echo create_drop_down( "cbo_team_member", 100,$blank_array,"", 1,"-- All --", date("Y",time()), "",0,"" );
                            ?>	
                        </td>
                        <td id="div_marchant_factory">               
                            <?
                               echo create_drop_down( "cbo_factory_marchant", 100,$blank_array,"",1,"-- All --", date("Y",time()), "",0,"" );
                            ?>
                        </td>
                        <td>
                           <? 
						   	   $date_cat_arr=array(1=>"Cut Off Date");
                               echo create_drop_down( "cbo_date_category", 120, $date_cat_arr,"", 1, "-- Select --", 1, "",0,"" );
                            ?>
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
                        </td>
                        <td>
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date" readonly>
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated(1)" />
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
            <br />
        </fieldset>
    	</div>
    </div>
    <div style="display:none" id="data_panel"></div>   
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>   
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_discrepancy').val(0);
</script>
</html>
