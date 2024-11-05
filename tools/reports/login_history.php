<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Login History","../../", 1, 1,'','','');
 
?>
<script language="javascript">
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  	 
	var permission='<? echo $permission; ?>';
		

function fn_report_generated(operation)
{
	if (form_validation('cbo_user_name*txt_date_from*txt_date_to','User Name*From Date*To Date')==false)
	{
		return;
	}
	
	else
	{
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate_login_history"+get_submitted_data_string('cbo_user_name*cbo_search*search_value*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title+"&type="+operation;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/login_history_controller.php",true);
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
         <h3 align="left" id="accordion_h1" style="width:1000px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1000px;">
                <table class="rpt_table" width="980" cellpadding="1" cellspacing="2" align="center" >
                	<thead>
                    	<tr>                   
                            <th width="220" class="must_entry_caption">User Name</th>
                            <th width="200">Search By</th>
                            <th width="100">Search Value</th>
                            <th width="220" class="must_entry_caption"> Login Date</th>
                            <th colspan="2" width="220"><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr>
                        <td> 
                            <?
                              echo create_drop_down( "cbo_user_name", 220,"select id,user_name from user_passwd where valid=1  order by user_name","id,user_name",1, "--Select--", "","",0 );
                            ?>
                        </td>
                        <td> 
                            <? 
							$search_by=array(0=>"All Data",1=>"LAN IP",2=>"MAC Address",3=>"WAN IP");
					  		echo create_drop_down( "cbo_search", 200, $search_by,"", 0, "", 0, "",0 );
					   		?>
                        </td>
                        <td>
                        <input type="text" style="width:100px" class="text_boxes"  name="search_value" id="search_value" />
                   		</td>
                        <td>
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:75px" placeholder="From Date" >&nbsp; To
                      	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:75px"  placeholder="To Date" >
                        </td>
                        <td>
                        <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated(0)" />
                        
                        <td>
                            <input type="button" id="show2_button" class="formbutton" style="width:100px" value="Show 2" onClick="fn_report_generated(1)" />
                        </td>
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