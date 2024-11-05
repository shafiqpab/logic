<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create PI Wise Yarn Receive
				
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar
Creation date 	: 	23-11-2013
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("PI Wise Yarn Receive","../../../", 1, 1, $unicode,1,1); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function generate_report(type)
{
	if( form_validation('cbo_company_name*txt_pi_no','Company Name*PI No')==false )
	{
		return;
	}
	
	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_store_name*txt_pi_no',"../../../")+'&report_title='+report_title+'&type='+type;
	freeze_window(3);
	http.open("POST","requires/pi_wise_yarn_receive_forpi_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse;  
}

function generate_report_reponse()
{	
	if(http.readyState == 4) 
	{	 
 		var reponse=trim(http.responseText).split("####");
		$("#report_container2").html(reponse[0]);  
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		
		show_msg('3');
		release_freezing();
	}
} 

function openmypage_pinumber()
{
	var companyID = $('#cbo_company_name').val();

	if (form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	
	var page_link='requires/pi_wise_yarn_receive_forpi_controller.php?action=pinumber_popup&companyID='+companyID;
	var title='PI Number Info';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var pi_id=this.contentDoc.getElementById("pi_id").value;
		var pi_no=this.contentDoc.getElementById("pi_no").value;
		
		$('#pi_id').val(pi_id);
		$('#txt_pi_no').val(pi_no);
	}
}

function new_window()
{
	document.getElementById('caption').style.visibility='visible';
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');

	document.getElementById('caption').style.visibility='hidden';
	d.close(); 
}


</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission); ?><br />    		 
    <form name="piWiseYarnReceive_1" id="piWiseYarnReceive_1" autocomplete="off" > 
        <div style="width:100%;" align="center">
            <fieldset style="width:660px;">
            <legend>Search Panel</legend> 
                <table class="rpt_table" width="650" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="150" class="must_entry_caption">Company</th>                                
                            <th width="140">Store</th>
                            <th width="100" class="must_entry_caption">PI Number</th>
                           
                            <th width="120" colspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('piWiseYarnReceive_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, " load_drop_down( 'requires/pi_wise_yarn_receive_forpi_controller', this.value, 'load_drop_down_store', 'store_td' );" );
                            ?>                            
                        </td>
                        <td id="store_td"> 
                            <?
                                echo create_drop_down( "cbo_store_name", 150, $blank_array,"", 1, "-- Select Store --", 0, "" );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_pi_no" name="txt_pi_no" class="text_boxes" style="width:100px" placeholder="Write Or Browse" onDblClick="openmypage_pinumber()"  />
                            <input type="hidden" id="pi_id" readonly /> 
                        </td>
                       
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:100px" class="formbutton" />
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Sweater Details" onClick="generate_report(2)" style="width:100px" class="formbutton" />
                        </td>
                    </tr>
                </table> 
            </fieldset>  
            
            <div id="report_container" align="center"></div>
        	<div id="report_container2"></div>
            
        </div>
    </form>    
</div>    
</body>  
<script>
	//set_multiselect('cbo_store_name','0','0','','0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
