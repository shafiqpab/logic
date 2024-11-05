<?
include('../../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
 
if ($action=="salesorder_popup") 
{
	echo load_html_head_contents("Sales Order Info", "../../../", 1, 0, $unicode, "", "");
	?>
	<script>
		function js_set_value(id) 
		{
			document.getElementById('selected_order_id').value = id;
			//document.getElementById('selected_batch_no').value = batch_no;
			
            parent.popupWindow.hide();
		}

        window.onload = function() {
            $('#cbo_company_name').attr('disabled','disabled');
        }

	</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
        <table width="530" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="8"><?php echo create_drop_down('cbo_string_search_type', 140, $string_search_type,'', 1, '-- Searching Type --'); ?></th>
                </tr>
                <tr>
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="90">Job No</th>
                    <th width="90">WO No</th>
                    <th width="90">Batch No</th>
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 80px" /></th>
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
                        <input type="hidden" id="selected_job"><? $data=explode('_',$data); ?>
                        <?php
                        echo create_drop_down('cbo_company_name', 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", 'id,company_name', 1, '-- Select Company --', $data[0], '', 1); ?>
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" style="width: 90px;">
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_workorder_no" id="txt_workorder_no" style="width: 90px;">
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_batch_no" id="txt_batch_no" style="width: 90px;">
                    </td>
                    <td align="center">
                        <input type="hidden" id="selected_order_id">
                         <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_workorder_no').value+'_'+document.getElementById('txt_batch_no').value, 'create_sales_order_list_view', 'search_div', 'yd_hydro_extractor_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width: 80px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
                </tr>
            </tbody>
        </table>    
        </form>
    </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?php
exit();
}

if($action == "create_sales_order_list_view") {
    // echo $data;die;
    $data=explode('_',$data);
    $search_type = $data[0];
    $condition = "";
    // echo $data[0];die;
    if($data[1]) {
        $condition.=" and a.company_id=$data[1]";
    } else {
        echo "<h3 style='margin-top: 10px;'>Please Select Company First.</h3>"; die;
    }

    if($data[0]==0 || $data[0]==4) 
	{ // no searching type or contents
        if ($data[2]!="") $condition.=" and a.yd_job like '%$data[2]%'";
        if ($data[3]!="") $condition.=" and a.order_no like '%$data[3]%'";
        if ($data[4]!="") $condition.=" and c.batch_number like '%$data[4]%'";
    } 
	else if($data[0]==1) { // exact
        if ($data[2]!="") $condition.=" and a.yd_job = '$data[2]'";
        if ($data[3]!="") $condition.=" and order_no ='$data[3]'";
        if ($data[4]!="") $condition.=" and c.batch_number ='$data[4]'";
    } 
	else if($data[0]==2) { // Starts with
        if ($data[2]!="") $condition.=" and a.yd_job like '$data[2]%'";
        if ($data[3]!="") $condition.=" and order_no like '$data[3]%'";
        if ($data[4]!="") $condition.=" and c.batch_number like '$data[4]%'";
    } 
	else if($data[0]==3) { // Ends with
        if ($data[2]!="") $condition.=" and a.yd_job like '%$data[2]'";
        if ($data[3]!="") $condition.=" and order_no like '%$data[3]'";
        if ($data[4]!="") $condition.=" and c.batch_number like '%$data[4]'";
    }

   $sql= "SELECT a.id, a.yd_job, a.order_no, c.yd_batch_id, c.batch_number 
   from yd_ord_mst a, yd_batch_mst c
   where a.id=c.yd_job_id and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1 $condition order by id";
    
    /*echo $sql;die;*/
    echo create_list_view("list_view", "Job No,WO No,Batch No", "140,140", "500","300", 0, $sql, "js_set_value", "id,order_no,batch_number", "", 1, "0,0,0", $arr, "yd_job,order_no,batch_number", "",'','0,0,0,0');
    exit();
}


if ($action=="load_drop_down_buyer")
{
   
    echo create_drop_down( "cbo_party_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data, "");

    /*echo create_drop_down( "cbo_company_name", 117, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yd_hydro_extractor_controller', this.value, 'load_drop_down_buyer', 'party_td' );");*/
    exit();
    
} 

if($action == "create_sales_order_list") 
{
    // echo $data;die;
    $data=explode('_',$data);
	
	
	 
    $yd_id_mst = $data[0];
	$batch_no= $data[2];
	
     $sql= "select a.id,a.within_group, b.id as dtls_id,b.sales_order_no, b.order_no, b.lot, b.count_id, b.yarn_composition_id, b.order_quantity,c.quantity,e.quantity as prod_qty
    from yd_ord_mst a, yd_ord_dtls b,yd_batch_dtls c,yd_batch_mst d, yd_production_dtls e
    where a.is_deleted = 0 and a.status_active = 1 and a.id = b.mst_id and b.id = c.yd_job_dtls_id and  d.id = c.mst_id  and  d.yd_batch_id=e.batch_no and e.load_unload_id=2 and e.entry_form=414  and a.id = '$yd_id_mst' and d.batch_number = '$batch_no' group by a.id,a.within_group, b.id,b.sales_order_no, b.order_no, b.lot, b.count_id, b.yarn_composition_id, b.order_quantity,c.quantity,e.quantity  order by id";
	
    /*$sql= "select a.id,a.within_group, b.id as dtls_id,b.sales_order_no, b.order_no, b.lot, b.count_id, b.yarn_composition_id, b.order_quantity
    from yd_ord_mst a, yd_ord_dtls b,yd_production_dtls c
    where a.is_deleted = 0 and a.status_active = 1 and a.id = b.mst_id and and a.id=c.job_id and a.id = '$yd_id_mst' order by id";*/
    /*echo $sql;*/
    // $count_sql = "select id, construction from LIB_YARN_COUNT_DETERMINA_MST where is_deleted = 0 and status_active = 1";
   $count_arr = return_library_array("Select id, yarn_count from  lib_yarn_count where  status_active=1", 'id', 'yarn_count');
    $comp_arr = return_library_array("select id,composition_name from lib_composition_array where is_deleted=0 and status_active=1", 'id', 'composition_name');
    $arr=array(2=>$count_arr, 3=>$comp_arr);

    echo create_list_view('list_view', 'Job No/Sales order no,Lot,Count,Composition,Batch quantity,Production Qty', '80,50,80,80,80,80', '500', '200', 0, $sql, 'put_data_into_dtls', "dtls_id", "", 1, '0,0,count_id,yarn_composition_id,0,0', $arr, 'sales_order_no,lot,count_id,yarn_composition_id,quantity,quantity', '', '', '0,0,0,0,1,1');

    exit();
}

if($action == "populate_hydro_extractor_from_list") {
    // echo 'inside controller';
    // echo "document.getElementById('txtSalesOrder').value = 500;\n";
    /*echo $data;
    echo "select b.style_ref, a.order_no, a.company_id, a.party_id, b.count_id, b.lot, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.order_no, b.order_quantity
        from yd_ord_mst a, yd_ord_dtls b
        where a.is_deleted=0 AND a.status_active=1 AND b.id='$data'";die;*/
    $data_array = sql_select("select b.style_ref,a.booking_without_order,a.booking_type,a.within_group,a.id as job_id,b.id as jobdtlsid,b.order_id as order_id, a.order_no, a.company_id, a.party_id, b.count_id, b.lot, b.yarn_composition_id, b.yd_color_id, b.no_bag,b.sales_order_no, b.order_no,b.sales_order_id,b.product_id, b.order_quantity,c.id as batch_id, c.yd_batch_id , c.batch_number
        from yd_ord_mst a, yd_ord_dtls b, yd_batch_mst c
        where a.is_deleted=0 AND a.status_active=1 AND a.id=b.mst_id and c.yd_job_id=a.id and b.id='$data'");


   /* echo "select b.style_ref,a.within_group,a.id as job_id,b.id as jobdtlsid,b.order_id as order_id, a.order_no, a.company_id, a.party_id, b.count_id, b.lot, b.yarn_composition_id, b.yd_color_id, b.no_bag,b.sales_order_no, b.order_no, b.order_quantity
        from yd_ord_mst a, yd_ord_dtls b
        where a.is_deleted=0 AND a.status_active=1 AND a.id=b.mst_id and a.id='$data'";die;*/
    $count_arr = return_library_array("Select id, yarn_count from  lib_yarn_count where  status_active=1", 'id', 'yarn_count');
    $comp_arr = return_library_array("select id,composition_name from lib_composition_array where is_deleted=0 and status_active=1", 'id', 'composition_name');
    $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

    // "select count_id, lot, order_no, order_quantity from yd_ord_dtls where mst_id=3";
    // echo "document.getElementById('txtSalesOrder').value = 5000;\n";
    echo "document.getElementById('txtLot').value = '" . $data_array[0][csf("lot")] . "';\n";
    echo "document.getElementById('txtCount').value = '" . $count_arr[$data_array[0][csf("count_id")]] . "';\n";
    echo "document.getElementById('txtComposition').value = '" . $comp_arr[$data_array[0][csf("yarn_composition_id")]] . "';\n";
    echo "document.getElementById('txtYdColor').value = '" . $color_arr[$data_array[0][csf("yd_color_id")]] . "';\n";
    /*echo "document.getElementById('txtPkg').value = '" . $data_array[0][csf("no_bag")] . "';\n";*/
    // echo "document.getElementById('txtWghtCone').value = '" . $data_array[0][csf("no_bag")] . "';\n";
    echo "document.getElementById('txtSalesOrder').value = '" . $data_array[0][csf("sales_order_no")] . "';\n";
    echo "document.getElementById('txtStyle').value = '" . $data_array[0][csf("style_ref")] . "';\n";
    echo "document.getElementById('sales_order_serial').value = '" . $data . "';\n";
    echo "document.getElementById('job_dtls_id').value = '" . $data_array[0][csf("jobdtlsid")] . "';\n";
    echo "document.getElementById('order_id').value = '" . $data_array[0][csf("order_id")] . "';\n";
    echo "document.getElementById('job_id').value = '" . $data_array[0][csf("job_id")] . "';\n";
    echo "document.getElementById('within_group').value = '" . $data_array[0][csf("within_group")] . "';\n";
    echo "document.getElementById('booking_without_order').value = '" . $data_array[0][csf("booking_without_order")] . "';\n";
    echo "document.getElementById('booking_type').value = '" . $data_array[0][csf("booking_type")] . "';\n";
    echo "document.getElementById('sales_order_id').value = '" . $data_array[0][csf("sales_order_id")] . "';\n";
    echo "document.getElementById('product_id').value = '" . $data_array[0][csf("product_id")] . "';\n";
    echo "document.getElementById('txtBatchNo').value = '" . $data_array[0][csf("batch_number")] . "';\n";
    echo "document.getElementById('batch_id').value = '" . $data_array[0][csf("batch_id")] . "';\n";


    // txtSalesOrder
    exit();
}

if($action == "populate_saved_data_from_list") {
    
    $data_array = sql_select("select b.id,b.quantity, b.remarks,b.temperature,b.speed, a.company_id, a.party_id
        from yd_production_mst a, yd_production_dtls b
        where a.is_deleted=0 AND a.status_active=1 AND b.id='$data'");

    /*echo "select b.quantity, b.remarks, a.company_id, a.party_id
        from yd_production_mst a, yd_production_dtls b
        where a.is_deleted=0 AND a.status_active=1 AND b.id='$data'";die;*/
   $count_arr = return_library_array("Select id, yarn_count from  lib_yarn_count where  status_active=1", 'id', 'yarn_count');
    $comp_arr = return_library_array("select id,composition_name from lib_composition_array where is_deleted=0 and status_active=1", 'id', 'composition_name');

    // "select count_id, lot, order_no, order_quantity from yd_ord_dtls where mst_id=3";
    // echo "document.getElementById('txtSalesOrder').value = 5000;\n";
    echo "document.getElementById('txtPrdctnQty').value = '" . $data_array[0][csf("quantity")] . "';\n";
    echo "document.getElementById('txtRemarks').value = '" . $data_array[0][csf("remarks")] . "';\n";
    /*echo "document.getElementById('update_id').value = '" . $data_array[0][csf("id")] . "';\n";*/
    echo "document.getElementById('txtTemperature').value = '" . $data_array[0][csf("temperature")] . "';\n";
    echo "document.getElementById('txtSpeed').value = '" . $data_array[0][csf("speed")] . "';\n";
    /*echo "$('#cbo_company_name').attr('disabled','false')".";\n";
    echo "$('#cbo_party_name').attr('disabled','false')".";\n";
    echo "$('#txtProductionDate').attr('disabled','false')".";\n";
    echo "$('#txtStartDate').attr('disabled','false')".";\n"; 
    echo "$('#txtEndDate').attr('disabled','false')".";\n";*/ 

    
    /*echo "document.getElementById('job_mst').value = '" . $data . "';\n";*/
    // txtSalesOrder
    exit();
}

if ($action=="save_update_delete")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    // echo $txtSalesOrder;die;
    /*$trans_Type="2";*/
    //echo $total_row;die;
    $flag=0;
    
    /*var_dump($process);die;*/
    // Insert Start Here ----------------------------------------------------------
    if ($operation==0)   
    {   

        $con = connect();
        if($db_type==0) mysql_query("BEGIN");
            
        if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
        else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";

        if(str_replace("'", '', $update_id) == '')
        {
            $new_product_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name),'', 'HE' , date("Y",time()), 5, "select id,prod_no_prefix,prod_no_prefix_num from yd_production_mst where company_id=$cbo_company_name and entry_form=417 $insert_date_con order by id desc ", "prod_no_prefix", "prod_no_prefix_num" ));

            $mst_id=return_next_id("id","yd_production_mst",1);
            // echo $mst_id;die;
            $field_array="id,entry_form,booking_without_order,booking_type,prod_no_prefix,prod_no_prefix_num, yd_prod_no,batch_no,company_id, party_id, prod_date, start_date, end_date, yarn_type_id, bobbin_type_id, remarks, inserted_by,insert_date, status_active,is_deleted";
            $data_array="(".$mst_id.",'417',".$booking_without_order.",".$booking_type.",'".$new_product_no[1]."','".$new_product_no[2]."','".$new_product_no[0]."',".$txtBatchNo.",".$cbo_company_name.",".$cbo_party_name.",".$txtProductionDate.",".$txtStartDate.",".$txtEndDate.",".$cbo_yarn_type.",".$cbo_bobbin_type.",".$txtRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
        
            $txt_production_id=$new_product_no[0];

            /*echo "10**INSERT INTO yd_production_mst (".$field_array.") VALUES ".$data_array; die;*/
            $rID=sql_insert("yd_production_mst",$field_array,$data_array,0);
            if($rID==1) $flag=1; else $flag=0;
        }
        else{
            $txt_production_id=$txtProductionId;
            $mst_id=$update_id;
            $flag=1;
            
        }

        
        /*echo $mst_id;die;*/
        
        $id1=return_next_id("id","yd_production_dtls",1);
        $field_array2="id, mst_id,entry_form,sales_order_id,sales_order_no,product_id, job_id, job_dtls_id, order_id, batch_id, quantity, temperature, speed,remarks,inserted_by, insert_date,status_active,is_deleted";  

        // echo $mst_id;die;
        
        $data_array2.="(".$id1.",".$mst_id.",'417',".$sales_order_id.",".$txtSalesOrder.",".$product_id.",".$job_id.",".$job_dtls_id.",".$order_id.",".$batch_id.",".$txtPrdctnQty.",".$txtTemperature.",".$txtSpeed.",".$txtRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
        //echo $id1;
        /* echo "10**INSERT INTO yd_production_dtls (".$field_array2.") VALUES ".$data_array2; die;*/
        $rID2=sql_insert("yd_production_dtls",$field_array2,$data_array2,1);  
        if($flag==1 && $rID2==1) $flag=1; else $flag=0;
        //echo "10**".$rID."**".$rID2   ; die;
        if($db_type==0)
        {   
            if($flag==1)
            {
                mysql_query("COMMIT");  
                echo "0**".str_replace("'",'',$txt_production_id)."**".str_replace("'",'',$mst_id)."**".str_replace("'",'',$txtSalesOrder);
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "10**".str_replace("'",'',$txt_production_id)."**".str_replace("'",'',$mst_id)."**".str_replace("'",'',$txtSalesOrder);
            }
        }
        else if($db_type==2)
        {   
            if($flag==1) 
            {
                oci_commit($con);
                echo "0**".str_replace("'",'',$txt_production_id)."**".str_replace("'",'',$mst_id)."**".str_replace("'",'',$txtSalesOrder);
            }
            else
            {
                oci_rollback($con);
                echo "10**".str_replace("'",'',$txt_production_id)."**".str_replace("'",'',$mst_id)."**".str_replace("'",'',$txtSalesOrder);
            }
        } 
        disconnect($con);
        die;        
    }
    else if ($operation==1)   // Update Here
    {
        $flag = 1;
        $con = connect();

        // echo '10**emnei echo';die;

        if($db_type==0) mysql_query("BEGIN");

        $field_array_mst="prod_date*start_date*end_date*yarn_type_id*bobbin_type_id*updated_by*update_date";
        $data_array_mst="".$txtProductionDate."*".$txtStartDate."*".$txtEndDate."*".$cbo_yarn_type."*".$cbo_bobbin_type."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

        $field_array_dtls = "batch_id*quantity*temperature*speed*remarks*updated_by*update_date";

        $data_array_dtls="".$batch_id."*".$txtPrdctnQty."*".$txtTemperature."*".$txtSpeed."*".$txtRemarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

       

        // sql_update($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
        $rID = sql_update("yd_production_mst", $field_array_mst, $data_array_mst, "id", $update_id, 0);
        $flag = ($flag && $rID);    // return true if $flag is true and mst table update is successful

        /*echo "10**"."yd_material_mst", $field_array_mst, $data_array_mst, "id", $update_id ;die;*/
        /*echo "10**" . bulk_update_sql_statement("yd_material_dtls", "id", $field_array_dtls, $data_array_dtls, $id_arr);die;*/
        /*var_dump($flag);die;*/

        $rID2 = sql_update("yd_production_dtls", $field_array_dtls, $data_array_dtls, "id", $update_dtls_id, 0);

       // $flag = ($flag && $rID2);   // return true if $flag is true and dtls table update is successful

        $flag = ($flag && $rID2);   // return true if $flag is true and dtls table insert is successful

        if($db_type==0) {
            if($flag) {
                mysql_query("COMMIT");
                echo "1**".str_replace("'",'',$txtProductionId)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txtSalesOrder);
                // echo "0**".str_replace("'", '', $txt_receive_no)."**".$id_mst."**".str_replace("'",'',$txt_job_no);
            } else {
                mysql_query("ROLLBACK");
                echo "10**".str_replace("'",'',$txtProductionId)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txtSalesOrder);
            }
        }
        else if($db_type==2) {
            if($flag) {
                oci_commit($con);
                echo "1**".str_replace("'",'',$txtProductionId)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txtSalesOrder);
            } else {
                oci_rollback($con);
                echo "10**".str_replace("'",'',$txtProductionId)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txtSalesOrder);
            }
        }

        disconnect($con);
        die;
    }
    else if ($operation==2)   // delete
    {
        $con = connect();
        if($db_type==0) mysql_query("BEGIN");
         //echo $zero_val;
         
        
        
        $field_array="status_active*is_deleted*updated_by*update_date";
        $data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
        $data_array_dtls="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
            
        $flag=1;
        $rID=sql_update("yd_production_mst",$field_array,$data_array,"id",$update_id,0);  
        if($rID==1 && $flag==1) $flag=1; else $flag=0;
        /*echo "INSERT INTO yd_production_mst (".$field_array.") VALUES ".$data_array; die;*/

        $rID1=sql_update("yd_production_dtls",$field_array,$data_array_dtls,"mst_id",$update_id,1); 
        if($rID1==1 && $flag==1) $flag=1; else $flag=0; 
                
        if($db_type==0)
        {
            if($flag==1)
            {
                mysql_query("COMMIT");  
                echo "2**".str_replace("'",'',$txtProductionId)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_dtls_id);
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "10**".str_replace("'",'',$txtProductionId)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_dtls_id);
            }
        }
        else if($db_type==2)
        {
            if($flag==1)
            {
                oci_commit($con);
                echo "2**".str_replace("'",'',$txtProductionId)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_dtls_id);
            }
            else
            {
                oci_rollback($con);
                echo "10**".str_replace("'",'',$txtProductionId)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_dtls_id);
            }
        }
        disconnect($con); 
    }
}


if($action == "create_production_order_list") {
    /*echo $data;die;*/
    $data=explode('_',$data);
    $mst_id = $data[0];
    $sql = "select a.company_id,a.party_id, a.start_date,a.yd_prod_no, a.prod_date, a.end_date, a.yarn_type_id, a.bobbin_type_id, b.id as dtls_id, b.mst_id, b.job_dtls_id, b.quantity,b.temperature,b.speed
    from yd_production_mst a, yd_production_dtls b
    where b.mst_id='$mst_id' and a.id=b.mst_id";
    /*echo $sql; die;*/
   $count_arr = return_library_array("Select id, yarn_count from  lib_yarn_count where  status_active=1", 'id', 'yarn_count');
    $comp_arr = return_library_array("select id,composition_name from lib_composition_array where is_deleted=0 and status_active=1", 'id', 'composition_name');
    $arr=array(2=>$count_arr, 3=>$comp_arr);

    echo create_list_view('list_view', 'YD Product No,Start Date,Production Date,End Date,Quantity,Temperature,Speed', '150,50,80,150,100,100,200', '820', '870', 0, $sql, 'put_data_into_form', "dtls_id", "", 1, '0,0,count_id,yarn_composition_id,0', $arr, 'yd_prod_no,start_date,prod_date,end_date,quantity,temperature,speed', '', '', '0,0,0,0,1');

    exit();
}

if ($action=="production_popup")
{
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
    $data=explode("_",$data);
    $company=$data[0];
   
    $party_name=$data[1];
    
    ?>
    <script>
        function js_set_value(str)
        { 
            $("#hidden_mst_id").val(str);
            document.getElementById('selected_production').value=str;
            parent.emailwindow.hide();
        }

        
        function fnc_load_party_company_popup(company,party_name)
        {   //alert(company+'_'+party_name+'_'+within_group);   
            load_drop_down( 'yd_hydro_extractor_controller', company+'_'+party_name, 'load_drop_down_buyer_pop', 'buyer_td' );
        }
        function search_by(val)
        {
            $('#txt_search_string').val('');
            if(val==1 || val==0) $('#search_by_td').html('Production ID');
            else if(val==2) $('#search_by_td').html('Order No');
            else if(val==3) $('#search_by_td').html('Buyer Job');
            else if(val==4) $('#search_by_td').html('Buyer PO');
            else if(val==5) $('#search_by_td').html('Buyer Style');
        }       
    </script>
    </head>
    <body onLoad="fnc_load_party_company_popup(<? echo $company;?>,<? echo $party_name;?>)">
        <div align="center" style="width:100%;" >
            <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
                <table width="870" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="10"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                        <tr>                     
                            <th width="140">Company Name</th>
                            <th width="120">Party Name</th>
                            <th width="80">Style No</th>
                            <th width="100">Job No/Sales order no.</th>
                            <th width="100">Search By</th>
                            <th width="100" id="search_by_td">Production ID</th>
                            <th width="100" colspan="2">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_production">  <!--  echo $data;-->
                            <? 
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'yd_hydro_extractor_controller', this.value+'_'+".$within_group.", 'load_drop_down_buyer_pop', 'buyer_td' );"); ?>
                            </td>
                            
                            <td id="buyer_td">
                                <? 
                                echo create_drop_down( "cbo_party_name", 120, $party_name,"", 1, "-- Select Party --", $selected, "" );?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_style" id="txt_search_style" class="text_boxes" style="width:80px" placeholder="Style No" />
                            </td>
                            <td>
                                <input type="text" name="txt_search_salesorder" id="txt_search_salesorder" class="text_boxes" style="width:100px" placeholder="Job No/Sales order no." />
                            </td>
                            <td>
                                <?
                                    $search_by_arr=array(1=>"Production ID",2=>"Style No",3=>"Job No/Sales order no.");
                                    echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From">
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_style').value+'_'+document.getElementById('txt_search_salesorder').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'create_issue_search_list_view', 'search_div', 'yd_hydro_extractor_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle">
                                <? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="text_boxes" style="width:70px">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="top" id=""><div id="search_div"></div></td>
                        </tr>
                    </tbody>
                </table>    
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}
if ($action=="batchno_popup")
{
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
    $data=explode("_",$data);
    $company=$data[0];
   
    $party_name=$data[1];
    
    ?>
    <script>
        function js_set_value(str)
        { 
           
            document.getElementById('selected_batch_no').value=str;
            parent.emailwindow.hide();
        }
        window.onload = function() {
            $('#cbo_company_name').attr('disabled','disabled');
        }

        
        function fnc_load_party_company_popup(company,party_name)
        {   //alert(company+'_'+party_name+'_'+within_group);   
            load_drop_down( 'yd_hydro_extractor_controller', company+'_'+party_name, 'load_drop_down_buyer_pop', 'buyer_td' );
        }
        function search_by(val)
        {
            $('#txt_search_string').val('');
            if(val==1 || val==0) $('#search_by_td').html('Batch No');
            else if(val==2) $('#search_by_td').html('Order No');
            else if(val==3) $('#search_by_td').html('Buyer Job');
            else if(val==4) $('#search_by_td').html('Buyer PO');
            else if(val==5) $('#search_by_td').html('Buyer Style');
        }       
    </script>
    </head>
    <body onLoad="fnc_load_party_company_popup(<? echo $company;?>,<? echo $party_name;?>)">
        <div align="center" style="width:100%;" >
            <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
                <table width="870" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="10"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                        <tr>                     
                            <th width="140">Company Name</th>
                            <th width="120">Party Name</th>
                            <th width="80">Style No</th>
                            <th width="100">Job No/Sales order no.</th>
                            <th width="100">Search By</th>
                            <th width="100" id="search_by_td">Batch No</th>
                            <th width="100" colspan="2">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_batch_no">  <!--  echo $data;-->
                            <? 
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'yd_hydro_extractor_controller', this.value+'_'+".$within_group.", 'load_drop_down_buyer_pop', 'buyer_td' );"); ?>
                            </td>
                            
                            <td id="buyer_td">
                                <? 
                                echo create_drop_down( "cbo_party_name", 120, $party_name,"", 1, "-- Select Party --", $selected, "" );?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_style" id="txt_search_style" class="text_boxes" style="width:80px" placeholder="Style No" />
                            </td>
                            <td>
                                <input type="text" name="txt_search_salesorder" id="txt_search_salesorder" class="text_boxes" style="width:100px" placeholder="Job No/Sales order no." />
                            </td>
                            <td>
                                <?
                                    $search_by_arr=array(1=>"Batch No",2=>"Style No",3=>"Job No/Sales order no");
                                    echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From">
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_style').value+'_'+document.getElementById('txt_search_salesorder').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'create_batch_no_search_list_view', 'search_div', 'yd_hydro_extractor_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle">
                                <? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="text_boxes" style="width:70px">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="top" id=""><div id="search_div"></div></td>
                        </tr>
                    </tbody>
                </table>    
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action=="load_drop_down_buyer_pop")
{
    //echo $data; die;
    $data=explode('_',$data);
    
        echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[1], "");
   
    
    exit();
} 

if($action=="create_issue_search_list_view")
{
    $data=explode('_',$data);
    
    $search_type =$data[6];
    
    $search_by=str_replace("'","",$data[7]);
    $search_str=trim(str_replace("'","",$data[8]));
    /*var_dump($data);die;*/
   /* echo $search_str;die;*/

    if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
    if ($data[1]!=0) $buyer_cond=" and a.party_id='$data[1]'"; else $buyer_cond="";
    if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
    if($db_type==0)
    { 
        if ($data[2]!="" &&  $data[3]!="") $production_date = "and a.prod_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $production_date ="";
    }
    else
    {
        if ($data[2]!="" &&  $data[3]!="") $production_date = "and a.prod_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $production_date ="";
    }
    $job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
    if($search_type==1)
    {
        if($search_str!="")
        {
            if($search_by==1) $search_com_cond="and a.prod_no_prefix_num='$search_str'";
            
            else if ($search_by==3) $search_com_cond=" and a.prod_no_prefix_num = '$search_str' ";
            
            
        }
        
        
    }
    else if($search_type==4 || $search_type==0)
    {
        if($search_str!="")
        {
            if($search_by==1) $search_com_cond="and a.prod_no_prefix_num like '$search_str%'";  
             
            else if ($search_by==3) $search_com_cond=" and a.prod_no_prefix_num like '$search_str%'";  
            
        }
        
    }
    else if($search_type==2)
    {
        if($search_str!="")
        {
            if($search_by==1) $search_com_cond="and a.prod_no_prefix_num like '$search_str%'";  
            
            
            else if ($search_by==3) $search_com_cond=" and a.prod_no_prefix_num like '$search_str%'";  
            
        }
        
    }
    else if($search_type==3)
    {
        if($search_str!="")
        {
            if($search_by==1) $search_com_cond="and a.prod_no_prefix_num like '%$search_str'";  
            
            
            else if ($search_by==3) $search_com_cond=" and a.prod_no_prefix_num like '%$search_str'";  
            
        }
        
    }   
    
    
            $order_buyer_po_array=array();
            $buyer_po_arr=array();
            $order_buyer_po='';
            /*$order_sql ="select b.id,b.buyer_po_no,b.buyer_style_ref from yd_ord_mst a, yd_ord_dtls b where a.id=b.mst_id and a.entry_form='374' $search_com_cond"; */
            
    
    $party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $comp=return_library_array("select id, company_name from lib_company",'id','company_name');
    $po_arr=return_library_array( "select id,order_no from yd_ord_dtls",'id','order_no');
    
    /*$po_ids=''; //$buyer_po_arr=array();
    if($within_group==1)
    {
        if($db_type==0) $id_cond="group_concat(b.id)";
        else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
        //echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
        if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
        {
            $po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
        }
        //echo $po_ids;
        if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
        
        $po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
        $po_sql_res=sql_select($po_sql);
        foreach ($po_sql_res as $row)
        {
            $buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
            $buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
        }
        unset($po_sql_res);
    }*/
    //$spo_ids='';
    
    if($db_type==0)
    {
        $id_cond="group_concat(b.id)";
        $insert_date_cond="year(a.insert_date)";
                        
        $buyer_po_id_cond="group_concat(distinct(b.order_id))";
    }
    else if($db_type==2)
    {
        $id_cond="listagg(b.id,',') within group (order by b.id)";
        $insert_date_cond="TO_CHAR(a.insert_date,'YYYY')";
        $wo_cond="listagg(b.job_dtls_id,',') within group (order by b.job_dtls_id)";
        $buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
    }
    /*if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
    {
        $spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.subcon_job=b.job_no_mst $search_com_cond", "id");
    }
    
    if ( $spo_ids!="") $spo_idsCond=" and b.job_dtls_id in ($spo_ids)"; else $spo_idsCond="";
    */
    
    $sql= "select a.id,b.job_id, a.yd_prod_no,a.prod_no_prefix,a.prod_no_prefix_num, $insert_date_cond as year, a.location_id, a.party_id, a.prod_date,a.start_date,a.end_date from yd_production_mst a, yd_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=417 $production_date $company $buyer_cond $issue_id_cond group by a.id,b.job_id, a.yd_prod_no, a.prod_no_prefix_num,a.prod_no_prefix, a.insert_date, a.location_id, a.party_id, a.prod_date,a.start_date,a.end_date order by a.id DESC "; // within group add here if need
    /*echo $sql;die; */
    $result = sql_select($sql);
    ?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
            <thead>
                <th width="40" >SL</th>
                <th width="70" >Production No</th>
                <th width="70" >Year</th>
                <th width="120" >Party Name</th>
                <th width="100" >Start Date</th>
                <th width="80" >Product Date</th>
                <th width="120">End Date</th>
                
               
            </thead>
        </table>
     <div style="width:820px; max-height:270px;overflow-y:scroll;" >     
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_po_list">
            <?
            $i=1;
            foreach( $result as $row )
            {   
                $party_name=$party_arr[$row[csf("party_id")]];
                ?>  
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onclick="js_set_value('<? echo $row[csf("id")].'_'.$row[csf("job_id")];?>');"> 
                        <td width="40" align="center"><? echo $i; ?></td>
                        <td width="70" align="center"><? echo $row[csf("yd_prod_no")]; ?></td>
                        <td width="70" align="center"><? echo $row[csf("year")]; ?></td>
                        <td width="120"><? echo $party_name; ?></td>        
                        <td width="100" align="center"><? echo $row[csf("start_date")]; ?></td>
                        <td width="80"><? echo change_date_format($row[csf("prod_date")]);  ?></td>  
                        <td width="120" style="word-break:break-all"><p><? echo $row[csf("end_date")]; ?></p></td>   
                        
                    </tr>
                <? 
                $i++;
            }
        ?>
            </table>
        </div>
     </div>
     <? 
    exit();
}

if($action=="create_batch_no_search_list_view")
{
    $data=explode('_',$data);
    
    $search_type =$data[6];
    
    $search_by=str_replace("'","",$data[7]);
    $search_str=trim(str_replace("'","",$data[8]));
    /*var_dump($data);die;*/
   /* echo $search_str;die;*/

    if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
    if ($data[1]!=0) $buyer_cond=" and a.party_id='$data[1]'"; else $buyer_cond="";
    if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
    if($db_type==0)
    { 
        if ($data[2]!="" &&  $data[3]!="") $batch_date = "and a.batch_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $batch_date ="";
    }
    else
    {
        if ($data[2]!="" &&  $data[3]!="") $batch_date = "and a.batch_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $batch_date ="";
    }
    $job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
    if($search_type==1)
    {
        if($search_str!="")
        {
            if($search_by==1) $search_com_cond="and a.yd_batch_id_prefix_num='$search_str'";
            
            else if ($search_by==3) $search_com_cond=" and a.yd_batch_id_prefix_num = '$search_str' ";
            
            
        }
        
        
    }
    else if($search_type==4 || $search_type==0)
    {
        if($search_str!="")
        {
            if($search_by==1) $search_com_cond="and a.yd_batch_id_prefix_num like '$search_str%'";  
             
            else if ($search_by==3) $search_com_cond=" and a.yd_batch_id_prefix_num like '$search_str%'";  
            
        }
        
    }
    else if($search_type==2)
    {
        if($search_str!="")
        {
            if($search_by==1) $search_com_cond="and a.yd_batch_id_prefix_num like '$search_str%'";  
            
            
            else if ($search_by==3) $search_com_cond=" and a.yd_batch_id_prefix_num like '$search_str%'";  
            
        }
        
    }
    else if($search_type==3)
    {
        if($search_str!="")
        {
            if($search_by==1) $search_com_cond="and a.yd_batch_id_prefix_num like '%$search_str'";  
            
            
            else if ($search_by==3) $search_com_cond=" and a.yd_batch_id_prefix_num like '%$search_str'";  
            
        }
        
    }   
    
    
            $order_buyer_po_array=array();
            $buyer_po_arr=array();
            $order_buyer_po='';
            /*$order_sql ="select b.id,b.buyer_po_no,b.buyer_style_ref from yd_ord_mst a, yd_ord_dtls b where a.id=b.mst_id and a.entry_form='374' $search_com_cond"; */
            
    
    $party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $comp=return_library_array("select id, company_name from lib_company",'id','company_name');
    $po_arr=return_library_array( "select id,order_no from yd_ord_dtls",'id','order_no');
    $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
    
    
    /*$po_ids=''; //$buyer_po_arr=array();
    if($within_group==1)
    {
        if($db_type==0) $id_cond="group_concat(b.id)";
        else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
        //echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
        if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
        {
            $po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
        }
        //echo $po_ids;
        if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
        
        $po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
        $po_sql_res=sql_select($po_sql);
        foreach ($po_sql_res as $row)
        {
            $buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
            $buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
        }
        unset($po_sql_res);
    }*/
    //$spo_ids='';
    
    if($db_type==0)
    {
        $id_cond="group_concat(b.id)";
        $insert_date_cond="year(a.insert_date)";
                        
        $buyer_po_id_cond="group_concat(distinct(b.order_id))";
    }
    else if($db_type==2)
    {
        $id_cond="listagg(b.id,',') within group (order by b.id)"; //listagg
        $insert_date_cond="TO_CHAR(a.insert_date,'YYYY')";
        $wo_cond="listagg(b.job_dtls_id,',') within group (order by b.job_dtls_id)";
        $buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
    }
    /*if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
    {
        $spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.subcon_job=b.job_no_mst $search_com_cond", "id");
    }
    
    if ( $spo_ids!="") $spo_idsCond=" and b.job_dtls_id in ($spo_ids)"; else $spo_idsCond="";
    */
    
    $sql= "select a.id, a.yd_batch_id,a.yd_batch_id_prefix,a.yd_batch_id_prefix_num, $insert_date_cond as year, a.location_id, a.batch_date,a.batch_color_id,a.batch_number,a.batch_weight from yd_batch_mst a, yd_batch_dtls b where a.id=b.mst_id and a.entry_form=398 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_date $company $buyer_cond $issue_id_cond group by a.id, a.yd_batch_id, a.yd_batch_id_prefix,a.yd_batch_id_prefix_num, a.insert_date, a.location_id, a.batch_date,a.batch_color_id,a.batch_number,a.batch_weight order by a.id DESC "; // within group add here if need
    /*echo $sql;die; */
    $result = sql_select($sql);
    ?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
            <thead>
                <th width="40" >SL</th>
                <th width="70" >Batch No</th>
                <th width="70" >Year</th>
                <th width="120" >Batch Date</th>
                <th width="100" >Batch Color</th>
                <th width="80" >Batch Number</th>
                <th width="120">Batch Weight</th>
                
               
            </thead>
        </table>
     <div style="width:820px; max-height:270px;overflow-y:scroll;" >     
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_po_list">
            <?
            $i=1;
            foreach( $result as $row )
            {   
                /*$color_arr=$color_arr[$row[csf("batch_color_id")]];*/
                ?>  
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onclick="js_set_value('<? echo $row[csf("id")].'_'.$row[csf("yd_batch_id")];?>');">  
                        <td width="40" align="center"><? echo $i; ?></td>
                        <td width="70" align="center"><? echo $row[csf("yd_batch_id")]; ?></td>
                        <td width="70" align="center"><? echo $row[csf("year")]; ?></td>
                        <td width="120"><? echo change_date_format($row[csf("batch_date")]);  ?></td> 
                        <td width="100" align="center"><? echo $color_arr[$row[csf("batch_color_id")]]; ?></td> 
                        <td width="80" align="center"><? echo $row[csf("batch_number")]; ?></td> 
                        <td width="120" style="word-break:break-all"><p><? echo $row[csf("batch_weight")]; ?></p></td>   
                        
                    </tr>
                <? 
                $i++;
            }
        ?>
            </table>
        </div>
     </div>
     <? 
    exit();
}

if ($action=="load_php_data_to_form")
{  
    
    $nameArray=sql_select( "select id,batch_no, yd_prod_no,prod_no_prefix,prod_no_prefix_num,prod_date, start_date,company_id, party_id, end_date from yd_production_mst where id='$data'");
   /* echo "select id, yd_prod_no,prod_no_prefix,prod_no_prefix_num,prod_date, start_date, party_id, end_date from yd_production_mst where id='$data'"; die;*/
    /*print_r($nameArray);die;*/

    $party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $comp=return_library_array("select id, company_name from lib_company",'id','company_name');
    foreach ($nameArray as $row)
    {   
        echo "document.getElementById('cbo_company_name').value     = '".$row[csf("company_id")]."';\n";
        echo "$('#cbo_company_name').attr('disabled','true')".";\n";  
        
        echo "load_drop_down('requires/yd_hydro_extractor_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'party_td' );\n";
       
        echo "document.getElementById('txtProductionId').value         = '".$row[csf("yd_prod_no")]."';\n";
        echo "document.getElementById('txtProductionDate').value   = '".change_date_format($row[csf("prod_date")])."';\n"; 
        echo "document.getElementById('txtStartDate').value   = '".change_date_format($row[csf("start_date")])."';\n"; 
        echo "document.getElementById('txtEndDate').value   = '".change_date_format($row[csf("end_date")])."';\n"; 
       
       /* echo "$('#cbo_company_name').attr('disabled','true')".";\n"; */
        echo "document.getElementById('cbo_party_name').value       = '".$row[csf("party_id")]."';\n";
        echo "document.getElementById('txtBatchNo').value         = '".$row[csf("batch_no")]."';\n";     
           
        echo "document.getElementById('update_id').value            = '".$row[csf("id")]."';\n";
        echo "$('#cbo_party_name').attr('disabled','true')".";\n";
        echo "$('#txtProductionDate').attr('disabled','true')".";\n";
        echo "$('#txtStartDate').attr('disabled','true')".";\n"; 
        echo "$('#txtEndDate').attr('disabled','true')".";\n";  

        
        
        /*echo "$('#cbo_within_group').attr('disabled','true')".";\n"; 
        echo "$('#cbo_party_name').attr('disabled','true')".";\n"; */
    }
    exit();
}



?>