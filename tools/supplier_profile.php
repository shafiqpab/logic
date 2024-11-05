<?
/*Some Details about this page-----------------------
Purpose			: 	This form will create Supplier Profile					
Functionality	:	
JS Functions	:
Created by		:	Monzu...>(Imran Babu)
Creation date 	: 	7-5-2016
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		: Created for Test Purpose
*/
session_start();
//Checking the user access
//$_SESSION['logic_erp']['user_id']="";
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:../../login.php");
//include common php function
require_once('../includes/common.php');
//Extract all request and stored into an‍ array index
extract($_REQUEST);
//checking user valid user permision
$_SESSION['page_permission']=$permission;
// load html head content and check which item are need to show or need
echo load_html_head_contents("Supplier Profile","../",1,1,$unicode,"","");
?>

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';
function fnc_supplier_details( operation )
{
	
	//alert("ok"); return;
	if (form_validation('txt_supplier_name','Supplier Name')==false)
	{
		return;
	}
	else // Save Here
	{
		//alert("ok"); return;
		//eval(get_submitted_variables('txt_supplier_name*txt_contact_person*txt_address*cbo_country_id*txt_email*txt_phone*txt_web*cbo_status*txt_remarks*update_id'));
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_supplier_name*txt_contact_person*txt_address*cbo_country_id*txt_email*txt_phone*txt_web*cbo_status*txt_remarks*update_id',"../");
		//alert(data);die();
		if($('#cbo_country_id').val()==4){
			alert('Not allowed');return;
			}
		freeze_window(operation);
		//alert(data); return;
		http.open("POST","requires/supplier_profile_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_supplier_profile_reponse;
	}
}


function fnc_supplier_profile_reponse()
{
	if(http.readyState == 4)
	{
		//alert("ok"); return;
		reponse = trim(http.responseText).split('**');
		show_msg(reponse[0]);
		//show_list_view(reponse[1],'group_details_view','group_details_view','../cost_center/requires/group_details_controller','setFilterGrid("list_view",-1)');
		show_list_view(reponse[1],'supplier_details_view','supplier_details_view','requires/supplier_profile_controller','setFilterGrid("list_view",-1)');
		reset_form('supplierdetail_1','','')
		set_button_status(0, permission,'fnc_supplier_details');
		release_freezing();
		}
}


</script>

<body onLoad="set_hotkey()">
	<div align="center" style="width:90%; position:relative;  margin-bottom:5px; margin-top:5px">
		<? echo load_freeze_divs ("../",$permission);  ?>
        <form name= ‍"supplierdetail_1" id="supplierdetail_1" autocomplete="off">
            <fieldset style="width:850px">
            <legend>Supplier Information</legend>
            <table width="100%" border="0" cellpadding="0" cellspacing="2">                
                <tr>
                    <td width="150" class="must_entry_caption">Supplier Name</td>
                    <td width="210"><input type="text" name="txt_supplier_name" id="txt_supplier_name" class="text_boxes" value="" style="width:200px;" maxlength="50" title="Maximum 50 Character" /></td>
                    <td  width="130">Contact Person</td>
                    <td><input type="text" name="txt_contact_person" id="txt_contact_person" class="text_boxes" value=""  style="width:200px;" maxlength="50" title="Maximum 50 Character"/></td>
                </tr>
                <tr>
                    <td width="130" class="must_entry_caption">Country</td>
                    <td width="210"><? echo create_drop_down( "cbo_country_id", 212, "select country_name,id from lib_country where is_deleted=0  and 
                    status_active=1 order by country_name", "id,country_name", 1, '--Select--', 0, $onchange_func  ); ?></td>
                    <td  width="130">Address</td>
                    <td><input type="text" name="txt_address" id="txt_address" class="text_boxes" value=""  style="width:200px;" maxlength="50" title="Maximum 50 Character"/></td>
                </tr>
                <tr>
                    <td width="130" class="must_entry_caption">Email</td>
                    <td width="210"><input type="text" name="txt_email" id="txt_email" class="text_boxes" value="" style="width:200px;" maxlength="50" title="Maximum 50 Character" /></td>
                    <td  width="130">Phone</td>
                    <td><input type="text" name="txt_phone" id="txt_phone" class="text_boxes" value=""  style="width:200px;" maxlength="50" title="Maximum 50 Character"/></td>
                </tr>
                <tr>
                    <td width="130" class="must_entry_caption">Website</td>
                    <td width="210"><input type="text" name="txt_web" id="txt_web" class="text_boxes" value="" style="width:200px;" maxlength="50" title="Maximum 50 Character" /></td>
                    <td  width="130">Status</td>
                    <td><? echo create_drop_down( "cbo_status", 210, $row_status,'', '', '', 1, '' ); ?></td>
                </tr>
                <tr>
                    <td width="130" class="must_entry_caption">Remarks</td>
                    <td colspan="3"><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" value="" style="width:546px;height:30px;margin-right:10px" maxlength="50" title="Maximum 50 Character" /></td> 
                                    
                </tr>
                <tr><td>&nbsp;</td></tr>
                <tr>
                    <td colspan="4" align="center" class="button_container" >
                    <? 
					 	echo load_submit_buttons( $permission,"fnc_supplier_details",0,0,"reset_form('supplierdetail_1','','')");
                       // echo load_submit_buttons( $permission, "fnc_supplier_details", 0,0 ,"reset_form('supplierdetail_1','','','','','')",1); 
                    ?> 
                    </td>
                  </tr>
                  <tr>
                    <td colspan="4"><input type="hidden" name="update_id" id="update_id"> </td>
                </tr>
                <tr>
                	<td colspan="4" style="text-align:center;font-size:25px;font-weight:bold">List of all data</td></tr>
                <tr>
                
                	<td colspan="4" id="supplier_details_view" >
					  <?						
                        $arr=array (8=>$row_status);
						echo create_list_view("list_view","Suppier Name,Contact Person,Address,Country,Email,Phone,Web,Status,Remarks", "130,80,100,100,70,70,70,90","850","auto",1, "
						select supplier_name,contact_person,address,country_id,email,phone,web,status_active,remarks,id from supplier_tbl_imran where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, 	"0,0,0,0,0,0,0,0,status_active", $arr, "supplier_name,contact_person,address,country_id,email,phone,web,status,remarks", "requires/supplier_profile_controller", 'setFilterGrid("list_view",-1);','');
                      ?>
                	</td>
                </tr>	
            </table>
        	</fieldset>
        </form>
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>