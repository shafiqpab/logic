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

/*if($action=="job_no_popup")
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'job_order_yarn_issue_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	if($db_type==0){ $insert_year="year(insert_date)";	$insert_month="month(insert_date)";}
	if($db_type==2){  $insert_year="to_char(insert_date,'yyyy')";  $insert_month="to_char(insert_date,'mm')";}
	if($year_id!=0) $year_cond=" and $insert_year=$year_id"; else $year_cond="";
	if($month_id!=0) $month_cond=" and $insert_month=$month_id"; else $month_cond="";
	
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	if($db_type==0) $insert_year="year(insert_date)";	
	if($db_type==2) $insert_year="to_char(insert_date,'yyyy')";
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $insert_year as year from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond $month_cond order by job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	
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
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
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
		$('#order_no_id').val( id );
		$('#order_no_val').val( ddd );
	} 
		  
	</script>
     <input type="hidden" id="order_no_id" />
     <input type="hidden" id="order_no_val" />
 <?

	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and b.buyer_name=$data[1]";
	if ($data[2]=="") $order_no=""; else $order_no=" and a.po_number=$data[2]";
	$job_no=str_replace("'","",$txt_job_id);
	if ($data[2]=="") $job_no_cond="";  
    else
	 { 
	    if($db_type==0) $job_no_cond="  and FIND_IN_SET(b.job_no,'$data[2]')";
		if($db_type==2) $job_no_cond="  and b.job_no_prefix_num in($data[2])";
	 }
	
	
	$sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a  where b.job_no=a.job_no_mst and b.company_name=$data[0] and b.is_deleted=0 $buyer_name $job_no_cond ORDER BY b.job_no";
	
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$arr=array(1=>$buyer);
	
	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,200","620","400",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "job_order_yarn_issue_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','',1) ;
	disconnect($con);
	exit();
}*/

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
$other_party_arr=return_library_array( "select id,other_party_name from lib_other_party", "id", "other_party_name");

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if(str_replace("'","",$cbo_issue_purpose)!="" && str_replace("'","",$cbo_issue_purpose)!=0) $issue_purpose_cond=" and e.issue_purpose=$cbo_issue_purpose";
	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and c.buyer_name=$cbo_buyer_id";
	
	$year_id=str_replace("'","",$cbo_year);
	$month_id=str_replace("'","",$cbo_month);
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(c.insert_date)=$year_id"; else $year_cond="";
		if($month_id!=0) $month_cond=" and month(c.insert_date)=$month_id"; else $month_cond="";
	}
	elseif($db_type==2)
	{
		if($year_id!=0) $year_cond=" and to_char(c.insert_date,'yyyy')=$year_id"; else $year_cond="";
		if($month_id!=0) $month_cond=" and to_char(c.insert_date,'mm')=$month_id"; else $month_cond="";
	}
	$txt_search_comm=str_replace("'","",$txt_search_comm);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$search_cond='';
	if ($txt_search_comm=="") 
	{
		$search_cond.="";
	}
	else
	{
		if($cbo_search_by==1) $search_cond.=" and c.job_no_prefix_num in ($txt_search_comm) ";
		else if($cbo_search_by==2) $search_cond.=" and c.style_ref_no LIKE '%$txt_search_comm%'";
		else if($cbo_search_by==3) $search_cond.=" and b.po_number LIKE '%$txt_search_comm%'";
		else if($cbo_search_by==4) $search_cond.=" and b.file_no LIKE '%$txt_search_comm%'";
		else if($cbo_search_by==5) $search_cond.=" and b.grouping LIKE '%$txt_search_comm%'";
		else if($cbo_search_by==6 && $txt_search_comm !="") 
		{
			$booking_po_array = return_library_array( "SELECT b.po_break_down_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no_prefix_num =$txt_search_comm and a.status_active=1 and a.is_deleted=0", "po_break_down_id", "po_break_down_id");
			// print_r($booking_po_array);die();
			$booking_po = implode(",", $booking_po_array);
			$search_cond.=" and b.id in($booking_po)";
		}
		else if($cbo_search_by==7 && $txt_search_comm !="") 
		{
			$wo_po_array = return_library_array( "SELECT c.id from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, wo_po_break_down c where a.id=b.mst_id and b.job_no=c.job_no_mst and a.yarn_dyeing_prefix_num = $txt_search_comm and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0", "id", "id");
			// print_r($wo_po_array);die();
			$wo_po = implode(",", $wo_po_array);
			$search_cond.=" and b.id in($wo_po)";
		}
	}
	// echo $search_cond;die();
	// $booking_array = return_library_array("SELECT po_break_down_id,booking_no from wo_booking_dtls where status_active=1 and is_deleted=0", "po_break_down_id", "booking_no");
 	ob_start();
	?>
    <fieldset style="width:1930px;">
        <table cellpadding="0" cellspacing="0" width="1900">
            <tr  class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="20" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
            </tr>
            <tr  class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
            </tr>
        </table>
        <table width="1917" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
            <thead>
                <th width="30">SL</th>
                <th width="100">Job No</th>
                <th width="60">Buyer</th>
                <th width="100">Booking No</th>
                <th width="100">Style No</th>
                <th width="100">File No</th>
                <th width="100">Ref. No</th>
                <th width="100">Order no.</th>
                <th width="100">Supplier</th>
                <th width="50">Count</th>
                <th width="80">Yarn Brand</th>
                <th width="80">Color</th>
                <th width="90">Type</th>
                <th width="80">Lot No</th>
                <th width="180">Yarn Comp.</th>
                <th width="80">Total Issued/ Received</th> 
                <th width="80">In-house Qty.</th>  
                <th width="80">Outside Qty.</th>
                <th width="140">Issue To</th>
                <th width="">Comments</th>
            </thead>
        </table>
        <div style="width:1930px; overflow-y: scroll; max-height:350px;" id="scroll_body">
			<table width="1900" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
                <tbody>
                	<tr><td colspan="20" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b>Issued For Yarn Dyeing<?php //echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></b></td></tr>
                <?
				$i=1;
				$booking_qty=0;
				$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
				$product_array=array();
				$sql_data="Select id, yarn_count_id, brand, yarn_type, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, lot, color from product_details_master where item_category_id=1 and company_id=$cbo_company_id and status_active=1 and is_deleted=0";
				$result_sql_data=sql_select($sql_data);
				foreach($result_sql_data as $rows)
				{
					$product_array[$rows[csf('id')]]['arr_id']=$rows[csf('id')];
					$product_array[$rows[csf('id')]]['yarn_count_id']=$rows[csf('yarn_count_id')];
					$product_array[$rows[csf('id')]]['yarn_type']=$rows[csf('yarn_type')];
					$product_array[$rows[csf('id')]]['lot']=$rows[csf('lot')];
					$product_array[$rows[csf('id')]]['yarn_comp_type1st']=$rows[csf('yarn_comp_type1st')];
					$product_array[$rows[csf('id')]]['yarn_comp_percent1st']=$rows[csf('yarn_comp_percent1st')];
					$product_array[$rows[csf('id')]]['yarn_comp_type2nd']=$rows[csf('yarn_comp_type2nd')];
					$product_array[$rows[csf('id')]]['yarn_comp_percent2nd']=$rows[csf('yarn_comp_percent2nd')];
					$product_array[$rows[csf('id')]]['brand']=$rows[csf('brand')];
					$product_array[$rows[csf('id')]]['color']=$rows[csf('color')];
				}

				if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond_rec=" and a.buyer_id=$cbo_buyer_id";
				$year_id=str_replace("'","",$cbo_year);;
				$month_id=str_replace("'","",$cbo_month);;
				if($db_type==0)
				{
					if($year_id!=0) $year_cond_rec=" and year(d.insert_date)=$year_id"; else $year_cond_rec="";
					if($month_id!=0) $month_cond_rec=" and month(d.insert_date)=$month_id"; else $month_cond_rec="";
				}
				if($db_type==2)
				{
					if($year_id!=0) $year_cond_rec=" and to_char(d.insert_date,'yyyy')=$year_id"; else $year_cond_rec="";
					if($month_id!=0) $month_cond_rec=" and to_char(d.insert_date,'mm')=$month_id"; else $month_cond_rec="";
				}
				
				$search_cond_rec='';
				if ($txt_search_comm=="") 
				{
					$search_cond_rec.="";
				}
				else
				{
					if($cbo_search_by==1) $search_cond_rec.=" and d.job_no_prefix_num in ($txt_search_comm) ";
					else if($cbo_search_by==2) $search_cond_rec.=" and d.style_ref_no LIKE '%$txt_search_comm%'";
					else if($cbo_search_by==3) $search_cond_rec.=" and c.po_number LIKE '%$txt_search_comm%'";
					else if($cbo_search_by==4) $search_cond_rec.=" and c.file_no LIKE '%$txt_search_comm%'";
					else if($cbo_search_by==5) $search_cond_rec.=" and c.grouping LIKE '%$txt_search_comm%'";
					else if($cbo_search_by==6 && $txt_search_comm !="") 
					{
						$booking_po_array = return_library_array( "SELECT b.po_break_down_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no_prefix_num =$txt_search_comm and a.status_active=1 and a.is_deleted=0", "po_break_down_id", "po_break_down_id");
						$booking_po = implode(",", $booking_po_array);
						$search_cond_rec.=" and c.id in($booking_po)";
					}
					else if($cbo_search_by==7 && $txt_search_comm !="") 
					{
						$search_cond_rec.=" and e.yarn_dyeing_prefix_num ='$txt_search_comm'";
					}
				}
			
				if(str_replace("'","",$cbo_issue_purpose)!="" && str_replace("'","",$cbo_issue_purpose)!=0) $issue_purpose_cond_issue=" and a.issue_purpose=$cbo_issue_purpose";
				$sql_dye_issue ="SELECT g.id as prop_id, d.job_no, d.buyer_name as buyer_id, c.po_number,c.file_no, c.grouping, b.prod_id, d.style_ref_no, b.brand_id, b.supplier_id, f.fab_booking_no, a.knit_dye_source,a.knit_dye_company, g.quantity,a.id as issue_id,e.id as dyeing_id from inv_issue_master a, inv_transaction b, order_wise_pro_details g, wo_po_break_down c, wo_po_details_master d, wo_yarn_dyeing_mst e, wo_yarn_dyeing_dtls f where a.id = b.mst_id and b.id = g.trans_id and g.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.booking_id =e.id and e.id =f.mst_id and a.entry_form=3 and b.transaction_type=2 and a.issue_purpose=2 and g.entry_form =3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and g.status_active =1 and f.status_active =1 and d.company_name=$cbo_company_id $buyer_id_cond_rec $search_cond_rec $issue_purpose_cond_issue $year_cond_rec $month_cond_rec";
				//echo $sql_dye_issue;
				$result_issue=sql_select($sql_dye_issue);
				foreach ($result_issue as $row) 
				{
					if($propChk[$row[csf('prop_id')]] =="")
					{
						$row[csf('knit_dye_source')]."*".$row[csf('knit_dye_company')];

						$propChk[$row[csf('prop_id')]] = $row[csf('prop_id')];
						$prod_supplier_brand = $row[csf('prod_id')]."*".$row[csf('supplier_id')]."*".$row[csf('brand_id')]."*".$row[csf('knit_dye_source')]."*".$row[csf('knit_dye_company')];
						$source_data_issue[$row[csf('job_no')]][$prod_supplier_brand]['quantity']+=$row[csf('quantity')];
						$job_order_ref_issue[$row[csf('job_no')]]['buyer_id'] 	= $row[csf('buyer_id')];
						$job_order_ref_issue[$row[csf('job_no')]]['dyeing_id'] 	= $row[csf('dyeing_id')];
						$job_order_ref_issue[$row[csf('job_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
						$job_order_ref_issue[$row[csf('job_no')]]['po_number'] 	.= $row[csf('po_number')].",";
						$job_order_ref_issue[$row[csf('job_no')]]['file_no'] 		.= $row[csf('file_no')].",";
						$job_order_ref_issue[$row[csf('job_no')]]['grouping'] 	.= $row[csf('grouping')].",";
						$job_order_ref_issue[$row[csf('job_no')]]['booking_no'] 	.= $row[csf('fab_booking_no')].",";

						if($row[csf('knit_dye_source')] == 1){
							$source_data_issue[$row[csf('job_no')]][$prod_supplier_brand]['inhouse']+=$row[csf('quantity')];
						}else{
							$source_data_issue[$row[csf('job_no')]][$prod_supplier_brand]['outbound']+=$row[csf('quantity')];
						}
					}

					$issue_id_arr[] = $row[csf('issue_id')];
				}
				unset($propChk);
				unset($result_issue);

				$issue_id_string = implode(',',array_unique($issue_id_arr));
		
				if($issue_id_string!="")
				{
					//if($grey_product_id_string_cond!="")
					//{
						$issueRet_arr_sql= "SELECT c.id as trans_id, c.quantity as issue_ret_qnty, b.booking_id, d.job_no_mst from inv_transaction a, inv_receive_master b, order_wise_pro_details c, wo_po_break_down d where a.mst_id = b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and b.entry_form = 9 and a.item_category=1 and a.transaction_type=4 and b.receive_basis=1 and a.company_id = $cbo_company_id and a.issue_id in($issue_id_string) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and c.entry_form = 9 and c.trans_type = 4 ";
						//echo $issueRet_arr_sql;
						$issue_return_Data_Arr=sql_select($issueRet_arr_sql);

						$issueRet_arr=array(); $trans_check=array();
						foreach($issue_return_Data_Arr as $val)
						{
							if($trans_check[$val[csf("trans_id")]]=="")
							{
								$issueRet_arr[$val[csf('job_no_mst')]][$val[csf('booking_id')]]+=$val[csf('issue_ret_qnty')];
								$trans_check[$val[csf("trans_id")]]= $val[csf("trans_id")];
							}
						}
					//}
					
				}  

				foreach ($source_data_issue as $job_no => $job_data) 
				{
					$row_no=0;
					foreach ($job_data as $ref_str => $row) 
					{
						$row_no++;
					}
					$job_td_span[$job_no] = $row_no;
				}

				$issue_purpose_array=array();
				$grand_tot_qnty_issued=0;  $grand_tot_qnty_balance=0; $grand_tot_qnty=0;  $grand_tot_qnty_cotton=0; $grand_tot_qnty_other=0; $grand_tot_qnty_inside=0;  $grand_tot_qnty_outside=0;
				$issue_purpose_total_issued=0;
				foreach ($source_data_issue as $job_no => $job_data) 
				{
					$z=1;
					foreach ($job_data as $ref_str => $row) 
					{
						$strArr = explode("*", $ref_str);
						$prod_id = $strArr[0];
						$supplier_id = $strArr[1];
						$brand_id = $strArr[2];
						$knit_source_id = $strArr[3];
						$knit_company_id = $strArr[4];
						if($knit_source_id==1) {$issue_to = $company_arr[$knit_company_id];}else{$issue_to = $supplier_arr[$knit_company_id];}
						$rowspan= $job_td_span[$job_no];
						$buyer_id = $job_order_ref_issue[$job_no]['buyer_id'];
						$dyeing_id = $job_order_ref_issue[$job_no]['dyeing_id'];

						$issue_qnty=$row['quantity'] - $issueRet_arr[$job_no][$dyeing_id];

						$style_ref_no = $job_order_ref_issue[$job_no]['style_ref_no'];
						$po_number = implode(',',array_unique(explode(',',chop($job_order_ref_issue[$job_no]['po_number'],","))));
						$file_no = implode(',',array_unique(explode(',',chop($job_order_ref_issue[$job_no]['file_no'],","))));
						$grouping = implode(',',array_unique(explode(',',chop($job_order_ref_issue[$job_no]['grouping'],","))));
						$booking_no = implode(',',array_unique(explode(',',chop($job_order_ref_issue[$job_no]['booking_no'],","))));

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if ($product_array[$prod_id]['yarn_comp_type1st']!=0 && $product_array[$prod_id]['yarn_comp_percent1st']!=0 && $product_array[$prod_id]['yarn_comp_type2nd']!=0 && $product_array[$prod_id]['yarn_comp_percent2nd']!=0)
						{
							$yarn_comp_per=$composition[$product_array[$prod_id]['yarn_comp_type1st']].' '.$product_array[$prod_id]['yarn_comp_percent1st'].'%,'.$composition[$product_array[$prod_id]['yarn_comp_type2nd']].' '.$product_array[$prod_id]['yarn_comp_percent2nd'].'% ';
						}
						else if ($product_array[$prod_id]['yarn_comp_type1st']!=0 && $product_array[$prod_id]['yarn_comp_percent1st']!=0 && $product_array[$prod_id]['yarn_comp_type2nd']==0 && $product_array[$prod_id]['yarn_comp_percent2nd']==0)
						{
							$yarn_comp_per=$composition[$product_array[$prod_id]['yarn_comp_type1st']].' '.$product_array[$prod_id]['yarn_comp_percent1st'].'%';
						}
						else
						{
							$yarn_comp_per="";
						}
										?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<?
							if($z==1)
							{
								?>
								<td width="30" rowspan="<? echo $rowspan; ?>"><? echo $i; ?></td>
								<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $job_no; ?></p></td>
								<td width="60" rowspan="<? echo $rowspan; ?>"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
								<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $booking_no; ?></p></td>
								<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $style_ref_no; ?></p></td>
								<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $file_no; ?></p></td>
								<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $grouping; ?></p></td>
								<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $po_number;?></p></td>
								<?
							}
							?>
								<td width="100"><p><? echo $supplier_arr[$supplier_id]; ?></p></td>
								<td width="50"><p><? echo $count_arr[$product_array[$prod_id]['yarn_count_id']]; ?></p></td>
								<td width="80"><p><? echo $brand_arr[$brand_id]; ?></p></td>
								<td width="80"><p><? echo $color_arr[$product_array[$prod_id]['color']]; ?></p></td>
								<td width="90"><p><? echo $yarn_type[$product_array[$prod_id]['yarn_type']]; ?></p></td>
								<td width="80"><p><? echo $product_array[$prod_id]['lot']; ?></p></td>
								<td width="180" ><p><? echo $yarn_comp_per; ?></p></td>
								<td width="80" align="right"><? echo number_format($issue_qnty,2); ?></td>
								<td width="80" align="right"><? echo number_format($row['inhouse'],2); ?></td>  
								<td width="80" align="right"><? echo number_format($row['outbound'],2); ?></td>
								<td width="140"><p><? echo $issue_to; ?></p></td>
								<td width=""><p><? //echo ?>&nbsp;</p></td>
							</tr>
								
						<?
						$issue_purpose_total_issued+=$issue_qnty;
						$issue_purpose_total_inside+=$row['inhouse'];
						$issue_purpose_total_outside+=$row['outbound'];

						$z++;$i++;
					}
				}
				unset($source_data_issue);
				unset($job_order_ref_issue);
				unset($job_td_span);

					?>
	            <tr class="tbl_bottom">
	                <td colspan="15" align="right"><b>Issued For Yarn Dyeing Total</b></td>
	                <td align="right"><? echo number_format($issue_purpose_total_issued,2,'.',''); ?></td>
	                <td align="right"><? echo number_format($issue_purpose_total_inside,2,'.',''); ?></td>
	                <td align="right"><? echo number_format($issue_purpose_total_outside,2,'.',''); ?></td>
	                <td>&nbsp;</td>
	                <td>&nbsp;</td>
	            </tr>
            
            <tr><td colspan="20" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b>Dyed Yarn Received</b></td></tr>
			<?
			if(str_replace("'","",$cbo_issue_purpose)!="" && str_replace("'","",$cbo_issue_purpose)!=0) $issue_purpose_cond_rec=" and a.receive_purpose=$cbo_issue_purpose";
			$sql_rec =" select d.job_no, d.buyer_name as buyer_id, c.po_number,c.file_no, c.grouping, b.prod_id, b.id as trans_id, b.cons_quantity, d.style_ref_no, b.brand_id, b.supplier_id, e.pay_mode, f.fab_booking_no from inv_receive_master a, inv_transaction b, wo_po_break_down c, wo_po_details_master d, wo_yarn_dyeing_mst e, wo_yarn_dyeing_dtls f where a.id=b.mst_id and c.job_no_mst=d.job_no and b.job_no=d.job_no and a.booking_id = e.id and e.id = f.mst_id and a.item_category=1 and b.item_category=1 and b.transaction_type=1 and a.entry_form=1 and a.receive_purpose=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_deleted=0 and a.company_id=$cbo_company_id $buyer_id_cond_rec $search_cond_rec $issue_purpose_cond_rec $year_cond_rec $month_cond_rec order by d.job_no";
			
			$result_rec=sql_select($sql_rec);
			foreach ($result_rec as $row) 
			{
				if($transChk[$row[csf('trans_id')]] =="")
				{
					$transChk[$row[csf('trans_id')]] = $row[csf('trans_id')];
					if($row[csf('pay_mode')] == 3 || $row[csf('pay_mode')] == 5)
					{
						$supplier_name= $company_arr[$row[csf('supplier_id')]];
					}
					else{
						$supplier_name= $supplier_arr[$row[csf('supplier_id')]];
					}

					$prod_supplier_brand = $row[csf('prod_id')]."*".$supplier_name."*".$row[csf('brand_id')];
					$source_data[$row[csf('job_no')]][$prod_supplier_brand]['quantity']+=$row[csf('cons_quantity')];
					$job_order_ref[$row[csf('job_no')]]['buyer_id'] 	= $row[csf('buyer_id')];
					$job_order_ref[$row[csf('job_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
					$job_order_ref[$row[csf('job_no')]]['po_number'] 	.= $row[csf('po_number')].",";
					$job_order_ref[$row[csf('job_no')]]['file_no'] 		.= $row[csf('file_no')].",";
					$job_order_ref[$row[csf('job_no')]]['grouping'] 	.= $row[csf('grouping')].",";
					$job_order_ref[$row[csf('job_no')]]['booking_no'] 	.= $row[csf('fab_booking_no')].",";

					if($row[csf('pay_mode')] == 3 || $row[csf('pay_mode')] == 5){
						$source_data[$row[csf('job_no')]][$prod_supplier_brand]['inhouse']+=$row[csf('cons_quantity')];
					}else{
						$source_data[$row[csf('job_no')]][$prod_supplier_brand]['outbound']+=$row[csf('cons_quantity')];
					}
				}
			}

			foreach ($source_data as $job_no => $job_data) 
			{
				$row_no=0;
				foreach ($job_data as $ref_str => $row) 
				{
					$row_no++;
				}
				$job_td_span[$job_no] = $row_no;
			}

			foreach ($source_data as $job_no => $job_data) 
			{
				$z=1;
				foreach ($job_data as $ref_str => $row) 
				{
					$strArr = explode("*", $ref_str);
					$prod_id = $strArr[0];
					$supplier_id = $strArr[1];
					$brand_id = $strArr[2];
					$rowspan= $job_td_span[$job_no];
					$buyer_id = $job_order_ref[$job_no]['buyer_id'];
					$style_ref_no = $job_order_ref[$job_no]['style_ref_no'];
					$po_number = implode(',',array_unique(explode(',',chop($job_order_ref[$job_no]['po_number'],","))));
					$file_no = implode(',',array_unique(explode(',',chop($job_order_ref[$job_no]['file_no'],","))));
					$grouping = implode(',',array_unique(explode(',',chop($job_order_ref[$job_no]['grouping'],","))));
					$booking_no = implode(',',array_unique(explode(',',chop($job_order_ref[$job_no]['booking_no'],","))));

					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if ($product_array[$prod_id]['yarn_comp_type1st']!=0 && $product_array[$prod_id]['yarn_comp_percent1st']!=0 && $product_array[$prod_id]['yarn_comp_type2nd']!=0 && $product_array[$prod_id]['yarn_comp_percent2nd']!=0)
					{
						$yarn_comp_per=$composition[$product_array[$prod_id]['yarn_comp_type1st']].' '.$product_array[$prod_id]['yarn_comp_percent1st'].'%,'.$composition[$product_array[$prod_id]['yarn_comp_type2nd']].' '.$product_array[$prod_id]['yarn_comp_percent2nd'].'% ';
					}
					else if ($product_array[$prod_id]['yarn_comp_type1st']!=0 && $product_array[$prod_id]['yarn_comp_percent1st']!=0 && $product_array[$prod_id]['yarn_comp_type2nd']==0 && $product_array[$prod_id]['yarn_comp_percent2nd']==0)
					{
						$yarn_comp_per=$composition[$product_array[$prod_id]['yarn_comp_type1st']].' '.$product_array[$prod_id]['yarn_comp_percent1st'].'%';
					}
					else
					{
						$yarn_comp_per="";
					}
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<?
						if($z==1)
						{
							?>
							<td width="30" rowspan="<? echo $rowspan; ?>"><? echo $i; ?></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $job_no; ?></p></td>
							<td width="60" rowspan="<? echo $rowspan; ?>"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $booking_no; ?></p></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $style_ref_no; ?></p></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $file_no; ?></p></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $grouping; ?></p></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $po_number;?></p></td>
							<?
						}
						?>
							<td width="100"><p><? echo $supplier_id; ?></p></td>
							<td width="50"><p><? echo $count_arr[$product_array[$prod_id]['yarn_count_id']]; ?></p></td>
							<td width="80"><p><? echo $brand_arr[$brand_id]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$product_array[$prod_id]['color']]; ?></p></td>
							<td width="90"><p><? echo $yarn_type[$product_array[$prod_id]['yarn_type']]; ?></p></td>
							<td width="80"><p><? echo $product_array[$prod_id]['lot']; ?></p></td>
							<td width="180" ><p><? echo $yarn_comp_per; ?></p></td>
							<td width="80" align="right"><? echo number_format($row['quantity'],2); ?></td>
							<td width="80" align="right"><? echo number_format($row['inhouse'],2); ?></td>  
							<td width="80" align="right"><? echo number_format($row['outbound'],2); ?></td>
							<td width="140"><p>&nbsp;</p></td>
							<td width=""><p>&nbsp;</p></td>
						</tr>
					<?
					$issue_purpose_total_rec+=$row['quantity'];
					$issue_purpose_total_inside_rec+=$row['inhouse'];
					$issue_purpose_total_outside_rec+=$row['outbound'];
					$z++;$i++;
				}
			}
			?>                    
            <tr class="tbl_bottom">
                <td colspan="15" align="right"><b>Dyed Yarn Received Total</b></td>
                <td align="right"><? echo number_format($issue_purpose_total_rec,2,'.','');?></td>
                <td align="right"><? echo number_format($issue_purpose_total_inside_rec,2,'.','');?></td>
                <td align="right"><? echo number_format($issue_purpose_total_outside_rec,2,'.',''); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr class="tbl_bottom">
                <td colspan="15" align="right"><b>Receive Balance / Process Loss Qty</b></td>
                <td align="right"><? $balnace_process_loss=$issue_purpose_total_issued-$issue_purpose_total_rec; echo number_format($balnace_process_loss,2,'.',''); ?></td>

                <td align="right"><? $balance_inside = $issue_purpose_total_inside-$issue_purpose_total_inside_rec; echo number_format($balance_inside,2,'.',''); ?></td>
                <td align="right"><? $balance_outside = $issue_purpose_total_outside-$issue_purpose_total_outside_rec; echo number_format($balance_outside,2,'.',''); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr class="tbl_bottom">
                <td colspan="15" align="right"><b>Receive Balance / Process Loss %</b></td>
                <td align="right"><? $balnace_process_loss_per=($balnace_process_loss/$issue_purpose_total_issued)*100; echo number_format($balnace_process_loss_per,2,'.','').'%'; ?></td>
                <td align="right">
                	<? 
                	if($issue_purpose_total_inside>0)
                	{
                		$balnace_process_loss_per_inside=($balance_inside/$issue_purpose_total_inside)*100; 
                	}
                	echo number_format($balnace_process_loss_per_inside,2,'.','').'%'; ?>
                </td>
                <td align="right"><? 
                	if($issue_purpose_total_outside>0)
                	{
                		$balnace_process_loss_per_outside=($balance_outside/$issue_purpose_total_outside)*100; 
                	}
                	echo number_format($balnace_process_loss_per_outside,2,'.','').'%'; ?>
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr><td colspan="20" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b>Issued For Knitting (Less Issued Return)</b></td></tr>
            <?
            $return_qty_array=array();
            $ret_sql="SELECT c.prod_id, c.po_breakdown_id,
                 sum(case when a.knitting_source=1 then c.quantity end ) as inside_return,
                 sum(case when a.knitting_source=3 then c.quantity end ) as outside_return
                 from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and c.trans_id!=0 and a.entry_form=9 and c.entry_form=9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.prod_id, c.po_breakdown_id"; //,sum(case when a.knitting_source in(1,3) then b.cons_quantity end ) as return_qnty
            $ret_sql_result=sql_select($ret_sql); 
            foreach($ret_sql_result as $row)
            {
                $return_qty_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['inside_return']=$row[csf('inside_return')];
                $return_qty_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['outside_return']=$row[csf('outside_return')];
                $return_qty_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['return_qnty']=($row[csf('inside_return')]+$row[csf('outside_return')]);
            }
				
			if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond_knit=" and c.buyer_name=$cbo_buyer_id";
			$year_id=str_replace("'","",$cbo_year);;
			$month_id=str_replace("'","",$cbo_month);;
			
			$search_cond_knit='';
			if ($txt_search_comm=="") 
			{
				$search_cond_knit.="";
			}
			else
			{
				if($cbo_search_by==1) $search_cond_knit.=" and c.job_no_prefix_num in ($txt_search_comm) ";
				else if($cbo_search_by==2) $search_cond_knit.=" and c.style_ref_no LIKE '%$txt_search_comm%'";
				else if($cbo_search_by==3) $search_cond_knit.=" and b.po_number LIKE '%$txt_search_comm%'";
				else if($cbo_search_by==4) $search_cond_knit.=" and b.file_no LIKE '%$txt_search_comm%'";
				else if($cbo_search_by==5) $search_cond_knit.=" and b.grouping LIKE '%$txt_search_comm%'";
				else if($cbo_search_by==6 && $txt_search_comm !="") 
				{
					$booking_po_array = return_library_array( "SELECT b.po_break_down_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no_prefix_num =$txt_search_comm and a.status_active=1 and a.is_deleted=0", "po_break_down_id", "po_break_down_id");
					// print_r($booking_po_array);die();
					$booking_po = implode(",", $booking_po_array);
					$search_cond_knit.=" and b.id in($booking_po)";
				}
				else if($cbo_search_by==7 && $txt_search_comm !="") 
				{
					$wo_po_array = return_library_array( "SELECT c.id from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, wo_po_break_down c where a.id=b.mst_id and b.job_no=c.job_no_mst and a.yarn_dyeing_prefix_num = $txt_search_comm and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0", "id", "id");
					// print_r($wo_po_array);die();
					$wo_po = implode(",", $wo_po_array);
					$search_cond_knit.=" and b.id in($wo_po)";
				}
			}
			
			if(str_replace("'","",$cbo_issue_purpose)!="" && str_replace("'","",$cbo_issue_purpose)!=0) $issue_purpose_cond_knit=" and e.issue_purpose=$cbo_issue_purpose";
			if($db_type==0)
			{	
				if($year_id!=0) $year_cond_knit=" and year(c.insert_date)=$year_id"; else $year_cond_knit="";
				if($month_id!=0) $month_cond_knit=" and month(c.insert_date)=$month_id"; else $month_cond_knit="";
				$sql_knitting="SELECT a.id, a.trans_id, a.trans_type, a.po_breakdown_id, group_concat(concat_ws('**',a.prod_id,a.quantity)) as prod_id, sum(a.quantity) as quantity, group_concat(distinct(b.po_number)) as po_number,group_concat(distinct(b.file_no)) as file_no, group_concat(distinct(b.grouping)) as grouping, c.job_no, c.buyer_name, c.style_ref_no,
				 group_concat(case when a.entry_form ='3' and e.knit_dye_source=1 then concat_ws('**',a.prod_id,a.quantity) end ) as issue_inside,
				 group_concat(case when a.entry_form ='3' and e.knit_dye_source=3 then concat_ws('**',a.prod_id,a.quantity) end ) as issue_outside,
				 d.brand_id,
				 group_concat(case when a.entry_form ='3' then concat_ws('**',a.prod_id,a.quantity) end ) as issue_qnty,
				 e.issue_number, e.issue_purpose, e.knit_dye_source, e.knit_dye_company, e.company_id, e.booking_id, e.booking_no,d.supplier_id
				 from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_issue_master e 
				 where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.trans_id=d.id and d.mst_id=e.id and d.item_category=1 and a.trans_type=2 and e.entry_form=3 and e.issue_purpose=1 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.company_name=$cbo_company_id $buyer_id_cond_knit $search_cond $issue_purpose_cond_knit $year_cond_knit $month_cond_knit group by c.job_no, c.buyer_name, c.style_ref_no, d.supplier_id order by c.job_no";
					
			}
			if($db_type==2)
			{	
				if($year_id!=0) $year_cond_knit=" and to_char(c.insert_date,'yyyy')=$year_id"; else $year_cond_knit="";
				if($month_id!=0) $month_cond_knit=" and to_char(c.insert_date,'mm')=$month_id"; else $month_cond_knit="";
				/*
				$sql_knitting="SELECT 
				listagg((a.po_breakdown_id),',') within group (order by a.po_breakdown_id) as po_breakdown_id,
				listagg((a.prod_id ||'**' || a.quantity),',') within group (order by a.prod_id)  as prod_id,
				sum(a.quantity) as quantity,
				listagg((cast(b.po_number as varchar2(4000))),',') within group (order by b.po_number) as po_number,
				listagg(b.file_no,',') within group (order by b.file_no) as file_no,
				listagg(b.grouping,',') within group (order by b.grouping) as grouping,
				c.job_no, c.buyer_name as buyer_name, c.style_ref_no as style_ref_no,
				listagg((case when a.entry_form ='3' and e.knit_dye_source=1 then (a.prod_id || '**' || a.quantity) end ),',') within group (order by a.prod_id) as issue_inside,
				listagg((case when a.entry_form ='3' and e.knit_dye_source=3 then (a.prod_id || '**' || a.quantity) end ),',') within group (order by a.prod_id) as issue_outside,
				max(d.brand_id) as brand_id,
				listagg((case when a.entry_form ='3' then (a.prod_id || '**' || a.quantity) end ),',') within group (order by a.prod_id) as issue_qnty,
				listagg((cast(e.issue_purpose as varchar2(4000))),',') within group (order by e.issue_purpose) as issue_purpose,
				max(e.knit_dye_source) as knit_dye_source, max(e.knit_dye_company) as knit_dye_company, max(e.company_id) as company_id, max(e.booking_id) as booking_id, max(e.booking_no) as booking_no,d.supplier_id
				from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_issue_master e 
				where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.trans_id=d.id and d.mst_id=e.id and d.item_category=1 and a.trans_type=2 and e.issue_purpose=1 and e.entry_form=3 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.company_name=$cbo_company_id $buyer_id_cond_knit $search_cond $issue_purpose_cond_knit $year_cond_knit $month_cond_knit 
				group by c.job_no, c.buyer_name, c.style_ref_no, d.supplier_id
				order by c.job_no";
				*/
			
				$sql_knitting="
					SELECT 
						RTRIM(XMLAGG(XMLELEMENT(e,a.po_breakdown_id,',').EXTRACT('//text()') ORDER BY a.po_breakdown_id).GETCLOBVAL(),',') AS po_breakdown_id,
						RTRIM(XMLAGG(XMLELEMENT(e,a.prod_id ||'**' || a.quantity,',').EXTRACT('//text()') ORDER BY A.prod_id).GETCLOBVAL(),',') AS prod_id,
						sum(a.quantity) as quantity,
						RTRIM(XMLAGG(XMLELEMENT(e,b.po_number,',').EXTRACT('//text()') ORDER BY b.po_number).GETCLOBVAL(),',') AS po_number,
						RTRIM(XMLAGG(XMLELEMENT(e,b.file_no,',').EXTRACT('//text()') ORDER BY b.file_no).GETCLOBVAL(),',') AS file_no,
						RTRIM(XMLAGG(XMLELEMENT(e,b.grouping,',').EXTRACT('//text()') ORDER BY b.grouping).GETCLOBVAL(),',') AS grouping,
						c.job_no, c.buyer_name as buyer_name, c.style_ref_no as style_ref_no,
						RTRIM(XMLAGG(XMLELEMENT(e,(case when a.entry_form ='3' and e.knit_dye_source=1 then (a.prod_id|| '**' || a.quantity) end ),',').EXTRACT('//text()') ORDER BY a.prod_id).GETCLOBVAL(),',') as issue_inside,
						RTRIM(XMLAGG(XMLELEMENT(e,(case when a.entry_form ='3' and e.knit_dye_source=3 then (a.prod_id|| '**' || a.quantity) end ),',').EXTRACT('//text()') ORDER BY a.prod_id).GETCLOBVAL(),',') as issue_outside,
						max(d.brand_id) as brand_id,
						RTRIM(XMLAGG(XMLELEMENT(e,(case when a.entry_form ='3' then (a.prod_id|| '**' || a.quantity) end ),',').EXTRACT('//text()') ORDER BY a.prod_id).GETCLOBVAL(),',') as issue_qnty,
						RTRIM(XMLAGG(XMLELEMENT(e,e.issue_purpose,',').EXTRACT('//text()') ORDER BY e.issue_purpose).GETCLOBVAL(),',') AS issue_purpose,
						max(e.knit_dye_source) as knit_dye_source, max(e.knit_dye_company) as knit_dye_company, max(e.company_id) as company_id, max(e.booking_id) as booking_id, max(e.booking_no) as booking_no,d.supplier_id
					FROM
						order_wise_pro_details a,
						wo_po_break_down b,
						wo_po_details_master c,
						inv_transaction d,
						inv_issue_master e 
					WHERE
						a.po_breakdown_id=b.id 
						and b.job_no_mst=c.job_no 
						and a.trans_id=d.id 
						and d.mst_id=e.id 
						and d.item_category=1 
						and a.trans_type=2 
						and e.issue_purpose=1 
						and e.entry_form=3 
						and a.entry_form=3 
						and a.status_active=1 
						and a.is_deleted=0 
						and b.status_active=1 
						and c.status_active=1 
						and c.is_deleted=0 
						and d.status_active=1 
						and d.is_deleted=0 
						and e.status_active=1 
						and e.is_deleted=0 
						and c.company_name=$cbo_company_id 
						$buyer_id_cond_knit 
						$search_cond 
						$issue_purpose_cond_knit 
						$year_cond_knit 
						$month_cond_knit 
					GROUP BY
						c.job_no,
						c.buyer_name,
						c.style_ref_no,
						d.supplier_id
					ORDER BY
						c.job_no
				";
			}
			//echo $sql_knitting;//die; //and d.prod_id=21959 
			$result_knitting=sql_select($sql_knitting);
			$issue_purpose_total_issued=0; 

			foreach($result_knitting as $row)
			{
				if($row[csf('knit_dye_source')]==3)
				{
					$knit_dye_source=$supplier_arr[$row[csf('knit_dye_company')]];
				}
				else
				{
					$knit_dye_source="";
				}
				
				//04.03.2020 by Zaman
				$row[csf('po_breakdown_id')] = $row[csf('po_breakdown_id')]->load();
				$row[csf('prod_id')] = $row[csf('prod_id')]->load();
				$row[csf('po_number')] = $row[csf('po_number')]->load();
				$row[csf('file_no')] = $row[csf('file_no')]->load();
				$row[csf('grouping')] = $row[csf('grouping')]->load();
				$row[csf('issue_inside')] = $row[csf('issue_inside')]->load();
				$row[csf('issue_outside')] = $row[csf('issue_outside')]->load();
				$row[csf('issue_qnty')] = $row[csf('issue_qnty')]->load();
				$row[csf('issue_purpose')] = $row[csf('issue_purpose')]->load();
				//end
				
				$prod_id=explode(',',$row[csf('prod_id')]);
				$issue_inside=explode(',',$row[csf('issue_inside')]);
				$issue_outside=explode(',',$row[csf('issue_outside')]);
				$issue_qty=explode(',',$row[csf('issue_qnty')]);

				$prod_dataArray=array();
				foreach( $prod_id as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$prod_dataArray[$id]+=$qnty;
					//print_r($val).',';
				}
				$qtyinArray=array();
				foreach( $issue_inside as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$qtyinArray[$id]+=$qnty;
					//print_r($val).',';
				}
				$qtyoutArray=array();
				foreach( $issue_outside as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$qtyoutArray[$id]+=$qnty;
					//print_r($val).',';
				}
				$issueqtyArray=array();
				foreach( $issue_qty as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$issueqtyArray[$id]+=$qnty;
					//print_r($val).',';
				}

				/*$insidereturnArray=array();
				foreach( $inside_return as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$insidereturnArray[$id]+=$qnty;
					//print_r($val).',';
				}

				$outsidereturnArray=array();
				foreach( $outside_return as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$outsidereturnArray[$id]+=$qnty;
					//print_r($val).',';
				}
				
				$returnqtyArray=array();
				foreach( $return_qty as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$returnqtyArray[$id]+=$qnty;
					//print_r($val).',';
				}*/

				//$booking_no=$row[csf("booking_no")];
				//$booking_tot_qnty_row=0; 
				$issue_purpose_total_knit=0; $issue_purpose_total_inside_knit=0;$issue_purpose_total_outside_knit=0;
				
				$z=0; $rowspan=count($prod_dataArray);
				foreach($prod_dataArray as $key=>$value)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//$booking_qty=return_field_value("sum(grey_fab_qnty) as grey_fab_qnty","wo_booking_dtls","booking_no='$booking_no'","grey_fab_qnty");
					if ($product_array[$key]['yarn_comp_type1st']!=0 && $product_array[$key]['yarn_comp_percent1st']!=0 && $product_array[$key]['yarn_comp_type2nd']!=0 && $product_array[$key]['yarn_comp_percent2nd']!=0)
					{
						$yarn_comp_per=$composition[$product_array[$key]['yarn_comp_type1st']].' '.$product_array[$key]['yarn_comp_percent1st'].'%,'.$composition[$product_array[$key]['yarn_comp_type2nd']].' '.$product_array[$key]['yarn_comp_percent2nd'].'% ';
					}
					else if ($product_array[$key]['yarn_comp_type1st']!=0 && $product_array[$key]['yarn_comp_percent1st']!=0 && $product_array[$key]['yarn_comp_type2nd']==0 && $product_array[$key]['yarn_comp_percent2nd']==0)
					{
						$yarn_comp_per=$composition[$product_array[$key]['yarn_comp_type1st']].' '.$product_array[$key]['yarn_comp_percent1st'].'%';
					}
					else
					{
						$yarn_comp_per="";
					}
					$po_id_ret=implode(',',array_unique(explode(',',$row[csf('po_breakdown_id')])));
					$issue_purpose=implode(',',array_unique(explode(',',$row[csf('issue_purpose')])));
					
				?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<?
						if($z==0)
						{
						?>
							<td width="30" rowspan="<? echo $rowspan; ?>"><? echo $i; ?></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $row[csf('job_no')]; ?></p></td>
							<td width="60" rowspan="<? echo $rowspan; ?>"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $row[csf('booking_no')]; ?></p></td>
                            <td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo implode(',',array_unique(explode(',',$row[csf('style_ref_no')]))); ?></p></td>
                            <td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo implode(',',array_unique(explode(',',$row[csf('file_no')]))); ?></p></td>
                            <td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo implode(',',array_unique(explode(',',$row[csf('grouping')]))); ?></p></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo implode(',',array_unique(explode(',',$row[csf('po_number')]))); ?></p></td>
						<?
						}
						?>
                        <td width="100" ><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
						<td width="50"><p><? echo $count_arr[$product_array[$key]['yarn_count_id']]; ?></p></td>
						<td width="80"><p><? echo $brand_arr[$product_array[$key]['brand']]; ?></p></td>
                        <td width="80"><p><? echo $color_arr[$product_array[$key]['color']]; ?></p></td>
						<td width="90"><p><? echo $yarn_type[$product_array[$key]['yarn_type']]; ?></p></td>
						<td width="80"><p><? echo $product_array[$key]['lot']; ?></p></td>
						<td width="180" ><p><? echo $yarn_comp_per; ?></p></td>
                        <td width="80" align="right">
                        	<a href="##" onClick="openmypage_job('<? echo $row[csf('po_breakdown_id')]; ?>','yarn_issue','<? echo $product_array[$key]['yarn_count_id']; ?>','<? echo $product_array[$key]['yarn_comp_type1st']; ?>','<? echo $product_array[$key]['yarn_comp_percent1st']; ?>','<? echo $product_array[$key]['yarn_comp_type2nd']; ?>','<? echo $product_array[$key]['yarn_comp_percent2nd']; ?>','<? echo $product_array[$key]['yarn_type']; ?>','<? echo $product_array[$key]['lot']; ?>',<? echo $issue_purpose; ?>)">
							<? 
							$return_qnty = 0; $inside_return = 0 ; $outside_return = 0;
							if($po_id_ret != "")
							{
								foreach (explode(",",$po_id_ret) as $po_id)
								{
									$return_qnty += $return_qty_array[$po_id][$key]['return_qnty'];
									$inside_return += $return_qty_array[$po_id][$key]['inside_return'];
									$outside_return += $return_qty_array[$po_id][$key]['outside_return'];
								}
							}
							// $issue_qty=$issueqtyArray[$key]-$return_qnty;//$return_qty_array[$po_id_ret][$key]['return_qnty']; 
							//echo $issueqtyArray[$key]."-".$return_qnty;
							$inside_tot_qty=$qtyinArray[$key]-$inside_return;
							$outside_tot_qty=$qtyoutArray[$key]-$outside_return;
							$issue_qty = $inside_tot_qty+$outside_tot_qty;
							echo number_format($issue_qty,2); 
							$issued_tot_qnty_knit+=$issue_qty; 
							?>
						</a>
						</td>
                        <td width="80" align="right">
                        	<? 
                        		// $inside_tot_qty=$qtyinArray[$key]-$inside_return;//$return_qty_array[$po_id_ret][$key]['inside_return']; 
                        		echo number_format($inside_tot_qty,2); $inside_total_qnty+=$inside_tot_qty; 
                        	?>
                        </td>  
						<td width="80" align="right">
							<? 
								// $outside_tot_qty=$qtyoutArray[$key]-$outside_return;//$return_qty_array[$po_id_ret][$key]['outside_return']; 
								echo number_format($outside_tot_qty,2); $outside_total_qnty+=$outside_tot_qty; 
							?>
						</td>
						<td width="140"><p><? if($qtyoutArray[$key]!=0)  echo $supplier_arr[$row[csf('knit_dye_company')]]; ?></p></td>
						<td width=""><p><? //echo ?>&nbsp;</p></td>
					</tr>
					<?
					$issue_purpose_total_knit+=$issue_qty;
					$issue_purpose_total_inside_knit+=$inside_tot_qty;
					$issue_purpose_total_outside_knit+=$outside_tot_qty;
					$z++;
				}
				?>
					<tr class="tbl_bottom">
						<td colspan="9" align="right"><b>&nbsp;</b></td>
						<td colspan="6" align="right"><b>Job Total</b></td>
                        <td align="right"><? echo number_format($issue_purpose_total_knit,2,'.',''); $grand_tot_qnty_knit+=$issue_purpose_total_knit; ?></td>
						<td align="right"><? echo number_format($issue_purpose_total_inside_knit,2,'.',''); $grand_tot_qnty_inside_knit+=$issue_purpose_total_inside_knit; ?></td>
						<td align="right"><? echo number_format($issue_purpose_total_outside_knit,2,'.',''); $grand_tot_qnty_outside_knit+=$issue_purpose_total_outside_knit;?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<?
				$i++;
			}
			?>
			<tr class="tbl_bottom">
				<td colspan="15" align="right"><b>Issued For Knitting (Less Issued Return) Total</b></td>
				<td align="right"><? echo number_format($grand_tot_qnty_knit,2,'.','');//unset($issue_purpose_total_issued); ?></td>
				<!--<td>&nbsp;</td>-->
				<td align="right"><? echo number_format($grand_tot_qnty_inside_knit,2,'.','');//unset($issue_purpose_total_inside); ?></td>
				<td align="right"><? echo number_format($grand_tot_qnty_outside_knit,2,'.','');//unset($issue_purpose_total_outside); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr><td colspan="20" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b>Issue For Sample (With Order)</b></td></tr>
			<?	
			if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond_sample=" and c.buyer_name=$cbo_buyer_id";
			$year_id=str_replace("'","",$cbo_year);;
			$month_id=str_replace("'","",$cbo_month);;
		
			if(str_replace("'","",$cbo_issue_purpose)!="" && str_replace("'","",$cbo_issue_purpose)!=0) $issue_purpose_cond_sample=" and e.issue_purpose=$cbo_issue_purpose";
			if($db_type==0)
			{
				if($year_id!=0) $year_cond_sample=" and year(c.insert_date)=$year_id"; else $year_cond_sample="";
				if($month_id!=0) $month_cond_sample=" and month(c.insert_date)=$month_id"; else $month_cond_sample="";						
				$sql_sample="SELECT a.id, a.trans_id, a.trans_type, a.po_breakdown_id, group_concat(concat_ws('**',a.prod_id,a.quantity)) as prod_id, sum(a.quantity) as quantity, group_concat(distinct(b.po_number)) as po_number, group_concat(distinct(b.file_no)) as file_no, group_concat(distinct(b.grouping)) as grouping, c.job_no, c.buyer_name, c.style_ref_no,
				 d.brand_id, group_concat(case when a.entry_form ='3' and e.knit_dye_source=1 then concat_ws('**',a.prod_id,a.quantity) end ) as issue_inside, group_concat(case when a.entry_form ='3' and e.knit_dye_source=3 then concat_ws('**',a.prod_id,a.quantity) end ) as issue_outside, d.brand_id,group_concat(case when a.entry_form ='3' then concat_ws('**',a.prod_id,a.quantity) end ) as issue_qnty,
				 e.issue_number, e.issue_purpose, e.knit_dye_source, e.knit_dye_company, e.company_id, e.booking_id, e.booking_no,d.supplier_id
				 from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_issue_master e 
				 where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.trans_id=d.id and d.mst_id=e.id and d.item_category=1 and d.transaction_type=2 and e.entry_form=3 and e.issue_purpose=4 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.company_name=$cbo_company_id $buyer_id_cond_sample $search_cond $issue_purpose_cond_sample $year_cond_sample $month_cond_sample group by e.issue_purpose, c.job_no, c.buyer_name, c.style_ref_no, d.supplier_id order by e.issue_purpose,c.job_no";
			}
			elseif($db_type==2)
		    {	
				if($year_id!=0) $year_cond_sample=" and to_char(c.insert_date,'yyyy')=$year_id"; else $year_cond_sample="";
				if($month_id!=0) $month_cond_sample=" and to_char(c.insert_date,'mm')=$month_id"; else $month_cond_sample="";	
				/*				
				$sql_sample="SELECT listagg((a.po_breakdown_id),',') within group (order by a.po_breakdown_id) as po_breakdown_id,
				listagg((a.prod_id|| '**' || a.quantity),',') within group (order by a.prod_id) as prod_id, sum(a.quantity)  as quantity,
				listagg((case when a.entry_form ='3' and e.knit_dye_source=1 then (a.prod_id|| '**' || a.quantity) end ),',') within group (order by a.prod_id) as issue_inside, 
				listagg((case when a.entry_form ='3' and e.knit_dye_source=3 then (a.prod_id|| '**' || a.quantity) end ),',') within group (order by a.prod_id) as issue_outside, 
				listagg((case when a.entry_form ='3' then (a.prod_id|| '**' || a.quantity) end ),',') within group (order by a.prod_id) as issue_qnty,
				listagg((b.po_number),',') within group (order by b.po_number) as po_number, 
				listagg(b.file_no,',') within group (order by b.file_no) as file_no,
				listagg(b.grouping,',') within group (order by b.grouping) as grouping,
				c.job_no, c.buyer_name as buyer_name, c.style_ref_no as style_ref_no, max(d.brand_id) as brand_id,
				e.issue_purpose, listagg((e.knit_dye_source),',') within group (order by e.knit_dye_source) as knit_dye_source,
				listagg((e.knit_dye_company),',') within group (order by e.knit_dye_company) as knit_dye_company,
				listagg((e.company_id),',') within group (order by e.company_id) as company_id,
				listagg((e.booking_id),',') within group (order by e.booking_id) as booking_id, 
				listagg((e.booking_no),',') within group (order by e.booking_no) as booking_no,
				d.supplier_id
				
				from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_issue_master e 
				where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.trans_id=d.id and d.mst_id=e.id and d.item_category=1 and d.transaction_type=2 and e.entry_form=3 and e.issue_purpose=4 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.company_name=$cbo_company_id $buyer_id_cond_sample $search_cond $issue_purpose_cond_sample $year_cond_sample $month_cond_sample 
				group by e.issue_purpose, c.job_no, c.buyer_name, c.style_ref_no, d.supplier_id
				order by e.issue_purpose, c.job_no ";
				*/
				
				$sql_sample="
					SELECT
						RTRIM(XMLAGG(XMLELEMENT(e,a.po_breakdown_id,',').EXTRACT('//text()') ORDER BY a.po_breakdown_id).GETCLOBVAL(),',') AS po_breakdown_id,
						RTRIM(XMLAGG(XMLELEMENT(e,a.prod_id || '**'|| a.quantity,',').EXTRACT('//text()') ORDER BY a.prod_id).GETCLOBVAL(),',') AS prod_id,
						sum(a.quantity)  as quantity,
						RTRIM(XMLAGG(XMLELEMENT(e,(case when a.entry_form ='3' and e.knit_dye_source=1 then (a.prod_id|| '**' || a.quantity) end ),',').EXTRACT('//text()') ORDER BY a.prod_id).GETCLOBVAL(),',') as issue_inside,
						RTRIM(XMLAGG(XMLELEMENT(e,(case when a.entry_form ='3' and e.knit_dye_source=3 then (a.prod_id|| '**' || a.quantity) end ),',').EXTRACT('//text()') ORDER BY a.prod_id).GETCLOBVAL(),',') as issue_outside,						 
						RTRIM(XMLAGG(XMLELEMENT(e,(case when a.entry_form ='3' then (a.prod_id|| '**' || a.quantity) end ),',').EXTRACT('//text()') ORDER BY a.prod_id).GETCLOBVAL(),',') as issue_qnty,						 
						RTRIM(XMLAGG(XMLELEMENT(e,b.po_number,',').EXTRACT('//text()') ORDER BY b.po_number).GETCLOBVAL(),',') AS po_number,
						RTRIM(XMLAGG(XMLELEMENT(e,b.file_no,',').EXTRACT('//text()') ORDER BY b.file_no).GETCLOBVAL(),',') AS file_no,
						RTRIM(XMLAGG(XMLELEMENT(e,b.grouping,',').EXTRACT('//text()') ORDER BY b.grouping).GETCLOBVAL(),',') AS grouping,
						c.job_no, c.buyer_name as buyer_name, c.style_ref_no as style_ref_no, max(d.brand_id) as brand_id, e.issue_purpose,
						listagg((e.knit_dye_source),',') within group (order by e.knit_dye_source) as knit_dye_source,
						listagg((e.knit_dye_company),',') within group (order by e.knit_dye_company) as knit_dye_company,
						listagg((e.company_id),',') within group (order by e.company_id) as company_id,
						listagg((e.booking_id),',') within group (order by e.booking_id) as booking_id, 
						listagg((e.booking_no),',') within group (order by e.booking_no) as booking_no,
						d.supplier_id
						
					FROM
						order_wise_pro_details a,
						wo_po_break_down b,
						wo_po_details_master c,
						inv_transaction d,
						inv_issue_master e 
					WHERE
						a.po_breakdown_id=b.id 
						and b.job_no_mst=c.job_no 
						and a.trans_id=d.id 
						and d.mst_id=e.id 
						and d.item_category=1 
						and d.transaction_type=2 
						and e.entry_form=3 
						and e.issue_purpose=4 
						and a.entry_form=3 
						and a.status_active=1 
						and a.is_deleted=0 
						and b.status_active=1 
						and c.status_active=1 
						and c.is_deleted=0 
						and d.status_active=1 
						and d.is_deleted=0 
						and e.status_active=1 
						and e.is_deleted=0 
						and c.company_name=$cbo_company_id 
						$buyer_id_cond_sample 
						$search_cond 
						$issue_purpose_cond_sample 
						$year_cond_sample 
						$month_cond_sample 
					GROUP BY
						e.issue_purpose,
						c.job_no,
						c.buyer_name,
						c.style_ref_no,
						d.supplier_id
					ORDER BY
						e.issue_purpose,
						c.job_no
				";						
		    }
			 			
				//echo $sql_sample;die;
				$result_sample=sql_select($sql_sample);
				foreach($result_sample as $row)
				{
					if($row[csf('knit_dye_source')]==3)
					{
						$knit_dye_source=$supplier_arr[$row[csf('knit_dye_company')]];
					}
					else
					{
						$knit_dye_source="";
					}
					
					//04.03.2020 by Zaman
					$row[csf('po_breakdown_id')] = $row[csf('po_breakdown_id')]->load();
					$row[csf('prod_id')] = $row[csf('prod_id')]->load();
					$row[csf('issue_inside')] = $row[csf('issue_inside')]->load();
					$row[csf('issue_outside')] = $row[csf('issue_outside')]->load();
					$row[csf('issue_qnty')] = $row[csf('issue_qnty')]->load();
					$row[csf('po_number')] = $row[csf('po_number')]->load();
					$row[csf('file_no')] = $row[csf('file_no')]->load();
					$row[csf('grouping')] = $row[csf('grouping')]->load();
					//end
					
					$prod_id=explode(',',$row[csf('prod_id')]);
					$issue_inside=explode(',',$row[csf('issue_inside')]);
					$issue_outside=explode(',',$row[csf('issue_outside')]);
					$issue_qty=explode(',',$row[csf('issue_qnty')]);

					$prod_dataArray=array();
					foreach( $prod_id as $val)
					{
						$id_val=explode('**',$val);
						$id=$id_val[0];
						$qnty=$id_val[1];
						$prod_dataArray[$id]+=$qnty;
						//print_r($val).',';
					}
					$qtyinArray=array();
					foreach( $issue_inside as $val)
					{
						$id_val=explode('**',$val);
						$id=$id_val[0];
						$qnty=$id_val[1];
						$qtyinArray[$id]+=$qnty;
						//print_r($val).',';
					}
					$qtyoutArray=array();
					foreach( $issue_outside as $val)
					{
						$id_val=explode('**',$val);
						$id=$id_val[0];
						$qnty=$id_val[1];
						$qtyoutArray[$id]+=$qnty;
						//print_r($val).',';
					}
					$issueqtyArray=array();
					foreach( $issue_qty as $val)
					{
						$id_val=explode('**',$val);
						$id=$id_val[0];
						$qnty=$id_val[1];
						$issueqtyArray[$id]+=$qnty;
						//print_r($val).',';
					}

					$booking_no=$row[csf("booking_no")];
					//$booking_tot_qnty_row=0; 
					$issue_purpose_total_issued_sample=0; $issue_purpose_total_inside_sample=0; $issue_purpose_total_outside_sample=0;
					
					$z=0; $rowspan=count($prod_dataArray);
					foreach($prod_dataArray as $key=>$value)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$booking_qty=return_field_value("sum(grey_fab_qnty) as grey_fab_qnty","wo_booking_dtls","booking_no='$booking_no'","grey_fab_qnty");
						if ($product_array[$key]['yarn_comp_type1st']!=0 && $product_array[$key]['yarn_comp_percent1st']!=0 && $product_array[$key]['yarn_comp_type2nd']!=0 && $product_array[$key]['yarn_comp_percent2nd']!=0)
						{
							$yarn_comp_per=$composition[$product_array[$key]['yarn_comp_type1st']].' '.$product_array[$key]['yarn_comp_percent1st'].'%,'.$composition[$product_array[$key]['yarn_comp_type2nd']].' '.$product_array[$key]['yarn_comp_percent2nd'].'% ';
						}
						else if ($product_array[$key]['yarn_comp_type1st']!=0 && $product_array[$key]['yarn_comp_percent1st']!=0 && $product_array[$key]['yarn_comp_type2nd']==0 && $product_array[$key]['yarn_comp_percent2nd']==0)
						{
							$yarn_comp_per=$composition[$product_array[$key]['yarn_comp_type1st']].' '.$product_array[$key]['yarn_comp_percent1st'].'%';
						}
						else
						{
							$yarn_comp_per="";
						}
						
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<?
							if($z==0)
							{
								?>
								<td width="30" rowspan="<? echo $rowspan; ?>"><? echo $i; ?></td>
								<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $row[csf('job_no')]; ?></p></td>
								<td width="60" rowspan="<? echo $rowspan; ?>"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
								<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $row[csf('booking_no')]; ?></p></td>
                                <td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo implode(',',array_unique(explode(',',$row[csf('style_ref_no')]))); ?></p></td>
                                <td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo implode(',',array_unique(explode(',',$row[csf('file_no')]))); ?></p></td>
                                <td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo implode(',',array_unique(explode(',',$row[csf('grouping')]))); ?></p></td>
								<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo implode(',',array_unique(explode(',',$row[csf('po_number')]))); ?></p></td>
								<?
							}
							?>
                            <td width="100" ><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
							<td width="50"><p><? echo $count_arr[$product_array[$key]['yarn_count_id']]; ?></p></td>
							<td width="80"><p><? echo $brand_arr[$product_array[$key]['brand']]; ?></p></td>
                            <td width="80"><p><? echo $color_arr[$product_array[$key]['color']]; ?></p></td>
							<td width="90"><p><? echo $yarn_type[$product_array[$key]['yarn_type']]; ?></p></td>
							<td width="80"><p><? echo $product_array[$key]['lot']; ?></p></td>
							<td width="180" ><p><? echo $yarn_comp_per; ?></p></td>
                            <td width="80" align="right"><? echo number_format($issueqtyArray[$key],2); $issued_tot_qnty+=$issueqtyArray[$key]; ?></td>
							<td width="80" align="right"><? echo number_format($qtyinArray[$key],2); $inside_tot_qnty+=$qtyinArray[$key]; ?></td>  
							<td width="80" align="right"><? echo number_format($qtyoutArray[$key],2); $outside_tot_qnty+=$qtyoutArray[$key]; ?></td>
							<td width="140"><p><? if($qtyoutArray[$key]!=0)  echo $supplier_arr[$row[csf('knit_dye_company')]]; ?></p></td>
							<td width=""><p><? //echo ?>&nbsp;</p></td>
						</tr>
						<?
						$issue_purpose_total_issued_sample+=$issueqtyArray[$key];
						$issue_purpose_total_inside_sample+=$qtyinArray[$key];
						$issue_purpose_total_outside_sample+=$qtyoutArray[$key];
						$z++;
					}
					?>
						<tr class="tbl_bottom">
							<td colspan="9" align="right"><b>&nbsp;</b></td>
							<td colspan="6" align="right"><b>Job Total</b></td>
                            <td align="right"><? echo number_format($issue_purpose_total_issued_sample,2,'.',''); $grand_tot_qnty_issued_sample+=$issue_purpose_total_issued_sample; ?></td>
							<td align="right"><? echo number_format($issue_purpose_total_inside_sample,2,'.',''); $grand_tot_qnty_inside_sample+=$issue_purpose_total_inside_sample; ?></td>
							<td align="right"><? echo number_format($issue_purpose_total_outside_sample,2,'.',''); $grand_tot_qnty_outside_sample+=$issue_purpose_total_outside_sample; ?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<?
					$i++;
					
				}
				?>
				<tr class="tbl_bottom">
					<td colspan="15" align="right"><b>Purpose Total</b></td>
					<td align="right"><? echo number_format($grand_tot_qnty_issued_sample,2,'.','');//unset($issue_purpose_total_issued); ?></td>
					<!--<td>&nbsp;</td>-->
					<td align="right"><? echo number_format($grand_tot_qnty_inside_sample,2,'.','');//unset($issue_purpose_total_inside); ?></td>
					<td align="right"><? echo number_format($grand_tot_qnty_outside_sample,2,'.','');//unset($issue_purpose_total_outside); ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>					
				<tr class="tbl_bottom">
					<td colspan="15" align="right"><b>Total Issued (Knitting + Sample With Order)</b></td>
					<td align="right"><? $total_issued=$grand_tot_qnty_knit+$grand_tot_qnty_issued_sample; echo number_format($total_issued,2,'.',''); ?></td>
					<!--<td>&nbsp;</td>-->
					<td align="right"><? $total_issue_in=$grand_tot_qnty_inside_knit+$grand_tot_qnty_inside_sample; echo number_format($total_issue_in,2,'.',''); ?></td>
					<td align="right"><? $total_issue_out=$grand_tot_qnty_outside_knit+$grand_tot_qnty_outside_sample; echo number_format($total_issue_out,2,'.',''); ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>					
            </table> 
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
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if($action=="report_generate_challan")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if(str_replace("'","",$cbo_issue_purpose)!="" && str_replace("'","",$cbo_issue_purpose)!=0) $issue_purpose_cond=" and e.issue_purpose=$cbo_issue_purpose";
	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and c.buyer_name=$cbo_buyer_id";
	
	$year_id=str_replace("'","",$cbo_year);;
	$month_id=str_replace("'","",$cbo_month);;

	if($year_id!=0) $year_cond=" and year(c.insert_date)=$year_id"; else $year_cond="";
	if($month_id!=0) $month_cond=" and month(c.insert_date)=$month_id"; else $month_cond="";
	
	$txt_search_comm=str_replace("'","",$txt_search_comm);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$search_cond='';
	if ($txt_search_comm=="") 
	{
		$search_cond.="";
	}
	else
	{
		if($cbo_search_by==1) $search_cond.=" and c.job_no_prefix_num in ($txt_search_comm) ";
		else if($cbo_search_by==2) $search_cond.=" and c.style_ref_no LIKE '%$txt_search_comm%'";
		else if($cbo_search_by==3) $search_cond.=" and b.po_number LIKE '%$txt_search_comm%'";
		else if($cbo_search_by==4) $search_cond.=" and b.file_no LIKE '%$txt_search_comm%'";
		else if($cbo_search_by==5) $search_cond.=" and b.grouping LIKE '%$txt_search_comm%'";
		else if($cbo_search_by==6 && $txt_search_comm !="") 
		{
			$booking_po_array = return_library_array( "SELECT b.po_break_down_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no_prefix_num =$txt_search_comm and a.status_active=1 and a.is_deleted=0", "po_break_down_id", "po_break_down_id");
			// print_r($booking_po_array);die();
			$booking_po = implode(",", $booking_po_array);
			$search_cond.=" and b.id in($booking_po)";
		}
		else if($cbo_search_by==7 && $txt_search_comm !="") 
		{
			$wo_po_array = return_library_array( "SELECT c.id from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, wo_po_break_down c where a.id=b.mst_id and b.job_no=c.job_no_mst and a.yarn_dyeing_prefix_num = $txt_search_comm and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0", "id", "id");
			// print_r($wo_po_array);die();
			$wo_po = implode(",", $wo_po_array);
			$search_cond.=" and b.id in($wo_po)";
		}
	}
	
	$prog_arr=return_library_array( "select requisition_no, knit_id from ppl_yarn_requisition_entry",'requisition_no','knit_id');
	
	ob_start();
	?>
    <fieldset style="width:1900px;">
        <table cellpadding="0" cellspacing="0" width="1870">
            <tr  class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="20" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
            </tr>
            <tr  class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
            </tr>
        </table>
        <table width="1887" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
            <thead>
                <th width="30">SL</th>
                <th width="90">System Challan</th>
                <th width="70">Date</th> 
                <th width="70">Prog. No</th>
                <th width="100">Job No</th>
                <th width="60">Buyer</th>
                <th width="100">Order no.</th>
                <th width="100">Style No</th>
                <th width="100">Booking No</th>
                <th width="100">Supplier</th>
                <th width="50">Count</th>
                <th width="80">Yarn Brand</th>
                <th width="80">Color</th>
                <th width="90">Type</th>
                <th width="80">Lot No</th>
                <th width="180">Yarn Comp.</th>
                <th width="80">Total Issued/ Received</th> 
                <th width="80">In-house Qty.</th>  
                <th width="80">Outside Qty.</th>
                <th width="100">Issue To</th>
                <th width="">Comments</th>
            </thead>
        </table>
        <div style="width:1900px; overflow-y: scroll; max-height:350px;" id="scroll_body">
			<table width="1870" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
                <tbody>
                	<tr><td colspan="21" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b>Issued For Yarn Dyeing<?php //echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></b></td></tr>
                <?
					$i=1; 
					$booking_qty=0;
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$product_array=array();
					$sql_data="Select id, yarn_count_id, yarn_type, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, lot, brand, color from product_details_master where item_category_id=1 and company_id=$cbo_company_id and status_active=1 and is_deleted=0";
					$result_sql_data=sql_select($sql_data);
					foreach($result_sql_data as $rows)
					{
						$product_array[$rows[csf('id')]]['arr_id']=$rows[csf('id')];
						$product_array[$rows[csf('id')]]['yarn_count_id']=$rows[csf('yarn_count_id')];
						$product_array[$rows[csf('id')]]['yarn_type']=$rows[csf('yarn_type')];
						$product_array[$rows[csf('id')]]['lot']=$rows[csf('lot')];
						$product_array[$rows[csf('id')]]['brand']=$rows[csf('brand')];
						$product_array[$rows[csf('id')]]['yarn_comp_type1st']=$rows[csf('yarn_comp_type1st')];
						$product_array[$rows[csf('id')]]['yarn_comp_percent1st']=$rows[csf('yarn_comp_percent1st')];
						$product_array[$rows[csf('id')]]['yarn_comp_type2nd']=$rows[csf('yarn_comp_type2nd')];
						$product_array[$rows[csf('id')]]['yarn_comp_percent2nd']=$rows[csf('yarn_comp_percent2nd')];
						$product_array[$rows[csf('id')]]['color']=$rows[csf('color')];
					}
					//$sql="select knit_id, requisition_no, prod_id, sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where requisition_no in ($all_req_no) and status_active=1 and is_deleted=0 group by requisition_no, prod_id, knit_id";
					
				if($db_type==0)
				{ 
				     if($year_id!=0) $year_cond=" and year(c.insert_date)=$year_id"; else $year_cond="";
                   	 if($month_id!=0) $month_cond=" and month(c.insert_date)=$month_id"; else $month_cond="";
					 $sql="SELECT a.id, a.trans_id, a.trans_type, a.po_breakdown_id, group_concat(concat_ws('**',a.prod_id,a.quantity)) as prod_id, sum(a.quantity) as quantity, group_concat(distinct(b.po_number)) as po_number, c.job_no, c.buyer_name, c.style_ref_no,
					 d.brand_id,  max(d.transaction_date) as transaction_date,
					 group_concat(case when a.entry_form ='3' and e.knit_dye_source=1 then concat_ws('**',a.prod_id,a.quantity) end ) as issue_inside, group_concat(case when a.entry_form ='3' and e.knit_dye_source=3 then concat_ws('**',a.prod_id,a.quantity) end ) as issue_outside, d.brand_id,group_concat(case when a.entry_form ='3' then concat_ws('**',a.prod_id,a.quantity) end ) as issue_qnty, group_concat(d.requisition_no) as requisition_no,
					 e.issue_number, e.issue_purpose, e.knit_dye_source, e.knit_dye_company, e.company_id, e.booking_id, e.booking_no, e.issue_number_prefix_num,d.supplier_id
					 from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_issue_master e 
					 where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.trans_id=d.id and d.mst_id=e.id and d.item_category=1 and d.transaction_type=2 and e.entry_form=3 and e.issue_purpose=2 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.company_name=$cbo_company_id $buyer_id_cond $search_cond $issue_purpose_cond $year_cond $month_cond 
					 group by e.issue_number_prefix_num, c.job_no ,d.supplier_id 
					 order by e.issue_number_prefix_num, c.job_no";
				}
				else if($db_type==2)
				{ 
				    if($year_id!=0) $year_cond=" and to_char(c.insert_date,'yyyy')=$year_id"; else $year_cond="";
	                if($month_id!=0) $month_cond=" and to_char(c.insert_date,'mm')=$month_id"; else $month_cond="";					 
					$sql="SELECT listagg((a.prod_id || '**'|| a.quantity),',') within group (order by a.prod_id) as prod_id, sum(a.quantity) as quantity, 
							listagg(b.po_number,',') within group (order by po_number) as po_number, c.job_no, c.buyer_name, c.style_ref_no,
						 	listagg((case when a.entry_form ='3' and e.knit_dye_source=1 then (a.prod_id|| '**' || a.quantity) end ),',')within group (order by a.prod_id) as issue_inside, 
							listagg((case when a.entry_form ='3' and e.knit_dye_source=3 then (a.prod_id|| '**' || a.quantity) end ),',') within group (order by a.prod_id)  as issue_outside, max(d.brand_id),  max(d.transaction_date) as transaction_date,
							listagg((case when a.entry_form ='3' then (a.prod_id|| '**' ||a.quantity) end ),',') within group (order by a.prod_id) as issue_qnty,
							listagg(d.requisition_no,',') within group (order by d.requisition_no) as requisition_no,
						 max(e.issue_purpose) as issue_purpose, max(e.knit_dye_source) as knit_dye_source, max(e.knit_dye_company) as knit_dye_company, max(e.company_id) as company_id, max(e.booking_id) as booking_id, max(e.booking_no) as booking_no, e.issue_number_prefix_num ,d.supplier_id
					 from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_issue_master e 
					 where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.trans_id=d.id and d.mst_id=e.id and d.item_category=1 and d.transaction_type=2 and e.entry_form=3 and e.issue_purpose=2 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.company_name=$cbo_company_id $buyer_id_cond $search_cond $issue_purpose_cond $year_cond $month_cond 
					 group by  e.issue_number_prefix_num, c.job_no, c.buyer_name, c.style_ref_no, d.supplier_id
					 order by e.issue_number_prefix_num, c.job_no";
				}
					//echo $sql;
					$result=sql_select($sql);
					$issue_purpose_array=array();
					$grand_tot_qnty_issued=0;  $grand_tot_qnty_balance=0; $grand_tot_qnty=0;  $grand_tot_qnty_cotton=0; $grand_tot_qnty_other=0; $grand_tot_qnty_inside=0;  $grand_tot_qnty_outside=0;
					$issue_purpose_total_issued=0; 

					foreach($result as $row)
					{
						if($row[csf('knit_dye_source')]==3)
						{
							$knit_dye_source=$supplier_arr[$row[csf('knit_dye_company')]];
						}
						else
						{
							$knit_dye_source="";
						}
						$prod_id=explode(',',$row[csf('prod_id')]);
						$issue_inside=explode(',',$row[csf('issue_inside')]);
						$issue_outside=explode(',',$row[csf('issue_outside')]);
						$issue_qty=explode(',',$row[csf('issue_qnty')]);

						$prod_dataArray=array();
						foreach( $prod_id as $val)
						{
							$id_val=explode('**',$val);
							$id=$id_val[0];
							$qnty=$id_val[1];
							$prod_dataArray[$id]+=$qnty;
							//print_r($val).',';
						}
						$qtyinArray=array();
						foreach( $issue_inside as $val)
						{
							$id_val=explode('**',$val);
							$id=$id_val[0];
							$qnty=$id_val[1];
							$qtyinArray[$id]+=$qnty;
							//print_r($val).',';
						}
						$qtyoutArray=array();
						foreach( $issue_outside as $val)
						{
							$id_val=explode('**',$val);
							$id=$id_val[0];
							$qnty=$id_val[1];
							$qtyoutArray[$id]+=$qnty;
							//print_r($val).',';
						}
						$issueqtyArray=array();
						foreach( $issue_qty as $val)
						{
							$id_val=explode('**',$val);
							$id=$id_val[0];
							$qnty=$id_val[1];
							$issueqtyArray[$id]+=$qnty;
							//print_r($val).',';
						}

						$booking_no=$row[csf("booking_no")];
						$booking_tot_qnty_row=0; 
						$issued_tot_qnty=0; $tot_qnty_balance=0;$inside_tot_qnty=0;  $outside_tot_qnty=0;$cotton_tot_qnty=0;$other_tot_qnty=0;
						
						
						$z=0; $rowspan=count($prod_dataArray);
						foreach($prod_dataArray as $key=>$value)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$booking_qty=return_field_value("sum(grey_fab_qnty) as grey_fab_qnty","wo_booking_dtls","booking_no='$booking_no'","grey_fab_qnty");
							if ($product_array[$key]['yarn_comp_type1st']!=0 && $product_array[$key]['yarn_comp_percent1st']!=0 && $product_array[$key]['yarn_comp_type2nd']!=0 && $product_array[$key]['yarn_comp_percent2nd']!=0)
							{
								$yarn_comp_per=$composition[$product_array[$key]['yarn_comp_type1st']].' '.$product_array[$key]['yarn_comp_percent1st'].'%,'.$composition[$product_array[$key]['yarn_comp_type2nd']].' '.$product_array[$key]['yarn_comp_percent2nd'].'% ';
							}
							else if ($product_array[$key]['yarn_comp_type1st']!=0 && $product_array[$key]['yarn_comp_percent1st']!=0 && $product_array[$key]['yarn_comp_type2nd']==0 && $product_array[$key]['yarn_comp_percent2nd']==0)
							{
								$yarn_comp_per=$composition[$product_array[$key]['yarn_comp_type1st']].' '.$product_array[$key]['yarn_comp_percent1st'].'%';
							}
							else
							{
								$yarn_comp_per="";
							}
							
							$req_no=array_unique(explode(",",$row[csf('requisition_no')]));
							$prog_no='';
							foreach($req_no as $val)
							{
								if($prog_no=='') $prog_no=$prog_arr[$val]; else $prog_no.=','.$prog_arr[$val];
							}
						?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                <?
								if($z==0)
								{
								?>
                                    <td width="30" rowspan="<? echo $rowspan; ?>"><? echo $i; ?></td>
                                    <td width="90" rowspan="<? echo $rowspan; ?>" align="center"><p><? echo $row[csf('issue_number_prefix_num')]; ?></p></td>
                                    <td width="70" rowspan="<? echo $rowspan; ?>"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
                                    <td width="70" rowspan="<? echo $rowspan; ?>"><p><? echo $prog_no; ?></p></td> 
                                    <td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $row[csf('job_no')]; ?></p></td>
                                    <td width="60" rowspan="<? echo $rowspan; ?>"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
                                    <td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo implode(',',array_unique(explode(',',$row[csf('po_number')]))); ?></p></td>
                                    <td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo implode(',',array_unique(explode(',',$row[csf('style_ref_no')]))); ?></p></td>
                                    <td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $row[csf('booking_no')]; ?></p></td>
                                <?
								}
								?>
                                <td width="100" ><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
                                <td width="50"><p><? echo $count_arr[$product_array[$key]['yarn_count_id']]; ?></p></td>
                                <td width="80"><p><? echo $brand_arr[$product_array[$key]['brand']]; ?></p></td>
                                <td width="80"><p><? echo $color_arr[$product_array[$key]['color']]; ?></p></td>
                                <td width="90"><p><? echo $yarn_type[$product_array[$key]['yarn_type']]; ?></p></td>
                                <td width="80"><p><? echo $product_array[$key]['lot']; ?></p></td>
                                <td width="180" ><p><? echo $yarn_comp_per; ?></p></td>
                                <td width="80" align="right"><? echo number_format($issueqtyArray[$key],2); $issued_tot_qnty+=$issueqtyArray[$key]; ?></td> 
                                <!--<td width="100"><p><?// echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p></td>-->
                                <td width="80" align="right"><? echo number_format($qtyinArray[$key],2); $inside_tot_qnty+=$qtyinArray[$key]; ?></td>  
                                <td width="80" align="right"><? echo number_format($qtyoutArray[$key],2); $outside_tot_qnty+=$qtyoutArray[$key]; ?></td>
                                <td width="100"><p><? if($qtyoutArray[$key]!=0)  echo $supplier_arr[$row[csf('knit_dye_company')]]; ?></p></td>
                                <td width=""><p><? //echo ?></p></td>
                            </tr>
                        	<?
							$issue_purpose_total_issued+=$issueqtyArray[$key];
							$issue_purpose_total_inside+=$qtyinArray[$key];
							$issue_purpose_total_outside+=$qtyoutArray[$key];
							$z++;
						}
						?>
                            <tr class="tbl_bottom">
                                <td colspan="7" align="right"><b>&nbsp;</b></td>
                                <td colspan="9" align="right"><b>Challan Total</b></td>
                                <td align="right"><? echo number_format($issued_tot_qnty,2,'.',''); $grand_tot_qnty_issued+=$issued_tot_qnty; ?></td>
                               <!-- <td>&nbsp;</td>-->
                                <td align="right"><? echo number_format($inside_tot_qnty,2,'.',''); $grand_tot_qnty_inside+=$inside_tot_qnty; ?></td>
                                <td align="right"><? echo number_format($outside_tot_qnty,2,'.',''); $grand_tot_qnty_outside+=$outside_tot_qnty; ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <?
						$i++;
						
					}
					?>
                    <tr class="tbl_bottom">
                        <td colspan="16" align="right"><b>Issued For Yarn Dyeing Total</b></td>
                        <td align="right"><? echo number_format($issue_purpose_total_issued,2,'.','');//unset($issue_purpose_total_issued); ?></td>
                       <!-- <td>&nbsp;</td>-->
                        <td align="right"><? echo number_format($issue_purpose_total_inside,2,'.','');unset($issue_purpose_total_inside); ?></td>
                        <td align="right"><? echo number_format($issue_purpose_total_outside,2,'.','');unset($issue_purpose_total_outside); ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
					
                    <tr><td colspan="21" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b>Dyed Yarn Received</b></td></tr>
                <?
				
			if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond_rec=" and a.buyer_id=$cbo_buyer_id";
			$year_id=str_replace("'","",$cbo_year);;
			$month_id=str_replace("'","",$cbo_month);
			
			$search_cond_rec='';
			if ($txt_search_comm=="") 
			{
				$search_cond_rec.="";
			}
			else
			{
				if($cbo_search_by==1) $search_cond_rec.=" and d.job_no_prefix_num in ($txt_search_comm) ";
				else if($cbo_search_by==2) $search_cond_rec.=" and d.style_ref_no LIKE '%$txt_search_comm%'";
				else if($cbo_search_by==3) $search_cond_rec.=" and c.po_number LIKE '%$txt_search_comm%'";
				else if($cbo_search_by==4) $search_cond_rec.=" and c.file_no LIKE '%$txt_search_comm%'";
				else if($cbo_search_by==5) $search_cond_rec.=" and c.grouping LIKE '%$txt_search_comm%'";
				else if($cbo_search_by==6 && $txt_search_comm !="") 
				{
					$booking_po_array = return_library_array( "SELECT b.po_break_down_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no_prefix_num =$txt_search_comm and a.status_active=1 and a.is_deleted=0", "po_break_down_id", "po_break_down_id");
					// print_r($booking_po_array);die();
					$booking_po = implode(",", $booking_po_array);
					$search_cond_rec.=" and c.id in($booking_po)";
				}
				else if($cbo_search_by==7 && $txt_search_comm !="") 
				{
					$wo_po_array = return_library_array( "SELECT c.id from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, wo_po_break_down c where a.id=b.mst_id and b.job_no=c.job_no_mst and a.yarn_dyeing_prefix_num = $txt_search_comm and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0", "id", "id");
					// print_r($wo_po_array);die();
					$wo_po = implode(",", $wo_po_array);
					$search_cond_rec.=" and c.id in($wo_po)";
				}
			}
			
			if(str_replace("'","",$cbo_issue_purpose)!="" && str_replace("'","",$cbo_issue_purpose)!=0) $issue_purpose_cond_rec=" and a.receive_purpose=$cbo_issue_purpose";
			if($db_type==0)
			{	
				if($year_id!=0) $year_cond_rec=" and year(a.insert_date)=$year_id"; else $year_cond_rec="";
				if($month_id!=0) $month_cond_rec=" and month(a.insert_date)=$month_id"; else $month_cond_rec="";
				$sql_rec="SELECT a.id, a.recv_number, a.buyer_id, a.booking_id, a.booking_no, a.recv_number_prefix_num,
				b.job_no, max(b.transaction_date) as transaction_date, b.brand_id, group_concat(b.requisition_no) as requisition_no,
				group_concat(concat_ws('**',b.prod_id,b.cons_quantity)) as prod_id, sum(b.cons_quantity) as quantity, 
				group_concat(distinct(c.po_number)) as po_number, d.buyer_name, d.style_ref_no,
				group_concat(case when a.entry_form ='1' then concat_ws('**',b.prod_id,b.cons_quantity) end ) as rec_qty,b.supplier_id
				 from inv_receive_master a, inv_transaction b, wo_po_break_down c, wo_po_details_master d
				 where a.id=b.mst_id and c.job_no_mst=d.job_no and b.job_no=d.job_no and a.item_category=1 and b.item_category=1 and b.transaction_type=1 and a.entry_form=1 and a.receive_purpose=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and d.status_active=1 and d.is_deleted=0 and a.company_id=$cbo_company_id $buyer_id_cond_rec $search_cond_rec $issue_purpose_cond_rec $year_cond_rec $month_cond_rec 
				 group by  a.recv_number_prefix_num, d.job_no ,b.supplier_id
				 order by  a.recv_number_prefix_num, d.job_no";					
			}
			else if($db_type==2)
			{	
				if($year_id!=0) $year_cond_rec=" and to_char(a.insert_date,'yyyy')=$year_id"; else $year_cond_rec="";
				if($month_id!=0) $month_cond_rec=" and to_char(a.insert_date,'mm')=$month_id"; else $month_cond_rec="";
				$sql_rec="SELECT max(a.buyer_id), a.recv_number_prefix_num,
					 d.job_no, listagg((b.prod_id|| '**' || b.cons_quantity),',') within group (order by b.prod_id)  as prod_id, sum(b.cons_quantity) as quantity, listagg(c.po_number,',') within group (order by c.po_number) as po_number, d.buyer_name, d.style_ref_no,
						 Max(b.brand_id) as brand_id, listagg(b.requisition_no,',') within group (order by b.requisition_no) as requisition_no,
						 listagg((case when a.entry_form ='1' then (b.prod_id|| '**' || b.cons_quantity) end ),',') within group (order by null) as rec_qty, b.supplier_id, max(b.transaction_date) as transaction_date,max(a.booking_no)
					 from inv_receive_master a, inv_transaction b, wo_po_break_down c, wo_po_details_master d
					 where a.id=b.mst_id and c.job_no_mst=d.job_no and b.job_no=d.job_no and a.item_category=1 and b.item_category=1 and b.transaction_type=1 and a.entry_form=1 and a.receive_purpose=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and d.status_active=1 and d.is_deleted=0 and a.company_id=$cbo_company_id $buyer_id_cond_rec $search_cond_rec $issue_purpose_cond_rec $year_cond_rec $month_cond_rec 
					 group by a.recv_number_prefix_num, d.job_no, d.buyer_name, d.style_ref_no ,b.supplier_id
					 order by a.recv_number_prefix_num, d.job_no";	
			}
			//echo $sql_rec;//die;
			$result_rec=sql_select($sql_rec);

			foreach($result_rec as $row)
			{
				if($row[csf('knit_dye_source')]==3)
				{
					$knit_dye_source=$supplier_arr[$row[csf('knit_dye_company')]];
				}
				else
				{
					$knit_dye_source="";
				}
				$prod_id=explode(',',$row[csf('prod_id')]);
				$issue_inside=explode(',',$row[csf('issue_inside')]);
				$issue_outside=explode(',',$row[csf('issue_outside')]);
				$rec_qty=explode(',',$row[csf('rec_qty')]);

				$prod_dataArray=array();
				foreach( $prod_id as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$prod_dataArray[$id]+=$qnty;
					//print_r($val).',';
				}
				$qtyinArray=array();
				foreach( $issue_inside as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$qtyinArray[$id]+=$qnty;
					//print_r($val).',';
				}
				$qtyoutArray=array();
				foreach( $issue_outside as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$qtyoutArray[$id]+=$qnty;
					//print_r($val).',';
				}
				$recqtyArray=array();
				foreach( $rec_qty as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$recqtyArray[$id]+=$qnty;
					//print_r($val).',';
				}

				$issued_tot_qnty_rec=0;$issue_purpose_total_inside_rec=0; $issue_purpose_total_outside_rec=0;
				
				$z=0; $rowspan=count($prod_dataArray);
				foreach($prod_dataArray as $key=>$value)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//$booking_qty=return_field_value("sum(grey_fab_qnty) as grey_fab_qnty","wo_booking_dtls","booking_no='$booking_no'","grey_fab_qnty");
					if ($product_array[$key]['yarn_comp_type1st']!=0 && $product_array[$key]['yarn_comp_percent1st']!=0 && $product_array[$key]['yarn_comp_type2nd']!=0 && $product_array[$key]['yarn_comp_percent2nd']!=0)
					{
						$yarn_comp_per=$composition[$product_array[$key]['yarn_comp_type1st']].' '.$product_array[$key]['yarn_comp_percent1st'].'%,'.$composition[$product_array[$key]['yarn_comp_type2nd']].' '.$product_array[$key]['yarn_comp_percent2nd'].'% ';
					}
					else if ($product_array[$key]['yarn_comp_type1st']!=0 && $product_array[$key]['yarn_comp_percent1st']!=0 && $product_array[$key]['yarn_comp_type2nd']==0 && $product_array[$key]['yarn_comp_percent2nd']==0)
					{
						$yarn_comp_per=$composition[$product_array[$key]['yarn_comp_type1st']].' '.$product_array[$key]['yarn_comp_percent1st'].'%';
					}
					else
					{
						$yarn_comp_per="";
					}
					
					$req_no=array_unique(explode(",",$row[csf('requisition_no')]));
					$prog_no='';
					foreach($req_no as $val)
					{
						if($prog_no=='') $prog_no=$prog_arr[$val]; else $prog_no.=','.$prog_arr[$val];
					}
				?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<?
						if($z==0)
						{
						?>
							<td width="30" rowspan="<? echo $rowspan; ?>"><? echo $i; ?></td>
							<td width="90" rowspan="<? echo $rowspan; ?>" align="center"><p><? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
							<td width="70" rowspan="<? echo $rowspan; ?>"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
                            <td width="70" rowspan="<? echo $rowspan; ?>"><p><? echo $prog_no; ?></p></td> 
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $row[csf('job_no')]; ?></p></td>
							<td width="60" rowspan="<? echo $rowspan; ?>"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo implode(',',array_unique(explode(',',$row[csf('po_number')])));//$row[csf('po_number')]; ?></p></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo implode(',',array_unique(explode(',',$row[csf('style_ref_no')]))); ?></p></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $row[csf('booking_no')]; ?></p></td>
						<?
						}
						?>
                        <td width="100" ><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
						<td width="50"><p><? echo $count_arr[$product_array[$key]['yarn_count_id']]; ?></p></td>
						<td width="80"><p><? echo $brand_arr[$row[csf('brand_id')]]; ?></p></td>
                        <td width="80"><p><? echo $color_arr[$product_array[$key]['color']]; ?></p></td>
						<td width="90"><p><? echo $yarn_type[$product_array[$key]['yarn_type']]; ?></p></td>
						<td width="80"><p><? echo $product_array[$key]['lot']; ?></p></td>
						<td width="180" ><p><? echo $yarn_comp_per; ?></p></td>
						<td width="80" align="right"><? echo number_format($recqtyArray[$key],2); $issued_tot_qnty_rec+=$recqtyArray[$key]; ?></td> 
						<!--<td width="100"><p><?// echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p></td>-->
						<td width="80" align="right"><? echo number_format($qtyinArray[$key],2); $inside_tot_qnty+=$qtyinArray[$key]; ?></td>  
						<td width="80" align="right"><? echo number_format($qtyoutArray[$key],2); $outside_tot_qnty+=$qtyoutArray[$key]; ?></td>
						<td width="100"><p><? if($qtyoutArray[$key]!=0)  echo $supplier_arr[$row[csf('knit_dye_company')]]; ?></p></td>
						<td width=""><p><? //echo ?></p></td>
					</tr>
					<? 
					$issue_purpose_total_rec+=$recqtyArray[$key];
					$issue_purpose_total_inside_rec+=$qtyinArray[$key];
					$issue_purpose_total_outside_rec+=$qtyoutArray[$key];
					$z++;
				}
				?>
					<tr class="tbl_bottom">
						<td colspan="7" align="right"><b>&nbsp;</b></td>
						<td colspan="9" align="right"><b>Challan Total</b></td>
						<td align="right"><? echo number_format($issued_tot_qnty_rec,2,'.',''); $grand_tot_qnty_issued+=$issued_tot_qnty_rec; ?></td>
						<!--<td>&nbsp;</td>-->
						<td align="right"><? echo number_format($inside_tot_qnty_rec,2,'.',''); $grand_tot_qnty_inside+=$inside_tot_qnty_rec; ?></td>
						<td align="right"><? echo number_format($outside_tot_qnty_rec,2,'.',''); $grand_tot_qnty_outside+=$outside_tot_qnty_rec; ?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<?
				$i++;
			}
			?>                    
            <tr class="tbl_bottom">
                <td colspan="16" align="right"><b>Dyed Yarn Received Total</b></td>
                <td align="right"><? echo number_format($issue_purpose_total_rec,2,'.',''); ?></td>
                <!--<td>&nbsp;</td>-->
                <td align="right"><? echo number_format($issue_purpose_total_inside_rec,2,'.',''); ?></td>
                <td align="right"><? echo number_format($issue_purpose_total_outside_rec,2,'.',''); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr class="tbl_bottom">
                <td colspan="16" align="right"><b>Receive Balance / Process Loss Qty</b></td>
                <td align="right"><? $balnace_process_loss=$issue_purpose_total_issued-$issue_purpose_total_rec; echo number_format($balnace_process_loss,2,'.',''); ?></td>
                <!--<td>&nbsp;</td>-->
                <td align="right"><? //echo number_format($issue_purpose_total_inside,2,'.',''); ?></td>
                <td align="right"><? //echo number_format($issue_purpose_total_outside,2,'.',''); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr class="tbl_bottom">
                <td colspan="16" align="right"><b>Receive Balance / Process Loss %</b></td>
                <td align="right"><? $balnace_process_loss_per=($balnace_process_loss/$issue_purpose_total_issued)*100; echo number_format($balnace_process_loss_per,2,'.','').'%'; ?>&nbsp;</td>
               <!-- <td>&nbsp;</td>-->
                <td align="right"><? //echo number_format($issue_purpose_total_inside,2,'.',''); ?></td>
                <td align="right"><? //echo number_format($issue_purpose_total_outside,2,'.',''); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr><td colspan="21" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b>Issued For Knitting (Less Issued Return)</b></td></tr>
            
            <?
			if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond_knit=" and c.buyer_name=$cbo_buyer_id";
			$year_id=str_replace("'","",$cbo_year);;
			$month_id=str_replace("'","",$cbo_month);;
		
			if(str_replace("'","",$cbo_issue_purpose)!="" && str_replace("'","",$cbo_issue_purpose)!=0) $issue_purpose_cond_knit=" and e.issue_purpose=$cbo_issue_purpose";
					
			if($db_type==0)
			{		
				if($year_id!=0) $year_cond_knit=" and year(c.insert_date)=$year_id"; else $year_cond_knit="";
				if($month_id!=0) $month_cond_knit=" and month(c.insert_date)=$month_id"; else $month_cond_knit="";
				$sql_knitting="SELECT a.id, a.trans_id, a.trans_type, a.po_breakdown_id, group_concat(concat_ws('**',a.prod_id,a.quantity)) as prod_id, sum(a.quantity) as quantity, group_concat(distinct(b.po_number)) as po_number, c.job_no, c.buyer_name, c.style_ref_no,
				 group_concat(case when a.entry_form ='3' and e.knit_dye_source=1 then concat_ws('**',a.prod_id,a.quantity) end ) as issue_inside,
				 group_concat(case when a.entry_form ='3' and e.knit_dye_source=3 then concat_ws('**',a.prod_id,a.quantity) end ) as issue_outside,
				 d.brand_id, group_concat(d.requisition_no) as requisition_no,
				 group_concat(case when a.entry_form ='3' then concat_ws('**',a.prod_id,a.quantity) end ) as issue_qnty,
				 group_concat(case when a.entry_form ='9' and e.knit_dye_source=1 then concat_ws('**',a.prod_id,a.quantity) end ) as inside_return,
				 group_concat(case when a.entry_form ='9' and e.knit_dye_source=3 then concat_ws('**',a.prod_id,a.quantity) end ) as outside_return,
				 group_concat(case when a.entry_form ='9' and e.knit_dye_source in (1,3) then concat_ws('**',a.prod_id,a.quantity) end ) as return_qnty,
				 e.issue_number, e.issue_purpose, e.knit_dye_source, e.knit_dye_company, e.company_id, e.booking_id, e.booking_no, max(d.transaction_date) as transaction_date, e.challan_no, e.issue_number_prefix_num, d.supplier_id
				 from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_issue_master e 
				 where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.trans_id=d.id and d.mst_id=e.id and d.item_category=1 and a.trans_type in (2,4) and e.entry_form=3 and e.issue_purpose=1 and a.entry_form in (3,9) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.company_name=$cbo_company_id $buyer_id_cond_knit $search_cond $issue_purpose_cond_knit $year_cond_knit $month_cond_knit 
				 group by e.issue_number_prefix_num, c.job_no , d.supplier_id
				 order by e.issue_number_prefix_num, c.job_no";
			}
			elseif($db_type==2)
			{		
				if($year_id!=0) $year_cond_knit=" and   to_char(c.insert_date,'yyyy')=$year_id"; else $year_cond_knit="";
				if($month_id!=0) $month_cond_knit=" and to_char(c.insert_date,'mm')=$month_id"; else $month_cond_knit="";
				$sql_knitting="SELECT 
				 listagg((a.po_breakdown_id),',') within group (order by a.po_breakdown_id) as po_breakdown_id,
				 listagg((a.prod_id ||'**' || a.quantity),',') within group (order by a.prod_id)  as prod_id,
				 sum(a.quantity) as quantity,
				 listagg((b.po_number),',') within group (order by b.po_number) as po_number, c.job_no, c.buyer_name, c.style_ref_no,
				 listagg((case when a.entry_form ='3' and e.knit_dye_source=1 then (a.prod_id || '**' || a.quantity) end ),',') within group (order by a.prod_id) as issue_inside,
				 listagg((case when a.entry_form ='3' and e.knit_dye_source=3 then (a.prod_id || '**' || a.quantity) end ),',') within group (order by a.prod_id) as issue_outside,
				 max(d.brand_id) as brand_id, listagg(d.requisition_no,',') within group (order by d.requisition_no) as requisition_no,
				 listagg((case when a.entry_form ='3' then (a.prod_id || '**' || a.quantity) end ),',') within group (order by a.prod_id) as issue_qnty,
				 listagg((case when a.entry_form ='9' and e.knit_dye_source=1 then (a.prod_id || '**' || a.quantity) end ),',') within group (order by a.prod_id) as inside_return,
				 listagg((case when a.entry_form ='9' and e.knit_dye_source=3 then (a.prod_id || '**' || a.quantity) end ),',') within group (order by a.prod_id) as outside_return,
				 listagg((case when a.entry_form ='9' and e.knit_dye_source in (1,3) then (a.prod_id || '**' || a.quantity) end ),',') within group (order by a.prod_id) as return_qnty,
				 max(e.knit_dye_source) as knit_dye_source, max(e.knit_dye_company) as knit_dye_company, max(e.company_id) as company_id, max(e.booking_id) as booking_id, max(e.booking_no) as booking_no, max(d.transaction_date) as transaction_date, e.issue_number_prefix_num , d.supplier_id
					 from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_issue_master e 
					 where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.trans_id=d.id and d.mst_id=e.id and d.item_category=1 and a.trans_type in (2,4) and e.entry_form=3 and e.issue_purpose=1 and a.entry_form in (3,9) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.company_name=$cbo_company_id $buyer_id_cond_knit $search_cond $issue_purpose_cond_knit $year_cond_knit $month_cond_knit 
					 group by e.issue_number_prefix_num, c.job_no, c.buyer_name, c.style_ref_no , d.supplier_id 
					 order by e.issue_number_prefix_num, c.job_no";	
			}
			//echo $sql_knitting;//die;
			$result_knitting=sql_select($sql_knitting);
			$issue_purpose_total_issued=0; 

			foreach($result_knitting as $row)
			{
				if($row[csf('knit_dye_source')]==3)
				{
					$knit_dye_source=$supplier_arr[$row[csf('knit_dye_company')]];
				}
				else
				{
					$knit_dye_source="";
				}
				$prod_id=explode(',',$row[csf('prod_id')]);
				$issue_inside=explode(',',$row[csf('issue_inside')]);
				$issue_outside=explode(',',$row[csf('issue_outside')]);
				$issue_qty=explode(',',$row[csf('issue_qnty')]);
				$return_qty=explode(',',$row[csf('return_qnty')]);
				$inside_return=explode(',',$row[csf('inside_return')]);
				$outside_return=explode(',',$row[csf('outside_return')]);

				$prod_dataArray=array();
				foreach( $prod_id as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$prod_dataArray[$id]+=$qnty;
					//print_r($val).',';
				}
				$qtyinArray=array();
				foreach( $issue_inside as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$qtyinArray[$id]+=$qnty;
					//print_r($val).',';
				}
				$qtyoutArray=array();
				foreach( $issue_outside as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$qtyoutArray[$id]+=$qnty;
					//print_r($val).',';
				}
				$issueqtyArray=array();
				foreach( $issue_qty as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$issueqtyArray[$id]+=$qnty;
					//print_r($val).',';
				}

				$insidereturnArray=array();
				foreach( $inside_return as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$insidereturnArray[$id]+=$qnty;
					//print_r($val).',';
				}

				$outsidereturnArray=array();
				foreach( $outside_return as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$outsidereturnArray[$id]+=$qnty;
					//print_r($val).',';
				}

				
				$returnqtyArray=array();
				foreach( $return_qty as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$returnqtyArray[$id]+=$qnty;
					//print_r($val).',';
				}

				//$booking_no=$row[csf("booking_no")];
				//$booking_tot_qnty_row=0; 
				$issue_purpose_total_knit=0; $issue_purpose_total_inside_knit=0;$issue_purpose_total_outside_knit=0;

				
				$z=0; $rowspan=count($prod_dataArray);
				foreach($prod_dataArray as $key=>$value)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//$booking_qty=return_field_value("sum(grey_fab_qnty) as grey_fab_qnty","wo_booking_dtls","booking_no='$booking_no'","grey_fab_qnty");
					if ($product_array[$key]['yarn_comp_type1st']!=0 && $product_array[$key]['yarn_comp_percent1st']!=0 && $product_array[$key]['yarn_comp_type2nd']!=0 && $product_array[$key]['yarn_comp_percent2nd']!=0)
					{
						$yarn_comp_per=$composition[$product_array[$key]['yarn_comp_type1st']].' '.$product_array[$key]['yarn_comp_percent1st'].'%,'.$composition[$product_array[$key]['yarn_comp_type2nd']].' '.$product_array[$key]['yarn_comp_percent2nd'].'% ';
					}
					else if ($product_array[$key]['yarn_comp_type1st']!=0 && $product_array[$key]['yarn_comp_percent1st']!=0 && $product_array[$key]['yarn_comp_type2nd']==0 && $product_array[$key]['yarn_comp_percent2nd']==0)
					{
						$yarn_comp_per=$composition[$product_array[$key]['yarn_comp_type1st']].' '.$product_array[$key]['yarn_comp_percent1st'].'%';
					}
					else
					{
						$yarn_comp_per="";
					}
					
					$req_no=array_unique(explode(",",$row[csf('requisition_no')]));
					$prog_no='';
					foreach($req_no as $val)
					{
						if($prog_no=='') $prog_no=$prog_arr[$val]; else $prog_no.=','.$prog_arr[$val];
					}
				?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<?
						if($z==0)
						{
						?>
							<td width="30" rowspan="<? echo $rowspan; ?>" align="center"><? echo $i; ?></td>
							<td width="90" rowspan="<? echo $rowspan; ?>" align="center"><p><? echo $row[csf('issue_number_prefix_num')]; ?></p></td>
							<td width="70" rowspan="<? echo $rowspan; ?>"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td> 
                            <td width="70" rowspan="<? echo $rowspan; ?>"><p><? echo $prog_no; ?></p></td> 
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $row[csf('job_no')]; ?></p></td>
							<td width="60" rowspan="<? echo $rowspan; ?>"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo  implode(',',array_unique(explode(',',$row[csf('po_number')]))); ?></p></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo implode(',',array_unique(explode(',',$row[csf('style_ref_no')]))); ?></p></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $row[csf('booking_no')]; ?></p></td>
						<?
						}
						?>
                        <td width="100" ><p><?  echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
						<td width="50"><p><? echo $count_arr[$product_array[$key]['yarn_count_id']]; ?></p></td>
						<td width="80"><p><? echo $brand_arr[$product_array[$key]['brand']]; ?></p></td>
                        <td width="80"><p><? echo $color_arr[$product_array[$key]['color']]; ?></p></td>
						<td width="90"><p><? echo $yarn_type[$product_array[$key]['yarn_type']]; ?></p></td>
						<td width="80"><p><? echo $product_array[$key]['lot']; ?></p></td>
						<td width="180" ><p><? echo $yarn_comp_per; ?></p></td>
						<td width="80" align="right"><a href="##" onClick="openmypage_challan('<? echo $row[csf('po_breakdown_id')]; ?>','yarn_issue','<? echo $product_array[$key]['yarn_count_id']; ?>','<? echo $product_array[$key]['yarn_comp_type1st']; ?>','<? echo $product_array[$key]['yarn_comp_percent1st']; ?>','<? echo $product_array[$key]['yarn_comp_type2nd']; ?>','<? echo $product_array[$key]['yarn_comp_percent2nd']; ?>','<? echo $product_array[$key]['yarn_type']; ?>','<? echo $product_array[$key]['lot']; ?>','<? echo $row[csf('issue_number_prefix_num')]; ?>')"><? $issue_qty=$issueqtyArray[$key]-$returnqtyArray[$key]; echo number_format($issue_qty,2); $issued_tot_qnty_knit+=$issue_qty; ?></a></td> 
						<!--<td width="100"><p><?// echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p></td>-->
						<td width="80" align="right"><? $inside_tot_qty=$qtyinArray[$key]-$insidereturnArray[$key]; echo number_format($inside_tot_qty,2); $inside_total_qnty+=$inside_tot_qty; ?></td>  
						<td width="80" align="right"><? $outside_tot_qty=$qtyoutArray[$key]-$outsidereturnArray[$key]; echo number_format($outside_tot_qty,2); $outside_total_qnty+=$outside_tot_qty; ?></td>
						<td width="100"><p><? if($qtyoutArray[$key]!=0)  echo $supplier_arr[$row[csf('knit_dye_company')]]; ?></p></td>
						<td width=""><p><? //echo ?></p></td>
					</tr>
					<?
					$issue_purpose_total_knit+=$issue_qty;
					$issue_purpose_total_inside_knit+=$inside_tot_qty;
					$issue_purpose_total_outside_knit+=$outside_tot_qty;
					$z++;
				}
				?>
					<tr class="tbl_bottom">
						<td colspan="7" align="right"><b>&nbsp;</b></td>
						<td colspan="9" align="right"><b>Challan Total</b></td>
						<td align="right"><? echo number_format($issue_purpose_total_knit,2,'.',''); $grand_tot_qnty_knit+=$issue_purpose_total_knit; ?></td>
						<!--<td>&nbsp;</td>-->
						<td align="right"><? echo number_format($issue_purpose_total_inside_knit,2,'.',''); $grand_tot_qnty_inside_knit+=$issue_purpose_total_inside_knit; ?></td>
						<td align="right"><? echo number_format($issue_purpose_total_outside_knit,2,'.',''); $grand_tot_qnty_outside_knit+=$issue_purpose_total_outside_knit; ?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<?
				$i++;
			}
			?>
			<tr class="tbl_bottom">
				<td colspan="16" align="right"><b>Issued For Knitting (Less Issued Return) Total</b></td>
				<td align="right"><? echo number_format($grand_tot_qnty_knit,2,'.',''); ?></td>
				<!--<td>&nbsp;</td>-->
				<td align="right"><? echo number_format($grand_tot_qnty_inside_knit,2,'.',''); ?></td>
				<td align="right"><? echo number_format($grand_tot_qnty_outside_knit,2,'.',''); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr><td colspan="21" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b>Issue For Sample (With Order)</b></td></tr>
			<?	
			if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond_sample=" and c.buyer_name=$cbo_buyer_id";
			$year_id=str_replace("'","",$cbo_year);;
			$month_id=str_replace("'","",$cbo_month);;
		
			if($year_id!=0) $year_cond_sample=" and year(c.insert_date)=$year_id"; else $year_cond_sample="";
			if($month_id!=0) $month_cond_sample=" and month(c.insert_date)=$month_id"; else $month_cond_sample="";

			if(str_replace("'","",$cbo_issue_purpose)!="" && str_replace("'","",$cbo_issue_purpose)!=0) $issue_purpose_cond_sample=" and e.issue_purpose=$cbo_issue_purpose";
			if($db_type==0)
			{
				if($year_id!=0) $year_cond_sample=" and year(c.insert_date)=$year_id"; else $year_cond_sample="";
				if($month_id!=0) $month_cond_sample=" and month(c.insert_date)=$month_id"; else $month_cond_sample="";				
				$sql_sample="SELECT a.id, a.trans_id, a.trans_type, a.po_breakdown_id, group_concat(concat_ws('**',a.prod_id,a.quantity)) as prod_id, sum(a.quantity) as quantity, group_concat(distinct(b.po_number)) as po_number, c.job_no, c.buyer_name, c.style_ref_no,
				d.brand_id, max(d.transaction_date) as transaction_date, group_concat(d.requisition_no) as requisition_no,
				group_concat(case when a.entry_form ='3' and e.knit_dye_source=1 then concat_ws('**',a.prod_id,a.quantity) end ) as issue_inside, 
				group_concat(case when a.entry_form ='3' and e.knit_dye_source=3 then concat_ws('**',a.prod_id,a.quantity) end ) as issue_outside, 
				group_concat(case when a.entry_form ='3' then concat_ws('**',a.prod_id,a.quantity) end ) as issue_qnty,
				e.issue_number, e.issue_purpose, e.knit_dye_source, e.knit_dye_company, e.company_id, e.booking_id, e.booking_no, e.issue_number_prefix_num, d.supplier_id
				from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_issue_master e 
				where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.trans_id=d.id and d.mst_id=e.id and d.item_category=1 and d.transaction_type=2 and e.entry_form=3 and e.issue_purpose=4 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.company_name=$cbo_company_id $buyer_id_cond_sample $search_cond $issue_purpose_cond_sample $year_cond_sample $month_cond_sample 
				group by e.issue_number_prefix_num, c.job_no , d.supplier_id
				order by e.issue_number_prefix_num, c.job_no";
			}
			else if($db_type==2)
			{
				if($year_id!=0) $year_cond_sample=" and to_char(c.insert_date,'yyyy')=$year_id"; else $year_cond_sample="";
				if($month_id!=0) $month_cond_sample=" and to_char(c.insert_date,'mm')=$month_id"; else $month_cond_sample="";				
				$sql_sample="SELECT listagg((a.po_breakdown_id),',') within group (order by a.po_breakdown_id) as po_breakdown_id,
				listagg((a.prod_id|| '**' || a.quantity),',') within group (order by a.prod_id) as prod_id, sum(a.quantity)  as quantity,
				listagg((case when a.entry_form ='3' and e.knit_dye_source=1 then (a.prod_id|| '**' || a.quantity) end ),',') within group (order by a.prod_id) as issue_inside, 
				listagg((case when a.entry_form ='3' and e.knit_dye_source=3 then (a.prod_id|| '**' || a.quantity) end ),',') within group (order by a.prod_id) as issue_outside, 
				listagg((case when a.entry_form ='3' then (a.prod_id|| '**' || a.quantity) end ),',') within group (order by a.prod_id) as issue_qnty,
				listagg((b.po_number),',') within group (order by b.po_number) as po_number, listagg(d.requisition_no,',') within group (order by d.requisition_no) as requisition_no,
				c.job_no, c.buyer_name, c.style_ref_no, max(d.brand_id) as brand_id,
				max(e.issue_purpose) as issue_purpose, listagg((e.knit_dye_source),',') within group (order by e.knit_dye_source) as knit_dye_source,
				 listagg((e.knit_dye_company),',') within group (order by e.knit_dye_company) as knit_dye_company,
				  listagg((e.company_id),',') within group (order by e.company_id) as company_id,
				   listagg((e.booking_id),',') within group (order by e.booking_id) as booking_id, 
				   listagg((e.booking_no),',') within group (order by e.booking_no) as booking_no, max(d.transaction_date) as transaction_date, e.issue_number_prefix_num , d.supplier_id
				
				from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_issue_master e 
				where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.trans_id=d.id and d.mst_id=e.id and d.item_category=1 and d.transaction_type=2 and e.entry_form=3 and e.issue_purpose=4 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.company_name=$cbo_company_id $buyer_id_cond_sample $search_cond $issue_purpose_cond_sample $year_cond_sample $month_cond_sample  
				group by e.issue_number_prefix_num, c.job_no, c.buyer_name, c.style_ref_no , d.supplier_id
				order by e.issue_number_prefix_num, c.job_no ";
			}
			//echo $sql_sample;//die;
			$result_sample=sql_select($sql_sample);
			foreach($result_sample as $row)
			{
				if($row[csf('knit_dye_source')]==3)
				{
					$knit_dye_source=$supplier_arr[$row[csf('knit_dye_company')]];
				}
				else
				{
					$knit_dye_source="";
				}
				$prod_id=explode(',',$row[csf('prod_id')]);
				$issue_inside=explode(',',$row[csf('issue_inside')]);
				$issue_outside=explode(',',$row[csf('issue_outside')]);
				$issue_qty=explode(',',$row[csf('issue_qnty')]);

				$prod_dataArray=array();
				foreach( $prod_id as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$prod_dataArray[$id]+=$qnty;
					//print_r($val).',';
				}
				$qtyinArray=array();
				foreach( $issue_inside as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$qtyinArray[$id]+=$qnty;
					//print_r($val).',';
				}
				$qtyoutArray=array();
				foreach( $issue_outside as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$qtyoutArray[$id]+=$qnty;
					//print_r($val).',';
				}
				$issueqtyArray=array();
				foreach( $issue_qty as $val)
				{
					$id_val=explode('**',$val);
					$id=$id_val[0];
					$qnty=$id_val[1];
					$issueqtyArray[$id]+=$qnty;
					//print_r($val).',';
				}

				$booking_no=$row[csf("booking_no")];
				//$booking_tot_qnty_row=0; 
				$issue_purpose_total_issued_sample=0; $issue_purpose_total_inside_sample=0; $issue_purpose_total_outside_sample=0;
				
				$z=0; $rowspan=count($prod_dataArray);
				foreach($prod_dataArray as $key=>$value)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$booking_qty=return_field_value("sum(grey_fab_qnty) as grey_fab_qnty","wo_booking_dtls","booking_no='$booking_no'","grey_fab_qnty");
					if ($product_array[$key]['yarn_comp_type1st']!=0 && $product_array[$key]['yarn_comp_percent1st']!=0 && $product_array[$key]['yarn_comp_type2nd']!=0 && $product_array[$key]['yarn_comp_percent2nd']!=0)
					{
						$yarn_comp_per=$composition[$product_array[$key]['yarn_comp_type1st']].' '.$product_array[$key]['yarn_comp_percent1st'].'%,'.$composition[$product_array[$key]['yarn_comp_type2nd']].' '.$product_array[$key]['yarn_comp_percent2nd'].'% ';
					}
					else if ($product_array[$key]['yarn_comp_type1st']!=0 && $product_array[$key]['yarn_comp_percent1st']!=0 && $product_array[$key]['yarn_comp_type2nd']==0 && $product_array[$key]['yarn_comp_percent2nd']==0)
					{
						$yarn_comp_per=$composition[$product_array[$key]['yarn_comp_type1st']].' '.$product_array[$key]['yarn_comp_percent1st'].'%';
					}
					else
					{
						$yarn_comp_per="";
					}
					
					$req_no=array_unique(explode(",",$row[csf('requisition_no')]));
					$prog_no='';
					foreach($req_no as $val)
					{
						if($prog_no=='') $prog_no=$prog_arr[$val]; else $prog_no.=','.$prog_arr[$val];
					}
				?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<?
						if($z==0)
						{
						?>
							<td width="30" rowspan="<? echo $rowspan; ?>"><? echo $i; ?></td>
							<td width="90" rowspan="<? echo $rowspan; ?>"><p><? echo $row[csf('issue_number_prefix_num')]; ?></p></td>
							<td width="70" rowspan="<? echo $rowspan; ?>"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
                            <td width="70" rowspan="<? echo $rowspan; ?>"><p><? echo $prog_no; ?></p></td> 
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $row[csf('job_no')]; ?></p></td>
							<td width="60" rowspan="<? echo $rowspan; ?>"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo implode(',',array_unique(explode(',',$row[csf('po_number')]))); ?></p></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo implode(',',array_unique(explode(',',$row[csf('style_ref_no')]))); ?></p></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $row[csf('booking_no')]; ?></p></td>
						<?
						}
						?>
                        <td width="100" ><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
						<td width="50"><p><? echo $count_arr[$product_array[$key]['yarn_count_id']]; ?></p></td>
						<td width="80"><p><? echo $brand_arr[$product_array[$key]['brand']]; ?></p></td>
                        <td width="80"><p><? echo $color_arr[$product_array[$key]['color']]; ?></p></td>
						<td width="90"><p><? echo $yarn_type[$product_array[$key]['yarn_type']]; ?></p></td>
						<td width="80"><p><? echo $product_array[$key]['lot']; ?></p></td>
						<td width="180" ><p><? echo $yarn_comp_per; ?></p></td>
						<td width="80" align="right"><? echo number_format($issueqtyArray[$key],2); $issued_tot_qnty+=$issueqtyArray[$key]; ?></td> 
						<!--<td width="100"><p><?// echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p></td>-->
						<td width="80" align="right"><? echo number_format($qtyinArray[$key],2); $inside_tot_qnty+=$qtyinArray[$key]; ?></td>  
						<td width="80" align="right"><? echo number_format($qtyoutArray[$key],2); $outside_tot_qnty+=$qtyoutArray[$key]; ?></td>
						<td width="100"><p><? if($qtyoutArray[$key]!=0)  echo $supplier_arr[$row[csf('knit_dye_company')]]; ?></p></td>
						<td width=""><p><? //echo ?></p></td>
					</tr>
					<?
					$issue_purpose_total_issued_sample+=$issueqtyArray[$key];
					$issue_purpose_total_inside_sample+=$qtyinArray[$key];
					$issue_purpose_total_outside_sample+=$qtyoutArray[$key];
					$z++;
				}
				?>
                <tr class="tbl_bottom">
                    <td colspan="7" align="right"><b>&nbsp;</b></td>
                    <td colspan="9" align="right"><b>Challan Total</b></td>
                    <td align="right"><? echo number_format($issue_purpose_total_issued_sample,2,'.',''); $grand_tot_qnty_issued_sample+=$issue_purpose_total_issued_sample; ?></td>
                  <!-- <td>&nbsp;</td>-->
                    <td align="right"><? echo number_format($issue_purpose_total_inside_sample,2,'.',''); $grand_tot_qnty_inside_sample+=$issue_purpose_total_inside_sample; ?></td>
                    <td align="right"><? echo number_format($issue_purpose_total_outside_sample,2,'.',''); $grand_tot_qnty_outside_sample+=$issue_purpose_total_outside_sample; ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
					<?
				$i++;
				}
				?>
                    <tr class="tbl_bottom">
                        <td colspan="16" align="right"><b>Total Issued (Knitting + Sample With Order)</b></td>
                        <td align="right"><? $total_issued=$grand_tot_qnty_knit+$grand_tot_qnty_issued_sample; echo number_format($total_issued,2,'.',''); ?></td>
                        <!--<td>&nbsp;</td>-->
                        <td align="right"><? $total_issue_in=$grand_tot_qnty_inside_knit+$grand_tot_qnty_inside_sample; echo number_format($total_issue_in,2,'.',''); ?></td>
                        <td align="right"><? $total_issue_out=$grand_tot_qnty_outside_knit+$grand_tot_qnty_outside_sample; echo number_format($total_issue_out,2,'.',''); ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>					
                    
				</table> 
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
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if($action=="yarn_issue")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
	</script>	
	<div style="width:860px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:855px; margin-left:3px">
		<div id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="840" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="10"><b>Yarn Issue</b></th>
				</thead>
				<thead>
                    <th width="105">Issue Id</th>
                    <th width="90">Issue To</th>
                    <th width="105">Booking No</th>
                    <th width="70">Challan No</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="65">Issue Date</th>
                    <th width="80">Yarn Type</th>
                    <th width="80">Issue Qty (In)</th>
                    <th>Issue Qty (Out)</th>
				</thead>
                <?
				if($challan!='') $challan_cond=" and a.issue_number_prefix_num='$challan'"; else $challan_cond="";
				if($issue_purpose >0) $issue_purpose_cond="and a.issue_purpose='$issue_purpose'"; else $issue_purpose_cond="";
                $i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0;
				if($db_type==0)
				{
					$sql_rec="select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, b.id as prod_id, c.product_name_details, d.brand_id,d.demand_no,a.issue_purpose,a.issue_basis from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and c.lot='$lot' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $challan_cond $issue_purpose_cond group by a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no,c.lot, c.yarn_type, b.id,c.product_name_details, d.brand_id,d.demand_no,a.issue_purpose,a.issue_basis";
				}
				else if($db_type==2)
				{
					$sql_rec="select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, b.id as prod_id, c.product_name_details,d.brand_id,d.demand_no,a.issue_purpose,a.issue_basis from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and c.lot='$lot' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $challan_cond $issue_purpose_cond group by  a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no,c.lot, c.yarn_type, b.id, c.product_name_details, d.brand_id,d.demand_no,a.issue_purpose,a.issue_basis";
				}

				//echo $sql_rec;
				
                $result_rec=sql_select($sql_rec);
				foreach($result_rec as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$issue_to='';
					if($row[csf('knit_dye_source')]==1) 
					{
						$issue_to=$company_arr[$row[csf('knit_dye_company')]]; 
					}
					else if($row[csf('knit_dye_source')]==3) 
					{
						$issue_to=$supplier_arr[$row[csf('knit_dye_company')]];
					}
					else
						$issue_to="&nbsp;";
						
                    $yarn_issued=$row[csf('issue_qnty')];

                    if($row[csf('issue_basis')]==8)
                    {
                    	$booking_no  = $row[csf('demand_no')];
                    }
                    else{
                    	$booking_no  = $row[csf('booking_no')];
                    }
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="90"><p><? echo $issue_to; ?></p></td>
                        <td width="105"><p><? echo $booking_no;?></p></td>
                        <td width="70"><p><? echo $row[csf('challan_no')]; ?></p></td>
                        <td width="70"><p><? echo $brand_arr[$row[csf('brand_id')]]; ?></p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="65" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td align="right" width="80">
							<? 
								if($row[csf('knit_dye_source')]==1)
								{
									echo number_format($yarn_issued,2);
									$total_yarn_issue_qnty+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<? 
								if($row[csf('knit_dye_source')]==3)
								{ 
									echo number_format($yarn_issued,2); 
									$total_yarn_issue_qnty_out+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
                <tr style="font-weight:bold" class="general">
                    <td align="right" colspan="8">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <tr style="font-weight:bold" bgcolor="#9999CC">
                    <td align="right" colspan="9">Issue Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty+$total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <thead>
                    <th colspan="10"><b>Yarn Return</b></th>
                </thead>
                <thead>
                	<th width="105">Return Id</th>
                    <th width="90">Return From</th>
                    <th width="105">Booking No</th>
                    <th width="70">Challan No</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="75">Return Date</th>
                    <th width="80">Yarn Type</th>
                    <th width="80">Return Qty (In)</th>
                    <th>Return Qty (Out)</th>
               </thead>
                <?
				if($challan!='') $challan_iss_cond=" and d.issue_challan_no='$challan'"; else $challan_iss_cond="";
                $total_yarn_return_qnty_in=0; $total_yarn_return_qnty_out=0;
				if($db_type==0)
				{
				$sql_return="select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, b.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and c.lot='$lot' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $challan_iss_cond group by a.id, b.prod_id";
				}
				elseif($db_type==2)
				{
				$sql_return="select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and c.lot='$lot' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $challan_iss_cond group by a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no,c.lot, c.yarn_type, c.product_name_details, d.brand_id";
				}
				//echo $sql_return;
                $result_return=sql_select($sql_return);
				foreach($result_return as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
				
					if($row[csf('knitting_source')]==1) 
					{
						$return_from=$company_arr[$row[csf('knitting_company')]]; 
					}
					else if($row[csf('knitting_source')]==3) 
					{
						$return_from=$supplier_arr[$row[csf('knitting_company')]];
					}
					else
						$return_from="&nbsp;";
						
                    $yarn_returned=$row[csf('returned_qnty')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="90"><p><? echo $return_from; ?></p></td>
                        <td width="105"><p><? echo $row[csf('booking_no')];?></p></td>
                        <td width="70"><p><? echo $row[csf('challan_no')]; ?></p></td>
                        <td width="70"><p><? echo $brand_arr[$row[csf('brand_id')]]; ?></p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="65" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td align="right" width="80">
							<? 
								if($row[csf('knitting_source')]==1)
								{
									echo number_format($yarn_returned,2);
									$total_yarn_return_qnty_in+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<? 
								if($row[csf('knitting_source')]==3)
								{ 
									echo number_format($yarn_returned,2); 
									$total_yarn_return_qnty_out+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
                <tr style="font-weight:bold" class="general">
                     
                    <td align="right" colspan="8">Total</td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty_in,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty_out,2);?></td>
                </tr>

                <tr style="font-weight:bold" bgcolor="#9999CC">
                    <td align="right" colspan="9">Issue Return Total</td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty_in+$total_yarn_return_qnty_out,2);?></td>
                </tr>

                <tfoot>    
                    <tr>
                        <th align="right" colspan="9">Total Balance</th>
                        <th align="right"><? echo number_format(($total_yarn_issue_qnty+$total_yarn_issue_qnty_out)-($total_yarn_return_qnty_in+$total_yarn_return_qnty_out),2);?></th>
                    </tr>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
	<?
    exit();
}