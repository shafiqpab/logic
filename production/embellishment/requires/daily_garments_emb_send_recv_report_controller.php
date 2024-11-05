<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "","" );     	 
	exit();
}


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}
//order search------------------------------//
if($action=="order_surch")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str_or = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push( str_or );				
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					selected_no.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					num += selected_no[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 ); 
				num 	= num.substr( 0, num.length - 1 );
				//alert(num);
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
				$('#txt_selected_no').val( num );
		}
    </script>
    <?
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$job_year=str_replace("'","",$job_year);
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	
	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";
	if($db_type==0)
	{
		if($job_year!=0) $job_year_cond=" and year(b.insert_date)=$job_year"; else $job_year_cond="";
		$select_date=" year(b.insert_date)";
	}
	else if($db_type==2)
	{
		if($job_year!=0) $job_year_cond=" and to_char(b.insert_date,'YYYY')=$job_year"; else $job_year_cond="";
		$select_date=" to_char(b.insert_date,'YYYY')";
	}
	if($txt_style_ref_id!="") $style_cond=" and b.id in($txt_style_ref_id)"; else $style_cond="";
	$sql = "select a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$company $buyer_cond $job_year_cond  and a.status_active=1 and b.status_active=1"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Order NO,Job No,Year,Style Ref No","150,80,70,150","500","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_order_id_no;?>';
	var style_id='<? echo $txt_order_id;?>';
	var style_des='<? echo $txt_order;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    
    <?
	exit();
}

if($action=="report_generate") 
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_title=str_replace("'","",$report_title);

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_w_company_name=str_replace("'","",$cbo_w_company_name);
	$cbo_location_id=str_replace("'","",$cbo_location_id); 
	$cbo_production_type=str_replace("'","",$cbo_production_type); 
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_search_text=trim(str_replace("'","",$txt_search_text));
	
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	if($cbo_w_company_name==0) $cbo_w_company_name_cond=""; else $cbo_w_company_name_cond=" and a.SENDING_COMPANY in($cbo_w_company_name) ";
	if($cbo_location_id==0) $location_id_cond=""; else $location_id_cond=" and a.SENDING_LOCATION in($cbo_location_id) ";
	if($cbo_production_type==0) $cbo_production_type_cond=""; else $cbo_production_type_cond=" and b.production_type in($cbo_production_type) ";

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		$buyer_id_cond="";
	}
	else
	{
		$buyer_id_cond=" and d.buyer_name in($cbo_buyer_name)";
	}
	
	$orer_job_cond="";
	
	if($cbo_search_by == 2 && $txt_search_text !="")
	{
		$order_job_cond=" and  c.po_number LIKE '%$txt_search_text%'";
		//$order_search_cond=" and  a.po_break_down_id = '$txt_search_text'";
	}
	else
	{
		$order_job_cond=" and d.style_ref_no  like '%$txt_search_text%'";
	}
	
	$date_cond='';	$ex_fact_date_cond='';$est_date_cond='';$reso_date_cond='';
		
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and a.production_date between '$start_date' and '$end_date'";
		}
		
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier ","id","supplier_name");
	$locationArr = return_library_array("select id,location_name from lib_location ","id","location_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$itemArr = return_library_array("select id,item_name from  lib_garment_item ","id","item_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	
	//================================== for receive ==================================
	$sql="SELECT a.COMPANY_ID,  a.PRODUCTION_DATE,a.production_source,  a.SERVING_COMPANY,  a.LOCATION,  a.PO_BREAK_DOWN_ID,  a.ITEM_NUMBER_ID,  b.PRODUCTION_TYPE,  b.COLOR_SIZE_BREAK_DOWN_ID,
		  SUM(
		  CASE
			WHEN a.PRODUCTION_TYPE = 3
			THEN b.PRODUCTION_QNTY
			ELSE 0
		  END) AS receive_qnty,
		  SUM(
		  CASE
			WHEN a.PRODUCTION_TYPE = 2
			THEN b.PRODUCTION_QNTY
			ELSE 0
		  END) AS issue_qnty,  a.EMBEL_NAME,  a.EMBEL_TYPE,  a.SENDING_COMPANY,  a.SENDING_LOCATION,  d.JOB_NO,  c.PO_NUMBER,  c.SHIPMENT_DATE, d.style_ref_no, d.BUYER_NAME,  e.COLOR_NUMBER_ID,
		  e.SIZE_NUMBER_ID,
		  SUM(e.PLAN_CUT_QNTY) AS PLAN_CUT_QNTY
		FROM PRO_GARMENTS_PRODUCTION_MST a
		INNER JOIN PRO_GARMENTS_PRODUCTION_DTLS b
		ON a.ID = b.MST_ID
		INNER JOIN WO_PO_BREAK_DOWN c
		ON a.PO_BREAK_DOWN_ID = c.ID
		INNER JOIN WO_PO_DETAILS_MASTER d
		ON c.JOB_NO_MST = d.JOB_NO
		INNER JOIN WO_PO_COLOR_SIZE_BREAKDOWN e
		ON e.ID                = b.COLOR_SIZE_BREAK_DOWN_ID
		WHERE b.STATUS_ACTIVE  = 1
		AND a.STATUS_ACTIVE    = 1
		AND a.PRODUCTION_TYPE IN (2, 3)
		AND b.PRODUCTION_QNTY IS NOT NULL
		$cbo_w_company_name_cond $location_id_cond $date_cond $order_job_cond $buyer_id_cond
		
		GROUP BY a.COMPANY_ID,  a.PRODUCTION_DATE, a.production_source, a.SERVING_COMPANY,  a.LOCATION,  a.PO_BREAK_DOWN_ID,  a.ITEM_NUMBER_ID,  b.PRODUCTION_TYPE,  b.COLOR_SIZE_BREAK_DOWN_ID,
	    a.EMBEL_NAME,  a.EMBEL_TYPE,  a.SENDING_COMPANY,  a.SENDING_LOCATION,  d.JOB_NO,  c.PO_NUMBER,  c.SHIPMENT_DATE,  d.style_ref_no, d.BUYER_NAME,  e.COLOR_NUMBER_ID,  e.SIZE_NUMBER_ID order by a.PRODUCTION_DATE, d.BUYER_NAME";
	//echo $sql; die;
	$sql_result = sql_select($sql);
	
	foreach( $sql_result as $row)
	{
		$data_arr [$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['production_date']=$row[csf('production_date')];
		$data_arr [$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['production_source']=$row[csf('production_source')];
		$data_arr [$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['SERVING_COMPANY']=$row[csf('SERVING_COMPANY')];
		$data_arr [$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['SENDING_COMPANY']=$row[csf('SENDING_COMPANY')];
		$data_arr [$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['SENDING_LOCATION']=$row[csf('SENDING_LOCATION')];
		$data_arr [$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['po_break_down_id']=$row[csf('po_break_down_id')];
		$data_arr [$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['item_number_id']=$row[csf('item_number_id')];
		$data_arr [$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['color_number_id']=$row[csf('color_number_id')];
		
		$data_arr [$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['plan_cut_qnty']+=$row[csf('plan_cut_qnty')];
		$data_arr [$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['receive_qnty']+=$row[csf('receive_qnty')];
		$data_arr [$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['issue_qnty']+=$row[csf('issue_qnty')];
		$data_arr [$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['po_number']=$row[csf('po_number')];
		$data_arr [$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$data_arr [$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['buyer_name']=$row[csf('buyer_name')];
		$data_arr [$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['job_no']=$row[csf('job_no')];
		
		$po_no_array[$row[csf('po_break_down_id')]]=array("po_break_down_id"=>$row[csf('po_break_down_id')],"location"=>$row[csf('location')],"buyer_name"=>$row[csf('buyer_name')],"style_ref_no"=>$row[csf('style_ref_no')]);									

		$job_po_size_array[$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_po_color_size_qnty_array[$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('issue_qnty')];
		$job_po_color_qnty_array[$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf('issue_qnty')];
			
		$serv_data[$row[csf('production_source')]][$row[csf('SERVING_COMPANY')]]['production_source']=$row[csf('production_source')];
		$serv_data[$row[csf('production_source')]][$row[csf('SERVING_COMPANY')]]['issue_qnty']+=$row[csf('issue_qnty')];
		$serv_data[$row[csf('production_source')]][$row[csf('SERVING_COMPANY')]]['receive_qnty']+=$row[csf('receive_qnty')];
		$serv_data[$row[csf('production_source')]][$row[csf('SERVING_COMPANY')]]['plan_cut_qnty']+=$row[csf('plan_cut_qnty')];
	}

	//echo "<pre>";print_r($data_arr);
	//echo $sql;
	ob_start();
	?>
	<div align = "center" style="width:1580px">
        <fieldset style="width:100%;">	
			<table id="table_header_1" class="rpt_table" width="480" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<caption><strong>SUMMERY Data</strong> </caption>
                <thead>
                    <tr>
						<th align="center" style="word-break: break-word;" width="130">Serving Company</th>
						<th align="center" style="word-break: break-word;" width="130">Source</th>
						<th align="center" style="word-break: break-word;" width="120">Issue Qty</th>
                        <th align="center" style="word-break: break-word;">Rcvd Qty</th>
                    </tr>	
                </thead>
				<tbody>
				<?
				$i=1;
				foreach($serv_data as $source_id=>$source_data)
				{
					foreach($source_data as $serv_id=>$dr)
					{
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td align="left" style="word-break: break-word;" width="130"><? if($source_id==1){echo $companyArr[$serv_id];}else  echo $supplierArr[$serv_id]; ?></td>
							<td align="left" style="word-break: break-word;" width="130"><? if($source_id==1){echo "Inhouse";}else  echo "Outside"; ?></td>
							<td align="right" style="word-break: break-word;" width="120"><? echo $dr['issue_qnty'];?></td>
							<td align="right" style="word-break: break-word;"><? echo $dr['receive_qnty']; ?></td>
						</tr>
					<?
						$t_issue +=$dr['issue_qnty']; 
						$t_rcvd +=$dr['receive_qnty']; 
						$i++;
					}
				}
				?>
				</tbody>
				<tfoot>
					<tr>
						<td align="right"><strong></strong></td>
						<td align="right"><strong>Total :</strong></td>
						<td align="right"><strong><?echo $t_issue;?></strong></td>
                        <td align="right"> <strong><?echo $t_rcvd;?></strong></td>
                    </tr>
				</tfoot>
            </table>
			<br>
            <table id="table_header_1" class="rpt_table" width="1580" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<caption><strong><? echo '<br>'.$report_title.'('.$production_type[$cbo_production_type].')'.'<br>'.$company_arr[$company_name].'<br>'.$start_date.' To '.$end_date;?> </strong> </caption>
                <thead>
                    <tr>
                        <th width="30">SL</th>
						<th style="word-break: break-word;" width="130">Working Company</th>
						<th style="word-break: break-word;" width="120">Location</th>
                        <th style="word-break: break-word;" width="120">Buyer</th>
                        <th style="word-break: break-word;" width="130">Style</th>
                        <th style="word-break: break-word;" width="120">Order No</th>
                        <th style="word-break: break-word;" width="120">Gmt Item</th>
						<th style="word-break: break-word;" width="120">Color</th>
						<?
							foreach($job_po_size_array as $job_id=>$job_value)
							{
								foreach($job_value as $po_id=>$po_value)
								{
									foreach($po_value as $size_id=>$size_value)
									{
										if($size_value !="")
										{
											?>
												<th width="60"><? echo $itemSizeArr[$size_value];?></th>
											<?
										}
									}
								}
							}
						?>
                        <th  align="right" width="">Total</th>
                    </tr>
                </thead>
            </table>
            <div style="width:1580px; max-height:400px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="1560" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                	<?
					$i =1;
						
							foreach($data_arr as $scom_id => $scom_value)
							{		
								foreach($scom_value as $loc_id => $loc_value)
								{	
									foreach($loc_value as $job_id => $job_value)
									{
										foreach($job_value as $po_id => $po_value)
										{				
											foreach($po_value as $item_id => $item_value)
											{
												foreach($item_value as $col_id => $dr)
												{
													?>
													<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
														<td width="30"><? echo $i;?></td>
														<td style="word-break: break-all;"  width="130"><? echo $companyArr[$dr['SENDING_COMPANY']];?></td>
														<td style="word-break: break-all;" width="120"><? echo $locationArr[$dr['SENDING_LOCATION']];?></td>
														<td style="word-break: break-all;" width="120"><? echo $buyer_arr[$dr['buyer_name']];?></td>
														<td style="word-break: break-all;" width="130"><? echo $dr['style_ref_no'];?></td>
														<td style="word-break: break-all;" width="120"><? echo $dr['po_number'];?></td>
														<td style="word-break: break-all;" width="120"><? echo $itemArr[$dr['item_number_id']];?></td> 
														<td style="word-break: break-all;" width="120"><? echo $colorArr[$dr['color_number_id']];?></td>
														<?
															foreach($job_po_size_array as $job_ids=>$job_values)
															{
																foreach($job_values as $po_ids=>$po_values)
																{
																	foreach($po_values as $size_ids=>$size_values)
																	{
																		if($size_values !="")
																		{
																			?>
																				<td  width="60" align="right"><? echo $job_po_color_size_qnty_array[$job_id][$po_id][$item_id][$col_id][$size_values];?></td>
																			<?
																		}
																	}
																}
															}
														?>
														<td align="center" ><strong><? echo $job_po_color_qnty_array[$job_id][$po_id][$item_id][$col_id];?></strong></td>
													</tr>
													<?
												$i++;
												}
											}
										}
									}
								}
							}
						
						
					?>
                </table>
            </div>
            <table class="rpt_table" width="1580" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tfoot>
                	<td  colspan ="11" align="right"><strong></strong></td>
                </tfoot>
            </table>
        </fieldset>
	</div>
	<?	
	
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
    echo "$html****$filename****$report_type"; 
	exit();	
}

if($action=="report_generate_date") 
{ 
		$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_title=str_replace("'","",$report_title);

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_w_company_name=str_replace("'","",$cbo_w_company_name);
	$cbo_location_id=str_replace("'","",$cbo_location_id); 
	$cbo_production_type=str_replace("'","",$cbo_production_type); 
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_search_text=trim(str_replace("'","",$txt_search_text));
	
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	if($cbo_w_company_name==0) $cbo_w_company_name_cond=""; else $cbo_w_company_name_cond=" and a.SENDING_COMPANY in($cbo_w_company_name) ";
	if($cbo_location_id==0) $location_id_cond=""; else $location_id_cond=" and a.SENDING_LOCATION in($cbo_location_id) ";
	if($cbo_production_type==0) $cbo_production_type_cond=""; else $cbo_production_type_cond=" and b.production_type in($cbo_production_type) ";

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		$buyer_id_cond="";
	}
	else
	{
		$buyer_id_cond=" and d.buyer_name in($cbo_buyer_name)";
	}
	
	$orer_job_cond="";
	
	if($cbo_search_by == 2 && $txt_search_text !="")
	{
		$order_job_cond=" and  c.po_number LIKE '%$txt_search_text%'";
		//$order_search_cond=" and  a.po_break_down_id = '$txt_search_text'";
	}
	else
	{
		$order_job_cond=" and d.style_ref_no  like '%$txt_search_text%'";
	}
	
	$date_cond='';	$ex_fact_date_cond='';$est_date_cond='';$reso_date_cond='';
		
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and a.production_date between '$start_date' and '$end_date'";
		}
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier ","id","supplier_name");
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$locationArr = return_library_array("select id,location_name from lib_location ","id","location_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$itemArr = return_library_array("select id,item_name from  lib_garment_item ","id","item_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	//================================== for receive ==================================
	$sql="SELECT a.COMPANY_ID,  a.PRODUCTION_DATE, a.production_source,  a.SERVING_COMPANY,  a.LOCATION,  a.PO_BREAK_DOWN_ID,  a.ITEM_NUMBER_ID,  b.PRODUCTION_TYPE,  b.COLOR_SIZE_BREAK_DOWN_ID,
		  SUM(
		  CASE
			WHEN a.PRODUCTION_TYPE = 3
			THEN b.PRODUCTION_QNTY
			ELSE 0
		  END) AS receive_qnty,
		  SUM(
		  CASE
			WHEN a.PRODUCTION_TYPE = 2
			THEN b.PRODUCTION_QNTY
			ELSE 0
		  END) AS issue_qnty,  a.EMBEL_NAME,  a.EMBEL_TYPE,  a.SENDING_COMPANY,  a.SENDING_LOCATION,  d.JOB_NO,  c.PO_NUMBER,  c.SHIPMENT_DATE, d.style_ref_no, d.BUYER_NAME,  e.COLOR_NUMBER_ID,
		  e.SIZE_NUMBER_ID,
		  SUM(e.PLAN_CUT_QNTY) AS PLAN_CUT_QNTY
		FROM PRO_GARMENTS_PRODUCTION_MST a
		INNER JOIN PRO_GARMENTS_PRODUCTION_DTLS b
		ON a.ID = b.MST_ID
		INNER JOIN WO_PO_BREAK_DOWN c
		ON a.PO_BREAK_DOWN_ID = c.ID
		INNER JOIN WO_PO_DETAILS_MASTER d
		ON c.JOB_NO_MST = d.JOB_NO
		INNER JOIN WO_PO_COLOR_SIZE_BREAKDOWN e
		ON e.ID                = b.COLOR_SIZE_BREAK_DOWN_ID
		WHERE b.STATUS_ACTIVE  = 1
		AND a.STATUS_ACTIVE    = 1
		AND a.PRODUCTION_TYPE IN (2, 3)
		AND b.PRODUCTION_QNTY IS NOT NULL
		$cbo_w_company_name_cond $location_id_cond $date_cond $order_job_cond $buyer_id_cond
		
		GROUP BY a.COMPANY_ID,  a.PRODUCTION_DATE, a.production_source, a.SERVING_COMPANY,  a.LOCATION,  a.PO_BREAK_DOWN_ID,  a.ITEM_NUMBER_ID,  b.PRODUCTION_TYPE,  b.COLOR_SIZE_BREAK_DOWN_ID,
	    a.EMBEL_NAME,  a.EMBEL_TYPE,  a.SENDING_COMPANY,  a.SENDING_LOCATION,  d.JOB_NO,  c.PO_NUMBER,  c.SHIPMENT_DATE,  d.style_ref_no, d.BUYER_NAME,  e.COLOR_NUMBER_ID,  e.SIZE_NUMBER_ID order by a.PRODUCTION_DATE, d.BUYER_NAME";
	//echo $sql; die;
	$sql_result = sql_select($sql);
	
	foreach( $sql_result as $row)
	{
		$data_arr [$row[csf('production_date')]] [$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['production_date']=$row[csf('production_date')];
		$data_arr [$row[csf('production_date')]] [$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['production_source']=$row[csf('production_source')];
		$data_arr [$row[csf('production_date')]][$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['SENDING_COMPANY']=$row[csf('SENDING_COMPANY')];
		$data_arr [$row[csf('production_date')]][$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['SENDING_LOCATION']=$row[csf('SENDING_LOCATION')];
		$data_arr [$row[csf('production_date')]][$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['po_break_down_id']=$row[csf('po_break_down_id')];
		$data_arr [$row[csf('production_date')]][$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['item_number_id']=$row[csf('item_number_id')];
		$data_arr [$row[csf('production_date')]][$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['color_number_id']=$row[csf('color_number_id')];
		
		$data_arr [$row[csf('production_date')]][$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['plan_cut_qnty']+=$row[csf('plan_cut_qnty')];
		$data_arr [$row[csf('production_date')]][$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['receive_qnty']+=$row[csf('receive_qnty')];
		$data_arr [$row[csf('production_date')]] [$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['issue_qnty']+=$row[csf('issue_qnty')];
		$data_arr [$row[csf('production_date')]] [$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['po_number']=$row[csf('po_number')];
		$data_arr [$row[csf('production_date')]][$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$data_arr [$row[csf('production_date')]][$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['buyer_name']=$row[csf('buyer_name')];
		$data_arr [$row[csf('production_date')]][$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['job_no']=$row[csf('job_no')];
		

		$color_recv_total[$row[csf('production_date')]][$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf('receive_qnty')];
		$color_issue_total[$row[csf('production_date')]][$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf('issue_qnty')];
		$po_recv_total[$row[csf('production_date')]][$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]]+=$row[csf('receive_qnty')];
		$po_issue_total[$row[csf('production_date')]][$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]][$row[csf('job_no')]][$row[csf('po_break_down_id')]]+=$row[csf('issue_qnty')];
		$location_recv_total[$row[csf('production_date')]][$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]]+=$row[csf('receive_qnty')];
		$location_issue_total[$row[csf('production_date')]][$row[csf('SENDING_COMPANY')]][$row[csf('SENDING_LOCATION')]]+=$row[csf('issue_qnty')];
		$date_rcvd_total[$row[csf('production_date')]]+=$row[csf('receive_qnty')];
		$date_issue_total[$row[csf('production_date')]]+=$row[csf('issue_qnty')];
		
		
		$serv_data[$row[csf('production_source')]][$row[csf('SERVING_COMPANY')]]['production_source']=$row[csf('production_source')];
		$serv_data[$row[csf('production_source')]][$row[csf('SERVING_COMPANY')]]['issue_qnty']+=$row[csf('issue_qnty')];
		$serv_data[$row[csf('production_source')]][$row[csf('SERVING_COMPANY')]]['receive_qnty']+=$row[csf('receive_qnty')];
		$serv_data[$row[csf('production_source')]][$row[csf('SERVING_COMPANY')]]['plan_cut_qnty']+=$row[csf('plan_cut_qnty')];
	}
	//echo "<pre>";print_r($color_total);
	//echo $sql;
	ob_start();
	?>
	<div align = "center" style="width:1330px">
        <fieldset style="width:98%;">	
			
			<div style="width:1280px;" align="center">	
			<table id="table_header_1" class="rpt_table" width="480" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<caption><strong>SUMMERY Data</strong> </caption>
                <thead>
                    <tr>
						<th align="center" style="word-break: break-word;" width="130">Serving Company</th>
						<th align="center" style="word-break: break-word;" width="130">Source</th>
						<th align="center" style="word-break: break-word;" width="120">Issue Qty</th>
                        <th align="center" style="word-break: break-word;">Rcvd Qty</th>
                    </tr>	
                </thead>
				<tbody>
				<?
				$i=1;
				foreach($serv_data as $source_id=>$source_data)
				{
					foreach($source_data as $serv_id=>$dr)
					{
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td align="left" style="word-break: break-word;" width="130"><? if($source_id==1){echo $companyArr[$serv_id];}else  echo $supplierArr[$serv_id]; ?></td>
							<td align="left" style="word-break: break-word;" width="130"><? if($source_id==1){echo "Inhouse";}else  echo "Outside"; ?></td>
							<td align="right" style="word-break: break-word;" width="120"><? echo $dr['issue_qnty'];?></td>
							<td align="right" style="word-break: break-word;"><? echo $dr['receive_qnty']; ?></td>
						</tr>
					<?
						$t_issue +=$dr['issue_qnty']; 
						$t_rcvd +=$dr['receive_qnty']; 
						$i++;
					}
				}
				?>
				</tbody>
				<tfoot>
					<tr>
						<td align="right"><strong></strong></td>
						<td align="right"><strong>Total :</strong></td>
						<td align="right"><strong><?echo $t_issue;?></strong></td>
                        <td align="right"> <strong><?echo $t_rcvd;?></strong></td>
                    </tr>
				</tfoot>
            </table>
			<br>
				<table id="table_header_1" class="rpt_table" width="1280" cellpadding="0" cellspacing="0" border="1" rules="all">
					<caption><strong><? echo '<br>'.$report_title.'<br>'.$company_arr[$company_name].'<br>'.$start_date.' To '.$end_date;?> </strong> </caption>
					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="80">Prod Date</th>
							<th width="130">Working Company</th>
							<th width="120">Location</th>
							<th width="120">Buyer</th>
							<th width="130">Style</th>
							<th width="120">Order No</th>
							<th width="120">Gmt Item</th>
							<th width="120">Color</th>
							<th width="80">Size</th>
							<th width="80">Send Qty</th>
							<th>Rcvd Qty</th>
						</tr>
					</thead>
				</table>
			</div>
			<div style="width:1280px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="1260" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tbody>
						<?
						$i =1;
						foreach($data_arr as $date_id => $date_value)
						{
							foreach($date_value as $scom_id => $scom_value)
							{		
								foreach($scom_value as $loc_id => $loc_value)
								{	
									foreach($loc_value as $job_id => $job_value)
									{
										foreach($job_value as $po_id => $po_value)
										{				
											foreach($po_value as $item_id => $item_value)
											{
												foreach($item_value as $col_id => $dr)
												{
														?>
														<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
															<td width="30"><? echo $i;?></td>
															<td width="80"><? echo $dr['production_date'];?></td>
															<td style="word-break: break-all;"width="130"><? echo $companyArr[$dr['SENDING_COMPANY']];?></td>
															<td style="word-break: break-all;"width="120"><? echo $locationArr[$dr['SENDING_LOCATION']];?></td>
															<td style="word-break: break-all;"width="120"><? echo $buyer_arr[$dr['buyer_name']];?></td>
															<td style="word-break: break-all;" width="130"><? echo $dr['style_ref_no'];?></td>
															<td width="120"><? echo $dr['po_number'];?></td>
															<td style="word-break: break-all;"width="120"><? echo $itemArr[$dr['item_number_id']];?></td> 
															<td style="word-break: break-all;"width="120"><? echo $colorArr[$dr['color_number_id']];?></td>
															<td width="80"><? echo $itemSizeArr[$dr['size_number_id']];?></td>
															<td width="80" align="right"><? echo $dr['issue_qnty'];?></td>
															<td align = "right"><? echo $dr['receive_qnty'];?></td>
														</tr>
														<?
													$i++;
													$grand_rcvd_total_prod += $dr['receive_qnty'];
													$grand_issue_total_prod += $dr['issue_qnty'];
													
												}
											}
										}			
										?>
										<tr bgcolor="<? echo '#d3d5d8';?>">
											<td  colspan ="9"  align = "right"><strong>PO Total :</strong></td>
											<td  align = "right"></td>
											<td align = "right"><strong><? echo $po_issue_total[$date_id][$scom_id][$loc_id][$job_id][$po_id];?></strong></td>
											<td align = "right"><strong><? echo $po_recv_total[$date_id][$scom_id][$loc_id][$job_id][$po_id];?></strong></td>
										</tr>
										
										<?

									}
								}
							}
							?>
							<tr bgcolor="<? echo '#d3d5d8';?>">
								<td  colspan ="9"  align = "right"><strong>Date Total :<?echo ' ['.$date_id.'] =';?></strong></td>
								<td  align = "right"></td>
								<td align = "right"><strong><? echo $date_issue_total[$date_id];?></strong></td>
								<td align = "right"><strong><? echo $date_rcvd_total[$date_id];?></strong></td>
							</tr>
							
							<?
						}
							
						?>
					</tbody>
					<tfoot bgcolor="<? echo '#949698'?>">
						<td  colspan ="9" align="right"><strong>Grand Total :</strong></td>
						<td  align = "right"></td>
						<td align = "right"><strong><? echo $grand_issue_total_prod;?></strong></td>
						<td align = "right"><strong><? echo $grand_rcvd_total_prod;?></strong></td>
					</tfoot>
				</table>
			</div>
        </fieldset>
	</div>
	<?	
	
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
    echo "$html****$filename****$report_type"; 
	exit();	
}

//--------------------------------------------------------------------------------------------------------------------

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;
	
	if($search_type==1 || $search_type==2 || $search_type==3) $search_cond=$job_no;
	//else if($search_type==2) $search_cond=$job_no;
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
					?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<? //echo $search_cond;?>" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $search_type; ?>'+'**'+'<? echo $job_no; ?>'+'**'+'<? echo $po_no; ?>', 'create_job_no_search_list_view', 'search_div', 'daily_garments_emb_send_recv_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}
if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	$search_type=$data[6];
	$po_no=$data[8];
	$job_no=$data[7];
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="a.style_ref_no";
	 else if($search_by==1) $search_field="a.job_no";
	 else $search_field="b.po_number";
	//if($job_no!='') $job_no_cond="and a.job_no_prefix_num=$job_no"; else $job_no_cond="";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	if($search_type==1)
	{
		$search_filed="id,job_no_prefix_num";
	}
	else if($search_type==2)
	{
		$search_filed="po_id,po_number";
	}
	else
	{
		$search_filed="id,style_ref_no";
	}
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.id as po_id,b.po_number, $year_field from wo_po_details_master a,wo_po_break_down b where   b.job_no_mst=a.job_no and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No,Po No", "120,130,80,60,100","700","240",0, $sql , "js_set_value", "$search_filed", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "",'','0,0,0,0,0,0','') ;
	exit(); 
} // Job Search end

?>