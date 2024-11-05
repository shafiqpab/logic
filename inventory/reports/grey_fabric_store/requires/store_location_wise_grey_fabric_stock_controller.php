<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'store_location_wise_grey_fabric_stock_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","620","270",0, $sql , "js_set_value", "id,job_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
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
	$cbo_company_id=trim(str_replace("'","",$cbo_company_id));
	$cbo_year=trim(str_replace("'","",$cbo_year));
	$txt_job_no=trim(str_replace("'","",$txt_job_no));
	$txt_job_id=trim(str_replace("'","",$txt_job_id));
	$txt_file_no=trim(str_replace("'","",$txt_file_no));
	$txt_ref_no=trim(str_replace("'","",$txt_ref_no));
	$txt_prog_no=trim(str_replace("'","",$txt_prog_no));
	
	$constuction_arr=array(); $composition_arr=array(); $type_array=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach($data_array as $row )
		{
			/*if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}*/
			
			$constuction_arr[$row[csf('id')]]=$row[csf('construction')];
			
		}
	}
	$str_cond="";
	
	if ($txt_job_no!="")
	{
		$txt_job_no_arr=explode(",",$txt_job_no);
		$all_job_no="";
		foreach($txt_job_no_arr as $job_no)
		{
			$all_job_no.="'".$job_no."',";
		}
		$all_job_no=chop($all_job_no,",");
		$str_cond=" and d.job_no_mst in ($all_job_no) ";
	}
	if($cbo_year!=0)
	{
		if($db_type==0)
		{
			$str_cond.=" and year(d.insert_date)=$cbo_year";
		}
		else
		{
			$str_cond.=" and TO_CHAR(d.insert_date,'YYYY')=$cbo_year";
		}
	}
	
	if($txt_file_no!="") $str_cond.=" and d.file_no LIKE '".$txt_file_no."%'";
	if($txt_ref_no!="") $str_cond.=" and d.grouping LIKE '".$txt_ref_no."%'";
	if($txt_prog_no!="") $str_cond.=" and a.booking_id LIKE '".$txt_prog_no."%'";
	
	//echo $str_cond.jahid;die;
	
	$plan_sql=sql_select("select b.id, b.machine_dia, b.machine_gg, b.program_qnty from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id");
	$plan_data_arr=array();
	foreach($plan_sql as $row)
	{
		$plan_data_arr[$row[csf("id")]]["machine_dia"]=$row[csf("machine_dia")];
		$plan_data_arr[$row[csf("id")]]["machine_gg"]=$row[csf("machine_gg")];
		$plan_data_arr[$row[csf("id")]]["program_qnty"]=$row[csf("program_qnty")];
	}
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
	$rcv_store_arr=return_library_array( "select b.grey_sys_id, a.store_id from inv_receive_master a, pro_grey_prod_delivery_dtls b where a.booking_id=b.mst_id and a.entry_form=58 and b.entry_form=56", "grey_sys_id", "store_id"  );
	
	$sql_barcode=sql_select("select barcode_no, sum(case when entry_form=58 then qnty else 0 end) as rcv_qnty, sum(case when entry_form=82 then qnty else 0 end) as transfer_qnty from pro_roll_details where entry_form in(58,82) and barcode_no>0 and status_active=1 and is_deleted=0  group by barcode_no");
	$barcode_data_arr=array();
	foreach($sql_barcode as $row)
	{
		$barcode_data_arr[$row[csf('barcode_no')]]['rcv_qnty']=$row[csf("rcv_qnty")];
		$barcode_data_arr[$row[csf('barcode_no')]]['transfer_qnty']=$row[csf("transfer_qnty")];
	}
	//var_dump($barcode_data_arr[$row[csf('barcode_no')]]);die;
	//echo $str_cond.jahid;die;
	if($db_type==0)
	{
		$sql="select a.id as mst_id, a.booking_id, a.buyer_id, a.store_id, a.location_id, d.file_no, d.grouping as ref_no, b.febric_description_id, group_concat(b.gsm) as fin_gsm, group_concat(b.width) as fin_dia, group_concat(b.machine_no_id) as machine_no_id,  group_concat(b.stitch_length) as stitch_length,  group_concat(b.color_range_id) as color_range_id,  group_concat(b.color_id) as color_id, group_concat(c.barcode_no) as barcode_no, sum(c.qnty) as rcv_qnty
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d
			where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.receive_basis=2 and a.company_id=$cbo_company_id and a.entry_form=2 and c.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $str_cond
			group by a.id, a.booking_id, a.buyer_id, a.store_id, a.location_id, d.file_no, d.grouping, b.febric_description_id";
	}
	else
	{
		$sql="select a.id as mst_id, a.booking_id, a.buyer_id, a.store_id, a.location_id, d.file_no, d.grouping as ref_no, b.febric_description_id, LISTAGG(CAST(b.gsm AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.gsm) as fin_gsm, LISTAGG(CAST(b.width AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.width) as fin_dia, LISTAGG(CAST(b.machine_no_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.machine_no_id) as machine_no_id, LISTAGG(CAST(b.stitch_length AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.stitch_length) as stitch_length, LISTAGG(CAST(b.color_range_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.color_range_id) as color_range_id, LISTAGG(CAST(b.color_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.color_id) as color_id, LISTAGG(CAST(c.barcode_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.barcode_no) as barcode_no, sum(c.qnty) as rcv_qnty
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d
			where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.receive_basis=2 and a.company_id=$cbo_company_id and a.entry_form=2 and c.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $str_cond
			group by a.id, a.booking_id, a.buyer_id, a.store_id, a.location_id, d.file_no, d.grouping, b.febric_description_id";
	}
	//echo $sql;//die;
	
	$sql_result=sql_select($sql);
	ob_start();
	?>
    <div style="width:1320px;" >
	<fieldset style="width:1320px">
		<table cellpadding="0" cellspacing="0" width="1300">
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="17" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="17" style="font-size:16px"><strong><? 
			   $com_name=return_field_value("company_name","lib_company","id=".$cbo_company_id,"company_name");
			   echo $com_name; ?></strong></td>
			</tr>
		</table>
		<table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
			<thead>
                <th width="30">SL</th>
                <th width="60">Buyer Name</th>
                <th width="50">File No</th>
                <th width="50">Ref. No</th>
                <th width="50">Prog. No</th>
                <th width="110">Construction</th>
                <th width="70">M/C NO.</th>
                <th width="60">MC Dia and Gauge</th>
                <th width="60">F/Dia</th>
                <th width="60">S. Length</th>
                <th width="120">Color Range</th>
                <th width="120">Color</th>
                <th width="80">Program Qnty</th>
                <th width="120">Store Location</th>
                <th width="80">Rcvd. Qty.</th>
                <th width="80">Store To Store Transfer Qty</th>
                <th>Store Balance Qty.</th>
			</thead>
		</table>
		<div style="width:1320px; overflow-y: scroll; max-height:250px;" id="scroll_body">
			<table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
                <tbody> 
                <? 
				$i=1; $tot_recv_qty=0; $tot_iss_qty=0; $tot_stock_qnty=0;
				foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					
					$unique_barcode=array_unique(explode(",",$row[csf("barcode_no")]));
					$plan_barcode=implode(",",$unique_barcode);
					$rcv_qnty=$transfer_qnty=0;
					foreach($unique_barcode as $barcodo_no)
					{
						$rcv_qnty+=$barcode_data_arr[$barcodo_no]["rcv_qnty"];
						$transfer_qnty+=$barcode_data_arr[$barcodo_no]["transfer_qnty"];
						//var_dump($barcode_data_arr[$barcodo_no]);die;
					}
					//echo $row[csf('barcode_no')];
					 
					$rcv_balance=$rcv_qnty-$transfer_qnty;
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="60"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
						<td width="50" align="center"><p><? echo $row[csf("file_no")]; ?>&nbsp;</p></td>
						<td width="50" align="center"><p><? echo $row[csf("ref_no")]; ?>&nbsp;</p></td>
						<td width="50" align="center"><p><? echo $row[csf("booking_id")]; ?>&nbsp;</p></td>
						<td width="110"><p><? echo $constuction_arr[$row[csf("febric_description_id")]]; ?>&nbsp;</p></td>
						<td width="70"><p>
						<?
						$all_machine_no= array_unique(explode(",",$row[csf("machine_no_id")]));
						$all_machine="";
						foreach($all_machine_no as $machince_id)
						{
							$all_machine.=$machine_arr[$machince_id].",";
						}
						$all_machine=chop($all_machine,",");
						echo $all_machine; 
						?>&nbsp;</p></td>
						<td width="60"><p><? echo $plan_data_arr[$row[csf("booking_id")]]["machine_dia"]."X".$plan_data_arr[$row[csf("booking_id")]]["machine_gg"]; ?>&nbsp;</p></td>
						<td width="60" align="center"><p><? echo implode(",",array_unique(explode(",",$row[csf("fin_dia")]))); ?>&nbsp;</p></td>
						<td width="60" align="center"><p><? echo implode(",",array_unique(explode(",",$row[csf("stitch_length")]))); ?>&nbsp;</p></td>
						<td width="120"><p>
						<?
						$color_range_arr= array_unique(explode(",",$row[csf("color_range_id")]));
						$all_color_range="";
						foreach($color_range_arr as $color_range_id)
						{
							$all_color_range.=$color_range[$color_range_id].",";
						}
						$all_color_range=chop($all_color_range,",");
						echo $all_color_range; 
						?>&nbsp;</p></td>
						<td width="120"><p>
						<? 
						$color_id_arr= array_unique(explode(",",$row[csf("color_id")]));
						$all_color="";
						foreach($color_id_arr as $color_id)
						{
							$all_color.=$color_arr[$color_id].",";
						}
						$all_color=chop($all_color,",");
						echo $all_color; 
						?>&nbsp;</p></td>
						<td width="80" align="right"><p><? echo number_format($plan_data_arr[$row[csf("booking_id")]]["program_qnty"],2); ?>&nbsp;</p></td>
						<td width="120"><p><? echo $store_arr[$rcv_store_arr[$row[csf("mst_id")]]]; ?>&nbsp;</p></td>
						<td width="80" align="right"><a href="##" onClick="openpage('recv_popup','510px','<? echo $plan_barcode; ?>')"><? echo number_format($rcv_qnty,2,'.',''); ?></a></td>
						<td width="80" align="right"><a href="##" onClick="openpage('transfer_popup','1010px','<? echo $plan_barcode; ?>')"><? echo number_format($transfer_qnty,2,'.',''); ?></a></td>
						<td align="right"><? echo number_format($rcv_balance,2,'.',''); ?></td>
					</tr>
					<?	
					$i++;
					$total_porg_qnty+=$plan_data_arr[$row[csf("booking_id")]]["program_qnty"];
					$tot_recv_qty+=$rcv_qnty; 
					$tot_transfer_qnty+=$transfer_qnty; 
					$tot_rcv_balance+=$rcv_balance;
				}
				?>
                </tbody>
			</table>
		</div>     
		<table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">               
            <tfoot>
                <tr>
                	<th width="30">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="80" align="right" id="value_total_porg_qnty"><? //echo number_format($total_porg_qnty,2);?></th>
                    <th width="120" align="right">Total:</th>
                    <th width="80" align="right" id="value_tot_recv_qty"><? echo number_format($tot_recv_qty,2);?></th>
                    <th width="80" align="right" id="value_tot_transfer_qnty"><? echo number_format($tot_transfer_qnty,2);?></th>
                    <th  align="right" id="value_tot_rcv_balance"><? echo number_format($tot_rcv_balance,2);?></th>
                </tr>
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
    echo "$html####$filename"; 
    exit();
}

if($action=="recv_popup")
{
 	echo load_html_head_contents("Receive Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
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
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$('#tbl_list_search tbody tr:first').hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			$('#tbl_list_search tbody tr:first').show();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="280px";
		}
	</script>
    <div style="width:500px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <div style="width:500px;" id="report_container">
	<fieldset style="width:500px">
    	<table width="480" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
			<thead>
                <th width="40">SL No.</th>
                <th width="150">Delivery Challan No.</th>
                <th width="80">Roll No</th>
                <th width="100">Barcode No</th>
                <th >Roll Qty</th>
			</thead>
        </table>
        <div style="width:500px; max-height:280px; overflow-y:scroll" id="scroll_body" align="left">	 
        <table width="480" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="tbl_list_search" >  
            <tbody>
			<?
				$i=0; $tot_grey_qnty=0;
                $sql="select a.recv_number, c.barcode_no, c.roll_no, c.qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(58) and c.entry_form in(58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data) order by c.barcode_no";
                //echo $sql;//die;
                $result= sql_select($sql);
                foreach($result as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40" align="center"><? echo $i; ?></td>
                        <td width="150"><p><? echo $row[csf('recv_number')]; ?>&nbsp;</p></td>
                        <td width="80" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
                        <td width="100" align="center"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
                    </tr>
                <? 
					$tot_grey_qnty+=$row[csf('qnty')];
                } 
            ?>
             </tbody>
         </table>
         </div>
    	<table width="480" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
            <tfoot>
                <th colspan="3" align="right">Roll Total :</th>
                <th width="104" align="right"><? echo number_format($tot_grey_qnty,2); ?></th>
            </tfoot>
        </table>
    </fieldset>
   	</div>
<?
exit();
}

if($action=="transfer_popup")
{
 	echo load_html_head_contents("Receive Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
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
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$('#tbl_list_search tbody tr:first').hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			$('#tbl_list_search tbody tr:first').show();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="280px";
		}
	</script>
    <div style="width:1000px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <div style="width:1000px;" id="report_container">
	<fieldset style="width:1000px">
    	<table width="980" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
			<thead>
                <th width="40">SL No.</th>
                <th width="140">Form Store.</th>
                <th width="140">To store</th>
                <th width="120">Trans challan No</th>
                <th width="80">Challan Date</th>
                <th width="110">Delivery Challan No</th>
                <th width="70">Program No</th>
                <th width="70">Roll No</th>
                <th width="90">Barcode No</th>
                <th>Roll Qty</th>
			</thead>
        </table>
        <div style="width:1000px; max-height:280px; overflow-y:scroll" id="scroll_body" align="left">	 
        <table width="980" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="tbl_list_search" >  
            <tbody>
			<?
				$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );
				$delivery_sql=sql_select("select a.id, a.sys_number, c.booking_id from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, inv_receive_master c where a.id=b.mst_id and b.grey_sys_id=c.id and a.entry_form=56 and b.entry_form=56 and c.entry_form=2 and c.receive_basis=2");
				$delivery_data_arr=array();
				foreach($delivery_sql as $row)
                {
					$delivery_data_arr[$row[csf("id")]]["sys_number"]=$row[csf("sys_number")];
					$delivery_data_arr[$row[csf("id")]]["booking_id"]=$row[csf("booking_id")];
				}
				$i=0; $tot_grey_qnty=0;
                $sql="select a.transfer_system_id, a.transfer_date, b.from_store, b.to_store, b.knit_program_id as challan_id, c.barcode_no, c.roll_no, c.qnty 
				from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data) order by c.barcode_no";
                //echo $sql;die;
                $result= sql_select($sql);
                foreach($result as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40" align="center"><? echo $i; ?></td>
                        <td width="140"><p><? echo $store_arr[$row[csf('from_store')]]; ?>&nbsp;</p></td>
                        <td width="140"><p><? echo $store_arr[$row[csf('to_store')]]; ?>&nbsp;</p></td>
                        <td width="120"><p><? echo $row[csf('transfer_system_id')]; ?>&nbsp;</p></td>
                        <td width="80" align="center"><p><? if($row[csf('transfer_date')]!="" && $row[csf('transfer_date')]!="0000-00-00") echo change_date_format($row[csf('transfer_date')]); ?>&nbsp;</p></td>
                        <td width="110" align="center"><p><? echo $delivery_data_arr[$row[csf("challan_id")]]["sys_number"]; ?>&nbsp;</p></td>
                        <td width="70" align="center"><p><? echo $delivery_data_arr[$row[csf("challan_id")]]["booking_id"]; ?>&nbsp;</p></td>
                        <td width="70" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
                        <td width="90" align="center"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
                    </tr>
                <? 
					$tot_grey_qnty+=$row[csf('qnty')];
                } 
            ?>
             </tbody>
         </table>
         </div>
    	<table width="980" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
            <tfoot>
                <th colspan="9" align="right">Roll Total :</th>
                <th width="109"  align="right"><? echo number_format($tot_grey_qnty,2); ?></th>
            </tfoot>
        </table>
    </fieldset>
   	</div>
<?
exit();
}



?>

