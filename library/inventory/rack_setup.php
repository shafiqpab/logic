<?
/****************************************************************
|   Purpose         :   This form is Rack Setup
|   Functionality   :
|   JS Functions    :
|   Created by      :   MA.Kaiyum
|   Creation date   :   28-06-2018
|   Updated by      :
|   Update date     :
|   QC Performed BY :
|   QC Date         :
|   Comments        :
******************************************************************/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Inventory Rack Setup Information", "../../", 1, 1,$unicode,'','');

if ($_SESSION['logic_erp']["data_level_secured"]==1)
{
	if ($_SESSION['logic_erp']["buyer_id"]!=0 && $_SESSION['logic_erp']["buyer_id"]!="") $buyer_name=" and id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_name="";
	if ($_SESSION['logic_erp']["company_id"]!=0 && $_SESSION['logic_erp']["company_id"]!="") $company_name="and id in (".$_SESSION['logic_erp']["company_id"].")"; else $company_name="";
}
else
{
	$buyer_name="";
	$company_name="";
}
?>

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';


	function fnc_inventory_rack_info( operation )
	{
       if (form_validation('cbo_company_name*cbo_location_name*cbo_store_name*cbo_floor_id*cbo_room_id*txt_rack*txt_rack_sequence','Company Name*Location Name*Store Name*Floor Name*Room Name*Rack Name*Rack Sequence')==false)
		{
			return;
		}
		else
		{
            eval(get_submitted_variables('cbo_company_name*cbo_location_name*cbo_floor_id*cbo_room_id*txt_rack*txt_rack_sequence*cbo_store_name*cbo_status*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_floor_id*cbo_room_id*txt_rack*txt_rack_sequence*cbo_store_name*cbo_status*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/rack_setup_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_inventoryRack_reponse;
		}
	}

	function fnc_inventoryRack_reponse()
	{
		if(http.readyState == 4)
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);
			document.getElementById('update_id').value  = reponse[2];
			show_list_view('','inventoryRack_list_view','inventory_rack_list','../inventory/requires/rack_setup_controller','setFilterGrid("list_view",-1)');
			set_button_status(0, permission, 'fnc_inventory_rack_info',1);
			//reset_form('inventoryRackInfo_1','','');
            document.getElementById('update_id').value  = '';
            $('#txt_rack').removeAttr('disabled','disabled');
            document.getElementById('txt_rack').value  = '';
			document.getElementById('txt_rack_sequence').value  = '';
            setFilterGrid('tbl_list_search', -1);
			release_freezing();
		}
	}

    $(document).ready(function (e) {
        setFilterGrid('tbl_list_search', -1);
    });

    function check_all_report()
    {
        $("input[name=chkRack]").each(function () {

            if ($('#check_all').prop('checked') == true)
                $(this).attr('checked', 'true');
            else
                $(this).removeAttr('checked');
        });
    }

    function fnc_print_creation(type)
	{
        var data="";
		var error=1;

        $("input[name=chkRack]").each(function(index, element) {

            if( $(this).prop('checked')==true)
            {
                error=0;
                var idd=$(this).attr('id').split("_");
                //alert(idd[1]);
                var rack_dtls_id=$('#txtFloorRoomRackDtlsId_'+idd[1] ).val();

                if(rack_dtls_id!="")
                {
                    if(data=="") data=$('#txtFloorRoomRackDtlsId_'+idd[1] ).val(); else data=data+","+$('#txtFloorRoomRackDtlsId_'+idd[1] ).val();
                }
                else
                {
                    $(this).prop('checked',false);
                }
            }
        });
		if( error==1 )
		{
			alert('No data selected');
			return;
		}

		freeze_window(3);

        if(type==1)
        {
			var url=return_ajax_request_value(data, "print_barcode", "requires/rack_setup_controller");
		}

        window.open(url,"##");
		release_freezing();
    }

 </script>
</head>
<body  onLoad="set_hotkey()">
	<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>
	<fieldset style="width:750px;">
		<legend>Inventory Rack Information</legend>
		<form name="inventoryRackInfo_1" id="inventoryRackInfo_1" autocomplete="off">
			<table cellpadding="0" cellspacing="2" width="100%" align="center" border="0">
            	<tr><td width="100%" align="center">
                        <table width="550" align="center">
                        <tr>
                            <td width="150" class="must_entry_caption">Company</td>
                            <td colspan="2"> <?
								echo create_drop_down( "cbo_company_name", 262, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 $company_cond  order by company_name",'id,company_name', 1, '--- Select Company ---', 0, "load_drop_down( 'requires/rack_setup_controller', this.value, 'load_drop_down_location', 'location' )" ); ?>
                         </td>
                        </tr>
                        <tr>
                            <td width="150" class="must_entry_caption">Location</td>
                            <td colspan="2" id="location">
							<?
								 echo create_drop_down( "cbo_location_name", 262, $blank_array,'', 1, '--- Select Location ---', 0, "" );
                            ?>

                            </td>
                        </tr>


						<tr>
                            <td width="150" class="must_entry_caption">Store</td>
                            <td colspan="2" id="store_td">
							<?
								 echo create_drop_down( "cbo_store_name", 262, $blank_array,'', 1, '--- Select Location ---', 0, "" );
                            ?>
                            </td>
                        </tr>



						<tr>
                            <td width="150" class="must_entry_caption">Floor</td>
                               <td colspan="2" id="floor_td">
							<?
								 echo create_drop_down( "cbo_floor_id", 262, $blank_array,'', 1, '--- Select Floor ---', 0, "" );
                            ?>
                            </td>
                        </tr>


                        <tr>
                            <td width="150" class="must_entry_caption">Room</td>
                               <td colspan="2" id="room_td">
                            <?
                                 echo create_drop_down( "cbo_room_id", 262, $blank_array,'', 1, '--- Select Room ---', 0, "" );
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="150" class="must_entry_caption">Rack</td>
                            <td colspan="2">
                            <input type="text" name="txt_rack" id="txt_rack" style="width:250px" class="text_boxes"  maxlength="50" title="Maximum 50 Character">
                            </td>

                        </tr>

                         <tr>
                            <td width="150" class="must_entry_caption">Rack Sequence</td>
                            <td colspan="2">
                            <input type="text" name="txt_rack_sequence" id="txt_rack_sequence" style="width:250px" class="text_boxes_numeric"  maxlength="50" title="Maximum 50 Character">
                            </td>

                        </tr>
                        <tr>
                            <td>Status</td>
                            <td  colspan="2"><?
                                    echo create_drop_down( "cbo_status", 262, $row_status,'', '', '', 1 );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" align="center">&nbsp;
                                <input  type="hidden" name="update_id" id="update_id">
                            </td>
                        </tr>
                        <tr>
                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                <?
                                    echo load_submit_buttons( $permission, "fnc_inventory_rack_info", 0,0 ,"reset_form('inventoryRackInfo_1','','',1)",1);
                                ?>
                                 <input type="button" id="Print_1" value="Print" class="formbutton" onClick="fnc_print_creation(1)" style="width:110px; ">
                            </td>
                        </tr>
                        <tr>
                           <td colspan="3" height="20" valign="bottom" align="center" class="button_container"></td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                           <td colspan="3" valign="bottom" align="center"  id="inventory_rack_list">
							 <?
							 	$lib_company_arr=return_library_array( "select id,company_name from lib_company where status_active=1 and is_deleted=0", "id","company_name" );
							 	$lib_location_arr=return_library_array( "select id,location_name from lib_location where status_active=1 and is_deleted=0", "id","location_name" );
							 	$lib_floor_arr=return_library_array( "select floor_id,floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id and a.status_active=1 and a.is_deleted=0", "floor_id","floor_room_rack_name" );

                                $lib_room_arr=return_library_array( "select room_id,floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active=1 and a.is_deleted=0", "room_id","floor_room_rack_name" );

                                $lib_store_arr=return_library_array("select id, store_name from lib_store_location where status_active = 1 and is_deleted = 0", 'id', 'store_name');

                                // $arr=array(0=>$lib_company_arr,1=>$lib_location_arr,2=>$lib_floor_arr,3=>$lib_room_arr,5=>$row_status);
                                $arr=array(0=>$lib_company_arr,1=>$lib_location_arr,2=>$lib_store_arr,3=>$lib_floor_arr,4=>$lib_room_arr,6=>$row_status); // issue id:7658
                                 if($db_type==0)
                                    {
                                        $sql="select a.company_id,b.location_id,b.floor_id,b.room_id,a.floor_room_rack_name,a.status_active,b.store_id, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and b.shelf_id ='' and b.bin_id ='' group by a.company_id,b.location_id,b.floor_id,b.room_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id,b.store_id";
                                    }
                                    else
                                    {
                                     $sql="select a.company_id,b.location_id,b.floor_id,b.room_id,a.floor_room_rack_name,a.status_active,b.store_id, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and b.shelf_id is null and b.bin_id is null group by a.company_id,b.location_id,b.floor_id,b.room_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id,b.store_id";
                                    }
                               /*  echo create_list_view ( "list_view", "Company Name,Location Name,Store,Floor Name,Room Name,Rack Name,Status", "150,80,100,100,60,80,30","700","220",1, $sql, "get_php_form_data", "floor_room_rack_id,floor_room_rack_dtls_id","'load_php_data_to_form'", 1, "company_id,location_id,store_id,floor_id,room_id,0,status_active", $arr, "company_id,location_id,store_id,floor_id,room_id,floor_room_rack_name,status_active", "../inventory/requires/rack_setup_controller", 'setFilterGrid("list_view",-1);' ) ; */
                               $result = sql_select($sql);
								?>
                                 <style>
                                    .wrd_brk{word-break: break-all;word-wrap: break-word;}
                                </style>
                                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width='850'>
                                    <thead>
                                        <tr>
                                            <th width="50">Check All <input type="checkbox" name="check_all" id="check_all" onClick="check_all_report()"></th>
                                            <th width="30" class="wrd_brk">SL</th>
                                            <th width="150" class="wrd_brk">Company Name</th>
                                            <th width="100" class="wrd_brk">Location Name</th>
                                            <th width="100" class="wrd_brk">Store Name</th>
                                            <th width="100" class="wrd_brk">Floor Name</th>
                                            <th width="100" class="wrd_brk">Room Name</th>
                                            <th width="100" class="wrd_brk">Rack Name</th>
                                            <th class="wrd_brk">Status</th>
                                        </tr>
                                    </thead>
                                </table>
                                <div style="width:850px; max-height:220px; overflow-y:scroll" id="scroll_body">
                                    <table width="830px" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_search">
                                        <tbody >
                                            <?
                                            $i = 1;
                                            foreach ($result as $row)
                                            {

                                                if ($i % 2 == 0) $bgcolor = "#E9F3FF";
                                                else $bgcolor = "#FFFFFF";
                                                ?>
                                                <tr bgcolor="<? echo $bgcolor; ?>"
                                                    style="cursor:pointer">
                                                    <td width="50" align="center" valign="middle">
                                                        <input id="chkRack_<? echo $i; ?>" type="checkbox" name="chkRack">

                                                        <input type="hidden" name="txtFloorRoomRackDtlsId[]" id="txtFloorRoomRackDtlsId_<? echo $i; ?>" value="<? echo $row[csf('floor_room_rack_dtls_id')]; ?>">
                                                    </td>
                                                    <td width="30" class="wrd_brk" onClick='get_php_form_data("<? echo $row[csf("floor_room_rack_id")].'_'.$row[csf("floor_room_rack_dtls_id")]; ?>","load_php_data_to_form","requires/rack_setup_controller")'><?php echo $i; ?></td>
                                                    <td width="150" class="wrd_brk" onClick='get_php_form_data("<? echo $row[csf("floor_room_rack_id")].'_'.$row[csf("floor_room_rack_dtls_id")]; ?>","load_php_data_to_form","requires/rack_setup_controller")'><?php echo $lib_company_arr[$row[csf("company_id")]]; ?></td>
                                                    <td width="100" class="wrd_brk" onClick='get_php_form_data("<? echo $row[csf("floor_room_rack_id")].'_'.$row[csf("floor_room_rack_dtls_id")]; ?>","load_php_data_to_form","requires/rack_setup_controller")'><?php echo $lib_location_arr[$row[csf("location_id")]]; ?></td>
                                                    <td width="100" class="wrd_brk" onClick='get_php_form_data("<? echo $row[csf("floor_room_rack_id")].'_'.$row[csf("floor_room_rack_dtls_id")]; ?>","load_php_data_to_form","requires/rack_setup_controller")'><?php echo $lib_store_arr[$row[csf("store_id")]]; ?></td>
                                                    <td width="100" class="wrd_brk" onClick='get_php_form_data("<? echo $row[csf("floor_room_rack_id")].'_'.$row[csf("floor_room_rack_dtls_id")]; ?>","load_php_data_to_form","requires/rack_setup_controller")'><?php echo $lib_floor_arr[$row[csf("floor_id")]]; ?></td>
                                                    <td width="100" class="wrd_brk" onClick='get_php_form_data("<? echo $row[csf("floor_room_rack_id")].'_'.$row[csf("floor_room_rack_dtls_id")]; ?>","load_php_data_to_form","requires/rack_setup_controller")'><?php echo $lib_room_arr[$row[csf("room_id")]]; ?></td>
                                                    <td width="100" class="wrd_brk" onClick='get_php_form_data("<? echo $row[csf("floor_room_rack_id")].'_'.$row[csf("floor_room_rack_dtls_id")]; ?>","load_php_data_to_form","requires/rack_setup_controller")'><?php echo $row[csf("floor_room_rack_name")]; ?></td>
                                                    <td class="wrd_brk" onClick='get_php_form_data("<? echo $row[csf("floor_room_rack_id")].'_'.$row[csf("floor_room_rack_dtls_id")]; ?>","load_php_data_to_form","requires/rack_setup_controller")'><?php echo $row_status[$row[csf("status_active")]]; ?></td>
                                                </tr>
                                                <? $i++;
                                            } ?>
                                        </tbody>

                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
                </tr>
			</table>
		</form>
	</fieldset>
    </div>
 </body>
 <script src="../../includes/functions_bottom.js" type="text/javascript">//set_bangla();</script>
