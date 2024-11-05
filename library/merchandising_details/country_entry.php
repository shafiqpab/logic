<?php
/******************************************************************
|	Purpose			:	County Name entry
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Zakaria joy
|	Creation date 	:	20.11.2020
|	Updated by 		: 		
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		: Entry Form 454
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
		
		if (form_validation('txt_country_name','Country Name')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_country_name*txt_short_name*txt_region*update_id*cbo_zone_name',"../../");
			//alert(data);
			freeze_window(operation);
			http.open("POST","requires/country_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_save_update_delete_info;
		}
	}

function fnc_save_update_delete_info()
{
	if(http.readyState == 4) 
	{
		var response=http.responseText.split('**');	
		if(trim(response[0])==19)
		{
			release_freezing(); 
			alert('delete restricted');
			return;
		}	
		show_msg(trim(response[0]));
		if(trim(response[0])==11)
		{
			alert('Duplicate data found');
			//return;
		}
		
		
		show_list_view('','country_list_view','country_list_view','requires/country_entry_controller','setFilterGrid("list_view",-1)');
		reset_form('cutoff_1','','');
		set_button_status(0, permission, 'fnc_cut_off',1);	
		release_freezing(); 
	}
	
}

</script>
</head>
<body  onload="set_hotkey();">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div align="center" style="width:80%; margin-left:120px;">	
		<form name="cutoff_1" id="cutoff_1"  autocomplete="off">
            <fieldset style="width:500px;"><legend>Country Entry</legend>
            <table cellpadding="0" cellspacing="10" width="500" height="50" align="center">
                <tr>
                
                    <td width="110" align="right" class="must_entry_caption">Country Name</td>
                    <td><input type="text" id="txt_country_name" name="txt_country_name" class="text_boxes" />
                    <input type="hidden" id="update_id" name="update_id" class="text_boxes" readonly /></td>
                    <td>Short Name</td>
                    <td><input type="text" id="txt_short_name" name="txt_short_name" class="text_boxes" /></td>
                </tr>
                 <tr>
                
                    <td width="110" align="right" class=""> Region</td>
                    <td><input type="text" id="txt_region" name="txt_region" class="text_boxes" /> </td>
                    <td>
					  Zone 
					</td>
					<td width="110" align="right"><? 
					     $zone = array("European Union"=>"European Union","European Zone"=>"European Zone","New Market"=>"New Market");
						  echo create_drop_down( "cbo_zone_name", 150, $zone,"", 1, "-- Select Zone --", $selected, "",0 );
						?></td>

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
            <div id="country_list_view">
				<?php
                $arr=array (2=>$cut_up_array);
                echo  create_list_view ( "list_view", "Country Name,Short Name,Region,zone", "100,100,70,80","450","220",0, "select id, country_name,short_name,region,cut_off,zone from lib_country where status_active=1 and is_deleted=0  order by country_name Asc ", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,cut_off", "", "country_name,short_name,region,zone", "requires/country_entry_controller", 'setFilterGrid("list_view",-1);','0,0,0');
                ?>
            </div>
        </fieldset>
    </form>
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
