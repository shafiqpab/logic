<?
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
 
function fnc_location_info( operation )
{
	if (form_validation('txt_location_name*cbo_company_id','Location Name','Company')==false)
	{
		return;
	}
	else // Save Here
	{
		eval(get_submitted_variables('txt_location_name*txt_contact_person*txt_contact_no*cbo_country_id*txt_website*txt_email*cbo_company_id*txt_address*txt_remark*cbo_status*update_id'));
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_location_name*txt_contact_person*txt_contact_no*cbo_country_id*txt_website*txt_email*cbo_company_id*txt_address*txt_remark*cbo_status*update_id',"../../");
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/location_details_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_on_submit_reponse;
	}
}

function fnc_on_submit_reponse()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText)
		var reponse=trim(http.responseText).split('**');
		show_msg(trim(reponse[0]));
		show_list_view(reponse[1],'location_list_view','location_list_view','../cost_center/requires/location_details_controller','setFilterGrid("list_view",-1)');
		reset_form('locationdetailsform_1','','');
		set_button_status(0, permission, 'fnc_location_info',1);
		release_freezing();
	}
}
</script>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">  
		<? echo load_freeze_divs ("../../",$permission);  ?>   
        
        <fieldset style="width:800px; margin-top:10px;">
            <legend>Location Information</legend>
            <form id="locationdetailsform_1"  name=""action="locationdetailsform_1" >
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="60%">
                        <table border="0" cellpadding="0" cellspacing="2">
                        <tr>
                        <td width="150" class="must_entry_caption">Location Name</td>
                        <td><input type="text" name="txt_location_name" style="width:265px;" id="txt_location_name" class="text_boxes" value="" width="400" maxlength="120" title="Maximum 120 Character" />
                        
                        </td>
                        </tr>
                        
                        <tr>
                        <td>Contact Person</td>
                        <td><input type="text" name="txt_contact_person" style="width:265px;" id="txt_contact_person" class="text_boxes" maxlength="64" title="Maximum 64 Character"  /></td>
                        </tr>
                        <tr>
                        <td>Contact Number</td>
                        <td  width="384"><input type="text" name="txt_contact_no" id="txt_contact_no"  style="width:265px;" class="text_boxes_numeric" value="" maxlength="64" title="Maximum 64 Character" /></td>
                        </tr>
                        <tr>
                        <td>Country</td>
                        <td>
                        <? echo create_drop_down( "cbo_country_id", 278, "select country_name,id from lib_country where is_deleted=0  and 
                        status_active=1 order by country_name", "id,country_name", 1, '--Select--', 0, $onchange_func  ); ?>
                        </td>
                        </tr>
                        <tr>
                        <td>Website</td>
                        <td><input type="text" name="txt_website" style="width:265px;" id="txt_website" class="text_boxes" value="" maxlength="64" title="Maximum 64 Character" /></td>
                        </tr>
                        <tr>
                        <td>Email</td>
                        <td><input type="text" name="txt_email"  style="width:265px;" id="txt_email" class="text_boxes" value="" maxlength="32" title="Maximum 32 Character"  /></td>
                        </tr>
                        </table>
                        </td>
                        <td width="40%" valign="top">
                        <table width="100" border="0" cellpadding="0" cellspacing="2">
                        <tr>
                        <td width="14%" class="must_entry_caption">Company</td>
                        <td width="86%">
							<? echo create_drop_down( "cbo_company_id", 280, "select company_name,id from lib_company comp where is_deleted=0  and 
                            status_active=1 $company_cond order by company_name", "id,company_name", 1, '--Select--', 0, $onchange_func  );
                            ?>
                        </td>
                        </tr>
                        
                        <tr>
                        <td width="14%">Address</td>
                        <td><textarea name="txt_address" id="txt_address" class="text_area" style="resize:none; width:265px; height:40px;" maxlength="500" title="Maximum 500 Character" ></textarea></td>
                        </tr>
                        <tr>
                        <td>Remark</td>
                        <td><textarea name="txt_remark" id="txt_remark" class="text_area"   style="resize:none; width:265px; height:40px;" maxlength="500" title="Maximum 500 Character" ></textarea></td>
                        </tr>
                        <tr>
                        <td>Status</td>
                        <td>
                        <? echo create_drop_down( "cbo_status", 280, $row_status,'', $is_select, $select_text, 1, $onchange_func ); ?>
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
                                echo load_submit_buttons( $permission, "fnc_location_info", 0,0 ,"reset_form('locationdetailsform_1','','')",1);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center" id="location_list_view" >
							<? 
							$lib_company_arr=return_library_array( "select id,company_name from lib_company where status_active=1 and is_deleted=0", "id","company_name" );
                            $arr=array (1=>$lib_company_arr);
       						echo create_list_view ( "list_view", "Location Name,Company Name,Contact Person,Contact Number,Email.", "200,150,150,150","900","200",0, "select  location_name,company_id,contact_person,contact_no,email,id from lib_location where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,company_id,0,0,0", $arr , "location_name,company_id,contact_person,contact_no,email", "../cost_center/requires/location_details_controller", 'setFilterGrid("list_view",-1);' ) ;
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
