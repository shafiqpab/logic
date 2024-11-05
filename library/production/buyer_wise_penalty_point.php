<?php
/******************************************************************
|	Purpose			:	This form will create Buyer Wise Penalty Point
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Rehan Uddin
|	Creation date 	:	16-10-2018
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
echo load_html_head_contents("Buyer Wise Penalty Point Entry","../../", 1, 1, "",'1','');


?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	function fnc_buyer_penalty_entry( operation )
	{
		if (form_validation('cbo_buyer_id*cbo_defect_name*cbo_inch*txt_penalty_point','Buyer Name*Defect Name*Inch*Point')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_buyer_id*cbo_defect_name*cbo_inch*txt_penalty_point*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/buyer_wise_penalty_point_controller.php",true);
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
				show_list_view('','penalty_list_view','penalty_list','../production/requires/buyer_wise_penalty_point_controller','setFilterGrid("list_view",-1)');			
				reset_form('buyerpenalty_1','buyer_penalty_name','')
				set_button_status(0, permission, 'fnc_buyer_penalty_entry',1);
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
                    <td width="110" class="must_entry_caption">Defect Name</td>
                    <td>
                    	
                    	<? echo create_drop_down( "cbo_defect_name", 152, "select defect_name, short_name from lib_defect_name where status_active =1 and is_deleted=0 order by short_name ASC","defect_name,short_name", 1, "-- Select Defect --", $selected, "" ); ?>


                    </td>
                </tr>

                 
                <tr>
                	<td  width="110" class="must_entry_caption">Found in (Inch)</td>
                	<td><? echo create_drop_down("cbo_inch" . $i, 152, $knit_defect_inchi_array, "", 1, "-- Select --", 0, "", '', ''); ?></td>
                </tr>

                  <tr>
                    <td width="110" >Penalty Point</td>
                    <td><input style="width: 142px;"  class="text_boxes"  type="text" id="txt_penalty_point" name="txt_penalty_point" value=""> 
                    <input type="hidden" name="update_id" id="update_id" value="">
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
	                	$defect_arr = return_library_array("select id, defect_name from  lib_defect_name", "id", "defect_name");
                        $defect_short_arr = return_library_array("select id, short_name from  lib_defect_name", "id", "short_name");

	                	$arr=array(0=>$buyer_arr,1=>$defect_arr,2=>$knit_defect_inchi_array);
	                	echo  create_list_view ( "list_view", "Buyer Name,Defect Name,Found in(Inch),Point", "150,100,100,50","600","220",1, "SELECT id, buyer_id, defect_name, inch, penalty_point from buyer_wise_penalty_point where status_active=1 and is_deleted=0 order by id desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "buyer_id,defect_name,inch,0", $arr , "buyer_id,defect_name,inch,penalty_point", "../production/requires/buyer_wise_penalty_point_controller", 'setFilterGrid("list_view",-1);' ) ;
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
