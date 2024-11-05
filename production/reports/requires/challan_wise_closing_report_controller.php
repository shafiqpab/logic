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
	echo create_drop_down( "cbo_location_name", 130, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id=$data group by id,location_name  order by location_name","id,location_name", 1, "-- Select location --", $selected, "load_drop_down( 'requires/challan_wise_closing_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_name", 130, "SELECT id,floor_name from lib_prod_floor where location_id=$data and status_active =1 and is_deleted=0 and production_process in(5) group by id,floor_name order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "" );
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
									echo create_drop_down( "cbo_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'challan_wise_closing_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value, 'search_list_view', 'search_div', 'challan_wise_closing_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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
	if($search_string!="")	{$search_cond=" and $search_field='$search_string'";}
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

	$wo_company_name 	= str_replace("'","",$cbo_wo_company_name);
	$location_name 		= str_replace("'","",$cbo_location_name);
	$floor_name			= str_replace("'","",$cbo_floor_name);
	$year 				= str_replace("'","",$cbo_year);
	$buyer_name 		= str_replace("'","",$cbo_buyer_name);
	$hidden_job_id 		= str_replace("'","",$hidden_job_id);
	$challan_no 		= str_replace("'","",$txt_challan_no);
	$date_from 			= str_replace("'","",$txt_date_from);
	$date_to 			= str_replace("'","",$txt_date_to);
	
	
	$sql_cond .= ($buyer_name != 0) 		? " and a.buyer_name=$buyer_name" : "";
	$sql_cond .= ($hidden_job_id != "") 	? " and a.id in($hidden_job_id)" : "";
	$sql_cond .= ($wo_company_name != 0) 	? " and d.serving_company=$wo_company_name" : "";
	$sql_cond .= ($location_name != 0) 		? " and d.location=$location_name" : "";
	$sql_cond .= ($floor_name != 0) 	? " and d.floor_id =$floor_name" : "";
	// $sql_cond .= ($year != 0) 				? " and to_char(a.insert_date,'YYYY')='$year'" : "";

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
        $sql_cond.= " and d.production_date between '$start_date' and '$end_date'";
    }

	$challan_cond = ($challan_no != "") ? " and f.sys_number_prefix_num=$challan_no" : "";
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
		
	/*==========================================================================================/
	/									getting gmts prod data 									/
	/==========================================================================================*/ 	
	$sql=" SELECT a.id as job_id, a.job_no,a.buyer_name,a.style_ref_no as style,b.id as po_id,c.item_number_id as item_id,c.color_number_id as color_id,c.size_number_id as size_id,c.order_quantity,d.location,d.serving_company,d.floor_id,d.production_type,e.production_qnty,e.cut_no,f.sys_number,d.sewing_line,d.floor_id,d.production_date,d.prod_reso_allo,e.bundle_no

	from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e,pro_gmts_delivery_mst f 
	where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id and f.id=d.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.production_type=4 and e.production_qnty>0 $sql_cond $challan_cond
	order by c.size_order,d.production_date";
	// echo $sql;die;
	$sql_res = sql_select($sql);
	$data_array = array();
	$color_qty_array = array();
	$po_id_array = array();
	$bundle_array = array();
	$bundle_challan_array = array();
	foreach ($sql_res as $v) 
	{
		$sewing_line='';
		if($v['PROD_RESO_ALLO']==1)
		{
			$line_number=explode(",",$prod_reso_arr[$v['SEWING_LINE']]);
			foreach($line_number as $val)
			{
				if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
			}
		}
		else 
		{
			$sewing_line=$lineArr[$v['SEWING_LINE']];
		}

		$data_array[$v['PRODUCTION_TYPE']][$v['PRODUCTION_DATE']][$v['SYS_NUMBER']][$v['FLOOR_ID']][$v['SEWING_LINE']][$v['JOB_NO']][$v['ITEM_ID']][$v['COLOR_ID']][$v['SIZE_ID']]['buyer_name'] = $v['BUYER_NAME'];
		$data_array[$v['PRODUCTION_TYPE']][$v['PRODUCTION_DATE']][$v['SYS_NUMBER']][$v['FLOOR_ID']][$v['SEWING_LINE']][$v['JOB_NO']][$v['ITEM_ID']][$v['COLOR_ID']][$v['SIZE_ID']]['style'] = $v['STYLE'];	
		$data_array[$v['PRODUCTION_TYPE']][$v['PRODUCTION_DATE']][$v['SYS_NUMBER']][$v['FLOOR_ID']][$v['SEWING_LINE']][$v['JOB_NO']][$v['ITEM_ID']][$v['COLOR_ID']][$v['SIZE_ID']]['sys_number'] = $v['SYS_NUMBER'];	
		$data_array[$v['PRODUCTION_TYPE']][$v['PRODUCTION_DATE']][$v['SYS_NUMBER']][$v['FLOOR_ID']][$v['SEWING_LINE']][$v['JOB_NO']][$v['ITEM_ID']][$v['COLOR_ID']][$v['SIZE_ID']]['cut_no'] .= $v['CUT_NO'].",";	
		$data_array[$v['PRODUCTION_TYPE']][$v['PRODUCTION_DATE']][$v['SYS_NUMBER']][$v['FLOOR_ID']][$v['SEWING_LINE']][$v['JOB_NO']][$v['ITEM_ID']][$v['COLOR_ID']][$v['SIZE_ID']]['sewing_line'] = $sewing_line;

		$bundle_array[$v['PRODUCTION_TYPE']][$v['PRODUCTION_DATE']][$v['SYS_NUMBER']][$v['FLOOR_ID']][$v['SEWING_LINE']][$v['JOB_NO']][$v['ITEM_ID']][$v['COLOR_ID']][$v['BUNDLE_NO']] = $v['BUNDLE_NO'];	

		$data_array[$v['PRODUCTION_TYPE']][$v['PRODUCTION_DATE']][$v['SYS_NUMBER']][$v['FLOOR_ID']][$v['SEWING_LINE']][$v['JOB_NO']][$v['ITEM_ID']][$v['COLOR_ID']][$v['SIZE_ID']]['qty'] += $v['PRODUCTION_QNTY'];	
		$color_qty_array[$v['PRODUCTION_TYPE']][$v['PRODUCTION_DATE']][$v['SYS_NUMBER']][$v['FLOOR_ID']][$v['SEWING_LINE']][$v['JOB_NO']][$v['ITEM_ID']][$v['COLOR_ID']] += $v['PRODUCTION_QNTY'];

		$po_id_array[$v['PO_ID']] = $v['PO_ID'];
		$bundle_challan_array[$v['BUNDLE_NO']] = $v['SYS_NUMBER'];
	}

    if(count($sql_res)==0)
    {
        ?>
        <center>
            <div style="width: 80%;" class="alert alert-danger">Data not found.Please try again.</div>
        </center>
        <?
        die();
    }
	// echo "<pre>";print_r($data_array);echo "</pre>";

	/*==========================================================================================/
	/									sewing output data  									/
	/==========================================================================================*/ 	
	$po_id_cond = where_con_using_array($po_id_array,0,"b.id");
	$sql=" SELECT a.id as job_id, a.job_no,a.buyer_name,a.style_ref_no as style,b.id as po_id,c.item_number_id as item_id,c.color_number_id as color_id,c.size_number_id as size_id,c.order_quantity,d.location,d.serving_company,d.floor_id,d.production_type,e.production_qnty,e.cut_no,f.sys_number,d.sewing_line,d.floor_id,d.production_date,d.prod_reso_allo,e.bundle_no

	from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e,pro_gmts_delivery_mst f 
	where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id and f.id=d.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.production_type=5 and e.production_qnty>0 $sql_cond $po_id_cond
	order by c.size_order,d.production_date";
	// echo $sql;die;
	$sql_res = sql_select($sql);
	$output_qty_arr = array();
	foreach ($sql_res as $v) 
	{
		$output_qty_arr[$bundle_challan_array[$v['BUNDLE_NO']]][$v['FLOOR_ID']][$v['SEWING_LINE']][$v['JOB_NO']][$v['ITEM_ID']][$v['COLOR_ID']][$v['SIZE_ID']]['qty'] += $v['PRODUCTION_QNTY'];//[$v['PRODUCTION_TYPE']][$v['PRODUCTION_DATE']]
	}
	// echo "<pre>";print_r($output_qty_arr);echo "</pre>";
	$tbl_width = 1850;	
	ob_start();
	?>
	<fieldset style="width:<?=$tbl_width+20;?>px;">
		
		<div style="width:<?=$tbl_width+20;?>px;">
			<table width="<?=$tbl_width;?>"  cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="15" align="center" style="border:none;font-size:18px; font-weight:bold" >Chalan Wise Closing Report</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" ><?=$company_lib[$wo_company_name];?></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="15" align="center" style="border:none;font-size:15px; font-weight:bold" >From <?=$date_from;?> To <?=$date_to;?></td>
				</tr>
			</table>			
			
				
				<div style="width:<?=$tbl_width+20;?>px; float:left;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width;?>" class="rpt_table" align="left">
						<thead>
							<tr>
								<th width="20">Sl</th>								
								<th width="70">Input Date</th>
								<th width="120">Challan</th>
								<th width="120">Floor</th>
								<th width="120">Line</th>
								<th width="120">Buyer</th>
								<th width="80">Job</th>
								<th width="120">Style</th>
								<th width="120">Item</th>
								<th width="80">Order Cut</th>
								<th width="120">Cutting NO</th>
								<th width="120">Color</th>
								<th width="80">Gmts Qty.</th>
								<th width="80">No Of Bundle</th>
								<th width="80">Size</th>
								<th width="80">Input Qty.</th>
								<th width="80">Output Qty</th>
								<th width="80">Balance</th>
							</tr>								
						</thead>
					</table>
					
					<div style="max-height:425px; overflow-y:scroll; width:<?=$tbl_width+20;?>px;" id="scroll_body">
						<table  border="1" style="border-collapse: collapse;" class="rpt_table"  width="<?=$tbl_width;?>" rules="all" id="table_body"  align="left">
							<tbody>
								<?
								$i=1;
								$gr_color_qty = 0;
								$gr_input_qty = 0;
								$gr_output_qty = 0;
								$gr_balance = 0;
								foreach ($data_array[4] as $date => $date_data) 
								{
									foreach ($date_data as $challan_no => $challan_data) 
									{
										foreach ($challan_data as $flr_id => $flr_data) 
										{
											foreach ($flr_data as $l_id => $l_data) 
											{
												foreach ($l_data as $job_no => $job_data) 
												{
													foreach ($job_data as $itm_id => $itm_data) 
													{
														foreach ($itm_data as $color_id => $color_data) 
														{
															$cl = 0;
															foreach ($color_data as $size_id => $row) 
															{
																$color_qty = $color_qty_array[4][$date][$challan_no][$flr_id][$l_id][$job_no][$itm_id][$color_id];
																$tot_bundle = count($bundle_array[4][$date][$challan_no][$flr_id][$l_id][$job_no][$itm_id][$color_id]);
																$output_qty = $output_qty_arr[$challan_no][$flr_id][$l_id][$job_no][$itm_id][$color_id][$size_id]['qty'];
																$balance = $row['qty'] - $output_qty;
																$cut_no = implode(", ", array_filter(array_unique(explode(",",$row['cut_no']))));
																$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
																?>
																<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
																	<?
																	if($cl==0)
																	{
																		?>
																		<td valign="middile" rowspan="<?=count($color_data);?>" width="20"><?=$i;?></td>								
																		<td valign="middile" rowspan="<?=count($color_data);?>" width="70" align="center"> <?=change_date_format($date);?></td>
																		<td valign="middile" rowspan="<?=count($color_data);?>" width="120" align="center"><?=$challan_no;?></td>
																		<td valign="middile" rowspan="<?=count($color_data);?>" width="120"><?=$floor_lib[$flr_id];?></td>
																		<td valign="middile" rowspan="<?=count($color_data);?>" width="120"><?=$row['sewing_line'];?></td>
																		<td valign="middile" rowspan="<?=count($color_data);?>" width="120"><?=$buyer_lib[$row['buyer_name']];?></td>
																		<td valign="middile" rowspan="<?=count($color_data);?>" width="80"><?=$job_no;?></td>
																		<td valign="middile" rowspan="<?=count($color_data);?>" width="120"><?=$row['style'];?></td>
																		<td valign="middile" rowspan="<?=count($color_data);?>" width="120"><?=$garments_item[$itm_id];?></td>
																		<td valign="middile" rowspan="<?=count($color_data);?>" width="80"><?=$ia;?></td>
																		<td valign="middile" rowspan="<?=count($color_data);?>" width="120"> <?=$cut_no;?></td>
																		<td valign="middile" rowspan="<?=count($color_data);?>" width="120"><?=$color_lib[$color_id];?></td>
																		<td valign="middile" align="right" rowspan="<?=count($color_data);?>" width="80"><?=number_format($color_qty,0);?></td>
																		<td align="center" valign="middile" rowspan="<?=count($color_data);?>" width="80"><?=$tot_bundle;?></td>
																		<?
																		$cl++;
																		$i++;
																	}
																	?>
																	<td valign="middile" width="80"><?=$size_lib[$size_id];?></td>
																	<td valign="middile" align="right" width="80"><?=number_format($row['qty'],0);?></td>
																	<td valign="middile" align="right" width="80"><?=number_format($output_qty,0);?></td>
																	<td valign="middile" align="right" width="80"><?=number_format($balance,0);?></td>
																</tr>
																<?
																$i++;
																$gr_color_qty += $color_qty;
																$gr_input_qty += $row['qty'];
																$gr_output_qty += $output_qty;
																$gr_balance += $balance;
															}
														}
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
								<th width="70"> </th>
								<th width="120"></th>
								<th width="120"></th>
								<th width="120"></th>
								<th width="120"></th>
								<th width="80"></th>
								<th width="120"></th>
								<th width="120"></th>
								<th width="80"></th>
								<th width="120"> </th>
								<th width="120">Grand Total</th>
								<th width="80"><?=number_format($gr_color_qty,0);?></th>
								<th width="80"></th>
								<th width="80"></th>
								<th width="80"><?=number_format($gr_input_qty,0);?></th>
								<th width="80"><?=number_format($gr_output_qty,0);?></th>
								<th width="80"><?=number_format($gr_balance,0);?></th>
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
			
		$sql=" SELECT c.color_number_id as color_id,c.size_number_id as size_id,c.order_quantity,d.production_date,d.location,d.serving_company,d.floor_id,d.production_type,d.prod_reso_allo,d.sewing_line,e.cut_no,e.production_qnty,d.id, f.SYS_NUMBER

			from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e, pro_gmts_delivery_mst f where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id and f.id=d.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.production_type=4 and e.production_qnty>0 $sql_cond order by c.size_order,d.production_date";		
		// echo $sql; //die;
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

			$data_array[$val['SERVING_COMPANY']][$val['PRODUCTION_DATE']][$val['CUT_NO']][$val['FLOOR_ID']][$sewing_line][$val['COLOR_ID']][$val['ID']][$val['SYS_NUMBER']][$val['SIZE_ID']]['qty'] += $val['PRODUCTION_QNTY'];
			$data_array[$val['SERVING_COMPANY']][$val['PRODUCTION_DATE']][$val['CUT_NO']][$val['FLOOR_ID']][$sewing_line][$val['COLOR_ID']][$val['ID']][$val['SYS_NUMBER']][$val['SIZE_ID']]['prod_reso_allo'] = $val['PROD_RESO_ALLO'];
			
			$size_arr[$val['SIZE_ID']] = $val['SIZE_ID'];
			
		}
		// echo "<pre>";print_r($data_array);echo "</pre>";

		// echo $sql_cond;
		$tbl_width = 860+(count($size_arr)*60);	
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
									<th width="90">Bundle Wise Input <br> Challan No</th>
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
														foreach ($line_data as $color_id => $color_data) 
														{
															 foreach ($color_data as $sys_cln_id => $sys_cln_data) 
														   {
																foreach ($sys_cln_data as $sys_number_id => $row) 
																{			
																		
																	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
																	?>
																	<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
																		<td width="20"><p><?=$i;?></p></td>
																		<td width="120"><p><?=$company_lib[$wo_name];?></p></td>
																		<td width="60"><p><?=$pdate;?></p></td>							
																		<td width="90"><p><?=$sys_number_id;?></p></td>							
																		<td width="80"><p><?=$sys_cln_id;?></p></td>
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
									<th width="90"></th>
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