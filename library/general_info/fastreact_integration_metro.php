<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Store Location of a company.
Functionality	:	First create Store Location and save. select a team from list view for update.
JS Functions	:
Created by		:	Monzu 
Creation date 	: 	07-10-2012
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
echo load_html_head_contents("Fast React Integration", "../../", 1, 1,$unicode,1,'');
?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';	

function fnc_store_location( operation )
{
	//eval(get_submitted_variables('cbo_fr_integrtion'));
	var data="action=save_update_delete&operation="+operation+'&cbo_fr_integrtion='+$('#cbo_fr_integrtion').val()+'&received_date='+$('#received_date').val() ;
	
	freeze_window(operation);
	http.open("POST","requires/fastreact_integration_metro_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_store_location_reponse;
}

function fnc_store_location_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		reset_form('storelocation_1','','');
		set_button_status(0, permission, 'fnc_store_location',1);
		release_freezing();
		window.open("requires/frfiles/fr_files.zip","##");
		//window.open("requires/frfiles/ImgFolders.zip","##");
	}
}

</script>
</head>
<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center" style="width:100%;">
        <fieldset style="width:600px;">
       	 <legend>FR Integration</legend>
            <form name="storelocation_1" id="storelocation_1" autocomplete = "off">	
              <table cellpadding="0" cellspacing="2" width="100%">
              	<tr>
                  	<td width="121">Export PO Cutoff Date</td>
                  	<td width="375"><input type="text" class="datepicker" id="received_date" style="width:100px" > </td>        
                </tr>
                <tr>
                  	<td width="121">Export Module </td>
                  	<td width="375"><? 
					 	$fr_item_list= array( 0=>"All Modules"); 
						//,1=>"Customer",2=>"Products",3=>"Orders",4=>"Events",5=>"Production Updates",6=>"Attendance",7=>"Image Attach"
                  		echo create_drop_down( "cbo_fr_integrtion", 315, $fr_item_list, 0, "", 1, "" );
                  	?>	</td>            
                </tr>
                <tr style="display:none">
                  	<td width="121">Status</td>
                  	<td><? echo create_drop_down( "cbo_status", 224, $row_status,"", "", "", 1, "" ); ?></td>
                </tr>
                <tr>
                <td>&nbsp;</td><td>&nbsp;</td></tr>
                <tr>
                    <td align="center" colspan="2" class="button_container"><? echo load_submit_buttons( $permission, "fnc_store_location", 0,0 ,"reset_form('storelocation_1','','')",1); ?></td>
                </tr>
              </table>
            </form>
           
        </fieldset>
        </div>
	</body>
    
<script>//set_multiselect('cbo_catagory_item','0','0','','');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
