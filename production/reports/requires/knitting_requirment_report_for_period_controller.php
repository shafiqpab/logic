<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$yarn_count_library=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
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
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'knitting_requirment_report_for_period_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
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
	//echo $month_id;
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
	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	if($db_type==0)
	{
	if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
	$year_field_con=" and to_char(insert_date,'YYYY')";
	if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	exit(); 
} // Job Search end
if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
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
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}
    </script>
</head>
<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:780px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
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
                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'buyer_order_wise_knitting_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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
if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	echo $data[1];
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
	if($search_by==1) 
		$search_field="b.po_number"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no"; 	
	else 
		$search_field="a.job_no";
	$start_date =trim($data[4]);
	$end_date =trim($data[5]);	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	$arr=array(0=>$company_arr,1=>$buyer_arr);
	$sql= "select b.id, $year_field a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,130,50,60,130,130","760","220",0, $sql , "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr , "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "",'','0,0,0,0,0,0,3','',1) ;
   exit(); 
}
if($action=="report_generate")
{
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 
		$company_name= str_replace("'","",$cbo_company_name);
		if(str_replace("'","",$cbo_buyer_name)==0)
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
			$buyer_id_cond=" and a.buyer_name in (".str_replace("'","",$cbo_buyer_name).")";
		}
		
		
		if(str_replace("'","",$txt_season)!="") $season_cond=" and UPPER(a.season) like '%".strtoupper(str_replace("'","",$txt_season))."%'"; else $season_cond="";
		if(str_replace("'","",$txt_order)!="") $order_cond=" and b.id in(".str_replace("'","",$txt_order).")"; else $order_cond="";
		
		
		if(str_replace("'","",$txt_construction)!="") 
		{
			
			$construction_cond=" and UPPER(d.construction) like '%".strtoupper(str_replace("'","",$txt_construction))."%'"; 
			$construction_cond1=" and UPPER(b.construction) like '%".strtoupper(str_replace("'","",$txt_construction))."%'"; 
		}
		else 
		{
			$construction_cond="";
			$construction_cond1="";
		}
		if(str_replace("'","",$txt_composition)!="") 
		{
			$composition_cond=" and UPPER(d.composition) like '%".strtoupper(str_replace("'","",$txt_composition))."%'";
			$composition_cond1=" and UPPER(b.copmposition) like '%".strtoupper(str_replace("'","",$txt_composition))."%'";
		}
		else 
		{
			$composition_cond="";
			$composition_cond1="";
		}
		if(str_replace("'","",$txt_gsm)!="" && str_replace("'","",$txt_gsm_to)!="")
		{
			if(str_replace("'","",$txt_gsm)!="") 
			{
				$gsm_cond=" and d.gsm_weight between '".str_replace("'","",$txt_gsm)."' and '".str_replace("'","",$txt_gsm_to)."'";
				$gsm_cond1=" and b.gsm_weight between '".str_replace("'","",$txt_gsm)."' and '".str_replace("'","",$txt_gsm_to)."'";
			}
			else 
			{
				$gsm_cond="";
				$gsm_cond1="";
			}
		}
		
		
	
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
		$date_cond=" and a.booking_date between '$start_date' and '$end_date'";
		}
	
		
	ob_start();
?>

		 <?
		//echo "select a.buyer_name,a.job_no,a.season,b.id,b.po_number,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty ,d.id as pre_cost_dtls_id,d.construction,d.composition,d.fab_nature_id,d.color_type_id,d.fabric_source,d.gsm_weight,d.width_dia_type   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no  and b.id=c.po_break_down_id and a.company_name=$company_name $buyer_id_cond $season_cond $construction_cond $composition_cond $gsm_cond  and d.fab_nature_id=2 and d.fabric_source=1  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id,pre_cost_dtls_id";
		
	$sql_po=sql_select("select a.buyer_name,a.style_ref_no,a.job_no,a.season,b.id,b.po_number,b.file_no,b.grouping,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty ,d.id as pre_cost_dtls_id,d.construction,d.composition,d.fab_nature_id,d.color_type_id,d.fabric_source,d.gsm_weight,d.width_dia_type   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no  and b.id=c.po_break_down_id and a.company_name=$company_name $buyer_id_cond $season_cond $order_cond  $gsm_cond  and d.fab_nature_id=2 and d.fabric_source=1  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id,pre_cost_dtls_id");
	
	$data_arr=array();
	foreach($sql_po as $sql_po_row)
	{
		$data_arr[po_id][$sql_po_row[csf('id')]]=$sql_po_row[csf('id')];
		$data_arr[po_number][$sql_po_row[csf('id')]]=$sql_po_row[csf('po_number')];
		$data_arr[buyer_name][$sql_po_row[csf('id')]]=$sql_po_row[csf('buyer_name')];
		$data_arr[season][$sql_po_row[csf('id')]]=$sql_po_row[csf('season')];
		$data_arr[style_ref_no][$sql_po_row[csf('id')]]=$sql_po_row[csf('style_ref_no')];
		$data_arr[file_no][$sql_po_row[csf('id')]]=$sql_po_row[csf('file_no')];
		$data_arr[grouping][$sql_po_row[csf('id')]]=$sql_po_row[csf('grouping')];
		/*$data_arr[construction][$sql_po_row[csf('id')]]=$sql_po_row[csf('construction')];
		$data_arr[composition][$sql_po_row[csf('id')]]=$sql_po_row[csf('composition')];
		$data_arr[gsm_weight][$sql_po_row[csf('id')]]=$sql_po_row[csf('gsm_weight')];
		$data_arr[color_type_id][$sql_po_row[csf('id')]]=$sql_po_row[csf('color_type_id')];*/
		$data_arr[width_dia_type][$sql_po_row[csf('pre_cost_dtls_id')]][$sql_po_row[csf('id')]]=$sql_po_row[csf('width_dia_type')];
		$data_arr[construction][$sql_po_row[csf('pre_cost_dtls_id')]][$sql_po_row[csf('id')]]=$sql_po_row[csf('construction')];
		$data_arr[composition][$sql_po_row[csf('pre_cost_dtls_id')]][$sql_po_row[csf('id')]]=$sql_po_row[csf('composition')];
		$data_arr[gsm_weight][$sql_po_row[csf('pre_cost_dtls_id')]][$sql_po_row[csf('id')]]=$sql_po_row[csf('gsm_weight')];
		$data_arr[color_type][$sql_po_row[csf('pre_cost_dtls_id')]][$sql_po_row[csf('id')]]=$sql_po_row[csf('color_type_id')];


		
	}
	//echo $job_no_string;
   $txt_order_no_id=implode(',',$data_arr[po_id]);
	
	
	$nameArray=sql_select("
	select
	a.booking_date,
	b.pre_cost_fabric_cost_dtls_id,
	b.job_no,
	b.po_break_down_id,
	b.booking_no,
	b.grey_fab_qnty,
	b.color_type,
	b.construction,
	b.copmposition,
	b.gsm_weight,
	b.dia_width,
	b.fabric_color_id,
	b.booking_type,
	b.is_short
FROM
	wo_booking_mst a,
	wo_booking_dtls b
WHERE
	a.job_no=b.job_no and
    a.booking_no=b.booking_no and
	b.po_break_down_id in (".$txt_order_no_id.") $construction_cond1 $composition_cond1 $gsm_cond1 $date_cond and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	order by b.dia_width");
		$report_data_array=array();
		foreach($nameArray as $rows)
		{
		 $report_data_array[dia_width][$rows[csf('dia_width')]]=$rows[csf('dia_width')];
		 $report_data_array[$rows[csf('dia_width')]][$rows[csf('pre_cost_fabric_cost_dtls_id')]][$rows[csf('po_break_down_id')]][$rows[csf('booking_no')]][$rows[csf('fabric_color_id')]][job_no]=$rows[csf('job_no')];
		 $report_data_array[$rows[csf('dia_width')]][$rows[csf('pre_cost_fabric_cost_dtls_id')]][$rows[csf('po_break_down_id')]][$rows[csf('booking_no')]][$rows[csf('fabric_color_id')]][po_break_down_id]=$rows[csf('po_break_down_id')];
		 $report_data_array[$rows[csf('dia_width')]][$rows[csf('pre_cost_fabric_cost_dtls_id')]][$rows[csf('po_break_down_id')]][$rows[csf('booking_no')]][$rows[csf('fabric_color_id')]][booking_no]=$rows[csf('booking_no')];
		 
		 $report_data_array[$rows[csf('dia_width')]][$rows[csf('pre_cost_fabric_cost_dtls_id')]][$rows[csf('po_break_down_id')]][$rows[csf('booking_no')]][$rows[csf('fabric_color_id')]][booking_type]=$rows[csf('booking_type')];
		 $report_data_array[$rows[csf('dia_width')]][$rows[csf('pre_cost_fabric_cost_dtls_id')]][$rows[csf('po_break_down_id')]][$rows[csf('booking_no')]][$rows[csf('fabric_color_id')]][is_short]=$rows[csf('is_short')];
		 
		 $report_data_array[$rows[csf('dia_width')]][$rows[csf('pre_cost_fabric_cost_dtls_id')]][$rows[csf('po_break_down_id')]][$rows[csf('booking_no')]][$rows[csf('fabric_color_id')]][booking_date]=$rows[csf('booking_date')];
		 $report_data_array[$rows[csf('dia_width')]][$rows[csf('pre_cost_fabric_cost_dtls_id')]][$rows[csf('po_break_down_id')]][$rows[csf('booking_no')]][$rows[csf('fabric_color_id')]][color_type]=$rows[csf('color_type')];
		 $report_data_array[$rows[csf('dia_width')]][$rows[csf('pre_cost_fabric_cost_dtls_id')]][$rows[csf('po_break_down_id')]][$rows[csf('booking_no')]][$rows[csf('fabric_color_id')]][construction]=$rows[csf('construction')];
		 $report_data_array[$rows[csf('dia_width')]][$rows[csf('pre_cost_fabric_cost_dtls_id')]][$rows[csf('po_break_down_id')]][$rows[csf('booking_no')]][$rows[csf('fabric_color_id')]][copmposition]=$rows[csf('copmposition')];
		 $report_data_array[$rows[csf('dia_width')]][$rows[csf('pre_cost_fabric_cost_dtls_id')]][$rows[csf('po_break_down_id')]][$rows[csf('booking_no')]][$rows[csf('fabric_color_id')]][gsm_weight]=$rows[csf('gsm_weight')];
		 $report_data_array[$rows[csf('dia_width')]][$rows[csf('pre_cost_fabric_cost_dtls_id')]][$rows[csf('po_break_down_id')]][$rows[csf('booking_no')]][$rows[csf('fabric_color_id')]][grey_fab_qnty]+=$rows[csf('grey_fab_qnty')];
		}
		
		
		
	$sql_prod=("select a.booking_no,b.width,c.po_breakdown_id,c.color_id,sum(c.quantity) as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=13 and a.entry_form=2 and  a.company_id=$company_name and  a.receive_basis=1 and b.status_active=1 and b.is_deleted=0 and a.booking_no='OG-Fb-16-00318' group by a.booking_no,b.width,c.po_breakdown_id,c.color_id");
	   //echo $sql_prod;
	$data_prod=sql_select($sql_prod);
	$recv_id='';
	foreach($data_prod as $row){
	//$knitting_prod[$row[csf('width')]][$row[csf('po_breakdown_id')]][$row[csf('booking_no')]][$row[csf('color_id')]]=$row[csf('knitting_qnty')];	
	//$key=$row[csf('booking_no')].$row[csf('po_breakdown_id')].$row[csf('width')].$row[csf('color_id')];
	$key=$row[csf('booking_no')].$row[csf('po_breakdown_id')].$row[csf('width')];
	$knitting_prod[$key]+=$row[csf('knitting_qnty')];	
	}
		 //var_dump($knitting_prod);
		
		
		
		?>
        
        
        
        
        
        
        
        
        <fieldset style="width:2010px;">
 	<table width="2010" cellspacing="0" cellpadding="0" border="0" rules="all" >
            <tr class="form_caption">
                <td colspan="26" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="26" align="center">
                <strong>
				<? 
				if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
				{
				echo "From ".$start_date." To ".$end_date;
				}
				?>
                </strong>
                </td>
            </tr>
            <tr class="form_caption">
                <td colspan="26" align="center"><? echo $company_library[$company_name]; ?></td>
            </tr>
        </table>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2010" class="rpt_table" >
        <thead>
            <th width="40">SL</th>
            <th width="100">Buyer</th>
            <th width="70">Job No</th>
            <th width="110">Order No</th>
            
            <th width="80">Ref No</th>
            <th width="80">File No</th>
            <th width="100">Style No</th>
            
            <th width="70">Season</th>
            <th width="100">Booking No</th>
            <th width="70">Booking Type</th>
            <th width="30">Is Short</th>
            <th width="80">Booking Date</th>
            <th width="70">Construction</th>
            <th width="70">Composition</th>
            <th width="50">GSM</th>
            <th width="100">Fabric Color</th>
            <th width="100">Color Type</th>
            <th width="60">Dia/Width Type</th>
            <th width="70">Booking Qty</th>
            <th width="100">Machine Dia X Gauge</th>
            <th width="50">Stitch Length</th>
            <th width="50">Yarn Count</th>
            
            <th width="70">Knitting Prod.</th>
            <th width="70">Knitting Balance</th>
            
            
            <th>Remarks</th>
           
        </thead>
    </table>
    <div style="width:2030px; overflow-y:scroll; max-height:450px;" id="scroll_body">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2010" class="rpt_table" id="tbl_list_search">
        
        <?
      
         $total_booking_qty=0;
         $ii=1; 
         foreach($report_data_array[dia_width] as $dia_width=>$dia_widthvalue)
         {
			 ?>
             <tr bgcolor="#A0A0A4"> 
            <td width="40" colspan="25"><strong><? echo "Dia:  ".$dia_widthvalue; ?></strong></td>
            </tr>
             
             <?
			 $total_booking_qnty_dia_wise=0;
		 foreach($report_data_array[$dia_widthvalue] as $pre_cost_fabric_cost_dtls_id=>$pre_cost_fabric_cost_dtls_idvalue)
         {
		 foreach($report_data_array[$dia_widthvalue][$pre_cost_fabric_cost_dtls_id] as $po_id=>$po_id_value)
         {
		 foreach($report_data_array[$dia_widthvalue][$pre_cost_fabric_cost_dtls_id][$po_id] as $booking_no=>$booking_no_value)
         {
		 foreach($report_data_array[$dia_widthvalue][$pre_cost_fabric_cost_dtls_id][$po_id][$booking_no] as $fabric_color_id=>$fabric_color_value)
         {
         if($ii%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
         ?>
         <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $ii; ?>"> 
            <td width="40"><? echo $ii; ?></td>
            
            <td width="100"><p><? echo $buyer_library[$data_arr[buyer_name][$po_id]]; ?></p></td>
            <td width="70" title="<? echo  $pre_cost_fabric_cost_dtls_id;?>"><p><?  echo $fabric_color_value[job_no]; ?></p></td>
            <td width="110" title="<? echo $po_id; ?>"><p><?  echo  $data_arr[po_number][$po_id]; ?></p></td>
            
            <td width="80"><p><? echo $data_arr[grouping][$po_id]; ?></p></td>
            <td width="80"><p><? echo $data_arr[file_no][$po_id]; ?></p></td>
            <td width="100"><p><? echo $data_arr[style_ref_no][$po_id]; ?></p></td>

            <td width="70" align="center"><p><?  echo  $data_arr[season][$po_id]; ?></p></td>
            <td width="100"><p><?  echo $fabric_color_value[booking_no]; ?></p></td>
             <td width="70"><p>
			 <? 
			 if($fabric_color_value[booking_type]==1)
			 {
			   echo "Main Fabric";
			 }
			 if($fabric_color_value[booking_type]==4)
			 {
			   echo "Sample Fabric";
			 }
			 ?></p></td>
              <td width="30"><p><?  echo $yes_no[$fabric_color_value[is_short]]; ?></p></td>
            <td width="80"><p><?  echo $fabric_color_value[booking_date]; ?></p></td>
            
            <td width="70"><p><?  echo $data_arr[construction][$pre_cost_fabric_cost_dtls_id][$po_id]; ?></p></td>
            <td width="70" align="center"><p><?  echo $data_arr[composition][$pre_cost_fabric_cost_dtls_id][$po_id]; ?></p></td>
            
            <td width="50" align="center"><p><?  echo $data_arr[gsm_weight][$pre_cost_fabric_cost_dtls_id][$po_id]; ?></p></td>
             <td width="100"><p><? echo $color_library[$fabric_color_id]; ?></p></td>
            <td width="100"><p><? echo $color_type[$data_arr[color_type][$pre_cost_fabric_cost_dtls_id][$po_id]]; ?></p></td>
            <td width="60" align="center"><p><? echo $fabric_typee[$data_arr[width_dia_type][$pre_cost_fabric_cost_dtls_id][$po_id]]; ?></p></td>
            
            
            <td width="70" align="right"><p><? echo number_format($fabric_color_value[grey_fab_qnty],2); $total_booking_qty+=$fabric_color_value[grey_fab_qnty]; $total_booking_qnty_dia_wise+=$fabric_color_value[grey_fab_qnty]; ?></p></td>
            <td width="100"><p></td>
            <td width="50"><p></td>
            <td width="50"><p></td>
            <td width="70" align="right"><? 
				$key=$booking_no.$po_id.$dia_width;
				
				//echo $booking_no.'*'.$po_id.'*'.$dia_width.'*'.$fabric_color_id;
				echo $knitting_prod = $knitting_prod[$key]; $tot_knitting_prod+=$knitting_prod;
			?></td>
            <td width="70" align="right"><? echo number_format($knitting_prod_bal=($fabric_color_value[grey_fab_qnty]-$knitting_prod),2); $tot_knitting_prod_bal+=$knitting_prod_bal;?></td>
            <td></td>
            
        </tr>
       <?
       $ii++; 
	   }
	   }
	   }
	   }
	   ?>
       <tr>
            <td colspan="18" >Dia Total:</td>
            <td width="70" align="right"><? echo number_format($total_booking_qnty_dia_wise,2,'.',''); ?></td>
            <td width="100">&nbsp;</td>
            <td width="50">&nbsp;</td>
            <td width="50">&nbsp;</td>
            <td width="70">&nbsp;</td>
            <td width="70">&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
       <?
	   }
	  ?>
       <tfoot>
            <th  colspan="18" align="left"> Grand Total:</th>
            <th width="70"><? echo number_format($total_booking_qty,2,'.',''); ?></th>
            <th width="100"></th>
            <th width="50"></th>
            <th width="50"></th>
            <th width="70"><? echo number_format($tot_knitting_prod,2,'.',''); ?></th>
            <th width="70"><? echo number_format($tot_knitting_prod_bal,2,'.',''); ?></th>
            <th></th>
            
        </tfoot>
     </table>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2010" class="rpt_table">
        
     </table>
    </div>
</fieldset>

<?
	exit();
}
if($action=="receive_grey_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id;
	?>
    <script>
	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
	}	
</script>	
<fieldset style="width:540px; margin-left:3px">
		<div align="center">
        <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        <div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="540" cellpadding="0" cellspacing="0" align="center">
                 <caption>
                    <b>Knit Grey Fabrics Received Info</b>
               </caption>
				<thead>
                    <th width="30">Sl</th>
                     <th width="80">Receive Date</th>
                     <th width="70">Prog. No</th>
                    <th width="100">Receive ID</th>
                     <th width="50">Receive Ch. No</th>
                     <th width="80">Receive Qty</th>
                    <th width="50">Roll</th>
                    <th width="50">Rack No</th>
				</thead>
                <tbody>
                <?
					$i=1;
					$knitting_recv_qnty_array=array(); $prod_id_arr=array();
					$sql_prod=("select a.id, a.booking_id,b.no_of_roll as roll,b.rack,b.order_id,a.recv_number, a.receive_date,a.challan_no, sum(b.grey_receive_qnty) as knitting_qnty, max(trans_id) as trans_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and  a.company_id='$companyID' and  a.booking_id='$prog_no'  and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 group by a.id, a.booking_id,b.rack,b.order_id,a.recv_number, a.receive_date,b.no_of_roll,a.challan_no");
					//echo $sql_prod;
					$data_prod=sql_select($sql_prod);
					$recv_id='';
					foreach($data_prod as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						//echo $row[csf('id')];
						if($row[csf('trans_id')]>0)
						{
						?>
                            <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="70"><p><? echo $prog_no; ?></p></td>
                            <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="50"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('knitting_qnty')],2); ?></p></td>
                            <td width="50"><p><? echo $row[csf('roll')]; ?></p></td>
                            <td width="50"><p><? echo $row[csf('rack')]; ?></p></td>
                        </tr> 
						<?
						$tot_qty+=$row[csf('knitting_qnty')];
						}
						else
						{
							if($recv_id=='') $recv_id= $row[csf('id')]; else $recv_id.=','.$row[csf('id')];
						}
						$i++;
					}
					//var_dump($knitting_recv_qnty_array[$row[csf('booking_id')]]);
					$sql_recv="select a.id, a.booking_id,b.no_of_roll as roll,b.rack,b.order_id,a.recv_number, a.receive_date,a.challan_no,a.booking_id, sum(b.grey_receive_qnty) as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.item_category=13 and a.company_id='$companyID' and  a.booking_id in($recv_id) and a.entry_form=22 and a.receive_basis=9 and b.status_active=1 and b.is_deleted=0 group by a.booking_id,a.id, a.booking_id,b.no_of_roll,b.rack,b.order_id,a.recv_number, a.receive_date,a.challan_no";
					$data_recv=sql_select($sql_recv);
					foreach($data_recv as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
								?>
                            <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="70"><p><? echo $prog_no; ?></p></td>
                            <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="50"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('knitting_qnty')],2); ?></p></td>
                            <td width="50"><p><? echo $row[csf('roll')]; ?></p></td>
                            <td width="50"><p><? echo $row[csf('rack')]; ?></p></td>
                        </tr> 
						<?
						$tot_qty_gery+=$row[csf('knitting_qnty')];
						$i++;
					}
					$total_balance=$tot_qty_gery+$tot_qty;
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right">Total</td>
                        <td align="right"><? echo number_format($total_balance,2); ?>&nbsp;</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
              </div>
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="issue_grey_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id;
	?>
    <script>
	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
	}	
</script>	
<fieldset style="width:540px; margin-left:3px">
		<div align="center">
        <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        <div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="540" cellpadding="0" cellspacing="0" align="center">
                 <caption>
                    <b>Knit Grey Fabrics Received Info</b>
               </caption>
				<thead>
                    <th width="30">Sl</th>
                     <th width="80">Issue Date</th>
                     <th width="70">Prog. No</th>
                    <th width="100">Issue ID</th>
                     <th width="50">Issue Ch. No</th>
                     <th width="80">Delivery Qty</th>
                    <th width="50">Roll</th>
                    <th width="50">Rack No</th>
				</thead>
                <tbody>
                <?
					$i=1;
					$mrr_sql=( "select a.issue_number,a.issue_date,a.challan_no,b.no_of_roll,b.rack,b.program_no, b.issue_qnty as knitting_issue_qnty from  inv_issue_master a,inv_grey_fabric_issue_dtls b where a.id=b.mst_id and a.item_category=13 and b.program_no='$prog_no' and a.entry_form=16 and a.issue_basis=3 and b.status_active=1 and b.is_deleted=0  group by b.program_no,b.issue_qnty,a.issue_number,a.issue_date,a.challan_no,b.no_of_roll,b.rack ");
					$dtlsArray=sql_select($mrr_sql);
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="70"><p><? echo $prog_no; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="50"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('knitting_issue_qnty')],2); ?></p></td>
                            <td width="50"><p><? echo $row[csf('no_of_roll')]; ?></p></td>
                            <td width="50"><p><? echo $row[csf('rack')]; ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('knitting_issue_qnty')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
              </div>
        </div>
    </fieldset>
    <?
	exit();
}
?>