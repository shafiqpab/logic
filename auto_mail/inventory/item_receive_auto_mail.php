<?php
date_default_timezone_set("Asia/Dhaka");

header('Content-type:text/html; charset=utf-8');
session_start();

$date = '';
if(isset($_REQUEST['date'])){
    $date=$_REQUEST['date'];
}
 
include('../../includes/common.php');
require('../setting/mail_setting.php');

$store_library = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
$user_name_arr = return_library_array("select id, user_name from  user_passwd", 'id', 'user_name');
$current_date = change_date_format(date("Y-m-d H:i:s", strtotime(add_time(date("H:i:s",time()), 0))), '', '', 1);
$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))), '', '', 1);
$group_arr = return_library_array( "select id, item_name from  lib_item_group",'id','item_name');

if($date){
    $txt_date_to = $date;
}
else{
    $txt_date_from = $previous_date;
    $txt_date_to   = $current_date;
}
// print_r( $txt_date_to);
// exit;
 


$transSql="SELECT a.ID,
         a.TRANSACTION_TYPE,
         a.STORE_ID,
         a.MST_ID,
         a.PROD_ID,
         a.TRANSACTION_DATE,
         a.CONS_UOM,
         a.ITEM_CATEGORY,
         a.DEPARTMENT_ID,
         a.CONS_RATE,
         a.CONS_QUANTITY,
         a.ORDER_RATE,
         a.INSERTED_BY,
         a.INSERT_DATE,
         b.ITEM_DESCRIPTION,
         b.ITEM_GROUP_ID,
         b.SUB_GROUP_NAME,
         b.ITEM_CODE,
         b.ITEM_SIZE
    FROM inv_transaction a, product_details_master b
   WHERE     a.prod_id = b.id and a.item_category=11
         AND a.transaction_date BETWEEN '$txt_date_to' AND '$txt_date_to'";
    $transSqlRes = sql_select($transSql);
    $dataArr = array();$mst_id_arr = array();
    foreach( $transSqlRes as $row){
        $key = $row['PROD_ID'].'*'.$row['TRANSACTION_DATE'];
        $dataArr[$key][$row['TRANSACTION_TYPE']] = $row;
        $dataArr[$key]['STORE_ID']=$row['STORE_ID'];
        $dataArr[$key]['TRANSACTION_DATE']=$row['TRANSACTION_DATE'];
        $dataArr[$key]['INSERTED_BY']=$row['INSERTED_BY'];
        $dataArr[$key]['INSERT_DATE']=$row['INSERT_DATE'];
        $dataArr[$key]['ITEM_SIZE']=$row['ITEM_SIZE'];
        $dataArr[$key]['ITEM_GROUP_ID']=$row['ITEM_GROUP_ID'];
        $dataArr[$key]['ITEM_CODE']=$row['ITEM_CODE'];
        $dataArr[$key]['ITEM_DESCRIPTION']=$row['ITEM_DESCRIPTION'];
        $dataArr[$key]['SUB_GROUP_NAME']=$row['SUB_GROUP_NAME'];
        $dataArr[$key][$row['TRANSACTION_TYPE']]['CONS_QTY']+=$row['CONS_QUANTITY'];
        $mst_id_arr[$row['TRANSACTION_TYPE']][$row['MST_ID']] = $row['MST_ID'];


        $totalArr[$row['TRANSACTION_TYPE']]+=$row['CONS_QUANTITY'];

    }


    // echo "<pre>";
    // print_r($dataArr);
    // echo "<pre>";
    // exit;
    //echo $transSql;


    $sql ="select a.id as MST_ID,a.CHALLAN_NO, a.CURRENCY_ID, 1 as TYPE,a.RECV_NUMBER as TRNS_REF,a.BOOKING_NO from inv_receive_master a where a.status_active=1 ".where_con_using_array( $mst_id_arr[1],0,'a.id')."
  --  union all
   -- select b.id as MST_ID,b.CHALLAN_NO,0 as CURRENCY_ID, 2 as TYPE, B.ISSUE_NUMBER as TRNS_REF, '' as BOOKING_NO from inv_issue_master b where  b.status_active=1 ".where_con_using_array( $mst_id_arr[2],0,'b.id')."
    ";
   // echo $sql;
    $sqlRes = sql_select($sql);
    $mst_data_arr=array();
    foreach($sqlRes as $row){
        $mst_data_arr[$row['TYPE']][$row['MST_ID']]['TRNS_REF']=$row['TRNS_REF'];
        $mst_data_arr[$row['TYPE']][$row['MST_ID']]['CHALLAN_NO']=$row['CHALLAN_NO'];
        $mst_data_arr[$row['TYPE']][$row['MST_ID']]['CURRENCY_ID']=$row['CURRENCY_ID'];
        $mst_data_arr[$row['TYPE']][$row['MST_ID']]['BOOKING_NO']=$row['BOOKING_NO'];
    }

   // print_r($mst_data_arr);exit;

    ?>

    <div align="center">
	    <h2>Date Wise Item Receive Issue Report [General]</h2>
		<p>Date:<?= change_date_format($txt_date_to); ?></p>
	</div>

<table border="1" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th rowspan="2">SL</th>
                <th rowspan="2">Store Name</th>
                <th rowspan="2">Trans. Date</th>
                <th rowspan="2">Trans. Ref.</th>
                <th rowspan="2">Challan No</th>
                <th rowspan="2">Pur. Reqsn/Book. No/Store Reqsn</th>
                <th rowspan="2">Item Group</th>
                <th rowspan="2">Item Sub-Group</th>
                <th rowspan="2">Item Description</th>
                <th rowspan="2">Item Code</th>
                <th rowspan="2">Size</th>
                <th rowspan="2">Currency</th>
                <th colspan="3">Receive</th>
                <th colspan="3">Issue</th>
                <th rowspan="2">User</th>
                <th rowspan="2">Insert Date & Time</th>
            </tr>
            <tr>
                <th>Rcv. Qty</th>
                <th>Iss. Rtrn Qty</th>
                <th>Trans. In Qty</th>
                <th>Issue Qty</th>
                <th>Trans. Out Qty</th>
                <th>Rcv Rtrn Qty.</th>
            </tr>
        </thead>

        

        <tbody>
            <?php
            $i=1;
            foreach($dataArr as $key => $valArr){
              
            ?>
            <tr>
                <td><?= $i;?></td>
                <td><?= $store_library[$valArr['STORE_ID']];?></td>
                <td><?= change_date_format($valArr['TRANSACTION_DATE']);?></td>
                <td><?= $mst_data_arr[1][$valArr[1]['MST_ID']]['TRNS_REF'];?></td>
                <td><?= $mst_data_arr[1][$valArr[1]['MST_ID']]['CHALLAN_NO'];?></td>
                <td><?= $mst_data_arr[1][$valArr[1]['MST_ID']]['BOOKING_NO'];?></td>
                <td><?= $group_arr[$valArr['ITEM_GROUP_ID']];?></td>
                <td><?= $valArr['SUB_GROUP_NAME'];?></td>
                <td><?= $valArr['ITEM_DESCRIPTION'];?></td>
                <td><?= $valArr['ITEM_CODE'];?></td>
                <td><?= $valArr['ITEM_SIZE'];?></td>
                <td><?= $currency[$mst_data_arr[1][$valArr[1]['MST_ID']]['CURRENCY_ID']];?></td>
                <td align="right"><?= number_format($valArr[1]['CONS_QTY'],2);?></td>
                <td align="right"><?= number_format($valArr[4]['CONS_QTY'],2);?></td>
                <td align="right"><?= number_format($valArr[5]['CONS_QTY'],2);?></td>
                <td align="right"><?= number_format($valArr[2]['CONS_QTY'],2);?></td>
                <td align="right"><?= number_format($valArr[6]['CONS_QTY'],2);?></td>
                <td align="right"><?= number_format($valArr[3]['CONS_QTY'],2);?></td>
                <td><?= $user_name_arr[$valArr['INSERTED_BY']];?></td>
                <td><?= $valArr['INSERT_DATE'];?></td>

            </tr>
            <?php
                $i++;
                }
            ?>
        </tbody>

        <tfoot>
            <tr>
                <th colspan="12" align="right">Total:</th>
                <th align="right"><?= $totalArr[1]; ?></th>
                <th align="right"><?= $totalArr[4]; ?></th>
                <th align="right"><?= $totalArr[5]; ?></th>
                <th align="right"><?= $totalArr[2]; ?></th>
                <th align="right"><?= $totalArr[6]; ?></th>
                <th align="right"><?= $totalArr[3]; ?></th>
                <th align="right"></th>
                <th align="right"></th>
            </tr>
        </tfoot>

        





<?

die;











    



$sql_receive_res = "SELECT a.id, a.recv_number, a.challan_no, a.challan_date, a.supplier_id,a.knitting_source, a.knitting_company, a.currency_id, a.exchange_rate, a.booking_id, a.booking_no, a.receive_basis, b.item_category from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.item_category in(11) and b.transaction_type in(1,4) and a.status_active=1 and b.status_active=1 and b.is_deleted=0 order by b.transaction_date, b.id";
//echo $sql_receive_res;die;
$sql_result_receive_res = sql_select($sql_receive_res);
foreach($sql_result_receive_res as $row)
{
    $receive_num_arr[$row[csf("id")]]["id"]=$row[csf("id")];
    $receive_num_arr[$row[csf("id")]]["recv_number"]=$row[csf("recv_number")];
    $receive_num_arr[$row[csf("id")]]["challan_no"]=$row[csf("challan_no")];
    $receive_num_arr[$row[csf("id")]]["challan_date"]=$row[csf("challan_date")];
    $receive_num_arr[$row[csf("id")]]["supplier_id"]=$row[csf("supplier_id")];
    $receive_num_arr[$row[csf("id")]]["knitting_source"]=$row[csf("knitting_source")];
    $receive_num_arr[$row[csf("id")]]["knitting_company"]=$row[csf("knitting_company")];
    $receive_num_arr[$row[csf("id")]]["currency_id"]=$row[csf("currency_id")];
    $receive_num_arr[$row[csf("id")]]["exchange_rate"]=$row[csf("exchange_rate")];
    $receive_num_arr[$row[csf("id")]]["booking_id"]=$row[csf("booking_id")];
    $receive_num_arr[$row[csf("id")]]["booking_no"]=$row[csf("booking_no")];
    $receive_num_arr[$row[csf("id")]]["receive_basis"]=$row[csf("receive_basis")];
    $receive_num_arr[$row[csf("booking_id")]]["receive_basis"]=$row[csf("receive_basis")];
    $receive_num_arr[$row[csf("id")]]["item_category"]=$row[csf("item_category")];
 
}

$receive_num_arr2 = array();
$sql_receive_res2 = "SELECT id, transaction_type, cons_quantity, item_category, transaction_date, INSERT_DATE  from inv_transaction where item_category in(11) and transaction_type in(1,2,3,4,5,6) and transaction_date between '$txt_date_to' and '$txt_date_to' and status_active=1 and is_deleted=0";
$sql_result_receive_res2 = sql_select($sql_receive_res2);
foreach($sql_result_receive_res2 as $row)
{
    $receive_num_arr2[$row[csf("id")]]["transaction_date"]=$row[csf("transaction_date")];
    $receive_num_arr2[$row[csf("id")]]["cons_quantity"]=$row[csf("cons_quantity")];
    $receive_num_arr2[$row[csf("id")]]["type"]=$row[csf("transaction_type")];
     
}
// echo "<pre>";
// print_r($receive_num_arr2);exit;
// echo "</pre>";
// echo $sql_receive_res;exit;
?>

    <div align="center">
	    <h2>Date Wise Item Receive Issue Report [General]</h2>
		<p><strong>Company Name </strong>: MFG Fashion Group</p>
		<p>Date:<?= change_date_format($txt_date_to); ?></p>
	</div>


    <table border="1" cellpadding="0" cellspacing="0">
        <tr>
            <th rowspan="2">SL</th>
            <th rowspan="2">Store Name</th>
            <th rowspan="2">Trans. Date</th>
            <th rowspan="2">Trans. Ref.</th>
            <th rowspan="2">Challan No</th>
            <th rowspan="2">Pur. Reqsn/Book. No/Store Reqsn</th>
            <th rowspan="2">Item Group</th>
            <th rowspan="2">Item Sub-Group</th>
            <th rowspan="2">Item Description</th>
            <th rowspan="2">Item Code</th>
            <th rowspan="2">Size</th>
            <th rowspan="2">Currency</th>
            <th colspan="3">Receive</th>
            <th colspan="3">Issue</th>
            <th rowspan="2">User</th>
            <th rowspan="2">Insert Date & Time</th>
        </tr>
        <tr>
            <th>Rcv. Qty</th>
            <th>Iss. Rtrn Qty</th>
            <th>Trans. In Qty</th>
            <th>Issue Qty</th>
            <th>Trans. Out Qty</th>
            <th>Rcv Rtrn Qty.</th>
        </tr>
 
        <?php
        $rcv_qty = 0;
        $iss_rtrn_qty = 0;
        $trans_in_qty = 0;
        $issue_qty = 0;
        $trans_out_qty = 0;
        $rcv_rtrn_qty = 0;
        foreach($sql_results as $key=>$row)
        {
            ?>
        <tr>
            <td align="center"><?= $row['ID'];?></td>
            <td><?= $store_library[$row['STORE_ID']];?></td>
            <td><?= change_date_format($row['TRANSACTION_DATE']); ?></td>
            <td>
                <?php
                if($row[csf("transaction_type")]==1  || $row[csf("transaction_type")]==4)
                {
                    echo $receive_num_arr[$row[csf('rec_issue_id')]]["recv_number"];
                }
                else if($row[csf("transaction_type")]==2  || $row[csf("transaction_type")]==3)
                {
                    echo $issue_num_arr[$row[csf('rec_issue_id')]]["issue_number"];
                }
                else if( $row[csf("transaction_type")]==5 || $row[csf("transaction_type")]==6)
                {
                    echo $transfer_num_arr[$row[csf('rec_issue_id')]]["transfer_system_id"];
                }
                ?> 
            </td>
            <td align="center"><?= $receive_num_arr[$row[csf('rec_issue_id')]]["challan_no"]; ?></td>
            <td>
            <?php
            if($row[csf("transaction_type")]==1  || $row[csf("transaction_type")]==4)
            {
                if($receive_num_arr[$row[csf('rec_issue_id')]]["receive_basis"]==7)
                {
                    echo '1 4'.$req_book_no=$requisiton_arr[$receive_num_arr[$row[csf('rec_issue_id')]]["booking_id"]];
                }
                else if ($receive_num_arr[$row[csf('rec_issue_id')]]["receive_basis"]==2)
                {
                    $requ_no=$wo_arr[$receive_num_arr[$row[csf('rec_issue_id')]]["booking_id"]]['requ_no'];
                    $reqs=explode(",", rtrim($requ_no,','));
                    $req_book_no='';
                    foreach($reqs as $val)
                    {
                        $req_book_no.=$reqisition_data[$val]['requ_no'].", ";
                    }
                    echo $req_book_no=rtrim($req_book_no,', ');
                }
                else
                {
                    echo $req_book_no="";
                } 
            }
            else if($row[csf("transaction_type")]==2  || $row[csf("transaction_type")]==3)
            {
                echo $req_book_no=$issue_num_arr[$row[csf('rec_issue_id')]]["req_no"];
            }
            else if($row[csf("transaction_type")]==5  || $row[csf("transaction_type")]==6)
            {
                echo $req_book_no=$transfer_num_arr[$row[csf('rec_issue_id')]]["req_no"];
            }
            
            ?>
              
            </td>
            <td align="center"><?= $group_arr[$row['ITEM_GROUP_ID']];?></td>
            <td align="center"><?= $row['SUB_GROUP_NAME'];?></td>
            <td><?= $row['ITEM_DESCRIPTION'];?></td>
            <td align="center"><?= $row['ITEM_CODE'];?></td>
            <td align="center"><?= $row['ITEM_SIZE'];?></td>
            <td align="center">
                <?= $currency[$receive_num_arr[$row[csf('rec_issue_id')]]["currency_id"]];?>
            </td>


            <!-- <th>Rcv. Qty</th>
            <th>Iss. Rtrn Qty</th>
            <th>Trans. In Qty</th>
            <th>Issue Qty</th>
            <th>Trans. Out Qty</th>
            <th>Rcv Rtrn Qty.</th> -->


            <td align="center">

            <?php
            //$transaction_type = $row[csf('transaction_type')];
           $sql_receive_res2 = "SELECT id, transaction_type, cons_quantity, item_category, transaction_date, INSERT_DATE  from inv_transaction where item_category in(11) and transaction_type in(1,2,3,4,5,6) and transaction_date between '$txt_date_to' and '$txt_date_to' and transaction_type='$transaction_type' and status_active=1 and is_deleted=0";
           // echo $sql_receive_res2;die;
           // $sql_result_receive_res2 = sql_select($sql_receive_res2);
            // foreach($sql_result_receive_res2 as $row)
            // {
            //     echo $row[csf("cons_quantity")];
            // }

            echo $receive_num_arr2[$row['ID']]['type'];
            ?>
                 
            </td>
            <td align="center">
            <?= $row[csf('transaction_type')]; ?>
            </td>
            <td align="center">
            <?= $row[csf('transaction_type')]; ?>
            </td>


            
            <td align="center">
            <?= $row[csf('transaction_type')]; ?>
            </td>
            <td align="center">
            <?= $row[csf('transaction_type')]; ?>
            </td>
            <td align="center">
            <?= $row[csf('transaction_type')]; ?>
            </td> 
            <td><?= $user_name_arr[$row["INSERTED_BY"]];?></td>
            <td><?= $row["INSERT_DATE"];?></td>
        </tr>
        <?php
        }
        unset($sql_result);
        ?> 

        <tfoot>
            <tr>
                <th colspan="12" align="right">Total:</th>
                <th><?= $rcv_qty; ?></th>
                <th><?= $iss_rtrn_qty; ?></th>
                <th><?= $trans_in_qty; ?></th>
                <th><?= $issue_qty; ?></th>
                <th><?= $trans_out_qty; ?></th>
                <th><?= $rcv_rtrn_qty; ?></th>
                <th></th>
            </tr>
        </tfoot>