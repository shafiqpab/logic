<?
/******************************************************************
|	Purpose			:	This form will create loan information
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md. Didarul Alam
|	Creation date 	:	03.08.2016
|	Updated by 		: 		
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
*******************************************************************/
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
		
function fnc_fastreact_integration( operation )
{
	
    if( form_validation('shift_date*received_date','Start Date*End Date')==false )
	{
		return;
	}
    
    var data="action=save_update_delete&operation="+operation+'&shift_date='+$('#shift_date').val()+'&received_date='+$('#received_date').val()+'&mising_po=generate_mising_po_list'+'&report_title=Mising PO';	
	freeze_window(operation);
	http.open("POST","requires/fastreact_comparison_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_store_location_reponse;   
}

function fnc_store_location_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');              
		//reset_form('fastreact_integrationform_1','','');
        $('#report_container2').html(reponse[0]);
        document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
		release_freezing();
	}
}

</script>

</head>

<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">  
        <? echo load_freeze_divs ("../../",$permission);  ?>
        <fieldset style="width:500px ">  
            <form id="fastreact_integrationform_1"  name="fastreact_integrationform_1" autocomplete="off" >                
                <table  border="0" cellpadding="0" cellspacing="1" width="450" style="margin-top:20px;">                    
                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            Shipment Date From
                            <input type="text" name="shift_date" id="shift_date" class="datepicker" style="width:110px;" placeholder="Select Date" />
                            &nbsp;&nbsp;                        
                           Shipment Date To
                            <input type="text" name="received_date" id="received_date" class="datepicker" style="width:110px;" placeholder="Select Date" />
                        </td>
                    </tr>
                    <tr>
                       
                        <td colspan="2" height="35" valign="middle" align="center">
                            
                           <input type="button" class="image_uploader" style="width:192px" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', 999999999,'', 'fr_csv', 2,0,'', 2)">
                        </td>
                    </tr>
                    
                    <tfoot>
                        <tr>
                            <td colspan="8" align="center" style="padding-top:10px;" class="button_container">
                            <input type="button" id="data_process" value="Synchronize" style="width:270px; height:45px" class="formbutton" onClick="fnc_fastreact_integration(0)" />
                            </td>
                        </tr>
                    </tfoot>
                </table>               
            </form>
        </fieldset>
        
        <br>
        <div id="report_container" align="center"></div>
        <div id="report_container2"> 
    </div>   
</body>
    
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
