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
| for load_drop_down_floor
|------------------------------------------------------------------------
*/
if ($action == "load_drop_down_floor")
{
	echo create_drop_down("cbo_floor", 100, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=2 and b.company_id=$data and b.status_active=1 and b.is_deleted=0 group by a.id, a.floor_name order by a.floor_name", "id,floor_name", 1, "-- Select Floor --", 0, "", "");
	exit();
}

if ($action == "eval_multi_select")
{
	echo "set_multiselect('cbo_floor','0','0','','0');\n";
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
    $year               = str_replace("'","",$cbo_year);
	$month_to           = str_replace("'","",$cbo_to_month);
    $cbo_floor          = str_replace("'","",$cbo_floor);

    $num_days = cal_days_in_month(CAL_GREGORIAN, $month_to, $year);
	$end_date=$year."-".$month_to."-$num_days";

    if ($month_to>9) 
    {
        //echo $month_to.'=string';die;
        $end_date2=$year."-".$month_to."-01";
    }
    else {
        //echo $month_to.'=string2';die;
        $end_date2=$year."-0".$month_to."-01";
    }

    //$end_date2=$year."-0".$month_to."-01";
    $start_date1 = date('Y-m-d', strtotime($end_date2.'-1 month'));
    $start_date2 = date('Y-m-d', strtotime($end_date2.'-2 month'));
    //echo $start_date2 ."=".$start_date1 = date('Y-m-d', strtotime($end_date2.'-1 month')).'='.$end_date;

    $end_date22=explode('-',$end_date2);
    $start_date11=explode('-',$start_date1);
    $start_date22=explode('-',$start_date2);

    if ($cbo_floor==0 || $cbo_floor=='')
	{
		$floor_id_cond="";
	}
    else 
    {
        $floor_id_cond=" and f.floor_id in($cbo_floor)";
    }
 
    

    if ($company==0 || $company=="") $companyCond=""; else $companyCond="  and a.company_id in($company)";
    if ($cbo_within_group==0 || $cbo_within_group=="") $withinGroupCond=""; else $withinGroupCond="  and d.within_group in($cbo_within_group)";
    if ($cbo_within_group==0 || $cbo_within_group=="") $knitting_withinGroupCond=""; else $knitting_withinGroupCond="  and e.within_group in($cbo_within_group)";
    if ($cbo_within_group==0 || $cbo_within_group=="") $fdg_withinGroupCond=""; else $fdg_withinGroupCond="  and c.within_group in($cbo_within_group)";

    // if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	if($db_type==0) 
	{
		$date_cond=" and f.process_end_date between '$start_date2' and '$end_date'";
	
	}
	if($db_type==2) 
	{
		
		$date_cond=" and f.process_end_date between '".date("j-M-Y",strtotime($start_date2))."' and '".date("j-M-Y",strtotime($end_date))."'";
	
       
	}
	// echo $date_cond;die;

    

    $companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $floorArr = return_library_array("select id,floor_name from lib_prod_floor", "id", "floor_name");

    // ==================== Dyeing Production CB Report Start ===================== 
   
    $sql = "SELECT a.id as batch_id, a.total_trims_weight, a.batch_weight, f.process_end_date as production_date,d.within_group,f.floor_id
    from pro_batch_create_dtls b, fabric_sales_order_mst d, pro_fab_subprocess f, pro_batch_create_mst a 
    where f.batch_id=a.id and a.id=b.mst_id and f.batch_id=b.mst_id and b.po_id=d.id 
    $companyCond $date_cond $withinGroupCond $floor_id_cond
    and a.entry_form=0   and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=1 and b.is_sales=1 order by f.process_end_date ";
   
    
    if($cbo_inbound_subcon==1)
    {
        $sql_subcon="SELECT a.id as batch_id, a.total_trims_weight, a.batch_weight, b.batch_qnty AS sub_batch_qnty, f.process_end_date as production_date, null as within_group, f.floor_id
        from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f
        where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond $date_cond $floor_id_cond and a.entry_form=36 and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 ";
       
    }

    //echo $sql_subcon;
   
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

    // echo "<pre>";
    // print_r($batch_product_arr);

    $prodCapacityFromArr  = array();
    $prodCapacityToArr    = array();
    $batchIdChk           = array();
    $prodCapacityToSumArr = array();
    $prodCapacityFromSumArr1 = array();
    $prodCapacityFromSumArr2 = array();
    $dyeing_to_qty       = 0;
    $dyeing_frm_qty      = 0;
    foreach($batchdata as $row)
    {
        if($batchIdChk[$row[csf("batch_id")]]=='')
        {
            $batchIdChk[$row[csf("batch_id")]]=$row[csf("batch_id")];

            if(strtotime($end_date2) <= strtotime($row[csf("production_date")]))
            {
              
                $prod_data = explode('-',$row[csf("production_date")]);
                $prodCapacityToArr[$row[csf("floor_id")]][$prod_data[0]]["production"] +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];
                //$prodCapacityToArr[$row[csf("floor_id")]][$row[csf("production_date")]]["production"] +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];
                $prodCapacityToSumArr[$row[csf("floor_id")]] +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];
                
            }
            else  if(strtotime($start_date1) <= strtotime($row[csf("production_date")]))
            {
                $prod_data = explode('-',$row[csf("production_date")]);
                $prodCapacityFromArr1[$row[csf("floor_id")]][$prod_data[0]]["production"] +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];
                //$prodCapacityFromArr1[$row[csf("floor_id")]][$row[csf("production_date")]]["production"] +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];
                $prodCapacityFromSumArr1[$row[csf("floor_id")]] += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];

            }
            else 
            {
                $prod_data = explode('-',$row[csf("production_date")]);
                $prodCapacityFromArr2[$row[csf("floor_id")]][$prod_data[0]]["production"] +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];
                //$prodCapacityFromArr2[$row[csf("floor_id")]][$row[csf("production_date")]]["production"] +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];
                $prodCapacityFromSumArr2[$row[csf("floor_id")]] += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];

            } 
        }
       
    }
    unset($batchdata);
   
    //echo "<pre>";print_r($prodCapacityFromArr1);

    if($subcondata > 0 )
    {
        $batchIdsubconChk = array();
        $floorIdsubconChk = array();
        foreach($subcondata as $row)
        {
            if($batchIdsubconChk[$row[csf("batch_id")]]=='')
            {
                $batchIdsubconChk[$row[csf("batch_id")]]=$row[csf("batch_id")];
    
                if(strtotime($end_date2) <= strtotime($row[csf("production_date")]))
                {
                    $prod_data = explode('-',$row[csf("production_date")]);
                    $prodCapacityToArr[$row[csf("floor_id")]][$prod_data[0]]["production"] += $row[csf("sub_batch_qnty")]+$row[csf("total_trims_weight")];
                    $prodCapacityToSumArr[$row[csf("floor_id")]] += $row[csf("sub_batch_qnty")]+$row[csf("total_trims_weight")];
                }
                else if(strtotime($start_date1) <= strtotime($row[csf("production_date")]))
                {
                    $prod_data = explode('-',$row[csf("production_date")]);
                    $prodCapacityFromArr1[$row[csf("floor_id")]][$prod_data[0]]["production"] +=$row[csf("sub_batch_qnty")]+$row[csf("total_trims_weight")];
                    $prodCapacityFromSumArr1[$row[csf("floor_id")]] +=$row[csf("sub_batch_qnty")]+$row[csf("total_trims_weight")];
                }
                else 
                {
                    $prod_data = explode('-',$row[csf("production_date")]);
                    $prodCapacityFromArr2[$row[csf("floor_id")]][$prod_data[0]]["production"] +=$row[csf("sub_batch_qnty")]+$row[csf("total_trims_weight")];
                    $prodCapacityFromSumArr2[$row[csf("floor_id")]] +=$row[csf("sub_batch_qnty")]+$row[csf("total_trims_weight")];
                  
                } 
            }
           
        }
        unset($subcondata);
    }



    //------------------------------------------------------------------------------------------------

    //$floorArr = array_unique(explode(",", $cbo_floor ));
    $floorDataArr=array_filter(array_unique(explode(",",$cbo_floor)));
    asort($floorDataArr);

    $dayArr = array(1=>'01',2=>'02',3=>'03',4=>'04',5=>'05',6=>'06',7=>'07',8=>'08',9=>'09',10=>'10',11=>'11',12=>'12',13=>'13',14=>'14',15=>'15',16=>'16',17=>'17',18=>'18',19=>'19',20=>'20',21=>'21',22=>'22',23=>'23',24=>'24',25=>'25',26=>'26',27=>'27',28=>'28',29=>'29',30=>'30',31=>'31');
    
    ob_start();
    ?>
    <style>
        .wrd_brk{word-break: break-all;word-wrap: break-word;}          
    </style>

    <fieldset style="width:1400px;" >
  
        <table width="1400" cellpadding="0" cellspacing="0"  rules="all" class="rpt_table" style="border:none;">
            <tr class="form_caption" style="border:none;">
                <td colspan="14" align="center" style="border:none;font-size:20px ; font-weight:bold" ><? echo $report_title;?></td>
            </tr>
            <tr style="border:none;">
                <td colspan="14" align="center" style="border:none; font-size:18px ;">
                    Company Name : <? echo $companyArr[str_replace("'", "", $company)]; ?>
                </td>
            </tr>
        </table>
      
        <!-- ==================== Dyeing Production Start ( Summery ) =====================  -->

        <div style="width:100%; " align="center">
            <table width="590" cellpadding="0" cellspacing="0" border="1" rules="all"  class="rpt_table">
                <thead>
                    <tr> <td colspan="14" >&nbsp;</td></tr>
                    <tr> <th colspan="14" style="font: 16px tahoma;"> Dyeing Production CB Report Summary </th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <table width="590" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left" class="rpt_table">
                    
                                <thead>
                                    <tr><th colspan="7" ><? echo $months[ltrim($end_date22[1],0)].'( '.$end_date22[0].' )'.' -- '.$months[ltrim($start_date11[1],0)].'( '.$start_date11[0].' )';?> Difference</th></tr>
                                    <tr>
                                        <th width="30" >Sl</th>
                                        <th width="170" >Floor</th>
                                        <th width="130" >
                                            <?
                                            echo $months[ltrim($end_date22[1],0)].'-'.$end_date22[0];
                                            
                                            ?>
                                        </th>
                                        <th width="130" >
                                            <? 
                                            echo $months[ltrim($start_date11[1],0)].'-'.$start_date11[0];
                                            
                                            ?>
                                        </th>
                                        <th width="130" >Difference</th>
                                    </tr>
                                </thead>
                            </table>
                            <div>
                            <table width="590" cellpadding="0" cellspacing="0" border="1"  align="left" rules="all" class="rpt_table">
                                
                                    <?
                                    $k=1;
                                    $tot_prodCapacityToSum1 = 0;
                                    $tot_prodCapacityFromSum1 = 0;
                                    $tot_difToFromSum1 = 0;
                                    foreach ($floorDataArr as $floorId) 
                                    { 
                                        if ($k%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                        ?>
                                        <tr bgcolor="<? echo $bgcolor;?>" id="tr1<? echo $k;?>">
                                            <td width="30" class="wrd_brk" align="center"><? echo $k;?>&nbsp;</td>
                                            <td width="170" class="wrd_brk" ><? echo $floorArr[$floorId];;?>&nbsp;</td>
                                            <td width="130" class="wrd_brk" align="right"><? echo number_format($prodCapacityToSumArr[$floorId],2);?>&nbsp;</td>
                                            <td width="130" class="wrd_brk" align="right"><? echo number_format($prodCapacityFromSumArr1[$floorId],2);?>&nbsp;</td>
                                            <td width="130" class="wrd_brk" align="right"><? echo number_format($prodCapacityToSumArr[$floorId]-$prodCapacityFromSumArr1[$floorId],2);?>&nbsp;</td>
                                        </tr>
                                    <? 
                                    $k++;
                                    $tot_prodCapacityToSum1 +=$prodCapacityToSumArr[$floorId];
                                    $tot_prodCapacityFromSum1 +=$prodCapacityFromSumArr1[$floorId];
                                    $tot_difToFromSum1 +=$prodCapacityToSumArr[$floorId]-$prodCapacityFromSumArr1[$floorId];
                                } ?>
                                <tfoot>
                                    <tr bgcolor="#a6acaf">
                                        <th width="200" colspan="2">Total : </th>
                                        <th width="130" align="right"><? echo number_format($tot_prodCapacityToSum1,2);?>&nbsp;</th>
                                        <th width="130" align="right"><? echo number_format($tot_prodCapacityFromSum1,2);?>&nbsp;</th>
                                        <th width="130" align="right"><? echo number_format($tot_difToFromSum1,2);?>&nbsp;</th>
                                    </tr>
                                    
                                </tfoot>
                            </table>
                            </div>
                        </td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td>
                            <table width="590" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left" class="rpt_table">
                    
                                <thead>
                                    <tr><th colspan="7" ><? echo $months[ltrim($start_date11[1],0)].'( '.$start_date11[0].' )'.' -- '.$months[ltrim($start_date22[1],0)].'( '.$start_date22[0].' )';?> Difference</th></tr>
                                    <tr>
                                        <th width="30" >Sl</th>
                                        <th width="170" ">Floor</th>
                                        <th width="130" >
                                            <?
                                            echo $months[ltrim($start_date11[1],0)].'-'.$start_date11[0];
                                            
                                            ?>
                                        </th>
                                        <th width="130" >
                                            <? 
                                            echo $months[ltrim($start_date22[1],0)].'-'.$start_date22[0];
                                            
                                            ?>
                                        </th>
                                        <th width="130" >Difference</th>
                                    </tr>
                                </thead>
                            </table>
                            <div>
                            <table width="590" cellpadding="0" cellspacing="0" border="1"  align="left" rules="all" class="rpt_table">
                                
                                    <?
                                    $lk=1;
                                    $tot_prodCapacityToSum2 = 0;
                                    $tot_prodCapacityFromSum2 = 0;
                                    $tot_difToFromSum2 = 0;
                                    foreach ($floorDataArr as $floorId) 
                                    { 
                                        if ($lk%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                        ?>
                                        <tr bgcolor="<? echo $bgcolor;?>"  id="tr2<? echo $lk;?>">
                                            <td width="30" class="wrd_brk" align="center"><? echo $lk;?>&nbsp;</td>
                                            <td width="170" class="wrd_brk" ><? echo $floorArr[$floorId];;?>&nbsp;</td>
                                            <td width="130" class="wrd_brk" align="right"><? echo number_format($prodCapacityFromSumArr1[$floorId],2);?>&nbsp;</td>
                                            <td width="130" class="wrd_brk" align="right"><? echo number_format($prodCapacityFromSumArr2[$floorId],2);?>&nbsp;</td>
                                            <td width="130" class="wrd_brk" align="right"><? echo number_format($prodCapacityFromSumArr1[$floorId]-$prodCapacityFromSumArr2[$floorId],2);?>&nbsp;</td>
                                        </tr>
                                    <? 
                                    $lk++;
                                    $tot_prodCapacityToSum2 +=$prodCapacityFromSumArr1[$floorId];
                                    $tot_prodCapacityFromSum2 +=$prodCapacityFromSumArr2[$floorId];
                                    $tot_difToFromSum2 +=$prodCapacityFromSumArr1[$floorId]-$prodCapacityFromSumArr2[$floorId];
                                } ?>
                                <tfoot>
                                    <tr bgcolor="#a6acaf">
                                        <th width="200" colspan="2">Total : </th>
                                        <th width="130" align="right"><? echo number_format($tot_prodCapacityToSum2,2);?>&nbsp;</th>
                                        <th width="130" align="right"><? echo number_format($tot_prodCapacityFromSum2,2);?>&nbsp;</th>
                                        <th width="130" align="right"><? echo number_format($tot_difToFromSum2,2);?>&nbsp;</th>
                                    </tr>
                                    
                                </tfoot>
                            </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ==================== Dyeing Production Start (Details) =====================  -->

  
        <div style="width:100%; " align="center">
          
        <table width="1400" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left" class="rpt_table">

        <?
        
        foreach ($floorDataArr as $floorId) 
        {
            //var_dump($floorId);
            if(!in_array($floorId,$floorIdChkArr))
            {
                $floorIdChkArr[]=$floorId;
                ?>
        
                    <thead>
                    <tr> <td colspan="14" >&nbsp;</td></tr>
                        <tr> <th colspan="14" style="font: 16px tahoma;"> Dyeing Production CB Report ( <? echo $floorArr[$floorId];?> )</th></tr>
                    </thead>
                <? 
            }?>
                <tr>
                    <td>
                        <table width="700" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left" class="rpt_table">
                
                            <thead>
                        
                                <tr>
                                    <th width="100" rowspan="3">Day</th>
                                    <th width="200" colspan="2">
                                        <? echo $months[ltrim($end_date22[1],0)].'-'.$end_date22[0];?>
                                    </th>
                                    <th width="200" colspan="2">
                                        <? echo $months[ltrim($start_date11[1],0)].'-'.$start_date11[0];?>
                                    </th>
                                    <th width="200" colspan="2">Difference from Last Month</th>
                                </tr>
                                <tr>
                                    <th width="100">Production</th>
                                    <th width="100">CB</th>
                                    <th width="100">Production</th>
                                    <th width="100">CB</th>
                                    <th width="100">Production</th>
                                    <th width="100">CB</th>
                                </tr>
                                <tr>
                                    <th width="100">Kg</th>
                                    <th width="100">Kg</th>
                                    <th width="100">Kg</th>
                                    <th width="100">Kg</th>
                                    <th width="100">Kg</th>
                                    <th width="100">Kg</th>
                                </tr>
                            </thead>
                        </table>
                        
                        <!-- style="max-height:350px; overflow-y:auto; width:1667px;" -->
                        <div>
                            <table width="700" cellpadding="0" cellspacing="0" border="1"  align="left" rules="all" class="rpt_table">
                        
                            <? 	$i=1;
                                $prev_to_qty =0;
                                $prev_frm_qty =0;
                                $frm_total_qty =0;
                                $prod_frm_total_qty =0;
                                $prod_to_total_qty =0;
                                $cbo_frm_total_qty =0;
                                $cbo_to_total_qty =0;
                                $total_diff_prod_qty =0;
                                $total_diff_cb_qty =0;
                                $diff_prod =0;
                                foreach ($dayArr as $key => $val) 
                                {
                                    if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                ?>

                                <tr bgcolor="<? echo $bgcolor;?>"  id="tr<? echo $i;?>">
                                    
                                    <td width="100" class="wrd_brk" align="center"><? echo $val;?>&nbsp;</td>
                                    <td width="100" class="wrd_brk" align="right">
                                    <? echo number_format($prodCapacityToArr[$floorId][$val]["production"],2);$prev_to_qty +=$prodCapacityToArr[$floorId][$val]["production"];?> &nbsp;
                                    </td>
                                    <td width="100" class="wrd_brk" align="right">
                                    <? 
                                    if($prodCapacityToArr[$floorId][$val]["production"] >0 )
                                    {
                                        $cb_to_qty = $prev_to_qty;
                                    }
                                    else
                                    {
                                        $cb_to_qty = 0;
                                    }
                                    
                                    echo number_format($cb_to_qty,2);
                                    ?>&nbsp;
                                    </td>
                                    <td width="100" class="wrd_brk" align="right" >
                                    <? echo number_format($prodCapacityFromArr1[$floorId][$val]["production"],2); $prev_frm_qty +=$prodCapacityFromArr1[$floorId][$val]["production"]; ?>&nbsp;
                                    </td>
                                    <td width="100" class="wrd_brk" align="right">
                                    <? 
                                    if($prodCapacityFromArr1[$floorId][$val]["production"] >0)
                                    {
                                        $cb_from_qty = $prev_frm_qty; 
                                    
                                    }
                                    else
                                    {
                                        $cb_from_qty = 0;
                                    }
                                    echo number_format($cb_from_qty,2);
                                    ?>&nbsp;
                                
                                    </td>
                                    <td width="100" class="wrd_brk" align="right">
                                    <? 
                                    $diff_prod = $prodCapacityToArr[$floorId][$val]["production"]-$prodCapacityFromArr1[$floorId][$val]["production"];
                                    echo number_format($diff_prod,2);?>&nbsp;
                                    </td>
                                    <td width="100" class="wrd_brk" align="right" >
                                    <? 
                                    $diff_cb =$cb_to_qty-$cb_from_qty;
                                    echo number_format($diff_cb,2);?>&nbsp;
                                    </td>
                                    
                                </tr>
                                <?
                                $i++;
                                $prod_frm_total_qty +=$prodCapacityFromArr1[$floorId][$val]["production"];
                                $prod_to_total_qty +=$prodCapacityToArr[$floorId][$val]["production"];
                                $cbo_frm_total_qty +=$cb_from_qty;
                                $cbo_to_total_qty +=$cb_to_qty;
                                $total_diff_prod_qty +=$diff_prod;
                                $total_diff_cb_qty +=$diff_cb;
                                }
                                ?>
                                <tfoot>
                                    <tr bgcolor="#a6acaf">
                                        <th width="100" >Total : </th>
                                        <th width="100" align="right"><? echo number_format($prod_to_total_qty,2);?>&nbsp;</th>
                                        <th width="100" align="right"><? echo number_format($cbo_to_total_qty,2);?>&nbsp;</th>
                                        <th width="100" align="right"><? echo number_format($prod_frm_total_qty,2);?>&nbsp;</th>
                                        <th width="100" align="right"><? echo number_format($cbo_frm_total_qty,2);?>&nbsp;</th>
                                        <th width="100" align="right"><? echo number_format($total_diff_prod_qty,2);?>&nbsp;</th>
                                        <th width="100" align="right"><? echo number_format($total_diff_cb_qty,2);?>&nbsp;</th>
                                    </tr>
                                    
                                </tfoot>
                            </table>
                        </div>
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>
                        <table width="700" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left" class="rpt_table">
            
                            <thead>
                        
                                <tr>
                                    <th width="100" rowspan="3">Day</th>
                                    <th width="200" colspan="2">
                                        <?
                                            echo $months[ltrim($start_date11[1],0)].'-'.$start_date11[0];
                                        ?>
                                    </th>
                                    <th width="200" colspan="2">
                                        <? 
                                        echo $months[ltrim($start_date22[1],0)].'-'.$start_date22[0];
                                        
                                        ?>
                                    </th>
                                    <th width="200" colspan="2">Difference from Last Month</th>
                                </tr>
                                <tr>
                                    <th width="100">Production</th>
                                    <th width="100">CB</th>
                                    <th width="100">Production</th>
                                    <th width="100">CB</th>
                                    <th width="100">Production</th>
                                    <th width="100">CB</th>
                                </tr>
                                <tr>
                                    <th width="100">Kg</th>
                                    <th width="100">Kg</th>
                                    <th width="100">Kg</th>
                                    <th width="100">Kg</th>
                                    <th width="100">Kg</th>
                                    <th width="100">Kg</th>
                                </tr>
                            </thead>
                        </table>

                        <!-- style="max-height:350px; overflow-y:auto; width:1667px;" -->
                        <div>
                            <table width="700" cellpadding="0" cellspacing="0" border="1"  align="left" rules="all" class="rpt_table">
                        
                            <? 	$i=1;
                                $prev_to_qty =0;
                                $prev_frm_qty =0;
                                $frm_total_qty =0;
                                $prod_frm_total_qty =0;
                                $prod_to_total_qty =0;
                                $cbo_frm_total_qty =0;
                                $cbo_to_total_qty =0;
                                $total_diff_prod_qty =0;
                                $total_diff_cb_qty =0;
                                $diff_prod =0;
                                foreach ($dayArr as $key => $val) 
                                {
                                    if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                ?>

                                <tr bgcolor="<? echo $bgcolor;?>"  id="tr<? echo $i;?>">
                                    
                                    <td width="100" class="wrd_brk" align="center"><? echo $val;?>&nbsp;</td>
                                    <td width="100" class="wrd_brk" align="right">
                                    <? echo number_format($prodCapacityFromArr1[$floorId][$val]["production"],2);$prev_to_qty +=$prodCapacityFromArr1[$floorId][$val]["production"];?> &nbsp;
                                    </td>
                                    <td width="100" class="wrd_brk" align="right">
                                    <? 
                                    if($prodCapacityFromArr1[$floorId][$val]["production"] >0 )
                                    {
                                        $cb_to_qty = $prev_to_qty;
                                    }
                                    else
                                    {
                                        $cb_to_qty = 0;
                                    }
                                    
                                    echo number_format($cb_to_qty,2);
                                    ?>&nbsp;
                                    </td>
                                    <td width="100" class="wrd_brk" align="right" >
                                    <? echo number_format($prodCapacityFromArr2[$floorId][$val]["production"],2); $prev_frm_qty +=$prodCapacityFromArr2[$floorId][$val]["production"]; ?>&nbsp;
                                    </td>
                                    <td width="100" class="wrd_brk" align="right">
                                    <? 
                                    if($prodCapacityFromArr2[$floorId][$val]["production"] >0)
                                    {
                                        $cb_from_qty = $prev_frm_qty; 
                                    
                                    }
                                    else
                                    {
                                        $cb_from_qty = 0;
                                    }
                                    echo number_format($cb_from_qty,2);
                                    ?>&nbsp;
                                
                                    </td>
                                    <td width="100" class="wrd_brk" align="right">
                                    <? 
                                    $diff_prod = $prodCapacityFromArr1[$floorId][$val]["production"]-$prodCapacityFromArr2[$floorId][$val]["production"];
                                    echo number_format($diff_prod,2);?>&nbsp;
                                    </td>
                                    <td width="100" class="wrd_brk" align="right" >
                                    <? 
                                    $diff_cb =$cb_to_qty-$cb_from_qty;
                                    echo number_format($diff_cb,2);?>&nbsp;
                                    </td>
                                    
                                </tr>
                                <?
                                $i++;
                                $prod_frm_total_qty +=$prodCapacityFromArr2[$floorId][$val]["production"];
                                $prod_to_total_qty +=$prodCapacityFromArr1[$floorId][$val]["production"];
                                $cbo_frm_total_qty +=$cb_from_qty;
                                $cbo_to_total_qty +=$cb_to_qty;
                                $total_diff_prod_qty +=$diff_prod;
                                $total_diff_cb_qty +=$diff_cb;
                                }
                                ?>
                                <tfoot>
                                    <tr bgcolor="#a6acaf">
                                        <th width="100" >Total : </th>
                                        <th width="100" align="right"><? echo number_format($prod_to_total_qty,2);?>&nbsp;</th>
                                        <th width="100" align="right"><? echo number_format($cbo_to_total_qty,2);?>&nbsp;</th>
                                        <th width="100" align="right"><? echo number_format($prod_frm_total_qty,2);?>&nbsp;</th>
                                        <th width="100" align="right"><? echo number_format($cbo_frm_total_qty,2);?>&nbsp;</th>
                                        <th width="100" align="right"><? echo number_format($total_diff_prod_qty,2);?>&nbsp;</th>
                                        <th width="100" align="right"><? echo number_format($total_diff_cb_qty,2);?>&nbsp;</th>
                                    </tr>
                                    
                                </tfoot>
                            </table>
                        </div>
                    </td>
                   
                </tr>
               
            <? 
           
        } ?>    
            
            
        </table>
           

          

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