<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');
$location_arr=return_library_array( "select id, location_name from lib_location where status_active=1 and is_deleted=0",'id','location_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

$sample_arr=return_library_array(" select id,sample_name from lib_sample where status_active=1 order by sample_name",'id','sample_name');
$other_party_name_arr=return_library_array( "select id, other_party_name from  lib_other_party",'id','other_party_name');
$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');





if($action=="load_drop_down_party")
{
  $data=explode("_",$data);
  if($data[1]==1)
  {
    echo create_drop_down( "cbo_party_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- select Company --", "", "");
  }
  else
  {
    echo create_drop_down( "cbo_party_name", 100, "select sent_to from inv_gate_pass_mst where status_active =1 and is_deleted=0 and company_id='$data[0]' and within_group=2 group by sent_to order by sent_to","sent_to,sent_to", 1, "-- select Party --","", "" );
  } 
  exit();
}


if ($action=="load_drop_down_location")
{
   
  echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- All --", 0, "" );
  exit();
}



//report generated here--------------------//
if($action=="generate_report")
{ 
  $process = array( &$_POST );
  extract(check_magic_quote_gpc( $process ));
  
  $cbo_company_name=str_replace("'","",$cbo_company_name);
  $cbo_location_id=str_replace("'","",$cbo_location_id);
  $cbo_within_group=str_replace("'","",$cbo_within_group);
  $cbo_party_name=str_replace("'","",$cbo_party_name);
  $cbo_item_cat=str_replace("'","",$cbo_item_cat);
  $cbo_returnable=str_replace("'","",$cbo_returnable);
  $txt_gate_pass=str_replace("'","",$txt_gate_pass);
  $txt_gate_in=str_replace("'","",$txt_gate_in);
  $txt_date_from=str_replace("'","",$txt_date_from);
  $txt_date_to=str_replace("'","",$txt_date_to);
  $report_type=str_replace("'","",$type);

  
  //if($cbo_within_group!=0) $within_group_cond=" and c.within_group=$cbo_within_group"; else $within_group_cond="";
  if($cbo_returnable!=0) $cbo_returnable_cond=" and a.returnable=$cbo_returnable"; else $cbo_returnable_cond="";
  if($cbo_location_id!=0) $location_cond=" and a.com_location_id =$cbo_location_id"; else $location_cond="";
  if($cbo_item_cat!=0) $item_category_cond=" and b.item_category_id=$cbo_item_cat"; else $item_category_cond.="";
  if($cbo_company_name!=0) $company_conds.=" and a.company_id=$cbo_company_name"; else $company_conds.="";

  if($txt_gate_pass=='')$gate_pass_con="";else $gate_pass_con=" and a.gate_pass_no like('%".trim(str_replace("'","",$txt_gate_pass))."%')"; //a.inv_gate_pass_mst_id
  if($txt_gate_pass=='')$gate_pass_cond="";else $gate_pass_cond=" and a.sys_number like('%".trim(str_replace("'","",$txt_gate_pass))."%')"; //a.inv_gate_pass_mst_id

  if($txt_gate_in=='')$gate_in_con="";else $gate_in_con=" and a.sys_number like('%".trim(str_replace("'","",$txt_gate_in))."%')"; 

  if($cbo_party_name!='0') $party_con=" and c.sent_to ='$cbo_party_name'";else $party_con=""; 

  if($db_type==0)
  {
    if($txt_date_from!="" && $txt_date_to!="") $out_date_cond=" and a.in_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $out_date_cond=""; 
      
  }
  else
  {
    if($txt_date_from!="" && $txt_date_to!="") $out_date_cond="and a.in_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $out_date_cond="";
  }
  ob_start();
  ?>
  <fieldset style="width:2170px;">
    <div style="width:2170px;">
      <table width="2150"  cellpadding="0" cellspacing="0" border="0"  align="left">                        <tr>
          <td colspan="20" align="center" style="font-size:16px; font-weight:bold" >Gate Entry Report</td>              
          </tr>
          <tr>
          <td colspan="20" align="center" style="font-size:14px;">
          <? if(str_replace("'","",$cbo_company_name)!=0) { ?>
           <? echo 'Company Name :'. $company_arr[str_replace("'","",$cbo_company_name)];
          } else {echo '';};
          ?>  
          </td>
        </tr>
      </table>
      <br />
      <table width="2150" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_2" align="left"> 
        <thead>
           <tr>
            <th rowspan="2" width="30">SL</th>
            <th rowspan="2" width="120">Party Name</th>
            <th rowspan="2" width="100">Party Challan No</th>
            <th rowspan="2" width="120">Company Location</th>
            <th rowspan="2" width="120">System ID No.</th>
            <th rowspan="2" width="80" >Entry Date</th>
            <th rowspan="2" width="80">Out Date</th>
            <th rowspan="2" width="70">Returnable</th>
            <th rowspan="2" width="150">Description</th>
            <th rowspan="2" width="120">Gate Pass No</th>
            <th rowspan="2" width="80">Gate Pass Qty</th>
            <th rowspan="2" width="100">Item Issue Challan</th>
            <th rowspan="2" width="120">Item Category</th>
            <th rowspan="2" width="80">Item gate In Qty</th>
            <th rowspan="2" width="60">UOM</th>
            <th colspan="4" width="">Item In (Converted)</th>
            <th rowspan="2" width="100">Total Gate In Received</th>
            <th rowspan="2" width="80">Balance</th>
            <th rowspan="2" width="100">Gate In Remarks</th>
        </tr>
        <tr>
          <th width="70">Converted Qty.</th>
            <th width="70">Converted Loos Qty</th>
            <th width="100">Converted Remarks</th>
            <th width="60">Converted Uom</th>
        </tr>
        </thead>
      </table> 
      <div style="width:2170px; overflow-y: scroll; max-height:350px;" id="scroll_body" align="left">
        <table width="2150" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_1" align="left">
          <tbody>
            <?
            $i=$k=1;
            //count($get_out_data);
            $sql_gate_in="SELECT  a.sys_number,a.company_id,a.com_location_id,a.in_date,a.gate_pass_no,a.out_date,a.returnable,a.challan_no as issue_challan,a.carried_by,b.sample_id,b.item_category_id,b.item_description,sum(b.quantity) as quantity,sum(b.amount) as amount,b.uom,b.buyer_order,b.remarks, sum(b.chalan_qty) as chalan_qty,c.sent_to,c.within_group,c.challan_no, a.party_challan
            FROM inv_gate_in_mst a, inv_gate_in_dtl b, inv_gate_pass_mst c
            WHERE a.id=b.mst_id and a.inv_gate_pass_mst_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.within_group=$cbo_within_group $cbo_returnable_cond $location_cond $item_category_cond $company_conds $gate_pass_con $gate_in_con $party_con $out_date_cond group by a.sys_number,a.company_id,a.com_location_id,a.in_date,a.gate_pass_no,a.out_date,a.returnable,a.challan_no,a.carried_by,b.sample_id,b.item_category_id,b.item_description,b.uom,b.buyer_order,b.remarks,c.sent_to,c.within_group,c.challan_no,a.party_challan order by a.gate_pass_no,b.uom,a.sys_number"; //and a.returnable=1   and a.gate_pass_no=c.sys_number
            // echo $sql_gate_in; //die;
            $gate_in_data=sql_select($sql_gate_in);
            
            $gate_pass_sql="select a.sys_number,sum(b.quantity) as quantity from inv_gate_pass_mst a,  inv_gate_pass_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 $gate_pass_cond group by a.sys_number";
            $gate_pass_data=sql_select($gate_pass_sql);
            $gate_pass_array=array();
            foreach ($gate_pass_data as $data) 
            {
              $gate_pass_array[$data[csf('sys_number')]]['quantity']=$data[csf('quantity')];
             // $gate_pass_array[$data[csf('sys_number')]]['chalan_qty']=$data[csf('chalan_qty')];
            }

            $sql="select b.id,b.gate_in_sys_no,b.item_catagory_id as main_category, b.item_description,b.quantity,b.uom,b.remarks, c.item_category_id,a.gate_pass_no
            from returnable_item_dtls b,  inv_gate_in_mst a, inv_gate_in_dtl c
            where a.sys_number=b.gate_in_sys_no and a.id=c.mst_id and b.status_active=1 and b.is_deleted=0 
            group by b.id,b.gate_in_sys_no,b.item_catagory_id, b.item_description,b.quantity,b.uom,b.remarks, c.item_category_id, a.gate_pass_no";


            //echo $sql; die;

            $convert_data=sql_select($sql);
            $convert_array=array();
            foreach ($convert_data as $data) 
            {
              $convert_array[$data[csf('gate_in_sys_no')]][$data[csf('item_category_id')]]['item_catagory_id']=$data[csf('main_category')];
              //$convert_array[$data[csf('gate_in_sys_no')]][$data[csf('item_catagory_id')]]['item_description']=$data[csf('item_description')];
              if ($data[csf('main_category')] == $data[csf('item_category_id')]) {
               // $convert_array[$data[csf('gate_in_sys_no')]][$data[csf('item_category_id')]]['loss_qty']=$data[csf('quantity')];
                $convert_array[$data[csf('gate_pass_no')]][$data[csf('item_category_id')]]['loss_qty'] +=$data[csf('quantity')];
              }else{
                //$convert_array[$data[csf('gate_in_sys_no')]][$data[csf('item_category_id')]]['convert_qty']=$data[csf('quantity')];
                $convert_array[$data[csf('gate_pass_no')]][$data[csf('item_category_id')]]['convert_qty'] +=$data[csf('quantity')];
              $convert_array[$data[csf('gate_in_sys_no')]][$data[csf('item_category_id')]]['uom']=$data[csf('uom')];
              $convert_array[$data[csf('gate_in_sys_no')]][$data[csf('item_category_id')]]['remarks']=$data[csf('remarks')];
              }
            }

            //echo "<pre>";
            //print_r($convert_array); die;
            //var_dump($convert_array);

            $rowspan_arr=array(); $gate_pass_uom_qty_arr=array(); $total_blance_arr=array();
            $gate_pass_qty=0;
            foreach ($gate_in_data as $key => $value) 
            {
              $rowspan_arr[$value[csf('gate_pass_no')]][$value[csf('uom')]]++;
              $gate_pass_uom_qty_arr[$value[csf('gate_pass_no')]][$value[csf('uom')]]+=$value[csf('chalan_qty')];
              //$gate_pass_uom_returnable_arr[$value[csf('gate_pass_no')]][$value[csf('uom')]]['con_qty'] +=$value[csf('quantity')];
              //$gate_pass_uom_returnable_arr[$value[csf('gate_pass_no')]][$value[csf('uom')]]['con_loos_qty'] +=$value[csf('loss_qty')];

              //$conQty=$convert_array[$value[csf('sys_number')]][$value[csf('item_category_id')]]['convert_qty'];
              //$conLoss=$convert_array[$value[csf('sys_number')]][$value[csf('item_category_id')]]['loss_qty'];
              $total_receive=$conQty+$conLoss;
              $gate_pass_qty=$value[csf("chalan_qty")];
              $total_blance=$gate_pass_qty-$total_receive;
              $total_blance_arr[$value[csf('gate_pass_no')]][$value[csf('uom')]]+=$total_blance;
            }
            //echo "<pre>";print_r($total_blance_arr);
            $row_ini=0;
            $test_arr=array();
            $tot_challan=$tot_quantity=$tot_conQty=$tot_conLoss=$tot_amount=$tot_gate_receive=0;
            foreach($gate_in_data as $val)
            {
              if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
              $company=$company_arr[$val[csf("company_id")]];
              //$sending_company=$company_arr[$val[csf("sending_company")]];

              if ($val[csf("within_group")]==1) {
                $party=$company_arr[$val[csf("sent_to")]];
              }else{
                $party=$val[csf("sent_to")];
              }

              $com_location=$out_location='';
              if($val[csf("com_location_id")])$com_location='['.$location_arr[$val[csf("com_location_id")]].']';
              if($val[csf("out_location_id")])$out_location='['.$location_arr[$val[csf("out_location_id")]].']';
              $challan_no=$val[csf("challan_no")];
              //$company_arr,$supplier_arr;
              $rowspan=$rowspan_arr[$val[csf("gate_pass_no")]][$val[csf("uom")]];
              //$gate_pass_uom_qty=$gate_pass_uom_qty_arr[$val[csf("gate_pass_no")]][$val[csf("uom")]];

              $gate_pass_uom_qty=$gate_pass_array[$val[csf("gate_pass_no")]]['quantity'];


              $con_qty= $gate_pass_uom_returnable_arr[$val[csf("gate_pass_no")]][$val[csf("uom")]]['con_qty'];
              $loose_qty= $gate_pass_uom_returnable_arr[$val[csf("gate_pass_no")]][$val[csf("uom")]]['con_loos_qty'];


              $total_blance_qty=$total_blance_arr[$val[csf("gate_pass_no")]][$val[csf("uom")]];
              $balance=0;
              // echo $total_blance_qty;
              ?>
              <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('troutp_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="troutp_<? echo $i; ?>">
                 
                  <td width="30" align="center" style="word-break: break-all;"><p><? echo $i; ?></p></td>
                  
                  <td width="120" style="word-break: break-all;"><p><? echo $party; ?></p></td>
                  <td width="100" align="center" style="word-break: break-all;"><p><? echo $val[csf("party_challan")]; ?></p></td>
                  <td width="120" style="word-break: break-all;"><p><? echo $company.'<br>'.$com_location; ?></p></td>
                  <td width="120" style="word-break: break-all;"><p><? echo $val[csf('sys_number')]; ?></a></p></td>
                  <td width="80" align="center" style="word-break: break-all;"><p><? echo '&nbsp;'.change_date_format($val[csf("in_date")]); ?></p></td>
                  <td width="80" align="center" style="word-break: break-all;"><p><? echo  '&nbsp;'.change_date_format($val[csf("out_date")]); ?></p></td>
                  <td width="70" align="center" style="word-break: break-all;"><p><? echo $yes_no[$val[csf("returnable")]]; ?></p></td>
                  <td width="150" style="word-break: break-all;"><p><? echo $val[csf("item_description")]; ?></p></td>
                  <td width="120" style="word-break: break-all;"><p><? echo $val[csf("gate_pass_no")]; ?></p></td>

                  <?
                  $str2=$val[csf("gate_pass_no")].$val[csf("uom")];
                  if ( $str != $str2 ) 
                  {
                    $row_ini=0;                
                  }
                  else
                  {
                    $row_ini=1; 
                  }

                  if ($row_ini==0)
                  {
                    ?>
                    <td width="80" align="right" style="word-break: break-all;" title="<? echo $rowspan; ?>" rowspan="<? echo $rowspan; ?>"><p><? echo number_format($gate_pass_uom_qty,2); ?></p></td>
                    <?
                    // $row_ini++;
                    $str=$val[csf("gate_pass_no")].$val[csf("uom")];
                    $test_arr[$val[csf("gate_pass_no")]][$val[csf("uom")]]=$str;
                    $tot_challan+=$gate_pass_uom_qty;
                  }
                  ?>                  

                  <td width="100" align="center" style="word-break: break-all;"><p><? echo $val[csf("issue_challan")]; ?></p></td>
                  <td width="120" style="word-break: break-all;"><p><? echo $item_category[$val[csf("item_category_id")]]; ?></p></td>
                  <td width="80" align="right" style="word-break: break-all;"><p><? echo number_format($val[csf("quantity")],2); ?></p></td>
                  <td width="60" align="center" style="word-break: break-all;"><p><? echo $unit_of_measurement[$val[csf("uom")]]; ?></p></td>
                  <?
                  if ($row_ini==0)
                  {
                    ?>
                    <td width="70" align="right" style="word-break: break-all;" rowspan="<? echo $rowspan; ?>"><p><a href='#report_details' onClick="fnc_converted_data('<? echo $val[csf('company_id')]; ?>','<? echo $val[csf('gate_pass_no')]; ?>','<? echo $val[csf('sys_number')]; ?>','<? echo $val[csf('returnable')]; ?>');">
                        <?
                      echo number_format($convert_array[$val[csf('gate_pass_no')]][$val[csf('item_category_id')]]['convert_qty'],2);
                      //echo number_format($con_qty,2) ?></p></td>
                      <td width="70" align="right" style="word-break: break-all;"  rowspan="<? echo $rowspan; ?>"><p><? echo number_format($convert_array[$val[csf('gate_pass_no')]][$val[csf('item_category_id')]]['loss_qty'],2); ?></p></td>
                    <?
                    $row_ini++;
                  }?>
                  
                  
                  <td width="100" style="word-break: break-all;"><p><? echo $convert_array[$val[csf('sys_number')]][$val[csf('item_category_id')]]['remarks']; ?></p></td>
                  <td width="60" align="center" style="word-break: break-all;"><p><? echo $unit_of_measurement[$convert_array[$val[csf('sys_number')]][$val[csf('item_category_id')]]['uom']]; ?></p></td>
                  <?
                  $str4=$val[csf("gate_pass_no")].$val[csf("uom")];
                  if ( $str3 != $str4 ) 
                  {
                    $row_ini=0;                
                  }
                  else
                  {
                    $row_ini=1; 
                  }

                  if ($row_ini==0)
                  {
                    ?>
                    <td width="100" align="right" style="word-break: break-all;" rowspan="<? echo $rowspan; ?>"><p><?
                    //$item_qty=$val[csf("quantity")];
                    $conQty=$convert_array[$val[csf('gate_pass_no')]][$val[csf('item_category_id')]]['convert_qty'];
                    $conLoss=$convert_array[$val[csf('gate_pass_no')]][$val[csf('item_category_id')]]['loss_qty'];
                    $total_receive=$conQty+$conLoss; 
                    echo number_format($total_receive,2);?></p></td>
                    <td width="80" align="right" style="word-break: break-all;" title="<? echo $rowspan; ?>" rowspan="<? echo $rowspan; ?>"><p><? 
                    $balance=$gate_pass_uom_qty-$total_receive;
                    echo number_format($balance,2); $tot_amount+=$balance;?></p></td>
                    <?
                    $row_ini++;
                    $str3=$val[csf("gate_pass_no")].$val[csf("uom")];
                    $test_arr[$val[csf("gate_pass_no")]][$val[csf("uom")]]=$str3;
                    
                    $tot_conQty+=$conQty;
                    $tot_conLoss+=$conLoss;
                    $tot_gate_receive+=$total_receive;
                  }
                  ?>
                  <td width="100" style="word-break: break-all;"><p><? echo $val[csf("remarks")]; ?></p></td>
              </tr>
              <?
              
              $tot_quantity+=$val[csf("quantity")];
              //$tot_amount+=$total_blance_qty;
             
              
              $i++;
            }
            ?>   
          </tbody>
          <tfoot>
            
            <th colspan="10"></th>
            <th align="right" id="td_total_challan"><? echo number_format($tot_challan,2);?></th> 
            <th></th>
            <th></th>
            
            <th align="right" id="td_total_qty"><? echo number_format($tot_quantity,2);?> </th>
            <th></th> 
            <th align="right" id="td_con_qty"><? echo number_format($tot_conQty,2);?> </th>
            <th align="right" id="td_con_loss_qty"><? echo number_format($tot_conLoss,2);?></th> 
            <th></th> 
            <th></th> 
            <th align="right" id="td_total_receive"><? echo number_format($tot_gate_receive,2);?></th> 
            <th align="right" id="td_total_amount"><? echo number_format($tot_amount,2);?></th> 
            <th></th> 
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
  echo "$total_data####$filename####$report_type";
  exit();
}




if($action=="converted_data")
{
  echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
  extract($_REQUEST);
   //echo $system_id; die;
    /*$ex_data  = explode("_", $data);
   
    $company_id   = $ex_data[0];
    $txt_pass_id  = $ex_data[1];
    $system_id  = $ex_data[2];
    $returnable   = $ex_data[3];*/
    
     //echo  $sql="SELECT id,entry_form,item_catagory_id,item_description,quantity,uom,remarks,gate_pass_id,gate_pass_sys_id from returnable_item_dtls where gate_pass_sys_id='$txt_pass_id' and status_active=1 and is_deleted=0 and entry_form=363  order by id";
     $sql="select b.id,b.gate_in_sys_no,b.item_catagory_id, b.item_description,b.quantity,b.uom,b.remarks,a.gate_pass_no
            from returnable_item_dtls b,  inv_gate_in_mst a, inv_gate_in_dtl c
            where a.sys_number=b.gate_in_sys_no and a.id=c.mst_id and b.status_active=1 and b.is_deleted=0 and a.gate_pass_no='$txt_pass_id' and c.item_category_id!=b.item_catagory_id
            group by b.id,b.gate_in_sys_no,b.item_catagory_id, b.item_description,b.quantity,b.uom,b.remarks, a.gate_pass_no";
      $convated_data_arr=sql_select($sql);  
    //echo $sql; die;
    
  ?>    
    <div id="data_panel" align="center" style="width:100%">
        <fieldset style="width: 98%">
      
        
        <table width="780" cellspacing="0" class="rpt_table" border="0" id="tbl_returnable_details" rules="all">
                <thead>
            <tr>
              <th width="50">Sl</th>
              <th width="130">Item Catagory</th>
              <th width="130">Item Description</th>
              <th width="130">Qty.</th>
              <th width="130">UOM</th>
              <th width="130">Remarks</th>
            </tr>
          </thead>  
                <tbody>
            
                <?
                $i=1;
                foreach ($convated_data_arr as $row) 
                {     
                      ?>                         
                      <tr>
                          <td width="50" align="center"><? echo $i; ?></td>
                          <td width="130" align="center"><? echo  $item_category[$row[csf('item_catagory_id')]]; ?></td>
                          <td width="130" align="center"><? echo $row[csf('item_description')]; ?></td>
                          <td width="130" align="center"><? echo number_format($row[csf('quantity')],2); ?></td>
                          <td width="130" align="center"><? echo  $unit_of_measurement[$row[csf('uom')]]; ?></td>
                          <td width="130" align="right"><? echo  $row[csf('remarks')]; ?></td>
                      </tr>
           
                      <?
                      $i++;                                     
                }
             ?>
             </tbody>       
        </table>
      </fieldset>
    </div> 
    <?
    exit(); 
}


?>

