<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create File Wise Yarn Receive Report
				
Functionality	:	
JS Functions	:
Created by		:	REZA
Creation date 	: 	26-6-2016
Updated by 		: 	Jahid	
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
echo load_html_head_contents("File Wise Yarn Receive","../../../", 1, 1, $unicode,1,1); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function fn_file_no()
{
	//alert(1);return;
	if( form_validation('cbo_company_name*cbo_file_year','Company Name*File Year')==false )
	{
		return;
	}
	var company_id=$('#cbo_company_name').val();
	var file_year=$('#cbo_file_year').val();
	var page_link='requires/file_wise_yarn_receive_controller.php?action=file_search&company_id='+company_id+'&file_year='+file_year;
	var title='File Search Form';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=535px,height=350px,center=1,resize=1,scrolling=0','../../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var file_ref=this.contentDoc.getElementById("txt_selected_id").value;
		$('#txt_internal_file_no').val(file_ref);
					 
	}
}


function generate_report()
{
	if( form_validation('cbo_company_name*cbo_file_year','Company Name*File Year')==false )
	{
		return;
	}
	
	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_file_year*txt_internal_file_no',"../../../")+'&report_title='+report_title;
	freeze_window(3);
	http.open("POST","requires/file_wise_yarn_receive_controller.php",true);
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
 

function new_window()
{
	//document.getElementById('caption').style.visibility='visible';
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');

	//document.getElementById('caption').style.visibility='hidden';
	d.close(); 
}
function open_mypage_rcvRtn(mrr_no,return_no,lot)
{
        //var company_id=$('#cbo_company_name').val();
	//var file_year=$('#cbo_file_year').val();
	var page_link='requires/file_wise_yarn_receive_controller.php?action=receive_return_popup&mrr_no='+mrr_no+'&return_no='+return_no+'&lot='+lot;
	var title='MRR Details Info';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=535px,height=350px,center=1,resize=1,scrolling=0','../../');
	
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?><br />    		 
    <form name="fileWiseYarnReceive_1" id="fileWiseYarnReceive_1" autocomplete="off" > 
    <h3 style="width:625px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:620px;">
            <legend>Search Panel</legend> 
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th class="must_entry_caption">Company</th>                             
                            <th class="must_entry_caption">File Year</th>
                            <th>File No</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('fileWiseYarnReceive_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/file_wise_yarn_receive_controller', this.value, 'load_drop_down_file_year', 'td_file_year' );" );
                            ?>                            
                        </td>
                        <td id="td_file_year">
                            <input type="text" id="cbo_file_year" name="cbo_file_year" class="text_boxes" style="width:140px"  placeholder="--Year--" />
                        </td>
                        <td>
                            <input type="text" name="txt_internal_file_no" id="txt_internal_file_no" style="width:150px" class="text_boxes" onDblClick="fn_file_no()" readonly="readonly" placeholder="Double Click" />
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:100px" class="formbutton" />
                        </td>
                    </tr>
                </table> 
            </fieldset>  
        </div>
            <div id="report_container" align="center"></div>
        	<div id="report_container2"></div>
            
    </form>    
</div>    
</body>  
<script>
	//set_multiselect('cbo_store_name','0','0','','0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
