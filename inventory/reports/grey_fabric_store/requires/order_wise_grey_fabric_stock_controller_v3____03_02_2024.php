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
	echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}
if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=113 and is_deleted=0 and status_active=1");
	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#show').hide();\n";
	echo "$('#show2').hide();\n";
	echo "$('#order_wise').hide();\n";
	echo "$('#order_wise_2').hide();\n";
	echo "$('#order_wise_3').hide();\n";
	echo "$('#order_wise_2_excel').hide();\n";
	echo "$('#order_wise_4').hide();\n";
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==108){echo "$('#show').show();\n";}
			if($id==255){echo "$('#show2').show();\n";}
			if($id==124){echo "$('#order_wise').show();\n";}     
			if($id==577){echo "$('#order_wise_2').show();\n";}     
			if($id==578){echo "$('#order_wise_3').show();\n";}     
			if($id==579){echo "$('#order_wise_2_excel').show();\n";}     
			if($id==363){echo "$('#order_wise_4').show();\n";}     
		}
	}
	exit(); 
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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'order_wise_grey_fabric_stock_controller_v3', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_booking_no_search_list_view', 'search_div', 'order_wise_grey_fabric_stock_controller_v3', 'setFilterGrid(\'tbl_list_div\',-1)');" style="width:100px;" />
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

	$sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a  where b.id=a.job_id and b.company_name=$data[0] and b.is_deleted=0 $buyer_name $job_no_cond ORDER BY b.job_no DESC";
		//echo $sql;
	$arr=array(1=>$buyer_arr);

	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "order_wise_grey_fabric_stock_controller_v3",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	disconnect($con);
	exit();
}

if ($action == "load_drop_down_store")
{
	$data = explode("**", $data);

	if ($data[1] == 2)
	{
		$disable = 1;
	}
	else
	{
		$disable = 0;
	}
	echo create_drop_down("cbo_store_name", 100, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type in(13)  order by a.store_name", "id,store_name", 1, "-- All Store--", 0, "", $disable);
	exit();
}

$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$machine_arr=return_library_array( "select id, dia_width from lib_machine_name", "id", "dia_width"  );


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$rpt_type=str_replace("'","",$rpt_type);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_search_comm=str_replace("'","",$txt_search_comm);
	$cbo_sock_for=str_replace("'","",$cbo_sock_for);
	$cbo_value_with=str_replace("'","",$cbo_value_with);
	$booking_no=str_replace("'","",$txt_booking_no);
	$cbo_date_cat=str_replace("'","",$cbo_date_cat);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_presentation=str_replace("'","",$cbo_presentation);
	$cbo_store_wise=str_replace("'","",$cbo_store_wise);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	
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
	
	// ============= Booking Condition Start ====================
	if($booking_no != '')
	{
		if($booking_no!='') $book_no_cond="and a.booking_no_prefix_num in($booking_no)";else $book_no_cond="";
		$sql="SELECT a.buyer_id,b.job_no, b.po_break_down_id as po_id, b.construction, b.fabric_color_id, a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $book_no_cond";
		// and a.booking_no='RpC-Fb-22-00011'
		$sql_result=sql_select($sql);
		$po_ids='';

		foreach( $sql_result as $row )
		{
			//$key=$row[csf('buyer_id')].$row[csf('job_no')].$row[csf('po_id')].$row[csf('construction')].$row[csf('fabric_color_id')];
			//$grey_qnty_array[$key]+=$row[csf('grey_req_qnty')];
			
			if($po_ids=='') $po_ids=$row[csf('po_id')];else $po_ids.=",".$row[csf('po_id')];
			// $booking_array[$row[csf('po_id')]]['booking_no'].=$row[csf('booking_no')].',';
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
	// ============= Booking Condition End ======================
	
	$date_from=str_replace("'","",$txt_date_from_trans);
	if( $date_from=="") $receive_date=""; else $receive_date= " and e.receive_date <=".$txt_date_from_trans."";
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
		$buyer_id_cond=" and b.buyer_name=$cbo_buyer_id";
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

	$store_cond="";$store_trans_cond="";
	if ($cbo_store_name!=0) 
	{
		$store_cond=" and c.store_id=$cbo_store_name";
		$store_trans_cond=" and f.store_id=$cbo_store_name";
	}
	
	$search_cond='';$search_trans_cond='';
	if($cbo_search_by==1)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.style_ref_no LIKE '$txt_search_comm%'";
		if ($txt_search_comm=="") $search_trans_cond.=""; else $search_trans_cond.=" and d.style_ref_no LIKE '$txt_search_comm%'";
	}
	else if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.po_number LIKE '$txt_search_comm%'";
		if ($txt_search_comm=="") $search_trans_cond.=""; else $search_trans_cond.=" and c.po_number LIKE '$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.file_no LIKE '$txt_search_comm%'";
		if ($txt_search_comm=="") $search_trans_cond.=""; else $search_trans_cond.=" and c.file_no LIKE '$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.grouping LIKE '$txt_search_comm%'";
		if ($txt_search_comm=="") $search_trans_cond.=""; else $search_trans_cond.=" and c.grouping LIKE '$txt_search_comm%'";
	}
	else
	{
		$search_cond.="";
	}
	
	$order_cond="";
	if($cbo_sock_for==1)
	{
		$order_cond=" and a.shiping_status<>3 and a.is_confirmed=1";
	}
	else if($cbo_sock_for==2)
	{
		$order_cond=" and a.status_active=3";
	}
	else if($cbo_sock_for==3)
	{
		$order_cond=" and a.shiping_status=3 and a.status_active=1";
	}
	else if($cbo_sock_for==4) // Block Order ( Projected )
	{
		$order_cond=" and a.shiping_status<>3 and a.is_confirmed=2";
	}
	else
	{
		$order_cond="";
	}

	$po_date_cond="";
	if($txt_date_from !="" && $txt_date_to !="")
	{
		if($cbo_date_cat==1)
		{
			$po_date_cond=" and a.pub_shipment_date between '$txt_date_from' and '$txt_date_to'";
			$po_date_trans_cond=" and c.pub_shipment_date between '$txt_date_from' and '$txt_date_to'";
		}
		elseif($cbo_date_cat==2)
		{
			$po_date_cond=" and a.update_date between '$txt_date_from' and '$txt_date_to' and a.status_active=3";
			$po_date_trans_cond=" and c.update_date between '$txt_date_from 12:59:59 AM' and '$txt_date_to 12:59:59 PM' and c.status_active=3";
		}
	}

	$product_array=array();	
	$prod_query="Select id, detarmination_id, gsm, dia_width, brand, yarn_count_id, lot, color from product_details_master where item_category_id=13 and company_id=$cbo_company_id and status_active=1 and is_deleted=0 ";
	$prod_query_sql=sql_select($prod_query);
	if(count($prod_query_sql)>0)
	{
		foreach( $prod_query_sql as $row )
		{
			$product_array[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
			$product_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
			$product_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
			$product_array[$row[csf('id')]]['brand']=$row[csf('brand')];
			$product_array[$row[csf('id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
			$product_array[$row[csf('id')]]['lot']=$row[csf('lot')];
			$product_array[$row[csf('id')]]['color']=$row[csf('color')];
		}
	}

	if($db_type==0) $select_up_date=" DATE_FORMAT(a.update_date, '%d %M %Y') as calcel_date "; else $select_up_date=" to_char(a.update_date, 'DD-MM-YYYY') as calcel_date ";	
	
	$con = connect();
	$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (67)");
	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=67");
	oci_commit($con);
	
	// =============== Receive Start =================================

	$sql_recv="SELECT e.entry_form, e.booking_id as e_booking_id, g.booking_no as g_booking_no, a.po_number as po_number, a.file_no, a.grouping, a.po_quantity as po_quantity, b.job_no, b.buyer_name, b.style_ref_no, (g.qnty) as quantity, a.id as po_breakdown_id, d.prod_id, d.stitch_length, d.brand_id, d.color_id, (d.color_range_id) as color_range_id, d.yarn_lot, d.yarn_count, c.store_id, d.floor_id, d.room, d.rack, d.self, d.bin_box, (d.machine_no_id) as machine_no_id, d.id as did, d.no_of_roll, e.booking_no as booking_no, e.entry_form, a.pub_shipment_date, a.status_active, $select_up_date, g.barcode_no 
	from wo_po_details_master b, wo_po_break_down a, pro_roll_details g, pro_grey_prod_entry_dtls d, inv_transaction c, inv_receive_master e
	where a.t_year>=2020 and b.id=a.job_id and a.id=g.po_breakdown_id and g.dtls_id=d.id and d.trans_id=c.id and c.transaction_type=1 and c.mst_id=e.id and d.mst_id=e.id and b.status_active=1 and b.is_deleted=0 and e.entry_form in (2,22,58) and g.entry_form in(2,22,58) and d.status_active=1 and d.is_deleted=0 AND e.company_id=$cbo_company_id and e.item_category=13 and e.status_active=1 and e.is_deleted=0 and g.booking_without_order=0
	$year_cond $buyer_id_cond $job_no_cond $search_cond $receive_date $order_cond $po_id_cond $po_date_cond $store_cond";
    //echo $sql_recv;

	$recvArray=sql_select( $sql_recv );
	$data_arr=array();$poArr = array();$prodArray=array(); $dupli_qty_chk=array();
	foreach ($recvArray as $key => $row) 
	{
		if($dupli_qty_chk[$row[csf("prop_id")]] == "")
		{
			if ($row[csf("store_id")]=="") $row[csf("store_id")]=0;
			if ($row[csf("floor_id")]=="") $row[csf("floor_id")]=0;
			if ($row[csf("room")]=="") $row[csf("room")]=0;
			if ($row[csf("rack")]=="") $row[csf("rack")]=0;
			if ($row[csf("self")]=="") $row[csf("self")]=0;
			if ($row[csf("bin_box")]=="") $row[csf("bin_box")]=0;

			$dupli_qty_chk[$row[csf("prop_id")]] =$row[csf("prop_id")];
			$str_ref=$row[csf("po_breakdown_id")]."*".$row[csf("prod_id")]."*".$row[csf("store_id")]."*".$row[csf("floor_id")]."*".$row[csf("room")]."*".$row[csf("rack")]."*".$row[csf("self")]."*".$row[csf("bin_box")]."*".$row[csf("yarn_lot")];
			$data_arr[$row[csf("job_no")]][$str_ref]['job_no']=$row[csf("job_no")];
			$data_arr[$row[csf("job_no")]][$str_ref]['status_active']=$row[csf("status_active")];
			$data_arr[$row[csf("job_no")]][$str_ref]['calcel_date']=$row[csf("calcel_date")];
			$data_arr[$row[csf("job_no")]][$str_ref]['po_number']=$row[csf("po_number")];
			$data_arr[$row[csf("job_no")]][$str_ref]['file_no']=$row[csf("file_no")];
			$data_arr[$row[csf("job_no")]][$str_ref]['grouping']=$row[csf("grouping")];
			$data_arr[$row[csf("job_no")]][$str_ref]['po_quantity']+=$row[csf("po_quantity")];
			$data_arr[$row[csf("job_no")]][$str_ref]['buyer_name']=$row[csf("buyer_name")];
			$data_arr[$row[csf("job_no")]][$str_ref]['style_ref_no']=$row[csf("style_ref_no")];
			$data_arr[$row[csf("job_no")]][$str_ref]['pub_shipment_date']=$row[csf("pub_shipment_date")];


			if($row[csf("entry_form")]==58)
			{
				$data_arr[$row[csf("job_no")]][$str_ref]['rec_roll']++;
			}
			else if($row[csf("entry_form")]==2 || $row[csf("entry_form")]==22)
			{
				$data_arr[$row[csf("job_no")]][$str_ref]['rec_roll'] +=$row[csf("no_of_roll")];
			}
			
			if($row[csf("entry_form")]==2)
			{
				$data_arr[$row[csf("job_no")]][$str_ref]['program_no'].=$row[csf("e_booking_id")].',';
			}
			else if($row[csf("entry_form")]==22)
			{
				$data_arr[$row[csf("job_no")]][$str_ref]['program_no'].=$row[csf("f_booking_id")].',';
			}
			else if($row[csf("entry_form")]==58)
			{
				$data_arr[$row[csf("job_no")]][$str_ref]['program_no'] .=$row[csf("g_booking_no")].',';
			}

			$data_arr[$row[csf("job_no")]][$str_ref]['quantity']+=$row[csf("quantity")];
			$data_arr[$row[csf("job_no")]][$str_ref]['stitch_length'].=$row[csf("stitch_length")].',';
			$data_arr[$row[csf("job_no")]][$str_ref]['brand_id'].=$row[csf("brand_id")].',';
			$data_arr[$row[csf("job_no")]][$str_ref]['color_id'].=$row[csf("color_id")].',';
			$data_arr[$row[csf("job_no")]][$str_ref]['color_range_id'].=$row[csf("color_range_id")].',';
			// $data_arr[$row[csf("job_no")]][$str_ref]['yarn_lot'].=$row[csf("yarn_lot")].',';
			$data_arr[$row[csf("job_no")]][$str_ref]['yarn_count'].=$row[csf("yarn_count")].',';
			$data_arr[$row[csf("job_no")]][$str_ref]['machine_no_id']=$row[csf("machine_no_id")];		
			$data_arr[$row[csf("job_no")]][$str_ref]['booking_no'].=$row[csf("booking_no")].',';
			$data_arr[$row[csf("job_no")]][$str_ref]['entry_form']=$row[csf("entry_form")];
			
			// $data_arr[$row[csf("job_no")]][$str_ref]['barcode_no'] .=$row[csf("barcode_no")].",";

			$poArr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
			$prodArray[$row[csf("prod_id")]] = $row[csf("prod_id")];
		}
	}





	// echo "<pre>";print_r($data_arr);die;
	// =============== Receive End =================================

	// =============== Transfer In Start ===========================
	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond_trans=""; else $job_no_cond_trans=" and d.job_no_prefix_num in ($job_no) ";
	$year_id=str_replace("'","",$cbo_year);
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond_trans=" and year(d.insert_date)=$year_id"; else $year_cond_trans="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_cond_trans=" and TO_CHAR(d.insert_date,'YYYY')=$year_id"; else $year_cond_trans="";
	}
	
	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";
	
	$date_from=str_replace("'","",$txt_date_from_trans);
	if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from_trans."";

	$trans_order_cond="";
	if($cbo_sock_for==1)
	{
		$trans_order_cond=" and c.shiping_status<>3 and c.status_active=1";
	}
	else if($cbo_sock_for==2)
	{
		$trans_order_cond=" and c.status_active=3";
	}
	else if($cbo_sock_for==3)
	{
		$trans_order_cond=" and c.shiping_status=3 and c.status_active=1";
	}
	else
	{
		$trans_order_cond="";
	}
	if(str_replace("'","",$cbo_buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond_trans="";
		}
		else
		{
			$buyer_id_cond_trans="";
		}
	}
	else
	{
		$buyer_id_cond_trans=" and d.buyer_name=$cbo_buyer_id";
	}
	
	if( str_replace("'","",$txt_date_from_trans)=="") $trans_in_date=""; else $trans_in_date= " and f.transaction_date <=".$txt_date_from_trans."";
	if($db_type==0) $select_up_date=" DATE_FORMAT(c.update_date, '%d %M %Y') as calcel_date "; else $select_up_date=" to_char(c.update_date, 'DD-MM-YYYY') as calcel_date ";

	// ==== Transfer In and Out sql

	$trans_in_out_sql="SELECT e.entry_form, cast(g.booking_no as varchar2(100)) as program_no, d.company_name, e.prod_id, e.po_breakdown_id, e.trans_type, (a.qnty) AS rcv_qty, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, (b.id) AS issue_roll, d.job_no, d.buyer_name, d.style_ref_no, c.po_number, c.pub_shipment_date, c.status_active, $select_up_date, b.yarn_lot, b.y_count, b.brand_id, b.stitch_length, b.gsm, b.dia_width, b.machine_no_id, a.barcode_no 
	FROM wo_po_break_down c 
	INNER JOIN wo_po_details_master d ON c.job_id = d.id
	INNER JOIN order_wise_pro_details e ON c.id = e.po_breakdown_id 
	INNER JOIN inv_transaction f ON e.trans_id = f.id 
	INNER JOIN inv_item_transfer_dtls b ON e.dtls_id = b.id 
	INNER JOIN pro_roll_details a ON a.dtls_id=b.id AND a.status_active = 1 
	LEFT JOIN pro_roll_details g ON g.barcode_no=a.barcode_no and g.entry_form=2 and g.receive_basis=2 AND a.status_active = 1
	WHERE c.t_year>=2020 and d.status_active = 1 AND d.is_deleted = 0 AND e.status_active = 1 AND e.is_deleted = 0 AND e.entry_form IN(82,83,110,183) AND a.entry_form IN(82,83,110,183) AND e.trans_type IN(5,6) AND f.status_active = 1 AND f.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0  $buyer_id_cond_trans $year_cond_trans $job_no_cond_trans $order_id_cond_trans $trans_order_cond $po_date_trans_cond $trans_in_date $search_trans_cond $store_trans_cond $po_id_cond_c"; 
	//echo $trans_in_out_sql; die;

	$trans_in_outResult = sql_select($trans_in_out_sql);
	foreach ($trans_in_outResult as $key => $val) 
	{
		if ($val[csf('trans_type')]==5) 
		{
			$barcode_no_arr[$val[csf('barcode_no')]]=$val[csf('barcode_no')];
		}
	}
	// echo "<pre>";print_r($barcode_no_arr);die;

	if(!empty($barcode_no_arr))
	{
		foreach($barcode_no_arr as $barcode_no)
		{
			if( $barcode_no_check[$barcode_no] =="" )
	        {
	            $barcode_no_check[$barcode_no]=$barcode_no;
	            $barcodeno = $barcode_no;
	            // echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)";
	            execute_query("insert into tmp_barcode_no (userid, barcode_no,entry_form) values ($user_id,$barcodeno,67)");
	        }			
		}
		oci_commit($con);

		$production_sql=sql_select("SELECT b.booking_id, c.color_id, c.color_range_id, d.barcode_no
			from inv_receive_master b, pro_grey_prod_entry_dtls c, pro_roll_details d, tmp_barcode_no g
			where b.id=c.mst_id and c.id=d.dtls_id and b.id=d.mst_id and d.barcode_no=g.barcode_no and g.entry_form=67 and g.userid=$user_id and b.receive_basis=2 and b.entry_form=2"); //and d.roll_id=0
		$production_data=array();	
		foreach($production_sql as $row)
		{
			$production_data[$row[csf("barcode_no")]]["booking_id"]=$row[csf("booking_id")];
			$production_data[$row[csf("barcode_no")]]["color_id"]=$row[csf("color_id")];
			$production_data[$row[csf("barcode_no")]]["color_range_id"]=$row[csf("color_range_id")];
		}
	}

	foreach($trans_in_outResult as $row)
	{
		$compId = $row[csf('company_name')];
		$productId = $row[csf('prod_id')];
		$orderId = $row[csf('po_breakdown_id')];
		$storeId = $row[csf('store_id')]*1;
		$floorId = $row[csf('floor_id')]*1;
		$roomId = $row[csf('room')]*1;
		$rackId = $row[csf('rack')]*1;
		$selfId = $row[csf('self')]*1;
		$binId = $row[csf('bin_box')]*1;

		$color_ids=$production_data[$row[csf("barcode_no")]]["color_id"];
		$color_range_ids=$production_data[$row[csf("barcode_no")]]["color_range_id"];

		$str_ref=$orderId."*".$productId."*".$storeId."*".$floorId."*".$roomId."*".$rackId."*".$selfId."*".$binId."*".$row[csf("yarn_lot")];
		if($row[csf('trans_type')] == 5)
		{
			$poArr[$orderId] = $orderId;
			$prodArray[$row[csf("prod_id")]] = $row[csf("prod_id")];

			if($row[csf('entry_form')] ==13)
			{
				$data_arr[$row[csf("job_no")]][$str_ref]['rec_roll'] += $row[csf('issue_roll')];
			}
			else
			{
				$data_arr[$row[csf("job_no")]][$str_ref]['rec_roll']++;
			}

			$data_arr[$row[csf("job_no")]][$str_ref]['transfer_in_qnty'] += $row[csf('rcv_qty')];

			$data_arr[$row[csf("job_no")]][$str_ref]['pub_shipment_date']=$row[csf("pub_shipment_date")];
			$data_arr[$row[csf("job_no")]][$str_ref]['status_active']=$row[csf("status_active")];
			$data_arr[$row[csf("job_no")]][$str_ref]['calcel_date']=$row[csf("calcel_date")];
			$data_arr[$row[csf("job_no")]][$str_ref]['job_no']=$row[csf("job_no")];
			$data_arr[$row[csf("job_no")]][$str_ref]['po_number']=$row[csf("po_number")];
			$data_arr[$row[csf("job_no")]][$str_ref]['file_no']=$row[csf("file_no")];
			$data_arr[$row[csf("job_no")]][$str_ref]['grouping']=$row[csf("grouping")];
			$data_arr[$row[csf("job_no")]][$str_ref]['buyer_name']=$row[csf("buyer_name")];
			$data_arr[$row[csf("job_no")]][$str_ref]['style_ref_no']=$row[csf("style_ref_no")];

			$data_arr[$row[csf("job_no")]][$str_ref]['stitch_length'].=$row[csf("stitch_length")].',';
			$data_arr[$row[csf("job_no")]][$str_ref]['brand_id'].=$row[csf("brand_id")].',';
			$data_arr[$row[csf("job_no")]][$str_ref]['color_id'].=$color_ids.',';
			$data_arr[$row[csf("job_no")]][$str_ref]['color_range_id'].=$color_range_ids.',';
			// $data_arr[$row[csf("job_no")]][$str_ref]['yarn_lot'].=$row[csf("yarn_lot")].',';
			$data_arr[$row[csf("job_no")]][$str_ref]['yarn_count'].=$row[csf("y_count")].',';
			$data_arr[$row[csf("job_no")]][$str_ref]['machine_no_id']=$row[csf("machine_no_id")];
			$data_arr[$row[csf("job_no")]][$str_ref]['booking_no'].=$row[csf("booking_no")].',';
			$data_arr[$row[csf("job_no")]][$str_ref]['program_no'].=$row[csf("program_no")].',';
			// $data_arr[$row[csf("job_no")]][$str_ref]['barcode_no'] .=$row[csf("barcode_no")].",";
		}
		if($row[csf('trans_type')] == 6)
		{
			$transf_out_data_arr[$row[csf("job_no")]][$str_ref]['rollIssueQty']+=$row[csf("issue_roll")];
			$transf_out_data_arr[$row[csf("job_no")]][$str_ref]['transferOut']+=$row[csf("rcv_qty")];
		}
	}
	unset($trans_in_outResult);
	// echo "<pre>";print_r($data_arr);die;


	// =============== Transfer In End =============================

	if(empty($data_arr))
	{
		echo "Data Not Found";
		die;
	}

	// ============== po id insert into GBL_TEMP_ENGINE Start ============

	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 67, 1,$poArr, $empty_arr); // PO Id temp entry
	// ============== po id insert into tmp_po_id End =============

	// =============== Issue query Start ===========================
	$sql_issue="SELECT p.job_no_mst, a.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.store_name, b.floor_id, b.room, b.rack, b.self, b.bin_box, sum(a.qnty ) as issue_qnty, count(a.id) as issue_roll 
	from wo_po_break_down p, pro_roll_details a, inv_grey_fabric_issue_dtls b, GBL_TEMP_ENGINE g
	where p.id=a.po_breakdown_id and a.dtls_id=b.id and p.id=g.ref_val and g.entry_form=67 and g.ref_from=1 and g.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(61)
	group by p.job_no_mst, a.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.store_name, b.floor_id, b.room, b.rack, b.self, b.bin_box";
	// echo $sql_issue;die;
	$result_sql_issue=sql_select( $sql_issue );
	$issue_array=array();
	foreach ($result_sql_issue as $row)
	{
		if ($row[csf("store_name")]=="") $row[csf("store_name")]=0;
		if ($row[csf("floor_id")]=="") $row[csf("floor_id")]=0;
		if ($row[csf("room")]=="") $row[csf("room")]=0;
		if ($row[csf("rack")]=="") $row[csf("rack")]=0;
		if ($row[csf('self')]=="") $row[csf('self')]=0;
		if ($row[csf("bin_box")]=="") $row[csf("bin_box")]=0;
		
		$str_ref=$row[csf("po_breakdown_id")]."*".$row[csf("prod_id")]."*".$row[csf("store_name")]."*".$row[csf("floor_id")]."*".$row[csf("room")]."*".$row[csf("rack")]."*".$row[csf("self")]."*".$row[csf("bin_box")]."*".$row[csf("yarn_lot")];
		$issue_array[$row[csf("job_no_mst")]][$str_ref]['issue_qnty']+=$row[csf("issue_qnty")];
		$issue_array[$row[csf("job_no_mst")]][$str_ref]['issue_roll']+=$row[csf("issue_roll")];
	}	
	// echo "<pre>";print_r($issue_array);
	// =============== Issue query End =============================

	// ============== Receive return and Issue return Start ========

	$sql_retn="SELECT b.po_breakdown_id, a.prod_id, a.batch_lot, a.store_id, a.floor_id, a.room, a.rack, a.self, a.bin_box, c.po_number, c.pub_shipment_date, to_char(c.update_date, 'DD-MM-YYYY') as calcel_date, g.buyer_name, g.style_ref_no, sum(b.quantity) as iss_rtn_qty, c.job_no_mst, f.yarn_lot as lot, f.yarn_count, f.brand_id
	from inv_transaction a, PRO_GREY_PROD_ENTRY_DTLS f, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master g, GBL_TEMP_ENGINE d, product_details_master e 
	where c.t_year>=2020 and a.id=b.trans_id and a.id=f.TRANS_ID and f.id=b.dtls_id and A.PROD_ID=e.id and b.po_breakdown_id=c.id and c.id=d.ref_val and d.user_id=$user_id and d.entry_form=67 and d.ref_from=1 and a.status_active=1 and a.is_deleted=0 and c.job_id=g.id and g.status_active=1 and g.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=13 and b.entry_form in(84) and a.company_id=$cbo_company_id and a.transaction_type in(4) and b.trans_type in (4) 
	group by b.po_breakdown_id, a.prod_id, a.batch_lot, a.store_id, a.floor_id, a.room, a.rack, a.self, a.bin_box, c.job_no_mst, c.po_number, c.pub_shipment_date, c.update_date, g.buyer_name, g.style_ref_no, f.yarn_lot, f.yarn_count, f.brand_id";
	// echo $sql_retn;die;
	$data_retn_array=sql_select($sql_retn);
	$$retn_arr=array();
	//buyer_name=wo_po_details_master
	//style_ref_no=wo_po_details_master
	foreach($data_retn_array as $row )
	{
		if ($row[csf("store_id")]=="") $row[csf("store_id")]=0;
		if ($row[csf("floor_id")]=="") $row[csf("floor_id")]=0;
		if ($row[csf("room")]=="") $row[csf("room")]=0;
		if ($row[csf("rack")]=="") $row[csf("rack")]=0;
		if ($row[csf('self')]=="") $row[csf('self')]=0;
		if ($row[csf("bin_box")]=="") $row[csf("bin_box")]=0;
		
		$str_ref=$row[csf("po_breakdown_id")]."*".$row[csf("prod_id")]."*".$row[csf("store_id")]."*".$row[csf("floor_id")]."*".$row[csf("room")]."*".$row[csf("rack")]."*".$row[csf("self")]."*".$row[csf("bin_box")]."*".$row[csf("lot")];
		$retn_arr[$row[csf("job_no_mst")]][$str_ref]['iss_rtn_qty']=$row[csf("iss_rtn_qty")];
		$retn_arr[$row[csf("job_no_mst")]][$str_ref]['rcv_rtn_qty']=$row[csf("rcv_rtn_qty")];

		if($row[csf("iss_rtn_qty")])
		{
			$prodArray[$row[csf("prod_id")]] = $row[csf("prod_id")];
			$data_arr[$row[csf("job_no_mst")]][$str_ref]['job_no']=$row[csf("job_no_mst")];
			$data_arr[$row[csf("job_no_mst")]][$str_ref]['pub_shipment_date']=$row[csf("pub_shipment_date")];
			$data_arr[$row[csf("job_no_mst")]][$str_ref]['status_active']=$row[csf("status_active")];
			$data_arr[$row[csf("job_no_mst")]][$str_ref]['calcel_date']=$row[csf("calcel_date")];
			$data_arr[$row[csf("job_no_mst")]][$str_ref]['po_number']=$row[csf("po_number")];
			$data_arr[$row[csf("job_no_mst")]][$str_ref]['file_no']=$row[csf("file_no")];
			$data_arr[$row[csf("job_no_mst")]][$str_ref]['grouping']=$row[csf("grouping")];
			$data_arr[$row[csf("job_no_mst")]][$str_ref]['buyer_name']=$row[csf("buyer_name")];
			$data_arr[$row[csf("job_no_mst")]][$str_ref]['style_ref_no']=$row[csf("style_ref_no")];
			$data_arr[$row[csf("job_no_mst")]][$str_ref]['brand_id'].=$row[csf("brand_id")].',';
			$data_arr[$row[csf("job_no_mst")]][$str_ref]['yarn_count'].=$row[csf("yarn_count")].',';
		}
	}

	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 67, 2,$prodArray, $empty_arr); // all Prod Id temp entry

	// ============= Receive return and Issue return End ===========

	// ============= Booking Req Qty Start =========================
	$sql="SELECT a.buyer_id,b.job_no, b.po_break_down_id as po_id,b.construction,b.fabric_color_id, sum(b.grey_fab_qnty) as grey_req_qnty, a.booking_no 
	from wo_booking_mst a, wo_booking_dtls b, GBL_TEMP_ENGINE g
	where a.booking_no=b.booking_no and b.po_break_down_id=g.ref_val and g.entry_form=67 and g.ref_from=1 and g.user_id=$user_id and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1
	group by a.buyer_id,b.job_no,b.po_break_down_id,b.construction,b.fabric_color_id,a.booking_no";
	// echo $sql;
	$sql_result=sql_select($sql);
	$po_ids='';

	$booking_array=array();
	foreach( $sql_result as $row )
	{
		$booking_array[$row[csf('po_id')]]['booking_no'].=$row[csf('booking_no')].',';
		// echo $key=$row[csf('buyer_id')].'='.$row[csf('job_no')].'='.$row[csf('po_id')].'='.$row[csf('construction')].'='.$color_arr[$row[csf('fabric_color_id')]].'<br>';
		$key=$row[csf('buyer_id')].$row[csf('job_no')].$row[csf('po_id')].$row[csf('construction')].$color_arr[$row[csf('fabric_color_id')]];
		$grey_qnty_array[$key]+=$row[csf('grey_req_qnty')];
	}
	unset($sql_result);
	// ============= Booking Req Qty End ========================

	// ============== For Program Start =========================
	$sqlYarn = "SELECT e.prod_id, e.entry_form, g.febric_description_id, g.gsm, g.width, g.machine_dia, g.stitch_length, g.color_id, g.color_range_id, g.yarn_count, g.brand_id, g.yarn_lot, d.booking_id 
	from order_wise_pro_details e inner join pro_grey_prod_entry_dtls g on e.dtls_id = g.id, GBL_TEMP_ENGINE b
	where e.entry_form in(2,22,58,84) and e.prod_id=b.ref_val and b.entry_form=67 and b.ref_from=2 and b.user_id=$user_id ";
	// echo $sqlYarn;die;
	$sqlYarnRslt = sql_select($sqlYarn);
	$yarnInfoArr = array();
	foreach($sqlYarnRslt as $row)
	{
		$prodId = $row[csf('prod_id')];
		// echo $prodId.'===<br>';
		$yarnInfoArr[$prodId]['construction'] = $constructtion_arr[$row[csf('febric_description_id')]];
		$yarnInfoArr[$prodId]['composition'] = $composition_arr[$row[csf('febric_description_id')]];
		$yarnInfoArr[$prodId]['gsm'] = $row[csf('gsm')];
		$yarnInfoArr[$prodId]['width'] = $row[csf('width')];
		$yarnInfoArr[$prodId]['machine_dia'] = $row[csf('machine_dia')];
		$yarnInfoArr[$prodId]['stitch_length'] = $row[csf('stitch_length')];
		if ($row[csf('entry_form')]==2) 
		{
			$yarnInfoArr[$prodId]['program_no'] = $row[csf('booking_id')];
		}
		
		$expColor = explode(',', $row[csf('color_id')]);
		$clrArr = array();
		foreach($expColor as $clr)
		{
			$clrArr[$clr] = $color_arr[$clr];
		}
		
		$yarnInfoArr[$prodId]['color_id'] = implode(',', $clrArr);
		$yarnInfoArr[$prodId]['color_range_id'] = $row[csf('color_range_id')];
		$yarnInfoArr[$prodId]['yarn_count'] = $row[csf('yarn_count')];
		$yarnInfoArr[$prodId]['brand_id'] = $row[csf('brand_id')];
		$yarnInfoArr[$prodId]['yarn_lot'] = $row[csf('yarn_lot')];
	}
	unset($sqlYarnRslt);
	// =================== For Program End ======================

	// =================== DOH Start ======================
	$transaction_date_array=array();
	$sql_date="SELECT a.prod_id, min(a.transaction_date) as min_date, max(a.transaction_date) as max_date from inv_transaction a, GBL_TEMP_ENGINE b where a.prod_id=b.ref_val and b.entry_form=67 and b.ref_from=2 and b.user_id=$user_id and a.status_active=1 and a.is_deleted=0 group by a.prod_id";
	$sql_date_result=sql_select($sql_date);
	foreach( $sql_date_result as $row )
	{
		$transaction_date_array[$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
		$transaction_date_array[$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
	}
	// =================== DOH End ======================

	$floor_room_rack_array=return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");
	$store_array=return_library_array("select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=13 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name", "id", "store_name");
	
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (67)");
	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=67");
	oci_commit($con);
	disconnect($con);
	
	ob_start();
	?>
	<fieldset>
		<table cellpadding="0" cellspacing="0" width="2770">
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="36" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="36" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="36" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from_trans)) ;?></strong></td>
			</tr>
		</table>
		<table width="3140" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
				<tr>
					<th width="30" rowspan="2">SL</th>
					<th colspan="16">Fabric Details</th>
					<th colspan="3">Used Yarn Details</th>
					<th width="120" rowspan="2">Receive Basis (BK/PLN/ PI/GPE)</th>
					<th width="80" rowspan="2">Req. Qty.</th>
					<th colspan="5">Receive Details</th>
					<th colspan="5">Issue Details</th>
					<th colspan="10">Stock Details</th>
				</tr>
				<tr>
					<th width="80">Job No.</th>
					<th width="80">Buyer</th>
					<th width="80">Order No.</th>
                    <th width="70">Ship Date</th>
                    <th width="70">Cancel Date</th>
					<th width="80">Style Ref</th>
					<th width="100">Program No.</th>
					<th width="100">Booking No.</th>
					<th width="100">Construction</th>
					<th width="100">Composition</th>
					<th width="60">GSM</th>
					<th width="60">F/Dia</th>
					<th width="60">M/Dia</th>
					<th width="60">Stich Length</th> 
					<th width="80">Dyeing Color</th>
					<th width="80">Color Range</th>
					<th width="60">Y. Count</th>
					<th width="80">Y. Brand</th>
					<th width="80">Y. Lot</th>
					<th width="80">Recv. Qty.</th>
					<th width="80">Issue Ret. Qty.</th>
					<th width="80">Transf. In Qty.</th>
					<th width="80">Total Recv.</th>
					<th width="60">Recv. Roll</th>
					<th width="80">Issue Qty.</th>
					<th width="80">Recv. Ret. Qty.</th>
					<th width="80">Transf. Out Qty.</th>
					<th width="80">Total Issue</th>
					<th width="60">Issue Roll</th>
					<th width="80">Stock Qty.</th>
					<th width="60">Roll Qty.</th>
					<th width="50">Store</th>
					<th width="50">Floor</th>
					<th width="50">Room</th>
					<th width="50">Rack</th>
					<th width="50">Shelf</th>
					<th width="50">DOH</th>
					<th width="50">Recv. Balance</th>
					<th>Issue Balance</th>
				</tr>
			</thead>
		</table>
		<div style="width:3160px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="3140" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
				<?

				$i=1;$ttt=1;
				// echo "<pre>";print_r($data_arr);
				foreach ($data_arr as $job => $str_arr)
				{
					foreach ($str_arr as $str_ref_arr => $row) 
					{								
						$str_data=explode("*", $str_ref_arr);
						// echo "<pre>"; print_r($str_data);
						$order_id=$str_data[0];
						$prod_id=$str_data[1];
						$store=$str_data[2];
						$floor=$str_data[3];
						$room=$str_data[4];
						$rack=$str_data[5];
						$self=$str_data[6];
						$bin=$str_data[7];	
						$yarn_lot=$str_data[8];	
						/*if ($rowspan_check_arr[$job][$order_id][$prod_id]=="") 
						{
							$j=1;$k=1;
							$rowspan_check_arr[$job][$order_id][$prod_id]=$prod_id;
						}

						$rowspan=$rowspan_count_arr[$job][$order_id][$prod_id];*/	
						 
						// $yarn_lot =implode(",", array_unique(explode(",", chop($row["yarn_lot"],",")))) ;
						$yarn_counts_arr = array_unique(array_filter(explode(",", $row['yarn_count'])));
						$count_val='';
						foreach ($yarn_counts_arr as $val)
						{
							if($val>0){ if($count_val=='') $count_val=$count_arr[$val]; else $count_val.=",".$count_arr[$val]; }
						}
						
						$rece_data_arr[$order_id][$prod_id]['recvQnty']=$row['quantity'];

						//$color_name=$yarnInfoArr[$prod_id]['color_id'];
						$color_id=array_unique(explode(',',$row['color_id']));
						$color_name='';$colorId_string='';
						foreach ($color_id as $val)
						{
							if($val>0)
							{ 
								if($color_name=='')
								{
									$color_name=$color_arr[$val];
									$colorId=$val;
								}
								else 
								{
									$color_name.=",".$color_arr[$val];
									 //$colorId.=",".$val;
								}
							}
						}
						
						$color_range_id=array_unique(explode(',',chop($row['color_range_id'],','))); 
						// $machine_dia=$yarnInfoArr[$prod_id]['machine_dia'];
						//$color_range_id = explode(',',$yarnInfoArr[$prod_id]['color_range_id']);
						$color_range_name='';
						foreach ($color_range_id as $val)
						{
							if($val>0)
							{ 
								if($color_range_name=='')
								{
									$color_range_name=$color_range[$val];
								}
								else 
								{
									$color_range_name.=",".$color_range[$val];
								}
							}
						}
						
						$brand_id=array_unique(explode(',',$row['brand_id'])); $brand_name="";
						foreach ($brand_id as $val)
						{
							if($val>0){ if($brand_name=='') $brand_name=$brand_arr[$val]; else $brand_name.=",".$brand_arr[$val]; }
						}
						//$program_no = $yarnInfoArr[$prod_id]['program_no'];

						$program_no="";
						$program_no = implode(",",array_unique(explode(",",chop($row['program_no'],','))));
						
						//--------------------------------------------------------------------------start

						$job_no=$row['job_no'];	
						$style_ref_no=$row['style_ref_no'];	
						$buyer_name=$buyer_arr[$row['buyer_name']];	
						$buyer_id=$row['buyer_name'];	
						$po_number=$row['po_number'];	
						$break_down_id=$order_id;	
						$construction_data=$constructionArr[$product_array[$prod_id]['detarmination_id']];	
						$dyeing_color_string=$color_name;
						$colorId_string=$color_name;

						// echo $buyer_id_prv.'*'.$job_no_prv.'*'.$break_down_id_prv.'*'.$construction_data_prv.'*'.$colorId_string_prv.'<br>';
						$groupKey = $buyer_id.$job_no.$break_down_id.$construction_data.$colorId_string;
						$groupKeyReq = $buyer_id_prv.$job_no_prv.$break_down_id_prv.$construction_data_prv.$colorId_string_prv;
						$grey_qnty=$grey_qnty_array[$groupKeyReq];	// booking req qty

						//-----------------------------------------------------------------------------end

						// $trnsfer_in_qty=$transf_out_data_arr[$job][$str_ref_arr]['transferIn'];
						$trnsfer_in_qty=$row["transfer_in_qnty"];
						$trnsfer_out_qty=$transf_out_data_arr[$job][$str_ref_arr]['transferOut'];

						$recv_retn=$retn_arr[$job][$str_ref_arr]['rcv_rtn_qty'];
						$issue_retn=$retn_arr[$job][$str_ref_arr]['iss_rtn_qty'];
						
						// echo $str_ref_arr.'<br>';
						$issue_qty=$issue_array[$job][$str_ref_arr]['issue_qnty'];
						$issue_roll=$issue_array[$job][$str_ref_arr]['issue_roll']+$transf_out_data_arr[$job][$str_ref_arr]['rollIssueQty'];

						$rec_bal=$row['quantity']+$trnsfer_in_qty+$issue_retn;

						//echo $issue_qty.'+'.$trnsfer_out_qty.'+'.$recv_retn;
						$issue_bal=$issue_qty+$trnsfer_out_qty+$recv_retn;
						$stock=$rec_bal-$issue_bal; 

						

						if($cbo_value_with==1 ) 
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if(!in_array($groupKey,$con_arr))
							{
								if($ttt!=1)
								{
									$total_grey_qnty+=$grey_qnty;
									?>
									<tr class="tbl_bottom">
										<td width="30" title="Sub Total Grouping=[buyer_id][job_no][break_down_id][construction_data][colorId_string]"></td>
										<td width="80" style="color:#E2E2E2;" align="center"><p><? //echo $job_no_prv;?></p></td>
										<td width="80" style="color:#E2E2E2;" align="center"><p><? //echo $buyer_name_prv;?></p></td>
										<td width="80" style="color:#E2E2E2;" align="center"><p><? //echo $po_number_prv;?></p></td>
	                                    <td width="70" align="center"><p><? //echo change_date_format($row[csf("pub_shipment_date")]); ?></p></td>
	                                    <td width="70" align="center"><p><? //if($row[csf("status_active")]==3) echo change_date_format($row[csf("calcel_date")]); ?></p></td>
										<td width="80" style="color:#E2E2E2;" align="center"><p><? //echo $style_ref_no_prv;?></p></td>
										<td width="100"></td>
										<td width="100"><p><? //echo chop($booking_array[$break_down_id]['booking_no'],","); ?>&nbsp;</p></td>
										<td width="100" style="color:#E2E2E2;"><p><? //echo $construction_data_prv;?></p></td>
										<td width="100" style="color:#E2E2E2;"><p><? //echo $composition_string_prv;?></p></td>
										<td width="60" style="color:#E2E2E2;"><p><? //echo $gsm_string;?></p></td>
										<td width="60" style="color:#E2E2E2;"><p><? //echo $fdia_string;?></p></td>
										<td width="60"></td>
										<td width="60"></td>
										<td width="80" style="color:#E2E2E2;"><p><? //echo $dyeing_color_string_prv;?></p></td>
										<td width="80"></td>
										<td width="60"></td>
										<td width="80"></td>
										<td width="80"></td>
										<td width="120"></td>
										<td width="80" align="right" title="Req qty"><p><b><? echo number_format($grey_qnty,2,'.','');?></b></p></td>
										<td width="80" align="right"><? echo number_format($tot_rec_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80"  align="right"><? echo number_format($tot_iss_retn_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($tot_transfer_in_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($tot_rec_bal,2,'.',''); ?>&nbsp;</td>
										<td width="60" align="right"><? echo $tot_rec_roll; ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($tot_issue_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($tot_recv_retn_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($tot_transfer_out_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($tot_issue_bal,2,'.',''); ?>&nbsp;</td>
										<td width="60" align="right"><? echo $tot_issue_roll; ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($tot_stock,2,'.',''); ?>&nbsp;</td>
										<td width="60" align="right"><? //echo $tot_stock_roll_qty; ?>&nbsp;</td>
										<td width="50" align="center">&nbsp;</td>
										<td width="50" align="center">&nbsp;</td>
										<td width="50" align="center">&nbsp;</td>
										<td width="50" align="right">&nbsp;</td>
										<td width="50" align="right">&nbsp;</td>
										<td width="50" align="right">&nbsp;</td>
										<td width="50" align="right"><? echo number_format($grey_qnty-$tot_rec_bal,2,'.',''); //?></td>
										<td width="" align="right"><? echo number_format($grey_qnty-$tot_issue_bal,2,'.','');// ?></td>
									</tr>
									<?
									unset($colorData);
									unset($colorId_string);
									unset($grey_qnty);
									unset($dyeing_color_string);
									unset($colorId_string);
									unset($break_down_id);
									unset($tot_req_qty);
									unset($tot_rec_qty);
									unset($tot_transfer_in_qty);
									unset($tot_rec_bal);
									unset($tot_rec_roll);
									unset($tot_issue_qty);
									unset($tot_transfer_out_qty);
									unset($tot_issue_bal);
									unset($tot_issue_roll);
									unset($tot_stock);
									unset($tot_stock_roll_qty);
									unset($tot_iss_retn_qty);
									unset($tot_recv_retn_qty);
								}
								$ttt++;
							}
							$con_arr[]=$groupKey;
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30" title="Receive A"><? echo $i; ?></td>
								<td width="80"><p><? echo $job_no;?></p></td>
								<td width="80"><p><? echo $buyer_name; ?></p></td>
								<td width="80" title="<?=$order_id;?>"><p><? echo $po_number; ?></p></td>
	                            <td width="70" align="center"><p><? echo change_date_format($row["pub_shipment_date"]); ?></p></td>
	                            <td width="70" align="center"><p><? if($row["status_active"]==3) echo change_date_format($row["calcel_date"]); ?></p></td>
								<td width="80"><p><? echo $style_ref_no; ?></p></td>
								<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
								<td width="100"><p><? echo implode(",",array_unique(explode(",",chop($booking_array[$order_id]['booking_no'],",")))); ?>&nbsp;</p></td>					
								<td width="100" title="Prod ID: <? echo $prod_id;?>"><p><? echo $constructionArr[$product_array[$prod_id]['detarmination_id']]; ?></p></td>
								<td width="100"><p><? echo $copmpositionArr[$product_array[$prod_id]['detarmination_id']]; ?></p></td>
								<td width="60"><p><? echo $product_array[$prod_id]['gsm']; ?></p></td>
								<td width="60"><p><? echo $product_array[$prod_id]['dia_width']; ?></p></td>
								<td width="60"><p><? echo $machine_arr[$row['machine_no_id']]; ?></p></td>
								<td width="60"><p><? echo implode(",",array_unique(explode(",",chop($row['stitch_length'],",")))); ?></p></td>
								<td width="80"><p><? echo $color_name; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $color_range_name; ?>&nbsp;</p></td>
								<td width="60"><p><? echo $count_val; ?>&nbsp;</p></td> 
								<td width="80"><p><? echo $brand_name; ?>&nbsp;</p></td>
								<td width="80" title="<?=$yarn_lot;?>"><p><? echo $yarn_lot; ?>&nbsp;</p></td>
								<td width="120"><p><? echo implode(",",array_unique(explode(",",chop($row['booking_no'],",")))); ?>&nbsp;</p></td>
								<td width="80" align="right"><p>&nbsp;</p><? //echo number_format($grey_qnty,2); $total_grey_qnty+=$grey_qnty;?></td>
								<td width="80" align="right"><p>
									<?  $tot_rec_qty+=$row['quantity']; ?>
									<a href='#report_details' onClick="openmypage_delivery_all('<? echo $order_id; ?>','<? echo $program_no; ?>','<? echo $prod_id; ?>','<? echo $row['entry_form']; ?>','<? echo ""; ?>','<? echo ""; ?>','950px','report_all_popup',1);"><? echo number_format($row['quantity'],2); ?></a>&nbsp;</p>
								</td>
								<td width="80" align="right" title="<?php echo $order_id."*".$prod_id."*".$yarn_count."*".$row['batch_lot']."*". $row['rack']."*". $row['self']; ?>"><p>
									<a href='#report_details' onClick="openmypage_delivery_all('<? echo $order_id; ?>','<? echo $program_no; ?>','<? echo $prod_id; ?>','<? echo $row['entry_form']; ?>','<? echo $po_id."**".$value[1]."**".$value[2]."**".$value[3]."**".$store."**".$floor."**".$room."**".$rack."**".$self."**".$yarn_lot; ?>','<? echo ""; ?>','450px','report_all_popup',2);"><? echo number_format($issue_retn,2); $tot_iss_retn_qty+=$issue_retn; ?></a>&nbsp;</p>
								</td>
								<td width="80" align="right"><p>
									<a href='#report_details' onClick="openmypage_delivery_all('<? echo $order_id; ?>','<? echo $program_no; ?>','<? echo $prod_id; ?>','<? echo $row['entry_form']; ?>','<? echo $po_id."**".$value[1]."**".$value[2]."**".$value[3]."**".$store."**".$floor."**".$room."**".$rack."**".$self; ?>','<? echo ""; ?>','650px','report_all_popup',3);"><? echo number_format($row['transfer_in_qnty'],2); $tot_transfer_in_qty+=$row['transfer_in_qnty']; ?></a>&nbsp;</p> 
								</td>
								<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal; ?>&nbsp;</p></td>
								<td width="60" align="right"><p><? $rec_roll=$row['rec_roll']+$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?>&nbsp;</p></td> 							
								<td width="80" align="right" title="<? echo $str_ref_arr; ?>"><p>
									<a href='#report_details' onClick="openmypage_delivery_all('<? echo $order_id; ?>','<? echo $program_no; ?>','<? echo $prod_id; ?>','<? echo $row['entry_form']; ?>','<? echo $row['stitch_length']."**".$yarn_lot."**".$value[2]."**".$value[3]."**".$store."**".$floor."**".$room."**".$rack."**".$self; ?>','<? echo ""; ?>','850px','report_all_popup',4);">	<? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></a>&nbsp;</p>
								</td>
								<td width="80" align="right"><p><? echo number_format($recv_retn,2); $tot_recv_retn_qty +=$recv_retn; ?>&nbsp;</p></td>
								<td width="80" align="right"><p>
									<a href='#report_details' onClick="openmypage_delivery_all('<? echo $order_id; ?>','<? echo $program_no; ?>','<? echo $prod_id; ?>','<? echo $row['entry_form']; ?>','<? echo $po_id."**".$value[1]."**".$value[2]."**".$value[3]."**".$store."**".$floor."**".$room."**".$rack."**".$self; ?>','<? echo ""; ?>','650px','report_all_popup',5);"><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty; ?>
									</a>&nbsp;</p>
								</td>
								<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
								<td width="60" align="right"><p><? $iss_roll=$issue_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</p></td>						
								<td width="80" align="right"><p>
									<a href='#report_details' onClick="openmypage_delivery_all('<? echo $order_id; ?>','<? echo $program_no; ?>','<? echo $prod_id; ?>','<? echo $row['entry_form']; ?>','<? echo $row['stitch_length']."**".$yarn_lot."**".$value[2]."**".$value[3]."**".$store."**".$floor."**".$room."**".$rack."**".$self; ?>','<? echo ""; ?>','650px','report_all_popup',6);"><? echo number_format($stock,2); $tot_stock+=$stock; ?></a>&nbsp;</p>
								</td>
								<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty;?>&nbsp;</p></td>
								<td width="50" align="center" title="<?=$store;?>"><p><? echo $store_array[$store]; ?>&nbsp;</p></td>	
								<td width="50" align="center" title="<?=$floor;?>"><p><? echo $floor_room_rack_array[$floor]; ?>&nbsp;</p></td>	
								<td width="50" align="center" title="<?=$room;?>"><p><? echo $floor_room_rack_array[$room]; ?>&nbsp;</p></td>		
								<td width="50" align="center" title="<?=$rack;?>"><p><? echo $floor_room_rack_array[$rack]; ?>&nbsp;</p></td>
								<td width="50" align="center" title="<?=$self;?>"><p><? echo $floor_room_rack_array[$self]; ?>&nbsp;</p></td>
								<?
								$daysOnHand = datediff("d",change_date_format($transaction_date_array[$prod_id]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));?>				
								<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
								<td width="50" align="right"><p><? //echo $grey_qnty-$rec_bal; ?></p></td>
								<td width="" align="right"><p><? //echo $grey_qnty-$issue_bal; ?></p></td>
							</tr>
							<?
							$i++;
							$grand_tot_rec_qty+=$row['quantity'];
							$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
							$grand_tot_rec_bal+=$rec_bal;
							$grand_tot_rec_roll+=$rec_roll;
							$grand_tot_issue_qty+=$issue_qty;
							$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
							$grand_tot_issue_bal+=$issue_bal;
							$grand_tot_issue_roll+=$iss_roll;
							$grand_tot_stock+=$stock;
							$grand_tot_roll_qty+=$stock_roll_qty;
							$grand_tot_issue_retn_qty+=$issue_retn;
							$grand_tot_recv_retn_qty+=$recv_retn;
							
							$colorId_string_prv=$color_name;
							$dyeing_color_string_prv=$color_name;
							$job_no_prv=$row['job_no'];	
							$style_ref_no_prv=$row['style_ref_no'];	
							$buyer_name_prv=$buyer_arr[$row['buyer_name']];	
							$buyer_id_prv=$row['buyer_name'];	
							$po_number_prv=$row['po_number'];	
							$break_down_id_prv=$order_id;	
							$construction_data_prv=$constructionArr[$product_array[$prod_id]['detarmination_id']];
						}
						else if($cbo_value_with==2 && number_format($stock,2,".","")>0) 
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if(!in_array($groupKey,$con_arr))
							{
								if($ttt!=1)
								{
									$total_grey_qnty+=$grey_qnty;
									?>
									<tr class="tbl_bottom">
										<td width="30" title="Sub Total Grouping=[buyer_id][job_no][break_down_id][construction_data][colorId_string]"></td>
										<td width="80" style="color:#E2E2E2;" align="center"><p><? //echo $job_no_prv;?></p></td>
										<td width="80" style="color:#E2E2E2;" align="center"><p><? //echo $buyer_name_prv;?></p></td>
										<td width="80" style="color:#E2E2E2;" align="center"><p><? //echo $po_number_prv;?></p></td>
	                                    <td width="70" align="center"><p><? //echo change_date_format($row[csf("pub_shipment_date")]); ?></p></td>
	                                    <td width="70" align="center"><p><? //if($row[csf("status_active")]==3) echo change_date_format($row[csf("calcel_date")]); ?></p></td>
										<td width="80" style="color:#E2E2E2;" align="center"><p><? //echo $style_ref_no_prv;?></p></td>
										<td width="100"></td>
										<td width="100"><p><? //echo chop($booking_array[$break_down_id]['booking_no'],","); ?>&nbsp;</p></td>
										<td width="100" style="color:#E2E2E2;"><p><? //echo $construction_data_prv;?></p></td>
										<td width="100" style="color:#E2E2E2;"><p><? //echo $composition_string_prv;?></p></td>
										<td width="60" style="color:#E2E2E2;"><p><? //echo $gsm_string;?></p></td>
										<td width="60" style="color:#E2E2E2;"><p><? //echo $fdia_string;?></p></td>
										<td width="60"></td>
										<td width="60"></td>
										<td width="80" style="color:#E2E2E2;"><p><? //echo $dyeing_color_string_prv;?></p></td>
										<td width="80"></td>
										<td width="60"></td>
										<td width="80"></td>
										<td width="80"></td>
										<td width="120"></td>
										<td width="80" align="right" title="Req qty"><p><b><? echo number_format($grey_qnty,2,'.','');?></b></p></td>
										<td width="80" align="right"><? echo number_format($tot_rec_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80"  align="right"><? echo number_format($tot_iss_retn_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($tot_transfer_in_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($tot_rec_bal,2,'.',''); ?>&nbsp;</td>
										<td width="60" align="right"><? echo $tot_rec_roll; ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($tot_issue_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($tot_recv_retn_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($tot_transfer_out_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($tot_issue_bal,2,'.',''); ?>&nbsp;</td>
										<td width="60" align="right"><? echo $tot_issue_roll; ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($tot_stock,2,'.',''); ?>&nbsp;</td>
										<td width="60" align="right"><? //echo $tot_stock_roll_qty; ?>&nbsp;</td>
										<td width="50" align="center">&nbsp;</td>
										<td width="50" align="center">&nbsp;</td>
										<td width="50" align="center">&nbsp;</td>
										<td width="50" align="right">&nbsp;</td>
										<td width="50" align="right">&nbsp;</td>
										<td width="50" align="right">&nbsp;</td>
										<td width="50" align="right"><? echo number_format($grey_qnty-$tot_rec_bal,2,'.',''); //?></td>
										<td width="" align="right"><? echo number_format($grey_qnty-$tot_issue_bal,2,'.','');// ?></td>
									</tr>
									<?
									unset($colorData);
									unset($colorId_string);
									unset($grey_qnty);
									unset($dyeing_color_string);
									unset($colorId_string);
									unset($break_down_id);
									unset($tot_req_qty);
									unset($tot_rec_qty);
									unset($tot_transfer_in_qty);
									unset($tot_rec_bal);
									unset($tot_rec_roll);
									unset($tot_issue_qty);
									unset($tot_transfer_out_qty);
									unset($tot_issue_bal);
									unset($tot_issue_roll);
									unset($tot_stock);
									unset($tot_stock_roll_qty);
									unset($tot_iss_retn_qty);
									unset($tot_recv_retn_qty);
								}
								$ttt++;
							}
							$con_arr[]=$groupKey;
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30" title="Receive B"><? echo $i; ?></td>
								<td width="80"><p><? echo $job_no;?></p></td>
								<td width="80"><p><? echo $buyer_name; ?></p></td>
								<td width="80"><p><? echo $po_number; ?></p></td>
	                            <td width="70" align="center"><p><? echo change_date_format($row["pub_shipment_date"]); ?></p></td>
	                            <td width="70" align="center"><p><? if($row["status_active"]==3) echo change_date_format($row["calcel_date"]); ?></p></td>
								<td width="80"><p><? echo $style_ref_no; ?></p></td>
								<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
								<td width="100"><p><? echo implode(",",array_unique(explode(",",chop($booking_array[$order_id]['booking_no'],",")))); ?>&nbsp;</p></td>					
								<td width="100"><p><? echo $constructionArr[$product_array[$prod_id]['detarmination_id']]; ?></p></td>
								<td width="100"><p><? echo $copmpositionArr[$product_array[$prod_id]['detarmination_id']]; ?></p></td>
								<td width="60"><p><? echo $product_array[$prod_id]['gsm']; ?></p></td>
								<td width="60"><p><? echo $product_array[$prod_id]['dia_width']; ?></p></td>
								<td width="60"><p><? echo $machine_arr[$row['machine_no_id']]; ?></p></td>
								<td width="60"><p><? echo implode(",",array_unique(explode(",",chop($row['stitch_length'],',')))); ?></p></td>
								<td width="80"><p><? echo $color_name; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $color_range_name; ?>&nbsp;</p></td>
								<td width="60"><p><? echo $count_val; ?>&nbsp;</p></td> 
								<td width="80"><p><? echo $brand_name; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $yarn_lot; ?>&nbsp;</p></td>
								<td width="120"><p><? echo implode(",",array_unique(explode(",",chop($row['booking_no'],",")))); ?>&nbsp;</p></td>
								<td width="80" align="right"><p>&nbsp;</p><? //echo number_format($grey_qnty,2); $total_grey_qnty+=$grey_qnty;?></td>
								<td width="80" align="right"><p> <? $tot_rec_qty+=$row['quantity']; ?><a href='#report_details' onClick="openmypage_delivery_all('<? echo $order_id; ?>','<? echo $program_no; ?>','<? echo $prod_id; ?>','<? echo $row['entry_form']; ?>','<? echo ""; ?>','<? echo ""; ?>','950px','report_all_popup',1);"><? echo number_format($row['quantity'],2); ?></a>&nbsp;</p></td>									
								<td width="80" align="right"><p><? echo number_format($issue_retn,2); $tot_iss_retn_qty+=$issue_retn; ?>&nbsp;</p></td>
								<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2); $tot_transfer_in_qty+=$trnsfer_in_qty; ?>&nbsp;</p></td>
								<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal; ?>&nbsp;</p></td>
								<td width="60" align="right"><p><? $rec_roll=$row['rec_roll']+$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?>&nbsp;</p></td> 				
								<td width="80" align="right"><p>
									<a href='#report_details' onClick="openmypage_delivery_all('<? echo $order_id; ?>','<? echo $program_no; ?>','<? echo $prod_id; ?>','<? echo $row['entry_form']; ?>','<? echo $row['stitch_length']."**".$yarn_lot."**".$value[2]."**".$value[3]."**".$store."**".$floor."**".$room."**".$rack."**".$self; ?>','<? echo ""; ?>','850px','report_all_popup',4);">	<? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></a>&nbsp;</p>
								</td>
								<td width="80" align="right"><p><? echo number_format($recv_retn,2); $tot_recv_retn_qty +=$recv_retn; ?>&nbsp;</p></td>
								<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty; ?>&nbsp;</p></td>
								<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
								<td width="60" align="right"><p><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</p></td>										<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
								<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty;?>&nbsp;</p></td>
								<td width="50" align="center"><p><? echo $store_array[$store]; ?>&nbsp;</p></td>	
								<td width="50" align="center"><p><? echo $floor_room_rack_array[$floor]; ?>&nbsp;</p></td>	
								<td width="50" align="center"><p><? echo $floor_room_rack_array[$room]; ?>&nbsp;</p></td>		
								<td width="50" align="center"><p><? echo $floor_room_rack_array[$rack]; ?>&nbsp;</p></td>
								<td width="50" align="center"><p><? echo $floor_room_rack_array[$self]; ?>&nbsp;</p></td>
								<?
								$daysOnHand = datediff("d",change_date_format($transaction_date_array[$prod_id]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));?>				<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
								<td width="50" align="right"><p><? //echo $rec_bal; ?></p></td>
								<td width="" align="right"><p><? //echo $issue_bal; ?></p></td>
							</tr>
							<?
							$i++;
							$grand_tot_rec_qty+=$row['quantity'];
							$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
							$grand_tot_rec_bal+=$rec_bal;
	                    	//echo $rec_roll."**".$i."<br>";
							$grand_tot_rec_roll+=$rec_roll;
							$grand_tot_issue_qty+=$issue_qty;
							$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
							$grand_tot_issue_bal+=$issue_bal;
							$grand_tot_issue_roll+=$iss_roll;
							$grand_tot_stock+=$stock;
							$grand_tot_roll_qty+=$stock_roll_qty;
							$grand_tot_issue_retn_qty+=$issue_retn;
							$grand_tot_recv_retn_qty+=$recv_retn;

							$colorId_string_prv=$color_name;
							$dyeing_color_string_prv=$color_name;
							$job_no_prv=$row['job_no'];	
							$style_ref_no_prv=$row['style_ref_no'];	
							$buyer_name_prv=$buyer_arr[$row['buyer_name']];	
							$buyer_id_prv=$row['buyer_name'];	
							$po_number_prv=$row['po_number'];	
							$break_down_id_prv=$order_id;	
							$construction_data_prv=$constructionArr[$product_array[$prod_id]['detarmination_id']];
						}
					}
				}
				// die('end now');
				$groupKeyReq = $buyer_id_prv.$job_no_prv.$break_down_id_prv.$construction_data_prv.$colorId_string_prv;
				$grey_qnty=$grey_qnty_array[$groupKeyReq];
				$total_grey_qnty+=$grey_qnty;
				?>
				<tr class="tbl_bottom">
					<td width="30">&nbsp;</td>
					<td width="80" style="color:#E2E2E2;" align="center"><p><? //echo $job_no;?></p></td>
					<td width="80" style="color:#E2E2E2;" align="center"><p><? //echo $buyer_name;?></p></td>
					<td width="80" style="color:#E2E2E2;" align="center"><p><? //echo $po_number;?></p></td>
                    <td width="70" style="color:#E2E2E2;" align="center"><p><? //echo $po_number;?></p></td>
                    <td width="70" style="color:#E2E2E2;" align="center"><p><? //echo $po_number;?></p></td>
					<td width="80" style="color:#E2E2E2;" align="center"><p><? //echo $style_ref_no;?></p></td>
					<td width="100">&nbsp;</td>
					<td width="100"><p><? //echo $booking_array[$po_id]['booking_no']; ?>&nbsp;</p></td>
					<td width="100" style="color:#E2E2E2;"><p><? //echo $construction_data_prv;?></p></td>
					<td width="100" style="color:#E2E2E2;"><p><? //echo $composition_string;?></p></td>
					<td width="60" style="color:#E2E2E2;"><p><? //echo $gsm_string;?></p></td>
					<td width="60" style="color:#E2E2E2;"><p><? //echo $fdia_string;?></p></td>
					<td width="60">&nbsp;</td>
					<td width="60"></td>
					<td width="80" style="color:#E2E2E2;"><p><? //echo $dyeing_color_string_prv;?></p></td>
					<td width="80"></td>
					<td width="60"></td>
					<td width="80"></td>
					<td width="80"></td>
					<td width="120">&nbsp;</td>
					<td width="80" align="right"><p><? echo number_format($grey_qnty,2,'.','');?></p></td>
					<td width="80" align="right"><? echo number_format($tot_rec_qty,2,'.',''); ?>&nbsp;</td>
					<td width="80" align="right"><? echo number_format($tot_iss_retn_qty,2,'.',''); ?>&nbsp;</td>
					<td width="80" align="right"><? echo number_format($tot_transfer_in_qty,2,'.',''); ?>&nbsp;</td>
					<td width="80" align="right"><? echo number_format($tot_rec_bal,2,'.',''); ?>&nbsp;</td>
					<td width="60" align="right"><? echo $tot_rec_roll; ?>&nbsp;</td>
					<td width="80" align="right"><? echo number_format($tot_issue_qty,2,'.',''); ?>&nbsp;</td>
					<td width="80" align="right"><? echo number_format($tot_recv_retn_qty,2,'.',''); ?>&nbsp;</td>
					<td width="80" align="right"><? echo number_format($tot_transfer_out_qty,2,'.',''); ?>&nbsp;</td>
					<td width="80" align="right"><? echo number_format($tot_issue_bal,2,'.',''); ?>&nbsp;</td>
					<td width="60" align="right"><? echo $tot_issue_roll; ?>&nbsp;</td>
					<td width="80" align="right"><? //echo number_format($tot_stock,2,'.',''); ?>&nbsp;</td>
					<td width="60" align="right"><? //echo $tot_stock_roll_qty; ?>&nbsp;</td>
					<td width="50" align="right">&nbsp;</td>
					<td width="50" align="right">&nbsp;</td>
					<td width="50" align="right">&nbsp;</td>
					<td width="50" align="right">&nbsp;</td>
					<td width="50" align="right">&nbsp;</td>
					<td width="50" align="right">&nbsp;</td>
					<td width="50" align="right"><p><? echo number_format($grey_qnty-$tot_rec_bal,2,'.',''); //?></p></td>
					<td width="" align="right"><p><? echo number_format($grey_qnty-$tot_issue_bal,2,'.','');// ?></p></td>
				</tr>

				<tr class="tbl_bottom">
					<td width="30" align="right" colspan="21">Grand Total</td>
					<td width="80" align="right"><p><? echo number_format($total_grey_qnty,2,'.','');?></p></td>
					<td width="80" align="right"><? echo number_format($grand_tot_rec_qty,2,'.',''); ?>&nbsp;</td>
					<td width="80" align="right"><? echo number_format($grand_tot_issue_retn_qty,2,'.',''); ?>&nbsp;</td>
					<td width="80" align="right"><? echo number_format($grand_tot_transfer_in_qty,2,'.',''); ?>&nbsp;</td>
					<td width="80" align="right"><? echo number_format($grand_tot_rec_bal,2,'.',''); ?>&nbsp;</td>
					<td width="60" align="right"><? echo $grand_tot_rec_roll; ?>&nbsp;</td>
					<td width="80" align="right"><? echo number_format($grand_tot_issue_qty,2,'.',''); ?>&nbsp;</td>
					<td width="80" align="right"><? echo number_format($grand_tot_recv_retn_qty,2,'.',''); ?>&nbsp;</td>
					<td width="80" align="right"><? echo number_format($grand_tot_transfer_out_qty,2,'.',''); ?>&nbsp;</td>
					<td width="80" align="right"><? echo number_format($grand_tot_issue_bal,2,'.',''); ?>&nbsp;</td>
					<td width="60" align="right"><? echo $grand_tot_issue_roll; ?>&nbsp;</td>
					<td width="80" align="right"><? echo number_format($grand_tot_stock,2,'.',''); ?>&nbsp;</td>
					<td width="60" align="right"><? echo $grand_tot_roll_qty; ?>&nbsp;</td>
					<td width="50" align="right">&nbsp;</td>
					<td width="50" align="right">&nbsp;</td>
					<td width="50" align="right">&nbsp;</td>
					<td width="50" align="right">&nbsp;</td>
					<td width="50" align="right">&nbsp;</td>
					<td width="50" align="right">&nbsp;</td>
					<td width="50" align="right"><p><? echo number_format($total_grey_qnty-$grand_tot_rec_bal,2,'.',''); //?></p></td>
					<td width="" align="right"><p><? echo number_format($total_grey_qnty-$grand_tot_issue_qty,2,'.','');// ?></p></td>
				</tr>	
			</table>
		</div>
	</fieldset>
	<?	
	foreach (glob("*.xls") as $filename) {
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}

	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$html####$filename####$rptType";
	disconnect($con);
	exit();
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

if($action=="report_all_popup")
{
	echo load_html_head_contents("Grey Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$companyID= $companyID;
	$orderID=$orderID;
	$programNo=$programNo;
	$prodID=$prodID;
	$entryForm=$entryForm;
	$to_date=$to_date;
	$year_id=$year_id;
	$type=$type;
	if( str_replace("'","",$to_date)=="") $trans_date=""; else $trans_date= " and a.transaction_date <=".$to_date."";

	if($type==2)
	{
		$issue_return_data=explode("**",$issue_return_data);
		$issueRetrn_poId=$issue_return_data[0];
		$issueRetrn_prod_id=$issue_return_data[1];
		$issueRetrn_yarn_count=$issue_return_data[2];
		$issueRetrn_batch_lot=$issue_return_data[3];
		$issueRetrn_store=$issue_return_data[4];
		$issueRetrn_floor=$issue_return_data[5];
		$issueRetrn_room=$issue_return_data[6];
		$issueRetrn_rack=$issue_return_data[7];
		$issueRetrn_self=$issue_return_data[8];
		$issueRetrn_yarn_lot=$issue_return_data[9];
	}
	else if($type==3)
	{
		$transIn_data=explode("**",$transIn_data);
		$transIn_poId=$transIn_data[0];
		$transIn_prod_id=$transIn_data[1];
		$transIn_yarn_count=$transIn_data[2];
		$transIn_batch_lot=$transIn_data[3];
		$transIn_store=$transIn_data[4];
		$transIn_floor=$transIn_data[5];
		$transIn_room=$transIn_data[6];
		$transIn_rack=$transIn_data[7];
		$transIn_self=$transIn_data[8];
	}
	else if($type==4 || $type==6)
	{
		$other_data=explode("**",$other_data);
		$stitch_length=$other_data[0];
		$lot=$other_data[1];
	}
	
	if($type==5)
	{
		$transOut_data=explode("**",$transOut_data);
		$transOut_poId=$transOut_data[0];
		$transOut_prod_id=$transOut_data[1];
		$transOut_yarn_count=$transOutn_data[2];
		$transOut_batch_lot=$transOut_data[3];
		$transOut_store=$transOut_data[4];
		$transOut_floor=$transOut_data[5];
		$transOut_room=$transOut_data[6];
		$transOut_rack=$transOut_data[7];
		$transOut_self=$transOut_data[8];
	}
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(b.insert_date)=$year_id"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_cond=" and TO_CHAR(b.insert_date,'YYYY')=$year_id"; else $year_cond="";
	}
	$product_details_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
	$color_name_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
	$brand_name_arr = return_library_array("select id, brand_name from  lib_brand", 'id', 'brand_name');
	$buyer_name_arr = return_library_array("select id, buyer_name from  lib_buyer", 'id', 'buyer_name');
	$store_name_arr = return_library_array("select id, store_name from  lib_store_location", 'id', 'store_name');
	$supplier_arr = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1", 'id', 'supplier_name');
	if ($type==1) 
    {
		?>
		<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0">
	                <thead>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="150">Knitting Challan No</th>
	                        <th width="100">Knitting Source</th>
	                        <th width="100">Vendor</th>
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
	             <div style="width:1050px; max-height:410px; overflow-y:scroll" id="scroll_body">
	                <table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0" id="table_body">
	                    <?
	                   	if($db_type==0)
						{
							$sql_dtls="select e.recv_number,e.knitting_source,e.booking_no as knitting_challan_no,e.receive_date, sum(c.quantity) as quantity, group_concat(d.stitch_length) as stitch_length, d.yarn_lot 
							from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e 
							where a.job_id=b.id and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0  
							and c.po_breakdown_id=a.id and c.dtls_id=d.id and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 and  e.item_category=13 and e.status_active=1 and e.is_deleted=0 
							$year_cond 
							and a.id=$orderID and e.company_id=$companyID  and c.prod_id=$prodID and e.entry_form=$entryForm 
							group by e.recv_number,e.knitting_source,e.booking_no,e.receive_date,a.id, a.po_number, a.file_no, a.grouping, a.po_quantity, c.prod_id, d.yarn_count, d.yarn_lot, 
							d.rack, d.self, b.job_no, b.buyer_name, b.style_ref_no,e.entry_form,c.quantity
							order by a.id,a.po_number, c.prod_id ";
						}
						else if($db_type==2)
						{
							$sqlData="SELECT e.recv_number,e.knitting_source, e.knitting_company,e.booking_no as knitting_challan_no,e.receive_date, sum(c.quantity) as quantity,
							listagg(d.stitch_length,',') within group (order by d.stitch_length) as stitch_length, d.yarn_lot 
							from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e 
							where a.job_id=b.id and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=a.id and c.dtls_id=d.id and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 and  e.item_category=13 and e.status_active=1 and e.is_deleted=0 
							$year_cond and a.id=$orderID and e.company_id=$companyID  and c.prod_id=$prodID and e.entry_form=$entryForm 
							group by e.recv_number,e.knitting_source, e.knitting_company,e.booking_no,e.receive_date,a.id, a.po_number, a.file_no, a.grouping, a.po_quantity, c.prod_id, d.yarn_count, d.yarn_lot, 
							d.rack, d.self, b.job_no, b.buyer_name, b.style_ref_no,e.entry_form,c.quantity
							order by a.po_number, e.knitting_source ";
						}
						//echo $sqlData;
						$sql_dtls=sql_select($sqlData);
						$i=1;
						foreach ($sql_dtls as $row) 
						{
							if ($row[csf('knitting_source')]==1) 
							{
								$vendor=$company_arr[$row[csf('knitting_company')]];
							}
							else
							{
								$vendor=$supplier_arr[$row[csf('knitting_company')]];
							}
							
	                    	?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="150"><p><? echo $row[csf('knitting_challan_no')]; ?></p></td>
	                            <td width="100"  align="center"><? echo $knitting_source[$row[csf('knitting_source')]];// ?></td>
	                            <td width="100"  align="center"><? echo $vendor; ?></td>
	                            <td width="150"><div style="word-break:break-all"><p><? echo $row[csf('recv_number')]; ?></p></div></td>
	                            <td width="60"><div style="word-break:break-all"><? echo change_date_format($row[csf('receive_date')]);//$buyer_name_arr[$job_data_arr[$po_id]["buyer_name"]]; ?></div></td>
	                            <td width="60" align="center"><div style="word-break:break-all"><? echo $row[csf('room')]; ?></div></td>
	                            <td width="60" align="center"><div style="word-break:break-all"><? echo $row[csf('rack')]; ?></div></td>

	                            <td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('yarn_lot')]; ?></div></td>
	                            <td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('stitch_length')]; ?></div></td>
	                            <td align="right"><? echo $row[csf("quantity")]; ?>&nbsp;</td> 
	                            
	                        </tr>
	                    <?
	                    $total_recv_qty+=$row[csf("quantity")];
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                    	<tr>
	                            <th colspan="10" align="right">Total</th>
	                            <th align="right"><? echo number_format($total_recv_qty,2); ?></th>
	                        </tr>
	                    </tfoot>
	                </table>
				</div>		
			</div>
		</fieldset>	
		<?
	}
	else if ($type==2)  
	{
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
							$issue_return_cond="";
							if ($issueRetrn_yarn_lot=="") 
							{
								$issue_return_cond=""; 
							}
							else 
							{
								$issue_return_cond.=" and g.lot ='$issueRetrn_yarn_lot'";
							}
							//echo $issueRetrn_yarn_lot;die;
							
							/*if ($issueRetrn_yarn_count=="") $issue_return_cond.=""; else $issue_return_cond.=" and f.yarn_count in('$issueRetrn_yarn_count')";
							if ($issueRetrn_rack) $issue_return_cond.=" and f.rack ='$issueRetrn_rack'"; else $issue_return_cond.="";
							if ($issueRetrn_self) $issue_return_cond.=" and f.self ='$issueRetrn_self'"; else $issue_return_cond.="";
							if ($issueRetrn_store) $issue_return_cond.=" and f.store_id ='$issueRetrn_store'"; else $issue_return_cond.="";
							if ($issueRetrn_floor) $issue_return_cond.=" and f.store_id ='$issueRetrn_floor'"; else $issue_return_cond.="";

							$search_cond="";
							if ($issueRetrn_poId=="") $search_cond.=""; else $search_cond.=" and b.po_breakdown_id in($issueRetrn_poId)";
							if ($issueRetrn_prod_id=="") $search_cond.=""; else $search_cond.=" and a.prod_id in($issueRetrn_prod_id)";
							if ($issueRetrn_yarn_count=="") $search_cond.=""; else $search_cond.=" and a.yarn_count in('$issueRetrn_yarn_count')";
							if ($issueRetrn_batch_lot=="") $search_cond.=""; else $search_cond.=" and a.batch_lot in('$issueRetrn_batch_lot')";
							if ($issueRetrn_rack=="") $search_cond.=""; else $search_cond.=" and a.rack ='$issueRetrn_rack'";
							//if ($issueRetrn_self=="") $search_cond.=""; else $search_cond.=" and a.self ='$issueRetrn_self'";

							$sql_retn="select b.po_breakdown_id, a.prod_id, a.yarn_count, a.batch_lot, a.rack, a.self, sum(case when a.transaction_type=4 and b.trans_type=4 and b.entry_form in(51,84) then b.quantity end) as iss_rtn_qty, sum(case when a.transaction_type=3 and b.trans_type=3 and b.entry_form=45 then b.quantity end) as rcv_rtn_qty,
							sum(case when a.transaction_type in(6) and b.trans_type in(6) and b.entry_form in(82) then b.quantity end) as transferOut,
							sum(case when a.transaction_type in(5) and b.trans_type in(5) and b.entry_form in(82) then b.quantity end) as transferIn 
							from inv_transaction a, order_wise_pro_details b, wo_po_break_down c 
							where a.id=b.trans_id and b.po_breakdown_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=13 and b.entry_form in(45,51,81,82,83,84) and a.company_id=$companyID and a.transaction_type in(1,2,3,4,5,6) and b.trans_type in (1,2,3,4,5,6) $return_order_cond 
							group by b.po_breakdown_id, a.prod_id, a.batch_lot, a.yarn_count, a.rack, a.self";
							$data_retn_array=sql_select($sql_retn);
							$transf_data_arr=array();
							foreach($data_retn_array as $row )
							{
								if($row[csf('self')]=="") $row[csf('self')]=0;
								if($row[csf('rack')]=="") $row[csf('rack')]=0;
								$retn_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('batch_lot')]][$row[csf('rack')]][$row[csf('self')]]['iss']=$row[csf('iss_rtn_qty')];
								$retn_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('batch_lot')]][$row[csf('rack')]][$row[csf('self')]]['recv']=$row[csf('rcv_rtn_qty')];

								$transf_data_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('batch_lot')]][$row[csf('rack')]][$row[csf('self')]][5]['trnsfIn']=$row[csf('transferIn')];
								$transf_data_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('batch_lot')]][$row[csf('rack')]][$row[csf('self')]][6]['trnsfOut']=$row[csf('transferOut')];

								$rtn_data=$row[csf('po_breakdown_id')]."_".$row[csf('prod_id')]."_".$row[csf('yarn_count')]."_".$row[csf('batch_lot')]."_".$row[csf('rack')]."_".$row[csf('self')];
								$retn_data_arr[]=$rtn_data;
							}
							
							if($db_type==0)
							{
								$sqlData="select  a.id as po_breakdown_id,c.prod_id, f.rack, f.self,f.yarn_count, f.batch_lot,e.recv_number,e.knitting_source,e.booking_no as knitting_challan_no,e.receive_date, sum(c.quantity) as quantity, group_concat(d.stitch_length) as stitch_length, d.yarn_lot 
								from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e 
								where a.job_id=b.id and f.id=c.trans_id and f.id=d.trans_id and b.status_active=1 and b.is_deleted=0 and c.trans_type=4 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0  
								and c.po_breakdown_id=a.id and c.dtls_id=d.id and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 and  e.item_category=13 and e.status_active=1 and e.is_deleted=0 
								$year_cond 
								and a.id=$orderID and e.company_id=$companyID  and c.prod_id=$prodID and e.entry_form in(52,84) 
								   group by a.id,c.prod_id, f.rack, f.self,f.yarn_count, f.batch_lot,e.recv_number,e.knitting_source,e.booking_no,e.receive_date, a.po_number, a.file_no, a.grouping, a.po_quantity,c.prod_id, d.yarn_count, d.yarn_lot, d.rack, d.self, b.job_no, b.buyer_name, b.style_ref_no,e.entry_form,c.quantity 
								order by a.id,a.po_number, c.prod_id ";
							}
							else if($db_type==2)
							{
								$sqlData="select  a.id as po_breakdown_id,c.prod_id, f.rack, f.self,f.yarn_count, f.batch_lot,e.recv_number,e.knitting_source,e.booking_no as knitting_challan_no,e.receive_date, sum(c.quantity) as quantity,
								listagg(d.stitch_length,',') within group (order by d.stitch_length) as stitch_length, d.yarn_lot 
								from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e,inv_transaction f 
								where a.job_id=b.id and f.id=c.trans_id and f.id=d.trans_id and b.status_active=1 and b.is_deleted=0 and c.trans_type=4 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0  
								and c.po_breakdown_id=a.id and c.dtls_id=d.id and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 and  e.item_category=13 and e.status_active=1 and e.is_deleted=0 
								$year_cond 
								and a.id=$orderID and e.company_id=$companyID  and c.prod_id=$prodID and e.entry_form in(52,84)
								  group by a.id,c.prod_id, f.rack, f.self,f.yarn_count, f.batch_lot,e.recv_number,e.knitting_source,e.booking_no,e.receive_date, a.po_number, a.file_no, a.grouping, a.po_quantity,c.prod_id, d.yarn_count, d.yarn_lot, d.rack, d.self, b.job_no, b.buyer_name, b.style_ref_no,e.entry_form,c.quantity 
								order by a.id,a.po_number, c.prod_id ";
							}
							*/

							$sqlData="SELECT e.recv_number, e.receive_date, sum(c.quantity) as quantity
							from wo_po_break_down a, order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e,inv_transaction f, product_details_master g
							where a.id=c.po_breakdown_id and c.trans_id=f.id and f.id=d.trans_id  and c.trans_type=4 and c.trans_id!=0 and f.prod_id=g.id and c.status_active=1 and c.is_deleted=0 and  c.dtls_id=d.id and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 and e.item_category=13 and e.status_active=1 and e.is_deleted=0  and a.id=$orderID and e.company_id=$companyID and c.prod_id=$prodID and e.entry_form in(52,84) $issue_return_cond						
							group by e.recv_number, e.receive_date";

							/*$sql_retn="select d.recv_number,d.receive_date,b.po_breakdown_id, a.prod_id, a.yarn_count, a.batch_lot, a.rack, a.self, sum(case when a.transaction_type=4 and b.trans_type=4 and b.entry_form in(51,84) then b.quantity end) as iss_rtn_qty
							from inv_transaction a, order_wise_pro_details b, wo_po_break_down c,inv_receive_master d 
							where a.id=b.trans_id and b.po_breakdown_id=c.id and d.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=13 and b.entry_form in(51,84) and a.company_id=$companyID and a.transaction_type in(1,2,3,4,5,6) and b.trans_type in (1,2,3,4,5,6)  $search_cond
							group by d.recv_number,d.receive_date,b.po_breakdown_id, a.prod_id, a.batch_lot, a.yarn_count, a.rack, a.self";*/
						
						//echo $sqlData;===========
						$sql_dtls=sql_select($sqlData);

						$i=1;
						foreach ($sql_dtls as $row) {
								if($row[csf('self')]=="") $row[csf('self')]=0;
								if($row[csf('rack')]=="") $row[csf('rack')]=0;

							
							//$issue_retn=$retn_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('batch_lot')]][$row[csf('rack')]][$row[csf('self')]]['iss'];
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="150"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                            <td width="150"  align="center"><? echo change_date_format($row[csf('receive_date')]);// ?></td>
	                            <td align="right"><? echo number_format($row[csf('quantity')],2); ?>&nbsp;</td> 
	                            
	                        </tr>
	                    <?
	                    $total_issue_retrn_qty+=$row[csf('quantity')];
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
	else if ($type==3)  
	{
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
							$search_cond="";
							if ($transIn_poId=="") $search_cond.=""; else $search_cond.=" and b.po_breakdown_id in($transIn_poId)";
							if ($transIn_prod_id=="") $search_cond.=""; else $search_cond.=" and a.prod_id in($transIn_prod_id)";
							if ($transIn_yarn_count=="") $search_cond.=""; else $search_cond.=" and a.yarn_count in('$transIn_yarn_count')";
							if ($transIn_batch_lot=="") $search_cond.=""; else $search_cond.=" and a.batch_lot in('$transIn_batch_lot')";
							if ($transIn_rack=="") $search_cond.=""; else $search_cond.=" and a.rack ='$transIn_rack'";
							//if ($issueRetrn_self=="") $search_cond.=""; else $search_cond.=" and a.self ='$issueRetrn_self'";

							$sql_transfer_in="select e.transfer_system_id,e.transfer_date,f.from_order_id,d.recv_number,d.receive_date,b.po_breakdown_id, a.prod_id, a.yarn_count, a.batch_lot, a.rack, a.self, sum(case when a.transaction_type in(5) and b.trans_type in(5) and b.entry_form in(82) then b.quantity end) as transferIn
							from inv_transaction a, order_wise_pro_details b, wo_po_break_down c,inv_receive_master d,inv_item_transfer_mst e, inv_item_transfer_dtls f  
							where a.id=b.trans_id and b.po_breakdown_id=c.id and d.id=a.mst_id and a.status_active=1 and a.mst_id=e.id and e.id=f.mst_id and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.item_category=13 and b.entry_form in(82) and a.company_id=$companyID and a.transaction_type in(1,2,3,4,5,6) and b.trans_type in (1,2,3,4,5,6) $search_cond 
							group by d.recv_number,d.receive_date,b.po_breakdown_id, a.prod_id, a.batch_lot, a.yarn_count, a.rack, a.self,e.transfer_system_id,e.transfer_date,f.from_order_id";
							//, sum(case when a.transaction_type=3 and b.trans_type=3 and b.entry_form=45 then b.quantity end) as rcv_rtn_qty,sum(case when a.transaction_type in(6) and b.trans_type in(6) and b.entry_form in(82) then b.quantity end) as transferOut,  and b.entry_form in(45,51,81,82,83,84)

							$sql_dtls=sql_select($sql_transfer_in);
	 						$from_orderId=$sql_dtls[0][csf('from_order_id')];

							$sql_booking=sql_select("select b.booking_no,b.po_break_down_id,c.po_number from wo_booking_mst a,wo_booking_dtls b ,wo_po_break_down c where a.booking_no=b.booking_no  and a.job_no=c.job_no_mst and  a.po_break_down_id='$from_orderId' and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id='$from_orderId' group by b.booking_no,b.po_break_down_id,c.po_number");
							foreach ($sql_booking as $row) {
								$booking_arr[$row[csf('po_break_down_id')]]['booking_no']=$row[csf('booking_no')];
								$booking_arr[$row[csf('po_break_down_id')]]['po_number']=$row[csf('po_number')];
							}
							//echo $sql_retn;
							$i=1;
							foreach ($sql_dtls as $row) {
		                    ?>
		                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
		                            <td width="30"><? echo $i; ?></td>
		                            <td width="150"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
		                            <td width="150"  align="center"><? echo change_date_format($row[csf('transfer_date')]);// ?></td>
		                            <td width="100"><p><? echo $booking_arr[$row[csf('from_order_id')]]['po_number']; ?></p></td>
		                            <td width="100"><p><? echo $booking_arr[$row[csf('from_order_id')]]['booking_no']; ?></p></td>
		                            <td align="right"><? echo number_format($row[csf("transferIn")],2); ?>&nbsp;</td> 
		                        </tr>
		                    <?
		                    $total_transferIn_qty+=$row[csf("transferIn")];
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
				<table border="1" class="rpt_table" rules="all" width="930" cellpadding="0" cellspacing="0">
	                <thead>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="150">Issue ID</th>
	                        <th width="100">Dyeing Source</th>
	                        <th width="100">Vendor</th>
	                        <th width="150">Issue Date</th>
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
						if ($lot=="") $search_cond=""; else $search_cond=" and d.yarn_lot in('$lot')";
						$sqlData="SELECT c.issue_number,c.issue_date,c.knit_dye_source, c.knit_dye_company,d.yarn_lot,d.stitch_length,b.trans_type, b.po_breakdown_id, b.prod_id, sum(b.quantity) as qnty 
						from inv_transaction a, order_wise_pro_details b,inv_issue_master c,inv_grey_fabric_issue_dtls d 
						where a.id=b.trans_id and a.mst_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(61) and a.item_category=13 and a.transaction_type in(1,2,3,4,5,6) and b.trans_type  in(1,2,3,4,5,6)  and b.po_breakdown_id=$orderID and b.prod_id=$prodID $search_cond 
						group by b.trans_type, b.po_breakdown_id,c.knit_dye_source, c.knit_dye_company, b.prod_id,c.issue_number,c.issue_date,d.yarn_lot,d.stitch_length order by c.knit_dye_source";

						// echo $sqlData;
						$sql_dtls=sql_select($sqlData);
						$i=1;
						foreach ($sql_dtls as $row) 
						{
							if ($row[csf('knit_dye_source')]==1) 
							{
								$vendor=$company_arr[$row[csf('knit_dye_company')]];
							}
							else
							{
								$vendor=$supplier_arr[$row[csf('knit_dye_company')]];
							}
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="150" align="center"><p><? echo $row[csf('issue_number')]; ?></p></td>
	                            <td width="100"  align="center"><? echo $knitting_source[$row[csf('knit_dye_source')]];// ?></td>
	                            <td width="100"  align="center"><? echo $vendor;?></td>
	                            <td width="150" align="center"><div style="word-break:break-all"><p><? echo change_date_format($row[csf('issue_date')]);//$buyer_name_arr[$job_data_arr[$po_id]["buyer_name"]]; ?></p></div></td>
	                            <td width="60"><div style="word-break:break-all"></div></td>
	                            <td width="60" align="center"><div style="word-break:break-all"><? echo $row[csf('rack')]; ?></div></td>

	                            <td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('yarn_lot')]; ?></div></td>
	                            <td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('stitch_length')]; ?></div></td>
	                            <td align="right"><? echo $row[csf("qnty")]; ?>&nbsp;</td> 
	                            
	                        </tr>
	                    <?
	                    $total_recv_qty+=$row[csf("qnty")];
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
	else if ($type==5)  
	{
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
						$search_cond="";
						if ($transOut_poId=="") $search_cond.=""; else $search_cond.=" and b.po_breakdown_id in($transOut_poId)";
						if ($transOut_prod_id=="") $search_cond.=""; else $search_cond.=" and a.prod_id in($transOut_prod_id)";
						if ($transOut_yarn_count=="") $search_cond.=""; else $search_cond.=" and a.yarn_count in('$transOut_yarn_count')";
						if ($transOut_batch_lot=="") $search_cond.=""; else $search_cond.=" and a.batch_lot in('$transOut_batch_lot')";
						//if ($transOut_rack=="") $search_cond.=""; else $search_cond.=" and a.rack ='$transOut_rack'";
						//if ($transOut_self=="") $search_cond.=""; else $search_cond.=" and a.self ='$transOut_self'";

						$sql_dtls=sql_select($sql_transfer_out);

						$sql_transfer_out_data=sql_select("SELECT a.id,a.transfer_system_id,a.transfer_date, b.from_order_id, b.to_order_id, b.transfer_qnty, b.from_prod_id, b.to_prod_id
						from inv_item_transfer_mst a, inv_item_transfer_dtls b 
						where a.id=b.mst_id and b.from_order_id=$orderID and b.from_prod_id=$prodID and a.status_active=1 and a.is_deleted=0
						group by a.id,a.transfer_system_id,a.transfer_date, b.from_order_id,b.to_order_id, b.transfer_qnty, b.from_prod_id, b.to_prod_id");

 						$to_orderId=$sql_transfer_out_data[0][csf('to_order_id')];	
						
						$sql_booking=sql_select("SELECT b.booking_no,b.po_break_down_id,c.po_number 
						from wo_booking_mst a,wo_booking_dtls b ,wo_po_break_down c 
						where a.booking_no=b.booking_no  and a.job_no=c.job_no_mst and a.po_break_down_id='$to_orderId' and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id='$to_orderId' 
						group by b.booking_no,b.po_break_down_id,c.po_number");
						foreach ($sql_booking as $row) 
						{
							$booking_arr[$row[csf('po_break_down_id')]]['booking_no'].=$row[csf('booking_no')].",";
							$booking_arr[$row[csf('po_break_down_id')]]['po_number'].=$row[csf('po_number')].",";
						}
						//echo $sql_retn;
						$i=1;
						foreach ($sql_transfer_out_data as $row) 
						{
	                    	?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="150"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
	                            <td width="150"  align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
	                            <td width="100"><p><? echo chop($booking_arr[$row[csf('from_order_id')]]['po_number'],','); ?></p></td>
	                            <td width="100"><p><? echo chop($booking_arr[$row[csf('from_order_id')]]['booking_no'],','); ?></p></td>
	                            <td align="right"><? echo number_format($row[csf("transfer_qnty")],2); ?>&nbsp;</td> 
	                        </tr>
		                    <?
		                    $total_transferOut_qty+=$row[csf("transfer_qnty")];
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
	else if ($type==6) 
	{
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
						if($db_type==0)
						{
							$sql_recv_dtls="select c.po_breakdown_id,c.prod_id,e.recv_number,e.knitting_source,e.booking_no as knitting_challan_no,e.receive_date, c.quantity as quantity, group_concat(d.stitch_length) as stitch_length, d.yarn_lot 
							from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e 
							where a.job_id=b.id and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0  
							and c.po_breakdown_id=a.id and c.dtls_id=d.id and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 and  e.item_category=13 and e.status_active=1 and e.is_deleted=0 
							$year_cond 
							and a.id=$orderID and e.company_id=$companyID  and c.prod_id=$prodID and e.entry_form=$entryForm 
							group by c.po_breakdown_id,c.prod_id,e.recv_number,e.knitting_source,e.booking_no,e.receive_date,a.id, a.po_number, a.file_no, a.grouping, a.po_quantity, c.prod_id, d.yarn_count, d.yarn_lot, 
							d.rack, d.self, b.job_no, b.buyer_name, b.style_ref_no,e.entry_form,c.quantity
							order by a.id,a.po_number, c.prod_id ";
						}
						else if($db_type==2)
						{
							$sql_recv_dtls="select e.recv_number,e.knitting_source,e.booking_no as knitting_challan_no,e.receive_date, c.quantity as quantity,
							listagg(d.stitch_length,',') within group (order by d.stitch_length) as stitch_length, d.yarn_lot 
							from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e 
							where a.job_id=b.id and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0  
							and c.po_breakdown_id=a.id and c.dtls_id=d.id and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 and  e.item_category=13 and e.status_active=1 and e.is_deleted=0 
							$year_cond 
							and a.id=$orderID and e.company_id=$companyID  and c.prod_id=$prodID and e.entry_form=$entryForm 
							group by e.recv_number,e.knitting_source,e.booking_no,e.receive_date,a.id, a.po_number, a.file_no, a.grouping, a.po_quantity, c.prod_id, d.yarn_count, d.yarn_lot, 
							d.rack, d.self, b.job_no, b.buyer_name, b.style_ref_no,e.entry_form,c.quantity
							order by a.id,a.po_number, c.prod_id ";
						}

							if ($lot=="") $search_cond=""; else $search_cond=" and d.yarn_lot in('$lot')";
							$sql_issue_data="select c.issue_number,c.issue_date,c.knit_dye_source,d.yarn_lot,d.stitch_length,b.trans_type, b.po_breakdown_id, b.prod_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b,inv_issue_master c,inv_grey_fabric_issue_dtls d where a.id=b.trans_id and a.mst_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(61) and a.item_category=13 and a.transaction_type in(1,2,3,4,5,6) and b.trans_type  in(1,2,3,4,5,6)  and b.po_breakdown_id=$orderID and b.prod_id=$prodID $search_cond group by b.trans_type, b.po_breakdown_id,c.knit_dye_source, b.prod_id,c.issue_number,c.issue_date,d.yarn_lot,d.stitch_length";
							
							$issue_arr=array();
							foreach ($sql_trans_sql as $row) {
								$issue_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]["issueQnty"]=$row[csf('qnty')];
							}
							//print_r($issue_arr);
						
						//echo $sqlData;
						$sql_dtls=sql_select($sql_recv_dtls);
						$i=1;
						foreach ($sql_dtls as $row) {
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="150"><p><? echo $row[csf('knitting_challan_no')]; ?></p></td>
	                            <td width="100"  align="center"><? echo $row[csf('yarn_lot')];// ?></td>
	                            <td width="100" align="center"><div style="word-break:break-all"><p><? echo $row[csf('stitch_length')]; ?></p></div></td>
	                            <td width="100"><div style="word-break:break-all"><? echo $row[csf('rack')];?></div></td>
	                            <td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('shelf')]; ?></div></td>
	                            <td align="right"><? echo $balance= $row[csf("quantity")]-$issue_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]["issueQnty"]; ?>&nbsp;</td> 
	                            
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
                    	$programData=sql_select("select a.issue_number as sys_number,a.issue_date as sys_no_date,sum(b.cons_quantity) as grey_receive_qnty,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id from inv_issue_master a, inv_transaction b, order_wise_pro_details c, inv_grey_fabric_issue_dtls d where a.id=b.mst_id and b.id=c.trans_id and c.dtls_id=d.id and a.entry_form in(16,61) and c.trans_id <>0 and c.trans_type=2 and c.entry_form in (16,61) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($prodID) group by a.issue_number,a.issue_date,b.room,b.rack,b.self,b.bin_box,b.cons_uom,d.no_of_roll,b.store_id,c.po_breakdown_id,b.prod_id");
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
						$programData=sql_select("SELECT a.transfer_system_id,a.transfer_date,a.to_store_id,sum(b.transfer_qnty) as transfer_qnty,b.to_rack as rack,b.to_shelf as self,b.bin_box,b.uom,b.no_of_roll,c.po_breakdown_id,b.from_prod_id as prod_id,d.roll_no ,sum(d.qnty) as roll_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c,pro_roll_details d where a.id=b.mst_id and b.to_trans_id=c.trans_id and b.id=d.dtls_id and a.entry_form in(82,83) AND d.entry_form IN (82, 83) and c.trans_id <>0 and c.entry_form in (82,83) and c.trans_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$companyID and c.prod_id in($prodID) group by c.po_breakdown_id,b.from_prod_id,a.transfer_system_id,a.transfer_date,a.to_store_id,b.to_rack,b.to_shelf,b.bin_box,b.uom,b.no_of_roll,d.roll_no");    

						                 
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
	exit();
}