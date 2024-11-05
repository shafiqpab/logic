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
	echo create_drop_down( 'cbo_table', 100, "select id, table_name from lib_table_entry where status_active=1 and is_deleted=0  and floor_name=$floor and table_type=5 order by table_name", "id,table_name", 1, "-- Select Table --", $selected, '', 0 );
	exit();
}

if ($action == "load_drop_down_machine") {
	if($db_type==2)
	{
		echo create_drop_down( "cbo_machine_name", 100, "select id,machine_no || '-' || brand as machine_name from lib_machine_name where category_id=3 and floor_id=$data and status_active=1 and is_deleted=0 and is_locked=0 order by seq_no","id,machine_name", 1,"-- Select Machine --", $selected, "","" );
	}
	else if($db_type==0)
	{
		echo create_drop_down( "cbo_machine_name", 100, "select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=3 and floor_id=$data and status_active=1 and is_deleted=0 and is_locked=0 order by  seq_no","id,machine_name", 1, "-- Select Machine --", $selected, "","" );
	}
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
   	/*$sql_bundle_no="SELECT c.bundle_no_prefix_num,c.bundle_no,c.barcode_no,b.sys_no from subcon_ord_mst a, sub_material_mst b, prnting_bundle_dtls c, printing_bundle_issue_dtls d where a.embellishment_job=b.embl_job_no and b.id=c.item_rcv_id and c.barcode_no='$data[0]' and  d.entry_form=495 and  d.bundle_dtls_id=c.id and d.rcv_id=b.id and d.wo_id=a.id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0  and c.status_active =1 and c.is_deleted =0";
	$bdlNoArray =sql_select($sql_bundle_no); 
	foreach ($bdlNoArray as $row) 
	{
		echo "Bundle No " . $row[csf('bundle_no')] . " Found in Challan No " . $row[csf('sys_no')] . ".";
	}*/

	$sql_next_process="SELECT e.issue_number from prnting_bundle_dtls c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e where e.id=d.mst_id and c.barcode_no='$data[0]' and d.entry_form=495 and  d.bundle_dtls_id=c.id and e.status_active =1 and e.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0";
	$next_process_data =sql_select($sql_next_process); 

	foreach ($next_process_data as $row) 
	{
		echo "Already Issue found, System id " . $row[csf('issue_number')] . ".";
	}
	exit();

    /*$result=sql_select("select a.sys_number,b.bundle_no from pro_gmts_delivery_mst a,pro_garments_production_dtls b where a.id=b.delivery_mst_id and b.barcode_no='$data[0]' and b.production_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number, b.bundle_no");
    foreach ($result as $row)
    {
       echo "Bundle No " . $row[csf('bundle_no')] . " Found in Challan No " . $row[csf('sys_number')] . ".";
    }

    exit();*/
}


/* if($action=="create_receive_search_list_view")
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
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond=""; $buyer_po_cond=""; $buyer_style_cond="";
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
	//die;

	$con = connect();
	$sql_issue="SELECT id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id
			from printing_bundle_issue_dtls where entry_form=495 and status_active=1 and is_deleted=0 order by id ASC";
	$issue_result =sql_select($sql_issue);
	$bundle_dtls_id=""; $wo_id='';
	foreach($issue_result as $row)
	{
		$bundle_dtls_id=$row[csf('bundle_dtls_id')];
		if($bundle_dtls_id!=0)
		{
			$r_id2=execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form) values ($user_id,$bundle_dtls_id,495)");
		}
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
	if ( $spo_ids!="") $spo_idsCond=" and b.job_dtls_id in ($spo_ids)"; else $spo_idsCond="";
	if ( $spo_ids!="") $spo_idsCond_wo=" and b.id in ($spo_ids)"; else $spo_idsCond_wo="";
	$sql_job="SELECT a.within_group,a.company_id , a.id, a.embellishment_job, a.order_id, a.order_no, b.id as po_id, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty, b.order_uom,b.gmts_item_id,b.buyer_buyer,b.buyer_style_ref,b.main_process_id,b.embl_type,b.body_part
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id  and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $spo_idsCond_wo and c.qnty>0 order by c.id ASC";
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
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['embellishment_job']=$row[csf('embellishment_job')];
	}

	$sql="SELECT c.id as bundl_mst_id, c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.pcs_per_bundle, c.no_of_bundle, c.bundle_qty, c.remarks,c.bundle_num_prefix_no,d.id as bundle_dtls_id, d.mst_id, d.item_rcv_id, d.pcs_per_bundle, d.bundle_qty, d.barcode_no, d.barcode_id, d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id as rcv_dtls_id, a.embl_job_no,a.party_id,a.location_id,a.id as rcv_id,a.subcon_date,a.sys_no, $insert_date_cond as year
	from prnting_bundle_mst c, prnting_bundle_dtls d, sub_material_dtls b, sub_material_mst a  where  c.id = d.mst_id and c.item_rcv_dtls_id = b.id and d.item_rcv_id = b.mst_id and d.item_rcv_id = a.id and a.id = b.mst_id and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond $spo_idsCond $po_idsCond $location_cond and d.id not in (select barcode_no from tmp_barcode_no where userid=$user_id and entry_form=495) 
	group by c.id , c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.pcs_per_bundle, c.no_of_bundle, c.bundle_qty, c.remarks,c.bundle_num_prefix_no,d.id , d.mst_id, d.item_rcv_id, d.pcs_per_bundle, d.bundle_qty, d.barcode_no, d.barcode_id , d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id, a.embl_job_no,a.party_id,a.location_id,a.id ,a.subcon_date,a.sys_no, a.insert_date order by b.id";
	$result = sql_select($sql);
	?>
    <div>
    	<table cellpadding="0" cellspacing="2" border="1" width="1040" id="details_tbl" rules="all">
            <thead class="form_table_header">
                <tr align="center" >
                    <th width="30" ></th>
                    <th width="40" >SL</th>
                    <th width="120" >Barcode No.</th>
                    <th width="100" >Comapany</th>
                    <th width="100" >Location</th>
                    <th width="60" >Within Group</th>
                    <th width="100" >Customer</th>
                    <th width="100" >Cus. Buyer</th>
                    <th width="60" >Receive Date</th>
                    <th width="60">Issue Ch No.</th>
                    <th width="120">Embl.Job No.</th>
                    <th width="100">Order No</th>
                    <th width="100">Cust. Style Ref.</th>
                    <th width="60">Embl. Name</th>
                    <th width="60">Embl. Type</th>
                    <th width="80">Body Part</th>
                    <th width="100">Color</th>
                    <th width="60">Size</th>
                    <th width="80">Bundle No.</th>
                    <th >Bundle Qty.</th>
                </tr>
            </thead>
            <div style="width:1040px; max-height:270px; overflow-y:scroll;" >
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
						$embellishment_job=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['embellishment_job'];
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
		                    <input type="hidden" name="updateIdDtls[]" id="updatedtlsid_<? echo $i; ?>" value=""  >
	                        
		                    <td width="40" align="center"><? echo $i; ?></td>
		                        <!--onDblClick="job_search_popup('requires/embellishment_material_issue_bundle_controller.php?action=job_popup','Order Selection Form')" -->
		                    
		                    <td><input name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $row[csf("barcode_no")]; ?>" style="width:107px" readonly /></td>
		                    <td><? 
								echo create_drop_down( "cboCompanyId_".$i, 100, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",$row[csf("company_id")], "",1,'','','','','','',"cboCompanyId[]"); ?></td>
							
							<td><? 
							echo create_drop_down( "cboLocationId_".$i, 100, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --",$row[csf("location_id")], "",1,'','','','','','',"cboLocationId[]"); ?></td>
							<td><? echo create_drop_down( "cboWithinGroup_".$i, 100, $yes_no,"", 1, "-- Select --",$within_group, "",1,'','','','','','',"cboWithinGroup[]"); ?></td>
							<td><? echo create_drop_down( "cboPartyId_".$i, 100, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Customer --",$row[csf("party_id")], "",1,'','','','','','',"cboPartyId[]"); ?></td>
		                    <td><input name="txtCustBuyer[]" id="txtCustBuyer_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $buyer_buyer; ?>"  style="width:87px" readonly /></td>
		                    <td><input name="txtRecDate[]" id="txtRecDate_<? echo $i; ?>" type="text" class="datepicker" value="<? echo change_date_format($row[csf("subcon_date")]); ?>"  style="width:87px" disabled /></td>
		                    <td><input name="txtIssueCh[]" id="txtIssueCh_<? echo $i; ?>" type="text" class="text_boxes" value=""  style="width:47px"  /></td>
		                    <td><input name="txtEmbJob[]" id="txtEmbJob_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $embellishment_job; ?>"  style="width:107px" readonly /></td>
		                    <td><input name="txtOrder_<? echo $i; ?>" id="txtOrder_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $order_no; ?>"  style="width:87px" readonly /></td>
		                    <td><input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $style; ?>"  style="width:87px" readonly /></td>
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
		                    <td><input name="txtBundleNo[]" id="txtBundleNo_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $row[csf("bundle_no")]; ?>"  style="width:67px" readonly title="<? echo $row[csf('barcode_no')]; ?>" /></td>
		                    <td><input name="txtBundleQty[]" id="txtBundleQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $row[csf("bundle_qty")]; ?>" readonly /></td>
		                    <td style="display: none;"><input name="txtProdQty[]" id="txtProdQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="" onKeyUp="fnc_total_calculate ();" /></td>
		                </tr>
	                <?
	                $totalBndlQty+=$row[csf("bundle_qty")];
	                $i++;
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
                    <td>&nbsp;</td>
                    <td>Total:</td>
                    <td><input name="txtTotBundleqty" id="txtTotBundleqty" class="text_boxes_numeric" type="text" value="<? echo $totalBndlQty; ?>" readonly /></td>
                    
                </tr>
            </tfoot>
        </table>
    </div>
    <? //die;
    $r_id3=execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=495");
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
} */

if($action=="create_receive_search_list_view")
{
	$data=explode('_',$data);
	$search_type =$data[6];
	$search_receive =$data[4];
	$within_group =$data[7];
	$location =$data[10];
	$search_by=str_replace("'","",$data[8]);
	$search_str=trim(str_replace("'","",$data[9]));

	if($within_group==1)
	{ 
	//if ($data[0]!=0) $company=" and f.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[0]!=0) $company=" and b.serving_company='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[4]=='' && $data[5]=='' && $search_str=='' && $data[2]=="" &&  $data[3]==""){
		echo "Please Select Date Range"; die; 
	} 
	if ($data[1]!=0) $buyer_cond=" and b.company_id='$data[1]'"; else $buyer_cond="";
 	//if ($data[1]!=0) $buyer_cond=" and a.party_id='$data[1]'"; else $buyer_cond="";
	if ($location!=0) $location_cond=" and b.location='$location'"; else $location_cond="";
	if ($within_group!=0) $withinGroup=" and b.production_source='$within_group'"; else $withinGroup="";
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $recieve_date = "and m.recv_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $recieve_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $recieve_date = "and m.recv_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $recieve_date ="";
	}
	
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond=""; $buyer_po_cond=""; $buyer_style_cond="";
	if($search_type==1)
	{
		if($search_str!="") 
		{
			if($search_by==1) $search_com_cond="and g.job_no_mst='$search_str'";
			else if($search_by==2) $search_com_cond="and b.wo_order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and f.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4){
				$po_cond=" and b.po_number = '$search_str' ";
				$buyer_po_cond=" and g.buyer_po_no = '$search_str' "; 
			} 
			else if ($search_by==5){
				$style_cond=" and a.style_ref_no = '$search_str' ";
				$buyer_style_cond=" and g.buyer_style_ref = '$search_str' ";
			} 
		}
		if ($data[4]!='') $rec_id_cond=" and m.recv_num_prefix_no='$data[4]'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and b.challan_no='$data[5]'"; else $challan_no_cond=""; 
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and g.job_no_mst like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.wo_order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and f.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4){
				$po_cond=" and b.po_number like '%$search_str%'"; 
				$buyer_po_cond=" and g.buyer_po_no like '%$search_str%'"; 
			} 
			else if ($search_by==5){
				$style_cond=" and a.style_ref_no like '%$search_str%'";
				$buyer_style_cond=" and g.buyer_style_ref like '%$search_str%'";
			}   
		}
		if ($data[4]!='') $rec_id_cond=" and m.recv_num_prefix_no like '%$data[4]%'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and b.challan_no like '%$data[5]%'"; else $challan_no_cond="";
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and g.job_no_mst like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.wo_order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and f.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4){
				$po_cond=" and b.po_number like '$search_str%'";
				$buyer_po_cond=" and g.buyer_po_no like '$search_str%'";
			} 
			else if ($search_by==5){
				$style_cond=" and a.style_ref_no like '$search_str%'";
				$buyer_style_cond=" and g.buyer_style_ref like '$search_str%'";
			}   
		}
		if ($data[4]!='') $rec_id_cond=" and m.recv_num_prefix_no like '$data[4]%'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '$data[5]%'"; else $challan_no_cond="";
		if ($data[9]!='') $order_no_cond=" and order_no like '$data[9]%'"; else $order_no_cond="";
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and g.job_no_mst like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and b.wo_order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and f.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4){
				$po_cond=" and b.po_number like '%$search_str'";
				$buyer_po_cond=" and g.buyer_po_no like '%$search_str'";
			} 
			else if ($search_by==5){
				$style_cond=" and a.style_ref_no like '%$search_str'";
				$buyer_style_cond=" and g.buyer_style_ref like '%$search_str'";
			}   
		} 
		if ($data[4]!='') $rec_id_cond=" and m.recv_num_prefix_no like '%$data[4]'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and b.challan_no like '%$data[5]'"; else $challan_no_cond="";
	}	
	
	$size_arr=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');
	$color_arr=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	$buyer_arr=return_library_array( "select id,buyer_name from  lib_buyer", "id","buyer_name"  );
	
	$po_ids=''; $buyer_po_arr=array();
	if($within_group==1)
	{
		if($db_type==0) $id_cond="group_concat(b.id)";
		else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
		//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
		{
			$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.id=b.job_id $job_cond $style_cond $po_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
		}
		//echo $po_ids;
		if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
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
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.embellishment_job=b.job_no_mst $search_com_cond $buyer_po_cond $buyer_style_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}
	if ( $spo_ids!="") $spo_idsCond=" and b.job_dtls_id in ($spo_ids)"; else $spo_idsCond="";


	if($within_group==1)
	{
		
		$year_field="";
	if($db_type==0)
	{
		$year_field="YEAR(f.insert_date)";
	}
	else if($db_type==2)
	{
		$year_field="to_char(f.insert_date,'YYYY')";
	}
	if($db_type==0) { $year_cond=" and YEAR(b.insert_date)=$data[11]";   }
	if($db_type==2) {$year_cond=" and to_char(b.insert_date,'YYYY')=$data[11]";}
	
	/*	
		 $sql="SELECT c.id as bundl_mst_id, c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.pcs_per_bundle, c.no_of_bundle, c.bundle_qty, c.remarks,c.bundle_num_prefix_no,d.id as bundle_dtls_id, d.mst_id, d.item_rcv_id, d.pcs_per_bundle, d.bundle_qty, d.barcode_no, d.barcode_id, d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id as rcv_dtls_id, a.embl_job_no,a.party_id,a.location_id,a.id as rcv_id,a.subcon_date,a.sys_no, $insert_date_cond as year
	from prnting_bundle_mst c, prnting_bundle_dtls d, sub_material_dtls b, sub_material_mst a  where  c.id = d.mst_id and c.item_rcv_dtls_id = b.id and d.item_rcv_id = b.mst_id and d.item_rcv_id = a.id and a.id = b.mst_id and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond $spo_idsCond $po_idsCond $location_cond 
	group by c.id , c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.pcs_per_bundle, c.no_of_bundle, c.bundle_qty, c.remarks,c.bundle_num_prefix_no,d.id , d.mst_id, d.item_rcv_id, d.pcs_per_bundle, d.bundle_qty, d.barcode_no, d.barcode_id , d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id, a.embl_job_no,a.party_id,a.location_id,a.id ,a.subcon_date,a.sys_no, a.insert_date order by b.id"; 
	*/
	
	  $sql="SELECT c.id as bundle_dtls_id,d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, $year_field as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty,c.is_rescan, e.po_number,b.company_id,b.location,b.production_source,b.serving_company,b.embel_name, b.embel_type,b.wo_order_no,b.challan_no,b.wo_order_id,g.id as job_dtls_id,b.id as bundl_mst_id,e.job_id,g.buyer_po_id,e.grouping,m.recv_date,n.quantity  as bundle_qty,m.id as rcv_id,n.id as rcv_dtls_id  from pro_garments_production_mst b,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f,subcon_ord_dtls g,printing_bundle_receive_mst m,printing_bundle_receive_dtls n  where  b.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and  d.job_id=f.id and b.wo_order_no=g.order_no  and c.production_type=2 and  g.buyer_po_id=e.id and m.id=n.mst_id and b.id=n.bundle_mst_id and c.id=n.bundle_dtls_id and g.id=n.job_dtls_id  and c.status_active=1 and c.is_deleted=0 and n.status_active=1 and n.is_deleted=0 and n.print_issue_status=0 $company $location_cond $withinGroup  $recieve_date $challan_no_cond $job_cond $rec_id_cond $search_com_cond $buyer_po_cond $buyer_style_cond $year_cond group by  c.id,d.id, e.id, f.job_no_prefix_num,f.insert_date, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty,c.is_rescan, e.po_number,b.company_id,b.location,b.production_source,b.serving_company,b.embel_name, b.embel_type,b.wo_order_no,b.challan_no,b.wo_order_id,g.id,b.id,e.job_id,g.buyer_po_id,e.grouping,m.recv_date,n.quantity,m.id,n.id order by  c.cut_no,length(c.bundle_no) asc, c.bundle_no asc "; 
	
	  
		
				 
	}
	//and d.id not in (select barcode_no from tmp_barcode_no where userid=$user_id and entry_form=495) 
	$result = sql_select($sql);
	//print_r($result); die;
	foreach($result as $row)
	{
		$job_dtls_id_arr[$row[csf('job_dtls_id')]] =$row[csf('job_dtls_id')];
		$bundle_dtls_id_arr[$row[csf('bundle_dtls_id')]] =$row[csf('bundle_dtls_id')];
	}
	
	$job_dtls_id_arr = array_filter($job_dtls_id_arr);
	if(count($job_dtls_id_arr)>0)
	{
		$job_dtls_ids = implode(",", $job_dtls_id_arr);
		$job_dtls_id_cond=""; $jobIdCond="";
		if($db_type==2 && count($job_dtls_id_arr)>999)
		{
			$job_dtls_id_arr_chunk=array_chunk($job_dtls_id_arr,999) ;
			foreach($job_dtls_id_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$jobIdCond.=" b.id in($chunk_arr_value) or ";
			}

			$job_dtls_id_cond.=" and (".chop($jobIdCond,'or ').")";
		}
		else
		{
			$job_dtls_id_cond=" and b.id in($job_dtls_ids)";
		}

		 $sql_job="SELECT a.within_group,a.company_id , a.id, a.embellishment_job, a.order_id, a.order_no, b.id as po_id, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty, b.order_uom,b.gmts_item_id,b.buyer_buyer,b.buyer_style_ref,b.main_process_id,b.embl_type,b.body_part
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
		where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id  and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $job_dtls_id_cond and c.qnty>0 order by c.id ASC";
		$dataArray =sql_select($sql_job);
		foreach ($dataArray as $row) 
		{
			$job_arr[$row[csf('po_id')]]['style']=$row[csf('buyer_style_ref')];
			$job_arr[$row[csf('po_id')]]['buyer_buyer']=$row[csf('buyer_buyer')];
			$job_arr[$row[csf('po_id')]]['order_no']=$row[csf('order_no')];
			$job_arr[$row[csf('po_id')]]['main_process_id']=$row[csf('main_process_id')];
			$job_arr[$row[csf('po_id')]]['embl_type']=$row[csf('embl_type')];
			$job_arr[$row[csf('po_id')]]['body_part']=$row[csf('body_part')];
			$job_arr[$row[csf('po_id')]]['color_id']=$row[csf('color_id')];
			$job_arr[$row[csf('po_id')]]['size_id']=$row[csf('size_id')];
			$job_arr[$row[csf('po_id')]]['wo_id']=$row[csf('id')];
			$job_arr[$row[csf('po_id')]]['within_group']=$row[csf('within_group')];
			$job_arr[$row[csf('po_id')]]['embellishment_job']=$row[csf('embellishment_job')];
		}
	}

	if(count($bundle_dtls_id_arr)>0)
	{
		$bundle_dtls_ids = implode(",", $bundle_dtls_id_arr);
		$bundle_dtls_id_cond=""; $bundleDtlsIdCond="";
		if($db_type==2 && count($bundle_dtls_id_arr)>999)
		{
			$bundle_dtls_id_arr_chunk=array_chunk($bundle_dtls_id_arr,999) ;
			foreach($bundle_dtls_id_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$bundleDtlsIdCond.=" bundle_dtls_id in($chunk_arr_value) or ";
			}

			$bundle_dtls_id_cond.=" and (".chop($bundleDtlsIdCond,'or ').")";
		}
		else
		{
			$bundle_dtls_id_cond=" and bundle_dtls_id in($bundle_dtls_ids)";
		}

		$sql_issue=sql_select("SELECT id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id from printing_bundle_issue_dtls where entry_form=495 and status_active=1 and is_deleted=0 $bundle_dtls_id_cond");

		foreach ($sql_issue as $val) {
			$issued_bundle_barcode[$val[csf('bundle_dtls_id')]] = $val[csf('bundle_dtls_id')];
		}
	}
	?>
	<style type="text/css">
		.word_wrap_break {word-break:break-all; word-wrap: break-word;}
	</style>
	
    <div>
    		<table cellpadding="0" cellspacing="2" border="1" width="1690" id="details_tbl" rules="all" align="left">
            <thead class="form_table_header">
                <tr align="center" >
                    <th width="50" >SL</th>
                    <th width="90" >Barcode No.</th>
                    <th width="100">Comapany</th>
                    <th width="100" >Location</th>
                    <th width="60" >Within Group</th>
                    <th width="100" >Customer</th>
                    <th width="100" >Cus. Buyer</th>
                    <th width="80" >Receive Date</th>
                    <th width="80">Issue Ch No.</th>
                    <th width="100">Embl.Job No.</th>
                    <th width="140">Order No</th>
                    <th width="140">Cust. Style Ref.</th>
                    <th width="60">Embl. Name</th>
                    <th width="60">Embl. Type</th>
                    <th width="80">Body Part</th>
                    <th width="130">Color</th>
                    <th width="60">Size</th>
                    <th width="80">Bundle No.</th>
                    <th width="80">Bundle Qty.</th>
                </tr>
            </thead>
		</table>
            <div style="width:1710px; max-height:270px; overflow-y:scroll; float:left" >
				<table cellpadding="0" cellspacing="2"  width="1690" id="table_body" class="rpt_table" rules="all" align="left">
	         	<tbody id="rec_issue_table">
	         	<?
					$i=1;
		            foreach($result as $row)
		            {
 						 
							if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$style=$job_arr[$row[csf('job_dtls_id')]]['style'];
							//$buyer_buyer=$job_arr[$row[csf('job_dtls_id')]]['buyer_buyer'];
							$buyer_buyer=$buyer_arr[$row[csf('buyer_name')]];
							$order_no=$job_arr[$row[csf('job_dtls_id')]]['order_no'];
							$main_process_id=$job_arr[$row[csf('job_dtls_id')]]['main_process_id'];
							$embl_type_id=$job_arr[$row[csf('job_dtls_id')]]['embl_type'];
							$body_part_id=$job_arr[$row[csf('job_dtls_id')]]['body_part'];
							//$color_id=$job_arr[$row[csf('job_dtls_id')]]['color_id'];
							//$size_id=$job_arr[$row[csf('job_dtls_id')]]['size_id'];
							$color_id=$row[csf('color_number_id')];
							$size_id=$row[csf('size_number_id')];
							//$order_id=$row[csf('wo_order_id')];
							$order_id=$job_arr[$row[csf('job_dtls_id')]]['wo_id'];
							//$wo_id=$job_arr[$row[csf('job_dtls_id')]]['wo_id'];
							$within_group=$job_arr[$row[csf('job_dtls_id')]]['within_group'];
							$embellishment_job=$job_arr[$row[csf('job_dtls_id')]]['embellishment_job'];
							$checkBox_check="checked";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td width="50" align="center">
									<? echo $i; ?>
									<input type="checkbox" name="barcodeChkbox[]" id="barcodeChkbox_<? echo $i; ?>" value="" <? echo $checkBox_check; ?> />
									<input type="hidden" name="woID[]" id="woID_<? echo $i; ?>" value="<? echo $order_id; ?>">
									<input type="hidden" name="woDtlsID[]" id="woDtlsID_<? echo $i; ?>" value="<? echo $row[csf("job_dtls_id")]; ?>"  >
									<input type="hidden" name="woBreakID[]" id="woBreakID_<? echo $i; ?>" value="<? echo $row[csf("colorsizeid")]; ?>"  >
									<input type="hidden" name="rcvID[]" id="rcvID_<? echo $i; ?>" value="<? echo $row[csf("rcv_id")]; ?>"  >
									<input type="hidden" name="rcvDtlsID[]" id="rcvDtlsID_<? echo $i; ?>" value="<? echo $row[csf("rcv_dtls_id")]; ?>"  >
									<input type="hidden" name="bundleMstID[]" id="bundleMstID_<? echo $i; ?>" value="<? echo $row[csf("bundl_mst_id")]; ?>"  >
									<input type="hidden" name="bundleDtlsID[]" id="bundleDtlsID_<? echo $i; ?>" value="<? echo $row[csf("bundle_dtls_id")]; ?>"  >
									<input type="hidden" name="updateIdDtls[]" id="updatedtlsid_<? echo $i; ?>" value="">
								</td>
									
								<td width="90">
									<? echo $row[csf("barcode_no")]; ?>
									<input type="hidden" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf("barcode_no")]; ?>" style="width:90px" readonly />
								</td>
								<td width="100"><? 
									echo create_drop_down( "cboCompanyId_".$i, 90, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",$row[csf("serving_company")], "",1,'','','','','','',"cboCompanyId[]"); ?></td>
								
								<td width="100">
									<? 
									echo create_drop_down( "cboLocationId_".$i, 90, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --",$row[csf("location")], "",1,'','','','','','',"cboLocationId[]"); 
									?>
								</td>
								<td width="60">
									<? echo create_drop_down( "cboWithinGroup_".$i, 50, $yes_no,"", 1, "-- Select --",$within_group, "",1,'','','','','','',"cboWithinGroup[]"); ?>
								</td>
								<td width="100">
									<? echo create_drop_down( "cboPartyId_".$i, 90, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Customer --",$row[csf("company_id")], "",1,'','','','','','',"cboPartyId[]"); ?>
								</td>
								<td width="100">
									<? echo $buyer_buyer; ?>
									<input type="hidden" name="txtCustBuyer[]" id="txtCustBuyer_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_buyer; ?>"  style="width:87px" readonly />
								</td>
								<td width="80">
									<input name="txtRecDate[]" id="txtRecDate_<? echo $i; ?>" type="text" class="datepicker" value="<? echo change_date_format($row[csf("recv_date")]); ?>"  style="width:55px" disabled />
								</td>
								<td width="80">
									<input name="txtIssueCh[]" id="txtIssueCh_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $row[csf("challan_no")] ?>"  style="width:47px"  />
								</td>
								<td width="100">
									<? echo $embellishment_job; ?>
									<input name="txtEmbJob[]" id="txtEmbJob_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $embellishment_job; ?>"  style="width:90px" readonly />
								</td>
								<td width="140" class="word_wrap_break">
									<? echo $order_no; ?>
									<input type="hidden" name="txtOrder_<? echo $i; ?>" id="txtOrder_<? echo $i; ?>" class="text_boxes" value="<? echo $order_no; ?>"  style="width:87px" readonly />
								</td>
								<td width="140" class="word_wrap_break">
									<? echo $style; ?>
									<input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:87px" readonly />
								</td>
								<td width="60">
									<? echo create_drop_down( "cboProcessName_<? echo $i; ?>", 50, $emblishment_name_array,"", 1, "--Select--",$main_process_id, "",1,'','','','','','',"cboEmbType[]"); ?>
								</td>
								<td width="60">
									<? 
									if($main_process_id==1) $emb_type=$emblishment_print_type;
									else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
									else if($main_process_id==3) $emb_type=$emblishment_wash_type;
									else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
									else if($main_process_id==5) $emb_type=$emblishment_gmts_type;
									echo create_drop_down( "cboEmbType_".$i, 60, $emb_type,"", 1, "-- Select --",$embl_type_id, "",1,'','','','','','',"cboEmbType[]"); ?>
								</td>
								<td width="80">
									<? echo create_drop_down( "cboBodyPart_".$i, 70, $body_part,"", 1, "-- Select --",$body_part_id, "",1,'','','','','','',"cboBodyPart[]"); ?>
								</td>
								<td width="130" class="word_wrap_break">
									<? echo $color_arr[$color_id]; ?>
									<input type="hidden" id="txtcolor_<? echo $i; ?>" name="txtcolor_<? echo $i; ?>" class="text_boxes" value="<? echo $color_arr[$color_id]; ?>"  style="width:87px" readonly/>
								</td>
								<td width="60">
								<? echo $size_arr[$size_id]; ?>
									<input type="hidden" id="txtsize_<? echo $i; ?>" name="txtsize_<? echo $i; ?>" class="text_boxes txt_size" value="<? echo $size_arr[$size_id]; ?>"  style="width:47px" readonly/>
								</td>
								<td width="80">
								<? echo $row[csf("bundle_no")]; ?>
									<input name="txtBundleNo[]" id="txtBundleNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("bundle_no")]; ?>"  style="width:67px" readonly title="<? echo $row[csf('barcode_no')]; ?>" />
								</td>
								<td width="80" align="right">
									<? echo $row[csf("bundle_qty")]; ?>
									<input type="hidden" name="txtBundleQty[]" id="txtBundleQty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf("bundle_qty")]; ?>" style="width:70px" readonly />
									<input name="txtProdQty[]" id="txtProdQty_<? echo $i; ?>" type="hidden" class="text_boxes_numeric" value="" onKeyUp="fnc_total_calculate ();" />
								</td>
								
							</tr>
							<?
							$totalBndlQty+=$row[csf("bundle_qty")];
							$i++;
						 
	                }
	                ?>
	            </tbody>
				</table>
	        </div>
			<table cellpadding="0" cellspacing="2" border="1" width="1690" id="tbl_footer" rules="all" align="left">
				<tfoot>
					<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
						<td width="50" colspan="" align="left">
						All <input form="form_all" type="checkbox" name="check_all" id="check_all" value=""  onclick="fnCheckUnCheckAll(this.checked)"/> 
						</td>
						<td colspan="16" width="80">Total:</td> 
						<td width="80">
							<input name="txtTotBundleqty" id="txtTotBundleqty" class="text_boxes_numeric" type="text" value="<? echo $totalBndlQty; ?>" style="width:70px" readonly />
							<input type="hidden" name="txt_row_no" id="txt_row_no" class="text_boxes_numeric" value="<? echo $i; ?>" style="width:70px" readonly />
						</td>
						
					</tr>
				</tfoot>
        	</table>
    </div>
    <?
	}
	else if($within_group==2)
	{
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
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond=""; $buyer_po_cond=""; $buyer_style_cond="";
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
			$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.id=b.job_id $job_cond $style_cond $po_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
		}
		//echo $po_ids;
		if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
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
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.embellishment_job=b.job_no_mst $search_com_cond $buyer_po_cond $buyer_style_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}
	if ( $spo_ids!="") $spo_idsCond=" and b.job_dtls_id in ($spo_ids)"; else $spo_idsCond="";


	if($within_group==2)
	{
	  $sql="SELECT c.id as bundl_mst_id, c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.pcs_per_bundle, c.no_of_bundle, c.bundle_qty, c.remarks,c.bundle_num_prefix_no,d.id as bundle_dtls_id, d.mst_id, d.item_rcv_id, d.pcs_per_bundle, d.bundle_qty, d.barcode_no, d.barcode_id, d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id as rcv_dtls_id, a.embl_job_no,a.party_id,a.location_id,a.id as rcv_id,a.subcon_date,a.sys_no, $insert_date_cond as year
	from prnting_bundle_mst c, prnting_bundle_dtls d, sub_material_dtls b, sub_material_mst a  where  c.id = d.mst_id and c.item_rcv_dtls_id = b.id and d.item_rcv_id = b.mst_id and d.item_rcv_id = a.id and a.id = b.mst_id and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond $spo_idsCond $po_idsCond $location_cond 
	group by c.id , c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.pcs_per_bundle, c.no_of_bundle, c.bundle_qty, c.remarks,c.bundle_num_prefix_no,d.id , d.mst_id, d.item_rcv_id, d.pcs_per_bundle, d.bundle_qty, d.barcode_no, d.barcode_id , d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id, a.embl_job_no,a.party_id,a.location_id,a.id ,a.subcon_date,a.sys_no, a.insert_date order by b.id";
	}
 	//and d.id not in (select barcode_no from tmp_barcode_no where userid=$user_id and entry_form=495) 
	$result = sql_select($sql);
	foreach($result as $row)
	{
		$job_dtls_id_arr[$row[csf('job_dtls_id')]] =$row[csf('job_dtls_id')];
		$bundle_dtls_id_arr[$row[csf('bundle_dtls_id')]] =$row[csf('bundle_dtls_id')];
	}
	
	$job_dtls_id_arr = array_filter($job_dtls_id_arr);
	if(count($job_dtls_id_arr)>0)
	{
		$job_dtls_ids = implode(",", $job_dtls_id_arr);
		$job_dtls_id_cond=""; $jobIdCond="";
		if($db_type==2 && count($job_dtls_id_arr)>999)
		{
			$job_dtls_id_arr_chunk=array_chunk($job_dtls_id_arr,999) ;
			foreach($job_dtls_id_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$jobIdCond.=" b.id in($chunk_arr_value) or ";
			}

			$job_dtls_id_cond.=" and (".chop($jobIdCond,'or ').")";
		}
		else
		{
			$job_dtls_id_cond=" and b.id in($job_dtls_ids)";
		}

		$sql_job="SELECT a.within_group,a.company_id , a.id, a.embellishment_job, a.order_id, a.order_no, b.id as po_id, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty, b.order_uom,b.gmts_item_id,b.buyer_buyer,b.buyer_style_ref,b.main_process_id,b.embl_type,b.body_part
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
		where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id  and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $job_dtls_id_cond and c.qnty>0 order by c.id ASC";
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
			$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['embellishment_job']=$row[csf('embellishment_job')];
		}
	}

	if(count($bundle_dtls_id_arr)>0)
	{
		$bundle_dtls_ids = implode(",", $bundle_dtls_id_arr);
		$bundle_dtls_id_cond=""; $bundleDtlsIdCond="";
		if($db_type==2 && count($bundle_dtls_id_arr)>999)
		{
			$bundle_dtls_id_arr_chunk=array_chunk($bundle_dtls_id_arr,999) ;
			foreach($bundle_dtls_id_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$bundleDtlsIdCond.=" bundle_dtls_id in($chunk_arr_value) or ";
			}

			$bundle_dtls_id_cond.=" and (".chop($bundleDtlsIdCond,'or ').")";
		}
		else
		{
			$bundle_dtls_id_cond=" and bundle_dtls_id in($bundle_dtls_ids)";
		}

		$sql_issue=sql_select("SELECT id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id from printing_bundle_issue_dtls where entry_form=495 and status_active=1 and is_deleted=0 $bundle_dtls_id_cond");

		foreach ($sql_issue as $val) {
			$issued_bundle_barcode[$val[csf('bundle_dtls_id')]] = $val[csf('bundle_dtls_id')];
		}
	}
	?>
	<style type="text/css">
		.word_wrap_break {word-break:break-all; word-wrap: break-word;}
	</style>
	
    <div>
    		<table cellpadding="0" cellspacing="2" border="1" width="1650" id="details_tbl" rules="all" align="left">
            <thead class="form_table_header">
                <tr align="center" >
                    <th width="50" >SL</th>
                    <th width="90" >Barcode No.</th>
                    <th width="100" >Comapany</th>
                    <th width="100" >Location</th>
                    <th width="60" >Within Group</th>
                    <th width="100" >Customer</th>
                    <th width="100" >Cus. Buyer</th>
                    <th width="60" >Receive Date</th>
                    <th width="60">Issue Ch No.</th>
                    <th width="100">Embl.Job No.</th>
                    <th width="140">Order No</th>
                    <th width="120">Cust. Style Ref.</th>
                    <th width="60">Embl. Name</th>
                    <th width="60">Embl. Type</th>
                    <th width="80">Body Part</th>
                    <th width="120">Color</th>
                    <th width="60">Size</th>
                    <th width="80">Bundle No.</th>
                    <th width="80">Bundle Qty.</th>
                </tr>
            </thead>
		</table>
            <div style="width:1670px; max-height:270px; overflow-y:scroll; float:left" >
				<table cellpadding="0" cellspacing="2"  width="1650" id="table_body" class="rpt_table" rules="all" align="left">
	         	<tbody id="rec_issue_table">
	         	<?
					$i=1;
		            foreach($result as $row)
		            {
						if($issued_bundle_barcode[$row[csf('bundle_dtls_id')]] =="" )
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
							$embellishment_job=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['embellishment_job'];
							$checkBox_check="checked";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td width="50" align="center">
									<? echo $i; ?>
									<input type="checkbox" name="barcodeChkbox[]" id="barcodeChkbox_<? echo $i; ?>" value="" <? echo $checkBox_check; ?> />
									<input type="hidden" name="woID[]" id="woID_<? echo $i; ?>" value="<? echo $wo_id; ?>"  >
									<input type="hidden" name="woDtlsID[]" id="woDtlsID_<? echo $i; ?>" value="<? echo $row[csf("job_dtls_id")]; ?>"  >
									<input type="hidden" name="woBreakID[]" id="woBreakID_<? echo $i; ?>" value="<? echo $row[csf("job_break_id")]; ?>"  >
									<input type="hidden" name="rcvID[]" id="rcvID_<? echo $i; ?>" value="<? echo $row[csf("rcv_id")]; ?>"  >
									<input type="hidden" name="rcvDtlsID[]" id="rcvDtlsID_<? echo $i; ?>" value="<? echo $row[csf("rcv_dtls_id")]; ?>"  >
									<input type="hidden" name="bundleMstID[]" id="bundleMstID_<? echo $i; ?>" value="<? echo $row[csf("bundl_mst_id")]; ?>"  >
									<input type="hidden" name="bundleDtlsID[]" id="bundleDtlsID_<? echo $i; ?>" value="<? echo $row[csf("bundle_dtls_id")]; ?>"  >
									<input type="hidden" name="updateIdDtls[]" id="updatedtlsid_<? echo $i; ?>" value=""  >
								</td>
									
								<td width="90">
									<? echo $row[csf("barcode_no")]; ?>
									<input type="hidden" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf("barcode_no")]; ?>" style="width:90px" readonly />
								</td>
								<td width="100"><? 
									echo create_drop_down( "cboCompanyId_".$i, 90, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",$row[csf("company_id")], "",1,'','','','','','',"cboCompanyId[]"); ?></td>
								
								<td width="100">
									<? 
									echo create_drop_down( "cboLocationId_".$i, 90, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --",$row[csf("location_id")], "",1,'','','','','','',"cboLocationId[]"); 
									?>
								</td>
								<td width="60">
									<? echo create_drop_down( "cboWithinGroup_".$i, 50, $yes_no,"", 1, "-- Select --",$within_group, "",1,'','','','','','',"cboWithinGroup[]"); ?>
								</td>
								<td width="100">
									<? echo create_drop_down( "cboPartyId_".$i, 90, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Customer --",$row[csf("party_id")], "",1,'','','','','','',"cboPartyId[]"); ?>
								</td>
								<td width="100">
									<? echo $buyer_buyer; ?>
									<input type="hidden" name="txtCustBuyer[]" id="txtCustBuyer_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_buyer; ?>"  style="width:87px" readonly />
								</td>
								<td width="60">
									<input name="txtRecDate[]" id="txtRecDate_<? echo $i; ?>" type="text" class="datepicker" value="<? echo change_date_format($row[csf("subcon_date")]); ?>"  style="width:55px" disabled />
								</td>
								<td width="60">
									<input name="txtIssueCh[]" id="txtIssueCh_<? echo $i; ?>" type="text" class="text_boxes" value=""  style="width:47px"  />
								</td>
								<td width="100">
									<? echo $embellishment_job; ?>
									<input name="txtEmbJob[]" id="txtEmbJob_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $embellishment_job; ?>"  style="width:107px" readonly />
								</td>
								<td width="140" class="word_wrap_break">
									<? echo $order_no; ?>
									<input type="hidden" name="txtOrder_<? echo $i; ?>" id="txtOrder_<? echo $i; ?>" class="text_boxes" value="<? echo $order_no; ?>"  style="width:87px" readonly />
								</td>
								<td width="140" class="word_wrap_break">
									<? echo $style; ?>
									<input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:87px" readonly />
								</td>
								<td width="60">
									<? echo create_drop_down( "cboProcessName_<? echo $i; ?>", 50, $emblishment_name_array,"", 1, "--Select--",$main_process_id, "",1,'','','','','','',"cboEmbType[]"); ?>
								</td>
								<td width="60">
									<? 
									if($main_process_id==1) $emb_type=$emblishment_print_type;
									else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
									else if($main_process_id==3) $emb_type=$emblishment_wash_type;
									else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
									else if($main_process_id==5) $emb_type=$emblishment_gmts_type;
									echo create_drop_down( "cboEmbType_".$i, 60, $emb_type,"", 1, "-- Select --",$embl_type_id, "",1,'','','','','','',"cboEmbType[]"); ?>
								</td>
								<td width="80">
									<? echo create_drop_down( "cboBodyPart_".$i, 70, $body_part,"", 1, "-- Select --",$body_part_id, "",1,'','','','','','',"cboBodyPart[]"); ?>
								</td>
								<td width="130" class="word_wrap_break">
									<? echo $color_arr[$color_id]; ?>
									<input type="hidden" id="txtcolor_<? echo $i; ?>" name="txtcolor_<? echo $i; ?>" class="text_boxes" value="<? echo $color_arr[$color_id]; ?>"  style="width:87px" readonly/>
								</td>
								<td width="60">
								<? echo $size_arr[$size_id]; ?>
									<input type="hidden" id="txtsize_<? echo $i; ?>" name="txtsize_<? echo $i; ?>" class="text_boxes txt_size" value="<? echo $size_arr[$size_id]; ?>"  style="width:47px" readonly/>
								</td>
								<td width="80">
								<? echo $row[csf("bundle_no")]; ?>
									<input name="txtBundleNo[]" id="txtBundleNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("bundle_no")]; ?>"  style="width:67px" readonly title="<? echo $row[csf('barcode_no')]; ?>" />
								</td>
								<td width="80" align="right">
									<? echo $row[csf("bundle_qty")]; ?>
									<input type="hidden" name="txtBundleQty[]" id="txtBundleQty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf("bundle_qty")]; ?>" style="width:70px" readonly />
									<input name="txtProdQty[]" id="txtProdQty_<? echo $i; ?>" type="hidden" class="text_boxes_numeric" value="" onKeyUp="fnc_total_calculate ();" />
								</td>
								
							</tr>
							<?
							$totalBndlQty+=$row[csf("bundle_qty")];
							$i++;
						}
	                }
	                ?>
	            </tbody>
				</table>
	        </div>
			<table cellpadding="0" cellspacing="2" border="1" width="1650" id="tbl_footer" rules="all" align="left">
				<tfoot>
					<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
						<td width="50" colspan="" align="left">
						All <input form="form_all" type="checkbox" name="check_all" id="check_all" value=""  onclick="fnCheckUnCheckAll(this.checked)"/> 
						</td>
						<td width="90">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="140">&nbsp;</td>
						<td width="140">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="130">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="80">Total:</td>
						<td width="80">
							<input name="txtTotBundleqty" id="txtTotBundleqty" class="text_boxes_numeric" type="text" value="<? echo $totalBndlQty; ?>" style="width:70px" readonly />
							<input type="hidden" name="txt_row_no" id="txt_row_no" class="text_boxes_numeric" value="<? echo $i; ?>" style="width:70px" readonly />
						</td>
						
					</tr>
				</tfoot>
        	</table>
    </div>
    <?
	}
	
	//die;
    /* $r_id3=execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=495");
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
	} */
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$trans_Type="2"; $barcode_dup_chk_arr=array();
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
		$new_iss_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'PMIB', date("Y",time()), 5, "select issue_num_prefix,issue_num_prefix_no from printing_bundle_issue_mst where entry_form=495 and company_id=$cbo_company_name $insert_date_con order by id desc ", "issue_num_prefix", "issue_num_prefix_no" ));
		
		$field_array="id,company_id,entry_form , issue_num_prefix, issue_num_prefix_no, issue_number ,issue_date,floor_id,table_id,machine_id,within_group,inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",".$cbo_company_name.",495,'".$new_iss_no[1]."','".$new_iss_no[2]."','".$new_iss_no[0]."',".$txt_issue_date.",".$cbo_floor.",".$cbo_table.",".$cbo_machine_name.",".$cbo_within_group.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		$issue_number=$new_iss_no[0]; 

		$field_array1="id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,barcode_no, inserted_by, insert_date, status_active, is_deleted";
		
		$all_wo_id='';
		for($j=1; $j<=$total_row; $j++)
		{
			$woID			= "woID_".$j; 
			$all_wo_id.=str_replace("'", '', $$woID).',';
		}
		$all_wo_ids=implode(",",array_unique(explode(",",chop($all_wo_id,","))));
	   	$sql_prev_issue="SELECT a.issue_number,b.bundle_dtls_id from printing_bundle_issue_mst a, printing_bundle_issue_dtls b where a.id=b.mst_id and a.entry_form=495 and b.entry_form=495 and b.wo_id in ($all_wo_ids) and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0"; 
		$prev_issue_res =sql_select($sql_prev_issue); 
		foreach ($prev_issue_res as $row) 
		{
			$prev_issue_arr[$row[csf('bundle_dtls_id')]]=$row[csf('issue_number')];
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
			$txtRecDate		= "txtRecDate_".$i;
			$txtIssueCh		= "txtIssueCh_".$i;
			$txtProdQty		= "txtBundleQty_".$i;
			$updatedtlsid	= "updatedtlsid_".$i;
			$txtBarcodeNo	= "txtBarcodeNo_".$i;
			if(!in_array(str_replace("'", '', $$bundleDtlsID), $barcode_dup_chk_arr, true)){
        		array_push( $barcode_dup_chk_arr, str_replace("'", '', $$bundleDtlsID));
    		}else{
    			echo "121**Duplicate Barcode No. Found"; die;
    		}

    		$issue_no=$prev_issue_arr[str_replace("'", '', $$bundleDtlsID)];
    		if($issue_no)
			{
    			echo "121**Issue Found. System ID : $issue_no "; die;
    		}
			if ($add_commaa!=0) $data_array1 .=",";
			$data_array1.="(".$id1.",".$id.",".$$cboCompanyId.",495,".$$woID.",".$$woDtlsID.",".$$woBreakID.",".$$rcvID.",".$$rcvDtlsID.",".$$bundleMstID.",".$$bundleDtlsID.",".$$txtIssueCh.",".$$txtProdQty.",".$$txtBarcodeNo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$id_arr_trans[]=str_replace("'",'',$$bundleDtlsID);
			$data_array_trans_update[str_replace("'",'',$$bundleDtlsID)] = explode("*",(1));
			 
			$id1=$id1+1; $add_commaa++;
		}
		$flag=1;
		//echo "INSERT INTO printing_bundle_issue_dtls (".$field_array1.") VALUES ".$data_array1; die;
		$rID=sql_insert("printing_bundle_issue_mst",$field_array,$data_array,0);
		if($flag==1 && $rID==1) $flag=1; else $flag=0;

		$rID1=sql_insert("printing_bundle_issue_dtls",$field_array1,$data_array1,0);
		if($flag==1 && $rID1==1 && $rID==1) $flag=1; else $flag=0;
		
		
		if(str_replace("'",'',$cbo_within_group)==1)
		{
 			 if(count($id_arr_trans)>0)
			{
				$field_array_trans_update="print_issue_status";
				$rID4=execute_query(bulk_update_sql_statement("printing_bundle_receive_dtls","bundle_dtls_id",$field_array_trans_update,$data_array_trans_update,$id_arr_trans),1);
				if($rID4==1 && $flag==1) $flag=1; else $flag=0;	
			}
		} 
		
		
		
		//echo "10**".$rID."**".$rID1."**".$rID4	; die;
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
				
		$id1=return_next_id("id","printing_bundle_issue_dtls",1) ;
		$field_array="issue_date*floor_id*table_id*machine_id*within_group*updated_by*update_date";
		$data_array="".$txt_issue_date."*".$cbo_floor."*".$cbo_table."*".$cbo_machine_name."*".$cbo_within_group."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$field_array_status="updated_by*update_date*status_active*is_deleted";
		$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$field_arr_up="challan_no*quantity*updated_by*update_date*status_active*is_deleted";
		$field_array1="id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,barcode_no, inserted_by, insert_date, status_active, is_deleted";

		$all_wo_id='';
		for($j=1; $j<=$total_row; $j++)
		{
			$woID			= "woID_".$j; 
			$all_wo_id.=str_replace("'", '', $$woID).',';
		}
		$all_wo_ids=implode(",",array_unique(explode(",",chop($all_wo_id,","))));
	   	$sql_prev_issue="SELECT a.issue_number,b.bundle_dtls_id from printing_bundle_issue_mst a, printing_bundle_issue_dtls b where a.id=b.mst_id and a.entry_form=495 and b.entry_form=495 and b.wo_id in ($all_wo_ids) and a.id!=$update_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0"; 
		$prev_issue_res =sql_select($sql_prev_issue); 
		foreach ($prev_issue_res as $row) 
		{
			$prev_issue_arr[$row[csf('bundle_dtls_id')]]=$row[csf('issue_number')];
		}

		$sql_production_check = sql_select("SELECT a.issue_number as PRODUCTION_NO from printing_bundle_issue_dtls b, printing_bundle_issue_dtls c, printing_bundle_issue_mst a where b.entry_form=495 and b.mst_id=$update_id and b.status_active =1 and b.is_deleted =0 and b.bundle_dtls_id = c.bundle_dtls_id and c.entry_form=497 and c.status_active=1 and a.status_active=1 and c.mst_id=a.id and a.entry_form= 497");

		if(!empty($sql_production_check))
		{
			echo "121**Production Found. System ID : ".$sql_production_check[0]['PRODUCTION_NO'];
			disconnect($con);
			die;
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
			$txtRecDate		= "txtRecDate_".$i;
			$txtIssueCh		= "txtIssueCh_".$i;
			$txtProdQty		= "txtBundleQty_".$i;
			$updatedtlsid	= "updatedtlsid_".$i;
			$txtBarcodeNo	= "txtBarcodeNo_".$i;
			if(!in_array(str_replace("'", '', $$bundleDtlsID), $barcode_dup_chk_arr, true)){
        		array_push( $barcode_dup_chk_arr, str_replace("'", '', $$bundleDtlsID));
    		}else{
    			echo "121**Duplicate Barcode No. Found"; die;
    		}

    		$issue_no=$prev_issue_arr[str_replace("'", '', $$bundleDtlsID)];
    		if($issue_no){
    			echo "121**Issue Found. System ID : $issue_no "; die;
    		}
    		
			if(str_replace("'","",$$updatedtlsid)=="")
			{
				if ($add_commaa!=0) $data_array1 .=",";
				$data_array1.="(".$id1.",".$update_id.",".$$cboCompanyId.",495,".$$woID.",".$$woDtlsID.",".$$woBreakID.",".$$rcvID.",".$$rcvDtlsID.",".$$bundleMstID.",".$$bundleDtlsID.",".$$txtIssueCh.",".$$txtProdQty.",".$$txtBarcodeNo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_arr_trans[]=str_replace("'",'',$$bundleDtlsID);
				$data_array_trans_update[str_replace("'",'',$$bundleDtlsID)] = explode("*",(1));
			 
				$id1=$id1+1; $add_commaa++;
			}
			else if(str_replace("'","",$$updatedtlsid)!="")
			{
				$data_arr_up[str_replace("'","",$$updatedtlsid)]=explode("*",("".$$txtIssueCh."*".$$txtProdQty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0"));
				$id_arr_iss[]=str_replace("'","",$$updatedtlsid);
				//$hdn_break_id_arr[]=str_replace("'","",$$updatedtlsid);
			}
		}


		
		$flag=1;
		$rID=sql_update("printing_bundle_issue_mst",$field_array,$data_array,"id",$update_id,0); 
		if($rID==1 && $flag==1) $flag=1; else $flag=0;	

		if($flag==1)
		{
			
			//echo "10**".$field_array_status."===".$data_array_status."==".$update_id; die;
			$rID1=sql_multirow_update("printing_bundle_issue_dtls",$field_array_status,$data_array_status,"mst_id",$update_id,0);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($data_array1!="" && $flag==1)
		{
			//echo "10**INSERT INTO sub_material_dtls (".$field_array2.") VALUES ".$data_array2; die;
			$rID2=sql_insert("printing_bundle_issue_dtls",$field_array1,$data_array1,1);
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		}
			
		if($data_arr_up!=""  && $flag==1)
		{
			//echo "10**".bulk_update_sql_statement( "sub_material_dtls", "id", $field_arr_up,$data_arr_up,$hdn_break_id_arr);
			$rID3=execute_query(bulk_update_sql_statement( "printing_bundle_issue_dtls", "id", $field_arr_up,$data_arr_up,$id_arr_iss),1);
			if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		
		
		if(str_replace("'",'',$cbo_within_group)==1)
		{
 			if(count($id_arr_trans)>0)
			{
				$field_array_trans_update="print_issue_status";
				$rID4=execute_query(bulk_update_sql_statement("printing_bundle_receive_dtls","bundle_dtls_id",$field_array_trans_update,$data_array_trans_update,$id_arr_trans),1);
				if($rID4==1 && $flag==1) $flag=1; else $flag=0;	
			}
		//echo "10**".$data_delete; die;
		     $remove_id=chop($data_delete,",");
		    if($remove_id!="")
			{
				$field_array_del_sales="print_issue_status";
				$data_array_del_sales=0;
				$rID9=sql_multirow_update("printing_bundle_receive_dtls",$field_array_del_sales,$data_array_del_sales,"bundle_dtls_id",$remove_id,1);
				if($rID9==1 && $flag==1) $flag=1; else $flag=0;	
			}
		} 
		
		  
		
	// echo "10**".$rID."**".$rID1."**".$rID2."**".$rID3."**".$rID4."**".$rID9."**".$flag; die;
		 
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id);
			}
		}
		else if($db_type==2)
		{
			if($flag==1) 
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id);
			}
		}
		disconnect($con); die;
	}
	else if ($operation==2)   // delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

  	    $all_wo_id='';
		for($j=1; $j<=$total_row; $j++)
		{
			$woID			= "updatedtlsid_".$j; 
			$all_wo_id.=str_replace("'", '', $$woID).','; 
			 
		}
		$all_wo_ids=implode(",",array_unique(explode(",",chop($all_wo_id,","))));
		  $sql_next_trans="SELECT a.issue_number,b.issue_dtls_id from printing_bundle_issue_mst a, printing_bundle_issue_dtls b where a.id=b.mst_id and a.entry_form=497 and b.entry_form=497 and b.issue_dtls_id in ($all_wo_ids) and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$sql_next_trans_res =sql_select($sql_next_trans); 
		foreach ($sql_next_trans_res as $row) 
		{
			$next_trans_arr[$row[csf('issue_dtls_id')]]=$row[csf('issue_number')];
		}

		$field_array_status="updated_by*update_date*status_active*is_deleted";
		$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$data_array1="";  $add_commaa=0; $updatedtlsDelId=''; $updatedtlsDelIds='';  $bundleDtlsIDDeletes='';$flag=1;
		for($i=1; $i<=$total_row; $i++)
		{
			$updatedtlsid	= "updatedtlsid_".$i;
			$issueDtlsID	= "issueDtlsID_".$i;
			$bundleDtlsID	= "bundleDtlsID_".$i;
    		$next_trans=$next_trans_arr[str_replace("'", '', $$updatedtlsid)];
    		if($next_trans){
    			echo "121**Production Found. System ID : $next_trans "; die;
    		}
			if(str_replace("'","",$$updatedtlsid)!="")
			{
				$updatedtlsDelId.=str_replace("'","",$$updatedtlsid).',';
				$bundleDtlsIDDelete.=str_replace("'","",$$bundleDtlsID).',';
				$id_arr_Delete[]=str_replace("'",'',$$bundleDtlsID);
			}
		}

		$updatedtlsDelIds=implode(",",array_unique(explode(",",(chop($updatedtlsDelId,',')))));
		$bundleDtlsIDDeletes=implode(",",array_unique(explode(",",(chop($bundleDtlsIDDelete,',')))));
		
		//echo "10**".$bundleDtlsIDDeletes; die;
		
		if(count($id_arr_Delete)==count($sql_next_trans_res))
		{
		// echo "10**".$bundleDtlsIDDeletes; die;
			$rID2=sql_update("printing_bundle_issue_mst",$field_array_status,$data_array_status,"id",$update_id,0); 
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;	
			//echo "10**".$updatedtlsDelIds; die;
			$rID = sql_multirow_update("printing_bundle_issue_dtls", $field_array_status, $data_array_status, "id", $updatedtlsDelIds, 1);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;	
			
			$field_array_del_sales="print_issue_status";
			$data_array_del_sales=0;
			$rID9=sql_multirow_update("printing_bundle_receive_dtls",$field_array_del_sales,$data_array_del_sales,"bundle_dtls_id",$bundleDtlsIDDeletes,1);
			if($rID9==1 && $flag==1) $flag=1; else $flag=0;	
			
			if(str_replace("'",'',$cbo_within_group)==1)
			{
				$field_array_del_sales="print_issue_status";
				$data_array_del_sales=0;
				$rID9=sql_multirow_update("printing_bundle_receive_dtls",$field_array_del_sales,$data_array_del_sales,"bundle_dtls_id",$bundleDtlsIDDeletes,1);
				if($rID9==1 && $flag==1) $flag=1; else $flag=0;	
			} 
		}
		else
		{
		    //$rID2=sql_update("printing_bundle_issue_mst",$field_array_status,$data_array_status,"id",$update_id,0); 
			//if($rID2==1 && $flag==1) $flag=1; else $flag=0;	
			//echo "10**".$updatedtlsDelIds; die;
			$rID = sql_multirow_update("printing_bundle_issue_dtls", $field_array_status, $data_array_status, "id", $updatedtlsDelIds, 1);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;	
			
			$field_array_del_sales="print_issue_status";
			$data_array_del_sales=0;
			$rID9=sql_multirow_update("printing_bundle_receive_dtls",$field_array_del_sales,$data_array_del_sales,"bundle_dtls_id",$bundleDtlsIDDeletes,1);
			if($rID9==1 && $flag==1) $flag=1; else $flag=0;	
			
			if(str_replace("'",'',$cbo_within_group)==1)
			{
				$field_array_del_sales="print_issue_status";
				$data_array_del_sales=0;
				$rID9=sql_multirow_update("printing_bundle_receive_dtls",$field_array_del_sales,$data_array_del_sales,"bundle_dtls_id",$bundleDtlsIDDeletes,1);
				if($rID9==1 && $flag==1) $flag=1; else $flag=0;	
			} 
			
		}
		
		//echo "10**".$rID."**".$rID1."**".$rID2."**".$rID3."**".$flag; die;
		 
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id);
			}
		}
		else if($db_type==2)
		{
			if($flag==1) 
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id);
			}
		}
		disconnect($con); die;
	}
}


if( $action == 'issue_item_details_update_bundle')
{
	//$data=explode("**",$data);
	$mst_id=$data;
	$con = connect();
	$sql_issue="SELECT id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity
			from printing_bundle_issue_dtls where mst_id=$mst_id and  entry_form=495 and status_active=1 and is_deleted=0 order by id ASC";
	
	$issue_result =sql_select($sql_issue);
	
	$bundle_dtls_id=""; $wo_id='';
	foreach($issue_result as $iss_id)
	{
		$issue_row_id=$iss_id[csf('bundle_dtls_id')];
		if($issue_row_id!=0)
		{
			$bundle_dtls_id=$iss_id[csf('bundle_dtls_id')];
			$r_id2=execute_query("insert into tmp_poid (userid, poid) values ($user_id,$bundle_dtls_id)");
			//echo $r_id2; die;
			//if($bundle_dtls_id=="") $bundle_dtls_id=$iss_id[csf('bundle_dtls_id')];else $bundle_dtls_id.=",".$iss_id[csf('bundle_dtls_id')];
		}
		$issue_item_arr[$iss_id[csf('wo_dtls_id')]][$iss_id[csf('wo_break_id')]][$iss_id[csf('bundle_dtls_id')]]['challan_no']=$iss_id[csf('challan_no')];
	 	$issue_item_arr[$iss_id[csf('wo_dtls_id')]][$iss_id[csf('wo_break_id')]][$iss_id[csf('bundle_dtls_id')]]['quantity']=$iss_id[csf('quantity')];
	 	$issue_item_arr[$iss_id[csf('wo_dtls_id')]][$iss_id[csf('wo_break_id')]][$iss_id[csf('bundle_dtls_id')]]['updateIdDtls']=$iss_id[csf('id')];
		$job_dtls_id .=$iss_id[csf('wo_dtls_id')].',';
	}
	//print_r($issue_item_arr);
	$job_dtls_ids=implode(",",array_unique(explode(",",(chop($job_dtls_id,',')))));
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
	where b.id in ($job_dtls_ids) and a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id  and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   and c.qnty>0 order by c.id ASC";
	
	$dataArray =sql_select($sql_job);
	foreach ($dataArray as $row) 
	{
	 	$job_arr[$row[csf('po_id')]]['style']=$row[csf('buyer_style_ref')];
	 	$job_arr[$row[csf('po_id')]]['buyer_buyer']=$row[csf('buyer_buyer')];
	 	$job_arr[$row[csf('po_id')]]['order_no']=$row[csf('order_no')];
	 	$job_arr[$row[csf('po_id')]]['main_process_id']=$row[csf('main_process_id')];
	 	$job_arr[$row[csf('po_id')]]['embl_type']=$row[csf('embl_type')];
	 	$job_arr[$row[csf('po_id')]]['body_part']=$row[csf('body_part')];
	 	$job_arr[$row[csf('po_id')]]['color_id']=$row[csf('color_id')];
	 	$job_arr[$row[csf('po_id')]]['size_id']=$row[csf('size_id')];
	 	$job_arr[$row[csf('po_id')]]['wo_id']=$row[csf('id')];
	 	$job_arr[$row[csf('po_id')]]['within_group']=$row[csf('within_group')];
	 	$job_arr[$row[csf('po_id')]]['embellishment_job']=$row[csf('embellishment_job')];
	}

	//$sql_bundle="SELECT a.id as bundl_mst_id, a.company_id, a.po_id, a.order_breakdown_id, a.item_rcv_dtls_id, a.pcs_per_bundle, a.no_of_bundle, a.bundle_qty, a.remarks,b.id, b.mst_id, b.item_rcv_id, b.pcs_per_bundle as dtls_pcs_per_bundle, b.bundle_qty as dtls_bundle_qty, b.barcode_no, b.barcode_id from prnting_bundle_mst a, prnting_bundle_dtls b where a.id=b.mst_id and b.item_rcv_id=$update_id and a.item_rcv_dtls_id=$itemRcvDtlsId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id ASC";

	/*$sql="SELECT a.id as bundl_mst_id, a.company_id, a.po_id, a.order_breakdown_id, a.item_rcv_dtls_id, a.pcs_per_bundle, a.no_of_bundle, a.bundle_qty, a.remarks,a.bundle_num_prefix_no, b.id as bundle_dtls_id, b.mst_id, b.item_rcv_id, b.pcs_per_bundle, b.bundle_qty, b.barcode_no, b.barcode_id , b.bundle_no, c.job_dtls_id, c.job_break_id, c.buyer_po_id, d.embl_job_no,d.party_id,d.location_id,c.id as rcv_dtls_id,d.id as rcv_id,d.subcon_date
	from prnting_bundle_mst a, prnting_bundle_dtls b, sub_material_dtls c, sub_material_mst d , tmp_poid e where a.id=b.mst_id and a.item_rcv_dtls_id=c.id and b.item_rcv_id=c.mst_id and b.item_rcv_id=d.id and d.id=c.mst_id and e.poid=b.id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0
	group by a.id, a.company_id, a.po_id, a.order_breakdown_id, a.item_rcv_dtls_id, a.pcs_per_bundle, a.no_of_bundle, a.bundle_qty, a.remarks,a.bundle_num_prefix_no, b.id, b.mst_id, b.item_rcv_id, b.pcs_per_bundle, b.bundle_qty, b.barcode_no, b.barcode_id, b.bundle_no , c.job_dtls_id, c.job_break_id, c.buyer_po_id,c.id, d.embl_job_no,d.party_id,d.location_id,d.id,d.subcon_date order by b.id";*/
	$year_field="";
	if($db_type==0)
	{
		$year_field="YEAR(f.insert_date)";
	}
	else if($db_type==2)
	{
		$year_field="to_char(f.insert_date,'YYYY')";
	}
	
	$buyer_arr=return_library_array( "select id,buyer_name from  lib_buyer", "id","buyer_name"  );
	
	 $sql="SELECT c.id as bundle_dtls_id,d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, $year_field as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty,c.is_rescan, e.po_number,b.company_id,b.location,b.production_source,b.serving_company,b.embel_name, b.embel_type,b.wo_order_no,b.challan_no,b.wo_order_id,g.id as job_dtls_id,b.id as bundl_mst_id,e.job_id,g.buyer_po_id,e.grouping,m.recv_date,n.quantity  as bundle_qty,m.id as rcv_id,n.id as rcv_dtls_id  from pro_garments_production_mst b,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f,subcon_ord_dtls g,printing_bundle_receive_mst m,printing_bundle_receive_dtls n,tmp_poid k  where  b.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and  d.job_id=f.id and b.wo_order_no=g.order_no and k.poid=c.id  and c.production_type=2 and  g.buyer_po_id=e.id and m.id=n.mst_id and b.id=n.bundle_mst_id and c.id=n.bundle_dtls_id and g.id=n.job_dtls_id and c.status_active=1 and c.is_deleted=0 and n.status_active=1 and n.is_deleted=0 and n.print_issue_status=1 $company $location_cond $withinGroup  $recieve_date $challan_no_cond $job_cond group by  c.id,d.id, e.id, f.job_no_prefix_num, f.insert_date, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty,c.is_rescan, e.po_number,b.company_id,b.location,b.production_source,b.serving_company,b.embel_name, b.embel_type,b.wo_order_no,b.challan_no,b.wo_order_id,g.id,b.id,e.job_id,g.buyer_po_id,e.grouping,m.recv_date,n.quantity,m.id,n.id order by c.cut_no,length(c.bundle_no) asc, c.bundle_no asc "; 
	/*$bdlArray =sql_select($sql_bundle);
	$noOf_bundle=0; $pcsPer_bundle=0; $bundleQty=0; $totalType=1; $bundle_chk_arr=array();
	if(count($bdlArray)>0){
		foreach ($bdlArray as $row) 
		{
			$noOf_bundle++;
		 	$bundle_arr[$row[csf('bundl_mst_id')]][$row[csf('pcs_per_bundle')]][$row[csf('no_of_bundle')]][$row[csf('bundle_qty')]][$row[csf('remarks')]].=$row[csf('id')].'_'.$row[csf('dtls_pcs_per_bundle')].'_'.$row[csf('dtls_bundle_qty')].'_'.$row[csf('barcode_no')].'_'.$row[csf('barcode_id')].'#';
		 	if (!in_array($row[csf('bundl_mst_id')], $bundle_chk_arr)){
		 		$pcsPer_bundle +=$row[csf('pcs_per_bundle')];
		 		$totalType ++;
		 	}
		 	$bundle_chk_arr[$row[csf('bundl_mst_id')]]=$row[csf('bundl_mst_id')];
		 	$bundleQty +=$row[csf('dtls_bundle_qty')];
		}
	}*/
	$result = sql_select($sql);
	if(count($result)>0){
	?>
	<style type="text/css">
		.word_wrap_break {word-break:break-all; word-wrap: break-word;}
	</style>
	    <div>
	    	<table cellpadding="0" cellspacing="2" border="1" width="1770" id="details_tbl" rules="all" align="left">
	            <thead class="form_table_header">
	                <tr align="center" >
	                     <th width="50" >SL</th>
                        <th width="90" >Barcode No.</th>
                        <th width="100">Comapany</th>
                        <th width="100" >Location</th>
                        <th width="60" >Within Group</th>
                        <th width="100" >Customer</th>
                        <th width="100" >Cus. Buyer</th>
                        <th width="80" >Receive Date</th>
                        <th width="80">Issue Ch No.</th>
                        <th width="100">Embl.Job No.</th>
                        <th width="140">Order No</th>
                        <th width="140">Cust. Style Ref.</th>
                        <th width="100">IR/IB</th>
                        <th width="60">Embl. Name</th>
                        <th width="60">Embl. Type</th>
                        <th width="80">Body Part</th>
                        <th width="130">Color</th>
                        <th width="60">Size</th>
                        <th width="80">Bundle No.</th>
                        <th width="80">Bundle Qty.</th>
	                </tr>
	            </thead>
	        </table>
            <div style="width:1790px; max-height:270px;overflow-y:scroll;float:left" >
            	<table cellpadding="0" cellspacing="2"  width="1770" id="table_body" class="rpt_table" rules="all" align="left">
		         	<tbody id="rec_issue_table">
		         	<?
						$i=1;
			            foreach($result as $row)
			            {
			                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$style=$job_arr[$row[csf('job_dtls_id')]]['style'];
							//$buyer_buyer=$job_arr[$row[csf('job_dtls_id')]]['buyer_buyer'];
							//$order_no=$job_arr[$row[csf('job_dtls_id')]]['order_no'];
							//$order_id=$row[csf('wo_order_id')];
							$order_id=$job_arr[$row[csf('job_dtls_id')]]['wo_id'];
							$order_no=$row[csf('wo_order_no')];
							//$main_process_id=$job_arr[$row[csf('job_dtls_id')]]['main_process_id'];
							//$embl_type_id=$job_arr[$row[csf('job_dtls_id')]]['embl_type'];
							
							$buyer_buyer=$buyer_arr[$row[csf('buyer_name')]];
							$main_process_id=$row[csf('embel_name')];
							$embl_type_id=$row[csf('embel_type')];
							$body_part_id=$job_arr[$row[csf('job_dtls_id')]]['body_part'];
							//$color_id=$job_arr[$row[csf('job_dtls_id')]]['color_id'];
							//$size_id=$job_arr[$row[csf('job_dtls_id')]]['size_id'];
							$color_id=$row[csf('color_number_id')];
							$size_id=$row[csf('size_number_id')];
							//$wo_id=$job_arr[$row[csf('job_dtls_id')]]['wo_id'];
							//$order_id=$row[csf('wo_order_id')];
							$within_group=$job_arr[$row[csf('job_dtls_id')]]['within_group'];
							$embellishment_job=$job_arr[$row[csf('job_dtls_id')]]['embellishment_job'];
							$challan_no=$issue_item_arr[$row[csf('job_dtls_id')]][$row[csf('colorsizeid')]][$row[csf('bundle_dtls_id')]]['challan_no'];
		 					$quantity=$issue_item_arr[$row[csf('job_dtls_id')]][$row[csf('colorsizeid')]][$row[csf('bundle_dtls_id')]]['quantity'];
		 					$updateIdDtls=$issue_item_arr[$row[csf('job_dtls_id')]][$row[csf('colorsizeid')]][$row[csf('bundle_dtls_id')]]['updateIdDtls'];
							$checkBox_check="checked";
							
							 
							?>
			                <tr>
			                	<td width="50" align="center">
			                		<? echo $i; ?>
	                                <input type="checkbox" name="barcodeChkbox[]" id="barcodeChkbox_<? echo $i; ?>" value="" <? echo $checkBox_check; ?> />
	                                <input type="hidden" name="woID[]" id="woID_<? echo $i; ?>" value="<? echo $order_id; ?>"  >
				                    <input type="hidden" name="woDtlsID[]" id="woDtlsID_<? echo $i; ?>" value="<? echo $row[csf("job_dtls_id")]; ?>"  >
				                    <input type="hidden" name="woBreakID[]" id="woBreakID_<? echo $i; ?>" value="<? echo $row[csf("colorsizeid")]; ?>"  >
				                    <input type="hidden" name="rcvID[]" id="rcvID_<? echo $i; ?>" value="<? echo $row[csf("rcv_id")]; ?>"  >
				                    <input type="hidden" name="rcvDtlsID[]" id="rcvDtlsID_<? echo $i; ?>" value="<? echo $row[csf("rcv_dtls_id")]; ?>"  >
				                    <input type="hidden" name="bundleMstID[]" id="bundleMstID_<? echo $i; ?>" value="<? echo $row[csf("bundl_mst_id")]; ?>"  >
				                    <input type="hidden" name="bundleDtlsID[]" id="bundleDtlsID_<? echo $i; ?>" value="<? echo $row[csf("bundle_dtls_id")]; ?>"  >
				                    <input type="hidden" name="updateIdDtls[]" id="updatedtlsid_<? echo $i; ?>" value="<? echo $updateIdDtls; ?>"  >
	                            </td>
			                    
			                    <td width="90">
			                    	<? echo $row[csf("barcode_no")]; ?>
			                    	<input name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("barcode_no")]; ?>" style="width:90px" readonly />
			                    </td>
			                    <td width="100"><? 
									echo create_drop_down( "cboCompanyId_".$i, 90, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",$row[csf("serving_company")], "",1,'','','','','','',"cboCompanyId[]");
									?>
								</td> 
								<td width="100">
									<? 
									echo create_drop_down( "cboLocationId_".$i, 90, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --",$row[csf("location")], "",1,'','','','','','',"cboLocationId[]"); 
									?>
								</td>
								<td width="60">
									<? echo create_drop_down( "cboWithinGroup_".$i, 50, $yes_no,"", 1, "-- Select --",$within_group, "",1,'','','','','','',"cboWithinGroup[]"); ?>
								</td>
								<td width="100">
										<? echo create_drop_down( "cboPartyId_".$i, 90, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Customer --",$row[csf("company_id")], "",1,'','','','','','',"cboPartyId[]"); ?>
								</td>
			                    <td width="100">
			                    	<? echo $buyer_buyer; ?>
			                    	<input name="txtCustBuyer[]" id="txtCustBuyer_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $buyer_buyer; ?>"  style="width:87px" readonly />
			                    </td>
			                    <td width="80">
			                    	<input name="txtRecDate[]" id="txtRecDate_<? echo $i; ?>" type="text" class="datepicker" value="<? echo change_date_format($row[csf("recv_date")]); ?>" disabled style="width:55px" />
			                    </td>
			                    <td width="80">
			                    	<input name="txtIssueCh[]" id="txtIssueCh_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $challan_no; ?>"  style="width:47px"  />
			                    </td>
			                    <td width="100">
			                    	<? echo $embellishment_job; ?>
			                    	<input name="txtEmbJob[]" id="txtEmbJob_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $embellishment_job; ?>"  style="width:100px" readonly />
			                    </td>
			                    <td width="140" class="word_wrap_break">
			                    	<? echo $order_no; ?>
			                    	<input name="txtOrder_<? echo $i; ?>" id="txtOrder_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $order_no; ?>"  style="width:87px" readonly />
			                    </td>
			                    <td width="140" class="word_wrap_break">
			                    	<? echo $style; ?>
			                    	<input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:87px" readonly />
			                    </td>
								<td width="100" class="word_wrap_break">
			                    	<? echo $row[csf("grouping")]; ?>
			                    	<!-- <input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:87px" readonly /> -->
			                    </td>
			                    <td width="60">
			                    	<? 
			                    	echo create_drop_down( "cboProcessName_<? echo $i; ?>", 60, $emblishment_name_array,"", 1, "--Select--",$main_process_id, "",1,'','','','','','',"cboEmbType[]"); 
			                    	?>
			                    </td>
								<td width="60">
									<? 
									if($main_process_id==1) $emb_type=$emblishment_print_type;
									else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
									else if($main_process_id==3) $emb_type=$emblishment_wash_type;
									else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
									else if($main_process_id==5) $emb_type=$emblishment_gmts_type;
									echo create_drop_down( "cboEmbType_".$i, 60, $emb_type,"", 1, "-- Select --",$embl_type_id, "",1,'','','','','','',"cboEmbType[]"); ?>
								</td>
								<td width="80">
									<? echo create_drop_down( "cboBodyPart_".$i, 70, $body_part,"", 1, "-- Select --",$body_part_id, "",1,'','','','','','',"cboBodyPart[]"); ?>
										
								</td>
								<td width="130" class="word_wrap_break">
									<? echo $color_arr[$color_id]; ?>
									<input type="hidden" id="txtcolor_<? echo $i; ?>" name="txtcolor_<? echo $i; ?>" class="text_boxes" value="<? echo $color_arr[$color_id]; ?>"  style="width:87px" readonly/>
								</td>
			                    <td width="60">
			                    	<? echo $size_arr[$size_id]; ?>
			                    	<input type="hidden" id="txtsize_<? echo $i; ?>" name="txtsize_<? echo $i; ?>" class="text_boxes txt_size" value="<? echo $size_arr[$size_id]; ?>"  style="width:47px" readonly/>
			                    </td>
			                    <td width="80">
			                    	<? echo $row[csf("bundle_no")]; ?>
			                    	<input type="hidden" name="txtBundleNo[]" id="txtBundleNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf("bundle_no")]; ?>"  style="width:67px" readonly  title="<? echo $row[csf('barcode_no')]; ?>" />
			                    </td>
			                    <td width="80" align="right">
			                    	<? echo $row[csf("bundle_qty")]; ?>
			                    	<input type="hidden" name="txtBundleQty[]" id="txtBundleQty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf("bundle_qty")]; ?>"  readonly />
			                    	<input type="hidden" name="txtProdQty[]" id="txtProdQty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $quantity; ?>" onKeyUp="fnc_total_calculate ();"   />
			                    </td>
			                    
			                </tr>
		                <?
		                $totalBndlQty+=$row[csf("bundle_qty")];
		                $i++;
		                }
		                ?>
		            </tbody>
	        	</table>
	        </div>
	        <table cellpadding="0" cellspacing="2" border="1" width="1770" id="tbl_footer" rules="all" align="left">
	            <tfoot>
	            	<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
	                    <td width="50" align="left">
	                    All	<input form="form_all" type="checkbox" name="check_all" id="check_all" checked="checked" value=""  onclick="fnCheckUnCheckAll(this.checked)"/>
	                    </td>
	                   <td colspan="18" >Total:</td>
	                    <td width="80">
	                    	<input name="txtTotBundleqty" id="txtTotBundleqty" class="text_boxes_numeric" type="text" value="<? echo $totalBndlQty; ?>" style="width:70px" readonly />
	                    	<input type="hidden" name="txt_row_no" id="txt_row_no" class="text_boxes_numeric" value="<? echo $i; ?>" style="width:70px" readonly />
	                    </td>
	                    
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

if( $action == 'issue_item_details_update')
{
	//$data=explode("**",$data);
	$mst_id=$data;
	$con = connect();
	$sql_issue="SELECT id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity
			from printing_bundle_issue_dtls where mst_id=$mst_id and  entry_form=495 and status_active=1 and is_deleted=0 order by id ASC";
	
	$issue_result =sql_select($sql_issue);
	
	$bundle_dtls_id=""; $wo_id='';
	foreach($issue_result as $iss_id)
	{
		$issue_row_id=$iss_id[csf('bundle_dtls_id')];
		if($issue_row_id!=0)
		{
			$bundle_dtls_id=$iss_id[csf('bundle_dtls_id')];
			$r_id2=execute_query("insert into tmp_poid (userid, poid) values ($user_id,$bundle_dtls_id)");
			//echo $r_id2; die;
			//if($bundle_dtls_id=="") $bundle_dtls_id=$iss_id[csf('bundle_dtls_id')];else $bundle_dtls_id.=",".$iss_id[csf('bundle_dtls_id')];
		}
		$issue_item_arr[$iss_id[csf('wo_dtls_id')]][$iss_id[csf('wo_break_id')]][$iss_id[csf('bundle_dtls_id')]]['challan_no']=$iss_id[csf('challan_no')];
	 	$issue_item_arr[$iss_id[csf('wo_dtls_id')]][$iss_id[csf('wo_break_id')]][$iss_id[csf('bundle_dtls_id')]]['quantity']=$iss_id[csf('quantity')];
	 	$issue_item_arr[$iss_id[csf('wo_dtls_id')]][$iss_id[csf('wo_break_id')]][$iss_id[csf('bundle_dtls_id')]]['updateIdDtls']=$iss_id[csf('id')];
		$wo_id .=$iss_id[csf('wo_id')].',';
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
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['within_group']=$row[csf('within_group')];
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['embellishment_job']=$row[csf('embellishment_job')];
	}

	//$sql_bundle="SELECT a.id as bundl_mst_id, a.company_id, a.po_id, a.order_breakdown_id, a.item_rcv_dtls_id, a.pcs_per_bundle, a.no_of_bundle, a.bundle_qty, a.remarks,b.id, b.mst_id, b.item_rcv_id, b.pcs_per_bundle as dtls_pcs_per_bundle, b.bundle_qty as dtls_bundle_qty, b.barcode_no, b.barcode_id from prnting_bundle_mst a, prnting_bundle_dtls b where a.id=b.mst_id and b.item_rcv_id=$update_id and a.item_rcv_dtls_id=$itemRcvDtlsId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id ASC";

	$sql="SELECT a.id as bundl_mst_id, a.company_id, a.po_id, a.order_breakdown_id, a.item_rcv_dtls_id, a.pcs_per_bundle, a.no_of_bundle, a.bundle_qty, a.remarks,a.bundle_num_prefix_no, b.id as bundle_dtls_id, b.mst_id, b.item_rcv_id, b.pcs_per_bundle, b.bundle_qty, b.barcode_no, b.barcode_id , b.bundle_no, c.job_dtls_id, c.job_break_id, c.buyer_po_id, d.embl_job_no,d.party_id,d.location_id,c.id as rcv_dtls_id,d.id as rcv_id,d.subcon_date
	from prnting_bundle_mst a, prnting_bundle_dtls b, sub_material_dtls c, sub_material_mst d , tmp_poid e where a.id=b.mst_id and a.item_rcv_dtls_id=c.id and b.item_rcv_id=c.mst_id and b.item_rcv_id=d.id and d.id=c.mst_id and e.poid=b.id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0
	group by a.id, a.company_id, a.po_id, a.order_breakdown_id, a.item_rcv_dtls_id, a.pcs_per_bundle, a.no_of_bundle, a.bundle_qty, a.remarks,a.bundle_num_prefix_no, b.id, b.mst_id, b.item_rcv_id, b.pcs_per_bundle, b.bundle_qty, b.barcode_no, b.barcode_id, b.bundle_no , c.job_dtls_id, c.job_break_id, c.buyer_po_id,c.id, d.embl_job_no,d.party_id,d.location_id,d.id,d.subcon_date order by b.id";
	/*$bdlArray =sql_select($sql_bundle);
	$noOf_bundle=0; $pcsPer_bundle=0; $bundleQty=0; $totalType=1; $bundle_chk_arr=array();
	if(count($bdlArray)>0){
		foreach ($bdlArray as $row) 
		{
			$noOf_bundle++;
		 	$bundle_arr[$row[csf('bundl_mst_id')]][$row[csf('pcs_per_bundle')]][$row[csf('no_of_bundle')]][$row[csf('bundle_qty')]][$row[csf('remarks')]].=$row[csf('id')].'_'.$row[csf('dtls_pcs_per_bundle')].'_'.$row[csf('dtls_bundle_qty')].'_'.$row[csf('barcode_no')].'_'.$row[csf('barcode_id')].'#';
		 	if (!in_array($row[csf('bundl_mst_id')], $bundle_chk_arr)){
		 		$pcsPer_bundle +=$row[csf('pcs_per_bundle')];
		 		$totalType ++;
		 	}
		 	$bundle_chk_arr[$row[csf('bundl_mst_id')]]=$row[csf('bundl_mst_id')];
		 	$bundleQty +=$row[csf('dtls_bundle_qty')];
		}
	}*/
	$result = sql_select($sql);
	if(count($result)>0){
	?>
	<style type="text/css">
		.word_wrap_break {word-break:break-all; word-wrap: break-word;}
	</style>
	    <div>
	    	<table cellpadding="0" cellspacing="2" border="1" width="1650" id="details_tbl" rules="all" align="left">
	            <thead class="form_table_header">
	                <tr align="center" >
	                    <th width="50" >SL</th>
	                    <th width="90" >Barcode No.</th>
	                    <th width="100" >Comapany</th>
	                    <th width="100" >Location</th>
	                    <th width="60" >Within Group</th>
	                    <th width="100" >Customer</th>
	                    <th width="100" >Cus. Buyer</th>
	                    <th width="60" >Receive Date</th>
	                    <th width="60">Issue Ch No.</th>
	                    <th width="100">Embl.Job No.</th>
	                    <th width="140">Order No</th>
	                    <th width="120">Cust. Style Ref.</th>
	                    <th width="60">Embl. Name</th>
	                    <th width="60">Embl. Type</th>
	                    <th width="80">Body Part</th>
	                    <th width="120">Color</th>
	                    <th width="60">Size</th>
	                    <th width="80">Bundle No.</th>
	                    <th width="80">Bundle Qty.</th>
	                </tr>
	            </thead>
	        </table>
            <div style="width:1670px; max-height:270px;overflow-y:scroll;float:left" >
            	<table cellpadding="0" cellspacing="2"  width="1650" id="table_body" class="rpt_table" rules="all" align="left">
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
							$embellishment_job=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['embellishment_job'];
							$challan_no=$issue_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('bundle_dtls_id')]]['challan_no'];
		 					$quantity=$issue_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('bundle_dtls_id')]]['quantity'];
		 					$updateIdDtls=$issue_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('bundle_dtls_id')]]['updateIdDtls'];
							$checkBox_check="checked";
							?>
			                <tr>
			                	<td width="50" align="center">
			                		<? echo $i; ?>
	                                <input type="checkbox" name="barcodeChkbox[]" id="barcodeChkbox_<? echo $i; ?>" value="" <? echo $checkBox_check; ?> />
	                                <input type="hidden" name="woID[]" id="woID_<? echo $i; ?>" value="<? echo $wo_id; ?>"  >
				                    <input type="hidden" name="woDtlsID[]" id="woDtlsID_<? echo $i; ?>" value="<? echo $row[csf("job_dtls_id")]; ?>"  >
				                    <input type="hidden" name="woBreakID[]" id="woBreakID_<? echo $i; ?>" value="<? echo $row[csf("job_break_id")]; ?>"  >
				                    <input type="hidden" name="rcvID[]" id="rcvID_<? echo $i; ?>" value="<? echo $row[csf("rcv_id")]; ?>"  >
				                    <input type="hidden" name="rcvDtlsID[]" id="rcvDtlsID_<? echo $i; ?>" value="<? echo $row[csf("rcv_dtls_id")]; ?>"  >
				                    <input type="hidden" name="bundleMstID[]" id="bundleMstID_<? echo $i; ?>" value="<? echo $row[csf("bundl_mst_id")]; ?>"  >
				                    <input type="hidden" name="bundleDtlsID[]" id="bundleDtlsID_<? echo $i; ?>" value="<? echo $row[csf("bundle_dtls_id")]; ?>"  >
				                    <input type="hidden" name="updateIdDtls[]" id="updatedtlsid_<? echo $i; ?>" value="<? echo $updateIdDtls; ?>"  >
	                            </td>
			                    
			                    <td width="90">
			                    	<? echo $row[csf("barcode_no")]; ?>
			                    	<input name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("barcode_no")]; ?>" style="width:90px" readonly />
			                    </td>
			                    <td width="100"><? 
									echo create_drop_down( "cboCompanyId_".$i, 90, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",$row[csf("company_id")], "",1,'','','','','','',"cboCompanyId[]");
									?>
								</td>
								<td width="100">
									<? 
									echo create_drop_down( "cboLocationId_".$i, 90, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --",$row[csf("location_id")], "",1,'','','','','','',"cboLocationId[]"); 
									?>
								</td>
								<td width="60">
									<? echo create_drop_down( "cboWithinGroup_".$i, 50, $yes_no,"", 1, "-- Select --",$within_group, "",1,'','','','','','',"cboWithinGroup[]"); ?>
								</td>
								<td width="100">
									<? echo create_drop_down( "cboPartyId_".$i, 90, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Customer --",$row[csf("party_id")], "",1,'','','','','','',"cboPartyId[]"); ?>
								</td>
			                    <td width="100">
			                    	<? echo $buyer_buyer; ?>
			                    	<input name="txtCustBuyer[]" id="txtCustBuyer_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $buyer_buyer; ?>"  style="width:87px" readonly />
			                    </td>
			                    <td width="60">
			                    	<input name="txtRecDate[]" id="txtRecDate_<? echo $i; ?>" type="text" class="datepicker" value="<? echo change_date_format($row[csf("subcon_date")]); ?>" disabled style="width:55px" />
			                    </td>
			                    <td width="60">
			                    	<input name="txtIssueCh[]" id="txtIssueCh_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $challan_no; ?>"  style="width:47px"  />
			                    </td>
			                    <td width="100">
			                    	<? echo $embellishment_job; ?>
			                    	<input name="txtEmbJob[]" id="txtEmbJob_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $embellishment_job; ?>"  style="width:100px" readonly />
			                    </td>
			                    <td width="140" class="word_wrap_break">
			                    	<? echo $order_no; ?>
			                    	<input name="txtOrder_<? echo $i; ?>" id="txtOrder_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $order_no; ?>"  style="width:87px" readonly />
			                    </td>
			                    <td width="140" class="word_wrap_break">
			                    	<? echo $style; ?>
			                    	<input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:87px" readonly />
			                    </td>
			                    <td width="60">
			                    	<? 
			                    	echo create_drop_down( "cboProcessName_<? echo $i; ?>", 80, $emblishment_name_array,"", 1, "--Select--",$main_process_id, "",1,'','','','','','',"cboEmbType[]"); 
			                    	?>
			                    </td>
								<td width="60">
									<? 
									if($main_process_id==1) $emb_type=$emblishment_print_type;
									else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
									else if($main_process_id==3) $emb_type=$emblishment_wash_type;
									else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
									else if($main_process_id==5) $emb_type=$emblishment_gmts_type;
									echo create_drop_down( "cboEmbType_".$i, 60, $emb_type,"", 1, "-- Select --",$embl_type_id, "",1,'','','','','','',"cboEmbType[]"); ?>
								</td>
								<td width="80">
									<? echo create_drop_down( "cboBodyPart_".$i, 70, $body_part,"", 1, "-- Select --",$body_part_id, "",1,'','','','','','',"cboBodyPart[]"); ?>
										
								</td>
								<td width="130" class="word_wrap_break">
									<? echo $color_arr[$color_id]; ?>
									<input type="hidden" id="txtcolor_<? echo $i; ?>" name="txtcolor_<? echo $i; ?>" class="text_boxes" value="<? echo $color_arr[$color_id]; ?>"  style="width:87px" readonly/>
								</td>
			                    <td width="60">
			                    	<? echo $size_arr[$size_id]; ?>
			                    	<input type="hidden" id="txtsize_<? echo $i; ?>" name="txtsize_<? echo $i; ?>" class="text_boxes txt_size" value="<? echo $size_arr[$size_id]; ?>"  style="width:47px" readonly/>
			                    </td>
			                    <td width="80">
			                    	<? echo $row[csf("bundle_no")]; ?>
			                    	<input type="hidden" name="txtBundleNo[]" id="txtBundleNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf("bundle_no")]; ?>"  style="width:67px" readonly  title="<? echo $row[csf('barcode_no')]; ?>" />
			                    </td>
			                    <td width="80" align="right">
			                    	<? echo $row[csf("bundle_qty")]; ?>
			                    	<input type="hidden" name="txtBundleQty[]" id="txtBundleQty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf("bundle_qty")]; ?>"  readonly />
			                    	<input type="hidden" name="txtProdQty[]" id="txtProdQty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $quantity; ?>" onKeyUp="fnc_total_calculate ();"   />
			                    </td>
			                    
			                </tr>
		                <?
		                $totalBndlQty+=$row[csf("bundle_qty")];
		                $i++;
		                }
		                ?>
		            </tbody>
	        	</table>
	        </div>
	        <table cellpadding="0" cellspacing="2" border="1" width="1650" id="tbl_footer" rules="all" align="left">
	            <tfoot>
	            	<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
	                    <td width="50" align="left">
	                    All	<input form="form_all" type="checkbox" name="check_all" id="check_all" checked="checked" value=""  onclick="fnCheckUnCheckAll(this.checked)"/>
	                    </td>
	                    <td width="90">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="140">&nbsp;</td>
						<td width="140">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="130">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="80">Total:</td>
	                    <td width="80">
	                    	<input name="txtTotBundleqty" id="txtTotBundleqty" class="text_boxes_numeric" type="text" value="<? echo $totalBndlQty; ?>" style="width:70px" readonly />
	                    	<input type="hidden" name="txt_row_no" id="txt_row_no" class="text_boxes_numeric" value="<? echo $i; ?>" style="width:70px" readonly />
	                    </td>
	                    
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

if ($action=="issue_popup_in_dtls")
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
			load_drop_down( 'embellishment_material_issue_bundle_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_buyer_pop', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Embl. Job No.');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer PO');
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
                            <th width="140" class="must_entry_caption">Company Name</th>
                            <th width="50">Within Group</th>
                            <th width="120">Party Name</th>
                            <th width="70">Issue ID</th>
                            <th width="80">Challan No</th>
                            <th width="100">Search By</th>
                    		<th width="100" id="search_by_td">Embl. Job No.</th>
                            <th width="100" colspan="2">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_job">  <!--  echo $data;-->
							<? 
								echo create_drop_down( "cbo_company_name", 140, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'embellishment_material_issue_bundle_controller', this.value+'_'+".$within_group.", 'load_drop_down_buyer_pop', 'buyer_td' );"); ?>
                            </td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 0, "--  --",$within_group, "load_drop_down( 'embellishment_material_issue_bundle_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer_pop', 'buyer_td' );" ); ?>
							</td>
                            <td id="buyer_td">
								<? 
								echo create_drop_down( "cbo_party_name", 120, $blank_array,"", 1, "-- Select Party --", $selected, "" );?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:60px" placeholder="Issue ID" />
                            </td>
                            <td>
                                <input type="text" name="txt_search_challan" id="txt_search_challan" class="text_boxes" style="width:70px" placeholder="Challan" />
                            </td>
                            <td>
								<?
                                    $search_by_arr=array(1=>"Embl. Job No.",2=>"W/O No",3=>"Buyer Job",4=>"Buyer PO",5=>"Buyer Style");
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_challan').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'create_issue_search_list_view', 'search_div', 'embellishment_material_issue_bundle_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="top" id=""><div id="search_div"></div></td>
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

if ($action=="issue_popup")
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
                            <th width="140" class="must_entry_caption">Company Name</th>
                            <th width="100">Issue No.</th>
                            <th width="100">Challan No.</th>
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
                            <td align="center">
                                <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From">
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_challan_no').value, 'create_issue_search_list_view', 'popup_search_div', 'embellishment_material_issue_bundle_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
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
	if($data[4] !='') $search_com_cond.="and b.challan_no like '%$data[4]%'";  
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
	
	
	$sql= "SELECT a.id, a.company_id, a.issue_number, a.issue_date, a.floor_id, a.table_id,b.challan_no  from printing_bundle_issue_mst a, printing_bundle_issue_dtls b where a.id=b.mst_id and a.entry_form=495 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_com_cond $issue_date $company group by a.id, a.company_id, a.issue_number, a.issue_date, a.floor_id, a.table_id,b.challan_no order by a.id DESC ";
	//echo $sql; 
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="420" class="rpt_table">
            <thead>
                <th width="50" >SL</th>
                <th width="150" >Issue No</th>
                <th width="100" >Challan No</th>
                <th >Issue Date</th>
                
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
                             <td width="100" align="center"><? echo $row[csf("challan_no")]; ?></td>
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
	$nameArray=sql_select( "SELECT a.id, a.company_id, a.issue_number, a.issue_date, a.floor_id, a.table_id,machine_id from printing_bundle_issue_mst a where a.id='$data' and a.status_active =1 and a.is_deleted=0 and a.entry_form=495" ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_issue_no').value 		= '".$row[csf("issue_number")]."';\n";
		echo "document.getElementById('txt_issue_date').value 		= '".change_date_format($row[csf("issue_date")])."';\n";
		echo "load_drop_down( 'requires/embellishment_material_issue_bundle_controller', '" . $row[csf('floor_id')] . "', 'load_drop_down_table', 'table_td' );\n";
		echo "load_drop_down( 'requires/embellishment_material_issue_bundle_controller', '" . $row[csf('floor_id')] . "', 'load_drop_down_machine', 'td_machine' );\n";
		echo "document.getElementById('cbo_floor').value 			= '".$row[csf("floor_id")]."';\n";
		echo "document.getElementById('cbo_table').value 			= '".$row[csf("table_id")]."';\n";
		echo "document.getElementById('cbo_machine_name').value 	= '".$row[csf("machine_id")]."';\n";
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
			load_drop_down( 'embellishment_material_issue_bundle_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_buyer_pop', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Embl. Job No.');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer PO');
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
                            <th width="140" class="must_entry_caption">Company Name</th>
                            <th width="50">Within Group</th>
                            <th width="120">Party Name</th>
                            <th width="70">Receive ID</th>
                            <th width="80">Challan No</th>
                            <th width="100">Search By</th>
                    		<th width="100" id="search_by_td">Embl. Job No.</th>
                            <th width="100" colspan="2">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_job">  <!--  echo $data;-->
							<? 
								echo create_drop_down( "cbo_company_name", 140, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'embellishment_material_issue_bundle_controller', this.value+'_'+".$within_group.", 'load_drop_down_buyer_pop', 'buyer_td' );"); ?>
                            </td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 0, "--  --",2, "load_drop_down( 'embellishment_material_issue_bundle_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer_pop', 'buyer_td' );" ); ?>
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
                                    $search_by_arr=array(1=>"Embl. Job No.",2=>"W/O No",3=>"Buyer Job",4=>"Buyer PO",5=>"Buyer Style");
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_challan').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'create_receive_search_list_view_browse', 'search_div', 'embellishment_material_issue_bundle_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
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
	if ($data[4]=='' && $data[5]=='' && $search_str=='' && $data[2]=="" &&  $data[3]==""){
		echo "Please Select Date Range"; die; 
	} 

	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
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
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond=""; $buyer_po_cond=""; $buyer_style_cond="";
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
	//$comp=return_library_array("SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	//$po_arr=return_library_array( "SELECT id,order_no from subcon_ord_dtls where status_active =1 and is_deleted=0",'id','order_no');
	
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
	/*if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.embellishment_job=b.job_no_mst $search_com_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}*/

	if((($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2)) || ($within_group==2 && (($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))))
	{
		//echo "select $id_cond as id from subcon_ord_mst a, subcon_ord_dtls b where a.embellishment_job=b.job_no_mst $search_com_cond $buyer_po_cond $buyer_style_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.embellishment_job=b.job_no_mst $search_com_cond $buyer_po_cond $buyer_style_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}
	$con = connect();
	$sql_issue="SELECT id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id
			from printing_bundle_issue_dtls where entry_form=495 and status_active=1 and is_deleted=0 order by id ASC";
	$issue_result =sql_select($sql_issue);
	$bundle_dtls_id=""; $wo_id='';
	foreach($issue_result as $row)
	{
		$bundle_dtls_id=$row[csf('bundle_dtls_id')];
		if($bundle_dtls_id!=0)
		{
			$r_id2=execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form) values ($user_id,$bundle_dtls_id,495)");
		}
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
	
	if ( $spo_ids!="") $spo_idsCond=" and b.job_dtls_id in ($spo_ids)"; else $spo_idsCond="";
	//if ( $spo_ids!="") $spo_idsCond_wo=" and b.id in ($spo_ids)"; else $spo_idsCond_wo="";
	

	$sql="SELECT c.id as bundl_mst_id, c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.pcs_per_bundle, c.no_of_bundle, c.bundle_qty, c.remarks,c.bundle_num_prefix_no,d.id as bundle_dtls_id, d.mst_id, d.item_rcv_id, d.pcs_per_bundle, d.bundle_qty, d.barcode_no, d.barcode_id , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id as rcv_dtls_id, a.embl_job_no,a.party_id,a.location_id,a.id as rcv_id,a.subcon_date,a.sys_no, $insert_date_cond as year,e.order_no
	from prnting_bundle_mst c, prnting_bundle_dtls d, sub_material_dtls b, sub_material_mst a, subcon_ord_mst e  where  c.id = d.mst_id and c.item_rcv_dtls_id = b.id and d.item_rcv_id = b.mst_id and d.item_rcv_id = a.id and a.id = b.mst_id and e.embellishment_job=a.embl_job_no and e.entry_form=204 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond $spo_idsCond $po_idsCond  and d.id not in (select barcode_no from tmp_barcode_no where userid=$user_id and entry_form=495) 
	group by c.id , c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.pcs_per_bundle, c.no_of_bundle, c.bundle_qty, c.remarks,c.bundle_num_prefix_no,d.id , d.mst_id, d.item_rcv_id, d.pcs_per_bundle, d.bundle_qty, d.barcode_no, d.barcode_id , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id, a.embl_job_no,a.party_id,a.location_id,a.id ,a.subcon_date,a.sys_no, a.insert_date,e.order_no order by b.id";


	//echo $sql; 
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="860" class="rpt_table">
            <thead>
                <th width="40" >SL</th>
                <th width="100" >Emb. Job No.</th>
                <th width="100" >WO No.</th>
                <th width="100" >Receive No</th>
                <th width="50" >Year</th>
                <th width="100" >Party Name</th>
                <th width="100" >Challan No</th>
                <th width="60" >Receive Date</th>
                <th >Barcode</th>
            </thead>
     	</table>
     <div style="width:860px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach($result as $row)
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$party_name="";
				if($row[csf("within_group")]==1) $party_name=$comp[$row[csf("party_id")]]; else $party_name=$party_arr[$row[csf("party_id")]];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("rcv_id")]."_".$row[csf("bundle_dtls_id")];?>');" > 
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="100" align="center"><? echo $row[csf("embl_job_no")]; ?></td>
						<td width="100" align="center"><? echo $row[csf("order_no")]; ?></td>
						<td width="100" align="center"><? echo $row[csf("sys_no")]; ?></td>
                        <td width="50" align="center"><? echo $row[csf("year")]; ?></td>
                        <td width="100"><? echo $party_name; ?></td>		
						<td width="100"><? echo $row[csf("chalan_no")]; ?></td>
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
     $r_id3=execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=495");
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

if( $action == 'create_new_table' )
{
	//$data=explode("**",$data);
	//$mst_id=$data;
	//echo $mst_id ; die;
	
	?>
	<style type="text/css">
		.word_wrap_break {word-break:break-all; word-wrap: break-word;}
	</style>
    <div>
    	<table cellpadding="0" cellspacing="2" border="1" width="1670" id="details_tbl" rules="all" align="left">
            <thead class="form_table_header">
                <tr align="center" >
                     <th width="50" >SL</th>
                    <th width="90" >Barcode No.</th>
                    <th width="100">Comapany</th>
                    <th width="100" >Location</th>
                    <th width="60" >Within Group</th>
                    <th width="100" >Customer</th>
                    <th width="100" >Cus. Buyer</th>
                    <th width="80" >Receive Date</th>
                    <th width="80">Issue Ch No.</th>
                    <th width="100">Embl.Job No.</th>
                    <th width="140">Order No</th>
                    <th width="140">Cust. Style Ref.</th>
                    <th width="60">Embl. Name</th>
                    <th width="60">Embl. Type</th>
                    <th width="80">Body Part</th>
                    <th width="130">Color</th>
                    <th width="60">Size</th>
                    <th width="80">Bundle No.</th>
                    <th width="80">Bundle Qty.</th>
                </tr>
            </thead>
        </table>
        <div style="width:1690px; max-height:270px;overflow-y:scroll;float:left" >
        	<table cellpadding="0" cellspacing="2"  width="1670" id="table_body" class="rpt_table" rules="all" align="left">
	         	<tbody id="rec_issue_table">
	         	
	            </tbody>
        	</table>
        </div>
        <table cellpadding="0" cellspacing="2" border="1" width="1670" id="tbl_footer" rules="all" align="left">
	        <tfoot>
	        	<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
					<td width="50" colspan="" align="left">
					All <input form="form_all" type="checkbox" name="check_all" id="check_all" value=""  onclick="fnCheckUnCheckAll(this.checked)"/> 
					</td>
					<td colspan="16" width="80">Total:</td>
					<td width="80">
						<input name="txtTotBundleqty" id="txtTotBundleqty" class="text_boxes_numeric" type="text" value="<? echo $totalBndlQty; ?>" style="width:70px" readonly />
					</td>
					
				</tr>
	        </tfoot>
    	</table>
    </div>
    <style type="text/javascript">
    	setFilterGrid('rec_issue_table',-1,tableFilters);
    </style>
    <?
	exit();
}
if($action=="append_new_item_bundle")
{
	$exdata=explode("**",$data);
	$type=$exdata[2];
	if($type==2){
		
	    $bundle = explode(",", $exdata[0]);
	    $total_row =$exdata[3];
	    //$mst_id = explode(",", $ex_data[2]);
	    $bundle_nos = "'" . implode("','", $bundle) . "'";
	    //$vscan=$ex_data[4];
	    //$source_cond=$ex_data[5];

		$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond=""; $scnbundle_nos_cond=""; $cutbundle_nos_cond="";
		if($db_type==2 && $bundle_count>400)
		{
			//$bundle_nos_cond=" and (";
			//$scnbundle_nos_cond=" and (";
			$cutbundle_nos_cond=" and (";
			$bundleArr=array_chunk(explode(",",$bundle_nos),399);
			foreach($bundleArr as $bundleNos)
			{
				$bundleNos=implode(",",$bundleNos);
				//$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
				//$scnbundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
				$cutbundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
			}
			/*$bundle_nos_cond=chop($bundle_nos_cond,'or ');
			$bundle_nos_cond.=")";

			$scnbundle_nos_cond=chop($scnbundle_nos_cond,'or ');
			$scnbundle_nos_cond.=")";*/

			$cutbundle_nos_cond=chop($cutbundle_nos_cond,'or ');
			$cutbundle_nos_cond.=")";
		}
		else
		{
			//$bundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
			//$scnbundle_nos_cond=" and b.barcode_no in ($bundle_nos)";
			//$cutbundle_nos_cond=" and barcode_no in ($bundle_nos)";

			$cutbundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
		}
	}else{
		$rcv_id =$exdata[0];
		$bundle_dtls_id =$exdata[1];
		$total_row =$exdata[3];
		$cutbundle_nos_cond=" and a.id=$rcv_id and d.id=$bundle_dtls_id ";
	}

	
	
	$size_arr=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');
	$color_arr=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	$buyer_arr=return_library_array( "select id,buyer_name from  lib_buyer", "id","buyer_name"  );
	if($db_type==0)
	{
		$insert_date_cond="year(a.insert_date)";
	}
	else if($db_type==2)
	{
		$insert_date_cond="TO_CHAR(a.insert_date,'YYYY')";
	}
	
	//echo $sql; 

	$sql_job="SELECT a.within_group,a.company_id , a.id, a.embellishment_job, a.order_id, a.order_no, b.id as po_id, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty, b.order_uom,b.gmts_item_id,b.buyer_buyer,b.buyer_style_ref,b.main_process_id,b.embl_type,b.body_part
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
			where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id  and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   and c.qnty>0 order by c.id ASC";
	
	$dataArray =sql_select($sql_job);
	foreach ($dataArray as $row) 
	{
	 	$job_arr[$row[csf('po_id')]]['style']=$row[csf('buyer_style_ref')];
	 	$job_arr[$row[csf('po_id')]]['buyer_buyer']=$row[csf('buyer_buyer')];
	 	$job_arr[$row[csf('po_id')]]['order_no']=$row[csf('order_no')];
	 	$job_arr[$row[csf('po_id')]]['main_process_id']=$row[csf('main_process_id')];
	 	$job_arr[$row[csf('po_id')]]['embl_type']=$row[csf('embl_type')];
	 	$job_arr[$row[csf('po_id')]]['body_part']=$row[csf('body_part')];
	 	$job_arr[$row[csf('po_id')]]['color_id']=$row[csf('color_id')];
	 	$job_arr[$row[csf('po_id')]]['size_id']=$row[csf('size_id')];
	 	$job_arr[$row[csf('po_id')]]['wo_id']=$row[csf('id')];
	 	$job_arr[$row[csf('po_id')]]['within_group']=$row[csf('within_group')];
	 	$job_arr[$row[csf('po_id')]]['embellishment_job']=$row[csf('embellishment_job')];
	}

	//$sql_bundle="SELECT a.id as bundl_mst_id, a.company_id, a.po_id, a.order_breakdown_id, a.item_rcv_dtls_id, a.pcs_per_bundle, a.no_of_bundle, a.bundle_qty, a.remarks,b.id, b.mst_id, b.item_rcv_id, b.pcs_per_bundle as dtls_pcs_per_bundle, b.bundle_qty as dtls_bundle_qty, b.barcode_no, b.barcode_id from prnting_bundle_mst a, prnting_bundle_dtls b where a.id=b.mst_id and b.item_rcv_id=$update_id and a.item_rcv_dtls_id=$itemRcvDtlsId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id ASC";

/*	$sql="SELECT c.id as bundl_mst_id, c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.pcs_per_bundle, c.no_of_bundle, c.bundle_qty, c.remarks,c.bundle_num_prefix_no,d.id as bundle_dtls_id, d.mst_id, d.item_rcv_id, d.pcs_per_bundle, d.bundle_qty, d.barcode_no, d.barcode_id, d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id as rcv_dtls_id, a.embl_job_no,a.party_id,a.location_id,a.id as rcv_id,a.subcon_date,a.sys_no, $insert_date_cond as year
	from prnting_bundle_mst c, prnting_bundle_dtls d, sub_material_dtls b, sub_material_mst a  where  c.id = d.mst_id and c.item_rcv_dtls_id = b.id and d.item_rcv_id = b.mst_id and d.item_rcv_id = a.id and a.id = b.mst_id $cutbundle_nos_cond and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 
	group by c.id , c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.pcs_per_bundle, c.no_of_bundle, c.bundle_qty, c.remarks,c.bundle_num_prefix_no,d.id , d.mst_id, d.item_rcv_id, d.pcs_per_bundle, d.bundle_qty, d.barcode_no, d.barcode_id , b.job_dtls_id, d.bundle_no, b.job_break_id, b.buyer_po_id, b.id, a.embl_job_no,a.party_id,a.location_id,a.id ,a.subcon_date,a.sys_no, a.insert_date order by b.id";*/
	
	
		$year_field="";
	if($db_type==0)
	{
		$year_field="YEAR(f.insert_date)";
	}
	else if($db_type==2)
	{
		$year_field="to_char(f.insert_date,'YYYY')";
	}
	
 	  $sql="SELECT c.id as bundle_dtls_id,d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, $year_field as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty,c.is_rescan, e.po_number,b.company_id,b.location,b.production_source,b.serving_company,b.embel_name, b.embel_type,b.wo_order_no,b.challan_no,b.wo_order_id,g.id as job_dtls_id,b.id as bundl_mst_id,e.job_id,g.buyer_po_id,e.grouping,m.recv_date,n.quantity  as bundle_qty,m.id as rcv_id,n.id as rcv_dtls_id  from pro_garments_production_mst b,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f,subcon_ord_dtls g,printing_bundle_receive_mst m,printing_bundle_receive_dtls n  where  b.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and  d.job_id=f.id and b.wo_order_no=g.order_no  and c.production_type=2 and  g.buyer_po_id=e.id and m.id=n.mst_id and b.id=n.bundle_mst_id and c.id=n.bundle_dtls_id and g.id=n.job_dtls_id and c.status_active=1 and c.is_deleted=0 and n.status_active=1 and n.is_deleted=0 and n.print_issue_status=0 $cutbundle_nos_cond group by c.id,d.id, e.id, f.job_no_prefix_num, f.insert_date, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty,c.is_rescan, e.po_number,b.company_id,b.location,b.production_source,b.serving_company,b.embel_name, b.embel_type,b.wo_order_no,b.challan_no,b.wo_order_id,g.id ,b.id ,e.job_id,g.buyer_po_id,e.grouping,m.recv_date,n.quantity,m.id,n.id order by  c.cut_no,length(c.bundle_no) asc, c.bundle_no asc "; 
	/*$bdlArray =sql_select($sql_bundle);
	$noOf_bundle=0; $pcsPer_bundle=0; $bundleQty=0; $totalType=1; $bundle_chk_arr=array();
	if(count($bdlArray)>0){
		foreach ($bdlArray as $row) 
		{
			$noOf_bundle++;
		 	$bundle_arr[$row[csf('bundl_mst_id')]][$row[csf('pcs_per_bundle')]][$row[csf('no_of_bundle')]][$row[csf('bundle_qty')]][$row[csf('remarks')]].=$row[csf('id')].'_'.$row[csf('dtls_pcs_per_bundle')].'_'.$row[csf('dtls_bundle_qty')].'_'.$row[csf('barcode_no')].'_'.$row[csf('barcode_id')].'#';
		 	if (!in_array($row[csf('bundl_mst_id')], $bundle_chk_arr)){
		 		$pcsPer_bundle +=$row[csf('pcs_per_bundle')];
		 		$totalType ++;
		 	}
		 	$bundle_chk_arr[$row[csf('bundl_mst_id')]]=$row[csf('bundl_mst_id')];
		 	$bundleQty +=$row[csf('dtls_bundle_qty')];
		}
	}*/
	$result = sql_select($sql);
	$i=$total_row+1;
    foreach($result as $row)
    {
        if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$style=$job_arr[$row[csf('job_dtls_id')]]['style'];
		//$buyer_buyer=$job_arr[$row[csf('job_dtls_id')]]['buyer_buyer'];
		$buyer_buyer=$buyer_arr[$row[csf('buyer_name')]];
		//$order_no=$job_arr[$row[csf('job_dtls_id')]]['order_no'];
		//$main_process_id=$job_arr[$row[csf('job_dtls_id')]]['main_process_id'];
		//$embl_type_id=$job_arr[$row[csf('job_dtls_id')]]['embl_type'];
		$body_part_id=$job_arr[$row[csf('job_dtls_id')]]['body_part'];
		//$color_id=$job_arr[$row[csf('job_dtls_id')]]['color_id'];
		//$size_id=$job_arr[$row[csf('job_dtls_id')]]['size_id'];
		//$wo_id=$job_arr[$row[csf('job_dtls_id')]]['wo_id'];
		$within_group=$job_arr[$row[csf('job_dtls_id')]]['within_group'];
		$embellishment_job=$job_arr[$row[csf('job_dtls_id')]]['embellishment_job'];
		
		//$order_id=$row[csf('wo_order_id')];
		$order_id=$job_arr[$row[csf('job_dtls_id')]]['wo_id'];
		$order_no=$row[csf('wo_order_no')];
		$buyer_buyer=$buyer_arr[$row[csf('buyer_name')]];
		$main_process_id=$row[csf('embel_name')];
		$embl_type_id=$row[csf('embel_type')];
		$color_id=$row[csf('color_number_id')];
		$size_id=$row[csf('size_number_id')];
		$checkBox_check="checked";
		?>
        <tr>
        	<td width="50" align="center">
        		<? echo $i; ?>
                <input type="checkbox" name="barcodeChkbox[]" id="barcodeChkbox_<? echo $i; ?>" value="" <? echo $checkBox_check; ?> />
                <input type="hidden" name="woID[]" id="woID_<? echo $i; ?>" value="<? echo $order_id; ?>"  >
	            <input type="hidden" name="woDtlsID[]" id="woDtlsID_<? echo $i; ?>" value="<? echo $row[csf("job_dtls_id")]; ?>"  >
	            <input type="hidden" name="woBreakID[]" id="woBreakID_<? echo $i; ?>" value="<? echo $row[csf("colorsizeid")]; ?>"  >
	            <input type="hidden" name="rcvID[]" id="rcvID_<? echo $i; ?>" value="<? echo $row[csf("rcv_id")]; ?>"  >
	            <input type="hidden" name="rcvDtlsID[]" id="rcvDtlsID_<? echo $i; ?>" value="<? echo $row[csf("rcv_dtls_id")]; ?>"  >
	            <input type="hidden" name="bundleMstID[]" id="bundleMstID_<? echo $i; ?>" value="<? echo $row[csf("bundl_mst_id")]; ?>"  >
	            <input type="hidden" name="bundleDtlsID[]" id="bundleDtlsID_<? echo $i; ?>" value="<? echo $row[csf("bundle_dtls_id")]; ?>"  >
	            <input type="hidden" name="updateIdDtls[]" id="updatedtlsid_<? echo $i; ?>" value=""  >
            </td>
            
            <td width="90">
            	<? echo $row[csf("barcode_no")]; ?>  
            	<input name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("barcode_no")]; ?>" style="width:90px" readonly />
            </td>
            <td width="100">
            	<? 
				echo create_drop_down( "cboCompanyId_".$i, 90, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",$row[csf("serving_company")], "",1,'','','','','','',"cboCompanyId[]"); 
				?>
			</td>
			
			<td width="100">
				<?
				echo create_drop_down( "cboLocationId_".$i, 90, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --",$row[csf("location")], "",1,'','','','','','',"cboLocationId[]"); 
			?>
				
			</td>
			<td width="60">
				<? echo create_drop_down( "cboWithinGroup_".$i, 50, $yes_no,"", 1, "-- Select --",$within_group, "",1,'','','','','','',"cboWithinGroup[]"); 
				?>
			</td>
			<td width="100">
				<? echo create_drop_down( "cboPartyId_".$i, 90, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Customer --",$row[csf("company_id")], "",1,'','','','','','',"cboPartyId[]"); 
				?>
			</td>
            <td width="100">
            	<? echo $buyer_buyer; ?>
            	<input name="txtCustBuyer[]" id="txtCustBuyer_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $buyer_buyer; ?>"  style="width:87px" readonly />
            </td>
            <td width="80">
            	<input name="txtRecDate[]" id="txtRecDate_<? echo $i; ?>" type="text" class="datepicker" value="<? echo change_date_format($row[csf("recv_date")]); ?>" disabled style="width:55px" />
            </td>
            <td width="80">
            	<input name="txtIssueCh[]" id="txtIssueCh_<? echo $i; ?>" type="text" class="text_boxes" value="<? echo $row[csf("challan_no")]?>"  style="width:47px"  />
            </td>
            <td width="100">
            	<? echo $embellishment_job; ?>
            	<input name="txtEmbJob[]" id="txtEmbJob_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $embellishment_job; ?>"  style="width:107px" readonly />
            </td>
            <td width="140" class="word_wrap_break">
            	<? echo $order_no; ?>
            	<input name="txtOrder_<? echo $i; ?>" id="txtOrder_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $order_no; ?>"  style="width:87px" readonly />
            </td>
            <td width="140" class="word_wrap_break">
            	<? echo $style; ?>
            	<input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:87px" readonly />
            </td>
			<td width="100" class="word_wrap_break">
            	<? echo $row[csf('grouping')]; ?>
            	<!-- <input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:87px" readonly /> -->
            </td>
            <td width="60">
            	<? 
            	echo create_drop_down( "cboProcessName_<? echo $i; ?>", 60, $emblishment_name_array,"", 1, "--Select--",$main_process_id, "",1,'','','','','','',"cboEmbType[]"); ?>
            </td>
			<td width="60">
				<? 
				if($main_process_id==1) $emb_type=$emblishment_print_type;
				else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
				else if($main_process_id==3) $emb_type=$emblishment_wash_type;
				else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
				else if($main_process_id==5) $emb_type=$emblishment_gmts_type;
				echo create_drop_down( "cboEmbType_".$i, 60, $emb_type,"", 1, "-- Select --",$embl_type_id, "",1,'','','','','','',"cboEmbType[]"); 
				?>
					
			</td>
			<td width="80">
				<? echo create_drop_down( "cboBodyPart_".$i, 70, $body_part,"", 1, "-- Select --",$body_part_id, "",1,'','','','','','',"cboBodyPart[]"); ?>
			</td>
			<td width="130" class="word_wrap_break">
				<? echo $color_arr[$color_id]; ?>
				<input type="hidden" id="txtcolor_<? echo $i; ?>" name="txtcolor_<? echo $i; ?>" class="text_boxes" value="<? echo $color_arr[$color_id]; ?>"  style="width:87px" readonly/>
			</td>
            <td width="60">
            	<? echo $size_arr[$size_id]; ?>
            	<input type="hidden" id="txtsize_<? echo $i; ?>" name="txtsize_<? echo $i; ?>" class="text_boxes txt_size" value="<? echo $size_arr[$size_id]; ?>"  style="width:47px" readonly/>
            </td>
            <td width="80">
            	<? echo $row[csf("bundle_no")]; ?>
            	<input name="txtBundleNo[]" id="txtBundleNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("bundle_no")]; ?>"  style="width:67px" readonly  title="<? echo $row[csf('barcode_no')]; ?>" />
            </td>
            <td width="80" align="right">
            	<? echo $row[csf("bundle_qty")]; ?>
            	<input name="txtBundleQty[]" id="txtBundleQty_<? echo $i; ?>" type="hidden" class="text_boxes_numeric" value="<? echo $row[csf("bundle_qty")]; ?>" readonly />
            	<input name="txtProdQty[]" id="txtProdQty_<? echo $i; ?>" type="hidden" class="text_boxes_numeric" value="" onKeyUp="fnc_total_calculate ();" />
            </td>
        </tr>
    <?
    $i++;
    }
	
	
	exit();
}
if($action=="append_new_item")
{
	$exdata=explode("**",$data);
	$type=$exdata[2];
	if($type==2){
		
	    $bundle = explode(",", $exdata[0]);
	    $total_row =$exdata[3];
	    //$mst_id = explode(",", $ex_data[2]);
	    $bundle_nos = "'" . implode("','", $bundle) . "'";
	    //$vscan=$ex_data[4];
	    //$source_cond=$ex_data[5];

		$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond=""; $scnbundle_nos_cond=""; $cutbundle_nos_cond="";
		if($db_type==2 && $bundle_count>400)
		{
			//$bundle_nos_cond=" and (";
			//$scnbundle_nos_cond=" and (";
			$cutbundle_nos_cond=" and (";
			$bundleArr=array_chunk(explode(",",$bundle_nos),399);
			foreach($bundleArr as $bundleNos)
			{
				$bundleNos=implode(",",$bundleNos);
				//$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
				//$scnbundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
				$cutbundle_nos_cond.=" d.barcode_no in($bundleNos) or ";
			}
			/*$bundle_nos_cond=chop($bundle_nos_cond,'or ');
			$bundle_nos_cond.=")";

			$scnbundle_nos_cond=chop($scnbundle_nos_cond,'or ');
			$scnbundle_nos_cond.=")";*/

			$cutbundle_nos_cond=chop($cutbundle_nos_cond,'or ');
			$cutbundle_nos_cond.=")";
		}
		else
		{
			//$bundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
			//$scnbundle_nos_cond=" and b.barcode_no in ($bundle_nos)";
			//$cutbundle_nos_cond=" and barcode_no in ($bundle_nos)";

			$cutbundle_nos_cond=" and d.barcode_no in ($bundle_nos)";
		}
	}else{
		$rcv_id =$exdata[0];
		$bundle_dtls_id =$exdata[1];
		$total_row =$exdata[3];
		$cutbundle_nos_cond=" and a.id=$rcv_id and d.id=$bundle_dtls_id ";
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
	
	//echo $sql; 

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
	 	$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['embellishment_job']=$row[csf('embellishment_job')];
	}

	//$sql_bundle="SELECT a.id as bundl_mst_id, a.company_id, a.po_id, a.order_breakdown_id, a.item_rcv_dtls_id, a.pcs_per_bundle, a.no_of_bundle, a.bundle_qty, a.remarks,b.id, b.mst_id, b.item_rcv_id, b.pcs_per_bundle as dtls_pcs_per_bundle, b.bundle_qty as dtls_bundle_qty, b.barcode_no, b.barcode_id from prnting_bundle_mst a, prnting_bundle_dtls b where a.id=b.mst_id and b.item_rcv_id=$update_id and a.item_rcv_dtls_id=$itemRcvDtlsId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id ASC";

	$sql="SELECT c.id as bundl_mst_id, c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.pcs_per_bundle, c.no_of_bundle, c.bundle_qty, c.remarks,c.bundle_num_prefix_no,d.id as bundle_dtls_id, d.mst_id, d.item_rcv_id, d.pcs_per_bundle, d.bundle_qty, d.barcode_no, d.barcode_id, d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id as rcv_dtls_id, a.embl_job_no,a.party_id,a.location_id,a.id as rcv_id,a.subcon_date,a.sys_no, $insert_date_cond as year
	from prnting_bundle_mst c, prnting_bundle_dtls d, sub_material_dtls b, sub_material_mst a  where  c.id = d.mst_id and c.item_rcv_dtls_id = b.id and d.item_rcv_id = b.mst_id and d.item_rcv_id = a.id and a.id = b.mst_id $cutbundle_nos_cond and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 
	group by c.id , c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.pcs_per_bundle, c.no_of_bundle, c.bundle_qty, c.remarks,c.bundle_num_prefix_no,d.id , d.mst_id, d.item_rcv_id, d.pcs_per_bundle, d.bundle_qty, d.barcode_no, d.barcode_id , b.job_dtls_id, d.bundle_no, b.job_break_id, b.buyer_po_id, b.id, a.embl_job_no,a.party_id,a.location_id,a.id ,a.subcon_date,a.sys_no, a.insert_date order by b.id";
	/*$bdlArray =sql_select($sql_bundle);
	$noOf_bundle=0; $pcsPer_bundle=0; $bundleQty=0; $totalType=1; $bundle_chk_arr=array();
	if(count($bdlArray)>0){
		foreach ($bdlArray as $row) 
		{
			$noOf_bundle++;
		 	$bundle_arr[$row[csf('bundl_mst_id')]][$row[csf('pcs_per_bundle')]][$row[csf('no_of_bundle')]][$row[csf('bundle_qty')]][$row[csf('remarks')]].=$row[csf('id')].'_'.$row[csf('dtls_pcs_per_bundle')].'_'.$row[csf('dtls_bundle_qty')].'_'.$row[csf('barcode_no')].'_'.$row[csf('barcode_id')].'#';
		 	if (!in_array($row[csf('bundl_mst_id')], $bundle_chk_arr)){
		 		$pcsPer_bundle +=$row[csf('pcs_per_bundle')];
		 		$totalType ++;
		 	}
		 	$bundle_chk_arr[$row[csf('bundl_mst_id')]]=$row[csf('bundl_mst_id')];
		 	$bundleQty +=$row[csf('dtls_bundle_qty')];
		}
	}*/
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
		$embellishment_job=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['embellishment_job'];
		$checkBox_check="checked";
		?>
        <tr>
        	<td width="50" align="center">
        		<? echo $i; ?>
                <input type="checkbox" name="barcodeChkbox[]" id="barcodeChkbox_<? echo $i; ?>" value="" <? echo $checkBox_check; ?> />
                <input type="hidden" name="woID[]" id="woID_<? echo $i; ?>" value="<? echo $wo_id; ?>"  >
	            <input type="hidden" name="woDtlsID[]" id="woDtlsID_<? echo $i; ?>" value="<? echo $row[csf("job_dtls_id")]; ?>"  >
	            <input type="hidden" name="woBreakID[]" id="woBreakID_<? echo $i; ?>" value="<? echo $row[csf("job_break_id")]; ?>"  >
	            <input type="hidden" name="rcvID[]" id="rcvID_<? echo $i; ?>" value="<? echo $row[csf("rcv_id")]; ?>"  >
	            <input type="hidden" name="rcvDtlsID[]" id="rcvDtlsID_<? echo $i; ?>" value="<? echo $row[csf("rcv_dtls_id")]; ?>"  >
	            <input type="hidden" name="bundleMstID[]" id="bundleMstID_<? echo $i; ?>" value="<? echo $row[csf("bundl_mst_id")]; ?>"  >
	            <input type="hidden" name="bundleDtlsID[]" id="bundleDtlsID_<? echo $i; ?>" value="<? echo $row[csf("bundle_dtls_id")]; ?>"  >
	            <input type="hidden" name="updateIdDtls[]" id="updatedtlsid_<? echo $i; ?>" value=""  >
            </td>
            
            <td width="90">
            	<? echo $row[csf("barcode_no")]; ?>
            	<input name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("barcode_no")]; ?>" style="width:90px" readonly />
            </td>
            <td width="100">
            	<? 
				echo create_drop_down( "cboCompanyId_".$i, 90, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",$row[csf("company_id")], "",1,'','','','','','',"cboCompanyId[]"); 
				?>
			</td>
			
			<td width="100">
				<?
				echo create_drop_down( "cboLocationId_".$i, 90, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --",$row[csf("location_id")], "",1,'','','','','','',"cboLocationId[]"); 
			?>
				
			</td>
			<td width="60">
				<? echo create_drop_down( "cboWithinGroup_".$i, 50, $yes_no,"", 1, "-- Select --",$within_group, "",1,'','','','','','',"cboWithinGroup[]"); 
				?>
			</td>
			<td width="100">
				<? echo create_drop_down( "cboPartyId_".$i, 90, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Customer --",$row[csf("party_id")], "",1,'','','','','','',"cboPartyId[]"); 
				?>
			</td>
            <td width="100">
            	<? echo $buyer_buyer; ?>
            	<input name="txtCustBuyer[]" id="txtCustBuyer_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $buyer_buyer; ?>"  style="width:87px" readonly />
            </td>
            <td width="60">
            	<input name="txtRecDate[]" id="txtRecDate_<? echo $i; ?>" type="text" class="datepicker" value="<? echo change_date_format($row[csf("subcon_date")]); ?>" disabled style="width:55px" />
            </td>
            <td width="60">
            	<input name="txtIssueCh[]" id="txtIssueCh_<? echo $i; ?>" type="text" class="text_boxes" value=""  style="width:47px"  />
            </td>
            <td width="100">
            	<? echo $embellishment_job; ?>
            	<input name="txtEmbJob[]" id="txtEmbJob_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $embellishment_job; ?>"  style="width:107px" readonly />
            </td>
            <td width="140" class="word_wrap_break">
            	<? echo $order_no; ?>
            	<input name="txtOrder_<? echo $i; ?>" id="txtOrder_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $order_no; ?>"  style="width:87px" readonly />
            </td>
            <td width="140" class="word_wrap_break">
            	<? echo $style; ?>
            	<input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:87px" readonly />
            </td>
            <td width="60">
            	<? 
            	echo create_drop_down( "cboProcessName_<? echo $i; ?>", 80, $emblishment_name_array,"", 1, "--Select--",$main_process_id, "",1,'','','','','','',"cboEmbType[]"); ?>
            </td>
			<td width="60">
				<? 
				if($main_process_id==1) $emb_type=$emblishment_print_type;
				else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
				else if($main_process_id==3) $emb_type=$emblishment_wash_type;
				else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
				else if($main_process_id==5) $emb_type=$emblishment_gmts_type;
				echo create_drop_down( "cboEmbType_".$i, 60, $emb_type,"", 1, "-- Select --",$embl_type_id, "",1,'','','','','','',"cboEmbType[]"); 
				?>
					
			</td>
			<td width="80">
				<? echo create_drop_down( "cboBodyPart_".$i, 70, $body_part,"", 1, "-- Select --",$body_part_id, "",1,'','','','','','',"cboBodyPart[]"); ?>
			</td>
			<td width="130" class="word_wrap_break">
				<? echo $color_arr[$color_id]; ?>
				<input type="hidden" id="txtcolor_<? echo $i; ?>" name="txtcolor_<? echo $i; ?>" class="text_boxes" value="<? echo $color_arr[$color_id]; ?>"  style="width:87px" readonly/>
			</td>
            <td width="60">
            	<? echo $size_arr[$size_id]; ?>
            	<input type="hidden" id="txtsize_<? echo $i; ?>" name="txtsize_<? echo $i; ?>" class="text_boxes txt_size" value="<? echo $size_arr[$size_id]; ?>"  style="width:47px" readonly/>
            </td>
            <td width="80">
            	<? echo $row[csf("bundle_no")]; ?>
            	<input name="txtBundleNo[]" id="txtBundleNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("bundle_no")]; ?>"  style="width:67px" readonly  title="<? echo $row[csf('barcode_no')]; ?>" />
            </td>
            <td width="80" align="right">
            	<? echo $row[csf("bundle_qty")]; ?>
            	<input name="txtBundleQty[]" id="txtBundleQty_<? echo $i; ?>" type="hidden" class="text_boxes_numeric" value="<? echo $row[csf("bundle_qty")]; ?>" readonly />
            	<input name="txtProdQty[]" id="txtProdQty_<? echo $i; ?>" type="hidden" class="text_boxes_numeric" value="" onKeyUp="fnc_total_calculate ();" />
            </td>
        </tr>
    <?
    $i++;
    }
	
	
	exit();
}
?>