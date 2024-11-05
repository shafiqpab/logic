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
	
	extract(check_magic_quote_gpc( $process ));
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");

	$imge_arr=return_library_array( "select master_tble_id, image_location from  common_photo_library where file_type=1",'master_tble_id','image_location');
	$supllier_arr = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", 'id', 'supplier_name');

	$cbo_company_id= str_replace("'","",$cbo_company_id);
	$cbo_search_by = str_replace("'","",trim($cbo_search_by));
	$start_date=str_replace("'","",trim($txt_date_from));
	$end_date=str_replace("'","",trim($txt_date_to));
	$cbo_supplier_name=str_replace("'","",trim($cbo_supplier_name));
	$txt_search_common=str_replace("'","",trim($txt_search_common));
	$cbo_date_type=str_replace("'","",trim($cbo_date_type));
	$supplier_con='';
	$company_con='';
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


	if(!empty($txt_search_common))
	{
		if($cbo_search_by==1)
		{
			$wo_no_con=" and a.do_number_prefix_num=" . $txt_search_common ;
		}
		else if($cbo_search_by==2)
		{
			$search_field_cond = " and a.prefix_no_num=" . $txt_search_common ;
		}
		else if($cbo_search_by==3)
		{
			$wo_no_con = " and LOWER(a.fabric_sales_order_no) like LOWER('%" . $txt_search_common . "%')";
		}
		else if($cbo_search_by==4)
		{
			$wo_no_con = " and LOWER(a.booking_no) like LOWER('%" . $txt_search_common . "%')";
		}else{
			$wo_no_con=	" and LOWER(a.style_ref_no) like LOWER('%" . $txt_search_common . "%')";
		}
	}


	$wo_date_con='';


	if($cbo_date_type==1)
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
			if ($start_date!="" &&  $end_date!="") $wo_date_con  = "and a.wo_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $wo_date_con ="";
		}

		if($db_type==2)
		{
			if ($start_date!="" &&  $end_date!="") $wo_date_con  = "and a.wo_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $wo_date_con ="";
		}
	}

	$kw_sql="SELECT  a.id as dyeing_wo_id,b.id,b.fabric_desc,b.machine_dia,b.machine_gg,b.stitch_length,b.color_range,a.buyer_id,a.within_group,a.wo_date,a.do_no,a.style_ref_no,a.booking_no,a.fabric_sales_order_no,b.body_part_id,b.color_id,b.process_name,b.shade,b.proccess_loss,b.wo_qty from dyeing_work_order_mst a , dyeing_work_order_dtls b where  a.id=b.mst_id   $wo_date_con $wo_no_con ";
	//echo "<pre>".$kw_sql."</pre>";

	$kw_result=sql_select($kw_sql);
	$data_arr=array();
	$wo_dtls_ids=array();
	$wo_dtls_ids_string='';
	foreach ($kw_result as $row) {
		$wo_dtls_ids[]=$row[csf('id')];
		
		$data_arr[$row[csf('id')]]['id']=$row[csf('id')];
		$data_arr[$row[csf('id')]]['do_no']=$row[csf('do_no')];
		$data_arr[$row[csf('id')]]['wo_date']=$row[csf('wo_date')];
		$data_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
		$data_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
		$data_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
		$data_arr[$row[csf('id')]]['stitch_length']=$row[csf('stitch_length')];
		$data_arr[$row[csf('id')]]['color_range']=$row[csf('color_range')];
		$data_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$data_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
		$data_arr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
		$data_arr[$row[csf('id')]]['dyeing_wo_id']=$row[csf('dyeing_wo_id')];
		$data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
		$data_arr[$row[csf('id')]]['fabric_sales_order_no']=$row[csf('fabric_sales_order_no')];
		$data_arr[$row[csf('id')]]['body_part_id']=$row[csf('body_part_id')];
		$data_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
		$data_arr[$row[csf('id')]]['shade']=$row[csf('shade')];
		$data_arr[$row[csf('id')]]['proccess_loss']=$row[csf('proccess_loss')];
		$data_arr[$row[csf('id')]]['process_name']=$row[csf('process_name')];
		$data_arr[$row[csf('id')]]['wo_qty']=$row[csf('wo_qty')];
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
		where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.entry_form=422 and b.entry_form=422 $date_search_cond $supplier_con $company_con $search_field_cond $wo_dtls_cond order by a.id,b.id";

	//echo "<pre>".$sql."</pre>";
	$result=sql_select($sql);
	//print_r($result);

	$bill_wise=array();
	$pre_b='';
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


	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	
	
	$table_width="2650"; $colspan="24";
	ob_start();
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
                    <th width="112">Bill No</th>
                    <th width="100">Manual Bill No</th>
                    <th width="122">Supplier Name</th>
                    <th width="100">Buyer Name</th>
                    <th width="110">Style Ref. No</th>
                    <th width="110">Fab. Booking No</th>
                    <th width="110" >FSO No</th>
                    <th width="112">WO No</th>
                    <th width="160">Body Part</th>
                    <th width="250">Fabric Description</th>
                    <th width="250">Color Name</th>
                    <th width="80">Color Range</th>
                    
                    <th width="70">Shade %</th>
                    <th width="70">Process Loss %</th>
                    <th width="250">Process Name</th>
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

						
						

						$wo_qty+=$data_arr[$row[csf('wo_dtls_id')]]['wo_qty'];
						$bill_qty+=$row[csf('bill_qty')];
						$amount+=$row[csf('amount')];

						 $cons_comp=$constructtion_arr[$data_arr[$row[csf('wo_dtls_id')]]['fabric_desc']].", ".$composition_arr[$data_arr[$row[csf('wo_dtls_id')]]['fabric_desc']];

					 	$color='';
					 	$color_id=array_unique(explode(",",$data_arr[$row[csf('wo_dtls_id')]]['color_id']));
					 	foreach($color_id as $val)
					 	{
					 		if($val>0) $color.=$color_arr[$val].",";
					 	}
					 	$color=chop($color,',');
					 	$process_names=explode(",", $data_arr[$row[csf('wo_dtls_id')]]['process_name']);
					 	$process='';
					 	foreach($process_names as $id)
					     {
					     	$process.=$conversion_cost_head_array[$id].",";
					     }
					     $process=chop($process,',');
					     $buyer='';
					     if($data_arr[$row[csf('wo_dtls_id')]]['within_group']==1)
					     {
					     	$buyer=$company_arr[$data_arr[$row[csf('wo_dtls_id')]]['buyer_id']];
					     }else{
					     	$buyer=$buyer_arr[$data_arr[$row[csf('wo_dtls_id')]]['buyer_id']];
					     }
				


						?>
				
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
								
								
								
								<td ><?=$i; ?></td>
								<td ><p><?=change_date_format($row[csf('bill_date')]); ?></p></td>
								<td ><p><?=$row[csf('bill_no')]; ?></p></td>
								<td ><p><?=$row[csf('manual_bill_no')]; ?></p></td>
								<td ><p><?=$supllier_arr[$row[csf('supplier_id')]]; ?></p></td>
								
								
								<td><p><?=$buyer; ?></p></td>
								<td><p><?=$data_arr[$row[csf('wo_dtls_id')]]['style_ref_no']; ?></p></td>
								<td><p><?=$data_arr[$row[csf('wo_dtls_id')]]['booking_no']; ?></p></td>
								<td><p><?=$data_arr[$row[csf('wo_dtls_id')]]['fabric_sales_order_no']; ?></p></td>
								<td><p>
									<? 
										$popup="<a href='##' style='color:blue; ' onclick=\"generate_wo_order_report('".$cbo_company_id."','".$data_arr[$row[csf('wo_dtls_id')]]['dyeing_wo_id']."')\"><font style='font-weight:bold' >".$data_arr[$row[csf('wo_dtls_id')]]['do_no']."</font></a>";

									echo $popup; ?></p>
								</td>
								<td><p><?=$body_part[$data_arr[$row[csf('wo_dtls_id')]]['body_part_id']] ?></p></td>
								<td><p><?=$cons_comp; ?></p></td>
								<td><p><?=$color; ?></p></td>
								<td><p><?=$color_range[$data_arr[$row[csf('wo_dtls_id')]]['color_range']]; ?></p></td>
								<td><p><?=$data_arr[$row[csf('wo_dtls_id')]]['shade']; ?></p></td>
								<td><p><?=$data_arr[$row[csf('wo_dtls_id')]]['proccess_loss']; ?></p></td>
								<td><p><?=$process; ?></p></td>
								<td align="right"><p><?=number_format($data_arr[$row[csf('wo_dtls_id')]]['wo_qty'],2); ?></p></td>
								<td align="right"><p><?=number_format($row[csf('bill_qty')],2); ?></p></td>
								<td align="right"><p><?=number_format($row[csf('rate')],2); ?></p></td>
								<td align="right"><?=number_format($row[csf('amount')],2); ?></td>
								<?php if($previous_bill_no!=$current_bill_no){

										$upcharge+=$row[csf('upchage')];
										$discount+=$row[csf('discount')];
										$t_amount=$bill_wise[$row[csf('bill_no')]]['amount']+$bill_wise[$row[csf('bill_no')]]['upchage']-$bill_wise[$row[csf('bill_no')]]['discount'];
										$tot_bill_amt+=$t_amount;
										$cnt=$bill_wise[$row[csf('bill_no')]]['cnt'];

									?>
									
									<td style="vertical-align: top;" align="right"  rowspan="<?php echo $cnt; ?>"><?=number_format($bill_wise[$row[csf('bill_no')]]['upchage'],2); ?>
										
									</td>
									<td style="vertical-align: top;" align="right" rowspan="<?php echo $cnt; ?>"><?=number_format($row[csf('discount')],2); ?></td>
									<td style="vertical-align: top;" align="right"  rowspan="<?php echo $cnt; ?>"><?=number_format($t_amount,2); ?></td>

							<?php	}?>

								
							</tr>

				
						
						<?	
							$i++;
							$previous_bill_no=$current_bill_no;

						}
					

				}
			 ?>
			</tbody>
			 <tfoot>
			 	<td colspan="17" align="right">Total</td>
			 	<td align="right"><?php echo number_format($wo_qty,2); ?></td>
			 	<td align="right"><?php echo number_format($bill_qty,2); ?></td>
			 	<td></td>
			 	<td align="right"><?php echo number_format($amount,2); ?></td>
			 	<td align="right"><?php echo number_format($upcharge,2); ?></td>
			 	<td align="right"><?php echo number_format($discount,2); ?></td>
			 	<td align="right"><?php echo number_format($tot_bill_amt,2); ?></td>
			 	
			 	
			 </tfoot>
           </table>
        
        
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
	echo "$total_data####$filename";
	exit();
}

if ($action == "work_order_print") 
{
	echo load_html_head_contents("Dyeing W/O ", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode("**", $data);
	
	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[1]'","image_location");
	$supplier_arr = return_library_array("select id,supplier_name from lib_supplier", 'id', 'supplier_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$yarn_lot_arr=return_library_array( "select d.barcode_no, c.yarn_lot from pro_grey_prod_entry_dtls c, pro_roll_details d where c.id=d.dtls_id and c.status_active=1 and d.status_active=1 and d.entry_form in(2,22,58)", "barcode_no", "yarn_lot");

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
	
		
        <table style="margin-top:10px;" width="1700" border="1" rules="all" cellpadding="3" cellspacing="0" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Issue Date</th>
                <th width="110">Issue No  </th>
                <th width="180">Body Part</th>
                <th width="270">Fabric Construction<br> &<br> Composition</th>
                <th width="40">GSM</th>
                <th width="40">DIA</th>
                <th width="40">S.L</th>
                <th width="100">Yarn Lot</th>
                <th width="130">Yarn Count</th>
                <th width="70">Issue Qty.</th>
                <th width="70">WO Qty.</th>
                <th width="70">Rate</th>
                <th width="80">Amount</th>
                
                <th>Remarks</th>
            </thead>
            <tbody>
            	<?php 

            		$sql="SELECT a.id,a.issue_date,a.issue_no,a.body_part_id,a.fabric_desc,a.machine_dia,a.machine_gg,a.stitch_length,a.yarn,a.issue_qnty,a.wo_qty,a.rate,a.amount,a.remark_text,a.barcode_no from dyeing_work_order_dtls a where a.status_active=1 and a.is_deleted=0 and a.mst_id=$data[1] and a.id in (select b.wo_dtls_id from wo_bill_dtls b where b.status_active=1 and b.is_deleted=0)";
            		//echo $sql;die;
            		$result=sql_select($sql);
            		$i=1;
            		$issue_qnty=0;
            		$wo_qty=0;
            		$amount=0;
            		foreach ($result as $row) 
            		{
            			$issue_qnty+=$row[csf('issue_qnty')];
            			$wo_qty+=$row[csf('wo_qty')];
            			$amount+=$row[csf('amount')];

            			$cons_comp=$constructtion_arr[$row[csf('fabric_desc')]].", ".$composition_arr[$row[csf('fabric_desc')]];

            			$yarn_count_arr=explode(",",$row[csf('yarn')]);
            			$$yarn_count_arr=array_unique($yarn_count_arr);
            			$Ycount='';
						foreach($yarn_count_arr as $count_id)
						{
							if($Ycount=='') $Ycount=$yarn_count_details[$count_id]; else $Ycount.=",".$yarn_count_details[$count_id];
						}
            			
            			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

            		 ?>

            		 	<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
								<td><?=$i; ?></td>
								<td>
									<p><?=change_date_format($row[csf('issue_date')]); ?></p>
								</td>
								<td><p><?php echo $row[csf('issue_no')]; ?></p></td>
								<td><p><?php echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
								<td><p><?php echo $cons_comp; ?></p></td>
								<td><p><?php echo $row[csf('machine_gg')]; ?></p></td>
								<td><p><?php echo $row[csf('machine_dia')]; ?></p></td>
								<td><p><?php echo $row[csf('stitch_length')]; ?></p></td>
								<?php 
									$barcodes=explode(",", $row[csf('barcode_no')]);
									$lot_arr=array();
									foreach ($barcodes as $barcode) {
										array_push($lot_arr, $yarn_lot_arr[$barcode]);
									}
									$lot_arr=array_unique($lot_arr);
									$lot=implode(",", $lot_arr);
								 ?>
								<td><p><?php echo $lot; ?></p></td>
								<td><p><?php echo $Ycount; ?></p></td>
								<td align="right"><p><?php echo number_format($row[csf('issue_qnty')],2) ;?></p></td>
								<td align="right"><p><?php echo number_format($row[csf('wo_qty')],2) ; ?></p></td>
								<td align="right"><p><?php echo number_format($row[csf('rate')],2) ;?></p></td>
								<td align="right"><p><?php echo number_format($row[csf('amount')],2); ?></p></td>
								<td><p><?php echo $row[csf('remark_text')] ;?></p></td>

            		 	</tr>
            			<?php 
            			$i++;
            		} 

            	?>

             </tbody>
             <tfoot>
				<tr>
					<td colspan="10" align="right">Total</td>
					<td align="right"><p><?php echo number_format($issue_qnty,2) ?></p></td>
					<td align="right"><p><?php echo number_format($wo_qty,2) ?></p></td>
					<td align="right"></td>
					<td align="right"><p><?php echo number_format($amount,2) ?></p></td>
					<td></td>
				</tr>
            </tfoot>
        </table>
		
    </div>
    <?
    exit();
}


?>