<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where company_id in($data)  and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "load_drop_down( 'requires/finish_fabric_stock_summary_controller','$data'+'***'+this.value, 'load_drop_down_stores', 'store_td' );fncMultiStore();","" );
	exit();
}
if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	$company_id = $data[0];
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($company_id) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

/*if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_id", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id='$data' and  b.category_type in(2,3)  group by a.id,a.store_name","id,store_name", 1, "--Select Store--", 0, "",$disable );
	exit();
}*/

if($action=="load_drop_down_stores")
{
    extract($_REQUEST);

    $datas=explode("***", $data);

    $company_ids = str_replace("'","",$datas[0]); 
    if($datas[1] != ""){$location_id_cond="and a.location_id=$datas[1]";}
	echo create_drop_down( "cbo_store_id", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id in($company_ids) and b.category_type in(2,3) $location_id_cond  group by a.id,a.store_name","id,store_name", 0, "", 0, "",$disable );
	exit();
}


if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$search_type_main= $search_type;
	?>
	<script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	
	function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value(functionParam);

			}
		}
	function toggle( x, origColor ) {
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
                    <th>Year</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170"><? if($search_type_main==1){echo "Please Enter Job No";}else if($search_type_main==2){echo "Please Enter Style";}else if($search_type_main==3){echo "Please Enter Booking";} ?></th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($companyID) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name  order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>
                        <td> 
                            <?
								echo create_drop_down( "cbo_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                            ?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Booking No");

							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							if($search_type_main==1){$search_id=1;}else if($search_type_main==2){$search_id=2;}else if($search_type_main==3){$search_id=3;}
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", $search_id,$dd,1 );
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $search_type_main; ?>'+'**'+document.getElementById('cbo_year').value, 'create_job_no_search_list_view', 'search_div', 'finish_fabric_stock_summary_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	$search_by=$data[2];
	//$year_id=$data[4];
	$search_type_main=$data[4];
	$cbo_year=$data[5];
	//$month_id=$data[5];
	//echo $company_id;die;

	$year_id=str_replace("'","",$cbo_year);

	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";
		if($year_id!=0) $year_cond_2=" and year(a.insert_date)=$year_id"; else $year_cond_2="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_cond=" and TO_CHAR(insert_date,'YYYY')=$year_id"; else $year_cond="";
		if($year_id!=0) $year_cond_2=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_cond_2="";
	}


	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond_2=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond_2="";
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond_3=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond_3="";
		}
		else
		{
			$buyer_id_cond="";
			$buyer_id_cond_2="";
			$buyer_id_cond_3="";
		}
	}
	else
	{
		if($search_by==1 || $search_by==2)
		{
			$buyer_id_cond=" and buyer_name=$data[1]";
			$buyer_id_cond_2=" and a.buyer_name=$data[1]";
		}
		else
		{
			$buyer_id_cond=" and buyer_id=$data[1]";
			$buyer_id_cond_2=" and a.buyer_id=$data[1]";
		}
	}

	
	$search_string="%".trim($data[3])."%";
	if($search_by==1) {$search_field="job_no_prefix_num";$search_field_2="a.job_no_prefix_num";} else if($search_by==2){$search_field="style_ref_no";$search_field_2="a.style_ref_no";}else{$search_field="booking_no_prefix_num";$search_field_2="a.booking_no_prefix_num";}
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="year(insert_date) as year ";
	else if($db_type==2) $year_field_by="to_char(insert_date,'YYYY') as year ";
	//if($db_type==0) $month_field_by="and month(insert_date)";
	//else if($db_type==2) $month_field_by="and to_char(insert_date,'MM')";
	///if($db_type==0) $year_field="and year(insert_date)=$year_id";
	//else if($db_type==2) $year_field="and to_char(insert_date,'YYYY')";

	/*if($db_type==0)
	{
		if($year_id==0)$year_cond=""; else $year_cond="and year(insert_date)='$year_id'";
	}
	else if($db_type==2)
	{
		if($year_id==0)$year_cond=""; else $year_cond="and to_char(insert_date,'YYYY')='$year_id'";
	}
	else $year_cond="";*/

	//if($year_id!=0) $year_cond="$year_field='$year_id'"; else $year_cond="";
	//if($month_id!=0) $month_cond="$month_field_by=$month_id"; else $month_cond="";
	
	if($search_type_main==1 || $search_type_main==2)
	{
		$arr=array (0=>$company_arr,1=>$buyer_arr);
		$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field_by from wo_po_details_master where status_active=1 and is_deleted=0 and company_name in($company_id) and $search_field like '$search_string' $buyer_id_cond $year_cond  order by insert_date,job_no";
	}
	else
	{
		$approved=array(0=>"No",1=>"Yes");
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
		$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
		$po_num=return_library_array( "select job_no, job_no_prefix_num from wo_po_details_master",'job_no','job_no_prefix_num');
		$arr=array (2=>$comp,3=>$buyer_arr,4=>$po_num,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved);


		$sql= "select  x.id,x.booking_no_prefix_num, x.booking_no,x.booking_date,x.company_id,x.buyer_id,x.job_no,x.item_category,x.fabric_source,x.supplier_id,x.is_approved,x.booking_type,x.insert_date from ( 

		select a.id,a.booking_no_prefix_num, a.booking_no as booking_no,a.booking_date as booking_date,a.company_id,a.buyer_id,b.job_no,a.item_category,a.fabric_source,a.supplier_id,a.is_approved,a.booking_type,a.insert_date
		from wo_booking_mst a, wo_booking_dtls b 
		where a.booking_no=b.booking_no and a.company_id in($company_id) $year_cond_2 $buyer_id_cond_2 and $search_field_2 like '$search_string' and a.booking_type in(1,4) and a.is_short in(1,2) and a.status_active=1 and a.is_deleted=0
		group by a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, b.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved,a.booking_type,a.insert_date
		union all 
		select a.id,a.booking_no_prefix_num, a.booking_no as booking_no,a.booking_date as booking_date,a.company_id,a.buyer_id,a.job_no,a.item_category,a.fabric_source,a.supplier_id,a.is_approved,a.booking_type,a.insert_date 
		from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b 
		where a.booking_no=b.booking_no and a.booking_type = 4 and a.company_id in ($company_id) $year_cond_2 $buyer_id_cond_3 and $search_field_2 like '$search_string' and a.status_active=1 and a.is_deleted=0 
		group by a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved,a.booking_type,a.insert_date )


		x group by x.id,x.booking_no_prefix_num, x.booking_no,x.booking_date,x.company_id,x.buyer_id,x.job_no,x.item_category,x.fabric_source,x.supplier_id,x.is_approved,x.booking_type,x.insert_date 
		order by x.company_id,TO_CHAR(x.insert_date,'YYYY'),x.booking_type,booking_no";



  		/*$sql= "select a.id,a.booking_no_prefix_num, a.booking_no as booking_no,a.booking_date as booking_date,a.company_id,a.buyer_id,b.job_no,a.item_category,a.fabric_source,a.supplier_id,a.is_approved,a.booking_type,a.insert_date  from wo_booking_mst a, wo_booking_dtls b  where a.booking_no=b.booking_no and a.company_id in($company_id) $year_cond_2 $buyer_id_cond_2 and $search_field_2 like '$search_string' and a.booking_type in(1,4) and a.is_short in(1,2) and  a.status_active=1  and a.is_deleted=0 group by a.id, a.booking_no_prefix_num,  a.booking_no, a.booking_date, a.company_id, a.buyer_id, b.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved,a.booking_type,a.insert_date    
  			union all 
  			select a.id,a.booking_no_prefix_num, a.booking_no as booking_no,a.booking_date as booking_date,a.company_id,a.buyer_id,a.job_no,a.item_category,a.fabric_source,a.supplier_id,a.is_approved,a.booking_type,a.insert_date from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.booking_type = 4 and a.company_id in ($company_id) $year_cond_2 $buyer_id_cond_3 and $search_field_2 like '$search_string' and  a.status_active=1  and a.is_deleted=0 group by a.id, a.booking_no_prefix_num,  a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved,a.booking_type,a.insert_date  order by a.company_id,a.insert_date,booking_type,booking_no";*/

		//$sql= "select id,booking_no_prefix_num, booking_no,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved from wo_booking_mst  where company_id in($company_id) $year_cond $buyer_id_cond and $search_field like '$search_string' and booking_type=1 and is_short in(1,2) and  status_active=1  and 	is_deleted=0 order by booking_date,booking_no";
	}
	//echo $sql;die;
	if($search_type_main==1)
	{
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","620","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	}
	else if($search_type_main==2)
	{
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","620","270",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	}
	else
	{
		echo  create_list_view("tbl_list_search", "Booking No,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved", "100,80,70,60,90,80,80,50","740","320",0, $sql , "js_set_value", "id,booking_no_prefix_num", "", 1, "0,0,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved", $arr , "booking_no,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved", '','','0,0,0,0,0,0,0,0,0','',1);
	}
	
   exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$rpt_type=str_replace("'","",$rpt_type);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_store_name=str_replace("'","",$cbo_store_id);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	if($cbo_search_by==1)
	{
		$job_no=str_replace("'","",$txt_job_no);
		$txt_job_id=str_replace("'","",$txt_job_id);
	}
	else if($cbo_search_by==2)
	{
		$job_no=str_replace("'","",$txt_job_id);
		$style_ref_id=str_replace("'","",$txt_job_id);
		$style_ref_no=str_replace("'","",$txt_job_no);
		$styleExp=explode(",", $style_ref_no);
		$styleReff="";
		foreach ($styleExp as $stylRef) {
			$styleReff.="'".$stylRef."',";
		}
		$style_ref_no=chop($styleReff,",");
	}
	else if($cbo_search_by==3)
	{
		$book_no 			= trim(str_replace("'","",$txt_job_no));
		$book_id 			= str_replace("'","",$txt_job_id);
	}

	//$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);

	//echo $cbo_company_id."*".$cbo_location_id."*".$cbo_buyer_id."*".$cbo_store_id."*".$txt_job_no."*".$txt_job_id."*".$txt_date_from."*".$txt_date_to ."*".$rpt_type;die;

	if($rpt_type==1)
	{
		if($cbo_store_name > 0){
			$store_cond = " and b.store_id in ($cbo_store_name)";
			$store_cond_2 = " and c.store_id in ($cbo_store_name)";
		}

		if($buyer_id==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and d.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and d.buyer_id=$buyer_id";
			$buyer_id_cond_1=" and a.buyer_id=$buyer_id";
			$buyer_id_cond_2=" and c.buyer_id='$buyer_id'";
			$buyer_id_cond_3=" and f.buyer_id='$buyer_id'";
			$buyer_id_cond_4=" and g.buyer_id='$buyer_id'";
		}

		$date_cond="";
		if( $date_to!="")
		{
			//if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
			//else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

			if($db_type==0)$end_date=change_date_format($date_to,"yyyy-mm-dd","");
			else if($db_type==2) $end_date=change_date_format($date_to,"","",1);

			//$date_cond   = " and b.transaction_date <= '$end_date'";
			//$date_cond_2 = " and c.transaction_date <= '$end_date'";
			$date_cond   = " and b.transaction_date <= '$end_date'";
			$date_cond_2 = " and c.transaction_date <= '$end_date'";
		
		}

		
		$company_arr 	= return_library_array("select id, company_name from lib_company where status_active=1","id","company_name");
		$supplier_arr 	= return_library_array("select id,short_name from lib_supplier where status_active=1","id","short_name");
		$buyer_arr 		= return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		$season_arr 	= return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');
		$store_arr 		= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		$color_arr 		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
		$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
		

		if($cbo_search_by==1 && $job_no!="")
		{
			if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and f.job_no_prefix_num in ($job_no) ";
			$txt_job_id =str_replace("'", "", $txt_job_id);
			if ($txt_job_id=="") $job_id_cond=""; else $job_id_cond=" and f.id in ($txt_job_id) ";
		}
		if($cbo_search_by==2 && $job_no!="")
		{
			if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and f.id in ($job_no) ";
		}
		else
		{
			if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and f.job_no_prefix_num in ($job_no) ";
			if ($book_id=="") $book_id_cond=""; else $book_id_cond=" and d.id in ($book_id) ";
		}
		
		
		if ($book_no=="") $booking_no_cond=""; else $booking_no_cond=" and d.booking_no_prefix_num in($book_no) ";
		if ($cbo_location_id==0) $location_id_cond=""; else $location_id_cond=" and f.location_name = '$cbo_location_id'";
		if ($cbo_location_id==0) $location_id_cond_2=""; else $location_id_cond_2=" and a.location_id = '$cbo_location_id'";
		if ($cbo_location_id==0) $location_id_cond_3=""; else $location_id_cond_3=" and a.to_location_id = '$cbo_location_id'";


		if($cbo_search_by==2 && $job_no=="")
		{
			if ($style_ref_id=="") $style_ref_no_cond="and f.style_ref_no in ($style_ref_no)"; else $style_ref_no_cond="";
			$job_by_style_ref = "select f.job_no_prefix_num from wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f where c.status_active=1 and e.status_active=1 and f.job_no=e.job_no_mst and c.booking_type in (1,4) and c.booking_no=d.booking_no and c.po_break_down_id=e.id and f.company_name in ($cbo_company_id) $buyer_id_cond $job_no_cond $booking_no_cond $style_ref_no_cond $location_id_cond group by  f.job_no_prefix_num";
			$job_by_style_ref_result = sql_select($job_by_style_ref);
			$jobNos="";
			foreach ($job_by_style_ref_result as $row)
			{
				$jobNos.="'".$row[csf("job_no_prefix_num")]."',";
			}
			$job_no=chop($jobNos,",");
			if ($jobNos=="") $job_no_cond=""; else $job_no_cond=" and f.job_no_prefix_num in ($job_no) ";
			
		}

		if($job_no != "" || $book_no!="" ||  $style_ref_no!="" || $buyer_id!=0 )
		{
			$serch_ref_sql_1 = "select c.booking_no from wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f where c.status_active=1 and e.status_active=1 and f.job_no=e.job_no_mst and e.job_no_mst=c.job_no and c.booking_type in (1,4) and c.booking_no=d.booking_no and c.po_break_down_id=e.id and f.company_name in ($cbo_company_id) $buyer_id_cond $job_no_cond  $job_id_cond $booking_no_cond $book_id_cond $style_ref_no_cond $location_id_cond and d.ref_closing_status=0";

			$concate="";
			if($job_no == "")
			{
				$concate = " union all ";
				$serch_ref_sql_2 = " select d.booking_no from wo_non_ord_samp_booking_mst d where d.booking_type = 4 and d.company_id in ($cbo_company_id) $booking_no_cond $book_id_cond   $buyer_id_cond and d.ref_closing_status=0";
			}
			$serch_ref_sql = $serch_ref_sql_1.$concate.$serch_ref_sql_2;
			$serch_ref_result = sql_select($serch_ref_sql);

			foreach ($serch_ref_result as $val)
			{
				$search_book_arr[$val[csf("booking_no")]] = $val[csf("booking_no")];
			}
		}
		if(!empty($search_book_arr))
		{
			$search_book_nos="'".implode("','",$search_book_arr)."'";
			$search_book_arr = explode(",", $search_book_nos);

			$all_book_nos_cond=""; $bookCond="";
			if($db_type==2 && count($search_book_arr)>999)
			{
				$all_search_book_arr_chunk=array_chunk($search_book_arr,999) ;
				foreach($all_search_book_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$bookCond.="  e.booking_no in($chunk_arr_value) or ";
				}

				$all_book_nos_cond.=" and (".chop($bookCond,'or ').")";
			}
			else
			{
				$all_book_nos_cond=" and e.booking_no in($search_book_nos)";
			}
		}
		
		$composition_arr=array();
	    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ";
	    $data_deter=sql_select($sql_deter);

	    if(count($data_deter)>0)
	    {
	    	foreach( $data_deter as $row )
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
	    			$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
	    			$constructionArr[$row[csf('id')]]=$row[csf('construction')];
	    			list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
	    			$copmpositionArr[$row[csf('id')]]=$cps;
	    		}
	    	}
	    }

		$recv_bookingSql = "SELECT  x.id,x.company_name, x.company_id,x.booking_no, x.cons_uom,x.buyer_id, x.booking_type,x.insert_date, x.body_part_id,x.fabric_description_id, x.color_id, x.gsm,x.width, x.booking_no_id, x.booking_without_order,x.receive_basis, x.knitting_source,x.knitting_company,x.wo_pi_prod_id,x.wo_pi_prod_no, x.transaction_date, x.prod_id, x.store_id,x.dia_width_type,x.po_breakdown_id, x.quantity,x.order_rate,x.pi_wo_batch_no,x.body_part_name  from ( 
		SELECT b.id,f.company_name, a.company_id,e.booking_no, b.cons_uom,g.buyer_id, g.booking_type as booking_type,TO_CHAR(g.insert_date,'YYYY') as insert_date, c.body_part_id,c.fabric_description_id, j.color as color_id, c.gsm,c.width, e.booking_no_id, a.booking_without_order,a.receive_basis, a.knitting_source,a.knitting_company,a.booking_id as wo_pi_prod_id,a.booking_no as wo_pi_prod_no, b.transaction_date, b.prod_id, b.store_id,listagg(c.dia_width_type,',') within group (order by c.dia_width_type) as dia_width_type,listagg(d.po_breakdown_id,',') within group (order by d.po_breakdown_id) as po_breakdown_id, b.cons_quantity as quantity,b.order_rate,b.pi_wo_batch_no,h.body_part_full_name as body_part_name  
		FROM inv_receive_master a, inv_transaction b,lib_company f ,wo_booking_mst g, pro_finish_fabric_rcv_dtls c ,order_wise_pro_details d ,product_details_master j, pro_batch_create_mst e, lib_body_part h 
		WHERE a.company_id in ($cbo_company_id) and a.id = b.mst_id and b.id=c.trans_id and e.booking_no=g.booking_no and c.prod_id=j.id  and d.prod_id=j.id and c.trans_id = d.trans_id  and d.entry_form=37 and d.po_breakdown_id <>0 and a.company_id=f.id and b.transaction_type=1 and a.entry_form=37 and a.status_active =1 and b.status_active =1 and c.status_active =1 and b.pi_wo_batch_no=e.id and c.body_part_id=h.id and h.status_active = 1 and h.is_deleted = 0  $store_cond $date_cond  $all_book_nos_cond  $location_id_cond_2 $buyer_id_cond_4 and  a.booking_without_order=0 and g.ref_closing_status=0 
		group by b.id,e.booking_no,e.booking_no_id, a.booking_without_order, a.company_id,a.receive_basis, a.knitting_source,a.knitting_company, a.booking_id,a.booking_no, b.transaction_date, b.prod_id, b.store_id, c.fabric_description_id,c.body_part_id, c.gsm, c.width, j.color,b.cons_uom,g.buyer_id,g.booking_type ,g.insert_date,c.dia_width_type,b.cons_quantity,b.order_rate,b.pi_wo_batch_no,f.company_name,h.body_part_full_name  
		union all 
		SELECT b.id,f.company_name, a.company_id,e.booking_no, b.cons_uom,g.buyer_id, g.booking_type as booking_type,TO_CHAR(g.insert_date,'YYYY') as insert_date, c.body_part_id,c.fabric_description_id, j.color as color_id, c.gsm,c.width, e.booking_no_id, a.booking_without_order,a.receive_basis, a.knitting_source,a.knitting_company,a.booking_id as wo_pi_prod_id,a.booking_no as wo_pi_prod_no, b.transaction_date, b.prod_id, b.store_id,listagg(c.dia_width_type,',') within group (order by c.dia_width_type) as dia_width_type,listagg(d.po_breakdown_id,',') within group (order by d.po_breakdown_id) as po_breakdown_id, b.cons_quantity as quantity,b.order_rate,b.pi_wo_batch_no,h.body_part_full_name as body_part_name  
		FROM inv_receive_master a, inv_transaction b,lib_company f ,wo_booking_mst g, pro_finish_fabric_rcv_dtls c ,order_wise_pro_details d ,product_details_master j,pro_batch_create_mst e, lib_body_part h 
		WHERE a.company_id in ($cbo_company_id) and a.id = b.mst_id and b.id=c.trans_id and e.booking_no=g.booking_no and c.prod_id=j.id  and d.prod_id=j.id and c.trans_id = d.trans_id  and d.entry_form=37 and d.po_breakdown_id <>0 and a.company_id=f.id and b.transaction_type=1 and a.entry_form=37 and a.status_active =1 and b.status_active =1 and c.status_active =1 and b.pi_wo_batch_no=e.id and c.body_part_id=h.id and h.status_active = 1 and h.is_deleted = 0  $store_cond $date_cond  $all_book_nos_cond  $location_id_cond_2 $buyer_id_cond_4 and  a.booking_without_order is NULL and g.ref_closing_status=0 
		group by b.id,e.booking_no,e.booking_no_id, a.booking_without_order, a.company_id,a.receive_basis, a.knitting_source,a.knitting_company, a.booking_id,a.booking_no, b.transaction_date, b.prod_id, b.store_id, c.fabric_description_id,c.body_part_id, c.gsm, c.width, j.color,b.cons_uom,g.buyer_id,g.booking_type ,g.insert_date,c.dia_width_type,b.cons_quantity,b.order_rate,b.pi_wo_batch_no,f.company_name,h.body_part_full_name 
		union all 

		SELECT b.id,f.company_name, a.company_id,e.booking_no, b.cons_uom,g.buyer_id,g.booking_type as booking_type,TO_CHAR(g.insert_date,'YYYY') as insert_date, c.body_part_id,c.fabric_description_id, j.color as color_id, c.gsm, 
		c.width, e.booking_no_id, a.booking_without_order,a.receive_basis, a.knitting_source,a.knitting_company,a.booking_id as wo_pi_prod_id,a.booking_no as wo_pi_prod_no, b.transaction_date, b.prod_id, b.store_id,listagg(c.dia_width_type,',') within group (order by c.dia_width_type) as dia_width_type,
		null as po_breakdown_id, b.cons_quantity as quantity,b.order_rate,b.pi_wo_batch_no,h.body_part_full_name as body_part_name  
		FROM inv_receive_master a, inv_transaction b,lib_company f ,wo_non_ord_samp_booking_mst g, pro_finish_fabric_rcv_dtls c, product_details_master j, pro_batch_create_mst e, lib_body_part h  
		WHERE a.company_id in ($cbo_company_id) and a.id = b.mst_id and b.id=c.trans_id and c.prod_id=j.id and e.booking_no=g.booking_no  and a.company_id=f.id and b.transaction_type=1 and a.entry_form=37 and a.status_active =1 and b.status_active =1 and c.status_active =1 and b.pi_wo_batch_no=e.id and c.body_part_id=h.id and h.status_active = 1 and h.is_deleted = 0  $store_cond $date_cond  $all_book_nos_cond  $location_id_cond_2 $buyer_id_cond_4 and  a.booking_without_order=1 and g.ref_closing_status=0 
		group by b.id,e.booking_no,e.booking_no_id, a.booking_without_order, a.company_id,a.receive_basis, a.knitting_source,a.knitting_company, a.booking_id, a.booking_no, b.transaction_date, b.prod_id, b.store_id, c.fabric_description_id,c.body_part_id, c.gsm, c.width, j.color,b.cons_uom,g.buyer_id,g.booking_type,g.insert_date,c.dia_width_type,b.cons_quantity,b.order_rate,b.pi_wo_batch_no,f.company_name,h.body_part_full_name 
		) x 
		group by x.id,x.company_name, x.company_id,x.booking_no, x.cons_uom,x.buyer_id, x.booking_type,x.insert_date, x.body_part_id,x.fabric_description_id, x.color_id, x.gsm, 
		x.width, x.booking_no_id, x.booking_without_order,x.receive_basis, x.knitting_source,x.knitting_company,x.wo_pi_prod_id,x.wo_pi_prod_no, x.transaction_date, x.prod_id, x.store_id,x.dia_width_type,
		x.po_breakdown_id, x.quantity,x.order_rate,x.pi_wo_batch_no,x.body_part_name  
		order by x.company_name,x.insert_date,x.booking_type,x.booking_no,x.cons_uom,x.body_part_name";


		$recv_booking_data = sql_select($recv_bookingSql);$recv_data_array=array();
		foreach ($recv_booking_data as  $val)
		{
			if($val[csf("width")]==""){$val[csf("width")]=0;}
			if($val[csf("wo_pi_prod_no")]==""){$val[csf("wo_pi_prod_no")]=0;}
			if($val[csf("order_rate")]==""){$val[csf("order_rate")]=0;}
			if($val[csf("gsm")]==""){$val[csf("gsm")]=0;}
			if($val[csf("pi_wo_batch_no")]==""){$val[csf("pi_wo_batch_no")]=0;}
			if($val[csf("cons_uom")]==""){$val[csf("cons_uom")]=0;}
			if($val[csf("body_part_id")]==""){$val[csf("body_part_id")]=0;}
			if($val[csf("fabric_description_id")]==""){$val[csf("fabric_description_id")]=0;}
			if($val[csf("color_id")]==""){$val[csf("color_id")]=0;}

			$recv_book_arr[$val[csf("booking_no")]] = $val[csf("booking_no")];
			//$recv_qnty_array[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$val[csf("body_part_id")]][$val[csf("fabric_description_id")]][$val[csf("color_id")]][$val[csf("gsm")]][$val[csf("width")]]['receive_qnty'] += $val[csf("quantity")];
			$recv_qnty_array[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("fabric_description_id")]][$val[csf("color_id")]]['receive_qnty'] += $val[csf("quantity")];
			$recv_bookingWithoutOrder[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("fabric_description_id")]][$val[csf("color_id")]]['booking_without_order'] = $val[csf("booking_without_order")];


			
			$bookingDataArr[$val[csf("cons_uom")]][$val[csf("company_id")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("fabric_description_id")]][$val[csf("color_id")]]['buyer_id']=$val[csf("buyer_id")];
			$bookingDataArr[$val[csf("cons_uom")]][$val[csf("company_id")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("fabric_description_id")]][$val[csf("color_id")]]['booking_qnty']=$val[csf("booking_qnty")];
			$bookingDataArr[$val[csf("cons_uom")]][$val[csf("company_id")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("fabric_description_id")]][$val[csf("color_id")]]['body_part_id']=$val[csf("body_part_id")];


			$mainArr[$val[csf("cons_uom")]][$val[csf("company_id")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("fabric_description_id")]][$val[csf("color_id")]]['booking_no']=$val[csf("booking_no")];
			$mainArr[$val[csf("cons_uom")]][$val[csf("company_id")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("fabric_description_id")]][$val[csf("color_id")]]['body_part']=$val[csf("body_part_id")];
			$mainArr[$val[csf("cons_uom")]][$val[csf("company_id")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("fabric_description_id")]][$val[csf("color_id")]]['fabrication_id']=$val[csf("fabric_description_id")];
			$mainArr[$val[csf("cons_uom")]][$val[csf("company_id")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("fabric_description_id")]][$val[csf("color_id")]]['color_id']=$val[csf("color_id")];
			$mainArr[$val[csf("cons_uom")]][$val[csf("company_id")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("fabric_description_id")]][$val[csf("color_id")]]['cons_uom']=$val[csf("cons_uom")];


			if($val[csf("booking_without_order")] == 0)
			{
				$all_po_id_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
				$po_array[$val[csf("booking_no")]][$ref_str]["po_no"] .= $val[csf("po_breakdown_id")].",";
			}

			if($val[csf("booking_without_order")] == 1)
			{
				$all_samp_book_arr[$val[csf("booking_no_id")]] = $val[csf("booking_no_id")];
			}
			$booking_no_arr[$val[csf("booking_no")]] = "'".$val[csf("booking_no")]."'";
			$batch_id_arr[$val[csf("pi_wo_batch_no")]] = $val[csf("pi_wo_batch_no")];
		}
		/*echo "<pre>";
		print_r($bookingDataArr);
		echo "</pre>";*/
		if(!empty($recv_book_arr))
		{
			$recv_book_nos="'".implode("','",$recv_book_arr)."'";
			$recv_book_arr = explode(",", $recv_book_nos);

			$all_recv_book_nos_cond=""; $bookCond="";
			if($db_type==2 && count($recv_book_arr)>999)
			{
				$all_recv_book_arr_chunk=array_chunk($recv_book_arr,999) ;
				foreach($all_recv_book_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$bookCond.="  c.booking_no in($chunk_arr_value) or ";
				}

				$all_recv_book_nos_cond.=" and (".chop($bookCond,'or ').")";
			}
			else
			{
				$all_recv_book_nos_cond=" and c.booking_no in($recv_book_nos)";
			}
		}

		$trans_in_sql = "SELECT x.company_id, x.company_name,x.id,x.transaction_date, x.pi_wo_batch_no, x.batch_no,x.booking_no, x.booking_no_id, x.booking_without_order, x.body_part_id, x.prod_id,x.store_id,x.detarmination_id, x.gsm, x.width, x.color_id, x.cons_uom,
		x.quantity,x.order_rate,x.po_breakdown_id,x.company_name, x.booking_type, x.insert_date,x.buyer_id,x.body_part_name from(
		SELECT c.company_id,g.company_name,a.id,c.transaction_date, c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id,c.store_id, d.detarmination_id, d.gsm, d.dia_width as width, d.color as color_id, c.cons_uom,sum(c.cons_quantity) as quantity,0 as order_rate, listagg(f.po_breakdown_id,',') within group (order by f.po_breakdown_id) as po_breakdown_id, h.booking_type as booking_type,TO_CHAR(h.insert_date,'YYYY') as insert_date,h.buyer_id,j.body_part_full_name as body_part_name   
		from inv_item_transfer_mst a, inv_item_transfer_dtls b,lib_company g, inv_transaction c,order_wise_pro_details f, product_details_master d, pro_batch_create_mst e,wo_booking_mst h, lib_body_part j  
		where a.id=b.mst_id and b.to_trans_id=c.id and c.prod_id=d.id and  c.id = f.trans_id and f.trans_type = 5 and f.status_active=1 and f.po_breakdown_id<>0 and c.pi_wo_batch_no=e.id and c.company_id=g.id and e.booking_no=h.booking_no and c.body_part_id=j.id and j.status_active = 1 and j.is_deleted = 0  and c.company_id in ($cbo_company_id) and c.item_category=2 and c.transaction_type=5 
		and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306) $store_cond_2 $date_cond_2 $all_book_nos_cond $location_id_cond_3 and a.transfer_criteria in(1,2,4,7)  and h.ref_closing_status=0   
		group by a.id,c.transaction_date, c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.company_id,g.company_name, c.body_part_id, c.prod_id,c.store_id, d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.order_rate,h.booking_type,h.insert_date,h.buyer_id,j.body_part_full_name   

		union all 
		SELECT c.company_id,g.company_name,a.id,c.transaction_date, c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id,c.store_id, d.detarmination_id, d.gsm, d.dia_width as width, d.color as color_id, c.cons_uom,sum(c.cons_quantity) as quantity,0 as order_rate,null as po_breakdown_id, h.booking_type as booking_type,TO_CHAR(h.insert_date,'YYYY') as insert_date,h.buyer_id,j.body_part_full_name as body_part_name   
		from inv_item_transfer_mst a, inv_item_transfer_dtls b,lib_company g, inv_transaction c, product_details_master d, pro_batch_create_mst e,wo_booking_mst h, lib_body_part j  
		where a.id=b.mst_id and b.to_trans_id=c.id and c.prod_id=d.id  and c.pi_wo_batch_no=e.id and c.company_id=g.id and e.booking_no=h.booking_no and c.body_part_id=j.id and j.status_active = 1 and j.is_deleted = 0  and c.company_id in ($cbo_company_id) and c.item_category=2 and c.transaction_type=5 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (306) $store_cond_2 $date_cond_2 $all_book_nos_cond $location_id_cond_3 and a.transfer_criteria in(6,8)  and h.ref_closing_status=0  
		group by a.id,c.transaction_date, c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.company_id,g.company_name, c.body_part_id, c.prod_id,c.store_id, d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.order_rate , h.booking_type,h.insert_date,h.buyer_id,j.body_part_full_name  
		)x 
		group by x.company_id, x.company_name,x.id,x.transaction_date, x.pi_wo_batch_no, x.batch_no,x.booking_no, x.booking_no_id, x.booking_without_order, x.body_part_id, x.prod_id,x.store_id,x.detarmination_id, x.gsm, x.width, x.color_id, x.cons_uom,x.quantity,x.order_rate,x.po_breakdown_id,x.company_name, x.booking_type, x.insert_date,x.buyer_id,x.body_part_name  
		order by x.company_name,x.insert_date,x.booking_type,x.booking_no,x.cons_uom,x.body_part_name";


		//echo $trans_in_sql;

		$trans_in_data = sql_select($trans_in_sql);
		foreach ($trans_in_data as  $val)
		{

			if($val[csf("order_rate")]==""){$val[csf("order_rate")]=0;}
			if($val[csf("gsm")]==""){$val[csf("gsm")]=0;}
			if($val[csf("width")]==""){$val[csf("width")]=0;}
			if($val[csf("pi_wo_batch_no")]==""){$val[csf("pi_wo_batch_no")]=0;}
			if($val[csf("cons_uom")]==""){$val[csf("cons_uom")]=0;}
			if($val[csf("body_part_id")]==""){$val[csf("body_part_id")]=0;}
			if($val[csf("detarmination_id")]==""){$val[csf("detarmination_id")]=0;}
			if($val[csf("color_id")]==""){$val[csf("color_id")]=0;}


			$trans_in_book_arr[$val[csf("booking_no")]] = $val[csf("booking_no")];
			//$recv_book_arr[$val[csf("booking_no")]] = $val[csf("booking_no")];
			//$trans_in_qnty_array[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$val[csf("body_part_id")]][$val[csf("detarmination_id")]][$val[csf("color_id")]][$val[csf("gsm")]][$val[csf("width")]]['trans_in_qnty'] += $val[csf("quantity")];
			$trans_in_qnty_array[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color_id")]]['trans_in_qnty'] += $val[csf("quantity")];

			//$mainArr[$val[csf("cons_uom")]][$val[csf("company_id")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color_id")]]['trans_in_qnty']=$val[csf("quantity")];


			$bookingDataArr[$val[csf("cons_uom")]][$val[csf("company_id")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color_id")]]['buyer_id']=$val[csf("buyer_id")];
			$bookingDataArr[$val[csf("cons_uom")]][$val[csf("company_id")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color_id")]]['booking_qnty']=$val[csf("booking_qnty")];
			$bookingDataArr[$val[csf("cons_uom")]][$val[csf("company_id")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color_id")]]['body_part_id']=$val[csf("body_part_id")];

			$mainArr[$val[csf("cons_uom")]][$val[csf("company_id")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color_id")]]['booking_no']=$val[csf("booking_no")];
			$mainArr[$val[csf("cons_uom")]][$val[csf("company_id")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color_id")]]['body_part']=$val[csf("body_part_id")];
			$mainArr[$val[csf("cons_uom")]][$val[csf("company_id")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color_id")]]['fabrication_id']=$val[csf("detarmination_id")];
			$mainArr[$val[csf("cons_uom")]][$val[csf("company_id")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color_id")]]['color_id']=$val[csf("color_id")];
			$mainArr[$val[csf("cons_uom")]][$val[csf("company_id")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color_id")]]['cons_uom']=$val[csf("cons_uom")];


			if($val[csf("booking_without_order")] == 0)
			{
				$all_po_id_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
				$po_array[$val[csf("booking_no")]][$ref_str]["po_no"] .= $val[csf("po_breakdown_id")].",";
			}

			if($val[csf("booking_without_order")] == 1)
			{
				$all_samp_book_arr[$val[csf("booking_no_id")]] = $val[csf("booking_no_id")];
			}
			$booking_no_arr[$val[csf("booking_no")]] = "'".$val[csf("booking_no")]."'";
			$batch_id_arr[$val[csf("pi_wo_batch_no")]] = $val[csf("pi_wo_batch_no")];

			//$trans_in_id_arr[$val[csf("booking_no")]][$val[csf("body_part_id")]][$val[csf("color_id")]][$val[csf("detarmination_id")]][$val[csf("gsm")]][$val[csf("width")]][$val[csf("cons_uom")]]['trans_in_id'].=$val[csf("id")].",";
			$trans_in_id_arr[$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("color_id")]][$val[csf("detarmination_id")]][$val[csf("cons_uom")]]['trans_in_id'].=$val[csf("id")].",";

		}
		//print_r($trans_in_qnty_array);
		
		if(!empty($trans_in_book_arr))
		{
			$trans_in_book_nos="'".implode("','",$trans_in_book_arr)."'";
			$trans_in_book_arr = explode(",", $trans_in_book_nos);
			$all_booking_arr=$recv_book_arr+$trans_in_book_arr;
			//$all_booking_arrx = explode(",", $all_booking_arr);

			//print_r($all_booking_arr);

			$all_recv_book_nos_cond=""; $bookCond="";
			if($db_type==2 && count($all_booking_arr)>999)
			{
				$all_recv_book_arr_chunk=array_chunk($all_booking_arr,999) ;
				foreach($all_recv_book_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$bookCond.="  c.booking_no in($chunk_arr_value) or ";
				}

				$all_recv_book_nos_cond.=" and (".chop($bookCond,'or ').")";
			}
			else
			{
				$all_recv_book_nos_cond=" and c.booking_no in($recv_book_nos)";
			}
		}

		if($all_recv_book_nos_cond!="" || $all_trans_in_book_nos_cond!="")
		{
			
			$booking_main_qry_sql = sql_select("SELECT x.body_part,x.body_part_name,x.fabrication_id,x.company_name,x.buyer_id, x.booking_no,x.cons_uom,x.color_id,x.gsm,x.dia,x.booking_type,x.booking_qnty,x.insert_date   
			from (
			SELECT b.body_part_id as body_part, g.body_part_full_name as body_part_name,b.lib_yarn_count_deter_id as fabrication_id,f.company_id as company_name,f.buyer_id, c.booking_no as booking_no,b.uom as cons_uom,c.fabric_color_id as color_id ,b.gsm_weight as gsm,c.dia_width as dia , f.booking_type as booking_type,sum(c.fin_fab_qnty) as booking_qnty,TO_CHAR(f.insert_date,'YYYY') as insert_date   
			from wo_pre_cost_fabric_cost_dtls b,wo_booking_dtls c,wo_booking_mst f, lib_body_part g 
			where f.booking_no=c.booking_no and  b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0  and c.status_active = 1 and b.is_deleted = 0 and c.pre_cost_fabric_cost_dtls_id = b.id  and c.booking_type in(1,4) and f.company_id in ($cbo_company_id) $buyer_id_cond_3 $all_recv_book_nos_cond  $all_trans_in_book_nos_cond and f.ref_closing_status=0 and b.body_part_id=g.id and g.status_active = 1 and g.is_deleted = 0 
			group by b.body_part_id, g.body_part_full_name,b.lib_yarn_count_deter_id,f.company_id,f.buyer_id, c.booking_no,b.uom,c.fabric_color_id,b.gsm_weight,c.dia_width , f.booking_type,f.insert_date
			union all 
			select b.body_part_id as body_part, g.body_part_full_name as body_part_name,b.lib_yarn_count_deter_id as fabrication_id,f.company_id as company_name,f.buyer_id, c.booking_no as booking_no,b.uom as cons_uom,c.fabric_color_id as color_id ,b.gsm_weight as gsm,c.dia_width as dia , f.booking_type as booking_type,sum(c.fin_fab_qnty) as booking_qnty,TO_CHAR(f.insert_date,'YYYY') as insert_date    
			from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c,wo_booking_mst f, lib_body_part g 
			where f.booking_no=c.booking_no and b.job_no=c.job_no and a.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.pre_cost_fabric_cost_dtls_id = a.id and a.fabric_description = b.id and c.booking_type = 3 and f.company_id in ($cbo_company_id) $buyer_id_cond_3 $all_recv_book_nos_cond $all_trans_in_book_nos_cond and f.ref_closing_status=0 and b.body_part_id=g.id and g.status_active = 1 and g.is_deleted = 0 
			group by b.body_part_id, g.body_part_full_name,b.lib_yarn_count_deter_id,f.company_id,f.buyer_id, c.booking_no,b.uom,c.fabric_color_id ,b.gsm_weight,c.dia_width, f.booking_type,f.insert_date 
			union all 
			select c.body_part as body_part, g.body_part_full_name as body_part_name,c.lib_yarn_count_deter_id as fabrication_id,f.company_id as company_name,f.buyer_id, f.booking_no as booking_no,c.uom as cons_uom,c.fabric_color as color_id ,c.gsm_weight as gsm,c.dia_width as dia, f.booking_type as booking_type,sum(c.finish_fabric) as booking_qnty,TO_CHAR(f.insert_date,'YYYY') as insert_date  
			from wo_non_ord_samp_booking_mst f, wo_non_ord_samp_booking_dtls c, lib_body_part g 
			where f.booking_no=c.booking_no and f.booking_type = 4 and f.company_id in ($cbo_company_id) $buyer_id_cond_3 $all_recv_book_nos_cond $all_trans_in_book_nos_cond and c.is_deleted=0 and c.status_active=1 and f.is_deleted=0 and f.status_active=1 and f.ref_closing_status=0  and c.body_part=g.id and g.status_active = 1 and g.is_deleted = 0
			group by c.body_part, g.body_part_full_name,c.lib_yarn_count_deter_id,f.company_id,f.buyer_id, f.booking_no,c.uom,c.fabric_color,c.gsm_weight,c.dia_width, f.booking_type,f.insert_date  
			)  x 
			group by x.body_part,x.body_part_name,x.fabrication_id,x.company_name,x.buyer_id, x.booking_no,x.cons_uom,x.color_id,x.gsm,x.dia, x.booking_type,x.booking_qnty,x.insert_date   
			order by x.company_name,x.insert_date,x.booking_type,x.booking_no,x.cons_uom, x.body_part_name");
			
			//$buyer_id_cond $job_no_cond  $job_id_cond $booking_no_cond $book_id_cond $style_ref_no_cond $location_id_cond


			foreach ($booking_main_qry_sql as $val)
			{
				$bookingDataArr[$val[csf("cons_uom")]][$val[csf("company_name")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part")]]][$val[csf("fabrication_id")]][$val[csf("color_id")]]['buyer_id']=$val[csf("buyer_id")];
				$bookingDataArr[$val[csf("cons_uom")]][$val[csf("company_name")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part")]]][$val[csf("fabrication_id")]][$val[csf("color_id")]]['booking_qnty']+=$val[csf("booking_qnty")];
				$bookingDataArr[$val[csf("cons_uom")]][$val[csf("company_name")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part")]]][$val[csf("fabrication_id")]][$val[csf("color_id")]]['body_part_id']=$val[csf("body_part")];

				$mainArr[$val[csf("cons_uom")]][$val[csf("company_name")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part")]]][$val[csf("fabrication_id")]][$val[csf("color_id")]]['booking_no']=$val[csf("booking_no")];
				$mainArr[$val[csf("cons_uom")]][$val[csf("company_name")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part")]]][$val[csf("fabrication_id")]][$val[csf("color_id")]]['body_part']=$val[csf("body_part")];
				$mainArr[$val[csf("cons_uom")]][$val[csf("company_name")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part")]]][$val[csf("fabrication_id")]][$val[csf("color_id")]]['fabrication_id']=$val[csf("fabrication_id")];
				$mainArr[$val[csf("cons_uom")]][$val[csf("company_name")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part")]]][$val[csf("fabrication_id")]][$val[csf("color_id")]]['color_id']=$val[csf("color_id")];
				$mainArr[$val[csf("cons_uom")]][$val[csf("company_name")]][$val[csf("insert_date")]][$val[csf("booking_type")]][$val[csf("booking_no")]][$body_part[$val[csf("body_part")]]][$val[csf("fabrication_id")]][$val[csf("color_id")]]['cons_uom']=$val[csf("cons_uom")];
				
			}
		}
		/*echo "<pre>";
		print_r($mainArr);
		echo "</pre>";*/
		$all_po_id_arr = array_filter($all_po_id_arr);
		$all_po_id_arr = array_unique(explode(",",implode(",", $all_po_id_arr)));

		if(!empty($all_po_id_arr))
		{
			$all_po_ids=implode(",",$all_po_id_arr);
			$all_po_id_cond=""; $poCond="";
			$all_po_id_cond_2=""; $poCond_2="";
			if($db_type==2 && count($all_po_id_arr)>999)
			{
				$all_po_id_arr_chunk=array_chunk($all_po_id_arr,999) ;
				foreach($all_po_id_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$poCond.="  e.id in($chunk_arr_value) or ";
					$poCond_2.="  b.po_break_down_id in($chunk_arr_value) or ";
				}

				$all_po_id_cond.=" and (".chop($poCond,'or ').")";
				$all_po_id_cond_2.=" and (".chop($poCond_2,'or ').")";
			}
			else
			{
				$all_po_id_cond=" and e.id in($all_po_ids)";
				$all_po_id_cond_2=" and b.po_break_down_id in($all_po_ids)";
			}

			$booking_sql = sql_select("SELECT a.body_part_id,c.booking_no,a.lib_yarn_count_deter_id,a.color_size_sensitive,c.fabric_color_id,  c.gmts_color_id, c.color_type, d.booking_date, d.pay_mode, d.booking_type, d.entry_form, d.is_short,f.company_name, f.job_no, f.style_ref_no, f.buyer_name, f.client_id, f.season_buyer_wise, f.total_set_qnty, f.job_quantity, c.fin_fab_qnty, a.uom, c.rate, d.supplier_id
			from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f
			where a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and e.status_active=1 and a.id = c.pre_cost_fabric_cost_dtls_id and  f.job_no = e.job_no_mst and c.booking_type =1 and c.booking_no = d.booking_no and c.po_break_down_id = e.id $all_po_id_cond $buyer_id_cond  and d.ref_closing_status=0 
			union all
			select a.body_part_id,c.booking_no,a.lib_yarn_count_deter_id,a.color_size_sensitive,c.fabric_color_id,c.gmts_color_id,c.color_type, d.booking_date, d.pay_mode, d.booking_type, d.entry_form, d.is_short,f.company_name,
			f.job_no, f.style_ref_no, f.buyer_name, f.client_id, f.season_buyer_wise,f.total_set_qnty, f.job_quantity, c.fin_fab_qnty, a.uom, c.rate, d.supplier_id 
			from wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls c , wo_booking_mst d , wo_po_break_down e, wo_po_details_master f
			where a.job_no=c.job_no and a.status_active=1 and c.status_active=1 and e.status_active=1 and a.id = c.pre_cost_fabric_cost_dtls_id and f.job_no = e.job_no_mst  and c.booking_type in(3,4) and c.booking_no = d.booking_no  and c.po_break_down_id = e.id $all_po_id_cond $buyer_id_cond  and d.ref_closing_status=0 ");

			foreach ($booking_sql as  $val)
			{
				if ($val[csf("color_size_sensitive")]==1) 
				{
					$bookingColor=$val[csf("gmts_color_id")];
				}
				elseif($val[csf("color_size_sensitive")]==3)
				{
					$bookingColor=$val[csf("fabric_color_id")];
				}

				$book_po_ref[$val[csf("booking_no")]]["company_name"] 	= $val[csf("company_name")];
				$book_po_ref[$val[csf("booking_no")]]["buyer_name"] 	= $val[csf("buyer_name")];
				$book_po_ref[$val[csf("booking_no")]]["job_no"] 		.= $val[csf("job_no")].",";
				$book_po_ref[$val[csf("booking_no")]]["client_id"] 		= $val[csf("client_id")];
				$book_po_ref[$val[csf("booking_no")]]["season"] 		.= $val[csf("season_buyer_wise")].",";
				$book_po_ref[$val[csf("booking_no")]]["style_ref_no"] 	.= $val[csf("style_ref_no")].",";
				$book_po_ref[$val[csf("booking_no")]]["booking_no"] 	= $val[csf("booking_no")];
				$book_po_ref[$val[csf("booking_no")]]["booking_date"] 	= $val[csf("booking_date")];
				$book_po_ref[$val[csf("booking_no")]]["pay_mode"] 		= $pay_mode[$val[csf("pay_mode")]];
				if($val[csf("pay_mode")] == 3 || $val[csf("pay_mode")] == 5)
				{
					$book_po_ref[$val[csf("booking_no")]]["supplier"] = $company_arr[$val[csf("supplier_id")]];
				}else{
					$book_po_ref[$val[csf("booking_no")]]["supplier"] = $supplier_arr[$val[csf("supplier_id")]];
				}

				$job_qnty_arr[$val[csf("job_no")]]["qnty"] = $val[csf("job_quantity")]*$val[csf("total_set_qnty")];
				$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$bookingColor]["qnty"] += $val[csf("fin_fab_qnty")];
				$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$bookingColor]["color_type"] .= $color_type[$val[csf("color_type")]].",";

				$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$bookingColor]["amount"] += $val[csf("fin_fab_qnty")]*$val[csf("rate")];

				$bookingType="";
				if($val[csf('booking_type')] == 4)
				{
					$bookingType = "Sample With Order";
				}
				else
				{
					$bookingType = $booking_type_arr[$val[csf('entry_form')]];
				}
				$book_po_ref[$val[csf("booking_no")]]["booking_type"] = $bookingType;
			}
		}
		/*echo "<pre>";
		print_r($book_po_ref);*/

		$all_samp_book_ids = implode(",", $all_samp_book_arr);
		$non_samp_sql = sql_select("select a.booking_date, a.booking_no, a.pay_mode, a.company_id, a.supplier_id, b.lib_yarn_count_deter_id, b.gmts_color,b.uom, b.color_type_id, b.body_part, a.buyer_id, b.style_des from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no = b.booking_no and b.status_active =1 and a.booking_type = 4 and a.id in ($all_samp_book_ids)");

		foreach ($non_samp_sql as  $val)
		{
			$book_po_ref[$val[csf("booking_no")]]["booking_no"]   	= $val[csf("booking_no")];
			$book_po_ref[$val[csf("booking_no")]]["booking_date"]  	= $val[csf("booking_date")];
			$book_po_ref[$val[csf("booking_no")]]["company_name"] 	= $val[csf("company_id")];
			$book_po_ref[$val[csf("booking_no")]]["buyer_name"] 	= $val[csf("buyer_id")];
			$book_po_ref[$val[csf("booking_no")]]["style_ref_no"] 	= $val[csf("style_des")];
			$book_po_ref[$val[csf("booking_no")]]["booking_type"] 	= "Sample WithOut Order";
			if($val[csf("pay_mode")] == 3 || $val[csf("pay_mode")] 	== 5)
			{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $company_arr[$val[csf("supplier_id")]];
			}else{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $supplier_arr[$val[csf("supplier_id")]];
			}
		}
		$batch_id_arr = array_filter($batch_id_arr);
		if(!empty($batch_id_arr))
		{
			$batch_ids= implode(",",$batch_id_arr);

			$all_batch_ids_cond=""; $batchCond="";
			if($db_type==2 && count($batch_id_arr)>999)
			{
				$batch_id_arr_chunk=array_chunk($batch_id_arr,999) ;
				foreach($batch_id_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$batchCond.="  e.id in($chunk_arr_value) or ";
				}
				$all_batch_ids_cond.=" and (".chop($batchCond,'or ').")";
			}
			else
			{
				$all_batch_ids_cond=" and e.id in($batch_ids)";
			}
		}
		$issRtnSql = "select a.id,c.company_id,c.transaction_date, d.knit_dye_source, b.body_part_id, b.prod_id,c.store_id, b.fabric_description_id, b.gsm, b.width, b.color_id,c.cons_uom, c.cons_quantity as quantity, c.order_rate, b.batch_id, e.batch_no, e.booking_no, e.booking_without_order from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e where a.id = b.mst_id and b.trans_id = c.id and c.issue_id = d.id and a.entry_form = 52 and a.item_category = 2 and c.pi_wo_batch_no = e.id and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in  ($cbo_company_id) $store_cond_2 $date_cond_2 $all_batch_ids_cond  order by e.booking_no";
		//echo $issRtnSql;
		$issRtnData = sql_select($issRtnSql);
		foreach ($issRtnData as $val)
		{
			
			//$issRtnRef_str = $val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$constructionArr[$val[csf("fabric_description_id")]]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")];
			$issRtnRef_str = $val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$constructionArr[$val[csf("fabric_description_id")]]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")];
			

			$date_frm=date('Y-m-d',strtotime($end_date));
			$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
			$ref_str="";
			/*if($transaction_date <= $date_frm)
			{*/
				if($val[csf("knit_dye_source")] == 1)
				{
					//$issue_return_data[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$val[csf("body_part_id")]][$val[csf("fabric_description_id")]][$val[csf("color_id")]][$val[csf("gsm")]][$val[csf("width")]]['inside_return']+=$val[csf("quantity")];
					//$issue_return_data[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$val[csf("body_part_id")]][$val[csf("fabric_description_id")]][$val[csf("color_id")]][$val[csf("gsm")]][$val[csf("width")]]['inside_return_amount']+=$val[csf("quantity")]*$val[csf("order_rate")];

					$issue_return_data[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("fabric_description_id")]][$val[csf("color_id")]]['inside_return']+=$val[csf("quantity")];
					$issue_return_data[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("fabric_description_id")]][$val[csf("color_id")]]['inside_return_amount']+=$val[csf("quantity")]*$val[csf("order_rate")];


					//$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["inside_return"] += $val[csf("quantity")];
					//$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["inside_return_amount"] += $val[csf("quantity")]*$val[csf("order_rate")];
				}
				else
				{
					//$issue_return_data[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$val[csf("body_part_id")]][$val[csf("fabric_description_id")]][$val[csf("color_id")]][$val[csf("gsm")]][$val[csf("width")]]['outside_return']+=$val[csf("quantity")];
					//$issue_return_data[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$val[csf("body_part_id")]][$val[csf("fabric_description_id")]][$val[csf("color_id")]][$val[csf("gsm")]][$val[csf("width")]]['outside_return_amount']+=$val[csf("quantity")]*$val[csf("order_rate")];
					$issue_return_data[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("fabric_description_id")]][$val[csf("color_id")]]['outside_return']+=$val[csf("quantity")];
					$issue_return_data[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("fabric_description_id")]][$val[csf("color_id")]]['outside_return_amount']+=$val[csf("quantity")]*$val[csf("order_rate")];

					//$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["outside_return"] += $val[csf("quantity")];
					//$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["outside_return_amount"] += $val[csf("quantity")]*$val[csf("order_rate")];
				}
			/*}
			else
			{
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["opening"] += $val[csf("quantity")];
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["opening_amount"] +=$val[csf("quantity")]*$val[csf("order_rate")];
			}*/
			$issue_return_id_arr[$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("color_id")]][$val[csf("fabric_description_id")]][$val[csf("cons_uom")]]['issue_return_id'].=$val[csf("id")].",";
		}

		$issue_sql = sql_select(
			"SELECT c.company_id,a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, sum(c.cons_quantity) as cons_quantity, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, 0 as booking_without_order, round(c.order_rate,2) as order_rate,listagg(e.id,',') within group (order by e.id) as batch_id  from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, wo_booking_mst f  where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and e.booking_no=f.booking_no and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 $all_batch_ids_cond $location_id_cond_2 $buyer_id_cond_3 and a.entry_form=18 and c.status_active=1 and b.status_active=1 and a.status_active=1 and c.item_category =2 and c.transaction_type =2 group by c.company_id,a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, round(c.order_rate,2)
			union all
			select c.company_id,a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, sum(c.cons_quantity) as cons_quantity, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, 1 as booking_without_order, round(c.order_rate,2) as order_rate,listagg(e.id,',') within group (order by e.id) as batch_id  from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, wo_non_ord_samp_booking_mst f  where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and e.booking_no=f.booking_no and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 $all_batch_ids_cond $location_id_cond_2 $buyer_id_cond_3 and a.entry_form=18 and c.status_active=1 and b.status_active=1 and a.status_active=1 and c.item_category =2 and c.transaction_type =2 group by c.company_id,a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, round(c.order_rate,2)
			"
			);

		$batchIds_arr=array();
		foreach ($issue_sql as $val)
		{
			$issRef_str="";
		
			//$issRef_str = $val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$constructionArr[$val[csf("detarmination_id")]]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")];
			$issRef_str = $val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$constructionArr[$val[csf("detarmination_id")]]."*".$val[csf("color")]."*".$val[csf("cons_uom")];



			$date_frm=date('Y-m-d',strtotime($end_date));
			$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
			
			$batchIds_arr[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color")]]['batch_id'].=$val[csf("batch_id")].",";

			
			/*if($transaction_date <= $date_frm)
			{*/
				if($val[csf("issue_purpose")] == 9)
				{
					if($val[csf("knit_dye_source")] == 1)
					{
						//$issue_qnty_array[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$val[csf("body_part_id")]][$val[csf("detarmination_id")]][$val[csf("color")]][$val[csf("gsm")]][$val[csf("dia_width")]]['cutting_inside']+=$val[csf("cons_quantity")];
						$issue_qnty_array[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color")]]['cutting_inside']+=$val[csf("cons_quantity")];
						$recv_bookingWithoutOrder[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color")]]['booking_without_order']=$val[csf("booking_without_order")];
						//$batchIds_arr[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color")]]['batch_id'].=$val[csf("batch_id")].",";

						//$issue_data[$val[csf("booking_no")]][$issRef_str]["cutting_inside"] += $val[csf("cons_quantity")];
					}
					else
					{
						//$issue_qnty_array[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$val[csf("body_part_id")]][$val[csf("detarmination_id")]][$val[csf("color")]][$val[csf("gsm")]][$val[csf("dia_width")]]['cutting_outside']+=$val[csf("cons_quantity")];
						$issue_qnty_array[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color")]]['cutting_outside']+=$val[csf("cons_quantity")];
						//$issue_data[$val[csf("booking_no")]][$issRef_str]["cutting_outside"] += $val[csf("cons_quantity")];
						$recv_bookingWithoutOrder[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color")]]['booking_without_order']=$val[csf("booking_without_order")];
						//$batchIds_arr[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color")]]['batch_id'].=$val[csf("batch_id")].",";


					}
				}
				else
				{
					//$issue_qnty_array[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$val[csf("body_part_id")]][$val[csf("detarmination_id")]][$val[csf("color")]][$val[csf("gsm")]][$val[csf("dia_width")]]['other_issue']+=$val[csf("cons_quantity")];
					$issue_qnty_array[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color")]]['other_issue']+=$val[csf("cons_quantity")];
					$recv_bookingWithoutOrder[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color")]]['booking_without_order']=$val[csf("booking_without_order")];
					//$batchIds_arr[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color")]]['batch_id'].=$val[csf("batch_id")].",";
					//$issue_data[$val[csf("booking_no")]][$issRef_str]["other_issue"] += $val[csf("cons_quantity")];

				}
				//$issue_qnty_array[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$val[csf("body_part_id")]][$val[csf("detarmination_id")]][$val[csf("color")]][$val[csf("gsm")]][$val[csf("dia_width")]]['issue_amount']+= $val[csf("cons_quantity")]*$val[csf("order_rate")];
				$issue_qnty_array[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color")]]['issue_amount']+= $val[csf("cons_quantity")]*$val[csf("order_rate")];
				$recv_bookingWithoutOrder[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color")]]['booking_without_order']= $val[csf("booking_without_order")];
				//$batchIds_arr[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color")]]['batch_id'].= $val[csf("batch_id")].",";
				//$issue_data[$val[csf("booking_no")]][$issRef_str]["issue_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];

			/*}
			else
			{
				$issue_data[$val[csf("booking_no")]][$issRef_str]["opening_issue"] += $val[csf("cons_quantity")];
				$issue_data[$val[csf("booking_no")]][$issRef_str]["opening_issue_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
			}*/
		}
		/*echo "<pre>";
		print_r($issue_data);
		die;*/
		$rcvRtnSql = sql_select("select a.id,c.transaction_date, c.company_id, c.prod_id, c.store_id, c.cons_quantity, c.cons_uom, d.detarmination_id,d.color, d.gsm, d.dia_width, e.booking_no, e.booking_without_order, b.body_part_id from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 $all_batch_ids_cond and c.prod_id=d.id and c.pi_wo_batch_no=e.id and a.status_active =1 and b.status_active =1 and c.status_active =1 order by e.booking_no");
		foreach ($rcvRtnSql as $val)
		{		
			//$rcvRtn_str = $val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$constructionArr[$val[csf("detarmination_id")]]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")];
			$rcvRtn_str = $val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$constructionArr[$val[csf("detarmination_id")]]."*".$val[csf("color")]."*".$val[csf("cons_uom")];

			//$recv_rtn_qnty_array[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$val[csf("body_part_id")]][$val[csf("detarmination_id")]][$val[csf("color")]][$val[csf("gsm")]][$val[csf("dia_width")]]['recv_rtn_qnty']+=$val[csf("cons_quantity")];
			$recv_rtn_qnty_array[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color")]]['recv_rtn_qnty']+=$val[csf("cons_quantity")];

			$date_frm=date('Y-m-d',strtotime($end_date));
			$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
			$ref_str="";
			/*if($transaction_date <= $date_frm)
			{*/
				$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["qnty"] += $val[csf("cons_quantity")];
				$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
			/*}
			else
			{
				$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["opening_qnty"] += $val[csf("cons_quantity")];
				$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["opening_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
			}*/
			$recv_return_id_arr[$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("color")]][$val[csf("detarmination_id")]][$val[csf("cons_uom")]]['recv_return_id'].=$val[csf("id")].",";
		}
		$transOutSql = sql_select("select a.id,c.company_id,c.transaction_date,c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id, c.store_id, d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.cons_quantity,c.order_rate from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 $all_batch_ids_cond $location_id_cond_2 and c.item_category=2 and b.active_dtls_id_in_transfer=1 and c.transaction_type=6 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306) order by e.booking_no");
		foreach ($transOutSql as $val)
		{
			
			//$transOut_str = $val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$constructionArr[$val[csf("detarmination_id")]]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")];
			$transOut_str = $val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$constructionArr[$val[csf("detarmination_id")]]."*".$val[csf("color")]."*".$val[csf("cons_uom")];
			
			$date_frm=date('Y-m-d',strtotime($end_date));
			$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
			$ref_str="";
			/*if($transaction_date <= $date_frm)
			{*/

				//$trans_out_data[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$val[csf("body_part_id")]][$val[csf("detarmination_id")]][$val[csf("color")]][$val[csf("gsm")]][$val[csf("dia_width")]]['qnty']+=$val[csf("cons_quantity")];
				//$trans_out_data[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$val[csf("body_part_id")]][$val[csf("detarmination_id")]][$val[csf("color")]][$val[csf("gsm")]][$val[csf("dia_width")]]['amount']+=$val[csf("cons_quantity")]*$val[csf("order_rate")];

				$trans_out_data[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color")]]['qnty']+=$val[csf("cons_quantity")];
				$trans_out_data[$val[csf("company_id")]][$val[csf("booking_no")]][$val[csf("cons_uom")]][$body_part[$val[csf("body_part_id")]]][$val[csf("detarmination_id")]][$val[csf("color")]]['amount']+=$val[csf("cons_quantity")]*$val[csf("order_rate")];


				//$trans_out_data[$val[csf("booking_no")]][$transOut_str]["qnty"] += $val[csf("cons_quantity")];
				//$trans_out_data[$val[csf("booking_no")]][$transOut_str]["amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
			/*}
			else
			{
				$trans_out_data[$val[csf("booking_no")]][$transOut_str]["opening_qnty"] += $val[csf("cons_quantity")];
				$trans_out_data[$val[csf("booking_no")]][$transOut_str]["opening_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
			}*/
			//$trans_out_id_arr[$val[csf("booking_no")]][$val[csf("body_part_id")]][$val[csf("color")]][$val[csf("detarmination_id")]][$val[csf("gsm")]][$val[csf("dia_width")]][$val[csf("cons_uom")]]['trans_out_id'].=$val[csf("id")].",";
			$trans_out_id_arr[$val[csf("booking_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("color")]][$val[csf("detarmination_id")]][$val[csf("cons_uom")]]['trans_out_id'].=$val[csf("id")].",";
		}

		/*echo "<pre>";
		print_r($trans_out_data);
		echo "</pre>";*/
		//die;

		if($all_po_id_cond_2!="")
		{

			/*$consumption_sql = sql_select("select distinct c.job_no,c.body_part_id,c.lib_yarn_count_deter_id, c.construction, c.composition,b.color_number_id,c.uom,b.cons,b.requirment,b.po_break_down_id,f.order_quantity from wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls c, wo_pre_cos_fab_co_avg_con_dtls b,wo_po_break_down e,wo_po_color_size_breakdown f where a.job_no = c.job_no and b.job_no=c.job_no 
			and c.id = b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=e.id and c.job_no=e.job_no_mst $all_po_id_cond and e.id=f.po_break_down_id and b.color_number_id=f.color_number_id  and c.fab_nature_id=2 and c.status_active =1 and b.status_active=1 $all_po_id_cond_2 ");
			//and b.color_size_table_id=f.id*/



			$consumption_sql=sql_select("select distinct c.job_no,c.body_part_id,c.lib_yarn_count_deter_id, c.construction, c.composition,b.gmts_sizes,b.color_number_id,c.uom,b.cons,b.requirment,b.po_break_down_id,c.color_size_sensitive ,d.contrast_color_id 
			from wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls c, wo_pre_cos_fab_co_avg_con_dtls b left join wo_pre_cos_fab_co_color_dtls d on  b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id 
			where a.job_no = c.job_no and b.job_no=c.job_no and c.id = b.pre_cost_fabric_cost_dtls_id and c.fab_nature_id=2 
			and c.status_active =1 and b.status_active=1 $all_po_id_cond_2   
			group by c.job_no,c.body_part_id,c.lib_yarn_count_deter_id, c.construction, c.composition,b.gmts_sizes,b.color_number_id,c.uom,b.cons,b.requirment,b.po_break_down_id  ,c.color_size_sensitive,d.contrast_color_id
			order by b.po_break_down_id ");
			


			/*$consumption_sql=sql_select("select distinct c.job_no,c.body_part_id,c.lib_yarn_count_deter_id, c.construction, c.composition,b.gmts_sizes,b.color_number_id,c.uom,b.cons,b.requirment,b.po_break_down_id
			 from wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls c, wo_pre_cos_fab_co_avg_con_dtls b
			 where a.job_no = c.job_no and b.job_no=c.job_no and c.id = b.pre_cost_fabric_cost_dtls_id 
			  and c.fab_nature_id=2 and c.status_active =1 and b.status_active=1 $all_po_id_cond_2    
			 order by b.po_break_down_id");*/


			//$all_po_id_cond_2
			//and b.color_number_id=14 and c.body_part_id=14
			
			//and b.po_break_down_id=40038

			/*$orderQnty_sql=sql_select("select c.job_no,c.body_part_id,c.lib_yarn_count_deter_id, c.construction, c.composition,f.size_number_id,f.color_number_id,c.uom,f.po_break_down_id,sum(f.order_quantity) as order_quantity
			 from wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls c,wo_po_break_down e,wo_po_color_size_breakdown f 
			where a.job_no = c.job_no 
			 and c.job_no=e.job_no_mst and e.id=f.po_break_down_id 
			 and c.fab_nature_id=2 and c.status_active =1 and f.status_active =1  $all_po_id_cond  group by  c.job_no,c.body_part_id,c.lib_yarn_count_deter_id, c.construction, c.composition,f.size_number_id,f.color_number_id,c.uom,f.po_break_down_id 
			 order by f.po_break_down_id");*/

			$orderQnty_sql=sql_select("select c.job_no,c.body_part_id,c.lib_yarn_count_deter_id, c.construction, c.composition,f.size_number_id,f.color_number_id,c.uom,f.po_break_down_id,sum(f.order_quantity) as order_quantity ,c.color_size_sensitive ,d.contrast_color_id 
			from wo_pre_cost_mst a,wo_po_break_down e,wo_po_color_size_breakdown f, wo_pre_cost_fabric_cost_dtls c left join wo_pre_cos_fab_co_color_dtls d on  c.id=d.pre_cost_fabric_cost_dtls_id 
			where a.job_no = c.job_no and c.job_no=e.job_no_mst and e.id=f.po_break_down_id and c.fab_nature_id=2 and c.status_active =1 and f.status_active =1  $all_po_id_cond
			group by c.job_no,c.body_part_id,c.lib_yarn_count_deter_id, c.construction, c.composition,f.size_number_id,f.color_number_id,c.uom,f.po_break_down_id ,c.color_size_sensitive ,d.contrast_color_id 
			order by f.po_break_down_id");





			//$all_po_id_cond 
			//and f.po_break_down_id=40038
			//and c.body_part_id=14 and f.color_number_id=14 
		

			//$consumption_sql = sql_select("select c.job_no,c.body_part_id,c.lib_yarn_count_deter_id, c.construction, c.composition, b.color_number_id, a.costing_per,c.uom,b.cons,b.requirment,b.po_break_down_id from wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls c, wo_pre_cos_fab_co_avg_con_dtls b where a.job_no = c.job_no and b.job_no=c.job_no and c.id = b.pre_cost_fabric_cost_dtls_id and c.fab_nature_id=2 and c.status_active =1 and b.status_active=1 $all_po_id_cond_2 group by c.job_no,c.body_part_id, c.lib_yarn_count_deter_id, c.construction, c.composition,b.color_number_id,a.costing_per,c.uom,b.cons,b.requirment,b.po_break_down_id");
			foreach ($orderQnty_sql as $val)
			{
				if($val[csf("color_size_sensitive")]==3)
				{
					$colorIDs=$val[csf("contrast_color_id")];
				}
				else
				{
					$colorIDs=$val[csf("color_number_id")];
				}


				$orderQnty_single_arr[$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$colorIDs][$val[csf("uom")]][$val[csf("size_number_id")]][$val[csf("po_break_down_id")]]["orderQnty_single"]= $val[csf("order_quantity")];
				$orderQnty_arr[$val[csf("job_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("lib_yarn_count_deter_id")]][$colorIDs][$val[csf("uom")]]["orderQnty"]+= $val[csf("order_quantity")];
			}
			unset($orderQnty_sql);
			/*echo "<pre>";
			print_r($orderQnty_arr);
			echo "</pre>";
	     	die;*/
	     	//$consumtionCal=0;
			foreach ($consumption_sql as $val)
			{
				/*if($val[csf("costing_per")] == 1){
					$multipy_with = 1;
				}elseif ($val[csf("costing_per")] == 2) {
					$multipy_with = 12;
				}elseif ($val[csf("costing_per")] == 3) {
					$multipy_with = .5;
				}elseif ($val[csf("costing_per")] == 4) {
					$multipy_with = .3333;
				}elseif ($val[csf("costing_per")] == 5) {
					$multipy_with = .25;
				}*/

				//$consumption_arr[$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("color_number_id")]][$val[csf("uom")]] += $multipy_with*($val[csf("requirment")]/$val[csf("gmts_sizes")]);

				/*$consumption_arr[$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("color_number_id")]][$val[csf("uom")]]["consump_per_dzn"]+= ($val[csf("requirment")]*$val[csf("order_quantity")])/12;
				$consumption_arr[$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("color_number_id")]][$val[csf("uom")]]["order_quantity"]+= $val[csf("order_quantity")];*/
				if($val[csf("color_size_sensitive")]==3)
				{
					$colorID=$val[csf("contrast_color_id")];
				}
				else
				{
					$colorID=$val[csf("color_number_id")];
				}

				$consqumptionQntySing=$consumption_arr_single[$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$colorID][$val[csf("uom")]][$val[csf("gmts_sizes")]][$val[csf("po_break_down_id")]]["consump_per_dzn_single"]=$val[csf("requirment")];
				
				$orderQntySing=$orderQnty_single_arr[$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$colorID][$val[csf("uom")]][$val[csf("gmts_sizes")]][$val[csf("po_break_down_id")]]["orderQnty_single"];





				//$consumtionCal+=($orderQntySing*$consqumptionQntySing)/12;

				$consumption_arr[$val[csf("job_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("lib_yarn_count_deter_id")]][$colorID][$val[csf("uom")]]["consump_per_dzn"]+=($orderQntySing*$consqumptionQntySing)/12;//$consumtionCal;

				$orderQnty_arr[$val[csf("job_no")]][$body_part[$val[csf("body_part_id")]]][$val[csf("lib_yarn_count_deter_id")]][$colorID][$val[csf("uom")]]["orderQnty"];

				/*$consumptionTotal+=$consumption_arr_single[$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("color_number_id")]][$val[csf("uom")]][$val[csf("gmts_sizes")]][$val[csf("po_break_down_id")]]["consump_per_dzn_single"]= $val[csf("requirment")]*$orderQnty_single_arr[$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("color_number_id")]][$val[csf("uom")]][$val[csf("gmts_sizes")]][$val[csf("po_break_down_id")]]["orderQnty_single"]/12;

				$consumption_arr[$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("color_number_id")]][$val[csf("uom")]]["consump_per_dzn"]+=$consumptionTotal;*/


				//$orderQnty_arr[$val[csf("job_no")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("color_number_id")]]["orderQnty"]+= $val[csf("order_quantity")];


				//$consumption_arr[$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("color_number_id")]][$val[csf("uom")]]["order_quantity"]+= $val[csf("order_quantity")];

				//$consumption_arr[$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("color_number_id")]][$val[csf("uom")]]["cons"] = $val[csf("requirment")];

				//$consumption_arr[$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("color_number_id")]][$val[csf("uom")]]["po_break_down_id"] = $val[csf("po_break_down_id")];
			}
			unset($consumption_sql);
		}
		//echo $orderQnty_arr;
		/*echo "<pre>";
	    print_r($consumption_arr);
	    echo "</pre>";*/
	   // die;

	   
	    

	    $table_width = "1770";
		$col_span = "10";
		$col_span2 = "6";
		ob_start();
		?>
		<style type="text/css">
			.word_break_wrap {
				word-break: break-all;
				word-wrap: break-word;
			}
			.grad1 {
				  background-image: linear-gradient(#e6e6e6, #b1b1cd, #e0e0eb);
				}
			.grad2 {
				background-color: #d9dddc;
			  	/*background-image: linear-gradient(#b6e6e6, #b1b1cd, #b0b0eb);*/
			}
		</style>
		<fieldset style="width:<? echo $table_width+20;?>px;">
			<table cellpadding="0" cellspacing="0" width="1770">
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="23" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="23" style="font-size:14px"><strong> <? //if($date_from!="") echo "From : ".change_date_format(str_replace("'","",$txt_date_from)) ;?> <? if($date_to!="") echo "To : ".change_date_format(str_replace("'","",$txt_date_to)) ;?></strong></td>
				</tr>
			</table>

			<table width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
				<thead>
					<th width="30">SL</th>
					<th width="100">Buyer</th>
					<th width="100">Buyer Client</th>
					<th width="100">Style</th>
					<th width="100">Job</th>
					<th width="100">Booking No</th>
					<th width="100">Body Part</th>
					<th width="120">F.Construction</th>
					<th width="100">F. Color</th>
					<th width="50">UOM</th>
					<th width="100">Booking Qty</th>
					<th width="100">Total Rcv</th>
					<th width="100">Rcved Balance Qty</th>
					<th width="100">Total Issue</th>
					<th width="100">Stock Qty</th>
					<th width="100">Consumption / Dzn</th>
					<th width="100"><p>Possible Cut Pcs.(Stock Qty)</p></th>
				</thead>
			</table>
			<div style="<? echo $table_width+20;?>px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 

					<?
					$i=1;
					$grand_total_booking_qty=$grand_total_tot_receive=$grand_total_tot_receive_balance=$grand_total_total_issue=$grand_total_stock_qnty=0;

					if($job_no != "" || $book_no!="" ||  $style_ref_no!="" || $buyer_id!=0 )
					{
						if($date_to!="")
						{
							if(empty($search_book_arr)){die;}
						}
						else
						{
							if(empty($search_book_arr)){die;}
						}
					}

					$receiveQnty = $transInQnty = $recvRtnQnty =0;
					/*echo "<pre>";
					print_r($mainArr);
					echo "</pre>";*/


					foreach ($mainArr as $cons_uom => $uom_data)
					{
						$uom_total_booking_qty=$uom_total_tot_receive=$uom_total_tot_receive_balance=$uom_total_total_issue=$uom_total_stock_qnty=0;
						foreach ($uom_data as $company_id => $company_data)
						{
							foreach ($company_data as $insert_date => $insert_date_data)
							{
								foreach ($insert_date_data as $booking_type_id => $booking_type_data)
								{
									foreach ($booking_type_data as $booking_no => $booking_no_data)
									{
										foreach ($booking_no_data as $body_part_name => $body_part_data)
										{
											foreach ($body_part_data as $fabrication_id => $fabrication_data)
											{
												foreach ($fabrication_data as $color_id => $row)
												{

													//echo $cons_uom."=".$company_id."=".$insert_date."=".$booking_type_id."=".$booking_no."=".$body_part_name."=".$fabrication_id."=".$color_id."<br/>";
													
													$buyer_id=$bookingDataArr[$cons_uom][$company_id][$insert_date][$booking_type_id][$booking_no][$body_part_name][$fabrication_id][$color_id]['buyer_id'];

													$booking_qnty=$bookingDataArr[$cons_uom][$company_id][$insert_date][$booking_type_id][$booking_no][$body_part_name][$fabrication_id][$color_id]['booking_qnty'];
													$body_part_id=$bookingDataArr[$cons_uom][$company_id][$insert_date][$booking_type_id][$booking_no][$body_part_name][$fabrication_id][$color_id]['body_part_id'];

													$receiveQnty =$recv_qnty_array[$company_id][$booking_no][$cons_uom][$body_part_name][$fabrication_id][$color_id]['receive_qnty'];
													$booking_without_order_status =$recv_bookingWithoutOrder[$company_id][$booking_no][$cons_uom][$body_part_name][$fabrication_id][$color_id]['booking_without_order'];
													$batch_ids =$batchIds_arr[$company_id][$booking_no][$cons_uom][$body_part_name][$fabrication_id][$color_id]['batch_id'];
													
													$transInQnty=$trans_in_qnty_array[$company_id][$booking_no][$cons_uom][$body_part_name][$fabrication_id][$color_id]['trans_in_qnty'];

													$recvRtnQnty=$recv_rtn_qnty_array[$company_id][$booking_no][$cons_uom][$body_part_name][$fabrication_id][$color_id]['recv_rtn_qnty'];



													$cutting_inside=$issue_qnty_array[$company_id][$booking_no][$cons_uom][$body_part_name][$fabrication_id][$color_id]['cutting_inside'];
													$cutting_outside=$issue_qnty_array[$company_id][$booking_no][$cons_uom][$body_part_name][$fabrication_id][$color_id]['cutting_outside'];
													$other_issue=$issue_qnty_array[$company_id][$booking_no][$cons_uom][$body_part_name][$fabrication_id][$color_id]['other_issue'];
													

													$trans_out_qnty=$trans_out_data[$company_id][$booking_no][$cons_uom][$body_part_name][$fabrication_id][$color_id]['qnty'];
													

													$inside_return=$issue_return_data[$company_id][$booking_no][$cons_uom][$body_part_name][$fabrication_id][$color_id]['inside_return'];
													$outside_return=$issue_return_data[$company_id][$booking_no][$cons_uom][$body_part_name][$fabrication_id][$color_id]['outside_return'];


															

													//echo $receiveQnty ."+". $transInQnty ."-". $recvRtnQnty."==";
													//echo $cutting_inside ."+". $cutting_outside ."+". $other_issue  ."+". $trans_out_qnty ."-(". $inside_return  ."+".  $outside_return.")"."<br/>";

													$tot_receive = $receiveQnty + $transInQnty - $recvRtnQnty;
													$total_issue = $cutting_inside + $cutting_outside + $other_issue + $trans_out_qnty-($inside_return + $outside_return);




													$booking_balance_qnty 	= $booking_qnty- $tot_receive;
													//$booking_balance_amount = $booking_balance_qnty*$booking_rate;
													$tot_receive_balance 	= $booking_qnty-$tot_receive;


													$stock_qnty 	= $tot_receive - $total_issue;

													/*$po_number 	= implode(",",array_unique(explode(",",chop($po_array[$booking_no][$prodStr]["po_no"],","))));
													$pi_no 	= implode(",",array_unique(explode(",",chop($pi_no,","))));
													$lc_sc_no 	= implode(",",array_unique(explode(",",chop($lc_sc_no,","))));
													$prodStrArr 	= explode("*", $prodStr);*/

													//echo $booking_no.'<br>';
													$job_arr 		= array_filter(array_unique(explode(",",chop($book_po_ref[$booking_no]["job_no"],","))));
													$job_quantity 	= ""; $consump_per_dzn="";$consump_po_id_count="";$consump_per_dzn_color_size_wise="";$order_quantity_color_size="";$consump_per_dzn_color_size_wise_avg="";
													foreach ($job_arr as $job)
													{
														$job_quantity += $job_qnty_arr[$job]["qnty"];
														//$consump_per_dzn += $consumption_arr[$job][$prodStrArr[2]][$prodStrArr[3]][$prodStrArr[6]][$prodStrArr[7]]["cons"];
														//$consump_po_id_count .= $consumption_arr[$job][$prodStrArr[2]][$prodStrArr[3]][$prodStrArr[6]][$prodStrArr[7]]["po_break_down_id"].",";

														//$consump_per_dzn_color_size_wise += $consumption_arr[$job][$prodStrArr[1]][$prodStrArr[2]][$prodStrArr[5]][$prodStrArr[6]]["consump_per_dzn"];

														
														$consump_per_dzn_color_size_wise +=$consumption_arr[$job][$body_part_name][$fabrication_id][$color_id][$cons_uom]["consump_per_dzn"];


														$order_quantity_color_size += $orderQnty_arr[$job][$body_part_name][$fabrication_id][$color_id][$cons_uom]["orderQnty"];


														//$order_quantity_color_size += $orderQnty_arr[$job][$prodStrArr[1]][$prodStrArr[2]][$prodStrArr[5]][$prodStrArr[6]]["orderQnty"];


														$consump_per_dzn_color_size_wise_avg+=($consump_per_dzn_color_size_wise/$order_quantity_color_size)*12;
														//echo $consump_per_dzn_color_size_wise_avg;
														//echo $consump_per_dzn_color_size_wise."/".$order_quantity_color_size."==";


													}
													//echo $consump_per_dzn_color_size_wise."/".$order_quantity_color_size;
													$consump_po_id_count_arr=array_unique(explode(",", chop($consump_po_id_count,",")));
													$consump_po_id_counts=count($consump_po_id_count_arr);
													$job_nos = implode(",", $job_arr);

													$client_arr = array_unique(explode(",",chop($book_po_ref[$booking_no]["client_id"],",")));
													$client_nos="";
													foreach ($client_arr as $client_id)
													{
														$client_nos .= $buyer_arr[$client_id].",";
													}

												

													$style_ref_no = implode(",",array_unique(explode(",",chop($book_po_ref[$booking_no]["style_ref_no"],","))));;
													$pay_mode_nos = implode(",",array_unique(explode(",",chop($book_po_ref[$booking_no]["pay_mode"],","))));

													$booking_date = $book_po_ref[$booking_no]["booking_date"];
													$booking_type = $book_po_ref[$booking_no]["booking_type"];

													/*$dia_width_type_arr = array_filter(array_unique(explode(",",chop($dia_width_types,","))));

													$dia_width_type="";
													foreach ($dia_width_type_arr as $width_type)
													{
														$dia_width_type .= $fabric_typee[$width_type].",";
													}
													$dia_width_type = chop($dia_width_type,",");*/


														//echo $booking_no.'='.$prodStrArr[1].'='.$prodStrArr[2].'='.$prodStrArr[5];

													

													//$row[csf("store_id")]."*".
													//$prodStr = $row[csf("body_part")]."__".$row[csf("fabrication_id")]."__".$row[csf("gsm")]."__".$row[csf("dia")]."__".$row[csf("color_id")]."__".$row[csf("cons_uom")];
													$prodStr = $body_part_id."__".$fabrication_id."__".$color_id."__".$cons_uom."__".$booking_without_order_status."__".$batch_ids;

													//RECEIVE
													//issue Return ids
													$issue_rtn_id=$issue_return_id_arr[$booking_no][$body_part_name][$color_id][$fabrication_id][$cons_uom]['issue_return_id'];
													$issue_rtn_id=chop($issue_rtn_id,",");
													//transfer in ids
													//$transferIn_id=$trans_in_id_arr[$row[csf("booking_no")]][$row[csf("body_part")]][$row[csf("color_id")]][$row[csf("fabrication_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("cons_uom")]]['trans_in_id'];
													$transferIn_id=$trans_in_id_arr[$booking_no][$body_part_name][$color_id][$fabrication_id][$cons_uom]['trans_in_id'];
													$transferIn_id=chop($transferIn_id,",");

													//ISSUE
													//receive Return ids
													$recv_rtn_id=$recv_return_id_arr[$booking_no][$body_part_name][$color_id][$fabrication_id][$cons_uom]['recv_return_id'];
													$recv_rtn_id=chop($recv_rtn_id,",");
													//transfer out ids
													/*$trans_out_id_arr[$row[csf("booking_no")]][$row[csf("body_part")]][$row[csf("color_id")]][$row[csf("fabrication_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("cons_uom")]]['trans_out_id'];
													$transferOut_id=$trans_out_id_arr[$row[csf("booking_no")]][$row[csf("body_part")]][$row[csf("color_id")]][$row[csf("fabrication_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("cons_uom")]]['trans_out_id'];*/

													$transferOut_id=$trans_out_id_arr[$booking_no][$body_part_name][$color_id][$fabrication_id][$cons_uom]['trans_out_id'];

													$transferOut_id=chop($transferOut_id,",");







													if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
														<td width="30"><? echo $i;?></td>
														<td width="100" align="center"><? echo $buyer_arr[$buyer_id];?></td>
														<td width="100" align="center"><? echo chop($client_nos,",");?></td>
														<td width="100" align="center"><p class="word_break_wrap"><? echo $style_ref_no;?></p></td>
														<td width="100" align="center"><p class="word_break_wrap"><? echo $job_nos;?></p></td>
														<td width="100" align="center"><? echo $booking_no;?></td>
														<td width="100" align="center" title="<? echo $body_part_id;?>"><p class="word_break_wrap"><? echo $body_part_name;?></p></td>
														<td title="<? echo $fabrication_id; ?>" width="120" align="center"><p class="word_break_wrap"><? echo $constructionArr[$fabrication_id];?></p></td>
														<td width="100" align="center" title="<? echo $color_id;?>"><p class="word_break_wrap"><? echo $color_arr[$color_id];?></p></td>
														<td width="50" align="center" title="<? echo $cons_uom; ?>"><? echo $unit_of_measurement[$cons_uom]; ?></td>
														<td width="100" align="right"><? echo number_format($booking_qnty,2,".","");?></td>

														


														<td width="100" align="right" title="<? echo "rcv=$receiveQnty + trin=$transInQnty - rcvrtn=$recvRtnQnty"; ?>">
															<a href="##" onclick="openmypage_qnty('<? echo $booking_no;?>','<? echo $prodStr;?>','openmypage_receive','<? echo $date_from;?>','<? echo $date_to;?>','<? echo $issue_rtn_id; ?>','<? echo $transferIn_id;?>','<? echo $recv_rtn_id;?>','<? echo $transferOut_id;?>','<? echo $cbo_store_name;?>');"><? echo number_format($tot_receive,2,".",""); ?>
																
															</a>
														</td>
													

														<td width="100" align="right"><? echo number_format($tot_receive_balance,2,".","")?></td>

														<td width="100" align="right"><a href="##" onclick="openmypage_qnty('<? echo $booking_no;?>','<? echo $prodStr;?>','openmypage_issue_popup','<? echo $date_from;?>','<? echo $date_to;?>','<? echo $issue_rtn_id; ?>','<? echo $transferIn_id;?>','<? echo $recv_rtn_id;?>','<? echo $transferOut_id;?>','<? echo $cbo_store_name;?>');"><? echo number_format($total_issue,2,".","");?></a>

														</td>

														<td width="100" align="right"><a href="##" onclick="openmypage_qnty('<? echo $booking_no;?>','<? echo $prodStr;?>','openmypage_balance_popup','<? echo $date_from;?>','<? echo $date_to;?>','<? echo $issue_rtn_id; ?>','<? echo $transferIn_id;?>','<? echo $recv_rtn_id; ?>','<? echo $transferOut_id;?>','<? echo $cbo_store_name;?>');"><? echo number_format($stock_qnty,2,".","");?></a>



														<td width="100" align="right"><? 
														if ($consump_per_dzn_color_size_wise_avg>0) {
															$consump_per_dzn_calc=$consump_per_dzn_color_size_wise_avg; 
														}
														else
														{
															$consump_per_dzn_calc=0.0000;
														}
														echo number_format($consump_per_dzn_calc,4,".","");

														//echo "==";echo $job."*".$prodStrArr[1]."*".$prodStrArr[2]."*".$prodStrArr[5]."*".$prodStrArr[6];

														?></td>
														<td width="100" align="right"><? $possible_cutPic=($tot_receive/$consump_per_dzn_calc)*12; 
														if(is_infinite ($possible_cutPic)){
															echo $possible_cutPic =0;
														}
														else
														{
															if(is_nan($possible_cutPic)){echo $possible_cutPic=0;}else{echo ceil($possible_cutPic);}
															
														}
														?></td>
													</tr>
													<?
													$i++;
													$uom_total_booking_qty+=$booking_qnty;
													$uom_total_tot_receive+=$tot_receive;
													$uom_total_tot_receive_balance+=$tot_receive_balance;
													$uom_total_total_issue+=$total_issue;
													$uom_total_stock_qnty+=$stock_qnty;
												}
											}
										}
									}
								}
							}
						}

							

							?>		
							<tr class="grad1">
								<td colspan="<? echo $col_span;?>" align="right"><strong>Total : </strong></td>
								<td width="100" align="right" id="">&nbsp;<strong><? echo number_format($uom_total_booking_qty,2,".",""); ?></strong></td>
								<td width="100" align="right" id="">&nbsp;<strong><? echo number_format($uom_total_tot_receive,2,".",""); ?></strong></td>
								<td width="100" align="right" id="">&nbsp;<strong><? echo number_format($uom_total_tot_receive_balance,2,".",""); ?></strong></td>
								<td width="100" align="right" id="">&nbsp;<strong><? echo number_format($uom_total_total_issue,2,".",""); ?></td>
								<td width="100" align="right" id="">&nbsp;<strong><? echo number_format($uom_total_stock_qnty,2,".",""); ?></strong></td>
								<td width="100" align="right"></td>
								<td></td>
							</tr>
							<?
					}

					?>


					
				</table>
			</div>
		</fieldset>
		<?
	}
		

	
    $html = ob_get_contents();
    ob_clean();
    foreach (glob($user_id."*.xls") as $filename) {
    	@unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$rpt_type";
    exit();
}

if($action=="openmypage_receive")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<?
				//$ref_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];

				$prod_ref = explode("__", $prod_ref);
				//$prod_id = $prod_ref[0];
				//$store_id = $prod_ref[0];
				$body_part_id = $prod_ref[0];
				$fabric_description_id = $prod_ref[1];
				/*$gsm = $prod_ref[2];
				$width = $prod_ref[3];*/
				$color_id = $prod_ref[2];
				$cons_uom = $prod_ref[3];
				$booking_without_order = $prod_ref[4];
				$floor_id = $prod_ref[5];
				$room = $prod_ref[6];
				$rack = $prod_ref[7];
				$self = $prod_ref[8];
				//$from_date

				$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
				$store_arr=return_library_array( "select id,store_name  from lib_store_location", "id", "store_name");
				$i=1;
				if($body_part_id!='') $body_part_cond=" and b.body_part_id='$body_part_id'"; else $body_part_cond="";
				//if($width!='') $width_cond=" and c.width='$width'"; else $width_cond="";
				//if($width!='') $width_cond2=" and d.dia_width='$width'"; else $width_cond2="";
				if($prod_ref[8])
				{
					$room_rack_cond = " and b.floor_id='$floor_id' and b.room='$room' and b.rack='$rack' and b.self = '$self'";
					$room_rack_cond2 = " and c.floor_id='$floor_id' and c.room='$room' and c.rack='$rack' and c.self = '$self'";
				}
				if($to_date != "")
				{
					//$date_condition = " and b.transaction_date  between '".$from_date."' and '".$to_date."'";
					//$date_condition_2 = " and c.transaction_date  between '".$from_date."' and '".$to_date."'";
					$date_condition   = " and b.transaction_date <= '$to_date'";
					$date_condition_2 = " and c.transaction_date <= '$to_date'";
				}
				if($transfer_in_ids!=""){$trans_id_cond = "and a.id in($transfer_in_ids)";}
				if($issue_rtn_id!=""){$retrn_id_cond = "and a.id in($issue_rtn_id)";}
				if($recv_rtn_id!=""){$retrn_id_cond2 = "and a.id in($recv_rtn_id)";}
				
				if($buyerId!=0){$buyerId_cond = "and f.buyer_id in($buyerId)";}
				if($booking_without_order==""){$bookingOrdStatusCond = "and a.booking_without_order is NULL";}else{$bookingOrdStatusCond = "and a.booking_without_order=$booking_without_order";}

				if($store_ids != "")
				{
					$store_cond = " and b.store_id in ($store_ids) ";
					$store_cond_2 = " and c.store_id in ($store_ids) ";
				}

				//and b.store_id= '$store_id'
				/*$rcv_sql = sql_select("SELECT a.recv_number as transaction_id,a.receive_date,c.width as dia, sum(b.cons_quantity) as quantity,b.transaction_type,b.transaction_date,b.store_id  from inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c, pro_batch_create_mst e  WHERE a.company_id in ($companyID) and a.id = b.mst_id and b.id = c.trans_id  and b.transaction_type =1 and a.entry_form = 37 and a.status_active =1 and b.status_active =1 and c.status_active =1  and b.pi_wo_batch_no = e.id and e.booking_no = '$booking_no' and c.color_id=$color_id and c.body_part_id= '$body_part_id' and c.gsm = '$gsm' $width_cond $buyerId_cond  and b.cons_uom = '$cons_uom' $room_rack_cond $date_condition group by a.recv_number,a.receive_date,c.width,b.transaction_type,b.transaction_date,b.store_id");*/

				//$bookingOrdStatusCond

				$rcv_sql = sql_select("SELECT a.recv_number as transaction_id,a.receive_date,c.width as dia, sum(b.cons_quantity) as quantity,b.transaction_type,b.transaction_date,b.store_id  from inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c,product_details_master d, pro_batch_create_mst e, wo_booking_mst f  WHERE a.company_id in ($companyID) and a.id = b.mst_id and b.id = c.trans_id and c.prod_id=d.id  and b.transaction_type =1 and a.entry_form = 37 and b.pi_wo_batch_no = e.id  and e.booking_no = f.booking_no and e.booking_no = '$booking_no'  and d.color=$color_id and c.body_part_id= '$body_part_id' and c.fabric_description_id='$fabric_description_id' $buyerId_cond  and b.cons_uom = '$cons_uom' $room_rack_cond $date_condition $store_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1  and b.is_deleted=0 and c.status_active =1  and c.is_deleted=0 and e.status_active =1  and e.is_deleted=0 group by a.recv_number,a.receive_date,c.width,b.transaction_type,b.transaction_date,b.store_id 
					union all
					SELECT a.recv_number as transaction_id,a.receive_date,c.width as dia, sum(b.cons_quantity) as quantity,b.transaction_type,b.transaction_date,b.store_id  from inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c,product_details_master d, pro_batch_create_mst e, wo_non_ord_samp_booking_mst f  WHERE a.company_id in ($companyID) and a.id = b.mst_id and b.id = c.trans_id and c.prod_id=d.id  and b.transaction_type =1 and a.entry_form = 37 and b.pi_wo_batch_no = e.id  and e.booking_no = f.booking_no and e.booking_no = '$booking_no'  and d.color=$color_id and c.body_part_id= '$body_part_id' and c.fabric_description_id='$fabric_description_id' $buyerId_cond  and b.cons_uom = '$cons_uom' $room_rack_cond $date_condition $store_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1  and b.is_deleted=0 and c.status_active =1  and c.is_deleted=0 and e.status_active =1  and e.is_deleted=0 group by a.recv_number,a.receive_date,c.width,b.transaction_type,b.transaction_date,b.store_id
					");
				  //union all  
					
				
				//and c.store_id  = $store_id
				/*$issue_recv_rtn_sqlx = sql_select("select  a.issue_number as transaction_id,a.issue_date as receive_date,d.dia_width as dia, sum(c.cons_quantity) as quantity,c.transaction_type, c.transaction_date,c.store_id from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and a.company_id in ($companyID)  and d.color = '$color_id'  and b.body_part_id =$body_part_id and c.cons_uom = '$cons_uom' and e.booking_no = '$booking_no' and d.gsm='$gsm' $width_cond2 $room_rack_cond2  and a.entry_form = 46 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category =2 and c.transaction_type =3 $retrn_id_cond2 and c.prod_id=d.id and c.pi_wo_batch_no=e.id $date_condition_2  group by a.issue_number,a.issue_date,d.dia_width,c.transaction_type, c.transaction_date,c.store_id ");*/
				$issue_recv_rtn_sqlx = sql_select("SELECT  a.issue_number as transaction_id,a.issue_date as receive_date,d.dia_width as dia, sum(c.cons_quantity) as quantity,c.transaction_type, c.transaction_date,c.store_id from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and a.company_id in ($companyID)  and d.color = '$color_id'  and b.body_part_id =$body_part_id and d.detarmination_id='$fabric_description_id' and c.cons_uom = '$cons_uom' and e.booking_no = '$booking_no'  $room_rack_cond2  and a.entry_form = 46 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category =2 and c.transaction_type =3 $retrn_id_cond2 and c.prod_id=d.id and c.pi_wo_batch_no=e.id $date_condition_2 $store_cond_2 group by a.issue_number,a.issue_date,d.dia_width,c.transaction_type, c.transaction_date,c.store_id ");


					//union all 
				
				//and b.store_id= '$store_id'
				$rcv_trans_in_sql = sql_select("SELECT a.transfer_system_id as transaction_id, a.transfer_date as receive_date,d.dia_width as dia, sum(b.cons_quantity) as quantity,b.transaction_type,b.transaction_date,b.store_id from inv_item_transfer_mst a, inv_item_transfer_dtls c, inv_transaction b , product_details_master d,  pro_batch_create_mst e where a.id=c.mst_id and c.to_trans_id=b.id and b.prod_id=d.id and b.pi_wo_batch_no=e.id and b.item_category=2 and b.transaction_type=5 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306) and b.company_id in ($companyID) $trans_id_cond and e.booking_no = '$booking_no'  and d.color=$color_id and c.to_body_part= '$body_part_id' and d.detarmination_id='$fabric_description_id' and c.uom = '$cons_uom' $room_rack_cond $date_condition $store_cond group by  a.id,a.transfer_system_id, a.transfer_date,d.dia_width,b.transaction_type,b.transaction_date,b.store_id"); //and c.width='$width'
				//echo $rcv_sql;
			?>
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="6">Total Receive Pop-up</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="70">Transaction Date</th>
						<th width="150">Transaction ID</th>
						<th width="150">Store Name</th>
						<th width="80">Dia</th>
						<th>Qty.</th>
					</tr>
				</thead>
				<tbody>
					<?

					foreach($rcv_sql as $row)
					{
						$date_frm=date('Y-m-d',strtotime($from_date));
						$transaction_date=date('Y-m-d',strtotime($row[csf('receive_date')]));
						if( $from_date == "" || ( $from_date != "" && ($transaction_date >= $date_frm)))
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="70" align="center"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="150" align="center"><p><? echo $row[csf('transaction_id')]; ?></p></td>
								<td width="150" align="center"><p><? echo $store_arr[$row[csf('store_id')]]; ?></p></td>
								<td width="80" align="center"><p><? echo $row[csf('dia')]; ?></p></td>
								<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							</tr>
							<?
							$tot_recv_qty+=$row[csf('quantity')];
							$i++;
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_recv_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="6">Transfer In</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="70">Transaction Date</th>
						<th width="150">Transaction ID</th>
						<th width="150">Store Name</th>
						<th width="80">Dia</th>
						<th>Qty.</th>
					</tr>
				</thead>
				<tbody>
					<?
					$iii=1;
					foreach($rcv_trans_in_sql as $row)
					{
						$date_frm=date('Y-m-d',strtotime($from_date));
						$transaction_date=date('Y-m-d',strtotime($row[csf('receive_date')]));
						if( $from_date == "" || ( $from_date != "" && ($transaction_date >= $date_frm)))
						{
							if ($iii%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $iii+200; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $iii+200;?>">
								<td width="30"><p><? echo $iii; ?></p></td>
								<td width="70" align="center"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="150" align="center"><p><? echo $row[csf('transaction_id')]; ?></p></td>
								<td width="150" align="center"><p><? echo $store_arr[$row[csf('store_id')]]; ?></p></td>
								<td width="80" align="center"><p><? echo $row[csf('dia')]; ?></p></td>								
								<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							</tr>
							<?
							$tot_trns_in_qty+=$row[csf('quantity')];
							$iii++;
						}
					}
					
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_trns_in_qty,2); ?>&nbsp;</td>
					</tr>
					
				</tfoot>
			</table>
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="6">Receive Rtn Qnty</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="70">Transaction Date</th>
						<th width="150">Transaction ID</th>
						<th width="150">Store Name</th>
						<th width="80">Dia</th>
						<th>Qty.</th>
					</tr>
				</thead>
				<tbody>
					<?
					$ii=1;
					foreach($issue_recv_rtn_sqlx as $row)
					{
						$date_frm=date('Y-m-d',strtotime($from_date));
						$transaction_date=date('Y-m-d',strtotime($row[csf('receive_date')]));
						if( $from_date == "" || ( $from_date != "" && ($transaction_date >= $date_frm)))
						{
							if ($ii%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $ii+100; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $ii+100;?>">
								<td width="30"><p><? echo $ii; ?></p></td>
								<td width="70" align="center"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="150" align="center"><p><? echo $row[csf('transaction_id')]; ?></p></td>
								<td width="150" align="center"><p><? echo $store_arr[$row[csf('store_id')]]; ?></p></td>
								<td width="80" align="center"><p><? echo $row[csf('dia')]; ?></p></td>
								<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							</tr>
							<?
							$tot_recv_rtn_qtyx+=$row[csf('quantity')];
							$ii++;
						}
					}
					$total_over_all_rev_qnty = $tot_recv_qty+$tot_trns_in_qty-$tot_recv_rtn_qtyx;
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_recv_rtn_qtyx,2); ?>&nbsp;</td>
					</tr>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Grand Total</td>
						<td align="right">&nbsp;<? echo number_format($total_over_all_rev_qnty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	exit();
}
if($action=="openmypage_issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">

			<?
				$prod_ref = explode("__", $prod_ref);
				//$store_id = $prod_ref[0];
				$body_part_id = $prod_ref[0];
				$fabric_description_id = $prod_ref[1];
				/*$gsm = $prod_ref[2];
				$width = $prod_ref[3];*/
				$color_id = $prod_ref[2];
				$cons_uom = $prod_ref[3];
				$booking_without_order = $prod_ref[4];
				$batch_id = chop($prod_ref[5],",");
				$floor_id = $prod_ref[6];
				$room = $prod_ref[7];
				$rack = $prod_ref[8];
				$self = $prod_ref[9];
				$from_date=$from_date;
				$to_date=$to_date;

				$store_arr=return_library_array( "select id,store_name  from lib_store_location", "id", "store_name");

				//$color_arr=return_library_array( "select id,color_name from lib_color where id = '$color_id'", "id", "color_name");
				$i=1;
				//if($width!='') $width_cond = " and d.dia_width='$width'"; else $width_cond = "";
				//if($width!='') $width_cond2 = " and c.width='$width'"; else $width_cond = "";

				if($prod_ref[8])
				{
					$room_rack_cond = " and c.floor_id='$floor_id' and c.room='$room' and c.rack='$rack' and c.self = '$self'";
					$room_rack_cond2 = " and b.floor_id='$floor_id' and b.room='$room' and b.rack='$rack' and b.self = '$self'";
				}
				if($to_date != "")
				{
					//$date_condition = " and c.transaction_date  between '".$from_date."' and '".$to_date."'";
					//$date_condition_2 = " and b.transaction_date  between '".$from_date."' and '".$to_date."'";
					$date_condition   = " and b.transaction_date <= '$to_date'";
					$date_condition_2 = " and c.transaction_date <= '$to_date'";
				}
				if($transfer_out_ids!=""){$trans_id_cond = "and a.id in($transfer_out_ids)";}
				if($recv_rtn_id!=""){$retrn_id_cond = "and a.id in($recv_rtn_id)";}
				if($issue_rtn_id!=""){$retrn_id_cond2 = "and a.id in($issue_rtn_id)";}

				if($buyerId!=0){$buyerId_cond = " and f.buyer_id in($buyerId)";}
				if($booking_without_order==""){$bookingOrdStatusCond = "and e.booking_without_order is NULL";}else{$bookingOrdStatusCond = "and e.booking_without_order=$booking_without_order";}

				if($store_ids != "")
				{
					$store_cond = " and b.store_id in ($store_ids) ";
					$store_cond_2 = " and c.store_id in ($store_ids) ";
				}


			/*
					$date_from=str_replace("'","",$txt_date_from);
					$date_to=str_replace("'","",$txt_date_to);
					$date_cond="";
					if($date_from!="" && $date_to!="")
					{
						if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
						else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

						if($db_type==0)$end_date=change_date_format($date_to,"yyyy-mm-dd","");
						else if($db_type==2) $end_date=change_date_format($date_to,"","",1);

						//$date_cond   = " and b.transaction_date <= '$end_date'";
						//$date_cond_2 = " and c.transaction_date <= '$end_date'";
						$date_cond   = " and b.transaction_date between '$start_date' and '$end_date'";
						$date_cond_2 = " and c.transaction_date between '$start_date' and '$end_date'";

					}*/
				//and c.store_id  = $store_id
				/*$issue_sql = sql_select("select a.issue_number as transaction_id, c.transaction_date,c.transaction_type, sum(c.cons_quantity) as quantity,d.dia_width as dia,c.store_id from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c , product_details_master d, pro_batch_create_mst e  where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($companyID) and a.knit_dye_source in(1,0)  and d.color=$color_id and b.body_part_id =$body_part_id and c.cons_uom = '$cons_uom' and e.booking_no = '$booking_no' and d.gsm='$gsm' $width_cond $room_rack_cond and a.entry_form = 18 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category =2 and c.transaction_type =2 $date_condition_2 $buyerId_cond group by a.issue_number, c.transaction_date,c.transaction_type,d.dia_width,c.store_id"); */
				//and e.booking_without_order=$booking_without_order


				$issue_sql = sql_select("SELECT a.issue_number as transaction_id, c.transaction_date,c.transaction_type, sum(c.cons_quantity) as quantity,d.dia_width as dia,c.store_id from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c , product_details_master d, pro_batch_create_mst e, wo_booking_mst f  where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and e.booking_no=f.booking_no and c.company_id in ($companyID) and d.color=$color_id and b.body_part_id =$body_part_id  and d.detarmination_id='$fabric_description_id' and c.cons_uom = '$cons_uom' and e.booking_no = '$booking_no'  $room_rack_cond and a.entry_form = 18 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category =2 and c.transaction_type =2 $date_condition_2 $buyerId_cond  and e.id in($batch_id) $store_cond_2 group by a.issue_number, c.transaction_date,c.transaction_type,d.dia_width,c.store_id
					union all 
					SELECT a.issue_number as transaction_id, c.transaction_date,c.transaction_type, sum(c.cons_quantity) as quantity,d.dia_width as dia,c.store_id from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c , product_details_master d, pro_batch_create_mst e, wo_non_ord_samp_booking_mst f  where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and e.booking_no=f.booking_no and c.company_id in ($companyID) and d.color=$color_id and b.body_part_id =$body_part_id  and d.detarmination_id='$fabric_description_id' and c.cons_uom = '$cons_uom' and e.booking_no = '$booking_no'  $room_rack_cond and a.entry_form = 18 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category =2 and c.transaction_type =2 $date_condition_2 $buyerId_cond  and e.id in($batch_id) $store_cond_2 group by a.issue_number, c.transaction_date,c.transaction_type,d.dia_width,c.store_id"
					); 

				//**
				//
				//Remove the condition and a.knit_dye_source in(1,0)
				//
				//**

				//union all

				//and b.store_id= '$store_id'
				/*$rcv_issue_rtn_sqlx = sql_select("SELECT a.recv_number as transaction_id,b.transaction_date,a.receive_date,b.transaction_type, sum(b.cons_quantity) as quantity,c.width as dia,b.store_id from inv_receive_master a, pro_finish_fabric_rcv_dtls c,inv_transaction b, inv_issue_master d, pro_batch_create_mst e  where a.id = c.mst_id and c.trans_id = b.id and b.issue_id = d.id and b.transaction_type =4 and a.entry_form = 52 and a.item_category = 2 and b.pi_wo_batch_no = e.id and a.status_active =1 and c.status_active=1 and b.status_active =1 and b.company_id in ($companyID) $retrn_id_cond2 and e.booking_no = '$booking_no'  and c.color_id=$color_id and c.body_part_id= '$body_part_id' and c.gsm = '$gsm' $width_cond2 and b.cons_uom = '$cons_uom' $room_rack_cond2 $date_condition group by a.recv_number,a.receive_date,c.width,b.transaction_type,b.transaction_date,b.store_id"); */

				$rcv_issue_rtn_sqlx = sql_select("SELECT a.recv_number as transaction_id,b.transaction_date,a.receive_date,b.transaction_type, sum(b.cons_quantity) as quantity,c.width as dia,b.store_id from inv_receive_master a, pro_finish_fabric_rcv_dtls c,inv_transaction b, inv_issue_master d, pro_batch_create_mst e  where a.id = c.mst_id and c.trans_id = b.id and b.issue_id = d.id and b.transaction_type =4 and a.entry_form = 52 and a.item_category = 2 and b.pi_wo_batch_no = e.id and a.status_active =1 and c.status_active=1 and b.status_active =1 and b.company_id in ($companyID) $retrn_id_cond2 and e.booking_no = '$booking_no'  and c.color_id=$color_id and c.body_part_id= '$body_part_id' and c.fabric_description_id='$fabric_description_id' and b.cons_uom = '$cons_uom' $room_rack_cond2 $date_condition $store_cond group by a.recv_number,a.receive_date,c.width,b.transaction_type,b.transaction_date,b.store_id"); 

				//and c.store_id  = $store_id
				//union all
				/*$issue_trns_out_sql = sql_select("select a.transfer_system_id as transaction_id, c.transaction_date,c.transaction_type, sum(c.cons_quantity) as quantity,d.dia_width as dia,c.store_id from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($companyID) $trans_id_cond and c.item_category=2 and c.transaction_type=6 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306) and b.color_id = '$color_id' and b.body_part_id =$body_part_id and c.cons_uom = '$cons_uom' and e.booking_no = '$booking_no' and d.gsm='$gsm' $width_cond $room_rack_cond  $date_condition_2 group by a.transfer_system_id, c.transaction_date,c.transaction_type,d.dia_width,c.store_id");*/ //and d.dia_width = '$width'
				$issue_trns_out_sql = sql_select("SELECT a.transfer_system_id as transaction_id, c.transaction_date,c.transaction_type, sum(c.cons_quantity) as quantity,d.dia_width as dia,c.store_id from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($companyID) $trans_id_cond and c.item_category=2 and c.transaction_type=6 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306) and d.color = '$color_id' and b.body_part_id =$body_part_id  and d.detarmination_id='$fabric_description_id' and c.cons_uom = '$cons_uom' and e.booking_no = '$booking_no'  $room_rack_cond  $date_condition_2 $store_cond_2 group by a.transfer_system_id, c.transaction_date,c.transaction_type,d.dia_width,c.store_id");
			?>
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Total Issue Pop-up</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="100">Transaction Date</th>
						<th width="150">Transaction ID</th>
						<th width="110">Store Name</th>
						<th width="40">Dia</th>
						<th>Qty.</th>

					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($issue_sql as $row)
					{
						$date_frm=date('Y-m-d',strtotime($from_date));
						$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
						if( $from_date == "" || ( $from_date != "" && ($transaction_date >= $date_frm)))
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="100" align="center"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('transaction_id')]; ?></p></td>
								<td width="110" align="center"><p><? echo $store_arr[$row[csf('store_id')]]; ?></p></td>
								<td width="40" align="center"><p><? echo $row[csf('dia')]; ?></p></td>
								<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							</tr>
							<?
							$tot_issue_qty+=$row[csf('quantity')];
							$i++;
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_issue_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Transfer Out</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="100">Transaction Date</th>
						<th width="150">Transaction ID</th>
						<th width="110">Store Name</th>
						<th width="40">Dia</th>
						<th>Qty.</th>

					</tr>
				</thead>
				<tbody>
					<?
					$iii=1;		
					foreach($issue_trns_out_sql as $row)
					{
						$date_frm=date('Y-m-d',strtotime($from_date));
						$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
						if( $from_date == "" || ( $from_date != "" && ($transaction_date >= $date_frm)))
						{
							if ($iii%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $iii+200; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $iii+200;?>">
								<td width="30"><p><? echo $iii; ?></p></td>
								<td width="100" align="center"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('transaction_id')]; ?></p></td>
								<td width="110" align="center"><p><? echo $store_arr[$row[csf('store_id')]]; ?></p></td>
								<td width="40" align="center"><p><? echo $row[csf('dia')]; ?></p></td>
								<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							</tr>
							<?
							$tot_tram_out_qty+=$row[csf('quantity')];
							$iii++;
						}
					}
					
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_tram_out_qty,2); ?>&nbsp;</td>
					</tr>
					
				</tfoot>
			</table>
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Issue Rtn. Qnty</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="100">Transaction Date</th>
						<th width="150">Transaction ID</th>
						<th width="110">Store Name</th>
						<th width="40">Dia</th>
						<th>Qty.</th>

					</tr>
				</thead>
				<tbody>
					<?
					$ii=1;	
					foreach($rcv_issue_rtn_sqlx as $row)
					{
						$date_frm=date('Y-m-d',strtotime($from_date));
						$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
						if( $from_date == "" || ( $from_date != "" && ($transaction_date >= $date_frm)))
						{
							if ($ii%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $ii+100; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $ii+100;?>">
								<td width="30"><p><? echo $ii; ?></p></td>
								<td width="100" align="center"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('transaction_id')]; ?></p></td>
								<td width="110" align="center"><p><? echo $store_arr[$row[csf('store_id')]]; ?></p></td>
								<td width="40" align="center"><p><? echo $row[csf('dia')]; ?></p></td>
								<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							</tr>
							<?
							$tot_issue_rtn_qtyx+=$row[csf('quantity')];
							$ii++;
						}
					}
						$total_over_all_issue_qnty=$tot_issue_qty+$tot_tram_out_qty-$tot_issue_rtn_qtyx;
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_issue_rtn_qtyx,2); ?>&nbsp;</td>
					</tr>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Grand Total</td>
						<td align="right">&nbsp;<? echo number_format($total_over_all_issue_qnty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	exit();
}

if($action=="openmypage_balance_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<?
				//recv part
				//$ref_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];

				$prod_ref = explode("__", $prod_ref);
				//$prod_id = $prod_ref[0];
				//$store_id = $prod_ref[0];
				$body_part_id = $prod_ref[0];
				$fabric_description_id = $prod_ref[1];
				/*$gsm = $prod_ref[2];
				$width = $prod_ref[3];*/
				$color_id = $prod_ref[2];
				$cons_uom = $prod_ref[3];
				$booking_without_order = $prod_ref[4];
				$batch_id = chop($prod_ref[5],",");
				$floor_id = $prod_ref[6];
				$room = $prod_ref[7];
				$rack = $prod_ref[8];
				$self = $prod_ref[9];
				//$from_date

				$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
				$i=1;
				if($body_part_id!='') $body_part_cond=" and b.body_part_id='$body_part_id'"; else $body_part_cond="";
				//if($width!='') $width_cond=" and c.width='$width'"; else $width_cond="";
				if($prod_ref[8])
				{
					$room_rack_cond = " and b.floor_id='$floor_id' and b.room='$room' and b.rack='$rack' and b.self = '$self'";
				}
				if($to_date != "")
				{
					//$date_condition = " and b.transaction_date  between '".$from_date."' and '".$to_date."'";
					$date_condition   = " and b.transaction_date <= '$to_date'";
					$date_condition_2   = " and c.transaction_date <= '$to_date'";
				}
				if($transfer_in_ids!=""){$trans_in_id_cond = "and a.id in($transfer_in_ids)";}
				if($issue_return_id!=""){$retrn_id_cond = "and a.id in($issue_return_id)";}
				if($recv_return_id!=""){$retrn_id_cond2 = "and a.id in($recv_return_id)";}
		
					if($booking_without_order == "")
					{
						if($db_type==0){
							$booking_without_order_cond = " and a.booking_without_order='$booking_without_order'";
						}else{
							$booking_without_order_cond =" and a.booking_without_order is null";
						}
					}
					else
					{
						$booking_without_order_cond =" and a.booking_without_order='$booking_without_order'";
					}

					if($store_ids !=""){
						$store_cond = " and b.store_id in (".$store_ids.")";
					}

					if($buyerId)
					{
						$buyer_cond= " and f.buyer_id = $buyerId";
					}

					$rcv_sql = sql_select("SELECT a.recv_number as transaction_id,a.receive_date,c.width as dia, sum(b.cons_quantity) as quantity,b.transaction_type,b.transaction_date,b.store_id,b.rack,b.bin_box,e.id as batch_id,e.batch_no from inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c, pro_batch_create_mst e WHERE a.company_id in ($companyID) and a.id = b.mst_id and b.id = c.trans_id and b.transaction_type =1 and a.entry_form = 37 and a.status_active =1 and b.status_active =1 and c.status_active =1  and b.pi_wo_batch_no = e.id and e.booking_no = '$booking_no' and c.color_id=$color_id and c.body_part_id= '$body_part_id' and c.fabric_description_id='$fabric_description_id' and b.cons_uom = '$cons_uom' $room_rack_cond $date_condition $store_cond group by a.recv_number,a.receive_date,c.width,b.transaction_type,b.transaction_date,b.store_id,b.rack,b.bin_box,e.id,e.batch_no   
				  union all  
					SELECT a.recv_number as transaction_id,a.receive_date,c.width as dia, sum(b.cons_quantity) as quantity,b.transaction_type,b.transaction_date,b.store_id,b.rack,b.bin_box,e.id as batch_id,e.batch_no from inv_receive_master a, pro_finish_fabric_rcv_dtls c,inv_transaction b, inv_issue_master d, pro_batch_create_mst e  where a.id = c.mst_id and c.trans_id = b.id and b.issue_id = d.id and b.transaction_type =4 and a.entry_form = 52 and a.item_category = 2 and b.pi_wo_batch_no = e.id and a.status_active =1 and c.status_active=1 and b.status_active =1 and b.company_id in ($companyID) $retrn_id_cond and e.booking_no = '$booking_no' and c.color_id=$color_id and c.body_part_id= '$body_part_id' and c.fabric_description_id='$fabric_description_id' and b.cons_uom = '$cons_uom' $room_rack_cond $date_condition $store_cond group by a.recv_number,a.receive_date,c.width,b.transaction_type,b.transaction_date,b.store_id,b.rack,b.bin_box,e.id,e.batch_no   
					union all 
					SELECT a.transfer_system_id as transaction_id, a.transfer_date as receive_date,d.dia_width as dia, sum(b.cons_quantity) as quantity,b.transaction_type,b.transaction_date,b.store_id,b.rack,b.bin_box,e.id as batch_id,e.batch_no from inv_item_transfer_mst a, inv_item_transfer_dtls c, inv_transaction b , product_details_master d,  pro_batch_create_mst e where a.id=c.mst_id and c.to_trans_id=b.id and b.prod_id=d.id and b.pi_wo_batch_no=e.id and b.item_category=2 and b.transaction_type=5 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306) and b.company_id in ($companyID) $trans_in_id_cond and e.booking_no = '$booking_no' and c.color_id=$color_id and c.to_body_part= '$body_part_id' and d.detarmination_id='$fabric_description_id' and c.uom = '$cons_uom' $room_rack_cond $date_condition $store_cond group by  a.id,a.transfer_system_id, a.transfer_date,b.transaction_type,b.transaction_date,d.dia_width,b.store_id,b.rack,b.bin_box,e.id,e.batch_no ");

					//and a.booking_without_order='$booking_without_order'  and e.id in($batch_id)

				foreach($rcv_sql as $row)
				{

					$batch_arr[$row[csf('batch_id')]]["batch_no"]=$row[csf('batch_no')];
					$recv_qnty_arr[$row[csf('store_id')]."**".$row[csf('rack')]."**".$row[csf('bin_box')]."**".$batch_arr[$row[csf('batch_id')]]["batch_no"]."**".$row[csf('dia')]]["recvQnty"]+=$row[csf('quantity')];
				}

				$i=1;
				//if($width!='') $width_cond = " and d.dia_width='$width'"; else $width_cond = "";

				if($prod_ref[8])
				{
					$room_rack_cond = " and c.floor_id='$floor_id' and c.room='$room' and c.rack='$rack' and c.self = '$self'";
				}
				/*if($to_date != "")
				{
					$date_condition = " and c.transaction_date  between '".$from_date."' and '".$to_date."'";
				}*/
				if($transfer_out_ids!=""){$trans_out_id_cond = "and a.id in($transfer_out_ids)";}
				if($issue_return_id!=""){$retrn_id_cond = "and a.id in($issue_return_id)";}
				if($recv_return_id!=""){$retrn_id_cond2 = "and a.id in($recv_return_id)";}
				$store_arr=return_library_array( "select id,store_name  from lib_store_location", "id", "store_name");


				$issue_sql = sql_select("SELECT a.issue_number as transaction_id, c.transaction_date,c.transaction_type, sum(c.cons_quantity) as quantity,d.dia_width as dia,c.store_id,c.floor_id,c.room,c.rack,c.self,c.bin_box,e.id as batch_id,e.batch_no from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c , product_details_master d, pro_batch_create_mst e, wo_booking_mst f  where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and e.booking_no=f.booking_no and c.company_id in ($companyID) and d.color=$color_id and b.body_part_id =$body_part_id and d.detarmination_id='$fabric_description_id' and c.cons_uom = '$cons_uom' and e.booking_no = '$booking_no' $room_rack_cond and a.entry_form = 18 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category =2 and c.transaction_type =2 $date_condition_2 and e.id in($batch_id) $buyer_cond group by a.issue_number, c.transaction_date,c.transaction_type,d.dia_width,c.store_id,c.floor_id,c.room,c.rack,c.self,c.bin_box,e.id,e.batch_no 
				union all
				  select a.issue_number as transaction_id, c.transaction_date,c.transaction_type, sum(c.cons_quantity) as quantity,d.dia_width as dia,c.store_id,c.floor_id,c.room,c.rack,c.self,c.bin_box,e.id as batch_id,e.batch_no from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c , product_details_master d, pro_batch_create_mst e, wo_non_ord_samp_booking_mst f  where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and e.booking_no=f.booking_no and c.company_id in ($companyID) and d.color=$color_id and b.body_part_id =$body_part_id and d.detarmination_id='$fabric_description_id' and c.cons_uom = '$cons_uom' and e.booking_no = '$booking_no' $room_rack_cond and a.entry_form = 18 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category =2 and c.transaction_type =2 $date_condition_2 and e.id in($batch_id) $buyer_cond group by a.issue_number, c.transaction_date,c.transaction_type,d.dia_width,c.store_id,c.floor_id,c.room,c.rack,c.self,c.bin_box,e.id,e.batch_no
				union all
				select  a.issue_number as transaction_id, c.transaction_date,c.transaction_type, sum(c.cons_quantity) as quantity,d.dia_width as dia,c.store_id,c.floor_id,c.room,c.rack,c.self,c.bin_box,e.id as batch_id,e.batch_no from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and a.company_id in ($companyID)  and d.color = '$color_id' and b.body_part_id =$body_part_id and d.detarmination_id='$fabric_description_id' and c.cons_uom = '$cons_uom' and e.booking_no = '$booking_no' $room_rack_cond  and a.entry_form = 46 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category =2 and c.transaction_type =3 $date_condition_2 $retrn_id_cond2 and c.prod_id=d.id and c.pi_wo_batch_no=e.id group by a.issue_number, c.transaction_date,c.transaction_type,d.dia_width,c.store_id,c.floor_id,c.room,c.rack,c.self,c.bin_box,e.id,e.batch_no  
				union all
				select a.transfer_system_id as transaction_id, c.transaction_date,c.transaction_type, sum(c.cons_quantity) as quantity,d.dia_width as dia,c.store_id,c.floor_id,c.room,c.rack,c.self,c.bin_box,e.id as batch_id,e.batch_no from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($companyID) $trans_out_id_cond and c.item_category=2 and c.transaction_type=6 $date_condition_2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306) and b.color_id = '$color_id' and b.body_part_id =$body_part_id and d.detarmination_id='$fabric_description_id' and c.cons_uom = '$cons_uom' and e.booking_no = '$booking_no'  $room_rack_cond group by a.transfer_system_id, c.transaction_date,c.transaction_type,d.dia_width,c.store_id,c.floor_id,c.room,c.rack,c.self,c.bin_box,e.id,e.batch_no");

				//and a.knit_dye_source in(1,0,3)
			
				foreach($issue_sql as $row)
				{
					$store_id.=$row[csf('store_id')].",";
					$floor_id.=$row[csf('floor_id')].",";
					$room.=$row[csf('room')].",";
					$rack.=$row[csf('rack')].",";
					$self.=$row[csf('self')].",";
					$binBox.=$row[csf('bin_box')].",";
					$issue_qnty_arr[$row[csf('store_id')]."**".$row[csf('rack')]."**".$row[csf('bin_box')]."**".$batch_arr[$row[csf('batch_id')]]["batch_no"]."**".$row[csf('dia')]]["issueQnty"]+=$row[csf('quantity')];
				}

				$store_id=chop($store_id,",");
				$floor_id=chop($floor_id,",");
				$room=chop($room,",");
				$rack=chop($rack,",");
				$self=chop($self,",");
				$binBox=chop($binBox,",");

				//print_r($issue_qnty_arr);

				if($rack==""){$rack=0;}
				if($self==""){$self=0;}
				if($room==""){$room=0;}
				if($store_id==""){$store_id=0;}
				if($floor_id==""){$floor_id=0;}

				$lib_rack_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and b.store_id in($store_id) and b.floor_id in($floor_id) and b.room_id in($room) and b.rack_id in($rack) and b.shelf_id in($self) order by b.floor_id", "rack_id","floor_room_rack_name" );
				$lib_bin_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and b.store_id in($store_id) and b.floor_id in($floor_id) and b.room_id in($room) and b.rack_id in($rack) and b.shelf_id in($self) order by b.floor_id", "bin_id","floor_room_rack_name" );
			?>
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Total Stock Pop-up</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="100">Store Name</th>
						<th width="100">Rack Name</th>
						<th width="100">Bin Box</th>
						<th width="100">Batch No</th>
						<th width="50">Dia</th>
						<th>Qty.</th>
					</tr>
				</thead>
				<tbody>
					<?

					foreach($recv_qnty_arr  as $key => $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$row_datas 	=explode("**", $key);
						$storeName 	=$store_arr[$row_datas[0]];
						$rackName 	=$lib_rack_arr[$row_datas[1]];
						$binBoxName =$lib_bin_arr[$row_datas[2]];
						$batchNamex =$batch_arr[$row_datas[3]]["batch_no"];
						$batchName 	=$row_datas[3];
						$dia 		=$row_datas[4];
						$recvQnt 	=$row['recvQnty'];
						$issueQnt 	=$issue_qnty_arr[$key]["issueQnty"];
						$Balance 	=$recvQnt-$issueQnt;

						if($Balance >0)
						{
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="100" align="center"><p><? echo $storeName; ?></p></td>
								<td width="100" align="center"><p><? echo $rackName; ?></p></td>
								<td width="100" align="center"><p><? echo $binBoxName; ?></p></td>
								<td width="100" align="center" title="<? echo $row_datas[3]; ?>"><p><? echo $batchName; ?></p></td>
								<td width="50" align="center"><p><? echo $dia; ?></p></td>
								<td align="right"><p><? echo number_format($Balance,2); ?></p></td>
							</tr>
							<?
								$tot_balance_qty+=$Balance;
							$i++;
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="6" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_balance_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
		<?
		exit();

}

?>