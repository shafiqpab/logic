<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("User Creation","../", 1, 1, $unicode,'','');
 /*
$fieldlevel_arr[1][1]['name']="User ID";
$fieldlevel_arr[1][1]['id']="txt_user_id";

$fieldlevel_arr[1][2]['name']="Password";
$fieldlevel_arr[1][2]['id']="txt_passwd";

$fieldlevel_arr[1][3]['name']="Confirm Password";
$fieldlevel_arr[1][3]['id']="txt_conf_passwd";

$fieldlevel_arr[1][4]['name']="Full User Name";
$fieldlevel_arr[1][4]['id']="txt_full_user_name";

$fieldlevel_arr[1][5]['name']="Desig";  
$fieldlevel_arr[1][5]['id']="cbo_designation";

$fieldlevel_arr[1][6]['name']="User Level";
$fieldlevel_arr[1][6]['id']="cbo_user_level";

$fieldlevel_arr[1][7]['name']="Unit Name";
$fieldlevel_arr[1][7]['id']="cbo_unit_name_show";

$fieldlevel_arr[1][8]['name']="Buyer Name";
$fieldlevel_arr[1][8]['id']="cbo_user_buyer_show";

$fieldlevel_arr[1][9]['name']="Data Level Security";
$fieldlevel_arr[1][9]['id']="cbo_data_level_sec";

$fieldlevel_arr[1][10]['name']="Bind to IP";
$fieldlevel_arr[1][10]['id']="txt_ip_addres";

$fieldlevel_arr[1][11]['name']="Expiry Date";
$fieldlevel_arr[1][11]['id']="txt_exp_date";

$fieldlevel_arr[1][12]['name']="Status";
$fieldlevel_arr[1][12]['id']="cbo_user_sts";

$fieldlevel_arr[2][1]['id']="Sales Qty";
$fieldlevel_arr[2][2]['name']="sales qnty";
*/
?>
	<script language="javascript">
        if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  	 
        var permission='<? echo $permission; ?>';
            
        function openmypage(page_link,title)
        {
            if (title=="Company Selection")
                var data=document.getElementById('cbo_unit_name').value;
            else
                var data= document.getElementById('cbo_user_buyer').value;
                
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+"&data="+data, title, 'width=630px,height=330px,center=1,resize=0,scrolling=0',' ')
            
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
                var order_id=this.contentDoc.getElementById("txt_selected_id")
                var order_no=this.contentDoc.getElementById("txt_selected") //Access form field with id="emailfield"
                if (title=="Company Selection")
                {
                    document.getElementById('cbo_unit_name_show').value=order_no.value;	
                    document.getElementById('cbo_unit_name').value=order_id.value;	
                }
                else
                {
                    document.getElementById('cbo_user_buyer_show').value=order_no.value;	
                    document.getElementById('cbo_user_buyer').value=order_id.value;	
                }
                 
            }
        }
    
        function fnc_user_creation(operation)
        {
            if (form_validation('txt_user_id*txt_passwd*txt_full_user_name*cbo_designation*txt_conf_passwd*cbo_user_level','User Name*Password*Confirm Password*User Level')==false)
            {
                return;
            }
            else if (trim(document.getElementById('txt_passwd').value)!=trim(document.getElementById('txt_conf_passwd').value))	
            {
                $('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
                 { 
                    $(this).html('Password and Confirm Password Should be Same.').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
                     document.getElementById('txt_conf_passwd').focus();
                 });
            }
            else
            {
                eval(get_submitted_variables('txt_user_id*txt_passwd*txt_full_user_name*cbo_designation*cbo_user_level*cbo_user_buyer*cbo_data_level_sec*cbo_unit_name*txt_ip_addres*txt_exp_date*cbo_user_sts*update_id*cbo_department'));
                var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_user_id*txt_passwd*txt_full_user_name*cbo_designation*cbo_user_level*cbo_user_buyer*cbo_data_level_sec*cbo_unit_name*txt_ip_addres*txt_exp_date*cbo_user_sts*update_id*cbo_department',"../");
                //alert(data);return;
                freeze_window(operation);
                http.open("POST","requires/user_creation_controller.php",true);
                http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                http.send(data);
                http.onreadystatechange = fnc_on_submit_reponse;
            }
        }
        function fnc_on_submit_reponse()
        {
            if(http.readyState == 4) 
            {
                var reponse=trim(http.responseText).split('**');
                //alert(http.responseText);release_freezing();return;
                //if (reponse[0].length>2) reponse[0]=10;
                if(reponse[0]==0 || reponse[0]==1)
                {
                    show_msg(reponse[0]);
                    release_freezing();
                    set_button_status(0, permission, 'fnc_user_creation',1);
                }
                else if(reponse[0]==2)
                {
                    show_msg(reponse[0]);
                    reset_form('usercreationform_1','','','','','');
                    release_freezing();
                }
                else
                {
                    show_msg(reponse[0]);
                    release_freezing();
                }
				show_list_view(reponse[1],'show_list_view','show_list_view','requires/user_creation_controller','setFilterGrid("list_view",-1)');
            }
        }
    </script>
</head>
<body onLoad="set_hotkey()">
	 <div align="center">
		 <? echo load_freeze_divs ("../",$permission);  
         $permission_id=sql_select("select * from field_level_access where page_id=1 and user_id=1") ;
         foreach( $permission_id as $tm)
         {
             //echo $fieldlevel_arr[$tm[csf('page_id')]][$tm[csf('field_id')]]['id'];
             $new_arr[$fieldlevel_arr[$tm[csf('page_id')]][$tm[csf('field_id')]]['id']]=$tm[csf('permission_id')];
         }
         // print_r($new_arr);
         ?>
        <script>
            <?
            $new_arr= json_encode( $new_arr );
            echo "var new_arr = ". $new_arr . ";\n";
            ?>
        </script>
        <form name="usercreationform_1" id="usercreationform_1" autocomplete="off">
            <fieldset style="width:550px;">
                <legend> Accounts Information</legend>
                <div style="width:100%;"  align="center">
                    <table width="80%">
                        <tr>
                            <td>User ID</td>
                            <td>
                                <input type="text" name="txt_user_id" tabindex="2" id="txt_user_id" class="text_boxes" style="width:220px" onBlur= "get_php_form_data( this.value, 'populate_user_info', '../tools/requires/user_creation_controller' )" />
                            </td>
                        </tr>
                        <tr>
                            <td>Password</td>
                            <td>
                                <input type="password" name="txt_passwd" id="txt_passwd" tabindex="3" class="text_boxes" style="width:220px"  />
                            </td>
                        </tr>
                        <tr>
                            <td>Confirm Password</td>
                            <td>
                                <input type="password" name="txt_conf_passwd" id="txt_conf_passwd" tabindex="4" class="text_boxes" style="width:220px"  />
                            </td>
                        </tr>
                        <tr>
                            <td>Full User Name</td>
                            <td>
                                <input type="text" name="txt_full_user_name" id="txt_full_user_name" tabindex="4" class="text_boxes" style="width:220px"  />
                            </td>
                        </tr>
                        <tr>
                            <td>Designation</td>
                            <td>
                                <? echo create_drop_down( "cbo_designation", 232, "select id,custom_designation from lib_designation where status_active=1 and is_deleted=0 order by custom_designation","id,custom_designation", 1, "-- Select Designation--", $selected ); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Department</td>
                            <td>
                                <? echo create_drop_down( "cbo_department", 232, "select id,department_name from  lib_department where status_active=1 and is_deleted=0 order by department_name","id,department_name", 1, "-- Select Department--", $selected ); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>User Level</td>
                            <td>
                                <select name="cbo_user_level" id="cbo_user_level" class="combo_boxes" tabindex="5" style="width:232px" >
                                    <option value="0">-- Select --</option>
                                    <option value="1">General User</option>
                                    <option value="2">Admin User</option>
                                    <option value="3">Demo User</option>
                                    <option value="4">Buyer User</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Unit Name</td>
                            <td>
                            <input type="text" name="cbo_unit_name_show" id="cbo_unit_name_show" placeholder="Double Click for Company" class="text_boxes" style="width:220px" onDblClick="openmypage('requires/user_creation_controller.php?action=company_selection_popup','Company Selection')" readonly>
                            <input type="hidden" name="cbo_unit_name" id="cbo_unit_name" class="text_boxes" style="width:220px" readonly >
                            </td>
                        </tr>
                        <tr>
                            <td>Buyer Name</td>
                            <td>
                            <input type="text" name="cbo_user_buyer_show" placeholder="Double Click for Buyer" id="cbo_user_buyer_show" class="text_boxes" style="width:220px" onDblClick="openmypage('requires/user_creation_controller.php?action=buyer_selection_popup','Buyer Selection')" readonly>
                            <input type="hidden" name="cbo_user_buyer" id="cbo_user_buyer" class="text_boxes" style="width:220px" readonly >
                            </td>
                        </tr>
                        <tr>
                            <td>Data Level Security</td>
                            <td>
                                <select name="cbo_data_level_sec" id="cbo_data_level_sec" class="combo_boxes" tabindex="6" style="width:232px">
                                    <option value="0">Access All Data</option>
                                     <option value="1">Limited Access</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Bind to IP </td>
                            <td><input type="text" name="txt_ip_addres" placeholder="Comma Seperate LAN and WAN IP" title="Comma Seperate LAN and WAN IP" id="txt_ip_addres" tabindex="7" class="text_boxes" style="width:220px"></td>
                        </tr>
                        <tr>
                            <td>Expiry Date</td>
                            <td>
                                <input type="text" size="12" name="txt_exp_date" id="txt_exp_date" tabindex="8" class="datepicker"  style="width:80px"/>
                                &nbsp;Status
                                <select name="cbo_user_sts" id="cbo_user_sts" class="combo_boxes" tabindex="9" style="width:92px">
                                    <option value="1">Active</option>
                                    <option value="2">Inactive</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td height="25" valign="middle" class="image_uploader" colspan="2" onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'user_info', 0 ,0)" align="center"><strong>CLICK TO ADD IMAGE</strong> 
                            </td>
                        </tr>
                        <tr>
                            <td  height="30" align="center" valign="bottom" colspan="2"> <input type="hidden" name="update_id" id="update_id"/> 
                                 <? echo load_submit_buttons( $permission, "fnc_user_creation", 0,0 ,"reset_form('usercreationform_1','','')",1); ?>
                            </td>
                        </tr>
                        
                        
                        <tr>
                        	<td colspan="2" align="center" id="show_list_view">
                            	<?
								//ALTER TABLE `user_passwd` ADD `department_id` number( 11 ) NOT NULL DEFAULT '0' AFTER `valid`
									 $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');	
									 $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
									 $arr=array (2=>$custom_designation,3=>$Department);
									 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, "select id, user_name, department_id, user_full_name, designation from  user_passwd where valid=1", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
								?>
                            </td>
                        </tr>
                        
                    </table>
                </div>
            </fieldset>
        </form>
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$.each( new_arr, function( key, value ) {
		if(value==2)
		{  
			var perm="disabled"; 
			$('#'+key).attr('disabled',perm); 
		}
		//else var perm="enabled";
		//alert( key + ": " + value );
	});
</script>
</html>