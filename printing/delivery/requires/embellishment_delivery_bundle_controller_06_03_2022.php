<?
include('../../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id = $_SESSION['logic_erp']["user_id"];


$trans_Type="2";

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
$size_arr=return_library_array( "SELECT id,size_name from  lib_size where status_active =1 and is_deleted=0",'id','size_name');

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "SELECT id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );	
	exit();	 
}

if ($action=="load_drop_down_buyer_pop")
{
	//echo $data; die;
	$data=explode('_',$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 120, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 120, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
		exit();
	}
} 

if ($action=="load_drop_down_table")
{
	$data=explode('_', $data);
	$company = $data[2];
	$location = $data[1];
	$floor = $data[0];
    // and company_name=$company  and location_name=$location
	echo create_drop_down( 'cbo_table', 120, "select id, table_name from lib_table_entry where status_active=1 and is_deleted=0  and floor_name=$floor and table_type=1 order by table_name", "id,table_name", 1, "-- Select Table --", $selected, '', 0 );
	exit();
}


if ($action=="load_drop_down_buyer")
{
	//echo $data; die;
	$data=explode('_',$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
		exit();
	}
} 

if($action=="load_drop_down_company_supplier")
{
	$data = explode("**",$data);
	if($data[0]==3)
	{
		//echo create_drop_down( "cbo_company_supplier", 140, "select id, supplier_name from lib_supplier where find_in_set(2,party_type) and find_in_set($data[1],tag_company) and status_active=1 and is_deleted=0","id,supplier_name", 1, "--Select Supplier--", 1, "" );
		echo create_drop_down( "cbo_company_supplier", 150, "SELECT a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=2 and a.status_active=1 and a.is_deleted=0 order by a.supplier_name","id,supplier_name", 1, "--Select Supplier--", $selected, "" );
	}
	else if($data[0]==1)
	{
		if($data[1]!="")
		{
			 echo create_drop_down( "cbo_company_supplier", 150,"SELECT id,company_name from lib_company where is_deleted=0 and status_active=1 order by company_name","id,company_name", 1, "--Select Supplier--", $data[1], "",1 );	
		}
		else
		{
			 echo create_drop_down( "cbo_company_supplier", 150, $blank_array,"", 1, "--Select Company--", $selected, "",0 );
		}
	}
	else
	{
		echo create_drop_down( "cbo_company_supplier", 150, $blank_array,"", 1, "--Select Supplier--", $selected, "",0 );
	}
	exit();	
}

if ($action=="load_drop_down_buyer_pop")
{
	//echo $data; die;
	$data=explode('_',$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 120, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 120, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
	}
	exit();
} 

if($action=="load_drop_down_emb_type")
{
	$data=explode('_',$data);
	
	if($data[0]==1) $emb_type=$emblishment_print_type;
	else if($data[0]==2) $emb_type=$emblishment_embroy_type;
	else if($data[0]==3) $emb_type=$emblishment_wash_type;
	else if($data[0]==4) $emb_type=$emblishment_spwork_type;
	else if($data[0]==5) $emb_type=$emblishment_gmts_type;
	
	echo create_drop_down( "cboReType_".$data[1], 80,$emb_type,"", 1, "-- Select --", "", "","","" ); 
	exit();
}

if ($action == "challan_duplicate_check")
{
    $data=explode("__",$data);
    /*$sql_bundle_no="SELECT c.bundle_no_prefix_num,c.bundle_no,c.barcode_no,b.sys_no from subcon_ord_mst a, sub_material_mst b, prnting_bundle_dtls c, printing_bundle_issue_dtls d where a.embellishment_job=b.embl_job_no and b.id=c.item_rcv_id and c.barcode_no='$data[0]' and  d.entry_form=499 and  d.bundle_dtls_id=c.id and d.rcv_id=b.id and d.wo_id=a.id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0  and c.status_active =1 and c.is_deleted =0  and d.status_active =1 and d.is_deleted =0 and c.bundle_no is not null";

	$bdlNoArray =sql_select($sql_bundle_no); 
	foreach ($bdlNoArray as $row) 
	{
		echo "Bundle No " . $row[csf('bundle_no')] . " Found in Challan No " . $row[csf('sys_no')] . ".";
	}*/
	$sql_next_process="SELECT e.issue_number from prnting_bundle_dtls c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e where e.id=d.mst_id and c.barcode_no='$data[0]' and d.entry_form=499 and  d.bundle_dtls_id=c.id and e.status_active =1 and e.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0";
	$next_process_data =sql_select($sql_next_process); 

	foreach ($next_process_data as $row) 
	{
		echo "Already Delivery found, System id " . $row[csf('issue_number')] . ".";
	}
	exit();
}
if($action=="create_receive_search_list_view")
{
	$data=explode('_',$data);
	$search_type =$data[6];
	$within_group =$data[7];
	$location =$data[10];
	$search_by=str_replace("'","",$data[8]);
	$search_str=trim(str_replace("'","",$data[9]));

	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[4]=='' && $data[5]=='' && $search_str=='' && $data[2]=="" &&  $data[3]==""){
		echo "Please Select Date Range"; die; 
	} 
	if ($data[1]!=0) $buyer_cond=" and a.party_id='$data[1]'"; else $buyer_cond="";
	if ($location!=0) $location_cond=" and a.location_id='$location'"; else $location_cond="";
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $recieve_date = "and a.subcon_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $recieve_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $recieve_date = "and a.subcon_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $recieve_date ="";
	}
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4){
				$po_cond=" and b.po_number = '$search_str' ";
				$buyer_po_cond=" and b.buyer_po_no = '$search_str' ";
			} 
			else if ($search_by==5){
				$style_cond=" and a.style_ref_no = '$search_str' ";
				$buyer_style_cond=" and b.buyer_style_ref = '$search_str' ";
			}
		}
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num='$data[4]'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no='$data[5]'"; else $challan_no_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4){
				$po_cond=" and b.po_number like '%$search_str%'"; 
				$buyer_po_cond=" and b.buyer_po_no like '%$search_str%'"; 
			} 
			else if ($search_by==5){
				$style_cond=" and a.style_ref_no like '%$search_str%'";
				$buyer_style_cond=" and b.buyer_style_ref like '%$search_str%'";
			}   
		}
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num like '%$data[4]%'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '%$data[5]%'"; else $challan_no_cond="";
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4){
				$po_cond=" and b.po_number like '$search_str%'";
				$buyer_po_cond=" and b.buyer_po_no like '$search_str%'";
			} 
			else if ($search_by==5){
				$style_cond=" and a.style_ref_no like '$search_str%'";
				$buyer_style_cond=" and b.buyer_style_ref like '$search_str%'";
			}   
		}
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num like '$data[4]%'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '$data[5]%'"; else $challan_no_cond="";
		if ($data[9]!='') $order_no_cond=" and order_no like '$data[9]%'"; else $order_no_cond="";
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4){
				$po_cond=" and b.po_number like '%$search_str'";
				$buyer_po_cond=" and b.buyer_po_no like '%$search_str'";
			} 
			else if ($search_by==5){
				$style_cond=" and a.style_ref_no like '%$search_str'";
				$buyer_style_cond=" and b.buyer_style_ref like '%$search_str'";
			}   
		}
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num like '%$data[4]'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '%$data[5]'"; else $challan_no_cond="";
	}	
	
	//$party_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	//$comp=return_library_array("SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	//$po_arr=return_library_array( "SELECT id,order_no from subcon_ord_dtls where status_active =1 and is_deleted=0",'id','order_no');
	$size_arr=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');
	$color_arr=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	
	$po_ids=''; $buyer_po_arr=array();
	if($within_group==1)
	{
		if($db_type==0) $id_cond="group_concat(b.id)";
		else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
		//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
		{
			$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
		}
		//echo $po_ids;
		if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
		
		$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		}
		unset($po_sql_res);
	}
	$spo_ids='';
	
	if($db_type==0)
	{
		$id_cond="group_concat(b.id)";
		$insert_date_cond="year(a.insert_date)";
		$wo_cond="group_concat(distinct(b.job_dtls_id))";
		$buyer_po_id_cond="group_concat(distinct(b.buyer_po_id))";
	}
	else if($db_type==2)
	{
		$id_cond="listagg(b.id,',') within group (order by b.id)";
		$insert_date_cond="TO_CHAR(a.insert_date,'YYYY')";
		$wo_cond="listagg(b.job_dtls_id,',') within group (order by b.job_dtls_id)";
		$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
	}
	if((($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2)) || ($within_group==2 && (($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))))
	{
		//echo "select $id_cond as id from subcon_ord_mst a, subcon_ord_dtls b where a.embellishment_job=b.job_no_mst $search_com_cond $buyer_po_cond $buyer_style_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.embellishment_job=b.job_no_mst $search_com_cond $buyer_po_cond $buyer_style_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}
	
	if ( $spo_ids!="") $spo_idsCond=" and b.job_dtls_id in ($spo_ids)"; else $spo_idsCond="";
	//echo $sql; 
	$sql_production="SELECT id as production_dtls_id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id
			from printing_bundle_issue_dtls where  entry_form=497 and status_active=1 and is_deleted=0 order by id ASC";
	
	$production_result =sql_select($sql_production);
	foreach($production_result as $row)
	{
	 	$production_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['quantity']+=$row[csf('quantity')];
	 	$production_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['production_dtls_id'].=$row[csf('production_dtls_id')].',';
	}

	$sql_qc="SELECT id as qc_dtls_id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id
			from printing_bundle_issue_dtls where  entry_form=498 $spo_idsCondProd and status_active=1 and is_deleted=0 order by id ASC";
	
	$qc_result =sql_select($sql_qc); $issue_dtls_id_arr=array();
	foreach($qc_result as $row)
	{
	 	$qc_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['quantity']+=$row[csf('quantity')];
	 	$qc_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['qc_dtls_id'].=$row[csf('qc_dtls_id')].',';
	 	$issue_dtls_id_arr[]=$row[csf('issue_dtls_id')];
	}
	//echo "10**"; print_r($issue_dtls_id_arr); die;
	$con = connect(); $r_id2=true; $r_id3=true;
	foreach($issue_dtls_id_arr as $val)
	{
		//echo $val; die;
		$issue_dtls_id=$val;
		$r_id2=execute_query("insert into tmp_poid (userid, poid) values ($user_id,$issue_dtls_id)");
	}

	$sql_issue="SELECT id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id
			from printing_bundle_issue_dtls where entry_form=499 and status_active=1 and is_deleted=0 order by id ASC";
	$issue_result =sql_select($sql_issue);
	$bundle_dtls_id=""; $wo_id='';
	foreach($issue_result as $row)
	{
		$bundle_dtls_id=$row[csf('bundle_dtls_id')];
		if($bundle_dtls_id!=0)
		{
			$r_id3=execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form) values ($user_id,$bundle_dtls_id,499)");
		}
	}
	//print_r($issue_item_arr);
	//$wo_ids=implode(",",array_unique(explode(",",(chop($wo_id,',')))));
	if($db_type==0)
	{
		if($r_id2==1 && $r_id3==1)
		{
			mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		//echo $r_id2==1 && $r_id3==1; die;
		if($r_id2==1 && $r_id3==1)
		{
			oci_commit($con);  
		}
	}


	$sql_job="SELECT a.within_group,a.company_id , a.id, a.embellishment_job, a.order_id, a.order_no, b.id as po_id, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty, b.order_uom,b.gmts_item_id,b.buyer_buyer,b.buyer_style_ref,b.main_process_id,b.embl_type,b.body_part
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id  and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   and c.qnty>0 order by c.id ASC";
	
	$dataArray =sql_select($sql_job);
	foreach ($dataArray as $row) 
	{
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['style']=$row[csf('buyer_style_ref')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['buyer_buyer']=$row[csf('buyer_buyer')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['order_no']=$row[csf('order_no')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['main_process_id']=$row[csf('main_process_id')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['embl_type']=$row[csf('embl_type')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['body_part']=$row[csf('body_part')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['color_id']=$row[csf('color_id')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['size_id']=$row[csf('size_id')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['wo_id']=$row[csf('id')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['within_group']=$row[csf('within_group')];
	}

	$sql="SELECT c.id as bundl_mst_id, c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.no_of_bundle, c.remarks,c.bundle_num_prefix_no,d.id as bundle_dtls_id, d.mst_id, d.item_rcv_id, d.bundle_qty, d.barcode_no, d.barcode_id, d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id as rcv_dtls_id, a.embl_job_no,a.party_id,a.location_id,a.id as rcv_id,a.subcon_date,a.sys_no, $insert_date_cond as year, f.issue_date,e.id as issue_dtls_id,e.challan_no
	from sub_material_mst a , sub_material_dtls b ,prnting_bundle_mst c, prnting_bundle_dtls d , printing_bundle_issue_dtls e , printing_bundle_issue_mst f ,tmp_poid g where  g.poid=e.id and f.entry_form=495 and c.id = d.mst_id and c.item_rcv_dtls_id = b.id and d.item_rcv_id = b.mst_id and d.item_rcv_id = a.id and a.id = b.mst_id and a.id=e.rcv_id and b.id=e.rcv_dtls_id and c.id=e.bundle_mst_id and d.id=e.bundle_dtls_id and e.mst_id=f.id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond $spo_idsCond $po_idsCond $location_cond and d.id not in (select barcode_no from tmp_barcode_no where userid=$user_id and entry_form=499) 
	group by c.id , c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.no_of_bundle, c.remarks,c.bundle_num_prefix_no,d.id , d.mst_id, d.item_rcv_id, d.bundle_qty, d.barcode_no, d.barcode_id , d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id, a.embl_job_no,a.party_id,a.location_id,a.id ,a.subcon_date,a.sys_no, a.insert_date, f.issue_date,e.id,e.challan_no order by b.id";
	$result = sql_select($sql);
	?>
    <div>
    	<table cellpadding="0" cellspacing="2" border="1" width="1140" id="details_tbl" rules="all">
            <thead class="form_table_header">
                <tr align="center" >
                    <th width="30" ></th>
                    <th width="40" >SL</th>
                    <th width="120" >Barcode No</th>
                    <th width="100" >Comapany</th>
                    <th width="100" >Location</th>
                    <th width="60" >Within Group</th>
                    <th width="100" >Customer</th>
                    <th width="100" >Cus. Buyer</th>
                    <th width="60" >Issue Date</th>
                    <th width="60">Issue Ch No</th>
                    <th width="60">Order No</th>
                    <th width="60">Cust. Style Ref.</th>
                    <th width="60">Embl. Name</th>
                    <th width="60">Embl. Type</th>
                    <th width="80">Body Part</th>
                    <th width="100">Color</th>
                    <th width="60">Size</th>
                    <th width="80">Bundle NO</th>
                    <th width="60">Bundle Qty.</th>
                    <th width="60">Prod. Qty.</th>
                    <th width="60">QC Qty.</th>
                    <th >RMK</th>
                </tr>
            </thead>
            <div style="width:820px; max-height:270px; overflow-y:scroll;" >
	         	<tbody id="rec_issue_table">
	         	<?
					$i=1;
		            foreach($result as $row)
		            {
		                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$style=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['style'];
						$buyer_buyer=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['buyer_buyer'];
						$order_no=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['order_no'];
						$main_process_id=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['main_process_id'];
						$embl_type_id=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['embl_type'];
						$body_part_id=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['body_part'];
						$color_id=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['color_id'];
						$size_id=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['size_id'];
						$wo_id=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['wo_id'];
						$within_group=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['within_group'];
						
						$prod_qty=$production_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('issue_dtls_id')]]['quantity'];
						$production_dtls_ids=$production_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('issue_dtls_id')]]['production_dtls_id'];
						$qc_qty=$qc_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('issue_dtls_id')]]['quantity'];
						$qc_dtls_ids=$qc_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('issue_dtls_id')]]['qc_dtls_id'];

						$productionDtlsIds=implode(",",array_unique(explode(",",(chop($production_dtls_ids,',')))));
						$qcDtlsIds=implode(",",array_unique(explode(",",(chop($qc_dtls_ids,',')))));
						$checkBox_check="checked";
						?>
		                <tr>
		                	<td>
                                <input type="checkbox" name="barcodeChkbox[]" id="barcodeChkbox_<? echo $i; ?>" value="" <? echo $checkBox_check; ?> />
                            </td>
		                    <input type="hidden" name="woID[]" id="woID_<? echo $i; ?>" value="<? echo $wo_id; ?>"  >
		                    <input type="hidden" name="woDtlsID[]" id="woDtlsID_<? echo $i; ?>" value="<? echo $row[csf("job_dtls_id")]; ?>"  >
		                    <input type="hidden" name="woBreakID[]" id="woBreakID_<? echo $i; ?>" value="<? echo $row[csf("job_break_id")]; ?>"  >
		                    <input type="hidden" name="rcvID[]" id="rcvID_<? echo $i; ?>" value="<? echo $row[csf("rcv_id")]; ?>"  >
		                    <input type="hidden" name="rcvDtlsID[]" id="rcvDtlsID_<? echo $i; ?>" value="<? echo $row[csf("rcv_dtls_id")]; ?>"  >
		                    <input type="hidden" name="bundleMstID[]" id="bundleMstID_<? echo $i; ?>" value="<? echo $row[csf("bundl_mst_id")]; ?>"  >
		                    <input type="hidden" name="bundleDtlsID[]" id="bundleDtlsID_<? echo $i; ?>" value="<? echo $row[csf("bundle_dtls_id")]; ?>"  >
		                    <input type="hidden" name="issueDtlsID[]" id="issueDtlsID_<? echo $i; ?>" value="<? echo $row[csf("issue_dtls_id")]; ?>"  >
		                    <input type="hidden" name="productionDtlsIds[]" id="productionDtlsIds_<? echo $i; ?>" value="<? echo $productionDtlsIds; ?>"  >
		                    <input type="hidden" name="qcDtlsIds[]" id="qcDtlsIds<? echo $i; ?>" value="<? echo $qcDtlsIds; ?>"  >
		                    <input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value=""  >
	                        
		                    <td width="40" align="center"><? echo $i; ?></td>
		                        <!--onDblClick="job_search_popup('requires/embellishment_delivery_bundle_controller.php?action=job_popup','Order Selection Form')" -->
		                    
		                    <td><input name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $row[csf("barcode_no")]; ?>" style="width:107px" readonly /></td>
		                    <td><? 
								echo create_drop_down( "cboCompanyId_".$i, 100, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",$row[csf("company_id")], "",1,'','','','','','',"cboCompanyId[]"); ?></td>
							
							<td><? 
							echo create_drop_down( "cboLocationId_".$i, 100, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --",$row[csf("location_id")], "",1,'','','','','','',"cboLocationId[]"); ?></td>
							<td><? echo create_drop_down( "cboWithinGroup_".$i, 100, $yes_no,"", 1, "-- Select --",2, "",1,'','','','','','',"cboWithinGroup[]"); ?></td>
							<td><? echo create_drop_down( "cboPartyId_".$i, 100, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Customer --",$row[csf("party_id")], "",1,'','','','','','',"cboPartyId[]"); ?></td>
		                    <td><input name="txtCustBuyer[]" id="txtCustBuyer_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $buyer_buyer; ?>"  style="width:87px" readonly /></td>
		                    <td><input name="txtIssueDate[]" id="txtIssueDate_<? echo $i; ?>" type="text" class="datepicker" value="<? echo change_date_format($row[csf("issue_date")]); ?>"  style="width:87px" disabled /></td> 
		                    <td><input name="txtIssueCh[]" id="txtIssueCh_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $row[csf("challan_no")]; ?>"  style="width:47px"  readonly  /></td>
		                    <td><input name="txtOrder_<? echo $i; ?>" id="txtOrder_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $order_no; ?>"  style="width:47px" readonly /></td>
		                    <td><input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $style; ?>"  style="width:47px" readonly /></td>
		                     <td><? echo create_drop_down( "cboProcessName_<? echo $i; ?>", 80, $emblishment_name_array,"", 1, "--Select--",$main_process_id, "",1,'','','','','','',"cboEmbType[]"); ?></td>
							<td><?
								if($main_process_id==1) $emb_type=$emblishment_print_type;
								else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
								else if($main_process_id==3) $emb_type=$emblishment_wash_type;
								else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
								else if($main_process_id==5) $emb_type=$emblishment_gmts_type; 
								echo create_drop_down( "cboEmbType_".$i, 60, $emb_type,"", 1, "-- Select --",$embl_type_id, "",1,'','','','','','',"cboEmbType[]"); ?></td>
							<td><? echo create_drop_down( "cboBodyPart_".$i, 80, $body_part,"", 1, "-- Select --",$body_part_id, "",1,'','','','','','',"cboBodyPart[]"); ?></td>
							<td><input type="text" id="txtcolor_<? echo $i; ?>" name="txtcolor_<? echo $i; ?>" class="text_boxes" value="<? echo $color_arr[$color_id]; ?>"  style="width:87px" readonly/></td>
		                    <td><input type="text" id="txtsize_<? echo $i; ?>" name="txtsize_<? echo $i; ?>" class="text_boxes txt_size" value="<? echo $size_arr[$size_id]; ?>"  style="width:47px" readonly/></td>
		                    <td><input name="txtBundleNo[]" id="txtBundleNo_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $row[csf("bundle_no")]; ?>"  style="width:67px" readonly /></td>
		                    <td><input name="txtBundleQty[]" id="txtBundleQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $row[csf("bundle_qty")]; ?>"  style="width:47px" readonly /></td>
		                    <td><input name="txtProdQty[]" id="txtProdQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $prod_qty; ?> " style="width:47px" readonly /></td> 
		                    <td style="display: none;"><input name="txtRejQty[]" id="txtRejQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<??>"  placeholder="Display"   readonly style="width:47px"  />
		                     	</td>
		                    <td><input name="txtQcQty[]" id="txtQcQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $qc_qty; ?>" placeholder="Display" style="width:47px" readonly /></td>
		                    <td><input name="txtRemark[]" id="txtRemark_<? echo $i; ?>" type="text" class="text_boxes" value="" placeholder="Write" /></td>
		                </tr>
	                <?
	                $i++;
	                $totBndlQty+=$row[csf("bundle_qty")];
	                $totProdQty+=$prod_qty;
	                $totQcQty+=$qc_qty;
	                }
	                ?>
	            </tbody>
	        </div>
            <tfoot>
            	<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
                    <td colspan="3" align="left"><input form="form_all" type="checkbox" name="check_all" id="check_all" value=""  onclick="fnCheckUnCheckAll(this.checked)"/> Check / Uncheck All</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total:</td>
                    <td><input name="txtTotBundleqty" id="txtTotBundleqty" class="text_boxes_numeric" type="text" value="<? echo $totBndlQty; ?>"  style="width:47px" readonly /></td>
                    <td><input name="txtTotProdqty" id="txtTotProdqty" class="text_boxes_numeric" type="text" value="<? echo $totProdQty; ?>" style="width:47px" readonly /></td>
                    <td style="display: none;"><input name="txtTotRejqty" id="txtTotRejqty" class="text_boxes_numeric" type="text" style="width:47px"  readonly /></td>
                    <td><input name="txtTotQcqty" id="txtTotQcqty" class="text_boxes_numeric" type="text" value="<? echo $totQcQty; ?>" style="width:47px"  readonly /></td>
                    <td>&nbsp;</td>
                    
                </tr>
            </tfoot>
        </table>
    </div>
    <script type="text/javascript">fnc_total_calculate();</script>
    <? 
    $r_id4=true; $r_id5=true;
    $r_id4=execute_query("delete from tmp_poid where userid=$user_id");
    $r_id5=execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=499");
	if($db_type==0)
	{
		if($r_id4==1 && $r_id5==1)
		{
			mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($r_id4==1 && $r_id5==1)
		{
			oci_commit($con);  
		}
	}
	
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$barcode_dup_chk_arr=array();
	// Insert Start Here ----------------------------------------------------------
	if ($operation==0)   
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=return_next_id("id","printing_bundle_issue_mst",1) ;
		$id1=return_next_id("id","printing_bundle_issue_dtls",1) ;
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		$new_iss_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'PMDB', date("Y",time()), 5, "select issue_num_prefix,issue_num_prefix_no from printing_bundle_issue_mst where entry_form=499 and company_id=$cbo_company_name $insert_date_con order by id desc ", "issue_num_prefix", "issue_num_prefix_no" ));
		
		$field_array="id,company_id,entry_form , issue_num_prefix, issue_num_prefix_no, issue_number ,issue_date, remarks, delivery_point, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",".$cbo_company_name.",499,'".$new_iss_no[1]."','".$new_iss_no[2]."','".$new_iss_no[0]."',".$txt_delivery_date.",".$txt_remarks.",".$txt_delivery_point.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		$issue_number=$new_iss_no[0];

		$field_array1="id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,issue_dtls_id,challan_no,quantity,production_dtls_id, reject_qty, defect_qty, remarks, inserted_by, insert_date, status_active, is_deleted";
		
		$all_wo_id='';
		for($j=1; $j<=$total_row; $j++)
		{
			$woID			= "woID_".$j; 
			$all_wo_id.=str_replace("'", '', $$woID).',';
		}
		$all_wo_ids=implode(",",array_unique(explode(",",chop($all_wo_id,","))));
	   	$sql_prev_issue="SELECT a.issue_number,b.issue_dtls_id from printing_bundle_issue_mst a, printing_bundle_issue_dtls b where a.id=b.mst_id and a.entry_form=499 and b.entry_form=499 and b.wo_id in ($all_wo_ids) and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0"; 
		$prev_issue_res =sql_select($sql_prev_issue); 
		foreach ($prev_issue_res as $row) 
		{
			$prev_issue_arr[$row[csf('issue_dtls_id')]]=$row[csf('issue_number')];
		}

		$data_array1="";  $add_commaa=0;
		for($i=1; $i<=$total_row; $i++)
		{
			$cboCompanyId	= "cboCompanyId_".$i; 
			$woID			= "woID_".$i; 
			$woDtlsID		= "woDtlsID_".$i;
			$woBreakID		= "woBreakID_".$i;
			$rcvID			= "rcvID_".$i;
			$rcvDtlsID		= "rcvDtlsID_".$i;
			$bundleMstID	= "bundleMstID_".$i;
			$bundleDtlsID	= "bundleDtlsID_".$i;
			$txtIssueDate	= "txtIssueDate_".$i;
			$txtIssueCh		= "txtIssueCh_".$i;
			//$txtProdQty	= "txtProdQty_".$i;
			$issueDtlsID	= "issueDtlsID_".$i;
			$updatedtlsid	= "updatedtlsid_".$i;
			$productionDtlsIds	= "productionDtlsIds_".$i;
			$txtRejQty		= "txtRejQty_".$i;
			$hdnDtlsdata	= "hdnDtlsdata_".$i;
			$txtQcQty		= "txtQcQty_".$i;
			$txtRemark		= "txtRemark_".$i;
			if(!in_array(str_replace("'", '', $$bundleDtlsID), $barcode_dup_chk_arr, true)){
        		array_push( $barcode_dup_chk_arr, str_replace("'", '', $$bundleDtlsID));
    		}else{
    			echo "121**Duplicate Barcode No. Found"; die;
    		}
    		$issue_no=$prev_issue_arr[str_replace("'", '', $$issueDtlsID)];
    		if($issue_no){
    			echo "121**Delivery Found. System ID : $issue_no "; die;
    		}

			if ($add_commaa!=0) $data_array1 .=",";
			$data_array1.="(".$id1.",".$id.",".$$cboCompanyId.",499,".$$woID.",".$$woDtlsID.",".$$woBreakID.",".$$rcvID.",".$$rcvDtlsID.",".$$bundleMstID.",".$$bundleDtlsID.",".$$issueDtlsID.",".$$txtIssueCh.",".$$txtQcQty.",".$$productionDtlsIds.",".$$txtRejQty.",".$$hdnDtlsdata.",".$$txtRemark.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			 
			$id1=$id1+1; $add_commaa++;
		}
		$flag=1;
		//echo "INSERT INTO printing_bundle_issue_dtls (".$field_array1.") VALUES ".$data_array1; die;
		$rID=sql_insert("printing_bundle_issue_mst",$field_array,$data_array,0);
		if($flag==1 && $rID==1) $flag=1; else $flag=0;

		$rID1=sql_insert("printing_bundle_issue_dtls",$field_array1,$data_array1,0);
		if($flag==1 && $rID1==1 && $rID==1) $flag=1; else $flag=0;
		
		//echo "10**".$rID."**".$rID1	; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$issue_number)."**".str_replace("'",'',$id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$issue_number)."**".str_replace("'",'',$id);
			}
		}
		else if($db_type==2)
		{
			if($flag==1) 
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$issue_number)."**".str_replace("'",'',$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$issue_number)."**".str_replace("'",'',$id);
			}
		}
		disconnect($con);
		die;		
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		/*$prod_number=return_field_value( "sys_no", "subcon_embel_production_dtls"," job_no=$txt_job_no and status_active=1 and is_deleted=0");
		if($prod_number){
			echo "emblProduction**".str_replace("'","",$txt_job_no)."**".$prod_number;
			disconnect($con); die;
		}*/
		
		
		$id1=return_next_id("id","printing_bundle_issue_dtls",1) ;
		$field_array="issue_date*remarks*delivery_point*updated_by*update_date";
		$data_array="".$txt_delivery_date."*".$txt_remarks."*".$txt_delivery_point."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$field_array_status="updated_by*update_date*status_active*is_deleted";
		$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$field_arr_up="challan_no*quantity*reject_qty*defect_qty*remarks*updated_by*update_date*status_active*is_deleted";
		$field_array1="id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,issue_dtls_id,challan_no,quantity,production_dtls_id, reject_qty, defect_qty, remarks, inserted_by, insert_date, status_active, is_deleted";
		
		$all_wo_id='';
		for($j=1; $j<=$total_row; $j++)
		{
			$woID			= "woID_".$j; 
			$all_wo_id.=str_replace("'", '', $$woID).',';
		}
		$all_wo_ids=implode(",",array_unique(explode(",",chop($all_wo_id,","))));
	   	$sql_prev_issue="SELECT a.issue_number,b.issue_dtls_id from printing_bundle_issue_mst a, printing_bundle_issue_dtls b where a.id=b.mst_id and a.entry_form=499 and b.entry_form=499 and b.wo_id in ($all_wo_ids) and a.id!=$update_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0"; 
		$prev_issue_res =sql_select($sql_prev_issue); 
		foreach ($prev_issue_res as $row) 
		{
			$prev_issue_arr[$row[csf('issue_dtls_id')]]=$row[csf('issue_number')];
		}

		$sql_next_trans="SELECT a.bill_no from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.entry_form=395 and a.delivery_id=$update_id and a.is_bundle=1 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0"; 
		$sql_next_trans_res =sql_select($sql_next_trans); 
		if(count($sql_next_trans_res)>0)
		{
			echo "121**Bill Found. System ID : $sql_next_trans_res[0][csf('bill_no')] "; disconnect($con); die;
		}
		

		$data_array1="";  $add_commaa=0;
		for($i=1; $i<=$total_row; $i++)
		{
			$cboCompanyId	= "cboCompanyId_".$i; 
			$woID			= "woID_".$i; 
			$woDtlsID		= "woDtlsID_".$i;
			$woBreakID		= "woBreakID_".$i;
			$rcvID			= "rcvID_".$i;
			$rcvDtlsID		= "rcvDtlsID_".$i;
			$bundleMstID	= "bundleMstID_".$i;
			$bundleDtlsID	= "bundleDtlsID_".$i;
			$txtIssueDate	= "txtIssueDate_".$i;
			$txtIssueCh		= "txtIssueCh_".$i;
			//$txtProdQty		= "txtProdQty_".$i;
			$issueDtlsID	= "issueDtlsID_".$i;
			$updatedtlsid	= "updatedtlsid_".$i;
			$productionDtlsIds	= "productionDtlsIds_".$i;
			$txtRejQty		= "txtRejQty_".$i;
			$hdnDtlsdata	= "hdnDtlsdata_".$i;
			$txtQcQty		= "txtQcQty_".$i;
			$txtRemark		= "txtRemark_".$i;
			if(!in_array(str_replace("'", '', $$bundleDtlsID), $barcode_dup_chk_arr, true)){
        		array_push( $barcode_dup_chk_arr, str_replace("'", '', $$bundleDtlsID));
    		}else{
    			echo "121**Duplicate Barcode No. Found"; die;
    		}
    		$issue_no=$prev_issue_arr[str_replace("'", '', $$issueDtlsID)];
    		if($issue_no){
    			echo "121**Delivery Found. System ID : $issue_no "; die;
    		}
			
			if(str_replace("'","",$$updatedtlsid)=="")
			{
				if ($add_commaa!=0) $data_array1 .=",";
				$data_array1.="(".$id1.",".$update_id.",".$$cboCompanyId.",499,".$$woID.",".$$woDtlsID.",".$$woBreakID.",".$$rcvID.",".$$rcvDtlsID.",".$$bundleMstID.",".$$bundleDtlsID.",".$$issueDtlsID.",".$$txtIssueCh.",".$$txtQcQty.",".$$productionDtlsIds.",".$$txtRejQty.",".$$hdnDtlsdata.",".$$txtRemark.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			 
				$id1=$id1+1; $add_commaa++;
			}
			else if(str_replace("'","",$$updatedtlsid)!="")
			{
				$data_arr_up[str_replace("'","",$$updatedtlsid)]=explode("*",("".$$txtIssueCh."*".$$txtQcQty."*".$$txtRejQty."*".$$hdnDtlsdata."*".$$txtRemark."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0"));
				$id_arr[]=str_replace("'","",$$updatedtlsid);
				//$hdn_break_id_arr[]=str_replace("'","",$$updatedtlsid);
			}
		}
		
		$flag=1;
		$rID=sql_update("printing_bundle_issue_mst",$field_array,$data_array,"id",$update_id,0); 
		if($rID==1 && $flag==1) $flag=1; else $flag=0;	

		if($flag==1){
			$rID1=sql_multirow_update("printing_bundle_issue_dtls",$field_array_status,$data_array_status,"mst_id",$update_id,0);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($data_array1!="" && $flag==1)
		{
			//echo "10**INSERT INTO printing_bundle_issue_dtls (".$field_array1.") VALUES ".$data_array1; die;
			$rID2=sql_insert("printing_bundle_issue_dtls",$field_array1,$data_array1,1);
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		}
			
		if($data_arr_up!=""  && $flag==1)
		{
			//echo "10**".bulk_update_sql_statement( "printing_bundle_issue_dtls", "id", $field_arr_up,$data_arr_up,$id_arr_iss);
			$rID3=execute_query(bulk_update_sql_statement( "printing_bundle_issue_dtls", "id", $field_arr_up,$data_arr_up,$id_arr),1);
			if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//echo "10**".$rID."**".$rID1."**".$rID2."**".$rID3."**".$flag; die;
		 
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$txt_delivery_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_delivery_no)."**".str_replace("'",'',$update_id);
			}
		}
		else if($db_type==2)
		{
			if($flag==1) 
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_delivery_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_delivery_no)."**".str_replace("'",'',$update_id);
			}
		}
		disconnect($con); die;
	}
	else if ($operation==2)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$sql_next_trans="SELECT a.bill_no from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.entry_form=395 and a.delivery_id=$update_id and a.is_bundle=1 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0"; 
		$sql_next_trans_res =sql_select($sql_next_trans); 
		if(count($sql_next_trans_res)>0)
		{
			echo "121**Bill Found. System ID : $sql_next_trans_res[0][csf('bill_no')] "; disconnect($con); die;
		}
		
		$field_array_status="updated_by*update_date*status_active*is_deleted";
		$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$data_array1="";  $add_commaa=0; $updatedtlsDelId=''; $updatedtlsDelIds=''; $flag=1;
		for($i=1; $i<=$total_row; $i++)
		{
			$updatedtlsid	= "updatedtlsid_".$i;
			if(str_replace("'","",$$updatedtlsid)!=""){
				$updatedtlsDelId.=str_replace("'","",$$updatedtlsid).',';
			}
		}

		$updatedtlsDelIds=implode(",",array_unique(explode(",",(chop($updatedtlsDelId,',')))));
		//echo "10**".$updatedtlsDelIds; die;
		$rID = sql_multirow_update("printing_bundle_issue_dtls", $field_array_status, $data_array_status, "id", $updatedtlsDelIds, 1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;	
		//echo "10**".$rID."**".$rID1."**".$rID2."**".$rID3."**".$flag; die;
		 
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'",'',$txt_delivery_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_delivery_no)."**".str_replace("'",'',$update_id);
			}
		}
		else if($db_type==2)
		{
			if($flag==1) 
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_delivery_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_delivery_no)."**".str_replace("'",'',$update_id);
			}
		}
		disconnect($con); die;
	}
}

if( $action == 'issue_item_details_update' )
{
	//$data=explode("**",$data);
	$mst_id=$data;
	$sql_production="SELECT id as production_dtls_id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id
			from printing_bundle_issue_dtls where  entry_form=497 and status_active=1 and is_deleted=0 order by id ASC";
	
	$production_result =sql_select($sql_production);
	foreach($production_result as $row)
	{
	 	$production_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['quantity']+=$row[csf('quantity')];
	 	$production_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['production_dtls_id'].=$row[csf('production_dtls_id')].',';
	}

	$sql_qc="SELECT id as qc_dtls_id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id
			from printing_bundle_issue_dtls where  entry_form=498 $spo_idsCondProd and status_active=1 and is_deleted=0 order by id ASC";
	
	$qc_result =sql_select($sql_qc);
	foreach($qc_result as $row)
	{
	 	$qc_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['quantity']+=$row[csf('quantity')];
	 	$qc_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['qc_dtls_id'].=$row[csf('qc_dtls_id')].',';
	}

	$sql_delivery="SELECT id as del_dtls_id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id
			from printing_bundle_issue_dtls where  entry_form=499 and mst_id=$mst_id and status_active=1 and is_deleted=0 order by id ASC";
	
	$del_result =sql_select($sql_delivery); $issue_dtls_id_arr=array();
	foreach($del_result as $row_data)
	{
		$del_item_arr[$row_data[csf('wo_dtls_id')]][$row_data[csf('wo_break_id')]][$row_data[csf('issue_dtls_id')]]['updatedtlsid']=$row_data[csf('del_dtls_id')];
	 	$issue_dtls_id_arr[]=$row_data[csf('issue_dtls_id')];
	 	$wo_id.=$row_data[csf('wo_id')].',';
	}
	$issue_dtls_id_arr=array_unique($issue_dtls_id_arr);
	//echo "10**"; print_r($issue_dtls_id_arr); die;
	$con = connect();
	foreach($issue_dtls_id_arr as $val)
	{
		//echo $val; die;
		$issue_dtls_id=$val;
		$r_id2=execute_query("insert into tmp_poid (userid, poid) values ($user_id,$issue_dtls_id)");
	}
	//print_r($issue_item_arr);
	$wo_ids=implode(",",array_unique(explode(",",(chop($wo_id,',')))));
	if($db_type==0)
	{
		if($r_id2)
		{
			mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		//echo $r_id2; die;
		if($r_id2)
		{
			oci_commit($con);  
		}
	}

	//die;

	$sql_job="SELECT a.within_group,a.company_id , a.id, a.embellishment_job, a.order_id, a.order_no, b.id as po_id, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty, b.order_uom,b.gmts_item_id,b.buyer_buyer,b.buyer_style_ref,b.main_process_id,b.embl_type,b.body_part
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.id in ($wo_ids) and a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id  and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   and c.qnty>0 order by c.id ASC";
	
	$dataArray =sql_select($sql_job);
	foreach ($dataArray as $row) 
	{
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['style']=$row[csf('buyer_style_ref')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['buyer_buyer']=$row[csf('buyer_buyer')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['order_no']=$row[csf('order_no')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['main_process_id']=$row[csf('main_process_id')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['embl_type']=$row[csf('embl_type')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['body_part']=$row[csf('body_part')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['color_id']=$row[csf('color_id')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['size_id']=$row[csf('size_id')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['wo_id']=$row[csf('id')];
	}

	/*$issue_sql="SELECT  e.id as issue_dtls_id , f.issue_date 
	from printing_bundle_issue_dtls e , printing_bundle_issue_mst f , tmp_poid g where g.poid=e.id and e.mst_id=f.id and e.entry_form=495 and e.status_active=1 and e.is_deleted=0";
	$issue_result =sql_select($issue_sql);
	foreach($issue_result as $row)
	{
		$issue_arr[$row[csf('issue_dtls_id')]]['issue_date']=change_date_format($row[csf('issue_date')]);
	}*/

	$sql="SELECT c.id as bundl_mst_id, c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.no_of_bundle, c.remarks,c.bundle_num_prefix_no,d.id as bundle_dtls_id, d.mst_id, d.item_rcv_id, d.bundle_qty, d.barcode_no, d.barcode_id, d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id as rcv_dtls_id, a.embl_job_no,a.party_id,a.location_id,a.id as rcv_id,a.subcon_date,a.sys_no, f.issue_date,e.id as issue_dtls_id,e.challan_no
	from sub_material_mst a , sub_material_dtls b ,prnting_bundle_mst c, prnting_bundle_dtls d , printing_bundle_issue_dtls e , printing_bundle_issue_mst f ,tmp_poid g where  g.poid=e.id and f.entry_form=495 and c.id = d.mst_id and c.item_rcv_dtls_id = b.id and d.item_rcv_id = b.mst_id and d.item_rcv_id = a.id and a.id = b.mst_id and a.id=e.rcv_id and b.id=e.rcv_dtls_id and c.id=e.bundle_mst_id and d.id=e.bundle_dtls_id and e.mst_id=f.id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond $spo_idsCond $po_idsCond $location_cond
	group by c.id , c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.no_of_bundle, c.remarks,c.bundle_num_prefix_no,d.id , d.mst_id, d.item_rcv_id, d.bundle_qty, d.barcode_no, d.barcode_id , d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id, a.embl_job_no,a.party_id,a.location_id,a.id ,a.subcon_date,a.sys_no, f.issue_date,e.id,e.challan_no order by b.id";
	
	$result = sql_select($sql);
	if(count($result)>0){
	?>
	    <div>
	    	<table cellpadding="0" cellspacing="2" border="1" width="1140" id="details_tbl" rules="all">
	            <thead class="form_table_header">
	                <tr align="center" >
	                    <th width="30" ></th>
	                    <th width="40" >SL</th>
	                    <th width="120" >Barcode No</th>
	                    <th width="100" >Comapany</th>
	                    <th width="100" >Location</th>
	                    <th width="60" >Within Group</th>
	                    <th width="100" >Customer</th>
	                    <th width="100" >Cus. Buyer</th>
	                    <th width="60" >Issue Date</th>
	                    <th width="60">Issue Ch No</th>
	                    <th width="60">Order No</th>
	                    <th width="60">Cust. Style Ref.</th>
	                    <th width="60">Embl. Name</th>
	                    <th width="60">Embl. Type</th>
	                    <th width="80">Body Part</th>
	                    <th width="100">Color</th>
	                    <th width="60">Size</th>
	                    <th width="80">Bundle NO</th>
	                    <th width="60">Bundle Qty.</th>
	                    <th width="60">Prod. Qty.</th>
	                    <th width="60">QC Qty.</th>
	                    <th >RMK</th>
	                </tr>
	            </thead>
	            <div style="width:820px; max-height:270px; overflow-y:scroll;" >
		         	<tbody id="rec_issue_table">
		         	<?
						$i=1;
			            foreach($result as $row)
			            {
			                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$style=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['style'];
							$buyer_buyer=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['buyer_buyer'];
							$order_no=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['order_no'];
							$main_process_id=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['main_process_id'];
							$embl_type_id=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['embl_type'];
							$body_part_id=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['body_part'];
							$color_id=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['color_id'];
							$size_id=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['size_id'];
							$wo_id=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['wo_id'];
							$within_group=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['within_group'];
							
							$prod_qty=$production_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('issue_dtls_id')]]['quantity'];
							$production_dtls_ids=$production_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('issue_dtls_id')]]['production_dtls_id'];
							$qc_qty=$qc_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('issue_dtls_id')]]['quantity'];
							$qc_dtls_ids=$qc_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('issue_dtls_id')]]['qc_dtls_id'];
							$updatedtlsid=$del_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('issue_dtls_id')]]['updatedtlsid'];

							$productionDtlsIds=implode(",",array_unique(explode(",",(chop($production_dtls_ids,',')))));
							$qcDtlsIds=implode(",",array_unique(explode(",",(chop($qc_dtls_ids,',')))));
							$checkBox_check ="checked";
							?>
			                <tr>
			                	<td>
	                                <input type="checkbox" name="barcodeChkbox[]" id="barcodeChkbox_<? echo $i; ?>" value="" <? echo $checkBox_check; ?> />
	                            </td>
			                    <input type="hidden" name="woID[]" id="woID_<? echo $i; ?>" value="<? echo $wo_id; ?>"  >
			                    <input type="hidden" name="woDtlsID[]" id="woDtlsID_<? echo $i; ?>" value="<? echo $row[csf("job_dtls_id")]; ?>"  >
			                    <input type="hidden" name="woBreakID[]" id="woBreakID_<? echo $i; ?>" value="<? echo $row[csf("job_break_id")]; ?>"  >
			                    <input type="hidden" name="rcvID[]" id="rcvID_<? echo $i; ?>" value="<? echo $row[csf("rcv_id")]; ?>"  >
			                    <input type="hidden" name="rcvDtlsID[]" id="rcvDtlsID_<? echo $i; ?>" value="<? echo $row[csf("rcv_dtls_id")]; ?>"  >
			                    <input type="hidden" name="bundleMstID[]" id="bundleMstID_<? echo $i; ?>" value="<? echo $row[csf("bundl_mst_id")]; ?>"  >
			                    <input type="hidden" name="bundleDtlsID[]" id="bundleDtlsID_<? echo $i; ?>" value="<? echo $row[csf("bundle_dtls_id")]; ?>"  >
			                    <input type="hidden" name="issueDtlsID[]" id="issueDtlsID_<? echo $i; ?>" value="<? echo $row[csf("issue_dtls_id")]; ?>"  >
			                    <input type="hidden" name="productionDtlsIds[]" id="productionDtlsIds_<? echo $i; ?>" value="<? echo $productionDtlsIds; ?>"  >
			                    <input type="hidden" name="qcDtlsIds[]" id="qcDtlsIds<? echo $i; ?>" value="<? echo $qcDtlsIds; ?>"  >
			                    <input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value="<? echo $updatedtlsid; ?>"  >
		                        
			                    <td width="40" align="center"><? echo $i; ?></td>
			                        <!--onDblClick="job_search_popup('requires/embellishment_delivery_bundle_controller.php?action=job_popup','Order Selection Form')" -->
			                    
			                    <td><input name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $row[csf("barcode_no")]; ?>" style="width:107px" readonly /></td>
			                    <td><? 
									echo create_drop_down( "cboCompanyId_".$i, 100, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",$row[csf("company_id")], "",1,'','','','','','',"cboCompanyId[]"); ?></td>
								
								<td><? 
								echo create_drop_down( "cboLocationId_".$i, 100, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --",$row[csf("location_id")], "",1,'','','','','','',"cboLocationId[]"); ?></td>
								<td><? echo create_drop_down( "cboWithinGroup_".$i, 100, $yes_no,"", 1, "-- Select --",2, "",1,'','','','','','',"cboWithinGroup[]"); ?></td>
								<td><? echo create_drop_down( "cboPartyId_".$i, 100, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Customer --",$row[csf("party_id")], "",1,'','','','','','',"cboPartyId[]"); ?></td>
			                    <td><input name="txtCustBuyer[]" id="txtCustBuyer_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $buyer_buyer; ?>"  style="width:87px" readonly /></td>
			                    <td><input name="txtIssueDate[]" id="txtIssueDate_<? echo $i; ?>" type="text" class="datepicker" value="<? echo change_date_format($row[csf("issue_date")]); ?>"  style="width:87px" disabled /></td> 
			                    <td><input name="txtIssueCh[]" id="txtIssueCh_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $row[csf("challan_no")]; ?>"  style="width:47px"  readonly  /></td>
			                    <td><input name="txtOrder_<? echo $i; ?>" id="txtOrder_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $order_no; ?>"  style="width:47px" readonly /></td>
			                    <td><input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $style; ?>"  style="width:47px" readonly /></td>
			                     <td><? echo create_drop_down( "cboProcessName_<? echo $i; ?>", 80, $emblishment_name_array,"", 1, "--Select--",$main_process_id, "",1,'','','','','','',"cboEmbType[]"); ?></td>
								<td><?
									if($main_process_id==1) $emb_type=$emblishment_print_type;
									else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
									else if($main_process_id==3) $emb_type=$emblishment_wash_type;
									else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
									else if($main_process_id==5) $emb_type=$emblishment_gmts_type; 
									echo create_drop_down( "cboEmbType_".$i, 60, $emb_type,"", 1, "-- Select --",$embl_type_id, "",1,'','','','','','',"cboEmbType[]"); ?></td>
								<td><? echo create_drop_down( "cboBodyPart_".$i, 80, $body_part,"", 1, "-- Select --",$body_part_id, "",1,'','','','','','',"cboBodyPart[]"); ?></td>
								<td><input type="text" id="txtcolor_<? echo $i; ?>" name="txtcolor_<? echo $i; ?>" class="text_boxes" value="<? echo $color_arr[$color_id]; ?>"  style="width:87px" readonly/></td>
			                    <td><input type="text" id="txtsize_<? echo $i; ?>" name="txtsize_<? echo $i; ?>" class="text_boxes txt_size" value="<? echo $size_arr[$size_id]; ?>"  style="width:47px" readonly/></td>
			                    <td><input name="txtBundleNo[]" id="txtBundleNo_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $row[csf("bundle_no")]; ?>"  style="width:67px" readonly /></td>
			                    <td><input name="txtBundleQty[]" id="txtBundleQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $row[csf("bundle_qty")]; ?>"  style="width:47px" readonly /></td>
			                    <td><input name="txtProdQty[]" id="txtProdQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $prod_qty; ?> " style="width:47px" readonly /></td> 
			                    <td style="display: none;"><input name="txtRejQty[]" id="txtRejQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<??>"  placeholder="Display"   readonly style="width:47px"  />
			                     	</td>
			                    <td><input name="txtQcQty[]" id="txtQcQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $qc_qty; ?>" placeholder="Display" style="width:47px" readonly /></td>
			                    <td><input name="txtRemark[]" id="txtRemark_<? echo $i; ?>" type="text" class="text_boxes" value="" placeholder="Write" /></td>
			                </tr>
		                <?
		                $i++;
		                $totBndlQty+=$row[csf("bundle_qty")];
		                $totProdQty+=$prod_qty;
		                $totQcQty+=$qc_qty;
		                }
		                ?>
		            </tbody>
		        </div>
	            <tfoot>
	            	<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
	                    <td colspan="3" align="left"><input form="form_all" type="checkbox" name="check_all" id="check_all" value=""  onclick="fnCheckUnCheckAll(this.checked)"/> Check / Uncheck All</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>Total:</td>
	                    <td><input name="txtTotBundleqty" id="txtTotBundleqty" class="text_boxes_numeric" type="text" value="<? echo $totBndlQty; ?>"  style="width:47px" readonly /></td>
	                    <td><input name="txtTotProdqty" id="txtTotProdqty" class="text_boxes_numeric" type="text" value="<? echo $totProdQty; ?>" style="width:47px" readonly /></td>
	                    <td style="display: none;"><input name="txtTotRejqty" id="txtTotRejqty" class="text_boxes_numeric" type="text" style="width:47px"  readonly /></td>
	                    <td><input name="txtTotQcqty" id="txtTotQcqty" class="text_boxes_numeric" type="text" value="<? echo $totQcQty; ?>" style="width:47px"  readonly /></td>
	                    <td>&nbsp;</td>
	                    
	                </tr>
	            </tfoot>
	        </table>
	    </div>
    <?
    }else{

	}
    //die;
	$r_id3=execute_query("delete from tmp_poid where userid=$user_id");
	if($db_type==0)
	{
		if($r_id3)
		{
			mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($r_id3)
		{
			oci_commit($con);  
		}
	}
	
	exit();
}

if ($action=="production_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	$company=$data[0];
	$location=$data[1];
	$party_name=$data[2];
	$within_group=$data[3];
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
				
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
                <table width="400" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                    	<tr>                	 
                            <th width="140" class="must_entry_caption" >Company Name</th>
                            <th width="100">Delivery No.</th>
                            <th width="100" colspan="2">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_job">  <!--  echo $data;-->
							<? 
								echo create_drop_down( "cbo_company_name", 140, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, ""); ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From">
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_issue_search_list_view', 'popup_search_div', 'embellishment_delivery_bundle_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="4" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="top" id=""><div id="popup_search_div"></div></td>
                        </tr>
                    </tbody>
                </table>    
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if($action=="create_issue_search_list_view")
{
	$data=explode('_',$data);
	$search_type =$data[6];
	$search_str=trim(str_replace("'","",$data[1]));

	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $issue_date = "and a.issue_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $issue_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $issue_date = "and a.issue_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $issue_date ="";
	}
	if($search_str !='') $search_com_cond="and a.issue_number like '%$search_str%'";  
	
	$comp=return_library_array("SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	
	
	$sql= "SELECT a.id, a.company_id, a.issue_number, a.issue_date, a.floor_id, a.table_id  from printing_bundle_issue_mst a, printing_bundle_issue_dtls b where a.id=b.mst_id and a.entry_form=499 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_com_cond $issue_date $company group by a.id, a.company_id, a.issue_number, a.issue_date, a.floor_id, a.table_id order by a.id DESC ";
	//echo $sql; 
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="420" class="rpt_table">
            <thead>
                <th width="50" >SL</th>
                <th width="150" >Delivery No</th>
                <th >Delivery Date</th>
                
            </thead>
     	</table>
	    <div style="width:420px; max-height:270px;overflow-y:scroll;" >	 
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table" id="tbl_po_list">
				<?
				$i=1;
	            foreach( $result as $row )
	            {
	                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")];?>');" > 
							<td width="50" align="center"><? echo $i; ?></td>
							<td width="150" align="center"><? echo $row[csf("issue_number")]; ?></td>
	                        <td  align="center"><? echo $row[csf("issue_date")]; ?></td>
						</tr>
					<? 
					$i++;
	            }
	   			?>
				</table>
			</div>
	    </div>
    <?	
	exit();
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "SELECT a.id, a.company_id, a.issue_number, a.issue_date, a.floor_id, a.table_id, a.qc_name,a.shift_id,a.remarks,a.delivery_point from printing_bundle_issue_mst a where a.id='$data' and a.status_active =1 and a.is_deleted=0 and a.entry_form=499" ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_delivery_no').value 		= '".$row[csf("issue_number")]."';\n";
		echo "document.getElementById('txt_delivery_date').value 	= '".change_date_format($row[csf("issue_date")])."';\n";
		
	    echo "document.getElementById('txt_remarks').value          = '".$row[csf("remarks")]."';\n";
	    echo "document.getElementById('txt_delivery_point').value          = '".$row[csf("delivery_point")]."';\n";
	    echo "document.getElementById('update_id').value            = '".$row[csf("id")]."';\n";
	}
	exit();
}

if ($action=="receive_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	$company=$data[0];
	$location=$data[1];
	$party_name=$data[2];
	$within_group=$data[3];
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_order_popup(company,party_name,within_group)
		{   //alert(company+'_'+party_name+'_'+within_group);	
			load_drop_down( 'embellishment_delivery_bundle_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_buyer_pop', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Embl. Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer Po');
			else if(val==5) $('#search_by_td').html('Buyer Style');
		}		
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $company;?>,<? echo $party_name;?>,<? echo $within_group;?>)">
        <div align="center" style="width:100%;" >
            <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
                <table width="870" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="10"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>                	 
                            <th width="140" class="must_entry_caption" >Company Name</th>
                            <th width="50">Within Group</th>
                            <th width="120">Party Name</th>
                            <th width="70">Receive ID</th>
                            <th width="80">Challan No</th>
                            <th width="100">Search By</th>
                    		<th width="100" id="search_by_td">Embl. Job No</th>
                            <th width="100" colspan="2">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_job">  <!--  echo $data;-->
							<? 
								echo create_drop_down( "cbo_company_name", 140, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'embellishment_delivery_bundle_controller', this.value+'_'+".$within_group.", 'load_drop_down_buyer_pop', 'buyer_td' );"); ?>
                            </td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 0, "--  --",2, "load_drop_down( 'embellishment_delivery_bundle_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer_pop', 'buyer_td' );" ); ?>
							</td>
                            <td id="buyer_td">
								<? 
								echo create_drop_down( "cbo_party_name", 120, $blank_array,"", 1, "-- Select Party --", $selected, "" );?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:60px" placeholder="Receive ID" />
                            </td>
                            <td>
                                <input type="text" name="txt_search_challan" id="txt_search_challan" class="text_boxes" style="width:70px" placeholder="Challan" />
                            </td>
                            <td>
								<?
                                    $search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                                    echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From">
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_challan').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'create_receive_search_list_view_browse', 'search_div', 'embellishment_delivery_bundle_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <div id="search_div"></div>   
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_receive_search_list_view_browse")
{
	$data=explode('_',$data);
	$search_type =$data[6];
	$within_group =$data[7];
	$search_by=str_replace("'","",$data[8]);
	$search_str=trim(str_replace("'","",$data[9]));

	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[4]=='' && $data[5]=='' && $search_str=='' && $data[2]=="" &&  $data[3]==""){
		echo "Please Select Date Range"; die; 
	} 
	if ($data[1]!=0) $buyer_cond=" and a.party_id='$data[1]'"; else $buyer_cond="";
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $recieve_date = "and a.subcon_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $recieve_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $recieve_date = "and a.subcon_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $recieve_date ="";
	}
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4){
				$po_cond=" and b.po_number = '$search_str' ";
				$buyer_po_cond=" and b.buyer_po_no = '$search_str' ";
			} 
			else if ($search_by==5){
				$style_cond=" and a.style_ref_no = '$search_str' ";
				$buyer_style_cond=" and b.buyer_style_ref = '$search_str' ";
			} 
		}
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num='$data[4]'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no='$data[5]'"; else $challan_no_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4){
				$po_cond=" and b.po_number like '%$search_str%'"; 
				$buyer_po_cond=" and b.buyer_po_no like '%$search_str%'"; 
			} 
			else if ($search_by==5){
				$style_cond=" and a.style_ref_no like '%$search_str%'";
				$buyer_style_cond=" and b.buyer_style_ref like '%$search_str%'";
			}   
		}
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num like '%$data[4]%'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '%$data[5]%'"; else $challan_no_cond="";
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4){
				$po_cond=" and b.po_number like '$search_str%'";
				$buyer_po_cond=" and b.buyer_po_no like '$search_str%'";
			} 
			else if ($search_by==5){
				$style_cond=" and a.style_ref_no like '$search_str%'";
				$buyer_style_cond=" and b.buyer_style_ref like '$search_str%'";
			}  
		}
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num like '$data[4]%'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '$data[5]%'"; else $challan_no_cond="";
		if ($data[9]!='') $order_no_cond=" and order_no like '$data[9]%'"; else $order_no_cond="";
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4){
				$po_cond=" and b.po_number like '%$search_str'";
				$buyer_po_cond=" and b.buyer_po_no like '%$search_str'";
			} 
			else if ($search_by==5){
				$style_cond=" and a.style_ref_no like '%$search_str'";
				$buyer_style_cond=" and b.buyer_style_ref like '%$search_str'";
			} 
		}
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num like '%$data[4]'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '%$data[5]'"; else $challan_no_cond="";
	}	
	
	$party_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	$comp=return_library_array("SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	$po_arr=return_library_array( "SELECT id,order_no from subcon_ord_dtls where status_active =1 and is_deleted=0",'id','order_no');
	
	$po_ids=''; $buyer_po_arr=array();
	if($within_group==1)
	{
		if($db_type==0) $id_cond="group_concat(b.id)";
		else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
		//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
		{
			$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
		}
		//echo $po_ids;
		if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
		
		$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		}
		unset($po_sql_res);
	}
	$spo_ids='';
	
	if($db_type==0)
	{
		$id_cond="group_concat(b.id)";
		$insert_date_cond="year(a.insert_date)";
		$wo_cond="group_concat(distinct(b.job_dtls_id))";
		$buyer_po_id_cond="group_concat(distinct(b.buyer_po_id))";
	}
	else if($db_type==2)
	{
		$id_cond="listagg(b.id,',') within group (order by b.id)";
		$insert_date_cond="TO_CHAR(a.insert_date,'YYYY')";
		$wo_cond="listagg(b.job_dtls_id,',') within group (order by b.job_dtls_id)";
		$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
	}
	if((($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2)) || ($within_group==2 && (($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))))
	{
		//echo "select $id_cond as id from subcon_ord_mst a, subcon_ord_dtls b where a.embellishment_job=b.job_no_mst $search_com_cond $buyer_po_cond $buyer_style_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.embellishment_job=b.job_no_mst $search_com_cond $buyer_po_cond $buyer_style_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}

	$sql_issue="SELECT id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id
			from printing_bundle_issue_dtls where entry_form=499 and status_active=1 and is_deleted=0 order by id ASC";
	$issue_result =sql_select($sql_issue);
	$bundle_dtls_id=""; $wo_id='';
	foreach($issue_result as $row)
	{
		$bundle_dtls_id=$row[csf('bundle_dtls_id')];
		if($bundle_dtls_id!=0)
		{
			$r_id3=execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form) values ($user_id,$bundle_dtls_id,499)");
		}
	}
	//print_r($issue_item_arr);
	//$wo_ids=implode(",",array_unique(explode(",",(chop($wo_id,',')))));
	if($db_type==0)
	{
		if($r_id3==1)
		{
			mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		//echo $r_id2; die;
		if($r_id3==1)
		{
			oci_commit($con);  
		}
	}
	
	if ( $spo_ids!="") $spo_idsCond=" and b.job_dtls_id in ($spo_ids)"; else $spo_idsCond="";
	

	$sql="SELECT c.id as bundl_mst_id, c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.pcs_per_bundle, c.no_of_bundle, c.bundle_qty, c.remarks,c.bundle_num_prefix_no,d.id as bundle_dtls_id, d.mst_id, d.item_rcv_id, d.pcs_per_bundle, d.bundle_qty, d.barcode_no, d.barcode_id, d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id as rcv_dtls_id, a.embl_job_no,a.party_id,a.location_id,a.id as rcv_id,a.subcon_date,a.sys_no, e.id as production_dtls_id , f.issue_date ,e.issue_dtls_id , $insert_date_cond as year,g.order_no
	from sub_material_mst a , sub_material_dtls b ,prnting_bundle_mst c, prnting_bundle_dtls d , printing_bundle_issue_dtls e , printing_bundle_issue_mst f , subcon_ord_mst g where c.id = d.mst_id and c.item_rcv_dtls_id = b.id and d.item_rcv_id = b.mst_id and d.item_rcv_id = a.id and a.id = b.mst_id and a.id=e.rcv_id and b.id=rcv_dtls_id and c.id=bundle_mst_id and d.id=e.bundle_dtls_id and e.mst_id=f.id and  g.embellishment_job=a.embl_job_no and e.wo_id=g.id and g.entry_form=204 and f.entry_form=498 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond $spo_idsCond $po_idsCond $location_cond and d.id not in (select barcode_no from tmp_barcode_no where userid=$user_id and entry_form=499) 
	group by c.id , c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.pcs_per_bundle, c.no_of_bundle, c.bundle_qty, c.remarks,c.bundle_num_prefix_no,d.id , d.mst_id, d.item_rcv_id, d.pcs_per_bundle, d.bundle_qty, d.barcode_no, d.barcode_id , d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id, a.embl_job_no,a.party_id,a.location_id,a.id ,a.subcon_date,a.sys_no, a.insert_date, e.id, f.issue_date,e.id,g.order_no,e.issue_dtls_id  order by b.id";


	//echo $sql; 
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
            <thead>
                <th width="40" >SL</th>
                <th width="100" >Emb. Job No.</th>
                <th width="100" >WO No.</th>
                <th width="100" >Receive No</th>
                <th width="50" >Year</th>
                <th width="100" >Party Name</th>
                <th width="60" >Receive Date</th>
                <th >Barcode</th>
            </thead>
     	</table>
     <div style="width:820px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach($result as $row)
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$party_name="";
				if($row[csf("within_group")]==1) $party_name=$comp[$row[csf("party_id")]]; else $party_name=$party_arr[$row[csf("party_id")]];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("rcv_id")]."_".$row[csf("bundle_dtls_id")]."_".$row[csf("issue_dtls_id")];?>');" > 
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="100" align="center"><? echo $row[csf("embl_job_no")]; ?></td>
						<td width="100" align="center"><? echo $row[csf("order_no")]; ?></td>
						<td width="100" align="center"><? echo $row[csf("sys_no")]; ?></td>
                        <td width="50" align="center"><? echo $row[csf("year")]; ?></td>
                        <td width="100"><? echo $party_name; ?></td>		
						<td width="60"><? echo change_date_format($row[csf("subcon_date")]);  ?></td>
                        <td align="center" style="word-break:break-all"><p><? echo $row[csf("barcode_no")]; ?></p></td>	
					</tr>
				<? 
				$i++;
            }
   			?>
			</table>
		</div>
    </div>
    <?	
    $r_id5=execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=499");
	if($db_type==0)
	{
		if($r_id5==1)
		{
			mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($r_id5==1)
		{
			oci_commit($con);  
		}
	}
	exit();
}


if( $action == 'create_new_table' )
{
	//$data=explode("**",$data);
	//$mst_id=$data;
	//echo $mst_id ; die;
	
	?>
    <div>
    	<table cellpadding="0" cellspacing="2" border="1" width="1140" id="details_tbl" rules="all">
            <thead class="form_table_header">
                <tr align="center" >
                    <th width="30" ></th>
                    <th width="40" >SL</th>
                    <th width="120" >Barcode No</th>
                    <th width="100" >Comapany</th>
                    <th width="100" >Location</th>
                    <th width="60" >Within Group</th>
                    <th width="100" >Customer</th>
                    <th width="100" >Cus. Buyer</th>
                    <th width="60" >Issue Date</th>
                    <th width="60">Issue Ch No</th>
                    <th width="60">Order No</th>
                    <th width="60">Cust. Style Ref.</th>
                    <th width="60">Embl. Name</th>
                    <th width="60">Embl. Type</th>
                    <th width="80">Body Part</th>
                    <th width="100">Color</th>
                    <th width="60">Size</th>
                    <th width="80">Bundle NO</th>
                    <th width="60">Bundle Qty.</th>
                    <th width="60">Prod. Qty.</th>
                    <th width="60">QC Qty.</th>
                    <th >RMK</th>
                </tr>
            </thead>
            <div style="width:820px; max-height:270px; overflow-y:scroll;" >
	         	<tbody id="rec_issue_table">
	         	
	            </tbody>
	        </div>
            <tfoot>
            	<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
                    <td colspan="3" align="left"><input form="form_all" type="checkbox" name="check_all" id="check_all" value=""  onclick="fnCheckUnCheckAll(this.checked)"/> Check / Uncheck All</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total:</td>
                    <td><input name="txtTotBundleqty" id="txtTotBundleqty" class="text_boxes_numeric" type="text" value=""  style="width:47px" readonly /></td>
                    <td><input name="txtTotProdqty" id="txtTotProdqty" class="text_boxes_numeric" type="text" value="" style="width:47px" readonly /></td>
                    <td style="display: none;"><input name="txtTotRejqty" id="txtTotRejqty" class="text_boxes_numeric" type="text" style="width:47px"  readonly /></td>
                    <td><input name="txtTotQcqty" id="txtTotQcqty" class="text_boxes_numeric" type="text" value="" style="width:47px"  readonly /></td>
                    <td>&nbsp;</td>
                    
                </tr>
            </tfoot>
        </table>
    </div>
    <?
	exit();
}
if($action=="append_new_item")
{
	//echo $data;
	$exdata=explode("**",$data);
	$type=$exdata[2];
	if($type==2){
	    $bundle = explode(",", $exdata[0]);
	    $total_row =$exdata[3];
	    $bundle_nos = "'" . implode("','", $bundle) . "'";
		$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond=""; $scnbundle_nos_cond=""; $cutbundle_nos_cond="";
		if($db_type==2 && $bundle_count>400)
		{
			$cutbundle_nos_cond=" and (";
			$bundleArr=array_chunk(explode(",",$bundle_nos),399);
			foreach($bundleArr as $bundleNos)
			{
				$bundleNos=implode(",",$bundleNos);
				$cutbundle_nos_cond.=" d.barcode_no in($bundleNos) or ";
			}
			$cutbundle_nos_cond=chop($cutbundle_nos_cond,'or ');
			$cutbundle_nos_cond.=")";
		}
		else
		{
			$cutbundle_nos_cond=" and d.barcode_no in ($bundle_nos)";
		}
	}else{
		$rcv_id =$exdata[0];
		$bundle_dtls_id =$exdata[1];
		$total_row =$exdata[3];
		$issue_dtls_id =$exdata[4];
		$cutbundle_nos_cond="and a.id=$rcv_id and d.id=$bundle_dtls_id and e.id=$issue_dtls_id ";
	}
	$size_arr=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');
	$color_arr=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	if($db_type==0)
	{
		$insert_date_cond="year(a.insert_date)";
	}
	else if($db_type==2)
	{
		$insert_date_cond="TO_CHAR(a.insert_date,'YYYY')";
	}

	$sql_job="SELECT a.within_group,a.company_id , a.id, a.embellishment_job, a.order_id, a.order_no, b.id as po_id, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty, b.order_uom,b.gmts_item_id,b.buyer_buyer,b.buyer_style_ref,b.main_process_id,b.embl_type,b.body_part
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id  and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   and c.qnty>0 order by c.id ASC";
	
	$dataArray =sql_select($sql_job);
	foreach ($dataArray as $row) 
	{
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['style']=$row[csf('buyer_style_ref')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['buyer_buyer']=$row[csf('buyer_buyer')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['order_no']=$row[csf('order_no')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['main_process_id']=$row[csf('main_process_id')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['embl_type']=$row[csf('embl_type')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['body_part']=$row[csf('body_part')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['color_id']=$row[csf('color_id')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['size_id']=$row[csf('size_id')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['wo_id']=$row[csf('id')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['within_group']=$row[csf('within_group')];
	}

	$sql_production="SELECT id as production_dtls_id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id
			from printing_bundle_issue_dtls where  entry_form=497 and status_active=1 and is_deleted=0 order by id ASC";
	
	$production_result =sql_select($sql_production);
	foreach($production_result as $row)
	{
	 	$production_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['quantity']+=$row[csf('quantity')];
	 	$production_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['production_dtls_id'].=$row[csf('production_dtls_id')].',';
	}

	$sql_qc="SELECT id as qc_dtls_id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id
			from printing_bundle_issue_dtls where  entry_form=498 $spo_idsCondProd and status_active=1 and is_deleted=0 order by id ASC";
	
	$qc_result =sql_select($sql_qc); $issue_dtls_id_arr=array();
	foreach($qc_result as $row)
	{
	 	$qc_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['quantity']+=$row[csf('quantity')];
	 	$qc_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['qc_dtls_id'].=$row[csf('qc_dtls_id')].',';
	 	$issue_dtls_id_arr[]=$row[csf('issue_dtls_id')];
	}
	//echo "10**"; print_r($issue_dtls_id_arr); die;
	$con = connect();
	foreach($issue_dtls_id_arr as $val)
	{
		//echo $val; die;
		$issue_dtls_id=$val;
		$r_id2=execute_query("insert into tmp_poid (userid, poid) values ($user_id,$issue_dtls_id)");
	}
	//print_r($issue_item_arr);
	//$wo_ids=implode(",",array_unique(explode(",",(chop($wo_id,',')))));
	if($db_type==0)
	{
		if($r_id2)
		{
			mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		//echo $r_id2; die;
		if($r_id2)
		{
			oci_commit($con);  
		}
	}

	$sql="SELECT c.id as bundl_mst_id, c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.no_of_bundle, c.remarks,c.bundle_num_prefix_no,d.id as bundle_dtls_id, d.mst_id, d.item_rcv_id, d.bundle_qty, d.barcode_no, d.barcode_id, d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id as rcv_dtls_id, a.embl_job_no,a.party_id,a.location_id,a.id as rcv_id,a.subcon_date,a.sys_no, $insert_date_cond as year, f.issue_date,e.id as issue_dtls_id,e.challan_no
	from sub_material_mst a , sub_material_dtls b ,prnting_bundle_mst c, prnting_bundle_dtls d , printing_bundle_issue_dtls e , printing_bundle_issue_mst f ,tmp_poid g where  g.poid=e.id and f.entry_form=495 and c.id = d.mst_id and c.item_rcv_dtls_id = b.id and d.item_rcv_id = b.mst_id and d.item_rcv_id = a.id and a.id = b.mst_id and a.id=e.rcv_id and b.id=e.rcv_dtls_id and c.id=e.bundle_mst_id and d.id=e.bundle_dtls_id and e.mst_id=f.id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond $spo_idsCond $po_idsCond $location_cond $cutbundle_nos_cond
	group by c.id , c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.no_of_bundle, c.remarks,c.bundle_num_prefix_no,d.id , d.mst_id, d.item_rcv_id, d.bundle_qty, d.barcode_no, d.barcode_id , d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id, a.embl_job_no,a.party_id,a.location_id,a.id ,a.subcon_date,a.sys_no, a.insert_date, f.issue_date,e.id,e.challan_no order by b.id";

	$result = sql_select($sql);
	
	$i=$total_row+1;
    foreach($result as $row)
    {
        if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$style=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['style'];
		$buyer_buyer=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['buyer_buyer'];
		$order_no=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['order_no'];
		$main_process_id=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['main_process_id'];
		$embl_type_id=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['embl_type'];
		$body_part_id=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['body_part'];
		$color_id=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['color_id'];
		$size_id=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['size_id'];
		$wo_id=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['wo_id'];
		$within_group=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['within_group'];
		
		$prod_qty=$production_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('issue_dtls_id')]]['quantity'];
		$production_dtls_ids=$production_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('issue_dtls_id')]]['production_dtls_id'];
		$qc_qty=$qc_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('issue_dtls_id')]]['quantity'];
		$qc_dtls_ids=$qc_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('issue_dtls_id')]]['qc_dtls_id'];

		$productionDtlsIds=implode(",",array_unique(explode(",",(chop($production_dtls_ids,',')))));
		$qcDtlsIds=implode(",",array_unique(explode(",",(chop($qc_dtls_ids,',')))));
		$checkBox_check="checked";
		?>
        <tr>
        	<td>
                <input type="checkbox" name="barcodeChkbox[]" id="barcodeChkbox_<? echo $i; ?>" value="" <? echo $checkBox_check; ?> />
            </td>
            <input type="hidden" name="woID[]" id="woID_<? echo $i; ?>" value="<? echo $wo_id; ?>"  >
            <input type="hidden" name="woDtlsID[]" id="woDtlsID_<? echo $i; ?>" value="<? echo $row[csf("job_dtls_id")]; ?>"  >
            <input type="hidden" name="woBreakID[]" id="woBreakID_<? echo $i; ?>" value="<? echo $row[csf("job_break_id")]; ?>"  >
            <input type="hidden" name="rcvID[]" id="rcvID_<? echo $i; ?>" value="<? echo $row[csf("rcv_id")]; ?>"  >
            <input type="hidden" name="rcvDtlsID[]" id="rcvDtlsID_<? echo $i; ?>" value="<? echo $row[csf("rcv_dtls_id")]; ?>"  >
            <input type="hidden" name="bundleMstID[]" id="bundleMstID_<? echo $i; ?>" value="<? echo $row[csf("bundl_mst_id")]; ?>"  >
            <input type="hidden" name="bundleDtlsID[]" id="bundleDtlsID_<? echo $i; ?>" value="<? echo $row[csf("bundle_dtls_id")]; ?>"  >
            <input type="hidden" name="issueDtlsID[]" id="issueDtlsID_<? echo $i; ?>" value="<? echo $row[csf("issue_dtls_id")]; ?>"  >
            <input type="hidden" name="productionDtlsIds[]" id="productionDtlsIds_<? echo $i; ?>" value="<? echo $productionDtlsIds; ?>"  >
            <input type="hidden" name="qcDtlsIds[]" id="qcDtlsIds<? echo $i; ?>" value="<? echo $qcDtlsIds; ?>"  >
            <input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value=""  >
            
            <td width="40" align="center"><? echo $i; ?></td>
                <!--onDblClick="job_search_popup('requires/embellishment_delivery_bundle_controller.php?action=job_popup','Order Selection Form')" -->
            
            <td><input name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $row[csf("barcode_no")]; ?>" style="width:107px" readonly /></td>
            <td><? 
				echo create_drop_down( "cboCompanyId_".$i, 100, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",$row[csf("company_id")], "",1,'','','','','','',"cboCompanyId[]"); ?></td>
			
			<td><? 
			echo create_drop_down( "cboLocationId_".$i, 100, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --",$row[csf("location_id")], "",1,'','','','','','',"cboLocationId[]"); ?></td>
			<td><? echo create_drop_down( "cboWithinGroup_".$i, 100, $yes_no,"", 1, "-- Select --",2, "",1,'','','','','','',"cboWithinGroup[]"); ?></td>
			<td><? echo create_drop_down( "cboPartyId_".$i, 100, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Customer --",$row[csf("party_id")], "",1,'','','','','','',"cboPartyId[]"); ?></td>
            <td><input name="txtCustBuyer[]" id="txtCustBuyer_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $buyer_buyer; ?>"  style="width:87px" readonly /></td>
            <td><input name="txtIssueDate[]" id="txtIssueDate_<? echo $i; ?>" type="text" class="datepicker" value="<? echo change_date_format($row[csf("issue_date")]); ?>"  style="width:87px" disabled /></td> 
            <td><input name="txtIssueCh[]" id="txtIssueCh_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $row[csf("challan_no")]; ?>"  style="width:47px"  readonly  /></td>
            <td><input name="txtOrder_<? echo $i; ?>" id="txtOrder_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $order_no; ?>"  style="width:47px" readonly /></td>
            <td><input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $style; ?>"  style="width:47px" readonly /></td>
             <td><? echo create_drop_down( "cboProcessName_<? echo $i; ?>", 80, $emblishment_name_array,"", 1, "--Select--",$main_process_id, "",1,'','','','','','',"cboEmbType[]"); ?></td>
			<td><?
				if($main_process_id==1) $emb_type=$emblishment_print_type;
				else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
				else if($main_process_id==3) $emb_type=$emblishment_wash_type;
				else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
				else if($main_process_id==5) $emb_type=$emblishment_gmts_type; 
				echo create_drop_down( "cboEmbType_".$i, 60, $emb_type,"", 1, "-- Select --",$embl_type_id, "",1,'','','','','','',"cboEmbType[]"); ?></td>
			<td><? echo create_drop_down( "cboBodyPart_".$i, 80, $body_part,"", 1, "-- Select --",$body_part_id, "",1,'','','','','','',"cboBodyPart[]"); ?></td>
			<td><input type="text" id="txtcolor_<? echo $i; ?>" name="txtcolor_<? echo $i; ?>" class="text_boxes" value="<? echo $color_arr[$color_id]; ?>"  style="width:87px" readonly/></td>
            <td><input type="text" id="txtsize_<? echo $i; ?>" name="txtsize_<? echo $i; ?>" class="text_boxes txt_size" value="<? echo $size_arr[$size_id]; ?>"  style="width:47px" readonly/></td>
            <td><input name="txtBundleNo[]" id="txtBundleNo_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $row[csf("bundle_no")]; ?>"  style="width:67px" readonly /></td>
            <td><input name="txtBundleQty[]" id="txtBundleQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $row[csf("bundle_qty")]; ?>"  style="width:47px" readonly /></td>
            <td><input name="txtProdQty[]" id="txtProdQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $prod_qty; ?> " style="width:47px" readonly /></td> 
            <td style="display: none;"><input name="txtRejQty[]" id="txtRejQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<??>"  placeholder="Display"   readonly style="width:47px"  />
             	</td>
            <td><input name="txtQcQty[]" id="txtQcQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $qc_qty; ?>" placeholder="Display" style="width:47px" readonly /></td>
            <td><input name="txtRemark[]" id="txtRemark_<? echo $i; ?>" type="text" class="text_boxes" value="" placeholder="Write" /></td>
        </tr>
    <?
    $i++;
	} 
	$r_id3=execute_query("delete from tmp_poid where userid=$user_id");
	if($db_type==0)
	{
		if($r_id3)
		{
			mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($r_id3)
		{
			oci_commit($con);  
		}
	}
	exit();
}

if($action=="reject_qty_popup")
{
	echo load_html_head_contents("Reject Qty Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
    ?>
    <script>
		function fnc_close()
		{
			var tot_row=$('#tbl_share_details_entry tbody tr').length;
			
			var data_break_down="";
			for(var i=1; i<=tot_row; i++)
			{
				/*if (form_validation('txtFabricReject_'+i+'*txtPrintReject_'+i+'*txtPartShort_'+i,'Fabric Reject*Print Reject*Part Short')==false)
				{
					return;
				}*/
				if($("#txtFabricReject_"+i).val()=="") $("#txtFabricReject_"+i).val(0)
				if($("#txtPrintReject_"+i).val()=="") $("#txtPrintReject_"+i).val(0);
				if($("#txtPartShort_"+i).val()=="") $("#txtPartShort_"+i).val(0);
				if($("#hiddenid_"+i).val()=="") $("#hiddenid_"+i).val(0);
				if(data_break_down=="")
				{
					data_break_down+=$('#txtFabricReject_'+i).val()+'_'+$('#txtPrintReject_'+i).val()+'_'+$('#txtPartShort_'+i).val()+'_'+$('#hiddenid_'+i).val();
				}
				else
				{
					data_break_down+="__"+$('#txtFabricReject_'+i).val()+'_'+$('#txtPrintReject_'+i).val()+'_'+$('#txtPartShort_'+i).val()+'_'+$('#hiddenid_'+i).val();
				}
			}
			$('#hidden_break_tot_row').val( data_break_down );
			//alert(data_break_down);//return;
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:100%;" >
		<form name="ratepopup_1"  id="ratepopup_1" autocomplete="off">
			<table class="rpt_table" width="430px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
				<thead>
					<th width="130">Fabric Reject (Defect Qty)</th>
                    <th width="130">Print Reject (Defect Qty)</th>
					<th>Part Short (Defect Qty)</th>
				</thead>
				<tbody>
					<input type="hidden" name="hidden_break_tot_row" id="hidden_break_tot_row" class="text_boxes" style="width:90px" />
					<?
					if($hdnDtlsUpdateId !='')
					{
							$data=explode('_',$data_break);
							?>
							<tr>
								<td>
                                <input type="text" id="txtFabricReject_1" name="txtFabricReject_1"  class="text_boxes_numeric" style="width:130px"  value="<? echo $data[0]; ?>"/>	
								</td>
                                <td>
                                <input type="text" id="txtPrintReject_1" name="txtPrintReject_1"  class="text_boxes_numeric" style="width:130px"  value="<? echo $data[1]; ?>"/>	
                                </td>
                                <td>
                                 <input type="text" id="txtPartShort_1" name="txtPartShort_1"  class="text_boxes_numeric" style="width:130px"  value="<? echo $data[2]; ?>"/>	
                                   <input type="hidden" id="hiddenid_1" name="hiddenid_1"  style="width:15px;" class="text_boxes" value="<? echo $job_dtls_id; ?>" />
								</td>
							</tr>
							<?
					}
					else
					{
						$data=explode('_',$data_break);
						?>
                        <tr>
                            <td><input type="text" id="txtFabricReject_1" name="txtFabricReject_1"  class="text_boxes_numeric" style="width:130px"  value=""/></td>
                            <td><input type="text" id="txtPrintReject_1" name="txtPrintReject_1"  class="text_boxes_numeric" style="width:130px"  value=""/></td>
                            <td>
                            <input type="text" id="txtPartShort_1" name="txtPartShort_1"  class="text_boxes_numeric" style="width:130px"  value=""/>
                            <input type="hidden" id="hiddenid_1" name="hiddenid_1"  class="text_boxes_numeric" style="width:130px"  value="<? echo $job_dtls_id; ?>" />
                             </td>
                        </tr>
						<?
					}
					?> 
				</tbody>
				<tfoot>
					<th colspan="4">&nbsp;</th> 
				</tfoot>
			</table> 
			<table>
				<tr>
					<td align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" /></td>
				</tr>
			</table>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}



if($action=="embl_delivery_bundle_entry_print")
{
	extract($_REQUEST); 
	$data = explode('*', $data);
	$company=$data[0];
	//$location=$data[3];
	$location=0;
	$company_library = return_library_array("SELECT id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$buyer_library = return_library_array("SELECT id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
	$size_arr=return_library_array( "SELECT id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$imge_arr=return_library_array( "SELECT master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$location_arr = return_library_array("SELECT id, location_name from lib_location where status_active=1 and is_deleted=0", 'id', 'location_name');
	
	$sql_mst = "SELECT id, delivery_no, company_id, party_location, location_id, within_group, party_id, delivery_date, job_no, challan_no, remarks from subcon_delivery_mst where entry_form=254 and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray = sql_select($sql_mst); $party_name=""; $party_address=""; $party_address="";
	if( $dataArray[0][csf('within_group')]==1)
	{
		$party_name=$company_library[$dataArray[0][csf('party_id')]];
		$party_address=show_company($dataArray[0][csf('party_id')],'','');
		//if($party_address!="") $party_address=$party_name.', '.$party_address;
	}
	else if($dataArray[0][csf('within_group')]==2) 
	{
		$party_name=$buyer_library[$dataArray[0][csf('party_id')]];
		$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=".$dataArray[0][csf('party_id')].""); 
		foreach ($nameArray as $result)
		{ 
			if($result!="") $party_address=$result['address_1'];
		}
		//if($address!="") $party_address=$party_name.', '.$party_address;
	}


	$com_dtls = fnc_company_location_address($company, $location, 2);

	$sql_wo_del="SELECT a.size_id,a.color_id, b.order_no, b.buyer_buyer, b.buyer_style_ref, b.job_no_mst, b.embl_type,b.main_process_id, c.party_id , d.id as del_dtls_id,d.mst_id,d.company_id,d.entry_form,d.wo_id,d.wo_dtls_id,d.wo_break_id,d.rcv_id,d.rcv_dtls_id,d.bundle_mst_id,d.bundle_dtls_id,d.challan_no,d.quantity,d.issue_dtls_id,e.issue_number as challan_no, e.issue_date as delivery_date, e.remarks as mst_remarks, e.delivery_point  from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e where e.id=d.mst_id and e.id='$data[1]' and a.id=d.wo_break_id and a.mst_id=b.id and b.id=d.wo_dtls_id and a.job_no_mst=c.embellishment_job and d.wo_id=c.id and b.mst_id=c.id and d.entry_form=499 and e.entry_form=499 and d.mst_id='$data[1]' and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0 ";
	$sql_wo_del_res=sql_select($sql_wo_del);
	foreach ($sql_wo_del_res as $row)
	{ 
		if($row[csf("size_id")]!="") $all_sizes.=$row[csf("size_id")].',';
		if($row[csf("party_id")]!="") $all_party_id.=$row[csf("party_id")].',';
		if($row[csf("bundle_dtls_id")]!="") $all_bundle_dtls_ids.=$row[csf("bundle_dtls_id")].',';
		if($row[csf("issue_dtls_id")]!="") $all_issue_dtls_ids.=$row[csf("issue_dtls_id")].',';
		$wo_del_arr[$row[csf("order_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("job_no_mst")]][$row[csf("main_process_id")]][$row[csf("embl_type")]][$row[csf("color_id")]]['quantity'] +=$row[csf("quantity")];
		$wo_del_arr[$row[csf("order_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("job_no_mst")]][$row[csf("main_process_id")]][$row[csf("embl_type")]][$row[csf("color_id")]]['size_id'] .=$row[csf("size_id")].',';
		$wo_del_arr[$row[csf("order_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("job_no_mst")]][$row[csf("main_process_id")]][$row[csf("embl_type")]][$row[csf("color_id")]]['issue_dtls_id'] .=$row[csf("issue_dtls_id")].',';
		
		$wo_del_size_arr[$row[csf("order_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("job_no_mst")]][$row[csf("main_process_id")]][$row[csf("embl_type")]][$row[csf("color_id")]][$row[csf("size_id")]]['quantity'] +=$row[csf("quantity")];
		$wo_del_size_arr[$row[csf("order_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("job_no_mst")]][$row[csf("main_process_id")]][$row[csf("embl_type")]][$row[csf("color_id")]][$row[csf("size_id")]]['bundle_dtls_id'] .=$row[csf("bundle_dtls_id")].',';
		$wo_del_size_arr[$row[csf("order_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("job_no_mst")]][$row[csf("main_process_id")]][$row[csf("embl_type")]][$row[csf("color_id")]][$row[csf("size_id")]]['issue_dtls_id'] .=$row[csf("issue_dtls_id")].',';
	}
	$all_size=array_unique(explode(",",chop($all_sizes,',')));
	//print_r($all_size);
	$all_bundle_dtls_id=implode(",",array_unique(explode(",",chop($all_bundle_dtls_ids,','))));
	$all_party_ids=implode(",",array_unique(explode(",",chop($all_party_id,','))));
	$all_issue_dtls_ids=chop($all_issue_dtls_ids,',');
	$sql_party="SELECT buyer_name, address_1,contact_person ,address_4 , web_site, buyer_email, country_id from lib_buyer where id in ($all_party_ids) and status_active=1";
	$sql_party_res=sql_select($sql_party);
	foreach ($sql_party_res as $result)
	{ 
		$party_name=$result[csf("buyer_name")];
		$party_address=$result[csf("address_1")];
		$contact_person=$result[csf("contact_person")];
		$contact_person_mobile=$result[csf("address_4")];
		$buyer_email=$result[csf("buyer_email")];
		$web_site=$result[csf("web_site")];
	}

	$sql_bundle="SELECT c.id,c.cut_no from prnting_bundle_dtls c where c.id in ($all_bundle_dtls_id) and c.status_active =1 and c.is_deleted =0";
	$sql_bundle_res=sql_select($sql_bundle);

	foreach ($sql_bundle_res as $row)
	{ 
		$bundle_arr[$row[csf("id")]]['cut_no']=$row[csf("cut_no")];
	}

	$sql_defect_qty="SELECT c.issue_dtls_id,c.defect_qty from printing_bundle_issue_dtls c where c.issue_dtls_id in ($all_issue_dtls_ids) and c.entry_form=498 and c.status_active =1 and c.is_deleted =0 ";
	$sql_defect_qty_res=sql_select($sql_defect_qty);

	foreach ($sql_defect_qty_res as $row)
	{ 
		$defect_qty_array=explode('_', $row[csf("defect_qty")]);
		$defect_qty_arr[$row[csf("issue_dtls_id")]]['defect_qty'] +=$defect_qty_array[0];
		$defect_qty_arr[$row[csf("issue_dtls_id")]]['reject_qty'] +=$defect_qty_array[1];
		/*foreach ($defect_qty_array as $val)
		{ 
			
		}*/
	}




	foreach ($wo_del_arr as $order_no => $order_no_data ) 
	{
		foreach ($order_no_data as $buyer_buyer => $buyer_buyer_data ) 
		{
			foreach ($buyer_buyer_data as $buyer_style_ref => $buyer_style_ref_data ) 
			{
				foreach ($buyer_style_ref_data as $job_no_mst => $job_no_mst_data ) 
				{
					foreach ($job_no_mst_data as $main_process_id => $main_process_id_data ) 
					{	
						foreach ($main_process_id_data as $emb_type_id => $emb_type_id_data ) 
						{
							foreach ($emb_type_id_data as $color_id => $row ) 
							{
								//echo $row['size_id'].'=='; 
								$all_del_sizes=array_unique(explode(",",chop($row['size_id'],',')));
								
			                    foreach ($all_del_sizes as $val)
								{ 
									$issue_dtls_id=$wo_del_size_arr[$order_no][$buyer_buyer][$buyer_style_ref][$job_no_mst][$main_process_id][$emb_type_id][$color_id][$val]['issue_dtls_id'];
									$all_issue_dtls_id=array_unique(explode(",",chop($issue_dtls_id,',')));
									
									foreach ($all_issue_dtls_id as $issData)
									{

										$fab_defect_qty2 +=$defect_qty_arr[$issData]['defect_qty'];
										$print_defect_qty2 +=$defect_qty_arr[$issData]['reject_qty'];

										//echo "<pre>".$fab_defect_qty."</pre>"; 
									}
									//$tot_size_reject_arr[$val]['fab_defect_qty']=$fab_defect_qty2;
									//$tot_size_reject_arr[$val]['print_defect_qty']=$print_defect_qty2;
									
								}
									
							}
						}
					}
				}
			}
		}
	}

	$width=800+(count($all_size)*60);
	$mst_width=$width-610;
	$top_width=$width-300;
	$width_px=$width.'px';
	//$width=1100;
	//print_r($defect_qty_arr);
	?>
    <div style="width:<? echo $width; ?>px; font-size:13px">
        <!--<table width="100%" cellpadding="0" cellspacing="0" >-->
        <table align="center" cellspacing="0" width="<? echo $width; ?>"   class="rpt_table">
            <tr>
                <td width="300" align="left"> 
                    <img  src='../../<? echo $com_dtls[2]; ?>' height='50%' width='50%' />
                </td>
                <td>
                    <table width="<? echo $top_width; ?>" cellspacing="0" align="center">
                        <tr>
                            <td align="center" style="font-size:20px"><strong ><? echo $com_dtls[0]; ?></strong></td>
                            <td width="200"><strong >&nbsp;&nbsp;&nbsp;</strong></td>
                        </tr>
                        <tr class="form_caption">
                            <td  align="center" style="font-size:14px">  
                                <? echo $com_dtls[1]; ?> 
                            </td> 
                            <td width="200"><strong >&nbsp;&nbsp;&nbsp;</strong></td> 
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px"><strong><? echo $data[2]; ?></strong></td>
                            <td width="200"><strong >&nbsp;&nbsp;&nbsp;</strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>
       	<table align="center" cellspacing="0" width="<? echo $width; ?>"   class="rpt_table" style="font-size:12px">
            <tr>
            	<td align="left" width="130"><strong>To:</strong></td>
                <td width="175"><? echo $party_name; ?></td>
                <td width="<? echo $mst_width; ?>"></td>
            	<td width="130" align="left"><strong>Challan No.: </strong></td>
                <td width="175"><? echo $sql_wo_del_res[0][csf('challan_no')]; ?></td>
            </tr>
            <tr>
            	<td align="left" width="130"><strong>Address: </strong></td>
                <td width="175"><? echo $party_address; ?></td>
                <td width="<? echo $mst_width; ?>"></td>
            	<td align="left"><strong>Delivery Date: </strong></td>
                <td width="175"><? echo change_date_format($sql_wo_del_res[0][csf('delivery_date')]); ?></td>
            </tr>
            <tr>
            	<td width="130" align="left"><strong>Person:</strong></td>
                <td width="175"><? echo $contact_person; ?></td>
                <td width="<? echo $mst_width; ?>"></td>
            	<td align="left"><strong>Email: </strong></td>
                <td width="175"><? echo $buyer_email; ?></td>
            </tr>
            <tr>
            	<td width="130" align="left"><strong>Cell No.:</strong></td>
                <td width="175"><? echo $contact_person_mobile; ?></td>
                <td width="<? echo $mst_width; ?>"></td>
            	<td align="left"><strong>Website: </strong></td>
                <td width="175"><? echo $web_site; ?></td>
            </tr>
            <tr>
            	
                <td width="130" align="left"><strong>Delivery Location:</strong></td>
                <td width="175"><? echo $sql_wo_del_res[0][csf('delivery_point')]; ?></td>
                <td width="<? echo $mst_width; ?>"></td>
            	<td style="display: none;" align="left"><strong>Fab.Reject Qty: </strong></td>
                <td style="display: none;" width="175"><? echo $fab_defect_qty2; ?></td>
                <td width="130" align="left"><strong>Print Reject Qty:</strong></td>
                <td width="175"><? echo $print_defect_qty2; ?></td>
            </tr>
            <tr>
            	
                <td width="130" align="left"><strong>Remarks:</strong></td>
                <td colspan="3"><? echo $sql_wo_del_res[0][csf('mst_remarks')]; ?></td>
            </tr>
        </table>
        <br>
        <div style="width:100%;">
            <table align="center" cellspacing="0" width="<? echo $width; ?>" border="1" rules="all" class="rpt_table" style="font-size:12px">
                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
                    <th width="30">SL</th>
                    <th width="120">WO No</th>
                    <th width="100">Buyer's Buyer</th>
                    <th width="100">Style</th>
                    <th width="120">Job No</th>
                    <th width="80">Process/Type</th>
                    <th width="100">Color</th>
                    <?
                    foreach ($all_size as $val)
					{ 
						?>
						<th width="60" style="word-break:break-all"><? echo $size_arr[$val]; ?></th>
						<!-- <th width="80">F.R Qty</th> -->
						<!-- <th width="80">Print Reject Qty</th> -->
						<?
					}
				    ?>
                    <th width="60">Goods Delivery</th>
                    <th width="60">F.R Qty</th>
                    <th width="60">Total</th>
                </thead>
                <tbody>
                	<?
                	if(count($wo_del_arr)>0)
			    	{ 
			    		$i=1; $tot_size_arr=array(); $grand_fab_defect_qty2=0; $grand_last_total_qty=0; $grand_size_total_qty=0;
			    		foreach ($wo_del_arr as $order_no => $order_no_data ) 
						{
							foreach ($order_no_data as $buyer_buyer => $buyer_buyer_data ) 
							{
								foreach ($buyer_buyer_data as $buyer_style_ref => $buyer_style_ref_data ) 
								{
									foreach ($buyer_style_ref_data as $job_no_mst => $job_no_mst_data ) 
									{
										foreach ($job_no_mst_data as $main_process_id => $main_process_id_data ) 
										{	
											foreach ($main_process_id_data as $emb_type_id => $emb_type_id_data ) 
											{
												//$grand_fab_defect_qty2=0; $grand_last_total_qty=0;
												foreach ($emb_type_id_data as $color_id => $row ) 
												{
													//echo $row['size_id'].'=='; 
													$all_del_sizes=array_unique(explode(",",chop($row['size_id'],',')));
													if($main_process_id==1) $emb_type=$emblishment_print_type;
													else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
													else if($main_process_id==3) $emb_type=$emblishment_wash_type;
													else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
													else if($main_process_id==5) $emb_type=$emblishment_gmts_type; 
													if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>
								                    <tr bgcolor="<? echo $bgcolor; ?>">
								                        <td><? echo $i; ?></td>
								                        <td style="word-break:break-all"><? echo $order_no ?></td>
								                        <td style="word-break:break-all"><? echo $buyer_buyer; ?></td>
								                        <td style="word-break:break-all"><? echo $buyer_style_ref; ?></td>
								                        <td style="word-break:break-all"><? echo $job_no_mst; ?></td>
								                        <td style="word-break:break-all"><? echo $emb_type[$emb_type_id]; ?></td>
								                        <td style="word-break:break-all"><? echo $color_arr[$color_id]; ?></td>
								                        <?
								                        $size_total_qty=0; $total_defect_qty=0; $cut_nos=''; $cut_no=''; $bundle_dtls_id='';
									                    foreach ($all_size as $val)
														{ 
															$quantity=$wo_del_size_arr[$order_no][$buyer_buyer][$buyer_style_ref][$job_no_mst][$main_process_id][$emb_type_id][$color_id][$val]['quantity'];
															$bundle_dtls_id=$wo_del_size_arr[$order_no][$buyer_buyer][$buyer_style_ref][$job_no_mst][$main_process_id][$emb_type_id][$color_id][$val]['bundle_dtls_id'];
															$issue_dtls_id=$wo_del_size_arr[$order_no][$buyer_buyer][$buyer_style_ref][$job_no_mst][$main_process_id][$emb_type_id][$color_id][$val]['issue_dtls_id'];
															$all_bundle_dtls_id=array_unique(explode(",",chop($bundle_dtls_id,',')));
															$all_issue_dtls_id=array_unique(explode(",",chop($issue_dtls_id,',')));
															foreach ($all_bundle_dtls_id as $bndlData)
															{
																$cut_no .=$bundle_arr[$bndlData]['cut_no'].',';
															}
															$cut_nos=implode(",",array_unique(explode(",",chop($cut_no,','))));

															foreach ($all_issue_dtls_id as $issData)
															{
																$fab_defect_qty +=$defect_qty_arr[$issData]['defect_qty'];
																$print_defect_qty +=$defect_qty_arr[$issData]['reject_qty'];
															}

															?>
															<td align="right" ><? echo $quantity; ?></br><? echo $cut_nos; ?></td>
															 <!-- <td align="right" ><? //echo $fab_defect_qty; ?></td> -->

															<!-- <td align="right" ><? //echo $print_defect_qty; ?></td> -->
															<?

															$size_total_qty +=$quantity;
															$total_defect_qty =$fab_defect_qty;
															$tot_size_arr[$val]+=$quantity;
															$tot_size_reject_arr[$val]['fab_defect_qty']=$fab_defect_qty;
															$tot_size_reject_arr[$val]['print_defect_qty']=$print_defect_qty;
															$quantity=0;
															$last_total=$size_total_qty+$total_defect_qty;
														}
														?>
														
								                        <td align="right" ><? echo $size_total_qty; ?></td>
								                        <td align="right" ><? echo $total_defect_qty; ?></td>
								                        <td align="right" ><? echo $last_total; ?></td>
								                    </tr>
													<?
													$grand_last_total_qty+=$last_total;
													$grand_size_total_qty+=$size_total_qty;
													$grand_fab_defect_qty2+=$total_defect_qty;
												}
											}
										}
									}
								}
							}
						}
					}
                	?>
                </tbody>
                <tfoot>
                	<td colspan="7" align="right"><b>Total:</b></td>
                	<?
                    foreach ($all_size as $val)
					{ 
						$totalQuantity=$tot_size_arr[$val];
						$fab_defect_qty=$tot_size_reject_arr[$val]['fab_defect_qty'];
						$print_defect_qty=$tot_size_reject_arr[$val]['print_defect_qty'];
						$grand_fab_defect_qty+=$fab_defect_qty;
						//$grand_fab_defect_qty+=$total_defect_qty;
						$grand_print_defect_qty+=$print_defect_qty;
						$grandTotalQuantity+=$totalQuantity;
						?>
						<td align="right" ><strong><? echo $totalQuantity; ?></strong></td>
						<!-- <td align="right" ><strong><? //echo $fab_defect_qty; ?></td> -->
						<!-- <td align="right" ><strong><? //echo $print_defect_qty; ?></td> -->
						<?
					}
					?>
                    <td align="right" ><strong><? echo $grand_size_total_qty; //$grandTotalQuantity; ?></td>
                    <td align="right" ><strong><? echo $grand_fab_defect_qty2; ?></td>
                    <td align="right" ><strong><? echo $grand_last_total_qty; ?></td>
                </tfoot>
				
            </table>
            <br>
            	<? echo signature_table(254, $company, $width); ?>
        </div>
    </div>
	<?
	exit();
}
?>