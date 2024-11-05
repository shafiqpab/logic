<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Woven Garments prod calendar
					
Functionality	:	
				

JS Functions	:

Created by		:	CTO 
Creation date 	: 	06.02.2013
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
	 
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=450px,center=1,resize=0,scrolling=0', '' )
	
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


function fnc_trims_approval( operation )
{
	if (form_validation('cbo_company_name*cbo_location_name*cbo_day_off_1','Plsease Select Comapny*Plsease Select location*Plsease Select off day')==false)
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



function numbersonly(myfield, e, dec)
{
	
	var key;
	var keychar;

	if (window.event)
   		key = window.event.keyCode;
	else if (e)
    	key = e.which;
	else
   		return true;
	keychar = String.fromCharCode(key);

	// control keys
	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
    return true;
	
	// numbers
	else if ((("0123456789.").indexOf(keychar) > -1))
   		return true;
	else
    	return false;
}


</script>
 
</head>
 
<body onLoad="set_hotkey()">
 		<div style="width:100%;" align="center">
    
    	<br/>
        <? echo load_freeze_divs ("../",$permission);  ?>
       
<fieldset style="width:660px "><!-- Start Field Set -->
			<legend>Production Calendar</legend>
			
			<form name="production_planning" id="production_planning" method="" autocomplete="off">  <!-- Start Form -->
            
            	<div style="width:760px; overflow:auto" align="center">
                    
                <table width="100%" border="0">
                
						 <tr>
									<td width="200">Company Name</td>
                                	<td width="170">
                               		<?
							   		echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/prod_calendar_controller', this.value, 'load_drop_down_location', 'location' );" );
							   		?> 
                                 	</td>&nbsp;
                                	<td width="100">Location</td>
                               		<td id="location">
                              		<? 
							  		echo create_drop_down( "cbo_location_name", 172, $blank_array,"", 1, "-- Select --", $selected, "",0 );		
									?>	
                              		</td>
                         </tr>		
						 <tr>
									<td width="200">Line No.</td>
                                	<td colspan="3"> <input type="text" name="txt_line_no" id="txt_line_no" class="text_boxes" onDblClick="openmypage('requires/prod_calendar_controller.php?action=order_popup','Line Information')" placeholder="Double Click For Search" readonly style="width:450px" />
                   					</td>
                          </tr>
                          <tr>
                                	<td width="200">Day Off1</td>
                                 	<td>
                       			 <? 
                           			//$all_cal_day=array(0=>"-- Select Day--",1=>"Saturday",2=>"Sunday",3=>"Monday",4=>"Tuesday",5=>"Wednesday",6=>"Thursday",7=>"Friday");
                           			//echo create_drop_down( "cbo_day_off_1", 160, $all_cal_day,"", 1, "", 0, "",0,"" );
									echo create_drop_down( "cbo_day_off_1", 160, $all_cal_day,"", 1, "-- Select day--", $selected, "",0 );
                        			?>
                    				</td>
                                	<td>&nbsp;</td><td>&nbsp;</td>
						   </tr>
                           <tr>
                                	<td width="200">Day Off2</td>
                               	 	<td>
                        			<? 
                           			//$all_cal_day=array(0=>"-- Select Day--",1=>"Saturday",2=>"Sunday",3=>"Monday",4=>"Tuesday",5=>"Wednesday",6=>"Thursday",7=>"Friday");
                           			//echo create_drop_down( "cbo_day_off_2", 160, $all_cal_day,"", 1, "", 0, "",0,"" );
									echo create_drop_down( "cbo_day_off_2", 160, $all_cal_day,"", 1, "-- Select day--", $selected, "",0 );
                        			?>
                                	</td><td>&nbsp;</td><td>&nbsp;</td>
							</tr>
                            <tr>
                                	<td width="200">Working Hour </td>
                                	<td><input type="text" name="txt_working_hr" id="txt_working_hr" class="text_boxes" style="width:152px;text-align:right;" onkeypress=
      "return numbersonly(this,event)"/></td>	
                                	<td>&nbsp;</td><td>&nbsp;</td>					
                            </tr>
                            <tr>
                                	<td width="200">Max OT </td>
                                	<td><input type="text" name="txt_max_ot" id="txt_max_ot" class="text_boxes" style="width:152px;text-align:right;"  onkeypress=
      "return numbersonly(this,event)"/></td>	
                                	<td>&nbsp;</td><td>&nbsp;</td>					
                            </tr>
                            <tr>
                                	<td width="200">Treat Govt. Holiday As Work. Day</td>
                               		<td>
                                   	<!-- <select name="cbo_treat_holiday" id="cbo_treat_holiday" class="combo_boxes" style="width:165px" >
                                    <option value="1">Yes</option>
                                     <option value="0" selected >No</option>
                                    </select>-->
                                    <?
                                    //$yes_no=array(1=>"Yes",2=>"No");
									echo create_drop_down( "cbo_treat_holiday", 160, $yes_no,"", 1, "", 1, "",0,"" );
									?>
                                	</td>	
                                	<td>&nbsp;</td><td>&nbsp;</td>					
                           </tr>
                           <tr>
                                	<td colspan="2" align="center">
                                    &nbsp; <input type="hidden" name="save_up" style="width:100px" id="save_up"/>
                                    &nbsp; <input type="hidden" name="txt_line_no_hidden" style="width:100px" id="txt_line_no_hidden"/>
                                	</td><td>&nbsp;</td><td>&nbsp;</td>				
                           </tr>
                           <tr>
                            		<td>&nbsp;</td>	
                               	 	<!--<td colspan="2" align="center">
                                    <input type="submit" value="Save" name="save" style="width:100px" id="save" class="formbutton"/>&nbsp;&nbsp;
                                    <input type="reset" value="  Refresh  " style="width:100px" name="reset" id="reset" class="formbutton" onClick="hidden_field_reset()"/>	
                                	</td>-->	
                                	<td align="center" colspan="9" valign="middle" class="button_container">
                                  	<? echo load_submit_buttons( $permission, "fnc_trims_approval", 0,0 ,"reset_form('production_planning','','')",1) ; ?>
                                	</td>
                                	<td>&nbsp;</td>		
                           </tr>
					</table>
            		</div>
                    
            </form> <!-- End Form -->
            
             <div style="width:760px;" id="production_calendar">
             </div>
			
</fieldset> <!-- End Field Set -->
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>