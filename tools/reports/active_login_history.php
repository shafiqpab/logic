<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Active Login History","../../", 1, 1,'','','');
 
?>
<script language="javascript">
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  	 
	var permission='<? echo $permission; ?>';
		

function fn_report_generated(operation)
{
	if (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
	{
		return;
	}
	
	else
	{
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate_login_history&operation="+operation+get_submitted_data_string('txt_date_from*txt_date_to*txt_time_from*txt_time_to',"../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/active_login_history_controller.php",true);
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
			//alert (reponse[2]);return;
				var tot_rows=reponse[2];
			$("#report_container2").html(reponse[0]);
			//$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			setFilterGrid("table_body",-1);
			release_freezing();
			show_msg('3');
		}
	}
	
	</script>
</head>
<body onLoad="set_hotkey()">
	 <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:530px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:530px;">
                <table class="rpt_table" width="95%" cellpadding="1" cellspacing="2" align="center" >
                	<thead>
                    	<tr>                   
                            <th class="must_entry_caption"> Login Date</th>
                            <th>Time Range</th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr>
                        <td align="center">
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:75px" placeholder="From Date" value="<? echo date('d-m-Y',time());?>">&nbsp; To
                      	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:75px"  placeholder="To Date" value="<? echo date('d-m-Y',time());?>" >
                        </td>
                        <td align="center">
                        <input type="text" name="txt_time_from" id="txt_time_from" class="text_boxes" style="width:100px" placeholder="From Time" value="" readonly >&nbsp; To
                      	<input type="text" name="txt_time_to" id="txt_time_to" class="text_boxes" style="width:100px"  placeholder="To Time" value="" readonly >
                        
                        
                        </td>
                   </tr>
                   <tr>
                        <td colspan="3" align="center">
                        <input type="button" id="show_button" class="formbutton" style="width:100px" value="Active" onClick="fn_report_generated(1)" />
                        <input type="button" id="show_button" class="formbutton" style="width:100px" value="All User" onClick="fn_report_generated(2)" />
                       
                        </td>
                     </tr>
                  </tbody>
                </table>
            </fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div> 
    </div>
       
</body>
<script>
	$('#txt_time_from').timepicker({timeFormat: 'hh:mm:ss tt',showSecond:true,ampm: true});  	
	$('#txt_time_to').timepicker({timeFormat: 'hh:mm:ss tt',showSecond:true,ampm: true});  	
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>