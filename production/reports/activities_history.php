<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------

//echo load_html_head_contents("Activities History","../../", $filter, 1, $unicode,'','');
echo load_html_head_contents("Activities History", "../../", 1, 1,'','','');
 
?>
<script language="javascript">
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  	 
	var permission='<? echo $permission; ?>';
		

	function fn_report_generated(operation)
	{
		//alert($('#txt_date_from').val()); return;
		
		if (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
		{
			return;
		}
		
		else
		{
			
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate_login_history"+get_submitted_data_string('txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/activities_history_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;  
		}
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			//alert(reponse[1]);
			//document.getElementById('report_container').innerHTML='<a href="requires/tmp_report_file/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
	</script>
</head>
<body onLoad="set_hotkey()">
	 <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:750px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:750px;">
                <table class="rpt_table" width="350" cellpadding="1" cellspacing="2" align="center" >
                	<thead>
                    	<tr>                   
                            <th width="200" class="must_entry_caption"> Date Range</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr>
                        <td>
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:75px" placeholder="From Date" >&nbsp; To
                      	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:75px"  placeholder="To Date" >
                        </td>
                        <td align="center">
                        <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated(0)" />
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
            </fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div> 
    </div>
       
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>