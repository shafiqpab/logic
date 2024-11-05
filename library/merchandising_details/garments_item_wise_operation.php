
<?
/* -------------------------------------------- Comments

  Purpose	: 	This form will create Body Part Entry

  Functionality	:

  JS Functions	:

  Created by	:	Wayasel Ahmed
  Creation date : 	18-03-23
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
echo load_html_head_contents("Garments Item wise Operation", "../../", 1, 1, $unicode, '', '');

?>

<script>
    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';

    function fnc_body_part_entry(operation)
    {
    //     if(operation!=0){
	// 	var update_id = document.getElementById('update_id').value;
	// 	var response=return_global_ajax_value( update_id, 'is_used_body_part', '', 'requires/garments_item_wise_operation_controller');
	// 	if(response == 1){
	// 		alert("Update or Delete restricted ! Because this page used another page.");
	// 		return;
	// 	}

    // }

        if (form_validation('cbo_item_name*txt_operation_id*cbo_status','Item ID*Operation ID*Status') == false)
        {
            return;
        }
        else
        {
            var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('cbo_item_name*txt_operation_id*txt_operation_name*cbo_status*update_id', "../../");
            // alert(data);return;
            freeze_window(operation);
            http.open("POST", "requires/garments_item_wise_operation_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_body_part_entry_reponse;
        }
    }

    function fnc_body_part_entry_reponse()
    {
        if (http.readyState == 4)
        {
            //console.log(http.responseText);
            var reponse = trim(http.responseText).split('**');
            // if (response[0].length > 2)
			// {
			// 	response[0] = 10;
			// }

            show_msg(reponse[0]);
            show_list_view('', 'body_part_list_view', 'body_part_list', 'requires/garments_item_wise_operation_controller', 'setFilterGrid("list_view",-1)');
            reset_form('bodypartoperationentry_1', '', '');
            if(reponse[0]==1 || reponse[0]==0)
		{
			$('#cbo_item_name').removeAttr('disabled','disabled');
			$('#txt_operation_id').removeAttr('disabled','disabled');
            $('#cbo_status').removeAttr('disabled','disabled');
		}
            set_button_status(0, permission, 'fnc_body_part_entry', 1);
            release_freezing();
        }
    }

function openmypage_entry_page()
    {
        var txt_entry_page_id = $('#txt_entry_page_id').val();
        var title = 'Operation Name List';
        var page_link = 'requires/garments_item_wise_operation_controller.php?txt_entry_page_id='+txt_entry_page_id+'&action=operation_name_popup';

        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0]
            var operation_id=this.contentDoc.getElementById("operation_id").value;
            var txt_operation_name=this.contentDoc.getElementById("operation_id_name").value;
            $('#txt_operation_id').val(operation_id);
            $('#txt_operation_name').val(txt_operation_name);
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
                        <td width="120" class="must_entry_caption">Item Name</td>
                        <td align="center"> 
                            <?
                            echo create_drop_down( "cbo_item_name", 150, "select id,item_name from lib_garment_item","id,item_name", 1, "-- Select Item --", $selected, "" );
                            ?>
                        </td>
                        <td width="100" class="must_entry_caption">Operation Name </td>
                        <td>
                             <input type="text" name="txt_operation_name" id="txt_operation_name" class="text_boxes" style="width:142px;" placeholder="Click To Search"  onClick="openmypage_entry_page();" readonly />
                            <input type="hidden" name="txt_operation_id" id="txt_operation_id" value="" />
                        </td>

                        <td class="must_entry_caption">Status</td>
                        <td><?
                            echo create_drop_down("cbo_status", 155, $row_status, '', 0, '---Select---',1,'',0,0);
                            ?>
                        </td>
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
	                        $item_arr=return_library_array( "select id, item_name from  lib_garment_item", "id", "item_name"  );	
	                        $operation_name_arr=return_library_array( "select id, operation_name from  lib_sewing_operation_entry", "id", "operation_name");
                            $arr = array(0 => $item_arr, 1 => $operation_name_arr,2=>$row_status);
 							$sql="select id, item_id, operation_id, status from  garments_item_wise_operation where is_deleted=0 order by id desc";

                            echo create_list_view("list_view", "Item Name,Operation Name,Status", "200,150,100", "650", "220", 1,$sql,"get_php_form_data", "id", "'load_php_data_to_form'", 1, "item_id,operation_id,status", $arr, "item_id,operation_id,status", "requires/garments_item_wise_operation_controller", 'setFilterGrid("list_view",-1);', '0,0,0');
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
