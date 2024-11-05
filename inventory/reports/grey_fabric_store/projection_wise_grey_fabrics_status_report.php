<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Projection Wise Grey Fabrics Status Report.
Functionality	:	
JS Functions	:
Created by		:	Md Mahbubur Rahman
Creation date 	: 	15-10-2019
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
echo load_html_head_contents("Projection Wise Grey Fabrics Status Report", "../../../", 1, 1,'',1,1);
?>	

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";  
	var permission = '<? echo $permission; ?>';
	function fn_report_generated(type)
	{
		var buyer_name = $('#cbo_buyer_name').val();
		//alert(buyer_name);
		
		if(buyer_name!=0)
		{
			if (form_validation('cbo_company_name','Comapny Name')==false)
			{
				return;
			}
		}
		else
		{
			if (form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*date_from*date_to')==false)
			{
				return;
			}
		}
		
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_year*cbo_buyer_name*txt_date_from*txt_date_to*txt_job_no*txt_style_no',"../../../")+'&type='+type;
			
			freeze_window(3);
			http.open("POST","requires/projection_wise_grey_fabrics_status_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		
	}

	function fn_report_generated_reponse()
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
		 
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide(); 
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
		$('#table_body tr:first').show();
	}

 </script>
 </head>
 
<body onLoad="set_hotkey();">
<form id="fabricReceiveStatusReport_1">
 <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../../",'');  ?>
         <h3 style="width:900px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
         <fieldset style="width:900px;">
          <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                	<th class="must_entry_caption" width="120">Company Name</th>
                    <th>Buyer Name</th>
                    <th width="60">Year</th>
                    <th width="120">Job No</th>
                    <th width="120">Style</th>
                    <th class="must_entry_caption" colspan="2" width="140">Order Entry Date Range</th>
                    <th width="100"><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('fabricReceiveStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                </thead>
                <tbody>
                    <tr>
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/projection_wise_grey_fabrics_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:120px" /></td>
                        <td><input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:120px" /></td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:90px" placeholder="From Date" readonly>
                        </td>
                        <td>
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:90px"  placeholder="To Date" readonly>
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Projection  Wise" onClick="fn_report_generated(3)" />
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="10" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
         </fieldset>
         </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
 </form>   
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    