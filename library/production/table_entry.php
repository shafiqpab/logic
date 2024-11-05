<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Table Entry					
Functionality	:	Must fill Blue colored field
JS Functions	:
Created by		:	Sapayth
Creation date 	: 	12-08-2020
Updated by 		: 		
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents('Table Entry', '../../', 1, 1, $unicode, '', '');

/*if ($_SESSION['logic_erp']["data_level_secured"]==1) {
	if ($_SESSION['logic_erp']["buyer_id"]!=0 && $_SESSION['logic_erp']["buyer_id"]!="") $buyer_name=" and id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_name= '';
	if ($_SESSION['logic_erp']["company_id"]!=0 && $_SESSION['logic_erp']["company_id"]!="") $company_name="and id in (".$_SESSION['logic_erp']["company_id"].")"; else $company_name= '';
}
else {
	$buyer_name= '';
	$company_name= '';
}*/
?>

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<?php echo $permission; ?>';

function fnc_saveUpdateDelete( operation )
{
   if (form_validation('cbo_company_name*cbo_location_name*cbo_floor_name*cbo_table_type*txt_table_sequence*txt_table_name', 'Company Name*Location Name*Floor Name*Table Type*Table Sequence*Table Name*Table Group')==false) {
		return;
	}

	// eval(get_submitted_variables('cbo_company_name*cbo_location_name*cbo_floor_name*cbo_table_type*txt_table_sequence*txt_table_name*txt_table_group*txt_man_power*cbo_table_status*update_id'));
	var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_floor_name*cbo_table_type*txt_table_sequence*txt_table_name*txt_table_group*txt_man_power*cbo_table_status*update_id', '../../');
	freeze_window(operation);
	http.open("POST", "requires/table_entry_controller.php", true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_saveUpdateDelete_response;
}

function fnc_saveUpdateDelete_response()
{
	if(http.readyState == 4) {
		//alert(http.responseText);
        var reponse=trim(http.responseText).split('**');
        if (reponse[0].length>2) reponse[0]=10;
        show_msg(reponse[0]);
        document.getElementById('update_id').value  = reponse[2];
		show_list_view('', 'table_entry_list_view', 'table_entry_list', '../production/requires/table_entry_controller', 'setFilterGrid("list_view",-1)');
		set_button_status(1, permission, 'fnc_saveUpdateDelete', 1);
        if(reponse[0] == 2) {
            reset_form('tableentry_1', '', '', '', '', '');
        } else {
            reset_form('tableentry_1', '', '', '', '', 'cbo_company_name*cbo_location_name*cbo_floor_name*update_id');
        }
		
		release_freezing();
	}
}	
 </script>
</head>

<body>
	<div align="center" style="width:100%;">
	    <?php echo load_freeze_divs ('../../', $permission); ?>
        <fieldset style="width:750px;">
        	<legend>Sewing Line Info</legend>
        	<form name="tableentry_1" id="tableentry_1" autocomplete="off">	
        		<table cellpadding="0" cellspacing="2" width="100%" align="center" border="0">
                	<tr>
                        <td width="100%" align="center">
                            <table align="center">
                                <tr>
                                    <td></td>
                                    <td width="150" class="must_entry_caption">Company</td>
                                    <td colspan="2">
                                        <?php
                                            echo create_drop_down( 'cbo_company_name', 262, "select company_name, id from lib_company where is_deleted=0 and status_active=1 $company_name order by company_name", 'id,company_name', 1, '--- Select Company ---', 0, "load_drop_down( 'requires/table_entry_controller', this.value, 'load_drop_down_location', 'location' )" );
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td width="150" class="must_entry_caption">Location</td>
                                    <td colspan="2" id="location">
                                        <?php
                                            echo create_drop_down( 'cbo_location_name', 262, "select location_name,id from lib_location where is_deleted=0 and status_active=1 order by location_name", 'id,location_name', 1, '--- Select Location ---', 0, "load_drop_down( 'requires/table_entry_controller', this.value, 'load_drop_down_floor', 'floor' )"  );
                                        ?>
                                    </td>
                                </tr>	
                                <tr>
                                    <td></td>
                                    <td width="150" class="must_entry_caption">Floor</td>
                                    <td colspan="2" id="floor">
                                        <?php
                                            echo create_drop_down( 'cbo_floor_name', 262, "select floor_name,id from  lib_prod_floor where is_deleted=0 and status_active=1 order by floor_name", 'id,floor_name', 1, '--- Select Floor ---', 0, '' );
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td width="150" class="must_entry_caption">Table Type</td>
                                    <td colspan="2">
                                        <?php
                                            $tblTypeArr = array(1 => 'Cutting', 2 => 'Iron', 3 => 'Poly', 4 => 'Finishing', 5 => 'Printing');
                                            echo create_drop_down("cbo_table_type", 262, $tblTypeArr, '', 1, '--- Select ---', 0, '');
                                        ?>
                                        
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td width="150" class="must_entry_caption">Table Sequence</td>
                                    <td  colspan="2">
                                        <input type="text" name="txt_table_sequence" id="txt_table_sequence" class="text_boxes_numeric" style="width:250px" />						
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td width="150"><span class="must_entry_caption">Table Name</span>-Group</td>
                                    <td colspan="2">
                                        <input type="text" name="txt_table_name" id="txt_table_name" class="text_boxes" style="width:115px" placeholder="Table Name" />---<input type="text" name="txt_table_group" id="txt_table_group" class="text_boxes" style="width:115px" placeholder="Table Group" />						
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td width="150">Table/MP</td>
                                    <td  colspan="2">
                                        <input type="text" name="txt_man_power" id="txt_man_power" class="text_boxes_numeric" style="width:250px" />                      
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Status</td>
                                    <td colspan="2">
                                        <?php
                                            echo create_drop_down( "cbo_table_status", 262, $row_status,'', '', '', 1 );
                                        ?>                                     
                                    </td>
                                </tr>
                                <tr>
                                   <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                        <input type="hidden" name="update_id" id="update_id">
                                        <?php 
                                            echo load_submit_buttons( $permission, "fnc_saveUpdateDelete", 0,0 ,"reset_form('tableentry_1','','','')");
                                        ?>
                                    </td>					
                                </tr>
                            </table>
                        </td>
                    </tr>				
        		</table>
        	</form>
            <table>
                <tr>
                   <td colspan="3" valign="bottom" align="center" id="table_entry_list">
                        <?php
                            $floor=return_library_array( 'select floor_name,id from lib_prod_floor where is_deleted=0', 'id', 'floor_name' );
                            $arr=array(2=>$floor);
                            echo create_list_view( 'list_view', 'Company,Location,Floor,Table Name,Table Group,Table Serial,Man Power', '150,120,80,80,120,100', '750', '220', 1, "select c.company_name, l.location_name, a.floor_name, a.table_name, a.table_group, a.table_sequence, a.id,a.man_power from lib_table_entry a, lib_company c, lib_location l where a.company_name=c.id and a.location_name=l.id and a.is_deleted=0 order by a.id desc", 'get_php_form_data', 'id', "'load_php_data_to_form'", 1, '0,0,floor_name', $arr , 'company_name,location_name,floor_name,table_name,table_group,table_sequence,man_power', '../production/requires/table_entry_controller', 'setFilterGrid("list_view",-1);' );
                        ?>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
 </body>
 <script src="../../includes/functions_bottom.js" type="text/javascript"></script>