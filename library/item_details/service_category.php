<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Service Category", "../../", 1, 1,$unicode,'','');

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

		var permission='<? echo $permission; ?>';	
			
	function fnc_service_cat_info(operation)
	{
		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][732]);?>')
		{
			if (form_validation('<? echo implode('*', $_SESSION['logic_erp']['mandatory_field'][732]);?>', '<? echo implode('*', $_SESSION['logic_erp']['field_message'][732]);?>')==false)
			{
				return;
			}
		}
		 
		if (form_validation('txt_service_category*txt_service_name','Service category*Service Name')==false)
		{
			return;
		}
		else
		{
			

			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_service_code*txt_service_category*txt_service_group*update_id*txt_service_name*cbo_status',"../../");
		
			freeze_window(operation);
			
			http.open("POST","requires/service_category_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_service_cat_info_response;
		}
	}

	function fnc_service_cat_info_response()
	{
		if(http.readyState == 4) 
		{
			//release_freezing(); return;
			var reponse=trim(http.responseText).split('**');
		
			if(reponse[0]==15) 
			{ 
				setTimeout('fnc_service_cat_info( 0 )',8000); 
			}
			else if (reponse[0] == 22) 
			{
				alert(reponse[1]);
				return;
			}
			else
			{
				if (reponse[0].length>2) reponse[0]=10;
				show_msg(reponse[0]);
				show_list_view('','search_list_view','service_cat_list_view','requires/service_category_controller','setFilterGrid("list_view",-1)');
				// document.getElementById('update_id').value  = response[2];
				reset_form('service_cat_1','','','','','');
				set_button_status(0, permission, 'fnc_service_cat_info',1);	
				release_freezing(); 
			}
		}
	}
	</script>
</head>

<body onLoad="set_hotkey()">
<div align="center">
     <? echo load_freeze_divs ("../../", $permission);  ?>
	<fieldset style="width:400px;">
		
		<form name="service_cat_1" id="service_cat_1">	
			<table cellpadding="0" cellspacing="2" width="100%">

           <tr>
            <th class="txt_service_code">Service Code</th>
            <th class="txt_service_group">Service Group</th>
            <th class="must_entry_caption">Service Category</th>
            <th class="must_entry_caption">Service Name</th>
            <th>Status</th>
           </tr>
			 <tr>
					
             <td >
                <input type="text" name="txt_service_code" id="txt_service_code" class="text_boxes" placeholder="Write"value=""  />
             </td>
             <td >
                <input type="text" name="txt_service_group" id="txt_service_group" class="text_boxes" placeholder="Write" value="" />
             </td>
             <td >
                <input type="text" name="txt_service_category" id="txt_service_category" class="text_boxes" placeholder="Write" value=""  />
             </td>
             <td >
                <input type="text" name="txt_service_name" id="txt_service_name" class="text_boxes" placeholder="Write"  value=""/>
             </td>
             
             <td valign="top">
                <?
                    echo create_drop_down( "cbo_status", 110, $row_status,"", "", "", 1, "" );
                ?> 
                <input type="hidden" name="update_id" id="update_id" >
            </td>  					
            </td>
			</tr>	
				<tr>
				 
				<td colspan="4" align="center" style="padding-top:10px;" class="button_container">
				<?
				echo load_submit_buttons( $permission, "fnc_service_cat_info", 0,0 ,"reset_form('service_cat_1','','','','')",1);
				
				
				?>
				</td>				
				</tr>
				<tr>
			  		<td height="16" colspan="4"></td>
			  	</tr>
			</table>
		</form>	
	</fieldset>
	<fieldset style="width:300px; margin-top:10px">
		<legend>List View</legend>
		<form>
			<div style="width:600px; margin-top:10px" id="service_cat_list_view" align="left">
                            <?
			$arr=array (4=>$row_status);
			echo  create_list_view ( "list_view,tbl_scroll_body","Service Code,Service Group,Service Category,Service Name,Status", "100,150,100,100,50","600","220",0,  "select id,service_code,service_group,service_category,service_name,status_active from lib_service_category where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,0,0,status_active", $arr,"service_code,service_group,service_category,service_name,status_active", "../item_details/requires/service_category_controller", 'setFilterGrid("list_view",-1);' ) ;
							 ?>
            </div>
		</form>
	</fieldset>	
</div>
  
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

<script>
 


$( "thead tr th" ).dblclick(function() {
  $( "#tbl_scroll_body" ).animate({scrollTop:0}, 'slow');
});
 
</script>

<script>
<?
if(implode('*', $_SESSION['logic_erp']['mandatory_field'][732])) 
{
	$json_mandatory_field = json_encode($_SESSION['logic_erp']['mandatory_field'][732]);
	echo "var mandatory_field_arr= " . $json_mandatory_field . ";\n";
}
?>
if('<? echo implode('*', $_SESSION['logic_erp']['mandatory_field'][732]);?>')
{
	$.each(mandatory_field_arr, function(key, value){
		$(("."+value)).css("color", "blue");
	})
}
</script>

</html>
