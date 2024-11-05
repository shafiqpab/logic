<?php
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
//echo load_html_head_contents("Profit Center Details ","../../", 1, 1, $unicode);
echo load_html_head_contents("Profit Center Details", "../../", 1, 1,$unicode,'','');
?>

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';	
		
function fnc_profit_center( operation )
{
	if (form_validation('txt_prof_cntr_name*cbo_company','Profit Center Name*Company')==false)
	{
		return;
	}
	else
	{
		eval(get_submitted_variables('txt_prof_cntr_name*cbo_company*txt_contact_person*txt_area_address*txt_contact_no*txt_area_remark*cbo_country*cbo_status*txt_website*txt_email*update_id'));
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_prof_cntr_name*cbo_company*txt_contact_person*txt_area_address*txt_contact_no*txt_area_remark*cbo_country*cbo_status*txt_website*txt_email*update_id',"../../");
		
		 
		freeze_window(operation);
		 
		http.open("POST","requires/profit_center_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_profit_center_reponse;
	}
}

function  fnc_profit_center_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		show_list_view(reponse[1],'prof_center_list_view','prof_center_list_view','../cost_center/requires/profit_center_controller','setFilterGrid("list_view",-1)');
		reset_form('profitcenterdetail_1','','');
		set_button_status(0, permission, 'fnc_profit_center',1);
		release_freezing();
	}
}
	</script>
</head>



<body onLoad="set_hotkey()">
    <div align="center">
		<? echo load_freeze_divs ("../../", $permission );  ?>
       
        <form id="profitcenterdetail_1"  name="profitcenterdetail_1" autocomplete="off">
            <fieldset style="width:800px;">
            <legend>general information</legend>
            
            
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%">
						<table width="100%" border="0" cellpadding="0" cellspacing="2">
							<tr>
								<td width="40%" class="must_entry_caption">Profit Center Name:</td>
								<td>
									<input type="text" name="txt_prof_cntr_name" id="txt_prof_cntr_name" class="text_boxes" maxlength="64" title="Maximum 64 Character"  /></td>
							</tr>
							<tr>
								<td>Contact Person:</td>
								<td><input type="text" name="txt_contact_person" id="txt_contact_person" class="text_boxes"  maxlength="64" title="Maximum 64 Character"/></td>
							</tr>
							<tr>
								<td>Contact Number:</td>
								<td><input type="text" name="txt_contact_no" id="txt_contact_no" class="text_boxes_numeric" value="" maxlength="64" title="Maximum 50 Character" /></td>
							</tr>
							<tr>
								<td>Country:</td>
								<td>
                                    <?
								echo create_drop_down( "cbo_country", 125, "select country_name,id from lib_country where is_deleted=0  and 
								status_active=1 order by country_name", "id,country_name", 1,'--Select--', 0, ''); 
                                     ?>
								</td>
							</tr>
							<tr>
								<td>Website:</td>
								<td><input type="text" name="txt_website" id="txt_website" class="text_boxes" maxlength="40" title="Maximum 40 Character" /></td>
							</tr>
							<tr>
								<td>Email:</td>
								<td><input type="text" name="txt_email" id="txt_email" class="text_boxes"  maxlength="64" title="Maximum 50 Character" /></td>
							</tr>
						</table>
					</td>
					<td valign="top">
						<table width="100%" border="0" cellpadding="0" cellspacing="2">
							<tr>
								<td width="20%" class="must_entry_caption">Company:</td>
								<td>
                                    <? echo create_drop_down( "cbo_company", 140, "select company_name,id from lib_company comp where is_deleted=0  and 
                            status_active=1 $company_cond order by company_name", "id,company_name", 1, '--Select--', 0, $onchange_func  );
                            ?>
								</td>
							</tr>
							<tr>
								<td width="20%">Address:</td>
								<td><textarea name="address" id="txt_area_address" class="text_area" ></textarea></td>
							</tr>
							<tr>
								<td>Remark:</td>
								<td><textarea name="txt_area_remark" id="txt_area_remark" class="text_area"  ></textarea></td>
							</tr>
							<tr>
								<td>Status:</td>
								<td>
                                  <?
                                echo create_drop_down( "cbo_status", 140, $row_status,"", "", "", 1, "" );
                                  ?>  
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2"><input type="hidden" id="update_id" name="update_id"></td>
				</tr>
                <tr>
					<td colspan="2" align="center" style="padding-top:10px;" class="button_container" >
                            <? 
                                echo load_submit_buttons( $permission, "fnc_profit_center", 0,0 ,"reset_form('profitcenterdetail_1','','')",1);
                            ?>	
					</td>
				</tr>
			</table>
		</fieldset>
        </form>
        
        <fieldset style="width:800px;">
            <legend>List View</legend>
                <table>
                    <tr>
                        <td id="prof_center_list_view">
							<?
							$company_name=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
                            $arr=array (1=>$company_name);
    echo  create_list_view ( "list_view", "Profit Center Name,Company Name,Contact Person,Contact Number,Email", "130,150,200,100","800","220",0, " select  	profit_center_name,company_id,contact_person,contact_no,email,id from lib_profit_center where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,company_id,0,0,0", $arr , "profit_center_name,company_id,contact_person,contact_no,email", "../cost_center/requires/profit_center_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0' ) ;
                            ?>
                           
                        </td>
                    </tr>
                </table>
            </fieldset>
	
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>