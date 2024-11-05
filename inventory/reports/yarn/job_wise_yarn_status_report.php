<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Fabric Receive Status Report 2.
Functionality	:	
JS Functions	:
Created by		:	Md Didarul Alam
Creation date 	: 	01/12/2019
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
echo load_html_head_contents("Job Wise Yarn Status Report","../../../", 1, 1, $unicode,0,0); 	
?>	
<script>
	var permission = '<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";  

	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		else
		{			
			var txt_job_no = $('#txt_job_no').val();
			var txt_booking_no = $('#txt_booking_no').val();

			if (txt_job_no=="" && txt_booking_no=="")
			{
				if (form_validation('txt_date_from*txt_date_to', 'Date Form*Date To')==false)
				{
					return;
				}
			}
			var action = "";
			if(type == 1){
				action = "report_generate";
			}else if(type == 2){
				action = "report_generate2";
			}
					
			var data="action="+action+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*cbo_year_selection*txt_job_no*txt_style*txt_booking_no',"../../../");

			freeze_window(3);
			http.open("POST","requires/job_wise_yarn_status_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>&nbsp;&nbsp;<input type="button" onclick="new_window_summary()" value="Print Preview Summary" name="Print" class="formbutton" style="width:150px"/>&nbsp;&nbsp;<input type="button" onclick="new_window_summary_yarn_count()" value="Print Yarn Count Summary" name="Print" class="formbutton" style="width:150px"/>'; 
			//setFilterGrid("tbl_list_search",-1,tableFilters);
			//append_report_checkbox('table_header_1',1);
			// $("input:checkbox").hide();
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		$(".flt").css("display","none");
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$(".flt").css("display","block");
	}

	function new_window_summary()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('summary').innerHTML+'</body</html>');
		d.close(); 
	}


	function new_window_summary_yarn_count()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('summary_yarn_count').innerHTML+'</body</html>');
		d.close(); 
	}




	function openmy_popup_page(data,order_id,action,popup_width)
	{  		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/job_wise_yarn_status_report_controller.php?data='+data+'&order_id='+order_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');	
	}	

	function openmy_popup_page_two(data,issue_id,prod_id,rtn_id,action,popup_width)
	{  	
		//alert(rtn_id);	
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/job_wise_yarn_status_report_controller.php?data='+data+'&issue_id='+issue_id+'&prod_id='+prod_id+'&rtn_id='+rtn_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');	
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

<form id="fabricReceiveStatusReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../../",$permission);  ?>
         <h3 style="width:910px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:910px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                	<th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    
                    <th colspan="2" title="Data Will be Populated Acording to Pub. Ship Date Wise." id="date_td"> Shipment Date</th>
                    <th>Job Year</th>
                    <th>Job No</th>
                    <th>Style</th>
                    <th>F. Booking No</th>
                    <th ><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('fabricReceiveStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/job_wise_yarn_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                       
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" readonly>
                        </td>
                        <td>
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" readonly>
                        </td>
                        <td>
                        	<? 
								echo create_drop_down( "cbo_year", 80, create_year_array(),"", 1,"-- All --", "", "",0,"" );
							?>
                        </td>
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Write"/></td>
                        <td><input type="text" name="txt_style" id="txt_style" class="text_boxes" style="width:70px" placeholder="Write"/></td>
                        <td><input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px" placeholder="Write"/></td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" />
							<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show 2" onClick="fn_report_generated(2)" />
                        </td>
                    </tr>
                    <tr class="general">
                    	<td colspan="9" align="center"> <? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
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
