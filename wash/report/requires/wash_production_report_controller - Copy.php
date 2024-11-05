<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');


if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
	exit();	 
} 
if($action=="job_no_popup") 
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id)
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
    </script>
    <input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	$year_job = str_replace("'","",$year);

    if($db_type==0) 
	{
		$year_field="year(a.insert_date) as year"; 
		 if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year_job";
	}
    else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		 if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year_job";
	}
    else 
	{
		$year_field="";
		$year_field_cond="";
	}
    
	//echo $cbo_within_group."sfsdf";
	
    if($cbo_buyer_name==0) $buyer_cond=""; else $buyer_cond=" and a.party_id='$cbo_buyer_name'";
	if($cbo_within_group==0) $within_group=""; else $within_group=" and a.within_group='$cbo_within_group'";
	
	if($cbo_within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
    
   // $buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	
	
   $sql="select a.party_id, a.subcon_job, a.job_no_prefix_num, $year_field, b.order_no, b.cust_style_ref, b.main_process_id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company_id' and  a.entry_form=295 $buyer_cond $within_group $year_field_cond order by a.id desc";
    ?>
    <table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="80">Job no</th>
            <th width="70">Year</th>
            <th width="130">Buyer</th>
            <th width="110">Style</th>
            <th width="120">Po number</th>
       </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
    <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
     <? $data_array=sql_select($sql);
        $i=1;
		 foreach($data_array as $row)
		 {
			 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		 ?>
			<tr bgcolor="<? echo $bgcolor; ?>"  onClick="js_set_value('<? echo $row[csf('job_no_prefix_num')]; ?>')" style="cursor:pointer;">
				<td width="30"><? echo $i; ?></td>
				<td align="center"  width="80"><? echo $row[csf('job_no_prefix_num')]; ?></td>
				<td align="center" width="70"><? echo $row[csf('year')]; ?></td>
				<td width="130"><? echo $party_arr[$row[csf('party_id')]]; ?></td>
				<td width="110"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
				<td width="120"><p><? echo $row[csf('order_no')]; ?></p></td>
			</tr>
			<? $i++; 
			} 
		?>
    </table>
    </div>
	<script> setFilterGrid("table_body2",-1); </script>
    <?
	disconnect($con);
	exit();
}


if($action=="report_generate")
{ 
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$job_no=str_replace("'","",$txt_job_no);
	$cbo_buyer_id=str_replace("'","",$cbo_party_name);
	$company_id=str_replace("'","",$cbo_company_id);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	$cboProcess=str_replace("'","",$cboProcess);
	$cbo_year =str_replace("'","",$cbo_year_selection);
	
	if($db_type==0)
	{
		//$year_field="year(a.insert_date) as year"; 
		if($cbo_year!=0) $year_cond="and YEAR(d.insert_date)=$cbo_year";  else $year_field_cond="";
	}
	else if($db_type==2)
	{
		//$year_field="to_char(a.insert_date,'YYYY') as year";
		if($cbo_year!=0) $year_cond=" and to_char(d.insert_date,'YYYY')=$cbo_year"; else $year_field_cond="";
		//$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
	}
	
	//echo $year_cond; die;
	
	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);
	
	if($db_type==0) $select_from_date=change_date_format($from_date,'yyyy-mm-dd');
	if($db_type==2) $select_from_date=change_date_format($from_date,'','',1);
	if($db_type==0) $select_to_date=change_date_format($to_date,'yyyy-mm-dd');
	if($db_type==2) $select_to_date=change_date_format($to_date,'','',1);
	
	
	if($cbo_buyer_id==0) $party_con=""; else $party_con=" and d.party_id='$cbo_buyer_id'";
	if($cbo_within_group==0) $within_group=""; else $within_group=" and d.within_group='$cbo_within_group'";
	//echo $cboProcess; die;
	if($cboProcess==1)
	{
		 $wash_Process=" and b.process_id is null";
	}
	if($cboProcess==3)
	{
		 $wash_Process=" and b.process_id='$cboProcess'";
	}
	if($cboProcess==2)
	{
		$wash_Process=" and b.process_id='$cboProcess'";
	}
	
	//echo $wash_Process; die;
	
	if($cbo_within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
    
	
	
	if(str_replace("'","",$company_id)==0)$company_name=""; else $company_name=" and d.company_id=$company_id";
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and d.job_no_prefix_num in ($job_no) ";
	
	$color_library_arr=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	if($db_type==0)
	{
		if( $from_date==0 && $to_date==0 ) $production_date=""; else $production_date= " and b.production_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";	
 	}
	if($db_type==2)
	{
		if( $from_date==0 && $to_date==0 ) $production_date=""; else $production_date= " and b.production_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";	
 	
	}
 	
	
	$receeive_qty_array=array();
	$sql_receeive="Select b.job_dtls_id,a.subcon_date,b.quantity as receeive_qnty from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.entry_form=296 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_receeive_result=sql_select($sql_receeive); $receeive_date_array=array(); 
	foreach ($sql_receeive_result as $row)
	{
		$receeive_qty_array[$row[csf('job_dtls_id')]]['receeive_qnty']+=$row[csf('receeive_qnty')];
	}

	$delevery_qty_array=array();
	$sql_del="select a.order_id, a.delivery_qty, a.sort_qty, a.reject_qty from subcon_delivery_dtls a, subcon_delivery_mst b where b.id=a.mst_id  and b.entry_form=303 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_del_result=sql_select($sql_del);
	foreach ($sql_del_result as $row)
	{
		$delevery_qty_array[$row[csf('order_id')]]['delivery_qty']+=$row[csf('delivery_qty')];
		$delevery_qty_array[$row[csf('order_id')]]['sort_qty']+=$row[csf('sort_qty')];
		$delevery_qty_array[$row[csf('order_id')]]['reject_qty']+=$row[csf('reject_qty')];
		
	}
	
  	$batch_sql = "select a.id,b.po_id,a.operation_type,c.qcpass_qty
    from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_embel_production_dtls c ,subcon_embel_production_mst d 
    where a.id=b.mst_id and a.process_id='1'  and a.entry_form=316   and b.po_id=c.po_id and c.mst_id=d.id and  d.entry_form=301 and a.id=d.recipe_id  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0  
    group by a.id,b.po_id,a.operation_type,c.qcpass_qty";
	
   	$sql_batch_result=sql_select($batch_sql);
	$batch_qty_array=array(); $operation_array=array();
	foreach ($sql_batch_result as $row)
	{
		$batch_qty_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('operation_type')]]['qcpass_qty']=$row[csf('qcpass_qty')];
		//if($row[csf('operation_type')]=1)
		$operation_array[$row[csf('id')]][$row[csf('po_id')]]['operation_type']=$row[csf('operation_type')];
	}
	
	//echo "<pre>";
	//print_r($operation_array);
	
	if($db_type==0) $recipe_id_cond=",group_concat(a.recipe_id) as recipe_id";
	else if($db_type==2) $recipe_id_cond=",listagg(a.recipe_id,',') within group (order by a.recipe_id) as recipe_id";
	if($select_from_date=="" && $select_to_date=="") 
	{
		//,b.color_size_id
		$job_sql="select a.job_no $recipe_id_cond ,c.body_part,c.id as po_id,d.id,d.within_group,d.job_no_prefix_num, d.subcon_job, d.party_id, c.order_no,c.buyer_po_no, c.buyer_style_ref,c.gmts_item_id ,c.party_buyer_name,c.gmts_color_id,
		
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=1  then b.qcpass_qty else 0 end) as whisker_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=2   then b.qcpass_qty else 0 end) as hand_sand_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=3   then b.qcpass_qty else 0 end) as pp_spray_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=4   then b.qcpass_qty else 0 end) as pigment_spray_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=5   then b.qcpass_qty else 0 end) as tagging_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=6   then b.qcpass_qty else 0 end) as destroy_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=7   then b.qcpass_qty else 0 end) as ythreed_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=8   then b.qcpass_qty else 0 end) as tieing_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=9   then b.qcpass_qty else 0 end) as grinding_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=10   then b.qcpass_qty else 0 end) as resing_depping_spray_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=11   then b.qcpass_qty else 0 end) as wrinkle_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=30   then b.qcpass_qty else 0 end) as dry_others_qty,
		sum(case when a.entry_form=342 and b.process_id=3 and b.wash_type_id=1   then b.qcpass_qty else 0 end) as laser_whisker_qty,
		sum(case when a.entry_form=342 and b.process_id=3 and b.wash_type_id=2   then b.qcpass_qty else 0 end) as laser_brush_qty,
		sum(case when a.entry_form=342 and b.process_id=3 and b.wash_type_id=3   then b.qcpass_qty else 0 end) as laser_destroy_qty,
		sum(case when a.entry_form=342 and b.process_id=3 and b.wash_type_id=4   then b.qcpass_qty else 0 end) as laser_chemo_print_qty,
		sum(case when a.entry_form=342 and b.process_id=3 and b.wash_type_id=20  then b.qcpass_qty else 0 end) as laser_others_qty,
		sum(case when a.entry_form=301  then b.qcpass_qty else 0 end) as production_qty
		
		from subcon_embel_production_mst a,
		subcon_embel_production_dtls b,
		subcon_ord_dtls c,
		subcon_ord_mst d 
		where 
		a.id=b.mst_id and c.mst_id=d.id and  
		c.id=b.po_id and d.subcon_job=a.job_no  $sql_con and a.entry_form in( 342,301)  
		and d.is_deleted=0 and d.status_active=1 and a.is_deleted=0 
		and a.status_active=1 and b.is_deleted=0 and b.status_active=1 
		and c.is_deleted=0 and c.status_active=1 $job_no_cond $company_name $within_group
		$party_con $production_date $wash_Process $year_cond
		group by a.job_no ,c.body_part ,c.id,d.id,d.within_group,d.job_no_prefix_num, d.subcon_job, d.party_id, c.order_no,c.buyer_po_no, c.buyer_style_ref,c.gmts_item_id,c.party_buyer_name,c.gmts_color_id";
	}
	else
	{  
		$job_sql="select a.job_no $recipe_id_cond ,c.body_part,c.id as po_id,d.id,d.within_group,d.job_no_prefix_num, d.subcon_job, d.party_id, c.order_no,c.buyer_po_no, c.buyer_style_ref,c.gmts_item_id ,c.party_buyer_name,c.gmts_color_id,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=1 and b.production_date  between '".$select_from_date."' and '".$select_to_date."' then b.qcpass_qty else 0 end) as whisker_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=2  and b.production_date  between '".$select_from_date."' and '".$select_to_date."' then b.qcpass_qty else 0 end) as hand_sand_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=3  and b.production_date  between '".$select_from_date."' and '".$select_to_date."' then b.qcpass_qty else 0 end) as pp_spray_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=4  and b.production_date  between '".$select_from_date."' and '".$select_to_date."' then b.qcpass_qty else 0 end) as pigment_spray_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=5  and b.production_date  between '".$select_from_date."' and '".$select_to_date."' then b.qcpass_qty else 0 end) as tagging_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=6  and b.production_date  between '".$select_from_date."' and '".$select_to_date."' then b.qcpass_qty else 0 end) as destroy_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=7 and b.production_date  between '".$select_from_date."' and '".$select_to_date."' then b.qcpass_qty else 0 end) as ythreed_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=8  and b.production_date  between '".$select_from_date."' and '".$select_to_date."' then b.qcpass_qty else 0 end) as tieing_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=9  and b.production_date  between '".$select_from_date."' and '".$select_to_date."' then b.qcpass_qty else 0 end) as grinding_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=10  and b.production_date  between '".$select_from_date."' and '".$select_to_date."' then b.qcpass_qty else 0 end) as resing_depping_spray_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=11  and b.production_date  between '".$select_from_date."' and '".$select_to_date."' then b.qcpass_qty else 0 end) as wrinkle_qty,
		sum(case when a.entry_form=342 and b.process_id=2 and b.wash_type_id=30  and b.production_date  between '".$select_from_date."' and '".$select_to_date."' then b.qcpass_qty else 0 end) as dry_others_qty,
		sum(case when a.entry_form=342 and b.process_id=3 and b.wash_type_id=1  and b.production_date  between '".$select_from_date."' and '".$select_to_date."' then b.qcpass_qty else 0 end) as laser_whisker_qty,
		sum(case when a.entry_form=342 and b.process_id=3 and b.wash_type_id=2  and b.production_date  between '".$select_from_date."' and '".$select_to_date."' then b.qcpass_qty else 0 end) as laser_brush_qty,
		sum(case when a.entry_form=342 and b.process_id=3 and b.wash_type_id=3  and b.production_date  between '".$select_from_date."' and '".$select_to_date."' then b.qcpass_qty else 0 end) as laser_destroy_qty,
		sum(case when a.entry_form=342 and b.process_id=3 and b.wash_type_id=4  and b.production_date  between '".$select_from_date."' and '".$select_to_date."' then b.qcpass_qty else 0 end) as laser_chemo_print_qty,
		sum(case when a.entry_form=342 and b.process_id=3 and b.wash_type_id=20  and b.production_date  between '".$select_from_date."' and '".$select_to_date."' then b.qcpass_qty else 0 end) as laser_others_qty
		 from 
		subcon_embel_production_mst a,
		subcon_embel_production_dtls b,
		subcon_ord_dtls c,
		subcon_ord_mst d 
		where 
		a.id=b.mst_id and c.mst_id=d.id and  c.id=b.po_id  and d.subcon_job=a.job_no   $sql_con and a.entry_form in( 342,301)  and d.is_deleted=0 and d.status_active=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $job_no_cond $company_name $party_con $production_date $within_group $wash_Process
		group by a.job_no,c.body_part ,c.id,d.id,d.within_group,d.job_no_prefix_num, d.subcon_job, d.party_id, c.order_no,c.buyer_po_no, c.buyer_style_ref,c.gmts_item_id,c.party_buyer_name,c.gmts_color_id";
	}
		
	$job_sql_result=sql_select($job_sql);
	/*echo "<pre>";
	print_r($job_sql_result); */
	ob_start();
	if ($cbo_process==4)
	{
		$tbl_width=1950;
		$col_span=23;
	}
	else
	{
		$tbl_width=1790;
		$col_span=21;
	}
	//$job_sql_result[$row["within_group"]];
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	?>
    <style type="text/css">
		.wrd_brk{word-break: break-all;}
	</style>
     <!--<div style="width:2620px"> -->
     <fieldset style="width:2620px;">
     <div style="width:2620px; margin:0 auto;">
     
         <!--<table width="100%" cellspacing="0" >-->
          <table cellpadding="0" cellspacing="0" width="2620">
         		<tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="29" style="font-size:20px"><strong><? echo 'Wash Production Report'; ?></strong></td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td colspan="29" align="center" style="border:none; font-size:14px;">
                        <b><? echo $company_library[$company_id]; ?></b>
                    </td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="29" style="font-size:12px">
                        <? if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo "From ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
                    </td>
                </tr>
            </table>
         	<!--<table width="2600" cellspacing="0" border="1" class="rpt_table" rules="all">-->
            <table width="2600" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                        <th width="35">SL</th>
                        <th width="100">Party Buyer</th>
                        <th width="100">Party</th>
                        <th width="100">Job No</th>
                        <th width="130">Buyer Style Ref.</th>
                        <th width="130">Buyer PO</th>
                        <th width="100">Gmts. Item</th>
                        <th width="100">Color</th>
                        <th width="80">Receive </th>
                        <th width="80">Whisker</th>
                        <th width="80">Hand Sand</th>
                        <th width="80">Tagging</th>
                        <th width="80">Tieing</th>
                        <th width="80">Granding</th>
                        <th width="80">1st Wash</th>
                        <th width="80">Destroy</th>
                        <th width="80">PP Spray</th>
                        <th width="80">Final wash</th>
                        <th width="80">3D</th>
                        <th width="80">Wrinkle</th>
                        <th width="80">Laser Whisker</th>
                        <th width="80">Laser Brush</th>
                        <th width="80">Laser Destroy</th>
                        <th width="80">Laser Chemo Print</th>
                        <th width="80">Dyeing</th>
                        <th width="80">Others</th>
                        <th width="80">Delivery</th>
                        <th width="80">Balance Qty</th>
                        <th >Remarks</th>
                </thead>
            </table>
             <div style="max-height:300px; overflow-y:scroll; width:2620px" id="scroll_body">
             <table width="2600" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body">
				<?  
					$i=1;
					
					foreach($job_sql_result as $row)
					{
						//echo $row[csf('whisker_qty')]."==".$row[csf("party_buyer_name")];
						$receeive_qty =$receeive_qty_array[$row[csf('po_id')]]['receeive_qnty'];
						$delevery_qty =$delevery_qty_array[$row[csf('po_id')]]['delivery_qty'];
						$blance_qty=$receeive_qty-$delevery_qty;
						//echo $row[csf("recipe_id")];
						$first_wash_qty=$final_wash_qty=$first_dyeing_qty=0;
					    $batch_ids=array_unique(explode(",",$row[csf("recipe_id")]));
						
						//echo $row[csf("recipe_id")];
							//echo "<pre>";
							//print_r($batch_ids); 
						foreach($batch_ids as $val)
						{
							//$operationType="";
							//echo $val."==".$row[csf('po_id')]."++ ";
							
							//echo "<pre>";
							//print_r($operation_array); 
							//if($val)
							//{
								$operationType=$operation_array[$val][$row[csf('po_id')]]['operation_type'];
							
								//==0==3678++ 1==8973==3678++ 2==8974==3678++ 
								//echo $operationType."==".$val."==".$row[csf('po_id')]."++ ";
								if($operationType==1)
								{
									$first_wash_qty +=$batch_qty_array[$val][$row[csf('po_id')]][$operationType]['qcpass_qty'];
								}
								else if($operationType==2)
								{
									$final_wash_qty+=$batch_qty_array[$val][$row[csf('po_id')]][$operationType]['qcpass_qty'];
								}
								else
								{
									$first_dyeing_qty+=$batch_qty_array[$val][$row[csf('po_id')]][$operationType]['qcpass_qty'];
								}
							//}
							

						}
						//$jobs_details_idArr =  array_unique(explode(",", $row[csf("jobs_details_id")]));
						//print_r($jobs_details_idArr);
						/*
						$jobs_details = "";
						foreach($jobs_details_idArr as $jobsId){
							if($jobs_details!="") $jobs_details .= ", ";
							$jobs_details += $receeive_qty_array[$jobsId]['receeive_qnty'];
							$receeive_date=change_date_format($receeive_date_array[$jobsId]['subcon_date']);
							$currentDate=change_date_format(date("Y/m/d"));
						}*/
						//echo $currentDate."dkf".$receeive_date;
						
						?>
						
                          <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td  width="35" id="wrd_brk"><? echo $i; ?></td>
                            <td width="100" id="wrd_brk"><? echo $row[csf("party_buyer_name")]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $party_arr[$row[csf("party_id")]]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $row[csf("job_no_prefix_num")]; ?></td>
                            <td   width="130" id="wrd_brk"><? echo $row[csf("buyer_style_ref")]; ?></td>
                            <td width="130" id="wrd_brk"><? echo $row[csf("buyer_po_no")]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $color_library_arr[$row[csf("gmts_color_id")]]; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><? echo $receeive_qty; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $row[csf('whisker_qty')]; ?></td>
                            <td width="80" id="wrd_brk" align="right"><?php echo $row[csf('hand_sand_qty')]; ?></td>
                            <td width="80" id="wrd_brk" align="right"><?php echo $row[csf('tagging_qty')]; ?></td>
                            <td width="80" id="wrd_brk" align="right"><?php echo $row[csf('tieing_qty')]; ?></td>
                            <td width="80" id="wrd_brk" align="right"><?php echo $row[csf('grinding_qty')]; ?></td>
                            <td width="80" id="wrd_brk" align="right"><?php echo $first_wash_qty; ?></td>
                            <td width="80" id="wrd_brk" align="right"><?php echo $row[csf('destroy_qty')]; ?></td>
                            <td width="80" id="wrd_brk" align="right"><?php echo $row[csf('pp_spray_qty')]; ?></td>
                            <td width="80" id="wrd_brk" align="right"><?php echo $final_wash_qty; ?></td>
                            <td width="80" id="wrd_brk" align="right"><?php echo $row[csf('ythreed_qty')]; ?></td>
                            <td width="80" id="wrd_brk" align="right"><?php echo $row[csf('wrinkle_qty')]; ?></td>
                            <td width="80" id="wrd_brk" align="right"><?php echo $row[csf('laser_whisker_qty')]; ?></td>
                            <td width="80" id="wrd_brk" align="right"><?php echo $row[csf('laser_brush_qty')]; ?></td>
                            <td width="80" id="wrd_brk" align="right"><?php echo $row[csf('laser_destroy_qty')]; ?></td>
                            <td width="80" id="wrd_brk" align="right"><?php echo $row[csf('laser_chemo_print_qty')]; ?></td>
                            <td width="80" id="wrd_brk" align="right"><?php echo $first_dyeing_qty; ?></td>
                            <td width="80" id="wrd_brk" align="right"><?php echo $row[csf('laser_others_qty')]; ?></td>
                            <td width="80" id="wrd_brk" align="right"><?php echo $delevery_qty; ?></td>
                            <td width="80" id="wrd_brk" align="right"><?php echo $blance_qty; ?></td>
                            <td  id="wrd_brk"><?php echo $row[csf('')]; ?></td>
						  </tr>
                       
						<?		
					$i++;
					}
                  ?>
                </table>
         	</div>
         <table width="2600" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td  width="35" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td   width="130" >&nbsp;</td>
                <td width="130" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="100" >Grand Total: </td>
                <td width="80" id="gt_receeive_qty_id"></td>
                <td width="80" id="gt_whisker_qty_id"></td>
                <td width="80" id="gt_hand_sand_qty_id"></td>
                <td width="80" id="gt_tagging_qty_id"></td>
                <td width="80" id="gt_tieing_qty_id"></td>
                <td width="80" id="gt_grinding_qty_id"></td>
                <td width="80" id="gt_first_wash_qty_id"></td>
                <td width="80" id="gt_destroy_qty_id"></td>
                <td width="80" id="gt_ythreed_qty_id"></td>
                <td width="80" id="gt_final_wash_qty_id"></td>
                <td width="80" id="gt_ythreed_qty_id"></td>
                <td width="80" id="gt_wrinkle_qty_id"></td>
                <td width="80" id="gt_laser_whisker_qty_id"></td>
                <td width="80" id="gt_laser_brush_qty_id"></td>
                <td width="80" id="gt_laser_destroy_qty_id"></td>
                <td width="80" id="gt_laser_chemo_print_qty_id"></td>
                <td width="80" id="gt_first_dyeing_qty_id"></td>
                <td width="80" id="gt_laser_others_qty_id"></td>
                <td width="80" id="gt_delevery_qty_id"></td>
                <td width="80" id="gt_blance_qty_id"></td>
                <td>&nbsp;</td>
			</tr>
		</table> 
     </div>
     </fieldset>
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
    echo "$html**$filename"; 
    exit();
}

?>