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
	$party="1,3,21,90";
	echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}
if($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "load_drop_down( 'requires/style_wise_daily_finish_fabric_follow_up_report_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_store', 'store_td' );","" );
	exit();
}
if($action=="load_drop_down_store")
{
	$data_arr=explode("_",$data);
	if($data_arr[1]!=""){$location_cond="and comp.company_id=$data_arr[0] and comp.location_id=$data_arr[1]";}else{$location_cond="and comp.company_id=$data_arr[0]";}
		$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
		$store_location_id = $userCredential[0][csf('store_location_id')];
		if ($store_location_id != '') {$store_location_credential_cond = "and comp.id in($store_location_id)";} else { $store_location_credential_cond = "";}
	$sql = "select comp.id, comp.store_name from lib_store_location comp where comp.status_active=1 and comp.is_deleted=0  $location_cond  and comp.item_category_id='2' $store_location_credential_cond  group by comp.id, comp.store_name order by comp.store_name";
	echo create_drop_down( "cbo_store_id", 90, $sql,"id,store_name", 1, "--Select Store--", $selected, "","" );
	exit();
}
if($action=="order_no_popup")
{
	echo load_html_head_contents("Order Info", "../../../../", 1, 1,'','','');
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
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( ddd );
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
		
		function fn_generate_list(){
			if((form_validation('txt_search_job','Job')==false) && (form_validation('txt_search_style','Style')==false))
			{
				return;
			}
			else
			{
				show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_search_job').value+'**'+document.getElementById('txt_search_style').value+'**'+'<? echo $cbo_year_id; ?>', 'create_order_no_search_list_view', 'search_div', 'style_wise_daily_finish_fabric_follow_up_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
			}
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
							<th>Search Job</th>
							<th>Search Style</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                            <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
							<input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
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
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="fn_generate_list()" style="width:70px;" />
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

if($action=="create_order_no_search_list_view")
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

	if($data[2]!='') $job_cond=" and a.job_no_prefix_num=".trim($data[2]).""; else $job_cond="";
	if($data[3]!='') $style_cond=" and a.style_ref_no like '".trim($data[3])."'"; else $style_cond="";

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="a.style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="year(a.insert_date)";
	else if($db_type==2) $year_field_by="to_char(a.insert_date,'YYYY')";
	else $year_field_by="";

	if($year_id!=0) $year_cond=" and $year_field_by='$year_id'"; else $year_cond="";

	$arr=array (0=>$buyer_arr);
	$sql= "select b.id, a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, $year_field_by as year,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $buyer_id_cond $job_cond $style_cond $year_cond order by a.id DESC";

	echo create_list_view("tbl_list_search", "Buyer Name,Job No,Po Number,Style Ref. No", "100,100,200,60","610","270",0, $sql , "js_set_value", "id,po_number", "", 1, "buyer_name,0,0,0", $arr , "buyer_name,job_no,po_number,style_ref_no", "",'','0,0,0,0','',1) ;
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_search_job').value+'**'+document.getElementById('txt_search_style').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'style_wise_daily_finish_fabric_follow_up_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:70px;" />
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
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_booking_no').value,'create_booking_search_list_view', 'search_div', 'style_wise_daily_finish_fabric_follow_up_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
$consumtion_library=return_library_array( "select job_no, avg_finish_cons from wo_pre_cost_fabric_cost_dtls", "job_no", "avg_finish_cons");

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$type=str_replace("'","",$type);
	$cbo_company_id		=str_replace("'","",$cbo_company_id);
	$cbo_location_id	=str_replace("'","",$cbo_location_id);
	$cbo_buyer_id		=str_replace("'","",$cbo_buyer_id);
	$cbo_year 			=str_replace("'","",$cbo_year);
	$txt_job_no 		=str_replace("'","",$txt_job_no);
	$txt_job_id 		=str_replace("'","",$txt_job_id);
	$txt_style 			=str_replace("'","",$txt_style);
	$txt_order_no_show 	=str_replace("'","",$txt_order_no_show);
	$txt_order_no 		=str_replace("'","",$txt_order_no);
	$txt_book_no 		=str_replace("'","",$txt_book_no);
	$txt_book_id 		=str_replace("'","",$txt_book_id);
	$txt_date_from 		=str_replace("'","",$txt_date_from);
	$cbo_store_id		=str_replace("'","",$cbo_store_id);

	//echo $cbo_report_type;die;
	if($cbo_location_id!="" && $cbo_location_id!=0) $location_id_cond=" and a.location_id=$cbo_location_id";
	if($cbo_buyer_id!="" && $cbo_buyer_id!=0) $buyer_id_cond=" and a.buyer_id=$cbo_buyer_id";

	if($db_type==0)
	{
		if($cbo_year!=0) $year_search_cond=" and year(a.insert_date)=$cbo_year"; else $year_search_cond="";
	}
	else if($db_type==2)
	{
		if($cbo_year!=0) $year_search_cond=" and TO_CHAR(a.insert_date,'YYYY')=$cbo_year"; else $year_search_cond="";
	}

	if($txt_job_no!='') $job_no_cond=" and d.job_no_prefix_num in($txt_job_no)";
	//if($txt_job_id!=0) $job_no_cond=" and b.job_no=$txt_job_id";

	if($txt_style!='') $style_ref_cond=" and d.style_ref_no='$txt_style'";
	
	if($txt_order_no_show!='') $order_no_cond=" and c.po_number in('$txt_order_no_show')";
	if($txt_order_no!='') $order_id_cond=" and c.id in($txt_order_no)";

	if($txt_book_no!='') $booking_no_cond=" and a.booking_no_prefix_num
	 in($txt_book_no)";
	if($txt_book_id!='') $booking_id_cond=" and a.id in($txt_book_id)";

	if($cbo_store_id!=0) $store_id_cond=" and d.store_id=$cbo_store_id";
	if($cbo_store_id!=0) $store_id_cond_trans_in=" and b.to_store=$cbo_store_id";
	if($txt_date_from=="") $receive_date=""; else $receive_date= " and c.transaction_date <=".$txt_date_from."";
	if($txt_date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";

	ob_start();
	if($type==1) // show button start
	{
		
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
		$order_arr=return_library_array("select id, po_number from wo_po_break_down","id","po_number");

		?>
		<style>
			.line_br{
			 word-break: break-all; 
			 word-wrap: break-word;
			}
		</style>
		<fieldset style="width:2690px;">
			<table cellpadding="0" cellspacing="0" width="1310">
				<tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="21" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="21" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="2690" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th width="30">SL</th>	
					<th width="120">Buyer</th>	
					<th width="120">Season</th>	
					<th width="120">Job No</th>	
					<th width="120">Order No	</th>
					<th width="120">Style</th>	
					<th width="120">Booking No</th>	
					<th width="120">Gmts. Color</th>	
					<th width="120">Body Part</th>	
					<th width="120">Color Type</th>	
					<th width="120">Fab. Color</th>	
					<th width="120">Fabric Type</th>	
					<th width="120">Req Qty</th>	
					<th width="120">Prev. Received</th>	
					<th width="120">Today Received</th>	
					<th width="120">Total Received</th>	
					<th width="120" title="Req Qty - Total Received">Balance</th>
					<th width="120">Prev. Issue</th>	
					<th width="120">Today Issued</th>	
					<th width="120">Total Issued	</th>
					<th width="120">Stock</th>
					<th colspan="2" >Batch No</th>
				</thead>
			</table>
			<div style="width:2710px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="2690" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
					<?
					$po_break_id_con='';
					if($txt_search_booking!='')
					{
						$sql_booking_query="select id, po_break_down_id, fabric_color_id, booking_no from wo_booking_dtls where booking_no like '%$txt_search_booking' and status_active=1 and is_deleted=0 and booking_type=1";
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

					$sql_query="SELECT a.id as booking_id,a.booking_no,a.buyer_id,a.job_no,b.po_break_down_id,
					b.gmts_color_id,b.color_type,b.fabric_color_id,sum(b.fin_fab_qnty)  as fin_fab_qnty,b.construction,c.po_number, 
					d.season,d.style_ref_no ,e.body_part_id
					from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e 
					where a.booking_no=b.booking_no 
					and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and b.pre_cost_fabric_cost_dtls_id = e.id
					and b.job_no=e.job_no 
					and a.company_id=$cbo_company_id $buyer_id_cond $year_search_cond $job_no_cond $style_ref_cond $order_no_cond $order_id_cond $booking_no_cond $booking_id_cond and b.booking_type = 1
					and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 
					group by a.id,a.booking_no,a.buyer_id,a.job_no,b.po_break_down_id,
					b.gmts_color_id,b.color_type,b.fabric_color_id,b.construction,c.po_number,
					d.season,d.style_ref_no,d.season,d.style_ref_no,e.body_part_id

					union all 

					select a.id as booking_id,a.booking_no,a.buyer_id,a.job_no,b.po_break_down_id,
					b.gmts_color_id,b.color_type,b.fabric_color_id,sum(b.fin_fab_qnty)  as fin_fab_qnty,b.construction,c.po_number, 
					d.season,d.style_ref_no ,e.body_part_id
					from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,wo_pre_cost_fab_conv_cost_dtls f 
					where a.booking_no=b.booking_no 
					and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and b.pre_cost_fabric_cost_dtls_id = e.id and  b.pre_cost_fabric_cost_dtls_id = f.id and f.fabric_description = e.id 
					and b.job_no=e.job_no 
					and a.company_id=$cbo_company_id $buyer_id_cond $year_search_cond $job_no_cond $style_ref_cond $order_no_cond $order_id_cond $booking_no_cond $booking_id_cond and b.booking_type = 4
					and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 
					group by a.id,a.booking_no,a.buyer_id,a.job_no,b.po_break_down_id,
					b.gmts_color_id,b.color_type,b.fabric_color_id,b.construction,c.po_number,
					d.season,d.style_ref_no,d.season,d.style_ref_no,e.body_part_id";
					// echo $sql_query;die;
					$nameArray=sql_select($sql_query); 
					//echo "49,53,51,36";
					$booking_ids="";
					foreach ($nameArray as $row)
					{
						$booking_ids.=$row[csf("booking_id")].",";

						$main_arr[$row[csf("booking_no")]][$row[csf("gmts_color_id")]][$row[csf("buyer_id")]][$row[csf("season")]][$row[csf("job_no")]][$row[csf("style_ref_no")]][$row[csf("body_part_id")]][$row[csf("color_type")]][$row[csf("fabric_color_id")]][$row[csf("construction")]]['booking_no']=$row[csf("booking_no")];
						$main_arr[$row[csf("booking_no")]][$row[csf("gmts_color_id")]][$row[csf("buyer_id")]][$row[csf("season")]][$row[csf("job_no")]][$row[csf("style_ref_no")]][$row[csf("body_part_id")]][$row[csf("color_type")]][$row[csf("fabric_color_id")]][$row[csf("construction")]]['gmts_color_id']=$row[csf("gmts_color_id")];
						$main_arr[$row[csf("booking_no")]][$row[csf("gmts_color_id")]][$row[csf("buyer_id")]][$row[csf("season")]][$row[csf("job_no")]][$row[csf("style_ref_no")]][$row[csf("body_part_id")]][$row[csf("color_type")]][$row[csf("fabric_color_id")]][$row[csf("construction")]]['buyer_id']=$row[csf("buyer_id")];
						$main_arr[$row[csf("booking_no")]][$row[csf("gmts_color_id")]][$row[csf("buyer_id")]][$row[csf("season")]][$row[csf("job_no")]][$row[csf("style_ref_no")]][$row[csf("body_part_id")]][$row[csf("color_type")]][$row[csf("fabric_color_id")]][$row[csf("construction")]]['season']=$row[csf("season")];
						$main_arr[$row[csf("booking_no")]][$row[csf("gmts_color_id")]][$row[csf("buyer_id")]][$row[csf("season")]][$row[csf("job_no")]][$row[csf("style_ref_no")]][$row[csf("body_part_id")]][$row[csf("color_type")]][$row[csf("fabric_color_id")]][$row[csf("construction")]]['job_no']=$row[csf("job_no")];
						$main_arr[$row[csf("booking_no")]][$row[csf("gmts_color_id")]][$row[csf("buyer_id")]][$row[csf("season")]][$row[csf("job_no")]][$row[csf("style_ref_no")]][$row[csf("body_part_id")]][$row[csf("color_type")]][$row[csf("fabric_color_id")]][$row[csf("construction")]]['po_break_down_id'].=$row[csf("po_break_down_id")].",";
						$main_arr[$row[csf("booking_no")]][$row[csf("gmts_color_id")]][$row[csf("buyer_id")]][$row[csf("season")]][$row[csf("job_no")]][$row[csf("style_ref_no")]][$row[csf("body_part_id")]][$row[csf("color_type")]][$row[csf("fabric_color_id")]][$row[csf("construction")]]['style_ref_no']=$row[csf("style_ref_no")];
						$main_arr[$row[csf("booking_no")]][$row[csf("gmts_color_id")]][$row[csf("buyer_id")]][$row[csf("season")]][$row[csf("job_no")]][$row[csf("style_ref_no")]][$row[csf("body_part_id")]][$row[csf("color_type")]][$row[csf("fabric_color_id")]][$row[csf("construction")]]['body_part_id']=$row[csf("body_part_id")];
						$main_arr[$row[csf("booking_no")]][$row[csf("gmts_color_id")]][$row[csf("buyer_id")]][$row[csf("season")]][$row[csf("job_no")]][$row[csf("style_ref_no")]][$row[csf("body_part_id")]][$row[csf("color_type")]][$row[csf("fabric_color_id")]][$row[csf("construction")]]['color_type']=$row[csf("color_type")];
						$main_arr[$row[csf("booking_no")]][$row[csf("gmts_color_id")]][$row[csf("buyer_id")]][$row[csf("season")]][$row[csf("job_no")]][$row[csf("style_ref_no")]][$row[csf("body_part_id")]][$row[csf("color_type")]][$row[csf("fabric_color_id")]][$row[csf("construction")]]['fabric_color_id']=$row[csf("fabric_color_id")];
						$main_arr[$row[csf("booking_no")]][$row[csf("gmts_color_id")]][$row[csf("buyer_id")]][$row[csf("season")]][$row[csf("job_no")]][$row[csf("style_ref_no")]][$row[csf("body_part_id")]][$row[csf("color_type")]][$row[csf("fabric_color_id")]][$row[csf("construction")]]['construction']=$row[csf("construction")];
						$main_arr[$row[csf("booking_no")]][$row[csf("gmts_color_id")]][$row[csf("buyer_id")]][$row[csf("season")]][$row[csf("job_no")]][$row[csf("style_ref_no")]][$row[csf("body_part_id")]][$row[csf("color_type")]][$row[csf("fabric_color_id")]][$row[csf("construction")]]['fin_fab_qnty']+=$row[csf("fin_fab_qnty")];
						$main_arr[$row[csf("booking_no")]][$row[csf("gmts_color_id")]][$row[csf("buyer_id")]][$row[csf("season")]][$row[csf("job_no")]][$row[csf("style_ref_no")]][$row[csf("body_part_id")]][$row[csf("color_type")]][$row[csf("fabric_color_id")]][$row[csf("construction")]]['booking_id']=$row[csf("booking_id")];
					}


					

					// echo "<pre>"; print_r($main_arr);die;
					$booking_ids=chop($booking_ids.",");
				    $booking_ids=implode(",",array_filter(array_unique(explode(",",$booking_ids))));

				    if($booking_ids!="")
				    {
				        $booking_ids=explode(",",$booking_ids);  
				        $knit_ids_chnk=array_chunk($booking_ids,999);
				        $booking_ids_cond=" and";
				        $booking_ids_cond_trns_in=" and";
				        foreach($knit_ids_chnk as $dtls_id)
				        {
				        	if($booking_ids_cond==" and")  $booking_ids_cond.="(c.booking_no_id in(".implode(',',$dtls_id).")"; else $booking_ids_cond.=" or c.booking_no_id in(".implode(',',$dtls_id).")";

				        	if($booking_ids_cond_trns_in==" and")  $booking_ids_cond_trns_in.="(b.to_ord_book_id in(".implode(',',$dtls_id).")"; else $booking_ids_cond_trns_in.=" or b.to_ord_book_id in(".implode(',',$dtls_id).")";
				        }
				        $booking_ids_cond.=")";
				        $booking_ids_cond_trns_in.=")";
				        //echo $booking_ids_cond;die;
				    }
				    if($db_type==0)
					{
						$batch_no_cond=",group_concat(distinct(c.batch_no)) as batch_no";
					}
					else
					{
						$batch_no_cond=",LISTAGG(c.batch_no, ',') WITHIN GROUP (ORDER BY c.batch_no) as batch_no";
					}

					$receQnty_sql=" SELECT a.id,b.booking_id,b.job_no,b.body_part_id,b.color_id,b.fabric_description_id,
					sum(b.receive_qnty) as receive_qnty
					,sum(case when a.entry_form in(7,37) and d.transaction_date<'$txt_date_from' then b.receive_qnty else 0 end) as receive_qnty_prev
					,sum(case when a.entry_form in(7,37) and d.transaction_date='$txt_date_from' then b.receive_qnty else 0 end) as receive_qnty_today $batch_no_cond 
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c ,inv_transaction d where a.id=b.mst_id and c.id=b.batch_id and b.trans_id=d.id and a.entry_form in(7,37) $booking_ids_cond $location_id_cond $store_id_cond and b.trans_id>0 group by a.id,b.booking_id,b.job_no,b.body_part_id,b.color_id,b.fabric_description_id";
					// echo $receQnty_sql;die;
					$receQnty_sql_arr=sql_select($receQnty_sql);
					foreach ($receQnty_sql_arr as $row) 
					{
						// echo $row[csf('booking_id')].']['.$row[csf('job_no')].']['.$row[csf('body_part_id')].']['.$row[csf('color_id')].'*<br>';
						$recv_qnty_arr[$row[csf('booking_id')]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["receive_qnty_total"]=$row[csf('receive_qnty')];
						$recv_qnty_arr[$row[csf('booking_id')]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["receive_qnty_prev"]=$row[csf('receive_qnty_prev')];
						$recv_qnty_arr[$row[csf('booking_id')]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["receive_qnty_today"]=$row[csf('receive_qnty_today')];
						$recv_qnty_arr[$row[csf('booking_id')]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["batch_no"]=$row[csf('batch_no')];
						$recv_qnty_arr[$row[csf('booking_id')]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["recv_id"]=$row[csf('id')];
					}

					$issue_retnQnty_sql=" SELECT a.id,b.prod_id,c.booking_no_id as booking_id,b.color_id,
					sum(b.receive_qnty) as issue_retn_qnty
					,sum(case when a.entry_form in(52) and d.transaction_date<'$txt_date_from' then b.receive_qnty else 0 end) as issue_retn_qnty_prev
					,sum(case when a.entry_form in(52) and d.transaction_date='$txt_date_from' then b.receive_qnty else 0 end) as issue_retn_qnty_today $batch_no_cond, b.body_part_id,c.color_id as batch_color,e.job_no  
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c,inv_transaction d, wo_booking_mst e where a.id=b.mst_id and c.id=b.batch_id and b.trans_id=d.id and c.booking_no_id=e.id and a.entry_form in(52) $booking_ids_cond $store_id_cond and b.trans_id>0 group by  a.id,b.prod_id,c.booking_no_id,b.body_part_id,b.color_id,c.color_id,e.job_no";
 					$issue_retnQnty_sql_arr=sql_select($issue_retnQnty_sql);
					foreach ($issue_retnQnty_sql_arr as $row) 
					{
						$issue_retn_qnty_arr[$row[csf('booking_id')]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["issue_retn_qnty"] 		=$row[csf('issue_retn_qnty')];
						$issue_retn_qnty_arr[$row[csf('booking_id')]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["issue_retn_qnty_prev"] 	=$row[csf('issue_retn_qnty_prev')];
						$issue_retn_qnty_arr[$row[csf('booking_id')]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["issue_retn_qnty_today"] 	=$row[csf('issue_retn_qnty_today')];
						$issue_retn_qnty_arr[$row[csf('booking_id')]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["issue_retn_id"] 	=$row[csf('id')];
					}


					$transInQnty_sql="SELECT a.id,b.to_body_part as body_part_id,b.to_ord_book_id as booking_id,sum(b.transfer_qnty) as transfer_qnty_in
					,sum(case when a.entry_form in(14) and d.transaction_date<'$txt_date_from' then b.transfer_qnty else 0 end) as transfer_qnty_in_prev
					,sum(case when a.entry_form in(14) and d.transaction_date='$txt_date_from' then b.transfer_qnty else 0 end) as transfer_qnty_in_today
					,c.batch_no,c.color_id as batch_color,b.to_store,e.color_id,f.job_no  
					from inv_item_transfer_mst a, inv_item_transfer_dtls b,pro_batch_create_mst c,inv_transaction d ,order_wise_pro_details e,wo_booking_mst f 
					where a.id=b.mst_id and b.trans_id=d.id and c.id=b.batch_id and b.id=e.dtls_id and d.id=e.trans_id  and b.to_ord_book_id=f.id and a.entry_form in(14) $booking_ids_cond_trns_in $store_id_cond_trans_in and b.trans_id>0 and b.trans_id>0  group by a.id,b.to_body_part,b.to_ord_book_id,c.batch_no,c.color_id,b.to_store,e.color_id,f.job_no";
					//and b.to_order_id='$poIDS'
					$transInQnty_sql_arr=sql_select($transInQnty_sql);
					foreach ($transInQnty_sql_arr as $row) 
					{
						$transInQnty_arr[$row[csf('booking_id')]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["transfer_qnty_in"] 		=$row[csf('transfer_qnty_in')];
						$transInQnty_arr[$row[csf('booking_id')]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["transfer_qnty_in_prev"] 	=$row[csf('transfer_qnty_in_prev')];
						$transInQnty_arr[$row[csf('booking_id')]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["transfer_qnty_in_today"] 	=$row[csf('transfer_qnty_in_today')];
						$transInQnty_arr[$row[csf('booking_id')]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["transfer_in_id"] 	=$row[csf('id')];
					}

					//Issue Qury
					$issueQnty_sql="SELECT a.id,c.booking_no_id as booking_id,e.color_id,b.body_part_id,sum(b.issue_qnty) as issue_qnty ,sum(case when a.entry_form in(18) and d.transaction_date<'$txt_date_from' then b.issue_qnty else 0 end) as issue_qnty_prev,sum(case when a.entry_form in(18) and d.transaction_date='$txt_date_from' then b.issue_qnty else 0 end) as issue_qnty_today $batch_no_cond 
					from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c,inv_transaction d ,order_wise_pro_details e where a.id=b.mst_id and b.trans_id=d.id and c.id=b.batch_id and b.id=e.dtls_id and d.id=e.trans_id and a.entry_form in(18) $booking_ids_cond $store_id_cond and b.trans_id>0 group by a.id,c.booking_no_id,e.color_id,b.body_part_id";
					// echo $issueQnty_sql;die;
					$issueQnty_sql_arr=sql_select($issueQnty_sql);

					foreach ($issueQnty_sql_arr as $row) 
					{
						$issue_qnty_arr[$row[csf('booking_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["issue_qnty_total"]+=$row[csf('issue_qnty')];
						$issue_qnty_arr[$row[csf('booking_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["issue_qnty_prev"]+=$row[csf('issue_qnty_prev')];
						$issue_qnty_arr[$row[csf('booking_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["issue_qnty_today"]+=$row[csf('issue_qnty_today')];
						$issue_qnty_arr[$row[csf('booking_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["batch_no"]=$row[csf('batch_no')];
						$issue_qnty_arr[$row[csf('booking_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["issue_id"]=$row[csf('id')];
					}

					/*echo "<pre>";
					print_r($issue_qnty_arr);*/

					$recv_retn_Qnty_sql="SELECT a.id,f.id as booking_id,b.body_part_id,sum(b.issue_qnty) as recv_rtn_qnty
					,sum(case when a.entry_form in(46) and d.transaction_date<'$txt_date_from' then b.issue_qnty else 0 end) as recv_rtn_qnty_prev,sum(case when a.entry_form in(46) and d.transaction_date='$txt_date_from' then b.issue_qnty else 0 end) as recv_rtn_qnty_today,
					c.color_id as batch_color ,e.color_id 
					from inv_issue_master a, inv_finish_fabric_issue_dtls b,pro_batch_create_mst c,inv_transaction d ,order_wise_pro_details e,wo_booking_mst f 
					where a.id=b.mst_id and b.trans_id=d.id and c.id=b.batch_id and b.id=e.dtls_id and d.id=e.trans_id and b.booking_no=f.booking_no and a.entry_form in(46) $booking_ids_cond $store_id_cond and b.trans_id>0 and b.trans_id>0 group by a.id,f.id,b.body_part_id,c.color_id,e.color_id";
					$recvRetrnQnty_sql_arr=sql_select($recv_retn_Qnty_sql);
					foreach ($recvRetrnQnty_sql_arr as $row) 
					{
						$recvRtrn_qnty_arr[$row[csf('booking_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["recv_rtn_qnty"]=$row[csf('recv_rtn_qnty')];
						$recvRtrn_qnty_arr[$row[csf('booking_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["recv_rtn_qnty_prev"]=$row[csf('recv_rtn_qnty_prev')];
						$recvRtrn_qnty_arr[$row[csf('booking_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["recv_rtn_qnty_today"]=$row[csf('recv_rtn_qnty_today')];
						$recvRtrn_qnty_arr[$row[csf('booking_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["recv_rtn_id"]=$row[csf('id')];
					}
						//print_r($recvRtrn_qnty_arr);


					$transOutQnty_sql="SELECT a.id,b.body_part_id,c.booking_no_id as booking_id,sum(b.transfer_qnty) as transfer_qnty_out
					,sum(case when a.entry_form in(14) and d.transaction_date<'$txt_date_from' then b.transfer_qnty else 0 end) as transfer_qnty_out_prev
					,sum(case when a.entry_form in(14) and d.transaction_date='$txt_date_from' then b.transfer_qnty else 0 end) as transfer_qnty_out_today
					,c.batch_no,c.color_id as batch_color,b.to_store,e.color_id,f.job_no  
					from inv_item_transfer_mst a, inv_item_transfer_dtls b,pro_batch_create_mst c,inv_transaction d ,order_wise_pro_details e,wo_booking_mst f 
					where a.id=b.mst_id and b.trans_id=d.id and c.id=b.batch_id and b.id=e.dtls_id and d.id=e.trans_id  and b.to_ord_book_id=f.id and a.entry_form in(14) $booking_ids_cond $store_id_cond and b.trans_id>0 and b.trans_id>0  group by a.id,b.body_part_id,c.booking_no_id,c.batch_no,c.color_id,b.to_store,e.color_id,f.job_no";

					$transOutQnty_sql_arr=sql_select($transOutQnty_sql);
					foreach ($transOutQnty_sql_arr as $row) 
					{
						$transOutQnty_arr[$row[csf('booking_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["transfer_qnty_out"]=$row[csf('transfer_qnty_out')];
						$transOutQnty_arr[$row[csf('booking_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["transfer_qnty_out_prev"]=$row[csf('transfer_qnty_out_prev')];
						$transOutQnty_arr[$row[csf('booking_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["transfer_qnty_out_today"]=$row[csf('transfer_qnty_out_today')];
						$transOutQnty_arr[$row[csf('booking_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["transfer_out_id"]=$row[csf('id')];
					}



					$i=1; $k=1;$total_rec_return_qnty=0;$total_issue_ret_qnty=0;$issue_trns_out_qnty=0;$rec_trns_in_qnty=0;
					$fin_color_array=array(); $fin_color_data_arr=array();$checkbuyerArr=array(); $po_break_id_arr=array();

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

					foreach ($main_arr as $booking_no => $booking_data)
					{
						foreach ($booking_data as $gmts_color_id => $gmts_color_data)
						{
							$color_span_arr_count[$booking_no]+=1;
							foreach ($gmts_color_data as $buyer_id => $buyer_data)
							{
								foreach ($buyer_data as $season => $season_data)
								{
									foreach ($season_data as $job_no => $job_data)
									{
										foreach ($job_data as $style_ref_no => $style_data)
										{
											foreach ($style_data as $body_part_id => $body_data)
											{
													foreach ($body_data as $color_type_id => $color_type_data)
													{
														foreach ($color_type_data as $fabric_color_id => $fabric_color_data)
														{
															foreach ($fabric_color_data as $construction => $construction_data)
															{
																$booking_span_arr[$booking_no]++;
																$color_span_arr[$booking_no][$gmts_color_id]++;
															}
														}
													}
											}
										}	
									}
								}
							}
						}
					}
				
					/*echo "<pre>";
					print_r($color_span_arr);
					//echo $color_span_arr_count;
					die;*/		
				
					
					foreach ($main_arr as $booking_no => $booking_data)
					{
						$row_looping_booking=0;
						foreach ($booking_data as $gmts_color_id => $gmts_color_data)
						{
							$row_looping_gmts_color=0;
							foreach ($gmts_color_data as $buyer_id => $buyer_data)
							{
								foreach ($buyer_data as $season => $season_data)
								{
									foreach ($season_data as $job_no => $job_data)
									{
										foreach ($job_data as $style_ref_no => $style_data)
										{
											foreach ($style_data as $body_part_id => $body_data)
											{
												foreach ($body_data as $color_type_id => $color_type_data)
												{
													foreach ($color_type_data as $fabric_color_id => $fabric_color_data)
													{
														foreach ($fabric_color_data as $construction => $construction_data)
														{
															$poNum="";$poIDS="";
															$poID=explode(",", $construction_data["po_break_down_id"]);
															foreach ($poID as $poIds) 
															{
																$poNum.=$order_arr[$poIds].",";
																$poIDS.=$poIds.",";
															}
															
																//echo $booking_span_count;
					
															if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
															$dzn_qnty=0; $rec_qty=0;
															if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
															else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
															else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
															else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
															else $dzn_qnty=1;

															$order_id=$row[csf('po_id')];
															$color_id=$row[csf("color_id")];

															$issue_ret_qnty=$row[csf('issue_ret_qnty')];
															$trans_in_qty=$row[csf("rec_trns_qnty")];
															$rec_qty=($row[csf("receive_qnty")]);

															$trans_out=($row[csf("issue_trns_qnty")]);
															$iss_qty=$row[csf("issue_qnty")];
															$rec_ret_qnty=($rec_return_qnty[$row[csf('po_id')]][$row[csf('color_id')]]['rec_ret_qnty']);
															//echo $trans_in_qty.'+'.$rec_qty.'+'.$issue_ret_qnty.'<br>';

															$total_receive=$trans_in_qty+$rec_qty+$issue_ret_qnty;
															$total_issue=$trans_out+$iss_qty+$rec_ret_qnty;
															$stock = 0;
															$stock = $total_receive-$total_issue;
															$stock = number_format($stock,2);
															// echo $stock."<br>";
															// echo $value_for_search_by;
															// die('Go to hell');
															$color_wise_qnty_arr[$booking_no][$gmts_color_id]=$construction_data["fin_fab_qnty"];
															//recv id
															$recv_ids= $recv_qnty_arr[$construction_data["booking_id"]][$job_no][$body_part_id][$fabric_color_id]["recv_id"];
															//issue_id
															$issue_ids=$issue_qnty_arr[$construction_data["booking_id"]][$body_part_id][$fabric_color_id]["issue_id"];
															//transfer_in_id
															$trans_in_ids=$transInQnty_arr[$construction_data["booking_id"]][$job_no][$body_part_id][$fabric_color_id]["transfer_in_id"];
															//transfer_out_id
															$trans_out_ids=$transOutQnty_arr[$construction_data["booking_id"]][$body_part_id][$fabric_color_id]["transfer_out_id"];;
															//issue_rtn_id
															$issue_rtn_ids=$issue_retn_qnty_arr[$construction_data["booking_id"]][$job_no][$body_part_id][$fabric_color_id]["issue_retn_id"];
															//recv_rtn_id
															$recv_rtn_ids=$recvRtrn_qnty_arr[$construction_data["booking_id"]][$body_part_id][$fabric_color_id]["recv_rtn_id"];

															// ALL RECEIVE Prev 
															//echo $construction_data["booking_id"].']['.$job_no.']['.$body_part_id.']['.$fabric_color_id.'=<br>';
															$recv_prev_qnty= $recv_qnty_arr[$construction_data["booking_id"]][$job_no][$body_part_id][$fabric_color_id]["receive_qnty_prev"];
															$issue_rtn_prev_qnty=$issue_retn_qnty_arr[$construction_data["booking_id"]][$job_no][$body_part_id][$fabric_color_id]["issue_retn_qnty_prev"];
															$trans_in_prev_qnty=$transInQnty_arr[$construction_data["booking_id"]][$job_no][$body_part_id][$fabric_color_id]["transfer_qnty_in_prev"];

															//ALL RECEIVE Today
															$recv_today_qnty=$recv_qnty_arr[$construction_data["booking_id"]][$job_no][$body_part_id][$fabric_color_id]["receive_qnty_today"];
															$issue_rtn_today_qnty=$issue_retn_qnty_arr[$construction_data["booking_id"]][$job_no][$body_part_id][$fabric_color_id]["issue_retn_qnty_today"];
															$trans_in_today_qnty=$transInQnty_arr[$construction_data["booking_id"]][$job_no][$body_part_id][$fabric_color_id]["transfer_qnty_in_today"];

															// ALL RECEIVE
															$tot_recv=$recv_qnty_arr[$construction_data["booking_id"]][$job_no][$body_part_id][$fabric_color_id]["receive_qnty_total"];
															$tot_issue_rtn=$issue_retn_qnty_arr[$construction_data["booking_id"]][$job_no][$body_part_id][$fabric_color_id]["issue_retn_qnty"];
															$tot_trans_in_qnty=$transInQnty_arr[$construction_data["booking_id"]][$job_no][$body_part_id][$fabric_color_id]["transfer_qnty_in"];

															// ALL ISSUE Prev
															$issue_prev_qnty= $issue_qnty_arr[$construction_data["booking_id"]][$body_part_id][$fabric_color_id]["issue_qnty_prev"];
															$recv_rtn_prev_qnty=$recvRtrn_qnty_arr[$construction_data["booking_id"]][$body_part_id][$fabric_color_id]["recv_rtn_qnty_prev"];
															$trans_out_prev_qnty=$transOutQnty_arr[$construction_data["booking_id"]][$body_part_id][$fabric_color_id]["transfer_qnty_out_prev"];

															//ALL ISSUE Today
															$issue_today_qnty=$issue_qnty_arr[$construction_data["booking_id"]][$body_part_id][$fabric_color_id]["issue_qnty_today"];
															$recv_rtn_today_qnty=$recvRtrn_qnty_arr[$construction_data["booking_id"]][$body_part_id][$fabric_color_id]["recv_rtn_qnty_today"];
															$trans_out_today_qnty=$transOutQnty_arr[$construction_data["booking_id"]][$body_part_id][$fabric_color_id]["transfer_qnty_out_today"];

															// ALL ISSUE
															$tot_issue=$issue_qnty_arr[$construction_data["booking_id"]][$body_part_id][$fabric_color_id]["issue_qnty_total"];
															$tot_recv_rtn=$recvRtrn_qnty_arr[$construction_data["booking_id"]][$body_part_id][$fabric_color_id]["recv_rtn_qnty"];
															$tot_trans_out_qnty=$transOutQnty_arr[$construction_data["booking_id"]][$body_part_id][$fabric_color_id]["transfer_qnty_out"];
															?>
															
															<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
																<? 
																if($row_looping_booking==0) 
																{
																	$bookingSpan=$color_span_arr_count[$booking_no]+$booking_span_arr[$booking_no];
																	?>

																	<td rowspan="<? echo $bookingSpan; ?>" width="30" align="center"><? echo $i; ?></td>
																	<td rowspan="<? echo $bookingSpan; ?>" width="120" align="center"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
																	<td rowspan="<? echo $bookingSpan; ?>" width="120" align="center"><p><? echo $season; ?></p></td>
																	<td rowspan="<? echo $bookingSpan; ?>" width="120" align="center"><p><? echo $job_no; ?></p></td>
																	<td class="line_br" rowspan="<? echo $bookingSpan; ?>" width="120" align="center"><p><? echo chop($poNum,","); //$order_arr[$po_break_down_id]; ?></p></td>
																	<td rowspan="<? echo $bookingSpan; ?>" width="120" align="center"><p><? echo $style_ref_no; ?></p></td>
																	<td title="<? echo $construction_data["booking_id"]; ?>" rowspan="<? echo $bookingSpan; ?>" width="120" align="center"><p><? echo $booking_no; ?></p></td>

																	<?
																	$i++;
																}

																if($row_looping_gmts_color==0) 
																{
																	?>

																	<td class="line_br" rowspan="<? echo $color_span_arr[$booking_no][$gmts_color_id]; ?>" width="120" align="center"><p><? echo $color_arr[$gmts_color_id]; 
																		

																	?></p></td>
																	<?
																}
																?>

																<td class="line_br" width="120" align="center" title="<? echo $body_part_id; ?>"><p><? echo $body_part[$body_part_id]; ?></p></td>
																<td class="line_br" width="120" align="center"><p><? echo $color_type[$color_type_id]; ?></p></td>
																<td class="line_br" width="120" align="center" title="<? echo $fabric_color_id; ?>"><p><? echo $color_arr[$fabric_color_id]; ?></p></td>
																<td class="line_br" width="120" align="center"><p><? echo $construction; ?></p></td>
																<td class="line_br" width="120" align="center"><p><? echo number_format($construction_data["fin_fab_qnty"],2,'.',''); ?></p></td>
																<td class="line_br" width="120" align="center"><p><? echo $recvPrevQnty=$recv_prev_qnty+$issue_rtn_prev_qnty+$trans_in_prev_qnty;
																	?>
																</p></td>
																<td class="line_br" width="120" align="center"><p><? echo $recvTodayQnty=$recv_today_qnty+$issue_rtn_today_qnty+$trans_in_today_qnty; ?></p></td>
																<td class="line_br" width="120" align="center"><p>
																	<a href='#report_details' onClick="openmypage_issue_recv('<? echo $recv_qnty_arr[$construction_data["booking_id"]][$job_no][$body_part_id][$fabric_color_id]["recv_id"]; ?>','1','<? echo $buyer_id."*".$job_no."*".chop($poIDS,",")."*".$booking_no."*".$gmts_color_id."*".$body_part_id."*".$color_type_id."*".$fabric_color_id."*".$construction."*".$txt_date_from."*".$cbo_location_id."*".$cbo_store_id."*".$cbo_company_id."*".$recv_ids."___".$issue_rtn_ids."___".$trans_in_ids; ?>');">
																	<? echo $recvQnty=$tot_recv+$tot_issue_rtn+$tot_trans_in_qnty;?></a></p>
																</td>
																<td class="line_br" width="120" align="center"><p><? $balanceQnty= $construction_data["fin_fab_qnty"]-$recvQnty; 
																echo number_format($balanceQnty,4,'.','');?></p></td>

																<td class="line_br" width="120" align="center"><p><? echo $issuePrevQnty=$issue_prev_qnty+$recv_rtn_prev_qnty+$trans_out_prev_qnty; ?></p></td>
																<td class="line_br" width="120" align="center"><p><? echo $issueTodayQnty=$issue_today_qnty+$recv_rtn_today_qnty+$trans_out_today_qnty; ?></p></td>
																<td class="line_br" width="120" align="center"><p>
																	<a href='#report_details' onClick="openmypage_issue_recv('<? echo $issue_qnty_arr[$construction_data["booking_id"]][$body_part_id][$fabric_color_id]["issue_id"]; ?>','2','<? echo $buyer_id."*".$job_no."*".chop($poIDS,",")."*".$booking_no."*".$gmts_color_id."*".$body_part_id."*".$color_type_id."*".$fabric_color_id."*".$construction."*".$txt_date_from."*".$cbo_location_id."*".$cbo_store_id."*".$cbo_company_id."*".$issue_ids."___".$recv_rtn_ids."___".$trans_out_ids; ?>');">
																		<? echo $issueQnty= $tot_issue+$tot_recv_rtn+$tot_trans_out_qnty; ?></a></p>
																</td>
																<td class="line_br" width="120" align="center"><p><? echo $stock= $recvQnty-$issueQnty; ?></p></td>
																<td class="line_br" colspan="2" align="center"><p><? echo $recv_qnty_arr[$construction_data["booking_id"]][$job_no][$body_part_id][$fabric_color_id]["batch_no"]; ?></p></td>
															</tr>
															<?
															$row_looping_booking++;
															$row_looping_gmts_color++;
															//$i++;
															$booking_tot_fin_fab_qnty+=$construction_data["fin_fab_qnty"];
															$color_tot_qnty+=$color_wise_qnty_arr[$booking_no][$gmts_color_id];
															$color_tot_recv_prev+=$recvPrevQnty;
															$color_tot_recv_today+=$recvTodayQnty;
															$color_tot_recv+=$recvQnty;
															$color_tot_balance+=$balanceQnty;

															$color_tot_issue_prev+=$issuePrevQnty;
															$color_tot_issue_today+=$issueTodayQnty;
															$color_tot_issue+=$issueQnty;
															$color_tot_stock+=$stock;


															$booking_tot_recv_prev+=$recvPrevQnty;
															$booking_tot_recv_today+=$recvTodayQnty;
															$booking_tot_recv+=$recvQnty;
															$booking_tot_balance+=$balanceQnty;

															$booking_tot_issue_prev+=$issuePrevQnty;
															$booking_tot_issue_today+=$issueTodayQnty;
															$booking_tot_issue+=$issueQnty;
															$booking_tot_stock+=$stock;
														}
													}
												}
											}
										}	
									}
								}
							}
						
								?>
								<tr bgcolor="#ccc">
									<td align="right" colspan="5"><strong>Color Total</strong></td>
									<td align="center"><strong><? echo number_format($color_tot_qnty,4,'.',''); ?></strong></td>
									<td align="center"><strong><?  echo $color_tot_recv_prev; ?></strong></td>
									<td align="center"><strong><?  echo $color_tot_recv_today; ?></strong></td>
									<td align="center"><strong><?  echo $color_tot_recv; ?></strong></td>
									<td align="center"><strong><?  echo number_format($color_tot_balance,4,'.',''); ?></strong></td>

									<td align="center"><strong><?  echo $color_tot_issue_prev; ?></strong></td>
									<td align="center"><strong><?  echo $color_tot_issue_today; ?></strong></td>
									<td align="center"><strong><?  echo $color_tot_issue; ?></strong></td>
									<td align="center"><strong><?  echo $color_tot_stock; ?></strong></td>
									<td colspan="2"></td>
									
								</tr>
								<?	
								$color_tot_qnty=0;
								$color_tot_recv_prev=0;
								$color_tot_recv_today=0;
								$color_tot_recv=0;
								$color_tot_balance=0;
								$color_tot_issue_prev=0;
								$color_tot_issue_today=0;
								$color_tot_issue=0;
						}
							?>
							<tr bgcolor="grey">
								<td align="right" colspan="12"><strong>Booking Total</strong></td>
								<td align="center"><strong><?  echo number_format($booking_tot_fin_fab_qnty,4,'.',''); ?></strong></td>

								<td align="center"><strong><?  echo $booking_tot_recv_prev; ?></strong></td>
								<td align="center"><strong><?  echo $booking_tot_recv_today; ?></strong></td>
								<td align="center"><strong><?  echo $booking_tot_recv; ?></strong></td>
								<td align="center"><strong><?  echo number_format($booking_tot_balance,4,'.',''); ?></strong></td>

								<td align="center"><strong><?  echo $booking_tot_issue_prev; ?></strong></td>
								<td align="center"><strong><?  echo $booking_tot_issue_today; ?></strong></td>
								<td align="center"><strong><?  echo $booking_tot_issue; ?></strong></td>
								<td align="center"><strong><?  echo $booking_tot_stock; ?></strong></td>
								<td colspan="2"></td>
								
							</tr>
							<?
							$booking_tot_fin_fab_qnty=0;
							$booking_tot_recv_prev=0;
							$booking_tot_recv_today=0;
							$booking_tot_recv=0;
							$booking_tot_balance=0;
							$booking_tot_issue_prev=0;
							$booking_tot_issue_today=0;
							$booking_tot_issue=0;
					}			
					?>
					
			</div>
		</fieldset>
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


    /*$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    //echo "$html**$filename**$type";
    echo "$html";*/
    exit();
}
if($action=="open_recv_issue_popup")
{
	echo load_html_head_contents("Received and Issued Popup", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data_string_arr=explode("*", $data_string);

	$buyer_id 		=$data_string_arr[0];
	$job_no 		=$data_string_arr[1];
	$poIDS 			=$data_string_arr[2];
	$booking_no 	=$data_string_arr[3];
	$gmts_color_id 	=$data_string_arr[4];
	$body_part_id 	=$data_string_arr[5];
	$color_type_id 	=$data_string_arr[6];
	$fabric_color_id=$data_string_arr[7];
	$construction 	=$data_string_arr[8];
	$txt_date_from 	=$data_string_arr[9];
	$cbo_location_id=$data_string_arr[10];
	$cbo_store_id 	=$data_string_arr[11];
	$cbo_company_id =$data_string_arr[12];
	$system_mst_ids =$data_string_arr[13];

	$system_mst_ids=explode("___", $system_mst_ids);
	$system_mst_id=$system_mst_ids[0];
	$system_rtn_id=$system_mst_ids[1];
	$system_trnsf_id=$system_mst_ids[2];

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');

	$fabric_desc_arr=return_library_array("select id, item_description from product_details_master where item_category_id=2","id","item_description");

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
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
	if($rpt_type==1)
	{
		?>
		<fieldset style="width:1300px; margin-left:3px">
			<strong>Receive Details</strong>
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="1320" cellpadding="0" cellspacing="0" align="center">
					<thead>
						<tr>
							<th width="50">Sl</th>
							<th width="100">Product ID</th>
							<th width="100">Transection ID</th>
							<th width="100">Transection Date</th>
							<th width="100">Batch No</th>
							<th width="100">Service Company</th>
							<th width="100">Service Location</th>
							<th width="100">Batch Color</th>
							<th width="100">Fabric Des.</th>
							<th width="100">GSM</th>
							<th width="100">F.Dia</th>
							<th width="100">Fin. Rcv. Qty.</th>
							<th>Store Name</th>
						</tr>
					</thead>
					<tbody>
						<?	
						if($cbo_location_id>0) $location_id_cond=" and a.location_id=$cbo_location_id";
						if($cbo_store_id!=0) $store_id_cond=" and d.store_id=$cbo_store_id";
						if($job_no!="") $job_no_cond=" and b.job_no='$job_no'";
						if($fabric_color_id!="") $fabric_color_id_cond=" and b.color_id='$fabric_color_id'";
						if($body_part_id!="") $body_part_id_cond=" and b.body_part_id='$body_part_id'";
						if($poIDS!="") $order_id_cond=" and b.order_id in('$poIDS')";
						if($txt_date_from!="") $txt_date_from_cond=" and d.transaction_date<'$txt_date_from'";
						if($system_mst_id!="") $recv_id_cond=" and a.id in('$system_mst_id')";
						$receQnty_sql=" select a.id,a.recv_number,a.knitting_source,a.knitting_company,a.knitting_location_id,b.prod_id,b.booking_id,b.job_no,b.color_id,b.fabric_description_id,
						sum(b.receive_qnty) as receive_qnty,b.body_part_id,b.gsm,b.width,c.batch_no,c.color_id as batch_color,d.transaction_date,d.store_id 
						 from inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c,inv_transaction d where a.id=b.mst_id and c.id=b.batch_id and b.trans_id=d.id and a.entry_form in(7,37) and c.booking_no='$booking_no' $job_no_cond $fabric_color_id_cond $body_part_id_cond $order_id_cond $location_id_cond $store_id_cond  $txt_date_from_cond $recv_id_cond and d.transaction_type=1 and b.trans_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by  a.id,a.recv_number,a.knitting_source,a.knitting_company,a.knitting_location_id,b.prod_id,b.booking_id,b.job_no,b.body_part_id,b.color_id,b.fabric_description_id,b.gsm,b.width,c.batch_no,c.color_id,d.transaction_date,d.store_id";
						$receQnty_sql_arr=sql_select($receQnty_sql);
						/*foreach ($receQnty_sql_arr as $row) 
						{
							$recv_qnty_arr[$row[csf('booking_id')]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["receive_qnty_total"]=$row[csf('receive_qnty')];
							$recv_qnty_arr[$row[csf('booking_id')]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["receive_qnty_prev"]=$row[csf('receive_qnty_prev')];
							$recv_qnty_arr[$row[csf('booking_id')]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["receive_qnty_today"]=$row[csf('receive_qnty_today')];
							$recv_qnty_arr[$row[csf('booking_id')]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["batch_no"]=$row[csf('batch_no')];
							$recv_qnty_arr[$row[csf('booking_id')]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_id')]]["recv_id"]=$row[csf('id')];
						}*/


						$i=1;$tot_recv_qnty=0;
						foreach($receQnty_sql_arr as $row)
						{
							
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if( $row[csf('knitting_source')]==1){$knitting_company=$company_arr[$row[csf('knitting_company')]];}else{$knitting_company=$supplier_name[$row[csf('knitting_company')]];}
							if($row[csf('fabric_description_id')]==0 || $row[csf('fabric_description_id')]=="")
								$fabric_desc=$fabric_desc_arr[$row[csf('prod_id')]];
							else
								$fabric_desc=$composition_arr[$row[csf('fabric_description_id')]];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="50" align="center"><p><? echo $i; ?></p>xxx</td>
								<td width="100" align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
								<td width="100" align="center"><p><? echo  $row[csf('recv_number')];?></p></td>
								<td width="100" align="center"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('batch_no')]; ?></p></td>
								<td width="100" align="center"><p><? echo $knitting_company; ?></p></td>
								<td width="100" align="center"><p><? echo $location_arr[$row[csf('knitting_location_id')]];  ?></p></td>
								<td width="100" align="center"><p><? echo $color_arr[$row[csf('batch_color')]]; ?></p></td>
								<td width="100" align="center"><p><? echo $fabric_desc; ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('gsm')]; ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('width')]; ?></p></td>
								<td width="100" align="right"><p><? echo number_format($row[csf('receive_qnty')],2); ?></p></td>
								<td align="center"><? echo $store_arr[$row[csf('store_id')]]; ?></td>
							</tr>
							<?
							$tot_recv_qnty+=$row[csf('receive_qnty')];
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="11" align="right">Total</td>
							<td align="right">&nbsp;<? echo number_format($tot_recv_qnty,2); ?>&nbsp;</td>
							<td></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<fieldset style="width:1300px; margin-left:3px">
			<strong>Issue Return Details</strong>
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="1320" cellpadding="0" cellspacing="0" align="center">
					<thead>
						<tr>
							<th width="50">Sl</th>
							<th width="100">Product ID</th>
							<th width="100">Transection ID</th>
							<th width="100">Transection Date</th>
							<th width="100">Batch No</th>
							<th width="100">Service Company</th>
							<th width="100">Service Location</th>
							<th width="100">Batch Color</th>
							<th width="100">Fabric Des.</th>
							<th width="100">GSM</th>
							<th width="100">F.Dia</th>
							<th width="100">Fin. Rcv. Qty.</th>
							<th>Store Name</th>
						</tr>
					</thead>
					<tbody>
						<?	
						//if($cbo_location_id!="" && $cbo_location_id!=0) $location_id_cond=" and a.location_id=$cbo_location_id";
						if($cbo_location_id>0) $location_id_cond=" and a.location_id=$cbo_location_id";
						if($cbo_store_id!=0) $store_id_cond=" and d.store_id=$cbo_store_id";
						if($job_no!="") $job_no_cond=" and b.job_no='$job_no'";
						if($fabric_color_id!="") $fabric_color_id_cond=" and b.color_id='$fabric_color_id'";
						if($body_part_id!="") $body_part_id_cond=" and b.body_part_id='$body_part_id'";
					
						$issue_retnQnty_sql=" select a.id,a.recv_number,a.knitting_source,a.knitting_company,a.knitting_location_id,b.prod_id,b.booking_id,b.job_no,b.color_id,b.fabric_description_id,
						sum(b.receive_qnty) as receive_qnty,b.body_part_id,b.gsm,b.width,c.batch_no,c.color_id as batch_color,d.transaction_date,d.store_id 
						 from inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c,inv_transaction d where a.id=b.mst_id and c.id=b.batch_id and b.trans_id=d.id and a.entry_form in(52) and c.booking_no='$booking_no' $location_id_cond $job_no_cond $fabric_color_id_cond $body_part_id_cond $store_id_cond and b.trans_id>0 and d.transaction_type=4 and a.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  group by  a.id,a.recv_number,a.knitting_source,a.knitting_company,a.knitting_location_id,b.prod_id,b.booking_id,b.job_no,b.body_part_id,b.color_id,b.fabric_description_id,b.gsm,b.width,c.batch_no,c.color_id,d.transaction_date,d.store_id";
						$issue_retnQnty_sql_arr=sql_select($issue_retnQnty_sql);

						$i=1;$tot_issue_rtn_qnty=0;
						foreach($issue_retnQnty_sql_arr as $row)
						{
							
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if( $row[csf('knitting_source')]==1){$knitting_company=$company_arr[$row[csf('knitting_company')]];}else{$knitting_company=$supplier_name[$row[csf('knitting_company')]];}
							if($row[csf('fabric_description_id')]==0 || $row[csf('fabric_description_id')]=="")
								$fabric_desc=$fabric_desc_arr[$row[csf('prod_id')]];
							else
								$fabric_desc=$composition_arr[$row[csf('fabric_description_id')]];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="50" align="center"><p><? echo $i; ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
								<td width="100" align="center"><p><? echo  $row[csf('recv_number')];?></p></td>
								<td width="100" align="center"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('batch_no')]; ?></p></td>
								<td width="100" align="center"><p><? echo $knitting_company; ?></p></td>
								<td width="100" align="center"><p><? echo $location_arr[$row[csf('knitting_location_id')]];  ?></p></td>
								<td width="100" align="center"><p><? echo $color_arr[$row[csf('batch_color')]]; ?></p></td>
								<td width="100" align="center"><p><? echo $fabric_desc; ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('gsm')]; ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('width')]; ?></p></td>
								<td width="100" align="right"><p><? echo number_format($row[csf('receive_qnty')],2); ?></p></td>
								<td align="center"><? echo $store_arr[$row[csf('store_id')]]; ?></td>
							</tr>
							<?
							$tot_issue_rtn_qnty+=$row[csf('receive_qnty')];
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="11" align="right">Total</td>
							<td align="right">&nbsp;<? echo number_format($tot_issue_rtn_qnty,2); ?>&nbsp;</td>
							<td></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<fieldset style="width:1300px; margin-left:3px">
			<strong>Transfer In Details</strong>
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="1320" cellpadding="0" cellspacing="0" align="center">
					<thead>
						<tr>
							<th width="50">Sl</th>
							<th width="100">Product ID</th>
							<th width="100">Transection ID</th>
							<th width="100">Transection Date</th>
							<th width="100">Batch No</th>
							<th width="100">Service Company</th>
							<th width="100">Service Location</th>
							<th width="100">Batch Color</th>
							<th width="100">Fabric Des.</th>
							<th width="100">GSM</th>
							<th width="100">F.Dia</th>
							<th width="100">Fin. Rcv. Qty.</th>
							<th>Store Name</th>
						</tr>
					</thead>
					<tbody>
						<?
						if($cbo_location_id>0) $location_id_cond=" and a.location_id=$cbo_location_id";
						if($cbo_store_id!=0) $store_id_cond=" and b.to_store=$cbo_store_id";
						if($system_trnsf_id!="") $system_trnsf_id_cond=" and a.id in('$system_trnsf_id')";

						/*if($job_no!="") $job_no_cond=" and b.job_no='$job_no'";
						if($fabric_color_id!="") $fabric_color_id_cond=" and b.color_id='$fabric_color_id'";
						if($body_part_id!="") $body_part_id_cond=" and b.body_part_id='$body_part_id'";
						*/
						//a.recv_number,a.knitting_source,a.knitting_company,a.knitting_location_id,b.prod_id,b.booking_id,b.job_no,b.body_part_id,b.color_id,
						//sum(b.receive_qnty) as receive_qnty,b.fabric_description_id,b.gsm,b.width
						if($system_trnsf_id!="")
						{
							$transInQnty_sql="select a.id,a.transfer_system_id,a.company_id,a.location_id,b.from_prod_id,b.body_part_id,sum(b.transfer_qnty) as transfer_qnty,c.batch_no,c.color_id as batch_color ,d.transaction_date,b.to_store,e.color_id 
							from inv_item_transfer_mst a, inv_item_transfer_dtls b,pro_batch_create_mst c,inv_transaction d ,order_wise_pro_details e 
							where a.id=b.mst_id and b.trans_id=d.id and c.id=b.batch_id and b.id=e.dtls_id and d.id=e.trans_id and a.entry_form in(14) and b.to_order_id='$poIDS' $system_trnsf_id_cond $store_id_cond and b.trans_id>0 and d.transaction_type=5 group by a.id,a.transfer_system_id,a.company_id,a.location_id,b.from_prod_id,b.body_part_id,c.batch_no,c.color_id,d.transaction_date, b.to_store,e.color_id ";
						}
						$transInQnty_sql_arr=sql_select($transInQnty_sql);
						$prod_ids="";
						foreach($transInQnty_sql_arr as $row)
						{
							$prod_ids.=$row[csf("from_prod_id")].",";
						}

						$prod_ids=chop($prod_ids,",");
						$product_data=sql_select("select id,product_name_details,gsm,dia_width from product_details_master where id in(".$prod_ids.")");
						foreach($product_data as $row)
						{
							$product_details_arr[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
							$product_details_arr[$row[csf("id")]]["gsm"]=$row[csf("gsm")];
							$product_details_arr[$row[csf("id")]]["dia_width"]=$row[csf("dia_width")];
						}
						$i=1;$tot_transIn_qty=0;
						foreach($transInQnty_sql_arr as $row)
						{
							
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if( $row[csf('knit_dye_source')]==1){$knitting_company=$company_arr[$row[csf('knit_dye_company')]];}else{$knitting_company=$supplier_name[$row[csf('knit_dye_company')]];}
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="50" align="center"><p><? echo $i; ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('from_prod_id')]; ?></p></td>
								<td width="100" align="center"><p><? echo  $row[csf('transfer_system_id')];?></p></td>
								<td width="100" align="center"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('batch_no')]; ?></p></td>
								<td width="100" align="center"><p><? echo $knitting_company; ?></p></td>
								<td width="100" align="center"><p><? echo $location_arr[$row[csf('location_id')]];  ?></p></td>
								<td width="100" align="center"><p><? echo $color_arr[$row[csf('batch_color')]]; ?></p></td>
								<td width="100" align="center"><p><? echo $product_details_arr[$row[csf("from_prod_id")]]["product_name_details"]; ?></p></td>
								<td width="100" align="center"><p><? echo $product_details_arr[$row[csf("from_prod_id")]]["gsm"]; ?></p></td>
								<td width="100" align="center"><p><? echo $product_details_arr[$row[csf("from_prod_id")]]["dia_width"]; ?></p></td>
								<td width="100" align="right"><p><? echo number_format($row[csf('transfer_qnty')],2); ?></p></td>
								<td align="center"><? echo $store_arr[$row[csf('to_store')]]; ?></td>
							</tr>
							<?
							$tot_transIn_qty+=$row[csf('transfer_qnty')];
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="11" align="right">Total</td>
							<td align="right">&nbsp;<? echo number_format($tot_transIn_qty,2); ?>&nbsp;</td>
							<td></td>
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
		<fieldset style="width:1300px; margin-left:3px">
			<strong>Issue Details</strong>
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="1320" cellpadding="0" cellspacing="0" align="center">
					<thead>
						<tr>
							<th width="50">Sl</th>
							<th width="100">Product ID</th>
							<th width="100">Transection ID</th>
							<th width="100">Transection Date</th>
							<th width="100">Batch No</th>
							<th width="100">Service Company</th>
							<th width="100">Service Location</th>
							<th width="100">Batch Color</th>
							<th width="100">Fabric Des.</th>
							<th width="100">GSM</th>
							<th width="100">F.Dia</th>
							<th width="100">Fin. Rcv. Qty.</th>
							<th>Store Name</th>
						</tr>
					</thead>
					<tbody>
						<?
						if($cbo_location_id>0) $location_id_cond=" and a.location_id=$cbo_location_id";
						if($cbo_store_id!=0) $store_id_cond=" and d.store_id=$cbo_store_id";
						if($body_part_id!="") $body_part_id_cond=" and b.body_part_id='$body_part_id'";
						
						if($poIDS!="") $order_id_cond=" and b.order_id in('$poIDS')";
						if($fabric_color_id!="") $fabric_color_id_cond=" and e.color_id='$fabric_color_id'";
						if($txt_date_from!="") $txt_date_from_cond=" and d.transaction_date<'$txt_date_from'";
						if($system_mst_id!="") $issue_id_cond=" and a.id in('$system_mst_id')";
				
						//a.recv_number,a.knitting_source,a.knitting_company,a.knitting_location_id,b.prod_id,b.booking_id,b.job_no,b.body_part_id,b.color_id,
						//sum(b.receive_qnty) as receive_qnty,b.fabric_description_id,b.gsm,b.width

						$issueQnty_sql="select a.id,a.issue_number,a.knit_dye_source,a.knit_dye_company,a.location_id,b.prod_id,a.booking_id,b.body_part_id,sum(b.issue_qnty) as issue_qnty,c.batch_no,c.color_id as batch_color ,d.transaction_date,d.store_id,e.color_id 
						from inv_issue_master a, inv_finish_fabric_issue_dtls b,pro_batch_create_mst c,inv_transaction d ,order_wise_pro_details e 
						where a.id=b.mst_id and b.trans_id=d.id and c.id=b.batch_id and b.id=e.dtls_id and d.id=e.trans_id and a.entry_form in(18) and c.booking_no='$booking_no' $store_id_cond $body_part_id_cond $fabric_color_id_cond $order_id_cond $txt_date_from_cond $issue_id_cond and b.trans_id>0 and d.transaction_type=2 group by a.id,a.issue_number,a.knit_dye_source,a.knit_dye_company,a.location_id,b.prod_id,a.booking_id,b.body_part_id,c.batch_no,c.color_id,d.transaction_date, d.store_id,e.color_id ";
						$issueQnty_sql_arr=sql_select($issueQnty_sql);
						$prod_ids="";
						foreach($issueQnty_sql_arr as $row)
						{
							$prod_ids.=$row[csf("prod_id")].",";
						}

						$prod_ids=chop($prod_ids,",");
						$product_data=sql_select("select id,product_name_details,gsm,dia_width from product_details_master where id in(".$prod_ids.")");
						foreach($product_data as $row)
						{
							$product_details_arr[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
							$product_details_arr[$row[csf("id")]]["gsm"]=$row[csf("gsm")];
							$product_details_arr[$row[csf("id")]]["dia_width"]=$row[csf("dia_width")];
						}
						$i=1;$tot_issue_qty=0;
						foreach($issueQnty_sql_arr as $row)
						{
							
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if( $row[csf('knit_dye_source')]==1){$knitting_company=$company_arr[$row[csf('knit_dye_company')]];}else{$knitting_company=$supplier_name[$row[csf('knit_dye_company')]];}
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="50" align="center"><p><? echo $i; ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
								<td width="100" align="center"><p><? echo  $row[csf('issue_number')];?></p></td>
								<td width="100" align="center"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('batch_no')]; ?></p></td>
								<td width="100" align="center"><p><? echo $knitting_company; ?></p></td>
								<td width="100" align="center"><p><? echo $location_arr[$row[csf('location_id')]];  ?></p></td>
								<td width="100" align="center"><p><? echo $color_arr[$row[csf('batch_color')]]; ?></p></td>
								<td width="100" align="center"><p><? echo $product_details_arr[$row[csf("prod_id")]]["product_name_details"]; ?></p></td>
								<td width="100" align="center"><p><? echo $product_details_arr[$row[csf("prod_id")]]["gsm"]; ?></p></td>
								<td width="100" align="center"><p><? echo $product_details_arr[$row[csf("prod_id")]]["dia_width"]; ?></p></td>
								<td width="100" align="right"><p><? echo number_format($row[csf('issue_qnty')],2); ?></p></td>
								<td align="center"><? echo $store_arr[$row[csf('store_id')]]; ?></td>
							</tr>
							<?
							$tot_issue_qty+=$row[csf('issue_qnty')];
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="11" align="right">Total</td>
							<td align="right">&nbsp;<? echo number_format($tot_issue_qty,2); ?>&nbsp;</td>
							<td></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<fieldset style="width:1300px; margin-left:3px">
			<strong>Receive  Return Details</strong>
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="1320" cellpadding="0" cellspacing="0" align="center">
					<thead>
						<tr>
							<th width="50">Sl</th>
							<th width="100">Product ID</th>
							<th width="100">Transection ID</th>
							<th width="100">Transection Date</th>
							<th width="100">Batch No</th>
							<th width="100">Service Company</th>
							<th width="100">Service Location</th>
							<th width="100">Batch Color</th>
							<th width="100">Fabric Des.</th>
							<th width="100">GSM</th>
							<th width="100">F.Dia</th>
							<th width="100">Fin. Rcv. Qty.</th>
							<th>Store Name</th>
						</tr>
					</thead>
					<tbody>
						<?
						if($cbo_location_id>0) $location_id_cond=" and a.location_id=$cbo_location_id";
						if($cbo_store_id!=0) $store_id_cond=" and d.store_id=$cbo_store_id";
						//if($cbo_location_id>0) $location_id_cond=" and a.location_id=$cbo_location_id";
						if($body_part_id!="") $body_part_id_cond=" and b.body_part_id='$body_part_id'";

						//a.recv_number,a.knitting_source,a.knitting_company,a.knitting_location_id,b.prod_id,b.booking_id,b.job_no,b.body_part_id,b.color_id,
						//sum(b.receive_qnty) as receive_qnty,b.fabric_description_id,b.gsm,b.width

						$recv_retn_Qnty_sql="select a.id,a.issue_number,a.knit_dye_source,a.knit_dye_company,a.location_id,b.prod_id,a.booking_id,b.body_part_id,sum(b.issue_qnty) as issue_qnty,c.batch_no,c.color_id as batch_color ,d.transaction_date,d.store_id,e.color_id 
						from inv_issue_master a, inv_finish_fabric_issue_dtls b,pro_batch_create_mst c,inv_transaction d ,order_wise_pro_details e 
						where a.id=b.mst_id and b.trans_id=d.id and c.id=b.batch_id and b.id=e.dtls_id and d.id=e.trans_id and a.entry_form in(46) and c.booking_no='$booking_no' $store_id_cond $body_part_id_cond and b.trans_id>0 and d.transaction_type=3 group by a.id,a.issue_number,a.knit_dye_source,a.knit_dye_company,a.location_id,b.prod_id,a.booking_id,b.body_part_id,c.batch_no,c.color_id,d.transaction_date, d.store_id,e.color_id ";
						$rcvRtnQnty_sql_arr=sql_select($recv_retn_Qnty_sql);
						$prod_ids="";
						foreach($rcvRtnQnty_sql_arr as $row)
						{
							$prod_ids.=$row[csf("prod_id")].",";
						}

						$prod_ids=chop($prod_ids,",");
						$product_data=sql_select("select id,product_name_details,gsm,dia_width from product_details_master where id in(".$prod_ids.")");
						foreach($product_data as $row)
						{
							$product_details_arr[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
							$product_details_arr[$row[csf("id")]]["gsm"]=$row[csf("gsm")];
							$product_details_arr[$row[csf("id")]]["dia_width"]=$row[csf("dia_width")];
						}
						$i=1;$tot_issue_qty=0;
						foreach($rcvRtnQnty_sql_arr as $row)
						{
							
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if( $row[csf('knit_dye_source')]==1){$knitting_company=$company_arr[$row[csf('knit_dye_company')]];}else{$knitting_company=$supplier_name[$row[csf('knit_dye_company')]];}
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="50" align="center"><p><? echo $i; ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
								<td width="100" align="center"><p><? echo  $row[csf('issue_number')];?></p></td>
								<td width="100" align="center"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('batch_no')]; ?></p></td>
								<td width="100" align="center"><p><? echo $knitting_company; ?></p></td>
								<td width="100" align="center"><p><? echo $location_arr[$row[csf('location_id')]];  ?></p></td>
								<td width="100" align="center"><p><? echo $color_arr[$row[csf('batch_color')]]; ?></p></td>
								<td width="100" align="center"><p><? echo $product_details_arr[$row[csf("prod_id")]]["product_name_details"]; ?></p></td>
								<td width="100" align="center"><p><? echo $product_details_arr[$row[csf("prod_id")]]["gsm"]; ?></p></td>
								<td width="100" align="center"><p><? echo $product_details_arr[$row[csf("prod_id")]]["dia_width"]; ?></p></td>
								<td width="100" align="right"><p><? echo number_format($row[csf('issue_qnty')],2); ?></p></td>
								<td align="center"><? echo $store_arr[$row[csf('store_id')]]; ?></td>
							</tr>
							<?
							$tot_issue_qty+=$row[csf('issue_qnty')];
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="11" align="right">Total</td>
							<td align="right">&nbsp;<? echo number_format($tot_issue_qty,2); ?>&nbsp;</td>
							<td></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<fieldset style="width:1300px; margin-left:3px">
			<strong>Transfer Out Details</strong>
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="1320" cellpadding="0" cellspacing="0" align="center">
					<thead>
						<tr>
							<th width="50">Sl</th>
							<th width="100">Product ID</th>
							<th width="100">Transection ID</th>
							<th width="100">Transection Date</th>
							<th width="100">Batch No</th>
							<th width="100">Service Company</th>
							<th width="100">Service Location</th>
							<th width="100">Batch Color</th>
							<th width="100">Fabric Des.</th>
							<th width="100">GSM</th>
							<th width="100">F.Dia</th>
							<th width="100">Fin. Rcv. Qty.</th>
							<th>Store Name</th>
						</tr>
					</thead>
					<tbody>
						<?

					
					/*	$transInQnty_sql="select a.id,a.transfer_system_id,a.company_id,a.location_id,b.from_prod_id,b.body_part_id,sum(b.transfer_qnty) as transfer_qnty,c.batch_no,c.color_id as batch_color ,d.transaction_date,b.to_store,e.color_id 
						from inv_item_transfer_mst a, inv_item_transfer_dtls b,pro_batch_create_mst c,inv_transaction d ,order_wise_pro_details e 
						where a.id=b.mst_id and b.trans_id=d.id and c.id=b.batch_id and b.id=e.dtls_id and d.id=e.trans_id and a.entry_form in(14) and b.to_order_id='$poIDS' $store_id_cond and b.trans_id>0 and d.transaction_type=5 group by a.id,a.transfer_system_id,a.company_id,a.location_id,b.from_prod_id,b.body_part_id,c.batch_no,c.color_id,d.transaction_date, b.to_store,e.color_id ";*/



						if($cbo_location_id!="" && $cbo_location_id!=0) $location_id_cond=" and a.location_id=$cbo_location_id";
						if($cbo_store_id!=0) $store_id_cond=" and d.store_id=$cbo_store_id";
						if($txt_date_from!="") $txt_date_from_cond=" and d.transaction_date<'$txt_date_from'";
						if($system_trnsf_id!="") $system_trnsf_id_cond=" and a.id in('$system_trnsf_id')";
						if($fabric_color_id!="") $fabric_color_id_cond=" and b.color_id='$fabric_color_id'";
						if($body_part_id!="") $body_part_id_cond=" and b.body_part_id='$body_part_id'";
						if($poIDS!="") $order_id_cond=" and b.from_order_id in('$poIDS')";
						
						//a.recv_number,a.knitting_source,a.knitting_company,a.knitting_location_id,b.prod_id,b.booking_id,b.job_no,b.body_part_id,b.color_id,
						//sum(b.receive_qnty) as receive_qnty,b.fabric_description_id,b.gsm,b.width
						if($system_trnsf_id!="")
						{
							$transOutQnty_sql="select a.id,a.transfer_system_id,a.company_id,a.location_id,b.from_prod_id,b.body_part_id,sum(b.transfer_qnty) as transfer_qnty,c.batch_no,c.color_id as batch_color ,d.transaction_date,d.store_id,e.color_id 
							from inv_item_transfer_mst a, inv_item_transfer_dtls b,pro_batch_create_mst c,inv_transaction d ,order_wise_pro_details e 
							where a.id=b.mst_id and b.trans_id=d.id and c.id=b.batch_id and b.id=e.dtls_id and d.id=e.trans_id and a.entry_form in(14) and b.from_order_id='$poIDS' $system_trnsf_id_cond and c.booking_no='$booking_no' $store_id_cond $trans_out_id_cond $txt_date_from_cond $fabric_color_id_cond $order_id_cond $body_part_id_cond and b.trans_id>0 and d.transaction_type=6 group by a.id,a.transfer_system_id,a.company_id,a.location_id,b.from_prod_id,b.body_part_id,c.batch_no,c.color_id,d.transaction_date, d.store_id,e.color_id ";
						}
						$transOutQnty_sql_arr=sql_select($transOutQnty_sql);
						$prod_ids="";
						foreach($transOutQnty_sql_arr as $row)
						{
							$prod_ids.=$row[csf("from_prod_id")].",";
						}

						$prod_ids=chop($prod_ids,",");
						$product_data=sql_select("select id,product_name_details,gsm,dia_width from product_details_master where id in(".$prod_ids.")");
						foreach($product_data as $row)
						{
							$product_details_arr[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
							$product_details_arr[$row[csf("id")]]["gsm"]=$row[csf("gsm")];
							$product_details_arr[$row[csf("id")]]["dia_width"]=$row[csf("dia_width")];
						}
						$i=1;$tot_transOut_qty=0;
						foreach($transOutQnty_sql_arr as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if( $row[csf('knit_dye_source')]==1){$knitting_company=$company_arr[$row[csf('knit_dye_company')]];}else{$knitting_company=$supplier_name[$row[csf('knit_dye_company')]];}
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="50" align="center"><p><? echo $i; ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('from_prod_id')]; ?></p></td>
								<td width="100" align="center"><p><? echo  $row[csf('transfer_system_id')];?></p></td>
								<td width="100" align="center"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('batch_no')]; ?></p></td>
								<td width="100" align="center"><p><? echo $knitting_company; ?></p></td>
								<td width="100" align="center"><p><? echo $location_arr[$row[csf('location_id')]];  ?></p></td>
								<td width="100" align="center"><p><? echo $color_arr[$row[csf('batch_color')]]; ?></p></td>
								<td width="100" align="center"><p><? echo $product_details_arr[$row[csf("from_prod_id")]]["product_name_details"]; ?></p></td>
								<td width="100" align="center"><p><? echo $product_details_arr[$row[csf("from_prod_id")]]["gsm"]; ?></p></td>
								<td width="100" align="center"><p><? echo $product_details_arr[$row[csf("from_prod_id")]]["dia_width"]; ?></p></td>
								<td width="100" align="right"><p><? echo number_format($row[csf('transfer_qnty')],2); ?></p></td>
								<td align="center"><? echo $store_arr[$row[csf('store_id')]]; ?></td>
							</tr>
							<?
							$tot_transOut_qty+=$row[csf('transfer_qnty')];
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="11" align="right">Total</td>
							<td align="right">&nbsp;<? echo number_format($tot_transOut_qty,2); ?>&nbsp;</td>
							<td></td>
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