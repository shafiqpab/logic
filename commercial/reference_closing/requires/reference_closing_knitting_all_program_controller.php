<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$sample_name_library=return_library_array( "select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );
$item_arrs=return_library_array("select id,item_name from lib_garment_item where status_active=1 and is_deleted=0 order by item_name","id","item_name");
$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

function my_old($dob)
{
	$now = date('d-m-Y');
	$dob = explode('-', $dob);
	$now = explode('-', $now);
	$mnt = array(1 => 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	if (($now[2]%400 == 0) or ($now[2]%4==0 and $now[2]%100!=0)) $mnt[2]=29;
	if($now[0] < $dob[0]){
		$now[0] += $mnt[$now[1]-1];
		$now[1]--;
	}
	if($now[1] < $dob[1]){
		$now[1] += 12;
		$now[2]--;
	}
	if($now[2] < $dob[2]) return false;
	return  array('year' => $now[2] - $dob[2], 'mnt' => $now[1] - $dob[1], 'day' => $now[0] - $dob[0]);
}

if($action=="save_update_delete")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$user_code = $_SESSION['logic_erp']['user_code'];
	$unclose_id=str_replace("'","",$unclose_id);

	if($user_code!='SUPERADMIN')
	{
		die('You are not authenticated');
		disconnect($con);
	}
	
	//die();

	if($only_full==false)
	{
		//$closing_status=1;
	}

	// $closing_status=$unclose_id;
	//	echo "10**".$unclose_id.'=='.$only_full;die;

	if ($operation==0)
	{

		$con = connect();

		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$type=str_replace("'","",$cbo_ref_type);

		$txt_ref_cls_date = "'".date("d-M-Y", strtotime(str_replace("'", "", $txt_ref_cls_date)))."'";

		$id=return_next_id( "id", "inv_reference_closing", 1 ) ;//closing_status
		$field_array="id,company_id,closing_date,reference_type,closing_status,inv_pur_req_mst_id,mrr_system_no,inserted_by,insert_date";

		if($type==2)
		{
			$field_array_update="ref_closing_status*updated_by*update_date";
		}

		//print_r($total_id); die;
		$totid=str_replace("'","",$total_id);
		$all_id= explode("***",$totid);
		//	echo "10**";print_r($all_id); die;
		$totids="";$type_ids="";
		$all_id_arr_1=array();
		$all_id_arr_2=array();$all_id_type_arr=array();
		foreach($all_id as $all_ids)
		{
			list($ids,$mrr_no,$type_id)= explode('**', $all_ids);
			$all_id_arr_1[] =$ids;
			$all_id_arr_2[] =$mrr_no;
			$all_id_type_arr[$ids] =$type_id;
			$totids.=$ids.',';
			$mstIDtotids.=$mrr_no.',';
			$type_ids.=$type_id.',';
		}
		$totidss=chop($totids,",");
		$MIdtotids=chop($mstIDtotids,",");
		$type_IDs=chop($type_ids,",");

		$data_array="";
		for($j=0;$j<count($all_id_arr_1);$j++)
  		{
			if($data_array=='')
			{
				$data_array.="(".$id.",".$cbo_company_name.",".$txt_ref_cls_date.",".$cbo_ref_type.",".$unclose_id.",'".$all_id_arr_1[$j]."','".$all_id_arr_2[$j]."',999,'".$pc_date_time."')";
			}
			else
			{
				$data_array.=",(".$id.",".$cbo_company_name.",".$txt_ref_cls_date.",".$cbo_ref_type.",".$unclose_id.",'".$all_id_arr_1[$j]."','".$all_id_arr_2[$j]."',999,'".$pc_date_time."')";
			}
			
			$id=$id+1;
		}

		//print_r($data_array_update);die;		
		//echo "10**insert into inv_reference_closing (".$field_array.") values".$data_array; die;

		$rID=sql_insert("inv_reference_closing", $field_array, $data_array, 1);
		if($rID)
		{
			$flag=1;
		}
		else
		{
			$flag=0;
		}

		//echo "10**".$rID.'&&'.$rID; die;
			 
		if($flag)
		{
			if($type==2)//Kniting Closing...
			{
				if($only_full=='true')
				{
					$all_mst_arr=array_unique(explode(",", $MIdtotids));
					$all_progNo_arr=array_unique(explode(",", $totidss));

					foreach($all_mst_arr as $key)
					{
						$rID_up=execute_query("UPDATE inv_receive_master SET ref_closing_status='0', updated_by=999,update_date='$pc_date_time' where id=$key and entry_form=2",1);
					}

					foreach($all_progNo_arr as $prog_key)
					{
						$rID_up=execute_query("UPDATE ppl_planning_info_entry_dtls SET ref_closing_status='0', updated_by=999,update_date='$pc_date_time' where id=$prog_key",1);
					}
				}
				else
				{				
					$rID_up=execute_query("UPDATE inv_receive_master SET ref_closing_status='1', updated_by=999,update_date='$pc_date_time' where id in($MIdtotids) and entry_form=2",1);

					//echo "10**UPDATE inv_receive_master SET ref_closing_status='1', updated_by=999,update_date='$pc_date_time' where id in($MIdtotids) and entry_form=2"; die();

					$rID_up=execute_query("UPDATE ppl_planning_info_entry_dtls SET ref_closing_status='1', updated_by=999,update_date='$pc_date_time' where id in($totidss)",1);

					//echo "10**UPDATE ppl_planning_info_entry_dtls SET ref_closing_status='1', updated_by=999,update_date='$pc_date_time' where id in($totidss)"; die();
				}				
			}			
		}

		if($db_type==0)
		{
			if($rID && $rID_up)
			{
				mysql_query("COMMIT");
				echo "0**".$type;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$type;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_up)
			{
				oci_commit($con);
				echo "0**".$type;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$type;
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="show_details_knit_closing")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo "test";die;
	$company = str_replace("'","",$cbo_company_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$only_full=trim(str_replace("'","",$only_full));
	$check_only_full=trim(str_replace("'","",$check_only_full));
	//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
	//$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");

	die('you are not authorize this action');
 	?>
 	<script>
	 	//check all
		/*function check_all_data()
		{
		var tbl_row_count = document.getElementById( 'tbl_list').rows.length;

		tbl_row_count = tbl_row_count - 1;
		alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++)  {


			}
		}*/

		//this function for only ID
		var selected_id = new Array;
		function js_set_value(str)
		{
			
			if( jQuery.inArray( $('#chk_id_' + str).val(), selected_id ) == -1 )
			{
				selected_id.push($('#chk_id_' + str).val());
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#chk_id_' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + '***';
			}
			id = id.substr( 0, id.length - 1 );
			$('#total_id').val( id );
		}



		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value(i);
			}

			if($('#tbl_list_head thead tr #all_chk').is(':checked'))
			{
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = true;
				});
			}
			else
			{
				//var selected_id = new Array;
				$('#tbl_list tbody tr :checkbox').each(function()
				{
					this.checked = false;
				});
				//$('#total_id').val('');
			}
		}
		var ref_type=$('#cbo_ref_type').val();
		var tableFilters1 ="";

			$(document).ready(function() {
				var ref_type=$('#cbo_ref_type').val();
				if(ref_type!=370)
	            setFilterGrid('tbl_list',-1,tableFilters1);
	            //setFilterGrid('tbl_list',-1);
				//reset_hide_field();
	        });
	</script>
    <?

		$wo_date_cond="";
		if( $date_from!="" && $date_to!="" ) $wo_date_cond= " and a.receive_date between ".$txt_date_from." and ".$txt_date_to."";
		$lib_buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");
		//$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
		
		if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and c.program_date<$txt_date_to";
			
		//$result_prog=sql_select("SELECT a.id as mst_id, a.recv_number, a.receive_date, a.buyer_id, b.id as dtls_id, b.grey_receive_qnty as grey_receive_qnty, b.reject_fabric_receive as reject_fabric_receive, b.order_id, c.id as prog_no, c.program_qnty as program_qnty, c.status, c.program_date  from inv_receive_master a , pro_grey_prod_entry_dtls b, ppl_planning_info_entry_dtls c where a.id=b.mst_id and c.id=a.booking_id   and a.is_deleted=0 and a.status_active=1 and a.entry_form=2 and a.receive_basis=2 $wo_date_cond and a.company_id=$company and a.ref_closing_status=$only_full order by c.id asc");

		$prog_sql="SELECT a.id as mst_id, a.recv_number, a.receive_date, a.buyer_id, b.id as dtls_id, b.grey_receive_qnty as grey_receive_qnty, b.reject_fabric_receive as reject_fabric_receive, b.order_id, c.id as prog_no, c.program_qnty as program_qnty, c.status, c.program_date, 1 as type  from inv_receive_master a , pro_grey_prod_entry_dtls b, ppl_planning_info_entry_dtls c where a.id=b.mst_id and c.id=a.booking_id  and a.is_deleted=0 and a.status_active=1 and a.entry_form=2 and a.receive_basis=2 $wo_date_cond and a.company_id=$company and a.ref_closing_status=$only_full and c.is_deleted=0 and c.status_active=1 
		
		union all 

		SELECT 0 as mst_id, null as recv_number, null as receive_date, null as buyer_id, 0 as dtls_id, 0 as grey_receive_qnty, 0 as reject_fabric_receive, null as order_id, c.id as prog_no, c.program_qnty as program_qnty, c.status, c.program_date, 2 as type from ppl_planning_info_entry_mst b, ppl_planning_info_entry_dtls c where b.id=c.mst_id and b.is_deleted=0 and b.status_active=1 and b.company_id=$company and c.is_deleted=0 and c.status_active=1 and  c.ref_closing_status=$only_full $wo_date_cond order by type asc OFFSET 0 ROWS FETCH NEXT 1000 ROWS ONLY
		
		";

		//echo $prog_sql; die();

		$result_prog = sql_select($prog_sql);
		
		$prog_no="";
		foreach($result_prog as $row)
		{
			if($dtls_check[$row[csf('prog_no')]][$row[csf('dtls_id')]]=="")
			{
				$dtls_check[$row[csf('prog_no')]][$row[csf('dtls_id')]]=$row[csf('dtls_id')];
				$prog_dataArr[$row[csf('prog_no')]]['grey_receive_qnty']+=$row[csf('grey_receive_qnty')];
				$prog_dataArr[$row[csf('prog_no')]]['reject_fabric_receive']+=$row[csf('reject_fabric_receive')];
			}
			
			$prog_dataArr[$row[csf('prog_no')]]['program_date']=$row[csf('program_date')];
			$prog_dataArr[$row[csf('prog_no')]]['order_id']=$row[csf('order_id')];
			
			$prog_dataArr[$row[csf('prog_no')]]['status']=$row[csf('status')];
			$prog_dataArr[$row[csf('prog_no')]]['buyer_id']=$row[csf('buyer_id')];
			$prog_dataArr[$row[csf('prog_no')]]['recv_number']=$row[csf('recv_number')];
			$prog_dataArr[$row[csf('prog_no')]]['mst_id'].=$row[csf('mst_id')].',';
			if($prog_check[$row[csf('prog_no')]]=="")
			{
				$prog_check[$row[csf('prog_no')]]=$row[csf('prog_no')];
				$prog_dataArr[$row[csf('prog_no')]]['program_qnty']=$row[csf('program_qnty')];
				if($prog_no=="") $prog_no=$row[csf('prog_no')];else $prog_no.=",".$row[csf('prog_no')];
			}
			
		}
		
		$poIds=chop($prog_no,','); 
		$po_ids=count(array_unique(explode(",",$prog_no)));
		if($db_type==2 && $po_ids>1000)
		{
			$po_cond_for_in=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" b.id in($ids) or"; 
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
		}
		else
		{
			$poIds=implode(",",(array_unique(explode(",",$poIds))));
			$po_cond_for_in=" and b.id in($poIds)";
		}
		if($db_type==0) $year_field="YEAR(d.insert_date) as year"; 
		else if($db_type==2) $year_field="to_char(d.insert_date,'YYYY') as year";

		$po_result = sql_select("SELECT c.po_number,c.id as po_id,b.id as prog_no,d.style_ref_no,d.job_no,c.file_no,c.grouping as ref_no,$year_field from  ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, wo_po_break_down c,wo_po_details_master d where  b.id=a.dtls_id and c.id=a.po_id and d.job_no=c.job_no_mst and b.status_active =1 and c.status_active =1 and a.status_active =1 $po_cond_for_in");
		
		foreach($po_result as $row)
		{
			$po_arr[$row[csf('prog_no')]]['po_number'].=$row[csf('po_number')].',';
			$po_arr[$row[csf('prog_no')]]['job_no'].=$row[csf('job_no')].',';
			$po_arr[$row[csf('prog_no')]]['style_ref_no'].=$row[csf('style_ref_no')].',';
			$po_arr[$row[csf('prog_no')]]['file_no'].=$row[csf('file_no')].',';
			$po_arr[$row[csf('prog_no')]]['ref_no'].=$row[csf('ref_no')].',';
			$po_arr[$row[csf('prog_no')]]['year'].=$row[csf('year')].',';
		}
		$req_sql = "SELECT a.booking_no,c.knit_id,c.prod_id,c.requisition_no, c.yarn_qnty
		from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c
		where a.id=b.mst_id and b.id=c.knit_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond_for_in";

		$req_result = sql_select($req_sql);

		$req_no="";
		foreach($req_result as $row)
		{
			if($req_no=="") $req_no=$row[csf('requisition_no')];else $req_no.=",".$row[csf('requisition_no')];
		}
		$ReqIds=chop($req_no,','); 
		$req_ids=count(array_unique(explode(",",$ReqIds)));
		if($db_type==2 && $req_ids>1000)
		{
			$req_cond_for_in=" and (";
			$ReqIdsArr=array_chunk(explode(",",$ReqIds),999);
			foreach($ReqIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$req_cond_for_in.=" b.requisition_no in($ids) or"; 
			}
			$req_cond_for_in=chop($req_cond_for_in,'or ');
			$req_cond_for_in.=")";
		}
		else
		{
			$ReqIds=implode(",",(array_unique(explode(",",$ReqIds))));
			$req_cond_for_in=" and b.requisition_no in($ReqIds)";
		}
			
		$yarn_issue="SELECT a.id as issue_id, c.knit_id as prog_no, b.requisition_no, b.cons_quantity, b.cons_reject_qnty 
		from inv_issue_master a,inv_transaction b, ppl_yarn_requisition_entry c 
		where a.id=b.mst_id and b.requisition_no=c.requisition_no and a.entry_form=3 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $req_cond_for_in 
		group by a.id,c.knit_id ,b.requisition_no, b.cons_quantity, b.cons_reject_qnty";
			
		$yarn_result = sql_select($yarn_issue);
		$issue_id="";
		foreach($yarn_result as $row)
		{
			if($issue_id=="") $issue_id=$row[csf('issue_id')];else $issue_id.=",".$row[csf('issue_id')];
			$yarn_qty_arr[$row[csf('prog_no')]]['yarn_issue']+=$row[csf('cons_quantity')];
			$yarn_qty_arr[$row[csf('prog_no')]]['yarn_issue_rej']+=$row[csf('cons_reject_qnty')];
		}
		$IssueIds=chop($issue_id,','); 
		$issue_ids=count(array_unique(explode(",",$IssueIds)));
		if($db_type==2 && $issue_ids>1000)
		{
			$issue_cond_for_in=" and (";
			//$req_cond_for_in2=" and (";
			$IssueIdsArr=array_chunk(explode(",",$IssueIds),999);
			foreach($IssueIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$issue_cond_for_in.=" a.issue_id in($ids) or"; 
				//$req_cond_for_in2.=" a.booking_id in($ids) or"; 
			}
			$issue_cond_for_in=chop($issue_cond_for_in,'or ');
			$issue_cond_for_in.=")";
			//$req_cond_for_in2=chop($req_cond_for_in2,'or ');
			//$req_cond_for_in2.=")";
		}
		else
		{
			$IssueIds=implode(",",(array_unique(explode(",",$IssueIds))));
			$issue_cond_for_in=" and a.issue_id in($IssueIds)";
			//$req_cond_for_in2=" and a.booking_id in($poIds)";
		}
			
				
		$yarn_issue_ret="SELECT a.id, a.issue_id, c.knit_id as prog_no, b.requisition_no, b.cons_quantity, b.cons_reject_qnty 
		from inv_receive_master a, inv_transaction b, ppl_yarn_requisition_entry c 
		where a.id=b.mst_id and a.booking_id=c.requisition_no  and a.entry_form=9 and b.transaction_type=4 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $issue_cond_for_in 
		group by a.id,a.issue_id,c.knit_id,b.requisition_no,b.cons_quantity,b.cons_reject_qnty";
		$yarn_ret_result = sql_select($yarn_issue_ret);
		foreach($yarn_ret_result as $row)
		{
			$yarn_qty_arr[$row[csf('prog_no')]]['yarn_issue_ret']+=$row[csf('cons_quantity')];
			$yarn_qty_arr[$row[csf('prog_no')]]['yarn_issue_rej_ret']+=$row[csf('cons_reject_qnty')];
		}
			
			
		if($check_only_full=='true')
		{
			$closing_status=0;
		}
		else $closing_status=1;

	ob_start(); 
	$contents='';
	?>
    <div style="width:1630px">
    <input style="width:400px;" type="hidden" id="total_id" name="total_id"/>
    <table width="1630" id="tbl_list_head" class="rpt_table" rules="all" border="1" align="center">
        <thead>
            <tr>
            	<?php
            	$contents.= ob_get_flush();
				//ob_start();
            	?>
                <th width="30"><input style="margin-left:4px;" type="checkbox" id="all_chk" onclick="check_all_data();"/>
                &nbsp;
                <input style="margin-left:4px; width:20px;" type="hidden" id="unclose_id" name="unclose_id" value="<? echo $closing_status;?>"/>
                
                </th>
                <?php
            	//$contents.= ob_get_flush();
				ob_start();
            	?>
                <th width="20">SL</th>
                <th width="100">Program No</th>
                <th width="100">Program Date</th>
                <th width="100">Buyer</th>
                <th width="100">Job Year</th>
                <th width="100">Job No</th>
                <th width="100">Style Ref</th>
                <th width="90">Order No</th>
                <th width="90">File No</th>
                <th width="90">Int. Ref.</th>
                <th width="90">Prog. Qty.</th>
                <th width="90">Yarn Issue Qnty</th>
                <th width="90">Issue Return Qnty</th>
                <th width="90">Knitting Qnty</th>
                <th width="90">Reject Fabric Qnty</th>
                <th width="90">Knitting Balance</th>
                <th width="90">Process Loss Qty</th>
                <th width="">Knitting Status</th>
               
            </tr>
        </thead>
    </table>
    <div style="overflow:scroll; height:350px; width:1630px;">
    <table id="tbl_list" align="center" class="rpt_table" rules="all" border="1" width="1610">
    	<tbody>
        	<?
			
			$i=1;$k=1;
			foreach($prog_dataArr as $progNo=>$val)
			{
				
				if($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
					$po_number=rtrim($po_arr[$progNo]['po_number'],',');
					$po_number=implode(',',array_unique(explode(',',$po_number)));
					$style_ref_no=rtrim($po_arr[$progNo]['style_ref_no'],',');
					$style_ref_no=implode(',',array_unique(explode(',',$style_ref_no)));
					$file_no=rtrim($po_arr[$progNo]['file_no'],',');
					$file_no=implode(',',array_unique(explode(',',$file_no)));
					$ref_no=rtrim($po_arr[$progNo]['ref_no'],',');
					$ref_no=implode(',',array_unique(explode(',',$ref_no)));
					$job_year=rtrim($po_arr[$progNo]['year'],',');
					$job_year=implode(',',array_unique(explode(',',$job_year)));
					$job_no=rtrim($po_arr[$progNo]['job_no'],',');
					$job_no=implode(',',array_unique(explode(',',$job_no)));
					
					$mst_ids=rtrim($val[("mst_id")],',');
					$mst_ids=implode(',',array_unique(explode(',',$mst_ids)));
					
					//$yarn_qty_ret=$yarn_qty_arr[$progNo]['yarn_issue_ret'];
					//echo $yarn_qty_ret.'dd';
					
				?>
				<tr bgcolor="<? echo $bgcolor;?>" >
					<?php
	            	$contents.= ob_get_flush();
					//ob_start();
	            	?>
					<td style="word-break:break-all" width="30" align="center">
						<input type="checkbox" id="chk_id_<? echo $k;  ?>" onclick="js_set_value(<? echo $k;  ?>)" style="margin-left:3px;" value="<? echo $progNo.'**'.$mst_ids; //change_date_format ?>"/>
					</td>
					<?php
	            	//$contents.= ob_get_flush();
					ob_start();
	            	?>
					<td style="word-break:break-all" width="20" align="center"><? echo  $i++; ?></td>
					<td style="word-break:break-all" width="100" title="<? echo $val[("recv_number")];?>"><p><? echo $progNo;?>&nbsp;</p></td>
					<td style="word-break:break-all"  width="100" align="center"><p><? echo  change_date_format($val[("program_date")]);?>&nbsp;</p></td>
                    <td style="word-break:break-all"  width="100" align="center"><p><? echo  $lib_buyer_arr[$val[("buyer_id")]]; ;?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100" align="center" title=""><p><? echo  $job_year;?>&nbsp;</p></td>
					<td style="word-break:break-all" width="100"><div style="word-break:break-all"><? echo $job_no;?>	</div></td>
					<td style="word-break:break-all" width="100" align="center"><div style="word-break:break-all"><? echo $style_ref_no;?></div></td>
					<td width="90" align="center"><div style="word-break:break-all"><? echo $po_number; ?></div></td>
                    <td width="90" align="center" title=""><? echo $file_no;?></td>
                    <td width="90" align="center"><? echo $ref_no;?></td>
					<td width="90" align="right"><? echo number_format($val[("program_qnty")],2);?></td>
                    <td width="90" align="right"><? echo number_format($yarn_qty_arr[$progNo]['yarn_issue'],2);?></td>
                    <td width="90" align="right"><? echo number_format($yarn_qty_arr[$progNo]['yarn_issue_ret'],2);?></td>
                    <td width="90" align="right" title="Knitting Qty"><? echo number_format($val[("grey_receive_qnty")],2);?></td>
                    <td width="90" align="right"  title="Knitting Reject Qty"><? echo number_format($val[("reject_fabric_receive")],2);?></td>
                    <td width="90" align="right"><? echo number_format($val[("program_qnty")]-($val[("grey_receive_qnty")]+$val[("reject_fabric_receive")]),2);?></td>
                    
                    <td width="90" align="right" title="Process Loss Qty Formula: [Yarn Issue qty-(Issue Return Qnty+Knitting Qnty+Reject Fabric Qnty)]"><? echo number_format($yarn_qty_arr[$progNo]['yarn_issue']-($yarn_qty_arr[$progNo]['yarn_issue_ret']+$val[("grey_receive_qnty")]+$val[("reject_fabric_receive")]),2);?></td>

                    <td><? echo  $knitting_program_status[$val[("status")]]; ?></td>
                </tr>
				<?
				$k++;
			}
            ?>
        </tbody>
    </table>
    </div>
    <?

    $html .=$contents. ob_get_contents();
		ob_flush();
		//foreach (glob(""."*.xls") as $filename) 
		foreach (glob("*_".$user_id.".xls") as $filename)  // Only delete current user created excel/pdf file @reaz
		{			
			 @unlink($filename);
		}
		$name=time();
		//$name="$name".".xls";	
		$name = "$name"."_".$user_id.".xls";	
		$create_new_excel = fopen(''.$name, 'w');	
		$is_created = fwrite($create_new_excel,$html);
		?>
        <input type="hidden" id="txt_excl_link" value="<?php echo 'requires/'.$name; ?>" />
    </div>
	<?php 
}
?>
