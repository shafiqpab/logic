<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Knitting Status Report.
Functionality	:	
JS Functions	:
Created by		: 
Creation date 	: 	14-06-2017
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
echo load_html_head_contents("Knitting Summary Report", "../../", 1, 1,$unicode,1,1);

?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
 
function fn_report_generated(type)
{
	if (form_validation('cbo_company_name','Comapny Name')==false)
	{
		return;
	}
	
	var data="action=report_generate"+get_submitted_data_string('cbo_type*cbo_company_name*cbo_buyer_name*cbo_season_id*txt_job_no*hide_job_id*txt_machine_dia*cbo_party_type*txt_order_no*hide_order_id*txt_program_no*txt_machine_no*txt_machine_id*txt_date_from*txt_date_to*cbo_knitting_status*cbo_based_on*cbo_year*txt_booking_no',"../../")+'&report_type='+type;

	freeze_window(3);
	http.open("POST","requires/knitting_summary_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse;
}
	

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		var response=trim(http.responseText).split("####");
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:100px"/>&nbsp;&nbsp;'; 
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
	var job_IDs = $("#hide_job_id").val();
	var companyID = $("#cbo_company_name").val();
	var page_link='requires/knitting_summary_report_controller.php?action=order_no_search_popup&companyID='+companyID+'&job_IDs='+job_IDs;
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

function openmypage_job()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	
	var companyID = $("#cbo_company_name").val();
        var cbo_year = $("#cbo_year").val();
        
	var page_link='requires/knitting_summary_report_controller.php?action=job_no_search_popup&companyID='+companyID+'&cbo_year='+cbo_year;
	var title='Job No Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var job_no=this.contentDoc.getElementById("hide_job_no").value;
		var job_id=this.contentDoc.getElementById("hide_job_id").value;
		
		$('#txt_job_no').val(job_no);
		$('#hide_job_id').val(job_id);	 
	}
}
	
	
function generate_report(company_id,program_id)
{
	 print_report( company_id+'*'+program_id + '*' + '../../', "print", "../requires/yarn_requisition_entry_controller" ) ;
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
	var page_link='requires/knitting_summary_report_controller.php?action=machine_no_search_popup&companyID='+companyID;
	var title='Machine No Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=260px,height=300px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
//		var machine_no=this.contentDoc.getElementById("hide_machine").value.split("_");
//		$('#txt_machine_no').val(machine_no[1]);
                
                var machine_no=this.contentDoc.getElementById("hide_machine").value;
		var machine_id=this.contentDoc.getElementById("hide_machine_id").value;
		
		$('#txt_machine_no').val(machine_no);
		$('#txt_machine_id').val(machine_id);	
                
                
	}
}

function openmypage(ids,action)
{
    if(action=='knitting_prod_popup'){var width='800px';}
	else{var width='400px';}
	
	var title = "";
    var companyID = $("#cbo_company_name").val();
    var cbo_year = $("#cbo_year").val();
    var page_link='requires/knitting_summary_report_controller.php?action='+action+'&companyID='+companyID+'&cbo_year='+cbo_year+'&ids='+ids;
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+width+',height=250px,center=1,resize=1,scrolling=0','../');
}

function getbuyerId() 
{	
	var cbo_company_name = document.getElementById('cbo_company_name').value;
	load_drop_down( 'requires/knitting_summary_report_controller',cbo_company_name, 'load_drop_down_buyer', 'buyer_td' );
	set_multiselect('cbo_buyer_name','0','0','','0'); 
	setTimeout[($("#buyer_td a").attr("onclick", "disappear_list(cbo_buyer_name,'0');loadSeason();"), 3000)];
	
}
function loadSeason()
{
	var cbo_buyer_name = document.getElementById('cbo_buyer_name').value;
	load_drop_down( 'requires/knitting_summary_report_controller',cbo_buyer_name, 'load_drop_down_season', 'season_td'); 
}

</script>


</head>
 
<body onLoad="set_hotkey();">

<form id="knittingStatusReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:1550px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1550px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Season</th>
                    <th>Job Year</th>
                    <th>Job No</th>
                    <th>Booking No</th>
                    <th>Order No</th>
                    <th>Machine Dia</th>
                    <th>Report Type</th>
                    <th>Party Name</th>
                    <th>Program No</th>
                    <th>Machine No</th>
                    <th>Status</th>
                    <th>Based On</th>
                    <th>Date Range</th>
                </thead>
                <tbody>
                    <tr class="general" id="td_company">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "- Select Company -", $selected, "getbuyerId()" );
                            ?>
                        </td>
						<td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "- Select Buyer -", $selected, "",0,"" );
                            ?>
                        </td>
						
                        <td id="season_td">
                            <? 
                                echo create_drop_down( "cbo_season_id", 110, $blank_array,"", 1, "- Select Season -", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                        	<?
								echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>
                        </td>
                        <td>
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:60px" placeholder="Browse" onDblClick="openmypage_job();" autocomplete="off" readonly>
                            <input type="hidden" name="hide_job_id" id="hide_job_id" readonly>
                        </td>   
                         <td>
                            <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px" placeholder="Write Booking Prefix"  autocomplete="off" >
                        </td>    
                        </td>
                        <td>
                            <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:90px" placeholder="Browse" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off" readonly>
                            <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                        </td>
                        <td>
                            <input name="txt_machine_dia" id="txt_machine_dia" class="text_boxes" style="width:55px" placeholder="write" >
                        </td>
                        <td>
                        	<?
								$search_by_arr=array(0=>"All",1=>"Inside",3=>"Outside");
								echo create_drop_down( "cbo_type", 102, $search_by_arr,"",0, "", "0","load_drop_down( 'requires/knitting_summary_report_controller',this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_party_type', 'party_type_td' );set_multiselect('cbo_party_type','0','0','','');",0 );
							?>
                        </td> 
                        <td id="party_type_td">
                        	<?
								echo create_drop_down( "cbo_party_type", 120, $blank_array,"",1, "--Select--", "",'',1 );
							?>
                        </td> 
                        <td>
                            <input name="txt_program_no" id="txt_program_no" class="text_boxes_numeric" style="width:60px">
                        </td>
                        <td>
                            <input name="txt_machine_no" id="txt_machine_no" class="text_boxes" style="width:90px" onDblClick="fn_open_machine()" placeholder="Browse" readonly>
                            <input name="txt_machine_id" id="txt_machine_id" value="" type="hidden">
                        </td>
                         <td align="center">
                            <? 
                                echo create_drop_down( "cbo_knitting_status", 110, $knitting_program_status,"", 0, "- Select -", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                        	<?
								$based_on_arr=array(1=>"Plan Date",2=>"Program Date");
								echo create_drop_down( "cbo_based_on", 97, $based_on_arr,"",0, "", "",'',0 );
							?>
                        </td> 
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px" placeholder="From Date"/>
                            &nbsp;
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px" placeholder="To Date"/>
                        </td>
                        
                    </tr>
                    <tr>
                        <td colspan="12" align="center"><? echo load_month_buttons(1); ?></td>
                        <td colspan="3">
                        <input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated(1)" />
         
                             <input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knittingStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:60px" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>   
</body>
<script>
	set_multiselect('cbo_knitting_status','0','0','','');
   	set_multiselect('cbo_buyer_name','0','0','','0');
	

</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
