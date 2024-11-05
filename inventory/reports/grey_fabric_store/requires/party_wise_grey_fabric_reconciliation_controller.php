<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
$buyer_arr=return_library_array( "select id, short_name from  lib_buyer", "id", "short_name");
$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");

/*if($action=="load_drop_down_dyeing_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];

	if($data[0]==1)
	{
		echo create_drop_down( "txt_dyeing_com_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",0, "--Select Party--", "$company_id", "","" );
	}
	else if($data[0]==3)
	{
		//select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type=2
		echo create_drop_down( "txt_dyeing_com_id", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and c.supplier_id=b.supplier_id and c.tag_company=$company_id and b.party_type in(1,9,20) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name",0, "-- Select --", 1, "" );
	}
	else
	{
		echo create_drop_down( "txt_dyeing_com_id", 140, $blank_array,"",1, "--Select Party--", 1, "" );
	}

	exit();
}*/

/*if ($action=="eval_multi_select")
{
 	echo "set_multiselect('txt_dyeing_com_id','0','0','','0');\n";
	$data = explode("_",$data);

	if($data[0]==1)
	{
		echo "set_multiselect('txt_dyeing_com_id','0','1','".$data[1]."','0');\n";
	}
	exit();
}*/

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array; var selected_name = new Array;

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

			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
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
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'party_wise_grey_fabric_reconciliation_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];


	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

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

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no_prefix_num";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="and YEAR(insert_date)";
	else if($db_type==2) $year_field_by=" and to_char(insert_date,'YYYY')";
	else $year_field_by="";
	if($db_type==0) $month_field_by="and month(insert_date)";
	else if($db_type==2) $month_field_by=" and to_char(insert_date,'MM')";
	else $month_field_by="";
	if($db_type==0) $year_field=" YEAR(insert_date) as year";
	else if($db_type==2) $year_field="  to_char(insert_date,'YYYY') as year";
	else $year_field="";

	if($year_id!=0) $year_cond=" $year_field_by=$year_id"; else $year_cond="";
	if($month_id!=0) $month_cond=" $month_field_by=$month_id"; else $month_cond="";


	$arr=array (0=>$company_arr,1=>$buyer_arr);

	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field  from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond $month_cond order by job_no";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;

   exit();
}

if($action=="party_popup")
{
	echo load_html_head_contents("Party Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>

	<script>
		var selected_id = new Array; var selected_name = new Array;

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

			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
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

			$('#hide_party_id').val( id );
			$('#hide_party_name').val( name );
		}
    </script>
        <input type="hidden" name="hide_party_name" id="hide_party_name" value="" />
        <input type="hidden" name="hide_party_id" id="hide_party_id" value="" />
	<?

	if ($cbo_dyeing_source==3)
	{
		$sql="select a.id, a.supplier_name as party_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and c.supplier_id=b.supplier_id and c.tag_company=$companyID and b.party_type in(1,9,20,21,24) and a.status_active=1 group by a.id, a.supplier_name order by a.supplier_name";
	}
	elseif($cbo_dyeing_source==1)
	{
		$sql="select id, company_name as party_name from lib_company comp where id=$companyID and status_active=1 and is_deleted=0 order by company_name";
	}

	echo create_list_view("tbl_list_search", "Party Name", "380","380","270",0, $sql , "js_set_value", "id,party_name", "", 1, "0", $arr , "party_name", "",'setFilterGrid("tbl_list_search",-1);','0','',1) ;

   exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($db_type==0)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'yyyy-mm-dd');
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'yyyy-mm-dd');
	}
	elseif($db_type==2)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'','',1);
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'','',1);
	}
	$dyeing_company=str_replace("'","",$txt_dyeing_com_id);
	$type=str_replace("'","",$type);
	if (str_replace("'","",$cbo_dyeing_source)==0) $dyeing_source_cond=""; else $dyeing_source_cond=" and a.knit_dye_source=$cbo_dyeing_source";
	if (str_replace("'","",$cbo_dyeing_source)==0) $dyeing_source_rec_cond=""; else $dyeing_source_rec_cond=" and a.knitting_source=$cbo_dyeing_source";
	if ($dyeing_company=='') $dyeing_company_cond=""; else  $dyeing_company_cond="  and a.knit_dye_company in ($dyeing_company)";
	ob_start();
	if($type==1)
	{
	?>
        <fieldset style="width:1250px">
            <table width="1240" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
                <tr>
                   <td align="center" width="100%" colspan="11" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="11" style="font-size:16px"><strong><? echo $report_title; ?> (Summary)</strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="11" style="font-size:16px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
                </tr>
            </table>
            <br />
            <table width="1240" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="40">SL</th>
                    <th width="150">Party Name</th>
                    <th width="70">UOM</th>
                    <th width="120">Grey Issued</th>
                    <th width="120">Returnable Qty.</th>
                    <th width="120">Fabric Received</th>
                    <th width="120">Reject Fabric Received</th>
                    <th width="120">Grey Returned</th>
                    <th width="120">Reject Grey Returned</th>
                    <th width="120">Balance</th>
                    <th>Returnable Balance</th>
                </thead>
            </table>
            <div style="width:1240px; overflow-y: scroll; max-height:380px;" id="scroll_body">
                <table width="1220" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<?

                    $qty_arr=array();
					 $query_data=sql_select("select a.knitting_company,
						sum(case when a.entry_form in (2,22) and a.item_category=13 and b.item_category=13 and b.transaction_type=1 then b.cons_quantity end) as grey_receive_qnty,
						sum(case when a.entry_form in (2,22) and a.item_category=13 and b.item_category=13 and b.transaction_type=1 then b.cons_reject_qnty end) as reject_fabric_receive,
						sum(case when a.entry_form=51 and a.item_category=13 and b.item_category=13 and b.transaction_type=4 then b.cons_quantity end) as yarn_return_qnty,
						sum(case when a.entry_form=51 and a.item_category=13 and b.item_category=13 and b.transaction_type=4 then b.cons_reject_qnty end) as yarn_return_reject_qnty
						from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.company_id=$cbo_company_name $dyeing_source_rec_cond and a.receive_date between '$from_date' and '$to_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.knitting_company");
						//$dataArray=sql_select($query);

						foreach($query_data as $row_data)
						{
							$qty_arr[$row_data[csf('knitting_company')]]['grey_receive_qnty']=$row_data[csf('grey_receive_qnty')];
							$qty_arr[$row_data[csf('knitting_company')]]['reject_fabric_receive']=$row_data[csf('reject_fabric_receive')];
							$qty_arr[$row_data[csf('knitting_company')]]['yarn_return_qnty']=$row_data[csf('yarn_return_qnty')];
							$qty_arr[$row_data[csf('knitting_company')]]['yarn_return_reject_qnty']=$row_data[csf('yarn_return_reject_qnty')];
						} //var_dump($qty_arr);
					$i=1;

                  $sql="select a.knit_dye_source, a.knit_dye_company, sum(b.cons_quantity) as issue_qnty, sum(b.return_qnty) as return_qnty, max(b.cons_uom) as cons_uom from inv_issue_master a, inv_transaction b where a.item_category=13 and a.entry_form=16 and a.company_id=$cbo_company_name and a.issue_date between '$from_date' and '$to_date' and a.id=b.mst_id and b.item_category=13 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $dyeing_source_cond $dyeing_company_cond group by a.knit_dye_source, a.knit_dye_company";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if($row[csf('knit_dye_source')]==1)
							$knitting_party=$company_arr[$row[csf('knit_dye_company')]];
						else if($row[csf('knit_dye_source')]==3)
							$knitting_party=$supplier_arr[$row[csf('knit_dye_company')]];
						else
							$knitting_party="&nbsp;";


						$grey_receive_qnty=$qty_arr[$row[csf('knit_dye_company')]]['grey_receive_qnty'];
						$reject_fabric_receive=$qty_arr[$row[csf('knit_dye_company')]]['reject_fabric_receive'];
						$yarn_return_qnty=$qty_arr[$row[csf('knit_dye_company')]]['yarn_return_qnty'];
						$yarn_return_reject_qnty=$qty_arr[$row[csf('knit_dye_company')]]['yarn_return_reject_qnty'];
						$returnable_balance=$row[csf('return_qnty')]-$yarn_return_qnty;

						$balance=$row[csf('issue_qnty')]-($grey_receive_qnty+$reject_fabric_receive+$yarn_return_qnty+$yarn_return_reject_qnty);
                    ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="150"><p><? echo $knitting_party; ?>&nbsp;</p></td>
                            <td width="70" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
                            <td width="120" align="right"><? echo number_format($row[csf('issue_qnty')],2,'.',''); ?>&nbsp;</td>
                            <td width="120" align="right"><? echo number_format($row[csf('return_qnty')],2,'.',''); ?>&nbsp;</td>
                            <td width="120" align="right"><? echo number_format($grey_receive_qnty,2,'.',''); ?>&nbsp;</td>
                            <td width="120" align="right"><? echo number_format($reject_fabric_receive,2,'.',''); ?>&nbsp;</td>
                            <td width="120" align="right"><? echo number_format($yarn_return_qnty,2,'.',''); ?>&nbsp;</td>
                            <td width="120" align="right"><? echo number_format($yarn_return_reject_qnty,2,'.',''); ?>&nbsp;</td>
                            <td width="120"align="right"><? echo number_format($balance,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($returnable_balance,2,'.',''); ?>&nbsp;</td>
                        </tr>
                    <?
                        $i++;
                    }
                    ?>
                </table>
            </div>
        </fieldset>
	<?
	}
	else if($type==2)
	{
		//$all_party=explode(",",$knitting_company);

		$po_arr=array();
		$datapoArray=sql_select("select id, po_number, po_quantity from wo_po_break_down");

		foreach($datapoArray as $row)
		{
			$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
			$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
		}
		if($db_type==0) $grpby_field="group by trans_id";
		if($db_type==2) $grpby_field="group by trans_id,entry_form,trans_type";
		else $grpby_field="";

		if (str_replace("'","",$txt_challan)=="") $challan_cond=""; else $challan_cond=" and a.issue_number_prefix_num=$txt_challan";

		$order_nos_array=array();
		if($db_type==0)
		{
			$datapropArray=sql_select("select trans_id,
				CASE WHEN entry_form='16' and trans_type=2 THEN group_concat(po_breakdown_id) END AS yarn_order_id,
				CASE WHEN entry_form in (2,22) and trans_type=1 THEN group_concat(po_breakdown_id) END AS grey_order_id,
				CASE WHEN entry_form='51' and trans_type=4 THEN group_concat(po_breakdown_id) END AS yarn_return_order_id
				from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (2,16,22,51) and status_active=1 and is_deleted=0 group by trans_id");
		}
		else
		{
			$datapropArray=sql_select("select trans_id,
				listagg(CASE WHEN entry_form='16' and trans_type=2 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS yarn_order_id,
				listagg(CASE WHEN entry_form in (2,22) and trans_type=1 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS grey_order_id,
				listagg(CASE WHEN entry_form='51' and trans_type=4 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS yarn_return_order_id
				from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (2,16,22,51) and status_active=1 and is_deleted=0 group by trans_id,entry_form,trans_type");
		}

		foreach($datapropArray as $row)
		{
			$order_nos_array[$row[csf('trans_id')]]['yarn_issue']=$row[csf('yarn_order_id')];
			$order_nos_array[$row[csf('trans_id')]]['grey_recv']=$row[csf('grey_order_id')];
			$order_nos_array[$row[csf('trans_id')]]['yarn_return']=$row[csf('yarn_return_order_id')];
		}

	?>
        <fieldset style="width:2030px">
            <table width="2117" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
                <tr>
                   <td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $report_title; ?> (Party Wise)</strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
                </tr>
            </table>
            <br />
            <table width="2117" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="40">SL</th>
                    <th width="80">Date</th>
                    <th width="125">Transaction Ref.</th>
                    <th width="115">Recv. Challan No</th>
                    <th width="115">Issue Challan No</th>
                    <th width="130">Booking/Reqsn. No</th>
                    <th width="80">Buyer</th>
                    <th width="130">Order Numbers</th>
                    <th width="90">Order Qnty.</th>
                    <th width="80">Brand</th>
                    <th width="150">Item Description</th>
                    <th width="80">Lot</th>
                    <th width="60">UOM</th>
                    <th width="100">Grey Issued</th>
                    <th width="100">Returnable Qty.</th>
                    <th width="100">Fabric Received</th>
                    <th width="100">Reject Fabric Received</th>
                    <th width="100">Grey Returned</th>
                    <th width="100">Reject Grey Returned</th>
                    <th width="100">Balance</th>
                    <th width="">Returnable Balanace</th>
                </thead>
            </table>
            <div style="width:2117px; overflow-y: scroll; max-height:380px;" id="scroll_body">
                <table width="2100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<?
					if (str_replace("'","",$cbo_dyeing_source)==0) $dyeing_source_cond_party=""; else $dyeing_source_cond_party=" and knit_dye_source=$cbo_dyeing_source";
					if ($dyeing_company=='') $dyeing_company_cond_party=""; else  $dyeing_company_cond_party=" and a.id in ($dyeing_company)";
					if ($dyeing_company=='') $dyeing_company_cond_comp=""; else  $dyeing_company_cond_comp=" and id in ($dyeing_company)";
					$dyeing_source=str_replace("'","",$cbo_dyeing_source);
					//echo $cbo_dyeing_source;
					if ($dyeing_source==3)
					{
						$sql_party="SELECT a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c, inv_issue_master d where a.id=b.supplier_id and c.supplier_id=b.supplier_id and a.id=d.knit_dye_company and c.tag_company=$cbo_company_name and b.party_type in(1,9,20,21,24) and a.status_active=1 $dyeing_company_cond_party group by a.id, a.supplier_name order by a.supplier_name"; //13-11-2014  (add issue master for specific challan party)
					}
					elseif($dyeing_source==1)
					{
						$sql_party="SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $knitting_company_cond_comp $company_cond order by comp.company_name";
					}
					//echo $sql_party;
					$all_party_name=sql_select($sql_party);
                    $i=1; $m=1; $balance=0; $tot_iss_qnty=0; $tot_recv_qnty=0; $tot_rej_qnty=0; $tot_ret_qnty=0; $tot_reject_yarn_qnty=0; $challan_array=array();
					foreach ($all_party_name as $party )//($j=0;$j<=count($all_party);$j++)
					{
						$party_name=$party[csf('id')];

						if($dyeing_source==1) $dyeing_party=$company_arr[$party_name];
						else if($dyeing_source==3) $dyeing_party=$supplier_arr[$party_name];
						else $dyeing_party="&nbsp;";

						if ($dyeing_source==0) $dyeing_source_cond_party=""; else $dyeing_source_cond_party=" and a.knit_dye_source in ($dyeing_source)";
						if ($party_name=='') $dyeing_company_cond_party=""; else  $dyeing_company_cond_party=" and a.knit_dye_company in ($party_name)";

					 	$sql="SELECT a.issue_number, a.issue_number_prefix_num, a.buyer_id, a.booking_id, a.booking_no, a.issue_date, a.challan_no, a.issue_basis, b.id as trans_id, b.cons_uom, b.requisition_no, b.brand_id, b.cons_quantity as issue_qnty, b.return_qnty, c.product_name_details, c.lot
					 	from inv_issue_master a, inv_transaction b, product_details_master c
					 	where a.item_category=13 and a.entry_form=16 and a.company_id=$cbo_company_name   and a.issue_date between '$from_date' and '$to_date' and a.id=b.mst_id and b.item_category=13 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $dyeing_source_cond_party  $dyeing_company_cond_party $challan_cond
					 	order by a.knit_dye_company, a.issue_number_prefix_num, a.issue_date";
					 	// echo $sql;
						$result=sql_select($sql);
						if (!empty($result))
						{
							?><tr bgcolor="#EFEFEF"><td colspan="21"><b>Party name: <? echo $dyeing_party; ?></b></td></tr><?
						}
						foreach($result as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							if($row[csf('issue_basis')]==1)
								$booking_reqsn_no=$row[csf('booking_no')];
							else if($row[csf('issue_basis')]==3)
								$booking_reqsn_no=$row[csf('requisition_no')];
							else
								$booking_reqsn_no="&nbsp;";

							$balance=$balance+$row[csf('issue_qnty')];
                    		$tot_iss_qnty=$tot_iss_qnty+$row[csf('issue_qnty')];
							$tot_returnable_qnty+=$row[csf('return_qnty')];
							$tot_return_balance+=$row[csf('return_qnty')]+$row[csf('cons_quantity')];

							$order_nos=''; $order_qnty=0;
							$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['yarn_issue']);
							foreach($all_po_id as $po_id)
							{
								if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
								$order_qnty+=$po_arr[$po_id]['qnty'];
							}

						?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="40"><? echo $i; ?></td>
								<td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
								<td width="125"><p><? echo $row[csf('issue_number')]; ?></p></td>
								<td width="115">&nbsp;</td>
								<td width="115"><p>&nbsp;<? echo $row[csf('challan_no')]; ?></p></td>
								<td width="130"><p>&nbsp;<? echo $booking_reqsn_no; ?></p></td>
								<td width="80"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
								<td width="130"><p><? echo $order_nos; ?>&nbsp;</p></td>
								<td width="90" align="right"><? echo number_format($order_qnty,0,'.',''); ?>&nbsp;</td>
								<td width="80"><p>&nbsp;<? echo $brand_arr[$row[csf('brand_id')]]; ?></p></td>
								<td width="150"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td width="80"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="60" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
								<td width="100" align="right"><? echo number_format($row[csf('issue_qnty')],2,'.',''); ?>&nbsp;</td>
                                <td width="100" align="right"><? echo number_format($row[csf('return_qnty')],2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right"><? echo number_format($balance,2,'.',''); ?>&nbsp;</td>
                                <td align="right"><? $return_balance=$row[csf('return_qnty')]; echo number_format($return_balance,2,'.',''); ?>&nbsp;</td>
							</tr>
						<?
						$i++;
						$issue_qty_tot+=$row[csf('issue_qnty')];
						$returnable_qnty_tot+=$row[csf('return_qnty')];
						$balance_qty_tot+=$balance;
						$returnable_balance+=$row[csf('return_qnty')];
					}
					if ($dyeing_source==0) $dyeing_source_cond_party_rec=""; else $dyeing_source_cond_party_rec=" and a.knitting_source in ($dyeing_source)";
					if ($party_name=='') $dyeing_company_cond_party_rec=""; else  $dyeing_company_cond_party_rec=" and a.knitting_company in ($party_name)";

					if (str_replace("'","",$txt_challan)=="") $issue_challan_cond=""; else $issue_challan_cond=" and a.yarn_issue_challan_no=$txt_challan";

					$query="SELECT a.recv_number, a.buyer_id, a.booking_no, a.buyer_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, b.id as trans_id, b.cons_uom,  b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, c.product_name_details, c.lot
					from inv_receive_master a, inv_transaction b, product_details_master c
					where a.item_category=13 and a.entry_form in(2,22,51) and a.company_id=$cbo_company_name  and a.receive_date between '$from_date' and '$to_date' and a.id=b.mst_id and b.item_category=13 and b.transaction_type in(1,4) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $dyeing_source_cond_party_rec  $dyeing_company_cond_party_rec $issue_challan_cond
					order by a.knitting_company, a.yarn_issue_challan_no, b.transaction_type, a.receive_date";//and a.knitting_source=$cbo_dyeing_source and a.knitting_company=$party_name
					//echo $query;
					$result2=sql_select($query);
					foreach($result2 as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if($row[csf('item_category')]==13)
						{
							$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['grey_recv']);
							$tot_recv_qnty+=$row[csf('cons_quantity')];
							$tot_rej_qnty+=$row[csf('cons_reject_qnty')];
							$balance=$balance-($row[csf('cons_quantity')]+$row[csf('cons_reject_qnty')]);
						}
						else
						{
							$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['yarn_return']);
							$tot_ret_qnty+=$row[csf('cons_quantity')];
							$tot_reject_yarn_qnty+=$row[csf('cons_reject_qnty')];
							$balance=$balance-($row[csf('cons_quantity')]+$row[csf('cons_reject_qnty')]);
						}
						$order_nos=''; $order_qnty=0;
						foreach($all_po_id as $po_id)
						{
							if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
							$order_qnty+=$po_arr[$po_id]['qnty'];
						}
						$tot_returnable_qnty+=$row[csf('return_qnty')];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td width="40"><? echo $i; ?></td>
							<td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
							<td width="125"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="115"><p>&nbsp;<? echo $row[csf('challan_no')]; ?></p></td>
							<td width="115"><p>&nbsp;<? echo $row[csf('yarn_issue_challan_no')]; ?></p></td>
							<td width="130"><p>&nbsp;<? echo $row[csf('booking_no')]; ?></p></td>
							<td width="80"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
							<td width="130"><p><? echo $order_nos; ?>&nbsp;</p></td>
							<td width="90" align="right"><? echo number_format($order_qnty,0,'.',''); ?>&nbsp;</td>
							<td width="80"><p>&nbsp;<? echo $brand_arr[$row[csf('brand_id')]]; ?></p></td>
							<td width="150"><p><? echo $row[csf('product_name_details')]; ?></p></td>
							<td width="80"><p><? echo $row[csf('lot')]; ?></p></td>
							<td width="60" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
							<td width="100" align="right">&nbsp;</td>
							<td width="100" align="right"><? echo number_format($row[csf('return_qnty')],2,'.',''); ?>&nbsp;</td>
							<td width="100" align="right"><? if($row[csf('item_category')]==13) echo number_format($row[csf('cons_quantity')],2,'.',''); ?>&nbsp;</td>
							<td width="100" align="right"><? if($row[csf('item_category')]==13) echo number_format($row[csf('cons_reject_qnty')],2,'.',''); ?>&nbsp;</td>
							<td width="100" align="right"><? if($row[csf('item_category')]==1) $yearn_return=$row[csf('cons_quantity')]; echo number_format($yearn_return,2,'.',''); ?>&nbsp;</td>
							<td width="100" align="right"><? if($row[csf('item_category')]==1) echo number_format($row[csf('cons_reject_qnty')],2,'.',''); ?>&nbsp;</td>
							<td width="100" align="right"><? echo number_format($balance,2,'.',''); ?>&nbsp;</td>
							<td align="right"><? $return_balance=$row[csf('return_qnty')]-$row[csf('cons_quantity')]; echo number_format($return_balance,2,'.',''); ?>&nbsp;</td>
						</tr>
					<?
					$i++;
				}
			$m++;
			}
			?>
		</table>
	</div>
	<?
	}
	else if($type==3)
	{

		?>
		<table width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="40">SL</th>
                    <th width="80">Date</th>
                    <th width="115">Transaction Ref.</th>
                    <th width="115">Recv. Challan No</th>
                    <th width="115">Issue Challan No</th>
                    <th width="130">Booking/Reqsn. No</th>
                    <th width="80">Buyer</th>
                    <th width="130">Order Numbers</th>
                    <th width="90">Order Qnty.</th>
                    <th width="150">Item Description</th>
                    <th width="60">UOM</th>
                    <th width="100">Grey Fab. Issued</th>
                    <th width="100">Grey Issue Return</th>
                    <th width="100">Fin. Fabric Received</th>
                    <th width="100">Fini. Rcvd. Return</th>
                    <th>Balance</th>
                </thead>
            </table>
        <div style="width:1675px; overflow-y: scroll; max-height:380px;" id="scroll_body">
            <table width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
		<?

		$datapoArray=sql_select("select id, po_number, po_quantity from wo_po_break_down");

		foreach($datapoArray as $row)
		{
			$po_arr[$row[csf('id')]]['po_no']=$row[csf('po_number')];
			$po_arr[$row[csf('id')]]['po_qty']=$row[csf('po_quantity')];
		}

			$txt_date_from=str_replace("'","",$txt_date_from);
			$txt_date_to=str_replace("'","",$txt_date_to);
		if( $txt_date_from!="" && $txt_date_to!="" )
		{
			if($db_type==0)
			{
				$date_cond= " and a.issue_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
			}
			else
			{
				$date_cond= " and a.issue_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
			}
		}


	if (str_replace("'","",$txt_challan)==""){
	$challan_cond="";
	$challan_cond_rec="";
	}
	else
	{
	$challan_cond=" and a.issue_number_prefix_num=$txt_challan";
	$challan_cond_rec=" and a.yarn_issue_challan_no=$txt_challan";
	}

	if (str_replace("'","",$txt_dyeing_com_id)==""){
		$knit_company_cond_party_iss="";
		$knit_company_cond_party_rec="";
	}
	else
	{
		$knit_company_cond_party_iss=" and a.knit_dye_company in(".str_replace("'","",$txt_dyeing_com_id).")";
		$knit_company_cond_party_rec=" and a.knitting_company in(".str_replace("'","",$txt_dyeing_com_id).")";
	}

	$sql = "select
		a.id,
		a.issue_number_prefix_num,
		a.issue_number,
		a.challan_no,
		a.knit_dye_company,
		a.knit_dye_source,
		a.issue_date,
		a.booking_no,
		a.buyer_id,
		a.order_id,
	b.issue_qnty,
	c.product_name_details,
	c.unit_of_measure
			from inv_issue_master a, inv_grey_fabric_issue_dtls b, product_details_master c
			where a.id=b.mst_id and b.prod_id=c.id and a.knit_dye_source=$cbo_dyeing_source and a.entry_form=16 and a.company_id=$cbo_company_name $date_cond $challan_cond $knit_company_cond_party_iss";


		//echo $sql;
		$res = sql_select($sql);
		foreach($res as $row)
		{
			$issue_data_arr[$row[csf('knit_dye_source')].'**'.$row[csf('knit_dye_company')]][$row[csf('issue_number_prefix_num')]][]=array(
				'issue_id'=>$row[csf('id')],
				'issue_number'=>$row[csf('issue_number')],
				'issue_date'=>$row[csf('issue_date')],
				'challan_no'=>$row[csf('challan_no')],
				'booking_no'=>$row[csf('booking_no')],
				'buyer_id'=>$row[csf('buyer_id')],
				'order_id'=>$row[csf('order_id')],
				'product_name_details'=>$row[csf('product_name_details')],
				'unit_of_measure'=>$row[csf('unit_of_measure')],
				'issue_qnty'=>$row[csf('issue_qnty')],
				'key'=>$row[csf('id')].'**'.$row[csf('product_name_details')]

			);

		}


	//----------------issue return;
		$issue_ret = "select a.issue_id,a.recv_number_prefix_num,a.challan_no, a.recv_number,a.booking_no, a.supplier_id, a.receive_date, a.recv_number, b.cons_quantity,c.product_name_details
					from inv_receive_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id
					where a.id=b.mst_id and a.company_id=$cbo_company_name and b.item_category=13 and b.transaction_type=4";
		$res_issur_ret = sql_select($issue_ret);
		foreach($res_issur_ret as $row)
		{
			$data_issue_ret_arr[$row[csf('issue_id')].'**'.$row[csf('product_name_details')]][]=array(
				'recv_number_prefix_num'=>$row[csf('recv_number_prefix_num')],
				'challan_no'=>$row[csf('challan_no')],
				'recv_number'=>$row[csf('recv_number')],
				'booking_no'=>$row[csf('booking_no')],
				'receive_date'=>$row[csf('receive_date')],
				'cons_quantity'=>$row[csf('cons_quantity')],
				'product_name_details'=>$row[csf('product_name_details')],
			);

		}
	//--------receive

		$sql_rec="select a.recv_number_prefix_num,a.challan_no,a.receive_basis,a.recv_number,a.booking_no,a.receive_date, a.yarn_issue_challan_no, c.product_name_details, b.receive_qnty, b.order_id, b.buyer_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b left join product_details_master c on b.prod_id=c.id where a.id=b.mst_id and a.item_category=2 and a.entry_form=37 and a.knitting_source=$cbo_dyeing_source  and a.company_id=$cbo_company_name $challan_cond_rec $knit_company_cond_party_rec"	;

		$res_rec = sql_select($sql_rec);
		foreach($res_rec as $row)
		{
			$data_rec_arr[$row[csf('yarn_issue_challan_no')].'**'.$row[csf('product_name_details')]][]=array		(
				'challan_no'=>$row[csf('challan_no')],
				'recv_number'=>$row[csf('recv_number')],
				'booking_no'=>$row[csf('booking_no')],
				'receive_date'=>$row[csf('receive_date')],
				'receive_qnty'=>$row[csf('receive_qnty')],
				'product_name_details'=>$row[csf('product_name_details')],
			);

		}

		//print_r($data_rec_arr);

	//receive return;
			$sql = "select a.issue_number,a.received_mrr_no,a.issue_date,a.received_id,b.cons_quantity ,c.product_name_details
				from inv_issue_master a ,inv_transaction b, product_details_master c
				where a.id=b.mst_id and b.prod_id=c.id and a.item_category=2 and a.entry_form=46 and a.company_id=$cbo_company_name";
		//echo $sql;die;
		$result = sql_select($sql);
		foreach($result as $row)
		{
			$data_rec_ret_arr[$row[csf('received_mrr_no')].'**'.$row[csf('product_name_details')]][]=array(
				'challan_no'=>$row[csf('challan_no')],
				'issue_number'=>$row[csf('issue_number')],
				'issue_date'=>$row[csf('issue_date')],
				'cons_quantity'=>$row[csf('cons_quantity')],
				'product_name_details'=>$row[csf('product_name_details')],
			);

		}

	//print_r($data_rec_ret_arr);


	//------------------------------------
		$s=1;
		foreach($issue_data_arr as $kds_kdc=>$challan_arr)
		{
			list($knite_source,$party_id)=explode('**',$kds_kdc);
			?>
				<tr bgcolor="#dddddd">
					<td colspan="16" align="left" >
					<b>Party Name: <? if ($knite_source==1) echo $company_arr[$party_id]; else if ($knite_source==3) echo $supplier_arr[$party_id]; ?></b>
					</td>
				</tr>
			<?
			foreach($challan_arr as $challan_id=>$challan_row)
			{
				?>
					<tr bgcolor="#dddddd">
						<td colspan="16"><b>Grey Issue Chalan No :  <? echo $challan_id; ?></b></td>
					</tr>
				<?
				$issue_blance=0; $i=0;
				foreach($challan_row as $rows)
				{	$i++;$s++;
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $s;?>','<? echo $bgcolor;?>')" id="tr<? echo $s;?>">
						<td width="40"><? echo $i;?></td>
						<td width="80" align="center"><? echo change_date_format($rows[issue_date]); ?></td>
						<td width="115" align="center"><? echo $rows[issue_number]; ?></td>
						<td width="115"><? //echo $rows[challan_no]; ?></td>
						<td width="115"><? echo $rows[challan_no]; ?></td>
						<td width="130" align="center"><? echo $rows[booking_no]; ?></td>
						<td width="80"><? echo $buyer_arr[$rows[buyer_id]]; ?></td>
						<td width="130"><? echo $po_arr[$rows[order_id]][po_no]; ?></td>
						<td width="90" align="right"><? echo $po_arr[$rows[order_id]][po_qty]; ?></td>
						<td width="150"><? echo $rows[product_name_details]; ?></td>
						<td width="60" align="center"><? echo $unit_of_measurement[$rows[unit_of_measure]]; ?></td>
						<td width="100" align="right"><? echo $rows[issue_qnty]; $issue_blance+=$rows[issue_qnty]; ?></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td align="right"><? echo $issue_blance; ?></td>
					</tr>
				<?
					//issur return;
					foreach($data_issue_ret_arr[$rows['key']] as $rows_ir)
					{
						$i++;$s++;
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $s;?>','<? echo $bgcolor;?>')" id="tr<? echo $s;?>">
							<td width="40"><? echo $i;?></td>
							<td width="80" align="center"><? echo change_date_format($rows_ir[receive_date]); ?></td>
							<td width="115" align="center"><? echo $rows_ir[recv_number]; ?></td>
							<td width="115" align="center"><? echo $rows_ir[challan_no]; ?></td>
							<td width="115"></td>
							<td width="130" align="center"><? echo $rows_ir[booking_no]; ?></td>
							<td width="80"><? echo $buyer_arr[$rows[buyer_id]]; ?></td>
							<td width="130"><? echo $po_arr[$rows[order_id]][po_no]; ?></td>
							<td width="90" align="right"><? echo $po_arr[$rows[order_id]][po_qty]; ?></td>
							<td width="150"><? echo $rows_ir[product_name_details]; ?></td>
							<td width="60" align="center"><? echo $unit_of_measurement[$rows[unit_of_measure]]; ?></td>
							<td width="100"></td>
							<td width="100" align="right"><? echo $rows_ir[cons_quantity]; $issue_blance-=$rows_ir[cons_quantity]; ?></td>
							<td width="100"></td>
							<td width="100"></td>
							<td align="right"><? echo $issue_blance; ?></td>
						</tr>
					<?
					}//issur retur;
					//received;
					$key=$challan_id.'**'.$rows[product_name_details];
					//echo  $key.'*';
					//var_dump($data_rec_arr);
					foreach($data_rec_arr[$key] as $rows_ir)
					{
						$i++;$s++;
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $s;?>','<? echo $bgcolor;?>')" id="tr<? echo $s;?>">
							<td width="40"><? echo $i;?></td>
							<td width="80" align="center"><? echo change_date_format($rows_ir[receive_date]); ?></td>
							<td width="115" align="center"><? echo $rows_ir[recv_number]; ?></td>
							<td width="115" align="center"><? echo $rows_ir[challan_no]; ?></td>
							<td width="115"></td>
							<td width="130" align="center"><? echo $rows_ir[booking_no]; ?></td>
							<td width="80"><? echo $buyer_arr[$rows[buyer_id]]; ?></td>
							<td width="130"><? echo $po_arr[$rows[order_id]][po_no]; ?></td>
							<td width="90" align="right"><? echo $po_arr[$rows[order_id]][po_qty]; ?></td>
							<td width="150"><? echo $rows_ir[product_name_details]; ?></td>
							<td width="60" align="center"><? echo $unit_of_measurement[$rows[unit_of_measure]]; ?></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100" align="right"><? echo $rows_ir[receive_qnty]; $issue_blance+=$rows_ir[receive_qnty]; ?></td>
							<td width="100"></td>
							<td align="right"><? echo $issue_blance; ?></td>
						</tr>
					<?
						//received ret;
						$key=$rows_ir[recv_number].'**'.$rows[product_name_details];
						foreach($data_rec_ret_arr[$key] as $rows_rr)
						{
							$i++;$s++;
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $s;?>','<? echo $bgcolor;?>')" id="tr<? echo $s;?>">
								<td width="40"><? echo $i;?></td>
								<td width="80" align="center"><? echo change_date_format($rows_rr[issue_date]); ?></td>
								<td width="115" align="center"><? echo $rows_rr[issue_number]; ?></td>
								<td width="115" align="center"><? echo $rows_rr[challan_no]; ?></td>
								<td width="115"></td>
								<td width="130" align="center"><? echo $rows_rr[booking_no]; ?></td>
								<td width="80"><? echo $buyer_arr[$rows[buyer_id]]; ?></td>
								<td width="130"><? echo $po_arr[$rows[order_id]][po_no]; ?></td>
								<td width="90" align="right"><? echo $po_arr[$rows[order_id]][po_qty]; ?></td>
								<td width="150"><? echo $rows_rr[product_name_details]; ?></td>
								<td width="60" align="center"><? echo $unit_of_measurement[$rows[unit_of_measure]]; ?></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="100" align="right"><? echo $rows_rr[cons_quantity]; $issue_blance-=$rows_ir[cons_quantity]; ?></td>
								<td align="right"><? echo $issue_blance; ?></td>
							</tr>
						<?
						}//receive ret;
					}//receive;
				}
			}
		}
		?>
		</table>
		<?

	}
	else if($type==4) //Returnable Button
	{
		//$all_party=explode(",",$knitting_company);
		$po_arr=array();
		$datapoArray=sql_select("select id, job_no_mst, po_number, po_quantity from wo_po_break_down");

		foreach($datapoArray as $row)
		{
			$po_arr[$row[csf('id')]]['job']=$row[csf('job_no_mst')];
			$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
			$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
		}
		if($db_type==0) $grpby_field="group by trans_id";
		if($db_type==2) $grpby_field="group by trans_id,entry_form,trans_type";
		else $grpby_field="";

		if (str_replace("'","",$txt_challan)=="") $challan_cond=""; else $challan_cond=" and a.issue_number_prefix_num=$txt_challan";

		$order_nos_array=array();
		if($db_type==0)
		{
			$datapropArray=sql_select("select trans_id,
				CASE WHEN entry_form='16' and trans_type=2 THEN group_concat(po_breakdown_id) END AS yarn_order_id,
				CASE WHEN entry_form in (2,22) and trans_type=1 THEN group_concat(po_breakdown_id) END AS grey_order_id,
				CASE WHEN entry_form='51' and trans_type=4 THEN group_concat(po_breakdown_id) END AS yarn_return_order_id
				from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (2,16,22,51) and status_active=1 and is_deleted=0 group by trans_id");
		}
		else
		{
			$datapropArray=sql_select("select trans_id,
				listagg(CASE WHEN entry_form='16' and trans_type=2 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS yarn_order_id,
				listagg(CASE WHEN entry_form in (2,22) and trans_type=1 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS grey_order_id,
				listagg(CASE WHEN entry_form='51' and trans_type=4 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS yarn_return_order_id
				from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (2,16,22,51) and status_active=1 and is_deleted=0 group by trans_id,entry_form,trans_type");
		}

		foreach($datapropArray as $row)
		{
			$order_nos_array[$row[csf('trans_id')]]['yarn_issue']=$row[csf('yarn_order_id')];
			$order_nos_array[$row[csf('trans_id')]]['grey_recv']=$row[csf('grey_order_id')];
			$order_nos_array[$row[csf('trans_id')]]['yarn_return']=$row[csf('yarn_return_order_id')];
		}
		?>
        <fieldset style="width:1400px">
            <table width="1370" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
                <tr>
                   <td align="center" width="100%" colspan="15" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="15" style="font-size:16px"><strong><? echo $report_title; ?> (Returnable)</strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="15" style="font-size:16px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
                </tr>
            </table>
            <br />
            <table width="1370" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="40">SL</th>
                    <th width="70">Date</th>
                    <th width="110">Job No.</th>
                    <th width="110">Order No</th>
                    <th width="80">Buyer</th>
                    <th width="80">Count</th>
                    <th width="80">Supplier</th>
                    <th width="80">Type</th>
                    <th width="80">Lot</th>
                    <th width="130">Booking/Reqsn. No</th>
                    <th width="100">Issue Qty.</th>
                    <th width="100">Returnable Qty.</th>
                    <th width="100">Returned Qty</th>
                    <th width="100">Reject Qty</th>
                    <th width="">Returnable Balanace</th>
                </thead>
            </table>
            <div style="width:1390px; overflow-y: scroll; max-height:380px;" id="scroll_body">
                <table width="1370" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<?
				$company_arr=return_library_array("select id, company_name from lib_company", "id", "company_name");
				$supplier_arr=return_library_array("select id, short_name from lib_supplier", "id", "short_name");
				$count_arr=return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
				if (str_replace("'","",$cbo_dyeing_source)==0) $knitting_source_cond_party=""; else $knitting_source_cond_party=" and knit_dye_source=$cbo_dyeing_source";
				if ($knitting_company=='') $knitting_company_cond_party=""; else  $knitting_company_cond_party=" and a.id in ($knitting_company)";
				if ($knitting_company=='') $knitting_company_cond_comp=""; else  $knitting_company_cond_comp=" and id in ($knitting_company)";
				$knit_source=str_replace("'","",$cbo_dyeing_source);

				$i=1; $k=1; $j=1; $balance=0; $tot_iss_qnty=0; $tot_recv_qnty=0; $tot_rej_qnty=0; $tot_ret_qnty=0; $tot_reject_yarn_qnty=0; $challan_array=array(); $party_array=array(); /*$receive_array=array();
				if (str_replace("'","",$txt_challan)=="") $issue_challan_cond=""; else $issue_challan_cond=" and a.yarn_issue_challan_no=$txt_challan";
				if ($from_date!='' && $to_date!='') $rec_date_cond=" and a.receive_date between '$from_date' and '$to_date'"; else $rec_date_cond="";
				$sql_rec="select a.recv_number, a.booking_no, a.buyer_id, a.receive_date, a.knitting_source, a.knitting_company, a.item_category, a.challan_no, a.yarn_issue_challan_no, b.id as trans_id, b.cons_uom,  b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, c.product_name_details, c.lot from inv_receive_master a, inv_transaction b, product_details_master c where a.item_category in(1,13) and a.entry_form in(2,22) and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category in(1,13) and b.transaction_type in(1) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $knit_source_cond_party_rec  $knit_company_cond_party_rec $issue_challan_cond $rec_date_cond order by a.knitting_company, a.yarn_issue_challan_no";//and a.knitting_source=$cbo_dyeing_source and a.knitting_company=$party_name
				$sql_rec_result=sql_select($sql_rec);
				foreach($sql_rec_result as $row)
				{
					if($row[csf('item_category')]==13)
					{
						$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['grey_recv']);

						$tot_recv_qnty+=$row[csf('cons_quantity')];
						$tot_rej_qnty+=$row[csf('cons_reject_qnty')];
						$balance=$balance-($row[csf('cons_quantity')]+$row[csf('cons_reject_qnty')]);
					}
					else
					{
						$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['yarn_return']);
						$tot_ret_qnty+=$row[csf('cons_quantity')];
						$tot_reject_yarn_qnty+=$row[csf('cons_reject_qnty')];
						$balance=$balance-($row[csf('cons_quantity')]+$row[csf('cons_reject_qnty')]);
					}
					$order_nos=''; $order_qnty=0;
					foreach($all_po_id as $po_id)
					{
						if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
						$order_qnty+=$po_arr[$po_id]['qnty'];
					}
					$tot_returnable_qnty+=$row[csf('return_qnty')];

					$receive_array[$row[csf('yarn_issue_challan_no')]].=change_date_format($row[csf('receive_date')],'dd-mm-yyyy')."_".$row[csf('recv_number')]."_".$row[csf('challan_no')]."_".$row[csf('yarn_issue_challan_no')]."_".$row[csf('booking_no')]."_".$row[csf('buyer_id')]."_".$order_nos."_".$order_qnty."_".$row[csf('brand_id')]."_".$row[csf('product_name_details')]."_".$row[csf('lot')]."_".$row[csf('cons_uom')]."_".$row[csf('cons_quantity')]."_".$row[csf('cons_reject_qnty')]."_".$row[csf('return_qnty')]."***";
				}
				//var_dump($receive_array);die;*/
				$receive_ret_array=array();
				if (str_replace("'","",$txt_challan)=="") $issue_challan_ret_cond=""; else $issue_challan_ret_cond=" and b.issue_challan_no=$txt_challan";
				if ($from_date!='' && $to_date!='') $ret_date_cond=" and a.receive_date between '$from_date' and '$to_date'"; else $ret_date_cond="";
				$sql_return="select a.booking_no, a.buyer_id, a.receive_date, a.item_category, b.issue_challan_no, b.id as trans_id, c.supplier_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, c.lot, c.yarn_count_id, c.yarn_type from inv_receive_master a, inv_transaction b, product_details_master c where a.item_category=13 and a.entry_form in(51) and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category=13 and b.transaction_type in(4) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $knit_source_cond_party_rec  $knit_company_cond_party_rec $ret_date_cond $issue_challan_ret_cond order by a.knitting_company, b.issue_challan_no";//and a.knitting_source=$cbo_dyeing_source and a.knitting_company=$party_name

				$sql_return_result=sql_select($sql_return);
				foreach($sql_return_result as $row)
				{
					if($row[csf('item_category')]==13)
					{
						$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['grey_recv']);
						$tot_recv_qnty+=$row[csf('cons_quantity')];
						$tot_rej_qnty+=$row[csf('cons_reject_qnty')];
						$balance=$balance-($row[csf('cons_quantity')]+$row[csf('cons_reject_qnty')]);
					}
					else
					{
						$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['yarn_return']);
						$tot_ret_qnty+=$row[csf('cons_quantity')];
						$tot_reject_yarn_qnty+=$row[csf('cons_reject_qnty')];
						$balance=$balance-($row[csf('cons_quantity')]+$row[csf('cons_reject_qnty')]);
					}
					$all_job_no=""; $order_nos=''; $order_qnty=0;
					foreach($all_po_id as $po_id)
					{
						if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
						if($all_job_no=='') $all_job_no=$po_arr[$po_id]['job']; else $all_job_no.=",".$po_arr[$po_id]['job'];
						$order_qnty+=$po_arr[$po_id]['qnty'];
					}
					$job_no=implode(",",array_unique(explode(",",$all_job_no)));
					$tot_returnable_qnty+=$row[csf('return_qnty')];

					$receive_ret_array[$row[csf('issue_challan_no')]].=change_date_format($row[csf('receive_date')],'dd-mm-yyyy')."_".$row[csf('booking_no')]."_".$row[csf('buyer_id')]."_".$order_nos."_".$order_qnty."_".$row[csf('yarn_count_id')]."_".$row[csf('supplier_id')]."_".$row[csf('yarn_type')]."_".$row[csf('lot')]."_".$row[csf('cons_quantity')]."_".$row[csf('cons_reject_qnty')]."_".$row[csf('return_qnty')]."_".$job_no."***";
				}
				//var_dump($receive_ret_array);die;
				if ($knit_source==0) $knit_source_cond_party=""; else $knit_source_cond_party=" and a.knit_dye_source in ($knit_source)";
				if ($knitting_company=='') $knit_company_cond_party=""; else  $knit_company_cond_party=" and a.knit_dye_company in ($knitting_company)";
				if ($from_date!='' && $to_date!='') $iss_date_cond=" and a.issue_date between '$from_date' and '$to_date'"; else $iss_date_cond="";

				$sql="select a.issue_number, a.issue_number_prefix_num, a.issue_purpose, a.buyer_id, a.knit_dye_company, a.knit_dye_source, a.booking_id, a.booking_no, a.issue_date, a.issue_basis, b.id as trans_id, b.requisition_no, b.supplier_id, b.cons_quantity as issue_qnty, b.return_qnty, c.yarn_count_id, c.yarn_type, c.lot from inv_issue_master a, inv_transaction b, product_details_master c where a.item_category=13 and a.entry_form=16 and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category=13 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.return_qnty!=0 $knit_source_cond_party  $knit_company_cond_party $challan_cond $iss_date_cond order by a.knit_dye_company, a.issue_number_prefix_num";
				$result=sql_select($sql); $rec_dtls_array=array();
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					if($row[csf('issue_basis')]==1)
						$booking_reqsn_no=$row[csf('booking_no')];
					else if($row[csf('issue_basis')]==3)
						$booking_reqsn_no=$row[csf('requisition_no')];
					else
						$booking_reqsn_no="&nbsp;";

					$all_job_no=''; $order_nos=''; $order_qnty=0;
					$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['yarn_issue']);
					foreach($all_po_id as $po_id)
					{
						if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
						if($all_job_no=='') $all_job_no=$po_arr[$po_id]['job']; else $all_job_no.=",".$po_arr[$po_id]['job'];
						$order_qnty+=$po_arr[$po_id]['qnty'];
					}
					$job_no=implode(",",array_unique(explode(",",$all_job_no)));
					$trans_data_in=$row[csf('issue_number_prefix_num')];
					$trans_in_array[]=$trans_data_in;

					$trans_data_ret=$row[csf('issue_number_prefix_num')];
					$trans_ret_array[]=$trans_data_ret;
					$balance_return_ret=0; $ch_ret_qty=0;
					if (!in_array( $row[csf("knit_dye_company")],$party_array) )
					{
						if($k!=1)
						{
							$dataArray_ret=array_filter(explode("***",substr($receive_ret_array[$iss_challan],0,-1)));
							foreach($dataArray_ret as $key=>$val_ret)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$value=explode("_",$val_ret);
								$ret_date=$value[0];
								$ret_booking=$value[1];
								$ret_buyer=$value[2];
								$ret_order=$value[3];
								$ret_po_qty=$value[4];
								$ret_count_id=$value[5];
								$ret_supplier=$value[6];
								$ret_type=$value[7];
								$ret_lot=$value[8];
								$ret_qty=$value[9];
								$ret_rej_qty=$value[10];
								$ret_returnable_qty=$value[11];
								$ret_job=$value[12];

								$balance_ret=$ret_qty+$ret_rej_qty;
								$balance_return_ret+=$balance_ret;
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="40"><? echo $i; ?></td>
									<td width="70" align="center"><? echo $ret_date; ?></td>
									<td width="110"><p><? echo $ret_job; ?></p></td>
									<td width="110"><p><? echo $ret_order; ?></p></td>
									<td width="80"><p><? echo $buyer_arr[$ret_buyer]; ?></td>
									<td width="80"><p>&nbsp;<? echo $count_arr[$ret_count_id]; ?></p></td>
									<td width="80"><p><? echo $supplier_arr[$ret_supplier]; ?>&nbsp;</p></td>
									<td width="80"><p><? echo $yarn_type[$ret_type]; ?>&nbsp;</p></td>
									<td width="80"><? echo $ret_lot; ?>&nbsp;</td>
									<td width="130"><p>&nbsp;<? echo $ret_booking; ?></p></td>
									<td width="100" align="right"><p><? //echo $ret_returnable_qty; ?></p></td>
									<td width="100" align="right"><p><? //echo $ret_qty; ?></p></td>
									<td width="100" align="right"><? echo number_format($ret_qty,0,'.',''); ?></td>
									<td width="100" align="right"><? echo number_format($ret_rej_qty,0,'.',''); ?></td>
									<td align="right"><? //echo number_format($balance_ret,0,'.',''); ?></td>
								</tr>
								<?
								$i++;
								$ch_ret_qty+=$ret_qty;
								$ch_ret_rej_qty+=$ret_rej_qty;
								$ch_balance_ret+=$balance_ret;

								$party_ret_qty+=$ret_qty;
								$party_ret_rej_qty+=$ret_rej_qty;
								$party_balance_ret+=$balance_ret;

								$grand_ret_qty+=$ret_qty;
								$grand_ret_rej_qty+=$ret_rej_qty;
								$grand_balance_ret+=$balance_ret;
							}
							$ch_balance=$ch_issue_qty_tot-($ch_rec_qty+$ch_rec_rej_qty+$balance_ret+$ch_ret_rej_qty);
							$ch_balance_returnable=$ch_returnable_qnty_tot-($ch_ret_qty+$ch_ret_rej_qty);

							$party_balance=$party_issue_qty_tot-($party_rec_qty+$party_rec_rej_qty+$party_ret_qty+$party_ret_rej_qty);
							$party_balance_returnable=$party_returnable_qnty_tot-($party_ret_qty+$party_ret_rej_qty);

						?>
							<tr class="tbl_bottom">
								<td colspan="10" align="right"><b>Challan Total</b></td>
								<td align="right"><? echo number_format($ch_issue_qty_tot,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($ch_returnable_qnty_tot,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($ch_ret_qty,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($ch_ret_rej_qty,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($ch_balance_returnable,2,'.',''); ?>&nbsp;</td>
							</tr>
							<tr class="tbl_bottom">
								<td colspan="10" align="right">Party Total</td>
								<td align="right"><? echo number_format($party_issue_qty_tot,2); ?></td>
								<td align="right"><? echo number_format($party_returnable_qnty_tot,2); ?></td>
								<td align="right"><? echo number_format($party_ret_qty,2); ?></td>
								<td align="right"><? echo number_format($party_ret_rej_qty,2); ?></td>
								<td align="right"><? echo number_format($party_balance,2); ?></td>
							</tr>
						<?
							//unset($po_qty_tot);
							unset($ch_issue_qty_tot);
							unset($ch_returnable_qnty_tot);
							unset($ch_ret_qty);
							unset($ch_ret_rej_qty);
							unset($ch_balance);
							unset($ch_balance_returnable);

							unset($party_issue_qty_tot);
							unset($party_returnable_qnty_tot);
							unset($party_balance_qty_tot);
							unset($party_balance);
						}
						?>
							<tr bgcolor="#dddddd">
								<td colspan="15" align="left" ><b>Party Name: <? if ($row[csf("knit_dye_source")]==1) echo $company_arr[$row[csf("knit_dye_company")]]; else if ($row[csf("knit_dye_source")]==3) echo $supplier_arr[$row[csf("knit_dye_company")]]; ?></b></td>
							</tr>
						<?

						$party_array[$k]=$row[csf("knit_dye_company")];
						$k++;
					}
								$chh_ret_qty=0;
					if(!in_array($row[csf('issue_number_prefix_num')],$challan_array))
					{
						if($j!=1)
						{
							$dataArray_ret=array_filter(explode("***",substr($receive_ret_array[$iss_challan],0,-1)));
							foreach($dataArray_ret as $key=>$val_ret)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$value=explode("_",$val_ret);
								$ret_date=$value[0];
								$ret_booking=$value[1];
								$ret_buyer=$value[2];
								$ret_order=$value[3];
								$ret_po_qty=$value[4];
								$ret_count_id=$value[5];
								$ret_supplier=$value[6];
								$ret_type=$value[7];
								$ret_lot=$value[8];
								$ret_qnty=$value[9];
								$ret_rej_qty=$value[10];
								$ret_returnable_qty=$value[11];
								$ret_job=str_replace("**","",$value[12]);

								$balance_ret=$ret_qnty+$ret_rej_qty;
								$balance_return_ret+=$balance_ret;
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="40"><? echo $i; ?></td>
									<td width="70" align="center"><? echo $ret_date; ?></td>
									<td width="110"><p><? echo $ret_job; ?></p></td>
									<td width="110"><p><? echo $ret_order; ?></p></td>
									<td width="80"><p><? echo $buyer_arr[$ret_buyer]; ?></td>
									<td width="80"><p><? echo $count_arr[$ret_count_id]; ?></p></td>
									<td width="80"><p><? echo $supplier_arr[$ret_supplier]; ?>&nbsp;</p></td>
									<td width="80"><p><? echo $yarn_type[$ret_type]; ?>&nbsp;</p></td>
									<td width="80"><? echo $ret_lot; ?>&nbsp;</td>
									<td width="130"><p>&nbsp;<? echo $ret_booking; ?></p></td>
									<td width="100" align="right"><p><? //echo $ret_returnable_qty; ?></p></td>
									<td width="100" align="right"><p><? //echo $ret_qty; ?></p></td>
									<td width="100" align="right"><? echo number_format($ret_qnty,2,'.',''); ?></td>
									<td width="100" align="right"><? echo number_format($ret_rej_qty,2,'.',''); ?></td>
									<td align="right"><? //echo number_format($balance_ret,2,'.',''); ?></td>
								</tr>
								<?
								$i++;
								$chh_ret_qty+=$ret_qnty;
								$ch_ret_rej_qty+=$ret_rej_qty;
								$ch_balance_ret+=$balance_ret;

								$party_ret_qty+=$ret_qnty;
								$party_ret_rej_qty+=$ret_rej_qty;
								$party_balance_ret+=$balance_ret;

								$grand_ret_qty+=$ret_qnty;
								$grand_ret_rej_qty+=$ret_rej_qty;
								$grand_balance_ret+=$balance_ret;
							}
							$ch_balance=$ch_returnable_qnty_tot-($chh_ret_qty+$ch_ret_rej_qty);
							$ch_balance_returnable=$ch_returnable_qnty_tot-($chh_ret_qty+$ch_ret_rej_qty);
						?>
							<tr class="tbl_bottom">
								<td colspan="10" align="right"><b>Challan Total</b></td>
								<td align="right"><? echo number_format($ch_issue_qty_tot,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($ch_returnable_qnty_tot,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($chh_ret_qty,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($ch_ret_rej_qty,2,'.',''); ?>&nbsp;</td>

								<td align="right"><? echo number_format($ch_balance_returnable,2,'.',''); ?>&nbsp;</td>
							</tr>
					<?
							//unset($po_qty_tot);
							unset($ch_issue_qty_tot);
							unset($ch_returnable_qnty_tot);
							unset($ch_rec_qty);
							unset($ch_rec_rej_qty);
							unset($ch_ret_qty);
							unset($ch_ret_rej_qty);
							unset($ch_balance);
							unset($ch_balance_returnable);
						}
					?>
						<tr><td colspan="15" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b> Challan No:&nbsp;&nbsp;<?php echo $row[csf('issue_number_prefix_num')].'; Issue Purpose:  '.$yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></b></td></tr>
					<?
						$challan_array[$j]=$row[csf('issue_number_prefix_num')];
						$j++;
					}
					$return_balance=0;
					$iss_challan=$row[csf('issue_number_prefix_num')];
				?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<td width="40"><? echo $i; ?></td>
						<td width="70" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
						<td width="110"><p><? echo $job_no; ?></p></td>
                        <td width="110"><p><? echo $order_nos; ?>&nbsp;</p></td>
                        <td width="80"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
                        <td width="80"><p>&nbsp;<? echo $count_arr[$row[csf('yarn_count_id')]]; ?></p></td>
                        <td width="80"><p>&nbsp;<? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
                        <td width="80"><p>&nbsp;<? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td width="80"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="130"><p>&nbsp;<? echo $booking_reqsn_no; ?></p></td>
                        <td width="100" align="right"><? echo number_format($row[csf('issue_qnty')],2,'.',''); ?>&nbsp;</td>
                        <td width="100" align="right"><? echo number_format($row[csf('return_qnty')],2,'.',''); ?>&nbsp;</td>
                        <td width="100" align="right">&nbsp;</td>
						<td width="100" align="right">&nbsp;</td>
                        <td align="right"><? //$return_balance+=$row[csf('return_qnty')]; echo number_format($return_balance,2,'.',''); ?>&nbsp;</td>
					</tr>
				<?
					$i++;
					$ch_issue_qty_tot+=$row[csf('issue_qnty')];
					$ch_returnable_qnty_tot+=$row[csf('return_qnty')];
					$ch_balance_qty_tot+=$balance_issue;
					$ch_returnable_balance+=$row[csf('return_qnty')];

					$party_issue_qty_tot+=$row[csf('issue_qnty')];
					$party_returnable_qnty_tot+=$row[csf('return_qnty')];
					$party_balance_qty_tot+=$row[csf('issue_qnty')];
					$party_returnable_balance+=$row[csf('return_qnty')];

					$grand_issue_qty_tot+=$row[csf('issue_qnty')];
					$grand_returnable_qnty_tot+=$row[csf('return_qnty')];
					$grand_balance_qty_tot+=$row[csf('issue_qnty')];
					$grand_returnable_balance+=$row[csf('return_qnty')];
				}
				//$dataArray2=array_filter(explode("***",substr($receive_array[$row[csf('yarn_issue_challan_no')]],0,-1)));

				foreach($receive_ret_array as $key=>$val2)
				{
					$dataArray3=array_filter(explode("***",substr($val2,0,-1)));
					foreach($dataArray3 as $id=>$val3)
					{
						$value=explode("_",$val3);
						$value_ret=explode("_",$val3);
						//echo $key;
						//print_r($val3);

						if(in_array($key,$trans_ret_array))
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							//echo $value_ret[12];
							$ret_date=$value_ret[0];
							$ret_booking=$value_ret[1];
							$ret_buyer=$value_ret[2];
							$ret_order=$value_ret[3];
							$ret_po_qty=$value_ret[4];
							$ret_count_id=$value_ret[5];
							$ret_supplier=$value_ret[6];
							$ret_type=$value_ret[7];
							$ret_lot=$value_ret[8];
							$ret_qty=$value_ret[9];
							$ret_rej_qty=$value_ret[10];
							$ret_returnable_qty=$value_ret[11];
							$ret_job=$job_no=str_replace("**","",$value_ret[12]);

							$balance_ret=($balance_issue-($ret_qty+$ret_rej_qty));
							$balance_return_ret+=$ret_returnable_qty;
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="70" align="center"><? echo $ret_date; ?></td>
                                <td width="110"><p><? echo $ret_job; ?></p></td>
                                <td width="110"><p><? echo $ret_order; ?></p></td>
                                <td width="80"><p><? echo $buyer_arr[$ret_buyer]; ?></td>
                                <td width="80"><p>&nbsp;<? echo $count_arr[$ret_count_id]; ?></p></td>
                                <td width="80"><p><? echo $supplier_arr[$ret_supplier]; ?>&nbsp;</p></td>
                                <td width="80"><p><? echo $yarn_type[$ret_type]; ?>&nbsp;</p></td>
                                <td width="80"><? echo $ret_lot; ?>&nbsp;</td>
                                <td width="130"><p>&nbsp;<? echo $ret_booking; ?></p></td>
                                <td width="100" align="right"><p><? //echo $ret_returnable_qty; ?></p></td>
                                <td width="100" align="right"><p><? //echo $ret_qty; ?></p></td>
                                <td width="100" align="right"><? echo number_format($ret_qty,2,'.',''); ?></td>
                                <td width="100" align="right"><? echo number_format($ret_rej_qty,2,'.',''); ?></td>
                                <td align="right"><? //echo number_format($balance_ret,2,'.',''); ?></td>
                            </tr>
							<?
							$i++;
							$ch_ret_qty+=$ret_qty;
							$ch_ret_returnable_qty+=$ret_returnable_qty;
							$ch_ret_rej_qty+=$ret_rej_qty;
							$ch_balance_return_ret+=$balance_return_ret;

							$party_ret_qty+=$ret_qty;
							$party_ret_returnable_qty+=$ret_returnable_qty;
							$party_ret_rej_qty+=$ret_rej_qty;
							$party_balance_return_ret+=$balance_return_ret;

							$grand_ret_qty+=$ret_qty;
							$grand_ret_returnable_qty+=$ret_returnable_qty;
							$grand_ret_rej_qty+=$ret_rej_qty;
							$grand_balance_return_ret+=$balance_return_ret;

						}
					}
				}
				$ch_balance=$ch_issue_qty_tot-($ch_rec_qty+$ch_rec_rej_qty+$ch_ret_qty+$ch_ret_rej_qty);
				$ch_balance_returnable=$ch_returnable_qnty_tot-($ch_ret_qty+$ch_ret_rej_qty);

				$party_balance=$party_issue_qty_tot-($party_rec_qty+$party_rec_rej_qty+$party_ret_qty+$party_ret_rej_qty);
				$party_balance_returnable=$party_returnable_qnty_tot-($party_ret_qty+$party_ret_rej_qty);
				?>
                <tr class="tbl_bottom">
                <td colspan="10" align="right"><b>Challan Total</b></td>
                <td align="right"><? echo number_format($ch_issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($ch_returnable_qnty_tot,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($ch_ret_qty,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($ch_ret_rej_qty,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($ch_balance_returnable,2,'.',''); ?>&nbsp;</td>
            </tr>
            <tr class="tbl_bottom">
                <td colspan="10" align="right">Party Total</td>
                <td align="right"><? echo number_format($party_issue_qty_tot,2); ?></td>
                <td align="right"><? echo number_format($party_returnable_qnty_tot,2); ?></td>
                <td align="right"><? echo number_format($party_ret_qty,2); ?></td>
                <td align="right"><? echo number_format($party_ret_rej_qty,2); ?></td>
                <td align="right"><? echo number_format($party_balance_returnable,2); ?></td>
            </tr>
			<tfoot>
				<th colspan="10" align="right">Total</th>
				<th align="right"><? echo number_format($grand_issue_qty_tot,2); ?></th>
				<th align="right"><? echo number_format($grand_returnable_qnty_tot,2); ?></th>
				<th align="right"><? echo number_format($grand_ret_qty,2); ?></th>
				<th align="right"><? echo number_format($grand_ret_rej_qty,2); ?></th>
				<th align="right"><?
				//$grand_balance=$grand_issue_qty_tot-($grand_rec_qty+$grand_rec_rej_qty+$grand_ret_qty+$grand_ret_rej_qty);
					$grand_returnable_balance=$grand_returnable_qnty_tot-($grand_ret_qty+$grand_ret_rej_qty);
				echo number_format($grand_returnable_balance,2); ?></th>
			</tfoot>
		</table>
	</div>
    </fieldset>



    <?
	}
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
	echo "$total_data####$filename";
	exit();
}

if($action=="report_generate_job")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($db_type==0)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'yyyy-mm-dd');
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'yyyy-mm-dd');
	}
	if($db_type==2)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'','',1);
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'','',1);
	}

	$knitting_company=str_replace("'","",$txt_dyeing_com_id);
	$type=str_replace("'","",$type);
	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and c.job_no_prefix_num in ($job_no) ";//and FIND_IN_SET(c.job_no_prefix_num,'$job_no')

	ob_start();

	$all_party=explode(",",$knitting_company);

	?>
        <fieldset style="width:2030px">
            <table width="2017" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
                <tr>
                   <td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="21" style="font-size:14px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="21" style="font-size:12px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
                </tr>
            </table>
            <br />
            <table width="2017" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="30">SL</th>
                    <th width="70">Date</th>
                    <th width="120">Transaction Ref.</th>
                    <th width="100">Recv. Challan No</th>
                    <th width="100">Issue Challan No</th>
                    <th width="120">Booking/ Req. No</th>
                    <th width="80">Buyer</th>
                    <th width="130">Order Numbers</th>
                    <th width="90">Order Qty.</th>
                    <th width="80">Brand</th>
                    <th width="150">Item Description</th>
                    <th width="80">Lot</th>
                    <th width="50">UOM</th>
                    <th width="100">Grey Issued</th>
                    <th width="100">Returnable Qty.</th>
                    <th width="100">Fabric Received</th>
                    <th width="100">Reject Fabric Received</th>
                    <th width="100">Grey Returned</th>
                    <th width="100">Reject Grey Returned</th>
                    <th width="100">Balance</th>
                    <th width="">Returnable Balanace</th>
                </thead>
            </table>
            <div style="width:2017px; overflow-y: scroll; max-height:380px;" id="scroll_body">
                <table width="2000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<?

				$po_arr=array();
				$datapoArray=sql_select("select id, po_number, po_quantity from wo_po_break_down");

				foreach($datapoArray as $row)
				{
					$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
					$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
				}

				$order_nos_array=array();
				if($db_type==0)
				{
					$datapropArray=sql_select("select trans_id,
						CASE WHEN entry_form='16' and trans_type=2 THEN group_concat(po_breakdown_id) END AS yarn_order_id,
						CASE WHEN entry_form in (2,22) and trans_type=1 THEN group_concat(po_breakdown_id) END AS grey_order_id,
						CASE WHEN entry_form='51' and trans_type=4 THEN group_concat(po_breakdown_id) END AS yarn_return_order_id
						from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (2,16,22,51) and status_active=1 and is_deleted=0 group by trans_id ");
				}
				elseif($db_type==2)
				{
					$datapropArray=sql_select("select trans_id,
						listagg(CASE WHEN entry_form='16' and trans_type=2 THEN  po_breakdown_id END,',') within group (order by po_breakdown_id) AS yarn_order_id,
						listagg(CASE WHEN entry_form in (2,22) and trans_type=1 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) END AS grey_order_id,
						listagg(CASE WHEN entry_form='51' and trans_type=4 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) END AS yarn_return_order_id
						from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (2,16,22,51) and status_active=1 and is_deleted=0 group by trans_id,entry_form,trans_type ");
				}

				foreach($datapropArray as $row)
				{
					$order_nos_array[$row[csf('trans_id')]]['yarn_issue']=$row[csf('yarn_order_id')];
					$order_nos_array[$row[csf('trans_id')]]['grey_recv']=$row[csf('grey_order_id')];
					$order_nos_array[$row[csf('trans_id')]]['yarn_return']=$row[csf('yarn_return_order_id')];
				}

				if (str_replace("'","",$cbo_dyeing_source)==0) $knitting_source_cond_party=""; else $knitting_source_cond_party=" and knit_dye_source=$cbo_dyeing_source";
				if ($knitting_company=='') $knitting_company_cond_party=""; else  $knitting_company_cond_party="  and a.id in ($knitting_company)";
				if ($knitting_company=='') $knitting_company_cond_comp=""; else  $knitting_company_cond_comp="  and id in ($knitting_company)";
				$knit_source=str_replace("'","",$cbo_dyeing_source);
				//echo $cbo_dyeing_source;
				if ($knit_source==3)
				{
					$sql_party="select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and c.supplier_id=b.supplier_id and c.tag_company=$cbo_company_name and b.party_type in(1,9,20,21,24) and a.status_active=1 $knitting_company_cond_party group by a.id, a.supplier_name order by a.supplier_name";
				}
				elseif($knit_source==1)
				{
					$sql_party="select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $knitting_company_cond_comp $company_cond order by comp.company_name";
				}

				$all_party=sql_select($sql_party);


                    $i=1; $balance=0; $tot_iss_qnty=0; $tot_recv_qnty=0; $tot_rej_qnty=0; $tot_ret_qnty=0; $tot_reject_yarn_qnty=0;
					foreach($all_party as $party)//($j=0;$j<=count($all_party)-1;$j++)
					{
						$party_name=$party[csf('id')];

						if($knit_source==1)
							$knitting_party=$company_arr[$party_name];
						else if($knit_source==3)
							$knitting_party=$supplier_arr[$party_name];
						else
							$knitting_party="&nbsp;";

						echo '<tr bgcolor="#EFEFEF"><td colspan="21"><b>Party name: '.$knitting_party.'</b></td></tr>';

						if (str_replace("'","",$txt_challan)=="") $issue_challan_cond=""; else $issue_challan_cond=" and e.challan_no=$txt_challan";

						if ($knit_source==0) $knit_source_cond_party=""; else $knit_source_cond_party=" and e.knit_dye_source=$cbo_dyeing_source";
						if ($party_name=='') $knit_company_cond_party=""; else  $knit_company_cond_party=" and e.knit_dye_company in ($party_name)";

							 if($db_type==0)
							 {
								 $sql_job="select a.id, a.trans_id, a.po_breakdown_id,
									CASE WHEN a.entry_form='16' and a.trans_type=2 THEN group_concat(a.po_breakdown_id) END AS yarn_order_id,
									CASE WHEN a.entry_form in (2,22) and a.trans_type=1 THEN group_concat(a.po_breakdown_id) END AS grey_order_id,
									CASE WHEN a.entry_form='51' and a.trans_type=4 THEN group_concat(a.po_breakdown_id) END AS yarn_return_order_id ,
									b.id, group_concat(distinct(b.po_number)) as po_number, b.po_quantity,
									c.job_no, c.buyer_name, c.style_ref_no,
									d.id as trans_id, d.cons_uom, d.requisition_no, d.brand_id, d.cons_quantity as issue_qnty, d.return_qnty,
									e.issue_number, e.buyer_id, e.booking_id, e.booking_no, e.buyer_id, e.issue_date, e.challan_no, e.issue_basis,
									f.product_name_details, f.lot
									from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_issue_master e, product_details_master f
									where a.trans_id<>0 and a.quantity>0 and a.entry_form in (2,16,22,51) and a.status_active=1 and a.is_deleted=0
									and a.po_breakdown_id=b.id and b.status_active=1
									and b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 and c.company_name=$cbo_company_name
									and a.trans_id=d.id and d.item_category=13 and d.transaction_type=2 and d.status_active=1 and d.is_deleted=0
									and d.mst_id=e.id and e.entry_form in (2,16,22,51) and e.issue_date between '$from_date' and '$to_date' and e.status_active=1 and e.is_deleted=0
									and d.prod_id=f.id $knit_source_cond_party $knit_company_cond_party $job_no_cond $issue_challan_cond
									group by c.job_no";

							 }
							 else if($db_type==2)
							 {
									$sql_job="select  min(a.trans_id) as trans_id ,sum(b.po_quantity) as po_quantity,
									c.job_no, c.buyer_name, c.style_ref_no,
									min(d.cons_uom) as cons_uom, max(d.requisition_no) as requisition_no, min(d.brand_id) as brand_id, sum(d.cons_quantity) as issue_qnty, sum( d.return_qnty) as return_qnty,
									e.issue_number, min(e.booking_id),min(e.booking_no), max(e.issue_date) as issue_date, min(e.challan_no) as challan_no,min(e.issue_basis) as issue_basis,
									f.product_name_details, min(f.lot) as lot ,
									listagg( CASE WHEN a.entry_form='16' and a.trans_type=2 THEN a.po_breakdown_id END,',') within group (order by a.po_breakdown_id)  AS yarn_order_id, 						listagg(CASE WHEN a.entry_form in (2,22) and a.trans_type=1 THEN  a.po_breakdown_id END,',') within group (order by a.po_breakdown_id)  AS grey_order_id,
									listagg(CASE WHEN a.entry_form='51' and a.trans_type=4 THEN a.po_breakdown_id END,',') within group (order by a.po_breakdown_id)  AS yarn_return_order_id,
									b.po_number
									from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_issue_master e, product_details_master f
									where a.trans_id<>0 and a.quantity>0 and a.entry_form in (2,16,22,51) and a.status_active=1 and a.is_deleted=0
									and a.po_breakdown_id=b.id and b.status_active=1
									and b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 and c.company_name=$cbo_company_name
									and a.trans_id=d.id and d.item_category=13 and d.transaction_type=2 and d.status_active=1 and d.is_deleted=0
									and d.mst_id=e.id and e.entry_form in (2,16,22,51) and e.issue_date between '$from_date' and '$to_date' and e.status_active=1 and e.is_deleted=0 and d.prod_id=f.id $knit_source_cond_party $knit_company_cond_party $job_no_cond $issue_challan_cond group by c.job_no, c.buyer_name, c.style_ref_no, b.po_number, f.product_name_details, e.issue_number order by c.job_no, c.buyer_name, c.style_ref_no, b.po_number, f.product_name_details, e.issue_number ";
							 }

						//echo $sql_job;//die;and e.issue_date between '$from_date' and '$to_date'

						$result_job=sql_select($sql_job); $job_array=array();
						foreach($result_job as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							if($row[csf('issue_basis')]==1)
								$booking_reqsn_no=$row[csf('booking_no')];
							else if($row[csf('issue_basis')]==3)
								$booking_reqsn_no=$row[csf('requisition_no')];
							else
								$booking_reqsn_no="&nbsp;";


							$balance=$balance+$row[csf('issue_qnty')];
                    		$tot_iss_qnty=$tot_iss_qnty+$row[csf('issue_qnty')];

							$po_num=$row[csf('po_number')];
							$po_number=implode(",",array_unique(explode(",",$po_num)));


							$order_nos=''; $order_qnty=0;
							if(!in_array($row[csf('job_no')],$job_array))
							{
								if($i!=1)
								{
								?>
									<tr class="tbl_bottom">
                                        <td colspan="8" align="right"><b>Job Total</b></td>
                                        <?
										//$po_qty_tot=0;
										?>
                                        <td align="right"><? echo number_format($po_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right" colspan="4">&nbsp;</td>
                                        <td align="right"><? echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($returnable_qnty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($balance_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($returnable_balance,2,'.',''); ?>&nbsp;</td>
                                    </tr>
							<?
									unset($po_qty_tot);
									unset($issue_qty_tot);
									unset($returnable_qnty_tot);
									unset($balance_qty_tot);
									unset($returnable_balance);
								}
							?>
								<tr><td colspan="21" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php echo $row[csf('job_no')]; ?></b></td></tr>
							<?
								$job_array[$i]=$row[csf('job_no')];
							}

							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="70" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
								<td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
								<td width="100">&nbsp;</td>
								<td width="100"><p>&nbsp;<? echo $row[csf('challan_no')]; ?></p></td>
								<td width="120"><p>&nbsp;<? echo $booking_reqsn_no; ?></p></td>
								<td width="80"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
								<td width="130"><p><? echo $po_number; //$row[csf('po_number')]; ?></p></td>
								<td width="90" align="right"><? echo number_format($row[csf('po_quantity')],0,'.',''); ?>&nbsp;</td>
								<td width="80"><p>&nbsp;<? echo $brand_arr[$row[csf('brand_id')]]; ?></p></td>
								<td width="150"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td width="80"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="50" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
								<td width="100" align="right"><? echo number_format($row[csf('issue_qnty')],2,'.',''); ?>&nbsp;</td>
                                <td width="100" align="right"><? echo number_format($row[csf('return_qnty')],2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right"><? echo number_format($balance,2,'.',''); ?>&nbsp;</td>
                                <td align="right"><? $return_balance=$row[csf('return_qnty')]; echo number_format($return_balance,2,'.',''); ?>&nbsp;</td>
							</tr>
						<?
							$po_qty_tot+=$row[csf('po_quantity')];
							$issue_qty_tot+=$row[csf('issue_qnty')];
							$balance_qty_tot+=$balance;

							$returnable_qnty_tot+=$row[csf('return_qnty')];
							$tot_returnable_qnty+=$row[csf('return_qnty')];
							$returnable_balance+=$row[csf('return_qnty')];
							$tot_returnable_balance+=$row[csf('return_qnty')];
							$i++;
						}

						if (str_replace("'","",$txt_challan)=="") $recissue_challan_cond=""; else $recissue_challan_cond=" and a.yarn_issue_challan_no=$txt_challan";

						//echo $query="select a.recv_number, a.buyer_id, a.booking_no, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, b.id as trans_id, b.cons_uom,  b.brand_id, b.cons_quantity, b.cons_reject_qnty, c.product_name_details, c.lot from inv_receive_master a, inv_transaction b, product_details_master c, order_wise_pro_details d, wo_po_break_down e where b.id=d.trans_id and d.trans_type in (1,4) and d.entry_form in (2,22,9) and d.po_breakdown_id=e.id and a.item_category in(1,13) and a.entry_form in(2,22,9) and a.company_id=$cbo_company_name and a.knitting_source=$cbo_dyeing_source and a.knitting_company=$party_name  and a.id=b.mst_id and b.item_category in(1,13) and b.transaction_type in(1,4) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $recissue_challan_cond order by b.transaction_type, a.receive_date";//and a.receive_date between '$from_date' and '$to_date'

						if ($knit_source==0) $knit_source_cond_party_rec=""; else $knit_source_cond_party_rec=" and e.knitting_source in ($knit_source)";
						if ($party_name=='') $knit_company_cond_party_rec=""; else  $knit_company_cond_party_rec=" and e.knitting_company in ($party_name)";

						if($db_type==0)
						{
							$query="select a.trans_id, b.po_number, sum(b.po_quantity) as po_quantity,
								c.job_no, c.buyer_name, c.style_ref_no,
								d.cons_uom,d.requisition_no, d.brand_id, sum(d.cons_quantity) as receive_qnty, sum( d.return_qnty) as return_qnty, sum(d.cons_quantity) as cons_quantity, sum(d.cons_reject_qnty) as cons_reject_qnty,
								e.booking_id, max(e.receive_date) as receive_date, e.challan_no, e.receive_basis, e.recv_number,
								group_concat(e.yarn_issue_challan_no) as yarn_issue_challan_no,
								group_concat(e.item_category) as item_category,
								group_concat(e.booking_no) as booking_no,
								f.product_name_details,
								group_concat(f.lot) as lot,
								CASE WHEN a.entry_form in (2,22) and a.trans_type=1 THEN  group_concat(a.po_breakdown_id) END  AS grey_order_id,
								CASE WHEN a.entry_form='51' and a.trans_type=4 THEN group_concat(a.po_breakdown_id) END  AS yarn_return_order_id
								from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_receive_master e, product_details_master f
								where a.trans_id<>0 and a.quantity>0 and a.entry_form in (2,22,51) and a.status_active=1 and a.is_deleted=0
								and a.po_breakdown_id=b.id and b.status_active=1
								and b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 and c.company_name=$cbo_company_name
								and a.trans_id=d.id and d.item_category=1 and d.transaction_type in (1,4) and d.status_active=1 and d.is_deleted=0
								and d.mst_id=e.id and e.entry_form in (2,22,51) and e.receive_date between '$from_date' and '$to_date' and e.status_active=1 and e.is_deleted=0 and d.prod_id=f.id $knit_source_cond_party_rec $knit_company_cond_party_rec $job_no_cond $recissue_challan_cond group by c.job_no, c.buyer_name, c.style_ref_no, b.po_number, f.product_name_details, e.recv_number, e.item_category order by c.job_no, c.buyer_name, c.style_ref_no, b.po_number, f.product_name_details, e.recv_number";
						}
						elseif($db_type==2)
						{
							$query="select  min(a.trans_id) as trans_id, b.po_number, sum(b.po_quantity) as po_quantity,
								c.job_no, c.buyer_name, c.style_ref_no,
								min(d.cons_uom) as cons_uom, max(d.requisition_no) as requisition_no, min(d.brand_id) as brand_id, sum(d.cons_quantity) as receive_qnty, sum(d.cons_quantity) as cons_quantity, sum(d.cons_reject_qnty) as cons_reject_qnty, sum( d.return_qnty) as return_qnty,
								min(e.booking_id), max(e.receive_date) as receive_date, min(e.challan_no) as challan_no, min(e.receive_basis) as receive_basis, e.recv_number,
								listagg(CAST(e.yarn_issue_challan_no as varchar2(4000)),',') within group (order by e.yarn_issue_challan_no) as yarn_issue_challan_no,
								listagg(CAST(e.item_category as varchar2(4000)),',') within group (order by e.item_category) as item_category,
								listagg(CAST(e.booking_no as varchar2(4000)),',') within group (order by e.booking_no) as booking_no, e.item_category,
								f.product_name_details,
								listagg(CAST(f.lot as varchar2(4000)),',') within group (order by f.lot) as lot,
								listagg(CASE WHEN a.entry_form in (2,22) and a.trans_type=1 THEN  a.po_breakdown_id END,',') within group (order by a.po_breakdown_id)  AS grey_order_id,
								listagg(CASE WHEN a.entry_form='51' and a.trans_type=4 THEN a.po_breakdown_id END,',') within group (order by a.po_breakdown_id)  AS yarn_return_order_id
								from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_receive_master e, product_details_master f
								where a.trans_id<>0 and a.quantity>0 and a.entry_form in (2,22,51) and a.status_active=1 and a.is_deleted=0
								and a.po_breakdown_id=b.id and b.status_active=1
								and b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 and c.company_name=$cbo_company_name
								and a.trans_id=d.id and d.item_category=1 and d.transaction_type in (1,4) and d.status_active=1 and d.is_deleted=0
								and d.mst_id=e.id and e.entry_form in (2,22,51) and e.receive_date between '$from_date' and '$to_date' and e.status_active=1 and e.is_deleted=0 and d.prod_id=f.id $knit_source_cond_party_rec $knit_company_cond_party_rec $job_no_cond $recissue_challan_cond group by c.job_no, c.buyer_name, c.style_ref_no, b.po_number, f.product_name_details, e.recv_number, e.item_category order by c.job_no, c.buyer_name, c.style_ref_no, b.po_number, f.product_name_details, e.recv_number";
						}
						$result2=sql_select($query); //$job_rec_array=array();
						foreach($result2 as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							if($row[csf('item_category')]==13)
							{
								$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['grey_recv']);
								$tot_recv_qnty+=$row[csf('cons_quantity')];
								$tot_rej_qnty+=$row[csf('cons_reject_qnty')];
								$balance=$balance-($row[csf('cons_quantity')]+$row[csf('cons_reject_qnty')]);
							}
							else
							{
								$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['yarn_return']);
								$tot_ret_qnty+=$row[csf('cons_quantity')];
								$tot_reject_yarn_qnty+=$row[csf('cons_reject_qnty')];
								$balance=$balance-($row[csf('cons_quantity')]+$row[csf('cons_reject_qnty')]);
							}

							$order_nos=''; $order_qnty=0;
							foreach($all_po_id as $po_id)
							{
								if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
								$order_qnty+=$po_arr[$po_id]['qnty'];
							}
							$po_number=implode(",",array_unique(explode(",",$row[csf('po_number')])));
							$returnable_tot+=$row[csf('return_qnty')];
							$grand_tot_balance+=$row[csf('cons_quantity')]+$row[csf('cons_reject_qnty')];

							if(!in_array($row[csf('job_no')],$job_rec_array))
							{
								if($i!=1)
								{
								?>
									<tr class="tbl_bottom">
                                        <td colspan="8" align="right"><b>Job Total</b></td>
                                        <?
										//$po_qty_tot=0;
										?>
                                        <td align="right"><? //echo number_format($po_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right" colspan="4">&nbsp;</td>
                                        <td align="right"><? echo number_format($returnable_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($receive_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($balance_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($tot_returnable_balance,2,'.',''); ?>&nbsp;</td>
                                    </tr>
								<?
									unset($po_qty_tot);
									unset($receive_qty_tot);
									unset($returnable_tot);
									unset($balance_qty_tot);
									unset($tot_returnable_balance);
								}
							?>
								<tr><td colspan="21" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php echo $row[csf('job_no')]; ?></b></td></tr>
							<?
								$job_rec_array[$i]=$row[csf('job_no')];
							}
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="100"><p>&nbsp;<? echo $row[csf('challan_no')]; ?></p></td>
								<td width="100"><p>&nbsp;<? echo $row[csf('yarn_issue_challan_no')]; ?></p></td>
								<td width="120"><p>&nbsp;<? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
								<td width="130"><p><? echo $po_number; ?>&nbsp;</p></td>
								<td width="90" align="right"><? echo number_format($row[csf('po_quantity')],0,'.',''); ?>&nbsp;</td>
								<td width="80"><p>&nbsp;<? echo $brand_arr[$row[csf('brand_id')]]; ?></p></td>
								<td width="150"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td width="80"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="50" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
                                <td width="100" align="right"><? echo number_format($row[csf('return_qnty')],2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right"><? if($row[csf('item_category')]==13) echo number_format($row[csf('cons_quantity')],2,'.',''); $fab_rec_tot=$row[csf('cons_quantity')]; ?>&nbsp;</td>
								<td width="100" align="right"><? if($row[csf('item_category')]==13) echo number_format($row[csf('cons_reject_qnty')],2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right"><? if($row[csf('item_category')]==1) echo number_format($row[csf('cons_quantity')],2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right"><? if($row[csf('item_category')]==1) echo number_format($row[csf('cons_reject_qnty')],2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right"><? echo number_format($balance,2,'.',''); ?>&nbsp;</td>
                                <td align="right"><? $return_balance=$row[csf('return_qnty')]-$row[csf('cons_quantity')]; echo number_format($return_balance,2,'.',''); ?>&nbsp;</td>
							</tr>
						<?
							$po_qty_tot+=$row[csf('po_quantity')];
							$receive_qty_tot+=$row[csf('receive_qnty')];
							$balance_qty_tot+=$balance;
							$tot_returnable_qnty+=$row[csf('return_qnty')];
							$returnable_balance+=$row[csf('return_qnty')]-$row[csf('cons_quantity')];
							$tot_returnable_balance+=$row[csf('return_qnty')]-$row[csf('cons_quantity')];
							$grand_tot_balance+=$balance_qty_tot;
							$i++;
						}
					}
                    ?>
                        <tr class="tbl_bottom">
                            <td colspan="8" align="right"><b>Job Total</b></td>
                            <?
							unset($tot_returnable_balance);
                            //$po_qty_tot=0;
                            ?>
                            <td align="right"><? //echo number_format($po_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right" colspan="4">&nbsp;</td>
                            <td align="right"><? echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($returnable_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($balance_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($tot_returnable_balance,2,'.',''); ?>&nbsp;</td>
                        </tr>
                    <tfoot>
                        <th colspan="13" align="right">Total</th>
                        <th align="right"><? echo number_format($tot_iss_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_returnable_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_recv_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_rej_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_ret_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_reject_yarn_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_iss_qnty-($tot_recv_qnty+$tot_rej_qnty+$tot_ret_qnty+$tot_reject_yarn_qnty),2); ?></th>
                        <th align="right"><? echo number_format($tot_returnable_qnty-$tot_ret_qnty,2); ?></th>
                    </tfoot>
                </table>
            </div>
        </fieldset>
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
	echo "$total_data####$filename";
	exit();
}


if($action=="print_button_variable_setting")
{
	$print_report_format=0;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=20 and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();
}



?>
