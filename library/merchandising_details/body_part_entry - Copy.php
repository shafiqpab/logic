<?
/* -------------------------------------------- Comments

  Purpose	: 	This form will create Body Part Entry

  Functionality	:

  JS Functions	:

  Created by	:	Mezbah
  Creation date : 	11-03-2017
  Updated by 	:
  Update date	:

  QC Performed BY:

  QC Date	:

  Comments	:

 */

session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);

$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Body Part Entry", "../../", 1, 1, $unicode, '', '');

?>	

<script>
    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';

    function fnc_body_part_entry(operation)
    {
        if (form_validation('body_part_full_name*body_part_short_name*body_part_type*txt_entry_page', 'Body Part Full Name*Body Part Short Name*Body Part Type*Tag Entry Page') == false)
        {
            return;  
        } 
        else
        {
            if (operation == 2)
            {
                var con = confirm("Do You Want To Delete Data Permanently ?");
                if (con == false)
                {
                    void(0);
                    return;
                }
            }

            var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('body_part_full_name*body_part_short_name*body_part_type*cbo_status*update_id*txt_entry_page_id*txt_entry_page*cut_lay_entry', "../../");
            //alert(data);return;
            freeze_window(operation);
            http.open("POST", "requires/body_part_entry_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_body_part_entry_reponse;
        }
    }

    function fnc_body_part_entry_reponse()
    {
        if (http.readyState == 4)
        {
            console.log(http.responseText);
            var response = trim(http.responseText).split('**');
            if (response[0].length > 2)
			{
				response[0] = 10;
			}
                
            show_msg(response[0]);
            show_list_view('', 'body_part_list_view', 'body_part_list', 'requires/body_part_entry_controller', 'setFilterGrid("list_view",-1)');
            set_button_status(0, permission, 'fnc_body_part_entry', 1);
            reset_form('bodypartoperationentry_1', '', '');
            release_freezing();
        }
    }

function openmypage_entry_page()
    {
        var txt_entry_page_id = $('#txt_entry_page_id').val();
        var title = 'Entry Page Selection Form'; 
        var page_link = 'requires/body_part_entry_controller.php?txt_entry_page_id='+txt_entry_page_id+'&action=entry_page_popup';
          
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0]
            var entry_page_id=this.contentDoc.getElementById("hidden_entry_page_id").value; 
            var entry_page_name=this.contentDoc.getElementById("hidden_entry_page_name").value;
            $('#txt_entry_page_id').val(entry_page_id); 
            $('#txt_entry_page').val(entry_page_name);
        }
    }
    function setValue()
    {
        if(document.getElementById("cut_lay_entry").checked)
        {
            $("#cut_lay_entry").val(1);
        }
        else
        {
            $("#cut_lay_entry").val(0);
        }
    }
</script>

</head>

<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs("../../", $permission); ?>

        <fieldset style="width:800px;">
            <legend>Body Part Entry</legend>
            <form name="bodypartoperationentry_1" id="bodypartoperationentry_1" autocomplete="off">	
                <table width="100%">
                    <tr>
                        <td width="120" class="must_entry_caption">Body Part Full Name</td>
                        <td width="150">
                            <input type="text" name="body_part_full_name" id="body_part_full_name" class="text_boxes" style="width:142px"/>
                        </td>
                                              

                        <td width="120" class="must_entry_caption">Body Part Short Name</td>
                        <td>
                            <input type="text" name="body_part_short_name" id="body_part_short_name" class="text_boxes" style="width:142px;" />						
                        </td>
                        <td width="100" class="must_entry_caption"> &nbsp;Entry Page </td>
                        <td>
                        <input type="text" name="txt_entry_page" id="txt_entry_page" class="text_boxes" style="width:142px;" placeholder="Click To Search"  onClick="openmypage_entry_page();" readonly />
                            <input type="hidden" name="txt_entry_page_id" id="txt_entry_page_id" value="" />
                            </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Body Part Type</td>
                        <td>
                            <?
							
                            echo create_drop_down("body_part_type", 155, $body_part_type, '', 1, '---Select---');
                            ?>						
                        </td>

                        <td>Status</td>
                        <td><?
                            echo create_drop_down("cbo_status", 155, $row_status, '', 1, '---Select---');
                            ?> 					
                        </td>
                        <td>Embellishment/Cut Lay Entry</td>
                        <td><input type="checkbox" name="cut_lay_entry" id="cut_lay_entry" value="0" onchange="setValue()" ></td>
                    </tr>

                    <tr>
                        <td colspan="6" align="center" class="button_container">
                            <?
                            echo load_submit_buttons($permission, "fnc_body_part_entry", 0, 0, "reset_form('bodypartoperationentry_1','','')",1);
                            ?>
                            <input type="hidden" name="update_id" id="update_id" >		
                        </td>					
                    </tr>
                    <tr>
                        <td colspan="6" height="15" align="center"></td>					
                    </tr>	
                    <tr>
                        <td colspan="6" align="center" id="body_part_list">				
                            <?
                            $sql_entry_page=sql_select("select m_menu_id,menu_name from main_menu where status=1  order by menu_name");
                            foreach($sql_entry_page as $row)
                            {
                                    $entry_page_arr[$row[csf('m_menu_id')]]=$row[csf('menu_name')];
                            }

                            $sqls=sql_select("select id,entry_page_id from lib_body_part where is_deleted=0");
                            foreach($sqls as $val)
                            {
                                if($val[csf("entry_page_id")]!="" || $val[csf("entry_page_id")]!=0)
                                {
                                    if(strpos($val[csf("entry_page_id")], ",")==false)
                                    {
                                       $entry_page[$val[csf("id")]]=$entry_form[$val[csf('entry_page_id')]];
                                    }
                                    else
                                    {
                                        $menu_name="";
                                        $vals=explode(",", $val[csf("entry_page_id")]);
                                        foreach($vals as $menu_id)
                                        {
                                            if($menu_name=="") {$menu_name .=$entry_form[$menu_id];}
                                            else{$menu_name .=','.$entry_form[$menu_id];}
                                        }
                                        $entry_page[$val[csf("id")]]=$menu_name;

                                    }

                                }
                                
                            }
                           
                            $arr = array(2 => $body_part_type, 3 => $row_status,4=>$entry_page);
 							$sql="select body_part_full_name,body_part_short_name,body_part_type,status,id,entry_page_id from  lib_body_part where is_deleted=0 order by id desc";
							
                            echo create_list_view("list_view", "Body Part Full Name,Body Part Short Name,Body Part Type,Status,Entry Page", "200,150,100,70,200", "850", "220", 1,$sql,"get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,body_part_type,status,id", $arr, "body_part_full_name,body_part_short_name,body_part_type,status,id", "requires/body_part_entry_controller", 'setFilterGrid("list_view",-1);', '0,0,0,0');
                            ?>
                        </td>					
                    </tr>		
                </table>
            </form>	
        </fieldset>

    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
