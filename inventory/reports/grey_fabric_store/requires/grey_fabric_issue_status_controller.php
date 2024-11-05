<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action == "load_drop_down_knitting_com") 
{
	$data = explode("_", $data);
	$company_id = $data[1];

	if ($data[0] == 1) 
    {
		echo create_drop_down("cbo_working_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", "$company_id", "", "");
	} 
    else if ($data[0] == 3) 
    {
		echo create_drop_down("cbo_working_company_id", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(20,21) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select Company --", 0, "");
	} 
    else 
    {
		echo create_drop_down("cbo_working_company_id", 150, $blank_array, "", 1, "-- Select Knitting Company --", 1, "");
	}
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0);
	exit();
}

if ($action=="job_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	$year_id=$data[2];
?>
    <script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

	function toggle( x, origColor ) {
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
		$('#job_no_id').val( id );
		$('#job_no_val').val( ddd );
	}

	</script>
     <input type="hidden" id="job_no_id" />
     <input type="hidden" id="job_no_val" />
 <?
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and buyer_name=$data[1]";
	if($db_type==0)
	{
		$year_field_by="and YEAR(insert_date)";
		$year_cond= "year(insert_date) as year";
	}
	else if($db_type==2)
	{
		$year_field_by=" and to_char(insert_date,'YYYY')";
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
	if(trim($year_id!=0)) $year_cond_search=" $year_field_by=$year_id"; else $year_cond_search="";

	$sql="select id, job_no_prefix_num, job_no, $year_cond, buyer_name, style_ref_no, style_description from wo_po_details_master where company_name=$data[0] and is_deleted=0 $buyer_name  $year_cond_search ORDER BY job_no DESC";
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array(2=>$buyer);

	echo  create_list_view("list_view", "Job No,Year,Buyer,Style Ref.,Style Desc.", "70,70,110,150,180","620","350",0, $sql, "js_set_value", "id,job_no_prefix_num", "", 1, "0,0,buyer_name,0,0,0", $arr , "job_no_prefix_num,year,buyer_name,style_ref_no,style_description", "grey_fabric_issue_status_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
	disconnect($con);
	exit();
}

if ($action=="batch_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
?>
    <script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

	function toggle( x, origColor ) {
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
		$('#batch_no_id').val( id );
		$('#batch_no_val').val( ddd );
	}

	</script>
     <input type="hidden" id="batch_no_id" />
     <input type="hidden" id="batch_no_val" />
 <?
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and buyer_name=$data[1]";
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$sql="select id, batch_no, extention_no, batch_for, booking_no, color_id, batch_weight from pro_batch_create_mst where entry_form=0 and company_id=$data[0] and is_deleted=0 ";//entry_form=16 and
	$arr=array(2=>$color_library);

	echo  create_list_view("list_view", "Batch No,Batch Ext.,Color,Booking No, Batch For,Batch Weight ", "100,60,100,130,100,80","640","350",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,0,color_id,0,0,0", $arr , "batch_no,extention_no,color_id,booking_no,batch_for,batch_weight", "employee_info_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,2','',1) ;
	disconnect($con);
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
	$cbo_working_company_id=str_replace("'","",$cbo_working_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$txt_int_ref=str_replace("'","",$txt_int_ref);
	$job_no=trim(str_replace("'","",$txt_job_no));
	$job_no_id=str_replace("'","",$txt_job_no_id);
	$batch_no_id=trim(str_replace("'","",$txt_batch_no_id));
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$cbo_year= trim(str_replace("'","",$cbo_year));
	$issue_purpose= trim(str_replace("'","",$cbo_issue_purpose));

	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and g.job_no_prefix_num in ($job_no) ";
	if ($txt_int_ref=="") $int_ref_cond=""; else $int_ref_cond=" and f.grouping in ($txt_int_ref) ";

	if ($cbo_working_company_id)
	{
		$cbo_working_company_cond=" and a.knit_dye_company=$cbo_working_company_id";
	}
	if ($cbo_company){ $cbo_company_cond=" and a.company_id=$cbo_company";}
	if ($cbo_knitting_source){ $knitting_source_cond=" and a.knit_dye_source=$cbo_knitting_source";}

	if ($cbo_buyer==0) $buyer_id=""; else $buyer_id=" and g.buyer_name=$cbo_buyer";
	if ($batch_no_id=="") $batch_no=""; else $batch_no=" and a.batch_no in ( $batch_no_id )";
	if( $date_from=="" && $date_to=="" )
	{
		$issue_date="";
	}
	else
	{
		if($db_type==0)
		{
			$issue_date= " and a.issue_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			$issue_date= " and a.issue_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}

	if($cbo_buyer==0)
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
		$buyer_id_cond=" and a.buyer_name in (".str_replace("'","",$cbo_buyer).")";
	}
	if ($issue_purpose==0) $issuePurposeCond=""; else $issuePurposeCond=" and a.issue_purpose=$issue_purpose";

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );

	$countArr=return_library_array( "select id,yarn_count from  lib_yarn_count", "id", "yarn_count"  );
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$batch_arr = return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
	$color_arr = get_color_array();
	ob_start();
	?>
	 <style>
        .wrd_brk{word-break: break-all;word-wrap: break-word;}
    </style>
    <div>
    <table width="1690" cellpadding="0" cellspacing="0" id="caption" align="center">
        <tr>
           <td align="center" width="100%" colspan="16" class="form_caption">
           	<strong style="font-size:18px">
           		<?
           		if ($cbo_company==0)
           		{
           			echo "Working Company Name: ".$company_library[$cbo_working_company_id];
           		}
           		else
           		{
           			echo " Company Name: ". $company_library[$cbo_company];
           		}
           		?>
           	</strong>
           </td>
        </tr>
        <tr>
           <td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
        </tr>
        <tr>
           <td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
        </tr>
    </table>
    <table width="1790" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
        <thead>
            <tr>
                <th width="35">SL</th>
                <th width="60">Prod. Id</th>
                <th width="80">Issue Date</th>
                <th width="100">Trnasection No</th>
                <th width="120">Body Part</th>
                <th width="120">Construction</th>
                <th width="150">Composition</th>
                <th width="80">Dia/Width </th>
                <th width="80">GSM</th>
                <th width="100">Color</th>
                <th width="100">Purpose</th>
                <th width="80">Issue Qty.</th>
                <th width="80">Yarn Lot</th>
                <th width="80">Yrn. Count</th>
                <th width="100">Order No</th>
                <th width="120">Buyer</th>
                <th width="50">Job Year</th>
                <th width="70">Job No</th>
                <th width="70">Batch No</th>
                <th>Company</th>
            </tr>
        </thead>
    </table>
    <div style="width:1810px; max-height:300px; overflow-y:scroll; float: left;" id="scroll_body" >
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1790" class="rpt_table" id="tbl_issue_status" >
	<?
	if($db_type==0)
	{
		$select_job_year=" YEAR(g.insert_date) as job_year";
		$select_yarn_lot = " b.yarn_lot as yarn_lot";
		if($cbo_year) { $year_cond=" and YEAR(g.insert_date)=$cbo_year";}
	}
	else if($db_type==2)
	{
		$select_job_year=" to_char(g.insert_date,'YYYY') as job_year";
		$select_yarn_lot = " CAST(b.yarn_lot as varchar2(500)) as yarn_lot";
		if($cbo_year) { $year_cond=" and to_char(g.insert_date,'YYYY')=$cbo_year";}
	}

	$body_part_array=array();
	$job_sql=sql_select("select job_no, lib_yarn_count_deter_id as deter_id, body_part_id from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0 ");
	foreach( $job_sql as $job_row )
	{
		$body_part_array[$job_row[csf('job_no')]][$job_row[csf('deter_id')]]['body']=$job_row[csf('body_part_id')];
	}

	$dtls_sql="SELECT a.id, a.issue_number, a.issue_purpose, a.company_id, a.knit_dye_source, a.knit_dye_company, g.buyer_name, a.booking_no, a.batch_no, e.po_breakdown_id as order_id, f.po_number, a.issue_basis, b.prod_id,a.issue_date, e.quantity as issue_qnty, CAST(b.yarn_lot as varchar2(500)) as yarn_lot, b.yarn_count, d.gsm, d.dia_width, d.detarmination_id, $select_job_year, g.job_no_prefix_num, g.job_no, b.color_id
	from inv_issue_master a, inv_grey_fabric_issue_dtls b, product_details_master d, order_wise_pro_details e, wo_po_break_down f, wo_po_details_master g
	where a.id=b.mst_id and b.prod_id=d.id and e.trans_id=b.trans_id and e.prod_id=d.id and e.po_breakdown_id= f.id and f.job_no_mst= g.job_no and a.entry_form in(16,61) and a.item_category=13 $cbo_company_cond $knitting_source_cond $cbo_working_company_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $job_no_cond $int_ref_cond $year_cond $issue_date $issuePurposeCond order by a.id";
	//echo $dtls_sql;die;
	$dtls_sql_result=sql_select($dtls_sql);

	$composition_arr=array();
    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ";
    $data_deter=sql_select($sql_deter);

    if(count($data_deter)>0)
    {
    	foreach( $data_deter as $row )
    	{
    		if(array_key_exists($row[csf('id')],$composition_arr))
    		{
    			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
    			$constructionArr[$row[csf('id')]]=$constructionArr[$row[csf('id')]];
    			list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
    			$copmpositionArr[$row[csf('id')]]=$cps;
    		}
    		else
    		{
    			$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
    			$constructionArr[$row[csf('id')]]=$row[csf('construction')];
    			list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
    			$copmpositionArr[$row[csf('id')]]=$cps;
    		}
    	}
    }
    unset($data_deter);

	$i=1;
	foreach ( $dtls_sql_result as $row )
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$body_part_id=$body_part_array[$row[csf('job_no')]][$row[csf("detarmination_id")]]['body'];

		$color = '';
		$colorArr = explode(",", $row[csf('color_id')]);
		foreach ($colorArr as $val)
		{
			if($color=="")
			{
				$color .= $color_arr[$val];
			}else {
				$color .= ",".$color_arr[$val];
			}
		}

		if ($row[csf("knit_dye_source")]==1) 
		{
			$dye_company=$company_library[$row[csf('knit_dye_company')]];
		}
		else
		{
			$dye_company=$supplier_library[$row[csf('knit_dye_company')]];
		}

		?>
		<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
			<td width="35"  align="center" class="wrd_brk"><p><? echo $i; ?></p></td>
			<td width="60" class="wrd_brk"><p><? echo $row[csf("prod_id")]; ?></p></td>
			<td width="80" class="wrd_brk"><p><? echo change_date_format($row[csf("issue_date")]); ?></p></td>
			<td width="100" class="wrd_brk"><p><? echo $row[csf("issue_number")]; ?></p></td>
			<td width="120" class="wrd_brk"><p><? echo $body_part[$body_part_id]; ?></p></td>
			<td width="120" class="wrd_brk"><p><? echo $constructionArr[$row[csf("detarmination_id")]]; ?></p></td>
			<td width="150" class="wrd_brk"><p><? echo $composition_arr[$row[csf("detarmination_id")]]; ?></p></td>
			<td width="80" class="wrd_brk"><p><? echo $row[csf("dia_width")]; ?></p></td>
			<td width="80" class="wrd_brk"><p><? echo $row[csf("gsm")]; ?></p></td>
			<td width="100" class="wrd_brk"><p><? echo $color; ?></p></td>
			<td width="100" class="wrd_brk"><p><? echo $yarn_issue_purpose[$row[csf("issue_purpose")]]; ?></p></td>
			<td width="80" align="right" class="wrd_brk"><? echo number_format($row[csf("issue_qnty")],2); ?></td>
			<td width="80" align="center" class="wrd_brk"><p><? echo $row[csf("yarn_lot")]; ?></p></td>
			<td width="80" align="center" class="wrd_brk"><p><? echo $countArr[$row[csf("yarn_count")]]; ?></p></td>
			<td width="100" align="center" class="wrd_brk"><p><? echo $row[csf('po_number')]; ?></p></td>
			<td width="120" class="wrd_brk"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
			<td width="50" align="center" class="wrd_brk"><p><? echo $row[csf('job_year')]; ?></p></td>
			<td width="70" align="center" class="wrd_brk"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
			<td width="70" class="wrd_brk"><p><? echo $batch_arr[$row[csf("batch_no")]]; ?></p></td>
			<td align="center" class="wrd_brk"><p><? echo $dye_company; ?></p></td>
        </tr>
 		<?
		$tot_qnty+=$row[csf("issue_qnty")];
		$i++;
	}
	?>
    </table>
    <table width="1790 " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tfoot>
            <th width="35">&nbsp;</th>
            <th width="60">&nbsp;</th>
            <th width="80">&nbsp;</th>
            <th width="100">&nbsp;</th>
            <th width="120">&nbsp;</th>
            <th width="120">&nbsp;</th>
            <th width="150">&nbsp;</th>
            <th width="80">&nbsp;</th>
            <th width="80">&nbsp;</th>
            <th width="100">&nbsp;</th>
            <th width="100">&nbsp;</th>
            <th width="80" align="right" id="value_tot_qnty">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo number_format($tot_qnty,2); ?></th>
            <th width="80">&nbsp;</th>
            <th width="80">&nbsp;</th>
            <th width="100">&nbsp;</th>
            <th width="120">&nbsp;</th>
            <th width="50">&nbsp;</th>
            <th width="70">&nbsp;</th>
            <th width="70">&nbsp;</th>
            <th width="">&nbsp;</th>
        </tfoot>
    </table>
    </div>
    </div>
    <?
	foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	//echo "$total_data####$filename";
	exit();
}
?>