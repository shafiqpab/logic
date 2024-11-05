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
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

if ($action=="load_drop_down_store")
{
	//$data=explode("**",$data);
	//if($data[1]==2) $disable=1; else $disable=0;

	//$store_cond = ($userCredential[0][csf("store_location_id")]) ? " and a.id in (".$userCredential[0][csf("store_location_id")].")" : "" ;
	echo create_drop_down( "cbo_store_id", 100, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id='$data' and  b.category_type in(2,3)  group by a.id,a.store_name","id,store_name", 1, "--Select Store--", 0, "",$disable );
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

if ($action=="booking_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	$company_id=$data[0];
	$buyer_id=$data[1];
	$year_id=$data[2];

	?>
	<script>
	function js_set_value(booking_no)
	{
		document.getElementById('selected_booking').value=booking_no;
		//alert(booking_no);
		parent.emailwindow.hide();
	}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="750" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
	    	<tr>
	        	<td align="center" width="100%">
	            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
	                    <thead>
	                       <th width="150">Buyer Name</th>
	                       <th width="150">Booking No</th>
	                       <th width="200">Date Range</th><th></th>
	                    </thead>
	        			<tr>
	                    <input type="hidden" id="selected_booking">
	                   	<td>
	                     <?
						echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_id,"",0 );
								?>
	                    </td>
	                    <td>
	                    	<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes_numeric">
	                    </td>
	                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
						  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
						 </td>
	            		 <td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_booking_no').value,'create_booking_search_list_view', 'search_div', 'order_wise_finish_fabric_stock_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
	        		</tr>
	             </table>
	          </td>
	        </tr>
	        <tr>
	            <td  align="center" height="40" valign="middle">
	            <?
				echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
				?>
				<? echo load_month_buttons();  ?>
	            </td>
	            </tr>
	        <tr>
	            <td align="center"valign="top" id="search_div">
	            </td>
	        </tr>
	    </table>
	    </form>
	   </div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	$company=$data[3];
	//if ($data[3]!=0) $company="  company_id='$data[3]'"; else { echo "Please Select Company First."; die; }
	if ($data[0]!=0) $buyer=" and buyer_id='$data[0]'"; else { echo "Please Select Buyer First."; die; }
	if ($data[4]!="") {$booking=" and booking_no_prefix_num='$data[4]'";}
	//if ($data[4]!=0) $job_no=" and job_no='$data[4]'"; else $job_no='';
	if($db_type==0)
	{
	if ($data[1]!="" &&  $data[2]!="") $booking_date  = "and booking_date  between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
	if ($data[1]!="" &&  $data[2]!="") $booking_date  = "and booking_date  between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'"; else $booking_date ="";
	}
	$order_arr=return_library_array("select id, po_number from wo_po_break_down","id","po_number");
	$po_array=array();
	$sql_po= sql_select("SELECT booking_no,po_break_down_id from wo_booking_mst  where company_id='$company' $buyer $booking $booking_date and booking_type=1 and is_short=2 and   status_active=1  and is_deleted=0 order by booking_no");
	foreach($sql_po as $row)
	{
		 $po_id=explode(",",$row[csf("po_break_down_id")]);
		//print_r( $po_id);
		$po_number_string="";
		foreach($po_id as $key=> $value )
		{
			$po_number_string.=$order_arr[$value].",";
		}
		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
	}
	// print_r($po_array);
	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$po_num=return_library_array( "select job_no, job_no_prefix_num from wo_po_details_master",'job_no','job_no_prefix_num');
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$po_num,5=>$po_array,6=>$item_category,7=>$fabric_source,8=>$suplier,9=>$approved,10=>$is_ready);
	 $sql= "SELECT booking_no_prefix_num, booking_no,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved from wo_booking_mst  where company_id=$company $buyer $booking $booking_date and booking_type=1 and is_short in(1,2) and  status_active=1  and 	is_deleted=0 order by booking_no";
	 // echo $sql;
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,PO number,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "80,80,70,100,90,200,80,80,50,50","1020","320",0, $sql , "js_set_value", "booking_no_prefix_num", "", 1, "0,0,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','','0,0,0,0,0,0,0,0,0,0,0','','');

	exit();
}// Booking Search End


$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per");
//$consumtion_library=return_library_array( "select job_no, avg_finish_cons from wo_pre_cost_fabric_cost_dtls", "job_no", "avg_finish_cons");

// ================================Print button ==============================

if($action=="print_button_variable_setting")
{

    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=135 and is_deleted=0 and status_active=1");
	$printButton=explode(',',$print_report_format);

	 foreach($printButton as $id){
		if($id==108)$buttonHtml.='<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:60px" class="formbutton" />';
		if($id==259)$buttonHtml.='<input type="button" name="search" id="search" value="Show2" onClick="generate_report(2)" style="width:60px;" class="formbutton" />';
		if($id==242)$buttonHtml.='<input type="button" name="search" id="search" value="Show3" onClick="generate_report(3)" style="width:60px;" class="formbutton" />';
		if($id==359)$buttonHtml.='<input type="button" name="search" id="search" value="Show4" onClick="generate_report(4)" style="width:60px;" class="formbutton" />';
	 }
	 echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";

    exit();
}
// ======================= End Print button =================================================

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_report_type=str_replace("'","",$cbo_report_type);
	$cbo_presentation=str_replace("'","",$cbo_presentation);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$value_for_search_by=str_replace("'","",$cbo_value_for_search_by);
	$txt_search_comm= trim(str_replace("'","",$txt_search_comm));
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$txt_search_booking=trim(str_replace("'","",$txt_search_booking));
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_store_id=str_replace("'","",$cbo_store_id);
	$cbo_year=str_replace("'","",$cbo_year);

	$cbo_sock_for=trim(str_replace("'","",$cbo_sock_for));
	$cbo_date_cat=str_replace("'","",$cbo_date_cat);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	// echo $cbo_year;die;
	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";

	if(str_replace("'","",$cbo_store_id)!=0) $store_id_cond=" and c.store_id=$cbo_store_id";
	if(str_replace("'","",$cbo_shipment_status)!="" && str_replace("'","",$cbo_shipment_status)!=0) $shipment_id_cond=" and b.shiping_status=$cbo_shipment_status";


	$job_no=str_replace("'","",$txt_job_no);
	$search_cond='';
	//if($cbo_report_type !=2 && $type!=1)
	if($cbo_report_type !=2)
	{
		if($cbo_search_by==1)
		{
			if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.job_no_prefix_num in ($txt_search_comm) ";
		}

		else if($cbo_search_by==2)
		{
			if ($txt_search_comm=="") $search_cond.="";
			else
			{
				$sql_style="select id, style_ref_no from wo_po_details_master where style_ref_no like '%$txt_search_comm%'";
				$sql_style_res=sql_select($sql_style);
				foreach ($sql_style_res as $val) {
					$style_id .= $val[csf('id')].',';
				}
				$style_ids=chop($style_id,',');
				$search_cond.=" and a.id in($style_ids) ";
			}
		}
		else if($cbo_search_by==3)
		{
			if ($txt_search_comm=="") $search_cond.="";
			else
			{
				$sql_order="select id, po_number from wo_po_break_down where po_number like '%$txt_search_comm%'";
				$sql_order_res=sql_select($sql_order);
				foreach ($sql_order_res as $val) {
					$po_id .= $val[csf('id')].',';
				}
				$po_ids=chop($po_id,',');
				$search_cond.=" and b.id in($po_ids) ";
			}
		}
		else if($cbo_search_by==4)
		{
			if ($txt_search_comm=="") $search_cond.="";
			else
			{
				$sql_file="select id, file_no from wo_po_break_down where file_no like '%$txt_search_comm%'";
				$sql_file_res=sql_select($sql_file);
				foreach ($sql_file_res as $val) {
					$file_id .= $val[csf('id')].',';
				}
				$file_ids=chop($file_id,',');
				$search_cond.=" and b.id in($file_ids) ";
			}
		}
		else if($cbo_search_by==5)
		{
			if ($txt_search_comm=="") $search_cond.="";
			else
			{
				$sql_grouping="select id, grouping from wo_po_break_down where grouping like '%$txt_search_comm%'";
				$sql_grouping_res=sql_select($sql_grouping);
				foreach ($sql_grouping_res as $val) {
					$grouping_id .= $val[csf('id')].',';
				}
				$grouping_ids=chop($grouping_id,',');
				$search_cond.=" and b.id in($grouping_ids) ";
			}
		}
		else
		{
			$search_cond.="";
		}
	}

	$date_from=str_replace("'","",$txt_date_from_st);
	if( $date_from=="") $receive_date=""; else $receive_date= " and c.transaction_date <=".$txt_date_from_st."";

	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";

	$date_from=str_replace("'","",$txt_date_from_st);
	if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from_st."";

	ob_start();
	if($type==1) // show button start
	{

		if($cbo_report_type==1) // Knit Finish Start
		{

			$product_array=array();
			$sql_product="select id, color from product_details_master where item_category_id=2 and status_active=1 and is_deleted=0";
			$sql_product_result=sql_select($sql_product);
			foreach( $sql_product_result as $row )
			{
				$product_array[$row[csf('id')]]=$row[csf('color')];
			}
			unset($sql_product_result);

			$transfer_arr=array(); $all_data_arr=array();

			if($db_type==0)
			{
				$prod_id_cond=" group_concat(b.from_prod_id)";
				if($cbo_year!="") $year_cond="and year(a.insert_date) in($cbo_year)"; else $year_cond="";
			}
			else if($db_type==2)
			{
				$prod_id_cond=" listagg(cast(b.from_prod_id as varchar2(4000)),',') within group (order by b.from_prod_id)";
				if($cbo_year!="") $year_cond="and to_char(a.insert_date,'YYYY') in($cbo_year)";  else $year_cond="";
			}

			$iss_return_qnty=array();
			$sql_issue_ret=sql_select("select po_breakdown_id, prod_id, sum(quantity) as issue_ret_qnty  from order_wise_pro_details where trans_id!=0 and status_active=1 and is_deleted=0 and entry_form=126 group by po_breakdown_id, prod_id");

			foreach( $sql_issue_ret as $row )
			{
				$iss_return_qnty[$row[csf('po_breakdown_id')]][$product_array[$row[csf('prod_id')]]]['issue_ret_qnty']+=$row[csf('issue_ret_qnty')];
			}
			unset($sql_issue_ret);

			$rec_return_qnty=array();
			$sql_rec_ret=sql_select("select po_breakdown_id, prod_id, sum(quantity) as rec_ret_qnty from order_wise_pro_details where trans_id!=0 and status_active=1 and is_deleted=0 and entry_form=46 group by po_breakdown_id, prod_id");
			foreach( $sql_rec_ret as $row )
			{
				$rec_return_qnty[$row[csf('po_breakdown_id')]][$product_array[$row[csf('prod_id')]]]['rec_ret_qnty']+=$row[csf('rec_ret_qnty')];
			}
			unset($sql_rec_ret);
			/*echo "<pre>";
			print_r($rec_return_qnty);*/

			$booking_qnty=array();
			$sql_booking=sql_select("select b.po_break_down_id, b.fabric_color_id, sum(b.fin_fab_qnty ) as fin_fab_qnty  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,b.fabric_color_id");
			foreach( $sql_booking as $row_book)
			{
				$booking_qnty[$row_book[csf('po_break_down_id')]][$row_book[csf('fabric_color_id')]]['fin_fab_qnty']=$row_book[csf('fin_fab_qnty')];
			}
			unset($sql_booking);
			$actual_cut_qnty=array();
			$sql_actual_cut_qty=sql_select("select a.po_break_down_id,c.color_number_id, sum(b.production_qnty) as actual_cut_qty  from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=1 and a.is_deleted=0 and a.status_active=1  group by  a.po_break_down_id,c.color_number_id");
			foreach( $sql_actual_cut_qty as $row_actual)
			{
				$actual_cut_qnty[$row_actual[csf('po_break_down_id')]][$row_actual[csf('color_number_id')]]['actual_cut_qty']=$row_actual[csf('actual_cut_qty')];
			}
			unset($sql_actual_cut_qty);

			$result_consumtion=array();

			$sql_consumtiont_qty=sql_select("SELECT b.po_break_down_id, b.color_number_id, c.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from  wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c where b.pre_cost_fabric_cost_dtls_id=c.id and b.pcs>0  group by b.po_break_down_id, b.color_number_id, c.body_part_id");

			foreach($sql_consumtiont_qty as $row_consum)
			{
				$result_consumtion[$row_consum[csf('po_break_down_id')]][$row_consum[csf('color_number_id')]]+=$row_consum[csf('requirment')]/$row_consum[csf('pcs')];
			}
			unset($sql_consumtiont_qty);




			if($cbo_presentation==1) // order wise
			{

				$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
				?>
				<fieldset style="width:2490px;">
					<table cellpadding="0" cellspacing="0" width="1480">
						<tr class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="22" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
						</tr>
						<tr  class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="22" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
						</tr>
						<tr  class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="22" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from_st)) ;?></strong></td>
						</tr>
					</table>
					<table width="2490" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
						<thead>
							<th width="30">SL</th>
							<th width="70">Buyer</th>
							<th width="60">Job Year</th>
							<th width="60">Job</th>
							<th width="100">Booking No.</th>
							<th width="100">Order</th>
							<th width="100">Internal Ref</th>
							<th width="110">Style</th>
							<th width="80">Order Qty (Pcs)</th>
							<th width="60">Ex-factory</th>
							<th width="90">Shipping Status </th>
							<th width="90">Stock For </th>
							<th width="90">Shipment Date</th>
							<th width="90">Fin. Color</th>

							<th width="80">Req. Qty</th>
	                        <th width="80" title="Trans. in">Trans. In</th>
							<th width="80" title="Received">Received</th>

							<th width="70" title="Issue Return">Issue Return</th>
							<th width="80" title="Rec.+Issue Rtn.+Trans. in">Total Received</th>
							<th width="80" title="Req.-Totat Rec.">Received Balance</th>
	                        <th width="80" title="Trans. out">Trans Out</th>
							<th width="80" title="Issued">Issued To Others</th>
							<th width="80" title="Issued">Issued To Cutting</th>
							<th width="80" title="Issued">Issue Balance</th>

							<th width="70" title="Recv. Return">Recv. Return</th>
							<th width="80" title="Issue+Rec. Ret.+Trans. out">Total Issued</th>

							<th width="80" title="Total Rec.- Total Issue">Stock</th>
							<th width="80" title="Total Rec.- Total Issue">DOH</th>
							<th width="80">Consumption Pcs.</th>
							<th width="80">Possible Cut Pcs.</th>
							<th>Actual Cut</th>
						</thead>
					</table>
					<div style="width:2490px; max-height:350px; overflow-y:scroll;" id="scroll_body">
						<table width="2490" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
							<?
							$po_break_id_con='';
							if($txt_search_booking!='')
							{
								$sql_booking_query="select id, po_break_down_id, fabric_color_id, booking_no from wo_booking_dtls where booking_no like '%$txt_search_booking' and status_active=1 and is_deleted=0 and booking_type=1";
								//echo $sql_booking_query;die;
								$bookArray=sql_select($sql_booking_query);
								$booking_po_id_arr=array();
								$booking_po_data_arr=array();
								foreach ($bookArray as $row)
								{
									$booking_po_id_arr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
									$booking_po_data_arr[$row[csf("po_break_down_id")]][$row[csf("fabric_color_id")]]['booking_no']=$row[csf("booking_no")];
								}

								$all_booking_no=array_chunk($booking_po_id_arr,999);
								$all_booking_no_cond=" and(";
								foreach($all_booking_no as $booking_id)
								{
									if($all_booking_no_cond==" and(")
									{
										$all_booking_no_cond.=" b.id in(".implode(',',$booking_id).")";
									}
									else
									{
										$all_booking_no_cond.=" or b.id in(".implode(',',$booking_id).")";
									}
								}
								$all_booking_no_cond.=")";

								if($all_booking_no_cond == " and()"){
									$all_booking_no_cond = "";
								}
							}

							// echo $all_booking_no_cond;die;

							if($db_type==0)
							{
								$select_prod_id =" group_concat(c.prod_id) as prod_id";
							}else{
								$select_prod_id =" listagg(cast(d.prod_id as varchar2(4000)),',') within group (order by d.prod_id) as prod_id";
							}

							if($txt_date_from !="" && $txt_date_to!="")
							{
								if($cbo_date_cat==1) $search_cond.=" and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to' ";
								else if($cbo_date_cat==2) $search_cond.=" and b.update_date between '$txt_date_from' and '$txt_date_to' and b.status_active=3";
							}

							if($cbo_sock_for==1)
							{
								$search_cond.=" and b.shiping_status<>3 and b.status_active=1";
							}
							else if($cbo_sock_for==2)
							{
								$search_cond.=" and b.status_active=3";
							}
							else if($cbo_sock_for==3)
							{
								$search_cond.=" and b.shiping_status=3 and b.status_active=1";
							}

							$sql_query="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, sum(a.total_set_qnty) as ratio, b.id as po_id, b.po_number,b.grouping, b.plan_cut as po_quantity,b.shipment_date, b.shiping_status,b.status_active,
							sum(case when d.entry_form in (7,37,66,68) then d.quantity else 0 end) as receive_qnty,
							sum(case when d.entry_form in(14,15,134,306) and d.trans_type=5 then d.quantity else 0 end) as rec_trns_qnty, to_char(a.insert_date,'YYYY') as year,
							sum(case when d.entry_form in (18,71) and f.issue_purpose <>9 then d.quantity else 0 end) as issue_qnty,
							sum(case when d.entry_form in (18,71) and f.issue_purpose=9 then d.quantity else 0 end) as issue_cut_qnty,
							sum(case when d.entry_form in (126,52) then d.quantity else 0 end) as issue_ret_qnty,
							sum(case when d.entry_form in (46) then d.quantity else 0 end) as rec_ret_qnty,
							sum(case when d.entry_form in(14,15,134,306) and d.trans_type=6 then d.quantity else 0 end) as issue_trns_qnty, e.color color_id, d.prod_id,min(c.transaction_date) as min_date, max(c.transaction_date) as max_date
							from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details d,product_details_master e,inv_transaction c left join inv_issue_master f on f.id=c.mst_id and f.status_active=1 and c.TRANSACTION_TYPE=2
							where a.job_no=b.job_no_mst and b.id=d.po_breakdown_id  and d.trans_id=c.id and c.prod_id=e.id and d.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and d.entry_form in (14,7,37,66,68,14,15,18,71,126,134,52,46,306) and c.item_category=2 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active in (1,3) and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_name=$cbo_company_id $all_booking_no_cond $receive_date $buyer_id_cond $store_id_cond $shipment_id_cond $search_cond $year_cond
							group by a.job_no_prefix_num,a.job_no,a.buyer_name,a.style_ref_no,a.insert_date,b.id,b.po_number,b.grouping,e.color,b.plan_cut,b.shiping_status,b.status_active,d.prod_id,b.shipment_date
							order by a.buyer_name,a.job_no,b.po_number,e.color";
							//echo $sql_query;die;
							//$select_prod_id


							$i=1; $k=1;$total_rec_return_qnty=0;$total_issue_ret_qnty=0;$issue_trns_out_qnty=0;$rec_trns_in_qnty=0;
							$nameArray=sql_select($sql_query); $fin_color_array=array(); $fin_color_data_arr=array();$checkbuyerArr=array(); $po_break_id_arr=array();$prod_id_arr=array();$prod_id_all="";

							foreach ($nameArray as $row)
							{
								$datastr = $row[csf('job_no')]."##".$row[csf('job_no_prefix_num')]."##".$row[csf('style_ref_no')]."##".$row[csf('po_number')]."##".$row[csf('po_id')]."##".$row[csf('color_id')]."##".$row[csf('po_quantity')]."##".$row[csf('shiping_status')];

								$data_array[$row[csf('buyer_name')]][$datastr]['buyer_name']		=$row[csf('buyer_name')];
								$data_array[$row[csf('buyer_name')]][$datastr]['year']		        =$row[csf('year')];
								$data_array[$row[csf('buyer_name')]][$datastr]['job_no']			=$row[csf('job_no')];
								$data_array[$row[csf('buyer_name')]][$datastr]['job_no_prefix_num']	=$row[csf('job_no_prefix_num')];
								$data_array[$row[csf('buyer_name')]][$datastr]['style_ref_no']		=$row[csf('style_ref_no')];
								$data_array[$row[csf('buyer_name')]][$datastr]['po_number']			=$row[csf('po_number')];
								$data_array[$row[csf('buyer_name')]][$datastr]['po_id']				=$row[csf('po_id')];
								$data_array[$row[csf('buyer_name')]][$datastr]['grouping']			=$row[csf('grouping')];
								$data_array[$row[csf('buyer_name')]][$datastr]['color_id']			=$row[csf('color_id')];
								$data_array[$row[csf('buyer_name')]][$datastr]['po_quantity']		=$row[csf('po_quantity')];
								$data_array[$row[csf('buyer_name')]][$datastr]['shiping_status']	=$row[csf('shiping_status')];
								$data_array[$row[csf('buyer_name')]][$datastr]['shipment_date']		=$row[csf('shipment_date')];
								$data_array[$row[csf('buyer_name')]][$datastr]['ratio']				+=$row[csf('ratio')];
								$data_array[$row[csf('buyer_name')]][$datastr]['prod_id']			.=$row[csf('prod_id')].",";
								$data_array[$row[csf('buyer_name')]][$datastr]['min_date']			=$row[csf('min_date')];
								$data_array[$row[csf('buyer_name')]][$datastr]['max_date']			=$row[csf('max_date')];

								$data_array[$row[csf('buyer_name')]][$datastr]['receive_qnty']		+=$row[csf('receive_qnty')];
								$data_array[$row[csf('buyer_name')]][$datastr]['rec_trns_qnty']		+=$row[csf('rec_trns_qnty')];
								$data_array[$row[csf('buyer_name')]][$datastr]['issue_qnty']		+=$row[csf('issue_qnty')];
								$data_array[$row[csf('buyer_name')]][$datastr]['issue_cut_qnty']		+=$row[csf('issue_cut_qnty')];
								$data_array[$row[csf('buyer_name')]][$datastr]['issue_ret_qnty']	+=$row[csf('issue_ret_qnty')];
								$data_array[$row[csf('buyer_name')]][$datastr]['rec_ret_qnty']		+=$row[csf('rec_ret_qnty')];
								$data_array[$row[csf('buyer_name')]][$datastr]['issue_trns_qnty']	+=$row[csf('issue_trns_qnty')];
								$data_array[$row[csf('buyer_name')]][$datastr]['status_active']	     =$row[csf('status_active')];

								$prod_data_array[$row[csf('buyer_name')]][$datastr][$row[csf('prod_id')]]['receive_qnty']		+=$row[csf('receive_qnty')];
								$prod_data_array[$row[csf('buyer_name')]][$datastr][$row[csf('prod_id')]]['rec_trns_qnty']		+=$row[csf('rec_trns_qnty')];
								$prod_data_array[$row[csf('buyer_name')]][$datastr][$row[csf('prod_id')]]['issue_qnty']			+=$row[csf('issue_qnty')];
								$prod_data_array[$row[csf('buyer_name')]][$datastr][$row[csf('prod_id')]]['issue_ret_qnty']		+=$row[csf('issue_ret_qnty')];
								$prod_data_array[$row[csf('buyer_name')]][$datastr][$row[csf('prod_id')]]['rec_ret_qnty']		+=$row[csf('rec_ret_qnty')];
								$prod_data_array[$row[csf('buyer_name')]][$datastr][$row[csf('prod_id')]]['issue_trns_qnty']	+=$row[csf('issue_trns_qnty')];

								$prod_id_all.=$row[csf('prod_id')].",";

							}

							if($txt_search_booking =='')
							{
								// ======================== STORE PO ID IN GLOBL TEMP TBL FOR FUTURE USE =======================
								$con = connect();
								$tr_str=execute_query("delete from gbl_temp_report_id where user_id=$user_id");
								if($tr_str) oci_commit($con);

								$temp_table_id=return_field_value("max(id)+1 as id","gbl_temp_report_id","1=1","id");
								if($temp_table_id=="") $temp_table_id=1;
								$booking_po_data_arr=array();
								foreach($nameArray as $row)
								{
									$po_break_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
									$color_id_arr[$row[csf('color_id')]]=$row[csf('color_id')];

									if($po_check[$row[csf("po_id")]]=="")
									{
										$po_check[$row[csf("po_id")]]=$row[csf("po_id")];
										$r_id=execute_query("insert into gbl_temp_report_id (id, ref_val, ref_from, user_id, ref_string) values ($temp_table_id,".$row[csf("po_id")].",1,$user_id,'".$row[csf("po_id")]."')");
										if($r_id)
										{
											$r_id=1;
										}
										else
										{
											echo "insert into gbl_temp_report_id (id, ref_val, ref_from, user_id, ref_string) values ($temp_table_id,".$row[csf("po_id")].",1,$user_id,'".$row[csf("po_id")]."')";
											oci_rollback($con);
											die;
										}
										$temp_table_id++;
									}
									if($color_id_check[$row[csf("color_id")]]=="")
									{
										$color_id_check[$row[csf("color_id")]]=$row[csf("color_id")];
										$r_id2=execute_query("insert into gbl_temp_report_id (id, ref_val, ref_from, user_id, ref_string) values ($temp_table_id,".$row[csf("color_id")].",2,$user_id,'".$row[csf("color_id")]."')");
										if($r_id)
										{
											$r_id2=1;
										}
										else
										{
											echo "insert into gbl_temp_report_id (id, ref_val, ref_from, user_id, ref_string) values ($temp_table_id,".$row[csf("color_id")].",2,$user_id,'".$row[csf("color_id")]."')";
											oci_rollback($con);
											die;
										}
										$temp_table_id++;
									}
								}

								if($r_id && $r_id2)
								{
									oci_commit($con);
								}
								else
								{
									oci_rollback($con);
								}

								//$sql_booking_po_query=sql_select("select po_break_down_id, booking_no, fabric_color_id from wo_booking_dtls where po_break_down_id in(".implode(',',$po_break_id_arr).") and  fabric_color_id in(".implode(',',$color_id_arr).") and status_active=1 and is_deleted=0 and booking_type=1 ");
								$sql_booking_po_query=sql_select("select po_break_down_id, booking_no, fabric_color_id from wo_booking_dtls where po_break_down_id in(select ref_string from gbl_temp_report_id where user_id=$user_id and ref_from=1) and  fabric_color_id in(select ref_string from gbl_temp_report_id where user_id=$user_id and ref_from=2) and status_active=1 and is_deleted=0 and booking_type=1 ");

								foreach ($sql_booking_po_query as $row)
								{
									$booking_po_data_arr[$row[csf("po_break_down_id")]][$row[csf("fabric_color_id")]]['booking_no']=$row[csf("booking_no")];
								}
							}
							$con = connect();
							$tr_str=execute_query("delete from gbl_temp_report_id where user_id=$user_id");
							if($tr_str) oci_commit($con);

							foreach ($data_array as $buyer_name=>$buyer_data)
							{
								foreach ($buyer_data as $buyer_str => $row)
								{
									// $shistatus = $row["shiping_status"];
									// var_dump($shistatus);
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$dzn_qnty=0; $rec_qty=0;
									if($costing_per_id_library[$row['job_no']]==1) $dzn_qnty=12;
									else if($costing_per_id_library[$row['job_no']]==3) $dzn_qnty=12*2;
									else if($costing_per_id_library[$row['job_no']]==4) $dzn_qnty=12*3;
									else if($costing_per_id_library[$row['job_no']]==5) $dzn_qnty=12*4;
									else $dzn_qnty=1;

									$order_id=$row['po_id'];
									$color_id=$row["color_id"];
									$row["prod_id"]=chop($row["prod_id"],",");

									$issue_ret_qnty=$row['issue_ret_qnty'];
									$trans_in_qty=$row["rec_trns_qnty"];
									$rec_qty=($row["receive_qnty"]);

									$trans_out=($row["issue_trns_qnty"]);
									$iss_qty=$row["issue_qnty"];
									$issue_cut_qnty=$row["issue_cut_qnty"];
									$rec_ret_qnty=$row["rec_ret_qnty"];
									//$rec_ret_qnty=($rec_return_qnty[$row['po_id']][$row['color_id']]['rec_ret_qnty']);
									//echo $trans_in_qty.'+'.$rec_qty.'+'.$issue_ret_qnty.'<br>';

									$total_receive=$trans_in_qty+$rec_qty+$issue_ret_qnty;
									$total_issue=$trans_out+$iss_qty+$rec_ret_qnty+$issue_cut_qnty;
									$stock = 0;
									$stock = $total_receive-$total_issue;

									$prod_str="";
									$prod_id_arr = array_unique(explode(",", $row["prod_id"]));
									if(!empty($prod_id_arr))
									{
										foreach ($prod_id_arr as $pval)
										{
											$prod_stock = ($prod_data_array[$buyer_name][$buyer_str][$pval]['receive_qnty'] + $prod_data_array[$buyer_name][$buyer_str][$pval]['issue_ret_qnty'] + $prod_data_array[$buyer_name][$buyer_str][$pval]['rec_trns_qnty']) - ($prod_data_array[$buyer_name][$buyer_str][$pval]['issue_qnty'] + $prod_data_array[$buyer_name][$buyer_str][$pval]['issue_trns_qnty'] + $prod_data_array[$buyer_name][$buyer_str][$pval]['rec_ret_qnty']);

											$prod_str .= "p=".$pval .", s=" .$prod_stock."; ";
										}

										$prod_str = chop($prod_str,"; ");
									}


									//$stock = number_format($stock,2);
									// echo $stock."<br>";
									// echo $value_for_search_by;
									// die('Go to hell');
									if($value_for_search_by==2 && $stock > 0)
									{
										if(!in_array($row["buyer_name"],$checkbuyerArr))
										{
											if($k!=1)
											{
												?>
												<tr class="tbl_bottom">
													<td colspan="8" align="right">Buyer Total</td>
													<td align="right"><? //echo number_format($po_quantity,2);$po_quantity=0; ?></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>

													<td align="right"><? echo number_format($subtot_book_qty,2); $subtot_book_qty=0;?></td>
			                                        <td align="right"><? echo number_format($subtot_trns_in_qnty,2); $subtot_trns_in_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_rec_qty,2); $subtot_rec_qty=0;?></td>

													<td align="right"><? echo number_format($subtot_issue_ret_qnty,2); $subtot_issue_ret_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_total_receive,2); $subtot_total_receive=0;?></td>
													<td align="right"><? echo number_format($subtot_rec_bal,2); $subtot_rec_bal=0;?></td>
			                                        <td align="right"><?  echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_iss_qty,2); $subtot_iss_qty=0;?></td>
													<td align="right"><? echo number_format($sub_iss_cut_qnty,2);$sub_iss_cut_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_iss_balance_qty,2); $subtot_iss_balance_qty=0;?></td>

													<td align="right"><? echo number_format($subtot_rec_ret_qnty,2); $subtot_rec_ret_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_total_issue,2); $subtot_total_issue=0;?></td>
													<td align="right"><? echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
													<td align="right"><? //echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
													<td align="right"><? //echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_possible_cut_pcs,2); $subtot_possible_cut_pcs=0;?></td>
													<td align="right"><? echo number_format($subtot_actual_qty,2); $subtot_actual_qty=0;?></td>
												</tr>
												<?
											}
											$checkbuyerArr[]=$row["buyer_name"];
											$k++;
										}
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30" align="center"><? echo $i; ?></td>
											<td width="70" align="center"><p><? echo $buyer_arr[$row["buyer_name"]]; ?></p></td>
											<td width="60" align="center"><p><? echo $row["year"]; ?> </p></td>
											<td width="60" align="center"><p><? echo $row["job_no_prefix_num"]; ?></p></td>

											<td width="100" align="center"><p><? echo $booking_po_data_arr[$row["po_id"]][$row["color_id"]]['booking_no']; ?></p></td>

											<td width="100" align="center" title="<? echo $row["po_id"] ;?>" ><p><? echo $row["po_number"]; ?></p></td>
											<td width="100" align="center" title="" ><p><? echo $row["grouping"]; ?></p></td>
											<td width="110" align="center"><p><? echo $row["style_ref_no"]; ?></p></td>
											<td width="80" align="right"><p><? $po_quantity+=$row["po_quantity"];echo number_format($row["po_quantity"],2,'.',''); ?></p></td>
											<td width="60" align="center"><a href='#report_details' onClick="openmypage_ex_factory('<? echo $row['po_id']; ?>','1');">View</a></td>
											<td width="90" align="center"><p><? echo $shipment_status[$row["shiping_status"]]; ?></p></td>
											<td width="90" align="center">
												<p><?
												if($row["shiping_status"] !=3 && $row["status_active"]=1)
												{
													echo "Running Order";
												}
												else if($row["status_active"]=3)
												{
													echo "Cancelled Order";
												}
												else if($row["shiping_status"] ==3 && $row["status_active"]=1)
												{
													echo "Left Over";
												}
												?></p>
											</td>
											<td width="90" align="center"><p><? echo change_date_format($row["shipment_date"]); ?></p></td>
											<td width="90" align="center" title="<? echo $row["color_id"] ;?>"><p><? echo $color_arr[$row["color_id"]]; ?></p></td>
											<td width="80" align="right">
												<?
												$book_qty=$booking_qnty[$row['po_id']][$row['color_id']]['fin_fab_qnty'];
												$subtot_book_qty+=$book_qty;
												echo number_format($book_qty,2,'.','');
												?>
											</td>
											<?
											$subtot_issue_ret_qnty+=$issue_ret_qnty;
											$subtot_trns_in_qnty+=$trans_in_qty;
											$subtot_trns_out_qnty+=$trans_out;
											$subtot_iss_qty+=$iss_qty;
											$sub_iss_cut_qnty+=$issue_cut_qnty;
											$subtot_rec_ret_qnty+=$rec_ret_qnty;

											$subtot_total_receive+=$total_receive;
											$subtot_total_issue+=$total_issue;

											$subtot_stock+=$stock;
											$subtot_rec_qty+=$rec_qty;
											?>
			                                <td width="80" title="Trans. In" align="right">
			                                	<a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>',55,'trans_in_popup');">
													<? echo number_format($trans_in_qty,2,'.',''); ?>
												</a>
			                                </td>
											<td width="80" align="right"><? echo number_format($rec_qty,2,'.',''); ?></td>
											<td width="70" align="right">
												<a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>',2,'issue_ret_popup');">
													<? echo number_format($issue_ret_qnty,2,'.',''); ?>
												</a>
											</td>
											<td width="80" align="right">
												<a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>',1,'receive_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"><? echo number_format($total_receive,2,'.',''); ?>
												</a>
											</td>
											<td width="80" align="right"><p><? $rec_bal=$book_qty-$total_receive; $subtot_rec_bal+=$rec_bal;echo number_format($rec_bal,2,'.',''); ?></p></td>
											<td width="80" align="right">
												<a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>',55,'trans_out_popup');">
													<? echo number_format($trans_out,2,'.',''); ?>
												</a>
											</td>
											<td width="80" align="right" title="GGG"><p><a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row[csf('prod_id')]; ?>','<? echo $row['color_id']; ?>',3,'issue_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"> <? echo number_format($iss_qty,2,'.',''); ?></a></p></td>

											<td width="80" align="right" title="GGG"><p><a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row[csf('prod_id')]; ?>','<? echo $row['color_id']; ?>',3,'issue_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"> <? echo number_format($issue_cut_qnty,2,'.',''); ?></a></p></td>
											<td width="80" align="right"><p><? $iss_balance_qty=$book_qty-$iss_qty; $subtot_iss_balance_qty+=$iss_balance_qty; echo number_format($iss_balance_qty,2,'.',''); ?></p></td>

											<td width="70" align="right"><a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>',4,'receive_ret_popup');">
												<? echo number_format($rec_ret_qnty,2,'.',''); ?>
											</a>
											<td width="80" align="right"><? echo number_format($total_issue,2,'.',''); ?></td>
											<td width="80" align="right" title="<? echo $prod_str;?>">
												<p>
													<a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>',5,'stock_popup',<? echo $cbo_store_id;?>);">
														<? echo number_format($stock,2); ?>
													</a>
												</p>
											</td>

											<? $daysOnHand=datediff("d",change_date_format($row['max_date'],'','',1),date("Y-m-d")); ?>
											<td width="80" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
											<td width="80" align="center"><p><? $cons_per=($result_consumtion[$row['po_id']][$row['color_id']]); echo number_format($cons_per,8,'.',''); ?></p></td>
											<td width="80" align="right"><p><? $possible_cut_pcs=$total_issue/($cons_per);$subtot_possible_cut_pcs+=$possible_cut_pcs; echo ($cons_per!="")?number_format($possible_cut_pcs):"0"; ?></p></td>
											<td width="" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>','actual_cut_popup');"><? $actual_qty=$actual_cut_qnty[$row['po_id']][$row['color_id']]['actual_cut_qty'];$subtot_actual_qty+=$actual_qty; echo number_format($actual_qty,2,'.',''); ?></a>&nbsp;</p></td>
										</tr>
										<?
										$i++;
										$total_order_qnty+=$row["po_quantity"];
										$rec_trns_in_qnty+=$trans_in_qty;
										$total_req_qty+=$book_qty;
										$total_rec_qty+=$rec_qty;
										$total_receive_qnty+=$total_receive;
										$total_rec_bal+=$rec_bal;
										$issue_trns_out_qnty+=$trans_out;
										$total_issue_qty+=$iss_qty;
										$total_issue_balance_qty+=$iss_balance_qty;
										$total_issue_quantity+=$total_issue;
										$total_stock+=$stock;
										$total_possible_cut_pcs+=$possible_cut_pcs;
										$total_rec_return_qnty+=$rec_ret_qnty;
										$total_issue_ret_qnty+=$issue_ret_qnty;
										$total_actual_cut_qty+=$actual_qty;
									}
									else if($value_for_search_by==1)
									{
										if(!in_array($row["buyer_name"],$checkbuyerArr))
										{
											if($k!=1)
											{
												?>
												<tr class="tbl_bottom">
													<td colspan="8" align="right">Buyer Total</td>
													<td align="right"><? //echo number_format($po_quantity,2);$po_quantity=0; ?></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>

													<td align="right"><? echo number_format($subtot_book_qty,2); $subtot_book_qty=0;?></td>
			                                        <td align="right"><? echo number_format($subtot_trns_in_qnty,2); $subtot_trns_in_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_rec_qty,2); $subtot_rec_qty=0;?></td>

													<td align="right"><? echo number_format($subtot_issue_ret_qnty,2); $subtot_issue_ret_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_total_receive,2); $subtot_total_receive=0;?></td>
													<td align="right"><? echo number_format($subtot_rec_bal,2); $subtot_rec_bal=0;?></td>
			                                        <td align="right"><?  echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_iss_qty,2); $subtot_iss_qty=0;?></td>
													<td align="right"><? echo number_format($sub_iss_cut_qnty,2);$sub_iss_cut_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_iss_balance_qty,2); $subtot_iss_balance_qty=0;?></td>

													<td align="right"><? echo number_format($subtot_rec_ret_qnty,2); $subtot_rec_ret_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_total_issue,2); $subtot_total_issue=0;?></td>
													<td align="right"><? echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
													<td align="right"><? //echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
													<td align="right"><? //echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_possible_cut_pcs,2); $subtot_possible_cut_pcs=0;?></td>
													<td align="right"><? echo number_format($subtot_actual_qty,2); $subtot_actual_qty=0;?></td>
												</tr>
												<?
											}
											$checkbuyerArr[]=$row["buyer_name"];
											$k++;
										}
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30" align="center"><? echo $i; ?></td>
											<td width="70" align="center"><p><? echo $buyer_arr[$row["buyer_name"]]; ?></p></td>
											<td width="60" align="center"><p><? echo $row["year"]; ?> </p></td>
											<td width="60" align="center"><p><? echo $row["job_no_prefix_num"]; ?></p></td>

											<td width="100" align="center"><p><? echo $booking_po_data_arr[$row["po_id"]][$row["color_id"]]['booking_no']; ?></p></td>

											<td width="100" align="center" title="<? echo $row["po_id"] ;?>" ><p><? echo $row["po_number"]; ?></p></td>
											<td width="100" align="center" title="" ><p><? echo $row["grouping"]; ?></p></td>
											<td width="110" align="center"><p><? echo $row["style_ref_no"]; ?></p></td>
											<td width="80" align="right"><p><? $po_quantity+=$row["po_quantity"];echo number_format($row["po_quantity"],2,'.',''); ?></p></td>
											<td width="60" align="center"><a href='#report_details' onClick="openmypage_ex_factory('<? echo $row['po_id']; ?>','1');">View</a></td>
											<td width="90" align="center"><p><? echo $shipment_status[$row["shiping_status"]]; ?></p></td>
											<td width="90" align="center"><p><?
											if($row["shiping_status"] !=3 && $row["status_active"]=1)
											{
												echo "Running Order";
											}
											else if($row["status_active"]=3)
											{
												echo "Cancelled Order";
											}
											else if($row["shiping_status"] ==3 && $row["status_active"]=1)
											{
												echo "Left Over";
											}
											?></p></td>
											<td width="90" align="center"><p><? echo change_date_format($row["shipment_date"]); ?></p></td>
											<td width="90" align="center" title="<? echo $row["color_id"] ;?>"><p><? echo $color_arr[$row["color_id"]]; ?></p></td>
											<td width="80" align="right">
												<?
												$book_qty=$booking_qnty[$row['po_id']][$row['color_id']]['fin_fab_qnty'];
												$subtot_book_qty+=$book_qty;
												echo number_format($book_qty,2,'.','');
												?>
											</td>
											<?
											$subtot_issue_ret_qnty+=$issue_ret_qnty;
											$subtot_trns_in_qnty+=$trans_in_qty;
											$subtot_trns_out_qnty+=$trans_out;
											$subtot_iss_qty+=$iss_qty;
											$sub_iss_cut_qnty+=$issue_cut_qnty;
											$subtot_rec_ret_qnty+=$rec_ret_qnty;

											$subtot_total_receive+=$total_receive;
											$subtot_total_issue+=$total_issue;

											$subtot_stock+=$stock;
											$subtot_rec_qty+=$rec_qty;
											?>
			                                <td width="80" title="Trans. In" align="right">
			                                	<a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>',55,'trans_in_popup');">
													<? echo number_format($trans_in_qty,2,'.',''); ?>
												</a>
			                                </td>
											<td width="80" align="right"><? echo number_format($rec_qty,2,'.',''); ?></td>
											<td width="70" align="right">
												<a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>',2,'issue_ret_popup');">
													<? echo number_format($issue_ret_qnty,2,'.',''); ?>
												</a>
											</td>
											<td width="80" align="right">
												<a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>',1,'receive_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"><? echo number_format($total_receive,2,'.',''); ?>
												</a>
											</td>
											<td width="80" align="right"><p><? $rec_bal=$book_qty-$total_receive; $subtot_rec_bal+=$rec_bal;echo number_format($rec_bal,2,'.',''); ?></p></td>
											<td width="80" align="right">
												<a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>',55,'trans_out_popup');">
													<? echo number_format($trans_out,2,'.',''); ?>
												</a>
											</td>
											<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row[csf('prod_id')]; ?>','<? echo $row['color_id']; ?>',3,'issue_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"> <? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
											<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row[csf('prod_id')]; ?>','<? echo $row['color_id']; ?>',3,'cutting_issue_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"> <? echo number_format($issue_cut_qnty,2,'.',''); ?></a></p></td>

											<td width="80" align="right"><p><? $iss_balance_qty=$book_qty-$iss_qty; $subtot_iss_balance_qty+=$iss_balance_qty; echo number_format($iss_balance_qty,2,'.',''); ?></p></td>

											<td width="70" align="right">
											<a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row[csf('color_id')]; ?>',4,'receive_ret_popup');">
												<? echo number_format($rec_ret_qnty,2,'.',''); ?>
											</a>
											</td>
											<td width="80" align="right"><? echo number_format($total_issue,2,'.',''); ?></td>
											<td width="80" align="right" title="<? echo $prod_str;?>">
												<p>
													<a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>',5,'stock_popup',<? echo $cbo_store_id;?>);">
														<? echo number_format($stock,2); ?>
													</a>
												</p>
											</td>
											<? $daysOnHand=datediff("d",change_date_format($row['max_date'],'','',1),date("Y-m-d")); ?>
											<td width="80" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
											<td width="80" align="center"><p><? $cons_per=($result_consumtion[$row['po_id']][$row['color_id']]); echo number_format($cons_per,8,'.',''); ?></p></td>
											<td width="80" align="right">
											<p>
											<?
											$possible_cut_pcs=$total_issue/($cons_per);$subtot_possible_cut_pcs+=$possible_cut_pcs;
											echo ($cons_per!="")?number_format($possible_cut_pcs):"0"; ?>

											</p>
											</td>
											<td width="" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>','actual_cut_popup');"><? $actual_qty=$actual_cut_qnty[$row['po_id']][$row['color_id']]['actual_cut_qty'];$subtot_actual_qty+=$actual_qty; echo number_format($actual_qty,2,'.',''); ?></a>&nbsp;</p></td>
										</tr>
										<?
										$i++;
										$total_order_qnty+=$row["po_quantity"];
										$rec_trns_in_qnty+=$trans_in_qty;
										$total_req_qty+=$book_qty;
										$total_rec_qty+=$rec_qty;
										$total_receive_qnty+=$total_receive;
										$total_rec_bal+=$rec_bal;
										$issue_trns_out_qnty+=$trans_out;
										$total_issue_qty+=$iss_qty;
										$total_issue_cut_qnty+=$issue_cut_qnty;
										$total_issue_balance_qty+=$iss_balance_qty;
										$total_issue_quantity+=$total_issue;
										$total_stock+=$stock;
										$total_possible_cut_pcs+=$possible_cut_pcs;
										$total_rec_return_qnty+=$rec_ret_qnty;
										$total_issue_ret_qnty+=$issue_ret_qnty;
										$total_actual_cut_qty+=$actual_qty;
									}

								}
							}

							?>
							<tr class="tbl_bottom">
								<td colspan="8" align="right">Buyer Total</td>
								<td align="right"><? //echo number_format($po_quantity,2);$po_quantity=0; ?></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>

								<td align="right" ><? echo number_format($subtot_book_qty,2); $subtot_book_qty=0;?></td>
	                            <td align="right"><? echo number_format($subtot_trns_in_qnty,2); $subtot_trns_in_qnty=0;?></td>
								<td align="right"><? echo number_format($subtot_rec_qty,2); $subtot_rec_qty=0;?></td>

								<td align="right"><? echo number_format($subtot_issue_ret_qnty,2); $subtot_issue_ret_qnty=0;?></td>
								<td align="right"><? echo number_format($subtot_total_receive,2); $subtot_total_receive=0;?></td>
								<td align="right"><? echo number_format($subtot_rec_bal,2); $subtot_rec_bal=0;?></td>
	                            <td align="right"><? echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
								<td align="right"><? echo number_format($subtot_iss_qty,2); $subtot_iss_qty=0;?></td>
								<td align="right"><? echo number_format($sub_iss_cut_qnty,2);$sub_iss_cut_qnty=0;?></td>
								<td align="right"><? echo number_format($subtot_iss_balance_qty,2); $subtot_iss_balance_qty=0;?></td>

								<td align="right"><? echo number_format($subtot_rec_ret_qnty,2); $subtot_rec_ret_qnty=0;?></td>
								<td align="right"><? echo number_format($subtot_total_issue,2); $subtot_total_issue=0;?></td>
								<td align="right"><? echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
								<td align="right"><? //echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
								<td align="right"><? //echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
								<td align="right"><? echo number_format($subtot_possible_cut_pcs,2); $subtot_possible_cut_pcs=0;?></td>
								<td align="right"><? echo number_format($subtot_actual_qty,2); $subtot_actual_qty=0;?></td>
							</tr>
						</table>
						<table width="2490" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
							<tfoot>
								<th width="30"></th>
								<th width="70"></th>
								<th width="60"></th>
								<th width="60"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="110">Grand Total</th>
								<th width="80" align="right" id=""></th>
								<th width="60"></th>
								<th width="90"></th>
								<th width="90"></th>
								<th width="90"></th>
								<th width="90"></th>
								<th width="80" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
                                <th width="80" align="right" id="value_total_tranin_qty"><? echo number_format($rec_trns_in_qnty,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
								<th width="70" align="right" id="value_total_issue_ret_qty"><? echo number_format($total_issue_ret_qnty,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_receive_qty"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>
                                <th width="80" align="right" id="value_total_tranout_qty"><? echo number_format($issue_trns_out_qnty,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_issue_cut_qty"><? echo number_format($total_issue_cut_qnty,2,'.','') ; ?></th>
								<th width="80" align="right" id="value_total_issue_balance_qty"><? echo number_format($total_issue_balance_qty,2,'.',''); ?></th>

								<th width="70"  id="value_recv_ret_qty"><? echo number_format($total_rec_return_qnty,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_issue_quantity"><? echo number_format($total_issue_quantity,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
								<th width="80">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="80" align="right" id="total_possible_cut_pcs"><? echo number_format($total_possible_cut_pcs,2,'.',''); ?></th>
								<th width="" align="right" id="total_actual_cut_qty"><? echo number_format($total_actual_cut_qty,2,'.',''); ?></th>
							</tfoot>
						</table>
					</div>
				</fieldset>
				<?
			}
			else // style wise
			{
				$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
				?>
				<fieldset style="width:1910px;">
					<table cellpadding="0" cellspacing="0" width="1830">
						<tr class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="20" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
						</tr>
						<tr  class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
						</tr>
						<tr  class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="20" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from_st)) ;?></strong></td>
						</tr>
					</table>
					<table width="1900" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
						<thead>
							<th width="30">SL</th>
							<th width="70">Buyer</th>
							<th width="60">Job</th>
							<th width="100">Booking</th>
							<th width="50">Year</th>
							<th width="110">Style</th>
							<th width="90">Job Qty. (Pcs)</th>
							<th width="60">Order Status</th>
							<th width="110">Fin. Color</th>
							<th width="90">Req. Qty</th>
							<th width="80" title="Trans. in">Trans. In</th>
							<th width="80" title="Received">Received</th>
							<th width="70" title="Issue Return">Issue Return</th>
							<th width="80" title="Rec.+Issue Rtn.+Trans. in">Total Received</th>
							<th width="80" title="Req.-Totat Rec.">Received Balance</th>
	                        <th width="80" title="Trans. out">Trans Out</th>
							<th width="80" title="Issued">Issued</th>
							<th width="80" title="Issued Balance">Issue Balance</th>
							<th width="70" title="Recv. Return">Recv. Return</th>
							<th width="80" title="Issue+Rec. Ret.+Trans. out">Total Issued</th>
							<th width="80" title="Total Rec.- Total Issue">Stock</th>

							<th width="100">Consumption Pcs.</th>
							<th width="80">Possible Cut Pcs.</th>
							<th>Actual Cut</th>
						</thead>
					</table>
					<div style="width:1920px; max-height:350px; overflow-y:scroll;" id="scroll_body">
						<table width="1900" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
							<?
							$po_break_id_con=''; $booking_po_data_arr=array();
							if($txt_search_booking!='')
							{
								$sql_booking_query=sql_select("select b.job_no_mst, d.booking_no, d.fabric_color_id from wo_po_break_down b, wo_booking_dtls d where  d.po_break_down_id=b.id and d.booking_no like'%$txt_search_booking%'and b.status_active=1 and b.is_deleted=0 and d.status_active=1");

								$bookArray=sql_select($sql_booking_query); $booking_po_id_arr=array(); $booking_po_data_arr=array();
								foreach ($bookArray as $row)
								{
									$booking_po_id_arr[]="'".$row[csf('job_no_mst')]."'";
									$booking_po_data_arr[$row[csf("job_no_mst")]][$row[csf("fabric_color_id")]]['booking_no']=$row[csf("booking_no")];
								}
								$po_break_id_con = " and a..job_no in(".implode(',',$booking_po_id_arr).")";
								$booking_po_data_arr[$row[csf("job_no_mst")]][$row[csf("fabric_color_id")]]['booking_no'].=$row[csf("booking_no")].",";
							}
							if($db_type==0)
							{
								$select_fld= "year(a.insert_date)as year";
								$sql_query="Select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, group_concat(b.id) as po_id, sum(b.plan_cut*a.total_set_qnty) as po_quantity, $select_fld,
								sum(case when d.entry_form in (7,37,66,68) then d.quantity else 0 end) as receive_qnty,
								sum(case when d.entry_form in(14,15,134) and d.trans_type=5 then d.quantity else 0 end) as rec_trns_qnty,
								sum(case when d.entry_form in (18,71) then d.quantity else 0 end) as issue_qnty,
								sum(case when d.entry_form in (126,52) then d.quantity else 0 end) as issue_ret_qnty,
								sum(case when d.entry_form in(14,15,134) and d.trans_type=6 then d.quantity else 0 end) as issue_trns_qnty,
								d.color_id
								from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d
								where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1
								and d.entry_form in (7,37,66,68,14,15,18,71,126,134,52) and c.item_category=2 and c.status_active=1 and c.is_deleted=0 and c.id=d.trans_id and d.status_active=1 and d.is_deleted=0 and d.po_breakdown_id=b.id and
								a.company_name=$cbo_company_id $po_break_id_con $receive_date $buyer_id_cond $store_id_cond $shipment_id_cond $search_cond $year_cond
								group by a.job_no_prefix_num, a.job_no, a.insert_date, a.buyer_name, a.style_ref_no, a.total_set_qnty, d.color_id order by a.buyer_name, a.job_no";
							}

							else if($db_type==2)
							{
								$select_fld= "TO_CHAR(a.insert_date,'YYYY') as year";
								$sql_query="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, LISTAGG(b.id, ',') 	WITHIN GROUP (ORDER BY b.id) as po_id, sum(b.plan_cut*a.total_set_qnty) as po_quantity, $select_fld,
								sum(case when d.entry_form in (7,37,66,68) then d.quantity else 0 end) as receive_qnty,
								sum(case when d.entry_form in(14,15,134) and d.trans_type=5 then d.quantity else 0 end) as rec_trns_qnty,
								sum(case when d.entry_form in (18,71) then d.quantity else 0 end) as issue_qnty,
								sum(case when d.entry_form in (126,52) then d.quantity else 0 end) as issue_ret_qnty,
								sum(case when d.entry_form in(14,15,134) and d.trans_type=6 then d.quantity else 0 end) as issue_trns_qnty,d.color_id
								from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d
								where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1
								and d.entry_form in (7,37,66,68,14,15,18,71,126,134,52) and c.item_category=2 and c.status_active=1 and c.is_deleted=0 and c.id=d.trans_id and d.trans_id!=0 and d.status_active=1 and d.is_deleted=0 and d.po_breakdown_id=b.id  and
								a.company_name=$cbo_company_id $po_break_id_con $receive_date $buyer_id_cond $store_id_cond $shipment_id_cond $search_cond $year_cond
								group by a.job_no_prefix_num, a.job_no, a.insert_date, a.buyer_name, a.style_ref_no, a.total_set_qnty, d.color_id order by a.buyer_name, a.job_no";
							}

	                		//echo $sql_query;
							$i=1; $k=1;$total_rec_return_qnty=0;$total_issue_ret_qnty=0;$total_tran_in_qnty=0;$total_tran_out_qnty=0;
							$nameArray=sql_select($sql_query); $fin_color_array=array(); $fin_color_data_arr=array();$checkbuyerArr=array();
							if($txt_search_booking =='')
							{
								$po_break_id_arr=array();
								foreach ($nameArray as $row)
								{
									$po_break_id_arr[]="'".$row[csf('job_no')]."'";
									$color_id_arr[]=$row[csf('color_id')];
								}
								$sql_booking_po_query=sql_select("select b.job_no_mst, d.booking_no, d.fabric_color_id from wo_po_break_down b, wo_booking_dtls d where  d.po_break_down_id=b.id and b.job_no_mst in(".implode(',',$po_break_id_arr).") and d.fabric_color_id in(".implode(',',$color_id_arr).") and b.status_active=1 and b.is_deleted=0 and d.status_active=1");
								foreach ($sql_booking_po_query as $row)
								{
									$booking_po_data_arr[$row[csf("job_no_mst")]][$row[csf("fabric_color_id")]]['booking_no'].=$row[csf("booking_no")].",";
								}
							}

							foreach ($nameArray as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$dzn_qnty=0; $rec_qty=0; $orderToissueQty=0; $orderTorecQty=0; $book_qty=0; $issue_ret_qnty=0; $iss_qty=0; $actual_qty=0;$cons_per=0;$receive_ret_qnty=0;
								if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
								else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
								else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
								else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
								else $dzn_qnty=1;

								$color_id=$row[csf("color_id")];
								//echo 'PO ID='.$row[csf('po_id')].', Color='.$color_id=$row[csf("color_id")].'<br>';
								$po_ids=array_unique(explode(",",$row[csf('po_id')]));
								$rec_ret_qnty=0;
								foreach($po_ids as $po_id)
								{
									$book_qty+=$booking_qnty[$po_id][$color_id]['fin_fab_qnty'];
									$issue_ret_qnty+=$iss_return_qnty[$po_id][$color_id]['issue_ret_qnty'];
									$actual_qty+=$actual_cut_qnty[$po_id][$color_id]['actual_cut_qty'];

									$cons_per+=$result_consumtion[$po_id][$color_id];
									$rec_ret_qnty+=$rec_return_qnty[$po_id][$color_id]['rec_ret_qnty'];
								}
								$po_ids=implode(",",$po_ids);

								$trans_in_qty=$row[csf("rec_trns_qnty")];
								$rec_qty=($row[csf("receive_qnty")]);
								$trans_out=($row[csf("issue_trns_qnty")]);
								$iss_qty=$row[csf("issue_qnty")];
								$total_receive=$trans_in_qty+$rec_qty+$issue_ret_qnty;
								$total_issue=$trans_out+$iss_qty+$rec_ret_qnty;
								$stock=$total_receive-$total_issue;
								$stock = number_format($stock,2,'.','');

								if($value_for_search_by==2 && $stock > 0)
								{
									if(!in_array($row[csf("buyer_name")],$checkbuyerArr))
									{
										if($k!=1) // Buyer wise sum/total here------------
										{
											?>
											<tr class="tbl_bottom">
												<td colspan="6" align="right">Buyer Total</td>
												<td align="right"><? //echo number_format($po_quantity,2);$po_quantity=0; ?></td>
												<td></td>
												<td></td>
												<td><? echo number_format($subtot_book_qty,2); $subtot_book_qty=0;?></td>
												<td align="right"><? echo number_format($subtot_trns_in_qnty,2); $subtot_trns_in_qnty=0;?></td>
												<td align="right"><? echo number_format($subtot_rec_qty,2); $subtot_rec_qty=0;?></td>

												<td align="right"><? echo number_format($subtot_issue_ret_qnty,2); $subtot_issue_ret_qnty=0;?></td>
												<td align="right"><? echo number_format($subtot_total_receive,2); $subtot_total_receive=0;?></td>
												<td align="right"><? echo number_format($subtot_rec_bal,2); $subtot_rec_bal=0;?></td>
		                                        <td align="right"><?  echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
												<td align="right"><? echo number_format($subtot_rec_ret_qnty,2); $subtot_rec_ret_qnty=0;?></td>
												<td align="right"><? echo number_format($subtot_total_issue,2); $subtot_total_issue=0;?></td>
												<td align="right"><? echo number_format($subtot_issue_bal,2); $subtot_issue_bal=0;?></td>
												<td align="right"><? echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
												<td align="right"></td>
												<td align="right"><? echo number_format($subtot_possible_cut_pcs,2); $subtot_possible_cut_pcs=0;?></td>

												<td align="right"><? echo number_format($subtot_actual_qty,2); $subtot_actual_qty=0;?></td>
											</tr>
											<?
										}
										$checkbuyerArr[]=$row[csf("buyer_name")];
										$k++;
									}
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="70"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
										<td width="60" align="center"><p><? echo $row[csf("job_no_prefix_num")]; ?></p></td>
										<td width="100" align="center"><p><? echo chop($booking_po_data_arr[$row[csf("job_no")]][$color_id]['booking_no'],","); ?></p></td>
										<td width="50" align="center"><p><? echo $row[csf("year")]; ?></p></td>
										<td width="110" style="word-break:break-all"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
										<td width="90" align="right"><p><? $po_quantity+=$row[csf("po_quantity")];echo $row[csf("po_quantity")]; ?>&nbsp;</p></td>
										<td width="60" align="center"><a href='#report_details' onClick="openmypage_ex_factory('<? echo $po_ids; ?>','2');">View</a></td>
										<td width="110" style="word-break:break-all"><p><? echo $color_arr[$color_id]; ?></p></td>
										<td width="90" align="right"><p><? $subtot_book_qty+=$book_qty;echo number_format($book_qty,2,'.','');//$booking_qty,2,'.',''); ?>&nbsp;</p></td>
										<?

										$subtot_issue_ret_qnty+=$issue_ret_qnty;
										$subtot_trns_in_qnty+=$trans_in_qty;
										$subtot_trns_out_qnty+=$trans_out;
										$subtot_iss_qty+=$iss_qty;
										$subtot_rec_ret_qnty+=$rec_ret_qnty;

										$subtot_total_receive+=$total_receive;
										$subtot_total_issue+=$total_issue;
										//$iss_qty_cal=$row[csf("issue_qnty")]+$rec_return_qnty[$row[csf('po_id')]][$row[csf('color_id')]]['rec_ret_qnty']+$row[csf("issue_trns_qnty")];
										$subtot_stock+=$stock;
										$subtot_rec_qty+=$rec_qty;
										?>
										<td width="80" title="Trans. In" align="right">
											<a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',55,'trans_in_popup');">
												<? echo number_format($trans_in_qty,2,'.',''); ?>
											</a>
										</td>
										<td width="80" align="right"><? echo number_format($rec_qty,2,'.',''); ?></td>
										<td width="70" align="right">
											<a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',2,'issue_ret_popup');">
												<? echo number_format($issue_ret_qnty,2,'.',''); ?>
											</a>
										</td>
										<td width="80" align="right">
											<a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',1,'receive_popup');"><? echo number_format($total_receive,2,'.',''); ?>
											</a>
										</td>

										<td width="80" align="right"><p><? $rec_bal=$book_qty-$total_receive; $subtot_rec_bal+=$rec_bal;echo number_format($rec_bal,2,'.',''); ?></p></td>
										<td width="80" align="right">
											<a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',55,'trans_in_popup');">
												<? echo number_format($trans_out,2,'.',''); ?>
											</a>
										</td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',3,'issue_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"> <? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><? $issue_bal=$book_qty-$iss_qty; $subtot_issue_bal+=$issue_bal;echo number_format($issue_bal,2,'.',''); ?></p></td>

										<td width="70" align="right">
										<a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',4,'receive_ret_popup');">
											<? echo number_format($rec_ret_qnty,2,'.',''); ?>
										</a>
										<td width="80" align="right"><? echo number_format($total_issue,2,'.',''); ?></td>
										<td width="80" align="right" title="<? echo "Receive: ".$row[csf("receive_qnty")]."##".$issue_ret_qnty."##".$row[csf("rec_trns_qnty")]."Issue: ".$row[csf("issue_qnty")]."##".$receive_ret_qnty."##".$row[csf("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',5,'knit_stock_popup');"><? echo number_format($stock,2,'.',''); ?></a></p></td>
										<td width="100" align="right"><p><? echo number_format($cons_per,8,'.',''); ?></p></td>
										<td width="80" align="right"><p><? $possible_cut_pcs=$iss_qty_cal/($cons_per); $subtot_possible_cut_pcs+=$possible_cut_pcs;echo number_format($possible_cut_pcs); ?></p></td>
										<td width="" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>','actual_cut_popup');"><? echo $actual_qty;$subtot_actual_qty+=$actual_qty; ?></a>&nbsp;</p></td>
									</tr>
									<?
									$i++;
									$total_order_qnty+=$row[csf("po_quantity")];
									$rec_trns_in_qnty+=$trans_in_qty;
									$total_req_qty+=$book_qty;
									$total_rec_qty+=$rec_qty;
									$total_receive_qnty+=$total_receive;
									$total_rec_bal+=$rec_bal;
									$total_issue_bal+=$issue_bal;
									$issue_trns_out_qnty+=$trans_out;
									$total_issue_qty+=$iss_qty;
									$total_issue_quantity+=$total_issue;
									$total_stock+=$stock;
									$total_possible_cut_pcs+=$possible_cut_pcs;
									$total_actual_cut_qty+=$actual_qty;
									$total_rec_return_qnty+=$receive_ret_qnty;
									$total_issue_ret_qnty+=$issue_ret_qnty;
								}
								else if($value_for_search_by==1)
								{
									if(!in_array($row[csf("buyer_name")],$checkbuyerArr))
									{
										if($k!=1) // Buyer wise sum/total here------------
										{
											?>
											<tr class="tbl_bottom">
												<td colspan="6" align="right">Buyer Total</td>
												<td align="right"><? //echo number_format($po_quantity,2);$po_quantity=0; ?></td>
												<td></td>
												<td></td>
												<td><? echo number_format($subtot_book_qty,2); $subtot_book_qty=0;?></td>
												<td align="right"><? echo number_format($subtot_trns_in_qnty,2); $subtot_trns_in_qnty=0;?></td>
												<td align="right"><? echo number_format($subtot_rec_qty,2); $subtot_rec_qty=0;?></td>

												<td align="right"><? echo number_format($subtot_issue_ret_qnty,2); $subtot_issue_ret_qnty=0;?></td>
												<td align="right"><? echo number_format($subtot_total_receive,2); $subtot_total_receive=0;?></td>
												<td align="right"><? echo number_format($subtot_rec_bal,2); $subtot_rec_bal=0;?></td>
									            <td align="right"><?  echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
												<td align="right"><? echo number_format($subtot_total_issue,2); $subtot_total_issue=0;?></td>
												<td align="right"><? echo number_format($subtot_issue_bal,2); $subtot_issue_bal=0;?></td>

												<td align="right"><? echo number_format($subtot_rec_ret_qnty,2); $subtot_rec_ret_qnty=0;?></td>
												<td align="right"></td>
												<td align="right"><? echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
												<td align="right"><? echo number_format($total_issue_quantity,2); $total_issue_quantity=0;?></td>
												<td align="right"><? echo number_format($subtot_possible_cut_pcs,2); $subtot_possible_cut_pcs=0;?></td>

												<td align="right"><? echo number_format($subtot_actual_qty,2); $subtot_actual_qty=0;?></td>
											</tr>
											<?
										}
										$checkbuyerArr[]=$row[csf("buyer_name")];
										$k++;
									}
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="70"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
										<td width="60" align="center"><p><? echo $row[csf("job_no_prefix_num")]; ?></p></td>
										<td width="100" align="center"><p><? echo chop($booking_po_data_arr[$row[csf("job_no")]][$color_id]['booking_no'],","); ?></p></td>
										<td width="50" align="center"><p><? echo $row[csf("year")]; ?></p></td>
										<td width="110" style="word-break:break-all"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
										<td width="90" align="right"><p><? $po_quantity+=$row[csf("po_quantity")];echo $row[csf("po_quantity")]; ?>&nbsp;</p></td>
										<td width="60" align="center"><a href='#report_details' onClick="openmypage_ex_factory('<? echo $po_ids; ?>','2');">View</a></td>
										<td width="110" style="word-break:break-all"><p><? echo $color_arr[$color_id]; ?></p></td>
										<td width="90" align="right"><p><? $subtot_book_qty+=$book_qty;echo number_format($book_qty,2,'.','');//$booking_qty,2,'.',''); ?>&nbsp;</p></td>
										<?

										$subtot_issue_ret_qnty+=$issue_ret_qnty;
										$subtot_trns_in_qnty+=$trans_in_qty;
										$subtot_trns_out_qnty+=$trans_out;
										$subtot_iss_qty+=$iss_qty;
										$subtot_rec_ret_qnty+=$rec_ret_qnty;

										$subtot_total_receive+=$total_receive;
										$subtot_total_issue+=$total_issue;
										//$iss_qty_cal=$row[csf("issue_qnty")]+$rec_return_qnty[$row[csf('po_id')]][$row[csf('color_id')]]['rec_ret_qnty']+$row[csf("issue_trns_qnty")];
										$subtot_stock+=$stock;
										$subtot_rec_qty+=$rec_qty;
										?>
										<td width="80" title="Trans. In" align="right">
											<a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',55,'trans_in_popup');">
												<? echo number_format($trans_in_qty,2,'.',''); ?>
											</a>
										</td>
										<td width="80" align="right"><? echo number_format($rec_qty,2,'.',''); ?></td>
										<td width="70" align="right">
											<a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',2,'issue_ret_popup');">
												<? echo number_format($issue_ret_qnty,2,'.',''); ?>
											</a>
										</td>
										<td width="80" align="right">
											<a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',1,'receive_popup');"><? echo number_format($total_receive,2,'.',''); ?>
											</a>
										</td>

										<td width="80" align="right"><p><? $rec_bal=$book_qty-$total_receive; $subtot_rec_bal+=$rec_bal;echo number_format($rec_bal,2,'.',''); ?></p></td>
										<td width="80" align="right">
											<a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',55,'trans_in_popup');">
												<? echo number_format($trans_out,2,'.',''); ?>
											</a>
										</td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',3,'issue_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"> <? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><? $issue_bal=$book_qty-$iss_qty; $subtot_issue_bal+=$issue_bal;echo number_format($issue_bal,2,'.',''); ?></p></td>

										<td width="70" align="right" title="<? echo 'PO: '.$po_ids.', Color: '.$color_id; ?>">
										<a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',4,'receive_ret_popup');">
											<? echo number_format($rec_ret_qnty,2,'.',''); ?>
										</a></td>
										<td width="80" align="right"><? echo number_format($total_issue,2,'.',''); ?></td>
										<td width="80" align="right" title="<? echo "Receive: ".$row[csf("receive_qnty")]."##".$issue_ret_qnty."##".$row[csf("rec_trns_qnty")]."Issue: ".$row[csf("issue_qnty")]."##".$receive_ret_qnty."##".$row[csf("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',5,'knit_stock_popup');"><? echo number_format($stock,2,'.',''); ?></a></p></td>
										<td width="100" align="right"><p><? echo number_format($cons_per,8,'.',''); ?></p></td>
										<td width="80" align="right"><p><? $possible_cut_pcs=$iss_qty_cal/($cons_per); $subtot_possible_cut_pcs+=$possible_cut_pcs;echo number_format($possible_cut_pcs); ?></p></td>
										<td width="" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>','actual_cut_popup');"><? echo $actual_qty;$subtot_actual_qty+=$actual_qty; ?></a>&nbsp;</p></td>
									</tr>
									<?
									$i++;
									$total_order_qnty+=$row[csf("po_quantity")];
									$rec_trns_in_qnty+=$trans_in_qty;
									$total_req_qty+=$book_qty;
									$total_rec_qty+=$rec_qty;
									$total_receive_qnty+=$total_receive;
									$total_rec_bal+=$rec_bal;
									$total_issue_bal+=$issue_bal;
									$issue_trns_out_qnty+=$trans_out;
									$total_issue_qty+=$iss_qty;
									$total_issue_quantity+=$total_issue;
									$total_stock+=$stock;
									$total_possible_cut_pcs+=$possible_cut_pcs;
									$total_actual_cut_qty+=$actual_qty;
									$total_rec_return_qnty+=$receive_ret_qnty;
									$total_issue_ret_qnty+=$issue_ret_qnty;
								}

							}
							?>
							<tr class="tbl_bottom">
								<td colspan="6" align="right">Buyer Total</td>
								<td align="right"><? //echo number_format($po_quantity,2);$po_quantity=0; ?></td>
								<td></td>
								<td></td>
								<td><? echo number_format($subtot_book_qty,2); $subtot_book_qty=0;?></td>
								<td align="right"><? echo number_format($subtot_trns_in_qnty,2); $subtot_trns_in_qnty=0;?></td>
								<td align="right"><? echo number_format($subtot_rec_qty,2); $subtot_rec_qty=0;?></td>

								<td align="right"><? echo number_format($subtot_issue_ret_qnty,2); $subtot_issue_ret_qnty=0;?></td>
								<td align="right"><? echo number_format($subtot_total_receive,2); $subtot_total_receive=0;?></td>
								<td align="right"><? echo number_format($subtot_rec_bal,2); $subtot_rec_bal=0;?></td>
								<td align="right"><?  echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
						        <td align="right"><? echo number_format($subtot_total_issue,2); $subtot_total_issue=0;?></td>
						        <td align="right"><? echo number_format($subtot_issue_bal,2); $subtot_issue_bal=0;?></td>

								<td align="right"><? echo number_format($subtot_rec_ret_qnty,2); $subtot_rec_ret_qnty=0;?></td>
								<td align="right"><? echo number_format($total_issue_quantity,2); $total_issue_quantity=0;?></td>
								<td align="right"><? echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
								<td align="right"></td>
								<td align="right"><? echo number_format($subtot_possible_cut_pcs,2); $subtot_possible_cut_pcs=0;?></td>

								<td align="right"><? echo number_format($subtot_actual_qty,2); $subtot_actual_qty=0;?></td>
							</tr>
						</table>
					</div>
					<table width="1900" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
						<tfoot>
							<th width="30"></th>
							<th width="70"></th>
							<th width="60"></th>
							<th width="100"></th>
							<th width="50"></th>
							<th width="110">Grand Total</th>
							<th width="90" align="right" id=""><? //echo number_format($total_order_qnty,2,'.',''); ?></th>
							<th width="60"></th>
							<th width="110">&nbsp;</th>
							<th width="90" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
	                        <th width="80" align="right" id="value_total_tranin_qty"><? echo number_format($rec_trns_in_qnty,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
							<th width="70" align="right" id="value_total_issue_ret_qty"><? echo number_format($total_issue_ret_qnty,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_receive_qty"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>
	                        <th width="80" align="right" id="value_total_tranout_qty"><? echo number_format($issue_trns_out_qnty,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_issue_balance_qty"><? echo number_format($total_issue_bal,2,'.',''); ?></th>
							<th width="70"  id="value_recv_ret_qty"><? echo number_format($total_rec_return_qnty,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_issue_quantity"><? echo number_format($total_issue_quantity,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
							<th width="100">&nbsp;</th>
							<th width="80" align="right" id="total_possible_cut_pcs"><? echo number_format($total_possible_cut_pcs,0,'.',''); ?></th>
							<th width="" align="right" id="total_actual_cut_qty"><? echo $total_actual_cut_qty; ?></th>
						</tfoot>
					</table>
				</fieldset>
				<?
			}
		}
		else if($cbo_report_type==2) // Woven Finish Start
		{
			$search_cond ="";
			if ($txt_search_comm !="")
			{
				if($cbo_search_by==1)
				{
					$search_cond=" and a.job_no_prefix_num in ($txt_search_comm) ";
				}
				elseif($cbo_search_by==2)
				{
					$search_cond=" and a.style_ref_no like '%$txt_search_comm%'";
				}
				elseif($cbo_search_by==3)
				{
					$search_cond=" and b.po_number like '%$txt_search_comm%'";
				}
				elseif($cbo_search_by==4)
				{
					$search_cond=" and b.file_no like '%$txt_search_comm%'";
				}
				elseif($cbo_search_by==2)
				{
					$search_cond=" and b.grouping like '%$txt_search_comm%'";
				}
			}

			if($cbo_year){
				if($db_type==0)
				{
					$year_cond=" and year(a.insert_date) in($cbo_year)";
				}
				else
				{
					$year_cond=" and TO_CHAR(a.insert_date,'YYYY') in($cbo_year)";
				}
			}

			$booking_qnty=array();
			if($txt_search_booking!="")
			{
				$sql_booking=sql_select("select b.po_break_down_id,b.fabric_color_id, sum(b.fin_fab_qnty ) as fin_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=3 and a.booking_type=1 and a.company_id=$cbo_company_id and a.booking_no like '%".$txt_search_booking."%' and a.is_deleted=0 and a.status_active=1 and b.booking_type=1 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,b.fabric_color_id");
				foreach( $sql_booking as $row_book)
				{
					$booking_qnty[$row_book[csf('po_break_down_id')]][$row_book[csf('fabric_color_id')]]['fin_fab_qnty']=$row_book[csf('fin_fab_qnty')];
					$booking_po_arr[$row_book[csf('po_break_down_id')]] =$row_book[csf('po_break_down_id')];
				}

				if(!empty($booking_po_arr))
				{
					$poCond=$booking_po_cond="";
					if($db_type==2 && count($booking_po_arr)>999)
					{
						$booking_po_arr_chunk=array_chunk($booking_po_arr,999) ;
						foreach($booking_po_arr_chunk as $chunk_arr)
						{
							$poCond.=" b.id in(".implode(",",$chunk_arr).") or ";
						}

						$booking_po_cond.=" and (".chop($poCond,'or ').")";

					}
					else
					{
						$booking_pos = implode(",", $booking_po_arr);
						$booking_po_cond=" and b.id in($booking_pos)";
					}
				}
			}

			if($db_type==0){
				$select_year = " year(a.insert_date) as year";
			}else{
				$select_year = " TO_CHAR(a.insert_date,'YYYY') as year";
			}

			$sql_query = "select a.id, a.company_name, a.job_no_prefix_num, a.job_no, $select_year, a.buyer_name, a.style_ref_no, a.total_set_qnty, b.id as po_id, b.po_number, b.plan_cut as po_quantity, b.shiping_status, sum( e.quantity ) as receive_qnty, e.color_id,b.shipment_date
			 from wo_po_details_master a, wo_po_break_down b,inv_receive_master d,inv_transaction c, order_wise_pro_details e
			 where a.job_no=b.job_no_mst and e.po_breakdown_id=b.id and d.id=c.mst_id and c.id=e.trans_id and d.entry_form=e.entry_form and e.entry_form in (17,209) and e.trans_id!=0 and d.entry_form in (17,209) and d.item_category=3
			 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.company_name=$cbo_company_id $buyer_id_cond $receive_date $store_id_cond $search_cond $year_cond $booking_po_cond
			 group by a.id, a.company_name, a.job_no_prefix_num, a.job_no, a.insert_date, a.buyer_name, a.style_ref_no, a.total_set_qnty, b.id, b.po_number, e.color_id, b.plan_cut, b.shiping_status,b.shipment_date
			 union all
			 select a.id,a.company_name, a.job_no_prefix_num, a.job_no, $select_year, a.buyer_name, a.style_ref_no,a.total_set_qnty, b.id as po_id, b.po_number, b.plan_cut as po_quantity, b.shiping_status, sum(d.quantity) as receive_qnty, d.color_id,b.shipment_date
			from  wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d
			where a.job_no=b.job_no_mst and b.id = d.po_breakdown_id and c.id = d.trans_id and d.trans_type=5 and d.entry_form = 258 and a.company_name=$cbo_company_id $buyer_id_cond $receive_date $store_id_cond $search_cond $year_cond $booking_po_cond and c.status_active=1 and d.status_active =1
			group by a.id, a.company_name, a.job_no_prefix_num, a.job_no, a.insert_date, a.buyer_name, a.style_ref_no, a.total_set_qnty, b.id, b.po_number, d.color_id, b.plan_cut, b.shiping_status,b.shipment_date
			order by buyer_name	";

			//echo $sql_query;die;

			$con = connect();
			$r_id2=execute_query("delete from tmp_poid where userid=$user_id ");
				if($r_id2)
			{
				oci_commit($con);
			}

			$recv_tran_in = sql_select($sql_query);
			foreach ($recv_tran_in as $row)
			{
				$po_data_arr[$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('color_id')]]['quantity'] += $row[csf('receive_qnty')];
				$job_data_arr[$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('color_id')]]['quantity'] += $row[csf('receive_qnty')];

				if($po_reference[$row[csf('po_id')]] == "")
				{
					$po_reference[$row[csf('po_id')]] = $row[csf('job_no')]."##".$row[csf('job_no_prefix_num')]."##".$row[csf('style_ref_no')]."##".$row[csf('po_number')]."##".$row[csf('po_quantity')]."##".$row[csf('shiping_status')]."##".$row[csf('year')]."##".$row[csf('shipment_date')];
					$job_reference[$row[csf('job_no')]]["job_quantity"] 		+=  $row[csf('po_quantity')]*$row[csf('total_set_qnty')];
					$job_reference[$row[csf('job_no')]]["year"] 				=  $row[csf('year')];
					$job_reference[$row[csf('job_no')]]["style_ref_no"] 		=  $row[csf('style_ref_no')];
					$job_reference[$row[csf('job_no')]]["job_no_prefix_num"] 	=  $row[csf('job_no_prefix_num')];
					$job_reference[$row[csf('job_no')]]["po_ids"] 				.=  $row[csf('po_id')].",";

					$r_id=execute_query("insert into tmp_poid (userid, poid, type) values ($user_id,".$row[csf('po_id')].",333)");
					if($r_id)
					{
						$r_id=1;
					}
					else
					{
						echo "insert into tmp_poid (userid, poid, type) values ($user_id,".$row[csf('po_id')].",333)";
						$r_id2=execute_query("delete from tmp_poid where userid=$user_id ");
						oci_rollback($con);
						die;
					}

				}
			}

			if($r_id)
			{
				oci_commit($con);
			}
			else
			{
				oci_rollback($con);
				disconnect($con);
			}

			$total_issue_sql = sql_select("select d.po_breakdown_id,d.color_id, sum(d.quantity) as issue_qnty from inv_transaction c, order_wise_pro_details d, tmp_poid e where c.id=d.trans_id and d.po_breakdown_id= e.poid and e.userid= $user_id and e.type=333 and c.item_category=3 and c.company_id=$cbo_company_id and c.transaction_type in (2,3,6) and d.entry_form in (19,202,258) and c.status_active=1 and d.status_active =1 $receive_date group by d.po_breakdown_id, d.color_id");

			foreach( $total_issue_sql as $row )
			{
				$issue_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['issue_qnty'] +=$row[csf('issue_qnty')];
			}
			unset($total_issue_sql);

			/*echo "<pre>";
			print_r($issue_qnty_arr);
			die;*/

			if(empty($booking_qnty))
			{
				$sql_booking=sql_select("select b.po_break_down_id,b.fabric_color_id, sum(b.fin_fab_qnty ) as fin_fab_qnty  from wo_booking_mst a, wo_booking_dtls b, tmp_poid e where a.booking_no=b.booking_no and b.po_break_down_id=e.poid and e.userid=$user_id and e.type=333 and a.item_category=3 and a.booking_type=1 and a.is_deleted=0 and a.status_active=1 and b.booking_type=1 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,b.fabric_color_id");
				foreach( $sql_booking as $row_book)
				{
					$booking_qnty[$row_book[csf('po_break_down_id')]][$row_book[csf('fabric_color_id')]]['fin_fab_qnty']=$row_book[csf('fin_fab_qnty')];
				}
			}


			$actual_cut_qnty=array();
			$sql_actual_cut_qty=sql_select(" select a.po_break_down_id,c.color_number_id, sum(b.production_qnty) as actual_cut_qty  from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, tmp_poid e where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=1 and a.po_break_down_id=e.poid and e.userid=$user_id and e.type=333 and a.is_deleted=0 and a.status_active=1  group by  a.po_break_down_id,c.color_number_id");
			foreach( $sql_actual_cut_qty as $row_actual)
			{
				$actual_cut_qnty[$row_actual[csf('po_break_down_id')]][$row_actual[csf('color_number_id')]]['actual_cut_qty']=$row_actual[csf('actual_cut_qty')];
			}

			$result_consumtion=array();
			$sql_consumtiont_qty=sql_select(" select b.job_no, b.po_break_down_id, b.color_number_id, sum( b.cons ) / count( b.color_number_id ) AS conjunction, pcs   from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a, tmp_poid e WHERE a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=e.poid and e.userid=$user_id and e.type=333 GROUP BY b.po_break_down_id, b.color_number_id, pcs, b.job_no ");
			$con_avg=0;
			foreach($sql_consumtiont_qty as $row_consum)
			{
				$con_avg= $row_consum[csf("conjunction")];///str_replace("'","",$row_sew[csf("pcs")]);
				$con_per_pcs=$con_avg/$row_consum[csf("pcs")];
				$result_consumtion[$row_consum[csf('job_no')]][$row_consum[csf('po_break_down_id')]][$row_consum[csf('color_number_id')]]['consum']=$con_per_pcs;
			}

			$r_id2=execute_query("delete from tmp_poid where userid=$user_id ");
				if($r_id2)
			{
				oci_commit($con);
			}

	        if($cbo_presentation==1)
			{
				$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
				?>
				<fieldset style="width:1480px;">
					<table cellpadding="0" cellspacing="0" width="1340">
						<tr  class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
						</tr>
						<tr  class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
						</tr>
						<tr  class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from_st)) ;?></strong></td>
						</tr>
					</table>
					<table width="1460" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
						<thead>
							<th width="30">SL</th>
							<th width="100">Buyer</th>
							<th width="50">Job Year</th>
							<th width="50">Job</th>
							<th width="100">Order</th>
							<th width="100">Style</th>
							<th width="80">Order Qty (Pcs)</th>
							<th width="60">Ex-factory</th>
							<th width="80">Shipping Status</th>
							<th width="80">Shipment Date</th>
							<th width="90">Color</th>

							<th width="80">Req. Qty</th>
							<th width="80" style="word-break: break-all;">Total Received</th>
							<th width="80">Received Balance</th>
							<th width="80">Total Issued</th>

							<th width="80">Stock</th>
							<th width="80">Consumption</th>
							<th width="80">Possible Cut Pcs.</th>
							<th>Actual Cut</th>
						</thead>
					</table>
					<div style="width:1480px; max-height:350px; overflow-y:scroll;" id="scroll_body">
						<table width="1460" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
							<?
							$i=1;
							foreach ($po_data_arr as $buyer_id => $buyer_data)
							{
								foreach ($buyer_data as $po_id => $po_id_data)
								{
									foreach ($po_id_data as $color_id => $row)
									{
										$po_ref_data = explode("##", $po_reference[$po_id]);
										$job_number 		= $po_ref_data[0];
										$job_no_prefix_num 	= $po_ref_data[1];
										$style_ref_no 		= $po_ref_data[2];
										$po_number 			= $po_ref_data[3];
										$poQnty 			= $po_ref_data[4];
										$shiping_status 	= $po_ref_data[5];
										$job_year       	= $po_ref_data[6];
										$ship_date       	= $po_ref_data[7];


										$dzn_qnty=0;
				                    	if($costing_per_id_library[$job_number]==1)
				                    	{
				                    		$dzn_qnty=12;
				                    	}
				                    	else if($costing_per_id_library[$job_number]==3)
				                    	{
				                    		$dzn_qnty=12*2;
				                    	}
				                    	else if($costing_per_id_library[$job_number]==4)
				                    	{
				                    		$dzn_qnty=12*3;
				                    	}
				                    	else if($costing_per_id_library[$job_number]==5)
				                    	{
				                    		$dzn_qnty=12*4;
				                    	}
				                    	else
				                    	{
				                    		$dzn_qnty=1;
				                    	}

				                    	$book_qty=$booking_qnty[$po_id][$color_id]['fin_fab_qnty'];
				                    	$iss_qty=$issue_qnty_arr[$po_id][$color_id]['issue_qnty'];

										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30"><? echo $i; ?></td>
											<td width="100" align="center"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
											<td width="50" align="center"><p><? echo $job_year ; ?></p></td>
											<td width="50" align="center"><p><? echo $job_no_prefix_num; ?></p></td>
											<td width="100" align="center"><p><? echo $po_number; ?></p></td>
											<td width="100" align="center"><p><? echo $style_ref_no; ?></p></td>
											<td width="80" align="right"><p><? echo number_format($poQnty,2,'.',''); ?>&nbsp;</p></td>
											<td width="60" align="center"><a href='#report_details' onClick="openmypage_ex_factory('<? echo $po_id; ?>','1');">View</a></td>
											<td width="80" align="center"><p><? echo $shipment_status[$shiping_status]; ?></p></td>
											<td width="80" align="center"><p><? echo change_date_format($ship_date); ?></p></td>
											<td width="90" align="center"><p><? echo $color_arr[$color_id]; ?></p></td>
											<td width="80" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>
			                                <td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_id; ?>','','<? echo $color_id; ?>',0,'woven_receive_popup','<? echo $cbo_store_id; ?>',<? echo $txt_date_from_st;?>);"><? echo number_format($row["quantity"],2,'.',''); ?></a></p></td>
			                                <td width="80" align="right">
			                                	<p>&nbsp;
			                                		<?
					                                	$rec_bal=$book_qty-$row["quantity"];
					                                	echo number_format($rec_bal,2,'.','');
					                                ?>
			                             		</p>
			                             	</td>
			                                <td width="80" align="right">
			                                	<p>
			                                		<a href='#report_details' onClick="openmypage('<? echo $po_id; ?>','','<? echo $color_id; ?>',2,'woven_issue_popup','<? echo $cbo_store_id; ?>',<? echo $txt_date_from_st;?>);">
			                                			<?
															echo number_format($iss_qty,2,'.','');
														?>
													</a>&nbsp;
												</p>
											</td>
			                                <td width="80" align="right">
			                                	<p>&nbsp;
			                                		<?
					                                	$stock=$row["quantity"]-$iss_qty;
					                                	echo number_format($stock,2,'.','');
					                                ?>
			                            		</p>
			                            	</td>
			                                <td width="80" align="center"><p><? $cons_per=$result_consumtion[$job_number][$po_id][$color_id]['consum']; echo number_format($cons_per,4,'.',''); ?></p></td>
			                                <td width="80" align="right">
			                                	<p>
			                                	<?
			                                	$act_issue = $issue_qnty_arr[$po_id][$color_id]['issue_qnty'];
			                                	$possible_cut_pcs=$act_issue/($cons_per/$dzn_qnty);
			                                	echo number_format($possible_cut_pcs,0,'.',''); ?>

			                                	</p>
			                                </td>
			                                <td width="" align="right">
			                                	<p>
			                                		<a href='#report_details' onClick="openmypage('<? echo $po_id; ?>','<? //echo $row[csf('prod_id')]; ?>','<? echo $color_id; ?>',4,'woven_actual_cut_popup','<? echo $cbo_store_id; ?>');">
			                                			<?
			                                				$actual_qty=$actual_cut_qnty[$po_id][$color_id]['actual_cut_qty'];
			                                				echo number_format($actual_qty,0,'.','');
			                                			?>
			                                		</a>&nbsp;
			                                	</p>
			                                </td>
			                        	</tr>
			                            <?
			                            $i++;

			                            if($chk_order[$po_id] =="")
			                            {
			                            	$po_quantity+=$poQnty;
			                            	$total_order_qnty+=$poQnty;
			                            	$chk_order[$po_id]= $po_id;
			                            }

			                            $subtot_book_qty+=$book_qty;
			                            $subtot_rec_qty+=$row["quantity"];
					                    $subtot_rec_bal+=$rec_bal;
					                    $subtot_iss_qty+=$iss_qty;
					                    $subtot_stock+=$stock;
					                    $subtot_possible_cut_pcs+=$possible_cut_pcs;
					                    $subtot_actual_qty+=$actual_qty;

			                            $total_req_qty+=$book_qty;
			                            $total_rec_qty+=$row["quantity"];
			                            $total_rec_bal+=$rec_bal;
			                            $total_issue_qty+=$iss_qty;
			                            $total_stock+=$stock;
			                            $total_possible_cut_pcs+=$possible_cut_pcs;
			                            $total_actual_cut_qty+=$actual_qty;

									}
								}
								?>
								<tr class="tbl_bottom">
									<td colspan="6" align="right">Buyer Total</td>
									<td align="right"><? echo number_format($po_quantity,2);$po_quantity=0; ?></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"></td>

									<td align="right"><? echo number_format($subtot_book_qty,2); $subtot_book_qty=0;?></td>
									<td align="right"><? echo number_format($subtot_rec_qty,2); $subtot_rec_qty=0;?></td>
									<td align="right"><? echo number_format($subtot_rec_bal,2); $subtot_rec_bal=0;?></td>
									<td align="right"><? echo number_format($subtot_iss_qty,2); $subtot_iss_qty=0;?></td>
									<td align="right"><? echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
									<td align="right"><? //echo number_format($subtot_issue_ret_qnty,2); $subtot_issue_ret_qnty=0;?></td>
									<td align="right"><? echo number_format($subtot_possible_cut_pcs,2); $subtot_possible_cut_pcs=0;?></td>
									<td align="right"> <? echo number_format($subtot_actual_qty,2); $subtot_actual_qty=0;?></td>
								</tr>
								<?
							}
	                        ?>
	                    </table>
	                    <table width="1460" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
	                    	<tfoot>
	                    		<th width="30"></th>
	                    		<th width="100"></th>
	                    		<th width="50"></th>
	                    		<th width="50"></th>
	                    		<th width="100"></th>
	                    		<th width="100">Grand Total</th>
	                    		<th width="80" align="right" id=""><? echo number_format($total_order_qnty,2,'.',''); ?></th>
	                    		<th width="60">&nbsp;</th>
	                    		<th width="80">&nbsp;</th>
	                    		<th width="80">&nbsp;</th>
	                    		<th width="90">&nbsp;</th>
	                    		<th width="80" align="right" id="total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
	                    		<th width="80" align="right" id="total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
	                    		<th width="80" align="right" id="total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>
	                    		<th width="80" align="right" id="total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
	                    		<th width="80" align="right" id="total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
	                    		<th width="80">&nbsp;</th>
	                    		<th width="80" align="right" id="total_possible_cut_pcs"><? echo number_format($total_possible_cut_pcs,0,'.',''); ?></th>
	                    		<th width="" align="right" id="total_actual_cut_qty"><? echo $total_actual_cut_qty; ?></th>
	                    	</tfoot>
	                    </table>
	                </div>
	            </fieldset>
	            <?
	        }
	        else
	        {
	        	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	        	?>
	        	<fieldset style="width:1322px;">
	        		<table cellpadding="0" cellspacing="0" width="1300">
	        			<tr class="form_caption" style="border:none;">
	        				<td align="center" width="100%" colspan="13" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
	        			</tr>
	        			<tr  class="form_caption" style="border:none;">
	        				<td align="center" width="100%" colspan="13" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
	        			</tr>
	        			<tr  class="form_caption" style="border:none;">
	        				<td align="center" width="100%" colspan="13" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from_st)) ;?></strong></td>
	        			</tr>
	        		</table>
	        		<table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	        			<thead>
	        				<th width="30">SL</th>
	        				<th width="70">Buyer</th>
	        				<th width="60">Job</th>
	        				<th width="50">Year</th>
	        				<th width="110">Style</th>
	        				<th width="90">Job Qty. (Pcs)</th>
	        				<th width="60">Order Status</th>
	        				<th width="90">Fin. Color</th>

	        				<th width="90">Req. Qty</th>
	        				<th width="90" title="Rec.+Issue Ret.+Trans. in">Total Received</th>
	        				<th width="90" title="Req.-Totat Rec.">Received Balance</th>
	        				<th width="90" title="Issue+Rec. Ret.+Trans. out">Total Issued</th>

	        				<th width="90" title="Total Rec.- Total Issue">Stock</th>
	        				<th width="100">Consumption Pcs.</th>
	        				<th width="80">Possible Cut Pcs.</th>
	        				<th>Actual Cut</th>
	        			</thead>
	        		</table>
	        		<div style="width:1320px; max-height:350px; overflow-y:scroll;" id="scroll_body">
	        			<table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
	        				<?
	        				/*echo "<pre>";
	        				print_r($job_reference);
	        				die;*/

	        				$i=1;
							foreach ($job_data_arr as $buyer_id => $buyer_data)
							{
								foreach ($buyer_data as $job_no => $job_no_data)
								{
									foreach ($job_no_data as $color_id => $row)
									{
										$job_quantity 		= $job_reference[$job_no]["job_quantity"];
										$style_ref_no 		= $job_reference[$job_no]["style_ref_no"];
										$job_no_prefix_num 	= $job_reference[$job_no]["job_no_prefix_num"];
										$year 				= $job_reference[$job_no]["year"];

										$dzn_qnty=0;
			        					if($costing_per_id_library[$row[csf('job_no')]]==1)
			        					{
			        						$dzn_qnty=12;
			        					}
			        					else if($costing_per_id_library[$row[csf('job_no')]]==3)
			        					{
			        						$dzn_qnty=12*2;
			        					}
			        					else if($costing_per_id_library[$row[csf('job_no')]]==4)
			        					{
			        						$dzn_qnty=12*3;
			        					}
			        					else if($costing_per_id_library[$row[csf('job_no')]]==5)
			        					{
			        						$dzn_qnty=12*4;
			        					}
			        					else
			        					{
			        						$dzn_qnty=1;
			        					}

			        					$book_qty=0; $iss_qty=0; $cons_per=0;
			        					$po_ids=array_unique(explode(",",chop($job_reference[$job_no]["po_ids"],",")));
			        					foreach($po_ids as $po_id)
			        					{
			        						$book_qty+=$booking_qnty[$po_id][$color_id]['fin_fab_qnty'];
			        						$iss_qty+=$issue_qnty_arr[$po_id][$color_id]['issue_qnty'];
			        						$cons_per+=$result_consumtion[$job_no][$po_id][$color_id]['consum'];
			        						$actual_qty +=$actual_cut_qnty[$po_id][$color_id]['actual_cut_qty'];
			        					}
			        					$po_ids=implode(",",$po_ids);

			        					?>
											<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
												<td width="30"><? echo $i; ?></td>
												<td width="70"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
												<td width="60" align="center"><p><? echo $job_no_prefix_num; ?></p></td>
												<td width="50" align="center"><p><? echo $year; ?></p></td>
												<td width="110"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
												<td width="90" align="right"><p><? echo $job_quantity; ?>&nbsp;</p></td>
												<td width="60" align="center"><a href='#report_details' onClick="openmypage_ex_factory('<? echo $po_ids; ?>','2');">View</a></td>
												<td width="90"><p><? echo $color_arr[$color_id]; ?></p></td>
												<td width="90" align="right"><p><? echo number_format($book_qty,2,'.','');?>&nbsp;</p></td>
												<?
			                                //$rec_qty=$row["quantity"];
			                                ?>

			                                <td width="90" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',0,'woven_receive_popup','<? echo $cbo_store_id; ?>',<? echo $txt_date_from_st;?>);"><? echo number_format($row["quantity"],2,'.',''); ?></a>&nbsp;</p></td>
			                                <td width="90" align="right">
			                                	<p>
			                                		<?
			                                			$rec_bal=$book_qty-$row["quantity"];
			                                			echo number_format($rec_bal,2,'.','');
			                                		?>&nbsp;
			                                	</p>
			                                </td>
			                                <td width="90" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',2,'woven_issue_popup','<? echo $cbo_store_id; ?>',<? echo $txt_date_from_st;?>);"><? echo number_format($iss_qty,2,'.',''); ?></a>&nbsp;</p></td>
			                                <td width="90" align="right">
			                                	<p>
			                                		<?
			                                			$stock=$row["quantity"]-$iss_qty;
			                                			echo number_format($stock,2,'.','');
			                                		?>&nbsp;
			                                	</p>
			                                </td>
			                                <td width="100" align="right"><p><? echo number_format($cons_per,4,'.',''); ?></p></td>
			                                <td width="80" align="right">
			                                	<p>
			                                		<?
			                                		$possible_cut_pcs=$iss_qty/($cons_per/$dzn_qnty);
			                                		echo number_format($possible_cut_pcs,0,'.',''); ?>
			                                	</p>
			                                </td>
			                                <td width="" align="right">
			                                	<p>
			                                	<a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',4,'woven_actual_cut_popup','<? echo $cbo_store_id; ?>')">
			                                		<? echo $actual_qty; ?>
			                                	</a>&nbsp;
			                                	</p>
			                            	</td>
			                            </tr>
			                            <?

			                            $po_quantity+=$job_quantity;
			                            $subtot_book_qty+=$book_qty;
			                            $subtot_rec_qty+=$row["quantity"];
			                            $subtot_rec_bal+=$rec_bal;
			                            $subtot_iss_qty+=$iss_qty;
			                            $subtot_stock+=$stock;
			                            $subtot_possible_cut_pcs+=$possible_cut_pcs;
			                            $subtot_actual_qty+=$actual_qty;

			                            $total_order_qnty+=$job_quantity;
			                            $total_req_qty+=$book_qty;
			                            $total_rec_qty+=$row["quantity"];
			                            $total_rec_bal+=$rec_bal;
			                            $total_issue_qty+=$iss_qty;
			                            $total_stock+=$stock;
			                            $total_possible_cut_pcs+=$possible_cut_pcs;
			                            $total_actual_cut_qty+=$actual_qty;

									}
								}
								?>
								<tr class="tbl_bottom">
									<td colspan="5" align="right">Buyer Total</td>
									<td align="right"><? echo number_format($po_quantity,2);$po_quantity=0; ?></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"><? echo number_format($subtot_book_qty,2); $subtot_book_qty=0;?></td>
									<td align="right"><? echo number_format($subtot_rec_qty,2); $subtot_rec_qty=0;?></td>
									<td align="right"><? echo number_format($subtot_rec_bal,2); $subtot_rec_bal=0;?></td>
									<td align="right"><? echo number_format($subtot_iss_qty,2); $subtot_iss_qty=0;?></td>
									<td align="right"><? echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
									<td align="right"><? //echo number_format($subtot_issue_ret_qnty,2); $subtot_issue_ret_qnty=0;?></td>
									<td align="right"><? echo number_format($subtot_possible_cut_pcs,2); $subtot_possible_cut_pcs=0;?></td>
									<td align="right"> <? echo number_format($subtot_actual_qty,2); $subtot_actual_qty=0;?></td>
								</tr>
								<?
							}

	        				/*if($db_type==0)
	        				{
	        					$sql_query="Select a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, sum(a.total_set_qnty) as ratio, group_concat(b.id) as po_id, sum(b.plan_cut*a.total_set_qnty) as po_quantity, year(a.insert_date) as year, sum( c.cons_quantity ) as receive_qnty,  e.color_id
	        					from wo_po_details_master a, wo_po_break_down b,inv_receive_master d, inv_transaction c, order_wise_pro_details e
	        					where a.job_no=b.job_no_mst and e.po_breakdown_id=b.id and  d.id=c.mst_id and c.id=e.trans_id and d.entry_form=e.entry_form and e.entry_form in (17) and e.trans_id!=0 and d.entry_form in (17)  and d.item_category=3 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and
	        					a.company_name=$cbo_company_id $receive_date $buyer_id_cond $store_id_cond $search_cond group by a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, e.color_id, a.insert_date, a.total_set_qnty order by a.buyer_name, a.job_no";
	        				}
	        				else if($db_type==2)
	        				{
	        					$sql_query="Select a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, sum(a.total_set_qnty) as ratio, LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as po_id, sum(b.plan_cut*a.total_set_qnty) as po_quantity, TO_CHAR(a.insert_date,'YYYY') as year, sum( c.cons_quantity ) as receive_qnty,  e.color_id
	        					from wo_po_details_master a, wo_po_break_down b,inv_receive_master d, inv_transaction c, order_wise_pro_details e
	        					where a.job_no=b.job_no_mst and e.po_breakdown_id=b.id and  d.id=c.mst_id and c.id=e.trans_id and d.entry_form=e.entry_form and e.entry_form in (17) and e.trans_id!=0 and d.entry_form in (17)  and d.item_category=3 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and
	        					a.company_name=$cbo_company_id $receive_date $buyer_id_cond  $store_id_cond $search_cond group by a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, e.color_id, a.insert_date, a.total_set_qnty order by a.buyer_name, a.job_no";
	        				}
	                    	//echo $sql_query;
	        				$i=1; $k=1; $checkbuyerArr=array();
	        				$nameArray=sql_select( $sql_query );
	        				foreach ($nameArray as $row)
	        				{
	        					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	        					$dzn_qnty=0;
	        					if($costing_per_id_library[$row[csf('job_no')]]==1)
	        					{
	        						$dzn_qnty=12;
	        					}
	        					else if($costing_per_id_library[$row[csf('job_no')]]==3)
	        					{
	        						$dzn_qnty=12*2;
	        					}
	        					else if($costing_per_id_library[$row[csf('job_no')]]==4)
	        					{
	        						$dzn_qnty=12*3;
	        					}
	        					else if($costing_per_id_library[$row[csf('job_no')]]==5)
	        					{
	        						$dzn_qnty=12*4;
	        					}
	        					else
	        					{
	        						$dzn_qnty=1;
	        					}

	        					$color_id=$row[csf("color_id")]; $book_qty=0; $iss_qty=0; $cons_per=0;
	        					$po_ids=array_unique(explode(",",$row[csf('po_id')]));
	        					foreach($po_ids as $po_id)
	        					{
	        						$book_qty+=$booking_qnty[$po_id][$color_id]['fin_fab_qnty'];
	        						$iss_qty+=$issue_qnty[$po_id][$color_id]['issue_qnty'];
	        						$cons_per+=$result_consumtion[$row[csf('job_no')]][$po_id][$color_id]['consum'];
	        						$actual_qty=$actual_cut_qnty[$po_id][$color_id]['actual_cut_qty'];
	        					}
	        					$po_ids=implode(",",$po_ids);
	        					if(!in_array($row[csf("buyer_name")],$checkbuyerArr))
	        					{

										if($k!=1) // product wise sum/total here------------
										{
											?>
											<tr class="tbl_bottom">
												<td colspan="5" align="right">Buyer Total</td>
												<td align="right"><? //echo number_format($po_quantity,2);$po_quantity=0; ?></td>
												<td align="right"></td>
												<td align="right"></td>


												<td align="right"><? echo number_format($subtot_book_qty,2); $subtot_book_qty=0;?></td>
												<td align="right"><? echo number_format($subtot_rec_qty,2); $subtot_rec_qty=0;?></td>

												<td align="right"><? echo number_format($subtot_rec_bal,2); $subtot_rec_bal=0;?></td>
												<td align="right"><? echo number_format($subtot_iss_qty,2); $subtot_iss_qty=0;?></td>

												<td align="right"><? echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
												<td align="right"><? //echo number_format($subtot_issue_ret_qnty,2); $subtot_issue_ret_qnty=0;?></td>
												<td align="right"><? echo number_format($subtot_possible_cut_pcs,2); $subtot_possible_cut_pcs=0;?></td>
												<td align="right"> <? echo number_format($subtot_actual_qty,2); $subtot_actual_qty=0;?></td>
											</tr>
											<?
										}
										$checkbuyerArr[]=$row[csf("buyer_name")];
										$k++;
									}
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="70"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
										<td width="60" align="center"><p><? echo $row[csf("job_no_prefix_num")]; ?></p></td>
										<td width="50" align="center"><p><? echo $row[csf("year")]; ?></p></td>
										<td width="110"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
										<td width="90" align="right"><p><? $po_quantity+=$row[csf("po_quantity")];echo $row[csf("po_quantity")]; ?>&nbsp;</p></td>
										<td width="60" align="center"><a href='#report_details' onClick="openmypage_ex_factory('<? echo $po_ids; ?>','2');">View</a></td>
										<td width="90"><p><? echo $color_arr[$color_id]; ?></p></td>
										<td width="90" align="right"><p><? $subtot_book_qty+=$book_qty; echo number_format($book_qty,2,'.','');//$booking_qty,2,'.',''); ?>&nbsp;</p></td>
										<?
	                                $rec_qty=$row[csf("receive_qnty")]; //$consumtion_library
	                                $subtot_rec_qty+=$rec_qty;
	                                ?>
	                                <td width="90" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>','woven_receive_popup');"><? echo number_format($rec_qty,2,'.',''); ?></a>&nbsp;</p></td>
	                                <td width="90" align="right"><p><? $rec_bal=$book_qty-$rec_qty; $subtot_rec_bal+=$rec_bal;echo number_format($rec_bal,2,'.',''); ?>&nbsp;</p></td>
	                                <td width="90" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>','woven_issue_popup');"><? echo number_format($iss_qty,2,'.',''); $subtot_iss_qty+=$iss_qty; ?></a>&nbsp;</p></td>
	                                <td width="90" align="right"><p><? $stock=$rec_qty-$iss_qty;$subtot_stock+=$stock; echo number_format($stock,2,'.',''); ?>&nbsp;</p></td>
	                                <td width="100" align="right"><p><? echo number_format($cons_per,4,'.',''); ?></p></td>
	                                <td width="80" align="right"><p><? $possible_cut_pcs=$iss_qty/($cons_per/$dzn_qnty); $subtot_possible_cut_pcs+=$possible_cut_pcs;echo number_format($possible_cut_pcs,0,'.',''); ?></p></td>
	                                <td width="" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>','woven_actual_cut_popup');"><? echo $actual_qty; $subtot_actual_qty+=$actual_qty;?></a>&nbsp;</p></td>
	                            </tr>
	                            <?
	                            $i++;
	                            $total_order_qnty+=$row[csf("po_quantity")];
	                            $total_req_qty+=$book_qty;
	                            $total_rec_qty+=$rec_qty;
	                            $total_rec_bal+=$rec_bal;
	                            $total_issue_qty+=$iss_qty;
	                            $total_stock+=$stock;
	                            $total_possible_cut_pcs+=$possible_cut_pcs;
	                            $total_actual_cut_qty+=$actual_qty;
	                        }*/
	                        ?>
	                    </table>
	                </div>
	                <table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
	                	<tfoot>
	                		<th width="30"></th>
	                		<th width="70"></th>
	                		<th width="60"></th>
	                		<th width="50"></th>
	                		<th width="110">Grand Total</th>
	                		<th width="90" align="right" id=""><? //echo number_format($total_order_qnty,2,'.',''); ?></th>
	                		<th width="60">&nbsp;</th>
	                		<th width="90">&nbsp;</th>
	                		<th width="90" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
	                		<th width="90" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
	                		<th width="90" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>
	                		<th width="90" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
	                		<th width="90" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
	                		<th width="100">&nbsp;</th>
	                		<th width="80" align="right" id="total_possible_cut_pcs"><? echo number_format($total_possible_cut_pcs,0,'.',''); ?></th>
	                		<th width="" align="right" id="total_actual_cut_qty"><? echo $total_actual_cut_qty; ?></th>
	                	</tfoot>
	                </table>
	            </fieldset>
	            <?
	        }
	    }
	}
	elseif($type==3) // show3 button start
	{
	    if($cbo_report_type==1 && $cbo_presentation==1) // Knit Finish Start show3
		{
			$con = connect();
			if($db_type==0)
			{
				$prod_id_cond=" group_concat(b.from_prod_id)";
				if($cbo_year !="") $year_cond="and year(a.insert_date) in($cbo_year)"; else $year_cond="";
			}
			else if($db_type==2)
			{
				$prod_id_cond=" listagg(cast(b.from_prod_id as varchar2(4000)),',') within group (order by b.from_prod_id)";
				if($cbo_year!="") $year_cond="and to_char(a.insert_date,'YYYY') in($cbo_year)";  else $year_cond="";
			}
			$receive_date_cond = str_replace("c.transfer_date", "d.transfer_date", $receive_date);
			if($txt_search_booking!='')
			{
				$booking_cond = " and c.booking_no like '%$txt_search_booking'";
			}
			// ================================ CREATING CONSTRUCTION - COMPOSITION ARRAY ===============================
			$composition_arr=array();
			$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
			$sql_deter_res=sql_select($sql_deter);
			if(count($sql_deter_res)>0)
			{
				foreach( $sql_deter_res as $row )
				{
					if(array_key_exists($row[csf('id')],$composition_arr))
					{
						$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
					else
					{
						$composition_arr[$row[csf('id')]]=$row[csf('construction')]."*".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
				}
			}
			unset($sql_deter_res);

			// =====================================================
			$result_consumtion=array();
			$sql_consumtiont_qty=sql_select(" select b.po_break_down_id, b.color_number_id, c.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs
				from  wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
				where b.pre_cost_fabric_cost_dtls_id=c.id and b.pcs>0 group by b.po_break_down_id, b.color_number_id, c.body_part_id");
			foreach($sql_consumtiont_qty as $row_consum)
			{
				$result_consumtion[$row_consum[csf('po_break_down_id')]][$row_consum[csf('color_number_id')]]+=$row_consum[csf('requirment')]/$row_consum[csf('pcs')];
			}

			unset($sql_consumtiont_qty);
			// =====================================================

			$actual_cut_qnty=array();
			$sql_actual_cut_qty=sql_select(" select a.po_break_down_id,c.color_number_id, sum(b.production_qnty) as actual_cut_qty  from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=1 and a.is_deleted=0 and a.status_active=1  group by  a.po_break_down_id,c.color_number_id");
			foreach( $sql_actual_cut_qty as $row_actual)
			{
				$actual_cut_qnty[$row_actual[csf('po_break_down_id')]][$row_actual[csf('color_number_id')]]['actual_cut_qty']=$row_actual[csf('actual_cut_qty')];
			}
			unset($sql_actual_cut_qty);
			// =========================================================



			// echo "<pre>";print_r($composition_arr);
			// ================================== MAIN QUERY =========================================
			$sql = "SELECT a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.po_number, b.id as po_id, c.construction, c.copmposition, c.booking_no, c.fabric_color_id as color_id, sum(d.order_quantity) as order_qty, c.id as dtls_id, sum(c.fin_fab_qnty) as req_qty, b.shiping_status, e.body_part_id, e.body_part_type
			from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c,wo_po_color_size_breakdown d ,wo_pre_cost_fabric_cost_dtls e
			where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.id=d.po_break_down_id and c.pre_cost_fabric_cost_dtls_id = e.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.booking_type=1 and c.construction is not null   $search_cond $buyer_id_cond $shipment_id_cond $year_cond $booking_cond
			group by a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.po_number, b.id, c.construction, c.copmposition, c.booking_no, c.fabric_color_id, c.id, b.shiping_status, e.body_part_id, e.body_part_type";
			// echo $sql;die();
			$sql_res = sql_select($sql);

			// ======================== STORE PO ID IN GLOBL TEMP TBL FOR FUTURE USE =======================
			$tr_str=execute_query("delete from gbl_temp_report_id where user_id=$user_id");
			if($tr_str) oci_commit($con);

			$temp_table_id=return_field_value("max(id)+1 as id","gbl_temp_report_id","1=1","id");
			if($temp_table_id=="") $temp_table_id=1;
			foreach($sql_res as $row)
			{
				if($po_check[$row[csf("po_id")]]=="")
				{
					$po_check[$row[csf("po_id")]]=$row[csf("po_id")];
					$r_id=execute_query("insert into gbl_temp_report_id (id, ref_val, ref_from, user_id, ref_string) values ($temp_table_id,".$row[csf("po_id")].",1,$user_id,'".$row[csf("po_id")]."')");
					if($r_id)
					{
						$r_id=1;
					}
					else
					{
						echo "insert into gbl_temp_report_id (id, ref_val, ref_from, user_id, ref_string) values ($temp_table_id,".$row[csf("po_id")].",1,$user_id,'".$row[csf("po_id")]."')";
						oci_rollback($con);
						die;
					}
					$temp_table_id++;
				}
			}

			if($r_id)
			{
				oci_commit($con);
			}
			else
			{
				oci_rollback($con);
			}
			// ====================================== End ==================================================
			$details_data = array(); $po_check = array(); $dtlsId_check = array(); $color_qnty = array(); $po_info_array = array();
			foreach($sql_res as $val)
			{
				$pre_key=$val[csf('po_id')]."*".$val[csf('color_id')]."*".$val[csf('construction')]."*".$val[csf('copmposition')]."*".$val[csf('body_part_id')];

				$details_data[$pre_key]['job_no_prefix_num']=$val[csf('job_no_prefix_num')];
				$details_data[$pre_key]['buyer_name']=$val[csf('buyer_name')];
				$details_data[$pre_key]['style_ref_no']=$val[csf('style_ref_no')];
				$details_data[$pre_key]['po_number']=$val[csf('po_number')];
				$details_data[$pre_key]['po_id']=$val[csf('po_id')];
				$details_data[$pre_key]['booking_no']=$val[csf('booking_no')];
				$details_data[$pre_key]['body_part_id']=$val[csf('body_part_id')];
				$details_data[$pre_key]['body_part_type']=$val[csf('body_part_type')];
				$details_data[$pre_key]['shiping_status']=$val[csf('shiping_status')];

				$po_info_array[$val[csf('po_id')]]['job']=$val[csf('job_no_prefix_num')];
				$po_info_array[$val[csf('po_id')]]['buyer']=$val[csf('buyer_name')];
				$po_info_array[$val[csf('po_id')]]['style']=$val[csf('style_ref_no')];
				$po_info_array[$val[csf('po_id')]]['po_number']=$val[csf('po_number')];
				$po_info_array[$val[csf('po_id')]]['booking_no']=$val[csf('booking_no')];
				$po_info_array[$val[csf('po_id')]]['body_part_id']=$val[csf('body_part_id')];
				$po_info_array[$val[csf('po_id')]]['body_part_type']=$val[csf('body_part_type')];

				if($po_check[$val[csf('po_id')]][$val[csf('color_id')]]=="")
				{
					$po_check[$val[csf('po_id')]][$val[csf('color_id')]] = $val[csf('po_id')];
					$color_qnty[$val[csf('po_id')]][$val[csf('color_id')]]['order_qty']+=$val[csf('order_qty')];
				}
				if($dtlsId_check[$pre_key][$val[csf('dtls_id')]][$val[csf('color_id')]]=="")
				{
					$dtlsId_check[$pre_key][$val[csf('dtls_id')]][$val[csf('color_id')]] = $val[csf('dtls_id')];
					$details_data[$pre_key]['req_qty']+=$val[csf('req_qty')];
				}
			}
			// echo "<pre>";print_r($details_data);

			unset($sql_res);

			$store_id_cond = str_replace("c.store_id", "e.store_id", $store_id_cond);
			// ============================ GETTING TRANSACTION DATA ==========================
			/*$sql_order_trams="SELECT a.po_breakdown_id as po_id,a.color_id,c.detarmination_id,b.prod_id,
				sum(case when a.entry_form in (7,37,66,68) then a.quantity else 0 end) as receive_qnty,
				sum(case when a.entry_form in(14,15,134) and a.trans_type=5 then a.quantity else 0 end) as rec_trns_qnty,
				sum(case when a.entry_form in (18,71) then a.quantity else 0 end) as issue_qnty,
				sum(case when a.entry_form in (126,52) then a.quantity else 0 end) as issue_ret_qnty,
				sum(case when a.entry_form in (46) then a.quantity else 0 end) as rec_ret_qnty,
				sum(case when a.entry_form in(14,15,134) and a.trans_type=6 then a.quantity else 0 end) as issue_trns_qnty, b.body_part_id
			from order_wise_pro_details a, inv_transaction b, product_details_master c
			where a.trans_id=b.id and b.prod_id=c.id and a.prod_id=c.id and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.entry_form in(7,37,66,68,14,15,134,46,18,71,126,52) and b.company_id=$cbo_company_id and a.po_breakdown_id in(select ref_string from gbl_temp_report_id where user_id=$user_id) $store_id_cond
			group by a.po_breakdown_id,a.color_id,c.detarmination_id,b.prod_id, b.body_part_id ";*/

			$sql_order_trams="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, sum(a.total_set_qnty) as ratio, b.id as po_id, b.po_number, b.plan_cut as po_quantity, b.shiping_status,
			sum(case when d.entry_form in (7,37,66,68) then d.quantity else 0 end) as receive_qnty,
			sum(case when d.entry_form in(14,15,134) and d.trans_type=5 then d.quantity else 0 end) as rec_trns_qnty,
			sum(case when d.entry_form in (18,71) then d.quantity else 0 end) as issue_qnty,
			sum(case when d.entry_form in (126,52) then d.quantity else 0 end) as issue_ret_qnty,
			sum(case when d.entry_form in(14,15,134) and d.trans_type=6 then d.quantity else 0 end) as issue_trns_qnty, e.color color_id, e.detarmination_id, c.body_part_id, c.prod_id
			from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details d,inv_transaction c,product_details_master e
			where a.job_no=b.job_no_mst and b.id=d.po_breakdown_id and d.trans_id=c.id and c.prod_id=e.id and d.trans_id!=0 and a.status_active=1 and a.is_deleted=0  and d.po_breakdown_id in(select ref_string from gbl_temp_report_id where user_id=$user_id) and d.entry_form in (14,7,37,66,68,14,15,18,71,126,134,52) and c.item_category=2 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_name=$cbo_company_id $all_booking_no_cond $receive_date $buyer_id_cond $store_id_cond $shipment_id_cond $search_cond $year_cond
			group by a.job_no_prefix_num,a.job_no,a.buyer_name,a.style_ref_no,b.id,b.po_number,e.color,b.plan_cut,b.shiping_status, e.detarmination_id, c.body_part_id, c.prod_id
			order by a.buyer_name,a.job_no,b.po_number,e.color ";
			//echo $sql_order_trams;die();
			$sql_order_trams_res = sql_select($sql_order_trams);
			if(count($sql_order_trams_res)==0)
			{
				?>
				<div style="color: red;font-size: 20px;text-align: center;font-weight: bold;">Data not available! Please try again.</div>
				<?
				die();
			}
			foreach ($sql_order_trams_res as $val)
			{
				$pre_key=$val[csf('po_id')]."*".$val[csf('color_id')]."*".$composition_arr[$val[csf('detarmination_id')]]."*".$val[csf('body_part_id')];
				$details_data[$pre_key]['receive_qnty']+=$val[csf('receive_qnty')];
				$details_data[$pre_key]['rec_trns_qnty']+=$val[csf('rec_trns_qnty')];
				$details_data[$pre_key]['issue_qnty']+=$val[csf('issue_qnty')];
				$details_data[$pre_key]['issue_ret_qnty']+=$val[csf('issue_ret_qnty')];
				$details_data[$pre_key]['issue_trns_qnty']+=$val[csf('issue_trns_qnty')];
				$details_data[$pre_key]['prod_id'].=$val[csf('prod_id')].",";
				$details_data[$pre_key]['po_id'] = $val[csf('po_id')];
				$details_data[$pre_key]['body_part_id'] = $val[csf('body_part_id')];
				$details_data[$pre_key]['shiping_status'] = $val[csf('shiping_status')];
				$details_data[$pre_key]['color_id'] = $val[csf('color_id')];
				//echo $val[csf('color_id')].'<br>';
			}
			unset($sql_order_trams_res);

			$booking_sql = "SELECT c.po_break_down_id as po_id, c.construction, c.copmposition,c.fabric_color_id as color_id, sum(c.fin_fab_qnty) as req_qty
			from  wo_booking_dtls c
			where c.status_active=1 and c.is_deleted=0 and c.booking_type=1 and c.construction is not null $booking_cond and c.po_break_down_id in(select ref_string from gbl_temp_report_id where user_id=$user_id) group by c.po_break_down_id, c.construction, c.copmposition,c.fabric_color_id";
			// echo $booking_sql;die();
			$booking_sql_res = sql_select($booking_sql);
			$reqQtyArr = array();
			foreach ($booking_sql_res as $val)
			{
				$reqQtyArr[$val[csf('po_id')]][$val[csf('color_id')]][$val[csf('construction')]][$val[csf('copmposition')]] = $val[csf('req_qty')];
			}

			//echo "<pre>";print_r($details_data);
			$tr_str=execute_query("delete from gbl_temp_report_id where user_id=$user_id");
			if($tr_str) oci_commit($con);

			$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
			$body_part_type_array=return_library_array( "select id, body_part_type from lib_body_part", "id", "body_part_type");
			?>
			<fieldset style="width:2430px;">
				<table cellpadding="0" cellspacing="0" width="1640">
					<tr class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="21" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
					</tr>
					<tr  class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
					</tr>
					<tr  class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="21" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from_st)) ;?></strong></td>
					</tr>
				</table>
				<table width="2430" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="70">Buyer</th>
						<th width="60">Job</th>
						<th width="100"><p>Booking No.</th>
						<th width="100">Order</th>
						<th width="110">Style</th>
						<th width="80"><p>Order Qty (Pcs)</p></th>
						<th width="100"><p>Ex-factory</th>

						<th width="100"><p>Shipping Status</p></th>

						<th width="90"><p>Fin. Color</p></th>
						<th width="100"><p>F.Construction</p></th>

						<th width="100"><p>Body Part</p></th>
						<th width="100"><p>Body Part Type</p></th>

						<th width="80">Req. Qty</th>
                        <th width="80" title="Trans. in"><p>Trans. In</p></th>
						<th width="80" title="Received"><p>Received</p></th>
						<th width="70" title="Issue Return"><p>Issue Return</p></th>
						<th width="80" title="Rec.+Issue Rtn.+Trans. in"><p>Total Received</p></th>
						<th width="80" title="Req.-Totat Rec."><p>Received Balance</p></th>
                        <th width="80" title="Trans. out"><p>Trans Out</p></th>
						<th width="80" title="Issued">Issued</th>
						<th width="70" title="Recv. Return"><p>Recv. Return</p></th>
						<th width="80" title="Issue+Rec. Ret.+Trans. out"><p>Total Issued</p></th>
						<th width="80" title="Total Rec.- Total Issue">Stock</th>

						<th width="80"><p>Consumption Pcs.</p></th>
						<th width="80"><p>Possible Cut Pcs.</p></th>
						<th width="80"><p>Actual Cut</p></th>
					</thead>
				</table>
				<div style="width:2450px; max-height:350px; overflow-y:scroll;" id="scroll_body">
					<table width="2430" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
						<?
						$i=1;
						foreach ($details_data as $dtls_key=>$row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$dzn_qnty=0;
							/*
							if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
							else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
							else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
							else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;*/

							//echo $val[csf('color_id')].' TEST '.$color_id;
							//echo $val[csf('po_id')].' TEST '.$order_id.'<br>';
							//echo $row['po_id'];
							//echo $row['color_id'];
							/*echo "<pre>";
							print_r($dtls_key);*/
							$data = explode("*", $dtls_key);

							$order_id=$data[0];
							$color_id=$data[1];

							$rec_qty=0;
							$reqQty = $reqQtyArr[$data[0]][$data[1]][$data[2]][$data[3]];
							$issue_ret_qnty=$row['issue_ret_qnty'];
							$trans_in_qty=$row["rec_trns_qnty"];
							$rec_qty=($row["receive_qnty"]);

							$trans_out=($row["issue_trns_qnty"]);
							$iss_qty=$row["issue_qnty"];
							$rec_ret_qnty=$row["rec_ret_qnty"];

							$total_receive=$trans_in_qty+$rec_qty+$issue_ret_qnty;
							$total_issue=$trans_out+$iss_qty+$rec_ret_qnty;
							$stock = 0;
							$stock = $total_receive-$total_issue;
							$stock = number_format($stock,2);
							// echo $stock."<br>";
							// echo $value_for_search_by;
							// die('Go to hell');
							if($value_for_search_by==2 && $stock > 0)
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="70" align="center"><p><? echo $buyer_arr[$po_info_array[$row["po_id"]]['buyer']]; ?></p></td>
									<td width="60" align="center"><p><? echo $po_info_array[$row["po_id"]]['job']; ?></p></td>
									<td width="100" align="center"><p><? echo $po_info_array[$row["po_id"]]['booking_no']; ?></p></td>
									<td width="100" align="center" title="<? echo $order_id ;?>" ><p><? echo $po_info_array[$row["po_id"]]['po_number']; ?></p></td>
									<td width="110" align="center"><p><? echo $po_info_array[$row["po_id"]]['style']; ?></p></td>
									<td width="80" align="right"><p><? $po_quantity+=$color_qnty[$order_id][$color_id]["order_qty"];echo number_format($color_qnty[$order_id][$color_id]["order_qty"],0); ?></p></td>
									<td width="100" align="center"><a href='#report_details' onClick="openmypage_ex_factory('<? echo $row['po_id']; ?>','1');">View</a></td>


									<td width="100" align="center"><p><? echo $shipment_status[$row["shiping_status"]]; ?></p></td>


									<td width="90" align="center" title="<? echo $data[1] ;?>"><p><? echo $color_arr[$data[1]]; ?></p></td>
									<td width="100" align="left"><p><? echo $data[2];?></p></td>


									<td width="100" align="center" title="Body P Type2"><p><? echo $body_part[$data[4]]; ?></p></td>
									<td width="100" align="center" title="Body P Type2"><p><? echo $body_part_type[$body_part_type_array[$data[4]]]; ?></p></td>


									<td width="80" align="right">
										<?
										$book_qty = $row['req_qty'];
										echo number_format($book_qty,2,'.','');
										?>
									</td>
	                                <td width="80" title="Trans. In" align="right"><? echo number_format($trans_in_qty,2,'.',''); ?></td>
									<td width="80" align="right"><? echo number_format($rec_qty,2,'.',''); ?></td>
									<td width="70" align="right">
										<a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id; ?>',2,'issue_ret_popup');">
											<? echo number_format($issue_ret_qnty,2,'.',''); ?>
										</a>
									</td>
									<td width="80" align="right">
										<a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id; ?>',1,'receive_popup','<? echo str_replace("'","",$cbo_store_id); ?>');">
										<? echo number_format($total_receive,2,'.',''); ?>
										</a>
									</td>
									<td width="80" align="right"><p><? $rec_bal=$book_qty-$total_receive; $subtot_rec_bal+=$rec_bal;echo number_format($rec_bal,2,'.',''); ?></p></td>
									<td width="80" align="right"><? echo number_format($trans_out,2,'.',''); ?></td>
									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id; ?>',3,'issue_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"> <? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
									<td width="70" align="right">
									<a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id; ?>',4,'receive_ret_popup');">
										<? echo number_format($rec_ret_qnty,2,'.',''); ?>
									</a>
									<td width="80" align="right"><? echo number_format($total_issue,2,'.',''); ?></td>
									<td width="80" align="right">
										<p>
											<a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id; ?>',5,'stock_popup',<? echo $cbo_store_id;?>);">
												<? echo $stock; ?>
											</a>
										</p>
									</td>


									<td width="80" align="center"><p><? $cons_per=($result_consumtion[$row['po_id']][$row['color_id']]); echo number_format($cons_per,8,'.','');
										// $cons_per=($result_consumtion[$order_id][$color_id]);?></p>
									</td>
									<td width="80" align="right"><p><? $possible_cut_pcs=$total_issue/($cons_per);$subtot_possible_cut_pcs+=$possible_cut_pcs; echo ($cons_per!="")?number_format($possible_cut_pcs):"0"; ?></p>
									</td>
									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id; ?>','actual_cut_popup');"><? $actual_qty=$actual_cut_qnty[$row['po_id']][$row['color_id']]['actual_cut_qty'];
									$subtot_actual_qty+=$actual_qty; echo number_format($actual_qty,2,'.','');
									// $actual_qty=$actual_cut_qnty[$order_id][$color_id]['actual_cut_qty']; ?></a>&nbsp;</p>
									</td>
								</tr>
								<?
								$i++;
								$total_order_qnty+=$row[csf("po_quantity")];
								$rec_trns_in_qnty+=$trans_in_qty;
								$total_req_qty+=$book_qty;
								$total_rec_qty+=$rec_qty;
								$total_receive_qnty+=$total_receive;
								$total_rec_bal+=$rec_bal;
								$issue_trns_out_qnty+=$trans_out;
								$total_issue_qty+=$iss_qty;
								$total_issue_quantity+=$total_issue;
								$total_stock+=$stock;
								$total_possible_cut_pcs+=$possible_cut_pcs;
								$total_rec_return_qnty+=$rec_ret_qnty;
								$total_issue_ret_qnty+=$issue_ret_qnty;
								$total_actual_cut_qty+=$actual_qty;
							}
							else if($value_for_search_by==1)// && $reqQty>0
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="70" align="center"><p><? echo $buyer_arr[$po_info_array[$row["po_id"]]['buyer']]; ?></p></td>
									<td width="60" align="center"><p><? echo $po_info_array[$row["po_id"]]['job']; ?></p></td>
									<td width="100" align="center"><p><? echo $po_info_array[$row["po_id"]]['booking_no']; ?></p></td>
									<td width="100" align="center" title="<? echo $order_id ;?>" ><p><? echo $po_info_array[$row["po_id"]]['po_number']; ?></p></td>
									<td width="110" align="center"><p><? echo $po_info_array[$row["po_id"]]['style']; ?></p></td>
									<td width="80" align="right"><p><? $po_quantity+=$color_qnty[$order_id][$color_id]["order_qty"];echo number_format($color_qnty[$order_id][$color_id]["order_qty"],0); ?></p></td>
									<td width="100" align="center"><a href='#report_details' onClick="openmypage_ex_factory('<? echo $row['po_id']; ?>','1');">View</a></td>

									<td width="100" align="center"><p><? echo $shipment_status[$row["shiping_status"]]; ?></p></td>

									<td width="90" align="center" title="<? echo $data[1] ;?>"><p><? echo $color_arr[$data[1]]; ?></p></td>
									<td width="100" align="left"><p><? echo $data[2];?></p></td>

									<td width="100" align="center"><p><? echo $body_part[$data[4]]; ?></p></td>
									<td width="100" align="center"><p><? echo $body_part_type[$body_part_type_array[$data[4]]]; ?></p></td>

									<td width="80" align="right">
										<?
										// $book_qty = $row['req_qty'];
										$book_qty = $reqQty;
										echo number_format($book_qty,2,'.','');
										?>
									</td>
	                                <td width="80" title="Trans. In" align="right"><? echo number_format($trans_in_qty,2,'.',''); ?></td>
									<td width="80" align="right"><? echo number_format($rec_qty,2,'.',''); ?></td>
									<td width="70" align="right">
										<a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id; ?>',2,'issue_ret_popup');">
											<? echo number_format($issue_ret_qnty,2,'.',''); ?>
										</a>
									</td>
									<td width="80" align="right">
										<a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id; ?>',1,'receive_popup','<? echo str_replace("'","",$cbo_store_id); ?>');">
										<? echo number_format($total_receive,2,'.',''); ?>
										</a>
									</td>
									<td width="80" align="right"><p><? $rec_bal=$book_qty-$total_receive; $subtot_rec_bal+=$rec_bal;echo number_format($rec_bal,2,'.',''); ?></p></td>
									<td width="80" align="right"><? echo number_format($trans_out,2,'.',''); ?></td>
									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id; ?>',3,'issue_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"> <? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
									<td width="70" align="right">
									<a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id; ?>',4,'receive_ret_popup');">
										<? echo number_format($rec_ret_qnty,2,'.',''); ?>
									</a>
									<td width="80" align="right"><? echo number_format($total_issue,2,'.',''); ?></td>
									<td width="80" align="right">
										<p>
											<a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id; ?>',5,'stock_popup',<? echo $cbo_store_id;?>);">
												<? echo $stock; ?>
											</a>
										</p>
									</td>


									<td width="80" align="center"><p><? $cons_per=($result_consumtion[$row['po_id']][$row['color_id']]); echo number_format($cons_per,8,'.','');
										// $cons_per=($result_consumtion[$order_id][$color_id]);?></p>
									</td>
									<td width="80" align="right"><p><? $possible_cut_pcs=$total_issue/($cons_per);$subtot_possible_cut_pcs+=$possible_cut_pcs; echo ($cons_per!="")?number_format($possible_cut_pcs):"0"; ?></p>
									</td>
									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id; ?>','actual_cut_popup');"><? $actual_qty=$actual_cut_qnty[$row['po_id']][$row['color_id']]['actual_cut_qty'];
									$subtot_actual_qty+=$actual_qty; echo number_format($actual_qty,2,'.','');
									// $actual_qty=$actual_cut_qnty[$order_id][$color_id]['actual_cut_qty']; ?></a>&nbsp;</p>
									</td>
								</tr>
								<?
								$i++;
								$total_order_qnty+=$row[csf("po_quantity")];
								$rec_trns_in_qnty+=$trans_in_qty;
								$total_req_qty+=$book_qty;
								$total_rec_qty+=$rec_qty;
								$total_receive_qnty+=$total_receive;
								$total_rec_bal+=$rec_bal;
								$issue_trns_out_qnty+=$trans_out;
								$total_issue_qty+=$iss_qty;
								$total_issue_quantity+=$total_issue;
								$total_stock+=$stock;
								$total_possible_cut_pcs+=$possible_cut_pcs;
								$total_rec_return_qnty+=$rec_ret_qnty;
								$total_issue_ret_qnty+=$issue_ret_qnty;
								$total_actual_cut_qty+=$actual_qty;
							}
						}
						?>
					</table>
					<table width="2430" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
						<tfoot>
							<th width="30"></th>
							<th width="70"></th>
							<th width="60"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="110"></th>
							<th width="80" align="right" id=""><? //echo number_format($total_order_qnty,2,'.',''); ?></th>
							<th width="100">&nbsp;</th>

							<th width="100">&nbsp;</th>

							<th width="90">&nbsp;</th>
							<th width="100">&nbsp;</th>


							<th width="100">&nbsp;</th>
							<th width="100" align="right">Grand Total</th>


							<th width="80" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
	                        <th width="80" align="right" id="value_total_tranin_qty"><? echo number_format($rec_trns_in_qnty,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
							<th width="70" align="right" id="value_total_issue_ret_qty"><? echo number_format($total_issue_ret_qnty,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_receive_qty"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>
	                        <th width="80" align="right" id="value_total_tranout_qty"><? echo number_format($issue_trns_out_qnty,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
							<th width="70"  id="value_recv_ret_qty"><? echo number_format($total_rec_return_qnty,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_issue_quantity"><? echo number_format($total_issue_quantity,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>


							<th width="80">&nbsp;</th>
							<th width="80" align="right" id="total_possible_cut_pcs"><? echo number_format($total_possible_cut_pcs,2,'.',''); ?></th>
							<th width="80" align="right" id="total_actual_cut_qty"><? echo number_format($total_actual_cut_qty,2,'.',''); ?></th>
						</tfoot>
					</table>
				</div>
			</fieldset>
			<?
		}
		else
		{
			?>
			<div style="color: red;font-size: 18px;text-align: center;font-weight: bold;">Only Work For Order Wise!</div>
			<?
			die();
		}
	}
	elseif($type==4) // show4 button start
	{
		if($cbo_report_type==1) // Knit Finish Start
		{
			$product_array=array();
			$sql_product="select id, color from product_details_master where item_category_id=2 and status_active=1 and is_deleted=0";
			$sql_product_result=sql_select($sql_product);
			foreach( $sql_product_result as $row )
			{
				$product_array[$row[csf('id')]]=$row[csf('color')];
			}
			unset($sql_product_result);

			$transfer_arr=array(); $all_data_arr=array();

			if($db_type==0)
			{
				$prod_id_cond=" group_concat(b.from_prod_id)";
				if($cbo_year!="") $year_cond="and year(a.insert_date) in($cbo_year)"; else $year_cond="";
			}
			else if($db_type==2)
			{
				$prod_id_cond=" listagg(cast(b.from_prod_id as varchar2(4000)),',') within group (order by b.from_prod_id)";
				if($cbo_year!="") $year_cond="and to_char(a.insert_date,'YYYY') in($cbo_year)";  else $year_cond="";
			}

			$iss_return_qnty=array();
			$sql_issue_ret=sql_select("select po_breakdown_id, prod_id, sum(quantity) as issue_ret_qnty  from order_wise_pro_details where trans_id!=0 and status_active=1 and is_deleted=0 and entry_form=126 group by po_breakdown_id, prod_id");
			foreach( $sql_issue_ret as $row )
			{
				$iss_return_qnty[$row[csf('po_breakdown_id')]][$product_array[$row[csf('prod_id')]]]['issue_ret_qnty']+=$row[csf('issue_ret_qnty')];
			}
			unset($sql_issue_ret);

			$rec_return_qnty=array();
			$sql_rec_ret=sql_select("select po_breakdown_id, prod_id, sum(quantity) as rec_ret_qnty from order_wise_pro_details where trans_id!=0 and status_active=1 and is_deleted=0 and entry_form=46 group by po_breakdown_id, prod_id");
			foreach( $sql_rec_ret as $row )
			{
				$rec_return_qnty[$row[csf('po_breakdown_id')]][$product_array[$row[csf('prod_id')]]]['rec_ret_qnty']+=$row[csf('rec_ret_qnty')];
			}
			unset($sql_rec_ret);
			/*echo "<pre>";
			print_r($rec_return_qnty);*/

			$booking_qnty=array();
			$sql_booking=sql_select("select b.po_break_down_id, b.fabric_color_id, sum(b.fin_fab_qnty ) as fin_fab_qnty  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,b.fabric_color_id");
			foreach( $sql_booking as $row_book)
			{
				$booking_qnty[$row_book[csf('po_break_down_id')]][$row_book[csf('fabric_color_id')]]['fin_fab_qnty']=$row_book[csf('fin_fab_qnty')];
			}
			unset($sql_booking);
			$actual_cut_qnty=array();
			$sql_actual_cut_qty=sql_select("select a.po_break_down_id,c.color_number_id, sum(b.production_qnty) as actual_cut_qty  from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=1 and a.is_deleted=0 and a.status_active=1  group by  a.po_break_down_id,c.color_number_id");
			foreach( $sql_actual_cut_qty as $row_actual)
			{
				$actual_cut_qnty[$row_actual[csf('po_break_down_id')]][$row_actual[csf('color_number_id')]]['actual_cut_qty']=$row_actual[csf('actual_cut_qty')];
			}
			unset($sql_actual_cut_qty);

			$result_consumtion=array();
			$sql_consumtiont_qty=sql_select("select b.po_break_down_id, b.color_number_id, c.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from  wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c	where b.pre_cost_fabric_cost_dtls_id=c.id and b.pcs>0 group by b.po_break_down_id, b.color_number_id, c.body_part_id");
			foreach($sql_consumtiont_qty as $row_consum)
			{
				$result_consumtion[$row_consum[csf('po_break_down_id')]][$row_consum[csf('color_number_id')]]+=$row_consum[csf('requirment')]/$row_consum[csf('pcs')];
			}

			unset($sql_consumtiont_qty);



			if($cbo_presentation==1) // order wise
			{
				$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
				?>
				<fieldset style="width:2515px;">
					<table cellpadding="0" cellspacing="0" width="1580">
						<tr class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="21" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
						</tr>
						<tr  class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
						</tr>
						<tr  class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="21" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from_st)) ;?></strong></td>
						</tr>
					</table>
					<table width="2490" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
						<thead>
							<th width="30">SL</th>
							<th width="70">Buyer</th>
							<th width="60">Job Year</th>
							<th width="60">Job</th>
							<th width="100">Booking No.</th>
							<th width="100">Order</th>
							<th width="100">Internal Ref</th>
							<th width="110">Style</th>
							<th width="80">Order Qty (Pcs)</th>
							<th width="60">Ex-factory</th>
							<th width="90">Shipping Status </th>
							<th width="90">Stock For </th>
							<th width="90">Shipment Date</th>
							<th width="90">Fin. Color</th>

							<th width="80">Req. Qty</th>
	                        <th width="80" title="Trans. in">Trans. In</th>
							<th width="80" title="Received">Received</th>

							<th width="70" title="Issue Return">Issue Return</th>
							<th width="80" title="Rec.+Issue Rtn.+Trans. in">Total Received</th>
							<th width="80" title="Req.-Totat Rec.">Received Balance</th>
	                        <th width="80" title="Trans. out">Trans Out</th>
							<th width="80" title="Issued">Issued</th>
							<th width="80" title="Issued">Issued to Reprocess</th>
							<th width="80" title="Issued">Issue Balance</th>

							<th width="70" title="Recv. Return">Recv. Return</th>
							<th width="80" title="Issue+Rec. Ret.+Trans. out">Total Issued</th>

							<th width="80" title="Total Rec.- Total Issue">Stock</th>
							<th width="80" title="Total Rec.- Total Issue">DOH</th>
							<th width="80">Consumption Pcs.</th>
							<th width="80">Possible Cut Pcs.</th>
							<th>Actual Cut</th>
						</thead>
					</table>
					<div style="width:2510px; max-height:350px; overflow-y:scroll;" id="scroll_body">
						<table width="2490" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
							<?
							$po_break_id_con='';
							if($txt_search_booking!='')
							{
								$sql_booking_query="select id, po_break_down_id, fabric_color_id, booking_no from wo_booking_dtls where booking_no like '%$txt_search_booking' and status_active=1 and is_deleted=0 and booking_type=1";
								//echo $sql_booking_query;die;
								$bookArray=sql_select($sql_booking_query);
								$booking_po_id_arr=array();
								$booking_po_data_arr=array();
								foreach ($bookArray as $row)
								{
									$booking_po_id_arr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
									$booking_po_data_arr[$row[csf("po_break_down_id")]][$row[csf("fabric_color_id")]]['booking_no']=$row[csf("booking_no")];
								}

								$all_booking_no=array_chunk($booking_po_id_arr,999);
								$all_booking_no_cond=" and(";
								foreach($all_booking_no as $booking_id)
								{
									if($all_booking_no_cond==" and(")
									{
										$all_booking_no_cond.=" b.id in(".implode(',',$booking_id).")";
									}
									else
									{
										$all_booking_no_cond.=" or b.id in(".implode(',',$booking_id).")";
									}
								}
								$all_booking_no_cond.=")";

								if($all_booking_no_cond == " and()"){
									$all_booking_no_cond = "";
								}
							}

							// echo $all_booking_no_cond;die;

							if($db_type==0)
							{
								$select_prod_id =" group_concat(c.prod_id) as prod_id";
							}else{
								$select_prod_id =" listagg(cast(d.prod_id as varchar2(4000)),',') within group (order by d.prod_id) as prod_id";
							}

							if($txt_date_from !="" && $txt_date_to!="")
							{
								if($cbo_date_cat==1) $search_cond.=" and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to' ";
								else if($cbo_date_cat==2) $search_cond.=" and b.update_date between '$txt_date_from' and '$txt_date_to' and b.status_active=3";
							}

							if($cbo_sock_for==1)
							{
								$search_cond.=" and b.shiping_status<>3 and b.status_active=1";
							}
							else if($cbo_sock_for==2)
							{
								$search_cond.=" and b.status_active=3";
							}
							else if($cbo_sock_for==3)
							{
								$search_cond.=" and b.shiping_status=3 and b.status_active=1";
							}

							$sql_query_issue=sql_select("SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, sum(a.total_set_qnty) as ratio, b.id as po_id, b.po_number,b.grouping, b.plan_cut as po_quantity,b.shipment_date, b.shiping_status,b.status_active,
							 sum(case when d.entry_form in (18,71) and f.issue_purpose!=44  then d.quantity else 0 end) as issue_qnty,
  							sum(case when d.entry_form in (18,71) and f.issue_purpose=44  then d.quantity else 0 end) as issue_qnty_re_process,
							e.color color_id, d.prod_id
							from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details d,inv_transaction c,product_details_master e,inv_issue_master f
							where a.job_no=b.job_no_mst and b.id=d.po_breakdown_id  and d.trans_id=c.id and c.prod_id=e.id and c.mst_id=f.id and f.entry_form in (18,71)  and d.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and d.entry_form in (18,71) and c.item_category=2 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active in (1,3) and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_name=$cbo_company_id $all_booking_no_cond $receive_date $buyer_id_cond $store_id_cond $shipment_id_cond $search_cond $year_cond
							group by a.job_no_prefix_num,a.job_no,a.buyer_name,a.style_ref_no,a.insert_date,b.id,b.po_number,b.grouping,e.color,b.plan_cut,b.shiping_status,b.status_active,d.prod_id,b.shipment_date
							order by a.buyer_name,a.job_no,b.po_number,e.color");
							foreach ($sql_query_issue as $row)
							{
								$datastr = $row[csf('job_no')]."##".$row[csf('job_no_prefix_num')]."##".$row[csf('style_ref_no')]."##".$row[csf('po_number')]."##".$row[csf('po_id')]."##".$row[csf('color_id')]."##".$row[csf('po_quantity')]."##".$row[csf('shiping_status')];
								$data_array[$row[csf('buyer_name')]][$datastr]['issue_qnty']			+=$row[csf('issue_qnty')];
								$data_array[$row[csf('buyer_name')]][$datastr]['issue_qnty_re_process']	+=$row[csf('issue_qnty_re_process')];
							}


							$sql_query="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, sum(a.total_set_qnty) as ratio, b.id as po_id, b.po_number,b.grouping, b.plan_cut as po_quantity,b.shipment_date, b.shiping_status,b.status_active,
							sum(case when d.entry_form in (7,37,66,68) then d.quantity else 0 end) as receive_qnty,
							sum(case when d.entry_form in(14,15,134,306) and d.trans_type=5 then d.quantity else 0 end) as rec_trns_qnty, to_char(a.insert_date,'YYYY') as year,
							sum(case when d.entry_form in (18,71) then d.quantity else 0 end) as issue_qnty,
							sum(case when d.entry_form in (126,52) then d.quantity else 0 end) as issue_ret_qnty,
							sum(case when d.entry_form in (46) then d.quantity else 0 end) as rec_ret_qnty,
							sum(case when d.entry_form in(14,15,134,306) and d.trans_type=6 then d.quantity else 0 end) as issue_trns_qnty, e.color color_id, d.prod_id
							from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details d,inv_transaction c,product_details_master e
							where a.job_no=b.job_no_mst and b.id=d.po_breakdown_id  and d.trans_id=c.id and c.prod_id=e.id and d.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and d.entry_form in (14,7,37,66,68,14,15,18,71,126,134,52,46,306) and c.item_category=2 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active in (1,3) and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_name=$cbo_company_id $all_booking_no_cond $receive_date $buyer_id_cond $store_id_cond $shipment_id_cond $search_cond $year_cond
							group by a.job_no_prefix_num,a.job_no,a.buyer_name,a.style_ref_no,a.insert_date,b.id,b.po_number,b.grouping,e.color,b.plan_cut,b.shiping_status,b.status_active,d.prod_id,b.shipment_date
							order by a.buyer_name,a.job_no,b.po_number,e.color";
							//echo $sql_query;die;
							//$select_prod_id


							$i=1; $k=1;$total_rec_return_qnty=0;$total_issue_ret_qnty=0;$issue_trns_out_qnty=0;$rec_trns_in_qnty=0;
							$nameArray=sql_select($sql_query); $fin_color_array=array(); $fin_color_data_arr=array();$checkbuyerArr=array(); $po_break_id_arr=array();$prod_id_arr=array();$prod_id_all="";

							foreach ($nameArray as $row)
							{
								$datastr = $row[csf('job_no')]."##".$row[csf('job_no_prefix_num')]."##".$row[csf('style_ref_no')]."##".$row[csf('po_number')]."##".$row[csf('po_id')]."##".$row[csf('color_id')]."##".$row[csf('po_quantity')]."##".$row[csf('shiping_status')];

								$data_array[$row[csf('buyer_name')]][$datastr]['buyer_name']		=$row[csf('buyer_name')];
								$data_array[$row[csf('buyer_name')]][$datastr]['year']		        =$row[csf('year')];
								$data_array[$row[csf('buyer_name')]][$datastr]['job_no']			=$row[csf('job_no')];
								$data_array[$row[csf('buyer_name')]][$datastr]['job_no_prefix_num']	=$row[csf('job_no_prefix_num')];
								$data_array[$row[csf('buyer_name')]][$datastr]['style_ref_no']		=$row[csf('style_ref_no')];
								$data_array[$row[csf('buyer_name')]][$datastr]['po_number']			=$row[csf('po_number')];
								$data_array[$row[csf('buyer_name')]][$datastr]['po_id']				=$row[csf('po_id')];
								$data_array[$row[csf('buyer_name')]][$datastr]['grouping']			=$row[csf('grouping')];
								$data_array[$row[csf('buyer_name')]][$datastr]['color_id']			=$row[csf('color_id')];
								$data_array[$row[csf('buyer_name')]][$datastr]['po_quantity']		=$row[csf('po_quantity')];
								$data_array[$row[csf('buyer_name')]][$datastr]['shiping_status']	=$row[csf('shiping_status')];
								$data_array[$row[csf('buyer_name')]][$datastr]['shipment_date']		=$row[csf('shipment_date')];
								$data_array[$row[csf('buyer_name')]][$datastr]['ratio']				+=$row[csf('ratio')];
								$data_array[$row[csf('buyer_name')]][$datastr]['prod_id']			.=$row[csf('prod_id')].",";

								$data_array[$row[csf('buyer_name')]][$datastr]['receive_qnty']		+=$row[csf('receive_qnty')];
								$data_array[$row[csf('buyer_name')]][$datastr]['rec_trns_qnty']		+=$row[csf('rec_trns_qnty')];
								//$data_array[$row[csf('buyer_name')]][$datastr]['issue_qnty']		+=$row[csf('issue_qnty')];
								$data_array[$row[csf('buyer_name')]][$datastr]['issue_ret_qnty']	+=$row[csf('issue_ret_qnty')];
								$data_array[$row[csf('buyer_name')]][$datastr]['rec_ret_qnty']		+=$row[csf('rec_ret_qnty')];
								$data_array[$row[csf('buyer_name')]][$datastr]['issue_trns_qnty']	+=$row[csf('issue_trns_qnty')];
								$data_array[$row[csf('buyer_name')]][$datastr]['status_active']	     =$row[csf('status_active')];

								$prod_data_array[$row[csf('buyer_name')]][$datastr][$row[csf('prod_id')]]['receive_qnty']		+=$row[csf('receive_qnty')];
								$prod_data_array[$row[csf('buyer_name')]][$datastr][$row[csf('prod_id')]]['rec_trns_qnty']		+=$row[csf('rec_trns_qnty')];
								$prod_data_array[$row[csf('buyer_name')]][$datastr][$row[csf('prod_id')]]['issue_qnty']			+=$row[csf('issue_qnty')];
								$prod_data_array[$row[csf('buyer_name')]][$datastr][$row[csf('prod_id')]]['issue_ret_qnty']		+=$row[csf('issue_ret_qnty')];
								$prod_data_array[$row[csf('buyer_name')]][$datastr][$row[csf('prod_id')]]['rec_ret_qnty']		+=$row[csf('rec_ret_qnty')];
								$prod_data_array[$row[csf('buyer_name')]][$datastr][$row[csf('prod_id')]]['issue_trns_qnty']	+=$row[csf('issue_trns_qnty')];

								$prod_id_all.=$row[csf('prod_id')].",";

							}

							if($txt_search_booking =='')
							{
								$booking_po_data_arr=array();
								foreach ($nameArray as $row)
								{
									$po_break_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
									$color_id_arr[$row[csf('color_id')]]=$row[csf('color_id')];
								}
								$sql_booking_po_query=sql_select("select po_break_down_id, booking_no, fabric_color_id from wo_booking_dtls where po_break_down_id in(".implode(',',$po_break_id_arr).") and  fabric_color_id in(".implode(',',$color_id_arr).") and status_active=1 and is_deleted=0 and booking_type=1 ");

								foreach ($sql_booking_po_query as $row)
								{
									$booking_po_data_arr[$row[csf("po_break_down_id")]][$row[csf("fabric_color_id")]]['booking_no']=$row[csf("booking_no")];
								}
							}

							$prod_id_all=chop($prod_id_all," , ");
							$tot_rows=count(array_unique(explode(",",$prod_id_all)));
							$prod_id_cond="";

							if($db_type==2 && $tot_rows>1000)
							{
								$poIds_cond_pre=" and (";
								$poIds_cond_suff.=")";
								$poIdsArr=array_chunk(explode(",",$prod_id_all),999);
								foreach($poIdsArr as $ids)
								{
									$ids=implode(",",$ids);
									$prod_id_cond.=" prod_id in($ids) or ";
								}

								$prod_id_cond=$poIds_cond_pre.chop($prod_id_cond,'or ').$poIds_cond_suff;
							}
							else
							{
								$prod_id_cond=" and prod_id in($prod_id_all)";
							}

							$transaction_date_array=array();
							$sql_date="Select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where status_active=1 and is_deleted=0 and item_category=2 $prod_id_cond group by prod_id";
							//echo $sql_date;
							$sql_date_result=sql_select($sql_date);
							foreach( $sql_date_result as $row )
							{
								$transaction_date_array[$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
								$transaction_date_array[$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
							}
							unset($sql_date_result);



							foreach ($data_array as $buyer_name=>$buyer_data)
							{
								foreach ($buyer_data as $buyer_str => $row)
								{
									// $shistatus = $row["shiping_status"];
									// var_dump($shistatus);
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$dzn_qnty=0; $rec_qty=0;
									if($costing_per_id_library[$row['job_no']]==1) $dzn_qnty=12;
									else if($costing_per_id_library[$row['job_no']]==3) $dzn_qnty=12*2;
									else if($costing_per_id_library[$row['job_no']]==4) $dzn_qnty=12*3;
									else if($costing_per_id_library[$row['job_no']]==5) $dzn_qnty=12*4;
									else $dzn_qnty=1;

									$order_id=$row['po_id'];
									$color_id=$row["color_id"];
									$row["prod_id"]=chop($row["prod_id"],",");

									$issue_ret_qnty=$row['issue_ret_qnty'];
									$trans_in_qty=$row["rec_trns_qnty"];
									$rec_qty=($row["receive_qnty"]);

									$trans_out=($row["issue_trns_qnty"]);
									$iss_qty=$row["issue_qnty"];
									$issue_qnty_re_process=$row["issue_qnty_re_process"];
									$rec_ret_qnty=$row["rec_ret_qnty"];
									//$rec_ret_qnty=($rec_return_qnty[$row['po_id']][$row['color_id']]['rec_ret_qnty']);
									//echo $trans_in_qty.'+'.$rec_qty.'+'.$issue_ret_qnty.'<br>';

									$total_receive=$trans_in_qty+$rec_qty+$issue_ret_qnty;
									$total_issue=$trans_out+$iss_qty+$rec_ret_qnty+$issue_qnty_re_process;
									$stock = 0;
									$stock = $total_receive-$total_issue;

									$prod_str="";
									$prod_id_arr = array_unique(explode(",", $row["prod_id"]));
									if(!empty($prod_id_arr))
									{
										foreach ($prod_id_arr as $pval)
										{
											$prod_stock = ($prod_data_array[$buyer_name][$buyer_str][$pval]['receive_qnty'] + $prod_data_array[$buyer_name][$buyer_str][$pval]['issue_ret_qnty'] + $prod_data_array[$buyer_name][$buyer_str][$pval]['rec_trns_qnty']) - ($prod_data_array[$buyer_name][$buyer_str][$pval]['issue_qnty'] + $prod_data_array[$buyer_name][$buyer_str][$pval]['issue_trns_qnty'] + $prod_data_array[$buyer_name][$buyer_str][$pval]['rec_ret_qnty']);

											$prod_str .= "p=".$pval .", s=" .$prod_stock."; ";
										}

										$prod_str = chop($prod_str,"; ");
									}


									//$stock = number_format($stock,2);
									// echo $stock."<br>";
									// echo $value_for_search_by;
									// die('Go to hell');
									if($value_for_search_by==2 && $stock > 0)
									{
										if(!in_array($row["buyer_name"],$checkbuyerArr))
										{
											if($k!=1)
											{
												?>
												<tr class="tbl_bottom">
													<td colspan="8" align="right">Buyer Total</td>
													<td align="right"><? //echo number_format($po_quantity,2);$po_quantity=0; ?></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>

													<td align="right"><? echo number_format($subtot_book_qty,2); $subtot_book_qty=0;?></td>
			                                        <td align="right"><? echo number_format($subtot_trns_in_qnty,2); $subtot_trns_in_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_rec_qty,2); $subtot_rec_qty=0;?></td>

													<td align="right"><? echo number_format($subtot_issue_ret_qnty,2); $subtot_issue_ret_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_total_receive,2); $subtot_total_receive=0;?></td>
													<td align="right"><? echo number_format($subtot_rec_bal,2); $subtot_rec_bal=0;?></td>
			                                        <td align="right"><?  echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_iss_qty,2); $subtot_iss_qty=0;?></td>
													<td align="right"><? echo number_format($subtot_iss_reprocess_qty,2); $subtot_iss_reprocess_qty=0;?></td>
													<td align="right"><? echo number_format($subtot_iss_balance_qty,2); $subtot_iss_balance_qty=0;?></td>

													<td align="right"><? echo number_format($subtot_rec_ret_qnty,2); $subtot_rec_ret_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_total_issue,2); $subtot_total_issue=0;?></td>
													<td align="right"><? echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
													<td align="right"><? //echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
													<td align="right"><? //echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_possible_cut_pcs,2); $subtot_possible_cut_pcs=0;?></td>
													<td align="right"><? echo number_format($subtot_actual_qty,2); $subtot_actual_qty=0;?></td>
												</tr>
												<?
											}
											$checkbuyerArr[]=$row["buyer_name"];
											$k++;
										}
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30" align="center"><? echo $i; ?></td>
											<td width="70" align="center"><p><? echo $buyer_arr[$row["buyer_name"]]; ?></p></td>
											<td width="60" align="center"><p><? echo $row["year"]; ?> </p></td>
											<td width="60" align="center"><p><? echo $row["job_no_prefix_num"]; ?></p></td>

											<td width="100" align="center"><p><? echo $booking_po_data_arr[$row["po_id"]][$row["color_id"]]['booking_no']; ?></p></td>

											<td width="100" align="center" title="<? echo $row["po_id"] ;?>" ><p><? echo $row["po_number"]; ?></p></td>
											<td width="100" align="center" title="" ><p><? echo $row["grouping"]; ?></p></td>
											<td width="110" align="center"><p><? echo $row["style_ref_no"]; ?></p></td>
											<td width="80" align="right"><p><? $po_quantity+=$row["po_quantity"];echo number_format($row["po_quantity"],2,'.',''); ?></p></td>
											<td width="60" align="center"><a href='#report_details' onClick="openmypage_ex_factory('<? echo $row['po_id']; ?>','1');">View</a></td>
											<td width="90" align="center"><p><? echo $shipment_status[$row["shiping_status"]]; ?></p></td>
											<td width="90" align="center">
												<p><?
												if($row["shiping_status"] !=3 && $row["status_active"]=1)
												{
													echo "Running Order";
												}
												else if($row["status_active"]=3)
												{
													echo "Cancelled Order";
												}
												else if($row["shiping_status"] ==3 && $row["status_active"]=1)
												{
													echo "Left Over";
												}
												?></p>
											</td>
											<td width="90" align="center"><p><? echo change_date_format($row["shipment_date"]); ?></p></td>
											<td width="90" align="center" title="<? echo $row["color_id"] ;?>"><p><? echo $color_arr[$row["color_id"]]; ?></p></td>
											<td width="80" align="right">
												<?
												$book_qty=$booking_qnty[$row['po_id']][$row['color_id']]['fin_fab_qnty'];
												$subtot_book_qty+=$book_qty;
												echo number_format($book_qty,2,'.','');
												?>
											</td>
											<?
											$subtot_issue_ret_qnty+=$issue_ret_qnty;
											$subtot_trns_in_qnty+=$trans_in_qty;
											$subtot_trns_out_qnty+=$trans_out;
											$subtot_iss_qty+=$iss_qty;
											$subtot_iss_reprocess_qty+=$issue_qnty_re_process;
											$subtot_rec_ret_qnty+=$rec_ret_qnty;

											$subtot_total_receive+=$total_receive;
											$subtot_total_issue+=$total_issue;

											$subtot_stock+=$stock;
											$subtot_rec_qty+=$rec_qty;
											?>
			                                <td width="80" title="Trans. In" align="right"><? echo number_format($trans_in_qty,2,'.',''); ?></td>
											<td width="80" align="right"><? echo number_format($rec_qty,2,'.',''); ?></td>
											<td width="70" align="right">
												<a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>',2,'issue_ret_popup');">
													<? echo number_format($issue_ret_qnty,2,'.',''); ?>
												</a>
											</td>
											<td width="80" align="right">
												<a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>',1,'receive_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"><? echo number_format($total_receive,2,'.',''); ?>
												</a>
											</td>
											<td width="80" align="right"><p><? $rec_bal=$book_qty-$total_receive; $subtot_rec_bal+=$rec_bal;echo number_format($rec_bal,2,'.',''); ?></p></td>
											<td width="80" align="right"><? echo number_format($trans_out,2,'.',''); ?></td>
											<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row[csf('prod_id')]; ?>','<? echo $row['color_id']; ?>',5,'issue_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"> <? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
											<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row[csf('prod_id')]; ?>','<? echo $row['color_id']; ?>',5,'issue_reprocess_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"> <? echo number_format($issue_qnty_re_process,2,'.',''); ?></a></p></td>
											<td width="80" align="right"><p><? $iss_balance_qty=$book_qty-$iss_qty; $subtot_iss_balance_qty+=$iss_balance_qty; echo number_format($iss_balance_qty,2,'.',''); ?></p></td>

											<td width="70" align="right"><a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>',4,'receive_ret_popup');">
												<? echo number_format($rec_ret_qnty,2,'.',''); ?>
											</a>
											<td width="80" align="right"><? echo number_format($total_issue,2,'.',''); ?></td>
											<td width="80" align="right" title="<? echo $prod_str;?>">
												<p>
													<a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>',5,'stock_popup',<? echo $cbo_store_id;?>);">
														<? echo number_format($stock,2); ?>
													</a>
												</p>
											</td>
											<? $daysOnHand=datediff("d",change_date_format($transaction_date_array[$row['prod_id']]['max_date'],'','',1),date("Y-m-d")); ?>
											<td width="80" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
											<td width="80" align="center"><p><? $cons_per=($result_consumtion[$row['po_id']][$row['color_id']]); echo number_format($cons_per,8,'.',''); ?></p></td>
											<td width="80" align="right"><p><? $possible_cut_pcs=$total_issue/($cons_per);$subtot_possible_cut_pcs+=$possible_cut_pcs; echo ($cons_per!="")?number_format($possible_cut_pcs):"0"; ?></p></td>
											<td width="" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>','actual_cut_popup');"><? $actual_qty=$actual_cut_qnty[$row['po_id']][$row['color_id']]['actual_cut_qty'];$subtot_actual_qty+=$actual_qty; echo number_format($actual_qty,2,'.',''); ?></a>&nbsp;</p></td>
										</tr>
										<?
										$i++;
										$total_order_qnty+=$row["po_quantity"];
										$rec_trns_in_qnty+=$trans_in_qty;
										$total_req_qty+=$book_qty;
										$total_rec_qty+=$rec_qty;
										$total_receive_qnty+=$total_receive;
										$total_rec_bal+=$rec_bal;
										$issue_trns_out_qnty+=$trans_out;
										$total_issue_qty+=$iss_qty;
										$total_issue_qty_reprocess+=$issue_qnty_re_process;
										$total_issue_balance_qty+=$iss_balance_qty;
										$total_issue_quantity+=$total_issue;
										$total_stock+=$stock;
										$total_possible_cut_pcs+=$possible_cut_pcs;
										$total_rec_return_qnty+=$rec_ret_qnty;
										$total_issue_ret_qnty+=$issue_ret_qnty;
										$total_actual_cut_qty+=$actual_qty;
									}
									else if($value_for_search_by==1)
									{
										if(!in_array($row["buyer_name"],$checkbuyerArr))
										{
											if($k!=1)
											{
												?>
												<tr class="tbl_bottom">
													<td colspan="8" align="right">Buyer Total</td>
													<td align="right"><? //echo number_format($po_quantity,2);$po_quantity=0; ?></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>

													<td align="right"><? echo number_format($subtot_book_qty,2); $subtot_book_qty=0;?></td>
			                                        <td align="right"><? echo number_format($subtot_trns_in_qnty,2); $subtot_trns_in_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_rec_qty,2); $subtot_rec_qty=0;?></td>

													<td align="right"><? echo number_format($subtot_issue_ret_qnty,2); $subtot_issue_ret_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_total_receive,2); $subtot_total_receive=0;?></td>
													<td align="right"><? echo number_format($subtot_rec_bal,2); $subtot_rec_bal=0;?></td>
			                                        <td align="right"><?  echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_iss_qty,2); $subtot_iss_qty=0;?></td>
													<td align="right"><? echo number_format($subtot_iss_reprocess_qty,2); $subtot_iss_reprocess_qty=0;?></td>
													<td align="right"><? echo number_format($subtot_iss_balance_qty,2); $subtot_iss_balance_qty=0;?></td>

													<td align="right"><? echo number_format($subtot_rec_ret_qnty,2); $subtot_rec_ret_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_total_issue,2); $subtot_total_issue=0;?></td>
													<td align="right"><? echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
													<td align="right"><? //echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
													<td align="right"><? //echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
													<td align="right"><? echo number_format($subtot_possible_cut_pcs,2); $subtot_possible_cut_pcs=0;?></td>
													<td align="right"><? echo number_format($subtot_actual_qty,2); $subtot_actual_qty=0;?></td>
												</tr>
												<?
											}
											$checkbuyerArr[]=$row["buyer_name"];
											$k++;
										}
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30" align="center"><? echo $i; ?></td>
											<td width="70" align="center"><p><? echo $buyer_arr[$row["buyer_name"]]; ?></p></td>
											<td width="60" align="center"><p><? echo $row["year"]; ?> </p></td>
											<td width="60" align="center"><p><? echo $row["job_no_prefix_num"]; ?></p></td>

											<td width="100" align="center"><p><? echo $booking_po_data_arr[$row["po_id"]][$row["color_id"]]['booking_no']; ?></p></td>

											<td width="100" align="center" title="<? echo $row["po_id"] ;?>" ><p><? echo $row["po_number"]; ?></p></td>
											<td width="100" align="center" title="" ><p><? echo $row["grouping"]; ?></p></td>
											<td width="110" align="center"><p><? echo $row["style_ref_no"]; ?></p></td>
											<td width="80" align="right"><p><? $po_quantity+=$row["po_quantity"];echo number_format($row["po_quantity"],2,'.',''); ?></p></td>
											<td width="60" align="center"><a href='#report_details' onClick="openmypage_ex_factory('<? echo $row['po_id']; ?>','1');">View</a></td>
											<td width="90" align="center"><p><? echo $shipment_status[$row["shiping_status"]]; ?></p></td>
											<td width="90" align="center"><p><?
											if($row["shiping_status"] !=3 && $row["status_active"]=1)
											{
												echo "Running Order";
											}
											else if($row["status_active"]=3)
											{
												echo "Cancelled Order";
											}
											else if($row["shiping_status"] ==3 && $row["status_active"]=1)
											{
												echo "Left Over";
											}
											?></p></td>
											<td width="90" align="center"><p><? echo change_date_format($row["shipment_date"]); ?></p></td>
											<td width="90" align="center" title="<? echo $row["color_id"] ;?>"><p><? echo $color_arr[$row["color_id"]]; ?></p></td>
											<td width="80" align="right">
												<?
												$book_qty=$booking_qnty[$row['po_id']][$row['color_id']]['fin_fab_qnty'];
												$subtot_book_qty+=$book_qty;
												echo number_format($book_qty,2,'.','');
												?>
											</td>
											<?
											$subtot_issue_ret_qnty+=$issue_ret_qnty;
											$subtot_trns_in_qnty+=$trans_in_qty;
											$subtot_trns_out_qnty+=$trans_out;
											$subtot_iss_qty+=$iss_qty;
											$subtot_iss_reprocess_qty+=$issue_qnty_re_process;
											$subtot_rec_ret_qnty+=$rec_ret_qnty;

											$subtot_total_receive+=$total_receive;
											$subtot_total_issue+=$total_issue;

											$subtot_stock+=$stock;
											$subtot_rec_qty+=$rec_qty;
											?>
			                                <td width="80" title="Trans. In" align="right"><? echo number_format($trans_in_qty,2,'.',''); ?></td>
											<td width="80" align="right"><? echo number_format($rec_qty,2,'.',''); ?></td>
											<td width="70" align="right">
												<a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>',2,'issue_ret_popup');">
													<? echo number_format($issue_ret_qnty,2,'.',''); ?>
												</a>
											</td>
											<td width="80" align="right">
												<a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>',1,'receive_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"><? echo number_format($total_receive,2,'.',''); ?>
												</a>
											</td>
											<td width="80" align="right"><p><? $rec_bal=$book_qty-$total_receive; $subtot_rec_bal+=$rec_bal;echo number_format($rec_bal,2,'.',''); ?></p></td>
											<td width="80" align="right"><? echo number_format($trans_out,2,'.',''); ?></td>
											<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row[csf('prod_id')]; ?>','<? echo $row['color_id']; ?>',5,'issue_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"> <? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
											<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row[csf('prod_id')]; ?>','<? echo $row['color_id']; ?>',5,'issue_reprocess_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"> <? echo number_format($issue_qnty_re_process,2,'.',''); ?></a></p></td>
											<td width="80" align="right"><p><? $iss_balance_qty=$book_qty-$iss_qty; $subtot_iss_balance_qty+=$iss_balance_qty; echo number_format($iss_balance_qty,2,'.',''); ?></p></td>

											<td width="70" align="right">
											<a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row[csf('color_id')]; ?>',4,'receive_ret_popup');">
												<? echo number_format($rec_ret_qnty,2,'.',''); ?>
											</a>
											<td width="80" align="right"><? echo number_format($total_issue,2,'.',''); ?></td>
											<td width="80" align="right" title="<? echo $prod_str;?>">
												<p>
													<a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>',5,'stock_popup',<? echo $cbo_store_id;?>);">
														<? echo number_format($stock,2); ?>
													</a>
												</p>
											</td>
											<? $daysOnHand=datediff("d",change_date_format($transaction_date_array[$row['prod_id']]['max_date'],'','',1),date("Y-m-d")); ?>
											<td width="80" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
											<td width="80" align="center"><p><? $cons_per=($result_consumtion[$row['po_id']][$row['color_id']]); echo number_format($cons_per,8,'.',''); ?></p></td>
											<td width="80" align="right">
											<p>
											<?
											$possible_cut_pcs=$total_issue/($cons_per);$subtot_possible_cut_pcs+=$possible_cut_pcs;
											echo ($cons_per!="")?number_format($possible_cut_pcs):"0"; ?>

											</p>
											</td>
											<td width="" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row['po_id']; ?>','<? echo $row['prod_id']; ?>','<? echo $row['color_id']; ?>','actual_cut_popup');"><? $actual_qty=$actual_cut_qnty[$row['po_id']][$row['color_id']]['actual_cut_qty'];$subtot_actual_qty+=$actual_qty; echo number_format($actual_qty,2,'.',''); ?></a>&nbsp;</p></td>
										</tr>
										<?
										$i++;
										$total_order_qnty+=$row["po_quantity"];
										$rec_trns_in_qnty+=$trans_in_qty;
										$total_req_qty+=$book_qty;
										$total_rec_qty+=$rec_qty;
										$total_receive_qnty+=$total_receive;
										$total_rec_bal+=$rec_bal;
										$issue_trns_out_qnty+=$trans_out;
										$total_issue_qty+=$iss_qty;
										$total_issue_qty_reprocess+=$issue_qnty_re_process;
										$total_issue_balance_qty+=$iss_balance_qty;
										$total_issue_quantity+=$total_issue;
										$total_stock+=$stock;
										$total_possible_cut_pcs+=$possible_cut_pcs;
										$total_rec_return_qnty+=$rec_ret_qnty;
										$total_issue_ret_qnty+=$issue_ret_qnty;
										$total_actual_cut_qty+=$actual_qty;
									}

								}
							}

							?>
							<tr class="tbl_bottom">
								<td colspan="8" align="right">Buyer Total</td>
								<td align="right"><? //echo number_format($po_quantity,2);$po_quantity=0; ?></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>

								<td align="right" ><? echo number_format($subtot_book_qty,2); $subtot_book_qty=0;?></td>
	                            <td align="right"><? echo number_format($subtot_trns_in_qnty,2); $subtot_trns_in_qnty=0;?></td>
								<td align="right"><? echo number_format($subtot_rec_qty,2); $subtot_rec_qty=0;?></td>

								<td align="right"><? echo number_format($subtot_issue_ret_qnty,2); $subtot_issue_ret_qnty=0;?></td>
								<td align="right"><? echo number_format($subtot_total_receive,2); $subtot_total_receive=0;?></td>
								<td align="right"><? echo number_format($subtot_rec_bal,2); $subtot_rec_bal=0;?></td>
	                            <td align="right"><? echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
								<td align="right"><? echo number_format($subtot_iss_qty,2); $subtot_iss_qty=0;?></td>
								<td align="right"><? echo number_format($subtot_iss_reprocess_qty,2); $subtot_iss_reprocess_qty=0;?></td>
								<td align="right"><? echo number_format($subtot_iss_balance_qty,2); $subtot_iss_balance_qty=0;?></td>

								<td align="right"><? echo number_format($subtot_rec_ret_qnty,2); $subtot_rec_ret_qnty=0;?></td>
								<td align="right"><? echo number_format($subtot_total_issue,2); $subtot_total_issue=0;?></td>
								<td align="right"><? echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
								<td align="right"><? //echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
								<td align="right"><? //echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
								<td align="right"><? echo number_format($subtot_possible_cut_pcs,2); $subtot_possible_cut_pcs=0;?></td>
								<td align="right"><? echo number_format($subtot_actual_qty,2); $subtot_actual_qty=0;?></td>
							</tr>
						</table>
						<table width="2490" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
							<tfoot>
								<th width="30"></th>
								<th width="70"></th>
								<th width="60"></th>
								<th width="60"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="110">Grand Total</th>
								<th width="80" align="right" id=""><? //echo number_format($total_order_qnty,2,'.',''); ?></th>
								<th width="60"></th>
								<th width="90"></th>
								<th width="90"></th>
								<th width="90"></th>
								<th width="90"></th>
								<th width="80" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
                                <th width="80" align="right" id="value_total_tranin_qty"><? echo number_format($rec_trns_in_qnty,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
								<th width="70" align="right" id="value_total_issue_ret_qty"><? echo number_format($total_issue_ret_qnty,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_receive_qty"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>
                                <th width="80" align="right" id="value_total_tranout_qty"><? echo number_format($issue_trns_out_qnty,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_issue_qty_reprocess"><? echo number_format($total_issue_qty_reprocess,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_issue_balance_qty"><? echo number_format($total_issue_balance_qty,2,'.',''); ?></th>

								<th width="70"  id="value_recv_ret_qty"><? echo number_format($total_rec_return_qnty,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_issue_quantity"><? echo number_format($total_issue_quantity,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_stock"><? //echo number_format($total_stock,2,'.',''); ?></th>
								<th width="80">&nbsp;</th>
								<th width="80" align="right" id="total_possible_cut_pcs"><? echo number_format($total_possible_cut_pcs,2,'.',''); ?></th>
								<th width="" align="right" id="total_actual_cut_qty"><? echo number_format($total_actual_cut_qty,2,'.',''); ?></th>
							</tfoot>
						</table>
					</div>
				</fieldset>
				<?
			}
			else // style wise
			{
				$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
				?>
				<fieldset style="width:2010px;">
					<table cellpadding="0" cellspacing="0" width="1930">
						<tr class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="20" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
						</tr>
						<tr  class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
						</tr>
						<tr  class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="20" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from_st)) ;?></strong></td>
						</tr>
					</table>
					<table width="2000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
						<thead>
							<th width="30">SL</th>
							<th width="70">Buyer</th>
							<th width="60">Job</th>
							<th width="100">Booking</th>
							<th width="50">Year</th>
							<th width="110">Style</th>
							<th width="90">Job Qty. (Pcs)</th>
							<th width="60">Order Status</th>
							<th width="110">Fin. Color</th>
							<th width="90">Req. Qty</th>
							<th width="80" title="Trans. in">Trans. In</th>
							<th width="80" title="Received">Received</th>
							<th width="70" title="Issue Return">Issue Return</th>
							<th width="80" title="Rec.+Issue Rtn.+Trans. in">Total Received</th>
							<th width="80" title="Req.-Totat Rec.">Received Balance</th>
	                        <th width="80" title="Trans. out">Trans Out</th>
							<th width="80" title="Issued">Issued</th>
							<th width="80" title="Issued">Issued to Reprocess</th>
							<th width="80" title="Issued Balance">Issue Balance</th>
							<th width="70" title="Recv. Return">Recv. Return</th>
							<th width="80" title="Issue+Rec. Ret.+Trans. out">Total Issued</th>
							<th width="80" title="Total Rec.- Total Issue">Stock</th>

							<th width="100">Consumption Pcs.</th>
							<th width="80">Possible Cut Pcs.</th>
							<th>Actual Cut</th>
						</thead>
					</table>
					<div style="width:2020px; max-height:350px; overflow-y:scroll;" id="scroll_body">
						<table width="2000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
							<?
							$po_break_id_con=''; $booking_po_data_arr=array();
							if($txt_search_booking!='')
							{
								$sql_booking_query=sql_select("select b.job_no_mst, d.booking_no, d.fabric_color_id from wo_po_break_down b, wo_booking_dtls d where  d.po_break_down_id=b.id and d.booking_no like'%$txt_search_booking%'and b.status_active=1 and b.is_deleted=0 and d.status_active=1");

								$bookArray=sql_select($sql_booking_query); $booking_po_id_arr=array(); $booking_po_data_arr=array();
								foreach ($bookArray as $row)
								{
									$booking_po_id_arr[]="'".$row[csf('job_no_mst')]."'";
									$booking_po_data_arr[$row[csf("job_no_mst")]][$row[csf("fabric_color_id")]]['booking_no']=$row[csf("booking_no")];
								}
								$po_break_id_con = " and a..job_no in(".implode(',',$booking_po_id_arr).")";
								$booking_po_data_arr[$row[csf("job_no_mst")]][$row[csf("fabric_color_id")]]['booking_no'].=$row[csf("booking_no")].",";
							}
							if($db_type==0)
							{
								$select_fld= "year(a.insert_date)as year";
								$sql_query="Select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, group_concat(b.id) as po_id, sum(b.plan_cut*a.total_set_qnty) as po_quantity, $select_fld,
								sum(case when d.entry_form in (7,37,66,68) then d.quantity else 0 end) as receive_qnty,
								sum(case when d.entry_form in(14,15,134) and d.trans_type=5 then d.quantity else 0 end) as rec_trns_qnty,
								sum(case when d.entry_form in (18,71) then d.quantity else 0 end) as issue_qnty,
								sum(case when d.entry_form in (126,52) then d.quantity else 0 end) as issue_ret_qnty,
								sum(case when d.entry_form in(14,15,134) and d.trans_type=6 then d.quantity else 0 end) as issue_trns_qnty,
								d.color_id
								from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d
								where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1
								and d.entry_form in (7,37,66,68,14,15,18,71,126,134,52) and c.item_category=2 and c.status_active=1 and c.is_deleted=0 and c.id=d.trans_id and d.status_active=1 and d.is_deleted=0 and d.po_breakdown_id=b.id and
								a.company_name=$cbo_company_id $po_break_id_con $receive_date $buyer_id_cond $store_id_cond $shipment_id_cond $search_cond $year_cond
								group by a.job_no_prefix_num, a.job_no, a.insert_date, a.buyer_name, a.style_ref_no, a.total_set_qnty, d.color_id order by a.buyer_name, a.job_no";
							}

							else if($db_type==2)
							{
								$select_fld= "TO_CHAR(a.insert_date,'YYYY') as year";

								$sql_query_issue=sql_select("SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, LISTAGG(b.id, ',') 	WITHIN GROUP (ORDER BY b.id) as po_id, sum(b.plan_cut*a.total_set_qnty) as po_quantity, $select_fld,

								sum(case when d.entry_form in (18,71) and f.issue_purpose!=44 then d.quantity else 0 end) as issue_qnty,
								sum(case when d.entry_form in (18,71) and f.issue_purpose=44 then d.quantity else 0 end) as issue_qnty_re_process,
								d.color_id
								from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d,inv_issue_master f
								where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1
								and d.entry_form in (18,71) and c.item_category=2 and c.status_active=1 and c.is_deleted=0 and c.id=d.trans_id and c.mst_id=f.id and f.entry_form in (18,71) and d.trans_id!=0 and d.status_active=1 and d.is_deleted=0 and d.po_breakdown_id=b.id  and
								a.company_name=$cbo_company_id $po_break_id_con $receive_date $buyer_id_cond $store_id_cond $shipment_id_cond $search_cond $year_cond
								group by a.job_no_prefix_num, a.job_no, a.insert_date, a.buyer_name, a.style_ref_no, a.total_set_qnty, d.color_id order by a.buyer_name, a.job_no");
								foreach ($sql_query_issue as $row)
								{
									$issueQnty_arr[$row[csf('job_no')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('color_id')]]['issue_qnty']=$row[csf('issue_qnty')];
									$issueQnty_arr[$row[csf('job_no')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('color_id')]]['issue_qnty_re_process']=$row[csf('issue_qnty_re_process')];
								}
								/*echo "<pre>";
								print_r($issueQnty_arr);
								echo "</pre>";*/
								$sql_query="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, LISTAGG(b.id, ',') 	WITHIN GROUP (ORDER BY b.id) as po_id, sum(b.plan_cut*a.total_set_qnty) as po_quantity, $select_fld,
								sum(case when d.entry_form in (7,37,66,68) then d.quantity else 0 end) as receive_qnty,
								sum(case when d.entry_form in(14,15,134) and d.trans_type=5 then d.quantity else 0 end) as rec_trns_qnty,
								sum(case when d.entry_form in (126,52) then d.quantity else 0 end) as issue_ret_qnty,
								sum(case when d.entry_form in(14,15,134) and d.trans_type=6 then d.quantity else 0 end) as issue_trns_qnty,d.color_id
								from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d
								where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1
								and d.entry_form in (7,37,66,68,14,15,18,71,126,134,52) and c.item_category=2 and c.status_active=1 and c.is_deleted=0 and c.id=d.trans_id and d.trans_id!=0 and d.status_active=1 and d.is_deleted=0 and d.po_breakdown_id=b.id  and
								a.company_name=$cbo_company_id $po_break_id_con $receive_date $buyer_id_cond $store_id_cond $shipment_id_cond $search_cond $year_cond
								group by a.job_no_prefix_num, a.job_no, a.insert_date, a.buyer_name, a.style_ref_no, a.total_set_qnty, d.color_id order by a.buyer_name, a.job_no";
							}

	                		//echo $sql_query;
							$i=1; $k=1;$total_rec_return_qnty=0;$total_issue_ret_qnty=0;$total_tran_in_qnty=0;$total_tran_out_qnty=0;
							$nameArray=sql_select($sql_query); $fin_color_array=array(); $fin_color_data_arr=array();$checkbuyerArr=array();
							if($txt_search_booking =='')
							{
								$po_break_id_arr=array();
								foreach ($nameArray as $row)
								{
									$po_break_id_arr[]="'".$row[csf('job_no')]."'";
									$color_id_arr[]=$row[csf('color_id')];
								}
								$sql_booking_po_query=sql_select("select b.job_no_mst, d.booking_no, d.fabric_color_id from wo_po_break_down b, wo_booking_dtls d where  d.po_break_down_id=b.id and b.job_no_mst in(".implode(',',$po_break_id_arr).") and d.fabric_color_id in(".implode(',',$color_id_arr).") and b.status_active=1 and b.is_deleted=0 and d.status_active=1");
								foreach ($sql_booking_po_query as $row)
								{
									$booking_po_data_arr[$row[csf("job_no_mst")]][$row[csf("fabric_color_id")]]['booking_no'].=$row[csf("booking_no")].",";
								}
							}

							foreach ($nameArray as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$dzn_qnty=0; $rec_qty=0; $orderToissueQty=0; $orderTorecQty=0; $book_qty=0; $issue_ret_qnty=0; $iss_qty=0; $actual_qty=0;$cons_per=0;$receive_ret_qnty=0;
								if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
								else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
								else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
								else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
								else $dzn_qnty=1;

								$color_id=$row[csf("color_id")];
								//echo 'PO ID='.$row[csf('po_id')].', Color='.$color_id=$row[csf("color_id")].'<br>';
								$po_ids=array_unique(explode(",",$row[csf('po_id')]));
								$rec_ret_qnty=0;
								foreach($po_ids as $po_id)
								{
									$book_qty+=$booking_qnty[$po_id][$color_id]['fin_fab_qnty'];
									$issue_ret_qnty+=$iss_return_qnty[$po_id][$color_id]['issue_ret_qnty'];
									$actual_qty+=$actual_cut_qnty[$po_id][$color_id]['actual_cut_qty'];

									$cons_per+=$result_consumtion[$po_id][$color_id];
									$rec_ret_qnty+=$rec_return_qnty[$po_id][$color_id]['rec_ret_qnty'];
								}
								$po_ids=implode(",",$po_ids);

								$trans_in_qty=$row[csf("rec_trns_qnty")];
								$rec_qty=($row[csf("receive_qnty")]);
								$trans_out=($row[csf("issue_trns_qnty")]);
								//echo $row[csf('job_no')]."=".$row[csf('buyer_name')]."=".$row[csf('style_ref_no')]."=".$row[csf('color_id')]."<br>";
								$iss_qty=$issueQnty_arr[$row[csf('job_no')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('color_id')]]['issue_qnty'];
								$issue_qnty_re_process=$issueQnty_arr[$row[csf('job_no')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('color_id')]]['issue_qnty_re_process'];
								$total_receive=$trans_in_qty+$rec_qty+$issue_ret_qnty;
								$total_issue=$trans_out+$iss_qty+$rec_ret_qnty+$issue_qnty_re_process;
								$stock=$total_receive-$total_issue;
								$stock = number_format($stock,2,'.','');

								if($value_for_search_by==2 && $stock > 0)
								{
									if(!in_array($row[csf("buyer_name")],$checkbuyerArr))
									{
										if($k!=1) // Buyer wise sum/total here------------
										{
											?>
											<tr class="tbl_bottom">
												<td colspan="6" align="right">Buyer Total</td>
												<td align="right"><? //echo number_format($po_quantity,2);$po_quantity=0; ?></td>
												<td></td>
												<td></td>
												<td><? echo number_format($subtot_book_qty,2); $subtot_book_qty=0;?></td>
												<td align="right"><? echo number_format($subtot_trns_in_qnty,2); $subtot_trns_in_qnty=0;?></td>
												<td align="right"><? echo number_format($subtot_rec_qty,2); $subtot_rec_qty=0;?></td>

												<td align="right"><? echo number_format($subtot_issue_ret_qnty,2); $subtot_issue_ret_qnty=0;?></td>
												<td align="right"><? echo number_format($subtot_total_receive,2); $subtot_total_receive=0;?></td>
												<td align="right"><? echo number_format($subtot_rec_bal,2); $subtot_rec_bal=0;?></td>
		                                        <td align="right"><?  echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
												<td align="right"><? echo number_format($subtot_rec_ret_qnty,2); $subtot_rec_ret_qnty=0;?></td>
												<td align="right"><? echo number_format($subtot_total_issue,2); $subtot_total_issue=0;?></td>
												<td align="right"><? echo number_format($subtot_total_issue_reprocess,2); $subtot_total_issue_reprocess=0;?></td>
												<td align="right"><? echo number_format($subtot_issue_bal,2); $subtot_issue_bal=0;?></td>
												<td align="right"><? echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
												<td align="right"></td>
												<td align="right"><? echo number_format($subtot_possible_cut_pcs,2); $subtot_possible_cut_pcs=0;?></td>

												<td align="right"><? echo number_format($subtot_actual_qty,2); $subtot_actual_qty=0;?></td>
											</tr>
											<?
										}
										$checkbuyerArr[]=$row[csf("buyer_name")];
										$k++;
									}
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="70"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
										<td width="60" align="center"><p><? echo $row[csf("job_no_prefix_num")]; ?></p></td>
										<td width="100" align="center"><p><? echo chop($booking_po_data_arr[$row[csf("job_no")]][$color_id]['booking_no'],","); ?></p></td>
										<td width="50" align="center"><p><? echo $row[csf("year")]; ?></p></td>
										<td width="110" style="word-break:break-all"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
										<td width="90" align="right"><p><? $po_quantity+=$row[csf("po_quantity")];echo $row[csf("po_quantity")]; ?>&nbsp;</p></td>
										<td width="60" align="center"><a href='#report_details' onClick="openmypage_ex_factory('<? echo $po_ids; ?>','2');">View</a></td>
										<td width="110" style="word-break:break-all"><p><? echo $color_arr[$color_id]; ?></p></td>
										<td width="90" align="right"><p><? $subtot_book_qty+=$book_qty;echo number_format($book_qty,2,'.','');//$booking_qty,2,'.',''); ?>&nbsp;</p></td>
										<?

										$subtot_issue_ret_qnty+=$issue_ret_qnty;
										$subtot_trns_in_qnty+=$trans_in_qty;
										$subtot_trns_out_qnty+=$trans_out;
										$subtot_iss_qty+=$iss_qty;
										$subtot_total_issue_reprocess+=$issue_qnty_re_process;
										$subtot_rec_ret_qnty+=$rec_ret_qnty;

										$subtot_total_receive+=$total_receive;
										$subtot_total_issue+=$total_issue;
										//$iss_qty_cal=$row[csf("issue_qnty")]+$rec_return_qnty[$row[csf('po_id')]][$row[csf('color_id')]]['rec_ret_qnty']+$row[csf("issue_trns_qnty")];
										$subtot_stock+=$stock;
										$subtot_rec_qty+=$rec_qty;
										?>
										<td width="80" title="Trans. In" align="right"><? echo number_format($trans_in_qty,2,'.',''); ?></td>
										<td width="80" align="right"><? echo number_format($rec_qty,2,'.',''); ?></td>
										<td width="70" align="right">
											<a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',2,'issue_ret_popup');">
												<? echo number_format($issue_ret_qnty,2,'.',''); ?>
											</a>
										</td>
										<td width="80" align="right">
											<a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',1,'receive_popup');"><? echo number_format($total_receive,2,'.',''); ?>
											</a>
										</td>

										<td width="80" align="right"><p><? $rec_bal=$book_qty-$total_receive; $subtot_rec_bal+=$rec_bal;echo number_format($rec_bal,2,'.',''); ?></p></td>
										<td width="80" align="right"><? echo number_format($trans_out,2,'.',''); ?></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',5,'issue_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"> <? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p><? echo number_format($issue_qnty_re_process,2,'.',''); ?></p></td>
										<td width="80" align="right"><p><? $issue_bal=$book_qty-$iss_qty; $subtot_issue_bal+=$issue_bal;echo number_format($issue_bal,2,'.',''); ?></p></td>

										<td width="70" align="right">
										<a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',4,'receive_ret_popup');">
											<? echo number_format($rec_ret_qnty,2,'.',''); ?>
										</a>
										<td width="80" align="right"><? echo number_format($total_issue,2,'.',''); ?></td>
										<td width="80" align="right" title="<? echo "Receive: ".$row[csf("receive_qnty")]."##".$issue_ret_qnty."##".$row[csf("rec_trns_qnty")]."Issue: ".$row[csf("issue_qnty")]."##".$receive_ret_qnty."##".$row[csf("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',5,'knit_stock_popup');"><? echo number_format($stock,2,'.',''); ?></a></p></td>
										<td width="100" align="right"><p><? echo number_format($cons_per,8,'.',''); ?></p></td>
										<td width="80" align="right"><p><? $possible_cut_pcs=$iss_qty_cal/($cons_per); $subtot_possible_cut_pcs+=$possible_cut_pcs;echo number_format($possible_cut_pcs); ?></p></td>
										<td width="" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>','actual_cut_popup');"><? echo $actual_qty;$subtot_actual_qty+=$actual_qty; ?></a>&nbsp;</p></td>
									</tr>
									<?
									$i++;
									$total_order_qnty+=$row[csf("po_quantity")];
									$rec_trns_in_qnty+=$trans_in_qty;
									$total_req_qty+=$book_qty;
									$total_rec_qty+=$rec_qty;
									$total_receive_qnty+=$total_receive;
									$total_rec_bal+=$rec_bal;
									$total_issue_bal+=$issue_bal;
									$issue_trns_out_qnty+=$trans_out;
									$total_issue_qty+=$iss_qty;
									$total_issue_qty_reprocess+=$issue_qnty_re_process;
									$total_issue_quantity+=$total_issue;
									$total_stock+=$stock;
									$total_possible_cut_pcs+=$possible_cut_pcs;
									$total_actual_cut_qty+=$actual_qty;
									$total_rec_return_qnty+=$receive_ret_qnty;
									$total_issue_ret_qnty+=$issue_ret_qnty;
								}
								else if($value_for_search_by==1)
								{
									if(!in_array($row[csf("buyer_name")],$checkbuyerArr))
									{
										if($k!=1) // Buyer wise sum/total here------------
										{
											?>
											<tr class="tbl_bottom">
												<td colspan="6" align="right">Buyer Total</td>
												<td align="right"><? //echo number_format($po_quantity,2);$po_quantity=0; ?></td>
												<td></td>
												<td></td>
												<td><? echo number_format($subtot_book_qty,2); $subtot_book_qty=0;?></td>
												<td align="right"><? echo number_format($subtot_trns_in_qnty,2); $subtot_trns_in_qnty=0;?></td>
												<td align="right"><? echo number_format($subtot_rec_qty,2); $subtot_rec_qty=0;?></td>

												<td align="right"><? echo number_format($subtot_issue_ret_qnty,2); $subtot_issue_ret_qnty=0;?></td>
												<td align="right"><? echo number_format($subtot_total_receive,2); $subtot_total_receive=0;?></td>
												<td align="right"><? echo number_format($subtot_rec_bal,2); $subtot_rec_bal=0;?></td>
									            <td align="right"><?  echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
												<td align="right"><? echo number_format($subtot_total_issue,2); $subtot_total_issue=0;?></td>
												<td align="right"><? echo number_format($subtot_total_issue_reprocess,2); $subtot_total_issue_reprocess=0;?></td>
												<td align="right"><? echo number_format($subtot_issue_bal,2); $subtot_issue_bal=0;?></td>

												<td align="right"><? echo number_format($subtot_rec_ret_qnty,2); $subtot_rec_ret_qnty=0;?></td>
												<td align="right"></td>
												<td align="right"><? echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
												<td align="right"><? echo number_format($total_issue_quantity,2); $total_issue_quantity=0;?></td>
												<td align="right"><? echo number_format($subtot_possible_cut_pcs,2); $subtot_possible_cut_pcs=0;?></td>

												<td align="right"><? echo number_format($subtot_actual_qty,2); $subtot_actual_qty=0;?></td>
											</tr>
											<?
										}
										$checkbuyerArr[]=$row[csf("buyer_name")];
										$k++;
									}
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="70"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
										<td width="60" align="center"><p><? echo $row[csf("job_no_prefix_num")]; ?></p></td>
										<td width="100" align="center"><p><? echo chop($booking_po_data_arr[$row[csf("job_no")]][$color_id]['booking_no'],","); ?></p></td>
										<td width="50" align="center"><p><? echo $row[csf("year")]; ?></p></td>
										<td width="110" style="word-break:break-all"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
										<td width="90" align="right"><p><? $po_quantity+=$row[csf("po_quantity")];echo $row[csf("po_quantity")]; ?>&nbsp;</p></td>
										<td width="60" align="center"><a href='#report_details' onClick="openmypage_ex_factory('<? echo $po_ids; ?>','2');">View</a></td>
										<td width="110" style="word-break:break-all"><p><? echo $color_arr[$color_id]; ?></p></td>
										<td width="90" align="right"><p><? $subtot_book_qty+=$book_qty;echo number_format($book_qty,2,'.','');//$booking_qty,2,'.',''); ?>&nbsp;</p></td>
										<?

										$subtot_issue_ret_qnty+=$issue_ret_qnty;
										$subtot_trns_in_qnty+=$trans_in_qty;
										$subtot_trns_out_qnty+=$trans_out;
										$subtot_iss_qty+=$iss_qty;
										$subtot_total_issue_reprocess+=$issue_qnty_re_process;
										$subtot_rec_ret_qnty+=$rec_ret_qnty;

										$subtot_total_receive+=$total_receive;
										$subtot_total_issue+=$total_issue;
										//$iss_qty_cal=$row[csf("issue_qnty")]+$rec_return_qnty[$row[csf('po_id')]][$row[csf('color_id')]]['rec_ret_qnty']+$row[csf("issue_trns_qnty")];
										$subtot_stock+=$stock;
										$subtot_rec_qty+=$rec_qty;
										?>
										<td width="80" title="Trans. In" align="right"><? echo number_format($trans_in_qty,2,'.',''); ?></td>
										<td width="80" align="right"><? echo number_format($rec_qty,2,'.',''); ?></td>
										<td width="70" align="right">
											<a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',2,'issue_ret_popup');">
												<? echo number_format($issue_ret_qnty,2,'.',''); ?>
											</a>
										</td>
										<td width="80" align="right">
											<a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',1,'receive_popup');"><? echo number_format($total_receive,2,'.',''); ?>
											</a>
										</td>

										<td width="80" align="right"><p><? $rec_bal=$book_qty-$total_receive; $subtot_rec_bal+=$rec_bal;echo number_format($rec_bal,2,'.',''); ?></p></td>
										<td width="80" align="right"><? echo number_format($trans_out,2,'.',''); ?></td>
										<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',5,'issue_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"> <? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
										<td width="80" align="right"><p> <? echo number_format($issue_qnty_re_process,2,'.',''); ?></p></td>
										<td width="80" align="right"><p><? $issue_bal=$book_qty-$iss_qty; $subtot_issue_bal+=$issue_bal;echo number_format($issue_bal,2,'.',''); ?></p></td>

										<td width="70" align="right" title="<? echo 'PO: '.$po_ids.', Color: '.$color_id; ?>">
										<a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',4,'receive_ret_popup');">
											<? echo number_format($rec_ret_qnty,2,'.',''); ?>
										</a></td>
										<td width="80" align="right"><? echo number_format($total_issue,2,'.',''); ?></td>
										<td width="80" align="right" title="<? echo "Receive: ".$row[csf("receive_qnty")]."##".$issue_ret_qnty."##".$row[csf("rec_trns_qnty")]."Issue: ".$row[csf("issue_qnty")]."##".$receive_ret_qnty."##".$row[csf("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',5,'knit_stock_popup');"><? echo number_format($stock,2,'.',''); ?></a></p></td>
										<td width="100" align="right"><p><? echo number_format($cons_per,8,'.',''); ?></p></td>
										<td width="80" align="right"><p><? $possible_cut_pcs=$iss_qty_cal/($cons_per); $subtot_possible_cut_pcs+=$possible_cut_pcs;echo number_format($possible_cut_pcs); ?></p></td>
										<td width="" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>','actual_cut_popup');"><? echo $actual_qty;$subtot_actual_qty+=$actual_qty; ?></a>&nbsp;</p></td>
									</tr>
									<?
									$i++;
									$total_order_qnty+=$row[csf("po_quantity")];
									$rec_trns_in_qnty+=$trans_in_qty;
									$total_req_qty+=$book_qty;
									$total_rec_qty+=$rec_qty;
									$total_receive_qnty+=$total_receive;
									$total_rec_bal+=$rec_bal;
									$total_issue_bal+=$issue_bal;
									$issue_trns_out_qnty+=$trans_out;
									$total_issue_qty+=$iss_qty;
									$total_issue_qty_reprocess+=$issue_qnty_re_process;
									$total_issue_quantity+=$total_issue;
									$total_stock+=$stock;
									$total_possible_cut_pcs+=$possible_cut_pcs;
									$total_actual_cut_qty+=$actual_qty;
									$total_rec_return_qnty+=$receive_ret_qnty;
									$total_issue_ret_qnty+=$issue_ret_qnty;
								}

							}
							?>
							<tr class="tbl_bottom">
								<td colspan="6" align="right">Buyer Total</td>
								<td align="right"><? //echo number_format($po_quantity,2);$po_quantity=0; ?></td>
								<td></td>
								<td></td>
								<td><? echo number_format($subtot_book_qty,2); $subtot_book_qty=0;?></td>
								<td align="right"><? echo number_format($subtot_trns_in_qnty,2); $subtot_trns_in_qnty=0;?></td>
								<td align="right"><? echo number_format($subtot_rec_qty,2); $subtot_rec_qty=0;?></td>

								<td align="right"><? echo number_format($subtot_issue_ret_qnty,2); $subtot_issue_ret_qnty=0;?></td>
								<td align="right"><? echo number_format($subtot_total_receive,2); $subtot_total_receive=0;?></td>
								<td align="right"><? echo number_format($subtot_rec_bal,2); $subtot_rec_bal=0;?></td>
								<td align="right"><?  echo number_format($subtot_trns_out_qnty,2); $subtot_trns_out_qnty=0;?></td>
						        <td align="right"><? echo number_format($subtot_total_issue,2); $subtot_total_issue=0;?></td>
						        <td align="right"><? echo number_format($subtot_total_issue_reprocess,2); $subtot_total_issue_reprocess=0;?></td>
						        <td align="right"><? echo number_format($subtot_issue_bal,2); $subtot_issue_bal=0;?></td>

								<td align="right"><? echo number_format($subtot_rec_ret_qnty,2); $subtot_rec_ret_qnty=0;?></td>
								<td align="right"><? echo number_format($total_issue_quantity,2); $total_issue_quantity=0;?></td>
								<td align="right"><? echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
								<td align="right"></td>
								<td align="right"><? echo number_format($subtot_possible_cut_pcs,2); $subtot_possible_cut_pcs=0;?></td>

								<td align="right"><? echo number_format($subtot_actual_qty,2); $subtot_actual_qty=0;?></td>
							</tr>
						</table>
					</div>
					<table width="2000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
						<tfoot>
							<th width="30"></th>
							<th width="70"></th>
							<th width="60"></th>
							<th width="100"></th>
							<th width="50"></th>
							<th width="110">Grand Total</th>
							<th width="90" align="right" id=""><? //echo number_format($total_order_qnty,2,'.',''); ?></th>
							<th width="60"></th>
							<th width="110">&nbsp;</th>
							<th width="90" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
	                        <th width="80" align="right" id="value_total_tranin_qty"><? echo number_format($rec_trns_in_qnty,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
							<th width="70" align="right" id="value_total_issue_ret_qty"><? echo number_format($total_issue_ret_qnty,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_receive_qty"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>
	                        <th width="80" align="right" id="value_total_tranout_qty"><? echo number_format($issue_trns_out_qnty,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_issue_qty_reprocess"><? echo number_format($total_issue_qty_reprocess,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_issue_balance_qty"><? echo number_format($total_issue_bal,2,'.',''); ?></th>
							<th width="70"  id="value_recv_ret_qty"><? echo number_format($total_rec_return_qnty,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_issue_quantity"><? echo number_format($total_issue_quantity,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
							<th width="100">&nbsp;</th>
							<th width="80" align="right" id="total_possible_cut_pcs"><? echo number_format($total_possible_cut_pcs,0,'.',''); ?></th>
							<th width="" align="right" id="total_actual_cut_qty"><? echo $total_actual_cut_qty; ?></th>
						</tfoot>
					</table>
				</fieldset>
				<?
			}
		}
		else if($cbo_report_type==2) // Woven Finish Start
		{
			?>
			<div style="color: red;font-size: 18px;text-align: center;font-weight: bold;">This report only for Knit Finish Fabric</div>
			<?
			 die;
			$search_cond ="";
			if ($txt_search_comm !="")
			{
				if($cbo_search_by==1)
				{
					$search_cond=" and a.job_no_prefix_num in ($txt_search_comm) ";
				}
				elseif($cbo_search_by==2)
				{
					$search_cond=" and a.style_ref_no like '%$txt_search_comm%'";
				}
				elseif($cbo_search_by==3)
				{
					$search_cond=" and b.po_number like '%$txt_search_comm%'";
				}
				elseif($cbo_search_by==4)
				{
					$search_cond=" and b.file_no like '%$txt_search_comm%'";
				}
				elseif($cbo_search_by==2)
				{
					$search_cond=" and b.grouping like '%$txt_search_comm%'";
				}
			}

			if($cbo_year){
				if($db_type==0)
				{
					$year_cond=" and year(a.insert_date) in($cbo_year)";
				}
				else
				{
					$year_cond=" and TO_CHAR(a.insert_date,'YYYY') in($cbo_year)";
				}
			}

			$booking_qnty=array();
			if($txt_search_booking!="")
			{
				$sql_booking=sql_select("select b.po_break_down_id,b.fabric_color_id, sum(b.fin_fab_qnty ) as fin_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=3 and a.booking_type=1 and a.company_id=$cbo_company_id and a.booking_no like '%".$txt_search_booking."%' and a.is_deleted=0 and a.status_active=1 and b.booking_type=1 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,b.fabric_color_id");
				foreach( $sql_booking as $row_book)
				{
					$booking_qnty[$row_book[csf('po_break_down_id')]][$row_book[csf('fabric_color_id')]]['fin_fab_qnty']=$row_book[csf('fin_fab_qnty')];
					$booking_po_arr[$row_book[csf('po_break_down_id')]] =$row_book[csf('po_break_down_id')];
				}

				if(!empty($booking_po_arr))
				{
					$poCond=$booking_po_cond="";
					if($db_type==2 && count($booking_po_arr)>999)
					{
						$booking_po_arr_chunk=array_chunk($booking_po_arr,999) ;
						foreach($booking_po_arr_chunk as $chunk_arr)
						{
							$poCond.=" b.id in(".implode(",",$chunk_arr).") or ";
						}

						$booking_po_cond.=" and (".chop($poCond,'or ').")";

					}
					else
					{
						$booking_pos = implode(",", $booking_po_arr);
						$booking_po_cond=" and b.id in($booking_pos)";
					}
				}
			}

			if($db_type==0){
				$select_year = " year(a.insert_date) as year";
			}else{
				$select_year = " TO_CHAR(a.insert_date,'YYYY') as year";
			}

			$sql_query = "select a.id, a.company_name, a.job_no_prefix_num, a.job_no, $select_year, a.buyer_name, a.style_ref_no, a.total_set_qnty, b.id as po_id, b.po_number, b.plan_cut as po_quantity, b.shiping_status, sum( e.quantity ) as receive_qnty, e.color_id,b.shipment_date
			 from wo_po_details_master a, wo_po_break_down b,inv_receive_master d,inv_transaction c, order_wise_pro_details e
			 where a.job_no=b.job_no_mst and e.po_breakdown_id=b.id and d.id=c.mst_id and c.id=e.trans_id and d.entry_form=e.entry_form and e.entry_form in (17,209) and e.trans_id!=0 and d.entry_form in (17,209) and d.item_category=3
			 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.company_name=$cbo_company_id $buyer_id_cond $receive_date $store_id_cond $search_cond $year_cond $booking_po_cond
			 group by a.id, a.company_name, a.job_no_prefix_num, a.job_no, a.insert_date, a.buyer_name, a.style_ref_no, a.total_set_qnty, b.id, b.po_number, e.color_id, b.plan_cut, b.shiping_status,b.shipment_date
			 union all
			 select a.id,a.company_name, a.job_no_prefix_num, a.job_no, $select_year, a.buyer_name, a.style_ref_no,a.total_set_qnty, b.id as po_id, b.po_number, b.plan_cut as po_quantity, b.shiping_status, sum(d.quantity) as receive_qnty, d.color_id,b.shipment_date
			from  wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d
			where a.job_no=b.job_no_mst and b.id = d.po_breakdown_id and c.id = d.trans_id and d.trans_type=5 and d.entry_form = 258 and a.company_name=$cbo_company_id $buyer_id_cond $receive_date $store_id_cond $search_cond $year_cond $booking_po_cond and c.status_active=1 and d.status_active =1
			group by a.id, a.company_name, a.job_no_prefix_num, a.job_no, a.insert_date, a.buyer_name, a.style_ref_no, a.total_set_qnty, b.id, b.po_number, d.color_id, b.plan_cut, b.shiping_status,b.shipment_date
			order by buyer_name	";

			//echo $sql_query;die;

			$con = connect();
			$r_id2=execute_query("delete from tmp_poid where userid=$user_id and type=333");
				if($r_id2)
			{
				oci_commit($con);
			}

			$recv_tran_in = sql_select($sql_query);
			foreach ($recv_tran_in as $row)
			{
				$po_data_arr[$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('color_id')]]['quantity'] += $row[csf('receive_qnty')];
				$job_data_arr[$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('color_id')]]['quantity'] += $row[csf('receive_qnty')];

				if($po_reference[$row[csf('po_id')]] == "")
				{
					$po_reference[$row[csf('po_id')]] = $row[csf('job_no')]."##".$row[csf('job_no_prefix_num')]."##".$row[csf('style_ref_no')]."##".$row[csf('po_number')]."##".$row[csf('po_quantity')]."##".$row[csf('shiping_status')]."##".$row[csf('year')]."##".$row[csf('shipment_date')];
					$job_reference[$row[csf('job_no')]]["job_quantity"] 		+=  $row[csf('po_quantity')]*$row[csf('total_set_qnty')];
					$job_reference[$row[csf('job_no')]]["year"] 				=  $row[csf('year')];
					$job_reference[$row[csf('job_no')]]["style_ref_no"] 		=  $row[csf('style_ref_no')];
					$job_reference[$row[csf('job_no')]]["job_no_prefix_num"] 	=  $row[csf('job_no_prefix_num')];
					$job_reference[$row[csf('job_no')]]["po_ids"] 				.=  $row[csf('po_id')].",";

					$r_id=execute_query("insert into tmp_poid (userid, poid, type) values ($user_id,".$row[csf('po_id')].",333)");
					if($r_id)
					{
						$r_id=1;
					}
					else
					{
						echo "insert into tmp_poid (userid, poid, type) values ($user_id,".$row[csf('po_id')].",333)";
						$r_id2=execute_query("delete from tmp_poid where userid=$user_id ");
						oci_rollback($con);
						die;
					}

				}
			}

			if($r_id)
			{
				oci_commit($con);
			}
			else
			{
				oci_rollback($con);
				disconnect($con);
			}

			$total_issue_sql = sql_select("select d.po_breakdown_id,d.color_id, sum(d.quantity) as issue_qnty from inv_transaction c, order_wise_pro_details d, tmp_poid e where c.id=d.trans_id and d.po_breakdown_id= e.poid and e.userid= $user_id and e.type=333 and c.item_category=3 and c.company_id=$cbo_company_id and c.transaction_type in (2,3,6) and d.entry_form in (19,202,258) and c.status_active=1 and d.status_active =1 $receive_date group by d.po_breakdown_id, d.color_id");

			foreach( $total_issue_sql as $row )
			{
				$issue_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['issue_qnty'] +=$row[csf('issue_qnty')];
			}
			unset($total_issue_sql);

			/*echo "<pre>";
			print_r($issue_qnty_arr);
			die;*/

			if(empty($booking_qnty))
			{
				$sql_booking=sql_select("select b.po_break_down_id,b.fabric_color_id, sum(b.fin_fab_qnty ) as fin_fab_qnty  from wo_booking_mst a, wo_booking_dtls b, tmp_poid e where a.booking_no=b.booking_no and b.po_break_down_id=e.poid and e.userid=$user_id and e.type=333 and a.item_category=3 and a.booking_type=1 and a.is_deleted=0 and a.status_active=1 and b.booking_type=1 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,b.fabric_color_id");
				foreach( $sql_booking as $row_book)
				{
					$booking_qnty[$row_book[csf('po_break_down_id')]][$row_book[csf('fabric_color_id')]]['fin_fab_qnty']=$row_book[csf('fin_fab_qnty')];
				}
			}


			$actual_cut_qnty=array();
			$sql_actual_cut_qty=sql_select(" select a.po_break_down_id,c.color_number_id, sum(b.production_qnty) as actual_cut_qty  from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, tmp_poid e where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=1 and a.po_break_down_id=e.poid and e.userid=$user_id and e.type=333 and a.is_deleted=0 and a.status_active=1  group by  a.po_break_down_id,c.color_number_id");
			foreach( $sql_actual_cut_qty as $row_actual)
			{
				$actual_cut_qnty[$row_actual[csf('po_break_down_id')]][$row_actual[csf('color_number_id')]]['actual_cut_qty']=$row_actual[csf('actual_cut_qty')];
			}

			$result_consumtion=array();
			$sql_consumtiont_qty=sql_select(" select b.job_no, b.po_break_down_id, b.color_number_id, sum( b.cons ) / count( b.color_number_id ) AS conjunction, pcs   from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a, tmp_poid e WHERE a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=e.poid and e.userid=$user_id and e.type=333 GROUP BY b.po_break_down_id, b.color_number_id, pcs, b.job_no ");
			$con_avg=0;
			foreach($sql_consumtiont_qty as $row_consum)
			{
				$con_avg= $row_consum[csf("conjunction")];///str_replace("'","",$row_sew[csf("pcs")]);
				$con_per_pcs=$con_avg/$row_consum[csf("pcs")];
				$result_consumtion[$row_consum[csf('job_no')]][$row_consum[csf('po_break_down_id')]][$row_consum[csf('color_number_id')]]['consum']=$con_per_pcs;
			}

			$r_id2=execute_query("delete from tmp_poid where userid=$user_id ");
			oci_rollback($con);
			die;

	        if($cbo_presentation==1)
			{
				$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
				?>
				<fieldset style="width:1480px;">
					<table cellpadding="0" cellspacing="0" width="1340">
						<tr  class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
						</tr>
						<tr  class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
						</tr>
						<tr  class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from_st)) ;?></strong></td>
						</tr>
					</table>
					<table width="1460" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
						<thead>
							<th width="30">SL</th>
							<th width="100">Buyer</th>
							<th width="50">Job Year</th>
							<th width="50">Job</th>
							<th width="100">Order</th>
							<th width="100">Style</th>
							<th width="80">Order Qty (Pcs)</th>
							<th width="60">Ex-factory</th>
							<th width="80">Shipping Status</th>
							<th width="80">Shipment Date</th>
							<th width="90">Color</th>

							<th width="80">Req. Qty</th>
							<th width="80">Total Received</th>
							<th width="80">Received Balance</th>
							<th width="80">Total Issued</th>

							<th width="80">Stock</th>
							<th width="80">Consumption</th>
							<th width="80">Possible Cut Pcs.</th>
							<th>Actual Cut</th>
						</thead>
					</table>
					<div style="width:1480px; max-height:350px; overflow-y:scroll;" id="scroll_body">
						<table width="1460" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
							<?
							$i=1;
							foreach ($po_data_arr as $buyer_id => $buyer_data)
							{
								foreach ($buyer_data as $po_id => $po_id_data)
								{
									foreach ($po_id_data as $color_id => $row)
									{
										$po_ref_data = explode("##", $po_reference[$po_id]);
										$job_number 		= $po_ref_data[0];
										$job_no_prefix_num 	= $po_ref_data[1];
										$style_ref_no 		= $po_ref_data[2];
										$po_number 			= $po_ref_data[3];
										$poQnty 			= $po_ref_data[4];
										$shiping_status 	= $po_ref_data[5];
										$job_year       	= $po_ref_data[6];
										$ship_date       	= $po_ref_data[7];


										$dzn_qnty=0;
				                    	if($costing_per_id_library[$job_number]==1)
				                    	{
				                    		$dzn_qnty=12;
				                    	}
				                    	else if($costing_per_id_library[$job_number]==3)
				                    	{
				                    		$dzn_qnty=12*2;
				                    	}
				                    	else if($costing_per_id_library[$job_number]==4)
				                    	{
				                    		$dzn_qnty=12*3;
				                    	}
				                    	else if($costing_per_id_library[$job_number]==5)
				                    	{
				                    		$dzn_qnty=12*4;
				                    	}
				                    	else
				                    	{
				                    		$dzn_qnty=1;
				                    	}

				                    	$book_qty=$booking_qnty[$po_id][$color_id]['fin_fab_qnty'];
				                    	$iss_qty=$issue_qnty_arr[$po_id][$color_id]['issue_qnty'];

										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30"><? echo $i; ?></td>
											<td width="100" align="center"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
											<td width="50" align="center"><p><? echo $job_year ; ?></p></td>
											<td width="50" align="center"><p><? echo $job_no_prefix_num; ?></p></td>
											<td width="100" align="center"><p><? echo $po_number; ?></p></td>
											<td width="100" align="center"><p><? echo $style_ref_no; ?></p></td>
											<td width="80" align="right"><p><? echo number_format($poQnty,2,'.',''); ?>&nbsp;</p></td>
											<td width="60" align="center"><a href='#report_details' onClick="openmypage_ex_factory('<? echo $po_id; ?>','1');">View</a></td>
											<td width="80" align="center"><p><? echo $shipment_status[$shiping_status]; ?></p></td>
											<td width="80" align="center"><p><? echo change_date_format($ship_date); ?></p></td>
											<td width="90" align="center"><p><? echo $color_arr[$color_id]; ?></p></td>
											<td width="80" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>
			                                <td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_id; ?>','','<? echo $color_id; ?>',0,'woven_receive_popup','<? echo $cbo_store_id; ?>',<? echo $txt_date_from_st;?>);"><? echo number_format($row["quantity"],2,'.',''); ?></a></p></td>
			                                <td width="80" align="right">
			                                	<p>&nbsp;
			                                		<?
					                                	$rec_bal=$book_qty-$row["quantity"];
					                                	echo number_format($rec_bal,2,'.','');
					                                ?>
			                             		</p>
			                             	</td>
			                                <td width="80" align="right">
			                                	<p>
			                                		<a href='#report_details' onClick="openmypage('<? echo $po_id; ?>','','<? echo $color_id; ?>',2,'woven_issue_popup','<? echo $cbo_store_id; ?>',<? echo $txt_date_from_st;?>);">
			                                			<?
															echo number_format($iss_qty,2,'.','');
														?>
													</a>&nbsp;
												</p>
											</td>
			                                <td width="80" align="right">
			                                	<p>&nbsp;
			                                		<?
					                                	$stock=$row["quantity"]-$iss_qty;
					                                	echo number_format($stock,2,'.','');
					                                ?>
			                            		</p>
			                            	</td>
			                                <td width="80" align="center"><p><? $cons_per=$result_consumtion[$job_number][$po_id][$color_id]['consum']; echo number_format($cons_per,4,'.',''); ?></p></td>
			                                <td width="80" align="right">
			                                	<p>
			                                	<?
			                                	$act_issue = $issue_qnty_arr[$po_id][$color_id]['issue_qnty'];
			                                	$possible_cut_pcs=$act_issue/($cons_per/$dzn_qnty);
			                                	echo number_format($possible_cut_pcs,0,'.',''); ?>

			                                	</p>
			                                </td>
			                                <td width="" align="right">
			                                	<p>
			                                		<a href='#report_details' onClick="openmypage('<? echo $po_id; ?>','<? //echo $row[csf('prod_id')]; ?>','<? echo $color_id; ?>',4,'woven_actual_cut_popup','<? echo $cbo_store_id; ?>');">
			                                			<?
			                                				$actual_qty=$actual_cut_qnty[$po_id][$color_id]['actual_cut_qty'];
			                                				echo number_format($actual_qty,0,'.','');
			                                			?>
			                                		</a>&nbsp;
			                                	</p>
			                                </td>
			                        	</tr>
			                            <?
			                            $i++;

			                            if($chk_order[$po_id] =="")
			                            {
			                            	$po_quantity+=$poQnty;
			                            	$total_order_qnty+=$poQnty;
			                            	$chk_order[$po_id]= $po_id;
			                            }

			                            $subtot_book_qty+=$book_qty;
			                            $subtot_rec_qty+=$row["quantity"];
					                    $subtot_rec_bal+=$rec_bal;
					                    $subtot_iss_qty+=$iss_qty;
					                    $subtot_stock+=$stock;
					                    $subtot_possible_cut_pcs+=$possible_cut_pcs;
					                    $subtot_actual_qty+=$actual_qty;

			                            $total_req_qty+=$book_qty;
			                            $total_rec_qty+=$row["quantity"];
			                            $total_rec_bal+=$rec_bal;
			                            $total_issue_qty+=$iss_qty;
			                            $total_stock+=$stock;
			                            $total_possible_cut_pcs+=$possible_cut_pcs;
			                            $total_actual_cut_qty+=$actual_qty;

									}
								}
								?>
								<tr class="tbl_bottom">
									<td colspan="6" align="right">Buyer Total</td>
									<td align="right"><? echo number_format($po_quantity,2);$po_quantity=0; ?></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"></td>

									<td align="right"><? echo number_format($subtot_book_qty,2); $subtot_book_qty=0;?></td>
									<td align="right"><? echo number_format($subtot_rec_qty,2); $subtot_rec_qty=0;?></td>
									<td align="right"><? echo number_format($subtot_rec_bal,2); $subtot_rec_bal=0;?></td>
									<td align="right"><? echo number_format($subtot_iss_qty,2); $subtot_iss_qty=0;?></td>
									<td align="right"><? echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
									<td align="right"><? //echo number_format($subtot_issue_ret_qnty,2); $subtot_issue_ret_qnty=0;?></td>
									<td align="right"><? echo number_format($subtot_possible_cut_pcs,2); $subtot_possible_cut_pcs=0;?></td>
									<td align="right"> <? echo number_format($subtot_actual_qty,2); $subtot_actual_qty=0;?></td>
								</tr>
								<?
							}
	                        ?>
	                    </table>
	                    <table width="1460" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
	                    	<tfoot>
	                    		<th width="30"></th>
	                    		<th width="100"></th>
	                    		<th width="50"></th>
	                    		<th width="50"></th>
	                    		<th width="100"></th>
	                    		<th width="100">Grand Total</th>
	                    		<th width="80" align="right" id=""><? echo number_format($total_order_qnty,2,'.',''); ?></th>
	                    		<th width="60">&nbsp;</th>
	                    		<th width="80">&nbsp;</th>
	                    		<th width="80">&nbsp;</th>
	                    		<th width="80" align="right" id="total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
	                    		<th width="80" align="right" id="total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
	                    		<th width="80" align="right" id="total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>
	                    		<th width="80" align="right" id="total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
	                    		<th width="80" align="right" id="total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
	                    		<th width="80">&nbsp;</th>
	                    		<th width="80" align="right" id="total_possible_cut_pcs"><? echo number_format($total_possible_cut_pcs,0,'.',''); ?></th>
	                    		<th width="" align="right" id="total_actual_cut_qty"><? echo $total_actual_cut_qty; ?></th>
	                    	</tfoot>
	                    </table>
	                </div>
	            </fieldset>
	            <?
	        }
	        else
	        {
	        	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	        	?>
	        	<fieldset style="width:1322px;">
	        		<table cellpadding="0" cellspacing="0" width="1300">
	        			<tr class="form_caption" style="border:none;">
	        				<td align="center" width="100%" colspan="13" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
	        			</tr>
	        			<tr  class="form_caption" style="border:none;">
	        				<td align="center" width="100%" colspan="13" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
	        			</tr>
	        			<tr  class="form_caption" style="border:none;">
	        				<td align="center" width="100%" colspan="13" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from_st)) ;?></strong></td>
	        			</tr>
	        		</table>
	        		<table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	        			<thead>
	        				<th width="30">SL</th>
	        				<th width="70">Buyer</th>
	        				<th width="60">Job</th>
	        				<th width="50">Year</th>
	        				<th width="110">Style</th>
	        				<th width="90">Job Qty. (Pcs)</th>
	        				<th width="60">Order Status</th>
	        				<th width="90">Fin. Color</th>

	        				<th width="90">Req. Qty</th>
	        				<th width="90" title="Rec.+Issue Ret.+Trans. in">Total Received</th>
	        				<th width="90" title="Req.-Totat Rec.">Received Balance</th>
	        				<th width="90" title="Issue+Rec. Ret.+Trans. out">Total Issued</th>

	        				<th width="90" title="Total Rec.- Total Issue">Stock</th>
	        				<th width="100">Consumption Pcs.</th>
	        				<th width="80">Possible Cut Pcs.</th>
	        				<th>Actual Cut</th>
	        			</thead>
	        		</table>
	        		<div style="width:1320px; max-height:350px; overflow-y:scroll;" id="scroll_body">
	        			<table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
	        				<?
	        				/*echo "<pre>";
	        				print_r($job_reference);
	        				die;*/

	        				$i=1;
							foreach ($job_data_arr as $buyer_id => $buyer_data)
							{
								foreach ($buyer_data as $job_no => $job_no_data)
								{
									foreach ($job_no_data as $color_id => $row)
									{
										$job_quantity 		= $job_reference[$job_no]["job_quantity"];
										$style_ref_no 		= $job_reference[$job_no]["style_ref_no"];
										$job_no_prefix_num 	= $job_reference[$job_no]["job_no_prefix_num"];
										$year 				= $job_reference[$job_no]["year"];

										$dzn_qnty=0;
			        					if($costing_per_id_library[$row[csf('job_no')]]==1)
			        					{
			        						$dzn_qnty=12;
			        					}
			        					else if($costing_per_id_library[$row[csf('job_no')]]==3)
			        					{
			        						$dzn_qnty=12*2;
			        					}
			        					else if($costing_per_id_library[$row[csf('job_no')]]==4)
			        					{
			        						$dzn_qnty=12*3;
			        					}
			        					else if($costing_per_id_library[$row[csf('job_no')]]==5)
			        					{
			        						$dzn_qnty=12*4;
			        					}
			        					else
			        					{
			        						$dzn_qnty=1;
			        					}

			        					$book_qty=0; $iss_qty=0; $cons_per=0;
			        					$po_ids=array_unique(explode(",",chop($job_reference[$job_no]["po_ids"],",")));
			        					foreach($po_ids as $po_id)
			        					{
			        						$book_qty+=$booking_qnty[$po_id][$color_id]['fin_fab_qnty'];
			        						$iss_qty+=$issue_qnty_arr[$po_id][$color_id]['issue_qnty'];
			        						$cons_per+=$result_consumtion[$job_no][$po_id][$color_id]['consum'];
			        						$actual_qty +=$actual_cut_qnty[$po_id][$color_id]['actual_cut_qty'];
			        					}
			        					$po_ids=implode(",",$po_ids);

			        					?>
											<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
												<td width="30"><? echo $i; ?></td>
												<td width="70"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
												<td width="60" align="center"><p><? echo $job_no_prefix_num; ?></p></td>
												<td width="50" align="center"><p><? echo $year; ?></p></td>
												<td width="110"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
												<td width="90" align="right"><p><? echo $job_quantity; ?>&nbsp;</p></td>
												<td width="60" align="center"><a href='#report_details' onClick="openmypage_ex_factory('<? echo $po_ids; ?>','2');">View</a></td>
												<td width="90"><p><? echo $color_arr[$color_id]; ?></p></td>
												<td width="90" align="right"><p><? echo number_format($book_qty,2,'.','');?>&nbsp;</p></td>
												<?
			                                //$rec_qty=$row["quantity"];
			                                ?>

			                                <td width="90" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',0,'woven_receive_popup','<? echo $cbo_store_id; ?>',<? echo $txt_date_from_st;?>);"><? echo number_format($row["quantity"],2,'.',''); ?></a>&nbsp;</p></td>
			                                <td width="90" align="right">
			                                	<p>
			                                		<?
			                                			$rec_bal=$book_qty-$row["quantity"];
			                                			echo number_format($rec_bal,2,'.','');
			                                		?>&nbsp;
			                                	</p>
			                                </td>
			                                <td width="90" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',2,'woven_issue_popup','<? echo $cbo_store_id; ?>',<? echo $txt_date_from_st;?>);"><? echo number_format($iss_qty,2,'.',''); ?></a>&nbsp;</p></td>
			                                <td width="90" align="right">
			                                	<p>
			                                		<?
			                                			$stock=$row["quantity"]-$iss_qty;
			                                			echo number_format($stock,2,'.','');
			                                		?>&nbsp;
			                                	</p>
			                                </td>
			                                <td width="100" align="right"><p><? echo number_format($cons_per,4,'.',''); ?></p></td>
			                                <td width="80" align="right">
			                                	<p>
			                                		<?
			                                		$possible_cut_pcs=$iss_qty/($cons_per/$dzn_qnty);
			                                		echo number_format($possible_cut_pcs,0,'.',''); ?>
			                                	</p>
			                                </td>
			                                <td width="" align="right">
			                                	<p>
			                                	<a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>',4,'woven_actual_cut_popup','<? echo $cbo_store_id; ?>')">
			                                		<? echo $actual_qty; ?>
			                                	</a>&nbsp;
			                                	</p>
			                            	</td>
			                            </tr>
			                            <?

			                            $po_quantity+=$job_quantity;
			                            $subtot_book_qty+=$book_qty;
			                            $subtot_rec_qty+=$row["quantity"];
			                            $subtot_rec_bal+=$rec_bal;
			                            $subtot_iss_qty+=$iss_qty;
			                            $subtot_stock+=$stock;
			                            $subtot_possible_cut_pcs+=$possible_cut_pcs;
			                            $subtot_actual_qty+=$actual_qty;

			                            $total_order_qnty+=$job_quantity;
			                            $total_req_qty+=$book_qty;
			                            $total_rec_qty+=$row["quantity"];
			                            $total_rec_bal+=$rec_bal;
			                            $total_issue_qty+=$iss_qty;
			                            $total_stock+=$stock;
			                            $total_possible_cut_pcs+=$possible_cut_pcs;
			                            $total_actual_cut_qty+=$actual_qty;

									}
								}
								?>
								<tr class="tbl_bottom">
									<td colspan="5" align="right">Buyer Total</td>
									<td align="right"><? echo number_format($po_quantity,2);$po_quantity=0; ?></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"><? echo number_format($subtot_book_qty,2); $subtot_book_qty=0;?></td>
									<td align="right"><? echo number_format($subtot_rec_qty,2); $subtot_rec_qty=0;?></td>
									<td align="right"><? echo number_format($subtot_rec_bal,2); $subtot_rec_bal=0;?></td>
									<td align="right"><? echo number_format($subtot_iss_qty,2); $subtot_iss_qty=0;?></td>
									<td align="right"><? echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
									<td align="right"><? //echo number_format($subtot_issue_ret_qnty,2); $subtot_issue_ret_qnty=0;?></td>
									<td align="right"><? echo number_format($subtot_possible_cut_pcs,2); $subtot_possible_cut_pcs=0;?></td>
									<td align="right"> <? echo number_format($subtot_actual_qty,2); $subtot_actual_qty=0;?></td>
								</tr>
								<?
							}

	        				/*if($db_type==0)
	        				{
	        					$sql_query="Select a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, sum(a.total_set_qnty) as ratio, group_concat(b.id) as po_id, sum(b.plan_cut*a.total_set_qnty) as po_quantity, year(a.insert_date) as year, sum( c.cons_quantity ) as receive_qnty,  e.color_id
	        					from wo_po_details_master a, wo_po_break_down b,inv_receive_master d, inv_transaction c, order_wise_pro_details e
	        					where a.job_no=b.job_no_mst and e.po_breakdown_id=b.id and  d.id=c.mst_id and c.id=e.trans_id and d.entry_form=e.entry_form and e.entry_form in (17) and e.trans_id!=0 and d.entry_form in (17)  and d.item_category=3 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and
	        					a.company_name=$cbo_company_id $receive_date $buyer_id_cond $store_id_cond $search_cond group by a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, e.color_id, a.insert_date, a.total_set_qnty order by a.buyer_name, a.job_no";
	        				}
	        				else if($db_type==2)
	        				{
	        					$sql_query="Select a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, sum(a.total_set_qnty) as ratio, LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as po_id, sum(b.plan_cut*a.total_set_qnty) as po_quantity, TO_CHAR(a.insert_date,'YYYY') as year, sum( c.cons_quantity ) as receive_qnty,  e.color_id
	        					from wo_po_details_master a, wo_po_break_down b,inv_receive_master d, inv_transaction c, order_wise_pro_details e
	        					where a.job_no=b.job_no_mst and e.po_breakdown_id=b.id and  d.id=c.mst_id and c.id=e.trans_id and d.entry_form=e.entry_form and e.entry_form in (17) and e.trans_id!=0 and d.entry_form in (17)  and d.item_category=3 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and
	        					a.company_name=$cbo_company_id $receive_date $buyer_id_cond  $store_id_cond $search_cond group by a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, e.color_id, a.insert_date, a.total_set_qnty order by a.buyer_name, a.job_no";
	        				}
	                    	//echo $sql_query;
	        				$i=1; $k=1; $checkbuyerArr=array();
	        				$nameArray=sql_select( $sql_query );
	        				foreach ($nameArray as $row)
	        				{
	        					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	        					$dzn_qnty=0;
	        					if($costing_per_id_library[$row[csf('job_no')]]==1)
	        					{
	        						$dzn_qnty=12;
	        					}
	        					else if($costing_per_id_library[$row[csf('job_no')]]==3)
	        					{
	        						$dzn_qnty=12*2;
	        					}
	        					else if($costing_per_id_library[$row[csf('job_no')]]==4)
	        					{
	        						$dzn_qnty=12*3;
	        					}
	        					else if($costing_per_id_library[$row[csf('job_no')]]==5)
	        					{
	        						$dzn_qnty=12*4;
	        					}
	        					else
	        					{
	        						$dzn_qnty=1;
	        					}

	        					$color_id=$row[csf("color_id")]; $book_qty=0; $iss_qty=0; $cons_per=0;
	        					$po_ids=array_unique(explode(",",$row[csf('po_id')]));
	        					foreach($po_ids as $po_id)
	        					{
	        						$book_qty+=$booking_qnty[$po_id][$color_id]['fin_fab_qnty'];
	        						$iss_qty+=$issue_qnty[$po_id][$color_id]['issue_qnty'];
	        						$cons_per+=$result_consumtion[$row[csf('job_no')]][$po_id][$color_id]['consum'];
	        						$actual_qty=$actual_cut_qnty[$po_id][$color_id]['actual_cut_qty'];
	        					}
	        					$po_ids=implode(",",$po_ids);
	        					if(!in_array($row[csf("buyer_name")],$checkbuyerArr))
	        					{

										if($k!=1) // product wise sum/total here------------
										{
											?>
											<tr class="tbl_bottom">
												<td colspan="5" align="right">Buyer Total</td>
												<td align="right"><? //echo number_format($po_quantity,2);$po_quantity=0; ?></td>
												<td align="right"></td>
												<td align="right"></td>


												<td align="right"><? echo number_format($subtot_book_qty,2); $subtot_book_qty=0;?></td>
												<td align="right"><? echo number_format($subtot_rec_qty,2); $subtot_rec_qty=0;?></td>

												<td align="right"><? echo number_format($subtot_rec_bal,2); $subtot_rec_bal=0;?></td>
												<td align="right"><? echo number_format($subtot_iss_qty,2); $subtot_iss_qty=0;?></td>

												<td align="right"><? echo number_format($subtot_stock,2); $subtot_stock=0;?></td>
												<td align="right"><? //echo number_format($subtot_issue_ret_qnty,2); $subtot_issue_ret_qnty=0;?></td>
												<td align="right"><? echo number_format($subtot_possible_cut_pcs,2); $subtot_possible_cut_pcs=0;?></td>
												<td align="right"> <? echo number_format($subtot_actual_qty,2); $subtot_actual_qty=0;?></td>
											</tr>
											<?
										}
										$checkbuyerArr[]=$row[csf("buyer_name")];
										$k++;
									}
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="70"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
										<td width="60" align="center"><p><? echo $row[csf("job_no_prefix_num")]; ?></p></td>
										<td width="50" align="center"><p><? echo $row[csf("year")]; ?></p></td>
										<td width="110"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
										<td width="90" align="right"><p><? $po_quantity+=$row[csf("po_quantity")];echo $row[csf("po_quantity")]; ?>&nbsp;</p></td>
										<td width="60" align="center"><a href='#report_details' onClick="openmypage_ex_factory('<? echo $po_ids; ?>','2');">View</a></td>
										<td width="90"><p><? echo $color_arr[$color_id]; ?></p></td>
										<td width="90" align="right"><p><? $subtot_book_qty+=$book_qty; echo number_format($book_qty,2,'.','');//$booking_qty,2,'.',''); ?>&nbsp;</p></td>
										<?
	                                $rec_qty=$row[csf("receive_qnty")]; //$consumtion_library
	                                $subtot_rec_qty+=$rec_qty;
	                                ?>
	                                <td width="90" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>','woven_receive_popup');"><? echo number_format($rec_qty,2,'.',''); ?></a>&nbsp;</p></td>
	                                <td width="90" align="right"><p><? $rec_bal=$book_qty-$rec_qty; $subtot_rec_bal+=$rec_bal;echo number_format($rec_bal,2,'.',''); ?>&nbsp;</p></td>
	                                <td width="90" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>','woven_issue_popup');"><? echo number_format($iss_qty,2,'.',''); $subtot_iss_qty+=$iss_qty; ?></a>&nbsp;</p></td>
	                                <td width="90" align="right"><p><? $stock=$rec_qty-$iss_qty;$subtot_stock+=$stock; echo number_format($stock,2,'.',''); ?>&nbsp;</p></td>
	                                <td width="100" align="right"><p><? echo number_format($cons_per,4,'.',''); ?></p></td>
	                                <td width="80" align="right"><p><? $possible_cut_pcs=$iss_qty/($cons_per/$dzn_qnty); $subtot_possible_cut_pcs+=$possible_cut_pcs;echo number_format($possible_cut_pcs,0,'.',''); ?></p></td>
	                                <td width="" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','','<? echo $color_id; ?>','woven_actual_cut_popup');"><? echo $actual_qty; $subtot_actual_qty+=$actual_qty;?></a>&nbsp;</p></td>
	                            </tr>
	                            <?
	                            $i++;
	                            $total_order_qnty+=$row[csf("po_quantity")];
	                            $total_req_qty+=$book_qty;
	                            $total_rec_qty+=$rec_qty;
	                            $total_rec_bal+=$rec_bal;
	                            $total_issue_qty+=$iss_qty;
	                            $total_stock+=$stock;
	                            $total_possible_cut_pcs+=$possible_cut_pcs;
	                            $total_actual_cut_qty+=$actual_qty;
	                        }*/
	                        ?>
	                    </table>
	                </div>
	                <table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
	                	<tfoot>
	                		<th width="30"></th>
	                		<th width="70"></th>
	                		<th width="60"></th>
	                		<th width="50"></th>
	                		<th width="110">Grand Total</th>
	                		<th width="90" align="right" id=""><? //echo number_format($total_order_qnty,2,'.',''); ?></th>
	                		<th width="60">&nbsp;</th>
	                		<th width="90">&nbsp;</th>
	                		<th width="90" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
	                		<th width="90" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
	                		<th width="90" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>
	                		<th width="90" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
	                		<th width="90" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
	                		<th width="100">&nbsp;</th>
	                		<th width="80" align="right" id="total_possible_cut_pcs"><? echo number_format($total_possible_cut_pcs,0,'.',''); ?></th>
	                		<th width="" align="right" id="total_actual_cut_qty"><? echo $total_actual_cut_qty; ?></th>
	                	</tfoot>
	                </table>
	            </fieldset>
	            <?
	        }
	    }
	}
	else // show2 button start
	{
		if($cbo_report_type==1 && $cbo_presentation==1) // Knit Finish Start
		{
			$con = connect();
			if($db_type==0)
			{
				$prod_id_cond=" group_concat(b.from_prod_id)";
				if($cbo_year!="") $year_cond="and year(a.insert_date) in($cbo_year)"; else $year_cond="";
			}
			else if($db_type==2)
			{
				$prod_id_cond=" listagg(cast(b.from_prod_id as varchar2(4000)),',') within group (order by b.from_prod_id)";
				if($cbo_year!="") $year_cond="and to_char(a.insert_date,'YYYY') in($cbo_year)";  else $year_cond="";
			}
			$receive_date_cond = str_replace("c.transfer_date", "d.transfer_date", $receive_date);
			if($txt_search_booking!='')
			{
				$booking_cond = " and c.booking_no like '%$txt_search_booking'";
			}
			// ================================ CREATING CONSTRUCTION - COMPOSITION ARRAY ===============================
			$composition_arr=array();
			$detarmination_id_arr=array();
			$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id asc";//  and a.id in(546,5,83)
			$sql_deter_res=sql_select($sql_deter);
			if(count($sql_deter_res)>0)
			{
				foreach( $sql_deter_res as $row )
				{
					if(array_key_exists($row[csf('id')],$composition_arr))
					{
						$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
						$detarmination_id_arr[str_replace(' ', '',$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%")]=$row[csf('id')];
					}
					else
					{
						$composition_arr[$row[csf('id')]]=$row[csf('construction')]."*".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
						$detarmination_id_arr[str_replace(' ', '',$row[csf('construction')]."*".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%")]=$row[csf('id')];
					}
				}
			}
			unset($sql_deter_res);
			// echo "<pre>";print_r($detarmination_id_arr);
			// ================================== MAIN QUERY =========================================

			if($txt_date_from !="" && $txt_date_to!="")
			{
				if($cbo_date_cat==1) $search_cond.=" and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to' ";
				else if($cbo_date_cat==2) $search_cond.=" and b.update_date between '$txt_date_from' and '$txt_date_to' and b.status_active=3";
			}

			if($cbo_sock_for==1) // Running Order
			{
				$search_cond.=" and b.shiping_status<>3 and b.status_active=1";
			}
			else if($cbo_sock_for==2) // Cancelled Order
			{
				$search_cond.=" and b.status_active=3";
			}
			else if($cbo_sock_for==3) // Left Over
			{
				$search_cond.=" and b.shiping_status=3 and b.status_active=1";
			}

			$sql = "SELECT a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.po_number, b.id as po_id, c.construction, c.copmposition, c.booking_no, c.fabric_color_id as color_id, sum(d.order_quantity) as order_qty, c.id as dtls_id, sum(c.fin_fab_qnty) as req_qty,b.pub_shipment_date, b.shiping_status,b.status_active
			from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c,wo_po_color_size_breakdown d
			where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.id=d.po_break_down_id and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.booking_type=1 and c.construction is not null   $search_cond $buyer_id_cond $shipment_id_cond $year_cond $booking_cond
			group by a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.po_number, b.id, c.construction, c.copmposition, c.booking_no, c.fabric_color_id, c.id,b.pub_shipment_date,b.shiping_status,b.status_active"; // and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0
			// echo $sql;die();
			$sql_res = sql_select($sql);

			// ======================== STORE PO ID IN GLOBL TEMP TBL FOR FUTURE USE =======================
			$tr_str=execute_query("delete from gbl_temp_report_id where user_id=$user_id");
			if($tr_str) oci_commit($con);

			$temp_table_id=return_field_value("max(id)+1 as id","gbl_temp_report_id","1=1","id");
			if($temp_table_id=="") $temp_table_id=1;
			foreach($sql_res as $row)
			{
				if($po_check[$row[csf("po_id")]]=="")
				{
					$po_check[$row[csf("po_id")]]=$row[csf("po_id")];
					$r_id=execute_query("insert into gbl_temp_report_id (id, ref_val, ref_from, user_id, ref_string) values ($temp_table_id,".$row[csf("po_id")].",1,$user_id,'".$row[csf("po_id")]."')");
					if($r_id)
					{
						$r_id=1;
					}
					else
					{
						echo "insert into gbl_temp_report_id (id, ref_val, ref_from, user_id, ref_string) values ($temp_table_id,".$row[csf("po_id")].",1,$user_id,'".$row[csf("po_id")]."')";
						oci_rollback($con);
						die;
					}
					$temp_table_id++;
				}
			}

			if($r_id)
			{
				oci_commit($con);
			}
			else
			{
				oci_rollback($con);
			}

			$details_data 	= array();
			$po_check 		= array();
			$dtlsId_check 	= array();
			$color_qnty 	= array();
			$po_info_array 	= array();

			foreach($sql_res as $val)
			{
				$pre_key=str_replace(' ', '', $val[csf('po_id')]."*".$val[csf('color_id')]."*".$val[csf('construction')]."*".$val[csf('copmposition')]);
				// $pre_key=$val[csf('po_id')]."*".$val[csf('color_id')]."*".$val[csf('construction')]."*".$val[csf('copmposition')];
				$cons_com = str_replace(' ', '',$val[csf('construction')]."*".$val[csf('copmposition')]);
				$deter_id = $detarmination_id_arr[$cons_com];
				// echo $cons_com."<br>";
				$details_data[$pre_key]['job_no_prefix_num']=$val[csf('job_no_prefix_num')];
				$details_data[$pre_key]['buyer_name']=$val[csf('buyer_name')];
				$details_data[$pre_key]['style_ref_no']=$val[csf('style_ref_no')];
				$details_data[$pre_key]['po_number']=$val[csf('po_number')];
				$details_data[$pre_key]['po_id']=$val[csf('po_id')];
				$details_data[$pre_key]['booking_no']=$val[csf('booking_no')];
				$details_data[$pre_key]['pub_shipment_date']=$val[csf('pub_shipment_date')];
				$details_data[$pre_key]['shiping_status']=$val[csf('shiping_status')];
				// $details_data[$pre_key]['detarmination_id']=$deter_id;
				if($val[csf('shiping_status')]!=3 && $val[csf('status_active')]==1) // Running Order
				{
					// $search_cond.=" and b.shiping_status<>3 and b.status_active=1";
					$details_data[$pre_key]['stock_for']='Running Order';
				}
				else if($val[csf('status_active')]==3) // Cancelled Order
				{
					// $search_cond.=" and b.status_active=3";
					$details_data[$pre_key]['stock_for']='Cancelled Order';
				}
				else if($val[csf('shiping_status')]==3 && $val[csf('status_active')]==1) // Left Over
				{
					// $search_cond.=" and b.shiping_status=3 and b.status_active=1";
					$details_data[$pre_key]['stock_for']='Left Over';
				}

				$po_info_array[$val[csf('po_id')]]['job']=$val[csf('job_no_prefix_num')];
				$po_info_array[$val[csf('po_id')]]['buyer']=$val[csf('buyer_name')];
				$po_info_array[$val[csf('po_id')]]['style']=$val[csf('style_ref_no')];
				$po_info_array[$val[csf('po_id')]]['po_number']=$val[csf('po_number')];
				$po_info_array[$val[csf('po_id')]]['booking_no']=$val[csf('booking_no')];
				$po_info_array[$val[csf('po_id')]]['pub_shipment_date']=$val[csf('pub_shipment_date')];

				if($po_check[$val[csf('po_id')]][$val[csf('color_id')]]=="")
				{
					$po_check[$val[csf('po_id')]][$val[csf('color_id')]] = $val[csf('po_id')];
					$color_qnty[$val[csf('po_id')]][$val[csf('color_id')]]['order_qty']+=$val[csf('order_qty')];
				}
				if($dtlsId_check[$pre_key][$val[csf('dtls_id')]][$val[csf('color_id')]]=="")
				{
					$dtlsId_check[$pre_key][$val[csf('dtls_id')]][$val[csf('color_id')]] = $val[csf('dtls_id')];
					$details_data[$pre_key]['req_qty']+=$val[csf('req_qty')];
				}
			}
			// echo "<pre>";print_r($details_data);
			unset($sql_res);
			$store_id_cond = str_replace("c.store_id", "b.store_id", $store_id_cond);
			// ============================ GETTING TRANSACTION DATA ==========================
			$sql_order_trams="SELECT a.po_breakdown_id as po_id,a.color_id,c.detarmination_id,b.prod_id,
				sum(case when a.entry_form in (7,37,66,68) then a.quantity else 0 end) as receive_qnty,
				sum(case when a.entry_form in(14,15,134) and a.trans_type=5 then a.quantity else 0 end) as rec_trns_qnty,
				sum(case when a.entry_form in (18,71) then a.quantity else 0 end) as issue_qnty,
				sum(case when a.entry_form in (126,52) then a.quantity else 0 end) as issue_ret_qnty,
				sum(case when a.entry_form in (46) then a.quantity else 0 end) as rec_ret_qnty,
				sum(case when a.entry_form in(14,15,134) and a.trans_type=6 then a.quantity else 0 end) as issue_trns_qnty, c.gsm, c.dia_width, b.pi_wo_batch_no
			from order_wise_pro_details a, inv_transaction b, product_details_master c
			where a.trans_id=b.id and b.prod_id=c.id and a.prod_id=c.id and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.entry_form in(7,37,66,68,14,15,134,46,18,71,126,52) and b.company_id=$cbo_company_id and a.po_breakdown_id in(select ref_string from gbl_temp_report_id where user_id=$user_id) $store_id_cond group by a.po_breakdown_id,a.color_id,c.detarmination_id,b.prod_id, c.gsm, c.dia_width, b.pi_wo_batch_no";
			// echo $sql_order_trams;die();
			$sql_order_trams_res = sql_select($sql_order_trams);
			if(count($sql_order_trams_res)==0)
			{
				?>
				<div style="color: red;font-size: 20px;text-align: center;font-weight: bold;">Data not available! Please try again.</div>
				<?
				die();
			}
			foreach ($sql_order_trams_res as $val)
			{
				$pre_key=str_replace(' ', '', $val[csf('po_id')]."*".$val[csf('color_id')]."*".$composition_arr[$val[csf('detarmination_id')]]);
				// $pre_key=$val[csf('po_id')]."*".$val[csf('color_id')]."*".$composition_arr[$val[csf('detarmination_id')]];
				$details_data[$pre_key]['receive_qnty']+=$val[csf('receive_qnty')];
				$details_data[$pre_key]['rec_trns_qnty']+=$val[csf('rec_trns_qnty')];
				$details_data[$pre_key]['issue_qnty']+=$val[csf('issue_qnty')];
				$details_data[$pre_key]['issue_ret_qnty']+=$val[csf('issue_ret_qnty')];
				$details_data[$pre_key]['issue_trns_qnty']+=$val[csf('issue_trns_qnty')];
				$details_data[$pre_key]['rec_ret_qnty']+=$val[csf('rec_ret_qnty')];
				$details_data[$pre_key]['prod_id'].=$val[csf('prod_id')].",";
				$details_data[$pre_key]['po_id'] = $val[csf('po_id')];
				$details_data[$pre_key]['detarmination_id'] = $val[csf('detarmination_id')];
				$details_data[$pre_key]['gsm'] = $val[csf('gsm')];
				$details_data[$pre_key]['dia_width'] = $val[csf('dia_width')];
				$details_data[$pre_key]['batch_id'] = $val[csf('pi_wo_batch_no')];

				$all_prod_id[$val[csf('prod_id')]] = $val[csf('prod_id')];
			}
			unset($sql_order_trams_res);

			if(!empty($all_prod_id))
		    {
		    	$all_prod_ids=implode(",",$all_prod_id);
		    	$all_prod_id_cond=""; $prodCond="";
		    	if($db_type==2 && count($all_prod_id)>999)
		    	{
		    		$all_prod_id_chunk=array_chunk($all_prod_id,999) ;
		    		foreach($all_prod_id_chunk as $chunk_arr)
		    		{
		    			$chunk_arr_value=implode(",",$chunk_arr);
		    			$prodCond.="  a.prod_id in($chunk_arr_value) or ";
		    		}

		    		$all_prod_id_cond.=" and (".chop($prodCond,'or ').")";
		    	}
		    	else
		    	{
		    		$all_prod_id_cond=" and a.prod_id in($all_prod_ids)";
		    	}

		    	$transaction_date_array=array();
		    	if($all_prod_id_cond!="")
		    	{
		    		$sql_date="SELECT c.po_breakdown_id, a.prod_id, min(a.transaction_date) as min_date, max(a.transaction_date) as max_date from inv_transaction a,order_wise_pro_details c where a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=2 $all_prod_id_cond  group by c.po_breakdown_id,a.prod_id";
		    		// echo $sql_date;die;

		    		$sql_date_result=sql_select($sql_date);
		    		foreach( $sql_date_result as $row )
		    		{
		    			$transaction_date_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
		    		}
		    		unset($sql_date_result);
		    	}
		    }
		    // echo "<pre>";print_r($transaction_date_array);

			$booking_sql = "SELECT c.po_break_down_id as po_id, c.construction, c.copmposition,c.fabric_color_id as color_id, sum(c.fin_fab_qnty) as req_qty
			from  wo_booking_dtls c
			where c.status_active=1 and c.is_deleted=0 and c.booking_type=1 and c.construction is not null $booking_cond and c.po_break_down_id in(select ref_string from gbl_temp_report_id where user_id=$user_id) group by c.po_break_down_id, c.construction, c.copmposition,c.fabric_color_id";
			// echo $booking_sql;die();
			$booking_sql_res = sql_select($booking_sql);
			$reqQtyArr = array();
			foreach ($booking_sql_res as $val)
			{
				$kk = $val[csf('po_id')].'*'.$val[csf('color_id')].'*'.$val[csf('construction')].'*'.$val[csf('copmposition')];
				$akey = str_replace(' ', '', $kk);
				$reqQtyArr[$akey] = $val[csf('req_qty')];
			}

			// echo "<pre>";print_r($details_data);
			$tr_str=execute_query("delete from gbl_temp_report_id where user_id=$user_id");
			if($tr_str) oci_commit($con);

			$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
			$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");
			?>
			<fieldset style="width:1970px;">
				<table cellpadding="0" cellspacing="0" width="1310">
					<tr class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="21" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
					</tr>
					<tr  class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
					</tr>
					<tr  class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="21" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from_st)) ;?></strong></td>
					</tr>
				</table>
				<table width="2460" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="70">Buyer</th>
						<th width="60">Job</th>
						<th width="100">Booking No.</th>
						<th width="100">Order</th>
						<th width="110">Style</th>
						<th width="80">Order Qty (Pcs)</th>
						<th width="80">Shipment Date</th>
						<th width="80">Shipping Status</th>
						<th width="80">Stock For</th>
						<th width="80">GSM</th>
						<th width="80">Width /Dia Type</th>
						<th width="100">F.Construction</th>
						<th width="100">F.Composition</th>
						<th width="90">Fin. Color</th>
						<th width="90">Batch No</th>

						<th width="80">Req. Qty</th>
                        <th width="80" title="Trans. in">Trans. In</th>
						<th width="80" title="Received">Received</th>

						<th width="70" title="Issue Return">Issue Return</th>
						<th width="80" title="Rec.+Issue Rtn.+Trans. in">Total Received</th>
						<th width="80" title="Req.-Totat Rec.">Received Balance</th>
                        <th width="80" title="Trans. out">Trans Out</th>
						<th width="80" title="Issued">Issued</th>

						<th width="70" title="Recv. Return">Recv. Return</th>
						<th width="80" title="Issue+Rec. Ret.+Trans. out">Total Issued</th>

						<th width="80" title="Total Rec.- Total Issue">Stock</th>
						<th width="80">DOH</th>
					</thead>
				</table>
				<div style="width:2480px; max-height:350px; overflow-y:scroll;" id="scroll_body">
					<table width="2460" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
						<?
						$i=1;
						foreach ($details_data as $dtls_key=>$row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$dzn_qnty=0;
							/*
							if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
							else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
							else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
							else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;*/
							$data = explode("*", $dtls_key);

							$order_id=$data[0];
							$color_id=$data[1];
							$detarmination_id=$row["detarmination_id"];
							if($detarmination_id==""){$detarmination_id=0;}

							$rec_qty=0;
							$reqQty = $reqQtyArr[$dtls_key];
							$issue_ret_qnty=$row['issue_ret_qnty'];
							$trans_in_qty=$row["rec_trns_qnty"];
							$rec_qty=($row["receive_qnty"]);

							$trans_out=($row["issue_trns_qnty"]);
							$iss_qty=$row["issue_qnty"];
							$rec_ret_qnty=$row["rec_ret_qnty"];

							$total_receive = 0;
							$total_issue = 0;
							$total_receive=$trans_in_qty+$rec_qty+$issue_ret_qnty;
							$total_issue=$trans_out+$iss_qty+$rec_ret_qnty;
							$stock = 0;
							$stock = $total_receive-$total_issue;
							$stockQnty =$stock;
							$stock = number_format($stock,2);
							// echo $stock."<br>";
							// echo $value_for_search_by;
							// die('Go to hell');
							$prod_id=chop($row['prod_id'],',');
							$daysOnHand = datediff("d",change_date_format($transaction_date_array[$order_id][$prod_id]['max_date'],'','',1),date("Y-m-d"));

							if($value_for_search_by==2 && $stock > 0)
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="70" align="center"><p><? echo $buyer_arr[$po_info_array[$row["po_id"]]['buyer']]; ?></p></td>
									<td width="60" align="center"><p><? echo $po_info_array[$row["po_id"]]['job']; ?></p></td>

									<td width="100" align="center"><p><? echo $po_info_array[$row["po_id"]]['booking_no']; ?></p></td>

									<td width="100" align="center" title="<? echo $order_id ;?>" ><p><? echo $po_info_array[$row["po_id"]]['po_number']; ?></p></td>
									<td width="110" align="center"><p><? echo $po_info_array[$row["po_id"]]['style']; ?></p></td>
									<td width="80" align="right"><p><? $po_quantity+=$color_qnty[$order_id][$color_id]["order_qty"];echo number_format($color_qnty[$order_id][$color_id]["order_qty"],0); ?></p></td>
									<td width="80" align="center"><p><? echo change_date_format($po_info_array[$row["po_id"]]['pub_shipment_date']); ?></p></td>
									<td width="80" align="center"><p><? echo $shipment_status[$row["shiping_status"]]; ?></p></td>
									<td width="80" align="center"><p><? echo $row["stock_for"]; ?></p></td>
									<td width="80" align="center" title="<? echo $prod_id;?>"><p><? echo $row["gsm"]; ?></p></td>
									<td width="80" align="center"><p><? echo $row["dia_width"]; ?></p></td>
									<td width="100" align="left"><p><? echo $data[2];?></p></td>
									<td width="100" align="left"><p><? echo $data[3];?></p></td>
									<td width="90" align="center" title="<? echo $data[1] ;?>"><p><? echo $color_arr[$data[1]]; ?></p></td>
									<td width="90" align="center"><p><? echo $batch_arr[$row["batch_id"]]; ?></p></td>
									<td width="80" align="right">
										<?
										$book_qty = $row['req_qty'];
										echo number_format($book_qty,2,'.','');
										?>
									</td>
	                                <td width="80" title="Trans. In" align="right"><? echo number_format($trans_in_qty,2,'.',''); ?></td>
									<td width="80" align="right"><? echo number_format($rec_qty,2,'.',''); ?></td>
									<td width="70" align="right">
										<a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id; ?>',2,'issue_ret_popup');">
											<? echo number_format($issue_ret_qnty,2,'.',''); ?>
										</a>
									</td>
									<td width="80" align="right">
										<a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id."__".$detarmination_id; ?>',1,'receive_popup','<? echo str_replace("'","",$cbo_store_id); ?>');">
										<? echo number_format($total_receive,2,'.',''); ?>
										</a>
									</td>
									<td width="80" align="right"><p><? $rec_bal=$book_qty-$total_receive; $subtot_rec_bal+=$rec_bal;echo number_format($rec_bal,2,'.',''); ?></p></td>
									<td width="80" align="right"><? echo number_format($trans_out,2,'.',''); ?></td>
									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id."__".$detarmination_id; ?>',3,'issue_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"> <? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
									<td width="70" align="right">
									<a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id; ?>',4,'receive_ret_popup');">
										<? echo number_format($rec_ret_qnty,2,'.',''); ?>
									</a>
									<td width="80" align="right"><? echo number_format($total_issue,2,'.',''); ?></td>
									<td width="80" align="right">
										<p>
											<a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id."__".$detarmination_id; ?>',5,'stock_popup',<? echo $cbo_store_id;?>);">
												<? echo $stock; ?>
											</a>
										</p>
									</td>
									<td width="80" align="right"><? echo $daysOnHand; ?></td>
								</tr>
								<?
								$i++;
								$total_order_qnty+=$row[csf("po_quantity")];
								$rec_trns_in_qnty+=$trans_in_qty;
								$total_req_qty+=$book_qty;
								$total_rec_qty+=$rec_qty;
								$total_receive_qnty+=$total_receive;
								$total_rec_bal+=$rec_bal;
								$issue_trns_out_qnty+=$trans_out;
								$total_issue_qty+=$iss_qty;
								$total_issue_quantity+=$total_issue;
								$total_stock+=$stockQnty;
								$total_possible_cut_pcs+=$possible_cut_pcs;
								$total_rec_return_qnty+=$rec_ret_qnty;
								$total_issue_ret_qnty+=$issue_ret_qnty;
								$total_actual_cut_qty+=$actual_qty;
							}
							else if($value_for_search_by==1)// && $reqQty>0
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="70" align="center"><p><? echo $buyer_arr[$po_info_array[$row["po_id"]]['buyer']]; ?></p></td>
									<td width="60" align="center"><p><? echo $po_info_array[$row["po_id"]]['job']; ?></p></td>

									<td width="100" align="center"><p><? echo $po_info_array[$row["po_id"]]['booking_no']; ?></p></td>

									<td width="100" align="center" title="<? echo $order_id ;?>" ><p><? echo $po_info_array[$row["po_id"]]['po_number']; ?></p></td>
									<td width="110" align="center"><p><? echo $po_info_array[$row["po_id"]]['style']; ?></p></td>
									<td width="80" align="right"><p><? $po_quantity+=$color_qnty[$order_id][$color_id]["order_qty"];echo number_format($color_qnty[$order_id][$color_id]["order_qty"],0); ?></p></td>
									<td width="80" align="center"><p><? echo change_date_format($po_info_array[$row["po_id"]]['pub_shipment_date']); ?></p></td>
									<td width="80" align="center"><p><? echo $shipment_status[$row["shiping_status"]]; ?></p></td>
									<td width="80" align="center"><p><? echo $row["stock_for"]; ?></p></td>
									<td width="80" align="center" title="<? echo $prod_id;?>"><p><? echo $row["gsm"]; ?></p></td>
									<td width="80" align="center"><p><? echo $row["dia_width"]; ?></p></td>
									<td width="100" align="left"><p><? echo $data[2];?></p></td>
									<td width="100" align="left"><p><? echo $data[3];?></p></td>
									<td width="90" align="center" title="<? echo $data[1] ;?>"><p><? echo $color_arr[$data[1]]; ?></p></td>
									<td width="90" align="center"><p><? echo $batch_arr[$row["batch_id"]]; ?></p></td>
									<td width="80" align="right">
										<?
										// $book_qty = $row['req_qty'];
										$book_qty = $reqQty;
										echo number_format($book_qty,2,'.','');
										?>
									</td>
	                                <td width="80" title="Trans. In" align="right"><? echo number_format($trans_in_qty,2,'.',''); ?></td>
									<td width="80" align="right"><? echo number_format($rec_qty,2,'.',''); ?></td>
									<td width="70" align="right">
										<a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id; ?>',2,'issue_ret_popup');">
											<? echo number_format($issue_ret_qnty,2,'.',''); ?>
										</a>
									</td>
									<td width="80" align="right">
										<a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id."__".$detarmination_id; ?>',1,'receive_popup','<? echo str_replace("'","",$cbo_store_id); ?>');">
										<? echo number_format($total_receive,2,'.',''); ?>
										</a>
									</td>
									<td width="80" align="right"><p><? $rec_bal=$book_qty-$total_receive; $subtot_rec_bal+=$rec_bal;echo number_format($rec_bal,2,'.',''); ?></p></td>
									<td width="80" align="right"><? echo number_format($trans_out,2,'.',''); ?></td>
									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id."__".$detarmination_id; ?>',3,'issue_popup','<? echo str_replace("'","",$cbo_store_id); ?>');"> <? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
									<td width="70" align="right">
									<a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id; ?>',4,'receive_ret_popup');">
										<? echo number_format($rec_ret_qnty,2,'.',''); ?>
									</a>
									<td width="80" align="right"><? echo number_format($total_issue,2,'.',''); ?></td>
									<td width="80" align="right">
										<p>
											<a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo chop($row['prod_id'],','); ?>','<? echo $color_id."__".$detarmination_id; ?>',5,'stock_popup',<? echo $cbo_store_id;?>);">
												<? echo $stock; ?>
											</a>
										</p>
									</td>
									<td width="80" align="right"><? echo $daysOnHand; ?></td>
								</tr>
								<?
								$i++;
								$total_order_qnty+=$row[csf("po_quantity")];
								$rec_trns_in_qnty+=$trans_in_qty;
								$total_req_qty+=$book_qty;
								$total_rec_qty+=$rec_qty;
								$total_receive_qnty+=$total_receive;
								$total_rec_bal+=$rec_bal;
								$issue_trns_out_qnty+=$trans_out;
								$total_issue_qty+=$iss_qty;
								$total_issue_quantity+=$total_issue;
								$total_stock+=$stockQnty;
								$total_possible_cut_pcs+=$possible_cut_pcs;
								$total_rec_return_qnty+=$rec_ret_qnty;
								$total_issue_ret_qnty+=$issue_ret_qnty;
								$total_actual_cut_qty+=$actual_qty;
							}
						}
						?>
						</table>
						<table width="2460" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
							<tfoot>
								<th width="30"></th>
								<th width="70"></th>
								<th width="60"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="110">Grand Total</th>
								<th width="80" align="right" id=""><? //echo $total_order_qnty; ?></th>
								<th width="80"></th>
								<th width="80"></th>
								<th width="80"></th>
								<th width="80"></th>
								<th width="80"></th>
								<th width="100">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="90">&nbsp;</th>
								<th width="90">&nbsp;</th>
								<th width="80" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
                                <th width="80" align="right" id="value_total_tranin_qty"><? echo number_format($rec_trns_in_qnty,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
								<th width="70" align="right" id="value_total_issue_ret_qty"><? echo number_format($total_issue_ret_qnty,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_receive_qty"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>
                                <th width="80" align="right" id="value_total_tranout_qty"><? echo number_format($issue_trns_out_qnty,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
								<th width="70"  id="value_recv_ret_qty"><? echo number_format($total_rec_return_qnty,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_issue_quantity"><? echo number_format($total_issue_quantity,2,'.',''); ?></th>
								<th width="80" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
								<th width="80" align="right"></th>
							</tfoot>
						</table>
					</div>
				</fieldset>
				<?
		}
		else
		{
			?>
			<div style="color: red;font-size: 18px;text-align: center;font-weight: bold;">Only Work For Order Wise!</div>
			<?
			die();
		}
	}
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$type";
    exit();
}

if($action=="open_exfactory")
{
	echo load_html_head_contents("Ex-factory Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:320px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="300" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th width="50">Sl</th>
						<th width="100">Ex-factory Date</th>
						<th>Ex-factory Qty</th>
					</tr>
				</thead>
				<tbody>
					<?

					$sql="select id, ex_factory_date, ex_factory_qnty
					from  pro_ex_factory_mst
					where status_active=1 and is_deleted=0 and po_break_down_id in($po_id)";
					//echo $mrr_sql;

					$dtlsArray=sql_select($sql);
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="50" align="center"><p><? echo $i; ?></p></td>
							<td width="100" align="center"><p><? if($row[csf('ex_factory_date')]!="" &&  $row[csf('ex_factory_date')]!="0000-00-00") echo change_date_format($row[csf('ex_factory_date')]); ?></p></td>
							<td align="right"><? echo number_format($row[csf('ex_factory_qnty')],2); ?></td>
						</tr>
						<?
						$tot_exfact_qty+=$row[csf('ex_factory_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="2" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_exfact_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
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

if($action=="receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$color_ex = explode("__", $color);
	$color = $color_ex[0];
	$detarmination_id = $color_ex[1];
	//echo "test";die();
	if($detarmination_id=="")
	{
		?>
		<fieldset style="width:570px; margin-left:3px">
			<script>
				function print_window()
				{
					document.getElementById('scroll_body').style.overflow="auto";
	            	document.getElementById('scroll_body').style.maxHeight="none";
	            	$("#tbl_list_search tr:first").hide();
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
						'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
					d.close();
					document.getElementById('scroll_body').style.overflow="auto";
	            	document.getElementById('scroll_body').style.maxHeight="400px";
	            	$("#tbl_list_search tr:first").show();
				}

				$(document).ready(function(e) {
					setFilterGrid('tbl_list_search',-1);
				});

			</script>
			<?
			ob_start();
			?>
			<div align="center">
				<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
					<tr>
						<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
						<td> <div id="report_container"> </div> </td>
					</tr>
				</table>
			</div>
			<div id="scroll_body" align="center">

				<table border="1" class="rpt_table" rules="all" width="1775" cellpadding="0" cellspacing="0" align="center" >
					<thead>
						<tr>
							<th colspan="20">Receive Details</th>
						</tr>
						<tr>
							<th width="30">Sl</th>
							<th width="110">System ID</th>
							<th width="70">Receive Date</th>
							<th width="80">Dyeing Source</th>
							<th width="110">Dyeing Company</th>
							<th width="100">Challan No</th>
							<th width="80">Color</th>
							<th width="80">Batch No</th>
							<th width="60">Floor</th>
							<th width="60">Room</th>
							<th width="60">Rack No</th>
							<th width="60">Shelf</th>
							<th width="80">Grey Qty.</th>
							<th width="80">Fin. Rcv. Qty.</th>
							<th width="70">Process Loss Qty.</th>
							<th width="70">Process Loss %</th>
							<th width="200">Fabric Des.</th>
							<th width="50">GSM</th>
							<th width="50">F.Dia</th>
							<th>Collar/Cuff Pcs</th>
						</tr>
					</thead>
					<tbody id="tbl_list_search">
						<?
						$product_arr=return_library_array( "select id, product_name_details from product_details_master where status_active=1 and item_category_id=2 and company_id='$companyID'", "id", "product_name_details");
						$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where status_active=1 and company_id='$companyID'", "id", "batch_no");
						$row_info_sql = "select dtls_id, entry_form, qc_pass_qnty, reject_qnty from pro_roll_details where status_active=1 and is_deleted=0 and entry_form in (66,68) and po_breakdown_id in($po_id)";

						$room__rack_shelf_arr=return_library_array( "select floor_room_rack_id, floor_room_rack_name from  lib_floor_room_rack_mst where status_active=1 and company_id='$companyID'", "floor_room_rack_id", "floor_room_rack_name");

						$row_info = sql_select($row_info_sql);
						$roll_arr = array();
						foreach ($row_info as $roll_row) {
							$roll_arr[$roll_row[csf("dtls_id")]][$roll_row[csf("entry_form")]] += $roll_row[csf("qc_pass_qnty")]+$roll_row[csf("reject_qnty")];
						}

						$finish_production_info_sql = "select a.id as recv_id, a.recv_number, b.id as dtls_id, b.grey_used_qty, b.body_part_id, b.fabric_description_id
						from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c
						where a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=c.trans_id and b.trans_id>0 and c.entry_form in(7,37) and a.entry_form in (7,37) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id='$companyID' and c.po_breakdown_id in($po_id)";
						// echo $finish_production_info_sql;
						$finish_production_info = sql_select($finish_production_info_sql);
						$finish_production = array();
						foreach ($finish_production_info as $fin_row) {

							if ($dtls_id_check[$fin_row[csf("dtls_id")]]=="") {
								$dtls_id_check[$fin_row[csf("dtls_id")]]=$fin_row[csf("dtls_id")];
								$finish_production[$fin_row[csf("dtls_id")]][$fin_row[csf("fabric_description_id")]][$fin_row[csf("body_part_id")]]['grey_used_qty'] = $fin_row[csf("grey_used_qty")];
							}

						}
						/*echo '<pre>';
						print_r($finish_production);*/


						/*$mrr_sql="select a.recv_number, a.booking_no, a.receive_date, a.knitting_source, a.knitting_company, a.challan_no, a.emp_id, a.qc_name, b.id as dtls_id, b.rack_no as rack_no, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, sum(c.quantity) as quantity, sum(c.returnable_qnty) as returnable_qnty, c.color_id, c.entry_form, c.dtls_id
						from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c
						where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (7,37,66,68) and c.entry_form in (7,37,66,68)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' and c.trans_id >0 and c.trans_type=1
						group by a.recv_number, a.receive_date, a.booking_no, a.emp_id, b.rack_no, b.prod_id, b.body_part_id, b.fabric_description_id, c.color_id, a.knitting_source, a.knitting_company, a.challan_no, a.qc_name, b.batch_id, b.gsm, b.width, c.entry_form, c.dtls_id, b.id";*/

						$store_id=str_replace("'","",$store_id);
						$prod_id=str_replace("'","",$prod_id);
						$store_cond="";
						$prod_id_cond="";
						if($store_id>0) $store_cond=" and d.store_id=$store_id";
						if($prod_id>0 || $prod_id !="") $prod_id_cond=" and b.prod_id in($prod_id)";

						$mrr_sql="SELECT a.recv_number, a.booking_no, a.receive_date, a.knitting_source, a.knitting_company, a.challan_no, a.emp_id, a.qc_name, b.id as dtls_id, b.rack_no as rack_no,b.floor, b.room, b.shelf_no, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, sum(c.quantity) as quantity, sum(c.returnable_qnty) as returnable_qnty, c.color_id, c.entry_form, c.dtls_id
						from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, inv_transaction d, order_wise_pro_details c
						where a.id=b.mst_id and b.trans_id=d.id and b.id=c.dtls_id and d.id=c.trans_id and a.entry_form in (7,37,66,68) and c.entry_form in (7,37,66,68)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' and c.trans_id >0 and c.trans_type=1 $store_cond $prod_id_cond
						group by a.recv_number, a.receive_date, a.booking_no, a.emp_id, b.rack_no,b.floor, b.room, b.shelf_no, b.prod_id, b.body_part_id, b.fabric_description_id, c.color_id, a.knitting_source, a.knitting_company, a.challan_no, a.qc_name, b.batch_id, b.gsm, b.width, c.entry_form, c.dtls_id, b.id";

						$i=1;
						//echo $mrr_sql;die;

						$dtlsArray=sql_select($mrr_sql);

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
							if($row[csf('entry_form')]==37)
							{ // without roll
								 // echo $row[csf("recv_number")].' : '.$row[csf("fabric_description_id")].' : '.$row[csf("body_part_id")].'<br>';

								$grey_used_qty=$finish_production[$row[csf("dtls_id")]][$row[csf("fabric_description_id")]][$row[csf("body_part_id")]]['grey_used_qty'];

								/*$booking_qty=$finish_production[$row[csf("booking_no")]][$row[csf("fabric_description_id")]][$row[csf("body_part_id")]];*/
							}
							else
							{
								$grey_used_qty=$roll_arr[$row[csf("dtls_id")]][$row[csf("entry_form")]];
							}
							//$process_loss=($row[csf('quantity')]/$grey_used_qty)*100;
							$process_loss=($grey_used_qty-$row[csf('quantity')]);
							$process_loss_percent = ($grey_used_qty-$row[csf('quantity')])/$grey_used_qty*100;
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
								<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
								<td width="110"><p><? echo $knitting_company; ?></p></td>
								<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
								<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
								<td width="80"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
								<td width="60"><p><? echo $room__rack_shelf_arr[$row[csf('floor')]]; ?></p></td>
								<td width="60"><p><? echo $room__rack_shelf_arr[$row[csf('room')]]; ?></p></td>
								<td width="60"><p><? echo $room__rack_shelf_arr[$row[csf('rack_no')]]; ?></p></td>
								<td width="60"><p><? echo $room__rack_shelf_arr[$row[csf('shelf_no')]]; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($grey_used_qty,2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
								<td width="70" title="Fin Recv Qty/Grey Qty*100" align="right"><p><? echo number_format($process_loss); ?></p></td>
								<td width="70" title="(Grey Qty-Fin Recv Qty)/Grey Qty*100" align="right"><p><? echo number_format($process_loss_percent,2).'%'; ?></p></td>
								<td width="200" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
								<td width="50" align="center"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
								<td width="50" align="center"><p><? echo $row[csf('width')]; ?></p></td>
								<td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
							$tot_grey_used_qty+=$grey_used_qty;
							$tot_reject_qty+=$row[csf('returnable_qnty')];
							$i++;
						}

						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="12" align="right">Total</td>
							<td align="right"><? echo number_format($tot_grey_used_qty,2); ?> </td>

	                        <td align="right"><? echo number_format($tot_qty,2); ?> </td>
	                       <!-- <td align="right"><? //echo number_format($tot_qty_in,2); ?> </td>-->
							<td colspan="4"> </td>
							<td align="right">&nbsp;<? //echo number_format($tot_qty,2); ?>&nbsp;</td>
							<td align="right">&nbsp;<? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
						</tr>
					</tfoot>

				</table>
				<br>
				<table border="1" class="rpt_table" rules="all" width="1040" cellpadding="0" cellspacing="0" align="left">
					<thead>
						<tr>
							<th colspan="11">Transfer In Details</th>
						</tr>
						<tr>
							<th width="30">Sl</th>
							<th width="130">Transfer ID</th>
							<th width="70">Transfer Date</th>
							<th width="60">Floor</th>
							<th width="60">Room</th>
							<th width="60">Rack</th>
							<th width="60">Shelf</th>
							<th width="60">Batch</th>
							<th width="300">Fabric Des.</th>
							<th width="50">UOM</th>
							<th>Qty</th>
						</tr>
					</thead>
					<tbody>
						<?
						$store_transf_cond="";
						if($store_id>0) $store_transf_cond=" and b.to_store=$store_id"; //cons_quantity
						$sql_transfer_in="SELECT a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, sum(d.cons_quantity) as transfer_out_qnty, b.batch_id,d.floor_id, d.room, d.rack, d.self
						from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, inv_transaction d
						where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.trans_id=d.id and c.entry_form in(14,15,134) and c.trans_type=5 and c.color_id='$color' and a.transfer_criteria in(1,2,4) and c.po_breakdown_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 $store_transf_cond
						group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, b.batch_id,d.floor_id, d.room, d.rack, d.self order by a.id";
						//echo $sql_transfer_in;die;
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where status_active=1 and company_id='$companyID'", "id", "batch_no");

						$transfer_data=sql_select($sql_transfer_in);
						//echo "<pre>";print_r($transfer_data);die;
						$i=1;
						foreach($transfer_data as $row)
						{
							//echo "jahid";die;
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center"><p><? echo $i; ?></p></td>
								<td><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
								<td><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
								<td><p><? echo $room__rack_shelf_arr[$row[csf('floor_id')]]; ?></p></td>
								<td><p><? echo $room__rack_shelf_arr[$row[csf('room')]]; ?></p></td>
								<td><p><? echo $room__rack_shelf_arr[$row[csf('rack')]]; ?></p></td>
								<td><p><? echo $room__rack_shelf_arr[$row[csf('self')]]; ?></p></td>
								<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
								<td><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
								<td><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?> &nbsp;</p></td>
								<td  align="right"><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?></p></td>
							</tr>
							<?
							$tot_trans_qty+=$row[csf('transfer_out_qnty')];
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="10" align="right">Total</td>
							<td align="right"><? echo number_format($tot_trans_qty,2); ?>&nbsp;</td>
						</tr>
						<tr class="tbl_bottom">
							<td colspan="10" align="right">Total Receive Balance</td>
							<td align="right">&nbsp;<? $tot_balance=$tot_qty+$tot_trans_qty; echo number_format($tot_balance,2); ?>&nbsp;</td>
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
	}
	else // for show2 btn
	{
		?>
		<fieldset style="width:570px; margin-left:3px">
			<script>
				function print_window()
				{
					document.getElementById('scroll_body').style.overflow="auto";
	            	document.getElementById('scroll_body').style.maxHeight="none";
	            	$("#tbl_list_search tr:first").hide();
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
						'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
					d.close();
					document.getElementById('scroll_body').style.overflow="auto";
	            	document.getElementById('scroll_body').style.maxHeight="400px";
	            	$("#tbl_list_search tr:first").show();
				}

				$(document).ready(function(e) {
					setFilterGrid('tbl_list_search',-1);
				});

			</script>
			<?
			ob_start();
			?>
			<div align="center">
				<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
					<tr>
						<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
						<td> <div id="report_container"> </div> </td>
					</tr>
				</table>
			</div>
			<div id="scroll_body" align="center">

				<table border="1" class="rpt_table" rules="all" width="1675" cellpadding="0" cellspacing="0" align="center" >
					<thead>
						<tr>
							<th colspan="19">Receive Details</th>
						</tr>
						<tr>
							<th width="30">Sl</th>
							<th width="110">System ID</th>
							<th width="70">Receive Date</th>
							<th width="80">Dyeing Source</th>
							<th width="110">Dyeing Company</th>
							<th width="100">Challan No</th>
							<th width="80">Color</th>
							<th width="80">Batch No</th>
							<th width="60">Room</th>
							<th width="60">Rack No</th>
							<th width="60">Shelf</th>
							<th width="80">Grey Qty.</th>
							<th width="80">Fin. Rcv. Qty.</th>
							<th width="70">Process Loss Qty.</th>
							<th width="70">Process Loss %</th>
							<th width="200">Fabric Des.</th>
							<th width="50">GSM</th>
							<th width="50">F.Dia</th>
							<th>Collar/Cuff Pcs</th>
						</tr>
					</thead>
					<tbody id="tbl_list_search">
						<?
						$product_arr=return_library_array( "select id, product_name_details from product_details_master where status_active=1 and item_category_id=2 and company_id='$companyID'", "id", "product_name_details");
						$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where status_active=1 and company_id='$companyID'", "id", "batch_no");
						$row_info_sql = "select dtls_id, entry_form, qc_pass_qnty, reject_qnty from pro_roll_details where status_active=1 and is_deleted=0 and entry_form in (66,68) and po_breakdown_id in($po_id)";

						$room__rack_shelf_arr=return_library_array( "select floor_room_rack_id, floor_room_rack_name from  lib_floor_room_rack_mst where status_active=1 and company_id='$companyID'", "floor_room_rack_id", "floor_room_rack_name");

						$row_info = sql_select($row_info_sql);
						$roll_arr = array();
						foreach ($row_info as $roll_row) {
							$roll_arr[$roll_row[csf("dtls_id")]][$roll_row[csf("entry_form")]] += $roll_row[csf("qc_pass_qnty")]+$roll_row[csf("reject_qnty")];
						}

						$finish_production_info_sql = "select a.id as recv_id, a.recv_number, b.id as dtls_id, b.grey_used_qty, b.body_part_id, b.fabric_description_id
						from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c
						where a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=c.trans_id and b.trans_id>0 and c.entry_form in(7,37) and a.entry_form in (7,37) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id='$companyID' and c.po_breakdown_id in($po_id)";
						// echo $finish_production_info_sql;
						$finish_production_info = sql_select($finish_production_info_sql);
						$finish_production = array();
						foreach ($finish_production_info as $fin_row) {

							if ($dtls_id_check[$fin_row[csf("dtls_id")]]=="") {
								$dtls_id_check[$fin_row[csf("dtls_id")]]=$fin_row[csf("dtls_id")];
								$finish_production[$fin_row[csf("dtls_id")]][$fin_row[csf("fabric_description_id")]][$fin_row[csf("body_part_id")]]['grey_used_qty'] = $fin_row[csf("grey_used_qty")];
							}

						}
						/*echo '<pre>';
						print_r($finish_production);*/


						/*$mrr_sql="select a.recv_number, a.booking_no, a.receive_date, a.knitting_source, a.knitting_company, a.challan_no, a.emp_id, a.qc_name, b.id as dtls_id, b.rack_no as rack_no, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, sum(c.quantity) as quantity, sum(c.returnable_qnty) as returnable_qnty, c.color_id, c.entry_form, c.dtls_id
						from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c
						where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (7,37,66,68) and c.entry_form in (7,37,66,68)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' and c.trans_id >0 and c.trans_type=1
						group by a.recv_number, a.receive_date, a.booking_no, a.emp_id, b.rack_no, b.prod_id, b.body_part_id, b.fabric_description_id, c.color_id, a.knitting_source, a.knitting_company, a.challan_no, a.qc_name, b.batch_id, b.gsm, b.width, c.entry_form, c.dtls_id, b.id";*/

						$store_id=str_replace("'","",$store_id);
						$prod_id=str_replace("'","",$prod_id);
						$store_cond="";
						$prod_id_cond="";
						if($store_id>0) $store_cond=" and d.store_id=$store_id";
						if($prod_id>0 || $prod_id !="") $prod_id_cond=" and b.prod_id in($prod_id)";

						$mrr_sql="SELECT a.recv_number, a.booking_no, a.receive_date, a.knitting_source, a.knitting_company, a.challan_no, a.emp_id, a.qc_name, b.id as dtls_id, b.rack_no as rack_no, b.room, b.shelf_no, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, sum(c.quantity) as quantity, sum(c.returnable_qnty) as returnable_qnty, c.color_id, c.entry_form, c.dtls_id
						from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, inv_transaction d, order_wise_pro_details c
						where a.id=b.mst_id and b.trans_id=d.id and b.id=c.dtls_id and d.id=c.trans_id and a.entry_form in (7,37,66,68) and c.entry_form in (7,37,66,68)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' and c.trans_id >0 and c.trans_type=1 $store_cond $prod_id_cond
						group by a.recv_number, a.receive_date, a.booking_no, a.emp_id, b.rack_no, b.room, b.shelf_no, b.prod_id, b.body_part_id, b.fabric_description_id, c.color_id, a.knitting_source, a.knitting_company, a.challan_no, a.qc_name, b.batch_id, b.gsm, b.width, c.entry_form, c.dtls_id, b.id";

						$i=1;
						//echo $mrr_sql;die;

						$dtlsArray=sql_select($mrr_sql);

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
							if($row[csf('entry_form')]==37)
							{ // without roll
								 // echo $row[csf("recv_number")].' : '.$row[csf("fabric_description_id")].' : '.$row[csf("body_part_id")].'<br>';

								$grey_used_qty=$finish_production[$row[csf("dtls_id")]][$row[csf("fabric_description_id")]][$row[csf("body_part_id")]]['grey_used_qty'];

								/*$booking_qty=$finish_production[$row[csf("booking_no")]][$row[csf("fabric_description_id")]][$row[csf("body_part_id")]];*/
							}
							else
							{
								$grey_used_qty=$roll_arr[$row[csf("dtls_id")]][$row[csf("entry_form")]];
							}
							//$process_loss=($row[csf('quantity')]/$grey_used_qty)*100;
							$process_loss=($grey_used_qty-$row[csf('quantity')]);
							$process_loss_percent = ($grey_used_qty-$row[csf('quantity')])/$grey_used_qty*100;
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
								<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
								<td width="110"><p><? echo $knitting_company; ?></p></td>
								<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
								<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
								<td width="80"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
								<td width="60"><p><? echo $room__rack_shelf_arr[$row[csf('room')]]; ?></p></td>
								<td width="60"><p><? echo $room__rack_shelf_arr[$row[csf('rack_no')]]; ?></p></td>
								<td width="60"><p><? echo $room__rack_shelf_arr[$row[csf('shelf_no')]]; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($grey_used_qty,2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
								<td width="70" title="Fin Recv Qty/Grey Qty*100" align="right"><p><? echo number_format($process_loss); ?></p></td>
								<td width="70" title="(Grey Qty-Fin Recv Qty)/Grey Qty*100" align="right"><p><? echo number_format($process_loss_percent,2).'%'; ?></p></td>
								<td width="200" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
								<td width="50" align="center"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
								<td width="50" align="center"><p><? echo $row[csf('width')]; ?></p></td>
								<td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
							$tot_grey_used_qty+=$grey_used_qty;
							$tot_reject_qty+=$row[csf('returnable_qnty')];
							$i++;
						}

						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="11" align="right">Total</td>
							<td align="right"><? echo number_format($tot_grey_used_qty,2); ?> </td>

	                        <td align="right"><? echo number_format($tot_qty,2); ?> </td>
	                       <!-- <td align="right"><? //echo number_format($tot_qty_in,2); ?> </td>-->
							<td colspan="4"> </td>
							<td align="right">&nbsp;<? //echo number_format($tot_qty,2); ?>&nbsp;</td>
							<td align="right">&nbsp;<? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
						</tr>
					</tfoot>

				</table>
				<br>
				<table border="1" class="rpt_table" rules="all" width="940" cellpadding="0" cellspacing="0" align="left">
					<thead>
						<tr>
							<th colspan="10">Transfer In Details</th>
						</tr>
						<tr>
							<th width="30">Sl</th>
							<th width="130">Transfer ID</th>
							<th width="70">Transfer Date</th>
							<th width="60">Room</th>
							<th width="60">Rack</th>
							<th width="60">Shelf</th>
							<th width="60">Batch</th>
							<th width="300">Fabric Des.</th>
							<th width="50">UOM</th>
							<th>Qty</th>
						</tr>
					</thead>
					<tbody>
						<?
						$store_transf_cond="";
						if($store_id>0) $store_transf_cond=" and b.to_store=$store_id"; //cons_quantity
						$sql_transfer_in="SELECT a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, sum(d.cons_quantity) as transfer_out_qnty, b.batch_id, d.room, d.rack, d.self
						from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, inv_transaction d, product_details_master e
						where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.trans_id=d.id and c.entry_form in(14,15,134) and c.trans_type=5 and c.color_id='$color' and d.prod_id=e.id and e.detarmination_id=$detarmination_id and a.transfer_criteria in(1,2,4) and c.po_breakdown_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 $store_transf_cond
						group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, b.batch_id, d.room, d.rack, d.self order by a.id";
						//echo $sql_transfer_in;die;
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where status_active=1 and company_id='$companyID'", "id", "batch_no");

						$transfer_data=sql_select($sql_transfer_in);
						//echo "<pre>";print_r($transfer_data);die;
						$i=1;
						foreach($transfer_data as $row)
						{
							//echo "jahid";die;
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center"><p><? echo $i; ?></p></td>
								<td><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
								<td><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
								<td><p><? echo $room__rack_shelf_arr[$row[csf('room')]]; ?></p></td>
								<td><p><? echo $room__rack_shelf_arr[$row[csf('rack')]]; ?></p></td>
								<td><p><? echo $room__rack_shelf_arr[$row[csf('self')]]; ?></p></td>
								<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
								<td><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
								<td><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?> &nbsp;</p></td>
								<td  align="right"><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?></p></td>
							</tr>
							<?
							$tot_trans_qty+=$row[csf('transfer_out_qnty')];
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="9" align="right">Total</td>
							<td align="right"><? echo number_format($tot_trans_qty,2); ?>&nbsp;</td>
						</tr>
						<tr class="tbl_bottom">
							<td colspan="9" align="right">Total Receive Balance</td>
							<td align="right">&nbsp;<? $tot_balance=$tot_qty+$tot_trans_qty; echo number_format($tot_balance,2); ?>&nbsp;</td>
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
	}
	exit();
}//Knit Finish end

if($action=="issue_ret_popup")
{
	echo load_html_head_contents("Issue Ret. Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1080px;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

			/*	$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        }); */

    </script>
    <?
    ob_start();
    ?>
    <div id="scroll_body" align="center">
    	<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
    		<tr>
    			<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
    			<td> <div id="report_container"> </div> </td>
    		</tr>
    	</table>
    	<table border="1" class="rpt_table" rules="all" width="1080" cellpadding="0" cellspacing="0" align="center">
    		<thead>
    			<tr>
    				<th colspan="11">Issue Return Details</th>
    			</tr>
    			<tr>
    				<th width="30">Sl</th>
    				<th width="110">System ID</th>
    				<th width="80">Ret. Date</th>
    				<th width="80">Dyeing Source</th>
    				<th width="120">Dyeing Company</th>
    				<th width="100">Challan No</th>
    				<th width="100">Color</th>
    				<th width="100">Batch No</th>
    				<th width="80">Rack</th>
    				<th width="80">Ret. Qty</th>
    				<th width="">Fabric Des.</th>

    			</tr>
    		</thead>
    		<tbody>
    			<?
    			$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
    			$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
    			$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68)";
    			$result_issue=sql_select($sql_issue);
    			$issue_arr=array();
    			foreach($result_issue as $row)
    			{
    				$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
    				$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
    				$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
    			}

    			$i=1;

    			$ret_sql="select a.recv_number,a.challan_no,a.issue_id, a.receive_date,b.prod_id,b.pi_wo_batch_no, sum(c.quantity) as quantity,sum(c.returnable_qnty) as returnable_qnty,c.color_id from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
    			where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (52,126) and c.entry_form in (52,126)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' and c.trans_id!=0 group by a.recv_number,a.challan_no, a.receive_date,b.prod_id,a.issue_id, b.pi_wo_batch_no,c.color_id";
					//echo $ret_sql;

    			$retDataArray=sql_select($ret_sql);

    			foreach($retDataArray as $row)
    			{
    				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
    				$rack=$issue_arr[$row[csf('pi_wo_batch_no')]]['rack'];
						//echo $row[csf('pi_wo_batch_no')].'='.$batch_no_arr[$row[csf('pi_wo_batch_no')]];
    				$knit_dye_source=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_source'];
    				$knit_dye_company=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_company'];

    				if($knit_dye_source==1)
    				{
    					$knitting_company=$company_arr[$knit_dye_company];
    				}
    				else
    				{
    					$knitting_company=$supplier_name_arr[$knit_dye_company];
    				}


    				?>
    				<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
    					<td width="30"><p><? echo $i; ?></p></td>
    					<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
    					<td width="80"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
    					<td width="80"><p><? echo $knitting_source[$knit_dye_source]; ?></p></td>
    					<td width="120" ><p><? echo $knitting_company; ?></p></td>
    					<td width="100" ><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
    					<td  width="100" align="right"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
    					<td  width="100" align="right"><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
    					<td  width="80" align="right"><p><? echo $row[csf('Rack')]; ?></p></td>
    					<td  width="80" align="right"><p><? echo $row[csf('quantity')]; ?></p></td>

    					<td align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
    				</tr>
    				<?
    				$tot_issue_return_qty+=$row[csf('quantity')];
						//$tot_returnable_qnty+=$row[csf('returnable_qnty')];
    				$i++;
    			}
    			?>
    		</tbody>
    		<tfoot>
    			<tr class="tbl_bottom">
    				<td colspan="9" align="right">Total</td>
    				<td align="right">&nbsp;<? echo number_format($tot_issue_return_qty,2); ?>&nbsp;</td>
    				<td align="right">&nbsp;</td>
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

if($action=="trans_in_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$color_ex = explode("__", $color);
	$color = $color_ex[0];
	$detarmination_id = $color_ex[1];
	//echo "test";die();
	if($detarmination_id=="")
	{
		?>
		<fieldset style="width:570px; margin-left:3px">
			<script>
				function print_window()
				{
					document.getElementById('scroll_body').style.overflow="auto";
	            	document.getElementById('scroll_body').style.maxHeight="none";
	            	$("#tbl_list_search tr:first").hide();
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
						'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
					d.close();
					document.getElementById('scroll_body').style.overflow="auto";
	            	document.getElementById('scroll_body').style.maxHeight="400px";
	            	$("#tbl_list_search tr:first").show();
				}

				$(document).ready(function(e) {
					setFilterGrid('tbl_list_search',-1);
				});
			</script>
			<?
			ob_start();
			?>
			<div align="center">
				<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
					<tr>
						<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
						<td> <div id="report_container"> </div> </td>
					</tr>
				</table>
			</div>
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="530" cellpadding="0" cellspacing="0" align="left">
					<thead>
						<tr>
							<th colspan="6">Transfer In Details</th>
						</tr>
						<tr>
							<th width="30">Sl</th>
							<th width="70">Transfer Date</th>
							<th width="130">Transfer ID</th>
							<th width="100">Batch</th>
							<th width="100">Color</th>
							<th>Qty</th>
						</tr>
					</thead>
					<tbody>
						<?
						$store_transf_cond="";
						if($store_id>0) $store_transf_cond=" and b.to_store=$store_id"; //cons_quantity
						$sql_transfer_in="SELECT a.id, a.transfer_system_id, a.transfer_date, sum(d.cons_quantity) as transfer_in_qnty, b.batch_id, b.color_id
						from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, inv_transaction d
						where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.trans_id=d.id and c.entry_form in(14,15,134) and c.trans_type=5 and b.color_id='$color' and a.transfer_criteria in(1,2,4) and c.po_breakdown_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 $store_transf_cond
						group by a.id, a.transfer_system_id, a.transfer_date, b.batch_id, b.color_id";
						//echo $sql_transfer_in;//die;//  and c.color_id='$color'
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where status_active=1 and company_id='$companyID'", "id", "batch_no");

						$transfer_data=sql_select($sql_transfer_in);
						//echo "<pre>";print_r($transfer_data);die;
						$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0 and id=$color","id","color_name");
						$i=1;
						foreach($transfer_data as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center"><p><? echo $i; ?></p></td>
								<td><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
								<td><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
								<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
								<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
								<td  align="right"><p><? echo number_format($row[csf('transfer_in_qnty')],2); ?></p></td>
							</tr>
							<?
							$tot_trans_qty+=$row[csf('transfer_in_qnty')];
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="5" align="right">Total</td>
							<td align="right"><? echo number_format($tot_trans_qty,2); ?>&nbsp;</td>
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
	}
	exit();
} //Knit Finish end

if($action=="trans_out_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$color_ex = explode("__", $color);
	$color = $color_ex[0];
	$detarmination_id = $color_ex[1];
	//echo "test";die();
	if($detarmination_id=="")
	{
		?>
		<fieldset style="width:570px; margin-left:3px">
			<script>
				function print_window()
				{
					document.getElementById('scroll_body').style.overflow="auto";
	            	document.getElementById('scroll_body').style.maxHeight="none";
	            	$("#tbl_list_search tr:first").hide();
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
						'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
					d.close();
					document.getElementById('scroll_body').style.overflow="auto";
	            	document.getElementById('scroll_body').style.maxHeight="400px";
	            	$("#tbl_list_search tr:first").show();
				}

				$(document).ready(function(e) {
					setFilterGrid('tbl_list_search',-1);
				});
			</script>
			<?
			ob_start();
			?>
			<div align="center">
				<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
					<tr>
						<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
						<td> <div id="report_container"> </div> </td>
					</tr>
				</table>
			</div>
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="530" cellpadding="0" cellspacing="0" align="left">
					<thead>
						<tr>
							<th colspan="6">Transfer Out Details</th>
						</tr>
						<tr>
							<th width="30">Sl</th>
							<th width="70">Transfer Date</th>
							<th width="130">Transfer ID</th>
							<th width="100">Batch</th>
							<th width="100">Color</th>
							<th>Qty</th>
						</tr>
					</thead>
					<tbody>
						<?
						$store_transf_cond="";
						if($store_id>0) $store_transf_cond=" and b.to_store=$store_id"; //cons_quantity
						$sql_transfer_in="SELECT a.id, a.transfer_system_id, a.transfer_date, sum(d.cons_quantity) as transfer_in_qnty, b.batch_id, b.color_id
						from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, inv_transaction d
						where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.trans_id=d.id and c.entry_form in(14,15,134) and c.trans_type=6 and b.color_id='$color' and a.transfer_criteria in(1,2,4) and c.po_breakdown_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 $store_transf_cond
						group by a.id, a.transfer_system_id, a.transfer_date, b.batch_id, b.color_id";
						//echo $sql_transfer_in;//die;//  and c.color_id='$color'
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where status_active=1 and company_id='$companyID'", "id", "batch_no");

						$transfer_data=sql_select($sql_transfer_in);
						//echo "<pre>";print_r($transfer_data);die;
						$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0 and id=$color","id","color_name");
						$i=1;
						foreach($transfer_data as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center"><p><? echo $i; ?></p></td>
								<td><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
								<td><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
								<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
								<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
								<td  align="right"><p><? echo number_format($row[csf('transfer_in_qnty')],2); ?></p></td>
							</tr>
							<?
							$tot_trans_qty+=$row[csf('transfer_in_qnty')];
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="5" align="right">Total</td>
							<td align="right"><? echo number_format($tot_trans_qty,2); ?>&nbsp;</td>
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
	}
	exit();
} //Knit Finish end

if($action=="woven_receive_popup_old") //Not used
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="100">Receive ID</th>
						<th width="75">Receive Date</th>
						<th width="240">Fabric Description</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;

					$mrr_sql="select a.recv_number, a.receive_date, b.prod_id, sum(c.quantity) as quantity
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id  and a.entry_form in (17) and c.entry_form in (17)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' group by a.recv_number, a.receive_date, b.prod_id";
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
							<td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="240" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="4" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}
if($action=="woven_receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1000px; margin-left:3px">
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
	?>
		<div id="scroll_body" align="center">
        	<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
                <tr>
                    <td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
                     <td> <div id="report_container"> </div> </td>

                </tr>
             </table>
			<table border="1" class="rpt_table" rules="all" width="945" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="9">Receive Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="110">System ID</th>
                        <th width="70">Receive Date</th>
                        <th width="100">Challan No</th>
                        <th width="80">Color</th>
                        <th width="80">Fin. Rcv. Qty.</th>
                        <th width="200">Fabric Des.</th>
                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
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
					if($store_id) {$store_cond = " and b.store_id = $store_id";}
					if($from_date) {$date_cond = " and b.transaction_date <='".$from_date."'";}
					$mrr_sql="select a.recv_number, a.challan_no, a.receive_date,c.color_id, d.product_name_details, d.gsm, d.dia_width, sum(c.quantity) as quantity
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d
					where a.id=b.mst_id and b.id=c.trans_id  and a.entry_form in (17) and c.entry_form in (17)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.prod_id = d.id and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' $store_cond $date_cond
					group by a.recv_number, a.challan_no, a.receive_date,c.color_id, d.product_name_details, d.gsm, d.dia_width";

					$dtlsArray=sql_select($mrr_sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            <td width="200" ><p><? echo $row[csf('product_name_details')]; ?></p></td>
                            <td width="50" align="center"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
                            <td width="50" align="center"><p><? echo $row[csf('dia_width')]; ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$receive_quantity+=$row[csf('quantity')];
						$i++;

					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right">Total</td>
                       	<td align="right"><? echo number_format($receive_quantity,2); ?> </td>
                        <td align="right">&nbsp; </td>
                        <td align="right">&nbsp; </td>
                        <td align="right">&nbsp;</td>
                    </tr>
                </tfoot>
            </table>


            <table border="1" class="rpt_table" rules="all" width="945" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="9">Transfer In Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="110">System ID</th>
                        <th width="70">Receive Date</th>
                        <th width="100">Challan No</th>
                        <th width="80">Color</th>
                        <th width="80">Fin. Transfer. Qty.</th>
                        <th width="200">Fabric Des.</th>
                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                    </tr>
				</thead>
                <tbody>
                <?
                	if($store_id) {$store_cond = " and b.to_store=$store_id";}
                	if($from_date) {$transfer_date_cond = " and a.transfer_date <='".$from_date."'";}
					$i=1;
					$mrr_sql_trnsf="select a.transfer_system_id, a.transfer_date, a.challan_no, c.color_id, sum(c.quantity) as quantity, d.product_name_details, d.gsm, d.dia_width from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.to_trans_id=c.trans_id and c.prod_id=d.id and c.entry_form = 258 and c.trans_type=5 and a.to_company=$companyID and c.po_breakdown_id in ($po_id) and c.color_id='$color' $store_cond $transfer_date_cond and c.status_active=1 and b.status_active =1 and a.status_active=1 group by a.transfer_system_id, a.transfer_date, a.challan_no, c.color_id, d.product_name_details, d.gsm, d.dia_width";

					$dtlsArray=sql_select($mrr_sql_trnsf);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                             <td width="70"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>

                             <td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                             <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>

                            <td width="200" ><p><? echo $row[csf('product_name_details')]; ?></p></td>
                            <td width="50" align="center"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
                            <td width="50" align="center"><p><? echo $row[csf('dia_width')]; ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$trans_in_qty+=$row[csf('quantity')];
						$i++;

					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right">Total</td>
                        <td align="right"><? echo number_format($trans_in_qty,2); ?> </td>
                        <td align="right">&nbsp;</td>
                        <td align="right">&nbsp;</td>
                        <td align="right">&nbsp;</td>
                    </tr>
                </tfoot>
            </table>

            <table border="1" class="rpt_table" rules="all" width="945" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="18">Issue Return Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="110">System ID</th>
                        <th width="70">Receive Date</th>
                        <th width="100">Challan No</th>
                        <th width="80">Color</th>
                        <th width="80">Fin. Transfer. Qty.</th>
                        <th width="200">Fabric Des.</th>
                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                    </tr>
				</thead>
                <tbody>
                <?
                	if($store_id) $store_cond = " and b.store_id = $store_id";
					$mrr_sql_issue_rtn = "select a.recv_number, a.challan_no, b.transaction_date, b.store_id, d.product_name_details, d.gsm,d.dia_width, c.po_breakdown_id,c.color_id, c.quantity from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.trans_id and b.prod_id= d.id and a.company_id=$companyID and c.po_breakdown_id in ($po_id) and c.color_id ='$color' $store_cond $date_cond and a.entry_form =209 and b.transaction_type =4 and b.item_category=3 and b.status_active =1 and c.status_active =1 and a.status_active =1 order by a.id desc";
					$dtlsArray=sql_select($mrr_sql_issue_rtn);
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                             <td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                             <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>

                            <td width="200" ><p><? echo $row[csf('product_name_details')]; ?></p></td>
                            <td width="50" align="center"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
                            <td width="50" align="center"><p><? echo $row[csf('dia_width')]; ?></p></td>
                        </tr>
						<?
						$tot_issue_return_qty+=$row[csf('quantity')];
						$tot_qty+=$row[csf('quantity')];
						$i++;

					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_issue_return_qty,2); ?> </td>
                        <td align="right">&nbsp;</td>
                        <td >&nbsp;</td>
                        <td align="right">&nbsp;</td>
                    </tr>
                    <tr class="tbl_bottom">
                    	<td colspan="5" align="right">Grand Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?> </td>
                        <td align="right">&nbsp;</td>
                        <td >&nbsp;</td>
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

if($action=="issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );
	$color_ex = explode("__", $color);
	// echo "<pre>";print_r($color_ex);die;
	$color = $color_ex[0];
	$detarmination_id = $color_ex[1];
	// echo $detarmination_id.'==';die;

	?>
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
	?>
	<fieldset style="width:970px; margin-left:3px">
	<?
	if($detarmination_id=="")
	{
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1040" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Issue To Cutting Info</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Issue No</th>
						<th width="120">Issue to Company</th>
						<th width="100">Challan No</th>
						<th width="100">Issue Date</th>
						<th width="100">Color</th>
						<th width="100">Batch No</th>
						<th width="100">Issue Purpose</th>
						<th width="60">Rack No</th>
						<th width="80">Qty</th>
                       <!-- <th width="80">Trans. Out Qty.</th>-->
						<th width="">Fabric Des.</th>
					</tr>
				</thead>
				<tbody>
					<?
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
					$i=1;

					$sql_trans_out="select a.id, a.transfer_system_id as issue_number,a.to_company, a.transfer_date as issue_date,a.challan_no, b.uom, b.from_prod_id, (c.quantity) as quantity_out, b.batch_id,to_rack as rack_no,c.prod_id, c.quantity,c.color_id from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15,134) and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 ";
					//group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, b.batch_id,c.prod_id, c.quantity,c.color_id
					$trans_out=sql_select($sql_trans_out);

					if($store_id)
					{
						$store_cond = " and d.store_id = '$store_id'";
					}
					else
					{
						$store_cond = "";
					}
					if($type==5)
					{
						$issue_purpose_cond="and a.issue_purpose!=44";
					}
					$mrr_sql="SELECT a.id, a.company_id, a.issue_number, a.issue_date, a.challan_no, a.issue_purpose, a.entry_form, b.prod_id, b.batch_id, b.rack_no, b.cutting_unit, c.quantity,c.color_id, d.pi_wo_batch_no
					from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c, INV_TRANSACTION d
					where a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=d.id and c.trans_id=d.id and d.transaction_type=2 and d.item_category=2 and a.entry_form in(18,71) and c.entry_form in(18,71) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and d.is_deleted=0 and d.status_active=1 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID'  and c.color_id='$color' $store_cond $issue_purpose_cond and a.issue_purpose!=9"; //$store_cond
					// echo $mrr_sql;die;
					$dtlsArray=sql_select($mrr_sql);
					$tot_out_qty=0;
					foreach($dtlsArray as $row)
					{
						if ($row[csf('entry_form')]==71)
						{
							$batch_id=$row[csf('pi_wo_batch_no')];
						}
						else
						{
							$batch_id=$row[csf('batch_id')];
						}

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
							<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="100"><p><? echo $batch_no_arr[$batch_id]; ?></p></td>
							<td width="100"><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p></td>
							<td width="60" ><p><? echo $row[csf('rack_no')]; ?></p></td>
							<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                           <!-- <td width="80" align="right" ><p><? //echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>-->
							<td  align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}

					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?></td>
                        <td align="right"><? //echo number_format($tot_out_qty,2); ?></td>

					</tr>

				</tfoot>
			</table>

			<br>
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="display:none">
				<thead>
					<tr>
						<th colspan="6">Transfer Out Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="130">Transfer ID</th>
						<th width="70">Transfer Date</th>
						<th width="200">Fabric Des.</th>
						<th width="50">UOM</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?
					//$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$i=1;
					//$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b where a.company_id=$companyID and a.id=b.mst_id and a.transfer_criteria=4 and a.from_order_id in($po_id) and b.from_prod_id in ($prod_id) and a.item_category=2 group by a.from_order_id, b.from_prod_id, a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.from_prod_id, b.uom";

					$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15,134) and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id";
					$transfer_out=sql_select($sql_transfer_out);
					foreach($transfer_out as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="130"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
							<td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
							<td width="200" ><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
							<td width="50" ><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?> &nbsp;</p></td>
							<td  align="right"><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?></p></td>
						</tr>
						<?
						$tot_trans_qty+=$row[csf('transfer_out_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
						<td align="right"><? echo number_format($tot_trans_qty,2); ?>&nbsp;</td>
					</tr>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total Issue Balance</td>
						<td align="right"><? $tot_iss_bal=$tot_qty+$tot_trans_qty; echo number_format($tot_iss_bal,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>

			</table>
		</div>
		<?
	}
	else
	{
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1040" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Issue To Cutting Info</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Issue No</th>
						<th width="120">Issue to Company</th>
						<th width="100">Challan No</th>
						<th width="100">Issue Date</th>
						<th width="100">Color</th>
						<th width="100">Batch No</th>
						<th width="100">Issue Purpose</th>
						<th width="60">Rack No</th>
						<th width="80">Qty</th>
                       <!-- <th width="80">Trans. Out Qty.</th>-->
						<th width="">Fabric Des.</th>
					</tr>
				</thead>
				<tbody>
					<?
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
					$i=1;

					$sql_trans_out="select a.id, a.transfer_system_id as issue_number,a.to_company, a.transfer_date as issue_date,a.challan_no, b.uom, b.from_prod_id, (c.quantity) as quantity_out, b.batch_id,to_rack as rack_no,c.prod_id, c.quantity,c.color_id from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15,134) and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 ";
					//group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, b.batch_id,c.prod_id, c.quantity,c.color_id
					$trans_out=sql_select($sql_trans_out);

					if($store_id)
					{
						$store_cond = " and e.store_id = '$store_id'";
					}
					else
					{
						$store_cond = "";
					}
					if($type==5)
					{
						$issue_purpose_cond="and a.issue_purpose!=44";
					}
					$mrr_sql="SELECT a.id,a.company_id, a.issue_number, a.issue_purpose, a.issue_date,a.challan_no, a.entry_form, b.prod_id,b.batch_id,b.rack_no, b.cutting_unit, c.quantity,c.color_id, e.pi_wo_batch_no
					from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c,product_details_master d, INV_TRANSACTION e
					where a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=e.id and c.trans_id=e.id and e.transaction_type=2 and e.item_category=2 and a.entry_form in(18,71)  and c.entry_form in(18,71) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID'  and c.color_id='$color' $store_cond $issue_purpose_cond and c.prod_id=d.id and d.detarmination_id=$detarmination_id";
					//echo $mrr_sql;die;//a.issue_purpose!=9"

					$dtlsArray=sql_select($mrr_sql);
					$tot_out_qty=0;
					foreach($dtlsArray as $row)
					{
						if ($row[csf('entry_form')]==71)
						{
							$batch_id=$row[csf('pi_wo_batch_no')];
						}
						else
						{
							$batch_id=$row[csf('batch_id')];
						}
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
							<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="100"><p><? echo $batch_no_arr[$batch_id]; ?></p></td>
							<td width="100"><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p></td>
							<td width="60" ><p><? echo $row[csf('rack_no')]; ?></p></td>
							<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                           <!-- <td width="80" align="right" ><p><? //echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>-->
							<td  align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}

					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?></td>
                        <td align="right"><? //echo number_format($tot_out_qty,2); ?></td>

					</tr>

				</tfoot>
			</table>

			<br>
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="display:none">
				<thead>
					<tr>
						<th colspan="6">Transfer Out Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="130">Transfer ID</th>
						<th width="70">Transfer Date</th>
						<th width="200">Fabric Des.</th>
						<th width="50">UOM</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?
					//$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$i=1;
					//$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b where a.company_id=$companyID and a.id=b.mst_id and a.transfer_criteria=4 and a.from_order_id in($po_id) and b.from_prod_id in ($prod_id) and a.item_category=2 group by a.from_order_id, b.from_prod_id, a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.from_prod_id, b.uom";

					$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15,134) and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id";
					$transfer_out=sql_select($sql_transfer_out);
					foreach($transfer_out as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="130"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
							<td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
							<td width="200" ><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
							<td width="50" ><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?> &nbsp;</p></td>
							<td  align="right"><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?></p></td>
						</tr>
						<?
						$tot_trans_qty+=$row[csf('transfer_out_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">

						<td colspan="5" align="right">Total</td>
						<td align="right"><? echo number_format($tot_trans_qty,2); ?>&nbsp;</td>
					</tr>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total Issue Balance</td>
						<td align="right"><? $tot_iss_bal=$tot_qty+$tot_trans_qty; echo number_format($tot_iss_bal,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>

			</table>
		</div>
		<?
    }

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
} // Issue End

if($action=="cutting_issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );
	$color_ex = explode("__", $color);
	// echo "<pre>";print_r($color_ex);die;
	$color = $color_ex[0];
	$detarmination_id = $color_ex[1];
	// echo $detarmination_id.'==';die;

	?>
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
	?>
	<fieldset style="width:970px; margin-left:3px">
	<?
	if($detarmination_id=="")
	{
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1040" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Issue To Cutting Info</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Issue No</th>
						<th width="120">Issue to Company</th>
						<th width="100">Challan No</th>
						<th width="100">Issue Date</th>
						<th width="100">Color</th>
						<th width="100">Batch No</th>
						<th width="100">Issue Purpose</th>
						<th width="60">Rack No</th>
						<th width="80">Qty</th>
                       <!-- <th width="80">Trans. Out Qty.</th>-->
						<th width="">Fabric Des.</th>
					</tr>
				</thead>
				<tbody>
					<?
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
					$i=1;

					$sql_trans_out="select a.id, a.transfer_system_id as issue_number,a.to_company, a.transfer_date as issue_date,a.challan_no, b.uom, b.from_prod_id, (c.quantity) as quantity_out, b.batch_id,to_rack as rack_no,c.prod_id, c.quantity,c.color_id from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15,134) and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 ";
					//group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, b.batch_id,c.prod_id, c.quantity,c.color_id
					$trans_out=sql_select($sql_trans_out);

					if($store_id)
					{
						$store_cond = " and d.store_id = '$store_id'";
					}
					else
					{
						$store_cond = "";
					}
					if($type==5)
					{
						$issue_purpose_cond="and a.issue_purpose!=44";
					}
					$mrr_sql="SELECT a.id, a.company_id, a.issue_number, a.issue_date, a.challan_no, a.issue_purpose, a.entry_form, b.prod_id, b.batch_id, b.rack_no, b.cutting_unit, c.quantity,c.color_id, d.pi_wo_batch_no
					from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c, INV_TRANSACTION d
					where a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=d.id and c.trans_id=d.id and d.transaction_type=2 and d.item_category=2 and a.entry_form in(18,71) and c.entry_form in(18,71) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and d.is_deleted=0 and d.status_active=1 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID'  and c.color_id='$color' $store_cond $issue_purpose_cond and a.issue_purpose=9"; //$store_cond
					// echo $mrr_sql;die;
					$dtlsArray=sql_select($mrr_sql);
					$tot_out_qty=0;
					foreach($dtlsArray as $row)
					{
						if ($row[csf('entry_form')]==71)
						{
							$batch_id=$row[csf('pi_wo_batch_no')];
						}
						else
						{
							$batch_id=$row[csf('batch_id')];
						}

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
							<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="100"><p><? echo $batch_no_arr[$batch_id]; ?></p></td>
							<td width="100"><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p></td>
							<td width="60" ><p><? echo $row[csf('rack_no')]; ?></p></td>
							<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                           <!-- <td width="80" align="right" ><p><? //echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>-->
							<td  align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}

					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?></td>
                        <td align="right"><? //echo number_format($tot_out_qty,2); ?></td>

					</tr>

				</tfoot>
			</table>

			<br>
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="display:none">
				<thead>
					<tr>
						<th colspan="6">Transfer Out Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="130">Transfer ID</th>
						<th width="70">Transfer Date</th>
						<th width="200">Fabric Des.</th>
						<th width="50">UOM</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?
					//$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$i=1;
					//$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b where a.company_id=$companyID and a.id=b.mst_id and a.transfer_criteria=4 and a.from_order_id in($po_id) and b.from_prod_id in ($prod_id) and a.item_category=2 group by a.from_order_id, b.from_prod_id, a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.from_prod_id, b.uom";

					$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15,134) and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id";
					$transfer_out=sql_select($sql_transfer_out);
					foreach($transfer_out as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="130"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
							<td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
							<td width="200" ><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
							<td width="50" ><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?> &nbsp;</p></td>
							<td  align="right"><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?></p></td>
						</tr>
						<?
						$tot_trans_qty+=$row[csf('transfer_out_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
						<td align="right"><? echo number_format($tot_trans_qty,2); ?>&nbsp;</td>
					</tr>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total Issue Balance</td>
						<td align="right"><? $tot_iss_bal=$tot_qty+$tot_trans_qty; echo number_format($tot_iss_bal,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>

			</table>
		</div>
		<?
	}
	else
	{
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1040" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Issue To Cutting Info</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Issue No</th>
						<th width="120">Issue to Company</th>
						<th width="100">Challan No</th>
						<th width="100">Issue Date</th>
						<th width="100">Color</th>
						<th width="100">Batch No</th>
						<th width="100">Issue Purpose</th>
						<th width="60">Rack No</th>
						<th width="80">Qty</th>
                       <!-- <th width="80">Trans. Out Qty.</th>-->
						<th width="">Fabric Des.</th>
					</tr>
				</thead>
				<tbody>
					<?
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
					$i=1;

					$sql_trans_out="select a.id, a.transfer_system_id as issue_number,a.to_company, a.transfer_date as issue_date,a.challan_no, b.uom, b.from_prod_id, (c.quantity) as quantity_out, b.batch_id,to_rack as rack_no,c.prod_id, c.quantity,c.color_id from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15,134) and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 ";
					//group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, b.batch_id,c.prod_id, c.quantity,c.color_id
					$trans_out=sql_select($sql_trans_out);

					if($store_id)
					{
						$store_cond = " and e.store_id = '$store_id'";
					}
					else
					{
						$store_cond = "";
					}
					if($type==5)
					{
						$issue_purpose_cond="and a.issue_purpose!=44";
					}
					$mrr_sql="SELECT a.id,a.company_id, a.issue_number, a.issue_purpose, a.issue_date,a.challan_no, a.entry_form, b.prod_id,b.batch_id,b.rack_no, b.cutting_unit, c.quantity,c.color_id, e.pi_wo_batch_no
					from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c,product_details_master d, INV_TRANSACTION e
					where a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=e.id and c.trans_id=e.id and e.transaction_type=2 and e.item_category=2 and a.entry_form in(18,71)  and c.entry_form in(18,71) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID'  and c.color_id='$color' $store_cond $issue_purpose_cond and c.prod_id=d.id and d.detarmination_id=$detarmination_id";
					//echo $mrr_sql;die;//a.issue_purpose!=9"

					$dtlsArray=sql_select($mrr_sql);
					$tot_out_qty=0;
					foreach($dtlsArray as $row)
					{
						if ($row[csf('entry_form')]==71)
						{
							$batch_id=$row[csf('pi_wo_batch_no')];
						}
						else
						{
							$batch_id=$row[csf('batch_id')];
						}
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
							<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="100"><p><? echo $batch_no_arr[$batch_id]; ?></p></td>
							<td width="100"><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p></td>
							<td width="60" ><p><? echo $row[csf('rack_no')]; ?></p></td>
							<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                           <!-- <td width="80" align="right" ><p><? //echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>-->
							<td  align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}

					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?></td>
                        <td align="right"><? //echo number_format($tot_out_qty,2); ?></td>

					</tr>

				</tfoot>
			</table>

			<br>
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="display:none">
				<thead>
					<tr>
						<th colspan="6">Transfer Out Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="130">Transfer ID</th>
						<th width="70">Transfer Date</th>
						<th width="200">Fabric Des.</th>
						<th width="50">UOM</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?
					//$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$i=1;
					//$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b where a.company_id=$companyID and a.id=b.mst_id and a.transfer_criteria=4 and a.from_order_id in($po_id) and b.from_prod_id in ($prod_id) and a.item_category=2 group by a.from_order_id, b.from_prod_id, a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.from_prod_id, b.uom";

					$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15,134) and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id";
					$transfer_out=sql_select($sql_transfer_out);
					foreach($transfer_out as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="130"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
							<td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
							<td width="200" ><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
							<td width="50" ><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?> &nbsp;</p></td>
							<td  align="right"><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?></p></td>
						</tr>
						<?
						$tot_trans_qty+=$row[csf('transfer_out_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">

						<td colspan="5" align="right">Total</td>
						<td align="right"><? echo number_format($tot_trans_qty,2); ?>&nbsp;</td>
					</tr>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total Issue Balance</td>
						<td align="right"><? $tot_iss_bal=$tot_qty+$tot_trans_qty; echo number_format($tot_iss_bal,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>

			</table>
		</div>
		<?
    }

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
} // Cutting Issue End

if($action=="issue_reprocess_popup") //issue re process
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );
	$color_ex = explode("__", $color);
	$color = $color_ex[0];
	$detarmination_id = $color_ex[1];
	//echo $type;

	?>
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
	?>
	<fieldset style="width:970px; margin-left:3px">
	<?
	if($detarmination_id=="")
	{
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1040" cellpadding="0" cellspacing="0" align="center">
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
						<th width="100">Batch No</th>
						<th width="60">Rack No</th>
						<th width="80">Qty</th>
                       <!-- <th width="80">Trans. Out Qty.</th>-->
						<th width="">Fabric Des.</th>
					</tr>
				</thead>
				<tbody>
					<?
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
					$i=1;

					$sql_trans_out="select a.id, a.transfer_system_id as issue_number,a.to_company, a.transfer_date as issue_date,a.challan_no, b.uom, b.from_prod_id, (c.quantity) as quantity_out, b.batch_id,to_rack as rack_no,c.prod_id, c.quantity,c.color_id from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15,134) and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 ";
					//group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, b.batch_id,c.prod_id, c.quantity,c.color_id
					$trans_out=sql_select($sql_trans_out);

					if($store_id)
					{
						$store_cond = " and b.store_id = '$store_id'";
					}
					else
					{
						$store_cond = "";
					}

					$mrr_sql="select a.id,a.company_id, a.issue_number, a.issue_date,a.challan_no, b.prod_id,b.batch_id,b.rack_no, b.cutting_unit, c.quantity,c.color_id
					from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(18,71) and c.entry_form in(18,71) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID'  and c.color_id='$color' and a.issue_purpose=44 $store_cond";

					$dtlsArray=sql_select($mrr_sql);
					$tot_out_qty=0;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
							<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td width="60" ><p><? echo $row[csf('rack_no')]; ?></p></td>
							<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                           <!-- <td width="80" align="right" ><p><? //echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>-->
							<td  align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}

					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="8" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?></td>
                        <td align="right"><? //echo number_format($tot_out_qty,2); ?></td>

					</tr>

				</tfoot>
			</table>

			<br>
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="display:none">
				<thead>
					<tr>
						<th colspan="6">Transfer Out Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="130">Transfer ID</th>
						<th width="70">Transfer Date</th>
						<th width="200">Fabric Des.</th>
						<th width="50">UOM</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?
					//$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$i=1;
					//$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b where a.company_id=$companyID and a.id=b.mst_id and a.transfer_criteria=4 and a.from_order_id in($po_id) and b.from_prod_id in ($prod_id) and a.item_category=2 group by a.from_order_id, b.from_prod_id, a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.from_prod_id, b.uom";

					$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15,134) and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id";
					$transfer_out=sql_select($sql_transfer_out);
					foreach($transfer_out as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="130"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
							<td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
							<td width="200" ><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
							<td width="50" ><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?> &nbsp;</p></td>
							<td  align="right"><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?></p></td>
						</tr>
						<?
						$tot_trans_qty+=$row[csf('transfer_out_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
						<td align="right"><? echo number_format($tot_trans_qty,2); ?>&nbsp;</td>
					</tr>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total Issue Balance</td>
						<td align="right"><? $tot_iss_bal=$tot_qty+$tot_trans_qty; echo number_format($tot_iss_bal,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>

			</table>
		</div>
		<?
	}
	else
	{
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1040" cellpadding="0" cellspacing="0" align="center">
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
						<th width="100">Batch No</th>
						<th width="60">Rack No</th>
						<th width="80">Qty</th>
                       <!-- <th width="80">Trans. Out Qty.</th>-->
						<th width="">Fabric Des.</th>
					</tr>
				</thead>
				<tbody>
					<?
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
					$i=1;

					$sql_trans_out="select a.id, a.transfer_system_id as issue_number,a.to_company, a.transfer_date as issue_date,a.challan_no, b.uom, b.from_prod_id, (c.quantity) as quantity_out, b.batch_id,to_rack as rack_no,c.prod_id, c.quantity,c.color_id from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15,134) and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 ";
					//group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, b.batch_id,c.prod_id, c.quantity,c.color_id
					$trans_out=sql_select($sql_trans_out);

					if($store_id)
					{
						$store_cond = " and b.store_id = '$store_id'";
					}
					else
					{
						$store_cond = "";
					}

					$mrr_sql="SELECT a.id,a.company_id, a.issue_number, a.issue_date,a.challan_no, b.prod_id,b.batch_id,b.rack_no, b.cutting_unit, c.quantity,c.color_id
					from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c,product_details_master d
					where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(18,71)  and c.entry_form in(18,71) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID'  and c.color_id='$color' and a.issue_purpose=44 $store_cond and c.prod_id=d.id and d.detarmination_id=$detarmination_id ";
					//echo $mrr_sql;

					$dtlsArray=sql_select($mrr_sql);
					$tot_out_qty=0;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
							<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td width="60" ><p><? echo $row[csf('rack_no')]; ?></p></td>
							<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                           <!-- <td width="80" align="right" ><p><? //echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>-->
							<td  align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}

					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="8" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?></td>
                        <td align="right"><? //echo number_format($tot_out_qty,2); ?></td>

					</tr>

				</tfoot>
			</table>

			<br>
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="display:none">
				<thead>
					<tr>
						<th colspan="6">Transfer Out Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="130">Transfer ID</th>
						<th width="70">Transfer Date</th>
						<th width="200">Fabric Des.</th>
						<th width="50">UOM</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?
					//$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$i=1;
					//$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b where a.company_id=$companyID and a.id=b.mst_id and a.transfer_criteria=4 and a.from_order_id in($po_id) and b.from_prod_id in ($prod_id) and a.item_category=2 group by a.from_order_id, b.from_prod_id, a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.from_prod_id, b.uom";

					$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15,134) and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id";
					$transfer_out=sql_select($sql_transfer_out);
					foreach($transfer_out as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="130"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
							<td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
							<td width="200" ><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
							<td width="50" ><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?> &nbsp;</p></td>
							<td  align="right"><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?></p></td>
						</tr>
						<?
						$tot_trans_qty+=$row[csf('transfer_out_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">

						<td colspan="5" align="right">Total</td>
						<td align="right"><? echo number_format($tot_trans_qty,2); ?>&nbsp;</td>
					</tr>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total Issue Balance</td>
						<td align="right"><? $tot_iss_bal=$tot_qty+$tot_trans_qty; echo number_format($tot_iss_bal,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>

			</table>
		</div>
	<?
    }

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
} // Issue re process End

if($action=="receive_ret_popup")
{
	echo load_html_head_contents("Recv Ret. Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );


	?>
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
	?>
	<div id="report_id" align="center" style="width:960px">
		<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
				<td> <div id="report_container"> </div> </td>
			</tr>
		</table>
		<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0" align="center">
			<thead>
				<tr>
					<th colspan="10">Receive Return Details</th>
				</tr>
				<tr>
					<th width="30">Sl</th>
					<th width="110">Recv.Ret.ID</th>
					<th width="120">Recv.Ret.Company</th>
					<th width="100">Challan No</th>
					<th width="70">Return Date</th>
					<th width="100">Color</th>
					<th width="100">Batch No</th>
					<th width="70">Rack No</th>
					<th  width="70">Return Qty</th>
					<th width="">Fabric Des.</th>
				</tr>
			</thead>
			<tbody>
				<?
				$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );

				$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68)";
				$result_issue=sql_select($sql_issue);
				$issue_arr=array();
				foreach($result_issue as $row)
				{
					$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
					$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
					$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
				}

				$i=1;
				$ret_sql="select a.issue_number,a.company_id,a.challan_no,a.issue_date,b.pi_wo_batch_no,b.rack,c.color_id, b.prod_id, sum(c.quantity) as quantity
				from inv_issue_master a, inv_transaction b, order_wise_pro_details c
				where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (46) and c.entry_form in (46)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color'  and c.trans_id!=0 group by a.issue_number,a.company_id,a.challan_no,a.issue_date,b.pi_wo_batch_no,c.color_id,b.rack, b.prod_id";
				$retDataArray=sql_select($ret_sql);

				foreach($retDataArray as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$knit_dye_source=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_source'];
					$knit_dye_company=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_company'];
							//$rack=$issue_arr[$row[csf('pi_wo_batch_no')]]['rack'];
					if($knit_dye_source==1)
					{
						$knitting_company=$company_arr[$knit_dye_company];
					}
					else
					{
						$knitting_company=$supplier_name_arr[$knit_dye_company];
					}
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
						<td width="130"><p><? echo $knitting_company; ?></p></td>
						<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
						<td width="70"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
						<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
						<td width="100"><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
						<td width="70"><p><? echo $row[csf('rack')]; ?></p></td>

						<td  align="right" width="70"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
						<td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
					</tr>
					<?
					$tot_ret_qty+=$row[csf('quantity')];
					$i++;
				}
				?>
			</tbody>
			<tfoot>
				<tr class="tbl_bottom">
					<td colspan="8" align="right">Total</td>
					<td align="right"><? echo number_format($tot_ret_qty,2); ?>&nbsp;</td>
					<td> </td>
				</tr>

			</tfoot>
		</table>

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
	</div>
	<?

}



if($action=="woven_issue_popup_old") //Not used
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
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
			d.close();
		}

	</script>

	<?
	ob_start();
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th width="30">Sl</th>
					<th width="100">Issue ID</th>
					<th width="75">Issue Date</th>
					<th width="230">Fabric Description</th>
					<th>Qty</th>
				</thead>
				<tbody>
					<?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;

					$mrr_sql="select a.id, a.issue_number, a.issue_date, b.prod_id, c.quantity
					from  inv_issue_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=19 and c.entry_form=19 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and c.color_id='$color'";
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
							<td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
							<td width="200" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="4" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="woven_issue_popup")
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
		?>
	<fieldset style="width:1020px; margin-left:3px">

		<div id="report_id" align="center">
         <table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
                     <td> <div id="report_container"> </div> </td>
                </tr>
             </table>
			<table border="1" class="rpt_table" rules="all" width="1045" cellpadding="0" cellspacing="0" align="left">
				<thead>
                   <tr>
                    	<th colspan="10">Issue To Cutting Info</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="110">Issue No</th>
                        <th width="120">Issue to Company</th>
                        <th width="100">Challan No</th>
                        <th width="70">Issue Date</th>
                        <th width="80">Color</th>
                        <th width="80">Qty</th>
                        <th width="200">Fabric Des.</th>
                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                    </tr>
				</thead>
                <tbody>
                <?
					$i=1;
					if($store_id) {$store_cond = " and b.store_id='$store_id'";}
					if($from_date) {$date_cond = " and b.transaction_date <='$from_date'";}
					$mrr_sql="select a.company_id, a.issue_number, a.challan_no,a.issue_date, d.product_name_details, d.gsm, d.dia_width, sum(c.quantity) as quantity,c.color_id
					from  inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id = d.id and a.entry_form=19 and c.entry_form=19 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and c.color_id='$color' $store_cond $date_cond group by a.company_id, a.issue_number, a.challan_no,a.issue_date, d.product_name_details, d.gsm, d.dia_width, c.color_id";
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
                            <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td align="center" width="70"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td align="center" width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                            <td width="200" align="right"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                            <td width="50" align="right"><p><? echo $row[csf('gsm')]; ?></p></td>
                            <td width="50" align="right"><p><? echo $row[csf('dia_width')]; ?></p></td>
                        </tr>
						<?

						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="6" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?></td>
                        <td>&nbsp; </td>
                        <td>&nbsp; </td>
                        <td>&nbsp; </td>
                    </tr>
                </tfoot>
            </table>
            <table border="1" class="rpt_table" rules="all" width="1045" cellpadding="0" cellspacing="0" align="left">
				<thead>
                   <tr>
                    	<th colspan="10">Receive Return To Supplier</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="110">Issue No</th>
                        <th width="120">Issue to Company</th>
                        <th width="100">Challan No</th>
                        <th width="70">Issue Date</th>
                        <th width="80">Color</th>
                        <th width="80">Qty</th>
                        <th width="200">Fabric Des.</th>
                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                    </tr>
				</thead>
                <tbody>
                <?
					$i=1;
					if($store_id) {$store_cond = " and b.store_id='$store_id'";}
					$mrr_sql_recv_rtrn="select a.company_id, a.issue_number, a.challan_no,a.issue_date, d.product_name_details, d.gsm, d.dia_width, sum(c.quantity) as quantity,c.color_id
					from  inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id = d.id and a.entry_form=202 and c.entry_form=202 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and c.color_id='$color' $store_cond $date_cond group by a.company_id, a.issue_number, a.challan_no,a.issue_date, d.product_name_details, d.gsm, d.dia_width, c.color_id";
					$dtlsArray=sql_select($mrr_sql_recv_rtrn);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td align="center" width="70"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td align="center" width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                            <td width="200" align="right"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                            <td width="50" align="right"><p><? echo $row[csf('gsm')]; ?></p></td>
                            <td width="50 align="right"><p><? echo $row[csf('dia_width')]; ?></p></td>
                        </tr>
						<?

						$tot_qty+=$row[csf('quantity')];
						$rcv_return_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="6" align="right">Total</td>
                        <td align="right"><? echo number_format($rcv_return_qty,2); ?></td>
                        <td>&nbsp; </td>
                        <td>&nbsp; </td>
                        <td>&nbsp; </td>
                    </tr>
                </tfoot>
            </table>
			<table border="1" class="rpt_table" rules="all" width="1045" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="10">Transfer Out Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="110">System ID</th>
                        <th width="120">Issue to Company</th>
                        <th width="100">Challan No</th>
                        <th width="70">Receive Date</th>
                        <th width="80">Color</th>
                        <th width="80">Fin. Transfer. Qty.</th>
                        <th width="200">Fabric Des.</th>
                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                    </tr>
				</thead>
                <tbody>
                <?
					$i=1;
					if($store_id) {$store_cond = " and b.from_store='$store_id'";}
					if($from_date) {$transfer_date_cond = " and a.transfer_date <='$from_date'";}
					$mrr_sql_trnsf=" select a.company_id, a.transfer_system_id, a.transfer_date, a.challan_no, c.color_id, sum(c.quantity) as quantity, d.product_name_details, d.gsm, d.dia_width from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.trans_id=c.trans_id and c.prod_id=d.id and c.entry_form = 258 and a.company_id=$companyID and c.trans_type=6 and c.po_breakdown_id in ($po_id) and c.color_id='$color' $store_cond $transfer_date_cond and c.status_active=1 and b.status_active =1 and a.status_active=1 group by  a.company_id, a.transfer_system_id, a.transfer_date, a.challan_no, c.color_id, d.product_name_details, d.gsm, d.dia_width ";

					$dtlsArray=sql_select($mrr_sql_trnsf);
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                            <td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            <td width="200" ><p><? echo $row[csf('product_name_details')]; ?></p></td>
                            <td width="50" align="center"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
                            <td width="50" align="center"><p><? echo $row[csf('dia_width')]; ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$transfer_qnty+=$row[csf('quantity')];
						$i++;

					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="6" align="right">Total</td>
                        <td align="right"><? echo number_format($transfer_qnty,2); ?> </td>
                        <td align="right">&nbsp;</td>
                        <td align="right">&nbsp;</td>
                        <td align="right">&nbsp;</td>
                    </tr>
                    <tr class="tbl_bottom">
                    	<td colspan="6" align="right">Grand Total</td>
                         <td align="right"><? echo number_format($tot_qty,2); ?> </td>
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


if($action=="knit_stock_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id."**".$color;die;
	?>
	<fieldset style="width:970px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th width="50">Sl</th>
						<th width="80">Product ID</th>
						<th width="200">Batch No</th>
						<th width="100">Floor</th>
						<th width="100">Room</th>
						<th width="100">Rack</th>
						<th width="100">Shelf</th>
						<th width="100">Bin</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?

if($db_type==0)
{
	$mrr_sql="select a.id as batch_id, a.batch_no, b.prod_id,
	sum((case when c.trans_type in(1,4,5) and c.entry_form in (7,37,66,68,14,15,52) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) and c.entry_form in (18,71,14,15,46) then c.quantity else 0 end)) as balance
	from  pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c
	where a.id=b.pi_wo_batch_no and b.id=c.trans_id  and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and c.color_id='$color' and c.entry_form in (7,37,66,68,14,15,18,71,46,52)
	group by a.id, a.batch_no, b.prod_id";
}
else
{
	$mrr_sql="select a.id as batch_id, a.batch_no, b.prod_id,
	sum((case when c.trans_type in(1,4,5) and c.entry_form in (7,37,66,68,14,15,52) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) and c.entry_form in (18,71,14,15,46) then c.quantity else 0 end)) as balance
	from  pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c
	where a.id=b.pi_wo_batch_no and b.id=c.trans_id  and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and c.color_id='$color' and c.entry_form in (7,37,66,68,14,15,18,71,46,52)
	group by a.id, a.batch_no, b.prod_id
	having sum((case when c.trans_type in(1,4,5) and c.entry_form in (7,37,66,68,14,15,52) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) and c.entry_form in (18,71,14,15,46) then c.quantity else 0 end))>0";


	$mrr_sql_room_rack_info=sql_select("select a.id as batch_id, a.batch_no, b.prod_id,b.floor_id,b.room, b.rack,b.self,b.bin_box
	from  pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c
	where a.id=b.pi_wo_batch_no and b.id=c.trans_id  and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and c.color_id='$color' and c.entry_form in (7,37,66,68,14,15,18,71,46,52)
	group by a.id, a.batch_no, b.prod_id,b.floor_id,b.room, b.rack,b.self,b.bin_box
	having sum((case when c.trans_type in(1,4,5) and c.entry_form in (7,37,66,68,14,15,52) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) and c.entry_form in (18,71,14,15,46) then c.quantity else 0 end))>0");

	foreach($mrr_sql_room_rack_info as $row)
	{
		$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['floor_id']=$row[csf('floor_id')];
		$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['room']=$row[csf('room')];
		$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['rack']=$row[csf('rack')];
		$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['self']=$row[csf('self')];
		$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['bin_box']=$row[csf('bin_box')];
	}


}
//echo $mrr_sql;//die;
$room__rack_shelf_arr=return_library_array( "select floor_room_rack_id, floor_room_rack_name from  lib_floor_room_rack_mst where status_active=1 and company_id='$companyID'", "floor_room_rack_id", "floor_room_rack_name");
$dtlsArray=sql_select($mrr_sql);
$i=1;
foreach($dtlsArray as $row)
{
	if ($i%2==0)
		$bgcolor="#E9F3FF";
	else
		$bgcolor="#FFFFFF";


	$floor=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['floor_id'];
	$room=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['room'];
	$rack=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['rack'];
	$self=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['self'];
	$bin=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['bin_box'];


	?>
	<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
		<td align="center"><p><? echo $i; ?></p></td>
		<td align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
		<td><p><? echo $row[csf('batch_no')]; ?></p></td>
		<td align="center"><p><? echo $room__rack_shelf_arr[$floor]; ?></p></td>
		<td align="center"><p><? echo $room__rack_shelf_arr[$room]; ?></p></td>
		<td align="center"><p><? echo $room__rack_shelf_arr[$rack]; ?></p></td>
		<td align="center"><p><? echo $room__rack_shelf_arr[$self]; ?></p></td>
		<td align="center"><p><? echo $room__rack_shelf_arr[$bin]; ?></p></td>
		<td align="right"><p><? echo number_format($row[csf('balance')],2); ?></p></td>
	</tr>
	<?
	$tot_qty+=$row[csf('balance')];
	$i++;
}
?>
</tbody>
<tfoot>
	<tr class="tbl_bottom">
		<td colspan="8" align="right">Total</td>
		<td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
	</tr>
</tfoot>
</table>
</div>
</fieldset>
<?
exit();
}

if($action=="actual_cut_popup")
{
	echo load_html_head_contents("Actual Cut Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th width="30">Sl</th>
					<!--<th width="100">Issue ID</th>-->
					<th width="75">Production Date</th>
					<th width="200">Item Name</th>
					<th width="80">Qty</th>
				</thead>
				<tbody>
					<?
					if($db_type==0) $select_grpby_actual="group by a.id";
					if($db_type==2) $select_grpby_actual=" group by a.id,a.production_date, a.item_number_id,b.color_size_break_down_id, c.color_mst_id";
					else $select_grpby_actual="";
					//$color_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					$mrr_sql="select a.id, a.production_date, a.item_number_id, sum(b.production_qnty) as production_qnty, b.color_size_break_down_id, c.color_mst_id
					from  pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.color_number_id='$color' and a.production_type=1 and a.is_deleted=0 and a.status_active=1 and a.po_break_down_id in ($po_id) $select_grpby_actual";
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
<!--                            <td width="100"><p><? //echo $row[csf('issue_number')]; ?></p></td>
-->                            <td width="75"><p><? echo change_date_format($row[csf('production_date')]); ?></p></td>
<td width="200" ><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
<td width="80" align="right"><p><? echo number_format($row[csf('production_qnty')],0); ?></p></td>
</tr>
<?
$tot_qty+=$row[csf('production_qnty')];
$i++;
}
?>
</tbody>
<tfoot>
	<tr class="tbl_bottom">
		<td colspan="3" align="right">Total</td>
		<td align="right"><? echo number_format($tot_qty,0); ?>&nbsp;</td>
	</tr>
</tfoot>
</table>
</div>
</fieldset>
<?
exit();
} //actual end

if($action=="woven_actual_cut_popup")
{
	echo load_html_head_contents("Actual Cut Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th width="30">Sl</th>
					<!--<th width="100">Issue ID</th>-->
					<th width="75">Production Date</th>
					<th width="200">Item Name</th>
					<th width="80">Qty</th>
				</thead>
				<tbody>
					<?
					if($db_type==0) $select_grpby_actual="group by a.id";
					if($db_type==2) $select_grpby_actual=" group by a.id,a.production_date, a.item_number_id,b.color_size_break_down_id, c.color_mst_id";
					else $select_grpby_actual="";
					//$color_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					$mrr_sql="select a.id, a.production_date, a.item_number_id, sum(b.production_qnty) as production_qnty, b.color_size_break_down_id, c.color_mst_id
					from  pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.color_number_id='$color' and a.production_type=1 and a.is_deleted=0 and a.status_active=1 and a.po_break_down_id in ($po_id) $select_grpby_actual";
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
<!--                            <td width="100"><p><? //echo $row[csf('issue_number')]; ?></p></td>
-->                            <td width="75"><p><? echo change_date_format($row[csf('production_date')]); ?></p></td>
<td width="200" ><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
<td width="80" align="right"><p><? echo number_format($row[csf('production_qnty')],0); ?></p></td>
</tr>
<?
$tot_qty+=$row[csf('production_qnty')];
$i++;
}
?>
</tbody>
<tfoot>
	<tr class="tbl_bottom">
		<td colspan="3" align="right">Total</td>
		<td align="right"><? echo number_format($tot_qty,0); ?>&nbsp;</td>
	</tr>
</tfoot>
</table>
</div>
</fieldset>
<?
exit();
}

if($action=="stock_popup______backup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id."**".$color;die;
	$color_ex = explode("__", $color);
	$color = $color_ex[0];
	$detarmination_id = $color_ex[1];
	if($detarmination_id=="")
	{
		?>
		<fieldset style="width:1070px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="1050" cellpadding="0" cellspacing="0" align="center">
					<thead>
						<tr>
							<th width="50">Sl</th>
							<th width="80">Color</th>
							<th width="100">Batch No</th>
							<th width="100">Floor</th>
							<th width="100">Room</th>
							<th width="100">Rack</th>
							<th width="100">Shelf</th>
							<th width="100">Bin</th>

							<th width="200">Fabric Des.</th>
							<th>Qty</th>
						</tr>
					</thead>
					<tbody>
						<?
						if($store_id){
							$store_cond = " and b.store_id = $store_id";
						}else{
							$store_cond = "";
						}
						$mrr_sql = "SELECT  a.id as batch_id,a.batch_no, d.product_name_details,e.color_name, sum((case when c.trans_type in(1,4,5) and c.entry_form in (7,37,66,68,14,15,52) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) and c.entry_form in (18,71,14,15,46) then c.quantity else 0 end)) as balance,b.prod_id FROM pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c ,product_details_master d, lib_color e WHERE a.id=b.pi_wo_batch_no and b.id=c.trans_id and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and c.color_id='$color' and c.entry_form in (7,37,66,68,14,15,18,71,46,52) $store_cond and b.prod_id = d.id and d.color = e.id GROUP BY a.id,a.batch_no, d.product_name_details,e.color_name,b.prod_id";

						$dtlsArray=sql_select($mrr_sql);

						$mrr_sql_room_rack_info=sql_select("select a.id as batch_id, a.batch_no, b.prod_id,b.floor_id,b.room, b.rack,b.self,b.bin_box
							from  pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c
							where a.id=b.pi_wo_batch_no and b.id=c.trans_id  and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and c.color_id='$color' and c.entry_form in (7,37,66,68,14,15,18,71,46,52)
							group by a.id, a.batch_no, b.prod_id,b.floor_id,b.room, b.rack,b.self,b.bin_box
							having sum((case when c.trans_type in(1,4,5) and c.entry_form in (7,37,66,68,14,15,52) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) and c.entry_form in (18,71,14,15,46) then c.quantity else 0 end))>0");

							foreach($mrr_sql_room_rack_info as $row)
							{
								$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['floor_id']=$row[csf('floor_id')];
								$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['room']=$row[csf('room')];
								$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['rack']=$row[csf('rack')];
								$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['self']=$row[csf('self')];
								$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['bin_box']=$row[csf('bin_box')];
							}

							$room__rack_shelf_arr=return_library_array( "select floor_room_rack_id, floor_room_rack_name from  lib_floor_room_rack_mst where status_active=1 and company_id='$companyID'", "floor_room_rack_id", "floor_room_rack_name");



						$i=1;
						foreach($dtlsArray as $row)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

							$floor=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['floor_id'];
							$room=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['room'];
							$rack=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['rack'];
							$self=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['self'];
							$bin=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['bin_box'];

							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center"><p><? echo $i; ?></p></td>
								<td align="center"><p><? echo $row[csf('color_name')]; ?></p></td>
								<td><p><? echo $row[csf('batch_no')]; ?></p></td>

								<td align="center"><p><? echo $room__rack_shelf_arr[$floor]; ?></p></td>
								<td align="center"><p><? echo $room__rack_shelf_arr[$room]; ?></p></td>
								<td align="center"><p><? echo $room__rack_shelf_arr[$rack]; ?></p></td>
								<td align="center"><p><? echo $room__rack_shelf_arr[$self]; ?></p></td>
								<td align="center"><p><? echo $room__rack_shelf_arr[$bin]; ?></p></td>

								<td align="center"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td align="right"><p><? echo number_format($row[csf('balance')],2); ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('balance')];
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="9" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}
	else
	{
		?>
		<fieldset style="width:1070px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="1050" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th width="50">Sl</th>
						<th width="80">Color</th>
						<th width="100">Batch No</th>
						<th width="100">Floor</th>
						<th width="100">Room</th>
						<th width="100">Rack</th>
						<th width="100">Shelf</th>
						<th width="100">Bin</th>
						<th width="200">Fabric Des.</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?
					if($store_id)
					{
						$store_cond = " and b.store_id = $store_id";
					}else{
						$store_cond = "";
					}

					$mrr_sql = "SELECT  a.id as batch_id, a.batch_no, d.product_name_details,e.color_name, sum((case when c.trans_type in(1,4,5) and c.entry_form in (7,37,66,68,14,15,52) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) and c.entry_form in (18,71,14,15,46) then c.quantity else 0 end)) as balance,b.prod_id FROM pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c ,product_details_master d, lib_color e WHERE a.id=b.pi_wo_batch_no and b.id=c.trans_id and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and c.color_id='$color' and c.entry_form in (7,37,66,68,14,15,18,71,46,52) $store_cond and b.prod_id = d.id and d.color = e.id and d.detarmination_id=$detarmination_id GROUP BY a.id,a.batch_no, d.product_name_details,e.color_name,b.prod_id";

					$dtlsArray=sql_select($mrr_sql);

					$mrr_sql_room_rack_info=sql_select("select a.id as batch_id, a.batch_no, b.prod_id,b.floor_id,b.room, b.rack,b.self,b.bin_box
					from  pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c
					where a.id=b.pi_wo_batch_no and b.id=c.trans_id  and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and c.color_id='$color' and c.entry_form in (7,37,66,68,14,15,18,71,46,52)
					group by a.id, a.batch_no, b.prod_id,b.floor_id,b.room, b.rack,b.self,b.bin_box
					having sum((case when c.trans_type in(1,4,5) and c.entry_form in (7,37,66,68,14,15,52) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) and c.entry_form in (18,71,14,15,46) then c.quantity else 0 end))>0");

					foreach($mrr_sql_room_rack_info as $row)
					{
						$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['floor_id']=$row[csf('floor_id')];
						$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['room']=$row[csf('room')];
						$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['rack']=$row[csf('rack')];
						$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['self']=$row[csf('self')];
						$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['bin_box']=$row[csf('bin_box')];
					}

					$room__rack_shelf_arr=return_library_array( "select floor_room_rack_id, floor_room_rack_name from  lib_floor_room_rack_mst where status_active=1 and company_id='$companyID'", "floor_room_rack_id", "floor_room_rack_name");



					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$floor=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['floor_id'];
						$room=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['room'];
						$rack=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['rack'];
						$self=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['self'];
						$bin=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['bin_box'];

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td align="center"><p><? echo $i; ?></p></td>
							<td align="center"><p><? echo $row[csf('color_name')]; ?></p></td>
							<td><p><? echo $row[csf('batch_no')]; ?></p></td>
							<td align="center"><p><? echo $room__rack_shelf_arr[$floor]; ?></p></td>
							<td align="center"><p><? echo $room__rack_shelf_arr[$room]; ?></p></td>
							<td align="center"><p><? echo $room__rack_shelf_arr[$rack]; ?></p></td>
							<td align="center"><p><? echo $room__rack_shelf_arr[$self]; ?></p></td>
							<td align="center"><p><? echo $room__rack_shelf_arr[$bin]; ?></p></td>
							<td align="center"><p><? echo $row[csf('product_name_details')]; ?></p></td>
							<td align="right"><p><? echo number_format($row[csf('balance')],2); ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('balance')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
		<?
	}
	exit();
}

if($action=="stock_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id."**".$color;die;
	$color_ex = explode("__", $color);
	$color = $color_ex[0];
	$detarmination_id = $color_ex[1];
	if($detarmination_id=="")
	{
		?>
		<fieldset style="width:1070px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="1050" cellpadding="0" cellspacing="0" align="center">
					<thead>
						<tr>
							<th width="50">Sl</th>
							<th width="80">Color</th>
							<th width="100">Batch No</th>
							<th width="100">Floor</th>
							<th width="100">Room</th>
							<th width="100">Rack</th>
							<th width="100">Shelf</th>
							<th width="100">Bin</th>

							<th width="200">Fabric Des.</th>
							<th>Qty</th>
						</tr>
					</thead>
					<tbody>
						<?
						if($store_id){
							$store_cond = " and b.store_id = $store_id";
						}else{
							$store_cond = "";
						}
						/*$mrr_sql = "SELECT  a.id as batch_id,a.batch_no, d.product_name_details,e.color_name, sum((case when c.trans_type in(1,4,5) and c.entry_form in (7,37,66,68,14,15,52) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) and c.entry_form in (18,71,14,15,46) then c.quantity else 0 end)) as balance,b.prod_id
						FROM pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c ,product_details_master d, lib_color e
						WHERE a.id=b.pi_wo_batch_no and b.id=c.trans_id and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and c.color_id='$color' and c.entry_form in (7,37,66,68,14,15,18,71,46,52) $store_cond and b.prod_id = d.id and d.color = e.id
						GROUP BY a.id,a.batch_no, d.product_name_details,e.color_name,b.prod_id";*/

						$mrr_sql = "SELECT a.id as batch_id,a.batch_no, d.product_name_details,d.detarmination_id,d.gsm,d.dia_width,e.color_name,
						sum(case when c.trans_type in(1,4,5) and c.entry_form in (7,37,66,14,15,52) then c.quantity else 0 end) as recv, sum(case when c.trans_type in(2,3,6) and c.entry_form in (18,71,14,15,46,134) then c.quantity else 0 end) as iss, b.prod_id
						FROM pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c ,product_details_master d, lib_color e
						WHERE a.id=b.pi_wo_batch_no and b.id=c.trans_id and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and d.color ='$color' and c.entry_form in (7,37,66,14,15,18,71,46,52,134) $store_cond and b.prod_id = d.id and d.color = e.id
						GROUP BY a.id,a.batch_no, d.product_name_details,d.detarmination_id,d.gsm,d.dia_width,e.color_name,b.prod_id
						union all
						SELECT a.id as batch_id,a.batch_no, d.product_name_details,d.detarmination_id,d.gsm,d.dia_width,e.color_name, sum(case when c.trans_type in(1) and c.entry_form in (68) then c.quantity else 0 end) as recv, 0 as iss, b.prod_id
						FROM pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c ,product_details_master d, lib_color e
						WHERE a.id=b.batch_id and b.id=c.trans_id and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and d.color ='$color' and c.entry_form in (68) $store_cond and b.prod_id = d.id and d.color = e.id
						GROUP BY a.id,a.batch_no, d.product_name_details,d.detarmination_id,d.gsm,d.dia_width,e.color_name,b.prod_id";
						//echo $mrr_sql;//die; //  and c.color_id='$color'

						$dtlsArray=sql_select($mrr_sql);
						foreach ($dtlsArray as $key => $row)
						{
							$stock_data_array[$row[csf('batch_id')]][$row[csf('prod_id')]]['batch_no']=$row[csf('batch_no')];
							$stock_data_array[$row[csf('batch_id')]][$row[csf('prod_id')]]['product_name_details']=$row[csf('product_name_details')];
							$stock_data_array[$row[csf('batch_id')]][$row[csf('prod_id')]]['detarmination_id']=$row[csf('detarmination_id')];
							$stock_data_array[$row[csf('batch_id')]][$row[csf('prod_id')]]['gsm']=$row[csf('gsm')];
							$stock_data_array[$row[csf('batch_id')]][$row[csf('prod_id')]]['dia_width']=$row[csf('dia_width')];
							$stock_data_array[$row[csf('batch_id')]][$row[csf('prod_id')]]['color_name']=$row[csf('color_name')];
							$stock_data_array[$row[csf('batch_id')]][$row[csf('prod_id')]]['recv']+=$row[csf('recv')];
							$stock_data_array[$row[csf('batch_id')]][$row[csf('prod_id')]]['iss']+=$row[csf('iss')];
						}
						// echo "<pre>";print_r($stock_data_array);die;

						$mrr_sql_room_rack_info=sql_select("SELECT a.id as batch_id, a.batch_no, b.prod_id,b.floor_id,b.room, b.rack,b.self,b.bin_box
						from  pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c
						where a.id=b.pi_wo_batch_no and b.id=c.trans_id  and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and c.color_id='$color' and c.entry_form in (7,37,66,14,15,18,71,46,52)
						group by a.id, a.batch_no, b.prod_id,b.floor_id,b.room, b.rack,b.self,b.bin_box
						having sum((case when c.trans_type in(1,4,5) and c.entry_form in (7,37,66,14,15,52) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) and c.entry_form in (18,71,14,15,46) then c.quantity else 0 end))>0
						union all
						SELECT a.id as batch_id, a.batch_no, b.prod_id,b.floor_id,b.room, b.rack,b.self,b.bin_box
						from  pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c
						where a.id=b.batch_id and b.id=c.trans_id  and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and c.color_id='$color' and c.entry_form in (68)
						group by a.id, a.batch_no, b.prod_id,b.floor_id,b.room, b.rack,b.self,b.bin_box
						having sum((case when c.trans_type in(1,4,5) and c.entry_form in (68) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) and c.entry_form in (18,71,14,15,46) then c.quantity else 0 end))>0");
						foreach($mrr_sql_room_rack_info as $row)
						{
							$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['floor_id']=$row[csf('floor_id')];
							$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['room']=$row[csf('room')];
							$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['rack']=$row[csf('rack')];
							$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['self']=$row[csf('self')];
							$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['bin_box']=$row[csf('bin_box')];
						}

						$room__rack_shelf_arr=return_library_array( "select floor_room_rack_id, floor_room_rack_name from  lib_floor_room_rack_mst where status_active=1 and company_id='$companyID'", "floor_room_rack_id", "floor_room_rack_name");

						$composition_arr=array();
						$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
						$sql_deter_res=sql_select($sql_deter);
						if(count($sql_deter_res)>0)
						{
							foreach( $sql_deter_res as $row )
							{
								if(array_key_exists($row[csf('id')],$composition_arr))
								{
									$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
								}
								else
								{
									$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
								}
							}
						}
						unset($sql_deter_res);

						$i=1;
						foreach ($stock_data_array as $batch_id => $batch_id_val)
						{
							foreach ($batch_id_val as $prod_id => $row)
							{
								if ($i%2==0)
								$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";

								$floor=$romRackInfoArr[$batch_id][$row['batch_no']][$prod_id]['floor_id'];
								$room=$romRackInfoArr[$batch_id][$row['batch_no']][$prod_id]['room'];
								$rack=$romRackInfoArr[$batch_id][$row['batch_no']][$prod_id]['rack'];
								$self=$romRackInfoArr[$batch_id][$row['batch_no']][$prod_id]['self'];
								$bin=$romRackInfoArr[$batch_id][$row['batch_no']][$prod_id]['bin_box'];
								$balance=$row['recv']-$row['iss'];
								if ($balance>0)
								{
									?>
									<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td align="center"><p><? echo $i; ?></p></td>
										<td align="center"><p><? echo $row['color_name']; ?></p></td>
										<td><p><? echo $row['batch_no']; ?></p></td>

										<td align="center"><p><? echo $room__rack_shelf_arr[$floor]; ?></p></td>
										<td align="center"><p><? echo $room__rack_shelf_arr[$room]; ?></p></td>
										<td align="center"><p><? echo $room__rack_shelf_arr[$rack]; ?></p></td>
										<td align="center"><p><? echo $room__rack_shelf_arr[$self]; ?></p></td>
										<td align="center"><p><? echo $room__rack_shelf_arr[$bin]; ?></p></td>

										<td align="center"><p><? echo $composition_arr[$row['detarmination_id']].', '.$row['gsm'].', '.$row['dia_width']; //$row['product_name_details'];?></p></td>
										<td align="right"><p><? echo number_format($balance,2); ?></p></td>
									</tr>
									<?
									$tot_qty+=$balance;
								}
								$i++;
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="9" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}
	else
	{
		?>
		<fieldset style="width:1070px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="1050" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th width="50">Sl</th>
						<th width="80">Color</th>
						<th width="100">Batch No</th>
						<th width="100">Floor</th>
						<th width="100">Room</th>
						<th width="100">Rack</th>
						<th width="100">Shelf</th>
						<th width="100">Bin</th>
						<th width="200">Fabric Des.</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?
					if($store_id)
					{
						$store_cond = " and b.store_id = $store_id";
					}else{
						$store_cond = "";
					}

					$mrr_sql = "SELECT  a.id as batch_id, a.batch_no, d.product_name_details,e.color_name, sum((case when c.trans_type in(1,4,5) and c.entry_form in (7,37,66,68,14,15,52) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) and c.entry_form in (18,71,14,15,46) then c.quantity else 0 end)) as balance,b.prod_id FROM pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c ,product_details_master d, lib_color e WHERE a.id=b.pi_wo_batch_no and b.id=c.trans_id and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and c.color_id='$color' and c.entry_form in (7,37,66,68,14,15,18,71,46,52) $store_cond and b.prod_id = d.id and d.color = e.id and d.detarmination_id=$detarmination_id GROUP BY a.id,a.batch_no, d.product_name_details,e.color_name,b.prod_id";

					$dtlsArray=sql_select($mrr_sql);

					$mrr_sql_room_rack_info=sql_select("select a.id as batch_id, a.batch_no, b.prod_id,b.floor_id,b.room, b.rack,b.self,b.bin_box
					from  pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c
					where a.id=b.pi_wo_batch_no and b.id=c.trans_id  and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and c.color_id='$color' and c.entry_form in (7,37,66,68,14,15,18,71,46,52)
					group by a.id, a.batch_no, b.prod_id,b.floor_id,b.room, b.rack,b.self,b.bin_box
					having sum((case when c.trans_type in(1,4,5) and c.entry_form in (7,37,66,68,14,15,52) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) and c.entry_form in (18,71,14,15,46) then c.quantity else 0 end))>0");

					foreach($mrr_sql_room_rack_info as $row)
					{
						$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['floor_id']=$row[csf('floor_id')];
						$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['room']=$row[csf('room')];
						$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['rack']=$row[csf('rack')];
						$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['self']=$row[csf('self')];
						$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['bin_box']=$row[csf('bin_box')];
					}

					$room__rack_shelf_arr=return_library_array( "select floor_room_rack_id, floor_room_rack_name from  lib_floor_room_rack_mst where status_active=1 and company_id='$companyID'", "floor_room_rack_id", "floor_room_rack_name");



					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$floor=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['floor_id'];
						$room=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['room'];
						$rack=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['rack'];
						$self=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['self'];
						$bin=$romRackInfoArr[$row[csf('batch_id')]][$row[csf('batch_no')]][$row[csf('prod_id')]]['bin_box'];

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td align="center"><p><? echo $i; ?></p></td>
							<td align="center"><p><? echo $row[csf('color_name')]; ?></p></td>
							<td><p><? echo $row[csf('batch_no')]; ?></p></td>
							<td align="center"><p><? echo $room__rack_shelf_arr[$floor]; ?></p></td>
							<td align="center"><p><? echo $room__rack_shelf_arr[$room]; ?></p></td>
							<td align="center"><p><? echo $room__rack_shelf_arr[$rack]; ?></p></td>
							<td align="center"><p><? echo $room__rack_shelf_arr[$self]; ?></p></td>
							<td align="center"><p><? echo $room__rack_shelf_arr[$bin]; ?></p></td>
							<td align="center"><p><? echo $row[csf('product_name_details')]; ?></p></td>
							<td align="right"><p><? echo number_format($row[csf('balance')],2); ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('balance')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
		</fieldset>
		<?
	}
	exit();
}

?>