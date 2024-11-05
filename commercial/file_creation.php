<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for inter file no
Functionality	:
JS Functions	:
Created by		:	Md Jakir Hosen
Creation date 	: 	18-09-2012
Updated by 		: 	Md Jakir Hosen
Update date		: 	18-09-2022
QC Performed BY	:
QC Date			:
Comments		:

*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$company_credential_cond = "";
if ($company_id != "") {
    $company_credential_cond = "and comp.id in($company_id)";
}
$user_id = $_SESSION['logic_erp']['user_id'];
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("File Creation", "../", 1, 1,'','1','');
?>
<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

    var permission='<? echo $permission; ?>';
    <?
    if($_SESSION['logic_erp']['data_arr'][107])
    {
        $data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][107] );
        echo "var field_level_data= ". $data_arr . ";\n";
    }
    ?>

    function fnc_file_creation(operation)
    {
		if(operation==2)
		{
			alert("Delete Not Allow.");
			show_msg(13);return;
		}
		
        if (form_validation('cbo_company_name*cbo_buyer_name*cbo_file_type*txt_year*txt_file_date*cbo_file_status*cbo_file_closing_status', 'Company*Buyer Name*File Year*File Date*File Status*File Closing Status')==false)
        {
            return;
        }
        if(operation == 4){
            print_report( $('#txt_system_id').val()+'*'+$('#cbo_company_name').val(), "print_file_creation", "requires/file_creation_controller" );
            return;
        }
        var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_system_id*cbo_company_name*cbo_buyer_name*cbo_file_type*txt_year*txt_file_date*cbo_file_status*cbo_file_closing_status*txt_file_value*cbo_currency_name*txt_file_qty*txt_conversion_factor*cbo_lien_bank*txt_ship_date*cbo_ready_to_approved*txt_remarks*txt_style_ref*cbo_item_name*txt_fab_description*txt_yarn_qnty*txt_fob*txt_created_user',"../");

        freeze_window(operation);

        http.open("POST","requires/file_creation_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_file_creation_response;
    }

    function fnc_file_creation_response()
    {
        if(http.readyState == 4)
        {
            var reponse=trim(http.responseText).split('**');
            if(trim(reponse[0])==0)
            {
                $('#file_no').val(reponse[1]);
                $('#txt_system_id').val(reponse[2]);
                $('#txt_created_user').val(reponse[3]);
                disable_fields("cbo_company_name*cbo_buyer_name*cbo_file_type*txt_year");
                show_msg(trim(reponse[0]));
                $('#div_button_container').html('');
                show_list_view(reponse[2],'file_creation_list_view','item_group_list_view','requires/file_creation_controller','setFilterGrid("list_view",-1)');
                set_button_status(1, permission, 'fnc_file_creation',1, 1);
            }else if(trim(reponse[0])==1){
                $('#div_button_container').html('');
                disable_fields("cbo_company_name*cbo_buyer_name*cbo_file_type*txt_year");
                show_list_view(reponse[1],'file_creation_list_view','item_group_list_view','requires/file_creation_controller','setFilterGrid("list_view",-1)');
                show_msg(trim(reponse[0]));
            }else if(trim(reponse[0])==2){
                $('#div_button_container').html('');
                reset_form('file_creation_1','','','','enable_fields(\'cbo_company_name*cbo_buyer_name*cbo_file_type*txt_year\')');
                show_msg(trim(reponse[0]));
                show_list_view(reponse[1],'file_creation_list_view','item_group_list_view','requires/file_creation_controller','setFilterGrid("list_view",-1)');
            }
            else{
                show_msg(trim(reponse[0]));
            }
            release_freezing();
        }
    }

    function new_window()
	{		
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		$('#list_view tr:first').hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../css/style_print.css" type="text/css"/></head><body>'+document.getElementById('item_group_list_view').innerHTML+'</body</html>');
		d.close();

		$('#list_view tr:first').show();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="225px";
	}

    function enable_fields(str){
        var splitStr = str.split('*');
        $.each(splitStr, function (index, val){
            $('#'+val).prop('disabled', false);
        });
    }

    function disable_fields(str){
        var splitStr = str.split('*');
        $.each(splitStr, function (index, val){
            $('#'+val).prop('disabled', true);
        });
    }
	
	function check_exchange_rate()
	{
		var cbo_company_id=$('#cbo_company_name').val();
		var cbo_currercy=$('#cbo_currency_name').val();
		var receive_date = $('#txt_file_date').val();
		if( form_validation('cbo_company_name*cbo_currency_name*txt_file_date','Company Name*Currency*Date')==false )
		{
			return;
		}
		var response=return_global_ajax_value( cbo_currercy+"**"+receive_date+"**"+cbo_company_id, 'check_conversion_rate', '', 'requires/file_creation_controller');
		var response=response.split("_");
		$('#txt_conversion_factor').val(response[1]);
	}
	
	function fnResetForm()
	{
		reset_form('file_creation_1','','','','enable_fields(\'cbo_company_name*cbo_buyer_name*cbo_file_type*txt_year\')','txt_file_date*txt_year');
		set_button_status(0, permission, 'fnc_file_creation',1, 0);
	}
	
	function openmypage_style_ref()
	{
        
		if( form_validation('cbo_company_name*cbo_buyer_name','Company Name*Buyer')==false )
		{
			return;
		}
		var cbo_buyer = $('#cbo_buyer_name').val();
		var title = 'Style Ref';	
		var page_link = 'requires/file_creation_controller.php?action=style_ref_popup&cbo_buyer='+cbo_buyer;
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var style_id=this.contentDoc.getElementById("hidden_style_id").value;
			var buyer_id=this.contentDoc.getElementById("hidden_buyer_id").value;
			var style_ref=this.contentDoc.getElementById("hidden_style_ref").value;
			var product_dep_id=this.contentDoc.getElementById("hidden_product_dep_id").value;
			$('#cbo_buyer').val(buyer_id);
			$('#txt_style_ref').val(style_ref);
			$('#cbo_buyer').attr('disabled',true);
			$('#txt_style_ref').attr('readonly',true);
			//$('#txt_internal_ref').attr('readonly',true);
		}
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../",$permission); ?>

    <fieldset style="width:1080px; margin-bottom:10px;">
        <legend>File Creation Entry</legend>
        <form name="file_creation_1" id="file_creation_1" autocomplete="off" method="POST" action="" >
            <table cellpadding="0" cellspacing="1" width="100%">
                <tr>
                    <td colspan="8" align="center" ><b>File No.</b>
                        <input type="hidden" name="txt_system_id" id="txt_system_id" readonly class="text_boxes">
                        <input type="text" name="file_no" id="file_no"  placeholder="Display" readonly class="text_boxes">
                    </td>
                </tr>
                <tr><td height="5" colspan="8"></td></tr>
                <tr>
                    <td width="120" class="must_entry_caption" style="padding: 3px 0px;">Company</td>
                    <td width="150">
                        <?
                        echo create_drop_down( "cbo_company_name", 142, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_credential_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/file_creation_controller', this.value, 'load_drop_down_buyer_search', 'buyer_td_id' );check_exchange_rate();" );
                        ?>
                    </td>
                    <td class="must_entry_caption" width="120">Buyer Name</td>
                    <td id="buyer_td_id" width="150">
                        <? echo create_drop_down( "cbo_buyer_name", 142, $blank_array,"", 1, "-- Select Buyer --", 0, "" ); ?>
                    </td>
                    <td class="must_entry_caption" width="120">File Type</td>
                    <td width="150">
                        <?
                        $file_type = array(1 => "Yarn Procurement", 2=>"Projection Order", 3=>"Confirm Order");
                        echo create_drop_down( "cbo_file_type", 142, $file_type,"", 1, "-- Select Type --", 0, "" );
                        ?>
                    </td>
                    <td width="120">Style Name</td>
                    <td width="150">
                        <input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:130px" placeholder="Write/Browse" onDblClick="openmypage_style_ref()" title="" />
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption">File Year</td>
                    <td>
						<?
							//$sql="select distinct TO_CHAR(pub_shipment_date, 'YYYY') from wo_po_break_down where status_active=1 and is_deleted=0";
							//echo create_drop_down( "txt_year", 100,$sql,"txt_year,txt_year", 0, "-- Select --", 0,"");
							?>
						<input name="txt_year" id="txt_year" style="width:130px" class="text_boxes_numeric" maxlength="4" value="<?=date('Y')+1;?>" title="Maximum Character 4" >
					</td>
                    <td class="must_entry_caption">File Date</td>
                    <td >
                        <input type="text" name="txt_file_date" id="txt_file_date" style="width:130px"  onChange="check_exchange_rate();" class="datepicker" readonly value="<?=date('d-m-Y')?>"/>
                    </td>
                    <td class="must_entry_caption">File Status</td>
                    <td>
                        <?
                        $file_status = array(1 => "Active", 2=>"Inactive");
                        echo create_drop_down( "cbo_file_status", 142, $file_status,"", 0, "-- Select Status --", 1, "" );
                        ?>
                    </td>
                    <td>Garments Item</td>
                    <td>
                        <?
						echo create_drop_down( "cbo_item_name", 142, $garments_item,"", 0, "", $selected, "",0,0 );
						?>
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption">File Closing Status</td>
                    <td>
                        <?
                        $file_closing_status = array(1 => "Running", 2=>"Close");
                        echo create_drop_down( "cbo_file_closing_status", 142, $file_closing_status,"", 0, "-- Select Closing Status --", 1, "" );
                        ?>
                    </td>
                    <td style="padding: 3px 0px;">File Value</td>
                    <td >
                        <input type="text" name="txt_file_value" id="txt_file_value" style="width:130px" class="text_boxes_numeric"  value="" placeholder="Write"/>
                    </td>
                    <td>File Currency</td>
                    <td>
                        <?
                        echo create_drop_down( "cbo_currency_name", 142, $currency,"", 0, "", 2, "check_exchange_rate();" );
                        ?>
                    </td>
                    <td>Fabric Description</td>
                    <td>
                        <input type="text" name="txt_fab_description" id="txt_fab_description" style="width:130px" class="text_boxes"  value=""/>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 3px 0px;">File Qty</td>
                    <td >
                        <input type="text" name="txt_file_qty" id="txt_file_qty" style="width:130px" class="text_boxes_numeric" placeholder="Write" value=""/>
                    </td>
                    <td style="padding: 3px 0px;">Exchange Rate</td>
                    <td >
                        <input type="text" name="txt_conversion_factor" id="txt_conversion_factor" style="width:130px" class="text_boxes_numeric"  value="" placeholder="Write"/>
                    </td>
                    <td>Lien Bank</td>
                    <td>
                        <?
                        if ($db_type==0)
                        {
                            echo create_drop_down( "cbo_lien_bank", 142, "select concat(a.bank_name,' (', a.branch_name,')') as bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- Select Lien Bank --", 0, "" );
                        }
                        else
                        {
                            echo create_drop_down( "cbo_lien_bank", 142, "select (bank_name || ' (' || branch_name || ')' ) as bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- Select Lien Bank --", 0, "" );
                        }
                        ?>
                    </td>
					<td>Yarn Qty</td>
                    <td>
                        <input type="text" name="txt_yarn_qnty" id="txt_yarn_qnty" style="width:130px" class="text_boxes_numeric"  value=""/>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 3px 0px;">Approx Ship Date</td>
                    <td >
                        <input type="text" name="txt_ship_date" id="txt_ship_date" style="width:130px" class="datepicker" readonly value=""/>
                    </td>
                    <td style="padding: 3px 0px;">Ready To Approve</td>
                    <td><? echo create_drop_down( "cbo_ready_to_approved", 142, $yes_no,"", 1, "-- Select --", 2, "","","" ); ?></td>

                    <td>Created By</td>
                    <td>
                        <?=create_drop_down( "txt_created_user", 142, "select id,team_leader_name from lib_marketing_team where project_type=1 and team_type in (0,1,2) and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "" ); ?>
                    </td>
                    <td>FOB</td>
                    <td>
                        <input type="text" name="txt_fob" id="txt_fob" style="width:130px" class="text_boxes_numeric"  value=""/>
                    </td>
                </tr>
                <tr>
                    <td>Remarks</td>
                    <td colspan="7">
                        <input type="text" name="txt_remarks" id="txt_remarks" style="width:945px" class="text_boxes"  value="" placeholder="Write"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="8" height="50" valign="middle" align="center" class="button_container">
                        <? echo load_submit_buttons( $permission, "fnc_file_creation", 0,1,"fnResetForm();",1); ?>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
    <?
    ob_start();
    $html = '<div style="width:100%; float:left; margin:auto" align="center">
    <fieldset style="width:1300px; margin-top:20px">
        <legend>File No. List View </legend>
        
        <div style="width:1300px; margin-top:3px; margin-bottom: 3px;" id="item_group_list_view" align="left">
            <table class="rpt_table" id="rpt_tablelist_view" rules="all" width="1280" cellspacing="0" cellpadding="0" border="0">
                <thead>
                    <tr>
                        <th width="50">SL No</th>
                        <th width="120">Company</th>
                        <th width="120"> Buyer</th>
                        <th width="50"> File Year</th>
                        <th width="100"> File Type</th>
                        <th width="110"> File No.</th>
                        <th width="70"> File Date</th>
                        <th width="60"> Closing Status</th>
                        <th width="90"> Style</th>
                        <th width="90"> Fabric Description</th>
                        <th width="80"> Yarn Qty</th>
                        <th width="80"> FOB</th>
                        <th width="60"> File Status</th>
                        <th width="100"> Insert By</th>
                        <th> Approved</th>
                    </tr>
                </thead>
            </table>

            <div style="width:1300px; max-height:220px; overflow-y:scroll" align="left" id="scroll_body">
                <table class="rpt_table" id="list_view" rules="all" width="1280" cellspacing="0" cellpadding="0" border="0">';
    ?>
    <div style="width:100%; float:left; margin:auto" align="center">
        <fieldset style="width:1300px; margin-top:20px">
            <legend>File No. List View </legend>
            
            <div style="width:1300px; margin-top:3px; margin-bottom: 3px;" id="item_group_list_view" align="left">
                <table class="rpt_table" id="rpt_tablelist_view" rules="all" width="1280" cellspacing="0" cellpadding="0" border="0">
                    <thead>
                        <tr>
                            <th width="50">SL No</th>
                            <th width="120">Company</th>
                            <th width="120"> Buyer</th>
                            <th width="50"> File Year</th>
                            <th width="100"> File Type</th>
                            <th width="110"> File No.</th>
                            <th width="70"> File Date</th>
                            <th width="60"> Closing Status</th>
                            <th width="90"> Style</th>
                            <th width="90"> Fabric Description</th>
                            <th width="80"> Yarn Qty</th>
                            <th width="80"> FOB</th>
                            <th width="60"> File Status</th>
                            <th width="100"> Insert By</th>
                            <th> Approved</th>
                        </tr>
                    </thead>
                </table>

                <div style="width:1300px; max-height:220px; overflow-y:scroll" align="left" id="scroll_body">
                    <table class="rpt_table" id="list_view" rules="all" width="1280" cellspacing="0" cellpadding="0" border="0">
                        <?
                        $company_cond1 = "";
                        if($company_id != ""){
                            $company_cond1 = " and company_id in ($company_id)";
                        }
                        
                        $buyer_name=return_library_array("select id, buyer_name from lib_buyer where status_active = 1 and is_deleted = 0","id","buyer_name");
                        $company_name=return_library_array("select id, company_name from lib_company where status_active = 1 and is_deleted = 0","id","company_name");
                        $user_arr = return_library_array("select id, user_name from user_passwd", "id", "user_name");
                        $file_type_arr = array(1 => "Yarn Procurement", 2=>"Projection Order", 3=>"Confirm Order");
                        $file_closing_status_arr = array(1 => "Running", 2=>"Close");
                        $file_status_active = array(1 => "Active", 2=>"Inactive");
                        
                        $sql = "SELECT id, company_id, buyer_id, file_year, file_type, file_no, file_date, file_closing_status, style_ref_no, fabric_description, yarn_qnty, fob, status_active, insert_user, approve_status from lib_file_creation where is_deleted=0 $company_cond1 order by id desc";
                        $result = sql_select($sql);

                        $sl = 1;
                        foreach($result as $row)
                        {
                            $id = $row['ID'];
                            $company_id = $company_name[$row['COMPANY_ID']];
                            $buyer_id = $buyer_name[$row['BUYER_ID']];
                            $file_year = $row['FILE_YEAR'];
                            $file_type = $file_type_arr[$row['FILE_TYPE']];
                            $file_no = $row['FILE_NO'];
                            $file_date = $row['FILE_DATE'];
                            $file_closing_status = $file_closing_status_arr[$row['FILE_CLOSING_STATUS']];
                            $style_ref_no = $row['STYLE_REF_NO'];
                            $fabric_description = $row['FABRIC_DESCRIPTION'];
                            $yarn_qnty = $row['YARN_QNTY'];
                            $fob = $row['FOB'];
                            $status_active = $file_status_active[$row['STATUS_ACTIVE']];
                            $insert_user = $user_arr[$row['INSERT_USER']];
                            $approve_status = $yes_no[$row['APPROVE_STATUS']];

                            $bgcolor=($sl%2==0)? "#E9F3FF":"#FFFFFF";
                            ?>
                            <tr style="cursor:pointer" bgcolor="<?=$bgcolor; ?>" onclick="get_php_form_data('<?= $id?>','load_php_data_to_form','requires/file_creation_controller')" id="tr_<?=$sl; ?>">
                                <td width="50"><? echo $sl; ?></td>
                                <td width="120"><? echo $company_id; ?></td>
                                <td width="120"><? echo $buyer_id; ?></td>
                                <td width="50"><? echo $file_year; ?></td>
                                <td width="100"><? echo $file_type; ?></td>
                                <td width="110"><? echo $file_no; ?></td>
                                <td width="70"><? echo $file_date; ?></td>
                                <td width="60"><? echo $file_closing_status; ?></td>
                                <td width="90"><? echo $style_ref_no; ?></td>
                                <td width="90"><? echo $fabric_description; ?></td>
                                <td width="80"><? echo $yarn_qnty; ?></td>
                                <td width="80"><? echo $fob; ?></td>
                                <td width="60"><? echo $status_active; ?></td>
                                <td width="100"><? echo $insert_user; ?></td>
                                <td><? echo $approve_status; ?></td>
                            </tr>
                            <?
                            $html .= '<tr style="cursor:pointer" bgcolor="'.$bgcolor.'" onclick="get_php_form_data(\''.$id.'\',\'load_php_data_to_form\',\'requires/file_creation_controller\')" id="tr_'.$sl.'">
                            <td width="50">'. $sl .'</td>
                            <td width="120">'. $company_id .'</td>
                            <td width="120">'. $buyer_id .'</td>
                            <td width="50">'. $file_year .'</td>
                            <td width="100">'. $file_type .'</td>
                            <td width="110">'. $file_no .'</td>
                            <td width="70">'. $file_date .'</td>
                            <td width="60">'. $file_closing_status .'</td>
                            <td width="90">'. $style_ref_no .'</td>
                            <td width="90">'. $fabric_description .'</td>
                            <td width="80">'. $yarn_qnty .'</td>
                            <td width="80">'. $fob .'</td>
                            <td width="60">'. $status_active .'</td>
                            <td width="100">'. $insert_user .'</td>
                            <td>'. $approve_status .'</td>
                        </tr>';
                            $sl++;
                        }
                        $html .= '</table></div></div></fieldset></div>';
                        ?>
                    </table>
                </div>
            </div>
        </fieldset>
    </div>
    <?
    foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
		//if( @filemtime($filename) < (time()-$seconds_old) )
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	ob_end_clean();
	//echo "$total_data****$filename";
	$bank_ids = implode(",",$bank_ids);

    ?>
    <div style="text-align:center;"  id="div_button_container">
        <? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --","","","","2,3,4" ); ?>
        <a href="<? echo $filename; ?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"></a>
        <input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>
    </div>
    <? echo $html; ?>
</div>
</body>
<script>
check_exchange_rate();
set_multiselect('cbo_item_name', '0', '0', '', '0');
</script>
<script>
    $(document).ready(function(e) {
    setFilterGrid('list_view',-1);
    });
</script>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>