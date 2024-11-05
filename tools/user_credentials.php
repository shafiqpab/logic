<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create PI Approval

Functionality	:

JS Functions	:

Created by		:	Md. Didarul Alam
Creation date 	: 	16-08-2016
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
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("User Credentials","../", 1, 1, $unicode,1,'');
?>
<script language="javascript">
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
    var permission='<? echo $permission; ?>';

   

    function openmypage(page_link,title)
    {
        if (title=="User Selection") {
            var data=document.getElementById('cbo_user_name').value;
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+"&data="+data, title, "width=310px,height=330px,center=1,resize=0,scrolling=0",'')
        }
        else if (title=="TNA Task Selection") {
            var data= document.getElementById('cbo_tna_task_id').value;
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+"&data="+data, title, "width=630px,height=330px,center=1,resize=0,scrolling=0",'')
        }
        else if (title=="Buyer Selection") {
            var data= document.getElementById('cbo_user_buyer').value;
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+"&data="+data, title, "width=630px,height=330px,center=1,resize=0,scrolling=0",'')
            
        }else if (title=="Role Selection") {
            var data = $('#update_id').val();
            if(data==''){
              alert("Please Bring Data");
			  return;
		    }
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+"&data="+data, title, "width=395px,height=330px,center=1,resize=0,scrolling=0",'');
            
        } else {
            var data= document.getElementById('cbo_user_supplier').value;
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+"&data="+data, title, "width=630px,height=330px,center=1,resize=0,scrolling=0",'')
        }

        emailwindow.onclose=function()
        {
            var selected_id=this.contentDoc.getElementById("txt_selected_id")
            var selected_name=this.contentDoc.getElementById("txt_selected") //Access form field with id="emailfield"

            if (title=="User Selection")
            {
               user_id = selected_id.value;
               get_php_form_data(user_id, "load_php_data_to_form", "requires/user_credentials_controller" );
            } 
            else if (title=="TNA Task Selection")
            {
                document.getElementById('cbo_tna_task_show').value=selected_name.value;
                document.getElementById('cbo_tna_task_id').value=selected_id.value;
            } 
            else if (title=="Buyer Selection")
            {
                document.getElementById('cbo_user_buyer_show').value=selected_name.value;
                document.getElementById('cbo_user_buyer').value=selected_id.value;
            } 
            else {
                document.getElementById('cbo_user_supplier_show').value=selected_name.value;
                document.getElementById('cbo_user_supplier').value=selected_id.value;
            }
        }
    }

    function fnc_user_creation(operation)
    {
        if (form_validation('cbo_user_name_show*cbo_company_name*cbo_location_name','User Name*Company Name*Location Name')==false)
        {
            return;
        } 
		else {
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_user_name*cbo_company_name*cbo_location_name*cbo_store_name*cbo_user_buyer*cbo_user_supplier*cbo_item_category*cbo_tna_task_id*cbo_graph_id*update_id*cbo_workstation_location*cbo_credential_user_planner*cbo_planner_type',"../");
            freeze_window(operation);
            http.open("POST","requires/user_credentials_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            release_freezing();
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
                set_button_status(1, permission, 'fnc_user_creation',1);
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
            show_list_view(reponse[1],'show_list_view','show_list_view','requires/user_credentials_controller','setFilterGrid("list_view",-1)');
        }
    }

function getCompanyId() {
    var company_id = document.getElementById('cbo_company_name').value;
    var update_id = document.getElementById('update_id').value;

    if(company_id !='') {
		var data="action=load_drop_down_company_location&choosenCompany="+company_id+"&update_id="+update_id;
		http.open("POST","requires/user_credentials_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function(){
			if(http.readyState == 4)
			{
                //var response = trim(http.responseText).split("**");
				var response = trim(http.responseText);
                $('#location_td').html(response);
                //$('#store_td').html(response[1]);
                //$('#item_category_td').html(response[2]);
				//set_multiselect('cbo_location_name*cbo_store_name','0*0','0','','getStore()*0');
                set_multiselect('cbo_location_name','0','0','','0','getStore()');
			   	//set_multiselect('cbo_location_name*cbo_store_name','0*0','1',response[2],'0*0');//'12,18*104,53'
			}
		};
    }
}
function getStore() {
    var company_id = document.getElementById('cbo_company_name').value;
    var location_id = document.getElementById('cbo_location_name').value;
    if(company_id !='') {
        var data="action=load_drop_down_location_store&choosenCompany="+company_id+"&location_id="+location_id;
        http.open("POST","requires/user_credentials_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = function(){
            if(http.readyState == 4)
            {
                var response = trim(http.responseText);
                $('#store_td').html(response);
                set_multiselect('cbo_store_name','0','0','','0');
            }
        };
    }
}
function updateStore(){
    var company_id = document.getElementById('cbo_company_name').value;
    var location_id = document.getElementById('cbo_location_name').value;
    var update_id = document.getElementById('update_id').value;
    if(company_id !='') {
        var data="action=load_update_location_store&choosenCompany="+company_id+"&location_id="+location_id+"&update_id="+update_id;
        http.open("POST","requires/user_credentials_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = function(){
            if(http.readyState == 4)
            {
                var response = trim(http.responseText).split("**");
                $('#store_td').html(response[0]);
                set_multiselect('cbo_store_name','0','0','','0');
                set_multiselect('cbo_store_name','0','1',response[1],'0');
            }
        };
    }
}
</script>



</head>
<body onLoad="set_hotkey()">
	 <div align="center">
		 <? echo load_freeze_divs ("../",$permission);
         $permission_id=sql_select("select * from field_level_access where page_id=1 and user_id=1") ;
         $new_arr=array();
         foreach( $permission_id as $tm)
         {
            $new_arr[$fieldlevel_arr[$tm[csf('page_id')]][$tm[csf('field_id')]]['id']]=$tm[csf('permission_id')];
         }
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
                            <td class="must_entry_caption">User Name</td>
                            <td>
                                <input type="text" name="cbo_user_name_show" placeholder="Double Click for User" id="cbo_user_name_show" class="text_boxes" style="width:220px" onDblClick="openmypage('requires/user_credentials_controller.php?action=user_selection_popup','User Selection')" readonly>
                                <input type="hidden" name="cbo_user_name" id="cbo_user_name" class="text_boxes" style="width:220px" readonly >
                            </td>
                        </tr>

                        <tr>
                            <td width="130" class="must_entry_caption">Unit / Company Name </td>
                            <td width="" id="td_company">
                              <? echo create_drop_down( "cbo_company_name", 232, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 order by company_name", "id,company_name", 0, "", '', ''); ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="must_entry_caption">Company Location</td>
                            <td id="location_td" >
                               <? 	//echo create_drop_down( "cbo_location_name", 232, "select id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 0, "", '', '' );
                               echo create_drop_down( "cbo_location_name", 232, $blank_array,"", 0, "0", '', '','','' );
                               ?>
                            </td>
                        </tr>

                        <tr>
                            <td>Store Name</td>

                            <td width="170" id="store_td">
                                <?
                                    //echo create_drop_down( "cbo_store_name", 232, "select id,store_name from lib_store_location where status_active=1 and is_deleted=0 order by store_name","id,store_name", 0, "", '', '');
                                    echo create_drop_down( "cbo_store_name", 232, $blank_array,"", 0, "0", '', '','','' );
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>Buyer Name</td>
                            <td>
                                <input type="text" name="cbo_user_buyer_show" placeholder="Double Click for Buyer" id="cbo_user_buyer_show" class="text_boxes" style="width:220px" onDblClick="openmypage('requires/user_credentials_controller.php?action=buyer_selection_popup','Buyer Selection')" readonly>
                                <input type="hidden" name="cbo_user_buyer" id="cbo_user_buyer" class="text_boxes" style="width:220px" readonly >
                            </td>
                        </tr>

                        <tr>
                            <td>Supplier Name</td>
                            <td>
                                <input type="text" name="cbo_user_supplier_show" placeholder="Double Click for Supplier" id="cbo_user_supplier_show" class="text_boxes" style="width:220px" onDblClick="openmypage('requires/user_credentials_controller.php?action=supplier_selection_popup','Supplier Selection')" readonly>
                                <input type="hidden" name="cbo_user_supplier" id="cbo_user_supplier"  style="width:220px" readonly >
                            </td>
                        </tr>

                        <tr>
                            <td>Item Category</td>
                            <td id="item_category_td">
                                <?
                                echo create_drop_down( "cbo_item_category", 232, $item_category,"", "0", "", 0, "", '', '' );
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>TNA Task</td>
                            <td>
                                <input type="text" name="cbo_tna_task_show" placeholder="Double Click for TNA Task" id="cbo_tna_task_show" class="text_boxes" style="width:220px" onDblClick="openmypage('requires/user_credentials_controller.php?action=tna_task_selection_popup','TNA Task Selection')" readonly>
                                <input type="hidden" name="cbo_tna_task_id" id="cbo_tna_task_id" readonly >
                            </td>
                        </tr>

                        <tr>
                            <td>Home Graph</td>
                            <td>
                                 <?
								 	foreach($home_graph_arr as $key=>$val){
										$graph_arr[$key]=ucfirst(str_replace(array('_','.php'),array(' ',''),$val));
									}
									echo create_drop_down( "cbo_graph_id", 232, $graph_arr,"", 1, "-- Select --", $selected );
								  ?>

                            </td>
                        </tr>
                        <tr>
                            <td>Workstation Placement Location</td>
                            <td>
                               <? 	echo create_drop_down( "cbo_workstation_location", 232, "select id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", '', '' );
                               ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Credential Use for Planner</td>
                            <td>
                                <?  
								   $is_user_planner=array(1=>"Yes",2=>"No"); 
                                   echo create_drop_down( "cbo_credential_user_planner", 131, $is_user_planner,"", 1, "-- Select --", 2, "" );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Is Planner.</td>
                              <td> 
                                <?  
								   $is_planner=array(1=>"Yes",2=>"No"); 
                                   echo create_drop_down( "cbo_planner_type", 131, $is_planner,"", 1, "-- Select --", 2, "" );
                                ?>
                               
                               <input type="button" name="button" class="formbutton" value="Role" onclick="openmypage('requires/user_credentials_controller.php?action=role_selection_popup','Role Selection');return false;" style="width:100px">

                                <input type="hidden" name="cbo_role" id="cbo_role" class="text_boxes" style="width:220px" readonly >
                            </td>  
                       
                        </tr>
                        <tr>
                            <td  height="30" align="center" valign="bottom" colspan="2"> <input type="hidden" name="update_id" id="update_id"/>
                                 <? echo load_submit_buttons( $permission, "fnc_user_creation", 1,0 ,"reset_form('usercreationform_1','','')",1); ?>
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="2" align="center" id="show_list_view">
                            	<?
								$custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');
								$Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
								$arr=array (2=>$custom_designation,3=>$Department);
								echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, "select id, user_name, department_id, user_full_name, designation from  user_passwd where valid=1", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_credentials_controller", 'setFilterGrid("list_view",-1);' ) ;
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
//set_multiselect('cbo_company_name*cbo_item_category*cbo_location_name*cbo_store_name','0*0*0*0','0','','0*0*0*0','getCompanyId()*0*0*0');
set_multiselect('cbo_company_name*cbo_item_category*cbo_location_name*cbo_store_name','0*0*0*0','0','','0*0*0*0','0*0*0*0');
</script>


<script>
setTimeout[
($("#td_company a").attr("onclick","disappear_list(cbo_company_name,'0');getCompanyId();") ,3000),
($("#location_td a").attr("onclick","disappear_list(cbo_location_name,'0');getStore();") ,3000),
($("#location_td a").attr("onclick","disappear_list(cbo_location_name,'0');updateStore();") ,3000)
];
//setTimeout[];

$.each( new_arr, function( key, value ) {
    if(value==2)
    {
        var perm="disabled";
        $('#'+key).attr('disabled',perm);
    }
});
</script>
</html>