<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

include('../../../../includes/class4/class.conditions.php');
include('../../../../includes/class4/class.reports.php');
include('../../../../includes/class4/class.fabrics.php');


$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	$company_id = $data[0];
	echo create_drop_down( "cbo_buyer_id", 140, "SELECT buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($company_id) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

if($action=="load_drop_down_location")
{
    extract($_REQUEST);
    $company_ids = str_replace("'","",$data); 
	echo create_drop_down( "cbo_location_id", 140, "SELECT id,location_name from lib_location where company_id in($company_ids)  and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "load_drop_down( 'requires/multi_company_style_wise_finish_fabric_stock_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_stores', 'store_td' );","" );
	exit();
}
if($action=="load_drop_down_stores")
{
    extract($_REQUEST);

    $datas=explode("_", $data);

    $company_ids = str_replace("'","",$datas[0]); 
    if($datas[1] != ""){$location_id_cond="and a.location_id in ($datas[1])";}
	echo create_drop_down( "cbo_store_id", 120, "SELECT a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id in($company_ids) and b.category_type in(3) $location_id_cond  group by a.id,a.store_name","id,store_name", 0, "", 0, "",$disable );
	exit();
}


if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
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
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( ddd );
		}

		/*function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]);
			$("#hide_job_no").val(splitData[1]);
			parent.emailwindow.hide();
		}*/
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:580px;">
				<table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search Job</th>
						<th>Search Style</th>
						<!--<th>Search Order</th>-->
						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
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
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_job" id="txt_search_job" placeholder="Job No" />
							</td>
							<td align="center">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_style" id="txt_search_style" placeholder="Style Ref." />
							</td>
	                        <!--<td align="center">
	                            <input type="text" style="width:80px" class="text_boxes" name="txt_search_order" id="txt_search_order" placeholder="Order No" />
	                        </td> +'**'+document.getElementById('txt_search_order').value-->
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_search_job').value+'**'+document.getElementById('txt_search_style').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'order_wise_finish_fabric_stock_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:70px;" />
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
	//$month_id=$data[5];
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

	if($data[2]!='') $job_cond=" and job_no_prefix_num=$data[2]"; else $job_cond="";
	if($data[3]!='') $style_cond=" and style_ref_no like '$data[3]'"; else $style_cond="";
	//if($data[4]!='') $order_cond=" and po_number like '$data[4]'"; else $order_cond="";

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="year(insert_date)";
	else if($db_type==2) $year_field_by="to_char(insert_date,'YYYY')";
	else $year_field_by="";

	if($year_id!=0) $year_cond=" and $year_field_by='$year_id'"; else $year_cond="";
	//if($month_id!=0) $month_cond="$month_field_by=$month_id"; else $month_cond="";
	$arr=array (0=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, buyer_name, style_ref_no, $year_field_by as year from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id $buyer_id_cond $job_cond $style_cond $year_cond order by id DESC";

	echo create_list_view("tbl_list_search", "Buyer Name,Job No,Year,Style Ref. No", "170,130,80,60","610","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "buyer_name,0,0,0", $arr , "buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0','',1) ;
	exit();
}

if($action=="job_no_popup2")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
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
    <input type='hidden' id='hide_job_no' name="hide_job_no" />
    <input type='hidden' id='hide_job_id' name="hide_job_id" />
	<?
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	$company_id=str_replace("'","",$companyID);
	$buyer_id=str_replace("'","",$buyer_name);
	$year_id=str_replace("'","",$cbo_year_id);
	$search_type=str_replace("'","",$search_type);
	//$month_id=$data[5];
	//echo $month_id;
	$sql_cond="";
	if($buyer_id>0) $sql_cond .=" and a.buyer_name=$buyer_id";
	if($buyer_id>0) $sql_cond2 =" and a.buyer_id=$buyer_id";
	
	if($db_type==0) $year_field_by="year(a.insert_date)";
	else if($db_type==2) $year_field_by="to_char(a.insert_date,'YYYY')";
	else $year_field_by="";
	
	if($year_id!=0) $year_cond=" and $year_field_by='$year_id'"; else $year_cond="";
	
	if($search_type==1)
	{
		$arr=array (2=>$buyer_arr);
		$sql= "select a.id as id, a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, $year_field_by as year from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $sql_cond $year_cond order by id DESC";
		
		//echo create_list_view("list_view", "Buyer Name,Job No,Year,Style Ref. No", "170,130,100","610","350",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "buyer_name,0,0,0", $arr , "buyer_name,job_no_prefix_num,year,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0','') ;

		echo create_list_view("list_view", "Year,Job No,Buyer Name,Style Ref", "100,130,170","610","350",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0,0,buyer_name,0", $arr , "year,job_no_prefix_num,buyer_name,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0','') ;
		exit();
	}
	else if($search_type==2)
	{
		$arr=array (2=>$buyer_arr);
		$sql= "select a.id as id, a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, $year_field_by as year, b.id as po_id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id $sql_cond $year_cond order by id DESC";
		
		//echo create_list_view("list_view", "Buyer Name,Job No,Year,Style Ref. No,Order No", "170,70,70,100","610","350",0, $sql , "js_set_value", "job_no,style_ref_no", "", 1, "buyer_name,0,0,0,0", $arr , "buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "","setFilterGrid('list_view',-1)",'0,0,0,0,0','') ;

		echo create_list_view("list_view", "Year,Job No, Buyer Name, Style Ref. No, Order No", "170,70,70,100","610","350",0, $sql , "js_set_value", "job_no,style_ref_no", "", 1, "0,0,buyer_name,0,0", $arr , "year,job_no_prefix_num, buyer_name,style_ref_no,po_number", "","setFilterGrid('list_view',-1)",'0,0,0,0,0','') ;
		exit();
	}
	
}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
if($action=="report_generate_summery")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	//$cbo_presentation=str_replace("'","",$cbo_presentation);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_search_comm=str_replace("'","",$txt_search_comm);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_uom=str_replace("'","",$cbo_uom);
	$cbo_store_name=str_replace("'","",$cbo_store_id);
	$cbo_shipment_status=str_replace("'","",$cbo_shipment_status);
	$cbo_value_range_by=str_replace("'","",$cbo_value_range_by);
	$cbo_location_ids=str_replace("'","",$cbo_location_id);
	$cbo_uom=str_replace("'","",$cbo_uom);
	if($cbo_uom) $uom_cond = " and c.cons_uom in ($cbo_uom) "; else $uom_cond = "";
	//echo $cbo_report_type;die;

	if($cbo_store_name>0){$storeCond="and c.store_id in($cbo_store_name)";}else{$storeCond="";}
	if($cbo_shipment_status>0){$shiping_status_cond=" and b.shiping_status in ($cbo_shipment_status)";}

	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";
	$job_no=str_replace("'","",$txt_job_no);
	$search_cond='';
	if($cbo_search_by==1)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.job_no_prefix_num in ($txt_search_comm) ";
	}
	else if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.style_ref_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.po_number LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.file_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==5)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.grouping LIKE '%$txt_search_comm%'";
	}
	else
	{
		$search_cond.="";
	}

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date=""; else $receive_date= " and c.transaction_date <=".$txt_date_from."";

	if( $date_from=="") $today_receive_date=""; else $today_receive_date= " c.transaction_date=".$txt_date_from."";

	$cbo_year_val=str_replace("'","",$cbo_year);
	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";
	if($db_type==0)
	{
		$prod_id_cond=" group_concat(b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and year(a.insert_date)='$cbo_year_val'"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$prod_id_cond=" listagg(cast(b.from_prod_id as varchar2(4000)),',') within group (order by b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and to_char(a.insert_date,'YYYY')='$cbo_year_val'";  else $year_cond="";
	}

	$product_array=array(); $allProductIdsArr=array();

	$cbo_uom=str_replace("'","",$cbo_uom);
	if($cbo_uom) $uom_cond_prod = " and unit_of_measure in ($cbo_uom) "; else $uom_cond_prod = "";
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$company_lib_arr=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$store_lib_arr=return_library_array( "select id, store_name from LIB_STORE_LOCATION", "id", "store_name"  );
	//$body_part_lib_arr=return_library_array( "select id, body_part_full_name from lib_body_part", "id", "body_part_full_name"  );
	//$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

	$product_sql = sql_select("select id, product_name_details,unit_of_measure from product_details_master where status_active = 1 and is_deleted = 0 $uom_cond_prod");
	foreach ($product_sql as $prow)
	{
		$product_arr[$prow[csf("id")]] = $prow[csf("product_name_details")];
		$uomFromProductArr[$prow[csf("id")]] = $prow[csf("unit_of_measure")];
		$productIds .= $prow[csf("id")].",";
	}


	$allProductIdsArr = array_filter(array_unique(explode(",",chop($productIds,","))));
	if(count($allProductIdsArr) > 999 && $db_type == 2 )
	{
		$allProductIdsChunkArr=array_chunk($allProductIdsArr,999) ;
		foreach($allProductIdsChunkArr as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$pid_cond.="  d.prod_id in($chunk_arr_value) or ";
		}

		$productId_cond.=" and (".chop($pid_cond,'or ').")";
	}else{

		$productId_cond.=" and d.prod_id in (".implode(",",$allProductIdsArr).")";
	}
	if($cbo_uom) $productId_cond=$productId_cond;else $productId_cond='';
	if($db_type==0)
	{
		$select_fld= "year(a.insert_date) as year";
	}
	else if($db_type==2)
	{
		$select_fld= "TO_CHAR(a.insert_date,'YYYY') as year";
	}
	if($cbo_report_type==2) // Woven Finish Start
	{

		$sql_query="
		Select
		a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number as po_no,d.color_id,d.prod_id, c.order_rate, $select_fld,e.product_name_details as prod_desc,e.detarmination_id,c.store_id,c.body_part_id , 
		(case when d.entry_form in (17) then d.quantity else 0 end) as receive_qnty,
		(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then d.quantity else 0 end) as finish_fabric_transfer_in, 
		(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then d.quantity else 0 end) as issue_rtn,
		(case when d.entry_form in (17) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,
		(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 and $today_receive_date then d.quantity else 0 end) as today_trans_in,
		(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 and $today_receive_date then d.quantity else 0 end) as today_issue_rtn, 
		(case when d.entry_form in (19) then d.quantity else 0 end) as issue_qnty, (case when d.entry_form in (258) 
		and c.transaction_type=6 and d.trans_type=6 then d.quantity else 0 end) as finish_fabric_transfer_out, 
		(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then d.quantity else 0 end) as recv_rtn,
		(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 and $today_receive_date then d.quantity else 0 end) as today_recv_rtn, 
		(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 and $today_receive_date then d.quantity else 0 end) as today_trans_out,
		(case when d.entry_form in (19) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty,

		(case when d.entry_form in (17) and c.transaction_type=1 then c.cons_rate else 0 end) *(case when d.entry_form in (17) and c.transaction_type=1 then d.quantity else 0 end) as receive_amount,
		(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then d.quantity else 0 end) 
		*(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then c.cons_rate else 0 end) as transfer_in_amount,
		(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then d.quantity else 0 end)*(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then c.cons_rate else 0 end) as issue_rtn_amount, 
		(case when d.entry_form in (19) and c.transaction_type=2 then c.cons_rate else 0 end)*(case when d.entry_form in (19) and c.transaction_type=2 then d.quantity else 0 end) as issue_amount, 
		
		(case when d.entry_form in (258) 
		and c.transaction_type=6 and d.trans_type=6 then c.cons_rate else 0 end) *(case when d.entry_form in (258) 
		and c.transaction_type=6 and d.trans_type=6 then d.quantity else 0 end) as transfer_out_amount, 

		(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then d.quantity else 0 end)*(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then c.cons_rate else 0 end) as recv_rtn_amount ,
		(case when d.entry_form in (17) and $today_receive_date and c.transaction_type=1 then d.quantity else 0 end)*(case when d.entry_form in (17) and $today_receive_date and c.transaction_type=1 then c.cons_rate else 0 end) as today_receive_amount, 

		(case when d.entry_form in (258) and $today_receive_date and c.transaction_type=5 and d.trans_type=5 then d.quantity else 0 end)*
		(case when d.entry_form in (258) and $today_receive_date and c.transaction_type=5 and d.trans_type=5 then c.cons_rate else 0 end) as today_transfer_in_amount,


		(case when d.entry_form in (209) and $today_receive_date and c.transaction_type=4 and d.trans_type=4 then d.quantity else 0 end)*(case when d.entry_form in (209) and $today_receive_date and c.transaction_type=4 and d.trans_type=4 then c.cons_rate else 0 end) as today_issue_rtn_amount, 
		(case when d.entry_form in (19) and $today_receive_date and c.transaction_type=2 then c.cons_rate else 0 end) *(case when d.entry_form in (19) and $today_receive_date and c.transaction_type=2 then d.quantity else 0 end) as today_issue_amount, 

		(case when d.entry_form in (258) and $today_receive_date and c.transaction_type=6 and d.trans_type=6 then c.cons_rate else 0 end)*
		(case when d.entry_form in (258) and $today_receive_date and c.transaction_type=6 and d.trans_type=6 then d.quantity else 0 end) as today_transfer_out_amount, 


		(case when d.entry_form in (202) and $today_receive_date and c.transaction_type=3 and d.trans_type=3 then d.quantity else 0 end)*(case when d.entry_form in (202) and $today_receive_date and c.transaction_type=3 and d.trans_type=3 then c.cons_rate else 0 end) as today_recv_rtn_amount 

		from
		wo_po_details_master a,
		wo_po_break_down b,
		inv_transaction c,
		order_wise_pro_details d,product_details_master e 
		where
		a.job_no=b.job_no_mst 
		and d.po_breakdown_id=b.id 
		and d.trans_id=c.id and d.prod_id=e.id and c.prod_id=e.id 
		and d.trans_id!=0
		and d.entry_form in (17,19,202,209,258) 
		and c.item_category=3  
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
		and a.company_name in(".$cbo_company_id.") $receive_date $buyer_id_cond $search_cond $year_cond $storeCond $shiping_status_cond  $uom_cond order by a.job_no,d.color_id";


		//echo $sql_query; die;
		$style_wise_arr=array();
		$nameArray=sql_select($sql_query);
		$uomArrForStore=array();
		$uomArr=array();
		$uniqe_arrChkStore=array();
		$uniqe_arrChkBuyer=array();
		foreach ($nameArray as $row)
		{

			if($uomFromProductArr[$row[csf('prod_id')]] == "") continue;
			//$uomArr[]=$uomFromProductArr[$row[csf('prod_id')]]; 
			//--------------------Start Store wise array-------------------------
			$style_wise_arr_store[$row[csf('store_id')]]['job_no']=$row[csf('job_no')];
			$style_wise_arr_store[$row[csf('store_id')]]['job_no_pre']=$row[csf('job_no_prefix_num')];
			$style_wise_arr_store[$row[csf('store_id')]]['year']=$row[csf('year')];
			$style_wise_arr_store[$row[csf('store_id')]]['buyer_name']=$row[csf('buyer_name')];
			$style_wise_arr_store[$row[csf('store_id')]]['body_part_id']=$row[csf('body_part_id')];
			
			$style_wise_arr_store[$row[csf('store_id')]]['companyId']=$row[csf('company_name')];
			$style_wise_arr_store[$row[csf('store_id')]]['store_id']=$row[csf('store_id')];

			$style_wise_arr_store[$row[csf('store_id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$style_wise_arr_store[$row[csf('store_id')]]['po_id'].=$row[csf('po_id')].',';
			$style_wise_arr_store[$row[csf('store_id')]]['po_no'].=$row[csf('po_no')].',';
			$style_wise_arr_store[$row[csf('store_id')]]['detarmination_id']=$row[csf('detarmination_id')];
			if($uniqe_arrChkStore[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]!=$uomFromProductArr[$row[csf('prod_id')]])
			{
				$style_wise_arr_store[$row[csf('store_id')]]['uom'].=$uomFromProductArr[$row[csf('prod_id')]].",";
				$uniqe_arrChkStore[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]=$uomFromProductArr[$row[csf('prod_id')]];

			}

			$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['receive_qnty']+=$row[csf('receive_qnty')];
			$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['issue_qnty']+=$row[csf('issue_qnty')];
			$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['issue_rtn']+=$row[csf('issue_rtn')];
			$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['recv_rtn']+=$row[csf('recv_rtn')];

			$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['finish_fabric_transfer_in']+=$row[csf('finish_fabric_transfer_in')];
			$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['finish_fabric_transfer_out']+=$row[csf('finish_fabric_transfer_out')];


			$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['totalRecv']=$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['receive_qnty']+$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['issue_rtn']+$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['finish_fabric_transfer_in'];

			$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['totalIssue']=$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['issue_qnty']+$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['recv_rtn']+$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['finish_fabric_transfer_out'];

			$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['totalStockQnty']=$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['totalRecv']-$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['totalIssue'];


			if ($style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['totalStockQnty']>0) {
				$uomArrForStore[]=$uomFromProductArr[$row[csf('prod_id')]]; 
			}

			$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['receive_amount']+=$row[csf('receive_amount')];
			$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['transfer_in_amount']+=$row[csf('transfer_in_amount')];
			$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['issue_rtn_amount']+=$row[csf('issue_rtn_amount')];

			$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['issue_amount']+=$row[csf('issue_amount')];
			$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['transfer_out_amount']+=$row[csf('transfer_out_amount')];
			$style_wise_arr_uom_store[$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]]['recv_rtn_amount']+=$row[csf('recv_rtn_amount')];	
									

			//---------------------Start Buyer wise Array------------------------
			$style_wise_arr[$row[csf('buyer_name')]]['job_no']=$row[csf('job_no')];
			$style_wise_arr[$row[csf('buyer_name')]]['job_no_pre']=$row[csf('job_no_prefix_num')];
			$style_wise_arr[$row[csf('buyer_name')]]['year']=$row[csf('year')];
			$style_wise_arr[$row[csf('buyer_name')]]['buyer_name']=$row[csf('buyer_name')];
			$style_wise_arr[$row[csf('buyer_name')]]['body_part_id']=$row[csf('body_part_id')];
			
			$style_wise_arr[$row[csf('buyer_name')]]['companyId']=$row[csf('company_name')];
			$style_wise_arr[$row[csf('buyer_name')]]['store_id']=$row[csf('store_id')];

			$style_wise_arr[$row[csf('buyer_name')]]['style_ref_no']=$row[csf('style_ref_no')];
			$style_wise_arr[$row[csf('buyer_name')]]['po_id'].=$row[csf('po_id')].',';
			$style_wise_arr[$row[csf('buyer_name')]]['po_no'].=$row[csf('po_no')].',';
			$style_wise_arr[$row[csf('buyer_name')]]['detarmination_id']=$row[csf('detarmination_id')];
			if($uniqe_arrChkBuyer[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]!=$uomFromProductArr[$row[csf('prod_id')]])
			{
				$style_wise_arr[$row[csf('buyer_name')]]['uom'].=$uomFromProductArr[$row[csf('prod_id')]].",";
				$uniqe_arrChkBuyer[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]=$uomFromProductArr[$row[csf('prod_id')]];

			}

			$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['receive_qnty']+=$row[csf('receive_qnty')];
			$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['issue_qnty']+=$row[csf('issue_qnty')];
			$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['issue_rtn']+=$row[csf('issue_rtn')];
			$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['recv_rtn']+=$row[csf('recv_rtn')];

			$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['finish_fabric_transfer_in']+=$row[csf('finish_fabric_transfer_in')];
			$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['finish_fabric_transfer_out']+=$row[csf('finish_fabric_transfer_out')];


			$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['totalRecv']=$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['receive_qnty']+$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['issue_rtn']+$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['finish_fabric_transfer_in'];

			$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['totalIssue']=$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['issue_qnty']+$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['recv_rtn']+$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['finish_fabric_transfer_out'];

			$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['totalStockQnty']=$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['totalRecv']-$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['totalIssue'];


			if ($style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['totalStockQnty']>0) {
				$uomArr[]=$uomFromProductArr[$row[csf('prod_id')]]; 
			}


			$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['receive_amount']+=$row[csf('receive_amount')];
			$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['transfer_in_amount']+=$row[csf('transfer_in_amount')];
			$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['issue_rtn_amount']+=$row[csf('issue_rtn_amount')];

			$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['issue_amount']+=$row[csf('issue_amount')];
			$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['transfer_out_amount']+=$row[csf('transfer_out_amount')];
			$style_wise_arr_uom[$row[csf('buyer_name')]][$uomFromProductArr[$row[csf('prod_id')]]]['recv_rtn_amount']+=$row[csf('recv_rtn_amount')];	
									
		}
		/*echo "<pre>";
		print_r($style_wise_arr); die;*/

		
		//echo "<pre>";print_r($uomArr);echo "</pre>"; die;
		$uomArrForStore=array_unique($uomArrForStore);
		$uom_th_widthStore=0;
		foreach ($uomArrForStore as $keys => $values) {
			$uom_th_widthStore+=200;

		}


		$uomArr=array_unique($uomArr);
		$uom_th_width=0;
		foreach ($uomArr as $keys => $values) {
			$uom_th_width+=200;

		}
		ob_start();
		?>
		<fieldset style="width:<? echo 340+$uom_th_width; ?>px;">
			<table cellpadding="0" cellspacing="0" width="<? echo 310+$uom_th_width; ?>">
				<tr  class="form_caption" style="border:none;">
					<td width="10%" align="left">All Value Converted to Taka</td>
					<td align="center" width="90%" colspan="15" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td width="10%" align="left"></td>
					<td align="center" width="90%" colspan="15" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td width="10%" align="left"></td>
					<td align="center" width="90%" colspan="15" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>


			<h3>Store Wise Stock and Value Summary </h3>
			<table width="<? echo 350+$uom_th_widthStore; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					
					<tr>
						<th width="30">SL</th>
						<th width="100">Store</th>

						<? foreach ($uomArrForStore as $key => $values) 
						{
							?>
							<th width="100" title="<? echo $values; ?>">Stock Qnty (<? echo $unit_of_measurement[$values]; ?>)</th>
							<th width="100">Stock Value</th>
							<?
						}
						?>
						<th>Total Value</th>
										
					</tr>
					
				</thead>
			</table>
			<div style="width:<? echo 370+$uom_th_widthStore; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="<? echo 350+$uom_th_widthStore; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
					<?
					$i=1;
					$totalStockUomArrStore=array();
					$totalStockValueUomArrStore=array();
					$grandTotalStockValueStore=0;
					foreach ($style_wise_arr_store as $storeId => $val)
					{
						//--------------------------------
						//if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						//$fab_desc_type=$product_arr[$desc_key];
						$fab_desc_type=$desc_key;
						$po_nos=rtrim($val['po_no'],',');
						$po_nos=implode(",",array_unique(explode(",",$po_nos)));
						$poids=rtrim($val['po_id'],',');

						$po_ids=array_unique(explode(",",$poids));
						$issue_ret_qnty=0;$receive_ret_qnty=0;
						foreach($po_ids as $po_id)
						{
							//echo $job_key."=".$po_id."=".$color_key."=".$product_array[$desc_key]['yarn_did']."=".$product_array[$desc_key]['dia_width']."=".$product_array[$desc_key]['fin_fab_qnty']."<br>";
						
							//echo $job_key.'ii'.$po_id;
							//echo $job_key."=".$po_id."=".$color_key."=".$desc_key."<br/>";
							//echo $issue_ret_qnty.",";
						}

						//echo $job_key.'ii';
						$po_ids=implode(",",$po_ids);


						$uomIdsStore=chop($val['uom'],',');
						$uomIdsStore=array_unique(explode(",",$val['uom']));
						$rec_qtyStore=array();
						$rec_tranfInStore=array();
						$rec_issueRtnStore=array();
						$iss_qtyStore=array();
						$iss_trnsOutStore=array();
						$iss_recvRtnStore=array();
						$total_recQntyStore=array();
						$total_issQntyStore=array();
						$iss_qty_calStore=array();
						$rec_qty_calStore=array();
						$stockStore=array();
						$StockValueStore=array();
						$StockValueTotalStore=array();
						$stockTotalStore=array();

						$recv_amount_all_Store 				=array();
						$recv_issue_rtn_amount_all_Store 		=array();
						$recv_trans_in_amount_all_Store 		=array();
						$issue_amount_all_Store 				=array();
						$issue_rev_rtn_amount_all_Store 		=array();
						$issue_trans_out_amount_all_Store 	=array();

						foreach ($uomIdsStore as $uomIdsSto)
						{
							$rec_qtyStore[$storeId][$uomIdsSto]=$style_wise_arr_uom_store[$storeId][$uomIdsSto]['receive_qnty'];
							$rec_tranfInStore[$storeId][$uomIdsSto]=$style_wise_arr_uom_store[$storeId][$uomIdsSto]['finish_fabric_transfer_in'];
							$rec_issueRtnStore[$storeId][$uomIdsSto]=$style_wise_arr_uom_store[$storeId][$uomIdsSto]['issue_rtn'];

							$iss_qtyStore[$storeId][$uomIdsSto]=$style_wise_arr_uom_store[$storeId][$uomIdsSto]['issue_qnty'];
							$iss_trnsOutStore[$storeId][$uomIdsSto]=$style_wise_arr_uom_store[$storeId][$uomIdsSto]['finish_fabric_transfer_out'];
							$iss_recvRtnStore[$storeId][$uomIdsSto]=$style_wise_arr_uom_store[$storeId][$uomIdsSto]['recv_rtn'];

							$total_recQntyStore[$storeId][$uomIdsSto]=$rec_qtyStore[$storeId][$uomIdsSto]+$rec_tranfInStore[$storeId][$uomIdsSto]+$rec_issueRtnStore[$storeId][$uomIdsSto];
							$total_issQntyStore[$storeId][$uomIdsSto]=$iss_qtyStore[$storeId][$uomIdsSto]+$iss_trnsOutStore[$storeId][$uomIdsSto]+$iss_recvRtnStore[$storeId][$uomIdsSto];

							$rec_qty_calStore[$storeId][$uomIdsSto]=($total_recQntyStore[$storeId][$uomIdsSto]);
							$iss_qty_calStore[$storeId][$uomIdsSto]=($total_issQntyStore[$storeId][$uomIdsSto]);
							

							$stockStoreWise[$storeId][$uomIdsSto]=$rec_qty_calStore[$storeId][$uomIdsSto]-$iss_qty_calStore[$storeId][$uomIdsSto];
							
							$stockTotalStore[$uomIdsSto]=$rec_qty_calStore[$storeId][$uomIdsSto]-$iss_qty_calStore[$storeId][$uomIdsSto];
							$totalStockUomArrStore[$uomIdsSto]+=$rec_qty_calStore[$storeId][$uomIdsSto]-$iss_qty_calStore[$storeId][$uomIdsSto];

							$recv_amount_all_Store[$storeId][$uomIdsSto] 				=$style_wise_arr_uom_store[$storeId][$uomIdsSto]['receive_amount'];
							$recv_issue_rtn_amount_all_Store[$storeId][$uomIdsSto] 		=$style_wise_arr_uom_store[$storeId][$uomIdsSto]['issue_rtn_amount'];
							$recv_trans_in_amount_all_Store[$storeId][$uomIdsSto] 		=$style_wise_arr_uom_store[$storeId][$uomIdsSto]['transfer_in_amount'];
							$issue_amount_all_Store[$storeId][$uomIdsSto]				=$style_wise_arr_uom_store[$storeId][$uomIdsSto]['issue_amount'];
							$issue_rev_rtn_amount_all_Store[$storeId][$uomIdsSto] 		=$style_wise_arr_uom_store[$storeId][$uomIdsSto]['recv_rtn_amount'];
							$issue_trans_out_amount_all_Store[$storeId][$uomIdsSto] 	=$style_wise_arr_uom_store[$storeId][$uomIdsSto]['transfer_out_amount'];
			

							if(is_nan($recv_amount_all_Store[$storeId][$uomIdsSto])){$recv_amount_all_Store[$storeId][$uomIdsSto]=0;}
							if(is_nan($recv_issue_rtn_amount_all_Store[$storeId][$uomIdsSto])){$recv_issue_rtn_amount_all_Store[$storeId][$uomIdsSto]=0;}
							if(is_nan($recv_trans_in_amount_all_Store[$storeId][$uomIdsSto])){$recv_trans_in_amount_all_Store[$storeId][$uomIdsSto]=0;}
							if(is_nan($issue_amount_all_Store[$storeId][$uomIdsSto])){$issue_amount_all_Store[$storeId][$uomIdsSto]=0;}
							if(is_nan($issue_rev_rtn_amount_all_Store[$storeId][$uomIdsSto])){$issue_rev_rtn_amount_all_Store[$storeId][$uomIdsSto]=0;}
							if(is_nan($issue_trans_out_amount_all_Store[$storeId][$uomIdsSto])){$issue_trans_out_amount_all_Store[$storeId][$uomIdsSto]=0;}


							$rec_amount_total_store[$storeId][$uomIdsSto]=($style_wise_arr_uom_store[$storeId][$uomIdsSto]['receive_amount']+$style_wise_arr_uom_store[$storeId][$uomIdsSto]['transfer_in_amount']+$style_wise_arr_uom_store[$storeId][$uomIdsSto]['issue_rtn_amount']);
							
							$rec_avg_rate_total_store[$storeId][$uomIdsSto]=$rec_amount_total_store[$storeId][$uomIdsSto]/$total_recQnty[$storeId][$uomIdsSto];

							$iss_amount_totalStore[$storeId][$uomIdsSto]=($style_wise_arr_uom_store[$storeId][$uomIdsSto]['issue_amount']+$style_wise_arr_uom_store[$storeId][$uomIdsSto]['transfer_out_amount']+$style_wise_arr_uom_store[$storeId][$uomIdsSto]['recv_rtn_amount']);
							$issue_avg_rate_totalStore[$storeId][$uomIdsSto]=$iss_amount_totalStore[$storeId][$uomIdsSto]/$total_issQnty[$storeId][$uomIdsSto];

							if (number_format($stockStoreWise[$storeId][$uomIdsSto],2,'.','')>0.00) {
								$StockValueStoreWise[$storeId][$uomIdsSto]=$rec_amount_total_store[$storeId][$uomIdsSto]-$iss_amount_totalStore[$storeId][$uomIdsSto];					
							}

							$totalStockValueUomArrStore[$uomIdsSto]+=$rec_amount_total_store[$storeId][$uomIdsSto]-$iss_amount_totalStore[$storeId][$uomIdsSto];
							
							$StockValueTotalStore[$uomIdsSto]=$rec_amount_total_store[$storeId][$uomIdsSto]-$iss_amount_totalStore[$storeId][$uomIdsSto];
						}

						$totalStockValueStoreValRangeChk=0;											
						foreach ($uomArrForStore as $key => $valueUOMs) 
						{
							$totalStockValueStoreValRangeChk+=$StockValueStoreWise[$storeId][$valueUOMs];
						}


						if($cbo_value_range_by==2 &&  number_format($totalStockValueStoreValRangeChk,2,'.','')>0.00)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="100" title="<? echo $val[("store_id")]; ?>"><p><? echo $store_lib_arr[$val[("store_id")]]; ?></p></td>
								
								<? 
								$totalStockValueStore=0;											
								foreach ($uomArrForStore as $key => $valueUOM) 
								{
									$totalStockValueStore+=$StockValueStoreWise[$storeId][$valueUOM];
									?>
									<td align="right" width="100"><? //echo $stockStoreWise[$buyerIds][$uomID] ;
									//echo $stockStoreWise[$buyerId][$valueUOM];
									echo number_format($stockStoreWise[$storeId][$valueUOM],2);
									?></td>
									<td align="right" width="100"><? echo number_format($StockValueStoreWise[$storeId][$valueUOM],2); ?></td>
									<? 
								}
								?>
								<td width="" align="right"><p><? echo number_format($totalStockValueStore,2,'.',''); ?></p></td>
							</tr>
								<?
								$grandTotalStockValueStore+=$totalStockValueStore;
								$i++;

						}
						else if($cbo_value_range_by==1 || $cbo_value_range_by==0)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="100" title="<? echo $val[("store_id")]; ?>"><p><? echo $store_lib_arr[$val[("store_id")]]; ?></p></td>
								
								<? 
								$totalStockValueStore=0;											
								foreach ($uomArrForStore as $key => $valueUOM) 
								{
									$totalStockValueStore+=$StockValueStoreWise[$storeId][$valueUOM];
									?>
									<td align="right" width="100"><? //echo $stockStoreWise[$buyerIds][$uomID] ;
									//echo $stockStoreWise[$buyerId][$valueUOM];
									echo number_format($stockStoreWise[$storeId][$valueUOM],2);
									?></td>
									<td align="right" width="100"><? echo number_format($StockValueStoreWise[$storeId][$valueUOM],2); ?></td>
									<? 
								}
								?>
								<td width="" align="right"><p><? echo number_format($totalStockValueStore,2,'.',''); ?></p></td>
							</tr>
								<?
								$grandTotalStockValueStore+=$totalStockValueStore;
								$i++;
						}
	
					}
				
						
						?>
					</table>
                	<table width="<? echo 350+$uom_th_widthStore; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
                    <tfoot>
                        <th width="30"></th>
						<th width="100"></th>
						<? 

						foreach ($uomArrForStore as $key => $valuesStore) 
						{
							
							?>
								<th width="100" align="right"><? echo number_format($totalStockUomArrStore[$valuesStore],2); ?></th>
								<th width="100" align="right"><? echo number_format($totalStockValueUomArrStore[$valuesStore],2); ?></th>
							<?
						}
						?>	
						<th width="" align="right" ><? echo number_format($grandTotalStockValueStore,2); ?></th>
                        
                    </tfoot>
                </table>
            </div>



            <br/>
            <h3>Buyer Wise Stock and Value Summary </h3>
			<table width="<? echo 350+$uom_th_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="100">Buyer</th>

						<? foreach ($uomArr as $key => $values) 
						{
							?>
							<th width="100">Stock Qnty (<? echo $unit_of_measurement[$values]; ?>)</th>
							<th width="100">Stock Value</th>
							<?
						}
						?>
						<th>Total Value</th>
										
					</tr>
					
				</thead>
			</table>
			<div style="width:<? echo 370+$uom_th_width; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="<? echo 350+$uom_th_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
					<?
					$i=1;
					$totalStockUomArr=array();
					$totalStockValueUomArr=array();
					$grandTotalStockValue=0;
					foreach ($style_wise_arr as $buyerId => $val)
					{
						//--------------------------------
						//if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						//$fab_desc_type=$product_arr[$desc_key];
						$fab_desc_type=$desc_key;
						$po_nos=rtrim($val['po_no'],',');
						$po_nos=implode(",",array_unique(explode(",",$po_nos)));
						$poids=rtrim($val['po_id'],',');

						$po_ids=array_unique(explode(",",$poids));
						$issue_ret_qnty=0;$receive_ret_qnty=0;
						foreach($po_ids as $po_id)
						{
							//echo $job_key."=".$po_id."=".$color_key."=".$product_array[$desc_key]['yarn_did']."=".$product_array[$desc_key]['dia_width']."=".$product_array[$desc_key]['fin_fab_qnty']."<br>";
						
							//echo $job_key.'ii'.$po_id;
							//echo $job_key."=".$po_id."=".$color_key."=".$desc_key."<br/>";
							//echo $issue_ret_qnty.",";
						}

						//echo $job_key.'ii';
						$po_ids=implode(",",$po_ids);


						$uomIds=chop($val['uom'],',');
						$uomIds=array_unique(explode(",",$val['uom']));
						$rec_qty=array();
						$rec_tranfIn=array();
						$rec_issueRtn=array();
						$iss_qty=array();
						$iss_trnsOut=array();
						$iss_recvRtn=array();
						$total_recQnty=array();
						$total_issQnty=array();
						$iss_qty_cal=array();
						$stock=array();
						$StockValue=array();
						$StockValueTotal=array();
						$stockTotal=array();

						$recv_amount_all 				=array();
						$recv_issue_rtn_amount_all 		=array();
						$recv_trans_in_amount_all 		=array();
						$issue_amount_all 				=array();
						$issue_rev_rtn_amount_all 		=array();
						$issue_trans_out_amount_all 	=array();

						foreach ($uomIds as $uomIdss)
						{
							$rec_qty[$buyerId][$uomIdss]=$style_wise_arr_uom[$buyerId][$uomIdss]['receive_qnty'];
							$rec_tranfIn[$buyerId][$uomIdss]=$style_wise_arr_uom[$buyerId][$uomIdss]['finish_fabric_transfer_in'];
							$rec_issueRtn[$buyerId][$uomIdss]=$style_wise_arr_uom[$buyerId][$uomIdss]['issue_rtn'];

							$iss_qty[$buyerId][$uomIdss]=$style_wise_arr_uom[$buyerId][$uomIdss]['issue_qnty'];
							$iss_trnsOut[$buyerId][$uomIdss]=$style_wise_arr_uom[$buyerId][$uomIdss]['finish_fabric_transfer_out'];
							$iss_recvRtn[$buyerId][$uomIdss]=$style_wise_arr_uom[$buyerId][$uomIdss]['recv_rtn'];

							$total_recQnty[$buyerId][$uomIdss]=$rec_qty[$buyerId][$uomIdss]+$rec_tranfIn[$buyerId][$uomIdss]+$rec_issueRtn[$buyerId][$uomIdss];
							$total_issQnty[$buyerId][$uomIdss]=$iss_qty[$buyerId][$uomIdss]+$iss_trnsOut[$buyerId][$uomIdss]+$iss_recvRtn[$buyerId][$uomIdss];

							$rec_qty_cal[$buyerId][$uomIdss]=($total_recQnty[$buyerId][$uomIdss]);
							$iss_qty_cal[$buyerId][$uomIdss]=($total_issQnty[$buyerId][$uomIdss]);
							$stock[$buyerId][$uomIdss]=$rec_qty_cal[$buyerId][$uomIdss]-$iss_qty_cal[$buyerId][$uomIdss];
							$stockTotal[$uomIdss]=$rec_qty_cal[$buyerId][$uomIdss]-$iss_qty_cal[$buyerId][$uomIdss];


							$totalStockUomArr[$uomIdss]+=$rec_qty_cal[$buyerId][$uomIdss]-$iss_qty_cal[$buyerId][$uomIdss];

							$recv_amount_all[$buyerId][$uomIdss] 				=$style_wise_arr_uom[$buyerId][$uomIdss]['receive_amount'];
							$recv_issue_rtn_amount_all[$buyerId][$uomIdss] 		=$style_wise_arr_uom[$buyerId][$uomIdss]['issue_rtn_amount'];
							$recv_trans_in_amount_all[$buyerId][$uomIdss] 		=$style_wise_arr_uom[$buyerId][$uomIdss]['transfer_in_amount'];
							$issue_amount_all[$buyerId][$uomIdss]				=$style_wise_arr_uom[$buyerId][$uomIdss]['issue_amount'];
							$issue_rev_rtn_amount_all[$buyerId][$uomIdss] 		=$style_wise_arr_uom[$buyerId][$uomIdss]['recv_rtn_amount'];
							$issue_trans_out_amount_all[$buyerId][$uomIdss] 	=$style_wise_arr_uom[$buyerId][$uomIdss]['transfer_out_amount'];
			

							if(is_nan($recv_amount_all[$buyerId][$uomIdss])){$recv_amount_all[$buyerId][$uomIdss]=0;}
							if(is_nan($recv_issue_rtn_amount_all[$buyerId][$uomIdss])){$recv_issue_rtn_amount_all[$buyerId][$uomIdss]=0;}
							if(is_nan($recv_trans_in_amount_all[$buyerId][$uomIdss])){$recv_trans_in_amount_all[$buyerId][$uomIdss]=0;}
							if(is_nan($issue_amount_all[$buyerId][$uomIdss])){$issue_amount_all[$buyerId][$uomIdss]=0;}
							if(is_nan($issue_rev_rtn_amount_all[$buyerId][$uomIdss])){$issue_rev_rtn_amount_all[$buyerId][$uomIdss]=0;}
							if(is_nan($issue_trans_out_amount_all[$buyerId][$uomIdss])){$issue_trans_out_amount_all[$buyerId][$uomIdss]=0;}


							$rec_amount_total[$buyerId][$uomIdss]=($style_wise_arr_uom[$buyerId][$uomIdss]['receive_amount']+$style_wise_arr_uom[$buyerId][$uomIdss]['transfer_in_amount']+$style_wise_arr_uom[$buyerId][$uomIdss]['issue_rtn_amount']);
							
							$rec_avg_rate_total[$buyerId][$uomIdss]=$rec_amount_total[$buyerId][$uomIdss]/$total_recQnty[$buyerId][$uomIdss];

							$iss_amount_total[$buyerId][$uomIdss]=($style_wise_arr_uom[$buyerId][$uomIdss]['issue_amount']+$style_wise_arr_uom[$buyerId][$uomIdss]['transfer_out_amount']+$style_wise_arr_uom[$buyerId][$uomIdss]['recv_rtn_amount']);
							$issue_avg_rate_total[$buyerId][$uomIdss]=$iss_amount_total[$buyerId][$uomIdss]/$total_issQnty[$buyerId][$uomIdss];

							if (number_format($stock[$buyerId][$uomIdss],2,'.','')>0.00) {
								$StockValue[$buyerId][$uomIdss]=$rec_amount_total[$buyerId][$uomIdss]-$iss_amount_total[$buyerId][$uomIdss];
							}

							$totalStockValueUomArr[$uomIdss]+=$rec_amount_total[$buyerId][$uomIdss]-$iss_amount_total[$buyerId][$uomIdss];
							
							$StockValueTotal[$uomIdss]=$rec_amount_total[$buyerId][$uomIdss]-$iss_amount_total[$buyerId][$uomIdss];
						}

						$totalStockValueValRangeChk=0;											
						foreach ($uomArr as $key => $valueUom) 
						{
							$totalStockValueValRangeChk+=$StockValue[$buyerId][$valueUom];
						}

						if($cbo_value_range_by==2 &&  number_format($totalStockValueValRangeChk,2,'.','')>0.00)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
								
								<? 
								$totalStockValue=0;											
								foreach ($uomArr as $key => $value) 
								{
									$totalStockValue+=$StockValue[$buyerId][$value];
									?>
									<td align="right" width="100"><? //echo $stock[$buyerIds][$uomID] ;
									//echo $stock[$buyerId][$value];
									echo number_format($stock[$buyerId][$value],2);
									?></td>
									<td align="right" width="100"><? echo number_format($StockValue[$buyerId][$value],2); ?></td>
									<? 
								}
								?>
								<td width="" align="right"><p><? echo number_format($totalStockValue,2,'.',''); ?></p></td>
							</tr>
								<?
								$grandTotalStockValue+=$totalStockValue;
								$i++;

						}
						else if($cbo_value_range_by==1 || $cbo_value_range_by==0)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
								
								<? 
								$totalStockValue=0;											
								foreach ($uomArr as $key => $value) 
								{
									$totalStockValue+=$StockValue[$buyerId][$value];
									?>
									<td align="right" width="100"><? //echo $stock[$buyerIds][$uomID] ;
									//echo $stock[$buyerId][$value];
									echo number_format($stock[$buyerId][$value],2);
									?></td>
									<td align="right" width="100"><? echo number_format($StockValue[$buyerId][$value],2); ?></td>
									<? 
								}
								?>
								<td width="" align="right"><p><? echo number_format($totalStockValue,2,'.',''); ?></p></td>
							</tr>
								<?
								$grandTotalStockValue+=$totalStockValue;
								$i++;
						}
	
					}
						
						?>
					</table>
                	<table width="<? echo 350+$uom_th_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
                    <tfoot>
                        <th width="30"></th>
						<th width="100"></th>
						<? 

						foreach ($uomArr as $key => $valuess) 
						{
							
							?>
								<th width="100" align="right"><? echo number_format($totalStockUomArr[$valuess],2); ?></th>
								<th width="100" align="right"><? echo number_format($totalStockValueUomArr[$valuess],2); ?></th>
							<?
						}
						?>	
						<th width="" align="right" ><? echo number_format($grandTotalStockValue,2); ?></th>
                        
                    </tfoot>
                </table>
            </div>
        </fieldset>
        <?
    }

    $html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$report_type";
	exit();
}
if($action=="report_generate5")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	//$cbo_presentation=str_replace("'","",$cbo_presentation);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_search_comm=str_replace("'","",$txt_search_comm);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_store_name=str_replace("'","",$cbo_store_id);
	$cbo_shipment_status=str_replace("'","",$cbo_shipment_status);
	$cbo_value_range_by=str_replace("'","",$cbo_value_range_by);
	$cbo_location_ids=str_replace("'","",$cbo_location_id);
	$cbo_uom=str_replace("'","",$cbo_uom);
	if($cbo_uom) $uom_cond = " and c.cons_uom in ($cbo_uom) "; else $uom_cond = "";
	//echo $cbo_report_type;die;

	if($cbo_store_name>0){$storeCond="and c.store_id in($cbo_store_name)";}else{$storeCond="";}
	if($cbo_shipment_status>0){$shiping_status_cond=" and b.shiping_status in ($cbo_shipment_status)";}

	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";
	$job_no=str_replace("'","",$txt_job_no);
	$search_cond='';
	if($cbo_search_by==1)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.job_no_prefix_num in ($txt_search_comm) ";
	}
	else if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.style_ref_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.po_number LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.file_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==5)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.grouping LIKE '%$txt_search_comm%'";
	}
	else
	{
		$search_cond.="";
	}

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date=""; else $receive_date= " and c.transaction_date <=".$txt_date_from."";

	if( $date_from=="") $today_receive_date=""; else $today_receive_date= " c.transaction_date=".$txt_date_from."";

	$cbo_year_val=str_replace("'","",$cbo_year);
	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";
	if($db_type==0)
	{
		$prod_id_cond=" group_concat(b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and year(a.insert_date)='$cbo_year_val'"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$prod_id_cond=" listagg(cast(b.from_prod_id as varchar2(4000)),',') within group (order by b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and to_char(a.insert_date,'YYYY')='$cbo_year_val'";  else $year_cond="";
	}

	$product_array=array(); $allProductIdsArr=array();

	$cbo_uom=str_replace("'","",$cbo_uom);
	if($cbo_uom) $uom_cond_prod = " and unit_of_measure in ($cbo_uom) "; else $uom_cond_prod = "";
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$company_lib_arr=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$store_lib_arr=return_library_array( "select id, store_name from LIB_STORE_LOCATION", "id", "store_name"  );
	$body_part_lib_arr=return_library_array( "select id, body_part_full_name from lib_body_part", "id", "body_part_full_name"  );
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$product_sql = sql_select("select id, product_name_details,unit_of_measure from product_details_master where status_active = 1 and is_deleted = 0 $uom_cond_prod");
	foreach ($product_sql as $prow)
	{
		$product_arr[$prow[csf("id")]] = $prow[csf("product_name_details")];
		$uomFromProductArr[$prow[csf("id")]] = $prow[csf("unit_of_measure")];
		$productIds .= $prow[csf("id")].",";
	}


	$allProductIdsArr = array_filter(array_unique(explode(",",chop($productIds,","))));
	if(count($allProductIdsArr) > 999 && $db_type == 2 )
	{
		$allProductIdsChunkArr=array_chunk($allProductIdsArr,999) ;
		foreach($allProductIdsChunkArr as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$pid_cond.="  d.prod_id in($chunk_arr_value) or ";
		}

		$productId_cond.=" and (".chop($pid_cond,'or ').")";
	}else{

		$productId_cond.=" and d.prod_id in (".implode(",",$allProductIdsArr).")";
	}
	if($cbo_uom) $productId_cond=$productId_cond;else $productId_cond='';
	if($db_type==0)
	{
		$select_fld= "year(a.insert_date) as year";
	}
	else if($db_type==2)
	{
		$select_fld= "TO_CHAR(a.insert_date,'YYYY') as year";
	}
 	if($cbo_report_type==2) // Woven Finish Start
	{
		$product_array=array();
		$sql_product="select id, color, detarmination_id,dia_width,weight,product_name_details from product_details_master where item_category_id=3 and status_active=1 and is_deleted=0";
		$sql_product_result=sql_select($sql_product);
		foreach( $sql_product_result as $row )
		{
			$product_array[$row[csf('product_name_details')]]['color']=$row[csf('color')];
			$product_array[$row[csf('product_name_details')]]['dia_width']=$row[csf('dia_width')];
			$product_array[$row[csf('product_name_details')]]['weight']=$row[csf('weight')];
			$product_array[$row[csf('product_name_details')]]['yarn_did']=$row[csf('detarmination_id')];
		}
		$issue_qnty=array();
		$sql_issue=sql_select(" select b.po_breakdown_id,b.color_id, sum(b.quantity) as issue_qnty  from inv_issue_master a,inv_transaction c, order_wise_pro_details b where a.id=c.mst_id and c.prod_id=b.prod_id  and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and a.issue_purpose in(4,9) and b.entry_form=19 $storeCond group by b.po_breakdown_id,b.color_id");
		foreach( $sql_issue as $row_iss )
		{
			$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
		} //var_dump($issue_qnty);

		$booking_qnty=array(); $booking_qnty_chk= array();
		$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty ) as fin_fab_qnty, b.id as dtls_id, c.uom, c.lib_yarn_count_deter_id,b.gsm_weight,b.dia_width from wo_booking_mst a, wo_booking_dtls b , wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
		/*$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty) as fin_fab_qnty, c.lib_yarn_count_deter_id,b.gsm_weight,b.dia_width from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");*/
		foreach( $sql_booking as $row)
		{
			if($booking_qnty_chk[$row[csf('dtls_id')]] == "")
			{
				$booking_qnty_chk[$row[csf('dtls_id')]]=$row[csf('dtls_id')];
				$booking_qnty[$row[csf('uom')]][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]][$row[csf('gsm_weight')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
			}
		}

		/*echo "<pre>";
		print_r($booking_qnty);*/

		unset($sql_booking);

		$iss_return_qnty=array();
		$sql_issue_ret=sql_select("select a.job_no,d.po_breakdown_id as po_id, d.color_id,d.prod_id, (d.quantity) as issue_ret_qnty  from wo_po_details_master a, wo_po_break_down b,order_wise_pro_details d,inv_transaction c where a.job_no=b.job_no_mst and b.id=d.po_breakdown_id and d.trans_id=c.id and  d.trans_id!=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=209  and
			a.company_name in(".$cbo_company_id.")  $buyer_id_cond $search_cond $year_cond $storeCond $shiping_status_cond");
		foreach( $sql_issue_ret as $row )
		{
			$fab_desc_tmp1=explode(",",$product_arr[$row[csf('prod_id')]]);
			$fab_desc_tmp=$fab_desc_tmp1[0];

			$iss_return_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('prod_id')]]['issue_ret_qnty']+=$row[csf('issue_ret_qnty')];
		}
		unset($sql_issue_ret);
		//$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		$fabric_type_arr=return_library_array( "select id, type from lib_yarn_count_determina_mst where fab_nature_id=3 and entry_form=426", "id", "type"  );

		ob_start();
		?>
		<fieldset style="width:1840px;">
			<table cellpadding="0" cellspacing="0" width="1810">
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="15" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="15" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="15" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="2510" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>

					<tr>
						<th width="30" rowspan="2">SL</th>
						<th width="100" rowspan="2">Company</th>
						<th width="100" rowspan="2">Store Name</th>
						<th width="100" rowspan="2">Buyer</th>
						<th width="60" rowspan="2">Job</th>
						<th width="50" rowspan="2">Year</th>
						<th width="110" rowspan="2">Style</th>

						<th width="60" rowspan="2">Order Status</th>
						<th width="100" rowspan="2">Shiping Status</th>
						<th width="110" rowspan="2">Fin. Fab. Color</th>
						<th width="100" rowspan="2">Body Part</th>
						<th width="100" rowspan="2">Fab. Type</th>
						<th width="220" rowspan="2">Fab. Desc.</th>
						<th width="50" rowspan="2">UOM</th>

						<th width="80" rowspan="2">Req. Qty</th>

						<th width="240" colspan="3">Today Recv.</th>
						<th width="240" title="Rec.+Issue Ret.+Trans. in" colspan="3">Total Received</th>
					

						<th width="80" title="Req.-Totat Rec." rowspan="2">Received Balance</th>
						<th width="240" colspan="3">Today Issue</th>
						<th width="240" title="Issue+Rec. Ret.+Trans. out" colspan="3">Total Issued</th>
						<th width="" title="Total Rec.- Total Issue" rowspan="2">Stock</th>
					</tr>
					<tr>
						<th width="80">Receive</th>
						<th width="80">Issue Return</th>
						<th width="80">Transfer In</th>
						<th width="80">Receive</th>
						<th width="80">Issue Return</th>
						<th width="80">Transfer In</th>
						<th width="80">Issue</th>
						<th width="80">Receive Return</th>
						<th width="80">Transfer Out</th>
						<th width="80">Issue</th>
						<th width="80">Receive Return</th>
						<th width="80">Transfer Out</th>
					</tr>
				</thead>
			</table>
			<div style="width:2530px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="2510" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
					<?
					if($db_type==0) $prod_cond="group_concat(e.prod_id) as prod_id";
					else if($db_type==2) $prod_cond="listagg(cast(e.prod_id as varchar2(4000)),',') within group (order by e.prod_id) as prod_id";


						$sql_query="
						Select
						a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number as po_no,d.color_id,d.prod_id, c.order_rate, $select_fld,e.product_name_details as prod_desc,e.detarmination_id,c.store_id,c.body_part_id , 
						(case when d.entry_form in (17) then d.quantity else 0 end) as receive_qnty,
						(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then d.quantity else 0 end) as finish_fabric_transfer_in, 
						(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then d.quantity else 0 end) as issue_rtn,
						(case when d.entry_form in (17) and  $today_receive_date then d.quantity else 0 end) as today_receive_qnty,
						(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 and $today_receive_date then d.quantity else 0 end) as today_trans_in,
						(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 and  $today_receive_date then d.quantity else 0 end) as today_issue_rtn, 
						(case when d.entry_form in (19) then d.quantity else 0 end) as issue_qnty, (case when d.entry_form in (258) 
						and c.transaction_type=6 and d.trans_type=6 then d.quantity else 0 end) as finish_fabric_transfer_out, 
						(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then d.quantity else 0 end) as recv_rtn,
						(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 and  $today_receive_date then d.quantity else 0 end) as today_recv_rtn, 
						(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 and $today_receive_date then d.quantity else 0 end) as today_trans_out,
						(case when d.entry_form in (19) and  $today_receive_date then d.quantity else 0 end) as today_issue_qnty 

						from
						wo_po_details_master a,
						wo_po_break_down b,
						inv_transaction c,
						order_wise_pro_details d,product_details_master e 
						where
						a.job_no=b.job_no_mst 
						and d.po_breakdown_id=b.id 
						and d.trans_id=c.id and d.prod_id=e.id and c.prod_id=e.id 
						and d.trans_id!=0
						and d.entry_form in (17,19,202,209,258) 
						and c.item_category=3 
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
						and a.company_name in(".$cbo_company_id.") $receive_date $buyer_id_cond $search_cond $year_cond $storeCond $shiping_status_cond $uom_cond order by a.job_no,d.color_id";


					//echo $sql_query; die;
					$style_wise_arr=array();
					$nameArray=sql_select($sql_query);
					foreach ($nameArray as $row)
					{
						if($uomFromProductArr[$row[csf('prod_id')]] == "") continue;
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no']=$row[csf('job_no')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no_pre']=$row[csf('job_no_prefix_num')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['year']=$row[csf('year')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['buyer_name']=$row[csf('buyer_name')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['body_part_id']=$row[csf('body_part_id')];
						
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['companyId']=$row[csf('company_name')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['store_id']=$row[csf('store_id')];

						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['style_ref_no']=$row[csf('style_ref_no')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_id'].=$row[csf('po_id')].',';
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_no'].=$row[csf('po_no')].',';
						

						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['receive_qnty']+=$row[csf('receive_qnty')];
						
						
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_qnty']+=$row[csf('issue_qnty')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_rtn']+=$row[csf('issue_rtn')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['recv_rtn']+=$row[csf('recv_rtn')];
											
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_qnty']+=$row[csf('today_issue_qnty')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_trans_in']+=$row[csf('today_trans_in')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_trans_out']+=$row[csf('today_trans_out')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_rtn']+=$row[csf('today_issue_rtn')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_recv_rtn']+=$row[csf('today_recv_rtn')];

						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['finish_fabric_transfer_in']+=$row[csf('finish_fabric_transfer_in')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['finish_fabric_transfer_out']+=$row[csf('finish_fabric_transfer_out')];
						
													


						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['receive_amount']+=($row[csf('receive_qnty')]*$row[csf('order_rate')]);

						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['detarmination_id']=$row[csf('detarmination_id')];

						
						//$style_wise_arr[$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_id')]]['stock_value']+=(($row[csf('receive_qnty')]*1 - $row[csf('issue_qnty')]*1)*$row[csf('order_rate')]);
						
						//echo $row[csf('receive_qnty')]."=".$row[csf('issue_qnty')]."=".(($row[csf('receive_qnty')]*1-$row[csf('issue_qnty')]*1)."=".$row[csf('order_rate')])."<br>";
					}
					//echo "<pre>";
					//print_r($style_wise_arr); die;

					$i=1;
					foreach ($style_wise_arr as $compaId => $company_data)
					{
						foreach ($company_data as $storeId => $store_data)
						{
							foreach ($store_data as $cons_uom => $cons_uom_data)
							{
								$sub_req_qty=0;
								
								$sub_today_recv = 0;
								$sub_today_recv_rtn = 0;
								$sub_today_issue = 0;
								$sub_today_issue_rtn = 0;
								$sub_today_trans_in = 0;
								$sub_today_trans_out = 0;

								$sub_rec_qty = 0;
								$sub_issue_qty = 0;
								$sub_rec_tranfIn = 0;
								$sub_rec_issueRtn = 0;
								$sub_iss_trnsOut = 0;
								$sub_iss_recvRtn = 0;

								$sub_rec_bal = 0;
								$sub_stock=0;

								foreach ($cons_uom_data  as $job_key=>$job_val)
								{
									foreach ($job_val  as $color_key=>$color_val)
									{
										foreach ($color_val  as $desc_key=>$val)
										{
											//if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

											$dzn_qnty=0;
											
											if($costing_per_id_library[$job_key]==1) $dzn_qnty=12;
											else if($costing_per_id_library[$job_key]==3) $dzn_qnty=12*2;
											else if($costing_per_id_library[$job_key]==4) $dzn_qnty=12*3;
											else if($costing_per_id_library[$job_key]==5) $dzn_qnty=12*4;
											else $dzn_qnty=1;

											$color_id=$row[csf("color_id")];
											//$fab_desc_type=$product_arr[$desc_key];
											$fab_desc_type=$desc_key;
											$po_nos=rtrim($val['po_no'],',');
											$po_nos=implode(",",array_unique(explode(",",$po_nos)));
											$poids=rtrim($val['po_id'],',');

											$po_ids=array_unique(explode(",",$poids));
											$book_qty=0;$issue_ret_qnty=0;$receive_ret_qnty=0;
											foreach($po_ids as $po_id)
											{
												//echo $job_key."=".$po_id."=".$color_key."=".$product_array[$desc_key]['yarn_did']."=".$product_array[$desc_key]['dia_width']."=".$product_array[$desc_key]['fin_fab_qnty']."<br>";
												$book_qty+=$booking_qnty[$cons_uom][$job_key][$po_id][$color_key][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']][$product_array[$desc_key]['weight']]['fin_fab_qnty'];
												//echo $job_key.'ii'.$po_id;
												$issue_ret_qnty+=$iss_return_qnty[$job_key][$po_id][$color_key][$desc_key]['issue_ret_qnty'];
												//echo $job_key."=".$po_id."=".$color_key."=".$desc_key."<br/>";
												//echo $issue_ret_qnty.",";
												$receive_ret_qnty+=$rec_return_qnty[$job_key][$po_id][$color_key][$desc_key]['rec_ret_qnty'];
											}

											//echo $job_key.'ii';
											$po_ids=implode(",",$po_ids);
											$today_recv=$val[("today_receive_qnty")];
											$today_issue=$val[("today_issue_qnty")];
											$today_recv_rtn=$val[("today_recv_rtn")];
											$today_issue_rtn=$val[("today_issue_rtn")];
											$today_trans_in=$val[("today_trans_in")];
											$today_trans_out=$val[("today_trans_out")];

											$rec_qty=$val[("receive_qnty")];
											$rec_tranfIn=$val[("finish_fabric_transfer_in")];
											$rec_issueRtn=$val[("issue_rtn")];
			
											$iss_qty=$val[("issue_qnty")];
											$iss_trnsOut=$val[("finish_fabric_transfer_out")];
											$iss_recvRtn=$val[("recv_rtn")];
			
											$total_recQnty=$rec_qty+$rec_tranfIn+$rec_issueRtn;
											$total_issQnty=$iss_qty+$iss_trnsOut+$iss_recvRtn;

											$rec_bal=$book_qty-$total_recQnty-$rec_issueRtn; 
											
											$receive_amount=number_format($val[("receive_amount")],2,'.','');
											$rec_qty_cal=($total_recQnty);
											$iss_qty_cal=($total_issQnty);
											$stock=$rec_qty_cal-$iss_qty_cal;
											
											//rate calculating
											$rate = $receive_amount/$rec_qty;
											$issue_amount = number_format(($val[("issue_qnty")]*$rate),2,'.','');
											$stock_amount = number_format(($stock*$rate),2,'.','');

											if($cbo_value_range_by==2 &&  number_format($stock,2,'.','')>0.00)
											{
												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
													<td width="30"><? echo $i; ?></td>
													<td width="100"><p><? echo $company_lib_arr[$val[("companyId")]]; ?></p></td>
													<td width="100"><p><? echo $store_lib_arr[$val[("store_id")]]; ?></p></td>
													<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
													<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
													<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
													<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>
													<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
													<td width="100" title="<? echo $po_ids;?>"><p>
														<? 
															$po_ids_exp=explode(",", $po_ids);
															$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
															foreach ($po_ids_exp as $row) {
																if($po_id_shipingStatus_arr[$row]==3)
																{
																	$full_shipSts_countx++;
																}
																else if($po_id_shipingStatus_arr[$row]==2){
																	$partial_shipSts_countx++;
																}
																else if($po_id_shipingStatus_arr[$row]==1){
																	$panding_shipSts_countx++;
																}
																$poId_countx++;
															}
															if($full_shipSts_countx==$poId_countx){
																$ShipingStatus="Full Delivery/Closed";
															}
															else if ($partial_shipSts_countx==$poId_countx) {
																$ShipingStatus="Partial Delivery";
															}
															else if ($panding_shipSts_countx==$poId_countx) {
																$ShipingStatus="Full Pending";
															}
															else
															{
																$ShipingStatus="Partial Delivery";
															}
															echo $ShipingStatus;
														?></p></td>
													<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
													<td width="100"><p><? echo $body_part_lib_arr[$val[("body_part_id")]]; ?></p></td>
													<td width="100" align="center"><p><? echo $fabric_type_arr[$val[("detarmination_id")]]; ?></p></td>
													<td width="220" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $fab_desc_type; ?></p></td>
													<td width="50"><p><? echo $unit_of_measurement[$cons_uom]; ?></p></td>
													<td width="80" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>
																	

													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_receive_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</a></p></td>
													<td width="80" align="right"><p><a href='#report_details'  onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_issue_rtn_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_issue_rtn,2,'.',''); ?>&nbsp;</a></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_transf_in_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_trans_in,2,'.',''); ?>&nbsp;</a></p></td>

													
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_only_receive_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_only_issue_rtn_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($rec_issueRtn,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_only_transf_in_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($rec_tranfIn,2,'.',''); ?></a></p></td>


													<td width="80" align="right"><p><? echo number_format($rec_bal,2,'.',''); ?></p></td>

													
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_issue_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_recv_rtn_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_recv_rtn,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_transf_out_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_trans_out,2,'.',''); ?></a></p></td>


													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_only_issue_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_only_recv_rtn_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($iss_recvRtn,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_only_transf_out_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($iss_trnsOut,2,'.',''); ?></a></p></td>

													
													<td width="" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 6; ?>','woven_knit_stock_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($stock,2,'.',''); ?></a></p></td>
									
												</tr>
													<?
													$i++;

													$sub_req_qty+=$book_qty;
													$sub_rec_bal+=$rec_bal;
													$sub_stock+=$stock;
													
													//$sub_receive_amount+=$receive_amount;
													//$sub_issue_amount+=$issue_amount;
													//$sub_stock_value+=$stock_amount;

													$sub_rec_qty+=$rec_qty;
													$sub_issue_qty+=$iss_qty;

													$sub_rec_tranfIn+=$rec_tranfIn;
													$sub_rec_issueRtn+=$rec_issueRtn;
													$sub_iss_trnsOut+=$iss_trnsOut;
													$sub_iss_recvRtn+=$iss_recvRtn;

													$sub_today_recv +=$today_recv;
													$sub_today_recv_rtn +=$today_recv_rtn;
													$sub_today_issue+=$today_issue;
													$sub_today_issue_rtn +=$today_issue_rtn;
													$sub_today_trans_in +=$today_trans_in;
													$sub_today_trans_out +=$today_trans_out;

													
													$total_req_qty+=$book_qty;
													$total_rec_bal+=$rec_bal;
													
													$total_stock+=$stock;
													$total_possible_cut_pcs+=$possible_cut_pcs;
													$total_actual_cut_qty+=$actual_qty;
													$total_rec_return_qnty+=$receive_ret_qnty;
													$total_issue_ret_qnty+=$issue_ret_qnty;

													$total_rec_qty+=$rec_qty;
													$total_issue_qty+=$iss_qty;
													$total_rec_tranfIn+=$rec_tranfIn;
													$total_rec_issueRtn+=$rec_issueRtn;
													$total_iss_trnsOut+=$iss_trnsOut;
													$total_iss_recvRtn+=$iss_recvRtn;

													
													$total_today_recv+=$today_recv;
													$total_today_issue_rtn+=$today_issue_rtn;
													$total_today_trans_in+=$today_trans_in;

													$total_today_issue+=$today_issue;
													$total_today_recv_rtn+=$today_recv_rtn;
													$total_today_trans_out+=$today_trans_out;

													//$total_receive_amount+=$receive_amount;
													//$total_issue_amount+=$issue_amount;
													//$total_stock_value+=$stock_amount;
											}
											else if($cbo_value_range_by==1 || $cbo_value_range_by==0)
											{
												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
													<td width="30"><? echo $i; ?></td>
													<td width="100"><p><? echo $company_lib_arr[$val[("companyId")]]; ?></p></td>
													<td width="100"><p><? echo $store_lib_arr[$val[("store_id")]]; ?></p></td>
													<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
													<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
													<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
													<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>
													<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
													<td width="100" title="<? echo $po_ids;?>"><p>
														<? 
															$po_ids_exp=explode(",", $po_ids);
															$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
															foreach ($po_ids_exp as $row) {
																if($po_id_shipingStatus_arr[$row]==3)
																{
																	$full_shipSts_countx++;
																}
																else if($po_id_shipingStatus_arr[$row]==2){
																	$partial_shipSts_countx++;
																}
																else if($po_id_shipingStatus_arr[$row]==1){
																	$panding_shipSts_countx++;
																}
																$poId_countx++;
															}
															if($full_shipSts_countx==$poId_countx){
																$ShipingStatus="Full Delivery/Closed";
															}
															else if ($partial_shipSts_countx==$poId_countx) {
																$ShipingStatus="Partial Delivery";
															}
															else if ($panding_shipSts_countx==$poId_countx) {
																$ShipingStatus="Full Pending";
															}
															else
															{
																$ShipingStatus="Partial Delivery";
															}
															echo $ShipingStatus;
														?></p></td>
													<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
													<td width="100"><p><? echo $body_part_lib_arr[$val[("body_part_id")]]; ?></p></td>
													<td width="100" align="center"><p><? echo $fabric_type_arr[$val[("detarmination_id")]]; ?></p></td>
													<td width="220" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $fab_desc_type; ?></p></td>
													<td width="50"><p><? echo $unit_of_measurement[$cons_uom]; ?></p></td>
													<td width="80" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>
																	

													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_receive_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</a></p></td>
													<td width="80" align="right"><p><a href='#report_details'  onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_issue_rtn_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_issue_rtn,2,'.',''); ?>&nbsp;</a></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_transf_in_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_trans_in,2,'.',''); ?>&nbsp;</a></p></td>

													
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_only_receive_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_only_issue_rtn_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($rec_issueRtn,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_only_transf_in_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($rec_tranfIn,2,'.',''); ?></a></p></td>


													<td width="80" align="right"><p><? echo number_format($rec_bal,2,'.',''); ?></p></td>

													
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_issue_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_recv_rtn_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_recv_rtn,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_transf_out_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_trans_out,2,'.',''); ?></a></p></td>


													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_only_issue_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_only_recv_rtn_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($iss_recvRtn,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_only_transf_out_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($iss_trnsOut,2,'.',''); ?></a></p></td>

													
													<td width="" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 6; ?>','woven_knit_stock_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($stock,2,'.',''); ?></a></p></td>
									
												</tr>
													<?
													$i++;

													$sub_req_qty+=$book_qty;
													$sub_rec_bal+=$rec_bal;
													$sub_stock+=$stock;
													
													//$sub_receive_amount+=$receive_amount;
													//$sub_issue_amount+=$issue_amount;
													//$sub_stock_value+=$stock_amount;

													$sub_rec_qty+=$rec_qty;
													$sub_issue_qty+=$iss_qty;

													$sub_rec_tranfIn+=$rec_tranfIn;
													$sub_rec_issueRtn+=$rec_issueRtn;
													$sub_iss_trnsOut+=$iss_trnsOut;
													$sub_iss_recvRtn+=$iss_recvRtn;

													$sub_today_recv +=$today_recv;
													$sub_today_recv_rtn +=$today_recv_rtn;
													$sub_today_issue+=$today_issue;
													$sub_today_issue_rtn +=$today_issue_rtn;
													$sub_today_trans_in +=$today_trans_in;
													$sub_today_trans_out +=$today_trans_out;

													$total_req_qty+=$book_qty;
													$total_rec_bal+=$rec_bal;
													
													$total_stock+=$stock;
													$total_possible_cut_pcs+=$possible_cut_pcs;
													$total_actual_cut_qty+=$actual_qty;
													$total_rec_return_qnty+=$receive_ret_qnty;
													$total_issue_ret_qnty+=$issue_ret_qnty;

													$total_rec_qty+=$rec_qty;
													$total_issue_qty+=$iss_qty;
													$total_rec_tranfIn+=$rec_tranfIn;
													$total_rec_issueRtn+=$rec_issueRtn;
													$total_iss_trnsOut+=$iss_trnsOut;
													$total_iss_recvRtn+=$iss_recvRtn;

													$total_today_recv+=$today_recv;
													$total_today_issue_rtn+=$today_issue_rtn;
													$total_today_trans_in+=$today_trans_in;

													$total_today_issue+=$today_issue;
													$total_today_recv_rtn+=$today_recv_rtn;
													$total_today_trans_out+=$today_trans_out;

													//$total_receive_amount+=$receive_amount;
													//$total_issue_amount+=$issue_amount;
													//$total_stock_value+=$stock_amount;
											}

										}
									}
								}
									?>
									<tr style="font-weight:bold;background-color:#e0e0e0;">
										<td colspan="14" align="right">Store and UOM Wise Total</td>
										<td align="right"><? echo number_format($sub_req_qty,2,'.','') ?></td>

										<td align="right"><? echo number_format($sub_today_recv,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_today_issue_rtn,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_today_trans_in,2,'.','') ?></td>

										<td align="right"><? echo number_format($sub_rec_qty,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_rec_issueRtn,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_rec_tranfIn,2,'.','') ?></td>
									
										<td align="right"><? echo number_format($sub_rec_bal,2,'.','') ?></td>

										<td align="right"><? echo number_format($sub_today_issue,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_today_recv_rtn,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_today_trans_out,2,'.','') ?></td>
										
										<td align="right"><? echo number_format($sub_issue_qty,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_iss_recvRtn,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_iss_trnsOut,2,'.','') ?></td>

										<td align="right"><? echo number_format($sub_stock,2,'.','') ?></td>
									</tr>
									<?
							}
						}
					}
						?>
					</table>
                	<table width="2510" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
                    <tfoot>
                        <th width="30"></th>
						<th width="100"></th>
						<th width="100"></th>
                        <th width="100"></th>
                        <th width="60"></th>
                        <th width="50"></th>
                        <th width="110">&nbsp;</th>

                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
						<th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="220">&nbsp;</th>
                        <th width="50">Total</th>
                        <th width="80" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
                        
						<th width="80" align="right" id="value_total_today_rec_qty"><? //echo number_format($total_today_recv,2,'.',''); ?></th>
                        <th width="80" align="right" id="value_total_today_rec_qty"><? //echo number_format($total_today_issue_rtn,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_rec_qty"><? //echo number_format($total_today_trans_in,2,'.',''); ?></th>

						<th width="80" align="right" id="value_total_rec_qty"><? //echo number_format($total_rec_qty,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_rec_qty"><? //echo number_format($total_rec_issueRtn,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_rec_qty"><? //echo number_format($total_rec_tranfIn,2,'.',''); ?></th>

                        <th width="80" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>

                        <th width="80"  id="value_recv_today_issue_qty"><? //echo number_format($total_today_issue,2,'.',''); ?></th>
						<th width="80"  id="value_recv_today_issue_qty"><? //echo number_format($total_today_recv_rtn,2,'.',''); ?></th>
						<th width="80"  id="value_recv_today_issue_qty"><? //echo number_format($total_today_trans_out,2,'.',''); ?></th>

                        <th width="80" align="right" id="value_total_issue_qty"><? //echo number_format($total_issue_qty,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_issue_qty"><? //echo number_format($total_iss_recvRtn,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_issue_qty"><? //echo number_format($total_iss_trnsOut,2,'.',''); ?></th>


                        <th width="" align="right" id="value_total_stock"><? //echo number_format($total_stock,2,'.',''); ?></th>
                        
                    </tfoot>
                </table>
            </div>
        </fieldset>
        <?
    }

    $html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$report_type";
	exit();
}
if($action=="report_generate6")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	//$cbo_presentation=str_replace("'","",$cbo_presentation);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_search_comm=str_replace("'","",$txt_search_comm);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_store_name=str_replace("'","",$cbo_store_id);
	$cbo_shipment_status=str_replace("'","",$cbo_shipment_status);
	$cbo_value_range_by=str_replace("'","",$cbo_value_range_by);
	$cbo_location_ids=str_replace("'","",$cbo_location_id);
	$cbo_uom=str_replace("'","",$cbo_uom);
	if($cbo_uom) $uom_cond = " and c.cons_uom in ($cbo_uom) "; else $uom_cond = "";
	//echo $cbo_report_type;die;

	if($cbo_store_name>0){$storeCond="and c.store_id in($cbo_store_name)";}else{$storeCond="";}
	if($cbo_shipment_status>0){$shiping_status_cond=" and b.shiping_status in ($cbo_shipment_status)";}

	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";
	$job_no=str_replace("'","",$txt_job_no);
	$search_cond='';
	if($cbo_search_by==1)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.job_no_prefix_num in ($txt_search_comm) ";
	}
	else if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.style_ref_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.po_number LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.file_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==5)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.grouping LIKE '%$txt_search_comm%'";
	}
	else
	{
		$search_cond.="";
	}

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date=""; else $receive_date= " and c.transaction_date <=".$txt_date_from."";

	if( $date_from=="") $today_receive_date=""; else $today_receive_date= " c.transaction_date=".$txt_date_from."";

	$cbo_year_val=str_replace("'","",$cbo_year);
	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";
	if($db_type==0)
	{
		$prod_id_cond=" group_concat(b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and year(a.insert_date)='$cbo_year_val'"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$prod_id_cond=" listagg(cast(b.from_prod_id as varchar2(4000)),',') within group (order by b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and to_char(a.insert_date,'YYYY')='$cbo_year_val'";  else $year_cond="";
	}

	$product_array=array(); $allProductIdsArr=array();

	$cbo_uom=str_replace("'","",$cbo_uom);
	if($cbo_uom) $uom_cond_prod = " and unit_of_measure in ($cbo_uom) "; else $uom_cond_prod = "";
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$company_lib_arr=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$store_lib_arr=return_library_array( "select id, store_name from LIB_STORE_LOCATION", "id", "store_name"  );
	$body_part_lib_arr=return_library_array( "select id, body_part_full_name from lib_body_part", "id", "body_part_full_name"  );
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

	$product_sql = sql_select("select id, product_name_details,unit_of_measure from product_details_master where status_active = 1 and is_deleted = 0 $uom_cond_prod");
	foreach ($product_sql as $prow)
	{
		$product_arr[$prow[csf("id")]] = $prow[csf("product_name_details")];
		$uomFromProductArr[$prow[csf("id")]] = $prow[csf("unit_of_measure")];
		$productIds .= $prow[csf("id")].",";
	}


	$allProductIdsArr = array_filter(array_unique(explode(",",chop($productIds,","))));
	if(count($allProductIdsArr) > 999 && $db_type == 2 )
	{
		$allProductIdsChunkArr=array_chunk($allProductIdsArr,999) ;
		foreach($allProductIdsChunkArr as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$pid_cond.="  d.prod_id in($chunk_arr_value) or ";
		}

		$productId_cond.=" and (".chop($pid_cond,'or ').")";
	}else{

		$productId_cond.=" and d.prod_id in (".implode(",",$allProductIdsArr).")";
	}
	if($cbo_uom) $productId_cond=$productId_cond;else $productId_cond='';
	if($db_type==0)
	{
		$select_fld= "year(a.insert_date) as year";
	}
	else if($db_type==2)
	{
		$select_fld= "TO_CHAR(a.insert_date,'YYYY') as year";
	}
	if($cbo_report_type==2) // Woven Finish Start
	{
		$product_array=array();
		$sql_product="select id, color, detarmination_id,dia_width,weight,product_name_details from product_details_master where item_category_id=3 and status_active=1 and is_deleted=0";
		$sql_product_result=sql_select($sql_product);
		foreach( $sql_product_result as $row )
		{
			$product_array[$row[csf('product_name_details')]]['color']=$row[csf('color')];
			$product_array[$row[csf('product_name_details')]]['dia_width']=$row[csf('dia_width')];
			$product_array[$row[csf('product_name_details')]]['weight']=$row[csf('weight')];
			$product_array[$row[csf('product_name_details')]]['yarn_did']=$row[csf('detarmination_id')];
		}
		$issue_qnty=array();
		$sql_issue=sql_select(" select b.po_breakdown_id,b.color_id, sum(b.quantity) as issue_qnty  from inv_issue_master a,inv_transaction c, order_wise_pro_details b where a.id=c.mst_id and c.prod_id=b.prod_id  and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and a.issue_purpose in(4,9) and b.entry_form=19 $storeCond group by b.po_breakdown_id,b.color_id");
		foreach( $sql_issue as $row_iss )
		{
			$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
		} //var_dump($issue_qnty);

		$booking_qnty=array(); $booking_qnty_chk= array();
		$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty ) as fin_fab_qnty, b.id as dtls_id, c.uom, c.lib_yarn_count_deter_id,b.gsm_weight,b.dia_width from wo_booking_mst a, wo_booking_dtls b , wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
		/*$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty) as fin_fab_qnty, c.lib_yarn_count_deter_id,b.gsm_weight,b.dia_width from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");*/
		foreach( $sql_booking as $row)
		{
			if($booking_qnty_chk[$row[csf('dtls_id')]] == "")
			{
				$booking_qnty_chk[$row[csf('dtls_id')]]=$row[csf('dtls_id')];
				$booking_qnty[$row[csf('uom')]][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]][$row[csf('gsm_weight')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
			}
		}

		/*echo "<pre>";
		print_r($booking_qnty);*/

		unset($sql_booking);

		$iss_return_qnty=array();
		$sql_issue_ret=sql_select("select a.job_no,d.po_breakdown_id as po_id, d.color_id,d.prod_id, (d.quantity) as issue_ret_qnty  from wo_po_details_master a, wo_po_break_down b,order_wise_pro_details d,inv_transaction c where a.job_no=b.job_no_mst and b.id=d.po_breakdown_id and d.trans_id=c.id and  d.trans_id!=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=209  and
			a.company_name in(".$cbo_company_id.")  $buyer_id_cond $search_cond $year_cond $storeCond $shiping_status_cond");
		foreach( $sql_issue_ret as $row )
		{
			$fab_desc_tmp1=explode(",",$product_arr[$row[csf('prod_id')]]);
			$fab_desc_tmp=$fab_desc_tmp1[0];

			$iss_return_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('prod_id')]]['issue_ret_qnty']+=$row[csf('issue_ret_qnty')];
		}
		unset($sql_issue_ret);
		//$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		$fabric_type_arr=return_library_array( "select id, type from lib_yarn_count_determina_mst where fab_nature_id=3 and entry_form=426", "id", "type"  );

		ob_start();
		?>
		<fieldset style="width:1840px;">
			<table cellpadding="0" cellspacing="0" width="1810">
				<tr  class="form_caption" style="border:none;">
					<td width="10%" align="left">All Value Converted to Taka</td>
					<td align="center" width="90%" colspan="15" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td width="10%" align="left"></td>
					<td align="center" width="90%" colspan="15" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td width="10%" align="left"></td>
					<td align="center" width="90%" colspan="15" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="3550" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>

					<tr>
						<th width="30" rowspan="2">SL</th>
						<th width="100" rowspan="2">Company</th>
						<th width="100" rowspan="2">Store Name</th>
						<th width="100" rowspan="2">Buyer</th>
						<th width="60" rowspan="2">Job</th>
						<th width="50" rowspan="2">Year</th>
						<th width="110" rowspan="2">Style</th>

						<th width="60" rowspan="2">Order Status</th>
						<th width="100" rowspan="2">Shiping Status</th>
						<th width="110" rowspan="2">Fin. Fab. Color</th>
						<th width="100" rowspan="2">Body Part</th>
						<th width="100" rowspan="2">Fab. Type</th>
						<th width="220" rowspan="2">Fab. Desc.</th>
						<th width="50" rowspan="2">UOM</th>

						<th width="80" rowspan="2">Req. Qty</th>

						<th width="240" colspan="6">Today Recv.</th>
						<th width="240" title="Rec.+Issue Ret.+Trans. in" colspan="6">Total Received</th>
					
						<th width="80" title="Req.-Totat Rec." rowspan="2">Received Balance</th>
						<th width="240" colspan="6">Today Issue</th>
						<th width="240" title="Issue+Rec. Ret.+Trans. out" colspan="6">Total Issued</th>
						<th width="80" title="Total Rec.- Total Issue" rowspan="2">Stock</th>
						<th width="" title=""  rowspan="2">Stock Value</th>
					</tr>
					<tr>
						<th width="80">Receive</th>
						<th width="80">Receive Value</th>
						<th width="80">Issue Return</th>
						<th width="80">Issue Return Value</th>
						<th width="80">Transfer In</th>
						<th width="80">Transfer In Value</th>

						<th width="80">Receive</th>
						<th width="80">Receive Value</th>
						<th width="80">Issue Return</th>
						<th width="80">Issue Return Value</th>
						<th width="80">Transfer In</th>
						<th width="80">Transfer In Value</th>

						<th width="80">Issue</th>
						<th width="80">Issue Value</th>
						<th width="80">Receive Return</th>
						<th width="80">Receive Return Value</th>
						<th width="80">Transfer Out</th>
						<th width="80">Transfer Out Value</th>
						<th width="80">Issue</th>
						<th width="80">Issue Value</th>
						<th width="80">Receive Return</th>
						<th width="80">Receive Return Value</th>
						<th width="80">Transfer Out</th>
						<th width="80">Transfer Out Value</th>
					</tr>
				</thead>
			</table>
			<div style="width:3570px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="3550" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
					<?
					if($db_type==0) $prod_cond="group_concat(e.prod_id) as prod_id";
					else if($db_type==2) $prod_cond="listagg(cast(e.prod_id as varchar2(4000)),',') within group (order by e.prod_id) as prod_id";

					$sql_query="
					Select
					a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number as po_no,d.color_id,d.prod_id, c.order_rate, $select_fld,e.product_name_details as prod_desc,e.detarmination_id,c.store_id,c.body_part_id , 
					(case when d.entry_form in (17) then d.quantity else 0 end) as receive_qnty,
					(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then d.quantity else 0 end) as finish_fabric_transfer_in, 
					(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then d.quantity else 0 end) as issue_rtn,
					(case when d.entry_form in (17) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,
					(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 and $today_receive_date then d.quantity else 0 end) as today_trans_in,
					(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 and $today_receive_date then d.quantity else 0 end) as today_issue_rtn, 
					(case when d.entry_form in (19) then d.quantity else 0 end) as issue_qnty, (case when d.entry_form in (258) 
					and c.transaction_type=6 and d.trans_type=6 then d.quantity else 0 end) as finish_fabric_transfer_out, 
					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then d.quantity else 0 end) as recv_rtn,
					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 and $today_receive_date then d.quantity else 0 end) as today_recv_rtn, 
					(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 and $today_receive_date then d.quantity else 0 end) as today_trans_out,
					(case when d.entry_form in (19) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty,

					(case when d.entry_form in (17) and c.transaction_type=1 then c.cons_rate else 0 end) *(case when d.entry_form in (17) and c.transaction_type=1 then d.quantity else 0 end) as receive_amount,
					(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then d.quantity else 0 end) 
					*(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then c.cons_rate else 0 end) as transfer_in_amount,
					(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then d.quantity else 0 end)*(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then c.cons_rate else 0 end) as issue_rtn_amount, 
					(case when d.entry_form in (19) and c.transaction_type=2 then c.cons_rate else 0 end)*(case when d.entry_form in (19) and c.transaction_type=2 then d.quantity else 0 end) as issue_amount, 
					
					(case when d.entry_form in (258) 
					and c.transaction_type=6 and d.trans_type=6 then c.cons_rate else 0 end) *(case when d.entry_form in (258) 
					and c.transaction_type=6 and d.trans_type=6 then d.quantity else 0 end) as transfer_out_amount, 

					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then d.quantity else 0 end)*(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then c.cons_rate else 0 end) as recv_rtn_amount ,
					(case when d.entry_form in (17) and $today_receive_date and c.transaction_type=1 then d.quantity else 0 end)*(case when d.entry_form in (17) and $today_receive_date and c.transaction_type=1 then c.cons_rate else 0 end) as today_receive_amount, 

					(case when d.entry_form in (258) and $today_receive_date and c.transaction_type=5 and d.trans_type=5 then d.quantity else 0 end)*
					(case when d.entry_form in (258) and $today_receive_date and c.transaction_type=5 and d.trans_type=5 then c.cons_rate else 0 end) as today_transfer_in_amount,


					(case when d.entry_form in (209) and $today_receive_date and c.transaction_type=4 and d.trans_type=4 then d.quantity else 0 end)*(case when d.entry_form in (209) and $today_receive_date and c.transaction_type=4 and d.trans_type=4 then c.cons_rate else 0 end) as today_issue_rtn_amount, 
					(case when d.entry_form in (19) and $today_receive_date and c.transaction_type=2 then c.cons_rate else 0 end) *(case when d.entry_form in (19) and $today_receive_date and c.transaction_type=2 then d.quantity else 0 end) as today_issue_amount, 

					(case when d.entry_form in (258) and $today_receive_date and c.transaction_type=6 and d.trans_type=6 then c.cons_rate else 0 end)*
					(case when d.entry_form in (258) and $today_receive_date and c.transaction_type=6 and d.trans_type=6 then d.quantity else 0 end) as today_transfer_out_amount, 


					(case when d.entry_form in (202) and $today_receive_date and c.transaction_type=3 and d.trans_type=3 then d.quantity else 0 end)*(case when d.entry_form in (202) and $today_receive_date and c.transaction_type=3 and d.trans_type=3 then c.cons_rate else 0 end) as today_recv_rtn_amount 

					from
					wo_po_details_master a,
					wo_po_break_down b,
					inv_transaction c,
					order_wise_pro_details d,product_details_master e 
					where
					a.job_no=b.job_no_mst 
					and d.po_breakdown_id=b.id 
					and d.trans_id=c.id and d.prod_id=e.id and c.prod_id=e.id 
					and d.trans_id!=0
					and d.entry_form in (17,19,202,209,258) 
					and c.item_category=3  
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
					and a.company_name in(".$cbo_company_id.") $receive_date $buyer_id_cond $search_cond $year_cond $storeCond $shiping_status_cond $uom_cond order by a.job_no,d.color_id";


					//echo $sql_query; die;
					$style_wise_arr=array();
					$nameArray=sql_select($sql_query);
					foreach ($nameArray as $row)
					{
						if($uomFromProductArr[$row[csf('prod_id')]] == "") continue;
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no']=$row[csf('job_no')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['job_no_pre']=$row[csf('job_no_prefix_num')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['year']=$row[csf('year')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['buyer_name']=$row[csf('buyer_name')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['body_part_id']=$row[csf('body_part_id')];
						
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['companyId']=$row[csf('company_name')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['store_id']=$row[csf('store_id')];

						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['style_ref_no']=$row[csf('style_ref_no')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_id'].=$row[csf('po_id')].',';
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['po_no'].=$row[csf('po_no')].',';
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['detarmination_id']=$row[csf('detarmination_id')];

						

						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['receive_qnty']+=$row[csf('receive_qnty')];
						
						
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_qnty']+=$row[csf('issue_qnty')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_rtn']+=$row[csf('issue_rtn')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['recv_rtn']+=$row[csf('recv_rtn')];
											
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_qnty']+=$row[csf('today_issue_qnty')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_trans_in']+=$row[csf('today_trans_in')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_trans_out']+=$row[csf('today_trans_out')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_rtn']+=$row[csf('today_issue_rtn')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_recv_rtn']+=$row[csf('today_recv_rtn')];

						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['finish_fabric_transfer_in']+=$row[csf('finish_fabric_transfer_in')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['finish_fabric_transfer_out']+=$row[csf('finish_fabric_transfer_out')];


						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['receive_amount']+=$row[csf('receive_amount')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['transfer_in_amount']+=$row[csf('transfer_in_amount')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_rtn_amount']+=$row[csf('issue_rtn_amount')];

						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['issue_amount']+=$row[csf('issue_amount')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['transfer_out_amount']+=$row[csf('transfer_out_amount')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['recv_rtn_amount']+=$row[csf('recv_rtn_amount')];


						//Today amount arr
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_receive_amount']+=$row[csf('today_receive_amount')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_transfer_in_amount']+=$row[csf('today_transfer_in_amount')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_rtn_amount']+=$row[csf('today_issue_rtn_amount')];

						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_issue_amount']+=$row[csf('today_issue_amount')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_transfer_out_amount']+=$row[csf('today_transfer_out_amount')];
						$style_wise_arr[$row[csf('company_name')]][$row[csf('store_id')]][$uomFromProductArr[$row[csf('prod_id')]]][$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_recv_rtn_amount']+=$row[csf('today_recv_rtn_amount')];
												
						
					}
					//echo "<pre>";
					//print_r($style_wise_arr); die;

					$i=1;
					foreach ($style_wise_arr as $compaId => $company_data)
					{
						foreach ($company_data as $storeId => $store_data)
						{
							foreach ($store_data as $cons_uom => $cons_uom_data)
							{
								$sub_req_qty=0;

								$sub_today_recv = 0;
								$sub_today_recv_rtn = 0;
								$sub_today_issue = 0;
								$sub_today_issue_rtn = 0;
								$sub_today_trans_in = 0;
								$sub_today_trans_out = 0;

								$sub_rec_qty = 0;
								$sub_issue_qty = 0;
								$sub_rec_tranfIn = 0;
								$sub_rec_issueRtn = 0;
								$sub_iss_trnsOut = 0;
								$sub_iss_recvRtn = 0;

								$sub_rec_bal = 0;
								$sub_stock=0;

								$sub_today_recv_amnt = 0;
								$sub_today_recv_rtn_amnt = 0;
								$sub_today_issue_amnt = 0;
								$sub_today_issue_rtn_amnt = 0;
								$sub_today_trans_in_amnt = 0;
								$sub_today_trans_out_amnt = 0;

								$sub_rec_qty_amnt = 0;
								$sub_issue_qty_amnt = 0;
								$sub_rec_tranfIn_amnt = 0;
								$sub_rec_issueRtn_amnt = 0;
								$sub_iss_trnsOut_amnt = 0;
								$sub_iss_recvRtn_amnt = 0;

								$sub_stock_amnt=0;

								foreach ($cons_uom_data  as $job_key=>$job_val)
								{
									foreach ($job_val  as $color_key=>$color_val)
									{
										foreach ($color_val  as $desc_key=>$val)
										{
											//if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

											$dzn_qnty=0;
											
											if($costing_per_id_library[$job_key]==1) $dzn_qnty=12;
											else if($costing_per_id_library[$job_key]==3) $dzn_qnty=12*2;
											else if($costing_per_id_library[$job_key]==4) $dzn_qnty=12*3;
											else if($costing_per_id_library[$job_key]==5) $dzn_qnty=12*4;
											else $dzn_qnty=1;

											$color_id=$row[csf("color_id")];
											//$fab_desc_type=$product_arr[$desc_key];
											$fab_desc_type=$desc_key;
											$po_nos=rtrim($val['po_no'],',');
											$po_nos=implode(",",array_unique(explode(",",$po_nos)));
											$poids=rtrim($val['po_id'],',');

											$po_ids=array_unique(explode(",",$poids));
											$book_qty=0;$issue_ret_qnty=0;$receive_ret_qnty=0;
											foreach($po_ids as $po_id)
											{
												//echo $job_key."=".$po_id."=".$color_key."=".$product_array[$desc_key]['yarn_did']."=".$product_array[$desc_key]['dia_width']."=".$product_array[$desc_key]['fin_fab_qnty']."<br>";
												$book_qty+=$booking_qnty[$cons_uom][$job_key][$po_id][$color_key][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']][$product_array[$desc_key]['weight']]['fin_fab_qnty'];
												//echo $job_key.'ii'.$po_id;
												$issue_ret_qnty+=$iss_return_qnty[$job_key][$po_id][$color_key][$desc_key]['issue_ret_qnty'];
												//echo $job_key."=".$po_id."=".$color_key."=".$desc_key."<br/>";
												//echo $issue_ret_qnty.",";
												$receive_ret_qnty+=$rec_return_qnty[$job_key][$po_id][$color_key][$desc_key]['rec_ret_qnty'];
											}

											//echo $job_key.'ii';
											$po_ids=implode(",",$po_ids);
											$today_recv=$val[("today_receive_qnty")];
											$today_issue=$val[("today_issue_qnty")];
											$today_recv_rtn=$val[("today_recv_rtn")];
											$today_issue_rtn=$val[("today_issue_rtn")];
											$today_trans_in=$val[("today_trans_in")];
											$today_trans_out=$val[("today_trans_out")];

											$rec_qty=$val[("receive_qnty")];
											$rec_tranfIn=$val[("finish_fabric_transfer_in")];
											$rec_issueRtn=$val[("issue_rtn")];
			
											$iss_qty=$val[("issue_qnty")];
											$iss_trnsOut=$val[("finish_fabric_transfer_out")];
											$iss_recvRtn=$val[("recv_rtn")];
			
											$total_recQnty=$rec_qty+$rec_tranfIn+$rec_issueRtn;
											$total_issQnty=$iss_qty+$iss_trnsOut+$iss_recvRtn;

											$rec_bal=$book_qty-$total_recQnty-$rec_issueRtn; 
											
											//$receive_amount=number_format($val[("receive_amount")],2,'.','');
											$rec_qty_cal=($total_recQnty);
											$iss_qty_cal=($total_issQnty);
											$stock=$rec_qty_cal-$iss_qty_cal;
											
											//rate calculating
											//$rate = $receive_amount/$rec_qty;
											//$issue_amount = number_format(($val[("issue_qnty")]*$rate),2,'.','');
											//$stock_amount = number_format(($stock*$rate),2,'.','');

											/*$recv_amount_today 				=$val[("today_receive_amount")]/$today_recv;
											$recv_issue_rtn_amount_today 	=$val[("today_issue_rtn_amount")]/$today_issue_rtn;
											$recv_trans_in_amount_today 	=$val[("today_transfer_in_amount")]/$today_trans_in;
											$issue_amount_today 			=$val[("today_issue_amount")]/$today_issue;
											$issue_rev_rtn_amount_today 	=$val[("today_recv_rtn_amount")]/$today_recv_rtn;
											$issue_trans_out_amount_today 	=$val[("today_transfer_out_amount")]/$today_trans_out;

											$recv_amount_all 				=$val[("receive_amount")]/$rec_qty;
											$recv_issue_rtn_amount_all 		=$val[("issue_rtn_amount")]/$rec_issueRtn;
											$recv_trans_in_amount_all 		=$val[("transfer_in_amount")]/$rec_tranfIn;
											$issue_amount_all 				=$val[("issue_amount")]/$iss_qty;
											$issue_rev_rtn_amount_all 		=$val[("recv_rtn_amount")]/$iss_recvRtn;
											$issue_trans_out_amount_all 	=$val[("transfer_out_amount")]/$iss_trnsOut;*/

											$recv_amount_today 				=$val[("today_receive_amount")];
											$recv_issue_rtn_amount_today 	=$val[("today_issue_rtn_amount")];
											$recv_trans_in_amount_today 	=$val[("today_transfer_in_amount")];
											$issue_amount_today 			=$val[("today_issue_amount")];
											$issue_rev_rtn_amount_today 	=$val[("today_recv_rtn_amount")];
											$issue_trans_out_amount_today 	=$val[("today_transfer_out_amount")];

											$recv_amount_all 				=$val[("receive_amount")];
											$recv_issue_rtn_amount_all 		=$val[("issue_rtn_amount")];
											$recv_trans_in_amount_all 		=$val[("transfer_in_amount")];
											$issue_amount_all 				=$val[("issue_amount")];
											$issue_rev_rtn_amount_all 		=$val[("recv_rtn_amount")];
											$issue_trans_out_amount_all 	=$val[("transfer_out_amount")];



											if(is_nan($recv_amount_today)){$recv_amount_today=0;}
											if(is_nan($recv_issue_rtn_amount_today)){$recv_issue_rtn_amount_today=0;}
											if(is_nan($recv_trans_in_amount_today)){$recv_trans_in_amount_today=0;}
											if(is_nan($issue_amount_today)){$issue_amount_today=0;}
											if(is_nan($issue_rev_rtn_amount_today)){$issue_rev_rtn_amount_today=0;}
											if(is_nan($issue_trans_out_amount_today)){$issue_trans_out_amount_today=0;}
											if(is_nan($recv_amount_all)){$recv_amount_all=0;}
											if(is_nan($recv_issue_rtn_amount_all)){$recv_issue_rtn_amount_all=0;}
											if(is_nan($recv_trans_in_amount_all)){$recv_trans_in_amount_all=0;}
											if(is_nan($issue_amount_all)){$issue_amount_all=0;}
											if(is_nan($issue_rev_rtn_amount_all)){$issue_rev_rtn_amount_all=0;}
											if(is_nan($issue_trans_out_amount_all)){$issue_trans_out_amount_all=0;}

											$rec_amount_total=($val[("receive_amount")]+$val[("transfer_in_amount")]+$val[("issue_rtn_amount")]);
											$rec_avg_rate_total=$rec_amount_total/$total_recQnty;

											$iss_amount_total=($val[("issue_amount")]+$val[("transfer_out_amount")]+$val[("recv_rtn_amount")]);
											$issue_avg_rate_total=$iss_amount_total/$total_issQnty;

											$StockValue=$rec_amount_total-$iss_amount_total;

											if($cbo_value_range_by==2 &&  number_format($stock,2,'.','')>0.00)
											{
												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
													<td width="30"><? echo $i; ?></td>
													<td width="100"><p><? echo $company_lib_arr[$val[("companyId")]]; ?></p></td>
													<td width="100"><p><? echo $store_lib_arr[$val[("store_id")]]; ?></p></td>
													<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
													<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
													<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
													<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>
													<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
													<td width="100" title="<? echo $po_ids;?>"><p>
														<? 
															$po_ids_exp=explode(",", $po_ids);
															$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
															foreach ($po_ids_exp as $row) {
																if($po_id_shipingStatus_arr[$row]==3)
																{
																	$full_shipSts_countx++;
																}
																else if($po_id_shipingStatus_arr[$row]==2){
																	$partial_shipSts_countx++;
																}
																else if($po_id_shipingStatus_arr[$row]==1){
																	$panding_shipSts_countx++;
																}
																$poId_countx++;
															}
															if($full_shipSts_countx==$poId_countx){
																$ShipingStatus="Full Delivery/Closed";
															}
															else if ($partial_shipSts_countx==$poId_countx) {
																$ShipingStatus="Partial Delivery";
															}
															else if ($panding_shipSts_countx==$poId_countx) {
																$ShipingStatus="Full Pending";
															}
															else
															{
																$ShipingStatus="Partial Delivery";
															}
															echo $ShipingStatus;
														?></p></td>
													<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
													<td width="100"><p><? echo $body_part_lib_arr[$val[("body_part_id")]]; ?></p></td>
													<td width="100" align="center"><p><? echo $fabric_type_arr[$val[("detarmination_id")]]; ?></p></td>
													<td width="220" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $fab_desc_type; ?></p></td>
													<td width="50"><p><? echo $unit_of_measurement[$cons_uom]; ?></p></td>
													<td width="80" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>
																	

													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_receive_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</a></p></td>
													<td width="80" align="right"><p><? echo number_format($recv_amount_today,2,'.',''); ?></p></td>
													<td width="80" align="right"><p><a href='#report_details'  onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_issue_rtn_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_issue_rtn,2,'.',''); ?>&nbsp;</a></p></td>
													<td width="80" align="right"><p><? echo number_format($recv_issue_rtn_amount_today,2,'.',''); ?></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_transf_in_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_trans_in,2,'.',''); ?>&nbsp;</a></p></td>
													<td width="80" align="right"><p><? echo number_format($recv_trans_in_amount_today,2,'.',''); ?></p></td>

													
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_only_receive_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><? echo number_format($recv_amount_all,2,'.',''); ?></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_only_issue_rtn_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($rec_issueRtn,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><? echo number_format($recv_issue_rtn_amount_all,2,'.',''); ?></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_only_transf_in_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($rec_tranfIn,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><? echo number_format($recv_trans_in_amount_all,2,'.',''); ?></p></td>


													<td width="80" align="right"><p><? echo number_format($rec_bal,2,'.',''); ?></p></td>

													
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_issue_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_amount_today,2,'.',''); ?></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_recv_rtn_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_recv_rtn,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_rev_rtn_amount_today,2,'.',''); ?></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_transf_out_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_trans_out,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_trans_out_amount_today,2,'.',''); ?></p></td>


													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_only_issue_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_amount_all,2,'.',''); ?></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_only_recv_rtn_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($iss_recvRtn,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_rev_rtn_amount_all,2,'.',''); ?></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_only_transf_out_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($iss_trnsOut,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_trans_out_amount_all,2,'.',''); ?></p></td>

													
													<td width="80" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 6; ?>','woven_knit_stock_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($stock,2,'.',''); ?></a></p></td>
													<td width="" align="right"><p><? echo number_format($StockValue,2,'.',''); ?></p></td>
									
												</tr>
													<?
													$i++;

													$sub_req_qty+=$book_qty;
													$sub_rec_bal+=$rec_bal;
													$sub_stock+=$stock;
													
													//$sub_receive_amount+=$receive_amount;
													//$sub_issue_amount+=$issue_amount;
													//$sub_stock_value+=$stock_amount;

													$sub_rec_qty+=$rec_qty;
													$sub_issue_qty+=$iss_qty;

													$sub_rec_tranfIn+=$rec_tranfIn;
													$sub_rec_issueRtn+=$rec_issueRtn;
													$sub_iss_trnsOut+=$iss_trnsOut;
													$sub_iss_recvRtn+=$iss_recvRtn;

													$sub_today_recv +=$today_recv;
													$sub_today_recv_rtn +=$today_recv_rtn;
													$sub_today_issue+=$today_issue;
													$sub_today_issue_rtn +=$today_issue_rtn;
													$sub_today_trans_in +=$today_trans_in;
													$sub_today_trans_out +=$today_trans_out;

													$total_req_qty+=$book_qty;
													$total_rec_bal+=$rec_bal;
													
													$total_stock+=$stock;
													$total_possible_cut_pcs+=$possible_cut_pcs;
													$total_actual_cut_qty+=$actual_qty;
													$total_rec_return_qnty+=$receive_ret_qnty;
													$total_issue_ret_qnty+=$issue_ret_qnty;

													$total_rec_qty+=$rec_qty;
													$total_issue_qty+=$iss_qty;
													$total_rec_tranfIn+=$rec_tranfIn;
													$total_rec_issueRtn+=$rec_issueRtn;
													$total_iss_trnsOut+=$iss_trnsOut;
													$total_iss_recvRtn+=$iss_recvRtn;

													$total_today_recv+=$today_recv;
													$total_today_issue_rtn+=$today_issue_rtn;
													$total_today_trans_in+=$today_trans_in;

													$total_today_issue+=$today_issue;
													$total_today_recv_rtn+=$today_recv_rtn;
													$total_today_trans_out+=$today_trans_out;						

													$sub_today_recv_amnt+=$recv_amount_today;
													$sub_today_issue_rtn_amnt+=$recv_issue_rtn_amount_today;
													$sub_today_trans_in_amnt+=$recv_trans_in_amount_today;
													$sub_today_issue_amnt+=$issue_amount_today;
													$sub_today_recv_rtn_amnt+=$issue_rev_rtn_amount_today;
													$sub_today_trans_out_amnt+=$issue_trans_out_amount_today;
					
													$sub_rec_qty_amnt+=$recv_amount_all;
													$sub_rec_issueRtn_amnt+=$recv_issue_rtn_amount_all;
													$sub_rec_tranfIn_amnt+=$recv_trans_in_amount_all;
													$sub_issue_qty_amnt+=$issue_amount_all;
													$sub_iss_recvRtn_amnt+=$issue_rev_rtn_amount_all;
													$sub_iss_trnsOut_amnt+=$issue_trans_out_amount_all;
													
													$sub_stock_amnt+=$StockValue; 

													$total_rec_qty_amnt+=$recv_amount_all;
													$total_rec_issueRtn_amnt+=$recv_issue_rtn_amount_all;
													$total_rec_tranfIn_amnt+=$recv_trans_in_amount_all;
													
													$total_issue_qty_amnt+=$issue_amount_all;
													$total_iss_recvRtn_amnt+=$issue_rev_rtn_amount_all;
													$total_iss_trnsOut_amnt+=$issue_trans_out_amount_all;
													
													$total_today_recv_amnt+=$recv_amount_today;
													$total_today_issue_rtn_amnt+=$recv_issue_rtn_amount_today;
													$total_today_trans_in_amnt+=$recv_trans_in_amount_today;

													$total_today_issue_amnt+=$issue_amount_today;
													$total_today_recv_rtn_amnt+=$issue_rev_rtn_amount_today;
													$total_today_trans_out_amnt+=$issue_trans_out_amount_today;	

													$total_stock_amnt+=$StockValue; 

													//$total_receive_amount+=$receive_amount;
													//$total_issue_amount+=$issue_amount;
													//$total_stock_value+=$stock_amount;
											}
											else if($cbo_value_range_by==1 || $cbo_value_range_by==0)
											{
												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
													<td width="30"><? echo $i; ?></td>
													<td width="100"><p><? echo $company_lib_arr[$val[("companyId")]]; ?></p></td>
													<td width="100"><p><? echo $store_lib_arr[$val[("store_id")]]; ?></p></td>
													<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
													<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
													<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
													<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>
													<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
													<td width="100" title="<? echo $po_ids;?>"><p>
														<? 
															$po_ids_exp=explode(",", $po_ids);
															$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
															foreach ($po_ids_exp as $row) {
																if($po_id_shipingStatus_arr[$row]==3)
																{
																	$full_shipSts_countx++;
																}
																else if($po_id_shipingStatus_arr[$row]==2){
																	$partial_shipSts_countx++;
																}
																else if($po_id_shipingStatus_arr[$row]==1){
																	$panding_shipSts_countx++;
																}
																$poId_countx++;
															}
															if($full_shipSts_countx==$poId_countx){
																$ShipingStatus="Full Delivery/Closed";
															}
															else if ($partial_shipSts_countx==$poId_countx) {
																$ShipingStatus="Partial Delivery";
															}
															else if ($panding_shipSts_countx==$poId_countx) {
																$ShipingStatus="Full Pending";
															}
															else
															{
																$ShipingStatus="Partial Delivery";
															}
															echo $ShipingStatus;
														?></p></td>
													<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
													<td width="100"><p><? echo $body_part_lib_arr[$val[("body_part_id")]]; ?></p></td>
													<td width="100" align="center"><p><? echo $fabric_type_arr[$val[("detarmination_id")]]; ?></p></td>
													<td width="220" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $fab_desc_type; ?></p></td>
													<td width="50"><p><? echo $unit_of_measurement[$cons_uom]; ?></p></td>
													<td width="80" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>
																	

													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_receive_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</a></p></td>
													<td width="80" align="right"><p><? echo number_format($recv_amount_today,2,'.',''); ?></p></td>
													<td width="80" align="right"><p><a href='#report_details'  onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_issue_rtn_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_issue_rtn,2,'.',''); ?>&nbsp;</a></p></td>
													<td width="80" align="right"><p><? echo number_format($recv_issue_rtn_amount_today,2,'.',''); ?></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_transf_in_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_trans_in,2,'.',''); ?>&nbsp;</a></p></td>
													<td width="80" align="right"><p><? echo number_format($recv_trans_in_amount_today,2,'.',''); ?></p></td>

													
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_only_receive_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><? echo number_format($recv_amount_all,2,'.',''); ?></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_only_issue_rtn_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($rec_issueRtn,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><? echo number_format($recv_issue_rtn_amount_all,2,'.',''); ?></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_only_transf_in_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($rec_tranfIn,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><? echo number_format($recv_trans_in_amount_all,2,'.',''); ?></p></td>


													<td width="80" align="right"><p><? echo number_format($rec_bal,2,'.',''); ?></p></td>

													
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_issue_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_amount_today,2,'.',''); ?></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_recv_rtn_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_recv_rtn,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_rev_rtn_amount_today,2,'.',''); ?></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_only_today_transf_out_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($today_trans_out,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_trans_out_amount_today,2,'.',''); ?></p></td>


													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_only_issue_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_amount_all,2,'.',''); ?></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_only_recv_rtn_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($iss_recvRtn,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_rev_rtn_amount_all,2,'.',''); ?></p></td>
													<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_only_transf_out_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($iss_trnsOut,2,'.',''); ?></a></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_trans_out_amount_all,2,'.',''); ?></p></td>

													
													<td width="80" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $val[("companyId")]; ?>','<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 6; ?>','woven_knit_stock_popup','','','','','<? echo $val[("body_part_id")]; ?>','<? echo $val[("store_id")]; ?>');"><? echo number_format($stock,2,'.',''); ?></a></p></td>
													<td width="" align="right"><p><? echo number_format($StockValue,2,'.',''); ?></p></td>
									
												</tr>
													<?
													$i++;

													$sub_req_qty+=$book_qty;
													$sub_rec_bal+=$rec_bal;
													$sub_stock+=$stock;
													
													//$sub_receive_amount+=$receive_amount;
													//$sub_issue_amount+=$issue_amount;
													//$sub_stock_value+=$stock_amount;

													$sub_rec_qty+=$rec_qty;
													$sub_issue_qty+=$iss_qty;

													$sub_rec_tranfIn+=$rec_tranfIn;
													$sub_rec_issueRtn+=$rec_issueRtn;
													$sub_iss_trnsOut+=$iss_trnsOut;
													$sub_iss_recvRtn+=$iss_recvRtn;

													$sub_today_recv +=$today_recv;
													$sub_today_recv_rtn +=$today_recv_rtn;
													$sub_today_issue+=$today_issue;
													$sub_today_issue_rtn +=$today_issue_rtn;
													$sub_today_trans_in +=$today_trans_in;
													$sub_today_trans_out +=$today_trans_out;

													$total_req_qty+=$book_qty;
													$total_rec_bal+=$rec_bal;
													
													$total_stock+=$stock;
													$total_possible_cut_pcs+=$possible_cut_pcs;
													$total_actual_cut_qty+=$actual_qty;
													$total_rec_return_qnty+=$receive_ret_qnty;
													$total_issue_ret_qnty+=$issue_ret_qnty;

													$total_rec_qty+=$rec_qty;
													$total_issue_qty+=$iss_qty;
													$total_rec_tranfIn+=$rec_tranfIn;
													$total_rec_issueRtn+=$rec_issueRtn;
													$total_iss_trnsOut+=$iss_trnsOut;
													$total_iss_recvRtn+=$iss_recvRtn;

													$total_today_recv+=$today_recv;
													$total_today_issue_rtn+=$today_issue_rtn;
													$total_today_trans_in+=$today_trans_in;

													$total_today_issue+=$today_issue;
													$total_today_recv_rtn+=$today_recv_rtn;
													$total_today_trans_out+=$today_trans_out;						

													$sub_today_recv_amnt+=$recv_amount_today;
													$sub_today_issue_rtn_amnt+=$recv_issue_rtn_amount_today;
													$sub_today_trans_in_amnt+=$recv_trans_in_amount_today;
													$sub_today_issue_amnt+=$issue_amount_today;
													$sub_today_recv_rtn_amnt+=$issue_rev_rtn_amount_today;
													$sub_today_trans_out_amnt+=$issue_trans_out_amount_today;
					
													$sub_rec_qty_amnt+=$recv_amount_all;
													$sub_rec_issueRtn_amnt+=$recv_issue_rtn_amount_all;
													$sub_rec_tranfIn_amnt+=$recv_trans_in_amount_all;
													$sub_issue_qty_amnt+=$issue_amount_all;
													$sub_iss_recvRtn_amnt+=$issue_rev_rtn_amount_all;
													$sub_iss_trnsOut_amnt+=$issue_trans_out_amount_all;
													
													$sub_stock_amnt+=$StockValue; 

													$total_rec_qty_amnt+=$recv_amount_all;
													$total_rec_issueRtn_amnt+=$recv_issue_rtn_amount_all;
													$total_rec_tranfIn_amnt+=$recv_trans_in_amount_all;
													
													$total_issue_qty_amnt+=$issue_amount_all;
													$total_iss_recvRtn_amnt+=$issue_rev_rtn_amount_all;
													$total_iss_trnsOut_amnt+=$issue_trans_out_amount_all;
													
													$total_today_recv_amnt+=$recv_amount_today;
													$total_today_issue_rtn_amnt+=$recv_issue_rtn_amount_today;
													$total_today_trans_in_amnt+=$recv_trans_in_amount_today;

													$total_today_issue_amnt+=$issue_amount_today;
													$total_today_recv_rtn_amnt+=$issue_rev_rtn_amount_today;
													$total_today_trans_out_amnt+=$issue_trans_out_amount_today;	

													$total_stock_amnt+=$StockValue; 
													//$total_receive_amount+=$receive_amount;
													//$total_issue_amount+=$issue_amount;
													//$total_stock_value+=$stock_amount;
											}

										}
									}
								}
									?>
									<tr style="font-weight:bold;background-color:#e0e0e0;">
										<td colspan="14" align="right">Store and UOM Wise Total</td>
										<td align="right"><? echo number_format($sub_req_qty,2,'.','') ?></td>

										<td align="right"><? echo number_format($sub_today_recv,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_today_recv_amnt,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_today_issue_rtn,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_today_issue_rtn_amnt,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_today_trans_in,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_today_trans_in_amnt,2,'.','') ?></td>

										<td align="right"><? echo number_format($sub_rec_qty,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_rec_qty_amnt,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_rec_issueRtn,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_rec_issueRtn_amnt,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_rec_tranfIn,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_rec_tranfIn_amnt,2,'.','') ?></td>

										<td align="right"><? echo number_format($sub_rec_bal,2,'.','') ?></td>

										<td align="right"><? echo number_format($sub_today_issue,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_today_issue_amnt,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_today_recv_rtn,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_today_recv_rtn_amnt,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_today_trans_out,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_today_trans_out_amnt,2,'.','') ?></td>
										
										<td align="right"><? echo number_format($sub_issue_qty,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_issue_qty_amnt,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_iss_recvRtn,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_iss_recvRtn_amnt,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_iss_trnsOut,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_iss_trnsOut_amnt,2,'.','') ?></td>
	
										<td align="right"><? echo number_format($sub_stock,2,'.','') ?></td>
										<td align="right"><? echo number_format($sub_stock_amnt,2,'.','') ?></td>
										
									</tr>
									<?
							}
						}
					}
						?>
					</table>
                	<table width="3550" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
                    <tfoot>
                        <th width="30"></th>
						<th width="100"></th>
						<th width="100"></th>
                        <th width="100"></th>
                        <th width="60"></th>
                        <th width="50"></th>
                        <th width="110">&nbsp;</th>

                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
						<th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="220">&nbsp;</th>
                        <th width="50">Total</th>
                        <th width="80" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
                        
						<th width="80" align="right" id="value_total_today_rec_qty"><? //echo number_format($total_today_recv,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_recv_amnt,2,'.',''); ?></th>
                        <th width="80" align="right" id="value_total_today_rec_qty"><? //echo number_format($total_today_issue_rtn,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_issue_rtn_amnt,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_rec_qty"><? //echo number_format($total_today_trans_in,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_trans_in_amnt,2,'.',''); ?></th>


						<th width="80" align="right" id="value_total_rec_qty"><? //echo number_format($total_rec_qty,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_rec_qty_amnt,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_rec_qty"><? //echo number_format($total_rec_issueRtn,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_rec_issueRtn_amnt,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_rec_qty"><? //echo number_format($total_rec_tranfIn,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_rec_tranfIn_amnt,2,'.',''); ?></th>
                     
                        <th width="80" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>

                        <th width="80"  id="value_recv_today_issue_qty"><? //echo number_format($total_today_issue,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_issue_amnt,2,'.',''); ?></th>
						<th width="80"  id="value_recv_today_issue_qty"><? //echo number_format($total_today_recv_rtn,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_recv_rtn_amnt,2,'.',''); ?></th>
						<th width="80"  id="value_recv_today_issue_qty"><? //echo number_format($total_today_trans_out,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_trans_out_amnt,2,'.',''); ?></th>

                        <th width="80" align="right" id="value_total_issue_qty"><? //echo number_format($total_issue_qty,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_issue_qty_amnt,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_issue_qty"><? //echo number_format($total_iss_recvRtn,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_iss_recvRtn_amnt,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_issue_qty"><? //echo number_format($total_iss_trnsOut,2,'.',''); ?></th>
						<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_iss_trnsOut_amnt,2,'.',''); ?></th>

                        <th width="80" align="right" id="value_total_stock"><? //echo number_format($total_stock,2,'.',''); ?></th>
						<th width="" align="right" id="value_total_stock"><? echo number_format($total_stock_amnt,2,'.',''); ?></th>
                        
                    </tfoot>
                </table>
            </div>
        </fieldset>
        <?
    }

    $html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$report_type";
	exit();
}
if($action=="open_order_exfactory")
{
	echo load_html_head_contents("Ex-factory Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:480px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th width="50">Sl</th>
						<th width="120">Order No</th>
						<th width="80">Ex-factory Date</th>
						<th width="100">Ex-factory Qty</th>
						<th>Order Status</th>
					</tr>
				</thead>
				<tbody>
					<?

					$sql="select a.id as order_id, a.po_number, a.shiping_status, b.ex_factory_date, b.ex_factory_qnty
					from wo_po_break_down a, pro_ex_factory_mst b
					where a.id=b.po_break_down_id and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($po_id)";
				//echo $sql;

					$dtlsArray=sql_select($sql);
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="50" align="center"><p><? echo $i; ?></p></td>
							<td width="120"><? echo $row[csf('po_number')]; ?></td>
							<td width="80" align="center"><p><? if($row[csf('ex_factory_date')]!="" &&  $row[csf('ex_factory_date')]!="0000-00-00") echo change_date_format($row[csf('ex_factory_date')]); ?></p></td>
							<td align="right" width="100"><? echo number_format($row[csf('ex_factory_qnty')],2); ?></td>
							<td><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
						</tr>
						<?
						$tot_exfact_qty+=$row[csf('ex_factory_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<th width="50">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="80">Total</th>
						<th width="100"><? echo number_format($tot_exfact_qty,2); ?></th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="woven_only_receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1195px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1175" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="18">Receive Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="50">Rate</th>
						<th width="80">Amount</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th>Collar/Cuff Pcs</th>
					</tr>
				</thead>
				<tbody>
					<?
					$gsm_arr=return_library_array( "select id, gsm_weight from lib_yarn_count_determina_mst", "id", "gsm_weight");
					$prodData=sql_select("select id, item_description, unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$gsm_arr[$row[csf('detarmination_id')]];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$book_sql="select a.booking_no, b.grey_fab_qnty from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id)";
					$dtlsbook=sql_select($book_sql);
					$booking_arr=array();
					foreach($dtlsbook as $row)
					{
						$booking_arr[$row[csf('booking_no')]]['grey_qty']+=$row[csf('grey_fab_qnty')];
					}

					$i=1;
					
					$mrr_sql="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, b.cons_rate, sum(c.quantity) as quantity
				from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
				where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and a.entry_form in (17) and c.entry_form in (17)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' and b.store_id='$store_id' group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no, b.cons_rate";
					
					
					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						if($row[csf('knitting_source')]==1) $knitting_company=$company_arr[$row[csf('knitting_company')]];
						else $knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];

						$booking_qty=$booking_arr[$row[csf('booking_no')]]['grey_qty'];
						$process_loss=($row[csf('quantity')]/$booking_qty)*100;

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[csf('cons_rate')],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format(($row[csf('quantity')]*$row[csf('cons_rate')]),2); ?></p></td>
							<td width="200" ><p><? echo trim($description," , "); ?></p></td>
							<td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $dia; ?></p></td>
							<td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_amount+=number_format(($row[csf('quantity')]*$row[csf('cons_rate')]),2,'.','');
						$tot_booking_qty+=$booking_qty;
						$tot_reject_qty+=$row[csf('returnable_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="4" align="right">Total</td>
						<td align="right"><? //echo number_format($tot_booking_qty,2); ?> </td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
						<td align="right"></td>
						<td align="right"><? echo number_format($tot_amount,2); ?> </td>
						<td colspan="2"> </td>
						<td align="right">&nbsp;<? //echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;<? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table> 
         </div>
     </fieldset>
     <?
     $html=ob_get_contents();
     ob_flush();

     foreach (glob(""."*.xls") as $filename)
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
     	$(document).ready(function(e) {
     		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
     	});

     </script>
     <?
     exit();
}
if($action=="woven_only_issue_rtn_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1195px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			
		
             <table border="1" class="rpt_table" rules="all" width="1175" cellpadding="0" cellspacing="0" align="center">
             	<thead>
             		<tr>
             			<th colspan="18">Issue Return Details</th>
             		</tr>
             		<tr>
                        <th width="30">Sl</th>
                        <th width="110">System ID</th>
                        <th width="70">Return Date</th>
                        <!-- <th width="80">Dyeing Source</th>
                        <th width="110">Dyeing Company</th> -->
                        <th width="100">Challan No</th>
                        <th width="80">Color</th>
                        <!-- <th width="80">Batch No</th>
                        <th width="60">Rack No</th>
                        <th width="80">Grey Qty.</th> -->
                        <th width="80">Issue Return Qty.</th>
                        <th width="50">Rate</th>
                        <th width="80">Amount</th>
                        <!-- <th width="70">Process Loss Qty.</th>
                        <th width="60">QC ID</th>
                        <th width="80">QC Name</th> -->
                        <th width="200">Fabric Des.</th>
                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                        <th>Collar/Cuff Pcs</th>
                    </tr>
                </thead>
                <tbody>
                	<?
                	$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
                	$prod_array=array();
                	foreach($prodData as $row)
                	{
                		$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
                		$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
                		$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
                	}

                	$book_sql="select a.booking_no, b.grey_fab_qnty from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id)";
                	$dtlsbook=sql_select($book_sql);

                	$booking_arr=array();
                	foreach($dtlsbook as $row)
                	{
                		$booking_arr[$row[csf('booking_no')]]['grey_qty']+=$row[csf('grey_fab_qnty')];
                	}

                	$i=1;

				
					$mrr_sql_issue_rtn="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, sum(c.quantity) as quantity,b.cons_rate,b.cons_amount
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and a.entry_form in (209) and c.entry_form in (209)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' and b.store_id='$store_id' group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no,b.cons_rate,b.cons_amount";
				

					$dtlsArray=sql_select($mrr_sql_issue_rtn);
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$tot_reject=$row[csf('returnable_qnty')];
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}
						$booking_qty=$booking_arr[$row[csf('booking_no')]]['grey_qty'];
						$process_loss=($row[csf('quantity')]/$booking_qty)*100;

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                            <!--  <td width="80"><p><? //echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
                            <td width="110"><p><? //echo $knitting_company; ?></p></td> -->
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <!-- <td width="80"><p><? //echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <td width="60"><p><? //echo $row[csf('rack')]; ?></p></td>
                            <td width="80" align="right"><p><? //echo number_format($booking_qty,2); ?></p></td>-->
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            <td width="50" align="right"><p><? echo number_format($row[csf('cons_rate')],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format(($row[csf('quantity')]*$row[csf('cons_rate')]),2); ?></p></td>
                            <!-- <td width="70" title="Fin Recv Qty/Grey Qty*100" align="right"><p><? //echo number_format($process_loss); ?></p></td>
                            <td width="60" align="center"><p><? //echo $row[csf('emp_id')]; ?></p></td>
                            <td width="80" align="center"><p><? //echo $row[csf('qc_name')]; ?></p></td> -->
                            <td width="200" ><p><? echo trim($description," , "); ?></p></td>
                            <td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
                            <td width="50" align="center"><p><? echo $dia; ?></p></td>
                            <td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                        </tr>
                         <?
                         $tot_issueRtn_qty+=$row[csf('quantity')];
						 $tot_issueRtn_amount+=number_format(($row[csf('quantity')]*$row[csf('order_rate')]),2,'.','');
                         $tot_booking_qty+=$booking_qty;
                         $tot_reject_qty+=$row[csf('returnable_qnty')];
                         $i++;
                     }
                     ?>
                 </tbody>
                 <tfoot>
                 	<tr class="tbl_bottom">
                 		<td colspan="4" align="right">Total</td>
                 		<td align="right"><? //echo number_format($tot_booking_qty,2); ?> </td>
                 		<td align="right"><? echo number_format($tot_issueRtn_qty,2); ?> </td>
                 		<td align="right"></td>
                 		<td align="right"><? //echo number_format($tot_issueRtn_qty,2); ?> </td>
                 		<td align="right"></td>
                 		<td align="right"><? //echo number_format($tot_issueRtn_amount,2); ?> </td>
                 		
                 		<td align="right">&nbsp;<? //echo number_format($tot_qty,2); ?>&nbsp;</td>
                 		<td align="right">&nbsp;<? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
                 	</tr>
                 </tfoot>
             </table>
         </div>
     </fieldset>
     <?
     $html=ob_get_contents();
     ob_flush();

     foreach (glob(""."*.xls") as $filename)
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
     	$(document).ready(function(e) {
     		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
     	});

     </script>
     <?
     exit();
}
if($action=="woven_only_transf_in_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1195px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			
			<table border="1" class="rpt_table" rules="all" width="1175" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="18">Transfer In Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
                        <!-- <th width="80">Dyeing Source</th>
                        <th width="110">Dyeing Company</th> -->
                        <th width="100">Challan No</th>
                        <th width="80">Color</th>
                        <!-- <th width="80">Batch No</th>
                        <th width="60">Rack No</th>
                        <th width="80">Grey Qty.</th> -->
                        <th width="80">Fin. Transfer. Qty.</th>
                        <th width="50">Rate</th>
                        <th width="80">Amount</th>
                        <!-- <th width="70">Process Loss Qty.</th>
                        <th width="60">QC ID</th>
                        <th width="80">QC Name</th> -->
                        <th width="200">Fabric Des.</th>
                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                        <th>Collar/Cuff Pcs</th>
                    </tr>
                </thead>
                <tbody>
                	<?
                	$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
                	$prod_array=array();
                	foreach($prodData as $row)
                	{
                		$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
                		$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
                		$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
                	}

                	$book_sql="select a.booking_no, b.grey_fab_qnty from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id)";
                	$dtlsbook=sql_select($book_sql);

                	$booking_arr=array();
                	foreach($dtlsbook as $row)
                	{
                		$booking_arr[$row[csf('booking_no')]]['grey_qty']+=$row[csf('grey_fab_qnty')];
                	}

                	$i=1;
					$mrr_sql_trnsf="select a.transfer_system_id, a.challan_no,a.transfer_date,c.color_id, b.prod_id,b.rack,d.feb_description_id,d.gsm,d.dia_width,
					sum(c.quantity) as quantity,b.cons_rate 
					from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e 
					where a.id=d.mst_id and d.to_trans_id=b.id
					and a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and b.transaction_type=5 and c. trans_type=5 and a.entry_form in (258) and c.entry_form in (258) and a.item_category=3 
					and a.is_deleted=0 and a.status_active=1 
					and b.status_active=1 and b.is_deleted=0
					and c.po_breakdown_id in($po_id) and a.to_company='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' and b.store_id in($store_id)
					group by a.transfer_system_id, a.challan_no, a.transfer_date,c.color_id, b.prod_id,b.rack,d.feb_description_id,d.gsm,d.dia_width,b.cons_rate";
					

					$dtlsArray=sql_select($mrr_sql_trnsf);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}
						$booking_qty=$booking_arr[$row[csf('booking_no')]]['grey_qty'];
						$process_loss=($row[csf('quantity')]/$booking_qty)*100;

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                            <!--  <td width="80"><p><? //echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
                            <td width="110"><p><? //echo $knitting_company; ?></p></td> -->
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <!-- <td width="80"><p><? //echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <td width="60"><p><? //echo $row[csf('rack')]; ?></p></td>
                            <td width="80" align="right"><p><? //echo number_format($booking_qty,2); ?></p></td>-->
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            <td width="50" align="right"><p><? echo number_format($row[csf('cons_rate')],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format(($row[csf('quantity')]*$row[csf('cons_rate')]),2); ?></p></td>
                            <!-- <td width="70" title="Fin Recv Qty/Grey Qty*100" align="right"><p><? //echo number_format($process_loss); ?></p></td>
                            <td width="60" align="center"><p><? //echo $row[csf('emp_id')]; ?></p></td>
                            <td width="80" align="center"><p><? //echo $row[csf('qc_name')]; ?></p></td> -->
                            
                            <td width="200" ><p><? echo trim($description," , "); ?></p></td>
                            <td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
                            <td width="50" align="center"><p><? echo $dia; ?></p></td>
                            <td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                         </tr>
                         <?
                         $tot_qty_trns+=$row[csf('quantity')];
						 $tot_amount_trns+=number_format(($row[csf('quantity')]*$row[csf('cons_rate')]),2,'.','');
                         $tot_booking_qty+=$booking_qty;
                         $tot_reject_qty+=$row[csf('returnable_qnty')];
                         $i++;

                     }
                     ?>
                 </tbody>
                 <tfoot>
                 	<tr class="tbl_bottom">
                 		<td colspan="4" align="right">Total</td>
                 		<td align="right"><? //echo number_format($tot_booking_qty,2); ?> </td>
                 		<td align="right"><? echo number_format($tot_qty_trns,2); ?> </td>
                 		<td align="right"></td>
                 		<td align="right"><? echo number_format($tot_amount_trns,2); ?> </td>
                 		<td colspan="2"> </td>
                 		<td align="right">&nbsp;<? //echo number_format($tot_qty,2); ?>&nbsp;</td>
                 		<td align="right">&nbsp;<? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
                 	</tr>
                 </tfoot>
             </table>
         </div>
     </fieldset>
     <?
     $html=ob_get_contents();
     ob_flush();

     foreach (glob(""."*.xls") as $filename)
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
     	$(document).ready(function(e) {
     		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
     	});

     </script>
     <?
     exit();
}
if($action=="woven_only_today_receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1250px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
				<?
					$gsm_arr=return_library_array( "select id, gsm_weight from lib_yarn_count_determina_mst", "id", "gsm_weight");
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");

					$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$gsm_arr[$row[csf('detarmination_id')]];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$date_from=str_replace("'","",$from_date);
					if( $date_from=="") $today_receive_date=""; else $today_receive_date= "and b.transaction_date='".$date_from."'";

				$mrr_sql="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, b.cons_rate, sum(c.quantity) as quantity
				from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
				where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id  and a.entry_form in (17) and c.entry_form in (17)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' and b.store_id='$store_id' $today_receive_date group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no, b.cons_rate";
		
				
					

				$dtlsArray=sql_select($mrr_sql);
				
			?>
			<table border="1" class="rpt_table" rules="all" width="1235" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="13">Receive Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="80">Dyeing Source</th>
						<th width="110">Dyeing Company</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="50">Rate</th>
						<th width="80">Amount</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>

					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];

						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						
						//amount calculating
						$amount = ($row[csf('quantity')]*$row[csf('cons_rate')]);
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
							<td width="110"><p><? echo $knitting_company; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[csf('cons_rate')],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($amount,2); ?></p></td>
							<td width="200" ><p><? echo trim($description," , "); ?></p></td>
							<td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $dia; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_amount+=number_format($amount,2,'.','');
						$tot_reject_qty+=$row[csf('returnable_qnty')];
						$i++;

					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="7" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
						<td align="right"></td>
						<td align="right"><? echo number_format($tot_amount,2); ?> </td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
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
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});

	</script>
	<?
	exit();
}
if($action=="woven_only_today_issue_rtn_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1250px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
				<?
					$gsm_arr=return_library_array( "select id, gsm_weight from lib_yarn_count_determina_mst", "id", "gsm_weight");
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");

					$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$gsm_arr[$row[csf('detarmination_id')]];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$date_from=str_replace("'","",$from_date);
					if( $date_from=="") $today_receive_date=""; else $today_receive_date= "and b.transaction_date='".$date_from."'";
		
					$mrr_issue_rtn_sql="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, b.cons_rate, sum(c.quantity) as quantity
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and a.entry_form in (209) and c.entry_form in (209)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' and b.store_id='$store_id' $today_receive_date group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no, b.cons_rate";

					$dtlsArray_issue_rtn=sql_select($mrr_issue_rtn_sql);
					
				?>	
			<table border="1" class="rpt_table" rules="all" width="1235" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="13">Issue Return Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="80">Dyeing Source</th>
						<th width="110">Dyeing Company</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="50">Rate</th>
						<th width="80">Amount</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>

					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($dtlsArray_issue_rtn as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];

						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						
						//amount calculating
						$amount = ($row[csf('quantity')]*$row[csf('cons_rate')]);
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
							<td width="110"><p><? echo $knitting_company; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[csf('cons_rate')],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($amount,2); ?></p></td>
							<td width="200" ><p><? echo trim($description," , "); ?></p></td>
							<td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $dia; ?></p></td>
						</tr>
						<?
						$tot_qty3+=$row[csf('quantity')];
						$tot_amount3+=number_format($amount,2,'.','');
						$tot_reject_qty3+=$row[csf('returnable_qnty')];
						$i++;

					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="7" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty3,2); ?> </td>
						<td align="right"></td>
						<td align="right"><? echo number_format($tot_amount3,2); ?> </td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
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
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});

	</script>
	<?
	exit();
}
if($action=="woven_only_today_transf_in_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1250px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
				<?
					$gsm_arr=return_library_array( "select id, gsm_weight from lib_yarn_count_determina_mst", "id", "gsm_weight");
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");

					$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$gsm_arr[$row[csf('detarmination_id')]];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$date_from=str_replace("'","",$from_date);
					if( $date_from=="") $today_receive_date=""; else $today_receive_date= "and b.transaction_date='".$date_from."'";

					$mrr_trns_in_sql="select a.transfer_system_id as  recv_number,null as knitting_source,null as booking_no, null as knitting_company, a.challan_no,a.transfer_date as receive_date,c.color_id, b.prod_id,b.rack, b.cons_rate, sum(c.quantity) as quantity
					from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e  
					where a.id=d.mst_id and d.to_trans_id=b.id
					and a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and b.transaction_type=5 and c. trans_type=5 and a.entry_form in (258) and c.entry_form in (258) and a.item_category=3 
					and a.is_deleted=0 and a.status_active=1 
					and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.to_company='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' and b.store_id='$store_id' $today_receive_date  group by a.transfer_system_id, a.challan_no, a.transfer_date,c.color_id, b.prod_id,b.rack, b.cons_rate";

					$dtlsArray_trns_in=sql_select($mrr_trns_in_sql);
				?>

			<table border="1" class="rpt_table" rules="all" width="1235" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="13">Transfer In Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="80">Dyeing Source</th>
						<th width="110">Dyeing Company</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="50">Rate</th>
						<th width="80">Amount</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>

					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($dtlsArray_trns_in as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];

						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						
						//amount calculating
						$amount = ($row[csf('quantity')]*$row[csf('cons_rate')]);
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
							<td width="110"><p><? echo $knitting_company; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[csf('cons_rate')],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($amount,2); ?></p></td>
							<td width="200" ><p><? echo trim($description," , "); ?></p></td>
							<td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $dia; ?></p></td>
						</tr>
						<?
						$tot_qty2+=$row[csf('quantity')];
						$tot_amount2+=number_format($amount,2,'.','');
						$tot_reject_qty2+=$row[csf('returnable_qnty')];
						$i++;

					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="7" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty2,2); ?> </td>
						<td align="right"></td>
						<td align="right"><? echo number_format($tot_amount2,2); ?> </td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
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
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});

	</script>
	<?
	exit();
}



if($action=="woven_only_issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_id').innerHTML+'</body</html>');
			d.close();
		}
	</script>
	<?
	ob_start();
	$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
	?>
	<fieldset style="width:1130px; margin-left:3px">
		<div id="report_id" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1110" cellpadding="0" cellspacing="0" align="left">
				<thead>
					<tr>
						<th colspan="10">Issue To Cutting Info</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Issue No</th>
						<th width="120">Issue to Company</th>
						<th width="100">Challan No</th>
						<th width="100">Issue Date</th>
						<th width="100">Color</th>
                        <!-- <th width="100">Batch No</th>
                        <th width="60">Rack No</th> -->
                        <th width="80">Qty</th>
                        <th width="50">Rate</th>
                        <th width="80">Amount</th>
                        <th width="">Fabric Des.</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $i=1;
					
                    $mrr_sql="select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack,b.cons_rate
                    from  inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
                    where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id  and a.entry_form=19 and c.entry_form=19 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' and b.store_id='$store_id' and b.transaction_date <='$from_date' group by a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id,c.color_id,c.prod_id,b.rack,b.cons_rate";
					
                    $dtlsArray=sql_select($mrr_sql);
					
					$poIdArr = array();
					foreach($dtlsArray as $row)
                    {
						$poIdArr[$row[csf('prod_id')]] = $row[csf('prod_id')];
					}
                    $product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$poIdArr).")", "id", "product_name_details"  );

                    foreach($dtlsArray as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
							
						//amount calculating
						$amount = ($row[csf('quantity')]*$row[csf('cons_rate')]);
                        ?>
                        <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td align="center" width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td align="center" width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <!--<td width="100"><p><? //echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <td width="60" ><p><? //echo $row[csf('rack')]; ?></p></td> -->
                            <td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                            <td width="50" align="right" ><p><? echo number_format($row[csf('cons_rate')],2); ?> &nbsp;</p></td>
                            <td width="80" align="right" ><p><? echo number_format($amount,2); ?> &nbsp;</p></td>
                            <td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                        <?
                        $tot_qty+=$row[csf('quantity')];
                        $tot_amount+=number_format($amount,2,'.','');
                        $i++;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="6" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                        <td align="right"></td>
                        <td align="right"><? echo number_format($tot_amount,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
	<?
    $html=ob_get_contents();
    ob_flush();

    foreach (glob(""."*.xls") as $filename)
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
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
        });

    </script>
    <?
    exit();
}
if($action=="woven_only_recv_rtn_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_id').innerHTML+'</body</html>');
			d.close();
		}
	</script>
	<?
	ob_start();
	$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
	?>
	<fieldset style="width:1130px; margin-left:3px">
		<div id="report_id" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		
            <table border="1" class="rpt_table" rules="all" width="1110" cellpadding="0" cellspacing="0" align="left">
                <thead>
                    <tr>
                        <th colspan="10">Receive Return To Supplier</th>
                    </tr>
                    <tr>
                        <th width="30">Sl</th>
                        <th width="110">Issue No</th>
                        <th width="120">Issue to Company</th>
                        <th width="100">Challan No</th>
                        <th width="100">Issue Date</th>
                        <th width="100">Color</th>
                    	<!-- <th width="100">Batch No</th>
                        <th width="60">Rack No</th> -->
                        <th width="80">Qty</th>
                        <th width="50">Rate</th>
                        <th width="80">Amount</th>
                        <th width="">Fabric Des.</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $i=1;
                
                    $mrr_sql_recv_rtrn="select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, c.quantity,c.color_id,c.prod_id,b.rack,b.cons_rate
                    from  inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
                    where a.id=b.mst_id and b.id=c.trans_id  and c.prod_id=e.id and b.prod_id=e.id and a.entry_form=202 and c.entry_form=202 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' and b.store_id='$store_id'";
	               
                    $dtlsArray=sql_select($mrr_sql_recv_rtrn);
					$poIdArr = array();
					foreach($dtlsArray as $row)
                    {
						$poIdArr[$row[csf('prod_id')]] = $row[csf('prod_id')];
					}
                    $product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$poIdArr).")", "id", "product_name_details"  );

                    foreach($dtlsArray as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
							
						//amount calculating
						$amount = ($row[csf('quantity')]*$row[csf('cons_rate')]);
                        ?>
                        <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td align="center" width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td align="center" width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <!--<td width="100"><p><? //echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <td width="60" ><p><? //echo $row[csf('rack')]; ?></p></td> -->
                            <td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                            <td width="50" align="right" ><p><? echo number_format($row[csf('cons_rate')],2); ?> &nbsp;</p></td>
                            <td width="80" align="right" ><p><? echo number_format($amount,2); ?> &nbsp;</p></td>
                            <td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                        <?
                        $tot_qty2+=$row[csf('quantity')];
                        $tot_amount2+=number_format($amount,2,'.','');
                        $i++;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="6" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty2,2); ?>&nbsp;</td>
                        <td align="right"></td>
                        <td align="right"><? echo number_format($tot_amount2,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
	<?
    $html=ob_get_contents();
    ob_flush();

    foreach (glob(""."*.xls") as $filename)
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
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
        });

    </script>
    <?
    exit();
}
if($action=="woven_only_transf_out_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_id').innerHTML+'</body</html>');
			d.close();
		}
	</script>
	<?
	ob_start();
	$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
	?>
	<fieldset style="width:1130px; margin-left:3px">
		<div id="report_id" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1110" cellpadding="0" cellspacing="0" align="left">
				<thead>
					<tr>
						<th colspan="10">Transfer Out</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Issue No</th>
						<th width="120">Issue to Company</th>
						<th width="100">Challan No</th>
						<th width="100">Issue Date</th>
						<th width="100">Color</th>
                        <!-- <th width="100">Batch No</th>
                        <th width="60">Rack No</th> -->
                        <th width="80">Qty</th>
                        <th width="50">Rate</th>
                        <th width="80">Amount</th>
                        <th width="">Fabric Des.</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $i=1;
					
	                    $mrr_sql="select a.company_id, a.transfer_system_id as issue_number, a.challan_no,a. transfer_date as issue_date, b.prod_id, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack,b.cons_rate  
						from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e  
						where a.id=d.mst_id and d.trans_id=b.id and a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id  and b.transaction_type=6 and c. trans_type=6 and a.entry_form in (258) and c.entry_form in (258) 
						and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) 
						and a.company_id='$companyID' and e.product_name_details='$prod_id'  and c.color_id='$color' and b.store_id='$store_id' and b.transaction_date<='$from_date'  
						group by a.company_id, a.transfer_system_id, a.challan_no, a.transfer_date,c.color_id,c.prod_id,b.rack,b.prod_id,b.cons_rate";
					
                    $dtlsArray=sql_select($mrr_sql);
					
					$poIdArr = array();
					foreach($dtlsArray as $row)
                    {
						$poIdArr[$row[csf('prod_id')]] = $row[csf('prod_id')];
					}
                    $product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$poIdArr).")", "id", "product_name_details"  );

                    foreach($dtlsArray as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
							
						//amount calculating
						$amount = ($row[csf('quantity')]*$row[csf('cons_rate')]);
                        ?>
                        <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td align="center" width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td align="center" width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <!--<td width="100"><p><? //echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <td width="60" ><p><? //echo $row[csf('rack')]; ?></p></td> -->
                            <td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                            <td width="50" align="right" ><p><? echo number_format($row[csf('cons_rate')],2); ?> &nbsp;</p></td>
                            <td width="80" align="right" ><p><? echo number_format($amount,2); ?> &nbsp;</p></td>
                            <td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                        <?
                        $tot_qty+=$row[csf('quantity')];
                        $tot_amount+=number_format($amount,2,'.','');
                        $i++;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="6" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                        <td align="right"></td>
                        <td align="right"><? echo number_format($tot_amount,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
	<?
    $html=ob_get_contents();
    ob_flush();

    foreach (glob(""."*.xls") as $filename)
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
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
        });

    </script>
    <?
    exit();
}

if($action=="woven_only_today_issue_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1150px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>

			
					<?
					$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$date_from=str_replace("'","",$from_date);
					if( $date_from=="") $today_issue_date=""; else $today_issue_date= "and b.transaction_date='".$from_date."'";
					/*select a.company_id,a.recv_number as issue_number, a.challan_no,a.receive_date as issue_date, b.prod_id, c.quantity,c.color_id,c.prod_id,b.rack
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id  and a.entry_form in (209) and c.entry_form in (209)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id=$prod_id and c.color_id='$color' $today_issue_date */
					
					$mrr_sql="select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack,b.cons_rate 
					from inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
					where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id  and a.entry_form=19 and c.entry_form=19 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' and b.store_id='$store_id' $today_issue_date group by a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id,c.color_id,c.prod_id,b.rack,b.cons_rate ";					
						?>
						<table border="1" class="rpt_table" rules="all" width="1130" cellpadding="0" cellspacing="0" align="center">
							<thead>
								<tr>
									<th colspan="10">Issue To Cutting Info</th>
								</tr>
								<tr>
									<th width="30">Sl</th>
									<th width="110">Issue No</th>
									<th width="120">Issue to Company</th>
									<th width="100">Challan No</th>
									<th width="100">Issue Date</th>
									<th width="100">Color</th>
									<th width="80">Qty</th>
									<th width="50">Rate</th>
									<th width="80">Amount</th>
									<th width="">Fabric Des.</th>
								</tr>
							</thead>
							<tbody>
								<?

								$dtlsArray=sql_select($mrr_sql);

								$i=1;
								foreach($dtlsArray as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									$description = $prod_array[$row[csf('prod_id')]]['name_details'];
									$amount = ($row[csf('quantity')]*$row[csf('cons_rate')]);
									?>
									<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td width="30"><p><? echo $i; ?></p></td>
										<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
										<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
										<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
										<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
										<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
										<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
										<td width="50" align="right" ><p><? echo number_format($row[csf('cons_rate')],2); ?> &nbsp;</p></td>
										<td width="80" align="right" ><p><? echo number_format($amount,2); ?> &nbsp;</p></td>
										<td  align="right"><p><? echo trim($description, " , "); ?></p></td>
									</tr>
									<?
									$tot_qty+=$row[csf('quantity')];
									$tot_amount+=number_format($amount,2,'.','');
									$i++;
								}
								?>
							</tbody>
							<tfoot>
								<tr class="tbl_bottom">
									<td colspan="6" align="right">Total</td>
									<td align="right"><? echo number_format($tot_qty,2); ?></td>
									<td align="right"></td>
									<td align="right"><? echo number_format($tot_amount,2); ?></td>
									<td>&nbsp; </td>
								</tr>
							</tfoot>
						</table>
						<?
					
					?>
		</div>
	</fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
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
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});

	</script>
	<?
	exit();
}
if($action=="woven_only_today_recv_rtn_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1150px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>

			
					<?
					$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$date_from=str_replace("'","",$from_date);
					if( $date_from=="") $today_issue_date=""; else $today_issue_date= "and b.transaction_date='".$from_date."'";
					/*select a.company_id,a.recv_number as issue_number, a.challan_no,a.receive_date as issue_date, b.prod_id, c.quantity,c.color_id,c.prod_id,b.rack
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id  and a.entry_form in (209) and c.entry_form in (209)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id=$prod_id and c.color_id='$color' $today_issue_date */
					
					$mrr_sql="select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, c.quantity,c.color_id,c.prod_id,b.rack,b.cons_rate 
					from inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
					where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id and  a.entry_form=202 and c.entry_form=202 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' and b.store_id='$store_id' $today_issue_date ";
						?>
						<table border="1" class="rpt_table" rules="all" width="1130" cellpadding="0" cellspacing="0" align="center">
							<thead>
								<tr>
									<th colspan="10">Today Receive Return</th>
								</tr>
								<tr>
									<th width="30">Sl</th>
									<th width="110">Issue No</th>
									<th width="120">Issue to Company</th>
									<th width="100">Challan No</th>
									<th width="100">Issue Date</th>
									<th width="100">Color</th>
									<th width="80">Qty</th>
									<th width="50">Rate</th>
									<th width="80">Amount</th>
									<th width="">Fabric Des.</th>
								</tr>
							</thead>
							<tbody>
								<?

								$dtlsArray=sql_select($mrr_sql);

								$i=1;
								foreach($dtlsArray as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									$description = $prod_array[$row[csf('prod_id')]]['name_details'];
									$amount = ($row[csf('quantity')]*$row[csf('cons_rate')]);
									?>
									<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td width="30"><p><? echo $i; ?></p></td>
										<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
										<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
										<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
										<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
										<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
										<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
										<td width="50" align="right" ><p><? echo number_format($row[csf('cons_rate')],2); ?> &nbsp;</p></td>
										<td width="80" align="right" ><p><? echo number_format($amount,2); ?> &nbsp;</p></td>
										<td  align="right"><p><? echo trim($description, " , "); ?></p></td>
									</tr>
									<?
									$tot_qty+=$row[csf('quantity')];
									$tot_amount+=number_format($amount,2,'.','');
									$i++;
								}
								?>
							</tbody>
							<tfoot>
								<tr class="tbl_bottom">
									<td colspan="6" align="right">Total</td>
									<td align="right"><? echo number_format($tot_qty,2); ?></td>
									<td align="right"></td>
									<td align="right"><? echo number_format($tot_amount,2); ?></td>
									<td>&nbsp; </td>
								</tr>
							</tfoot>
						</table>
						<?
					
					?>
		</div>
	</fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
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
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});

	</script>
	<?
	exit();
}
if($action=="woven_only_today_transf_out_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1150px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>

			
					<?
					$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$date_from=str_replace("'","",$from_date);
					if( $date_from=="") $today_issue_date=""; else $today_issue_date= "and b.transaction_date='".$from_date."'";
					/*select a.company_id,a.recv_number as issue_number, a.challan_no,a.receive_date as issue_date, b.prod_id, c.quantity,c.color_id,c.prod_id,b.rack
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id  and a.entry_form in (209) and c.entry_form in (209)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id=$prod_id and c.color_id='$color' $today_issue_date */
					
						$mrr_sql="select a.company_id, a.transfer_system_id as issue_number, a.challan_no,a. transfer_date as issue_date, b.prod_id, c.quantity,c.color_id,c.prod_id,b.rack,b.cons_rate  
						from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e  
						where a.id=d.mst_id and d.trans_id=b.id and a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id and b.transaction_type=6 and c. trans_type=6 and a.entry_form in (258) and c.entry_form in (258) 
						and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) 
						and a.company_id='$companyID' and e.product_name_details='$prod_id' and c.color_id='$color' and b.store_id='$store_id' $today_issue_date 
						group by a.company_id, a.transfer_system_id, a.challan_no, a.transfer_date,c.color_id,c.prod_id,b.rack , c.quantity,b.prod_id,b.cons_rate";
					

					
						?>
						<table border="1" class="rpt_table" rules="all" width="1130" cellpadding="0" cellspacing="0" align="center">
							<thead>
								<tr>
									<th colspan="10">Today Transfer Out</th>
								</tr>
								<tr>
									<th width="30">Sl</th>
									<th width="110">Issue No</th>
									<th width="120">Issue to Company</th>
									<th width="100">Challan No</th>
									<th width="100">Issue Date</th>
									<th width="100">Color</th>
									<th width="80">Qty</th>
									<th width="50">Rate</th>
									<th width="80">Amount</th>
									<th width="">Fabric Des.</th>
								</tr>
							</thead>
							<tbody>
								<?

								$dtlsArray=sql_select($mrr_sql);

								$i=1;
								foreach($dtlsArray as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									$description = $prod_array[$row[csf('prod_id')]]['name_details'];
									$amount = ($row[csf('quantity')]*$row[csf('cons_rate')]);
									?>
									<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td width="30"><p><? echo $i; ?></p></td>
										<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
										<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
										<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
										<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
										<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
										<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
										<td width="50" align="right" ><p><? echo number_format($row[csf('cons_rate')],2); ?> &nbsp;</p></td>
										<td width="80" align="right" ><p><? echo number_format($amount,2); ?> &nbsp;</p></td>
										<td  align="right"><p><? echo trim($description, " , "); ?></p></td>
									</tr>
									<?
									$tot_qty+=$row[csf('quantity')];
									$tot_amount+=number_format($amount,2,'.','');
									$i++;
								}
								?>
							</tbody>
							<tfoot>
								<tr class="tbl_bottom">
									<td colspan="6" align="right">Total</td>
									<td align="right"><? echo number_format($tot_qty,2); ?></td>
									<td align="right"></td>
									<td align="right"><? echo number_format($tot_amount,2); ?></td>
									<td>&nbsp; </td>
								</tr>
							</tfoot>
						</table>
						<?
					
					?>
		</div>
	</fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
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
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});

	</script>
	<?
	exit();
}

if($action=="woven_knit_stock_popup") //Stock
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id."**".$color;die;
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_id').innerHTML+'</body</html>');
			d.close();
		}
	</script>
	<?
	ob_start();
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="report_id" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="8"> Woven Stock Details</th>
					</tr>
					<tr>
						<th width="50">Sl</th>
						<th width="80">Product ID</th>
						<th width="200">Batch No</th>
						<th width="100">Floor</th>
						<th width="100">Room</th>
						<th width="100">Rack</th>
						<th width="100">Shelf</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?

					if($rpt_btn=='btn3')
					{
						$mrr_sql="
						select c.prod_id, b.floor_id, b.room, b.rack, b.self,
							sum(case when c.entry_form in (17) then c.quantity else 0 end) as recv_qnty,
							sum(case when c.entry_form in (19) then c.quantity else 0 end) as issue_qnty,
							 0  as issue_retn_qnty, 
							 0 as recv_rtn_qnty ,0 as finish_fabric_transfer_in,
							 0 as finish_fabric_transfer_out 
						from
							inv_transaction b,
							order_wise_pro_details c,product_details_master e 
						where
							b.id=c.trans_id  and c.prod_id=e.id and b.prod_id=e.id and c.entry_form in(17,19,258,209,202) and b.item_category=3 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in (".$po_id.") and c.color_id='".$color."' and b.batch_id in($batchId) and e.product_name_details='".$prod_id."'  and b.transaction_date <='$from_date' 
						group by
							c.prod_id, b.floor_id, b.room, b.rack, b.self 

						union all

						select c.prod_id, b.floor_id, b.room, b.rack, b.self,0 as recv_qnty, 0 as issue_qnty,
							 sum(case when c.entry_form in (209) and b.transaction_type=4 then c.quantity else 0 end) as issue_retn_qnty, 
							 sum(case when c.entry_form in (202) and b.transaction_type=3 then c.quantity else 0 end) as recv_rtn_qnty,
							 sum (case when c.entry_form in (258) and b.transaction_type=5 then c.quantity else 0 end) as finish_fabric_transfer_in, 
							 sum (case when c.entry_form in (258) and b.transaction_type=6 then c.quantity else 0 end) as finish_fabric_transfer_out 
						from
							inv_transaction b,
							order_wise_pro_details c,product_details_master e 
						where
							b.id=c.trans_id  and c.prod_id=e.id and b.prod_id=e.id and c.entry_form in(17,19,258,209,202) and b.item_category=3 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in (".$po_id.") and c.color_id='".$color."' and b.pi_wo_batch_no in($batchId) and e.product_name_details='".$prod_id."'  and b.transaction_date <='$from_date' 
						group by
							c.prod_id, b.floor_id, b.room, b.rack, b.self 

							";
					}
					else
					{
						$mrr_sql=" select x.prod_id,x.batch_id, x.floor_id, x.room, x.rack, x.self, x.recv_qnty, x.issue_qnty ,x.issue_retn_qnty,x.recv_rtn_qnty ,x.finish_fabric_transfer_in,x.finish_fabric_transfer_out from(
						select c.prod_id, cast(b.batch_id as varchar2(4000)) as batch_id, b.floor_id, b.room, b.rack, b.self,
							sum(case when c.entry_form in (17) then c.quantity else 0 end) as recv_qnty,
							sum(case when c.entry_form in (19) then c.quantity else 0 end) as issue_qnty,
							sum(case when c.entry_form in (209) and b.transaction_type=4 then c.quantity else 0 end) as issue_retn_qnty,
							sum(case when c.entry_form in (202) and b.transaction_type=3 then c.quantity else 0 end) as recv_rtn_qnty,
							0 as finish_fabric_transfer_in,
							0 as finish_fabric_transfer_out
							
						from
							inv_transaction b,
							order_wise_pro_details c,product_details_master e 
						where
							b.id=c.trans_id  and c.prod_id=e.id and b.prod_id=e.id and c.entry_form in(17,19,258,209,202) and b.item_category=3 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in (".$po_id.") and c.color_id='".$color."' and e.product_name_details='".$prod_id."'  and b.body_part_id='".$body_part."' and b.store_id='".$store_id."' and b.transaction_date <='$from_date' 
						group by
							c.prod_id, b.batch_id, b.floor_id, b.room, b.rack, b.self 
						union all 
							select c.prod_id, cast(b.pi_wo_batch_no as varchar2(4000)) as batch_id, b.floor_id, b.room, b.rack, b.self,
							0 as recv_qnty,
							0 as issue_qnty,
							0 as issue_retn_qnty,
							0 as recv_rtn_qnty,
							sum  (case when c.entry_form in (258) and b.transaction_type=5 then c.quantity else 0 end) as finish_fabric_transfer_in,
							sum (case when c.entry_form in (258) and b.transaction_type=6 then c.quantity else 0 end) as finish_fabric_transfer_out 
							from
							inv_transaction b,
							order_wise_pro_details c,product_details_master e 
						where
							b.id=c.trans_id  and c.prod_id=e.id and b.prod_id=e.id and c.entry_form in(17,19,258,209,202) and b.item_category=3 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in (".$po_id.") and c.color_id='".$color."' and e.product_name_details='".$prod_id."'  and b.body_part_id='".$body_part."' and b.store_id='".$store_id."' and b.transaction_date <='$from_date' 
						group by
							c.prod_id, b.pi_wo_batch_no, b.floor_id, b.room, b.rack, b.self ) x group by  x.prod_id,x.batch_id, x.floor_id, x.room, x.rack, x.self, x.recv_qnty, x.issue_qnty ,x.issue_retn_qnty,x.recv_rtn_qnty ,x.finish_fabric_transfer_in,x.finish_fabric_transfer_out

							";
					}
					
					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);
					if(empty($dtlsArray))
					{
						echo get_empty_data_msg();
						die;
					}
					
					$batchIdArr = array();
					$floorIdArr = array();
					$roomIdArr = array();
					$rackIdArr = array();
					$shelfIdArr = array();
					foreach($dtlsArray as $row)
					{
						if($rpt_btn=='btn3')
						{
							$batchIdArr[$batchId] = $batchId;
						}
						else
						{
							if($row[csf('batch_id')]!="")
							{
								$batchIdArr[$row[csf('batch_id')]] = $row[csf('batch_id')];
							}
						}
						
						$row[csf('floor_id')] = ($row[csf('floor_id')]*1);
						$row[csf('room')] = ($row[csf('room')]*1);
						$row[csf('rack')] = ($row[csf('rack')]*1);
						$row[csf('self')] = ($row[csf('self')]*1);
						
						if($row[csf('floor_id')] != 0)
						{
							$floorIdArr[$row[csf('floor_id')]] = $row[csf('floor_id')];
						}
						
						if($row[csf('room')] != 0)
						{
							$roomIdArr[$row[csf('room')]] = $row[csf('room')];
						}
						
						if($row[csf('rack')] != 0)
						{
							$rackIdArr[$row[csf('rack')]] = $row[csf('rack')];
						}
						
						if($row[csf('self')] != 0)
						{
							$shelfIdArr[$row[csf('self')]] = $row[csf('self')];
						}
					}
					
					//product_name_details
					$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in (".$po_id.") and status_active = 1 and is_deleted = 0", "id", "product_name_details"  );
					
					//pro_batch_create_mst
					$batchCondition = '';
					if(!empty($batchIdArr))
					{
						$batchCondition = " and id in(".implode(",", $batchIdArr).")";
					}
					$batch_no_arr=return_library_array( "select id, batch_no from pro_batch_create_mst where status_active = 1 and is_deleted = 0 ".$batchCondition."",'id','batch_no');

					if($rpt_btn=='btn3')
					{
						$batchName="";
						$batchNameArr=explode(',', $batchId);
						foreach ($batchNameArr as $vall) {
						 	$batchName.=$batch_no_arr[$vall].",";
						 } 
						 $batchName=chop($batchName,",");
					}
					else
					{
						$batchName=$batch_no_arr[$row[csf('batch_id')]];
					}
					
					//floorSql
					$floorSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")
					";
					//echo $floorSql;
					$floorDetails = return_library_array( $floorSql, 'floor_room_rack_id', 'floor_room_rack_name');
					
					//roomSql
					$roomSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.room_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")
					";
					$roomDetails = return_library_array( $roomSql, 'floor_room_rack_id', 'floor_room_rack_name');
					
					//rackSql
					$rackSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.serial_no
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")
					";
					$rackDetails = return_library_array( $rackSql, 'floor_room_rack_id', 'floor_room_rack_name');
					
					/*
					$rackSerialNoSql = "
						SELECT b.floor_room_rack_dtls_id, b.serial_no
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")
						GROUP BY b.floor_room_rack_dtls_id, b.serial_no
						ORDER BY b.serial_no ASC
					";
					$rackSerialNoResult = sql_select($rackSerialNoSql);
					foreach($rackSerialNoResult as $row)
					{
						$rackSerialNoArr[$row[csf('floor_room_rack_dtls_id')]] = $row[csf('serial_no')];
					}
					*/
				
					//selfSql
					
					$shelfSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.shelf_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")
					";
					$shelfDetails = return_library_array( $shelfSql, 'floor_room_rack_id', 'floor_room_rack_name');
					
					//binSql
					/*
					$binSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.bin_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyId.")
					";
					$binDetails = return_library_array( $binSql, 'floor_room_rack_id', 'floor_room_rack_name');					
					*/
					
					$i=1;
					foreach($dtlsArray as $row)
					{

						if($row[csf('recv_qnty')]>0 || $row[csf('finish_fabric_transfer_in')]>0 ||$row[csf('issue_retn_qnty')]>0 || $row[csf('issue_qnty')]>0 || $row[csf('finish_fabric_transfer_out')]>0 ||$row[csf('recv_rtn_qnty')]>0)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							
							$row[csf('floor_id')] = (($row[csf('floor_id')]*1) == 0 ? '' : $floorDetails[$row[csf('floor_id')]]);
							$row[csf('room')] = (($row[csf('room')]*1) == 0 ? '' : $roomDetails[$row[csf('room')]]);
							$row[csf('rack')] = (($row[csf('rack')]*1) == 0 ? '' : $rackDetails[$row[csf('rack')]]);
							$row[csf('self')] = (($row[csf('self')]*1) == 0 ? '' : $shelfDetails[$row[csf('self')]]);

							$tot_balance=($row[csf('recv_qnty')]+$row[csf('finish_fabric_transfer_in')]+$row[csf('issue_retn_qnty')])-($row[csf('issue_qnty')]+$row[csf('finish_fabric_transfer_out')]+$row[csf('recv_rtn_qnty')]);
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center"><p><? echo $i; ?></p></td>
								<td align="center" title="<? echo $product_arr[$row[csf('prod_id')]];?>"><p><? echo $row[csf('prod_id')]; ?></p></td>
								<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
								<td align="center"><p><? echo $row[csf('floor_id')]; ?></p></td>
								<td align="center"><p><? echo $row[csf('room')]; ?></p></td>
								<td align="center"><p><? echo $row[csf('rack')]; ?></p></td>
								<td align="center"><p><? echo $row[csf('self')]; ?></p></td>
								<td align="right"><p><? echo number_format($tot_balance,2); ?></p></td>
							</tr>
							<?
							$tot_qty+=$tot_balance;
							$i++;
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="7" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
		<?
		$html=ob_get_contents();
		ob_flush();
		foreach (glob(""."*.xls") as $filename)
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
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
	</fieldset>
	<?
	exit();
}

?>