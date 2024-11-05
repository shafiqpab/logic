<?php
/********************************* Comments *************************
*	Purpose			: 	This Form Will Create Dynamic Letter
*	Functionality	:	
*	JS Functions	:
*	Created by		:	Md. Nuruzzaman 
*	Creation date 	: 	05-10-2015
*	Updated by 		: 		
*	Update date		: 		   
*	QC Performed BY	:	Mohammad Abdul Kaiyum	
*	QC Date			:	04-11-2015
*	Comments		:


*********************************************************************/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Dynamic Letter", "../../", 1, 1,$unicode,'','');
?>
	<script type="text/javascript" src="../../ckeditor/ckeditor.js"></script>
	<script>
        if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
        var permission='<? echo $permission; ?>';

		function fnc_submit_dynamic_letter(operation)
		{
			//alert("su..re");
			if (form_validation('cbo_letter_type','Letter Type')==false)
			{
				return;
			}
			
			var txt_letter_body=editor.getData();
			txt_letter_body=txt_letter_body.split('&').join('**'); // replace("&","**");
			txt_letter_body=txt_letter_body.split('?').join('****'); // replace("&","**");
			txt_letter_body=txt_letter_body.replace(/<div.*>/gi, "\n");
			
			var data="action=save_update_delete&operation="+operation+'&txt_letter_body='+txt_letter_body+get_submitted_data_string('cbo_letter_type*update_id',"../../");
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/dynamic_letter_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_reponse;
		}
		
        function fn_reponse()
        {
            if(http.readyState == 4) 
            {
                //alert(http.responseText); //return; 
                var reponse=trim(http.responseText).split('**');
                show_msg(reponse[0]);
				$('#update_id').val('');
				$('#cbo_letter_type').val(0);
				editor.setData('');
                set_button_status(0, permission, 'fnc_submit_dynamic_letter',1);
				location.reload();
                release_freezing();
            }
        }
		
		function fnc_set_value(id, letter_type )
		{
			//alert("su..re");
			$('#update_id').val(id);
			$('#cbo_letter_type').val(letter_type);
			editor.setData( $('#txt_hidden'+id).val());
			set_button_status(1, permission, 'fnc_submit_dynamic_letter',1);	
		}
		
		function reset_formss()
		{
			//alert("su..re");
			$('#update_id').val('');
			$('#cbo_letter_type').val(0);
			editor.setData('');
		}
    </script>
</head>
<body onLoad="set_hotkey()">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div align="center" style="width:100%;">
        <fieldset style="width:100%;">
         <legend>Dynamic Letter</legend>
            <form name="dynamicLetter_1" id="dynamicLetter_1" autocomplete = "off">	
                <table cellpadding="0" cellspacing="2" width="100%">
                     <tr>
                     	<td align="center">Letter Type : 
						<? 
							echo create_drop_down( "cbo_letter_type", 150, $letter_type_arr,"", 1, "-- select --", 0, "" ); 
						?>
                        </td>
                     </tr>
                     <tr>
                        <td><input type="text" id="txt_letter_body" name="txt_letter_body" /></td>
                    </tr>
                    <tr>
                        <td>&nbsp;<input type="hidden" name="update_id" id="update_id"></td>
                    </tr>
                    <tr>
                          <td align="center" class="button_container">
                           <? 
                           //echo load_submit_buttons( $permission, "fnc_submit_dynamic_letter",0,0 ,"reset_form('dynamicLetter_1','','')",1);
						   echo load_submit_buttons( $permission, "fnc_submit_dynamic_letter",0,0 ,"reset_formss()",1);
                          ?>                   
                           </td>
                    </tr>
            </table>
            </form>
        </fieldset>
        <fieldset style="margin-top:2px">
        <legend>Dynamic Letter List View</legend>
            <table width="270" cellpadding="0" cellspacing="0" border="0" class="rpt_table" rules="all">        
                <thead> 
                    <th width="50">SL No</th>
                    <th>Letter Type</th>
                </thead>
            </table>
            <div style="max-height:200px; width:270px; overflow-y:scroll" id="">
                <table id="list_view" width="250" height="" cellpadding="0" cellspacing="0" border="0" class="rpt_table" rules="all">
                    <tbody>
						<?php
						
						/*$con = connect();
						$query = "select id, letter_type, letter_body from dynamic_letter where status_active=1 and is_deleted=0";
						$stmt = oci_parse ($con, $query);
						$arr=oci_execute($stmt, OCI_DEFAULT);
						$arr=ocifetchinto($stmt, $arr, OCI_ASSOC);
						echo $arr['letter_body']->load(); */
						
                        $array=sql_select("select id, letter_type, letter_body from dynamic_letter where status_active=1 and is_deleted=0");
						
                        
						
						$sl=0;
                        foreach($array as $row)
                        {
                            $sl++;
                            ?>
                            <tr id="tr_<?php echo $sl; ?>" height="20" bgcolor="<?php echo ($sl%2==0)?"#E9F3FF":"#FFFFFF"; ?>" onClick="fnc_set_value('<?php echo $row[csf('id')]; ?>','<?php echo $row[csf('letter_type')]; ?>')" style="cursor:pointer;"> 
                                <td width="50"><?php echo $sl; ?>
                                <textarea style="display:none;" id="txt_hidden<? echo $row[csf('id')]; ?>"  ><? if($row[csf('letter_body')]->load()!="") echo $row[csf('letter_body')]->load(); ?></textarea>
                                </td>
                                <td><?php echo $letter_type_arr[$row[csf('letter_type')]]; ?></td>
                            </tr>
                            <?php 
                        }
                        ?>            
                    </tbody>
            </table>
        </div> 
		</fieldset>
        </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script> var editor = CKEDITOR.replace('txt_letter_body');</script>
</html>
