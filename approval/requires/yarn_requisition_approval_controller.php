<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];
$menu_id=$_SESSION['menu_id'];
$user_id=$_SESSION['logic_erp']['user_id'];
include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
//$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_name=str_replace("'","",$cbo_company_name);
	$approval_type=str_replace("'","",$cbo_approval_type);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$user_id=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;

	//echo $menu_id;die;

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	//echo "select sequence_no from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0";
	//echo $user_sequence_no;die("with fiq");
	$min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0","seq");
	
	//print_r($user_sequence_no);die;
	
	$buyer_ids_array=array();
	//echo "select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2";die;
	$buyerData=sql_select("select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2");
	foreach($buyerData as $row)
	{
		//$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
	}
	
	$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0 and buyer_id is null");
	//echo "select max(sequence_no) from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0 $buyer_id_cond_seq";die;
	
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Pre-Costing.</font>";
		die;
	}
	if($approval_type ==0 ){ //Approval Type = un-approved (List of un-approved requ nos)

		if($user_sequence_no==$min_sequence_no) // first approval authority
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and b.buyer_id in($buyer_ids)";
			//echo $buyer_id_cond2;die;

			$sql="SELECT a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.supplier_id,  a.requisition_date, a.delivery_date, a.is_approved, listagg(b.buyer_id, ',') within group (order by b.buyer_id) buyer_id
			from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
			where a.id=b.mst_id and a.company_id=$company_name and a.item_category_id=1  and a.is_deleted=0 and a.status_active=1 and a.ready_to_approve=1 and (a.is_approved in (0) or a.is_approved is null) $buyer_id_cond2 group by a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.requisition_date, a.delivery_date, a.supplier_id, a.is_approved
			  order by a.requisition_date desc";
            //echo $sql;//die("with sumon");

		}
		else if($sequence_no=="") //last approval authority having bypass=no previlages // Next User bypass Yes
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			//echo $buyer_ids;die;
			if($buyer_ids=="") $buyer_id_condnon2=""; else $buyer_id_condnon2=" and b.buyer_id in($buyer_ids)";
			
			
			if($db_type==2)
			{
				$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
				//echo $seqSql;die;
				$seqData=sql_select($seqSql);
				
				
				$buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
				foreach($seqData as $sRow)
				{
					if($sRow[csf('bypass')]==2)
					{
						$sequence_no_by_no.=$sRow[csf('sequence_no')].",";
						if($sRow[csf('buyer_id')]!="") 
						{
							$buyerIds.=$sRow[csf('buyer_id')].",";
							
							$buyer_id_arr=explode(",",$sRow[csf('buyer_id')]);
							$result=array_diff($buyer_id_arr,$check_buyerIds_arr);
							if(count($result)>0)
							{
								$query_string.=" (c.sequence_no=".$sRow[csf('sequence_no')]." and b.buyer_id in(".implode(",",$result).")) or ";
							}
							$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
						}
					}
					else
					{
						$sequence_no_by_yes.=$sRow[csf('sequence_no')].",";
					}
				}
				//var_dump($check_buyerIds_arr);die;
				$buyerIds=chop($buyerIds,',');
				if($buyerIds=="") 
				{
					$buyerIds_cond=""; 
					$seqCond="";
				}
				else 
				{
					$buyerIds_cond=" and b.buyer_id not in($buyerIds)";
					$seqCond=" and (".chop($query_string,'or ').")";
				}
				//echo $seqCond;die;
				$sequence_no_by_no=chop($sequence_no_by_no,',');
				$sequence_no_by_yes=chop($sequence_no_by_yes,',');
				
				if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
				if($sequence_no_by_no=="") $sequence_no_by_no=0;
				
				
				//$booking_id=return_field_value("listagg(mst_id,',') within group (order by mst_id) as booking_id","wo_non_ord_samp_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.item_category in(4) and a.booking_type=5 and b.sequence_no in ($sequence_no_by) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_cond","booking_id");
				//$booking_id=implode(",",array_unique(explode(",",$booking_id)));
				
				//	$booking_id_app_byuser=return_field_value("listagg(mst_id,',') within group (order by mst_id) as booking_id","wo_non_ord_samp_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.item_category in(4) and  a.booking_type=5 b.sequence_no=$user_sequence_no and b.entry_form=8 and b.current_approval_status=1","booking_id");            
				//	$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
				
				$booking_id='';
				/*$req_id_sql="SELECT 
				a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.supplier_id, a.requisition_date, a.delivery_date, a.is_approved, listagg(b.buyer_id, ',') within group (order by b.buyer_id) buyer_id
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
		where a.id=b.mst_id and a.company_id=$company_name  and a.is_deleted=0 and a.status_active=1 and a.ready_to_approve=1 and a.is_approved in (0,3) $buyer_id_cond2 group by a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.requisition_date, a.delivery_date, a.supplier_id, a.is_approved order by requisition_date desc";*/
				//$booking_id_sql="select distinct (c.mst_id) as booking_id from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, approval_history c  where a.id=b.mst_id and a.id=c.mst_id and b.mst_id=c.mst_id and a.company_id=$company_name and c.sequence_no in ($sequence_no_by_no) and c.entry_form=20 and c.current_approval_status=1 $buyer_id_condnon2  $seqCond";
				
				$booking_id_sql="select distinct (c.mst_id) as booking_id from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, approval_history c  where a.id=b.mst_id and a.id=c.mst_id and b.mst_id=c.mst_id and a.company_id=$company_name  and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.sequence_no in ($sequence_no_by_no) and c.entry_form=20 and c.current_approval_status=1 $buyer_id_condnon2  $seqCond
				union
				select distinct (c.mst_id) as booking_id from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, approval_history c  where a.id=b.mst_id and a.id=c.mst_id and b.mst_id=c.mst_id and a.company_id=$company_name and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.sequence_no in ($sequence_no_by_yes) and c.entry_form=20 and c.current_approval_status=1 $buyer_id_condnon2  ";
				//echo $booking_id_sql;die;
				$bResult=sql_select($booking_id_sql);
				foreach($bResult as $bRow)
				{
					$booking_id.=$bRow[csf('booking_id')].",";
				}
				
				$booking_id=chop($booking_id,',');
				
				$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","inv_purchase_requisition_mst a, approval_history b","a.id=b.mst_id  and a.is_deleted=0 and a.status_active=1 and a.company_id=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=20 and b.current_approval_status=1","booking_id");
				$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
				
				$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
				$booking_id=implode(",",$result);
				
				$booking_id_cond="";
				if($booking_id!="")
				{
					if($db_type==2 && count($result)>999)
					{
						$booking_id_chunk_arr=array_chunk($result,999) ;
						foreach($booking_id_chunk_arr as $chunk_arr)
						{
							$chunk_arr_value=implode(",",$chunk_arr);	
							$bokIds_cond.=" a.id in($chunk_arr_value) or ";	
						}
						
						$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
						//echo $booking_id_cond;die;
					}
					else
					{
						$booking_id_cond=" and a.id in($booking_id)";	 
					}
				}
				else $booking_id_cond="";
				
				if($booking_id!=="")
				{
					/*$sql="select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no,a.pay_mode, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(4) and a.booking_type=5 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond group by a.id, a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source,a.pay_mode, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date,a.is_approved, a.po_break_down_id
					union all 
					select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no,a.pay_mode, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(4) and a.booking_type=5 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and a.ready_to_approved=1 and a.is_approved in(1,0) $booking_id_cond $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond group by a.id, a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source,a.pay_mode, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date,a.is_approved, a.po_break_down_id  order by prefix_num";*/
					
					$sql="SELECT a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.supplier_id, a.requisition_date, a.delivery_date, a.is_approved, listagg(b.buyer_id, ',') within group (order by b.buyer_id) buyer_id		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b		where a.id=b.mst_id and a.company_id=$company_name and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and a.ready_to_approve=1 and a.is_approved in (3,0) $booking_id_cond $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond group by a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.requisition_date, a.delivery_date, a.supplier_id, a.is_approved 		
					union all 
					SELECT a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.supplier_id, a.requisition_date, a.delivery_date, a.is_approved, listagg(b.buyer_id, ',') within group (order by b.buyer_id) buyer_id		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
					where a.id=b.mst_id and a.company_id=$company_name  and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and a.ready_to_approve=1 and a.is_approved in (0) $buyer_id_condnon2 $buyer_id_condnon $buyerIds_cond group by a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.requisition_date, a.delivery_date, a.supplier_id, a.is_approved order by requisition_date desc";
					
				}
				else 
				{
					
					 /* $sql="select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no,a.pay_mode,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(4) and a.booking_type=5  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond  group by a.id,a.booking_no_prefix_num,a.booking_no, a.item_category,a.pay_mode, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date,a.is_approved, a.po_break_down_id  order by a.booking_no_prefix_num";*/
					  $sql="SELECT a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.supplier_id, a.requisition_date, a.delivery_date, a.is_approved, listagg(b.buyer_id, ',') within group (order by b.buyer_id) buyer_id	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
					  where a.id=b.mst_id and a.company_id=$company_name  and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and a.ready_to_approve=1 and a.is_approved=$approval_type $buyer_id_condnon2 $buyerIds_cond group by a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.requisition_date, a.delivery_date, a.supplier_id, a.is_approved order by requisition_date desc";
				}	
				// echo $sql;	
			}
			

			/*$sql="SELECT a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.supplier_id, a.requisition_date, a.delivery_date, a.is_approved, listagg(b.buyer_id, ',') within group (order by b.buyer_id) buyer_id
			from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
			where a.id=b.mst_id and a.company_id=$company_name  and a.is_deleted=0 and a.status_active=1 and a.ready_to_approve=1 and a.is_approved in (0,3) $buyer_id_cond2 group by a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.requisition_date, a.delivery_date, a.supplier_id, a.is_approved order by requisition_date desc";*/
			//echo $sql;
		}
		else // bypass No
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and b.buyer_id in($buyer_ids)";

			//$user_sequence_no=$user_sequence_no-1;


			if($db_type==0)
			{
				$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and
				sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0");
			}
			else
			{
				$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no",
				"electronic_approval_setup","company_id=$company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and
				is_deleted=0","sequence_no");
			}
			//echo $sequence_no_by_pass;die;
			if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
			else $sequence_no_cond=" and (c.sequence_no='$sequence_no' or c.sequence_no in ($sequence_no_by_pass))";

			$sql="SELECT a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.supplier_id, a.requisition_date, a.delivery_date, a.is_approved, listagg(b.buyer_id, ',') within group (order by b.buyer_id) buyer_id
			from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, approval_history c
			where a.id=b.mst_id and a.id=c.mst_id and  a.company_id=$company_name and a.item_category_id=1  and a.is_deleted=0 and a.status_active=1 and a.ready_to_approve=1 and a.is_approved in (0,3) and c.current_approval_status=1 $sequence_no_cond  $buyer_id_cond2 group by a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.requisition_date, a.delivery_date, a.is_approved, a.supplier_id  order by requisition_date desc"; //$sequence_no_cond
			//echo $user_sequence_no ."_".$min_sequence_no;die;
			//echo $sql;//die;
		}


		//and a.id not in(select mst_id from approval_history where approved_by= $user_id )
	}else{ //Approval Type = approved (List of approved requ list)

		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and b.buyer_id in($buyer_ids)";
		$sequence_no_cond=" and c.approved_by='$user_id'";
		$sql="SELECT a.id,c.id as approval_id, a.company_id, a.requ_prefix_num, a.requ_no, a.supplier_id, a.requisition_date, a.delivery_date, a.is_approved, listagg(b.buyer_id, ',') within group (order by b.buyer_id) buyer_id,a.basis
		from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, approval_history c where a.id=b.mst_id and a.id=c.mst_id and c.entry_form=20 and c.current_approval_status=1 and  a.company_id=$company_name and a.item_category_id=1  and a.is_approved  in(1,3) and a.ready_to_approve = 1 and a.status_active=1 and a.is_deleted=0 $sequence_no_cond $buyer_id_cond2  group by a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.requisition_date, a.delivery_date, a.is_approved, a.supplier_id, c.id, a.basis
		order by requisition_date desc";
		//echo $sql;
	}
	
	//echo $sql;


	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:650px; margin-top:10px">
        <legend>Yarn Requisition Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="620" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="60">SL</th>
                    <th width="150">Requisition No</th>
                    <th width="120">Supplier</th>
                    <th width="120">Requisition Date</th>
                    <th>Delivery Date</th>
                </thead>
            </table>
            <div style="width:620px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                            $i=1;
							$supplier=return_library_array("select id, supplier_name from lib_supplier ",'id','supplier_name');

							$nameArray=sql_select($sql);
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0) $bgcolor="#E9F3FF";									
								else $bgcolor="#FFFFFF";
								$value='';
								if($approval_type==0)
								{
									$value=$row[csf('id')];
								}
								else
								{
									//$app_id=return_field_value("id","approval_history","mst_id ='".$row[csf('id')]."' and entry_form='20' and current_approval_status=1 ");
									$value=$row[csf('id')]."**".$row[csf('approval_id')];
								}

								$print_report_format=0;
							    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=5 and report_id=69 and is_deleted=0 and status_active=1");
							    $printButton=explode(',',$print_report_format);
							    $first_report=$printButton[0];
							    if ($first_report==134) $buttonHtml='<a href="../commercial/work_order/requires/yarn_requisition_entry_controller.php?data='.$row[csf('company_id')].'*'.$row[csf('id')].'*Yarn Purchase Requisition*'.$row[csf('is_approved')].'&action=yarn_requisition_print" style="color:#000" target="_blank">'.$row[csf('requ_prefix_num')].'</a>';
							    else if($first_report==135) $buttonHtml='<a href="../commercial/work_order/requires/yarn_requisition_entry_controller.php?data='.$row[csf('company_id')].'*'.$row[csf('id')].'*Yarn Purchase Requisition*'.$row[csf('basis')].'&action=yarn_requisition_print_2" style="color:#000" target="_blank">'.$row[csf('requ_prefix_num')].'</a>';
							    else if($first_report==136) $buttonHtml='<a href="../commercial/work_order/requires/yarn_requisition_entry_controller.php?data='.$row[csf('company_id')].'*'.$row[csf('id')].'*Yarn Purchase Requisition*7*'.$row[csf('basis')].'&action=yarn_requisition_print_3" style="color:#000" target="_blank">'.$row[csf('requ_prefix_num')].'</a>';
							    else if($first_report==137) $buttonHtml='<a href="../commercial/work_order/requires/yarn_requisition_entry_controller.php?data='.$row[csf('company_id')].'*'.$row[csf('id')].'*Yarn Purchase Requisition*&action=yarn_requisition_print_4" style="color:#000" target="_blank">'.$row[csf('requ_prefix_num')].'</a>';
							    else if($first_report==64) $buttonHtml='<a href="../commercial/work_order/requires/yarn_requisition_entry_controller.php?data='.$row[csf('company_id')].'*'.$row[csf('id')].'*Yarn Purchase Requisition*&action=yarn_requisition_print_5" style="color:#000" target="_blank">'.$row[csf('requ_prefix_num')].'</a>';
								else if($first_report==72) $buttonHtml='<a href="../commercial/work_order/requires/yarn_requisition_entry_controller.php?data='.$row[csf('company_id')].'*'.$row[csf('id')].'*Yarn Purchase Requisition*&action=yarn_requisition_print_6" style="color:#000" target="_blank">'.$row[csf('requ_prefix_num')].'</a>';
								else if($first_report==777) $buttonHtml='<a href="../commercial/work_order/requires/yarn_requisition_entry_controller.php?data='.$row[csf('company_id')].'*'.$row[csf('id')].'*Yarn Purchase Requisition*&action=yarn_requisition_print_fso" style="color:#000" target="_blank">'.$row[csf('requ_prefix_num')].'</a>';
								else $buttonHtml='<a href="../commercial/work_order/requires/yarn_requisition_entry_controller.php?data='.$row[csf('company_id')].'*'.$row[csf('id')].'*Yarn Purchase Requisition*&action=yarn_requisition_print_7" style="color:#000" target="_blank">'.$row[csf('requ_prefix_num')].'</a>';
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="req_id_<? echo $i;?>" name="req_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="requisition_id_<? echo $i;?>" name="requisition_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" /><!--this is uesd for delete row-->
                                    </td>
									<td width="60" align="center"><? echo $i; ?></td>
									<td width="150"><? echo $buttonHtml?></td>
                                    <td width="120"><p><? echo $supplier[$row[csf('supplier_id')]]; ?></p></td>
									<td width="120" align="center"><? if($row[csf('requisition_date')]!="0000-00-00") echo change_date_format($row[csf('requisition_date')]); ?></td>
									<td align="center"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?></td>
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="620" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/>

					<input type="button" value="Deny" class="formbutton" style="width:100px;<?= ($approval_type==1)?' display:none':''; ?>" onClick="submit_approved(<?=$i; ?>,5);"/>
				
				</td>
					
				</tfoot>
			</table>
        </fieldset>
    </form>
<?
	exit();
}

if($action=='user_popup')
{
    echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
    ?>  
    <script>
      function js_set_value(id)
      { 
        document.getElementById('selected_id').value=id;
      	parent.emailwindow.hide();
      }
    </script>

    <form>
            <input type="hidden" id="selected_id" name="selected_id" /> 
           <?php
            $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');  
             $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');   ;
             $sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.SEQUENCE_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.entry_form=20 and valid=1 and a.id!=$user_id  and b.is_deleted=0 order by b.SEQUENCE_NO";
                //echo $sql;die;
             $arr=array (2=>$custom_designation,3=>$Department);
             echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Seq. No", "100,120,130,120,50,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id,0", $arr , "user_name,user_full_name,designation,department_id,SEQUENCE_NO", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
            ?>
    </form>
    <script language="javascript" type="text/javascript">
      setFilterGrid("tbl_style_ref");
    </script>
	<?
	exit();
}

if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$msg=''; $flag=''; $response='';


	$user_id_approval=$_SESSION['logic_erp']['user_id'];
	//$user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;
	

	//echo "select sequence_no from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0";
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");

	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");
	//echo $user_sequence_no;die;
	

	if($approval_type==0)
	{
		$response=$req_nos;

		$is_not_last_user=return_field_value("a.sequence_no as sequence_no","electronic_approval_setup a"," a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 ","sequence_no");
		//echo "select a.sequence_no from electronic_approval_setup a where  a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 ";die;
		//echo $is_not_last_user;die;
		$partial_approval = "";

		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, user_ip,inserted_by,insert_date";
		$id=return_next_id( "id","approval_history", 1 ) ;

		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($req_nos) and entry_form=20 group by mst_id","mst_id","approved_no");
		//print_r($max_approved_no_arr);die;
		//echo "select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($req_nos) and entry_form=20 group by mst_id"; die;

		$approved_status_arr = return_library_array("select id, is_approved from inv_purchase_requisition_mst where id in($req_nos)","id","is_approved");

		//echo "select id, is_approved from inv_purchase_requisition_mst where id in($req_nos)";die;
		$approved_no_array=array();
		$req_nos_all=explode(",",$req_nos);
		//$booking_nos_all=explode(",",$booking_nos);
		//$app_instru_all=explode(",",$appv_instras);


		for($i=0;$i<count($req_nos_all);$i++)
		{
			//$val=$booking_nos_all[$i];
			$req_id=$req_nos_all[$i];

			$approved_no=$max_approved_no_arr[$req_id];
			$approved_status=$approved_status_arr[$req_id];
			//echo $approved_no."**".$approved_status;die;
			if($approved_status==0 || $approved_status == 3)
			{

				$approved_no=$approved_no+1;
				$approved_no_array[$req_id]=$approved_no;

			}
			//echo $approved_no_array;die;
			if($is_not_last_user == "") // blank=last user
			{
				if($user_sequence_no == $min_sequence_no){ //first user is the last user
					$partial_approval=1;
				}else{
					if($is_not_last_user == "")//last user
					{
						$partial_approval=1;						
					}else{ // by pass no but not last user
						$partial_approval=3;

					}
				}
			}
			else // not last user (partial approve)
			{
				$partial_approval=3;
			}
			//echo "20_".$partial_approval;die("with pondit");
			$req_nos_arr[]=$req_id;
			$data_array_reqsi_no_update[$req_id]=explode("*",($partial_approval));
			//print_r($data_array_reqsi_no_update);die;
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",20,".$req_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."','".$user_ip."',".$user_id_approval.",'".$pc_date_time."')";
			$id=$id+1;

		}
		//print_r($data_array_reqsi_no_update);die;

		$approved_string="";

		foreach($approved_no_array as $key=>$value)
		{
			$approved_string.=" WHEN $key THEN $value";
		}

		$approved_string_mst="CASE id ".$approved_string." END";
		$approved_string_dtls="CASE mst_id ".$approved_string." END";

		$sql_insert="insert into inv_pur_requisition_mst_hist(id, hist_mst_id, approved_no, entry_form, requ_no, requ_no_prefix, requ_prefix_num, company_id, item_category_id, supplier_id, location_id, division_id, department_id, section_id, requisition_date, store_name, pay_mode, source, cbo_currency, delivery_date, do_no, attention, remarks, terms_and_condition, manual_req, ready_to_approve, is_approved, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date)
			select
			'', id, $approved_string_mst, entry_form, requ_no, requ_no_prefix, requ_prefix_num, company_id, item_category_id, supplier_id, location_id, division_id, department_id, section_id, requisition_date, store_name, pay_mode, source, cbo_currency, delivery_date, do_no, attention, remarks, terms_and_condition, manual_req, ready_to_approve, is_approved, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date from  inv_purchase_requisition_mst where id in ($req_nos)";
			//echo $sql_insert;die;





		$sql_insert_dtls="insert into  inv_pur_requisition_dtls_hist(id, approved_no, mst_id, product_id, user_code_maintain, required_for, job_id, job_no, buyer_id, style_ref_no, color_id, count_id, composition_id, com_percent, yarn_type_id, yarn_inhouse_date, cons_uom, quantity, rate, amount, stock, remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
			select
			'', $approved_string_dtls, mst_id, product_id, user_code_maintain, required_for, job_id, job_no, buyer_id, style_ref_no, color_id, count_id, composition_id, com_percent, yarn_type_id, yarn_inhouse_date, cons_uom, quantity, rate, amount, stock, remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from  inv_purchase_requisition_dtls where mst_id in ($req_nos)";

		$field_array_booking_update = "is_approved";
		//echo "10**".$field_array."__".$data_array."__===".$data_array_reqsi_no_update;die;
		$rID=execute_query(bulk_update_sql_statement( "inv_purchase_requisition_mst", "id", $field_array_booking_update, $data_array_reqsi_no_update, $req_nos_arr));
		if($rID) $flag=1; else $flag=0;
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=20 and mst_id in ($req_nos)";
		$rIDapp=execute_query($query,1);
		if($flag==1)
		{
			if($rIDapp) $flag=1; else $flag=0;
		}

		$rID2=sql_insert("approval_history",$field_array,$data_array,0);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

		$rID3=execute_query($sql_insert,0);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}

		$rID4=execute_query($sql_insert_dtls,1);

		if($flag==1)
		{
			if($rID4) $flag=1; else $flag=0;
		}

		if($flag==1) $msg='19'; else $msg='21';
	}

	else if($approval_type==5)
	{

		$rID1=sql_multirow_update("inv_purchase_requisition_mst","IS_APPROVED*READY_TO_APPROVE*APPROVED_SEQU_BY",'0*0*0',"id",$req_nos,0);
		if($rID1) $flag=1; else $flag=0;

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=20 and current_approval_status=1 and mst_id in ($req_nos)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}

				
	
		
		 // echo "191**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
		
		$response=$req_nos;
		if($flag==1) $msg='50'; else $msg='51';

	} 
	else
	{
		$req_nos = explode(',',$req_nos);

		$reqs_ids=''; $app_ids='';

		foreach($req_nos as $value)
		{
			$data = explode('**',$value);
			$reqs_id=$data[0];
			$app_id=$data[1];

			if($reqs_ids=='') $reqs_ids=$reqs_id; else $reqs_ids.=",".$reqs_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
		}
		$rID=sql_multirow_update("inv_purchase_requisition_mst","is_approved*ready_to_approve","0*0","id",$reqs_ids,0);
		if($rID) $flag=1; else $flag=0;

		$data=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//echo "22**".$app_ids;die;

		$rID2=sql_multirow_update("approval_history","un_approved_by*un_approved_date*current_approval_status*updated_by*update_date",$data,"id",$app_ids,0);
		//echo $rID."_".$rID2;die;
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

		$response=$reqs_ids;

		if($flag==1) $msg='20'; else $msg='22';
	}

	if($db_type==0)
	{
		if($flag==1)
		{
			mysql_query("COMMIT");
			echo $msg."**".$response;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo $msg."**".$response;
		}
	}

	if($db_type==2 || $db_type==1 )
	{
		if($flag==1)
		{
			oci_commit($con);
			echo $msg."**".$response;
		}
		else
		{
			oci_rollback($con);
			echo $msg."**".$response;
		}
	}
	disconnect($con);
	die;

}
?>

