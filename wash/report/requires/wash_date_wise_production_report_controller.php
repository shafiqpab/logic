<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id=str_replace("'","",$cbo_company_id);
	$cboProcess=str_replace("'","",$cboProcess);
	$from_date=str_replace("'","",$txt_date_from);
	if($db_type==0) $select_from_date=change_date_format($from_date,'yyyy-mm-dd');
	if($db_type==2) $select_from_date=change_date_format($from_date,'','',1);

	
	
	
	
	if(str_replace("'","",$company_id)==0)$company_name=""; else $company_name=" and d.company_id=$company_id";
	
	if($db_type==0)
	{
		if( $from_date==0) $production_date=""; else $production_date= " and c.production_date='".change_date_format($from_date,'yyyy-mm-dd')."'";	
	}
	if($db_type==2)
	{
		if( $from_date==0) $production_date=""; else $production_date= " and c.production_date='".change_date_format($from_date,'','',1)."'";	
	}

	
	
	
	if($cboProcess==0)
	{
			 $job_sql="select e.process,e.embellishment_type,	
		sum(case when a.entry_form=316  and e.process=1 and a.operation_type=1 then c.qcpass_qty else 0 end) as first_wash_qty,
		sum(case when a.entry_form=316  and e.process=1 and a.operation_type=2 then c.qcpass_qty else 0 end) as fainal_wash_qty,
		sum(case when a.entry_form=316  and e.process=1  then c.rewash_qty else 0 end) as rewash_qty
		from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_embel_production_dtls c ,subcon_embel_production_mst d ,subcon_ord_breakdown e
		where a.id=b.mst_id and e.process=1 and  a.status_active=1 and c.po_id=e.mst_id and   
		a.entry_form=316 and b.po_id=c.po_id and c.mst_id=d.id and d.job_no=e.job_no_mst and d.entry_form=301 and a.id=d.recipe_id and a.is_deleted=0   $company_name $wash_Process $production_date
		group by e.embellishment_type,e.process";
		$job_sql_result=sql_select($job_sql);
	}
	if($cboProcess==1)
		{
			 $job_sql="select e.process,e.embellishment_type,	
		sum(case when a.entry_form=316  and e.process=1 and a.operation_type=1 then c.qcpass_qty else 0 end) as first_wash_qty,
		sum(case when a.entry_form=316  and e.process=1 and a.operation_type=2 then c.qcpass_qty else 0 end) as fainal_wash_qty,
		sum(case when a.entry_form=316  and e.process=1  then c.rewash_qty else 0 end) as rewash_qty
		from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_embel_production_dtls c ,subcon_embel_production_mst d ,subcon_ord_breakdown e
		where a.id=b.mst_id and e.process=1 and  a.status_active=1 and c.po_id=e.mst_id and   
		a.entry_form=316 and b.po_id=c.po_id and c.mst_id=d.id and d.job_no=e.job_no_mst and d.entry_form=301 and a.id=d.recipe_id and a.is_deleted=0   $company_name $wash_Process $production_date
		group by e.embellishment_type,e.process";
	
	$job_sql_result=sql_select($job_sql);
	}
	
	/* $dry_sql="select e.process,e.embellishment_type,	
	sum(case when d.entry_form=342  and e.process=2  then c.qcpass_qty else 0 end) as qcpass_qty
	from subcon_embel_production_dtls c ,subcon_embel_production_mst d ,subcon_ord_breakdown e
	where  e.process=2 and   c.po_id=e.mst_id and   
	c.mst_id=d.id  and d.job_no=e.job_no_mst and  d.entry_form=342 and c.status_active=1 and c.is_deleted=0   $company_name $wash_Process $production_date
	group by e.embellishment_type,e.process";*/
	
	if($cboProcess==0)
	{
		$dry_Process=" and c.process_id in ('2','3')";
	}
	if($cboProcess==1)
	{
		$dry_Process=" and c.process_id in ('1')";
	}
	if($cboProcess==2)
	{
		$dry_Process=" and c.process_id in ('2')";
	}
	if($cboProcess==3)
	{
		$dry_Process=" and c.process_id in ('3')";
	}
	
	
	$dry_sql="select c.process_id,c.wash_type_id,
	sum(case when d.entry_form=342  and c.process_id='2'  then c.qcpass_qty else 0 end) as dry_qcpass_qty,
	sum(case when d.entry_form=342  and c.process_id='3'  then c.qcpass_qty else 0 end) as leger_qcpass_qty
	from subcon_embel_production_dtls c ,subcon_embel_production_mst d
	where  c.mst_id=d.id and  d.entry_form=342 and c.status_active=1 and c.is_deleted=0   $company_name $dry_Process $production_date
	group by c.process_id,c.wash_type_id";
	$dry_sql_result=sql_select($dry_sql);
	ob_start();
	?>
    <style type="text/css">
		.wrd_brk{word-break: break-all;}
	</style>
     <fieldset style="width:620px; margin:0 auto;">
     <div style="width:620px; margin:0 auto;">
          <table cellpadding="0" cellspacing="0" width="620">
                <tr  class="form_caption" style="border:none;">
                    <td colspan="9" align="center" style="border:none; font-size:14px;">
                        <b><? echo $company_library[$company_id]; ?></b>
                    </td>
                </tr>
                 <tr  class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="9" style="font-size:12px">
                         <? echo "Wet Process Production Report";?>
                    </td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="9" style="font-size:12px">
                        <? if(str_replace("'","",$txt_date_from)!="") echo "Production Date: ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy');?>
                    </td>
                </tr>
            </table>
            <table width="600" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                        <th width="35">SL</th>
                        <th width="100">Type Of wash Process</th>
                        <th width="100">First wash</th>
                        <th width="100">Final wash</th>
                        <th width="130">Gross Total pcs</th>
                        <th>Final Production pcs</th>
                </thead>
            </table>
             <div style="max-height:300px; overflow-y:scroll; width:620px" id="scroll_body">
             <table width="600" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body">
				<?  
					$i=1;
					$recipe_po_arr=array();
					foreach($job_sql_result as $row)
					{
						?>
                          <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td  width="35" id="wrd_brk"><? echo $i; ?></td>
                            <td width="100" id="wrd_brk"><? echo $wash_wet_process[$row[csf("embellishment_type")]]; ?></td> 
                            <td  width="100" id="wrd_brk"><? echo $row[csf("first_wash_qty")]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $row[csf("fainal_wash_qty")]; ?></td>
                            <td   width="130" id="wrd_brk"></td>
                            <td id="wrd_brk"><? echo $row[csf("first_wash_qty")]+$row[csf("fainal_wash_qty")]; ?></td>
						  </tr>
						<?		
					$i++;
					}
                  ?>
                </table>
         	</div>
         <table width="600" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td  width="35" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td   width="130" >Total</td>
                <td id="gt_blance_qty_id"></td>
                
			</tr>
		</table> 
     </div>
     </fieldset>
     <br/>
     <fieldset style="width:320px; margin:0 auto;">
     <div style="width:320px; margin:0 auto;">
          <table cellpadding="0" cellspacing="0" width="320">
                
                 <tr  class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="9" style="font-size:12px">
                         <? echo "Dry Process Production Report";?>
                    </td>
                </tr>
            </table>
            <table width="300" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                        <th width="35">SL</th>
                        <th width="100">Type Of wash Process</th>
                        <th>Production Qty pcs</th>
                </thead>
            </table>
             <div style="max-height:300px; overflow-y:scroll; width:320px" id="scroll_body1">
             <table width="300" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body1">
				<?  
					$i=1;
					foreach($dry_sql_result as $row)
					{
						?>
                          <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td  width="35" id="wrd_brk"><? echo $i; ?></td>
                            <td width="100" id="wrd_brk"><? 
							if($row[csf("process_id")]==2){
							echo $wash_dry_process[$row[csf("wash_type_id")]];}elseif($row[csf("process_id")]==3){echo $wash_laser_desing[$row[csf("wash_type_id")]];} 	
							?></td> 
                            <td id="wrd_brk"><? 
							if($row[csf("process_id")]==2){
							echo $row[csf("dry_qcpass_qty")];}elseif($row[csf("process_id")]==3){echo $row[csf("leger_qcpass_qty")];} ?></td>
						  </tr>
						<?		
					$i++;
					}
                  ?>
                </table>
         	</div>
         <table width="300" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td  width="35" >&nbsp;</td>
                <td width="100" >Total</td>
                <td id="gt_qcpass_qty_id"></td>
			</tr>
		</table> 
     </div>
     </fieldset>
     <br/>
      <fieldset style="width:320px; margin:0 auto;">
     <div style="width:320px; margin:0 auto;">
            <table width="300" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                        <th width="35">SL</th>
                        <th width="100">Type Of wash Process</th>
                        <th>Re-Wash pcs</th>
                </thead>
            </table>
             <div style="max-height:300px; overflow-y:scroll; width:320px" id="scroll_body2">
             <table width="300" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body2">
				<?  
					$i=1;
					foreach($job_sql_result as $row)
					{
						?>
                          <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td  width="35" id="wrd_brk"><? echo $i; ?></td>
                            <td width="100" id="wrd_brk"><? echo $wash_wet_process[$row[csf("embellishment_type")]]; ?></td> 
                            <td id="wrd_brk"><? echo $row[csf("rewash_qty")]; ?></td>
						  </tr>
						<?		
					$i++;
					}
                  ?>
                </table>
         	</div>
             <table width="300" border="1" cellpadding="0" cellspacing="0" rules="all"> 
                <tr class="tbl_bottom">
                    <td  width="35" >&nbsp;</td>
                    <td width="100" >Total</td>
                    <td id="gt_rewash_qty_id"></td>
                </tr>
            </table> 
     </div>
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