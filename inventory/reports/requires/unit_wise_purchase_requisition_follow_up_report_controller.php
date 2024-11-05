<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
  echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where company_id in($data) and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- All --", 0, "set_multiselect('cbo_location_id','0','0','','0')" );
  exit();
}

if ($action=="load_drop_down_store")
{
  echo create_drop_down( "cbo_store_id", 120, "select id,store_name from lib_store_location where company_id in($data) and status_active =1 and is_deleted=0 order by store_name","id,store_name", 1, "-- All --", 0, "" );
  exit();
}

//report generated here--------------------//
if($action=="generate_report")
{ 
  $process = array( &$_POST );
  extract(check_magic_quote_gpc( $process ));
  
  $cbo_company_name=str_replace("'","",$cbo_company_name);
  $cbo_location_id=str_replace("'","",$cbo_location_id);
  $cbo_item_cat_id=str_replace("'","",$cbo_item_cat_id);
  $cbo_item_group_id=str_replace("'","",$cbo_item_group_id);
  $txt_req_no=str_replace("'","",$txt_req_no);
  $txt_wo_no=str_replace("'","",$txt_wo_no);
  $cbo_report_criteria=str_replace("'","",$cbo_report_criteria);
  $cbo_store_id=str_replace("'","",$cbo_store_id);
  $txt_date_from=str_replace("'","",$txt_date_from);
  $txt_date_to=str_replace("'","",$txt_date_to);
  $report_type=str_replace("'","",$type);

  $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
  $location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
  $item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');
  $department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name');
  $search_conds='';
  if($cbo_company_name!='') $search_conds.=" and a.company_id in ($cbo_company_name) ";
  if($cbo_location_id!='') $search_conds.=" and a.location_id in ($cbo_location_id) "; 
  if($cbo_item_cat_id!='') $search_conds.=" and b.item_category in ($cbo_item_cat_id) "; 
  if($cbo_item_group_id!='') $search_conds.=" and c.item_group_id in ($cbo_item_group_id) "; 
  if($txt_req_no!='') $search_conds.=" and a.requ_no like '%$txt_req_no' ";
  if($txt_wo_no!='') $search_conds.=" and d.wo_number like '%$txt_wo_no' ";
  if($cbo_store_id!=0) $search_conds.=" and a.store_name=$cbo_store_id ";

  if($txt_date_from!="" && $txt_date_to!="")
  {
    if($db_type==0)
    {
      if($txt_date_from!="" && $txt_date_to!="") $search_conds.=" and a.requisition_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' ";
    }
    else
    {
      if($txt_date_from!="" && $txt_date_to!="") $search_conds.="and a.requisition_date between '".change_date_format($txt_date_from,'','',-1)."' and '".change_date_format($txt_date_to,'','',-1)."' "; 
    }
  }
  if($txt_wo_no!='')
  {
    $req_sql="SELECT a.id as MST_ID, a.REQU_PREFIX_NUM, a.COMPANY_ID, a.LOCATION_ID, a.REQUISITION_DATE, a.DEPARTMENT_ID, b.id as DTLS_ID, b.ITEM_CATEGORY, b.CONS_UOM, b.QUANTITY, b.RATE, b.AMOUNT, b.REMARKS, c.id as PROD_ID, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION,d.id as WO_ID, d.WO_NUMBER_PREFIX_NUM, d.WO_DATE,e.id as WO_DTLS_ID, e.supplier_order_quantity as WO_QNTY
    FROM inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c, wo_non_order_info_mst d, wo_non_order_info_dtls e
    WHERE a.id=b.mst_id and b.product_id=c.id and a.entry_form=69 and b.id=e.requisition_dtls_id and d.id=e.mst_id and d.wo_basis_id=1 and d.entry_form=147 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $search_conds order by a.company_id,a.location_id,a.id"; 
    // echo $req_sql;die;
    $req_data=sql_select($req_sql);
    $req_id_arr=$req_dtls_id_arr=$prod_id_arr=$wo_id_arr=array();
    $dataArr=array();
    $summaryArr=array();
    foreach($req_data as $row)
    {
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['mst_id']=$row['MST_ID'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['dtls_id']=$row['DTLS_ID'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['company_id']=$row['COMPANY_ID'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['requ_num']=$row['REQU_PREFIX_NUM'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['location_id']=$row['LOCATION_ID'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['requisition_date']=$row['REQUISITION_DATE'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['department_id']=$row['DEPARTMENT_ID'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['item_category']=$row['ITEM_CATEGORY'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['cons_uom']=$row['CONS_UOM'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['req_qnty']=$row['QUANTITY'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['req_rate']=$row['RATE'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['req_amount']=$row['AMOUNT'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['remarks']=$row['REMARKS'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['prod_id']=$row['PROD_ID'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['item_group_id']=$row['ITEM_GROUP_ID'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['item_description']=$row['ITEM_DESCRIPTION'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['wo_id']=$row['WO_ID'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['wo_dtls_id']=$row['WO_DTLS_ID'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['wo_num'].=$row['WO_NUMBER_PREFIX_NUM'].', ';
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['wo_date'].=$row['WO_DATE'].',';
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['wo_qnty']+=$row['WO_QNTY'];

      $wo_id_arr[$row['WO_ID']]=$row['WO_ID'];
      $prod_id_arr[$row['PROD_ID']]=$row['PROD_ID'];
    }
  }
  else
  {
    $req_sql="SELECT a.id as MST_ID, a.REQU_PREFIX_NUM, a.COMPANY_ID, a.LOCATION_ID, a.REQUISITION_DATE, a.DEPARTMENT_ID, b.id as DTLS_ID, b.ITEM_CATEGORY, b.CONS_UOM, b.QUANTITY, b.RATE, b.AMOUNT, b.REMARKS, c.id as PROD_ID, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION
    FROM inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c
    WHERE a.id=b.mst_id and b.product_id=c.id and a.entry_form=69 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $search_conds order by a.company_id,a.location_id,a.id"; 
    // echo $req_sql;die;
    $req_data=sql_select($req_sql);
    $req_id_arr=$req_dtls_id_arr=$prod_id_arr=array();
    $dataArr=array();
    $summaryArr=array();
    foreach($req_data as $row)
    {
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['mst_id']=$row['MST_ID'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['dtls_id']=$row['DTLS_ID'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['company_id']=$row['COMPANY_ID'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['requ_num']=$row['REQU_PREFIX_NUM'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['location_id']=$row['LOCATION_ID'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['requisition_date']=$row['REQUISITION_DATE'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['department_id']=$row['DEPARTMENT_ID'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['item_category']=$row['ITEM_CATEGORY'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['cons_uom']=$row['CONS_UOM'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['req_qnty']=$row['QUANTITY'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['req_rate']=$row['RATE'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['req_amount']=$row['AMOUNT'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['remarks']=$row['REMARKS'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['prod_id']=$row['PROD_ID'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['item_group_id']=$row['ITEM_GROUP_ID'];
      $dataArr[$row['MST_ID']][$row['DTLS_ID']]['item_description']=$row['ITEM_DESCRIPTION'];
  
      $req_id_arr[$row['MST_ID']]=$row['MST_ID'];
      $req_dtls_id_arr[$row['DTLS_ID']]=$row['DTLS_ID'];
      $prod_id_arr[$row['PROD_ID']]=$row['PROD_ID'];
    }
    $req_id=where_con_using_array($req_id_arr,0,'b.requisition_no');
    $req_dtls_id=where_con_using_array($req_dtls_id_arr,0,'b.requisition_dtls_id');
    $wo_sql="SELECT a.id as WO_ID, a.WO_NUMBER_PREFIX_NUM, a.COMPANY_NAME, a.WO_DATE, a.LOCATION_ID,b.id as WO_DTLS_ID, b.requisition_no as REQ_ID, b.requisition_dtls_id as REQ_DTLS_ID, b.supplier_order_quantity as WO_QNTY
    FROM wo_non_order_info_mst a, wo_non_order_info_dtls b
    WHERE a.id=b.mst_id and a.wo_basis_id=1 and a.entry_form=147 $req_id $req_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; 
    // echo $wo_sql;die;
    $wo_data=sql_select($wo_sql);
    $wo_id_arr=array();
    foreach($wo_data as $row)
    {
      $dataArr[$row['REQ_ID']][$row['REQ_DTLS_ID']]['wo_id']=$row['WO_ID'];
      $dataArr[$row['REQ_ID']][$row['REQ_DTLS_ID']]['wo_dtls_id']=$row['WO_DTLS_ID'];
      $dataArr[$row['REQ_ID']][$row['REQ_DTLS_ID']]['wo_num'].=$row['WO_NUMBER_PREFIX_NUM'].', ';
      $dataArr[$row['REQ_ID']][$row['REQ_DTLS_ID']]['wo_date'].=$row['WO_DATE'].',';
      $dataArr[$row['REQ_ID']][$row['REQ_DTLS_ID']]['wo_qnty']+=$row['WO_QNTY'];
  
      $summaryArr[$row['COMPANY_NAME']][$row['LOCATION_ID']]['wo_qnty']+=$row['WO_QNTY'];
  
      $wo_id_arr[$row['WO_ID']]=$row['WO_ID'];
    }
  }
  
  $prod_id=where_con_using_array($prod_id_arr,0,'b.prod_id');
  $wo_id=where_con_using_array($wo_id_arr,0,'a.booking_id');
  $rcv_sql="SELECT a.id as RCV_ID, b.id as TRANS_ID, b.pi_wo_batch_no as WO_ID, b.PROD_ID, b.ORDER_QNTY
  FROM inv_receive_master a, inv_transaction b
  WHERE a.id=b.mst_id and a.receive_basis=2 and a.entry_form=20 $prod_id $wo_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 "; 
  // echo $rcv_sql;die;
  $rcv_data=sql_select($rcv_sql);
  $rcv_data_arr=array();
  foreach($rcv_data as $row)
  {
    $rcv_data_arr[$row['WO_ID']][$row['PROD_ID']]['rcv_id'].=$row['RCV_ID'].',';
    $rcv_data_arr[$row['WO_ID']][$row['PROD_ID']]['trans_id'].=$row['TRANS_ID'].',';
    $rcv_data_arr[$row['WO_ID']][$row['PROD_ID']]['rcv_qnty']+=$row['ORDER_QNTY'];
  }
  $dataResult=array();
  foreach($dataArr as $reqID=>$row)
  {
    foreach($row as $val)
    {
      if($cbo_report_criteria==1)
      {
        $dataResult[$val['mst_id']][$val['dtls_id']]['company_id']=$val['company_id'];
        $dataResult[$val['mst_id']][$val['dtls_id']]['requ_num']=$val['requ_num'];
        $dataResult[$val['mst_id']][$val['dtls_id']]['location_id']=$val['location_id'];
        $dataResult[$val['mst_id']][$val['dtls_id']]['requisition_date']=$val['requisition_date'];
        $dataResult[$val['mst_id']][$val['dtls_id']]['department_id']=$val['department_id'];
        $dataResult[$val['mst_id']][$val['dtls_id']]['item_category']=$val['item_category'];
        $dataResult[$val['mst_id']][$val['dtls_id']]['cons_uom']=$val['cons_uom'];
        $dataResult[$val['mst_id']][$val['dtls_id']]['req_qnty']=$val['req_qnty'];
        $dataResult[$val['mst_id']][$val['dtls_id']]['req_rate']=$val['req_rate'];
        $dataResult[$val['mst_id']][$val['dtls_id']]['req_amount']=$val['req_amount'];
        $dataResult[$val['mst_id']][$val['dtls_id']]['remarks']=$val['remarks'];
        $dataResult[$val['mst_id']][$val['dtls_id']]['prod_id']=$val['prod_id'];
        $dataResult[$val['mst_id']][$val['dtls_id']]['item_group_id']=$val['item_group_id'];
        $dataResult[$val['mst_id']][$val['dtls_id']]['item_description']=$val['item_description'];
        $dataResult[$val['mst_id']][$val['dtls_id']]['wo_id']=$val['wo_id'];
        $dataResult[$val['mst_id']][$val['dtls_id']]['wo_num']=$val['wo_num'];
        $dataResult[$val['mst_id']][$val['dtls_id']]['wo_date']=$val['wo_date'];
        $dataResult[$val['mst_id']][$val['dtls_id']]['wo_qnty']=$val['wo_qnty'];

        $summaryArr[$val['company_id']][$val['location_id']]['company_id']=$val['company_id'];
        $summaryArr[$val['company_id']][$val['location_id']]['location_id']=$val['location_id'];
        $summaryArr[$val['company_id']][$val['location_id']]['req_qnty']+=$val['QUANTITY'];
        $summaryArr[$val['company_id']][$val['location_id']]['req_amount']+=$val['AMOUNT'];
        $summaryArr[$val['company_id']][$val['location_id']]['wo_qnty']+=$val['WO_QNTY'];
      }
      else
      {
        if($dataArr[$val['mst_id']][$val['dtls_id']]['wo_dtls_id']=='')
        {
          $dataResult[$val['mst_id']][$val['dtls_id']]['company_id']=$val['company_id'];
          $dataResult[$val['mst_id']][$val['dtls_id']]['requ_num']=$val['requ_num'];
          $dataResult[$val['mst_id']][$val['dtls_id']]['location_id']=$val['location_id'];
          $dataResult[$val['mst_id']][$val['dtls_id']]['requisition_date']=$val['requisition_date'];
          $dataResult[$val['mst_id']][$val['dtls_id']]['department_id']=$val['department_id'];
          $dataResult[$val['mst_id']][$val['dtls_id']]['item_category']=$val['item_category'];
          $dataResult[$val['mst_id']][$val['dtls_id']]['cons_uom']=$val['cons_uom'];
          $dataResult[$val['mst_id']][$val['dtls_id']]['req_qnty']=$val['req_qnty'];
          $dataResult[$val['mst_id']][$val['dtls_id']]['req_rate']=$val['req_rate'];
          $dataResult[$val['mst_id']][$val['dtls_id']]['req_amount']=$val['req_amount'];
          $dataResult[$val['mst_id']][$val['dtls_id']]['remarks']=$val['remarks'];
          $dataResult[$val['mst_id']][$val['dtls_id']]['prod_id']=$val['prod_id'];
          $dataResult[$val['mst_id']][$val['dtls_id']]['item_group_id']=$val['item_group_id'];
          $dataResult[$val['mst_id']][$val['dtls_id']]['item_description']=$val['item_description'];
  
          $summaryArr[$val['company_id']][$val['location_id']]['company_id']=$val['company_id'];
          $summaryArr[$val['company_id']][$val['location_id']]['location_id']=$val['location_id'];
          $summaryArr[$val['company_id']][$val['location_id']]['req_qnty']+=$val['QUANTITY'];
          $summaryArr[$val['company_id']][$val['location_id']]['req_amount']+=$val['AMOUNT'];
        }
      }
    }
  }
  ob_start();
  ?>
  <style>
    .wrd_brk{word-break: break-all;}
    .left{text-align: left;}
    .center{text-align: center;}
    .right{text-align: right;}
  </style>

  <fieldset style="width:1760px;">
    <div style="width:1760px;">
      <table width="670"  cellpadding="0" cellspacing="0" border="0" class="right">                        
        <tr>
          <td colspan="14" class="center" style="font-size:16px; font-weight:bold" >Unit Wise Purchas Requisition Summary</td>              
        </tr>
      </table>
      <br />
      <table width="670" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" class="right"> 
        <thead>
          <tr>
            <th width="120">Company</th>
            <th width="150">Location</th>
            <th width="100">All Req. Total</th>
            <th width="100">Total Amount</th>
            <th width="100">NO. Of W/O</th>
            <th >NO. Of W/O Pending</th>
          </tr>
        </thead>
        <tbody>
          <?
            $i=1;
            foreach($summaryArr as $companyID=>$locationID)
            {
              foreach($locationID as $row)
              {
                if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trrpt_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trrpt_<? echo $i; ?>">
                    <td><?echo $company_arr[$row['company_id']];?></td>
                    <td><?echo $location_arr[$row['location_id']]?></td>
                    <td class="right"><?echo $row['req_qnty'];?></td>
                    <td class="right"><?echo number_format($row['req_amount'],2);?></td>
                    <td class="right"><?echo $row['wo_qnty'];?></td>
                    <td class="right"><?echo $row['req_qnty']-$row['wo_qnty'];?></td>
                  </tr>
                <?
                $i++;
              }
            }
          ?>
        </tbody>
      </table> 

      <br />
      <table width="1740" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_2" align="left"> 
        <thead>
          <tr>
            <th width="100">Company</th>
            <th width="100">Location</th>
            <th width="80">Purch. Req. Date</th>
            <th width="100">Item Category</th>
            <th width="100">Item Group</th>
            <th width="80" >Req.No</th>
            <th width="100">Item Description</th>
            <th width="80">Req.Qty.</th>
            <th width="80">Uom</th>
            <th width="80">Unit Price</th>
            <th width="80">Total Amount</th>
            <th width="100">Dept.Purpose</th>
            <th width="80">W/O Date</th>
            <th width="80">W/O No.</th>
            <th width="80">W/O qty</th>
            <th width="80">W/O Late</th>
            <th width="80">Req. Late</th>
            <th width="80">Receive Qty</th>
            <th width="80">Rec.Balance</th>
            <th >Remarks</th>
          </tr>
        </thead>
      </table> 
      <div style="width:1760px; overflow-y: scroll; max-height:350px;" id="scroll_body" align="left">
        <table width="1740" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_1" align="left">
          <tbody>
            <?
            $i=1;
            foreach($dataResult as $reqID=>$row)
            {
              $k=1;
              $rowspan=count($row);
              foreach($row as $val)
              {
                if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <?
                    if($k==1)
                    {
                      ?>
                        <td width="100" rowspan="<?=$rowspan;?>" class="wrd_brk"><? echo $company_arr[$val["company_id"]]; ?></td>
                        <td width="100" rowspan="<?=$rowspan;?>" class="wrd_brk"><? echo $location_arr[$val["location_id"]]; ?></td>
                        <td width="80" rowspan="<?=$rowspan;?>" class="wrd_brk center">&nbsp;<? echo change_date_format($val["requisition_date"]);?></td>
                      <?
                    }
                    ?>
                    <td width="100" class="wrd_brk"><? echo $item_category[$val["item_category"]]; ?></td>
                    <td width="100" class="wrd_brk"><? echo $item_group_arr[$val["item_group_id"]]; ?></td>
                    <?
                    if($k==1)
                    {
                      ?>
                        <td width="80" rowspan="<?=$rowspan;?>" class="wrd_brk"><? echo $val["requ_num"] ?></td>
                      <?
                    }
                    ?>
                    <td width="100" class="wrd_brk"><? echo $val["item_description"]; ?></td>
                    <td width="80" class="wrd_brk right"><? echo $val["req_qnty"];?></td>
                    <td width="80" class="wrd_brk center"><? echo $unit_of_measurement[$val["cons_uom"]]; ?></td>
                    <td width="80" class="wrd_brk right"><? echo $val["req_rate"]; ?></td>
                    <td width="80" class="wrd_brk right"><? echo $val["req_amount"]; ?></td>
                    <?
                    if($k==1)
                    {
                      ?>
                        <td width="100" rowspan="<?=$rowspan;?>" class="wrd_brk"><? ?></td>
                      <?
                    }
                    ?>
                    <td width="80" class="wrd_brk center">
                      <?
                      $wo_date='';
                      $wo_date_arr=explode(",",chop($val["wo_date"],','));
                      foreach($wo_date_arr as $woDate){$wo_date.=change_date_format($woDate).', ';}
                      echo rtrim($wo_date,', ');
                     ?>
                    </td>
                    <td width="80" class="wrd_brk right"><? echo rtrim($val["wo_num"],', '); ?></td>
                    <td width="80" class="wrd_brk right"><? echo $val["wo_qnty"]; ?></td>
                    <td width="80" class="wrd_brk right">
                      <? 
                        $late_count='';
                        foreach($wo_date_arr as $woDate){$late_count.=datediff( "d", $val["requisition_date"], $woDate).', ';}
                        echo rtrim($late_count,', ');
                      ?>
                    </td>
                    <td width="80" class="wrd_brk right"><?echo datediff( "d", $val["requisition_date"],date("d-M-y")); ?></td>
                    <td width="80" class="wrd_brk right">
                      <a href='##' style='color:#000' onClick="openmypage_rcv('<? echo chop($rcv_data_arr[$val['wo_id']][$val['prod_id']]['rcv_id'],',')?>','<? echo chop($rcv_data_arr[$val['wo_id']][$val['prod_id']]['trans_id'],',')?>');"><? echo $rcv_data_arr[$val['wo_id']][$val['prod_id']]['rcv_qnty']; ?></a>
                    </td>
                    <td width="80" class="wrd_brk right"><? echo $val["wo_qnty"]-$rcv_data_arr[$val['wo_id']][$val['prod_id']]['rcv_qnty']; ?></td>
                    <td class="wrd_brk"><? echo $val["remarks"];?></td>
                  </tr>
                <?
                $k++;
                $i++;
              }
            }
            ?>   
          </tbody>
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

if($action=="rcv_popup")
{
  echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
  extract($_REQUEST);
  $rcv_sql="SELECT a.id, a.RECV_NUMBER, a.RECEIVE_DATE, b.ORDER_QNTY
  FROM inv_receive_master a, inv_transaction b
  WHERE a.id=b.mst_id and a.receive_basis=2 and a.entry_form=20 and a.id in ($rcv_id) and b.id in ($trans_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 "; 
  // echo $rcv_sql; die;
  $data_arr=sql_select($rcv_sql);  
  ?> 
    <style>
      .wrd_brk{word-break: break-all;}
      .left{text-align: left;}
      .center{text-align: center;}
      .right{text-align: right;}
    </style>   
    <div id="data_panel" align="center" style="width:100%">
      <fieldset style="width: 98%">
        <table width="280" cellspacing="0" class="rpt_table" border="0" id="tbl_returnable_details" rules="all">
          <thead>
            <tr>
              <th width="80">Date</th>
              <th width="120">MRR</th>
              <th >Qty</th>
            </tr>
          </thead>  
          <tbody>
            <?
              $i=1;
              foreach ($data_arr as $row) 
              {     
                ?>                         
                <tr>
                    <td class="wrd_brk center"><? echo change_date_format($row['RECEIVE_DATE']); ?></td>
                    <td class="wrd_brk"><? echo $row['RECV_NUMBER']; ?></td>
                    <td class="wrd_brk right"><? echo $row['ORDER_QNTY']; ?></td>
                </tr>
                <?
                $total_rcv+=$row['ORDER_QNTY'];
                $i++;                                     
              }
            ?>
          </tbody>   
          <tfoot>
            <tr>
              <th></th>
              <th><b>Total</b></th>
              <th class="wrd_brk right"><?echo $total_rcv;?></th>
            </tr>
          </tfoot>    
        </table>
      </fieldset>
    </div> 
    <?
    exit(); 
}
?>