<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
	
		var selected_id = new Array, selected_name = new Array();
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
			
		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];
		
			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( ddd );
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
                    <th>
                    	<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                        <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    	<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    </th> 					
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
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'order_wise_grey_fabric_stock_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	
	if($db_type==0)
	{
		if($year_id!=0) $year_search_cond=" and year(insert_date)=$year_id"; else $year_search_cond="";
		$year_cond= "year(insert_date)as year";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(insert_date,'YYYY')=$year_id"; else $year_search_cond="";
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
		
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_cond from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_search_cond $month_cond order by job_no DESC";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","620","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
   exit(); 
} 

$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
$machine_arr=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
$supplier_arr=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$constuction_arr=array(); $composition_arr=array(); $type_array=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach($data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			
			$constuction_arr[$row[csf('id')]]=$row[csf('construction')];
			
			if($row[csf('type_id')]>0)
			{
				$type_array[$row[csf('id')]].=$yarn_type[$row[csf('type_id')]].",";
			}
		}
	}
	
	if(str_replace("'","",$cbo_buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and b.buyer_name=$cbo_buyer_id";
	}
	
	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and b.job_no_prefix_num in ($job_no) ";
	$year_id=str_replace("'","",$cbo_year);

	$year_cond="";
	if($year_id!=0)
	{
		if($db_type==0)
		{
			$year_cond=" and year(b.insert_date)=$year_id";
		}
		else
		{
			$year_cond=" and TO_CHAR(b.insert_date,'YYYY')=$year_id";
		}
	}

	$txt_order_no=trim(str_replace("'","",$txt_order_no));
	$txt_file_no=trim(str_replace("'","",$txt_file_no));
	$txt_ref_no=trim(str_replace("'","",$txt_ref_no));
	
	if($txt_order_no!="") $po_cond=" and a.po_number LIKE '".trim($txt_order_no)."%'";
	if($txt_file_no!="") $file_cond=" and a.file_no LIKE '".trim($txt_file_no)."%'";
	if($txt_ref_no!="") $ref_cond=" and a.grouping LIKE '".trim($txt_ref_no)."%'";
	
	if(str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.receive_date <=".$txt_date_from."";
	
	$poArr=array(); $poIds=''; $tot_rows=0; $fileRefArr=array();
	$sql="select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst and b.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $po_cond $file_cond $ref_cond order by a.id";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		$tot_rows++;
		$ref_file=$row[csf('buyer_name')]."_".$row[csf('job_no')]."_".$row[csf('grouping')]."_".$row[csf('file_no')];
		$poIds.=$row[csf('id')].",";
		$poArr[$row[csf('id')]]=$ref_file;
		
		$fileRefArr[$ref_file].=$row[csf('id')].",";
	}
	unset($result);	

	$poIds=chop($poIds,','); $poIds_cond=""; $poIds_cond_roll=""; $poIds_cond_delv="";
	if($db_type==2 && $tot_rows>1000)
	{
		$poIds_cond_pre=" and ("; $poIds_cond_suff.=")";
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$poIds_cond.=" b.po_break_down_id in($ids) or ";
			$poIds_cond_roll.=" c.po_breakdown_id in($ids) or ";
			$poIds_cond_delv.=" order_id in($ids) or ";
		}
		
		$poIds_cond=$poIds_cond_pre.chop($poIds_cond,'or ').$poIds_cond_suff;
		$poIds_cond_roll=$poIds_cond_pre.chop($poIds_cond_roll,'or ').$poIds_cond_suff;
		$poIds_cond_delv=$poIds_cond_pre.chop($poIds_cond_delv,'or ').$poIds_cond_suff;
	}
	else
	{
		$poIds_cond=" and b.po_break_down_id in($poIds)";
		$poIds_cond_roll=" and c.po_breakdown_id in($poIds)";
		$poIds_cond_delv=" and order_id in($poIds)";
	}
	
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	
	$grey_qnty_array=return_library_array( "select b.po_break_down_id as po_id, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $poIds_cond group by b.po_break_down_id", "po_id", "grey_req_qnty");
	
	$delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 $poIds_cond_delv", "barcode_num", "grey_sys_id");
	$iss_qty_arr=return_library_array("select c.barcode_no, c.qnty from pro_roll_details c where c.entry_form=61 and c.status_active=1 and c.is_deleted=0 $poIds_cond_roll", "barcode_no","qnty");
	
	$plan_arr=array();
	$plan_data=sql_select("select id, machine_dia, machine_gg from ppl_planning_info_entry_dtls");
	foreach($plan_data as $row)
	{
		$plan_arr[$row[csf('id')]]=$row[csf('machine_dia')]."X".$row[csf('machine_gg')];
	}
	unset($plan_data);	
	
	$recvDataArr=array();
	$sqlRecv="select id, receive_basis, booking_id, knitting_source, knitting_company FROM inv_receive_master WHERE entry_form=2 and receive_basis=2";
	$recvData=sql_select($sqlRecv);
	foreach($recvData as $row)
	{
		$recvDataArr[$row[csf('id')]]=$row[csf('receive_basis')]."__".$row[csf('booking_id')];
	}
	unset($recvData);
	
	$recvDtlsDataArr=array();
	$query="select a.id, a.entry_form, a.receive_basis, a.booking_id, a.knitting_source, a.knitting_company, b.febric_description_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, c.barcode_no, c.po_breakdown_id, c.qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 $trans_date $poIds_cond_roll";
	$data_array=sql_select($query);
	foreach($data_array as $row)
	{
		$machine_dia_gg='';
		if($row[csf("entry_form")]==58) 
		{
			$production_id=$delv_arr[$row[csf('barcode_no')]];
			$recv_data=explode("__",$recvDataArr[$production_id]);
			$receive_basis=$recv_data[0];
			$booking_id=$recv_data[1];
			
			if($receive_basis==2) 
			{
				$machine_dia_gg=$plan_arr[$booking_id];
			}
		}
		else if($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==2) 
		{
			$machine_dia_gg=$plan_arr[$row[csf("booking_id")]];
		}
		
		$knitting_company='';
		if($row[csf('knitting_source')]==1)
		{
			$knitting_company=$company_short_arr[$row[csf('knitting_company')]];
		}
		else if($row[csf('knitting_source')]==3)
		{
			$knitting_company=$supplier_arr[$row[csf('knitting_company')]];
		}
		
		$ref_file=$poArr[$row[csf('po_breakdown_id')]];

		if($row[csf('brand_id')]=="") $row[csf('brand_id')]=0;
		if($row[csf('width')]=="") $row[csf('width')]=0;
		
		$data=$row[csf('febric_description_id')]."**".$row[csf('yarn_count')]."**".$row[csf('brand_id')]."**".$row[csf('yarn_lot')]."**".$row[csf('width')]."**".$row[csf('stitch_length')]."**".$row[csf('gsm')]."**".$row[csf('machine_no_id')]."**".$knitting_company."**".$machine_dia_gg;

		$iss_qnty=$iss_qty_arr[$row[csf('barcode_no')]];
		
		$recvDtlsDataArr[$ref_file][$data]['recv']+=$row[csf("qnty")];
		$recvDtlsDataArr[$ref_file][$data]['iss']+=$iss_qnty;
		
		if($row[csf('color_range_id')]>0)
		{
			$recvDtlsDataArr[$ref_file][$data]['range'].=$row[csf('color_range_id')].",";
		}
		
		if($row[csf('color_id')]>0)
		{
			$recvDtlsDataArr[$ref_file][$data]['color'].=$row[csf('color_id')].",";
		}
		
		$recvDtlsDataArr[$ref_file][$data]['barcode_no'].=$row[csf('barcode_no')].",";
	}
	unset($data_array);	
	//print_r($recvDtlsDataArr); 
	ob_start();
	?>
	<fieldset style="width:2020px">
		<table cellpadding="0" cellspacing="0" width="2020">
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="24" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="24" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="24" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
			</tr>
		</table>
		<table width="2020" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
			<thead>
                <th width="40">SL</th>
                <th width="100">Construction</th>
                <th width="105">Color</th>
                <th width="80">Color Range</th>
                <th width="80">Y-Count</th>
                <th width="80">Yarn Type</th>
                <th width="120">Yarn Composition</th>
                <th width="70">Brand</th>
                <th width="80">Yarn Lot</th>
                <th width="70">MC Dia and Gauge</th>
                <th width="60">F/Dia</th>
                <th width="60">S. Length</th>
                <th width="70">GSM</th>
                <th width="70">M/C NO.</th>
                <th width="70">Knitting Company</th>
                <th width="90">Receive Qty.</th>
                <th width="90">Issue Return Qty.</th>
                <th width="90">Transf. In Qty.</th>
                <th width="90">Total Receive</th>
                <th width="90">Issue Qty.</th>
                <th width="90">Receive Return Qty.</th>
                <th width="90">Transf. Out Qty.</th>
                <th width="90">Total Issue</th>
                <th>Stock Qty.</th>
			</thead>
		</table>
		<div style="width:2020px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="2000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left"> 
			<? 
				$i=1; $tot_recv_qty=0; $tot_iss_qty=0; $tot_trans_in_qty=0; $tot_trans_out_qty=0; $grand_tot_recv_qty=0; $grand_tot_iss_qty=0; $grand_stock_qty=0;
				foreach($fileRefArr as $fileRefArrData=>$poIds)
				{
					$fileRefData=explode("_",$fileRefArrData);
					$buyer_id=$fileRefData[0];
					$job_no=$fileRefData[1];
					$refNo=$fileRefData[2];
					$fileNo=$fileRefData[3];
					
					$grey_qnty=0;
					$poIds=chop($poIds,",");
					$poIdsArr=explode(",",$poIds);
					foreach($poIdsArr as $po_id)
					{
						$grey_qnty+=$grey_qnty_array[$po_id];
					}
					
					?>
					<tr><td colspan="24" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php echo "Buyer: ".$buyer_arr[$buyer_id]."; Job No: ".$job_no."; File No: ".$fileNo."; Ref. No: ".$refNo.";" ?>&nbsp;&nbsp;<a href="##" onClick="openpage_fabric_booking('fabric_booking_popup','<? echo $poIds; ?>')"><? echo "Grey Fabric Qty(Kg): ".number_format($grey_qnty,2); ?></a></b></td></tr>
					<?
					$ref_recv_qty=0; $ref_iss_ret_qty=0; $ref_iss_qty=0; $ref_rec_ret_qty=0; $ref_trans_in_qty=0; $ref_trans_out_qty=0; 
					$ref_tot_recv_qnty=0; $ref_tot_iss_qnty=0; $ref_stock_qnty=0;
					foreach($recvDtlsDataArr[$fileRefArrData] as $data=>$value)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$datas=explode("**",$data);
						$febric_description_id=$datas[0];
						$brand_name=$brand_arr[$datas[2]];
						$yarn_lot=$datas[3];
						$width=$datas[4];
						$stitch_length=$datas[5];
						$gsm=$datas[6];
						$machine_no=$machine_arr[$datas[7]];
						$knitting_company=$datas[8];
						$machine_dia_gg=$datas[9];
						
						$yarn_count='';
						$yarn_count_id=array_unique(explode(",",$datas[1]));
						foreach($yarn_count_id as $count_id)
						{
							if($count_id>0) $yarn_count.=$count_arr[$count_id].',';
						}
						$yarn_count=chop($yarn_count,",");
						
						$constuction=$constuction_arr[$febric_description_id];
						$composition=$composition_arr[$febric_description_id];
						$yarn_type_name=implode(",",array_unique(explode(",",chop($type_array[$febric_description_id],','))));
						
						$recv_qty=$value['recv'];
						$iss_qty=$value['iss'];
						
						$recv_tot_qty=$recv_qty;
						$iss_tot_qty=$iss_qty;
						$stock_qty=$recv_tot_qty-$iss_tot_qty;
						
						$colorRange='';
						$colorRangeIds=array_unique(explode(",",$value['range']));
						foreach($colorRangeIds as $range_id)
						{
							if($range_id>0) $colorRange.=$color_range[$range_id].',';
						}
						$colorRange=chop($colorRange,",");
						
						$color='';
						$colorIds=array_unique(explode(",",$value['color']));
						foreach($colorIds as $color_id)
						{
							if($color_id>0) $color.=$color_arr[$color_id].',';
						}
						$color=chop($color,",");
						
						$barcode_nos=chop($value['barcode_no'],",");
						
						$dataP=$fileNo."_".$refNo."_".$constuction."_".$colorRange."_".$yarn_count."_".$yarn_type_name."_".$composition."_".$brand_name."_".$yarn_lot."_".$machine_dia_gg."_".$width."_".$stitch_length."_".$gsm."_".$machine_no."_".$knitting_company."_".$stock_qty."_".$barcode_nos;
						
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td width="40"><? echo $i; ?></td>
							<td width="100"><p><? echo $constuction; ?>&nbsp;</p></td>
							<td width="105"><p><? echo $color; ?>&nbsp;</p></td>
                            <td width="80"><p><? echo $colorRange; ?>&nbsp;</p></td>
							<td width="80"><p><? echo $yarn_count; ?>&nbsp;</p></td>
							<td width="80"><p><? echo $yarn_type_name; ?>&nbsp;</p></td>
							<td width="120"><p><? echo $composition; ?>&nbsp;</p></td>
                            <td width="70"><p><? echo $brand_name; ?>&nbsp;</p></td>
							<td width="80"><p><? echo $yarn_lot; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $machine_dia_gg; ?>&nbsp;</p></td>
							<td width="60"><p><? echo $width; ?>&nbsp;</p></td>
                            <td width="60"><p><? echo $stitch_length; ?>&nbsp;</p></td>
                            <td width="70"><p><? echo $gsm; ?>&nbsp;</p></td>
                            <td width="70"><p><? echo $machine_no; ?>&nbsp;</p></td>
                            <td width="70"><p><? echo $knitting_company; ?>&nbsp;</p></td>
							<td width="90" align="right"><a href="##" onClick="openpage('recv_popup','<? echo $dataP; ?>')"><? echo number_format($recv_qty,2); ?></a></td>
							<td width="90" align="right"><? echo number_format($iss_ret_qty,2); ?></td>
							<td width="90" align="right"><? echo number_format($trans_in_qty,2); ?></td>
							<td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>
							<td width="90" align="right"><a href="##" onClick="openpage('iss_popup','<? echo $dataP; ?>')"><? echo number_format($iss_qty,2); ?></a></td>
							<td width="90" align="right"><? echo number_format($recv_ret_qty,2); ?></p></td>
							<td width="90" align="right"><? echo number_format($trans_out_qty,2); ?></td>
							<td width="90" align="right"><? echo number_format($iss_tot_qty,2); ?></td>
							<td align="right"><a href="##" onClick="openpage('stock_popup','<? echo $dataP; ?>')"><? echo number_format($stock_qty,2); ?></a></td>
						</tr>
					<?	
						$i++;
						
						$ref_recv_qty+=$recv_qty;
						$ref_iss_qty+=$iss_qty;
						$ref_stock_qnty+=$stock_qty;
						$ref_tot_recv_qnty+=$recv_qty;
						$ref_tot_iss_qnty+=$iss_qty;
						
						$tot_recv_qty+=$recv_tot_qty; 
						$tot_iss_qty+=$iss_tot_qty; 
						
						$grand_tot_recv_qty+=$recv_qty; 
						$grand_tot_iss_qty+=$iss_qty;
						$grand_stock_qty+=$stock_qty;
					}
					?>
                    
					<tr class="tbl_bottom">
						<td colspan="15" align="right"><b>Total</b></td>
						<td align="right"><? echo number_format($ref_recv_qty,2,'.',''); ?></td>
						<td align="right"><? echo number_format($ref_iss_ret_qty,2,'.',''); ?></td>
						<td align="right"><? echo number_format($ref_trans_in_qty,2,'.',''); ?></td>
						<td align="right"><? echo number_format($ref_tot_recv_qnty,2,'.',''); ?></td>
						<td align="right"><? echo number_format($ref_iss_qty,2,'.',''); ?></td>
						<td align="right"><? echo number_format($ref_rec_ret_qty,2,'.',''); ?></td>
						<td align="right"><? echo number_format($ref_trans_out_qty,2,'.',''); ?></td>
						<td align="right"><? echo number_format($ref_tot_iss_qnty,2,'.',''); ?></td>
						<td align="right"><? echo number_format($ref_stock_qnty,2,'.',''); ?></td>
					</tr>
				<?
				}
				?>
				<tfoot>
					<tr>
						<th colspan="15" align="right"><b>Grand Total</b></th>
						<th align="right"><? echo number_format($tot_recv_qty,2,'.',''); ?></th>
						<th align="right"><? echo number_format($tot_iss_ret_qty,2,'.',''); ?></th>
						<th align="right"><? echo number_format($tot_trans_in_qty,2,'.',''); ?></th>
						<th align="right"><? echo number_format($grand_tot_recv_qty,2,'.',''); ?></th>
						<th align="right"><? echo number_format($tot_iss_qty,2,'.',''); ?></th>
						<th align="right"><? echo number_format($tot_rec_ret_qty,2,'.',''); ?></th>
						<th align="right"><? echo number_format($tot_trans_out_qty,2,'.',''); ?></th>
						<th align="right"><? echo number_format($grand_tot_iss_qty,2,'.',''); ?></th>
						<th align="right"><? echo number_format($grand_stock_qty,2,'.',''); ?></th>
					</tr>
				</tfoot>
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
    echo "$html####$filename"; 
    exit();
}

if($action=="fabric_booking_popup")
{
 	echo load_html_head_contents("Fabric Booking Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
?>
	<fieldset style="width:890px">
        <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
            <thead>
                <th width="40">SL</th>
                <th width="60">Booking No</th>
                <th width="50">Year</th>
                <th width="60">Type</th>
                <th width="80">Booking Date</th>
                <th width="90">Color</th>
                <th width="110">Fabric</th>
                <th width="150">Composition</th>
                <th width="70">GSM</th>
                <th width="70">Dia</th>
                <th>Grey Req. Qty.</th>
            </thead>
        </table>
        <div style="width:100%; max-height:320px; overflow-y:scroll">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
			<?
                if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
                else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
                else $year_field="";//defined Later
				
				$i=1; $tot_grey_qnty=0;
                $sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_type, a.is_short, a.booking_date, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width, sum(b.grey_fab_qnty) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 group by a.id, a.booking_type, a.is_short, a.booking_date, a.insert_date, a.booking_no_prefix_num, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width order by a.id";
               //echo $sql;//die;
                $result= sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					
					if($row[csf('booking_type')]==4) 
					{
						$booking_type="Sample";
					}
					else
					{
						if($row[csf('is_short')]==1) $booking_type="Short"; else $booking_type="Main"; 
					}
               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="60">&nbsp;&nbsp;&nbsp;<? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
                        <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                        <td width="60" align="center"><p><? echo $booking_type; ?></p></td>
                        <td width="80" align="center"><? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>
                        <td width="90"><p><? echo $color_arr[$row[csf('fabric_color_id')]]; ?>&nbsp;</p></td>
                        <td width="110"><p><? echo $row[csf('construction')]; ?>&nbsp;</p></td>
                        <td width="150"><p><? echo $row[csf('copmposition')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $row[csf('gsm_weight')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $row[csf('dia_width')]; ?>&nbsp;</p></td>
                        <td align="right" style="padding-right:5px"><? echo number_format($row[csf('grey_fab_qnty')],2); ?></td>
                    </tr>
                <? 
					$tot_grey_qnty+=$row[csf('grey_fab_qnty')];
					$i++;
                } 
            ?>
            	<tfoot>
                	<th colspan="10">Total</th>
                    <th style="padding-right:5px"><? echo number_format($tot_grey_qnty,2); ?></th>
                </tfoot>
			</table>
		</div> 
    </fieldset>
<?
exit();
}

if($action=="recv_popup")
{
 	echo load_html_head_contents("Receive Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$data=explode("_",$data);
	
	$barcode_nos=$data[16];
?>
	<script>
		var tableFilters = {
						   col_operation: {
						   id: ["value_grey_qty"],
						   col: [4],
						   operation: ["sum"],
						   write_method: ["innerHTML"]
						}
					}
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1,tableFilters);
		});
	</script>
	<fieldset style="width:1190px">
    	<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">            	
			<thead>
                <th width="70">File No.</th>
                <th width="70">Ref. No.</th>
                <th width="80">Construction</th>
                <th width="80">Color Range</th>
                <th width="70">Y-Count</th>
                <th width="80">Yarn Type</th>
                <th width="120">Yarn Composition</th>
                <th width="70">Brand</th>
                <th width="70">Yarn Lot</th>
                <th width="70">MC Dia & Gauge</th>
                <th width="60">F/Dia</th>
                <th width="60">S. Length</th>
                <th width="60">GSM</th>
                <th width="60">M/C NO.</th>
                <th width="60">Knitting Company</th>
                <th>Stock Qty.</th>
			</thead>
            <tr bgcolor="#FFFFFF">
            	<td width="70"><p><? echo $data[0]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[1]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $data[2]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $data[3]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[4]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $data[5]; ?>&nbsp;</p></td>
                <td width="120"><p><? echo $data[6]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[7]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[8]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[9]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[10]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[11]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[12]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[13]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[14]; ?>&nbsp;</p></td>
                <td align="right"><p><? echo $data[15]; ?>&nbsp;</p></td>
            </tr>
		</table>
        
        <table cellpadding="0" width="500" class="rpt_table" rules="all" border="1">
            <thead>
                <th width="40">SL</th>
                <th width="120">Store Name</th>
                <th width="100">Bacode No</th>
                <th width="80">Roll No</th>
                <th>Roll Weight</th>
            </thead>
        </table>
        <div style="width:500px; max-height:250px; overflow-y:scroll">
            <table cellpadding="0" width="480" class="rpt_table" rules="all" border="1" id="tbl_list_search">
			<?
				$i=0; $tot_grey_qnty=0;
                $sql="select s.store_name, c.barcode_no, c.roll_no, c.qnty from inv_receive_master a, lib_store_location s,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.store_id=s.id and a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) order by s.store_name, c.barcode_no";
                //echo $sql;//die;
                $result= sql_select($sql);
                foreach($result as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="120"><p><? echo $row[csf('store_name')]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
                        <td width="80" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
                    </tr>
                <? 
					$tot_grey_qnty+=$row[csf('qnty')];
                } 
            ?>
            </table>
		</div> 
        <table cellpadding="0" width="480" class="rpt_table" rules="all" border="1">
            <tfoot>
                <th colspan="3">Roll Total :</th>
                <th width="80" style="text-align:center"><? echo $i; ?></th>
                <th width="134" id="value_grey_qty"><? echo number_format($tot_grey_qnty,2); ?></th>
            </tfoot>
        </table>
    </fieldset>
<?
exit();
}

if($action=="stock_popup")
{
 	echo load_html_head_contents("Stock Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$data=explode("_",$data);
	
	$barcode_nos=$data[16];
	$iss_qty_arr=return_library_array("select c.barcode_no, c.qnty from pro_roll_details c where c.entry_form=61 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)", "barcode_no","qnty");
?>
	<script>
		var tableFilters = {
						   col_operation: {
						   id: ["value_grey_qty"],
						   col: [4],
						   operation: ["sum"],
						   write_method: ["innerHTML"]
						}
					}
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1,tableFilters);
		});
	</script>
	<fieldset style="width:1190px">
    	<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">            	
			<thead>
                <th width="70">File No.</th>
                <th width="70">Ref. No.</th>
                <th width="80">Construction</th>
                <th width="80">Color Range</th>
                <th width="70">Y-Count</th>
                <th width="80">Yarn Type</th>
                <th width="120">Yarn Composition</th>
                <th width="70">Brand</th>
                <th width="70">Yarn Lot</th>
                <th width="70">MC Dia & Gauge</th>
                <th width="60">F/Dia</th>
                <th width="60">S. Length</th>
                <th width="60">GSM</th>
                <th width="60">M/C NO.</th>
                <th width="60">Knitting Company</th>
                <th>Stock Qty.</th>
			</thead>
            <tr bgcolor="#FFFFFF">
            	<td width="70"><p><? echo $data[0]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[1]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $data[2]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $data[3]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[4]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $data[5]; ?>&nbsp;</p></td>
                <td width="120"><p><? echo $data[6]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[7]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[8]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[9]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[10]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[11]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[12]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[13]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[14]; ?>&nbsp;</p></td>
                <td align="right"><p><? echo $data[15]; ?>&nbsp;</p></td>
            </tr>
		</table>
        
        <table cellpadding="0" width="500" class="rpt_table" rules="all" border="1">
            <thead>
                <th width="40">SL</th>
                <th width="120">Store Name</th>
                <th width="100">Bacode No</th>
                <th width="80">Roll No</th>
                <th>Roll Weight</th>
            </thead>
        </table>
        <div style="width:500px; max-height:250px; overflow-y:scroll">
            <table cellpadding="0" width="480" class="rpt_table" rules="all" border="1" id="tbl_list_search">
			<?
				$i=0; $tot_stock_qnty=0;
                $sql="select s.store_name, c.barcode_no, c.roll_no, c.qnty from inv_receive_master a, lib_store_location s,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.store_id=s.id and a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) order by s.store_name, c.barcode_no";
                //echo $sql;//die;
                $result= sql_select($sql);
                foreach($result as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					
					$stock_qty=$row[csf('qnty')]-$iss_qty_arr[$row[csf('barcode_no')]];
               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="120"><p><? echo $row[csf('store_name')]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
                        <td width="80" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($stock_qty,2); ?></td>
                    </tr>
                <? 
					$tot_stock_qnty+=$stock_qty;
                } 
            ?>
            </table>
		</div> 
        <table cellpadding="0" width="480" class="rpt_table" rules="all" border="1">
            <tfoot>
                <th colspan="3">Roll Total :</th>
                <th width="80" style="text-align:center"><? echo $i; ?></th>
                <th width="134" id="value_grey_qty"><? echo number_format($tot_stock_qnty,2); ?></th>
            </tfoot>
        </table>
	</fieldset>
<?
exit();
}

if($action=="iss_popup")
{
 	echo load_html_head_contents("Stock Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$data=explode("_",$data);
	
	$barcode_nos=$data[16];
	$iss_qty_arr=return_library_array("select c.barcode_no, c.qnty from pro_roll_details c where c.entry_form=61 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)", "barcode_no","qnty");
?>
	<script>
		var tableFilters = {
						   col_operation: {
						   id: ["value_grey_qty"],
						   col: [4],
						   operation: ["sum"],
						   write_method: ["innerHTML"]
						}
					}
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1,tableFilters);
		});
	</script>
	<fieldset style="width:1190px">
    	<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" style="margin-bottom:10px">            	
			<thead>
                <th width="70">File No.</th>
                <th width="70">Ref. No.</th>
                <th width="80">Construction</th>
                <th width="80">Color Range</th>
                <th width="70">Y-Count</th>
                <th width="80">Yarn Type</th>
                <th width="120">Yarn Composition</th>
                <th width="70">Brand</th>
                <th width="70">Yarn Lot</th>
                <th width="70">MC Dia & Gauge</th>
                <th width="60">F/Dia</th>
                <th width="60">S. Length</th>
                <th width="60">GSM</th>
                <th width="60">M/C NO.</th>
                <th width="60">Knitting Company</th>
                <th>Stock Qty.</th>
			</thead>
            <tr bgcolor="#FFFFFF">
            	<td width="70"><p><? echo $data[0]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[1]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $data[2]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $data[3]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[4]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $data[5]; ?>&nbsp;</p></td>
                <td width="120"><p><? echo $data[6]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[7]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[8]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $data[9]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[10]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[11]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[12]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[13]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $data[14]; ?>&nbsp;</p></td>
                <td align="right"><p><? echo $data[15]; ?>&nbsp;</p></td>
            </tr>
		</table>
        
        <table cellpadding="0" width="500" class="rpt_table" rules="all" border="1">
            <thead>
                <th width="40">SL</th>
                <th width="90">Issue Id</th>
                <th width="120">Issue Purpose </th>
                <th width="80">Total Roll</th>
                <th>Roll Weight</th>
            </thead>
        </table>
        <div style="width:500px; max-height:250px; overflow-y:scroll">
            <table cellpadding="0" width="480" class="rpt_table" rules="all" border="1" id="tbl_list_search">
			<?
				$i=0; $tot_iss_qnty=0; $tot_roll=0;
                $sql="select a.id, a.issue_number_prefix_num, a.issue_purpose, count(1) as tot_roll, sum(c.qnty) as qnty from inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=61 and c.entry_form=61 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) group by a.id, a.issue_number_prefix_num, a.issue_purpose order by a.id";
                //echo $sql;//die;
                $result= sql_select($sql);
                foreach($result as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					
               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="90" align="center"><p><? echo $row[csf('issue_number_prefix_num')]; ?>&nbsp;</p></td>
                        <td width="120"><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?>&nbsp;</p></td>
                        <td width="80" align="center"><p><? echo $row[csf('tot_roll')]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
                    </tr>
                <? 
					$tot_iss_qnty+=$row[csf('qnty')];
					$tot_roll+=$row[csf('tot_roll')];
                } 
            ?>
            </table>
		</div>
        <table cellpadding="0" width="480" class="rpt_table" rules="all" border="1">
            <tfoot>
                <th colspan="3">Roll Total :</th>
                <th width="80" style="text-align:center"><? echo $tot_roll; ?></th>
                <th width="144" id="value_grey_qty"><? echo number_format($tot_iss_qnty,2); ?></th>
            </tfoot>
        </table> 
    </fieldset>
<?
exit();
}

?>

