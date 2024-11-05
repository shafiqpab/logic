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
	$to_date=str_replace("'","",$txt_date_to);
	if($db_type==0) $select_from_date=change_date_format($from_date,'yyyy-mm-dd');
	if($db_type==2) $select_from_date=change_date_format($from_date,'','',1);
	if($db_type==0) $select_to_date=change_date_format($to_date,'yyyy-mm-dd');
	if($db_type==2) $select_to_date=change_date_format($to_date,'','',1);
	
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
			if($search_type==1) $search_com_cond=" and c.buyer_po_no like '%$search_str'";
			else if($search_type==2) $search_com_cond=" and c.buyer_style_ref like '%$search_str'"; 
			else if($search_type==3) $search_com_cond=" and c.job_no_mst like '%$search_str'";  
			else if($search_type==4) $search_com_cond=" and c.order_no like '%$search_str'";  
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
	if(str_replace("'","",$company_id)==0)$company_name=""; else $company_name=" and a.company_id=$company_id";
	if($db_type==0)
	{
		if( $from_date==0) $batch_date=""; else $batch_date= " and a.batch_date='".change_date_format($from_date,'yyyy-mm-dd')."'";
			
 	}
	if($db_type==2)
	{
		if( $from_date==0) $batch_date=""; else $batch_date= " and a.batch_date='".change_date_format($from_date,'','',1)."'";	
	}
		
		
	//batch_date, batch_against, batch_for, 
  // company_id
		
		if($db_type==0) $operation_type=",group_concat(a.operation_type) as operation_type";
		else if($db_type==2) $operation_type=",listagg(a.operation_type,',') within group (order by a.operation_type) as operation_type";
		
		 $batch_sql = "select count(a.id) as batch ,d.party_id,c.party_buyer_name,c.job_no_mst,c.buyer_style_ref,a.color_id,b.prod_id,sum(b.roll_no) as batch_qnty $operation_type from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_ord_dtls c ,subcon_ord_mst d 
			where a.id=b.mst_id and a.process_id='1' and  a.status_active=1 and  a.entry_form=316 and b.po_id=c.id and c.mst_id=d.id and  d.entry_form=295 and a.is_deleted=0  $company_name $batch_date $party_con $within_group $search_com_cond group by d.party_id,c.party_buyer_name,c.job_no_mst,c.buyer_style_ref,a.color_id,b.prod_id";
	
	// echo $batch_sql;
	 
	$batch_sql_result=sql_select($batch_sql);	
	ob_start();
	?>
     <style type="text/css">
		.wrd_brk{word-break: break-all;}
	</style>
     <fieldset style="width:1020px;">
     <div style="width:1020px; margin:0 auto;">
          <table cellpadding="0" cellspacing="0" width="1020">
         		<tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="12" style="font-size:20px"><strong><? echo 'Wash Batch Status Report'; ?></strong></td>
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
            <table width="1000" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                        <th width="35">SL</th>
                        <th width="100">Party Name</th>
                        <th width="100">Buyer</th>
                        <th width="100">Job No</th>
                        <th width="100">Style</th>
                        <th width="100">Color</th>
                        <th width="100">Gmt Item</th>
                        <th width="100">Wash Type</th>
                        <th width="80">No OF Batch</th>
                        <th width="80">Quantity/Batch</th>
                        <th>Total</th>
                </thead>
            </table>
             <div style="max-height:300px; overflow-y:scroll; width:1020px" id="scroll_body">
             <table width="1000" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body">
				<?  
					$i=1;
					
					foreach($batch_sql_result as $row)
					{
						$operation_type="";
						//$operation_type_id=explode(",",$row[csf('operation_type_id')]); $wash_operation_arr
						 $operation_type_id=array_unique(explode(",",$row[csf("operation_type")]));
						foreach($operation_type_id as $type)
						{
							if($operation_type=="") $operation_type=$type; else $operation_type.=','.$type;
						}
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
                            <td  width="100" id="wrd_brk"><? echo $row[csf("job_no_mst")]; ?></td>
                            <td   width="100" id="wrd_brk"><? echo $row[csf("buyer_style_ref")]; ?></td>
                            <td width="100" id="wrd_brk"><? echo $color_library_arr[$row[csf("color_id")]]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $garments_item[$row[csf("prod_id")]]; ?></td>
                            <td  width="100" id="wrd_brk" align="center"><? echo "Gamts wash"; ?></td>
                            <td  width="80" id="wrd_brk" align="center"><? echo $row[csf("batch")]; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $row[csf('batch_qnty')]; ?></td>
                            <td id="wrd_brk" align="right"><?php echo $row[csf('batch_qnty')]; ?></td>
						  </tr>
						<?		
					$i++;
					}
                  ?>
                </table>
         	</div>
         <table width="1000" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td  width="35" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td   width="100" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td width="80">Grand Total:</td>
                <td width="80" id="gt_batch_qty_id"></td>
                <td  id="gt_batch_qty_ids"></td>
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