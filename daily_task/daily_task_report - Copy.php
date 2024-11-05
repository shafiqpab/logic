<?
/*-------------------------------------------- Comments

Purpose			: 	Daily Task Entry (CRM)
					
Functionality	:	
				

JS Functions	:

Created by		:	Md. Helal Uddin
Creation date 	: 	20-06-2020
Updated by 		: 		
Update date		: 		   

QC Performed BY	:		

QC Date			:	

Comments		:

*/

session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$menu_id=$_SESSION['menu_id'];
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Daily Task Entry", "../", 1, 1,'','','');



?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	
	
	
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1">

         <h3 style="width:1011px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:800px;">
             	<input type="hidden" name="txt_style_ref" id="txt_style_ref">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                       
                        <tbody>
                            <tr class="general">
                                <td> 
                                   <textarea id="txt_issue_details" cols="4" name="txt_issue_details" class="text_area" maxlength="1000" style="width:730px; height:50px" placeholder="Issue Task Details Here"></textarea>
                                </td>
                                
                            </tr>
                            <tr>
                            	
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
    
</body>
<script src="includes/functions_bottom.js" type="text/javascript"></script>
<script>

</script>
</html>