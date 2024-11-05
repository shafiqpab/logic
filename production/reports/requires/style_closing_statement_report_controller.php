<? 
//Dtls Button Created by Aziz

header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 0, "-- Select buyer --", 0, "","" );//load_drop_down( 'requires/daily_knitting_production_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );$location_cond
  exit();	 
}

if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_buyer_name','0','0','','0');\n";
    // echo "setTimeout[($('#floor_td a').attr('onclick','disappear_list(cbo_floor,0);getFloorId();') ,3000)];\n";
    exit();
}

if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";
//item style------------------------------//

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 130, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id=$data group by id,location_name  order by location_name","id,location_name", 1, "-- Select location --", $selected, "load_drop_down( 'requires/style_closing_statement_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );" );
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
				name += selected_name[i] + ',';
				style += selected_style[i] + ',';
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
			<fieldset style="width:760px;">
	            <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th class="must_entry_caption">Company Name</th>
	                    <th>Year</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="100">Job No</th>
	                    <th>Closing Date Range</th>
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
									echo create_drop_down( "cbo_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $company_name, "" );
								?>
	                        </td>                
	                        <td align="center">	
	                    	<?						
								echo create_drop_down( "cbo_year", 60, $year,"",1, "--Select--", date('Y'),'',0 );
							?>
	                        </td>                 
	                        <td align="center">	
	                    	<?
	                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 80, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>     
	                        <td align="center" id="search_by_td">				
	                            <input type="text" style="width:80px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
	                        </td> 
	                        <td>	                        	
	                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date">&nbsp;
	                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date">
	                        </td>
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**<?=$buyer_name;?>', 'search_list_view', 'search_div', 'style_closing_statement_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
	                    	</td>
	                    </tr>
	                    <tr>
	                    	<td colspan="6"><? echo load_month_buttons(1); ?></td>
	                    </tr>
	            	</tbody>
	           	</table>
	            <div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>        
	<script type="text/javascript">
		$("#cbo_company_name").attr('disabled',true);		
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
	
	if($data[6]=="")
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
		$buyer_id_cond=" and a.buyer_name in($data[6])";
	}
	
	$search_by=$data[1];
	if(str_replace("'", "", $data[2])!="")
	{
		$search_string="".trim($data[2])."";
	}

	if($search_by==1) 
		$search_field="a.job_no_prefix_num"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no";
	$search_cond="";
	if($search_string!="")	{$search_cond=" and $search_field='$search_string'";}
	$job_year =$data[3];
	
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

    if(str_replace("'", "", $data[4]) !="" && str_replace("'", "", $data[5]) !="")
    {   
    	// ===========================
    	$date_from = date('d-M-Y',strtotime($data[4]));
    	$date_to = date('d-M-Y',strtotime($data[5]));
    	$date_cond = " and c.ex_factory_date between '$date_from' and '$date_to'";
    }
    // ============================= get closing job ===============================
	$sql=sql_select( "SELECT b.job_no_mst, b.id from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst c where a.id=b.job_id and  b.id=c.po_break_down_id and a.status_active=1 and b.shiping_status=3 and a.company_name=$company_id $search_cond $buyer_id_cond $job_no_cond $job_year_cond $buyer_cond $date_cond" ); 
	if(count($sql)==0)
	{
		die('<div style="color:red;font-size:18px;">Data not found!');
	}
	$shipment_po_arr = array();
	$job_no_arr = array();
	foreach ($sql as $val) 
	{
		$shipment_po_arr[$val['JOB_NO_MST']][$val['ID']] = $val['ID'];
		$job_no_arr[$val['JOB_NO_MST']] = $val['JOB_NO_MST'];
	}

	$job_no_cond = where_con_using_array($job_no_arr,1,"a.job_no");
	$sql=sql_select( "SELECT a.job_no, b.id from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and b.status_active=1 $job_no_cond");  
	$all_po_arr = array();
	foreach ($sql as $v) 
	{
		$all_po_arr[$v['JOB_NO']][$v['ID']] = $v['ID'];
	}
	// echo "<pre>";print_r($shipment_po_arr);die();
	$closing_job_arr = array();
	foreach ($all_po_arr as $job => $job_data) 
	{
		if(count($all_po_arr[$job])==count($shipment_po_arr[$job]))
		{
			$closing_job_arr[$job] = $job;
		}
	}

	// print_r($closing_job_arr);
 	$job_no_cond = where_con_using_array($closing_job_arr,1,"a.job_no");
 	// ============================= end closing job ===============================

	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	
	$sql= "SELECT a.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $job_no_cond $job_year_cond $buyer_cond $job_no_cond group by a.id,
         a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no order by a.id desc"; 
    // echo $sql;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "100,100,50,100","550","220",0, $sql , "js_set_value", "id,job_no_prefix_num,style_ref_no","",1,"company_name,buyer_name,0,0,0,0",$arr,"company_name,buyer_name,year,job_no,style_ref_no","",'','0,0,0,0,0','',1) ;
   exit(); 
}


if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_name 		= str_replace("'","",$cbo_company_name);
	$buyer_name 		= str_replace("'","",$cbo_buyer_name);
	$year 				= str_replace("'","",$cbo_year);
	$hidden_job_id 		= str_replace("'","",$hidden_job_id);
	$txt_job_no 		= str_replace("'","",$txt_job_no);
	$txt_style_no 		= str_replace("'","",$txt_style_no);
	$date_from 			= str_replace("'","",$txt_date_from);
	$date_to 			= str_replace("'","",$txt_date_to);	
	$type 				= str_replace("'","",$type);	
	
	$sql_cond .= ($buyer_name != "") 		? " and a.buyer_name in($buyer_name)" : "";
	$sql_cond .= ($year != 0) 		? " and to_char(a.insert_date,'YYYY')=$year" : "";
	$sql_cond .= ($hidden_job_id != "") 	? " and a.id in($hidden_job_id)" : "";
	$sql_cond .= ($company_name != 0) 	? " and a.company_name=$company_name" : "";
	if($hidden_job_id=="")
	{
		$sql_cond .= ($txt_job_no != "") 	? " and a.job_no_prefix_num in($txt_job_no)" : "";
		// $sql_cond .= ($txt_style_no != "") 	? " and a.style_ref_no like '%$txt_style_no%'" : "";
	}

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
        $date_cond = " and c.ex_factory_date between '$start_date' and '$end_date'";
    }

	// echo $sql_cond;

	$company_lib=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$location_lib=return_library_array( "SELECT id, location_name from lib_location", "id", "location_name"  );
	$floor_lib=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );
    $season_lib = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", "id", "season_name");
    $buyer_lib  = return_library_array("select id, buyer_name from  lib_buyer", "id", "buyer_name");
	$color_lib=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_lib=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$lineArr = return_library_array("select a.id,a.line_name from lib_sewing_line a","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	if($type==1)
	{	

	    // ============================= get closing job ===============================
		$sql=sql_select( "SELECT a.style_ref_no,b.job_no_mst, b.id from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst c where a.id=b.job_id and  b.id=c.po_break_down_id and a.status_active=1 and b.shiping_status=3 and a.company_name=$company_name $sql_cond $date_cond" );  
		if(count($sql)==0)
		{
			die('<div style="color:red;font-size:18px;">Data not found!');
		}
		$shipment_po_arr = array();
		$job_no_arr = array();
		foreach ($sql as $val) 
		{
			$shipment_po_arr[$val['JOB_NO_MST']][$val['ID']] = $val['ID'];
			$job_no_arr[$val['JOB_NO_MST']] = $val['JOB_NO_MST'];
			$job_no_style_arr[$val['JOB_NO_MST']] = $val['STYLE_REF_NO'];
		}

		$job_no_cond = where_con_using_array($job_no_arr,1,"a.job_no");
		$sql=sql_select( "SELECT a.job_no, b.id from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and b.status_active=1 $job_no_cond");  
		$all_po_arr = array();
		foreach ($sql as $v) 
		{
			$all_po_arr[$v['JOB_NO']][$v['ID']] = $v['ID'];
		}
		// echo "<pre>";print_r($shipment_po_arr);die();
		$closing_job_arr = array();
		foreach ($all_po_arr as $job => $job_data) 
		{
			if(count($all_po_arr[$job])==count($shipment_po_arr[$job]))
			{
				$closing_job_arr[$job] = $job;
			//	$job_no_summ_arr[$val['JOB_NO_MST']] = $val['JOB_NO_MST'];
			}
		}

		//  print_r($closing_job_arr);
	 	$job_no_cond = where_con_using_array($closing_job_arr,1,"a.job_no");
		/*==========================================================================================/
		/											lay data 										/
		/==========================================================================================*/ 	
			
		 $sqlLay="SELECT a.job_no,a.job_no_prefix_num,b.id,b.po_quantity, e.size_qty as lay_qty from wo_po_details_master a,wo_po_break_down b,ppl_cut_lay_mst d,ppl_cut_lay_bundle e where a.job_no=d.job_no and a.id=b.job_id  and d.id=e.mst_id and b.id=e.order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.shiping_status=3 and e.size_qty>0 $sql_cond $job_no_cond";		
		// echo $sqlLay;die;
		$layData = sql_select($sqlLay);
		$tot_po_qty = 0;
		$tot_lay_qty = 0;$lay_cutQty_arr=array();
		foreach ($layData as  $val) 
		{
			//$tot_lay_qty += $val['LAY_QTY'];			
			$po_id_arr[$val['ID']] = $val['ID'];
			$lay_cutQty_arr[$val['JOB_NO']]+= $val['LAY_QTY'];
		}
		$po_id_cond = where_con_using_array($po_id_arr,0,"id");
		// $tot_po_qty = return_field_value( "sum(b.po_quantity*a.total_set_qnty)", "wo_po_details_master a, wo_po_break_down b"," a.id=b.job_id and b.status_active=1 $job_no_cond");
		$sql = "SELECT a.job_no,(b.po_quantity*a.total_set_qnty) as qty from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and b.status_active=1 $job_no_cond";
		// echo $sql; 
		$res = sql_select($sql);
		foreach ($res as $val) 
		{
			$poQty_arr[$val['JOB_NO']]+= $val['QTY'];
		}
		/*==========================================================================================/
		/											gmts data 										/
		/==========================================================================================*/	
		$sql=" SELECT a.job_no,d.production_type,d.embel_name,e.production_qnty,e.reject_qty,e.alter_qty,e.spot_qty

			from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.production_type in(1,3,4,5,7,8,11,15)  and b.shiping_status=3 $sql_cond $job_no_cond";		
		// echo $sql;die;
		$sql_res = sql_select($sql);
		$gmts_data_array = array();
		foreach ($sql_res as $val) 
		{
			$job_no=$val['JOB_NO'];
			$gmts_data_array[$job_no][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['qty'] += $val['PRODUCTION_QNTY'];		
			$gmts_data_array[$job_no][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['rej_qty'] += $val['REJECT_QTY'];		
			$gmts_data_array[$job_no][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['alter_qty'] += $val['ALTER_QTY'];		
			$gmts_data_array[$job_no][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['spot_qty'] += $val['SPOT_QTY'];		
		}
		/*==========================================================================================/
		/										gmts defect data 									/
		/==========================================================================================*/	
		$sql=" SELECT  a.job_no,(e.defect_qty) as defect_qty
 
			from wo_po_details_master a,wo_po_break_down b,pro_garments_production_mst d,pro_gmts_prod_dft e where a.id=b.job_id and b.id=d.po_break_down_id and d.id=e.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.production_type in(8,11) and b.shiping_status=3 $sql_cond $job_no_cond";		
		// echo $sql;die;
		$sql_res = sql_select($sql);
		$dft_data_array = array();
		foreach ($sql_res as $val) 
		{
			$job_no=$val['JOB_NO'];
			//$tot_dft_qty += $val['DEFECT_QTY'];	
			$dft_qtyArr[$job_no]+=$val['PRODUCTION_QNTY'];
		}
		/*==========================================================================================/
		/										shipment data 										/
		/==========================================================================================*/	
		$sql=" SELECT a.job_no,(case when d.entry_form !=85 then e.production_qnty else 0 end) as production_qnty,
		(case when d.entry_form =85 then e.production_qnty else 0 end) as return_qnty

			from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_ex_factory_mst d,pro_ex_factory_dtls e where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.shiping_status=3 $sql_cond $job_no_cond";		
		// echo $sql;die;
		$sql_res = sql_select($sql);
		$ship_qty = 0;
		$ship_rtn_qty = 0;
		foreach ($sql_res as $val) 
		{
			$job_no=$val['JOB_NO'];
			$ship_qtyArr[$job_no]+=$val['PRODUCTION_QNTY'];
			$ship_ret_qtyArr[$job_no]+=$val['RETURN_QNTY'];
			
			//$ship_qty += $val['PRODUCTION_QNTY'];
			//$ship_rtn_qty += $val['RETURN_QNTY'];
		}
		/*
		$excess_cut = max($tot_lay_qty - $tot_po_qty,0);
		$cut_rej_qty = $gmts_data_array[1][0]['rej_qty'];
		$cut_miss = max($gmts_data_array[1][0]['qty'] - ($gmts_data_array[4][0]['qty']+$gmts_data_array[3][1]['rej_qty']+$gmts_data_array[3][2]['rej_qty']),0);
		$cut_qty = $gmts_data_array[1][0]['qty'];
		
		$print_rej_qty = $gmts_data_array[3][1]['rej_qty'];
		$emb_rej_qty = $gmts_data_array[3][2]['rej_qty'];
		$input_qty = $gmts_data_array[4][0]['qty'];
		$output_qty = $gmts_data_array[5][0]['qty'];
		$sewing_rej_qty = $gmts_data_array[5][0]['rej_qty'];
		$sewing_miss = $input_qty - ($output_qty+$sewing_rej_qty);
		$finish_rej_qty = $gmts_data_array[8][0]['rej_qty']+$gmts_data_array[7][0]['rej_qty']+$gmts_data_array[15][0]['rej_qty']+$gmts_data_array[11][0]['rej_qty'];
		$finish_qty = $gmts_data_array[8][0]['qty'];
		$alter_spot_qty = $gmts_data_array[5][0]['alter_qty']+$gmts_data_array[5][0]['spot_qty'];
		$org_ship_qty = $ship_qty-$ship_rtn_qty;
		$short_ship = max($tot_po_qty - $org_ship_qty,0);
		$excess_ship = max($org_ship_qty - $tot_po_qty,0);

		$left_over_qty = $finish_qty - $org_ship_qty;
		$tot_rej = $alter_spot_qty+$finish_rej_qty+$sewing_rej_qty+$emb_rej_qty+$print_rej_qty+$cut_rej_qty;
		$lay_to_ship = ($tot_lay_qty>0) ? ($org_ship_qty/$tot_lay_qty)*100 : 0;
		$cut_to_ship = ($cut_qty>0) ? ($org_ship_qty/$cut_qty)*100 : 0;
		$fin_miss = $output_qty - ($finish_qty+$finish_rej_qty);
		$tot_miss = max($fin_miss+$sewing_miss+$cut_miss,0);


		// ===========================================================
		$excess_cut_prsnt = ($excess_cut/$tot_po_qty)*100;
		$cut_rej_prsnt = ($cut_rej_qty/$cut_qty)*100;
		$cut_miss_prsnt = ($cut_miss/$cut_qty)*100;
		$cut_prsnt = max((($cut_qty-$tot_po_qty)/$tot_po_qty)*100,0);
		$sew_rej_prsnt = ($sewing_rej_qty/$input_qty)*100;
		$sew_miss_prsnt = ($sewing_miss/$input_qty)*100;
		$short_ship_prsnt = ($short_ship/$tot_po_qty)*100;
		$excess_ship_prsnt = ($excess_ship/$tot_po_qty)*100;
		$left_over_prsnt = ($left_over_qty/$cut_qty)*100;
		$tot_miss_prsnt = ($tot_miss/$cut_qty)*100;
		$tot_rej_prsnt = ($tot_rej/$cut_qty)*100;*/

		$tbl_width = 1640;	
		ob_start();
		?>
		<fieldset style="width:<?=$tbl_width+20;?>px;">
			
			<div style="width:<?=$tbl_width+20;?>px;">
				<table width="<?=$tbl_width;?>"  cellspacing="0">
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:18px; font-weight:bold" >Job Closing Report [Summary]</td>
					</tr>
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" ><?=$company_lib[$company_name];?></td>
					</tr>
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:15px; font-weight:bold" >From <?=$date_from;?> To <?=$date_to;?></td>
					</tr>
				</table>
                <?
				$tot_summ_lay_cut_qty=$tot_summ_po_qty=$tot_summ_cut_miss=$tot_summ_cut_rej_qty=$tot_summ_cut_miss=$tot_summ_excess_cut_bal=$tot_summ_cut_qty=$tot_summ_print_rej_qty=$tot_summ_emb_rej_qty=$tot_summ_input_qty=$$tot_summ_sewing_rej_qty=$tot_summ_sewing_miss=$tot_summ_output_qty=$tot_summ_sewing_miss=$tot_summ_tot_dft_qty=$tot_summ_fin_miss=$tot_summ_cut_to_ship=$tot_summ_lay_to_ship=$tot_summ_tot_rej=$tot_summ_org_ship_qty=0;
                 foreach($closing_job_arr as $job_no=>$row)
				{
					$summ_tot_po_qty=$poQty_arr[$job_no];
					//$tot_dft_qty=$dft_qtyArr[$job_no];
					$summ_ship_qty=$ship_qtyArr[$job_no];
					$summ_ship_rtn_qty=$ship_ret_qtyArr[$job_no];
					$summ_cut_rej_qty = $gmts_data_array[$job_no][1][0]['rej_qty'];
					$summ_cut_miss = max($gmts_data_array[$job_no][1][0]['qty'] - ($gmts_data_array[$job_no][4][0]['qty']+$gmts_data_array[$job_no][3][1]['rej_qty']+$gmts_data_array[$job_no][3][2]['rej_qty']),0);
					$summ_excess_cut_bal = max($lay_cutQty_arr[$job_no] - $summ_tot_po_qty,0);
					$summ_cut_qty = $gmts_data_array[$job_no][1][0]['qty'];
					
					$summ_print_rej_qty = $gmts_data_array[$job_no][3][1]['rej_qty'];
					$summ_emb_rej_qty = $gmts_data_array[$job_no][3][2]['rej_qty'];
					$summ_input_qty = $gmts_data_array[$job_no][4][0]['qty'];
					$summ_output_qty = $gmts_data_array[$job_no][5][0]['qty'];
					$summ_sewing_rej_qty = $gmts_data_array[$job_no][5][0]['rej_qty'];
					$summ_sewing_miss = $summ_input_qty - ($summ_output_qty+$summ_sewing_rej_qty);
					$summ_finish_rej_qty = $gmts_data_array[$job_no][8][0]['rej_qty']+$gmts_data_array[$job_no][7][0]['rej_qty']+$gmts_data_array[$job_no][15][0]['rej_qty']+$gmts_data_array[$job_no][11][0]['rej_qty'];
					
					$summ_tot_dft_qty=$dft_qtyArr[$job_no];
					//$summ_ship_qty=$ship_qtyArr[$job_no];
					//$summ_ship_rtn_qty=$ship_ret_qtyArr[$job_no];
							
					$summ_finish_qty = $gmts_data_array[$job_no][8][0]['qty'];
					$summ_alter_spot_qty = $gmts_data_array[$job_no][5][0]['alter_qty']+$gmts_data_array[$job_no][5][0]['spot_qty'];
					$summ_org_ship_qty = $summ_ship_qty-$summ_ship_rtn_qty;
					//echo $summ_ship_qty.'='.$summ_ship_rtn_qty.'<br>';
					//$org_ship_qty = $ship_qty-$ship_rtn_qty;
					$summ_short_ship = max($summ_tot_po_qty - $summ_org_ship_qty,0);
					$summ_excess_ship = max($summ_org_ship_qty - $summ_tot_po_qty,0);
			
					 $summ_left_over_qty = $summ_finish_qty - $summ_org_ship_qty;
					$summ_tot_rej = $summ_alter_spot_qty+$summ_finish_rej_qty+$summ_sewing_rej_qty+$summ_emb_rej_qty+$summ_print_rej_qty+$summ_cut_rej_qty;
					$summ_lay_to_ship = ($lay_cutQty_arr[$job_no]>0) ? ($summ_org_ship_qty/$lay_cutQty_arr[$job_no])*100 : 0;
					$summ_fin_miss = $summ_output_qty - ($summ_finish_qty+$summ_finish_rej_qty);
					$summ_tot_miss = max($summ_fin_miss+$summ_sewing_miss+$summ_cut_miss,0);
					$summ_cut_to_ship = ($summ_cut_qty>0) ? ($summ_org_ship_qty/$summ_cut_qty)*100 : 0;
					//$cut_to_ship = ($cut_qty>0) ? ($org_ship_qty/$cut_qty)*100 : 0;
					
					$tot_summ_lay_cut_qty+=$lay_cutQty_arr[$job_no];
					$tot_summ_po_qty+=$poQty_arr[$job_no];
					//$tot_summ_org_ship_qty+= $summ_ship_qty-$summ_ship_rtn_qty;
					$tot_summ_excess_cut_bal+= $summ_excess_cut_bal;
					$tot_summ_cut_rej_qty+= $summ_cut_rej_qty;
					$tot_summ_cut_miss+= $summ_cut_miss;
					$tot_summ_cut_qty+= $summ_cut_qty;
					
					$tot_summ_print_rej_qty+= $summ_print_rej_qty;
					$tot_summ_emb_rej_qty+= $summ_emb_rej_qty;
					$tot_summ_input_qty+= $summ_input_qty;
					$tot_summ_sewing_rej_qty+= $summ_sewing_rej_qty;
					$tot_summ_sewing_miss+= $summ_sewing_miss;
					$tot_summ_output_qty+= $summ_output_qty;
					$tot_summ_finish_rej_qty+= $summ_finish_rej_qty;
					$tot_summ_tot_dft_qty+= $summ_tot_dft_qty;
					$tot_summ_fin_miss+= $summ_fin_miss;
					$tot_summ_finish_qty+= $summ_finish_qty;
					$tot_summ_org_ship_qty+= $summ_org_ship_qty;
					$tot_summ_short_ship+= $summ_short_ship;
					$tot_summ_excess_ship+= $summ_excess_ship;
					$tot_summ_left_over_qty+= $summ_left_over_qty;
					$tot_summ_tot_miss+= $summ_tot_miss;
					$tot_summ_tot_rej+= $summ_tot_rej;
					$tot_summ_lay_to_ship+= $summ_lay_to_ship;
					$tot_summ_cut_to_ship+= $summ_cut_to_ship;
				}
				?>		

				<div style="margin: 5px 0;width: 300px;float: left;">
					<table border="1" cellpadding="0" cellspacing="0" class="rpt_table">
						<tr><td width="200"><b>Total Order Quantity[Pcs]</b></td><td align="right" width="100"><?=number_format($tot_summ_po_qty,0);?></td></tr>
						<tr><td width="200"><b>Total Cutting</b></td><td align="right" width="100"><?=number_format($tot_summ_lay_cut_qty,0);?></td></tr>
						<tr><td width="200"><b>Total Shipment Quantity</b></td><td align="right" width="100"><?=number_format($summ_org_ship_qty,0);?></td></tr>
						<tr><td width="200"><b>Average Excess/Short Quantity</b></td><td align="right" width="100"><?=number_format($a,0);?></td></tr>
						<tr><td width="200"><b>Total Reject</b></td><td align="right" width="100"><?=number_format($summ_tot_rej,0);?></td></tr>
						<tr><td width="200"><b>Total Missing Quantity</b></td><td align="right" width="100"><?=number_format($summ_tot_miss,0);?></td></tr>
					</table>
					
				</div>	
				<div style="width:<?=$tbl_width+20;?>px; float:left;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width;?>" class="rpt_table" align="left">
						<thead>
							<tr>
								<th width="70"></th>	
                                <th width="70"></th>	
                                <th width="60"><?=$tot_summ_lay_cut_qty;?></th>							
								<th width="60"><?=$tot_summ_po_qty;?></th>								
								<th width="60" title="Lay Qty - PO Qty"><?=$tot_summ_excess_cut_bal;?></th>
								<th width="60"> <?=$tot_summ_cut_rej_qty;?></th>
								<th width="60" title="Cutting QC - (Input+Print Rej+Emb Rej)"><?=$tot_summ_cut_miss;?></th>
								<th width="60"><?=$tot_summ_cut_qty;?></th>
								<th width="60"><?=$tot_summ_print_rej_qty;?></th>
								<th width="60"><?=fn_number_format($tot_summ_emb_rej_qty,0);?></th>
								<th width="60"><?=fn_number_format($tot_summ_input_qty,0);?></th>
								<th width="60"><?=fn_number_format($tot_summ_sewing_rej_qty,0);?></th>
								<th width="60" title="Input - (Sew Output+Sew Rej)"><?=$summ_sewing_miss;?></th>
								<th width="60"><?=fn_number_format($tot_summ_output_qty,0);?></th>
								<th width="60"><?=fn_number_format($tot_summ_finish_rej_qty,0);?></th>
								<th width="60" title="Poly Dft Qty+Finishing Dft Qty"><?=fn_number_format($tot_summ_tot_dft_qty,0);?></th>
								<th width="60" title="Sewingout Qty-(Finishing Qty+Finishing Rej)"><?=fn_number_format($tot_summ_fin_miss,0);?></th>
								<th width="60"><?=fn_number_format($tot_summ_finish_qty,0);?></th>
								<th width="60" title="Shipment Qty - Shipment Return"><?=fn_number_format($tot_summ_org_ship_qty,0);?></th>
								<th width="60"><?=fn_number_format($tot_summ_short_ship,0);?></th>
								<th width="60" title="PO Qty - Shipment Qty"><?=fn_number_format($tot_summ_short_ship,0);?></th>
								<th width="60" title="Shipment Qty - PO Qty"> <?=fn_number_format($tot_summ_excess_ship,0);?></th>
                                
								<th width="60" title="Finishing Qty - Shipment Qty"><?=fn_number_format($tot_summ_left_over_qty,0);?></th>
								<th width="60" title="Cutting Missing + Sewing Missing + Finishing Missing"><?=fn_number_format($tot_summ_tot_miss,0);?></th>
								<th width="60" title="Cutting+Print+Emb+Sewing+Finishing"><?=fn_number_format($tot_summ_tot_rej,0);?></th>
								<th width="60" title="(Shipment/Lay Qty)*100"><?=fn_number_format($tot_summ_lay_to_ship,0);?></th>
								<th width="60" title="(Shipment/Cutting Qty)*100"><?=fn_number_format($tot_summ_cut_to_ship,0);?></th>
							</tr>	
                            
                            <tr>
								<th width="70">Job Number</th>	
                                <th width="70"> Style Number</th>	
                                <th width="60">Total Cut Qty</th>							
								<th width="60">Order Qty[Pcs]</th>								
								<th width="60" title="Lay Qty - PO Qty">Excess Cutting</th>
								<th width="60">Reject (Cutting) </th>
								<th width="60" title="Cutting QC - (Input+Print Rej+Emb Rej)">Missing Cutting</th>
								<th width="60">Cutting Output</th>
								<th width="60">Print Reject Qty</th>
								<th width="60">Emb. Reject</th>
								<th width="60">Sewing Input</th>
								<th width="60">Sewing Reject</th>
								<th width="60" title="Input - (Sew Output+Sew Rej)">Missing Sewing</th>
								<th width="60">Sewing Output</th>
								<th width="60">Reject Qty (Finishing)</th>
								<th width="60" title="Poly Dft Qty+Finishing Dft Qty">Spot / Sheding Prob  Qty</th>
								<th width="60" title="Sewingout Qty-(Finishing Qty+Finishing Rej)">Missing (Finishing)</th>
								<th width="60">Packing</th>
								<th width="60" title="Shipment Qty - Shipment Return">Ship Qty</th>
								<th width="60">Average Excess Shipment on Order Quantity</th>
								<th width="60" title="PO Qty - Shipment Qty"> Short Shipment Qty.</th>
								<th width="60" title="Shipment Qty - PO Qty"> Excess Shipment Qty.</th>
								<th width="60" title="Finishing Qty - Shipment Qty">Leftover</th>
								<th width="60" title="Cutting Missing + Sewing Missing + Finishing Missing">Total Missing</th>
								<th width="60" title="Cutting+Print+Emb+Sewing+Finishing">Total Reject</th>
								<th width="60" title="(Shipment/Lay Qty)*100">Shipment % on  Total Cutting</th>
								<th width="60" title="(Shipment/Cutting Qty)*100">Shipment % on Cutting Output</th>
							</tr>								
						</thead>
						<tbody>
                        <?
						$k=1;$total_lay_qty=$total_po_qty=$total_ex_cut_qty=$total_cut_rej_qty=$total_cut_miss=$total_cut_qty=$total_print_rej_qty=$total_emb_rej_qty=$total_sewing_rej_qty=$total_sewing_miss=$total_finish_rej_qty=$total_output_qty=$total_finish_rej_qty=$total_tot_dft_qty=$total_fin_miss=$total_tot_dft_qty=$total_cut_to_ship=$total_lay_to_ship=$total_cut_to_ship=$total_tot_rej=$total_input_qty=$total_input_qty=0;
                        foreach($closing_job_arr as $job_no=>$row)
						{
							$cut_rej_qty=0;$cut_miss=0;$cut_qty=0;$print_rej_qty=0;$emb_rej_qty=0;$input_qty=0;$output_qty=0;$sewing_rej_qty=0;$sewing_miss=0;$finish_rej_qty=0;$finish_qty=0;$alter_spot_qty=0;$alter_spot_qty=0;$org_ship_qty=0;$short_ship=0;$excess_ship=0;$left_over_qty=0;$tot_rej=0;$lay_to_ship=0;$cut_to_ship=0;$fin_miss=0;$tot_miss=0;
							$tot_po_qty=0;
							
							$bgcolor=($k%2==0)?"#E9F3FF":"#FFFFFF";	
							$lay_cut_qty=$lay_cutQty_arr[$job_no];
							$tot_po_qty=$poQty_arr[$job_no];
							$excess_cut_bal = max($lay_cut_qty - $tot_po_qty,0);
							
							$cut_rej_qty = $gmts_data_array[$job_no][1][0]['rej_qty'];
							$cut_miss = max($gmts_data_array[$job_no][1][0]['qty'] - ($gmts_data_array[$job_no][4][0]['qty']+$gmts_data_array[$job_no][3][1]['rej_qty']+$gmts_data_array[$job_no][3][2]['rej_qty']),0);
							$cut_qty = $gmts_data_array[$job_no][1][0]['qty'];
							
							$print_rej_qty = $gmts_data_array[$job_no][3][1]['rej_qty'];
							$emb_rej_qty = $gmts_data_array[$job_no][3][2]['rej_qty'];
							$input_qty = $gmts_data_array[$job_no][4][0]['qty'];
							$output_qty = $gmts_data_array[$job_no][5][0]['qty'];
							$sewing_rej_qty = $gmts_data_array[$job_no][5][0]['rej_qty'];
							$sewing_miss = $input_qty - ($output_qty+$sewing_rej_qty);
							$finish_rej_qty = $gmts_data_array[$job_no][8][0]['rej_qty']+$gmts_data_array[$job_no][7][0]['rej_qty']+$gmts_data_array[$job_no][15][0]['rej_qty']+$gmts_data_array[$job_no][11][0]['rej_qty'];
							
							$tot_dft_qty=$dft_qtyArr[$job_no];
							$ship_qty=$ship_qtyArr[$job_no];
							$ship_rtn_qty=$ship_ret_qtyArr[$job_no];
						
							$finish_qty = $gmts_data_array[$job_no][8][0]['qty'];
							$alter_spot_qty = $gmts_data_array[$job_no][5][0]['alter_qty']+$gmts_data_array[$job_no][5][0]['spot_qty'];
							$org_ship_qty = $ship_qty-$ship_rtn_qty;
							$short_ship = max($tot_po_qty - $org_ship_qty,0);
						//	echo $tot_po_qty.'='.$org_ship_qty.'='.$short_ship.'<br>';
							$excess_ship = max($org_ship_qty - $tot_po_qty,0);
					
							$left_over_qty = $finish_qty - $org_ship_qty;
							$tot_rej = $alter_spot_qty+$finish_rej_qty+$sewing_rej_qty+$emb_rej_qty+$print_rej_qty+$cut_rej_qty;
							$lay_to_ship = ($lay_cut_qty>0) ? ($org_ship_qty/$lay_cut_qty)*100 : 0;
							$cut_to_ship = ($cut_qty>0) ? ($org_ship_qty/$cut_qty)*100 : 0;
							$fin_miss = $output_qty - ($finish_qty+$finish_rej_qty);
							$tot_miss = max($fin_miss+$sewing_miss+$cut_miss,0);
							
							//============
							$excess_cut_prsnt = ($excess_cut/$tot_po_qty)*100;
							$cut_rej_prsnt = ($cut_rej_qty/$cut_qty)*100;
							$cut_miss_prsnt = ($cut_miss/$cut_qty)*100;
							$cut_prsnt = max((($cut_qty-$tot_po_qty)/$tot_po_qty)*100,0);
							$sew_rej_prsnt = ($sewing_rej_qty/$input_qty)*100;
							$sew_miss_prsnt = ($sewing_miss/$input_qty)*100;
							$short_ship_prsnt = ($short_ship/$tot_po_qty)*100;
							$excess_ship_prsnt = ($excess_ship/$tot_po_qty)*100;
							$left_over_prsnt = ($left_over_qty/$cut_qty)*100;
							$tot_miss_prsnt = ($tot_miss/$cut_qty)*100;
							$tot_rej_prsnt = ($tot_rej/$cut_qty)*100;
						?>
							<tr bgcolor="<? echo $bgcolor;?>" id="trsumm_<?= $k;?>" onClick="change_color('trsumm_<?= $k; ?>','<?= $bgcolor; ?>')">
								<td align="center"><?=$job_no;?></td>
                                <td align="center"><?=$job_no_style_arr[$job_no];?></td>
                                
                                <td align="right"><?=fn_number_format($lay_cut_qty,0);?></td>
								<td align="right"><?=fn_number_format($tot_po_qty,0);?></td>
								<td align="right" title="LayCut-POQty"><?=fn_number_format($excess_cut_bal,0);?></td>
								<td align="right"><?=fn_number_format($cut_rej_qty,0);?></td>
								<td align="right"><?=fn_number_format($cut_miss,0);?></td>
								<td align="right"><?=fn_number_format($cut_qty,0);?></td>
								<td align="right"><?=fn_number_format($print_rej_qty,0);?></td>
								<td align="right"><?=fn_number_format($emb_rej_qty,0);?></td>
								<td align="right"><?=fn_number_format($input_qty,0);?></td>
								<td align="right"><?=fn_number_format($sewing_rej_qty,0);?></td>
								<td align="right"><?=fn_number_format($sewing_miss,0);?></td>
								<td align="right"><?=fn_number_format($output_qty,0);?></td>
								<td align="right"><?=fn_number_format($finish_rej_qty,0);?></td>
								<td align="right"><?=fn_number_format($tot_dft_qty,0);?></td>
								<td align="right"><?=fn_number_format($fin_miss,0);?></td>
								<td align="right"><?=fn_number_format($finish_qty,0);?></td>
								<td align="right"><?=fn_number_format($org_ship_qty,0);?></td>
								<td align="right" title="PoQty-Ord ShipQty">T<?=fn_number_format($short_ship,0);?></td>
								<td align="right"><?=fn_number_format($short_ship,0);?></td>
								<td align="right"><?=fn_number_format($excess_ship,0);?></td>
								<td align="right"><?=fn_number_format($left_over_qty,0);?></td>
								<td align="right"><?=fn_number_format($tot_miss,0);?></td>
								<td align="right"><?=fn_number_format($tot_rej,0);?></td>
								<td align="right"  title="Ord ShipQty/Lay CutQty*100"><?=fn_number_format($lay_to_ship,2);?></td>
								<td align="right"><?=fn_number_format($cut_to_ship,2);?></td>
							</tr>
                            <?
							$k++;
							$total_lay_qty+=$lay_cut_qty;
							$total_po_qty+=$tot_po_qty;
							$total_ex_cut_qty+=$excess_cut_bal;
							$total_cut_rej_qty+=$cut_rej_qty;
							$total_cut_miss+=$cut_miss;
							$total_cut_qty+=$cut_qty;
							$total_print_rej_qty+=$print_rej_qty;
							$total_emb_rej_qty+=$emb_rej_qty;
							$total_input_qty+=$input_qty;
							$total_sewing_rej_qty+=$sewing_rej_qty;
							$total_sewing_miss+=$sewing_miss;
							$total_output_qty+=$output_qty;
							$total_finish_rej_qty+=$finish_rej_qty;
							$total_tot_dft_qty+=$tot_dft_qty;
							$total_fin_miss+=$fin_miss;
							$total_finish_qty+=$finish_qty;
							$total_org_ship_qty+=$org_ship_qty;
							$total_short_ship+=$short_ship;
							$total_short_ship_prsnt+=$short_ship_prsnt;
							$total_excess_ship+=$excess_ship;
							$total_left_over_qty+=$left_over_qty;
							$total_tot_miss+=$tot_miss;
							$total_tot_rej+=$tot_rej;
							$total_lay_to_ship+=$lay_to_ship;
							$total_cut_to_ship+=$cut_to_ship;
						}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th></th>
								<th></th>
                              	 <th><?=fn_number_format($total_lay_qty,0);?></th>
                                 <th><?=fn_number_format($total_po_qty,0);?></th>
								 
								<th><?=fn_number_format($total_ex_cut_qty,0);?></th>
								<th><?=fn_number_format($total_cut_rej_qty,0);?></th>
								<th><?=fn_number_format($total_cut_miss,0);?></th>
								<th><?=fn_number_format($total_cut_qty,0);?></th>
								<th><?=fn_number_format($total_print_rej_qty,0);?></th>
								<th><?=fn_number_format($total_emb_rej_qty,0);?></th>
                                <th><?=fn_number_format($total_input_qty,0);?></th>
								<th><?=fn_number_format($total_sewing_rej_qty,0);?></th>
								<th><?=fn_number_format($total_sewing_miss,0);?></th>
								<th><?=fn_number_format($total_output_qty,0);?></th>
                                
								<th><?=fn_number_format($total_finish_rej_qty,0);?></th>
								<th><?=fn_number_format($total_tot_dft_qty,0);?></th>
								<th><?=fn_number_format($total_fin_miss,0);?></th>
                                <th><?=fn_number_format($total_finish_qty,0);?></th>
								<th><?=fn_number_format($total_org_ship_qty,0);?></th>
								<th><?=fn_number_format($total_short_ship,0);?></th>
								
								<th><?=fn_number_format($total_short_ship,0);?></th>
								<th><?=fn_number_format($total_excess_ship,0);?></th>
                                
								<th><?=fn_number_format($total_left_over_qty,0);?></th>
								<th><?=fn_number_format($total_tot_miss,0);?></th>
								<th><?=fn_number_format($total_tot_rej,0);?></th>
								<th><?=fn_number_format($total_lay_to_ship,0);?></th>
								<th><?=fn_number_format($total_cut_to_ship,0);?></th>
							</tr>
						</tfoot>
					</table>					
				</div> 
			</div>   
		</fieldset>
		
		<?
	}
	
	if($type==2) //Dtls Button Aziz
	{	

	    // ============================= get closing job ===============================
		$sql=sql_select( "SELECT a.style_ref_no,b.job_no_mst, b.id from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst c where a.id=b.job_id and  b.id=c.po_break_down_id and a.status_active=1 and b.shiping_status=3 and a.company_name=$company_name $sql_cond $date_cond" );  
		if(count($sql)==0)
		{
			die('<div style="color:red;font-size:18px;">Data not found!');
		}
		$shipment_po_arr = array();
		$job_no_arr = array();
		foreach ($sql as $val) 
		{
			$shipment_po_arr[$val['JOB_NO_MST']][$val['ID']] = $val['ID'];
			$job_no_arr[$val['JOB_NO_MST']] = $val['JOB_NO_MST'];
			$job_no_style_arr[$val['JOB_NO_MST']] = $val['STYLE_REF_NO'];
		}

		$job_no_cond = where_con_using_array($job_no_arr,1,"a.job_no");
		$sql=sql_select( "SELECT a.id as jobid,a.job_no, b.id from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and b.status_active=1 $job_no_cond");  
		$all_po_arr = array();
		foreach ($sql as $v) 
		{
			$all_po_arr[$v['JOB_NO']] = $v['JOBID'];
		}
		// echo "<pre>";print_r($shipment_po_arr);die();
		$closing_job_arr = array();	$closing_jobId_arr = array();
		foreach ($all_po_arr as $job => $job_id) 
		{
			if(count($all_po_arr[$job])==count($shipment_po_arr[$job]))
			{
				$closing_job_arr[$job] = $job;
				$closing_jobId_arr[$job_id] = $job_id;
			}
		}

	//	  print_r($closing_jobId_arr);
	 	$job_id_cond = where_con_using_array($closing_jobId_arr,0,"a.id");
		/*==========================================================================================/
		 											lay data 										/
		/==========================================================================================*/ 	
		   $sqlLay="SELECT a.job_no,a.job_no_prefix_num,b.id,b.po_quantity, e.size_qty as lay_qty,f.color_id as COLORID,f.gmt_item_id as ITEM_ID from wo_po_details_master a,wo_po_break_down b,ppl_cut_lay_mst d,ppl_cut_lay_bundle e,ppl_cut_lay_dtls f where a.job_no=d.job_no and a.id=b.job_id  and d.id=e.mst_id and b.id=e.order_id and e.dtls_id=f.id and d.id=f.mst_id and e.mst_id=f.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.shiping_status=3 and e.size_qty>0 $sql_cond $job_id_cond";		
		// echo $sqlLay;die;
		$layData = sql_select($sqlLay);
		$tot_po_qty = 0;
		$tot_lay_qty = 0;$lay_cutQty_arr=array();
		foreach ($layData as  $val) 
		{
			//$tot_lay_qty += $val['LAY_QTY'];			
			$po_id_arr[$val['ID']] = $val['ID'];
			$lay_cutQty_arr[$val['JOB_NO']][$val['ITEM_ID']][$val['COLORID']]+= $val['LAY_QTY'];
		}
		$po_id_cond = where_con_using_array($po_id_arr,0,"id"); 
		// $tot_po_qty = return_field_value( "sum(b.po_quantity*a.total_set_qnty)", "wo_po_details_master a, wo_po_break_down b"," a.id=b.job_id and b.status_active=1 $job_no_cond");
		 $sql_po_main = "SELECT a.id as jobid, a.job_no,a.buyer_name as BUYER,a.style_ref_no as STYLE,b.id as POID,c.order_quantity as QTY,c.PLAN_CUT_QNTY,c.item_number_id as ITEMID,c.color_number_id as COLORID from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and  a.id=c.job_id  and b.id=c.po_break_down_id and b.status_active=1 and c.status_active=1 and c.is_deleted=0   $job_id_cond order by a.id asc";
	//  echo $sql_po_main; 
		$res_main = sql_select($sql_po_main);
		foreach ($res_main as $val) //ORDER_QUANTITY,ITEM_NUMBER_ID
		{
			//$tot_po_qty += $val['QTY'];
			$jobItemColorWise_arr[$val['JOB_NO']][$val['ITEMID']][$val['COLORID']]['qty']+= $val['QTY'];
			$jobItemColorWise_arr[$val['JOB_NO']][$val['ITEMID']][$val['COLORID']]['plan_qty']+= $val['PLAN_CUT_QNTY'];
			$jobItemColorWise_arr[$val['JOB_NO']][$val['ITEMID']][$val['COLORID']]['buyer']= $val['BUYER'];
			$jobItemColorWise_arr[$val['JOB_NO']][$val['ITEMID']][$val['COLORID']]['style']= $val['STYLE'];
			
			$PoId_arr[$val['POID']]= $val['POID'];
		}
		unset($res_main );
		/*==========================================================================================/
		/											gmts data 										/
		/==========================================================================================*/	
	 $sql_gmts=" SELECT A.JOB_NO,C.COLOR_NUMBER_ID,C.ITEM_NUMBER_ID,D.PRODUCTION_TYPE,D.EMBEL_NAME,E.PRODUCTION_QNTY,E.REJECT_QTY,E.ALTER_QTY,E.SPOT_QTY
			from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.production_type in(1,2,3,4,5,7,8,11,15)  and b.shiping_status=3 $sql_cond $job_id_cond";		
		// echo $sql;die;
		$sql_res_gmt = sql_select($sql_gmts);
		$gmts_data_array = array();
		foreach ($sql_res_gmt as $val) 
		{
			$job_no=$val['JOB_NO'];$color_id=$val['COLOR_NUMBER_ID'];$item_id=$val['ITEM_NUMBER_ID'];
			
			$gmts_data_array[$job_no][$item_id][$color_id][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['qty'] += $val['PRODUCTION_QNTY'];		
			$gmts_data_array[$job_no][$item_id][$color_id][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['rej_qty'] += $val['REJECT_QTY'];		
			$gmts_data_array[$job_no][$item_id][$color_id][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['alter_qty'] += $val['ALTER_QTY'];		
			$gmts_data_array[$job_no][$item_id][$color_id][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['spot_qty'] += $val['SPOT_QTY'];		
		}
		unset($sql_res_gmt);
		/*==========================================================================================/
		/										Fabric Booking data 									/
		/==========================================================================================*/
		//	$po_id_cond = where_con_using_array($PoId_arr,0,"a.id");	
		 $sql_booking=" SELECT  a.JOB_NO,b.PRECONS,b.FIN_FAB_QNTY,b.GMTS_COLOR_ID,c.ITEM_NUMBER_ID
			from wo_po_details_master a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.booking_type=1   $sql_cond $job_id_cond";		
		// echo $sql_booking;
		$sql_res_booking = sql_select($sql_booking);
		$booking_fin_arr = array();
		foreach ($sql_res_booking as $val) 
		{
			$job_no=$val['JOB_NO'];
			//$tot_dft_qty += $val['DEFECT_QTY'];	
			$booking_fin_arr[$job_no][$val['GMTS_COLOR_ID']][$val['ITEM_NUMBER_ID']]['fab_fin_qty']+=$val['FIN_FAB_QNTY'];
			$booking_fin_arr[$job_no][$val['GMTS_COLOR_ID']][$val['ITEM_NUMBER_ID']]['fab_cons']+=$val['PRECONS'];
			$booking_fin_arr[$job_no][$val['GMTS_COLOR_ID']][$val['ITEM_NUMBER_ID']]['fab_cons_row']+=1;
		}
		unset($sql_res_booking);
		//order_wise_pro_details
		/*==========================================================================================/
												Fin Fabric Recv Roll data 	 po_breakdown_id								/
		/==========================================================================================*/
		$sql_fab_recv=" SELECT  a.JOB_NO,c.QUANTITY
			from wo_po_details_master a,wo_po_break_down b,order_wise_pro_details c where a.id=b.job_id and b.id=c.po_breakdown_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.trans_type=1 and c.entry_form=317   $sql_cond $job_id_cond";		
		  //echo $sql_fab_recv;
		$sql_res_fab_recv = sql_select($sql_fab_recv);
		$fab_recv_arr = array();
		foreach ($sql_res_fab_recv as $val) 
		{
			$job_no=$val['JOB_NO'];
			//$tot_dft_qty += $val['DEFECT_QTY'];	
			$fab_recv_arr[$job_no]+=$val['FIN_FAB_QNTY'];
		}
		unset($sql_res_fab_recv);
		
		/*==========================================================================================/
		/										shipment data 										/
		/==========================================================================================*/	
		$sql_ship=" SELECT a.job_no,c.color_number_id as colorid,c.item_number_id as itemid,
		(case when d.entry_form !=85 then e.production_qnty else 0 end) as production_qnty,
		(case when d.entry_form =85 then e.production_qnty else 0 end) as return_qnty

			from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_ex_factory_mst d,pro_ex_factory_dtls e where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.shiping_status=3 $sql_cond $job_id_cond";		
		// echo $sql;die;
		$sql_res_ship = sql_select($sql_ship);
		$ship_qty = 0;
		$ship_rtn_qty = 0;
		foreach ($sql_res_ship as $val) 
		{
			$job_no=$val['JOB_NO'];
			$ship_qtyArr[$job_no][$val['COLORID']][$val['ITEMID']]+=$val['PRODUCTION_QNTY'];
			$ship_ret_qtyArr[$job_no][$val['COLORID']][$val['ITEMID']]+=$val['RETURN_QNTY'];
		}
	unset($sql_res_ship);
	
	
		$jobWiseArr=array();
		foreach($jobItemColorWise_arr as $job_no=>$job_data)
			{
			  $job_span=0;
			  foreach($job_data as $itemId=>$item_data)
			  {
				   $item_span=0;
			   foreach($item_data as $colorId=>$row)
				{
					$job_span++;$item_span++;
				}
				$jobWiseArr[$job_no]=$job_span;
				$jobItemWiseArr[$job_no][$itemId]=$item_span;
			  }
			}
		
		$tbl_width = 2620;	
		ob_start();				//print_r($jobWiseArr);
		?>
		<fieldset style="width:<?=$tbl_width+20;?>px;">
			
			<div style="width:<?=$tbl_width+20;?>px;">
				<table width="<?=$tbl_width;?>"  cellspacing="0">
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:18px; font-weight:bold" >Job Closing Report [Summary]</td>
					</tr>
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" ><?=$company_lib[$company_name];?></td>
					</tr>
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:15px; font-weight:bold" >From <?=$date_from;?> To <?=$date_to;?></td>
					</tr>
				</table>
				<div style="width:<?=$tbl_width+20;?>px; float:left;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width;?>" class="rpt_table" align="left">
						<thead>
                           <tr>
								<th colspan="7">&nbsp; </th>	
								<th colspan="4">Fabric Store</th>
								<th colspan="16">Cutting Section</th>
                                <th  colspan="5">Sewing</th>
                                <th  colspan="7">Finishing</th>
                                <th  colspan="4">GMT Missing  </th>
							</tr>
                            <tr>
								<th width="20">SL#</th>	
                                <th width="100"> Buyer</th>	
                                <th width="100">Job No</th>							
								<th width="100">Style Ref</th>								
								<th width="100" title="">Item</th>
								<th width="100">Color </th>
								<th width="70">Color Wise<br>Order Qty</th>
                                
								<th width="70">Fabric Required</th>
                                <th width="70">Fabric Receive</th>
								<th width="70">Fabric Issue</th>
								<th width="70">Balance</th>
                                
								<th width="70">Fabric Receive</th>
								<th width="70">Consumption</th>
								<th width="70" title="">Probable Cut Qty</th>
								<th width="70">Actual Cut Qty</th>
								<th width="70">No of parts<br> per body</th>
								<th width="70">Checked parts</th>
								<th width="70">Reject Qty</th>
								<th width="70">Total Cut Qty</th>
								<th width="70">Print Send QTY</th>
								<th width="70">Print Receive Qty</th>
								<th width="70"> Print Reject Qty</th>
								<th width="70"> Embroidery send</th>
								<th width="70">Embroidery receive</th>
								<th width="70">Embroidery reject</th>
								<th width="70" title="">Total Qty</th>
                                <th width="70" title="">Input Qty</th> 
                                
                                <th width="70" title="">Sewing Receive Qty</th>
                                <th width="70" title="">Sewing QC Pass Qty</th>
                                <th width="70" title="">Reject Qty</th>
                                <th width="70" title="">Sample</th>
                                <th width="70" title="">Total Qty</th>
                                
								<th width="70">Finishing Receive Qty</th>
								<th width="70">Ship Qty</th>
                                <th width="70">Reject Qty</th>
                                <th width="70">Ok Goods/ Unassort Qty</th>
                                <th width="70">Spot / Sheding Prob  Qty</th>
                                <th width="70">Shipping Sample</th>
                                <th width="70">Total Qty</th>
                                
                                <th width="70">Missing Cutting</th>
                                <th width="70">Missing Sewing</th>
                                <th width="70">Missing Sewing</th>
                                <th width="">Missing Finishing</th>
							</tr>								
						</thead>
						<tbody id="table_body">
                        <?
						$k=1;$total_fab_req_fin=$total_po_qty=$total_fab_cons=$total_plan_qty=$total_actual_lay_cutQty=$total_tot_cut_qty=$total_print_to_send=$total_print_to_send_reject=$total_print_to_recv=$total_embr_to_send=$total_embro_to_recv=$total_embro_reject=$total_sewRecv_qty=$total_sewQc_qty=$total_sew_reject=$total_tot_sew_qty=$total_fin_recv_qty=$total_ship_qty=$total_finishing_reject=$total_ok_good_qty=$total_shipping_sample_qty=$total_spot_shedding_qty=$total_tot_fin_qty=$total_missing_sewingQty=$total_missing_cuttingQty=$total_missing_sewingQty2=$total_missing_finish=0;
                        foreach($jobItemColorWise_arr as $job_no=>$job_data)
						{
						 $j=1;
						  foreach($job_data as $itemId=>$item_data)
						  {
							$item=1;
						   foreach($item_data as $colorId=>$row)
							{
							//$cut_rej_qty=0;$cut_miss=0;$cut_qty=0;$print_rej_qty=0;$emb_rej_qty=0;$input_qty=0;$output_qty=0;$sewing_rej_qty=0;$sewing_miss=0;$finish_rej_qty=0;$finish_qty=0;$alter_spot_qty=0;$alter_spot_qty=0;$org_ship_qty=0;$short_ship=0;$excess_ship=0;$left_over_qty=0;$tot_rej=0;$lay_to_ship=0;$cut_to_ship=0;$fin_miss=0;$tot_miss=0;
							//$tot_po_qty=0;
							
							$bgcolor=($k%2==0)?"#E9F3FF":"#FFFFFF";	
							$lay_cut_qty=$lay_cutQty_arr[$job_no];
							$fab_req_fin=$booking_fin_arr[$job_no][$itemId][$colorId]['fab_fin_qty'];
							$fab_cons_row=$booking_fin_arr[$job_no][$itemId][$colorId]['fab_cons_row'];
							$fab_cons=$booking_fin_arr[$job_no][$itemId][$colorId]['fab_cons']/$fab_cons_row;
							$actual_lay_cutQty=$lay_cutQty_arr[$job_no][$itemId][$colorId];
							
							$cutting_qc_reject=$gmts_data_array[$job_no][$itemId][$colorId][1][0]['rej_qty'];
							$tot_cut_qty=$actual_lay_cutQty+$cutting_qc_reject;
							$print_to_send=$gmts_data_array[$job_no][$itemId][$colorId][2][1]['qty'];
							$print_to_recv=$gmts_data_array[$job_no][$itemId][$colorId][3][1]['qty'];
							$print_to_send_reject=$gmts_data_array[$job_no][$itemId][$colorId][2][1]['rej_qty']+$gmts_data_array[$job_no][$itemId][$colorId][3][1]['rej_qty'];
							
							$embr_to_send=$gmts_data_array[$job_no][$itemId][$colorId][2][2]['qty'];
							$embro_to_recv=$gmts_data_array[$job_no][$itemId][$colorId][3][2]['qty'];
							$embro_reject=$gmts_data_array[$job_no][$itemId][$colorId][2][2]['rej_qty']+$gmts_data_array[$job_no][$itemId][$colorId][3][2]['rej_qty'];
							$tot_cut_part=$cutting_qc_reject+$print_to_recv+$print_to_send_reject+$embr_to_send+$embro_reject;
							
							$input_qty=$gmts_data_array[$job_no][$itemId][$colorId][4][0]['qty'];
							$sewRecv_qty=$gmts_data_array[$job_no][$itemId][$colorId][4][0]['qty'];
							$sewQc_qty=$gmts_data_array[$job_no][$itemId][$colorId][5][0]['qty'];
							
							$sew_reject=$gmts_data_array[$job_no][$itemId][$colorId][4][0]['rej_qty']+$gmts_data_array[$job_no][$itemId][$colorId][5][0]['rej_qty'];
							$tot_sew_qty=$sewQc_qty+$sew_reject;
							$fin_recv_qty=$gmts_data_array[$job_no][$itemId][$colorId][5][0]['qty'];
							$ship_qty=$ship_qtyArr[$job_no][$colorId][$itemId]-$ship_ret_qtyArr[$job_no][$colorId][$itemId];
							$finishing_reject=$gmts_data_array[$job_no][$itemId][$colorId][8][0]['rej_qty'];
							$ok_good_qty=0;$spot_shedding_qty=0;$shipping_sample_qty=0;
						//	$ship_ret_qty=$ship_ret_qtyArr[$job_no][$colorId][$item_id];
							
							$tot_fin_qty=$fin_recv_qty+$ship_qty+$ok_good_qty+$spot_shedding_qty+$shipping_sample_qty+$finishing_reject;
							$missing_cuttingQty=$input_qty-$sewRecv_qty;
							$missing_sewingQty=$tot_sew_qty-$sewRecv_qty;
							$missing_sewingQty2=$fin_recv_qty-$sewQc_qty;
							$missing_finish=$tot_fin_qty-$fin_recv_qty;
							
							$jobWiseRowSpan=$jobWiseArr[$job_no];
							$jobItemWiseRowSpan=$jobItemWiseArr[$job_no][$itemId];
							
							$ttl_cut="Cutting QC Reject+Print Recv+Print Send+Embro Send+Embro Reject";
							$ttl_fin="Fin Recv+ShipQty+OK Good+SpotShedding+shipping Sample+Fin Reject";
							
						?>
							<tr bgcolor="<? echo $bgcolor;?>" id="trsumm_<?= $k;?>" onClick="change_color('trsumm_<?= $k; ?>','<?= $bgcolor; ?>')">
								<?
                                if($j==1)
								 {
								?>
                               
                                <td rowspan="<? echo $jobWiseRowSpan;?>" align="center"><?=$k;?></td>
                                <td rowspan="<? echo $jobWiseRowSpan;?>" align="center"><?=$buyer_lib[$row['buyer']];?></td>
                                <td rowspan="<? echo $jobWiseRowSpan;?>" align="center"><?=$job_no;?></td>
                                <td  rowspan="<? echo $jobWiseRowSpan;?>" align="center"><?=$row['style'];?></td>
                                <?
								}
								 if($item==1)
								 {
								?>
                                <td rowspan="<? echo $jobItemWiseRowSpan;?>"><?=$garments_item[$itemId];?></td>
                                <?
								 }
								?>
                                <td align="center"><?=$color_lib[$colorId];?></td>
                                
                                <td align="right"><?=fn_number_format($row['qty'],0);?></td>
								<td align="right"><?=fn_number_format($fab_req_fin,0);?></td>
								<td align="right" title="LayCut-POQty"><?=fn_number_format($excess_cut_bal,0);?></td>
								<td align="right"><?=fn_number_format($cut_rej_qty,0);?></td>
								<td align="right"><?=fn_number_format($cut_miss,0);?></td>
								<td align="right"><?=fn_number_format($knit_fab_issue,0);?></td>
								<td align="right" title="WO Avg Fin Cons"><?=fn_number_format($fab_cons,0);?></td>
								<td align="right"><?=fn_number_format($row['plan_qty'],0);?></td>
								<td align="right"><?=fn_number_format($actual_lay_cutQty,0);?></td>
                                
								<td align="right"><? //fn_number_format($sewing_rej_qty,0);?></td>
								<td align="right"><? //fn_number_format($sewing_miss,0);?></td>
								<td align="right"><?=fn_number_format($cutting_qc_reject,0);?></td>
								<td align="right" title="Actual Qty+Reject Qty"><?=fn_number_format($tot_cut_qty,0);?></td>
								<td align="right"><?=fn_number_format($print_to_send,0);?></td>
                                <td align="right"><?=fn_number_format($print_to_recv,0);?></td>
								<td align="right" title="Issue+Recv"><?=fn_number_format($print_to_send_reject,0);?></td>
                            
								
								<td align="right"><?=fn_number_format($embr_to_send,0);?></td>
								<td align="right"><?=fn_number_format($embro_to_recv,0);?></td>
								<td align="right" title="Issue+Recv"><?=fn_number_format($embro_reject,0);?></td>
								<td align="right" title="<?=$ttl_cut;?>"><?=fn_number_format($tot_cut_part,0);?></td>
								<td align="right"><?=fn_number_format($input_qty,0);?></td>
								<td align="right"  title="SewIn"><?=fn_number_format($sewRecv_qty,0);?></td>
								<td align="right"  title="SewOut"><?=fn_number_format($sewQc_qty,0);?></td>
								<td align="right"  title="SewIn+Out"><?=fn_number_format($sew_reject,0);?></td>
								<td align="right"><? //fn_number_format($cut_to_ship,2);?></td>
                                <td align="right" title="SewQcPass+Reject(In+Out)"><?=fn_number_format($tot_sew_qty,0);?></td>
                                
                                <td align="right" title="SewOutQty"><?=fn_number_format($fin_recv_qty,0);?></td>
                                <td align="right"><?=fn_number_format($ship_qty,0);?></td>
                                <td align="right" title="GMT Fin Reject"><?=fn_number_format($finishing_reject,0);?></td>
                                <td align="right"><?=fn_number_format($ok_good_qty,0);?></td>
                                <td align="right"><?=fn_number_format($spot_shedding_qty,0);?></td>
                                <td align="right"><?=fn_number_format($shipping_sample_qty,0);?></td>
                                <td align="right" title="<?=$ttl_fin;?>"><?=fn_number_format($tot_fin_qty,0);?></td>
                                
                                <td align="right" title="SewRecv-SewInput"><?=fn_number_format($missing_cuttingQty,0);?></td>
                                <td align="right" title="Tot Sewing-SewRecv"><?=fn_number_format($missing_sewingQty,0);?></td>
                                <td align="right"  title="FinRecv-SewQcPass"><?=fn_number_format($missing_sewingQty2,0);?></td>
                                <td align="right" title="Tot Fin-Fin Recv"><?=fn_number_format($missing_finish,0);?></td>
                                
							</tr>
                            <?
							$k++; $j++;$item++;
							$total_po_qty+=$row['qty'];
							$total_fab_req_fin+=$fab_req_fin;
							
							$total_plan_qty+=$row['plan_qty'];
							$total_fab_cons+=$fab_cons;
							$total_actual_lay_cutQty+=$actual_lay_cutQty;
							
							$total_cutting_qc_reject+=$cutting_qc_reject;
							$total_tot_cut_qty+=$tot_cut_qty;
							$total_print_to_send+=$print_to_send;
							$total_print_to_recv+=$print_to_recv;
							$total_print_to_send_reject+=$print_to_send_reject;
							
							
							$total_embr_to_send+=$embr_to_send;
							$total_embro_to_recv+=$embro_to_recv;
							$total_embro_reject+=$embro_reject;
							
							$total_tot_cut_part+=$tot_cut_part;
							$total_input_qty+=$input_qty;
							$total_sewRecv_qty+=$sewRecv_qty;
							
							$total_sewQc_qty+=$sewQc_qty;
							$total_sew_reject+=$sew_reject;
							$total_tot_sew_qty+=$tot_sew_qty;
							$total_fin_recv_qty+=$fin_recv_qty;
							
							//$total_org_ship_qty+=$org_ship_qty;
							$total_ship_qty+=$ship_qty;
							$total_finishing_reject+=$finishing_reject;
							
							$total_ok_good_qty+=$ok_good_qty;
							$total_left_over_qty+=$left_over_qty;
							$total_tot_fin_qty+=$tot_fin_qty;
							
							
							$total_spot_shedding_qty+=$spot_shedding_qty;
							$total_shipping_sample_qty+=$shipping_sample_qty;
							
							$total_missing_cuttingQty+=$missing_cuttingQty;
							$total_missing_sewingQty+=$missing_sewingQty;
							$total_missing_sewingQty2+=$missing_sewingQty2;
							$total_missing_finish+=$missing_finish;
							}
						  }
						}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th></th>
								<th></th>
                                <th></th>
								<th></th>
                                <th></th>
								<th></th>
                              	 <th><?=fn_number_format($total_po_qty,0);?></th>
                                 <th><?=fn_number_format($total_fab_req_fin,0);?></th>
								 
								<th><?=fn_number_format($total_ex_cut_qty,0);?></th>
								<th><?=fn_number_format($total_cut_rej_qty,0);?></th>
								<th><?=fn_number_format($total_cut_miss,0);?></th>
								<th><?=fn_number_format($total_cut_qty,0);?></th>
								<th><?=fn_number_format($total_fab_cons,0);?></th>
								<th><?=fn_number_format($total_plan_qty,0);?></th>
                                <th><?=fn_number_format($total_actual_lay_cutQty,0);?></th>
								<th><? //fn_number_format($total_sewing_rej_qty,0);?></th>
								<th><? //fn_number_format($total_sewing_miss,0);?></th>
								<th><?=fn_number_format($total_cutting_qc_reject,0);?></th>
                                
								<th><?=fn_number_format($total_tot_cut_qty,0);?></th>
								<th><?=fn_number_format($total_print_to_send,0);?></th>
                                 <th><?=fn_number_format($total_print_to_recv,0);?></th>
								<th><?=fn_number_format($total_print_to_send_reject,0);?></th>
								<th><?=fn_number_format($total_embr_to_send,0);?></th>
								<th><?=fn_number_format($total_embro_to_recv,0);?></th>
								<th><?=fn_number_format($total_embro_reject,0);?></th>
								<th><?=fn_number_format($total_tot_cut_part,0);?></th>
								<th><?=fn_number_format($total_input_qty,0);?></th>
								<th><?=fn_number_format($total_sewRecv_qty,0);?></th>
								<th><?=fn_number_format($total_sewQc_qty,0);?></th>
								<th><?=fn_number_format($total_sew_reject,0);?></th>
								<th><? //fn_number_format($total_cut_to_ship,0);?></th>
                                <th><?=fn_number_format($total_tot_sew_qty,0);?></th>
                                <th><?=fn_number_format($total_fin_recv_qty,0);?></th>
                                <th><?=fn_number_format($total_ship_qty,0);?></th>
                                <th><?=fn_number_format($total_finishing_reject,0);?></th>
                                <th><?=fn_number_format($total_ok_good_qty,0);?></th>
                                <th><?=fn_number_format($total_spot_shedding_qty,0);?></th>
								<th><?=fn_number_format($total_shipping_sample_qty,0);?></th>
                                <th><?=fn_number_format($total_tot_fin_qty,0);?></th>
                                
                                <th><?=fn_number_format($total_missing_cuttingQty,0);?></th>
                                <th><?=fn_number_format($total_missing_sewingQty,0);?></th>
                                <th><?=fn_number_format($total_missing_sewingQty2,0);?></th>
                                <th><?=fn_number_format($total_missing_finish,0);?></th>
                                
							</tr>
						</tfoot>
					</table>					
				</div> 
			</div>   
		</fieldset>
		
		<?
	}
	/*foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//$filename=$user_id."_".$name.".xls";*/

	echo "$total_data####$filename";
	exit(); 
}

if($action=="production_popup")
{ 
	echo load_html_head_contents("Production Popup", "../../../", 1, 1, $unicode, '', '');
	// $process = array( &$_POST );
	// extract(check_magic_quote_gpc( $process ));
	extract($_REQUEST);
	list($wo_company_name,$location_name,$floor_name,$buyer_name,$style,$season,$item_id,$color_id,$start_date,$end_date,$job_no) = explode("**", $search_string);		
	
	

	$company_lib=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$location_lib=return_library_array( "SELECT id, location_name from lib_location", "id", "location_name"  );
	$floor_lib=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );
    $season_lib = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", "id", "season_name");
    $buyer_lib  = return_library_array("select id, buyer_name from  lib_buyer", "id", "buyer_name");
	$color_lib=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_lib=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$lineArr = return_library_array("select a.id,a.line_name from lib_sewing_line a","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
		
	
	$sql_cond = "";
	ob_start();
	if($type==1)
	{
		// $sql_cond .= ($hidden_job_id != "") ? " and a.id in($hidden_job_id)" : "";
		// $sql_cond .= ($buyer_name != 0) 	? " and a.buyer_name=$buyer_name" : "";
		// $sql_cond .= ($style != "") 		? " and a.style_ref_no='$style'" : "";
		$sql_cond .= ($job_no != "") 		? " and a.job_no='$job_no'" : "";
		$sql_cond .= ($item_id != "") 		? " and b.gmt_item_id in($item_id)" : "";
		$sql_cond .= ($color_id != "") 		? " and b.color_id in($color_id)" : "";
		
		$sql_cond .= ($wo_company_name != 0) 	? " and a.working_company_id=$wo_company_name" : "";
		$sql_cond .= ($location_name != 0) 		? " and a.location_id=$location_name" : "";
		if($type==1)
		{
			$sql_cond .= ($floor_name != 0) 	? " and a.floor_id in($floor_name)" : "";
		}	

		if($start_date !="" && $end_date !="")
	    {
	        $sql_cond.= " and a.entry_date between '$start_date' and '$end_date'";
	    }
	    /*==========================================================================================/
		/									getting gmts prod data 									/
		/==========================================================================================*/ 	
			
		$sql=" SELECT b.color_id as color_id,c.size_id as size_id,a.entry_date,a.location_id as location,a.working_company_id as serving_company,a.floor_id,a.cutting_no,c.size_qty

			from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond order by c.size_id";		
		// echo $sql;die;
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
		$size_arr = array();
		foreach ($sql_res as $val) 
		{
			$data_array[$val['SERVING_COMPANY']][$val['ENTRY_DATE']][$val['CUTTING_NO']][$val['FLOOR_ID']][$val['COLOR_ID']][$val['SIZE_ID']] += $val['SIZE_QTY'];
			
			$size_arr[$val['SIZE_ID']] = $val['SIZE_ID'];
			
		}
		// echo "<pre>";print_r($data_array);echo "</pre>";

		// echo $sql_cond;
		$tbl_width = 560+(count($size_arr)*60);	
		?>
		<fieldset style="width:<?=$tbl_width+20;?>px;">
			
			<div style="width:<?=$tbl_width+20;?>px;">			
					
					<div style="width:<?=$tbl_width+20;?>px; float:left;">
						<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width;?>" class="rpt_table" align="left">
							<thead>
								<tr>
									<th width="20">Sl</th>								
									<th width="120">Working Company</th>								
									<th width="60">Cutting Date</th>
									<th width="80">Sys. Cut No</th>
									<th width="100">Cutting Floor</th>
									<th width="100">Color</th>
									<?
									foreach ($size_arr as $key => $val) 
									{
										?>
										<th width="60"><?=$size_lib[$key];?></th>
										<?
									}
									?>
									<th width="80">Total</th>
								</tr>								
							</thead>
						</table>
						
						<div style="max-height:425px; overflow-y:scroll; width:<?=$tbl_width+20;?>px;" id="scroll_body">
							<table  border="1" style="border-collapse: collapse;" class="rpt_table"  width="<?=$tbl_width;?>" rules="all" id="table_bodys"  align="left">
								<tbody>
									<?
									$i=1;
									$gr_tot_arr = array();
									foreach ($data_array as $wo_name => $wo_data) 
									{	
										foreach ($wo_data as $pdate => $date_data) 
										{
											foreach ($date_data as $cut_no => $cut_data) 
											{
												foreach ($cut_data as $flr_name => $flr_data) 
												{
													foreach ($flr_data as $color_id => $row) 
													{			
														$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
														?>
														<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
															<td width="20"><p><?=$i;?></p></td>
															<td width="120"><p><?=$company_lib[$wo_name];?></p></td>
															<td width="60"><p><?=$pdate;?></p></td>							
															<td width="80"><p><?=$cut_no;?></p></td>
															<td width="100"><p><?=$floor_lib[$flr_name];?></p></td>
															<td width="100"><p><?=$color_lib[$color_id];?></p></td>
															<?
															$tot = 0;
															foreach ($size_arr as $key => $val) 
															{
																?>
																<td width="60" align="right"><?=number_format($row[$key],0);?></td>
																<?
																$tot += $row[$key];
																$gr_tot_arr[$key] += $row[$key];
															}
															?>
															<td align="right" width="80"><?=number_format($tot,0);?></td>
														</tr>
														<?
														$i++;															
													}
												}
											}
										}
									}
									?>
								</tbody>
							</table> 
						</div> 
						<table style="border-collapse: collapse;"  border="1" class="rpt_table"  width="<?=$tbl_width;?>" rules="all" align="left" >
							<tfoot>
								<tr>
									<th width="20"></th>								
									<th width="120"></th>								
									<th width="60"></th>
									<th width="80"></th>
									<th width="100"></th>
									<th width="100">Total</th>
									<?
									$tot = 0;
									foreach ($size_arr as $key => $val) 
									{
										?>
										<th width="60"><?=$gr_tot_arr[$key];?></th>
										<?
										$tot += $gr_tot_arr[$key];
									}
									?>
									<th width="80"><?=number_format($tot,0);?></th>
								</tr>
							</tfoot>
						</table>
					</div> 
			</div>   
		</fieldset>
		
		<?
	}
	else
	{
		// $sql_cond .= ($hidden_job_id != "") ? " and a.id in($hidden_job_id)" : "";
		$sql_cond .= ($buyer_name != 0) 	? " and a.buyer_name=$buyer_name" : "";
		$sql_cond .= ($style != "") 		? " and a.style_ref_no='$style'" : "";
		$sql_cond .= ($season != "") 		? " and a.season_buyer_wise='$season'" : "";
		$sql_cond .= ($job_no != "") 		? " and a.job_no='$job_no'" : "";
		$sql_cond .= ($item_id != "") 		? " and c.item_number_id in($item_id)" : "";
		$sql_cond .= ($color_id != "") 		? " and c.color_number_id in($color_id)" : "";
		
		$sql_cond .= ($wo_company_name != 0) 	? " and d.serving_company=$wo_company_name" : "";
		$sql_cond .= ($location_name != 0) 		? " and d.location=$location_name" : "";
		if($type==1)
		{
			$sql_cond .= ($floor_name != 0) 	? " and d.floor_id in($floor_name)" : "";
		}	

		if($start_date !="" && $end_date !="")
	    {
	        $sql_cond.= " and d.production_date between '$start_date' and '$end_date'";
	    }
	    /*==========================================================================================/
		/									getting gmts prod data 									/
		/==========================================================================================*/ 	
			
		$sql=" SELECT c.color_number_id as color_id,c.size_number_id as size_id,c.order_quantity,d.production_date,d.location,d.serving_company,d.floor_id,d.production_type,d.prod_reso_allo,d.sewing_line,e.cut_no,e.production_qnty

			from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.production_type=4 and e.production_qnty>0 $sql_cond order by c.size_order,d.production_date";		
		// echo $sql;die;
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
		$size_arr = array();
		foreach ($sql_res as $val) 
		{
			$sewing_line = "";
			if($val['PROD_RESO_ALLO']==1)
			{
				$line_number=explode(",",$prod_reso_arr[$val['SEWING_LINE']]);
				foreach($line_number as $vals)
				{
					if($sewing_line=='') $sewing_line=$lineArr[$vals]; else $sewing_line.=",".$lineArr[$vals];
				}
			}
			else
			{
				$sewing_line=$lineArr[$val['SEWING_LINE']];
			}

			$data_array[$val['SERVING_COMPANY']][$val['PRODUCTION_DATE']][$val['CUT_NO']][$val['FLOOR_ID']][$sewing_line][$val['COLOR_ID']][$val['SIZE_ID']]['qty'] += $val['PRODUCTION_QNTY'];
			$data_array[$val['SERVING_COMPANY']][$val['PRODUCTION_DATE']][$val['CUT_NO']][$val['FLOOR_ID']][$sewing_line][$val['COLOR_ID']][$val['SIZE_ID']]['prod_reso_allo'] = $val['PROD_RESO_ALLO'];
			
			$size_arr[$val['SIZE_ID']] = $val['SIZE_ID'];
			
		}
		// echo "<pre>";print_r($data_array);echo "</pre>";

		// echo $sql_cond;
		$tbl_width = 660+(count($size_arr)*60);	
		?>
		<fieldset style="width:<?=$tbl_width+20;?>px;">
			
			<div style="width:<?=$tbl_width+20;?>px;">			
					
					<div style="width:<?=$tbl_width+20;?>px; float:left;">
						<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width;?>" class="rpt_table" align="left">
							<thead>
								<tr>
									<th width="20">Sl</th>								
									<th width="120">Working Company</th>								
									<th width="60">Input Date</th>
									<th width="80">Input Challan No</th>
									<th width="100">Sewing Floor</th>
									<th width="100">Line No</th>
									<th width="100">Color</th>
									<?
									foreach ($size_arr as $key => $val) 
									{
										?>
										<th width="60"><?=$size_lib[$key];?></th>
										<?
									}
									?>
									<th width="80">Total</th>
								</tr>								
							</thead>
						</table>
						
						<div style="max-height:425px; overflow-y:scroll; width:<?=$tbl_width+20;?>px;" id="scroll_body">
							<table  border="1" style="border-collapse: collapse;" class="rpt_table"  width="<?=$tbl_width;?>" rules="all" id="table_bodys"  align="left">
								<tbody>
									<?
									$i=1;
									$gr_tot_arr = array();
									foreach ($data_array as $wo_name => $wo_data) 
									{	
										foreach ($wo_data as $pdate => $date_data) 
										{
											foreach ($date_data as $cut_no => $cut_data) 
											{
												foreach ($cut_data as $flr_name => $flr_data) 
												{
													foreach ($flr_data as $line_name => $line_data) 
													{
														foreach ($line_data as $color_id => $row) 
														{			
															$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
															?>
															<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
																<td width="20"><p><?=$i;?></p></td>
																<td width="120"><p><?=$company_lib[$wo_name];?></p></td>
																<td width="60"><p><?=$pdate;?></p></td>							
																<td width="80"><p><?=$cut_no;?></p></td>
																<td width="100"><p><?=$floor_lib[$flr_name];?></p></td>
																<td width="100"><p><?=$line_name;?></p></td>
																<td width="100"><p><?=$color_lib[$color_id];?></p></td>
																<?
																$tot = 0;
																foreach ($size_arr as $key => $val) 
																{
																	?>
																	<td width="60" align="right"><?=number_format($row[$key]['qty'],0);?></td>
																	<?
																	$tot += $row[$key]['qty'];
																	$gr_tot_arr[$key] += $row[$key]['qty'];
																}
																?>
																<td align="right" width="80"><?=number_format($tot,0);?></td>
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
								</tbody>
							</table> 
						</div> 
						<table style="border-collapse: collapse;"  border="1" class="rpt_table"  width="<?=$tbl_width;?>" rules="all" align="left" >
							<tfoot>
								<tr>
									<th width="20"></th>								
									<th width="120"></th>								
									<th width="60"></th>
									<th width="80"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100">Total</th>
									<?
									$tot = 0;
									foreach ($size_arr as $key => $val) 
									{
										?>
										<th width="60"><?=$gr_tot_arr[$key];?></th>
										<?
										$tot += $gr_tot_arr[$key];
									}
									?>
									<th width="80"><?=number_format($tot,0);?></th>
								</tr>
							</tfoot>
						</table>
					</div> 
			</div>   
		</fieldset>
		<?
	}
	$user_id = $user_id."_pop";
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

	// echo "$total_data####$filename";
	echo '<a target="_blank" href="'.$filename.'"><input type="button" value="Excel Preview" name="excel" id="exportBtn" class="formbutton" style="width:100px"/></a>';
	exit(); 
}
?>