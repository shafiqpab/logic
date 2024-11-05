<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Date Wise Production Report.
Functionality	:	
JS Functions	:
Created by		:	Md Thorat Islam
Creation date 	: 	27-Feb-2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
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
	$to_date=str_replace("'","",$txt_date_to);

	if($db_type==0) $select_from_date=change_date_format($from_date,'yyyy-mm-dd');
	if($db_type==2) $select_from_date=change_date_format($from_date,'','',1);
    
    if(str_replace("'","",$company_id)==0)$company_name=""; else $company_name=" and d.company_id=$company_id";
    
    
    if($db_type==0)
		{
			if( $from_date=='' && $to_date=='' ) $date_con=""; else $date_con= " and c.production_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";	
	 	}
		if($db_type==2)
		{
			if( $from_date=='' && $to_date=='' ) $date_con=""; else $date_con= " and c.production_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
		}

    if($cboProcess==0)
	{
        
        $job_sql="SELECT e.process,e.embellishment_type,c.production_date,	
		sum(case when a.entry_form=316  and e.process=1 and a.operation_type=2 then c.qcpass_qty else 0 end) as fainal_wash_qty
		from pro_batch_create_mst a, 
        pro_batch_create_dtls b,
        subcon_embel_production_dtls c ,
        subcon_embel_production_mst d ,
        subcon_ord_breakdown e
		where a.id=b.mst_id 
        and e.process=1 
        and  a.status_active=1 
        and c.po_id=e.mst_id 
        and a.entry_form=316 
        and b.po_id=c.po_id 
        and c.mst_id=d.id 
        and d.job_no=e.job_no_mst 
        and d.entry_form=301 
        and a.id=d.recipe_id 
        and a.is_deleted=0   
        $company_name $wash_Process $date_con
		group by e.embellishment_type,e.process,c.production_date order by c.production_date";
		$job_sql_result=sql_select($job_sql);
        //echo $job_sql;
        $tbl_width = 960+count($emb_type_arr)*60;
    }
	if($cboProcess==1)
    {
        $job_sql="SELECT e.process,e.embellishment_type,c.production_date,	
		sum(case when a.entry_form=316  and e.process=1 and a.operation_type=2 then c.qcpass_qty else 0 end) as fainal_wash_qty
		from pro_batch_create_mst a, 
        pro_batch_create_dtls b,
        subcon_embel_production_dtls c,
        subcon_embel_production_mst d,
        subcon_ord_breakdown e
		where a.id=b.mst_id 
        and e.process=1 
        and  a.status_active=1 
        and c.po_id=e.mst_id 
        and a.entry_form=316 
        and b.po_id=c.po_id 
        and c.mst_id=d.id 
        and d.job_no=e.job_no_mst 
        and d.entry_form=301 
        and a.id=d.recipe_id 
        and a.is_deleted=0   
        $company_name $wash_Process $date_con
		group by e.embellishment_type,e.process,c.production_date order by c.production_date";
        $job_sql_result=sql_select($job_sql);
        //echo $job_sql;
        $tbl_width = 960+count($emb_type_arr)*60;
	}
	?>
        <style type="text/css">
            .wrd_brk{word-break: break-all;}
        </style>
        <fieldset style="width:980px; margin:0 auto;">
        <div style="width:980px; margin:0 auto;">
            <table cellpadding="0" cellspacing="0" width="960">
                    <tr  class="form_caption" style="border:none;">
                        <td colspan="11" align="center" style="border:none; font-size:18px;">
                            Date wise Production Summery [knit Garments Wash]
                        </td>
                    </tr>
                    <tr  class="form_caption" style="border:none;">
                        <td align="center" width="100%" colspan="11" style="font-size:14px">
                            <?  echo "Date: ".$from_date." To ".$from_date;?>
                        </td>
                    </tr>
                </table>
                <table width="<?=$tbl_width;?>" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                    <?
                    $recipe_po_arr=array();
                    $emb_type_arr=array();
                        foreach ($job_sql_result as $row){
                            $data_array[$row[csf('production_date')]][$row[csf('embellishment_type')]]= $row[csf('fainal_wash_qty')];
                            $date_array[$row[csf('production_date')]]= $row[csf('production_date')];
                            $emb_type_arr[$row[csf('embellishment_type')]] = $row[csf('embellishment_type')];
                        }
                    ?>
                    <thead>
                            <th width="35">SL</th>
                            <th width="80">Date</th>
                            <?
                            foreach ($emb_type_arr as $type_id=>$type )
                            {
                                ?>
                                <th width="60"><p></p><? echo $wash_wet_process[$type_id]; ?></p></th>

                                <?                            }
                            ?>
                            <th width="80">TOTAL PRODUCTION</th>
                    </thead>
                </table>
                <div style="max-height:300px; overflow-y:scroll; width:980px" id="scroll_body">
                <table width="<?=$tbl_width;?>" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body">
                    <?  
                        $i=1;
                        $recipe_po_arr=array();
                        foreach ($job_sql_result as $row){
                            $data_array[$row[csf('production_date')]][$row[csf('embellishment_type')]]= $row[csf('fainal_wash_qty')];
                            $date_array[$row[csf('production_date')]]= $row[csf('production_date')];
                        }
                        foreach($data_array as $key => $row)
                        {
                            $total_production=0;
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="35" id="wrd_brk"><? echo $i; ?></td>
                                <td width="80" id="wrd_brk"><? echo $key; ?></td>
                                <?
                                foreach ($emb_type_arr as $type_id=>$type )
                                {
                                    ?>
                                    <td width="60" align="right"><? echo $row[$type_id]; ?></td>

                                    <?
                                    $total[$type_id]+=$row[$type_id];
                                    $type_total +=$row[$type_id];
                                    $grand_total+=$row[$type_id];
                                }

                                ?>
                                <td width="80" id="wrd_brk" align="right"><?=number_format($type_total,0);?></td>
                            </tr>
                            <?		
                        $i++;
                        $type_total=0;
                        }
                        ?>
                       
                    </table>
                </div>
            
            <table width="<?=$tbl_width?>" border="1" cellpadding="0" cellspacing="0" rules="all"> 
                <tr class="tbl_bottom">
                    <td  width="35" >&nbsp;</td>
                    <td width="80" >Total</td>

                   <?
                    foreach ($emb_type_arr as $type_id=>$type )
                    {
                        ?>
                        <td width="60" align="right"><? echo $total[$type_id]; ?></td>

                        <?
                    }
                    ?>
                    <td width="80"><?=$grand_total;?></td>
                    
                </tr>
            </table> 
        </div>
        </fieldset>
        <br/>
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