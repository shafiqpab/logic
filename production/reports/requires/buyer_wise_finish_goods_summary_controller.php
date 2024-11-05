<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 150, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company in($data) and a.status_active=1 and a.is_deleted=0 group by a.id,a.buyer_name order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
  	exit();	 
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 150, "select id,store_name from lib_store_location where status_active =1 and is_deleted=0 and company_id in($data) order by store_name","id,store_name", 1, "-- Select Store --", $selected, "",0 );     
	exit();	
}

if($action=="job_popup")
{
	echo load_html_head_contents("Search Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;var selected_id_arr = new Array;
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[0], selected_id_arr ) == -1 ) {
				selected_id_arr.push( str[0] );
				selected_id.push( str[1] );
				selected_name.push( str[2] );				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
		}
	
    </script>
	<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
	<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
	<?
	if($company_name){$company_cond=" and a.company_name in($company_name)";}
	if($job_year)
	{
		if($db_type==0){ $year_cond=" and YEAR(a.insert_date)=$job_year"; }
		else{ $year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$job_year"; }
	}

	if($db_type==0){ $year_field=", YEAR(a.insert_date) as job_year";}
	else{$year_field=", TO_CHAR(a.insert_date,'YYYY') as job_year";}

	$sql= "SELECT a.id, a.job_no, a.style_ref_no $year_field from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 $company_cond $year_cond order by a.id desc"; 
	// echo $sql;
		
	echo create_list_view("tbl_list_search", "Style,Job No,Year", "160,120","420","320",0, $sql , "js_set_value", "id,job_no","",1,"0,0,0",$arr,"style_ref_no,job_no,job_year","",'setFilterGrid("tbl_list_search",-1);','0,0,0','',1) ;

	exit(); 
}

if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($rpt_type==1)
	{

		$buyer_arr = return_library_array("SELECT id,buyer_name from lib_buyer", "id", "buyer_name");
		$room_rack_self_arr = return_library_array("SELECT floor_room_rack_id,floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");

        $search_cond='';
		if($cbo_company_name!=''){$search_cond.=" and a.company_id in($cbo_company_name) ";}
		if($cbo_store_name!=''){$search_cond.=" and a.store_id in($cbo_store_name) ";}
		if($cbo_year)
		{
			if($db_type==0){ $search_cond.=" and YEAR(c.insert_date)=$cbo_year"; }
			else{ $search_cond.=" and TO_CHAR(c.insert_date,'YYYY')=$cbo_year"; }
		}
		if($cbo_buyer_id!=0){$search_cond.=" and c.buyer_name=$cbo_buyer_id ";}
		if($hidden_job_id!='')
        {
			$job_id_arr=explode(',',$hidden_job_id);
			$job_id_in=where_con_using_array($job_id_arr,0,'c.id');
		}
 
        $date_array=array();
		if($db_type==0){
			$style_col=", group_concat(distinct(c.style_ref_no)) as STYLE_REF_NO";
			$room_col=", group_concat(distinct(a.rack_id)) as RACK_ID";
		}
		else{ 
			$style_col=", RTRIM(XMLAGG(XMLELEMENT(E,c.style_ref_no,',').EXTRACT('//text()') ORDER BY c.id).GetClobVal(),',') as STYLE_REF_NO ";
			$rack_col=", RTRIM(XMLAGG(XMLELEMENT(E,a.rack_id,',').EXTRACT('//text()') ORDER BY a.rack_id).GetClobVal(),',') as RACK_ID";
		}

		$main_sql ="SELECT c.buyer_name as BUYER_NAME, c.client_id as CLIENT_ID $style_col $rack_col,
		sum(case when a.production_type=81 then a.carton_qty else 0 end) as RECEIVE_CARTON_QNTY,
		sum(case when a.production_type=82 then a.carton_qty else 0 end) as ISSUE_CARTON_QNTY,
		sum(case when a.production_type=83 then a.carton_qty else 0 end) as ISSUE_CARTON_RTN_QNTY,
		sum(case when a.production_type=81 then a.production_quantity else 0 end) as RECEIVE_QNTY,
		sum(case when a.production_type=82 then a.production_quantity else 0 end) as ISSUE_QNTY,
		sum(case when a.production_type=83 then a.production_quantity else 0 end) as ISSUE_RTN_QNTY
		from pro_garments_production_mst a, wo_po_break_down b, wo_po_details_master c 
		where a.production_type in (81,82,83) and a.po_break_down_id=b.id and b.job_id=c.id $search_cond $job_id_in and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by c.buyer_name,c.client_id 
		order by c.buyer_name";	
								
		// echo $main_sql;die;
		$main_data = sql_select($main_sql);	

		$tbl_width=700;
		ob_start();	
		?>
		<style>
			.wrd_brk{word-break: break-all;}
			.center{text-align: center;}
			.right{text-align: right;}
		</style>

		<table width="<?=$tbl_width;?>" border="0">
			<tr class="form_caption" style="border:none;">
				<td colspan="7" align="center" style="border:none;font-size:18px; font-weight:bold" ><?=$report_title;?></td> 
			</tr>
			<tr class="form_caption" style="border:none;">
				<td colspan="7" align="center" style="border:none;font-size:16px; font-weight:bold" >[Buyer wise summary]</td> 
			</tr>
		</table>
		<table width="<?=$tbl_width+18;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
			<thead>
				<tr>
					<th width="50" >SL NO</th>
					<th width="100">Buyer</th>
					<th width="100">Buyer Client</th>
					<th width="150">Style</th>
					<th width="80" >Carton QTY</th>
					<th width="80" >PCS QTY</th>
					<th >RACK NO</th>
				</tr>
			</thead>
		</table>  
		<div style="width:<?=$tbl_width+18;?>px; overflow-y:scroll; max-height:250px" id="scroll_body" > 
			<table width="<?=$tbl_width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body"> 
				<tbody>
					<?	
						$i=1;
                        foreach($main_data as $val)
                        {	
							if ($i%2==0)$bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF"; 
							$blance_carton=$val["RECEIVE_CARTON_QNTY"]-$val["ISSUE_CARTON_QNTY"]+$val["ISSUE_CARTON_RTN_QNTY"];
							$blance_qnty=$val["RECEIVE_QNTY"]-$val["ISSUE_QNTY"]+$val["ISSUE_RTN_QNTY"];
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="50" class="center"><? echo $i; ?></td>
									<td class="wrd_brk" width="100"><? echo $buyer_arr[$val["BUYER_NAME"]]; ?></td>
									<td class="wrd_brk" width="100"><? echo $buyer_arr[$val["CLIENT_ID"]]; ?></td>
									<td class="wrd_brk" width="150"><? echo implode(", ",array_unique(explode(",",$val["STYLE_REF_NO"]->load()))); ?></td>
									<td class="wrd_brk right" width="80" ><? echo $blance_carton; ?></td>
									<td class="wrd_brk right" width="80" ><? echo $blance_qnty; ?></td>
									<td class="wrd_brk"><? 
										$rack_arr=array_unique(explode(",",$val["RACK_ID"]->load()));
										$rack_no='';
										foreach($rack_arr as $row)
										{
											if($rack_no!=''){$rack_no.=', '.$room_rack_self_arr[$row];}else{$rack_no=$room_rack_self_arr[$row];}
										}
										echo $rack_no; 
									?></td>                        
								</tr>
							<?
							$i++;
							$total_blance_carton+=$blance_carton;							
							$total_blance_qnty+=$blance_qnty;		
						} 
					?>
				</tbody>  
				<tfoot>
					<tr bgcolor="#A9D08E">
						<th colspan="4">TTL</th>
						<th ><?=$total_blance_carton;?></th>
						<th ><?=$total_blance_qnty;?></th>
						<th ></th>
					</tr>
				</tfoot>
			</table> 
		</div>
			
		<?
	}
	else if($rpt_type==2)
	{

		$buyer_arr = return_library_array("SELECT id,buyer_name from lib_buyer", "id", "buyer_name");
		$room_rack_self_arr = return_library_array("SELECT floor_room_rack_id,floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");

        $search_cond='';
		if($cbo_company_name!=''){$search_cond.=" and b.company_id in($cbo_company_name) ";}
		if($cbo_store_name!=''){$search_cond.=" and b.store_id in($cbo_store_name) ";}
		if($cbo_year)
		{
			if($db_type==0){ $search_cond.=" and YEAR(d.insert_date)=$cbo_year"; }
			else{ $search_cond.=" and TO_CHAR(d.insert_date,'YYYY')=$cbo_year"; }
		}
		if($cbo_buyer_id!=0){$search_cond.=" and d.buyer_name=$cbo_buyer_id ";}
		if($hidden_job_id!='')
        {
			$job_id_arr=explode(',',$hidden_job_id);
			$job_id_in=where_con_using_array($job_id_arr,0,'d.id');
		}
 
        $date_array=array();
		if($db_type==0){
			$po_id=", group_concat(distinct(c.id)) as PO_ID";
			$iss_remark=", group_concat(distinct(a.REMARKS)) as ISS_REMARKS";
		}
		else{ 
			$po_id=", RTRIM(XMLAGG(XMLELEMENT(E,c.id,',').EXTRACT('//text()') ORDER BY c.id).GetClobVal(),',') as PO_ID ";
			$iss_remark=", RTRIM(XMLAGG(XMLELEMENT(E,case when b.production_type=82 then a.remarks else null end,',').EXTRACT('//text()') ORDER BY a.id).GetClobVal(),',') as ISS_REMARKS";
		}

		$main_sql ="SELECT d.buyer_name as BUYER_NAME $po_id $iss_remark,
		sum(case when b.production_type=81 then b.carton_qty else 0 end) as RECEIVE_CARTON_QNTY,
		sum(case when b.production_type=82 and a.purpose_id=1 then b.carton_qty else 0 end) as ISSUE_CARTON_QNTY,
		sum(case when b.production_type=83 then b.carton_qty else 0 end) as ISSUE_CARTON_RTN_QNTY,
		sum(case when b.production_type=81 then b.production_quantity else 0 end) as RECEIVE_QNTY,
		sum(case when b.production_type=82 and a.purpose_id=1 then b.production_quantity else 0 end) as ISSUE_QNTY,
		sum(case when b.production_type=83 then b.production_quantity else 0 end) as ISSUE_RTN_QNTY
		from pro_gmts_delivery_mst a, pro_garments_production_mst b, wo_po_break_down c, wo_po_details_master d 
		where a.id=b.delivery_mst_id and b.production_type in (81,82,83) and b.po_break_down_id=c.id and c.job_id=d.id $search_cond $job_id_in and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 
		group by d.buyer_name
		order by d.buyer_name";		
		// echo $main_sql;die;

		$main_data = sql_select($main_sql);	

		foreach($main_data as $val)
		{
			$all_po_id.=implode(",",array_unique(explode(",",$val["PO_ID"]->load()))).',';
		}
		$all_po_id=explode(",",rtrim($all_po_id,","));
		$all_po_in=where_con_using_array($all_po_id,0,"c.id");
		$po_sql ="SELECT c.id as PO_ID,(c.po_quantity*d.total_set_qnty) as PO_QNTY_PCS
		from wo_po_break_down c, wo_po_details_master d 
		where c.job_id=d.id $all_po_in and c.status_active=1 and d.status_active=1 ";	
		// echo $po_sql;die;

		$po_data = sql_select($po_sql);	
		$po_data_arr=array();
		foreach($po_data as $val)
		{
			$po_data_arr[$val["PO_ID"]]=$val["PO_QNTY_PCS"];
		}

		$tbl_width=700;
		ob_start();	
		?>
		<style>
			.wrd_brk{word-break: break-all;}
			.center{text-align: center;}
			.right{text-align: right;}
		</style>

		<table width="<?=$tbl_width;?>" border="0">
			<tr class="form_caption" style="border:none;">
				<td colspan="7" align="center" style="border:none;font-size:18px; font-weight:bold" ><?=$report_title;?></td> 
			</tr>
			<tr class="form_caption" style="border:none;">
				<td colspan="7" align="center" style="border:none;font-size:16px; font-weight:bold" >[Buyer wise summary]</td> 
			</tr>
		</table>
		<table width="<?=$tbl_width+18;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
			<thead>
				<tr>
					<th width="50" >SL NO</th>
					<th width="100">Buyer</th>
					<th width="80">Order Qty</th>
					<th width="80">Total Pack in Pcs</th>
					<th width="80">Delivery Qty</th>
					<th width="80" >Balance in Pcs</th>
					<th width="80" >Balance in Ctns</th>
					<th >Remarks</th>
				</tr>
			</thead>
		</table>  
		<div style="width:<?=$tbl_width+18;?>px; overflow-y:scroll; max-height:250px" id="scroll_body" > 
			<table width="<?=$tbl_width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body"> 
				<tbody>
					<?	
						$i=1;
                        foreach($main_data as $val)
                        {	
							if ($i%2==0)$bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF"; 
							$blance_carton=$val["RECEIVE_CARTON_QNTY"]+$val["ISSUE_CARTON_QNTY"]+$val["ISSUE_CARTON_RTN_QNTY"];
							$rcv_qnty=$val["RECEIVE_QNTY"]+$val["ISSUE_RTN_QNTY"];
							$po_qnty_pcs=0;
							$all_po_arr=array_unique(explode(",",$val["PO_ID"]->load()));
							foreach($all_po_arr as $row)
							{
								$po_qnty_pcs+=$po_data_arr[$row];
							}
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="50" class="center"><?=$i++; ?></td>
									<td class="wrd_brk" width="100"><?=$buyer_arr[$val["BUYER_NAME"]]; ?></td>
									<td class="wrd_brk right" width="80"><?=$po_qnty_pcs; ?></td>
									<td class="wrd_brk right" width="80" ><?=$rcv_qnty; ?></td>
									<td class="wrd_brk right" width="80" ><?=$val["ISSUE_QNTY"]; ?></td>
									<td class="wrd_brk right" width="80" ><?=$rcv_qnty-$val["ISSUE_QNTY"]; ?></td>
									<td class="wrd_brk right" width="80" ><?=$blance_carton; ?></td>
									<td class="wrd_brk"><?=implode(", ",array_filter(explode(",",$val["ISS_REMARKS"]->load()))) ;?></td>                        
								</tr>
							<?
							$total_po_qnty+=$po_qnty_pcs;							
							$total_rcv_qnty+=$rcv_qnty;		
							$total_issue_qnty+=$val["ISSUE_QNTY"];							
							$total_blance_pcs+=$total_rcv_qnty-$val["ISSUE_QNTY"];							
							$total_blance_carton+=$blance_carton;							
						} 
					?>
				</tbody>  
				<tfoot>
					<tr bgcolor="#A9D08E">
						<th ></th>
						<th >GRAND TOTAL</th>
						<th ><?=$total_po_qnty;?></th>
						<th ><?=$total_rcv_qnty;?></th>
						<th ><?=$total_issue_qnty;?></th>
						<th ><?=$total_blance_pcs;?></th>
						<th ><?=$total_blance_carton;?></th>
						<th ></th>
					</tr>
				</tfoot>
			</table> 
		</div>
			
		<?
	}
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$rpt_type"; 
	exit();
}

?>