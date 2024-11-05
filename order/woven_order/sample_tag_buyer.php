<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create for sample tag buyer
Functionality	:	
JS Functions	:
Created by		:	Zakaria Joy 
Creation date 	: 	12-04-2020	
Updated by 		: 		
Update date		: 
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Info","../../", 1, 1, $unicode,1,'');
$con = connect();
if($db_type==0)
{
	mysql_query("BEGIN");
}
$id_sm=return_next_id( "id", "wo_po_sample_approval_info", 1 ) ;
$sample_tag=sql_select("select tag_sample, buyer_id, sequ from lib_buyer_tag_sample where sequ!=0 and buyer_id=65 order by sequ"); //and buyer_id=65
/*$buyerId="";
foreach($sample_tag as $srow)
{
	$buyerId.=$srow[csf("buyer_id")].",";
}
$buyer_Id=array_filter(array_unique(explode(",",$buyerId)));
$buyerIds=implode(",",$buyer_Id);

$bidCount=count($buyer_Id);

//echo $bidCount; die;
$buyeridcond="";
if($db_type==2 && $bidCount>1000)
{
	$buyeridcond=" and (";
	
	$buyerIdsArr=array_chunk(explode(",",$buyerIds),999);
	foreach($buyerIdsArr as $ids)
	{
		$ids=implode(",",$ids);
		$buyeridcond.=" a.buyer_name in($ids) or ";
	}
	$buyeridcond=chop($buyeridcond,'or ');
	$buyeridcond.=")";
}
else
{
	$buyeridcond=" and a.buyer_name in ($buyerIds)";
}*/

$field_array_sm="id,job_no_mst,po_break_down_id,color_number_id,sample_type_id,inserted_by,insert_date,status_active,is_deleted";
//$data_array_sample=sql_select("select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.id=b.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id");
$data_sample = "SELECT a.job_no, b.id as po_id, c.color_number_id, min(c.id) as color_size_table_id from wo_po_details_master a join wo_po_break_down b on a.job_no=b.job_no_mst join wo_po_color_size_breakdown c on b.job_no_mst=c.job_no_mst and b.id=c.po_break_down_id where a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and a.company_name in (3) and a.buyer_name=65 group by a.job_no, b.id, c.color_number_id order by b.id"; //a.buyer_name=65 and company_name in (6) and $buyeridcond
//echo $data_sample; die;
$data_array_sample = sql_select($data_sample); 
$poId=array();
foreach ($data_array_sample as $rowpo)
{
	//$poId.=$rowpo[csf("po_id")].",";
	$poId[$rowpo[csf("po_id")]]=$rowpo[csf("po_id")];
}
//$po_Id=array_filter(array_unique(explode(",",$poId)));


$poidCount=count($poId);

$poidcond="";
if($db_type==2 && $poidCount>700)
{
	$poidcond=" and (";
	
	//$poidcondArr=array_chunk(explode(",",$poIds),699);
	$poidcondArr=array_chunk($poId,699);
	foreach($poidcondArr as $ids)
	{
		$ids=implode(",",$ids);
		$poidcond.=" po_break_down_id in($ids) or ";
	}
	$poidcond=chop($poidcond,'or ');
	$poidcond.=")";
}
else
{
	$poIds=implode(",",$poId);
	$poidcond=" and po_break_down_id in ($poIds)";
}

$dup_data=sql_select("select id as id, job_no_mst, po_break_down_id, color_number_id, sample_type_id from wo_po_sample_approval_info where status_active=1 and is_deleted=0 $poidcond group by id, job_no_mst, po_break_down_id, color_number_id, sample_type_id");
//echo "select id as id, job_no_mst, po_break_down_id, color_number_id, sample_type_id from wo_po_sample_approval_info where status_active=1 and is_deleted=0 $poidcond group by id, job_no_mst, po_break_down_id, color_number_id, sample_type_id"; die;
$dupdataArr=array();
foreach($dup_data as $drow)
{
	$dupdataArr[$drow[csf('job_no_mst')]][$drow[csf('po_break_down_id')]][$drow[csf('color_number_id')]][$drow[csf('sample_type_id')]]=$drow[csf('id')];
}
unset($dup_data);
/*echo '<pre>';
print_r($dupdataArr);*/
foreach($sample_tag as $sample_tag_row)
{
	foreach ( $data_array_sample as $row_sam1 )
	{
		$dup_data_count=0;
		//echo $row_sam1[csf('job_no')].'--'.$row_sam1[csf('po_id')].'--'.$row_sam1[csf('color_size_table_id')].'--'.$sample_tag_row[csf('tag_sample')].'<br>';
		$dup_data_count=$dupdataArr[$row_sam1[csf('job_no')]][$row_sam1[csf('po_id')]][$row_sam1[csf('color_size_table_id')]][$sample_tag_row[csf('tag_sample')]]*1;
		//sql_select("select id from wo_po_sample_approval_info where job_no_mst='".$row_sam1[csf('job_no')]."' and po_break_down_id=".$row_sam1[csf('po_id')]." and color_number_id=".$row_sam1[csf('color_size_table_id')]." and sample_type_id='40' and status_active=1 and is_deleted=0");
		//if( count($dup_data) == 0)
		//echo $dup_data_count; die();
		if($dup_data_count == 0)
		{
			$insert_arr[$id_sm]= "(".$id_sm.",'".$row_sam1[csf('job_no')]."',".$row_sam1[csf('po_id')].",".$row_sam1[csf('color_size_table_id')].",'".$sample_tag_row[csf('tag_sample')]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			/*if ($sam!=1) $data_array_sm .=",";
			$data_array_sm .="(".$id_sm.",'".$row_sam1[csf('job_no')]."',".$row_sam1[csf('po_id')].",".$row_sam1[csf('color_size_table_id')].",'".$sample_tag_row[csf('tag_sample')]."',1,0)";*/
			$id_sm=$id_sm+1;
		}
	}
}
/*echo '<pre>';
print_r($insert_arr);
echo count($insert_arr); die;*/
$k=1;
if(count($insert_arr)>0)
{
	$data_summary=array_chunk($insert_arr,100);
	foreach ($data_summary as $data) {
		$sam =1; $data_array_sm ='';
		foreach ($data as $row) {
			if ($sam!=1) $data_array_sm .=",";
			//$data_array_sm .='"'.$row.'"';
			$data_array_sm .= $row;
			$sam++;
		}
		if($data_array_sm !='')
		{
			//$data_array_data = str_replace('"','',$data_array_sm);
			//echo $data_array_sm; die;
			$rID3=sql_insert("wo_po_sample_approval_info",$field_array_sm,$data_array_sm,1);
			if($rID3==1) $flag=1; else $flag=0;
		}
		$insert_rec[$k] = $flag;
		$k++;
	}
}
else
{
	$flag=1;
	echo "No Data found"; die;
}


if($db_type==0)
{
	if($flag==1)
	{
		mysql_query("COMMIT");
		echo "Successfully insert ". count($insert_arr). " Data";
	}
	else
	{
		mysql_query("ROLLBACK");
		echo '<pre>';
		print_r($insert_rec);
	}
}
else if($db_type==2 || $db_type==1 )
{
	if($flag==1)
	{
		oci_commit($con);
		echo "Successfully insert ". count($insert_arr). " Data";
	}
	else
	{
		oci_rollback($con);
		echo '<pre>';
		print_r($insert_rec);
	}
}



