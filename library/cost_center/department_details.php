<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Department details
Functionality	:	
JS Functions	:
Created by		:	CTO/sohel
Creation date 	: 	23-03-2013
Updated by 		: 		
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
//----------------------------------------------------------------------------------------------------------------
 echo load_html_head_contents("Location Details", "../../", 1, 1,$unicode,'','');

?>
<script type="text/javascript" charset="utf-8">

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';

function fnc_department_details( operation )
{
	if (form_validation('txt_department_name*cbo_division_id','Department Name','Division name')==false)
	{
		return;
	}
	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_department_name*cbo_division_id*txt_contact_person*txt_contact_no*cbo_country_id*txt_website*txt_email*txt_short_name*txt_address*txt_remark*cbo_status*update_id',"../../");
		freeze_window(operation);
		http.open("POST","requires/department_details_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_on_submit_reponse;
	}
}


function fnc_on_submit_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=http.responseText.split('**');
		if(reponse[0]==50)
		{
			alert("Data Insert, Update and Delete Restricted, Project is HRM Integrated");
			release_freezing();
		}
		else
		{
			show_msg(reponse[0]);
			show_list_view(reponse[1],'department_list_view','department_list_view','../cost_center/requires/department_details_controller','setFilterGrid("list_view",-1)');
			reset_form('departmentdetails_1','','');
			set_button_status(0, permission, 'fnc_department_details',1);
			release_freezing();
		}
	}
}
</script>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">  
		<? echo load_freeze_divs ("../../",$permission);  ?> 
        
         <fieldset style="width:800px; margin-top:10px;">
            <legend>Department Information</legend>
            <form id="departmentdetails_1"  name="departmentdetails_1" autocomplete="off" >  
 <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="50%">
                    <table border="0" cellpadding="0" cellspacing="2">
                    <tr>
                        <td width="250" class="must_entry_caption">Department Name</td>
                        <td>
                        <input type="text" name="txt_department_name" style="width:265px;" id="txt_department_name" class="text_boxes" value="" maxlength="64" title="Maximum 64 Character" />
                        </td>
                   </tr>
                    <tr>
                        <td width="250" class="must_entry_caption">Division</td>
                        <td>
						<?
                            //$onchange_func = "fn_check_company_division()";
						 if($db_type==2) $div_com="a.division_name || '-' || b.company_short_name as division_name" ;
						 else $div_com="concat(a.division_name,'-',b.company_short_name) as division_name" ;
						 
						echo create_drop_down( "cbo_division_id", 280, "select $div_com,a.id from lib_division a, lib_company b where a.company_id=b.id and a.is_deleted=0  and  a.status_active=1", "id,division_name", 1, '--Select--', 0, $onchange_func  );
                        ?>
                        </td>
                   </tr>
                   <tr>
                        <td width="250">Contact Person</td>
                        <td>
                        <input type="text" name="txt_contact_person" style="width:265px;" id="txt_contact_person" class="text_boxes" maxlength="64" title="Maximum 64 Character" />
                        </td>
                  </tr>
                   <tr>
                        <td width="250">Contact Number</td>
                        <td>
                        <input type="text" name="txt_contact_no" id="txt_contact_no"  style="width:265px;" class="text_boxes_numeric" value="" maxlength="64" title="Maximum 64 Character" />
                        </td>
                  </tr>
                   <tr>
                        <td width="250">Country</td>
                        <td>
                        <? 
						echo create_drop_down( "cbo_country_id", 278, "select country_name,id from lib_country where is_deleted=0  and 
                        status_active=1 order by country_name", "id,country_name", 1, '--Select--', 0, $onchange_func  ); 
						?>
                        </td>
                  </tr>
                  <tr>
                        <td width="250">Website</td>
                        <td>
                        <input type="text" name="txt_website" style="width:265px;" id="txt_website" class="text_boxes" value="" maxlength="64" title="Maximum 64 Character" />
                        </td>
                  </tr>
                  <tr>
                        <td width="250">Email</td>
                        <td>
                        <input type="text" name="txt_email"  style="width:265px;" id="txt_email" class="text_boxes" value="" maxlength="32" title="Maximum 32 Character"  />
                        </td>
                  </tr>
            </table>
                        </td>
                        <td width="50%" valign="top">
           <table width="100" border="0" cellpadding="0" cellspacing="2">
                   <tr>
                        <td width="250">Short Name</td>
                        <td>
						<input type="text" name="txt_short_name" style="width:265px;" id="txt_short_name" class="text_boxes" maxlength="5" title="Maximum 5 Character"  />
                        </td>
                   </tr>
                   <tr>
                        <td width="250">Address</td>
                        <td>
                        <textarea name="txt_address" id="txt_address" class="text_area" style="resize:none; width:265px; height:40px;" maxlength="500" title="Maximum 500 Character" ></textarea>
                        </td>
                   </tr>
                   <tr>
                        <td width="250">Remark</td>
                        <td>
                        <textarea name="txt_remark" id="txt_remark" class="text_area"   style="resize:none; width:265px; height:40px;" maxlength="500" title="Maximum 500 Character" ></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td width="250">Status</td>
                        <td>
                        <? 
						echo create_drop_down( "cbo_status", 280, $row_status,'', $is_select, $select_text, 1, $onchange_func ); 
						?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td> 
                        <input type="hidden" id="update_id" name="update_id">
                        </td>
                   </tr>
           </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center" style="padding-top:10px;" class="button_container" >
						<? 
                        echo load_submit_buttons( $permission, "fnc_department_details", 0,0 ,"reset_form('departmentdetails_1','','')",1);
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center" id="department_list_view" >
                        <?
						if($db_type==2) $div_com="a.division_name || '-' || b.company_short_name as division_name" ;
						 else $div_com="concat(a.division_name,'-',b.company_short_name) as division_name" ;
						 $lib_division_arr=return_library_array( "select $div_com,a.id from lib_division a,lib_company b where a.company_id=b.id", "id","division_name"  );
                		//$lib_division_arr=return_library_array( "select division_name,id from lib_division", "id","division_name"  );
						$arr=array (1=>$lib_division_arr);
    					echo  create_list_view ( "list_view", "Department Name,Division,Contact Person,Contact Number,Email.", "150,150,150,150","800","200",0, "select  department_name,division_id,contact_person,contact_no,email,id from lib_department where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,division_id,0,0,0", $arr , "department_name,division_id,contact_person,contact_no,email", "../cost_center/requires/department_details_controller", 'setFilterGrid("list_view",-1);' ) ;
						?>
                        </td>
                    </tr>
          </table>
       		</form>
       		</fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>