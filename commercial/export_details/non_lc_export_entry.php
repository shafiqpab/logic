<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for Non LC Export entry
					
Functionality	:	
				

JS Functions	:

Created by		:	Bilas 
Creation date 	: 	 
Updated by 		: 	Bilas
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
echo load_html_head_contents("Pre Export Finance Form", "../../", 1, 1,'','1','');
?>	
 
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';


function popup_loanamount()  
{
	 
	var beneficiary	= $('#cbo_beneficiary_name').val();
 	var save_data	= $('#save_data').val();
	var all_lc_sc 	= $('#all_lc_sc_id').val();
	var lc_or_sc 	= $('#lc_or_sc').val();
	var currency_name 	= $('#hid_currency_name').val();
	 
	if(form_validation('cbo_beneficiary_name','Beneficiary')==false)
	{
		return;
	}  
	
	var title = 'LC/SC Info';	
	var page_link = 'requires/pre_export_finance_controller.php?beneficiary='+beneficiary+'&lc_or_sc='+lc_or_sc+'&all_lc_sc='+all_lc_sc+'&save_data='+save_data+'&action=lcsc_popup&currency_name='+currency_name;
	 
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var save_string=this.contentDoc.getElementById("save_string").value;	 
		var tot_amount=this.contentDoc.getElementById("tot_amount").value;  //this is issue qnty 
 		var all_lcsc_id=this.contentDoc.getElementById("all_lcsc_id").value;
		var lc_or_sc=this.contentDoc.getElementById("lc_or_sc").value;  
		var hid_currency_name=this.contentDoc.getElementById("hid_currency_name").value;  
		
		$('#save_data').val(save_string);
		$('#txt_loan_amount').val(tot_amount);
 		$('#all_lc_sc_id').val(all_lcsc_id);
		$('#lc_or_sc').val(lc_or_sc);
		$('#hid_currency_name').val(hid_currency_name);
 	}
}


 
function fnc_pre_export_finance_entry(operation)
{

		if (form_validation('cbo_beneficiary_name*cbo_loan_type*txt_loan_number*cbo_bank_acc*txt_loan_amount*txt_conversion_rate','Beneficiary Name*Loan Type*Bank Account*Loan Amount*Conversion Rate')==false )
		{
			return;
		}
		else if($("#txt_loan_amount").val()*1<1)
		{
			alert("Check Loan Amount.");return;
		}
		else if($("#txt_conversion_rate").val()*1<1)
		{
			alert("Check Loan Conversion Rate.");return;
		}
 			
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_system_id*txt_system_number*cbo_beneficiary_name*txt_loan_date*cbo_loan_type*txt_loan_number*cbo_bank_acc*txt_loan_amount*txt_conversion_rate*txt_equivalent_fc*hid_currency_name*save_data*all_lc_sc_id*lc_or_sc*update_id',"../../");
		//freeze_window(operation);		
		http.open("POST","requires/pre_export_finance_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_pre_export_finance_entry_Reply_info;
}


function fnc_pre_export_finance_entry_Reply_info()
{
	
	if(http.readyState == 4) 
	{
		// alert(http.responseText);
		var reponse=http.responseText.split('**');	
		if(reponse[0]==20)
		{
			alert(reponse[1]);
			return;
		}
		else if(reponse[0]==0)
		{
			$("#txt_system_id").val(reponse[1]);
			$("#txt_system_number").val(reponse[2]);
		}
		show_msg(reponse[0]);	
		$("#tbl_child").find('input,select').val('');
		set_button_status(0, permission, 'fnc_pre_export_finance_entry',1);	
		show_list_view(reponse[1],'show_dtls_list_view','list_view_container','requires/pre_export_finance_controller','');
 		release_freezing(); 
	}
}


function system_number_popup()
{
	if( form_validation('cbo_beneficiary_name','Beneficiary Name')==false )
	{
		return;
	}
	var beneficiary_name = $("#cbo_beneficiary_name").val();
	var page_link='requires/pre_export_finance_controller.php?action=mrr_popup&beneficiary_name='+beneficiary_name; 
	var title="Search System Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var mrrID=this.contentDoc.getElementById("hidden_system_number").value; // mrr number
  		// master part call here
		get_php_form_data(mrrID, "populate_master_from_data", "requires/pre_export_finance_controller");		
		//list view call here
		show_list_view(mrrID,'show_dtls_list_view','list_view_container','requires/pre_export_finance_controller','');
		$("#tbl_child").find('input,select').val('');
 	}
}



function fn_calAmount()
{
	var amt = $("#txt_loan_amount").val();
	var rate = $("#txt_conversion_rate").val();
 	if(amt=="") amt=0;
	if(rate=="") rate=0;
	var totalAmt = amt/rate;
	$("#txt_equivalent_fc").val(number_format_common(totalAmt,5));
}

	
</script>
 
 
</head> 
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">																	
     	<? echo load_freeze_divs ("../../",$permission); ?><br/>
        <fieldset style="width:930px; margin-bottom:10px;">
        <form name="preexportFrm_1" id="preexportFrm_1" autocomplete="off" method="POST"  >
        	 
            	<!------------------------------ 1st form Start here ------------------------------------------->
                <table cellpadding="0" cellspacing="1" width="500" id="tbl_master">
                	<tr> 
                    	<td colspan="6" align="center" style="height:50px" ><b>System ID</b> 
                             <input type="text" name="txt_system_number" id="txt_system_number"  placeholder="Double Click" onDblClick="system_number_popup()" readonly class="text_boxes" />
                             <input type="hidden" name="txt_system_id" id="txt_system_id"  readonly class="text_boxes" />
                        </td>
                    </tr>
                  	<tr>
                    	<td width="110" align="right" class="must_entry_caption">Beneficiary&nbsp;&nbsp;</td>
                        <td width="170">
                        	<?
							   	echo create_drop_down( "cbo_beneficiary_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Beneficiary --", 0, "" );
							?>                          
                        </td>
                        <td width="110" align="right">Loan Date&nbsp;&nbsp;</td>
                    	<td width="170"><input name="txt_loan_date" id="txt_loan_date" class="datepicker" style="width:150px" readonly ></td>
                    </tr>
                    
                </table>            
        	<br />	 
            <!------------------------------ 1st form end here ------------------------------------------->
            <fieldset>
            	<table cellpadding="1" cellspacing="0" width="800" class="rpt_table" id="tbl_child">	
                    <thead>
                    	<tr> 		 	 	
                        	<th class="must_entry_caption">Loan Type</th>
                            <th class="must_entry_caption">Loan Number</th>
                            <th class="must_entry_caption">Bank Account</th>
                            <th class="must_entry_caption">Loan Amount</th>
                            <th class="must_entry_caption">Conversion Rate</th>
                            <th>Equivalent FC</th>
                         </tr>
                    </thead>
                    
                    	<tr align="center">
                        	<td>
                            	<?
							   		echo create_drop_down( "cbo_loan_type", 120, $ac_loan_type,"", 1, "--Select--", "", "", "", "10,11" );
								?>
                            </td>
                            <td>
                            	<input type="text"  class="text_boxes" id="txt_loan_number" name="txt_loan_number"  style="width:110px" placeholder="Entry"  />
                            </td>
                            <td>
                            	<?
							   		echo create_drop_down( "cbo_bank_acc", 120, $commercial_head,"", 1, "--Select--", "", "", "", "10,11,15,16" );
								?>
                            </td>
                            <td>
                            	<input type="text"  class="text_boxes_numeric" id="txt_loan_amount" name="txt_loan_amount"  style="width:110px" readonly placeholder="Double Click" onDblClick="popup_loanamount()"  />
                                 <!-- hdden field--->
                                <input type="hidden" id="save_data" readonly disabled />
                                <input type="hidden" id="all_lc_sc_id" readonly disabled />
                                <input type="hidden" id="lc_or_sc" readonly disabled /><!-- 1:LC, 2: SC -->
                                <input type="hidden" name="hid_currency_name" id="hid_currency_name" value="" >
                                <!------    ------->
                            </td>
                            <td>
                            	<input type="text"  class="text_boxes_numeric" id="txt_conversion_rate" name="txt_conversion_rate"  style="width:110px" placeholder="Entry" onKeyUp="fn_calAmount();" />
                            </td>
                            <td>
                            	<input type="text"  class="text_boxes_numeric" id="txt_equivalent_fc" name="txt_equivalent_fc"  style="width:110px" placeholder="Display" readonly  />
                            </td>
                        </tr>
                 </table>       
                 <table cellpadding="1" cellspacing="0" width="800" class="rpt_table">                           
                        <tr>
                            <td colspan="6" height="30" valign="middle" align="center" class="button_container">
                                 <!-- hdden field--->
                                 <input type="hidden" id="update_id" />
                                 <!------    ------->
								 <? 
                                    echo load_submit_buttons( $permission, "fnc_pre_export_finance_entry",0,0 ,"reset_form('preexportFrm_1','list_view_container','','','','');",1) ; 
                                 ?>
                            </td>
                        </tr>
                 </table>   
            </fieldset>        
            <!------------------------------ 2nd form start here ----------------------------------------->
			
            
            <!------------------------------ List View Start here ------------------------------------------->
            <fieldset>
            	<div style="width:1000px;" id="list_view_container"></div>
            </fieldset>
            <!------------------------------ List View Start end here --------------------------------------->
        	
        </form>
        </fieldset> 
 	</div>
</body> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>