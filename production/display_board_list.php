<?
/*-------------------------------------------- Comments
Purpose			: 	Display Board List

Functionality	:	
JS Functions	:
Created by		:	Rakib Hasan Mondal
Creation date 	: 	22-02-2023
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

$user_id = $_SESSION['logic_erp']["user_id"];
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Display Board List","../", 1, 1, "",'1','');
?>

<script>
    
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	function fnc_iron_input(operation)
	{ 
		if(operation==0 || operation==1 || operation==2)
		{
			if ( form_validation('text_report_name*text_report_link*cbo_status','Repoer Name*Report Link*Status')==false )
			{
				return;
			}		
			else
			{ 	
				freeze_window(operation);
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('text_report_name*text_report_link*cbo_status*update_id',"../");
				http.open("POST","requires/display_board_list_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_display_list_response; 
                 
			}
		}
	} 
    function fnc_display_list_response(){
        if(http.readyState == 4) 
        {  
            var reponse=trim(http.responseText).split('**');
            if(reponse[0]==15) 
            { 
                setTimeout('fnc_iron_input( 0 )',8000); 
            }
            else
            {
              if (reponse[0].length>2) reponse[0]=10; 
              show_list_view('','search_list_view','display_board_list_view','requires/display_board_list_controller','setFilterGrid("list_view",-1)');
                release_freezing();
            }  
        }
    } 
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">

<? echo load_freeze_divs ("../",$permission); ?>
<?php
    $sql = "SELECT a.id,a.report_name,a.report_link,a.status FROM  display_board_list a WHERE a.inserted_by = $user_id and a.status_active = 1 and is_deleted=0 order by id desc";
    $results = sql_select($sql); 
?>
    <form name="displayBoardEntery" id="displayBoardEntery" autocomplete="off" >
        <div style="width:650px; float:center;" align="center">  
            <fieldset style="width:650px;">
                <table cellpadding="0" cellspacing="1" width="420px" border="0">
                    <tr>
                        <td align="center" valign="top">
                            <fieldset> 
                                <table cellpadding="0" cellspacing="1" border="0" rules="all" width="420px">
                                    <thead> 
                                        <td class="must_entry_caption" >Report Name</td>
                                        <td class="must_entry_caption" >Report Link</td>
                                        <td class="must_entry_caption" >Status</td> 
                                    </thead>
									<tbody id="details_entry_list_view">  
                                        <tr> 
                                            <td>
                                                <input class="text_boxes"  name="text_report_name" id="text_report_name" value="" />
                                                <input type="hidden" name="update_id"  id="update_id">
                                            </td>
                                            <td>
                                                <input class="text_boxes"  name="text_report_link" id="text_report_link" value="" />
                                            </td> 
                                            <td>
                                                <?
                                                    echo create_drop_down( "cbo_status", 172, $row_status, "", 1, "-- Select Status --", $selected, "" );
                                                ?> 
                                            </td> 
                                        </tr> 
										
									</tbody>
                                </table>
                            </fieldset> 
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="10" class="button_container">
                            <?
                                echo load_submit_buttons($permission, "fnc_iron_input", 0,0,"reset_form('displayBoardEntery','','','','','')",1);
                                
                            ?> 
                        </td>
                    </tr>
                </table>
            </fieldset>  
        </div>
	</form>  
    <fieldset style="width:580px; margin-top:10px">
        <legend> List View </legend>
        <form>
            <div style="width:580px; margin-top:10px" id="display_board_list_view" align="left">
              
            </div>
        </form>
    </fieldset>  
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
 <script>
    show_list_view('','search_list_view','display_board_list_view','requires/display_board_list_controller','setFilterGrid("list_view",-1)');
    $( "thead tr th" ).dblclick(function() {
        $( "#tbl_scroll_body" ).animate({scrollTop:0}, 'slow');
    });
 </script>
</html>

