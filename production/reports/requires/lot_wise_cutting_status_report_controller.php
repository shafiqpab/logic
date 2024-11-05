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
	echo create_drop_down( "cbo_buyer_name", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );//load_drop_down( 'requires/daily_knitting_production_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );$location_cond
  exit();	 
}
if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";
//item style------------------------------//

if($action=="job_popup")
{
	echo load_html_head_contents("Search Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;var selected_style = new Array;var selected_id_arr = new Array;
		
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
				selected_style.push( str[3] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_style.splice( i, 1 );
			}
			var id = ''; var name = '';var style = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				style += selected_style[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			style = style.substr( 0, style.length - 1 );
			
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
			$('#hide_style_no').val( style );
		}
	
    </script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:710px;">
	            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th class="must_entry_caption">Company Name</th>
	                    <th>Buyer</th>
	                    <th>Year</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="100">Job No</th>
	                    <th>
                            <input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"> 
                            <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                            <input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
                        </th>
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <? 
									echo create_drop_down( "cbo_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'lot_wise_cutting_status_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
								?>
	                        </td>
	                        <td align="center" id="buyer_td">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_name $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>                  
	                        <td align="center">	
	                    	<?	
	                    		$date = date('Y');					
								echo create_drop_down( "cbo_year", 110, $year,"",1, "--Select--", $date,'',0 );
							?>
	                        </td>                 
	                        <td align="center">	
	                    	<?
	                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>     
	                        <td align="center" id="search_by_td">				
	                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
	                        </td> 
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value, 'search_list_view', 'search_div', 'lot_wise_cutting_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
	                    	</td>
	                    </tr>
	            	</tbody>
	           	</table>
	            <div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>        
	<script type="text/javascript">
		// $("#cbo_year").val('<?=$cbo_year;?>');
	</script>   
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}

if($action=="search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	
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
	if(str_replace("'", "", $data[3])!="")
	{
		$search_string="".trim($data[3])."";
	}

	$search_cond="";
	if($search_by==1 && $search_string!="") 
		$search_cond=" and a.job_no_prefix_num='$search_string'"; 
	else if($search_by==2 && $search_string!="") 
		$search_cond="and a.style_ref_no like '%$search_string%'";
	
	// if($search_string!="")	{$search_cond=" and $search_field='$search_string'";}
	$job_year =$data[4];
	
	if($job_year!=0)
	{
		if($db_type==0)
		{
			$job_year_cond=" and year(a.insert_date)='$job_year'";
		}
		else
		{
			$job_year_cond=" and to_char(a.insert_date,'YYYY')='$job_year'";	
		}
	}
	else
	{
		$job_year_cond="";
	}
	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	
	$sql= "SELECT a.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $job_no_cond $job_year_cond group by a.id,
         a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no order by a.id desc"; 
    // echo $sql;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "100,100,50,100","550","220",0, $sql , "js_set_value", "id,job_no,style_ref_no","",1,"company_name,buyer_name,0,0,0,0",$arr,"company_name,buyer_name,year,job_no,style_ref_no","",'','0,0,0,0,0','',1) ;
   exit(); 
}


if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_name 		= str_replace("'","",$cbo_company_name);
	$wo_company_name 	= str_replace("'","",$cbo_wo_company_name);
	$buyer_name 		= str_replace("'","",$cbo_buyer_name);
	$hidden_job_id 		= str_replace("'","",$hidden_job_id);
	$cutting_date 		= str_replace("'","",$txt_date);
	
	// ========================= lay cond ========================
    $dyeing_process_cond = "";
    if($company_name != 0){
        $sql_lay_cond .= " and a.company_name=$company_name";
        $dyeing_process_cond .= " and f.company_id=$company_name";
    }
	$sql_lay_cond .= ($buyer_name != 0) 		? " and a.buyer_name=$buyer_name" : "";
	$sql_lay_cond .= ($hidden_job_id != "") 	? " and a.id in($hidden_job_id)" : "";
    if($wo_company_name != 0){
        $sql_lay_cond .= " and c.working_company_id in($wo_company_name)";
//        $dyeing_process_cond .= " and f.service_company in ($wo_company_name)";
    }

	if($cutting_date !="")
    {
        if($db_type==0)
        {
            $start_date=change_date_format($cutting_date,"yyyy-mm-dd","");
        }
        else
        {
            $start_date=date("j-M-Y",strtotime($cutting_date));
        }
        $sql_lay_cond.= " and c.entry_date='$start_date'";
    }

	$company_lib=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$location_lib=return_library_array( "SELECT id, location_name from lib_location", "id", "location_name"  );
	$floor_lib=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );
    $season_lib = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", "id", "season_name");
    $buyer_lib  = return_library_array("select id, buyer_name from  lib_buyer", "id", "buyer_name");
	$color_lib=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_lib=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$lineArr = return_library_array("select a.id,a.line_name from lib_sewing_line a","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	// echo $date = date('d-m-Y H:i a',strtotime($start_date.' 11:20'));
	// echo date('H A',strtotime($date));
		
	/*==========================================================================================/
	/										getting lay  data 									/
	/==========================================================================================*/ 	
		
	$sql=" SELECT a.id as job_id, b.id as po_id, a.job_no,a.buyer_name,a.style_ref_no as style,a.client_id,a.season_buyer_wise as season,b.po_number,d.order_cut_no as cut_no,d.color_id,d.gmt_item_id as item_id,c.entry_date,c.cutting_no, f.shade,
		e.size_qty as total_lay,e.barcode_no,e.order_id,e.id as bndle_id, case when f.batch_no is null then h.batch_no else null end as lot_no
		from wo_po_details_master a,wo_po_break_down b,ppl_cut_lay_mst c,ppl_cut_lay_dtls d, ppl_cut_lay_bundle e,pro_roll_details f left join pro_batch_create_mst h on h.id = f.is_extra_roll where a.job_no=c.job_no and a.id=b.job_id and c.id=d.mst_id and b.id=e.order_id and c.id=e.mst_id and d.id=e.dtls_id and e.roll_id=f.id and f.entry_form=99 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and e.size_qty>0  $sql_lay_cond";
    //echo $sql; //die;
	$sql_res = sql_select($sql);
    if(count($sql_res)==0)
    {
        ?>
        <center>
            <div style="width: 80%;" class="alert alert-danger">Data not found.Please try again.</div>
        </center>
        <?
        die();
    }

	$data_array = array();
	$bcode_wise_data_array = array();
	$cutting_array = array(); $booking_po = []; $process_batch = [];
	foreach ($sql_res as  $val)
	{
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['SEASON']][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['COLOR_ID']][$val['LOT_NO']][$val['CUT_NO']][$val['CUTTING_NO']]['style'] = $val['STYLE'];
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['SEASON']][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['COLOR_ID']][$val['LOT_NO']][$val['CUT_NO']][$val['CUTTING_NO']]['buyer_name'] = $val['BUYER_NAME'];
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['SEASON']][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['COLOR_ID']][$val['LOT_NO']][$val['CUT_NO']][$val['CUTTING_NO']]['client_id'] = $val['CLIENT_ID'];
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['SEASON']][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['COLOR_ID']][$val['LOT_NO']][$val['CUT_NO']][$val['CUTTING_NO']]['item_id'] = $val['ITEM_ID'];
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['SEASON']][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['COLOR_ID']][$val['LOT_NO']][$val['CUT_NO']][$val['CUTTING_NO']]['entry_date'] = $val['ENTRY_DATE'];
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['SEASON']][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['COLOR_ID']][$val['LOT_NO']][$val['CUT_NO']][$val['CUTTING_NO']]['po_number'] .= $val['PO_NUMBER'].",";
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['SEASON']][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['COLOR_ID']][$val['LOT_NO']][$val['CUT_NO']][$val['CUTTING_NO']]['lot_id'] = $val['LOT_ID'];
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['SEASON']][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['COLOR_ID']][$val['LOT_NO']][$val['CUT_NO']][$val['CUTTING_NO']]['barcode_no'] .= $val['BARCODE_NO'].",";
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['SEASON']][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['COLOR_ID']][$val['LOT_NO']][$val['CUT_NO']][$val['CUTTING_NO']]['shade'][$val['SHADE']] = $val['SHADE'];

		
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['SEASON']][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['COLOR_ID']][$val['LOT_NO']][$val['CUT_NO']][$val['CUTTING_NO']]['total_prod'] += $val['TOTAL_LAY'];

		$bcode_wise_data_array[$val['BARCODE_NO']]['job_no'] = $val['JOB_NO'];
		$bcode_wise_data_array[$val['BARCODE_NO']]['item_id'] = $val['ITEM_ID'];
		$bcode_wise_data_array[$val['BARCODE_NO']]['color_id'] = $val['COLOR_ID'];
		$bcode_wise_data_array[$val['BARCODE_NO']]['lot'] = $val['LOT_ID'];
		$bcode_wise_data_array[$val['BARCODE_NO']]['cut_no'] = $val['CUT_NO'];
		$bcode_wise_data_array[$val['BARCODE_NO']]['cutting_no'] = $val['CUTTING_NO'];
		$cutting_array[$val['CUTTING_NO']] = $val['CUTTING_NO'];
        $booking_po[$val[csf('po_id')]] = $val[csf('po_id')];
        $process_batch[$val['LOT_NO']] = $val['LOT_NO'];
	}
	$po_arr_chunk = array_chunk($booking_po, 900);
    $po_cond = "";
    $po_cond2 = "";
    $po_cond3 = "";
    foreach ($po_arr_chunk as $k => $v){
        if($k == 0) {
            $po_cond .= " a.po_break_down_id in (" . implode(',', $v) . ")";
            $po_cond2 .= " id in (" . implode(',', $v) . ")";
            $po_cond3 .= " a.order_id in ('" . implode("','", $v) . "')";
        }else {
            $po_cond .= " or a.po_break_down_id in (" . implode(',', $v) . ")";
            $po_cond2 .= " or id in (" . implode(',', $v) . ")";
            $po_cond3 .= " or a.order_id in ('" . implode("','", $v) . "')";

        }
    }
    $booking_arr = sql_select("select a.job_no, a.po_break_down_id, a.booking_no from wo_booking_dtls a, wo_booking_dtls b where a.booking_no = b.booking_no and a.status_active = 1 and a.is_deleted = 0 and a.booking_type = 1 and ($po_cond) group by a.job_no, a.po_break_down_id, a.booking_no");
    $booking_no = [];
    foreach ($booking_arr as $data){
        $booking_no[$data[csf('job_no')]][$data[csf('booking_no')]] = $data[csf('booking_no')];
    }
    $ship_date_sql = sql_select("select to_char(max(pub_shipment_date), 'dd-mm-YYYY') as MAX_DATE, to_char(min(pub_shipment_date), 'dd-mm-YYYY') as MIN_DATE, job_no_mst as JOB_NO_MST  from wo_po_break_down where status_active = 1 and is_deleted = 0 and ($po_cond2) group by job_no_mst");
    $ship_date_arr = [];
    foreach ($ship_date_sql as $job){
        $ship_date_arr[$job['JOB_NO_MST']]['first_date'] = $job['MIN_DATE'];
        $ship_date_arr[$job['JOB_NO_MST']]['last_date'] = $job['MAX_DATE'];
    }

    // =============================== reject and replace qty ============================
	$cutting_no_cond = where_con_using_array($cutting_array,1,"a.cutting_no");
	$sql = "SELECT b.barcode_no, b.reject_qty,b.replace_qty from pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $cutting_no_cond";
	// echo $sql;
	$res = sql_select($sql);
	$rej_rep_qty_array = array();
	foreach ($res as $val) 
	{
		// $rej_rep_qty_array[$bcode_wise_data_array[$val['BARCODE_NO']]['job_no']][$bcode_wise_data_array[$val['BARCODE_NO']]['item_id']][$bcode_wise_data_array[$val['BARCODE_NO']]['color_id']][$bcode_wise_data_array[$val['BARCODE_NO']]['lot']][$bcode_wise_data_array[$val['BARCODE_NO']]['cut_no']][$bcode_wise_data_array[$val['BARCODE_NO']]['cutting_no']]['reject_qty'] += $val['REJECT_QTY'];
		// $rej_rep_qty_array[$bcode_wise_data_array[$val['BARCODE_NO']]['job_no']][$bcode_wise_data_array[$val['BARCODE_NO']]['item_id']][$bcode_wise_data_array[$val['BARCODE_NO']]['color_id']][$bcode_wise_data_array[$val['BARCODE_NO']]['lot']][$bcode_wise_data_array[$val['BARCODE_NO']]['cut_no']][$bcode_wise_data_array[$val['BARCODE_NO']]['cutting_no']]['replace_qty'] += $val['REPLACE_QTY'];

		$rej_rep_qty_array[$val['BARCODE_NO']]['reject_qty'] += $val['REJECT_QTY'];
		$rej_rep_qty_array[$val['BARCODE_NO']]['replace_qty'] += $val['REPLACE_QTY'];
	}
	// echo "<pre>";print_r($rej_rep_qty_array);echo "</pre>";
	foreach ($sql_res as $val) 
	{
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['SEASON']][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['COLOR_ID']][$val['LOT_NO']][$val['CUT_NO']][$val['CUTTING_NO']]['reject_qty'] += $rej_rep_qty_array[$val['BARCODE_NO']]['reject_qty'];
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['SEASON']][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['COLOR_ID']][$val['LOT_NO']][$val['CUT_NO']][$val['CUTTING_NO']]['replace_qty'] += $rej_rep_qty_array[$val['BARCODE_NO']]['replace_qty'];
	}
	// echo "<pre>";print_r($data_array);echo "</pre>";
    $batch_arr_chunk = array_chunk($process_batch, 900);
    $batch_cond = "";
    $batch_cond1 = "";
    foreach ($batch_arr_chunk as $k => $v){
        if($k == 0) {
            $batch_cond .= " f.batch_no in ('" . implode("','", $v) . "')";
            $batch_cond1 .= " d.batch_no in ('" . implode("','", $v) . "')";
        }else {
            $batch_cond .= " or f.batch_no in ('" . implode("','", $v) . "')";
            $batch_cond1 .= " or d.batch_no in ('" . implode("','", $v) . "')";
        }
    }
    $sql_process_data = sql_select("select f.batch_id, f.water_flow_meter, f.batch_no, f.load_unload_id, to_char(f.process_end_date, 'dd-mm-YYYY') as process_end_date, f.end_hours, f.hour_load_meter, f.end_minutes from pro_fab_subprocess f where f.load_unload_id=1 and f.entry_form=35 and f.status_active=1 and f.is_deleted=0 and ($batch_cond) $dyeing_process_cond order by f.id");
    //echo "select f.batch_id, f.water_flow_meter, f.batch_no, f.load_unload_id, to_char(f.process_end_date, 'dd-mm-YYYY') as process_end_date, f.end_hours, f.hour_load_meter, f.end_minutes from pro_fab_subprocess f where f.load_unload_id=1 and f.entry_form=35 and f.status_active=1 and f.is_deleted=0 and ($batch_cond) $dyeing_process_cond";

    $dyeing_batch_data = [];
    foreach ($sql_process_data as $k => $v){
        if( $dyeing_batch_data[$v[csf('batch_no')]]['unload_date'] == "") {
            $dyeing_batch_data[$v[csf('batch_no')]]['unload_date'] = $v[csf('process_end_date')];
        }
    }
    $sql_finish_date = sql_select("select to_char(e.receive_date, 'dd-mm-YYYY') as finish_date, d.batch_no, a.color_id from pro_finish_fabric_rcv_dtls a, pro_batch_create_mst d,  inv_receive_master e where a.mst_id = e.id and a.batch_id = d.id and a.status_active = 1 and a.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 and e.entry_form in (37, 225) and ($batch_cond1) order by e.id");
    $finish_date_arr = [];
    foreach ($sql_finish_date as $k => $val){
        if( $finish_date_arr[$val[csf('batch_no')]][$val[csf('color_id')]]['finish_date'] == "") {
            $finish_date_arr[$val[csf('batch_no')]][$val[csf('color_id')]]['finish_date'] = $val[csf('finish_date')];
        }
    }
    $sql_finish_date_production = sql_select("select to_char(e.receive_date, 'dd-mm-YYYY') as finish_date, d.batch_no from pro_finish_fabric_rcv_dtls a, pro_batch_create_mst d,  inv_receive_master e where a.mst_id = e.id and a.batch_id = d.id and a.status_active = 1 and a.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 and e.entry_form = 7 and ($batch_cond1) order by e.id");
    //echo "select to_char(e.receive_date, 'dd-mm-YYYY') as finish_date, d.batch_no from pro_finish_fabric_rcv_dtls a, pro_batch_create_mst d,  inv_receive_master e where a.mst_id = e.id and a.batch_id = d.id and a.status_active = 1 and a.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 and e.entry_form = 7 and ($batch_cond1)";
    $finish_Production_date_arr = [];
    foreach ($sql_finish_date_production as $k => $val){
        if($finish_Production_date_arr[$val[csf('batch_no')]]['finish_date'] == "") {
            $finish_Production_date_arr[$val[csf('batch_no')]]['finish_date'] = $val[csf('finish_date')];
        }
    }
	$tbl_width = 1940;
	$col_span = 16;
	ob_start();
	?>
	<fieldset style="width:<?=$tbl_width+20;?>px;">
		
		<div style="width:<?=$tbl_width+20;?>px;">
			<table width="<?=$tbl_width;?>"  cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="<?=$col_span;?>" align="center" style="border:none;font-size:18px; font-weight:bold" >Lot Wise Cutting Status Report</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="<?=$col_span;?>" align="center" style="border:none;font-size:16px; font-weight:bold" ><?=$company_lib[$wo_company_name];?></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="<?=$col_span;?>" align="center" style="border:none;font-size:15px; font-weight:bold" >Date: <?=$start_date;?></td>
				</tr>
			</table>
			
			<div style="width:<?=$tbl_width+20;?>px; float:left;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width;?>" class="rpt_table" align="left">
						<thead>
							<tr>
								<th width="20">Sl</th>								
								<th width="50">Date</th>								
								<th width="100">Buyer</th>								
								<th width="100">Client</th>
								<th width="80">Job</th>
								<th width="100">Style</th>
								<th width="100">Season</th>
								<th width="100">Booking No.</th>
								<th width="100">PO</th>
                                <th width="60">First Ship Date</th>
                                <th width="60">Last Ship Date</th>
                                <th width="100">Gmts Item</th>
								<th width="100">Color</th>
								<th width="100">Lot No.</th>
								<th width="70">Shade</th>
								<th width="60">Dyeing Unload Date</th>
								<th width="70">Dyeing Unload To Shipment Days</th>
								<th width="60">Finishing Date</th>
                                <th width="70">Finishing To Shipment Days</th>
                                <th width="80">Cutting No.</th>
								<th width="100">S.Cutting </th>
								<th width="80">Cutting Qt.</th>
								<th width="80">Lot Wise Reject</th>
								<th width="80">Lot Wise Replace</th>
							</tr>						
						</thead>
					</table>
					
					<div style="max-height:400px; overflow-y:scroll; width:<?=$tbl_width+20;?>px;" id="scroll_body">
						<table  border="1" style="border-collapse: collapse;" class="rpt_table"  width="<?=$tbl_width;?>" rules="all" id="table_body"  align="left">
							<tbody>
								<?
								$i=1;
								ksort($data_array);
								$gr_total_cut = 0;
								$gr_total_rej = 0;
								$gr_total_rep = 0;
								foreach ($data_array as $byr_name => $byr_data) 
								{
									$byr_total_cut = 0;
									$byr_total_rej = 0;
									$byr_total_rep = 0;
									foreach ($byr_data as $client_name => $client_data) 
									{
										ksort($client_data);
										foreach ($client_data as $season_id => $season_data) 
										{
											foreach ($season_data as $job => $job_data) 
											{
												$job_total_cut = 0;
												$job_total_rej = 0;
												$job_total_rep = 0;
												ksort($job_data);
												foreach ($job_data as $itm_name => $item_data) 
												{
													$itm_total_cut = 0;
													$itm_total_rej = 0;
													$itm_total_rep = 0;
													foreach ($item_data as $color_id => $color_data) 
													{
														$clr_total_cut = 0;
														$clr_total_rej = 0;
														$clr_total_rep = 0;
														foreach ($color_data as $lot_no => $lot_data) 
														{
															foreach ($lot_data as $cut_no => $cut_data) 
															{
																foreach ($cut_data as $cutting_no => $row) 
																{
																	$po_number = implode(",",array_unique(explode(",", chop($row['po_number'],','))));
																	$barcode_no = implode(",",array_unique(explode(",", chop($row['barcode_no'],','))));
																	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
																	$search_string = $barcode_no;

																	$reject_qty = $row['reject_qty'];

																	$replace_qty = $row['replace_qty'];
																	
																	?>
																	<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
																		<td valign="middle" width="20"><?=$i;?></td>
																		<td valign="middle" width="50"><?=change_date_format($row['entry_date']);?></td>
																		<td valign="middle" width="100"><p><?=$byr_name;?></p></td>
																		<td valign="middle" width="100"><p><?=$client_name;?></p></td>
																		<td valign="middle" width="80"><p><?=$job;?></td>
																		<td valign="middle" width="100"><p><?=$row['style'];?></p></td>

																		<td valign="middle" width="100"><p><?=$season_lib[$season_id];?></p></td>
																		<td valign="middle" width="100"><p><?=implode(',', $booking_no[$job]);?></p></td>
																		<td valign="middle" width="100"><p><?=$po_number;?></p></td>
																		<td valign="middle" width="60"><p><?=$ship_date_arr[$job]['first_date'];?></p></td>
																		<td valign="middle" width="60"><p><?=$ship_date_arr[$job]['last_date'];?></p></td>
																		<td valign="middle" width="100"><p><?=$itm_name;?></p></td>
																		<td valign="middle" width="100"><p><?=$color_lib[$color_id];?></p></td>
																		<td valign="middle" width="100"><p><?=$lot_no;?></p></td>
                                                                        <td valign="middle" width="70"><?=implode(',', $row['shade']);?></td>
                                                                        <td valign="middle" width="60"><?=$dyeing_batch_data[$lot_no]['unload_date'] != "" ? $dyeing_batch_data[$lot_no]['unload_date'] : $finish_date_arr[$lot_no][$color_id]['finish_date'];?></td>
                                                                        <td valign="middle" width="70" align="center">
                                                                            <?
                                                                            $date_diff = datediff("d",$dyeing_batch_data[$lot_no]['unload_date'] != "" ? $dyeing_batch_data[$lot_no]['unload_date'] : $finish_date_arr[$lot_no][$color_id]['finish_date'],$ship_date_arr[$job]['last_date']);
                                                                            echo $date_diff;
                                                                            ?>
                                                                        </td>
                                                                        <td valign="middle" width="60"><?=$dyeing_batch_data[$lot_no]['unload_date'] != "" ? $finish_Production_date_arr[$lot_no]['finish_date'] : "";?></td>
                                                                        <td valign="middle" width="70" align="center">
                                                                            <?
                                                                            $finish_date_diff = datediff("d",($dyeing_batch_data[$lot_no]['unload_date'] != "" ? $finish_Production_date_arr[$lot_no]['finish_date'] : ""),$ship_date_arr[$job]['last_date']);
                                                                            echo $finish_date_diff;
                                                                            ?>
                                                                        </td>
																		<td valign="middle" width="80" align="center"><p><?=$cut_no;?></p></td>
																		<td valign="middle" width="100"><p><?=$cutting_no;?></p></td>
																		<td valign="middle" width="80" align="right"><?=number_format($row['total_prod'],0);?></td>
																		<td valign="middle" width="80" align="right">
																			<a href="javascript:void(0);" onclick="fn_show_popup('<?=$search_string;?>','1');">
																				<?=number_format($reject_qty,0);?>
																			</a>
																		</td>
																		<td valign="middle" width="80" align="right">
																			<a href="javascript:void(0);" onclick="fn_show_popup('<?=$search_string;?>','2');">
																				<?=number_format($replace_qty,0);?>
																			</a>
																		</td>
																	</tr>
																	<?
																	$i++;	
																	$clr_total_cut += $row['total_prod'];
																	$clr_total_rej += $reject_qty;
																	$clr_total_rep += $replace_qty;

																	$itm_total_cut += $row['total_prod'];
																	$itm_total_rej += $reject_qty;
																	$itm_total_rep += $replace_qty;

																	$job_total_cut += $row['total_prod'];
																	$job_total_rej += $reject_qty;
																	$job_total_rep += $replace_qty;

																	$byr_total_cut += $row['total_prod'];
																	$byr_total_rej += $reject_qty;
																	$byr_total_rep += $replace_qty;

																	$gr_total_cut += $row['total_prod'];
																	$gr_total_rej += $reject_qty;
																	$gr_total_rep += $replace_qty;
																}
															}
														}	

														?>
														<tr style="text-align: right;background: #cddcdc; font-weight: bold;">
																							
															<td colspan="21">Color Total</td>
															
															<td><?=number_format($clr_total_cut,0);?></td>
															<td><?=number_format($clr_total_rej,0);?></td>
															<td><?=number_format($clr_total_rep,0);?></td>
														</tr>
														<?
													}
													?>
													<tr style="text-align: right;background: #CAF0F8; font-weight: bold;">
																					
														<td colspan="21">Item Total</td>
															
														<td><?=number_format($itm_total_cut,0);?></td>
														<td><?=number_format($itm_total_rej,0);?></td>
														<td><?=number_format($itm_total_rep,0);?></td>
													</tr>
													<?												
												}												
												?>
												<tr style="text-align: right;background: #dccdcd; font-weight: bold;">
																				
													<td colspan="21">Job Total</td>
															
													<td><?=number_format($job_total_cut,0);?></td>
													<td><?=number_format($job_total_rej,0);?></td>
													<td><?=number_format($job_total_rep,0);?></td>
												</tr>
												<?
											}
										}
									}									
									?>
									<tr style="text-align: right;background: #ACB992; font-weight: bold;">
																	
										<td colspan="21">Buyer Total</td>
															
										<td><?=number_format($byr_total_cut,0);?></td>
										<td><?=number_format($byr_total_rej,0);?></td>
										<td><?=number_format($byr_total_rep,0);?></td>
									</tr>
									<?
								}
								?>
							</tbody>
						</table> 
					</div> 
					<table style="border-collapse: collapse;"  border="1" class="rpt_table"  width="<?=$tbl_width;?>" rules="all" align="left" >
						<tfoot>
							<tr>
                                <th width="20"></th>
                                <th width="50"></th>
                                <th width="100"></th>
                                <th width="100"></th>
                                <th width="80"></th>
                                <th width="100"></th>
                                <th width="100"></th>
                                <th width="100"></th>
                                <th width="100"></th>
                                <th width="60"></th>
                                <th width="60"></th>
                                <th width="100"></th>
                                <th width="100"></th>
                                <th width="100"></th>
                                <th width="70"></th>
                                <th width="60"></th>
                                <th width="70"></th>
                                <th width="60"></th>
                                <th width="70"> </th>
                                <th width="80"></th>
								<th width="100">Grand Total</th>
								<th width="80"><?=number_format($gr_total_cut,0);?></th>
								<th width="80"><?=number_format($gr_total_rej,0);?></th>
								<th width="80"><?=number_format($gr_total_rep,0);?></th>
							</tr>
						</tfoot>
					</table>
			</div>
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
	//$filename=$user_id."_".$name.".xls";

	echo "$total_data####$filename";
	exit(); 
}

if($action=="cutting_popup")
{
	extract($_REQUEST); 
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'',''); 
 	list($table_no,$buyer_id,$client_id,$job_no,$item_id) = explode("**", $search_string);
	
	$buyer_library=return_library_array( "select id,buyer_name from  lib_buyer", "id", "buyer_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$actual_resource_library=return_library_array( "select id,line_number from prod_resource_mst", "id", "line_number"  );
	$job_sql = sql_select("SELECT a.company_name,a.style_ref_no,a.buyer_name,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.job_no_mst=$job_no");
	?>
    <div id="data_panel" style="width:100%;text-align: center;padding: 5px;">
		<script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('details_reports').innerHTML);
                d.close();
            }
        </script>
    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    	<span id="popup_report_container" align="center" style="width: 120px;"> </span>
    </div>
    <?
     ob_start();
	?>
    <div align="center" id="details_reports">
    <fieldset style="width:210px">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="210">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Reject Type</th>
                        <th width="80">Reject Qty</th>
                    </tr>
                </thead>
                <tbody>
				<?
                $search_string = "'".implode("','", explode(",", $search_string))."'";
                $sql = "SELECT a.defect_point_id,a.defect_qty from pro_gmts_prod_dft a where a.production_type=1 and a.status_active=1 and a.bundle_no in($search_string)";
                // echo $sql;
                $result = sql_select($sql);
                $i=1;
                $tot_defect_qty = 0;
                foreach($result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="left"><?=$cutting_qc_reject_type[$row[csf("defect_point_id")]];?></td>
                        <td align="right"><?=$row[csf("defect_qty")];?></td>
                    </tr>
                    <?
                    $i++;
                    $tot_defect_qty += $row[csf("defect_qty")];
                }
                ?>
                </tbody>
                <tfoot>
                	<th colspan="2">Total</th>
                	<th><? echo number_format($tot_defect_qty,0); ?></th>
                </tfoot>
            </table>
        </fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
	   @unlink($filename);
	}
	
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);	
	?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
		$(document).ready(function(e) 
		{
			document.getElementById('popup_report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Convert to Excel" name="excel" id="excel" style="padding:0 2px;" class="formbutton"/></a>&nbsp;&nbsp;';
		});	
	</script>
	</div>  
	<?
	exit();
}
?>