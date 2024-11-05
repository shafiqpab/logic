<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action == "load_drop_down_location") 
{
    echo create_drop_down("cbo_location", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name", "id,location_name", 1, "--All Location--", $selected, "", 0);
    exit();
}

if( $action=="report_generate" )
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name=str_replace("'","",$cbo_company_name);
    $cbo_location = str_replace("'","",$cbo_location); 
    $cbo_year=str_replace("'","",$cbo_year);
    $start_date = str_replace("'","",$txt_date_from);
    $end_date = str_replace("'","",$txt_date_to); 

    $service_company_cond = ($company_name == 0) ? "" : " and a.service_company=$company_name";
    $company_cond = ($company_name == 0) ? "" : " and a.company_id=$company_name";
    $knitting_company_cond = ($company_name == 0) ? "" : " and a.knitting_company=$company_name";
    $fin_location_cond = ($cbo_location == 0) ? "" : " and a.location_id=$cbo_location";
    $to_location_id_cond  = ($cbo_location == 0) ? "" : " and a.to_location_id=$cbo_location";
	
	if($db_type==0)
	{
		if($cbo_year!=0) $yearCond=" and year(a.insert_date)=".$cbo_year.""; else $yearCond="";
	}
	else
	{
		if($cbo_year!=0) $yearCond=" and to_char(a.insert_date,'YYYY')=".$cbo_year.""; else $yearCond="";
	}

    $dyeing_date_cond="";$finish_date_cond="";$subCon_finish_date_cond="";$finish_trans_date_cond="";$delivery_date_cond="";
    if($start_date!="" && $end_date!="")
    {
        $dyeing_date_cond="and a.production_date between '$start_date' and '$end_date'";
        $finish_date_cond="and a.receive_date between '$start_date' and '$end_date'";
        $subCon_finish_date_cond="and a.product_date between '$start_date' and '$end_date'";
        $finish_trans_date_cond="and a.transfer_date between '$start_date' and '$end_date'";
        $delivery_date_cond="and a.delivery_date between '$start_date' and '$end_date'";
    }
    // echo $date_cond;die;
		
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
    $location_library=return_library_array( "select id, location_name from lib_location where status_active =1 and is_deleted=0", "id", "location_name"  );
            
    // ========= Dyeing Production and Sub Con. Dyeing Production Start ==========
    /*
    |--------------------------------------------------------------------------
    | Dyeing Production > In-house, Unload, shade match
    | Sub Con. Dyeing Production > In-house, Unload, shade match
    |--------------------------------------------------------------------------
    |
    */
    $dyeing_prod_sql="SELECT a.service_company, a.production_date, a.process_id, a.entry_form, a.batch_id, a.batch_no, c.batch_qnty
    from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c
    where a.batch_id=b.id and b.id=c.mst_id and a.service_source=1 and a.entry_form in(35,38) and a.load_unload_id=2 and a.result=1 $dyeing_date_cond $service_company_cond $yearCond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
    // echo $dyeing_prod_sql;
    $dyeing_prod_sql_result= sql_select($dyeing_prod_sql);
    $data_arr=array();$batch_chk=array();
    foreach($dyeing_prod_sql_result as $row)
    {   
        if ($row[csf('entry_form')]==35) 
        {
            $data_arr[$row[csf('service_company')]]['prod_qty'] += $row[csf('batch_qnty')];
        }
        elseif ($row[csf('entry_form')]==38) 
        {
            $data_arr[$row[csf('service_company')]]['subCon_prod_qty'] += $row[csf('batch_qnty')];
        }

        if ($batch_chk[$row[csf('batch_id')]]=="") 
        {
            $batch_chk[$row[csf('batch_id')]]=$row[csf('batch_id')];
            $data_arr[$row[csf('service_company')]]['dyeing_no_of_batch'] ++;
        }
    }
    // echo "<pre>";print_r($data_arr);die;
    // =========== Dyeing Production and Sub Con. Dyeing Production End ==========

    
    // ========= Finish Fabric Start ==========
    /*
    |--------------------------------------------------------------------------
    | Production > Finish Fabric Production Entry
    |--------------------------------------------------------------------------
    |
    */
    $finish_fabric_sql=" SELECT a.knitting_company, a.location_id, b.batch_id, b.receive_qnty as fin_qty_in
    from inv_receive_master a, pro_finish_fabric_rcv_dtls b
    where a.id=b.mst_id and a.entry_form=7 and a.item_category=2 and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 $finish_date_cond $fin_location_cond $knitting_company_cond $yearCond";
    // echo $finish_fabric_sql;
    $finish_fabric_sql_result= sql_select($finish_fabric_sql);
    $batch_chk=array();
    foreach($finish_fabric_sql_result as $row)
    {
        $data_arr[$row[csf('knitting_company')]]['finish_qty'] += $row[csf('fin_qty_in')];
        $data_arr[$row[csf('knitting_company')]]['location_id'] .= $row[csf('location_id')].',';

        if ($batch_chk[$row[csf('batch_id')]]=="") 
        {
            $batch_chk[$row[csf('batch_id')]]=$row[csf('batch_id')];
            $data_arr[$row[csf('knitting_company')]]['finish_no_of_batch'] ++;
        }
    }
    // echo "<pre>";print_r($data_arr);die;
    // =========== Finish Fabric End ==========

    // =========== Sub Con. Finish Fabric Start ==========
    /*
    |--------------------------------------------------------------------------
    | S.con > Fabric Finishing Entry
    |--------------------------------------------------------------------------
    |
    */
    $subc_dyeing_fini_sql="SELECT a.company_id, a.location_id, a.entry_form, b.batch_id, b.product_qnty
    from subcon_production_mst a, subcon_production_dtls b 
    where a.id=b.mst_id and a.entry_form=292 $subCon_finish_date_cond $fin_location_cond $company_cond $yearCond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
    // echo $subc_dyeing_fini_sql;
    $subc_dyeing_fini_result=sql_select($subc_dyeing_fini_sql);
    $batch_chk=array();
    foreach ($subc_dyeing_fini_result as $value) 
    {
        $data_arr[$value[csf('company_id')]]['subCon_finish_qty'] += $value[csf('product_qnty')];
        $data_arr[$value[csf('company_id')]]['location_id'] .= $value[csf('location_id')].',';
        if ($batch_chk[$value[csf('batch_id')]]=="") 
        {
            $batch_chk[$value[csf('batch_id')]]=$value[csf('batch_id')];
            $data_arr[$value[csf('company_id')]]['finish_no_of_batch']++;
        }
    }
    // echo "<pre>";print_r($data_arr);die;
    // =========== Sub Con. Finish Fabric End ==========

    // =========== Knit Finish Fabric Transfer Entry Start ==========
    /*
    |--------------------------------------------------------------------------
    | Finish > Garments > Knit Finish Fabric Transfer Entry > 
    | only store to store, To Location
    |--------------------------------------------------------------------------
    |
    */
    $fin_transfer_sql="SELECT a.company_id, a.to_location_id, b.pi_wo_batch_no as batch_id, b.cons_quantity
    from inv_item_transfer_mst a, inv_transaction b
    where  a.id=b.mst_id and b.item_category=2 and a.transfer_criteria=2 and a.entry_form=14 and b.transaction_type in(5) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $finish_trans_date_cond $to_location_id_cond $company_cond $yearCond";
    // echo $fin_transfer_sql;
    $fin_transfer_sql_result=sql_select($fin_transfer_sql);
    // $batch_chk=array();
    foreach ($fin_transfer_sql_result as $row) 
    {
        $data_arr[$row[csf('company_id')]]['finish_trans_qty'] += $row[csf('cons_quantity')];
        $data_arr[$row[csf('company_id')]]['location_id'] .= $row[csf('to_location_id')].',';
        /*if ($batch_chk[$row[csf('batch_id')]]=="") 
        {
            $batch_chk[$row[csf('batch_id')]]=$row[csf('batch_id')];
            $data_arr[$row[csf('company_id')]]['trans_fin_no_of_batch'] ++;
        }*/
    }
    // echo "<pre>";print_r($data_arr);
    // =========== Knit Finish Fabric Transfer Entry End ==========


    // =========== SubCon Dye And Finishing Delivery Start ==========
    /*
    |--------------------------------------------------------------------------
    | S.Con > Delivery > SubCon Dye And Finishing Delivery
    |--------------------------------------------------------------------------
    |
    */
    $subCon_delivery_sql="SELECT a.company_id, a.location_id, a.delivery_date, a.location_id, b.delivery_qty FROM subcon_delivery_mst a, subcon_delivery_dtls b WHERE a.id=b.mst_id and a.entry_form=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $delivery_date_cond $fin_location_cond $company_cond $yearCond";
    // echo $subCon_delivery_sql;
    $subCon_delivery_sql_result= sql_select($subCon_delivery_sql);
    foreach($subCon_delivery_sql_result as $row)
    {
        $data_arr[$row[csf('company_id')]]['delivery_qty'] += $row[csf('delivery_qty')];
        $data_arr[$row[csf('company_id')]]['location_id'] .= $row[csf('location_id')].',';
    }
    // echo "<pre>";print_r($data_arr);
    // =========== SubCon Dye And Finishing Delivery End ==========


    // echo "<pre>";print_r($data_arr);

	ob_start();
	$width=1100;
	?>
    <style type="text/css">
        .first {
          background: rgba(0, 128, 0, 0.2);
        }
        .second {
          background: rgba(0, 128, 0, 0.4);
        }
        .third {
          background: rgba(0, 128, 0, 0.6);
        }
    </style>
    <div style="width:<? echo $width;?>px">
    <fieldset style="width:<? echo $width;?>px;">
        <table width="<? echo $width;?>">
            <tr class="form_caption">
                <td colspan="14" align="center"><strong><? echo $report_title;?></strong></td>
            </tr>
            <?
            if ($company_name!=0) 
            {
                ?>
                <tr class="form_caption">
                    <td colspan="14" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
                </tr>
                <?
            }
            ?>
            <tr class="form_caption">
                <td colspan="14" align="center">
                    <b>
                    <?
                    $date_head="";
                    if( $start_date)
                    {
                        $date_head .= change_date_format($start_date).' To ';
                    }
                    if( $end_date)
                    {
                        $date_head .= change_date_format($end_date);
                    }
                    echo $date_head;
                    ?></b>
                </td>
            </tr>
        </table>
         <!-- id="table_header_1" -->
        <table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="120">Working Company</th>
                <th width="150">Location</th>
                <th width="70">Number of Batch</th>
                <th width="70">Dyeing Qty. In-House</th>
                <th width="70">Dyeing Qty. Sub-con.</th>
                <th width="70">Total</th>
                <th width="70">Number of Batch</th>
                <th width="70">Finishing Qty. In-House</th>
                <th width="70">Finishing Qty. Sub-con.</th>
                <th width="70">Total</th>
                <th width="70">Finish Fabric Store To Store Trans.</th>
                <th width="70">Delivery qty. Sub-con.</th>
                <th width="">Total</th>
            </thead>
        </table>
        <div style="width:<? echo $width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <?

            $tot_rows=count($data_arr);
            $i=1; 
            $total_dyeing_no_of_batch=0;$total_prod_qty=0;$total_subCon_prod_qty=0;$total_fin_no_of_batch=0;$total_finish_qty=0;$total_subCon_finish_qty=0;$total_tot_fin_prod_qty=0; 
            foreach($data_arr as $company_id => $row )
            {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                $location_id_arr = array_unique(array_filter(explode(",", $row['location_id'])));
                $location_names="";
                foreach ($location_id_arr as $lid)
                {
                    $location_names .= ($location_names =="") ? $location_library[$lid] :  ",". $location_library[$lid];
                }
                $location_names =implode(",",array_filter(array_unique(explode(",", $location_names))));

                ?>
                <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                    <td width="40"><? echo $i; ?></td>
                    <td width="120"><? echo $company_library[$company_id]; ?></td>
                    <td width="150"><p style="word-break:break-all"><? echo $location_names; ?></p></td>
                    <td width="70" align="right" class="first"><p style="word-break:break-all"><? echo $row['dyeing_no_of_batch']; ?></p></td>
                    <td width="70" align="right" class="first"><p style="word-break:break-all"><? echo number_format($row['prod_qty'],2,'.',''); ?></p></td>
                    <td width="70" align="right" class="first"><p style="word-break:break-all"><? echo number_format($row['subCon_prod_qty'],2,'.',''); ?></p></td>
                    <td width="70" align="right" class="first"><p><? $tot_prod_qty=$row['prod_qty']+$row['subCon_prod_qty']; echo number_format($tot_prod_qty,2,'.','') ?></p></td>
                    <td width="70" align="right" class="second"><p><? echo $row['finish_no_of_batch']; ?></p></td>
                    <td width="70" align="right" class="second"><p style="word-break:break-all"> <? echo number_format($row['finish_qty'],2,'.',''); ?></p></td>
                    <td width="70" align="right" class="second"><p style="word-break:break-all"><? echo number_format($row['subCon_finish_qty'],2,'.',''); ?></p></td>
                    <td width="70" align="right" class="second"><? $tot_fin_prod_qty=$row['finish_qty']+$row['subCon_finish_qty']; echo number_format($tot_fin_prod_qty,2,'.',''); ?></td>
                    <td width="70" align="right" class="third"><? echo number_format($row['finish_trans_qty'],2,'.',''); ?></td>
                    <td width="70" align="right" class="third"><? echo number_format($row['delivery_qty'],2,'.',''); ?></td>
                    <td width="" align="right" class="third"><? $tot_trans_and_delivery=$row['finish_trans_qty']+$row['delivery_qty']; echo number_format($tot_trans_and_delivery,2,'.',''); ?></td>
                </tr>
                <?
                $total_dyeing_no_of_batch+=$row['dyeing_no_of_batch'];
			    $total_prod_qty+=$row['prod_qty'];
                $total_subCon_prod_qty+=$row['subCon_prod_qty'];
                $total_tot_prod_qty+=$tot_prod_qty;
                $total_fin_no_of_batch+=$row['finish_no_of_batch'];
                $total_finish_qty+=$row['finish_qty'];
                $total_subCon_finish_qty+=$row['subCon_finish_qty'];
                $total_tot_fin_prod_qty+=$tot_fin_prod_qty;
                $total_finish_trans_qty+=$row['finish_trans_qty'];
                $total_delivery_qty+=$row['delivery_qty'];
                $total_tot_trans_and_delivery+=$tot_trans_and_delivery;
                $i++;
            }
            ?>
            </table>
            <table class="rpt_table" width="<? echo $width;?>" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tfoot>
                    <th width="40"></th>
                    <th width="120"></th>
                    <th width="150" align="right"><strong>Total:</strong></th>
                    <th width="70" align="right" id="total_dyeing_no_of_batch"><? echo $total_dyeing_no_of_batch;?></th>
                    <th width="70" align="right" id="total_prod_qty"><? echo $total_prod_qty;?></th>
                    <th width="70" align="right" id="total_subCon_prod_qty"><? echo $total_subCon_prod_qty;?></th>
                    <th width="70" align="right" id="total_tot_prod_qty"><? echo $total_tot_prod_qty;?></th>
                    <th width="70" align="right" id="total_fin_no_of_batch"><? echo $total_fin_no_of_batch;?></th>
                    <th width="70" align="right" id="total_finish_qty"><? echo $total_finish_qty;?></th>
                    <th width="70" align="right" id="total_subCon_finish_qty"><? echo $total_subCon_finish_qty;?></th>
                    <th width="70" align="right" id="total_tot_fin_prod_qty"><? echo $total_tot_fin_prod_qty;?></th>
                    <th width="70" align="right" id="total_finish_trans_qty"><? echo $total_finish_trans_qty;?></th>
                    <th width="70" align="right" id="total_delivery_qty"><? echo $total_delivery_qty;?></th>
                    <th width="" align="right" id="total_tot_trans_and_delivery"><? echo $total_tot_trans_and_delivery;?></th>
                </tfoot>
            </table>
        </div>
        </fieldset>
    </div>
    <?

	 
	echo "$total_data****requires/$filename****$tot_rows";
	exit();	
}
?>