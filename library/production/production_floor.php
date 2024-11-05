<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Production Floor Information", "../../", 1, 1,$unicode,'','');

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
	
	function fnc_product_floor_info( operation )
	{
	   if (form_validation('cbo_company_name*cbo_location_name*txt_floor*txt_floor_sequence*cbo_production_process','Company Name*Location Name*Floor Name*Floor Sequence*Production Process')==false)
		{
			return;
		}
		else
		{
			eval(get_submitted_variables('cbo_company_name*cbo_location_name*txt_floor*cbo_status*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_location_name*txt_floor*txt_group_name*txt_floor_sequence*cbo_production_process*cbo_status*update_id*cbo_client',"../../");
			freeze_window(operation);
			http.open("POST","requires/production_floor_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_productionfloor_reponse;
		}
	}
	
	function fnc_productionfloor_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==50)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
		
			if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);
			document.getElementById('update_id').value  = reponse[2];
			show_list_view('','productionfloor_list_view','product_floor_list','../production/requires/production_floor_controller','setFilterGrid("list_view",-1)');
			set_button_status(0, permission, 'fnc_product_floor_info',1);
			reset_form('productfloorinfo_1','','');
			release_freezing();
		}
	}

    $(function()
    {
        $("#txt_group_name").keyup(function()
        {
            var min_length = 0; // min caracters to display the autocomplete
            var keyword = $('#txt_group_name').val();
            var action = "open_group_name_suggession";
            if (keyword.length >= min_length) 
            {
                $.ajax({
                    url: 'requires/production_floor_controller.php',
                    type: 'POST',
                    data: {keyword:keyword,action:action},
                    success:function(data)
                    {
                        $('#txt_group_name_list').show();
                        $('#txt_group_name_list').html(data);
                    }
                });
            } 
            else 
            {
                $('#txt_group_name_list').hide();
            }
        });

        $(document).click(function() { $('#txt_group_name_list').hide(); });
    });

            
    // set_item : this function will be executed when we select an item
    function set_item(item) 
    {
        // change input value
        $('#txt_group_name').val(item);
        // hide proposition list
        $('#txt_group_name_list').hide();
    }

    function get_company_config(company_id)
    {
        get_php_form_data(company_id,'get_company_config','requires/production_floor_controller' );
    }  
			
 </script> 
<style type="text/css">
    .group_container ul 
    {
        width: 90px;
        position: absolute;
        z-index: 9;
        background: #c6deff;
        list-style: none;
        margin-left: 170px;
        border-radius: 4px;
        max-height: 250px;
        overflow-y: auto;
    }
    .group_container ul li 
    {
        padding: 2px 5px;
        border-bottom: 1px dotted #f9f9f9;
        cursor: pointer;
    }
    .group_container ul li:hover 
    {
        background: #ffffff;
    }
    #txt_group_name_list 
    {
        display: none;
    }
</style>
</head>
<body  onLoad="set_hotkey()">
	<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>
	<fieldset style="width:650px;">
		<legend>Product Floor Information</legend>
		<form name="productfloorinfo_1" id="productfloorinfo_1" autocomplete="off">	
			<table cellpadding="0" cellspacing="2" width="100%" align="center" border="0">
            	<tr><td width="100%" align="center">
                        <table width="550" align="center">
                        <tr>
                            <td width="150" class="must_entry_caption">Company</td>
                            <td colspan="2"> 
                                <? 
												echo create_drop_down( "cbo_company_name", 362, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 $company_cond  order by company_name",'id,company_name', 1, '--- Select Company ---', 0, "get_company_config(this.value)" ); ?>
                         </td>
                        </tr>
                        <tr>
                            <td width="150" class="must_entry_caption">Location</td>
                            <td colspan="2" id="location"> 	
							<? 
								 echo create_drop_down( "cbo_location_name", 362, "select location_name,id from lib_location where is_deleted=0  and status_active=1 order by location_name",'id,location_name', 1, '--- Select Location ---', 0, "" );
                            ?>
                            </td>
                        </tr>	
                        <tr>
                            <td width="150" class="must_entry_caption">Floor</td>
                            <td colspan="2" style="display:inline-flex;">
                                <input type="text" name="txt_floor" id="txt_floor" style="width:120px" class="text_boxes"  maxlength="50" title="Maximum 50 Character">
                                <span class="group_container">
                                    Group
                                    <input type="text" name="txt_group_name" id="txt_group_name" style="width:80px" class="text_boxes"  maxlength="50" title="Maximum 50 Character">
                                    <ul id="txt_group_name_list"></ul>
                                </span>
                                <div id="party_type_td">
                                    <? echo create_drop_down( "cbo_client", 100, $blank_array,"", 1, "-- Select Client --", $selected, "" ); ?>
                                </div>
                            </td>
                            
                            
                        </tr>
                         <tr>
                            <td width="150" class="must_entry_caption">Floor Sequence</td>
                            <td colspan="2">
                            <input type="text" name="txt_floor_sequence" id="txt_floor_sequence" style="width:350px" class="text_boxes_numeric"  maxlength="50" title="Maximum 50 Character">
                            </td>
                            
                        </tr>		
                        <tr>
                            <td class="must_entry_caption">Production Process</td>
                            <td  colspan="2">
                                <? 
                                    asort($production_process);
                                    echo create_drop_down( "cbo_production_process", 362, $production_process,'', 1, '--Select--', 0 );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td  colspan="2"><? 
                                    echo create_drop_down( "cbo_status", 362, $row_status,'', '', '', 1 );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" align="center">&nbsp;						
                                <input  type="hidden"name="update_id" id="update_id">	
                            </td>					
                        </tr>
                        <tr>
                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                <? 
                                    echo load_submit_buttons( $permission, "fnc_product_floor_info", 0,0 ,"reset_form('productfloorinfo_1','','',1)",1);
                                ?>
                            </td>					
                        </tr>
                        <tr>
                           <td colspan="3" height="20" valign="bottom" align="center" class="button_container"></td>					
                        </tr>
                        <tr>
                           <td colspan="3" valign="bottom" align="center"  id="product_floor_list">
							 <?
                                $client_arr= return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
                                $arr=array(4=>$client_arr,5=>$production_process,6=>$row_status);
                                echo  create_list_view ( "list_view", "Company Name,Location Name,Floor Name,Floor Group,Clinet,Prod. Process,Status", "150,100,100,100,100,100,50","800","220",1, "select c.company_name,l.location_name,a.client_id,a.floor_name,a.group_name,a.status_active, a.production_process, a.id from  lib_prod_floor a, lib_company c, lib_location l  where a.company_id=c.id and a.location_id=l.id and a.is_deleted=0  order by a.floor_name", "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,0,0,0,client_id,production_process,status_active", $arr , "company_name,location_name,floor_name,group_name,client_id,production_process,status_active", "../production/requires/production_floor_controller", 'setFilterGrid("list_view",-1);' ) ;
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
