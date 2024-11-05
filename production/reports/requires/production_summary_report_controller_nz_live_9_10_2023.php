<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
if (!function_exists('pre'))
{
  function pre($array)
  {
    echo "<pre>";
    print_r($array);
    echo "</pre>";
  }
}
if (!function_exists('num_format'))
{
  function is_num($num)
  {
    return (is_infinite($num) || is_nan($num)) ? 0 : $num;
  }
}
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_location")
{
  extract($_REQUEST);
  echo create_drop_down( "cbo_location_id", 130, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in( $choosenCompany) group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );
  exit();
}

if ($action=="load_drop_down_floor")
{
  extract($_REQUEST);
  echo create_drop_down( "cbo_floor_id", 130, "SELECT id,floor_name from lib_prod_floor where location_id in( $choosenLocation ) and status_active =1 and is_deleted=0 and production_process in (4,5) group by id,floor_name order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "" );
  exit();
  // production_process in(1,4,5,8,9,10,11,13)
}

if ($action=='report_generate')
{
  $process = array(&$_POST);
  extract(check_magic_quote_gpc($process));

  $company_id = str_replace("'","",$cbo_company_id);
  $location_id= str_replace("'","",$cbo_location_id);
  $floor_id   = str_replace("'","",$cbo_floor_id);
  $form_date  = str_replace("'","",$txt_date_from);
  $to_date    = str_replace("'","",$txt_date_to);

  // ============================================================================================================
  //												Library
  // ============================================================================================================
  $company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
  $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
  $floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );

  if ($type==1) //Show
  {

      // =========================================================================================================
                                                //PRODUCTION RESOURCE DATA
      // =========================================================================================================
      $res_cond_sql ="";
      $res_cond_sql .= $company_id  ? " and a.company_id in ($company_id)" : "";
      $res_cond_sql .= $location_id ? " and a.location_id in ($location_id)"  : "";
      $res_cond_sql .= $floor_id    ? " and a.floor_id in ($floor_id)"     : "";
      $res_cond_sql .= ($form_date && $to_date) ?" and b.pr_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : "";
      $resource_sql="select a.company_id,a.floor_id,b.target_per_hour as target,b.working_hour from prod_resource_mst a, prod_resource_dtls b,lib_prod_floor c where a.id=b.mst_id and c.id = a.floor_id and a.is_deleted=0 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $res_cond_sql order by c.floor_serial_no asc";
      $resource_sql_res = sql_select($resource_sql);
      if (count($resource_sql_res) == 0 ) {
        echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Production Resource Data Not Found ** </h1>" ;
        die();
      }
      $prod_arr = array();
      foreach ($resource_sql_res as $v)
      {
        $prod_arr[$v['COMPANY_ID']][$v['FLOOR_ID']]['TARGET'] += ($v['TARGET'] * $v['WORKING_HOUR']);
      }

      // pre($prod_arr); die;

      // =========================================================================================================
                                                //PRODUCTION DATA
      // =========================================================================================================
      $cond_sql = '';
      $floor_cod = '';
      $cond_sql .= $company_id  ? " and a.serving_company in ($company_id)" : "";
      $cond_sql .= $location_id ? " and a.location in ($location_id)"  : "";
      $floor_cod .= $floor_id    ? " and a.floor_id in ($floor_id)"     : "";
      $cond_sql .= ($form_date && $to_date) ?" and a.production_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : "";

      $prod_sql = "select c.po_break_down_id as po_id,a.serving_company as company,a.floor_id,b.production_type as prod_type,
      case when  b.production_type=5 $floor_cod then b.production_qnty else 0 end as sew_out,
      case when  b.production_type=1 then b.production_qnty else 0 end as cut_qty,c.order_quantity as po_qty,c.order_total,c.order_rate as unit_price from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c,lib_prod_floor d where a.id = b.mst_id and a.po_break_down_id = c.po_break_down_id and c.id = b.color_size_break_down_id and d.id = a.floor_id and b.production_qnty > 0 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and b.production_type in (1, 5) $cond_sql order by d.floor_serial_no asc";

      // echo $prod_sql; die;
      $prod_sql_res = sql_select($prod_sql);


      $company_arr  = array();
      // $line_id_arr  = array();

      $company_wise_prod_arr = array();
      $cutting_arr = array();
      $po_id_arr = array();
      $floor_wise_po_arr = array();
      foreach ($prod_sql_res as $v)
      {
        if ($v['PROD_TYPE'] == 5 & $v['SEW_OUT'] > 0)
        {
          $po_id_arr[$v['PO_ID']] = $v['PO_ID'];   
          $company_arr[$v['COMPANY']] = $company_short_library[$v['COMPANY']];
          $prod_arr[$v['COMPANY']][$v['FLOOR_ID']]['PROD_QTY'] += $v['SEW_OUT'];
          $prod_arr[$v['COMPANY']][$v['FLOOR_ID']]['ACHIVED_VAL'] += ($v['SEW_OUT'] * $v['UNIT_PRICE']); 
          $floor_wise_po_arr[$v['COMPANY']][$v['FLOOR_ID']] [$v['PO_ID']] = $v['PO_ID']; 
        }
        if ($v['PROD_TYPE'] == 1)
        {
          $cutting_arr[$v['COMPANY']]['CUTTING_QTY'] += $v['CUT_QTY'];
        }

      }
      // pre($prod_arr); die;
       //=================================== CLEAR TEMP ENGINE ====================================
      $con = connect();
      execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 104 and ref_from in(1)");
    	oci_commit($con);

      //=================================== INSERT LINE ID INTO TEMP ENGINE ====================================
      fnc_tempengine("gbl_temp_engine", $user_id, 104, 1,$po_id_arr, $empty_arr);
     
      $floor_cod2 .= $floor_id    ? " and b.floor_id in ($floor_id)"     : "";
      $po_name_arr = return_library_array( "select a.id,a.po_number from wo_po_break_down a,gbl_temp_engine tmp where a.id=tmp.ref_val and tmp.user_id=$user_id and tmp.entry_form=104 and tmp.ref_from=1 and a.status_active=1 and a.is_deleted=0 ", "id", "po_number"  ); 
      
      // pre($po_name_arr); die;
      // $po_qty_sql = "select a.id as po_id,a.po_quantity,c.order_quantity,c.order_total,a.po_total_price,b.serving_company as company,b.floor_id,b.production_type from wo_po_break_down a,pro_garments_production_mst b,wo_po_color_size_breakdown c,gbl_temp_engine tmp where a.id=b.po_break_down_id and a.id=c.po_break_down_id and a.id=tmp.ref_val and b.production_type=5  $floor_cod2 and tmp.user_id=$user_id and tmp.entry_form=104 and tmp.ref_from=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
    /*   $po_qty_sql = "select b.po_break_down_id as po_id,c.item_number_id as item, c.order_quantity,c.order_total,b.serving_company as company,b.floor_id,b.production_type from pro_garments_production_mst b,wo_po_color_size_breakdown c,gbl_temp_engine tmp where b.po_break_down_id=c.po_break_down_id and b.po_break_down_id=tmp.ref_val and b.production_type=5  $floor_cod2 and tmp.user_id=$user_id and tmp.entry_form=104 and tmp.ref_from=1  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
      echo $po_qty_sql;die;
      $po_qty_sql_res = sql_select($po_qty_sql); 
      foreach ($po_qty_sql_res as  $v) 
      {
        $prod_arr [$v['COMPANY']][$v['FLOOR_ID']] ['PO_QTY'] += $v['ORDER_QUANTITY'];
        $prod_arr [$v['COMPANY']][$v['FLOOR_ID']] ['ORDER_TOTAL'] += $v['ORDER_TOTAL']; 
        $test_arr[$po_name_arr[$v['PO_ID']]][ $garments_item[$v['ITEM']]] ['PO_QTY'] += $v['ORDER_QUANTITY'];
        $test_arr[$po_name_arr[$v['PO_ID']]][ $garments_item[$v['ITEM']]] ['ORDER_TOTAL'] += $v['ORDER_TOTAL'];
      } */ 
      $po_qty_sql = "select c.po_break_down_id as po_id,sum(c.order_quantity) as po_qty,sum(c.order_total) as po_price  from wo_po_color_size_breakdown c,gbl_temp_engine tmp where  c.po_break_down_id=tmp.ref_val and tmp.user_id=$user_id and tmp.entry_form=104 and tmp.ref_from=1 and c.status_active=1 and c.is_deleted=0 group by c.po_break_down_id";
      // echo $po_qty_sql;die;
      $po_qty_sql_res = sql_select($po_qty_sql); 
      foreach ($po_qty_sql_res as  $v) 
      {
        $po_wise_data_arr [$v['PO_ID']]['PO_QTY'] += $v['PO_QTY'];
        $po_wise_data_arr [$v['PO_ID']]['PO_PRICE'] += $v['PO_PRICE'];

        $po_name_wise_data_arr [$po_name_arr[$v['PO_ID']]]['PO_QTY'] += $v['PO_QTY'];
        $po_name_wise_data_arr [$po_name_arr[$v['PO_ID']]]['PO_PRICE'] += $v['PO_PRICE'];
      }
      // pre($po_name_wise_data_arr);
      // =========================================================================================================
                                                //DATA MAKING
      // =========================================================================================================

      $comp_wise_floor_count  = array();
      $floor_wise_price_arr  = array();
      foreach ($floor_wise_po_arr as $comp_id => $comp_arr)
      {
        foreach ($comp_arr as $floor_id =>  $floor_arr) 
        {

          // $comp_wise_floor_count [$comp_id] +=1;
          foreach ($floor_arr as $po_id => $v) 
          {
            $floor_wise_price_arr[$comp_id][$floor_id]['PO_QTY']    += $po_wise_data_arr[$po_id]['PO_QTY']; 
            $floor_wise_price_arr[$comp_id][$floor_id]['PO_PRICE']  += $po_wise_data_arr[$po_id]['PO_PRICE']; 
            // $prod_arr[$comp_id][$floor_id]['PO_QTY'] += $v['ORDER_TOTAL']/ $v['PO_QTY']; 
          }
        }
      }
      // pre($floor_wise_price_arr);
      $fob_arr = array();
      foreach ($prod_arr as $comp_id => $comp_arr)
      {
        foreach ($comp_arr as $floor_id =>  $v) 
        {

          $comp_wise_floor_count [$comp_id] +=1;
          $po_qty = $floor_wise_price_arr[$comp_id][$floor_id]['PO_QTY'];
          $po_price = $floor_wise_price_arr[$comp_id][$floor_id]['PO_PRICE'];

          $prod_arr[$comp_id][$floor_id]['FOB'] += $po_price/$po_qty;
        }
      }
      //  pre($fob_arr); die;
      //=================================== CLEAR TEMP ENGINE ====================================
      $con = connect();
      execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 104 and ref_from in(1)");
    	oci_commit($con);
      disconnect($con);


      $floor_count = max($comp_wise_floor_count);
      // pre($prod_arr); die;
      $width1 = 500 +  ($floor_count*200);
      $width2 = 500;
      $width3 = 400;
      $ttl_bg ="#e3e032";
      ?>
      <style>

        tbody tr th{
            border: 1px solid #8DAFDA;
        }
      </style>

        <!-- ===================================================================================================== -->
                                                <!-- DETAILS PART -->
        <!-- ===================================================================================================== -->
        <fieldset  style="height:auto; width:<? echo $width1+20;?>px; margin:20px auto; padding:0;">
          <table width="<? echo $width1;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
            <thead class="form_caption" >
              <tr>
                <td colspan="6" align="center" style="font-size:18px; font-weight:bold" >Production Summary Report</td>
              </tr>
            </thead>
          </table>
          <div align="center" style="height:auto; width:<? echo $width1+20;?>px; margin: 10px auto; padding:0;">
            <div style="width:<?= $width1+20;?>px; max-height:350px; float:left; overflow-y:scroll;" id="scroll_body">
              <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table details_table" id="table_body" width="<?= $width1; ?>" rules="all" align="left">
                <?
                  $group_ttl_target = $group_ttl_achived = $group_ttl_target_val = $group_ttl_achived_val = 0;
                  foreach ($prod_arr as $comp_id =>  $comp_arr)
                  {
                    $total_target = $total_prod_qty = $total_fob = 0;
                    $total_floor = $comp_wise_floor_count[$comp_id];
                    $ex_col_span = $floor_count - $total_floor;
                    ?>
                      <thead>
                          <tr>
                              <th style="background:#f9d790;"  colspan="<?= ($floor_count*2 +4) ?>">
                                <?= $company_short_library[$comp_id] ?>
                              </th>
                          </tr>

                          <tr>
                            <th width="150"></th>
                            <?
                              foreach ($comp_arr as $floor_id => $v)
                              {
                                ?>
                                  <th width="100"><?=  $floor_library[$floor_id]?></th>
                                  <th width="100">Achieve %</th>
                                <?
                              }
                              if ($ex_col_span)
                              {
                                ?>
                                  <th colspan="<?= $ex_col_span*2 ?>" ></th>
                                <?
                              }
                            ?>

                            <!-- Floor total -->
                            <th width="150" style="background: <?=$ttl_bg?>;"></th>
                            <th width="100" style="background: <?=$ttl_bg?>;"><?= $short_company?> Total </th>
                            <th width="100" style="background: <?=$ttl_bg?>;"> Total Achieve% </th>
                          </tr>
                      </thead>
                      <tbody>
                          <tr>
                            <th> Target Production </th>
                            <?
                              foreach ($comp_arr as $floor_id => $v)
                              {
                                $target = $v['TARGET'] ?? 0;
                                $prod_qty = $v['PROD_QTY'] ?? 0;
                                $total_target +=$target;
                                $total_prod_qty +=$prod_qty;
                                $achieved_per = ($prod_qty/ $target)*100;
                                $achieved_per = is_num($achieved_per); // custom function
                                ?>

                                  <td align="right"><?= $target ?></td>
                                  <td rowspan="2" align="center" valign="middle" title="( Achieved Production /Target Production ) x 100 ">
                                    <?= round($achieved_per) ?>%
                                  </td>
                                <?
                              }
                              if ($ex_col_span) //Extra Column
                              {
                                ?>
                                  <th colspan="<?= $ex_col_span*2 ?>" rowspan="4"></th>
                                <?
                              }
                              $total_target_per = ($total_prod_qty/ $total_target)*100;
                              $total_target_per = is_num($total_target_per); // custom function
                              $group_ttl_target   += $total_target;
                              $group_ttl_achived  +=  $total_prod_qty;
                            ?>
                            <!-- Floor total -->
                            <th> Target Production </th>
                            <td align="right"><?= $total_target ?></td>
                            <td align="center" valign="middle" rowspan="2" title="( Achieved Production /Target Production ) x 100 ">
                              <?= round($total_target_per)?>%
                            </td>
                          </tr>
                          <tr>
                            <th> Achieved Production </th>
                            <?
                              foreach ($comp_arr as $floor_id => $v)
                              {
                                ?>
                                  <td  align="right"><?= $v['PROD_QTY']??0 ?></td>
                                <?
                              }
                            ?>

                            <!-- Floor total -->
                            <th> Achieved Production </th>
                            <td align="right"><?= $total_prod_qty ?></td>
                          </tr>
                          <tr>
                            <th> Target Value ($) </th>
                            <?
                               $tot_target_val=0;
                              foreach ($comp_arr as $floor_id => $v)
                              {
                                $target = $v['TARGET'] ?? 0;
                                $prod_qty = $v['PROD_QTY'] ?? 0;
                                $fob    = $v['FOB'];
                                // $fob    = $v['FOB']/ $v['JOB_COUNT'];
                                $fob    = is_num($fob);
                                $target_val = $target * $fob ;
                                $achieved_val = $v['ACHIVED_VAL'];
                                $total_fob +=$fob ;
                                $achived_val_per = ($achieved_val/ $target_val)*100;
                                $achived_val_per = is_num($achived_val_per); // custom function
                                $group_ttl_achived_val += $v['ACHIVED_VAL'];
                                $total_prod_val  += $v['ACHIVED_VAL'];

                                ?>
                                  <td align="right" title="Target Production (<?=$target?>) x AVG FOB (<?= $fob ?>) ">
                                    <?= number_format($target_val,2); $tot_target_val+=$target_val;  ?>
                                  </td>
                                  <td align="center" valign="middle" rowspan="2" title="( Achieved Value /Target  Value) x 100 ">
                                    <?= round($achived_val_per)?>%
                                  </td>
                                <?
                              }
                              $floor_fob = $total_fob/$total_floor;
                              $total_target_prod_val = $total_target * $floor_fob;

                              $floor_achived_per = $total_prod_val / $total_target_prod_val *100;
                              $floor_achived_per = is_num($floor_achived_per);
                              $group_ttl_target_val += $tot_target_val;

                            ?>

                            <!-- Floor total -->
                            <th> Target Value ($) </th>
                            <td align="right">
                              <?= number_format($tot_target_val,2)  ?>
                            </td>
                            <td rowspan="2" title="( Achieved Value /Target  Value) x 100 " align="center" valign="middle">
                                <?= round($floor_achived_per) ?>%
                            </td>
                          </tr>
                          <tr>
                            <th> Achieved Value ($) </th>
                            <?
                              foreach ($comp_arr as $floor_id => $v)
                              {
                                $prod_qty = $v['PROD_QTY'];
                                $fob      = $v['FOB'];
                                $fob      = is_num($fob);
                                $achieved_val = $v['ACHIVED_VAL'];
                                ?>
                                  <td align="right">
                                    <?= number_format($achieved_val,2) ?>
                                  </td>
                                <?
                              }
                            ?>
                            <!-- Floor total -->
                            <th> Achieved Value ($) </th>
                            <td align="right" >
                              <?= number_format($total_prod_val,2) ?>
                            </td>
                          </tr>
                      </tbody>
                    <?
                  }
                ?>
              </table>
            </div>
          </div>
		    </fieldset>


        <!-- ===================================================================================================== -->
                                                <!-- SUMMERY PART -->
        <!-- ===================================================================================================== -->
        <fieldset  style="height:auto; width:<? echo $width2+20;?>px; margin:20px auto; padding:0;">
          <table width="<? echo $width2;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
            <thead class="form_caption" >
              <tr>
                <td colspan="4" align="center" style="font-size:18px; font-weight:bold" >Group Summary(<?= implode(' & ',$company_arr ) ?>)</td>
              </tr>
            </thead>
          </table>
          <div align="center" style="height:auto; width:<? echo $width2+20;?>px; margin: 10px auto; padding:0;">
            <div style="width:<?= $width2+20;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
              <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width2; ?>" rules="all" align="left">
                <thead>
                    <tr>
                      <th width="150"></th>
                      <th width="100"></th>
                      <th width="100"> Access/Short Qty </th>
                      <th width="100">Achieve %</th>
                    </tr>
                </thead>
                <tbody>
                  <?
                    $group_acc_or_short_qty = $group_ttl_achived - $group_ttl_target;
                    $group_acc_or_short_val = $group_ttl_achived_val - $group_ttl_target_val;
                    $group_achived_qty_per  =  ($group_ttl_achived / $group_ttl_target)*100;
                    $group_achived_qty_per  =  is_num($group_achived_qty_per);//custom function
                    $group_achived_val_per  =  ($group_ttl_achived_val / $group_ttl_target_val)*100;
                    $group_achived_val_per  =  is_num($group_achived_val_per);//custom function
                  ?>
                    <tr>
                      <th> Target Production </th>
                      <td align="right"> <?= $group_ttl_target ?></td>
                      <td rowspan="2" align="center" valign="middle">
                         <?=  $group_acc_or_short_qty ?>
                        </td>
                      <td rowspan="2"  align="center" valign="middle">
                        <?= round($group_achived_qty_per) ?>%
                      </td>
                    </tr>
                    <tr>
                      <th> Achieved Production </th>
                      <td align="right"> <?= $group_ttl_achived ?> </td>
                    </tr>
                    <tr>
                      <th> Target Value ($) </th>
                      <td align="right"><?= number_format($group_ttl_target_val,2);?></td>
                      <td rowspan="2" align="center" valign="middle">
                        <?= number_format($group_acc_or_short_val,2) ?>
                      </td>
                      <td rowspan="2"  align="center" valign="middle">
                        <?= round($group_achived_val_per)?>%
                      </td>
                    </tr>
                    <tr>
                      <th> Achieved Value ($) </th>
                      <td align="right"> <?= number_format($group_ttl_achived_val,2)  ?> </td>
                    </tr>
                </tbody>
              </table>
            </div>
          </div>
		    </fieldset>


        <!-- ===================================================================================================== -->
                                                <!-- CUTTING PART -->
        <!-- ===================================================================================================== -->
        <fieldset  style="height:auto; width:<? echo $width3+20;?>px; margin:20px auto; padding:0;">
          <div align="center" style="height:auto; width:<? echo $width3+20;?>px; margin: 10px auto; padding:0;">
            <div style="width:<?= $width3+20;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
              <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width3; ?>" rules="all" align="left">

                <thead>
                    <tr>
                      <?
                        foreach ($company_arr as $company)
                        {
                          ?>
                            <th colspan="3"><?= $company ?> Cutting</th>
                          <?
                        }
                      ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                      <?
                        foreach ($company_arr as $company)
                        {
                          ?>
                            <th width="150"> Target: </th>
                            <td width="100"></td>
                            <td width="100" rowspan="2"></td>
                          <?
                        }
                      ?>
                    </tr>
                    <tr>
                      <?
                        foreach ($company_arr as $comp_id => $company)
                        {
                          ?>
                            <th> Production : </th>
                            <td align="right"><?= $cutting_arr[$comp_id]['CUTTING_QTY']??0 ?></td>
                          <?
                        }
                      ?>
                    </tr>
                </tbody>
              </table>
            </div>
          </div>
		    </fieldset>
      <?
  }

  foreach (glob($user_id."_*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";
	$create_new_excel = fopen($name, 'w');
	$is_created = fwrite($create_new_excel,ob_get_contents());
	//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	echo "####".$name;
	exit();
}

?>