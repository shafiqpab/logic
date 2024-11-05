<?
/****************************************************************
|   Purpose         :   This form is Room Setup
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
echo load_html_head_contents("Inventory Room Setup Information", "../../", 1, 1,$unicode,'','');

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
	
	
	function fnc_inventory_room_info( operation )
	{
	   if (form_validation('cbo_company_name*cbo_location_name*cbo_store_name*cbo_floor_id*txt_room*txt_room_sequence','Company Name*Location Name*Store Name*Floor Name*Room Name*Room Sequence')==false)
		{
			return;
		}
		else
		{
			eval(get_submitted_variables('cbo_company_name*cbo_location_name*cbo_floor_id*txt_room*txt_room_sequence*cbo_store_name*cbo_status*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_floor_id*txt_room*txt_room_sequence*cbo_store_name*cbo_status*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/room_setup_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_inventoryRoom_reponse;
		}
	}
	
	function fnc_inventoryRoom_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);
			document.getElementById('update_id').value  = reponse[2];
			show_list_view('','inventoryRoom_list_view','inventory_room_list','../inventory/requires/room_setup_controller','setFilterGrid("list_view",-1)');
			set_button_status(0, permission, 'fnc_inventory_room_info',1);
			reset_form('inventoryRoomInfo_1','','');
			release_freezing();
		}
	}
			
 </script>
</head>
<body  onLoad="set_hotkey()">
	<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>
	<fieldset style="width:650px;">
		<legend>Inventory Room Information</legend>
		<form name="inventoryRoomInfo_1" id="inventoryRoomInfo_1" autocomplete="off">	
			<table cellpadding="0" cellspacing="2" width="100%" align="center" border="0">
            	<tr><td width="100%" align="center">
                        <table width="450" align="center">
                        <tr>
                            <td width="150" class="must_entry_caption">Company</td>
                            <td colspan="2"> <? 
								echo create_drop_down( "cbo_company_name", 262, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 $company_cond  order by company_name",'id,company_name', 1, '--- Select Company ---', 0, "load_drop_down( 'requires/room_setup_controller', this.value, 'load_drop_down_location', 'location' )" ); ?>
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
                            <td colspan="2">
                            <input type="text" name="txt_room" id="txt_room" style="width:250px" class="text_boxes"  maxlength="50" title="Maximum 50 Character">
                            </td>
                            
                        </tr>                     
                         <tr>
                            <td width="150" class="must_entry_caption">Room Sequence</td>
                            <td colspan="2">
                            <input type="text" name="txt_room_sequence" id="txt_room_sequence" style="width:250px" class="text_boxes_numeric"  maxlength="50" title="Maximum 50 Character">
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
                                    echo load_submit_buttons( $permission, "fnc_inventory_room_info", 0,0 ,"reset_form('inventoryRoomInfo_1','','',1)",1);
                                ?>
                            </td>					
                        </tr>
                        <tr>
                           <td colspan="3" height="20" valign="bottom" align="center" class="button_container"></td>					
                        </tr>
                        <tr>
                           <td colspan="3" valign="bottom" align="center"  id="inventory_room_list">
							 <?
							 	$lib_company_arr=return_library_array( "select id,company_name from lib_company where status_active=1 and is_deleted=0", "id","company_name" );
							 	$lib_location_arr=return_library_array( "select id,location_name from lib_location where status_active=1 and is_deleted=0", "id","location_name" );
							 	$lib_floor_arr=return_library_array( "select floor_id,floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id and a.status_active=1 and a.is_deleted=0", "floor_id","floor_room_rack_name" );
                                $lib_store_arr=return_library_array("select id, store_name from lib_store_location where status_active = 1 and is_deleted = 0", 'id', 'store_name'); 
                                // $arr=array(0=>$lib_company_arr,1=>$lib_location_arr,2=>$lib_floor_arr,4=>$row_status);
                                $arr=array(0=>$lib_company_arr,1=>$lib_location_arr,2=>$lib_store_arr,3=>$lib_floor_arr,5=>$row_status); // issue id:7657
                                if($db_type==0)
                                   {
                                    $sql="select a.company_id,b.location_id,b.floor_id,a.floor_room_rack_name,a.status_active, b.store_id, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and b.rack_id is null and b.shelf_id ='' and b.bin_id ='' group by a.company_id,b.location_id,b.floor_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id,b.store_id";
                                   }
                                   else
                                   {
                                    $sql="select a.company_id,b.location_id,b.floor_id,a.floor_room_rack_name,a.status_active,b.store_id, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and b.rack_id is null and b.shelf_id is null and b.bin_id is null group by a.company_id,b.location_id,b.floor_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id,b.store_id";
                                   }
                                // echo  create_list_view ( "list_view", "Company Name,Location Name,Floor Name,Room Name,Status", "150,100,100,100,50","600","220",1,$sql,"get_php_form_data", "floor_room_rack_id,floor_room_rack_dtls_id","'load_php_data_to_form'", 1, "company_id,location_id,floor_id,0,status_active", $arr , "company_id,location_id,floor_id,floor_room_rack_name,status_active", "../inventory/requires/room_setup_controller", 'setFilterGrid("list_view",-1);' ) ;
                                echo create_list_view ( "list_view", "Company Name,Location Name,Store,Floor Name,Room Name,Status", "150,80,100,80,80","600","220",1, $sql, "get_php_form_data", "floor_room_rack_id,floor_room_rack_dtls_id","'load_php_data_to_form'", 1, "company_id,location_id,store_id,floor_id,0,status_active", $arr, "company_id,location_id,store_id,floor_id,floor_room_rack_name,status_active", "../inventory/requires/room_setup_controller", 'setFilterGrid("list_view",-1);' ) ;  // issue id: 7687
								?>
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
