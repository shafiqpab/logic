<?
session_start(); 
require_once('includes/common.php');
extract($_REQUEST);
$permission='1_1_1_1';
$_SESSION['page_permission']=$permission;


//--------------------------------------------------------------------------------------------------------------------
//echo load_html_head_contents("Wash Material Issue Info", "", 1,1, $unicode,1,'');
	echo load_html_head_contents("Daily Task Entry","", 1, 1, $unicode);
?>
<script type="text/javascript" charset="utf-8">
    //if( $('#index_page', window.parent.document).val()!=1) window.location.href = "logout.php"; 
    var permission='<? echo $permission; ?>';

    function fnc_issue_details( operation )
    {
            
		if (form_validation('cbo_company_id*cbo_team_leader_id*cbo_team_member_id*txt_issue_details*txt_receive_date*cbo_type*txt_end_minutes','Company*Team Leader*Team Member*Issue Details*Issue Detail*Task Date*Type*Minutes')==false)
		{
			return;
		}

		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_sys_id*txt_issue_details*txt_end_minutes*txt_comments*txt_receive_date*cbo_type*update_id*cbo_company_id*cbo_team_leader_id*cbo_team_member_id','');
		freeze_window(operation);
		http.open("POST","daily_task_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_issue_details_reponse;
        
    }
    
    function fnc_issue_details_reponse()
    {
        if(http.readyState == 4) 
        {
            
            var reponse=trim(http.responseText).split('**');
            release_freezing();
            
            if(reponse[0]==2)
            {
                show_msg(reponse[0])  ;
				reset_form('','','txt_sys_id*txt_issue_details*txt_end_minutes*txt_comments*update_id');
                set_button_status(0, permission, 'fnc_issue_details',1);
            }
            else if(reponse[0]==1)
            {
                show_msg(reponse[0]);
                document.getElementById('update_id').value  = reponse[1];
                document.getElementById('txt_sys_id').value  = reponse[2];
                set_button_status(0, permission, 'fnc_issue_details',1,1);
				reset_form('','','txt_sys_id*txt_issue_details*txt_end_minutes*txt_comments*update_id');
            }
            else if(reponse[0]==0)
            {
                show_msg(reponse[0]) ;
                document.getElementById('update_id').value  = reponse[1];
                document.getElementById('txt_sys_id').value  = reponse[2];
                set_button_status(0, permission, 'fnc_issue_details',1);
				 reset_form('','','txt_sys_id*txt_issue_details*txt_end_minutes*txt_comments*update_id');
            }else {
				reset_form('','','txt_sys_id*txt_issue_details*txt_end_minutes*txt_comments*update_id');
                set_button_status(0, permission, 'fnc_issue_details',1);
            }
        }
    }



     function openmypage_sys_id()
    {
        page_link='daily_task_controller.php?action=openmypage_sys_opup';
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail View', 'width=750px, height=300px, center=1, resize=0, scrolling=0','home');
            emailwindow.onclose=function()
            {
                var menu_info=this.contentDoc.getElementById("hidden_data").value; //alert(menu_info);
                if(menu_info.length){
                    var dataArr=menu_info.split('__');
                    $("#update_id").val(dataArr[0]);
                    $("#txt_sys_id").val(dataArr[1]);
                    $("#txt_receive_date").val(dataArr[6]);
                    $("#cbo_type").val(dataArr[4]);
                    $("#txt_issue_details").val(dataArr[2]);
                    $("#txt_comments").val(dataArr[5]);
                    $("#txt_end_minutes").val(dataArr[3]);
					
                    $("#cbo_company_id").val(dataArr[8]);
                    $("#cbo_team_leader_id").val(dataArr[9]);
                    if(dataArr[9]){
						load_drop_down( 'daily_task_controller',dataArr[9], 'load_team_member', 'td_team_member' );
					}
					
					$("#cbo_team_member_id").val(dataArr[10]);
					
					
					
                    set_button_status(1, permission, 'fnc_issue_details',1);
                    
                }
                
            }

    }

    function fnc_move_cursor(val,id, field_id,lnth,max_val)
        {
            var str_length=val.length;
            if(str_length==lnth)
            {
                $('#'+field_id).select();
                $('#'+field_id).focus();
            }
            if(val>max_val)
            {
                document.getElementById(id).value=max_val;
            }
        }


    function reset_function()
    {
        reset_form('issuedetails_1','list_container','txt_sys_id*update_id*txt_issue_details*txt_end_minutes*txt_receive_date*txt_comments');
        //$('#txt_issue_details').attr('disabled',false);
    }

</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("",$permission,1);  ?>
	<fieldset style="width:750px;">
		<form name="issuedetails_1" id="issuedetails_1" autocomplete="off" >
        
		<table width="740px"  cellpadding="5" cellspacing="5" align="center">
			<tr>
            	<td colspan="3" align="right"><b>Issue ID</b></td>
                <td colspan="3" align="left">
                <input type="text" name="txt_sys_id" id="txt_sys_id" class="text_boxes" style="width:130px" placeholder="Browse" onDblClick="openmypage_sys_id();" readonly />
                <input type="hidden" name="update_id" id="update_id" class="text_boxes" style="width:40px" />
                
                </td>
            </tr>

            
            <tr>
            	<td class="must_entry_caption" align="right">Company Name</td>
                <td>
			   		<?
					$unitStr = return_field_value("UNIT_ID", "USER_PASSWD", "id=".$_SESSION['logic_erp']['user_id']);
					$whereCon=($unitStr!='')?" and id in($unitStr)":"";
					echo create_drop_down( "cbo_company_id", 150, "SELECT id, company_name from lib_company where  status_active =1 and is_deleted=0 $whereCon order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );?>
            	</td>
            	<td class="must_entry_caption" align="right">Team Leader</td>
                <td id="td_team_leader">
	 				<? echo create_drop_down( "cbo_team_leader_id", 150, "select id,team_leader_name from lib_team_mst where project_type=1 and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'daily_task_controller', this.value, 'load_team_member', 'td_team_member' ); " ); ?>
                </td>
	 			<td class="must_entry_caption" align="right">Team Member</td>
                <td id="td_team_member"> 
			   		<? echo create_drop_down( "cbo_team_member_id", 150, $type,"",1, "-- Select --", 2, "",0,"" );?>
                </td>
            </tr>         
            
        	<tr>
            	<td class="must_entry_caption" align="right">Task Date</td>
                <td>
                <input type="text" name="txt_receive_date" value="<?php echo date('d-m-Y');?>" id="txt_receive_date" class="datepicker" style="width:138px" readonly  />
            	</td>
            	<td class="must_entry_caption" align="right">Type</td>
                <td>
			   <? 
			   $type=array(1=>"Idle Time",2=>"Active Time");
 				echo create_drop_down( "cbo_type", 150, $type,"",1, "-- Select --", 2, "",0,"" );?>
	 			</td>
	 			<td class="must_entry_caption" align="right">Task Time</td>
                <td> 
               <input type="text" name="txt_end_minutes" id="txt_end_minutes" class="text_boxes_numeric" placeholder="Minutes" style="width:138px;" onKeyUp="fnc_move_cursor(this.value,'txt_end_minutes','txt_end_date',2,1000)" />
                </td>
            </tr>
            <tr>
                <td align="right" class="must_entry_caption" >Task Details </td>
                <td colspan="5" align="left"><textarea id="txt_issue_details" cols="4" name="txt_issue_details" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:96%; height:50px" placeholder="Issue Task Details Here" ></textarea></td>
            </tr>
            

	        <tr>
	            	
	            	<td align="right"  width="100">Comments</td>
	                <td align="left" colspan="5" ><input type="text" name="txt_comments" id="txt_comments" class="text_boxes" style="width:96%" /></td>
	            	
	         </tr>
            <tr>
            	<td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6"  valign="bottom" align="center" class="button_container">
                    <? 
                    
                        echo load_submit_buttons( $permission, "fnc_issue_details", 0,0,"reset_function()",1);
						
                    ?>
                   
                </td>		 
            </tr>
		</table>
		</form>
	</fieldset>
	</div>
</body>
<script src="includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

?>








