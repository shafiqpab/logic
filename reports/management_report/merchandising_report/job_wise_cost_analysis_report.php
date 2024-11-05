<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Cost Breakdown Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam 
Creation date 	: 	10-02-2020
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
echo load_html_head_contents("Job Wise Cost Analysis Report","../../../", 1, 1, $unicode,1,1);
?>
<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	
	function fn_report_generated(type)
	{
		if(form_validation('txt_job_no','Job No')==false)
		{
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			if(type==1){
				var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job_no',"../../../")+'&report_title='+report_title;	
			}
			if(type==2){
				var data="action=report_generate_show2"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job_no',"../../../")+'&report_title='+report_title;
			}

			freeze_window(3);
			http.open("POST","requires/job_wise_cost_analysis_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>'; 



	 		show_msg('3');
			release_freezing();
		}
	}
	
	function openmypage_job()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else 
		{	
			var data = $("#cbo_company_name").val()+"_"+$("#cbo_buyer_name").val()+"_"+$("#cbo_year").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/job_wise_cost_analysis_report_controller.php?data='+data+'&action=job_no_popup', 'Job No Search', 'width=700px,height=420px,center=1,resize=0,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theemailjob=this.contentDoc.getElementById("txt_job_no").value;
				if ( theemailjob!="" )
				{
					freeze_window(5);
					$("#txt_job_no").val(theemailjob);
					release_freezing();
				}
			}
		}
	}






	function new_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_print.css" type="text/css"  /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="380px";
		 
	}


</script>

</head>
<body onLoad="set_hotkey();">
<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:700px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:700px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th class="must_entry_caption">Company Name</th>
                            <th>Buyer Name</th>
                            <th>Job Year</th>
                            <th>Job No.</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/job_wise_cost_analysis_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data( this.value, 'company_wise_report_button_setting','requires/job_wise_cost_analysis_report_controller' );" ); ?></td>
                            <td id="buyer_td"> <? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                            <td><? echo create_drop_down( "cbo_year", 70, create_year_array(),"", 1,"-- All --", $selected, "",0,"" ); ?></td>
                            <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:150px" placeholder="Browse" onDblClick="openmypage_job();" readonly /></td>
                            <td>
								<input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated(1)" />
								<input type="button" name="show2" id="show2" class="formbutton" style="width:60px;display:none;" value="Show 2" onClick="fn_report_generated(2)" />
							</td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    <div style="display:none" id="data_panel"></div>  
 </form>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
