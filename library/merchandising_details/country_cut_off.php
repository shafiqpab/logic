<?php
/******************************************************************
|	Purpose			:	
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Monir Hossain
|	Creation date 	:	17.10.2016
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
echo load_html_head_contents("Country Cut Off","../../", 1, 1, "",'1','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	function fnc_cut_off(operation)
	{
	
		 if(operation==2)
		 {
			 alert('You have no permission to delete it.');
			 return;
			 
		}
		
		if (form_validation('txt_Country_id','Country Name')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete_cutoff&operation="+operation+get_submitted_data_string('txt_Country_id*cbo_cutOff_id*update_id',"../../");
			//alert(data);
			freeze_window(operation);
			http.open("POST","requires/country_cut_off_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_cut_off_save_Reply_info;
		}
	}

function fnc_cut_off_save_Reply_info()
{
	if(http.readyState == 4) 
	{
		var response=http.responseText.split('**');	

		show_msg(trim(response[0]));	
		
		show_list_view('','cutoff_list_view','cutoff_list_view','requires/country_cut_off_controller','setFilterGrid("list_view",-1)');
		reset_form('cutoff_1','','');
		set_button_status(0, permission, 'fnc_cut_off',1);	
		release_freezing(); 
	}
	
}
	function contry_dis(){
	$('#txt_Country_id').attr('disabled','disabled');
	}

</script>
</head>
<body  onload="set_hotkey();contry_dis();">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div align="center" style="width:80%; margin-left:120px;">	
		<form name="cutoff_1" id="cutoff_1"  autocomplete="off">
            <fieldset style="width:500px;"><legend>Country Cut Off</legend>
            <table cellpadding="0" cellspacing="10" width="500" height="50" align="center">
                <tr>
                
                    <td width="110" align="right" class="must_entry_caption">Country</td>
                    <td><input type="text" id="txt_Country_id" name="txt_Country_id" class="text_boxes" /><input type="hidden" id="update_id" name="update_id" class="text_boxes" readonly /></td>
                    <td></td>
                    <td></td>
                    <td width="110" align="right">Cut Off </td>
                    <td><? echo create_drop_down( "cbo_cutOff_id", 100, $cut_up_array, 0, 1, "--Select--", "","" );  ?></td>
                </tr>
                <tr>
                <td></td>
                </tr>
                <tr>
                
                	<td colspan="6" align="center" style="padding-top:10px;" class="button_container">
                    <?
					echo load_submit_buttons( $permission, "fnc_cut_off", 0,0 ,"reset_form('cutoff_1','','','','')",1) ; 
					?>
                    </td>
                </tr>
				
            </table>
            <br>
           
	 </fieldset>
     <fieldset style="width:500px;">
            <div id="cutoff_list_view">
				<?php
                $arr=array (2=>$cut_up_array);
                echo  create_list_view ( "list_view", "Country Name,Short Name,Cut Off", "150,150","550","220",0, "select id, country_name,short_name,cut_off from lib_country where status_active=1 and is_deleted=0 order by country_name Asc ", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,cut_off", $arr, "country_name,short_name,cut_off", "requires/country_cut_off_controller", 'setFilterGrid("list_view",-1);','0,0');
                ?>
            </div>
        </fieldset>
    </form>
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
