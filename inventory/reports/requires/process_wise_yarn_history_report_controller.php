<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

require_once('../../../includes/class3/class.conditions.php');
require_once('../../../includes/class3/class.reports.php');
require_once('../../../includes/class3/class.yarns.php');


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
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	
	<script>
		
		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'process_wise_yarn_history_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
								</td>
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

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$company_short_arr=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
$buyer_array=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];

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

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,80,80,60","620","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	exit(); 
} 

if($action=="style_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array; var selected_name = new Array;

		function check_all_datas()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_div' ).rows.length;
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

		function js_set_value2( str ) {

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
			$('#hidden_style_no').val( id );
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
							<th width="170">Please Enter Style No</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />              
							<input type="hidden" name="hidden_style_no" id="hidden_style_no" value="" />              
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
									?>
								</td>   

								<td align="center">				
									<input type="text" style="width:130px" class="text_boxes" name="txt_style" id="txt_style" />	
								</td> 	
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_style').value+'**'+'<? echo $cbo_year_id; ?>', 'create_style_no_search_list_view', 'search_div', 'process_wise_yarn_history_report_controller', 'setFilterGrid(\'tbl_list_div\',-1)');" style="width:100px;" />
								</td>
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
if($action=="create_style_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[3];
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{ 
				$buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else 
			{ 
				$buyer_id_cond="";
			}
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
	
	if(trim($data[2])) 
	{
		$style_ref_cond=" and style_ref_no = ".trim($data[2]);
	}
	

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
	

	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_cond from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id $style_ref_cond $buyer_id_cond $year_search_cond order by style_ref_no";
	
	
	$sqlResult=sql_select($sql);
	?>
	<div align="center">
		<fieldset style="width:650px;margin-left:10px">
			<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="130">Company</th> 
						<th width="110">Buyer</th>
						<th width="110">Job No</th>
						<th width="120">Style Ref.</th>
					</thead>
				</table>
				<div style="width:670px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_div">
						<?
						$i=1;
						foreach($sqlResult as $row )
						{
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

							$data=$i.'_'.$row[csf('style_ref_no')];
						//echo $data;
							?>
							<tr id="tr_<?php echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value2('<? echo $data;?>')">
								<td width="30" align="center"><?php echo $i; ?>
									<td width="130"><p><? echo $company_arr[$row[csf('company_name')]]; ?></p></td>
									<td width="110"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
									<td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
									<td width="120"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
								</tr>
								<?
								$i++;
							}
							?>
						</table>
					</div>
					<table width="650" cellspacing="0" cellpadding="0" style="border:none" align="center">
						<tr>
							<td align="center" height="30" valign="bottom">
								<div style="width:100%">
									<div style="width:50%; float:left" align="left">
										<input type="checkbox" name="check_all" id="check_all" onClick="check_all_datas()"/>
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
		</fieldset>
	</div>
	<?
	exit(); 
}

if($action=="order_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	$data_ex = explode("_",$data);
	?>
	
	<script>
		
		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
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
			$('#order_no_id').val( id );
			$('#order_no_val').val( ddd );
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
								<input type="hidden" name="order_no_id" id="order_no_id" value="" />
								<input type="hidden" name="order_no_val" id="order_no_val" value="" />
							</th> 					
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$data_ex[0] $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$data_ex[1],"",0 );
									?>
								</td>                 
								<td align="center">	
									<?
									$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order NO");
									$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
									echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
									?>
								</td>     
								<td align="center" id="search_by_td">				
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
								</td> 	
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $data_ex[0]; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_order_no_search_list_view', 'search_div', 'process_wise_yarn_history_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
								</td>
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
	$year_id=$data[4];
	$month_id=$data[5];

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
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) 
	{
		$search_field="a.style_ref_no";
	} 
	else if($search_by==1)
	{
		$search_field="a.job_no";
	} 
	else
	{
		$search_field="b.po_number";
	}

	$arr=array (0=>$company_arr,1=>$buyer_arr);

	if($db_type==0)
	{
		if($year_id!=0) $year_search_cond=" and year(insert_date)=$year_id"; else $year_search_cond="";
		$year_cond= "year(a.insert_date)as year";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(insert_date,'YYYY')=$year_id"; else $year_search_cond="";
		$year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
	}

	$sql= "SELECT b.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, b.po_number, $year_cond from wo_po_details_master a,wo_po_break_down b  where a.id=b.job_id and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond order by a.job_no DESC";
	// echo $sql;

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,PO No", "120,80,80,60","620","270",0, $sql , "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,po_number", "",'','0,0,0,0,0','',1) ;
	exit();
} 

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id 	= str_replace("'","",$cbo_company_id);
	$buyer_id 		= str_replace("'","",$cbo_buyer_id);
	$ship_status 	= str_replace("'","",$cbo_shipment_status);
	$job_no 		= str_replace("'","",$txt_job_no);
	$job_id 		= str_replace("'","",$txt_job_id);
	$style_no 		= str_replace("'","",$txt_style_no);
	$order_no 		= str_replace("'","",$txt_order_no);
	$order_id 		= str_replace("'","",$txt_order_id);
	$date_from 		= str_replace("'","",$txt_date_from);
	$date_to 		= str_replace("'","",$txt_date_to);

	$sql_cond = " and a.company_name=$company_id";
	$sql_cond .= ($buyer_id!=0) ? " and a.buyer_name=$buyer_id" : "";
	$sql_cond .= ($ship_status!=0) ? " and b.shiping_status=$ship_status" : "";
	$sql_cond .= ($job_no!="") ? " and a.job_no_prefix_num in($job_no)" : "";
	$sql_cond .= ($job_id!="") ? " and a.id in($job_id)" : "";
	$sql_cond .= ($order_id!="") ? " and b.id in($order_id)" : "";

	if($style_no!="")
	{
		$style_no = "'".implode("','",explode(",",$style_no))."'";
		$sql_cond .= " and a.style_ref_no in($style_no)";
	}

	if($order_no!="")
	{
		$order_no = "'".implode("','",explode(",",$order_no))."'";
		$sql_cond .= " and b.po_number in($order_no)";
	}

	if($date_from!="" && $date_to!="")
	{
		$sql_cond .= " and b.po_received_date between $txt_date_from and $txt_date_to";
	}

    /*===============================================================================/
    /                                 	main query                                	 /
    /============================================================================== */
	$sql = "SELECT a.company_name,a.buyer_name,a.style_ref_no,a.job_no,b.id as po_id,b.po_number,b.po_received_date,b.unit_price,(b.po_total_price*a.total_set_qnty) as po_val,(b.po_quantity*a.total_set_qnty) as po_quantity,b.shiping_status from WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b where a.id=b.job_id and a.status_active=1 and b.status_active=1 $sql_cond order by a.job_no DESC";
	// echo $sql;
	$res= sql_select($sql);

	$po_id_arr = array();
	foreach ($res as $val) 
	{
		$po_id_arr[$val['PO_ID']] = $val['PO_ID'];
	}

    /*=============================================================================/
	/								grey yarn issue								   /
	/=============================================================================*/
	$po_cond = where_con_using_array($po_id_arr,0,"b.po_breakdown_id");
	$sqlGreyYArnIssue = "SELECT b.po_breakdown_id as po_id,b.quantity AS QTY from inv_transaction a, order_wise_pro_details b,inv_issue_master c,product_details_master f where a.id=b.trans_id and a.prod_id=b.prod_id and f.id=a.prod_id and b.prod_id=f.id and c.id=a.mst_id and a.item_category=1 and a.transaction_type in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.dyed_type in(0,2) and f.item_category_id=1 $po_cond";
	// echo $sqlGreyYArnIssue;die();
	$greyYarnISsueRes = sql_select($sqlGreyYArnIssue);
	$grey_yarn_data_array = array();
	$buyer_grey_yarn_data_array = array();
	foreach ($greyYarnISsueRes as $val) 
	{
        $grey_yarn_data_array[$val['PO_ID']] += $val['QTY'];
    }

	/*===============================================================================/
	/								yarn issue for dyeing							 /
	/===============================================================================*/
	$po_cond = where_con_using_array($po_id_arr,0,"b.po_breakdown_id");
	$sqlYArnIssue = "SELECT b.po_breakdown_id as po_id,a.TRANSACTION_TYPE,b.quantity AS QTY from inv_transaction a, order_wise_pro_details b,inv_issue_master c where a.id=b.trans_id and a.prod_id=b.prod_id and c.id=a.mst_id  and a.item_category=1 and a.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.issue_purpose=2 $po_cond";
	// echo $sqlYArnIssue;die();
	$yarnISsueRes = sql_select($sqlYArnIssue);
	$yarn_dyeing_data_array = array();
	foreach ($yarnISsueRes as $val) 
	{
        $yarn_dyeing_data_array[$val['PO_ID']]['issue_qty'] += $val['QTY'];
	}

	/*==============================================================================/
	/									dyed yarn rcv 								/
	/==============================================================================*/
	$po_cond = where_con_using_array($po_id_arr,0,"f.po_breakdown_id");
	$sqlYArnUsd = "SELECT f.po_breakdown_id as po_id, f.QUANTITY from inv_receive_master a, inv_transaction b,wo_yarn_dyeing_mst e,order_wise_pro_details f where a.id=b.mst_id and b.pi_wo_batch_no=e.id and e.id=a.booking_id and b.item_category=1 and f.trans_id=b.id and a.entry_form=1 and f.entry_form=1 and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose in(2)  $po_cond";
	// echo $sqlYArnUsd;die();
	$yarnUsdRes = sql_select($sqlYArnUsd);
	foreach ($yarnUsdRes as $val) 
	{
		$yarn_dyeing_data_array[$val['PO_ID']]['rcv_qty'] += $val['QUANTITY'];
	}

    /*===============================================================================/
    /                                 	knitting prod                              	 /
    /============================================================================== */
	$po_cond = where_con_using_array($po_id_arr,0,"b.po_breakdown_id");
	$sqlKnit = "SELECT a.knitting_source as source, b.po_breakdown_id as po_id, b.QUANTITY  from inv_receive_master a, order_wise_pro_details b,pro_grey_prod_entry_dtls c where a.id=c.mst_id and c.id=b.dtls_id and a.item_category=13 and b.entry_form=58 and a.entry_form=58 and b.trans_type=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 $po_cond";
	// echo $sqlKnit;
	$result = sql_select($sqlKnit);
	$kitting_data_array = array();
	foreach ($result as $val) 
	{
		$kitting_data_array[$val['PO_ID']][$val['SOURCE']] += $val['QUANTITY'];
	}

    /*===============================================================================/
    /                                 	dyeing prod                              	 /
    /============================================================================== */
	/* $po_cond = where_con_using_array($po_id_arr,0,"b.po_id");
	$sqlDye = "SELECT b., c.SERVICE_SOURCE,B.BATCH_QNTY AS QTY FROM PRO_BATCH_CREATE_MST A, PRO_BATCH_CREATE_DTLS B,PRO_FAB_SUBPROCESS C WHERE A.ID=B.MST_ID AND A.ID=C.BATCH_ID $po_cond AND C.LOAD_UNLOAD_ID=2 AND C.PROCESS_ID='31' AND C.RESULT=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0";
    // echo $sqlDye;die();
    $dyeRes = sql_select($sqlDye);
	$dyeing_data_array = array();
    foreach ($dyeRes as $val)
    {
       $dyeing_data_array[$val['PO_ID']][$val['SERVICE_SOURCE']] += $val['QTY'];
    } */

	/*===============================================================================/
	/								FF Purses Rcv data						 		 /
	/===============================================================================*/
	$po_cond = where_con_using_array($po_id_arr,0,"c.po_breakdown_id");
    $sqlFinFab = "SELECT b.transaction_type,c.po_breakdown_id as po_id,c.quantity AS QTY
	from inv_receive_master a,inv_transaction b,order_wise_pro_details c,wo_booking_mst f,pro_batch_create_mst g where a.id=b.mst_id and b.id=c.trans_id and b.transaction_type in(1,4)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(37,52) and a.item_category=2 and f.booking_type=1 and f.fabric_source=2 and f.status_active=1 and f.id=g.booking_no_id and g.id=b.pi_wo_batch_no and g.batch_against in(0) $po_cond";
    // echo $sqlFinFab;die();
    $finFabRes = sql_select($sqlFinFab);
    $finfab_purses_data_array = array();
	foreach ($finFabRes as $val) 
	{
        $finfab_purses_data_array[$val['PO_ID']][$val['TRANSACTION_TYPE']]['qty'] += $val['QTY'];
	}
	// print_r($finfab_purses_data_array);die();

	/*===============================================================================/
	/								FF Purses Issue data							 /
	/===============================================================================*/	
    $sqlFinFab = "SELECT b.TRANSACTION_TYPE,c.po_breakdown_id as po_id,c.quantity AS QTY
	from inv_issue_master a,inv_transaction b,order_wise_pro_details c,wo_booking_mst f,pro_batch_create_mst g where a.id=b.mst_id and b.id=c.trans_id and b.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(18,46) and a.item_category=2 and f.booking_type=1 and f.fabric_source=2 and f.status_active=1 and f.id=g.booking_no_id and g.id=b.pi_wo_batch_no and g.batch_against in(0) $po_cond";//and g.batch_against in(0) 
    // echo $sqlFinFab;die();
    $finFabRes = sql_select($sqlFinFab);
	foreach ($finFabRes as $val) 
	{
        $finfab_purses_data_array[$val['PO_ID']][$val['TRANSACTION_TYPE']]['qty'] += $val['QTY'];
	}

	/*===============================================================================/
	/									Dyeing data							 	 	 /
	/===============================================================================*/	
	$sqlFinFab = "SELECT a.knitting_source as source, c.po_breakdown_id as po_id,C.QUANTITY AS QNTY FROM INV_RECEIVE_MASTER A,INV_TRANSACTION B,ORDER_WISE_PRO_DETAILS C WHERE A.ID=B.MST_ID AND B.ID=C.TRANS_ID AND B.TRANSACTION_TYPE=1  and A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND A.ENTRY_FORM=37 AND C.ENTRY_FORM=37 AND A.ITEM_CATEGORY=2 $po_cond";
    // echo $sqlFinFab;die();
    $finFabRes = sql_select($sqlFinFab);
	$dyeing_data_array = array();
    foreach ($finFabRes as  $val) 
    {
       $dyeing_data_array[$val['PO_ID']][$val['SOURCE']] += $val['QNTY'];
    }

	/*===============================================================================/
	/									AOP Data							 	 	 /
	/===============================================================================*/
	$po_cond = where_con_using_array($po_id_arr,0,"b.order_id");
	$sqlDye = "SELECT B.order_id as PO_ID,b.BATCH_ISSUE_QTY as QTY FROM INV_RECEIVE_MAS_BATCHROLL A, PRO_GREY_BATCH_DTLS B WHERE A.ID=B.MST_ID  $po_cond  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 and a.dyeing_source=3 and a.entry_form=91";
	// echo $sqlDye;die();
	$subcob_aop_data = array();
	$dyeRes = sql_select($sqlDye);
	foreach ($dyeRes as $val) 
	{
		$subcob_aop_data[$val['PO_ID']] += $val['QTY'];
	}

	/*===============================================================================/
	/								FF Delivery Data						 	 	 /
	/===============================================================================*/
	$po_cond = where_con_using_array($po_id_arr,0,"b.order_id");
	$sqlDye = "SELECT B.ORDER_ID as PO_ID,b.GREY_USED_QNTY as QTY FROM PRO_GREY_PROD_DELIVERY_MST A, PRO_GREY_PROD_DELIVERY_DTLS B WHERE A.ID=B.MST_ID  $po_cond  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 and a.entry_form=54";
	// echo $sqlDye;die();
	$ff_delivery_data = array();
	$dyeRes = sql_select($sqlDye);
	foreach ($dyeRes as $val) 
	{
		$ff_delivery_data[$val['PO_ID']] += $val['QTY'];
	}

	/*===============================================================================/
	/								FF Issue for cutting					 	 	 /
	/===============================================================================*/
	$po_cond = where_con_using_array($po_id_arr,0,"c.PO_BREAKDOWN_ID");
	$sqlIssue = "SELECT a.KNIT_DYE_SOURCE as source,C.PO_BREAKDOWN_ID as PO_ID,C.QUANTITY AS QNTY FROM INV_ISSUE_MASTER A,INV_TRANSACTION B,ORDER_WISE_PRO_DETAILS C WHERE A.ID=B.MST_ID AND B.ID=C.TRANS_ID AND B.TRANSACTION_TYPE=2 AND A.ISSUE_PURPOSE=9 $po_cond and A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND A.ENTRY_FORM=18 AND A.ITEM_CATEGORY=2";
	// echo $sqlIssue;die();
	$ff_issue_data = array();
	$issueRes = sql_select($sqlIssue);
	foreach ($issueRes as $val) 
	{
		$ff_issue_data[$val['PO_ID']][$val['SOURCE']] += $val['QNTY'];
	}

    /*===============================================================================/
    /                                 Export Invoice                                 /
    /============================================================================== */
    $po_cond = where_con_using_array($po_id_arr,0,"b.po_breakdown_id");
    $sql = "SELECT a.id as inv_id, b.po_breakdown_id as po_id,b.current_invoice_qnty,b.current_invoice_value,b.current_invoice_rate,a.net_weight from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $po_cond";
	// echo $sql;
    $result = sql_select($sql);
    $invice_data = array();
    $invice_data2 = array();
    $inv_wise_qty_arr = array();
    $chk_inv = array();
    foreach ($result as $val) 
    {
        $invice_data[$val['PO_ID']]['inv_qty'] += $val['CURRENT_INVOICE_QNTY'];
        $invice_data[$val['PO_ID']]['inv_value'] += $val['CURRENT_INVOICE_VALUE'];
        $invice_data[$val['PO_ID']]['inv_rate'] = $val['CURRENT_INVOICE_RATE'];

        $invice_data2[$val['INV_ID']][$val['PO_ID']]['inv_qty'] += $val['CURRENT_INVOICE_QNTY'];
        $invice_data2[$val['INV_ID']][$val['PO_ID']]['net_weight'] = $val['NET_WEIGHT'];

        $inv_wise_qty_arr[$val['INV_ID']] += $val['CURRENT_INVOICE_QNTY'];
    }

	// ===================== getting shipment kg data ==============================
	$po_wise_shipment_qty_arr = array();
	foreach ($invice_data2 as $inv_id => $inv_data) 
	{
		foreach ($inv_data as $po_id => $row) 
		{
			$po_wise_shipment_qty_arr[$po_id] += ($inv_wise_qty_arr[$inv_id]>0) ? ($row['inv_qty']*$row['net_weight'])/$inv_wise_qty_arr[$inv_id] : 0;
			// echo "(".$row['inv_qty']."*".$row['net_weight'].")/".$inv_wise_qty_arr[$inv_id]."<br>";
		}
	}


	$tbl_width = 2500;
	ob_start();
	?>
	<fieldset style="width:<?=$tbl_width+20;?>px">
		<table cellpadding="0" cellspacing="0" width="1400">
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if( $txt_date_from!="" && $txt_date_to!="" ) echo change_date_format(str_replace("'","",$txt_date_from)) ." To ". change_date_format(str_replace("'","",$txt_date_to)) ;?></strong></td>
			</tr>
		</table>
		<table width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
			<thead>
				<tr>
					<th width="40"><p>Sl</p></th>
					<th width="100"><p>Company</p></th>
					<th width="100"><p>Buyer</p></th>
					<th width="90"><p>Job</p></th>
					<th width="100"><p>Style</p></th>
					<th width="100"><p>PO</p></th>
					<th width="70"><p>PO Date</p></th>
					<th width="70"><p>Order Qty[pcs]</p></th>
					<th width="70"><p>Unit Price</p></th>
					<th width="80"><p>Order Value</p></th>
					<th width="80"><p>Pur.Fab Rcv Qty</p></th>
					<th width="80"><p>Pur.Fab Issue Qty</p></th>
					<th width="80"><p>Yarn Issue Kg</p></th>
					<th width="80"><p>In-house Knitting Kg</p></th>
					<th width="80"><p>Sub-con Knitting Kg</p></th>
					<th width="80"><p>In-house Fab Dyeing Kg</p></th>
					<th width="80"><p>Sub-con Fab Dyeing Kg</p></th>
					<th width="80"><p>Grey Yarn Issue For YD Kg</p></th>
					<th width="80"><p>Dyed Yarn Receive (Kg)</p></th>
					<th width="80"><p>Sub-con AOP Kg</p></th>
					<th width="80"><p>Finish Fabric Delevery</p></th>
					<th width="80"><p>Cutting Section Received Kg</p></th>
					<th width="80"><p>In-House Sewing Kg</p></th>
					<th width="80"><p>Sub-con Sewing Kg</p></th>
					<th width="80"><p>Difference of Sewing minus Yarn / Fabric Issued Kg</p></th>
					<th width="80"><p>Shipment Kg</p></th>
					<th width="80"><p>Shipment Qty Pcs</p></th>
					<th width="80"><p>Unit Price</p></th>
					<th width="80"><p>Shipment Value</p></th>
					<th width="80"><p>Differnece Order minus Shipment Pcs</p></th>
					<th width="100"><p>Shipment Status</p></th>
				</tr>
			</thead>
		</table>
		<div style="width:<?=$tbl_width+20;?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" align="left"> 
				<tbody>
					<?
					$i=1;
					$tot_po_qty = 0;
					$tot_po_val = 0;
					$tot_po_qty = 0;
					$tot_inv_qty = 0;
					$tot_inv_val = 0;
					$tot_pur_ff_rcv_qty = 0;
					$tot_pur_ff_issue_qty = 0;
					$tot_in_knit_qty = 0;
					$tot_out_knit_qty = 0;
					$tot_yd_issue_qty = 0;
					$tot_yd_rcv_qty = 0;
					$tot_subcob_aop_qty = 0;
					$tot_ff_delv_qty = 0;
					$tot_cut_rcv_qty = 0;
					$tot_ff_issue_in_qty = 0;
					$tot_ff_issue_out_qty = 0;
					$tot_diff_of_sewing = 0;
					$tot_shipment_qty_kg = 0;
					$tot_order_to_ship_diff = 0;
					$tot_grey_yarn_iss_qty = 0;
					$tot_dye_in_qty = 0;
					$tot_dye_out_qty = 0;
					foreach ($res as $val) 
					{
						$inv_qty = $invice_data[$val['PO_ID']]['inv_qty'];
						$inv_value = $invice_data[$val['PO_ID']]['inv_value'];
						$inv_rate = $invice_data[$val['PO_ID']]['inv_rate'];

						$grey_yarn_iss_qty = $grey_yarn_data_array[$val['PO_ID']];

						$ff_pur_rcv_qty = $finfab_purses_data_array[$val['PO_ID']][1]['qty'];
						$ff_pur_issue_qty = $finfab_purses_data_array[$val['PO_ID']][2]['qty'];

						$yd_issue_qty = $yarn_dyeing_data_array[$val['PO_ID']]['issue_qty'];
						$yd_rcv_qty = $yarn_dyeing_data_array[$val['PO_ID']]['rcv_qty'];

						$in_knit_qty = $kitting_data_array[$val['PO_ID']][1];
						$out_knit_qty = $kitting_data_array[$val['PO_ID']][3];

						$dye_in_qty = $dyeing_data_array[$val['PO_ID']][1];
						$dye_out_qty = $dyeing_data_array[$val['PO_ID']][3];

						$subcob_aop_qty = $subcob_aop_data[$val['PO_ID']];
						$ff_delv_qty = $ff_delivery_data[$val['PO_ID']];
						$ff_issue_in_qty = $ff_issue_data[$val['PO_ID']][1];
						$ff_issue_out_qty = $ff_issue_data[$val['PO_ID']][3];
						$shipment_qty_kg = $po_wise_shipment_qty_arr[$val['PO_ID']];

						$diff_of_sewing = ($ff_issue_in_qty+$ff_issue_out_qty)-($ff_pur_issue_qty+$grey_yarn_iss_qty);
						// echo "(".$ff_issue_in_qty."+".$ff_issue_out_qty.")-(".$ff_pur_issue_qty."+".$grey_yarn_iss_qty.")<br>";
						$order_to_ship_diff = $val['PO_QUANTITY'] - $inv_qty;

						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><p><?=$i;?></p></td>
							<td width="100"><p><?=$company_short_arr[$company_id];?></p></td>
							<td width="100"><p><?=$buyer_array[$val['BUYER_NAME']];?></p></td>
							<td width="90"><p><?=$val['JOB_NO'];?></p></td>
							<td width="100"><p>&nbsp;<?=$val['STYLE_REF_NO'];?></p></td>
							<td width="100"><p>&nbsp;<?=$val['PO_NUMBER'];?></p></td>
							<td width="70" align="center"><p><?=change_date_format($val['PO_RECEIVED_DATE']);?></p></td>
							<td width="70" align="right"><p><?=number_format($val['PO_QUANTITY'],0);?></p></td>
							<td width="70" align="right"><p><?=number_format($val['UNIT_PRICE'],2);?></p></td>
							<td width="80" align="right"><p><?=number_format($val['PO_VAL'],2);?></p></td>
							<td width="80" align="right"><p><?=number_format($ff_pur_rcv_qty,2);?></p></td>
							<td width="80" align="right"><p><?=number_format($ff_pur_issue_qty,2);?></p></td>
							<td width="80" align="right"><p>
								<a href="javascript:void(0)" onclick="open_yarn_popup('<?=$val['PO_ID'];?>')">
									<?=number_format($grey_yarn_iss_qty,2);?>
								</a>
							</p></td>
							<td width="80" align="right"><p><?=number_format($in_knit_qty,2);?></p></td>
							<td width="80" align="right"><p><?=number_format($out_knit_qty,2);?></p></td>
							<td width="80" align="right"><p><?=number_format($dye_in_qty,2);?></p></td>
							<td width="80" align="right"><p><?=number_format($dye_out_qty,2);?></p></td>
							<td width="80" align="right"><p><?=number_format($yd_issue_qty,2);?></p></td>
							<td width="80" align="right"><p><?=number_format($yd_rcv_qty,2);?></p></td>
							<td width="80" align="right"><p><?=number_format($subcob_aop_qty,2);?></p></td>
							<td width="80" align="right"><p><?=number_format($ff_delv_qty,2);?></p></td>
							<td width="80" align="right"><p><?=number_format($dye_in_qty,2);?></p></td>
							<td width="80" align="right"><p><?=number_format($ff_issue_in_qty,2);?></p></td>
							<td width="80" align="right"><p><?=number_format($ff_issue_out_qty,2);?></p></td>
							<td width="80" align="right"><p><?=number_format($diff_of_sewing,2);?></p></td>
							<td width="80" align="right"><p><?=number_format($shipment_qty_kg,2);?></p></td>
							<td width="80" align="right"><p><?=number_format($inv_qty,0);?></p></td>
							<td width="80" align="right"><p><?=number_format($inv_rate,2);?></p></td>
							<td width="80" align="right"><p><?=number_format($inv_value,2);?></p></td>
							<td width="80" align="right"><p><?=number_format($order_to_ship_diff,0);?></p></td>
							<td width="100"><p><?=$shipment_status[$val['SHIPING_STATUS']];?></p></td>
						</tr>
						<?
						$i++;
						$tot_po_qty += $val['PO_QUANTITY'];
						$tot_po_val += $val['PO_VAL'];
						$tot_inv_qty += $inv_qty;
						$tot_inv_val += $inv_value;
						$tot_pur_ff_rcv_qty += $ff_pur_rcv_qty;
						$tot_pur_ff_issue_qty += $ff_pur_issue_qty;
						$tot_in_knit_qty += $in_knit_qty;
						$tot_out_knit_qty += $out_knit_qty;
						$tot_yd_issue_qty += $yd_issue_qty;
						$tot_yd_rcv_qty += $yd_rcv_qty;
						$tot_subcob_aop_qty += $subcob_aop_qty;
						$tot_ff_delv_qty += $ff_delv_qty;
						$tot_cut_rcv_qty += $dye_in_qty;
						$tot_ff_issue_in_qty += $ff_issue_in_qty;
						$tot_ff_issue_out_qty += $ff_issue_out_qty;
						$tot_tot_diff_of_sewing += $diff_of_sewing;
						$tot_shipment_qty_kg += $shipment_qty_kg;
						$tot_order_to_ship_diff += $order_to_ship_diff;
						$tot_grey_yarn_iss_qty += $grey_yarn_iss_qty;
						$tot_dye_in_qty += $dye_in_qty;
						$tot_dye_out_qty += $dye_out_qty;
					}
					?>
				</tbody>
			</table>
		</div>
		<table width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
			<tfoot>
				<tr>
					<th width="40"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="90"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="70">Total</th>
					<th width="70"><?=number_format($tot_po_qty,0);?></th>
					<th width="70"></th>
					<th width="80"><?=number_format($tot_po_val,2);?></th>
					<th width="80"><?=number_format($tot_pur_ff_rcv_qty,2);?></th>
					<th width="80"><?=number_format($tot_pur_ff_issue_qty,2);?></th>
					<th width="80"><?=number_format($tot_grey_yarn_iss_qty,2);?></th>
					<th width="80"><?=number_format($tot_in_knit_qty,2);?></th>
					<th width="80"><?=number_format($tot_out_knit_qty,2);?></th>
					<th width="80"><?=number_format($tot_dye_in_qty,2);?></th>
					<th width="80"><?=number_format($tot_dye_out_qty,2);?></th>
					<th width="80"><?=number_format($tot_yd_issue_qty,2);?></th>
					<th width="80"><?=number_format($tot_yd_rcv_qty,2);?></th>
					<th width="80"><?=number_format($tot_subcob_aop_qty,2);?></th>
					<th width="80"><?=number_format($tot_ff_delv_qty,2);?></th>
					<th width="80"><?=number_format($tot_cut_rcv_qty,2);?></th>
					<th width="80"><?=number_format($tot_ff_issue_in_qty,2);?></th>
					<th width="80"><?=number_format($tot_ff_issue_out_qty,2);?></th>
					<th width="80"><?=number_format($tot_tot_diff_of_sewing,2);?></th>
					<th width="80"><?=number_format($tot_shipment_qty_kg,2);?></th>
					<th width="80"><?=number_format($tot_inv_qty,0);?></th>
					<th width="80"></th>
					<th width="80"><?=number_format($tot_inv_val,2);?></th>
					<th width="80"><?=number_format($tot_order_to_ship_diff,2);?></th>
					<th width="100"></th>
				</tr>
			</tfoot>
		</table>
	</fieldset>
	<?

	// $html = ob_get_contents();
	// ob_clean();
	// foreach (glob("*.xls") as $filename) {
	// 	@unlink($filename);
	// }
	// $name=time();
	// $filename=$user_id."_".$name.".xls";
	// $create_new_doc = fopen($filename, 'w');	
	// $is_created = fwrite($create_new_doc, $html);
	// echo "$html####$filename####$rpt_type"; 
	exit();
}

if($action=="grey_yarn_issue_popup")
{
	echo load_html_head_contents("Grey Yarn Issue Popup", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$po_cond = " and b.po_breakdown_id=$po_id";
	$sqlGreyYArnIssue = "SELECT c.issue_number,c.issue_date,sum(b.quantity) as qty from inv_transaction a, order_wise_pro_details b,inv_issue_master c,product_details_master f where a.id=b.trans_id and a.prod_id=b.prod_id and f.id=a.prod_id and b.prod_id=f.id and c.id=a.mst_id and a.item_category=1 and a.transaction_type in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.dyed_type in(0,2) and f.item_category_id=1 $po_cond group by c.issue_number,c.issue_date order by c.issue_date";
	// echo $sqlGreyYArnIssue;die();
	$res = sql_select($sqlGreyYArnIssue);
	// print_r($res);
	?>
	<table cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" width="100%">
		<caption>Grey Yarn Issue</caption>
		<thead>
			<tr>
				<th width="50">Sl</th>
				<th width="120">Issue Number</th>
				<th width="80">Date</th>
				<th width="80">Qty</th>
			</tr>
		</thead>
		<tbody>
			<?
			$i=1;
			$tot_qty = 0;
			foreach ($res as $val) 
			{
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
				<tr bgcolor="<?=$bgcolor;?>">
					<td><?=$i;?></td>
					<td><?=$val['ISSUE_NUMBER'];?></td>
					<td align="center"><?=change_date_format($val['ISSUE_DATE']);?></td>
					<td align="right"><?=number_format($val['QTY'],2);?></td>
				</tr>
				<?
				$i++;
				$tot_qty += $val['QTY'];
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th><?=number_format($tot_qty,2);?></th>
			</tr>
		</tfoot>

	</table>
	<?
}

