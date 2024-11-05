<?
/* -------------------------------------------- Comments

  Purpose	: 	This form will create Body Part Entry

  Functionality	:

  JS Functions	:

  Created by	:	Palash
  Creation date : 	19-05-2022
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
echo load_html_head_contents("Sewing Defect Entry", "../../", 1, 1, $unicode, '', '');

?>

<script>
    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';

    function fnc_sewing_defect_part_entry(operation)
    {
        if(operation!=0){
		var update_id = document.getElementById('update_id').value;
		var response =return_global_ajax_value( update_id, 'is_used_body_part', '', 'requires/sewing_defect_entry_controller');
		if(operation == 2){
			alert("Delete restricted!");
			return;
		}

    }

        if (form_validation('txt_sewing_defect_full_name*txt_sewing_defect_short_name*cbo_sewing_defect_type*txt_entry_page', 'Defect Full Name* Defect Short Name*Defect Type*Tag Entry Page') == false)
        {
            return;
        }
        else
        {
            // if (operation == 2)
            // {
            //     var con = confirm("Do You Want To Delete Data Permanently ?");
            //     if (con == false)
            //     {
            //         void(0);
            //         return;
            //     }
            // }

            var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('txt_sewing_defect_full_name*txt_sewing_defect_short_name*cbo_sewing_defect_type*cbo_status*update_id*txt_entry_page_id*txt_entry_page*txt_defect_serial_no', "../../");
            //alert(data);return;
            freeze_window(operation);
            http.open("POST", "requires/sewing_defect_entry_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_sewing_defect_part_entry_reponse;
        }
    }

    function fnc_sewing_defect_part_entry_reponse()
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
            show_list_view('', 'sewing_defect_list_view', 'sewing_defect_list', 'requires/sewing_defect_entry_controller', 'setFilterGrid("list_view",-1)');
            reset_form('sewingpartoperationentry_1', '', '');
            if(reponse[0]==1 || reponse[0]==0)
		{
			$('#txt_sewing_defect_full_name').removeAttr('disabled','disabled');
			$('#txt_sewing_defect_short_name').removeAttr('disabled','disabled');
			$('#cbo_sewing_defect_type').removeAttr('disabled','disabled');
            $('#txt_entry_page').removeAttr('disabled','disabled');
            $('#cbo_status').removeAttr('disabled','disabled');
		}
            set_button_status(0, permission, 'fnc_sewing_defect_part_entry', 1);
            release_freezing();
        }
    }

function openmypage_entry_page()
    {
        var txt_entry_page_id = $('#txt_entry_page_id').val();
        var title = 'Entry Page Selection Form';
        var page_link = 'requires/sewing_defect_entry_controller.php?txt_entry_page_id='+txt_entry_page_id+'&action=entry_page_popup';

        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0]
            var entry_page_id=this.contentDoc.getElementById("hidden_entry_page_id").value;
            var entry_page_name=this.contentDoc.getElementById("hidden_entry_page_name").value;
            $('#txt_entry_page_id').val(entry_page_id);
            $('#txt_entry_page').val(entry_page_name);

            load_drop_down( 'requires/sewing_defect_entry_controller',entry_page_id,'load_drop_down_defect_type', 'defect_type_td' );
        }
    }
</script>

</head>

<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs("../../", $permission); ?>

        <fieldset style="width:900px;">
            <legend>Sewing Defect Entry</legend>
            <form name="sewingpartoperationentry_1" id="sewingpartoperationentry_1" autocomplete="off">
                <table width="100%">
                    <tr>
                        <td width="100" class="must_entry_caption"> &nbsp;Entry Page </td>
                        <td>
                        <input type="text" name="txt_entry_page" id="txt_entry_page" class="text_boxes" style="width:142px;" placeholder="Click To Search"  onClick="openmypage_entry_page();" readonly />
                            <input type="hidden" name="txt_entry_page_id" id="txt_entry_page_id" value="" />
                        </td>
                        <td class="must_entry_caption">Defect Type</td>
                        <td id="defect_type_td">
                        <? $defect_type_arr =array(2=>"Reject",3=>"Alter",4=>"Spot");
                           
                               echo create_drop_down("cbo_sewing_defect_type", 155, $defect_type_arr, '', 1, '---Select---');
                        ?>
                        </td>
                        <td width="120" class="must_entry_caption">Defect Full Name</td>
                        <td width="150">
                            <input type="text" name="txt_sewing_defect_full_name" id="txt_sewing_defect_full_name" class="text_boxes" style="width:142px"/>
                        </td>

                    </tr>
                    <tr>
                        <td width="120" class="must_entry_caption">Defect Short Name</td>
                        <td>
                            <input type="text" name="txt_sewing_defect_short_name" id="txt_sewing_defect_short_name" class="text_boxes" style="width:142px;" />
                        </td>
                        <td>Status</td>
                        <td><?
                            echo create_drop_down("cbo_status", 155, $row_status, '', 1, '---Select---');
                            ?>
                        </td>
                        <td width="120">Defect Serial No</td>
                        <td width="150">
                            <input type="text" class="text_boxes_numeric" name="txt_defect_serial_no" id="txt_defect_serial_no"  style="width:142px"/>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="6" align="center" class="button_container">
                            <?
                            echo load_submit_buttons($permission, "fnc_sewing_defect_part_entry", 0, 0, "reset_form('sewingpartoperationentry_1','','')",1);
                            ?>
                            <input type="hidden" name="update_id" id="update_id" >
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" height="15" align="center"></td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" id="sewing_defect_list">
                            <?
                            $sql_entry_page=sql_select("select m_menu_id,menu_name from main_menu where status=1  order by menu_name");
                            foreach($sql_entry_page as $row)
                            {
                                    $entry_page_arr[$row[csf('m_menu_id')]]=$row[csf('menu_name')];
                            }

                            $sqls=sql_select("select id,entry_page_id from lib_sewing_defect_mst where is_deleted=0");
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

                            // echo "<pre>";print_r($entry_page_arr);

                            $arr = array(0 => $defect_type_arr, 3 => $row_status,4=>$entry_page);
 							$sql="SELECT defect_type,full_name,short_name,status,id,entry_page_id,defect_serial_no from  lib_sewing_defect_mst where is_deleted=0 order by id desc";
                            $res = sql_select($sql);
                            
                            $defect_type_arr_bundle = array(2=>"Reject",3=>"Alter",4=>"Spot");
                           
                            $defect_type_arr_gross = array(1=>"Alter",2=>"Spot",3=>"Reject",4=>"Front Check",5=>"Back Check",6=>"Westband",7=>"Measurem");
                            $entry_page_arr = array(460=>'Bundle Wise Sewing Output',500=>'Sewing Output')
                           
                            ?>
                            <div>
                              <table width="890" cellpadding="0" cellspacing="0" border="1" class="rpt_table" id="rpt_tablelist_view" rules="all">
                                <thead>
                                  <tr>
                                    <th width="50">SL No</th>
                                    <th width="200">Defect Type</th>
                                    <th width="150">Defect Full Name</th>
                                    <th width="100"> Defect Short Name</th>
                                    <th width="70">Status</th>
                                    <th width="200">Entry Page</th>
                                    <th>Defect Serial No</th>
                                  </tr>
                                </thead>
                              </table>
                              <div style="max-height:220px; width:888px; overflow-y:auto" id="">
                                <table align="left" width="868" height="" cellpadding="0" cellspacing="0" border="1" class="rpt_table" id="list_view" rules="all">
                                  <tbody>
                                    <?
                                    $i = 1;
                                    foreach ($res as $v) 
                                    {
                                        if($v['ENTRY_PAGE_ID']==460) 
                                        {
                                            $defect_type_name = $defect_type_arr_bundle[$v['DEFECT_TYPE']];
                                        }
                                        else
                                        {
                                            $defect_type_name = $defect_type_arr_gross[$v['DEFECT_TYPE']];
                                        }
                                        ?>
                                        <tr onclick="get_php_form_data('<?=$v['ID'];?>','load_php_data_to_form','requires/sewing_defect_entry_controller')" bgcolor="#FFFFFF" style="cursor:pointer" id="tr_<?=$i;?>">
                                        <td width="50"><?=$i;?></td>
                                        <td align="left" width="200">
                                            <p><?=$defect_type_name;?></p>
                                        </td>
                                        <td align="left" width="150">
                                            <p><?=$v['FULL_NAME'];?></p>
                                        </td>
                                        <td align="left" width="100">
                                            <p><?=$v['SHORT_NAME'];?></p>
                                        </td>
                                        <td align="left" width="70">
                                            <p><?=$row_status[$v['STATUS']];?></p>
                                        </td>
                                        <td align="left" width="200">
                                            <p><?=$entry_page_arr[$v['ENTRY_PAGE_ID']];?></p>
                                        </td>
                                        <td align="left">
                                            <p><?=$v['DEFECT_SERIAL_NO'];?></p>
                                        </td>
                                        </tr>
                                        <?
                                        $i++;
                                    }
                                    ?>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                            <script>
                              setFilterGrid("list_view", -1);
                            </script>
                            <?

                            // echo create_list_view("list_view", "Defect Type,Defect Full Name, Defect Short Name,Status,Entry Page,Defect Serial No", "200,150,100,70,200,50", "880", "220", 1,$sql,"get_php_form_data", "id", "'load_php_data_to_form'", 1, "defect_type,0,0,status,id,0", $arr, "defect_type,full_name,short_name,status,id,defect_serial_no", "requires/sewing_defect_entry_controller", 'setFilterGrid("list_view",-1);', '0,0,0,0,0,0');
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
