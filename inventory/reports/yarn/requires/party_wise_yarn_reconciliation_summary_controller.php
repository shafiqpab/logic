<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="print_button_variable_setting")
{	
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=34 and is_deleted=0 and status_active=1");
	if($print_report_format=='') $print_report_format=0;else $print_report_format=$print_report_format;
	echo "document.getElementById('hidden_report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();	
}

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
			// alert(str[0]);
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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'party_wise_yarn_reconciliation_summary_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	//echo $year_id;die;
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
	if($db_type==0) $year_field_by=" and YEAR(insert_date)"; 
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
	//echo $sql;die;

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

	if ($cbo_knitting_source==3)
	{
		$sql="select a.id, a.supplier_name as party_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and c.supplier_id=b.supplier_id and c.tag_company=$companyID and b.party_type in(1,9,20) and a.status_active=1  group by a.id, a.supplier_name order by a.supplier_name";
	}
	elseif($cbo_knitting_source==1)
	{
		$sql="select id, company_name as party_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name";
	}

	echo create_list_view("tbl_list_search", "Party Name", "380","380","270",0, $sql , "js_set_value", "id,party_name", "", 1, "0", $arr , "party_name", "",'setFilterGrid("tbl_list_search",-1);','0','',1) ;
	
	exit(); 
} 

if($action=="booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id=new Array; var selected_name=new Array; var booking_type=new Array; var job_no=new Array;
		
		function check_all_data()
		{
			// alert('find');return;
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value2( str ) 
		{
			// alert(str);return;
			if (str!="") str=str.split("_");
			if(booking_type.length==0)
			{
				booking_type.push( str[3] );
			}
			else if( jQuery.inArray( str[3], booking_type )== -1 &&  booking_type.length>0)
			{
				alert("Booking Mixed is Not Allow");return;
			}
			
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );

			if( jQuery.inArray( str[4], job_no ) == -1 ) 
			{
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				job_no.push( str[4] );
				// alert(job_no+'Test');
			}
			else 
			{
				// alert(job_no+'=Test2'); 
				for( var i = 0; i < job_no.length; i++ ) 
				{
					// alert(job_no[i] +'=='+ str[4] );
					if( job_no[i] == str[4] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				job_no.splice( i, 1 );
				
			}
			
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) 
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';				
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			//alert(id+'*'+name+'='+str[3]);return;// 16845*OG-Fb-20-00063=1 

			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
			//$('#hide_recv_id').val( rec_id );
			$("#hide_booing_type").val(str[3]);
		}
	</script>

	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:680px;">
					<table width="670" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th>Booking Type</th>

							<th>Search By</th>
							<th id="search_by_td_up" width="170">Please Enter Booking No</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
							<input type="hidden" name="hide_booing_type" id="hide_booing_type" value="" />
							<!--   <input type="hidden" name="hide_recv_id" id="hide_recv_id" value="" />-->
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
									$search_by_arr=array(1=>"With Order",2=>"Without Order");
									$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
									echo create_drop_down( "cbo_booking_type", 100, $search_by_arr,"",0, "--Select--", "3",$dd,0 );
									?>
								</td>               
								<td align="center">	
									<?
									$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Booking No");
									$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
									echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "3",$dd,0 );
									?>
								</td>     
								<td align="center" id="search_by_td">				
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
								</td> 	
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+document.getElementById('cbo_booking_type').value+'**'+'<? echo $job_ids;?>', 'create_booking_no_search_list_view', 'search_div', 'party_wise_yarn_reconciliation_summary_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_booking_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	//echo $year_id;die;
	$month_id=$data[5];
	$booking_type=$data[6];
	$job_ids=$data[7];

	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{ 
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else 
			{ 
				$buyer_id_cond="";$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
		$buyer_id_cond2=" and a.buyer_id=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) 
	{
		$search_field="a.style_ref_no";
	}
	else if($search_by==1)
	{
		$search_field="a.job_no_prefix_num";
	}
	else $search_field="b.booking_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	else $year_field_by="";
	if($db_type==0) $month_field_by="and month(a.insert_date)"; 
	else if($db_type==2) $month_field_by=" and to_char(a.insert_date,'MM')";
	else $month_field_by="";
	if($db_type==0) $year_field=" YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="  to_char(a.insert_date,'YYYY') as year";
	else $year_field="";

	if($year_id!=0) $year_cond=" $year_field_by=$year_id"; else $year_cond="";
	if($month_id!=0) $month_cond=" $month_field_by=$month_id"; else $month_cond="";
	if($job_ids!="") $job_cond = " and a.id in ($job_ids) ";



	if($booking_type==1)
	{
		 $sql= "select a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.booking_no,c.id as booking_id  
		 from wo_po_details_master a, wo_booking_dtls b, wo_booking_mst c 
		 where a.job_no=b.job_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.booking_type in(1,4,3) and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond $month_cond $job_cond group by a.job_no,b.booking_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,c.id  order by a.job_no desc";
	}
	else if($booking_type==2 && $job_ids =="")
	{
		$sql= "select a.company_id as company_name, a.buyer_id as buyer_name, b.style_des as style_ref_no,a.booking_no,a.id as booking_id  from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.booking_type=4 and a.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond2 $year_cond $month_cond group by a.booking_no,a.company_id, a.buyer_id, b.style_des,a.id  order by a.booking_no desc";
	}
	//echo $sql;die;
	$sqlResult=sql_select($sql);
	?>

	<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="130">Company</th> 
				<th width="110">Buyer</th>
				<th width="110">Job No</th>
				<th width="120">Style Ref.</th>
				<th width="">Booking No</th>

			</thead>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach($sqlResult as $row )
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				$data=$i.'_'.$row[csf('booking_id')].'_'.$row[csf('booking_no')].'_'.$booking_type.'_'.$row[csf('job_no')];
						//echo $data;
				?>
				<tr id="tr_<?php echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value2('<? echo $data;?>')">
					<td width="30" align="center"><?php echo $i; ?>
					<td width="130"><p><? echo $company_arr[$row[csf('company_name')]]; ?></p></td>
					<td width="110"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
					<td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
					<td width="120"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td width=""><p><? echo $row[csf('booking_no')]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
		<table width="600" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/>
							Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px"/>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</form>

	<?
	
	exit(); 
} 

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	ob_start();
	if($type==2) //Source and Party Wise
	{
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		$txt_job_no=str_replace("'","",$txt_job_no);
		$txt_booking_no=str_replace("'","",$txt_booking_no);
		$txt_internal_ref=str_replace("'","",$txt_internal_ref);
		$txt_date_from=str_replace("'","",$txt_date_from);
		$txt_date_to=str_replace("'","",$txt_date_to);
		$txt_job_id=str_replace("'","",$txt_job_id);
		$hide_booking_id=str_replace("'","",$hide_booking_id);
		$hide_booking_type=str_replace("'","",$hide_booking_type);
		$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
		$txt_knitting_com_id=str_replace("'","",$txt_knitting_com_id);
		$cbo_year=str_replace("'","",$cbo_year);

		if($cbo_knitting_source) $source_cond = " and a.knitting_source=".$cbo_knitting_source;
		if($txt_knitting_com_id!="") $source_cond = " and a.knitting_company in (".$txt_knitting_com_id.")";

		if($cbo_knitting_source) $source_cond2 = " and a.knit_dye_source=".$cbo_knitting_source;
		if($txt_knitting_com_id!="") $source_cond2 = " and a.knit_dye_company in (".$txt_knitting_com_id.")";
		
		$issue_wise_location=return_library_array( "select id, location_id from inv_issue_master where status_active=1 and is_deleted=0 and entry_form=3 and issue_basis in(1,3) and issue_purpose in(1,4,8) and company_id=$cbo_company_name", "id", "location_id");
		$job_no_arr=return_library_array( "select id, job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0", "id", "job_no_mst");
		// print_r($job_no_arr);die;
		
		$all_data_arr=array(); $all_po_check=array();$all_po_id="";
		
		if($txt_job_no =="" && $txt_booking_no =="" && $txt_internal_ref =="" && $txt_date_from !="" && $txt_date_to !="")
		{
			
			$issue_sql="SELECT a.id as mst_id, a.entry_form, a.issue_number, a.challan_no, a.issue_basis, a.issue_purpose, a.booking_no, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, b.id as trans_id, b.prod_id, b.transaction_date, b.requisition_no, b.brand_id, b.cons_uom, b.cons_quantity,b.remarks, c.po_breakdown_id as po_breakdown_id
			from inv_issue_master a, inv_transaction b, order_wise_pro_details c
			where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=3 and c.entry_form=3 and nvl(c.is_sales,0)!=1 and a.issue_purpose in(1,4) and b.transaction_type=2 and c.trans_type=2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.knit_dye_company>0 and a.issue_basis in(1,3) and a.company_id=$cbo_company_name and b.transaction_date between '$txt_date_from' and '$txt_date_to' $source_cond2
			union all
			select a.id as mst_id, a.entry_form, a.issue_number, a.challan_no, a.issue_basis, a.issue_purpose, a.booking_no, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, b.id as trans_id, b.prod_id, b.transaction_date, b.cons_uom, b.requisition_no, b.brand_id, b.cons_quantity,b.remarks, 0 as po_breakdown_id
			from inv_issue_master a, inv_transaction b
			where a.id=b.mst_id and a.entry_form=3 and a.issue_purpose=8 and b.transaction_type=2 and a.status_active=1 and b.status_active=1 and a.knit_dye_company>0 and a.issue_basis in(1,3) and a.company_id=$cbo_company_name and b.transaction_date between '$txt_date_from' and '$txt_date_to' $source_cond2";
			
			// echo $issue_sql;die;
			
			$issue_result=sql_select($issue_sql);
			
			if(count($issue_result)<1)
			{
				echo '<span style="font-weight: bold; font-size:20px;">No Data Found</span>';die;
			}
			
			foreach($issue_result as $row)
			{
				if($trans_check[$row[csf("mst_id")]][$row[csf("trans_id")]]=="")
				{
					$trans_check[$row[csf("mst_id")]][$row[csf("trans_id")]]=$row[csf("trans_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("issue_date")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("issue_number")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("issue_basis")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_purpose"]=$row[csf("issue_purpose")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["requisition_no"].=$row[csf("requisition_no")].",";
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
				}
				if($issue_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=="" && $row[csf("po_breakdown_id")]>0)
				{
					$issue_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=$row[csf("po_breakdown_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["po_breakdown_id"].=$row[csf("po_breakdown_id")].",";
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["job_no"] = $job_no_arr[$row[csf("po_breakdown_id")]];
				}
				
				if($all_po_check[$row[csf("po_breakdown_id")]]=="" && $row[csf("po_breakdown_id")]>0)
				{
					$all_po_check[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
					$all_po_id.=$row[csf("po_breakdown_id")].",";
				}
				
			}
			
			unset($issue_result);
			$book_without_order_cond="";
			if($db_type==0)
			{
				$book_without_order_cond=" and (b.booking_without_order=0 or b.booking_without_order='')";
			}
			else
			{
				$book_without_order_cond=" and (b.booking_without_order=0 or b.booking_without_order is null)";
			}
			
			$receive_sql="SELECT a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, b.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no,b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type,b.remarks, c.po_breakdown_id as po_breakdown_id, a.issue_id 
			from inv_receive_master a, inv_transaction b , order_wise_pro_details c
			where a.id=b.mst_id and b.id=c.trans_id and b.item_category in(1,13) and a.entry_form in(9,2,22,58) and c.entry_form in(9,2,22,58) and nvl(c.is_sales,0)!=1 and b.transaction_type in(1,4) and c.trans_type in(1,4) and a.receive_basis in(1,2,3,9,10,11)  $book_without_order_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.company_id=$cbo_company_name and b.transaction_date between '$txt_date_from' and '$txt_date_to' $source_cond
			union all
			select a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, b.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type,b.remarks, 0 as po_breakdown_id , a.issue_id
			from inv_receive_master a, inv_transaction b 
			where a.id=b.mst_id and b.item_category in(1,13) and a.entry_form in(9,2,22,58) and b.transaction_type in(1,4) and a.receive_basis in(1,2,3,9,10,11) and b.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and b.transaction_date between '$txt_date_from' and '$txt_date_to' $source_cond";
			//echo $receive_sql;die;
			$receive_result=sql_select($receive_sql);
			
			foreach($receive_result as $row)
			{
				if($trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=="")
				{
					$location_id="";
					if($row[csf('entry_form')]==9)
					{
						$location_id=$issue_wise_location[$row[csf('issue_id')]];
					}
					else
					{
						$location_id=$row[csf('location_id')];
					}
					$trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=$row[csf("trans_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("receive_date")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("recv_number")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("receive_basis")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_id"]=$row[csf("booking_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_without_order"]=$row[csf("booking_without_order")];
					
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["pi_wo_batch_no"]=$row[csf("pi_wo_batch_no")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];
					
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_reject_qnty"]+=$row[csf("cons_reject_qnty")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
				}
				if($rcv_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=="" && $row[csf("po_breakdown_id")]>0)
				{
					$rcv_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=$row[csf("po_breakdown_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["po_breakdown_id"].=$row[csf("po_breakdown_id")].",";
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["job_no"]= $job_no_arr[$row[csf("po_breakdown_id")]];
				}
				
				if($all_po_check[$row[csf("po_breakdown_id")]]=="" && $row[csf("po_breakdown_id")]>0)
				{
					$all_po_check[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
					$all_po_id.=$row[csf("po_breakdown_id")].",";
				}
			}

			
			unset($receive_result);
			
			$all_po_id=chop($all_po_id,",");
			if($all_po_id!="")
			{
				$all_po_id=array_unique(explode(",",$all_po_id));
				if($db_type==2 && count($all_po_id)>999)
				{
					$po_cond=" and (";
					$poIdsArr=array_chunk($all_po_id,999);
					foreach($poIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$po_cond.=" b.id in($ids) or"; 
					}
					$po_cond=chop($po_cond,'or ');
					$po_cond.=")";
				}
				else
				{
					$poIds=implode(",",$all_po_id);
					$po_cond=" and b.id in($poIds)";
				}
				$order_sql="select a.job_no, a.style_ref_no, a.buyer_name, b.id, b.po_number, b.po_quantity, b.grouping as int_file_no from wo_po_break_down b, wo_po_details_master a where b.job_no_mst=a.job_no $po_cond";
				//echo $order_sql;die;
				$datapoArray=sql_select($order_sql);
				foreach($datapoArray as $row)
				{
					$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
					$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
					$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
					$po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
					$po_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
					$po_arr[$row[csf('id')]]['int_file_no']=$row[csf('int_file_no')];
				}
				
				unset($datapoArray);
				
				$sql_requ= "select a.dtls_id, a.booking_no, b.requisition_no,b.prod_id from ppl_planning_entry_plan_dtls a, ppl_yarn_requisition_entry b where b.knit_id=a.dtls_id and  a.status_active=1 and a.is_deleted=0";
				$result_requ=sql_select($sql_requ);
				$requisition_booking_arr=array();
				$grey_booking_no=array();
				foreach($result_requ as $row)
				{
					$requisition_booking_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['booking_no']=$row[csf('booking_no')];
					$grey_booking_no[$row[csf('dtls_id')]]['booking_no']=$row[csf('booking_no')];
				}

			}
			
		}
		else
		{
			if($txt_booking_no !="" && $hide_booking_type==2)
			{
				$booking_ord_sql="select a.company_id as company_name, a.buyer_id as buyer_name, a.booking_no, a.id as booking_id  
				from wo_non_ord_samp_booking_mst a 
				where  a.id in($hide_booking_id)";
				$booking_ord_result=sql_select($booking_ord_sql);
				foreach($booking_ord_result as $row)
				{
					$po_arr[$row[csf('booking_id')]]['buyer_name']=$row[csf('buyer_name')];
				}
				
				unset($booking_ord_result);
				
				if($txt_date_from!="" && $txt_date_to!="")
				{
					$date_condtion=" and b.transaction_date between '$txt_date_from' and '$txt_date_to'";
				}
				
				
				$issue_sql="select a.id as mst_id, a.entry_form, a.issue_number, a.challan_no, a.issue_basis, a.issue_purpose, a.booking_no, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, b.id as trans_id, b.prod_id, b.transaction_date, b.cons_uom, b.requisition_no, b.brand_id, b.cons_quantity,b.remarks, 0 as po_breakdown_id
				from inv_issue_master a, inv_transaction b
				where a.id=b.mst_id and a.entry_form=3 and a.issue_purpose=8 and b.transaction_type=2 and a.status_active=1 and b.status_active=1 and a.knit_dye_company>0 and a.issue_basis in(1,3) and a.company_id=$cbo_company_name and a.booking_id in($hide_booking_id) $date_condtion $source_cond2 ";
				
				//echo $issue_sql;die;
				
				$issue_result=sql_select($issue_sql);
				
				
				if(count($issue_result)<1)
				{
					echo '<span style="font-weight: bold; font-size:20px;">No Data Found</span>';die;
				}
				
				foreach($issue_result as $row)
				{
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("transaction_date")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("issue_number")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("issue_basis")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_purpose"]=$row[csf("issue_purpose")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["requisition_no"].=$row[csf("requisition_no")].",";
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
				}
				
				unset($issue_result);
				
				$receive_sql="select a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, a.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type,b.remarks, 0 as po_breakdown_id, a.issue_id
				from inv_receive_master a, inv_transaction b 
				where a.id=b.mst_id and b.item_category in(1,13) and a.entry_form in(9,2,22,58) and b.transaction_type in(1,4) and a.receive_basis in(1,2,3,9,10,11) and a.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.booking_id in($hide_booking_id) $date_condtion $source_cond";
				//echo $receive_sql;die;
				$receive_result=sql_select($receive_sql); // [$row[csf('location_id')]]
				
				foreach($receive_result as $row)
				{
					$location_id="";
					if($row[csf('entry_form')]==9)
					{
						$location_id=$issue_wise_location[$row[csf('issue_id')]];
					}
					else
					{
						$location_id=$row[csf('location_id')];
					}
					
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("receive_date")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("recv_number")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];

					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("receive_basis")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_id"]=$row[csf("booking_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_without_order"]=$row[csf("booking_without_order")];
					
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["pi_wo_batch_no"]=$row[csf("pi_wo_batch_no")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];
					
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_reject_qnty"]+=$row[csf("cons_reject_qnty")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
				}
				
				unset($receive_result);
			}
			else
			{
				if($txt_date_from!="" && $txt_date_to!="")
				{
					$date_condtion=" and b.transaction_date between '$txt_date_from' and '$txt_date_to'";
				}
				
				if($db_type==0) $year_field_by=" and YEAR(a.insert_date)"; 
				else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
				else $year_field_by="";
				
				$year_cond="";
				if($cbo_year > 0) $year_cond=" $year_field_by=$cbo_year";
				
				if($txt_booking_no !="")
				{
					$all_po_check=array();$all_po_id="";
					$booking_ord_sql= "select a.job_no, a.style_ref_no, a.buyer_name, b.id, b.po_number, b.po_quantity, b.grouping as int_file_no from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst d 
					where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no=d.booking_no and d.id in($hide_booking_id) $year_cond";
					// echo $booking_ord_sql;die;
					$booking_ord_result=sql_select($booking_ord_sql);
					foreach($booking_ord_result as $row)
					{
						$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
						$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
						$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
						$po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
						$po_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
						$po_arr[$row[csf('id')]]['int_file_no']=$row[csf('int_file_no')];
						if($all_po_check[$row[csf('id')]]=="")
						{
							$all_po_check[$row[csf('id')]]=$row[csf('id')];
							$all_po_id.=$row[csf('id')].",";
						}
					}
					
					unset($booking_ord_result);
					
					$book_arr=explode(",",$txt_booking_no);
					$book_cond="";
					foreach($book_arr as $book_no)
					{
						$book_cond.="'".$book_no."',";
					}
					$book_cond=chop($book_cond,",");
					$sql_requ= "select a.dtls_id, a.booking_no,b.requisition_no,b.prod_id from ppl_planning_entry_plan_dtls a, ppl_yarn_requisition_entry b where b.knit_id=a.dtls_id and  a.status_active=1 and a.is_deleted=0 and a.booking_no in($book_cond)";
					//echo $sql_requ;die;
					$result_requ=sql_select($sql_requ);
					$requisition_booking_arr=array();
					$grey_booking_no=array();
					foreach($result_requ as $row)
					{
						$requisition_booking_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['booking_no']=$row[csf('booking_no')];
						$grey_booking_no[$row[csf('dtls_id')]]['booking_no']=$row[csf('booking_no')];
						$all_plan_id.=$row[csf('dtls_id')].",";
						$all_req_no.=$row[csf('requisition_no')].",";
					}
					$all_plan_id=implode(",",array_unique(explode(",",chop($all_plan_id,","))));
					$all_req_no=implode(",",array_unique(explode(",",chop($all_req_no,","))));
					
					//echo $all_plan_id;die;
					if($all_req_no=="") $req_no_cond=""; else $req_no_cond=" and b.requisition_no in($all_req_no)";
					if($all_req_no=="") $iss_rtn_req_no_cond=""; else $iss_rtn_req_no_cond=" and a.booking_id in($all_req_no)";
					if($all_plan_id=="") $all_plan_id_cond=""; else $all_plan_id_cond=" and a.booking_id in($all_plan_id)";

					if($db_type==0) $trans_year=" and YEAR(b.transaction_date)"; 
					else if($db_type==2) $trans_year=" and to_char(b.transaction_date,'YYYY')";
					else $trans_year="";
					
					$trans_year_cond="";
					if($cbo_year > 0) $trans_year_cond=" $trans_year=$cbo_year";

					$issue_sql="SELECT a.id as mst_id, a.entry_form, a.issue_number, a.challan_no, a.issue_basis, a.issue_purpose, a.booking_no, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, b.id as trans_id, b.prod_id, b.transaction_date, b.requisition_no, b.brand_id, b.cons_uom, b.cons_quantity,b.remarks, c.po_breakdown_id as po_breakdown_id
					from inv_issue_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=3 and c.entry_form=3 and nvl(c.is_sales,0)!=1 and a.issue_purpose in(1,4) and b.transaction_type=2 and c.trans_type=2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.knit_dye_company>0 and a.issue_basis in(1) and a.company_id=$cbo_company_name and a.booking_id in($hide_booking_id) $date_condtion $source_cond2 $trans_year_cond
					union all
					select a.id as mst_id, a.entry_form, a.issue_number, a.challan_no, a.issue_basis, a.issue_purpose, a.booking_no, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, b.id as trans_id, b.prod_id, b.transaction_date, b.requisition_no, b.brand_id, b.cons_uom, b.cons_quantity,b.remarks, c.po_breakdown_id as po_breakdown_id
					from inv_issue_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=3 and c.entry_form=3 and a.issue_purpose in(1,4) and b.transaction_type=2 and c.trans_type=2 and  nvl(c.is_sales,0)!=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.knit_dye_company>0 and a.issue_basis in(3) and a.company_id=$cbo_company_name $req_no_cond $date_condtion $source_cond2 $trans_year_cond";
					//c.is_sales!=1 and
					//echo $issue_sql;die;
					
					$issue_result=sql_select($issue_sql);
					if(count($issue_result)<1)
					{
						echo '<span style="font-weight: bold; font-size:20px;">No Data Found</span>';die;
					}
					foreach($issue_result as $row) //
					{
						if($trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=="")
						{
							$trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=$row[csf("trans_id")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("transaction_date")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("issue_number")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("issue_basis")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_purpose"]=$row[csf("issue_purpose")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["requisition_no"].=$row[csf("requisition_no")].",";
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
						}
						if($issue_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=="" && $row[csf("po_breakdown_id")]>0)
						{
							$issue_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=$row[csf("po_breakdown_id")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["po_breakdown_id"].=$row[csf("po_breakdown_id")].",";
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["job_no"] = $job_no_arr[$row[csf("po_breakdown_id")]];
						}
					}
					unset($issue_result);
					$book_without_order_cond="";
					if($db_type==0)
					{
						$book_without_order_cond=" and (a.booking_without_order=0 or a.booking_without_order='')";
					}
					else
					{
						$book_without_order_cond=" and (a.booking_without_order=0 or a.booking_without_order is null)";
					}
					
					$issue_rtn_sql="SELECT a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.issue_id, a.receive_basis, a.entry_form, a.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type,b.remarks, c.po_breakdown_id as po_breakdown_id 
					from inv_receive_master a, inv_transaction b , order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id and b.item_category in(1) and a.entry_form in(9) and c.entry_form in(9) and nvl(c.is_sales,0)!=1 and b.transaction_type in(4) and c.trans_type in(4) and a.receive_basis in(1) $book_without_order_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.company_id=$cbo_company_name and a.booking_id in($hide_booking_id) $date_condtion $source_cond $trans_year_cond
					union all
					select a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.issue_id, a.receive_basis, a.entry_form, a.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type,b.remarks, c.po_breakdown_id as po_breakdown_id 
					from inv_receive_master a, inv_transaction b , order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id and b.item_category in(1) and a.entry_form in(9) and c.entry_form in(9) and nvl(c.is_sales,0)!=1 and b.transaction_type in(4) and c.trans_type in(4) and a.receive_basis in(3) $book_without_order_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.company_id=$cbo_company_name $iss_rtn_req_no_cond $date_condtion $source_cond $trans_year_cond";
					 //echo $issue_rtn_sql;die;

					$issue_rtn_result=sql_select($issue_rtn_sql);
					foreach($issue_rtn_result as $row)
					{
						if($trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=="")
						{
							$trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=$row[csf("trans_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$issue_wise_location[$row[csf('issue_id')]]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$issue_wise_location[$row[csf('issue_id')]]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("receive_date")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$issue_wise_location[$row[csf('issue_id')]]][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$issue_wise_location[$row[csf('issue_id')]]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("recv_number")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$issue_wise_location[$row[csf('issue_id')]]][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$issue_wise_location[$row[csf('issue_id')]]][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("receive_basis")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$issue_wise_location[$row[csf('issue_id')]]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_id"]=$row[csf("booking_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$issue_wise_location[$row[csf('issue_id')]]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$issue_wise_location[$row[csf('issue_id')]]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_without_order"]=$row[csf("booking_without_order")];
							
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$issue_wise_location[$row[csf('issue_id')]]][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$issue_wise_location[$row[csf('issue_id')]]][$row[csf('mst_id')]][$row[csf('prod_id')]]["pi_wo_batch_no"]=$row[csf("pi_wo_batch_no")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$issue_wise_location[$row[csf('issue_id')]]][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];
							
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$issue_wise_location[$row[csf('issue_id')]]][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$issue_wise_location[$row[csf('issue_id')]]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$issue_wise_location[$row[csf('issue_id')]]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$issue_wise_location[$row[csf('issue_id')]]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_reject_qnty"]+=$row[csf("cons_reject_qnty")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$issue_wise_location[$row[csf('issue_id')]]][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
						}
						if($rcv_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=="" && $row[csf("po_breakdown_id")]>0)
						{
							$rcv_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=$row[csf("po_breakdown_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$issue_wise_location[$row[csf('issue_id')]]][$row[csf('mst_id')]][$row[csf('prod_id')]]["po_breakdown_id"].=$row[csf("po_breakdown_id")].",";
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$issue_wise_location[$row[csf('issue_id')]]][$row[csf('mst_id')]][$row[csf('prod_id')]]["job_no"] = $job_no_arr[$row[csf("po_breakdown_id")]];
						}
					}
					
					unset($issue_rtn_result);

					if($all_plan_id=="")
					{
						$all_prog_id="'". 0 . "'";
					}
					else
					{
						$all_plan_id_arr=explode(",",$all_plan_id);
						foreach($all_plan_id_arr as $plan_id)
						{
							$all_prog_id.="'". $plan_id . "',";
						}
						$all_prog_id=chop($all_prog_id,",");
					}
					
					$receive_sql="SELECT a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, a.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type, c.po_breakdown_id as po_breakdown_id 
					from inv_receive_master a, inv_transaction b , order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id and b.item_category in(13) and a.entry_form in(22) and c.entry_form in(22) and b.transaction_type in(1) and c.trans_type in(1) and nvl(c.is_sales,0)!=1 and a.receive_basis in(2) $book_without_order_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.company_id=$cbo_company_name and a.booking_id in($hide_booking_id) $date_condtion $source_cond $trans_year_cond
					union all
					select a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, a.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type, c.po_breakdown_id as po_breakdown_id 
					from inv_receive_master p, inv_receive_master a, inv_transaction b , order_wise_pro_details c
					where p.id=a.booking_id and a.id=b.mst_id and b.id=c.trans_id and b.item_category in(13) and a.entry_form in(22) and c.entry_form in(22) and b.transaction_type in(1) and c.trans_type in(1) and a.receive_basis in(9) $book_without_order_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and nvl(c.is_sales,0)!=1 and c.status_active=1 and a.company_id=$cbo_company_name and p.booking_id in($hide_booking_id) $date_condtion $source_cond $trans_year_cond
					union all
					select a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, a.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type, c.po_breakdown_id as po_breakdown_id 
					from inv_receive_master a, inv_transaction b , order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id and b.item_category in(13) and a.entry_form in(2) and c.entry_form in(2) and nvl(c.is_sales,0)!=1 and b.transaction_type in(1) and c.trans_type in(1) and a.receive_basis in(1) $book_without_order_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.company_id=$cbo_company_name and a.booking_id in($hide_booking_id) $date_condtion $source_cond $trans_year_cond
					union all
					select a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, a.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type, c.po_breakdown_id as po_breakdown_id 
					from inv_receive_master a, inv_transaction b , order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id and b.item_category in(13) and a.entry_form in(2) and c.entry_form in(2) and b.transaction_type in(1) and c.trans_type in(1) and nvl(c.is_sales,0)!=1 and a.receive_basis in(2) $book_without_order_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.company_id=$cbo_company_name $all_plan_id_cond $date_condtion $source_cond $trans_year_cond
					union all
					select a.id as mst_id, a.recv_number, c.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, a.booking_without_order, b.trans_id, b.prod_id, 0 as pi_wo_batch_no, b.uom as cons_uom, 0 as brand_id, b.grey_receive_qnty as cons_quantity, 0 as return_qnty, b.reject_fabric_receive as cons_reject_qnty, 1 as transaction_type, c.po_breakdown_id as po_breakdown_id
					from inv_receive_master a, pro_grey_prod_entry_dtls b,  pro_roll_details  c
					where  a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(58) and c.entry_form in(58) and nvl(c.is_sales,0)!=1 and a.receive_basis in(10) $book_without_order_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.company_id=$cbo_company_name and (c.booking_no in($book_cond) or c.booking_no in($all_prog_id) )  $source_cond ";
					//$date_condtion , $trans_year_cond
					
					 //echo $receive_sql;die;
					
					$receive_result=sql_select($receive_sql);
					//[$row[csf('location_id')]]
					foreach($receive_result as $row)
					{
						if($trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=="")
						{
							$trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=$row[csf("trans_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("receive_date")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("recv_number")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("receive_basis")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_id"]=$row[csf("booking_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_without_order"]=$row[csf("booking_without_order")];
							
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["pi_wo_batch_no"]=$row[csf("pi_wo_batch_no")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];
							
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_reject_qnty"]+=$row[csf("cons_reject_qnty")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
						}
						if($rcv_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=="" && $row[csf("po_breakdown_id")]>0)
						{
							$rcv_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=$row[csf("po_breakdown_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["po_breakdown_id"].=$row[csf("po_breakdown_id")].",";
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["job_no"] = $job_no_arr[$row[csf("po_breakdown_id")]];
						}
					}
					
					unset($receive_result);
				}
				else
				{
					if($txt_job_no !="")
					{
						$order_sql="select a.job_no, a.style_ref_no, a.buyer_name, b.id, b.po_number, b.po_quantity, b.grouping as int_file_no from wo_po_break_down b, wo_po_details_master a where b.job_no_mst=a.job_no and a.id in($txt_job_id) $year_cond";
						$datapoArray=sql_select($order_sql);
						foreach($datapoArray as $row)
						{
							$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
							$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
							$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
							$po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
							$po_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
							$po_arr[$row[csf('id')]]['int_file_no']=$row[csf('int_file_no')];
							$all_po_id.=$row[csf('id')].",";
						}
						
						unset($datapoArray);
					}
					else
					{
						//grouping
						$order_sql="select a.job_no, a.style_ref_no, a.buyer_name, b.id, b.po_number, b.po_quantity, b.grouping as int_file_no from wo_po_break_down b, wo_po_details_master a where b.job_no_mst=a.job_no and b.grouping ='$txt_internal_ref' $year_cond";
						//echo $order_sql;
						$datapoArray=sql_select($order_sql);
						foreach($datapoArray as $row)
						{
							$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
							$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
							$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
							$po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
							$po_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
							$po_arr[$row[csf('id')]]['int_file_no']=$row[csf('int_file_no')];
							$all_po_id.=$row[csf('id')].",";
						}
						unset($datapoArray);
					}
									
					$all_po_id=chop($all_po_id,",");

					if($all_po_id!="")
					{
						$all_po_id=array_unique(explode(",",$all_po_id));
						if($db_type==2 && count($all_po_id)>999)
						{
							$po_cond=" and (";
							$po_id_cond = " and (";
							$poIdsArr=array_chunk($all_po_id,999);
							foreach($poIdsArr as $ids)
							{
								$ids=implode(",",$ids);
								$po_cond.=" c.po_breakdown_id in($ids) or"; 
								$po_id_cond.=" a.po_id in($ids) or"; 
							}
							$po_cond=chop($po_cond,'or ');
							$po_cond.=")";

							$po_id_cond=chop($po_id_cond,'or ');
							$po_id_cond.=")";
						}
						else
						{
							$poIds=implode(",",$all_po_id);
							$po_cond=" and c.po_breakdown_id in($poIds)";
							$po_id_cond=" and a.po_id in($poIds)";
						}
						
						$sql_requ= "select a.dtls_id, a.booking_no, b.requisition_no, b.prod_id from ppl_planning_entry_plan_dtls a, ppl_yarn_requisition_entry b where b.knit_id=a.dtls_id and  a.status_active=1 and a.is_deleted=0 $po_id_cond";
						//echo $sql_requ;die;
						$result_requ=sql_select($sql_requ);
						$requisition_booking_arr=array();
						$grey_booking_no=array();
						foreach($result_requ as $row)
						{
							$requisition_booking_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['booking_no']=$row[csf('booking_no')];
							$grey_booking_no[$row[csf('dtls_id')]]['booking_no']=$row[csf('booking_no')];
						}
						
						$issue_sql="SELECT a.id as mst_id, a.entry_form, a.issue_number, a.challan_no, a.issue_basis, a.issue_purpose, a.booking_no, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, b.id as trans_id, b.prod_id, b.transaction_date, b.requisition_no, b.brand_id, b.cons_uom, b.cons_quantity as cons_quantity,b.remarks, c.po_breakdown_id as po_breakdown_id
						from inv_issue_master a, inv_transaction b, order_wise_pro_details c
						where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=3 and c.entry_form=3 and nvl(c.is_sales,0)!=1 and a.issue_purpose in(1,4) and b.transaction_type=2 and c.trans_type=2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.knit_dye_company>0 and a.issue_basis in(1,3) and a.company_id=$cbo_company_name $date_condtion $po_cond $source_cond2";//b.cons_quantity,
						//echo $issue_sql;die;
						$issue_result=sql_select($issue_sql);
						if(count($issue_result)<1)
						{
							echo '<span style="font-weight: bold; font-size:20px;">No Data Found</span>';die;
						}
						
						foreach($issue_result as $row)
						{
							if($trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=="")
							{
								$trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=$row[csf("trans_id")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("transaction_date")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("issue_number")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("issue_basis")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_purpose"]=$row[csf("issue_purpose")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["requisition_no"].=$row[csf("requisition_no")].",";
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
							}
							if($issue_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=="" && $row[csf("po_breakdown_id")]>0)
							{
								$issue_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=$row[csf("po_breakdown_id")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["po_breakdown_id"].=$row[csf("po_breakdown_id")].",";
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["job_no"] = $job_no_arr[$row[csf("po_breakdown_id")]];
							}
						}
						unset($issue_result);
						$book_without_order_cond="";
						if($db_type==0)
						{
							$book_without_order_cond=" and (b.booking_without_order=0 or b.booking_without_order='')";
						}
						else
						{
							$book_without_order_cond=" and (b.booking_without_order=0 or b.booking_without_order is null)";
						}
						
						$receive_sql="SELECT a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, b.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type,b.remarks, c.po_breakdown_id as po_breakdown_id, a.issue_id 
						from inv_receive_master a, inv_transaction b , order_wise_pro_details c
						where a.id=b.mst_id and b.id=c.trans_id and b.item_category in(1,13) and a.entry_form in(9,2,22,58) and c.entry_form in(9,2,22,58) and (c.is_sales!=1 or c.is_sales is null) and b.transaction_type in(1,4) and c.trans_type in(1,4) and a.receive_basis in(1,2,3,9,10,11) $book_without_order_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.company_id=$cbo_company_name $date_condtion $po_cond $source_cond";
						//echo $receive_sql;die;
						$receive_result=sql_select($receive_sql);
						
						foreach($receive_result as $row)
						{
							if($trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=="")
							{
								$trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=$row[csf("trans_id")];
								$location_id="";
								if($row[csf('entry_form')]==9)
								{
									$location_id=$issue_wise_location[$row[csf('issue_id')]];
								}
								else
								{
									$location_id=$row[csf('location_id')];
								}
								
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("receive_date")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("recv_number")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("receive_basis")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_id"]=$row[csf("booking_id")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_without_order"]=$row[csf("booking_without_order")];
								
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["pi_wo_batch_no"]=$row[csf("pi_wo_batch_no")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];
								
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_reject_qnty"]+=$row[csf("cons_reject_qnty")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
							}
							if($rcv_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=="" && $row[csf("po_breakdown_id")]>0)
							{
								$rcv_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=$row[csf("po_breakdown_id")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["po_breakdown_id"].=$row[csf("po_breakdown_id")].",";
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["job_no"] = $job_no_arr[$row[csf("po_breakdown_id")]];
							}
						}
						
						unset($receive_result);
					}
				}
			}
		}

		$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
		$buyer_arr=return_library_array( "select id, short_name from  lib_buyer", "id", "short_name");
		$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
		$location_arr=return_library_array("select id, location_name from lib_location","id","location_name");
		?>
		<div>
			<table width="2090" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
				<tr>
					<td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr> 
				<tr>  
					<td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $report_title; ?> (Party Wise)</strong></td>
				</tr>  
				<tr> 
					<td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? //echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
				</tr>
			</table>
			<table width="2090" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="80">Date</th>
					<th width="130">Transaction Ref.</th>
					<th width="80">Challan No</th>
					<th width="100">Booking/Reqsn</th>
					<th width="120">Booking No</th>
					<th width="70">Buyer</th>
					<th width="110">Style Ref.</th>
					<th width="100">Internal Ref.</th>
					<th width="100">Job No</th>
					<th width="130">Order Numbers</th>
					<th width="90">Order Qnty.</th>
					<th width="80">Brand</th>
					<th width="150">Item Description</th>
					<th width="80">Lot</th>
					<th width="60">UOM</th>
					<th width="90">Yarn Issued</th>
					<th width="90">Fabric Received</th>
					<th width="90">Reject Fabric Received</th>
					<th width="90">Yarn Returned</th>
					<th width="90">Reject Yarn Returned</th>
					<th width="90">Balance</th> 
					<th width="">Remarks</th> 
				</thead>
			</table>
			<div style="width:2090px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="2070" cellpadding="2" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
					<?
					$product_arr=array();
					$sql_prod="select id, product_name_details, lot from product_details_master where item_category_id in (1) ";//13
					$sql_prod_res=sql_select($sql_prod);
					foreach($sql_prod_res as $rowp)
					{
						$product_arr[$rowp[csf('id')]]['name']=$rowp[csf('product_name_details')];
						$product_arr[$rowp[csf('id')]]['lot']=$rowp[csf('lot')];
					}
					unset($sql_prod_res);
					
					$i=1;
					foreach ($all_data_arr as $source_id=>$source_data )
					{
						foreach ($source_data as $party_id=>$party_data )
						{
							foreach ($party_data as $location_id=>$location_data )
							{
								?>
								<tr bgcolor="#EFEFEF"><td colspan="21" title="<? echo $location_id.jahid; ?>"><b>Party name: 
								<? 
								if($source_id==1)
								{
									echo $company_arr[$party_id];
									echo " ".$location_arr[$location_id]; 
								}
								else echo $supplier_arr[$party_id]; 
								?>
								</b></td></tr>
								<?
								foreach($location_data as $mst_id=>$mst_data)
								{
									foreach($mst_data as $prod_id=>$prod_data)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										//if($prod_data["booking_no"]!="") $booking_req_no=$prod_data["booking_no"]; else $booking_req_no=$prod_data["requisition_no"];
										$all_po_breakdown_id=explode(",",chop($prod_data["po_breakdown_id"],","));
										$buyer_id=$style_num=$order_num=$booking_req_no=$int_file_no="";$order_qnty=0;
										foreach($all_po_breakdown_id as $po_id)
										{
											$buyer_id=$po_arr[$po_id]['buyer_name'];
											if($style_check[$i][$po_arr[$po_id]['style']]=="")
											{
												$style_check[$i][$po_arr[$po_id]['style']]=$po_arr[$po_id]['style'];
												$style_num.=$po_arr[$po_id]['style'].",";
											}
											$order_num.=$po_arr[$po_id]['name'].",";
											$int_file_no.=$po_arr[$po_id]['int_file_no'].",";
											$order_qnty+=$po_arr[$po_id]['qnty'];
										}
										$issue_qty=$issue_rtn_qty=$issue_rtn_reject_qty=$grey_rcv_qty=0;$booking_no="";$grey_rcv_reject_qty=0;
										
										if($prod_data["entry_form"]==3)
										{
											//echo $mst_id."==>".$prod_id."==>".$prod_data["cons_quantity"]."<br>";
											$issue_qty=$prod_data["cons_quantity"];
											if($prod_data["basis"]==1)
											{
												$booking_req_no=$prod_data["booking_no"];
											} 
											else  
											{
												//echo $mst_id."==>".$prod_id."==>".$prod_data["cons_quantity"]."<br>";
												$booking_req_no=implode(",",array_unique(explode(",",chop($prod_data["requisition_no"],","))));
												$booking_req_arr=array_unique(explode(",",chop($prod_data["requisition_no"],",")));
												foreach ($booking_req_arr as $req_no) {
													$booking_no=$requisition_booking_arr[$req_no][$prod_id]['booking_no'];
												}
												//$booking_no=$requisition_booking_arr[$req_no][$prod_id]['booking_no'];
											}
										}
										else if($prod_data["entry_form"]==9)
										{
											$issue_rtn_qty=$prod_data["cons_quantity"];
											$issue_rtn_reject_qty=$prod_data["cons_reject_qnty"];
											
											//for issue return booking no and req no
											if($prod_data["basis"]==3)
											{
												$booking_req_no=$prod_data["booking_no"];
												$booking_no=$requisition_booking_arr[$booking_req_no][$prod_id]['booking_no'];
											}
										}
										else
										{
											if($prod_data["basis"]==10)
											{
												//$booking_no=$grey_booking_no[$roll_data_arr[$mst_id]]['booking_no'];
												$booking_no=$grey_booking_no[$prod_data["booking_no"]]['booking_no'];
											}
											else
											{
												$booking_no=$prod_data["booking_no"];
											}
											//$booking_no=$prod_data["booking_no"];
											$grey_rcv_qty=$prod_data["cons_quantity"];
											$grey_rcv_reject_qty=$prod_data["cons_reject_qnty"];
										}
										$balance +=($issue_qty-($issue_rtn_qty+$issue_rtn_reject_qty+$grey_rcv_qty+$grey_rcv_reject_qty));
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30" align="center" title="<? echo "Prog Num=".$prod_data["booking_no"]; ?>"><? echo $i; ?></td>
											<td width="80" align="center"><? echo change_date_format($prod_data["trans_date"]); ?></td>
											<td width="130" style="word-break:break-all"><? echo $prod_data["trans_ref_no"]; ?></td>
											<td width="80"><p>&nbsp;<? echo  $prod_data["challan_no"]; ?></p></td>
											<td width="100" align="center"><p>&nbsp;<? echo $booking_req_no; ?></p></td>
											<td width="120" title="<? echo $mst_id."==".$prod_data["basis"]; ?>"><p>&nbsp;<? echo $booking_no; ?></p></td>
											<td width="70"><p><? echo $buyer_arr[$buyer_id]; ?>&nbsp;</p></td>
											<td width="110" style="word-break:break-all"><? echo chop($style_num,","); ?></td>
											<td width="100" style="word-break:break-all"><? echo implode(",",array_unique(explode(",",chop($int_file_no,",")))); ?></td>
											<td width="100" style="word-break:break-all"><? echo $prod_data["job_no"] ; ?></td>
											<td width="130" style="word-break:break-all"><? echo chop($order_num,","); ?></td>
											<td width="90" align="right"><? echo number_format($order_qnty,0,'.',''); ?></td>
											<td width="80"><p>&nbsp;<? echo $brand_arr[$prod_data["brand_id"]]; ?></p></td>
											<td width="150" style="word-break:break-all"><? echo $product_arr[$prod_id]['name']; ?></td>
											<td width="80"><p><? echo $product_arr[$prod_id]['lot']; ?></p></td>
											<td width="60" align="center"><? echo $unit_of_measurement[$prod_data["cons_uom"]]; ?>&nbsp;</td>
											<td width="90" align="right"><? echo number_format($issue_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($grey_rcv_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($grey_rcv_reject_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($issue_rtn_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($issue_rtn_reject_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($balance,2); ?></td>
											<td style="word-break:break-all"><? echo $prod_data["remarks"]; ?></td>
										</tr>
										<?
										$i++;
										$party_issue_qty+=$issue_qty;
										$party_rec_qty+=$grey_rcv_qty;
										$party_fab_rej_qty+=$grey_rcv_reject_qty;
										$party_return_qty+=$issue_rtn_qty;
										$party_yarn_rej_qty+=$issue_rtn_reject_qty;
										//$party_balance+=$balance;
										
										$tot_issue_qty+=$issue_qty;
										$tot_rec_qty+=$grey_rcv_qty;
										$tot_fab_rej_qty+=$grey_rcv_reject_qty;
										$tot_return_qty+=$issue_rtn_qty;
										$tot_yarn_rej_qty+=$issue_rtn_reject_qty;
										
									}
								}
								
								?>
								<tr bgcolor="#CCCCCC" >
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td colspan="3" align="right"><strong>Party And Location Total:</strong></td> 
									<td align="right"><? echo number_format($party_issue_qty,2); ?></td>
									<td align="right"><? echo number_format($party_rec_qty,2); ?></td>
									<td align="right"><? echo number_format($party_fab_rej_qty,2); ?></td>
									<td align="right"><? echo number_format($party_return_qty,2); ?></td>
									<td align="right"><? echo number_format($party_yarn_rej_qty,2); ?></td>
									<td align="right"><? echo number_format($balance,2); ?></td>
									<td>&nbsp;</td>
								</tr>
								<?
								$tot_balance+=$balance;
								$party_issue_qty=$party_rec_qty=$party_fab_rej_qty=$party_return_qty=$party_yarn_rej_qty= $party_balance=0;$balance=0;	
							}
						}
					}
					?>
				</table>
			</div>
			<table width="2090" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
				<tr>
					<td width="30">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="130">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="120">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="130">&nbsp;</td>
					<td width="90">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="150">&nbsp;</td>
					<td width="80">&nbsp;</td>

					<td width="60"><strong>Grand Total</strong></td>
					<td width="90" align="right"><? echo number_format($tot_issue_qty,2); ?></td>

					<td width="90" align="right"><? echo number_format($tot_rec_qty,2); ?></td>
					<td width="90" align="right"><? echo number_format($tot_fab_rej_qty,2); ?></td>

					<td width="90" align="right"><? echo number_format($tot_return_qty,2); ?></td>
					<td width="90" align="right"><? echo number_format($tot_yarn_rej_qty,2); ?></td>
					<td width="90" align="right"><? echo number_format($tot_balance,2); ?></td>
					<td width="">&nbsp;</td>
				</tr>
			</table>
		</div>
		<?
	}
	else if($type==8) // Sample Without Order Button Start
	{
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		$txt_job_no=str_replace("'","",$txt_job_no);
		$txt_booking_no=str_replace("'","",$txt_booking_no);
		$txt_internal_ref=str_replace("'","",$txt_internal_ref);
		$txt_date_from=str_replace("'","",$txt_date_from);
		$txt_date_to=str_replace("'","",$txt_date_to);
		$txt_job_id=str_replace("'","",$txt_job_id);
		$hide_booking_id=str_replace("'","",$hide_booking_id);
		$hide_booking_type=str_replace("'","",$hide_booking_type);
		$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
		$txt_knitting_com_id=str_replace("'","",$txt_knitting_com_id);
		$cbo_year=str_replace("'","",$cbo_year);

		if($cbo_knitting_source) $source_cond = " and a.knitting_source=".$cbo_knitting_source;
		if($txt_knitting_com_id!="") $source_cond = " and a.knitting_company in (".$txt_knitting_com_id.")";

		if($cbo_knitting_source) $source_cond2 = " and a.knit_dye_source=".$cbo_knitting_source;
		if($txt_knitting_com_id!="") $source_cond2 = " and a.knit_dye_company in (".$txt_knitting_com_id.")";
		
		$issue_wise_location=return_library_array( "select id, location_id from inv_issue_master where status_active=1 and is_deleted=0 and entry_form=3 and issue_basis in(1,3) and issue_purpose in(1,4,8) and company_id=$cbo_company_name", "id", "location_id");
		
		$all_data_arr=array(); $all_po_check=array();$all_po_id="";
		if($txt_job_no =="" && $txt_booking_no =="" && $txt_internal_ref =="" && $txt_date_from !="" && $txt_date_to !="")
		{
			
			$issue_sql="SELECT a.id as mst_id, a.entry_form, a.issue_number, a.challan_no, a.issue_basis, a.issue_purpose, a.booking_id, a.booking_no, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, b.id as trans_id, b.prod_id, b.transaction_date, b.cons_uom, b.requisition_no, b.brand_id, b.cons_quantity,b.remarks, 0 as po_breakdown_id
			from inv_issue_master a, inv_transaction b
			where a.id=b.mst_id and a.entry_form=3 and a.issue_purpose=8 and b.transaction_type=2 and a.status_active=1 and b.status_active=1 and a.knit_dye_company>0 and a.issue_basis in(1,3) and a.company_id=$cbo_company_name and b.transaction_date between '$txt_date_from' and '$txt_date_to' $source_cond2";
			
			//echo $issue_sql;die;
			
			$issue_result=sql_select($issue_sql);
			
			if(count($issue_result)<1)
			{
				echo '<span style="font-weight: bold; font-size:20px;">No Data Found</span>';die;
			}
			
			foreach($issue_result as $row)
			{
				if($trans_check[$row[csf("mst_id")]][$row[csf("trans_id")]]=="")
				{
					$trans_check[$row[csf("mst_id")]][$row[csf("trans_id")]]=$row[csf("trans_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_date"]=$row[csf("issue_date")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("issue_number")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("issue_basis")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_purpose"]=$row[csf("issue_purpose")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("transaction_date")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["requisition_no"].=$row[csf("requisition_no")].",";
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
				}
				if($issue_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=="" && $row[csf("po_breakdown_id")]>0)
				{
					$issue_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=$row[csf("po_breakdown_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["po_breakdown_id"].=$row[csf("po_breakdown_id")].",";
				}
				
				if($all_po_check[$row[csf("po_breakdown_id")]]=="" && $row[csf("po_breakdown_id")]>0)
				{
					$all_po_check[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
					$all_po_id.=$row[csf("po_breakdown_id")].",";
				}
				$all_booking_id[$row[csf("booking_id")]]=$row[csf("booking_id")];
			}
			// echo "<pre>";print_r($all_booking_id);
			unset($issue_result);
			
			
			$receive_sql="SELECT a.id as mst_id, a.recv_number, a.booking_id, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, b.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type,b.remarks, 0 as po_breakdown_id , a.issue_id
			from inv_receive_master a, inv_transaction b 
			where a.id=b.mst_id and b.item_category in(1,13) and a.entry_form in(9,2,22,58) and b.transaction_type in(1) and a.receive_basis in(1,2,3,9,10,11) and b.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and b.transaction_date between '$txt_date_from' and '$txt_date_to' $source_cond
			union
			SELECT a.id as mst_id, a.recv_number, a.booking_id, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, b.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type,b.remarks, 0 as po_breakdown_id , a.issue_id
			from inv_receive_master a, inv_transaction b 
			where a.id=b.mst_id and b.item_category in(1,13) and a.entry_form in(9,2,22,58) and b.transaction_type in(4) and a.receive_basis in(1,2,3,9,10,11) and a.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and b.transaction_date between '$txt_date_from' and '$txt_date_to' $source_cond
			";
			//echo $receive_sql;//die;
			$receive_result=sql_select($receive_sql);
			$booking_data_arr = array();
			foreach($receive_result as $row)
			{
				if($trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=="")
				{
					$location_id="";
					if($row[csf('entry_form')]==9)
					{
						$location_id=$issue_wise_location[$row[csf('issue_id')]];
					}
					else
					{
						$location_id=$row[csf('location_id')];
					}
					if($booking_duplicate_chk[$row[csf("booking_no")]]=='')
					{
						$booking_duplicate_chk[$row[csf("booking_no")]]=$row[csf("booking_no")];
						array_push($booking_data_arr,$row[csf("booking_no")]);
					}
				

					$trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=$row[csf("trans_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("receive_date")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("recv_number")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("receive_basis")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_id"]=$row[csf("booking_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_without_order"]=$row[csf("booking_without_order")];
					
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["pi_wo_batch_no"]=$row[csf("pi_wo_batch_no")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];
					
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_reject_qnty"]+=$row[csf("cons_reject_qnty")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
				}
				if($rcv_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=="" && $row[csf("po_breakdown_id")]>0)
				{
					$rcv_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=$row[csf("po_breakdown_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["po_breakdown_id"].=$row[csf("po_breakdown_id")].",";
				}
				
				if($all_po_check[$row[csf("po_breakdown_id")]]=="" && $row[csf("po_breakdown_id")]>0)
				{
					$all_po_check[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
					$all_po_id.=$row[csf("po_breakdown_id")].",";
				}
			}

			
			unset($receive_result);
			
			$all_po_id=chop($all_po_id,",");
			if($all_po_id!="")
			{
				$all_po_id=array_unique(explode(",",$all_po_id));
				if($db_type==2 && count($all_po_id)>999)
				{
					$po_cond=" and (";
					$poIdsArr=array_chunk($all_po_id,999);
					foreach($poIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$po_cond.=" b.id in($ids) or"; 
					}
					$po_cond=chop($po_cond,'or ');
					$po_cond.=")";
				}
				else
				{
					$poIds=implode(",",$all_po_id);
					$po_cond=" and b.id in($poIds)";
				}
				$order_sql="select a.job_no, a.style_ref_no, a.buyer_name, b.id, b.po_number, b.po_quantity, b.grouping as int_file_no from wo_po_break_down b, wo_po_details_master a where b.job_no_mst=a.job_no $po_cond";
				//echo $order_sql;die;
				$datapoArray=sql_select($order_sql);
				foreach($datapoArray as $row)
				{
					$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
					$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
					$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
					$po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
					$po_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
					$po_arr[$row[csf('id')]]['int_file_no']=$row[csf('int_file_no')];
				}
				
				unset($datapoArray);
				
				$sql_requ= "select a.dtls_id, a.booking_no, b.requisition_no,b.prod_id from ppl_planning_entry_plan_dtls a, ppl_yarn_requisition_entry b where b.knit_id=a.dtls_id and  a.status_active=1 and a.is_deleted=0";
				$result_requ=sql_select($sql_requ);
				$requisition_booking_arr=array();
				$grey_booking_no=array();
				foreach($result_requ as $row)
				{
					$requisition_booking_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['booking_no']=$row[csf('booking_no')];
					$grey_booking_no[$row[csf('dtls_id')]]['booking_no']=$row[csf('booking_no')];
				}


				

			}

			$barcode_arr_sql = "SELECT a.id, a.booking_id,a.booking_no, c.barcode_no
			FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($booking_data_arr,1,'a.booking_no')."";
			//echo $data_arr_sql;
			$barcode_arr_sql_rslt = sql_select($barcode_arr_sql);
			$barcode_no_arr=array();
			$barcode_info_arr=array();
			foreach($barcode_arr_sql_rslt as $row)
			{
				if($booking_barcode_chk[$row[csf("barcode_no")]]=='')
				{
					$booking_barcode_chk[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
					array_push($barcode_no_arr,$row[csf("barcode_no")]);

					$barcode_info_arr[$row[csf('booking_no')]]['barcode_no'] .=$row[csf('barcode_no')].',';
				}
			}
			unset($barcode_arr_sql_rslt);
			//var_dump($barcode_info_arr);


			$booking_data_sql = "SELECT a.id, a.booking_no,  c.barcode_no
			FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 ".where_con_using_array($barcode_no_arr,0,'c.barcode_no')." order by c.barcode_no desc";
			//echo $booking_data_sql;
			$booking_data_arr_rslt = sql_select($booking_data_sql);
			$booking_no_info_arr = array();
			foreach($booking_data_arr_rslt as $row)
			{
				$booking_no_info_arr[$row[csf('barcode_no')]]['booking_no'] .=$row[csf('booking_no')].',';
			}
			unset($booking_data_arr_rslt);

		}
		else
		{
			if($txt_booking_no !="" && $hide_booking_type==2)
			{				
				if($txt_date_from!="" && $txt_date_to!="")
				{
					$date_condtion=" and b.transaction_date between '$txt_date_from' and '$txt_date_to'";
				}
				$rcv_book_sql="SELECT b.mst_id as recv_book_id from PRO_GREY_PROD_DELIVERY_DTLS b where b.entry_form=56 and b.order_id in($hide_booking_id) and b.status_active=1 and b.is_deleted=0 group by b.mst_id";
				$rcv_book_data=sql_select($rcv_book_sql);
				$all_smn_rcv_booking_id="";
				foreach($rcv_book_data as $row)
				{
					$all_smn_rcv_booking_id.=$row[csf('recv_book_id')].",";
				}
				$all_smn_rcv_booking_id=chop($all_smn_rcv_booking_id,",");
				if($all_smn_rcv_booking_id!="")
				{
					$all_smn_rcv_booking_id=array_unique(explode(",",$all_smn_rcv_booking_id));
					if($db_type==2 && count($all_smn_rcv_booking_id)>999)
					{
						$rcv_smn_booking_cond=" and (";
						$poIdsArr=array_chunk($all_smn_rcv_booking_id,999);
						foreach($poIdsArr as $ids)
						{
							$ids=implode(",",$ids);
							$rcv_smn_booking_cond.=" a.booking_id in($ids) or"; 
						}
						$rcv_smn_booking_cond=chop($rcv_smn_booking_cond,'or ');
						$rcv_smn_booking_cond.=")";
					}
					else
					{
						$rcv_booking_ids=implode(",",$all_smn_rcv_booking_id);
						$rcv_smn_booking_cond=" and a.booking_id in($rcv_booking_ids)";
					}
				}
				// echo $rcv_smn_booking_cond;die;
				
				$issue_sql="SELECT a.id as mst_id, a.entry_form, a.issue_number, a.challan_no, a.issue_basis, a.issue_purpose, a.booking_id, a.booking_no, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, b.id as trans_id, b.prod_id, b.transaction_date,b.cons_uom, b.requisition_no, b.brand_id, b.cons_quantity,b.remarks, 0 as po_breakdown_id
				from inv_issue_master a, inv_transaction b
				where a.id=b.mst_id and a.entry_form=3 and a.issue_purpose=8 and b.transaction_type=2 and a.status_active=1 and b.status_active=1 and a.knit_dye_company>0 and a.issue_basis in(1,3) and a.company_id=$cbo_company_name and a.booking_id in($hide_booking_id) $date_condtion $source_cond2 ";
				
				//echo $issue_sql;die;
				
				$issue_result=sql_select($issue_sql);
				
				
				if(count($issue_result)<1)
				{
					echo '<span style="font-weight: bold; font-size:20px;">No Data Found</span>';die;
				}
				
				foreach($issue_result as $row)
				{
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("transaction_date")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("issue_number")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("issue_basis")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_purpose"]=$row[csf("issue_purpose")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["requisition_no"].=$row[csf("requisition_no")].",";
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
					$all_booking_id[$row[csf("booking_id")]]=$row[csf("booking_id")];
				}
				
				unset($issue_result);
				
				$receive_sql="SELECT a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, a.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type,b.remarks, 0 as po_breakdown_id, a.issue_id
				from inv_receive_master a, inv_transaction b 
				where a.id=b.mst_id and b.item_category in(1,13) and a.entry_form in(9,2,22,58) and b.transaction_type in(4) and a.receive_basis in(1,2,3,9,10,11) and a.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.booking_id in($hide_booking_id) $date_condtion $source_cond
				union
				SELECT a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, a.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type,b.remarks, 0 as po_breakdown_id, a.issue_id
				from inv_receive_master a, inv_transaction b 
				where a.id=b.mst_id and b.item_category in(1,13) and a.entry_form in(9,2,22,58) and b.transaction_type in(1) and a.receive_basis in(1,2,3,9,10,11) and b.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name $rcv_smn_booking_cond $date_condtion $source_cond";
				// echo $receive_sql;die;
				$receive_result=sql_select($receive_sql); // [$row[csf('location_id')]]
				$booking_data_arr =array();
				foreach($receive_result as $row)
				{
					$location_id="";
					if($row[csf('entry_form')]==9)
					{
						$location_id=$issue_wise_location[$row[csf('issue_id')]];
					}
					else
					{
						$location_id=$row[csf('location_id')];
					}

					if($booking_duplicate_chk[$row[csf("booking_no")]]=='')
					{
						$booking_duplicate_chk[$row[csf("booking_no")]]=$row[csf("booking_no")];
						array_push($booking_data_arr,$row[csf("booking_no")]);
					}
					
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("receive_date")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("recv_number")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];

					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("receive_basis")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_id"]=$row[csf("booking_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_without_order"]=$row[csf("booking_without_order")];
					
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["pi_wo_batch_no"]=$row[csf("pi_wo_batch_no")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];
					
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_reject_qnty"]+=$row[csf("cons_reject_qnty")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
				}
				
				unset($receive_result);

				$barcode_arr_sql = "SELECT a.id, a.booking_id,a.booking_no, c.barcode_no
			FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($booking_data_arr,1,'a.booking_no')."";
			//echo $data_arr_sql;
			$barcode_arr_sql_rslt = sql_select($barcode_arr_sql);
			$barcode_no_arr=array();
			$barcode_info_arr=array();
			foreach($barcode_arr_sql_rslt as $row)
			{
				if($booking_barcode_chk[$row[csf("barcode_no")]]=='')
				{
					$booking_barcode_chk[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
					array_push($barcode_no_arr,$row[csf("barcode_no")]);

					$barcode_info_arr[$row[csf('booking_no')]]['barcode_no'] .=$row[csf('barcode_no')].',';
				}
			}
			unset($barcode_arr_sql_rslt);
			//var_dump($barcode_info_arr);


			$booking_data_sql = "SELECT a.id, a.booking_no,  c.barcode_no
			FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 ".where_con_using_array($barcode_no_arr,0,'c.barcode_no')." order by c.barcode_no desc";
			//echo $booking_data_sql;
			$booking_data_arr_rslt = sql_select($booking_data_sql);
			$booking_no_info_arr = array();
			foreach($booking_data_arr_rslt as $row)
			{
				$booking_no_info_arr[$row[csf('barcode_no')]]['booking_no'] .=$row[csf('booking_no')].',';
			}
			unset($booking_data_arr_rslt);
			}
			else
			{
				if($txt_internal_ref !="")
				{
					if($txt_date_from!="" && $txt_date_to!="")
					{
						$date_condtion=" and b.transaction_date between '$txt_date_from' and '$txt_date_to'";
					}

					$all_smn_booking_id="";
					$smn_int_ref_sql="SELECT a.id as booking_id, a.booking_no, c.buyer_name, c.style_ref_no, c.internal_ref
					from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, sample_development_mst c
					where a.booking_no=b.booking_no and b.style_id=c.id and a.entry_form_id=140 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.internal_ref ='$txt_internal_ref'
					group by a.id, a.booking_no, c.buyer_name, c.style_ref_no, c.internal_ref";
					// echo $sample_req_sql;die;
					$int_ref_sql_result=sql_select($smn_int_ref_sql);
					foreach($int_ref_sql_result as $row)
					{
						$all_smn_booking_id.=$row[csf('booking_id')].",";
					}
					unset($int_ref_sql_result);

					$all_smn_booking_id=chop($all_smn_booking_id,",");
					if($all_smn_booking_id!="")
					{
						$all_smn_booking_id=array_unique(explode(",",$all_smn_booking_id));
						if($db_type==2 && count($all_smn_booking_id)>999)
						{
							$smn_booking_cond=" and (";
							$poIdsArr=array_chunk($all_smn_booking_id,999);
							foreach($poIdsArr as $ids)
							{
								$ids=implode(",",$ids);
								$smn_booking_cond.=" a.booking_id in($ids) or"; 
							}
							$smn_booking_cond=chop($smn_booking_cond,'or ');
							$smn_booking_cond.=")";
						}
						else
						{
							$booking_ids=implode(",",$all_smn_booking_id);
							$smn_booking_cond=" and a.booking_id in($booking_ids)";
							$smn_booking_cond2=" and b.order_id in($booking_ids)";
						}
						$rcv_book_sql="SELECT b.mst_id as recv_book_id from PRO_GREY_PROD_DELIVERY_DTLS b where b.entry_form=56 $smn_booking_cond2 and b.status_active=1 and b.is_deleted=0 group by b.mst_id";
						$rcv_book_data=sql_select($rcv_book_sql);
						$all_smn_rcv_booking_id="";
						foreach($rcv_book_data as $row)
						{
							$all_smn_rcv_booking_id.=$row[csf('recv_book_id')].",";
						}
						$all_smn_rcv_booking_id=chop($all_smn_rcv_booking_id,",");
						if($all_smn_rcv_booking_id!="")
						{
							$all_smn_rcv_booking_id=array_unique(explode(",",$all_smn_rcv_booking_id));
							if($db_type==2 && count($all_smn_rcv_booking_id)>999)
							{
								$rcv_smn_booking_cond=" and (";
								$poIdsArr=array_chunk($all_smn_rcv_booking_id,999);
								foreach($poIdsArr as $ids)
								{
									$ids=implode(",",$ids);
									$rcv_smn_booking_cond.=" a.booking_id in($ids) or"; 
								}
								$rcv_smn_booking_cond=chop($rcv_smn_booking_cond,'or ');
								$rcv_smn_booking_cond.=")";
							}
							else
							{
								$rcv_booking_ids=implode(",",$all_smn_rcv_booking_id);
								$rcv_smn_booking_cond=" and a.booking_id in($rcv_booking_ids)";
							}
						}
						// echo $rcv_smn_booking_cond;die;
						$issue_sql="SELECT a.id as mst_id, a.entry_form, a.issue_number, a.challan_no, a.issue_basis, a.issue_purpose, a.booking_id, a.booking_no, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, b.id as trans_id, b.prod_id, b.transaction_date,b.remarks, b.cons_uom, b.requisition_no, b.brand_id, b.cons_quantity, 0 as po_breakdown_id
						from inv_issue_master a, inv_transaction b
						where a.id=b.mst_id and a.entry_form=3 and a.issue_purpose=8 and b.transaction_type=2 and a.status_active=1 and b.status_active=1 and a.knit_dye_company>0 and a.issue_basis in(1,3) and a.company_id=$cbo_company_name $smn_booking_cond $date_condtion $source_cond2 ";
						// echo $issue_sql;
						$issue_result=sql_select($issue_sql);
						if(count($issue_result)<1)
						{
							echo '<span style="font-weight: bold; font-size:20px;">No Data Found</span>';die;
						}
						
						foreach($issue_result as $row)
						{
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("transaction_date")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("issue_number")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("issue_basis")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_purpose"]=$row[csf("issue_purpose")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["requisition_no"].=$row[csf("requisition_no")].",";
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('location_id')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
							$all_booking_id[$row[csf("booking_id")]]=$row[csf("booking_id")];
						}						
						unset($issue_result);

						$receive_sql="SELECT a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, a.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type,b.remarks, 0 as po_breakdown_id, a.issue_id
						from inv_receive_master a, inv_transaction b 
						where a.id=b.mst_id and b.item_category in(1,13) and a.entry_form in(9,2,22,58) and b.transaction_type in(4) and a.receive_basis in(1,2,3,9,10,11) and a.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name  $smn_booking_cond $date_condtion $source_cond
						union
						SELECT a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, a.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type,b.remarks, 0 as po_breakdown_id, a.issue_id
						from inv_receive_master a, inv_transaction b 
						where a.id=b.mst_id and b.item_category in(1,13) and a.entry_form in(9,2,22,58) and b.transaction_type in(1) and a.receive_basis in(1,2,3,9,10,11) and b.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name  $rcv_smn_booking_cond $date_condtion $source_cond";
						//echo $receive_sql;die;
						$receive_result=sql_select($receive_sql);						
						foreach($receive_result as $row)
						{
							$location_id="";
							if($row[csf('entry_form')]==9)
							{
								$location_id=$issue_wise_location[$row[csf('issue_id')]];
							}
							else
							{
								$location_id=$row[csf('location_id')];
							}
							
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("receive_date")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("recv_number")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];

							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("receive_basis")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_id"]=$row[csf("booking_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_without_order"]=$row[csf("booking_without_order")];
							
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["pi_wo_batch_no"]=$row[csf("pi_wo_batch_no")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];
							
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_reject_qnty"]+=$row[csf("cons_reject_qnty")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$location_id][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
						}						
						unset($receive_result);
					}
				}
				
			}
		}
		//echo "<pre>";print_r($all_data_arr);die;
		if(!empty($all_booking_id)) // Non-order sample Buyer, Style and Internal Ref cond
		{
			$all_booking_ids = implode(",", $all_booking_id);
			if($db_type==2 && count($all_booking_id)>999)
			{
				$all_booking_id_chunk=array_chunk($all_booking_id,999) ;
				$all_booking_ids_cond = " and (";

				foreach($all_booking_id_chunk as $chunk_arr)
				{
					$all_booking_ids_cond.=" a.id in(".implode(",",$chunk_arr).") or ";
				}

				$all_booking_ids_cond = chop($all_booking_ids_cond,"or ");
				$all_booking_ids_cond .=")";
			}
			else
			{
				$all_booking_ids_cond=" and a.id in($all_booking_ids)";
			}

			$sample_req_sql="SELECT a.booking_no, c.buyer_name, c.style_ref_no, c.internal_ref
			from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, sample_development_mst c
			where a.booking_no=b.booking_no and b.style_id=c.id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $all_booking_ids_cond
			group by a.booking_no, c.buyer_name, c.style_ref_no, c.internal_ref";
			// and a.booking_no='MF-SMN-20-00066'//and a.entry_form_id=140
			$sample_req_result=sql_select($sample_req_sql);
			$buyer_style_ref_arr = array();
			foreach ($sample_req_result as $key => $row) 
			{
				$buyer_style_ref_arr[$row[csf('booking_no')]]['smn_buyer_name']=$row[csf('buyer_name')];
				$buyer_style_ref_arr[$row[csf('booking_no')]]['smn_style_ref_no']=$row[csf('style_ref_no')];
				$buyer_style_ref_arr[$row[csf('booking_no')]]['smn_internal_ref']=$row[csf('internal_ref')];
			}
		}
		

		$product_arr=array();
		$sql_prod="select id, product_name_details, lot from product_details_master where item_category_id in (1) ";//13
		$sql_prod_res=sql_select($sql_prod);
		foreach($sql_prod_res as $rowp)
		{
			$product_arr[$rowp[csf('id')]]['name']=$rowp[csf('product_name_details')];
			$product_arr[$rowp[csf('id')]]['lot']=$rowp[csf('lot')];
		}
		unset($sql_prod_res);

		$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
		$buyer_arr=return_library_array( "select id, short_name from  lib_buyer", "id", "short_name");
		$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
		$location_arr=return_library_array("select id, location_name from lib_location","id","location_name");
		?>
		<div>
			<table width="1750" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
				<tr>
					<td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr> 
				<tr>  
					<td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $report_title; ?> (Party Wise)</strong></td>
				</tr>  
				<tr> 
					<td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? //echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
				</tr>
			</table>
			<table width="1750" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="80">Date</th>
					<th width="130">Transaction Ref.</th>
					<th width="80">Challan No</th>
					<th width="100">Booking</th>
					<th width="70">Buyer</th>
					<th width="110">Style Ref.</th>
					<th width="100">Internal Ref.</th>
					<th width="80">Brand</th>
					<th width="150">Item Description</th>
					<th width="80">Lot</th>
					<th width="60">UOM</th>
					<th width="90">Yarn Issued</th>
					<th width="90">Fabric Received</th>
					<th width="90">Reject Fabric Received</th>
					<th width="90">Yarn Returned</th>
					<th width="90">Reject Yarn Returned</th>
					<th width="90">Balance</th> 
					<th width="">Remarks</th> 
				</thead>
			</table>
			<div style="width:1750px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="1730" cellpadding="2" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
					<?					
					$i=1;
					foreach ($all_data_arr as $source_id=>$source_data )
					{
						foreach ($source_data as $party_id=>$party_data )
						{
							foreach ($party_data as $location_id=>$location_data )
							{
								?>
								<tr bgcolor="#EFEFEF"><td colspan="21" title="<? echo $location_id.jahid; ?>"><b>Party name: 
								<? 
								if($source_id==1)
								{
									echo $company_arr[$party_id]; echo " ".$location_arr[$location_id]; 
								}
								else echo $supplier_arr[$party_id]; 
								?>
								</b></td></tr>
								<?
								foreach($location_data as $mst_id=>$mst_data)
								{
									foreach($mst_data as $prod_id=>$prod_data)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										$booking_req_no="";
										$issue_qty=$issue_rtn_qty=$issue_rtn_reject_qty=$grey_rcv_qty=0;$booking_no="";$grey_rcv_reject_qty=0;
										if($prod_data["entry_form"]==3)
										{
											$issue_qty=$prod_data["cons_quantity"];
											if($prod_data["basis"]==1)
											{
												$booking_req_no=$prod_data["booking_no"];
											} 
											else  
											{
												$booking_req_no=implode(",",array_unique(explode(",",chop($prod_data["requisition_no"],","))));

												$booking_req_arr=array_unique(explode(",",chop($prod_data["requisition_no"],",")));
												foreach ($booking_req_arr as $req_no) {
													$booking_no=$requisition_booking_arr[$req_no][$prod_id]['booking_no'];
												}
											}
										}
										else if($prod_data["entry_form"]==9)
										{
											$issue_rtn_qty=$prod_data["cons_quantity"];
											$issue_rtn_reject_qty=$prod_data["cons_reject_qnty"];
											$booking_req_no=$prod_data["booking_no"];
										}
										else
										{
											if($prod_data["basis"]==10)
											{
												$booking_no=$grey_booking_no[$prod_data["booking_no"]]['booking_no'];
												$grey_rcv_reject_qty=$prod_data["cons_reject_qnty"];

												$barcode_info_arr=array_unique(explode(",",chop($barcode_info_arr[$prod_data["booking_no"]]['barcode_no'],",")));

												foreach($barcode_info_arr as $barcode)
												{
													$booking_info_no .= $booking_no_info_arr[$barcode]['booking_no'];
												}
												$booking_req_no=implode(",",array_unique(explode(",",chop($booking_info_no,","))));
												
											}
											else
											{
												$booking_no=$prod_data["booking_no"];
											}
											$grey_rcv_qty=$prod_data["cons_quantity"];
											
										}
										$balance +=($issue_qty-($issue_rtn_qty+$issue_rtn_reject_qty+$grey_rcv_qty+$grey_rcv_reject_qty));
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30" align="center" title="<? echo "Prog Num=".$prod_data["booking_no"]; ?>"><? echo $i; ?></td>
											<td width="80" align="center"><? echo change_date_format($prod_data["trans_date"]); ?></td>
											<td width="130" style="word-break:break-all"><? echo $prod_data["trans_ref_no"]; ?></td>
											<td width="80"><p>&nbsp;<? echo  $prod_data["challan_no"]; ?></p></td>
											<td title="<? echo $mst_id."==".$prod_data["basis"]; ?>" width="100" align="center"><p>&nbsp;<? echo $booking_req_no; //$booking_no?></p></td>
											<td width="70" title="<? echo 'Buyer id:'.$buyer_id; ?>"><p><? echo $buyer_arr[$buyer_style_ref_arr[$booking_req_no]['smn_buyer_name']]; //$buyer_arr[$buyer_id]; ?>&nbsp;</p></td>
											<td width="110" style="word-break:break-all"><? 
											echo $buyer_style_ref_arr[$booking_req_no]['smn_style_ref_no']; //chop($style_num,","); ?></td>
											<td width="100" style="word-break:break-all"><? 
											echo $buyer_style_ref_arr[$booking_req_no]['smn_internal_ref'];
											//implode(",",array_unique(explode(",",chop($int_file_no,",")))); ?></td>
											<td width="80"><p>&nbsp;<? echo $brand_arr[$prod_data["brand_id"]]; ?></p></td>
											<td width="150" style="word-break:break-all"><? echo $product_arr[$prod_id]['name']; ?></td>
											<td width="80"><p><? echo $product_arr[$prod_id]['lot']; ?></p></td>
											<td width="60" align="center"><? echo $unit_of_measurement[$prod_data["cons_uom"]]; ?>&nbsp;</td>
											<td width="90" align="right"><? echo number_format($issue_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($grey_rcv_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($grey_rcv_reject_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($issue_rtn_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($issue_rtn_reject_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($balance,2); ?></td>
											<td width="" style="word-break:break-all"><? echo $prod_data["remarks"]; ?></td>
										</tr>
										<?
										$i++;
										$party_issue_qty+=$issue_qty;
										$party_rec_qty+=$grey_rcv_qty;
										$party_fab_rej_qty+=$grey_rcv_reject_qty;
										$party_return_qty+=$issue_rtn_qty;
										$party_yarn_rej_qty+=$issue_rtn_reject_qty;
										//$party_balance+=$balance;
										
										$tot_issue_qty+=$issue_qty;
										$tot_rec_qty+=$grey_rcv_qty;
										$tot_fab_rej_qty+=$grey_rcv_reject_qty;
										$tot_return_qty+=$issue_rtn_qty;
										$tot_yarn_rej_qty+=$issue_rtn_reject_qty;
										
									}
								}
								
								?>
								<tr bgcolor="#CCCCCC" >
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td colspan="3" align="right"><strong>Party And Location Total:</strong></td> 
									<td align="right"><? echo number_format($party_issue_qty,2); ?></td>
									<td align="right"><? echo number_format($party_rec_qty,2); ?></td>
									<td align="right"><? echo number_format($party_fab_rej_qty,2); ?></td>
									<td align="right"><? echo number_format($party_return_qty,2); ?></td>
									<td align="right"><? echo number_format($party_yarn_rej_qty,2); ?></td>
									<td align="right"><? echo number_format($balance,2); ?></td>
									<td>&nbsp;</td>
								</tr>
								<?
								$tot_balance+=$balance;
								$party_issue_qty=$party_rec_qty=$party_fab_rej_qty=$party_return_qty=$party_yarn_rej_qty= $party_balance=0;$balance=0;	
							}
						}
					}
					?>
				</table>
			</div>
			<table width="1750" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
				<tr>
					<td width="30">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="130">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="150">&nbsp;</td>
					<td width="80">&nbsp;</td>

					<td width="60"><strong>Grand Total</strong></td>
					<td width="90" align="right"><? echo number_format($tot_issue_qty,2); ?></td>

					<td width="90" align="right"><? echo number_format($tot_rec_qty,2); ?></td>
					<td width="90" align="right"><? echo number_format($tot_fab_rej_qty,2); ?></td>

					<td width="90" align="right"><? echo number_format($tot_return_qty,2); ?></td>
					<td width="90" align="right"><? echo number_format($tot_yarn_rej_qty,2); ?></td>
					<td width="90" align="right"><? echo number_format($tot_balance,2); ?></td>
					<td width="">&nbsp;</td>
				</tr>
			</table>
		</div>
		<?
	} // Sample Without Order Button end
	else
	{
		$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
		$buyer_arr=return_library_array( "select id, short_name from  lib_buyer", "id", "short_name");
		$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

		$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
		$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
		$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
		if($type!=7)
		{
			$booking_buyer= return_library_array( "select a.booking_no, a.buyer_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "booking_no", "buyer_id");
		}
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
		//$knitting_company=str_replace("'","",$txt_knitting_com_id);
		
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$date_cond=" and b.transaction_date between $txt_date_from and $txt_date_to";
			$date_cond2="and a.receive_date between $txt_date_from and $txt_date_to";
		}
		//echo $date_cond;
		
		$booking_no=str_replace("'","",$txt_booking_no);
		$hide_booking_id=str_replace("'","",$hide_booking_id);
		$booking_type=str_replace("'","",$hide_booking_type);
		$hide_recv_id=str_replace("'","",$hide_recv_id);
	
		$txt_fso_no=str_replace("'","",$txt_fso_no);
		$hide_fso_id=str_replace("'","",$hide_fso_id);
		$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
	
		$txt_knitting_com_id=str_replace("'","",$txt_knitting_com_id);

		//var_dump($txt_knitting_com_id);die;
		//if($cbo_knitting_source>0) $source_cond = " and a.knitting_source=".$cbo_knitting_source;
		$source_cond="";
		$source_cond2="";
		if($cbo_knitting_source) $source_cond .= " and a.knitting_source=".$cbo_knitting_source;
		if($txt_knitting_com_id!="") $source_cond .= " and a.knitting_company in (".$txt_knitting_com_id.")";

		if($cbo_knitting_source) $source_cond2 .= " and a.knit_dye_source=".$cbo_knitting_source;
		if($txt_knitting_com_id!="") $source_cond2 .= " and a.knit_dye_company in (".$txt_knitting_com_id.")";
		
		$type=str_replace("'","",$type);

		if ($type==6 || $type==7) 
		{
			$booking="";
			if(str_replace("'","",$txt_job_id) !="" || str_replace("'","",$txt_internal_ref) !="")
			{
				$booking_no="";
				$txt_job_ids=str_replace("'","",$txt_job_id);
				if (str_replace("'","",$txt_internal_ref)=="") $intrl_ref_cnd=""; else $intrl_ref_cnd=" and d.grouping=$txt_internal_ref";
				if ($txt_job_ids=="") 
				{
					$txt_job_no_cnd=""; 
				}
				else 
				{
					$txt_job_no_cnd=" and c.id in ($txt_job_ids)";
				}
	
				$jobNo_arr=sql_select("select e.booking_no from wo_po_details_master c, wo_po_break_down d, wo_booking_mst e where c.job_no=d.job_no_mst and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.job_no=e.job_no and e.status_active=1 and e.is_deleted=0  $intrl_ref_cnd $txt_job_no_cnd");
				foreach ($jobNo_arr as $value) 
				{
					$booking_no .= ($booking_no=="")? "'".$value[csf("booking_no")]."'" : ",'".$value[csf("booking_no")]."'";
					$booking .= ($booking=="")? " and a.sales_booking_no in ('".$value[csf("booking_no")]."'" : ",'".$value[csf("booking_no")]."'";
				}
				$booking.=")";
			}
	
			if($txt_fso_no!="")
			{
				$sales_orders="";
				foreach (explode(",", $txt_fso_no) as $row) 
				{
					$sales_orders.= ($sales_orders=="") ? "'".$row."'" : ",'".$row."'";
				}
	
				$sql_sales_order= "SELECT a.sales_booking_no from  fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and job_no in ($sales_orders) $booking group by a.sales_booking_no";

				$result_data=sql_select($sql_sales_order);
				foreach ($result_data as $value) 
				{
					$sales_ord_wise_booking_arr[$value[csf("sales_booking_no")]]=$value[csf("sales_booking_no")];
				}	
				$booking_no=implode(",", $sales_ord_wise_booking_arr);
			}
			else
			{
				$booking_no=$booking_no;
			}	
			//echo $booking_no; die;
		}
		
		$hide_booking_id=implode(",",array_unique(explode(",",$hide_booking_id)));
		$booking_nos=array_unique(explode(",",$booking_no));
		if($booking_no!="")
		{
			$booking_nos_con='';
			foreach($booking_nos as $bno)
			{
				if($booking_nos_con=='') $booking_nos_con="'".$bno."'"; else $booking_nos_con.=','."'".$bno."'";
			}
		}
		//echo $booking_no.'kjj';die;
			
		if (str_replace("'","",$txt_internal_ref)=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping=$txt_internal_ref";
	
		if($type !=6 && $type !=7) //without sales order wise type
		{
			$po_arr=array();
			$datapoArray=sql_select("select a.style_ref_no,b.id, b.po_number, b.po_quantity from wo_po_break_down b,wo_po_details_master a where b.job_no_mst=a.job_no $internal_ref_cond");
	
			$po_ids="";
			foreach($datapoArray as $row)
			{
				$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
				$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
				$po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
				
				if($po_ids=='') $po_ids=$row[csf('id')]; else $po_ids.=','.$row[csf('id')];
			}	
			unset($datapoArray);
			$poIds=chop($po_ids,',');
			$po_cond_for_in="";
			$po_ids=count(array_unique(explode(",",$po_ids)));
			if($db_type==2 && $po_ids>1000)
			{
				$po_cond_for_in=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$po_cond_for_in.=" a.po_id in($ids) or"; 
				}
				$po_cond_for_in=chop($po_cond_for_in,'or ');
				$po_cond_for_in.=")";
			}
			else
			{
				$poIds=implode(",",array_unique(explode(",",$po_ids)));
				$po_cond_for_in=" and a.po_id in($poIds)";
			}
			//echo $po_cond_for_in;	
	
			//$booking_con2 //FK-Fb-18-00055--dtls_id = 5555
			$sql_plan= "select a.dtls_id,a.booking_no from  ppl_planning_entry_plan_dtls a where  a.status_active=1 and a.is_deleted=0 $po_cond_for_in ";
			$result_plan=sql_select($sql_plan);
			$prog_id='';
			foreach($result_plan as $row)
			{
				//echo $row[csf('booking_no')];
				
				$plan_booking_arr[$row[csf('dtls_id')]]['booking_no']=$row[csf('booking_no')];
				if($prog_id=='') $prog_id=$row[csf('dtls_id')]; else $prog_id.=','.$row[csf('dtls_id')];
			}
		}
		
		if($booking_nos_con!='')
		{
			if($booking_type==1)
			{
				 $booking_con2="and a.booking_no in($booking_nos_con)";
			}
			else
			{
				 $booking_con2="and a.booking_no in($booking_nos_con)";
			}
		}
			
		$is_sales="";
		$sales_ord_arr=array();
		if($type==6 || $type==7)
		{ 
			$is_sales= " and a.is_sales=1"; 
			$all_booking_cond=$all_booking_cond2="";
	
			if ($booking_nos_con)
				{ 
					$all_booking_cond= " and a.sales_booking_no in ($booking_nos_con)";
					$all_booking_cond2= " and booking_no in ($booking_nos_con)";
				}
			$sql_sales_order= "SELECT a.job_no as sales_order_no , a.sales_booking_no, a.style_ref_no, a.within_group from  fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $all_booking_cond group by a.job_no , a.sales_booking_no, a.style_ref_no, a.within_group";
			$result_data=sql_select($sql_sales_order);
	
			foreach ($result_data as $value) 
			{
				$sales_ord_arr[$value[csf("sales_booking_no")]]["sales_order_no"]=$value[csf("sales_order_no")];
				$sales_ord_arr[$value[csf("sales_booking_no")]]["style_ref_no"]=$value[csf("style_ref_no")];
				$sales_ord_arr[$value[csf("sales_booking_no")]]["within_group"]=$value[csf("within_group")];
				$sales_order_booking[$value[csf("sales_booking_no")]]=$value[csf("sales_booking_no")];
			}
	
			$sql_booking_type="SELECT booking_no, booking_type, is_short from wo_booking_mst where status_active=1 and is_deleted=0 $all_booking_cond2 union all select booking_no, booking_type, is_short from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0";
			$sql_booking_type_data=sql_select($sql_booking_type);
			foreach ($sql_booking_type_data as $value) 
			{
				if($value[csf("booking_type")]==1 && $value[csf("is_short")]==2)
				{
					$booking_type_arr[$value[csf("booking_no")]]="Main";
				}
				else if($value[csf("booking_type")]==1 && $value[csf("is_short")]==1)
				{
					$booking_type_arr[$value[csf("booking_no")]]="Short";
				}
				else if($value[csf("booking_type")]==4)
				{
					$booking_type_arr[$value[csf("booking_no")]]="Sample";
				}
			}
		}	
		
		$sql_requ= "select a.dtls_id,a.booking_no,b.requisition_no from  ppl_planning_entry_plan_dtls a,ppl_yarn_requisition_entry b where  b.knit_id=a.dtls_id and  a.status_active=1 and a.is_deleted=0 $booking_con2 $is_sales";
		
		$result_requ=sql_select($sql_requ);
		$requisition_nos='';
		foreach($result_requ as $row)
		{
			//echo $row[csf('booking_no')];
			$requisition_numArr[$row[csf('booking_no')]]['requisition_no'].=$row[csf('requisition_no')].',';
			$requisition_numArr2[$row[csf('requisition_no')]]['booking_no']=$row[csf('booking_no')];
			$requisition_numArr2[$row[csf('requisition_no')]]['dtls_id']=$row[csf('dtls_id')];
			$requisition_numArr3[$row[csf('dtls_id')]]['booking_no']=$row[csf('booking_no')];
			$requisition_numArr4[$row[csf('dtls_id')]]['requisition_no']=$row[csf('requisition_no')];
			if($requisition_nos=='') $requisition_nos=$row[csf('requisition_no')]; else $requisition_nos.=','.$row[csf('requisition_no')];
		}
			
	
		//and a.booking_id in($prog_id)
		$sqlRecv = "select a.recv_number,a.booking_id,a.booking_no,a.receive_basis from inv_receive_master a where  a.receive_basis in(2,9,1) and a.entry_form in(2,22) ";
		$result_sqlRecv=sql_select($sqlRecv);
		foreach($result_sqlRecv as $row)
		{
			//echo $row[csf('booking_no')];
			if($row[csf('receive_basis')]==1) //Booking
			{
				$prod_recv_arr[$row[csf('recv_number')]]['booking_no']=$row[csf('booking_no')];
			}
			if($row[csf('receive_basis')]==2) //Plan
			{
				$prod_recv_arr[$row[csf('recv_number')]]['booking_no']=$plan_booking_arr[$row[csf('booking_id')]]['booking_no'];		
			}		
		}
		
		if($booking_no!="")
		{
			//echo $hide_booking_id; // 10045
			$booking_con="and a.booking_id in($hide_booking_id)";
			//echo $requisition_nos.'PPPooooooo';
						
			if ($db_type == 0) $grp_conct="group_concat(distinct(a.dtls_id)) as dtls_id";
			else $grp_conct="LISTAGG(a.dtls_id, ',') WITHIN GROUP (ORDER BY a.dtls_id) as dtls_id";
			if ($db_type == 0) $grp_conct_rec="group_concat(distinct(id)) as recv_id";
			else $grp_conct_rec="LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as recv_id";
			if ($db_type == 0) $grp_conct_greyrec="group_concat(distinct(a.booking_id)) as booking_id";
			else $grp_conct_greyrec="LISTAGG(a.booking_id, ',') WITHIN GROUP (ORDER BY a.booking_id) as booking_id";
			if ($db_type == 0) $grp_conct_greyrec2="group_concat(distinct(a.id)) as rec_id";
			else $grp_conct_greyrec2="LISTAGG(a.id, ',') WITHIN GROUP (ORDER BY a.id) as rec_id";
	
			if ($db_type == 0) $grp_conct_challan="group_concat(mst_id) as challan_id";
			else $grp_conct_challan="LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as challan_id";
			
			$booking_id = return_field_value("booking_id as booking_id", "inv_issue_master", "booking_id in(".$hide_booking_id.")  and issue_purpose in(1,4,8) and issue_basis in(1,3) and entry_form=3 and item_category=1","booking_id");//Yarn Issue
			//echo $booking_id;						
		
			$recv_booking_id = return_field_value("$grp_conct_greyrec", "inv_receive_master a ", " a.booking_id in(".$hide_booking_id.") and a.entry_form in(22,9,2) and a.receive_basis in(1,3,2,9)","booking_id"); //Yarn Issue Ret/Knitting Prod/Recv
	
	
			// 5555,5579,5579,5582 
			$recvplan_id = return_field_value("$grp_conct", "ppl_planning_entry_plan_dtls a", "a.booking_no in($booking_nos_con)","dtls_id");

			$recv_booking_plan_id = return_field_value("$grp_conct_greyrec", "inv_receive_master a ", " a.booking_id in(".$recvplan_id.") and a.entry_form in(2) and a.receive_basis in(3,2)","booking_id");
			 
			  //echo $sql = "select a.id from inv_receive_master a where  a.booking_id in(".$recvplan_id.") and a.entry_form in(2) and a.receive_basis in(1,3,2,9)";
			 // 25273,25276,25425 
			 $recv_booking_id2 = return_field_value("$grp_conct_greyrec2", "inv_receive_master a ", " a.booking_id in(".$recvplan_id.") and a.entry_form in(2) and a.receive_basis in(1,3,2,9)","rec_id");
			 $fabric_store_auto_update=return_field_value("auto_update","variable_settings_production","company_name =$cbo_company_name and variable_list=15 and item_category_id=13 and is_deleted=0 and status_active=1");
			 if($fabric_store_auto_update == 2){
				//$get_delivery_challan = sql_select("select mst_id from PRO_GREY_PROD_DELIVERY_DTLS where grey_sys_id in($recv_booking_id2)");
				$get_delivery_challan = return_field_value("$grp_conct_challan", "PRO_GREY_PROD_DELIVERY_DTLS", " grey_sys_id in(".$recv_booking_id2.")","challan_id");
				$get_delivery_challan = implode(",",array_unique(explode(",",$get_delivery_challan)));
			 }else{
				$get_delivery_challan="";
			 }
	
			
			 
			 $recv_booking_id3 = return_field_value("$grp_conct_greyrec2", "inv_receive_master a ", " a.booking_id in(".$recv_booking_id2.") and a.entry_form in(22) and a.receive_basis in(1,3,2,9)","rec_id");	
			//
			//echo $recvplan_id.'dd'; 
	
			$booking_ids=implode(",",array_unique(explode(",",$booking_id)));
			$requisition_ids=implode(",",array_unique(explode(",",$requisition_nos)));
			$recv_booking_id=implode(",",array_unique(explode(",",$recv_booking_id)));
			$recv_booking_id2=implode(",",array_unique(explode(",",$recv_booking_id2)));
			
			$recv_booking_plan_id=implode(",",array_unique(explode(",",$recv_booking_plan_id)));

			$delivery_challan=implode(",",array_unique(explode(",",$get_delivery_challan)));
			
			if($recv_booking_id=="") $recv_booking_id=0;else $recv_booking_id=$recv_booking_id;
			if($recv_booking_id2=="") $recv_booking_id2=0;else $recv_booking_id2=$recv_booking_id2;
			if($recv_booking_plan_id=="") $recv_booking_plan_id=0;else $recv_booking_plan_id=$recv_booking_plan_id;
			if($delivery_challan=="") $delivery_challan=0;else $delivery_challan=$delivery_challan;
			if($requisition_ids=="") $requisition_ids=0;else $requisition_ids=$requisition_ids;
			if($booking_ids=="") $booking_ids=0;else $booking_ids=$booking_ids;
			
			$booking_cond_issue="and (a.booking_id in($booking_ids) OR  b.requisition_no in($requisition_ids))";	//Issue
	
			$all_booking_rec_ids=$recv_booking_id.','.$requisition_ids.','.$recv_booking_plan_id.','.$recv_booking_id2.",".$delivery_challan;
			$booking_cond_recv="and a.booking_id in($all_booking_rec_ids)";
			
			
			if($recv_booking_id2!='' || $recv_booking_id2!=0)
			{
				if($delivery_challan!="")
				{
					$recv_booking_id2 .= ",".$delivery_challan;
				}
	
				$booking_cond_recv2="and a.booking_id in($recv_booking_id2) ";
			}
			else
			{
				$booking_cond_recv2="";	
			}
	
			if($recv_booking_id3!='' || $recv_booking_id3!=0)
			{
				$recv_booking_id3_recv="and a.id in($recv_booking_id3) ";
			}
			else
			{
				$recv_booking_id3_recv="";	
			}
		}
		
		$txt_internal_ref=str_replace("'","",$txt_internal_ref);
		$txt_job_no=str_replace("'","",$txt_job_no);
		//$txt_challan=str_replace("'","",$txt_challan);
		
		if($type==61) //Sales Order Wise Old
		{
			if($db_type==0) $grpby_field="group by trans_id";
			if($db_type==2) $grpby_field="group by trans_id,entry_form,trans_type";
			else $grpby_field="";
			?>
			<div>
				<table width="2100" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
					<tr>
					   <td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
					</tr> 
					<tr>  
					   <td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $report_title; ?> (Sales Order Wise)</strong></td>
					</tr>  
				</table>
				<table width="2200" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<thead>
						<th width="40">SL</th>
						<th width="130">Fabric Sales Order</th>
						<th width="125">Fabric Booking No</th>
						<th width="80">Booking Type</th>
						<th width="80">Style Ref</th>
						<th width="80">Date</th>
						<th width="120">Transaction Ref.</th>
						<th width="80">Challan No.</th>
						<th width="80">Requ No.</th>
						<th width="140">Buyer</th>
						<th width="90">Brand.</th>
						<th width="200">Item Description</th>
						<th width="80">Lot</th>
						<th width="80">UOM</th>
						<th width="60">Yarn Issued</th>
						<th width="100">Yarn Returnable qty</th>
					   
						<th width="100">Fabric Received</th>
						<th width="100">Reject Fabric Received</th>
	
						<th width="100">DY/TW/ WX/RCon Rec.</th>
	
						<th width="100">Yarn Returned</th>
						<th width="100">Reject Yarn Returned</th>
						<th width="">Balance</th> 
					   
					</thead>
				</table>
				<div style="width:2200px; overflow-y: scroll; max-height:380px;" id="scroll_body">
					<table width="2183" cellpadding="2" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
					<?
						if($recvplan_id)
						{
							$planning_cond=" and a.booking_id in ($recvplan_id)";
						}
	
						$product_arr=array();
						$sql_prod="select id, product_name_details, lot, brand from product_details_master where item_category_id in (1,2,22,13) ";
						$sql_prod_res=sql_select($sql_prod);
						foreach($sql_prod_res as $rowp)
						{
							$product_arr[$rowp[csf('id')]]['name']=$rowp[csf('product_name_details')];
							$product_arr[$rowp[csf('id')]]['lot']=$rowp[csf('lot')];
							$product_arr[$rowp[csf('id')]]['brand']=$rowp[csf('brand')];
						}
						unset($sql_prod_res);
						 $rec_qty_arr=array();
						 $sql_rec="SELECT a.id, a.recv_number, a.recv_number_prefix_num, a.buyer_id, a.booking_no,a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no,  a.knitting_source, a.knitting_company, a.receive_basis, a.entry_form, b.id as trans_id, b.prod_id, b.pi_wo_batch_no,b.cons_uom,  b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type from inv_receive_master a, inv_transaction b $propotionate_tbl where a.item_category in(1,13) and a.entry_form in(9,22) and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category in(1,13) and b.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.receive_basis in(3,4,9) $date_cond $where_cond  $booking_cond_recv 
							union
							 SELECT a.id, a.recv_number, a.recv_number_prefix_num, a.buyer_id, a.booking_no,a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no,  a.knitting_source, a.knitting_company, a.receive_basis, a.entry_form, b.id as trans_id, b.prod_id, b.pi_wo_batch_no,b.cons_uom,  b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type from inv_receive_master a, inv_transaction b $propotionate_tbl where a.item_category in(1) and a.entry_form=1 and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category in(1) and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.receive_basis in(4) and a.receive_purpose in (2,12,15,38) $date_cond $where_cond  $booking_cond_recv
							order by knitting_source, knitting_company, recv_number_prefix_num, receive_date"; 
						// echo $sql_rec; 
	
						$sql_rec_res=sql_select($sql_rec);
						foreach ($sql_rec_res as $rowRec ) //recv Basis 2=Independed not issue ret.
						{
							$trns_date=''; $date_frm=''; $date_to='';
							$trns_date=date('Y-m-d',strtotime($rowRec[csf('receive_date')]));
							$date_frm=date('Y-m-d',strtotime($from_date));
							$date_to=date('Y-m-d',strtotime($to_date));
	
								if(($rowRec[csf('entry_form')]==9) || ($rowRec[csf('entry_form')]==1))
								{
									$rec_qty_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]][$rowRec[csf('trans_id')]][$rowRec[csf('prod_id')]]['rej_yarn']+=$rowRec[csf('cons_reject_qnty')];
									$rec_qty_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]][$rowRec[csf('trans_id')]][$rowRec[csf('prod_id')]]['ret_yarn']+=$rowRec[csf('cons_quantity')];
									$yarnRec_qty_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]][$rowRec[csf('trans_id')]][$rowRec[csf('prod_id')]]['yRec']+=$rowRec[csf('cons_quantity')];
									
									$yarnRec_dtls_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]][$rowRec[csf('trans_id')]][$rowRec[csf('prod_id')]]['entry_form']=$rowRec[csf('entry_form')];
	
									$booking_nos=$requisition_numArr2[$rowRec[csf('booking_no')]]['booking_no'];	
								}
								else
								{
									$rec_qty_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]][$rowRec[csf('trans_id')]][$rowRec[csf('prod_id')]]['rej_fab']+=$rowRec[csf('cons_reject_qnty')];
									$rec_qty_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]][$rowRec[csf('trans_id')]][$rowRec[csf('prod_id')]]['fRec']+=$rowRec[csf('cons_quantity')];
									$receive_basis=$rowRec[csf('receive_basis')];
									/*if($receive_basis==2) //Booking
									{
									*/
										$booking_nos=$rowRec[csf('booking_no')];
									/*
									}
									else
									{
										$booking_nos=$prod_recv_arr[$rowRec[csf('booking_no')]]['booking_no'];		
									}*/
								}
	
								if ($sales_order_booking[$booking_nos] !='') 
								{
									$buyer=$booking_buyer[$booking_nos];
									$all_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]]['rec'].=$rowRec[csf('recv_number')].'**'.$rowRec[csf('recv_number_prefix_num')].'**'.$buyer.'**'.$rowRec[csf('booking_no')].'**'.$rowRec[csf('receive_date')].'**'.$rowRec[csf('challan_no')].'**'.$rowRec[csf('receive_basis')].'**'.$rowRec[csf('knitting_source')].'**'.$rowRec[csf('trans_id')].'**'.$rowRec[csf('prod_id')].'**'.$rowRec[csf('cons_uom')].'**'.$rowRec[csf('brand_id')].'**'.$rowRec[csf('yarn_issue_challan_no')].'**'.$rowRec[csf('item_category')].'**'.$rowyRec[csf('pi_wo_batch_no')].'**'.$rowyRec[csf('pi_wo_batch_no')].'**'.$booking_nos.'___';
								}
								
							}
						//print_r($all_data_arr);
						unset($sql_rec_res);
	
						//========================================This is for roll wise receive===============================================
						$all_rec_barcode_cond="";
						if ($date_cond2) 
						{
							$roll_sql="SELECT a.id as trans_id, a.receive_date, a.knitting_source, a.knitting_company, c.barcode_no, c.qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $date_cond2 and a.company_id=$cbo_company_name and a.entry_form=58 and b.status_active=1 and b.is_deleted=0 and a.id=c.mst_id and c.status_active=1 and c.is_deleted=0 and c.entry_form=58 and b.id=c.dtls_id";
							$roll_rec=sql_select($roll_sql);
							foreach ($roll_rec as $data) 
							{
								$rec_barcode[$data[csf('barcode_no')]]=$data[csf('barcode_no')];
								$rec_barcode_data[$data[csf('barcode_no')]]["trans_id"]=$data[csf('trans_id')];
								$rec_barcode_data[$data[csf('barcode_no')]]["qnty"]=$data[csf('qnty')];
								$rec_barcode_data[$data[csf('barcode_no')]]["receive_date"]=$data[csf('receive_date')];
							}
	
							if($db_type==0)
							{
								$all_rec_barcode_cond=" and c.barcode_no in (".implode(",", $rec_barcode).")";
							}
							else
							{
								if(count($rec_barcode)>999)
								{
									$arr_barcode=array_chunk($rec_barcode, 999);
									$all_rec_barcode_cond=" and (";
									foreach ($arr_barcode as $value) 
									{
										$all_rec_barcode_cond .="c.barcode_no in (".implode(",", $value).") or ";
									}
									$all_rec_barcode_cond=chop($all_rec_barcode_cond,"or ");
									$all_rec_barcode_cond.=")";
								}
								else
	
								{
									$all_rec_barcode_cond=" and c.barcode_no in (".implode(",", $rec_barcode).")";
								}
							}
	
							$sql_rec="SELECT a.id, a.recv_number, a.recv_number_prefix_num, a.buyer_id, a.booking_no,a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no,  a.knitting_source, a.knitting_company, a.receive_basis, a.entry_form, b.id as trans_id, b.yarn_prod_id as prod_id, 0 as pi_wo_batch_no,b.uom as cons_uom,  b.brand_id, b.grey_receive_qnty as cons_quantity, 0 as return_qnty, b.reject_fabric_receive as cons_reject_qnty, 1 as transaction_type, a.roll_maintained, c.barcode_no,c.reject_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.item_category in(13) and a.entry_form=2 and a.company_id=$cbo_company_name and a.roll_maintained=1 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $planning_cond $all_rec_barcode_cond and c.mst_id=a.id and c.dtls_id=b.id and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 order by knitting_source, knitting_company, recv_number_prefix_num, receive_date";	
						}
						else
						{
							$barcode_no_arr=array();
							$sql_rec="SELECT distinct(c.barcode_no) as barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.item_category in(13) and a.entry_form=2 and a.company_id=$cbo_company_name and a.roll_maintained=1 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $planning_cond and c.mst_id=a.id and c.dtls_id=b.id and c.entry_form=2 and c.status_active=1 and c.is_deleted=0";
							$sql_rec_res=sql_select($sql_rec);
							foreach ($sql_rec_res as $rowRec )
							{
								$barcode_no_arr[$rowRec[csf("barcode_no")]]=$rowRec[csf("barcode_no")];
							}
	
							//making condition using barcode
							if($db_type !=0)
							{
								if(count($barcode_no_arr)>999)
								{
									$arr_barcode=array_chunk($barcode_no_arr, 999);
									$all_rec_barcode_cond=" and (";
									foreach ($arr_barcode as $value) 
									{
										$all_rec_barcode_cond .="c.barcode_no in (".implode(",", $value).") or ";
									}
									$all_rec_barcode_cond=chop($all_rec_barcode_cond,"or ");
									$all_rec_barcode_cond.=")";
								}
								else
								{
									$all_rec_barcode_cond=" and c.barcode_no in (".implode(",", $barcode_no_arr).")";
								}
							}
							else
							{
								$all_rec_barcode_cond=" and c.barcode_no in (".implode(",", $barcode_no_arr).")";
							}
	
							//Making Roll informations......
							$roll_sql="SELECT a.id as trans_id, a.receive_date, a.knitting_source, a.knitting_company, c.barcode_no, c.qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $date_cond2 and a.company_id=$cbo_company_name and a.entry_form=58 and b.status_active=1 and b.is_deleted=0 and a.id=c.mst_id and c.status_active=1 and c.is_deleted=0 and c.entry_form=58 and b.id=c.dtls_id $all_rec_barcode_cond";
							$roll_rec=sql_select($roll_sql);
							foreach ($roll_rec as $data) 
							{
								$rec_barcode_data[$data[csf('barcode_no')]]["trans_id"]=$data[csf('trans_id')];
								$rec_barcode_data[$data[csf('barcode_no')]]["qnty"]=$data[csf('qnty')];
								$rec_barcode_data[$data[csf('barcode_no')]]["receive_date"]=$data[csf('receive_date')];
							}
	
							$sql_rec="SELECT a.id, a.recv_number, a.recv_number_prefix_num, a.buyer_id, a.booking_no,a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no,  a.knitting_source, a.knitting_company, a.receive_basis, a.entry_form, b.id as trans_id, b.yarn_prod_id as prod_id, 0 as pi_wo_batch_no,b.uom as cons_uom,  b.brand_id, b.grey_receive_qnty as cons_quantity, 0 as return_qnty, b.reject_fabric_receive as cons_reject_qnty, 1 as transaction_type, a.roll_maintained, c.barcode_no,c.reject_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.item_category in(13) and a.entry_form=2 and a.company_id=$cbo_company_name and a.roll_maintained=1 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $planning_cond and c.mst_id=a.id and c.dtls_id=b.id and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 order by knitting_source, knitting_company, recv_number_prefix_num, receive_date";
						}
	
						$sql_rec_res=sql_select($sql_rec);
						foreach ($sql_rec_res as $rowRec )  //For roll wise receive
						{
							$reject=0;
							$barcodes="";
							$barcode_arr=array();
							$mst_id=$rowRec[csf('id')];
							$dtls_id=$rowRec[csf('trans_id')];
							$booking_nos=$requisition_numArr3[$rowRec[csf('booking_no')]]['booking_no'];
							$requisition_no=$requisition_numArr4[$rowRec[csf('booking_no')]]['requisition_no'];
	
							$barcode_rej[$rowRec[csf("barcode_no")]]["reject"]=$rowRec[csf("reject_qnty")];
	
							$trans_id=$rec_barcode_data[$rowRec[csf('barcode_no')]]["trans_id"];
							$rec_qty_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]][$trans_id][$rowRec[csf('prod_id')]]['rej_fab']+=$rowRec[csf("reject_qnty")];
	
							$rec_qty_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]][$trans_id][$rowRec[csf('prod_id')]]['fRec']+=$rec_barcode_data[$rowRec[csf('barcode_no')]]["qnty"];
	
							if ($sales_order_booking[$booking_nos]!='') 
							{
								$receive_date=$rec_barcode_data[$rowRec[csf('barcode_no')]]["receive_date"];
								$buyer=$booking_buyer[$booking_nos];
	
								$all_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]]['rec'].=$rowRec[csf('recv_number')].'**'.$rowRec[csf('recv_number_prefix_num')].'**'.$buyer.'**'.$rowRec[csf('booking_no')].'**'.$receive_date.'**'.$rowRec[csf('challan_no')].'**'.$rowRec[csf('receive_basis')].'**'.$rowRec[csf('knitting_source')].'**'.$trans_id.'**'.$rowRec[csf('prod_id')].'**'.$rowRec[csf('cons_uom')].'**'.$rowRec[csf('brand_id')].'**'.$rowRec[csf('yarn_issue_challan_no')].'**'.$rowRec[csf('item_category')].'**'.$rowyRec[csf('pi_wo_batch_no')].'**'.$rowyRec[csf('pi_wo_batch_no')].'**'.$booking_nos.'___';
							}
	
						}
						unset($sql_rec_res);
	
	
						if (str_replace("'","",$cbo_knitting_source)==0) $knit_source_cond_party=""; else $knit_source_cond_party=" and a.knit_dye_source=$cbo_knitting_source";
						if ($knitting_company=='') $knit_company_cond_party=""; else $knit_company_cond_party=" and a.knit_dye_company in ($knitting_company)";
	
						$iss_qty_arr=array(); 
	
						$sql_iss="SELECT a.issue_number, a.issue_number_prefix_num, a.buyer_id, a.booking_no, a.issue_date, a.challan_no, a.issue_basis, a.knit_dye_source, a.knit_dye_company, b.id as trans_id, b.prod_id, b.transaction_date, b.cons_uom, b.requisition_no, b.brand_id, b.cons_quantity, b.return_qnty from inv_issue_master a, inv_transaction b  $propotionate_tbl where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.issue_basis in(3,4) and a.issue_purpose in(1,4,8) $date_cond $where_cond $booking_cond_issue order by  a.knit_dye_source, a.knit_dye_company, a.issue_number_prefix_num, a.issue_date";
	
						//echo $sql_iss;die;
						$sql_iss_res=sql_select($sql_iss); $tot_opening_balIssue=$tot_opening_bal_return=0;
						foreach ($sql_iss_res as $rowIss )
						{
							$trns_date=''; $date_frm=''; $date_to='';
							$trns_date=date('Y-m-d',strtotime($rowIss[csf('issue_date')]));
							$date_frm=date('Y-m-d',strtotime($from_date));
							$date_to=date('Y-m-d',strtotime($to_date));
							$issue_basis=$rowIss[csf('issue_basis')];
	
							$booking_no=$requisition_numArr2[$rowIss[csf('requisition_no')]]['booking_no'];
						
							
							$iss_qty_arr[$rowIss[csf('knit_dye_source')]][$rowIss[csf('knit_dye_company')]][$rowIss[csf('trans_id')]][$rowIss[csf('prod_id')]]['issue']+=$rowIss[csf('cons_quantity')];
	
							$iss_qty_arr[$rowIss[csf('knit_dye_source')]][$rowIss[csf('knit_dye_company')]][$rowIss[csf('trans_id')]][$rowIss[csf('prod_id')]]['return']+=$rowIss[csf('return_qnty')];
	
							if ($sales_order_booking[$booking_no] !='') 
							{	
								$all_data_arr[$rowIss[csf('knit_dye_source')]][$rowIss[csf('knit_dye_company')]]['iss'].=$rowIss[csf('issue_number')].'**'.$rowIss[csf('issue_number_prefix_num')].'**'.$rowIss[csf('buyer_id')].'**'.$rowIss[csf('booking_no')].'**'.$rowIss[csf('issue_date')].'**'.$rowIss[csf('challan_no')].'**'.$rowIss[csf('issue_basis')].'**'.$rowIss[csf('knit_dye_source')].'**'.$rowIss[csf('trans_id')].'**'.$rowIss[csf('prod_id')].'**'.$rowIss[csf('cons_uom')].'**'.$rowIss[csf('requisition_no')].'**'.$rowIss[csf('brand_id')].'**'.$booking_no.'___';
							}
						}
						//die;
						//print_r($all_data_arr);
						
						unset($sql_iss_res); $i=1;
						foreach ($all_data_arr as $source_id=>$source_data )
						{
							foreach ($source_data as $party_id=>$party_data )
							{
							
								if($source_id==1) $knitting_party=$company_arr[$party_id]; 
								else if($source_id==3) $knitting_party=$supplier_arr[$party_id];
								else $knitting_party="&nbsp;";	
								 $balance=0; $return_balance=0;
								$party_issue_qty=$party_returnable_qty=$party_rec_qty=$party_fab_rej_qty=$party_return_qty=$party_yarn_rej_qty=$party_yarnRec_qty=0;
								?>
								<tr bgcolor="#EFEFEF"><td colspan="22"><b>Party name: <? echo $knitting_party; ?></b></td></tr>
								<?
							//Issue Data
							$ex_partyIssData='';
							$ex_partyIssData=array_filter(array_unique(explode('___',$party_data['iss']))); 
							foreach($ex_partyIssData as $dataIss)
							{
								$ex_data_iss='';
								$ex_data_iss=explode('**',$dataIss);
								
								$iss_num=''; $iss_no_pre=''; $buyer_id=''; $booking_no=''; $iss_date=''; $challan_no=''; $iss_basis=''; 
								$party_source=''; $trns_id=''; $prod_id=''; $cons_uom=''; $req_no=''; $brand_id='';  $issue_qty=0; 
	
								$iss_num=$ex_data_iss[0]; 
								$iss_no_pre=$ex_data_iss[1]; 
								$buyer_id=$ex_data_iss[2]; 
								$booking_no=$ex_data_iss[3]; 
								$iss_date=$ex_data_iss[4]; 
								$challan_no=$ex_data_iss[5]; 
								$iss_basis=$ex_data_iss[6]; 
								$party_source=$ex_data_iss[7]; 
								$trns_id=$ex_data_iss[8]; 
								$prod_id=$ex_data_iss[9]; 
								$cons_uom=$ex_data_iss[10];  
								$req_no=$ex_data_iss[11]; 
								$brand_id=$ex_data_iss[12];
								$booking_number=$ex_data_iss[13];
	
								$sales_order_no=$sales_ord_arr[$booking_number]["sales_order_no"];
								$sales_style_no=$sales_ord_arr[$booking_number]["style_ref_no"];
	
								$issue_qty=$iss_qty_arr[$source_id][$party_id][$trns_id][$prod_id]['issue'];
								$yarn_returnable_qty=$iss_qty_arr[$source_id][$party_id][$trns_id][$prod_id]['return'];
								
								$balance=($balance+$issue_qty);
								 
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="40"><? echo $i; ?></td>
									<td width="130" align="center"><? echo $sales_order_no; ?></td>
									<td width="125"><div style="word-wrap:break-word; width:125px"><? echo $booking_number; ?></div></td>
									<td width="80" align="center"><? echo $booking_type_arr[$booking_number]; ?></td>
									<td width="80"><p><? echo $sales_style_no; ?></p></td>
									<td width="80" align="center"><p>&nbsp;<? echo change_date_format($iss_date); ?></p></td>
									<td width="120"><p>&nbsp;<? echo $iss_num; ?></p></td>
									<td width="80" align="center"><p><? echo $challan_no; ?>&nbsp;</p></td>
									<td width="80" align="center"><? echo $req_no; ?></td>
									<td width="140"><div style="word-wrap:break-word; width:130px"><? echo $buyer_arr[$buyer_id]; ?></div></td>
									<td width="90" align="center" style="word-wrap:break-word;">
										<? echo $brand_arr[$product_arr[$prod_id]['brand']]; ?></td>
									<td width="200"><p>&nbsp;<? echo $product_arr[$prod_id]['name']; ?></p></td>
									<td width="80"><div style="word-wrap:break-word;"><? echo $product_arr[$prod_id]['lot']; ?></div></td>
									<td width="80"><p><? echo $unit_of_measurement[$cons_uom]; ?></p></td>
									<td width="60" align="right"><? echo number_format($issue_qty,2); ?>&nbsp;</td>
									<td width="100" align="right"><? echo number_format($yarn_returnable_qty,2); ?></td>
								 
									<td width="100" align="right">&nbsp;</td>
									<td width="100" align="right">&nbsp;</td>
	
									<td width="100" align="right">&nbsp;</td>
								   
									<td width="100" align="right">&nbsp;</td>
									<td width="100" align="right">&nbsp;</td>
									<td width="" align="right"><? echo number_format($balance,2); ?></td>
								   
								</tr>
								<?
								
								$party_issue_qty+=$issue_qty;
								$tot_issue_qty+=$issue_qty;
								$party_returnable_qty+=$yarn_returnable_qty;
								$tot_returnable_qty+=$yarn_returnable_qty;
								$i++;
							}
							// Receive Data
							$ex_partyRecData='';
							$ex_partyRecData=array_filter(array_unique(explode('___',$party_data['rec']))); 
							foreach($ex_partyRecData as $dataRec)
							{
								$ex_data_rec='';
								$ex_data_rec=explode('**',$dataRec);
								
								$rec_num=''; $rec_num_pre=''; $buyer_id=''; $booking_no=''; $rec_date=''; $challan_no=''; $rec_basis=''; 
								$party_source=''; $trns_id=''; $prod_id=''; $cons_uom=''; $yarn_iss_challlan=''; $brand_id=''; $item_category=''; 
								$receive_qty=0; $return_qty=0; $fab_reject_qty=0; $yarn_reject_qty=0; $yarnReceive_qty=0;
	
								$rec_num=$ex_data_rec[0]; 
								$rec_num_pre=$ex_data_rec[1]; 
								$buyer_id=$ex_data_rec[2]; 
								$booking_no=$ex_data_rec[3]; 
								$rec_date=$ex_data_rec[4]; 
								$challan_no=$ex_data_rec[5]; 
								$rec_basis=$ex_data_rec[6]; 
								$party_source=$ex_data_rec[7]; 
								$trns_id=$ex_data_rec[8]; 
								$prod_id=$ex_data_rec[9]; 
								$cons_uom=$ex_data_rec[10];  
								$brand_id=$ex_data_rec[11];
								$yarn_iss_challlan=$ex_data_rec[12]; 
								$item_category=$ex_data_rec[13]; 
								$pi_wo_batch_no=$ex_data_rec[14];
								$bookingNo=$ex_data_rec[16];
	
								$sales_order_no=$sales_ord_arr[$bookingNo]["sales_order_no"];
								$sales_style_no=$sales_ord_arr[$bookingNo]["style_ref_no"];
								
								$receive_qty=$rec_qty_arr[$source_id][$party_id][$trns_id][$prod_id]['fRec'];
								$fab_reject_qty=$rec_qty_arr[$source_id][$party_id][$trns_id][$prod_id]['rej_fab'];
								$yarn_reject_qty=$rec_qty_arr[$source_id][$party_id][$trns_id][$prod_id]['rej_yarn'];
	
								$entry_form=$yarnRec_dtls_arr[$source_id][$party_id][$trns_id][$prod_id]['entry_form'];
								if($entry_form==1)
								{
									$yrn_qty=$yarnReceive_qty=$yarnRec_qty_arr[$source_id][$party_id][$trns_id][$prod_id]['yRec'];
								}
								else
								{
									$yrn_qty=$return_qty=$rec_qty_arr[$source_id][$party_id][$trns_id][$prod_id]['ret_yarn'];
								}
	
								$balance=$opening_balance+$balance-($receive_qty+$fab_reject_qty+$yarn_reject_qty+$yrn_qty);
								$opening_balance=0;
								$return_balance=$return_balance-($return_qty+$yarn_reject_qty); 
								 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								if($rec_basis==1)
									$booking_reqsn_no=$booking_no;
								else if($rec_basis==3)
								{
									//$booking_reqsn_no=$req_no;
									$booking_reqsn_no=$booking_no;
								}
								else
									$booking_reqsn_no="&nbsp;";	
								?>
								
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="40"><? echo $i; ?></td>
									<td width="130" align="center"><? echo $sales_order_no; ?></td>
									<td width="125"><div style="word-wrap:break-word; width:125px"><? echo $bookingNo; ?></div></td>
									<td width="80" align="center"><p><? echo $booking_type_arr[$bookingNo]; ?></p></td>
									<td width="80"><p><? echo $sales_style_no; ?></p></td>
									<td width="80" align="center"><p>&nbsp;<? echo change_date_format($rec_date); ?></p></td>
									<td width="120"><p>&nbsp;<? echo $rec_num; ?></p></td>
									<td width="80" align="center"><p><? echo $challan_no; ?>&nbsp;</p></td>
									<td width="80" align="center"><? echo $booking_reqsn_no; ?></td>
									<td width="140"><div style="word-wrap:break-word; width:130px"><? echo $buyer_arr[$buyer_id]; ?></div></td>
									<td width="90" align="center" style="word-wrap:break-word;"><? echo $brand_arr[$product_arr[$prod_id]['brand']]; ?></td>
									<td width="200"><p>&nbsp;<? echo $product_arr[$prod_id]['name']; ?></p></td>
									<td width="80"><div style="word-wrap:break-word;"><? echo $product_arr[$prod_id]['lot']; ?></div></td>
									<td width="80"><p><? echo $unit_of_measurement[$cons_uom]; ?></p></td>
									<td width="60" align="center">&nbsp;</td>
									<td width="100" align="right">&nbsp;</td>
								  
									<td width="100" align="right"><? echo number_format($receive_qty,2); ?></td>
									<td width="100" align="right"><? echo number_format($fab_reject_qty,2); ?></td>
	
									<td width="100" align="right"> <? if($entry_form==1){ echo  number_format($yarnReceive_qty,2);} ?></td>
								   
									<td width="100" align="right"> <? if($entry_form !=1){ echo number_format($return_qty,2); }  ?> </td>
	
									<td width="100" align="right"><? echo number_format($yarn_reject_qty,2); ?></td>
									<td width="" align="right"><? echo number_format($balance,2); ?></td>
								   
								</tr>
								<?
								$party_rec_qty+=$receive_qty;
								$tot_rec_qty+=$receive_qty;
								$party_fab_rej_qty+=$fab_reject_qty;
								$tot_fab_rej_qty+=$fab_reject_qty;
								$party_yarnRec_qty+=$yarnReceive_qty;
								$tot_yarnRec_qty+=$yarnReceive_qty;
								$party_return_qty+=$return_qty;
								$tot_return_qty+=$return_qty;
								$party_yarn_rej_qty+=$yarn_reject_qty;
								$tot_yarn_rej_qty+=$yarn_reject_qty;
								$i++;
							}
							
							?>
							<tr bgcolor="#CCCCCC" >
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
	
								<td><strong>Party Total</strong></td>
								<td align="right"><? echo number_format($party_issue_qty,2); ?></td> 
								<td align="right"><? echo number_format($party_returnable_qty,2); ?></td>
							   
								<td align="right"><? echo number_format($party_rec_qty,2); ?></td>
								<td align="right"><? echo number_format($party_fab_rej_qty,2); ?></td>
							  
								<td align="right"><? echo number_format($party_yarnRec_qty,2); ?></td>
								
								<td align="right"><? echo number_format($party_return_qty,2); ?></td>
								<td align="right"><? echo number_format($party_yarn_rej_qty,2); ?></td>
								<td align="right"><? echo number_format($balance,2); ?></td>
							   
							</tr>
							<?
							$tot_balance+=$balance;
							$tot_return_balance+=$return_balance;
						}
						}
						?>
					</table>
				</div>
				<table width="2200" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
					<tr>
						<td width="40">&nbsp;</td>
						<td width="130">&nbsp;</td>
						<td width="125">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="120">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="140">&nbsp;</td>
						<td width="90">&nbsp;</td>
						<td width="200">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="80"><strong>Grand Total</strong></td>
						<td width="60"><? echo number_format($tot_issue_qty,2); ?></td>
						<td width="100" align="right"><? echo number_format($tot_returnable_qty,2); ?></td>
					   
						<td width="100" align="right"><? echo number_format($tot_rec_qty,2); ?></td>
						<td width="100" align="right"><? echo number_format($tot_fab_rej_qty,2); ?></td>
	
						<td width="100" align="right"><? echo number_format($tot_yarnRec_qty,2); ?></td>
					 
						<td width="100" align="right"><? echo number_format($tot_return_qty,2); ?></td>
						<td width="100" align="right"><? echo number_format($tot_yarn_rej_qty,2); ?></td>
						<td width="" align="right"><? echo number_format($tot_balance,2); ?></td>
					   
					</tr>
				</table>
			</div>
			<?
		}
		if($type == 6)
		{
			$location_arr=return_library_array("select company_id, location_name from lib_location","company_id","location_name");
			?>
			<div>
				<table width="2200" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
					<tr>
					   <td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
					</tr> 
					<tr>  
					   <td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $report_title; ?> (Sales Order Wise)</strong></td>
					</tr>  
				</table>
				<table width="2340" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<thead>
						<th width="40">SL</th>
						<th width="130">Fabric Sales Order</th>
						<th width="125">Fabric Booking No</th>
						<th width="80">Booking Type</th>
						<th width="80">Style Ref</th>
						<th width="80">Date</th>
						<th width="120">Transaction Ref.</th>
						<th width="80">Challan No.</th>
						<th width="80">Requ No.</th>
						<th width="140">PO Buyer</th>
						<th width="140">Buyer/Unit</th>
						<th width="90">Brand.</th>
						<th width="200">Item Description</th>
						<th width="80">Lot</th>
						<th width="80">UOM</th>
						<th width="60">Yarn Issued</th>
						<th width="100">Yarn Returnable qty</th>
					   
						<th width="100">Fabric Received</th>
						<th width="100">Reject Fabric Received</th>
	
						<th width="100">DY/TW/ WX/RCon Rec.</th>
	
						<th width="100">Yarn Returned</th>
						<th width="100">Reject Yarn Returned</th>
						<th width="">Balance</th> 
					   
					</thead>
				</table>
				<div style="width:2358px; overflow-y: scroll; max-height:380px; float: left;" id="scroll_body">
					<table width="2340" cellpadding="2" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" align="left">
						<? 
						//=======reject qnty
	
						$barcodeRejectQnty = return_library_array("select reject_qnty, barcode_no
						from pro_roll_details where entry_form = 2 and status_active = 1", 'barcode_no', 'reject_qnty');
	
						$barcode_nos = sql_select("select  a.barcode_no,b.recv_number,c.job_no,b.knitting_company,b.knitting_source, b.receive_date,c.sales_booking_no
						from pro_roll_details a , inv_receive_master b, fabric_sales_order_mst c
						where a.mst_id = b.id and a.po_breakdown_id = c.id and a.entry_form = 58 and a.is_sales = 1 and b.company_id= $cbo_company_name
						and a.status_active = 1 ");
						$trns_date=''; $date_frm=''; $date_to='';
						$date_frm=date('Y-m-d',strtotime($from_date));
						$date_to=date('Y-m-d',strtotime($to_date));
						
						foreach ($barcode_nos as $barcode) 
						{
							$rejectQntyArr[$barcode[csf("recv_number")]][$barcode[csf("job_no")]]["RejectQnty"] += $barcodeRejectQnty[$barcode[csf("barcode_no")]];
	
							$trns_date=date('Y-m-d',strtotime($barcode[csf('receive_date')]));
							if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
							{
								if($trns_date<$date_frm)
								{
									$sales_booking_no = $barcode[csf('sales_booking_no')];
									
									if ($sales_order_booking[$sales_booking_no] !='') 
									{
										$party_wise_opening[$barcode[csf('knitting_source')]][$barcode[csf('knitting_company')]]['opening_reject'] += $barcodeRejectQnty[$barcode[csf("barcode_no")]];
									}
								}
							}	
						}
						//=======
	
						$buyer_from_sales= return_library_array("select a.job_no , (case when a.within_group = 2 then a.buyer_id when a.within_group = 1 and b.id is not null then b.buyer_id when a.within_group = 1 and c.id is not null then c.buyer_id end) buyer_id 
						from fabric_sales_order_mst a left join wo_booking_mst b on a.sales_booking_no = b.booking_no and b.status_active = 1 left join   wo_non_ord_samp_booking_mst c on a.sales_booking_no = c.booking_no 
						and c.status_active = 1 where a.status_active = 1 and a.company_id= $cbo_company_name",'job_no','buyer_id');
						//echo "======".$buyer_from_sales['D n C-FSOE-17-00001'];die;
						$product_arr=array();
						$sql_prod="select id, product_name_details, lot, brand from product_details_master where item_category_id in (1,2,22,13) ";
						$sql_prod_res=sql_select($sql_prod);
						foreach($sql_prod_res as $rowp)
						{
							$product_arr[$rowp[csf('id')]]['name']=$rowp[csf('product_name_details')];
							$product_arr[$rowp[csf('id')]]['lot']=$rowp[csf('lot')];
							$product_arr[$rowp[csf('id')]]['brand']=$rowp[csf('brand')];
						}
						unset($sql_prod_res);
						 $rec_qty_arr=array();
						 $sql_yarn="SELECT a.knit_dye_source as knitting_source, a.knit_dye_company as  knitting_company,d.sales_booking_no, d.job_no as sales_order,d.style_ref_no,d.buyer_id,d.within_group, a.issue_number as system_ref,a.challan_no, a.issue_basis as  basis, a.entry_form, b.requisition_no, b.prod_id, b.cons_uom, b.brand_id, sum(b.cons_quantity) as cons_quantity, sum(b.return_qnty) as return_qnty, sum(b.cons_reject_qnty) as cons_reject_qnty, b.transaction_type, a.issue_date as trans_date  
							 from inv_issue_master a, inv_transaction b, order_wise_pro_details c, fabric_sales_order_mst d
							 where  a.id=b.mst_id and b.id = c.trans_id and c.po_breakdown_id = d.id and b.item_category in(1) and b.transaction_type in(2)  
							 and a.entry_form in(3) and c.is_sales = 1 and b.status_active = 1 and a.issue_basis in (3,4) and a.company_id =$cbo_company_name $source_cond2 $date_cond
							  group by a.knit_dye_source , a.knit_dye_company ,d.sales_booking_no, d.job_no ,d.style_ref_no,d.buyer_id,d.within_group,  a.issue_number ,a.challan_no, a.issue_basis , a.entry_form, b.requisition_no, b.prod_id, b.cons_uom, b.brand_id,  b.transaction_type, a.issue_date 
							 union all
							  select a.knitting_source, a.knitting_company,d.sales_booking_no, d.job_no as sales_order,d.style_ref_no,d.buyer_id,d.within_group, a.recv_number as system_ref,a.challan_no,  a.receive_basis as  basis, a.entry_form, (case when a.receive_basis = 3 then a.booking_id end) as requisition_no, b.prod_id, b.cons_uom, b.brand_id, sum(b.cons_quantity) as cons_quantity, sum(b.return_qnty) as return_qnty, sum(b.cons_reject_qnty) as cons_reject_qnty, b.transaction_type,a.receive_date as trans_date
							 from inv_receive_master a, inv_transaction b, order_wise_pro_details c, fabric_sales_order_mst d
							 where  a.id=b.mst_id and b.id = c.trans_id and c.po_breakdown_id = d.id and b.item_category in(1) and b.transaction_type in(4)  
							 and a.entry_form in(9) and a.company_id=$cbo_company_name $source_cond $date_cond and c.is_sales = 1 and b.status_active = 1 and a.receive_basis in (3,4)
							  group by a.knitting_source, a.knitting_company,d.sales_booking_no, d.job_no ,d.style_ref_no,d.buyer_id,d.within_group, a.recv_number,a.challan_no, a.receive_basis , a.entry_form,  b.prod_id, b.cons_uom, b.brand_id,  b.transaction_type,a.receive_date, a.booking_id
							 order by sales_order,trans_date,entry_form"; 
						//echo $sql_yarn; die;
	
						$sql_yarn_res=sql_select($sql_yarn);
						foreach ($sql_yarn_res as $rowRec ) 
						{
							$sales_booking_no = $rowRec[csf('sales_booking_no')];
	
							if ($sales_order_booking[$sales_booking_no] !='') 
							{
								
								if($rowRec[csf('entry_form')]==3)
								{
									$return_reject_qnty = $rowRec[csf('return_qnty')];
								}else{
									$return_reject_qnty = $rowRec[csf('cons_reject_qnty')];
								}
	
								$all_sales_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]][$rowRec[csf('sales_order')]]['yarn'].=$rowRec[csf('system_ref')].'**'.$rowRec[csf('sales_booking_no')].'**'.$rowRec[csf('style_ref_no')].'**'.$rowRec[csf('trans_date')].'**'.$rowRec[csf('challan_no')].'**'.$rowRec[csf('basis')].'**'.$rowRec[csf('entry_form')].'**'.$rowRec[csf('transaction_type')].'**'.$rowRec[csf('prod_id')].'**'.$rowRec[csf('cons_uom')].'**'.$rowRec[csf('requisition_no')].'**'.$rowRec[csf('brand_id')].'**'.$rowRec[csf('cons_quantity')].'**'.$rowRec[csf('buyer_id')].'**'.$rowRec[csf('within_group')].'**'.$return_reject_qnty.'##';
							}
								
						}
	
						//========================Opening  ==================
	
						if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
						{
							 $sql_yarn_opening=" select a.knit_dye_source as knitting_source, a.knit_dye_company as knitting_company,d.sales_booking_no, d.job_no as sales_order, sum(b.cons_quantity) as cons_quantity, sum(b.return_qnty) as return_qnty, 
							 sum(b.cons_reject_qnty) as cons_reject_qnty ,a.entry_form
							 from inv_issue_master a, inv_transaction b, order_wise_pro_details c, fabric_sales_order_mst d 
							 where a.id=b.mst_id and b.id = c.trans_id and c.po_breakdown_id = d.id and b.item_category in(1) and b.transaction_type in(2) and a.entry_form in(3) and c.is_sales = 1 and b.status_active = 1 
							 and a.issue_basis in (3,4) and a.company_id =$cbo_company_name and b.transaction_date < '" . str_replace("'","",$txt_date_from) . "' 
							 group by a.knit_dye_source , a.knit_dye_company ,d.sales_booking_no, d.job_no ,a.entry_form
							 union all 
							 select a.knitting_source, a.knitting_company,d.sales_booking_no, d.job_no as sales_order,sum(b.cons_quantity) as cons_quantity, sum(b.return_qnty) as return_qnty, sum(b.cons_reject_qnty) as cons_reject_qnty ,a.entry_form
							 from inv_receive_master a, inv_transaction b, order_wise_pro_details c, fabric_sales_order_mst d 
							 where a.id=b.mst_id and b.id = c.trans_id and c.po_breakdown_id = d.id and b.item_category in(1) and b.transaction_type in(4) and a.entry_form in(9) and a.company_id=$cbo_company_name and b.transaction_date < '" . str_replace("'","",$txt_date_from) . "'
							 and c.is_sales = 1 and b.status_active = 1 and a.receive_basis in (3,4) 
							 group by a.knitting_source, a.knitting_company,d.sales_booking_no, d.job_no,a.entry_form
							 order by sales_order"; 
							
	
							$sql_yarn_opening_res=sql_select($sql_yarn_opening);
							foreach ($sql_yarn_opening_res as $row_open ) 
							{
								$sales_booking_no_opening = $row_open[csf('sales_booking_no')];
	
								if ($sales_order_booking[$sales_booking_no_opening] !='') 
								{
									if($row_open[csf('entry_form')] ==3)
									{
										$party_wise_opening[$row_open[csf('knitting_source')]][$row_open[csf('knitting_company')]]['opening'] += $row_open[csf('cons_quantity')];
									}else{
										$party_wise_opening[$row_open[csf('knitting_source')]][$row_open[csf('knitting_company')]]['opening'] -= $row_open[csf('cons_quantity')];
										$party_wise_opening[$row_open[csf('knitting_source')]][$row_open[csf('knitting_company')]]['opening'] -= $row_open[csf('cons_reject_qnty')];
									}
									
									//$party_wise_opening[$row_open[csf('knitting_source')]][$row_open[csf('knitting_company')]]['opening'] -= $row_open[csf('return_qnty')];
									
									
								}
									
							}
	
	
						$sql_grey_dyed_yarn_opening =  sql_select("select a.knitting_source, a.knitting_company, d.sales_booking_no,sum(b.cons_quantity) as cons_quantity,sum(b.cons_reject_qnty) as cons_reject_qnty
							from inv_receive_master a, inv_transaction b, order_wise_pro_details c, fabric_sales_order_mst d
							where a.id= b.mst_id and b.id= c.trans_id and c.po_breakdown_id = d.id and c.is_sales = 1 and  a.receive_basis in (2,4,10) and a.entry_form in (2,22,58) and c.trans_id > 0 and b.status_active = 1 and d.status_active =1 and a.status_active=1 and a.item_category in (13) and a.company_id=$cbo_company_name and b.transaction_date < '" . str_replace("'","",$txt_date_from) . "'
							group by a.knitting_source, a.knitting_company, d.sales_booking_no
							union all    
							select 3 as knitting_source, a.supplier_id as knitting_company,e.sales_booking_no,sum(b.cons_quantity) as cons_quantity,sum(b.cons_reject_qnty) as cons_reject_qnty
							from inv_receive_master a, inv_transaction b, wo_yarn_dyeing_mst c,wo_yarn_dyeing_dtls d, fabric_sales_order_mst e 
							where  a.id=b.mst_id and b.pi_wo_batch_no = d.id and c.id = d.mst_id and d.job_no = e.job_no and b.item_category =1 and b.transaction_type =1 and a.entry_form =1 and a.receive_basis = 2 and c.entry_form in (94,135) and a.receive_purpose in (2,12,15,38) and b.status_active = 1  and c.is_sales = 1 and a.company_id=$cbo_company_name and b.transaction_date < '" . str_replace("'","",$txt_date_from) . "'
							group by a.supplier_id,e.sales_booking_no");
	
						foreach ($sql_grey_dyed_yarn_opening as $row_open_2 ) 
						{
							$sales_booking_no_opening_2 = $row_open_2[csf('sales_booking_no')];
							if ($sales_order_booking[$sales_booking_no_opening_2] !='') 
							{
								$party_wise_opening[$row_open_2[csf('knitting_source')]][$row_open_2[csf('knitting_company')]]['opening'] -= $row_open_2[csf('cons_quantity')];
								$party_wise_opening[$row_open_2[csf('knitting_source')]][$row_open_2[csf('knitting_company')]]['opening'] -= $row_open_2[csf('cons_reject_qnty')];
							}
						}
					}
					//========================End Opening Receive ==================
					
	
					$sql_grey_dyed_yarn =  sql_select("SELECT a.knitting_source, a.knitting_company, d.sales_booking_no,a.recv_number as system_ref,d.style_ref_no, d.buyer_id,d.within_group, a.receive_date as trans_date,a.challan_no,b.prod_id, a.receive_basis as basis,b.transaction_type, d.job_no as sales_order,sum(b.cons_quantity) as cons_quantity,sum(b.cons_reject_qnty) as cons_reject_qnty, b.cons_uom, b.brand_id,a.item_category, a.entry_form
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c, fabric_sales_order_mst d
					where a.id= b.mst_id and b.id= c.trans_id and c.po_breakdown_id = d.id and c.is_sales = 1 and  a.receive_basis in (2,4,10) and a.entry_form in (2,22,58) and c.entry_form in (2,22,58) and c.trans_id > 0 and b.status_active = 1 and d.status_active =1 and a.status_active=1 and a.item_category in (13) and a.company_id=$cbo_company_name $source_cond $date_cond
					group by a.knitting_source, a.knitting_company, d.sales_booking_no,a.recv_number ,d.style_ref_no, d.buyer_id,d.within_group, a.receive_date, a.challan_no,b.prod_id, a.receive_basis,b.transaction_type, d.job_no , b.cons_uom, b.brand_id,a.item_category ,a.entry_form
					union all    
					select 3 as knitting_source, a.supplier_id as knitting_company,e.sales_booking_no, a.recv_number as system_ref,e.style_ref_no, e.buyer_id,e.within_group, a.receive_date as trans_date,a.challan_no,b.prod_id,a.receive_basis as  basis,b.transaction_type ,e.job_no as sales_order,sum(b.cons_quantity) as cons_quantity,sum(b.cons_reject_qnty) as cons_reject_qnty, b.cons_uom,b.brand_id,a.item_category ,a.entry_form
					from inv_receive_master a, inv_transaction b, wo_yarn_dyeing_mst c,wo_yarn_dyeing_dtls d, fabric_sales_order_mst e 
					where  a.id=b.mst_id and b.pi_wo_batch_no = c.id and c.id = d.mst_id and d.job_no = e.job_no and b.item_category =1 and b.transaction_type =1 and a.entry_form =1 and a.receive_basis = 2 and c.entry_form in (94,135) and a.receive_purpose in (2,12,15,38) and b.status_active = 1  and c.is_sales = 1 and a.company_id=$cbo_company_name $source_cond $date_cond  
					group by a.supplier_id,e.sales_booking_no, a.recv_number,e.style_ref_no, e.buyer_id,e.within_group, a.receive_date, a.challan_no,b.prod_id,a.receive_basis,b.transaction_type ,e.job_no, b.cons_uom,b.brand_id,a.item_category ,a.entry_form 
					order by sales_order,trans_date");
	
					
	
	
					$return_reject_qnty = 0;
					foreach ($sql_grey_dyed_yarn as $rowGrey) 
					{
						$sales_booking_no = $rowGrey[csf('sales_booking_no')];
						if ($sales_order_booking[$sales_booking_no] !='') 
						{
							$return_reject_qnty = $rowGrey[csf('cons_reject_qnty')];
						
							$all_sales_data_arr[$rowGrey[csf('knitting_source')]][$rowGrey[csf('knitting_company')]][$rowGrey[csf('sales_order')]]['grey_dyed_yarn'].=$rowGrey[csf('system_ref')].'**'.$rowGrey[csf('sales_booking_no')].'**'.$rowGrey[csf('style_ref_no')].'**'.$rowGrey[csf('trans_date')].'**'.$rowGrey[csf('challan_no')].'**'.$rowGrey[csf('basis')].'**'.$rowGrey[csf('entry_form')].'**'.$rowGrey[csf('transaction_type')].'**'.$rowGrey[csf('prod_id')].'**'.$rowGrey[csf('cons_uom')].'**'.$rowGrey[csf('brand_id')].'**'.$rowGrey[csf('buyer_id')].'**'.$rowGrey[csf('within_group')].'**'.$rowGrey[csf('cons_quantity')].'**'.$rowGrey[csf('item_category')].'**'.$return_reject_qnty.'##';
	
	
						}
					}
					/*echo "<pre>";
					print_r($party_wise_opening);
					die;*/
					$i=1;$balance=0;$opening_balance=0; 
					$tot_issue_qty=$tot_yarn_returnable=$tot_issue_return=$tot_yarn_reject=$tot_fabric_rcv=$tot_return_reject_qnty=$tot_dyed_twis_yarn =0;
					foreach ($all_sales_data_arr as $source_id =>$source_data) 
					{
						foreach ($source_data as $party_id => $party_data) 
						{
							asort($party_data);
							foreach ($party_data as $sales_order => $sales_data) 
							{
								
								if($source_id==1) $knitting_party=$company_arr[$party_id]; 
								else if($source_id==3) $knitting_party=$supplier_arr[$party_id];
								else $knitting_party="&nbsp;";	
								//echo "****".$party_wise_opening[$source_id][$party_id]['opening'];die;
								$yarn_issue_and_return= array();
								$yarn_issue_and_return = array_filter(array_unique(explode("##",$sales_data["yarn"])));
								if($chkParty[$knitting_party] == "")
								{
									$chkParty[$knitting_party]= $knitting_party;

									if($i>1)
									{
										?>
										<tr bgcolor="#EFEFEF">
											<td width="40">&nbsp;</td>
											<td width="130" align="center">&nbsp;</td>
											<td width="125"><div style="word-wrap:break-word; width:125px">&nbsp;</div></td>
											<td width="80" align="center">&nbsp;</td>
											<td width="80"><p>&nbsp;</p></td>
											<td width="80" align="center"><p>&nbsp;</p></td>
											<td width="120"><p>&nbsp;</p></td>
											<td width="80" align="center"><p>&nbsp;</p></td>
											<td width="80" align="center">&nbsp;</td>
											<td width="140"><div style="word-wrap:break-word; width:130px">&nbsp;</div></td>
											<td width="140"><div style="word-wrap:break-word; width:130px">&nbsp;</div></td>
											<td width="90" align="center" style="word-wrap:break-word;">&nbsp;
												</td>
											<td width="360" colspan="3"><p>Party wise Total</p></td>
											<td width="60" align="right"><? echo number_format($tot_issue_qty,2);?></td>
											<td width="100" align="right"><? echo number_format($tot_yarn_returnable,2);?></td>
										 
											<td width="100" align="right"><? echo number_format($tot_fabric_rcv,2);?></td>
											<td width="100" align="right"><? echo number_format($tot_return_reject_qnty,2);?></td>

											<td width="100" align="right"><? echo number_format($tot_dyed_twis_yarn,2);?></td>
										   
											<td width="100" align="right"><? echo number_format($tot_issue_return,2);?></td>
											<td width="100" align="right"><? echo number_format($tot_yarn_reject,2);?></td>
											<td width="" align="right"><? echo number_format($balance,2);//echo number_format($tot_balance,2); ?>&nbsp;</td>   
										</tr>

										<?
										$tot_issue_qty =0;
										$tot_yarn_returnable =0;
										$tot_issue_return =0;
										$tot_yarn_reject =0;
										$tot_fabric_rcv =0;
										$tot_return_reject_qnty =0;
										$tot_dyed_twis_yarn =0;
										$balance=0;
									}
									$opening_balance = $party_wise_opening[$source_id][$party_id]['opening']-$party_wise_opening[$source_id][$party_id]['opening_reject']; 

									?>
									<tr bgcolor="#EFEFEF"><td colspan="23" title="<? echo $party_id;?>"><b>Party name: <? echo $knitting_party." "; if($source_id==1) echo $location_arr[$party_id]; ?></b></td></tr>
									<? 
									if($opening_balance>0)
									{
										?>
										<tr bgcolor="#EFEFEF">
											<td colspan="22"><b>Opening Balance </b></td>
											<td align="right"><? echo number_format($opening_balance,2);?></td>
										</tr>
										<?
									}
									
								}
								$balance +=$opening_balance;
								foreach ($yarn_issue_and_return as $yarn_exp_row) 
								{
									$yarn_exp_data=array();
									$yarn_exp_data = explode("**",$yarn_exp_row);

									$system_ref=$yarn_exp_data[0]; 
									$sales_booking_no=$yarn_exp_data[1]; 
									$style_ref_no=$yarn_exp_data[2]; 
									$trans_date=$yarn_exp_data[3]; 
									$challan_no=$yarn_exp_data[4]; 
									$basis=$yarn_exp_data[5]; 
									$entry_form=$yarn_exp_data[6]; 
									$transaction_type=$yarn_exp_data[7]; 
									$prod_id=$yarn_exp_data[8]; 
									$cons_uom=$yarn_exp_data[9];  
									$requisition_no=$yarn_exp_data[10];  
									$brand_id=$yarn_exp_data[11]; 
									$cons_quantity=$yarn_exp_data[12]; 
									$buyer_id=$yarn_exp_data[13]; 
									$within_group=$yarn_exp_data[14]; 
									$return_reject_qnty=$yarn_exp_data[15]; 

									$issue_quantity=$yarn_returnable=$issue_return=$yarn_reject = 0;
									if($transaction_type == 2){
										$issue_quantity = $cons_quantity;
										$yarn_returnable = $return_reject_qnty;
										$balance += $issue_quantity;
										//$balance -= $return_reject_qnty;

									}else{
										$issue_return =  $cons_quantity;
										$yarn_reject = $return_reject_qnty;
										$balance -= $issue_return;
										$balance -= $return_reject_qnty;
									}
									if($within_group == 1)
									{
										$buyer_unit = $company_arr[$buyer_id];
										$buyer_name = $buyer_arr[$buyer_from_sales[$sales_order]];
									}else{
										$buyer_unit =  $buyer_arr[$buyer_id];
										$buyer_name="";
									}
									
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
									?>
										
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="40"><? echo $i; ?></td>
											<td width="130" align="center"><? echo $sales_order; ?></td>
											<td width="125"><p style="word-wrap:break-word; width:125px"><? echo $sales_booking_no; ?></p></td>
											<td width="80" align="center"><? echo $booking_type_arr[$sales_booking_no]; ?></td>
											<td width="80"><p><? echo $style_ref_no; ?></p></td>
											<td width="80" align="center"><p>&nbsp;<? echo change_date_format($trans_date); ?></p></td>
											<td width="120"><p>&nbsp;<? echo $system_ref; ?></p></td>
											<td width="80" align="center"><p><? echo $challan_no; ?>&nbsp;</p></td>
											<td width="80" align="center"><? echo $requisition_no; ?></td>
											<td width="140"><p style="word-wrap:break-word; width:130px"><? echo $buyer_name; ?></p></td>
											<td width="140"><p style="word-wrap:break-word; width:130px"><? echo $buyer_unit; ?></p></td>
											<td width="90" align="center" ><p style="word-wrap:break-word;">
												<? echo $brand_arr[$brand_id];//$brand_arr[$product_arr[$prod_id]['brand']]; ?></p></td>
											<td width="200"><p>&nbsp;<? echo $product_arr[$prod_id]['name']; ?></p></td>
											<td width="80"><p style="word-wrap:break-word;"><? echo $product_arr[$prod_id]['lot']; ?></p></td>
											<td width="80"><p><? echo $unit_of_measurement[$cons_uom]; ?></p></td>
											<td width="60" align="right"><? echo number_format($issue_quantity,2); ?>&nbsp;</td>
											<td width="100" align="right"><? echo number_format($yarn_returnable,2); ?></td>
										 
											<td width="100" align="right">&nbsp;</td>
											<td width="100" align="right">&nbsp;</td>

											<td width="100" align="right">&nbsp;</td>
										   
											<td width="100" align="right"><? echo number_format($issue_return,2);?></td>
											<td width="100" align="right"><? echo number_format($yarn_reject,2);?></td>
											<td width="" align="right"><? echo number_format($balance,2); ?>&nbsp;</td>
										   
										</tr>
										
									<?
									$i++;
									$tot_issue_qty +=$issue_quantity;
									$tot_yarn_returnable +=$yarn_returnable;
									$tot_issue_return +=$issue_return;
									$tot_yarn_reject +=$yarn_reject;
									$grand_tot_issue_qty +=$issue_quantity;
									$grand_tot_yarn_returnable +=$yarn_returnable;
									$grand_tot_issue_return +=$issue_return;
									$grand_tot_yarn_reject +=$yarn_reject;
									//$tot_balance += $balance;
								}

								
								$system_ref=$sales_booking_no=$style_ref_no=$trans_date="";

								$greyRcv_and_DyedYarnRcv = array();
								$greyRcv_and_DyedYarnRcv = array_filter(array_unique(explode("##",$sales_data["grey_dyed_yarn"])));
								/*echo "<pre>";
								print_r($greyRcv_and_DyedYarnRcv);
								echo "</pre>";
								die;*/
								$within_group=$item_category_id=$brand_id=$buyer_id=$cons_quantity=$return_reject_qnty=0;
								foreach ($greyRcv_and_DyedYarnRcv as $greyExpData) 
								{
									$greyExpRow=array();
									$greyExpRow = explode("**",$greyExpData);

									$system_ref=$greyExpRow[0]; 
									$sales_booking_no=$greyExpRow[1]; 
									$style_ref_no=$greyExpRow[2]; 
									$trans_date=$greyExpRow[3]; 
									$challan_no=$greyExpRow[4]; 
									$basis=$greyExpRow[5]; 
									$entry_form=$greyExpRow[6]; 
									$transaction_type=$greyExpRow[7]; 
									$prod_id=$greyExpRow[8]; 
									$cons_uom=$greyExpRow[9];   
									$brand_id=$greyExpRow[10]; 
									$buyer_id=$greyExpRow[11]; 
									$within_group=$greyExpRow[12]; 
									$cons_quantity=$greyExpRow[13]; 
									$item_category_id=$greyExpRow[14]; 
									$return_reject_qnty=$greyExpRow[15]; 
									//echo $buyer_id."_______".$within_group;die;
									if($within_group == 1)
									{
										$buyer_unit=$company_arr[$buyer_id]; 
										$buyer_name = $buyer_arr[$buyer_from_sales[$sales_order]];
									}else
									{
										$buyer_unit=$buyer_arr[$buyer_id]; 
										$buyer_name = "";
									}
									if($item_category_id == 13){
										$fabric_rcv = $cons_quantity;
									}else{
										$dyed_twis_yarn = $cons_quantity;
									}

									if($entry_form ==58)
									{
										$return_reject_qnty = $rejectQntyArr[$system_ref][$sales_order]["RejectQnty"];
										$balance -= $return_reject_qnty;
									}

									$balance -= $fabric_rcv+$dyed_twis_yarn+$return_reject_qnty;

									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="40"><? echo $i; ?></td>
											<td width="130" align="center"><? echo $sales_order; ?></td>
											<td width="125"><p style="word-wrap:break-word; width:125px"><? echo $sales_booking_no; ?></p></td>
											<td width="80" align="center"><? echo $booking_type_arr[$sales_booking_no]; ?></td>
											<td width="80"><p><? echo $style_ref_no; ?></p></td>
											<td width="80" align="center"><p>&nbsp;<? echo change_date_format($trans_date); ?></p></td>
											<td width="120"><p>&nbsp;<? echo $system_ref; ?></p></td>
											<td width="80" align="center"><p><? echo $challan_no; ?>&nbsp;</p></td>
											<td width="80" align="center">&nbsp;</td>
											<td width="140"><p style="word-wrap:break-word; width:130px"><? echo $buyer_name; ?></p></td>
											<td width="140"><p style="word-wrap:break-word; width:130px"><? echo $buyer_unit; ?></p></td>
											<td width="90" align="center" ><p style="word-wrap:break-word;">
												<? echo $brand_arr[$brand_id];//$brand_arr[$product_arr[$prod_id]['brand']]; ?></p></td>
											<td width="200"><p>&nbsp;<? echo $product_arr[$prod_id]['name']; ?></p></td>
											<td width="80"><p style="word-wrap:break-word;"><? echo $product_arr[$prod_id]['lot']; ?></p></td>
											<td width="80"><p><? echo $unit_of_measurement[$cons_uom]; ?></p></td>
											<td width="60" align="right">&nbsp;</td>
											<td width="100" align="right">&nbsp;</td>
										 
											<td width="100" align="right"><? echo number_format($fabric_rcv,2);?></td>
											<td width="100" align="right"><? echo number_format($return_reject_qnty,2);?></td>

											<td width="100" align="right"><? echo number_format($dyed_twis_yarn,2);?></td>
										   
											<td width="100" align="right">&nbsp;</td>
											<td width="100" align="right">&nbsp;</td>
											<td width="" align="right"><? echo number_format($balance,2); ?>&nbsp;</td>
										   
										</tr>
									
						
									<?
									$i++;

									$tot_fabric_rcv +=$fabric_rcv;
									$tot_return_reject_qnty +=$return_reject_qnty;
									$tot_dyed_twis_yarn +=$dyed_twis_yarn;

									$grand_tot_fabric_rcv +=$fabric_rcv;
									$grand_tot_return_reject_qnty +=$return_reject_qnty;
									$grand_tot_dyed_twis_yarn +=$dyed_twis_yarn;
									//$tot_balance += $balance;

								}

							}
							$opening_balance=0;
							$tot_balance += $balance;
						}	
					}

					?>
					<tr bgcolor="#EFEFEF">
						<td width="40">&nbsp;</td>
						<td width="130" align="center">&nbsp;</td>
						<td width="125"><div style="word-wrap:break-word; width:125px">&nbsp;</div></td>
						<td width="80" align="center">&nbsp;</td>
						<td width="80"><p>&nbsp;</p></td>
						<td width="80" align="center"><p>&nbsp;</p></td>
						<td width="120"><p>&nbsp;</p></td>
						<td width="80" align="center"><p>&nbsp;</p></td>
						<td width="80" align="center">&nbsp;</td>
						<td width="140"><div style="word-wrap:break-word; width:130px">&nbsp;</div></td>
						<td width="140"><div style="word-wrap:break-word; width:130px">&nbsp;</div></td>
						<td width="90" align="center" style="word-wrap:break-word;">&nbsp;
							</td>
						<td width="360" colspan="3" align="center"><p><b>Party wise Total</b></p></td>
						<td width="60" align="right"><? echo number_format($tot_issue_qty,2);?></td>
						<td width="100" align="right"><? echo number_format($tot_yarn_returnable,2);?></td>
					 
						<td width="100" align="right"><? echo number_format($tot_fabric_rcv,2);?></td>
						<td width="100" align="right"><? echo number_format($tot_return_reject_qnty,2);?></td>

						<td width="100" align="right"><? echo number_format($tot_dyed_twis_yarn,2);?></td>
					   
						<td width="100" align="right"><? echo number_format($tot_issue_return,2);?></td>
						<td width="100" align="right"><? echo number_format($tot_yarn_reject,2);?></td>
						<td width="" align="right"><? echo number_format($balance,2); ?>&nbsp;</td>
						   
					</tr>
				</table>
			</div>
			<table width="2340" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
					<td width="40">&nbsp;</td>
					<td width="130" align="center">&nbsp;</td>
					<td width="125"><div style="word-wrap:break-word; width:125px">&nbsp;</div></td>
					<td width="80" align="center">&nbsp;</td>
					<td width="80"><p>&nbsp;</p></td>
					<td width="80" align="center"><p>&nbsp;</p></td>
					<td width="120"><p>&nbsp;</p></td>
					<td width="80" align="center"><p>&nbsp;</p></td>
					<td width="80" align="center">&nbsp;</td>
					<td width="140"><div style="word-wrap:break-word; width:130px">&nbsp;</div></td>
					<td width="140"><div style="word-wrap:break-word; width:130px">&nbsp;</div></td>
					<td width="90" align="center" style="word-wrap:break-word;">&nbsp;
						</td>
					<td width="200" ><p>Grand Total</p></td>
					<td width="80" ><p></p></td>
					<td width="80" ><p></p></td>
					<td width="60" align="right"><? echo number_format($grand_tot_issue_qty,2);?></td>
					<td width="100" align="right"><? echo number_format($grand_tot_yarn_returnable,2);?></td>
				 
					<td width="100" align="right"><? echo number_format($grand_tot_fabric_rcv,2);?></td>
					<td width="100" align="right"><? echo number_format($grand_tot_return_reject_qnty,2);?></td>

					<td width="100" align="right"><? echo number_format($grand_tot_dyed_twis_yarn,2);?></td>
				   
					<td width="100" align="right"><? echo number_format($grand_tot_issue_return,2);?></td>
					<td width="100" align="right"><? echo number_format($grand_tot_yarn_reject,2);?></td>
					<td width="" align="right"><? echo number_format($tot_balance,2);?>&nbsp;</td>
					   
				</tr>
			</table>
			</div>
			<?
		}
		if($type==7) //Sales Order Wise (Summary)
		{
			if($db_type==0) $grpby_field="group by trans_id";
			if($db_type==2) $grpby_field="group by trans_id,entry_form,trans_type";
			else $grpby_field="";
			?>
			<div>
				<table width="1200" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
					<tr>
					   <td align="center" width="100%" colspan="10" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
					</tr> 
					<tr>  
					   <td align="center" width="100%" colspan="10" style="font-size:16px"><strong><? echo $report_title; ?> (Sales Order Wise)</strong></td>
					</tr>  
				</table>
				<table width="1200" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<thead>
						<th width="50">SL</th>
					   
						<th width="200">Party Name</th>
						<th width="100">Yarn Issued</th>
						<th width="100">Yarn Returnable qty</th>
					   
						<th width="100">Fabric Received</th>
						<th width="100">Reject Fabric Received</th>
	
						<th width="100">DY/TW/ WX/RCon Rec.</th>
	
						<th width="100">Yarn Returned</th>
						<th width="100">Reject Yarn Returned</th>
						<th width="">Balance</th> 
					   
					</thead>
				</table>
				<div style="width:1200px; overflow-y: scroll; max-height:380px;" id="scroll_body">
					<table width="1183" cellpadding="2" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
					<?
						$rec_qty_arr=array();
						$sql_rec="SELECT a.recv_number, a.recv_number_prefix_num, a.buyer_id, a.booking_no,a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no,  a.knitting_source, a.knitting_company, a.receive_basis, a.entry_form, b.id as trans_id, b.prod_id, b.pi_wo_batch_no,b.cons_uom,  b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type from inv_receive_master a, inv_transaction b $propotionate_tbl where a.item_category in(1,13) and a.entry_form in(9,22) and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category in(1,13) and b.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_basis in(3,4,9) $date_cond $where_cond  $booking_cond_recv $source_cond
							union
							 SELECT a.recv_number, a.recv_number_prefix_num, a.buyer_id, a.booking_no,a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no,  a.knitting_source, a.knitting_company, a.receive_basis, a.entry_form, b.id as trans_id, b.prod_id, b.pi_wo_batch_no,b.cons_uom,  b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type from inv_receive_master a, inv_transaction b $propotionate_tbl where a.item_category in(1) and a.entry_form=1 and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category in(1) and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.receive_basis in(4) and a.receive_purpose in (2,12,15,38) $date_cond $where_cond  $booking_cond_recv $source_cond
							order by knitting_source, knitting_company, recv_number_prefix_num, receive_date"; 	
						// echo $sql_rec; 
	
						$sql_rec_res=sql_select($sql_rec);
						foreach ($sql_rec_res as $rowRec ) //recv Basis 2=Independed not issue ret.
						{
							$trns_date=''; $date_frm=''; $date_to='';
							$trns_date=date('Y-m-d',strtotime($rowRec[csf('receive_date')]));
							$date_frm=date('Y-m-d',strtotime($from_date));
							$date_to=date('Y-m-d',strtotime($to_date));
	
							if(($rowRec[csf('entry_form')]==9) || ($rowRec[csf('entry_form')]==1))
							{
								$booking_nos=$requisition_numArr2[$rowRec[csf('booking_no')]]['booking_no'];	
							}
							else
							{
								$booking_nos=$rowRec[csf('booking_no')];
							}

	
							if (in_array($booking_nos, $sales_order_booking)) 
							{
								if(($rowRec[csf('entry_form')]==9) || ($rowRec[csf('entry_form')]==1))
								{
									$all_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]]['rej_yarn']+=$rowRec[csf('cons_reject_qnty')];
									
									if(($rowRec[csf('entry_form')]==1))
									{
										$all_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]]['yRec']+=$rowRec[csf('cons_quantity')];
									}
									else
									{
										$all_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]]['ret_yarn']+=$rowRec[csf('cons_quantity')];
									}
								}
								else
								{
									$all_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]][$rowRec[csf('trans_id')]][$rowRec[csf('prod_id')]]['rej_fab']+=$rowRec[csf('cons_reject_qnty')];
									$all_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]][$rowRec[csf('trans_id')]][$rowRec[csf('prod_id')]]['fRec']+=$rowRec[csf('cons_quantity')];
								}
	
								$all_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]]['knitting_company']=$rowRec[csf('knitting_company')];
							}	
						}
	
						$sql_rec="SELECT a.id, a.recv_number, a.recv_number_prefix_num, a.buyer_id, a.booking_no,a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no,  a.knitting_source, a.knitting_company, a.receive_basis, a.entry_form, b.id as trans_id, b.yarn_prod_id as prod_id, 0 as pi_wo_batch_no,b.uom as cons_uom,  b.brand_id, b.grey_receive_qnty as cons_quantity, 0 as return_qnty, b.reject_fabric_receive as cons_reject_qnty, 1 as transaction_type, a.roll_maintained from inv_receive_master a, pro_grey_prod_entry_dtls b where a.item_category in(13) and a.entry_form=2 and a.company_id=$cbo_company_name and a.id=b.mst_id and b.yarn_prod_id is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond2 $planning_cond  $booking_cond_recv $source_cond
							order by knitting_source, knitting_company, recv_number_prefix_num, receive_date";
						// echo $sql_rec;
						$sql_rec_res=sql_select($sql_rec);
						foreach ($sql_rec_res as $rowRec )  //For roll wise receive
						{
							$trns_date=''; $date_frm=''; $date_to='';
							$trns_date=date('Y-m-d',strtotime($rowRec[csf('receive_date')]));
							$date_frm=date('Y-m-d',strtotime($from_date));
							$date_to=date('Y-m-d',strtotime($to_date));
	
							if($rowRec[csf('roll_maintained')]==1)
							{ 

								$reject=0;
								$barcodes="";
								$barcode_arr=array();
								$mst_id=$rowRec[csf('id')];
								$dtls_id=$rowRec[csf('trans_id')];
								$booking_nos=$requisition_numArr3[$rowRec[csf('booking_no')]]['booking_no'];
	
								if (in_array($booking_nos, $sales_order_booking)) 
								{

									$roll_sql="SELECT reject_qnty, barcode_no from pro_roll_details where mst_id=$mst_id and dtls_id=$dtls_id and entry_form=2 and status_active=1 and is_deleted=0";
									foreach (sql_select($roll_sql) as $value) 
									{
										$barcode_arr[$value[csf("barcode_no")]]=$value[csf("barcode_no")];
										//$reject+=$value[csf("reject_qnty")];
										$barcode_rej[$value[csf("barcode_no")]]["reject"]=$value[csf("reject_qnty")];
									}
	
									$barcodes=implode(",", $barcode_arr);
	
									$roll_sql="SELECT a.id as trans_id, a.receive_date, b.barcode_no, b.qnty from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.barcode_no in ($barcodes) and b.entry_form=58 and b.status_active=1 and b.is_deleted=0";

									$roll_rec=sql_select($roll_sql);
									foreach ($roll_rec as $data) 
									{
										$all_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]]['rej_fab']+=$barcode_rej[$data[csf("barcode_no")]]["reject"];
	
										$all_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]]['fRec']+=$data[csf('qnty')];
									}
	
									$all_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]]['knitting_company']=$rowRec[csf('knitting_company')];
								}

							}else {

								$all_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]]['rej_fab']+=$rowRec[csf('cons_reject_qnty')];
	
								$all_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]]['fRec']+=$rowRec[csf('cons_quantity')];
							}
						}
						unset($sql_rec_res);
						
						//echo "<pre>";
						//print_r($all_data_arr);

						unset($sql_rec_res);
						if (str_replace("'","",$cbo_knitting_source)==0) $knit_source_cond_party=""; else $knit_source_cond_party=" and a.knit_dye_source=$cbo_knitting_source";
						$txt_knitting_com_id = str_replace("'","",$txt_knitting_com_id);
						$party_cond = '';
						if ($txt_knitting_com_id == '' || $txt_knitting_com_id == 0)
						{
							$party_cond = '';
						}
						else
						{
							$party_cond = "and a.knit_dye_company in ($txt_knitting_com_id)";
						}


						if ($knitting_company=='') $knit_company_cond_party=""; else $knit_company_cond_party=" and a.knit_dye_company in ($knitting_company)";
						
						$iss_qty_arr=array(); 
	
						$sql_iss="SELECT a.issue_number, a.issue_number_prefix_num, a.buyer_id, a.booking_no, a.issue_date, a.challan_no, a.issue_basis, a.knit_dye_source, a.knit_dye_company, b.id as trans_id, b.prod_id, b.transaction_date, b.cons_uom, b.requisition_no, b.brand_id, b.cons_quantity, b.return_qnty from inv_issue_master a, inv_transaction b  $propotionate_tbl where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.issue_basis in(3,4) and a.issue_purpose in(1,4,8) $date_cond $where_cond $booking_cond_issue $party_cond  order by  a.knit_dye_source, a.knit_dye_company, a.issue_number_prefix_num, a.issue_date";
	
						// echo $sql_iss;die;
						$sql_iss_res=sql_select($sql_iss); $tot_opening_balIssue=$tot_opening_bal_return=0;

						foreach ($sql_iss_res as $rowIss )
						{
							$trns_date=''; $date_frm=''; $date_to='';
							$trns_date=date('Y-m-d',strtotime($rowIss[csf('issue_date')]));
							$date_frm=date('Y-m-d',strtotime($from_date));
							$date_to=date('Y-m-d',strtotime($to_date));
							$issue_basis=$rowIss[csf('issue_basis')];
	
							$booking_no=$requisition_numArr2[$rowIss[csf('requisition_no')]]['booking_no'];
	
							if (in_array($booking_no, $sales_order_booking)) 
							{	
	
								$all_data_arr[$rowIss[csf('knit_dye_source')]][$rowIss[csf('knit_dye_company')]]['issue']+=$rowIss[csf('cons_quantity')];
	
								$all_data_arr[$rowIss[csf('knit_dye_source')]][$rowIss[csf('knit_dye_company')]]['return']+=$rowIss[csf('return_qnty')];
	
								$all_data_arr[$rowIss[csf('knit_dye_source')]][$rowIss[csf('knit_dye_company')]]['knitting_company']=$rowIss[csf('knit_dye_company')];
							}
						}
						//die;
						/*echo "<pre>"; 
						print_r($all_data_arr); die;*/
						unset($sql_iss_res); 
	
						$party_issue_qty=$party_returnable_qty=$party_rec_qty=$party_fab_rej_qty=$party_return_qty=0;
						$party_yarn_rej_qty=$party_yarnRec_qty=0;$party_balance=0;
	
						$i=1;
						foreach ($all_data_arr as $source_id=>$source_data )
						{
							/*echo "<pre>"; 
							print_r($source_data); die;*/
							foreach ($source_data as $party_id=> $data) 
							{
							if($source_id==1) $knitting_party=$company_arr[$party_id]; 
							else if($source_id==3) $knitting_party=$supplier_arr[$party_id];
							else $knitting_party="&nbsp;";	
							
							$balance=0; $return_balance=0;
							$issue_qty=0; 
	
							$issue_qty=$all_data_arr[$source_id][$party_id]['issue'];
							$yarn_returnable_qty=$all_data_arr[$source_id][$party_id]['return'];
	
							$balance=($balance+$issue_qty);
							
							$receive_qty=$all_data_arr[$source_id][$party_id]['fRec'];
							//echo $receive_qty."test";
							$fab_reject_qty=$all_data_arr[$source_id][$party_id]['rej_fab'];
							$yarn_reject_qty=$all_data_arr[$source_id][$party_id]['rej_yarn'];
							$yarnReceive_qty=$all_data_arr[$source_id][$party_id]['yRec'];
							$return_qty=$all_data_arr[$source_id][$party_id]['ret_yarn'];
							
							$balance=$opening_balance+$balance-($receive_qty+$fab_reject_qty+$yarn_reject_qty+$yarnReceive_qty+$return_qty);
								
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="50"><? echo $i; ?></td>
									
									<td width="200"><p><? echo $knitting_party; ?></p></td>
									<td width="100" align="right"><? echo number_format($issue_qty,2); ?>&nbsp;</td>
									<td width="100" align="right"><? echo number_format($yarn_returnable_qty,2); ?></td>
								 
									<td width="100" align="right"><? echo number_format($receive_qty,2); ?></td>
									<td width="100" align="right"><? echo number_format($fab_reject_qty,2); ?></td>
	
									<td width="100" align="right"><? echo number_format($yarnReceive_qty,2); ?></td>
								   
									<td width="100" align="right"><? echo number_format($return_qty,2); ?></td>
									<td width="100" align="right"><? echo number_format($yarn_reject_qty,2); ?></td>
									<td width="" align="right"><? echo number_format($balance,2); ?></td>
								</tr>
								<?
								
								$party_issue_qty+=$issue_qty;
								$party_returnable_qty+=$yarn_returnable_qty;
								$party_rec_qty+=$receive_qty;
								$party_fab_rej_qty+=$fab_reject_qty;
								$party_yarnRec_qty+=$yarnReceive_qty;
								$party_return_qty+=$return_qty;
								$party_yarn_rej_qty+=$yarn_reject_qty;
								$party_balance+=$balance;
								$i++;
							}
	
						}
							
						?>
						   <tr bgcolor="#CCCCCC" >
								<td>&nbsp;</td>
								<td align="right"><strong>Total &nbsp;</strong></td>
								<td align="right"><? echo number_format($party_issue_qty,2); ?>&nbsp;</td> 
								<td align="right"><? echo number_format($party_returnable_qty,2); ?></td>
							   
								<td align="right"><? echo number_format($party_rec_qty,2); ?></td>
								<td align="right"><? echo number_format($party_fab_rej_qty,2); ?></td>
							  
								<td align="right"><? echo number_format($party_yarnRec_qty,2); ?></td>
								
								<td align="right"><? echo number_format($party_return_qty,2); ?></td>
								<td align="right"><? echo number_format($party_yarn_rej_qty,2); ?></td>
								<td align="right"><? echo number_format($party_balance,2); ?></td>
							</tr> 
					</table>
				</div>
			</div>
			<?
		}
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


if($action == "report_generate_sales_order")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	ob_start();
	$started = microtime(true);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_arr=return_library_array( "select id, short_name from  lib_buyer", "id", "short_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

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
	
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$date_cond=" and b.transaction_date between $txt_date_from and $txt_date_to";
		$date_cond2="and a.receive_date between $txt_date_from and $txt_date_to";
	}
	
	$booking_no=str_replace("'","",$txt_booking_no);
	$hide_booking_id=str_replace("'","",$hide_booking_id);
	$booking_type=str_replace("'","",$hide_booking_type);
	$txt_fso_no=str_replace("'","",$txt_fso_no);
	$hide_fso_id=str_replace("'","",$hide_fso_id);
	$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
	$txt_knitting_com_id=str_replace("'","",$txt_knitting_com_id);
	
	$source_cond = $source_cond2 = $source_cond3 ="";
	if($txt_knitting_com_id!="") $source_cond .= " and a.knitting_company in (".$txt_knitting_com_id.")";
	if($cbo_knitting_source) $source_cond .= " and a.knitting_source=".$cbo_knitting_source;
	if($txt_knitting_com_id!="") $source_cond2 .= " and a.knit_dye_company in (".$txt_knitting_com_id.")";
	if($cbo_knitting_source) $source_cond2 .= " and a.knit_dye_source=".$cbo_knitting_source;	
	if($txt_knitting_com_id!="") $source_cond3 .= " and a.supplier_id in (".$txt_knitting_com_id.")";

	if(str_replace("'","",$txt_job_id) !="" || str_replace("'","",$txt_internal_ref) !="")
	{
		$txt_job_ids=str_replace("'","",$txt_job_id);
		if (str_replace("'","",$txt_internal_ref)=="") $intrl_ref_cnd=""; else $intrl_ref_cnd=" and d.grouping=$txt_internal_ref";
		if ($txt_job_ids=="") 
		{
			$txt_job_no_cnd=""; 
		}
		else 
		{
			$txt_job_no_cnd=" and c.id in ($txt_job_ids)";
		}

		$jobNo_arr=sql_select("select e.booking_no,e.id as booking_id from wo_po_details_master c, wo_po_break_down d, wo_booking_mst e where c.job_no=d.job_no_mst and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.job_no=e.job_no and e.status_active=1 and e.is_deleted=0  $intrl_ref_cnd $txt_job_no_cnd");
		foreach ($jobNo_arr as $value) 
		{
			$hide_booking_id .= ($hide_booking_id=="")? $value[csf("booking_id")] : ",".$value[csf("booking_id")];
		}
	}

	$hide_booking_id=implode(",",array_unique(explode(",",$hide_booking_id)));
	$hide_fso_id=implode(",",array_unique(explode(",",$hide_fso_id)));

	if($hide_fso_id!="") $sales_order_condition .= " and d.id in (".$hide_fso_id.")";
	if($hide_booking_id!="") $sales_order_condition .= " and d.booking_id in (".$hide_booking_id.")";

	if($hide_fso_id!="") $sales_order_condition2 .= " and e.id in (".$hide_fso_id.")";
	if($hide_booking_id!="") $sales_order_condition2 .= " and e.booking_id in (".$hide_booking_id.")";

	$type=str_replace("'","",$type);

	
	

	if($type == 60)
	{
		$booking_type_ref_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
		?>

		<style>
		.word_break_wrap {
			word-break: break-all;
			word-wrap:break-word;
		}
	</style>
	<div>
		<table width="2200" cellpadding="0" cellspacing="0" id="caption">
			<tr>
				<td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
			</tr> 
			<tr>  
				<td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $report_title; ?> (Sales Order Wise)</strong></td>
			</tr>  
		</table>
		<table width="2340" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th width="130">Fabric Sales Order</th>
				<th width="125">Fabric Booking No</th>
				<th width="80">Booking Type</th>
				<th width="80">Style Ref</th>
				<th width="80">Date</th>
				<th width="120">Transaction Ref.</th>
				<th width="80">Challan No.</th>
				<th width="80">Requ No.</th>
				<th width="140">PO Buyer</th>
				<th width="140">Buyer/Unit</th>
				<th width="90">Brand.</th>
				<th width="200">Item Description</th>
				<th width="80">Lot</th>
				<th width="80">Purpose</th>
				<th width="60">Yarn Issued</th>
				<th width="100">Yarn Returnable qty</th>
				<th width="100">Fabric Received</th>
				<th width="100">Reject Fabric Received</th>
				<th width="100">DRY/DY/TW/ WX/RCon Rec.</th>
				<th width="100">Yarn Returned</th>
				<th width="100">Reject Yarn Returned</th>
				<th width="">Balance</th>
			</thead>
		</table>
		<div style="width:2358px; overflow-y: scroll; max-height:380px; float: left;" id="scroll_body">
			<table width="2340" cellpadding="2" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" align="left">
				<?
				$product_arr=array();
				$sql_prod="select id, product_name_details, lot, brand from product_details_master where item_category_id in (1,2,22,13) ";
				$sql_prod_res=sql_select($sql_prod);
				foreach($sql_prod_res as $rowp)
				{
					$product_arr[$rowp[csf('id')]]['name']=$rowp[csf('product_name_details')];
					$product_arr[$rowp[csf('id')]]['lot']=$rowp[csf('lot')];
					$product_arr[$rowp[csf('id')]]['brand']=$rowp[csf('brand')];
				}
				unset($sql_prod_res);

				$rec_qty_arr=array();
				$sql_yarn="select a.knit_dye_source as knitting_source, a.knit_dye_company as  knitting_company,d.sales_booking_no, d.job_no as sales_order,d.style_ref_no,d.buyer_id,d.within_group, a.issue_number as system_ref,a.challan_no, a.issue_basis as  basis,c.issue_purpose, a.entry_form, b.requisition_no, b.prod_id, b.cons_uom, b.brand_id, sum(b.cons_quantity) as cons_quantity, sum(b.return_qnty) as return_qnty, sum(b.cons_reject_qnty) as cons_reject_qnty, b.transaction_type, a.issue_date as trans_date,d.po_buyer,d.id sales_id ,d.booking_without_order, d.booking_entry_form, d.booking_type
				from inv_issue_master a, inv_transaction b, order_wise_pro_details c, fabric_sales_order_mst d
				where  a.id=b.mst_id and b.id = c.trans_id and c.po_breakdown_id = d.id and b.item_category in(1) and b.transaction_type in(2)  
				and a.entry_form=3 and c.entry_form=3 and c.is_sales=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.company_id=$cbo_company_name $date_cond $sales_order_condition $source_cond2 and a.issue_basis in (1,3,4) 
				group by a.knit_dye_source,a.knit_dye_company,d.sales_booking_no,d.job_no,d.style_ref_no,d.buyer_id,d.within_group,  a.issue_number ,a.challan_no, a.issue_basis ,c.issue_purpose, a.entry_form, b.requisition_no, b.prod_id, b.cons_uom,b.brand_id,b.transaction_type,a.issue_date ,d.po_buyer,d.id,d.booking_without_order, d.booking_entry_form, d.booking_type
				union all
				select a.knitting_source, a.knitting_company,d.sales_booking_no, d.job_no as sales_order,d.style_ref_no,d.buyer_id,d.within_group, a.recv_number as system_ref,a.challan_no,  a.receive_basis as  basis,0 issue_purpose, a.entry_form, (case when a.receive_basis = 3 then a.booking_id end) as requisition_no, b.prod_id, b.cons_uom, b.brand_id, sum(b.cons_quantity) as cons_quantity, sum(b.return_qnty) as return_qnty, sum(b.cons_reject_qnty) as cons_reject_qnty, b.transaction_type,a.receive_date as trans_date,d.po_buyer,d.id sales_id ,d.booking_without_order, d.booking_entry_form, d.booking_type
				from inv_receive_master a, inv_transaction b, order_wise_pro_details c, fabric_sales_order_mst d
				where  a.id=b.mst_id and b.id = c.trans_id and c.po_breakdown_id = d.id and b.item_category in(1) and b.transaction_type in(4)  
				and a.entry_form=9 and c.entry_form=9 and a.company_id=$cbo_company_name $date_cond $source_cond $sales_order_condition and c.is_sales=1 and b.status_active=1 and a.status_active=1 and c.status_active=1 and d.status_active=1 and a.receive_basis in (1,3,4)				
				group by a.knitting_source, a.knitting_company,d.sales_booking_no, d.job_no ,d.style_ref_no,d.buyer_id,d.within_group, a.recv_number,a.challan_no, a.receive_basis , a.entry_form,  b.prod_id, b.cons_uom, b.brand_id,  b.transaction_type,a.receive_date, a.booking_id,d.po_buyer,d.id,d.booking_without_order,d.booking_entry_form,d.booking_type";

				$sql_yarn_res=sql_select($sql_yarn);
				$return_reject_qnty = $returnable_qnty = 0;
				foreach ($sql_yarn_res as $rowRec ) 
				{						
					$issTransChk[$rowRec[csf('trans_id')]] = $rowRec[csf('trans_id')];
					$sales_booking_no = $rowRec[csf('sales_booking_no')];

					
					if($rowRec[csf('knitting_company')] > 0 || $rowRec[csf('knitting_company')] !=""){
						if($rowRec[csf('entry_form')]==3)
						{						
							$returnable_qnty = $rowRec[csf('return_qnty')];
							$return_reject_qnty=0;
						}else{
							$returnable_qnty=0;
							$return_reject_qnty = $rowRec[csf('cons_reject_qnty')];
						}
						$all_sales_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]][$rowRec[csf('sales_order')]]['yarn'].=$rowRec[csf('system_ref')].'**'.$rowRec[csf('sales_booking_no')].'**'.$rowRec[csf('style_ref_no')].'**'.$rowRec[csf('trans_date')].'**'.$rowRec[csf('challan_no')].'**'.$rowRec[csf('basis')].'**'.$rowRec[csf('entry_form')].'**'.$rowRec[csf('transaction_type')].'**'.$rowRec[csf('prod_id')].'**'.$rowRec[csf('issue_purpose')].'**'.$rowRec[csf('requisition_no')].'**'.$rowRec[csf('brand_id')].'**'.$rowRec[csf('cons_quantity')].'**'.$rowRec[csf('buyer_id')].'**'.$rowRec[csf('within_group')].'**'.$return_reject_qnty.'**'.$rowRec[csf('po_buyer')].'**'.$returnable_qnty.'##';
						//$sales_order_ids_arr[$rowRec[csf('sales_id')]] = $rowRec[csf('sales_id')];

						$booking_arr[$sales_booking_no] = "'".$sales_booking_no."'";
					}
					if($rowRec[csf('booking_type')] == 4)
					{
						if($rowRec[csf('booking_without_order')] == 1)
						{
							$bookingType = "Sample Without Order";
						}
						else
						{
							$bookingType =  "Sample With Order";
						}
					}
					else
					{
						$bookingType =  $booking_type_ref_arr[$rowRec[csf('booking_entry_form')]];
					}			
					$sales_booking_type[$rowRec[csf("sales_order")]] = $bookingType; 


				}

				
				unset($issTransChk);
				$party_wise_opening=array();
				if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="" && $txt_knitting_com_id !="")
				{					
					$sql_yarn_opening="select a.knit_dye_source as knitting_source, a.knit_dye_company as  knitting_company,d.sales_booking_no, d.job_no as sales_order,d.style_ref_no,d.buyer_id,d.within_group, a.issue_number as system_ref,a.challan_no, a.issue_basis as  basis,c.issue_purpose, a.entry_form, b.requisition_no, b.prod_id, b.cons_uom, b.brand_id, sum(b.cons_quantity) as cons_quantity, sum(b.return_qnty) as return_qnty, sum(b.cons_reject_qnty) as cons_reject_qnty, b.transaction_type, a.issue_date as trans_date,d.po_buyer,d.id sales_id ,d.booking_without_order, d.booking_entry_form, d.booking_type
					from inv_issue_master a, inv_transaction b, order_wise_pro_details c, fabric_sales_order_mst d
					where  a.id=b.mst_id and b.id = c.trans_id and c.po_breakdown_id = d.id and b.item_category in(1) and b.transaction_type in(2)  
					and a.entry_form=3 and c.entry_form=3 and c.is_sales=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.company_id=$cbo_company_name $source_cond2 and a.issue_basis in (1,3,4) and b.transaction_date >= '01-Jul-2017' and b.transaction_date < '" . str_replace("'","",$txt_date_from) . "'
					group by a.knit_dye_source,a.knit_dye_company,d.sales_booking_no,d.job_no,d.style_ref_no,d.buyer_id,d.within_group,  a.issue_number ,a.challan_no, a.issue_basis ,c.issue_purpose, a.entry_form, b.requisition_no, b.prod_id, b.cons_uom,b.brand_id,b.transaction_type,a.issue_date ,d.po_buyer,d.id,d.booking_without_order, d.booking_entry_form, d.booking_type
					union all
					select a.knitting_source, a.knitting_company,d.sales_booking_no, d.job_no as sales_order,d.style_ref_no,d.buyer_id,d.within_group, a.recv_number as system_ref,a.challan_no,  a.receive_basis as  basis,0 issue_purpose, a.entry_form, (case when a.receive_basis = 3 then a.booking_id end) as requisition_no, b.prod_id, b.cons_uom, b.brand_id, sum(b.cons_quantity) as cons_quantity, sum(b.return_qnty) as return_qnty, sum(b.cons_reject_qnty) as cons_reject_qnty, b.transaction_type,a.receive_date as trans_date,d.po_buyer,d.id sales_id ,d.booking_without_order, d.booking_entry_form, d.booking_type
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c, fabric_sales_order_mst d
					where  a.id=b.mst_id and b.id = c.trans_id and c.po_breakdown_id = d.id and b.item_category in(1) and b.transaction_type in(4)  
					and a.entry_form=9 and c.entry_form=9 and a.company_id=$cbo_company_name $source_cond and c.is_sales=1 and b.status_active=1 and a.status_active=1 and c.status_active=1 and d.status_active=1 and a.receive_basis in(1,3,4) and b.transaction_date >= '01-Jul-2017' and b.transaction_date < '" . str_replace("'","",$txt_date_from) . "'
					group by a.knitting_source, a.knitting_company,d.sales_booking_no, d.job_no ,d.style_ref_no,d.buyer_id,d.within_group, a.recv_number,a.challan_no, a.receive_basis , a.entry_form,  b.prod_id, b.cons_uom, b.brand_id,  b.transaction_type,a.receive_date, a.booking_id,d.po_buyer,d.id,d.booking_without_order,d.booking_entry_form,d.booking_type";

					$sql_yarn_opening_res=sql_select($sql_yarn_opening);
					foreach ($sql_yarn_opening_res as $row_open) 
					{
						if($row_open[csf('knitting_company')] > 0 || $row_open[csf('knitting_company')] !=""){
							if($row_open[csf('entry_form')]==3)
							{
								$party_wise_opening[$row_open[csf('knitting_source')]][$row_open[csf('knitting_company')]]['opening_issue'] += $row_open[csf('cons_quantity')];
							}else{
								$party_wise_opening[$row_open[csf('knitting_source')]][$row_open[csf('knitting_company')]]['opening_return'] += $row_open[csf('cons_quantity')];
								$party_wise_opening[$row_open[csf('knitting_source')]][$row_open[csf('knitting_company')]]['opening_return_reject'] += $row_open[csf('cons_reject_qnty')];
							}
						}
					}

					$sql_grey_opening_sql = "select a.knitting_source, a.knitting_company,d.id sales_id, d.sales_booking_no,a.recv_number as system_ref,d.style_ref_no, d.buyer_id,d.within_group, a.receive_date as trans_date,a.challan_no,b.prod_id, a.receive_basis as basis,b.transaction_type, d.job_no as sales_order,sum(b.cons_quantity) as cons_quantity,sum(b.cons_reject_qnty) as cons_reject_qnty, b.cons_uom, b.brand_id,a.item_category, a.entry_form
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c, fabric_sales_order_mst d
					where a.id=b.mst_id and b.id=c.trans_id and c.po_breakdown_id=d.id and c.is_sales=1 and a.receive_basis in (2,4,10) and a.entry_form in(2,22,58) and c.entry_form in (2,22,58) and c.trans_id > 0 and b.status_active=1 and d.status_active=1 and a.status_active=1 and c.status_active=1 and a.item_category=13 and a.company_id=$cbo_company_name $sales_order_condition $source_cond and b.transaction_date >= '01-Jul-2017' and b.transaction_date < '" . str_replace("'","",$txt_date_from) . "'
					group by a.knitting_source, a.knitting_company,d.id, d.sales_booking_no,a.recv_number ,d.style_ref_no, d.buyer_id,d.within_group, a.receive_date, a.challan_no,b.prod_id, a.receive_basis,b.transaction_type, d.job_no , b.cons_uom, b.brand_id,a.item_category ,a.entry_form";
					if($cbo_knitting_source==3 || $cbo_knitting_source==0)
					{
						$sql_grey_opening_sql .=" union all    
						select 3 as knitting_source, a.supplier_id as knitting_company,e.id sales_id,e.sales_booking_no,sum(b.cons_quantity) as cons_quantity,sum(b.cons_reject_qnty) as cons_reject_qnty , b.id as trans_id
						from inv_receive_master a, inv_transaction b, wo_yarn_dyeing_mst c,wo_yarn_dyeing_dtls d, fabric_sales_order_mst e 
						where  a.id=b.mst_id and b.pi_wo_batch_no = d.id and c.id = d.mst_id and d.job_no = e.job_no and b.item_category =1 and b.transaction_type =1 and a.entry_form =1 and a.receive_basis = 2 and c.entry_form in (94,135) and a.receive_purpose in (2,12,15,38,46) and b.status_active = 1 and a.status_active = 1 and c.status_active = 1 and d.status_active = 1 and e.status_active = 1  and c.is_sales = 1 and a.company_id=$cbo_company_name $sales_order_condition2 $source_cond3 and b.transaction_date < '" . str_replace("'","",$txt_date_from) . "'
						group by a.supplier_id,e.id,e.sales_booking_no, b.id";
					}

					$sql_grey_opening =  sql_select($sql_grey_opening_sql);

					foreach ($sql_grey_opening as $row_open_2 ) 
					{
						if($row_open_2[csf('knitting_company')]>0){
							$party_wise_opening[$row_open_2[csf('knitting_source')]][$row_open_2[csf('knitting_company')]]['opening_grey'] += $row_open_2[csf('cons_quantity')];
							//$party_wise_opening[$row_open_2[csf('knitting_source')]][$row_open_2[csf('knitting_company')]]['opening_grey_reject'] = $row_open_2[csf('cons_reject_qnty')];
						}
						$sales_order_ids_arr[$row_open_2[csf('sales_id')]] = $row_open_2[csf('sales_id')];
					}
				}
				//print_r($sales_order_ids_arr);die;

				$sql_grey_fabric =  sql_select("select a.knitting_source, a.knitting_company,d.id sales_id, d.sales_booking_no,a.recv_number as system_ref,d.style_ref_no, d.buyer_id,d.within_group, a.receive_date as trans_date,a.challan_no,b.prod_id, a.receive_basis as basis,b.transaction_type, d.job_no as sales_order,sum(b.cons_quantity) as cons_quantity,sum(b.cons_reject_qnty) as cons_reject_qnty, b.cons_uom, b.brand_id,a.item_category, a.entry_form
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c, fabric_sales_order_mst d
					where a.id=b.mst_id and b.id=c.trans_id and c.po_breakdown_id=d.id and c.is_sales=1 and a.receive_basis in (2,4,10) and a.entry_form in(2,22,58) and c.entry_form in (2,22,58) and c.trans_id > 0 and b.status_active=1 and d.status_active=1 and a.status_active=1 and c.status_active=1 and a.item_category=13 and a.company_id=$cbo_company_name $date_cond $source_cond $sales_order_condition
					group by a.knitting_source, a.knitting_company,d.id, d.sales_booking_no,a.recv_number ,d.style_ref_no, d.buyer_id,d.within_group, a.receive_date, a.challan_no,b.prod_id, a.receive_basis,b.transaction_type, d.job_no , b.cons_uom, b.brand_id,a.item_category ,a.entry_form");

				$return_reject_qnty = 0;
				foreach ($sql_grey_fabric as $rowGrey) 
				{
					$return_reject_qnty = 0;
					if($rowGrey[csf('knitting_company')]>0){
						$all_sales_data_arr[$rowGrey[csf('knitting_source')]][$rowGrey[csf('knitting_company')]][$rowGrey[csf('sales_order')]]['grey_dyed_yarn'].=$rowGrey[csf('system_ref')].'**'.$rowGrey[csf('sales_booking_no')].'**'.$rowGrey[csf('style_ref_no')].'**'.$rowGrey[csf('trans_date')].'**'.$rowGrey[csf('challan_no')].'**'.$rowGrey[csf('basis')].'**'.$rowGrey[csf('entry_form')].'**'.$rowGrey[csf('transaction_type')].'**'.$rowGrey[csf('prod_id')].'**'.$rowGrey[csf('cons_uom')].'**'.$rowGrey[csf('brand_id')].'**'.$rowGrey[csf('buyer_id')].'**'.$rowGrey[csf('within_group')].'**'.$rowGrey[csf('cons_quantity')].'**'.$rowGrey[csf('item_category')].'**'.$return_reject_qnty.'##';
					}
					$sales_order_ids_arr[$rowGrey[csf('sales_id')]] = $rowGrey[csf('sales_id')];
				}

				if($cbo_knitting_source==3 || $cbo_knitting_source==0)
				{
					$sql_grey_dyed_yarn_sql_2 = "    
					select 3 as knitting_source, a.supplier_id as knitting_company,e.sales_booking_no, a.recv_number as system_ref,e.style_ref_no, e.buyer_id,e.within_group, a.receive_date as trans_date,a.challan_no,b.prod_id,a.receive_basis as  basis, a.receive_purpose, b.transaction_type ,e.job_no as sales_order,(b.cons_quantity) as cons_quantity,(b.cons_reject_qnty) as cons_reject_qnty, b.cons_uom,b.brand_id,a.item_category ,a.entry_form, e.po_buyer,e.id sales_id ,e.booking_without_order, e.booking_entry_form, e.booking_type , b.id as trans_id
					from inv_receive_master a, inv_transaction b, wo_yarn_dyeing_mst c,wo_yarn_dyeing_dtls d, fabric_sales_order_mst e 
					where  a.id=b.mst_id and b.pi_wo_batch_no=c.id and c.id = d.mst_id and d.job_no=e.job_no and b.item_category=1 and b.transaction_type=1 and a.entry_form=1 and a.receive_basis=2 and c.entry_form in (94,135) and a.receive_purpose in (2,12,15,38,46) and b.status_active=1 and a.status_active=1 and c.is_sales=1 and a.company_id=$cbo_company_name $date_cond $sales_order_condition2 $source_cond3
					order by e.job_no,a.receive_date asc";
					$sql_grey_dyed_yarn_2 =  sql_select($sql_grey_dyed_yarn_sql_2);

					$return_reject_qnty = 0;
					foreach ($sql_grey_dyed_yarn_2 as $rowGrey) 
					{
						if($rcvTransChk[$rowGrey[csf("trans_id")]] == "")
						{								
							$rcvTransChk[$rowGrey[csf("trans_id")]] = $rowGrey[csf("trans_id")];
							$sales_booking_no = $rowGrey[csf('sales_booking_no')];
							$return_reject_qnty = $rowGrey[csf('cons_reject_qnty')];

							$all_sales_data_arr[$rowGrey[csf('knitting_source')]][$rowGrey[csf('knitting_company')]][$rowGrey[csf('sales_order')]]['grey_dyed_yarn'].=$rowGrey[csf('system_ref')].'**'.$rowGrey[csf('sales_booking_no')].'**'.$rowGrey[csf('style_ref_no')].'**'.$rowGrey[csf('trans_date')].'**'.$rowGrey[csf('challan_no')].'**'.$rowGrey[csf('basis')].'**'.$rowGrey[csf('entry_form')].'**'.$rowGrey[csf('transaction_type')].'**'.$rowGrey[csf('prod_id')].'**'.$rowGrey[csf('receive_purpose')].'**'.$rowGrey[csf('brand_id')].'**'.$rowGrey[csf('buyer_id')].'**'.$rowGrey[csf('within_group')].'**'.$rowGrey[csf('cons_quantity')].'**'.$rowGrey[csf('item_category')].'**'.$return_reject_qnty.'**'.$rowGrey[csf('po_buyer')].'##';

							//$sales_order_ids_arr[$rowGrey[csf('sales_id')]] = $rowGrey[csf('sales_id')];
							$booking_arr[$sales_booking_no] = "'".$sales_booking_no."'";

							if($rowGrey[csf('booking_type')] == 4)
							{
								if($rowGrey[csf('booking_without_order')] == 1)
								{
									$bookingType = "Sample Without Order";
								}
								else
								{
									$bookingType =  "Sample With Order";
								}
							}
							else
							{
								$bookingType = $booking_type_ref_arr[$rowGrey[csf('booking_entry_form')]];
							}			
							$sales_booking_type[$rowGrey[csf("sales_order")]] = $bookingType; 
						}
					}

					unset($sql_grey_dyed_yarn_2);
				}

				$all_sales_ids=array_unique($sales_order_ids_arr);
				if($db_type==2 && count($all_sales_ids)>999)
				{
					$po_cond=" and (";
					$po_cond2=" and (";
					$poIdsArr=array_chunk($all_sales_ids,999);
					foreach($poIdsArr as $ids)
					{
						$ids=rtrim(implode(",",$ids),", ");
						$po_cond.=" po_breakdown_id in($ids) or"; 
						$po_cond2.=" a.po_breakdown_id in($ids) or"; 
					}
					$po_cond=chop($po_cond,'or ');
					$po_cond2=chop($po_cond2,'or ');
					$po_cond.=")";
					$po_cond2.=")";
				}
				else
				{
					$poIds=rtrim(implode(",",$all_sales_ids),", ");
					$po_cond=" and po_breakdown_id in($poIds)";
					$po_cond2=" and a.po_breakdown_id in($poIds)"; 
				}

				if(!empty($all_sales_ids))
				{
					$barcodeRejectQnty = return_library_array("select reject_qnty, barcode_no from pro_roll_details where entry_form=2 and status_active=1 $po_cond and reject_qnty>0", 'barcode_no', 'reject_qnty');
					$barcode_nos = sql_select("select a.barcode_no,b.recv_number,c.job_no,b.knitting_company,b.knitting_source,b.receive_date,c.sales_booking_no
						from pro_roll_details a,inv_receive_master b,fabric_sales_order_mst c
						where a.mst_id=b.id and a.po_breakdown_id=c.id and a.entry_form=58 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_sales=1 and b.company_id=$cbo_company_name $po_cond2 and c.status_active=1");

					$trns_date=''; $date_frm=''; $date_to='';
					$date_frm=date('Y-m-d',strtotime($from_date));
					$date_to=date('Y-m-d',strtotime($to_date));

					foreach ($barcode_nos as $barcode) 
					{
						if(($trns_date==$date_frm) && ($barcode[csf('knitting_company')] > 0))
						{
							$rejectQntyArr[$barcode[csf("recv_number")]][$barcode[csf("job_no")]]["RejectQnty"] += $barcodeRejectQnty[$barcode[csf("barcode_no")]];
						}
						$trns_date=date('Y-m-d',strtotime($barcode[csf('receive_date')]));
						if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="" && $txt_knitting_com_id !="")
						{
							if(($trns_date<$date_frm) && ($barcode[csf('knitting_company')] > 0))
							{
								$party_wise_opening[$barcode[csf('knitting_source')]][$barcode[csf('knitting_company')]]['opening_grey_reject'] += $barcodeRejectQnty[$barcode[csf("barcode_no")]];
							}
						}	
					}
				}

				$i=1;$balance=0;$opening_balance=0; 
				$tot_issue_qty=$tot_yarn_returnable=$tot_issue_return=$tot_yarn_reject=$tot_fabric_rcv=$tot_return_reject_qnty=$tot_dyed_twis_yarn =0;
				foreach ($all_sales_data_arr as $source_id =>$source_data) 
				{
					foreach ($source_data as $party_id => $party_data) 
					{
						foreach ($party_data as $sales_order => $sales_data) 
						{
							if($source_id==1) $knitting_party=$company_arr[$party_id]; 
							else if($source_id==3) $knitting_party=$supplier_arr[$party_id];
							else $knitting_party="&nbsp;";

							$yarn_issue_and_return = array_filter(explode("##",$sales_data["yarn"]));
							if( $chkParty[$knitting_party] == "")
							{
								$chkParty[$knitting_party]= $knitting_party;
								if($i>1)
								{
									?>
									<tr bgcolor="#EFEFEF">
										<td width="40"></td>
										<td width="130" align="center"></td>
										<td width="125"></td>
										<td width="80" align="center"></td>
										<td width="80"></td>
										<td width="80" align="center"></td>
										<td width="120"></td>
										<td width="80" align="center"></td>
										<td width="80" align="center"></td>
										<td width="140"></td>
										<td width="140"></td>
										<td width="90" align="center" ></td>
										<td width="360" colspan="3"><p>Party wise Total</p></td>
										<td width="60" align="right"><? echo number_format($tot_issue_qty,2);?></td>
										<td width="100" align="right"><? echo number_format($tot_yarn_returnable,2);?></td>
										<td width="100" align="right"><? echo number_format($tot_fabric_rcv,2);?></td>
										<td width="100" align="right"><? echo number_format($tot_return_reject_qnty,2);?></td>
										<td width="100" align="right"><? echo number_format($tot_dyed_twis_yarn,2);?></td>
										<td width="100" align="right"><? echo number_format($tot_issue_return,2);?></td>
										<td width="100" align="right"><? echo number_format($tot_yarn_reject,2);?></td>
										<td width="" align="right"><? echo number_format($balance,2); ?></td>   
									</tr>
									<?
									$tot_issue_qty=0;
									$tot_yarn_returnable=0;
									$tot_issue_return=0;
									$tot_yarn_reject=0;
									$tot_fabric_rcv=0;
									$tot_return_reject_qnty=0;
									$tot_dyed_twis_yarn=0;
									$balance=0;
								}

								$opening_balance = $party_wise_opening[$source_id][$party_id]['opening_issue']-($party_wise_opening[$source_id][$party_id]['opening_return']+$party_wise_opening[$source_id][$party_id]['opening_return_reject']+$party_wise_opening[$source_id][$party_id]['opening_grey']+$party_wise_opening[$source_id][$party_id]['opening_grey_reject']);
								?>
								<tr bgcolor="#EFEFEF"><td colspan="23"><b>Party name: <? echo $knitting_party; ?></b></td></tr>
								<? 
								if($opening_balance > 0)
								{
									?>
									<tr bgcolor="#EFEFEF"><td colspan="22"><b>Opening Balance </b></td>
										<td align="right">
											<? 
											echo number_format($opening_balance,2);
											?>
										</td>
									</tr>
									<?
								}
							}

							if($opening_balance > 0){
								$balance +=$opening_balance;
							}
							$opening_balance=0;

							foreach ($yarn_issue_and_return as $yarn_exp_row) 
							{
								$yarn_exp_data=array();
								$yarn_exp_data = explode("**",$yarn_exp_row);

								$system_ref=$yarn_exp_data[0]; 
								$sales_booking_no=$yarn_exp_data[1]; 
								$style_ref_no=$yarn_exp_data[2]; 
								$trans_date=$yarn_exp_data[3]; 
								$challan_no=$yarn_exp_data[4]; 
								$basis=$yarn_exp_data[5]; 
								$entry_form=$yarn_exp_data[6]; 
								$transaction_type=$yarn_exp_data[7]; 
								$prod_id=$yarn_exp_data[8]; 
								$issue_purpose=$yarn_exp_data[9];  
								$requisition_no=$yarn_exp_data[10];  
								$brand_id=$yarn_exp_data[11]; 
								$cons_quantity=$yarn_exp_data[12]; 
								$buyer_id=$yarn_exp_data[13]; 
								$within_group=$yarn_exp_data[14]; 
								$return_reject_qnty=$yarn_exp_data[15]; 
								$po_buyer=$yarn_exp_data[16]; 
								$yarn_returnable_qnty=$yarn_exp_data[17]; 

								$issue_quantity=$yarn_returnable=$issue_return=$yarn_reject = 0;
								if($transaction_type == 2){
									$issue_quantity = $cons_quantity;
									$yarn_returnable = $yarn_returnable_qnty;
									$balance += $issue_quantity;

								}else{
									$issue_return =  $cons_quantity;
									$yarn_reject = $return_reject_qnty;
									$balance -= $issue_return;
									$balance -= $return_reject_qnty;
								}

								if($within_group == 1)
								{
									$buyer_unit = $company_arr[$buyer_id];
									$buyer_name = $buyer_arr[$po_buyer];
								}else{
									$buyer_unit =  $buyer_arr[$buyer_id];
									$buyer_name="";
								}

								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>

								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="40"><? echo $i; ?></td>
									<td width="130" align="center"><? echo $sales_order; ?></td>
									<td width="125" ><? echo $sales_booking_no; ?></td>
									<td width="80" align="center"><? echo $sales_booking_type[$sales_order]; ?></td>
									<td width="80"><p ><? echo $style_ref_no; ?></p></td>
									<td width="80" align="center"><? echo change_date_format($trans_date); ?></td>
									<td width="120"><p>&nbsp;<? echo $system_ref; ?></p></td>
									<td width="80" align="center"><p><? echo $challan_no; ?>&nbsp;</p></td>
									<td width="80" align="center"><? echo $requisition_no; ?></td>
									<td width="140" ><? echo $buyer_name; ?></td>
									<td width="140" ><p><? echo $buyer_unit; ?></p></td>
									<td width="90" align="center" >
										<? echo $brand_arr[$product_arr[$prod_id]['brand']]; ?>	
									</td>
									<td width="200"><p >&nbsp;<? echo $product_arr[$prod_id]['name']; ?></p></td>
									<td width="80" ><p><? echo $product_arr[$prod_id]['lot']; ?></p></td>
									<td width="80" align="center"><? echo $yarn_issue_purpose[$issue_purpose]; ?></td>
									<td width="60" align="right"><? echo number_format($issue_quantity,2); ?></td>
									<td width="100" align="right"><? echo number_format($yarn_returnable,2); ?></td>
									<td width="100" align="right"></td>
									<td width="100" align="right"></td>
									<td width="100" align="right"></td>
									<td width="100" align="right"><? echo number_format($issue_return,2);?></td>
									<td width="100" align="right"><? echo number_format($yarn_reject,2);?></td>
									<td width="" align="right"><? echo number_format($balance,2); ?>&nbsp;</td>
								</tr>
								<?
								$i++;
								$tot_issue_qty +=$issue_quantity;
								$tot_yarn_returnable +=$yarn_returnable;
								$tot_issue_return +=$issue_return;
								$tot_yarn_reject +=$yarn_reject;
								
								$grand_tot_issue_qty +=$issue_quantity;
								$grand_tot_yarn_returnable +=$yarn_returnable;
								$grand_tot_issue_return +=$issue_return;
								$grand_tot_yarn_reject +=$yarn_reject;
							}

							$system_ref=$sales_booking_no=$style_ref_no=$trans_date="";
							$greyRcv_and_DyedYarnRcv = array();
							$greyRcv_and_DyedYarnRcv = array_filter(array_unique(explode("##",$sales_data["grey_dyed_yarn"])));

							$within_group=$item_category_id=$brand_id=$buyer_id=$cons_quantity=$return_reject_qnty=0;
							foreach ($greyRcv_and_DyedYarnRcv as $greyExpData) 
							{
								$greyExpRow=array();
								$greyExpRow = explode("**",$greyExpData);

								$system_ref=$greyExpRow[0]; 
								$sales_booking_no=$greyExpRow[1]; 
								$style_ref_no=$greyExpRow[2]; 
								$trans_date=$greyExpRow[3]; 
								$challan_no=$greyExpRow[4]; 
								$basis=$greyExpRow[5]; 
								$entry_form=$greyExpRow[6]; 
								$transaction_type=$greyExpRow[7]; 
								$prod_id=$greyExpRow[8]; 
								$cons_uom=$greyExpRow[9];   
								$brand_id=$greyExpRow[10]; 
								$buyer_id=$greyExpRow[11]; 
								$within_group=$greyExpRow[12]; 
								$cons_quantity=$greyExpRow[13]; 
								$item_category_id=$greyExpRow[14]; 
								$return_reject_qnty=$greyExpRow[15]; 

								if($within_group == 1)
								{
									$buyer_unit=$company_arr[$buyer_id]; 
									$buyer_name = $buyer_arr[$buyer_from_sales[$sales_order]];
								}else
								{
									$buyer_unit=$buyer_arr[$buyer_id]; 
									$buyer_name = "";
								}
								
								if($item_category_id == 13){
									$fabric_rcv = $cons_quantity;
								}else{
									$dyed_twis_yarn = $cons_quantity;

								}

								if($entry_form ==58)
								{
									$return_reject_qnty = $rejectQntyArr[$system_ref][$sales_order]["RejectQnty"];
									//$balance -= $return_reject_qnty;
								}

								//$balance -= $return_reject_qnty;
								$balance -= ($fabric_rcv+$dyed_twis_yarn+$return_reject_qnty);

								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td><? echo $i; ?></td>
									<td align="center"><? echo $sales_order; ?></td>
									<td><? echo $sales_booking_no; ?></td>
									<td align="center"><? echo $sales_booking_type[$sales_booking_no]; ?></td>
									<td><p><? echo $style_ref_no; ?></p></td>
									<td align="center"><p><? echo change_date_format($trans_date); ?></p></td>
									<td><p><? echo $system_ref; ?></p></td>
									<td align="center"><p><? echo $challan_no; ?></p></td>
									<td align="center">&nbsp;</td>
									<td><? echo $buyer_name; ?></td>
									<td><? echo $buyer_unit; ?></td>
									<td align="center" style="word-break:break-all;"><? echo $brand_arr[$brand_id]; ?></td>
									<td style="word-break:break-all;"><? echo $product_arr[$prod_id]['name']; ?></td>
									<td style="word-break:break-all;"><? echo $product_arr[$prod_id]['lot']; ?></td>
									<td>&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right" style="word-break:break-all;"><? echo number_format($fabric_rcv,2);?></td>
									<td align="right" style="word-break:break-all;"><? echo number_format($return_reject_qnty,2);?></td>
									<td align="right" style="word-break:break-all;"><? echo number_format($dyed_twis_yarn,2);?></td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right" style="word-break:break-all;"><? echo number_format($balance,2); ?></td>
								</tr>
								<?
								$i++;

								$tot_fabric_rcv+=$fabric_rcv;
								$tot_return_reject_qnty +=$return_reject_qnty;
								$tot_dyed_twis_yarn +=$dyed_twis_yarn;

								$grand_tot_fabric_rcv+=$fabric_rcv;
								$grand_tot_return_reject_qnty+=$return_reject_qnty;
								$grand_tot_dyed_twis_yarn +=$dyed_twis_yarn;
							}
						}
						$opening_balance=0;
						$tot_balance += $balance;
					}	
				}
				?>
				<tr bgcolor="#EFEFEF">
					<td></td>
					<td align="center"></td>
					<td></td>
					<td align="center"></td>
					<td></td>
					<td align="center"></td>
					<td></td>
					<td align="center"></td>
					<td align="center"></td>
					<td></td>
					<td></td>
					<td align="center"></td>
					<td colspan="3" align="center"><p><b>Party wise Total</b></p></td>
					<td align="right"><? echo number_format($tot_issue_qty,2);?></td>
					<td align="right"><? echo number_format($tot_yarn_returnable,2);?></td>
					<td align="right"><? echo number_format($tot_fabric_rcv,2);?></td>
					<td align="right"><? echo number_format($tot_return_reject_qnty,2);?></td>
					<td align="right"><? echo number_format($tot_dyed_twis_yarn,2);?></td>
					<td align="right"><? echo number_format($tot_issue_return,2);?></td>
					<td align="right"><? echo number_format($tot_yarn_reject,2);?></td>
					<td align="right"><? echo number_format($balance,2); ?>&nbsp;</td>

				</tr>
			</table>
		</div>

		<table width="2340" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
				<td width="40"></td>
				<td width="130" align="center"></td>
				<td width="125"></td>
				<td width="80" align="center"></td>
				<td width="80"></td>
				<td width="80" align="center"></td>
				<td width="120"></td>
				<td width="80" align="center"><p></p></td>
				<td width="80" align="center"></td>
				<td width="140"></td>
				<td width="140"></td>
				<td width="90" align="center">&nbsp;</td>
				<td width="200"><p>Grand Total</p></td>
				<td width="80"></td>
				<td width="80"></td>
				<td width="60" align="right"><? echo number_format($grand_tot_issue_qty,2);?></td>
				<td width="100" align="right"><? echo number_format($grand_tot_yarn_returnable,2);?></td>
				<td width="100" align="right"><? echo number_format($grand_tot_fabric_rcv,2);?></td>
				<td width="100" align="right"><? echo number_format($grand_tot_return_reject_qnty,2);?></td>
				<td width="100" align="right"><? echo number_format($grand_tot_dyed_twis_yarn,2);?></td>
				<td width="100" align="right"><? echo number_format($grand_tot_issue_return,2);?></td>
				<td width="100" align="right"><? echo number_format($grand_tot_yarn_reject,2);?></td>
				<td width="" align="right"><? echo number_format($tot_balance,2);?>&nbsp;</td>

			</tr>
		</table>
	</div>
	<?
	echo "Execution Time: " . (microtime(true) - $started) . "S"; // in seconds

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

if($type==7) //Sales Order Wise (Summary)
{
	if($db_type==0) $grpby_field="group by trans_id";
	if($db_type==2) $grpby_field="group by trans_id,entry_form,trans_type";
	else $grpby_field="";
	?>
	<div>
		<table width="1200" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
			<tr>
				<td align="center" width="100%" colspan="10" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
			</tr> 
			<tr>  
				<td align="center" width="100%" colspan="10" style="font-size:16px"><strong><? echo $report_title; ?> (Sales Order Wise)</strong></td>
			</tr>  
		</table>
		<table width="1200" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
			<thead>
				<th width="50">SL</th>

				<th width="200">Party Name</th>
				<th width="100">Yarn Issued</th>
				<th width="100">Yarn Returnable qty</th>

				<th width="100">Fabric Received</th>
				<th width="100">Reject Fabric Received</th>

				<th width="100">DY/TW/ WX/RCon Rec.</th>

				<th width="100">Yarn Returned</th>
				<th width="100">Reject Yarn Returned</th>
				<th width="">Balance</th> 

			</thead>
		</table>
		<div style="width:1200px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="1183" cellpadding="2" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
				<?
				$rec_qty_arr=array();
				$sql_rec= "select a.recv_number, a.recv_number_prefix_num, a.buyer_id, a.booking_no,a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.receive_basis,a.entry_form, b.id as trans_id, b.prod_id, b.pi_wo_batch_no,b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type 
				from inv_receive_master a, inv_transaction b, order_wise_pro_details c, fabric_sales_order_mst d
				where  c.entry_form in(9,22,58) and a.company_id=$cbo_company_name and a.id=b.mst_id and b.id = c.trans_id and c.po_breakdown_id = d.id and c.is_sales = 1 and b.item_category in(1,13) and b.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_basis in(1,3,4,9,10) $sales_order_condition2 $date_cond $source_cond
				union 
				select a.recv_number, a.recv_number_prefix_num, a.buyer_id, a.booking_no,a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.receive_basis,a.entry_form, b.id as trans_id, b.prod_id, b.pi_wo_batch_no,b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type 
				from inv_receive_master a, inv_transaction b , order_wise_pro_details c, fabric_sales_order_mst d
				where a.item_category in(1) and a.entry_form=1 and a.company_id=$cbo_company_name and a.id=b.mst_id and b.id = c.trans_id and c.po_breakdown_id = d.id and c.is_sales = 1  and b.item_category in(1) and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_basis in(4) and a.receive_purpose in (2,12,15,38) $sales_order_condition2 $date_cond $source_cond ";

				 //a.item_category in(1,13) and a.entry_form in(9,22,58) and

				if($cbo_knitting_source==3 || $cbo_knitting_source==0)
				{
					$sql_rec.= " union all
					select a.recv_number , a.recv_number_prefix_num,e.buyer_id,e.sales_booking_no as booking_no,e.booking_id,a.receive_date, a.item_category ,a.challan_no, a.yarn_issue_challan_no, 3 as knitting_source, a.supplier_id as knitting_company, a.receive_basis,a.entry_form,b.id as trans_id, b.prod_id, b.pi_wo_batch_no,b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type
					from inv_receive_master a, inv_transaction b, wo_yarn_dyeing_mst c,wo_yarn_dyeing_dtls d, fabric_sales_order_mst e 
					where  a.id=b.mst_id and b.pi_wo_batch_no = c.id and c.id = d.mst_id and d.job_no = e.job_no and b.item_category =1 and b.transaction_type =1 and a.entry_form =1 and a.receive_basis = 2 and c.entry_form in (94,135) and a.receive_purpose in (2,12,15,38,46) and b.status_active = 1  and c.is_sales = 1 and a.company_id=$cbo_company_name $date_cond $sales_order_condition4 $source_cond3 ";
				}
				
				$sql_rec .=  " order by knitting_source, knitting_company, recv_number_prefix_num, receive_date";

				//echo $sql_rec;die;

				$sql_rec_res=sql_select($sql_rec);
				foreach ($sql_rec_res as $rowRec ) //recv Basis 2=Independed not issue ret.
				{
					if($tranIdChk[$rowRec[csf('trans_id')]] == "")
					{
						$tranIdChk[$rowRec[csf('trans_id')]] = $rowRec[csf('trans_id')];
						$trns_date=''; $date_frm=''; $date_to='';
						$trns_date=date('Y-m-d',strtotime($rowRec[csf('receive_date')]));
						$date_frm=date('Y-m-d',strtotime($from_date));
						$date_to=date('Y-m-d',strtotime($to_date));

						if(($rowRec[csf('entry_form')]==9) || ($rowRec[csf('entry_form')]==1))
						{
							$booking_nos=$requisition_numArr2[$rowRec[csf('booking_no')]]['booking_no'];	
						}
						else
						{
							$booking_nos=$rowRec[csf('booking_no')];
						}

						
						if(($rowRec[csf('entry_form')]==9) || ($rowRec[csf('entry_form')]==1))
						{
							$all_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]]['rej_yarn']+=$rowRec[csf('cons_reject_qnty')];
							
							if(($rowRec[csf('entry_form')]==1))
							{
								$all_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]]['yRec']+=$rowRec[csf('cons_quantity')];
							}
							else
							{
								$all_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]]['ret_yarn']+=$rowRec[csf('cons_quantity')];
							}
						}
						else
						{
							$all_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]]['rej_fab']+=$rowRec[csf('cons_reject_qnty')];
							$all_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]]['fRec']+=$rowRec[csf('cons_quantity')];
						}

						$all_data_arr[$rowRec[csf('knitting_source')]][$rowRec[csf('knitting_company')]]['knitting_company']=$rowRec[csf('knitting_company')];
					}						
				}
				

				
				if (str_replace("'","",$cbo_knitting_source)==0) $knit_source_cond_party=""; else $knit_source_cond_party=" and a.knit_dye_source=$cbo_knitting_source";
				if ($knitting_company=='') $knit_company_cond_party=""; else $knit_company_cond_party=" and a.knit_dye_company in ($knitting_company)";

				$iss_qty_arr=array(); 

				$sql_iss="SELECT a.issue_number, a.issue_number_prefix_num, a.buyer_id, a.booking_no, a.issue_date, a.challan_no, a.issue_basis,a.issue_purpose, a.knit_dye_source, a.knit_dye_company, b.id as trans_id, b.prod_id, b.transaction_date,b.cons_uom, b.requisition_no, b.brand_id, b.cons_quantity, b.return_qnty from inv_issue_master a, inv_transaction b , order_wise_pro_details c where a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 $source_cond2 $sales_order_condition_PoBreakDown and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2  and b.status_active=1 and b.is_deleted=0 $date_cond and b.id = c.trans_id and c.status_active=1 and c.is_sales=1 order by a.knit_dye_source, a.knit_dye_company, a.issue_number_prefix_num, a.issue_date";

					 $sql_iss_res=sql_select($sql_iss); $tot_opening_balIssue=$tot_opening_bal_return=0;
					 foreach ($sql_iss_res as $rowIss )
					 {
					 	$trns_date=''; $date_frm=''; $date_to='';
					 	$trns_date=date('Y-m-d',strtotime($rowIss[csf('issue_date')]));
					 	$date_frm=date('Y-m-d',strtotime($from_date));
					 	$date_to=date('Y-m-d',strtotime($to_date));
					 	$issue_basis=$rowIss[csf('issue_basis')];
					 	$issue_purpose=$rowIss[csf('issue_purpose')];

					 	if(( $issue_basis ==1 ||$issue_basis == 3 || $issue_basis ==4)) /*&& ($issue_purpose ==1 || $issue_purpose ==4 || $issue_purpose ==8))*/
					 	{

					 		$all_data_arr[$rowIss[csf('knit_dye_source')]][$rowIss[csf('knit_dye_company')]]['issue']+=$rowIss[csf('cons_quantity')];
					 		$all_data_arr[$rowIss[csf('knit_dye_source')]][$rowIss[csf('knit_dye_company')]]['return']+=$rowIss[csf('return_qnty')];
					 		$all_data_arr[$rowIss[csf('knit_dye_source')]][$rowIss[csf('knit_dye_company')]]['knitting_company']=$rowIss[csf('knit_dye_company')];
					 	}

					 }

					 unset($sql_iss_res); 

					 $party_issue_qty=$party_returnable_qty=$party_rec_qty=$party_fab_rej_qty=$party_return_qty=0;
					 $party_yarn_rej_qty=$party_yarnRec_qty=0;$party_balance=0;

					 $i=1;
					 foreach ($all_data_arr as $source_id=>$source_data )
					 {
					 	foreach ($source_data as $party_id=> $data) 
					 	{
					 		if($source_id==1) $knitting_party=$company_arr[$party_id]; 
					 		else if($source_id==3) $knitting_party=$supplier_arr[$party_id];
					 		else $knitting_party="&nbsp;";	

					 		$balance=0; $return_balance=0;
					 		$issue_qty=0; 

					 		$issue_qty=$all_data_arr[$source_id][$party_id]['issue'];
					 		$yarn_returnable_qty=$all_data_arr[$source_id][$party_id]['return'];

					 		$balance=($balance+$issue_qty);

					 		$receive_qty=$all_data_arr[$source_id][$party_id]['fRec'];
					 		$fab_reject_qty=$all_data_arr[$source_id][$party_id]['rej_fab'];
					 		$yarn_reject_qty=$all_data_arr[$source_id][$party_id]['rej_yarn'];
					 		$yarnReceive_qty=$all_data_arr[$source_id][$party_id]['yRec'];
					 		$return_qty=$all_data_arr[$source_id][$party_id]['ret_yarn'];

					 		$balance=$opening_balance+$balance-($receive_qty+$fab_reject_qty+$yarn_reject_qty+$yarnReceive_qty+$return_qty);

					 		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					 		?>
					 		<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
					 			<td width="50"><? echo $i; ?></td>

					 			<td width="200"><p><? echo $knitting_party; ?></p></td>
					 			<td width="100" align="right"><? echo number_format($issue_qty,2); ?>&nbsp;</td>
					 			<td width="100" align="right"><? echo number_format($yarn_returnable_qty,2); ?></td>

					 			<td width="100" align="right"><? echo number_format($receive_qty,2); ?></td>
					 			<td width="100" align="right"><? echo number_format($fab_reject_qty,2); ?></td>

					 			<td width="100" align="right"><? echo number_format($yarnReceive_qty,2); ?></td>

					 			<td width="100" align="right"><? echo number_format($return_qty,2); ?></td>
					 			<td width="100" align="right"><? echo number_format($yarn_reject_qty,2); ?></td>
					 			<td width="" align="right"><? echo number_format($balance,2); ?></td>
					 		</tr>
					 		<?

					 		$party_issue_qty+=$issue_qty;
					 		$party_returnable_qty+=$yarn_returnable_qty;
					 		$party_rec_qty+=$receive_qty;
					 		$party_fab_rej_qty+=$fab_reject_qty;
					 		$party_yarnRec_qty+=$yarnReceive_qty;
					 		$party_return_qty+=$return_qty;
					 		$party_yarn_rej_qty+=$yarn_reject_qty;
					 		$party_balance+=$balance;
					 		$i++;
					 	}

					 }

					 ?>
					 <tr bgcolor="#CCCCCC" >
					 	<td>&nbsp;</td>
					 	<td align="right"><strong>Total &nbsp;</strong></td>
					 	<td align="right"><? echo number_format($party_issue_qty,2); ?>&nbsp;</td> 
					 	<td align="right"><? echo number_format($party_returnable_qty,2); ?></td>

					 	<td align="right"><? echo number_format($party_rec_qty,2); ?></td>
					 	<td align="right"><? echo number_format($party_fab_rej_qty,2); ?></td>

					 	<td align="right"><? echo number_format($party_yarnRec_qty,2); ?></td>

					 	<td align="right"><? echo number_format($party_return_qty,2); ?></td>
					 	<td align="right"><? echo number_format($party_yarn_rej_qty,2); ?></td>
					 	<td align="right"><? echo number_format($party_balance,2); ?></td>
					 </tr> 
					</table>
				</div>
			</div>
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

if($action=="balance_popup") //Party Wise
{

	echo load_html_head_contents("Party Wise Balance Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_arr=return_library_array( "select id, short_name from  lib_buyer", "id", "short_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	
	$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");

	if($db_type==0)
	{
		$from_date=change_date_format(str_replace("'","",$from_date),'yyyy-mm-dd');
		$to_date=change_date_format(str_replace("'","",$to_date),'yyyy-mm-dd');
	}
	elseif($db_type==2)
	{
		$from_date=change_date_format(str_replace("'","",$from_date),'','',1);
		$to_date=change_date_format(str_replace("'","",$to_date),'','',1);
	}
	//echo $from_date.'='.$to_date;
	$knitting_company=str_replace("'","",$knit_party_id);
	
	$booking_no=str_replace("'","",$booking_no);
	$hide_booking_id=str_replace("'","",$hide_booking_id);
	$hide_basis_id=str_replace("'","",$hide_basis_id);
	$txt_challan=str_replace("'","",$txt_challan);
	if($booking_no!="")
	{
		if($hide_basis_id==1)
		{
			$booking_cond="and a.booking_id in($hide_booking_id)";	
		}
		else if($hide_basis_id==3) //Requisition
		{
			$booking_cond="and b.requisition_no in($hide_booking_id)";	
		}
		else if($hide_basis_id==4) //Sales
		{
			$booking_cond="and a.booking_id in($hide_booking_id)";	
		}
	}
	//$type=str_replace("'","",$type);
	if (str_replace("'","",$source_id)==0) $knitting_source_cond=""; else $knitting_source_cond=" and a.knit_dye_source=$source_id";
	if (str_replace("'","",$cbo_knitting_source)==0) $knitting_source_rec_cond=""; else $knitting_source_rec_cond=" and a.knitting_source=$source_id";
	if ($knitting_company=='') $knitting_company_cond=""; else  $knitting_company_cond="  and a.knit_dye_company in ($knitting_company)";
	if (str_replace("'","",$internal_ref)=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping=$internal_ref";
	

	if($internal_ref_cond!="")
	{
		$jobNo=sql_select("select b.id as po_breakdown_id  from wo_po_break_down b,wo_po_details_master a where b.job_no_mst=a.job_no $internal_ref_cond");
		$allJobNo="";
		foreach($jobNo as $row)
		{
			$allJobNo.=$row[csf('po_breakdown_id')].',';
		}
		$allJobNos= chop($allJobNo,',');
		$propotionate_tbl=',order_wise_pro_details c';
		$where_cond="  and   b.id=c.trans_id and c.po_breakdown_id in ($allJobNos)"; 
	}

		//echo "selectb.id as po_breakdown_id  from wo_po_break_down b,wo_po_details_master a where b.job_no_mst=a.job_no $internal_ref_cond";
		//$all_party=explode(",",$knitting_company);
	$po_arr=array();
	$datapoArray=sql_select("select a.style_ref_no,b.id, b.po_number, b.po_quantity from wo_po_break_down b,wo_po_details_master a where b.job_no_mst=a.job_no $internal_ref_cond");


	foreach($datapoArray as $row)
	{
		$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
		$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
		$po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
	}	
	unset($datapoArray);	
	if($db_type==0) $grpby_field="group by trans_id";
	if($db_type==2) $grpby_field="group by trans_id,entry_form,trans_type";
	else $grpby_field="";

	if (str_replace("'","",$txt_challan)=="") $challan_cond=""; else $challan_cond=" and a.issue_number_prefix_num=$txt_challan";
	
	$order_nos_array=array();
	if($db_type==0)
	{
		$datapropArray=sql_select("select trans_id,
			CASE WHEN entry_form='3' and trans_type=2 THEN group_concat(po_breakdown_id) END AS yarn_order_id,
			CASE WHEN entry_form in (2,22) and trans_type=1 THEN group_concat(po_breakdown_id) END AS grey_order_id,
			CASE WHEN entry_form='9' and trans_type=4 THEN group_concat(po_breakdown_id) END AS yarn_return_order_id 
			from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (2,3,9,22) and status_active=1 and is_deleted=0 group by trans_id");
	}
	else
	{
		$datapropArray=sql_select("select trans_id,
			listagg(CASE WHEN entry_form='3' and trans_type=2 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS yarn_order_id,
			listagg(CASE WHEN entry_form in (2,22) and trans_type=1 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS grey_order_id,
			listagg(CASE WHEN entry_form='9' and trans_type=4 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS yarn_return_order_id 
			from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (2,3,9,22) and status_active=1 and is_deleted=0 group by trans_id,entry_form,trans_type");
	}

	foreach($datapropArray as $row)
	{
		$order_nos_array[$row[csf('trans_id')]]['yarn_issue']=$row[csf('yarn_order_id')];
		$order_nos_array[$row[csf('trans_id')]]['grey_recv']=$row[csf('grey_order_id')];
		$order_nos_array[$row[csf('trans_id')]]['yarn_return']=$row[csf('yarn_return_order_id')];
	}	
	unset($datapropArray);

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
	<div>
		<div style="width:860px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<div id="report_container">
			<table width="2350" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
				<tr>
					<td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$com_id)]; ?></strong></td>
				</tr> 
				<tr>  
					<td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo 'Balance Details'; ?> </strong></td>
				</tr>  
				<tr> 
					<td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo "From ".change_date_format(str_replace("'","",$from_date))." To ".change_date_format(str_replace("'","",$to_date)); ?></strong></td>
				</tr>
			</table>
			<table width="2350" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th width="40">SL</th>
					<th width="80">Date</th>
					<th width="125">Transaction Ref.</th>
					<th width="115">Recv. Challan No</th>
					<th width="115">Issue Challan No</th>
					<th width="130">Booking/Reqsn. No</th>
					<th width="80">Buyer</th>
					<th width="130">Style Ref.</th>
					<th width="130">Order Numbers</th>
					<th width="90">Order Qnty.</th>
					<th width="80">Brand</th>
					<th width="150">Item Description</th>
					<th width="80">Lot</th>
					<th width="60">UOM</th>
					<th width="100">Yarn Issued</th>
					<th width="100">Returnable Qty.</th>
					<th width="100">Fabric Received</th>
					<th width="100">Reject Fabric Received</th>
					<th width="100">DY/TW/ WX/RCon Rec.</th>
					<th width="100">Yarn Returned</th>
					<th width="100">Reject Yarn Returned</th>
					<th width="100">Balance</th> 
					<th>Returnable Balance</th>
				</thead>
			</table>
			<div style="width:2350px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="2330" cellpadding="2" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
					<?
					$product_arr=array();
					$sql_prod="select id, product_name_details, lot from product_details_master where item_category_id in (1,2,22) ";
					$sql_prod_res=sql_select($sql_prod);
					foreach($sql_prod_res as $rowp)
					{
						$product_arr[$rowp[csf('id')]]['name']=$rowp[csf('product_name_details')];
						$product_arr[$rowp[csf('id')]]['lot']=$rowp[csf('lot')];
					}
					unset($sql_prod_res);
					
					if (str_replace("'","",$source_id)==0) $knit_source_cond=""; else $knit_source_cond=" and a.knitting_source=$source_id";
					if ($knitting_company=='') $knit_company_cond=""; else $knit_company_cond=" and a.knitting_company in ($knitting_company)";
					if ($knitting_company=='') $party_cond=""; else  $party_cond="  and a.supplier_id in ($knitting_company)";
					$yarnRec_qty_arr=array(); $all_data_arr=array();
					if (str_replace("'","",$source_id)==3)
					{
						$sql_yrec="select a.recv_number, a.recv_number_prefix_num, a.buyer_id, a.booking_no, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no,  a.knitting_source, a.supplier_id, a.receive_basis, a.entry_form, b.id as trans_id, b.prod_id, b.cons_uom,  b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty 
						from inv_receive_master a, inv_transaction b $propotionate_tbl where a.item_category=1 and a.entry_form=1 and a.company_id=$com_id and a.receive_purpose in (2,12,15,38) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
						$party_cond $issue_challan_cond $where_cond 
						
						order by a.supplier_id, a.yarn_issue_challan_no, b.transaction_type, a.receive_date ";
						//echo $sql_yrec; die;
						$sql_yRec_res=sql_select($sql_yrec);
						foreach ($sql_yRec_res as $rowyRec )
						{
							$trns_date=''; $date_frm=''; $date_to='';
							$trns_date=date('Y-m-d',strtotime($rowyRec[csf('receive_date')]));
							$date_frm=date('Y-m-d',strtotime($from_date));
							$date_to=date('Y-m-d',strtotime($to_date));
							//echo $trns_date.'--'.$date_frm;// die;
							$opening_balRec=0;
							if($trns_date<$date_frm)
							{
								 $opening_balRec=$rowyRec[csf('cons_quantity')];//+$rowyRec[csf('return_qnty')]+$rowyRec[csf('cons_reject_qnty')];
								 $yarnRec_qty_arr[$rowyRec[csf('supplier_id')]]['0']['0']['opening_bal_yRec']+=$opening_balRec;
								}
							//if($trns_date>=$date_frm && $trns_date<=$date_to)
							//echo $trns_date.'>='.'&&'.$trns_date.'<='.$date_to;
								if($trns_date>=$date_frm && $trns_date<=$date_to)
								{
								//echo $rowyRec[csf('cons_quantity')].'dddd';
									$yarnRec_qty_arr[$rowyRec[csf('supplier_id')]][$rowyRec[csf('trans_id')]][$rowyRec[csf('prod_id')]]['yRec']+=$rowyRec[csf('cons_quantity')];

									$all_data_arr[$rowyRec[csf('supplier_id')]]['yrec'].=$rowyRec[csf('recv_number')].'!!'.$rowyRec[csf('recv_number_prefix_num')].'!!'.$rowyRec[csf('buyer_id')].'!!'.$rowyRec[csf('booking_no')].'!!'.$rowyRec[csf('receive_date')].'!!'.$rowyRec[csf('challan_no')].'!!'.$rowyRec[csf('receive_basis')].'!!'.$rowyRec[csf('knitting_source')].'!!'.$rowyRec[csf('trans_id')].'!!'.$rowyRec[csf('prod_id')].'!!'.$rowyRec[csf('cons_uom')].'!!'.$rowyRec[csf('brand_id')].'!!'.$rowyRec[csf('yarn_issue_challan_no')].'!!'.$rowyRec[csf('item_category')].'___';
								}
							}
							unset($sql_yRec_res);
						}
					//print_r($all_data_arr[117]);


						$rec_qty_arr=array();
						$sql_rec="select a.recv_number, a.recv_number_prefix_num, a.buyer_id, a.booking_no, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no,  a.knitting_source, a.knitting_company, a.receive_basis, a.entry_form, b.id as trans_id, b.prod_id, b.cons_uom,  b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type 
						from inv_receive_master a, inv_transaction b $propotionate_tbl 
						where a.item_category in(1,13) and a.entry_form in(2,9,22) and a.company_id=$com_id and a.id=b.mst_id and b.item_category in(1,13) and b.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
						$knit_source_cond $knit_company_cond $issue_challan_cond $where_cond  

						order by a.knitting_company, a.yarn_issue_challan_no, b.transaction_type, a.receive_date ";
					//echo $sql_rec; die;
						$sql_rec_res=sql_select($sql_rec);$tot_opening_bal_rej_yarn=$tot_bal_opening_balfRec=$tot_opening_bal_ret_yarn=0;
						foreach ($sql_rec_res as $rowRec )
						{
							$trns_date=''; $date_frm=''; $date_to='';
							$trns_date=date('Y-m-d',strtotime($rowRec[csf('receive_date')]));
							$date_frm=date('Y-m-d',strtotime($from_date));
							$date_to=date('Y-m-d',strtotime($to_date));


							if($trns_date<$date_frm)
							{
							//echo $trns_date.'<'.$date_frm.', '; 
								$opening_balRec=0;
							//rowRec
								if($rowRec[csf('entry_form')]==9)
								{
									$return_qnty=$rowRec[csf('cons_quantity')];	
								//$rec_qty_arr[$rowRec[csf('knitting_company')]]['0']['0']['opening_bal_ret_yarn']+=$return_qnty;
								}
							$opening_balRec=$rowRec[csf('cons_quantity')]-$return_qnty;//+$rowRec[csf('return_qnty')]+$rowRec[csf('cons_reject_qnty')]
							//echo $opening_balRec.', '; 
							//echo $rowRec[csf('return_qnty')].'d';
							$tot_opening_bal_rej_yarn+=$rowRec[csf('cons_reject_qnty')];
							$tot_bal_opening_balfRec+=$opening_balRec;	
							$tot_opening_bal_ret_yarn+=$return_qnty;	

							$rec_qty_arr[$rowRec[csf('knitting_company')]]['0']['0']['opening_bal_fRec']+=$opening_balRec;
							//$yarnRec_qty_arr[$rowRec[csf('knitting_company')]]['0']['0']['opening_bal_yRec']+=$rowRec[csf('cons_quantity')];
							$rec_qty_arr[$rowRec[csf('knitting_company')]]['0']['0']['opening_bal_rej_yarn']+=$rowRec[csf('cons_reject_qnty')];
							$rec_qty_arr[$rowRec[csf('knitting_company')]]['0']['0']['opening_bal_ret_yarn']+=$return_qnty;
						}
						//print_r($rec_qty_arr);
						if($trns_date>=$date_frm && $trns_date<=$date_to)
						{
							//echo $rowRec[csf('entry_form')].'mmm';
							
							if($rowRec[csf('entry_form')]==9)
							{
								$rec_qty_arr[$rowRec[csf('knitting_company')]][$rowRec[csf('trans_id')]][$rowRec[csf('prod_id')]]['ret_po']=$order_nos_array[$rowRec[csf('trans_id')]]['yarn_return'];
								//echo $rowyRec[csf('cons_quantity')].'sss';
								$rec_qty_arr[$rowRec[csf('knitting_company')]][$rowRec[csf('trans_id')]][$rowRec[csf('prod_id')]]['rej_yarn']+=$rowRec[csf('cons_reject_qnty')];
								$rec_qty_arr[$rowRec[csf('knitting_company')]][$rowRec[csf('trans_id')]][$rowRec[csf('prod_id')]]['ret_yarn']+=$rowRec[csf('cons_quantity')];
								$yarnRec_qty_arr[$rowRec[csf('knitting_company')]][$rowRec[csf('trans_id')]][$rowRec[csf('prod_id')]]['yRec']+=$rowRec[csf('cons_quantity')];
								
							}
							else
							{
								$rec_qty_arr[$rowRec[csf('knitting_company')]][$rowRec[csf('trans_id')]][$rowRec[csf('prod_id')]]['rej_fab']+=$rowRec[csf('cons_reject_qnty')];
								$rec_qty_arr[$rowRec[csf('knitting_company')]][$rowRec[csf('trans_id')]][$rowRec[csf('prod_id')]]['fRec']+=$rowRec[csf('cons_quantity')];
							}
							
							$all_data_arr[$rowRec[csf('knitting_company')]]['rec'].=$rowRec[csf('recv_number')].'**'.$rowRec[csf('recv_number_prefix_num')].'**'.$rowRec[csf('buyer_id')].'**'.$rowRec[csf('booking_no')].'**'.$rowRec[csf('receive_date')].'**'.$rowRec[csf('challan_no')].'**'.$rowRec[csf('receive_basis')].'**'.$rowRec[csf('knitting_source')].'**'.$rowRec[csf('trans_id')].'**'.$rowRec[csf('prod_id')].'**'.$rowRec[csf('cons_uom')].'**'.$rowRec[csf('brand_id')].'**'.$rowRec[csf('yarn_issue_challan_no')].'**'.$rowRec[csf('item_category')].'___';
						}
					}
					unset($sql_rec_res);
					if (str_replace("'","",$source_id)==0) $knit_source_cond_party=""; else $knit_source_cond_party=" and a.knit_dye_source=$source_id";
					if ($knitting_company=='') $knit_company_cond_party=""; else $knit_company_cond_party=" and a.knit_dye_company in ($knitting_company)";
					$iss_qty_arr=array(); 
					$sql_iss="select a.issue_number, a.issue_number_prefix_num, a.buyer_id, a.booking_no, a.issue_date, a.challan_no, a.issue_basis, a.knit_dye_source, a.knit_dye_company, b.id as trans_id, b.prod_id, b.transaction_date, b.cons_uom, b.requisition_no, b.brand_id, b.cons_quantity, b.return_qnty
					
					from inv_issue_master a, inv_transaction b  $propotionate_tbl 
					where a.item_category=1 and a.entry_form=3 and a.company_id=$com_id and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					$knit_source_cond_party $knit_company_cond_party $challan_cond $where_cond   $booking_cond
					order by a.knit_dye_company, a.issue_number_prefix_num, a.issue_date";
					//echo $sql_iss;die;
					$sql_iss_res=sql_select($sql_iss); $tot_opening_balIssue=$tot_opening_bal_return=0;
					foreach ($sql_iss_res as $rowIss )
					{
						$trns_date=''; $date_frm=''; $date_to='';
						$trns_date=date('Y-m-d',strtotime($rowIss[csf('issue_date')]));
						$date_frm=date('Y-m-d',strtotime($from_date));
						$date_to=date('Y-m-d',strtotime($to_date));
						//echo $trns_date.'--'.$date_frm.'--'.$date_to.'<br>'; 
						$opening_balIssue=0;
						if($trns_date<$date_frm)
						{
							//echo $trns_date.'--'.$date_frm.'--'.$date_to.'<br>'; 
							 $opening_balIssue=$rowIss[csf('cons_quantity')];//+$rowIss[csf('return_qnty')];
							 $tot_opening_balIssue+=$opening_balIssue;
							 $tot_opening_bal_return+=$rowIss[csf('return_qnty')];
							 
							 $iss_qty_arr[$rowIss[csf('knit_dye_company')]]['0']['0']['opening_bal']+=$opening_balIssue;
							 $iss_qty_arr[$rowIss[csf('knit_dye_company')]]['0']['0']['opening_bal_return']+=$rowIss[csf('return_qnty')];
							//echo  $opening_balIssue.'<br>';
							}

							if($trns_date>=$date_frm && $trns_date<=$date_to)
							{
								$iss_qty_arr[$rowIss[csf('knit_dye_company')]][$rowIss[csf('trans_id')]][$rowIss[csf('prod_id')]]['issue']+=$rowIss[csf('cons_quantity')];
								$iss_qty_arr[$rowIss[csf('knit_dye_company')]][$rowIss[csf('trans_id')]][$rowIss[csf('prod_id')]]['return']+=$rowIss[csf('return_qnty')];

								$all_data_arr[$rowIss[csf('knit_dye_company')]]['iss'].=$rowIss[csf('issue_number')].'**'.$rowIss[csf('issue_number_prefix_num')].'**'.$rowIss[csf('buyer_id')].'**'.$rowIss[csf('booking_no')].'**'.$rowIss[csf('issue_date')].'**'.$rowIss[csf('challan_no')].'**'.$rowIss[csf('issue_basis')].'**'.$rowIss[csf('knit_dye_source')].'**'.$rowIss[csf('trans_id')].'**'.$rowIss[csf('prod_id')].'**'.$rowIss[csf('cons_uom')].'**'.$rowIss[csf('requisition_no')].'**'.$rowIss[csf('brand_id')].'___';
							}
						}
					//die;

				//	print_r($iss_qty_arr);
						$empty_opening_balance=$tot_opening_balIssue-($tot_bal_opening_balfRec+$tot_opening_bal_rej_yarn+$tot_opening_bal_ret_yarn);
						$empty_retable_opening_balance=$tot_opening_bal_return-$tot_opening_bal_ret_yarn;
						unset($sql_iss_res); $i=1;
						if(empty($all_data_arr)) 
						{
							echo "<tr>
							<td width='2230' colspan='22' align='right'> ".number_format($empty_opening_balance,2)."</td>
							<td  align='right' width='100'>  ".number_format($empty_retable_opening_balance,2)."</td>
							</tr>";	
						}

						foreach ($all_data_arr as $party_id=>$party_data )
						{


							if(str_replace("'","",$source_id)==1) $knitting_party=$company_arr[$party_id]; 
							else if(str_replace("'","",$source_id)==3) $knitting_party=$supplier_arr[$party_id];
							else $knitting_party="&nbsp;";	
							$balance=0; $return_balance=0;
							$party_issue_qty=$party_returnable_qty=$party_rec_qty=$party_fab_rej_qty=$party_return_qty=$party_yarn_rej_qty=$party_yarnRec_qty=0;
						//echo $rec_qty_arr[$party_id]['0']['0']['opening_bal_fRec'].'<br>';

						//$opening_balance=$open_issue_qty-$returnable_qty;
						//$rec_qty_arr[$rowRec[csf('knitting_company')]]['0']['0']['opening_bal_fRec']
						$opening_bal_ret_yarn=$rec_qty_arr[$party_id]['0']['0']['opening_bal_ret_yarn'];//$rec_qty_arr[$rowRec[csf('knitting_company')]]['0']['0']['opening_bal_return_yarn']
						$opening_bal_return=$iss_qty_arr[$party_id]['0']['0']['opening_bal_return'];
						
						$opening_bal_fRec=$rec_qty_arr[$party_id]['0']['0']['opening_bal_fRec'];
						  //$opening_bal_yRec=$rec_qty_arr[$party_id]['0']['0']['opening_bal_yRec'];
						   //$opening_bal=$rec_qty_arr[$party_id]['0']['0']['opening_bal'];
						$opening_bal_issue= $iss_qty_arr[$party_id]['0']['0']['opening_bal'];
						   //echo $party_id.'='.$opening_bal_issue;
						$opening_bal_yRec_dye_twist= $yarnRec_qty_arr[$party_id]['0']['0']['opening_bal_yRec']; 
						$opening_bal_rej_yarn= $rec_qty_arr[$party_id]['0']['0']['opening_bal_rej_yarn'];
						  //$rec_qty_arr[$rowyRec[csf('knitting_company')]]['0']['0']['opening_bal_rej_yarn']
						$retable_opening_balance=$opening_bal_return-$opening_bal_ret_yarn;
						$opening_balance=$opening_bal_issue-($opening_bal_fRec+$opening_bal_rej_yarn+$opening_bal_ret_yarn+$opening_bal_yRec_dye_twist);
						//echo $opening_bal_issue.'='.$opening_bal_fRec.'=-'.$opening_bal_ret_yarn;
						//$opening_balance=$iss_qty_arr[$party_id]['0']['0']['opening_bal']-($rec_qty_arr[$party_id]['0']['0']['opening_bal_fRec']+$yarnRec_qty_arr[$party_id]['0']['0']['opening_bal_yRec']);
						?><tr bgcolor="#EFEFEF"><td colspan="23"><b>Party name: <? echo $knitting_party; ?></b></td></tr>
						<tr bgcolor="#EFEFEF"><td colspan="21" align="left"><b>Opening Balance: <? echo change_date_format($from_date); ?></b></td><td align="right"><? echo number_format($opening_balance,2); ?></b></td><td  align="right"><? echo number_format($retable_opening_balance,2);?></td></tr>
						<?
						//Issue Data
						$ex_partyIssData='';
						$ex_partyIssData=array_filter(array_unique(explode('___',$party_data['iss']))); 
						foreach($ex_partyIssData as $dataIss)
						{
							$ex_data_iss='';
							$ex_data_iss=explode('**',$dataIss);
							
							
							$iss_num=''; $iss_no_pre=''; $buyer_id=''; $booking_no=''; $iss_date=''; $challan_no=''; $iss_basis=''; $party_source=''; $trns_id=''; $prod_id=''; $cons_uom=''; $req_no=''; $brand_id=''; $opening_balance_issue=0; $issue_qty=0; $returnable_qty=0;
							$iss_num=$ex_data_iss[0]; 
							$iss_no_pre=$ex_data_iss[1]; 
							$buyer_id=$ex_data_iss[2]; 
							$booking_no=$ex_data_iss[3]; 
							$iss_date=$ex_data_iss[4]; 
							$challan_no=$ex_data_iss[5]; 
							$iss_basis=$ex_data_iss[6]; 
							$party_source=$ex_data_iss[7]; 
							$trns_id=$ex_data_iss[8]; 
							$prod_id=$ex_data_iss[9]; 
							$cons_uom=$ex_data_iss[10];  
							$req_no=$ex_data_iss[11]; 
							$brand_id=$ex_data_iss[12];
							
							$issue_qty=$iss_qty_arr[$party_id][$trns_id][$prod_id]['issue'];
							$returnable_qty=$iss_qty_arr[$party_id][$trns_id][$prod_id]['return'];
							//$iss_qty_arr[$rowIss[csf('knit_dye_company')]][$rowIss[csf('trans_id')]][$rowIss[csf('prod_id')]]['return']
							
							
							$balance=($opening_balance+$balance+$issue_qty);
							$opening_balance=0;
							//$balance=$val;
							$return_balance=$return_balance+$returnable_qty; 

							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($iss_basis==1)
								$booking_reqsn_no=$booking_no;
							else if($iss_basis==3)
								$booking_reqsn_no=$req_no;
							else
								$booking_reqsn_no="&nbsp;";

							$all_po_id_ret=array_unique(explode(",",$order_nos_array[$trns_id]['yarn_return']));
							//echo $order_nos_array[$trns_id]['yarn_return'].'<br>';
							$all_po_id=explode(",",$order_nos_array[$trns_id]['yarn_issue']);
							if($order_nos_array[$trns_id]['yarn_issue']!="")
							{
								foreach($all_po_id as $po_id) //$po_arr[$row[csf('id')]]['style']
								{
									if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
									if($style_ref=='') $style_ref=$po_arr[$po_id]['style']; else $style_ref.=",".$po_arr[$po_id]['style'];
									$order_qnty+=$po_arr[$po_id]['qnty'];
								}
							}
							else
							{
								//echo  $po_id.'DD';
								foreach($all_po_id_ret as $po_id) //$po_arr[$row[csf('id')]]['style']
								{
									if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
									if($style_ref=='') $style_ref=$po_arr[$po_id]['style']; else $style_ref.=",".$po_arr[$po_id]['style'];
									$order_qnty+=$po_arr[$po_id]['qnty'];
									
								}
							}
							$styles_ref=implode(",",array_unique(explode(",",$style_ref)));
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="40"><? echo $i; ?></td>
								<td width="80" align="center"><? echo change_date_format($iss_date); ?></td>
								<td width="125"><div style="word-wrap:break-word; width:125px"><? echo $iss_num; ?></div></td>
								<td width="115">&nbsp;</td>
								<td width="115"><p>&nbsp;<? echo $challan_no; ?></p></td>
								<td width="130"><p>&nbsp;<? echo $booking_reqsn_no; ?></p></td>
								<td width="80"><p><? echo $buyer_arr[$buyer_id]; ?>&nbsp;</p></td>
								<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $styles_ref; ?></div></td>
								<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $order_nos; ?></div></td>
								<td width="90" align="right"><? echo number_format($order_qnty,0,'.',''); ?></td>
								<td width="80"><p>&nbsp;<? echo $brand_arr[$brand_id]; ?></p></td>
								<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $product_arr[$prod_id]['name']; ?></div></td>
								<td width="80"><p><? echo $product_arr[$prod_id]['lot']; ?></p></td>
								<td width="60" align="center"><? echo $unit_of_measurement[$cons_uom]; ?>&nbsp;</td>
								<td width="100" align="right"><? echo number_format($issue_qty,2); ?></td>
								<td width="100" align="right"><? echo number_format($returnable_qty,2); ?></td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right"></td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right"><? echo number_format($balance,2); ?></td>
								<td align="right"><? echo number_format($return_balance,2);//$return_balance=0; ?></td>
							</tr>
							<?
							
							$party_issue_qty+=$issue_qty;
							$tot_issue_qty+=$issue_qty;
							$party_returnable_qty+=$returnable_qty;
							$tot_returnable_qty+=$returnable_qty;
							$i++;
						}
						// Receive Data
						$ex_partyRecData='';
						$ex_partyRecData=array_filter(array_unique(explode('___',$party_data['rec']))); 
						foreach($ex_partyRecData as $dataRec)
						{
							$ex_data_rec='';
							$ex_data_rec=explode('**',$dataRec);
							
							$rec_num=''; $rec_num_pre=''; $buyer_id=''; $booking_no=''; $rec_date=''; $challan_no=''; $rec_basis=''; $party_source=''; $trns_id=''; $prod_id=''; $cons_uom=''; $yarn_iss_challlan=''; $brand_id=''; $item_category=''; $receive_qty=0; $return_qty=0; $fab_reject_qty=0; $yarn_reject_qty=0; $yarnReceive_qty=0;
							$rec_num=$ex_data_rec[0]; 
							$rec_num_pre=$ex_data_rec[1]; 
							$buyer_id=$ex_data_rec[2]; 
							$booking_no=$ex_data_rec[3]; 
							$rec_date=$ex_data_rec[4]; 
							$challan_no=$ex_data_rec[5]; 
							$rec_basis=$ex_data_rec[6]; 
							$party_source=$ex_data_rec[7]; 
							$trns_id=$ex_data_rec[8]; 
							$prod_id=$ex_data_rec[9]; 
							$cons_uom=$ex_data_rec[10];  
							$brand_id=$ex_data_rec[11];
							$yarn_iss_challlan=$ex_data_rec[12]; 
							$item_category=$ex_data_rec[13]; 
							
							
							$receive_qty=$rec_qty_arr[$party_id][$trns_id][$prod_id]['fRec'];
							$yarnReceive_qty=$rec_qty_arr[$party_id][$trns_id][$prod_id]['yRec'];
							$return_qty=$rec_qty_arr[$party_id][$trns_id][$prod_id]['ret_yarn'];
							$fab_reject_qty=$rec_qty_arr[$party_id][$trns_id][$prod_id]['rej_fab'];
							$yarn_reject_qty=$rec_qty_arr[$party_id][$trns_id][$prod_id]['rej_yarn'];
							
							
							$balance=$opening_balance+$balance-($receive_qty+$yarnReceive_qty+$fab_reject_qty+$yarn_reject_qty+$return_qty);
							$opening_balance=0;
							$return_balance=$return_balance-($return_qty+$yarn_reject_qty); 
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($iss_basis==1)
								$booking_reqsn_no=$booking_no;
							else if($iss_basis==3)
								$booking_reqsn_no=$req_no;
							else
								$booking_reqsn_no="&nbsp;";

							$all_po_id='';
							if($item_category==13){
								$all_po_id=explode(",",$order_nos_array[$trns_id]['grey_recv']);
								$all_poid=$order_nos_array[$trns_id]['grey_recv'];
								
							}
							else {
								$all_po_id=explode(",",$order_nos_array[$trns_id]['yarn_issue']);
								$all_poid=$order_nos_array[$trns_id]['yarn_issue'];
								
							}

							$all_po_id_ret=explode(",",$rec_qty_arr[$party_id][$trns_id][$prod_id]['ret_po']);

							$order_nos=''; $style_ref=''; $order_qty=0;$po_idss='';
							if($all_poid!='')
							{
								foreach($all_po_id as $po_id)
								{
									if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
									if($style_ref=='') $style_ref=$po_arr[$po_id]['style']; else $style_ref.=",".$po_arr[$po_id]['style'];
									$order_qnty+=$po_arr[$po_id]['qnty'];
									//$po_idss.=$po_id.',';
								}
							}
							else
							{
								//echo $rec_qty_arr[$party_id][$trns_id][$prod_id]['ret_po'].'fff';
								$order_qnty=0;
								foreach($all_po_id_ret as $po_id)
								{
									if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
									if($style_ref=='') $style_ref=$po_arr[$po_id]['style']; else $style_ref.=",".$po_arr[$po_id]['style'];
									$order_qnty+=$po_arr[$po_id]['qnty'];
									//$po_idss.=$po_id.',';
								}
							}
							$styles_ref=implode(",",array_unique(explode(",",$style_ref)));	
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="40"><? echo $i; ?></td>
								<td width="80" align="center"><? echo change_date_format($rec_date); ?></td>
								<td width="125"><div style="word-wrap:break-word; width:125px"><? echo $rec_num; ?></div></td>
								<td width="115"><p>&nbsp;<? echo $challan_no; ?></p></td>
								<td width="115"><p>&nbsp;<? echo $yarn_iss_challlan; ?></p></td>
								<td width="130"><p>&nbsp;<? echo $booking_no; ?></p></td>
								<td width="80"><p><? echo $buyer_arr[$buyer_id]; ?>&nbsp;</p></td>
								<td width="130"><div style="word-wrap:break-word; width:130px"><? $styles_ref; ?></div></td>
								<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $order_nos; ?></div></td>
								<td width="90" align="right"><? echo number_format($order_qnty,0); ?></td>
								<td width="80"><p>&nbsp;<? echo $brand_arr[$brand_id]; ?></p></td>
								<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $product_arr[$prod_id]['name']; ?></div></td>
								<td width="80"><p><? echo $product_arr[$prod_id]['lot']; ?></p></td>
								<td width="60" align="center"><? echo $unit_of_measurement[$cons_uom]; ?>&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right"><? echo number_format($receive_qty,2); ?></td>
								<td width="100" align="right"><? echo number_format($fab_reject_qty,2); ?></td>
								<td width="100" align="right"><? echo number_format($yarnReceive_qty,2); ?></td>
								<td width="100" align="right"><? echo number_format($return_qty,2); ?></td>
								<td width="100" align="right"><? echo number_format($yarn_reject_qty,2); ?></td>
								<td width="100" align="right"><? echo number_format($balance,2); ?></td>
								<td align="right"><? echo number_format($return_balance,2); ?></td>
							</tr>
							<?
							//$party_issue_qty=$party_returnable_qty=$party_rec_qty=$party_fab_rej_qty=$party_return_qty=$party_yarn_rej_qty
							$party_rec_qty+=$receive_qty;
							$tot_rec_qty+=$receive_qty;
							$party_fab_rej_qty+=$fab_reject_qty;
							$tot_fab_rej_qty+=$fab_reject_qty;
							$party_yarnRec_qty+=$yarnReceive_qty;
							$tot_yarnRec_qty+=$yarnReceive_qty;
							$party_return_qty+=$return_qty;
							$tot_return_qty+=$return_qty;
							$party_yarn_rej_qty+=$yarn_reject_qty;
							$tot_yarn_rej_qty+=$yarn_reject_qty;
							$i++;
						}
						
						$ex_partyyRecData='';
						$ex_partyyRecData=array_filter(array_unique(explode('___',$party_data['yrec']))); 
						foreach($ex_partyyRecData as $datayRec)
						{
							//echo $datayRec.'==<br>';
							$ex_data_yrec='';
							$ex_data_yrec=explode('!!',$datayRec);
							
							$rec_num=''; $rec_num_pre=''; $buyer_id=''; $booking_no=''; $rec_date=''; $challan_no=''; $rec_basis=''; $party_source=''; $trns_id=''; $prod_id=''; $cons_uom=''; $yarn_iss_challlan=''; $brand_id=''; $item_category=''; $receive_qty=0; $return_qty=0; $fab_reject_qty=0; $yarn_reject_qty=0; $yarnReceive_qty=0;
							$rec_num=$ex_data_yrec[0]; 
							$rec_num_pre=$ex_data_yrec[1]; 
							$buyer_id=$ex_data_yrec[2]; 
							$booking_no=$ex_data_yrec[3]; 
							$rec_date=$ex_data_yrec[4]; 
							$challan_no=$ex_data_yrec[5]; 
							$rec_basis=$ex_data_yrec[6]; 
							$party_source=$ex_data_yrec[7]; 
							$trns_id=$ex_data_yrec[8]; 
							$prod_id=$ex_data_yrec[9]; 
							$cons_uom=$ex_data_yrec[10];  
							$brand_id=$ex_data_yrec[11];
							$yarn_iss_challlan=$ex_data_yrec[12]; 
							$item_category=$ex_data_yrec[13]; 
							
							$yarnReceive_qty=$yarnRec_qty_arr[$party_id][$trns_id][$prod_id]['yRec'];
							/*$receive_qty=$yarnRec_qty_arr[$party_id][$trns_id][$prod_id]['yRec'];
							
							$return_qty=$rec_qty_arr[$party_id][$trns_id][$prod_id]['return'];
							$fab_reject_qty=$rec_qty_arr[$party_id][$trns_id][$prod_id]['rej_fab'];
							$yarn_reject_qty=$rec_qty_arr[$party_id][$trns_id][$prod_id]['rej_yarn'];*/
							$return_qty=$rec_qty_arr[$party_id][$trns_id][$prod_id]['ret_yarn'];
							$balance=$opening_balance+$balance-($yarnReceive_qty);
							$opening_balance=0;
							$return_balance=$return_balance; 
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($iss_basis==1)
								$booking_reqsn_no=$booking_no;
							else if($iss_basis==3)
								$booking_reqsn_no=$req_no;
							else
								$booking_reqsn_no="&nbsp;";

							$all_po_id='';
							if($item_category==13)
								$all_po_id=explode(",",$order_nos_array[$trns_id]['grey_recv']);
							else
								$all_po_id=explode(",",$order_nos_array[$trns_id]['yarn_issue']);
							$order_nos='';$style_ref=''; $order_qty=0;
							foreach($all_po_id as $po_id)
							{
								if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
								if($style_ref=='') $style_ref=$po_arr[$po_id]['style']; else $style_ref.=",".$po_arr[$po_id]['style'];
								$order_qnty+=$po_arr[$po_id]['qnty'];
							}	
							$styles_ref=implode(",",array_unique(explode(",",$style_ref)));	
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="40"><? echo $i; ?></td>
								<td width="80" align="center"><? echo change_date_format($rec_date); ?></td>
								<td width="125"><div style="word-wrap:break-word; width:125px"><? echo $rec_num; ?></div></td>
								<td width="115"><p>&nbsp;<? echo $challan_no; ?></p></td>
								<td width="115"><p>&nbsp;<? echo $yarn_iss_challlan; ?></p></td>
								<td width="130"><p>&nbsp;<? echo $booking_no; ?></p></td>
								<td width="80"><p><? echo $buyer_arr[$buyer_id]; ?>&nbsp;</p></td>
								<td width="130"><div style="word-wrap:break-word; width:130px"><? $styles_ref; ?></div></td>
								<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $order_nos; ?></div></td>
								<td width="90" align="right"><? echo number_format($order_qnty,0); ?></td>
								<td width="80"><p>&nbsp;<? echo $brand_arr[$brand_id]; ?></p></td>
								<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $product_arr[$prod_id]['name']; ?></div></td>
								<td width="80"><p><? echo $product_arr[$prod_id]['lot']; ?></p></td>
								<td width="60" align="center"><? echo $unit_of_measurement[$cons_uom]; ?>&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right"><? echo number_format($yarnReceive_qty,2); ?></td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right"><? echo number_format($balance,2); ?></td>
								<td align="right"><? echo number_format($return_balance,2); ?></td>
							</tr>
							<?
							//$party_issue_qty=$party_returnable_qty=$party_rec_qty=$party_fab_rej_qty=$party_return_qty=$party_yarn_rej_qty
							$party_rec_qty+=$receive_qty;
							$tot_rec_qty+=$receive_qty;
							$party_fab_rej_qty+=$fab_reject_qty;
							$tot_fab_rej_qty+=$fab_reject_qty;
							$party_yarnRec_qty+=$yarnReceive_qty;
							$tot_yarnRec_qty+=$yarnReceive_qty;
							$party_return_qty+=$return_qty;
							$tot_return_qty+=$return_qty;
							$party_yarn_rej_qty+=$yarn_reject_qty;
							$tot_yarn_rej_qty+=$yarn_reject_qty;
							$i++;
						}
						
						?>
						<tr bgcolor="#CCCCCC" >
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><strong>Party Total</strong></td> 
							<td align="right"><? echo number_format($party_issue_qty,2); ?></td>
							<td align="right"><? echo number_format($party_returnable_qty,2); ?></td>
							<td align="right"><? echo number_format($party_rec_qty,2); ?></td>
							<td align="right"><? echo number_format($party_fab_rej_qty,2); ?></td>
							<td align="right"><? echo number_format($party_yarnRec_qty,2); ?></td>
							
							<td align="right"><? echo number_format($party_return_qty,2); ?></td>
							<td align="right"><? echo number_format($party_yarn_rej_qty,2); ?></td>
							<td align="right"><? echo number_format($balance,2); ?></td>
							<td align="right"><? echo number_format($return_balance,2); ?></td>
						</tr>
						<?
						$tot_balance+=$balance;
						$tot_return_balance+=$return_balance;
					}
					?>
				</table>
			</div>
			<table width="2350" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
				<tr>
					<td width="40">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="125">&nbsp;</td>
					<td width="115">&nbsp;</td>
					<td width="115">&nbsp;</td>
					<td width="130">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="130">&nbsp;</td>
					<td width="130">&nbsp;</td>
					<td width="90">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="150">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="60"><strong>Grand Total</strong></td>
					<td width="100" align="right"><? echo number_format($tot_issue_qty,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_returnable_qty,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_rec_qty,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_fab_rej_qty,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_yarnRec_qty,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_return_qty,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_yarn_rej_qty,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_balance,2); ?></td>
					<td align="right"><? echo number_format($tot_return_balance,2); ?></td>
				</tr>
			</table>
		</div>
	</div>
	<?
}

if($action=="report_generate_excel")
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_arr=return_library_array( "select id, short_name from  lib_buyer", "id", "short_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

	$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	

	
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
	$knitting_company=str_replace("'","",$txt_knitting_com_id);
	$type=str_replace("'","",$type);
	if (str_replace("'","",$cbo_knitting_source)==0) $knitting_source_cond=""; else $knitting_source_cond=" and a.knit_dye_source=$cbo_knitting_source";
	if (str_replace("'","",$cbo_knitting_source)==0) $knitting_source_rec_cond=""; else $knitting_source_rec_cond=" and a.knitting_source=$cbo_knitting_source";
	if ($knitting_company=='') $knitting_company_cond=""; else  $knitting_company_cond="  and a.knit_dye_company in ($knitting_company)";



	ob_start();

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
			CASE WHEN entry_form='3' and trans_type=2 THEN group_concat(po_breakdown_id) END AS yarn_order_id,
			CASE WHEN entry_form='9' and trans_type=4 THEN group_concat(po_breakdown_id) END AS yarn_return_order_id 
			from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (3,9) and status_active=1 and is_deleted=0 group by trans_id");
	}
	else
	{
		$datapropArray=sql_select("select trans_id,
			listagg(CASE WHEN entry_form='3' and trans_type=2 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS yarn_order_id,
			listagg(CASE WHEN entry_form='9' and trans_type=4 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS yarn_return_order_id 
			from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (3,9) and status_active=1 and is_deleted=0 group by trans_id,entry_form,trans_type");
	}

	foreach($datapropArray as $row) 
	{
		$order_nos_array[$row[csf('trans_id')]]['yarn_issue']=$row[csf('yarn_order_id')];
		$order_nos_array[$row[csf('trans_id')]]['yarn_return']=$row[csf('yarn_return_order_id')];
	}	

	

	if (str_replace("'","",$cbo_knitting_source)==0) $knitting_source_cond_party=""; else $knitting_source_cond_party=" and knit_dye_source=$cbo_knitting_source";
	if ($knitting_company=='') $knitting_company_cond_party=""; else  $knitting_company_cond_party=" and a.id in ($knitting_company)";
	if ($knitting_company=='') $knitting_company_cond_comp=""; else  $knitting_company_cond_comp=" and id in ($knitting_company)";
	$knit_source=str_replace("'","",$cbo_knitting_source);
	if ($knit_source==0) $knit_source_cond_party=""; else $knit_source_cond_party=" and a.knit_dye_source in ($knit_source)";
	if ($knitting_company=='') $knit_company_cond_party=""; else  $knit_company_cond_party=" and a.knit_dye_company in ($knitting_company)";
	if ($from_date!='' && $to_date!='') $iss_date_cond=" and a.issue_date between '$from_date' and '$to_date'"; else $iss_date_cond="";
	
	
	$sql="select a.id as issue_id, a.issue_number, a.issue_number_prefix_num, a.issue_purpose, a.buyer_id, a.knit_dye_company, a.knit_dye_source, a.booking_id, a.booking_no, a.issue_date, a.issue_basis, b.id as trans_id, b.requisition_no, b.supplier_id, b.cons_quantity as issue_qnty, b.return_qnty, c.yarn_count_id, c.yarn_type, c.lot 
	from inv_issue_master a, inv_transaction b, product_details_master c 
	where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.return_qnty!=0 $knit_source_cond_party  $knit_company_cond_party $challan_cond $iss_date_cond order by a.knit_dye_company, a.issue_number_prefix_num";
	$result=sql_select($sql);
	$all_issue_data=array();
	foreach($result as $row)
	{
		$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['yarn_issue']);
		$all_job_no=""; $order_nos=''; $order_qnty=0;
		foreach($all_po_id as $po_id)
		{
			if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
			if($all_job_no=='') $all_job_no=$po_arr[$po_id]['job']; else $all_job_no.=",".$po_arr[$po_id]['job'];
			$order_qnty+=$po_arr[$po_id]['qnty'];
		}
		$job_no=implode(",",array_unique(explode(",",$all_job_no)));
		$issue_id_arr[$row[csf("issue_id")]]=$row[csf("issue_id")];
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_number"]=$row[csf("issue_number")];
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_number_prefix_num"]=$row[csf("issue_number_prefix_num")];
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_purpose"]=$row[csf("issue_purpose")];
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["buyer_id"]=$row[csf("buyer_id")];
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["knit_dye_company"]=$row[csf("knit_dye_company")];
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["knit_dye_source"]=$row[csf("knit_dye_source")];
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["booking_id"]=$row[csf("booking_id")];
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["booking_no"]=$row[csf("booking_no")];
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_date"]=$row[csf("issue_date")];
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_basis"]=$row[csf("issue_basis")];
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["trans_id"]=$row[csf("trans_id")];
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_qnty"]=$row[csf("issue_qnty")];
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["returnable_qnty"]=$row[csf("return_qnty")];
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["yarn_count_id"]=$row[csf("yarn_count_id")];
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["yarn_type"]=$row[csf("yarn_type")];
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["lot"]=$row[csf("lot")];
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["job_no"]=$job_no;
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["order_nos"]=$order_nos;
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["type"]="2";
		
		$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["supplier_id"]=$row[csf("supplier_id")];
		
		
		$issue_purpose[$row[csf("issue_number_prefix_num")]]=$row[csf("issue_purpose")];
	}
	
	
	
	$receive_ret_array=array();
	$sql_return="select a.recv_number, a.knitting_source as knit_dye_source, a.knitting_company as knit_dye_company, a.booking_no, a.buyer_id, a.receive_date,a.recv_number, a.item_category, b.issue_challan_no as issue_number_prefix_num, b.issue_id, b.id as trans_id, c.supplier_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, c.yarn_count_id, c.yarn_type, c.lot 
	from inv_receive_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and  a.entry_form=9 and a.company_id=$cbo_company_name and  b.item_category=1 and b.transaction_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";// echo $sql_return;
	$sql_return_result=sql_select($sql_return);
	foreach($sql_return_result as $row)
	{
		
		$issue_ret_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("lot")]]["return_qnty"]+=$row[csf("cons_quantity")];
		$issue_ret_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("lot")]]["cons_reject_qnty"]+=$row[csf("cons_reject_qnty")];
		
	}
	
	
	if($knit_source==1){
		$knitting_party=$company_arr; 
	}
	else if($knit_source==3) {
		$knitting_party=$supplier_arr;	
	}
	
	
	?>
	<div>
		<table width="1380" cellpadding="0" cellspacing="0" id="caption" align="left" style="font-size:16px">
			<tr>
				<td align="center" colspan="17"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
			</tr> 
			<tr>  
				<td align="center" colspan="17"><strong><? echo $report_title; ?> (Returnable Without Challan)</strong></td>
			</tr>  
			<tr> 
				<td align="center" colspan="17"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
			</tr>
		</table>
		<br />
		<table width="1380" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
				<th width="30">SL</th>
				<th width="100">Party name</th>	
				<th width="100">Challan No</th>
				<th width="100">Issue Purpose</th>
				<th width="100">Job No.</th>
				<th width="100">Order No</th>
				<th width="100">Buyer</th>
				<th width="50">Count</th>
				<th width="100">Supplier</th>
				<th width="100">Type</th>
				<th width="50">Lot</th>
				<th width="100">Booking/Reqsn. No</th>
				<th width="60">Issue Qty.</th>
				<th width="60">Returnable Qty.</th>
				<th width="60">Returned Qty</th>
				<th width="60">Reject Qty</th>
				<th width="">Returnable Balanace</th>
			</thead>
		</table>
		<div style="width:1398px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="1380" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" align="left"> 
				<?


				$i=1; $k=1; $j=1; $balance=0; $tot_iss_qnty=0; $tot_recv_qnty=0; $tot_rej_qnty=0; $tot_ret_qnty=0; $tot_reject_yarn_qnty=0; $challan_array=array(); $party_array=array(); 
				foreach($all_issue_data as $knit_company=>$knit_company_data)
				{
					foreach($knit_company_data as $issue_chalan_no=>$issue_chalan_no_data)
					{
						foreach($issue_chalan_no_data as $trans_id=>$value)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$return_balance=($value['returnable_qnty']-($value['return_qnty']+$value['cons_reject_qnty']));
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="100"><? echo $knitting_party[$knit_company];?></td>
								<td width="100"><? echo $issue_chalan_no; ?></td>                               
								<td width="100"><? echo $yarn_issue_purpose[$issue_purpose[$issue_chalan_no]]; ?></td>                                <td width="100"><p><? echo $value['job_no']; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $value['order_nos']; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $buyer_arr[$value['buyer_id']]; ?>&nbsp;</p></td>
								<td width="50"><p><? echo $count_arr[$value['yarn_count_id']]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $supplier_arr[$value['supplier_id']]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $yarn_type[$value['yarn_type']]; ?>&nbsp;</p></td> 
								<td width="50"><p><? echo $value['lot']; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $value['booking_no']; ?>&nbsp;</p></td>
								<td width="60" align="right"><? echo number_format($value['issue_qnty'],2,'.',''); ?></td>
								<td width="60" align="right"><? echo number_format($value['returnable_qnty'],2,'.',''); ?></td>
								<td width="60" align="right"><? echo number_format($issue_ret_data[$knit_company][$issue_chalan_no][$value['lot']]["return_qnty"],2);//echo number_format($value['return_qnty'],2,'.',''); ?></td>
								<td width="60" align="right"><? echo number_format($issue_ret_data[$knit_company][$issue_chalan_no][$value['lot']]["cons_reject_qnty"],2);//number_format($value['cons_reject_qnty'],2,'.',''); ?></td>
								<td align="right"><? 
								$tot_ret_rej_qty = $issue_ret_data[$knit_company][$issue_chalan_no][$value['lot']]["return_qnty"]+ $issue_ret_data[$knit_company][$issue_chalan_no][$value['lot']]["cons_reject_qnty"];
								$balance=$return_balance-$tot_ret_rej_qty;
								echo number_format($balance,2,'.','');
								?></td>

							</tr>
							<?
							$i++;
							$challan_issue_qnty+=$value['issue_qnty'];
							$challan_returnable_qnty+=$value['returnable_qnty'];
							$challan_return_qnty+=$issue_ret_data[$knit_company][$issue_chalan_no][$value['lot']]["return_qnty"];
							$challan_cons_reject_qnty+=$issue_ret_data[$knit_company][$issue_chalan_no][$value['lot']]["cons_reject_qnty"];
							$challan_return_balance+=$balance;

						}
					}
				}

				?>


				<tfoot>
					<th colspan="12" align="right"><b>Total</b></th>
					<th align="right"><? echo number_format($challan_issue_qnty,2,'.',''); ?></th>
					<th align="right"><? echo number_format($challan_returnable_qnty,2,'.',''); ?></th>
					<th align="right"><? echo number_format($challan_return_qnty,2,'.',''); ?></th>
					<th align="right"><? echo number_format($challan_cons_reject_qnty,2,'.',''); ?></th>
					<th align="right"><? echo number_format($challan_return_balance,2,'.',''); ?></th>
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
	echo "$total_data####$filename";
	exit();
}

if ($action == "FSO_No_popup") 
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		var hide_fso_id='<? echo $hide_fso_id; ?>';
		var selected_id = new Array, selected_name = new Array();

		function check_all_data(is_checked)
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) 
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('txt_fso_row_id').value;
			if(old!="")
			{   
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{  
					js_set_value( old[i] ) 
				}
			}
		}

		function js_set_value( str) 
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );


			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) 
			{
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id =''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hide_fso_id').val( id );
			$('#hide_fso_no').val( name );
		}

	</script>

	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:710px;">
					<table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Company</th>
							<th>Buyer Name</th>
							<th>Job Year</th>
							<th>Within Group</th>
							<th>FSO NO.</th>
							<th>Booking NO.</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
							<input type="hidden" name="hide_fso_no" id="hide_fso_no" value="" />
							<input type="hidden" name="hide_fso_id" id="hide_fso_id" value="" />

						</thead>
						<tbody>
							<tr>
								<td>
									<?
									echo create_drop_down( "cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_company_id, "",1 );
									?>
								</td>
								<td align="center">
									<? 
									echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and b.tag_company=$cbo_company_id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
									?>
								</td>                 
								<td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td> 
								<td><? echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 1, "-- Select --", $selected, "",0,"" );?></td>    
								<td>				
									<input type="text" style="width:100px" class="text_boxes" name="txt_fso_no" id="txt_fso_no" />	
								</td> 	
								<td>				
									<input type="text" style="width:100px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />	
								</td> 
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_id; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('cbo_within_group').value+'**'+document.getElementById('txt_fso_no').value+'**'+document.getElementById('txt_booking_no').value+'**'+'<? echo $hidden_fso_id;?>'+'**'+'<? echo $job_ids;?>'+'**'+'<? echo $booking_id?>'+'**'+'<? echo $booking_type;?>', 'create_fso_no_search_list_view', 'search_div', 'party_wise_yarn_reconciliation_summary_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_fso_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$buyer_id=$data[1];
	$year=$data[2];
	$within_group=$data[3];
	$fso_no=trim($data[4]);
	$booking_no=trim($data[5]);

	$job_ids=trim($data[7]);
	$booking_ids=trim($data[8]);
	$booking_type=trim($data[9]);

	if($job_ids!="" || $booking_type=="1")
	{
		if($booking_ids!="") $job_book_cond = " and c.id in ($booking_ids)";
		if($job_ids!="") $job_book_cond .= " and a.id in ($job_ids)";
		$ref_booking_sql =  sql_select("select b.booking_no,c.id as booking_id 
		from wo_po_details_master a,wo_booking_dtls b ,wo_booking_mst c 
		where a.job_no=b.job_no and b.booking_no=c.booking_no and a.status_active=1 
		and a.is_deleted=0 and b.booking_type in(1,4,3) and a.company_name=$company_id $job_book_cond
		group by b.booking_no,c.id ");
		foreach ($ref_booking_sql as $val) 
		{
			$ref_booking_id[$val[csf("booking_id")]] = $val[csf("booking_id")];
		}
		$ref_booking_without_order = " and a.booking_without_order<>1";
	}
	else if($booking_type == "2")
	{
		if($booking_ids!="") 
		{
			$job_book_cond = " and c.id in ($booking_ids)";
			$ref_booking_sql = sql_select("select c.booking_no,c.id as booking_id from wo_non_ord_samp_booking_mst c where c.status_active=1 and c.is_deleted=0 and c.company_id=$company_id $job_book_cond group by c.booking_no,c.id");
			foreach ($ref_booking_sql as $val) 
			{
				$ref_booking_id[$val[csf("booking_id")]] = $val[csf("booking_id")];
			}

			$ref_booking_without_order = " and a.booking_without_order=1";
		}
	}



	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	$search_cond = "";

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") 
			{
				$buyer_cond_with_1=" and c.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_cond_with_2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			} 
			else 
			{
				$buyer_cond_with_1 =  "";
				$buyer_cond_with_2 =  "";
			}
		}
		else
		{
			$buyer_cond_with_1 =  "";
			$buyer_cond_with_2 =  "";
		}
	}
	else
	{
		$buyer_cond_with_1 =  " and c.buyer_id=$data[1]";
		$buyer_cond_with_2 =  " and a.buyer_id=$data[1]";
	}
	

	if($fso_no != "")
	{
		$search_cond .= " and a.job_no_prefix_num = '$fso_no'" ;
	}
	if($booking_no != "")
	{
		$search_cond .= " and a.sales_booking_no like '%$booking_no%'" ;
	}
	if($db_type==0)
	{
		if($year!=0) $search_cond .=" and YEAR(a.insert_date)=$year"; else $search_cond .="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year!=0) $search_cond .=" $year_field_con=$year"; else $search_cond .="";
	}

	if($ref_booking_id!="")
	{
		$ref_booking_cond = " and a.booking_id in (".implode(",", $ref_booking_id).") $ref_booking_without_order";
	}

	$sql_2 ="select a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b 
	where a.id = b.mst_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '2' $buyer_cond_with_2
	group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id 
	order by id desc";

	$sql_1 = "select a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b ,wo_booking_mst c
	where a.id = b.mst_id and a.sales_booking_no = c.booking_no and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 
	and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '1' $buyer_cond_with_1 $ref_booking_cond
	group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id
	union all
	select a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b ,wo_non_ord_samp_booking_mst c
	where a.id = b.mst_id and a.sales_booking_no = c.booking_no and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 
	and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '1' $buyer_cond_with_1 $ref_booking_cond
	group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id
	";

	if($within_group == 1 || $ref_booking_id!="")
	{
		$sql = $sql_1 ;
	}
	else if($within_group == 2)
	{
		$sql = $sql_2;
	}else
	{
		$sql = $sql_1." union all ". $sql_2 ;
	}
	//echo $sql;
	?>
	
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" align="left">
		<thead>
			<th width="40">SL</th>
			<th width="130">Company</th>
			<th width="120">Buyer</th>
			<th width="150">FSO No</th>
			<th width="">Booking No</th>
		</thead>
	</table>
	<div style="width:700px; overflow-y:scroll; max-height:250px; float: left;" id="buyer_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="682" class="rpt_table" id="tbl_list_search" >
			<?php 
			$i=1; $fso_row_id="";
			$nameArray=sql_select( $sql );
			foreach ($nameArray as $selectResult)
			{
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";	
				?>

				<tr height="20" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
					<td width="40" align="center"><?php echo "$i"; ?>
					<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $selectResult[csf('id')]; ?>"/>
					<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $selectResult[csf('job_no')]; ?>"/>
				</td>	
				<td width="130"><p><?php echo $company_arr[$selectResult[csf('company_id')]]; ?></p></td>
				<td width="120" title="<? echo $selectResult[csf('buyer_id')];?>"><?php echo $buyer_arr[$selectResult[csf('buyer_id')]]; ?></td>
				<td width="150"><p><?php echo $selectResult[csf('job_no')];?></p></td>
				<td width=""><?php echo $selectResult[csf('sales_booking_no')];?></td> 
			</tr>
			<?
			$i++;
		}
		?>
	</table>
	</div>

	<table width="600" cellspacing="0" cellpadding="0" style="border:none" align="left">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%"> 
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
					</div>
				</div>
			</td>
		</tr>
	</table>

	<?
	exit(); 
}
?>
