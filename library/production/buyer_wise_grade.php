<?php
/******************************************************************
|	Purpose			:	This form will create Buyer Wise Grade
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
echo load_html_head_contents("Buyer Wise Grade Entry","../../", 1, 1, "",'1','');


?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	function fnc_buyer_penalty_entry( operation )
	{
		if (form_validation('cbo_buyer_id*txt_to*cbo_grade','Buyer Name*To*Grade')==false)
		{
			return;
		}
		else
		{
			var from=$("#txt_from").val();
			if(from=="")
			{
				alert("Range From Required!!");
				return;
			}
 			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_buyer_id*txt_from*txt_to*cbo_grade*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/buyer_wise_grade_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
		}
		http.onreadystatechange = fnc_buyer_penalty_entry_response;
	}
	
	function fnc_buyer_penalty_entry_response()
	{
		if(http.readyState == 4) 
		{  
			var response=trim(http.responseText).split('**');
			show_msg(trim(response[0]));
			if(response[0]==11)
			{
				alert("duplicate row found");
			}
			if(response[0]==0 || response[0]==1)
			{
				show_list_view('','penalty_list_view','penalty_list','../production/requires/buyer_wise_grade_controller','setFilterGrid("list_view",-1)');			
				reset_form('buyerpenalty_1','buyer_penalty_name','');
				set_button_status(0, permission, 'fnc_buyer_penalty_entry',1);
			}
			if(response[0]==2 )
			{
				show_list_view('','penalty_list_view','penalty_list','../production/requires/buyer_wise_grade_controller','setFilterGrid("list_view",-1)');			
				reset_form('buyerpenalty_1','buyer_penalty_name','');
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
                    <td width="110" class="must_entry_caption">Buyer Name</td>
                    <td><? echo create_drop_down( "cbo_buyer_id", 152, "select id, buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name ASC","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); ?>
                    </td>
                </tr>

                 <tr>
                    <td width="110" class="must_entry_caption" >Range From </td>
                    <td><input style="width:53px;"  class="text_boxes_numeric"  type="text" id="txt_from" name="txt_from" value=""> 

                   <span class="must_entry_caption">To &nbsp; </span><input style="width:53px;"  class="text_boxes_numeric"  type="text" id="txt_to" name="txt_to" value=""> 
                    
                    </td>
                    <input type="hidden" name="update_id" id="update_id" value="">
                </tr>



                 <tr>
                    <td width="110" class="must_entry_caption">Grade</td>
                    <td>
                    	
                    	<? echo create_drop_down( "cbo_grade", 152, $fabric_shade,"", 1, "-- Select Grade --", $selected, "" ); ?>


                    </td>
                </tr>

                 
                

                 

                <tr>
                	<td colspan="5" height="40" valign="bottom" align="center" class="button_container">
                		<? 
                		echo load_submit_buttons( $permission, "fnc_buyer_penalty_entry", 0,0 ,"reset_form('buyerpenalty_1','','','')");
                		?>
                	</td>					
                </tr>
                <tr>
                	<td height="15"></td>
                </tr>
                <tr>
            </table>
            <table cellpadding="0" cellspacing="2" width="520px" align="center">
                <td colspan="5" valign="bottom" align="center"  id="penalty_list">
                	<?
	                	$buyer_arr = return_library_array("select id, buyer_name from  lib_buyer", "id", "buyer_name");
	                	$arr=array(0=>$buyer_arr, 3=>$fabric_shade);
	                	echo  create_list_view ( "list_view", "Buyer Name,From,To,Grade", "150,100,100,50","600","220",1, "SELECT id, buyer_id, range_from, range_to, grade from buyer_wise_grade_mst where status_active=1 and is_deleted=0 order by id desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "buyer_id,0,0,grade", $arr , "buyer_id,range_from,range_to,grade", "../production/requires/buyer_wise_grade_controller", 'setFilterGrid("list_view",-1);' ) ;
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
