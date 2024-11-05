<link href="../../../css/style_common.css" rel="stylesheet" type="text/css" media="screen" />
    <script type="text/javascript" src="../../../resources/jquery_ui/jquery-1.4.4.min.js"></script>
    <link href="../../../resources/jquery_ui/jquery-ui-1.8.10.custom.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="../../../resources/jquery_ui/jquery-ui-1.8.10.custom.min.js"></script>
<?
session_start();
include('../../../includes/common.php');
include('../../../includes/array_function.php');
include('../../../includes/common_functions.php');
extract ($_REQUEST);//data cheqing


if($action=='generate_report')
{
  if($wo_no!=''){$where_con = " and a.wo_number=$wo_no";}//for search
  if($item_id!=0){$where_con .= " and b.item_id=$item_id";}
  if($company_id!=0){$where_con .= " and a.company_name=$company_id";}
  if($supplier!=0){$where_con .= " and a.supplier_id=$supplier";}
  if ($from_date!="" &&  $to_date!=""){$where_con .= " and a.wo_date between '".date('d-m-Y',strtotime($from_date))."' and '".date('d-m-Y',strtotime($to_date))."'";} 
  $wo_sql="select  a.id,a.wo_number,a.supplier_id,a.wo_date,a.delivery_date,b.item_description,b.item_id,b.rate,b.uom,b.supplier_order_quantity,b.amount from  wo_non_order_info_mst a,wo_non_order_info_dtls b where a.wo_number=b.wo_number and a.is_deleted=0 and a.status_active=1 $where_con";
  $wo_sql_res = select_query( $wo_sql );
	$wo_request_arr=array();

	foreach($wo_sql_res as $row)
	{   
    $key=$row['wo_number'].'**'.$row['item_description'].'**'.$row['uom'];
		$wo_request_arr[qty][$key]+=$row['supplier_order_quantity'];
		$wo_request_arr[rate][$key]+=$row['rate'];
		$wo_request_arr[val][$key]+=$row['amount'];
		$wo_request_arr[wo_date][$key]=$row['wo_date'];
		$wo_request_arr[supplier_id][$key]=$row['supplier_id'];
		//$wo_request_arr[item_description][$key]=$row['item_description'];
	} 
  $receive_entry_sql="select a.wo_pi_no,a.order_uom,a.receive_qnty,c.item_description from inv_chem_dyes_receive_dtls a,inv_chem_dyes_receive_mst b,inv_chem_dyes_product_dtls c where b.id=a.mst_id and a.prod_id=c.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ";
  // echo $receive_entry_sql;
  $receive_entry_sql_res = select_query( $receive_entry_sql );
  $receive_entry_data_arr=array();
  foreach($receive_entry_sql_res as $row)
  {
    $key=$row['wo_pi_no'].'**'.$row['item_description'].'**'.$row['order_uom'];
		$receive_entry_data_arr[qty][$key]+=$row['receive_qnty'];
  }
  $supplier_arr=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
  $item_arr=return_library_array( "select id,item_name from lib_trim",'id','item_name');
  
  ?>
  <table align='center'> 
    <tr>
      <th colspan="13">TymBird Marketing And Distrubution Ltd.</th>
    </tr>
    <tr>
      <th colspan="13">Chemicals  WorkOrder  Statements</th>
    </tr>
    <tr>
      <th colspan="13">Date :01-02-2022 To 28-02-2022</th>
    </tr>     
  </table>
  
  <table border="1" rules="all" width="100%" class="rpt_table">
    <thead>
      <tr>
        <th>SL</th>
        <th>WO NO</th>
        <th>Supplier</th>
        <th>Date</th>
        <th>Item Name</th>
        <th>Qty</th>
        <th>UOM</th>
        <th>Rate</th>
        <th>Amount</th>
        <th>Item Recieved</th>
        <th>Rec Bal</th>
        <th>Item Issue</th>
        <th>Issue Bal</th>
      </tr>
    </thead>
    <?php
    $add=0;
    foreach($wo_request_arr[qty] as $key=>$qty)
    {
      list($row['wo_number'],$row['item_description'],$row['uom'])=explode('**',$key);
      ?>
      <tr>
        <td></td>
        <td style="text-align: right;"><?=$row['wo_number'];?></td>
        <td><?=$supplier_arr[$wo_request_arr[supplier_id][$key]];?></td>
        <td><?=$wo_request_arr[wo_date][$key];?></td>
        <td><?=$row[item_description]?></td>
        <td style="text-align: right;"><?=$wo_request_arr[qty][$key]?></td>   
        <td style="text-align: right;"><?=$row['uom'];?></td>
        <td style="text-align: right;"><?=(number_format($wo_request_arr[val][$key]/$wo_request_arr[qty][$key],2));?></td>
        <td style="text-align: right;"><?=$wo_request_arr[val][$key];?></td>
        <td style="text-align: right;"><?=$receive_entry_data_arr[qty][$key];?></td>
        <td style="text-align: right;"><?= ($wo_request_arr[qty][$key]-$receive_entry_data_arr[qty][$key]); ?></td> 
        <td><?=$issue_entry_data_arr[current_stock][$key];?></td>
        <td style="text-align: right;"><?=(($receive_entry_data_arr[qty][$key])-($issue_entry_data_arr[qty][$key])); ?></td>
      </tr>
      <?php
      $sum=$wo_request_arr[val][$key];
      $add+=$sum;
    }
    ?>
    <tr>
      <th  colspan="7">Sub Total</th>
      <td></td>
      <td style="text-align: right;"><?= $add?></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
  </table>
  <?   
  foreach (glob("tmp_report_file/*.xls") as $filename) 
  {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
  }
  //---------end------------//
  $name=time();
  $filename=$name.".xls";
  $create_new_doc = fopen($filename, 'w');	
  $is_created = fwrite($create_new_doc,ob_get_contents());
  echo "$total_data####$filename";
  exit();	
}
?>