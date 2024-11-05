<?php
//--------------------------------------------------------------------------------------------------------------------
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header('location:login.php');
include('../../../includes/common.php'); 
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_level=$_SESSION['logic_erp']["user_level"];
//--------------------------------------------------------------------------------------------------------------------
 
if($action=="search_popup") {
	echo load_html_head_contents('Search', '../../../', 1, 1, '', '', '');
	extract($_REQUEST);
?>
     
	<script>
		var searchType = <?php echo $searchType; ?>;

		function js_set_value(values) {			
			var values=values.split("_");

			document.getElementById('hdnJobNo').value = values[0];
			document.getElementById('hdnOrderNo').value = values[1];
			document.getElementById('hdnYear').value = values[2];

			parent.searchWindow.hide();
		}

		/**
		 * change search by title after the popup is loaded
		 */
		window.addEventListener('load', function() {
		    if(searchType == 1) {
				document.getElementById('search_by_td_up').innerHTML = 'Please Enter Job No';
			}
		})
	
    </script>

</head>

<body>
<div align="center">
	<form name="searchForm" id="searchForm">
		<fieldset style="width:780px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hdnJobNo" id="hdnJobNo" />
                    <input type="hidden" name="hdnOrderNo" id="hdnOrderNo" />
                    <input type="hidden" name="hdnYear" id="hdnYear" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?php
								echo create_drop_down( 'cbo_buyer_name', 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name", 'id,buyer_name', 1, '-- All Buyer--', 0, '' );
							?>
                        </td>
                        <td align="center">
                    	<?php
                       		$search_by_arr=array(1=>'Job No',2=>'Order No');
                       		if($searchType == 1) {
                       			$selected_index = 1;
                       		} else {
                       			$selected_index = 2;
                       		}
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down('cbo_search_by', 110, $search_by_arr, '', 0, '', $selected_index, $dd, 0);
						?>
                        </td>     
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" readonly>
                        </td>	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?php echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value, 'create_search_list_view', 'search_div', 'knitting_plan_and_position_style_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" height="20" valign="middle"><?php echo load_month_buttons(1); ?></td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?php
	exit(); 
}

 

if($action == 'generate_report') {  
	$process = array( &$_POST );
    // echo "<pre>";
    // print_r($process ) ;
    // die; 
	extract(check_magic_quote_gpc( $process )); 
    // echo $cbo_lc_company_name;
    // die;
	$html_res=''; 
    echo load_html_head_contents('Search', '../../../', 1, 1, '', '', '');

	if($type == 1) {
      $con_condition = '';
      if($cbo_lc_company_name !=0){
        $con_condition .= "and a.company_name=$cbo_lc_company_name" ;
      }  
      if($cbo_company_name !=0){
        $con_condition .= "and a.working_company_id=$cbo_company_name" ;
      }   
      if($cbo_buyer_name !=0){
        $con_condition .= "and a.buyer_name=$cbo_buyer_name" ;
      }   
      if($txt_date_from && $txt_date_to){
        $con_condition .= "and b.pub_shipment_date between '".change_date_format($txt_date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($txt_date_to,'dd-mm-yyyy','-',1)."'" ;
      }  
    $sql = "SELECT a.id,a.avg_unit_price ,b.po_quantity,c.smv_pcs,c.gmts_item_id,b.is_confirmed,to_char(b.pub_shipment_date,'MON-YYYY')as month_year , b.id as po_id FROM wo_po_details_master a,wo_po_break_down b,wo_po_details_mas_set_details c WHERE a.status_active=1 and b.status_active=1 and is_confirmed in(1,2) and a.is_deleted =0 and b.is_deleted =0  and a.id = b.job_id and a.id = c.job_id  $con_condition ORDER BY pub_shipment_date ASC";  
    
    // echo $sql; die;
   $result =  sql_select($sql); 
   $data = [];
   $job_id_arr = [];
   $po_arr = [];
   foreach($result  as $res){
    $job_id_arr[$res['ID']] = $res['ID'];
    $po_arr[$res['PO_ID']] = $res['PO_ID'];
    $data[$res['MONTH_YEAR']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['PO_QUANTITY'] +=  $res['PO_QUANTITY'];
    $data[$res['MONTH_YEAR']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['MINUTE'] +=  $res['PO_QUANTITY'] * $res['SMV_PCS']; 
    $data[$res['MONTH_YEAR']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['FOB'] +=  $res['PO_QUANTITY'] * $res['AVG_UNIT_PRICE']; 
    $data[$res['MONTH_YEAR']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['SMV_PCS'] += $res['SMV_PCS']; 
    $data[$res['MONTH_YEAR']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['UNIT_PRICE'] += $res['AVG_UNIT_PRICE']; 
   }
//    print_r($po_arr);

   $con = connect(); 
   fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 899, 1,$job_id_arr, $empty_arr); 
   oci_commit($con);   
   $cm_sql =  "SELECT a.job_id,a.cm_cost FROM WO_PRE_COST_DTLS a, GBL_TEMP_ENGINE b WHERE a.job_id = b.ref_val and  b.user_id=$user_id and b.entry_form=899 and  a.status_active=1 and a.is_deleted =0 " ;
   $cm_cost_res = sql_select($cm_sql); 
   execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form = 899");
   oci_commit($con); 

    //for production Qty
   $con = connect(); 
   fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 899, 1,$po_arr, $empty_arr); 
   oci_commit($con);   
   $prod_sql =  "SELECT a.production_quantity as prod_qty, a.po_break_down_id as po_id FROM pro_garments_production_mst a, GBL_TEMP_ENGINE b WHERE a.production_type= 5 and a.po_break_down_id = b.ref_val and  b.user_id=$user_id and b.entry_form=899 and  a.status_active=1 and a.is_deleted =0";
   $prod_sql_res = sql_select($prod_sql); 
   execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form = 899");
   oci_commit($con); 
   
    //  echo print_r($prod_sql_res) ;die;
   
   $cm_cost_arr=[];
   foreach($cm_cost_res as   $v){
        $cm_cost_arr[$v['JOB_ID']] = $v['CM_COST'];
   }
    //  for prod qty
   $porod_qty_arr=[];
   foreach($prod_sql_res as   $v){
        $porod_qty_arr[$v['PO_ID']] = $v['PROD_QTY'];
   }
  /*   echo "<pre>";
    print_r( $data) ;
    die;  */
   $month_wise_data_arr = [] ;
   $month_wise_prod_arr = [] ;
   foreach($data as $month => $m ){
    foreach ($m as $is_confirmed => $c){
        foreach ($c as $job_id =>$job){ 
            foreach ($job as $po_id =>$po){
                foreach ($po as $item => $v){
                    $prod_qty = $porod_qty_arr[$po_id];
                    // for booking
                    $month_wise_data_arr[$month][$is_confirmed]['PO_QUANTITY'] += $v['PO_QUANTITY'];  
                    $month_wise_data_arr[$month][$is_confirmed]['MINUTES'] += $v['MINUTE'];  
                    $month_wise_data_arr[$month][$is_confirmed]['FOB'] += $v['FOB'];  
                    $month_wise_data_arr[$month][$is_confirmed]['CM'] += $v['PO_QUANTITY'] * $cm_cost_arr[$job_id] ;  
                    // for production
                    $month_wise_prod_arr[$month][$is_confirmed]['PROD_QTY'] += $prod_qty;  
                    $month_wise_prod_arr[$month][$is_confirmed]['MINUTES'] += $prod_qty *  $v['SMV_PCS'] ;  
                    $month_wise_prod_arr[$month][$is_confirmed]['FOB'] +=  $prod_qty * $v['UNIT_PRICE'];  
                    $month_wise_prod_arr[$month][$is_confirmed]['CM'] += $prod_qty * $cm_cost_arr[$job_id] ;  
                } 
            }
        }
    } 
   }
//    echo "<pre>";
//    print_r(  $month_wise_prod_arr);die;
  
//    die;
//    echo $cm_cosst_res; 
     
         
?>
<style>
    #report_table tbody{
        background: #fff;
    }
</style>
    <body>
        <div align="center"> 
            <div style="margin-bottom:20px">
                <h3>Month Wise Order Booking Vs Production</h3>
                <p>Company Name : <span id='Company Name'></span></p>
                <p>Date Range From To : <span id='data_range'></span></p>
            </div>
            <fieldset style="width:1200px;"> 
                <table width="1200" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="report_table">
                    <!-- Booking -->
                    <thead>
                        <tr>
                            <th colspan="13" >Booking</th>
                        </tr>
                        <tr>
                            <th rowspan="2" width='80px'>Month</th>
                            <th colspan="4" >Confirm</th>
                            <th colspan="4">Projection</th>
                            <th colspan="4">Total</th>
                        </tr> 
                        <tr>
                            <th  width='80px'>Qty</th>
                            <th  width='80px'>Minute</th>
                            <th  width='80px'>CM</th>
                            <th  width='80px'>Value</th>
                            <th  width='80px'>Qty</th>
                            <th width='80px'>Minute</th>
                            <th width='80px'>CM</th>
                            <th width='80px'>Value</th>
                            <th width='80px'>Qty</th>
                            <th width='80px'>Minute</th>
                            <th width='80px'>CM</th>
                            <th width='80px'>Value</th>
                        </tr> 
                    </thead> 
                    <tbody>
                        <?php  
                            $gt_qty_conf= $gt_min_conf =  $gt_cm_conf = $gt_fob_conf = $gt_qty_not_conf =  $gt_min_not_conf = $gt_cm_not_conf =  $gt_fob_not_conf = 0;  
                            foreach( $month_wise_data_arr as $m => $v)
                            {  

                                $gt_qty_conf += $v[1]['PO_QUANTITY'];
                                $gt_min_conf += $v[1]['MINUTES'];
                                $gt_cm_conf += $v[1]['CM'];
                                $gt_fob_conf += $v[1]['FOB'];

                                $gt_qty_not_conf += $v[2]['PO_QUANTITY']; 
                                $gt_min_not_conf += $v[2]['MINUTES']; 
                                $gt_cm_not_conf += $v[2]['CM']; 
                                $gt_fob_not_conf += $v[2]['FOB']; 

                                $total_po_qty =  $v[1]['PO_QUANTITY'] + $v[2]['PO_QUANTITY'];
                                $total_minutes =  $v[1]['MINUTES'] + $v[2]['MINUTES'];
                                $total_cm =  $v[1]['CM'] + $v[2]['CM'];
                                $total_fob =  $v[1]['FOB'] + $v[2]['FOB'];
                        ?>
                        <tr>
                            <td width='80px'><?= $m  ?></td>
                            <td width='80px'><?= $v[1]['PO_QUANTITY']  ?></td>
                            <td width='80px'><?= round($v[1]['MINUTES'])  ?></td>
                            <td width='80px'><?= round($v[1]['CM'])  ?></td>
                            <td width='80px'><?= round($v[1]['FOB'])  ?></td>
                            <td width='80px'><?= $v[2]['PO_QUANTITY']  ?></td>
                            <td width='80px'><?= round($v[2]['MINUTES']) ?></td>
                            <td width='80px'><?= round($v[2]['CM'])  ?></td>
                            <td width='80px'><?= round($v[2]['FOB'])  ?></td>
                            <td width='80px'><?= $total_po_qty ?></td>
                            <td width='80px'><?= round($total_minutes) ?></td>
                            <td width='80px'><?= round($total_cm) ?></td>
                            <td width='80px'><?= round($total_fob)  ?></td>
                        </tr>
                        <?php
                            }
                            // echo '<pre>';
                            // print_r($gt);
                            // die;
                        ?> 
                        <tr>
                            <td width='80px'> <b> Total </b> </td>
                            <td width='80px'> <b> <?= $gt_qty_conf ?> </b> </td>
                            <td width='80px'> <b> <?= round( $gt_min_conf) ?></b> </td>
                            <td width='80px'> <b> <?= round( $gt_cm_conf) ?> </b> </td>
                            <td width='80px'> <b> <?= round($gt_fob_conf) ?> </b> </td>
                            <td width='80px'> <b> <?= $gt_qty_not_conf ?> </b> </td>
                            <td width='80px'> <b><?= round( $gt_min_not_conf) ?> </b> </td>
                            <td width='80px'> <b> <?= round( $gt_cm_not_conf) ?></b> </td>
                            <td width='80px'> <b> <?= round($gt_fob_not_conf) ?> </b> </td>
                            <td width='80px'> <b> <?= $gt_qty_conf +  $gt_qty_not_conf ?> </b> </td>
                            <td width='80px'> <b> <?= round($gt_min_conf +  $gt_min_not_conf ) ?> </b> </td>
                            <td width='80px'> <b> <?= round($gt_cm_conf +  $gt_cm_not_conf ) ?> </b> </td>
                            <td width='80px'> <b>  <?= round( $gt_fob_conf + $gt_fob_not_conf) ?> </b> </td>
                        </tr>
                    </tbody>
                     <!-- Production -->
                    <thead>
                        <tr>
                            <th colspan="13">Production</th>
                        </tr>
                        <tr>
                            <th rowspan="2" width='80px'>Month</th>
                            <th colspan="4">Confirm</th>
                            <th colspan="4">Projection</th>
                            <th colspan="4">Total</th>
                        </tr>
                        <tr>
                            <th width='80px'>Qty</th>
                            <th width='80px'>Minute</th>
                            <th width='80px'>CM</th>
                            <th width='80px'>Value</th>
                            <th width='80px'>Qty</th>
                            <th width='80px'>Minute</th>
                            <th width='80px'>CM</th>
                            <th width='80px'>Value</th>
                            <th width='80px'>Qty</th>
                            <th width='80px'>Minute</th>
                            <th width='80px'>CM</th>
                            <th width='80px'>Value</th>
                        </tr> 
                    </thead> 
                    <tbody>
                        <?php  
                            $prod_gt_qty_conf= $prod_gt_min_conf =  $prod_gt_cm_conf = $prod_gt_fob_conf = $prod_gt_qty_not_conf =  $prod_gt_min_not_conf = $prod_gt_cm_not_conf =  $prod_gt_fob_not_conf = 0;  
                            foreach( $month_wise_prod_arr as $m => $v)
                            {  
                                // print_r($v);
                            //     echo  $v[1]['FOB'];
                            //   die;
                                $prod_gt_qty_conf += $v[1]['PROD_QTY'];
                                $prod_gt_min_conf += $v[1]['MINUTES'];
                                $prod_gt_cm_conf += $v[1]['CM'];
                                $prod_gt_fob_conf += $v[1]['FOB'];

                                $prod_gt_qty_not_conf += $v[2]['PROD_QTY']; 
                                $prod_gt_min_not_conf += $v[2]['MINUTES']; 
                                $prod_gt_cm_not_conf += $v[2]['CM']; 
                                $prod_gt_fob_not_conf += $v[2]['FOB']; 

                                $prod_total_po_qty =  $v[1]['PROD_QTY'] + $v[2]['PROD_QTY'];
                                $prod_total_minutes =  $v[1]['MINUTES'] + $v[2]['MINUTES'];
                                $prod_total_cm =  $v[1]['CM'] + $v[2]['CM'];
                                $prod_total_fob =  $v[1]['FOB'] + $v[2]['FOB'];
                        ?>
                        <tr>
                            <td width='80px'><?= $m  ?></td>
                            <td width='80px'><?= $v[1]['PROD_QTY']  ?></td>
                            <td width='80px'><?= round($v[1]['MINUTES'])  ?></td>
                            <td width='80px'><?= round($v[1]['CM'])  ?></td>
                            <td width='80px'><?= round($v[1]['FOB'])  ?></td>
                            <td width='80px'><?= $v[2]['PROD_QTY']  ?></td>
                            <td width='80px'><?= round($v[2]['MINUTES']) ?></td>
                            <td width='80px'><?= round($v[2]['CM'])  ?></td>
                            <td width='80px'><?= round($v[2]['FOB'])  ?></td>
                            <td width='80px'><?= $prod_total_po_qty ?></td>
                            <td width='80px'><?= round($prod_total_minutes) ?></td>
                            <td width='80px'><?= round($prod_total_cm) ?></td>
                            <td width='80px'><?= round($prod_total_fob)  ?></td>
                        </tr>
                        <?php
                            }
                            // echo '<pre>';
                            // print_r($gt);
                            // die;
                        ?> 
                        <tr>
                            <td width='80px'> <b> Total </b> </td>
                            <td width='80px'> <b> <?= $prod_gt_qty_conf ?> </b> </td>
                            <td width='80px'> <b> <?= round( $prod_gt_min_conf) ?></b> </td>
                            <td width='80px'> <b> <?= round( $prod_gt_cm_conf) ?> </b> </td>
                            <td width='80px'> <b> <?= round($prod_gt_fob_conf) ?> </b> </td>
                            <td width='80px'> <b> <?= $prod_gt_qty_not_conf ?> </b> </td>
                            <td width='80px'> <b><?= round( $prod_gt_min_not_conf) ?> </b> </td>
                            <td width='80px'> <b> <?= round( $prod_gt_cm_not_conf) ?></b> </td>
                            <td width='80px'> <b> <?= round($prod_gt_fob_not_conf) ?> </b> </td>
                            <td width='80px'> <b> <?= $prod_gt_qty_conf + $prod_gt_qty_not_conf  ?> </b> </td>
                            <td width='80px'> <b> <?= round($prod_gt_min_conf +  $prod_gt_min_not_conf ) ?> </b> </td>
                            <td width='80px'> <b> <?= round($prod_gt_cm_conf +  $prod_gt_cm_not_conf ) ?> </b> </td>
                            <td width='80px'> <b>  <?= round( $prod_gt_fob_conf + $prod_gt_fob_not_conf) ?> </b> </td>
                        </tr>
                    </tbody>
                     <!-- balance -->
                    <thead>
                        <tr>
                            <th colspan="13">Balance</th>
                        </tr>
                        <tr>
                            <th rowspan="2" width='80px'>Month</th>
                            <th colspan="4">Confirm</th>
                            <th colspan="4">Projection</th>
                            <th colspan="4">Total</th>
                        </tr>
                        <tr>
                            <th width='80px'>Qty</th>
                            <th width='80px'>Minute</th>
                            <th width='80px'>CM</th>
                            <th width='80px'>Value</th>
                            <th width='80px'>Qty</th>
                            <th width='80px'>Minute</th>
                            <th width='80px'>CM</th>
                            <th width='80px'>Value</th>
                            <th width='80px'>Qty</th>
                            <th width='80px'>Minute</th>
                            <th width='80px'>CM</th>
                            <th width='80px'>Value</th>
                        </tr> 
                    </thead> 
                    <tbody>
                        <tr>
                            <td width='80px'>January'22</td>
                            <td width='80px'>3650785</td>
                            <td width='80px'>56994869.28</td>
                            <td width='80px'>8456753.685</td>
                            <td width='80px'>12747243.32</td>
                            <td width='80px'>70000</td>
                            <td width='80px'>1540000</td>
                            <td width='80px'>84000</td>
                            <td width='80px'>350000</td>
                            <td width='80px'>3720785</td>
                            <td width='80px'>58534869.28</td>
                            <td width='80px'>8540753.685</td>
                            <td width='80px'>13097243.32</td>
                        </tr>
                        <tr>
                            <td width='80px'>February'22</td>
                            <td width='80px'>3650785</td>
                            <td width='80px'>56994869.28</td>
                            <td width='80px'>8456753.685</td>
                            <td width='80px'>12747243.32</td>
                            <td width='80px'>70000</td>
                            <td width='80px'>1540000</td>
                            <td width='80px'>84000</td>
                            <td width='80px'>350000</td>
                            <td width='80px'>3720785</td>
                            <td width='80px'>58534869.28</td>
                            <td width='80px'>8540753.685</td>
                            <td width='80px'>13097243.32</td>
                        </tr>
                        <tr>
                            <td width='80px'> <b> Total </b> </td>
                            <td width='80px'> <b> 3650785 </b> </td>
                            <td width='80px'> <b> 56994869.28 </b> </td>
                            <td width='80px'> <b> 8456753.685 </b> </td>
                            <td width='80px'> <b> 12747243.32 </b> </td>
                            <td width='80px'> <b> 70000 </b> </td>
                            <td width='80px'> <b> 1540000 </b> </td>
                            <td width='80px'> <b> 84000 </b> </td>
                            <td width='80px'> <b> 350000 </b> </td>
                            <td width='80px'> <b> 3720785 </b> </td>
                            <td width='80px'> <b> 58534869.28 </b> </td>
                            <td width='80px'> <b> 8540753.685 </b> </td>
                            <td width='80px'> <b> 13097243.32 </b> </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset> 
        </div>
    </body>            
</html> 
<?php        
	}  

	if($type == 2) {
		echo "hello from type 2";
	}  

	if($type == 3) {
		echo "hello from type 3";
	}  

	if($type == 4) {
		echo "hello from type 4";
	}  
}  
?>  