<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');



if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
	exit();	 
} 

if($action=="report_generate")
{ 
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_buyer_id=str_replace("'","",$cbo_party_name);
	$company_id=str_replace("'","",$cbo_company_id);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	$from_date=str_replace("'","",$txt_date_from);
    if(!empty($from_date)){
        $from_date = $from_date;
    }else{
        $from_date = date("d-M-Y");
    }
    //echo $from_date; exit();
	if($db_type==0) $select_from_date=change_date_format($from_date,'yyyy-mm-dd');
	if($db_type==2) $select_from_date=change_date_format($from_date,'','',1);
	
	$date_from=date('Y-m-d', strtotime('-1 day', strtotime($from_date)));
	$datefrom=change_date_format($date_from,'yyyy-mm-dd');
	
	if($cbo_within_group==1)
	{
		$party_arr=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	}
	
	$search_str=trim(str_replace("'","",$txt_search_string));
	$search_type =str_replace("'","",$cbo_type);
	if($search_str!="")
	{
		 if($search_type==1) $search_com_cond=" and c.buyer_style_ref like '%$search_str'"; 
		else if($search_type==2) $search_com_cond=" and c.order_no like '%$search_str'";  
	}
		//echo $search_com_cond; die;
	if($cbo_buyer_id==0) $party_con=""; else $party_con=" and d.party_id='$cbo_buyer_id'";
	if($cbo_within_group==0) $within_group=""; else $within_group=" and d.within_group='$cbo_within_group'";
	// return_library_array satart 
	$color_library_arr=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	if($cbo_within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	// return_library_array end 
	if(str_replace("'","",$company_id)==0)$company_name=""; else $company_name=" and d.company_id=$company_id";
	if($db_type==0)
	{
		if( $from_date==0) $receive_date=""; else $receive_date= " and f.subcon_date='".change_date_format($from_date,'yyyy-mm-dd')."'";
		$cd = strtotime($from_date);
		$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)+$days,date('Y',$cd)));
		$start_date_prev	= date("d-M-Y", strtotime(add_date($retDAY,-1)));
		if( $from_date==0) $start_date_prev=""; else $start_date_prev= " and f.subcon_date='".change_date_format($start_date_prev,'yyyy-mm-dd')."'";
		if( $from_date==0) $start_date_total=""; else $start_date_total= " and f.subcon_date<='".change_date_format($from_date,'yyyy-mm-dd')."'";
		
		if( $from_date==0) $delivery_date_total=""; else $delivery_date_total= " and a.delivery_date<'".change_date_format($from_date,'yyyy-mm-dd')."'";
		if( $from_date==0) $delivery_date_today=""; else $delivery_date_today= " and a.delivery_date='".change_date_format($from_date,'yyyy-mm-dd')."'";
		if( $from_date==0) $delivery_date_prev=""; else $delivery_date_prev= " and a.delivery_date='".change_date_format($start_date_prev,'yyyy-mm-dd')."'";
			
 	}
	if($db_type==2)
	{
		if( $from_date==0) $receive_date=""; else $receive_date= " and f.subcon_date='".change_date_format($from_date,'','',1)."'";	
		$cd = strtotime($from_date);
	    $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)+$days,date('Y',$cd)));
	    $start_date_prev	= date("d-M-Y", strtotime(add_date($retDAY,-1)));
		//echo $start_date_prev."###".$from_date; exit();
		if( $from_date==0) $start_date_prev=""; else $start_date_prev= " and f.subcon_date='".change_date_format($start_date_prev,'','',1)."'";	
		if( $from_date==0) $start_date_total=""; else $start_date_total= " and f.subcon_date<='".change_date_format($from_date,'','',1)."'";	
		
		if( $from_date==0) $delivery_date_today=""; else $delivery_date_today= " and a.delivery_date='".change_date_format($from_date,'','',1)."'";	
		if( $from_date==0) $delivery_date_prev=""; else $delivery_date_prev= " and a.delivery_date='".change_date_format($start_date_prev,'','',1)."'";	
		if( $from_date==0) $delivery_date_total=""; else $delivery_date_total= " and a.delivery_date<'".change_date_format($from_date,'','',1)."'";	
	}
		/*if($db_type==0) $operation_type=",group_concat(a.operation_type) as operation_type";
		else if($db_type==2) $operation_type=",listagg(a.operation_type,',') within group (order by a.operation_type) as operation_type";*/
		$batch_sql = "select c.id as job_dtls_id,a.operation_type from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_ord_dtls c,subcon_ord_mst d 
			where a.id=b.mst_id and a.process_id='1' and  a.status_active=1 and  a.entry_form=316 and b.po_id=c.id and c.mst_id=d.id and  d.entry_form=295 and a.is_deleted=0  $company_name  $party_con $within_group group by c.id ,a.operation_type";  //$receive_date 
			$batch_sql_result=sql_select($batch_sql);	
			$batch_data=array();
			foreach($batch_sql_result as $row)
			{
				$batch_data[$row[csf('job_dtls_id')]]['operation_type']=$row[csf('operation_type')];
			}
			
			
			
			
			$receive_sql="select g.job_dtls_id,
			sum(case when f.trans_type=1 and f.entry_form=296 $start_date_prev then g.quantity else 0 end) as prevDay_recv_qty,
			sum(case when f.trans_type=1 and f.entry_form=296 $start_date_total then g.quantity else 0 end) as prevtotal_recv_qty
            from subcon_ord_mst d, subcon_ord_dtls c,sub_material_mst f,sub_material_dtls g
            where d.id=c.mst_id and d.entry_form=295  and f.id=g.mst_id  and f.embl_job_no=d.subcon_job  and c.id=g.job_dtls_id  and f.trans_type=1 and f.entry_form=296 $within_group $search_com_cond $company_name $party_con  group by g.job_dtls_id";

            //echo $receive_sql; exit();
			$receive_sql_result=sql_select($receive_sql);	
			$receive_data=array();
			foreach($receive_sql_result as $row)
			{
				$receive_data[$row[csf('job_dtls_id')]]['prevDay_recv_qty']+=$row[csf('prevDay_recv_qty')];
				$receive_data[$row[csf('job_dtls_id')]]['prevtotal_recv_qty']+=$row[csf('prevtotal_recv_qty')];
			}
			//echo "<pre>"; print_r($receive_data); exit();
			
			
			$do_sql="select c.id as job_dtls_id ,
			sum(case when a.entry_form = 303 $delivery_date_today then b.delivery_qty else 0 end) as ToDay_del_qty,
			sum(case when a.entry_form = 303 $delivery_date_prev then b.delivery_qty else 0 end) as prevDay_del_qty,
			sum(case when a.entry_form = 303 $delivery_date_total then b.delivery_qty else 0 end) as prevtotal_del_qty
			
            from subcon_delivery_mst a, subcon_delivery_dtls b,subcon_ord_dtls c,subcon_ord_mst d
            where a.entry_form = 303  and a.id=b.mst_id and  b.order_id=c.id and c.mst_id=d.id  $company_name  $party_con $within_group $delivery_date  group by c.id"; 

			$do_result = sql_select($do_sql);
			$do_all_data=array();
			foreach($do_result as $row)
			{
				$do_all_data[$row[csf('job_dtls_id')]]['prevDay_del_qty']+=$row[csf('prevDay_del_qty')];
				$do_all_data[$row[csf('job_dtls_id')]]['ToDay_del_qty']+=$row[csf('ToDay_del_qty')];
				$do_all_data[$row[csf('job_dtls_id')]]['prevtotal_del_qty']+=$row[csf('prevtotal_del_qty')];
				
			}
			//echo "<pre>";
			//print_r($do_all_data);
			if($db_type==0) $job_details_arr=",group_concat(c.id) as job_details_id";
			else if($db_type==2) $job_details_arr=",listagg(c.id,',') within group (order by c.id) as job_details_id";
			$job_sql1="select d.id, g.job_dtls_id, d.party_id,d.within_group,d.order_no,c.party_buyer_name,c.buyer_style_ref,c.gmts_color_id,c.gmts_item_id $job_details_arr
            from subcon_ord_mst d, subcon_ord_dtls c,subcon_ord_breakdown e ,sub_material_mst f,sub_material_dtls g
            where d.id=c.mst_id and d.entry_form=295 and c.id=e.mst_id and f.id=g.mst_id  and f.embl_job_no=d.subcon_job  and c.id=g.job_dtls_id and e.mst_id=g.job_dtls_id  and f.trans_type=1 and f.entry_form=296 and d.within_group=1 $search_com_cond $company_name $party_con  group by g.job_dtls_id, d.id, d.party_id,d.within_group,d.order_no,c.party_buyer_name,c.buyer_style_ref,c.gmts_color_id,c.gmts_item_id order by d.id desc"; 
	 //echo $job_sql1; exit();
	$job_sql_result1=sql_select($job_sql1);
	
	
	$job_sql2="select d.id, d.party_id,d.within_group,d.order_no,c.party_buyer_name,c.buyer_style_ref,c.gmts_color_id,c.gmts_item_id $job_details_arr
    from subcon_ord_mst d, subcon_ord_dtls c,subcon_ord_breakdown e ,sub_material_mst f,sub_material_dtls g
    where d.id=c.mst_id and d.entry_form=295 and c.id=e.mst_id and f.id=g.mst_id  and f.embl_job_no=d.subcon_job  and c.id=g.job_dtls_id and e.mst_id=g.job_dtls_id  and f.trans_type=1 and f.entry_form=296 and d.within_group=2 $search_com_cond $company_name $party_con  group by d.id, d.party_id,d.within_group,d.order_no,c.party_buyer_name,c.buyer_style_ref,c.gmts_color_id,c.gmts_item_id order by d.id desc"; 
	 //echo $job_sql2;
	$job_sql_result2=sql_select($job_sql2);
	
	
	
	 $rewash_sql1="select d.id, d.party_id,d.within_group,d.order_no,c.party_buyer_name,c.buyer_style_ref,c.gmts_color_id,c.gmts_item_id $job_details_arr
from subcon_ord_mst d, subcon_ord_dtls c,subcon_ord_breakdown e ,sub_material_mst f,sub_material_dtls g,pro_batch_create_mst h,pro_batch_create_dtls i
where d.id=c.mst_id and d.entry_form=295 and c.id=e.mst_id and f.id=g.mst_id  and f.embl_job_no=d.subcon_job  and c.id=g.job_dtls_id and e.mst_id=g.job_dtls_id  and f.trans_type=1 and f.entry_form=296 and d.within_group=1 $search_com_cond $company_name $party_con  and h.id=i.mst_id and h.entry_form=316 and c.id=i.po_id  and e.mst_id=i.po_id and h.batch_against=11  group by d.id, d.party_id,d.within_group,d.order_no,c.party_buyer_name,c.buyer_style_ref,c.gmts_color_id,c.gmts_item_id order by d.id desc"; 
	// echo $rewash_sql1;
	$rewash_sql_result1=sql_select($rewash_sql1);
	
	$rewash_sql2="select d.id, d.party_id,d.within_group,d.order_no,c.party_buyer_name,c.buyer_style_ref,c.gmts_color_id,c.gmts_item_id $job_details_arr
from subcon_ord_mst d, subcon_ord_dtls c,subcon_ord_breakdown e ,sub_material_mst f,sub_material_dtls g,pro_batch_create_mst h,pro_batch_create_dtls i
where d.id=c.mst_id and d.entry_form=295 and c.id=e.mst_id and f.id=g.mst_id  and f.embl_job_no=d.subcon_job  and c.id=g.job_dtls_id and e.mst_id=g.job_dtls_id  and f.trans_type=1 and f.entry_form=296 and d.within_group=2 $search_com_cond $company_name $party_con  and h.id=i.mst_id and h.entry_form=316 and c.id=i.po_id  and e.mst_id=i.po_id and h.batch_against=11  group by d.id, d.party_id,d.within_group,d.order_no,c.party_buyer_name,c.buyer_style_ref,c.gmts_color_id,c.gmts_item_id order by d.id desc"; 
	// echo $rewash_sql2;
	$rewash_sql_result2=sql_select($rewash_sql2);
	
	
	
	ob_start();
	?>
     <style type="text/css">
		.wrd_brk{word-break: break-all;}
	</style>
     <fieldset style="width:1320px;">
     <? if($cbo_within_group==1){ ?>
     <div style="width:1320px; margin:0 auto;">
          <table cellpadding="0" cellspacing="0" width="1320">
         		<tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="12" style="font-size:20px"><strong><? echo 'Order Wise Received And Delivery Statement'; ?></strong></td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td colspan="12" align="center" style="border:none; font-size:14px;">
                        <b><? echo $company_library[$company_id]; ?></b>
                    </td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="12" style="font-size:12px">
                        <? if(str_replace("'","",$txt_date_from)!="") echo "Date".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy');?>
                    </td>
                </tr>
            </table>
            <table width="1300" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                        <th width="35">SL</th>
                        <th width="100">Garments Supplier</th>
                        <th width="100">Name Of The Buyer</th>
                        <th width="100">WithIn Group</th>
                        <th width="100">Order No</th>
                        <th width="100">Style No</th>
                        <th width="100">Color</th>
                        <th width="100">Type of Garments</th>
                        <th width="80">Wash</th>
                        <th width="80">Yesterday Received</th>
                        <th width="80">Yesterday Delivery</th>
                        <th width="80">Total Received</th>
                        <th width="80">Total Delivery</th>
                        <th width="80">Morning Delivery</th>
                        <th>Balance Quantity</th>
                </thead>
            </table>
             <div style="max-height:300px; overflow-y:scroll; width:1320px" id="scroll_body">
             <table width="1300" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body">
				<?  
					$i=1;
					
					foreach($job_sql_result1 as $row)
					{
						$prevDayrecvqty="";$prevtotalrecvqty="";$prevDaydelqty="";$ToDaydelqty="";$prevtotaldelqty=""; $operation_type="";
						$job_details_id=array_unique(explode(",",$row[csf("job_details_id")]));
						foreach($job_details_id as $job_id)
						{
							if($job_details=="") $prevDayrecvqty=$receive_data[$job_id]['prevDay_recv_qty']; else $prevDayrecvqty +=$receive_data[$job_id]['prevDay_recv_qty'];
							if($job_details=="") $prevtotalrecvqty=$receive_data[$job_id]['prevtotal_recv_qty']; else $prevtotalrecvqty +=$receive_data[$job_id]['prevtotal_recv_qty'];
							
							
							if($job_details=="") $prevDaydelqty=$do_all_data[$job_id]['prevDay_del_qty']; else $prevDaydelqty +=$do_all_data[$job_id]['prevDay_del_qty'];
							if($job_details=="") $ToDaydelqty=$do_all_data[$job_id]['ToDay_del_qty']; else $ToDaydelqty +=$do_all_data[$job_id]['ToDay_del_qty'];
							if($job_details=="") $prevtotaldelqty=$do_all_data[$job_id]['prevtotal_del_qty']; else $prevtotaldelqty +=$do_all_data[$job_id]['prevtotal_del_qty'];
							if($job_details=="") $operation_type=$batch_data[$job_id]['operation_type']; else $operation_type.=','.$batch_data[$job_id]['operation_type'];
						}
						//$job_detailss=implode(",",array_unique(explode(",",$job_details)));  $batch_data[$row[csf('job_dtls_id')]]['operation_type']
						
						//$operation_type="";
						//$operation_type_id=explode(",",$row[csf('operation_type_id')]); $wash_operation_arr   
						 //$operation_type_id=array_unique(explode(",",$row[csf("operation_type")]));
						//foreach($operation_type_id as $type)
						//{
							//if($operation_type=="") $operation_type=$type; else $operation_type.=','.$type;
						//}
						$operation_type=implode(",",array_unique(explode(",",$operation_type)));
						//echo count($operation_type)."bnfgh"; 
						if($operation_type ==1 || $operation_type ==2 || $operation_type =='1,2')
						{
							$operation_name = 'Wash';
						}
						else
						{
							$operation_name = 'Dying';
						}
						?>
                          <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td  width="35" id="wrd_brk"><? echo $i; ?></td>
                            <td width="100" id="wrd_brk"><? echo $party_arr[$row[csf("party_id")]]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $row[csf("party_buyer_name")]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $yes_no[$row[csf("within_group")]]; ?></td>
                            <td   width="100" id="wrd_brk"><? echo $row[csf("order_no")]; ?></td>
                            <td width="100" id="wrd_brk"><? echo $row[csf("buyer_style_ref")]; ?></td> 
                            <td  width="100" id="wrd_brk"><? echo $color_library_arr[$row[csf("gmts_color_id")]]; ?></td>
                            <td  width="100" id="wrd_brk" align="center"><?php echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
                            <td  width="80" id="wrd_brk" align="center"><?php echo $operation_name; ?></td>
                            <td  width="80" id="wrd_brk" align="center"><?php echo $prevDayrecvqty;?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $prevDaydelqty; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $prevtotalrecvqty; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $prevtotaldelqty; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $ToDaydelqty; ?></td>
                            <td id="wrd_brk" align="right"><?php echo $prevtotalrecvqty-($prevtotaldelqty+$ToDaydelqty); ?></td>
						  </tr>
						<?		
					$i++;
					}
                  ?>
                </table>
         	</div>
         <table width="1300" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td  width="35" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td   width="100" >&nbsp;</td>
                <td   width="100" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="80" >Grand Total:</td>
                <td width="80" id="gt_Yrecev_qty_id"></td>
                <td width="80" id="gt_Ydel_qty_id"></td>
                <td width="80" id="gt_toreceev_qty_id"></td>
                <td width="80" id="gt_ToDeli_qty_id"></td>
                <td width="80" id="gt_MorDel_qty_id"></td>
                <td id="gt_baln_qty_id"></td>
			</tr>
		</table> 
     </div>
     <? } ?>
      <? if($cbo_within_group==2){ ?>
      <div style="width:1320px; margin:0 auto;">
          <table cellpadding="0" cellspacing="0" width="1320">
         		<tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="12" style="font-size:20px"><strong><? echo 'Order Wise Received And Delivery Statement'; ?></strong></td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td colspan="12" align="center" style="border:none; font-size:14px;">
                        <b><? echo $company_library[$company_id]; ?></b>
                    </td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="12" style="font-size:12px">
                        <? if(str_replace("'","",$txt_date_from)!="") echo "Date".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy');?>
                    </td>
                </tr>
            </table>
            <table width="1300" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                        <th width="35">SL</th>
                        <th width="100">Garments Supplier</th>
                        <th width="100">Name Of The Buyer</th>
                        <th width="100">WithIn Group</th>
                        <th width="100">Order No</th>
                        <th width="100">Style No</th>
                        <th width="100">Color</th>
                        <th width="100">Type of Garments</th>
                        <th width="80">Wash</th>
                        <th width="80">Yesterday Received</th>
                        <th width="80">Yesterday Delivery</th>
                        <th width="80">Total Received</th>
                        <th width="80">Total Delivery</th>
                        <th width="80">Morning Delivery</th>
                        <th>Balance Quantity</th>
                </thead>
            </table>
             <div style="max-height:300px; overflow-y:scroll; width:1320px" id="scroll_body">
             <table width="1300" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body">
				<?  
					$i=1;
					
					foreach($job_sql_result2 as $row)
					{
						$prevDayrecvqty="";$prevtotalrecvqty="";$prevDaydelqty="";$ToDaydelqty="";$prevtotaldelqty=""; $operation_type="";
						$job_details_id=array_unique(explode(",",$row[csf("job_details_id")]));
						foreach($job_details_id as $job_id)
						{
							if($job_details=="") $prevDayrecvqty=$receive_data[$job_id]['prevDay_recv_qty']; else $prevDayrecvqty +=$receive_data[$job_id]['prevDay_recv_qty'];
							if($job_details=="") $prevtotalrecvqty=$receive_data[$job_id]['prevtotal_recv_qty']; else $prevtotalrecvqty +=$receive_data[$job_id]['prevtotal_recv_qty'];
							
							
							if($job_details=="") $prevDaydelqty=$do_all_data[$job_id]['prevDay_del_qty']; else $prevDaydelqty +=$do_all_data[$job_id]['prevDay_del_qty'];
							if($job_details=="") $ToDaydelqty=$do_all_data[$job_id]['ToDay_del_qty']; else $ToDaydelqty +=$do_all_data[$job_id]['ToDay_del_qty'];
							if($job_details=="") $prevtotaldelqty=$do_all_data[$job_id]['prevtotal_del_qty']; else $prevtotaldelqty +=$do_all_data[$job_id]['prevtotal_del_qty'];
							if($job_details=="") $operation_type=$batch_data[$job_id]['operation_type']; else $operation_type.=','.$batch_data[$job_id]['operation_type'];
						}
						//$job_detailss=implode(",",array_unique(explode(",",$job_details)));  $batch_data[$row[csf('job_dtls_id')]]['operation_type']
						
						//$operation_type="";
						//$operation_type_id=explode(",",$row[csf('operation_type_id')]); $wash_operation_arr   
						 //$operation_type_id=array_unique(explode(",",$row[csf("operation_type")]));
						//foreach($operation_type_id as $type)
						//{
							//if($operation_type=="") $operation_type=$type; else $operation_type.=','.$type;
						//}
						$operation_type=implode(",",array_unique(explode(",",$operation_type)));
						//echo count($operation_type)."bnfgh"; 
						if($operation_type ==1 || $operation_type ==2 || $operation_type =='1,2')
						{
							$operation_name = 'Wash';
						}
						else
						{
							$operation_name = 'Dying';
						}
						?>
                          <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td  width="35" id="wrd_brk"><? echo $i; ?></td>
                            <td width="100" id="wrd_brk"><? echo $party_arr[$row[csf("party_id")]]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $row[csf("party_buyer_name")]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $yes_no[$row[csf("within_group")]]; ?></td>
                            <td   width="100" id="wrd_brk"><? echo $row[csf("order_no")]; ?></td>
                            <td width="100" id="wrd_brk"><? echo $row[csf("buyer_style_ref")]; ?></td> 
                            <td  width="100" id="wrd_brk"><? echo $color_library_arr[$row[csf("gmts_color_id")]]; ?></td>
                            <td  width="100" id="wrd_brk" align="center"><?php echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
                            <td  width="80" id="wrd_brk" align="center"><?php echo $operation_name; ?></td>
                            <td  width="80" id="wrd_brk" align="center"><?php echo $prevDayrecvqty;?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $prevDaydelqty; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $prevtotalrecvqty; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $prevtotaldelqty; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $ToDaydelqty; ?></td>
                            <td id="wrd_brk" align="right"><?php echo $prevtotalrecvqty-($prevtotaldelqty+$ToDaydelqty); ?></td>
						  </tr>
						<?		
					$i++;
					}
                  ?>
                </table>
         	</div>
         <table width="1300" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td  width="35" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td   width="100" >&nbsp;</td>
                <td   width="100" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="80" >Grand Total:</td>
                <td width="80" id="gt_Yrecev_qty_id"></td>
                <td width="80" id="gt_Ydel_qty_id"></td>
                <td width="80" id="gt_toreceev_qty_id"></td>
                <td width="80" id="gt_ToDeli_qty_id"></td>
                <td width="80" id="gt_MorDel_qty_id"></td>
                <td id="gt_baln_qty_id"></td>
			</tr>
		</table> 
     </div>
      <? }  ?>
      <? if($cbo_within_group==0){ ?>
      <div style="width:1320px; margin:0 auto;">
          <table cellpadding="0" cellspacing="0" width="1320">
         		<tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="12" style="font-size:20px"><strong><? echo 'Order Wise Received And Delivery Statement'; ?></strong></td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td colspan="12" align="center" style="border:none; font-size:14px;">
                        <b><? echo $company_library[$company_id]; ?></b>
                    </td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="12" style="font-size:12px">
                        <? if(str_replace("'","",$txt_date_from)!="") echo "Date".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy');?>
                    </td>
                </tr>
            </table>
            <table width="1300" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                        <th width="35">SL</th>
                        <th width="100">Garments Supplier</th>
                        <th width="100">Name Of The Buyer</th>
                        <th width="100">WithIn Group</th>
                        <th width="100">Order No</th>
                        <th width="100">Style No</th>
                        <th width="100">Color</th>
                        <th width="100">Type of Garments</th>
                        <th width="80">Wash</th>
                        <th width="80">Yesterday Received</th>
                        <th width="80">Yesterday Delivery</th>
                        <th width="80">Total Received</th>
                        <th width="80">Total Delivery</th>
                        <th width="80">Morning Delivery</th>
                        <th>Balance Quantity</th>
                </thead>
            </table>
             <div style="max-height:300px; overflow-y:scroll; width:1320px" id="scroll_body">
             <table width="1300" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body">
				<?  
					$i=1;
					
					foreach($job_sql_result1 as $row)
					{
						$prevDayrecvqty="";$prevtotalrecvqty="";$prevDaydelqty="";$ToDaydelqty="";$prevtotaldelqty=""; $operation_type="";
						$job_details_id=array_unique(explode(",",$row[csf("job_details_id")]));
						foreach($job_details_id as $job_id)
						{
							if($job_details=="") $prevDayrecvqty=$receive_data[$job_id]['prevDay_recv_qty']; else $prevDayrecvqty +=$receive_data[$job_id]['prevDay_recv_qty'];
							if($job_details=="") $prevtotalrecvqty=$receive_data[$job_id]['prevtotal_recv_qty']; else $prevtotalrecvqty +=$receive_data[$job_id]['prevtotal_recv_qty'];
							
							
							if($job_details=="") $prevDaydelqty=$do_all_data[$job_id]['prevDay_del_qty']; else $prevDaydelqty +=$do_all_data[$job_id]['prevDay_del_qty'];
							if($job_details=="") $ToDaydelqty=$do_all_data[$job_id]['ToDay_del_qty']; else $ToDaydelqty +=$do_all_data[$job_id]['ToDay_del_qty'];
							if($job_details=="") $prevtotaldelqty=$do_all_data[$job_id]['prevtotal_del_qty']; else $prevtotaldelqty +=$do_all_data[$job_id]['prevtotal_del_qty'];
							if($job_details=="") $operation_type=$batch_data[$job_id]['operation_type']; else $operation_type.=','.$batch_data[$job_id]['operation_type'];
						}
						//$job_detailss=implode(",",array_unique(explode(",",$job_details)));  $batch_data[$row[csf('job_dtls_id')]]['operation_type']
						
						//$operation_type="";
						//$operation_type_id=explode(",",$row[csf('operation_type_id')]); $wash_operation_arr   
						 //$operation_type_id=array_unique(explode(",",$row[csf("operation_type")]));
						//foreach($operation_type_id as $type)
						//{
							//if($operation_type=="") $operation_type=$type; else $operation_type.=','.$type;
						//}

						$operation_type=implode(",",array_unique(explode(",",$operation_type)));
						//echo count($operation_type)."bnfgh"; 
						if($operation_type ==1 || $operation_type ==2 || $operation_type =='1,2')
						{
							$operation_name = 'Wash';
						}
						else
						{
							$operation_name = 'Dying';
						}
						?>
                          <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td  width="35" id="wrd_brk"><? echo $i; ?></td>
                            <td width="100" id="wrd_brk"><? echo $party_arr[$row[csf("party_id")]]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $row[csf("party_buyer_name")]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $yes_no[$row[csf("within_group")]]; ?></td>
                            <td   width="100" id="wrd_brk"><? echo $row[csf("order_no")]; ?></td>
                            <td width="100" id="wrd_brk"><? echo $row[csf("buyer_style_ref")]; ?></td> 
                            <td  width="100" id="wrd_brk"><? echo $color_library_arr[$row[csf("gmts_color_id")]]; ?></td>
                            <td  width="100" id="wrd_brk" align="center"><?php echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
                            <td  width="80" id="wrd_brk" align="center"><?php echo $operation_name; ?></td>
                            <td  width="80" id="wrd_brk" align="center"><?php echo $prevDayrecvqty;?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $prevDaydelqty; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $prevtotalrecvqty; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $prevtotaldelqty; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $ToDaydelqty; ?></td>
                            <td id="wrd_brk" align="right"><?php echo $prevtotalrecvqty-($prevtotaldelqty+$ToDaydelqty); ?></td>
						  </tr>
						<?		
					$i++;
					}
                  ?>
                </table>
         	</div>
         <table width="1300" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td  width="35" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td   width="100" >&nbsp;</td>
                <td   width="100" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="80" >Grand Total:</td>
                <td width="80" id="gt_Yrecev_qty_id"></td>
                <td width="80" id="gt_Ydel_qty_id"></td>
                <td width="80" id="gt_toreceev_qty_id"></td>
                <td width="80" id="gt_ToDeli_qty_id"></td>
                <td width="80" id="gt_MorDel_qty_id"></td>
                <td id="gt_baln_qty_id"></td>
			</tr>
		</table> 
     </div>
     <br/>
      <div style="width:1320px; margin:0 auto;">
            <table width="1300" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                        <th width="35">SL</th>
                        <th width="100">Garments Supplier</th>
                        <th width="100">Name Of The Buyer</th>
                        <th width="100">WithIn Group</th>
                        <th width="100">Order No</th>
                        <th width="100">Style No</th>
                        <th width="100">Color</th>
                        <th width="100">Type of Garments</th>
                        <th width="80">Wash</th>
                        <th width="80">Yesterday Received</th>
                        <th width="80">Yesterday Delivery</th>
                        <th width="80">Total Received</th>
                        <th width="80">Total Delivery</th>
                        <th width="80">Morning Delivery</th>
                        <th>Balance Quantity</th>
                </thead>
            </table>
             <div style="max-height:300px; overflow-y:scroll; width:1320px" id="scroll_body1">
             <table width="1300" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body1">
				<?  
					$i=1;
					
					foreach($job_sql_result2 as $row)
					{
						$prevDayrecvqty="";$prevtotalrecvqty="";$prevDaydelqty="";$ToDaydelqty="";$prevtotaldelqty=""; $operation_type="";
						$job_details_id=array_unique(explode(",",$row[csf("job_details_id")]));
						foreach($job_details_id as $job_id)
						{
							if($job_details=="") $prevDayrecvqty=$receive_data[$job_id]['prevDay_recv_qty']; else $prevDayrecvqty +=$receive_data[$job_id]['prevDay_recv_qty'];
							if($job_details=="") $prevtotalrecvqty=$receive_data[$job_id]['prevtotal_recv_qty']; else $prevtotalrecvqty +=$receive_data[$job_id]['prevtotal_recv_qty'];
							
							
							if($job_details=="") $prevDaydelqty=$do_all_data[$job_id]['prevDay_del_qty']; else $prevDaydelqty +=$do_all_data[$job_id]['prevDay_del_qty'];
							if($job_details=="") $ToDaydelqty=$do_all_data[$job_id]['ToDay_del_qty']; else $ToDaydelqty +=$do_all_data[$job_id]['ToDay_del_qty'];
							if($job_details=="") $prevtotaldelqty=$do_all_data[$job_id]['prevtotal_del_qty']; else $prevtotaldelqty +=$do_all_data[$job_id]['prevtotal_del_qty'];
							if($job_details=="") $operation_type=$batch_data[$job_id]['operation_type']; else $operation_type.=','.$batch_data[$job_id]['operation_type'];
						}
						//$job_detailss=implode(",",array_unique(explode(",",$job_details)));  $batch_data[$row[csf('job_dtls_id')]]['operation_type']
						
						//$operation_type="";
						//$operation_type_id=explode(",",$row[csf('operation_type_id')]); $wash_operation_arr   
						 //$operation_type_id=array_unique(explode(",",$row[csf("operation_type")]));
						//foreach($operation_type_id as $type)
						//{
							//if($operation_type=="") $operation_type=$type; else $operation_type.=','.$type;
						//}

						$operation_type=implode(",",array_unique(explode(",",$operation_type)));
						//echo count($operation_type)."bnfgh"; 
						if($operation_type ==1 || $operation_type ==2 || $operation_type =='1,2')
						{
							$operation_name = 'Wash';
						}
						else
						{
							$operation_name = 'Dying';
						}
						?>
                          <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td  width="35" id="wrd_brk"><? echo $i; ?></td>
                            <td width="100" id="wrd_brk"><? echo $party_arr[$row[csf("party_id")]]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $row[csf("party_buyer_name")]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $yes_no[$row[csf("within_group")]]; ?></td>
                            <td   width="100" id="wrd_brk"><? echo $row[csf("order_no")]; ?></td>
                            <td width="100" id="wrd_brk"><? echo $row[csf("buyer_style_ref")]; ?></td> 
                            <td  width="100" id="wrd_brk"><? echo $color_library_arr[$row[csf("gmts_color_id")]]; ?></td>
                            <td  width="100" id="wrd_brk" align="center"><?php echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
                            <td  width="80" id="wrd_brk" align="center"><?php echo $operation_name; ?></td>
                            <td  width="80" id="wrd_brk" align="center"><?php echo $prevDayrecvqty;?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $prevDaydelqty; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $prevtotalrecvqty; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $prevtotaldelqty; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $ToDaydelqty; ?></td>
                            <td id="wrd_brk" align="right"><?php echo $prevtotalrecvqty-($prevtotaldelqty+$ToDaydelqty); ?></td>
						  </tr>
						<?		
					$i++;
					}
                  ?>
                </table>
         	</div>
         <table width="1300" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td  width="35" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td   width="100" >&nbsp;</td>
                <td   width="100" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="80" >Grand Total:</td>
                <td width="80" id="gt_Yrecev_qty_id1"></td>
                <td width="80" id="gt_Ydel_qty_id1"></td>
                <td width="80" id="gt_toreceev_qty_id1"></td>
                <td width="80" id="gt_ToDeli_qty_id1"></td>
                <td width="80" id="gt_MorDel_qty_id1"></td>
                <td id="gt_baln_qty_id1"></td>
			</tr>
		</table> 
     </div>
     <br/>
      <div style="width:1320px; margin:0 auto;">
          <table cellpadding="0" cellspacing="0" width="1320">
         		<tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="12" style="font-size:20px"><strong><? echo 'Re-Wash'; ?></strong></td>
                </tr>
            </table>
            <table width="1300" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                        <th width="35">SL</th>
                        <th width="100">Garments Supplier</th>
                        <th width="100">Name Of The Buyer</th>
                        <th width="100">WithIn Group</th>
                        <th width="100">Order No</th>
                        <th width="100">Style No</th>
                        <th width="100">Color</th>
                        <th width="100">Type of Garments</th>
                        <th width="80">Wash</th>
                        <th width="80">Yesterday Received</th>
                        <th width="80">Yesterday Delivery</th>
                        <th width="80">Total Received</th>
                        <th width="80">Total Delivery</th>
                        <th width="80">Morning Delivery</th>
                        <th>Balance Quantity</th>
                </thead>
            </table>
             <div style="max-height:300px; overflow-y:scroll; width:1320px" id="scroll_body11">
             <table width="1300" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body11">
				<?  
					$i=1;
					
					foreach($rewash_sql_result1 as $row)
					{
						$prevDayrecvqty="";$prevtotalrecvqty="";$prevDaydelqty="";$ToDaydelqty="";$prevtotaldelqty=""; $operation_type="";
						$job_details_id=array_unique(explode(",",$row[csf("job_details_id")]));
						foreach($job_details_id as $job_id)
						{
							if($job_details=="") $prevDayrecvqty=$receive_data[$job_id]['prevDay_recv_qty']; else $prevDayrecvqty +=$receive_data[$job_id]['prevDay_recv_qty'];
							if($job_details=="") $prevtotalrecvqty=$receive_data[$job_id]['prevtotal_recv_qty']; else $prevtotalrecvqty +=$receive_data[$job_id]['prevtotal_recv_qty'];
							
							
							if($job_details=="") $prevDaydelqty=$do_all_data[$job_id]['prevDay_del_qty']; else $prevDaydelqty +=$do_all_data[$job_id]['prevDay_del_qty'];
							if($job_details=="") $ToDaydelqty=$do_all_data[$job_id]['ToDay_del_qty']; else $ToDaydelqty +=$do_all_data[$job_id]['ToDay_del_qty'];
							if($job_details=="") $prevtotaldelqty=$do_all_data[$job_id]['prevtotal_del_qty']; else $prevtotaldelqty +=$do_all_data[$job_id]['prevtotal_del_qty'];
							if($job_details=="") $operation_type=$batch_data[$job_id]['operation_type']; else $operation_type.=','.$batch_data[$job_id]['operation_type'];
						}
						//$job_detailss=implode(",",array_unique(explode(",",$job_details)));  $batch_data[$row[csf('job_dtls_id')]]['operation_type']
						
						//$operation_type="";
						//$operation_type_id=explode(",",$row[csf('operation_type_id')]); $wash_operation_arr   
						 //$operation_type_id=array_unique(explode(",",$row[csf("operation_type")]));
						//foreach($operation_type_id as $type)
						//{
							//if($operation_type=="") $operation_type=$type; else $operation_type.=','.$type;
						//}

						$operation_type=implode(",",array_unique(explode(",",$operation_type)));
						//echo count($operation_type)."bnfgh"; 
						if($operation_type ==1 || $operation_type ==2 || $operation_type =='1,2')
						{
							$operation_name = 'Wash';
						}
						else
						{
							$operation_name = 'Dying';
						}
						?>
                          <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td  width="35" id="wrd_brk"><? echo $i; ?></td>
                            <td width="100" id="wrd_brk"><? echo $party_arr[$row[csf("party_id")]]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $row[csf("party_buyer_name")]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $yes_no[$row[csf("within_group")]]; ?></td>
                            <td   width="100" id="wrd_brk"><? echo $row[csf("order_no")]; ?></td>
                            <td width="100" id="wrd_brk"><? echo $row[csf("buyer_style_ref")]; ?></td> 
                            <td  width="100" id="wrd_brk"><? echo $color_library_arr[$row[csf("gmts_color_id")]]; ?></td>
                            <td  width="100" id="wrd_brk" align="center"><?php echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
                            <td  width="80" id="wrd_brk" align="center"><?php echo $operation_name; ?></td>
                            <td  width="80" id="wrd_brk" align="center"><?php echo $prevDayrecvqty;?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $prevDaydelqty; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $prevtotalrecvqty; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $prevtotaldelqty; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $ToDaydelqty; ?></td>
                            <td id="wrd_brk" align="right"><?php echo $prevtotalrecvqty-($prevtotaldelqty+$ToDaydelqty); ?></td>
						  </tr>
						<?		
					$i++;
					}
                  ?>
                </table>
         	</div>
         <table width="1300" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td  width="35" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td   width="100" >&nbsp;</td>
                <td   width="100" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="80" >Grand Total:</td>
                <td width="80" id="gt_Yrecev_qty_id11"></td>
                <td width="80" id="gt_Ydel_qty_id11"></td>
                <td width="80" id="gt_toreceev_qty_id11"></td>
                <td width="80" id="gt_ToDeli_qty_id11"></td>
                <td width="80" id="gt_MorDel_qty_id11"></td>
                <td id="gt_baln_qty_id11"></td>
			</tr>
		</table> 
     </div>
     <br/>
      <div style="width:1320px; margin:0 auto;">
            <table width="1300" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                        <th width="35">SL</th>
                        <th width="100">Garments Supplier</th>
                        <th width="100">Name Of The Buyer</th>
                        <th width="100">WithIn Group</th>
                        <th width="100">Order No</th>
                        <th width="100">Style No</th>
                        <th width="100">Color</th>
                        <th width="100">Type of Garments</th>
                        <th width="80">Wash</th>
                        <th width="80">Yesterday Received</th>
                        <th width="80">Yesterday Delivery</th>
                        <th width="80">Total Received</th>
                        <th width="80">Total Delivery</th>
                        <th width="80">Morning Delivery</th>
                        <th>Balance Quantity</th>
                </thead>
            </table>
             <div style="max-height:300px; overflow-y:scroll; width:1320px" id="scroll_body12">
             <table width="1300" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body12">
				<?  
					$i=1;
					
					foreach($rewash_sql_result2 as $row)
					{
						$prevDayrecvqty="";$prevtotalrecvqty="";$prevDaydelqty="";$ToDaydelqty="";$prevtotaldelqty=""; $operation_type="";
						$job_details_id=array_unique(explode(",",$row[csf("job_details_id")]));
						foreach($job_details_id as $job_id)
						{
							if($job_details=="") $prevDayrecvqty=$receive_data[$job_id]['prevDay_recv_qty']; else $prevDayrecvqty +=$receive_data[$job_id]['prevDay_recv_qty'];
							if($job_details=="") $prevtotalrecvqty=$receive_data[$job_id]['prevtotal_recv_qty']; else $prevtotalrecvqty +=$receive_data[$job_id]['prevtotal_recv_qty'];
							
							
							if($job_details=="") $prevDaydelqty=$do_all_data[$job_id]['prevDay_del_qty']; else $prevDaydelqty +=$do_all_data[$job_id]['prevDay_del_qty'];
							if($job_details=="") $ToDaydelqty=$do_all_data[$job_id]['ToDay_del_qty']; else $ToDaydelqty +=$do_all_data[$job_id]['ToDay_del_qty'];
							if($job_details=="") $prevtotaldelqty=$do_all_data[$job_id]['prevtotal_del_qty']; else $prevtotaldelqty +=$do_all_data[$job_id]['prevtotal_del_qty'];
							if($job_details=="") $operation_type=$batch_data[$job_id]['operation_type']; else $operation_type.=','.$batch_data[$job_id]['operation_type'];
						}
						//$job_detailss=implode(",",array_unique(explode(",",$job_details)));  $batch_data[$row[csf('job_dtls_id')]]['operation_type']
						
						//$operation_type="";
						//$operation_type_id=explode(",",$row[csf('operation_type_id')]); $wash_operation_arr   
						 //$operation_type_id=array_unique(explode(",",$row[csf("operation_type")]));
						//foreach($operation_type_id as $type)
						//{
							//if($operation_type=="") $operation_type=$type; else $operation_type.=','.$type;
						//}

						$operation_type=implode(",",array_unique(explode(",",$operation_type)));
						//echo count($operation_type)."bnfgh"; 
						if($operation_type ==1 || $operation_type ==2 || $operation_type =='1,2')
						{
							$operation_name = 'Wash';
						}
						else
						{
							$operation_name = 'Dying';
						}
						?>
                          <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td  width="35" id="wrd_brk"><? echo $i; ?></td>
                            <td width="100" id="wrd_brk"><? echo $party_arr[$row[csf("party_id")]]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $row[csf("party_buyer_name")]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $yes_no[$row[csf("within_group")]]; ?></td>
                            <td   width="100" id="wrd_brk"><? echo $row[csf("order_no")]; ?></td>
                            <td width="100" id="wrd_brk"><? echo $row[csf("buyer_style_ref")]; ?></td> 
                            <td  width="100" id="wrd_brk"><? echo $color_library_arr[$row[csf("gmts_color_id")]]; ?></td>
                            <td  width="100" id="wrd_brk" align="center"><?php echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
                            <td  width="80" id="wrd_brk" align="center"><?php echo $operation_name; ?></td>
                            <td  width="80" id="wrd_brk" align="center"><?php echo $prevDayrecvqty;?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $prevDaydelqty; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $prevtotalrecvqty; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $prevtotaldelqty; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $ToDaydelqty; ?></td>
                            <td id="wrd_brk" align="right"><?php echo $prevtotalrecvqty-($prevtotaldelqty+$ToDaydelqty); ?></td>
						  </tr>
						<?		
					$i++;
					}
                  ?>
                </table>
         	</div>
         <table width="1300" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td  width="35" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td   width="100" >&nbsp;</td>
                <td   width="100" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="80" >Grand Total:</td>
                <td width="80" id="gt_Yrecev_qty_id12"></td>
                <td width="80" id="gt_Ydel_qty_id12"></td>
                <td width="80" id="gt_toreceev_qty_id12"></td>
                <td width="80" id="gt_ToDeli_qty_id12"></td>
                <td width="80" id="gt_MorDel_qty_id12"></td>
                <td id="gt_baln_qty_id12"></td>
			</tr>
		</table> 
     </div>
      <? }  ?>
      
     </fieldset>
    <?
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename"; 
    exit();
}

?>