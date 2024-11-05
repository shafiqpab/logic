<?php
/******************************************************************
|	Purpose			:	This form will create company location count
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Rehan Uddin
|	Creation date 	:	22-10-2018
|	Updated by 		: 		
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
********************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Company Location Floor Line Count Entry","../../", 1, 1, "",'1','');


?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	function fnc_count_entry( operation )
	{
		if (form_validation('txt_company_count*txt_location_count*txt_floor_count*txt_line_count','Company*Location*Floor*Line')==false)
		{
			return;
		}
		else
		{
			 
 			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_company_count*txt_location_count*txt_floor_count*txt_line_count*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/company_location_count_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
		}
		http.onreadystatechange = fnc_count_entry_response;
	}
	
	function fnc_count_entry_response()
	{
		if(http.readyState == 4) 
		{  
			var response=trim(http.responseText).split('**');
			show_msg(trim(response[0]));
			if(response[0]==420)
			{
				alert("you can't insert second time ");
			}
			 
			if(response[0]==0 || response[0]==1)
			{
				show_list_view('','details_list_view','dtls_list','../production/requires/company_location_count_controller','setFilterGrid("list_view",-1)');			
				reset_form('buyerpenalty_1','','');
				set_button_status(0, permission, 'fnc_count_entry',1);
			}
			if(response[0]==2 )
			{
				show_list_view('','details_list_view','dtls_list','../production/requires/company_location_count_controller','setFilterGrid("list_view",-1)');			
				reset_form('buyerpenalty_1','','');
			}
			release_freezing();	
			 
		}
	}
	 
</script>
</head>
<body  onload="set_hotkey()">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div align="center" style="width:100%;">	
		<form name="buyerpenalty_1" id="buyerpenalty_1"  autocomplete="off">
            <fieldset style="width:500px;"><legend></legend>
            <table cellpadding="0" style="margin-left: 200px;" cellspacing="2" width="520px" align="center">
                 

                 <tr>
                    <td width="110" class="must_entry_caption" >Company Count </td>
                    <td><input style="width:120px;"  class="text_boxes_numeric"  type="text" id="txt_company_count" name="txt_company_count" value="">                   
                    
                    </td>
                    <input type="hidden" name="update_id" id="update_id" value="">
                </tr>
                <tr>
                    <td width="110" class="must_entry_caption" >Location Count </td>
                    <td><input style="width:120px;"  class="text_boxes_numeric"  type="text" id="txt_location_count" name="txt_location_count" value="">                   
                    
                    </td>
                    
                </tr>
                <tr>
                    <td width="110" class="must_entry_caption" >Floor Count </td>
                    <td><input style="width:120px;"  class="text_boxes_numeric"  type="text" id="txt_floor_count" name="txt_floor_count" value="">                   
                    
                    </td>
                    
                </tr>

                <tr>
                    <td width="110" class="must_entry_caption" >Line Count </td>
                    <td><input style="width:120px;"  class="text_boxes_numeric"  type="text" id="txt_line_count" name="txt_line_count" value="">                   
                    
                    </td>
                    
                </tr>



                 

                 
                

                 

                <tr>
                	<td colspan="5" height="40" valign="bottom" align="center" class="button_container">
                		<? 
                		echo load_submit_buttons( $permission, "fnc_count_entry", 0,0 ,"reset_form('buyerpenalty_1','','','')");
                		?>
                	</td>					
                </tr>
                <tr>
                	<td height="15"></td>
                </tr>
                <tr>
            </table>
            <table cellpadding="0" cellspacing="2" width="520px" align="center">
                <td colspan="5" valign="bottom" align="center"  id="dtls_list">
                	<?
                	$arr=array();
                	echo  create_list_view ( "list_view", "Company Count,Location Count,Floor Count,Line Count", "150,100,100,50","600","220",1, "SELECT id,company_count, location_count, floor_count, line_count  from company_loc_flr_line_count where  status_active=1 and is_deleted=0 order by id desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,0,0,0", $arr , "company_count,location_count,floor_count,line_count", "../production/requires/company_location_count_controller", 'setFilterGrid("list_view",-1);' ) ;
                	?>
                	</td>					
                </tr>




            </table>
            <br>
            <div style="width:500px; float:left; min-height:40px; margin:auto" align="center" id="buyer_penalty_name"></div>
	 </fieldset>
    </form>
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
