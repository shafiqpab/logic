<?php

header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

if ($action=='load_drop_down_location') {
    echo create_drop_down('cbo_location_name', 138, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "load_drop_down('requires/yd_production_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/yd_production_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_machine', 'machine_td');" );
}

if ($action=='load_drop_down_floor') {
    $ex_data=explode('_',$data);
    echo create_drop_down( "cbo_floor_name", 138, "select id,floor_name from lib_prod_floor where company_id=$ex_data[0] and location_id=$ex_data[1] and status_active =1 and is_deleted=0 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/yd_production_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value+'_'+this.value, 'load_drop_down_machine', 'machine_td' );" );
}

if ($action=='load_drop_down_machine') {
    $data= explode('_', $data);

    if($data[1]==0 || $data[2]==0) {
        echo create_drop_down('cbo_machine_id', 138, $blank_array, '', 1, '-- Select Machine --', $selected, '');
    } else {
        if($db_type==2) {
            echo create_drop_down( "cbo_machine_id", 138, "select id,machine_no || '-' || brand as machine_name from lib_machine_name where company_id='$data[0]' and location_id='$data[1]' and floor_id='$data[2]' and status_active=1 and is_deleted=0 and is_locked=0 order by seq_no","id,machine_name", 1,"-- Select Machine --", $selected, "","" );
        } else if($db_type==0) {
            echo create_drop_down( "cbo_machine_id", 138, "select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where company_id='$data[0]' and location_id='$data[1]' and floor_id='$data[2]' and status_active=1 and is_deleted=0 and is_locked=0 order by  seq_no","id,machine_name", 1, "-- Select Machine --", $selected, "","" );
        }
    }   
}

if ($action == 'batch_no_popup') {
    echo load_html_head_contents('Job Popup Info', '../../../', 1, 0, $unicode, '', '');
    ?>
    <script>
        function js_set_value(id) 
        {
            document.getElementById('hdnBatchMstId').value = id;
            parent.batchPopup.hide();
        }
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
        <form name="batchColorForm" id="batchColorForm" autocomplete="off">
            <table width="580" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                <thead> 
                    <tr>
                        <th colspan="8"><?php echo create_drop_down('cbo_search_type', 140, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
                    </tr>
                    <tr>
                        <th width="140" class="must_entry_caption">Company Name</th>
                        <th width="140">Batch Serial No</th>
                        <th width="140">Job No</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 80px" /></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                            <?php
                            echo create_drop_down('cbo_company_id', 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", 'id,company_name', 1, '-- Select Company --', $data, '', 1); ?>
                        </td>
                        <td>
                            <input class="text_boxes" type="text" name="txt_batch_no" id="txt_batch_no" style="width: 140px;">
                        </td>
                        <td>
                            <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" style="width: 140px;">
                        </td>
                        <td align="center">
                            <input type="hidden" id="hdnBatchMstId">
                            <input type="button" name="showBtn" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_search_type').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_batch_no').value+'_'+document.getElementById('txt_job_no').value, 'create_batch_search_list_view', 'search_div', 'yd_production_controller', 'setFilterGrid(\'list_view\',-1)')" style="width: 80px;" />
                        </td>
                    </tr>
                </tbody>
            </table>
            </form>
            <div id="search_div" style="margin-top: 10px;"></div>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
<?php
    exit();
}

if ($action == 'create_batch_search_list_view') {
    // echo $data;die;
    $data=explode('_',$data);
    $search_type = $data[0];
    $condition = '';
    $color_arr = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');
    $party_arr = return_library_array('select id, other_party_name from lib_other_party where status_active=1 and is_deleted=0', 'id', 'other_party_name');
    // echo $data[0];die;

    // echo '<pre>';
    // print_r($data);
    // echo '</pre>';

    if ($data[1]) {
        $condition.=" and a.company_id=$data[1]";
    } else {
        echo "<h3 style='margin-top: 10px;'>Please Select Company First.</h3>"; die;
    }

    // $list_arr = array(3 => $color_arr);

    if ($data[0]==0 || $data[0]==4) { // no searching type or contents
        if ($data[2]!="") $condition.=" and a.yd_batch_id like '%$data[2]%'";
        if ($data[3]!="") $condition.=" and b.yd_job like '%$data[3]%'";
    } else if($data[0]==1) { // exact
        if ($data[2]!="") $condition.=" and a.yd_batch_id = '$data[2]'";
        if ($data[3]!="") $condition.=" and b.yd_job ='$data[3]'";
    } else if($data[0]==2) { // Starts with
        if ($data[2]!="") $condition.=" and a.yd_batch_id like '$data[2]%'";
        if ($data[3]!="") $condition.=" and b.yd_job like '$data[3]%'";
    } else if($data[0]==3) { // Ends with
        if ($data[2]!="") $condition.=" and a.yd_batch_id like '%$data[2]'";
        if ($data[3]!="") $condition.=" and b.yd_job like '%$data[3]'";
    }

    $sql = "select a.id, a.yd_batch_id, a.batch_number, b.yd_job
            from yd_batch_mst a, yd_ord_mst b
            where a.is_deleted=0 and a.status_active=1 $condition and a.yd_job_id=b.id";

    // echo $sql;

    echo create_list_view('list_view', 'Batch Serial No, Batch Number,YD Job', '140,140', '500', '300', 0, $sql, 'js_set_value', 'id', '', 1, '0,0,0', '', 'yd_batch_id,batch_number,yd_job', '','', '0,0,0,0');

    exit();
}

if ($action == 'populate_mst_data_from_search_popup') {
    $data = explode('**', $data);
    $sql;
    $reqType = $data[0];
    $mstId = $data[1];
    $color_arr = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');

    // $dtlsIdArr = explode('_', $mstId);

    $sql = "select batch_number, yd_batch_id, company_id, location_id, extention_no, batch_color_id, order_id, booking_without_order, booking_type
		from yd_batch_mst where id=$mstId and status_active=1";

    //echo $sql;

    $result = sql_select($sql);

    $company_id = $result[0][csf('company_id')];

    echo "document.getElementById('txtBatchNo').value = '".$result[0][csf('batch_number')]."';\n";
    echo "document.getElementById('cbo_company_id').value = '".$result[0][csf('company_id')]."';\n";
    echo "load_drop_down('requires/yd_production_controller', $company_id, 'load_drop_down_location', 'location_td');";
    echo "document.getElementById('txtBatchId').value = '".$result[0][csf('yd_batch_id')]."';\n";
    echo "document.getElementById('txtExtNo').value = '".$result[0][csf('extention_no')]."';\n";
    echo "document.getElementById('txtYDColor').value = '".$color_arr[$result[0][csf('batch_color_id')]]."';\n";
    echo "document.getElementById('hdnBatchMstId').value = '".$mstId."';\n";
    echo "document.getElementById('hdnOrderId').value = '".$result[0][csf('order_id')]."';\n";
    echo "document.getElementById('hdnBookingWithoutOrder').value = '".$result[0][csf('booking_without_order')]."';\n";
    echo "document.getElementById('hdnBookingType').value = '".$result[0][csf('booking_type')]."';\n";
    
    exit();
}

if($action == 'populate_dtls_data_from_search_popup') {
    $data = explode('**', $data);
    $reqType = $data[0];
    $sl = 1;
    $dtlsIds;
    $dtlsIdArr;
    $dtlsIdStr;
    $sql;
    $batchMstId = $data[1];

    $count_arr = return_library_array("select id,construction from lib_yarn_count_determina_mst where is_deleted=0 and status_active=1", 'id', 'construction');
    $comp_arr = return_library_array("select id,composition_name from lib_composition_array where is_deleted=0 and status_active=1", 'id', 'composition_name');

    $sql = "select a.id, a.order_id, a.bobbin_type, a.winding_pckg_qty, a.quantity, b.order_no, b.style_ref, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id
            from yd_batch_dtls a, yd_ord_dtls b
            where a.mst_id=$batchMstId and a.status_active=1 and a.yd_job_dtls_id=b.id and a.is_deleted=0";

    // echo $sql;die;

    $result = sql_select($sql);

    $arr=array(1=>$count_arr, 2=>$count_arr, 3=>$comp_arr);

    echo create_list_view('list_view', 'Lot,Count,Yarn Type,Yarn Composition,Bobbin Type,Winding Package Qty,Batch QTY', '50,80,80,100,80,60,50', '580', '220', 0, $sql, 'put_data_into_dtls', 'id', '', 1, '0,count_id,yarn_type_id,yarn_composition_id,0,0,0', $arr, 'lot,count_id,yarn_type_id,yarn_composition_id,bobbin_type,winding_pckg_qty,quantity', '','', '0,0,0,0,0,0,0');

    // create_list_view($table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr, $show_sl, $field_printed_from_array_arr, $data_array_name_arr, $qry_field_list_array, $controller_file_path, $filter_grid_fnc, $fld_type_arr, $summary_flds, $check_box_all, $new_conn)

    // echo create_list_view('list_view', 'Sales Order No,Lot,Count,Composition,Order Qty', '80,50,80,150,100', '520', '700', 0, $sql, 'put_data_into_dtls', "dtls_id", "", 1, '0,0,count_id,yarn_composition_id,0', $arr, 'sales_order_no,lot,count_id,yarn_composition_id,order_quantity', '', '', '0,0,0,0,1');

    exit();
}

if($action == 'populate_mst_data_from_batchlist') {
	$data = explode('**', $data);
    $reqType = $data[0];
    $batchDtlsId = $data[1];

    $sql = "select a.mst_id, a.style_ref, b.sales_order_no, a.yd_color_id, a.sales_order_id, a.product_id
            from yd_ord_dtls a, yd_batch_dtls b
            where b.id=$batchDtlsId and a.mst_id=b.yd_job_id and a.status_active=1 and b.status_active=1";

    //echo $sql;

	$result = sql_select($sql);

	echo "document.getElementById('txtSalesOrderNo').value = '".$result[0][csf('sales_order_no')]."';\n";
    echo "document.getElementById('txtStyle').value = '".$result[0][csf('style_ref')]."';\n";
    echo "document.getElementById('hdnYdOrdId').value = '".$result[0][csf('mst_id')]."';\n";
    echo "document.getElementById('hdnSalesOrderId').value = '".$result[0][csf('sales_order_id')]."';\n";
    echo "document.getElementById('hdnProductId').value = '".$result[0][csf('product_id')]."';\n";
}

if($action == 'save_update_delete') {
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $operation = str_replace("'", '', $operation);

    $tempDtls=0;
    $tmpQty=0;

    if ($operation==0) {
        // save here
        $con = connect();
        $flag = 1;
        $entry_form = 414;

        $con = connect();
        $hdnProductionMstId = str_replace("'", '', $hdnProductionMstId);
        $mstId = $hdnProductionMstId != '' ? $hdnProductionMstId : return_next_id('id', 'yd_production_mst', 1);
        $dtlsId = return_next_id('id', 'yd_production_dtls', 1);
        //$dtlsId = return_next_id('id', 'yd_delivery_dtls', 1);

        $new_prod_id = explode('*', return_mrr_number( str_replace("'", '', $cbo_company_id), '', 'YDPR', date('Y',time()), 5, "select prod_no_prefix,prod_no_prefix_num from yd_production_mst where entry_form=$entry_form and company_id=$cbo_company_id order by id desc", 'prod_no_prefix', 'prod_no_prefix_num' ));
        
        if($db_type==0) {
            mysql_query("BEGIN");
        }

        if($hdnProductionMstId == '') {
            $field_array_mst = 'id, entry_form, yd_prod_no, prod_no_prefix, prod_no_prefix_num, booking_without_order, booking_type, batch_no, inserted_by, insert_date';
            $data_array_mst = "(".$mstId.",".$entry_form.",'".$new_prod_id[0]."', '".$new_prod_id[1]."', ".$new_prod_id[2].", ".$hdnBookingWithoutOrder.", ".$hdnBookingType.", ".$txtBatchId.", ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";

            // echo "10**insert into yd_production_mst(".$field_array_mst.") values ".$data_array_mst; die;
            $rID = sql_insert('yd_production_mst', $field_array_mst, $data_array_mst, 0);
            $flag = ($flag && $rID);   // return true if $flag is true
        }

        $field_array_dtls = 'id, entry_form, mst_id, batch_no, job_id, job_dtls_id, order_id, quantity, load_unload_id, company_id, service_source_id, service_company_id, process_id, start_date, start_hours, start_minutes, floor_id, machine_id, machine_group_id, remarks, loading_date, loading_hours, loading_minutes, party_id, location_id, sales_order_id, sales_order_no, product_id, inserted_by, insert_date';
        $data_array_dtls = "(".$dtlsId.",".$entry_form.",".$mstId.",".$txtBatchId.", ".$hdnYdOrdId.",".$tempDtls.",".$hdnYdOrdId.",".$tmpQty.",".$cbo_load_unload.", ".$cbo_company_id.", ".$cbo_service_source.", ".$cbo_service_company.", ".$cbo_process.", ".$txtProcessStartDate.", ".$txtProcessStartHour.", ".$txtProcessStartMinute.", ".$cbo_floor_name.", ".$cbo_machine_id.", ".$machine_group_id.", ".$txtRemarks.", ".$txtLoadingDate.", ".$txtLoadingHour.", ".$txtLoadingMinute.", ".$cbo_party.", ".$cbo_location_name.", ".$hdnSalesOrderId.", ".$txtSalesOrderNo.", ".$hdnProductId.", ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

        // echo "10**insert into yd_production_dtls(".$field_array_dtls.") values ".$data_array_dtls;die;
        $rID2 = sql_insert('yd_production_dtls', $field_array_dtls, $data_array_dtls, 0);

        $flag = ($flag && $rID2);   // return true if $flag is true and dtls table insert is successful

        if($db_type==0) {
            if($flag) {
                mysql_query("COMMIT");              
                echo '0**'.$new_prod_id[0].'**'.$mstId;
            } else {
                mysql_query("ROLLBACK");
                echo '10**'.$hdnYdOrdId;
            }
        }
        else if($db_type==2) {
            if($flag) {
                oci_commit($con);
                echo '0**'.$new_prod_id[0].'**'.$mstId;
            } else {
                oci_rollback($con);
                echo '10**'.$hdnYdOrdId;
            }
        }

        disconnect($con);
        die;
    }

    else if($operation==1) {
        // update here
        $flag = 1;
        $id_arr = array();
        $con = connect();
        $hdnYdOrdId = str_replace("'", '', $hdnYdOrdId);
        $hdnUpdateId = str_replace("'", '', $hdnUpdateId);
        $hdnProductionId = str_replace("'", '', $hdnProductionId);
        $hdnProductionMstId = str_replace("'", '', $hdnProductionMstId);

        if($db_type==0) mysql_query("BEGIN");
        // $field_array_mst= 'location_id*batch_color_id*batch_against*batch_color_range*batch_number*batch_weight*extention_no*batch_date* process_id*duration_req*machine_id*remarks*updated_by*update_date';

        // $data_array_mst=''.$cbo_location_name.'*'.$hdnColorId.'*'.$cbo_batch_against.'*'.$txtColorRange.'*'.$txtBatchNo.'*'.$txtBatchWeight.'*'.$txtExtnNo.'*'.$txtBatchDate.'*'.$cbo_process.'*'.$txtDurationReq.'*'.$cbo_machine.'*'.$txtRemarks.'*'.$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

        $field_array_dtls = 'quantity*load_unload_id*service_source_id*service_company_id*process_id*start_date*start_hours*start_minutes*floor_id*machine_id*machine_group_id*remarks*loading_date*loading_hours*loading_minutes*party_id*location_id*updated_by*update_date';
        $data_array_dtls = ''.$tmpQty.'*'.$cbo_load_unload.'*'.$cbo_service_source.'*'.$cbo_service_company.'*'.$cbo_process.'*'.$txtProcessStartDate.'*'.$txtProcessStartHour.'*'.$txtProcessStartMinute.'*'.$cbo_floor_name.'*'.$cbo_machine_id.'*'.$machine_group_id.'*'.$txtRemarks.'*'.$txtLoadingDate.'*'.$txtLoadingHour.'*'.$txtLoadingMinute.'*'.$cbo_party.'*'.$cbo_location_name.'*'.$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

        $rID = sql_update('yd_production_dtls', $field_array_dtls, $data_array_dtls, 'id', $hdnUpdateId, 0);

        $flag = ($flag && $rID);    // return true if $flag is true and dtls table update is successful

        if($db_type==0) {
            if($flag) {
                mysql_query('COMMIT');
                echo '1**'.$hdnProductionId.'**'.$hdnProductionMstId;
            } else {
                mysql_query('ROLLBACK');
                echo '10**'.$hdnProductionId.'**'.$hdnProductionMstId;
            }
        }
        else if($db_type==2) {
            if($flag) {
                oci_commit($con);
                echo '1**'.$hdnProductionId.'**'.$hdnProductionMstId;
            } else {
                oci_rollback($con);
                echo '10**'.$hdnProductionId.'**'.$hdnProductionMstId;
            }
        }

        disconnect($con);
        die;
    }

    else if($operation==2) {
        // delete here
        $flag = 1;
        $id_arr = array();
        $con = connect();
        $hdnUpdateId = str_replace("'", '', $hdnUpdateId);
        $hdnProductionId = str_replace("'", '', $hdnProductionId);
        $hdnProductionMstId = str_replace("'", '', $hdnProductionMstId);

        if($db_type==0) mysql_query("BEGIN");

        $field_array_dtls = 'status_active*is_deleted*updated_by*update_date';
        $data_array_dtls = '0*1*'.$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

        $rID = sql_update('yd_production_dtls', $field_array_dtls, $data_array_dtls, 'id', $hdnUpdateId, 0);

        $flag = ($flag && $rID);    // return true if $flag is true and dtls table update is successful

        if($db_type==0) {
            if($flag) {
                mysql_query('COMMIT');
                echo '2**'.$hdnProductionId.'**'.$hdnProductionMstId;
            } else {
                mysql_query('ROLLBACK');
                echo '10**'.$hdnProductionId.'**'.$hdnProductionMstId;
            }
        }
        else if($db_type==2) {
            if($flag) {
                oci_commit($con);
                echo '2**'.$hdnProductionId.'**'.$hdnProductionMstId;
            } else {
                oci_rollback($con);
                echo '10**'.$hdnProductionId.'**'.$hdnProductionMstId;
            }
        }

        disconnect($con);
        die;
    }
}

if($action == 'create_production_list') {
    $data = explode('**', $data);
    $reqType = $data[0];
    $productionMstId = $data[1];

    $sql = "select a.id, b.id as dtls_id, a.yd_prod_no, b.process_id, b.loading_hours, b.loading_minutes, b.quantity, b.load_unload_id
            from yd_production_mst a, yd_production_dtls b
            where a.id=$productionMstId and a.id=b.mst_id and b.entry_form=414 and b.status_active=1";

    // echo $sql;

    $arr = array(1 => $dyeing_sub_process, 2 => $loading_unloading);

    echo create_list_view('list_view', 'Production No,Process,Load/Unload,Loading Hour,Loading Minute', '140,100,80,100', '800', '400', 0, $sql, 'populateProductionData', 'dtls_id', '', 1, '0,process_id,load_unload_id,0,0', $arr, 'yd_prod_no,process_id,load_unload_id,loading_hours,loading_minutes', '', '', '0,0,0,0,0');

    exit();
}

if ($action == 'populate_mst_data_from_productionlist') {
    $data = explode('**', $data);
    $reqType = $data[0];
    $productionDtlsId = $data[1];

    $sql = "select load_unload_id, company_id, service_source_id, service_company_id, process_id, start_date, start_hours,
            start_minutes, floor_id, machine_id, machine_group_id, remarks, loading_date, loading_hours, loading_minutes, party_id, 
            location_id
            from yd_production_dtls
            where id=$productionDtlsId";

    // echo $sql;

    $result = sql_select($sql);

    echo "document.getElementById('cbo_load_unload').value = '".$result[0][csf('load_unload_id')]."';\n";
    echo "document.getElementById('cbo_company_id').value = '".$result[0][csf('company_id')]."';\n";
    echo "document.getElementById('cbo_service_source').value = '".$result[0][csf('service_source_id')]."';\n";
    echo "document.getElementById('cbo_service_company').value = '".$result[0][csf('service_company_id')]."';\n";
    echo "document.getElementById('cbo_process').value = '".$result[0][csf('process_id')]."';\n";
    echo "document.getElementById('txtProcessStartDate').value = '".change_date_format($result[0][csf('start_date')], "dd-mm-yyyy", "-")."';\n";
    echo "document.getElementById('txtProcessStartHour').value = '".$result[0][csf('start_hours')]."';\n";
    echo "document.getElementById('txtProcessStartMinute').value = '".$result[0][csf('start_minutes')]."';\n";
    echo "document.getElementById('cbo_floor_name').value = '".$result[0][csf('floor_id')]."';\n";
    echo "document.getElementById('cbo_machine_id').value = '".$result[0][csf('machine_id')]."';\n";
    echo "document.getElementById('machine_group_id').value = '".$result[0][csf('machine_group_id')]."';\n";
    echo "document.getElementById('txtRemarks').value = '".$result[0][csf('remarks')]."';\n";
    echo "document.getElementById('txtLoadingDate').value = '".change_date_format($result[0][csf('loading_date')], "dd-mm-yyyy", "-")."';\n";
    echo "document.getElementById('txtLoadingHour').value = '".$result[0][csf('loading_hours')]."';\n";
    echo "document.getElementById('txtLoadingMinute').value = '".$result[0][csf('loading_minutes')]."';\n";
    echo "document.getElementById('cbo_party').value = '".$result[0][csf('party_id')]."';\n";
    echo "document.getElementById('cbo_location_name').value = '".$result[0][csf('location_id')]."';\n";
    // change_date_format($result[0][csf('batch_date')], "dd-mm-yyyy", "-")
}

?>