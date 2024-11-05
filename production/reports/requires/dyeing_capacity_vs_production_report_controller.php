<? 
header('Content-type:text/html; charset=utf-8');
session_start();
require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

/*
|------------------------------------------------------------------------
| for load_drop_down_floor_from
|------------------------------------------------------------------------
*/

if ($action == "load_drop_down_floor_from")
{
	echo create_drop_down("cbo_floor_from", 100, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=2 and b.company_id=$data and b.status_active=1 and b.is_deleted=0 group by a.id, a.floor_name order by a.floor_name", "id,floor_name", 1, "-- Select Floor --", 0, "", "");
	exit();
}

/*
|------------------------------------------------------------------------
| for load_drop_down_floor_to
|------------------------------------------------------------------------
*/
if ($action == "load_drop_down_floor_to")
{
	echo create_drop_down("cbo_floor_to", 100, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=2 and b.company_id=$data and b.status_active=1 and b.is_deleted=0 group by a.id, a.floor_name order by a.floor_name", "id,floor_name", 1, "-- Select Floor --", 0, "", "");
	exit();
}


/*
|------------------------------------------------------------------------
| for report_generate
|------------------------------------------------------------------------
*/
if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

    $company            = str_replace("'","",$cbo_company_id);
    $cbo_within_group   = str_replace("'","",$cbo_within_group);
    $cbo_inbound_subcon = str_replace("'","",$cbo_inbound_subcon);
    $cbo_floor_from     = str_replace("'","",$cbo_floor_from);
    $cbo_floor_to       = str_replace("'","",$cbo_floor_to);
    $from_date          = str_replace("'", "", trim($txt_date_from));
    $to_date            = str_replace("'", "", trim($txt_date_to));

    if ($company==0 || $company=="") $companyCond=""; else $companyCond="  and a.company_id in($company)";
    if ($cbo_within_group==0 || $cbo_within_group=="") $withinGroupCond=""; else $withinGroupCond="  and d.within_group in($cbo_within_group)";

    if ($cbo_floor_from==0 || $cbo_floor_from=='')
	{
		$floor_id_from_cond="";
	}
    else 
    {
        $floor_id_from_cond=" and f.floor_id=$cbo_floor_from";
    }

    if ($cbo_floor_to==0 || $cbo_floor_to=='')
	{
		$floor_id_to_cond="";
	}
    else 
    {
        $floor_id_to_cond=" and f.floor_id=$cbo_floor_to";
    }

    //for date
	if($from_date != "" && $to_date != "")
	{
        $date_cond="and  f.process_end_date BETWEEN '$from_date' AND '$to_date'";
	}
	else
	{
		$date_cond = "";
	}

    $companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
   
    $floor_result=sql_select("select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=2 and b.company_id=$company and b.status_active=1 and b.is_deleted=0  group by a.id, a.floor_name order by a.floor_name");

    $floorArr = array();
    foreach($floor_result as $row)
    {
        $floorArr[$row[csf('id')]] = $row[csf('floor_name')];
    }

   
    $sql = "SELECT a.id as batch_id, a.total_trims_weight, a.batch_weight, f.process_end_date as production_date,d.within_group,f.floor_id, 1 as type
    from pro_batch_create_dtls b, fabric_sales_order_mst d, pro_fab_subprocess f, pro_batch_create_mst a 
    where f.batch_id=a.id and a.id=b.mst_id and f.batch_id=b.mst_id and b.po_id=d.id 
    $companyCond $date_cond $floor_id_from_cond $withinGroupCond
    and a.entry_form=0   and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=1 and b.is_sales=1
    union all
    SELECT a.id as batch_id, a.total_trims_weight, a.batch_weight, f.process_end_date as production_date,d.within_group,f.floor_id, 2 as type
    from pro_batch_create_dtls b, fabric_sales_order_mst d, pro_fab_subprocess f, pro_batch_create_mst a 
    where f.batch_id=a.id and a.id=b.mst_id and f.batch_id=b.mst_id and b.po_id=d.id 
    $companyCond $date_cond $floor_id_to_cond $withinGroupCond
    and a.entry_form=0 and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=1 and b.is_sales=1";
   
    
    if($cbo_inbound_subcon==1)
    {
        $sql_subcon="SELECT a.id as batch_id, a.total_trims_weight, a.batch_weight, b.batch_qnty AS sub_batch_qnty, f.process_end_date as production_date, null as within_group, f.floor_id, 1 as type
        from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f
        where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond $date_cond $floor_id_from_cond and a.entry_form=36 and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 
        union all
        SELECT a.id as batch_id, a.total_trims_weight, a.batch_weight, b.batch_qnty AS sub_batch_qnty, f.process_end_date as production_date, null as within_group, f.floor_id, 2 as type
        from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f
        where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond $date_cond $floor_id_to_cond and a.entry_form=36 and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0";

       
    }

    //echo $sql;
   
    $batchdata=sql_select($sql);
    $subcondata=sql_select($sql_subcon);
    
    $batchIdsChk = array();
    $batchIdsArr = array();
    foreach($batchdata as $row)
    {
        if($batchIdsChk[$row[csf("batch_id")]]=='')
        {
            $batchIdsChk[$row[csf("batch_id")]]=$row[csf("batch_id")];
            array_push($batchIdsArr,$row[csf("batch_id")]);
        }
    }

    $sql_prod_ref= sql_select("select a.id,a.batch_id, b.prod_id, b.const_composition, (b.batch_qty) as batch_qty, (b.production_qty) as production_qty
    from  pro_fab_subprocess a, pro_fab_subprocess_dtls b
    where a.id = b.mst_id and a.load_unload_id = 2 and b.load_unload_id=2 and b.entry_page=35 and a.status_active = 1 and b.status_active=1 ".where_con_using_array($batchIdsArr,0,'a.batch_id')." ");
    $batch_product_arr = array();
    foreach ($sql_prod_ref as $val) 
    {
        $batch_product_arr[$val[csf("batch_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
    }

    $prodCapacityFromArr = array();
    $prodCapacityToArr   = array();
    $prodCapacityDateArr = array();
    $batchIdChk          = array();
    $floorIdsArr         = array();
    $datefromChk         = array();
    $dateToChk           = array();
    $floorIdsChk         = array();
    
    foreach($batchdata as $row)
    {
        if($batchIdChk[$row[csf("batch_id")]]=='')
        {
            $batchIdChk[$row[csf("batch_id")]]=$row[csf("batch_id")];
            if($floorIdsChk[$row[csf("floor_id")]]=='')
            {
                $floorIdsChk[$row[csf("floor_id")]]=$row[csf("floor_id")];
                array_push($floorIdsArr,$row[csf("floor_id")]);
            }
          
            $prodCapacityDateArr[$row[csf("production_date")]]["production_date"]=$row[csf("production_date")];

            if($row[csf("type")]==1)
            {
                $prodCapacityFromArr[$row[csf("production_date")]]["floor_id"]=$row[csf("floor_id")];
                $prodCapacityFromArr[$row[csf("production_date")]]["production"] +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];
                $prodCapacityFromArr[$row[csf("production_date")]]["type"]=$row[csf("type")];

                if($datefromChk[$row[csf("production_date")]]=='')
                {
                    $datefromChk[$row[csf("production_date")]]=$row[csf("production_date")];
                    $prodCapacityFromArr[$row[csf("production_date")]]["prod_count"]++;
                }
            }
            else if($row[csf("type")]==2)
            {
                $prodCapacityToArr[$row[csf("production_date")]]["floor_id"]=$row[csf("floor_id")];
                $prodCapacityToArr[$row[csf("production_date")]]["production"] +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];
                $prodCapacityToArr[$row[csf("production_date")]]["type"]=$row[csf("type")];

                if($dateToChk[$row[csf("production_date")]]=='')
                {
                    $dateToChk[$row[csf("production_date")]]=$row[csf("production_date")];
                    $prodCapacityToArr[$row[csf("production_date")]]["prod_count"]++;
                }
            } 
        }
       
    }
    unset($batchdata);
   
    // echo "<pre>";
    // print_r($prodCapacityFromArr);

    // if($subcondata > 0 )
    // {
        $batchIdsubconChk = array();
        $floorIdsubconChk = array();
        foreach($subcondata as $row)
        {
            if($batchIdsubconChk[$row[csf("batch_id")]]=='')
            {
                $batchIdsubconChk[$row[csf("batch_id")]]=$row[csf("batch_id")];
                
                if($floorIdsubconChk[$row[csf("floor_id")]]=='')
                {
                    $floorIdsubconChk[$row[csf("floor_id")]]=$row[csf("floor_id")];
                    array_push($floorIdsArr,$row[csf("floor_id")]);
                }
               
                $prodCapacityDateArr[$row[csf("production_date")]]["production_date"]=$row[csf("production_date")];
    
                if($row[csf("type")]==1)
                {
                    $prodCapacityFromArr[$row[csf("production_date")]]["floor_id"]=$row[csf("floor_id")];
                    $prodCapacityFromArr[$row[csf("production_date")]]["production"] +=$row[csf("sub_batch_qnty")]+$row[csf("total_trims_weight")];
                    if($datefromChk[$row[csf("production_date")]]=='')
                    {
                        $datefromChk[$row[csf("production_date")]]=$row[csf("production_date")];
                        $prodCapacityFromArr[$row[csf("production_date")]]["prod_count"]++;
                    }
                }
                else if($row[csf("type")]==2)
                {
                    $prodCapacityToArr[$row[csf("production_date")]]["floor_id"]=$row[csf("floor_id")];
                    $prodCapacityToArr[$row[csf("production_date")]]["production"] +=$row[csf("sub_batch_qnty")]+$row[csf("total_trims_weight")];
                    if($dateToChk[$row[csf("production_date")]]=='')
                    {
                        $dateToChk[$row[csf("production_date")]]=$row[csf("production_date")];
                        $prodCapacityToArr[$row[csf("production_date")]]["prod_count"]++;
                    }
                } 
            }
           
        }
        unset($subcondata);
    //}
   

    $machine_result=sql_select("select id,prod_capacity,floor_id from lib_machine_name where status_active=1 and category_id = 2 ".where_con_using_array( $floorIdsArr,0,'floor_id')." order by seq_no ");
    $machine_capacity_arr = array();
    foreach($machine_result as $row)
    {
        $machine_capacity_arr[$row[csf('floor_id')]] +=$row[csf('prod_capacity')];
    }
   

    ob_start();
    ?>
    <style>
        .wrd_brk{word-break: break-all;word-wrap: break-word;}          
    </style>

    <fieldset style="width:980px;" >
  
        <table width="935" cellpadding="0" cellspacing="0"  rules="all" class="rpt_table" style="border:none;">
            <tr class="form_caption" style="border:none;">
                <td colspan="9" align="center" style="border:none;font-size:16px; font-weight:bold" >Dyeing Capacity Vs. Production Report</td>
            </tr>
            <tr style="border:none;">
                <td colspan="9" align="center" style="border:none; font-size:14px;">
                    Company Name : <? echo $companyArr[str_replace("'", "", $company)]; ?>
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="9" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
                </td>
            </tr>
        </table>
  
        <div style="width:100%; " align="center">
            
            <table width="935" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    
                    <tr>
                        <th width="100" rowspan="3">Date</th>
                        <th width="300" colspan="3"><? echo $floorArr[$cbo_floor_from];?></th>
                        <th width="300" colspan="3"><? echo $floorArr[$cbo_floor_to];?></th>
                        <th width="200" colspan="2">Difference</th>
                    </tr>
                    <tr>
                        <th width="100">Capacity</th>
                        <th width="100">Production</th>
                        <th width="100">Percentage</th>
                        <th width="100">Capacity</th>
                        <th width="100">Production</th>
                        <th width="100">Percentage</th>
                        <th width="100">Daily Difference</th>
                        <th width="100">Daily Difference</th>
                    </tr>
                    <tr>
                        <th width="100">Kg</th>
                        <th width="100">Kg</th>
                        <th width="100">%</th>
                        <th width="100">Kg</th>
                        <th width="100">Kg</th>
                        <th width="100">%</th>
                        <th width="100">Kg</th>
                        <th width="100">%</th>
                    </tr>
                </thead>
            </table>
            <!-- style="max-height:350px; overflow-y:auto; width:1667px;" -->
            <div>
            <table width="935" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <? 	$i=1;

                $g_tot_from_capacity = 0;
                $g_tot_from_batch_weight = 0;
                $g_tot_from_Percentage = 0;
                $g_tot_to_capacity = 0;
                $g_tot_to_batch_weight = 0;
                $g_tot_to_Percentage = 0;
                $g_tot_diff_prod = 0;
                $g_tot_diff_Percentage = 0;
                $from_prod_count = 0;
                $to_prod_count = 0;

                  asort($prodCapacityDateArr);
                   foreach($prodCapacityDateArr as $key => $val) 
                   {
                    if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                   
                  
                    ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                        
                            <td width="100" class="wrd_brk" align="center"><? echo change_date_format($val['production_date']);?>&nbsp;</td>
                            <td width="100" class="wrd_brk" align="right">
                                <?
                                $from_capacity = $machine_capacity_arr[$prodCapacityFromArr[$val["production_date"]]["floor_id"]];
                                echo number_format($from_capacity,2);
                                ?>&nbsp;
                            </td>
                            <td width="100" class="wrd_brk" align="right">
                                <?
                                $from_batch_weight = $prodCapacityFromArr[$val["production_date"]]["production"];
                                echo number_format($from_batch_weight,2);
                               
                                ?>&nbsp;
                            </td>
                            <td width="100" class="wrd_brk" align="right" title="(Production/Capacity)*100">
                                <?
                                if($from_capacity >0)
                                {
                                    $from_Percentage = ($from_batch_weight/ $from_capacity)*100;
                                   
                                }
                                else
                                {
                                    $from_Percentage = 0;
                                }
                                echo number_format($from_Percentage,2).'%';
                                ?> &nbsp;
                            </td>
                            <td width="100" class="wrd_brk" align="right">
                                <?
                                $to_capacity = $machine_capacity_arr[$prodCapacityToArr[$val["production_date"]]["floor_id"]];
                                echo number_format($to_capacity,2);
                                ?>&nbsp;
                            </td>
                            <td width="100" class="wrd_brk" align="right">
                                <?
                                $to_batch_weight = $prodCapacityToArr[$val["production_date"]]["production"];
                                echo number_format($to_batch_weight,2);
                             
                                ?>&nbsp;
                            </td>
                            <td width="100" class="wrd_brk" align="right" title="(Production/Capacity)*100">
                            <?
                                if($to_capacity >0)
                                {
                                    $to_Percentage = ($to_batch_weight/ $to_capacity)*100;
                                  
                                }
                                else
                                {
                                    $to_Percentage = 0;
                                }
                                echo number_format($to_Percentage,2).'%';
                                ?> &nbsp;
                            </td>
                            <td width="100" class="wrd_brk" align="right" title="(From Production- To Production)">
                                <?
                                $diff_prod = ($from_batch_weight-$to_batch_weight);
                                echo number_format($diff_prod,2);
                                ?> &nbsp;
                            </td>
                            <td width="100" class="wrd_brk" align="right" title="(From Percentage- To Percentage)">
                            <?
                                $diff_Percentage = ($from_Percentage-$to_Percentage);
                                echo number_format($diff_Percentage,2).'%';
                               
                                ?>&nbsp;
                            </td>
                            
                    
                    </tr>
                    <?
                    $i++;
                    $g_tot_from_capacity +=$from_capacity;
                    $g_tot_from_batch_weight +=$from_batch_weight;
                    $g_tot_to_capacity +=$to_capacity;
                    $g_tot_to_batch_weight +=$to_batch_weight;
                    $g_tot_diff_prod +=$diff_prod;

                    $from_prod_count += $prodCapacityFromArr[$val["production_date"]]["prod_count"];
                    $to_prod_count += $prodCapacityToArr[$val["production_date"]]["prod_count"];
                  
                     
                }?>

                    <tfoot>
                        <tr bgcolor="#a6acaf">
                            <th width="100" style="font-size:16px;text-align:center;font-weight:bold">Grand Total : </th>
                            <th width="100" align="right"><? echo number_format($g_tot_from_capacity,2);?>&nbsp;</th>
                            <th width="100" align="right"><? echo number_format($g_tot_from_batch_weight,2);?>&nbsp;</th>
                            <th width="100" align="right"><? 
                            $g_tot_from_Percentage = ($g_tot_from_batch_weight/$g_tot_from_capacity)*100;
                            echo number_format($g_tot_from_Percentage,2).'%';
                            ?>&nbsp;</th>
                            <th width="100" align="right"><? echo number_format($g_tot_to_capacity,2);?>&nbsp;</th>
                            <th width="100" align="right"><? echo number_format($g_tot_to_batch_weight,2);?>&nbsp;</th>
                            <th width="100" align="right"><? 
                            $g_tot_to_Percentage = ($g_tot_to_batch_weight/$g_tot_to_capacity)*100;
                            echo number_format($g_tot_to_Percentage,2).'%';?>&nbsp;</th>
                            <th width="100" align="right"><? echo number_format($g_tot_diff_prod,2);?>&nbsp;</th>
                            <th width="100" align="right"><? echo number_format($g_tot_from_Percentage-$g_tot_to_Percentage,2).'%';?>&nbsp;</th>
                        </tr>
                        <th width="100" style="font-size:16px;text-align:center;font-weight:bold">Avg./Day : </th>
                            <th width="100" align="right">&nbsp;</th>
                            <th width="100" align="right" title="<? echo 'Production Days : '.$from_prod_count;?>">
                                <?
                                if($from_prod_count >0)
                                {
                                    echo number_format($g_tot_from_batch_weight/$from_prod_count,2);
                                }
                                else
                                {
                                    echo "0.00";
                                }
                                
                                 ?>
                                &nbsp;</th>
                            <th width="100" align="right">&nbsp;</th>
                            <th width="100" align="right">&nbsp;</th>
                            <th width="100" align="right" title="<? echo 'Production Days : '.$to_prod_count;?>">
                                <? 
                                if($to_prod_count >0)
                                {
                                    echo number_format($g_tot_to_batch_weight/$to_prod_count,2);
                                }
                                else
                                {
                                    echo "0.00";
                                }
                               
                                ?>&nbsp;</th>
                            <th width="100" align="right">&nbsp;</th>
                            <th width="100" align="right">&nbsp;</th>
                            <th width="100" align="right">&nbsp;</th>
                        </tr>
                    </tfoot>
            </table>
            </div>
        </div>
    </fieldset>

    <?
    foreach (glob("$user_id*.xls") as $filename)
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    $filename=$user_id."_".$name.".xls";
    echo "$total_data####$filename";
    exit();
}
