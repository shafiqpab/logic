<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "",0 );     	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     	 
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_location=str_replace("'","",$cbo_location_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_search_string=trim(str_replace("'","",$txt_search_string));
	$txt_requisition_no=trim(str_replace("'","",$txt_requisition_no));
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$cbo_year_selection = str_replace("'","",$cbo_year_selection);
	$cbo_year_selection = substr($cbo_year_selection, -2);
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );

	if($cbo_company==0) $cbo_company_cond=""; else $cbo_company_cond=" and a.company_id=$cbo_company";
	if ($cbo_location==0) $location_id =""; else $location_id =" and a.location_id=$cbo_location ";	
	if ($cbo_buyer_name==0) $buyer_cond =""; else $buyer_cond =" and b.buyer_id=$cbo_buyer_name ";	
	if ($txt_requisition_no=="") $requ_no_cond =""; else $requ_no_cond =" and a.reqn_number_prefix_num=$txt_requisition_no and a.reqn_number like '%-$cbo_year_selection-%'";	

	$sales_ref="";
	if($txt_search_string !="")
	{
		if($cbo_search_by == 1)
		{
			$book_cond = " and b.program_booking_pi_no like '%".$txt_search_string."%'";
		}
		else
		{
			$sales_ref = $txt_search_string;
		}
	}



	$sales_sql = sql_select("select a.id, a.job_no from fabric_sales_order_mst a where a.job_no like '%".$sales_ref."%' and status_active=1 and is_deleted=0");
	foreach ($sales_sql as $val) 
	{
		$sales_arr[$val[csf("id")]] = $val[csf("id")];
		$sales_ref_arr[$val[csf("id")]] = $val[csf("job_no")];
	}

	if($txt_search_string !="" && $cbo_search_by==2)
	{
		$sales_ids = implode(",", array_filter($sales_arr));
		if($sales_ids!="") 
		{
			$salesCond = $sales_po_cond = ""; 
			$sales_id_arr=explode(",",$sales_ids);
			if($db_type==2 && count($sales_id_arr)>999)
			{
				$sales_id_chunk=array_chunk($sales_id_arr,999) ;
				foreach($sales_id_chunk as $chunk_arr)
				{
					$salesCond.=" c.po_id in(".implode(",",$chunk_arr).") or ";	
				}
						
				$sales_po_cond.=" and (".chop($salesCond,'or ').")";			
				
			}
			else
			{ 	
				
				$sales_po_cond.=" and c.po_id in($sales_ids)";
				  
			}

			$sales_po_cond .= " and b.is_sales=1";
		}
	}


	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
		}
		$date_cond=" and a.reqn_date between '$start_date' and '$end_date'";
	}

	$data_array = sql_select("select a.reqn_number,a.reqn_number_prefix_num,a.reqn_date,b.buyer_id, b.program_booking_pi_no, b.job_no , c.po_id, d.qnty as grey_issue_qnty, b.is_sales, b.body_part_id, b.color_type_id, b.construction, b.composition, b.gsm_weight, b.dia_width, b.color_id,  b.reqn_qty, b.remarks from pro_fab_reqn_for_batch_mst a, pro_fab_reqn_for_batch_dtls b, pro_fab_reqn_po_break c, PRO_ROLL_DETAILS d where a.id = b.mst_id and b.id =c.dtls_id and a.id = c.mst_id and b.entry_form = 123 AND d.ENTRY_FORM = 61 AND c.po_id = d.PO_BREAKDOWN_ID $cbo_company_cond $location_id $buyer_cond $date_cond $sales_po_cond $book_cond $requ_no_cond and a.is_deleted= 0 and a.status_active = 1 and  b.is_deleted= 0 and b.status_active = 1 and c.is_deleted= 0 and c.status_active = 1 AND d.is_deleted = 0 AND d.status_active = 1 order by a.id desc");

	ob_start();	
	?>
	<div id="scroll_body" align="center" style="height:auto; width:auto; margin:0 auto; padding:0;">
        <table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
               <td align="center" width="100%" colspan="16" class="form_caption" >
               	<strong style="font-size:18px"><? echo ' Company Name:' .$company_library[$cbo_company];?></strong>
               </td>
            </tr> 
            <tr>  
               <td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
            </tr>
            <tr>  
               <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
            </tr>  
        </table>
        <div align="center" style="height:auto;">
        <table width="1650" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
            <thead>
            	<tr>
            		<th width="40">Sl No.</th>
            		<th width="100">Requisition Date</th>
            		<th width="80">Year</th>
            		<th width="80">Requisition No</th>
            		<th width="100">Buyer</th>
            		<th width="100">Booking No</th>
            		<th width="110">FSO No</th>
            		<th width="100">Body Part</th>
            		<th width="100">Color TYPE</th>
            		<th width="100">Construction</th>
            		<th width="100">Composition</th>
            		<th width="80">F. GSM</th>
            		<th width="80">F. Dia</th>
            		<th width="100">Color</th>
            		<th width="100">Grey Issue</th>
            		<th width="80">Rqsn Qty</th>
            		<th width="100">Remarks</th>
            	</tr>
            </thead>
        </table>
        </div>
        <div align="left" style="width:1668px;max-height:300px; overflow-y:scroll;" id="scroll_body">
	        <table align="left" cellspacing="0" width="1650"  border="1" rules="all" class="rpt_table" id="table_body">  
	        	<tbody>
	        	<? 
	        	if(count($data_array)<1) echo "<span style='font-weight:bold;width:1000px;margin-left:600px; align:center'>Data Not Found</span>";
	        	$i = 1;
	        	foreach($data_array as $row)
	        	{
	        		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	        		?>
		        	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
		        		<td width="40" align="center"><? echo $i;?></td>
		        		<td width="100" align="center"><? echo change_date_format($row[csf("reqn_date")]);?></td>
		        		<td width="80" align="center"><? echo date("Y",strtotime($row[csf("reqn_date")]));?></td>
		        		<td width="80" align="center"><? echo $row[csf("reqn_number_prefix_num")];?></td>
		        		<td width="100" align="center"><? echo $buyer_library[$row[csf("buyer_id")]];?></td>
		        		<td width="100" align="center"><? echo $row[csf("program_booking_pi_no")];?></td>
		        		<td width="110" align="center"><? if($row[csf("is_sales")]==1) echo $sales_ref_arr[$row[csf("po_id")]];?></td>
		        		<td width="100" align="center"><? echo $body_part[$row[csf("body_part_id")]];?></td>
		        		<td width="100" align="center"><p><? echo $color_type[$row[csf("color_type_id")]];?></p></td>
		        		<td width="100" align="center"><p><? echo $row[csf("construction")];?></p></td>
		        		<td width="100" align="center"><p><? echo $row[csf("composition")];?></p></td>
		        		<td width="80" align="center"><? echo $row[csf("gsm_weight")];?></td>
		        		<td width="80" align="center"><? echo $row[csf("dia_width")];?></td>
		        		<td width="100" align="center"><p><? echo $color_library[$row[csf("color_id")]];?></p></td>
		        		<td width="100" align="center"><p><? echo $row[csf("grey_issue_qnty")];?></p></td>
		        		<td width="80" align="right"><? echo number_format($row[csf("reqn_qty")],2);?></td>
		        		<td width="100" align="center"><? echo $row[csf("remarks")];?></td>
		        	</tr>
	        		<? 
	        		$total_req_qnty += $row[csf("reqn_qty")];
					$total_grey_issue += $row[csf("grey_issue_qnty")];
	        		$i++;
	        	}
	        	?>
	        	</tbody>
	        </table>
    	</div>
    	<div align="left" style="width:1650px">
    		<table align="left" cellspacing="0" width="1650"  border="1" rules="all" class="rpt_table">  
	    		<tfoot>
	    			<tr>
	    				<th width="40">&nbsp;</th>
	            		<th width="100">&nbsp;</th>
	            		<th width="80">&nbsp;</th>
	            		<th width="80">&nbsp;</th>
	            		<th width="100">&nbsp;</th>
	            		<th width="100">&nbsp;</th>
	            		<th width="110">&nbsp;</th>
	            		<th width="100">&nbsp;</th>
	            		<th width="100">&nbsp;</th>
	            		<th width="100">&nbsp;</th>
	            		<th width="100">&nbsp;</th>
	            		<th width="80">&nbsp;</th>
	            		<th width="80">&nbsp;</th>
	            		<th width="100">Total</th>
	            		<th width="100" align="right" id="value_issue_qnty"><? echo number_format($total_grey_issue,2);?></th>
	            		<th width="80" align="right" id="value_req_qnty"><? echo number_format($total_req_qnty,2);?></th>
	            		<th width="100">&nbsp;</th>
	    			</tr>
	    		</tfoot>
    		</table>
    	</div>
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
?>
