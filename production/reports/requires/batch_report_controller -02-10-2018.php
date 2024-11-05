<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

$search_by_arr=array(1=>"Date Wise Report",2=>"Wait For Heat Setting",5=>"Wait For Singeing",3=>"Wait For Dyeing",4=>"Wait For Re-Dyeing");//--------------------------------------------------------------------------------------------------------------------
if($action=="batchnumbershow")
{
	echo load_html_head_contents("Batch Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script type="text/javascript">
  function js_set_value(id)
	  { 
		document.getElementById('selected_id').value=id;
		  parent.emailwindow.hide();
	  }
</script>
<input type="hidden" id="selected_id" name="selected_id" /> 
<?
if($db_type==0) $year_field_grpby="GROUP BY batch_no"; 
else if($db_type==2) $year_field_grpby=" GROUP BY batch_no,id,batch_no,batch_for,booking_no,color_id,batch_weight order by batch_no desc";
 $sql="select id,batch_no,batch_for,booking_no,color_id,batch_weight from pro_batch_create_mst where company_id=$company_name and is_deleted = 0 $year_field_grpby ";	
$arr=array(1=>$color_library);
	echo  create_list_view("list_view", "Batch no,Color,Booking no, Batch for,Batch weight ", "100,100,100,100,70","520","350",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,color_id,0,0,0", $arr , "batch_no,color_id,booking_no,batch_for,batch_weight", "employee_info_controller",'setFilterGrid("list_view",-1);','0') ;
	exit();
}//batchnumbershow;
/*if($action=="load_drop_down_buyer")
{ 
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
    echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	exit();
}//cbo_buyer_name_td*/
if($action=="load_drop_down_buyer")
{ 
	echo load_html_head_contents("Buyer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data=explode('_',$data);
	$company=$data[0];
	$report_type=$data[1];
	//echo $report_type;
	if($report_type==1 || $report_type==3)
	{
    echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	}
	else if($report_type==2)
	{
		 echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	}
	else if($report_type==0)
	{
		echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,2,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	}
	else
	{
	 echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","");	
	}
	exit();
}
if($action=="jobnumbershow")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data=explode('_',$data);
	// $report_type=$data[3];
	// print_r($data);
	//echo $batch_type."AAZZZ";
?>
<script type="text/javascript">
  function js_set_value(id)
	  {
		document.getElementById('selected_id').value=id;
		  parent.emailwindow.hide();
	  }
</script>
<input type="hidden" id="selected_id" name="selected_id" /> 
<?
if($db_type==0) $year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year"; 
else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
if($db_type==0) $year_field_grpby="GROUP BY a.job_no order by b.id desc"; 
else if($db_type==2) $year_field_grpby=" GROUP BY a.job_no,a.id,a.buyer_name,a.style_ref_no,a.gmts_item_id,b.po_number,a.job_no_prefix_num,a.insert_date,b.id order by b.id desc";
$year_job = str_replace("'","",$year);
if(trim($year)!=0) $year_cond=" $year_field_by=$year_job"; else $year_cond="";
//$cbo_buyer_name=($cbo_buyer_name==0)?"%%" : "%$cbo_buyer_name%";
if(trim($cbo_buyer_name)==0) $buyer_name_cond=""; else $buyer_name_cond=" and a.buyer_name=$cbo_buyer_name";
if(trim($cbo_buyer_name)==0) $sub_buyer_name_cond=""; else $sub_buyer_name_cond=" and a.party_id=$cbo_buyer_name";
if ($batch_type==0 || $batch_type==1)
{
	$sql="select a.id,a.buyer_name,a.style_ref_no,a.gmts_item_id,b.po_number,a.job_no_prefix_num as job_prefix,$year_field from wo_po_details_master a,wo_po_break_down b where b.job_no_mst=a.job_no and a.company_name=$company_id $buyer_name_cond $year_cond and a.is_deleted=0 $year_field_grpby";	
}
else
{
$sql="select a.id,a.party_id as buyer_name,c.item_id as gmts_item_id,b.cust_style_ref as style_ref_no,b.order_no as po_number,a.job_no_prefix_num as job_prefix,$year_field from  subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where b.job_no_mst=a.subcon_job and a.id=c.mst_id and b.id=c.order_id and a.company_id=$company_id  $sub_buyer_name_cond $year_cond and a.is_deleted=0 group by a.id,a.party_id,b.cust_style_ref,b.order_no ,a.job_no_prefix_num,a.insert_date,c.item_id";
}
//$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

?>
<table width="500" border="1" rules="all" class="rpt_table">
	<thead>
     <tr><th colspan="7"><? if($batch_type==0 || $batch_type==1)
		 { echo "Self Batch Order";} else if($batch_type==2) { echo "SubCon Batch Order";}?>  </th></tr>
        <tr>
            <th width="30">SL</th>
            <th width="100">Po number</th>
            <th width="50">Job no</th>
            <th width="40">Year</th>
            <th width="100">Buyer</th>
            <th width="100">Style</th>
            <th>Item Name</th>
        </tr>
   </thead>
</table>
<div style="max-height:300px; overflow:auto;">
<table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
 <? $rows=sql_select($sql);
	 $i=1;
	 foreach($rows as $data)
	 {
		 	if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
  ?>
	<tr bgcolor="<? echo  $bgcolor;?>" onclick="js_set_value('<? echo $data[csf('job_prefix')]; ?>')" style="cursor:pointer;">
		<td width="30"><? echo $i; ?></td>
		<td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
		<td align="center"  width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
        <td align="center" width="40"><p><? echo $data[csf('year')]; ?></p></td>
		<td width="100"><p><? echo $buyer_arr[$data[csf('buyer_name')]]; ?></p></td>
		<td width="100"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
		<td><p><? 
		$itemid=explode(",",$data[csf('gmts_item_id')]);
		foreach($itemid as $index=>$id){
			echo ($itemid[$index]==end($itemid))? $garments_item[$id] : $garments_item[$id].', ';
		}
		?></p></td>
	</tr>
    <? $i++; } ?>
</table>
</div>
<script> setFilterGrid("table_body2",-1); </script>
<?
	disconnect($con);
	exit();
}//JobNumberShow
if($action=="order_number_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script type="text/javascript">
  function js_set_value(id)
	  { 
		document.getElementById('selected_id').value=id;
		  parent.emailwindow.hide();
	  }
</script>
<input type="hidden" id="selected_id" name="selected_id" /> 
<?
$buyer = str_replace("'","",$buyer_name);
$year = str_replace("'","",$year);
$buyer = str_replace("'","",$buyer_name);
if($db_type==0) $year_field="SUBSTRING_INDEX(b.insert_date, '-', 1) as year"; 
else if($db_type==2) $year_field="to_char(b.insert_date,'YYYY') as year";
if($db_type==0) $year_field_by="and YEAR(b.insert_date)"; 
else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
if ($company_name==0) $company=""; else $company=" and b.company_name=$company_name";
if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
//echo $buyer;die;

//if ($buyer==0) $buyername=""; else $buyername=" and b.buyer_name=$buyer";//$cbo_buyer_name=($cbo_buyer_name==0)?"%%" : "%$cbo_buyer_name%";

if ($buyer==0) $buyername=""; else $buyername=" and b.buyer_name=$buyer";//$cbo_buyer_name=($cbo_buyer_name==0)?"%%" : "%$cbo_buyer_name%";
	if(trim($buyer)==0) $sub_buyer_name_cond=""; else $sub_buyer_name_cond=" and a.party_id=$buyer";
	if ($batch_type==0 || $batch_type==1)
	{
		$sql = "select distinct a.id,b.job_no,a.po_number,b.company_name,b.buyer_name,b.job_no_prefix_num as job_prefix,$year_field from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company $buyername $year_cond order by a.id desc"; 

	}
	else
	{
	$sql="select distinct a.id,b.job_no_mst as job_no ,a.party_id as buyer_name,a.company_id as company_name ,b.order_no as po_number,a.job_no_prefix_num as job_prefix,$year_field from  subcon_ord_mst a , subcon_ord_dtls b, subcon_ord_breakdown c where b.job_no_mst=a.subcon_job and a.id=c.mst_id and b.id=c.order_id and a.company_id=$company_name  $sub_buyer_name_cond $year_cond and a.is_deleted =0 group by a.id,a.party_id,b.job_no_mst,b.order_no ,a.job_no_prefix_num,a.company_id,b.insert_date";	
	}

$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
?>
<table width="370" border="1" rules="all" class="rpt_table">
	<thead>
    <tr><th colspan="5"><? if($batch_type==0 || $batch_type==1) echo "Self Batch Order"; else echo "SubCon Batch Order";?>  </th></tr>
        <tr>
            <th width="30">SL</th>
            <th width="100">Order Number</th>
            <th width="50">Job no</th>
            <th width="80">Buyer</th>
            <th width="40">Year</th>
        </tr>
   </thead>
</table>
<div style="max-height:300px; overflow:auto;">
<table id="table_body2" width="370" border="1" rules="all" class="rpt_table">
 <? $rows=sql_select($sql);
	 $i=1;
 foreach($rows as $data)
 {
	 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
  ?>
	<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value('<? echo $data[csf('po_number')]; ?>')" style="cursor:pointer;">
		<td width="30"><? echo $i; ?></td>
		<td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
		<td width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
		<td width="80"><p><? echo $buyer[$data[csf('buyer_name')]]; ?></p></td>
		<td width="40" align="center"><p><? echo $data[csf('year')]; ?></p></td>
	</tr>
    <? $i++; } ?>
</table>
</div>
<script> setFilterGrid("table_body2",-1); </script>
<?
	exit();
}
if($action=="batchextensionpopup")
{
	echo load_html_head_contents("Batch Ext Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script type="text/javascript">
  function js_set_value(id)
	  { 
		document.getElementById('selected_id').value=id;
		  parent.emailwindow.hide();
	  }
</script>
<input type="hidden" id="selected_id" name="selected_id" /> 
<?
$buyer = str_replace("'","",$buyer_name);
$year = str_replace("'","",$year);
$batch_number= str_replace("'","",$batch_number_show);
if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
if ($company_name==0) $company=""; else $company=" and a.company_id=$company_name";
if ($batch_number==0) $batch_no=""; else $batch_no=" and a.batch_no=$batch_number";
if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
//echo $buyer;die;
if ($buyer==0) $buyername=""; else $buyername=" and b.buyer_name=$buyer";
 $sql="select a.id,a.batch_no,a.extention_no,a.batch_for,a.booking_no,a.color_id,a.batch_weight from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.is_deleted=0 $company $batch_no ";	
$arr=array(2=>$color_library);
	echo  create_list_view("list_view", "Batch no,Extention No,Color,Booking no, Batch for,Batch weight ", "100,100,100,100,100,170","620","350",0, $sql, "js_set_value", "extention_no,extention_no", "", 1, "0,0,color_id,0,0,0", $arr , "batch_no,extention_no,color_id,booking_no,batch_for,batch_weight", "employee_info_controller",'setFilterGrid("list_view",-1);','0') ;
	exit();
}//batchnumbershow;
if($action=="batch_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company = str_replace("'","",$cbo_company_name);
	$working_company = str_replace("'","",$cbo_working_company_id);
	$buyer = str_replace("'","",$cbo_buyer_name);
	$job_number = str_replace("'","",$job_number);
	$job_number_id = str_replace("'","",$job_number_show);
	$batch_no = str_replace("'","",$batch_number_show);
	$batch_type = str_replace("'","",$cbo_batch_type);
	//echo $cbo_batch_type;die;
	//echo $batch_no;die;
	$batch_number_hidden = str_replace("'","",$batch_number);
	$ext_num = str_replace("'","",$txt_ext_no);
	$hidden_ext = str_replace("'","",$hidden_ext_no);
	$txt_order = str_replace("'","",$order_no);
	$hidden_order = str_replace("'","",$hidden_order_no);
	$cbo_type = str_replace("'","",$cbo_type);
	$year = str_replace("'","",$cbo_year);
	$file_no = str_replace("'","",$file_no);
	$ref_no = str_replace("'","",$ref_no);
	//echo $order_no;die;
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$process=33;
	//echo str_replace("'","",$batch_no);die;
	
	if ($buyer==0) $buyerdata=""; else $buyerdata="  and d.buyer_name='$buyer'";
	if ($buyer==0) $buyer_cond=""; else $buyer_cond="  and a.buyer_name='$buyer'";
	if ($batch_no=="") $batch_num=""; else $batch_num="  and a.batch_no='".str_replace("'","",$batch_no)."'";
	
	if ($file_no=="") $file_cond=""; else $file_cond="  and b.file_no='".$file_no."'";
	if ($ref_no=="") $ref_cond=""; else $ref_cond="  and b.grouping='$ref_no'";
	if ($company==0) $comp_cond=""; else $comp_cond=" and a.company_id=$company";
	if ($working_company==0) $working_comp_cond=""; else $working_comp_cond=" and a.working_company_id=$working_company";
	//a.company_id=$company
	
	if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";
	
	if($batch_type==0 || $batch_type==1 ||  $batch_type==3)
	{
		if ($txt_order=="") $order_no_cond=""; else $order_no_cond="  and b.po_number='$txt_order'";
		if ($txt_order=="") $order_no2=""; else $order_no2="  and c.po_number='$txt_order'";
		if ($job_number_id=="") $jobdata=""; else $jobdata="  and a.job_no_prefix_num in($job_number_id)";
	}
	//echo $jobdata;
	if($batch_type==0 || $batch_type==2)
	{
		if ($txt_order!='') $suborder_no="and c.order_no='$txt_order'"; else $suborder_no="";
		if ($job_number_id!='') $sub_job_cond="  and d.job_no_prefix_num in(".$job_number_id.") "; else $sub_job_cond="";
		if ($buyer==0) $sub_buyer_cond=""; else $sub_buyer_cond="  and d.party_id='".$buyer."' ";

	}
	
		if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
		else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
		if($db_type==0) $find_inset="and  FIND_IN_SET(33,a.process_id)"; 
		else if($db_type==2) $find_inset="and   ',' || a.process_id || ',' LIKE '%33%'";
		if ($ext_num=="") $ext_no=""; else $ext_no="  and a.extention_no=$ext_num ";
		if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	if($txt_date_from && $txt_date_to)
	{
	if($db_type==0)
		{
		$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
		$dates_com="and a.batch_date BETWEEN '$date_from' AND '$date_to'";
		}
	if($db_type==2)
		{
		$date_from=change_date_format($txt_date_from,'','',1);
		$date_to=change_date_format($txt_date_to,'','',1);
		$dates_com="and a.batch_date BETWEEN '$date_from' AND '$date_to'";
		}
	}
	
		$po_array=array(); 
		$po_sql=sql_select("select a.job_no_prefix_num 	as job_no,a.buyer_name, b.file_no,b.grouping as ref,b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond $order_no_cond $year_cond  $jobdata $ref_cond $file_cond");
		$poid='';
		foreach($po_sql as $row)
		{
			$po_array[$row[csf('id')]]['po_no']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['file']=$row[csf('file_no')];
			$po_array[$row[csf('id')]]['ref']=$row[csf('ref')];
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')]; 
			if($poid=='') $poid=$row[csf('id')]; else $poid.=",".$row[csf('id')];
		} //echo $poid;
		$sub_po_array=array(); 
		$sub_po_sql=sql_select("select d.job_no_prefix_num 	as job_no,d.party_id, c.id, c.order_no from subcon_ord_dtls c, subcon_ord_mst d  where d.subcon_job=c.job_no_mst  and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sub_job_cond $suborder_no $sub_buyer_cond $ref_cond $file_cond");
		$sub_poid='';
		foreach($sub_po_sql as $row)
		{
			$sub_po_array[$row[csf('id')]]['po_no']=$row[csf('order_no')];
			$sub_po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$sub_po_array[$row[csf('id')]]['buyer']=$row[csf('party_id')]; 
			if($sub_poid=='') $sub_poid=$row[csf('id')]; else $sub_poid.=",".$row[csf('id')];
		}
		//if($sub_poid=="") $sub_poid=0;else $sub_poid=$sub_poid;
		//echo $sub_poid.'gfgf';
			$po_id="";
			if($txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
			{ 
				  $po_id=$poid;
			}
			//echo $po_id;
			$sub_po_id="";
			if($txt_order!="" || $job_number_id!=""  || $year!=0)
			{ 
				   $sub_po_id=$sub_poid;
			}
			
			$po_id_cond="";
		if($po_id!="") 
		{
			//echo $po_id=substr($po_id,0,-1);
			if($db_type==0) $po_id_cond="and b.po_id in(".$po_id.")";
			else
			{
				$po_ids=explode(",",$po_id);
				if(count($po_ids)>990)
				{
					$po_id_cond="and (";
					$po_ids=array_chunk($po_ids,990);
					$z=0;
					foreach($po_ids as $id)
					{
						$id=implode(",",$id);
						if($z==0) $po_id_cond.=" b.po_id in(".$id.")";
						else $po_id_cond.=" or b.po_id in(".$id.")";
						$z++;
					}
					$po_id_cond.=")";
				}
				else $po_id_cond="and b.po_id in(".$po_id.")";
			}
		}
		//echo $po_id_cond;die;
		$sub_po_id_cond="";
		if($sub_po_id!="") 
		{
			$sub_po_id=substr($sub_po_id,0,-1);
			if($db_type==0) $sub_po_id_cond="and b.po_id in(".$sub_po_id.")";
			else
			{
				$sub_po_ids=explode(",",$sub_po_id);
				if(count($sub_po_ids)>990)
				{
					$sub_po_id_cond="and (";
					$sub_po_ids=array_chunk($sub_po_ids,990);
					$z=0;
					foreach($sub_po_ids as $id)
					{
						$id=implode(",",$id);
						if($z==0) $sub_po_id_cond.=" b.po_id in(".$id.")";
						else $sub_po_id_cond.=" or b.po_id in(".$id.")";
						$z++;
					}
					$sub_po_id_cond.=")";
				}
				else $sub_po_id_cond="and b.po_id in(".$sub_po_id.")";
			}
		}
			//echo  $sub_po_id_cond;
	//echo $po_id.'aaas';
	$yarn_lot_arr=array();
	if($db_type==0)
	{
	$yarn_lot_data=sql_select("select b.po_breakdown_id as po_id, a.prod_id,a.yarn_lot as yarnlot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!='0' group by a.prod_id,a.yarn_lot, b.po_breakdown_id");
	}
	else if($db_type==2)
	{
	$yarn_lot_data=sql_select("select b.po_breakdown_id as po_id, a.prod_id,a.yarn_lot as yarnlot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot is not null group by a.prod_id,a.yarn_lot, b.po_breakdown_id");
	}
	foreach($yarn_lot_data as $rows)
	{
		//$yarn_lot=explode(",",$rows[csf('yarnlot')]);
		$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_id')]]['lot'].=$rows[csf('yarnlot')].',';
	}
			//var_dump($yarn_lot);
	$sql_dyeing_subcon=sql_select("select batch_id from  pro_fab_subprocess where entry_form=38 and status_active=1 and is_deleted=0 and batch_id>0");
	$k=1;
	foreach($sql_dyeing_subcon as $row_sub)
	{
		if($k!==1) $sub_cond_d.=",";
		$sub_cond_d.=$row_sub[csf('batch_id')];
		$k++;
	}//echo $sub_cond;die;
	
	$sql_batch_dyeing=sql_select("select batch_id from  pro_fab_subprocess where entry_form=35 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_dyeing as $row_dyeing)
	{
		if($i!==1) $row_d.=",";
		$row_d.=$row_dyeing[csf('batch_id')];
		$i++;
	}
	$sql_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=32 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_h as $row_h)
	{
		if($i!==1) $row_heat.=",";
		$row_heat.=$row_h[csf('batch_id')];
		$i++;
	}
	/*$sub_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=32 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sub_batch_h as $subrow_h)
	{
		if($i!==1) $subrow_heat.=",";
		$subrow_heat.=$subrow_h[csf('batch_id')];
		$i++;
	}*/
	$sql_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=47 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_h as $row_s)
	{
		if($i!==1) $row_sin.=",";
		$row_sin.=$row_s[csf('batch_id')];
		$i++;
	}
	$sql_batch_dry=sql_select("select batch_id from  pro_fab_subprocess where entry_form=31 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_dry as $rowdry)
	{
		if($i!==1) $row_dry.=",";
		$row_dry.=$rowdry[csf('batch_id')];
		$i++;
	}
	$sql_batch_stenter=sql_select("select batch_id from  pro_fab_subprocess where entry_form=48 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_stenter as $row_sten)//Stentering
	{
		if($i!==1) $row_stentering.=",";
		$row_stentering.=$row_sten[csf('batch_id')];
		$i++;
	}
	if($db_type==0)
	{ 
		if($cbo_type==1)//Date Wise Report
		{
			if($batch_type==0 || $batch_type==1)
			{
				if($job_number_id!=0 || $txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
				{
					 $sql="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $dates_com  $batch_num $po_id_cond $ext_no $year_cond  GROUP BY a.id, a.batch_no, b.item_description,b.prod_id order by a.batch_date)";
				}
				else
				{
				  $sql="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.batch_against!=3  and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $ext_no $po_id_cond  $year_cond   GROUP BY a.batch_no, b.item_description,b.prod_id)
			union
			(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=0 and a.batch_against!=3  and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond  $dates_com  $batch_num $ext_no $year_cond  GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type) order by batch_date";	
				} 

			}
			else if($batch_type==0 || $batch_type==2) //SubCon
			{
				//select a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where a.company_id=$company and a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond   GROUP BY a.batch_no, d.party_id, a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.job_no_prefix_num, d.party_id
				if($job_number_id!=0 || $txt_order!="" || $file_no!="" || $ref_no!="")
				{
				 $sub_cond="select a.id,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.booking_no,a.color_id,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $working_comp_cond   $dates_com  $batch_num  $year_cond $sub_po_id_cond  $ref_cond $file_cond  GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.prod_id, b.width_dia_type order by a.batch_no";
				}
				else
				{
				$sub_cond="(select a.id,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b, pro_batch_create_mst a where   a.entry_form=36 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $comp_cond  $working_comp_cond  $batch_num $sub_po_id_cond  $year_cond   GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type)
			union
			(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,group_concat( distinct b.po_id ) AS po_id  from pro_batch_create_dtls b,pro_batch_create_mst a where   a.entry_form=36 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num $ext_no $sub_po_id_cond  $year_cond  GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type) order by batch_date";	
				}
		
			}
			else if($batch_type==0 || $batch_type==3) // Sample batch
			{
			if($job_number_id!=0 || $txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
			{
				 $sql_sam="select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.batch_against=3 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num  $ext_no $year_cond $po_id_cond  GROUP BY a.id,a.batch_no, b.item_description,b.prod_id order by a.batch_date";
			}
			else
			{
			$sql_sam="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=0 and a.batch_against=3 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num  $ext_no $year_cond GROUP BY a.id,a.batch_no, b.item_description,b.prod_id)
		union
		(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.batch_against=3 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num $ext_no $year_cond $po_id_cond  GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,b.width_dia_type)  order by batch_date";	
			}
		
			}
		}
		else if($cbo_type==2) //wait for Heat Setting
		{
			if($batch_type==0 || $batch_type==1)
			{
				if($row_h!=0)
				{
			 $sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,group_concat( distinct c.po_number ) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com $jobdata $batch_num $buyerdata $order_no2 $ext_no $year_cond  GROUP BY a.id,a.batch_no, b.item_description)
		union
		(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,null,null,null,null from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and b.po_id=0 and a.id=b.mst_id $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $comp_cond  $ext_no $year_cond  GROUP BY a.id,a.batch_no, b.item_description) order by batch_date";
				}
			}
			if($batch_type==0 || $batch_type==2)
			{
				if($row_h!=0)
				{
					$sub_cond="( select a.id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,group_concat( distinct c.order_no ) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst $dates_com $comp_cond   $batch_num  $working_comp_cond  $sub_buyer_cond $sub_job_cond $suborder_no $year_cond $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY a.id,a.batch_no, b.item_description)
		union
		(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,null,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,null,null,null,null from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=36 and b.po_id=0 and a.id=b.mst_id $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com  $batch_num  $ext_no $year_cond GROUP BY a.id, a.batch_no, b.item_description) order by batch_date";
				}
			}
		}
		else if($cbo_type==3)// Wait For Dyeing
		{
			//$find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
			//$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
			$find_inset="and  FIND_IN_SET(33,a.process_id)"; 
			$find_inset_not="and not FIND_IN_SET(33,a.process_id)"; 
			//else if($db_type==2) $find_inset="and   ',' || a.process_id || ',' LIKE '%33%'";
			if($batch_type==0 || $batch_type==1)
			{
		$sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,group_concat(distinct c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_fab_subprocess e,pro_fab_subprocess_dtls f where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and  e.batch_id=a.id and e.id=f.mst_id and b.prod_id=f.prod_id and e.entry_form=32  and a.id not in($row_d) $dates_com $jobdata $batch_num $buyerdata $order_no2 $ext_no $comp_cond $working_comp_cond  $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $find_inset GROUP BY  a.id,a.batch_no, b.po_id,b.prod_id,b.item_description)
			union
			(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,group_concat(distinct c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $comp_cond $dates_com $jobdata $batch_num $buyerdata $order_no2 $ext_no $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not GROUP BY a.id,a.batch_no,b.po_id,b.prod_id, b.item_description ) order by batch_date";
			}
			if($batch_type==0 || $batch_type==2) //SubCon Deying 
			{
		$sub_cond="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,group_concat( distinct c.order_no ) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id as buyer_name  from pro_batch_create_mst a,pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d where  a.id=b.mst_id and b.po_id=c.id  and d.subcon_job=c.job_no_mst    and a.id not in($sub_cond_d) $working_comp_cond  $comp_cond $dates_com  $batch_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond and a.entry_form=36 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0   GROUP BY a.batch_no, b.po_id,b.prod_id,b.item_description)
			union
			(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,null,null,null,null from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=36 and b.po_id=0 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id not in($sub_cond_d)  $comp_cond $working_comp_cond  $dates_com  $batch_num  $ext_no  GROUP BY a.id, a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type) order by batch_date";
			}
		}
		else if($cbo_type==4) //Wait For Re-Dyeing
		{
			if($batch_type==0 || $batch_type==1)
			{
		$sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,group_concat( distinct c.po_number ) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name
	from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and a.batch_against=2 and a.id not in($row_d) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $comp_cond  $working_comp_cond $dates_com $jobdata $batch_num $buyerdata $order_no2 $ext_no $year_cond GROUP BY a.id,a.batch_no, b.item_description)
	union
	(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,null,null,null,null
	from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=0 and a.id=b.mst_id and a.batch_against=2 and b.po_id=0 and a.id not in($row_d) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $comp_cond  $working_comp_cond $dates_com $batch_num $ext_no $year_cond GROUP BY a.id,a.batch_no, b.item_description) order by batch_date";
			}
			
		if($batch_type==0 || $batch_type==2) //SubCon Batch
		  {
			 
			$sql_subcon="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,group_concat(c.order_no) as po_number,d.subcon_job,d.job_no_prefix_num,d.party_id as buyer_name
		from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id not in($sub_cond_d)  $comp_cond $working_comp_cond $dates_com  $batch_num $sub_buyer_cond $sub_job_cond $suborder_no $ext_no $year_cond  GROUP BY a.id,a.batch_no, b.item_description)
				union
				(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,null,null,null,null from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=36 and b.po_id=0 and a.id=b.mst_id  and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id not in($sub_cond_d)  $comp_cond $working_comp_cond  $dates_com  $batch_num  $ext_no   GROUP BY a.id,a.batch_no, b.item_description) order by batch_date";
				
		 }
		}
		else if($cbo_type==5) //Wait For Singeing
		{
		if($db_type==0) $find_cond="and  FIND_IN_SET(94,a.process_id)"; 
		else if($db_type==2) $find_cond="and  ',' || a.process_id || ',' LIKE '%94%'";
		if($batch_type==0 || $batch_type==1)
			{
		$w_sing_arr=array_chunk(array_unique(explode(",",$row_sin)),999);
		if($w_sing_arr!=0)
		{
			 $sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,group_concat( distinct c.po_number ) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $comp_cond $working_comp_cond $dates_com $jobdata $batch_num $buyerdata $order_no2 $ext_no $year_cond ";
			$p=1;
			foreach($w_sing_arr as $sing_row)
			{
				if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
				$p++;
			}
			$sql .=")";
			$sql .=" GROUP BY a.id,a.batch_no, b.item_description,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name)
			union
			(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,null,null,null,null from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where   a.entry_form=0 and b.po_id=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $working_comp_cond $dates_com  $batch_num $ext_no $year_cond $comp_cond ";
			$p=1;
			foreach($w_sing_arr as $sing_row)
			{
				if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
				$p++;
			}
			$sql .=")";
			$sql .=" GROUP BY a.id,a.batch_no, b.item_description,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type) order by batch_date "; 
		}//W-end
		//echo $sql;
			}
		}
	}
	else //Oracle start here
	{
	if($cbo_type==1)//Date Wise Report
		{
 			if($batch_type==0 || $batch_type==1)
			{
		
		if($job_number_id!=0 || $txt_order!="")
		{
			//echo $order_no;
	  $sql="select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $working_comp_cond $dates_com $po_id_cond $batch_num  $ext_no $year_cond  GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form order by a.batch_date";
		}
		else
		{
			//echo $$job_number_id .'aaaa';listagg(b.po_id ,',') within group (order by b.po_id) AS 
			//echo $po_id_cond ;
		  $sql="(select a.id,a.batch_against,a.entry_form, a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,b.prod_id,b.width_dia_type from pro_batch_create_dtls b,pro_batch_create_mst a where   a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $po_id_cond $ext_no $year_cond $comp_cond  GROUP BY a.id,a.batch_against, a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form)
		union
		(select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,b.prod_id,b.width_dia_type from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com  $batch_num $ext_no $year_cond GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,b.po_id,b.prod_id,b.width_dia_type,a.entry_form) order by batch_date ";	
		}
		
			}
			else if($batch_type==0 || $batch_type==2)
			{
			if($job_number_id!=0 || $txt_order!="" || $file_no!="" || $ref_no!="")
			{
				 $sub_cond="select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no,a.batch_date,booking_no,a.batch_weight,a.color_id,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num  $year_cond  $sub_po_id_cond $ref_cond $file_cond $working_comp_cond $comp_cond  GROUP BY  a.id,a.batch_against,a.batch_no, a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, a.entry_form,b.rec_challan order by a.batch_date ";	
			}
			else
			{ 
				  $sub_cond="(select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id from pro_batch_create_dtls b, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $year_cond $sub_po_id_cond $ref_cond $file_cond $working_comp_cond  $comp_cond GROUP BY a.id,a.batch_against,a.batch_no, a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type,a.entry_form,b.rec_challan)
		union
		(select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id  from pro_batch_create_dtls b,pro_batch_create_mst a where   a.entry_form=36 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com  $batch_num $ext_no $year_cond  GROUP BY  a.id,a.batch_against, a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,b.po_id,b.prod_id,b.width_dia_type,a.entry_form,b.rec_challan) order by batch_date ";
				
			}
			
			}
			else if($batch_type==0 || $batch_type==3)//Sample Batch
			{
		if($job_number_id!=0 || $txt_order!="" || $file_no!="" || $ref_no!="" )
		{
			//echo $order_no;
	 $sql_sam="select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond  $dates_com  $batch_num $ext_no $year_cond $po_id_cond  GROUP BY a.id,a.batch_against,a.batch_no,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, b.item_description,b.prod_id,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no,b.prod_id, b.width_dia_type,a.entry_form order by a.batch_date";
		}
		else
		{
			//echo $$job_number_id .'aaaa';
		  $sql_sam="(select a.id,a.batch_against,a.entry_form, a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id  from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.batch_against=3 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com  $batch_num $po_id_cond  $ext_no $year_cond  GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no, b.prod_id, b.width_dia_type,a.entry_form)
		union
		(select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where   a.entry_form=0 and a.batch_against=2 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com  $batch_num   $ext_no $year_cond GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type,a.entry_form) order by batch_date ";	
		//echo $sql_sam;//and b.po_id=0
		}
			}
		}
		else if($cbo_type==2) //wait for Heat Setting
		{
		if($batch_type==0 || $batch_type==1)// Self batch
		{
			$w_heat_arr=array_chunk(array_unique(explode(",",$row_heat)),999);
			if($w_heat_arr!=0)
			{
				 $sql="select a.id,a.entry_form,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $find_inset  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond $dates_com $jobdata $batch_num $buyerdata $po_id_cond $ext_no $year_cond ";
				$p=1;
				foreach($w_heat_arr as $h_batch_id)
				{
					if($p==1) $sql .="and (a.id not in(".implode(',',$h_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$h_batch_id).")";
					$p++;
				}
				$sql .=")";
				$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form order by a.batch_date "; 
			} //echo $sql; 
			
		} //Batch Type End
		if($batch_type==0 || $batch_type==2) //Subcond batch
		 {
			$w_heat_arr=array_chunk(array_unique(explode(",",$row_heat)),999);
			if($w_heat_arr!=0)
			{
				 $sub_cond="select a.id,a.entry_form,a.batch_against,a.batch_no,a.batch_date,a.booking_no,a.batch_weight,a.color_id,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $comp_cond  $batch_num $sub_buyer_cond $sub_job_cond $suborder_no   $working_comp_cond $year_cond ";
				$p=1;
				foreach($w_heat_arr as $h_batch_id)
				{
					if($p==1) $sub_cond .="and (a.id not in(".implode(',',$h_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$h_batch_id).")";
					$p++;
				}
				$sub_cond .=")";
				$sub_cond .="   GROUP BY a.id, a.batch_no,a.batch_against,d.party_id,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.job_no_prefix_num, d.party_id,a.entry_form ,b.rec_challan order by a.batch_date"; 
				
			}
			//echo $sub_cond;
		 }//Batch type End
		}
		else if($cbo_type==3)// Wait For Dyeing
		{
		if($batch_type==0 || $batch_type==1)//Self Batch
		 {
		$w_dyeing_arr=array_chunk(array_unique(explode(",",$row_d)),999);
		if($w_dyeing_arr!=0)
			{
				$find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
				$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
			  $sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_fab_subprocess e,pro_fab_subprocess_dtls f where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and  e.batch_id=a.id and e.id=f.mst_id and b.prod_id=f.prod_id and e.entry_form=32 and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond  $comp_cond $working_comp_cond $dates_com $jobdata $batch_num $buyerdata $po_id_cond $ext_no $year_cond ";
				$p=1;
				foreach($w_dyeing_arr as $d_batch_id)
				{
					if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
					$p++;
				}
				$sql .=")";
				$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form)
				union
				(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $dates_com $jobdata $batch_num $comp_cond $buyerdata $po_id_cond $ext_no $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not GROUP BY a.id,a.batch_no, a.batch_against,b.item_description,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num,d.buyer_name,a.entry_form) order by batch_date "; 
				//echo $sql;//die;
			}
		 }//Self batch End
		 if($batch_type==0 || $batch_type==2) //SubCon Batch
		  {
			
		$w_dyeing_arr=array_chunk(array_unique(explode(",",$sub_cond_d)),999);
		if($w_dyeing_arr!=0)
			{
				//and a.id not in($sub_cond_d) $find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
				//$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
			  $sub_cond="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.booking_no,a.batch_weight,a.color_id,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id  from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond  $working_comp_cond  $comp_cond    and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond ";
				$p=1;
				foreach($w_dyeing_arr as $d_batch_id)
				{
					if($p==1) $sub_cond .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$d_batch_id).")";
					$p++;
				}
				$sub_cond .=")";
				$sub_cond .=" GROUP BY a.id, a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.extention_no,a.booking_no,d.party_id,b.item_description,b.po_id,b.prod_id,b.width_dia_type,d.subcon_job,d.job_no_prefix_num,d.party_id,a.entry_form,b.rec_challan)
				union
				(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.booking_no,a.batch_weight,a.color_id,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,null,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,c.job_no_mst,d.job_no_prefix_num,null from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not  $working_comp_cond $batch_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond $dates_com  $comp_cond  ";
				$p2=1;
				foreach($w_dyeing_arr as $d_batch_id)
				{
					if($p2==1) $sub_cond .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$d_batch_id).")";
					$p2++;
				}
				$sub_cond .=")";
				$sub_cond .=" GROUP BY a.id,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.extention_no,d.party_id,b.item_description,b.po_id,b.prod_id,
 b.width_dia_type,d.subcon_job, d.job_no_prefix_num,d.party_id,a.booking_no,c.job_no_mst,a.entry_form,b.rec_challan) order by a.batch_date "; 
				
			}//echo $sub_cond;
		 
		  }
			// echo $sub_cond;//die;
		}
		else if($cbo_type==4) //Wait For Re-Dyeing
		{
		
		if($batch_type==0 || $batch_type==1)//Self Batch
		 {
		$w_dyeing_arr=array_chunk(array_unique(explode(",",$row_d)),999);
		if($w_dyeing_arr!=0)
			{
		$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) as po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name
	from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com $jobdata $batch_num $buyerdata $po_id_cond $ext_no $year_cond $comp_cond";
			$p=1;
			foreach($w_dyeing_arr as $d_batch_id)
			 { //echo $d_batch_id;die;
				if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
				$p++;
			 }
			$sql .=")";
			$sql .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form)
			union
			(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,null,null,null,null from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=0 and b.po_id=0 and a.id=b.mst_id and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond  $comp_cond $dates_com  $batch_num $buyerdata $ext_no ";
			$p=1;
			foreach($w_dyeing_arr as $d_batch_id)
			 {
				if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
				$p++;
			 }
			$sql .=")";
			$sql .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form) order by batch_date ";
			}
		 }
		 if($batch_type==0 || $batch_type==2) //SubCon Batch
		  {
			 $w_dyeing_subcon=array_chunk(array_unique(explode(",",$sub_cond_d)),999);
			// print_r( $w_dyeing_subcon);
			if($w_dyeing_subcon!=0)
				{ // subcon_ord_dtls c, subcon_ord_mst d, 
			$sql_subcon="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) as po_number,d.subcon_job,d.job_no_prefix_num,d.party_id as buyer_name
		from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $sub_buyer_cond $sub_job_cond $suborder_no $ext_no $year_cond $comp_cond";
				$p=1;
				foreach($w_dyeing_subcon as $subcon_batch_id)
				 {
					
					if($p==1) $sql_subcon .=" and (a.id not in(".implode(',',$subcon_batch_id).")"; else  $sql_subcon .=" OR a.id not in(".implode(',',$subcon_batch_id).")";
					$p++;
				 }
				$sql_subcon .=")";
				$sql_subcon .="  GROUP BY a.id, a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,d.subcon_job,d.job_no_prefix_num,d.party_id,a.entry_form,b.rec_challan)
				union
				(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,null,null,null,null from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=36 and b.po_id=0 and a.id=b.mst_id and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond  $dates_com $sub_job_cond $batch_num $sub_buyer_cond  $suborder_no $ext_no $year_cond $comp_cond";
				
				$p=1;
				foreach($w_dyeing_subcon as $d_batch_id)
				 { 
					if($p==1) $sql_subcon .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql_subcon .=" OR a.id not in(".implode(',',$d_batch_id).")";
					$p++;
				 }
				
				 //echo $sql;
				$sql_subcon .=")";
				$sql_subcon .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form,b.rec_challan) order by batch_date ";
				}
			  }
			//echo $sql_subcon;
		 }	
		else if($cbo_type==5) //Wait For Singeing
		{
		if($db_type==0) $find_cond="and  FIND_IN_SET(94,a.process_id)"; 
		else if($db_type==2) $find_cond="and  ',' || a.process_id || ',' LIKE '%94%'";
		$w_sing_arr=array_chunk(array_unique(explode(",",$row_sin)),999);
		if($w_sing_arr!=0)
		{
			 $sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst    and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $working_comp_cond $dates_com $jobdata $batch_num $buyerdata $po_id_cond $ext_no $year_cond $comp_cond  ";
			$p=1;
			foreach($w_sing_arr as $sing_row)
			{
				if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
				$p++;
			}
			$sql .=")";
			$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form,b.rec_challan)
				union
				(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,null,null,null,null from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $comp_cond $working_comp_cond $dates_com  $batch_num $buyerdata $ext_no $year_cond ";
			$p=1;
			foreach($w_sing_arr as $sing_row)
			{
				if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
				$p++;
			}
			$sql .=")";
			$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form,b.rec_challan) order by batch_date "; 
			//echo $sql;
		}
	   }	
	}
	//echo $sql; //echo $sql_subcon;
	if($cbo_type==1)
	{
		if($batch_type==0 || $batch_type==1)
		{
			//echo $sql;
		$batchdata=sql_select($sql);
		//print_r($batchdata);
		}
		else if($batch_type==0 || $batch_type==2)
		{
		
			$sub_batchdata=sql_select($sub_cond);
		}
		else if($batch_type==0 || $batch_type==3)
		{
		
			$sam_batchdata=sql_select($sql_sam);
		}
	}
	else if($cbo_type==2)
	{
		if($batch_type==0 || $batch_type==1)
		{
		$batchdata=sql_select($sql);
		}
		else  if($batch_type==0 || $batch_type==2)
		{
			$sub_batchdata=sql_select($sub_cond);
		}
	}
	else if($cbo_type==3)
	{
		if($batch_type==0 || $batch_type==1)
		{
		$batchdata=sql_select($sql);
		}
		else if($batch_type==0 || $batch_type==2)
		{
			//echo $sub_cond;
			$sub_batchdata=sql_select($sub_cond);
		}
	}
	else if($cbo_type==4)
	{
		if($batch_type==0 || $batch_type==1)
		{
		$batchdata=sql_select($sql);
		}
		else if($batch_type==0 || $batch_type==2)
		{
			//echo $sub_cond;
			$sub_batchdata=sql_select($sql_subcon);
		}
	}
	else if($cbo_type==5)
	{
		if($batch_type==0 || $batch_type==1)
		{
		$batchdata=sql_select($sql);
		}
		/*else if($batch_type==0 || $batch_type==2)
		{
			//echo $sub_cond;
			$sub_batchdata=sql_select($sub_cond);
		}*/
	}
ob_start();
?>
<div align="center">
<fieldset style="width:1295px;">
<div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> <? echo $search_by_arr[$cbo_type]; ?> </strong>
<br><b>
<?
	//echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
	echo  ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to));
?> </b>
 </div>
 <div align="center">
  <? 
	if($batch_type==0 || $batch_type==1)
	{ ?>
    
 <div align="center"> <b>Self Batch </b></div>
 <table class="rpt_table" width="1510" cellpadding="0" cellspacing="0" border="1" rules="all">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="75">Batch Date</th>
                <th width="60">Batch No</th>
                <th width="40">Ext. No</th>
                <th width="80">Batch Against</th>
                <th width="80">Batch Color</th>
                <th width="80">Buyer</th>
                <th width="120">PO No</th>
                <th width="70">File No</th>
                <th width="70">Ref. No</th>
                <th width="80">W/O NO.</th> 
                 <th width="70">Job</th>
                <th width="100">Construction</th>
                <th width="150">Composition</th>
                <th width="50">Dia/ Width</th>
                <th width="50">GSM</th>
                <th width="60">Lot No</th>
                <th width="70">Batch Qty.</th>
                <th width="50">Batch Weight</th>
                <th>Grey Req.Qty</th>
            </tr>
        </thead>
</table>
<div style=" max-height:350px; width:1510px; overflow-y:scroll;" id="scroll_body">
<table class="rpt_table" id="table_body" width="1490" cellpadding="0" cellspacing="0" border="1" rules="all">
<tbody>
			<? 
			
			$booking_qnty_arr=array();
			$query=sql_select("select b.po_break_down_id,a.booking_no, b.fabric_color_id, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,a.booking_no, b.fabric_color_id");
			foreach($query as $row)
			{
				$booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
			}

			$smn_query=sql_select("select booking_no from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
			foreach($smn_query as $row)
			{
				$sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];				
			}
			$sam_query=sql_select("select booking_no from wo_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
			foreach($sam_query as $row)
			{
				$sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];				
			}
			$i=1;
			$f=0;
			$b=0;
			$btq=0;
			$tot_book_qty=0;
			$tot_grey_req_qty=0;
			$batch_chk_arr=array();$booking_chk_arr=array();
			foreach($batchdata as $batch)
			{ 	
				$sam_booking=$sam_booking_arr[$batch[csf('booking_no')]];
				//if (!in_array($batch[csf('booking_no')],$sam_booking_arr))
				if($sam_booking!=$batch[csf('booking_no')])
				{					
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 $order_id=$batch[csf('po_id')];
				$color_id=$batch[csf('color_id')];
				$booking_no=$batch[csf('booking_no')];
				$booking_qty=$booking_qnty_arr[$order_id][$booking_no][$color_id];
				$desc=explode(",",$batch[csf('item_description')]);
				$entry_form=$batch[csf('entry_form')];
				$po_ids=array_unique(explode(",",$batch[csf('po_id')]));
				$po_num='';	$po_file='';$po_ref='';$job_num='';$job_buyers='';$yarn_lot_num='';
				$grey_booking_qty=0;
				foreach($po_ids as $p_id)
				{
					if($po_num=='') $po_num=$po_array[$p_id]['po_no'];else $po_num.=",".$po_array[$p_id]['po_no'];
					if($po_file=='') $po_file=$po_array[$p_id]['file'];else $po_file.=",".$po_array[$p_id]['file'];
					if($po_ref=='') $po_ref=$po_array[$p_id]['ref'];else $po_ref.=",".$po_array[$p_id]['ref'];
					if($job_num=='') $job_num=$po_array[$p_id]['job_no'];else $job_num.=",".$po_array[$p_id]['job_no'];
					if($job_buyers=='') $job_buyers=$buyer_arr[$po_array[$p_id]['buyer']];else $job_buyers.=",".$buyer_arr[$po_array[$p_id]['buyer']];
					$ylot=rtrim($yarn_lot_arr[$batch[csf('prod_id')]][$p_id]['lot'],',');
					if($yarn_lot_num=='') $yarn_lot_num=$ylot;else $yarn_lot_num.=",".$ylot;
					$grey_booking_qty+=$booking_qnty_arr[$p_id][$booking_no][$color_id];
					
				}
				$yarn_lots=implode(",",array_unique(explode(",",$yarn_lot_num)));
				// $po_number=$po_array[$batch[csf('po_id')]]['no'];//implode(",",array_unique(explode(",",$batch[csf('po_number')]))); 
				// $book_qty+=$booking_qty;
				$booking_color=$order_id.$booking_no.$color_id;
				if (!in_array($booking_color,$booking_chk_arr))
				{ $b++;
					 $booking_chk_arr[]=$booking_color;
					  $tot_book_qty=$grey_booking_qty;
				}
				else
				{
					 $tot_book_qty=0;
				}
				
				//echo  $book_qty;
				?>
	            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
	            <? if (!in_array($batch[csf('id')],$batch_chk_arr) )
					{ $f++;
								?>
	                <td width="30"><? echo $f; ?></td>
	                <td align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
	                <td  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
	                <td  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
	                 <td  width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
	                <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $color_library[$batch[csf('color_id')]]; ?></div></td>
	                <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $job_buyers;//; ?></div></td>
					<?	
	                $batch_chk_arr[]=$batch[csf('id')];
	               // $book_qty+=$booking_qty;
	                  } 
					else
					  { ?>
	                <td width="30"><? //echo $sl; ?></td>
	                <td   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
	                <td   width="60"><p><? //echo $booking_qty; ?></p></td>
	                <td   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
	                <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
	                <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
	                <td  width="80"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
					<? }
					?>
	                <td width="120"><p><? echo $po_num;//$po_array[$batch[csf('po_id')]]['no']; ?></p></td>
	                <td width="70"><p><? echo $po_file; ?></p></td>
	                <td width="70"><p><?  echo $po_ref; ?></p></td>
	                <td width="80"><p><? echo $batch[csf('booking_no')]; ?></p></td>
	                <td width="70"><p><? echo $job_num; ?></p></td>
	                <td width="100"><div style="width:80px; word-wrap:break-word;"><? echo $desc[0]; ?></div></td>
	                <td width="150"><div style="width:150px; word-wrap:break-word;"><? echo $desc[1]; ?></div></td>
	                <td  width="50"><p><? echo $desc[3]; ?></p></td>
	                <td  width="50"><p><? echo $desc[2]; ?></p></td>
	                 <td  title="<? echo 'Prod Id'.$batch[csf('prod_id')].'=PO ID'.$order_id;?>" align="left" width="60"><div style="width:60px; word-wrap:break-word;"><? echo $yarn_lots; ?></div></td>
	                <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
	                <td  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($batch[csf('batch_weight')],2); ?></td>
	                <td><? echo number_format($tot_book_qty,2);?></td>
	            </tr>
				<? 
	                $i++;
	                $btq+=$batch[csf('batch_qnty')];
					$tot_grey_req_qty+=$tot_book_qty;
	                $balance=$tot_grey_req_qty-$btq;
	                $bal_qty=$balance;
	                if($bal_qty>0)
	                {
	                $color="";	
	                $txt="Over Batch Qty";
	                }
	                else if($bal_qty<0)
	                {
	                $color="red";
	                $txt="Below Batch Qty";
	                }
	            	}
                } 
        	 ?>
       </tbody>
</table>
<table class="rpt_table" width="1490" cellpadding="0" cellspacing="0" border="1" rules="all">
        <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="40">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="120">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                
                <th width="70">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="150">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="70"><? echo number_format($btq,2); ?></th>
                <th width="50">&nbsp;</th>
                <th><? //echo number_format($tot_grey_req_qty,2); ?></th>
            </tr>
            <tr>
                <td colspan="13" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
                <td colspan="14" align="left">&nbsp; 
                 <? echo number_format($tot_grey_req_qty,2); ?>
                </td>
            </tr>
             <tr>
                <td colspan="13" align="right" style="border:none;"> <b>Batch Qty.</b></td>
                <td colspan="14" align="left">&nbsp; <? echo number_format($btq,2); ?> </td>
            </tr>
             <tr>
                <td colspan="13" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
                <td colspan="14" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($bal_qty,2); ?> &nbsp;&nbsp;</font></b> </td>
            </tr>
        </tfoot>
</table>
</div> <br/>
<? 
}
if($batch_type==0 || $batch_type==2) 
	{		
?>
<div align="left"> <b>SubCond Batch </b></div>
 <table class="rpt_table" width="1320" cellpadding="0" cellspacing="0" border="1" rules="all">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="75">Batch Date</th>
                <th width="60">Batch No</th>
                <th width="40">Ext. No</th>
                <th width="80">Batch Aganist</th>
                <th width="80">Batch Color</th>
                <th width="50">Buyer</th>
                <th width="120">PO No</th>
                <th width="80">W/O NO.</th> 
                <th width="70">Job</th>
                <th width="80">Recv. Challan</th>
                <th width="150">Fabrics Desc.</th>
               
                <th width="50">Dia/ Width</th>
                <th width="50">GSM</th>
                <th width="60">Lot No</th>
                <th width="70">Batch Qty.</th>
                <th width="50">Batch Weight</th>
                <th>Grey Req. Qty</th>
            </tr>
        </thead>
</table>
<div style=" max-height:350px; width:1320px; overflow-y:scroll;" id="scroll_body">
<table class="rpt_table" id="table_body2" width="1300" cellpadding="0" cellspacing="0" border="1" rules="all">
<tbody>
			<? 
			/*$booking_qnty_arr=array();
			$query=sql_select("select b.po_break_down_id, b.fabric_color_id,a.booking_no, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id, a.booking_no,b.fabric_color_id");
			foreach($query as $row)
			{
				$booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
			}*/
			
			
			$sub_material_recv_arr=array();$sub_material_description_arr=array();
			$subcon_sql=sql_select("select b.order_id as po_break_down_id,b.gsm,b.material_description, b.color_id,a.chalan_no, sum(b.quantity ) as grey_fab_qnty from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.item_category_id in(1,2,3,13,14) and a.trans_type=1  and a.is_deleted=0 and a.status_active=1  and b.status_active=2 and b.is_deleted=0 group by b.order_id, a.chalan_no,b.color_id,a.trans_type,b.material_description,b.gsm");
			foreach($subcon_sql as $row)
			{
				$sub_material_recv_arr[$row[csf('po_break_down_id')]]+=$row[csf('grey_fab_qnty')];
				$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['desc']=$row[csf('material_description')];
				$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['gsm']=$row[csf('gsm')];
				$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['gsm']=$row[csf('gsm')];
			}
			
			//var_dump($sub_material_description_arr);die;
			$i=1;
			$f=0;
			$btq=0; $k=0;
			$book_qty_subcon=0;$subcon_tot_book_qty=0;
			$batch_chk_arr=array();$sub_qty_chk_arr=array();
			foreach($sub_batchdata as $batch)
			{ 
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$order_id_sub=$batch[csf('po_id')];
			$color_id=$batch[csf('color_id')];
			$booking_no=$batch[csf('booking_no')];
			$sub_challan=$batch[csf('rec_challan')]; 
			$subcon_booking_qty=$sub_material_recv_arr[$order_id_sub];//$booking_qnty_arr[$order_id][$booking_no][$color_id];
			$item_descript=$sub_material_description_arr[$order_id_sub][$sub_challan]['desc'];
			$gsm_subcon=$sub_material_description_arr[$order_id_sub][$sub_challan]['gsm'];
			$desc=explode(",",$batch[csf('item_description')]); 
			$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
			
			$entry_form=$batch[csf('entry_form')];
			
			$po_ids=array_unique(explode(",",$batch[csf('po_id')]));
			$sub_po_num='';	$sub_job_buyers='';$sub_job_buyers='';
			$subcon_booking_qty=0;
			foreach($po_ids as $p_id)
			{
				if($sub_po_num=='') $sub_po_num=$sub_po_array[$p_id]['po_no'];else $sub_po_num.=",".$sub_po_array[$p_id]['po_no'];
				if($sub_job_num=='') $sub_job_num=$sub_po_array[$p_id]['job_no'];else $sub_job_num.=",".$sub_po_array[$p_id]['job_no'];
				if($sub_job_buyers=='') $sub_job_buyers=$buyer_arr[$po_array[$p_id]['buyer']];else $sub_job_buyers.=",".$buyer_arr[$po_array[$p_id]['buyer']];
				$subcon_booking_qty+=$sub_material_recv_arr[$p_id];
				
			} 
			
			$booking_color2=$order_id_sub.$batch[csf('booking_no')].$batch[csf('color_id')];
			if (!in_array($booking_color2,$sub_qty_chk_arr))
					{ $k++;
						
						//echo $subcon_booking_qty;
						 $sub_qty_chk_arr[]=$booking_color2;
						  $subcon_tot_book_qty=$subcon_booking_qty;
					}
					else
					{
						 $subcon_tot_book_qty=0;
					}
			?>
            <tr bgcolor="<? echo $bgcolor; ?>" id="trd_<? echo $i; ?>" onclick="change_color('trd_<? echo $i; ?>','<? echo $bgcolor; ?>')">
            <? if (!in_array($batch[csf('id')],$batch_chk_arr) )
				{ $f++;
							?>
                <td width="30"><? echo $f; ?></td>
                <td align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
                <td  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                <td  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                 <td width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
                <td width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td width="50"><p><? echo $sub_job_buyers; ?></p></td>
				<?	
                $batch_chk_arr[]=$batch[csf('id')];
               
                  } 
				else
				  { ?>
                <td width="30"><? //echo $sl; ?></td>
                <td   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
                <td   width="60"><p><? //echo $booking_qty; ?></p></td>
                <td   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
                <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  width="50"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
				<? }
				?>
                <td width="120"><p><? echo $sub_po_num; ?></p></td>
                <td width="80"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                <td width="70"><p><? echo $sub_job_num; ?></p></td>
                <td width="80" ><p><? echo $batch[csf('rec_challan')]; ?></p></td>
                <td width="150" ><p><? echo $item_descript; ?></p></td>
                
               
              
                <td  width="50" title="<? echo $desc[2];  ?>"><p><? echo $desc[3]; ?></p></td>
                <td  width="50"><p><? echo $gsm_subcon; ?></p></td>
                 <td align="left" width="60"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$order_id_sub]['lot']; ?></p></td>
                <td align="right" width="70" ><? echo number_format($batch[csf('sub_batch_qnty')],2);  ?></td>
                <td  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($batch[csf('batch_weight')],2); ?></td>
                <td><? echo number_format($subcon_tot_book_qty,2); ?></td>
            </tr>
			<? 
                $i++;
                $btq_subcon+=$batch[csf('sub_batch_qnty')];
				$book_qty_subcon+=$subcon_tot_book_qty;
                $balance=$book_qty_subcon-$btq_subcon;
                $bal_qty_subcon=$balance;
                if($bal_qty_subcon>0)
                {
                $color="";	
                $txt="Over Batch Qty";
                }
                else if($bal_qty_subcon<0)
                {
                $color="red";
                $txt="Below Batch Qty";
                }
                } 
        	 ?>
       </tbody>
</table>
<table class="rpt_table" width="1300" cellpadding="0" cellspacing="0" border="1" rules="all">
        <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="40">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="120">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
             
                <th width="150">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="70"><? echo $btq_subcon; ?></th>
                <th width="50">&nbsp;</th>
                <th><? //echo $book_qty_subcon; ?></th>
            </tr>
            <tr>
                <td colspan="12" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
                <td colspan="7" align="left">&nbsp; 
                 <? echo number_format($book_qty_subcon,2); ?>
                </td>
            </tr>
             <tr>
                <td colspan="12" align="right" style="border:none;"> <b>Batch Qty.</b></td>
                <td colspan="7" align="left">&nbsp; <? echo number_format($btq_subcon,2); ?> </td>
            </tr>
             <tr>
                <td colspan="12" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
                <td colspan="7" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($bal_qty_subcon,2); ?> &nbsp;&nbsp;</font></b> </td>
            </tr>
        </tfoot>
</table>
</div>
<?
	}
	if($batch_type==0 || $batch_type==3)
	{ 
	 if($cbo_type==1)
		 {
	?>
    
 <div align="left"> <b>Sample Batch </b></div>
 <table class="rpt_table" width="1500" cellpadding="0" cellspacing="0" border="1" rules="all">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="75">Batch Date</th>
                <th width="60">Batch No</th>
                <th width="40">Ext. No</th>
                <th width="80">Batch Aganist</th>
                <th width="80">Batch Color</th>
                <th width="50">Buyer</th>
                <th width="120">PO No</th>
                <th width="70">File No</th>
                <th width="70">Ref No</th>
                <th width="100">W/O NO.</th> 
                <th width="70">Job</th>
                <th width="100">Construction</th>
                <th width="150">Composition</th>
                <th width="50">Dia/ Width</th>
                <th width="50">GSM</th>
                <th width="60">Lot No</th>
                <th width="70">Batch Qty.</th>
                <th width="50">Batch Weight</th>
                <th>Grey Req.Qty</th>
            </tr>
        </thead>
</table>
<div style=" max-height:350px; width:1500px; overflow-y:scroll;" id="scroll_body">
<table class="rpt_table" id="table_body3" width="1480" cellpadding="0" cellspacing="0" border="1" rules="all">
<tbody>
			<? 
			$sam_booking_qnty_arr=array();
			$sam_query=sql_select("select b.po_break_down_id,a.booking_no, b.fabric_color_id, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,a.booking_no, b.fabric_color_id");
			foreach($sam_query as $row)
			{
				$sam_booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
				
			}
			
			$smn_query=sql_select("select booking_no from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
			foreach($smn_query as $row)
			{
				$sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];				
			}
			$sam_query=sql_select("select booking_no from wo_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
			foreach($sam_query as $row)
			{
				$sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];				
			}
		
			$i=1;
			$f=0;
			$b=0;
			$btq=0;
			$tot_book_qty2=0;
			$tot_grey_req_qty=0;
			$batch_chk_arr=array();$booking_chk_arr2=array();
			//print_r($sam_batchdata );
			foreach($sam_batchdata as $batch)
			{ 
				$sam_booking=$sam_booking_arr[$batch[csf('booking_no')]];
				if($sam_booking==$batch[csf('booking_no')])
				{	
					
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_ids=array_unique(explode(",",$batch[csf('po_id')]));
				$po_num='';	$po_file='';$po_ref='';$job_num='';$job_buyers='';$sam_booking_qty=0;
				foreach($po_ids as $p_id)
				{
					if($po_num=='') $po_num=$po_array[$p_id]['po_no'];else $po_num.=",".$po_array[$p_id]['po_no'];
					if($po_file=='') $po_file=$po_array[$p_id]['file'];else $po_file.=",".$po_array[$p_id]['file'];
					if($po_ref=='') $po_ref=$po_array[$p_id]['ref'];else $po_ref.=",".$po_array[$p_id]['ref'];
					if($job_num=='') $job_num=$po_array[$p_id]['job_no'];else $job_num.=",".$po_array[$p_id]['job_no'];
					if($job_buyers=='') $job_buyers=$buyer_arr[$po_array[$p_id]['buyer']];else $job_buyers.=",".$buyer_arr[$po_array[$p_id]['buyer']];
					$sam_booking_qty+=$sam_booking_qnty_arr[$p_id][$booking_no][$color_id];
					
				}
				
				$order_id=$batch[csf('po_id')];
				$color_id=$batch[csf('color_id')];
				$booking_no=$batch[csf('booking_no')];
				//$sam_booking_no = $sam_booking_arr[$booking_no]['booking_no'];
				
				$desc=explode(",",$batch[csf('item_description')]);
				$entry_form=$batch[csf('entry_form')];
				//$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')]))); 
				//if($po_file=='') $po_file=$po_array[$order_id]['file'];else $po_file.=",".$po_array[$order_id]['file'];
				//if($po_ref=='') $po_ref=$po_array[$order_id]['ref'];else $po_ref.=",".$po_array[$order_id]['ref'];
				//$po_file=$po_array[$order_id]['file'];
				//$po_ref=$po_array[$order_id]['ref'];
				// $book_qty+=$booking_qty;.$batch[csf('booking_no')].$batch[csf('color_id')].$batch[csf('prod_id')]
				$booking_color=$order_id.$booking_no.$color_id;
				if (!in_array($booking_color,$booking_chk_arr2))
					{ $b++;
						
						
						 $booking_chk_arr2[]=$booking_color;
						  $tot_book_qty2=$sam_booking_qty;
					}
					else
					{
						 $tot_book_qty2=0;
					}
				
				//echo  $book_qty;
				?>
	            <tr bgcolor="<? echo $bgcolor; ?>" id="tr3_<? echo $i; ?>" onclick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor; ?>')">
	            <? if (!in_array($batch[csf('id')],$booking_chk_arr2))
					{ $f++;
								?>
	                <td width="30"><? echo $f; ?></td>
	                <td align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
	                <td  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
	                <td  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
	                <td width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
	                <td width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
	                <td width="50" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
					<?	
	                $batch_chk_arr[]=$batch[csf('id')];
					
					
	               // $book_qty+=$booking_qty;
	                  } 
					else
					  { ?>
	                <td width="30"><? //echo $sl; ?></td>
	                <td   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
	                <td   width="60"><p><? //echo $booking_qty; ?></p></td>
	                <td   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
	                <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
	                <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
	                <td  width="50"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
					<? }
					?>
	                <td width="120" ><p><? echo $po_num; ?></p></td>
	                <td width="70"><p><? echo $po_file; ?></p></td>
	                 <td width="70"><p><? echo $po_ref; ?></p></td>
	                <td width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
	                <td width="70" title="<? echo $batch[csf('job_no_prefix_num')]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
	                <td width="100" title="<? echo $desc[0]; ?>"><p><? echo $desc[0]; ?></p></td>
	                <td width="150" title="<? echo $desc[1]; ?>"><p><? echo $desc[1]; ?></p></td>
	                <td  width="50" title="<? echo $desc[3];  ?>"><p><? echo $desc[3]; ?></p></td>
	                <td  width="50" title="<? echo $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
	                 <td align="left" width="60" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]]; ?>"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$order_id]['lot']; ?></p></td>
	                <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
	                <td  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($batch[csf('batch_weight')],2); ?></td>
	                <td><? echo number_format($tot_book_qty2,2);?></td>
	            </tr>
				<? 
	                $i++;
	                $sam_btq+=$batch[csf('batch_qnty')];
					$tot_grey_req_qty+=$tot_book_qty2;
	                $sam_balance=$tot_grey_req_qty-$sam_btq;
	                $sam_bal_qty=$sam_balance;
	                if($sam_bal_qty>0)
	                {
	                $color="";	
	                $txt="Over Batch Qty";
	                }
	                else if($sam_bal_qty<0)
	                {
	                $color="red";
	                $txt="Below Batch Qty";
	                }
	                }
                } 
        	 ?>
       </tbody>
</table>
<table class="rpt_table" width="1480" cellpadding="0" cellspacing="0" border="1" rules="all">
        <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="40">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="120">&nbsp;</th>
                 <th width="70">&nbsp;</th>
                  <th width="100">&nbsp;</th>
                  <th width="70">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="150">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="70"><? echo number_format($sam_btq,2); ?></th>
                <th width="50">&nbsp;</th>
                <th><? //echo number_format($tot_grey_req_qty,2); ?></th>
            </tr>
            <tr>
                <td colspan="12" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
                <td colspan="13" align="left">&nbsp; 
                 <? echo number_format($tot_grey_req_qty,2); ?>
                </td>
            </tr>
             <tr>
                <td colspan="12" align="right" style="border:none;"> <b>Batch Qty.</b></td>
                <td colspan="13" align="left">&nbsp; <? echo number_format($sam_btq,2); ?> </td>
            </tr>
             <tr>
                <td colspan="12" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
                <td colspan="13" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($sam_bal_qty,2); ?> &nbsp;&nbsp;</font></b> </td>
            </tr>
        </tfoot>
</table>
</div> <br/>
<?
		 }
} 
	
?>
</div>
</fieldset>
</div>
<?
	exit();
}//BatchReport
?>