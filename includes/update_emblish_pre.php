<?
include('common.php');

 	 $color_data=sql_select("select c.status_active,c.id,c.po_break_down_id,d.job_no from wo_po_color_size_breakdown c,wo_pre_cost_mst a,wo_pre_cost_embe_cost_dtls d,wo_po_details_master e where a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and c.job_no_mst=e.job_no and d.job_no=e.job_no  and e.company_name=3 and a.entry_from=158  and to_char(a.insert_date,'YYYY')=2018 and d.cons_dzn_gmts>0 and c.status_active=0  and a.approved in(0,2) group by c.id,c.po_break_down_id,c.status_active,d.job_no ");
	

$job_color_delete="";
foreach($color_data as $row)
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
 $emblish_data=sql_select("select b.po_break_down_id,b.color_size_table_id,b.job_no from wo_pre_cos_emb_co_avg_con_dtls b,wo_pre_cost_mst a,wo_pre_cost_embe_cost_dtls c where c.id=b.pre_cost_emb_cost_dtls_id  and a.job_no=b.job_no and a.job_no=c.job_no and a.entry_from=158  and b.color_size_table_id>0 and c.cons_dzn_gmts>0  and to_char(a.insert_date,'YYYY')=2018  and c.job_no='".$row[csf('job_no')]."'  and a.approved in(0,2) group by  b.color_size_table_id,b.job_no,b.po_break_down_id ");
	 foreach( $color_data as $crow)
	 {
	 	if($row[csf('status_active')]==0)
		{
			if($crow[csf('po_break_down_id')]!=$row[csf('po_break_down_id')] && $crow[csf('color_size_table_id')]!=$row[csf('id')])
			{
				if($job_color_delete=="") $job_color_delete=$crow[csf('job_no')];else  $job_color_delete.=','.$crow[csf('job_no')];
			}
		}
		
	 }
	
	disconnect($con);
}
$jobs_color_delete=implode(",",array_unique(explode(",",$job_color_delete)));
echo "Job No# ".$jobs_color_delete;
echo "<br/>";
 

?>
