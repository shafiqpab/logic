<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Color List
					
Functionality	:	
				

JS Functions	:

Created by		:	Ashraful 
Creation date 	: 	17-02-2014
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
echo load_html_head_contents("Color Information", "../../", 1, 1,$unicode,1,'');
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';

function fnc_size_info( operation )
{
	if (form_validation('txt_size_name','Size Name')==false)
	{
		return;
	}
	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_size_name*txt_sequence*cbo_status*update_id',"../../");
		//alert(data);
		freeze_window(operation);
		http.open("POST","requires/size_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_size_info_reponse;
	}
}

function fnc_size_info_reponse()
{
	if(http.readyState == 4) 
	{  
	
	//alert(http.responseText);
		var reponse=trim(http.responseText).split('**');
		//alert(reponse[0])
		show_msg(reponse[0]);
		show_list_view('','color_list_view','color_list_view','../merchandising_details/requires/size_entry_controller','setFilterGrid("list_view",-1)');
		reset_form('colorinfo_1','','');
		set_button_status(0, permission, 'fnc_size_info',1);
		release_freezing();
	}
}

</script>
</head>
<body  onload="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center" style="width:100%;">	
    
	<fieldset style="width:500px;">
		<legend>Size Information</legend>
		<form name="colorinfo_1" id="colorinfo_1"  autocomplete="off">	
			<table cellpadding="0" cellspacing="2" width="500px">
			 	<tr>
					<td width="100" class="must_entry_caption">Size Name</td>
					<td >
						<input type="text" name="txt_size_name" id="txt_size_name" class="text_boxes" style="width:150px" />
					</td>
                    <td width="50" >Sequence</td>
					<td >
						<input type="text" name="txt_sequence" id="txt_sequence" class="text_boxes" style="width:150px" />
					</td>
                  </tr>
                 
                  </tr>
                  <tr>
                  <td width="50" >Status</td>
                      <td >
						 <?
						 echo create_drop_down( "cbo_status", 165, $row_status,'', $is_select, $select_text, 1, $onchange_func, '','','','',3 );
						 ?>
					</td>
                  
                  </tr>
                  <tr>
					<td colspan="4" align="center" class="button_container">
						<? 
					     echo load_submit_buttons( $permission, "fnc_size_info", 0,0 ,"reset_form('colorinfo_1','','')",1);
				        ?> 
                        <input type="hidden" name="update_id" id="update_id" >
					</td>				
				</tr>
                <tr>
						<td colspan="4" align="center" id="color_list_view">
							<?
							$arr=array (2=>$row_status);
							echo  create_list_view ( "list_view", "Size Name,Sequence,Status", "200,100,100","450","220",0, "select  size_name,sequence,status_active,id from   lib_size where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,status_active", $arr , "size_name,sequence,status_active", "../merchandising_details/requires/size_entry_controller", 'setFilterGrid("list_view",-1);' ) ;//\\192.168.11.252\logic_erp_3rd_version\prod_planning\cutting_plan\requires
							 ?>
						</td>
					</tr>
		   </table>
			</form> 
		</fieldset>	
	</div>
</div>
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>
