<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//report generated here--------------------//
if($action=="generate_report")
{ 
  $process = array( &$_POST );
  extract(check_magic_quote_gpc( $process ));
  
  $cbo_company_name=str_replace("'","",$cbo_company_name);
  $cbo_location_id=str_replace("'","",$cbo_location_id);
  $cbo_item_cat=str_replace("'","",$cbo_item_cat);
  $txt_date_from=str_replace("'","",$txt_date_from);
  $txt_date_to=str_replace("'","",$txt_date_to);
  $report_type=str_replace("'","",$type);

  $itemgroupArr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0","id","item_name");

  if($cbo_item_cat!=0){ $search_cond=" and a.item_category=$cbo_item_cat"; } 
  if($cbo_company_name!=0){ $search_cond.=" and a.company_id=$cbo_company_name";  } 

  if($db_type==0)
  {
    $date_form=change_date_format($txt_date_from,"yyyy-mm-dd");
    $date_to=change_date_format($txt_date_to,"yyyy-mm-dd");
  }
  else
  {
    $date_form=change_date_format($txt_date_from,"","",-1);
    $date_to=change_date_format($txt_date_to,"","",-1);
  }
  $main_sql="SELECT a.prod_id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.item_description as ITEM_DESCRIPTION, b.lot as LOT,b.yarn_comp_type1st as YARN_COMP_TYPE1ST,b.yarn_comp_percent1st as YARN_COMP_PERCENT1ST, b.yarn_type as YARN_TYPE,
  sum(case when a.transaction_date<'$date_form' and a.transaction_type in (1,4,5) then a.cons_quantity else 0 end) as OPENING_RECEIVE,
  sum(case when a.transaction_date<'$date_form' and a.transaction_type in (1,4,5) then a.cons_amount else 0 end) as OPENING_RECEIVE_AMT,
	sum(case when a.transaction_date<'$date_form' and a.transaction_type in (2,3,6) then a.cons_quantity else 0 end) as OPENING_ISSUE,
  sum(case when a.transaction_date<'$date_form' and a.transaction_type in (2,3,6) then a.cons_amount else 0 end) as OPENING_ISSUE_AMT,
  sum(case when a.transaction_type=1 and a.transaction_date between '$date_form' and '$date_to' then a.cons_quantity else 0 end) as PURCHASE,
  sum(case when a.transaction_type=2 and a.transaction_date between '$date_form' and '$date_to' then a.cons_quantity else 0 end) as ISSUE,
  sum(case when a.transaction_type=1 and a.transaction_date between '$date_form' and '$date_to' then a.cons_amount else 0 end) as PURCHASE_AMT,
  sum(case when a.transaction_type=2 and a.transaction_date between '$date_form' and '$date_to' then a.cons_amount else 0 end) as ISSUE_AMT
  from inv_transaction a, product_details_master b
  where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_cond 
  group by a.prod_id,b.item_group_id,b.item_description,b.lot,b.yarn_comp_type1st,b.yarn_comp_percent1st,b.yarn_type 
  having sum(case when a.transaction_type=1 and a.transaction_date between '$date_form' and '$date_to' then a.cons_quantity else 0 end)>0
  order by a.prod_id desc";
  //  echo $main_sql;die;
  $main_data=sql_select($main_sql);
  if($cbo_item_cat==1)
  {
    $width_tbl=1650;
    $colspan1=18;
    $colspan2=11;
  }
  else
  {
    $width_tbl=1550;
    $colspan1=17;
    $colspan2=10;
  }

  ob_start();
  ?>
  <style>
    .wrd_brk{word-break: break-all;}
    .center{text-align: center;}
    .right{text-align: right;}
  </style>

  <fieldset style="width:<?=$width_tbl+18;?>px;">
    <div style="width:<?=$width_tbl+18;?>px;">
      <table width="<?=$width_tbl+18;?>"  cellpadding="0" cellspacing="0" border="0"  align="left">                        
        <tr>
          <td colspan="19" style="font-size:16px; font-weight:bold" >Category- <?=$item_category[$cbo_item_cat];?></td>              
        </tr>
      </table>
      <br />
      <table width="<?=$width_tbl+18;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_2" align="left"> 
        <thead>
          <tr>
            <th colspan="<?=$colspan1;?>">Goods/Services input Purchase</th>
          </tr>
          <tr>
            <th rowspan="3" width="50">S.L</th>
            <th rowspan="3" width="80">Product ID</th>
            <th colspan="2">Opening Balance of Input stock</th>
            <th colspan="<?=$colspan2;?>"></th>
            <th colspan="2">Closing Balance of stock</th>
            <th rowspan="3">Comments</th>
          </tr>
          <tr>
            <th rowspan="2" width="80">Quantity (Unit)</th>
            <th rowspan="2" width="80">Value (Excluding all type of taxes)</th>
            <th rowspan="2" width="100" class="wrd_brk">Registered/Enlist/National ID</th>
            <?
              if($cbo_item_cat==1)
              {
                ?><th rowspan="2" width="100">Yarn Lot</th><?
              }
            ?>
            <th rowspan="2" width="200">Description</th>
            <th rowspan="2" width="80">Quantity</th>
            <th rowspan="2" width="80">Value (Excluding all type of taxes)</th>
            <th rowspan="2" width="100">Supplementary Duty( If Have)</th>
            <th rowspan="2" width="80">VAT</th>
            <th colspan="2" width="80">Total Stock quantity</th>
            <th colspan="2" width="80">Stock Consumption for production/process</th>
            <th rowspan="2" width="80">Quantity (Unit)</th>
            <th rowspan="2" width="80">Value (Excluding all type of taxes)</th>
          </tr>
          <tr>
            <th width="80">Quantity (Unit)</th>
            <th width="80">Value (Excluding all type of taxes)</th>
            <th width="80">Quantity (Unit)</th>
            <th width="80">Value (Excluding all type of taxes)</th>
          </tr>
        </thead>
      </table> 
      <div style="width:<?=$width_tbl+18;?>px; overflow-y: scroll; max-height:350px;" id="scroll_body" align="left">
        <table width="<?=$width_tbl;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_1" align="left">
          <tbody>
            <?
            $i=1;
            foreach($main_data as $val)
            {
                if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                if($cbo_item_cat==1)
                {
                  $itemDescription=$composition[$val["YARN_COMP_TYPE1ST"]]." ".$val["YARN_COMP_PERCENT1ST"]."%, ".$yarn_type[$val["YARN_TYPE"]];
                }
                else
                {
                  $itemDescription=$itemgroupArr[$val["ITEM_GROUP_ID"]].', '.$val["ITEM_DESCRIPTION"]; 
                }
                $opening_qty=$val["OPENING_RECEIVE"]-$val["OPENING_ISSUE"];
                $opening_amt=$val["OPENING_RECEIVE_AMT"]-$val["OPENING_ISSUE_AMT"];
                $closing_qty=$opening_qty+$val["PURCHASE"]-$val["ISSUE"];
                $closing_amt=$opening_amt+$val["PURCHASE_AMT"]-$val["ISSUE_AMT"];
                ?>
                  <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('troutp_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="troutp_<? echo $i; ?>">
                    <td width="50" class="wrd_brk center"><? echo $i; ?></td>
                    <td width="80" class="wrd_brk center"><? echo $val["PROD_ID"];?></td>
                    <td width="80" class="wrd_brk right"><? echo $opening_qty; ?></td>
                    <td width="80" class="wrd_brk right"><? echo number_format($opening_amt,2); ?></td>
                    <td width="100" ></td>
                    <?
                      if($cbo_item_cat==1)
                      {
                        ?><td width="100" class="wrd_brk"><? echo $val["LOT"];?></td><?
                      }
                    ?>
                    <td width="200" class="wrd_brk"><? echo $itemDescription; ?></td>
                    <td width="80" class="wrd_brk right">
                      <a href='##' onclick="fnc_rcv_details('<?=$val['PROD_ID'];?>','<?=$date_form;?>','<?=$date_to;?>','rcv_popup_details')"><? echo number_format($val["PURCHASE"],2); ?></a>  
                    </td>
                    <td width="80" class="wrd_brk right"><? echo number_format($val["PURCHASE_AMT"],2); ?></td>
                    <td width="100"></td>
                    <td width="80"></td>
                    <td width="80" class="wrd_brk right"><? echo $opening_qty+$val["PURCHASE"]; ?></td>
                    <td width="80" class="wrd_brk right"><? echo number_format($opening_amt+$val["PURCHASE_AMT"],2); ?></td>
                    <td width="80" class="wrd_brk right"><? echo $val["ISSUE"]; ?></td>
                    <td width="80" class="wrd_brk right"><? echo number_format($val["ISSUE_AMT"],2); ?></td>
                    <td width="80" class="wrd_brk right"><? echo $closing_qty; ?></td>
                    <td width="80" class="wrd_brk right"><? echo number_format($closing_amt,2); ?></td>
                    <td ></td>  
                  </tr>
                <?
                $i++;
            }
            ?>   
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
  echo "$total_data####$filename####$report_type";
  exit();
}


if ($action=="rcv_popup_details") 
{
	echo load_html_head_contents("Receive Details", "../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
  $supplier_sql=sql_select( "SELECT id as ID, supplier_name as SUPPLIER_NAME,address_1 as ADDRESS from lib_supplier");
  foreach($supplier_sql as $row)
  {
    $supplierArr[$row['ID']]['supplier_name']=$row['SUPPLIER_NAME'];
    $supplierArr[$row['ID']]['address']=$row['ADDRESS'];
  }

  $sql = "SELECT a.supplier_id as SUPPLIER_ID,sum(a.cons_quantity) as RCV_QNTY, b.recv_number as RECV_NUMBER, b.receive_date as RECEIVE_DATE, b.challan_no as CHALLAN_NO,b.challan_date as CHALLAN_DATE,b.receive_basis as RECEIVE_BASIS,b.booking_id as BOOKING_ID 
  from inv_transaction a, inv_receive_master b
  where a.mst_id=b.id and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.prod_id=$prod_id and a.transaction_date between '$date_from' and '$date_to'
  group by a.supplier_id,b.recv_number,b.receive_date,b.challan_no,b.challan_date,b.receive_basis,b.booking_id";
  // echo $sql;die;
  $result = sql_select($sql);
  $pi_id=$wo_id="";
  foreach($result as$row)
  {
    if($row["RECEIVE_BASIS"]==1)
    {
      $pi_id.=$row["BOOKING_ID"].',';
    }
    elseif($row["RECEIVE_BASIS"]==2)
    {
      $wo_id.=$row["BOOKING_ID"].',';
    }
  }
  $pi_id=implode(",",array_unique(explode(",",chop($pi_id,','))));
  $wo_id=implode(",",array_unique(explode(",",chop($wo_id,','))));
  $pi_info=array();
  if($pi_id!="")
  {
    $search_cond.=" and b.pi_id in ($pi_id) ";
    $pi_sql="SELECT a.pi_number as PI_NUMBER, a.source as SOURCE,a.goods_rcv_status as GOODS_RCV_STATUS,b.pi_id as PI_ID, d.lc_number as LC_NUMBER, d.lc_date as BTB_DATE 
    from com_pi_master_details a, com_pi_item_details b 
    left join com_btb_lc_pi c on b.pi_id=c.pi_id and c.status_active=1
    left join com_btb_lc_master_details d on c.com_btb_lc_master_details_id=d.id and d.status_active=1
    where a.id=b.pi_id and a.status_active=1 and b.status_active=1 and b.pi_id in ($pi_id)  group by a.pi_number,a.source,a.goods_rcv_status,b.pi_id,d.lc_number, d.lc_date";
    // echo $pi_sql;
    $pi_result=sql_select($pi_sql);
    foreach($pi_result as $row)
    {
      $pi_info[$row['GOODS_RCV_STATUS']][$row['PI_ID']]['pi_number']=$row['PI_NUMBER'];
      $pi_info[$row['GOODS_RCV_STATUS']][$row['PI_ID']]['source']=$row['SOURCE'];
      $pi_info[$row['GOODS_RCV_STATUS']][$row['PI_ID']]['lc_number']=$row['LC_NUMBER'];
      $pi_info[$row['GOODS_RCV_STATUS']][$row['PI_ID']]['btb_date']=$row['BTB_DATE'];
    }
  }
  if($wo_id!="")
  {
    $wo_pi_sql="SELECT a.pi_number as PI_NUMBER, a.source as SOURCE,a.goods_rcv_status as GOODS_RCV_STATUS,b.work_order_id as WORK_ORDER_ID, d.lc_number as LC_NUMBER, d.lc_date as BTB_DATE 
    from com_pi_master_details a, com_pi_item_details b 
    left join com_btb_lc_pi c on b.pi_id=c.pi_id and c.status_active=1
    left join com_btb_lc_master_details d on c.com_btb_lc_master_details_id=d.id and d.status_active=1
    where a.id=b.pi_id and a.status_active=1 and b.status_active=1 and b.work_order_id in ($wo_id) group by a.pi_number,a.source,a.goods_rcv_status,b.work_order_id,d.lc_number, d.lc_date";
    // echo $pi_sql;
    $wo_pi_result=sql_select($wo_pi_sql);
    foreach($wo_pi_result as $row)
    {
      $pi_info[$row['GOODS_RCV_STATUS']][$row['WORK_ORDER_ID']]['pi_number']=$row['PI_NUMBER'];
      $pi_info[$row['GOODS_RCV_STATUS']][$row['WORK_ORDER_ID']]['source']=$row['SOURCE'];
      $pi_info[$row['GOODS_RCV_STATUS']][$row['WORK_ORDER_ID']]['lc_number']=$row['LC_NUMBER'];
      $pi_info[$row['GOODS_RCV_STATUS']][$row['WORK_ORDER_ID']]['btb_date']=$row['BTB_DATE'];
    }
  }
	?>
  <style>
    .wrd_brk{word-break: break-all;}
    .center{text-align: center;}
    .right{text-align: right;}
  </style>
	<fieldset style="width:1100px">
		<legend>Item Details</legend>
    <div id="popup_print">
      <table width="1100" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
        <thead>
          <th width="120">MRR No</th>
          <th width="80">MRR Date</th>
          <th width="80">Challan Number</th>
          <th width="80">Challan date</th>
          <th width="120">Supplier Name</th>
          <th width="120">Address</th>
          <th width="80">Quantity</th>
          <th width="120">PI No</th>
          <th width="80">PI Source</th>
          <th width="120">BTB LC No</th>
          <th>BTB LC Date</th>
        </thead>
        <tbody>
          <?
            $i = 1;
            foreach ($result as $row) 
            {
              if($i % 2 == 0){ $bgcolor = "#E9F3FF"; }else{ $bgcolor = "#FFFFFF"; }		
              $pi_number=$source_id=$btb_lc_number=$btb_lc_date='';
              if($row["RECEIVE_BASIS"]==1)
              {
                $pi_number=$pi_info[2][$row['BOOKING_ID']]['pi_number'];
                $source_id=$pi_info[2][$row['BOOKING_ID']]['source'];
                $btb_lc_number=$pi_info[2][$row['BOOKING_ID']]['lc_number'];
                $btb_lc_date=$pi_info[2][$row['BOOKING_ID']]['btb_date'];
              }		
              if($row["RECEIVE_BASIS"]==2)
              {
                $pi_number=$pi_info[1][$row['BOOKING_ID']]['pi_number'];
                $source_id=$pi_info[1][$row['BOOKING_ID']]['source'];
                $btb_lc_number=$pi_info[1][$row['BOOKING_ID']]['lc_number'];
                $btb_lc_date=$pi_info[1][$row['BOOKING_ID']]['btb_date'];
              }		
              ?>
              <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                <td class="wrd_brk"><p><? echo $row["RECV_NUMBER"]; ?>&nbsp;</p></td>
                <td class="center"><p><? echo change_date_format($row["RECEIVE_DATE"]); ?>&nbsp;</p></td>
                <td class="wrd_brk"><p><? echo $row["CHALLAN_NO"]; ?>&nbsp;</p></td>
                <td class="center"><p><? echo change_date_format($row["CHALLAN_DATE"]); ?>&nbsp;</p></td>
                <td class="wrd_brk"><p><? echo $supplierArr[$row['SUPPLIER_ID']]['supplier_name']; ?>&nbsp;</p></td>
                <td class="wrd_brk"><p><? echo $supplierArr[$row['SUPPLIER_ID']]['address']; ?>&nbsp;</p></td>
                <td class="right"><p><? echo number_format($row["RCV_QNTY"],2); ?>&nbsp;</p></td>
                <td class="wrd_brk"><? echo $pi_number; ?>&nbsp;</td>
                <td class="center"><? echo $source[$source_id]; ?>&nbsp;</td>
                <td class="wrd_brk"><? echo $btb_lc_number; ?>&nbsp;</td>
                <td class="center"><? echo change_date_format($btb_lc_date); ?>&nbsp;</td>
              </tr>
              <?
              $i++;
            }
          ?>
        </tbody>
      </table>
    </div>
    <br>
    <div align="center">
      <input type="button" onclick="new_window_popup()" value="Print" name="Print" class="formbutton" style="width:100px"/>
    </div>
	</fieldset>
  <script>
    function new_window_popup()
    {
      var w = window.open("Surprise", "#");
      var d = w.document.open();
      d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('popup_print').innerHTML+'</body</html>');
      d.close(); 
    }
  </script>
	<?
	exit();
}

?>