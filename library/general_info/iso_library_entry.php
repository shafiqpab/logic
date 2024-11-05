<?
/*-------------------------------------------- Comments

Purpose			:   This form will create Iso library.

Functionality	: 

JS Functions	:

Created by		: 	Jahid
Creation date	:   15-05-2023
Updated by		:     
Update date		:        

QC Performed BY	:   

QC Date			: 

Comments		:

*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("create Iso library", "../../", 1, 1,$unicode,'','');

?>

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>'; 
    
function fnc_store_location( operation )
{
	if(operation==2)
	{
		show_msg(13);alert("Delete Restricted");return;
	}
	
	if(form_validation('cbo_company_id*cbo_module_name*cbo_menu_name*txt_iso_no','Company*Module*Menu*Iso No')==false)
	{
		return;
	}
	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_module_name*cbo_menu_name*txt_iso_no*cbo_status*update_id',"../../");
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/iso_library_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_store_location_response;
	}
}

function fnc_store_location_response()
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split('**');
		if(response[0]==11)
		{
			show_msg(response[0]);
			alert("Duplicate restricted, This ISO No Already Exist.");
			release_freezing();
			return;
		}
		
		show_msg(response[0]);
		show_list_view(response[1],'iso_list_view','iso_list_view','requires/iso_library_entry_controller','setFilterGrid("list_view",-1)');
		reset_form('storelocation_1','','');
		disable_enable_fields('cbo_company_id*cbo_module_name*cbo_menu_name',0);
		set_button_status(0, permission, 'fnc_store_location',1);
		release_freezing();
	}
}
  </script>

</head>

<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center" style="width:100%;">
  
        <fieldset style="width:900px;">
        <legend>ISO Library Entry</legend>
            <form name="storelocation_1" id="storelocation_1" autocomplete = "off"> 
              <table cellpadding="0" cellspacing="0" width="100%" border="2" rules="all">
              	<thead>
                	<tr>
                    	<th width="200" class="must_entry_caption">Company Name</th>
                        <th width="200" class="must_entry_caption">Module Name</th>
                        <th width="220" class="must_entry_caption">Page Name</th>
                        <th width="160" class="must_entry_caption">ISO No</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                	<tr>
                    	<td align="center">
                        <? 
							echo create_drop_down( "cbo_company_id", 182, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "show_list_view(this.value,'report_settings','list_view_report_settings','requires/iso_library_entry_controller','');" );
						?>
                        </td>
                        <td align="center">
                        <?
							echo create_drop_down( "cbo_module_name", 182, "select m_mod_id, main_module from main_module where status=1 order by main_module",'m_mod_id,main_module', 1, '--- Select Module ---', 0, "load_drop_down( 'requires/iso_library_entry_controller', this.value, 'load_drop_down_report_module', 'report_name_td' );"  );
						?>
                        </td>
                        <td align="center" id="report_name_td">
                        <?
							echo create_drop_down( "cbo_menu_name", 212, $blank_arr,'', 1, '--- Select Page ---', 0, ""  );
						?>
                        </td>
                        <td align="center"><input type="text" name="txt_iso_no" id="txt_iso_no" class="text_boxes" style="width:112px"  maxlength="50" title="Maximum 50 Character"/></td>
                        <td align="center">
                        <?
							echo create_drop_down( "cbo_status", 124, $row_status,"", "", "", 1, "" );
						?>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                	<tr><td colspan="5">&nbsp;</td></tr>
                    <tr>
                    	<td align="center" colspan="5" class="button_container">
                        <?
							echo load_submit_buttons( $permission, "fnc_store_location", 0,0 ,"reset_form('storelocation_1','','')",1);
						?>
                        <input type="hidden" id="update_id" name="update_id" />
                        </td>
                    </tr>
                </tfoot>
                	
              </table>
            </form>
          <fieldset style="margin-top:20px; width:900px;">
            <legend>ISO List View</legend>
              <table cellpadding="0" cellspacing="2" width="100%" border="1" rules="all">          
                  <tr>
                      <td align="center" id="iso_list_view">      
                         <?
                        $companyarr=return_library_array( "select id, company_name from lib_company",'id','company_name');
						$moduleArr=return_library_array( "select m_mod_id, main_module from main_module where status=1",'m_mod_id','main_module');
						$menuArr=return_library_array( "SELECT m_menu_id, menu_name FROM main_menu where status=1 and is_mobile_menu not in (1) and f_location is not null",'m_menu_id','menu_name');
						$arr=array (0=>$companyarr,1=>$moduleArr,2=>$menuArr,4=>$row_status);
						echo  create_list_view ( "list_view", "Company Name,Module Name,Page Name,ISO No,Status", "170,170,250,120","880","220",0, "select id, company_id, module_id, menu_id, iso_no, status_active from lib_iso where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company_id,module_id,menu_id,0,status_active", $arr , "company_id,module_id,menu_id,iso_no,status_active", "requires/iso_library_entry_controller", 'setFilterGrid("list_view",-1);' ) ; 
						 
                         ?>
                      </td>
                  </tr>            
              </table>
        	</fieldset>
        </fieldset>
        </div>
  </body>
    
<!--<script>set_multiselect('cbo_catagory_item','0','0','','');</script>-->
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
