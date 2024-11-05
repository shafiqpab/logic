<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Barcode Status Details Report Controller.
Functionality	:	
JS Functions	:
Created by		:	Md. Minul Hasan  
Creation date 	: 	23-05-2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		 
QC Date			:	 
Comments		:
*/

header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$user_id = $_SESSION['logic_erp']["user_id"];
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//-----------------------------------------------------------------------------------------------------------


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

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "SELECT id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );	
	exit();	 
}


if($action=="create_receive_search_list_view")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","", $cbo_company_name);
	$cbo_party_name=str_replace("'","", $cbo_party_name);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$txt_search_common=str_replace("'","", $txt_search_common);
	$txt_search_challan=str_replace("'","", $txt_search_challan);
	$cbo_string_search_type=str_replace("'","", $cbo_string_search_type);
	$cbo_within_group=str_replace("'","", $cbo_within_group);
	$cbo_type=str_replace("'","", $cbo_type);
	$txt_search_string=str_replace("'","", $txt_search_string);
	$cbo_location_name=str_replace("'","", $cbo_location_name);


	$search_type =$cbo_string_search_type;
	$within_group =$cbo_within_group;
	$location =$cbo_location_name;
	$search_by=str_replace("'","",$cbo_type);
	$search_str=trim(str_replace("'","",$txt_search_string));

	if ($cbo_company_name!=0) $company=" and a.company_id='$cbo_company_name'"; else { echo "Please Select Company First."; die; }
	if ($txt_search_common=='' && $txt_search_challan=='' && $search_str=='' && $txt_date_from=="" &&  $txt_date_to==""){
		echo "Please Select Date Range"; die; 
	} 

	if ($cbo_party_name!=0) $buyer_cond=" and a.party_id='$cbo_party_name'"; else $buyer_cond="";
	if ($location!=0) $location_cond=" and a.location_id='$location'"; else $location_cond="";
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($db_type==0)
	{ 
		if ($txt_date_from!="" &&  $txt_date_to!="") $recieve_date = "and a.subcon_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'"; else $recieve_date ="";
	}
	else
	{
		if ($txt_date_from!="" &&  $txt_date_to!="") $recieve_date = "and a.subcon_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."'"; else $recieve_date ="";
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
		if ($txt_search_common!='') $rec_id_cond=" and a.prefix_no_num='$txt_search_common'"; else $rec_id_cond="";
		if ($txt_search_challan!='') $challan_no_cond=" and a.chalan_no='$txt_search_challan'"; else $challan_no_cond="";
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
		if ($txt_search_common!='') $rec_id_cond=" and a.prefix_no_num like '%$txt_search_common%'"; else $rec_id_cond="";
		if ($txt_search_challan!='') $challan_no_cond=" and a.chalan_no like '%$txt_search_challan%'"; else $challan_no_cond="";
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
		if ($txt_search_common!='') $rec_id_cond=" and a.prefix_no_num like '$txt_search_common%'"; else $rec_id_cond="";
		if ($txt_search_challan!='') $challan_no_cond=" and a.chalan_no like '$txt_search_challan%'"; else $challan_no_cond="";
		if ($txt_search_string!='') $order_no_cond=" and order_no like '$txt_search_string%'"; else $order_no_cond="";
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
		if ($txt_search_common!='') $rec_id_cond=" and a.prefix_no_num like '%$txt_search_common'"; else $rec_id_cond="";
		if ($txt_search_challan!='') $challan_no_cond=" and a.chalan_no like '%$txt_search_challan'"; else $challan_no_cond="";
	}	
	
	$size_arr=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');
	$color_arr=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
	$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	
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

	$sql="SELECT c.id as bundl_mst_id, a.chalan_no, c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.pcs_per_bundle, c.no_of_bundle, c.bundle_qty, c.remarks,c.bundle_num_prefix_no,d.id as bundle_dtls_id, d.mst_id, d.item_rcv_id, d.pcs_per_bundle, d.bundle_qty, d.barcode_no, d.barcode_id, d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id as rcv_dtls_id, a.embl_job_no,a.party_id,a.location_id,a.id as rcv_id,a.subcon_date,a.sys_no, $insert_date_cond as year
	from prnting_bundle_mst c, prnting_bundle_dtls d, sub_material_dtls b, sub_material_mst a  where  c.id = d.mst_id and c.item_rcv_dtls_id = b.id and d.item_rcv_id = b.mst_id and d.item_rcv_id = a.id and a.id = b.mst_id and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond $spo_idsCond $po_idsCond $location_cond 
	group by c.id , a.chalan_no, c.company_id, c.po_id, c.order_breakdown_id, c.item_rcv_dtls_id, c.pcs_per_bundle, c.no_of_bundle, c.bundle_qty, c.remarks,c.bundle_num_prefix_no,d.id , d.mst_id, d.item_rcv_id, d.pcs_per_bundle, d.bundle_qty, d.barcode_no, d.barcode_id , d.bundle_no , b.job_dtls_id, b.job_break_id, b.buyer_po_id, b.id, a.embl_job_no,a.party_id,a.location_id,a.id ,a.subcon_date,a.sys_no, a.insert_date order by b.id";
	//and d.id not in (select barcode_no from tmp_barcode_no where userid=$user_id and entry_form=495) 
	$result = sql_select($sql);
	foreach($result as $row){
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

		$sql_job="SELECT a.within_group,a.company_id , b.buyer_po_no, a.id, a.embellishment_job, a.order_id, a.order_no, b.id as po_id, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty, b.order_uom,b.gmts_item_id,b.buyer_buyer,b.buyer_style_ref,b.main_process_id,b.embl_type,b.body_part
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
			$job_arr[$row[csf('po_id')]][$row[csf('breakdown_id')]]['buyer_po_no']=$row[csf('buyer_po_no')];
		}
	}

	$sql_issue_qty ="select b.id, c.id as bundle_dtls_id, c.barcode_no, b.rcv_id,b.rcv_dtls_id,b.bundle_mst_id, sum(b.quantity) as issue_qty from printing_bundle_issue_mst  a, printing_bundle_issue_dtls b, prnting_bundle_dtls c where a.id=b.mst_id and a.entry_form=495 and b.entry_form=495 and c.id=b.bundle_dtls_id group by b.id, c.id, c.barcode_no, b.rcv_id,b.rcv_dtls_id,b.bundle_mst_id,b.bundle_dtls_id";
	$sql_issue_qty =sql_select($sql_issue_qty);

	foreach ($sql_issue_qty as $row) 
	{
		$total_issue_arr[$row[csf('barcode_no')]][$row[csf('bundle_dtls_id')]] = $row[csf('issue_qty')];
	}

	$sql_production_qty ="select b.id, c.id as bundle_dtls_id, c.barcode_no, b.rcv_id,b.rcv_dtls_id,b.bundle_mst_id, sum(b.quantity) as production_qty from printing_bundle_issue_mst  a, printing_bundle_issue_dtls b, prnting_bundle_dtls c where a.id=b.mst_id and a.entry_form=497 and b.entry_form=497 and c.id=b.bundle_dtls_id group by b.id, c.id, c.barcode_no, b.rcv_id,b.rcv_dtls_id,b.bundle_mst_id,b.bundle_dtls_id";
	$sql_production_qty =sql_select($sql_production_qty);

	foreach ($sql_production_qty as $row) 
	{
		$total_production_qty_arr[$row[csf('barcode_no')]][$row[csf('bundle_dtls_id')]] = $row[csf('production_qty')];
	}


	$sql_qc_qty ="select b.id, c.id as bundle_dtls_id, c.barcode_no, b.rcv_id,b.rcv_dtls_id,b.bundle_mst_id, sum(b.quantity) as production_qty from printing_bundle_issue_mst  a, printing_bundle_issue_dtls b, prnting_bundle_dtls c where a.id=b.mst_id and a.entry_form=498 and b.entry_form=498 and c.id=b.bundle_dtls_id group by b.id, c.id, c.barcode_no, b.rcv_id,b.rcv_dtls_id,b.bundle_mst_id,b.bundle_dtls_id";
	$sql_qc_qty =sql_select($sql_qc_qty);

	foreach ($sql_qc_qty as $row) 
	{
		$total_qc_qty_arr[$row[csf('barcode_no')]][$row[csf('bundle_dtls_id')]] = $row[csf('production_qty')];
	}

	$sql_defect_qty ="select b.id, c.id as bundle_dtls_id, c.barcode_no, b.rcv_id,b.rcv_dtls_id,b.bundle_mst_id, b.defect_qty from printing_bundle_issue_mst  a, printing_bundle_issue_dtls b, prnting_bundle_dtls c where a.id=b.mst_id and a.entry_form=498 and b.entry_form=498 and c.id=b.bundle_dtls_id group by b.id, c.id, c.barcode_no, b.rcv_id,b.rcv_dtls_id,b.bundle_mst_id,b.bundle_dtls_id, b.defect_qty";
	$sql_defect_qty =sql_select($sql_defect_qty);

	foreach ($sql_defect_qty as $row) 
	{
		$sql_defect_arr[$row[csf('barcode_no')]][$row[csf('bundle_dtls_id')]] = $row[csf('defect_qty')];
	}


	$sql_delevery_qty ="select b.id, c.id as bundle_dtls_id, c.barcode_no, b.rcv_id,b.rcv_dtls_id,b.bundle_mst_id, sum(b.quantity) as delevery_qty from printing_bundle_issue_mst  a, printing_bundle_issue_dtls b, prnting_bundle_dtls c where a.id=b.mst_id and a.entry_form=499 and b.entry_form=499 and c.id=b.bundle_dtls_id group by b.id, c.id, c.barcode_no, b.rcv_id,b.rcv_dtls_id,b.bundle_mst_id,b.bundle_dtls_id";
	$sql_delevery_qty =sql_select($sql_delevery_qty);

	foreach ($sql_delevery_qty as $row) 
	{
		$total_delevery_qty_arr[$row[csf('barcode_no')]][$row[csf('bundle_dtls_id')]] = $row[csf('delevery_qty')];
	}
	ob_start();
	?>

    <div>
    	<table width="100%" cellspacing="0" >
            <tr style="border:none;">
                <td colspan="37" align="center" style="border:none; font-size:14px;">
                    <b><?php echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></b>
                </td>
            </tr>
        </table>
        <div style="width:1650px;" >
        <table width="1650" cellspacing="0" border="1" class="rpt_table" rules="all">
                <thead>
                	<tr>
                        <th width="20">SL</th>
                        <th width="100">Customer</th>
                        <th width="100">Work Order</th>
                        <th width="100">Challan</th>
                        <th width="130">Buyer</th>
                        <th width="130">Style</th>
                        <th width="100">Job No</th>
                        <th width="100">PO No</th>
                        <th width="80">Color</th>
                        <th width="80">Size</th>
                        <th width="80">Barcode No</th>
                        <th width="80" >Barcode Quantity</th>
                        <th width="80" >Issue Quantity</th>
                        <th width="80" >Porduction Quantity</th>
                        <th width="80" >Quality Quantity</th>
                        <th width="80" >Print Reject</th>
                        <th width="80" >Fabric Reject</th>
                        <th width="80" >Short Quantity</th>
                        <th width="80" >Total Reject</th>
                        <th width="80" >Delivery Quantity</th>
                    </tr>
                </thead>
            </table>
        </div>
            <div id="list_div" style="width:1650px; max-height:400px; overflow-y:scroll; float:left" >
			<table cellpadding="0" cellspacing="2"  width="1650" class="rpt_table" rules="all" align="left">

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
						$buyer_po_no=$job_arr[$row[csf('job_dtls_id')]][$row[csf('job_break_id')]]['buyer_po_no'];

						if ($within_group==1) {
							$customer = $company_arr[$row[csf('party_id')]];
							//$buyer = $company_arr[$row[csf('buyer_name')]];
						}
						else{
							$customer = $buyer_arr[$row[csf('party_id')]];
							//$buyer = $buyer_arr[$row[csf('buyer_name')]];
						}

						$tatal_defect_qty = $sql_defect_arr[$row[csf('barcode_no')]][$row[csf('bundle_dtls_id')]];

						$data=explode('_',$tatal_defect_qty);

						$total_reject = $data[0]+$data[1]+$data[2];

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                        <td width="20"><? echo $i;?></td>
						<td width="100"><?php echo $customer;?></td>
						<td width="100"><?php  echo $order_no;?></td>
						<td width="100"><?php  echo $row[csf('chalan_no')];?></td>
						<td width="130"><?php  echo $buyer_buyer;?></td>
						<td width="130"><?php  echo $style;?></td>
						<td width="100"><?php  echo $embellishment_job;?></td>
						<td width="100"><?php  echo $buyer_po_no;?></td>
						<td width="80"><?php  echo $color_arr[$color_id];?></td>
						<td width="80"><?php echo $size_arr[$size_id];?></td>
						<td width="80"><?php echo $row[csf('barcode_no')];?></td>
						<td width="80"><?php echo $row[csf('bundle_qty')];?></td>
						<td width="80"><?php echo $total_issue_arr[$row[csf('barcode_no')]][$row[csf('bundle_dtls_id')]];?></td>
						<td width="80"><?php echo $total_production_qty_arr[$row[csf('barcode_no')]][$row[csf('bundle_dtls_id')]];?></td>
						<td width="80"><?php echo $total_qc_qty_arr[$row[csf('barcode_no')]][$row[csf('bundle_dtls_id')]];?></td>
						<td width="80"><?php echo $data[0]?></td>
						<td width="80"><?php echo $data[1]?></td>
						<td width="80"><?php echo $data[2]?></td>
						<td width="80"><?php echo $total_reject;?></td>
						<td width="80"><?php echo $total_delevery_qty_arr[$row[csf('barcode_no')]][$row[csf('bundle_dtls_id')]];?></td>
					</tr>
						<?
						$i++;
	                }
	                ?>
	            </tbody>
				</table>
	        </div>

    </div>
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
    echo "$html**$filename**$report_type";
    exit();
}