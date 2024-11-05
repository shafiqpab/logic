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
            {
                var data=document.getElementById('cbo_unit_name').value;
                var data2="";
            }
            else if (title=="Working Selection")
            {
                var data=document.getElementById('cbo_working_name').value;
                var data2="";
            }
            else
            {
                var data= document.getElementById('cbo_user_buyer').value;
                var data2=document.getElementById('cbo_unit_name').value;
            }
             
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+"&data="+data+"&unit_name="+data2, title, 'width=630px,height=330px,center=1,resize=0,scrolling=0','');
            
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
                var order_id=this.contentDoc.getElementById("txt_selected_id")
                var order_no=this.contentDoc.getElementById("txt_selected") //Access form field with id="emailfield"
                if (title=="Company Selection")
                {
                    document.getElementById('cbo_unit_name_show').value=order_no.value;	
                    document.getElementById('cbo_unit_name').value=order_id.value;

                    var old_unit=data.split(",");
                    var new_unit_arr=order_id.value.split(",");
                    for (var i = 0; i < old_unit.length; i++) 
                    {
                        if(jQuery.inArray(old_unit[i],new_unit_arr)==-1)
                        {
                            document.getElementById('cbo_user_buyer_show').value='';   
                            document.getElementById('cbo_user_buyer').value=''; 
                            alert('Please click on Buyer popup to reset buyer priviledge.');
                            return;
                        }
                    }
                }
                else if(title=="Working Selection")
                {
                    document.getElementById('cbo_working_name_show').value=order_no.value;  
                    document.getElementById('cbo_working_name').value=order_id.value; 
                }
                else
                {
                    document.getElementById('cbo_user_buyer_show').value=order_no.value;	
                    document.getElementById('cbo_user_buyer').value=order_id.value;	
                }
            }
            
        }
		
		function openmypage_brand(page_link,title)
        {
           
                var data= document.getElementById('cbo_user_brand').value;
				var cbo_user_buyer= document.getElementById('cbo_user_buyer').value;
               // var data2=document.getElementById('cbo_unit_name').value;
			   // var cbo_user_buyer= document.getElementById('cbo_user_buyer').value;
               // var data2=document.getElementById('cbo_unit_name').value;
			   if(cbo_user_buyer=="")
			   {
				   if(form_validation('cbo_user_buyer_show','Buyer')==false)
					{
						return;
					}
			   }
           
             
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+"&data="+data+"&cbo_user_buyer="+cbo_user_buyer, title, 'width=630px,height=330px,center=1,resize=0,scrolling=0','');
            
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
                var order_id=this.contentDoc.getElementById("txt_selected_id")
                var order_no=this.contentDoc.getElementById("txt_selected") //Access form field with id="emailfield"
                if (title=="Brand Selection")
                {
                    document.getElementById('cbo_user_brand_show').value=order_no.value;	
                    document.getElementById('cbo_user_brand').value=order_id.value;	
                }
            }
            
        }
    
        function fnc_user_creation(operation)
        {
            if (trim(document.getElementById('txt_passwd').value)!=trim(document.getElementById('txt_conf_passwd').value) && operation != 1 ) 
            {
                $('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
                 { 
                    $(this).html('Password and Confirm Password Should be Same.').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
                     document.getElementById('txt_conf_passwd').focus();
                 });
                return;
            }
		
            if (form_validation('txt_user_id*txt_full_user_name*cbo_designation*cbo_user_level','User Name*Full User Name*Designation*User Level')==false)
            {
                return;
            }
            
            else
            {
                eval(get_submitted_variables('txt_user_id*txt_passwd*txt_full_user_name*cbo_designation*cbo_user_level*cbo_user_buyer*cbo_data_level_sec*cbo_unit_name*txt_ip_addres*txt_lan_mac_address*txt_exp_date*cbo_user_sts*txt_email*update_id*cbo_department*cbo_graph_id*cbo_user_brand*cbo_single_user_id*txt_norsel_weight_api*txt_norsel_printer*txt_norsel_printer_api*cbo_weight_scale_type'));
               
			    var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_user_id*txt_passwd*txt_full_user_name*cbo_designation*cbo_user_level*cbo_user_buyer*cbo_data_level_sec*cbo_unit_name*txt_ip_addres*txt_lan_mac_address*txt_exp_date*cbo_user_sts*txt_email*txt_mobile_number*update_id*cbo_department*cbo_graph_id*txt_employee_code*txt_conf_passwd*cbo_single_user_id*cbo_user_brand*cbo_working_name*txt_norsel_weight_api*txt_norsel_printer*txt_norsel_printer_api*cbo_weight_scale_type',"../");
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
                if(reponse[0]==0 || reponse[0]==1)
                {
                    show_msg(reponse[0]);
                    release_freezing();
                    reset_form('usercreationform_1','','','cbo_data_level_sec,1','','');
                    set_button_status(0, permission, 'fnc_user_creation',1);
                }

                else if(reponse[0]==20)
                {
                    alert(reponse[1]);
                    release_freezing();
                    return;
                }
                else if(reponse[0]==2)
                {
                    show_msg(reponse[0]);
                    reset_form('usercreationform_1','','','cbo_data_level_sec,1','','');
                    release_freezing();
                }
                else
                {
                    show_msg(reponse[0]);
                    release_freezing();
                }
					var data_level_sec_id=document.getElementById('cbo_data_level_sec').value;
				//alert(data_level_sec_id);
				//if(data_level_sec_id==1)
				//{
					fnc_log_user(data_level_sec_id);
				//}
				show_list_view(reponse[1],'show_list_view','show_list_view','requires/user_creation_controller','setFilterGrid("list_view",-1)');
            }
        }
		
	function fn_get_hrm_info(hrm_id){
		freeze_window(0);
		get_php_form_data(hrm_id, "populate_emp_info", "requires/user_creation_controller" );
		release_freezing();
	}
		
		
		function fnc_log_user(type)
		{
		//alert(type);
		
		if(type==1)
		{
			$("#log_user").show();
			$("#cbo_single_user_id").val(0);
		}
		else $("#log_user").hide();
		 
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
                    <table width="100%" border="1">
                        <tr>
                            <td width="120" class="must_entry_caption">User ID</td>
                            <td width="200"><input type="text" name="txt_user_id" tabindex="2" id="txt_user_id" class="text_boxes" style="width:190px" onBlur= "get_php_form_data( this.value, 'populate_user_info', '../tools/requires/user_creation_controller' )" /></td>
                            <td rowspan="13" style="width:200px;" valign="top">
                            	<img id="user_image" src="../img/userprofile.png" style="border:1px solid #FFF; border-radius:3px; max-height:300px; max-width:200px;" alt="No Image"> 
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Password</td>
                            <td><input type="password" name="txt_passwd" id="txt_passwd" tabindex="3" class="text_boxes" style="width:190px" /></td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Confirm Password</td>
                            <td><input type="password" name="txt_conf_passwd" id="txt_conf_passwd" tabindex="4" class="text_boxes" style="width:190px"  /></td>
                        </tr>
                        <tr>
                            <td>Employee Code</td>
                            <td><input type="text" name="txt_employee_code" id="txt_employee_code" tabindex="3" class="text_boxes" style="width:190px" onChange="fn_get_hrm_info(this.value);" /></td>
						</tr>
                        <tr>
                            <td>Email</td>
                            <td><input type="text" name="txt_email" id="txt_email" tabindex="3" class="text_boxes" style="width:190px" /></td>
						</tr>
                        <tr>
                            <td>Mobile Number</td>
                            <td><input type="text" name="txt_mobile_number" id="txt_mobile_number" tabindex="3" class="text_boxes" style="width:190px" /></td>
						</tr>
                        <tr>
                            <td class="must_entry_caption">Full User Name</td>
                            <td><input type="text" name="txt_full_user_name" id="txt_full_user_name" tabindex="4" class="text_boxes" style="width:190px" /></td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Designation</td>
                            <td><?=create_drop_down( "cbo_designation", 200, "select id,custom_designation from lib_designation where status_active=1 and is_deleted=0 order by custom_designation ASC","id,custom_designation", 1, "-- Select Designation--", $selected ); ?></td>
                        </tr>
                        <tr>
                            <td>Department</td>
                            <td><?=create_drop_down( "cbo_department", 200, "select id,department_name from  lib_department where status_active=1 and is_deleted=0 order by department_name","id,department_name", 1, "-- Select Department--", $selected ); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">User Level</td>
                            <td>
                                <select name="cbo_user_level" id="cbo_user_level" class="combo_boxes" tabindex="5" style="width:200px" >
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
                            <td><input type="text" name="cbo_unit_name_show" id="cbo_unit_name_show" placeholder="Double Click for Company" class="text_boxes" style="width:190px" onDblClick="openmypage('requires/user_creation_controller.php?action=company_selection_popup','Company Selection')" readonly>
                            <input type="hidden" name="cbo_unit_name" id="cbo_unit_name" class="text_boxes" style="width:100px" readonly >
                            </td>
                        </tr>
                        <tr>
                            <td>Working Unit</td>
                            <td><input type="text" name="cbo_working_name_show" id="cbo_working_name_show" placeholder="Double Click for Company" class="text_boxes" style="width:190px" onDblClick="openmypage('requires/user_creation_controller.php?action=company_selection_popup','Working Selection')" readonly>
                            <input type="hidden" name="cbo_working_name" id="cbo_working_name" class="text_boxes" style="width:100px" readonly >
                            </td>
                        </tr>
                        <tr>
                            <td>Buyer Name</td>
                            <td>
                                <input type="text" name="cbo_user_buyer_show" placeholder="Double Click for Buyer" id="cbo_user_buyer_show" class="text_boxes" style="width:190px" onDblClick="openmypage('requires/user_creation_controller.php?action=buyer_selection_popup','Buyer Selection');" readonly>
                                <input type="hidden" name="cbo_user_buyer" id="cbo_user_buyer" class="text_boxes" style="width:100px" readonly >
                            </td>
                        </tr>
                         <tr>
                            <td>Brand Name</td>
                            <td>
                            <input type="text" name="cbo_user_brand_show" placeholder="Double Click for Brand" id="cbo_user_brand_show" class="text_boxes" style="width:190px" onDblClick="openmypage_brand('requires/user_creation_controller.php?action=brand_selection_popup','Brand Selection');" readonly>
                            <input type="hidden" name="cbo_user_brand" id="cbo_user_brand" class="text_boxes" style="width:100px" readonly >
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Data Level Security</td>
                            <td>
                                <select name="cbo_data_level_sec" id="cbo_data_level_sec" onChange="fnc_log_user(this.value);" class="combo_boxes" tabindex="6" style="width:100px">
                                    <option value="0">Access All Data</option>
                                    <option value="1">Limited Access</option>
                                </select>
                                &nbsp; &nbsp;
                                <b style="display:none" id="log_user"><!--Kausar-->
                                <select name="cbo_single_user_id" id="cbo_single_user_id"  class="combo_boxes" tabindex="7" style="width:80px">
                                    <option value="0" selected>--Select--</option>
                                    <option value="1">Single user data[For Brand]</option>
                                    <option value="2">Lab Dip Approval V2</option>
                                </select>
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td>Home Graph</td>
                            <td>
                                 <? 
								 	foreach($home_graph_arr as $key=>$val){
										$graph_arr[$key]=ucfirst(str_replace(array('_','.php'),array(' ',''),$val));	
									}
									
									echo create_drop_down( "cbo_graph_id", 200, $graph_arr,"", 1, "-- Select --", $selected );
								  ?>
                            </td>
                            <td rowspan="5" style="width:200px;" valign="top">
                            	<img id="user_signature" src="../img/userprofile.png" style="border:1px solid #FFF; border-radius:3px; max-height:100px; max-width:200px;" alt="No Signature"> 
                            </td>
                        </tr>
                        <tr>
                            <td>Bind to IP </td>
                            <td><input type="text" name="txt_ip_addres" placeholder="Comma Seperate LAN and WAN IP" title="Comma Seperate LAN and WAN IP" id="txt_ip_addres" tabindex="7" class="text_boxes" style="width:190px"></td>
                        </tr>
                        <tr>
                            <td>LAN MAC Address</td>
                            <td><input type="text" name="txt_lan_mac_address" id="txt_lan_mac_address" tabindex="8" class="text_boxes" style="width:190px"></td>
                        </tr>
                        <tr>
                            <td>Expiry Date</td>
                            <td>
                                <input type="text" size="12" name="txt_exp_date" id="txt_exp_date" tabindex="9" class="datepicker"  style="width:65px"/>
                                &nbsp;Status
                                <select name="cbo_user_sts" id="cbo_user_sts" class="combo_boxes" tabindex="10" style="width:80px">
                                    <option value="1">Active</option>
                                    <option value="2">Inactive</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="button" class="image_uploader" style="width:120px" value="CLICK TO ADD IMAGE" onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'user_info', 0 ,0)"></td>
                            <td><input type="button" class="image_uploader" style="width:200px" value="ADD Signature" onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'user_signature', 0 ,0)"></td>
                        </tr>
						<tr>
                            <td>Weight Scale Type</td>
                            <td>
								<?
								$scale_type = array(1=>'Norsel',2=>'SSL');
									echo create_drop_down( "cbo_weight_scale_type", 200, $scale_type,"", 1, "-- Select --", $selected );
								?>
							</td>
                        </tr>
						<tr>
                            <td>Norsel weight scale API</td>
                            <td><input type="text" name="txt_norsel_weight_api" id="txt_norsel_weight_api" tabindex="8" class="text_boxes" style="width:190px"></td>
                        </tr>
						<tr>
                            <td>Norsel printer</td>
                            <td><input type="text" name="txt_norsel_printer" id="txt_norsel_printer" tabindex="8" class="text_boxes" style="width:190px"></td>
                        </tr>
						<tr>
                            <td>Norsel printer API</td>
                            <td><input type="text" name="txt_norsel_printer_api" id="txt_norsel_printer_api" tabindex="8" class="text_boxes" style="width:190px"></td>
                        </tr>
                        <tr>
                            <td  height="30" align="center" valign="bottom" colspan="2"> <input type="hidden" name="update_id" id="update_id"/> 
                                 <?=load_submit_buttons( $permission, "fnc_user_creation", 0,0 ,"reset_form('usercreationform_1','','','cbo_data_level_sec,1')",1); ?>
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="3" align="center" id="show_list_view">
                            	<?
								//ALTER TABLE `user_passwd` ADD `department_id` number( 11 ) NOT NULL DEFAULT '0' AFTER `valid`
									 $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');	
									 $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
									 $arr=array (2=>$custom_designation,3=>$Department,4=>array(1=>'Active',2=>'Inactive'));
									 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Status", "100,120,150,150,","660","220",0, "select id, user_name, department_id, user_full_name, designation,valid from  user_passwd where 1=1 order by valid", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,department_id,designation,department_id,valid", $arr , "user_name,user_full_name,designation,department_id,valid", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
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
	
	$("#cbo_single_user_id").val(0);
</script>
</html>