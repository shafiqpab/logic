<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Report  Setting Previliage.
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	16-05-2015
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
echo load_html_head_contents("Report Setting Previliage Report", "../../", 1, 1,'','','');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	
 
	var permission = '<? echo $permission; ?>';
	
function generate_report()
	{
		if(form_validation('txt_user','User Name')==false)
		{
			return;
		}
		
		//var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate"+get_submitted_data_string("txt_user_id*cbo_page_name","../../");
		freeze_window(3);
		http.open("POST","requires/user_priviledge_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText);
			//alert(http.responseText);return;
			$('#report_container2').html(response);
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			//setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
		}
	}
	
	function openmypage_item()
	{
		
		//var company = $("#cbo_company_name").val();	
		var page_link='requires/user_priviledge_report_controller.php?action=user_name_search'; 
		var title="Search User Name";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=305px,height=460px,overflow-y=hidden,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var user_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var user_description=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_user").val(user_description);
			$("#txt_user_id").val(user_id); 
		}
	}
		
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
    <form id="frm_lc_salse_contact" name="frm_lc_salse_contact">
    <div style="width:660px;">
    <h3 align="left" id="accordion_h1" style="width:660px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
    <div id="content_search_panel"> 
    <fieldset style="width:660px;">
                <table class="rpt_table" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                        <td width="80"  rowspan="2" align="center" style="font-size:14px;" class="must_entry_caption">User Name</td>
                        <td width="150" rowspan="2">
                        	<input type="text" style="width:145px;" name="txt_user" id="txt_user" onDblClick="openmypage_item()"  class="text_boxes" placeholder="Dubble Click For User"  readonly />   
                        <input type="hidden" name="txt_user_id" id="txt_user_id"/>
                        </td>
                        <td width="80"  rowspan="2" align="center">Page Name</td>
                        <td width="150" rowspan="2">
                        <?
							
                        	echo create_drop_down( "cbo_page_name", 155, "select m_menu_id, menu_name from  main_menu","m_menu_id,menu_name", 1, "-- Select Page --", $selected );
						?>	  
                        </td>
                        <td >
                        	<input type="reset" name="res" id="res" value="Reset" style="width:150px" class="formbutton" onClick="reset_form('frm_lc_salse_contact','report_container*report_container2','','','')" />
                        </td>
                    </tr>
                    <tr>
                        <td >
                        	<input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:150px" class="formbutton" />
                        </td>
                    </tr>
                </table>
    </fieldset>
    </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
    </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>