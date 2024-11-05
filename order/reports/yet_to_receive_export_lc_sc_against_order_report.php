<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Yet to Receive Export LC/SC against order Report.
Functionality	:	
JS Functions	:
Created by		:	REZA 
Creation date 	: 	18-08-2015
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
echo load_html_head_contents("Yet to Receive Export LC/SC against order", "../../", 1, 1,$unicode,1,1);
?>	

<script>
var permission = '<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  


 var tableFilters = 
		 {
			col_operation:
			{
				id: ["tot_qty","tot_val","tot_sf"],
				col: [8,10,13],
				operation: ["sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML"]
			}
		}	
		
function fn_report_generated(shiping_status)
{
	var company_name=$('#cbo_company_name').val();
	var date_from=$('#txt_date_from').val();
	var date_to=$('#txt_date_to').val();
	
	/*if(company_name!=0 || date_from!="" || date_to!="")
		{
			if(form_validation('cbo_company_name','Company')==false)
			{
				return;
			}
		}*/
			
	if(company_name==0 && date_from=='' && date_to=='' )
	{
		if (form_validation('cbo_company_name','Plsease Select Comapny')==false)//*txt_date_from*txt_date_to*Please Select From Date*Please Select To Date
		{
			return;
		}
	}
	else if(company_name==0)
	{
		if (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
		{
			return;
		}
	}
	
		
		var data="action=report_generate&shipingStatus="+shiping_status+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*txt_job_no*cbo_year*txt_order_no*txt_style*txt_date_from*txt_date_to*cbo_order_status*cbo_brand_id*cbo_season_name*cbo_season_year',"../");
		freeze_window(3);
		http.open("POST","requires/yet_to_receive_export_lc_sc_against_order_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
}
	

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split("****");
		$('#report_container2').html(reponse[0]);
			document.getElementById('report_container1').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;';/*<input type="button" onclick="new_window('+tot_rows+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>*/
 		
		setFilterGrid("tbl_details",-1,tableFilters);
					
		show_msg('3');
		release_freezing();

		
		
 	}
}






function show_comment_info(job_no)
	{
		if(job_no)
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'po_comments.php?job_no='+job_no, 'Comment Details', 'width=500px,height=300px,center=1,resize=0,scrolling=0',' ../')	
		}	
	}

function new_window(type)
	{
		var report_div='';
		var scroll_div='';
		if(type==1)
		{
			report_div="print_report_samp";
			//scroll_div='scroll_body';
		}
		else if(type==2)
		{ 
			report_div="print_report_pp";
			//scroll_div='scroll_body2';
		}
 		
 		//document.getElementById(scroll_div).style.overflow="auto";
		//document.getElementById(scroll_div).style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById(report_div).innerHTML+'</body</html>');
		d.close();
		
		//document.getElementById(scroll_div).style.overflowY="scroll";
		//document.getElementById(scroll_div).style.maxHeight="380px";
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
	
	function get_buyer_config(buyer_id)
	{
		//sub_dept_load(buyer_id,document.getElementById('cbo_product_department').value);
		//check_tna_templete(buyer_id)
		//get_php_form_data(buyer_id,'get_buyer_config','requires/woven_order_entry_controller' );
		load_drop_down( 'requires/yet_to_receive_export_lc_sc_against_order_report_controller', buyer_id, 'load_drop_down_season_buyer', 'season_td');
		load_drop_down( 'requires/yet_to_receive_export_lc_sc_against_order_report_controller', buyer_id+'*'+1, 'load_drop_down_brand', 'brand_td');
	}
</script>

</head>
 
<body onLoad="set_hotkey();">
<form id="sample_approval_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission);  ?>
       
         <fieldset style="width:1600px;">
        	<legend>Search Panel</legend>
            <table class="rpt_table" width="1580px" cellpadding="0" cellspacing="0" align="center">
               <thead>                    
                    <th>Company Name</th>
                    <th>Buyer Name</th>
                    <th>Brand</th>
                    <th>Season</th>
                    <th>Season Year</th>
                    <th>Team</th>
                    <th>Team Member</th>
                    <th>Job No</th>
                    <th>Year</th>
                     <th>Order No</th>
                    <th>Style No</th>
					<th>Order Status</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" /></th>
                 </thead>
                <tbody>
                <tr class="general">
                    <td> 
                        <?
                            echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yet_to_receive_export_lc_sc_against_order_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td');" );
                        ?>
                    </td>
                    <td id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"get_buyer_config(this.value);" );
                        ?>
                    </td> 
                   <td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 100, $blank_array,'', 1, "--Brand--",$selected, "" ); ?>
                     <td id="season_td"><? echo create_drop_down( "cbo_season_name", 100, $blank_array,"", 1, "-- Select Season --", $selected, "" ); ?>
                     	
                     </td>
                     <td>
                     	<? echo create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?>
                 	 </td>
                     <td>
                             <?
                                    echo create_drop_down( "cbo_team_name", 110, "select id,team_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_name","id,team_name", 1, "-- Select Team --", $selected, " load_drop_down( 'requires/yet_to_receive_export_lc_sc_against_order_report_controller', this.value, 'load_drop_down_team_member', 'team_td' )" );
                              ?>
                         </td>
                         <td id="team_td">
							 <? 
                                echo create_drop_down( "cbo_team_member", 120, $blank_array,"", 1, "- Select Team Member- ", $selected, "" );
                             ?>	
                         </td>
                         <td>
                           <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:80px"  placeholder="Job prifix No" >
                          </td>
                          <td>
                              <? 
                                echo create_drop_down( "cbo_year", 60, $year,"", 1, "- Select- ",  date("Y",time()), "" );
                             ?>	
                          
                          </td>
                           <td>
                           <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:90px"  placeholder="Write" >
                          </td>
                          <td>
                           <input type="text" name="txt_style" id="txt_style" class="text_boxes" style="width:90px"   placeholder="Write">
                          </td>
						   <td>
							<? 
								$order_status=array(0=>"All",1=>"Confirmed",2=>"Projected"); 
								echo create_drop_down( "cbo_order_status", 100, $order_status,"", 0, "", 1, "" ); 
                            ?>
                        </td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" >&nbsp; To
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date"  ></td>
                    <td>
                        <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated(0)" />
                    </td>
                </tr>
                </tbody>
            </table>
            <table>
            	<tr>
                	<td>
 						<? echo load_month_buttons(1);  ?>
                   	</td>
                </tr>
            </table> 
            <br />
        </fieldset>
    </div>
    
    <div id="report_container1" align="center"></div>
    <div id="report_container2" align="center"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
