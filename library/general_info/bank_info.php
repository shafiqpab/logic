<?

/*--- ----------------------------------------- Comments

Purpose			: 	This form will create Bank library 
					here 2 form is available where 1 is creating Bank info 
					and 2nd is creating Bank Accounts ni belongs to the bank.
					
Functionality	:	First create Bank info and save then add multiple members accounts one by one.
					select a bank from list view for update.

JS Functions	:

Created by		:	All 
Creation date 	: 	04-10-2012
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
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Bank Info", "../../", 1, 1,$unicode,'','');

?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

var permission='<? echo $permission; ?>';

function fnc_bank_entry( operation )
{
    if ( form_validation('txt_bank_name*txt_branch_name*txt_bank_code','Bank Name*Branch Name*Bank Code')==false )
	{
		return;
	}
	
	else
	{
		eval(get_submitted_variables('txt_bank_name*txt_branch_name*txt_bank_code*txt_bank_address*txt_bank_email*txt_bank_website*txt_bank_contact_person*txt_bank_phone_no*txt_swift_code*cb_lien_bank*cb_issuing_bank*cb_salary_bank*cb_advs_bank*txt_remarks*update_id*cbo_designation*cbo_ac_type*cbo_cheque_template'));
		
	if (document.getElementById('cb_lien_bank').checked==true)
	{
		cb_lien_bank=1;
	}
	else
	{	
		cb_lien_bank=0;
	}
	if (document.getElementById('cb_issuing_bank').checked==true)
	{
		cb_issuing_bank=1;
	}
	else
	{
		cb_issuing_bank=0;	
	}

	if (document.getElementById('cb_salary_bank').checked==true)
	{
		cb_salary_bank=1;
	}
	else
	{
		cb_salary_bank=0;
	}

	if (document.getElementById('cb_advs_bank').checked==true)
	{
		cb_advs_bank=1;
	}
	else
	{
		cb_advs_bank=0;
	}
		var data="action=save_update_delete&operation="+operation+"&cb_lien_bank="+cb_lien_bank+"&cb_issuing_bank="+cb_issuing_bank+"&cb_salary_bank="+cb_salary_bank+"&cb_advs_bank="+cb_advs_bank+get_submitted_data_string('txt_bank_name*txt_branch_name*txt_bank_code*txt_bank_address*txt_bank_email*txt_bank_website*txt_bank_contact_person*txt_bank_phone_no*txt_swift_code*txt_remarks*update_id*cbo_designation*cbo_ac_type*cbo_cheque_template*txt_bank_short_name',"../../");
		
		freeze_window(operation);
		http.open("POST","requires/bank_info_controller.php", true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_bank_info_reponse;
	}
}

function fnc_bank_info_reponse()
{
	if(http.readyState == 4) 
	{
		// release_freezing();return;
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		document.getElementById('update_id').value  = reponse[2];
		show_list_view('','bank_info_list_view','bank_info_list','../general_info/requires/bank_info_controller','setFilterGrid("list_view",-1)');
		set_button_status(1, permission, 'fnc_bank_info',1);
		release_freezing();
	}
}

function fnc_bank_acc_entry( operation )
{
	if (form_validation('cbo_account_type*cbo_currency*update_id','Account Type*Currency*')==false)
	{
		return;
	}
	else
	{
		eval(get_submitted_variables('cbo_account_type*txt_account_no*cbo_currency*txt_loan_limit*cbo_loan_type*cbo_company_name*cbo_status*update_id_dtl*update_id'));
		var data="action=save_update_delete_dtl&operation="+operation+get_submitted_data_string('cbo_account_type*txt_account_no*cbo_currency*txt_loan_limit*cbo_loan_type*cbo_company_name*cbo_status*update_id_dtl*update_id',"../../");
		freeze_window(operation);
		http.open("POST","requires/bank_info_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_bank_acc_entry_response;
	}
}

function fnc_bank_acc_entry_response()
{
	if(http.readyState == 4) 
	{
		
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		reset_form('bankaccinfo_2','','');
		
		show_list_view(document.getElementById('update_id').value, 'account_list_view', 'account_list_view', '../general_info/requires/bank_info_controller', 'setFilterGrid(\'list_view1\',-1)');
		
		show_list_view('','bank_info_list_view','bank_info_list','../general_info/requires/bank_info_controller','setFilterGrid("list_view",-1)');
		set_button_status(0, permission, 'fnc_bank_acc_entry',2);
		release_freezing();
		
	}
}

function fn_distribution()
{
	var update_id = $('#update_id').val();
	if(update_id=="")
	{
		alert("Save Bank Data First");
		return;
	}
	
	var title = 'Com. Proceed Distribution %';   
	var page_link = 'requires/bank_info_controller.php?update_id='+update_id+'&action=comm_distribution';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=450px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		//var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		//var information=this.contentDoc.getElementById("information").value;     //Access form field with id="emailfield"
	   // $('#information').val(information);
	}
}

</script>

</head>
<body onLoad="set_hotkey()">
        <div align="center" style="width:100%;">
				<? echo load_freeze_divs ("../../",$permission);  ?>
               
            <fieldset style="width:850px;">
            <legend>Bank Info </legend>
                <table cellpadding="0" cellspacing="2" width="100%">					
					<tr>
						<td colspan="6" height="10">
                            <form name="bankinfo_1" id="bankinfo_1"  autocomplete="off">
                            
                        <table width="100%">
                            <tr>
                                <td width="130" class="must_entry_caption">Bank Name</td>
                                <td width="170"> <!-- System Generated -->
                                    <input name="txt_bank_name" id="txt_bank_name" class="text_boxes" style="width:165px" maxlength="100" title="Maximum 100 Character" />
                                </td>
                                <td width="130" class="must_entry_caption">Branch Name</td>
                                <td width="170"> <!-- Selected & Add New-->
                                    <input name="txt_branch_name" id="txt_branch_name" class="text_boxes" style="width:165px" maxlength="100" title="Maximum 100 Character">							
                                </td>
                                <td width="130" class="must_entry_caption">Bank Code</td>
                                <td> <!-- Selected -->
                                    <input name="txt_bank_code" id="txt_bank_code" class="text_boxes" style="width:165px" maxlength="50" title="Maximum 50 Character" />
                                </td>
                            </tr>
                            <tr>
                                <td width="130" >Address</td>
                                <td colspan="3"> <!-- Display -->
                                    <input name="txt_bank_address" id="txt_bank_address" class="text_boxes" style="width:472px" maxlength="500" title="Maximum 500 Character" />						  
                                </td>
                                <td width="130" >Email</td>
                                <td > <!-- Display -->
                                    <input name="txt_bank_email" id="txt_bank_email" class="text_boxes" style="width:165px" maxlength="100" title="Maximum 100 Character">							
                                </td>
                            </tr>
                            <tr>
                                <td width="130" >Web Site</td>
                                <td width="170" > 
                                    <input name="txt_bank_website" id="txt_bank_website" class="text_boxes" style="width:165px" maxlength="30" title="Maximum 30 Character">							
                                </td>
                                <td width="130" >Contact Person	</td>
                                <td width="170"> 
                                    <input name="txt_bank_contact_person" id="txt_bank_contact_person" class="text_boxes"style="width:165px" maxlength="100" title="Maximum 100 Character" >						
                                </td>
                                <td width="130">Designation</td>
                                <td><? echo create_drop_down( "cbo_designation", 170, "select id,custom_designation from lib_designation where status_active=1 and is_deleted=0 order by custom_designation","id,custom_designation", 1, "-- Select Designation--", $selected ); ?>
                                </td>
                                
                            </tr>
                             <tr>
                                <td width="130" >Phone No</td>
                                <td>
                                    <input name="txt_bank_phone_no" id="txt_bank_phone_no" class="text_boxes"style="width:165px" maxlength="100" title="Maximum 100 Character" >                            
                                </td>
                                <td width="130" >Swift Code</td>
                                <td width="170" > 
                                    <input name="txt_swift_code" id="txt_swift_code" class="text_boxes" style="width:165px" maxlength="30" title="Maximum 30 Character">
                                </td>                                   		
                                </td>
                                <td width="130">Account Type</td>
                                <td>
                                <?
								$ac_type_arr=array(1=>"All", 2=>"Deductions at Source", 3=>"Distributions at Source"); 
								echo create_drop_down( "cbo_ac_type", 170, $ac_type_arr,"", 1, "-- Select", $selected ); 
								?>
                                </td>
                            </tr>
                            <tr>
                            	<td width="130" height="30" >Cheque Template</td>
                                <td>
									<?php
									$bank_cheque= array(1=>"Default",2=>"Southeast Bank Limited",3=>"Exim Bank");
                                    echo create_drop_down( "cbo_cheque_template", 175, $bank_cheque,"", "", "", 1, "" );
                                    ?>	
                                 </td>
                                <td width="130" height="30" > <!-- System Generated -->
                                    Bank Type
                                </td>
                                <td colspan="2">
                                        <input type='checkbox' name="cb_lien_bank" id="cb_lien_bank">&nbsp Lien Bank&nbsp;&nbsp;
                                        <input type='checkbox' name="cb_issuing_bank" id="cb_issuing_bank">&nbsp Issuing Bank&nbsp;&nbsp;
                                        <br>
                                        <input type='checkbox' name="cb_salary_bank" id="cb_salary_bank">&nbsp Salary Bank&nbsp;&nbsp;
                                        <input type='checkbox' name="cb_advs_bank" id="cb_advs_bank">&nbsp Advising Bank
                                </td>
                                <td colspan=""><input type="button" id="btn_proceed_dis" name="btn_proceed_dis" style="width:150px;" value="Proceed Distribution %" class="formbutton" onClick="fn_distribution()" /> </td> 
                            </tr>
                            <tr>
								<td width="130" >Bank Short Name</td>
                                <td width="170">
                                    <input name="txt_bank_short_name" id="txt_bank_short_name" class="text_boxes" style="width:165px" maxlength="20" title="Maximum 20 Character" />
                                </td>
                                <td width="130" >Remarks</td>
                                <td colspan="3"> 
                                    <input name="txt_remarks" id="txt_remarks" class="text_boxes"style="width:450px" maxlength="500" title="Maximum 500 Character" >							
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" align="center" height="10">
                                    <input type="hidden" name="update_id" id="update_id" >					
                                </td>		 
                            </tr>
                            <tr>
                                <td colspan="6" align="center" class="button_container">
                                     <? 
                                         echo load_submit_buttons( $permission, "fnc_bank_entry", 0,0 ,"reset_form('bankinfo_1','','')",1);
                                     ?> 						
                                </td>		 
                            </tr>
                       </table>	
					</form>	
                      </td>
					</tr>
                    <tr>
                    	<td colspan="6" height="8"></td>
                    </tr>
                    <tr>
                    	<td colspan="6"> <!-- Child Form  -->
                        	 
                        <fieldset style="width:90%">
							<legend>Add Account Info</legend>
							<form name="bankaccinfo_2" id="bankaccinfo_2">
								<table  cellspacing="0" cellpadding="0" width="100%" class="rpt_table" rules="all">
									
                                    <thead>
                                        <th width="140" align="center" class="must_entry_caption"><strong>Account Type</strong></th>
                                        <th width="123" align="center"><strong>Account No</strong></th>
                                        <th width="60" align="center" class="must_entry_caption"><strong>Currency</strong></th>
                                        <th width="80" align="center"><strong>Loan Limit</strong></th>
                                        <th width="80" align="center"><strong>Limit Type</strong></th>
                                        <th width="150" align="center"><strong>Company Name</strong></th>
                                        <th width="130" align="center"><strong>Chart Of Account</strong></th>
                                        <th width="" align="center"><strong>Status</strong></th>
                                        
                                    </thead>
									<tr class="general">
										<td>
                                             <? 
                                               echo create_drop_down( "cbo_account_type", 140, $commercial_head,'', 1, '--Select--', 0, "", "","5,6,10,11,15,16,20,21,22,81,82,87,92,93,94,95,170,171,188" );
                                             ?>									
                                          </td>
										<td>
												<input type="text" name="txt_account_no" id="txt_account_no"  class="text_boxes" style="width:120px" maxlength="50" title="Maximum 50 Character"   />										</td>
										<td>
												<?
                                                echo create_drop_down( "cbo_currency", 60, $currency,"", "", "", 1, "" );
                                                ?>										
                                        </td>
										<td>
												<input type="text" name="txt_loan_limit" id="txt_loan_limit"  class="text_boxes_numeric" style="width:80px; text-align:right"  />
                                        </td>
                                        <td>
                                                <?
                                                echo create_drop_down( "cbo_loan_type", 80, $loan_type,"", "", "", 1, "" );
                                                ?>									
										</td>
										<td>
											<?
												echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company where is_deleted=0  and status_active=1 order by company_name","id,company_name", 1, "--- Select Company ---", 0, "" );
											 ?>									
                                        </td>
                                        <td width="130" align="center">
                                        <input type="text" name="txt_chart_of_account" id="txt_chart_of_account"  class="text_boxes" style="width:120px"/>				</td>
										<td>
											<?
												 echo create_drop_down( "cbo_status",70, $row_status,"", "", "", 1, "" );
											?>
                                             <input type="hidden" name="update_id_dtl" id="update_id_dtl" value=""  width="5"/>
										</td>
                                        
									</tr>
                                    <tr>
										<td colspan="8" align="center" class="button_container">
                                        <?
											 echo load_submit_buttons( $permission, "fnc_bank_acc_entry", 0,0 ,"reset_form('bankaccinfo_2','','')",2);	
										?>
										</td>
									</tr>
									<tr>
										<td colspan="8" align="center">
											<div id="account_list_view" style="width:820px" align="center"></div>										
										</td>
									</tr>
								</table>
							</form>	
							</fieldset>	
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="6" height="15"></td>
                    </tr>
                    
                    <tr>
                    	<td colspan="6" id="bank_info_list" align="center">
                         <?
						 $sql= "select id,bank_name,branch_name,status_active,total_account  from lib_bank where is_deleted=0 order by bank_name  ";
		                 $arr=array (2=>$row_status);
						 echo  create_list_view ( "list_view", "Bank Name,Branch Name,Status,Total Account", "200,200,100","620","220",1, $sql, "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,0,status_active,0", $arr , "bank_name,branch_name,status_active,total_account", "../general_info/requires/bank_info_controller", 'setFilterGrid("list_view",-1);','0,0,0,1') ;	   
						?>
                        </td>
                    </tr>
               </table>
         	</fieldset>	 
        </div>
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
		
			