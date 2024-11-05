<?
/*-------------------------------------------- Comments---------------------------
Purpose         :   This page will take an entry of a re-process reason
Functionality   : 
JS Functions    :
Created by      :   Rezoanul
Creation date   :   19/06/23
Updated by      :     
Update date     :        
QC Performed BY :   
QC Date         : 
Comments        :
*/
// ********************************************************************************

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//-----------------------------------------------------------------------------------
echo load_html_head_contents("Re-Process Reason Entry","../../", 1, 1, "",'0','');
?>

<script>
  if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
  var permission='<? echo $permission; ?>';

  function fnc_re_process_reason_entry( operation )
  {
    if (form_validation('cbo_section_name*cbo_reason_type*txt_reason*cbo_status','Section*Reason Type*Reason*Status')==false)
    {
      return;
    }

    var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_section_name*cbo_reason_type*update_id*txt_reason*cbo_status',"../../");
    freeze_window(operation);
    http.open("POST","requires/re_process_reason_entry_controller.php",true);
    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    http.send(data);
    http.onreadystatechange = fnc_re_process_reason_entry_response;
  }
	
	function fnc_re_process_reason_entry_response()
	{
		if(http.readyState == 4) 
		{  
			var response=trim(http.responseText).split('**');
			show_msg(trim(response[0]));
      
			if(response[0]==11)
			{
				alert("Duplicate data found");
        release_freezing();
			}
			if(response[0]==0 || response[0]==1 || response[0]==2)
			{

				show_list_view('','reason_list_view','reason_list','requires/re_process_reason_entry_controller','setFilterGrid("list_view",-1)');        				
				reset_form('re_process_reason_entry_1','','');
				set_button_status(0, permission, 'fnc_re_process_reason_entry',1);
        release_freezing();
			} 
      release_freezing();

		}
	} 
  function fnResetForm()
  {
    set_button_status(0, permission, 'fnc_re_process_reason_entry',1);
  }

</script>

</head>
  <body onLoad="set_hotkey()">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <div align="center" style="width:100%;">
    
      <fieldset style="width:600px;">
        <legend>Re-Process Reason Entry Form</legend>
        <form name="re_process_reason_entry_1" id="re_process_reason_entry_1" autocomplete = "off"> 
          <table cellpadding="0" cellspacing="2" width="100%">
            <!-- section name  dropdown-->
            <tr>
              <td width="121" class="must_entry_caption">Section</td>
              <td>
                <?
                  $section_arr = array(1=>"Knitting",2=>"Dyeing", 3=>"Finishing", 4=>"Others");
                  echo create_drop_down( "cbo_section_name", 224, $section_arr,'', '1', '--- Select Section ---');
                ?>
                <input type="hidden" name="update_id" id="update_id">
              </td>                
            </tr>
            <!-- reason type  dropdown -->
            <tr>
              <td width="110" class="must_entry_caption">Reason Type</td>
              <td>
                <? 
                  $reason_type_arr = array(1=>"In-Charge",2=>"Others");
                  echo create_drop_down("cbo_reason_type", 224, $reason_type_arr, '', '1', '--Select--');
                ?>
              </td>
            </tr>
            <!-- reason text  -->
            <tr>
              <td width="121" class="must_entry_caption">Reason</td>
              <td width="375"><input style="width:212px" class="text_boxes" type="text" id="txt_reason" name="txt_reason" title="Maximum 50 Character"> 
            </tr>
            <!-- status  -->
            <tr>
              <td width="121" class="">Status</td>
              <td>
                <?php 
                  $reason_status_arr = array(1=>"Active",2=>"Inactive");
                  echo create_drop_down( "cbo_status", 224, $reason_status_arr,'', '', '', 1 ); 
                ?>
              </td>
            </tr>

            <!-- button container  -->
            <tr>
              <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                <? 
                  echo load_submit_buttons( $permission, "fnc_re_process_reason_entry", 0,0 ,"reset_form('re_process_reason_entry_1','','','');fnResetForm();");
                ?>
              </td>					
            </tr>
          </table>
        </form>
        <!-- data table  -->
        <table cellpadding="0" cellspacing="2" width="520px" align="center">
          <tr>
            <td valign="bottom" align="center"  id="reason_list">
              <?
                $arr = array(0 => $section_arr, 1 => $reason_type_arr, 3=> $reason_status_arr);
                echo  create_list_view ( "list_view", "Section Name,Reason Type, Reason, Status", "120,150,180,80","600","150",1, "SELECT id, section,reason_type, reason,status  from lib_re_process_reason_entry where is_deleted=0 order by id desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "section,reason_type,0,status", $arr , "section,reason_type,reason,status", "requires/re_process_reason_entry_controller", 'setFilterGrid("list_view",-1);' ) ;
              ?>
            </td>					
          </tr>
        </table>
        <br>
      </fieldset>
    </div>
  </body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
