<?
/*--- ----------------------------------------- Comments
Purpose			: 					
Functionality	:	
JS Functions	:
Created by		:	 
Creation date 	: 	28-07-2023
Updated by 		: 	 
Update date		: 	 
QC Performed BY	:		
QC Date			:
Comments		:
*/
/*session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;*/
//--------------------------------------------------------------------------------------------------------------------
//echo load_html_head_contents("", "../", 1,1, $unicode,1,'');
//$bulletin_copy_arr=array(1=>"New Bulletin",2=>"Extended Bulletin");

?>
<script>
function fnc_basic_entry( operation ) //fnc_yarn_entry
	{
	 var cbo_fabrication=$("#cbo_fabrication").val();
	 if(cbo_fabrication>0)
	 {
	  var fabric_desc= $("#cbo_fabrication :selected").text();
	 }
	 else fabric_desc='';
	//   alert(fabric_desc);
	   if (form_validation('txt_booking_no*cbo_fab_color_code*cbo_fabrication','Booking No*Fabric Color*Fabric Desc')==false)
		{
			return; 
		}	
		else
		{ 
			var data="action=save_update_delete_basic&operation="+operation+get_submitted_data_string('company_id*txt_booking_no*txt_booking_id*cbo_fab_color_code*cbo_fabrication*update_id*req_id*txt_color_type*txt_booking_sl_no*finish_dia_type*finish_dia*finished_gsm',"../")+'&fabric_desc='+fabric_desc;
			// alert(data);
			freeze_window(operation);
			http.open("POST","requires/basic_info_entry_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_basic_entry_response;
		}
	}
	
	function fnc_basic_entry_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
 			if(response[0]==0 || response[0]==1 || response[0]==2)
			 {
				show_msg(response[0]);
				 
			 
				document.getElementById('update_id').value ='';
				show_list_view(response[3], 'listview_basic_info', 'basic_entry_info_list', 'requires/basic_info_entry_controller', 'setFilterGrid(\'tbl_details\',-1);');
				
 				//set_button_status(1, permission, 'fnc_basic_entry',1);
				reset_form('basicinfo_1','','');
				//alert(permission);
				//set_button_status(0, permission, 'fnc_basic_entry',1);
				$('#save1').removeClass('formbutton_disabled').addClass('formbutton');
				$('#save1').removeAttr('onclick').attr('onclick','fnc_basic_entry(0);')
				$('#update1').removeAttr('onclick').attr('onclick','')
				$('#update1').removeClass('formbutton').addClass('formbutton_disabled'); 
			    $('#Delete1').removeAttr('onclick').attr('onclick','')
				$('#Delete1').removeClass('formbutton').addClass('formbutton_disabled'); 
				//set_button_status(0, permission, 'fnc_basic_entry',1,1);

				
			 }
			if(response[0]==10 )
			{
				show_msg(response[0]);
			}
			if(response[0]==1 )
			{
				show_msg(response[0]);
				//reset_form('basicinfo_1','','');
			}

 			release_freezing();
		}
	}
</script>

   
    <form name="basicinfo_1" id="basicinfo_1"  autocomplete="off"  >
    	<div  style="width:734px;padding-left: 0px;border-left-width: 0px;padding-right: 0px;border-right-width: 0px;margin-right: 18px;" align="center">    		
	        <fieldset style="width:735px;">
	        <legend style=" color:#000000"> <b>Basic Info Entry</b></legend>
	            <table cellpadding="0" cellspacing="2" width="100%">
                
	                <tr>
                        <td width="120" class="must_entry_caption"><strong>Fab Color/Code</strong> 
                        <input type="hidden" id="update_id" style="width:140px;" /></td>
	                    <td width="150" id="basic_color_td">
	                    
	                    <?
                             
	                        echo create_drop_down( "cbo_fab_color_code",150,$sql,",", 1, "--Select--", $selected, "","","","","","",2);
	                    ?>
	                    </td>
                        <td width="120" class=""><strong>Fabrication</strong></td>
	                    <td width="150" id="basic_fabric_td">
	                     
	                    <?
                            $sql= "";
	                        echo create_drop_down( "cbo_fabrication",150,$sql,",", 1, "--Select--", $selected, "","","","","","",2);
	                    ?>
	                    </td>
	                </tr>
	                <tr>
                        <td width="120"><strong>Color Type</strong></td>
						<td width="150" id="color_type_td">
	                    <?
                             echo create_drop_down( "txt_color_type",150,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?>
	                    </td>
                        </td>
	                    <td width="120" class="">Booking SL No.</td>                                        
	                    <td width="140">
	                         <input type="text" name="txt_booking_sl_no" id="txt_booking_sl_no"  class="text_boxes_numeric" style="width:138px" placeholder="Write" />
	                    </td>
	                </tr> 
	                <tr>
                        <td><strong>Finish Dia Type</strong></td>
	                    <td align="left">
	                 		<input type="text" id="finish_dia_type" class="text_boxes_numeric" style="width:140px;" placeholder="Open/Tube" readonly />
							 <input type="hidden" id="finish_dia" class="text_boxes_numeric"/>
	                    </td>
	                    <td><strong>Finished GSM</strong></td>
	                    <td align="left" id="finished_gsm_td"><?
                             echo create_drop_down( "finished_gsm",150,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?></td>
	                </tr>
	            </table>
	        </fieldset>
	            <br>
	            <table  style="border:none; width:735px;" cellpadding="0" cellspacing="1" border="0" id="">
	                <tr>
	                    <td align="center" class="button_container">
	                        <? 
	                            echo load_submit_buttons($permission,"fnc_basic_entry",0,0,"reset_form('basicinfo_1','basic_entry_info_list','','','')",1);
	                        ?>
	                    </td>
	                </tr>  
	            </table>
	           <br>
	           
        </div>
    	<div id="basic_entry_info_list" style="width:750px;">
        </div>
     
     </form>

		
			