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
  $floor_no_id   = str_replace("'","",$cbo_floor_id);
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
      $res_cond_sql .= $floor_no_id    ? " and a.floor_id in ($floor_no_id)"     : "";
      $res_cond_sql .= ($form_date && $to_date) ?" and b.pr_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : "";
      $resource_sql="select a.id as line_id, a.company_id,a.floor_id,b.target_per_hour as target,pr_date,b.working_hour,e.po_id,e.gmts_item_id as item from prod_resource_mst a, prod_resource_dtls b,lib_prod_floor c,prod_resource_dtls_mast d,prod_resource_color_size e where a.id=b.mst_id and a.id=d.mst_id and  b.mast_dtl_id=d.id and e.mst_id = a.id
      and dtls_id = d.id and c.id = a.floor_id and a.is_deleted=0 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $res_cond_sql  order by c.floor_serial_no asc";
      // echo $resource_sql; die;
      
      $resource_sql_res = sql_select($resource_sql);
      if (count($resource_sql_res) == 0 ) {
        echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Production Resource Data Not Found ** </h1>" ;
        die();
      }
      $prod_arr = $check_arr = $line_wise_data = $po_id_arr = $line_wise_po =$line_wise_target= array();
      foreach ($resource_sql_res as $v)
      {
        $date = strtotime($v['PR_DATE']);
        $line = $v['LINE_ID'];
        $floor = $v['FLOOR_ID'];
        if (!$check_arr[$line][$date]) 
        { 
          $prod_arr[$v['COMPANY_ID']][$floor]['TARGET'] += ($v['TARGET'] * $v['WORKING_HOUR']);
          $line_wise_target[$v['COMPANY_ID']][$floor][$line] += $v['TARGET'] * $v['WORKING_HOUR'];
        }
        $line_wise_data [$v['COMPANY_ID']][$floor][$line][$v['PO_ID']][$v['ITEM']] = $v['TARGET'] * $v['WORKING_HOUR'];
        $check_arr[$line][$date] = $line;
        $po_id_arr[$v['PO_ID']] = $v['PO_ID']; 
        $line_wise_po[$v['COMPANY_ID']][$floor][$line][$v['PO_ID']][$v['ITEM']] = $v['TARGET'] * $v['WORKING_HOUR'];
      }
  
       //=================================== CLEAR TEMP ENGINE ====================================
       $con = connect();
       execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 104 and ref_from in(1)");
       oci_commit($con);
 
       //=================================== INSERT LINE ID INTO TEMP ENGINE ====================================
       fnc_tempengine("gbl_temp_engine", $user_id, 104, 1,$po_id_arr, $empty_arr);
      
       $po_qty_sql = "select c.po_break_down_id as po_id,c.order_quantity as po_qty,c.order_total as po_price,item_number_id as item,order_rate  from wo_po_color_size_breakdown c,gbl_temp_engine tmp where  c.po_break_down_id=tmp.ref_val and tmp.user_id=$user_id and tmp.entry_form=104 and tmp.ref_from=1 and c.status_active=1 and c.is_deleted=0 ";
      //  echo $po_qty_sql;die;
       $po_qty_sql_res = sql_select($po_qty_sql); 
       foreach ($po_qty_sql_res as  $v) 
       {
         $po_wise_data_arr [$v['PO_ID']][$v['ITEM']]['PO_QTY'] += $v['PO_QTY'];
         $po_wise_data_arr [$v['PO_ID']][$v['ITEM']]['PO_PRICE'] += $v['PO_PRICE']; 
         $po_wise_data_arr [$v['PO_ID']][$v['ITEM']]['UNIT_PRICE'] = $v['ORDER_RATE']; 
       }
      //  pre($po_wise_data_arr); die;
 
      $floor_wise_po_info = array();
      foreach ($line_wise_po as  $company => $floor_arr) 
      {
          foreach ($floor_arr as $floor => $line_arr) 
          {
            foreach ($line_arr as $line => $po_arr) 
            {
              foreach ($po_arr as $po_id => $item_arr) 
              {
                foreach ($item_arr as $item => $target) 
                {
                   
                  $floor_wise_po_info[$company][$floor][$line]['PO_QTY'] +=  $po_wise_data_arr[$po_id][$item]['PO_QTY'] ;
                  $floor_wise_po_info[$company][$floor][$line]['PO_PRICE'] += $po_wise_data_arr[$po_id][$item]['PO_PRICE'];  
                
                }
              }
            }
          }
      }
      // pre($floor_wise_po_info) ;die;

      foreach ($floor_wise_po_info as  $company => $floor_arr) 
      {
          foreach ($floor_arr as $floor => $line_arr) 
          {
            foreach ($line_arr as $line => $v) 
            {
                $target_qty = $line_wise_target[$company][$floor][$line];
                $po_price   = $v['PO_PRICE'];
                $po_qty     = $v['PO_QTY'];

                $unit_price = $po_price / $po_qty;
                $prod_arr[$company][$floor]['TARGET_VAL'] += $target_qty * $unit_price;
                $prod_arr[$company][$floor]['FOB'] = $unit_price;
            }
          }
      }
      
      // pre($prod_arr);die;
      // =========================================================================================================
                                                //PRODUCTION DATA
      // =========================================================================================================
      $cond_sql = '';
      $floor_cod = '';
      $cond_sql .= $company_id  ? " and a.serving_company in ($company_id)" : "";
      $cond_sql .= $location_id ? " and a.location in ($location_id)"  : "";
      $floor_cod .= $floor_no_id? " and a.floor_id in ($floor_no_id)"     : "";
      $cond_sql .= ($form_date && $to_date) ?" and a.production_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : "";

      $prod_sql = "select c.po_break_down_id as po_id,a.serving_company as company,a.floor_id,b.production_type as prod_type,
      case when  b.production_type=5 then b.production_qnty else 0 end as sew_out,
      case when  b.production_type=1 then b.production_qnty else 0 end as cut_qty,c.order_quantity as po_qty,c.order_total,c.order_rate as unit_price from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c,lib_prod_floor d where a.id = b.mst_id and a.po_break_down_id = c.po_break_down_id and c.id = b.color_size_break_down_id and d.id = a.floor_id and b.production_qnty > 0 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and b.production_type in (1, 5) $cond_sql $floor_cod order by d.floor_serial_no asc";

      // echo $prod_sql; die;
      $prod_sql_res = sql_select($prod_sql);


      $company_arr  = array();
      // $line_id_arr  = array();

      $company_wise_prod_arr = array();
      $cutting_arr = array(); 
      $floor_wise_po_arr = array();
      foreach ($prod_sql_res as $v)
      {
        if ($v['PROD_TYPE'] == 5 & $v['SEW_OUT'] > 0)
        {
          // $po_id_arr[$v['PO_ID']] = $v['PO_ID'];   
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
      
      // =========================================================================================================
                                                //DATA MAKING
      // ========================================================================================================= 
      foreach ($prod_arr as $comp_id => $comp_arr)
      {
        foreach ($comp_arr as $floor_id =>  $v) 
        {

          $comp_wise_floor_count [$comp_id] +=1;
        }
      }
      //  pre($fob_arr); die;
      //=================================== CLEAR TEMP ENGINE ====================================
      $con = connect();
      execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 104 and ref_from in(1)");
    	oci_commit($con);
      disconnect($con);


      $floor_count = max($comp_wise_floor_count); 
      $width1 = 500 +  ($floor_count*200);
      $width2 = 500;
      $width3 = 400;
      $ttl_bg ="#e3e032";
      ob_start();
      // pre($prod_arr); die;
      ?>
      <style>

        tbody tr th{
            border: 1px solid #8DAFDA;
        }
      </style>

        <!-- ===================================================================================================== -->
                                                <!-- DETAILS PART -->
        <!-- ===================================================================================================== -->
        <div>
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
                      $total_target = $total_prod_qty = $total_fob = $total_prod_val =0;
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
                                  // $fob    = is_num($fob);
                                  $target_val = $v['TARGET_VAL'] ;
                                  $fob        = $target_val / $target ;
                                  $fob        = is_num($fob);
                                  // $target_val = $target * $fob ;
                                  $achieved_val = $v['ACHIVED_VAL'];
                                  $total_fob +=$fob ;
                                  $achived_val_per = ($achieved_val/ $target_val)*100;
                                  $achived_val_per = is_num($achived_val_per); // custom function
                                  $group_ttl_achived_val += $v['ACHIVED_VAL'];
                                  $total_prod_val  += $v['ACHIVED_VAL'];

                                  ?>
                                    <td align="right"  title="Target Production (<?=$target?>) x AVG FOB (<?= $fob ?>) ">
                                      <a href="#" onclick="target_value_popup(<?=$comp_id?>,<?=$floor_id?>)"> <?= number_format($target_val,2); $tot_target_val+=$target_val;  ?> </a>
                                    </td>
                                    <td align="center" valign="middle" rowspan="2" title="( Achieved Value /Target  Value) x 100 ">
                                      <?= round($achived_val_per)?>%
                                    </td>
                                  <?
                                }
                                $floor_fob = $total_fob/$total_floor;
                                $total_target_prod_val = $total_target * $floor_fob;

                                $floor_achived_per = is_num($floor_achived_per);
                                $group_ttl_target_val += $tot_target_val;
                                $floor_achived_per = $total_prod_val / $tot_target_val *100;

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
        </div>
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


if ($action=='target_value_popup') 
{
  echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode); 
  extract($_REQUEST);
  $company_id = str_replace("'","",$cbo_company_id);
  $location_id= str_replace("'","",$cbo_location_id);
  $floor_no_id   = str_replace("'","",$cbo_floor_id);
  $form_date  = $txt_date_from;
  $to_date    = $txt_date_to;

  // ============================================================================================================
  //												Library
  // ============================================================================================================
    
  $line_no_arr=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
  $lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
	order by sewing_line_serial");
  foreach($lineDataArr as $v)
	{ 
		$lineSerialArr[$v['ID']]=$v['SEWING_LINE_SERIAL']; 
	}

 

  // =========================================================================================================
                                            //PRODUCTION RESOURCE DATA
  // =========================================================================================================
  $res_cond_sql ="";
  $res_cond_sql .= $company_id  ? " and a.company_id in ($company_id)" : "";
  $res_cond_sql .= $floor_id    ? " and a.floor_id in ($floor_id)"     : "";
  $res_cond_sql .= ($form_date && $to_date) ?" and b.pr_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : "";
  $resource_sql="select a.id as line_id, a.company_id,a.floor_id,a.line_number,b.target_per_hour as target,pr_date,b.working_hour,e.po_id,e.gmts_item_id as item from prod_resource_mst a, prod_resource_dtls b,lib_prod_floor c,prod_resource_dtls_mast d,prod_resource_color_size e where a.id=b.mst_id and a.id=d.mst_id and  b.mast_dtl_id=d.id and e.mst_id = a.id
  and dtls_id = d.id and c.id = a.floor_id and a.is_deleted=0 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $res_cond_sql   order by c.floor_serial_no asc";
  // echo $resource_sql; die; 
  
  $resource_sql_res = sql_select($resource_sql);
  if (count($resource_sql_res) == 0 ) {
    echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Production Resource Data Not Found ** </h1>" ;
    die();
  }
  $prod_arr = $check_arr = $line_wise_data = $po_id_arr = array();
  foreach ($resource_sql_res as $v)
  {
    $date = strtotime($v['PR_DATE']);
    $line = $v['LINE_ID'];
    $floor = $v['FLOOR_ID'];
    $line_name_exp = explode(',',$v['LINE_NUMBER']);
    $line_name = '';
    foreach ($line_name_exp as $line_id) 
    {
      if ($line_name == '') 
      {
        $line_name .= $line_no_arr[$line_id];
      }else
      {
        $line_name .= ','.$line_no_arr[$line_id];
      }
    }
    $sewing_line_id = $line_name_exp[0];

    if($lineSerialArr[$sewing_line_id]=="")
		{
			$lastSlNo++;
			$slNo=$lastSlNo;
			$lineSerialArr[$sewing_line_id]=$slNo;
		}
		else
		{
			$slNo=$lineSerialArr[$sewing_line_id];
		}

    
    if (!$check_arr[$line][$date]) 
    { 

      $prod_arr[$line]['TARGET'] += ($v['TARGET'] * $v['WORKING_HOUR']);
      $prod_arr[$line]['LINE_NAME'] = $line_name ;
    }
    $line_wise_po[$slNo][$line][$v['PO_ID']][$v['ITEM']] = $v['PO_ID'];
    $check_arr[$line][$date] = $line;
    $po_id_arr[$v['PO_ID']] = $v['PO_ID']; 
  } 
  ksort($line_wise_po);   
  // pre($prod_arr); die;
  //=================================== CLEAR TEMP ENGINE ====================================
  $con = connect();
  execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 104 and ref_from in(1)");
  oci_commit($con);

  //=================================== INSERT LINE ID INTO TEMP ENGINE ====================================
  fnc_tempengine("gbl_temp_engine", $user_id, 104, 1,$po_id_arr, $empty_arr);

  $po_name_sql = "select c.id as po_id,c.po_number from wo_po_break_down c,gbl_temp_engine tmp where  c.id=tmp.ref_val and tmp.user_id=$user_id and tmp.entry_form=104 and tmp.ref_from=1 and c.status_active=1 and c.is_deleted=0 ";
  $po_name_sql_res = sql_select($po_name_sql); 
  foreach ($po_name_sql_res as  $v) 
  {
    $po_name_array [$v['PO_ID']]= $v['PO_NUMBER']; 
  }

  $po_qty_sql = "select c.po_break_down_id as po_id,c.order_quantity as po_qty,c.order_total as po_price,item_number_id as item,order_rate  from wo_po_color_size_breakdown c,gbl_temp_engine tmp where  c.po_break_down_id=tmp.ref_val and tmp.user_id=$user_id and tmp.entry_form=104 and tmp.ref_from=1 and c.status_active=1 and c.is_deleted=0 ";
  //  echo $po_qty_sql;die;
  $po_qty_sql_res = sql_select($po_qty_sql); 
  foreach ($po_qty_sql_res as  $v) 
  {
    $po_wise_data_arr [$v['PO_ID']][$v['ITEM']]['PO_QTY'] += $v['PO_QTY'];
    $po_wise_data_arr [$v['PO_ID']][$v['ITEM']]['PO_PRICE'] += $v['PO_PRICE']; 
    $po_wise_data_arr [$v['PO_ID']][$v['ITEM']]['UNIT_PRICE'] = $v['ORDER_RATE']; 
  }


  $line_span_arr = $po_span_arr = $line_wise_po_info =array();
  foreach ($line_wise_po as $sl => $sl_arr) 
  {
    foreach ($sl_arr as $line => $po_arr) 
    {
      foreach ($po_arr as $po_id => $item_arr) 
      {
        foreach ($item_arr as $item => $v) 
        {
          $line_span_arr[$line] ++;
          $po_span_arr[$line][$po_id] ++;
          $line_wise_po_info[$line]['PO_QTY']   += $po_wise_data_arr[$po_id][$item]['PO_QTY'] ;
          $line_wise_po_info[$line]['PO_PRICE'] += $po_wise_data_arr[$po_id][$item]['PO_PRICE'];
        }
      }
    }
  }
  
  // pre( $line_wise_po_info)  ;
  //=================================== CLEAR TEMP ENGINE ====================================
  $con = connect();
  execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 104 and ref_from in(1)");
  oci_commit($con);
  disconnect($con);
 
  $width1 = 660;
  $ttl_bg ="#e3e032";
  ob_start();
  // pre($prod_arr); die;
  ?>
  <style>

    tbody tr th{
        border: 1px solid #8DAFDA;
    }
  </style>

  <fieldset  style="height:auto; width:<? echo $width1+20;?>px; margin:20px auto; padding:0;">
    <table width="<? echo $width1;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
      <thead class="form_caption" >
        <tr>
          <td colspan="6" align="center" style="font-size:18px; font-weight:bold" >Target Value Details</td>
        </tr>
      </thead>
    </table>
    <div align="center" style="height:auto; width:<? echo $width1+20;?>px; margin: 10px auto; padding:0;">
      <div style="width:<?= $width1+20;?>px; max-height:350px; float:left; overflow-y:scroll;" id="scroll_body">
        <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table details_table" id="table_body" width="<?= $width1; ?>" rules="all" align="left"> 
          <thead> 
            <th> Sl </th>
            <th width="80"> Line </th>
            <th width="120"> PO Number </th>
            <th width="120"> Item </th>
            <th width="80"> Target </th>
            <th width="60"> Avg FOB </th>
            <th width="120"> Target Value </th>
          </thead>
          <tbody>  
              <?
                $i=$total_target = $total_target_value =  $total_po_qty  =$total_po_price  = 0;
                foreach ($line_wise_po as $sl => $sl_arr) 
                {
                  foreach ($sl_arr as $line => $po_arr) 
                  {
                    $j=0;
                    foreach ($po_arr as $po_id => $item_arr) 
                    {
                      $k=0;
                      foreach ($item_arr as $item => $v) 
                      {
                        $i++;
                        $po_qty   = $line_wise_po_info[$line]['PO_QTY'];
                        $po_price = $line_wise_po_info[$line]['PO_PRICE'];
                        $item_po_qty  = $po_wise_data_arr[$po_id][$item]['PO_QTY'];
                        $item_price   = $po_wise_data_arr[$po_id][$item]['PO_PRICE'];
                        $po_price = $line_wise_po_info[$line]['PO_PRICE'];
                        $unit_price = ($po_price /$po_qty );
                        $target = $prod_arr[$line]['TARGET']; 
                        $total_po_qty  += $item_po_qty ;
                        $total_po_price += $item_price;
                        

                        if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
                        ?>
                          <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                            <td align="center"><?= $i ?></td>
                            <? 
                              if ($j==0) 
                              { 
                                $total_target  += $target; 
                                ?>
                                  <td valign="middle" rowspan="<?=$line_span_arr[$line]?>" ><?=  $prod_arr[$line]['LINE_NAME'] ?></td>
                                <?
                              }     
                            ?>
                            <? 
                              if ($k==0) 
                              {  
                                ?>
                                  <td  valign="middle" rowspan="<?=$po_span_arr[$line][$po_id]?>"><?= $po_name_array[$po_id] ?></td>
                                <?
                              }     
                            ?>
                            
                            <td title="<?= " PO Qty =$item_po_qty, PO Price =$item_price" ?>"><?= $garments_item[$item] ?></td>
                            <? 
                              if ($j==0) 
                              { 
                                
                                $target_value = $unit_price * $target;
                                $total_target_value += $target_value;
                                ?>
                                  <td  valign="middle" rowspan="<?=$line_span_arr[$line]?>" align="right"><?= $target ?></td>
                                  <td valign="middle" rowspan="<?=$line_span_arr[$line]?>" align="right" title="<?= "PO Price($po_price)/PO Qty($po_qty) = $unit_price" ?>"><?= number_format($unit_price,2) ?></td>
                                  <td valign="middle" rowspan="<?=$line_span_arr[$line]?>" align="right" title="<?= "Unit Price($unit_price) * target($target)  = $target_value" ?>"><?= number_format($target_value,2) ?></td>
                                <?
                              }     
                            ?>  
                          </tr>
                        <?
                        $j++;
                        $k++; 
                      }
                    } 
                  }
                }  
              ?>  
          </tbody>
          <tfoot>
              <tr> 
                <?
                  $avg_unit_price = $total_target_value/$total_target;
                ?>
                <th colspan="4" align="right" >Total:</th>  
                <th align="right"><?= $total_target ?></th>
                <th align="right" title="<?= "$avg_unit_price" ?>"> <?= number_format($total_target_value/$total_target,2) ?></th>
                <th align="right" title="<?= $total_target_value ?>"><?= number_format( $total_target_value,2) ?></th>
              </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </fieldset> 

  <?
}
?>