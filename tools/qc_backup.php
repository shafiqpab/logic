<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------

//echo load_html_head_contents("QC Backup","../", 1, 1, $unicode,'','');
echo load_html_head_contents("QC Backup","../", 1, 1,'',1,'');
?>
<meta http-equiv="content-type" content="attachment;">
	<script>
        if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  	 
        var permission='<? echo $permission; ?>';

        function fnc_backup(type)
        {
           /* if (form_validation('txt_user_id*txt_passwd*txt_full_user_name*cbo_designation*txt_conf_passwd*cbo_user_level','User Name*Password*Confirm Password*User Level*Email')==false)
            {
                return;
            }
            else
            {*/
				if(type==1)
				{
                	var data="action=report_generate"+get_submitted_data_string('cbo_buyer_id*cbo_user_id*txt_date_from*txt_date_to*chk_old_data' ,"../");
				}
				else
				{
					var data="action=report_generate"+get_submitted_data_string('' ,"../");
				}
                //alert(data);return;
                freeze_window(operation);
                http.open("POST","requires/qc_backup_controller.php",true);
                http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                http.send(data);
                http.onreadystatechange = fnc_on_submit_reponse;
            //}
        }
		 
        function fnc_on_submit_reponse()
        {
            if(http.readyState == 4) 
            {
                var reponse=trim(http.responseText).split("****"); 
				//var file_name=reponse[1].replace("..","");
				$('#download_file').attr('href',reponse[1]);
				$('#download_file').attr('download',reponse[1]);
				
				//window.open(""+trim(reponse[1])+".txt","##");
				
				document.getElementById('download_file').click()
				show_msg('3');
				release_freezing();
            }
        }
		
		function fnc_chk_old_data()
		{
			if(document.getElementById('chk_old_data').checked==true)
			{
				document.getElementById('chk_old_data').value=1;
			}
			else if(document.getElementById('chk_old_data').checked==false)
			{
				document.getElementById('chk_old_data').value=2;
			}
		}
    </script>
</head>
<body onLoad="set_hotkey()">
	 <div align="center">
		 <? if($db_type==2){ echo load_freeze_divs ("../",$permission); ?>
		 <h3 style="width:750px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> <a href="" id="download_file" style="display:none"  download="FileName">Download it!</a>
         <div id="content_search_panel" style="width:750px" >    
         <fieldset style="width:750px;">
            <table class="rpt_table" width="750" cellpadding="0" cellspacing="0" align="center" rules="all" border="1">
               <thead>                    
                    <tr>
                        <th>Buyer Name</th>
                        <th>Insert User</th>
                        <th colspan="2" class="must_entry_caption">Costing Date Range</th>
                        <th style="display:none">Include Old Data</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:90px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                    <tr class="general">
                        <td width="150"><? echo create_drop_down( "cbo_buyer_id", 150, "select buy.id,buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" ); ?></td>
                        <td width="120"><? echo create_drop_down( "cbo_user_id", 120, "select id, user_name from user_passwd where valid=1 order by user_name","id,user_name", 1, "--Select User--", $selected, "","","" ); ?></td>
                        <td width="80"><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" ></td>
                        <td width="80"><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" ></td>
                        <td width="100" style="display:none"><input type="checkbox" name="chk_old_data" id="chk_old_data" onClick="fnc_chk_old_data();" value="2" ></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:90px" value="Download" onClick="fnc_backup(1)" /></td>
                    </tr>
                </tbody>
            </table>
            <table width="750">
            	<tr>
                	<td colspan="6" width="750" align="center"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table> 
            <br />
        </fieldset>
    </div>
    <? } else { echo load_freeze_divs ("../",$permission); ?>
		 <h3 style="width:150px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> <a href="" id="download_file" style="display:none"  download="FileName">Download it!</a>
         <div id="content_search_panel" style="width:150px" >    
         <fieldset style="width:150px;">
            <table class="rpt_table" width="150" cellpadding="0" cellspacing="0" align="center" rules="all" border="1">
                <tbody>
                <tr class="general">
                    <td>
                        <input type="button" id="show_button" class="formbutton" style="width:130px" value="Download" onClick="fnc_backup(2)" />
                    </td>
                </tr>
                </tbody>
            </table>
            <br />
        </fieldset>
    </div>
    <? } ?>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
 </form>    
</body>
	<? if($db_type==2){ ?><script>set_multiselect('cbo_buyer_id*cbo_user_id','0*0','0','','0*0');</script><? } ?>
	<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>    