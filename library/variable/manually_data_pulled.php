<?php
/*-------------------------------------------- Comments
Purpose			: 	Data Pull form HR.
Functionality	:	
JS Functions	:
Created by		:	Md. Reaz Uddin
Creation date 	: 	13.11.2019
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
echo load_html_head_contents("Manually Data Puller", "../../", 1, 1,$unicode,'','');


?>
 

<script language="javascript">

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<?php echo $permission; ?>';

function fnc_tna_process( operation )
{
	
	if ( form_validation('cbo_variable_list_production*cbo_table_name','Manually Data Pulled*Table Name')==false )
	{
		return;
	}
	else
	{
		var point		= escape(document.getElementById('cbo_variable_list_production').value);
		//alert(point); return;
		var data="action=data_proces&cbo_variable_list_production="+$('#cbo_variable_list_production').val()+"&cbo_table_name="+$('#cbo_table_name').val();
		//alert(data); return;
		freeze_window(operation);
		// alert(data)
		http.open("POST","requires/manually_data_pulled_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_tna_process_reponse;
	}
	 
}

function fnc_tna_process_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		
		release_freezing();
		
		if(reponse[0]==30)
		{
			$('#missing_po').html(reponse[1]);
		}
		
		if(typeof reponse[0] === "undefined")
		{
			$('#missing_po').html("Please Cheack Database Setup.");
		}
		
		if(reponse[0]==0)
		{
			$('#missing_po').html("Successfully "+reponse[1] +" Data Pulled.");
		}
		else
		{
			$('#missing_po').html( reponse[1] +" Data Pulled Fail.");
		}
	}
}

function clear_field()
{
	$('#missing_po').html("");
}
 
</script>
</head>

<body  onLoad="set_hotkey()">
	<div align="center" style="width:100%;">
         
		<?php echo load_freeze_divs ("../../",$permission);  ?>
        
        <fieldset style="width:500px;">
            <legend>Auto Data Puller</legend>
            <form name="productionVariableSettings" id="productionVariableSettings" >
                    <table  width="500px" cellpadding="0" border="0">
                        <tr>
                            <td width="200" align="center" class="must_entry_caption">Manually Data Pulled</td>
                            <td width="200">
                                 <?php 
                                    echo create_drop_down( "cbo_variable_list_production", 180, $yes_no,'', '0', '---- Select ----', '',"show_list_view(this.value,'on_change_data','variable_settings_container','../variable/requires/manually_data_pulled_controller','')",'','1'); 
                                ?>
                            </td>
                        </tr>
                    </table>
                 
                 <div style="width:550px; float:left; min-height:40px; margin:auto" align="center" id="variable_settings_container"></div>
                
            </form>
        </fieldset>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
