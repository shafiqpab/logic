<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sewing Production Line
					Selected company will populate Location and location onchange will change Floor
Functionality	:	Must fill Company, Location, Floor, Line Name
JS Functions	:
Created by		:	CTO 
Creation date 	: 	07-10-2012
Updated by 		:   REZA, Shafiq		
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
echo load_html_head_contents("Sewing Line Information", "../../", 1, 1,$unicode,'','');

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

function fnc_sewing_line_info( operation )
{
   if (form_validation('cbo_company_name*cbo_location_name*cbo_floor_name*txt_sewing_line_serial*txt_line_name*cbo_product_category','Company Name*Location Name*Floor Name*Line Serial*Line Name*Product Category')==false)
	{
		return;
	}
	
	else
	{
		eval(get_submitted_variables('cbo_company_name*cbo_location_name*cbo_floor_name*txt_sewing_line_serial*txt_line_name*txt_sewing_group*cbo_product_category*cbo_status*txt_man_power*update_id'));
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_floor_name*txt_sewing_line_serial*txt_line_name*txt_sewing_group*cbo_product_category*cbo_status*txt_man_power*update_id*txt_user_ids*txt_item_ids',"../../");
		freeze_window(operation);
		http.open("POST","requires/sewing_line_controller.php", true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_sewing_line_info_reponse;
	}
}

function fnc_sewing_line_info_reponse()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		document.getElementById('update_id').value  = reponse[2];
		show_list_view('','sewing_line_list_view','sewing_line_list','../production/requires/sewing_line_controller','setFilterGrid("list_view",-1)');
		set_button_status(0, permission, 'fnc_sewing_line_info',1);
		reset_form('sewinglineinfo_1','','','','','cbo_company_name*cbo_location_name*cbo_floor_name');
		release_freezing();
	}
}	

    const get_user_list=()=>{
		let cbo_company_name = document.getElementById('cbo_company_name').value;
		let cbo_location_name = document.getElementById('cbo_location_name').value;
		let txt_user_ids = document.getElementById('txt_user_ids').value;
		
		let title = 'User Selection Form';
		let page_link = 'requires/sewing_line_controller.php?action=get_user_list&cbo_company_name='+cbo_company_name+'&cbo_location_name='+cbo_location_name+'&txt_user_ids='+txt_user_ids;

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			let theform=this.contentDoc.forms[0]
			let user_ids=this.contentDoc.getElementById("selected_user_ids").value;
			let user_name=this.contentDoc.getElementById("selected_user_name").value;
			document.getElementById('txt_user_ids').value=user_ids;
			document.getElementById('txt_user_name').value=user_name;
		}
    }

    	

    const get_item_list=()=>{
		let cbo_company_name = document.getElementById('cbo_company_name').value;
		let cbo_location_name = document.getElementById('cbo_location_name').value;
		let txt_item_ids = document.getElementById('txt_item_ids').value;
		
		let title = 'User Selection Form';
		let page_link = 'requires/sewing_line_controller.php?action=get_item_list&cbo_company_name='+cbo_company_name+'&cbo_location_name='+cbo_location_name+'&txt_item_ids='+txt_item_ids;

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			let theform=this.contentDoc.forms[0]
			let user_ids=this.contentDoc.getElementById("selected_user_ids").value;
			let user_name=this.contentDoc.getElementById("selected_user_name").value;
			document.getElementById('txt_item_ids').value=user_ids;
			document.getElementById('txt_gmts_item').value=user_name;
		}
    }

 </script>
</head>

<body  onLoad="set_hotkey()">
	<div align="center" style="width:100%;">
		<?=load_freeze_divs ("../../",$permission);  ?>
	<fieldset style="width:650px;">
		<legend>Sewing Line Info</legend>
		<form name="sewinglineinfo_1" id="sewinglineinfo_1" autocomplete="off">	
			<table cellpadding="0" cellspacing="2" width="100%" align="center" border="0">
            	<tr><td width="100%" align="center">
                        <table width="450" align="center">
                        <tr>
                            <td width="150" class="must_entry_caption">Company</td>
                            <td colspan="2"><?=create_drop_down( "cbo_company_name", 262, "select company_name,id from lib_company where is_deleted=0  and status_active=1 $company_name order by company_name",'id,company_name', 1, '--- Select Company ---', 0, "load_drop_down( 'requires/sewing_line_controller', this.value, 'load_drop_down_location', 'location' )" ); ?></td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Location</td>
                            <td colspan="2" id="location"> <?=create_drop_down( "cbo_location_name", 262, "select location_name,id from lib_location where is_deleted=0  and status_active=1  order by location_name",'id,location_name', 1, '--- Select Location ---', 0, "load_drop_down( 'requires/sewing_line_controller', this.value, 'load_drop_down_floor', 'floor' )" ); ?></td>
                        </tr>	
                        <tr>
                            <td class="must_entry_caption">Floor</td>
                            <td colspan="2" id="floor"> 	
                                <? 
                                    echo create_drop_down( "cbo_floor_name", 262, "select floor_name,id from  lib_prod_floor where is_deleted=0  and status_active=1 and PRODUCTION_PROCESS = 5  order by floor_name",'id,floor_name', 1, '--- Select Floor ---', 0, '' );
                                ?>
                            </td>
                        </tr>		
                        <tr>
                            <td class="must_entry_caption">Sewing Line Sequence</td>
                            <td colspan="2">
                                <input type="text" name="txt_sewing_line_serial" id="txt_sewing_line_serial" class="text_boxes_numeric" style="width:250px" />						
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Line Name-Sewing Group</td>
                            <td colspan="2">
                                <input type="text" name="txt_line_name" id="txt_line_name" class="text_boxes" style="width:115px" placeholder="Line Name" />---<input type="text" name="txt_sewing_group" id="txt_sewing_group" class="text_boxes" style="width:115px" placeholder="Sewing Group" />						
                            </td>
                        </tr>
                         <tr>
                            <td>Garments Item</td>
                            <td  colspan="2">
                                <input type="text" name="txt_gmts_item" id="txt_gmts_item" class="text_boxes" style="width:250px" placeholder="Brows" onDblClick="get_item_list()" readonly /> 
                                <input type="hidden" name="txt_item_ids" id="txt_item_ids" readonly />			                                     
                            </td>
                        </tr>  
                        <tr>
                            <td class="must_entry_caption">Product Category </td>
                            <td colspan="2"><?=create_drop_down( "cbo_product_category", 262, $product_category,'', 1,"--- Select Category ---",0,"load_drop_down( 'requires/garment_item_controller', this.value, 'load_drop_down_product_type', 'product_type' );"); ?> </td>
						</tr>
                        <tr>
                            <td>Machine/MP</td>
                            <td colspan="2">
                                <input type="text" name="txt_man_power" id="txt_man_power" class="text_boxes_numeric" style="width:250px" />                      
                            </td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td colspan="2"><?=create_drop_down( "cbo_status", 262, $row_status,'', '', '', 1 ); ?> </td>
                        </tr>
                        <?php
                        if($_SESSION['logic_erp']['user_level']==2){
                        ?>
                         <tr>
                            <td>Tag User</td>
                            <td  colspan="2">
                                <input type="text" name="txt_user_name" id="txt_user_name" class="text_boxes" style="width:250px" placeholder="Brows" onDblClick="get_user_list()" readonly />                                      
                            </td>
                        </tr>                
                        <?php
                        }
                        ?>                     
                        <tr>
                            <td colspan="3" align="center">
                                <input type="hidden" name="txt_user_ids" id="txt_user_ids" readonly />						
                                <input type="hidden"name="update_id" id="update_id" readonly>	
                            </td>					
                        </tr>
                        <tr>
                           <td colspan="3" valign="bottom" align="center" class="button_container">
                                <?=load_submit_buttons( $permission, "fnc_sewing_line_info", 0,0 ,"reset_form('sewinglineinfo_1','','','')"); ?>
                            </td>					
                        </tr>
                        <tr>
                           <td colspan="3" height="20" valign="bottom" align="center" class="button_container"></td>					
                        </tr>
                        <tr>
                           <td colspan="3" valign="bottom" align="center"  id="sewing_line_list">
							 <?
                                $floor=return_library_array( "select floor_name,id from  lib_prod_floor where is_deleted=0", "id", "floor_name"  );
                                $arr=array(2=>$floor);
                                echo  create_list_view ( "list_view", "Company,Location,Floor,Sewing Line,Sewing Group,Line Serial,Man Power", "120,120,80,80,70,70","650","220",1, "SELECT c.company_name,l.location_name,a.floor_name, a.sewing_line_serial, a.sewing_group, a.line_name,a.id,a.man_power from lib_sewing_line a, lib_company c, lib_location l  where a.company_name=c.id and a.location_name=l.id and a.is_deleted=0  order by c.company_name,l.location_name,a.floor_name,a.sewing_line_serial asc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,0,floor_name", $arr , "company_name,location_name,floor_name,line_name,sewing_group,sewing_line_serial,man_power", "../production/requires/sewing_line_controller", 'setFilterGrid("list_view",-1);' ); ?>
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
    

