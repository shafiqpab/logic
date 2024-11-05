<?php
/******************************************************************
|	Purpose			:	
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md Mahbubur Rahman
|	Creation date 	:	17.01.2020
|	Updated by 		: 		
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
********************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Item Category List","../../", 1, 1, "",'1','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	function fnc_category_list(operation)
	{
		if(operation==2)
		{
			 alert('You have no permission to delete it.');
			 return;
		}
		if(operation==0)
		{
			 alert('You have no permission to Save it.');
			 return;
		}
		
		if (form_validation('txt_Category_id*txt_Category_short_name','Category Name*Short Name')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete_category&operation="+operation+get_submitted_data_string('txt_Category_id*txt_Category_short_name*hidden_Category_id*update_id*cbo_status',"../../");
			//alert(data);
			freeze_window(operation);
			http.open("POST","requires/item_category_list_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_category_list_save_Reply_info;
		}
	}

function fnc_category_list_save_Reply_info()
{
	if(http.readyState == 4) 
	{
		var response=http.responseText.split('**');	
		//alert(http.responseText);
		show_msg(trim(response[0]));
		if(response[0]==20)
		{
			alert(response[1]);
			release_freezing();return; 
		}
		show_list_view('','category_list_view','category_list_view','requires/item_category_list_controller','setFilterGrid("list_view",-1)');
		document.getElementById('update_id').value  = response[2];
		set_button_status(1, permission, 'fnc_category_list',1);	
		release_freezing(); 
	}
	
}
	function contry_dis(){
	$('#txt_Category_id').attr('disabled','disabled');
	}

</script>
</head>
<body  onload="set_hotkey();contry_dis();">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div align="center" style="width:80%; margin-left:120px;">	
		<form name="cutoff_1" id="cutoff_1"  autocomplete="off">
            <fieldset style="width:500px;"><legend>Country Cut Off</legend>
            <table cellpadding="0" cellspacing="10" width="500" height="50" align="center">
                <tr>
                
                    <td width="100" align="right" class="must_entry_caption">Item Category:</td>
                    <td width="150"><input type="text" id="txt_Category_id" name="txt_Category_id" class="text_boxes" style="width:120" />
                    <input type="hidden" id="hidden_Category_id" name="hidden_Category_id" class="text_boxes" />
                    <input type="hidden" id="update_id" name="update_id" class="text_boxes" readonly /></td>
                    <td width="100" align="right" class="must_entry_caption">Short Name</td>
                    <td>
						<input type="text" id="txt_Category_short_name" name="txt_Category_short_name" class="text_boxes" style="width:120" />
                    </td>
                </tr>
                <tr>
                	<td align="right" class="must_entry_caption">Status:</td>
                    <td>
                    <?
						echo create_drop_down( "cbo_status", 135, $row_status,'', $is_select, $select_text, 1, $onchange_func, "",'','' ); 
					?>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                	<td colspan="4"></td>
                </tr>
                <tr>
                	<td colspan="4" align="center" style="padding-top:10px;" class="button_container">
                    <?
					echo load_submit_buttons( $permission, "fnc_category_list", 1,0 ,"reset_form('cutoff_1','','','','')",1) ; 
					?>
                    </td>
                </tr>
            </table>
            <br>
	 		</fieldset>
         <fieldset style="width:500px;min-height:200">
                 <table width="500" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="50">SL</th>
                    <th width="180">Main Item Category</th>   
                    <th width="180">Update Category Name</th>
                    <th>Status</th>
                </thead>
            </table>
                <div style="max-height:340px; overflow:auto;" id="category_list_view">
                <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
                 <? 
                  
				$category_sql="select id,actual_category_name, category_id, short_name, status_active from lib_item_category_list order by category_id"; 
				$item_category_result=sql_select($category_sql);
					
					 $i=1;
                     foreach($item_category_result as $row)
                     { 
                        if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
						
						$actual_category_name=$row[csf('actual_category_name')];
						$category_id=$row[csf('category_id')];
						$shortname=$row[csf('short_name')];
						
						if($shortname!="")
						{
							$shortname=$shortname;
						}
						else
						{
							$shortname=$actual_category_name;
						}
						
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $category_id;?>","load_item_category","requires/item_category_list_controller")' style="cursor:pointer" >
                            <td width="50"><?php echo $category_id; ?></td>
                            <td width="180"><p><?php echo $actual_category_name; ?></p></td>
                            <td width="180"><p><?php echo $shortname; ?></p></td>
                            <td><p><?php echo $row_status[$row[csf('status_active')]]; ?></p></td>
                        </tr>
                        <? $i++; 
                    } 
                    ?>
                </table>
                </div>
                <script> setFilterGrid("table_body2",-1); </script>
            </fieldset>
    </form>
	
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
