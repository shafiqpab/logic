<?php
/******************************************************************
|	Purpose			:	This form will create Defect Name 
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
echo load_html_head_contents("Defect Name Entry","../../", 1, 1, "",'1','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	function fnc_defect_entry( operation )
	{
		if (form_validation('cbo_defect_name*txt_defect_short_name*cbo_defect_type','Defect Name*Defect Short Name*Type')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_defect_name*txt_defect_short_name*update_id*cbo_defect_type*txt_sequence*cbo_status',"../../");
			freeze_window(operation);
			http.open("POST","requires/defect_name_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
		}
		http.onreadystatechange = fnc_defect_entry_response;
	}
	
	function fnc_defect_entry_response()
	{
		if(http.readyState == 4) 
		{  
			var response=trim(http.responseText).split('**');
			show_msg(trim(response[0]));
			if(response[0]==11)
			{
				alert("Duplicate data found");
			}
			if(response[0]==0 || response[0]==1 || response[0]==2)
			{
				show_list_view('','penalty_list_view','penalty_list','../production/requires/defect_name_entry_controller','setFilterGrid("list_view",-1)');			
				reset_form('buyerpenalty_1','defect_name','')
				set_button_status(0, permission, 'fnc_defect_entry',1);
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
            <fieldset style="width:400px;"><legend></legend>
            <table cellpadding="0" cellspacing="2" align="center">
                <tr>
				 	<td width="110" class="must_entry_caption" align="right">Type</td>
                    <td>
                    	<? 
							$defectTypeArr=array(1=>'Finish Fabric Defect',2=>'Finish Fabric Observation',3=>'Grey Fabric Defect');
							echo create_drop_down("cbo_defect_type", 153, $defectTypeArr, '', '1', '--Select--','',"load_drop_down( 'requires/defect_name_entry_controller', this.value, 'load_drop_down_defect_name', 'defect_name_td' );");
						?>
                    </td>
                </tr>
				<tr>
				 	<td width="110" class="must_entry_caption" align="right">Defect Name</td>
                    <td id="defect_name_td"> 
                    	<? echo create_drop_down("cbo_defect_name", 153, $blank,"", 1, '--Select--');?>
                    </td>
                </tr>
                <tr>
                    <td width="110" class="must_entry_caption" align="right">Defect Short Name</td>
                    <td><input  style="width: 142px;"    class="text_boxes" type="text" id="txt_defect_short_name" name="txt_defect_short_name" value=""> <input type="hidden" id="update_id" name="update_id"> </td>
                </tr>
				<tr>
                    <td width="110" class="" align="right">Sequence</td>
                    <td><input  style="width: 142px;" class="text_boxes_numeric" type="text" id="txt_sequence" name="txt_sequence" value=""></td>
                </tr>
				<tr>
                    <td width="110" class="" align="right">Status</td>
                    <td><?php echo create_drop_down( "cbo_status", 153, $row_status,'', '', '', 1 ); ?></td>
                </tr>
                  

                <tr>
                	<td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                		<? 
                		echo load_submit_buttons( $permission, "fnc_defect_entry", 0,0 ,"reset_form('buyerpenalty_1','','','')");
                		?>
                	</td>					
                </tr>
                <tr>
                	<td height="15"></td>
                </tr>
                <tr>
            </table>
            <table cellpadding="0" cellspacing="2" width="520px" align="center">
                <td valign="bottom" align="center"  id="penalty_list">
                	<?

						 $type_arr=array(1=>'Finish Fabric Defect',2=>'Finish Fabric Observation',3=>'Grey Fabric Defect');
						 if($type_arr==3){
							$arr = array(0 => $knit_defect_array,3 => $type_arr, 4=> $row_status);
							echo  create_list_view ( "list_view", "Defect Name,Defect Short Name,Sequence,Type,Status", "120,100,80,150,100","600","150",1, "SELECT id,   sequence_no,defect_name, short_name,status_active,type  from lib_defect_name where is_deleted=0 order by id desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "defect_name,0,0,type,status_active", $arr , "defect_name,short_name,sequence_no,type,status_active", "../production/requires/defect_name_entry_controller", 'setFilterGrid("list_view",-1);' ) ;
							
						 }else{
							$arr = array(0 => $finish_qc_defect_array,3 => $type_arr, 4=> $row_status);
							echo  create_list_view ( "list_view", "Defect Name,Defect Short Name,Sequence,Type,Status", "120,100,80,150,100","600","150",1, "SELECT id,   sequence_no,defect_name, short_name,status_active,type  from lib_defect_name where is_deleted=0 order by id desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "defect_name,0,0,type,status_active", $arr , "defect_name,short_name,sequence_no,type,status_active", "../production/requires/defect_name_entry_controller", 'setFilterGrid("list_view",-1);' ) ; 
							
						 }
						
                	?>
                	</td>					
                </tr>
            </table>
            <br>
            <div style="width:500px; float:left; min-height:40px; margin:auto" align="center" id="defect_name"></div>
	 </fieldset>
    </form>
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
