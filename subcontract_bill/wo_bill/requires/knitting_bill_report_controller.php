<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');


$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


 
if($action=="report_generate")
{ 
	$process = array( &$_POST );

	// echo "<pre>";
	// print_r($process);
	// echo "</pre>";
	// die;
	
	extract(check_magic_quote_gpc( $process ));
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");

	$imge_arr=return_library_array( "select master_tble_id, image_location from  common_photo_library where file_type=1",'master_tble_id','image_location');
	$supllier_arr = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", 'id', 'supplier_name');

	$cbo_company_id= str_replace("'","",$cbo_company_id);
	$date_type = str_replace("'","",trim($cbo_date_type));
	$cbo_search_by = str_replace("'","",trim($cbo_search_by));
	$start_date=str_replace("'","",trim($txt_date_from));
	$end_date=str_replace("'","",trim($txt_date_to));
	$cbo_supplier_name=str_replace("'","",trim($cbo_supplier_name));
	$txt_search_common=str_replace("'","",trim($txt_search_common));
	$cbo_date_type=str_replace("'","",trim($cbo_date_type));
	$supplier_con='';
	$company_con='';
	//echo $cbo_search_by.'--'.$txt_search_common;die;
	if(!empty($cbo_supplier_name) && $cbo_supplier_name!=0)
	{
		$supplier_con=" and a.supplier_id=$cbo_supplier_name";
	}
	if(!empty($cbo_company_id) && $cbo_company_id!=0)
	{
		$company_con=" and  a.company_id=$cbo_company_id";
	}

	$wo_no_con='';
	$search_field_cond='';
	$wo_date_con='';
	$date_search_cond ='';

	if(!empty($txt_search_common))
	{
		if($cbo_search_by==1)
		{
			$wo_no_con=" and a.wo_number_prefix_num=" . $txt_search_common ;
		}
		else if($cbo_search_by==2)
		{
			$search_field_cond = " and a.prefix_no_num=" . $txt_search_common;
		}
		else if($cbo_search_by==3)
		{
			$search_field_cond = " and LOWER(b.fso_no) like LOWER('%" . $txt_search_common . "%')";
		}
		else if($cbo_search_by==4)
		{
			$search_field_cond = " and LOWER(b.booking_no) like LOWER('%" . $txt_search_common . "%')";
		}else{
			$wo_no_con=	" and LOWER(b.style_ref_no) like LOWER('%" . $txt_search_common . "%')";
		}
	}
	else 
	{

		if($date_type==1)
		{
			
			
			if($db_type==0)
			{
				if ($start_date!="" &&  $end_date!="") $date_search_cond  = "and a.bill_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $date_search_cond ="";
			}

			if($db_type==2)
			{
				if ($start_date!="" &&  $end_date!="") $date_search_cond  = "and a.bill_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $date_search_cond ="";
			}
			
		}else{
			
			if($db_type==0)
			{
				if ($start_date!="" &&  $end_date!="") $wo_date_con  = "and a.booking_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $wo_date_con ="";
			}

			if($db_type==2)
			{
				if ($start_date!="" &&  $end_date!="") $wo_date_con  = "and a.booking_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $wo_date_con ="";
			}
		}

	}


	$kw_sql="SELECT a.wo_no,a.id as knitting_wo_id,a.booking_date,b.id,b.fabric_desc,b.program_no,b.machine_dia,b.machine_gg,b.stitch_length,b.color_range,b.style_ref_no,b.within_group,b.buyer_id from knitting_work_order_mst a , knitting_work_order_dtls b where  a.id=b.mst_id  and a.status_active=1 and b.status_active=1 $wo_date_con $wo_no_con ";
	//echo "<pre>".$kw_sql."</pre>";

	$kw_result=sql_select($kw_sql);
	$data_arr=array();
	$wo_dtls_ids=array();
	foreach ($kw_result as $row) {
		$wo_dtls_ids[]=$row[csf('id')];
		$data_arr[$row[csf('id')]]['id']=$row[csf('id')];
		$data_arr[$row[csf('id')]]['wo_no']=$row[csf('wo_no')];
		$data_arr[$row[csf('id')]]['program_no']=$row[csf('program_no')];
		$data_arr[$row[csf('id')]]['booking_date']=$row[csf('booking_date')];
		$data_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
		$data_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
		$data_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
		$data_arr[$row[csf('id')]]['stitch_length']=$row[csf('stitch_length')];
		$data_arr[$row[csf('id')]]['color_range']=$row[csf('color_range')];
		$data_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$data_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
		$data_arr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
		$data_arr[$row[csf('id')]]['knitting_wo_id']=$row[csf('knitting_wo_id')];
	}

	if(!empty($wo_dtls_ids))
	{
		$wo_dtls_ids_string = implode(",",$wo_dtls_ids);
	    $wo_dtls_ids_string=implode(",",array_filter(array_unique(explode(",",$wo_dtls_ids_string))));
	    if($wo_dtls_ids_string!="")
	    {
	        $wo_dtls_ids_string=explode(",",$wo_dtls_ids_string);  
	        $wo_dtls_chnk=array_chunk($wo_dtls_ids_string,999);
	        $wo_dtls_cond=" and";
	        foreach($wo_dtls_chnk as $dtls_id)
	        {
	        if($wo_dtls_cond==" and")  $wo_dtls_cond.="(b.wo_dtls_id in(".implode(',',$dtls_id).")"; else $wo_dtls_cond.=" or b.wo_dtls_id in(".implode(',',$dtls_id).")";
	        }
	        $wo_dtls_cond.=")";
	        
	    }	
		
	}

	
	$sql="SELECT a.bill_no, a.bill_date,a.manual_bill_no,a.supplier_id,b.buyer_id,b.booking_no,b.fso_no,b.wo_qty,b.bill_qty,b.rate,b.amount,a.upchage,a.tot_bill_amt,a.tot_bill_qty,a.tot_wo_qty,a.discount ,b.wo_dtls_id
	from wo_bill_mst a,wo_bill_dtls b 
	where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.entry_form=421 and b.entry_form=421 $date_search_cond $supplier_con $company_con $search_field_cond $wo_dtls_cond order by a.id,b.id";

	
	$bill_wise=array();
	$pre_b='';
	$result=sql_select($sql);
	foreach ($result as $row) {
		$bill_wise[$row[csf('bill_no')]]['bill_qty']+=$row[csf('bill_qty')];
		$bill_wise[$row[csf('bill_no')]]['wo_qty']+=$row[csf('wo_qty')];
		$bill_wise[$row[csf('bill_no')]]['amount']+=$row[csf('amount')];
		if($pre_b!=$row[csf('bill_no')])
		{

			$bill_wise[$row[csf('bill_no')]]['cnt']=1;
			$pre_b=$row[csf('bill_no')];
		}else{
			$bill_wise[$row[csf('bill_no')]]['cnt']+=1;
		}
		$bill_wise[$row[csf('bill_no')]]['discount']=$row[csf('discount')];
		$bill_wise[$row[csf('bill_no')]]['upchage']=$row[csf('upchage')];
	}
	
	//echo "<pre>".$sql."</pre>";

	
	$table_width="2020"; $colspan="22";
	ob_start();
	?>
     <div> 
    <?
	if($date_type==1)
	{
	?>
    <fieldset style="width:100%">	
        <table cellpadding="0" cellspacing="0" width="<?=$table_width; ?>" border="0" rules="all">
            <tr class="form_caption">
               <td align="center" width="100%" colspan="<?=$colspan; ?>" style="font-size:16px"><strong><?=$company_arr[$cbo_company_id]; ?></strong></td>
            </tr>
            <tr class="form_caption">
               <td align="center" width="100%" colspan="<?=$colspan; ?>" style="font-size:16px"><strong><?=$report_title; ?></strong></td>
            </tr>
        </table>
        <table class="rpt_table" border="1" rules="all" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                	<th width="40">SL</th>
                    <th width="60">Bill Date</th>
                    <th width="70">Program No</th>
                    <th width="110">Bill No</th>
                    <th width="100">Manual Bill No</th>
                    <th width="120">Supplier Name</th>
                    <th width="100">Buyer Name</th>
                    <th width="110">Style Ref. No</th>
                    <th width="110">Fab. Booking No</th>
                    <th width="110" >FSO No</th>
                    <th width="110">WO No</th>
                    <th width="250">Fabric Description</th>
                    <th width="80">M/C Dia <br>x<br> Gauge</th>
                    <th width="50">S.L</th>
                    <th width="80">Color Range</th>
                    <th width="70">Wo Qty.</th>
                    <th width="70">Bill Qty.</th>
                    <th width="70">Rate</th>
                    <th width="80">Amount</th>
                    <th width="70">Upcharge</th>
                    <th width="70">Discount</th>
                    <th>Net Amount</th>
                </tr>

            </thead>
       
        
           <tbody>
            
			<?php 
				$i=1;
				$j=1;
				$previous_bill_no='';
				$wo_qty=0;
				$bill_qty=0;
				$amount=0;
				$upcharge=0;
				$discount=0;
				$tot_bill_amt=0;
				foreach ($result as $row) {

					if(in_array($row[csf('wo_dtls_id')], $wo_dtls_ids))
					{

						$current_bill_no=$row[csf('bill_no')];
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if($data_arr[$row[csf('wo_dtls_id')]]['within_group']==1){

							$buyer=$company_arr[$data_arr[$row[csf('wo_dtls_id')]]['buyer_id']];
						}else{
							$buyer=$buyer_arr[$data_arr[$row[csf('wo_dtls_id')]]['buyer_id']];
						}

						$wo_qty+=$row[csf('wo_qty')];
						$bill_qty+=$row[csf('bill_qty')];
						$amount+=$row[csf('amount')];
						
						$program_no=$data_arr[$row[csf('wo_dtls_id')]]['program_no'];
				


						?>
				
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
								
								
								
								<td><?=$i; ?></td>
								<td><p><?=change_date_format($row[csf('bill_date')]); ?></p></td>
                                <td><p><?=$program_no; ?></p></td>
                                
								<td><p><?=$row[csf('bill_no')]; ?></p></td>
								<td ><p><?=$row[csf('manual_bill_no')]; ?></p></td>
								<td ><p><?=$supllier_arr[$row[csf('supplier_id')]]; ?></p></td>
								
								
								<td><p><?=$buyer; ?></p></td>
								<td><p><?=$data_arr[$row[csf('wo_dtls_id')]]['style_ref_no']; ?></p></td>
								<td><p><?=$row[csf('booking_no')]; ?></p></td>
								<td><p><?=$row[csf('fso_no')]; ?></p></td>
								<td><p>
									<? 
										$popup="<a href='##' style='color:blue; ' onclick=\"generate_wo_order_report('".$cbo_company_id."','".$data_arr[$row[csf('wo_dtls_id')]]['knitting_wo_id']."')\"><font style='font-weight:bold' >".$data_arr[$row[csf('wo_dtls_id')]]['wo_no']."</font></a>";

									echo $popup; ?></p>
								</td>
								<td><p><?=$data_arr[$row[csf('wo_dtls_id')]]['fabric_desc']; ?></p></td>
								<td align="center"><p><?=$data_arr[$row[csf('wo_dtls_id')]]['machine_dia']." x ".$data_arr[$row[csf('wo_dtls_id')]]['machine_gg']; ?></p></td>
								<td align="center"><p><?=$data_arr[$row[csf('wo_dtls_id')]]['stitch_length']; ?></p></td>
								<td ><p><?=$color_range[$data_arr[$row[csf('wo_dtls_id')]]['color_range']]; ?></p></td>
								<td align="right"><p><?=number_format($row[csf('wo_qty')],2); ?></p></td>
								<td align="right"><p><?=number_format($row[csf('bill_qty')],2); ?></p></td>
								<td align="right"><p><?=number_format($row[csf('rate')],2); ?></p></td>
								<td align="right"><?=number_format($row[csf('amount')],2); ?></td>
								<?php if($previous_bill_no!=$current_bill_no){
										$upcharge+=$row[csf('upchage')];
										$discount+=$row[csf('discount')];
										$t_amount=$bill_wise[$row[csf('bill_no')]]['amount']+$bill_wise[$row[csf('bill_no')]]['upchage']-$bill_wise[$row[csf('bill_no')]]['discount'];										$tot_bill_amt+=$t_amount;
										$cnt=$bill_wise[$row[csf('bill_no')]]['cnt'];
									?>
                                <td style="vertical-align: top" align="right" rowspan="<?php echo $cnt; ?>"><?=number_format($row[csf('upchage')],2); ?>
                                </td>
                                <td style="vertical-align: top" align="right" rowspan="<?php echo $cnt; ?>"><?=number_format($row[csf('discount')],2); ?></td>
                                <td style="vertical-align: top" align="right" rowspan="<?php echo $cnt; ?>"><?=number_format($t_amount,2); ?></td>

							<?php	}?>
								
							</tr>
						<?	
							$i++;
							$previous_bill_no=$current_bill_no;
						}
				}
			 ?>
			
			</tbody>
			 <tfoot >
			 <tr>
			 	<td colspan="15" align="right">Total</td>
			 	<td align="right"><?php echo number_format($wo_qty,2); ?></td>
			 	<td align="right"><?php echo number_format($bill_qty,2); ?></td>
			 	<td></td>
			 	<td align="right"><?php echo number_format($amount,2); ?></td>
			 	<td align="right"><?php echo number_format($upcharge,2); ?></td>
			 	<td align="right"><?php echo number_format($discount,2); ?></td>
			 	<td align="right"><?php echo number_format($tot_bill_amt,2); ?></td>
			 </tr>
			 </tfoot>
           </table>
             </fieldset>
           <?
	}
	else
	{
		   if($db_type==0)
			{
				if ($start_date!="" &&  $end_date!="") $wo_date_con  = "and a.booking_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $wo_date_con ="";
			}

			if($db_type==2)
			{
				if ($start_date!="" &&  $end_date!="") $wo_date_con  = "and a.booking_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $wo_date_con ="";
			}
		//	echo $txt_search_common.'d'.$cbo_search_by;
	if(!empty($txt_search_common))
	{
		if($cbo_search_by==1)
		{
			$wo_no_con=" and a.wo_number_prefix_num=" . $txt_search_common ;
		}
		else if($cbo_search_by==2)
		{
			$search_field_cond = " and a.prefix_no_num=" . $txt_search_common;
			echo "<b>Not allowed</b>";die;
		}
		else if($cbo_search_by==3)
		{
			$search_field_cond = " and LOWER(b.fabric_sales_order_no) like LOWER('%" . $txt_search_common . "%')";
		}
		else if($cbo_search_by==4)
		{
			$search_field_cond = " and LOWER(b.booking_no) like LOWER('%" . $txt_search_common . "%')";
		}
		else{
			$wo_no_con=	" and LOWER(b.style_ref_no) like LOWER('%" . $txt_search_common . "%')";
		}
	 
	}
		   $kw_sql="SELECT a.wo_no,a.supplier_id,a.id as knitting_wo_id,a.booking_date,b.id,b.fabric_desc,b.booking_no,b.program_no,b.machine_dia,b.machine_gg,b.stitch_length,b.color_range,b.style_ref_no,b.within_group,b.buyer_id,b.fabric_sales_order_no as sales_no,b.program_no,b.rate,b.amount,b.wo_qty from knitting_work_order_mst a , knitting_work_order_dtls b where  a.id=b.mst_id  and a.status_active=1 and b.status_active=1 $wo_date_con $wo_no_con $search_field_cond $supplier_con  $company_con";
	  //echo "<pre>".$kw_sql."</pre>";

	$kw_result=sql_select($kw_sql);
	$data_arr=array();
	$wo_dtls_ids=array();
	foreach ($kw_result as $row) {
		
		$wo_data_arr[$row[csf('id')]]['wo_no']=$row[csf('wo_no')];
		$wo_data_arr[$row[csf('id')]]['knitting_wo_id']=$row[csf('knitting_wo_id')];
		$wo_data_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
		$wo_data_arr[$row[csf('id')]]['booking_date']=$row[csf('booking_date')];
		$wo_data_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
		$wo_data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
		$wo_data_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
		$wo_data_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
		$wo_data_arr[$row[csf('id')]]['stitch_length']=$row[csf('stitch_length')];
		$wo_data_arr[$row[csf('id')]]['color_range']=$row[csf('color_range')];
		$wo_data_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$wo_data_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
		$wo_data_arr[$row[csf('id')]]['program_no']=$row[csf('program_no')];
		$wo_data_arr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
		$wo_data_arr[$row[csf('id')]]['fso_no']=$row[csf('sales_no')];
		$wo_data_arr[$row[csf('id')]]['program_no']=$row[csf('program_no')];
		$wo_data_arr[$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
		$wo_data_arr[$row[csf('id')]]['amount']=$row[csf('amount')];
		$wo_data_arr[$row[csf('id')]]['rate']=$row[csf('rate')];
		$wo_data_arr[$row[csf('id')]]['wo_qty']=$row[csf('wo_qty')];
	}
	$table_width=1620;
	$colspan="16";
		   ?>
     
     <fieldset style="width:100%">	
        <table cellpadding="0" cellspacing="0" width="<?=$table_width; ?>" border="0" rules="all">
            <tr class="form_caption">
               <td align="center" width="100%" colspan="<?=$colspan; ?>" style="font-size:16px"><strong><?=$company_arr[$cbo_company_id]; ?></strong></td>
            </tr>
            <tr class="form_caption">
               <td align="center" width="100%" colspan="<?=$colspan; ?>" style="font-size:16px"><strong><?=$report_title; ?></strong></td>
            </tr>
        </table>
        <table class="rpt_table" border="1" rules="all" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                	<th width="40">SL</th>
                    <th width="80">WO Date</th>
                    <th width="70">Program No</th>
                    <th width="110">Supplier Name</th>
                    <th width="110">Buyer Name</th>
                    <th width="110">Style Ref. No</th>
                    <th width="110">Fab. Booking No</th>
                    <th width="120">FSO No</th>
                    <th width="120">WO No</th>
                    <th width="250" >Fabric Description</th>
                    <th width="80">M/C Dia <br>x<br> Gauge</th>
                    <th width="80">S.L</th>
                    <th width="80">Color Range</th>
                    <th width="80">Wo Qty.</th>
                    <th width="80">Rate</th>
                    <th width="">Amount</th>
                </tr>
            </thead>
           <tbody>
            
			<?php 
				$i=1;
				$j=1;
				$previous_bill_no='';
				$wo_qty=0;
				$amount=0;
				foreach ($wo_data_arr as $dtls_id=>$row) {

						$current_bill_no=$row[('buyer_id')];
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($row[('within_group')]==1){
							$buyer=$company_arr[$row[('buyer_id')]];
						}else{
							$buyer=$buyer_arr[$row[('buyer_id')]];
						}
						$wo_qty+=$row[('wo_qty')];
					//	$bill_qty+=$row[('bill_qty')];
						$amount+=$row[('amount')];
						?>
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
								<td><?=$i; ?></td>
								<td><p><?=change_date_format($row[('booking_date')]); ?></p></td>
                                <td><p><?=$row[('program_no')]; ?></p></td>
								<td  title="<?=$row[('supplier_id')];?>"><p><?=$supllier_arr[$row[('supplier_id')]]; ?></p></td>
								<td><p><?=$buyer; ?></p></td>
								<td><p><?=$row[('style_ref_no')]; ?></p></td>
								<td><p><?=$row[('booking_no')]; ?></p></td>
								<td><p><?=$row[('fso_no')]; ?></p></td>
								<td><p>
									<? 
										$popup="<a href='##' style='color:blue; ' onclick=\"generate_wo_order_report('".$cbo_company_id."','".$row[('knitting_wo_id')]."')\"><font style='font-weight:bold' >".$row[('wo_no')]."</font></a>";

									echo $popup; ?></p>
								</td>
								<td><p><?=$row[('fabric_desc')]; ?></p></td>
								<td align="center"><p><?=$row[('machine_dia')]." x ".$row[('machine_gg')]; ?></p></td>
								<td align="center"><p><?=$row[('stitch_length')]; ?></p></td>
								<td ><p><?=$color_range[$row[('color_range')]]; ?></p></td>
								<td align="right"><p><?=number_format($row[('wo_qty')],2); ?></p></td>
								<td align="right"><p><?=number_format($row[('rate')],2); ?></p></td>
								<td align="right"><?=number_format($row[('amount')],2); ?></td>
							</tr>
						<?	
							$i++;
				}
			 ?>
			
			</tbody>
			 <tfoot >
			 <tr>
			 	<td colspan="13" align="right">Total</td>
			 	<td align="right"><?php echo number_format($wo_qty,2); ?></td>
			 	<td></td>
			 	<td align="right"><?php echo number_format($amount,2); ?></td>
			 	
			 </tr>
			 </tfoot>
           </table>
             </fieldset>
           <?
	}
		   ?>
     </div>     
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
	echo "$total_data####$filename";
	exit();
}

if ($action == "work_order_print") 
{
	echo load_html_head_contents("Knitting W/O ", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode("**", $data);
	
	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[1]'","image_location");
	$supplier_arr = return_library_array("select id,supplier_name from lib_supplier", 'id', 'supplier_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$address="";
?>
	
		
        <table style="margin-top:10px;" width="1200" border="1" rules="all" cellpadding="3" cellspacing="0" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Program Date</th>
                <th width="90">Program no </th>
                <th width="250">Fabric Description</th>
                <th width="100">M/C Dia x Gauge</th>
                <th width="100">S.L</th>
                <th width="150">Color Range</th>
                <th width="100">Program Qty.</th>
                <th width="80">WO Qty.</th>
                <th width="70">Rate</th>
                
                <th>Amount</th>
            </thead>
            <tbody>
            	<?php 

            		$sql="SELECT a.id,a.program_date,a.program_no,a.fabric_desc,a.machine_dia,a.machine_gg,a.stitch_length,a.color_range,a.program_qnty,a.wo_qty,a.rate,a.amount from knitting_work_order_dtls a where a.status_active=1 and a.is_deleted=0 and a.mst_id=$data[1] and a.id in (select b.wo_dtls_id from wo_bill_dtls b where b.status_active=1 and b.is_deleted=0)";
            		//echo $sql;
            		$result=sql_select($sql);
            		$i=1;
            		$program_qnty=0;
            		$wo_qty=0;
            		$amount=0;
            		foreach ($result as $row) 
            		{
            			$program_qnty+=$row[csf('program_qnty')];
            			$wo_qty+=$row[csf('wo_qty')];
            			$amount+=$row[csf('amount')];
            			
            			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

            		 ?>

            		 	<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
								<td><?=$i; ?></td>
								<td>
									<p><?=change_date_format($row[csf('program_date')]); ?></p>
								</td>
								<td><p><?php echo $row[csf('program_no')] ?></p></td>
								<td><p><?php echo $row[csf('fabric_desc')] ?></p></td>
								<td><p><?php echo $row[csf('machine_dia')]." x ".$row[csf('machine_gg')] ?></p></td>
								<td><p><?php echo $row[csf('stitch_length')] ?></p></td>
								<td><p><?php echo $color_range[$row[csf('color_range')]] ?></p></td>
								<td align="right"><p><?php echo number_format($row[csf('program_qnty')],2) ?></p></td>
								<td align="right"><p><?php echo number_format($row[csf('wo_qty')],2) ?></p></td>
								<td align="right"><p><?php echo number_format($row[csf('rate')],2) ?></p></td>
								<td align="right"><p><?php echo number_format($row[csf('amount')],2) ?></p></td>

            		 	</tr>
            			<?php 
            			$i++;
            		} 

            	?>

             </tbody>
             <tfoot>
				<tr>
					<td colspan="7" align="right">Total</td>
					<td align="right"><p><?php echo number_format($program_qnty,2) ?></p></td>
					<td align="right"><p><?php echo number_format($wo_qty,2) ?></p></td>
					<td></td>
					<td align="right"><p><?php echo number_format($amount,2) ?></p></td>
				</tr>
            </tfoot>
        </table>
		
    </div>
    <?
    exit();
}


?>