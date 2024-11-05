<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Daily Task Report","../../", 1, 1,'','','');
 
?>
<script language="javascript">
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  	 
	var permission='<? echo $permission; ?>';
		

function fn_report_generated(operation)
{
	if($("#cbo_user_id").val()==0 && $("#txt_date_from").val()=='' && $("#txt_date_to").val()==''){
		if (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
		{
			return;
		}
	}
	else
	{
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_user_id*txt_issue_no*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title+"&type="+operation;
		freeze_window(3);
		http.open("POST","requires/daily_task_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;  
	}
}

function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**");
			var tot_rows=reponse[2];
			$("#report_container2").html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';



			release_freezing();
			show_msg('3');
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
	'<html><head><link rel="stylesheet" href="../../../css/style_print.css" type="text/css"  /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflow="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="350px";
		
		$("#table_body tr:first").show();
	}
	
	</script>
</head>
<body onLoad="set_hotkey()">
	 <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:800px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:800px;">
                <table class="rpt_table" cellpadding="1" cellspacing="2" align="center" >
                	<thead>
                    	<tr>                   
                            <th class="must_entry_caption">User Name</th>
                            <th>Issue NO</th>
                            <th class="must_entry_caption">Task Date</th>
                            <th colspan="2"><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr>
                        <td> 
                            <?
                              echo create_drop_down( "cbo_user_id", 200,"select id,user_name from user_passwd where valid=1  order by user_name","id,user_name",1, "--Select--", "","",0 );
                            ?>
                        </td>
                        <td>
                        <input type="text" style="width:120px" class="text_boxes"  name="txt_issue_no" id="txt_issue_no" />
                   		</td>
                        <td>
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:75px" placeholder="From Date" >&nbsp; To
                      	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:75px"  placeholder="To Date" >
                        </td>
                        <td align="center">
                        <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated(0)" />
                        </td>
                   </tr>
                    <tr>
                        <td colspan="5">
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </table> 
            </fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div> 
    </div>
       
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>