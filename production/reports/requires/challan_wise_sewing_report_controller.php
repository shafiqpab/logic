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

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 130, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id=$data group by id,location_name  order by location_name","id,location_name", 1, "-- Select location --", $selected, "load_drop_down( 'requires/style_and_color_wise_production_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_name", 130, "SELECT id,floor_name from lib_prod_floor where location_id=$data and status_active =1 and is_deleted=0 and production_process in(1) group by id,floor_name order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "" );
	exit();
}

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
									echo create_drop_down( "cbo_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'style_and_color_wise_production_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
								?>
	                        </td>
	                        <td align="center" id="buyer_td">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_name $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>                  
	                        <td align="center">	
	                    	<?						
								echo create_drop_down( "cbo_year", 110, $year,"",1, "--Select--", "",'',0 );
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value, 'search_list_view', 'search_div', 'style_and_color_wise_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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
		$("#cbo_year").val('<?=$cbo_year;?>');
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

	if($search_by==1) 
		$search_field="a.job_no_prefix_num"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no";
	$search_cond="";
	if($search_string!="")	{$search_cond=" and $search_field like '%$search_string%'";}
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
	$date_from 			= str_replace("'","",$txt_date_from);
	$date_to 			= str_replace("'","",$txt_date_to);
	
	// ========================= lay cond ========================
	$sql_lay_cond .= ($company_name != 0) 		? " and a.company_name=$company_name" : "";
	$sql_lay_cond .= ($buyer_name != 0) 		? " and a.buyer_name=$buyer_name" : "";
	$sql_lay_cond .= ($hidden_job_id != "") 	? " and a.id in($hidden_job_id)" : "";
	$sql_lay_cond .= ($wo_company_name != "") 	? " and c.working_company_id in($wo_company_name)" : "";

	if($date_from !="" && $date_to !="")
    {
        if($db_type==0)
        {
            $start_date=change_date_format($date_from,"yyyy-mm-dd","");
            $end_date=change_date_format($date_to,"yyyy-mm-dd","");
        }
        else
        {
            $start_date=date("j-M-Y",strtotime($date_from));
            $end_date=date("j-M-Y",strtotime($date_to));
        }
        $sql_lay_cond.= " and c.entry_date between '$start_date' and '$end_date'";
    }

    // ========================= gmts prod cond ========================
	$sql_gmts_cond .= ($company_name != 0) 		? " and a.company_name=$company_name" : "";
	$sql_gmts_cond .= ($buyer_name != 0) 		? " and a.buyer_name=$buyer_name" : "";
	$sql_gmts_cond .= ($hidden_job_id != "") 	? " and a.id in($hidden_job_id)" : "";
	$sql_gmts_cond .= ($wo_company_name != "") 	? " and d.serving_company in($wo_company_name)" : "";

	if($date_from !="" && $date_to !="")
    {
        if($db_type==0)
        {
            $start_date=change_date_format($date_from,"yyyy-mm-dd","");
            $end_date=change_date_format($date_to,"yyyy-mm-dd","");
        }
        else
        {
            $start_date=date("j-M-Y",strtotime($date_from));
            $end_date=date("j-M-Y",strtotime($date_to));
        }
        $sql_gmts_cond.= " and d.production_date between '$start_date' and '$end_date'";
    }

    // ========================= shipment cond ========================
	$sql_exf_cond .= ($company_name != 0) 		? " and a.company_name=$company_name" : "";
	$sql_exf_cond .= ($buyer_name != 0) 		? " and a.buyer_name=$buyer_name" : "";
	$sql_exf_cond .= ($hidden_job_id != "") 	? " and a.id in($hidden_job_id)" : "";
	$sql_exf_cond .= ($wo_company_name != "") 	? " and e.delivery_company_id in($wo_company_name)" : "";

	if($date_from !="" && $date_to !="")
    {
        if($db_type==0)
        {
            $start_date=change_date_format($date_from,"yyyy-mm-dd","");
            $end_date=change_date_format($date_to,"yyyy-mm-dd","");
        }
        else
        {
            $start_date=date("j-M-Y",strtotime($date_from));
            $end_date=date("j-M-Y",strtotime($date_to));
        }
        $sql_exf_cond.= " and e.delivery_date between '$start_date' and '$end_date'";
    }

	// echo $sql_exf_cond;

	$company_lib=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$location_lib=return_library_array( "SELECT id, location_name from lib_location", "id", "location_name"  );
	$floor_lib=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );
    $season_lib = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", "id", "season_name");
    $buyer_lib  = return_library_array("select id, buyer_name from  lib_buyer", "id", "buyer_name");
	$color_lib=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_lib=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$lineArr = return_library_array("select a.id,a.line_name from lib_sewing_line a","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
		
	/*==========================================================================================/
	/										getting lay  data 									/
	/==========================================================================================*/ 	
		
	$sql=" SELECT a.id as job_id,a.job_no,a.buyer_name,a.style_ref_no as style,a.client_id,d.gmt_item_id as item_id,d.color_id as color_id,e.size_id as size_id,sum(case when c.entry_date between '$start_date' and '$end_date' then e.size_qty else 0 end) as today_lay,
		sum(e.size_qty) as total_lay
		from wo_po_details_master a,wo_po_break_down b,ppl_cut_lay_mst c,ppl_cut_lay_dtls d,ppl_cut_lay_bundle e where a.job_no=c.job_no and a.id=b.job_id and c.id=d.mst_id and b.id=e.order_id and c.id=e.mst_id and d.id=e.dtls_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.size_qty>0 $sql_lay_cond group by a.id,a.job_no,a.buyer_name,a.style_ref_no,a.client_id,d.gmt_item_id,d.color_id,e.size_id";		
	// echo $sql;die;
	$sql_res = sql_select($sql);
    if(count($sql_res)==0)
    {
        ?>
        <!-- <center>
            <div style="width: 80%;" class="alert alert-danger">Data not found.Please try again.</div>
        </center> -->
        <?
        // die();
    }

	$data_array = array();
	$smry_data_array = array();
	$all_job_id_array = array();
	$job_id_array = array();
	$item_id_array = array();
	foreach ($sql_res as $val) 
	{
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$color_lib[$val['COLOR_ID']]][$val['SIZE_ID']]['style'] = $val['STYLE'];	
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$color_lib[$val['COLOR_ID']]][$val['SIZE_ID']]['today_lay'] += $val['TODAY_LAY'];	
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$color_lib[$val['COLOR_ID']]][$val['SIZE_ID']]['total_lay'] += $val['TOTAL_LAY'];	
		$all_job_id_array[$val['JOB_ID']] = $val['JOB_ID'];

		// $smry_data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]]['style'] = $val['STYLE'];	

		// $data_array[$buyer_lib[$val['BUYER_NAME']]][$location_lib[$val['LOCATION']]][$floor_lib[$val['FLOOR_ID']]][$buyer_lib[$val['BUYER_NAME']]][$val['STYLE']][$season_lib[$val['SEASON']]][$garments_item[$val['ITEM_ID']]][$color_lib[$val['COLOR_ID']]]['search_string'] = $val['BUYER_NAME']."**".$val['LOCATION']."**".$val['FLOOR_ID']."**".$val['BUYER_NAME']."**".$val['STYLE']."**".$val['SEASON']."**".$val['ITEM_ID']."**".$val['COLOR_ID']."**".$start_date."**".$end_date."**".$val['JOB_NO'];
	}
	// echo "<pre>";print_r($data_array);echo "</pre>";

	//  ==================== lay qty ====================
	/*$job_id_cond = where_con_using_array($job_id_array,0,"a.id");
	$item_id_cond = where_con_using_array($item_id_array,0,"b.item_number_id");
	$sql=" SELECT a.id as job_id,a.job_no,a.buyer_name,a.style_ref_no as style,a.client_id,b.item_number_id as item_id,b.color_number_id as color_id,b.size_number_id as size_id, sum(case when c.entry_date between '$start_date' and '$end_date' then d.size_qty else 0 end) as today_lay,
		sum(d.size_qty) as total_lay
		from wo_po_details_master a,wo_po_color_size_breakdown b,ppl_cut_lay_mst c,ppl_cut_lay_bundle d where a.job_no=c.job_no and a.id=b.job_id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_id_cond $item_id_cond group by a.id,a.job_no,a.buyer_name,a.style_ref_no,a.client_id,b.item_number_id,b.color_number_id,b.size_number_id";	
	// echo $sql;die();	
	$res = sql_select($sql);	
	$lay_qty_array = array();	
	$smry_lay_qty_array = array();	
	foreach ($res as $val) 
	{
		$lay_qty_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$color_lib[$val['COLOR_ID']]][$val['SIZE_ID']]['today_lay'] += $val['TODAY_LAY'];
		$lay_qty_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$color_lib[$val['COLOR_ID']]][$val['SIZE_ID']]['total_lay'] += $val['TOTAL_LAY'];

		// $smry_lay_qty_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]]['today_lay'] += $val['TODAY_LAY'];
		// $smry_lay_qty_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]]['total_lay'] += $val['TOTAL_LAY'];
	}*/

	// ===========================gmts prod data=========================
	$job_id_cond = where_con_using_array($job_id_array,0,"b.job_id");
	$sql=" SELECT a.id as job_id,a.job_no,a.buyer_name,a.style_ref_no as style,a.client_id,c.item_number_id as item_id,c.color_number_id as color_id,c.size_number_id as size_id,d.remarks,d.production_type, 
	sum(case when d.production_date between '$start_date' and '$end_date' then e.production_qnty else 0 end) as today_prod,
	sum(e.production_qnty) as total_prod,sum(case when d.production_type=5 then e.reject_qty else 0 end) as sew_rej_qty,sum(case when d.production_type=11 then e.reject_qty else 0 end) as poly_rej_qty

		from wo_po_details_master a,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e where a.id=c.job_id and c.po_break_down_id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.production_type in(4,5,8,11) and e.production_qnty>0 $sql_gmts_cond group by  a.id,a.job_no,a.buyer_name,a.style_ref_no,a.client_id,c.item_number_id,c.color_number_id,c.size_number_id,d.remarks,d.production_type";		
	// echo $sql;die;
	$sql_res = sql_select($sql);
	$job_id_array = array();
	$item_id_array = array();
	foreach ($sql_res as $val) 
	{
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$color_lib[$val['COLOR_ID']]][$val['SIZE_ID']]['style'] = $val['STYLE'];

		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$color_lib[$val['COLOR_ID']]][$val['SIZE_ID']][$val['PRODUCTION_TYPE']]['today_prod'] += $val['TODAY_PROD'];
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$color_lib[$val['COLOR_ID']]][$val['SIZE_ID']][$val['PRODUCTION_TYPE']]['total_prod'] += $val['TOTAL_PROD'];

		$smry_data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]]['style'] = $val['STYLE'];	
		$smry_data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]]['remarks'] = $val['REMARKS'];	
		$smry_data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]]['search_string'] = $val['BUYER_NAME']."**".$val['CLIENT_ID']."**".$val['JOB_NO']."**".$val['ITEM_ID'];

		$smry_data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['PRODUCTION_TYPE']]['today_prod'] += $val['TODAY_PROD'];
		$smry_data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['PRODUCTION_TYPE']]['total_prod'] += $val['TOTAL_PROD'];
		$smry_data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['PRODUCTION_TYPE']]['sew_rej_qty'] += $val['SEW_REJ_QTY'];
		$smry_data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['PRODUCTION_TYPE']]['poly_rej_qty'] += $val['POLY_REJ_QTY'];
		$all_job_id_array[$val['JOB_ID']] = $val['JOB_ID']; 
	}
	// echo "<pre>";print_r($smry_data_array);die();

	//  ==================== gmts prod qty ====================
	/*$job_id_cond = where_con_using_array($job_id_array,0,"a.id");
	$item_id_cond = where_con_using_array($item_id_array,0,"b.item_number_id");
	$sql=" SELECT a.id as job_id,a.job_no,a.buyer_name,a.style_ref_no as style,a.client_id,b.item_number_id as item_id,b.color_number_id as color_id,b.size_number_id as size_id,c.production_type, 
	sum(case when c.production_date between '$start_date' and '$end_date' then d.production_qnty else 0 end) as today_prod,
	sum(d.production_qnty) as total_prod,sum(case when c.production_type=5 then d.reject_qty else 0 end) as sew_rej_qty,sum(case when c.production_type=11 then d.reject_qty else 0 end) as poly_rej_qty
	from wo_po_details_master a,wo_po_color_size_breakdown b,pro_garments_production_mst c,pro_garments_production_dtls d where a.id=b.job_id and c.id=d.mst_id and b.id=d.color_size_break_down_id and b.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.production_type in(4,5,8,11) and d.production_qnty>0 $job_id_cond $item_id_cond group by a.id,a.job_no,a.buyer_name,a.style_ref_no,a.client_id,b.item_number_id,b.color_number_id,b.size_number_id,c.production_type";	
	// echo $sql;die();	
	$res = sql_select($sql);	
	$gmts_qty_array = array();	
	$smry_gmts_qty_array = array();	
	foreach ($res as $val) 
	{
		$gmts_qty_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$color_lib[$val['COLOR_ID']]][$val['SIZE_ID']][$val['PRODUCTION_TYPE']]['today_prod'] += $val['TODAY_PROD'];
		$gmts_qty_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$color_lib[$val['COLOR_ID']]][$val['SIZE_ID']][$val['PRODUCTION_TYPE']]['total_prod'] += $val['TOTAL_PROD'];

		$smry_gmts_qty_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['PRODUCTION_TYPE']]['today_prod'] += $val['TODAY_PROD'];
		$smry_gmts_qty_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['PRODUCTION_TYPE']]['total_prod'] += $val['TOTAL_PROD'];
		$smry_gmts_qty_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['PRODUCTION_TYPE']]['sew_rej_qty'] += $val['SEW_REJ_QTY'];
		$smry_gmts_qty_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$val['PRODUCTION_TYPE']]['poly_rej_qty'] += $val['POLY_REJ_QTY'];
	}*/

	// ===========================shipment data=========================
	$job_id_cond = where_con_using_array($job_id_array,0,"b.job_id");
	$sql=" SELECT a.id as job_id,a.job_no,a.buyer_name,a.style_ref_no as style,a.client_id,c.item_number_id as item_id,c.color_number_id as color_id,c.size_number_id as size_id, 
	sum(case when d.ex_factory_date between '$start_date' and '$end_date' then f.production_qnty else 0 end) as today_prod,
	sum(f.production_qnty) as total_prod

		from wo_po_details_master a,wo_po_color_size_breakdown c,pro_ex_factory_mst d,pro_ex_factory_delivery_mst e,pro_ex_factory_dtls f where a.id=c.job_id and c.po_break_down_id=d.po_break_down_id and e.id=d.delivery_mst_id and d.id=f.mst_id and c.id=f.color_size_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.production_qnty>0 $sql_exf_cond group by a.id,a.job_no,a.buyer_name,a.style_ref_no,a.client_id,c.item_number_id,c.color_number_id,c.size_number_id";		
	// echo $sql;die;
	$sql_res = sql_select($sql);
	$job_id_array = array();
	$item_id_array = array();
	foreach ($sql_res as $val) 
	{
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$color_lib[$val['COLOR_ID']]][$val['SIZE_ID']]['style'] = $val['STYLE'];	
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$color_lib[$val['COLOR_ID']]][$val['SIZE_ID']]['today_prod'] += $val['TODAY_PROD'];
		$data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$color_lib[$val['COLOR_ID']]][$val['SIZE_ID']]['total_prod'] += $val['TOTAL_PROD'];

		// $smry_data_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]]['style'] = $val['STYLE'];	
		$all_job_id_array[$val['JOB_ID']] = $val['JOB_ID'];
		// $job_id_array[$val['JOB_ID']] = $val['JOB_ID'];
		// $item_id_array[$val['ITEM_ID']] = $val['ITEM_ID'];	
	}

	//  ==================== shipment qty ====================
	/*$job_id_cond = where_con_using_array($job_id_array,0,"a.id");
	$item_id_cond = where_con_using_array($item_id_array,0,"b.item_number_id");
	$sql=" SELECT a.id as job_id,a.job_no,a.buyer_name,a.style_ref_no as style,a.client_id,b.item_number_id as item_id,b.color_number_id as color_id,b.size_number_id as size_id, 
	sum(case when c.ex_factory_date between '$start_date' and '$end_date' then d.production_qnty else 0 end) as today_prod,
	sum(d.production_qnty) as total_prod
	from wo_po_details_master a,wo_po_color_size_breakdown b,pro_ex_factory_mst c,pro_ex_factory_dtls d where a.id=b.job_id and c.id=d.mst_id and b.id=d.color_size_break_down_id and b.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.production_qnty>0 $job_id_cond $item_id_cond group by a.id,a.job_no,a.buyer_name,a.style_ref_no,a.client_id,b.item_number_id,b.color_number_id,b.size_number_id";	
	// echo $sql;die();	
	$res = sql_select($sql);	
	$ex_qty_array = array();		
	foreach ($res as $val) 
	{
		$ex_qty_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$color_lib[$val['COLOR_ID']]][$val['SIZE_ID']]['today_prod'] += $val['TODAY_PROD'];
		$ex_qty_array[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$color_lib[$val['COLOR_ID']]][$val['SIZE_ID']]['total_prod'] += $val['TOTAL_PROD'];
	}
	// echo "<pre>";print_r($ex_qty_array);echo "</pre>";*/

	// ============================= size qty ======================
	$job_id_cond = where_con_using_array($all_job_id_array,0,"a.id");
	$sql = "SELECT a.buyer_name,a.job_no,a.client_id,b.item_number_id as item_id,b.color_number_id as color_id,b.size_number_id as size_id,b.order_quantity FROM wo_po_details_master a, wo_po_color_size_breakdown b where a.id=b.job_id and b.status_active in(1,2,3) and b.is_deleted=0 $job_id_cond";
	// echo $sql;
	$res = sql_select($sql);
	foreach ($res as $val) 
	{
		$order_qty_arr[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]][$color_lib[$val['COLOR_ID']]][$val['SIZE_ID']] += $val['ORDER_QUANTITY'];
		$smry_order_qty_arr[$buyer_lib[$val['BUYER_NAME']]][$buyer_lib[$val['CLIENT_ID']]][$val['JOB_NO']][$garments_item[$val['ITEM_ID']]] += $val['ORDER_QUANTITY'];
	}
	// echo "<pre>";print_r($data_array);echo "</pre>";
	$tbl_width = 1860;	
	$smry_tbl_width = 1540;	
	ob_start();
	?>
	<fieldset style="width:<?=$tbl_width+20;?>px;">
		
		<div style="width:<?=$tbl_width+20;?>px;">
			<table width="<?=$tbl_width;?>"  cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="15" align="center" style="border:none;font-size:18px; font-weight:bold" >Style and Color wise Production Report</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" >Working Company Name : <?=$company_lib[$wo_company_name];?></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="15" align="center" style="border:none;font-size:15px; font-weight:bold" >Date: <?=$date_from;?> To <?=$date_to;?></td>
				</tr>
			</table>

			<!-- ======================== samary part ===================== -->
			<div style="width:<?=$smry_tbl_width+20;?>px; float:left;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$smry_tbl_width;?>" class="rpt_table" align="left">
						<caption style="text-align: left;font-size: 20px;font-weight: bold;">Report Summary Part</caption>
						<thead>
							<tr>
								<th width="20">Sl</th>								
								<th width="100">Buyer</th>								
								<th width="100">Client</th>
								<th width="100">Style</th>
								<th width="80">Job No</th>
								<th width="100">Garment Item</th>
								<th width="80">Order Qty(pcs)</th>
								<th width="80">Current Input</th>
								<th width="80">Total Input</th>
								<th width="80">Current Output</th>
								<th width="80">Total Output</th>
								<th width="80">Total Reject</th>
								<th width="80">Sew WIP</th>
								<th width="80">Current Poly</th>
								<th width="80">Total Poly</th>
								<th width="80">Total Poly Rej</th>
								<th width="80">Total Poly WIP</th>
								<th width="80">Input to Poly WIP</th>
								<th width="80">Remarks</th>
							</tr>						
						</thead>
					</table>
					
					<div style="max-height:200px; overflow-y:scroll; width:<?=$smry_tbl_width+20;?>px;" id="smry_scroll_body">
						<table  border="1" style="border-collapse: collapse;" class="rpt_table"  width="<?=$smry_tbl_width;?>" rules="all" id="smry_table_body"  align="left">
							<tbody>
								<?
								$i=1;
								$gr_order_qty = 0;
								$gr_today_in = 0;
								$gr_total_in = 0;
								$gr_today_out = 0;
								$gr_total_out = 0;
								$gr_sew_rej = 0;
								$gr_sew_wip = 0;
								$gr_today_poly = 0;
								$gr_total_poly = 0;
								$gr_poly_rej = 0;
								$gr_poly_wip = 0;
								$gr_input_to_poly = 0;

								$chk_color = array();

								ksort($smry_data_array);
								foreach ($smry_data_array as $byr_name => $byr_data) 
								{
									$byr_order_qty = 0;
									$byr_today_in = 0;
									$byr_total_in = 0;
									$byr_today_out = 0;
									$byr_total_out = 0;
									$byr_sew_rej = 0;
									$byr_sew_wip = 0;
									$byr_today_poly = 0;
									$byr_total_poly = 0;
									$byr_poly_rej = 0;
									$byr_poly_wip = 0;
									$byr_input_to_poly = 0;
									ksort($byr_data);
									foreach ($byr_data as $client_name => $client_data) 
									{
										ksort($client_data);
										foreach ($client_data as $job => $job_data) 
										{											
											$job_order_qty = 0;
											$job_today_in = 0;
											$job_total_in = 0;
											$job_today_out = 0;
											$job_total_out = 0;
											$job_sew_rej = 0;
											$job_sew_wip = 0;
											$job_today_poly = 0;
											$job_total_poly = 0;
											$job_poly_rej = 0;
											$job_poly_wip = 0;
											$job_input_to_poly = 0;
											ksort($job_data);
											foreach ($job_data as $itm_name => $row) 
											{
												$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
												$search_string = $row['search_string'];
												$order_qty = $smry_order_qty_arr[$byr_name][$client_name][$job][$itm_name];

												$today_in = $row[4]['today_prod'];
												$total_in = $row[4]['total_prod'];
												$today_out = $row[5]['today_prod'];
												$total_out = $row[5]['total_prod'];
												$sew_rej_qty = $row[5]['sew_rej_qty'];
												$sewing_wip = $total_in - $total_out;

												$today_poly = $row[11]['today_prod'];
												$total_poly = $row[11]['total_prod'];
												$poly_rej_qty = $row[11]['poly_rej_qty'];
												$poly_wip = $total_out - $total_poly;
												$input_to_poly = $total_in - $total_poly;
												?>
												<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
													<td width="20"><p><?=$i;?></p></td>
													<td width="100"><p><?=$byr_name;?></p></td>
													<td width="100"><p><?=$client_name;?></p></td>							
													<td width="100"><p><?=$row['style'];?></p></td>
													<td width="80"><p><?=$job;?></p></td>
													<td width="100"><p><?=$itm_name;?></p></td>
													<td width="80" align="right"><p><?=number_format($order_qty,0);?></p></td>

													<td width="80" align="right"><?=number_format($today_in,0);?></td>
													<td width="80" align="right"><?=number_format($total_in,0);?></td>

													<td width="80" align="right"><?=number_format($today_out,0);?></td>
													<td width="80" align="right"><?=number_format($total_out,0);?></td>
													<td width="80" align="right"><?=number_format($sew_rej_qty,0);?></td>
													<td width="80" align="right"><?=number_format($sewing_wip,0);?></td>

													<td width="80" align="right"><?=number_format($today_poly,0);?></td>
													<td width="80" align="right"><?=number_format($total_poly,0);?></td>

													<td width="80" align="right"><?=number_format($poly_rej_qty,0);?></td>
													<td width="80" align="right"><?=number_format($poly_wip,0);?></td>
													<td width="80" align="right"><?=number_format($input_to_poly,0);?></td>
													<td width="80" align="center">
														<a href="javascript:void(0)" onclick="openmypage_remarks_popup('<?=$search_string;?>')">
															View
														</a>															
													</td>
												</tr>
												<?
												$i++;

												$job_order_qty += $order_qty;
												$job_today_in += $today_in;
												$job_total_in += $total_in;
												$job_today_out += $today_out;
												$job_total_out += $total_out;
												$job_sew_rej += $sew_rej_qty ;
												$job_sew_wip += $sewing_wip ;
												$job_today_poly += $today_poly;
												$job_total_poly += $total_poly;
												$job_poly_rej += $poly_rej_qty;
												$job_poly_wip += $poly_wip;
												$job_input_to_poly += $input_to_poly;

												$byr_order_qty += $order_qty;
												$byr_today_in += $today_in;
												$byr_total_in += $total_in;
												$byr_today_out += $today_out;
												$byr_total_out += $total_out;
												$byr_sew_rej += $sew_rej_qty ;
												$byr_sew_wip += $sewing_wip ;
												$byr_today_poly += $today_poly;
												$byr_total_poly += $total_poly;
												$byr_poly_rej += $poly_rej_qty;
												$byr_poly_wip += $poly_wip;
												$byr_input_to_poly += $input_to_poly;

												$gr_order_qty += $order_qty;
												$gr_today_in += $today_in;
												$gr_total_in += $total_in;
												$gr_today_out += $today_out;
												$gr_total_out += $total_out;
												$gr_sew_rej += $sew_rej_qty;
												$gr_sew_wip += $sewing_wip;
												$gr_today_poly += $today_poly;
												$gr_total_poly += $total_poly;
												$gr_poly_rej += $poly_rej_qty;
												$gr_poly_wip += $poly_wip;
												$gr_input_to_poly += $input_to_poly;
													
											}
											?>
											<tr style="background: #E4CDA7;font-weight: bold;text-align: right;">
												<td></td>
												<td></td>
												<td></td>							
												<td></td>
												<td></td>
												<td>Style Total</td>
												<td><?=number_format($job_order_qty,0);?></td>

												<td><?=number_format($job_today_in,0);?></td>
												<td><?=number_format($job_total_in,0);?></td>

												<td><?=number_format($job_today_out,0);?></td>
												<td><?=number_format($job_total_out,0);?></td>
												<td><?=number_format($job_sew_rej,0);?></td>
												<td><?=number_format($job_sew_wip,0);?></td>

												<td><?=number_format($job_today_poly,0);?></td>
												<td><?=number_format($job_total_poly,0);?></td>

												<td><?=number_format($job_poly_rej,0);?></td>
												<td><?=number_format($job_poly_wip,0);?></td>
												<td><?=number_format($job_input_to_poly,0);?></td>
												<td></td>
											</tr>
											<?
										}
									}
									?>
									<tr style="background: #95D1CC;font-weight: bold;text-align: right;">
										<td></td>
										<td></td>
										<td></td>							
										<td></td>
										<td></td>
										<td>Buyer Total</td>
										<td><?=number_format($byr_order_qty,0);?></td>

										<td><?=number_format($byr_today_in,0);?></td>
										<td><?=number_format($byr_total_in,0);?></td>

										<td><?=number_format($byr_today_out,0);?></td>
										<td><?=number_format($byr_total_out,0);?></td>
										<td><?=number_format($byr_sew_rej,0);?></td>
										<td><?=number_format($byr_sew_wip,0);?></td>

										<td><?=number_format($byr_today_poly,0);?></td>
										<td><?=number_format($byr_total_poly,0);?></td>

										<td><?=number_format($byr_poly_rej,0);?></td>
										<td><?=number_format($byr_poly_wip,0);?></td>
										<td><?=number_format($byr_input_to_poly,0);?></td>
										<td></td>
									</tr>
									<?
								}
								?>
							</tbody>
						</table> 
					</div> 
					<table style="border-collapse: collapse;"  border="1" class="rpt_table"  width="<?=$smry_tbl_width;?>" rules="all" align="left" >
						<tfoot>
							<tr>
								<th width="20"></th>
								<th width="100"></th>
								<th width="100"></th>	
								<th width="100"></th>
								<th width="80"></th>
								<th width="100">Grand Total</th>
								<th width="80"><?=number_format($gr_order_qty,0);?></th>

								<th width="80"><?=number_format($gr_today_in,0);?></th>
								<th width="80"><?=number_format($gr_total_in,0);?></th>

								<th width="80"><?=number_format($gr_today_out,0);?></th>
								<th width="80"><?=number_format($gr_total_out,0);?></th>
								<th width="80"><?=number_format($gr_sew_rej,0);?></th>
								<th width="80"><?=number_format($gr_sew_wip,0);?></th>

								<th width="80"><?=number_format($gr_today_poly,0);?></th>
								<th width="80"><?=number_format($gr_total_poly,0);?></th>

								<th width="80"><?=number_format($gr_poly_rej,0);?></th>
								<th width="80"><?=number_format($gr_poly_wip,0);?></th>
								<th width="80"><?=number_format($gr_input_to_poly,0);?></th>
								<th width="80"></th>
							</tr>
						</tfoot>
					</table>
				</div>
				
				<!-- ======================= details part ============================ -->
				<div style="width:<?=$tbl_width+20;?>px; float:left;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width;?>" class="rpt_table" align="left">
						<caption style="text-align: left;font-size: 20px;font-weight: bold;">Report Details Part</caption>
						<thead>
							<tr>
								<th rowspan="2" width="20">Sl</th>								
								<th rowspan="2" width="100">Buyer</th>								
								<th rowspan="2" width="100">Client</th>
								<th rowspan="2" width="100">Style</th>
								<th rowspan="2" width="80">Job No</th>
								<th rowspan="2" width="100">Garment Item</th>
								<th rowspan="2" width="100">Color</th>
								<th rowspan="2" width="80">Size</th>
								<th rowspan="2" width="80">Order Qty(pcs)</th>
								<th colspan="3" width="180">Lay Cut Qty </th>
								<th colspan="3" width="180">Sewing Input</th>
								<th colspan="3" width="180">Sewing Output</th>
								<th colspan="3" width="180">Poly Entry</th>
								<th colspan="3" width="180">Packing & Finishing</th>
								<th colspan="3" width="180">Ex-Factory</th>
							</tr>		
							<tr>
								<th width="60">Today</th>
								<th width="60">Total</th>
								<th width="60">Balance</th>

								<th width="60">Today</th>
								<th width="60">Total</th>
								<th width="60">Balance</th>

								<th width="60">Today</th>
								<th width="60">Total</th>
								<th width="60">Balance</th>

								<th width="60">Today</th>
								<th width="60">Total</th>
								<th width="60">Balance</th>

								<th width="60">Today</th>
								<th width="60">Total</th>
								<th width="60">Balance</th>

								<th width="60">Today</th>
								<th width="60">Total</th>
								<th width="60">Balance</th>
							</tr>						
						</thead>
					</table>
					
					<div style="max-height:425px; overflow-y:scroll; width:<?=$tbl_width+20;?>px;" id="scroll_body">
						<table  border="1" style="border-collapse: collapse;" class="rpt_table"  width="<?=$tbl_width;?>" rules="all" id="table_body"  align="left">
							<tbody>
								<?
								$i=1;
								$gr_order_qty = 0;
								$gr_today_lay = 0;
								$gr_total_lay = 0;
								$gr_lay_balance = 0;
								$gr_today_in = 0;
								$gr_total_in = 0;
								$gr_in_balance = 0;
								$gr_today_out = 0;
								$gr_total_out = 0;
								$gr_out_balance = 0;
								$gr_today_poly = 0;
								$gr_total_poly = 0;
								$gr_poly_balance = 0;
								$gr_today_fin = 0;
								$gr_total_fin = 0;
								$gr_fin_balance = 0;
								$gr_today_ex = 0;
								$gr_total_ex = 0;
								$gr_ex_balance = 0;

								$chk_color = array();

								ksort($data_array);
								foreach ($data_array as $byr_name => $byr_data) 
								{										
									$byr_order_qty = 0;
									$byr_today_lay = 0;
									$byr_total_lay = 0;
									$byr_lay_balance = 0;
									$byr_today_in = 0;
									$byr_total_in = 0;
									$byr_in_balance = 0;
									$byr_today_out = 0;
									$byr_total_out = 0;
									$byr_out_balance = 0;
									$byr_today_poly = 0;
									$byr_total_poly = 0;
									$byr_poly_balance = 0;
									$byr_today_fin = 0;
									$byr_total_fin = 0;
									$byr_fin_balance = 0;
									$byr_today_ex = 0;
									$byr_total_ex = 0;
									$byr_ex_balance = 0;
									ksort($byr_data);
									foreach ($byr_data as $client_name => $client_data) 
									{
										ksort($client_data);
										foreach ($client_data as $job => $job_data) 
										{
											$job_order_qty = 0;
											$job_today_lay = 0;
											$job_total_lay = 0;
											$job_lay_balance = 0;
											$job_today_in = 0;
											$job_total_in = 0;
											$job_in_balance = 0;
											$job_today_out = 0;
											$job_total_out = 0;
											$job_out_balance = 0;
											$job_today_poly = 0;
											$job_total_poly = 0;
											$job_poly_balance = 0;
											$job_today_fin = 0;
											$job_total_fin = 0;
											$job_fin_balance = 0;
											$job_today_ex = 0;
											$job_total_ex = 0;
											$job_ex_balance = 0;
											ksort($job_data);
											foreach ($job_data as $itm_name => $itm_data) 
											{												
												$itm_order_qty = 0;
												$itm_today_lay = 0;
												$itm_total_lay = 0;
												$itm_lay_balance = 0;
												$itm_today_in = 0;
												$itm_total_in = 0;
												$itm_in_balance = 0;
												$itm_today_out = 0;
												$itm_total_out = 0;
												$itm_out_balance = 0;
												$itm_today_poly = 0;
												$itm_total_poly = 0;
												$itm_poly_balance = 0;
												$itm_today_fin = 0;
												$itm_total_fin = 0;
												$itm_fin_balance = 0;
												$itm_today_ex = 0;
												$itm_total_ex = 0;
												$itm_ex_balance = 0;
												ksort($itm_data);
												foreach ($itm_data as $clr_name => $clr_data) 
												{
													$clr_order_qty = 0;
													$clr_today_lay = 0;
													$clr_total_lay = 0;
													$clr_lay_balance = 0;
													$clr_today_in = 0;
													$clr_total_in = 0;
													$clr_in_balance = 0;
													$clr_today_out = 0;
													$clr_total_out = 0;
													$clr_out_balance = 0;
													$clr_today_poly = 0;
													$clr_total_poly = 0;
													$clr_poly_balance = 0;
													$clr_today_fin = 0;
													$clr_total_fin = 0;
													$clr_fin_balance = 0;
													$clr_today_ex = 0;
													$clr_total_ex = 0;
													$clr_ex_balance = 0;
													ksort($clr_data);
													foreach ($clr_data as $size_id => $row) 
													{
														$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
														// $search_string = $row['search_string'];
														$order_qty = $order_qty_arr[$byr_name][$client_name][$job][$itm_name][$clr_name][$size_id];
														$today_lay = $row['today_lay'];
														$total_lay = $row['total_lay'];
														$lay_balance = $order_qty - $total_lay;

														$today_in = $row[4]['today_prod'];
														$total_in = $row[4]['total_prod'];
														$in_balance = $total_lay - $total_in;

														$today_out = $row[5]['today_prod'];
														$total_out = $row[5]['total_prod'];
														$out_balance = $in_balance - $total_out;

														$today_poly = $row[11]['today_prod'];
														$total_poly = $row[11]['total_prod'];
														$poly_balance = $out_balance - $total_poly;

														$today_fin = $row[8]['today_prod'];
														$total_fin = $row[8]['total_prod'];
														$fin_balance = $poly_balance - $total_fin;

														// $today_ex = $ex_qty_array[$byr_name][$client_name][$job][$itm_name][$clr_name][$size_id]['today_prod'];
														// $total_ex = $ex_qty_array[$byr_name][$client_name][$job][$itm_name][$clr_name][$size_id]['total_prod'];
														$today_ex = $row['today_prod'];
														$total_ex = $row['total_prod'];
														
														$ex_balance = $fin_balance - $total_ex;
														?>
														<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
															<td width="20"><p><?=$i;?></p></td>
															<td width="100"><p><?=$byr_name;?></p></td>
															<td width="100"><p><?=$client_name;?></p></td>							
															<td width="100"><p><?=$row['style'];?></p></td>
															<td width="80"><p><?=$job;?></p></td>
															<td width="100"><p><?=$itm_name;?></p></td>
															<td width="100"><p><?=$clr_name;?></p></td>
															<td width="80"><p><?=$size_lib[$size_id];?></p></td>
															<td width="80" align="right"><p><?=number_format($order_qty,0);?></p></td>

															<td width="60" align="right"><?=number_format($today_lay,0);?></td>
															<td width="60" align="right"><?=number_format($total_lay,0);?></td>
															<td width="60" align="right"><?=number_format($lay_balance,0);?></td>

															<td width="60" align="right"><?=number_format($today_in,0);?></td>
															<td width="60" align="right"><?=number_format($total_in,0);?></td>
															<td width="60" align="right"><?=number_format($in_balance,0);?></td>

															<td width="60" align="right"><?=number_format($today_out,0);?></td>
															<td width="60" align="right"><?=number_format($total_out,0);?></td>
															<td width="60" align="right"><?=number_format($out_balance,0);?></td>

															<td width="60" align="right"><?=number_format($today_poly,0);?></td>
															<td width="60" align="right"><?=number_format($total_poly,0);?></td>
															<td width="60" align="right"><?=number_format($poly_balance,0);?></td>

															<td width="60" align="right"><?=number_format($today_fin,0);?></td>
															<td width="60" align="right"><?=number_format($total_fin,0);?></td>
															<td width="60" align="right"><?=number_format($fin_balance,0);?></td>

															<td width="60" align="right"><?=number_format($today_ex,0);?></td>
															<td width="60" align="right"><?=number_format($total_ex,0);?></td>
															<td width="60" align="right"><?=number_format($ex_balance,0);?></td>
														</tr>
														<?
														$i++;
														$clr_order_qty += $order_qty;
														$clr_today_lay += $today_lay;
														$clr_total_lay += $total_lay;
														$clr_lay_balance += $lay_balance;
														$clr_today_in += $today_in;
														$clr_total_in += $total_in;
														$clr_in_balance += $in_balance;
														$clr_today_out += $today_out;
														$clr_total_out += $total_out;
														$clr_out_balance += $out_balance ;
														$clr_today_poly += $today_poly;
														$clr_total_poly += $total_poly;
														$clr_poly_balance += $poly_balance;
														$clr_today_fin += $today_fin;
														$clr_total_fin += $total_fin;
														$clr_fin_balance += $fin_balance;
														$clr_today_ex += $today_ex;
														$clr_total_ex += $total_ex;
														$clr_ex_balance += $ex_balance;

														$itm_order_qty += $order_qty;
														$itm_today_lay += $today_lay;
														$itm_total_lay += $total_lay;
														$itm_lay_balance += $lay_balance;
														$itm_today_in += $today_in;
														$itm_total_in += $total_in;
														$itm_in_balance += $in_balance;
														$itm_today_out += $today_out;
														$itm_total_out += $total_out;
														$itm_out_balance += $out_balance ;
														$itm_today_poly += $today_poly;
														$itm_total_poly += $total_poly;
														$itm_poly_balance += $poly_balance;
														$itm_today_fin += $today_fin;
														$itm_total_fin += $total_fin;
														$itm_fin_balance += $fin_balance;
														$itm_today_ex += $today_ex;
														$itm_total_ex += $total_ex;
														$itm_ex_balance += $ex_balance;

														$job_order_qty += $order_qty;
														$job_today_lay += $today_lay;
														$job_total_lay += $total_lay;
														$job_lay_balance += $lay_balance;
														$job_today_in += $today_in;
														$job_total_in += $total_in;
														$job_in_balance += $in_balance;
														$job_today_out += $today_out;
														$job_total_out += $total_out;
														$job_out_balance += $out_balance ;
														$job_today_poly += $today_poly;
														$job_total_poly += $total_poly;
														$job_poly_balance += $poly_balance;
														$job_today_fin += $today_fin;
														$job_total_fin += $total_fin;
														$job_fin_balance += $fin_balance;
														$job_today_ex += $today_ex;
														$job_total_ex += $total_ex;
														$job_ex_balance += $ex_balance;

														$byr_order_qty += $order_qty;
														$byr_today_lay += $today_lay;
														$byr_total_lay += $total_lay;
														$byr_lay_balance += $lay_balance;
														$byr_today_in += $today_in;
														$byr_total_in += $total_in;
														$byr_in_balance += $in_balance;
														$byr_today_out += $today_out;
														$byr_total_out += $total_out;
														$byr_out_balance += $out_balance ;
														$byr_today_poly += $today_poly;
														$byr_total_poly += $total_poly;
														$byr_poly_balance += $poly_balance;
														$byr_today_fin += $today_fin;
														$byr_total_fin += $total_fin;
														$byr_fin_balance += $fin_balance;
														$byr_today_ex += $today_ex;
														$byr_total_ex += $total_ex;
														$byr_ex_balance += $ex_balance;

														$gr_order_qty += $order_qty;
														$gr_today_lay += $today_lay;
														$gr_total_lay += $total_lay;
														$gr_lay_balance += $lay_balance;
														$gr_today_in += $today_in;
														$gr_total_in += $total_in;
														$gr_in_balance += $in_balance;
														$gr_today_out += $today_out;
														$gr_total_out += $total_out;
														$gr_out_balance += $out_balance ;
														$gr_today_poly += $today_poly;
														$gr_total_poly += $total_poly;
														$gr_poly_balance += $poly_balance;
														$gr_today_fin += $today_fin;
														$gr_total_fin += $total_fin;
														$gr_fin_balance += $fin_balance;
														$gr_today_ex += $today_ex;
														$gr_total_ex += $total_ex;
														$gr_ex_balance += $ex_balance;
															
													}
													?>
													<tr style="background: #cddcdc;font-weight: bold;text-align: right;">
														<td width="20"></td>
														<td width="100"></td>
														<td width="100"></td>							
														<td width="100"></td>
														<td width="80"></td>
														<td width="100"></td>
														<td width="100"></td>
														<td width="80">Color Total</td>
														<td width="80"><?=number_format($clr_order_qty,0);?></td>

														<td width="60"><?=number_format($clr_today_lay,0);?></td>
														<td width="60"><?=number_format($clr_total_lay,0);?></td>
														<td width="60"><?=number_format($clr_lay_balance,0);?></td>

														<td width="60"><?=number_format($clr_today_in,0);?></td>
														<td width="60"><?=number_format($clr_total_in,0);?></td>
														<td width="60"><?=number_format($clr_in_balance,0);?></td>

														<td width="60"><?=number_format($clr_today_out,0);?></td>
														<td width="60"><?=number_format($clr_total_out,0);?></td>
														<td width="60"><?=number_format($clr_out_balance ,0);?></td>

														<td width="60"><?=number_format($clr_today_poly,0);?></td>
														<td width="60"><?=number_format($clr_total_poly,0);?></td>
														<td width="60"><?=number_format($clr_poly_balance,0);?></td>

														<td width="60"><?=number_format($clr_today_fin,0);?></td>
														<td width="60"><?=number_format($clr_total_fin,0);?></td>
														<td width="60"><?=number_format($clr_fin_balance,0);?></td>

														<td width="60"><?=number_format($clr_today_ex,0);?></td>
														<td width="60"><?=number_format($clr_total_ex,0);?></td>
														<td width="60"><?=number_format($clr_ex_balance,0);?></td>
													</tr>
													<?
												}
												?>
												<tr style="background: #dccdcd;font-weight: bold;text-align: right;">
													<td width="20"></td>
													<td width="100"></td>
													<td width="100"></td>							
													<td width="100"></td>
													<td width="80"></td>
													<td width="100"></td>
													<td width="100"></td>
													<td width="80">Item Total</td>
													<td width="80"><?=number_format($itm_order_qty,0);?></td>

													<td width="60"><?=number_format($itm_today_lay,0);?></td>
													<td width="60"><?=number_format($itm_total_lay,0);?></td>
													<td width="60"><?=number_format($itm_lay_balance,0);?></td>

													<td width="60"><?=number_format($itm_today_in,0);?></td>
													<td width="60"><?=number_format($itm_total_in,0);?></td>
													<td width="60"><?=number_format($itm_in_balance,0);?></td>

													<td width="60"><?=number_format($itm_today_out,0);?></td>
													<td width="60"><?=number_format($itm_total_out,0);?></td>
													<td width="60"><?=number_format($itm_out_balance ,0);?></td>

													<td width="60"><?=number_format($itm_today_poly,0);?></td>
													<td width="60"><?=number_format($itm_total_poly,0);?></td>
													<td width="60"><?=number_format($itm_poly_balance,0);?></td>

													<td width="60"><?=number_format($itm_today_fin,0);?></td>
													<td width="60"><?=number_format($itm_total_fin,0);?></td>
													<td width="60"><?=number_format($itm_fin_balance,0);?></td>
													
													<td width="60"><?=number_format($itm_today_ex,0);?></td>
													<td width="60"><?=number_format($itm_total_ex,0);?></td>
													<td width="60"><?=number_format($itm_ex_balance,0);?></td>
												</tr>
												<?
											}
											?>
											<tr style="background: #E4CDA7;font-weight: bold;text-align: right;">
												<td width="20"></td>
												<td width="100"></td>
												<td width="100"></td>							
												<td width="100"></td>
												<td width="80"></td>
												<td width="100"></td>
												<td width="100"></td>
												<td width="80">Job Total</td>
												<td width="80"><?=number_format($job_order_qty,0);?></td>

												<td width="60"><?=number_format($job_today_lay,0);?></td>
												<td width="60"><?=number_format($job_total_lay,0);?></td>
												<td width="60"><?=number_format($job_lay_balance,0);?></td>

												<td width="60"><?=number_format($job_today_in,0);?></td>
												<td width="60"><?=number_format($job_total_in,0);?></td>
												<td width="60"><?=number_format($job_in_balance,0);?></td>

												<td width="60"><?=number_format($job_today_out,0);?></td>
												<td width="60"><?=number_format($job_total_out,0);?></td>
												<td width="60"><?=number_format($job_out_balance ,0);?></td>

												<td width="60"><?=number_format($job_today_poly,0);?></td>
												<td width="60"><?=number_format($job_total_poly,0);?></td>
												<td width="60"><?=number_format($job_poly_balance,0);?></td>

												<td width="60"><?=number_format($job_today_fin,0);?></td>
												<td width="60"><?=number_format($job_total_fin,0);?></td>
												<td width="60"><?=number_format($job_fin_balance,0);?></td>
												
												<td width="60"><?=number_format($job_today_ex,0);?></td>
												<td width="60"><?=number_format($job_total_ex,0);?></td>
												<td width="60"><?=number_format($job_ex_balance,0);?></td>
											</tr>
											<?
										}
									}
									?>
									<tr style="background: #95D1CC;font-weight: bold;text-align: right;">
										<td width="20"></td>
										<td width="100"></td>
										<td width="100"></td>							
										<td width="100"></td>
										<td width="80"></td>
										<td width="100"></td>
										<td width="100"></td>
										<td width="80">Buyer Total</td>
										<td width="80"><?=number_format($byr_order_qty,0);?></td>

										<td width="60"><?=number_format($byr_today_lay,0);?></td>
										<td width="60"><?=number_format($byr_total_lay,0);?></td>
										<td width="60"><?=number_format($byr_lay_balance,0);?></td>

										<td width="60"><?=number_format($byr_today_in,0);?></td>
										<td width="60"><?=number_format($byr_total_in,0);?></td>
										<td width="60"><?=number_format($byr_in_balance,0);?></td>

										<td width="60"><?=number_format($byr_today_out,0);?></td>
										<td width="60"><?=number_format($byr_total_out,0);?></td>
										<td width="60"><?=number_format($byr_out_balance ,0);?></td>

										<td width="60"><?=number_format($byr_today_poly,0);?></td>
										<td width="60"><?=number_format($byr_total_poly,0);?></td>
										<td width="60"><?=number_format($byr_poly_balance,0);?></td>

										<td width="60"><?=number_format($byr_today_fin,0);?></td>
										<td width="60"><?=number_format($byr_total_fin,0);?></td>
										<td width="60"><?=number_format($byr_fin_balance,0);?></td>
										
										<td width="60"><?=number_format($byr_today_ex,0);?></td>
										<td width="60"><?=number_format($byr_total_ex,0);?></td>
										<td width="60"><?=number_format($byr_ex_balance,0);?></td>
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
								<th width="100"></th>
								<th width="100"></th>	
								<th width="100"></th>
								<th width="80"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="80">Grand Total</th>
								<th width="80"><?=number_format($gr_order_qty,0);?></th>

								<th width="60"><?=number_format($gr_today_lay,0);?></th>
								<th width="60"><?=number_format($gr_total_lay,0);?></th>
								<th width="60"><?=number_format($gr_lay_balance,0);?></th>

								<th width="60"><?=number_format($gr_today_in,0);?></th>
								<th width="60"><?=number_format($gr_total_in,0);?></th>
								<th width="60"><?=number_format($gr_in_balance,0);?></th>

								<th width="60"><?=number_format($gr_today_out,0);?></th>
								<th width="60"><?=number_format($gr_total_out,0);?></th>
								<th width="60"><?=number_format($gr_out_balance ,0);?></th>

								<th width="60"><?=number_format($gr_today_poly,0);?></th>
								<th width="60"><?=number_format($gr_total_poly,0);?></th>
								<th width="60"><?=number_format($gr_poly_balance,0);?></th>

								<th width="60"><?=number_format($gr_today_fin,0);?></th>
								<th width="60"><?=number_format($gr_total_fin,0);?></th>
								<th width="60"><?=number_format($gr_fin_balance,0);?></th>
								
								<th width="60"><?=number_format($gr_today_ex,0);?></th>
								<th width="60"><?=number_format($gr_total_ex,0);?></th>
								<th width="60"><?=number_format($gr_ex_balance,0);?></th>
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

if($action=="remarks_popup")
{
	extract($_REQUEST); 
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'',''); 
 	list($buyer_id,$client_id,$job_no,$item_id) = explode("**", $search_string);
	
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
	$actual_resource_library=return_library_array( "select id,line_number from prod_resource_mst", "id", "line_number"  );
	$job_sql = sql_select("SELECT a.company_name,a.style_ref_no,a.buyer_name,b.po_number,b.id from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and b.job_no_mst='$job_no'");
	$po_id_array = array();
	foreach ($job_sql as $v) 
	{
		$po_id_array[$v['ID']] = $v['ID'];
	}
	$po_ids = implode(",",$po_id_array);
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
    <div style="width:480px">
	    <table width="100%">
	    	<tr>
	    		<th colspan="2" align="left" width="49%">Company : <? echo $company_library[$job_sql[0][csf('company_name')]];?></th>
	    		<th colspan="2" align="left" width="49%">Style : <? echo $job_sql[0][csf('style_ref_no')];?></th>
	    	</tr>
	    	<tr>
	    		<th colspan="2" align="left" width="49%">Buyer : <? echo $buyer_short_library[$job_sql[0][csf('buyer_name')]];?></th>
	    		<th colspan="2" align="left" width="49%">Po No. : <? echo $job_sql[0][csf('po_number')];?></th>
	    	</tr>
	    </table>
	</div>
    <fieldset style="width:480px">
        <legend>Cut and Lay</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="100">Date</td>
                        <td width="120">Production Qnty</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
				<?
                if($job_no!="") $job_cond=" and a.job_no='$job_no'"; else $job_cond="";
                if($item_id>0) $item_cond=" and b.gmt_item_id=$item_id"; else $item_cond="";
                
                $sql_cutAndLay= sql_select("SELECT a.remarks, max(a.entry_date) as production_date ,sum(c.size_qty) as production_quantity from  ppl_cut_lay_mst a,  ppl_cut_lay_dtls b, ppl_cut_lay_bundle c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id $job_cond $item_cond and c.order_id in($po_ids) group by a.remarks order by production_date");
				
                $i=1;
                $tot_lay_qty = 0;
                foreach($sql_cutAndLay as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],0); ?></td>
                        <td><? echo $row[csf('remarks')];?></td>
                    </tr>
                    <?
                    $i++;
                    $tot_lay_qty += $row[csf("production_quantity")];
                }
                ?>
                </tbody>
                <tfoot>
                	<th colspan="2">Total</th>
                	<th><? echo number_format($tot_lay_qty,0); ?></th>
                	<th></th>
                </tfoot>
            </table>
        </fieldset>



        <fieldset style="width:480px">
        <legend>Cutting</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="100">Date</td>
                        <td width="120">Production Qnty</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
				<?                
                if($job_no!="") $job_cond=" and c.job_no_mst='$job_no'"; else $job_cond="";
                if($item_id>0) $item_cond=" and c.item_number_id=$item_id"; else $item_cond="";
                
                $sql_cutting= sql_select("SELECT a.DELIVERY_MST_ID,a.production_date,d.remarks,sum(b.production_qnty) as production_quantity from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c,pro_gmts_cutting_qc_mst d 
                where a.delivery_mst_id=d.id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in($po_ids) $job_cond $item_cond and a.production_type='1' and b.production_type='1' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   group by a.DELIVERY_MST_ID,a.production_date,d.remarks order by a.production_date");
				
                $i=1;
                $tot_cut_qty = 0;
                foreach($sql_cutting as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],2); ?></td>
                        <td><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                    <?
                    $i++;
                    $tot_cut_qty += $row[csf("production_quantity")];
                }
                ?>
                </tbody>
                <tfoot>
                	<th colspan="2">Total</th>
                	<th><? echo number_format($tot_cut_qty,0); ?></th>
                	<th></th>
                </tfoot>
            </table>
        </fieldset>
        <fieldset style="width:480px">
        <legend>Print/Embr Issue</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="100">Date</td>
                        <td width="120">Production Qnty</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
                <?
                if($job_no!="") $job_cond=" and c.job_no_mst='$job_no'"; else $job_cond="";
                if($item_id>0) $item_cond=" and c.item_number_id=$item_id"; else $item_cond="";

                $sql_print_issue= sql_select("SELECT a.production_date,b.remarks_dtls,sum(b.production_qnty) as production_quantity 
                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c,pro_gmts_delivery_mst d 
                where a.delivery_mst_id=d.id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in($po_ids) $job_cond $item_cond and a.production_type='2' and b.production_type='2' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   group by a.production_date,b.remarks_dtls  order by a.production_date");
                if(count($sql_print_issue)==0)
                {
                	$sql_print_issue= sql_select("SELECT a.production_date,b.remarks_dtls as remarks,sum(b.production_qnty) as production_quantity 
                	from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                	where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in($po_ids) $job_cond $item_cond and a.production_type='2' and b.production_type='2' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   group by a.production_date,b.remarks_dtls  order by a.production_date");
                }
                $i=1;
                $tot_print_qty = 0;
                foreach($sql_print_issue as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],2); ?></td>
                        <td><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                    <?
                    $i++;
                    $tot_print_qty += $row[csf("production_quantity")];
                }
                ?>
                </tbody>
                <tfoot>
                	<th colspan="2">Total</th>
                	<th><? echo number_format($tot_print_qty,0); ?></th>
                	<th></th>
                </tfoot>
            </table>
        </fieldset>
        <fieldset style="width:480px">
        <legend>Print/Embr Receive</legend>
             <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="100">Date</td>
                        <td width="120">Production Qnty</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql_print_rcv=  sql_select("SELECT a.production_date,a.remarks,sum(b.production_qnty) as production_quantity 
                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id in($po_ids) $job_cond $item_cond and a.production_type='3' and b.production_type='3' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   group by a.production_date,a.remarks  order by a.production_date");
                
                $i=1;
                $tot_print_qty = 0;
                foreach($sql_print_rcv as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],2); ?></td>
                        <td><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                    <?
                    $i++;
                    $tot_print_qty += $row[csf("production_quantity")];
                }
                ?>
                </tbody>
                <tfoot>
                	<th colspan="2">Total</th>
                	<th><? echo number_format($tot_print_qty,0); ?></th>
                	<th></th>
                </tfoot>
            </table>
        </fieldset>
        <fieldset style="width:480px">
        <legend>Sewing Input</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="70">Date</td>
                        <td width="80">Production Qnty</td>
                        <td width="100">Floor</td>
                        <td width="70">Line No</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql_sewing_in= sql_select("SELECT a.production_date, a.floor_id, a.prod_reso_allo, a.sewing_line, sum(b.production_qnty) as production_quantity, max(a.remarks) as remarks 
                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in($po_ids) $job_cond $item_cond and a.production_type='4' and b.production_type='4' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   group by a.production_date, a.floor_id, a.prod_reso_allo, a.sewing_line  order by a.production_date , a.floor_id");
                $i=1;
                $tot_input = 0;
                foreach($sql_sewing_in as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],0); ?></td>
                        <td ><? echo $floor_library[$row[csf("floor_id")]]; ?></td>
                        <td style="padding-left:3px;">
                        <?
                        if($row[csf("prod_reso_allo")]==1)
                        {
                            $sewing_ling_arr=array_unique(explode(",",$actual_resource_library[$row[csf("sewing_line")]]));
                            $all_sewing_line="";
                            foreach($sewing_ling_arr as $line_id)
                            {
                                $all_sewing_line.=$line_library[$line_id].",";
                            }
                            $all_sewing_line=chop($all_sewing_line," , ");
                            echo $all_sewing_line;
                        }
                        else
                        {
                            echo $line_library[$row[csf("sewing_line")]];
                        }
                        ?>
                        </td>
                        <td><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                    <?
                    $i++;
                    $tot_input += $row[csf("production_quantity")];
                }
                ?>
                </tbody>
                <tfoot>
                	<th colspan="2">Total</th>
                	<th><? echo number_format($tot_input,0); ?></th>
                	<th></th>
                	<th></th>
                	<th></th>
                </tfoot>
            </table>
        </fieldset>
        <fieldset style="width:480px">
        <legend >Sewing Output</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="70">Date</td>
                        <td width="80">Production Qnty</td>
                        <td width="100">Floor</td>
                        <td width="70">Line No</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql_sewing_out= sql_select("SELECT a.production_date, a.floor_id, a.prod_reso_allo, a.sewing_line, sum(b.production_qnty) as production_quantity, max(a.remarks) as remarks 
                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in($po_ids) $job_cond $item_cond and a.production_type='5' and b.production_type='5' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1     group by  a.production_date, a.floor_id, a.prod_reso_allo, a.sewing_line order by a.production_date , a.floor_id");
                $i=1;
                $tot_output = 0;
                foreach($sql_sewing_out as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],0); ?></td>
                        <td ><? echo $floor_library[$row[csf("floor_id")]]; ?></td>
                        <td style="padding-left:3px;">
                        <?
                        if($row[csf("prod_reso_allo")]==1)
                        {
                            $sewing_ling_arr=array_unique(explode(",",$actual_resource_library[$row[csf("sewing_line")]]));
                            $all_sewing_line="";
                            foreach($sewing_ling_arr as $line_id)
                            {
                                $all_sewing_line.=$line_library[$line_id].",";
                            }
                            $all_sewing_line=chop($all_sewing_line," , ");
                            echo $all_sewing_line;
                        }
                        else
                        {
                            echo $line_library[$row[csf("sewing_line")]];
                        }
                        ?>
                        </td>
                        <td><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                    <?
                    $i++;
                    $tot_output += $row[csf("production_quantity")];
                }
                ?>
                </tbody>
                <tfoot>
                	<th colspan="2">Total</th>
                	<th><? echo number_format($tot_output,0); ?></th>
                	<th></th>
                	<th></th>
                	<th></th>
                </tfoot>
            </table>
        </fieldset>
        <fieldset style="width:480px">
        <legend>Poly</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="70">Date</td>
                        <td width="80">Production Qty</td>
                        <td width="100">Floor</td>
                        <td width="70">Line No</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql_sewing_out= sql_select("SELECT a.production_date, a.floor_id, a.prod_reso_allo, a.sewing_line, sum(b.production_qnty) as production_quantity, max(a.remarks) as remarks 
                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in($po_ids) $job_cond $item_cond and a.production_type='11' and b.production_type='11' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   group by  a.production_date, a.floor_id, a.prod_reso_allo, a.sewing_line order by a.production_date , a.floor_id");
                $i=1;
                $tot_poly = 0;
                foreach($sql_sewing_out as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],0); ?></td>
                        <td><? echo $floor_library[$row[csf("floor_id")]]; ?></td>
                        <td style="padding-left:3px;">
                        <?
                        if($row[csf("prod_reso_allo")]==1)
                        {
                            $sewing_ling_arr=array_unique(explode(",",$actual_resource_library[$row[csf("sewing_line")]]));
                            $all_sewing_line="";
                            foreach($sewing_ling_arr as $line_id)
                            {
                                $all_sewing_line.=$line_library[$line_id].",";
                            }
                            $all_sewing_line=chop($all_sewing_line," , ");
                            echo $all_sewing_line;
                        }
                        else
                        {
                            echo $line_library[$row[csf("sewing_line")]];
                        }
                        ?>
                        </td>
                        <td><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                    <?
                    $i++;
                    $tot_poly += $row[csf("production_quantity")];
                }
                ?>
                </tbody>
                <tfoot>
                	<th colspan="2">Total</th>
                	<th><? echo number_format($tot_poly,0); ?></th>
                	<th></th>
                	<th></th>
                	<th></th>
                </tfoot>
            </table>
        </fieldset>
        <fieldset style="width:480px">
        <legend>Finishing & Packing</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="100">Date</td>
                        <td width="100">Production Qnty</td>
                        <td width="100">Floor</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql_finish_out= sql_select("SELECT a.production_date, a.floor_id, sum(b.production_qnty) as production_quantity, max(a.remarks) as remarks 
                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in($po_ids) $job_cond $item_cond and a.production_type='8' and b.production_type='8' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1    group by a.production_date,a.floor_id order by a.production_date , a.floor_id");
                $i=1;
                $tot_fin = 0;
                foreach($sql_finish_out as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],0); ?></td>
                        <td ><? echo $floor_library[$row[csf("floor_id")]]; ?></td>
                        <td><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                    <?
                    $i++;
                    $tot_fin += $row[csf("production_quantity")];
                }
                ?>
                </tbody>
                <tfoot>
                	<th colspan="2">Total</th>
                	<th><? echo number_format($tot_fin,0); ?></th>
                	<th></th>
                	<th></th>
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