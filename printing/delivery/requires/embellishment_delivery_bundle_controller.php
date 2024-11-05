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

if($action=="set_print_button")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=15 and report_id=276 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('$print_report_format');\n"; 
die;
}

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
		echo "1**Already Delivery found, System id " . $row[csf('issue_number')] . ".";
	}

	//echo "SELECT  a.id from subcon_ord_mst a, sub_material_mst b, prnting_bundle_dtls c where a.embellishment_job=b.embl_job_no and b.id=c.item_rcv_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0  and c.status_active =1 and c.is_deleted =0 and c.bundle_no is not null and c.barcode_no='$data[0]'"; die;
	$sql_barcode_wo= sql_select("SELECT  a.id from subcon_ord_mst a, sub_material_mst b, prnting_bundle_dtls c where a.embellishment_job=b.embl_job_no and b.id=c.item_rcv_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0  and c.status_active =1 and c.is_deleted =0 and c.bundle_no is not null and c.barcode_no='$data[0]'"); 

	foreach ($sql_barcode_wo as $row) 
	{
		echo "2**".$row[csf('id')];
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
	
	if($within_group==1)
	{ 

	 	
	
	  if ($data[0]!=0) $company=" and b.serving_company='$data[0]'"; else { echo "Please Select Company First."; die; }
		if ($data[4]=='' && $data[5]=='' && $search_str=='' && $data[2]=="" &&  $data[3]==""){
		echo "Please Select Date Range"; die; 
		} 
		if ($data[1]!=0) $buyer_cond=" and b.company_id='$data[1]'"; else $buyer_cond="";
		//if ($data[1]!=0) $buyer_cond=" and a.party_id='$data[1]'"; else $buyer_cond="";
		if ($location!=0) $location_cond=" and b.location='$location'"; else $location_cond="";
		if ($within_group!=0) $withinGroup=" and t.within_group='$within_group'"; else $withinGroup="";
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
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.embellishment_job=b.job_no_mst $search_com_cond $buyer_po_cond $buyer_style_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}
	
	if ( $spo_ids!="") $spo_idsCond=" and b.job_dtls_id in ($spo_ids)"; else $spo_idsCond="";
	//echo $sql; 

	/*$sql="SELECT c.id as bundl_mst_id, c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.no_of_bundle, c.remarks,c.bundle_num_prefix_no,d.id as bundle_dtls_id, d.mst_id, d.item_rcv_id, d.bundle_qty, d.barcode_no, d.barcode_id, d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id as rcv_dtls_id, a.embl_job_no,a.party_id,a.location_id,a.id as rcv_id,a.subcon_date,a.sys_no, $insert_date_cond as year, f.issue_date,e.id as qc_dtls_id, e.issue_dtls_id,e.challan_no, e.quantity
	from sub_material_mst a , sub_material_dtls b ,prnting_bundle_mst c, prnting_bundle_dtls d , printing_bundle_issue_dtls e , printing_bundle_issue_mst f  where  f.entry_form=498 and c.id = d.mst_id and c.item_rcv_dtls_id = b.id and d.item_rcv_id = b.mst_id and d.item_rcv_id = a.id and a.id = b.mst_id and a.id=e.rcv_id and b.id=e.rcv_dtls_id and c.id=e.bundle_mst_id and d.id=e.bundle_dtls_id and e.mst_id=f.id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond $spo_idsCond $po_idsCond $location_cond 
	group by c.id , c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.no_of_bundle, c.remarks,c.bundle_num_prefix_no,d.id , d.mst_id, d.item_rcv_id, d.bundle_qty, d.barcode_no, d.barcode_id , d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id, a.embl_job_no,a.party_id,a.location_id,a.id ,a.subcon_date,a.sys_no, a.insert_date, f.issue_date,e.id,e.issue_dtls_id, e.challan_no, e.quantity order by b.id";*/

	//and d.id not in (select barcode_no from tmp_barcode_no where userid=$user_id and entry_form=499) 
	//,tmp_poid g  where g.poid=e.id and 
	
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
	
	$buyer_arr=return_library_array( "select id,buyer_name from  lib_buyer", "id","buyer_name"  );
	
	    $sql="SELECT c.id as bundle_dtls_id,d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, $year_field as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty,c.is_rescan, e.po_number,b.company_id,b.location,b.production_source,b.serving_company,b.embel_name, b.embel_type,b.wo_order_no,b.challan_no,b.wo_order_id,g.id as job_dtls_id,b.id as bundl_mst_id,e.job_id,g.buyer_po_id,e.grouping,m.recv_date,n.quantity  as bundle_qty,m.id as rcv_id,n.id as rcv_dtls_id,t.issue_date,k.id as qc_dtls_id ,k.production_dtls_id,k.issue_dtls_id from pro_garments_production_mst b,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f,subcon_ord_dtls g,printing_bundle_receive_mst m,printing_bundle_receive_dtls n,printing_bundle_issue_dtls k,printing_bundle_issue_mst t  where  b.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and  d.job_id=f.id and b.wo_order_no=g.order_no  and c.production_type=2 and  n.id=k.rcv_dtls_id and n.bundle_dtls_id=k.bundle_dtls_id and m.id=n.mst_id and b.id=n.bundle_mst_id  and b.id=k.bundle_mst_id and k.entry_form=498 and c.id=n.bundle_dtls_id and c.id=k.bundle_dtls_id and t.id=k.mst_id and g.id=n.job_dtls_id and c.status_active=1 and c.is_deleted=0 and n.status_active=1 and n.is_deleted=0 and k.status_active=1 and k.is_deleted=0 and t.status_active=1 and t.is_deleted=0  and n.print_issue_status=1 and k.print_production_status=0  $company $location_cond $withinGroup  $recieve_date $challan_no_cond $job_cond $rec_id_cond $search_com_cond $buyer_po_cond $buyer_style_cond $year_cond group by  c.id,d.id, e.id, f.job_no_prefix_num, f.insert_date, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty,c.is_rescan, e.po_number,b.company_id,b.location,b.production_source,b.serving_company,b.embel_name, b.embel_type,b.wo_order_no,b.challan_no,b.wo_order_id,g.id,b.id,e.job_id,g.buyer_po_id,e.grouping,m.recv_date,n.quantity,m.id,n.id,t.issue_date,k.id,k.issue_dtls_id,k.production_dtls_id order by c.cut_no,length(c.bundle_no) asc, c.bundle_no asc "; 
	$result = sql_select($sql);

	if(!empty($result))
	{
		foreach($result as $row)
		{
			$job_dtls_id_arr[$row[csf('job_dtls_id')]] =$row[csf('job_dtls_id')];
			$bundle_dtls_id_arr[$row[csf('bundle_dtls_id')]] =$row[csf('bundle_dtls_id')];
		}
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
		unset($dataArray);
	}


	$job_dtls_id_cond_2 = " and wo_dtls_id in (".implode(",",$job_dtls_id_arr).")";
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

		/* $sql_production=sql_select("SELECT id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id from printing_bundle_issue_dtls where entry_form=497 and status_active=1 and is_deleted=0 $bundle_dtls_id_cond");
		

		foreach ($sql_production as $val) {
			$productioned_bundle_barcode[$val[csf('bundle_dtls_id')]] = $val[csf('bundle_dtls_id')];
		} */

		$sql_delivery="SELECT id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id
			from printing_bundle_issue_dtls where entry_form=499 and status_active=1 and is_deleted=0 order by id ASC";
		$delivery_result =sql_select($sql_delivery);
		foreach ($delivery_result as $val) {
			$delivered_bundle_barcode[$val[csf('bundle_dtls_id')]] = $val[csf('bundle_dtls_id')];
		}

		$sql_production="SELECT id as production_dtls_id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id
			from printing_bundle_issue_dtls where  entry_form=497 and status_active=1 and is_deleted=0 $bundle_dtls_id_cond";
	
		$production_result =sql_select($sql_production);
		foreach($production_result as $row)
		{
			$production_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['quantity']+=$row[csf('quantity')];
			$production_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['production_dtls_id'].=$row[csf('production_dtls_id')].',';
		}
		
		
		$sql_qc="SELECT id as qc_dtls_id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id,production_dtls_id from printing_bundle_issue_dtls where  entry_form=498 $spo_idsCondProd and status_active=1 and is_deleted=0 $job_dtls_id_cond_2 order by id ASC";
	
		$qc_result =sql_select($sql_qc); $issue_dtls_id_arr=array();
		foreach($qc_result as $row)
		{
			$qc_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('production_dtls_id')]]['quantity']+=$row[csf('quantity')];
			$qc_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('production_dtls_id')]]['qc_dtls_id'].=$row[csf('qc_dtls_id')].',';
			$issue_dtls_id_arr[$row[csf('issue_dtls_id')]]=$row[csf('issue_dtls_id')];
		}


		unset($sql_production);
	}
	?>
    <div>
    	<table cellpadding="0" cellspacing="2" border="1" width="1810" id="details_tbl" rules="all" align="left">
            <thead class="form_table_header">
                <tr align="center" >
                    <th width="50" >SL</th>
                    <th width="100" >Barcode No</th>
                    <th width="100" >Comapany</th>
                    <th width="100" >Location</th>
                    <th width="60" >Within Group</th>
                    <th width="100" >Customer</th>
                    <th width="100" >Cus. Buyer</th>
                    <th width="60" >Issue Date</th>
                    <th width="60">Issue Ch No</th>
                    <th width="140">Order No</th>
                    <th width="140">Cust. Style Ref.</th>
                    <th width="80">Embl. Name</th>
                    <th width="60">Embl. Type</th>
                    <th width="80">Body Part</th>
                    <th width="130">Color</th>
                    <th width="60">Size</th>
                    <th width="80">Bundle NO</th>
                    <th width="60">Bundle Qty.</th>
                    <th width="60">Prod. Qty.</th>
                    <th width="60">QC Qty.</th>
                    <th width="130">RMK</th>
                </tr>
            </thead>
		</table>
        <div style="width:1830px; max-height:270px; overflow-y:scroll;" >
			<table cellpadding="0" cellspacing="2"  width="1810" id="table_body" class="rpt_table" rules="all" align="left">
	         	<tbody id="rec_issue_table">
	         	<?
					$i=1;
		            foreach($result as $row)
		            {
						if($delivered_bundle_barcode[$row[csf('bundle_dtls_id')]] =="")
						{
							if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$style=$job_arr[$row[csf('job_dtls_id')]]['style'];
							//$buyer_buyer=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['buyer_buyer'];
							$buyer_buyer=$buyer_arr[$row[csf('buyer_name')]];
							$order_no=$job_arr[$row[csf('job_dtls_id')]]['order_no'];
							$main_process_id=$job_arr[$row[csf('job_dtls_id')]]['main_process_id'];
							$embl_type_id=$job_arr[$row[csf('job_dtls_id')]]['embl_type'];
							$body_part_id=$job_arr[$row[csf('job_dtls_id')]]['body_part'];
							//$color_id=$job_arr[$row[csf('job_dtls_id')]]['color_id'];
							//$size_id=$job_arr[$row[csf('job_dtls_id')]]['size_id'];
							$color_id=$row[csf('color_number_id')];
							$size_id=$row[csf('size_number_id')];
							$wo_id=$job_arr[$row[csf('job_dtls_id')]]['wo_id'];
							$within_group=$job_arr[$row[csf('job_dtls_id')]]['within_group'];
							
							$prod_qty=$production_item_arr[$row[csf('job_dtls_id')]][$row[csf('colorsizeid')]][$row[csf('issue_dtls_id')]]['quantity'];
							//$production_dtls_ids=$production_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('issue_dtls_id')]]['production_dtls_id'];

							//$qc_qty=$qc_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('issue_dtls_id')]]['quantity'];
							//$qc_dtls_ids=$qc_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('issue_dtls_id')]]['qc_dtls_id'];

							//$productionDtlsIds=implode(",",array_unique(explode(",",(chop($production_dtls_ids,',')))));
							//$qcDtlsIds=implode(",",array_unique(explode(",",(chop($qc_dtls_ids,',')))));
							
							//$prod_qty=$production_item_arr[$row[csf('job_dtls_id')]][$row[csf('colorsizeid')]][$row[csf('issue_dtls_id')]]['quantity'];
							$production_dtls_ids=$production_item_arr[$row[csf('job_dtls_id')]][$row[csf('colorsizeid')]][$row[csf('issue_dtls_id')]]['production_dtls_id'];
							$qc_qty=$qc_item_arr[$row[csf('job_dtls_id')]][$row[csf('colorsizeid')]][$row[csf('production_dtls_id')]]['quantity'];
							$qc_dtls_ids=$qc_item_arr[$row[csf('job_dtls_id')]][$row[csf('colorsizeid')]][$row[csf('production_dtls_id')]]['qc_dtls_id'];

							//$productionDtlsIds=$row[csf("production_dtls_id")];//implode(",",array_unique(explode(",",(chop($production_dtls_ids,',')))));
							//$qcDtlsIds=$row[csf("qc_dtls_id")];//implode(",",array_unique(explode(",",(chop($qc_dtls_ids,',')))));
							
							

							$productionDtlsIds = $row[csf('production_dtls_id')];
							$qcDtlsIds = $row[csf('qc_dtls_id')];
							//$qc_qty=$row[csf('bundle_qty')];

							$checkBox_check="checked";
							?>
							<tr>
								<td width="50" align="center">
								<? echo $i; ?>
									<input type="checkbox" name="barcodeChkbox[]" id="barcodeChkbox_<? echo $i; ?>" value="" <? echo $checkBox_check; ?> />
									<input type="hidden" name="woID[]" id="woID_<? echo $i; ?>" value="<? echo $wo_id; ?>"  >
									<input type="hidden" name="woDtlsID[]" id="woDtlsID_<? echo $i; ?>" value="<? echo $row[csf("job_dtls_id")]; ?>"  >
									<input type="hidden" name="woBreakID[]" id="woBreakID_<? echo $i; ?>" value="<? echo $row[csf("colorsizeid")]; ?>"  >
									<input type="hidden" name="rcvID[]" id="rcvID_<? echo $i; ?>" value="<? echo $row[csf("rcv_id")]; ?>"  >
									<input type="hidden" name="rcvDtlsID[]" id="rcvDtlsID_<? echo $i; ?>" value="<? echo $row[csf("rcv_dtls_id")]; ?>"  >
									<input type="hidden" name="bundleMstID[]" id="bundleMstID_<? echo $i; ?>" value="<? echo $row[csf("bundl_mst_id")]; ?>"  >
									<input type="hidden" name="bundleDtlsID[]" id="bundleDtlsID_<? echo $i; ?>" value="<? echo $row[csf("bundle_dtls_id")]; ?>"  >
									<input type="hidden" name="issueDtlsID[]" id="issueDtlsID_<? echo $i; ?>" value="<? echo $row[csf("issue_dtls_id")]; ?>"  >
									<input type="hidden" name="productionDtlsIds[]" id="productionDtlsIds_<? echo $i; ?>" value="<? echo $productionDtlsIds; ?>"  >
									<input type="hidden" name="qcDtlsIds[]" id="qcDtlsIds_<? echo $i; ?>" value="<? echo $qcDtlsIds; ?>"  >
									<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value=""  >
								</td>

								<td width="100">
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
									<? echo create_drop_down( "cboWithinGroup_".$i, 55, $yes_no,"", 1, "-- Select --",$within_group, "",1,'','','','','','',"cboWithinGroup[]"); 
									?>
								</td>
								<td width="100">
									<? echo create_drop_down( "cboPartyId_".$i, 90, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Customer --",$row[csf("company_id")], "",1,'','','','','','',"cboPartyId[]"); ?>
								</td>
								<td width="100">
								<? echo $buyer_buyer; ?>
									<input name="txtCustBuyer[]" id="txtCustBuyer_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $buyer_buyer; ?>"  style="width:87px" readonly />
								</td>
								<td width="60">
								<? echo change_date_format($row[csf("issue_date")]); ?>
									<input name="txtIssueDate[]" id="txtIssueDate_<? echo $i; ?>" type="hidden" class="datepicker" value="<? echo change_date_format($row[csf("issue_date")]); ?>"  style="width:87px" disabled />
								</td> 
								<td width="60">
								<? echo $row[csf("challan_no")]; ?>
									<input name="txtIssueCh[]" id="txtIssueCh_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("challan_no")]; ?>"  style="width:47px"  readonly  />
								</td>
								<td width="140">
								<? echo $order_no; ?>
									<input name="txtOrder_<? echo $i; ?>" id="txtOrder_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $order_no; ?>"  style="width:47px" readonly />
								</td>
								<td width="140">
								<? echo $style; ?>
									<input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:47px" readonly />
								</td>
								<td width="80">
									<? echo create_drop_down( "cboProcessName_<? echo $i; ?>", 70, $emblishment_name_array,"", 1, "--Select--",$main_process_id, "",1,'','','','','','',"cboEmbType[]"); ?>
								</td>
								<td width="60">
									<?
									if($main_process_id==1) $emb_type=$emblishment_print_type;
									else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
									else if($main_process_id==3) $emb_type=$emblishment_wash_type;
									else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
									else if($main_process_id==5) $emb_type=$emblishment_gmts_type; 
									echo create_drop_down( "cboEmbType_".$i, 55, $emb_type,"", 1, "-- Select --",$embl_type_id, "",1,'','','','','','',"cboEmbType[]"); ?>
								</td>
								<td width="80">
									<? echo create_drop_down( "cboBodyPart_".$i, 70, $body_part,"", 1, "-- Select --",$body_part_id, "",1,'','','','','','',"cboBodyPart[]"); ?>
								</td>
								<td width="130">
								<? echo $color_arr[$color_id]; ?>
									<input type="hidden" id="txtcolor_<? echo $i; ?>" name="txtcolor_<? echo $i; ?>" class="text_boxes" value="<? echo $color_arr[$color_id]; ?>"  style="width:87px" readonly/>
								</td>
								<td width="60">
								<? echo $size_arr[$size_id]; ?>
									<input type="hidden" id="txtsize_<? echo $i; ?>" name="txtsize_<? echo $i; ?>" class="text_boxes txt_size" value="<? echo $size_arr[$size_id]; ?>"  style="width:47px" readonly/>
								</td>
								<td width="80">
								<? echo $row[csf("bundle_no")]; ?>
									<input name="txtBundleNo[]" id="txtBundleNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("bundle_no")]; ?>"  style="width:67px" readonly />
								</td>
								<td width="60" align="right">
									<input name="txtBundleQty[]" id="txtBundleQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $row[csf("bundle_qty")]; ?>"  style="width:47px" readonly />
								</td>
								<td width="60">
									<input name="txtProdQty[]" id="txtProdQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $prod_qty; ?>" style="width:47px" readonly />
									<input name="txtRejQty[]" id="txtRejQty_<? echo $i; ?>" type="hidden" class="text_boxes_numeric" value="<? ?>"  placeholder="Display"   readonly style="width:47px"  />
								</td> 
								<td width="60">
									<input name="txtQcQty[]" id="txtQcQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $qc_qty; ?>" placeholder="Display" style="width:47px" readonly />
								</td>
								<td width="130">
									<input name="txtRemark[]" id="txtRemark_<? echo $i; ?>" type="text" class="text_boxes" value="" placeholder="Write" style="width:120px"/>
								</td>
							</tr>
							<?
							$i++;
							$totBndlQty+=$row[csf("bundle_qty")];
							$totProdQty+=$prod_qty;
							$totQcQty+=$qc_qty;
						}
	                }
	                ?>
	            </tbody>
			</table>
	    </div>
		<table cellpadding="0" cellspacing="2" border="1" width="1810" id="tbl_footer" rules="all" align="left">
			<tfoot>
				<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
					<td width="50" align="center">
						All <input form="form_all" type="checkbox" name="check_all" id="check_all" value=""  onclick="fnCheckUnCheckAll(this.checked)"/>
					</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="60">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="60">&nbsp;</td>
					<td width="60">&nbsp;</td>
					<td width="140">&nbsp;</td>
					<td width="140">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="60">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="130">&nbsp;</td>
					<td width="60">&nbsp;</td>
					<td width="80">Total:</td>
					<td width="60">
						<input name="txtTotBundleqty" id="txtTotBundleqty" class="text_boxes_numeric" type="text" value="<? echo $totBndlQty; ?>"  style="width:47px" readonly />
					</td>
					<td width="60">
						<input name="txtTotProdqty" id="txtTotProdqty" class="text_boxes_numeric" type="text" value="<? echo $totProdQty; ?>" style="width:47px" readonly />
						<input name="txtTotRejqty" id="txtTotRejqty" class="text_boxes_numeric" type="hidden" style="width:47px"  readonly />
					</td>

					<td width="60">
						<input name="txtTotQcqty" id="txtTotQcqty" class="text_boxes_numeric" type="text" value="<? echo $totQcQty; ?>" style="width:47px"  readonly />
					</td>
					<td width="130">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
    </div>
    <script type="text/javascript">fnc_total_calculate();</script>
    <? 
	}
	if($within_group==2)
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
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.embellishment_job=b.job_no_mst $search_com_cond $buyer_po_cond $buyer_style_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}
	
	if ( $spo_ids!="") $spo_idsCond=" and b.job_dtls_id in ($spo_ids)"; else $spo_idsCond="";
	//echo $sql; 

	$sql="SELECT c.id as bundl_mst_id, c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.no_of_bundle, c.remarks,c.bundle_num_prefix_no,d.id as bundle_dtls_id, d.mst_id, d.item_rcv_id, d.bundle_qty, d.barcode_no, d.barcode_id, d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id as rcv_dtls_id, a.embl_job_no,a.party_id,a.location_id,a.id as rcv_id,a.subcon_date,a.sys_no, $insert_date_cond as year, f.issue_date,e.id as qc_dtls_id, e.issue_dtls_id,e.challan_no, e.quantity
	from sub_material_mst a , sub_material_dtls b ,prnting_bundle_mst c, prnting_bundle_dtls d , printing_bundle_issue_dtls e , printing_bundle_issue_mst f  where  f.entry_form=498 and c.id = d.mst_id and c.item_rcv_dtls_id = b.id and d.item_rcv_id = b.mst_id and d.item_rcv_id = a.id and a.id = b.mst_id and a.id=e.rcv_id and b.id=e.rcv_dtls_id and c.id=e.bundle_mst_id and d.id=e.bundle_dtls_id and e.mst_id=f.id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond $spo_idsCond $po_idsCond $location_cond 
	group by c.id , c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.no_of_bundle, c.remarks,c.bundle_num_prefix_no,d.id , d.mst_id, d.item_rcv_id, d.bundle_qty, d.barcode_no, d.barcode_id , d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id, a.embl_job_no,a.party_id,a.location_id,a.id ,a.subcon_date,a.sys_no, a.insert_date, f.issue_date,e.id,e.issue_dtls_id, e.challan_no, e.quantity order by b.id";

	//and d.id not in (select barcode_no from tmp_barcode_no where userid=$user_id and entry_form=499) 
	//,tmp_poid g  where g.poid=e.id and 
	$result = sql_select($sql);

	if(!empty($result))
	{
		foreach($result as $row)
		{
			$job_dtls_id_arr[$row[csf('job_dtls_id')]] =$row[csf('job_dtls_id')];
			$bundle_dtls_id_arr[$row[csf('bundle_dtls_id')]] =$row[csf('bundle_dtls_id')];
		}
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
		unset($dataArray);
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

		/* $sql_production=sql_select("SELECT id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id from printing_bundle_issue_dtls where entry_form=497 and status_active=1 and is_deleted=0 $bundle_dtls_id_cond");
		

		foreach ($sql_production as $val) {
			$productioned_bundle_barcode[$val[csf('bundle_dtls_id')]] = $val[csf('bundle_dtls_id')];
		} */

		$sql_delivery="SELECT id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id
			from printing_bundle_issue_dtls where entry_form=499 and status_active=1 and is_deleted=0 order by id ASC";
		$delivery_result =sql_select($sql_delivery);
		foreach ($delivery_result as $val) {
			$delivered_bundle_barcode[$val[csf('bundle_dtls_id')]] = $val[csf('bundle_dtls_id')];
		}

		$sql_production="SELECT id as production_dtls_id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id
			from printing_bundle_issue_dtls where  entry_form=497 and status_active=1 and is_deleted=0 $bundle_dtls_id_cond";
	
		$production_result =sql_select($sql_production);
		foreach($production_result as $row)
		{
			$production_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['quantity']+=$row[csf('quantity')];
			$production_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['production_dtls_id'].=$row[csf('production_dtls_id')].',';
		}

		unset($sql_production);
	}
	?>
    <div>
    	<table cellpadding="0" cellspacing="2" border="1" width="1810" id="details_tbl" rules="all" align="left">
            <thead class="form_table_header">
                <tr align="center" >
                    <th width="50" >SL</th>
                    <th width="100" >Barcode No</th>
                    <th width="100" >Comapany</th>
                    <th width="100" >Location</th>
                    <th width="60" >Within Group</th>
                    <th width="100" >Customer</th>
                    <th width="100" >Cus. Buyer</th>
                    <th width="60" >Issue Date</th>
                    <th width="60">Issue Ch No</th>
                    <th width="140">Order No</th>
                    <th width="140">Cust. Style Ref.</th>
                    <th width="80">Embl. Name</th>
                    <th width="60">Embl. Type</th>
                    <th width="80">Body Part</th>
                    <th width="130">Color</th>
                    <th width="60">Size</th>
                    <th width="80">Bundle NO</th>
                    <th width="60">Bundle Qty.</th>
                    <th width="60">Prod. Qty.</th>
                    <th width="60">QC Qty.</th>
                    <th width="130">RMK</th>
                </tr>
            </thead>
		</table>
        <div style="width:1830px; max-height:270px; overflow-y:scroll;" >
			<table cellpadding="0" cellspacing="2"  width="1810" id="table_body" class="rpt_table" rules="all" align="left">
	         	<tbody id="rec_issue_table">
	         	<?
					$i=1;
		            foreach($result as $row)
		            {
						if($delivered_bundle_barcode[$row[csf('bundle_dtls_id')]] =="")
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

							//$qc_qty=$qc_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('issue_dtls_id')]]['quantity'];
							//$qc_dtls_ids=$qc_item_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]][$row[csf('issue_dtls_id')]]['qc_dtls_id'];

							$productionDtlsIds=implode(",",array_unique(explode(",",(chop($production_dtls_ids,',')))));
							//$qcDtlsIds=implode(",",array_unique(explode(",",(chop($qc_dtls_ids,',')))));

							$qcDtlsIds = $row[csf('qc_dtls_id')];
							$qc_qty=$row[csf('quantity')];

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
									<input type="hidden" name="issueDtlsID[]" id="issueDtlsID_<? echo $i; ?>" value="<? echo $row[csf("issue_dtls_id")]; ?>"  >
									<input type="hidden" name="productionDtlsIds[]" id="productionDtlsIds_<? echo $i; ?>" value="<? echo $productionDtlsIds; ?>"  >
									<input type="hidden" name="qcDtlsIds[]" id="qcDtlsIds<? echo $i; ?>" value="<? echo $qcDtlsIds; ?>"  >
									<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value=""  >
								</td>

								<td width="100">
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
									<? echo create_drop_down( "cboWithinGroup_".$i, 55, $yes_no,"", 1, "-- Select --",2, "",1,'','','','','','',"cboWithinGroup[]"); 
									?>
								</td>
								<td width="100">
									<? echo create_drop_down( "cboPartyId_".$i, 90, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Customer --",$row[csf("party_id")], "",1,'','','','','','',"cboPartyId[]"); ?>
								</td>
								<td width="100">
								<? echo $buyer_buyer; ?>
									<input name="txtCustBuyer[]" id="txtCustBuyer_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $buyer_buyer; ?>"  style="width:87px" readonly />
								</td>
								<td width="60">
								<? echo change_date_format($row[csf("issue_date")]); ?>
									<input name="txtIssueDate[]" id="txtIssueDate_<? echo $i; ?>" type="hidden" class="datepicker" value="<? echo change_date_format($row[csf("issue_date")]); ?>"  style="width:87px" disabled />
								</td> 
								<td width="60">
								<? echo $row[csf("challan_no")]; ?>
									<input name="txtIssueCh[]" id="txtIssueCh_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("challan_no")]; ?>"  style="width:47px"  readonly  />
								</td>
								<td width="140">
								<? echo $order_no; ?>
									<input name="txtOrder_<? echo $i; ?>" id="txtOrder_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $order_no; ?>"  style="width:47px" readonly />
								</td>
								<td width="140">
								<? echo $style; ?>
									<input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:47px" readonly />
								</td>
								<td width="80">
									<? echo create_drop_down( "cboProcessName_<? echo $i; ?>", 70, $emblishment_name_array,"", 1, "--Select--",$main_process_id, "",1,'','','','','','',"cboEmbType[]"); ?>
								</td>
								<td width="60">
									<?
									if($main_process_id==1) $emb_type=$emblishment_print_type;
									else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
									else if($main_process_id==3) $emb_type=$emblishment_wash_type;
									else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
									else if($main_process_id==5) $emb_type=$emblishment_gmts_type; 
									echo create_drop_down( "cboEmbType_".$i, 55, $emb_type,"", 1, "-- Select --",$embl_type_id, "",1,'','','','','','',"cboEmbType[]"); ?>
								</td>
								<td width="80">
									<? echo create_drop_down( "cboBodyPart_".$i, 70, $body_part,"", 1, "-- Select --",$body_part_id, "",1,'','','','','','',"cboBodyPart[]"); ?>
								</td>
								<td width="130">
								<? echo $color_arr[$color_id]; ?>
									<input type="hidden" id="txtcolor_<? echo $i; ?>" name="txtcolor_<? echo $i; ?>" class="text_boxes" value="<? echo $color_arr[$color_id]; ?>"  style="width:87px" readonly/>
								</td>
								<td width="60">
								<? echo $size_arr[$size_id]; ?>
									<input type="hidden" id="txtsize_<? echo $i; ?>" name="txtsize_<? echo $i; ?>" class="text_boxes txt_size" value="<? echo $size_arr[$size_id]; ?>"  style="width:47px" readonly/>
								</td>
								<td width="80">
								<? echo $row[csf("bundle_no")]; ?>
									<input name="txtBundleNo[]" id="txtBundleNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("bundle_no")]; ?>"  style="width:67px" readonly />
								</td>
								<td width="60" align="right">
									<input name="txtBundleQty[]" id="txtBundleQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $row[csf("bundle_qty")]; ?>"  style="width:47px" readonly />
								</td>
								<td width="60">
									<input name="txtProdQty[]" id="txtProdQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $prod_qty; ?>" style="width:47px" readonly />
									<input name="txtRejQty[]" id="txtRejQty_<? echo $i; ?>" type="hidden" class="text_boxes_numeric" value="<??>"  placeholder="Display"   readonly style="width:47px"  />
								</td> 
								<td width="60">
									<input name="txtQcQty[]" id="txtQcQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $qc_qty; ?>" placeholder="Display" style="width:47px" readonly />
								</td>
								<td width="130">
									<input name="txtRemark[]" id="txtRemark_<? echo $i; ?>" type="text" class="text_boxes" value="" placeholder="Write" style="width:120px"/>
								</td>
							</tr>
							<?
							$i++;
							$totBndlQty+=$row[csf("bundle_qty")];
							$totProdQty+=$prod_qty;
							$totQcQty+=$qc_qty;
						}
	                }
	                ?>
	            </tbody>
			</table>
	    </div>
		<table cellpadding="0" cellspacing="2" border="1" width="1810" id="tbl_footer" rules="all" align="left">
			<tfoot>
				<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
					<td width="50" align="center">
						All <input form="form_all" type="checkbox" name="check_all" id="check_all" value=""  onclick="fnCheckUnCheckAll(this.checked)"/>
					</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="60">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="60">&nbsp;</td>
					<td width="60">&nbsp;</td>
					<td width="140">&nbsp;</td>
					<td width="140">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="60">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="130">&nbsp;</td>
					<td width="60">&nbsp;</td>
					<td width="80">Total:</td>
					<td width="60">
						<input name="txtTotBundleqty" id="txtTotBundleqty" class="text_boxes_numeric" type="text" value="<? echo $totBndlQty; ?>"  style="width:47px" readonly />
					</td>
					<td width="60">
						<input name="txtTotProdqty" id="txtTotProdqty" class="text_boxes_numeric" type="text" value="<? echo $totProdQty; ?>" style="width:47px" readonly />
						<input name="txtTotRejqty" id="txtTotRejqty" class="text_boxes_numeric" type="hidden" style="width:47px"  readonly />
					</td>

					<td width="60">
						<input name="txtTotQcqty" id="txtTotQcqty" class="text_boxes_numeric" type="text" value="<? echo $totQcQty; ?>" style="width:47px"  readonly />
					</td>
					<td width="130">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
    </div>
    <script type="text/javascript">fnc_total_calculate();</script>
    <? 
	}
    /* $r_id4=true; $r_id5=true;
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
	} */
	
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$barcode_dup_chk_arr=array();
	// Insert Start Here ----------------------------------------------------------

	$all_wo_id='';
	for($j=1; $j<=$total_row; $j++)
	{
		$woID			= "woID_".$j; 
		$all_wo_id.=str_replace("'", '', $$woID).',';
	}
	$all_wo_ids=implode(",",array_unique(explode(",",chop($all_wo_id,","))));

	$qc_bundle_arr = return_library_array("SELECT a.bundle_dtls_id from printing_bundle_issue_dtls a where a.entry_form=498 and a.status_active=1 and a.is_deleted=0 and a.wo_id in ($all_wo_ids) group by a.bundle_dtls_id","bundle_dtls_id","bundle_dtls_id");

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
		
		$field_array="id,company_id,entry_form , issue_num_prefix, issue_num_prefix_no, issue_number ,issue_date, remarks, delivery_point,within_group,floor_id,inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",".$cbo_company_name.",499,'".$new_iss_no[1]."','".$new_iss_no[2]."','".$new_iss_no[0]."',".$txt_delivery_date.",".$txt_remarks.",".$txt_delivery_point.",".$cbo_within_group.",".$cbo_floor.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		$issue_number=$new_iss_no[0];

		$field_array1="id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,issue_dtls_id,challan_no,quantity,production_dtls_id, reject_qty, defect_qty, remarks,barcode_no,qc_production_dtls_id, inserted_by, insert_date, status_active, is_deleted";
		
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
			$txtBarcodeNo	= "txtBarcodeNo_".$i;
			$qcDtlsIds	= "qcDtlsIds_".$i;
			if(!in_array(str_replace("'", '', $$bundleDtlsID), $barcode_dup_chk_arr, true)){
        		array_push( $barcode_dup_chk_arr, str_replace("'", '', $$bundleDtlsID));
    		}else{
    			echo "121**Duplicate Barcode No. Found"; die;
    		}
    		$issue_no=$prev_issue_arr[str_replace("'", '', $$issueDtlsID)];
    		if($issue_no){
    			echo "121**Delivery Found. System ID : $issue_no "; die;
    		}

			if($qc_bundle_arr[str_replace("'", '', $$bundleDtlsID)] ==""){
				echo "121**QC not Found"; oci_rollback($con); disconnect($con); die;
			}

			if ($add_commaa!=0) $data_array1 .=",";
			$data_array1.="(".$id1.",".$id.",".$$cboCompanyId.",499,".$$woID.",".$$woDtlsID.",".$$woBreakID.",".$$rcvID.",".$$rcvDtlsID.",".$$bundleMstID.",".$$bundleDtlsID.",".$$issueDtlsID.",".$$txtIssueCh.",".$$txtQcQty.",".$$productionDtlsIds.",".$$txtRejQty.",".$$hdnDtlsdata.",".$$txtRemark.",".$$txtBarcodeNo.",".$$qcDtlsIds.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			 $id_arr_trans[]=str_replace("'",'',$$qcDtlsIds);
			 $data_array_trans_update[str_replace("'",'',$$qcDtlsIds)] = explode("*",(1));
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
				$field_array_trans_update="print_production_status";
				$rID4=execute_query(bulk_update_sql_statement("printing_bundle_issue_dtls","id",$field_array_trans_update,$data_array_trans_update,$id_arr_trans),1);
				if($rID4==1 && $flag==1) $flag=1; else $flag=0;	
			}
		} 
		
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
		$field_array="issue_date*remarks*delivery_point*within_group*floor_id*updated_by*update_date";
		$data_array="".$txt_delivery_date."*".$txt_remarks."*".$txt_delivery_point."*".$cbo_within_group."*".$cbo_floor."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$field_array_status="updated_by*update_date*status_active*is_deleted";
		$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$field_arr_up="challan_no*quantity*reject_qty*defect_qty*remarks*updated_by*update_date*status_active*is_deleted";
		$field_array1="id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,issue_dtls_id,challan_no,quantity,production_dtls_id, reject_qty, defect_qty, remarks,barcode_no,qc_production_dtls_id,inserted_by, insert_date, status_active, is_deleted";
		
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
			$txtBarcodeNo	= "txtBarcodeNo_".$i;
			$qcDtlsIds	= "qcDtlsIds_".$i;
			if(!in_array(str_replace("'", '', $$bundleDtlsID), $barcode_dup_chk_arr, true)){
        		array_push( $barcode_dup_chk_arr, str_replace("'", '', $$bundleDtlsID));
    		}else{
    			echo "121**Duplicate Barcode No. Found"; die;
    		}
    		$issue_no=$prev_issue_arr[str_replace("'", '', $$issueDtlsID)];
    		if($issue_no){
    			echo "121**Delivery Found. System ID : $issue_no "; die;
    		}

			if($qc_bundle_arr[str_replace("'", '', $$bundleDtlsID)] ==""){
				echo "121**QC not Found"; oci_rollback($con); disconnect($con); die;
			}
			
			if(str_replace("'","",$$updatedtlsid)=="")
			{
				if ($add_commaa!=0) $data_array1 .=",";
				$data_array1.="(".$id1.",".$update_id.",".$$cboCompanyId.",499,".$$woID.",".$$woDtlsID.",".$$woBreakID.",".$$rcvID.",".$$rcvDtlsID.",".$$bundleMstID.",".$$bundleDtlsID.",".$$issueDtlsID.",".$$txtIssueCh.",".$$txtQcQty.",".$$productionDtlsIds.",".$$txtRejQty.",".$$hdnDtlsdata.",".$$txtRemark.",".$$txtBarcodeNo.",".$$qcDtlsIds.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_arr_trans[]=str_replace("'",'',$$qcDtlsIds);
				$data_array_trans_update[str_replace("'",'',$$qcDtlsIds)] = explode("*",(1));
			 
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
		if(str_replace("'",'',$cbo_within_group)==1)
		{
 			 
			
			if(count($id_arr_trans)>0)
			{
				$field_array_trans_update="print_production_status";
				$rID4=execute_query(bulk_update_sql_statement("printing_bundle_issue_dtls","id",$field_array_trans_update,$data_array_trans_update,$id_arr_trans),1);
				if($rID4==1 && $flag==1) $flag=1; else $flag=0;	
			}
		
		    $remove_id=chop($data_delete,",");
			// echo "10**".$data_delete; die;
		    if($remove_id!="")
			{
				$field_array_del_sales="print_production_status";
				$data_array_del_sales=0;
				$rID9=sql_multirow_update("printing_bundle_issue_dtls",$field_array_del_sales,$data_array_del_sales,"id",$remove_id,1);
				if($rID9==1 && $flag==1) $flag=1; else $flag=0;	
			}
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
	else if ($operation==2)   // delete Here
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
			$productionDtlsIds	= "qcDtlsIds_".$i;
			if(str_replace("'","",$$updatedtlsid)!=""){
				$updatedtlsDelId.=str_replace("'","",$$updatedtlsid).',';
				$bundleprodDtlsIDDelete.=str_replace("'","",$$productionDtlsIds).',';
				$id_arr_Delete[]=str_replace("'",'',$$updatedtlsid);
			}
		}

		$updatedtlsDelIds=implode(",",array_unique(explode(",",(chop($updatedtlsDelId,',')))));
		$bundleprodDtlsIDDeletes=implode(",",array_unique(explode(",",(chop($bundleprodDtlsIDDelete,',')))));
		
		if(count($id_arr_Delete)==count($sql_next_trans_res))
		{
				 $rID2=sql_update("printing_bundle_issue_mst",$field_array_status,$data_array_status,"id",$update_id,0); 
				if($rID2==1 && $flag==1) $flag=1; else $flag=0;	
				//echo "10**".$updatedtlsDelIds; die;
				$rID = sql_multirow_update("printing_bundle_issue_dtls", $field_array_status, $data_array_status, "id", $updatedtlsDelIds, 1);
				if($rID==1 && $flag==1) $flag=1; else $flag=0;	
				
				if(str_replace("'",'',$cbo_within_group)==1)
				{
					$field_array_del_sales="print_production_status";
					$data_array_del_sales=0;
					$rID9=sql_multirow_update("printing_bundle_issue_dtls",$field_array_del_sales,$data_array_del_sales,"id",$bundleprodDtlsIDDeletes,1);
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
			
			if(str_replace("'",'',$cbo_within_group)==1)
			{
				$field_array_del_sales="print_production_status";
				$data_array_del_sales=0;
				$rID9=sql_multirow_update("printing_bundle_issue_dtls",$field_array_del_sales,$data_array_del_sales,"id",$bundleprodDtlsIDDeletes,1);
				if($rID9==1 && $flag==1) $flag=1; else $flag=0;	
			} 
				
			
		}
		
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

if( $action == 'issue_item_details_update_bundle' )
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

	$sql_qc="SELECT id as qc_dtls_id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id,production_dtls_id
			from printing_bundle_issue_dtls where  entry_form=498 $spo_idsCondProd and status_active=1 and is_deleted=0 order by id ASC";
	
	$qc_result =sql_select($sql_qc);
	foreach($qc_result as $row)
	{
	 	$qc_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('production_dtls_id')]]['quantity']+=$row[csf('quantity')];
	 	$qc_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('production_dtls_id')]]['qc_dtls_id'].=$row[csf('qc_dtls_id')].',';
	}

	$sql_delivery="SELECT id as del_dtls_id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id,qc_production_dtls_id
			from printing_bundle_issue_dtls where  entry_form=499 and mst_id=$mst_id and status_active=1 and is_deleted=0 order by id ASC";
	
	$del_result =sql_select($sql_delivery); $issue_dtls_id_arr=array();
	foreach($del_result as $row_data)
	{
		$del_item_arr[$row_data[csf('wo_dtls_id')]][$row_data[csf('wo_break_id')]][$row_data[csf('qc_production_dtls_id')]]['updatedtlsid']=$row_data[csf('del_dtls_id')];
	 	$issue_dtls_id_arr[]=$row_data[csf('qc_production_dtls_id')];
	 	$wo_id.=$row_data[csf('wo_id')].',';
		$job_dtls_id .=$row_data[csf('wo_dtls_id')].',';
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
	//$wo_ids=implode(",",array_unique(explode(",",(chop($wo_id,',')))));
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

	/*$issue_sql="SELECT  e.id as issue_dtls_id , f.issue_date 
	from printing_bundle_issue_dtls e , printing_bundle_issue_mst f , tmp_poid g where g.poid=e.id and e.mst_id=f.id and e.entry_form=495 and e.status_active=1 and e.is_deleted=0";
	$issue_result =sql_select($issue_sql);
	foreach($issue_result as $row)
	{
		$issue_arr[$row[csf('issue_dtls_id')]]['issue_date']=change_date_format($row[csf('issue_date')]);
	}*/

/*	$sql="SELECT c.id as bundl_mst_id, c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.no_of_bundle, c.remarks,c.bundle_num_prefix_no,d.id as bundle_dtls_id, d.mst_id, d.item_rcv_id, d.bundle_qty, d.barcode_no, d.barcode_id, d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id as rcv_dtls_id, a.embl_job_no,a.party_id,a.location_id,a.id as rcv_id,a.subcon_date,a.sys_no, f.issue_date,e.id as issue_dtls_id,e.challan_no
	from sub_material_mst a , sub_material_dtls b ,prnting_bundle_mst c, prnting_bundle_dtls d , printing_bundle_issue_dtls e , printing_bundle_issue_mst f ,tmp_poid g where  g.poid=e.id and f.entry_form=495 and c.id = d.mst_id and c.item_rcv_dtls_id = b.id and d.item_rcv_id = b.mst_id and d.item_rcv_id = a.id and a.id = b.mst_id and a.id=e.rcv_id and b.id=e.rcv_dtls_id and c.id=e.bundle_mst_id and d.id=e.bundle_dtls_id and e.mst_id=f.id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and  g.userid=$user_id $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond $spo_idsCond $po_idsCond $location_cond
	group by c.id , c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.no_of_bundle, c.remarks,c.bundle_num_prefix_no,d.id , d.mst_id, d.item_rcv_id, d.bundle_qty, d.barcode_no, d.barcode_id , d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id, a.embl_job_no,a.party_id,a.location_id,a.id ,a.subcon_date,a.sys_no, f.issue_date,e.id,e.challan_no order by b.id";*/
	
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
	 $sql="SELECT c.id as bundle_dtls_id,d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, $year_field as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty,c.is_rescan, e.po_number,b.company_id,b.location,b.production_source,b.serving_company,b.embel_name, b.embel_type,b.wo_order_no,b.challan_no,b.wo_order_id,g.id as job_dtls_id,b.id as bundl_mst_id,e.job_id,g.buyer_po_id,e.grouping,m.recv_date,n.quantity  as bundle_qty,m.id as rcv_id,n.id as rcv_dtls_id,t.issue_date,k.id as qc_dtls_id,k.production_dtls_id, k.issue_dtls_id  from pro_garments_production_mst b,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f,subcon_ord_dtls g,printing_bundle_receive_mst m,printing_bundle_receive_dtls n,printing_bundle_issue_dtls k,printing_bundle_issue_mst t, tmp_poid q  where  b.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and  d.job_id=f.id and b.wo_order_no=g.order_no  and c.production_type=2 and  n.id=k.rcv_dtls_id and n.bundle_dtls_id=k.bundle_dtls_id and m.id=n.mst_id and b.id=n.bundle_mst_id  and b.id=k.bundle_mst_id and c.id=n.bundle_dtls_id and c.id=k.bundle_dtls_id and t.id=k.mst_id and g.id=n.job_dtls_id and t.entry_form = 498  and  q.userid=$user_id  and q.poid = k.id  and c.status_active=1 and c.is_deleted=0 and n.status_active=1 and n.is_deleted=0 and n.print_issue_status=1 and k.print_production_status=1 $company $location_cond $withinGroup  $recieve_date $challan_no_cond $job_cond $rec_id_cond $search_com_cond $buyer_po_cond $buyer_style_cond group by  c.id,d.id, e.id, f.job_no_prefix_num, f.insert_date, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty,c.is_rescan, e.po_number,b.company_id,b.location,b.production_source,b.serving_company,b.embel_name, b.embel_type,b.wo_order_no,b.challan_no,b.wo_order_id,g.id,b.id,e.job_id,g.buyer_po_id,e.grouping,m.recv_date,n.quantity,m.id,n.id,t.issue_date,k.id, k.issue_dtls_id,k.production_dtls_id order by c.cut_no,length(c.bundle_no) asc, c.bundle_no asc ";     
	
	$result = sql_select($sql);
	if(count($result)>0){
	?>
	    <div>
	    	<table cellpadding="0" cellspacing="2" border="1" width="1910" id="details_tbl" rules="all" align="left">
	            <thead class="form_table_header">
					<tr align="center" >
						<th width="50" >SL</th>
						<th width="100" >Barcode No</th>
						<th width="100" >Comapany</th>
						<th width="100" >Location</th>
						<th width="60" >Within Group</th>
						<th width="100" >Customer</th>
						<th width="100" >Cus. Buyer</th>
						<th width="60" >Issue Date</th>
						<th width="60">Issue Ch No</th>
						<th width="140">Order No</th>
						<th width="140">Cust. Style Ref.</th>
						<th width="100">IR/IB</th>
						<th width="80">Embl. Name</th>
						<th width="60">Embl. Type</th>
						<th width="80">Body Part</th>
						<th width="130">Color</th>
						<th width="60">Size</th>
						<th width="80">Bundle NO</th>
						<th width="60">Bundle Qty.</th>
						<th width="60">Prod. Qty.</th>
						<th width="60">QC Qty.</th>
						<th width="130">RMK</th>
					</tr>
	            </thead>
			</table>
	        <div style="width:1930px; max-height:270px; overflow-y:scroll;" >
				<table cellpadding="0" cellspacing="2"  width="1910" id="table_body" class="rpt_table" rules="all" align="left">
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
							$wo_id=$job_arr[$row[csf('job_dtls_id')]]['wo_id'];
							$within_group=$job_arr[$row[csf('job_dtls_id')]]['within_group'];
							
							$prod_qty=$production_item_arr[$row[csf('job_dtls_id')]][$row[csf('colorsizeid')]][$row[csf('issue_dtls_id')]]['quantity'];
							$production_dtls_ids=$production_item_arr[$row[csf('job_dtls_id')]][$row[csf('colorsizeid')]][$row[csf('issue_dtls_id')]]['production_dtls_id'];
							$qc_qty=$qc_item_arr[$row[csf('job_dtls_id')]][$row[csf('colorsizeid')]][$row[csf('production_dtls_id')]]['quantity'];
							$qc_dtls_ids=$qc_item_arr[$row[csf('job_dtls_id')]][$row[csf('colorsizeid')]][$row[csf('production_dtls_id')]]['qc_dtls_id'];
							$updatedtlsid=$del_item_arr[$row[csf('job_dtls_id')]][$row[csf('colorsizeid')]][$row[csf('qc_dtls_id')]]['updatedtlsid'];

							$productionDtlsIds=$row[csf("production_dtls_id")];//implode(",",array_unique(explode(",",(chop($production_dtls_ids,',')))));
							$qcDtlsIds=$row[csf("qc_dtls_id")];//implode(",",array_unique(explode(",",(chop($qc_dtls_ids,',')))));
							$checkBox_check ="checked";
							?>
			                <tr>
								<td width="50" align="center">
								<? echo $i; ?>
	                                <input type="checkbox" name="barcodeChkbox[]" id="barcodeChkbox_<? echo $i; ?>" value="" <? echo $checkBox_check; ?> />
									<input type="hidden" name="woID[]" id="woID_<? echo $i; ?>" value="<? echo $wo_id; ?>"  >
									<input type="hidden" name="woDtlsID[]" id="woDtlsID_<? echo $i; ?>" value="<? echo $row[csf("job_dtls_id")]; ?>"  >
									<input type="hidden" name="woBreakID[]" id="woBreakID_<? echo $i; ?>" value="<? echo $row[csf("colorsizeid")]; ?>"  >
									<input type="hidden" name="rcvID[]" id="rcvID_<? echo $i; ?>" value="<? echo $row[csf("rcv_id")]; ?>"  >
									<input type="hidden" name="rcvDtlsID[]" id="rcvDtlsID_<? echo $i; ?>" value="<? echo $row[csf("rcv_dtls_id")]; ?>"  >
									<input type="hidden" name="bundleMstID[]" id="bundleMstID_<? echo $i; ?>" value="<? echo $row[csf("bundl_mst_id")]; ?>"  >
									<input type="hidden" name="bundleDtlsID[]" id="bundleDtlsID_<? echo $i; ?>" value="<? echo $row[csf("bundle_dtls_id")]; ?>"  >
									<input type="hidden" name="issueDtlsID[]" id="issueDtlsID_<? echo $i; ?>" value="<? echo $row[csf("issue_dtls_id")]; ?>"  >
									<input type="hidden" name="productionDtlsIds[]" id="productionDtlsIds_<? echo $i; ?>" value="<? echo $productionDtlsIds; ?>"  >
									<input type="hidden" name="qcDtlsIds[]" id="qcDtlsIds_<? echo $i; ?>" value="<? echo $qcDtlsIds; ?>"  >
									<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value="<? echo $updatedtlsid; ?>"  >
	                            </td>
			                    
			                    <td width="100">
								<? echo $row[csf("barcode_no")]; ?>
									<input name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("barcode_no")]; ?>" style="width:107px" readonly />
								</td>
			                    <td width="100">
									<? 
									echo create_drop_down( "cboCompanyId_".$i, 90, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",$row[csf("serving_company")], "",1,'','','','','','',"cboCompanyId[]"); ?>
								</td>
								
								<td width="100">
									<? 
									echo create_drop_down( "cboLocationId_".$i, 90, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --",$row[csf("location")], "",1,'','','','','','',"cboLocationId[]"); ?>
								</td>
								<td width="60">
									<? echo create_drop_down( "cboWithinGroup_".$i, 55, $yes_no,"", 1, "-- Select --",$within_group, "",1,'','','','','','',"cboWithinGroup[]"); ?>
								</td>
								<td width="100">
									<? echo  create_drop_down( "cboPartyId_".$i, 90, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Customer --",$row[csf("company_id")], "",1,'','','','','','',"cboPartyId[]");  ?>
								</td>
			                    <td width="100">
								<? echo $buyer_buyer; ?>
									<input name="txtCustBuyer[]" id="txtCustBuyer_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $buyer_buyer; ?>"  style="width:87px" readonly />
								</td>
			                    <td width="60">
								<? echo change_date_format($row[csf("issue_date")]); ?>
									<input name="txtIssueDate[]" id="txtIssueDate_<? echo $i; ?>" type="hidden" class="datepicker" value="<? echo change_date_format($row[csf("issue_date")]); ?>"  style="width:87px" disabled />
								</td> 
			                    <td width="60">
								<? echo $row[csf("challan_no")]; ?>
									<input name="txtIssueCh[]" id="txtIssueCh_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("challan_no")]; ?>"  style="width:47px"  readonly  />
								</td>
			                    <td width="140">
								<? echo $order_no; ?>
									<input name="txtOrder_<? echo $i; ?>" id="txtOrder_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $order_no; ?>"  style="width:47px" readonly />
								</td>
			                    <td width="140">
								<? echo $style; ?>
									<input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:47px" readonly />
								</td>
								<td width="100">
								<? echo $row[csf('grouping')]; ?>
									<!-- <input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:47px" readonly /> -->
								</td>
			                    <td width="80">
									 <? echo create_drop_down( "cboProcessName_<? echo $i; ?>", 70, $emblishment_name_array,"", 1, "--Select--",$main_process_id, "",1,'','','','','','',"cboEmbType[]"); ?>
								</td>
								<td width="60">
									<?
									if($main_process_id==1) $emb_type=$emblishment_print_type;
									else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
									else if($main_process_id==3) $emb_type=$emblishment_wash_type;
									else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
									else if($main_process_id==5) $emb_type=$emblishment_gmts_type; 
									echo create_drop_down( "cboEmbType_".$i, 55, $emb_type,"", 1, "-- Select --",$embl_type_id, "",1,'','','','','','',"cboEmbType[]"); ?>
									</td>
								<td width="80">
									<? echo create_drop_down( "cboBodyPart_".$i, 70, $body_part,"", 1, "-- Select --",$body_part_id, "",1,'','','','','','',"cboBodyPart[]"); ?>
								</td>
								<td width="130">
								<? echo $color_arr[$color_id]; ?>
									<input type="hidden" id="txtcolor_<? echo $i; ?>" name="txtcolor_<? echo $i; ?>" class="text_boxes" value="<? echo $color_arr[$color_id]; ?>"  style="width:87px" readonly/>
								</td>
			                    <td width="60">
								<? echo $size_arr[$size_id]; ?>
									<input type="hidden" id="txtsize_<? echo $i; ?>" name="txtsize_<? echo $i; ?>" class="text_boxes txt_size" value="<? echo $size_arr[$size_id]; ?>"  style="width:47px" readonly/>
								</td>
			                    <td width="80">
								<? echo $row[csf("bundle_no")]; ?>
									<input name="txtBundleNo[]" id="txtBundleNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("bundle_no")]; ?>"  style="width:67px" readonly />
								</td>
			                    <td width="60" align="right">
									<input name="txtBundleQty[]" id="txtBundleQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $row[csf("bundle_qty")]; ?>"  style="width:47px" readonly />
								</td>
			                    <td width="60">
									<input name="txtProdQty[]" id="txtProdQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $prod_qty; ?>" style="width:47px" readonly />
									<input name="txtRejQty[]" id="txtRejQty_<? echo $i; ?>" type="hidden" class="text_boxes_numeric" value="<? ?>"  placeholder="Display"   readonly style="width:47px"  />
								</td> 
								<td width="60">
									<input name="txtQcQty[]" id="txtQcQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $qc_qty; ?>" placeholder="Display" style="width:47px" readonly />
								</td>
			                    <td width="130">
									<input name="txtRemark[]" id="txtRemark_<? echo $i; ?>" type="text" class="text_boxes" value="" placeholder="Write" style="width:120px"/>
								</td>
			                </tr>
		                <?
		                $i++;
		                $totBndlQty+=$row[csf("bundle_qty")];
		                $totProdQty+=$prod_qty;
		                $totQcQty+=$qc_qty;
		                }
		                ?>
		            </tbody>
				</table>
		    </div>
			<table cellpadding="0" cellspacing="2" border="1" width="1810" id="tbl_footer" rules="all" align="left">
	            <tfoot>
	            	<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
						<td width="50" align="center">
							All <input form="form_all" type="checkbox" name="check_all" id="check_all" value=""  onclick="fnCheckUnCheckAll(this.checked)"/>
						</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="140">&nbsp;</td>
						<td width="140">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="130">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="80">Total:</td>
						<td width="60">
							<input name="txtTotBundleqty" id="txtTotBundleqty" class="text_boxes_numeric" type="text" value="<? echo $totBndlQty; ?>"  style="width:47px" readonly />
						</td>
						<td width="60">
							<input name="txtTotProdqty" id="txtTotProdqty" class="text_boxes_numeric" type="text" value="<? echo $totProdQty; ?>" style="width:47px" readonly />
							<input name="txtTotRejqty" id="txtTotRejqty" class="text_boxes_numeric" type="hidden" style="width:47px"  readonly />
						</td>
						<td width="60">
							<input name="txtTotQcqty" id="txtTotQcqty" class="text_boxes_numeric" type="text" value="<? echo $totQcQty; ?>" style="width:47px"  readonly />
						</td>
						<td width="130">&nbsp;</td>
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
	from sub_material_mst a , sub_material_dtls b ,prnting_bundle_mst c, prnting_bundle_dtls d , printing_bundle_issue_dtls e , printing_bundle_issue_mst f ,tmp_poid g where  g.poid=e.id and f.entry_form=495 and c.id = d.mst_id and c.item_rcv_dtls_id = b.id and d.item_rcv_id = b.mst_id and d.item_rcv_id = a.id and a.id = b.mst_id and a.id=e.rcv_id and b.id=e.rcv_dtls_id and c.id=e.bundle_mst_id and d.id=e.bundle_dtls_id and e.mst_id=f.id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and  g.userid=$user_id $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond $spo_idsCond $po_idsCond $location_cond
	group by c.id , c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.no_of_bundle, c.remarks,c.bundle_num_prefix_no,d.id , d.mst_id, d.item_rcv_id, d.bundle_qty, d.barcode_no, d.barcode_id , d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id, a.embl_job_no,a.party_id,a.location_id,a.id ,a.subcon_date,a.sys_no, f.issue_date,e.id,e.challan_no order by b.id";
	
	$result = sql_select($sql);
	if(count($result)>0){
	?>
	    <div>
	    	<table cellpadding="0" cellspacing="2" border="1" width="1810" id="details_tbl" rules="all" align="left">
	            <thead class="form_table_header">
					<tr align="center" >
						<th width="50" >SL</th>
						<th width="100" >Barcode No</th>
						<th width="100" >Comapany</th>
						<th width="100" >Location</th>
						<th width="60" >Within Group</th>
						<th width="100" >Customer</th>
						<th width="100" >Cus. Buyer</th>
						<th width="60" >Issue Date</th>
						<th width="60">Issue Ch No</th>
						<th width="140">Order No</th>
						<th width="140">Cust. Style Ref.</th>
						<th width="80">Embl. Name</th>
						<th width="60">Embl. Type</th>
						<th width="80">Body Part</th>
						<th width="130">Color</th>
						<th width="60">Size</th>
						<th width="80">Bundle NO</th>
						<th width="60">Bundle Qty.</th>
						<th width="60">Prod. Qty.</th>
						<th width="60">QC Qty.</th>
						<th width="130">RMK</th>
					</tr>
	            </thead>
			</table>
	        <div style="width:1830px; max-height:270px; overflow-y:scroll;" >
				<table cellpadding="0" cellspacing="2"  width="1810" id="table_body" class="rpt_table" rules="all" align="left">
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
									<input type="hidden" name="issueDtlsID[]" id="issueDtlsID_<? echo $i; ?>" value="<? echo $row[csf("issue_dtls_id")]; ?>"  >
									<input type="hidden" name="productionDtlsIds[]" id="productionDtlsIds_<? echo $i; ?>" value="<? echo $productionDtlsIds; ?>"  >
									<input type="hidden" name="qcDtlsIds[]" id="qcDtlsIds<? echo $i; ?>" value="<? echo $qcDtlsIds; ?>"  >
									<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value="<? echo $updatedtlsid; ?>"  >
	                            </td>
			                    
			                    <td width="100">
								<? echo $row[csf("barcode_no")]; ?>
									<input name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("barcode_no")]; ?>" style="width:107px" readonly />
								</td>
			                    <td width="100">
									<? 
									echo create_drop_down( "cboCompanyId_".$i, 90, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",$row[csf("company_id")], "",1,'','','','','','',"cboCompanyId[]"); ?>
								</td>
								
								<td width="100">
									<? 
									echo create_drop_down( "cboLocationId_".$i, 90, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --",$row[csf("location_id")], "",1,'','','','','','',"cboLocationId[]"); ?>
								</td>
								<td width="60">
									<? echo create_drop_down( "cboWithinGroup_".$i, 55, $yes_no,"", 1, "-- Select --",2, "",1,'','','','','','',"cboWithinGroup[]"); ?>
								</td>
								<td width="100">
									<? echo create_drop_down( "cboPartyId_".$i, 90, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Customer --",$row[csf("party_id")], "",1,'','','','','','',"cboPartyId[]"); ?>
								</td>
			                    <td width="100">
								<? echo $buyer_buyer; ?>
									<input name="txtCustBuyer[]" id="txtCustBuyer_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $buyer_buyer; ?>"  style="width:87px" readonly />
								</td>
			                    <td width="60">
								<? echo change_date_format($row[csf("issue_date")]); ?>
									<input name="txtIssueDate[]" id="txtIssueDate_<? echo $i; ?>" type="hidden" class="datepicker" value="<? echo change_date_format($row[csf("issue_date")]); ?>"  style="width:87px" disabled />
								</td> 
			                    <td width="60">
								<? echo $row[csf("challan_no")]; ?>
									<input name="txtIssueCh[]" id="txtIssueCh_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("challan_no")]; ?>"  style="width:47px"  readonly  />
								</td>
			                    <td width="140">
								<? echo $order_no; ?>
									<input name="txtOrder_<? echo $i; ?>" id="txtOrder_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $order_no; ?>"  style="width:47px" readonly />
								</td>
			                    <td width="140">
								<? echo $style; ?>
									<input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:47px" readonly />
								</td>
			                    <td width="80">
									 <? echo create_drop_down( "cboProcessName_<? echo $i; ?>", 70, $emblishment_name_array,"", 1, "--Select--",$main_process_id, "",1,'','','','','','',"cboEmbType[]"); ?>
								</td>
								<td width="60">
									<?
									if($main_process_id==1) $emb_type=$emblishment_print_type;
									else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
									else if($main_process_id==3) $emb_type=$emblishment_wash_type;
									else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
									else if($main_process_id==5) $emb_type=$emblishment_gmts_type; 
									echo create_drop_down( "cboEmbType_".$i, 55, $emb_type,"", 1, "-- Select --",$embl_type_id, "",1,'','','','','','',"cboEmbType[]"); ?>
									</td>
								<td width="80">
									<? echo create_drop_down( "cboBodyPart_".$i, 70, $body_part,"", 1, "-- Select --",$body_part_id, "",1,'','','','','','',"cboBodyPart[]"); ?>
								</td>
								<td width="130">
								<? echo $color_arr[$color_id]; ?>
									<input type="hidden" id="txtcolor_<? echo $i; ?>" name="txtcolor_<? echo $i; ?>" class="text_boxes" value="<? echo $color_arr[$color_id]; ?>"  style="width:87px" readonly/>
								</td>
			                    <td width="60">
								<? echo $size_arr[$size_id]; ?>
									<input type="hidden" id="txtsize_<? echo $i; ?>" name="txtsize_<? echo $i; ?>" class="text_boxes txt_size" value="<? echo $size_arr[$size_id]; ?>"  style="width:47px" readonly/>
								</td>
			                    <td width="80">
								<? echo $row[csf("bundle_no")]; ?>
									<input name="txtBundleNo[]" id="txtBundleNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("bundle_no")]; ?>"  style="width:67px" readonly />
								</td>
			                    <td width="60" align="right">
									<input name="txtBundleQty[]" id="txtBundleQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $row[csf("bundle_qty")]; ?>"  style="width:47px" readonly />
								</td>
			                    <td width="60">
									<input name="txtProdQty[]" id="txtProdQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $prod_qty; ?>" style="width:47px" readonly />
									<input name="txtRejQty[]" id="txtRejQty_<? echo $i; ?>" type="hidden" class="text_boxes_numeric" value="<??>"  placeholder="Display"   readonly style="width:47px"  />
								</td> 
								<td width="60">
									<input name="txtQcQty[]" id="txtQcQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $qc_qty; ?>" placeholder="Display" style="width:47px" readonly />
								</td>
			                    <td width="130">
									<input name="txtRemark[]" id="txtRemark_<? echo $i; ?>" type="text" class="text_boxes" value="" placeholder="Write" style="width:120px"/>
								</td>
			                </tr>
		                <?
		                $i++;
		                $totBndlQty+=$row[csf("bundle_qty")];
		                $totProdQty+=$prod_qty;
		                $totQcQty+=$qc_qty;
		                }
		                ?>
		            </tbody>
				</table>
		    </div>
			<table cellpadding="0" cellspacing="2" border="1" width="1810" id="tbl_footer" rules="all" align="left">
	            <tfoot>
	            	<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
						<td width="50" align="center">
							All <input form="form_all" type="checkbox" name="check_all" id="check_all" value=""  onclick="fnCheckUnCheckAll(this.checked)"/>
						</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="140">&nbsp;</td>
						<td width="140">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="130">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="80">Total:</td>
						<td width="60">
							<input name="txtTotBundleqty" id="txtTotBundleqty" class="text_boxes_numeric" type="text" value="<? echo $totBndlQty; ?>"  style="width:47px" readonly />
						</td>
						<td width="60">
							<input name="txtTotProdqty" id="txtTotProdqty" class="text_boxes_numeric" type="text" value="<? echo $totProdQty; ?>" style="width:47px" readonly />
							<input name="txtTotRejqty" id="txtTotRejqty" class="text_boxes_numeric" type="hidden" style="width:47px"  readonly />
						</td>
						<td width="60">
							<input name="txtTotQcqty" id="txtTotQcqty" class="text_boxes_numeric" type="text" value="<? echo $totQcQty; ?>" style="width:47px"  readonly />
						</td>
						<td width="130">&nbsp;</td>
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_challan_no').value, 'create_issue_search_list_view', 'popup_search_div', 'embellishment_delivery_bundle_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
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
	
	
	$sql= "SELECT a.id, a.company_id, a.issue_number, a.issue_date, a.floor_id, a.table_id,b.challan_no  from printing_bundle_issue_mst a, printing_bundle_issue_dtls b where a.id=b.mst_id and a.entry_form=499 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_com_cond $issue_date $company group by a.id, a.company_id, a.issue_number, a.issue_date, a.floor_id, a.table_id,b.challan_no order by a.id DESC ";
	//echo $sql; 
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="420" class="rpt_table">
            <thead>
                <th width="50" >SL</th>
                <th width="150" >Delivery No</th>
                 <th width="100" >Challan No</th>
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
	$nameArray=sql_select( "SELECT a.id, a.company_id, a.issue_number, a.issue_date, a.floor_id, a.table_id, a.qc_name,a.shift_id,a.remarks,a.delivery_point,a.floor_id from printing_bundle_issue_mst a where a.id='$data' and a.status_active =1 and a.is_deleted=0 and a.entry_form=499" ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_delivery_no').value 		= '".$row[csf("issue_number")]."';\n";
		echo "document.getElementById('txt_delivery_date').value 	= '".change_date_format($row[csf("issue_date")])."';\n";
		
	    echo "document.getElementById('txt_remarks').value          = '".$row[csf("remarks")]."';\n";
	    echo "document.getElementById('txt_delivery_point').value          = '".$row[csf("delivery_point")]."';\n";
	    echo "document.getElementById('update_id').value            = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_floor').value            = '".$row[csf("floor_id")]."';\n";
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
if($action=="append_new_item_bundle")
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
				$cutbundle_nos_cond.=" and k.barcode_no in($bundleNos) or ";
			}
			$cutbundle_nos_cond=chop($cutbundle_nos_cond,'or ');
			$cutbundle_nos_cond.=")";
		}
		else
		{
			$cutbundle_nos_cond=" and k.barcode_no in ($bundle_nos)";
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
	
	/* //echo "10**"; print_r($issue_dtls_id_arr); die;
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
	} */

	/*$sql="SELECT c.id as bundl_mst_id, c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.no_of_bundle, c.remarks,c.bundle_num_prefix_no,d.id as bundle_dtls_id, d.mst_id, d.item_rcv_id, d.bundle_qty, d.barcode_no, d.barcode_id, d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id as rcv_dtls_id, a.embl_job_no,a.party_id,a.location_id,a.id as rcv_id,a.subcon_date,a.sys_no, $insert_date_cond as year, f.issue_date,e.id as issue_dtls_id,e.challan_no
	from sub_material_mst a , sub_material_dtls b ,prnting_bundle_mst c, prnting_bundle_dtls d , printing_bundle_issue_dtls e , printing_bundle_issue_mst f where  f.entry_form=495 and c.id = d.mst_id and c.item_rcv_dtls_id = b.id and d.item_rcv_id = b.mst_id and d.item_rcv_id = a.id and a.id = b.mst_id and a.id=e.rcv_id and b.id=e.rcv_dtls_id and c.id=e.bundle_mst_id and d.id=e.bundle_dtls_id and e.mst_id=f.id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond $spo_idsCond $po_idsCond $location_cond $cutbundle_nos_cond
	group by c.id , c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.no_of_bundle, c.remarks,c.bundle_num_prefix_no,d.id , d.mst_id, d.item_rcv_id, d.bundle_qty, d.barcode_no, d.barcode_id , d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id, a.embl_job_no,a.party_id,a.location_id,a.id ,a.subcon_date,a.sys_no, a.insert_date, f.issue_date,e.id,e.challan_no order by b.id";*/
	
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
	
	  $sql="SELECT c.id as bundle_dtls_id,d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, $year_field as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty,c.is_rescan, e.po_number,b.company_id,b.location,b.production_source,b.serving_company,b.embel_name, b.embel_type,b.wo_order_no,b.challan_no,b.wo_order_id,g.id as job_dtls_id,b.id as bundl_mst_id,e.job_id,g.buyer_po_id,e.grouping,m.recv_date,n.quantity  as bundle_qty,m.id as rcv_id,n.id as rcv_dtls_id,t.issue_date,k.id as qc_dtls_id ,k.issue_dtls_id,k.production_dtls_id from pro_garments_production_mst b,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f,subcon_ord_dtls g,printing_bundle_receive_mst m,printing_bundle_receive_dtls n,printing_bundle_issue_dtls k,printing_bundle_issue_mst t  where  b.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and  d.job_id=f.id and b.wo_order_no=g.order_no  and c.production_type=2 and  n.id=k.rcv_dtls_id and n.bundle_dtls_id=k.bundle_dtls_id and m.id=n.mst_id and b.id=n.bundle_mst_id  and b.id=k.bundle_mst_id $cutbundle_nos_cond and k.entry_form=498 and c.id=n.bundle_dtls_id and c.id=k.bundle_dtls_id and t.id=k.mst_id and g.id=n.job_dtls_id and c.status_active=1 and c.is_deleted=0 and n.status_active=1 and n.is_deleted=0 and k.status_active=1 and k.is_deleted=0 and t.status_active=1 and t.is_deleted=0  and n.print_issue_status=1 and k.print_production_status=0  $company $location_cond $withinGroup  $recieve_date $challan_no_cond $job_cond $rec_id_cond $search_com_cond $buyer_po_cond $buyer_style_cond group by  c.id,d.id, e.id, f.job_no_prefix_num, f.insert_date, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty,c.is_rescan, e.po_number,b.company_id,b.location,b.production_source,b.serving_company,b.embel_name, b.embel_type,b.wo_order_no,b.challan_no,b.wo_order_id,g.id,b.id,e.job_id,g.buyer_po_id,e.grouping,m.recv_date,n.quantity,m.id,n.id,t.issue_date,k.id,k.issue_dtls_id,k.production_dtls_id order by c.cut_no,length(c.bundle_no) asc, c.bundle_no asc "; 

	//,tmp_poid g      g.poid=e.id and 

	$result = sql_select($sql);
	foreach($result as $row)
    {
		$job_dtls_id_arr[$row[csf('job_dtls_id')]] =$row[csf('job_dtls_id')]; 
	}

	if(!empty($job_dtls_id_arr))
	{
		$job_dtls_id_cond = " and b.id in (".implode(",",$job_dtls_id_arr).")";

		$sql_job="SELECT a.within_group,a.company_id , a.id, a.embellishment_job, a.order_id, a.order_no, b.id as po_id, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty, b.order_uom,b.gmts_item_id,b.buyer_buyer,b.buyer_style_ref,b.main_process_id,b.embl_type,b.body_part
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
		where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id  and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.qnty>0 $job_dtls_id_cond order by c.id ASC";
		
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
		}
		unset($dataArray);

		$job_dtls_id_cond_2 = " and wo_dtls_id in (".implode(",",$job_dtls_id_arr).")";
		$sql_production="SELECT id as production_dtls_id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id from printing_bundle_issue_dtls where  entry_form=497 and status_active=1 and is_deleted=0 $job_dtls_id_cond_2 order by id ASC";
	
		$production_result =sql_select($sql_production);
		foreach($production_result as $row)
		{
			$production_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['quantity']+=$row[csf('quantity')];
			$production_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['production_dtls_id'].=$row[csf('production_dtls_id')].',';
		}
		unset($production_result);

		$sql_qc="SELECT id as qc_dtls_id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id,production_dtls_id from printing_bundle_issue_dtls where  entry_form=498 $spo_idsCondProd and status_active=1 and is_deleted=0 $job_dtls_id_cond_2 order by id ASC";
	
		$qc_result =sql_select($sql_qc); $issue_dtls_id_arr=array();
		foreach($qc_result as $row)
		{
			$qc_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('production_dtls_id')]]['quantity']+=$row[csf('quantity')];
			$qc_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('production_dtls_id')]]['qc_dtls_id'].=$row[csf('qc_dtls_id')].',';
			$issue_dtls_id_arr[$row[csf('issue_dtls_id')]]=$row[csf('issue_dtls_id')];
		}
	}


	
	$i=$total_row+1;
    foreach($result as $row)
    {
		//N. B. QC check here
		if($issue_dtls_id_arr[$row[csf('issue_dtls_id')]] !="")
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
			$wo_id=$job_arr[$row[csf('job_dtls_id')]]['wo_id'];
			$within_group=$job_arr[$row[csf('job_dtls_id')]]['within_group'];
			
			$prod_qty=$production_item_arr[$row[csf('job_dtls_id')]][$row[csf('colorsizeid')]][$row[csf('issue_dtls_id')]]['quantity'];
			$production_dtls_ids=$production_item_arr[$row[csf('job_dtls_id')]][$row[csf('colorsizeid')]][$row[csf('issue_dtls_id')]]['production_dtls_id'];
			$qc_qty=$qc_item_arr[$row[csf('job_dtls_id')]][$row[csf('colorsizeid')]][$row[csf('production_dtls_id')]]['quantity'];
			$qc_dtls_ids=$qc_item_arr[$row[csf('job_dtls_id')]][$row[csf('colorsizeid')]][$row[csf('production_dtls_id')]]['qc_dtls_id'];

			$productionDtlsIds=$row[csf("production_dtls_id")];//implode(",",array_unique(explode(",",(chop($production_dtls_ids,',')))));
			$qcDtlsIds=$row[csf("qc_dtls_id")];//implode(",",array_unique(explode(",",(chop($qc_dtls_ids,',')))));
			$checkBox_check="checked";
			?>
			<tr>
				<td width="50" align="center">
					<? echo $i; ?>
					<input type="checkbox" name="barcodeChkbox[]" id="barcodeChkbox_<? echo $i; ?>" value="" <? echo $checkBox_check; ?> />
					<input type="hidden" name="woID[]" id="woID_<? echo $i; ?>" value="<? echo $wo_id; ?>"  >
					<input type="hidden" name="woDtlsID[]" id="woDtlsID_<? echo $i; ?>" value="<? echo $row[csf("job_dtls_id")]; ?>"  >
					<input type="hidden" name="woBreakID[]" id="woBreakID_<? echo $i; ?>" value="<? echo $row[csf("colorsizeid")]; ?>"  >
					<input type="hidden" name="rcvID[]" id="rcvID_<? echo $i; ?>" value="<? echo $row[csf("rcv_id")]; ?>"  >
					<input type="hidden" name="rcvDtlsID[]" id="rcvDtlsID_<? echo $i; ?>" value="<? echo $row[csf("rcv_dtls_id")]; ?>"  >
					<input type="hidden" name="bundleMstID[]" id="bundleMstID_<? echo $i; ?>" value="<? echo $row[csf("bundl_mst_id")]; ?>"  >
					<input type="hidden" name="bundleDtlsID[]" id="bundleDtlsID_<? echo $i; ?>" value="<? echo $row[csf("bundle_dtls_id")]; ?>"  >
					<input type="hidden" name="issueDtlsID[]" id="issueDtlsID_<? echo $i; ?>" value="<? echo $row[csf("issue_dtls_id")]; ?>"  >
					<input type="hidden" name="productionDtlsIds[]" id="productionDtlsIds_<? echo $i; ?>" value="<? echo $productionDtlsIds; ?>"  >
					<input type="hidden" name="qcDtlsIds[]" id="qcDtlsIds_<? echo $i; ?>" value="<? echo $qcDtlsIds; ?>"  >
					<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value=""  >
				</td>
				
				<td width="100">
					<? echo $row[csf("barcode_no")]; ?>
					<input name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("barcode_no")]; ?>" style="width:90px" readonly />
				</td>
				<td width="100"><? 
					echo create_drop_down( "cboCompanyId_".$i, 90, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",$row[csf("serving_company")], "",1,'','','','','','',"cboCompanyId[]"); ?></td>
				
				<td width="100"><? 
					echo create_drop_down( "cboLocationId_".$i, 90, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --",$row[csf("location")], "",1,'','','','','','',"cboLocationId[]"); ?>
				</td>
				<td width="60">
					<? echo create_drop_down( "cboWithinGroup_".$i, 55, $yes_no,"", 1, "-- Select --",$within_group, "",1,'','','','','','',"cboWithinGroup[]"); ?>
						
					</td>
				<td width="100">
					<? echo create_drop_down( "cboPartyId_".$i, 90, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Customer --",$row[csf("company_id")], "",1,'','','','','','',"cboPartyId[]");
					?>
				</td>
				<td width="100">
					<? echo $buyer_buyer; ?>
					<input name="txtCustBuyer[]" id="txtCustBuyer_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $buyer_buyer; ?>"  style="width:87px" readonly />
				</td>
				<td width="60">
					<? echo change_date_format($row[csf("issue_date")]); ?>
					<input name="txtIssueDate[]" id="txtIssueDate_<? echo $i; ?>" type="hidden" class="datepicker" value="<? echo change_date_format($row[csf("issue_date")]); ?>"  style="width:87px" disabled />
				</td> 
				<td width="60">
					<? echo $row[csf("challan_no")]; ?>
					<input name="txtIssueCh[]" id="txtIssueCh_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("challan_no")]; ?>"  style="width:47px"  readonly  />
				</td>
				<td width="140">
					<? echo $order_no; ?>
					<input name="txtOrder_<? echo $i; ?>" id="txtOrder_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $order_no; ?>"  style="width:47px" readonly />
				</td>
				<td width="140">
					<? echo $style; ?>
					<input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:47px" readonly />
				</td>
				<td width="100">
					<? echo $row[csf('grouping')]; ?>
					<!-- <input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:47px" readonly /> -->
				</td>
				
				<td width="80">
					<? echo create_drop_down( "cboProcessName_<? echo $i; ?>", 70, $emblishment_name_array,"", 1, "--Select--",$main_process_id, "",1,'','','','','','',"cboEmbType[]"); 
					?>
				</td>
				<td width="60"><?
					if($main_process_id==1) $emb_type=$emblishment_print_type;
					else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
					else if($main_process_id==3) $emb_type=$emblishment_wash_type;
					else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
					else if($main_process_id==5) $emb_type=$emblishment_gmts_type; 
					echo create_drop_down( "cboEmbType_".$i, 55, $emb_type,"", 1, "-- Select --",$embl_type_id, "",1,'','','','','','',"cboEmbType[]"); ?></td>
				<td width="80">
					<? echo create_drop_down( "cboBodyPart_".$i, 70, $body_part,"", 1, "-- Select --",$body_part_id, "",1,'','','','','','',"cboBodyPart[]"); 
					?>
				</td>
				<td width="130">
					<? echo $color_arr[$color_id]; ?>
					<input type="hidden" id="txtcolor_<? echo $i; ?>" name="txtcolor_<? echo $i; ?>" class="text_boxes" value="<? echo $color_arr[$color_id]; ?>"  style="width:87px" readonly/>
				</td>
				<td width="60">
					<? echo $size_arr[$size_id]; ?>
					<input type="hidden" id="txtsize_<? echo $i; ?>" name="txtsize_<? echo $i; ?>" class="text_boxes txt_size" value="<? echo $size_arr[$size_id]; ?>"  style="width:47px" readonly/>
				</td>
				<td width="80">
					<? echo $row[csf("bundle_no")]; ?>
					<input name="txtBundleNo[]" id="txtBundleNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("bundle_no")]; ?>"  style="width:67px" readonly />
				</td>
				<td width="60" align="right">
					<input name="txtBundleQty[]" id="txtBundleQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $row[csf("bundle_qty")]; ?>"  style="width:47px" readonly />
				</td>
				<td width="60">
					<input name="txtProdQty[]" id="txtProdQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $prod_qty; ?>" style="width:47px" readonly />
					<input name="txtRejQty[]" id="txtRejQty_<? echo $i; ?>" type="hidden" class="text_boxes_numeric" value="<? ?>"  placeholder="Display"   readonly style="width:47px"  />
				</td>
				<td width="60">
					<input name="txtQcQty[]" id="txtQcQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $qc_qty; ?>" placeholder="Display" style="width:47px" readonly />
				</td>
				<td width="130">
					<input name="txtRemark[]" id="txtRemark_<? echo $i; ?>" type="text" class="text_boxes" value="" placeholder="Write" style="width:120px"/>
				</td>
			</tr>
			<?
			$i++;
		}
	} 
	/* $r_id3=execute_query("delete from tmp_poid where userid=$user_id");
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
	
	/* //echo "10**"; print_r($issue_dtls_id_arr); die;
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
	} */

	$sql="SELECT c.id as bundl_mst_id, c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.no_of_bundle, c.remarks,c.bundle_num_prefix_no,d.id as bundle_dtls_id, d.mst_id, d.item_rcv_id, d.bundle_qty, d.barcode_no, d.barcode_id, d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id as rcv_dtls_id, a.embl_job_no,a.party_id,a.location_id,a.id as rcv_id,a.subcon_date,a.sys_no, $insert_date_cond as year, f.issue_date,e.id as issue_dtls_id,e.challan_no
	from sub_material_mst a , sub_material_dtls b ,prnting_bundle_mst c, prnting_bundle_dtls d , printing_bundle_issue_dtls e , printing_bundle_issue_mst f where  f.entry_form=495 and c.id = d.mst_id and c.item_rcv_dtls_id = b.id and d.item_rcv_id = b.mst_id and d.item_rcv_id = a.id and a.id = b.mst_id and a.id=e.rcv_id and b.id=e.rcv_dtls_id and c.id=e.bundle_mst_id and d.id=e.bundle_dtls_id and e.mst_id=f.id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond $spo_idsCond $po_idsCond $location_cond $cutbundle_nos_cond
	group by c.id , c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.no_of_bundle, c.remarks,c.bundle_num_prefix_no,d.id , d.mst_id, d.item_rcv_id, d.bundle_qty, d.barcode_no, d.barcode_id , d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id, a.embl_job_no,a.party_id,a.location_id,a.id ,a.subcon_date,a.sys_no, a.insert_date, f.issue_date,e.id,e.challan_no order by b.id";

	//,tmp_poid g      g.poid=e.id and 

	$result = sql_select($sql);
	foreach($result as $row)
    {
		$job_dtls_id_arr[$row[csf('job_dtls_id')]] =$row[csf('job_dtls_id')]; 
	}

	if(!empty($job_dtls_id_arr))
	{
		$job_dtls_id_cond = " and b.id in (".implode(",",$job_dtls_id_arr).")";

		$sql_job="SELECT a.within_group,a.company_id , a.id, a.embellishment_job, a.order_id, a.order_no, b.id as po_id, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty, b.order_uom,b.gmts_item_id,b.buyer_buyer,b.buyer_style_ref,b.main_process_id,b.embl_type,b.body_part
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
		where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id  and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.qnty>0 $job_dtls_id_cond order by c.id ASC";
		
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
		unset($dataArray);

		$job_dtls_id_cond_2 = " and wo_dtls_id in (".implode(",",$job_dtls_id_arr).")";
		$sql_production="SELECT id as production_dtls_id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id from printing_bundle_issue_dtls where  entry_form=497 and status_active=1 and is_deleted=0 $job_dtls_id_cond_2 order by id ASC";
	
		$production_result =sql_select($sql_production);
		foreach($production_result as $row)
		{
			$production_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['quantity']+=$row[csf('quantity')];
			$production_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['production_dtls_id'].=$row[csf('production_dtls_id')].',';
		}
		unset($production_result);

		$sql_qc="SELECT id as qc_dtls_id,mst_id,company_id,entry_form,wo_id,wo_dtls_id,wo_break_id,rcv_id,rcv_dtls_id,bundle_mst_id,bundle_dtls_id,challan_no,quantity,issue_dtls_id from printing_bundle_issue_dtls where  entry_form=498 $spo_idsCondProd and status_active=1 and is_deleted=0 $job_dtls_id_cond_2 order by id ASC";
	
		$qc_result =sql_select($sql_qc); $issue_dtls_id_arr=array();
		foreach($qc_result as $row)
		{
			$qc_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['quantity']+=$row[csf('quantity')];
			$qc_item_arr[$row[csf('wo_dtls_id')]][$row[csf('wo_break_id')]][$row[csf('issue_dtls_id')]]['qc_dtls_id'].=$row[csf('qc_dtls_id')].',';
			$issue_dtls_id_arr[$row[csf('issue_dtls_id')]]=$row[csf('issue_dtls_id')];
		}
	}


	
	$i=$total_row+1;
    foreach($result as $row)
    {
		//N. B. QC check here
		if($issue_dtls_id_arr[$row[csf('issue_dtls_id')]] !="")
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
					<input type="hidden" name="issueDtlsID[]" id="issueDtlsID_<? echo $i; ?>" value="<? echo $row[csf("issue_dtls_id")]; ?>"  >
					<input type="hidden" name="productionDtlsIds[]" id="productionDtlsIds_<? echo $i; ?>" value="<? echo $productionDtlsIds; ?>"  >
					<input type="hidden" name="qcDtlsIds[]" id="qcDtlsIds<? echo $i; ?>" value="<? echo $qcDtlsIds; ?>"  >
					<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value=""  >
				</td>
				
				<td width="100">
					<? echo $row[csf("barcode_no")]; ?>
					<input name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("barcode_no")]; ?>" style="width:90px" readonly />
				</td>
				<td width="100"><? 
					echo create_drop_down( "cboCompanyId_".$i, 90, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",$row[csf("company_id")], "",1,'','','','','','',"cboCompanyId[]"); ?></td>
				
				<td width="100"><? 
					echo create_drop_down( "cboLocationId_".$i, 90, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --",$row[csf("location_id")], "",1,'','','','','','',"cboLocationId[]"); ?>
				</td>
				<td width="60">
					<? echo create_drop_down( "cboWithinGroup_".$i, 55, $yes_no,"", 1, "-- Select --",2, "",1,'','','','','','',"cboWithinGroup[]"); ?>
						
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
					<? echo change_date_format($row[csf("issue_date")]); ?>
					<input name="txtIssueDate[]" id="txtIssueDate_<? echo $i; ?>" type="hidden" class="datepicker" value="<? echo change_date_format($row[csf("issue_date")]); ?>"  style="width:87px" disabled />
				</td> 
				<td width="60">
					<? echo $row[csf("challan_no")]; ?>
					<input name="txtIssueCh[]" id="txtIssueCh_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("challan_no")]; ?>"  style="width:47px"  readonly  />
				</td>
				<td width="140">
					<? echo $order_no; ?>
					<input name="txtOrder_<? echo $i; ?>" id="txtOrder_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $order_no; ?>"  style="width:47px" readonly />
				</td>
				<td width="140">
					<? echo $style; ?>
					<input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:47px" readonly />
				</td>
				<td width="80">
					<? echo create_drop_down( "cboProcessName_<? echo $i; ?>", 70, $emblishment_name_array,"", 1, "--Select--",$main_process_id, "",1,'','','','','','',"cboEmbType[]"); 
					?>
				</td>
				<td width="60"><?
					if($main_process_id==1) $emb_type=$emblishment_print_type;
					else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
					else if($main_process_id==3) $emb_type=$emblishment_wash_type;
					else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
					else if($main_process_id==5) $emb_type=$emblishment_gmts_type; 
					echo create_drop_down( "cboEmbType_".$i, 55, $emb_type,"", 1, "-- Select --",$embl_type_id, "",1,'','','','','','',"cboEmbType[]"); ?></td>
				<td width="80">
					<? echo create_drop_down( "cboBodyPart_".$i, 70, $body_part,"", 1, "-- Select --",$body_part_id, "",1,'','','','','','',"cboBodyPart[]"); 
					?>
				</td>
				<td width="130">
					<? echo $color_arr[$color_id]; ?>
					<input type="hidden" id="txtcolor_<? echo $i; ?>" name="txtcolor_<? echo $i; ?>" class="text_boxes" value="<? echo $color_arr[$color_id]; ?>"  style="width:87px" readonly/>
				</td>
				<td width="60">
					<? echo $size_arr[$size_id]; ?>
					<input type="hidden" id="txtsize_<? echo $i; ?>" name="txtsize_<? echo $i; ?>" class="text_boxes txt_size" value="<? echo $size_arr[$size_id]; ?>"  style="width:47px" readonly/>
				</td>
				<td width="80">
					<? echo $row[csf("bundle_no")]; ?>
					<input name="txtBundleNo[]" id="txtBundleNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("bundle_no")]; ?>"  style="width:67px" readonly />
				</td>
				<td width="60" align="right">
					<input name="txtBundleQty[]" id="txtBundleQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $row[csf("bundle_qty")]; ?>"  style="width:47px" readonly />
				</td>
				<td width="60">
					<input name="txtProdQty[]" id="txtProdQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $prod_qty; ?>" style="width:47px" readonly />
					<input name="txtRejQty[]" id="txtRejQty_<? echo $i; ?>" type="hidden" class="text_boxes_numeric" value="<??>"  placeholder="Display"   readonly style="width:47px"  />
				</td>
				<td width="60">
					<input name="txtQcQty[]" id="txtQcQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $qc_qty; ?>" placeholder="Display" style="width:47px" readonly />
				</td>
				<td width="130">
					<input name="txtRemark[]" id="txtRemark_<? echo $i; ?>" type="text" class="text_boxes" value="" placeholder="Write" style="width:120px"/>
				</td>
			</tr>
			<?
			$i++;
		}
	} 
	/* $r_id3=execute_query("delete from tmp_poid where userid=$user_id");
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

	$sql_wo_del="SELECT a.size_id,a.color_id, b.order_no, b.buyer_buyer, b.buyer_style_ref, b.job_no_mst, b.embl_type,b.main_process_id, c.party_id , d.id as del_dtls_id,d.mst_id,d.company_id,d.entry_form,d.wo_id,d.wo_dtls_id,d.wo_break_id,d.rcv_id,d.rcv_dtls_id,d.bundle_mst_id,d.bundle_dtls_id,d.challan_no,d.quantity,d.issue_dtls_id,e.issue_number as challan_no, e.issue_date as delivery_date, e.remarks as mst_remarks, e.delivery_point  from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e where e.id=d.mst_id and e.id='$data[1]' and a.mst_id=b.id and b.id=d.wo_dtls_id and a.job_no_mst=c.embellishment_job and d.wo_id=c.id and b.mst_id=c.id and d.entry_form=499 and e.entry_form=499 and d.mst_id='$data[1]' and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted=0 ";
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
															
															$fab_defect_qty=0;
															$print_defect_qty=0;

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
															$total_defect_qty +=$fab_defect_qty;
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


if($action=="embl_delivery_bundle_entry_print_2")
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
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );

	
	$sql_mst = "SELECT id, delivery_no, company_id, party_location, location_id, within_group, party_id, delivery_date, job_no, challan_no, remarks from subcon_delivery_mst where entry_form=254 and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray = sql_select($sql_mst); $party_name=""; $party_address=""; $party_address="";
	if( $dataArray[0][csf('within_group')]==1)
	{
		$party_name=$company_library[$dataArray[0][csf('party_id')]];
		$party_address=show_company($dataArray[0][csf('party_id')],'','');
	}
	else if($dataArray[0][csf('within_group')]==2) 
	{
		$party_name=$buyer_library[$dataArray[0][csf('party_id')]];
		$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=".$dataArray[0][csf('party_id')].""); 
		foreach ($nameArray as $result)
		{ 
			if($result!="") $party_address=$result['address_1'];
		}
	}

	$com_dtls = fnc_company_location_address($company, $location, 2);

	 $sql_wo_del="SELECT b.order_no, b.buyer_buyer, b.buyer_style_ref, b.job_no_mst, b.embl_type,b.main_process_id, c.party_id , d.id as del_dtls_id,d.mst_id,d.company_id,d.entry_form,d.wo_id,d.wo_dtls_id,d.wo_break_id,d.rcv_id,d.rcv_dtls_id,d.bundle_mst_id,d.bundle_dtls_id,d.challan_no,d.quantity,d.issue_dtls_id,e.issue_number as challan_no, e.issue_date as delivery_date, e.remarks as mst_remarks, e.delivery_point,e.inserted_by, g.sys_number from subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e,pro_garments_production_mst f, pro_gmts_delivery_mst g where e.id=d.mst_id and e.id='$data[1]' and b.id=d.wo_dtls_id and d.wo_id=c.id and b.mst_id=c.id and d.challan_no=g.sys_number_prefix_num and b.order_no=f.wo_order_no and g.id=f.delivery_mst_id and d.entry_form=499 and e.entry_form=499 and d.mst_id='$data[1]' and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted=0 ";

	$sql_wo_del_res=sql_select($sql_wo_del);
	$inserted_by=$user_library[$sql_wo_del_res[0][csf('inserted_by')]];
	$main_process_id=$sql_wo_del_res[0][csf('main_process_id')];
	$embl_type=$sql_wo_del_res[0][csf('embl_type')];


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

	if($main_process_id==1) $emb_type=$emblishment_print_type;
	else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
	else if($main_process_id==3) $emb_type=$emblishment_wash_type;
	else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
	else if($main_process_id==5) $emb_type=$emblishment_gmts_type; 

	$width=800+(count($all_size)*60);
	$mst_width=$width-610;
	$top_width=$width-300;
	$width_px=$width.'px';

	?>
    <div style="width:<? echo $width; ?>px; font-size:13px">
        <table align="center" cellspacing="0" width="<? echo $width; ?>"   class="rpt_table">
            <tr>
                <td width="300" align="left"> 
                    <!-- <img  src='../../<? //echo $com_dtls[2]; ?>' height='50%' width='50%' /> -->
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
                <td width="375"><b><? echo  $com_dtls[0] ?></b></td>
            
            	<td width="130" align="left"><strong>Dev Challan No.: </strong></td>
                <td width="375"><? echo $sql_wo_del_res[0][csf('challan_no')]; ?></td>
            </tr>
            <tr>
            	<td align="left" rowspan="2" width="130" style="vertical-align: top;"><strong>Address: </strong></td>
                <td width="375"  rowspan="2"><? echo $com_dtls[1]; ?></td>	
				<td align="left"><strong>Issue Challan No</strong></td>
                <td width="375"><? echo $sql_wo_del_res[0][csf('sys_number')]; ?></td>
            </tr>
			 <tr> 
			 <td align="left"><strong>Delivery Date: </strong></td>
                <td width="375"><? echo change_date_format($sql_wo_del_res[0][csf('delivery_date')]); ?></td>          	
            </tr>
            <tr>
                <td width="130" align="left"><strong>Delivery Point:</strong></td>
                <td width="375"><? echo $sql_wo_del_res[0][csf('delivery_point')]; ?></td>
                <td  align="left"><strong>Emb. Job No:</strong></td>
                <td width="375"><? echo $sql_wo_del_res[0][csf('job_no_mst')]; ?></td>
            </tr>
            <tr>
			    <td width="130" align="left"><strong>WO Number:</strong></td>
                <td width="375"><? echo $sql_wo_del_res[0][csf('order_no')]; ?></td>
                <td  align="left"><strong>Remarks:</strong></td>
                <td><? echo $sql_wo_del_res[0][csf('mst_remarks')]; ?></td>
            </tr>
			<tr>
			    <td width="130" align="left"><strong>Emb. Type:</strong></td>
                <td width="375"><? echo $emb_type[$embl_type]; ?></td>
            </tr>
        </table>
		<? 	
		 $sql_wo_del_dtls="SELECT b.order_no, b.buyer_buyer, b.buyer_style_ref, b.job_no_mst, b.embl_type,b.main_process_id, c.party_id, d.id as del_dtls_id,d.mst_id,d.company_id,d.entry_form,d.wo_id,d.wo_dtls_id,d.wo_break_id,d.rcv_id,d.rcv_dtls_id,d.bundle_mst_id,d.challan_no,d.quantity,d.issue_dtls_id,e.issue_number as challan_no, e.issue_date as delivery_date, e.remarks as mst_remarks, e.delivery_point,f.barcode_no,f.bundle_no,f.production_qnty ,g.size_number_id, g.color_number_id,b.body_part, h.quantity as bundel_qty,d.remarks
		from  subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e, pro_garments_production_dtls f , wo_po_color_size_breakdown g, printing_bundle_receive_dtls h
		where e.id=d.mst_id and e.id='$data[1]' and b.id=d.wo_dtls_id and d.wo_id=c.id and b.mst_id=c.id and d.bundle_dtls_id=f.id and f.color_size_break_down_id=g.id and  h.id=d.rcv_dtls_id and d.entry_form=499 and e.entry_form=499 and d.mst_id='$data[1]' and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted=0 order by g.size_number_id";
		$sql_dtls_res=sql_select($sql_wo_del_dtls);

		$print_del_arr=array();
		foreach($sql_dtls_res as $row){
			$print_del_arr[$row['SIZE_NUMBER_ID']][$row["BUYER_BUYER"]][$row["BARCODE_NO"]]["BARCODE_NO"]=$row["BARCODE_NO"];
			$print_del_arr[$row['SIZE_NUMBER_ID']][$row["BUYER_BUYER"]][$row["BARCODE_NO"]]["SIZE_NUMBER_ID"]=$row["SIZE_NUMBER_ID"];
			$print_del_arr[$row['SIZE_NUMBER_ID']][$row["BUYER_BUYER"]][$row["BARCODE_NO"]]["COLOR_NUMBER_ID"]=$row["COLOR_NUMBER_ID"];
		     $print_del_arr[$row['SIZE_NUMBER_ID']][$row["BUYER_BUYER"]][$row["BARCODE_NO"]]["BUYER_BUYER"]=$row["BUYER_BUYER"];
			 $print_del_arr[$row['SIZE_NUMBER_ID']][$row["BUYER_BUYER"]][$row["BARCODE_NO"]]["BUYER_STYLE_REF"]=$row["BUYER_STYLE_REF"];
			 $print_del_arr[$row['SIZE_NUMBER_ID']][$row["BUYER_BUYER"]][$row["BARCODE_NO"]]["BODY_PART"]=$row["BODY_PART"];
			 $print_del_arr[$row['SIZE_NUMBER_ID']][$row["BUYER_BUYER"]][$row["BARCODE_NO"]]["BUNDLE_NO"]=$row["BUNDLE_NO"];
			 $print_del_arr[$row['SIZE_NUMBER_ID']][$row["BUYER_BUYER"]][$row["BARCODE_NO"]]["PRODUCTION_QNTY"]=$row["PRODUCTION_QNTY"];
			 $print_del_arr[$row['SIZE_NUMBER_ID']][$row["BUYER_BUYER"]][$row["BARCODE_NO"]]["BUNDEL_QTY"]=$row["BUNDEL_QTY"];
			 $print_del_arr[$row['SIZE_NUMBER_ID']][$row["BUYER_BUYER"]][$row["BARCODE_NO"]]["REMARKS"]=$row["REMARKS"];
			 $print_del_arr[$row['SIZE_NUMBER_ID']][$row["BUYER_BUYER"]][$row["BARCODE_NO"]]["QUANTITY"]=$row["QUANTITY"];
		}
		// echo "<pre>";
		// print_r($print_del_arr);
	?>
        <br>
        <div style="width:100%;">
            <table align="center" cellspacing="0" width="<? echo $width; ?>" border="1" rules="all" class="rpt_table" style="font-size:12px">
                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
				<tr>
                    <th width="30">SL</th>
                    <th width="100">Cus. Buyer</th>
                    <th width="100">Style</th>
                    <th width="120">Body Part</th>
                    <th width="80">Color</th>
                    <th width="100">Size</th>
                    <th width="60">Barcode</th>
                    <th width="60">Bundle No</th>
                    <th width="60">Bundle Qty</th>
                    <th width="60">Prod. Qty</th>
                    <th width="60">QC. Qty</th>
                    <th width="60">Remarks</th>
					</tr>
                </thead>
                <tbody>
				<?
				 $i=1;$bundel_total_qty=0;$production_total_qty=0;$qc_total_qty=0; $bundel_count_arr=array();
				foreach ($print_del_arr as $barcode_no => $barcode_no_data ) 
				{
					$bundel_total_size_qty=0;
					$production_total_size_qty=0;
					$qc_total_size_qty=0;
					foreach ($barcode_no_data as $buyer_buyer => $buyer_buyer_data ) 
					{
						foreach ($buyer_buyer_data as $size_number_id => $row ) 
						{?>
						<tr>
							<td align="center"><?=$i?></td>
							<td align="center"><?=$buyer_library[$row["BUYER_BUYER"]]?></td>
							<td align="center"><?=$row["BUYER_STYLE_REF"]?></td>
							<td align="center"><?=$body_part[$row["BODY_PART"]]?></td>
							<td align="center"><?=$color_arr[$row["COLOR_NUMBER_ID"]]?></td>
							<td align="center"><?=$size_arr[$row["SIZE_NUMBER_ID"]]?></td>
							<td align="center"><?=$row["BARCODE_NO"]?></td>
							<td align="center"><?=$row["BUNDLE_NO"]?></td>
							<td align="right"><?=$row["BUNDEL_QTY"]?></td>
							<td align="right"><?=$row["PRODUCTION_QNTY"]?></td>
							<td align="right"><?=$row["QUANTITY"]?></td>
							<td align="center"><?=$row["REMARKS"]?></td>
						</tr> 
						<?
						$i++;
						$bundel_total_size_qty+=$row["BUNDEL_QTY"];
						$production_total_size_qty+=$row["PRODUCTION_QNTY"];
						$qc_total_size_qty+=$row["QUANTITY"];
						$bundel_total_qty+=$row["BUNDEL_QTY"];
						$production_total_qty+=$row["PRODUCTION_QNTY"];
						$qc_total_qty+=$row["QUANTITY"];
						} 
							
					}
					?>
						</tr>
							<td colspan="7" align="right"><b>Size wise Total:</b></td>
							<td><? echo count($buyer_buyer_data); ?></td>
							<td align="right" ><strong><? echo $bundel_total_size_qty;  ?></td>
							<td align="right" ><strong><? echo $production_total_size_qty; ?></td>
							<td align="right" ><strong><? echo $qc_total_size_qty; ?></td>
							<td></td>
						</tr>
					<?
						$bundel_count_arr[$row["SIZE_NUMBER_ID"]]["COUNT_ID"]=count($buyer_buyer_data);
				}	
					?>	
                </tbody>
                <tfoot>
				</tr>
                	<td colspan="7" align="right"><b> Grand Total:</b></td>
					<td><? echo count($buyer_buyer_data); ?></td>
                    <td align="right" ><strong><? echo $bundel_total_qty; ?></td>
                    <td align="right" ><strong><? echo $production_total_qty; ?></td>
                    <td align="right" ><strong><? echo $qc_total_qty; ?></td>
					<td></td>
					</tr>
                </tfoot>
            </table>
			<br><br>
			<? 
			$sql_summary=sql_select("SELECT sum(b.quantity) as quantity, sum(d.quantity) as bundel_qty,e.size_number_id,sum(c.production_qnty) as production_qnty FROM printing_bundle_issue_mst a,printing_bundle_issue_dtls b, pro_garments_production_dtls c, printing_bundle_receive_dtls d,wo_po_color_size_breakdown e  WHERE a.id = b.mst_id AND b.BUNDLE_MST_ID = c.MST_ID  and d.id=b.RCV_DTLS_ID and b.BUNDLE_DTLS_ID=c.id AND a.id='$data[1]' and c.color_size_break_down_id=e.id group by e.size_number_id");

			$print_sumarry_del_arr=array();
			foreach($sql_summary as $row){
				$print_sumarry_del_arr[$row['SIZE_NUMBER_ID']]["SIZE_NUMBER_ID"]=$row["SIZE_NUMBER_ID"];
				$print_sumarry_del_arr[$row['SIZE_NUMBER_ID']]["BUNDEL_QTY"]+=$row["BUNDEL_QTY"];
				$print_sumarry_del_arr[$row['SIZE_NUMBER_ID']]["PRODUCTION_QNTY"]+=$row["PRODUCTION_QNTY"];
				$print_sumarry_del_arr[$row['SIZE_NUMBER_ID']]["QUANTITY"]+=$row["QUANTITY"];
			}
			?>
			<table align="center" cellspacing="0" width="700" border="1" rules="all" class="rpt_table" style="font-size:12px">
                <thead bgcolor="#dddddd" align="center">
				<tr>
					<td align="center" colspan="6"> <b>Size Wise Summery</b></td>
				</tr>
				<tr>
                    <th width="30">SL</th>
                    <th width="80">Color</th>
                    <th width="100">Bundle</th>
                    <th width="100">Bundle Qty (pec)</th>
                    <th width="100">Prod. Qty (pcs)</th>
                    <th width="120">QC. Qty (Pcs)</th>
					</tr>
                </thead>
                <tbody>
				<?
				    $i=1; $bundel_total_qty=0;$production_total_qty=0;$qc_total_qty=0;
					foreach ($print_sumarry_del_arr as $size_data => $row ) 
					{
						$bundel_total_size_qty=0;$production_total_size_qty=0;$qc_total_size_qty=0;
					          ?>
								<tr>
									<td align="center"><?=$i?></td>
									<td align="center"><?=$size_arr[$row["SIZE_NUMBER_ID"]]?></td>
									<td align="center"><?=$bundel_count_arr[$row["SIZE_NUMBER_ID"]]["COUNT_ID"]?></td>
									<td align="right"><?=$row["BUNDEL_QTY"]?></td>
									<td align="right"><?=$row["PRODUCTION_QNTY"]?></td>
									<td align="right"><?=$row["QUANTITY"]?></td>
								</tr> 
							<?
							$i++;
							$bundel_total_qty+=$row["BUNDEL_QTY"];
							$production_total_qty+=$row["PRODUCTION_QNTY"];
							$qc_total_qty+=$row["QUANTITY"];
							$total_row+=$bundel_count_arr[$row["SIZE_NUMBER_ID"]]["COUNT_ID"];
							} 
					?>	
                </tbody>
                <tfoot>
				</tr>
					<td></td>
					<td>Total</td>
                    <td align="center" ><b><? echo $total_row; ?></b></td>
                    <td align="right" ><b><? echo $bundel_total_qty; ?></b></td>
                    <td align="right" ><b><? echo $production_total_qty; ?></b></td>
                    <td align="right" ><b><? echo $qc_total_qty; ?></b></td>
					</tr>
                </tfoot>
            </table>

            <br>
            	<? 
				  echo signature_table(254, $company, 1000,"1",20,$inserted_by); ?>
        </div>
    </div>
	<?
	exit();
}

if($action=="embl_delivery_bundle_entry_print_3_backUP")
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
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
    $size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $country_library=return_library_array( "select id,country_name from lib_country where status_active=1 and is_deleted=0 ", "id", "country_name" );
	$body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');


	
	$sql_mst = "SELECT id, delivery_no, company_id, party_location, location_id, within_group, party_id, delivery_date, job_no, challan_no, remarks from subcon_delivery_mst where entry_form=254 and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray = sql_select($sql_mst); $party_name=""; $party_address=""; $party_address="";
	if( $dataArray[0][csf('within_group')]==1)
	{
		$party_name=$company_library[$dataArray[0][csf('party_id')]];
		$party_address=show_company($dataArray[0][csf('party_id')],'','');
	}
	else if($dataArray[0][csf('within_group')]==2) 
	{
		$party_name=$buyer_library[$dataArray[0][csf('party_id')]];
		$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=".$dataArray[0][csf('party_id')].""); 
		foreach ($nameArray as $result)
		{ 
			if($result!="") $party_address=$result['address_1'];
		}
	}

	$com_dtls = fnc_company_location_address($company, $location, 2);

	 $sql_wo_del="SELECT b.order_no, b.buyer_buyer, b.buyer_style_ref, b.job_no_mst, b.embl_type,b.main_process_id, c.party_id , d.id as del_dtls_id,d.mst_id,d.company_id,d.entry_form,d.wo_id,d.wo_dtls_id,d.wo_break_id,d.rcv_id,d.rcv_dtls_id,d.bundle_mst_id,d.bundle_dtls_id,d.challan_no,d.quantity,d.issue_dtls_id,e.issue_number as challan_no, e.issue_date as delivery_date, e.remarks as mst_remarks, e.delivery_point,e.inserted_by, g.sys_number,c.location_id,h.batch_id,h.working_company_id as cut_company,g.body_part from subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e,pro_garments_production_mst f, pro_gmts_delivery_mst g, ppl_cut_lay_mst h where e.id=d.mst_id and e.id='$data[1]' and b.id=d.wo_dtls_id and d.wo_id=c.id and b.mst_id=c.id and d.challan_no=g.sys_number_prefix_num and b.order_no=f.wo_order_no and g.id=f.delivery_mst_id and f.cut_no=h.cutting_no and d.entry_form=499 and e.entry_form=499 and d.mst_id='$data[1]' and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted=0";

	$sql_wo_del_res=sql_select($sql_wo_del);
	$inserted_by=$user_library[$sql_wo_del_res[0][csf('inserted_by')]];
	$main_process_id=$sql_wo_del_res[0][csf('main_process_id')];
	$embl_type=$sql_wo_del_res[0][csf('embl_type')];


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

	if($main_process_id==1) $emb_type=$emblishment_print_type;
	else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
	else if($main_process_id==3) $emb_type=$emblishment_wash_type;
	else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
	else if($main_process_id==5) $emb_type=$emblishment_gmts_type; 

	$width=1200;
	$mst_width=$width-610;
	$top_width=$width-300;
	$width_px=$width.'px';

	?>
    <div style="width: 950px;" >
        <table cellspacing="0" style="font: 11px tahoma; width: 100%;">         
			<tr>
					<td colspan="100%" align="center" style="font-size:24px"><strong><? echo $com_dtls[0]; ?></strong></td>
				</tr>
				<tr class="form_caption">
					<td colspan="100%" align="center" style="font-size:14px">
						<?
							$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
							foreach ($nameArray as $result)
							{
								echo ucfirst($result[csf('city')]);
							}
						?>
					</td>
				</tr>
				<tr class="spaceUnder">
					<td colspan="100%" align="center" style="font-size:20px"><u><strong>Printing Delivery Entry [Bundle]</strong></u></td>
				</tr>
            <tr>
            	<td width="140" align="left"><strong>Dev Challan No.: </strong></td>
                <td width="140"><? echo $sql_wo_del_res[0][csf('challan_no')]; ?></td>
				<td width="120" align="left"><strong>Issue Challan No</strong></td>
                <td width="175"><? echo $sql_wo_del_res[0][csf('sys_number')]; ?></td>
				<td width="105" align="left"><strong>WO Number:</strong></td>
                <td width="155"><? echo $sql_wo_del_res[0][csf('order_no')]; ?></td>
				<td align="left" width="140"><strong>Delivery Date: </strong></td>
                <td width="155"><? echo change_date_format($sql_wo_del_res[0][csf('delivery_date')]); ?></td> 			
            </tr>
            <tr>
                <td  align="left"><strong>Working Location:</strong></td>
                <td><? echo $location_arr[$sql_wo_del_res[0][csf('location_id')]]; ?></td>
                <td  align="left"><strong>Buyer:</strong></td>
                <td><? echo $buyer_library[$sql_wo_del_res[0][csf('buyer_buyer')]]; ?></td>
				<td  align="left"><strong>Emb. Type:</strong></td>
                <td><? echo $emb_type[$embl_type]; ?></td>
				<td  align="left"><strong>Cutting Company	:</strong></td>
                <td><? echo $company_library[$sql_wo_del_res[0][csf('cut_company')]]; ?></td>
				
            </tr>
			<tr>
				<td  align="left"><strong>Body Part:</strong></td>
				<td ><? echo $body_part_arr[$sql_wo_del_res[0][csf('body_part')]]; ?></td>
				<td  align="left"><strong>Batch:</strong></td>
                <td><? echo $sql_wo_del_res[0][csf('batch_id')]; ?></td>
			</tr>
        </table>
         <? 
			$order_cut_arr=sql_select("SELECT a.CUTTING_NO,b.ORDER_CUT_NO FROM ppl_cut_lay_mst a,ppl_cut_lay_dtls b WHERE a.id=b.mst_id and a.COMPANY_ID=$company and a.ENTRY_FORM=99");
			$order_no_arr=array();
			foreach($order_cut_arr as $row){
				$order_no_arr[$row["CUTTING_NO"]]["ORDER_CUT_NO"]=$row["ORDER_CUT_NO"];
			}

			$sql_summary=sql_select("SELECT sum(b.quantity) as quantity, sum(d.quantity) as bundel_qty,f.PO_NUMBER,c.CUT_NO,c.BUNDLE_NO, e.item_number_id,e.color_number_id ,e.country_id,g.buyer_style_ref,e.size_number_id,sum(c.production_qnty) as production_qnty FROM printing_bundle_issue_mst a,printing_bundle_issue_dtls b,pro_garments_production_dtls c, printing_bundle_receive_dtls d, wo_po_color_size_breakdown e, wo_po_break_down f,subcon_ord_dtls g WHERE a.id = b.mst_id AND b.BUNDLE_MST_ID = c.MST_ID  and d.id=b.RCV_DTLS_ID and b.BUNDLE_DTLS_ID=c.id AND a.id='$data[1]' and c.color_size_break_down_id=e.id and e.po_break_down_id=f.id  and g.id=b.wo_dtls_id  group by f.PO_NUMBER,c.CUT_NO , e.item_number_id,e.color_number_id ,e.country_id,g.buyer_style_ref,e.size_number_id,c.BUNDLE_NO order by length(c.bundle_no) asc,c.bundle_no asc");

			$print_sumarry_del_arr=array();$bundle_size_arr=array();
			foreach($sql_summary as $row){
				
				$key=$row[csf('country_id')].$row[csf('po_number')].$row[csf('color_number_id')].$row[csf('buyer_style_ref')];

				$bundle_size_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];

				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["SIZE_NUMBER_ID"]=$row["SIZE_NUMBER_ID"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["ITEM_NUMBER_ID"]=$row["ITEM_NUMBER_ID"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["BUNDLE_NO"]=$row["BUNDLE_NO"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["CUT_NO"]=$row["CUT_NO"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["COUNTRY_ID"]=$row["COUNTRY_ID"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["PO_NUMBER"]=$row["PO_NUMBER"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["COLOR_NUMBER_ID"]=$row["COLOR_NUMBER_ID"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["BUYER_STYLE_REF"]=$row["BUYER_STYLE_REF"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["BUNDEL_QTY"]+=$row["BUNDEL_QTY"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["PRODUCTION_QNTY"]+=$row["PRODUCTION_QNTY"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["QUANTITY"]+=$row["QUANTITY"];

                $sizeQtyArr[$key][$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
                $size_wise_bundle_num_arr[$row[csf('size_number_id')]] ++;
                $bundleArr[$key][$row[csf('bundle_no')]]=$row[csf('bundle_no')];
			}

			// print_r($sizeQtyArr);
			$width = 900;
			$others_col_width = 720;
			$dif = $width - $others_col_width;
			$total_size = count($bundle_size_arr);
			$size_width = $dif / $total_size;
			$table_width=1000+($total_size*$size_width);
			?>
			<table class="details-view rpt_table" style="font-size:12px; margin-top:5px;" width="100%" cellspacing="0" cellpadding="0" border="1" align="left">
                <thead bgcolor="#dddddd" align="center">
				<tr>
                    <th class="outer"  width="30" rowspan="2">SL</th>
                    <th class="inner" width="80" rowspan="2">Cutting No.</th>
                    <th class="inner" width="130" rowspan="2">PO Number</th>
                    <th class="inner" width="100" rowspan="2">Item</th>
                    <th class="inner" width="100" rowspan="2">Style Ref</th>
                    <th class="inner" width="100" rowspan="2">Color</th>
                    <th class="inner" width="100" rowspan="2">Country</th>
                    <th class="inner" width="70" rowspan="2">Cut No</th>
					<th class="inner" align="center" width="<? echo $size_width;?>" colspan="<? echo count($bundle_size_arr); ?>">Size Breakdown</th>
                    <th class="inner" width="70" rowspan="2">Prod. Qty</th>
                    <th class="inner" width="70" rowspan="2">QC. Qty</th>
                    <th class="outer"  width="70" rowspan="2">Rej. Qty</th>	
				</tr>
				<tr style="border: #000;">
                    <?
                    $i=0;
                    foreach($bundle_size_arr as $inf)
                    {
                        ?>
                        <th class="inner" align="center" width="<? echo $size_width;?>" rowspan="2"><? echo $size_library[$inf]; ?></th>
                        <?
                    }
                    ?>
                 </tr>
                </thead>
                <tbody>
				<?
				    $i=1; 
					$bundel_total_qty=0;$production_total_qty=0;$qc_total_qty=0; $reject_qty=0;
					foreach ($print_sumarry_del_arr as $cut_data => $po_no_arr ) 
					{
						foreach ($po_no_arr as $po_data => $color_id_arr ) 
						{
							foreach ($color_id_arr as $key_data => $row ) 
							{
								$CutNumber = $row["CUT_NO"];
                                $cutnumbox = explode("-", $CutNumber);
								$cutnumbers = ltrim($cutnumbox[2], '0');

								 $key=$row[csf('country_id')].$row[csf('po_number')].$row[csf('color_number_id')].$row[csf('buyer_style_ref')];
								$production_total_size_qty=0;$qc_total_size_qty=0;
								$i % 2 == 0 ? $bgcolor = "#FFFFFF" : $bgcolor = "#E9F3FF";
									?>
										<tr bgcolor="<?=$bgcolor?>" >
											<td class="outer" align="center"><?=$i?></td>
											<td class="inner" align="center"><?=$cutnumbers?></td>
											<td class="inner" align="center"><?=$row["PO_NUMBER"]?></td>
											<td class="inner" align="center"><?=$garments_item[$row["ITEM_NUMBER_ID"]]?></td>
											<td class="inner" align="center"><?=$row["BUYER_STYLE_REF"]?></td>
											<td class="inner" align="center"><?=$color_arr[$row["COLOR_NUMBER_ID"]]?></td>
											<td class="inner" align="center"><?=$country_library[$row["COUNTRY_ID"]]?></td>
											<td class="inner" align="center"><?=$order_no_arr[$row["CUT_NO"]]["ORDER_CUT_NO"];?></td>
											<?
											foreach($bundle_size_arr as $size_id)
											{
												$size_qty=0;
												$size_qty=$sizeQtyArr[$key][$size_id];
												?>
												<td class="inner" class="inner_two" align="center" width="<? echo $size_width;?>"><? echo $size_qty; ?></td>
												<?
												$grand_total_size_arr[$size_id]+=$size_qty;
											}
											?>
											<td  class="inner" align="right"><?=$row["PRODUCTION_QNTY"]?></td>
											<td class="inner" align="right"><?=$row["QUANTITY"]?></td>
											<td class="outer" align="right"><?=$row["PRODUCTION_QNTY"]-$row["QUANTITY"]?></td>
										
										</tr> 
									<?
									$i++;
									//$bundel_total_qty+=$row["BUNDEL_QTY"];
									$production_total_qty+=$row["PRODUCTION_QNTY"];
									$qc_total_qty+=$row["QUANTITY"];
									$reject_qty+=$row["PRODUCTION_QNTY"]-$row["QUANTITY"];
									$grand_total_bundle_num+=count($bundleArr[$key]);
							} 
						}
					}
						?>	
					</tbody>
					<tfoot>
							<tr bgcolor="#dddddd">								
								<td colspan="8" class="outer" align="right" ><b>Total Qty-</b></td>
								<?
									foreach($bundle_size_arr as $size_id)
									{   
										?>
										<td class="inner" align="center" width="<? echo $size_width;?>">
										<b>
											<? 
											echo $grand_total_size_arr[$size_id];
											?>
										</b></td>
										<?
									}
								?>
								<td class="inner" align="right" ><b><?=$production_total_qty?></b></td>
								<td class="inner" align="right" ><b><?=$qc_total_qty?></b></td>
								<td align="right" class="outer" ><b><?=$reject_qty?></b></td>
							</tr>
							 <tr bgcolor="#dddddd" height="26"> 
								<td colspan="8" class="outer" align="right" ><b>Bundle Qty-</b></td>
								<? 
								foreach($bundle_size_arr as $size_id)
								{   
									?>
									<td class="inner" align="center" width="<?//echo $size_width;?>">
									<b>
										<?  
										//echo $size_wise_bundle_num_arr[$size_id];
										?>
									</b></td>
									<? 
								}
								?>
								<td align="center" class="outer" colspan="3"><b><?//=$grand_total_bundle_num?></b></td>							
							</tr> 
					</tfoot>
            </table>
 		<? 	
 	  /* $sql_wo_del_dtls="SELECT f.bundle_no,f.production_qnty ,g.size_number_id, g.color_number_id,b.body_part, d.quantity, p.number_start,p.number_end,p.pattern_no,f.cut_no
		from  subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e, pro_garments_production_dtls f , wo_po_color_size_breakdown g, printing_bundle_receive_dtls h, ppl_cut_lay_bundle p
		where e.id=d.mst_id and e.id='$data[1]' and b.id=d.wo_dtls_id and d.wo_id=c.id and b.mst_id=c.id and d.bundle_dtls_id=f.id and f.color_size_break_down_id=g.id and  h.id=d.rcv_dtls_id and p.bundle_no=f.bundle_no and d.entry_form=499 and e.entry_form=499 and d.mst_id='$data[1]' and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted=0 order by length(f.bundle_no) asc,f.bundle_no asc";*/
		
		 $sql_wo_del_dtls="SELECT f.bundle_no,f.production_qnty ,g.size_number_id, g.color_number_id,b.body_part, d.quantity, p.number_start,p.number_end,p.pattern_no,f.cut_no
		from  subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e, pro_garments_production_dtls f , wo_po_color_size_breakdown g, printing_bundle_receive_dtls h, ppl_cut_lay_bundle p
		where e.id=d.mst_id and e.id='$data[1]' and b.id=d.wo_dtls_id and d.wo_id=c.id and b.mst_id=c.id and d.bundle_dtls_id=f.id and f.color_size_break_down_id=g.id and  h.id=d.rcv_dtls_id and p.bundle_no=f.bundle_no and d.entry_form=499 and e.entry_form=499 and d.mst_id='$data[1]' and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted=0   and p.status_active =1 and p.is_deleted=0  and h.status_active =1 and h.is_deleted=0 and g.status_active =1 and g.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0   order by length(f.bundle_no) asc,f.bundle_no asc";
		$sql_dtls_res=sql_select($sql_wo_del_dtls);

		$print_del_arr=array();
		foreach($sql_dtls_res as $row){

			$print_del_arr[$row['CUT_NO']][$row['SIZE_NUMBER_ID']][$row['PATTERN_NO']][$row["BUNDLE_NO"]]["SIZE_NUMBER_ID"]=$row["SIZE_NUMBER_ID"];
			$print_del_arr[$row['CUT_NO']][$row['SIZE_NUMBER_ID']][$row['PATTERN_NO']][$row["BUNDLE_NO"]]["NUMBER_START"]=$row["NUMBER_START"];
			$print_del_arr[$row['CUT_NO']][$row['SIZE_NUMBER_ID']][$row['PATTERN_NO']][$row["BUNDLE_NO"]]["NUMBER_END"]=$row["NUMBER_END"];
			$print_del_arr[$row['CUT_NO']][$row['SIZE_NUMBER_ID']][$row['PATTERN_NO']][$row["BUNDLE_NO"]]["BUNDLE_NO"]=$row["BUNDLE_NO"];
			$print_del_arr[$row['CUT_NO']][$row['SIZE_NUMBER_ID']][$row['PATTERN_NO']][$row["BUNDLE_NO"]]["PRODUCTION_QNTY"]=$row["PRODUCTION_QNTY"];
			$print_del_arr[$row['CUT_NO']][$row['SIZE_NUMBER_ID']][$row['PATTERN_NO']][$row["BUNDLE_NO"]]["BUNDEL_QTY"]=$row["BUNDEL_QTY"];
			$print_del_arr[$row['CUT_NO']][$row['SIZE_NUMBER_ID']][$row['PATTERN_NO']][$row["BUNDLE_NO"]]["QUANTITY"]=$row["QUANTITY"];
			$print_del_arr[$row['CUT_NO']][$row['SIZE_NUMBER_ID']][$row['PATTERN_NO']][$row["BUNDLE_NO"]]["PATTERN_NO"]=$row["PATTERN_NO"];
 			 
		}
		// echo "<pre>";
		// print_r($print_del_arr);
	?>
    <?
 
			$item_segment =51;
			$current_row = 1;
			$current_cols = 1;
			$column_data = '';
			$end_table = 0;
			$first = 1;
 			$serial = 1;
  
  
	    $total_prod_qty=0; $total_qc_qty=0; $total_rej_qty=0;
		foreach ($print_del_arr as $cut_no => $cut_data_arr)
        { 
			foreach ($cut_data_arr as $size_id => $bundle_data_arr)
			{ 
				$sub_total_prod_qty=0; $sub_total_qc_qty=0; $sub_total_rej_qty=0;
				foreach ($bundle_data_arr as $pattern_no => $pattern_data_arr)
				{  
				
					$sl=1;
					$pattern_total=0;
					foreach ($pattern_data_arr as $bundle_no => $row)
					{
						$sub_total_prod_qty+=$row["PRODUCTION_QNTY"];
						$sub_total_qc_qty+=$row["QUANTITY"];
						$sub_total_rej_qty+=$row["PRODUCTION_QNTY"]-$row["QUANTITY"];
						$total_prod_qty+=$row["PRODUCTION_QNTY"];
						$total_qc_qty+=$row["QUANTITY"];
						$total_rej_qty+=$row["PRODUCTION_QNTY"]-$row["QUANTITY"];
						$bundleNumber = $row["BUNDLE_NO"];
						$parts = explode("-", $bundleNumber);
						if($parts[4]!="")
						{
							$part_four="-".$parts[4];
						}
						
						//$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
						$serial % 2 == 0 ? $bgcolor = "#FFFFFF" : $bgcolor = "#E9F3FF";
						if($current_row == 1)
						{
						$column_data .=' <table class="details-view rpt_table" style="font-size:15px; margin-top:5px; margin-left: 20px;"  cellspacing="0" cellpadding="0" border="1" align="left" width="415>
									<thead bgcolor="#dddddd" align="center">
										<tr bgcolor="#dddddd">
											<td width="4%" align="center" class="inner"><b>Sl</b></td>
											<td width="18%" align="center" class="inner"><b>Size</b></td>
											<th width="12%" class="inner" align="center">Pattern</th>
											<td width="20%"align="center" class="inner"><b>Bundle No</b></td>
											<td width="15%" align="center" class="inner"><b>RMG Qty</b></td>
											<td width="10%" align="center" class="inner"><b>Prod. Qty</b></td>
											<td width="10%" align="center" class="inner"><b>QC Qty</b></td>
											<td width="10%" align="center" class="outer"><b>Rej</b></td>
										</tr>
									</thead>
									<tbody>';
									$first++;
		
						}
						$column_data .='<tr bgcolor='.$bgcolor.'>
											<td width="4%" class="inner" align="center">'.$sl.'</td>
											<td width="18%" class="inner" align="center">'.$size_arr[$row["SIZE_NUMBER_ID"]].'</td>
											<td width="12%" class="inner" align="center">'.$row["PATTERN_NO"].'</td>
											<td width="20%" class="inner" align="center">'.$parts[2]."-".$parts[3].$part_four.'</td>
											<td width="15%" class="inner" align="center">'.($row["NUMBER_START"]." - ".$row["NUMBER_END"]).'</td>
											<td width="10%" class="inner" align="right">'.$row["PRODUCTION_QNTY"].'</td>
											<td width="10%" class="inner" align="right">'.$row["QUANTITY"].'</td>
											<td width="12%" class="outer" align="right">'.($row["PRODUCTION_QNTY"]-$row["QUANTITY"]).'</td>
										</tr>';
										$serial++;
										$sl++;
									   $pattern_total++;
									   
									  // echo $current_row." ==". $item_segment."<br>"; 
									   
								if ($current_row == $item_segment)
								{
									// echo $current_row .'=='. $item_segment."<br>";
									$current_row = 1;
									$column_data .= '</tbody></table>';

									if($current_cols == 2)
									{
										$end_table = 1;
										// $table_data .= '<p style="page-break-after: always;"></p>';
										$column_data .= '<div class="pagebreak"></div><br clear="all">';
										$current_cols = 1;
										//$item_segment = 67;
										//$item_segment = 66;
										$item_segment =62;

									}
									else
									{
										$current_cols++;
									}

								}
								else
								{
									$current_row++;
								}
 										 
					}
					
					$column_data .='<tr bgcolor="#dddddd"> 
										<td width="10%" align="right" colspan="2" class="inner" align="center"><b>'."Bundle Qty".'</b></td>
										<td width="12%" class="inner" align="right"><b>'.$pattern_total.'</b></td>
										<td width="12%" class="inner" align="right"></td>
										<td width="12%" class="inner" align="right"></td>
										<td width="12%" class="inner" align="right"></td>
										<td width="12%" class="inner" align="right"></td>
										<td width="12%" class="inner" align="right"></td>
									</tr>';
				}
					$column_data .='<tr bgcolor="#dddddd"> 
										<td width="10%" align="right" colspan="5" class="inner" align="center"><b>'."Size Total".'</b></td>
										<td width="12%" class="inner" align="right"><b>'.$sub_total_prod_qty.'</b></td>
										<td width="12%" class="inner" align="right"><b>'.$sub_total_qc_qty.'</b></td>
										<td width="12%" class="outer" align="right"><b>'.$sub_total_rej_qty.'</b></td>
									</tr>';
			}
		}
				$column_data .='</tfoot>
									<tr bgcolor="#dddddd">								
										<td width="10%" align="right" colspan="5" class="inner" align="center"><b>'."Grand Total".'</b></td>
										<td width="12%" class="inner" align="right"><b>'.$total_prod_qty.'</b></td>
										<td width="12%" class="inner" align="right"><b>'.$total_qc_qty.'</b></td>
										<td width="12%" class="outer" align="right"><b>'.$total_rej_qty.'</b></td>
									</tr>
							    </tfoot></table>';
                echo $column_data;
            ?>
			<br clear="all">
    </div>
	<?
	exit();
}

if($action=="embl_delivery_bundle_entry_print_3")
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
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
    $size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $country_library=return_library_array( "select id,country_name from lib_country where status_active=1 and is_deleted=0 ", "id", "country_name" );
	$body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');


	
	$sql_mst = "SELECT id, delivery_no, company_id, party_location, location_id, within_group, party_id, delivery_date, job_no, challan_no, remarks from subcon_delivery_mst where entry_form=254 and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray = sql_select($sql_mst); $party_name=""; $party_address=""; $party_address="";
	if( $dataArray[0][csf('within_group')]==1)
	{
		$party_name=$company_library[$dataArray[0][csf('party_id')]];
		$party_address=show_company($dataArray[0][csf('party_id')],'','');
	}
	else if($dataArray[0][csf('within_group')]==2) 
	{
		$party_name=$buyer_library[$dataArray[0][csf('party_id')]];
		$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=".$dataArray[0][csf('party_id')].""); 
		foreach ($nameArray as $result)
		{ 
			if($result!="") $party_address=$result['address_1'];
		}
	}

	$com_dtls = fnc_company_location_address($company, $location, 2);

	 $sql_wo_del="SELECT b.order_no, b.buyer_buyer, b.buyer_style_ref, b.job_no_mst, b.embl_type,b.main_process_id, c.party_id , d.id as del_dtls_id,d.mst_id,d.company_id,d.entry_form,d.wo_id,d.wo_dtls_id,d.wo_break_id,d.rcv_id,d.rcv_dtls_id,d.bundle_mst_id,d.bundle_dtls_id,d.challan_no,d.quantity,d.issue_dtls_id,e.issue_number as challan_no, e.issue_date as delivery_date, e.remarks as mst_remarks, e.delivery_point,e.inserted_by, g.sys_number,f.location,h.batch_id,h.working_company_id as cut_company,g.body_part from subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e,pro_garments_production_mst f, pro_gmts_delivery_mst g, ppl_cut_lay_mst h where e.id=d.mst_id and e.id='$data[1]' and b.id=d.wo_dtls_id and d.wo_id=c.id and b.mst_id=c.id and d.challan_no=g.sys_number_prefix_num and b.order_no=f.wo_order_no and g.id=f.delivery_mst_id and f.cut_no=h.cutting_no and d.entry_form=499 and e.entry_form=499 and d.mst_id='$data[1]' and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted=0";

	$sql_wo_del_res=sql_select($sql_wo_del);
	$inserted_by=$user_library[$sql_wo_del_res[0][csf('inserted_by')]];
	$main_process_id=$sql_wo_del_res[0][csf('main_process_id')];
	$embl_type=$sql_wo_del_res[0][csf('embl_type')];


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

	if($main_process_id==1) $emb_type=$emblishment_print_type;
	else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
	else if($main_process_id==3) $emb_type=$emblishment_wash_type;
	else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
	else if($main_process_id==5) $emb_type=$emblishment_gmts_type; 

	$width=1200;
	$mst_width=$width-610;
	$top_width=$width-300;
	$width_px=$width.'px';

	?>
    <div style="width: 950px;" >
        <table cellspacing="0" style="font: 11px tahoma; width: 100%;">         
			<tr>
					<td colspan="100%" align="center" style="font-size:24px"><strong><? echo $com_dtls[0]; ?></strong></td>
				</tr>
				<tr class="form_caption">
					<td colspan="100%" align="center" style="font-size:14px">
						<?
							$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
							foreach ($nameArray as $result)
							{
								echo ucfirst($result[csf('city')]);
							}
						?>
					</td>
				</tr>
				<tr class="spaceUnder">
					<td colspan="100%" align="center" style="font-size:20px"><u><strong>Printing Delivery Entry [Bundle]</strong></u></td>
				</tr>
            <tr>
            	<td width="140" align="left"><strong>Dev Challan No.: </strong></td>
                <td width="140"><? echo $sql_wo_del_res[0][csf('challan_no')]; ?></td>
				<td width="120" align="left"><strong>Issue Challan No</strong></td>
                <td width="175"><? echo $sql_wo_del_res[0][csf('sys_number')]; ?></td>
				<td width="105" align="left"><strong>WO Number:</strong></td>
                <td width="155"><? echo $sql_wo_del_res[0][csf('order_no')]; ?></td>
				<td align="left" width="140"><strong>Delivery Date: </strong></td>
                <td width="155"><? echo change_date_format($sql_wo_del_res[0][csf('delivery_date')]); ?></td> 			
            </tr>
            <tr>
                <td  align="left"><strong>Working Location:</strong></td>
                <td><? echo $location_arr[$sql_wo_del_res[0][csf('location')]]; ?></td>
                <td  align="left"><strong>Buyer:</strong></td>
                <td><? echo $buyer_library[$sql_wo_del_res[0][csf('buyer_buyer')]]; ?></td>
				<td  align="left"><strong>Emb. Type:</strong></td>
                <td><? echo $emb_type[$embl_type]; ?></td>
				<td  align="left"><strong>Cutting Company	:</strong></td>
                <td><? echo $company_library[$sql_wo_del_res[0][csf('cut_company')]]; ?></td>
				
            </tr>
			<tr>
				<td  align="left"><strong>Body Part:</strong></td>
				<td ><? echo $body_part_arr[$sql_wo_del_res[0][csf('body_part')]]; ?></td>
				<td  align="left"><strong>Batch:</strong></td>
                <td><? echo $sql_wo_del_res[0][csf('batch_id')]; ?></td>
			</tr>
        </table>
         <? 
			$order_cut_arr=sql_select("SELECT a.CUTTING_NO,b.ORDER_CUT_NO FROM ppl_cut_lay_mst a,ppl_cut_lay_dtls b WHERE a.id=b.mst_id and a.COMPANY_ID=$company and a.ENTRY_FORM=99");
			$order_no_arr=array();
			foreach($order_cut_arr as $row){
				$order_no_arr[$row["CUTTING_NO"]]["ORDER_CUT_NO"]=$row["ORDER_CUT_NO"];
			}

			$sql_summary=sql_select("SELECT sum(b.quantity) as quantity, sum(d.quantity) as bundel_qty,f.PO_NUMBER,c.CUT_NO,c.BUNDLE_NO, e.item_number_id,e.color_number_id ,e.country_id,g.buyer_style_ref,e.size_number_id,sum(c.production_qnty) as production_qnty FROM printing_bundle_issue_mst a,printing_bundle_issue_dtls b,pro_garments_production_dtls c, printing_bundle_receive_dtls d, wo_po_color_size_breakdown e, wo_po_break_down f,subcon_ord_dtls g WHERE a.id = b.mst_id AND b.BUNDLE_MST_ID = c.MST_ID  and d.id=b.RCV_DTLS_ID and b.BUNDLE_DTLS_ID=c.id AND a.id='$data[1]' and c.color_size_break_down_id=e.id and e.po_break_down_id=f.id  and g.id=b.wo_dtls_id  group by f.PO_NUMBER,c.CUT_NO , e.item_number_id,e.color_number_id ,e.country_id,g.buyer_style_ref,e.size_number_id,c.BUNDLE_NO order by length(c.bundle_no) asc,c.bundle_no asc");
			//echo $sql_summary; exit();
			$print_sumarry_del_arr=array();$bundle_size_arr=array();
			foreach($sql_summary as $row){
				
				$key=$row[csf('country_id')].$row[csf('po_number')].$row[csf('color_number_id')].$row[csf('buyer_style_ref')];

				$bundle_size_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];

				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["SIZE_NUMBER_ID"]=$row["SIZE_NUMBER_ID"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["ITEM_NUMBER_ID"]=$row["ITEM_NUMBER_ID"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["BUNDLE_NO"]=$row["BUNDLE_NO"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["CUT_NO"]=$row["CUT_NO"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["COUNTRY_ID"]=$row["COUNTRY_ID"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["PO_NUMBER"]=$row["PO_NUMBER"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["COLOR_NUMBER_ID"]=$row["COLOR_NUMBER_ID"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["BUYER_STYLE_REF"]=$row["BUYER_STYLE_REF"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["BUNDEL_QTY"]+=$row["BUNDEL_QTY"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["PRODUCTION_QNTY"]+=$row["PRODUCTION_QNTY"];
				$print_sumarry_del_arr[$row['CUT_NO']][$row['PO_NUMBER']][$row['color_number_id']]["QUANTITY"]+=$row["QUANTITY"];

                $sizeQtyArr[$key][$row[csf('size_number_id')]]+=$row[csf('quantity')];
                $size_wise_bundle_num_arr[$row[csf('size_number_id')]] ++;
                $bundleArr[$key][$row[csf('bundle_no')]]=$row[csf('bundle_no')];
			}

			// print_r($sizeQtyArr);
			$width = 900;
			$others_col_width = 720;
			$dif = $width - $others_col_width;
			$total_size = count($bundle_size_arr);
			$size_width = $dif / $total_size;
			$table_width=1000+($total_size*$size_width);
			?>
			<table class="details-view rpt_table" style="font-size:12px; margin-top:5px;" width="100%" cellspacing="0" cellpadding="0" border="1" align="left">
                <thead bgcolor="#dddddd" align="center">
				<tr>
                    <th class="outer"  width="30" rowspan="2">SL</th>
                    <th class="inner" width="80" rowspan="2">Cutting No.</th>
                    <th class="inner" width="130" rowspan="2">PO Number</th>
                    <th class="inner" width="100" rowspan="2">Item</th>
                    <th class="inner" width="100" rowspan="2">Style Ref</th>
                    <th class="inner" width="100" rowspan="2">Color</th>
                    <th class="inner" width="100" rowspan="2">Country</th>
                    <th class="inner" width="70" rowspan="2">Cut No</th>
					<th class="inner" align="center" width="<? echo $size_width;?>" colspan="<? echo count($bundle_size_arr); ?>">Size Breakdown</th>
					<th class="inner" width="70" rowspan="2">QC. Qty</th>
                    <th class="inner" width="70" rowspan="2">Prod. Qty</th>
                    <th class="outer"  width="70" rowspan="2">Rej. Qty</th>	
				</tr>
				<tr style="border: #000;">
                    <?
                    $i=0;
                    foreach($bundle_size_arr as $inf)
                    {
                        ?>
                        <th class="inner" align="center" width="<? echo $size_width;?>" rowspan="2"><? echo $size_library[$inf]; ?></th>
                        <?
                    }
                    ?>
                 </tr>
                </thead>
                <tbody>
				<?
				    $i=1; 
					$bundel_total_qty=0;$production_total_qty=0;$qc_total_qty=0; $reject_qty=0;
					foreach ($print_sumarry_del_arr as $cut_data => $po_no_arr ) 
					{
						foreach ($po_no_arr as $po_data => $color_id_arr ) 
						{
							foreach ($color_id_arr as $key_data => $row ) 
							{
								$CutNumber = $row["CUT_NO"];
                                $cutnumbox = explode("-", $CutNumber);
								$cutnumbers = ltrim($cutnumbox[2], '0');

								 $key=$row[csf('country_id')].$row[csf('po_number')].$row[csf('color_number_id')].$row[csf('buyer_style_ref')];
								$production_total_size_qty=0;$qc_total_size_qty=0;
								$i % 2 == 0 ? $bgcolor = "#FFFFFF" : $bgcolor = "#E9F3FF";
									?>
										<tr bgcolor="<?=$bgcolor?>" >
											<td class="outer" align="center"><?=$i?></td>
											<td class="inner" align="center"><?=$cutnumbers?></td>
											<td class="inner" align="center"><?=$row["PO_NUMBER"]?></td>
											<td class="inner" align="center"><?=$garments_item[$row["ITEM_NUMBER_ID"]]?></td>
											<td class="inner" align="center"><?=$row["BUYER_STYLE_REF"]?></td>
											<td class="inner" align="center"><?=$color_arr[$row["COLOR_NUMBER_ID"]]?></td>
											<td class="inner" align="center"><?=$country_library[$row["COUNTRY_ID"]]?></td>
											<td class="inner" align="center"><?=$order_no_arr[$row["CUT_NO"]]["ORDER_CUT_NO"];?></td>
											<?
											foreach($bundle_size_arr as $size_id)
											{
												$size_qty=0;
												$size_qty=$sizeQtyArr[$key][$size_id];
												?>
												<td class="inner" class="inner_two" align="center" width="<? echo $size_width;?>"><? echo $size_qty; ?></td>
												<?
												$grand_total_size_arr[$size_id]+=$size_qty;
											}
											?>
											<td class="inner" align="right"><?=$row["QUANTITY"]?></td>
											<td  class="inner" align="right"><?=$row["PRODUCTION_QNTY"]?></td>
											<td class="outer" align="right"><?=$row["PRODUCTION_QNTY"]-$row["QUANTITY"]?></td>
										
										</tr> 
									<?
									$i++;
									//$bundel_total_qty+=$row["BUNDEL_QTY"];
									$production_total_qty+=$row["PRODUCTION_QNTY"];
									$qc_total_qty+=$row["QUANTITY"];
									$reject_qty+=$row["PRODUCTION_QNTY"]-$row["QUANTITY"];
									$grand_total_bundle_num+=count($bundleArr[$key]);
							} 
						}
					}
						?>	
					</tbody>
					<tfoot>
							<tr bgcolor="#dddddd">								
								<td colspan="8" class="outer" align="right" ><b>Total Qty-</b></td>
								<?
									foreach($bundle_size_arr as $size_id)
									{   
										?>
										<td class="inner" align="center" width="<? echo $size_width;?>">
										<b>
											<? 
											echo $grand_total_size_arr[$size_id];
											?>
										</b></td>
										<?
									}
								?>
								<td class="inner" align="right" ><b><?=$qc_total_qty?></b></td>
								<td class="inner" align="right" ><b><?=$production_total_qty?></b></td>
								<td align="right" class="outer" ><b><?=$reject_qty?></b></td>
							</tr>
							 <tr bgcolor="#dddddd" height="26"> 
								<td colspan="8" class="outer" align="right" ><b>Bundle Qty-</b></td>
								<? 
								foreach($bundle_size_arr as $size_id)
								{   
									?>
									<td class="inner" align="center" width="<?//echo $size_width;?>">
									<b>
										<?  
										//echo $size_wise_bundle_num_arr[$size_id];
										?>
									</b></td>
									<? 
								}
								?>
								<td align="center" class="outer" colspan="3"><b><?//=$grand_total_bundle_num?></b></td>							
							</tr> 
					</tfoot>
            </table>
 		<? 	
 	  /* $sql_wo_del_dtls="SELECT f.bundle_no,f.production_qnty ,g.size_number_id, g.color_number_id,b.body_part, d.quantity, p.number_start,p.number_end,p.pattern_no,f.cut_no
		from  subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e, pro_garments_production_dtls f , wo_po_color_size_breakdown g, printing_bundle_receive_dtls h, ppl_cut_lay_bundle p
		where e.id=d.mst_id and e.id='$data[1]' and b.id=d.wo_dtls_id and d.wo_id=c.id and b.mst_id=c.id and d.bundle_dtls_id=f.id and f.color_size_break_down_id=g.id and  h.id=d.rcv_dtls_id and p.bundle_no=f.bundle_no and d.entry_form=499 and e.entry_form=499 and d.mst_id='$data[1]' and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted=0 order by length(f.bundle_no) asc,f.bundle_no asc";*/
		
		 $sql_wo_del_dtls="SELECT f.bundle_no,f.production_qnty ,g.size_number_id, g.color_number_id,b.body_part, d.quantity, p.number_start,p.number_end,p.pattern_no,f.cut_no
		from  subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e, pro_garments_production_dtls f , wo_po_color_size_breakdown g, printing_bundle_receive_dtls h, ppl_cut_lay_bundle p
		where e.id=d.mst_id and e.id='$data[1]' and b.id=d.wo_dtls_id and d.wo_id=c.id and b.mst_id=c.id and d.bundle_dtls_id=f.id and f.color_size_break_down_id=g.id and  h.id=d.rcv_dtls_id and p.bundle_no=f.bundle_no and d.entry_form=499 and e.entry_form=499 and d.mst_id='$data[1]' and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted=0   and p.status_active =1 and p.is_deleted=0  and h.status_active =1 and h.is_deleted=0 and g.status_active =1 and g.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0   order by length(f.bundle_no) asc,f.bundle_no asc";
		$sql_dtls_res=sql_select($sql_wo_del_dtls);

		$print_del_arr=array();
		foreach($sql_dtls_res as $row){

			$print_del_arr[$row['CUT_NO']][$row['SIZE_NUMBER_ID']][$row['PATTERN_NO']][$row["BUNDLE_NO"]]["SIZE_NUMBER_ID"]=$row["SIZE_NUMBER_ID"];
			$print_del_arr[$row['CUT_NO']][$row['SIZE_NUMBER_ID']][$row['PATTERN_NO']][$row["BUNDLE_NO"]]["NUMBER_START"]=$row["NUMBER_START"];
			$print_del_arr[$row['CUT_NO']][$row['SIZE_NUMBER_ID']][$row['PATTERN_NO']][$row["BUNDLE_NO"]]["NUMBER_END"]=$row["NUMBER_END"];
			$print_del_arr[$row['CUT_NO']][$row['SIZE_NUMBER_ID']][$row['PATTERN_NO']][$row["BUNDLE_NO"]]["BUNDLE_NO"]=$row["BUNDLE_NO"];
			$print_del_arr[$row['CUT_NO']][$row['SIZE_NUMBER_ID']][$row['PATTERN_NO']][$row["BUNDLE_NO"]]["PRODUCTION_QNTY"]=$row["PRODUCTION_QNTY"];
			$print_del_arr[$row['CUT_NO']][$row['SIZE_NUMBER_ID']][$row['PATTERN_NO']][$row["BUNDLE_NO"]]["BUNDEL_QTY"]=$row["BUNDEL_QTY"];
			$print_del_arr[$row['CUT_NO']][$row['SIZE_NUMBER_ID']][$row['PATTERN_NO']][$row["BUNDLE_NO"]]["QUANTITY"]=$row["QUANTITY"];
			$print_del_arr[$row['CUT_NO']][$row['SIZE_NUMBER_ID']][$row['PATTERN_NO']][$row["BUNDLE_NO"]]["PATTERN_NO"]=$row["PATTERN_NO"];
 			 
		}
		// echo "<pre>";
		// print_r($print_del_arr);
	?>
    <?
 
			$item_segment =51;
			$current_row = 1;
			$current_cols = 1;
			$column_data = '';
			$end_table = 0;
			$first = 1;
 			$serial = 1;
  
  
	    $total_prod_qty=0; $total_qc_qty=0; $total_rej_qty=0;
		foreach ($print_del_arr as $cut_no => $cut_data_arr)
        { 
			foreach ($cut_data_arr as $size_id => $bundle_data_arr)
			{ 
				$sub_total_prod_qty=0; $sub_total_qc_qty=0; $sub_total_rej_qty=0;
				foreach ($bundle_data_arr as $pattern_no => $pattern_data_arr)
				{  
				
					$sl=1;
					$pattern_total=0;
					foreach ($pattern_data_arr as $bundle_no => $row)
					{
						$sub_total_prod_qty+=$row["PRODUCTION_QNTY"];
						$sub_total_qc_qty+=$row["QUANTITY"];
						$sub_total_rej_qty+=$row["PRODUCTION_QNTY"]-$row["QUANTITY"];
						$total_prod_qty+=$row["PRODUCTION_QNTY"];
						$total_qc_qty+=$row["QUANTITY"];
						$total_rej_qty+=$row["PRODUCTION_QNTY"]-$row["QUANTITY"];
						$bundleNumber = $row["BUNDLE_NO"];
						$parts = explode("-", $bundleNumber);
						if($parts[4]!="")
						{
							$part_four="-".$parts[4];
						}
						
						//$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
						$serial % 2 == 0 ? $bgcolor = "#FFFFFF" : $bgcolor = "#E9F3FF";
						if($current_row == 1)
						{
						$column_data .=' <table class="details-view rpt_table" style="font-size:15px; margin-top:5px; margin-left: 20px;"  cellspacing="0" cellpadding="0" border="1" align="left" width="415>
									<thead bgcolor="#dddddd" align="center">
										<tr bgcolor="#dddddd">
											<td width="4%" align="center" class="inner"><b>Sl</b></td>
											<td width="18%" align="center" class="inner"><b>Size</b></td>
											<th width="12%" class="inner" align="center">Pattern</th>
											<td width="20%"align="center" class="inner"><b>Bundle No</b></td>
											<td width="15%" align="center" class="inner"><b>RMG Qty</b></td>
											<td width="10%" align="center" class="inner"><b>Prod. Qty</b></td>
											<td width="10%" align="center" class="inner"><b>QC Qty</b></td>
											<td width="10%" align="center" class="outer"><b>Rej</b></td>
										</tr>
									</thead>
									<tbody>';
									$first++;
		
						}
						$column_data .='<tr bgcolor='.$bgcolor.'>
											<td width="4%" class="inner" align="center">'.$sl.'</td>
											<td width="18%" class="inner" align="center">'.$size_arr[$row["SIZE_NUMBER_ID"]].'</td>
											<td width="12%" class="inner" align="center">'.$row["PATTERN_NO"].'</td>
											<td width="20%" class="inner" align="center">'.$parts[2]."-".$parts[3].$part_four.'</td>
											<td width="15%" class="inner" align="center">'.($row["NUMBER_START"]." - ".$row["NUMBER_END"]).'</td>
											<td width="10%" class="inner" align="right">'.$row["PRODUCTION_QNTY"].'</td>
											<td width="10%" class="inner" align="right">'.$row["QUANTITY"].'</td>
											<td width="12%" class="outer" align="right">'.($row["PRODUCTION_QNTY"]-$row["QUANTITY"]).'</td>
										</tr>';
										$serial++;
										$sl++;
									   $pattern_total++;
									   
									  // echo $current_row." ==". $item_segment."<br>"; 
									   
								if ($current_row == $item_segment)
								{
									// echo $current_row .'=='. $item_segment."<br>";
									$current_row = 1;
									$column_data .= '</tbody></table>';

									if($current_cols == 2)
									{
										$end_table = 1;
										// $table_data .= '<p style="page-break-after: always;"></p>';
										$column_data .= '<div class="pagebreak"></div><br clear="all">';
										$current_cols = 1;
										//$item_segment = 67;
										//$item_segment = 66;
										$item_segment =62;

									}
									else
									{
										$current_cols++;
									}

								}
								else
								{
									$current_row++;
								}
 										 
					}
					
					$column_data .='<tr bgcolor="#dddddd"> 
										<td width="10%" align="right" colspan="2" class="inner" align="center"><b>'."Bundle Qty".'</b></td>
										<td width="12%" class="inner" align="right"><b>'.$pattern_total.'</b></td>
										<td width="12%" class="inner" align="right"></td>
										<td width="12%" class="inner" align="right"></td>
										<td width="12%" class="inner" align="right"></td>
										<td width="12%" class="inner" align="right"></td>
										<td width="12%" class="inner" align="right"></td>
									</tr>';
				}
					$column_data .='<tr bgcolor="#dddddd"> 
										<td width="10%" align="right" colspan="5" class="inner" align="center"><b>'."Size Total".'</b></td>
										<td width="12%" class="inner" align="right"><b>'.$sub_total_prod_qty.'</b></td>
										<td width="12%" class="inner" align="right"><b>'.$sub_total_qc_qty.'</b></td>
										<td width="12%" class="outer" align="right"><b>'.$sub_total_rej_qty.'</b></td>
									</tr>';
			}
		}
				$column_data .='</tfoot>
									<tr bgcolor="#dddddd">								
										<td width="10%" align="right" colspan="5" class="inner" align="center"><b>'."Grand Total".'</b></td>
										<td width="12%" class="inner" align="right"><b>'.$total_prod_qty.'</b></td>
										<td width="12%" class="inner" align="right"><b>'.$total_qc_qty.'</b></td>
										<td width="12%" class="outer" align="right"><b>'.$total_rej_qty.'</b></td>
									</tr>
							    </tfoot></table>';
                echo $column_data;
            ?>
			<br clear="all">
    </div>
	<?
	exit();
}
?>