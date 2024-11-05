<?
/*-------------------------------------------- Comments----------------
Purpose			: 	This form will create Order Future Prediction Report
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	12/10/2014
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
-----------------------------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Future Prediction Info","../../../", 1, 1, $unicode,'',1);
?>	

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	
	function fn_report_generated()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*txt_date_from*txt_date_to*cbo_year*txt_job_no*txt_ref_no*txt_file_no',"../../../")+'&report_title='+report_title;;
		freeze_window(3);
		http.open("POST","requires/order_future_prediction_controller.php",true);
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
			//alert(reponse[0]);
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
	 		show_msg('3');
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="340px";
	}
	

	function generate_ex_factory_popup(action,job,id,width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_future_prediction_controller.php?action='+action+'&job='+job+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}	
</script>
</head>
<body>
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../");  ?>
   <h3 align="left"  id="accordion_h1" class="accordion_h" style="width:1220px" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div id="content_search_panel" style="width:1120"> 
            <form>
                <fieldset style="width:1200px;">
                    <div  style="width:100%" align="center">
                            <table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                    <tr>
                                        <th width="150" class="must_entry_caption">Company</th>
                                        <th width="150">Buyer</th>
                                        <th width="60">Job Year</th>
                                        <th width="100">Job No</th>
                                        
                                        <th width="100">Ref No</th>
                                        <th width="100">File No</th>
                                          
                                        <th width="130">Team</th>
                                        <th width="150">Team Member</th>
                                        <th width="170" >Date</th>
                                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('','report_container*report_container2','','','')" /></th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td align="center">
                                           <?
                                           echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " load_drop_down( '../merchandising_report/requires/order_future_prediction_controller', this.value, 'load_drop_down_buyer', 'buyer_td' )" );
                                            ?> 
                                    </td>
                                    <td id="buyer_td" align="center">
                                     <? 
                                        echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                                     ?>	
                                    </td>
                                    <td align="center">
									<? 
										$year_current=date("Y");
										echo create_drop_down( "cbo_year", 80, $year,"", 1, "--Select Year--", $year_current, "" );
                                    ?> 
                                    </td>
                                    <td align="center">
                            		<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:85px;" />
                                    </td>
                                    
                                    
                                    <td align="center">
                            		<input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:85px;" placeholder="Write" />
                                    </td>
                                    
                                    <td align="center">
                            		<input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:85px;" placeholder="Write"/>
                                    </td>
                                    
                                    
                                    <td align="center">                
                                    
                                    <?
                                           echo create_drop_down( "cbo_team_name", 130, "select id,team_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_name","id,team_name", 1, "-- Select Team --", $selected, " load_drop_down( '../merchandising_report/requires/order_future_prediction_controller', this.value, 'load_drop_down_team_member', 'team_td' )" );
                                            ?>
                                    </td>
                                    <td id="team_td" align="center">
                                    <div id="div_team">
                                    <? 
                                        echo create_drop_down( "cbo_team_member", 150, $blank_array,"", 1, "- Select Team Member- ", $selected, "" );
                                     ?>	
                                    </div>
                                    </td>
                                    <td align="center">
                                    <input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:60px">To
                                    <input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:60px">
                                    </td>
                                    <td>
                                    <input type="button" name="search" id="search" value="Show" onClick="fn_report_generated()" style="width:70px" class="formbutton" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="9" align="center">
                                        <? echo load_month_buttons(1); ?>
                                    </td>
                                </tr>
                            </table>
                    </div>
                </fieldset>
            </form>
        </div>
       <div id="report_container" align="center"></div>
       <div id="report_container2"> 
       
        </div>
    </div>
    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>