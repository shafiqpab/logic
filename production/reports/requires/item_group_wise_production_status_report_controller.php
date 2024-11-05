<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.fabrics.php');
require_once('../../../includes/class4/class.others.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_buyer")
    {
        echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-Select Buyer-", $selected, "");
        exit();
    }

// if ($action == "eval_multi_select") {
//     echo "set_multiselect('cbo_buyer_name','0','0','','0');\n";
//     // echo "setTimeout[($('#floor_td a').attr('onclick','disappear_list(cbo_floor,0);getFloorId();') ,3000)];\n";
//     exit();
// }

if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";
//item style------------------------------//

if($action=="generate_report")
{
   
	$process = array( &$_POST );
	// echo "<pre>";
	// print_r($process);
	extract(check_magic_quote_gpc( $process ));	
	$cbo_company_name=str_replace("'","", $cbo_company_name);
	$cbo_buyer_name=str_replace("'","", $cbo_buyer_name);
    $cbo_garments_item=str_replace("'","", $cbo_garments_item);
    $cbo_order_status=str_replace("'","", $cbo_order_status);
	$cbo_shipment_status=str_replace("'","", $cbo_shipment_status);
	$cbo_year=str_replace("'","", $cbo_year);	
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);


    $sql_cond="";
    
	if($cbo_company_name)$sql_cond.=" and a.company_name='$cbo_company_name'";
    if($cbo_buyer_name)$sql_cond.=" and a.buyer_name='$cbo_buyer_name'";
    if($cbo_garments_item)$sql_cond.=" and c.item_number_id='$cbo_garments_item'";
    if($cbo_order_status)$sql_cond.=" and b.is_confirmed='$cbo_order_status'";
    //if($cbo_order_status) $sql_cond=" and a.is_confirmed=$cbo_order_status";
    if($cbo_shipment_status)$sql_cond.=" and b.SHIPING_STATUS='$cbo_shipment_status'";
    if($cbo_year)$sql_cond.=" and to_char(a.insert_date,'YYYY')=$cbo_year";
    // $sql_cond. = " and YEAR(c.insert_date)=$cbo_year";
	if($txt_date_from !="" && $txt_date_to !="" )
    {
		
        $sql_cond.=" and b.SHIPMENT_DATE between'$txt_date_from' and '$txt_date_to'";
	}	  


    $ex_cond="";
    
	if($cbo_company_name)$ex_cond.=" and a.company_name='$cbo_company_name'";
    if($cbo_buyer_name)$ex_cond.=" and a.buyer_name='$cbo_buyer_name'";
    if($cbo_garments_item)$ex_cond.=" and c.item_number_id='$cbo_garments_item'";
    if($cbo_order_status)$ex_cond.=" and b.is_confirmed='$cbo_order_status'";
    if($cbo_shipment_status)$ex_cond.=" and b.SHIPING_STATUS='$cbo_shipment_status'";
    if($cbo_year)$ex_cond.=" and to_char(a.insert_date,'YYYY')=$cbo_year";
    
	if($txt_date_from !="" && $txt_date_to !="" )
    {
		
        $ex_cond.=" and b.SHIPMENT_DATE between'$txt_date_from' and '$txt_date_to'";
	}
	
    $item_library=return_library_array( "SELECT id,ITEM_NAME from LIB_GARMENT_ITEM", "id", "ITEM_NAME");
    $company_library=return_library_array( "SELECT id,COMPANY_NAME from lib_company", "id", "COMPANY_NAME");
  
    ?>
         
        
    <?                   
    ob_start();
    
    // ===========================================Item Group Wise Start ======================================// 
	if($type==1) 
	{         
        //============================================== Main-Query Start ============================================================//
        $sql="SELECT a.COMPANY_NAME,a.BUYER_NAME,b.SHIPMENT_DATE,a.INSERT_DATE,b.SHIPING_STATUS,b.ID as PO_ID, b.IS_CONFIRMED,c.ITEM_NUMBER_ID,c.ORDER_QUANTITY,d.PRODUCTION_TYPE,d.PRODUCTION_QNTY from WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN b, WO_PO_COLOR_SIZE_BREAKDOWN c ,PRO_GARMENTS_PRODUCTION_DTLS d where a.id=b.job_id and a.id=c.job_id and b.id=c.PO_BREAK_DOWN_ID and c.id=d.COLOR_SIZE_BREAK_DOWN_ID and d.PRODUCTION_TYPE in(1,5,8) and a.STATUS_ACTIVE= 1 and a.IS_DELETED=0 and b.STATUS_ACTIVE= 1 and b.IS_DELETED=0 and c.STATUS_ACTIVE= 1 and c.IS_DELETED=0 and d.STATUS_ACTIVE= 1 and d.IS_DELETED=0  $sql_cond ";
       // echo $sql;die;
        $sql_result=sql_select($sql);

        if(count($sql_result)==0)
        {
            echo " <div align='center'><h3 style='color:red'> No Data Found. </h3></div>";die;
        }

        $data_array=array();
        $po_id_arr=array();

        foreach ($sql_result as $k) 
        {
            $po_id_arr[$k['PO_ID']]=$k['PO_ID'];

            $data_array[$k['ITEM_NUMBER_ID']]['order_quantity'] +=$k['ORDER_QUANTITY'];

            if($k['PRODUCTION_TYPE'] ==1)//cutting
            {		
                $data_array[$k['ITEM_NUMBER_ID']]['cutting_qty'] +=$k['PRODUCTION_QNTY'];
            } 
            if ($k['PRODUCTION_TYPE'] ==5) 
            {
                $data_array[$k['ITEM_NUMBER_ID']]['sewing_qty'] +=$k['PRODUCTION_QNTY'];
            }
            if ($k['PRODUCTION_TYPE'] ==8) 
            {
                $data_array[$k['ITEM_NUMBER_ID']]['pack_qty'] +=$k['PRODUCTION_QNTY'];
            }
             
        }
        //   echo"<pre>";print_r($data_array);die;

        //============================================== Main-Query End ============================================================//

          
        //============================================== febric_rcv Start ===================================//

        $febric_rcv_sql="SELECT a.COMPANY_NAME,a.BUYER_NAME,b.SHIPMENT_DATE,a.INSERT_DATE,b.SHIPING_STATUS,b.ID as PO_ID, b.IS_CONFIRMED,c.ITEM_NUMBER_ID,c.ORDER_QUANTITY,e.RECEIVE_QNTY from WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN b, WO_PO_COLOR_SIZE_BREAKDOWN c ,INV_RECEIVE_MASTER d,PRO_FINISH_FABRIC_RCV_DTLS e,ORDER_WISE_PRO_DETAILS f where a.id=b.job_id and a.id=c.job_id and b.id=c.PO_BREAK_DOWN_ID and d.id=e.MST_ID and e.id=dtls_id and b.id=f.PO_BREAKDOWN_ID  and a.job_no=e.job_no and a.STATUS_ACTIVE= 1 and a.IS_DELETED=0 and b.STATUS_ACTIVE= 1 and b.IS_DELETED=0 and c.STATUS_ACTIVE= 1 and c.IS_DELETED=0 and d.STATUS_ACTIVE= 1 and d.IS_DELETED=0 and e.STATUS_ACTIVE= 1 and e.IS_DELETED=0 and f.STATUS_ACTIVE= 1 and f.IS_DELETED=0and d.entry_form in(37,68) and f.entry_form in(37,68) $sql_cond ";
        //echo $febric_rcv_sql;die;
        $febric_sql=sql_select($febric_rcv_sql);
        $febric_rcv_arr=array();
        foreach ($febric_sql as  $a) 
        {   if ($a['ENTRY_FORM']==37) 
			{
            $febric_rcv_arr[$a['ITEM_NUMBER_ID']]['febric_rcv'] +=$a['RECEIVE_QNTY'];
            }else
            {
                $febric_rcv_arr[$a['ITEM_NUMBER_ID']]['febric_rcv'] +=$a['RECEIVE_QNTY'];
            }
        }
        // echo"<pre>";print_r($febric_rcv_arr);die;

        //============================================== FEBRIC_RCV End ===================================//

        //============================================== FEBRIC  Require Start ===================================//
        $poIds = implode(",",$po_id_arr);
        $condition= new condition();     
        $condition->po_id_in($poIds);     
        $condition->init();
        $fabric= new fabric($condition);
        // echo $fabric->getQuery();die;
        $fabric_costing_arr = $fabric->getQtyArray_by_orderAndGmtsitem_knitAndwoven_greyAndfinish();
        // echo"<pre>";print_r($fabric_costing_arr);die;
        $fabric_require=array();
        foreach($fabric_costing_arr as $knit => $kint_val)
        {
            foreach($kint_val as $finish => $finish_val)
            {
                foreach($finish_val as $po =>$po_id)
                {
                    foreach($po_id as $item =>$item_arr)
                    {
                       $fabric_require[$item]= array_sum($item_arr);
                    }

                }
            }

        }
        // echo"<pre>";print_r($fabric_cos);die;

         //============================================== FEBRIC  Require End ===================================//

        


        //===================================== Shipment-Query Start ============================================================//

        $sql_shipment=" SELECT a.ID AS JOB_ID,a.COMPANY_NAME,a.BUYER_NAME,b.ID AS PO_ID,b.IS_CONFIRMED,b.SHIPING_STATUS,c.ITEM_NUMBER_ID,d.PRODUCTION_QNTY,g.DELIVERY_DATE  FROM WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b,WO_PO_COLOR_SIZE_BREAKDOWN c,PRO_EX_FACTORY_MST f,PRO_EX_FACTORY_DELIVERY_MST  g,PRO_EX_FACTORY_DTLS d WHERE a.id = c.job_id AND a.id = b.job_id AND b.id = c.po_break_down_id AND b.id = f.po_break_down_id AND g.id = f.delivery_mst_id and c.id=d.color_size_break_down_id and f.id=d.mst_id  AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1  AND d.is_deleted = 0  AND f.is_deleted = 0 AND f.status_active = 1 AND g.is_deleted = 0 AND g.status_active = 1  $ex_cond  order by a.id,b.id,c.id  ";
        // echo $sql_shipment;die;

        $shipment_sql=sql_select($sql_shipment);
        $shipment_array=array();
        foreach ($shipment_sql as  $val)
        {
            $shipment_array[$val['ITEM_NUMBER_ID']]['ship_qnty'] +=$val['PRODUCTION_QNTY'];
        }
        //    echo"<pre>";print_r($shipment_array);die;

        //============================================== Shipment-Query-End ==========================================//



        ob_start();
        ?>
       
        <br>
        <fieldset style="width:1100px">
            
            <div style="width:1100px">
                
                <table width="1080">
                        
                        <tr class="form_caption" style="border:none;">
                            <td align="center" style="border:none;font-size:20px; font-weight:bold" >Item Group Wise Order Status
                            </td>
                        </tr>
                        <tr style="border:none;">
                            <td  align="center" style="border:none; font-size:18px; font-weight:bold">
                                Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>
                            </td>
                        </tr>
                        <tr style="border:none;">
                            <td align="center" style="border:none;font-size:18px; font-weight:bold">Date :
                                <?
                                if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
                                {
                                    echo "From $txt_date_from To $txt_date_to" ;
                                }
                                ?>
                        </td>
                        </tr>
                </table>
            </div>   
                <br>
                    <div id="scroll_body">           
                        <table width="1100" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                            <thead>
                                <div style="width:1080px; float:center; font-size: 20px"><strong>Item Group Wise Order Information</strong></div>
                                <tr>
                                    <th  width="30">SL</th>
                                    <th  width="150">Item Group</th>
                                    <th  width="80">Order Qty.(Pcs)</th>
                                    <th  width="80">Fabric Require</th>
                                    <th  width="80">Fin.Fabric Received</th>
                                    <th  width="80">Fin.Fabric Recv Due</th>
                                    <th  width="80">Cutting</th>
                                    <th  width="80">Cutting Balance</th>
                                    <th  width="80">Sewing Qty</th>
                                    <th  width="80">Sewing Balance</th>
                                    <th  width="80">Packing And Finishing</th>
                                    <th  width="80">Packing And Finishing Balance</th>
                                    <th  width="80">Shipment</th>
                                    <th  width="80">Shipment Balance</th>
                                    

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                 $i=1;
                                 $total_order_qnty =0;
                                 $total_cut_qnty =0;
                                 $total_cut_balance =0;
                                 $total_sewing_qnty =0;
                                 $total_sewing_balance =0;
                                 $total_packing_qnty =0;
                                 $total_packing_balance =0;
                                 $total_ship_qnty =0;
                                 $total_ship_balance =0;
                                 $finish_feb_rcv_qty_ttl=0;


                                 foreach ($data_array as $item_key => $value)
                                 {
                                        $cutting_balance =($value['order_quantity']-$value['cutting_qty']);
                                        $sewing_balance = ($value['order_quantity']-$value['sewing_qty']);
                                        $packing_balance = ($value['order_quantity']-$value['pack_qty']);
                                        $ship_qnty = $shipment_array[$item_key]['ship_qnty'];
                                        $ship_balance = ( $value['order_quantity'] - $shipment_array[$item_key]['ship_qnty'] ) ;
                                        $finish_feb_rcv_qty =$febric_rcv_arr[$item_key]['febric_rcv'];
                                        $fin_fabric_recv_due =($fabric_require[$item_key] -  $finish_feb_rcv_qty);

                                        if ($i%2==0) $bgcolor="#E9F3FF";									
									    else $bgcolor="#FFFFFF";	
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('str_<? echo  $i; ?>','<? echo $bgcolor; ?>')" id="str_<? echo  $i; ?>">
                                        <td  width="30"><?=$i;?></td>
                                        <td  width="150"><?=$item_library[$item_key];?></td>
                                        <td  width="80" align="right"><?=$value['order_quantity'];?></td>
                                        <td  width="80" align="right"><?=number_format($fabric_require[$item_key],2);?></td>
                                        <td  width="80" align="right"><?=$finish_feb_rcv_qty ;?></td>
                                        <td  width="80" align="right"><?=number_format($fin_fabric_recv_due,2);?></td>
                                        <td  width="80" align="right"><?=$value['cutting_qty'];?></td>
                                        <td  width="80" align="right"><?=$cutting_balance;?></td>
                                        <td  width="80" align="right"><?=$value['sewing_qty'];?></td>
                                        <td  width="80" align="right"><?=$sewing_balance ;?></td>
                                        <td  width="80" align="right"><?=$value['pack_qty'];?></td>
                                        <td  width="80" align="right"><?=$packing_balance;?></td>
                                        <td  width="80" align="right"><?=$ship_qnty;?></td>
                                        <td  width="80" align="right"><?=$ship_balance;?></td>                
                                    </tr>   
                                    <?           
                                     $i++;
                                     $total_order_qnty += $value['order_quantity'];
                                     $total_cut_qnty += $value['cutting_qty'];
                                     $total_cut_balance += $cutting_balance;
                                     $total_sewing_qnty += $value['sewing_qty'];
                                     $total_sewing_balance += $sewing_balance;
                                     $total_packing_qnty +=$value['pack_qty'];
                                     $total_packing_balance +=$packing_balance;
                                     $total_ship_qnty +=$ship_qnty;
                                     $total_ship_balance +=$ship_balance;     
                                     $total_fabric_require_qnty += $fabric_require[$item_key];   
                                     $finish_feb_rcv_qty_ttl += $finish_feb_rcv_qty;
                                     $total_fin_fabric_recv_due    +=$fin_fabric_recv_due;
                                   
                                 }
                                 ?>                                    
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th  width="30"></th>
                                    <th  width="150"><b>Grand Total</b></th>
                                    <th  width="80" align="right"><?=$total_order_qnty;?></th>
                                    <th  width="80" align="right"><?=number_format($total_fabric_require_qnty,2);?></th>
                                    <th  width="80" align="right"><?=$finish_feb_rcv_qty_ttl;?></th>
                                    <th  width="80" align="right"><?=number_format($total_fin_fabric_recv_due,2)?></th>
                                    <th  width="80" align="right"><?=$total_cut_qnty;?></th>
                                    <th  width="80" align="right"><?=$total_cut_balance;?></th>
                                    <th  width="80" align="right"><?=$total_sewing_qnty;?></th>
                                    <th  width="80" align="right"><?=$total_sewing_balance;?></th>
                                    <th  width="80" align="right"><?=$total_packing_qnty;?></th>
                                    <th  width="80" align="right"><?=$total_packing_balance;?></th>
                                    <th  width="80" align="right"><?=$total_ship_qnty;?></th>
                                    <th  width="80" align="right"><?=$total_ship_balance;?></th>      
                                </tr> 
                            </tfoot>
                        </table>      
                    </div>    
            </div> 
        </fieldset>
        <?
    }
    // ===========================================Item Group Wise  END ======================================//


    // -------------------------------------Month Wise Start------------------------------------------------//
    if($type==2) 
	{       
        // $month= "TO_CHAR (b.SHIPMENT_DATE, 'Mon-dd')";
        $sql="SELECT a.COMPANY_NAME,a.BUYER_NAME,b.SHIPMENT_DATE,b.ID as PO_ID,TO_CHAR(b.SHIPMENT_DATE, 'Mon-dd') as REF_MONTH,b.SHIPING_STATUS,b.IS_CONFIRMED,c.ITEM_NUMBER_ID,c.ORDER_QUANTITY,d.PRODUCTION_TYPE,d.PRODUCTION_QNTY from WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN b, WO_PO_COLOR_SIZE_BREAKDOWN c ,PRO_GARMENTS_PRODUCTION_DTLS d where a.id=b.job_id and a.id=c.job_id and b.id=c.PO_BREAK_DOWN_ID and c.id=d.COLOR_SIZE_BREAK_DOWN_ID and d.PRODUCTION_TYPE in(1,5,8) and a.STATUS_ACTIVE= 1 and a.IS_DELETED=0 and b.STATUS_ACTIVE= 1 and b.IS_DELETED=0 and c.STATUS_ACTIVE= 1 and c.IS_DELETED=0 and d.STATUS_ACTIVE= 1 and d.IS_DELETED=0  $sql_cond order by b.SHIPMENT_DATE ASC ";
        // echo $sql;die;
         $sql_result=sql_select($sql);
 
         if(count($sql_result)==0)
         {
             echo " <div align='center'><h3 style='color:red'> No Data Found. </h3></div>";die;
         }
 
         $data_array=array();
         $po_id_arr =array();
         foreach ($sql_result as $v) 
         {
            // $ref_month_arr[$v['REF_MONTH']]=$v['REF_MONTH'];
            $po_id_arr[$v['PO_ID']]=$v['PO_ID'];
             $data_array[$v['REF_MONTH']][$v['ITEM_NUMBER_ID']]['order_quantity'] +=$v['ORDER_QUANTITY'];
 
             if($v['PRODUCTION_TYPE'] ==1)//cutting
             {		
                 $data_array[$v['REF_MONTH']][$v['ITEM_NUMBER_ID']]['cutting_qty'] +=$v['PRODUCTION_QNTY'];
             } 
             if ($v['PRODUCTION_TYPE'] ==5) 
             {
                 $data_array[$v['REF_MONTH']][$v['ITEM_NUMBER_ID']]['sewing_qty'] +=$v['PRODUCTION_QNTY'];
             }
             if ($v['PRODUCTION_TYPE'] ==8) 
             {
                 $data_array[$v['REF_MONTH']][$v['ITEM_NUMBER_ID']]['pack_qty'] +=$v['PRODUCTION_QNTY'];
             }
              
         }
        //    echo"<pre>";print_r($data_array);die;

        //============================================== febric_rcv Start ===================================//

        $febric_rcv_sql="SELECT a.COMPANY_NAME,a.BUYER_NAME,b.SHIPMENT_DATE,TO_CHAR(b.SHIPMENT_DATE, 'Mon-dd') as REF_MONTH,b.SHIPING_STATUS,b.ID as PO_ID, b.IS_CONFIRMED,c.ITEM_NUMBER_ID,c.ORDER_QUANTITY,e.RECEIVE_QNTY from WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN b, WO_PO_COLOR_SIZE_BREAKDOWN c ,INV_RECEIVE_MASTER d,PRO_FINISH_FABRIC_RCV_DTLS e,ORDER_WISE_PRO_DETAILS f where a.id=b.job_id and a.id=c.job_id and b.id=c.PO_BREAK_DOWN_ID and d.id=e.MST_ID and e.id=dtls_id and b.id=f.PO_BREAKDOWN_ID  and a.job_no=e.job_no and a.STATUS_ACTIVE= 1 and a.IS_DELETED=0 and b.STATUS_ACTIVE= 1 and b.IS_DELETED=0 and c.STATUS_ACTIVE= 1 and c.IS_DELETED=0 and d.STATUS_ACTIVE= 1 and d.IS_DELETED=0 and e.STATUS_ACTIVE= 1 and e.IS_DELETED=0 and f.STATUS_ACTIVE= 1 and f.IS_DELETED=0and d.entry_form in(37,68) and f.entry_form in(37,68) $sql_cond ";
        //echo $febric_rcv_sql;die;
        $febric_sql=sql_select($febric_rcv_sql);
        $febric_rcv_arr=array();
        foreach ($febric_sql as  $a) 
        {   if ($a['ENTRY_FORM']==37) 
			{
            $febric_rcv_arr[$a['REF_MONTH']][$a['ITEM_NUMBER_ID']]['febric_rcv'] +=$a['RECEIVE_QNTY'];
            }else
            {
                $febric_rcv_arr[$a['REF_MONTH']][$a['ITEM_NUMBER_ID']]['febric_rcv'] +=$a['RECEIVE_QNTY'];
            }
        }
        // echo"<pre>";print_r($febric_rcv_arr);die;

        $poIds = implode(",",$po_id_arr);
        $condition= new condition();     
        $condition->po_id_in($poIds);     
        $condition->init();
        $fabric= new fabric($condition);
        // echo $fabric->getQuery();die;
        $fabric_costing_arr = $fabric->getQtyArray_by_orderAndGmtsitem_knitAndwoven_greyAndfinish();
        // echo"<pre>";print_r($fabric_costing_arr);die;
        $fabric_require=array();
        foreach($fabric_costing_arr as $knit => $kint_val)
        {
            foreach($kint_val as $finish => $finish_val)
            {
                foreach($finish_val as $po =>$po_id)
                {
                    foreach($po_id as $item =>$item_arr)
                    {
                       $fabric_require[$item]= array_sum($item_arr);
                    }

                }
            }

        }
    //    echo"<pre>";print_r($fabric_require);die;

 
         $sql_shipment=" SELECT a.ID AS JOB_ID,a.COMPANY_NAME,a.BUYER_NAME,b.ID AS PO_ID,TO_CHAR(b.SHIPMENT_DATE, 'Mon-dd') as REF_MONTH,b.IS_CONFIRMED,b.SHIPING_STATUS,c.ITEM_NUMBER_ID,d.PRODUCTION_QNTY,g.DELIVERY_DATE  FROM WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b,WO_PO_COLOR_SIZE_BREAKDOWN c,PRO_EX_FACTORY_MST f,PRO_EX_FACTORY_DELIVERY_MST  g,PRO_EX_FACTORY_DTLS d WHERE a.id = c.job_id AND a.id = b.job_id AND b.id = c.po_break_down_id AND b.id = f.po_break_down_id AND g.id = f.delivery_mst_id and c.id=d.color_size_break_down_id and f.id=d.mst_id  AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1  AND d.is_deleted = 0  AND f.is_deleted = 0 AND f.status_active = 1 AND g.is_deleted = 0 AND g.status_active = 1  $ex_cond  order by a.id,b.id,c.id  ";
         // echo $sql_shipment;die;
 
         $shipment_sql=sql_select($sql_shipment);
         $shipment_array=array();
         foreach ($shipment_sql as  $r)
         {
             $shipment_array[$r['REF_MONTH']][$r['ITEM_NUMBER_ID']]['ship_qnty'] +=$r['PRODUCTION_QNTY'];
         }
            // echo"<pre>";print_r($shipment_array);die;
         ?>  
        
      
        <br>
        <fieldset style="width:1100px">
            
            <div style="width:1100px">
                
                <table width="1080">
                        
                        <tr class="form_caption" style="border:none;">
                            <td align="center" style="border:none;font-size:20px; font-weight:bold" >Monthly Order Status
                            </td>
                        </tr>
                        <tr style="border:none;">
                            <td  align="center" style="border:none; font-size:18px;font-weight:bold">
                                Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>
                            </td>
                        </tr>
                        <tr style="border:none;">
                            <td align="center" style="border:none;font-size:18px; font-weight:bold">Date :
                                <?
                                if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
                                {
                                    echo "From $txt_date_from To $txt_date_to" ;
                                }
                                ?>
                        </td>
                        </tr>
                </table>
            </div>   
                <br>
                    <div id="scroll_body">           
                        <table width="1200" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                            <thead>
                                <div style="width:1180px; float:center; font-size: 20px"><strong>Monthly Order Information</strong></div>
                                <tr>
                                    <th  width="30">SL</th>
                                    <th  width="150">Rfi Month</th>
                                    <th  width="150">Item Group</th>
                                    <th  width="80">Order Qty.(Pcs)</th>
                                    <th  width="80">Fabric Require</th>
                                    <th  width="80">Fin.Fabric Received</th>
                                    <th  width="80">Fin.Fabric Recv Due</th>
                                    <th  width="80">Cutting</th>
                                    <th  width="80">Cutting Balance</th>
                                    <th  width="80">Sewing Qty</th>
                                    <th  width="80">Sewing Balance</th>
                                    <th  width="80">Packing And Finishing</th>
                                    <th  width="80">Packing And Finishing Balance</th>
                                    <th  width="80">Shipment</th>
                                    <th  width="80">Shipment Balance</th>
                                    

                                </tr>
                            </thead>
                            <tbody>
                                <?
                                    $j=1;
                                    $total_order_qnty =0;
                                    $total_cut_qnty =0;
                                    $total_cut_balance =0;
                                    $total_sewing_qnty =0;
                                    $total_sewing_balance =0;
                                    $total_packing_qnty =0;
                                    $total_packing_balance =0;
                                    $ship_qnty_total =0;
                                    $total_ship_balance =0;
                                    $fabric_require_qnty_total=0;
                                    $finish_feb_rcv_qty_ttl=0;
                                    $fin_fabric_recv_due_total=0;
                                    
                                    foreach ($data_array as $month => $month_val) 
                                    {
                                        $month_total_order_qnty =0;
                                        $month_total_cut_qnty =0;
                                        $month_cut_balance =0;
                                        $month_sewing_qnty =0;
                                        $month_sewing_balance =0;
                                        $month_packing_qnty =0;
                                        $month_packing_balance =0;
                                        $month_ship_qnty =0;
                                        $month_ship_balance =0;
                                        $month_fabric_require_qnty=0;
                                        $month_finish_feb_rcv_qty_ttl=0;
                                        $month_fin_fabric_recv_due_total=0;

                                        foreach ($month_val as $item => $row) 
                                        {
                                            // if (!in_array($month, $data_array))
                                            // {
                                            //     $data_array[] = $month;
                                            // }
                                            $cutting_balance =($row['order_quantity']-$row['cutting_qty']);
                                            $sewing_balance = ($row['order_quantity']-$row['sewing_qty']);
                                            $packing_balance = ($row['order_quantity']-$row['pack_qty']);
                                            $finish_feb_rcv_qty =$febric_rcv_arr[$month][$item]['febric_rcv'];
                                            $fin_fabric_recv_due =($fabric_require[$item] -  $finish_feb_rcv_qty);
                                            $ship_qnty = $shipment_array[$month][$item]['ship_qnty'];
                                            $ship_balance = ( $row['order_quantity'] - $shipment_array[$month][$item]['ship_qnty'] ) ;
                                            if ($j%2==0) $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
                                            ?>
                                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('str_<? echo  $j; ?>','<? echo $bgcolor; ?>')" id="str_<? echo  $j; ?>">
                                                <td  width="30"><?=$j;?></td>
                                                <td  width="80"><?=$month;?></td>
                                                <td  width="150"><?=$item_library[$item];?></td>
                                                <td  width="80" align="right"><?=$row['order_quantity'];?></td>
                                                <td  width="80" align="right"><?=number_format($fabric_require[$item],2);?></td>
                                                <td  width="80" align="right"><?=$finish_feb_rcv_qty;?></td>
                                                <td  width="80" align="right"><?=number_format($fin_fabric_recv_due)?></td>
                                                <td  width="80" align="right"><?=$row['cutting_qty'];?></td>
                                                <td  width="80" align="right"><?=$cutting_balance;?></td>
                                                <td  width="80" align="right"><?=$row['sewing_qty'];?></td>
                                                <td  width="80" align="right"><?=$sewing_balance ;?></td>
                                                <td  width="80" align="right"><?=$row['pack_qty'];?></td>
                                                <td  width="80" align="right"><?=$packing_balance;?></td>
                                                <td  width="80" align="right"><?=$ship_qnty;?></td>
                                                <td  width="80" align="right"><?=$ship_balance;?></td>           
                        
                                           </tr>     
                                         <?  
                                            $j++;
                                                $month_total_order_qnty += $row['order_quantity'];
                                                $month_total_cut_qnty += $row['cutting_qty'];
                                                $month_cut_balance += $cutting_balance;
                                                $month_sewing_qnty += $row['sewing_qty'];
                                                $month_sewing_balance += $sewing_balance;
                                                $month_packing_qnty += $row['pack_qty'];
                                                $month_packing_balance += $packing_balance;;
                                                $month_ship_qnty += $shipment_array[$month][$item]['ship_qnty'];;
                                                $month_ship_balance += $ship_balance;
                                                $month_fabric_require_qnty += $fabric_require[$item];  
                                                $month_finish_feb_rcv_qty_ttl += $febric_rcv_arr[$month][$item]['febric_rcv'];
                                                $month_fin_fabric_recv_due_total    += $fin_fabric_recv_due;


                                                $total_order_qnty += $row['order_quantity'];
                                                $total_cut_qnty += $row['cutting_qty'];
                                                $total_cut_balance += $cutting_balance;
                                                $total_sewing_qnty += $row['sewing_qty'];
                                                $total_sewing_balance += $sewing_balance;
                                                $total_packing_qnty +=$row['pack_qty'];
                                                $total_packing_balance +=$packing_balance;
                                                $ship_qnty_total += $shipment_array[$month][$item]['ship_qnty'];
                                                $total_ship_balance +=$ship_balance;    
                                                $fabric_require_qnty_total += $fabric_require[$item];  
                                                $finish_feb_rcv_qty_ttl   += $febric_rcv_arr[$month][$item]['febric_rcv']  ;
                                                $fin_fabric_recv_due_total  += $fin_fabric_recv_due;
                                        }  
                                            ?>
                                        <tr style="background-color:#D0E7D2 ;">
                                        
                                            <th colspan="3" align="right" style="font-size:15px;"><b><?=$month;?> Total</b></th>
                                            <th  width="80" align="right"><?=$month_total_order_qnty;?></th>
                                            <th  width="80" align="right"><?=number_format($month_fabric_require_qnty,2);?></th>
                                            <th  width="80" align="right"><?=$finish_feb_rcv_qty_ttl ;?></th>
                                            <th  width="80" align="right"><?=number_format($month_fin_fabric_recv_due_total,2);?></th>
                                            <th  width="80" align="right"><?=$month_total_cut_qnty;?></th>
                                            <th  width="80" align="right"><?=$month_cut_balance;?></th>
                                            <th  width="80" align="right"><?=$total_sewing_qnty;?></th>
                                            <th  width="80" align="right"><?=$month_sewing_balance;?></th>
                                            <th  width="80" align="right"><?=$month_packing_qnty;?></th>
                                            <th  width="80" align="right"><?=$month_packing_balance;?></th>
                                            <th  width="80" align="right"><?=$month_ship_qnty;?></th>
                                            <th  width="80" align="right"><?=$month_ship_balance;?></th> 

                                       </tr>    
                                      <?        
                                            
                                    }                                                  
                                    ?>                 
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" align="right" style="font-size:15px;"><b>Grand Total</b></th>
                                    <th  width="80" align="right"><?=$total_order_qnty;?></th>
                                    <th  width="80" align="right"><?=number_format($fabric_require_qnty_total,2);?></th>
                                    <th  width="80" align="right"><?=$finish_feb_rcv_qty_ttl;?></th>
                                    <th  width="80" align="right"><?=number_format($fin_fabric_recv_due_total);?></th>
                                    <th  width="80" align="right"><?=$total_cut_qnty;?></th>
                                    <th  width="80" align="right"><?=$total_cut_balance;?></th>
                                    <th  width="80" align="right"><?=$total_sewing_qnty;?></th>
                                    <th  width="80" align="right"><?=$sewing_balance;?></th>
                                    <th  width="80" align="right"><?=$total_packing_qnty;?></th>
                                    <th  width="80" align="right"><?=$packing_balance;?></th>
                                    <th  width="80" align="right"><?=$ship_qnty_total;?></th>
                                    <th  width="80" align="right"><?=$total_ship_balance;?></th> 

                                </tr>
                                
                            </tfoot>
                        </table> 
                    
                        
                    </div>
                    
            </div>
            
        </fieldset>
   
        <?
    }
    // ===========================================Month Wise  END ======================================//
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_id*.xls") as $filename) {
        @unlink($filename);
    }
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$type";

	exit();   
}






		