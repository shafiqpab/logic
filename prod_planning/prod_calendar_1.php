<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Woven Garments Trims Approval
					
Functionality	:	
				

JS Functions	:

Created by		:	CTO 
Creation date 	: 	12-11-2012
Updated by 		: 		
Update date		: 		   

QC Performed BY	:		

QC Date			:	

Comments		:

*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Woven Trims Approval","../", 1, 1, $unicode,'','');
?>	
 
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

var permission='<? echo $permission; ?>';
 	 
function openmypage(page_link,title)
{
	 
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0', '' )
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var theemail=this.contentDoc.getElementById("selected_job") //Access form field with id="emailfield"
		if (theemail.value!="")
		{
			freeze_window(5);
			get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/trims_approval_controller" );
			show_list_view(document.getElementById('txt_job_no').value,'show_trims_approval_list','accordion_container','../woven_order/requires/trims_approval_controller','');
		 	set_button_status(0, permission, 'fnc_trims_approval',1);
			release_freezing();
		}
	}
}
	
function show_hide_content(row, id) 
{
	$('#row_'+row).toggle('fast', function() {
		 get_php_form_data( id, 'set_php_form_data', '../woven_order/requires/trims_approval_controller' );
	});
}

function fnc_trims_approval( operation )
{
	if (form_validation('txt_job_no*cbo_trims_type*txt_target_approval_date','Job No*Trims Name*Target Approval Date')==false)
	{
		return;
	}	
	else
	{
		eval(get_submitted_variables('txt_job_no*cbo_trims_type*txt_target_approval_date*txt_send_to_fatory_date*txt_submission_to_buyer_date*cbo_approval_status*txt_approval_reject_date*txt_sample_comments*cbo_supplier*cbo_status*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_job_no*cbo_trims_type*txt_target_approval_date*txt_send_to_fatory_date*txt_submission_to_buyer_date*cbo_approval_status*txt_approval_reject_date*txt_sample_comments*cbo_supplier*cbo_status*update_id',"../../");
		 
		freeze_window(operation);
	  
		http.open("POST","requires/trims_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_trims_approval_reponse;
	}
	 
}
	 
function fnc_trims_approval_reponse()
{
	
	if(http.readyState == 4) 
	{
	    
		var reponse=trim(http.responseText).split('**');
		 
		 show_msg(trim(reponse[0]));
		
		 show_list_view(document.getElementById('txt_job_no').value,'show_trims_approval_list','accordion_container','../woven_order/requires/trims_approval_controller','');
		 
		//reset_form('sizecolormaster_1','','');
		set_button_status(0, permission, 'fnc_trims_approval',1);
		release_freezing();
		
	}
}
 
</script>
 
</head>
 
<body onLoad="set_hotkey()">

<div style="width:100%;" align="center">
												<!-- Important Field outside Form --> <input type="hidden" id="garments_nature" value="2">
     <? echo load_freeze_divs ("../../",$permission);  ?>
    
     <table width="90%" cellpadding="0" cellspacing="2" align="center">
     	<tr>
        	<td width="70%" align="center" valign="top">  <!--   Form Left Container -->
            	<fieldset style="width:950px;">
                <legend>Production Calendar</legend>
                
            		<table  width="900" cellspacing="2" cellpadding="0" border="0">
                       <tr>
                            <td  width="130" height="" align="right"> Job No </td>              <!-- 11-00030  -->
                                <td  width="170" >
                                <input style="width:160px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/prod_calendar_controller.php?action=order_popup','Job/Order Selection Form')" class="text_boxes" autocomplete="off" placeholder="Search Job" name="txt_job_no" id="txt_job_no" readonly />
                                 
                                </td>
                                <td  width="130" align="right">Company Name </td>
                                <td width="170">
                               <?
							   		echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/prod_calendar_controller', this.value, 'load_drop_down_location', 'location' ); load_drop_down( 'requires/prod_calendar_controller', this.value, 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/prod_calendar_controller', this.value, 'load_drop_down_agent', 'agent_td' ); " );
							   ?> 
                                 </td>
                              <td width="130" align="right">Location Name</td>
                              <td id="location">
                              <? 
							  
							  	echo create_drop_down( "cbo_location_name", 172, $blank_array,"", 1, "-- Select --", $selected, "",0 );		
								
								?>	
                               
                              </td>
                        </tr>
                        <tr>
                        	<td align="right">Buyer Name</td>
                              <td id="buyer_td">
                              <? 
                                echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --", $selected, "" ,0);   
                                ?>	  
                              </td>
                            <td align="right">Style Ref.</td>
                        	<td>
                            	<input class="text_boxes" type="text" style="width:160px" placeholder="Double Click for Quotation" name="txt_style_ref" id="txt_style_ref"/>	
                            </td>
                            <td align="right">
                               Style Description
                            </td>
                            <td>	
                                <input class="text_boxes" type="text" style="width:160px;" name="txt_style_description" id="txt_style_description"/>
                            </td>
                        </tr>
                        <tr>
                            <td height="" align="right">Pord. Dept.</td>   
                                <td >
                                <? 
							   		echo create_drop_down( "cbo_product_department", 172, $product_dept, "",1, "-- Select prod. Dept--", $selected, "" ,0);
							   ?>
                                </td>
                               
                               
                              <td align="right">Currency</td>
                              <td>
                              <? 
							  	echo create_drop_down( "cbo_currercy", 172, $currency, "", 1, "-- Select Currency--", 2, "",0 );
								?>	  
                              </td>
                              <td align="right">Agent </td>
                                <td id="agent_td">
                                <?	 	echo create_drop_down( "cbo_agent", 172, $blank_array,"", 1, "-- Select Agent --", $selected, "",0 );  
	 	
									 ?>
                                </td>
                        </tr>
                        <tr>
                            
                              <td  align="right">Region</td>
                              <td>
                              <? 
							  	echo create_drop_down( "cbo_region", 172, $region, "",1, "-- Select Region --", $selected, "",0 );
								?>	  
                              </td>
                               <td align="right">Team Leader</td>   
    						<td>
                             <?  
							  	echo create_drop_down( "cbo_team_leader", 172, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'requires/prod_calendar_controller', this.value, 'load_drop_down_merchant', 'merchant_td' );",0 );
								?>		
                            </td>
							<td align="right">Dealing Merchant</td>   
    						<td id="merchant_td"> 
                            <? 
							  	echo create_drop_down( "cbo_dealing_merchant", 172, $blank_array,"", 1, "-- Select Team Member --", $selected, "",0 );
								?>	
                           </td>
                        </tr>
                        <tr>
                        	<td align="center" height="10" colspan="6"></td>
                        </tr>
                        <tr>
                        	<td align="center" height="20" colspan="6" id="accordion_container"></td>
                        </tr>
                        <tr>
                        	<td align="center" height="10" colspan="6"></td>
                        </tr>
                        <tr>
                        	<td align="center" colspan="6" valign="top" id="po_list_view">
                            	<form name="trimsapproval_1" id="trimsapproval_1" autocomplete="off">
                             	<table id="tbl_sample_info" class="rpt_table">
                                	<thead>
                                    <tr>
                                        <th width="100">Trims Name </th>
                                        <th width="100" >Target Approval Date</th>
                                        <th width="100">Sent To Supplier</th>
                                        <th width="100">Submission to Buyer </th>					     
                                        <th width="100">Action</th>
                                        <th width="100">Action Date</th>
                                        <th width="100">Supplier</th>
                                        <th width="100">Comment</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    	<tr>
                                            <td>
                                            <?
                                            	echo create_drop_down( "cbo_trims_type", 140, "select item_name,id from lib_item_group where is_deleted=0 and status_active=1 order by item_name","id,item_name", 1, "-- Select Trims --", $selected, "" );
												?>
                                            </td>
                                            <td>
                                                <input name="txt_target_approval_date" type="text" id="txt_target_approval_date" style="width:80px" class="datepicker"/>
                                            </td>
                                            <td>
                                                <input name="txt_send_to_fatory_date" type="text" id="txt_send_to_fatory_date" onChange="check_date_status(1)" style="width:80px" class="datepicker"/>
                                            </td>
                                            <td>
                                                <input name="txt_submission_to_buyer_date" type="text" id="txt_submission_to_buyer_date"   onchange="check_date_status(12)" style="width:80px" class="datepicker"/>
                                                    
                                            </td>
                                            <td>
                                            	<?
                                            	echo create_drop_down( "cbo_approval_status", 100, $approval_status,"", 1, "--   --", $selected, "" );
												?>
                                                 
                                            </td>
                                            <td>
                                                <input name="txt_approval_reject_date" type="text" id="txt_approval_reject_date" style="width:80px" class="datepicker"/>
                                                 
                                            </td>
                                            <td>
                                            	<?
                                            	echo create_drop_down( "cbo_supplier", 100, $approval_status,"", 1, "--   --", $selected, "" );
												?>
                                                 
                                            </td>
                                            <td>
                                                <input name="txt_sample_comments" type="text" id="txt_sample_comments" style="width:180px" class="text_boxes"/>
                                            </td>
                                            <td>
                                            	<?
                                            		echo create_drop_down( "cbo_status", 80, $row_status,"", '', "", $selected, "" );
												?>
                                             <input type="hidden" id="update_id">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" colspan="9" height="10">
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" colspan="9" valign="middle" class="button_container">
                                              <? echo load_submit_buttons( $permission, "fnc_trims_approval", 0,0 ,"reset_form('trimsapproval_1','','')",1) ; ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                </form>
                            </td>
                        </tr>
                          
                    </table>
                 
              </fieldset>
           </td>
         </tr>
         
	</table>
	</div>
</body>
           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>