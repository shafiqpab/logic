<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

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
if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store", 140, "select a.store_name,a.id from lib_store_location a,lib_store_location_category b where a.id = b.store_location_id and a.company_id='$data' and a.is_deleted=0  and a.status_active=1  and b.category_type =13 group by a.store_name,a.id order by a.store_name",'id,store_name', 1, '--- Select Store ---', 0, ""  );
}
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'order_and_booking_wise_grey_fabric_stock_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
$buyer_array=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;

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

if($action=="booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../../", 1, 1,'','','');
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
			name = name.substr( 0, name.length - 1 );
			
			
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
			//$('#hide_recv_id').val( rec_id );
			//$("#hide_booing_type").val(str[3]);
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
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Booking No</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
					   
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
								$search_by_arr=array(1=>"Booking No",2=>"Job No",3=>"Style Ref");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "1",$dd,0 );
							?>
							</td>     
							<td align="center" id="search_by_td">				
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
							</td> 	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_booking_no_search_list_view', 'search_div', 'order_and_booking_wise_grey_fabric_stock_report_controller', 'setFilterGrid(\'tbl_list_div\',-1)');" style="width:100px;" />
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
	$month_id=$data[5];
	
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

	if($search_by==3) 
	{
		 $search_field="a.style_ref_no";
	}
	else if($search_by==2)
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
	
	$sql= "select a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.booking_no,c.booking_no_prefix_num,c.id as booking_id  from wo_po_details_master a,wo_booking_dtls b ,wo_booking_mst c where a.job_no=b.job_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.booking_type in(1,4) and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond $month_cond group by a.job_no,b.booking_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,c.id,c.booking_no_prefix_num  order by a.job_no desc";
	
	//echo $sql;die;	
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
					<th width="">Booking No</th>
					
					</thead>
				</table>
				<div style="width:670px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_div">
					<?
					$i=1;
					foreach($sqlResult as $row )
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						
						$data=$i.'_'.$row[csf('booking_id')].'_'.$row[csf('booking_no_prefix_num')];
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

if ($action=="order_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data); 
	?>	
	<script>
		
		function js_set_value(str)
		{
			var splitData = str.split("_");
		//alert (splitData[1]);
		$("#order_no_id").val(splitData[0]); 
		$("#order_no_val").val(splitData[1]); 
		parent.emailwindow.hide();
	}
	
	</script>
	<input type="hidden" id="order_no_id" />
	<input type="hidden" id="order_no_val" />
	<?
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and b.buyer_name=$data[1]";
	if ($data[2]=="") $order_no=""; else $order_no=" and a.po_number=$data[2]";
	$job_no=str_replace("'","",$txt_job_id);
	if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and b.job_no_prefix_num='$data[2]'";

	$sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a  where b.job_no=a.job_no_mst and b.company_name=$data[0] and b.is_deleted=0 $buyer_name $job_no_cond ORDER BY b.job_no DESC";
		//echo $sql;
	$arr=array(1=>$buyer_arr);

	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "order_and_booking_wise_grey_fabric_stock_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	disconnect($con);
	exit();
}

$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
//$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
//$machine_arr=return_library_array( "select id, dia_width from lib_machine_name", "id", "dia_width"  );

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$rpt_type=str_replace("'","",$rpt_type);
	$txt_search_comm=str_replace("'","",$txt_search_comm);
	$booking_no=str_replace("'","",$txt_booking_no);
	$cbo_value_with=str_replace("'","",$cbo_value_with);
	$composition_arr=array();
	
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
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
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$row[csf('construction')];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;				
			}
		}
	}
	unset($data_array);
	
	$transaction_date_array=array();
	$sql_date="Select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where status_active=1 and is_deleted=0 and item_category=13 group by prod_id";
	$sql_date_result=sql_select($sql_date);
	foreach( $sql_date_result as $row )
	{
		$transaction_date_array[$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
		$transaction_date_array[$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
	}
	unset($sql_date_result);

	
	
	if($rpt_type==2)
	{		
		if($booking_no!='') $book_no_cond="and a.booking_no_prefix_num in($booking_no)";else $book_no_cond="";
		
		$sql="select a.buyer_id,b.job_no,b.po_break_down_id as po_id,b.construction,b.fabric_color_id, sum(b.grey_fab_qnty) as grey_req_qnty,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $book_no_cond group by a.buyer_id,b.job_no,b.po_break_down_id,b.construction,b.fabric_color_id,a.booking_no";

		$sql_result=sql_select($sql);
		$po_ids='';

		foreach( $sql_result as $row )
		{
			$key=$row[csf('buyer_id')].$row[csf('job_no')].$row[csf('po_id')].$row[csf('construction')].$row[csf('fabric_color_id')];//$color_arr[
			$grey_qnty_array[$key]+=$row[csf('grey_req_qnty')];
			
			if($po_ids=='') $po_ids=$row[csf('po_id')];else $po_ids.=",".$row[csf('po_id')];
			$booking_array[$row[csf('po_id')]]['booking_no'].=$row[csf('booking_no')].',';
			//$booking_array[$row[csf('po_id')]]['booking_no']=$row[csf('booking_no')];
		}
		unset($sql_result);

		$po_idss=implode(",",array_unique(explode(",",$po_ids)));

		if($booking_no!='') 
			{
				if($po_ids!='') 
				{
					$po_id_cond="and a.id in($po_idss)";
					$po_id_cond_c="and c.id in($po_idss)";
				}
				else {
					$po_id_cond="";
					$po_id_cond_c="";
				}
			}
	}
	
 	//print_r($booking_array);
    //echo $po_id_cond;
	
	if(str_replace("'","",$cbo_buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and b.buyer_name=$cbo_buyer_id";//.str_replace("'","",$cbo_buyer_name)
	}
	
	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and b.job_no_prefix_num in ($job_no) ";
	$year_id=str_replace("'","",$cbo_year);

	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(b.insert_date)=$year_id"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_cond=" and TO_CHAR(b.insert_date,'YYYY')=$year_id"; else $year_cond="";
	}
	
	/*$search_cond='';
	if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.po_number LIKE '$txt_search_comm%'";
	}
	
	else
	{
		$search_cond.="";
	}*/
	if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.po_number LIKE '$txt_search_comm%'";
	
	$order_cond="";
	
	if($rpt_type==2)
	{
		if($db_type==0)
		{
			$po_id_con="distinct(a.id) as po_id";
			$program_no_array=return_library_array( "select po_id, group_concat(distinct(dtls_id)) as dtls_id from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 group by po_id", "po_id", "dtls_id"  );
		}
		else
		{
			$po_id_con="LISTAGG(a.id, ',') WITHIN GROUP (ORDER BY a.id) as po_id ";
			$program_no_array=return_library_array( "select po_id, LISTAGG(dtls_id, ',') WITHIN GROUP (ORDER BY dtls_id) as dtls_id from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 group by po_id", "po_id", "dtls_id"  );	
		}
		/*if($booking_nos!="")
		{
			$sql_po=sql_select("select a.id as po_id from wo_po_break_down a, wo_booking_dtls b where b.job_no=a.job_no_mst  and b.booking_no in($booking_nos) and b.status_active=1 and b.is_deleted=0");
			$po_ids='';
			foreach($sql_po as $poID)
			{
				if($po_ids=='') $po_ids=$poID[csf('po_id')];else $po_ids.=",".$poID[csf('po_id')];
					$booking_array[$poID[csf('po_id')]]['booking_no'].=$row[csf('booking_no')].',';
			}
		
		}*/
		//echo $po_idss;
	
		/*if($db_type==0)
		{
			echo "select b.po_id, group_concat(distinct(dtls_id)) as dtls_id from wo_po_break_down b where status_active=1 and is_deleted=0 group by po_id";
			$program_no_array=return_library_array( "select b.po_id, group_concat(distinct(dtls_id)) as dtls_id from wo_po_break_down b where status_active=1 and is_deleted=0 group by po_id", "po_id", "dtls_id"  );
		}
		else
		{
			$program_no_array=return_library_array( "select po_id, LISTAGG(dtls_id, ',') WITHIN GROUP (ORDER BY dtls_id) as dtls_id from wo_po_break_down b where status_active=1 and is_deleted=0 group by po_id", "po_id", "dtls_id"  );	
		}*/
		if( str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.transaction_date <=".$txt_date_from."";
		if( str_replace("'","",$cbo_store)==0) $store_cond=""; else $store_cond= " and a.store_id=".$cbo_store."";
		
		$product_array=array();	
		$prod_query="Select id, detarmination_id, gsm, dia_width, brand from product_details_master where item_category_id=13 and company_id=$cbo_company_id and status_active=1 and is_deleted=0 ";
		$prod_query_sql=sql_select($prod_query);
		foreach( $prod_query_sql as $row )
		{
			$product_array[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
			$product_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
			$product_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
			$product_array[$row[csf('id')]]['brand']=$row[csf('brand')];
		}
		
		$product_id_arr=array(); $recvIssue_array=array(); $trans_arr=array();
		$sql_trans="SELECT b.trans_type, b.po_breakdown_id, b.prod_id, sum(b.quantity) as qnty 
		from inv_transaction a, order_wise_pro_details b 
		where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2,22,13,16,45,51,58,61,80,81,82,83,84,110) and a.item_category=13 and a.transaction_type in(1,2,3,4,5,6) and b.trans_type  in(1,2,3,4,5,6) $trans_date $store_cond
		group by b.trans_type, b.po_breakdown_id, b.prod_id";
		$result_trans=sql_select( $sql_trans );
		foreach ($result_trans as $row)
		{
			$recvIssue_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('trans_type')]]+=$row[csf('qnty')];
			$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
			//$recvIssue_array[$row[csf('po_breakdown_id')]][$row[csf('trans_type')]]+=$row[csf('qnty')];
			//$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
		}
	
		/*$sql_transfer_in="select a.transaction_type, a.order_id, a.prod_id, sum(a.cons_quantity) as trans_qnty from inv_transaction a, inv_item_transfer_mst b where a.mst_id=b.id and b.transfer_criteria=4 and b.item_category=13 and a.item_category=13 and a.transaction_type in(5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $trans_date group by a.transaction_type,a.order_id,a.prod_id";
		$data_transfer_in_array=sql_select($sql_transfer_in);
		foreach( $data_transfer_in_array as $row )
		{
			$trans_arr[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('transaction_type')]]=$row[csf('trans_qnty')];
			$product_id_arr[$row[csf('order_id')]].=$row[csf('prod_id')].",";
		}*/
		//print_r($trans_arr[3593]);
		ob_start();
		?>
        <style type="text/css">
        	.word_break_wrap{
				word-break:break-all;
				word-wrap:break-word;
				}
        </style>
		<fieldset style="width:2000px">
			<table cellpadding="0" cellspacing="0" width="1810">
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="22" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="22" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="22" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="2000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
				<thead>
					<tr>
						<th width="30" rowspan="2">SL</th>

						<th colspan="13">Fabric Details</th>
						<th colspan="3">Used Yarn Details</th>
						<th colspan="4">Receive Details</th>
						<th colspan="4">Issue Details</th>
						<th colspan="2">Stock Details</th>
					</tr>
					<tr>
						<th width="80">Job No</th>
						<th width="80">Buyer</th>
						<th width="100">Order No</th>
						<th width="100">Style No</th>
						<th width="90">Booking No</th>
						<th width="50">Product ID</th>
						<th width="180">Const. & Comp</th>

						<th width="40">GSM</th>
						<th width="80">Color Range</th>
						<th width="60">Dyeing Color</th>
						<th width="40">M/Dia</th>
						<th width="40">F/Dia</th>
						<th width="40">Stich Length</th>
						 	 	
						<th width="60">Y. Count</th>
						<th width="60">Y. Brand</th>
						<th width="60">Y. Lot</th>
						
						<th width="80">Recv. Qty.</th>
						<th width="80">Issue Return Qty.</th>
						<th width="80">Transf. In Qty.</th>
						<th width="80">Total Recv.</th>
						<th width="80">Issue Qty.</th>
						<th width="80">Receive Return Qty.</th>
						<th width="80">Transf. Out Qty.</th>
						<th width="80">Total Issue</th>
						<th width="80">Stock Qty.</th>
						<th>DOH</th>
					</tr>
				</thead>
			</table>
			<div style="width:2018px; overflow-y: scroll; max-height:280px;" id="scroll_body">
				<table width="2000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" align="left"> 
				<?
					$sql="SELECT b.job_no, b.buyer_name, b.style_ref_no, a.id, a.po_number, a.file_no, a.grouping, a.pub_shipment_date, sum(a.po_quantity) as po_quantity,c.booking_no
					from wo_po_break_down a ,wo_po_details_master b, wo_booking_mst c,wo_booking_dtls d 
					where b.job_no=a.job_no_mst  
					and a.id=d.po_break_down_id and c.booking_no=d.booking_no and d.booking_type in(1,4) and
					b.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  
					$year_cond $buyer_id_cond $job_no_cond $search_cond $order_cond $order_cond $po_id_cond  
					group by  b.job_no, b.buyer_name, b.style_ref_no, a.id, a.po_number, a.file_no, a.grouping, a.pub_shipment_date,c.booking_no
					order by a.id, a.pub_shipment_date ";
					//wo_booking_mst a, wo_booking_dtls
					$bookingDatas=sql_select($sql);
					$bookingDataArr=array();$poIDs="";$tot_rows=0;
					foreach($bookingDatas as $row)
					{
						$tot_rows++;
						$poIDs.=$row[csf('id')].",";
						$bookingNos.="'".$row[csf('booking_no')]."',";
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["booking_no"]=$row[csf('booking_no')];
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["id"]=$row[csf('id')];
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["job_no"]=$row[csf('job_no')];
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["buyer_name"]=$row[csf('buyer_name')];
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["style_ref_no"]=$row[csf('style_ref_no')];
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["po_number"]=$row[csf('po_number')];
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["file_no"]=$row[csf('file_no')];
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["grouping"]=$row[csf('grouping')];
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["pub_shipment_date"]=$row[csf('pub_shipment_date')];
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["po_quantity"]=$row[csf('po_quantity')];
						
					}
					$poIDs=chop($poIDs,",");
					$bookingNos=chop($bookingNos,",");
				    $poIDs=implode(",",array_filter(array_unique(explode(",",$poIDs))));
				    $bookingNos=implode(",",array_filter(array_unique(explode(",",$bookingNos))));
				    if($poIDs!="")
				    {
				        $poIDs=explode(",",$poIDs);  
				        $po_ids_chnk=array_chunk($poIDs,999);
				        $po_ids_cond=" and";
				        foreach($po_ids_chnk as $dtls_id)
				        {
				        if($po_ids_cond==" and")  $po_ids_cond.="(a.po_break_down_id in(".implode(',',$dtls_id).")"; else $po_ids_cond.=" or a.po_break_down_id in(".implode(',',$dtls_id).")";
				        }
				        $po_ids_cond.=")";
				        //echo $po_ids_cond;die;
				    }
				    if($bookingNos!="")
				    {
				        $bookingNos=explode(",",$bookingNos);  
				        $bookingNos_chnk=array_chunk($bookingNos,999);
				        $bookingNos_chnk_cond=" and";
				        foreach($bookingNos_chnk as $dtls_id)
				        {
				        if($bookingNos_chnk_cond==" and")  $bookingNos_chnk_cond.="(a.booking_no in(".implode(',',$dtls_id).")"; else $bookingNos_chnk_cond.=" or a.booking_no in(".implode(',',$dtls_id).")";
				        }
				        $bookingNos_chnk_cond.=")";
				        //echo $bookingNos_chnk_cond;die;
				    }

					/*	$sqlData="select e.recv_number,e.knitting_source,e.booking_no as knitting_challan_no,e.receive_date, sum(c.quantity) as quantity,
					listagg(d.stitch_length,',') within group (order by d.stitch_length) as stitch_length, d.yarn_lot,c.po_breakdown_id 
					from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e 
					where a.job_no_mst=b.job_no and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0  
					and c.po_breakdown_id=a.id and c.dtls_id=d.id and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 and  e.item_category=13 and e.status_active=1 and e.is_deleted=0 
					and a.id=$orderID and e.company_id=$companyID and c.prod_id=$prodID and c.entry_form in(2,22,58) 
					group by e.recv_number,e.knitting_source,e.booking_no,e.receive_date, d.yarn_lot,c.po_breakdown_id order by c.prod_id";*/



					$sql_dtls_qry="SELECT a.po_break_down_id, a.booking_no, c.yarn_lot, c.yarn_count,c.brand_id,c.stitch_length, c.prod_id,c.yarn_prod_id,c.color_range_id,c.color_id,c.machine_dia 
					from wo_booking_dtls a,pro_grey_prod_entry_dtls c,order_wise_pro_details d 
					where a.po_break_down_id=d.po_breakdown_id and c.prod_id=d.prod_id  and c.id=d.dtls_id and a.booking_type in(1,4) $po_ids_cond $bookingNos_chnk_cond
					and a.status_active=1 and a.is_deleted=0  and d.trans_type=1 and c.status_active=1 and c.is_deleted=0 and d.entry_form in(2) 
					and d.status_active=1 and d.is_deleted=0
					group by a.po_break_down_id, a.booking_no,c.yarn_lot,c.yarn_count,c.brand_id,c.stitch_length,c.prod_id,c.yarn_prod_id,c.color_range_id ,c.color_id,c.machine_dia";

					$yarn_prodids="";
					$nameArray=sql_select( $sql_dtls_qry );
					foreach ($nameArray as $row)
					{
						$yarn_prodids.=$row[csf('yarn_prod_id')].",";
						$yarn_count_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_count'] .=$row[csf('yarn_count')].",";
						$yarn_lot_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_lot'] .=$row[csf('yarn_lot')].",";
						$yarn_brand_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_prod_id'] .=$row[csf('yarn_prod_id')].",";
						$yarn_stich_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['stitch_length']=$row[csf('stitch_length')];
						$yarn_color_range_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['color_range_id']=$row[csf('color_range_id')];
						$yarn_color_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['color_id']=$row[csf('color_id')];
						$yarn_mdia_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['machine_dia']=$row[csf('machine_dia')];
					}
					$yarn_prodids=chop($yarn_prodids,",");
				    $yarn_prodids=implode(",",array_filter(array_unique(explode(",",$yarn_prodids))));
				    if($yarn_prodids!="")
				    {
				        $yarn_prodids=explode(",",$yarn_prodids);  
				        $prodids_chnk=array_chunk($yarn_prodids,999);
				        $prodids_cond=" and";
				        foreach($prodids_chnk as $dtls_id)
				        {
				        if($prodids_cond==" and")  $prodids_cond.="(a.id in(".implode(',',$dtls_id).")"; else $prodids_cond.=" or a.id in(".implode(',',$dtls_id).")";
				        }
				        $prodids_cond.=")";
				        //echo $po_ids_cond;die;
				    }
					$yarn_prod_sql=sql_select("select a.id, b.brand_name from product_details_master a, lib_brand b where a.brand = b.id and a.item_category_id = 1 and a.company_id=$cbo_company_id $prodids_cond");
					foreach($yarn_prod_sql as $row)
					{
						$yarn_brand_ref[$row[csf('id')]] .=$row[csf('brand_name')];
					}
					//echo $sql;
					//$result=sql_select( $sql );
					$i=1; $tot_recv_qty=0; $tot_iss_qty=0; $tot_trans_in_qty=0; $tot_trans_out_qty=0; $grand_tot_recv_qty=0; $grand_tot_iss_qty=0; $grand_stock_qty=0;
					$sub_tot_recv_qty=0; $sub_tot_iss_qty=0; $sub_tot_trans_in_qty=0; $sub_tot_trans_out_qty=0; $sub_grand_tot_recv_qty=0; $sub_grand_tot_iss_qty=0; $sub_grand_stock_qty=0;


					foreach($bookingDataArr as $bookingNum => $bookingData)
					{
						$show= false;
						foreach($bookingData as $po_id => $row)
						{
							$dataProd=array_filter(array_unique(explode(",",substr($product_id_arr[$po_id],0,-1))));
							if(count($dataProd)>0)
							{
								$order_recv_qty=0; $order_iss_ret_qty=0; $order_iss_qty=0; $order_rec_ret_qty=0; $order_trans_in_qty=0; $order_trans_out_qty=0; $order_tot_recv_qnty=0; $order_tot_iss_qnty=0; $order_stock_qnty=0;$p=1;
								foreach($dataProd as $prodId)
								{
								
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
									$recv_qty=$recvIssue_array[$po_id][$prodId][1];
									$iss_qty=$recvIssue_array[$po_id][$prodId][2];
									$iss_ret_qty=$recvIssue_array[$po_id][$prodId][4];
									$recv_ret_qty=$recvIssue_array[$po_id][$prodId][3];
									$trans_in_qty=$recvIssue_array[$po_id][$prodId][5];
									$trans_out_qty=$recvIssue_array[$po_id][$prodId][6];
									$recv_tot_qty=$recv_qty+$iss_ret_qty+$trans_in_qty;
									$iss_tot_qty=$iss_qty+$recv_ret_qty+$trans_out_qty;
									$stock_qty = $recv_tot_qty-$iss_tot_qty;
									//echo $trans_in_qty.', '.$trans_out_qty;
									$bookingNo=rtrim($booking_array[$po_id]['booking_no'],',');
									//$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
									$booking_no=implode(",",array_unique(explode(",",$bookingNo)));
									$yarnCount=$yarn_count_arr[$po_id][$row['booking_no']][$prodId]['yarn_count'];
									$yarnLot=$yarn_lot_arr[$po_id][$row['booking_no']][$prodId]['yarn_lot'];
									$yarnBrand=$yarn_brand_arr[$po_id][$row['booking_no']][$prodId]['yarn_prod_id'];
									$yarnStitch=$yarn_stich_arr[$po_id][$row['booking_no']][$prodId]['stitch_length'];
									$colorRangeId=$yarn_color_range_arr[$po_id][$row['booking_no']][$prodId]['color_range_id'];
									$colorId=$yarn_color_arr[$po_id][$row['booking_no']][$prodId]['color_id'];
									$machineDia=$yarn_mdia_arr[$po_id][$row['booking_no']][$prodId]['machine_dia'];


									$yarn_count_value ="";
									$yarnCountARR=array_filter(array_unique(explode(",",chop($yarnCount,","))));
									foreach ($yarnCountARR as  $yval) 
									{
										$yarn_count_value .= $count_arr[$yval].",";
									}
									$yarn_count_value=chop($yarn_count_value,",");

									$yarn_brand_value ="";
									$yarnBrandARR=array_filter(array_unique(explode(",",chop($yarnBrand,","))));
									foreach ($yarnBrandARR as  $yBval) 
									{
										$yarn_brand_value .= $yarn_brand_ref[$yBval].",";
									}
									$yarn_brand_value=chop($yarn_brand_value,",");

									$color_value ="";
									$colorARR=array_filter(array_unique(explode(",",chop($colorId,","))));
									foreach ($colorARR as  $yBval) 
									{
										$color_value .= $color_arr[$yBval].",";
									}
									$color_value=chop($color_value,",");

									$yarnLott= implode(",",array_filter(array_unique(explode(",",chop($yarnLot,",")))));

									
									if($cbo_value_with==1 || ($cbo_value_with==2 && number_format($stock_qty,4) > 0))
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">

											<td width="30"><? echo $i;//echo "==".$row[csf('id')]."=".$prodId; ?></td>
											<td width="80"><p class="word_break_wrap"><? echo $row['job_no']; ?>&nbsp;</p></td>
											<td width="80"><p class="word_break_wrap"><? echo $buyer_arr[$row['buyer_name']]; ?>&nbsp;</p></td>
											<td width="100"><p class="word_break_wrap"><? echo $row['po_number']; ?>&nbsp;</p></td>
											<td width="100"><p class="word_break_wrap"><? echo $row['style_ref_no']; ?>&nbsp;</p></td>
											<td width="90"><p class="word_break_wrap"><? echo $row['booking_no']; ?>&nbsp;</p></td>
											<td width="50"><p><? echo $prodId; ?></p></td>
											<td width="180"><p class="word_break_wrap"><? echo $composition_arr[$product_array[$prodId]['detarmination_id']]; ?></p></td>
											<td width="40"><p><? echo $product_array[$prodId]['gsm']; ?>&nbsp;</p></td>
											<td width="80"><p><? echo $color_range[$colorRangeId]; ?>&nbsp;</p></td>
											<td width="60"><p><? echo $color_value; ?>&nbsp;</p></td>
											<td width="40"><p><? echo $machineDia; ?>&nbsp;</p></td>
											<td width="40"><p><? echo $product_array[$prodId]['dia_width']; ?>&nbsp; <input type="hidden" name="" value="<? echo $yarnStitch; ?>"></p></td>
											<td width="40"><p><? echo $yarnStitch; ?>&nbsp;</p></td>
											
											<td width="60"><p><? echo $yarn_count_value; //$count_val; ?>&nbsp;</p></td>
											<td width="60"><p><? echo $yarn_brand_value; ?>&nbsp;</p></td>
											<td width="60"><p><? echo $yarnLott; ?>&nbsp;</p></td>
											
											<td width="80" align="right"><a href='#report_details' onClick="openmypage_delivery_all('<? echo $po_id; ?>','<? echo $row['booking_no']; ?>','<? echo $prodId; ?>','<? echo $yarn_brand_value; ?>','<? echo $yarnLott; ?>','<? echo $yarnStitch; ?>','950px','report_all_popup',1);"><? echo number_format($recv_qty,2); ?></a>
											</td>
											<td width="80" align="right"><a href='#report_details' onClick="openmypage_delivery_all('<? echo $po_id; ?>','<? echo $row['booking_no']; ?>','<? echo $prodId; ?>','<? echo $yarn_brand_value; ?>','<? echo $yarnLott; ?>','<? echo $yarnStitch; ?>','450px','report_all_popup',2);"><? echo number_format($iss_ret_qty,2); ?></a>
											</td>
											<td width="80" align="right"><a href='#report_details' onClick="openmypage_delivery_all('<? echo $po_id; ?>','<? echo $row['booking_no']; ?>','<? echo $prodId; ?>','<? echo $yarn_brand_value; ?>','<? echo $yarnLott; ?>','<? echo $yarnStitch; ?>','650px','report_all_popup',3);"><? echo number_format($trans_in_qty,2); ?></a>
											</td>
											<td width="80" align="right"><? echo number_format($recv_tot_qty,2); ?></td>
											<td width="80" align="right"><a href='#report_details' onClick="openmypage_delivery_all('<? echo $po_id; ?>','<? echo $row['booking_no']; ?>','<? echo $prodId; ?>','<? echo $yarn_brand_value; ?>','<? echo $yarnLott; ?>','<? echo $yarnStitch; ?>','850px','report_all_popup',4);"><? echo number_format($iss_qty,2); ?></a></p></td>
											<td width="80" align="right"><? echo number_format($recv_ret_qty,2); ?></p></td>
											<td width="80" align="right"><a href='#report_details' onClick="openmypage_delivery_all('<? echo $po_id; ?>','<? echo $row['booking_no']; ?>','<? echo $prodId; ?>','<? echo $yarn_brand_value; ?>','<? echo $yarnLott; ?>','<? echo $yarnStitch; ?>','650px','report_all_popup',5);"><? echo number_format($trans_out_qty,2); ?></a>	
												</td>
											<td width="80" align="right"><? echo number_format($iss_tot_qty,2); ?></td>
											<td width="80" align="right"><a href='#report_details' onClick="openmypage_delivery_all('<? echo $po_id; ?>','<? echo $row['booking_no']; ?>','<? echo $prodId; ?>','<? echo $yarn_brand_value; ?>','<? echo $yarnLott; ?>','<? echo $yarnStitch; ?>','650px','report_all_popup',6);"><? echo number_format($stock_qty,2); ?></a></td>
											<? $daysOnHand=datediff("d",change_date_format($transaction_date_array[$prodId]['max_date'],'','',1),date("Y-m-d")); ?>
											<td align="center"><? if($stock_qty>0) echo $daysOnHand; ?>&nbsp;</td>
										</tr>
										<?	
										//==
										$sub_booking .= $booking_no.",";
	                    				$sub_tot_recv_qty+=$recv_qty; 
										$sub_tot_iss_ret_qty+=$iss_ret_qty; 
										$sub_tot_iss_qty+=$iss_qty; 
										$sub_tot_rec_ret_qty+=$recv_ret_qty; 
										$sub_tot_trans_in_qty+=$trans_in_qty; 
										$sub_tot_trans_out_qty+=$trans_out_qty; 
										$sub_grand_tot_recv_qty+=$recv_tot_qty; 
										$sub_grand_tot_iss_qty+=$iss_tot_qty;
										$sub_grand_stock_qty+=$stock_qty;
	                					//==
										$i++;
										
										$order_recv_qty+=$recv_qty;
										$order_iss_ret_qty+=$iss_ret_qty;  
										$order_iss_qty+=$iss_qty;
										$order_rec_ret_qty+=$recv_ret_qty; 
										$order_trans_in_qty+=$trans_in_qty; 
										$order_trans_out_qty+=$trans_out_qty; 
										$order_tot_recv_qnty+=$recv_tot_qty; 
										$order_tot_iss_qnty+=$iss_tot_qty;
										$order_stock_qnty+=$stock_qty;
										
										$tot_recv_qty+=$recv_qty; 
										$tot_iss_ret_qty+=$iss_ret_qty; 
										$tot_iss_qty+=$iss_qty; 
										$tot_rec_ret_qty+=$recv_ret_qty; 
										$tot_trans_in_qty+=$trans_in_qty; 
										$tot_trans_out_qty+=$trans_out_qty; 
										$grand_tot_recv_qty+=$recv_tot_qty; 
										$grand_tot_iss_qty+=$iss_tot_qty;
										$grand_stock_qty+=$stock_qty;
										$show = true;
									}
								}
							}
						}
						if($show == true)
						{
							if ($sub_tot_recv_qty>0 || $sub_tot_iss_ret_qty>0 || $sub_tot_trans_in_qty>0 || $sub_grand_tot_recv_qty>0 || $sub_tot_iss_qty>0 || $sub_tot_rec_ret_qty>0 || $sub_tot_trans_out_qty>0|| $sub_grand_tot_iss_qty>0|| $sub_grand_stock_qty>0) 
							{
								?>
								<tr style="font-weight: bold; background-color: grey;">	
					                <td colspan="17" align="right">Booking Total <? //echo $unit_of_measurement[$row[csf('uom')]]; ?> </td>
			                		<td width="80" align="right"><? echo number_format($sub_tot_recv_qty,2); ?></td>
			                		<td width="80" align="right"><? echo number_format($sub_tot_iss_ret_qty,2); ?></td>  
			                		<td width="80" align="right"><? echo number_format($sub_tot_trans_in_qty,2); ?></td>  
			                		<td width="80" align="right"><? echo number_format($sub_grand_tot_recv_qty,2); ?></td>
			                		<td width="80" align="right"><? echo number_format($sub_tot_iss_qty,2); ?></td>  
			                		<td width="80" align="right"><? echo number_format($sub_tot_rec_ret_qty,2); ?></td>  
			                		<td width="80" align="right"><? echo number_format($sub_tot_trans_out_qty,2); ?></td>  
			                		<td width="80" align="right"><? echo number_format($sub_grand_tot_iss_qty,2); ?></td>  
			                		<td width="80" align="right"><? echo number_format($sub_grand_stock_qty,2); ?></td>  
			                		<td align="right"></td>  

					            </tr>
							<?
							}
						}

						$sub_tot_recv_qty=0; $sub_tot_iss_qty=0; $sub_tot_trans_in_qty=0; $sub_tot_trans_out_qty=0; $sub_grand_tot_recv_qty=0; $sub_grand_tot_iss_qty=0; $sub_grand_stock_qty=0;$sub_tot_iss_ret_qty=0;$sub_tot_rec_ret_qty=0;
					}
					?>
				</table>
			</div>
			<table width="2000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body_bottom" align="left"> 
				<tfoot>
					<tr>
                    
                    	<th width="30">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th align="right" width="50">&nbsp;</th>
						<th align="right" width="180">&nbsp;</th>
						<th align="right" width="40">&nbsp;</th>
						<th align="right" width="80">&nbsp;</th>
						<th align="right" width="60">&nbsp;</th>
						<th align="right" width="40">&nbsp;</th>
						<th align="right" width="40">&nbsp;</th>
						<th align="right" width="40">&nbsp;</th>
						<th align="right" width="60">&nbsp;</th>
						<th align="right" width="60">&nbsp;</th>
						<th align="right" width="60">&nbsp;</th>
						<th align="right" width="80" id="value_tot_recv_qty"><p><? echo number_format($tot_recv_qty,2,'.',''); ?></p></th>
						<th align="right" width="80" id="value_tot_iss_ret_qty"><p><? echo number_format($tot_iss_ret_qty,2,'.',''); ?></p></th>
						<th align="right" width="80" id="value_tot_trans_in_qty"><p><? echo number_format($tot_trans_in_qty,2,'.',''); ?></p></th>
						<th align="right" width="80" id="value_grand_tot_recv_qty"><p><? echo number_format($grand_tot_recv_qty,2,'.',''); ?></p></th>
						<th align="right" width="80" id="value_tot_iss_qty"><p><? echo number_format($tot_iss_qty,2,'.',''); ?></p></th>
						<th align="right" width="80" id="value_tot_rec_ret_qty"><p><? echo number_format($tot_rec_ret_qty,2,'.',''); ?></p></th>
						<th align="right" width="80" id="value_tot_trans_out_qty"><p><? echo number_format($tot_trans_out_qty,2,'.',''); ?></p></th>
						<th align="right" width="80" id="value_grand_tot_iss_qty"><p><? echo number_format($grand_tot_iss_qty,2,'.',''); ?></p></th>
						<th align="right" width="80" id="value_grand_stock_qty"><p><? echo number_format($grand_stock_qty,2,'.',''); ?></p></th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</fieldset>
		<?
	}
	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html####$filename####$rpt_type"; 
	exit();
}

if($action=="report_generate_2")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$rpt_type=str_replace("'","",$rpt_type);
	$txt_search_comm=str_replace("'","",$txt_search_comm);
	$booking_no=str_replace("'","",$txt_booking_no);
	$cbo_value_with=str_replace("'","",$cbo_value_with);
	$composition_arr=array();
	
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.INSERT_DATE>'31-Dec-2019 11:59:59 PM'";

	// echo $sql_deter;


	$data_array=sql_select($sql_deter);
	
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
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
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$row[csf('construction')];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;				
			}
		}
	}
	unset($data_array);
	
	$transaction_date_array=array();
	$sql_date="Select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where status_active=1 and is_deleted=0 and item_category=13 group by prod_id";
	$sql_date_result=sql_select($sql_date);
	foreach( $sql_date_result as $row )
	{
		$transaction_date_array[$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
		$transaction_date_array[$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
	}
	unset($sql_date_result);

	
	
	if($rpt_type==3)
	{		
		if($booking_no!='') $book_no_cond="and a.booking_no_prefix_num in($booking_no)";else $book_no_cond="";
		
		$sql="select a.buyer_id,b.job_no,b.po_break_down_id as po_id,b.construction,b.fabric_color_id, sum(b.grey_fab_qnty) as grey_req_qnty,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $book_no_cond  and a.BOOKING_DATE>'31-Dec-2019' group by a.buyer_id,b.job_no,b.po_break_down_id,b.construction,b.fabric_color_id,a.booking_no";

		// echo $sql;

		$sql_result=sql_select($sql);
		$po_ids='';

		foreach( $sql_result as $row )
		{
			$key=$row[csf('buyer_id')].$row[csf('job_no')].$row[csf('po_id')].$row[csf('construction')].$row[csf('fabric_color_id')];//$color_arr[
			$grey_qnty_array[$key]+=$row[csf('grey_req_qnty')];
			
			if($po_ids=='') $po_ids=$row[csf('po_id')];else $po_ids.=",".$row[csf('po_id')];
			$booking_array[$row[csf('po_id')]]['booking_no'].=$row[csf('booking_no')].',';
			//$booking_array[$row[csf('po_id')]]['booking_no']=$row[csf('booking_no')];
		}
		unset($sql_result);

		$po_idss=implode(",",array_unique(explode(",",$po_ids)));

		if($booking_no!='') 
			{
				if($po_ids!='') 
				{
					$po_id_cond="and a.id in($po_idss)";
					$po_id_cond_c="and c.id in($po_idss)";
				}
				else {
					$po_id_cond="";
					$po_id_cond_c="";
				}
			}
	}
	
 	//print_r($booking_array);
    //echo $po_id_cond;
	
	if(str_replace("'","",$cbo_buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and b.buyer_name=$cbo_buyer_id";//.str_replace("'","",$cbo_buyer_name)
	}
	
	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and b.job_no_prefix_num in ($job_no) ";
	$year_id=str_replace("'","",$cbo_year);

	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(b.insert_date)=$year_id and year(b.insert_date)>'2023' "; else $year_cond="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_cond=" and TO_CHAR(b.insert_date,'YYYY')=$year_id and TO_CHAR(b.insert_date,'YYYY')>'2019'"; else $year_cond="";
	}
	
	/*$search_cond='';
	if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.po_number LIKE '$txt_search_comm%'";
	}
	
	else
	{
		$search_cond.="";
	}*/
	if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.po_number LIKE '$txt_search_comm%'";
	
	$order_cond="";
	
	if($rpt_type==3)
	{
		if($db_type==0)
		{
			$po_id_con="distinct(a.id) as po_id";
			$program_no_array=return_library_array( "select po_id, group_concat(distinct(dtls_id)) as dtls_id from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 group by po_id", "po_id", "dtls_id"  );
		}
		else
		{
			$po_id_con="LISTAGG(a.id, ',') WITHIN GROUP (ORDER BY a.id) as po_id ";
			$program_no_array=return_library_array( "select po_id, LISTAGG(dtls_id, ',') WITHIN GROUP (ORDER BY dtls_id) as dtls_id from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 group by po_id", "po_id", "dtls_id"  );	
		}
		/*if($booking_nos!="")
		{
			$sql_po=sql_select("select a.id as po_id from wo_po_break_down a, wo_booking_dtls b where b.job_no=a.job_no_mst  and b.booking_no in($booking_nos) and b.status_active=1 and b.is_deleted=0");
			$po_ids='';
			foreach($sql_po as $poID)
			{
				if($po_ids=='') $po_ids=$poID[csf('po_id')];else $po_ids.=",".$poID[csf('po_id')];
					$booking_array[$poID[csf('po_id')]]['booking_no'].=$row[csf('booking_no')].',';
			}
		
		}*/
		//echo $po_idss;
	
		/*if($db_type==0)
		{
			echo "select b.po_id, group_concat(distinct(dtls_id)) as dtls_id from wo_po_break_down b where status_active=1 and is_deleted=0 group by po_id";
			$program_no_array=return_library_array( "select b.po_id, group_concat(distinct(dtls_id)) as dtls_id from wo_po_break_down b where status_active=1 and is_deleted=0 group by po_id", "po_id", "dtls_id"  );
		}
		else
		{
			$program_no_array=return_library_array( "select po_id, LISTAGG(dtls_id, ',') WITHIN GROUP (ORDER BY dtls_id) as dtls_id from wo_po_break_down b where status_active=1 and is_deleted=0 group by po_id", "po_id", "dtls_id"  );	
		}*/
		if( str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.transaction_date <=".$txt_date_from."";
		if( str_replace("'","",$cbo_store)==0) $store_cond=""; else $store_cond= " and a.store_id=".$cbo_store."";
		
		$product_array=array();	
		$prod_query="Select id, detarmination_id, gsm, dia_width, brand from product_details_master where item_category_id=13 and company_id=$cbo_company_id and status_active=1 and is_deleted=0 ";
		$prod_query_sql=sql_select($prod_query);
		foreach( $prod_query_sql as $row )
		{
			$product_array[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
			$product_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
			$product_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
			$product_array[$row[csf('id')]]['brand']=$row[csf('brand')];
		}
		
		$product_id_arr=array(); $recvIssue_array=array(); $trans_arr=array();
		$sql_trans="SELECT b.trans_type, b.po_breakdown_id, b.prod_id, sum(b.quantity) as qnty 
		from inv_transaction a, order_wise_pro_details b 
		where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2,22,13,16,45,51,58,61,80,81,82,83,84,110) and a.item_category=13 and a.transaction_type in(1,2,3,4,5,6) and b.trans_type  in(1,2,3,4,5,6) $trans_date $store_cond
		and a.transaction_date >'31-Dec-2019' group by b.trans_type, b.po_breakdown_id, b.prod_id";
		// echo $sql_trans;
		$result_trans=sql_select( $sql_trans );
		foreach ($result_trans as $row)
		{
			$recvIssue_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('trans_type')]]+=$row[csf('qnty')];
			$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
			//$recvIssue_array[$row[csf('po_breakdown_id')]][$row[csf('trans_type')]]+=$row[csf('qnty')];
			//$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
		}
	
		/*$sql_transfer_in="select a.transaction_type, a.order_id, a.prod_id, sum(a.cons_quantity) as trans_qnty from inv_transaction a, inv_item_transfer_mst b where a.mst_id=b.id and b.transfer_criteria=4 and b.item_category=13 and a.item_category=13 and a.transaction_type in(5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $trans_date group by a.transaction_type,a.order_id,a.prod_id";
		$data_transfer_in_array=sql_select($sql_transfer_in);
		foreach( $data_transfer_in_array as $row )
		{
			$trans_arr[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('transaction_type')]]=$row[csf('trans_qnty')];
			$product_id_arr[$row[csf('order_id')]].=$row[csf('prod_id')].",";
		}*/
		//print_r($trans_arr[3593]);
		ob_start();
		?>
        <style type="text/css">
        	.word_break_wrap{
				word-break:break-all;
				word-wrap:break-word;
				}
        </style>
		<fieldset style="width:2000px">
			<table cellpadding="0" cellspacing="0" width="1810">
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="22" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="22" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="22" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="2000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
				<thead>
					<tr>
						<th width="30" rowspan="2">SL</th>

						<th colspan="13">Fabric Details</th>
						<th colspan="3">Used Yarn Details</th>
						<th colspan="4">Receive Details</th>
						<th colspan="4">Issue Details</th>
						<th colspan="2">Stock Details</th>
					</tr>
					<tr>
						<th width="80">Job No</th>
						<th width="80">Buyer</th>
						<th width="100">Order No</th>
						<th width="100">Style No</th>
						<th width="90">Booking No</th>
						<th width="50">Product ID</th>
						<th width="180">Const. & Comp</th>

						<th width="40">GSM</th>
						<th width="80">Color Range</th>
						<th width="60">Dyeing Color</th>
						<th width="40">M/Dia</th>
						<th width="40">F/Dia</th>
						<th width="40">Stich Length</th>
						 	 	
						<th width="60">Y. Count</th>
						<th width="60">Y. Brand</th>
						<th width="60">Y. Lot</th>
						
						<th width="80">Recv. Qty.</th>
						<th width="80">Issue Return Qty.</th>
						<th width="80">Transf. In Qty.</th>
						<th width="80">Total Recv.</th>
						<th width="80">Issue Qty.</th>
						<th width="80">Receive Return Qty.</th>
						<th width="80">Transf. Out Qty.</th>
						<th width="80">Total Issue</th>
						<th width="80">Stock Qty.</th>
						<th>DOH</th>
					</tr>
				</thead>
			</table>
			<div style="width:2018px; overflow-y: scroll; max-height:280px;" id="scroll_body">
				<table width="2000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" align="left"> 
				<?
					$sql="SELECT b.job_no, b.buyer_name, b.style_ref_no, a.id, a.po_number, a.file_no, a.grouping, a.pub_shipment_date, sum(a.po_quantity) as po_quantity,c.booking_no
					from wo_po_break_down a ,wo_po_details_master b, wo_booking_mst c,wo_booking_dtls d 
					where b.job_no=a.job_no_mst  
					and a.id=d.po_break_down_id and c.booking_no=d.booking_no and d.booking_type in(1,4) and
					b.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and a.INSERT_DATE>'31-Dec-2019 11:59:59 PM'
					$year_cond $buyer_id_cond $job_no_cond $search_cond $order_cond $order_cond $po_id_cond  
					group by  b.job_no, b.buyer_name, b.style_ref_no, a.id, a.po_number, a.file_no, a.grouping, a.pub_shipment_date,c.booking_no
					order by a.id, a.pub_shipment_date ";
					//wo_booking_mst a, wo_booking_dtls

					// echo $sql;

					$bookingDatas=sql_select($sql);
					$bookingDataArr=array();$poIDs="";$tot_rows=0;
					foreach($bookingDatas as $row)
					{
						$tot_rows++;
						$poIDs.=$row[csf('id')].",";
						$bookingNos.="'".$row[csf('booking_no')]."',";
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["booking_no"]=$row[csf('booking_no')];
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["id"]=$row[csf('id')];
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["job_no"]=$row[csf('job_no')];
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["buyer_name"]=$row[csf('buyer_name')];
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["style_ref_no"]=$row[csf('style_ref_no')];
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["po_number"]=$row[csf('po_number')];
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["file_no"]=$row[csf('file_no')];
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["grouping"]=$row[csf('grouping')];
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["pub_shipment_date"]=$row[csf('pub_shipment_date')];
						$bookingDataArr[$row[csf('booking_no')]][$row[csf('id')]]["po_quantity"]=$row[csf('po_quantity')];
						
					}
					$poIDs=chop($poIDs,",");
					$bookingNos=chop($bookingNos,",");
				    $poIDs=implode(",",array_filter(array_unique(explode(",",$poIDs))));
				    $bookingNos=implode(",",array_filter(array_unique(explode(",",$bookingNos))));
				    if($poIDs!="")
				    {
				        $poIDs=explode(",",$poIDs);  
				        $po_ids_chnk=array_chunk($poIDs,999);
				        $po_ids_cond=" and";
				        foreach($po_ids_chnk as $dtls_id)
				        {
				        if($po_ids_cond==" and")  $po_ids_cond.="(a.po_break_down_id in(".implode(',',$dtls_id).")"; else $po_ids_cond.=" or a.po_break_down_id in(".implode(',',$dtls_id).")";
				        }
				        $po_ids_cond.=")";
				        //echo $po_ids_cond;die;
				    }
				    if($bookingNos!="")
				    {
				        $bookingNos=explode(",",$bookingNos);  
				        $bookingNos_chnk=array_chunk($bookingNos,999);
				        $bookingNos_chnk_cond=" and";
				        foreach($bookingNos_chnk as $dtls_id)
				        {
				        if($bookingNos_chnk_cond==" and")  $bookingNos_chnk_cond.="(a.booking_no in(".implode(',',$dtls_id).")"; else $bookingNos_chnk_cond.=" or a.booking_no in(".implode(',',$dtls_id).")";
				        }
				        $bookingNos_chnk_cond.=")";
				        //echo $bookingNos_chnk_cond;die;
				    }

					/*	$sqlData="select e.recv_number,e.knitting_source,e.booking_no as knitting_challan_no,e.receive_date, sum(c.quantity) as quantity,
					listagg(d.stitch_length,',') within group (order by d.stitch_length) as stitch_length, d.yarn_lot,c.po_breakdown_id 
					from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e 
					where a.job_no_mst=b.job_no and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0  
					and c.po_breakdown_id=a.id and c.dtls_id=d.id and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 and  e.item_category=13 and e.status_active=1 and e.is_deleted=0 
					and a.id=$orderID and e.company_id=$companyID and c.prod_id=$prodID and c.entry_form in(2,22,58) 
					group by e.recv_number,e.knitting_source,e.booking_no,e.receive_date, d.yarn_lot,c.po_breakdown_id order by c.prod_id";*/



					$sql_dtls_qry="SELECT a.po_break_down_id, a.booking_no, c.yarn_lot, c.yarn_count,c.brand_id,c.stitch_length, c.prod_id,c.yarn_prod_id,c.color_range_id,c.color_id,c.machine_dia 
					from wo_booking_dtls a,pro_grey_prod_entry_dtls c,order_wise_pro_details d 
					where a.po_break_down_id=d.po_breakdown_id and c.prod_id=d.prod_id  and c.id=d.dtls_id and a.booking_type in(1,4) $po_ids_cond $bookingNos_chnk_cond and a.INSERT_DATE>'31-Dec-2019 11:59:59 PM' and c.INSERT_DATE>'31-Dec-2019 11:59:59 PM' and d.INSERT_DATE>'31-Dec-2019 11:59:59 PM'
					and a.status_active=1 and a.is_deleted=0  and d.trans_type=1 and c.status_active=1 and c.is_deleted=0 and d.entry_form in(2) 
					and d.status_active=1 and d.is_deleted=0
					group by a.po_break_down_id, a.booking_no,c.yarn_lot,c.yarn_count,c.brand_id,c.stitch_length,c.prod_id,c.yarn_prod_id,c.color_range_id ,c.color_id,c.machine_dia";

					$yarn_prodids="";
					$nameArray=sql_select( $sql_dtls_qry );
					foreach ($nameArray as $row)
					{
						$yarn_prodids.=$row[csf('yarn_prod_id')].",";
						$yarn_count_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_count'] .=$row[csf('yarn_count')].",";
						$yarn_lot_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_lot'] .=$row[csf('yarn_lot')].",";
						$yarn_brand_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['yarn_prod_id'] .=$row[csf('yarn_prod_id')].",";
						$yarn_stich_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['stitch_length']=$row[csf('stitch_length')];
						$yarn_color_range_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['color_range_id']=$row[csf('color_range_id')];
						$yarn_color_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['color_id']=$row[csf('color_id')];
						$yarn_mdia_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['machine_dia']=$row[csf('machine_dia')];
					}
					$yarn_prodids=chop($yarn_prodids,",");
				    $yarn_prodids=implode(",",array_filter(array_unique(explode(",",$yarn_prodids))));
				    if($yarn_prodids!="")
				    {
				        $yarn_prodids=explode(",",$yarn_prodids);  
				        $prodids_chnk=array_chunk($yarn_prodids,999);
				        $prodids_cond=" and";
				        foreach($prodids_chnk as $dtls_id)
				        {
				        if($prodids_cond==" and")  $prodids_cond.="(a.id in(".implode(',',$dtls_id).")"; else $prodids_cond.=" or a.id in(".implode(',',$dtls_id).")";
				        }
				        $prodids_cond.=")";
				        //echo $po_ids_cond;die;
				    }
					$yarn_prod_sql=sql_select("select a.id, b.brand_name from product_details_master a, lib_brand b where a.brand = b.id and a.item_category_id = 1 and a.company_id=$cbo_company_id $prodids_cond");
					foreach($yarn_prod_sql as $row)
					{
						$yarn_brand_ref[$row[csf('id')]] .=$row[csf('brand_name')];
					}
					//echo $sql;
					//$result=sql_select( $sql );
					$i=1; $tot_recv_qty=0; $tot_iss_qty=0; $tot_trans_in_qty=0; $tot_trans_out_qty=0; $grand_tot_recv_qty=0; $grand_tot_iss_qty=0; $grand_stock_qty=0;
					$sub_tot_recv_qty=0; $sub_tot_iss_qty=0; $sub_tot_trans_in_qty=0; $sub_tot_trans_out_qty=0; $sub_grand_tot_recv_qty=0; $sub_grand_tot_iss_qty=0; $sub_grand_stock_qty=0;


					foreach($bookingDataArr as $bookingNum => $bookingData)
					{
						$show= false;
						foreach($bookingData as $po_id => $row)
						{
							$dataProd=array_filter(array_unique(explode(",",substr($product_id_arr[$po_id],0,-1))));
							if(count($dataProd)>0)
							{
								$order_recv_qty=0; $order_iss_ret_qty=0; $order_iss_qty=0; $order_rec_ret_qty=0; $order_trans_in_qty=0; $order_trans_out_qty=0; $order_tot_recv_qnty=0; $order_tot_iss_qnty=0; $order_stock_qnty=0;$p=1;
								foreach($dataProd as $prodId)
								{
								
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
									$recv_qty=$recvIssue_array[$po_id][$prodId][1];
									$iss_qty=$recvIssue_array[$po_id][$prodId][2];
									$iss_ret_qty=$recvIssue_array[$po_id][$prodId][4];
									$recv_ret_qty=$recvIssue_array[$po_id][$prodId][3];
									$trans_in_qty=$recvIssue_array[$po_id][$prodId][5];
									$trans_out_qty=$recvIssue_array[$po_id][$prodId][6];
									$recv_tot_qty=$recv_qty+$iss_ret_qty+$trans_in_qty;
									$iss_tot_qty=$iss_qty+$recv_ret_qty+$trans_out_qty;
									$stock_qty = $recv_tot_qty-$iss_tot_qty;
									//echo $trans_in_qty.', '.$trans_out_qty;
									$bookingNo=rtrim($booking_array[$po_id]['booking_no'],',');
									//$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
									$booking_no=implode(",",array_unique(explode(",",$bookingNo)));
									$yarnCount=$yarn_count_arr[$po_id][$row['booking_no']][$prodId]['yarn_count'];
									$yarnLot=$yarn_lot_arr[$po_id][$row['booking_no']][$prodId]['yarn_lot'];
									$yarnBrand=$yarn_brand_arr[$po_id][$row['booking_no']][$prodId]['yarn_prod_id'];
									$yarnStitch=$yarn_stich_arr[$po_id][$row['booking_no']][$prodId]['stitch_length'];
									$colorRangeId=$yarn_color_range_arr[$po_id][$row['booking_no']][$prodId]['color_range_id'];
									$colorId=$yarn_color_arr[$po_id][$row['booking_no']][$prodId]['color_id'];
									$machineDia=$yarn_mdia_arr[$po_id][$row['booking_no']][$prodId]['machine_dia'];


									$yarn_count_value ="";
									$yarnCountARR=array_filter(array_unique(explode(",",chop($yarnCount,","))));
									foreach ($yarnCountARR as  $yval) 
									{
										$yarn_count_value .= $count_arr[$yval].",";
									}
									$yarn_count_value=chop($yarn_count_value,",");

									$yarn_brand_value ="";
									$yarnBrandARR=array_filter(array_unique(explode(",",chop($yarnBrand,","))));
									foreach ($yarnBrandARR as  $yBval) 
									{
										$yarn_brand_value .= $yarn_brand_ref[$yBval].",";
									}
									$yarn_brand_value=chop($yarn_brand_value,",");

									$color_value ="";
									$colorARR=array_filter(array_unique(explode(",",chop($colorId,","))));
									foreach ($colorARR as  $yBval) 
									{
										$color_value .= $color_arr[$yBval].",";
									}
									$color_value=chop($color_value,",");

									$yarnLott= implode(",",array_filter(array_unique(explode(",",chop($yarnLot,",")))));

									
									if($cbo_value_with==1 || ($cbo_value_with==2 && number_format($stock_qty,4) > 0))
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">

											<td width="30"><? echo $i;//echo "==".$row[csf('id')]."=".$prodId; ?></td>
											<td width="80"><p class="word_break_wrap"><? echo $row['job_no']; ?>&nbsp;</p></td>
											<td width="80"><p class="word_break_wrap"><? echo $buyer_arr[$row['buyer_name']]; ?>&nbsp;</p></td>
											<td width="100"><p class="word_break_wrap"><? echo $row['po_number']; ?>&nbsp;</p></td>
											<td width="100"><p class="word_break_wrap"><? echo $row['style_ref_no']; ?>&nbsp;</p></td>
											<td width="90"><p class="word_break_wrap"><? echo $row['booking_no']; ?>&nbsp;</p></td>
											<td width="50"><p><? echo $prodId; ?></p></td>
											<td width="180"><p class="word_break_wrap"><? echo $composition_arr[$product_array[$prodId]['detarmination_id']]; ?></p></td>
											<td width="40"><p><? echo $product_array[$prodId]['gsm']; ?>&nbsp;</p></td>
											<td width="80"><p><? echo $color_range[$colorRangeId]; ?>&nbsp;</p></td>
											<td width="60"><p><? echo $color_value; ?>&nbsp;</p></td>
											<td width="40"><p><? echo $machineDia; ?>&nbsp;</p></td>
											<td width="40"><p><? echo $product_array[$prodId]['dia_width']; ?>&nbsp; <input type="hidden" name="" value="<? echo $yarnStitch; ?>"></p></td>
											<td width="40"><p><? echo $yarnStitch; ?>&nbsp;</p></td>
											
											<td width="60"><p><? echo $yarn_count_value; //$count_val; ?>&nbsp;</p></td>
											<td width="60"><p><? echo $yarn_brand_value; ?>&nbsp;</p></td>
											<td width="60"><p><? echo $yarnLott; ?>&nbsp;</p></td>
											
											<td width="80" align="right"><a href='#report_details' onClick="openmypage_delivery_all('<? echo $po_id; ?>','<? echo $row['booking_no']; ?>','<? echo $prodId; ?>','<? echo $yarn_brand_value; ?>','<? echo $yarnLott; ?>','<? echo $yarnStitch; ?>','950px','report_all_popup',1);"><? echo number_format($recv_qty,2); ?></a>
											</td>
											<td width="80" align="right"><a href='#report_details' onClick="openmypage_delivery_all('<? echo $po_id; ?>','<? echo $row['booking_no']; ?>','<? echo $prodId; ?>','<? echo $yarn_brand_value; ?>','<? echo $yarnLott; ?>','<? echo $yarnStitch; ?>','450px','report_all_popup',2);"><? echo number_format($iss_ret_qty,2); ?></a>
											</td>
											<td width="80" align="right"><a href='#report_details' onClick="openmypage_delivery_all('<? echo $po_id; ?>','<? echo $row['booking_no']; ?>','<? echo $prodId; ?>','<? echo $yarn_brand_value; ?>','<? echo $yarnLott; ?>','<? echo $yarnStitch; ?>','650px','report_all_popup',3);"><? echo number_format($trans_in_qty,2); ?></a>
											</td>
											<td width="80" align="right"><? echo number_format($recv_tot_qty,2); ?></td>
											<td width="80" align="right"><a href='#report_details' onClick="openmypage_delivery_all('<? echo $po_id; ?>','<? echo $row['booking_no']; ?>','<? echo $prodId; ?>','<? echo $yarn_brand_value; ?>','<? echo $yarnLott; ?>','<? echo $yarnStitch; ?>','850px','report_all_popup',4);"><? echo number_format($iss_qty,2); ?></a></p></td>
											<td width="80" align="right"><? echo number_format($recv_ret_qty,2); ?></p></td>
											<td width="80" align="right"><a href='#report_details' onClick="openmypage_delivery_all('<? echo $po_id; ?>','<? echo $row['booking_no']; ?>','<? echo $prodId; ?>','<? echo $yarn_brand_value; ?>','<? echo $yarnLott; ?>','<? echo $yarnStitch; ?>','650px','report_all_popup',5);"><? echo number_format($trans_out_qty,2); ?></a>	
												</td>
											<td width="80" align="right"><? echo number_format($iss_tot_qty,2); ?></td>
											<td width="80" align="right"><a href='#report_details' onClick="openmypage_delivery_all('<? echo $po_id; ?>','<? echo $row['booking_no']; ?>','<? echo $prodId; ?>','<? echo $yarn_brand_value; ?>','<? echo $yarnLott; ?>','<? echo $yarnStitch; ?>','650px','report_all_popup',6);"><? echo number_format($stock_qty,2); ?></a></td>
											<? $daysOnHand=datediff("d",change_date_format($transaction_date_array[$prodId]['max_date'],'','',1),date("Y-m-d")); ?>
											<td align="center"><? if($stock_qty>0) echo $daysOnHand; ?>&nbsp;</td>
										</tr>
										<?	
										//==
										$sub_booking .= $booking_no.",";
	                    				$sub_tot_recv_qty+=$recv_qty; 
										$sub_tot_iss_ret_qty+=$iss_ret_qty; 
										$sub_tot_iss_qty+=$iss_qty; 
										$sub_tot_rec_ret_qty+=$recv_ret_qty; 
										$sub_tot_trans_in_qty+=$trans_in_qty; 
										$sub_tot_trans_out_qty+=$trans_out_qty; 
										$sub_grand_tot_recv_qty+=$recv_tot_qty; 
										$sub_grand_tot_iss_qty+=$iss_tot_qty;
										$sub_grand_stock_qty+=$stock_qty;
	                					//==
										$i++;
										
										$order_recv_qty+=$recv_qty;
										$order_iss_ret_qty+=$iss_ret_qty;  
										$order_iss_qty+=$iss_qty;
										$order_rec_ret_qty+=$recv_ret_qty; 
										$order_trans_in_qty+=$trans_in_qty; 
										$order_trans_out_qty+=$trans_out_qty; 
										$order_tot_recv_qnty+=$recv_tot_qty; 
										$order_tot_iss_qnty+=$iss_tot_qty;
										$order_stock_qnty+=$stock_qty;
										
										$tot_recv_qty+=$recv_qty; 
										$tot_iss_ret_qty+=$iss_ret_qty; 
										$tot_iss_qty+=$iss_qty; 
										$tot_rec_ret_qty+=$recv_ret_qty; 
										$tot_trans_in_qty+=$trans_in_qty; 
										$tot_trans_out_qty+=$trans_out_qty; 
										$grand_tot_recv_qty+=$recv_tot_qty; 
										$grand_tot_iss_qty+=$iss_tot_qty;
										$grand_stock_qty+=$stock_qty;
										$show = true;
									}
								}
							}
						}
						if($show == true)
						{
							if ($sub_tot_recv_qty>0 || $sub_tot_iss_ret_qty>0 || $sub_tot_trans_in_qty>0 || $sub_grand_tot_recv_qty>0 || $sub_tot_iss_qty>0 || $sub_tot_rec_ret_qty>0 || $sub_tot_trans_out_qty>0|| $sub_grand_tot_iss_qty>0|| $sub_grand_stock_qty>0) 
							{
								?>
								<tr style="font-weight: bold; background-color: grey;">	
					                <td colspan="17" align="right">Booking Total <? //echo $unit_of_measurement[$row[csf('uom')]]; ?> </td>
			                		<td width="80" align="right"><? echo number_format($sub_tot_recv_qty,2); ?></td>
			                		<td width="80" align="right"><? echo number_format($sub_tot_iss_ret_qty,2); ?></td>  
			                		<td width="80" align="right"><? echo number_format($sub_tot_trans_in_qty,2); ?></td>  
			                		<td width="80" align="right"><? echo number_format($sub_grand_tot_recv_qty,2); ?></td>
			                		<td width="80" align="right"><? echo number_format($sub_tot_iss_qty,2); ?></td>  
			                		<td width="80" align="right"><? echo number_format($sub_tot_rec_ret_qty,2); ?></td>  
			                		<td width="80" align="right"><? echo number_format($sub_tot_trans_out_qty,2); ?></td>  
			                		<td width="80" align="right"><? echo number_format($sub_grand_tot_iss_qty,2); ?></td>  
			                		<td width="80" align="right"><? echo number_format($sub_grand_stock_qty,2); ?></td>  
			                		<td align="right"></td>  

					            </tr>
							<?
							}
						}

						$sub_tot_recv_qty=0; $sub_tot_iss_qty=0; $sub_tot_trans_in_qty=0; $sub_tot_trans_out_qty=0; $sub_grand_tot_recv_qty=0; $sub_grand_tot_iss_qty=0; $sub_grand_stock_qty=0;$sub_tot_iss_ret_qty=0;$sub_tot_rec_ret_qty=0;
					}
					?>
				</table>
			</div>
			<table width="2000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body_bottom" align="left"> 
				<tfoot>
					<tr>
                    
                    	<th width="30">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th align="right" width="50">&nbsp;</th>
						<th align="right" width="180">&nbsp;</th>
						<th align="right" width="40">&nbsp;</th>
						<th align="right" width="80">&nbsp;</th>
						<th align="right" width="60">&nbsp;</th>
						<th align="right" width="40">&nbsp;</th>
						<th align="right" width="40">&nbsp;</th>
						<th align="right" width="40">&nbsp;</th>
						<th align="right" width="60">&nbsp;</th>
						<th align="right" width="60">&nbsp;</th>
						<th align="right" width="60">&nbsp;</th>
						<th align="right" width="80" id="value_tot_recv_qty"><p><? echo number_format($tot_recv_qty,2,'.',''); ?></p></th>
						<th align="right" width="80" id="value_tot_iss_ret_qty"><p><? echo number_format($tot_iss_ret_qty,2,'.',''); ?></p></th>
						<th align="right" width="80" id="value_tot_trans_in_qty"><p><? echo number_format($tot_trans_in_qty,2,'.',''); ?></p></th>
						<th align="right" width="80" id="value_grand_tot_recv_qty"><p><? echo number_format($grand_tot_recv_qty,2,'.',''); ?></p></th>
						<th align="right" width="80" id="value_tot_iss_qty"><p><? echo number_format($tot_iss_qty,2,'.',''); ?></p></th>
						<th align="right" width="80" id="value_tot_rec_ret_qty"><p><? echo number_format($tot_rec_ret_qty,2,'.',''); ?></p></th>
						<th align="right" width="80" id="value_tot_trans_out_qty"><p><? echo number_format($tot_trans_out_qty,2,'.',''); ?></p></th>
						<th align="right" width="80" id="value_grand_tot_iss_qty"><p><? echo number_format($grand_tot_iss_qty,2,'.',''); ?></p></th>
						<th align="right" width="80" id="value_grand_stock_qty"><p><? echo number_format($grand_stock_qty,2,'.',''); ?></p></th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</fieldset>
		<?
	}
	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html####$filename####$rpt_type"; 
	exit();
}




if($action=="report_all_popup")
{
	echo load_html_head_contents("Grey Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$companyID 	= $companyID;
	$orderID 	=$orderID;
	$prodID 	=$prodID;
	$bookingID 	="'".$bookingID."'";
	$txt_date_from=$txt_date_from;
	$type 		=$type;
	$yarnBrand 	=$yarnBrand;
	$yLot 		=$yLot;
	$stichLength=$stichLength;

	if( str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.transaction_date <=".$txt_date_from."";


	//if( str_replace("'","",$to_date)=="") $trans_date=""; else $trans_date= " and a.transaction_date <=".$to_date."";
	/*if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(b.insert_date)=$year_id"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_cond=" and TO_CHAR(b.insert_date,'YYYY')=$year_id"; else $year_cond="";
	}*/
	$product_details_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
	$color_name_arr 	=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$yarncount 			= return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
	$brand_name_arr 	= return_library_array("select id, brand_name from  lib_brand", 'id', 'brand_name');
	$buyer_name_arr 	= return_library_array("select id, buyer_name from  lib_buyer", 'id', 'buyer_name');
	$store_name_arr 	= return_library_array("select id, store_name from  lib_store_location", 'id', 'store_name');
	if ($type==1) 
    {
		?>
		<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="930" cellpadding="0" cellspacing="0">
	                <thead>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="150">Knitting Challan No</th>
	                        <th width="100">Knitting Source</th>
	                        <th width="150">Recv. ID</th>
	                        <th width="60">Date </th>
	                        <th width="60">Grey Dia</th>
	                        <th width="60">Finished Dia</th>
	                        <th width="100">Lot</th>
	                        <th width="100">Stich Leng.</th>
	                        <th>Qty</th>
	                    </tr>
					</thead>
	             </table>
	             <div style="width:950px; max-height:410px; overflow-y:scroll" id="scroll_body">
	                <table border="1" class="rpt_table" rules="all" width="930" cellpadding="0" cellspacing="0" id="table_body">
	                    <?
	                   	if($db_type==0)
						{
							$sqlData="select e.recv_number,e.knitting_source,e.booking_no as knitting_challan_no,e.receive_date, sum(c.quantity) as quantity, group_concat(d.stitch_length) as stitch_length, d.yarn_lot,c.po_breakdown_id 
							from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e 
							where a.job_no_mst=b.job_no and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0  
							and c.po_breakdown_id=a.id and c.dtls_id=d.id and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 and  e.item_category=13 and e.status_active=1 and e.is_deleted=0 
							and a.id=$orderID and e.company_id=$companyID  and c.prod_id=$prodID and c.entry_form in(2,22,58)  
							   group by e.recv_number,e.knitting_source,e.booking_no,e.receive_date, d.yarn_lot,c.po_breakdown_id order by c.prod_id";
						}
						else if($db_type==2)
						{
						 	$sqlData="select e.recv_number,e.knitting_source,e.booking_no as knitting_challan_no,e.receive_date, sum(c.quantity) as quantity,
							listagg(d.stitch_length,',') within group (order by d.stitch_length) as stitch_length, d.yarn_lot,c.po_breakdown_id 
							from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e 
							where a.job_no_mst=b.job_no and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0  
							and c.po_breakdown_id=a.id and c.dtls_id=d.id and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 and  e.item_category=13 and e.status_active=1 and e.is_deleted=0 
							and a.id=$orderID and e.company_id=$companyID and c.prod_id=$prodID and c.entry_form in(2,22,58) 
							    group by e.recv_number,e.knitting_source,e.booking_no,e.receive_date, d.yarn_lot,c.po_breakdown_id order by c.prod_id";
						}
						if($db_type==0)
						{
							
							$sql_planning =sql_select("select group_concat(a.fabric_dia) as fabric_dia,group_concat(a.grey_dia) as grey_dia,b.booking_no,b.po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b,wo_po_break_down c  where a.id=b.dtls_id and b.po_id=c.id  and b.company_id=$companyID and b.po_id=$orderID and b.booking_no=$bookingID and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c. is_deleted=0 group by b.booking_no,b.po_id");
						}
						else
						{
							$sql_planning =sql_select("select listagg(a.fabric_dia,',') within group (order by a.fabric_dia) as fabric_dia,listagg(a.grey_dia,',') within group (order by a.grey_dia) as grey_dia,b.booking_no,b.po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b,wo_po_break_down c  where a.id=b.dtls_id and b.po_id=c.id  and b.company_id=$companyID and b.po_id=$orderID and b.booking_no=$bookingID and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c. is_deleted=0 group by b.booking_no,b.po_id");
						}
					 	

						foreach ($sql_planning as $row) 
						{
							$greyDia=implode(",",array_unique(explode(",", $row[csf('grey_dia')])));
							$fabricDia=implode(",",array_unique(explode(",", $row[csf('fabric_dia')])));

							$plannig_data_arr[$row[csf('booking_no')]][$row[csf('po_id')]]['fabric_dia']=$fabricDia;
							$plannig_data_arr[$row[csf('booking_no')]][$row[csf('po_id')]]['grey_dia']=$greyDia;
						}
						$bookingID=str_replace("'", "", $bookingID);
						//echo $sqlData;
						$sql_dtls=sql_select($sqlData);
						$i=1;
						foreach ($sql_dtls as $row) {
							$stitch_length=implode(",",array_unique(explode(",",$row[csf('stitch_length')])));
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="150"><p><? echo $row[csf('knitting_challan_no')]; ?></p></td>
	                            <td width="100"  align="center"><? echo $knitting_source[$row[csf('knitting_source')]];// ?></td>
	                            <td width="150"><div style="word-break:break-all"><p><? echo $row[csf('recv_number')]; ?></p></div></td>
	                            <td width="60"><div style="word-break:break-all"><? echo change_date_format($row[csf('receive_date')]);//$buyer_name_arr[$job_data_arr[$po_id]["buyer_name"]]; ?></div></td>
	                           
	                            <td width="60" align="center"><div style="word-break:break-all"><? echo $plannig_data_arr[$bookingID][$row[csf('po_breakdown_id')]]['grey_dia']; ?></div></td>
	                             <td width="60" align="center"><div style="word-break:break-all"><? echo $plannig_data_arr[$bookingID][$row[csf('po_breakdown_id')]]['fabric_dia']; ?></div></td>
	                            <td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('yarn_lot')]; ?></div></td>
	                            <td width="100" align="center"><div style="word-break:break-all"><? echo $stitch_length; ?></div></td>
	                            <td align="right"><? echo number_format($row[csf("quantity")],2); ?>&nbsp;</td> 
	                            
	                        </tr>
	                    <?
	                    $total_recv_qty+=$row[csf("quantity")];
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                    	<tr>
	                            <th colspan="9" align="right">Total</th>
	                            <th align="right"><? echo number_format($total_recv_qty,2); ?></th>
	                        </tr>
	                        
	                    </tfoot>
	                </table>
				</div>		
			</div>
		</fieldset>	
		<?
	}
	else if ($type==2)  {
		?>
		<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="430" cellpadding="0" cellspacing="0">
	                <thead>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="150">Issue Return ID</th>
	                        <th width="150">Return Date</th>
	                        <th>Qty</th>
	                    </tr>
					</thead>
	             </table>
	             <div style="width:450px; max-height:410px; overflow-y:scroll" id="scroll_body">
	                <table border="1" class="rpt_table" rules="all" width="430" cellpadding="0" cellspacing="0" id="table_body">
	                    <?
						$sql_retn="select b.po_breakdown_id, a.prod_id, a.yarn_count, a.batch_lot, a.rack, a.self, sum(case when a.transaction_type=4 and b.trans_type=4 and b.entry_form in(51,84) then b.quantity end) as iss_rtn_qty, sum(case when a.transaction_type=3 and b.trans_type=3 and b.entry_form=45 then b.quantity end) as rcv_rtn_qty,
						sum(case when a.transaction_type in(6) and b.trans_type in(6) and b.entry_form in(82) then b.quantity end) as transferOut,
						sum(case when a.transaction_type in(5) and b.trans_type in(5) and b.entry_form in(82) then b.quantity end) as transferIn 
						from inv_transaction a, order_wise_pro_details b, wo_po_break_down c 
						where a.id=b.trans_id and b.po_breakdown_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=13 and b.entry_form in(45,51,81,82,83,84) and a.company_id=$companyID and b.po_breakdown_id=$orderID and a.prod_id=$prodID and a.transaction_type in(1,2,3,4,5,6) and b.trans_type in (1,2,3,4,5,6) $return_order_cond 
						group by b.po_breakdown_id, a.prod_id, a.batch_lot, a.yarn_count, a.rack, a.self";
						$data_retn_array=sql_select($sql_retn);
						foreach($data_retn_array as $row )
						{
							if($row[csf('self')]=="") $row[csf('self')]=0;
							if($row[csf('rack')]=="") $row[csf('rack')]=0;
							$retn_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('batch_lot')]][$row[csf('rack')]][$row[csf('self')]]['iss_rtn_qty']+=$row[csf('iss_rtn_qty')];
						}
						if($db_type==0)
						{
							$sqlData="select a.id as po_breakdown_id,c.prod_id, f.rack, f.self,f.yarn_count, f.batch_lot,e.recv_number,e.knitting_source,e.booking_no as knitting_challan_no,e.receive_date, sum(c.quantity) as quantity, group_concat(d.stitch_length) as stitch_length, d.yarn_lot 
							from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e,inv_transaction f 
							where a.job_no_mst=b.job_no and f.id=c.trans_id and f.id=d.trans_id and b.status_active=1 and b.is_deleted=0 and c.trans_type=4 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0  
							and c.po_breakdown_id=a.id and c.dtls_id=d.id and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 and  e.item_category=13 and e.status_active=1 and e.is_deleted=0 
							and a.id=$orderID and e.company_id=$companyID  and c.prod_id=$prodID and e.entry_form in(52,84)
							  group by a.id,c.prod_id, f.rack, f.self,f.yarn_count, f.batch_lot,e.recv_number,e.knitting_source,e.booking_no,e.receive_date, a.po_number, a.file_no, a.grouping, a.po_quantity,c.prod_id, d.yarn_count, d.yarn_lot, d.rack, d.self, b.job_no, b.buyer_name, b.style_ref_no,e.entry_form,c.quantity 
							order by a.id,a.po_number, c.prod_id ";
						}
						else if($db_type==2)
						{
							$sqlData="select  a.id as po_breakdown_id,c.prod_id, f.rack, f.self,f.yarn_count, f.batch_lot,e.recv_number,e.knitting_source,e.booking_no as knitting_challan_no,e.receive_date, sum(c.quantity) as quantity,
							listagg(d.stitch_length,',') within group (order by d.stitch_length) as stitch_length, d.yarn_lot 
							from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e,inv_transaction f 
							where a.job_no_mst=b.job_no and f.id=c.trans_id and f.id=d.trans_id and b.status_active=1 and b.is_deleted=0 and c.trans_type=4 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0  
							and c.po_breakdown_id=a.id and c.dtls_id=d.id and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 and  e.item_category=13 and e.status_active=1 and e.is_deleted=0 
							and a.id=$orderID and e.company_id=$companyID  and c.prod_id=$prodID and e.entry_form in(52,84)
							  group by a.id,c.prod_id, f.rack, f.self,f.yarn_count, f.batch_lot,e.recv_number,e.knitting_source,e.booking_no,e.receive_date, a.po_number, a.file_no, a.grouping, a.po_quantity,c.prod_id, d.yarn_count, d.yarn_lot, d.rack, d.self, b.job_no, b.buyer_name, b.style_ref_no,e.entry_form,c.quantity 
							order by a.id,a.po_number, c.prod_id ";
						}
						
						$sql_dtls=sql_select($sqlData);
						$i=1;
						foreach ($sql_dtls as $row) {
							if($row[csf('self')]=="") $row[csf('self')]=0;
							if($row[csf('rack')]=="") $row[csf('rack')]=0;
							$issue_retn=$retn_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('batch_lot')]][$row[csf('rack')]][$row[csf('self')]]['iss_rtn_qty'];
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="150"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                            <td width="150"  align="center"><? echo change_date_format($row[csf('receive_date')]);// ?></td>
	                            <td align="right"><? echo number_format($issue_retn,2); ?>&nbsp;</td> 
	                            
	                        </tr>
	                    <?
	                    $total_issue_retrn_qty+=$issue_retn;
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                    	<tr>
	                            <th colspan="3" align="right">Total</th>
	                            <th align="right"><? echo number_format($total_issue_retrn_qty,2); ?></th>
	                        </tr>
	                        
	                    </tfoot>
	                </table>
				</div>		
			</div>
		</fieldset>	
		<?
	}
	else if ($type==3)  {
		?>
		<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0">
	                <thead>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="150">Transfer ID</th>
	                        <th width="150">Transfer Date</th>
	                        <th width="100">From Order</th>
	                        <th width="100">Booking No</th>
	                        <th>Qty</th>
	                    </tr>
					</thead>
	             </table>
	             <div style="width:650px; max-height:410px; overflow-y:scroll" id="scroll_body">
	                <table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_body">
	                    <?
						$sql_transfer_in="SELECT a.mst_id,d.recv_number,b.po_breakdown_id, a.prod_id, a.yarn_count, a.batch_lot, a.rack, a.self, sum(case when a.transaction_type in(5) and b.trans_type in(5) and b.entry_form in(82,83,110) then b.quantity end) as transfer_in
						from inv_transaction a, order_wise_pro_details b, wo_po_break_down c,inv_receive_master d 
						where a.id=b.trans_id and b.po_breakdown_id=c.id and d.id=a.mst_id and a.status_active=1  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=13 and b.entry_form in(82,83,110) and a.company_id=$companyID and b.po_breakdown_id=$orderID and a.prod_id=$prodID and a.transaction_type in(1,2,3,4,5,6) and b.trans_type in (1,2,3,4,5,6) 
						group by a.mst_id,d.recv_number,b.po_breakdown_id, a.prod_id, a.batch_lot, a.yarn_count, a.rack, a.self";
						$sql_dtls=sql_select($sql_transfer_in);
						$transferMstId="";
						foreach ($sql_dtls as $row) {
							$transferMstId.=$row[csf('mst_id')].",";
						}
 						$transferMstId=chop($transferMstId,",");
 						if($transferMstId!="")
 						{
						 	$sql_transfer_in_data=sql_select("SELECT  a.id,a.transfer_system_id,a.transfer_date,a.from_order_id,b.from_order_id as dtls_order_id from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.id in($transferMstId) group by a.id,a.transfer_system_id,a.transfer_date,a.from_order_id,b.from_order_id");
	 						$from_orderIDs="";
							foreach ($sql_transfer_in_data as $row) 
							{
								if($row[csf('from_order_id')] != 0)
								{
									$from_orderIDs.=$row[csf('from_order_id')].",";
									$trnsInArr[$row[csf('id')]]['transfer_system_id']=$row[csf('transfer_system_id')];
									$trnsInArr[$row[csf('id')]]['transfer_date']=$row[csf('transfer_date')];
									$trnsInArr[$row[csf('id')]]['from_order_id']=$row[csf('from_order_id')];
								}
								else
								{
									$from_orderIDs.=$row[csf('dtls_order_id')].",";
									$trnsInArr[$row[csf('id')]]['transfer_system_id']=$row[csf('transfer_system_id')];
									$trnsInArr[$row[csf('id')]]['transfer_date']=$row[csf('transfer_date')];
									$trnsInArr[$row[csf('id')]]['dtls_order_id']=$row[csf('dtls_order_id')];
								}
							}
							$from_orderIDs=chop($from_orderIDs,",");
							$sql_order=sql_select("SELECT id,po_number from  wo_po_break_down where id in($from_orderIDs) and status_active=1 and is_deleted=0");
							foreach ($sql_order as $row) {
								$po_arr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
							}
							$sql_booking=sql_select("SELECT b.booking_no,b.po_break_down_id from wo_booking_mst a,wo_booking_dtls b ,wo_po_break_down c where a.booking_no=b.booking_no  and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id in($from_orderIDs) group by b.booking_no,b.po_break_down_id");
							foreach ($sql_booking as $row) {
								$booking_arr[$row[csf('po_break_down_id')]]['booking_no'].=$row[csf('booking_no')].",";
							}
						}
						$i=1;
						foreach ($sql_dtls as $row) 
						{
							if($row[csf('self')]=="") $row[csf('self')]=0;
							if($row[csf('rack')]=="") $row[csf('rack')]=0;
	                    	?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="150"><p><? echo $trnsInArr[$row[csf('mst_id')]]['transfer_system_id']; ?></p></td>
	                            <td width="150"  align="center"><? echo change_date_format($trnsInArr[$row[csf('mst_id')]]['transfer_date']);// ?></td>
	                            <td width="100"><p><? echo $po_arr[$trnsInArr[$row[csf('mst_id')]]['from_order_id']]['po_number']=="" ? $po_arr[$trnsInArr[$row[csf('mst_id')]]['dtls_order_id']]['po_number'] : $po_arr[$trnsInArr[$row[csf('mst_id')]]['from_order_id']]['po_number']; ?></p></td>
	                            <td width="100"><p><? echo $booking_arr[$trnsInArr[$row[csf('mst_id')]]['from_order_id']]['booking_no']=="" ? $booking_arr[$trnsInArr[$row[csf('mst_id')]]['dtls_order_id']]['booking_no'] : $booking_arr[$trnsInArr[$row[csf('mst_id')]]['from_order_id']]['booking_no']; ?></p></td>
	                            <td align="right"><? echo number_format($row[csf('transfer_in')],2); ?>&nbsp;</td> 
	                        </tr>
		                    <?
		                    $total_transferIn_qty+=$row[csf('transfer_in')];
		                    $i++;
	                    }
	                   	?>
	                    <tfoot>
	                    	<tr>
	                            <th colspan="5" align="right">Total</th>
	                            <th align="right"><? echo number_format($total_transferIn_qty,2); ?></th>
	                        </tr>
	                        
	                    </tfoot>
	                </table>
				</div>		
			</div>
		</fieldset>	
		<?
	}
	else if ($type==4) {

		?>
		<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="830" cellpadding="0" cellspacing="0">
	                <thead>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="150">Issue ID</th>
	                        <th width="100">Dyeing Source</th>
	                        <th width="150">Issue Date</th>
	                        <th width="60">Grey Dia</th>
	                        <th width="60">Finished Dia</th>
	                        <th width="100">Lot</th>
	                        <th width="100">Stich Leng.</th>
	                        <th>Qty</th>
	                    </tr>
					</thead>
	             </table>
	             <div style="width:850px; max-height:410px; overflow-y:scroll" id="scroll_body">
	                <table border="1" class="rpt_table" rules="all" width="830" cellpadding="0" cellspacing="0" id="table_body">
	                    <?
							if ($lot=="") $search_cond=""; else $search_cond=" and d.yarn_lot in('$lot')";
							$sqlData="select c.issue_number,c.issue_date,c.knit_dye_source,d.yarn_lot,d.stitch_length,b.trans_type, b.po_breakdown_id, b.prod_id, sum(b.quantity) as qnty,c.booking_no from inv_issue_master c,inv_grey_fabric_issue_dtls d,order_wise_pro_details b where c.id=d.mst_id and b.trans_id=d.trans_id and b.status_active=1 and b.is_deleted=0 and b.entry_form in(61,16) and c.item_category=13 and c.company_id=$companyID and b.trans_type  in(1,2,3,4,5,6)  and b.po_breakdown_id=$orderID and b.prod_id=$prodID $search_cond group by b.trans_type, b.po_breakdown_id,c.knit_dye_source, b.prod_id,c.issue_number,c.issue_date,d.yarn_lot,d.stitch_length,c.booking_no";
							if($db_type==0)
							{
								$sql_planning =sql_select("select group_concat(a.fabric_dia) as fabric_dia,group_concat(a.grey_dia) as grey_dia,b.booking_no,b.po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b,wo_po_break_down c  where a.id=b.dtls_id and b.po_id=c.id  and b.company_id=$companyID and b.po_id=$orderID and b.booking_no=$bookingID and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c. is_deleted=0 group by b.booking_no,b.po_id");
							}
							else
							{
								$sql_planning =sql_select("select listagg(a.fabric_dia,',') within group (order by a.fabric_dia) as fabric_dia,listagg(a.grey_dia,',') within group (order by a.grey_dia) as grey_dia,b.booking_no,b.po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b,wo_po_break_down c  where a.id=b.dtls_id and b.po_id=c.id  and b.company_id=$companyID and b.po_id=$orderID and b.booking_no=$bookingID and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c. is_deleted=0 group by b.booking_no,b.po_id");
							}
							
							foreach ($sql_planning as $row) 
							{
								$greyDia=implode(",",array_unique(explode(",", $row[csf('grey_dia')])));
								$fabricDia=implode(",",array_unique(explode(",", $row[csf('fabric_dia')])));

								$plannig_data_arr[$row[csf('po_id')]]['fabric_dia']=$fabricDia;
								$plannig_data_arr[$row[csf('po_id')]]['grey_dia']=$greyDia;
							}

						$sql_dtls=sql_select($sqlData);
						$i=1;
						foreach ($sql_dtls as $row) {
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="150" align="center"><p><? echo $row[csf('issue_number')]; ?></p></td>
	                            <td width="100"  align="center"><? echo $knitting_source[$row[csf('knit_dye_source')]];// ?></td>
	                            <td width="150" align="center"><div style="word-break:break-all"><p><? echo change_date_format($row[csf('issue_date')]);//$buyer_name_arr[$job_data_arr[$po_id]["buyer_name"]]; ?></p></div></td>
	                            <td width="60"><div style="word-break:break-all"><? echo $plannig_data_arr[$row[csf('po_breakdown_id')]]['grey_dia']; ?></div></td>
	                            <td width="60" align="center"><div style="word-break:break-all"><? echo $plannig_data_arr[$row[csf('po_breakdown_id')]]['fabric_dia']; ?></div></td>

	                            <td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('yarn_lot')]; ?></div></td>
	                            <td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('stitch_length')]; ?></div></td>
	                            <td align="right"><? echo number_format($row[csf("qnty")],2); ?>&nbsp;</td> 
	                            
	                        </tr>
	                    <?
	                    $total_recv_qty+=$row[csf("qnty")];
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                    	<tr>
	                            <th colspan="8" align="right">Total</th>
	                            <th align="right"><? echo number_format($total_recv_qty,2); ?></th>
	                        </tr>
	                        
	                    </tfoot>
	                </table>
				</div>		
			</div>
		</fieldset>	
		<?
	}
	else if ($type==5)  {
		?>
		<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0">
	                <thead>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="150">Transfer ID</th>
	                        <th width="150">Transfer Date</th>
	                        <th width="100">To Order</th>
	                        <th width="100">Booking No</th>
	                        <th>Qty</th>
	                    </tr>
					</thead>
	             </table>
	             <div style="width:650px; max-height:410px; overflow-y:scroll" id="scroll_body">
	                <table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_body">
	                    <?
						$sql_transfer_out="select a.mst_id,d.recv_number,b.po_breakdown_id, a.prod_id, a.yarn_count, a.batch_lot, a.rack, a.self, sum(case when a.transaction_type in(6) and b.trans_type in(6) and b.entry_form in(82,83,110) then b.quantity end) as transfer_out
						from inv_transaction a, order_wise_pro_details b, wo_po_break_down c,inv_receive_master d 
						where a.id=b.trans_id and b.po_breakdown_id=c.id and d.id=a.mst_id and a.status_active=1  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=13 and b.entry_form in(82,83,110) and a.company_id=$companyID and b.po_breakdown_id=$orderID and a.prod_id=$prodID and a.transaction_type in(1,2,3,4,5,6) and b.trans_type in (1,2,3,4,5,6) 
						group by a.mst_id,d.recv_number,b.po_breakdown_id, a.prod_id, a.batch_lot, a.yarn_count, a.rack, a.self";
						$sql_dtls=sql_select($sql_transfer_out);
						$transferMstId="";
						foreach ($sql_dtls as $row) {
							$transferMstId.=$row[csf('mst_id')].",";
						}
 						$transferMstId=chop($transferMstId,",");
 						if($transferMstId!="")
 						{
						 	$sql_transfer_out_data=sql_select("SELECT  a.id,a.transfer_system_id,a.transfer_date,a.to_order_id,b.to_order_id as dtls_order_id from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.id in($transferMstId) group by a.id,a.transfer_system_id,a.transfer_date,a.to_order_id,b.to_order_id");
	 						$to_orderIDs="";
							foreach ($sql_transfer_out_data as $row) 
							{
								// $to_orderIDs.=$row[csf('to_order_id')].",";
								// $trnsOutArr[$row[csf('id')]]['transfer_system_id']=$row[csf('transfer_system_id')];
								// $trnsOutArr[$row[csf('id')]]['transfer_date']=$row[csf('transfer_date')];
								// $trnsOutArr[$row[csf('id')]]['to_order_id']=$row[csf('to_order_id')];
								if($row[csf('to_order_id')] != 0)
								{
									$to_orderIDs.=$row[csf('to_order_id')].",";
									$trnsOutArr[$row[csf('id')]]['transfer_system_id']=$row[csf('transfer_system_id')];
									$trnsOutArr[$row[csf('id')]]['transfer_date']=$row[csf('transfer_date')];
									$trnsOutArr[$row[csf('id')]]['to_order_id']=$row[csf('to_order_id')];
								}
								else
								{
									$to_orderIDs.=$row[csf('dtls_order_id')].",";
									$trnsOutArr[$row[csf('id')]]['transfer_system_id']=$row[csf('transfer_system_id')];
									$trnsOutArr[$row[csf('id')]]['transfer_date']=$row[csf('transfer_date')];
									$trnsOutArr[$row[csf('id')]]['to_order_id']=$row[csf('dtls_order_id')];
								}
							}
							$to_orderIDs=chop($to_orderIDs,",");
							$sql_order=sql_select("select id,po_number from  wo_po_break_down where id in($to_orderIDs) and status_active=1 and is_deleted=0");
							foreach ($sql_order as $row) {
								$po_arr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
							}
							$sql_booking=sql_select("select b.booking_no,b.po_break_down_id from wo_booking_mst a,wo_booking_dtls b ,wo_po_break_down c where a.booking_no=b.booking_no  and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id in($to_orderIDs) group by b.booking_no,b.po_break_down_id");
							foreach ($sql_booking as $row) {
								$booking_arr[$row[csf('po_break_down_id')]]['booking_no'].=$row[csf('booking_no')].",";
							}
						}
							$i=1;
							foreach ($sql_dtls as $row) 
							{
								if($row[csf('self')]=="") $row[csf('self')]=0;
								if($row[csf('rack')]=="") $row[csf('rack')]=0;
			                    ?>
			                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
			                            <td width="30"><? echo $i; ?></td>
			                            <td width="150"><p><? echo $trnsOutArr[$row[csf('mst_id')]]['transfer_system_id']; ?></p></td>
			                            <td width="150"  align="center"><? echo change_date_format($trnsOutArr[$row[csf('mst_id')]]['transfer_date']);// ?></td>
			                            <td width="100"><p><? echo $po_arr[$trnsOutArr[$row[csf('mst_id')]]['to_order_id']]['po_number']; ?></p></td>
			                            <td width="100"><p><? echo $booking_arr[$trnsOutArr[$row[csf('mst_id')]]['to_order_id']]['booking_no']; ?></p></td>
			                            <td align="right"><? echo number_format($row[csf('transfer_out')],2); ?>&nbsp;</td> 
			                        </tr>
			                    <?
			                    $total_transferOut_qty+=$row[csf('transfer_out')];
			                    $i++;
		                    }
	                   	 ?>
	                    <tfoot>
	                    	<tr>
	                            <th colspan="5" align="right">Total</th>
	                            <th align="right"><? echo number_format($total_transferOut_qty,2); ?></th>
	                        </tr>
	                        
	                    </tfoot>
	                </table>
				</div>		
			</div>
		</fieldset>	
		<?
	}
	else if ($type==6) {
		?>
		<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0">
	                <thead>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="150">Yarn Brand</th>
	                        <th width="100">Lot</th>
	                        <th width="100">Stich Leng.</th>
	                        <th width="100">Rack</th>
	                        <th width="100">Shelf</th>
	                        <th>Qty</th>
	                    </tr>
					</thead>
	             </table>
	             <div style="width:650px; max-height:410px; overflow-y:scroll" id="scroll_body">
	                <table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_body">
	                    <?
	                     $sql_stock="select 
	                  	sum(case when c.trans_type in(1,4,5)  then c.quantity end)as recv,
 						sum(case when c.trans_type in(2,3,6)  then c.quantity end) as issue,a.rack,a.self
  						  from inv_transaction a,order_wise_pro_details c  
  						  where a.id=c.trans_id and c.trans_type in(1,4,5,2,3,6) and c.entry_form in(2,22,58,16,61,81,82,83,84) and c.prod_id=$prodID and c.po_breakdown_id=$orderID and c.status_active=1 and c.is_deleted=0 and trans_id>0 group by a.rack,a.self";
	                  
						$sql_stock_dtls=sql_select($sql_stock);
						$i=1;
						foreach ($sql_stock_dtls as $row) {
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="150"><p><? echo $yarnBrand; ?></p></td>
	                            <td width="100"  align="center"><? echo $yLot; ?></td>
	                            <td width="100" align="center"><? echo $stichLength; ?></td>
	                            <td width="100"><div style="word-break:break-all"><? echo $row[csf('rack')];?></div></td>
	                            <td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('self')]; ?></div></td>
	                            <td align="right"><? echo $balance= $row[csf("recv")]-$row[csf('issue')]; ?>&nbsp;</td> 
	                            
	                        </tr>
	                    <?
	                    $total_balance+=$balance;
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                    	<tr>
	                            <th colspan="6" align="right">Total</th>
	                            <th align="right"><? echo number_format($total_balance,2); ?></th>
	                        </tr>
	                        
	                    </tfoot>
	                </table>
				</div>		
			</div>
		</fieldset>	
		<?	
	}
}
if($action=="fabric_booking_popup")
{
	echo load_html_head_contents("Fabric Booking Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	?>
	<fieldset style="width:890px">
		<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
			<thead>
				<th width="40">SL</th>
				<th width="60">Booking No</th>
				<th width="50">Year</th>
				<th width="60">Type</th>
				<th width="80">Booking Date</th>
				<th width="90">Color</th>
				<th width="110">Fabric</th>
				<th width="150">Composition</th>
				<th width="70">GSM</th>
				<th width="70">Dia</th>
				<th>Grey Req. Qty.</th>
			</thead>
		</table>
		<div style="width:100%; max-height:320px; overflow-y:scroll">
			<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
				<?
				if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
				else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
		        else $year_field="";//defined Later
		        
		        $i=1; $tot_grey_qnty=0;
		        $sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_type, a.is_short, a.booking_date, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width, sum(b.grey_fab_qnty) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 group by a.id, a.booking_type, a.is_short, a.booking_date, a.insert_date, a.booking_no_prefix_num, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width order by a.id";
		       //echo $sql;//die;
		        $result= sql_select($sql);
		        foreach($result as $row)
		        {
		        	if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
		        	
		        	if($row[csf('booking_type')]==4) 
		        	{
		        		$booking_type="Sample";
		        	}
		        	else
		        	{
		        		if($row[csf('is_short')]==1) $booking_type="Short"; else $booking_type="Main"; 
		        	}
		        	?>
		        	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		        		<td width="40"><? echo $i; ?></td>
		        		<td width="60">&nbsp;&nbsp;&nbsp;<? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
		        		<td width="50" align="center"><? echo $row[csf('year')]; ?></td>
		        		<td width="60" align="center"><p><? echo $booking_type; ?></p></td>
		        		<td width="80" align="center"><? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>
		        		<td width="90"><p><? echo $color_arr[$row[csf('fabric_color_id')]]; ?></p></td>
		        		<td width="110"><p><? echo $row[csf('construction')]; ?></p></td>
		        		<td width="150"><p><? echo $row[csf('copmposition')]; ?></p></td>
		        		<td width="70"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
		        		<td width="70"><p><? echo $row[csf('dia_width')]; ?></p></td>
		        		<td align="right" style="padding-right:5px"><? echo number_format($row[csf('grey_fab_qnty')],2); ?></td>
		        	</tr>
		        	<? 
		        	$tot_grey_qnty+=$row[csf('grey_fab_qnty')];
		        	$i++;
		        } 
		        ?>
		        <tfoot>
		        	<th colspan="10">Total</th>
		        	<th style="padding-right:5px"><? echo number_format($tot_grey_qnty,2); ?></th>
		        </tfoot>
    		</table>
		</div> 
	</fieldset>
    <?
    exit();
}
if($action=="grey_recv_popup")
{
	echo load_html_head_contents("Grey Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$companyID= $companyID;
	$orderID=$orderID;
	$programNo=$programNo;
	$prodID=$prodID;
	$product_details_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
	$color_name_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
	$brand_name_arr = return_library_array("select id, brand_name from  lib_brand", 'id', 'brand_name');
	$buyer_name_arr = return_library_array("select id, buyer_name from  lib_buyer", 'id', 'buyer_name');
	$store_name_arr = return_library_array("select id, store_name from  lib_store_location", 'id', 'store_name');

	?>
	<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                		<?
                		if ($type==1) {$tbl_title='Grey Fabrics Receive Details';}else{$tbl_title='Grey Fabrics Issue Details';}
                		?>
                        <th colspan="12"><b><? echo $tbl_title; ?></b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="70"><?if ($type==1) { echo 'Receive ID';}else{echo 'Issue ID';} ?></th>
                        <th width="120"><?if ($type==1) { echo 'Receive Date';}else{echo 'Issue Date';} ?></th>
                        <th width="200">Fabric Des</th>
                        <th width="80">Store</th>
                        <th width="80">Room</th>
                        <th width="100">Rack </th>
                        <th width="100">Shelf</th>
                        <th width="60">Bin</th>
                        <th width="60">UOM</th>
                        <th width="60">Qty</th>
                        <th>No of Roll</th>
                    </tr>
				</thead>
             </table>
             <div style="width:1050px; max-height:410px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0" id="table_body">
                    <?
                    if ($type==1) 
                    {
                    	$programData=sql_select("select a.recv_number as sys_number,a.receive_date as sys_no_date,sum(b.cons_quantity) as grey_receive_qnty,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id  
						from inv_receive_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d  
						where a.id=b.mst_id and b.id=c.trans_id and a.id=d.mst_id and c.dtls_id=d.id and a.entry_form in(2,22,58) and c.trans_id <>0 and c.trans_type=1 and c.entry_form in (2,22,58) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($prodID) group by a.recv_number,a.receive_date,b.room,b.rack,b.self,b.bin_box,b.cons_uom,d.no_of_roll,b.store_id,c.po_breakdown_id,b.prod_id");
                    }
                    else
                    {
                    	$programData=sql_select("select a.issue_number as sys_number,a.issue_date as sys_no_date,sum(b.cons_quantity) as grey_receive_qnty,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id from inv_issue_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d where a.id=b.mst_id and b.id=c.trans_id and c.dtls_id=d.id and a.entry_form in(16,61) and c.trans_id <>0 and c.trans_type=2 and c.entry_form in (16,61) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($prodID) group by a.issue_number,a.issue_date,b.room,b.rack,b.self,b.bin_box,b.cons_uom,d.no_of_roll,b.store_id,c.po_breakdown_id,b.prod_id");
                    }				
					
					
				
					$i=1;
					foreach ($programData as $row) {
						
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="70"><p><? echo $row[csf('sys_number')]; ?></p></td>
                            <td width="120"  align="center"><? echo $row[csf('sys_no_date')];// ?></td>
                            <td width="200"><div style="word-break:break-all"><p><? echo $product_details_arr[$row[csf('prod_id')]]; ?></p></div></td>
                            <td width="80"><div style="word-break:break-all"><? echo $store_name_arr[$row[csf('store_id')]];//$buyer_name_arr[$job_data_arr[$po_id]["buyer_name"]]; ?></div></td>
                            <td width="80" align="center"><div style="word-break:break-all"><? echo $row[csf('room')]; ?></div></td>
                            <td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('rack')]; ?></div></td>
                            <td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('self')]; ?></div></td>
                            <td width="60" align="center"><p><? echo $row[csf('bin_box')]; ?></p>&nbsp;</td>
                            <td width="60" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
                            <td width="60" align="right"><? echo $row[csf('grey_receive_qnty')]; ?>&nbsp;</td>
                            <td align="right"><? echo $row[csf("no_of_roll")]; ?>&nbsp;</td> 
                            
                        </tr>
                    <?
                    $total_recv_qty+=$row[csf('grey_receive_qnty')];
                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="10" align="right">Total</th>
                            <th align="right"><? echo number_format($total_recv_qty,2); ?></th>
                            <th align="right"></th>
                        </tr>
                        
                    </tfoot>
                </table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                		<?
                		if ($type==1) {$tbl_title='Grey Issue Return Details';}else{$tbl_title='Grey Receive Return Details';}
                		?>
                        <th colspan="12"><b><? echo $tbl_title; ?></b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="70"><?if ($type==1) { echo 'Issue Return ID';}else{echo 'Receive Return ID';} ?></th>
                        <th width="120"><?if ($type==1) { echo 'Issue Date';}else{echo 'Receive Date';} ?></th>
                        <th width="200">Fabric Des</th>
                        <th width="80">Store</th>
                        <th width="80">Room</th>
                        <th width="100">Rack </th>
                        <th width="100">Shelf</th>
                        <th width="60">Bin</th>
                        <th width="60">UOM</th>
                        <th width="60">Qty</th>
                        <th>No of Roll</th>
                    </tr>
				</thead>
             </table>
             <div style="width:1050px; max-height:410px; overflow-y:scroll" id="scroll_body2">
                <table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0" id="table_body2">
                    <?	
                    if ($type==1) 
                    {
                    	$programData=sql_select("select a.recv_number as sys_number,a.receive_date as sys_number_date,sum(b.cons_quantity) as grey_issue_rtn_qnty,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id,count(d.id) as roll_no   
						from inv_receive_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d  
						where a.id=b.mst_id and b.id=c.trans_id and a.id=d.mst_id and c.dtls_id=d.id and a.entry_form in(84,51) and c.trans_id <>0 and c.entry_form in (84,51) and c.trans_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($prodID) group by a.recv_number,a.receive_date,b.room,b.rack,b.self,b.bin_box,b.cons_uom,d.no_of_roll,b.store_id,c.po_breakdown_id,b.prod_id");
                    }
                    else
                    {			
					$programData=sql_select("select a.issue_number as sys_number,a.issue_date as sys_number_date,sum(b.cons_quantity) as grey_issue_rtn_qnty,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id,count(d.id) as roll_no from inv_issue_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d where a.id=b.mst_id and b.id=c.trans_id and a.id=d.mst_id and a.entry_form in(45) and c.trans_id <>0 and c.entry_form in (45) and c.trans_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=1 and c.po_breakdown_id in($orderID) and c.prod_id in($prodID) group by a.issue_number,a.issue_date,b.room,b.rack,b.self,b.bin_box,b.cons_uom,d.no_of_roll,b.store_id,c.po_breakdown_id,b.prod_id ");
					}
					
					$ii=1;
					foreach ($programData as $row) {
						$store_arr[$row[csf('prod_id')]]['store_id']=$row[csf('store_id')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $ii;?>">
                            <td width="30"><? echo $ii; ?></td>
                            <td width="70"><p><? echo $row[csf('sys_number')]; ?></p></td>
                            <td width="120"  align="center"><? echo $row[csf('sys_number_date')];// ?></td>
                            <td width="200"><div style="word-break:break-all"><p><? echo $product_details_arr[$row[csf('prod_id')]]; ?></p></div></td>
                            <td width="80"><div style="word-break:break-all"><? echo $store_name_arr[$row[csf('store_id')]];//$buyer_name_arr[$job_data_arr[$po_id]["buyer_name"]]; ?></div></td>
                            <td width="80" align="center"><div style="word-break:break-all"><? echo $row[csf('room')]; ?></div></td>
                            <td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('rack')]; ?></div></td>
                            <td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('self')]; ?></div></td>
                            <td width="60" align="center"><p><? echo $row[csf('bin_box')]; ?></p>&nbsp;</td>
                            <td width="60" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
                            <td width="60" align="right"><? echo $row[csf('grey_issue_rtn_qnty')]; ?>&nbsp;</td>
                            <td align="right"><? echo $row[csf("roll_no")]; ?>&nbsp;</td> 
                            
                        </tr>
                    <?
                    $total_issue_rtn_qty+=$row[csf('grey_issue_rtn_qnty')];
                    $ii++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="10" align="right">Total</th>
                            <th align="right"><? echo number_format($total_issue_rtn_qty,2); ?></th>
                            <th align="right"></th>
                        </tr>
                        
                    </tfoot>
                </table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                		<?
                		if ($type==1) {$tbl_title='Grey Transfer In Details';}else{$tbl_title='Grey Transfer Out Details';}
                		?>
                        <th colspan="12"><b><? echo $tbl_title; ?></b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="70">Transfer ID</th>
                        <th width="120">Receive Date</th>
                        <th width="200">Fabric Des</th>
                        <th width="80">Store</th>
                        <th width="80">Room</th>
                        <th width="100">Rack </th>
                        <th width="100">Shelf</th>
                        <th width="60">Bin</th>
                        <th width="60">UOM</th>
                        <th width="60">Qty</th>
                        <th>No of Roll</th>
                    </tr>
				</thead>
             </table>
             <div style="width:1050px; max-height:410px; overflow-y:scroll" id="scroll_body3">
                <table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0" id="table_body3">
                    <?	
                    if ($type==1) 
                    {
						//$programData=sql_select("select a.transfer_system_id,a.transfer_date,a.to_store_id,sum(b.transfer_qnty) as transfer_qnty,b.to_rack as rack,b.to_shelf as self,b.bin_box,b.uom,b.no_of_roll,c.po_breakdown_id,b.from_prod_id as prod_id from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.to_trans_id=c.trans_id and a.entry_form in(83) and c.trans_id <>0 and c.entry_form in (83) and c.trans_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$companyID  and c.prod_id in($prodID) group by c.po_breakdown_id,b.from_prod_id,a.transfer_system_id,a.transfer_date,a.to_store_id,b.to_rack,b.to_shelf,b.bin_box,b.uom,b.no_of_roll");  
						$programData=sql_select("select a.transfer_system_id,a.transfer_date,a.to_store_id,sum(b.transfer_qnty) as transfer_qnty,b.to_rack as rack,b.to_shelf as self,b.bin_box,b.uom,b.no_of_roll,c.po_breakdown_id,b.from_prod_id as prod_id,d.roll_no ,sum(d.qnty) as roll_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c,pro_roll_details d where a.id=b.mst_id and b.to_trans_id=c.trans_id and b.id=d.dtls_id and a.entry_form in(83) and c.trans_id <>0 and c.entry_form in (83) and c.trans_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=1 and c.prod_id in($prodID) group by c.po_breakdown_id,b.from_prod_id,a.transfer_system_id,a.transfer_date,a.to_store_id,b.to_rack,b.to_shelf,b.bin_box,b.uom,b.no_of_roll,d.roll_no");    

						                 
					}			
					else
					{
						//$programData=sql_select("select a.transfer_system_id,a.transfer_date,a.to_store_id,sum(b.transfer_qnty) as transfer_qnty,b.to_rack as rack,b.to_shelf as self,b.bin_box,b.uom,b.no_of_roll,c.po_breakdown_id,b.from_prod_id as prod_id from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.trans_id=c.trans_id and a.entry_form in(83) and c.trans_id <>0 and c.entry_form in (83) and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$companyID  and c.prod_id in($prodID) group by c.po_breakdown_id,b.from_prod_id,a.transfer_system_id,a.transfer_date,a.to_store_id,b.to_rack,b.to_shelf,b.bin_box,b.uom,b.no_of_roll");

						$programData=sql_select("select a.transfer_system_id,a.transfer_date,a.to_store_id,sum(b.transfer_qnty) as transfer_qnty,b.to_rack as rack,b.to_shelf as self,b.bin_box,b.uom,b.no_of_roll,c.po_breakdown_id,b.from_prod_id as prod_id,d.roll_no,sum(d.qnty) as roll_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c,pro_roll_details d  where a.id=b.mst_id and b.trans_id=c.trans_id and  b.id=d.dtls_id and a.entry_form in(83) and c.trans_id <>0 and c.entry_form in (83) and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$companyID  and c.prod_id in($prodID) group by c.po_breakdown_id,b.from_prod_id,a.transfer_system_id,a.transfer_date,a.to_store_id,b.to_rack,b.to_shelf,b.bin_box,b.uom,b.no_of_roll,d.roll_no");
					}

				

					$iii=1;
					foreach ($programData as $row) {
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $iii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $iii;?>">
                            <td width="30"><? echo $iii; ?></td>
                            <td width="70"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="120"  align="center"><? echo $row[csf('transfer_date')];// ?></td>
                            <td width="200"><div style="word-break:break-all"><p><? echo $product_details_arr[$row[csf('prod_id')]]; ?></p></div></td>
							<td width="80"><div style="word-break:break-all"><? if($row[csf('store_id')]>0){echo $store_name_arr[$row[csf('to_store_id')]];}else{echo  $store_name_arr[$store_arr[$row[csf('prod_id')]]['store_id']];} ?></div></td>                            <td width="80" align="center"><div style="word-break:break-all"><? echo $row[csf('room')]; ?></div></td>
                            <td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('rack')]; ?></div></td>
                            <td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('self')]; ?></div></td>
                            <td width="60" align="center"><p><? echo $row[csf('bin_box')]; ?></p>&nbsp;</td>
                            <td width="60" align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</td>
                            <td width="60" align="right"><? echo $row[csf('roll_qnty')]; ?>&nbsp;</td>
                            <td align="right"><? echo $row[csf("roll_no")]; ?>&nbsp;</td> 
                            
                        </tr>
                    <?
                    $total_trnsf_qty+=$row[csf('roll_qnty')];
                    $iii++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="10" align="right">Total</th>
                            <th align="right"><? echo number_format($total_trnsf_qty,2); ?></th>
                            <th align="right"></th>
                        </tr>
                        
                    </tfoot>
                </table>
			</div>
					
		</div>
	</fieldset>	

<? 
die;
					
					foreach( $programData as $row )
					{
						$program_no_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]=$row[csf('prog_no')];
					}

			            $i=1; $product_id_arr=array(); $recvIssue_array=array(); $trans_arr=array();
						$sql_trans="select b.trans_type, b.po_breakdown_id, b.prod_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2,22,13,16,45,51,58,61,80,81,83,84,110,183) and a.item_category=13 and a.transaction_type in(1,2,3,4,5,6) and b.trans_type in(1,2,3,4,5,6) $trans_date group by b.trans_type, b.po_breakdown_id, b.prod_id";
						$result_trans=sql_select( $sql_trans );
						foreach ($result_trans as $row)
						{
							$recvIssue_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('trans_type')]]=$row[csf('qnty')];
							$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
						}
	                  
	        			foreach($dataArray as $row)
	                    { 
						//$issue_id_arr[]=$row[csf('id')];
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
							$y_count = array_unique(explode(",", $row[csf('yarn_count')]));
							//$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
							$yarn_count_value = "";
							foreach ($y_count as $val) {
							if ($val > 0) {
							if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
							}
							}
							/*$brand_value = "";
							foreach ($brand_id as $bid) {
							if ($bid > 0) {
							if ($brand_value == '') $brand_value = $brand_name_arr[$bid]; else $brand_value .= ", " . $brand_name_arr[$bid];
							}
							}*/
							$po_id=$roll_no_data_arr[$row[csf("dtls_id")]][$row[csf("prod_id")]]["po_id"];
							$barcode_no=$roll_no_data_arr[$row[csf("dtls_id")]][$row[csf("prod_id")]]["barcode_no"];
							$no_of_roll=$roll_no_data_arr2[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["no_of_roll"];
							//echo $po_id.'dff';
							$brand_value=$brand_name_arr[$roll_data_arr[$barcode_no][$po_id][$row[csf("prod_id")]]["brand_id"]];
							$body_part_name=$body_part[$roll_data_arr[$barcode_no][$po_id][$row[csf("prod_id")]]["body_part_id"]];
							$gsm=$roll_data_arr[$barcode_no][$po_id][$row[csf("prod_id")]]["gsm"] ;
							$width=$roll_data_arr[$barcode_no][$po_id][$row[csf("prod_id")]]["width"] ;
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="70"><p><? echo $row[csf('issue_date')]; ?></p></td>
                            <td width="120"><? echo $row[csf('issue_number')];// ?></td>
                            <td width="150"><div style="word-break:break-all"><p><? echo $product_details_arr[$row[csf('prod_id')]]; ?></p></div></td>
                            <td width="80"><div style="word-break:break-all"><? echo $buyer_name_arr[$job_data_arr[$po_id]["buyer_name"]]; ?></div></td>
                            <td width="80"><div style="word-break:break-all"><? echo $job_data_arr[$po_id]["style"]; ?></div></td>
                            <td width="100"><div style="word-break:break-all"><? echo $job_data_arr[$po_id]["job_no"] ; ?></div></td>
                            <td width="100"><div style="word-break:break-all"><? echo $body_part_name; ?></div></td>
                            <td width="60"><p><? echo $row[csf('stitch_length')]; ?></p>&nbsp;</td>
                            <td width="60"><? echo $gsm; ?>&nbsp;</td>
                            <td width="60"><? echo $width; ?>&nbsp;</td>
                            <td><? echo $dya_gauge_arr[$row[csf("machine_id")]]["dia_width"] ?>&nbsp;</td> 
                            
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="10" align="right">Total</th>
                            <th align="right"><? echo number_format($total_issue_qnty,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>
  <script>
  setFilterGrid("table_body",-1);
  </script>
    <br>
    
    
    <?
	exit();
}
