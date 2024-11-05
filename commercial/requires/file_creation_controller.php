<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
$company_cond1=set_user_lavel_filtering(' and company_id','company_id');


if($action=="check_conversion_rate") //Conversion Exchange Rate
{ 
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date, $data[2] );
	echo "1"."_".$currency_rate;
	exit();	
}

if ($action=="load_drop_down_buyer_search")
{
    if($data != 0){
        echo create_drop_down( "cbo_buyer_name", 142, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "" );
        exit();
    }
    else{
        echo create_drop_down( "cbo_buyer_name", 142, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "" );
        exit();
    }
}

if ($action=="save_update_delete")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
	
	//echo $txt_year; die;
	
    if ($operation==0)  // Insert Here
    {
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if(str_replace("'", "", $txt_system_id) != "")
		{
			$field_array_update = "file_date*file_closing_status*file_value*currency_id*file_qty*conversion_factor*lien_bank*ship_date*ready_to_approve*remarks*update_user*update_date*status_active*created_by";
			$data_array_update = $txt_file_date . "*" . $cbo_file_closing_status . "*" . $txt_file_value . "*" . $cbo_currency_name . "*" . $txt_file_qty . "*" . $txt_conversion_factor . "*" . $cbo_lien_bank . "*" . $txt_ship_date . "*" . $cbo_ready_to_approved . "*" . $txt_remarks . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $cbo_file_status. "*" . $txt_created_user . "";
			$rID=sql_update("lib_file_creation",$field_array_update,$data_array_update,"id", str_replace("'", "", $txt_system_id),1);
		}
		else 
		{
			$user_arr = return_library_array("select id, user_name from user_passwd", "id", "user_name");
			$buyer_short_name = return_field_value("short_name", "lib_buyer", "id=$cbo_buyer_name", "short_name");
			$company_short_name = return_field_value("company_short_name", "lib_company", "id=$cbo_company_name", "company_short_name");
			$file_type_short = array(1 => "YP", 2 => "PO", 3 => "CO");	
			
			$sql_count_file = return_field_value("count(id) as counter", "lib_file_creation", "company_id = $cbo_company_name and file_year=$txt_year", "counter");
			//$sql_count_file = return_field_value("count(id) as counter", "lib_file_creation", "company_id = $cbo_company_name and buyer_id=$cbo_buyer_name", "counter");
			//echo  substr(str_replace("'", "", $txt_year), 2, 4); die;
            
			$file_no = $company_short_name . "/" . $buyer_short_name . "/" . substr(str_replace("'", "", $txt_year), 2, 4) . "/" . $file_type_short[str_replace("'", "", $cbo_file_type)] . "/" . (sprintf('%04d', $sql_count_file + 1));

            $id = return_next_id("id", "lib_file_creation", 1);
			$field_array = "id, file_no, company_id, buyer_id, file_year, file_date, file_closing_status, file_value, currency_id, file_qty, conversion_factor, lien_bank, ship_date, ready_to_approve, remarks, file_type, insert_user, insert_date, status_active, is_deleted, style_ref_no, gmts_item_id, fabric_description, yarn_qnty, fob, created_by";
			$data_array = "(" . $id . ",'" . $file_no . "'," . $cbo_company_name . "," . $cbo_buyer_name . "," . $txt_year . "," . $txt_file_date . "," . $cbo_file_closing_status . "," . $txt_file_value . "," . $cbo_currency_name . "," . $txt_file_qty . "," . $txt_conversion_factor . "," . $cbo_lien_bank . "," . $txt_ship_date . "," . $cbo_ready_to_approved . "," . $txt_remarks . "," . $cbo_file_type . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_file_status . ",0," . $txt_style_ref . "," . $cbo_item_name . "," . $txt_fab_description . "," . $txt_yarn_qnty . "," . $txt_fob . "," . $txt_created_user . ")";
			
			//echo "10**INSERT into lib_file_creation $field_array values $data_array"; die;
			$rID = sql_insert("lib_file_creation", $field_array, $data_array, 1);
		}

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$file_no."**".$id."**".$user_arr[$user_id];
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
				echo "0**".$file_no."**".$id."**".$user_arr[$user_id];
			}
			else{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
    }
    elseif ($operation == 1)   // Update Here
    {
            $con = connect();
            if ($db_type == 0) {
                mysql_query("BEGIN");
            }
            $field_array_update = "file_date*file_closing_status*file_value*currency_id*file_qty*conversion_factor*lien_bank*ship_date*ready_to_approve*remarks*update_user*update_date*status_active*style_ref_no*gmts_item_id*fabric_description*yarn_qnty*fob*created_by";
            $data_array_update = "" . $txt_file_date . "*" . $cbo_file_closing_status . "*" . $txt_file_value . "*" . $cbo_currency_name . "*" . $txt_file_qty . "*" . $txt_conversion_factor . "*" . $cbo_lien_bank . "*" . $txt_ship_date . "*" . $cbo_ready_to_approved . "*" . $txt_remarks . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $cbo_file_status . "*" . $txt_style_ref . "*" . $cbo_item_name . "*" . $txt_fab_description . "*" . $txt_yarn_qnty . "*" . $txt_fob . "*" . $txt_created_user . "";
			
            $update_id = str_replace("'", "", $txt_system_id);
            $rID = sql_update("lib_file_creation", $field_array_update, $data_array_update, "id", "" .$update_id."");
            if ($db_type == 0) {
                if ($rID) {
                    mysql_query("COMMIT");
                    echo "1**".$rID;
                } else {
                    mysql_query("ROLLBACK");
                    echo "10**".$rID;
                }
            }
            if ($db_type == 2 || $db_type == 1) {
                if ($rID) {
                    oci_commit($con);
                    echo "1**".$rID;
                } else {
                    oci_rollback($con);
                    echo "10**".$rID;
                }
            }
            disconnect($con);
            die;
    }
    elseif ($operation == 2)   // Delete Here
    {
        $update_id = str_replace("'", "", $txt_system_id);
        $field_array = "update_user*update_date*status_active*is_deleted";
        $data_array = "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
        $rID = sql_update("lib_file_creation", $field_array, $data_array, "id", "" . $update_id . "",1);
        if ($db_type == 0) {
            if ($rID) {
                mysql_query("COMMIT");
                echo "2**1";
            } else {
                mysql_query("ROLLBACK");
                echo "10**0";
            }
        }
        if ($db_type == 2 || $db_type == 1) {
            if ($rID) {
                oci_commit($con);
                echo "2**1";
            } else {
                oci_rollback($con);
                echo "10**0";
            }
        }
        disconnect($con);
        die;
    }
}

if($action == "load_php_data_to_form"){
    $sql_data = sql_select("SELECT id, file_no, company_id, buyer_id, file_year, file_date, file_closing_status, file_value, currency_id, file_qty, conversion_factor, lien_bank, ship_date, ready_to_approve, remarks, file_type, insert_user, status_active, approve_status, style_ref_no, gmts_item_id, fabric_description, yarn_qnty, fob, created_by from lib_file_creation where id = $data and is_deleted=0 $company_cond1 order by id desc");
    $user_arr = return_library_array("select id, user_name from user_passwd", "id", "user_name");
    if(count($sql_data) > 0){
        echo "$('#txt_system_id').val(".$sql_data[0][csf('id')].");\n";
        echo "$('#file_no').val('".$sql_data[0][csf('file_no')]."');\n";
        echo "$('#cbo_company_name').val(".$sql_data[0][csf('company_id')].");\n";
        echo "load_drop_down( 'requires/file_creation_controller', ".$sql_data[0][csf('company_id')].", 'load_drop_down_buyer_search', 'buyer_td_id' );\n";
        echo "$('#cbo_buyer_name').val(".$sql_data[0][csf('buyer_id')].");\n";
        echo "$('#cbo_file_type').val(".$sql_data[0][csf('file_type')].");\n";
        echo "$('#txt_year').val(".$sql_data[0][csf('file_year')].");\n";
        echo "$('#txt_file_date').val('".change_date_format($sql_data[0][csf('file_date')])."');\n";
        echo "$('#cbo_file_status').val(".$sql_data[0][csf('status_active')].");\n";
        echo "$('#cbo_file_closing_status').val(".$sql_data[0][csf('file_closing_status')].");\n";
        echo "$('#txt_file_value').val(".$sql_data[0][csf('file_value')].");\n";
        echo "$('#cbo_currency_name').val(".$sql_data[0][csf('currency_id')].");\n";
        echo "$('#txt_file_qty').val(".$sql_data[0][csf('file_qty')].");\n";
        echo "$('#txt_conversion_factor').val(".$sql_data[0][csf('conversion_factor')].");\n";
        echo "$('#cbo_lien_bank').val(".$sql_data[0][csf('lien_bank')].");\n";
        echo "$('#txt_ship_date').val('".change_date_format($sql_data[0][csf('ship_date')])."');\n";
        echo "$('#cbo_ready_to_approved').val(".$sql_data[0][csf('ready_to_approve')].");\n";
        echo "$('#txt_remarks').val('".$sql_data[0][csf('remarks')]."');\n";
        echo "$('#txt_created_user').val('".$sql_data[0][csf('created_by')]."');\n";
		
		echo "$('#txt_style_ref').val('".$sql_data[0][csf('style_ref_no')]."');\n";
        echo "$('#cbo_item_name').val('".$sql_data[0][csf('gmts_item_id')]."');\n";
        echo "$('#txt_fab_description').val('".$sql_data[0][csf('fabric_description')]."');\n";
        echo "$('#txt_yarn_qnty').val('".$sql_data[0][csf('yarn_qnty')]."');\n";
        echo "$('#txt_fob').val('".$sql_data[0][csf('fob')]."');\n";
		
		echo "set_multiselect('cbo_item_name','0','1','" . $sql_data[0][csf('gmts_item_id')] . "','0');\n";
		//, style_ref_no, gmts_item_id, fabric_description, yarn_qnty, fob
		//txt_style_ref*cbo_item_name*txt_fab_description*txt_yarn_qnty*txt_fob
        echo "disable_fields('cbo_company_name*cbo_buyer_name*cbo_file_type*txt_year');\n";
        echo "set_button_status(1, permission, 'fnc_file_creation',1, 1);\n";
    }
    exit();
}


if ($action == "file_creation_list_view"){

    ob_start();
    $html = '<table class="rpt_table" id="rpt_tablelist_view" rules="all" width="1280" cellspacing="0" cellpadding="0" border="0">
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
                        $html .= '</table></div>';
                        ?>
                    </table>
                </div>
            </div></fieldset></div>
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
    <div style="text-align:center;" id="div_button_container">
        <? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --","","","","2,3,4" ); ?>
        <a href="<? echo 'requires/' .$filename; ?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"></a>
        <input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>
    </div>
    <? echo $html;
    exit();
}

if ($action == "print_file_creation"){
    $data = explode('*',$data);
    $com_dtls = fnc_company_location_address($data[1], "", 2);
    $file_type = array(1 => "Yarn Procurement", 2=>"Projection Order", 3=>"Confirm Order");
    $file_status = array(1 => "Active", 2=>"Inactive");
    $file_approve = array(0 => "Not Approved", 1=>"Approved", 2=>"Partially Approved");
    $file_closing_status = array(1 => "Running", 2=>"Close");
    if ($db_type==0)
    {
        $bank_arr = return_library_array("select concat(a.bank_name,' (', a.branch_name,')') as bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name","id","bank_name");
    }
    else
    {
        $bank_arr = return_library_array("select (bank_name || ' (' || branch_name || ')' ) as bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name","id","bank_name");
    }
    $user_arr = return_library_array("select id, USER_NAME from user_passwd", "id", "user_name");
    $buyer_name=return_library_array("select id, buyer_name from lib_buyer where status_active = 1 and is_deleted = 0","id","buyer_name");
    $sql_data = sql_select("SELECT id, file_no, file_name, company_id, buyer_id, file_year, file_date, file_closing_status, file_value, file_qty, currency_id, file_qty, conversion_factor, lien_bank, ship_date, ready_to_approve, remarks, file_type, insert_user, status_active, approve_status from lib_file_creation where id = $data[0] and is_deleted=0  order by id desc");

    ?>
    <div style="width:1200px;">
        <table width="1180" cellspacing="0" border="0">
            <tr>
                <td colspan="2" rowspan="2">
                    <img src="../<? echo $com_dtls[2]; ?>" height="60" width="200" style="float:left;">
                </td>
                <td colspan="4" align="center" style="font-size:22px">
                    <strong><? echo $com_dtls[0]; ?></strong>
                </td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">
                <?
                echo $com_dtls[1];
                ?>
                </td>
            </tr>
        </table>
        <br>
        <br>
        <table  width="1180" cellspacing="0" border="0">
			<tr>
                <td width="120"  colspan='3' style="padding: 5px 2px;"><strong>System File No : <? $file= explode("/", $sql_data[0][csf('file_no')]); echo $file[0]."/".$file[1]."/".$file[2]."/". sprintf('%04d',$file[4]); //echo sprintf('%03d', $sql_count_file);?></strong></td>
				<td width="120"  colspan='3' style="padding: 5px 2px;"><strong>AKH File No: <? $file= explode("/", $sql_data[0][csf('file_name')]); echo  $file[0]."/".$file[1]."/".sprintf('%04d',$file[2]); //$sql_data[0][csf('file_name')];?></strong></td>
            </tr>
			<tr>
                <td width="120"  colspan='3' style="padding: 5px 2px;"></strong></td>
				<td width="120"  colspan='3' style="padding: 5px 2px;"><strong></strong></td>
            </tr>

			<tr>
                <td width="120" style="padding: 5px 2px;"><strong>Buyer</strong></td>
                <td width="160"  style="padding: 5px 2px;"><strong>: </strong><? echo $buyer_name[$sql_data[0][csf('buyer_id')]]; ?></td>
                <td width="120"  style="padding: 5px 2px;"><strong>File Type</strong></td>
                <td width="140"  style="padding: 5px 2px;"><strong>: </strong><? echo $file_type[$sql_data[0][csf('file_type')]]; ?></td>
                <td width="130"  style="padding: 5px 2px;"><strong>File Year</strong></td>
                <td width="120"  style="padding: 5px 2px;"><strong>: </strong><? echo $sql_data[0][csf('file_year')]; ?></td>
                <td width="90"  style="padding: 5px 2px;"><strong>Lien Bank</strong></td>
                <td  style="padding: 5px 2px;"><strong>: </strong><? echo $bank_arr[$sql_data[0][csf('lien_bank')]]; ?></td>
            </tr>
			<tr>
                <td width="120" style="padding: 5px 2px;"><strong>Style Name </strong></td>
                <td width="160"  style="padding: 5px 2px;"><strong></strong>:<? echo $sql_data[0][csf('style_ref_no')]; ?></td>
                <td width="120"  style="padding: 5px 2px;"><strong>Items </strong></td>
                <td width="140"  style="padding: 5px 2px;"><strong> </strong>:<? echo $sql_data[0][csf('gmts_item_id')]; ?></td>
                <td width="130"  style="padding: 5px 2px;"><strong>Yarn Qty </strong></td>
                <td width="120"  style="padding: 5px 2px;"><strong> </strong>:<? echo $sql_data[0][csf('yarn_qnty')]; ?></td>
                <td width="90"  style="padding: 5px 2px;"><strong>FOB </strong></td>
                <td  style="padding: 5px 2px;"><strong> </strong>:<? echo $sql_data[0][csf('fob')]; ?></td>
            </tr>
            <tr>
                <td  style="padding: 5px 2px;"><strong>File Date</strong></td>
                <td  style="padding: 5px 2px;"><strong>: </strong><? echo change_date_format($sql_data[0][csf('file_date')]); ?></td>
                <td  style="padding: 5px 2px;"><strong>File Status</strong></td>
                <td  style="padding: 5px 2px;"><strong>: </strong><? echo $file_status[$sql_data[0][csf('status_active')]]; ?></td>
                <td  style="padding: 5px 2px;"><strong>File Value</strong></td>
                <td  style="padding: 5px 2px;"><strong>: </strong><? echo $sql_data[0][csf('file_value')]; ?></td>
                <td  style="padding: 5px 2px;"><strong>Approved</strong></td>
                <td  style="padding: 5px 2px;"><strong>: </strong><? echo $file_approve[$sql_data[0][csf('approve_status')]]; ?></td>
            </tr>
            <tr>
                <td  style="padding: 5px 2px;"><strong>Closing Status</strong></td>
                <td  style="padding: 5px 2px;"><strong>: </strong><? echo $file_closing_status[$sql_data[0][csf('file_closing_status')]]; ?></td>
                <td  style="padding: 5px 2px;"><strong>Currency</strong></td>
                <td  style="padding: 5px 2px;"><strong>: </strong><? echo $currency[$sql_data[0][csf('currency_id')]]; ?></td>
                <td  style="padding: 5px 2px;"><strong>Approx Ship Date</strong></td>
                <td  style="padding: 5px 2px;"><strong>: </strong><? echo change_date_format($sql_data[0][csf('ship_date')]); ?></td>
				<td  style="padding: 5px 2px;"><strong>Order Qty :</strong></td>
				<td  style="padding: 5px 2px;"><strong>: </strong><? echo $sql_data[0][csf('file_qty')]; ?></td>
            </tr>
			<tr>
                <td  style="padding: 5px 2px;"><strong>Create For</strong></td>
                <td  style="padding: 5px 2px;"><strong>: </strong><? echo $user_arr[$sql_data[0][csf('insert_user')]];?></td>
            </tr>
            <tr>
                <td  style="padding: 5px 2px;"><strong>Remarks</strong></td>
                <td  style="padding: 5px 2px;" colspan="7"><strong>: </strong><? echo $sql_data[0][csf('remarks')]; ?></td>
            </tr>
			<tr>
				 <td width="120"  style="padding: 5px 2px;"><strong>Fabric Desc :</strong></td>
                <td width="140"  style="padding: 5px 2px;"><strong> </strong><? echo $file_type[$sql_data[0][csf('fabric_description')]]; ?></td>
			</tr>
        </table>
        <br>
        <br>
        <div style="padding-top: 70px;">
            <table width="1180" cellpadding="0" cellspacing="0" align="left">
                <td align="center" width="190">
                    <p style="min-height:40px;"><?//=$user_arr[$sql_data[0][csf('insert_user')]]?></p>
                    <strong></strong><br>
                    <strong style="text-decoration:overline">Prepared By</strong><br></td>
                </td>
				<td align="center" width="490">
                    
                    <strong></strong><br>
                    <strong style="text-decoration:overline">Checked By</strong><br></td>
                </td>
                <td align="center">
                    <p style="min-height:40px;"></p>
                    <strong></strong><br>
                    <strong style="text-decoration:overline">Authorized By</strong><br></td>
                </td>
            </table>

        </div>
    </div>
<?
    exit();
}

if ($action=="style_ref_popup")
{
	echo load_html_head_contents("Style Ref Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name"  );
	?> 
	<script>
		
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
		
		function js_set_value(id,buyer,style_ref,product_dep_id)
		{
			$('#hidden_style_id').val(id);
			$('#hidden_buyer_id').val(buyer);
			$('#hidden_style_ref').val(style_ref);
			$('#hidden_product_dep_id').val(product_dep_id);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:660px;margin-left:10px">
			<?
				$composition_arr=array();
				$compositionData=sql_select("select mst_id, copmposition_id, percent from lib_yarn_count_determina_dtls where status_active=1 and is_deleted=0");
				foreach( $compositionData as $row )
				{
					$composition_arr[$row[csf('mst_id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
				}
            ?>
            <input type="hidden" name="hidden_style_id" id="hidden_style_id" class="text_boxes" value="">
            <input type="hidden" name="hidden_buyer_id" id="hidden_buyer_id" class="text_boxes" value="">  
            <input type="hidden" name="hidden_style_ref" id="hidden_style_ref" class="text_boxes" value="">  
            <input type="hidden" name="hidden_internal_ref" id="hidden_internal_ref" class="text_boxes" value="">  
            <input type="hidden" name="hidden_product_dep_id" id="hidden_product_dep_id" class="text_boxes" value=""> 
            <div style="margin-left:10px; margin-top:10px">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="630">
                    <thead>
                        <th width="50">SL</th>
                        <th>Style Ref</th>
                        <th width="150">Buyer</th>
                    </thead>
                </table>
                <div style="width:650px; max-height:300px; overflow-y:scroll" id="list_container" align="left"> 
                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="630" id="tbl_list_search">  
                        <?
                        $i=1; if($garments_nature=="") $garments_nature=0;
						
						$data_array=sql_select("select id, PRODUCT_DEPARTMENT_ID,style_ref_name,buyer_id from lib_style_ref where status_active=1 and is_deleted=0 and BUYER_ID=$cbo_buyer");
                        foreach($data_array as $row)
                        {  
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            
							
                         ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('buyer_id')]; ?>','<? echo $row[csf('style_ref_name')]; ?>','<? echo $row[PRODUCT_DEPARTMENT_ID]; ?>')" style="cursor:pointer" >
                                <td align="center" width="50"><? echo $i; ?></td>
                                <td><? echo $row[csf('style_ref_name')]; ?></td>
                                <td width="150"><? echo $buyer_name_arr[$row[csf('buyer_id')]]; ?></td>
                            </tr>
                        <? 
                        $i++; 
                        } 
                        ?>
                    </table>
                </div> 
            </div>
		</fieldset>
	</form>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
